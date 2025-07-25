<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PedidoModel;

class PedidoController extends BaseController
{
    /**
     * Exibe a lista de todos os pedidos.
     */
    public function index()
{
    $model = new PedidoModel();

    $data = [
        'title'   => 'Gerenciamento de Pedidos',
        'pedidos' => $model->getAllPedidosComCliente(15), // 15 pedidos por página
        'pager'   => $model->pager
    ];

    return view('admin/pedidos/index', $data);
}
}