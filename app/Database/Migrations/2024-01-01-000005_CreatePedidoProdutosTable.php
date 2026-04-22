<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePedidoProdutosTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'pedido_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
            'produto_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
            'quantidade'      => ['type' => 'INT', 'constraint' => 11, 'null' => false],
            'preco_unitario'  => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => false],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('pedido_id', 'pedidos', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('produto_id', 'produtos', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('pedido_produtos');
    }

    public function down()
    {
        $this->forge->dropTable('pedido_produtos');
    }
}
