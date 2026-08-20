<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateProdutoVariacoesGenericas extends Migration
{
    public function up()
    {
        // Altera 'cor' e 'tamanho' para aceitarem nulo e tamanho ampliado
        $this->forge->modifyColumn('produto_variacoes', [
            'tamanho' => [
                'name'       => 'tamanho',
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
            ],
            'cor' => [
                'name'       => 'cor',
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
            ],
        ]);

        // Adiciona a coluna 'preco'
        $this->forge->addColumn('produto_variacoes', [
            'preco' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => null,
                'after'      => 'cor',
            ],
        ]);
    }

    public function down()
    {
        try {
            $this->forge->dropColumn('produto_variacoes', 'preco');
        } catch (\Throwable $e) {
            // Ignora erro caso a coluna não exista durante o rollback
        }
    }
}
