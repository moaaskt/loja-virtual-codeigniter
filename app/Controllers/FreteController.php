<?php

namespace App\Controllers;

use App\Models\ProdutoModel;
use App\Services\CarrinhoService;
use App\Services\FreteService;

class FreteController extends BaseController
{
    protected FreteService $freteService;
    protected CarrinhoService $carrinhoService;

    public function __construct()
    {
        $this->freteService    = new FreteService();
        $this->carrinhoService = new CarrinhoService();
    }

    /**
     * Endpoint API para simulação e cálculo de frete.
     * Aceita POST com JSON ou FormData: { cep, produto_id?, quantidade?, subtotal? }
     */
    public function calcular()
    {
        $cep       = $this->request->getVar('cep') ?? '';
        $produtoId = (int) ($this->request->getVar('produto_id') ?? 0);
        $qtd       = max(1, (int) ($this->request->getVar('quantidade') ?? 1));
        $subtotal  = (float) ($this->request->getVar('subtotal') ?? 0);

        $temFreteGratis = false;

        if ($produtoId > 0) {
            $produtoModel = new ProdutoModel();
            $produto = $produtoModel->find($produtoId);
            if ($produto) {
                $temFreteGratis = !empty($produto['frete_gratis']) && (int) $produto['frete_gratis'] === 1;
                $subtotal       = (float) $produto['preco'] * $qtd;
            }
        } elseif ($subtotal <= 0) {
            $subtotal = $this->carrinhoService->calcularTotal();
            // Verifica se algum item no carrinho possui frete grátis
            $carrinho = $this->carrinhoService->getCarrinho();
            $produtoModel = new ProdutoModel();
            foreach ($carrinho as $item) {
                $p = $produtoModel->find($item['id']);
                if ($p && !empty($p['frete_gratis'])) {
                    $temFreteGratis = true;
                    break;
                }
            }
        }

        $resultado = $this->freteService->calcular($cep, $subtotal, $temFreteGratis);

        return $this->response->setJSON($resultado);
    }
}
