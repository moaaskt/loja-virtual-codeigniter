<?php

namespace App\Services;

use App\Models\CupomModel;
use App\Models\ProdutoModel;

class CarrinhoService
{
    protected ProdutoModel $produtoModel;
    protected CupomModel $cupomModel;

    public function __construct()
    {
        $this->produtoModel = new ProdutoModel();
        $this->cupomModel   = new CupomModel();
    }

    public function getCarrinho(): array
    {
        return session()->get('carrinho') ?? [];
    }

    public function limpar(): void
    {
        session()->remove('carrinho');
        session()->remove('cupom');
        session()->remove('frete');
    }

    /**
     * Adiciona produto ao carrinho. Retorna ['ok' => bool, 'erro' => string].
     */
    public function adicionar(int $produtoId, int $quantidade, int $variacaoId = 0): array
    {
        $produto = $this->produtoModel->find($produtoId);

        if (!$produto) {
            return ['ok' => false, 'erro' => 'Produto não encontrado!'];
        }

        $db = \Config\Database::connect('default');
        $variacao = null;
        if ($variacaoId > 0) {
            $variacao = $db->table('produto_variacoes')->where('id', $variacaoId)->where('produto_id', $produtoId)->get()->getRowArray();
            if (!$variacao) {
                return ['ok' => false, 'erro' => 'Variação inválida.'];
            }
            $estoqueDisponivel = (int) $variacao['estoque'];
        } else {
            $temVariacoes = $db->table('produto_variacoes')->where('produto_id', $produtoId)->countAllResults() > 0;
            if ($temVariacoes) {
                return ['ok' => false, 'erro' => 'Por favor, selecione um tamanho e/ou cor.'];
            }
            $estoqueDisponivel = (int) $produto['estoque'];
        }

        if ($estoqueDisponivel <= 0) {
            return ['ok' => false, 'erro' => 'Produto fora de estoque.'];
        }

        if ($quantidade < 1) {
            return ['ok' => false, 'erro' => 'A quantidade mínima é 1.'];
        }

        $carrinho             = $this->getCarrinho();
        $cartKey              = $produtoId . '_' . $variacaoId;
        $quantidadeNoCarrinho = isset($carrinho[$cartKey]) ? (int) $carrinho[$cartKey]['quantidade'] : 0;
        $quantidadeTotal      = $quantidadeNoCarrinho + $quantidade;

        if ($quantidadeTotal > $estoqueDisponivel) {
            $disponivelParaAdicionar = $estoqueDisponivel - $quantidadeNoCarrinho;
            return ['ok' => false, 'erro' => "Estoque insuficiente. Você pode adicionar no máximo {$disponivelParaAdicionar} unidade(s) deste item."];
        }

        if (isset($carrinho[$cartKey])) {
            $carrinho[$cartKey]['quantidade'] = $quantidadeTotal;
        } else {
            $carrinho[$cartKey] = [
                'id'          => $produto['id'],
                'variacao_id' => $variacaoId,
                'nome'        => $produto['nome'],
                'preco'       => $produto['preco'],
                'imagem'      => $produto['imagem'],
                'quantidade'  => $quantidade,
                'tamanho'     => $variacao ? $variacao['tamanho'] : '',
                'cor'         => $variacao ? $variacao['cor'] : '',
            ];
        }

        session()->set('carrinho', $carrinho);
        return ['ok' => true];
    }

    /**
     * Atualiza quantidade de um item no carrinho.
     */
    public function atualizar(string $cartKey, int $quantidade): array
    {
        if ($quantidade < 1) {
            return ['ok' => false, 'erro' => 'Quantidade inválida.'];
        }

        $carrinho = $this->getCarrinho();
        if (!isset($carrinho[$cartKey])) {
            return ['ok' => false, 'erro' => 'Item não encontrado no carrinho.'];
        }

        $item = $carrinho[$cartKey];
        $produtoId = $item['id'];
        $variacaoId = $item['variacao_id'] ?? 0;

        $db = \Config\Database::connect('default');
        if ($variacaoId > 0) {
            $variacao = $db->table('produto_variacoes')->where('id', $variacaoId)->where('produto_id', $produtoId)->get()->getRowArray();
            $estoqueDisponivel = $variacao ? (int) $variacao['estoque'] : 0;
        } else {
            $produto = $this->produtoModel->find($produtoId);
            $estoqueDisponivel = $produto ? (int) $produto['estoque'] : 0;
        }

        if ($quantidade > $estoqueDisponivel) {
            return ['ok' => false, 'erro' => "Estoque insuficiente. Máximo disponível: {$estoqueDisponivel} unidade(s)."];
        }

        $carrinho[$cartKey]['quantidade'] = $quantidade;
        session()->set('carrinho', $carrinho);

        return ['ok' => true];
    }

