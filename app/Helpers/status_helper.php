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
            case 'processando':
                return 'bg-primary'; // Azul
            case 'enviado':
                return 'bg-info';    // Azul claro
            case 'concluido':
                return 'bg-success'; // Verde
            case 'cancelado':
                return 'bg-danger';  // Vermelho
            default:
                return 'bg-secondary'; // Cinza para qualquer outro status
        }
    }
}