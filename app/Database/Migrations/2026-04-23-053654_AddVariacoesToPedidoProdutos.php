<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVariacoesToPedidoProdutos extends Migration
{
    public function up()
    {
        $columns = [
            'variacao_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'produto_id'],
            'tamanho'     => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'variacao_id'],
            'cor'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'tamanho'],
        ];

        $toAdd = [];
        foreach ($columns as $name => $def) {
            if (!$this->db->fieldExists($name, 'pedido_produtos')) {
                $toAdd[$name] = $def;
            }
        }

        if (!empty($toAdd)) {
            $this->forge->addColumn('pedido_produtos', $toAdd);
        }

        // Adiciona foreign key somente se a coluna variacao_id foi criada agora
        if (isset($toAdd['variacao_id'])) {
            try {
                $this->forge->addForeignKey('variacao_id', 'produto_variacoes', 'id', 'SET NULL', 'CASCADE', 'fk_pedido_produtos_variacao_id');
            } catch (\Throwable $e) {
                // Ignora se a FK já existir
            }
        }
    }

    public function down()
    {
        try {
            $this->forge->dropForeignKey('pedido_produtos', 'fk_pedido_produtos_variacao_id');
        } catch (\Throwable $e) {
            // Ignore if foreign key was not created with this explicit name
        }

        $columnNames = ['variacao_id', 'tamanho', 'cor'];
        $toDrop = [];
        foreach ($columnNames as $name) {
            if ($this->db->fieldExists($name, 'pedido_produtos')) {
                $toDrop[] = $name;
            }
        }

        if (!empty($toDrop)) {
            $this->forge->dropColumn('pedido_produtos', $toDrop);
        }
    }
}
