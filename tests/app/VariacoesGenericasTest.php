<?php

namespace Tests\App;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\ProdutoModel;
use App\Models\CategoriaModel;
use App\Services\CarrinhoService;
use App\Services\PedidoService;

class VariacoesGenericasTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    private function getCategoriaId($db): int
    {
        $categoria = $db->table('categorias')->get()->getRowArray();
        if (!$categoria) {
            $db->table('categorias')->insert([
                'nome'      => 'Eletrônicos & Smartphones',
                'descricao' => 'Categoria de tecnologia',
                'ativo'     => 1,
            ]);
            return (int) $db->insertID();
        }
        return (int) $categoria['id'];
    }

    private function getClienteId($db): int
    {
        $usuario = $db->table('usuarios')->where('role', 'cliente')->get()->getRowArray();
        if (!$usuario) {
            $db->table('usuarios')->insert([
                'nome'       => 'Cliente Variação Teste',
                'email'      => 'cliente_var@teste.com',
                'senha_hash' => password_hash('123456', PASSWORD_DEFAULT),
                'role'       => 'cliente',
                'ativo'      => 1,
                'criado_em'  => date('Y-m-d H:i:s'),
            ]);
            return (int) $db->insertID();
        }
        return (int) $usuario['id'];
    }

    public function testCadastroEEdicaoDeProdutoComVariacoesFlexiveisEPrecoIndividual(): void
    {
        $db = \Config\Database::connect('default');
        $categoriaId = $this->getCategoriaId($db);

        $produtoModel = new ProdutoModel();

        // 1. Inserir Produto Base
        $produtoId = $produtoModel->insert([
            'categoria_id' => $categoriaId,
            'nome'         => 'Smartphone Galaxy Ultra Teste',
            'descricao'    => 'Aparelho topo de linha com múltiplas capacidades',
            'preco'        => 3000.00,
            'estoque'      => 17,
            'imagem'       => 'galaxy_ultra.jpg',
            'frete_gratis' => 1,
        ]);
        $this->assertIsInt($produtoId);
        $this->assertGreaterThan(0, $produtoId);

        // 2. Inserir Variações Genéricas (Capacidade, Cor opcional, Preço individual)
        $db->table('produto_variacoes')->insertBatch([
            [
                'produto_id' => $produtoId,
                'tamanho'    => '128GB',
                'cor'        => 'Preto',
                'preco'      => 3000.00,
                'estoque'    => 10,
            ],
            [
                'produto_id' => $produtoId,
                'tamanho'    => '256GB',
                'cor'        => 'Titânio',
                'preco'      => 3500.00,
                'estoque'    => 5,
            ],
            [
                'produto_id' => $produtoId,
                'tamanho'    => '512GB',
                'cor'        => null, // Cor opcional
                'preco'      => 4200.00,
                'estoque'    => 2,
            ],
        ]);

        $variacoes = $produtoModel->getVariacoes($produtoId);
        $this->assertCount(3, $variacoes);
        $this->assertEquals('128GB', $variacoes[0]['tamanho']);
        $this->assertEquals('Preto', $variacoes[0]['cor']);
        $this->assertEquals('3000.00', $variacoes[0]['preco']);

        $this->assertEquals('256GB', $variacoes[1]['tamanho']);
        $this->assertEquals('Titânio', $variacoes[1]['cor']);
        $this->assertEquals('3500.00', $variacoes[1]['preco']);

        $this->assertEquals('512GB', $variacoes[2]['tamanho']);
        $this->assertNull($variacoes[2]['cor']);
        $this->assertEquals('4200.00', $variacoes[2]['preco']);
    }

    public function testCarrinhoCalculaPrecoCustomizadoDaVariacao(): void
    {
        $db = \Config\Database::connect('default');
        $categoriaId = $this->getCategoriaId($db);

        // Cria produto de teste com variação de preço
        $produtoId = (int) $db->table('produtos')->insert([
            'categoria_id' => $categoriaId,
            'nome'         => 'Notebook Gamer X',
            'descricao'    => 'Notebook potente',
            'preco'        => 5000.00, // Preço base
            'estoque'      => 10,
            'imagem'       => 'notebook.jpg',
            'frete_gratis' => 1,
        ]);
        $produtoId = (int) $db->insertID();

        $var1Id = (int) $db->table('produto_variacoes')->insert([
            'produto_id' => $produtoId,
            'tamanho'    => '16GB RAM + 512GB SSD',
            'cor'        => 'Cinza Espacial',
            'preco'      => 5000.00,
            'estoque'    => 5,
        ]);
        $var1Id = (int) $db->insertID();

        $var2Id = (int) $db->table('produto_variacoes')->insert([
            'produto_id' => $produtoId,
            'tamanho'    => '32GB RAM + 1TB SSD',
            'cor'        => 'Cinza Espacial',
            'preco'      => 6500.00, // Preço customizado superior
            'estoque'    => 5,
        ]);
        $var2Id = (int) $db->insertID();

        $carrinhoService = new CarrinhoService();
        $carrinhoService->limpar();

        // Adiciona variação com preço customizado (R$ 6.500)
        $res = $carrinhoService->adicionar($produtoId, 2, $var2Id);
        $this->assertTrue($res['ok']);

        $carrinho = $carrinhoService->getCarrinho();
        $cartKey = $produtoId . '_' . $var2Id;
        $this->assertArrayHasKey($cartKey, $carrinho);
        $this->assertEquals(6500.00, $carrinho[$cartKey]['preco']);
        $this->assertEquals('32GB RAM + 1TB SSD', $carrinho[$cartKey]['tamanho']);
        $this->assertEquals('Cinza Espacial', $carrinho[$cartKey]['cor']);

        // Subtotal deve ser 2 * 6500 = 13000
        $this->assertEquals(13000.00, $carrinhoService->calcularSubtotal());
    }

    public function testCriacaoDePedidoComPrecoDiferenciadoEBaixaEstoqueVariacao(): void
    {
        $db = \Config\Database::connect('default');
        $categoriaId = $this->getCategoriaId($db);
        $clienteId   = $this->getClienteId($db);

        $produtoId = (int) $db->table('produtos')->insert([
            'categoria_id' => $categoriaId,
            'nome'         => 'Monitor 4K Teste',
            'descricao'    => 'Monitor para trabalho e jogos',
            'preco'        => 2000.00,
            'estoque'      => 10,
            'imagem'       => 'monitor.jpg',
            'frete_gratis' => 1,
        ]);
        $produtoId = (int) $db->insertID();

        $varId = (int) $db->table('produto_variacoes')->insert([
            'produto_id' => $produtoId,
            'tamanho'    => '32 polegadas 144Hz',
            'cor'        => 'Preto Fosco',
            'preco'      => 2800.00,
            'estoque'    => 8,
        ]);
        $varId = (int) $db->insertID();

        $carrinhoService = new CarrinhoService();
        $carrinhoService->limpar();
        $carrinhoService->adicionar($produtoId, 3, $varId);

        $pedidoService = new PedidoService();
        $endereco = [
            'cep'        => '01001-000',
            'logradouro' => 'Av. Paulista',
            'numero'     => '1000',
            'bairro'     => 'Bela Vista',
            'cidade'     => 'São Paulo',
            'uf'         => 'SP'
        ];

        $resPedido = $pedidoService->criarPedido($carrinhoService->getCarrinho(), $clienteId, $endereco);
        $this->assertTrue($resPedido['ok']);
        $this->assertArrayHasKey('pedido_id', $resPedido);

        // Validação no banco: valor total = 3 * 2800 = 8400
        $pedido = $db->table('pedidos')->where('id', $resPedido['pedido_id'])->get()->getRowArray();
        $this->assertEquals(8400.00, (float)$pedido['valor_total']);

        $itemPedido = $db->table('pedido_produtos')->where('pedido_id', $resPedido['pedido_id'])->get()->getRowArray();
        $this->assertEquals($varId, (int)$itemPedido['variacao_id']);
        $this->assertEquals('32 polegadas 144Hz', $itemPedido['tamanho']);
        $this->assertEquals('Preto Fosco', $itemPedido['cor']);
        $this->assertEquals(2800.00, (float)$itemPedido['preco_unitario']);
        $this->assertEquals(3, (int)$itemPedido['quantidade']);

        // Validação de estoque da variação (8 - 3 = 5) e do produto (10 - 3 = 7)
        $varAtualizada = $db->table('produto_variacoes')->where('id', $varId)->get()->getRowArray();
        $this->assertEquals(5, (int)$varAtualizada['estoque']);

        $prodAtualizado = $db->table('produtos')->where('id', $produtoId)->get()->getRowArray();
        $this->assertEquals(7, (int)$prodAtualizado['estoque']);
    }
}
