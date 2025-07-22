<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoriaModel extends Model
{
    protected $table            = 'categorias';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nome', 'descricao'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';

    // Validation
    protected $validationRules      = [
        'nome' => 'required|min_length[3]|max_length[100]|is_unique[categorias.nome,id,{id}]'
    ];
    protected $validationMessages   = [
        'nome' => [
            'required' => 'O campo Nome da Categoria é obrigatório.',
            'is_unique' => 'Esta categoria já existe.'
        ]
    ];
}