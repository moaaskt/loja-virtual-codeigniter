<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;
use App\Models\PedidoModel;

class ClienteController extends BaseController
{
    protected UsuarioModel $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    public function index()
    {
        return view('admin/clientes/index', [
            'title'    => 'Clientes Cadastrados',
            'clientes' => $this->usuarioModel->getClientesComEstatisticas(20),
            'pager'    => $this->usuarioModel->pager,
        ]);
    }

    public function toggle($id = null)
    {
        $cliente = $this->usuarioModel->find($id);

        if (!$cliente || $cliente['role'] !== 'cliente') {
            return redirect()->to(site_url('admin/clientes'))->with('error', 'Cliente não encontrado.');
        }

        $novoStatus = $cliente['ativo'] ? 0 : 1;
        $this->usuarioModel->update($id, ['ativo' => $novoStatus]);

        $msg = $novoStatus ? 'Conta reativada com sucesso.' : 'Conta desativada com sucesso.';
        return redirect()->to(site_url('admin/clientes'))->with('success', $msg);
    }

    public function show($id = null)
    {
        $cliente = $this->usuarioModel->find($id);

        if (!$cliente || $cliente['role'] !== 'cliente') {
            return redirect()->to(site_url('admin/clientes'))->with('error', 'Cliente não encontrado.');
        }

        $pedidoModel = new PedidoModel();
        
        $data = [
            'title'    => 'Detalhes do Cliente',
            'cliente'  => $cliente,
            'pedidos'  => $pedidoModel->getPedidosPorUsuario($id),
        ];

        return view('admin/clientes/show', $data);
    }
}
