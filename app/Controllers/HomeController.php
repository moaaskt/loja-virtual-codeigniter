<?php

namespace App\Controllers;

use App\Models\ProdutoModel;

class HomeController extends BaseController
{

    public function __construct()
    {
        // Carrega os helpers para todos os métodos deste controller
        helper(['form', 'url']);
    }



    public function index()
    {
        $produtoModel = new \App\Models\ProdutoModel();
        $categoriaModel = new \App\Models\CategoriaModel(); // <-- Adicionamos o model de categoria

        $data = [
            'produtos' => $produtoModel->getProdutosComCategoria(12),
            'pager' => $produtoModel->pager,
            'categorias' => $categoriaModel->findAll(), // <-- Buscamos todas as categorias
            'title' => 'Vitrine de Produtos'
        ];

        return view('shop/index', $data);
    }



    // Método para obter produtos por categoria
    public function produtosPorCategoria($categoriaId = null)
{
    $produtoModel = new \App\Models\ProdutoModel();
    $categoriaModel = new \App\Models\CategoriaModel();

    $categoria = $categoriaModel->find($categoriaId);

    if ($categoria === null) {
       throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

    $data = [
        'produtos'        => $produtoModel->getProdutosPorCategoria($categoriaId, 12),
        'pager'           => $produtoModel->pager,
        'categorias'      => $categoriaModel->findAll(),
        'title'           => 'Produtos da Categoria: ' . esc($categoria['nome']),
        'categoriaAtivaId' => $categoriaId // Para destacar a categoria ativa na view
    ];

    return view('shop/index', $data);
}





    // Método para buscar produtos com base no termo de busca 
   public function busca()
{
    $produtoModel = new \App\Models\ProdutoModel();
    $categoriaModel = new \App\Models\CategoriaModel(); // <-- LINHA ADICIONADA

    $termo = $this->request->getGet('termo');

    $data = [
        'produtos'   => $produtoModel->searchProdutosComCategoria($termo, 12),
        'pager'      => $produtoModel->pager,
        'title'      => 'Resultados da busca por: "' . esc($termo) . '"',
        'termoBusca' => esc($termo),
        'categorias' => $categoriaModel->findAll(), // <-- LINHA ADICIONADA
    ];

    // Reutiliza a mesma view da vitrine para mostrar os resultados
    return view('shop/index', $data);
}


    // Método para mostrar os detalhes de um produto específico
    public function produto($id = null)
    {
        $model = new ProdutoModel();

        // Usa nosso novo método para buscar um produto específico com sua categoria
        $produto = $model->findProdutoComCategoria($id);

        if (empty($produto)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Produto não encontrado.');
        }

        $data = [
            'title' => esc($produto['nome']),
            'produto' => $produto
        ];

        return view('shop/produto_detalhe', $data);
    }



    // Método para buscar produtos via API
    public function buscaApi()
    {
        $model = new \App\Models\ProdutoModel();

        $termo = $this->request->getGet('termo');

        // Usamos o mesmo método de busca que já tínhamos no Model
        $produtos = $model->searchProdutosComCategoria($termo, 12); // Limite de 12 resultados

        // A mágica está aqui: retorna os dados em formato JSON
        return $this->response->setJSON($produtos);
    }


}