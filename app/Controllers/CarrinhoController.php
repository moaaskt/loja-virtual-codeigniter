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
        $totais = $this->carrinhoService->calcularTotais();

        return view('shop/carrinho', [
            'title'    => 'Meu Carrinho de Compras',
            'carrinho' => $this->carrinhoService->getCarrinho(),
            'totais'   => $totais,
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

        return redirect()->to(site_url('carrinho'))->with('success', 'Produto adicionado ao carrinho!');
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

    public function aplicarCupom()
    {
        $codigo    = $this->request->getPost('codigo') ?? $this->request->getVar('codigo') ?? '';
        $resultado = $this->carrinhoService->aplicarCupom($codigo);

        if ($this->request->isAJAX()) {
            $totais = $this->carrinhoService->calcularTotais();
            return $this->response->setJSON(array_merge($resultado, ['totais' => $totais]));
        }

        if (!$resultado['ok']) {
            return redirect()->to(site_url('carrinho'))->with('error', $resultado['erro']);
        }

        return redirect()->to(site_url('carrinho'))->with('success', $resultado['mensagem']);
    }

    public function removerCupom()
    {
        $this->carrinhoService->removerCupom();

        if ($this->request->isAJAX()) {
            $totais = $this->carrinhoService->calcularTotais();
            return $this->response->setJSON(['ok' => true, 'totais' => $totais]);
        }

        return redirect()->to(site_url('carrinho'))->with('success', 'Cupom removido!');
    }

    public function selecionarFrete()
    {
        $json = $this->request->getJSON(true) ?? [];
        $modalidade = $json['modalidade'] ?? $this->request->getPost('modalidade') ?? $this->request->getVar('modalidade') ?? 'Padrão';
        $valor      = (float) ($json['valor'] ?? $this->request->getPost('valor') ?? $this->request->getVar('valor') ?? 0);
        $prazo      = $json['prazo'] ?? $this->request->getPost('prazo') ?? $this->request->getVar('prazo') ?? '';
        $cep        = $json['cep'] ?? $this->request->getPost('cep') ?? $this->request->getVar('cep') ?? '';

        $this->carrinhoService->selecionarFrete($modalidade, $valor, $prazo, $cep);

        if ($this->request->isAJAX()) {
            $totais = $this->carrinhoService->calcularTotais();
            return $this->response->setJSON(['ok' => true, 'success' => true, 'totais' => $totais]);
        }

        return redirect()->to(site_url('carrinho'))->with('success', 'Frete selecionado com sucesso!');
    }

    public function removerFrete()
    {
        $this->carrinhoService->removerFrete();

        if ($this->request->isAJAX()) {
            $totais = $this->carrinhoService->calcularTotais();
            return $this->response->setJSON(['ok' => true, 'success' => true, 'totais' => $totais]);
        }

        return redirect()->to(site_url('carrinho'))->with('success', 'Frete removido!');
    }
}
