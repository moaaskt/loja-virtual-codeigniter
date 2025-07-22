<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'usuarios';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['nome', 'email', 'senha_hash'];
    protected $beforeInsert     = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['senha_hash'])) {
            $data['data']['senha_hash'] = password_hash($data['data']['senha_hash'], PASSWORD_DEFAULT);
        }
        return $data;
    }
}