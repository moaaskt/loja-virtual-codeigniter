<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSoftDeleteToProdutos extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('deleted_at', 'produtos')) {
            $this->forge->addColumn('produtos', [
                'deleted_at' => [
                    'type'    => 'DATETIME',
                    'null'    => true,
                    'default' => null,
                    'after'   => 'atualizado_em',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('deleted_at', 'produtos')) {
            $this->forge->dropColumn('produtos', 'deleted_at');
        }
    }
}
