<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPagamentoToPedidos extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pedidos', [
            'forma_pagamento'  => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'default'    => null,
                'after'      => 'frete_valor',
            ],
            'status_pagamento' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => false,
                'default'    => 'pendente',
                'after'      => 'forma_pagamento',
            ],
        ]);

        // Atualiza a coluna status para incluir 'pago'
        $this->forge->modifyColumn('pedidos', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pendente', 'pago', 'processando', 'enviado', 'entregue', 'cancelado'],
                'default'    => 'pendente',
                'null'       => false,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pedidos', ['forma_pagamento', 'status_pagamento']);
        $this->forge->modifyColumn('pedidos', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pendente', 'processando', 'enviado', 'entregue', 'cancelado'],
                'default'    => 'pendente',
                'null'       => false,
            ],
        ]);
    }
}
