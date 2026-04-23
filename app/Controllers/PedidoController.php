<?php

namespace App\Controllers;

use App\Services\PedidoService;

class PedidoController extends BaseController
{
    protected PedidoService $pedidoService;

    public function __construct()
    {
        $this->pedidoService = new PedidoService();
    }

    public function sucesso()
    {
        return view('shop/pedido_sucesso', ['title' => 'Pedido Realizado!']);
    }

    public function finalizar()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('/'))->with('error', 'Você precisa estar logado para finalizar a compra.');
        }

        $carrinho = session()->get('carrinho') ?? [];
        if (empty($carrinho)) {
            return redirect()->to(site_url('carrinho'))->with('error', 'Seu carrinho está vazio.');
        }

        $enderecoData = $this->request->getPost(['cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'uf']);
        $resultado = $this->pedidoService->criarPedido($carrinho, (int) session()->get('usuario_id'), $enderecoData);

        if (!$resultado['ok']) {
            return redirect()->to(site_url('carrinho'))->with('error', $resultado['erro']);
        }

        session()->remove('carrinho');
        return redirect()->to(site_url('pedido/sucesso'))->with('success', 'Seu pedido foi realizado com sucesso!');
    }
}
