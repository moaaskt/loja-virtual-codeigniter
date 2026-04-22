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
                return 'bg-warning';
            case 'processando':
                return 'bg-primary';
            case 'enviado':
                return 'bg-info';
            case 'entregue':
                return 'bg-success';
            case 'cancelado':
                return 'bg-danger';
            default:
                return 'bg-secondary';
        }
    }
}