<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPagamentoToPedidos extends Migration
{
    public function up()
    {
        $columns = [
            'forma_pagamento' => [
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
        ];

        $toAdd = [];
        foreach ($columns as $name => $def) {
            if (!$this->db->fieldExists($name, 'pedidos')) {
                $toAdd[$name] = $def;
            }
        }

        if (!empty($toAdd)) {
            $this->forge->addColumn('pedidos', $toAdd);
        }

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
        $columnNames = ['forma_pagamento', 'status_pagamento'];
        $toDrop = [];
        foreach ($columnNames as $name) {
            if ($this->db->fieldExists($name, 'pedidos')) {
                $toDrop[] = $name;
            }
        }

        if (!empty($toDrop)) {
            $this->forge->dropColumn('pedidos', $toDrop);
        }

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
