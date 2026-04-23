<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGalleryVariantsToProdutos extends Migration
{
    public function up()
    {
        $fields = [
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

        $this->forge->addColumn('produtos', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('produtos', ['imagens_galeria', 'cores', 'tamanhos', 'frete_gratis']);
    }
}
