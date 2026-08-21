<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateProdutoVariacoesMultiAtributos extends Migration
{
    public function up()
    {
        $fields = [
            'sku' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'produto_id',
            ],
            'nome_variacao' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
                'after'      => 'sku',
            ],
            'atributos_json' => [
                'type'       => 'TEXT',
                'null'       => true,
                'default'    => null,
                'after'      => 'nome_variacao',
            ],
            'imagem_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
                'after'      => 'cor_hex',
            ],
            'codigo_barras' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null,
                'after'      => 'imagem_url',
            ],
        ];

        $this->forge->addColumn('produto_variacoes', $fields);
    }

    public function down()
    {
        try {
            $this->forge->dropColumn('produto_variacoes', 'sku');
            $this->forge->dropColumn('produto_variacoes', 'nome_variacao');
            $this->forge->dropColumn('produto_variacoes', 'atributos_json');
            $this->forge->dropColumn('produto_variacoes', 'imagem_url');
            $this->forge->dropColumn('produto_variacoes', 'codigo_barras');
        } catch (\Throwable $e) {
            // Ignora erro caso as colunas não existam durante o rollback
        }
    }
}
