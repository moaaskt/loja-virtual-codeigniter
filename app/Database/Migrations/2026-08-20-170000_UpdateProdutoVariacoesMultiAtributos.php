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

        $toAdd = [];
        foreach ($fields as $name => $def) {
            if (!$this->db->fieldExists($name, 'produto_variacoes')) {
                $toAdd[$name] = $def;
            }
        }

        if (!empty($toAdd)) {
            $this->forge->addColumn('produto_variacoes', $toAdd);
        }
    }

    public function down()
    {
        $columnNames = ['sku', 'nome_variacao', 'atributos_json', 'imagem_url', 'codigo_barras'];
        foreach ($columnNames as $name) {
            if ($this->db->fieldExists($name, 'produto_variacoes')) {
                $this->forge->dropColumn('produto_variacoes', $name);
            }
        }
    }
}
