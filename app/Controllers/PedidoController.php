<?php

namespace App\Controllers;

// Importaremos os models necessários aqui
use App\Models\PedidoModel;
use App\Models\PedidoProdutoModel;
use App\Models\ProdutoModel;

class PedidoController extends BaseController
{
    public function __construct()
    {
        helper(['form', 'url']);
    }



    

    // --- Função para exibir a página de sucesso do pedido ---
    public function sucesso()
    {
        // Apenas exibe uma view de confirmação
        return view('shop/pedido_sucesso', ['title' => 'Pedido Realizado!']);
    }


    // --- Função para finalizar o pedido ---
    public function finalizar()
    {
        // 1. Verificações de segurança
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('/'))->with('error', 'Você precisa estar logado para finalizar a compra.');
        }

        $carrinho = session()->get('carrinho') ?? [];
        if (empty($carrinho)) {
            return redirect()->to(site_url('carrinho'))->with('error', 'Seu carrinho está vazio.');
        }

        $pedidoModel      = new PedidoModel();
        $pedidoProdutoModel = new PedidoProdutoModel();
        $produtoModel     = new ProdutoModel();

        $db = \Config\Database::connect();
        $db->transStart();

        // Busca preços atuais do banco para todos os itens antes de calcular o total
        $itensPedido = [];
        $valorTotal  = 0;
        foreach ($carrinho as $produtoId => $item) {
            $produto = $produtoModel->find((int) $produtoId);
            if (!$produto) {
                $db->transRollback();
                return redirect()->to(site_url('carrinho'))->with('error', 'O produto "' . esc($item['nome']) . '" não está mais disponível.');
            }
            $valorTotal += $produto['preco'] * $item['quantidade'];
            $itensPedido[$produtoId] = [
                'item'    => $item,
                'produto' => $produto,
            ];
        }

        $pedidoModel->insert([
            'usuario_id'  => session()->get('usuario_id'),
            'valor_total' => $valorTotal,
            'status'      => 'pendente',
        ]);
        $pedidoId = $pedidoModel->getInsertID();

        foreach ($itensPedido as $produtoId => $dados) {
            $item    = $dados['item'];
            $produto = $dados['produto'];

            // Decrementa estoque com SELECT FOR UPDATE para evitar race condition
            $ok = $produtoModel->decrementarEstoque((int) $produtoId, (int) $item['quantidade'], $db);
            if (!$ok) {
                $db->transRollback();
                return redirect()->to(site_url('carrinho'))->with('error', 'Estoque insuficiente para "' . esc($item['nome']) . '". Verifique seu carrinho.');
            }

            $pedidoProdutoModel->insert([
                'pedido_id'      => $pedidoId,
                'produto_id'     => $produtoId,
                'quantidade'     => $item['quantidade'],
                'preco_unitario' => $produto['preco'],
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to(site_url('carrinho'))->with('error', 'Houve um erro ao processar seu pedido. Tente novamente.');
        }

        session()->remove('carrinho');
        return redirect()->to(site_url('pedido/sucesso'))->with('success', 'Seu pedido foi realizado com sucesso!');
    }
}