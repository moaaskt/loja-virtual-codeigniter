<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdutoModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'produtos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $deletedField     = 'deleted_at';
    protected $protectFields    = true;
    protected $allowedFields    = ['nome', 'descricao', 'preco', 'estoque', 'imagem', 'imagens_galeria', 'cores', 'tamanhos', 'frete_gratis', 'categoria_id'];

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



// Decrementa estoque dentro de uma transação aberta pelo chamador (usa FOR UPDATE para evitar race condition)
public function decrementarEstoque(int $produtoId, int $quantidade, \CodeIgniter\Database\BaseConnection $db): bool
{
    $row = $db->query(
        'SELECT estoque FROM produtos WHERE id = ? FOR UPDATE',
        [$produtoId]
    )->getRowArray();

    if ($row === null || $row['estoque'] < $quantidade) {
        return false;
    }

    $db->query(
        'UPDATE produtos SET estoque = estoque - ? WHERE id = ?',
        [$quantidade, $produtoId]
    );

    return true;
}

// Busca produtos relacionados (mesma categoria, excluindo o produto atual)
public function getRelacionados(int $categoriaId, int $excluirProdutoId, int $limit = 4): array
{
    return $this->select('produtos.*, categorias.nome as categoria_nome')
        ->join('categorias', 'categorias.id = produtos.categoria_id')
        ->where('produtos.categoria_id', $categoriaId)
        ->where('produtos.id !=', $excluirProdutoId)
        ->limit($limit)
        ->find();
}

// Método para obter todos os produtos com suas respectivas categorias
public function getProdutosComCategoria($perPage = 10)
{
    $this->select('produtos.*, categorias.nome as categoria_nome');
    $this->join('categorias', 'categorias.id = produtos.categoria_id');

    return $this->paginate($perPage);
}


    // Retorna as imagens da galeria do produto
    public function getImagens(int $produtoId): array
    {
        return $this->db->table('produto_imagens')
            ->where('produto_id', $produtoId)
            ->get()
            ->getResultArray();
    }

    // Retorna as variações (SKUs) do produto
    public function getVariacoes(int $produtoId): array
    {
        return $this->db->table('produto_variacoes')
            ->where('produto_id', $produtoId)
            ->get()
            ->getResultArray();
    }

    /**
     * Busca produtos com filtros combinados:
     *   - termo    : busca textual no nome/descrição/categoria
     *   - categorias: array de IDs de categorias (OR entre elas)
     *   - preco_min / preco_max : faixa de preços
     *   - marcas   : array de nomes de marcas (busca em nome/descricao)
     *   - generos  : array de gêneros (busca em nome/descricao)
     *
     * Retorna resultados paginados. Acesse $this->pager após a chamada.
     */
    public function getProdutosFiltrados(array $filtros = [], int $perPage = 12): array
    {
        $this->select('produtos.*, categorias.nome as categoria_nome');
        $this->join('categorias', 'categorias.id = produtos.categoria_id');

        // --- Filtro de TERMO LIVRE ---
        $termo = $filtros['termo'] ?? '';
        if (!empty($termo)) {
            $this->groupStart();
            $this->like('produtos.nome', $termo);
            $this->orLike('produtos.descricao', $termo);
            $this->orLike('categorias.nome', $termo);
            $this->groupEnd();
        }

        // --- Filtro de CATEGORIAS (OR entre as selecionadas) ---
        $categorias = $filtros['categorias'] ?? [];
        // Remove o valor vazio ("Todas") que pode vir no array
        $categorias = array_filter(array_map('intval', (array) $categorias));
        if (!empty($categorias)) {
            $this->whereIn('produtos.categoria_id', $categorias);
        }

        // --- Filtro de FAIXA DE PREÇO ---
        $precoMin = isset($filtros['preco_min']) && $filtros['preco_min'] !== '' ? (float) $filtros['preco_min'] : null;
        $precoMax = isset($filtros['preco_max']) && $filtros['preco_max'] !== '' ? (float) $filtros['preco_max'] : null;
        if ($precoMin !== null) {
            $this->where('produtos.preco >=', $precoMin);
        }
        if ($precoMax !== null) {
            $this->where('produtos.preco <=', $precoMax);
        }

        // --- Filtro de MARCAS (busca textual em nome + descricao) ---
        $marcas = array_filter((array) ($filtros['marcas'] ?? []));
        if (!empty($marcas)) {
            $this->groupStart();
            foreach ($marcas as $i => $marca) {
                if ($i === 0) {
                    $this->groupStart();
                    $this->like('produtos.nome', $marca);
                    $this->orLike('produtos.descricao', $marca);
                    $this->groupEnd();
                } else {
                    $this->orGroupStart();
                    $this->like('produtos.nome', $marca);
                    $this->orLike('produtos.descricao', $marca);
                    $this->groupEnd();
                }
            }
            $this->groupEnd();
        }

        // --- Filtro de GÊNERO (busca textual em nome + descricao) ---
        $generos = array_filter((array) ($filtros['generos'] ?? []));
        if (!empty($generos)) {
            $this->groupStart();
            foreach ($generos as $i => $genero) {
                if ($i === 0) {
                    $this->groupStart();
                    $this->like('produtos.nome', $genero);
                    $this->orLike('produtos.descricao', $genero);
                    $this->groupEnd();
                } else {
                    $this->orGroupStart();
                    $this->like('produtos.nome', $genero);
                    $this->orLike('produtos.descricao', $genero);
                    $this->groupEnd();
                }
            }
            $this->groupEnd();
        }

        return $this->paginate($perPage);
    }

}