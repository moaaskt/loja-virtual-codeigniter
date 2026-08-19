<?php

namespace App\Services;

use App\Models\CupomModel;
use App\Models\PedidoModel;
use App\Models\PedidoProdutoModel;
use App\Models\ProdutoModel;

class PedidoService
{
    protected PedidoModel $pedidoModel;
    protected PedidoProdutoModel $pedidoProdutoModel;
    protected ProdutoModel $produtoModel;
    protected CupomModel $cupomModel;

    public function __construct()
    {
        $this->pedidoModel        = new PedidoModel();
        $this->pedidoProdutoModel = new PedidoProdutoModel();
        $this->produtoModel       = new ProdutoModel();
        $this->cupomModel         = new CupomModel();
    }

    /**
     * Cria um pedido completo dentro de uma transaction.
     * Retorna ['ok' => true, 'pedido_id' => int] ou ['ok' => false, 'erro' => string].
     */
    public function criarPedido(
        array $carrinho,
        int $clienteId,
        array $enderecoData = [],
        ?array $cupomData = null,
        ?array $freteData = null
    ): array {
        $camposObrigatorios = ['cep', 'logradouro', 'numero', 'bairro', 'cidade', 'uf'];
        foreach ($camposObrigatorios as $campo) {
            if (empty($enderecoData[$campo])) {
                return ['ok' => false, 'erro' => 'Por favor, preencha todos os campos obrigatórios de endereço.'];
            }
        }

        $db = \Config\Database::connect('default');
        $db->transStart();

        $itensPedido = [];
        $subtotal    = 0.0;

        foreach ($carrinho as $cartKey => $item) {
            $produtoId = $item['id'];
            $produto = $this->produtoModel->find((int) $produtoId);

            if (!$produto) {
                $db->transRollback();
                return ['ok' => false, 'erro' => 'O produto "' . esc($item['nome']) . '" não está mais disponível.'];
            }

            $subtotal += (float) $produto['preco'] * (int) $item['quantidade'];
            $itensPedido[$cartKey] = ['item' => $item, 'produto' => $produto];
        }

        // Processamento de Cupom
        $descontoValor = 0.0;
        $cupomCodigo   = null;
        $cupomId       = null;

        $cupomSessao = $cupomData ?? session()->get('cupom');
        if (!empty($cupomSessao['codigo'])) {
            $validacaoCupom = $this->cupomModel->validarCupom($cupomSessao['codigo'], $subtotal);
            if ($validacaoCupom['valido']) {
                $descontoValor = (float) $validacaoCupom['desconto'];
                $cupomCodigo   = $validacaoCupom['cupom']['codigo'];
                $cupomId       = (int) $validacaoCupom['cupom']['id'];
            }
        }

        // Processamento de Frete
        $freteSessao     = $freteData ?? session()->get('frete');
        $freteValor      = 0.0;
        $freteModalidade = null;

        if (!empty($freteSessao)) {
            $freteValor      = max(0.0, (float) ($freteSessao['valor'] ?? 0));
            $freteModalidade = $freteSessao['modalidade'] ?? 'Padrão';
        }

        $valorTotal = max(0.0, $subtotal - $descontoValor) + $freteValor;

        $this->pedidoModel->insert([
            'usuario_id'       => $clienteId,
            'valor_total'      => $valorTotal,
            'cupom_codigo'     => $cupomCodigo,
            'desconto_valor'   => $descontoValor,
            'frete_modalidade' => $freteModalidade,
            'frete_valor'      => $freteValor,
            'status'           => 'pendente',
            'cep'              => $enderecoData['cep'],
            'logradouro'       => $enderecoData['logradouro'],
            'numero'           => $enderecoData['numero'],
            'complemento'      => $enderecoData['complemento'] ?? null,
            'bairro'           => $enderecoData['bairro'],
            'cidade'           => $enderecoData['cidade'],
            'uf'               => $enderecoData['uf'],
        ]);
        $pedidoId = $this->pedidoModel->getInsertID();

        foreach ($itensPedido as $cartKey => $dados) {
            $item       = $dados['item'];
            $produto    = $dados['produto'];
            $produtoId  = $item['id'];
            $variacaoId = $item['variacao_id'] ?? 0;
            $quantidade = (int) $item['quantidade'];

            // Baixa de estoque
            if ($variacaoId > 0) {
                $variacao = $db->table('produto_variacoes')->where('id', $variacaoId)->where('produto_id', $produtoId)->get()->getRowArray();
                if (!$variacao || $variacao['estoque'] < $quantidade) {
                    $db->transRollback();
                    return ['ok' => false, 'erro' => 'Estoque insuficiente para a variação de "' . esc($item['nome']) . '".'];
                }

                $db->table('produto_variacoes')
                   ->where('id', $variacaoId)
                   ->set('estoque', 'estoque - ' . $quantidade, false)
                   ->update();

                $this->produtoModel->decrementarEstoque((int) $produtoId, $quantidade, $db);
            } else {
                $ok = $this->produtoModel->decrementarEstoque((int) $produtoId, $quantidade, $db);
                if (!$ok) {
                    $db->transRollback();
                    return ['ok' => false, 'erro' => 'Estoque insuficiente para "' . esc($item['nome']) . '". Verifique seu carrinho.'];
                }
            }

            $this->pedidoProdutoModel->insert([
                'pedido_id'      => $pedidoId,
                'produto_id'     => $produtoId,
                'variacao_id'    => $variacaoId > 0 ? $variacaoId : null,
                'tamanho'        => $item['tamanho'] ?? null,
                'cor'            => $item['cor'] ?? null,
                'quantidade'     => $quantidade,
                'preco_unitario' => $produto['preco'],
            ]);
        }

        // Incrementa uso do cupom
        if ($cupomId) {
            $this->cupomModel->incrementarUso($cupomId);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return ['ok' => false, 'erro' => 'Houve um erro ao processar seu pedido. Tente novamente.'];
        }

        // Limpa sessões de compra
        session()->remove('carrinho');
        session()->remove('cupom');
        session()->remove('frete');

        return ['ok' => true, 'pedido_id' => $pedidoId];
    }
}
