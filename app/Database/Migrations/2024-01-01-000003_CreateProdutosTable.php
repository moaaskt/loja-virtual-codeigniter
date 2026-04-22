<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProdutosTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nome'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'descricao'     => ['type' => 'TEXT', 'null' => true],
            'preco'         => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => false],
            'estoque'       => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'imagem'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'categoria_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
            'criado_em'     => ['type' => 'DATETIME', 'null' => true],
            'atualizado_em' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('categoria_id', 'categorias', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('produtos');
    }

    public function down()
    {
        $this->forge->dropTable('produtos');
    }
}
