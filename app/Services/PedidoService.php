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
    public function criarPedido(array $carrinho, int $clienteId): array
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $itensPedido = [];
        $valorTotal  = 0;

        foreach ($carrinho as $produtoId => $item) {
            $produto = $this->produtoModel->find((int) $produtoId);

            if (!$produto) {
                $db->transRollback();
                return ['ok' => false, 'erro' => 'O produto "' . esc($item['nome']) . '" não está mais disponível.'];
            }

            $valorTotal += $produto['preco'] * $item['quantidade'];
            $itensPedido[$produtoId] = ['item' => $item, 'produto' => $produto];
        }

        $this->pedidoModel->insert([
            'usuario_id'  => $clienteId,
            'valor_total' => $valorTotal,
            'status'      => 'pendente',
        ]);
        $pedidoId = $this->pedidoModel->getInsertID();

        foreach ($itensPedido as $produtoId => $dados) {
            $item    = $dados['item'];
            $produto = $dados['produto'];

            $ok = $this->produtoModel->decrementarEstoque((int) $produtoId, (int) $item['quantidade'], $db);

            if (!$ok) {
                $db->transRollback();
                return ['ok' => false, 'erro' => 'Estoque insuficiente para "' . esc($item['nome']) . '". Verifique seu carrinho.'];
            }

            $this->pedidoProdutoModel->insert([
                'pedido_id'      => $pedidoId,
                'produto_id'     => $produtoId,
                'quantidade'     => $item['quantidade'],
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
