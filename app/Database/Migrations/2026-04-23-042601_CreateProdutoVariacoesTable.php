<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProdutoVariacoesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'produto_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tamanho' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'cor' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'estoque' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('produto_id', 'produtos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('produto_variacoes');
    }

    public function down()
    {
        $this->forge->dropTable('produto_variacoes');
    }
}
