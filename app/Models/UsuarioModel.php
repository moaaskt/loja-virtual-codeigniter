<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'usuarios';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    // Esta linha resolve o Erro #2
    protected $allowedFields    = ['nome', 'email', 'senha_hash'];

    // Dates
    protected $useTimestamps    = true;
    protected $createdField     = 'criado_em';
    protected $updatedField     = '';

    // Validation
    protected $validationRules      = [
        'nome'             => 'required|min_length[3]|max_length[128]',
        'email'            => 'required|valid_email|is_unique[usuarios.email,id,{id}]',
        'senha_hash'       => 'required|min_length[6]',
        'password_confirm' => 'required_with[senha_hash]|matches[senha_hash]',
    ];
    protected $validationMessages   = [
        'email' => [
            'is_unique' => 'Desculpe, este email já está em uso.'
        ],
        'password_confirm' => [
            'matches' => 'As senhas não conferem.'
        ]
    ];

    // Callbacks
    protected $beforeInsert   = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['senha_hash'])) {
            $data['data']['senha_hash'] = password_hash($data['data']['senha_hash'], PASSWORD_DEFAULT);
        }
        return $data;
    }
}