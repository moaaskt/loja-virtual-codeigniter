<?php

namespace Tests\App;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\FreteService;
use App\Services\CarrinhoService;
use App\Services\PedidoService;
use App\Models\CupomModel;
use App\Models\ProdutoModel;
use App\Models\PedidoModel;

class FreteCuponsTest extends CIUnitTestCase
{
    protected FreteService $freteService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->freteService = new FreteService();
    }

    private function garantirCuponsTeste(): void
    {
        $db = \Config\Database::connect('default');
        $cupons = [
            [
                'codigo'              => 'PRIMEIRACOMPRA',
                'tipo'                => 'porcentagem',
                'valor'               => 10.00,
                'valor_minimo_pedido' => 0.00,
                'limite_uso'          => 100,
                'vezes_usado'         => 0,
                'data_validade'       => date('Y-m-d', strtotime('+60 days')),
                'ativo'               => 1,
                'criado_em'           => date('Y-m-d H:i:s'),
                'atualizado_em'       => date('Y-m-d H:i:s'),
            ],
            [
                'codigo'              => 'OFF20',
                'tipo'                => 'fixo',
                'valor'               => 20.00,
                'valor_minimo_pedido' => 100.00,
                'limite_uso'          => 50,
                'vezes_usado'         => 0,
                'data_validade'       => date('Y-m-d', strtotime('+30 days')),
                'ativo'               => 1,
                'criado_em'           => date('Y-m-d H:i:s'),
                'atualizado_em'       => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($cupons as $cupom) {
            $existe = $db->table('cupons')->where('codigo', $cupom['codigo'])->countAllResults();
            if ($existe === 0) {
                $db->table('cupons')->insert($cupom);
            }
        }
    }

    public function testCalculoFreteRegiaoSP(): void
    {
        $res = $this->freteService->calcular('01310-100', 100.00, false);

        $this->assertTrue($res['ok']);
        $this->assertEquals('São Paulo', $res['regiao']);
        $this->assertCount(2, $res['opcoes']); // PAC and SEDEX

        $codigos = array_column($res['opcoes'], 'codigo');
        $this->assertContains('pac', $codigos);
        $this->assertContains('sedex', $codigos);
    }

    public function testCalculoFreteGratisAcimaDe199(): void
    {
        $res = $this->freteService->calcular('01310-100', 250.00, false);

        $this->assertTrue($res['ok']);
        $codigos = array_column($res['opcoes'], 'codigo');
        $this->assertContains('gratis', $codigos);

        $opcaoGratis = null;
        foreach ($res['opcoes'] as $op) {
            if ($op['codigo'] === 'gratis') {
                $opcaoGratis = $op;
                break;
            }
        }
        $this->assertNotNull($opcaoGratis);
        $this->assertEquals(0.0, $opcaoGratis['valor']);
    }

    public function testCalculoFreteRegioesVariadas(): void
    {
        // Nordeste (Salvador/BA)
        $resNE = $this->freteService->calcular('40010000', 100.00);
        $this->assertTrue($resNE['ok']);
        $this->assertEquals('Nordeste', $resNE['regiao']);

        // Sul (Porto Alegre/RS)
        $resSul = $this->freteService->calcular('90010000', 100.00);
        $this->assertTrue($resSul['ok']);
        $this->assertEquals('Sul (PR/SC/RS)', $resSul['regiao']);

        // CEP Inválido
        $resInvalido = $this->freteService->calcular('123', 100.00);
        $this->assertFalse($resInvalido['ok']);
    }

    public function testValidacaoCuponsComMySQL(): void
    {
        $this->garantirCuponsTeste();
        $db = \Config\Database::connect('default');
        $cupomModel = new CupomModel($db);

        // 1. Cupom PRIMEIRACOMPRA (10% OFF)
        $val1 = $cupomModel->validarCupom('PRIMEIRACOMPRA', 200.00);
        $this->assertTrue($val1['valido']);
        $this->assertEquals(20.00, $val1['desconto']);

        // 2. Cupom OFF20 (Fixo R$ 20, mínimo R$ 100)
        $valMinInvalido = $cupomModel->validarCupom('OFF20', 80.00);
        $this->assertFalse($valMinInvalido['valido']);
        $this->assertStringContainsString('mínimo', $valMinInvalido['erro']);

        $valMinValido = $cupomModel->validarCupom('OFF20', 150.00);
        $this->assertTrue($valMinValido['valido']);
        $this->assertEquals(20.00, $valMinValido['desconto']);

        // 3. Cupom Inexistente
        $valInexistente = $cupomModel->validarCupom('CUPOM_NAO_EXISTE_999', 100.00);
        $this->assertFalse($valInexistente['valido']);
    }

    public function testTotaisCarrinhoComCupomEFrete(): void
    {
        $this->garantirCuponsTeste();
        $db = \Config\Database::connect('default');
        $cupomModel = new CupomModel($db);

        // Subtotal R$ 200, Cupom 10% (R$ 20), Frete R$ 14.90 => Total = 180 + 14.90 = 194.90
        $subtotal = 200.00;
        $valCupom = $cupomModel->validarCupom('PRIMEIRACOMPRA', $subtotal);
        $this->assertTrue($valCupom['valido']);

        $desconto = $valCupom['desconto'];
        $frete    = 14.90;
        $total    = max(0.0, $subtotal - $desconto) + $frete;

        $this->assertEquals(20.00, $desconto);
        $this->assertEquals(194.90, $total);
    }
}
