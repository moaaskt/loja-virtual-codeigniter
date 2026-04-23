<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoriaModel;
use App\Models\PedidoProdutoModel;
use App\Models\ProdutoModel;

class ProdutosController extends BaseController
{
    protected ProdutoModel $produtoModel;
    protected CategoriaModel $categoriaModel;
    protected PedidoProdutoModel $pedidoProdutoModel;

    public function __construct()
    {
        $this->produtoModel       = new ProdutoModel();
        $this->categoriaModel     = new CategoriaModel();
        $this->pedidoProdutoModel = new PedidoProdutoModel();
    }

    public function index()
    {
        return view('admin/produtos/index', [
            'produtos' => $this->produtoModel->getProdutosComCategoria(10),
            'pager'    => $this->produtoModel->pager,
            'title'    => 'Lista de Produtos',
        ]);
    }

    public function new()
    {
        helper('form');
        return view('admin/produtos/new', [
            'title'      => 'Adicionar Novo Produto',
            'categorias' => $this->categoriaModel->findAll(),
        ]);
    }

    public function create()
    {
        $data = $this->request->getPost();
        
        // Calcular estoque total com base nas variações
        $variacoes = $this->request->getPost('variacoes') ?? [];
        $totalEstoque = 0;
        foreach ($variacoes as $v) {
            $totalEstoque += (int) $v['estoque'];
        }
        $data['estoque'] = $totalEstoque;
        $data['cores'] = ''; // Limpando campos descontinuados
        $data['tamanhos'] = '';

        // --- Imagem Principal ---
        $img = $this->request->getFile('imagem');
        if ($img && $img->isValid() && !$img->hasMoved()) {
            if (!$this->validateUpload($img)) {
                return redirect()->back()->withInput()->with('errors', ['imagem' => 'A imagem principal é inválida.']);
            }
            $novoNome = $img->getRandomName();
            $img->move(FCPATH . 'uploads/produtos', $novoNome);
            $data['imagem'] = $novoNome;
        } elseif (!empty($this->request->getPost('url_imagem'))) {
            $data['imagem'] = $this->request->getPost('url_imagem');
        }

        // --- Frete Grátis ---
        $data['frete_gratis'] = isset($data['frete_gratis']) ? 1 : 0;

        $db = \Config\Database::connect();
        $db->transStart();

        $produtoId = $this->produtoModel->insert($data);
        if (!$produtoId) {
            $db->transRollback();
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->produtoModel->errors())
                ->with('categorias', $this->categoriaModel->findAll());
        }

        // --- Variações ---
        foreach ($variacoes as $v) {
            $db->table('produto_variacoes')->insert([
                'produto_id' => $produtoId,
                'tamanho' => $v['tamanho'],
                'cor' => $v['cor'],
                'estoque' => (int) $v['estoque']
            ]);
        }

        // --- Galeria de Imagens ---
        $galeriaFiles = $this->request->getFileMultiple('imagens_galeria');
        if ($galeriaFiles) {
            foreach ($galeriaFiles as $gFile) {
                if ($gFile->isValid() && !$gFile->hasMoved()) {
                    if ($this->validateUpload($gFile)) {
                        $gNome = $gFile->getRandomName();
                        $gFile->move(FCPATH . 'uploads/produtos', $gNome);
                        $db->table('produto_imagens')->insert([
                            'produto_id' => $produtoId,
                            'caminho_imagem' => $gNome
                        ]);
                    }
                }
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('errors', ['db' => 'Erro ao salvar o produto no banco de dados.']);
        }

        return redirect()->to(site_url('admin/produtos'))->with('success', 'Produto criado com sucesso!');
    }

    public function edit($id = null)
    {
        helper('form');
        $produto = $this->produtoModel->find($id);

        if (empty($produto)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Produto não encontrado.');
        }

        return view('admin/produtos/edit', [
            'title'      => 'Editar Produto: ' . esc($produto['nome']),
            'produto'    => $produto,
            'categorias' => $this->categoriaModel->findAll(),
        ]);
    }

