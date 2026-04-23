<?php

namespace App\Controllers;

use App\Services\CarrinhoService;

class CarrinhoController extends BaseController
{
    protected CarrinhoService $carrinhoService;

    public function __construct()
    {
        $this->carrinhoService = new CarrinhoService();
    }

    public function index()
    {
        return view('shop/carrinho', [
            'title'    => 'Meu Carrinho de Compras',
            'carrinho' => $this->carrinhoService->getCarrinho(),
        ]);
    }

    public function adicionar()
    {
        $produtoId  = (int) $this->request->getPost('produto_id');
        $quantidade = (int) $this->request->getPost('quantidade');
        $variacaoId = (int) $this->request->getPost('variacao_id');

        $resultado = $this->carrinhoService->adicionar($produtoId, $quantidade, $variacaoId);

        if (!$resultado['ok']) {
            return redirect()->back()->with('error', $resultado['erro']);
        }

        return redirect()->back()->with('success', 'Produto adicionado ao carrinho!');
    }

    public function atualizar()
    {
        $cartKey    = $this->request->getPost('cart_key');
        $quantidade = (int) $this->request->getPost('quantidade');

        $resultado = $this->carrinhoService->atualizar($cartKey, $quantidade);

        if (!$resultado['ok']) {
            return redirect()->to(site_url('carrinho'))->with('error', $resultado['erro']);
        }

        return redirect()->to(site_url('carrinho'))->with('success', 'Quantidade atualizada!');
    }

    public function remover($cartKey = null)
    {
        $this->carrinhoService->remover($cartKey);
        return redirect()->to(site_url('carrinho'))->with('success', 'Produto removido do carrinho!');
    }
}
