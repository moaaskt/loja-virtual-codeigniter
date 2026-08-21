<?php

// app/Helpers/status_helper.php

if (!function_exists('getStatusColorClass')) {
    /**
     * Retorna a classe de cor do Bootstrap baseada no status do pedido.
     *
     * @param string $status
     * @return string
     */
    function getStatusColorClass(string $status): string
    {
        switch (strtolower($status)) {
            case 'pendente':
                return 'bg-warning text-dark';
            case 'pago':
                return 'bg-success';
            case 'processando':
                return 'bg-primary';
            case 'enviado':
                return 'bg-info text-dark';
            case 'entregue':
                return 'bg-success';
            case 'cancelado':
                return 'bg-danger';
            default:
                return 'bg-secondary';
        }
    }
}

if (!function_exists('getPagamentoStatusColorClass')) {
    /**
     * Retorna a classe de cor do Bootstrap para o status do pagamento.
     *
     * @param string $status
     * @return string
     */
    function getPagamentoStatusColorClass(string $status): string
    {
        switch (strtolower($status)) {
            case 'pago':
            case 'aprovado':
                return 'bg-success';
            case 'pendente':
                return 'bg-warning text-dark';
            case 'falhou':
            case 'recusado':
                return 'bg-danger';
            case 'estornado':
                return 'bg-dark';
            case 'cancelado':
            default:
                return 'bg-secondary';
        }
    }
}

if (!function_exists('getMetodoPagamentoLabel')) {
    /**
     * Retorna o nome amigável do método de pagamento.
     *
     * @param string|null $metodo
     * @return string
     */
    function getMetodoPagamentoLabel(?string $metodo): string
    {
        switch (strtolower($metodo ?? '')) {
            case 'pix':
                return 'Pix';
            case 'cartao_credito':
                return 'Cartão de Crédito';
            default:
                return !empty($metodo) ? ucfirst($metodo) : 'Não informado';
        }
    }
}

if (!function_exists('renderEstrelas')) {
    /**
     * Renderiza o HTML com estrelas (Bootstrap Icons) correspondente a uma nota de 0 a 5.
     *
     * @param float  $nota
     * @param string $size ('sm', 'md', 'lg')
     * @param bool   $showNumber
     * @return string
     */
    function renderEstrelas(float $nota, string $size = 'sm', bool $showNumber = false): string
    {
        $fontSize = match ($size) {
            'lg' => '1.25rem',
            'md' => '1rem',
            'xs' => '0.75rem',
            default => '0.875rem',
        };

        $html = '<span class="d-inline-flex align-items-center gap-1 text-warning" style="font-size:' . $fontSize . ';" title="' . number_format($nota, 1, ',', '.') . ' de 5 estrelas">';
        
        $notaArredondada = round($nota * 2) / 2; // Arredonda para o 0.5 mais próximo
        for ($i = 1; $i <= 5; $i++) {
            if ($notaArredondada >= $i) {
                $html .= '<i class="bi bi-star-fill"></i>';
            } elseif ($notaArredondada >= ($i - 0.5)) {
                $html .= '<i class="bi bi-star-half"></i>';
            } else {
                $html .= '<i class="bi bi-star text-muted opacity-50"></i>';
            }
        }

        if ($showNumber) {
            $html .= '<span class="ms-1 fw-bold text-dark" style="font-size:0.9em;">' . number_format($nota, 1, ',', '.') . '</span>';
        }

        $html .= '</span>';
        return $html;
    }
}

if (!function_exists('getBadgeStatusAvaliacao')) {
    /**
     * Retorna badge HTML para o status de uma avaliação.
     *
     * @param string $status
     * @return string
     */
    function getBadgeStatusAvaliacao(string $status): string
    {
        return match (strtolower($status)) {
            'aprovada'  => '<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check-circle me-1"></i>Aprovada</span>',
            'rejeitada' => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-x-circle me-1"></i>Rejeitada</span>',
            default     => '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle"><i class="bi bi-hourglass-split me-1"></i>Pendente</span>',
        };
    }
}

if (!function_exists('getTimelineEtapas')) {
    /**
     * Retorna a estrutura da Timeline de 5 etapas para um pedido.
     *
     * @param string      $status
     * @param string|null $statusPagamento
     * @return array
     */
    function getTimelineEtapas(string $status, ?string $statusPagamento = null): array
    {
        $statusNorm = strtolower(trim($status));
        $statusPagNorm = strtolower(trim($statusPagamento ?? ''));

        if (in_array($statusNorm, ['cancelado', 'falhou', 'recusado', 'reembolsado'])) {
            return [
                'is_cancelado' => true,
                'etapa_atual'  => -1,
                'status_label' => 'Pedido Cancelado',
                'badge_class'  => 'bg-danger text-white',
                'etapas'       => [
                    ['num' => 1, 'titulo' => 'Pedido Realizado', 'subtitulo' => 'Registrado no sistema', 'icone' => 'bi-receipt', 'concluido' => true, 'erro' => false],
                    ['num' => 2, 'titulo' => 'Pagamento', 'subtitulo' => 'Não autorizado ou cancelado', 'icone' => 'bi-credit-card-2-front', 'concluido' => false, 'erro' => true],
                    ['num' => 3, 'titulo' => 'Cancelado', 'subtitulo' => 'Pedido finalizado', 'icone' => 'bi-x-circle-fill', 'concluido' => true, 'erro' => true],
                ]
            ];
        }

        $etapa = 1; // 1: Pedido Realizado
        if (in_array($statusNorm, ['pago', 'aprovado']) || in_array($statusPagNorm, ['pago', 'aprovado'])) {
            $etapa = 2; // Pagamento Aprovado
        }
        if (in_array($statusNorm, ['em_separacao', 'separando', 'preparando', 'processando'])) {
            $etapa = 3; // Em Separação
        }
        if (in_array($statusNorm, ['enviado', 'em_transporte', 'a_caminho'])) {
            $etapa = 4; // Enviado
        }
        if (in_array($statusNorm, ['entregue', 'concluido', 'finalizado'])) {
            $etapa = 5; // Entregue
        }

        $etapasConfig = [
            ['num' => 1, 'titulo' => 'Pedido Realizado', 'subtitulo' => 'Aguardando pagamento', 'icone' => 'bi-bag-check-fill'],
            ['num' => 2, 'titulo' => 'Pagamento Confirmado', 'subtitulo' => 'Aprovado com sucesso', 'icone' => 'bi-credit-card-2-front-fill'],
            ['num' => 3, 'titulo' => 'Em Separação', 'subtitulo' => 'Separando no estoque', 'icone' => 'bi-box-seam-fill'],
            ['num' => 4, 'titulo' => 'Enviado', 'subtitulo' => 'A caminho do endereço', 'icone' => 'bi-truck'],
            ['num' => 5, 'titulo' => 'Entregue', 'subtitulo' => 'Recebido pelo cliente', 'icone' => 'bi-house-heart-fill'],
        ];

        foreach ($etapasConfig as &$item) {
            $item['concluido'] = $item['num'] <= $etapa;
            $item['ativo']     = $item['num'] === $etapa;
            $item['erro']      = false;
        }

        return [
            'is_cancelado' => false,
            'etapa_atual'  => $etapa,
            'status_label' => $etapasConfig[$etapa - 1]['titulo'] ?? ucfirst($status),
            'badge_class'  => getStatusColorClass($status),
            'etapas'       => $etapasConfig
        ];
    }
}