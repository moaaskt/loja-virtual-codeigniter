<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFreteCupomToPedidos extends Migration
{
    public function up()
    {
        $columns = [
            'cupom_codigo'     => ['type' => 'VARCHAR', 'constraint' => 50,   'null' => true, 'default' => null, 'after' => 'valor_total'],
            'desconto_valor'   => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => false, 'default' => 0.00, 'after' => 'cupom_codigo'],
            'frete_modalidade' => ['type' => 'VARCHAR', 'constraint' => 50,   'null' => true, 'default' => null, 'after' => 'desconto_valor'],
            'frete_valor'      => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => false, 'default' => 0.00, 'after' => 'frete_modalidade'],
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
    }

    public function down()
    {
        $columnNames = ['cupom_codigo', 'desconto_valor', 'frete_modalidade', 'frete_valor'];
        $toDrop = [];
        foreach ($columnNames as $name) {
            if ($this->db->fieldExists($name, 'pedidos')) {
                $toDrop[] = $name;
            }
        }

        if (!empty($toDrop)) {
            $this->forge->dropColumn('pedidos', $toDrop);
        }
    }
}
