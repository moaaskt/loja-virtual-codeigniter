<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateClienteFavoritosTable extends Migration
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
            'usuario_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'produto_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'criado_em' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['usuario_id', 'produto_id'], false, true); // Unique composite index
        $this->forge->addForeignKey('usuario_id', 'usuarios', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('produto_id', 'produtos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('cliente_favoritos', true);
    }

    public function down()
    {
        $this->forge->dropTable('cliente_favoritos', true);
    }
}
