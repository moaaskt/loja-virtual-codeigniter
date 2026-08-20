<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCorHexToProdutoVariacoes extends Migration
{
    public function up()
    {
        $this->forge->addColumn('produto_variacoes', [
            'cor_hex' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'default'    => null,
                'after'      => 'cor',
            ],
        ]);
    }

    public function down()
    {
        try {
            $this->forge->dropColumn('produto_variacoes', 'cor_hex');
        } catch (\Throwable $e) {
            // Ignora erro caso a coluna não exista durante o rollback
        }
    }
}
