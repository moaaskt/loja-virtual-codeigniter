<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\PagamentoModel;
use App\Models\PedidoModel;
use App\Models\PedidoProdutoModel;

class PedidoController extends BaseController
{
    public function index()
    {
        $model = new PedidoModel();

        $data = [
            'title'          => 'Gerenciamento de Pedidos',
            'pedidos'        => $model->getAllPedidosComCliente(15),
            'pager'          => $model->pager,
            'status_options' => ['pendente', 'pago', 'processando', 'enviado', 'entregue', 'cancelado'],
        ];

        return view('admin/pedidos/index', $data);
    }

    public function detalhe($id = null)
    {
        $pedidoModel        = new PedidoModel();
        $pedidoProdutoModel = new PedidoProdutoModel();
        $pagamentoModel     = new PagamentoModel();

        $pedido = $pedidoModel->getPedidoComCliente($id);

        if ($pedido === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title'          => 'Detalhes do Pedido #' . $pedido['id'],
            'pedido'         => $pedido,
            'pagamento'      => $pagamentoModel->buscarPorPedido($id),
            'produtos'       => $pedidoProdutoModel->getProdutosDePedido($id),
            'status_options' => ['pendente', 'pago', 'processando', 'enviado', 'entregue', 'cancelado'],
        ];

        return view('admin/pedidos/detalhe', $data);
    }

    public function atualizarStatus($id = null)
    {
        $model = new PedidoModel();
        $novoStatus = $this->request->getPost('status');
        $statusPermitidos = ['pendente', 'pago', 'processando', 'enviado', 'entregue', 'cancelado'];

        if (empty($novoStatus) || !in_array($novoStatus, $statusPermitidos)) {
            return redirect()->to(site_url('admin/pedidos'))->with('error', 'Status inválido.');
        }

        if ($model->update($id, ['status' => $novoStatus])) {
            return redirect()->to(site_url('admin/pedidos/detalhe/' . $id))->with('success', 'Status do pedido atualizado!');
        } else {
            return redirect()->to(site_url('admin/pedidos/detalhe/' . $id))->with('error', 'Erro ao atualizar o status.');
        }
    }
}