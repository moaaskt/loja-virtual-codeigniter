<?php

namespace App\Services;

use App\Models\AuditLogModel;
use Config\Services;

/**
 * AuditService — Serviço global para registro desacoplado de trilhas de auditoria (audit trail).
 */
class AuditService
{
    /**
     * Registra uma ação na trilha de auditoria do sistema.
     *
     * @param string                  $acao            Ação executada (ex: create, update, delete, status_change, login)
     * @param string                  $entidade        Entidade/Tabela afetada (ex: pedidos, produtos, usuarios, cupons)
     * @param int|null                $registroId      ID do registro afetado
     * @param array|object|null       $dadosNovos      Estado novo ou payload da alteração
     * @param array|object|null       $dadosAnteriores Estado anterior do registro antes da alteração
     * @param int|null                $usuarioId       ID do usuário responsável (se null, tenta pegar da sessão)
     * @return bool
     */
    public static function log(
        string $acao,
        string $entidade,
        ?int $registroId = null,
        array|object|null $dadosNovos = null,
        array|object|null $dadosAnteriores = null,
        ?int $usuarioId = null
    ): bool {
        try {
            $session = Services::session();
            $request = Services::request();

            // Identifica usuário
            $resolvedUserId = $usuarioId ?? $session->get('usuario_id') ?? $session->get('user_id') ?? null;
            if ($resolvedUserId !== null) {
                $resolvedUserId = (int) $resolvedUserId;
            }

            // Identifica IP e User-Agent com fallback seguro
            $ip = null;
            $userAgent = null;
            if ($request !== null && method_exists($request, 'getIPAddress')) {
                $ip = $request->getIPAddress();
            }
            if ($request !== null && method_exists($request, 'getUserAgent')) {
                $ua = $request->getUserAgent();
                $userAgent = $ua ? (string) $ua : null;
            }

            $auditModel = new AuditLogModel();

            $logData = [
                'usuario_id'       => $resolvedUserId,
                'acao'             => mb_substr($acao, 0, 50),
                'entidade'         => mb_substr($entidade, 0, 50),
                'registro_id'      => $registroId,
                'dados_anteriores' => $dadosAnteriores !== null ? json_encode($dadosAnteriores, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : null,
                'dados_novos'      => $dadosNovos !== null ? json_encode($dadosNovos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : null,
                'ip'               => $ip ? mb_substr($ip, 0, 45) : null,
                'user_agent'       => $userAgent ? mb_substr($userAgent, 0, 255) : null,
                'created_at'       => date('Y-m-d H:i:s'),
            ];

            return (bool) $auditModel->insert($logData);
        } catch (\Throwable $e) {
            log_message('error', '[AuditService] Falha ao registrar log de auditoria: ' . $e->getMessage());
            return false;
        }
    }
}
