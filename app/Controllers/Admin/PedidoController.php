<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PedidoModel;

class PedidoController extends BaseController
{
    // Boa prática: definir os status permitidos em um só lugar
    private $statusPermitidos = ['processando', 'enviado', 'concluido', 'cancelado'];

    /**
     * Exibe a lista de todos os pedidos.
     */
    public function index()
    {
        $model = new PedidoModel();

        $data = [
            'title'          => 'Gerenciamento de Pedidos',
            'pedidos'        => $model->getAllPedidosComCliente(15),
            'pager'          => $model->pager,
            'status_options' => $this->statusPermitidos // Envia os status para a view
        ];

        return view('admin/pedidos/index', $data);
    }

    /**
     * Atualiza o status de um pedido específico.
     */
    public function atualizarStatus($id = null)
    {
        $model = new PedidoModel();

        // Pega o novo status enviado pelo formulário
        $novoStatus = $this->request->getPost('status');

        // Validação: verifica se o status enviado é um dos permitidos
        if (empty($novoStatus) || !in_array($novoStatus, $this->statusPermitidos)) {
            return redirect()->to(site_url('admin/pedidos'))->with('error', 'Status inválido.');
        }

        // Tenta atualizar o pedido
        if ($model->update($id, ['status' => $novoStatus])) {
            return redirect()->to(site_url('admin/pedidos'))->with('success', 'Status do pedido atualizado com sucesso!');
        } else {
            return redirect()->to(site_url('admin/pedidos'))->with('error', 'Erro ao atualizar o status do pedido.');
        }
    }
}