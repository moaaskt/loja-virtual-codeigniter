<?php

namespace App\Controllers;

// Importaremos os models necessários aqui
use App\Models\PedidoModel;
use App\Models\PedidoProdutoModel;

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

        // Instancia os models
        $pedidoModel = new PedidoModel();
        $pedidoProdutoModel = new PedidoProdutoModel();

        // Conexão com o banco para usar a transação
        $db = \Config\Database::connect();

        // 2. Inicia a transação
        $db->transStart();

        // 3. Calcula o valor total e prepara os dados dos produtos
        $valorTotal = 0;
        $produtosDoPedido = [];
        foreach ($carrinho as $id => $item) {
            $valorTotal += $item['preco'] * $item['quantidade'];
            $produtosDoPedido[] = [
                // 'pedido_id' será adicionado depois que o pedido for criado
                'produto_id' => $id,
                'quantidade' => $item['quantidade'],
                'preco_unitario' => $item['preco'],
            ];
        }

        // 4. Insere o pedido na tabela `pedidos`
        $dadosPedido = [
            'usuario_id' => session()->get('usuario_id'),
            'valor_total' => $valorTotal,
            'status' => 'Processando' // Um status inicial
        ];
        $pedidoModel->insert($dadosPedido);

        // 5. Pega o ID do pedido que acabamos de criar
        $pedidoId = $pedidoModel->getInsertID();

        // 6. Insere cada produto na tabela `pedido_produtos`
        foreach ($produtosDoPedido as $produto) {
            $produto['pedido_id'] = $pedidoId; // Adiciona o ID do pedido a cada item
            $pedidoProdutoModel->insert($produto);
        }

        // 7. Finaliza a transação
        $db->transComplete();

        // 8. Verifica se a transação foi bem-sucedida
        if ($db->transStatus() === false) {
            // Se falhou, redireciona de volta com erro (nada foi salvo no banco)
            return redirect()->to(site_url('carrinho'))->with('error', 'Houve um erro ao processar seu pedido. Tente novamente.');
        } else {
            // Se deu certo, limpa o carrinho da sessão
            session()->remove('carrinho');

            // Redireciona para uma página de sucesso
            return redirect()->to(site_url('pedido/sucesso'))->with('success', 'Seu pedido foi realizado com sucesso!');
        }
    }
}