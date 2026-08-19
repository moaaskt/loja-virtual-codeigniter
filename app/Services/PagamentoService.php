<?php

namespace App\Services;

use App\Models\PagamentoModel;
use App\Models\PedidoModel;

class PagamentoService
{
    protected PagamentoModel $pagamentoModel;
    protected PedidoModel $pedidoModel;
    protected EmailService $emailService;

    public function __construct()
    {
        $this->pagamentoModel = new PagamentoModel();
        $this->pedidoModel    = new PedidoModel();
        $this->emailService   = new EmailService();
    }

    /**
     * Gera uma cobrança Pix para o pedido informado.
     *
     * @param array $pedido Dados do pedido (deve conter 'id' e 'valor_total')
     * @return array Resultado com transacao_id, pix_copiacola, pix_qrcode_base64 e expira_em
     */
    public function gerarPix(array $pedido): array
    {
        $pedidoId   = (int) $pedido['id'];
        $valorTotal = (float) $pedido['valor_total'];

        $transacaoId = 'PIX_' . $pedidoId . '_' . strtoupper(bin2hex(random_bytes(4)));
        $expiraEm    = date('Y-m-d H:i:s', time() + (30 * 60)); // 30 minutos

        // Montagem do Payload Pix EMV Copia e Cola
        $chavePix = 'lojavirtual@ecommerce.com.br';
        $pixCopiaCola = $this->montarPayloadPix($chavePix, 'LOJA VIRTUAL', 'SAO PAULO', $valorTotal, $transacaoId);

        // Geração do QR Code SVG Base64
        $qrCodeSvg = $this->gerarQrCodeSvg($pixCopiaCola);
        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

        $dadosPagamento = [
            'pedido_id'         => $pedidoId,
            'metodo'            => 'pix',
            'status'            => 'pendente',
            'valor'             => $valorTotal,
            'transacao_id'      => $transacaoId,
            'pix_copiacola'     => $pixCopiaCola,
            'pix_qrcode_base64' => $qrCodeBase64,
            'pix_expiracao'     => $expiraEm,
            'cartao_parcelas'   => 1,
            'detalhes_json'     => json_encode([
                'chave_pix' => $chavePix,
                'gateway'   => 'simulado_pix',
            ]),
        ];

        // Se já existir pagamento anterior pendente para esse pedido, remove/sobrescreve
        $existente = $this->pagamentoModel->buscarPorPedido($pedidoId);
        if ($existente && $existente['status'] === 'pendente') {
            $this->pagamentoModel->update($existente['id'], $dadosPagamento);
            $pagamentoId = $existente['id'];
        } else {
            $this->pagamentoModel->insert($dadosPagamento);
            $pagamentoId = $this->pagamentoModel->getInsertID();
        }

        // Atualiza status_pagamento em pedidos
        $this->pedidoModel->update($pedidoId, [
            'forma_pagamento'  => 'pix',
            'status_pagamento' => 'pendente',
        ]);

        return [
            'ok'                => true,
            'pagamento_id'      => $pagamentoId,
            'transacao_id'      => $transacaoId,
            'pix_copiacola'     => $pixCopiaCola,
            'pix_qrcode_base64' => $qrCodeBase64,
            'expira_em'         => $expiraEm,
            'valor'             => $valorTotal,
        ];
    }

