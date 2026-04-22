<?php

namespace App\Services;

use App\Models\ProdutoModel;

class CarrinhoService
{
    protected ProdutoModel $produtoModel;

    public function __construct()
    {
        $this->produtoModel = new ProdutoModel();
    }

    public function getCarrinho(): array
    {
        return session()->get('carrinho') ?? [];
    }

    public function limpar(): void
    {
        session()->remove('carrinho');
    }

    /**
     * Adiciona produto ao carrinho. Retorna ['ok' => bool, 'erro' => string].
     */
    public function adicionar(int $produtoId, int $quantidade): array
    {
        $produto = $this->produtoModel->find($produtoId);

        if (!$produto) {
            return ['ok' => false, 'erro' => 'Produto não encontrado!'];
        }

        $estoqueDisponivel = (int) $produto['estoque'];

        if ($estoqueDisponivel <= 0) {
            return ['ok' => false, 'erro' => 'Produto fora de estoque.'];
        }

        if ($quantidade < 1) {
            return ['ok' => false, 'erro' => 'A quantidade mínima é 1.'];
        }

        $carrinho             = $this->getCarrinho();
        $quantidadeNoCarrinho = isset($carrinho[$produtoId]) ? (int) $carrinho[$produtoId]['quantidade'] : 0;
        $quantidadeTotal      = $quantidadeNoCarrinho + $quantidade;

        if ($quantidadeTotal > $estoqueDisponivel) {
            $disponivelParaAdicionar = $estoqueDisponivel - $quantidadeNoCarrinho;
            return ['ok' => false, 'erro' => "Estoque insuficiente. Você pode adicionar no máximo {$disponivelParaAdicionar} unidade(s) deste produto."];
        }

        if (isset($carrinho[$produtoId])) {
            $carrinho[$produtoId]['quantidade'] = $quantidadeTotal;
        } else {
            $carrinho[$produtoId] = [
                'id'         => $produto['id'],
                'nome'       => $produto['nome'],
                'preco'      => $produto['preco'],
                'imagem'     => $produto['imagem'],
                'quantidade' => $quantidade,
            ];
        }

        session()->set('carrinho', $carrinho);
        return ['ok' => true];
    }

    /**
     * Atualiza quantidade de um produto no carrinho.
     */
    public function atualizar(int $produtoId, int $quantidade): array
    {
        if ($quantidade < 1) {
            return ['ok' => false, 'erro' => 'Quantidade inválida.'];
        }

        $produto = $this->produtoModel->find($produtoId);

        if (!$produto) {
            return ['ok' => false, 'erro' => 'Produto não encontrado.'];
        }

        $estoqueDisponivel = (int) $produto['estoque'];

        if ($quantidade > $estoqueDisponivel) {
            return ['ok' => false, 'erro' => "Estoque insuficiente. Máximo disponível: {$estoqueDisponivel} unidade(s)."];
        }

        $carrinho = $this->getCarrinho();

        if (isset($carrinho[$produtoId])) {
            $carrinho[$produtoId]['quantidade'] = $quantidade;
            session()->set('carrinho', $carrinho);
        }

        return ['ok' => true];
    }

    /**
     * Remove produto do carrinho.
     */
    public function remover(int $produtoId): void
    {
        $carrinho = $this->getCarrinho();
        unset($carrinho[$produtoId]);
        session()->set('carrinho', $carrinho);
    }

    public function calcularTotal(): float
    {
        $total = 0.0;
        foreach ($this->getCarrinho() as $item) {
            $total += $item['preco'] * $item['quantidade'];
        }
        return $total;
    }
}
