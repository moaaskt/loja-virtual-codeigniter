<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CupomModel;
use App\Services\AuditService;

class CuponsController extends BaseController
{
    protected CupomModel $cupomModel;

    public function __construct()
    {
        $this->cupomModel = new CupomModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        $data = [
            'title'   => 'Gerenciar Cupons de Desconto',
            'cupons'  => $this->cupomModel->orderBy('criado_em', 'DESC')->paginate(10),
            'pager'   => $this->cupomModel->pager,
        ];

        return view('admin/cupons/index', $data);
    }

    public function new()
    {
        return view('admin/cupons/form', [
            'title' => 'Novo Cupom de Desconto',
            'cupom' => null,
        ]);
    }

    public function create()
    {
        $data = $this->request->getPost();
        $data['codigo'] = strtoupper(trim($data['codigo'] ?? ''));

        // Trata campos nulos/vazios
        if (empty($data['limite_uso'])) {
            $data['limite_uso'] = null;
        }
        if (empty($data['data_validade'])) {
            $data['data_validade'] = null;
        }
        if (empty($data['valor_minimo_pedido'])) {
            $data['valor_minimo_pedido'] = 0.00;
        }
        $data['ativo'] = isset($data['ativo']) ? 1 : 0;

        $cupomId = $this->cupomModel->insert($data);
        if ($cupomId) {
            AuditService::log('create', 'cupons', (int) $cupomId, [
                'codigo'    => $data['codigo'],
                'tipo'      => $data['tipo'] ?? 'porcentagem',
                'valor'     => $data['valor'] ?? 0,
                'ativo'     => $data['ativo'] ?? 1,
            ]);
            return redirect()->to(site_url('admin/cupons'))->with('success', 'Cupom criado com sucesso!');
        }

        return redirect()->back()->withInput()->with('errors', $this->cupomModel->errors());
    }

    public function edit($id = null)
    {
        $cupom = $this->cupomModel->find($id);
        if (!$cupom) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Cupom não encontrado.');
        }

        return view('admin/cupons/form', [
            'title' => 'Editar Cupom: ' . esc($cupom['codigo']),
            'cupom' => $cupom,
        ]);
    }

    public function update($id = null)
    {
        $cupomAntigo = $this->cupomModel->find($id);
        if (!$cupomAntigo) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Cupom não encontrado.');
        }

        $data = $this->request->getPost();
        $data['id']     = (int) $id;
        $data['codigo'] = strtoupper(trim($data['codigo'] ?? ''));

        if (empty($data['limite_uso'])) {
            $data['limite_uso'] = null;
        }
        if (empty($data['data_validade'])) {
            $data['data_validade'] = null;
        }
        if (empty($data['valor_minimo_pedido'])) {
            $data['valor_minimo_pedido'] = 0.00;
        }
        $data['ativo'] = isset($data['ativo']) ? 1 : 0;

        if ($this->cupomModel->save($data)) {
            AuditService::log('update', 'cupons', (int) $id, [
                'codigo'    => $data['codigo'],
                'tipo'      => $data['tipo'] ?? ($cupomAntigo['tipo'] ?? 'porcentagem'),
                'valor'     => $data['valor'] ?? ($cupomAntigo['valor'] ?? 0),
                'ativo'     => $data['ativo'],
            ], [
                'codigo'    => $cupomAntigo['codigo'],
                'tipo'      => $cupomAntigo['tipo'],
                'valor'     => $cupomAntigo['valor'],
                'ativo'     => $cupomAntigo['ativo'],
            ]);
            return redirect()->to(site_url('admin/cupons'))->with('success', 'Cupom atualizado com sucesso!');
        }

        return redirect()->back()->withInput()->with('errors', $this->cupomModel->errors());
    }

    public function delete($id = null)
    {
        $cupom = $this->cupomModel->find($id);
        if (!$cupom) {
            return redirect()->to(site_url('admin/cupons'))->with('error', 'Cupom não encontrado.');
        }

        if ($this->cupomModel->delete($id)) {
            AuditService::log('delete', 'cupons', (int) $id, null, [
                'codigo' => $cupom['codigo'],
                'tipo'   => $cupom['tipo'],
                'valor'  => $cupom['valor'],
            ]);
            return redirect()->to(site_url('admin/cupons'))->with('success', 'Cupom excluído com sucesso!');
        }

        return redirect()->to(site_url('admin/cupons'))->with('error', 'Erro ao excluir o cupom.');
    }

    public function toggle($id = null)
    {
        $cupom = $this->cupomModel->find($id);
        if (!$cupom) {
            return redirect()->to(site_url('admin/cupons'))->with('error', 'Cupom não encontrado.');
        }

        $novoStatus = (int) $cupom['ativo'] === 1 ? 0 : 1;
        $this->cupomModel->update($id, ['ativo' => $novoStatus]);

        AuditService::log('status_change', 'cupons', (int) $id, [
            'ativo' => $novoStatus,
        ], [
            'ativo' => $cupom['ativo'],
        ]);

        $msg = $novoStatus === 1 ? 'Cupom ativado com sucesso!' : 'Cupom desativado com sucesso!';
        return redirect()->to(site_url('admin/cupons'))->with('success', $msg);
    }
}
