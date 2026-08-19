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