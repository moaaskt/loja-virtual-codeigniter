<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAtivoToUsuarios extends Migration
{
    public function up()
    {
        $this->forge->addColumn('usuarios', [
            'ativo' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'after'      => 'role',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('usuarios', 'ativo');
    }
}
