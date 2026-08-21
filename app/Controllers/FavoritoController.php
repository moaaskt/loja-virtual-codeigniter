<?php

namespace App\Controllers;

use App\Models\ClienteFavoritoModel;
use App\Models\ProdutoModel;

class FavoritoController extends BaseController
{
    protected ClienteFavoritoModel $favoritoModel;
    protected ProdutoModel $produtoModel;

    public function __construct()
    {
        $this->favoritoModel = new ClienteFavoritoModel();
        $this->produtoModel  = new ProdutoModel();
    }

    /**
     * Alterna status de favorito para um produto (AJAX).
     */
    public function toggle()
    {
        $usuarioId = (int) (session()->get('usuario_id') ?? $_SESSION['usuario_id'] ?? 0);
        if (!$usuarioId) {
            return $this->response->setStatusCode(401)->setJSON([
                'ok'            => false,
                'auth_required' => true,
                'erro'          => 'Faça login para salvar produtos na sua Lista de Desejos.',
            ]);
        }

        $produtoId = (int) ($this->request->getPost('produto_id') ?? $_POST['produto_id'] ?? $this->request->getJSON(true)['produto_id'] ?? 0);
        if ($produtoId <= 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'ok'   => false,
                'erro' => 'ID do produto inválido.',
            ]);
        }

        $produto = $this->produtoModel->find($produtoId);
        if (!$produto) {
            return $this->response->setStatusCode(404)->setJSON([
                'ok'   => false,
                'erro' => 'Produto não encontrado.',
            ]);
        }

        $resultado = $this->favoritoModel->toggleFavorito($usuarioId, $produtoId);
        $resultado['mensagem'] = $resultado['adicionado']
            ? 'Produto adicionado aos seus Favoritos!'
            : 'Produto removido dos seus Favoritos.';

        return $this->response->setStatusCode(200)->setJSON($resultado);
    }

    /**
     * Retorna a lista de IDs favoritados pelo cliente logado.
     */
    public function ids()
    {
        $usuarioId = (int) (session()->get('usuario_id') ?? $_SESSION['usuario_id'] ?? 0);
        if (!$usuarioId) {
            return $this->response->setStatusCode(200)->setJSON([
                'ok'    => true,
                'ids'   => [],
                'total' => 0,
            ]);
        }

        $ids = $this->favoritoModel->getIdsFavoritosPorUsuario($usuarioId);
        return $this->response->setStatusCode(200)->setJSON([
            'ok'    => true,
            'ids'   => $ids,
            'total' => count($ids),
        ]);
    }
}
