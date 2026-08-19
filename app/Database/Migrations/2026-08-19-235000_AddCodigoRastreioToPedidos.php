<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCodigoRastreioToPedidos extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('pedidos', [
            'codigo_rastreio' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null,
                'after'      => 'status_pagamento',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('pedidos', 'codigo_rastreio');
    }
}
