<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAvaliacoesTable extends Migration
{
    public function up(): void
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
            'usuario_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'pedido_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
            ],
            'nota' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
            ],
            'titulo' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'default'    => null,
            ],
            'comentario' => [
                'type' => 'TEXT',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pendente', 'aprovada', 'rejeitada'],
                'default'    => 'pendente',
            ],
            'compra_verificada' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('produto_id');
        $this->forge->addKey('usuario_id');
        $this->forge->addKey('pedido_id');
        $this->forge->addKey('status');
        $this->forge->addKey('compra_verificada');
        $this->forge->addKey('created_at');

        // Chaves estrangeiras com integridade referencial
        $this->forge->addForeignKey('produto_id', 'produtos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('usuario_id', 'usuarios', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('pedido_id', 'pedidos', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('avaliacoes', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('avaliacoes', true);
    }
}
