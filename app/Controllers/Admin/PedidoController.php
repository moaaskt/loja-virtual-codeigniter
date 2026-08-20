<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PagamentoModel;
use App\Models\PedidoModel;
use App\Models\PedidoProdutoModel;
use App\Services\AuditService;
use App\Services\EmailService;

class PedidoController extends BaseController
{
    public function index()
    {
        $model = new PedidoModel();

        $data = [
            'title'          => 'Gerenciamento de Pedidos',
            'pedidos'        => $model->getAllPedidosComCliente(15),
            'pager'          => $model->pager,
            'status_options' => ['pendente', 'pago', 'processando', 'enviado', 'entregue', 'cancelado'],
        ];

        return view('admin/pedidos/index', $data);
    }

    public function detalhe($id = null)
    {
        $pedidoModel        = new PedidoModel();
        $pedidoProdutoModel = new PedidoProdutoModel();
        $pagamentoModel     = new PagamentoModel();

        $pedido = $pedidoModel->getPedidoComCliente($id);

        if ($pedido === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title'          => 'Detalhes do Pedido #' . $pedido['id'],
            'pedido'         => $pedido,
            'pagamento'      => $pagamentoModel->buscarPorPedido($id),
            'produtos'       => $pedidoProdutoModel->getProdutosDePedido($id),
            'status_options' => ['pendente', 'pago', 'processando', 'enviado', 'entregue', 'cancelado'],
        ];

        return view('admin/pedidos/detalhe', $data);
    }

    public function atualizarStatus($id = null)
    {
        $model          = new PedidoModel();
        $emailService   = new EmailService();
        $novoStatus     = $this->request->getPost('status');
        $codigoRastreio = trim($this->request->getPost('codigo_rastreio') ?? '');
        $motivoCancelamento = trim($this->request->getPost('motivo_cancelamento') ?? '');

        $statusPermitidos = ['pendente', 'pago', 'processando', 'enviado', 'entregue', 'cancelado'];

        if (empty($novoStatus) || !in_array($novoStatus, $statusPermitidos)) {
            return redirect()->to(site_url('admin/pedidos'))->with('error', 'Status inválido.');
        }

        $camposUpdate = ['status' => $novoStatus];

        // Salvar código de rastreio ao enviar
        if ($novoStatus === 'enviado' && !empty($codigoRastreio)) {
            $camposUpdate['codigo_rastreio'] = $codigoRastreio;
        }

        $pedidoAntigo = $model->find($id);

        if ($model->update($id, $camposUpdate)) {
            AuditService::log('status_change', 'pedidos', (int) $id, [
                'status'          => $novoStatus,
                'codigo_rastreio' => $codigoRastreio ?: ($pedidoAntigo['codigo_rastreio'] ?? null),
            ], [
                'status'          => $pedidoAntigo['status'] ?? 'desconhecido',
                'codigo_rastreio' => $pedidoAntigo['codigo_rastreio'] ?? null,
            ]);

            // Disparos de e-mail conforme novo status
            match ($novoStatus) {
                'pago'     => $emailService->notificarPagamentoAprovado((int) $id),
                'enviado'  => $emailService->notificarPedidoEnviado((int) $id, !empty($codigoRastreio) ? $codigoRastreio : null),
                'cancelado'=> $emailService->notificarPedidoCancelado((int) $id, !empty($motivoCancelamento) ? $motivoCancelamento : null),
                default    => null,
            };

            return redirect()
                ->to(site_url('admin/pedidos/detalhe/' . $id))
                ->with('success', 'Status do pedido atualizado com sucesso!');
        }

        return redirect()
            ->to(site_url('admin/pedidos/detalhe/' . $id))
            ->with('error', 'Erro ao atualizar o status. Tente novamente.');
    }

    /**
     * Reenvio manual de notificação de e-mail para um pedido.
     */
    public function reenviarEmail($id = null, string $tipo = 'criado')
    {
        $emailService = new EmailService();

        $eventosLabels = [
            'criado'    => 'Pedido Criado',
            'pago'      => 'Pagamento Aprovado',
            'enviado'   => 'Pedido Enviado',
            'cancelado' => 'Pedido Cancelado',
        ];

        $eventoLabel = $eventosLabels[$tipo] ?? ucfirst($tipo);

        $resultado = match ($tipo) {
            'criado'   => $emailService->notificarPedidoCriado((int) $id),
            'pago'     => $emailService->notificarPagamentoAprovado((int) $id),
            'enviado'  => $emailService->notificarPedidoEnviado((int) $id),
            'cancelado'=> $emailService->notificarPedidoCancelado((int) $id),
            default    => ['ok' => false, 'mensagem' => 'Tipo de notificação inválido.'],
        };

        // Registro de Auditoria no Reenvio Manual do Pedido
        AuditService::log(
            'notification_resend',
            'pedidos',
            (int) $id,
            [
                'evento'    => $tipo,
                'resultado' => $resultado['ok'] ? 'sucesso' : 'falha',
                'mensagem'  => $resultado['mensagem'] ?? '',
            ],
            null
        );

        if ($resultado['ok']) {
            session()->setFlashdata('sucesso', "Notificação de {$eventoLabel} enviada com sucesso para o cliente!");
            return redirect()->to(site_url('admin/pedidos/detalhe/' . $id));
        }

        $msgErro = $resultado['mensagem'] ?? 'Erro desconhecido ao disparar e-mail.';
        session()->setFlashdata('erro', "Falha ao enviar notificação: {$msgErro}");
        return redirect()->to(site_url('admin/pedidos/detalhe/' . $id));
    }
}