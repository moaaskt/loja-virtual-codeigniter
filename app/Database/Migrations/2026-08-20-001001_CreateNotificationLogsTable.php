<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationLogsTable extends Migration
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
            'canal' => [
                'type'       => 'ENUM',
                'constraint' => ['email', 'whatsapp'],
                'default'    => 'email',
            ],
            'destinatario' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'evento' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'payload' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pendente', 'enviado', 'falhou'],
                'default'    => 'pendente',
            ],
            'tentativas' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 1,
            ],
            'mensagem_erro' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'enviado_em' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addKey('canal');
        $this->forge->addKey('status');
        $this->forge->addKey('evento');
        $this->forge->addKey('destinatario');
        $this->forge->addKey('created_at');

        $this->forge->createTable('notification_logs', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('notification_logs', true);
    }
}
