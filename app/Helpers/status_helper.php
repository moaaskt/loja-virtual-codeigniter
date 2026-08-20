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