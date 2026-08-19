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
        $rawInput = $this->request->getBody();
        $payload = !empty($rawInput) ? json_decode($rawInput, true) : null;

        if (empty($payload)) {
            try {
                $payload = $this->request->getJSON(true);
            } catch (\Throwable $e) {
                $payload = null;
            }
        }

        if (empty($payload)) {
            $post = $this->request->getPost();
            if (!empty($post)) {
                $payload = $post;
            }
        }

        if (empty($payload) || !is_array($payload)) {
            return $this->response->setStatusCode(400)->setJSON([
                'ok'   => false,
                'erro' => 'Payload JSON vazio ou formato inválido.',
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
        $rawInput = $this->request->getBody();
        $payload = !empty($rawInput) ? json_decode($rawInput, true) : null;

        if (empty($payload)) {
            try {
                $payload = $this->request->getJSON(true);
            } catch (\Throwable $e) {
                $payload = null;
            }
        }

        if (empty($payload)) {
            $post = $this->request->getPost();
            if (!empty($post)) {
                $payload = $post;
            }
        }

        $isJsonRequest = $this->request->isAJAX()
            || str_contains($this->request->getHeaderLine('Content-Type'), 'application/json')
            || str_contains($this->request->getHeaderLine('Accept'), 'application/json');

        if (empty($payload) || !is_array($payload)) {
            if ($isJsonRequest) {
                return $this->response->setStatusCode(400)->setJSON([
                    'ok'   => false,
                    'erro' => 'Payload JSON vazio ou formato inválido.',
                ]);
            }
            return redirect()->back()->with('error', 'Payload de simulação inválido ou vazio.');
        }

        $transacaoId = $payload['transacao_id'] ?? null;
        $pedidoId    = $payload['pedido_id'] ?? null;
        $evento      = $payload['evento'] ?? $payload['status'] ?? 'pago';

        $resultado = $this->pagamentoService->processarWebhook([
            'transacao_id' => $transacaoId,
            'pedido_id'    => $pedidoId,
            'evento'       => $evento,
            'pago_em'      => date('Y-m-d H:i:s'),
        ]);

        if ($isJsonRequest) {
            $statusCode = $resultado['ok'] ? 200 : 400;
            return $this->response->setStatusCode($statusCode)->setJSON($resultado);
        }

        if (!$resultado['ok']) {
            return redirect()->back()->with('error', 'Erro ao simular webhook: ' . $resultado['erro']);
        }

        return redirect()->back()->with('success', 'Webhook simulado com sucesso: ' . $resultado['mensagem']);
    }
}
