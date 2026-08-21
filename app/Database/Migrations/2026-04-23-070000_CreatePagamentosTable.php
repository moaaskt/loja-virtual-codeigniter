<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePagamentosTable extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('pagamentos')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'pedido_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'metodo' => [
                    'type'       => 'ENUM',
                    'constraint' => ['pix', 'cartao_credito'],
                    'null'       => false,
                ],
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['pendente', 'pago', 'falhou', 'cancelado', 'estornado'],
                    'default'    => 'pendente',
                    'null'       => false,
                ],
                'valor' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,2',
                    'null'       => false,
                ],
                'transacao_id' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => false,
                ],
                'pix_copiacola' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'pix_qrcode_base64' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'pix_expiracao' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'cartao_ultimos_digitos' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 4,
                    'null'       => true,
                ],
                'cartao_bandeira' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'null'       => true,
                ],
                'cartao_parcelas' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'default'    => 1,
                    'null'       => false,
                ],
                'detalhes_json' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'pago_em' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'criado_em' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'atualizado_em' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);

            $this->forge->addPrimaryKey('id');
            $this->forge->addUniqueKey('transacao_id');
            $this->forge->addForeignKey('pedido_id', 'pedidos', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('pagamentos');
        }
    }

    public function down()
    {
        $this->forge->dropTable('pagamentos', true);
    }
}
