<?php

namespace App\Controllers;

use App\Models\ProdutoModel;

class ProdutosController extends BaseController
{

    public function delete($id = null)
    {
        $model = new ProdutoModel();

        if ($model->delete($id)) {
            return redirect()->to(site_url('admin/produtos'))->with('success', 'Produto excluído com sucesso!');
        } else {
            return redirect()->to(site_url('admin/produtos'))->with('error', 'Erro ao excluir o produto.');
        }
    }

    
    public function update($id = null)
    {
        $produtoModel = new ProdutoModel();
        $data = $this->request->getPost();

        // Pega o arquivo de imagem enviado
        $img = $this->request->getFile('imagem');

        // Verifica se uma nova imagem foi enviada e se é válida
        if ($img && $img->isValid() && !$img->hasMoved()) {
            // Busca os dados antigos do produto, incluindo o nome da imagem antiga
            $produtoAntigo = $produtoModel->find($id);
            $imagemAntiga = $produtoAntigo['imagem'];

            // Se uma imagem antiga existir, apaga o arquivo do servidor
            if ($imagemAntiga && file_exists(FCPATH . 'uploads/produtos/' . $imagemAntiga)) {
                unlink(FCPATH . 'uploads/produtos/' . $imagemAntiga);
            }

            // Gera um novo nome e move a nova imagem
            $novoNome = $img->getRandomName();
            $img->move(FCPATH . 'uploads/produtos', $novoNome);

            // Adiciona o novo nome da imagem aos dados que serão atualizados
            $data['imagem'] = $novoNome;
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

        return view('produtos/edit', $data);
    }

    public function create()
    {
        $produtoModel = new ProdutoModel();
        $data = $this->request->getPost();

        // Pega o arquivo de imagem enviado
        $img = $this->request->getFile('imagem');

        // Verifica se um arquivo foi enviado e se é válido
        if ($img && $img->isValid() && !$img->hasMoved()) {
            // Gera um nome aleatório para o arquivo para evitar conflitos
            $novoNome = $img->getRandomName();

            // Move o arquivo para a pasta de uploads
            $img->move(FCPATH . 'uploads/produtos', $novoNome);

            // Salva o novo nome do arquivo no array de dados que vai para o banco
            $data['imagem'] = $novoNome;
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

        return view('produtos/new', $data);
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

        return view('produtos/index', $data);
    }
}