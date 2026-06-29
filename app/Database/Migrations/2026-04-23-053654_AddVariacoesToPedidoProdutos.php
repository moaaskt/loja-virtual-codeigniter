<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVariacoesToPedidoProdutos extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pedido_produtos', [
            'variacao_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'produto_id'],
            'tamanho'     => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'variacao_id'],
            'cor'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'tamanho'],
        ]);
        
        $this->forge->addForeignKey('variacao_id', 'produto_variacoes', 'id', 'SET NULL', 'CASCADE', 'fk_pedido_produtos_variacao_id');
    }

    public function down()
    {
        $this->forge->dropForeignKey('pedido_produtos', 'fk_pedido_produtos_variacao_id');
        $this->forge->dropColumn('pedido_produtos', ['variacao_id', 'tamanho', 'cor']);
    }
}
