<?php
namespace App\Controllers;

class AdminController extends BaseController
{
    public function index()
{
    $produtoModel = new \App\Models\ProdutoModel();
    $pedidoModel = new \App\Models\PedidoModel();

    $data = [
        'title' => 'Dashboard',
        'total_produtos' => $produtoModel->countAllResults(),
        'total_pedidos' => $pedidoModel->countAllResults(),
        'baixo_estoque' => $produtoModel->where('estoque <=', 5)->countAllResults(),
        'sem_estoque' => $produtoModel->where('estoque', 0)->countAllResults(),
    ];

    return view('admin/dashboard', $data);
}
}