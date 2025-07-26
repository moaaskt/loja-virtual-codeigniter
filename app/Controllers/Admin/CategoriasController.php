<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\CategoriaModel;
use App\Models\ProdutoModel;
class CategoriasController extends BaseController
{


    /**
     * Mostra o formulário para criar uma nova categoria.
     */



    public function new()
    {
        helper('form');
        return view('admin/categorias/new', ['title' => 'Nova Categoria']);
    }

    /**
     * Processa os dados do formulário e cria a nova categoria no banco.
     */
    public function create()
    {
        $model = new CategoriaModel();
        $data = $this->request->getPost();

        // O método insert() já aciona a validação que definimos no Model
        if ($model->insert($data)) {
            // Em caso de sucesso, redireciona para a lista com uma mensagem
            return redirect()->to(site_url('admin/categorias'))->with('success', 'Categoria criada com sucesso!');
        } else {
            // Em caso de falha na validação, volta ao formulário com os erros
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }
    }


    public function edit($id = null)
{
    helper('form');
    $model = new CategoriaModel();

    // Busca a categoria pelo ID
    $data = [
        'title' => 'Editar Categoria',
        'categoria' => $model->find($id)
    ];

    if (empty($data['categoria'])) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Categoria não encontrada.');
    }

    return view('admin/categorias/edit', $data);
}

public function update($id = null)
{
    $model = new CategoriaModel();
    $data = $this->request->getPost();

    // O método update() também aciona a validação
    if ($model->update($id, $data)) {
        return redirect()->to(site_url('admin/categorias'))->with('success', 'Categoria atualizada com sucesso!');
    } else {
        return redirect()->back()->withInput()->with('errors', $model->errors());
    }
}





    public function index()
    {
        $model = new CategoriaModel();

        $data = [
            'categorias' => $model->paginate(10), // Já com paginação!
            'pager' => $model->pager,
            'title' => 'Lista de Categorias'
        ];

        // Por enquanto não temos um layout, então vamos chamar a view diretamente
        return view('admin/categorias/index', $data);
    }

public function delete($id = null)
{
    // Precisamos verificar se existem produtos nesta categoria
    $produtoModel = new \App\Models\ProdutoModel();

    // Procura por qualquer produto que tenha este categoria_id
    $produtos = $produtoModel->where('categoria_id', $id)->findAll();

    // Se a lista de produtos NÃO estiver vazia, impede a exclusão
    if (!empty($produtos)) {
        return redirect()->to(site_url('admin/categorias'))
                         ->with('error', 'Não é possível excluir esta categoria, pois há produtos associados a ela.');
    }

    // Se não houver produtos, pode excluir normalmente
    $categoriaModel = new \App\Models\CategoriaModel();
    if ($categoriaModel->delete($id)) {
        return redirect()->to(site_url('admin/categorias'))->with('success', 'Categoria excluída com sucesso!');
    } else {
        return redirect()->to(site_url('admin/categorias'))->with('error', 'Erro ao excluir a categoria.');
    }
}


}