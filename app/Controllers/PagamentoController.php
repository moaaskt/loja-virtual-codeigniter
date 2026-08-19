<?php

namespace App\Controllers;

use App\Models\PagamentoModel;
use App\Models\PedidoModel;
use App\Models\PedidoProdutoModel;

class PagamentoController extends BaseController
{
    protected PedidoModel $pedidoModel;
    protected PagamentoModel $pagamentoModel;
    protected PedidoProdutoModel $pedidoProdutoModel;

    public function __construct()
    {
        $this->pedidoModel        = new PedidoModel();
        $this->pagamentoModel     = new PagamentoModel();
        $this->pedidoProdutoModel = new PedidoProdutoModel();
        helper('status');
    }

    /**
     * Exibe a página de pagamento do pedido (com QR Code Pix, Copia e Cola ou detalhes de cartão).
     */
    public function show($pedidoId = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))->with('error', 'Faça login para visualizar seu pedido.');
        }

        $usuarioId = (int) session()->get('usuario_id');
        $isAdmin   = (session()->get('usuario_nivel') === 'admin');

        $pedido = $this->pedidoModel->find((int) $pedidoId);

        if (!$pedido || (!$isAdmin && (int) $pedido['usuario_id'] !== $usuarioId)) {
            return redirect()->to(site_url('/'))->with('error', 'Pedido não encontrado.');
        }

        $pagamento = $this->pagamentoModel->buscarPorPedido((int) $pedidoId);
        $itens     = $this->pedidoProdutoModel->getProdutosDePedido((int) $pedidoId);

        return view('shop/pedido_pagamento', [
            'title'     => 'Pagamento do Pedido #' . $pedido['id'],
            'pedido'    => $pedido,
            'pagamento' => $pagamento,
            'itens'     => $itens,
        ]);
    }

    /**
     * Endpoint JSON para polling de status do pagamento no front-end.
     * Rota: GET api/pedidos/(:num)/status-pagamento
     */
    public function status($pedidoId = null)
    {
        $pedido = $this->pedidoModel->find((int) $pedidoId);

        if (!$pedido) {
            return $this->response->setStatusCode(404)->setJSON([
                'ok'    => false,
                'erro'  => 'Pedido não encontrado.',
            ]);
        }

        $pagamento = $this->pagamentoModel->buscarPorPedido((int) $pedidoId);

        $statusPagamento = $pagamento['status'] ?? $pedido['status_pagamento'] ?? 'pendente';
        $statusPedido    = $pedido['status'];
        $isPago          = in_array($statusPagamento, ['pago', 'aprovado']) || $statusPedido === 'pago';

        return $this->response->setJSON([
            'ok'               => true,
            'pedido_id'        => (int) $pedidoId,
            'status_pagamento' => $statusPagamento,
            'status_pedido'    => $statusPedido,
            'pago'             => $isPago,
            'pago_em'          => $pagamento['pago_em'] ?? null,
        ]);
    }
}
