<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'HomeController::index');


// --- Rota para buscar produtos ---
$routes->get('busca', 'HomeController::busca');

//-Rotas de login e autenticação
$routes->get('/', 'HomeController::index'); // Vitrine
$routes->get('login', 'AuthController::login'); // Página de login
$routes->post('auth/attempt-login', 'AuthController::attemptLogin'); // Processa o login
$routes->get('logout', 'AuthController::logout'); // Faz logout


// --- Grupo de rotas para a conta do cliente ---
// Apenas usuários autenticados podem acessar essas rotas
$routes->group('minha-conta', ['filter' => 'auth'], static function ($routes) {
    $routes->get('pedidos', 'ClienteController::index');
});


// --- Rota para a página inicial da loja ---
$routes->get('produto/(:num)', 'HomeController::produto/$1');


// --- Rota para finalizar o pedido ---
$routes->post('checkout/finalizar', 'PedidoController::finalizar');
$routes->get('pedido/sucesso', 'PedidoController::sucesso');

// --- Rota para adicionar um produto ao carrinho ---
$routes->post('carrinho/adicionar', 'CarrinhoController::adicionar');
$routes->get('carrinho', 'CarrinhoController::index');
$routes->post('carrinho/remover/(:num)', 'CarrinhoController::remover/$1');
$routes->post('carrinho/atualizar', 'CarrinhoController::atualizar');




// --- rotas para registro de usuários ---
$routes->get('registrar', 'AuthController::registrar'); // Mostra o formulário
$routes->post('registrar/salvar', 'AuthController::attemptRegister'); // Processa o formulário

// --- Rota para buscar produtos via API ---
$routes->get('api/produtos/busca', 'HomeController::buscaApi');

// --- Rota para buscar produtos por categoria ---
$routes->get('categoria/(:num)', 'HomeController::produtosPorCategoria/$1');



// --- ROTAS DO PAINEL ADMINISTRATIVO ---
$routes->group('admin', ['filter' => ['auth', 'admin']], static function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'AdminController::index');
    $routes->get('pedidos', 'Admin\PedidoController::index');
    $routes->post('pedidos/atualizar-status/(:num)', 'Admin\PedidoController::atualizarStatus/$1');



    // Rotas para Categorias
    $routes->get('categorias', 'CategoriasController::index');
    $routes->get('categorias/new', 'CategoriasController::new');
    $routes->post('categorias', 'CategoriasController::create');
    $routes->get('categorias/edit/(:num)', 'CategoriasController::edit/$1');
    $routes->post('categorias/(:num)', 'CategoriasController::update/$1');
    $routes->post('categorias/delete/(:num)', 'CategoriasController::delete/$1');

    // Rotas para Produtos
    $routes->get('produtos', 'ProdutosController::index');
    $routes->get('produtos/new', 'ProdutosController::new');
    $routes->post('produtos/create', 'ProdutosController::create');
    $routes->get('produtos/edit/(:num)', 'ProdutosController::edit/$1');
    $routes->post('produtos/update/(:num)', 'ProdutosController::update/$1');
    $routes->post('produtos/delete/(:num)', 'ProdutosController::delete/$1');
});