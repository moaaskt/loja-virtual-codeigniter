<?php

namespace App\Services;

use App\Models\NotificationLogModel;
use App\Models\PedidoModel;
use App\Models\PedidoProdutoModel;
use App\Models\UsuarioModel;
use CodeIgniter\Email\Email;

/**
 * EmailService — Serviço centralizado de envio de e-mails transacionais.
 *
 * Todos os métodos são resilientes: capturam exceções de SMTP, registram
 * na fila/histórico (notification_logs) e nunca lançam exceções fatais.
 */
class EmailService
{
    protected Email $mailer;
    protected PedidoModel $pedidoModel;
    protected PedidoProdutoModel $pedidoProdutoModel;
    protected NotificationLogModel $notificationLogModel;

    public function __construct()
    {
        $this->mailer               = \Config\Services::email();
        $this->pedidoModel          = new PedidoModel();
        $this->pedidoProdutoModel   = new PedidoProdutoModel();
        $this->notificationLogModel = new NotificationLogModel();
    }

    // -------------------------------------------------------------------------
    // Core send method
    // -------------------------------------------------------------------------

    /**
     * Envia um e-mail HTML a partir de uma view renderizada e registra no notification_logs.
     *
     * @param string      $para     Endereço de destino
     * @param string      $assunto  Assunto do e-mail
     * @param string      $view     View path (ex: 'emails/pedido_criado')
     * @param array       $dados    Variáveis passadas à view
     * @param string|null $evento   Identificador do evento (ex: pedido_criado)
     * @param int|null    $logId    Se for reprocessamento, passa o logId existente
     * @return array ['ok' => bool, 'mensagem' => string, 'log_id' => int|null]
     */
    public function enviar(
        string $para,
        string $assunto,
        string $view,
        array $dados = [],
        ?string $evento = null,
        ?int $logId = null
    ): array {
        $eventoNome = $evento ?? basename($view);
        $payloadArray = [
            'assunto' => $assunto,
            'view'    => $view,
            'dados'   => $dados,
        ];
        $payloadJson = json_encode($payloadArray, JSON_UNESCAPED_UNICODE);

        // Se não for reprocessamento com ID existente, cria o registro inicial como pendente
        if (!$logId) {
            try {
                $logId = $this->notificationLogModel->insert([
                    'canal'         => 'email',
                    'destinatario'  => $para,
                    'evento'        => $eventoNome,
                    'payload'       => $payloadJson,
                    'status'        => 'pendente',
                    'tentativas'    => 1,
                    'mensagem_erro' => null,
                    'enviado_em'    => null,
                ]);
            } catch (\Throwable $e) {
                log_message('error', "[EmailService] Falha ao registrar log de notificação: {$e->getMessage()}");
            }
        }

        try {
            $htmlBody = view($view, $dados);

            $this->mailer->clear();
            $this->mailer->setTo($para);
            $this->mailer->setSubject($assunto);
            $this->mailer->setMessage($htmlBody);

            if ($this->mailer->send(false)) {
                log_message('info', "[EmailService] E-mail enviado para {$para} | Assunto: {$assunto}");

                if ($logId) {
                    $this->notificationLogModel->update($logId, [
                        'status'        => 'enviado',
                        'mensagem_erro' => null,
                        'enviado_em'    => date('Y-m-d H:i:s'),
                    ]);
                }

                return [
                    'ok'       => true,
                    'mensagem' => 'E-mail enviado com sucesso.',
                    'log_id'   => $logId,
                ];
            }

            $debugInfo = $this->mailer->printDebugger(['headers']);
            $erroMsg   = !empty($debugInfo) ? strip_tags($debugInfo) : 'Falha no envio SMTP.';
            log_message('error', "[EmailService] Falha ao enviar para {$para}: {$erroMsg}");

            if ($logId) {
                $logAtual = $this->notificationLogModel->find($logId);
                $tentativas = $logAtual ? ((int) $logAtual['tentativas'] + 1) : 1;
                $this->notificationLogModel->update($logId, [
                    'status'        => 'falhou',
                    'tentativas'    => $tentativas,
                    'mensagem_erro' => mb_substr($erroMsg, 0, 500),
                ]);
            }

            return [
                'ok'       => false,
                'mensagem' => 'Falha ao enviar o e-mail. Verifique as configurações SMTP.',
                'log_id'   => $logId,
            ];
        } catch (\Throwable $e) {
            $erroMsg = $e->getMessage();
            log_message('error', "[EmailService] Exceção ao enviar para {$para}: {$erroMsg}");

            if ($logId) {
                $logAtual = $this->notificationLogModel->find($logId);
                $tentativas = $logAtual ? ((int) $logAtual['tentativas'] + 1) : 1;
                $this->notificationLogModel->update($logId, [
                    'status'        => 'falhou',
                    'tentativas'    => $tentativas,
                    'mensagem_erro' => mb_substr($erroMsg, 0, 500),
                ]);
            }

            return [
                'ok'       => false,
                'mensagem' => 'Erro interno no serviço de e-mail: ' . $erroMsg,
                'log_id'   => $logId,
            ];
        }
    }

