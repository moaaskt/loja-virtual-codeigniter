<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCorHexToProdutoVariacoes extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('cor_hex', 'produto_variacoes')) {
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
    }

    public function down()
    {
        if ($this->db->fieldExists('cor_hex', 'produto_variacoes')) {
            $this->forge->dropColumn('produto_variacoes', 'cor_hex');
        }
    }
}
