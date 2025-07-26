<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\PedidoModel;
use App\Models\PedidoProdutoModel;

class PedidoController extends BaseController
{
    // ... (seu método index() continua aqui, sem alterações) ...
    public function index()
    {
        $model = new PedidoModel();

        $data = [
            'title'          => 'Gerenciamento de Pedidos',
            'pedidos'        => $model->getAllPedidosComCliente(15),
            'pager'          => $model->pager,
            'status_options' => ['processando', 'enviado', 'concluido', 'cancelado']
        ];

        return view('admin/pedidos/index', $data);
    }


    // O método detalhe() foi corrigido
    public function detalhe($id = null)
    {
        $pedidoModel = new PedidoModel();
        // Agora podemos usar o nome curto da classe
        $pedidoProdutoModel = new PedidoProdutoModel();

        $pedido = $pedidoModel->getPedidoComCliente($id);

        if ($pedido === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title'          => 'Detalhes do Pedido #' . $pedido['id'],
            'pedido'         => $pedido,
            'produtos' => $pedidoProdutoModel->getProdutosDePedido($id),
            'status_options' => ['processando', 'enviado', 'concluido', 'cancelado']
        ];

        return view('admin/pedidos/detalhe', $data);
    }

    // Adicionei o método de atualizar status que tínhamos antes
    public function atualizarStatus($id = null)
    {
        $model = new PedidoModel();
        $novoStatus = $this->request->getPost('status');
        $statusPermitidos = ['processando', 'enviado', 'concluido', 'cancelado'];

        if (empty($novoStatus) || !in_array($novoStatus, $statusPermitidos)) {
            return redirect()->to(site_url('admin/pedidos'))->with('error', 'Status inválido.');
        }

        if ($model->update($id, ['status' => $novoStatus])) {
            // Redireciona de volta para a página de DETALHES com a mensagem
            return redirect()->to(site_url('admin/pedidos/detalhe/' . $id))->with('success', 'Status do pedido atualizado!');
        } else {
            return redirect()->to(site_url('admin/pedidos/detalhe/' . $id))->with('error', 'Erro ao atualizar o status.');
        }
    }
}