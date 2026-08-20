<?php

namespace Tests\Support\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ExampleSeeder extends Seeder
{
    public function run(): void
    {
        $forge = \Config\Database::forge($this->db);
        if (!$this->db->tableExists('factories')) {
            $forge->addField([
                'id'         => ['type' => 'INTEGER', 'auto_increment' => true, 'primary_key' => true],
                'name'       => ['type' => 'VARCHAR', 'constraint' => 31],
                'uid'        => ['type' => 'VARCHAR', 'constraint' => 31],
                'class'      => ['type' => 'VARCHAR', 'constraint' => 63],
                'icon'       => ['type' => 'VARCHAR', 'constraint' => 31],
                'summary'    => ['type' => 'VARCHAR', 'constraint' => 255],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
                'deleted_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $forge->addKey('id', true);
            $forge->createTable('factories', true);
        }

        $factories = [
            [
                'name'    => 'Test Factory',
                'uid'     => 'test001',
                'class'   => 'Factories\Tests\NewFactory',
                'icon'    => 'fas fa-puzzle-piece',
                'summary' => 'Longer sample text for testing',
            ],
            [
                'name'    => 'Widget Factory',
                'uid'     => 'widget',
                'class'   => 'Factories\Tests\WidgetPlant',
                'icon'    => 'fas fa-puzzle-piece',
                'summary' => 'Create widgets in your factory',
            ],
            [
                'name'    => 'Evil Factory',
                'uid'     => 'evil-maker',
                'class'   => 'Factories\Evil\MyFactory',
                'icon'    => 'fas fa-book-dead',
                'summary' => 'Abandon all hope, ye who enter here',
            ],
        ];

        $builder = $this->db->table('factories');
        $builder->truncate();

        foreach ($factories as $factory) {
            $builder->insert($factory);
        }
    }
}
