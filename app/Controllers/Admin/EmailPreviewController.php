<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\EmailService;

/**
 * EmailPreviewController — Painel de pré-visualização e teste de e-mails transacionais.
 *
 * Rotas:
 *   GET  admin/emails                        → Painel de gerenciamento de e-mails
 *   GET  admin/emails/preview/(:segment)     → Preview do template no navegador
 *   POST admin/emails/testar                 → Envia e-mail de teste SMTP
 *   POST admin/emails/reenviar/(:num)/(:segment) → Reenvia notificação para um pedido
 */
class EmailPreviewController extends BaseController
{
    /**
     * Painel principal de gerenciamento de e-mails transacionais.
     */
    public function index()
    {
        $data = [
            'title'     => 'E-mails & Notificações',
            'templates' => [
                [
                    'id'       => 'pedido_criado',
                    'label'    => 'Pedido Realizado',
                    'icon'     => '🛒',
                    'desc'     => 'Enviado ao cliente após criação do pedido no checkout.',
                    'badge'    => 'badge-purple',
                    'gatilho'  => 'Automático — ao finalizar checkout',
                ],
                [
                    'id'       => 'pagamento_aprovado',
                    'label'    => 'Pagamento Aprovado',
                    'icon'     => '✅',
                    'desc'     => 'Enviado quando o pagamento é confirmado (Pix/Webhook ou Cartão imediato).',
                    'badge'    => 'badge-success',
                    'gatilho'  => 'Automático — webhook ou cartão aprovado',
                ],
                [
                    'id'       => 'pedido_enviado',
                    'label'    => 'Pedido Enviado',
                    'icon'     => '🚚',
                    'desc'     => 'Enviado ao alterar status para "Enviado" no painel admin (com código de rastreio).',
                    'badge'    => 'badge-info',
                    'gatilho'  => 'Manual — ao alterar status para Enviado',
                ],
                [
                    'id'       => 'pedido_cancelado',
                    'label'    => 'Pedido Cancelado',
                    'icon'     => '❌',
                    'desc'     => 'Enviado ao cancelar pedido via painel admin ou falha de pagamento.',
                    'badge'    => 'badge-danger',
                    'gatilho'  => 'Automático — cancelamento ou falha de pagamento',
                ],
            ],
        ];

        return view('admin/emails/index', $data);
    }

    /**
     * Renderiza um template de e-mail no navegador com dados simulados (preview).
     */
    public function preview(string $template = 'pedido_criado')
    {
        $templatesPermitidos = ['pedido_criado', 'pagamento_aprovado', 'pedido_enviado', 'pedido_cancelado', 'teste_smtp'];

        if (!in_array($template, $templatesPermitidos)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Dados simulados realistas para preview
        $pedidoSimulado = [
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
            'codigo_rastreio'  => 'BR123456789SP',
            'criado_em'        => date('Y-m-d H:i:s'),
        ];

        $clienteSimulado = [
            'id'    => 5,
            'nome'  => 'Maria da Silva',
            'email' => 'maria@exemplo.com.br',
        ];

        $itensSimulados = [
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
        ];

        $dados = [
            'pedido'          => $pedidoSimulado,
            'cliente'         => $clienteSimulado,
            'itens'           => $itensSimulados,
            'motivo'          => 'Item indisponível em estoque.',
            'codigo_rastreio' => 'BR123456789SP',
            'destinatario'    => 'preview@gstore.com.br',
            'timestamp'       => date('d/m/Y H:i:s'),
        ];

        // Renderiza direto no browser sem o layout admin
        return view('emails/' . $template, $dados);
    }

    /**
     * Envia um e-mail de teste SMTP para o endereço informado.
     */
    public function testar()
    {
        $destinatario = trim($this->request->getPost('destinatario') ?? '');

        if (empty($destinatario) || !filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {
            return redirect()
                ->to(site_url('admin/emails'))
                ->with('error', 'Informe um endereço de e-mail válido para o teste.');
        }

        $emailService = new EmailService();
        $resultado    = $emailService->testarConexaoSmtp($destinatario);

        if ($resultado['ok']) {
            return redirect()
                ->to(site_url('admin/emails'))
                ->with('success', "E-mail de teste enviado com sucesso para {$destinatario}!");
        }

        return redirect()
            ->to(site_url('admin/emails'))
            ->with('error', 'Falha ao enviar e-mail de teste: ' . ($resultado['mensagem'] ?? 'Verifique as configurações SMTP em .env'));
    }
}
