<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NotificationLogModel;
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
        $res = $this->emailService->reprocessarNotificacao($id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($res);
        }

        if ($res['ok']) {
            return redirect()->back()->with('sucesso', 'Notificação reprocessada com sucesso!');
        }

        return redirect()->back()->with('erro', 'Falha ao reprocessar: ' . ($res['mensagem'] ?? 'Erro desconhecido.'));
    }
}
