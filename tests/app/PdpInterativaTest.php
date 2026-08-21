<?php

namespace Tests\App;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\ProdutoModel;
use App\Models\ProdutoVariacaoModel;
use App\Services\CarrinhoService;
use App\Controllers\HomeController;

class PdpInterativaTest extends CIUnitTestCase
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
                'nome'      => 'Smartphones & Gadgets',
                'descricao' => 'Categoria de tecnologia avançada',
                'ativo'     => 1,
            ]);
            return (int) $db->insertID();
        }
        return (int) $categoria['id'];
    }

    public function testPdpCarregaProdutoComVariacoesMultiAtributos(): void
    {
        $db = \Config\Database::connect('default');
        $categoriaId = $this->getCategoriaId($db);

        $produtoModel = new ProdutoModel();
        $variacaoModel = new ProdutoVariacaoModel();

        // 1. Cadastrar Produto
        $produtoId = $produtoModel->insert([
            'categoria_id' => $categoriaId,
            'nome'         => 'Smartphone Pro Max Interativo Teste',
            'descricao'    => 'Experiência imersiva na PDP com troca de fotos e atributos',
            'preco'        => 4999.00,
            'imagem'       => 'pro-max-base.jpg',
            'estoque'      => 50,
            'ativo'        => 1,
            'frete_gratis' => 1,
        ]);

        $this->assertIsNumeric($produtoId);

        // 2. Cadastrar SKUs com Variações
        $sku1 = 'PROMAX-AZUL-256GB-' . uniqid();
        $sku2 = 'PROMAX-PRETO-512GB-' . uniqid();

        $var1Id = $variacaoModel->insert([
            'produto_id'     => $produtoId,
            'sku'            => $sku1,
            'nome_variacao'  => 'Azul Titânio / 256GB / 12GB RAM',
            'atributos_json' => json_encode([
                'Cor'           => 'Azul Titânio',
                'Armazenamento' => '256GB',
                'Memória RAM'   => '12GB',
            ], JSON_UNESCAPED_UNICODE),
            'cor'            => 'Azul Titânio',
            'cor_hex'        => '#2e3a4e',
            'preco'          => 5499.00,
            'imagem_url'     => 'https://exemplo.com/azul.jpg',
            'estoque'        => 15,
        ]);

        $var2Id = $variacaoModel->insert([
            'produto_id'     => $produtoId,
            'sku'            => $sku2,
            'nome_variacao'  => 'Preto Titânio / 512GB / 16GB RAM',
            'atributos_json' => json_encode([
                'Cor'           => 'Preto Titânio',
                'Armazenamento' => '512GB',
                'Memória RAM'   => '16GB',
            ], JSON_UNESCAPED_UNICODE),
            'cor'            => 'Preto Titânio',
            'cor_hex'        => '#111827',
            'preco'          => 6499.00,
            'imagem_url'     => 'https://exemplo.com/preto.jpg',
            'estoque'        => 20,
        ]);

        // 3. Executar o método do controller para carregar a PDP
        $controller = new HomeController();
        $controller->initController(
            \Config\Services::request(),
            \Config\Services::response(),
            \Config\Services::logger()
        );

        $html = $controller->produto((int)$produtoId);
        $this->assertIsString($html);

        // 4. Asserções no HTML da PDP
        $this->assertStringContainsString('Smartphone Pro Max Interativo Teste', $html);
        $this->assertStringContainsString('pdp-sku-badge', $html);
        $this->assertStringContainsString('pdp-price', $html);
        $this->assertStringContainsString('pdp-main-img', $html);
        $this->assertStringContainsString('attr-group', $html);
        $this->assertStringContainsString('Azul Titânio', $html);
        $this->assertStringContainsString('256GB', $html);
        $this->assertStringContainsString('512GB', $html);
        $this->assertStringContainsString('https://exemplo.com/azul.jpg', $html);
    }

    public function testFluxoCompraDiretaPdpParaCarrinhoComSku(): void
    {
        $db = \Config\Database::connect('default');
        $categoriaId = $this->getCategoriaId($db);

        $produtoModel = new ProdutoModel();
        $variacaoModel = new ProdutoVariacaoModel();
        $carrinhoService = new CarrinhoService();
        $carrinhoService->limpar();

        $produtoId = $produtoModel->insert([
            'categoria_id' => $categoriaId,
            'nome'         => 'Notebook Ultra Gamer PDP Teste',
            'descricao'    => 'Notebook potente para testes de seleção de SKU',
            'preco'        => 7500.00,
            'imagem'       => 'notebook-base.jpg',
            'estoque'      => 10,
            'ativo'        => 1,
        ]);

        $skuNotebook = 'NOTE-RTX4070-32GB-' . uniqid();
        $varId = $variacaoModel->insert([
            'produto_id'     => $produtoId,
            'sku'            => $skuNotebook,
            'nome_variacao'  => 'RTX 4070 / 32GB RAM',
            'atributos_json' => json_encode(['GPU' => 'RTX 4070', 'RAM' => '32GB']),
            'preco'          => 8900.00,
            'imagem_url'     => 'https://exemplo.com/notebook-rtx.jpg',
            'estoque'        => 5,
        ]);

        // Simula adição ao carrinho via ID da variação selecionada na PDP
        $resultado = $carrinhoService->adicionar((int)$produtoId, 1, (int)$varId);
        $this->assertTrue($resultado['ok']);

        $carrinho = $carrinhoService->getCarrinho();
        $item = $carrinho[$produtoId . '_' . $varId];

        $this->assertEquals($skuNotebook, $item['sku']);
        $this->assertEquals('RTX 4070 / 32GB RAM', $item['nome_variacao']);
        $this->assertEquals(8900.00, $item['preco']);
        $this->assertEquals('https://exemplo.com/notebook-rtx.jpg', $item['imagem']);
        $this->assertEquals(8900.00, $carrinhoService->calcularTotal());
    }

    public function testRetrocompatibilidadePdpProdutoSemVariacao(): void
    {
        $db = \Config\Database::connect('default');
        $categoriaId = $this->getCategoriaId($db);

        $produtoModel = new ProdutoModel();
        $carrinhoService = new CarrinhoService();
        $carrinhoService->limpar();

        $produtoId = $produtoModel->insert([
            'categoria_id' => $categoriaId,
            'nome'         => 'Caneca Térmica Simples Sem Variação',
            'descricao'    => 'Caneca sem opções de cores ou tamanhos',
            'preco'        => 120.00,
            'imagem'       => 'caneca.jpg',
            'estoque'      => 40,
            'ativo'        => 1,
            'frete_gratis' => 0,
        ]);

        $controller = new HomeController();
        $controller->initController(
            \Config\Services::request(),
            \Config\Services::response(),
            \Config\Services::logger()
        );

        $html = $controller->produto((int)$produtoId);
        $this->assertIsString($html);
        $this->assertStringContainsString('Caneca Térmica Simples Sem Variação', $html);
        $this->assertStringContainsString('120,00', $html);
        $this->assertStringContainsString('btn-add-cart', $html);

        // Adicionar diretamente sem variação
        $res = $carrinhoService->adicionar((int)$produtoId, 2, null);
        $this->assertTrue($res['ok']);

        $this->assertEquals(240.00, $carrinhoService->calcularTotal());
    }
}
