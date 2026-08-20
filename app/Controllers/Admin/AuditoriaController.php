<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Models\UsuarioModel;

class AuditoriaController extends BaseController
{
    protected AuditLogModel $auditModel;
    protected UsuarioModel $usuarioModel;

    public function __construct()
    {
        $this->auditModel   = new AuditLogModel();
        $this->usuarioModel = new UsuarioModel();
    }

    public function index()
    {
        $filtros = [
            'usuario_id' => $this->request->getGet('usuario_id'),
            'acao'       => $this->request->getGet('acao'),
            'entidade'   => $this->request->getGet('entidade'),
            'busca'      => $this->request->getGet('busca'),
        ];

        $logs = $this->auditModel->getLogsComFiltros($filtros, 25);
        $pager = $this->auditModel->pager;

        // Lista de usuários para filtro rápido
        $usuarios = [];
        try {
            $usuarios = $this->usuarioModel->select('id, nome, email')->orderBy('nome', 'ASC')->findAll();
        } catch (\Throwable $e) {
            log_message('warning', '[AuditoriaController] Falha ao carregar usuários: ' . $e->getMessage());
        }

        return view('admin/auditoria/index', [
            'title'    => 'Trilha de Auditoria',
            'logs'     => $logs,
            'pager'    => $pager,
            'filtros'  => $filtros,
            'usuarios' => $usuarios,
        ]);
    }
}
