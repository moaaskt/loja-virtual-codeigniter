<?php

namespace Tests\App;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\AuditService;
use App\Models\AuditLogModel;

class AuditServiceTest extends CIUnitTestCase
{
    protected AuditLogModel $auditModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->auditModel = new AuditLogModel();
    }

    public function testRegistrarLogAuditoriaComSucesso(): void
    {
        $dadosAnteriores = ['status' => 'pendente', 'valor' => 100.0];
        $dadosNovos      = ['status' => 'pago', 'valor' => 100.0];

        $registrado = AuditService::log(
            'status_change',
            'pedidos',
            1042,
            $dadosNovos,
            $dadosAnteriores,
            1
        );

        $this->assertTrue($registrado, 'AuditService::log deve retornar true ao registrar com sucesso.');

        // Verifica inserção no banco de dados
        $ultimoLog = $this->auditModel->where('entidade', 'pedidos')
            ->where('registro_id', 1042)
            ->orderBy('id', 'DESC')
            ->first();

        $this->assertNotNull($ultimoLog);
        $this->assertEquals('status_change', $ultimoLog['acao']);
        $this->assertEquals('pedidos', $ultimoLog['entidade']);
        $this->assertEquals(1042, $ultimoLog['registro_id']);
        $this->assertEquals(1, $ultimoLog['usuario_id']);
        $this->assertStringContainsString('pendente', $ultimoLog['dados_anteriores']);
        $this->assertStringContainsString('pago', $ultimoLog['dados_novos']);
    }

    public function testRegistrarLogAnonimo(): void
    {
        $registrado = AuditService::log(
            'login_failed',
            'usuarios',
            null,
            ['email' => 'hacker@tentativa.com'],
            null,
            null
        );

        $this->assertTrue($registrado);

        $log = $this->auditModel->where('acao', 'login_failed')
            ->orderBy('id', 'DESC')
            ->first();

        $this->assertNotNull($log);
        $this->assertNull($log['usuario_id']);
        $this->assertStringContainsString('hacker@tentativa.com', $log['dados_novos']);
    }

    public function testFiltrosAuditLogModel(): void
    {
        // Cria registros para teste de filtro
        AuditService::log('create', 'produtos', 501, ['nome' => 'Camisa Teste'], null, 1);
        AuditService::log('delete', 'produtos', 502, null, ['nome' => 'Camisa Deletada'], 1);

        $logsCreate = $this->auditModel->getLogsComFiltros(['acao' => 'create', 'entidade' => 'produtos'], 10);
        $this->assertNotEmpty($logsCreate);
        foreach ($logsCreate as $l) {
            $this->assertEquals('create', $l['acao']);
            $this->assertEquals('produtos', $l['entidade']);
        }
    }
}
