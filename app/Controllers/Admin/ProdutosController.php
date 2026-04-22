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
        $img  = $this->request->getFile('imagem');

        if (!$this->validateUpload($img)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', ['imagem' => 'A imagem deve ser JPEG, PNG ou WebP e ter no máximo 2 MB.'])
                ->with('categorias', $this->categoriaModel->findAll());
        }

        if ($img && $img->isValid() && !$img->hasMoved()) {
            $novoNome = $img->getRandomName();
            $img->move(FCPATH . 'uploads/produtos', $novoNome);
            $data['imagem'] = $novoNome;
        } elseif (!empty($this->request->getPost('url_imagem'))) {
            $data['imagem'] = $this->request->getPost('url_imagem');
        }

        if ($this->produtoModel->insert($data)) {
            return redirect()->to(site_url('admin/produtos'))->with('success', 'Produto criado com sucesso!');
        }

        return redirect()->back()
            ->withInput()
            ->with('errors', $this->produtoModel->errors())
            ->with('categorias', $this->categoriaModel->findAll());
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
        $img    = $this->request->getFile('imagem');
        $urlImg = $this->request->getPost('url_imagem');

        if ($img && $img->isValid() && !$img->hasMoved()) {
            if (!$this->validateUpload($img)) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', ['imagem' => 'A imagem deve ser JPEG, PNG ou WebP e ter no máximo 2 MB.'])
                    ->with('categorias', $this->categoriaModel->findAll());
            }

            $produtoAntigo = $this->produtoModel->find($id);
            $imagemAntiga  = $produtoAntigo['imagem'] ?? null;

            if ($imagemAntiga && strpos($imagemAntiga, 'http') !== 0) {
                $caminhoAntigo = FCPATH . 'uploads/produtos/' . $imagemAntiga;
                if (file_exists($caminhoAntigo)) {
                    unlink($caminhoAntigo);
                }
            }

            $novoNome = $img->getRandomName();
            $img->move(FCPATH . 'uploads/produtos', $novoNome);
            $data['imagem'] = $novoNome;
        } elseif (!empty($urlImg)) {
            $data['imagem'] = $urlImg;
        }

        if ($this->produtoModel->update($id, $data)) {
            return redirect()->to(site_url('admin/produtos'))->with('success', 'Produto atualizado com sucesso!');
        }

        return redirect()->back()
            ->withInput()
            ->with('errors', $this->produtoModel->errors())
            ->with('categorias', $this->categoriaModel->findAll());
    }

    public function delete($id = null)
    {
        if ($this->pedidoProdutoModel->where('produto_id', $id)->first()) {
            return redirect()->to(site_url('admin/produtos'))
                ->with('error', 'Este produto não pode ser excluído pois já faz parte de um ou mais pedidos.');
        }

        try {
            $this->produtoModel->delete($id);
            return redirect()->to(site_url('admin/produtos'))->with('success', 'Produto excluído com sucesso!');
        } catch (\Exception $e) {
            return redirect()->to(site_url('admin/produtos'))->with('error', 'Erro ao excluir o produto: ' . $e->getMessage());
        }
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
