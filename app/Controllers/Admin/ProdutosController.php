<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\ProdutoModel;
use App\Models\CategoriaModel;
use App\Models\PedidoProdutoModel; 

class ProdutosController extends BaseController
{

    public function delete($id = null)
{
    // Precisamos do model de itens de pedido para fazer a verificação
    $pedidoProdutoModel = new \App\Models\PedidoProdutoModel();

    // Verifica se este produto_id existe em algum registro na tabela pedido_produtos
    $produtoEmPedido = $pedidoProdutoModel->where('produto_id', $id)->first();

    // Se o produto foi encontrado em um pedido, impede a exclusão
    if ($produtoEmPedido) {
        return redirect()->to(site_url('admin/produtos'))
                         ->with('error', 'Este produto não pode ser excluído, pois já faz parte de um ou mais pedidos.');
    }

    // Se o produto não estiver em nenhum pedido, a exclusão pode prosseguir
    $produtoModel = new \App\Models\ProdutoModel();
    if ($produtoModel->delete($id)) {
        return redirect()->to(site_url('admin/produtos'))->with('success', 'Produto excluído com sucesso!');
    } else {
        return redirect()->to(site_url('admin/produtos'))->with('error', 'Erro ao excluir o produto.');
    }
}

    
    public function update($id = null)
    {
        $produtoModel = new ProdutoModel();
        $data = $this->request->getPost();

        // Pega o arquivo de imagem enviado e a URL enviada
        $img = $this->request->getFile('imagem');
        $urlImg = $this->request->getPost('url_imagem');

        // Verifica se uma nova imagem foi enviada via upload de arquivo
        if ($img && $img->isValid() && !$img->hasMoved()) {
            // Busca os dados antigos do produto
            $produtoAntigo = $produtoModel->find($id);
            $imagemAntiga = $produtoAntigo['imagem'];

            // Se uma imagem antiga existir e for um arquivo local, apaga-o
            if ($imagemAntiga && strpos($imagemAntiga, 'http') !== 0 && file_exists(FCPATH . 'uploads/produtos/' . $imagemAntiga)) {
                unlink(FCPATH . 'uploads/produtos/' . $imagemAntiga);
            }

            // Gera um novo nome e move a nova imagem
            $novoNome = $img->getRandomName();
            $img->move(FCPATH . 'uploads/produtos', $novoNome);

            // Adiciona o novo nome da imagem aos dados
            $data['imagem'] = $novoNome;
        } elseif (!empty($urlImg)) {
            // Se não subiu arquivo mas informou URL
            $data['imagem'] = $urlImg;
        }

        if ($produtoModel->update($id, $data)) {
            return redirect()->to(site_url('admin/produtos'))->with('success', 'Produto atualizado com sucesso!');
        } else {
            // Se a validação falhar, precisamos reenviar a lista de categorias
            $categoriaModel = new \App\Models\CategoriaModel();
            return redirect()->back()
                ->withInput()
                ->with('errors', $produtoModel->errors())
                ->with('categorias', $categoriaModel->findAll());
        }
    }


    public function edit($id = null)
    {
        helper('form');
        $produtoModel = new ProdutoModel();
        $categoriaModel = new \App\Models\CategoriaModel();

        // Busca o produto pelo ID
        $produto = $produtoModel->find($id);

        if (empty($produto)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Produto não encontrado.');
        }

        $data = [
            'title' => 'Editar Produto: ' . esc($produto['nome']),
            'produto' => $produto,
            'categorias' => $categoriaModel->findAll() // Busca todas as categorias para o <select>
        ];

        return view('admin/produtos/edit', $data);
    }

    public function create()
    {
        $produtoModel = new ProdutoModel();
        $data = $this->request->getPost();

        // Pega o arquivo de imagem enviado e a URL
        $img = $this->request->getFile('imagem');
        $urlImg = $this->request->getPost('url_imagem');

        // Verifica se um arquivo foi enviado via upload
        if ($img && $img->isValid() && !$img->hasMoved()) {
            $novoNome = $img->getRandomName();
            $img->move(FCPATH . 'uploads/produtos', $novoNome);
            $data['imagem'] = $novoNome;
        } elseif (!empty($urlImg)) {
            // Caso contrário, se informou a URL da imagem
            $data['imagem'] = $urlImg;
        }

        if ($produtoModel->insert($data)) {
            return redirect()->to(site_url('admin/produtos'))->with('success', 'Produto criado com sucesso!');
        } else {
            // Se a validação falhar, precisamos reenviar a lista de categorias
            $categoriaModel = new \App\Models\CategoriaModel();
            return redirect()->back()
                ->withInput()
                ->with('errors', $produtoModel->errors())
                ->with('categorias', $categoriaModel->findAll());
        }
    }


    public function new()
    {
        helper('form');

        // Precisamos buscar as categorias para popular o <select> no formulário
        $categoriaModel = new \App\Models\CategoriaModel();

        $data = [
            'title' => 'Adicionar Novo Produto',
            'categorias' => $categoriaModel->findAll() // Busca todas as categorias
        ];

        return view('admin/produtos/new', $data);
    }

    public function index()
    {
        $model = new ProdutoModel();

        $data = [
            // Usando nosso novo método para buscar produtos com o nome da categoria!
            'produtos' => $model->getProdutosComCategoria(10),
            'pager' => $model->pager,
            'title' => 'Lista de Produtos'
        ];

        return view('admin/produtos/index', $data);
    }
}