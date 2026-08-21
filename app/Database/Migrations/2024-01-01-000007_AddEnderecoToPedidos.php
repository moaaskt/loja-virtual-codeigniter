<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEnderecoToPedidos extends Migration
{
    public function up()
    {
        $columns = [
            'cep'         => ['type' => 'VARCHAR', 'constraint' => 9,   'null' => true, 'default' => null, 'after' => 'status'],
            'logradouro'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'default' => null, 'after' => 'cep'],
            'numero'      => ['type' => 'VARCHAR', 'constraint' => 20,  'null' => true, 'default' => null, 'after' => 'logradouro'],
            'complemento' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null, 'after' => 'numero'],
            'bairro'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null, 'after' => 'complemento'],
            'cidade'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null, 'after' => 'bairro'],
            'uf'          => ['type' => 'CHAR',    'constraint' => 2,   'null' => true, 'default' => null, 'after' => 'cidade'],
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
        $columnNames = ['cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'uf'];
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
