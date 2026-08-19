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
    protected PagamentoService $pagamentoService;
    protected EmailService $emailService;

    public function __construct()
    {
        $this->pedidoModel        = new PedidoModel();
        $this->pedidoProdutoModel = new PedidoProdutoModel();
        $this->produtoModel       = new ProdutoModel();
        $this->cupomModel         = new CupomModel();
        $this->pagamentoService   = new PagamentoService();
        $this->emailService       = new EmailService();
    }

    /**
     * Cria um pedido completo dentro de uma transaction e processa o pagamento.
     * Retorna ['ok' => true, 'pedido_id' => int, ...] ou ['ok' => false, 'erro' => string].
     */
    public function criarPedido(
        array $carrinho,
        int $clienteId,
        array $enderecoData = [],
        ?array $cupomData = null,
        ?array $freteData = null,
        ?array $pagamentoData = null
    ): array {
        $camposObrigatorios = ['cep', 'logradouro', 'numero', 'bairro', 'cidade', 'uf'];
        foreach ($camposObrigatorios as $campo) {
            if (empty($enderecoData[$campo])) {
                return ['ok' => false, 'erro' => 'Por favor, preencha todos os campos obrigatórios de endereço.'];
            }
        }

        $formaPagamento = $pagamentoData['forma_pagamento'] ?? 'pix';
        if (!in_array($formaPagamento, ['pix', 'cartao_credito'])) {
            return ['ok' => false, 'erro' => 'Selecione uma forma de pagamento válida (Pix ou Cartão de Crédito).'];
        }

        // Pré-validação rápida se for cartão de crédito
        if ($formaPagamento === 'cartao_credito') {
            $validacaoCartao = $this->pagamentoService->validarDadosCartao($pagamentoData ?? []);
            if (!$validacaoCartao['valido']) {
                return ['ok' => false, 'erro' => $validacaoCartao['erro']];
            }
        }

        $db = \Config\Database::connect('default');
        $db->transStart();

        $itensPedido = [];
        $subtotal    = 0.0;

        foreach ($carrinho as $cartKey => $item) {
            $produtoId  = $item['id'];
            $variacaoId = $item['variacao_id'] ?? 0;
            $produto    = $this->produtoModel->find((int) $produtoId);

            if (!$produto) {
                $db->transRollback();
                return ['ok' => false, 'erro' => 'O produto "' . esc($item['nome']) . '" não está mais disponível.'];
            }

            $precoUnitario = (float) $produto['preco'];
            if ($variacaoId > 0) {
                $varRow = $db->table('produto_variacoes')->where('id', $variacaoId)->where('produto_id', $produtoId)->get()->getRowArray();
                if ($varRow && !empty($varRow['preco']) && (float) $varRow['preco'] > 0) {
                    $precoUnitario = (float) $varRow['preco'];
                }
            }

            $subtotal += $precoUnitario * (int) $item['quantidade'];
            $itensPedido[$cartKey] = [
                'item'           => $item,
                'produto'        => $produto,
                'preco_unitario' => $precoUnitario,
            ];
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
            'forma_pagamento'  => $formaPagamento,
            'status_pagamento' => 'pendente',
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
            $item          = $dados['item'];
            $produto       = $dados['produto'];
            $produtoId     = $item['id'];
            $variacaoId    = $item['variacao_id'] ?? 0;
            $quantidade    = (int) $item['quantidade'];
            $precoUnitario = $dados['preco_unitario'] ?? (float) $produto['preco'];

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
                'tamanho'        => !empty($item['tamanho']) ? $item['tamanho'] : null,
                'cor'            => !empty($item['cor']) ? $item['cor'] : null,
                'quantidade'     => $quantidade,
                'preco_unitario' => $precoUnitario,
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

        $pedidoCriado = [
            'id'          => $pedidoId,
            'valor_total' => $valorTotal,
        ];

        // Processa o pagamento após criação do pedido
        $resultadoPagamento = null;
        if ($formaPagamento === 'pix') {
            $resultadoPagamento = $this->pagamentoService->gerarPix($pedidoCriado);
        } elseif ($formaPagamento === 'cartao_credito') {
            $resultadoPagamento = $this->pagamentoService->processarCartao($pedidoCriado, $pagamentoData ?? []);
            if (!$resultadoPagamento['ok']) {
                // Em caso de falha de cartão, cancela o status do pedido
                $this->pedidoModel->atualizarStatusPagamento($pedidoId, 'falhou', 'cancelado');
                return [
                    'ok'        => false,
                    'pedido_id' => $pedidoId,
                    'erro'      => $resultadoPagamento['erro'],
                ];
            }
        }

        // Limpa sessões de compra
        session()->remove('carrinho');
        session()->remove('cupom');
        session()->remove('frete');

        // --- Disparos de e-mail ---
        // E-mail: Pedido criado
        $this->emailService->notificarPedidoCriado($pedidoId);

        // E-mail: Pagamento aprovado imediatamente (cartão aprovado na hora)
        if ($formaPagamento === 'cartao_credito' && isset($resultadoPagamento['status']) && $resultadoPagamento['status'] === 'pago') {
            $this->emailService->notificarPagamentoAprovado($pedidoId);
        }

        return [
            'ok'               => true,
            'pedido_id'        => $pedidoId,
            'forma_pagamento'  => $formaPagamento,
            'pagamento'        => $resultadoPagamento,
        ];
    }
}
