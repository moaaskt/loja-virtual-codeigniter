<?php

namespace App\Tests;

use App\Services\EmailService;
use App\Models\PedidoModel;
use App\Models\PedidoProdutoModel;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * EmailServiceTest — Testes da Phase 7: Notificações Transacionais por E-mail.
 *
 * Verifica:
 * 1. Renderização dos 4 templates de e-mail sem erros
 * 2. Resposta resiliente do EmailService quando SMTP não está configurado
 * 3. Campo codigo_rastreio na tabela pedidos (migration)
 * 4. Método atualizarRastreio() no PedidoModel
 */
class EmailServiceTest extends CIUnitTestCase
{
    protected EmailService $emailService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->emailService = new EmailService();
    }

    // -------------------------------------------------------------------------
    // 1. Testes de Renderização dos Templates
    // -------------------------------------------------------------------------

    /**
     * Testa se o template pedido_criado renderiza sem erros.
     */
    public function testTemplatePedidoCriadoRenderiza(): void
    {
        $html = $this->renderizarTemplate('emails/pedido_criado', $this->getDadosSimulados());

        $this->assertIsString($html);
        $this->assertNotEmpty($html);
        $this->assertStringContainsString('Pedido Recebido', $html);
        $this->assertStringContainsString('Maria da Silva', $html);
        $this->assertStringContainsString('1042', $html);
        $this->assertStringContainsString('R$', $html);
    }

    /**
     * Testa se o template pagamento_aprovado renderiza sem erros.
     */
    public function testTemplatePagamentoAprovadoRenderiza(): void
    {
        $html = $this->renderizarTemplate('emails/pagamento_aprovado', $this->getDadosSimulados());

        $this->assertIsString($html);
        $this->assertNotEmpty($html);
        $this->assertStringContainsString('Pagamento Aprovado', $html);
        $this->assertStringContainsString('1042', $html);
        $this->assertStringContainsString('Maria da Silva', $html);
    }

    /**
     * Testa se o template pedido_enviado renderiza sem erros.
     */
    public function testTemplatePedidoEnviadoRenderiza(): void
    {
        $dados = $this->getDadosSimulados();
        $dados['codigo_rastreio'] = 'BR123456789SP';

        $html = $this->renderizarTemplate('emails/pedido_enviado', $dados);

        $this->assertIsString($html);
        $this->assertNotEmpty($html);
        $this->assertStringContainsString('Pedido Enviado', $html);
        $this->assertStringContainsString('BR123456789SP', $html);
        $this->assertStringContainsString('Maria da Silva', $html);
    }

    /**
     * Testa se o template pedido_cancelado renderiza sem erros.
     */
    public function testTemplatePedidoCanceladoRenderiza(): void
    {
        $dados = $this->getDadosSimulados();
        $dados['motivo'] = 'Item indisponível em estoque.';

        $html = $this->renderizarTemplate('emails/pedido_cancelado', $dados);

        $this->assertIsString($html);
        $this->assertNotEmpty($html);
        $this->assertStringContainsString('Pedido Cancelado', $html);
        $this->assertStringContainsString('Item indispon', $html);
        $this->assertStringContainsString('Maria da Silva', $html);
    }

    /**
     * Testa se o template teste_smtp renderiza sem erros.
     */
    public function testTemplateTesteSmtpRenderiza(): void
    {
        $html = $this->renderizarTemplate('emails/teste_smtp', [
            'destinatario' => 'teste@gstore.com.br',
            'timestamp'    => date('d/m/Y H:i:s'),
        ]);

        $this->assertIsString($html);
        $this->assertNotEmpty($html);
        $this->assertStringContainsString('SMTP configurado', $html);
        $this->assertStringContainsString('teste@gstore.com.br', $html);
    }

    // -------------------------------------------------------------------------
    // 2. Testes de Resiliência do EmailService
    // -------------------------------------------------------------------------

    /**
     * Verifica que notificarPedidoCriado com ID inexistente retorna ['ok' => false]
     * sem lançar exceção.
     */
    public function testNotificarPedidoCriadoComIdInexistenteRetornaNaoOk(): void
    {
        $resultado = $this->emailService->notificarPedidoCriado(999999);

        $this->assertIsArray($resultado);
        $this->assertArrayHasKey('ok', $resultado);
        $this->assertFalse($resultado['ok']);
        $this->assertArrayHasKey('mensagem', $resultado);
    }

    /**
     * Verifica que notificarPagamentoAprovado com pedido array sem email
     * retorna ['ok' => false] sem explodir.
     */
    public function testNotificarPagamentoAprovadoSemEmailRetornaNaoOk(): void
    {
        $pedidoSemEmail = [
            'id'               => 1,
            'usuario_id'       => 99999,
            'valor_total'      => 100.0,
            'forma_pagamento'  => 'pix',
            'status_pagamento' => 'pago',
            'status'           => 'pago',
            'cep'              => '01310-100',
            'logradouro'       => 'Av. Paulista',
            'numero'           => '1000',
            'bairro'           => 'Bela Vista',
            'cidade'           => 'São Paulo',
            'uf'               => 'SP',
            'criado_em'        => date('Y-m-d H:i:s'),
        ];

        $resultado = $this->emailService->notificarPagamentoAprovado($pedidoSemEmail);

        $this->assertIsArray($resultado);
        $this->assertArrayHasKey('ok', $resultado);
        // Deve retornar false porque o cliente com usuario_id 99999 não existe
        $this->assertFalse($resultado['ok']);
    }

    /**
     * Verifica que notificarPedidoEnviado com ID inexistente retorna ['ok' => false].
     */
    public function testNotificarPedidoEnviadoComIdInexistenteRetornaNaoOk(): void
    {
        $resultado = $this->emailService->notificarPedidoEnviado(999999, 'BR999');

        $this->assertIsArray($resultado);
        $this->assertFalse($resultado['ok']);
    }

    /**
     * Verifica que notificarPedidoCancelado com ID inexistente retorna ['ok' => false].
     */
    public function testNotificarPedidoCanceladoComIdInexistenteRetornaNaoOk(): void
    {
        $resultado = $this->emailService->notificarPedidoCancelado(999999);

        $this->assertIsArray($resultado);
        $this->assertFalse($resultado['ok']);
    }

    // -------------------------------------------------------------------------
    // 3. Testes de Banco de Dados — Migration & Model
    // -------------------------------------------------------------------------

    /**
     * Verifica que a coluna codigo_rastreio existe na tabela pedidos.
     */
    public function testColunaCodigoRastreioPresenteNaTabelaPedidos(): void
    {
        $db      = \Config\Database::connect('default');
        $colunas = $db->getFieldNames('pedidos');

        $this->assertContains('codigo_rastreio', $colunas, 'A coluna codigo_rastreio deve existir na tabela pedidos após a migration.');
    }

    /**
     * Verifica que codigo_rastreio está no allowedFields do PedidoModel.
     */
    public function testCodigoRastreioEstaNoAllowedFieldsDoPedidoModel(): void
    {
        $model = new PedidoModel();

        // Usa reflexão para acessar propriedade protegida
        $reflection = new \ReflectionClass($model);
        $property   = $reflection->getProperty('allowedFields');
        $property->setAccessible(true);
        $allowedFields = $property->getValue($model);

        $this->assertContains('codigo_rastreio', $allowedFields);
    }

    /**
     * Verifica que o método atualizarRastreio() existe no PedidoModel.
     */
    public function testPedidoModelTemMetodoAtualizarRastreio(): void
    {
        $model = new PedidoModel();

        $this->assertTrue(
            method_exists($model, 'atualizarRastreio'),
            'PedidoModel deve ter o método atualizarRastreio()'
        );
    }

    // -------------------------------------------------------------------------
    // 4. Testes de Estrutura do EmailService
    // -------------------------------------------------------------------------

    /**
     * Verifica que o EmailService tem todos os métodos de notificação esperados.
     */
    public function testEmailServiceTemTodosOsMetodosDeNotificacao(): void
    {
        $this->assertTrue(method_exists($this->emailService, 'enviar'));
        $this->assertTrue(method_exists($this->emailService, 'notificarPedidoCriado'));
        $this->assertTrue(method_exists($this->emailService, 'notificarPagamentoAprovado'));
        $this->assertTrue(method_exists($this->emailService, 'notificarPedidoEnviado'));
        $this->assertTrue(method_exists($this->emailService, 'notificarPedidoCancelado'));
        $this->assertTrue(method_exists($this->emailService, 'testarConexaoSmtp'));
    }

    /**
     * Verifica que testarConexaoSmtp retorna array com chave 'ok' sem explodir.
     */
    public function testTestarConexaoSmtpRetornaArrayComChaveOk(): void
    {
        $resultado = $this->emailService->testarConexaoSmtp('teste@gstore.com.br');

        $this->assertIsArray($resultado);
        $this->assertArrayHasKey('ok', $resultado);
        $this->assertArrayHasKey('mensagem', $resultado);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Renderiza uma view de e-mail e retorna o HTML.
     */
    protected function renderizarTemplate(string $viewPath, array $dados): string
    {
        return view($viewPath, $dados);
    }

    /**
     * Retorna dados simulados realistas para testes de renderização.
     */
    protected function getDadosSimulados(): array
    {
        return [
            'pedido' => [
                'id'               => 1042,
                'usuario_id'       => 5,
                'valor_total'      => 349.90,
                'cupom_codigo'     => 'DESCONTO10',
                'desconto_valor'   => 38.88,
                'frete_modalidade' => 'SEDEX',
                'frete_valor'      => 29.90,
                'forma_pagamento'  => 'cartao_credito',
                'status_pagamento' => 'pago',
                'status'           => 'enviado',
                'cep'              => '01310-100',
                'logradouro'       => 'Av. Paulista',
                'numero'           => '1578',
                'complemento'      => 'Apto 42',
                'bairro'           => 'Bela Vista',
                'cidade'           => 'São Paulo',
                'uf'               => 'SP',
                'codigo_rastreio'  => null,
                'criado_em'        => date('Y-m-d H:i:s'),
            ],
            'cliente' => [
                'id'    => 5,
                'nome'  => 'Maria da Silva',
                'email' => 'maria@exemplo.com.br',
            ],
            'itens' => [
                [
                    'produto_nome'   => 'Tênis Running Pro 3000',
                    'tamanho'        => '42',
                    'cor'            => '#1a1a2e',
                    'quantidade'     => 1,
                    'preco_unitario' => 279.90,
                ],
                [
                    'produto_nome'   => 'Meia de Compressão Elite',
                    'tamanho'        => 'M',
                    'cor'            => null,
                    'quantidade'     => 2,
                    'preco_unitario' => 34.45,
                ],
            ],
            'motivo'          => null,
            'codigo_rastreio' => null,
            'destinatario'    => 'preview@gstore.com.br',
            'timestamp'       => date('d/m/Y H:i:s'),
        ];
    }
}
