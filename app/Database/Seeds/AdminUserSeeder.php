<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();
        $this->db->table('usuarios')->where('email', 'admin@admin.com')->delete();
        $this->db->enableForeignKeyChecks();

        $admin = [
            'nome'       => 'Administrador',
            'email'      => 'admin@admin.com',
            'senha_hash' => password_hash('123456', PASSWORD_DEFAULT),
            'role'       => 'admin',
            'ativo'      => 1,
            'criado_em'  => date('Y-m-d H:i:s'),
        ];

        $this->db->table('usuarios')->insert($admin);
    }
}
