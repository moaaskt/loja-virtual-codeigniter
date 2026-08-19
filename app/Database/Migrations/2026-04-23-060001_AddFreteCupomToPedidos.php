<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFreteCupomToPedidos extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pedidos', [
            'cupom_codigo'     => ['type' => 'VARCHAR', 'constraint' => 50,   'null' => true, 'default' => null, 'after' => 'valor_total'],
            'desconto_valor'   => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => false, 'default' => 0.00, 'after' => 'cupom_codigo'],
            'frete_modalidade' => ['type' => 'VARCHAR', 'constraint' => 50,   'null' => true, 'default' => null, 'after' => 'desconto_valor'],
            'frete_valor'      => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => false, 'default' => 0.00, 'after' => 'frete_modalidade'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pedidos', ['cupom_codigo', 'desconto_valor', 'frete_modalidade', 'frete_valor']);
    }
}