    /**
     * Reprocessa um disparo de notificação que falhou ou precisa de reenvio.
     *
     * @param int $logId ID do registro na tabela notification_logs
     * @return array ['ok' => bool, 'mensagem' => string, 'log_id' => int]
     */
    public function reprocessarNotificacao(int $logId): array
    {
        $log = $this->notificationLogModel->find($logId);
        if (!$log) {
            return ['ok' => false, 'mensagem' => 'Registro de notificação não encontrado.', 'log_id' => $logId];
        }

        // Incrementa o contador de tentativas antes do reenvio
        $tentativas = ((int) ($log['tentativas'] ?? 0)) + 1;
        $this->notificationLogModel->update($logId, [
            'tentativas' => $tentativas,
        ]);

        $payload = json_decode($log['payload'] ?? '{}', true);
        $para    = $log['destinatario'];
        $assunto = $payload['assunto'] ?? "Notificação — G'Store";
        $view    = $payload['view'] ?? ('emails/' . ($log['evento'] ?? 'teste_smtp'));
        $dados   = $payload['dados'] ?? [];

        return $this->enviar($para, $assunto, $view, $dados, $log['evento'], $logId);
    }

    // -------------------------------------------------------------------------
    // Notification methods
    // -------------------------------------------------------------------------

    /**
     * Notifica o cliente que o pedido foi criado com sucesso.
     */
    public function notificarPedidoCriado(int|array $pedido): array
    {
        $pedidoData = $this->resolverPedido($pedido);
        if (!$pedidoData) {
            return ['ok' => false, 'mensagem' => 'Pedido não encontrado.'];
        }

        $itens   = $this->pedidoProdutoModel->getProdutosDePedido($pedidoData['id']);
        $cliente = $this->resolverCliente($pedidoData['usuario_id']);

        if (empty($cliente['email'])) {
            return ['ok' => false, 'mensagem' => 'E-mail do cliente não encontrado.'];
        }

        return $this->enviar(
            $cliente['email'],
            "✅ Pedido #{$pedidoData['id']} recebido — G'Store",
            'emails/pedido_criado',
            [
                'pedido'  => $pedidoData,
                'itens'   => $itens,
                'cliente' => $cliente,
            ]
        );
    }

    /**
     * Notifica o cliente que o pagamento foi aprovado.
     */
    public function notificarPagamentoAprovado(int|array $pedido): array
    {
        $pedidoData = $this->resolverPedido($pedido);
        if (!$pedidoData) {
            return ['ok' => false, 'mensagem' => 'Pedido não encontrado.'];
        }

        $itens   = $this->pedidoProdutoModel->getProdutosDePedido($pedidoData['id']);
        $cliente = $this->resolverCliente($pedidoData['usuario_id']);

        if (empty($cliente['email'])) {
            return ['ok' => false, 'mensagem' => 'E-mail do cliente não encontrado.'];
        }

        return $this->enviar(
            $cliente['email'],
            "💳 Pagamento aprovado — Pedido #{$pedidoData['id']} — G'Store",
            'emails/pagamento_aprovado',
            [
                'pedido'  => $pedidoData,
                'itens'   => $itens,
                'cliente' => $cliente,
            ]
        );
    }

