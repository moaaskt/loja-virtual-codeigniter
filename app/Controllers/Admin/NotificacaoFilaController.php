<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NotificationLogModel;
use App\Services\AuditService;
use App\Services\EmailService;

class NotificacaoFilaController extends BaseController
{
    protected NotificationLogModel $notificationModel;
    protected EmailService $emailService;

    public function __construct()
    {
        $this->notificationModel = new NotificationLogModel();
        $this->emailService      = new EmailService();
    }

    public function index()
    {
        $filtros = [
            'canal'   => $this->request->getGet('canal'),
            'status'  => $this->request->getGet('status'),
            'evento'  => $this->request->getGet('evento'),
            'busca'   => $this->request->getGet('busca'),
        ];

        $logs         = $this->notificationModel->getLogsComFiltros($filtros, 25);
        $pager        = $this->notificationModel->pager;
        $estatisticas = $this->notificationModel->getEstatisticas();

        return view('admin/notificacoes/fila', [
            'title'        => 'Fila de Notificações',
            'logs'         => $logs,
            'pager'        => $pager,
            'filtros'      => $filtros,
            'estatisticas' => $estatisticas,
        ]);
    }

    public function reprocessar(int $id)
    {
        $logAntes = $this->notificationModel->find($id);
        $res      = $this->emailService->reprocessarNotificacao($id);
        $logDepois= $this->notificationModel->find($id);

        // Registro de Auditoria no Reenvio
        AuditService::log(
            'notification_resend',
            'notification_logs',
            $id,
            [
                'status'         => $logDepois['status'] ?? ($res['ok'] ? 'enviado' : 'falhou'),
                'tentativas'     => $logDepois['tentativas'] ?? null,
                'resultado'      => $res['ok'] ? 'sucesso' : 'falha',
                'mensagem'       => $res['mensagem'] ?? '',
                'destinatario'   => $logAntes['destinatario'] ?? '',
                'evento'         => $logAntes['evento'] ?? '',
            ],
            [
                'status'         => $logAntes['status'] ?? 'desconhecido',
                'tentativas'     => $logAntes['tentativas'] ?? null,
                'mensagem_erro'  => $logAntes['mensagem_erro'] ?? null,
            ]
        );

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($res);
        }

        if ($res['ok']) {
            return redirect()->back()->with('sucesso', "Notificação #{$id} reprocessada e enviada com sucesso para " . esc($logAntes['destinatario'] ?? '') . "!");
        }

        return redirect()->back()->with('erro', "Falha ao reprocessar notificação #{$id}: " . ($res['mensagem'] ?? 'Erro desconhecido no envio.'));
    }
}
