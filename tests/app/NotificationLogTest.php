<?php

namespace Tests\App;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\NotificationLogModel;
use App\Services\EmailService;

class NotificationLogTest extends CIUnitTestCase
{
    protected NotificationLogModel $notificationModel;
    protected EmailService $emailService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notificationModel = new NotificationLogModel();
        $this->emailService      = new EmailService();
    }

    public function testPersistenciaAoEnviarEmail(): void
    {
        $destinatario = 'teste_log@gstore.com.br';
        $assunto      = 'Teste de Notificação Persistida';

        $resultado = $this->emailService->enviar(
            $destinatario,
            $assunto,
            'emails/teste_smtp',
            ['destinatario' => $destinatario, 'timestamp' => date('d/m/Y H:i:s')],
            'teste_smtp'
        );

        $this->assertIsArray($resultado);
        $this->assertArrayHasKey('log_id', $resultado);

        if ($resultado['log_id']) {
            $log = $this->notificationModel->find($resultado['log_id']);
            $this->assertNotNull($log);
            $this->assertEquals($destinatario, $log['destinatario']);
            $this->assertEquals('email', $log['canal']);
            $this->assertEquals('teste_smtp', $log['evento']);
            $this->assertContains($log['status'], ['enviado', 'falhou']);
            $this->assertNotEmpty($log['payload']);
        }
    }

    public function testReprocessarNotificacao(): void
    {
        // Cria um log inicial simulando falha
        $logId = $this->notificationModel->insert([
            'canal'         => 'email',
            'destinatario'  => 'reprocessar@gstore.com.br',
            'evento'        => 'teste_smtp',
            'payload'       => json_encode([
                'assunto' => 'Reprocessamento de Teste',
                'view'    => 'emails/teste_smtp',
                'dados'   => ['destinatario' => 'reprocessar@gstore.com.br', 'timestamp' => date('d/m/Y H:i:s')],
            ]),
            'status'        => 'falhou',
            'tentativas'    => 1,
            'mensagem_erro' => 'Conexão SMTP recusada simulada.',
            'enviado_em'    => null,
        ]);

        $this->assertIsNumeric($logId);

        $resultado = $this->emailService->reprocessarNotificacao((int) $logId);

        $this->assertIsArray($resultado);
        $this->assertArrayHasKey('ok', $resultado);

        // Verifica incremento no número de tentativas (1 -> 2)
        $logAtualizado = $this->notificationModel->find($logId);
        $this->assertNotNull($logAtualizado);
        $this->assertGreaterThanOrEqual(2, (int) $logAtualizado['tentativas']);
        $this->assertContains($logAtualizado['status'], ['enviado', 'falhou']);
    }

    public function testEstatisticasNotificacoes(): void
    {
        $stats = $this->notificationModel->getEstatisticas();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total', $stats);
        $this->assertArrayHasKey('enviados', $stats);
        $this->assertArrayHasKey('falhas', $stats);
        $this->assertArrayHasKey('pendentes', $stats);
    }
}