    public function update($id = null)
    {
        $data   = $this->request->getPost();
        $produtoAntigo = $this->produtoModel->find($id);
        
        // Calcular estoque total com base nas variações
        $variacoes = $this->request->getPost('variacoes') ?? [];
        $totalEstoque = 0;
        foreach ($variacoes as $v) {
            $totalEstoque += (int) $v['estoque'];
        }
        $data['estoque'] = $totalEstoque;
        $data['cores'] = '';
        $data['tamanhos'] = '';
        
        // --- Imagem Principal ---
        $img    = $this->request->getFile('imagem');
        $urlImg = $this->request->getPost('url_imagem');

        if ($img && $img->isValid() && !$img->hasMoved()) {
            if (!$this->validateUpload($img)) {
                return redirect()->back()->withInput()->with('errors', ['imagem' => 'A imagem principal é inválida.']);
            }

            $imagemAntiga = $produtoAntigo['imagem'] ?? null;
            if ($imagemAntiga && strpos($imagemAntiga, 'http') !== 0) {
                $caminhoAntigo = FCPATH . 'uploads/produtos/' . $imagemAntiga;
                if (file_exists($caminhoAntigo)) { unlink($caminhoAntigo); }
            }

            $novoNome = $img->getRandomName();
            $img->move(FCPATH . 'uploads/produtos', $novoNome);
            $data['imagem'] = $novoNome;
        } elseif (!empty($urlImg)) {
            $data['imagem'] = $urlImg;
        }

        // --- Frete Grátis ---
        $data['frete_gratis'] = isset($data['frete_gratis']) ? 1 : 0;

        $db = \Config\Database::connect();
        $db->transStart();

        if (!$this->produtoModel->update($id, $data)) {
            $db->transRollback();
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->produtoModel->errors())
                ->with('categorias', $this->categoriaModel->findAll());
        }

        // --- Variações (Abordagem simples: deletar antigas e inserir novas) ---
        $db->table('produto_variacoes')->where('produto_id', $id)->delete();
        foreach ($variacoes as $v) {
            $db->table('produto_variacoes')->insert([
                'produto_id' => $id,
                'tamanho' => $v['tamanho'],
                'cor' => $v['cor'],
                'estoque' => (int) $v['estoque']
            ]);
        }

        // --- Galeria de Imagens (Upload) ---
        $galeriaFiles = $this->request->getFileMultiple('imagens_galeria');
        if ($galeriaFiles) {
            foreach ($galeriaFiles as $gFile) {
                if ($gFile->isValid() && !$gFile->hasMoved()) {
                    if ($this->validateUpload($gFile)) {
                        $gNome = $gFile->getRandomName();
                        $gFile->move(FCPATH . 'uploads/produtos', $gNome);
                        $db->table('produto_imagens')->insert([
                            'produto_id' => $id,
                            'caminho_imagem' => $gNome
                        ]);
                    }
                }
            }
        }

        // --- Galeria de Imagens (Exclusão) ---
        $imagensExcluir = $this->request->getPost('imagens_excluir') ?? [];
        if (!empty($imagensExcluir)) {
            $imagensNoBanco = $db->table('produto_imagens')->whereIn('id', $imagensExcluir)->where('produto_id', $id)->get()->getResultArray();
            foreach ($imagensNoBanco as $imgDel) {
                $caminho = FCPATH . 'uploads/produtos/' . $imgDel['caminho_imagem'];
                if (file_exists($caminho)) {
                    unlink($caminho);
                }
            }
            $db->table('produto_imagens')->whereIn('id', $imagensExcluir)->delete();
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('errors', ['db' => 'Erro ao atualizar o produto no banco de dados.']);
        }

        return redirect()->to(site_url('admin/produtos'))->with('success', 'Produto atualizado com sucesso!');
    }

    public function delete($id = null)
    {
        try {
            $this->produtoModel->delete($id);
            return redirect()->to(site_url('admin/produtos'))->with('success', 'Produto movido para a lixeira.');
        } catch (\Exception $e) {
            return redirect()->to(site_url('admin/produtos'))->with('error', 'Erro ao excluir o produto: ' . $e->getMessage());
        }
    }

    public function trash()
    {
        $produtos = $this->produtoModel->onlyDeleted()
            ->select('produtos.*, categorias.nome as categoria_nome')
            ->join('categorias', 'categorias.id = produtos.categoria_id')
            ->paginate(10);

        return view('admin/produtos/trash', [
            'produtos' => $produtos,
            'pager'    => $this->produtoModel->pager,
            'title'    => 'Lixeira de Produtos',
        ]);
    }

    public function restore($id = null)
    {
        $this->produtoModel->onlyDeleted()->where('id', $id)->set(['deleted_at' => null])->update();
        return redirect()->to(site_url('admin/produtos/trash'))->with('success', 'Produto restaurado com sucesso!');
    }

    /**
     * Valida MIME type e tamanho do arquivo de imagem.
     */
    protected function validateUpload(?\CodeIgniter\HTTP\Files\UploadedFile $img): bool
    {
        if (!$img || !$img->isValid() || $img->hasMoved()) {
            return true; // sem arquivo não é erro de upload
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSizeBytes = 2 * 1024 * 1024; // 2 MB

        if (!in_array($img->getMimeType(), $allowedMimes, true)) {
            return false;
        }

        if ($img->getSizeByUnit('b') > $maxSizeBytes) {
            return false;
        }

        return true;
    }
}
