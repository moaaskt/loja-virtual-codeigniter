<?php

namespace App\Services;

use App\Models\PedidoModel;
use App\Models\PedidoProdutoModel;
use App\Models\ProdutoModel;

class PedidoService
{
    protected PedidoModel $pedidoModel;
    protected PedidoProdutoModel $pedidoProdutoModel;
    protected ProdutoModel $produtoModel;

    public function __construct()
    {
        $this->pedidoModel       = new PedidoModel();
        $this->pedidoProdutoModel = new PedidoProdutoModel();
        $this->produtoModel      = new ProdutoModel();
    }

    /**
     * Cria um pedido completo dentro de uma transaction.
     * Retorna ['ok' => true, 'pedido_id' => int] ou ['ok' => false, 'erro' => string].
     */
    public function criarPedido(array $carrinho, int $clienteId, array $enderecoData = []): array
    {
        $camposObrigatorios = ['cep', 'logradouro', 'numero', 'bairro', 'cidade', 'uf'];
        foreach ($camposObrigatorios as $campo) {
            if (empty($enderecoData[$campo])) {
                return ['ok' => false, 'erro' => 'Por favor, preencha todos os campos obrigatórios de endereço.'];
            }
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $itensPedido = [];
        $valorTotal  = 0;

        foreach ($carrinho as $cartKey => $item) {
            $produtoId = $item['id'];
            $produto = $this->produtoModel->find((int) $produtoId);

            if (!$produto) {
                $db->transRollback();
                return ['ok' => false, 'erro' => 'O produto "' . esc($item['nome']) . '" não está mais disponível.'];
            }

            $valorTotal += $produto['preco'] * $item['quantidade'];
            $itensPedido[$cartKey] = ['item' => $item, 'produto' => $produto];
        }

        $this->pedidoModel->insert([
            'usuario_id'  => $clienteId,
            'valor_total' => $valorTotal,
            'status'      => 'pendente',
            'cep'         => $enderecoData['cep'],
            'logradouro'  => $enderecoData['logradouro'],
            'numero'      => $enderecoData['numero'],
            'complemento' => $enderecoData['complemento'] ?? null,
            'bairro'      => $enderecoData['bairro'],
            'cidade'      => $enderecoData['cidade'],
            'uf'          => $enderecoData['uf'],
        ]);
        $pedidoId = $this->pedidoModel->getInsertID();

        foreach ($itensPedido as $cartKey => $dados) {
            $item       = $dados['item'];
            $produto    = $dados['produto'];
            $produtoId  = $item['id'];
            $variacaoId = $item['variacao_id'] ?? 0;
            $quantidade = (int) $item['quantidade'];

            // Baixa de estoque cirúrgica
            if ($variacaoId > 0) {
                // Valida e diminui do estoque da variação específica
                $variacao = $db->table('produto_variacoes')->where('id', $variacaoId)->where('produto_id', $produtoId)->get()->getRowArray();
                if (!$variacao || $variacao['estoque'] < $quantidade) {
                    $db->transRollback();
                    return ['ok' => false, 'erro' => 'Estoque insuficiente para a variação de "' . esc($item['nome']) . '".'];
                }
                
                $db->table('produto_variacoes')
                   ->where('id', $variacaoId)
                   ->set('estoque', 'estoque - ' . $quantidade, false)
                   ->update();
                   
                // Diminuir do produto total também
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

        $db->transComplete();

        if ($db->transStatus() === false) {
            return ['ok' => false, 'erro' => 'Houve um erro ao processar seu pedido. Tente novamente.'];
        }

        return ['ok' => true, 'pedido_id' => $pedidoId];
    }
}
