<?php

namespace App\Controllers;

use App\Models\AvaliacaoModel;
use App\Models\ProdutoModel;
use App\Services\AuditService;

class AvaliacaoController extends BaseController
{
    protected AvaliacaoModel $avaliacaoModel;
    protected ProdutoModel $produtoModel;

    public function __construct()
    {
        helper(['form', 'url', 'status']);
        $this->avaliacaoModel = new AvaliacaoModel();
        $this->produtoModel   = new ProdutoModel();
    }

    /**
     * Processa a submissão de uma avaliação de produto pelo cliente.
     * Rota: POST /avaliacao/enviar (Protegida por filtro auth)
     */
    public function enviar()
    {
        $usuarioId = (int) session()->get('usuario_id');
        if (empty($usuarioId)) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(401)->setJSON([
                    'ok'       => false,
                    'mensagem' => 'Você precisa estar logado para avaliar este produto.',
                ]);
            }
            return redirect()->to(site_url('login'))->with('erro', 'Faça login para avaliar produtos.');
        }

        $produtoId  = (int) $this->request->getPost('produto_id');
        $nota       = (int) $this->request->getPost('nota');
        $titulo     = trim((string) $this->request->getPost('titulo'));
        $comentario = trim((string) $this->request->getPost('comentario'));

        // Valida existência do produto
        $produto = $this->produtoModel->find($produtoId);
        if (!$produto) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(404)->setJSON([
                    'ok'       => false,
                    'mensagem' => 'Produto não encontrado.',
                ]);
            }
            return redirect()->back()->with('erro', 'Produto não encontrado.');
        }

        // Valida elegibilidade (compra verificada e avaliação prévia)
        $statusPermissao = $this->avaliacaoModel->usuarioPodeAvaliar($usuarioId, $produtoId);

        $dadosAvaliacao = [
            'produto_id'        => $produtoId,
            'usuario_id'        => $usuarioId,
            'pedido_id'         => $statusPermissao['pedido_id'] ?? null,
            'nota'              => $nota,
            'titulo'            => !empty($titulo) ? $titulo : null,
            'comentario'        => $comentario,
            'status'            => 'pendente', // Enviada para moderação
            'compra_verificada' => $statusPermissao['comprou'] ? 1 : 0,
        ];

        // Se já avaliou anteriormente, atualiza a avaliação existente
        if ($statusPermissao['ja_avaliou'] && !empty($statusPermissao['avaliacao_existente']['id'])) {
            $avaliacaoId = (int) $statusPermissao['avaliacao_existente']['id'];
            $dadosAnteriores = $statusPermissao['avaliacao_existente'];

            if (!$this->avaliacaoModel->update($avaliacaoId, $dadosAvaliacao)) {
                $erros = $this->avaliacaoModel->errors();
                $msgErro = !empty($erros) ? implode('<br>', $erros) : 'Erro ao atualizar avaliação.';
                
                if ($this->request->isAJAX()) {
                    return $this->response->setStatusCode(422)->setJSON([
                        'ok'       => false,
                        'mensagem' => $msgErro,
                        'erros'    => $erros,
                    ]);
                }
                return redirect()->back()->withInput()->with('erro', $msgErro);
            }

            // Trilha de auditoria
            AuditService::log(
                'update',
                'avaliacoes',
                $avaliacaoId,
                $dadosAvaliacao,
                $dadosAnteriores,
                $usuarioId
            );

            $mensagemSucesso = 'Sua avaliação foi atualizada com sucesso e enviada para moderação!';
        } else {
            // Nova avaliação
            $avaliacaoId = $this->avaliacaoModel->insert($dadosAvaliacao);
            if (!$avaliacaoId) {
                $erros = $this->avaliacaoModel->errors();
                $msgErro = !empty($erros) ? implode('<br>', $erros) : 'Erro ao registrar avaliação.';

                if ($this->request->isAJAX()) {
                    return $this->response->setStatusCode(422)->setJSON([
                        'ok'       => false,
                        'mensagem' => $msgErro,
                        'erros'    => $erros,
                    ]);
                }
                return redirect()->back()->withInput()->with('erro', $msgErro);
            }

            // Trilha de auditoria
            AuditService::log(
                'create',
                'avaliacoes',
                (int) $avaliacaoId,
                $dadosAvaliacao,
                null,
                $usuarioId
            );

            $mensagemSucesso = 'Avaliação enviada com sucesso! Ela será publicada após a aprovação da moderação.';
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'ok'       => true,
                'mensagem' => $mensagemSucesso,
                'avaliacao_id' => $avaliacaoId,
            ]);
        }

        return redirect()->to(site_url('produto/' . $produtoId . '#secao-avaliacoes'))
            ->with('sucesso', $mensagemSucesso);
    }

    /**
     * API pública para buscar estatísticas e avaliações aprovadas do produto.
     * Rota: GET /api/produtos/(:num)/avaliacoes
     */
    public function listarApi(int $produtoId)
    {
        $produto = $this->produtoModel->find($produtoId);
        if (!$produto) {
            return $this->response->setStatusCode(404)->setJSON([
                'ok'       => false,
                'mensagem' => 'Produto não encontrado.',
            ]);
        }

        $estatisticas = $this->avaliacaoModel->getEstatisticasProduto($produtoId);
        $avaliacoes   = $this->avaliacaoModel->getAvaliacoesPorProduto($produtoId, 20, true);

        return $this->response->setJSON([
            'ok'           => true,
            'estatisticas' => $estatisticas,
            'avaliacoes'   => $avaliacoes,
        ]);
    }
}
