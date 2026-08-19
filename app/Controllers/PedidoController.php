<?php

namespace App\Controllers;

use App\Models\PagamentoModel;
use App\Models\PedidoModel;
use App\Models\PedidoProdutoModel;
use App\Services\PedidoService;

class PedidoController extends BaseController
{
    protected PedidoService $pedidoService;
    protected PedidoModel $pedidoModel;
    protected PagamentoModel $pagamentoModel;
    protected PedidoProdutoModel $pedidoProdutoModel;

    public function __construct()
    {
        $this->pedidoService      = new PedidoService();
        $this->pedidoModel        = new PedidoModel();
        $this->pagamentoModel     = new PagamentoModel();
        $this->pedidoProdutoModel = new PedidoProdutoModel();
        helper('status');
    }

    public function checkout()
    {
        if (!session()->get('isLoggedIn')) {
            session()->set('redirect_url', site_url('checkout'));
            return redirect()->to(site_url('login'))->with('info', 'Faça login ou crie sua conta para finalizar o seu pedido.');
        }

        $carrinhoService = new \App\Services\CarrinhoService();
        $carrinho = $carrinhoService->getCarrinho();

        if (empty($carrinho)) {
            return redirect()->to(site_url('carrinho'))->with('error', 'Seu carrinho está vazio.');
        }

        $totais = $carrinhoService->calcularTotais();

        return view('shop/checkout', [
            'title'    => 'Checkout Seguro',
            'carrinho' => $carrinho,
            'totais'   => $totais,
        ]);
    }

    public function sucesso($pedidoId = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('/'));
        }

        $pedido = null;
        $pagamento = null;
        $itens = [];

        if ($pedidoId) {
            $pedido = $this->pedidoModel->find((int) $pedidoId);
            if ($pedido && (int) $pedido['usuario_id'] === (int) session()->get('usuario_id')) {
                $pagamento = $this->pagamentoModel->buscarPorPedido((int) $pedidoId);
                $itens     = $this->pedidoProdutoModel->getProdutosDePedido((int) $pedidoId);
            }
        }

        return view('shop/pedido_sucesso', [
            'title'     => 'Pedido Realizado com Sucesso!',
            'pedido'    => $pedido,
            'pagamento' => $pagamento,
            'itens'     => $itens,
        ]);
    }

    public function finalizar()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))->with('error', 'Você precisa estar logado para finalizar a compra.');
        }

        $carrinho = session()->get('carrinho') ?? [];
        if (empty($carrinho)) {
            return redirect()->to(site_url('carrinho'))->with('error', 'Seu carrinho está vazio.');
        }

        $enderecoData = $this->request->getPost(['cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'uf']);
        $pagamentoData = $this->request->getPost(['forma_pagamento', 'cartao_numero', 'cartao_nome', 'cartao_validade', 'cartao_cvv', 'cartao_parcelas']);

        $resultado = $this->pedidoService->criarPedido(
            $carrinho,
            (int) session()->get('usuario_id'),
            $enderecoData,
            null,
            null,
            $pagamentoData
        );

        if (!$resultado['ok']) {
            return redirect()->to(site_url('checkout'))->with('error', $resultado['erro'])->withInput();
        }

        $pedidoId = $resultado['pedido_id'];

        if (($resultado['forma_pagamento'] ?? '') === 'pix') {
            return redirect()->to(site_url('pedido/pagamento/' . $pedidoId))
                ->with('success', 'Pedido gerado com sucesso! Utilize o QR Code ou código Pix para concluir o pagamento.');
        }

        return redirect()->to(site_url('pedido/sucesso/' . $pedidoId))
            ->with('success', 'Pagamento aprovado e pedido realizado com sucesso!');
    }
}
