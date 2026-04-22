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
        $produtoId = (int) $this->request->getPost('produto_id');
        $quantidade = (int) $this->request->getPost('quantidade');

        $resultado = $this->carrinhoService->adicionar($produtoId, $quantidade);

        if (!$resultado['ok']) {
            return redirect()->back()->with('error', $resultado['erro']);
        }

        return redirect()->back()->with('success', 'Produto adicionado ao carrinho!');
    }

    public function atualizar()
    {
        $produtoId = (int) $this->request->getPost('produto_id');
        $quantidade = (int) $this->request->getPost('quantidade');

        $resultado = $this->carrinhoService->atualizar($produtoId, $quantidade);

        if (!$resultado['ok']) {
            return redirect()->to(site_url('carrinho'))->with('error', $resultado['erro']);
        }

        return redirect()->to(site_url('carrinho'))->with('success', 'Quantidade atualizada!');
    }

    public function remover($produto_id = null)
    {
        $this->carrinhoService->remover((int) $produto_id);
        return redirect()->to(site_url('carrinho'))->with('success', 'Produto removido do carrinho!');
    }
}
