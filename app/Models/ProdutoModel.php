<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdutoModel extends Model
{
    protected $table            = 'produtos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nome', 'descricao', 'preco', 'estoque', 'imagem', 'categoria_id'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';

    // Validation
    protected $validationRules      = [
        'nome'         => 'required|min_length[3]|max_length[255]',
        'preco'        => 'required|decimal',
        'estoque'      => 'required|integer',
        'categoria_id' => 'required|is_natural_no_zero'
    ];
    protected $validationMessages   = [
        'nome' => [
            'required' => 'O campo Nome do Produto é obrigatório.',
        ],
        'preco' => [
            'required' => 'O campo Preço é obrigatório.',
            'decimal'  => 'Por favor, insira um valor de preço válido.'
        ],
        'estoque' => [
            'required' => 'O campo Estoque é obrigatório.',
            'integer'  => 'Por favor, insira um número inteiro para o estoque.'
        ],
        'categoria_id' => [
            'required'            => 'Você precisa selecionar uma categoria.',
            'is_natural_no_zero'  => 'Você precisa selecionar uma categoria válida.'
        ]
    ];




    
// método para obter produtos por categoria
 public function getProdutosPorCategoria($categoriaId, $perPage = 12)
{
    $this->select('produtos.*, categorias.nome as categoria_nome');
    $this->join('categorias', 'categorias.id = produtos.categoria_id');
    $this->where('produtos.categoria_id', $categoriaId);

    return $this->paginate($perPage);
}





 // Método para buscar produtos com suas categorias
public function searchProdutosComCategoria($termo, $perPage = 10)
{
    // Se o termo de busca estiver vazio, retorna todos os produtos paginados
    if (empty($termo)) {
        return $this->getProdutosComCategoria($perPage);
    }

    // Se houver um termo, faz a busca com LIKE
    $this->select('produtos.*, categorias.nome as categoria_nome');
    $this->join('categorias', 'categorias.id = produtos.categoria_id');

    $this->like('produtos.nome', $termo);
    $this->orLike('produtos.descricao', $termo);
    $this->orLike('categorias.nome', $termo);

    return $this->paginate($perPage);
}




// Método para encontrar um produto específico com sua categoria
public function findProdutoComCategoria($id)
{
    $this->select('produtos.*, categorias.nome as categoria_nome');
    $this->join('categorias', 'categorias.id = produtos.categoria_id');
    $this->where('produtos.id', $id);

    // Retorna apenas o primeiro (e único) resultado
    return $this->first(); 
}



// Método para obter todos os produtos com suas respectivas categorias
public function getProdutosComCategoria($perPage = 10)
{
    $this->select('produtos.*, categorias.nome as categoria_nome');
    $this->join('categorias', 'categorias.id = produtos.categoria_id');

    return $this->paginate($perPage);
}


}