    /**
     * Remove item do carrinho.
     */
    public function remover(string $cartKey): void
    {
        $carrinho = $this->getCarrinho();
        if (isset($carrinho[$cartKey])) {
            unset($carrinho[$cartKey]);
            session()->set('carrinho', $carrinho);
        }

        if (empty($carrinho)) {
            $this->limpar();
        }
    }

    public function calcularSubtotal(): float
    {
        $subtotal = 0.0;
        foreach ($this->getCarrinho() as $item) {
            $subtotal += $item['preco'] * $item['quantidade'];
        }
        return $subtotal;
    }

    // Alias retrocompatível
    public function calcularTotal(): float
    {
        return $this->calcularSubtotal();
    }

    // ----------------------------------------------------------------
    // Gestão de Cupons na Sessão
    // ----------------------------------------------------------------
    public function getCupom(): ?array
    {
        return session()->get('cupom');
    }

    public function aplicarCupom(string $codigo): array
    {
        $subtotal  = $this->calcularSubtotal();
        $resultado = $this->cupomModel->validarCupom($codigo, $subtotal);

        if (!$resultado['valido']) {
            return ['ok' => false, 'erro' => $resultado['erro']];
        }

        session()->set('cupom', [
            'id'       => $resultado['cupom']['id'],
            'codigo'   => $resultado['cupom']['codigo'],
            'tipo'     => $resultado['cupom']['tipo'],
            'valor'    => (float) $resultado['cupom']['valor'],
            'desconto' => (float) $resultado['desconto'],
        ]);

        return ['ok' => true, 'mensagem' => $resultado['mensagem'], 'desconto' => $resultado['desconto']];
    }

    public function removerCupom(): void
    {
        session()->remove('cupom');
    }

    // ----------------------------------------------------------------
    // Gestão de Frete na Sessão
    // ----------------------------------------------------------------
    public function getFrete(): ?array
    {
        return session()->get('frete');
    }

    public function selecionarFrete(string $modalidade, float $valor, string $prazo, string $cep): void
    {
        session()->set('frete', [
            'modalidade' => $modalidade,
            'valor'      => max(0.0, (float) $valor),
            'prazo'      => $prazo,
            'cep'        => $cep,
        ]);
    }

    public function removerFrete(): void
    {
        session()->remove('frete');
    }

    // ----------------------------------------------------------------
    // Consolidação de Totais (Subtotal - Desconto + Frete)
    // ----------------------------------------------------------------
    public function calcularTotais(): array
    {
        $subtotal = $this->calcularSubtotal();
        $desconto = 0.0;
        $cupom    = $this->getCupom();

        // Se houver cupom na sessão, revalida contra o subtotal atual
        if ($cupom) {
            $validacao = $this->cupomModel->validarCupom($cupom['codigo'], $subtotal);
            if ($validacao['valido']) {
                $desconto = (float) $validacao['desconto'];
                $cupom['desconto'] = $desconto;
                session()->set('cupom', $cupom);
            } else {
                $this->removerCupom();
                $cupom = null;
            }
        }

        $frete     = $this->getFrete();
        $freteValor = $frete ? (float) $frete['valor'] : 0.0;

        $totalFinal = max(0.0, $subtotal - $desconto) + $freteValor;

        return [
            'subtotal'   => $subtotal,
            'desconto'   => $desconto,
            'frete'      => $freteValor,
            'total'      => $totalFinal,
            'cupom'      => $cupom,
            'frete_info' => $frete,
        ];
    }
}
