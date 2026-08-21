<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateClienteEnderecosTable extends Migration
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
            'titulo' => [
                'type'       => 'VARCHAR',
                'constraint' => 60,
                'default'    => 'Meu Endereço',
            ],
            'destinatario' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
                'null'       => true,
            ],
            'cep' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'logradouro' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'numero' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'complemento' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'bairro' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'cidade' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'uf' => [
                'type'       => 'VARCHAR',
                'constraint' => 2,
            ],
            'padrao' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'criado_em' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'atualizado_em' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('usuario_id');
        $this->forge->addForeignKey('usuario_id', 'usuarios', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('cliente_enderecos', true);
    }

    public function down()
    {
        $this->forge->dropTable('cliente_enderecos', true);
    }
}
