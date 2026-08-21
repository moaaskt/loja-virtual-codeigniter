<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteFavoritoModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'cliente_favoritos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['usuario_id', 'produto_id', 'criado_em'];

    // Dates
    protected $useTimestamps = false;

    /**
     * Alterna status de favorito (adiciona se não existir, remove se existir).
     */
    public function toggleFavorito(int $usuarioId, int $produtoId): array
    {
        $existente = $this->where('usuario_id', $usuarioId)
                          ->where('produto_id', $produtoId)
                          ->first();

        if ($existente) {
            $this->where('id', $existente['id'])->delete();
            $adicionado = false;
        } else {
            $this->insert([
                'usuario_id' => $usuarioId,
                'produto_id' => $produtoId,
                'criado_em'  => date('Y-m-d H:i:s'),
            ]);
            $adicionado = true;
        }

        $total = $this->getTotalFavoritos($usuarioId);

        return [
            'ok'         => true,
            'adicionado' => $adicionado,
            'favorito'   => $adicionado,
            'total'      => $total,
        ];
    }

    /**
     * Verifica se o produto está favoritado pelo usuário.
     */
    public function isFavorito(int $usuarioId, int $produtoId): bool
    {
        return (bool) $this->where('usuario_id', $usuarioId)
                           ->where('produto_id', $produtoId)
                           ->countAllResults();
    }

    /**
     * Retorna array simples de IDs dos produtos favoritados pelo usuário.
     */
    public function getIdsFavoritosPorUsuario(int $usuarioId): array
    {
        $registros = $this->select('produto_id')
                          ->where('usuario_id', $usuarioId)
                          ->findAll();

        return array_map('intval', array_column($registros, 'produto_id'));
    }

    /**
     * Retorna lista completa de produtos favoritados pelo usuário com categoria e estoque.
     */
    public function getFavoritosPorUsuario(int $usuarioId): array
    {
        return $this->select('produtos.*, categorias.nome as categoria_nome, cliente_favoritos.criado_em as favoritado_em')
                    ->join('produtos', 'produtos.id = cliente_favoritos.produto_id')
                    ->join('categorias', 'categorias.id = produtos.categoria_id', 'left')
                    ->where('cliente_favoritos.usuario_id', $usuarioId)
                    ->where('produtos.deleted_at IS NULL')
                    ->orderBy('cliente_favoritos.id', 'DESC')
                    ->findAll();
    }

    /**
     * Retorna a contagem total de favoritos do usuário.
     */
    public function getTotalFavoritos(int $usuarioId): int
    {
        return $this->where('usuario_id', $usuarioId)->countAllResults();
    }
}