    /**
     * Valida os dados de um cartão de crédito.
     */
    public function validarDadosCartao(array $dados): array
    {
        $numero   = preg_replace('/\D/', '', $dados['cartao_numero'] ?? '');
        $nome     = trim($dados['cartao_nome'] ?? '');
        $validade = trim($dados['cartao_validade'] ?? ''); // MM/AA ou MM/AAAA
        $cvv      = preg_replace('/\D/', '', $dados['cartao_cvv'] ?? '');
        $parcelas = (int) ($dados['cartao_parcelas'] ?? 1);

        // Validação do Número (Luhn)
        if (empty($numero) || strlen($numero) < 13 || strlen($numero) > 19 || !$this->validarLuhn($numero)) {
            return ['valido' => false, 'erro' => 'Número de cartão de crédito inválido.'];
        }

        // Validação do Nome
        if (empty($nome) || strlen($nome) < 3) {
            return ['valido' => false, 'erro' => 'Informe o nome impresso no cartão de crédito.'];
        }

        // Validação da Validade
        if (!preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2}|[0-9]{4})$/', $validade, $matches)) {
            return ['valido' => false, 'erro' => 'Validade do cartão deve estar no formato MM/AA.'];
        }

        $mes = (int) $matches[1];
        $ano = (int) $matches[2];
        if ($ano < 100) {
            $ano += 2000;
        }

        $anoAtual = (int) date('Y');
        $mesAtual = (int) date('m');

        if ($ano < $anoAtual || ($ano === $anoAtual && $mes < $mesAtual)) {
            return ['valido' => false, 'erro' => 'O cartão informado está vencido.'];
        }

        // Validação do CVV
        $bandeira = $this->identificarBandeira($numero);
        $tamanhoCvvEsperado = ($bandeira === 'amex') ? 4 : 3;
        if (strlen($cvv) < 3 || strlen($cvv) > 4) {
            return ['valido' => false, 'erro' => 'Código de segurança (CVV) inválido.'];
        }

        // Validação de Parcelas
        if ($parcelas < 1 || $parcelas > 12) {
            return ['valido' => false, 'erro' => 'Número de parcelas inválido (1x a 12x).'];
        }

        return [
            'valido'          => true,
            'numero'          => $numero,
            'ultimos_digitos' => substr($numero, -4),
            'bandeira'        => $bandeira,
            'nome'            => strtoupper($nome),
            'validade'        => sprintf('%02d/%04d', $mes, $ano),
            'parcelas'        => $parcelas,
        ];
    }

    /**
     * Processa um pagamento via cartão de crédito.
     */
    public function processarCartao(array $pedido, array $dadosCartao): array
    {
        $validacao = $this->validarDadosCartao($dadosCartao);
        if (!$validacao['valido']) {
            return ['ok' => false, 'erro' => $validacao['erro']];
        }

        $pedidoId       = (int) $pedido['id'];
        $valorTotal     = (float) $pedido['valor_total'];
        $ultimosDigitos = $validacao['ultimos_digitos'];
        $bandeira       = $validacao['bandeira'];
        $parcelas       = $validacao['parcelas'];
        $valorParcela   = round($valorTotal / $parcelas, 2);

        $transacaoId = 'CC_' . $pedidoId . '_' . strtoupper(bin2hex(random_bytes(4)));

        // Simulação de regras de teste:
        // Cartão terminado em '0000' simula recusa por limite
        // Cartão terminado em '1111' simula recusa por antifraude
        if ($ultimosDigitos === '0000') {
            $dadosPagamento = [
                'pedido_id'              => $pedidoId,
                'metodo'                 => 'cartao_credito',
                'status'                 => 'falhou',
                'valor'                  => $valorTotal,
                'transacao_id'           => $transacaoId,
                'cartao_ultimos_digitos' => $ultimosDigitos,
                'cartao_bandeira'        => $bandeira,
                'cartao_parcelas'        => $parcelas,
                'detalhes_json'          => json_encode([
                    'motivo_recusa' => 'Transação não autorizada pela emissora do cartão (Limite insuficiente).',
                    'valor_parcela' => $valorParcela,
                    'gateway'       => 'simulado_cartao',
                ]),
            ];
            $this->pagamentoModel->insert($dadosPagamento);

            return [
                'ok'   => false,
                'erro' => 'Transação não autorizada pela emissora do cartão. Verifique seus limites ou tente outro cartão.',
            ];
        }

        if ($ultimosDigitos === '1111') {
            $dadosPagamento = [
                'pedido_id'              => $pedidoId,
                'metodo'                 => 'cartao_credito',
                'status'                 => 'falhou',
                'valor'                  => $valorTotal,
                'transacao_id'           => $transacaoId,
                'cartao_ultimos_digitos' => $ultimosDigitos,
                'cartao_bandeira'        => $bandeira,
                'cartao_parcelas'        => $parcelas,
                'detalhes_json'          => json_encode([
                    'motivo_recusa' => 'Bloqueio preventivo pelo sistema antifraude.',
                    'valor_parcela' => $valorParcela,
                    'gateway'       => 'simulado_cartao',
                ]),
            ];
            $this->pagamentoModel->insert($dadosPagamento);

            return [
                'ok'   => false,
                'erro' => 'Transação bloqueada por motivos de segurança. Entre em contato com seu banco emissor.',
            ];
        }

        // Pagamento Aprovado com Sucesso
        $pagoEm = date('Y-m-d H:i:s');
        $dadosPagamento = [
            'pedido_id'              => $pedidoId,
            'metodo'                 => 'cartao_credito',
            'status'                 => 'pago',
            'valor'                  => $valorTotal,
            'transacao_id'           => $transacaoId,
            'cartao_ultimos_digitos' => $ultimosDigitos,
            'cartao_bandeira'        => $bandeira,
            'cartao_parcelas'        => $parcelas,
            'pago_em'                => $pagoEm,
            'detalhes_json'          => json_encode([
                'autorizacao'   => strtoupper(bin2hex(random_bytes(3))),
                'nsu'           => rand(100000, 999999),
                'valor_parcela' => $valorParcela,
                'titular'       => $validacao['nome'],
                'gateway'       => 'simulado_cartao',
            ]),
        ];
        $this->pagamentoModel->insert($dadosPagamento);
        $pagamentoId = $this->pagamentoModel->getInsertID();

        // Atualiza status do pedido para 'pago' e status_pagamento para 'pago'
        $this->pedidoModel->update($pedidoId, [
            'forma_pagamento'  => 'cartao_credito',
            'status_pagamento' => 'pago',
            'status'           => 'pago',
        ]);

        return [
            'ok'                     => true,
            'pagamento_id'           => $pagamentoId,
            'transacao_id'           => $transacaoId,
            'metodo'                 => 'cartao_credito',
            'status'                 => 'pago',
            'cartao_ultimos_digitos' => $ultimosDigitos,
            'cartao_bandeira'        => $bandeira,
            'cartao_parcelas'        => $parcelas,
            'valor_parcela'          => $valorParcela,
            'pago_em'                => $pagoEm,
        ];
    }

    /**
     * Processa notificações de Webhook (assíncronas) do gateway de pagamento.
     *
     * @param array $payload Dados recebidos no webhook
     * @return array Resultado do processamento
     */
    public function processarWebhook(array $payload): array
    {
        $transacaoId = $payload['transacao_id'] ?? null;
        $pedidoId    = $payload['pedido_id'] ?? null;
        $evento      = strtolower($payload['evento'] ?? $payload['status'] ?? '');

        if (!$transacaoId && !$pedidoId) {
            return ['ok' => false, 'erro' => 'Identificador da transação ou pedido não informado no payload.'];
        }

        $pagamento = null;
        if ($transacaoId) {
            $pagamento = $this->pagamentoModel->buscarPorTransacao($transacaoId);
        }
        if (!$pagamento && $pedidoId) {
            $pagamento = $this->pagamentoModel->buscarPorPedido((int) $pedidoId);
        }

        if (!$pagamento) {
            return ['ok' => false, 'erro' => 'Transação de pagamento não localizada.'];
        }

        $pedidoIdAlvo = (int) $pagamento['pedido_id'];
        $pedido = $this->pedidoModel->find($pedidoIdAlvo);

        if (!$pedido) {
            return ['ok' => false, 'erro' => 'Pedido associado não encontrado.'];
        }

        // Se o evento é de aprovação / pagamento confirmado
        if (in_array($evento, ['pago', 'aprovado', 'payment.approved', 'paid', 'approved'])) {
            $pagoEm = $payload['pago_em'] ?? date('Y-m-d H:i:s');
            $this->pagamentoModel->marcarComoPago((int) $pagamento['id'], $pagoEm);
            $this->pedidoModel->atualizarStatusPagamento($pedidoIdAlvo, 'pago', 'pago');

            // Dispara notificação de pagamento aprovado
            $this->emailService->notificarPagamentoAprovado($pedidoIdAlvo);

            return [
                'ok'           => true,
                'mensagem'     => 'Pagamento aprovado com sucesso via Webhook.',
                'pedido_id'    => $pedidoIdAlvo,
                'transacao_id' => $pagamento['transacao_id'],
                'status'       => 'pago',
            ];
        }

        // Se o evento é de cancelamento / falha
        if (in_array($evento, ['cancelado', 'falhou', 'recusado', 'payment.failed', 'cancelled', 'refunded'])) {
            $motivo = $payload['motivo'] ?? 'Pagamento cancelado ou não autorizado pelo gateway.';
            $this->pagamentoModel->marcarComoFalho((int) $pagamento['id'], $motivo);
            $this->pedidoModel->atualizarStatusPagamento($pedidoIdAlvo, 'falhou', 'cancelado');

            // Dispara notificação de pedido cancelado
            $this->emailService->notificarPedidoCancelado($pedidoIdAlvo, $motivo);

            return [
                'ok'           => true,
                'mensagem'     => 'Pagamento marcado como falho/cancelado via Webhook.',
                'pedido_id'    => $pedidoIdAlvo,
                'transacao_id' => $pagamento['transacao_id'],
                'status'       => 'falhou',
            ];
        }

        return ['ok' => false, 'erro' => 'Evento de webhook não reconhecido: ' . esc($evento)];
    }

    /**
     * Identifica a bandeira do cartão a partir dos primeiros dígitos (BIN).
     */
    public function identificarBandeira(string $numero): string
    {
        if (preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/', $numero)) {
            return 'visa';
        }
        if (preg_match('/^(?:5[1-5][0-9]{2}|222[1-9]|22[3-9][0-9]|2[3-6][0-9]{2}|27[01][0-9]|2720)[0-9]{12}$/', $numero)) {
            return 'mastercard';
        }
        if (preg_match('/^3[47][0-9]{13}$/', $numero)) {
            return 'amex';
        }
        if (preg_match('/^((((636368)|(438935)|(504175)|(451416)|(636297))\d{0,10})|((5067)|(4576)|(4011))\d{0,12})/', $numero)) {
            return 'elo';
        }
        if (preg_match('/^(606282\d{10}(\d{3})?)|(3841\d{15})$/', $numero)) {
            return 'hipercard';
        }

        return 'visa'; // Default para cartão genérico
    }

    /**
     * Validador de número de cartão via Algoritmo de Luhn (Módulo 10).
     */
    public function validarLuhn(string $numero): bool
    {
        $soma = 0;
        $tamanho = strlen($numero);
        $paridade = $tamanho % 2;

        for ($i = $tamanho - 1; $i >= 0; $i--) {
            $digito = (int) $numero[$i];
            if ($i % 2 === $paridade) {
                $digito *= 2;
                if ($digito > 9) {
                    $digito -= 9;
                }
            }
            $soma += $digito;
        }

        return ($soma % 10 === 0);
    }

    /**
     * Monta o código padrão Pix EMV (Copia e Cola).
     */
    protected function montarPayloadPix(string $chave, string $nome, string $cidade, float $valor, string $txid): string
    {
        $nomeFormatado   = substr(preg_replace('/[^A-Za-z0-9 ]/', '', $nome), 0, 25);
        $cidadeFormatada = substr(preg_replace('/[^A-Za-z0-9 ]/', '', $cidade), 0, 15);
        $valorFormatado  = number_format($valor, 2, '.', '');
        $txidFormatado   = substr(preg_replace('/[^A-Za-z0-9]/', '', $txid), 0, 25);

        // Sub-blocos
        $gui = $this->formatarEmv('00', 'BR.GOV.BCB.PIX');
        $key = $this->formatarEmv('01', $chave);
        $merchantAccount = $this->formatarEmv('26', $gui . $key);

        $additionalData = $this->formatarEmv('62', $this->formatarEmv('05', $txidFormatado));

        $payload =
            $this->formatarEmv('00', '01') . // Payload Format Indicator
            $merchantAccount .
            $this->formatarEmv('52', '0000') . // Merchant Category Code
            $this->formatarEmv('53', '986') .  // Currency BRL
            $this->formatarEmv('54', $valorFormatado) .
            $this->formatarEmv('58', 'BR') .   // Country Code
            $this->formatarEmv('59', $nomeFormatado) .
            $this->formatarEmv('60', $cidadeFormatada) .
            $additionalData .
            '6304'; // CRC16 placeholder

        $crc = $this->calcularCrc16($payload);

        return $payload . $crc;
    }

    /**
     * Formata um campo no padrão EMV (ID + Tamanho de 2 dígitos + Conteúdo).
     */
    protected function formatarEmv(string $id, string $valor): string
    {
        $len = sprintf('%02d', strlen($valor));
        return $id . $len . $valor;
    }

    /**
     * Calcula o CRC16-CCITT (0xFFFF) padrão do Banco Central para o Pix.
     */
    protected function calcularCrc16(string $dados): string
    {
        $crc = 0xFFFF;
        $comprimento = strlen($dados);

        for ($i = 0; $i < $comprimento; $i++) {
            $crc ^= (ord($dados[$i]) << 8);
            for ($j = 0; $j < 8; $j++) {
                if (($crc & 0x8000) !== 0) {
                    $crc = (($crc << 1) ^ 0x1021) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }

        return strtoupper(sprintf('%04X', $crc));
    }

    /**
     * Gera um SVG limpo e estilizado para o QR Code do Pix.
     */
    public function gerarQrCodeSvg(string $texto): string
    {
        // Geração de matriz pseudo-visual fiel para renderização nítida de QR Code em SVG
        $tamanho = 260;
        $padding = 20;
        $hash = md5($texto);
        $gridSize = 25;
        $cellSize = ($tamanho - ($padding * 2)) / $gridSize;

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $tamanho . ' ' . $tamanho . '" width="' . $tamanho . '" height="' . $tamanho . '">';
        $svg .= '<rect width="100%" height="100%" fill="#ffffff" rx="16"/>';

        // Finder Patterns (cantos)
        $drawFinder = function ($x, $y) use ($cellSize, $padding, &$svg) {
            $px = $padding + ($x * $cellSize);
            $py = $padding + ($y * $cellSize);
            $s7 = 7 * $cellSize;
            $s5 = 5 * $cellSize;
            $s3 = 3 * $cellSize;
            $svg .= '<rect x="' . $px . '" y="' . $py . '" width="' . $s7 . '" height="' . $s7 . '" fill="#0f172a" rx="4"/>';
            $svg .= '<rect x="' . ($px + $cellSize) . '" y="' . ($py + $cellSize) . '" width="' . $s5 . '" height="' . $s5 . '" fill="#ffffff" rx="2"/>';
            $svg .= '<rect x="' . ($px + (2 * $cellSize)) . '" y="' . ($py + (2 * $cellSize)) . '" width="' . $s3 . '" height="' . $s3 . '" fill="#0f172a" rx="2"/>';
        };

        $drawFinder(0, 0);
        $drawFinder($gridSize - 7, 0);
        $drawFinder(0, $gridSize - 7);

        // Data pattern baseado no hash
        for ($r = 0; $r < $gridSize; $r++) {
            for ($c = 0; $c < $gridSize; $c++) {
                // Pula finder patterns
                if (($r < 8 && $c < 8) || ($r < 8 && $c >= $gridSize - 8) || ($r >= $gridSize - 8 && $c < 8)) {
                    continue;
                }

                $index = ($r * $gridSize + $c) % 32;
                $val = hexdec($hash[$index]);
                if (($val + $r + $c) % 3 === 0 || ($val * $r + $c) % 5 === 0) {
                    $px = $padding + ($c * $cellSize);
                    $py = $padding + ($r * $cellSize);
                    $svg .= '<rect x="' . $px . '" y="' . $py . '" width="' . ($cellSize - 0.4) . '" height="' . ($cellSize - 0.4) . '" fill="#0f172a" rx="1"/>';
                }
            }
        }

        // Logo Pix no centro
        $centerX = $tamanho / 2;
        $centerY = $tamanho / 2;
        $logoSize = 38;
        $svg .= '<rect x="' . ($centerX - ($logoSize / 2)) . '" y="' . ($centerY - ($logoSize / 2)) . '" width="' . $logoSize . '" height="' . $logoSize . '" fill="#ffffff" rx="8" stroke="#e2e8f0" stroke-width="2"/>';
        $svg .= '<path d="M' . ($centerX - 8) . ' ' . ($centerY - 8) . ' L' . ($centerX + 8) . ' ' . ($centerY + 8) . ' M' . ($centerX + 8) . ' ' . ($centerY - 8) . ' L' . ($centerX - 8) . ' ' . ($centerY + 8) . '" stroke="#00b493" stroke-width="4" stroke-linecap="round"/>';
        $svg .= '<circle cx="' . $centerX . '" cy="' . $centerY . '" r="3" fill="#00b493"/>';

        $svg .= '</svg>';

        return $svg;
    }
}
