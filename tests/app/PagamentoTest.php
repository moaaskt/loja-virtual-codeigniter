<?php

namespace Tests\App;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\PagamentoService;
use App\Models\PagamentoModel;
use App\Models\PedidoModel;

class PagamentoTest extends CIUnitTestCase
{
    protected PagamentoService $pagamentoService;
    protected PagamentoModel $pagamentoModel;
    protected PedidoModel $pedidoModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pagamentoService = new PagamentoService();
        $this->pagamentoModel   = new PagamentoModel();
        $this->pedidoModel      = new PedidoModel();
    }

    /**
     * Auxiliar para criar um pedido temporário no banco para testes.
     */
    protected function criarPedidoTeste(float $valorTotal = 150.00): int
    {
        $db = \Config\Database::connect('default');

        // Garante que temos um usuário de teste
        $usuario = $db->table('usuarios')->where('email', 'teste_pagamento@loja.com')->get()->getRowArray();
        if (!$usuario) {
            $db->table('usuarios')->insert([
                'nome'       => 'Cliente Teste Pagamento',
                'email'      => 'teste_pagamento@loja.com',
                'senha_hash' => password_hash('123456', PASSWORD_DEFAULT),
                'role'       => 'cliente',
            ]);
            $usuarioId = $db->insertID();
        } else {
            $usuarioId = (int) $usuario['id'];
        }

        $this->pedidoModel->insert([
            'usuario_id'       => $usuarioId,
            'valor_total'      => $valorTotal,
            'cupom_codigo'     => null,
            'desconto_valor'   => 0.00,
            'frete_modalidade' => 'SEDEX',
            'frete_valor'      => 15.00,
            'forma_pagamento'  => 'pix',
            'status_pagamento' => 'pendente',
            'status'           => 'pendente',
            'cep'              => '01310-100',
            'logradouro'       => 'Av. Paulista',
            'numero'           => '1000',
            'bairro'           => 'Bela Vista',
            'cidade'           => 'São Paulo',
            'uf'               => 'SP',
        ]);

        return (int) $this->pedidoModel->getInsertID();
    }

    public function testGerarPixEMVCopiaEColaEValores(): void
    {
        $pedidoId = $this->criarPedidoTeste(250.50);
        $pedido   = $this->pedidoModel->find($pedidoId);

        $resultado = $this->pagamentoService->gerarPix($pedido);

        $this->assertTrue($resultado['ok']);
        $this->assertNotEmpty($resultado['transacao_id']);
        $this->assertStringStartsWith('PIX_', $resultado['transacao_id']);
        $this->assertNotEmpty($resultado['pix_copiacola']);
        $this->assertStringContainsString('BR.GOV.BCB.PIX', $resultado['pix_copiacola']);
        $this->assertStringContainsString('6304', $resultado['pix_copiacola']); // CRC16 prefix
        $this->assertNotEmpty($resultado['pix_qrcode_base64']);
        $this->assertStringStartsWith('data:image/svg+xml;base64,', $resultado['pix_qrcode_base64']);
        $this->assertEquals(250.50, $resultado['valor']);

        // Verifica gravação no banco
        $pagamento = $this->pagamentoModel->buscarPorPedido($pedidoId);
        $this->assertNotNull($pagamento);
        $this->assertEquals('pix', $pagamento['metodo']);
        $this->assertEquals('pendente', $pagamento['status']);
        $this->assertEquals(250.50, (float) $pagamento['valor']);
    }

    public function testValidacaoLuhnEIdentificacaoBandeira(): void
    {
        // 1. Visa válida (4...)
        $cartaoVisa = '4000001234567899';
        $this->assertTrue($this->pagamentoService->validarLuhn($cartaoVisa));
        $this->assertEquals('visa', $this->pagamentoService->identificarBandeira($cartaoVisa));

        // 2. Mastercard válida (55...)
        $cartaoMaster = '5555555555554444';
        $this->assertTrue($this->pagamentoService->validarLuhn($cartaoMaster));
        $this->assertEquals('mastercard', $this->pagamentoService->identificarBandeira($cartaoMaster));

        // 3. Amex válida (37...)
        $cartaoAmex = '378282246310005';
        $this->assertTrue($this->pagamentoService->validarLuhn($cartaoAmex));
        $this->assertEquals('amex', $this->pagamentoService->identificarBandeira($cartaoAmex));

        // 4. Cartão com Luhn inválido
        $cartaoInvalido = '4000001234567892';
        $this->assertFalse($this->pagamentoService->validarLuhn($cartaoInvalido));
    }

    public function testValidacaoDadosCartaoErros(): void
    {
        // Cartão Vencido (com número válido no Luhn)
        $dadosVencido = [
            'cartao_numero'   => '4000001234567899',
            'cartao_nome'     => 'CLIENTE TESTE',
            'cartao_validade' => '01/20', // Vencido
            'cartao_cvv'      => '123',
            'cartao_parcelas' => 1,
        ];
        $val1 = $this->pagamentoService->validarDadosCartao($dadosVencido);
        $this->assertFalse($val1['valido']);
        $this->assertStringContainsString('vencido', $val1['erro']);

        // CVV Inválido
        $dadosCvv = [
            'cartao_numero'   => '4000001234567899',
            'cartao_nome'     => 'CLIENTE TESTE',
            'cartao_validade' => '12/32',
            'cartao_cvv'      => '1', // Curto
            'cartao_parcelas' => 1,
        ];
        $val2 = $this->pagamentoService->validarDadosCartao($dadosCvv);
        $this->assertFalse($val2['valido']);
        $this->assertStringContainsString('CVV', $val2['erro']);

        // Nome vazio
        $dadosNome = [
            'cartao_numero'   => '4000001234567899',
            'cartao_nome'     => '',
            'cartao_validade' => '12/32',
            'cartao_cvv'      => '123',
            'cartao_parcelas' => 1,
        ];
        $val3 = $this->pagamentoService->validarDadosCartao($dadosNome);
        $this->assertFalse($val3['valido']);
    }

    public function testProcessarCartaoAprovado(): void
    {
        $pedidoId = $this->criarPedidoTeste(300.00);
        $pedido   = $this->pedidoModel->find($pedidoId);

        $dadosCartao = [
            'cartao_numero'   => '4000001234567899',
            'cartao_nome'     => 'MARIA SILVA',
            'cartao_validade' => '12/32',
            'cartao_cvv'      => '999',
            'cartao_parcelas' => 3,
        ];

        $resultado = $this->pagamentoService->processarCartao($pedido, $dadosCartao);

        $this->assertTrue($resultado['ok']);
        $this->assertEquals('pago', $resultado['status']);
        $this->assertEquals('visa', $resultado['cartao_bandeira']);
        $this->assertEquals('7899', $resultado['cartao_ultimos_digitos']);
        $this->assertEquals(3, $resultado['cartao_parcelas']);
        $this->assertEquals(100.00, $resultado['valor_parcela']);

        // Verifica se pedido foi atualizado para pago
        $pedidoAtualizado = $this->pedidoModel->find($pedidoId);
        $this->assertEquals('pago', $pedidoAtualizado['status']);
        $this->assertEquals('pago', $pedidoAtualizado['status_pagamento']);
        $this->assertEquals('cartao_credito', $pedidoAtualizado['forma_pagamento']);
    }

    public function testProcessarCartaoRecusadoSimulado(): void
    {
        $pedidoId = $this->criarPedidoTeste(120.00);
        $pedido   = $this->pedidoModel->find($pedidoId);

        // Cartão terminando em 0000 com Luhn válido: 499273987160000
        $dadosCartaoRecusa = [
            'cartao_numero'   => '499273987160000', // Termina em 0000
            'cartao_nome'     => 'TESTE RECUSA',
            'cartao_validade' => '10/30',
            'cartao_cvv'      => '555',
            'cartao_parcelas' => 1,
        ];

        $resultado = $this->pagamentoService->processarCartao($pedido, $dadosCartaoRecusa);
        $this->assertFalse($resultado['ok']);
        $this->assertStringContainsString('não autorizada', $resultado['erro']);
    }

    public function testProcessarWebhookAprovado(): void
    {
        $pedidoId = $this->criarPedidoTeste(180.00);
        $pedido   = $this->pedidoModel->find($pedidoId);

        // 1. Gera cobrança Pix
        $pix = $this->pagamentoService->gerarPix($pedido);
        $this->assertTrue($pix['ok']);
        $transacaoId = $pix['transacao_id'];

        // 2. Dispara Webhook de aprovação
        $payload = [
            'transacao_id' => $transacaoId,
            'evento'       => 'pago',
            'pago_em'      => '2026-08-19 20:00:00',
        ];

        $resWebhook = $this->pagamentoService->processarWebhook($payload);
        $this->assertTrue($resWebhook['ok']);
        $this->assertEquals('pago', $resWebhook['status']);

        // 3. Verifica se o pedido e pagamento foram marcados como pagos
        $pedidoFinal = $this->pedidoModel->find($pedidoId);
        $this->assertEquals('pago', $pedidoFinal['status']);
        $this->assertEquals('pago', $pedidoFinal['status_pagamento']);

        $pagamentoFinal = $this->pagamentoModel->buscarPorTransacao($transacaoId);
        $this->assertEquals('pago', $pagamentoFinal['status']);
        $this->assertNotEmpty($pagamentoFinal['pago_em']);
    }

    public function testProcessarWebhookCancelado(): void
    {
        $pedidoId = $this->criarPedidoTeste(99.00);
        $pedido   = $this->pedidoModel->find($pedidoId);

        $pix = $this->pagamentoService->gerarPix($pedido);
        $transacaoId = $pix['transacao_id'];

        $payload = [
            'transacao_id' => $transacaoId,
            'evento'       => 'cancelado',
            'motivo'       => 'Tempo limite de pagamento expirado.',
        ];

        $resWebhook = $this->pagamentoService->processarWebhook($payload);
        $this->assertTrue($resWebhook['ok']);
        $this->assertEquals('falhou', $resWebhook['status']);

        $pedidoFinal = $this->pedidoModel->find($pedidoId);
        $this->assertEquals('cancelado', $pedidoFinal['status']);
        $this->assertEquals('falhou', $pedidoFinal['status_pagamento']);
    }
}
