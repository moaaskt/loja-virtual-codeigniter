<?php

namespace App\Controllers;

use App\Models\PedidoModel; // Vamos precisar deste model

class ClienteController extends BaseController
{
    public function __construct()
    {
        helper(['form', 'url']);
    }

    /**
     * Exibe o histórico de pedidos do usuário logado.
     */


    
public function index()
{
    $pedidoModel = new PedidoModel();
    $pedidoProdutoModel = new \App\Models\PedidoProdutoModel();
    $usuarioId = session()->get('usuario_id');

    $pedidos = $pedidoModel->getPedidosPorUsuario($usuarioId);
    $itens_dos_pedidos = [];

    if (!empty($pedidos)) {
        // Pega apenas os IDs de todos os pedidos
        $pedidoIds = array_column($pedidos, 'id');

        // Busca todos os produtos de todos os pedidos de uma só vez
       $produtos = $pedidoProdutoModel->getProdutosDePedido($pedidoIds);

        // Organiza os produtos por pedido_id para facilitar o uso na view
        foreach ($produtos as $produto) {
            $itens_dos_pedidos[$produto['pedido_id']][] = $produto;
        }
    }

    $data = [
        'title'             => 'Meus Pedidos',
        'pedidos'           => $pedidos,
        'itens_dos_pedidos' => $itens_dos_pedidos
    ];

    return view('cliente/meus_pedidos', $data);
}




}