<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\CarrinhoService;
use App\Services\PedidoService;

class AuditFlow extends BaseCommand
{
    protected $group       = 'Audit';
    protected $name        = 'audit:flow';
    protected $description = 'Executa a auditoria completa de carrinho, checkout e persistência de dados.';

    public function run(array $params)
    {
        $db = \Config\Database::connect('default');

        CLI::write("=== AUDITORIA DO FLUXO: CARRINHO, CHECKOUT E PERSISTÊNCIA ===", 'yellow');

        // 1. Obter produto
        $produto = $db->table('produtos')->where('estoque >', 0)->get()->getRowArray();
        if (!$produto) {
            CLI::error("ERRO: Nenhum produto encontrado no banco!");
            return;
        }
        CLI::write("[1] Produto selecionado para teste: {$produto['nome']} (ID: {$produto['id']}, Estoque: {$produto['estoque']}, Preço: R$ {$produto['preco']})", 'green');

        // 2. Testar Carrinho
        $carrinhoService = new CarrinhoService();
        $carrinhoService->limpar();

        // Adicionar produto
        $resAdd = $carrinhoService->adicionar((int)$produto['id'], 2);
        if ($resAdd['ok']) {
            CLI::write("[2] Adição ao carrinho (2 unidades): SUCESSO", 'green');
        } else {
            CLI::error("ERRO ao adicionar: {$resAdd['erro']}");
            return;
        }

        // Testar validação de estoque excedente
        $resExcesso = $carrinhoService->adicionar((int)$produto['id'], 99999);
        if (!$resExcesso['ok']) {
            CLI::write("[3] Bloqueio de adição de estoque excedente: SUCESSO (Mensagem: '{$resExcesso['erro']}')", 'green');
        } else {
            CLI::error("ERRO: Sistema permitiu adicionar mais itens do que o estoque total!");
            return;
        }

        // 3. Testar Checkout / Pedido
        $usuario = $db->table('usuarios')->where('role', 'cliente')->get()->getRowArray();
        CLI::write("[4] Cliente comprador: {$usuario['nome']} (ID: {$usuario['id']})", 'green');

        $endereco = [
            'cep' => '01001-000',
            'logradouro' => 'Praça da Sé',
            'numero' => '100',
            'complemento' => 'Apto 1',
            'bairro' => 'Sé',
            'cidade' => 'São Paulo',
            'uf' => 'SP'
        ];

        $estoqueAntes = (int)$produto['estoque'];
        $cart = $carrinhoService->getCarrinho();

        $pedidoService = new PedidoService();
        $resPedido = $pedidoService->criarPedido($cart, (int)$usuario['id'], $endereco);

        if (!$resPedido['ok']) {
            CLI::error("ERRO ao criar pedido: {$resPedido['erro']}");
            return;
        }
        $pedidoId = $resPedido['pedido_id'];
        CLI::write("[5] Pedido criado com sucesso! (ID do Pedido: {$pedidoId})", 'green');

        // 4. Validação no Banco de Dados
        $pedidoDb = $db->table('pedidos')->where('id', $pedidoId)->get()->getRowArray();
        CLI::write("[6] Registro do Pedido na tabela 'pedidos':", 'cyan');
        CLI::write("    - Status: {$pedidoDb['status']}");
        CLI::write("    - Valor Total: R$ {$pedidoDb['valor_total']}");
        CLI::write("    - Endereço: {$pedidoDb['logradouro']}, {$pedidoDb['numero']} - {$pedidoDb['cidade']}/{$pedidoDb['uf']} (CEP: {$pedidoDb['cep']})");

        $itensDb = $db->table('pedido_produtos')->where('pedido_id', $pedidoId)->get()->getResultArray();
        CLI::write("[7] Itens do Pedido na tabela 'pedido_produtos': " . count($itensDb) . " item(ns)", 'cyan');
        foreach ($itensDb as $item) {
            CLI::write("    - Produto ID: {$item['produto_id']} | Quantidade: {$item['quantidade']} | Preço Unitário: R$ {$item['preco_unitario']}");
        }

        // 5. Validação de Baixa de Estoque
        $produtoDepois = $db->table('produtos')->where('id', $produto['id'])->get()->getRowArray();
        CLI::write("[8] Validação de Estoque:", 'cyan');
        CLI::write("    - Estoque antes: {$estoqueAntes}");
        CLI::write("    - Estoque atual: {$produtoDepois['estoque']}");

        if ((int)$produtoDepois['estoque'] === $estoqueAntes - 2) {
            CLI::write("\n>>> RESULTADO FINAL: TODOS OS TESTES PASSARAM COM 100% DE SUCESSO! <<<", 'light_green');
        } else {
            CLI::error("\n>>> ERRO: A baixa de estoque não bateu! <<<");
        }
    }
}
