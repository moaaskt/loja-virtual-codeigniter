<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --------------------------------------------------------------------
// ROTAS PÚBLICAS (CLIENTE)
// --------------------------------------------------------------------
$routes->get('/', 'HomeController::index');
$routes->get('produto/(:num)', 'HomeController::produto/$1');
$routes->get('categoria/(:num)', 'HomeController::produtosPorCategoria/$1');
$routes->get('busca', 'HomeController::busca');
$routes->get('api/produtos/busca', 'HomeController::buscaApi');

// Carrinho
$routes->get('carrinho', 'CarrinhoController::index');
$routes->post('carrinho/adicionar', 'CarrinhoController::adicionar');
$routes->post('carrinho/atualizar', 'CarrinhoController::atualizar');
$routes->post('carrinho/remover/(:num)', 'CarrinhoController::remover/$1');

// Autenticação
$routes->get('login', 'AuthController::login', ['filter' => 'guest']);
$routes->post('auth/attempt-login', 'AuthController::attemptLogin', ['filter' => 'guest']);
$routes->get('registrar', 'AuthController::registrar', ['filter' => 'guest']);
$routes->post('registrar/salvar', 'AuthController::attemptRegister');
$routes->get('logout', 'AuthController::logout');

// Área do Cliente Logado
$routes->group('minha-conta', ['filter' => 'auth'], static function ($routes) {
    $routes->get('pedidos', 'ClienteController::index');
});
$routes->post('checkout/finalizar', 'PedidoController::finalizar', ['filter' => 'auth']);
$routes->get('pedido/sucesso', 'PedidoController::sucesso', ['filter' => 'auth']);

// --- ROTAS DO PAINEL ADMINISTRATIVO ---
$routes->group('admin', ['filter' => ['auth', 'admin']], static function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'Admin\AdminController::index');

    // Pedidos
    $routes->get('pedidos', 'Admin\PedidoController::index');
    $routes->get('pedidos/detalhe/(:num)', 'Admin\PedidoController::detalhe/$1');
    $routes->post('pedidos/atualizar-status/(:num)', 'Admin\PedidoController::atualizarStatus/$1');

    // Categorias
    $routes->get('categorias', 'Admin\CategoriasController::index');
    $routes->get('categorias/new', 'Admin\CategoriasController::new');
    $routes->post('categorias/create', 'Admin\CategoriasController::create');
    $routes->get('categorias/edit/(:num)', 'Admin\CategoriasController::edit/$1');
    // A linha abaixo foi a principal corrigida:
    $routes->post('categorias/update/(:num)', 'Admin\CategoriasController::update/$1'); 
    $routes->post('categorias/delete/(:num)', 'Admin\CategoriasController::delete/$1');

    // Produtos
    $routes->get('produtos', 'Admin\ProdutosController::index');
    $routes->get('produtos/new', 'Admin\ProdutosController::new');
    $routes->post('produtos/create', 'Admin\ProdutosController::create');
    $routes->get('produtos/edit/(:num)', 'Admin\ProdutosController::edit/$1');
    $routes->post('produtos/update/(:num)', 'Admin\ProdutosController::update/$1');
    $routes->post('produtos/delete/(:num)', 'Admin\ProdutosController::delete/$1');
});