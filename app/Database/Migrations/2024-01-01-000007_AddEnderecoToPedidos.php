<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEnderecoToPedidos extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pedidos', [
            'cep'         => ['type' => 'VARCHAR', 'constraint' => 9,   'null' => true, 'default' => null, 'after' => 'status'],
            'logradouro'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'default' => null, 'after' => 'cep'],
            'numero'      => ['type' => 'VARCHAR', 'constraint' => 20,  'null' => true, 'default' => null, 'after' => 'logradouro'],
            'complemento' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null, 'after' => 'numero'],
            'bairro'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null, 'after' => 'complemento'],
            'cidade'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null, 'after' => 'bairro'],
            'uf'          => ['type' => 'CHAR',    'constraint' => 2,   'null' => true, 'default' => null, 'after' => 'cidade'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pedidos', ['cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'uf']);
    }
}
