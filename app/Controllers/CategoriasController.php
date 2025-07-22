<?php

namespace App\Controllers;

use App\Models\CategoriaModel;

class CategoriasController extends BaseController
{


    /**
     * Mostra o formulário para criar uma nova categoria.
     */



    public function new()
    {
        helper('form');
        return view('categorias/new', ['title' => 'Nova Categoria']);
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
            return redirect()->to(site_url('categorias'))->with('success', 'Categoria criada com sucesso!');
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

    return view('categorias/edit', $data);
}

public function update($id = null)
{
    $model = new CategoriaModel();
    $data = $this->request->getPost();

    // O método update() também aciona a validação
    if ($model->update($id, $data)) {
        return redirect()->to(site_url('categorias'))->with('success', 'Categoria atualizada com sucesso!');
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
        return view('categorias/index', $data);
    }

public function delete($id = null)
{
    $model = new CategoriaModel();

    // O método delete() do Model remove o registro do banco de dados
    if ($model->delete($id)) {
        return redirect()->to(site_url('categorias'))->with('success', 'Categoria excluída com sucesso!');
    } else {
        // Em caso de erro, redireciona com uma mensagem de erro
        return redirect()->to(site_url('categorias'))->with('error', 'Erro ao excluir a categoria.');
    }
}


}