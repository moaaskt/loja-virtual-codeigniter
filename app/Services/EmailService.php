<?php

namespace App\Services;

use App\Models\PedidoModel;
use App\Models\PedidoProdutoModel;
use App\Models\UsuarioModel;
use CodeIgniter\Email\Email;

/**
 * EmailService — Serviço centralizado de envio de e-mails transacionais.
 *
 * Todos os métodos são resilientes: capturam exceções de SMTP e registram
 * em log sem lançar exceções fatais que quebrariam o fluxo HTTP do usuário.
 */
class EmailService
{
    protected Email $mailer;
    protected PedidoModel $pedidoModel;
    protected PedidoProdutoModel $pedidoProdutoModel;

    public function __construct()
    {
        $this->mailer             = \Config\Services::email();
        $this->pedidoModel        = new PedidoModel();
        $this->pedidoProdutoModel = new PedidoProdutoModel();
    }

    // -------------------------------------------------------------------------
    // Core send method
    // -------------------------------------------------------------------------

    /**
     * Envia um e-mail HTML a partir de uma view renderizada.
     *
     * @param string $para    Endereço de destino
     * @param string $assunto Assunto do e-mail
     * @param string $view    View path (ex: 'emails/pedido_criado')
     * @param array  $dados   Variáveis passadas à view
     * @return array ['ok' => bool, 'mensagem' => string]
     */
    public function enviar(string $para, string $assunto, string $view, array $dados = []): array
    {
        try {
            $htmlBody = view($view, $dados);

            $this->mailer->clear();
            $this->mailer->setTo($para);
            $this->mailer->setSubject($assunto);
            $this->mailer->setMessage($htmlBody);

            if ($this->mailer->send(false)) {
                log_message('info', "[EmailService] E-mail enviado para {$para} | Assunto: {$assunto}");
                return ['ok' => true, 'mensagem' => 'E-mail enviado com sucesso.'];
            }

            $debugInfo = $this->mailer->printDebugger(['headers']);
            log_message('error', "[EmailService] Falha ao enviar para {$para}: {$debugInfo}");
            return ['ok' => false, 'mensagem' => 'Falha ao enviar o e-mail. Verifique as configurações SMTP.'];
        } catch (\Throwable $e) {
            log_message('error', "[EmailService] Exceção ao enviar para {$para}: {$e->getMessage()}");
            return ['ok' => false, 'mensagem' => 'Erro interno no serviço de e-mail: ' . $e->getMessage()];
        }
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
