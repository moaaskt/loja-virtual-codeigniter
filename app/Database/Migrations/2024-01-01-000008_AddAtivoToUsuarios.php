<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAtivoToUsuarios extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('ativo', 'usuarios')) {
            $this->forge->addColumn('usuarios', [
                'ativo' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                    'after'      => 'role',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('ativo', 'usuarios')) {
            $this->forge->dropColumn('usuarios', 'ativo');
        }
    }
}
