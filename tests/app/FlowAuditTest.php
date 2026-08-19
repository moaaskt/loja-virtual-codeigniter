<?php

namespace Tests\App;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\CarrinhoService;
use App\Services\PedidoService;

class FlowAuditTest extends CIUnitTestCase
{
    public function testCarrinhoECheckoutComMySQL(): void
    {
        // Conecta diretamente ao banco padrão MySQL do Docker
        $db = \Config\Database::connect('default');
        
        // Pega um produto com estoque
        $produto = $db->table('produtos')->where('estoque >', 0)->get()->getRowArray();
        $this->assertNotEmpty($produto, 'Deve haver produtos semeados com estoque');

        $variacao = $db->table('produto_variacoes')->where('produto_id', $produto['id'])->where('estoque >', 0)->get()->getRowArray();
        $variacaoId = $variacao ? (int)$variacao['id'] : 0;

        $carrinhoService = new CarrinhoService();
        $carrinhoService->limpar();

        // 1. Teste de adição ao carrinho
        $resAdd = $carrinhoService->adicionar((int)$produto['id'], 2, $variacaoId);
        $this->assertTrue($resAdd['ok'], 'Deve adicionar produto ao carrinho: ' . ($resAdd['erro'] ?? ''));

        $cart = $carrinhoService->getCarrinho();
        $this->assertCount(1, $cart);

        // 2. Teste de tentativa de adicionar mais do que o estoque total
        $resExcesso = $carrinhoService->adicionar((int)$produto['id'], 99999, $variacaoId);
        $this->assertFalse($resExcesso['ok'], 'Não deve permitir adicionar quantidade superior ao estoque');

        // 3. Teste de checkout (PedidoService)
        $pedidoService = new PedidoService();
        $usuario = $db->table('usuarios')->where('role', 'cliente')->get()->getRowArray();
        $this->assertNotEmpty($usuario);

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

        $resPedido = $pedidoService->criarPedido($cart, (int)$usuario['id'], $endereco);
        $this->assertTrue($resPedido['ok'], 'Deve criar pedido com sucesso');
        $this->assertArrayHasKey('pedido_id', $resPedido);

        // 4. Validação no Banco de Dados
        $pedidoCriado = $db->table('pedidos')->where('id', $resPedido['pedido_id'])->get()->getRowArray();
        $this->assertNotEmpty($pedidoCriado);
        $this->assertEquals('pendente', $pedidoCriado['status']);
        $this->assertEquals($usuario['id'], $pedidoCriado['usuario_id']);

        $itensPedido = $db->table('pedido_produtos')->where('pedido_id', $resPedido['pedido_id'])->get()->getResultArray();
        $this->assertCount(1, $itensPedido);
        $this->assertEquals(2, $itensPedido[0]['quantidade']);

        // 5. Validação de Baixa de Estoque
        $produtoDepois = $db->table('produtos')->where('id', $produto['id'])->get()->getRowArray();
        $this->assertEquals($estoqueAntes - 2, (int)$produtoDepois['estoque'], 'O estoque deve ter sofrido baixa cirúrgica de 2 unidades');
    }
}
