<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AvaliacaoModel;
use App\Services\AuditService;

class AvaliacoesController extends BaseController
{
    protected AvaliacaoModel $avaliacaoModel;

    public function __construct()
    {
        helper(['form', 'url', 'status']);
        $this->avaliacaoModel = new AvaliacaoModel();
    }

    /**
     * Listagem de avaliações com paginação, filtros e estatísticas de moderação.
     * Rota: GET admin/avaliacoes
     */
    public function index()
    {
        $status    = $this->request->getGet('status') ?? '';
        $nota      = $this->request->getGet('nota') ?? '';
        $busca     = $this->request->getGet('busca') ?? '';
        $produtoId = $this->request->getGet('produto_id') ?? '';

        $filtros = [
            'status'     => $status,
            'nota'       => $nota,
            'busca'      => $busca,
            'produto_id' => $produtoId,
        ];

        $avaliacoes = $this->avaliacaoModel->getAvaliacoesComFiltros($filtros, 15);
        $contadores = $this->avaliacaoModel->getContadoresStatus();

        $data = [
            'title'      => 'Moderação de Avaliações',
            'avaliacoes' => $avaliacoes,
            'pager'      => $this->avaliacaoModel->pager,
            'contadores' => $contadores,
            'filtros'    => $filtros,
        ];

        return view('admin/avaliacoes/index', $data);
    }

    /**
     * Aprova uma avaliação para exibição pública.
     * Rota: POST admin/avaliacoes/aprovar/(:num)
     */
    public function aprovar(int $id)
    {
        $avaliacao = $this->avaliacaoModel->find($id);
        if (!$avaliacao) {
            return redirect()->back()->with('erro', 'Avaliação não encontrada.');
        }

        $dadosAnteriores = $avaliacao;
        $novoStatus = ['status' => 'aprovada'];

        $this->avaliacaoModel->update($id, $novoStatus);

        // Trilha de auditoria
        $adminId = (int) session()->get('usuario_id');
        AuditService::log('status_change', 'avaliacoes', $id, $novoStatus, $dadosAnteriores, $adminId);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'ok'       => true,
                'mensagem' => 'Avaliação #' . $id . ' aprovada com sucesso!',
                'status'   => 'aprovada',
            ]);
        }

        return redirect()->back()->with('sucesso', 'Avaliação #' . $id . ' aprovada com sucesso!');
    }

    /**
     * Rejeita uma avaliação (não será exibida publicamente).
     * Rota: POST admin/avaliacoes/rejeitar/(:num)
     */
    public function rejeitar(int $id)
    {
        $avaliacao = $this->avaliacaoModel->find($id);
        if (!$avaliacao) {
            return redirect()->back()->with('erro', 'Avaliação não encontrada.');
        }

        $dadosAnteriores = $avaliacao;
        $novoStatus = ['status' => 'rejeitada'];

        $this->avaliacaoModel->update($id, $novoStatus);

        // Trilha de auditoria
        $adminId = (int) session()->get('usuario_id');
        AuditService::log('status_change', 'avaliacoes', $id, $novoStatus, $dadosAnteriores, $adminId);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'ok'       => true,
                'mensagem' => 'Avaliação #' . $id . ' rejeitada.',
                'status'   => 'rejeitada',
            ]);
        }

        return redirect()->back()->with('sucesso', 'Avaliação #' . $id . ' rejeitada.');
    }

    /**
     * Exclui definitivamente uma avaliação.
     * Rota: POST admin/avaliacoes/delete/(:num)
     */
    public function delete(int $id)
    {
        $avaliacao = $this->avaliacaoModel->find($id);
        if (!$avaliacao) {
            return redirect()->back()->with('erro', 'Avaliação não encontrada.');
        }

        $dadosAnteriores = $avaliacao;
        $this->avaliacaoModel->delete($id);

        // Trilha de auditoria
        $adminId = (int) session()->get('usuario_id');
        AuditService::log('delete', 'avaliacoes', $id, null, $dadosAnteriores, $adminId);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'ok'       => true,
                'mensagem' => 'Avaliação excluída com sucesso.',
            ]);
        }

        return redirect()->to(site_url('admin/avaliacoes'))->with('sucesso', 'Avaliação excluída com sucesso.');
    }
}
