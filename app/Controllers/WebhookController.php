<?php

namespace App\Controllers;

use App\Services\PagamentoService;

class WebhookController extends BaseController
{
    protected PagamentoService $pagamentoService;

    public function __construct()
    {
        $this->pagamentoService = new PagamentoService();
    }

    /**
     * Endpoint para recebimento assíncrono de notificações de pagamento.
     * Rota: POST api/webhook/pagamento
     */
    public function receber()
    {
        $payload = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($payload)) {
            $rawInput = $this->request->getBody();
            if (!empty($rawInput)) {
                $payload = json_decode($rawInput, true) ?? [];
            }
        }

        if (empty($payload)) {
            return $this->response->setStatusCode(400)->setJSON([
                'ok'   => false,
                'erro' => 'Payload vazio ou formato inválido.',
            ]);
        }

        $resultado = $this->pagamentoService->processarWebhook($payload);

        if (!$resultado['ok']) {
            return $this->response->setStatusCode(400)->setJSON($resultado);
        }

        return $this->response->setStatusCode(200)->setJSON($resultado);
    }

    /**
     * Endpoint para simulação de webhook de pagamento (testes e painel administrativo).
     * Rota: POST api/webhook/simular
     */
    public function simular()
    {
        $payload = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($payload)) {
            $rawInput = $this->request->getBody();
            if (!empty($rawInput)) {
                $payload = json_decode($rawInput, true) ?? [];
            }
        }

        $transacaoId = $payload['transacao_id'] ?? null;
        $pedidoId    = $payload['pedido_id'] ?? null;
        $evento      = $payload['evento'] ?? 'pago';

        $resultado = $this->pagamentoService->processarWebhook([
            'transacao_id' => $transacaoId,
            'pedido_id'    => $pedidoId,
            'evento'       => $evento,
            'pago_em'      => date('Y-m-d H:i:s'),
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($resultado);
        }

        if (!$resultado['ok']) {
            return redirect()->back()->with('error', 'Erro ao simular webhook: ' . $resultado['erro']);
        }

        return redirect()->back()->with('success', 'Webhook simulado com sucesso: ' . $resultado['mensagem']);
    }
}
