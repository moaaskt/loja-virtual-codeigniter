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

        $relacionados = $model->getRelacionados(
            (int) $produto['categoria_id'],
            (int) $produto['id']
        );

        // Busca as imagens extras e as variações garantindo que retornem um array (vazio caso não existam)
        $imagens = $model->getImagens((int) $produto['id']);
        $variacoes = $model->getVariacoes((int) $produto['id']);

        $data = [
            'title'        => esc($produto['nome']),
            'produto'      => $produto,
            'relacionados' => $relacionados,
            'imagens'      => is_array($imagens) ? $imagens : [],
            'variacoes'    => is_array($variacoes) ? $variacoes : [],
        ];

        return view('shop/produto_detalhe', $data);
    }



    // Método para buscar produtos via API (suporta filtros completos)
    public function buscaApi()
    {
        $model = new \App\Models\ProdutoModel();

        $categorias = $this->request->getGet('categorias') ?? $this->request->getGet('categoria') ?? [];
        $marcas     = $this->request->getGet('marcas') ?? $this->request->getGet('marca') ?? [];
        $generos    = $this->request->getGet('generos') ?? $this->request->getGet('genero') ?? [];
        $termo      = $this->request->getGet('termo') ?? $this->request->getGet('q') ?? $this->request->getGet('busca') ?? '';

        // Coleta todos os parâmetros de filtro enviados via GET
        $filtros = [
            'termo'     => is_string($termo) ? trim($termo) : '',
            'categorias'=> is_array($categorias) ? $categorias : [$categorias],
            'preco_min' => $this->request->getGet('preco_min') ?? '',
            'preco_max' => $this->request->getGet('preco_max') ?? '',
            'marcas'    => is_array($marcas) ? $marcas : [$marcas],
            'generos'   => is_array($generos) ? $generos : [$generos],
        ];

        $produtos = $model->getProdutosFiltrados($filtros, 24);

        return $this->response->setJSON($produtos);
    }

}
