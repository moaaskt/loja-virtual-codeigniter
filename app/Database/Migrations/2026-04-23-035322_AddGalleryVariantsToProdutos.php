<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGalleryVariantsToProdutos extends Migration
{
    public function up()
    {
        $columns = [
            'imagens_galeria' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'imagem',
            ],
            'cores' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'imagens_galeria',
            ],
            'tamanhos' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'cores',
            ],
            'frete_gratis' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'tamanhos',
            ],
        ];

        $toAdd = [];
        foreach ($columns as $name => $def) {
            if (!$this->db->fieldExists($name, 'produtos')) {
                $toAdd[$name] = $def;
            }
        }

        if (!empty($toAdd)) {
            $this->forge->addColumn('produtos', $toAdd);
        }
    }

    public function down()
    {
        $columnNames = ['imagens_galeria', 'cores', 'tamanhos', 'frete_gratis'];
        $toDrop = [];
        foreach ($columnNames as $name) {
            if ($this->db->fieldExists($name, 'produtos')) {
                $toDrop[] = $name;
            }
        }

        if (!empty($toDrop)) {
            $this->forge->dropColumn('produtos', $toDrop);
        }
    }
}