    /**
     * Notifica o cliente que o pedido foi enviado / despachado.
     */
    public function notificarPedidoEnviado(int|array $pedido, ?string $codigoRastreio = null): array
    {
        $pedidoData = $this->resolverPedido($pedido);
        if (!$pedidoData) {
            return ['ok' => false, 'mensagem' => 'Pedido não encontrado.'];
        }

        $itens   = $this->pedidoProdutoModel->getProdutosDePedido($pedidoData['id']);
        $cliente = $this->resolverCliente($pedidoData['usuario_id']);

        if (empty($cliente['email'])) {
            return ['ok' => false, 'mensagem' => 'E-mail do cliente não encontrado.'];
        }

        // Usa o código passado como parâmetro ou o salvo no pedido
        $rastreio = $codigoRastreio ?? $pedidoData['codigo_rastreio'] ?? null;

        return $this->enviar(
            $cliente['email'],
            "🚚 Pedido #{$pedidoData['id']} enviado — G'Store",
            'emails/pedido_enviado',
            [
                'pedido'          => $pedidoData,
                'itens'           => $itens,
                'cliente'         => $cliente,
                'codigo_rastreio' => $rastreio,
            ]
        );
    }

    /**
     * Notifica o cliente que o pedido foi cancelado.
     */
    public function notificarPedidoCancelado(int|array $pedido, ?string $motivo = null): array
    {
        $pedidoData = $this->resolverPedido($pedido);
        if (!$pedidoData) {
            return ['ok' => false, 'mensagem' => 'Pedido não encontrado.'];
        }

        $itens   = $this->pedidoProdutoModel->getProdutosDePedido($pedidoData['id']);
        $cliente = $this->resolverCliente($pedidoData['usuario_id']);

        if (empty($cliente['email'])) {
            return ['ok' => false, 'mensagem' => 'E-mail do cliente não encontrado.'];
        }

        return $this->enviar(
            $cliente['email'],
            "❌ Pedido #{$pedidoData['id']} cancelado — G'Store",
            'emails/pedido_cancelado',
            [
                'pedido'  => $pedidoData,
                'itens'   => $itens,
                'cliente' => $cliente,
                'motivo'  => $motivo ?? 'Pedido cancelado pelo administrador.',
            ]
        );
    }

    /**
     * Envia um e-mail de teste para verificar a conectividade SMTP.
     */
    public function testarConexaoSmtp(string $destinatario): array
    {
        return $this->enviar(
            $destinatario,
            "🔧 Teste de Configuração SMTP — G'Store",
            'emails/teste_smtp',
            [
                'destinatario' => $destinatario,
                'timestamp'    => date('d/m/Y H:i:s'),
            ]
        );
    }

    // -------------------------------------------------------------------------
    // Helpers internos
    // -------------------------------------------------------------------------

    /**
     * Resolve dados do pedido: aceita int (ID) ou array já carregado.
     */
    protected function resolverPedido(int|array $pedido): ?array
    {
        if (is_array($pedido)) {
            return $pedido;
        }

        $data = $this->pedidoModel->getPedidoComCliente($pedido);
        return $data ?: null;
    }

    /**
     * Busca dados do cliente pelo ID do usuário.
     */
    protected function resolverCliente(int $usuarioId): array
    {
        try {
            $usuarioModel = new UsuarioModel();
            $usuario = $usuarioModel->find($usuarioId);
            return $usuario ?? [];
        } catch (\Throwable $e) {
            log_message('warning', "[EmailService] Não foi possível carregar o cliente ID {$usuarioId}: {$e->getMessage()}");
            return [];
        }
    }
}
