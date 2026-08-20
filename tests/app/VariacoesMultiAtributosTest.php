<?php

namespace Tests\App;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\ProdutoModel;
use App\Models\ProdutoVariacaoModel;
use App\Services\CarrinhoService;
use App\Services\PedidoService;

class VariacoesMultiAtributosTest extends CIUnitTestCase
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
                'nome'      => 'Smartphones & Tecnologia',
                'descricao' => 'Categoria de tecnologia avançada',
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
                'nome'       => 'Cliente Multi Atributos Teste',
                'email'      => 'cliente_multi@teste.com',
                'senha_hash' => password_hash('123456', PASSWORD_DEFAULT),
                'role'       => 'cliente',
                'ativo'      => 1,
                'criado_em'  => date('Y-m-d H:i:s'),
            ]);
            return (int) $db->insertID();
        }
        return (int) $usuario['id'];
    }

    public function testSalvarEBuscarVariacoesComMultiAtributosJsonESku(): void
    {
        $db = \Config\Database::connect('default');
        $categoriaId = $this->getCategoriaId($db);

        $produtoModel = new ProdutoModel();
        $variacaoModel = new ProdutoVariacaoModel();

        // 1. Inserir Produto
        $produtoId = $produtoModel->insert([
            'categoria_id' => $categoriaId,
            'nome'         => 'Smartphone Flagship 2026 Teste',
            'descricao'    => 'Smartphone com múltiplas configurações de memória e cor',
            'preco'        => 3999.00,
            'imagem'       => 'flagship-padrao.jpg',
            'estoque'      => 30,
            'ativo'        => 1,
            'frete_gratis' => 1,
        ]);

        $this->assertIsNumeric($produtoId);
        $this->assertGreaterThan(0, $produtoId);

        // 2. Inserir SKUs com Multi-Atributos
        $sku1Data = [
            'produto_id'     => $produtoId,
            'sku'            => 'PHONE-AZUL-256GB-12GB',
            'nome_variacao'  => 'Azul Titânio / 256GB / 12GB RAM',
            'atributos_json' => json_encode([
                'Cor'           => 'Azul Titânio',
                'Armazenamento' => '256GB',
                'Memória RAM'   => '12GB',
            ], JSON_UNESCAPED_UNICODE),
            'cor'            => 'Azul Titânio',
            'cor_hex'        => '#3b82f6',
            'preco'          => 4499.00,
            'imagem_url'     => 'https://exemplo.com/phone-azul.jpg',
            'codigo_barras'  => '789123456001',
            'estoque'        => 10,
        ];

        $skuUnicoPreto = 'PHONE-PRETO-512GB-' . uniqid();
        $sku2Data = [
            'produto_id'     => $produtoId,
            'sku'            => $skuUnicoPreto,
            'nome_variacao'  => 'Preto Titânio / 512GB / 16GB RAM',
            'atributos_json' => json_encode([
                'Cor'           => 'Preto Titânio',
                'Armazenamento' => '512GB',
                'Memória RAM'   => '16GB',
            ], JSON_UNESCAPED_UNICODE),
            'cor'            => 'Preto Titânio',
            'cor_hex'        => '#111827',
            'preco'          => 5299.00,
            'imagem_url'     => 'https://exemplo.com/phone-preto.jpg',
            'codigo_barras'  => '789123456002',
            'estoque'        => 20,
        ];

        $var1Id = $variacaoModel->insert($sku1Data);
        $var2Id = $variacaoModel->insert($sku2Data);

        $this->assertIsNumeric($var1Id);
        $this->assertIsNumeric($var2Id);

        // 3. Buscar Variações Formatadas
        $variacoes = $variacaoModel->getVariacoesFormatadas((int)$produtoId, 'flagship-padrao.jpg');
        $this->assertCount(2, $variacoes);

        $v1 = $variacoes[0];
        $this->assertEquals('PHONE-AZUL-256GB-12GB', $v1['sku']);
        $this->assertEquals('Azul Titânio / 256GB / 12GB RAM', $v1['nome_variacao']);
        $this->assertEquals(4499.00, $v1['preco']);
        $this->assertEquals('https://exemplo.com/phone-azul.jpg', $v1['imagem_url']);
        $this->assertIsArray($v1['atributos']);
        $this->assertEquals('256GB', $v1['atributos']['Armazenamento']);
        $this->assertEquals('12GB', $v1['atributos']['Memória RAM']);

        // 4. Buscar por SKU
        $encontradoPorSku = $variacaoModel->buscarPorSku($skuUnicoPreto);
        $this->assertNotNull($encontradoPorSku);
        $this->assertEquals($var2Id, $encontradoPorSku['id']);
        $this->assertEquals(5299.00, (float)$encontradoPorSku['preco']);
    }

    public function testExtrairAtributosDisponiveisNoModel(): void
    {
        $db = \Config\Database::connect('default');
        $categoriaId = $this->getCategoriaId($db);

        $produtoModel = new ProdutoModel();
        $variacaoModel = new ProdutoVariacaoModel();

        $produtoId = $produtoModel->insert([
            'categoria_id' => $categoriaId,
            'nome'         => 'Notebook Ultra Gamer Teste',
            'descricao'    => 'Notebook com opções de GPU e RAM',
            'preco'        => 7999.00,
            'imagem'       => 'notebook.jpg',
            'estoque'      => 15,
            'ativo'        => 1,
        ]);

        $variacaoModel->insert([
            'produto_id'     => $produtoId,
            'sku'            => 'NOTE-RTX4060-16GB',
            'nome_variacao'  => 'RTX 4060 / 16GB RAM',
            'atributos_json' => json_encode(['Placa de Vídeo' => 'RTX 4060', 'RAM' => '16GB']),
            'estoque'        => 5,
        ]);

        $variacaoModel->insert([
            'produto_id'     => $produtoId,
            'sku'            => 'NOTE-RTX4060-32GB',
            'nome_variacao'  => 'RTX 4060 / 32GB RAM',
            'atributos_json' => json_encode(['Placa de Vídeo' => 'RTX 4060', 'RAM' => '32GB']),
            'estoque'        => 5,
        ]);

        $variacaoModel->insert([
            'produto_id'     => $produtoId,
            'sku'            => 'NOTE-RTX4080-32GB',
            'nome_variacao'  => 'RTX 4080 / 32GB RAM',
            'atributos_json' => json_encode(['Placa de Vídeo' => 'RTX 4080', 'RAM' => '32GB']),
            'estoque'        => 5,
        ]);

        $atributosMap = $variacaoModel->getAtributosDisponiveis((int)$produtoId);

        $this->assertArrayHasKey('Placa de Vídeo', $atributosMap);
        $this->assertArrayHasKey('RAM', $atributosMap);
        $this->assertEquals(['RTX 4060', 'RTX 4080'], $atributosMap['Placa de Vídeo']);
        $this->assertEquals(['16GB', '32GB'], $atributosMap['RAM']);
    }

    public function testAdicionarVariacaoMultiAtributoAoCarrinho(): void
    {
        $db = \Config\Database::connect('default');
        $categoriaId = $this->getCategoriaId($db);

        $produtoModel = new ProdutoModel();
        $variacaoModel = new ProdutoVariacaoModel();
        $carrinhoService = new CarrinhoService();

        // Limpa carrinho
        $carrinhoService->limpar();

        $produtoId = $produtoModel->insert([
            'categoria_id' => $categoriaId,
            'nome'         => 'Smart TV OLED Teste',
            'descricao'    => 'Smart TV com variações de polegadas e voltagem',
            'preco'        => 2999.00,
            'imagem'       => 'tv-padrao.jpg',
            'estoque'      => 20,
            'ativo'        => 1,
        ]);

        $varId = $variacaoModel->insert([
            'produto_id'     => $produtoId,
            'sku'            => 'TV-65-110V',
            'nome_variacao'  => '65 Polegadas / 110V',
            'atributos_json' => json_encode(['Tamanho da Tela' => '65 Polegadas', 'Voltagem' => '110V']),
            'preco'          => 4299.00,
            'imagem_url'     => 'https://exemplo.com/tv-65.jpg',
            'estoque'        => 8,
        ]);

        $res = $carrinhoService->adicionar((int)$produtoId, 1, (int)$varId);
        $this->assertTrue($res['ok'], 'Deveria adicionar ao carrinho com sucesso.');

        $carrinho = $carrinhoService->getCarrinho();
        $cartKey = $produtoId . '_' . $varId;

        $this->assertArrayHasKey($cartKey, $carrinho);
        $item = $carrinho[$cartKey];

        $this->assertEquals('TV-65-110V', $item['sku']);
        $this->assertEquals('65 Polegadas / 110V', $item['nome_variacao']);
        $this->assertEquals(4299.00, $item['preco']);
        $this->assertEquals('https://exemplo.com/tv-65.jpg', $item['imagem']);
        $this->assertIsArray($item['atributos']);
        $this->assertEquals('65 Polegadas', $item['atributos']['Tamanho da Tela']);
        $this->assertEquals(4299.00, $carrinhoService->calcularTotal());
    }

    public function testCriarPedidoComVariacaoMultiAtributoEBaixaEstoque(): void
    {
        $db = \Config\Database::connect('default');
        $categoriaId = $this->getCategoriaId($db);
        $clienteId = $this->getClienteId($db);

        $produtoModel = new ProdutoModel();
        $variacaoModel = new ProdutoVariacaoModel();
        $carrinhoService = new CarrinhoService();
        $pedidoService = new PedidoService();

        $carrinhoService->limpar();

        $produtoId = $produtoModel->insert([
            'categoria_id' => $categoriaId,
            'nome'         => 'Fone Bluetooth Noise Cancelling Teste',
            'descricao'    => 'Fone de alta fidelidade com cores exclusivas',
            'preco'        => 899.00,
            'imagem'       => 'fone-padrao.jpg',
            'estoque'      => 10,
            'ativo'        => 1,
        ]);

        $varId = $variacaoModel->insert([
            'produto_id'     => $produtoId,
            'sku'            => 'FONE-PRATA-ANC',
            'nome_variacao'  => 'Prata / Edição Limitada',
            'atributos_json' => json_encode(['Cor' => 'Prata', 'Edição' => 'Limitada']),
            'cor'            => 'Prata',
            'preco'          => 999.00,
            'imagem_url'     => 'https://exemplo.com/fone-prata.jpg',
            'estoque'        => 5,
        ]);

        $carrinhoService->adicionar((int)$produtoId, 2, (int)$varId);

        $endereco = [
            'cep'        => '01310-100',
            'logradouro' => 'Avenida Paulista',
            'numero'     => '1000',
            'bairro'     => 'Bela Vista',
            'cidade'     => 'São Paulo',
            'uf'         => 'SP',
        ];

        $resultado = $pedidoService->criarPedido($carrinhoService->getCarrinho(), $clienteId, $endereco, null, null, ['forma_pagamento' => 'pix']);

        $this->assertTrue($resultado['ok'], 'Pedido deve ser criado com sucesso.');
        $this->assertGreaterThan(0, $resultado['pedido_id']);

        $pedido = (new \App\Models\PedidoModel())->find($resultado['pedido_id']);
        $this->assertNotNull($pedido);
        $this->assertEquals(1998.00, (float)$pedido['valor_total']); // 2 * 999

        // Validar baixa de estoque na variação específica
        $varAtualizada = $variacaoModel->find($varId);
        $this->assertEquals(3, (int)$varAtualizada['estoque'], 'Estoque da variação deve ter baixado de 5 para 3.');
    }

    public function testRetrocompatibilidadeComVariacoesLegadas(): void
    {
        $db = \Config\Database::connect('default');
        $categoriaId = $this->getCategoriaId($db);

        $produtoModel = new ProdutoModel();
        $variacaoModel = new ProdutoVariacaoModel();

        $produtoId = $produtoModel->insert([
            'categoria_id' => $categoriaId,
            'nome'         => 'Camisa Polo Básica Legada Teste',
            'descricao'    => 'Camisa com variação legada sem JSON',
            'preco'        => 79.90,
            'imagem'       => 'camisa.jpg',
            'estoque'      => 10,
            'ativo'        => 1,
        ]);

        // Inserir registro apenas com tamanho e cor (modelo antigo)
        $db->table('produto_variacoes')->insert([
            'produto_id' => $produtoId,
            'tamanho'    => 'GG',
            'cor'        => 'Azul Royal',
            'cor_hex'    => '#1e40af',
            'preco'      => 89.90,
            'estoque'    => 7,
        ]);
        $varId = $db->insertID();

        $variacoes = $variacaoModel->getVariacoesFormatadas((int)$produtoId, 'camisa.jpg');
        $this->assertCount(1, $variacoes);

        $v = $variacoes[0];
        $this->assertEquals('Azul Royal / GG', $v['nome_variacao']);
        $this->assertEquals('camisa.jpg', $v['imagem_url']);
        $this->assertEquals(89.90, $v['preco']);
        $this->assertArrayHasKey('Cor', $v['atributos']);
        $this->assertArrayHasKey('Tamanho / Opção', $v['atributos']);
        $this->assertEquals('Azul Royal', $v['atributos']['Cor']);
        $this->assertEquals('GG', $v['atributos']['Tamanho / Opção']);
    }
}
