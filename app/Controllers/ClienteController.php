<?php

namespace App\Controllers;

use App\Models\PedidoModel;
use App\Models\PedidoProdutoModel;
use App\Models\ClienteEnderecoModel;
use App\Models\UsuarioModel;

class ClienteController extends BaseController
{
    protected PedidoModel $pedidoModel;
    protected PedidoProdutoModel $pedidoProdutoModel;
    protected ClienteEnderecoModel $enderecoModel;
    protected UsuarioModel $usuarioModel;

    public function __construct()
    {
        helper(['form', 'url', 'status']);
        $this->pedidoModel        = new PedidoModel();
        $this->pedidoProdutoModel = new PedidoProdutoModel();
        $this->enderecoModel      = new ClienteEnderecoModel();
        $this->usuarioModel       = new UsuarioModel();
    }

    private function getUsuarioLogado(): array
    {
        $usuarioId = (int) session()->get('usuario_id');
        $usuario = $this->usuarioModel->find($usuarioId);
        if (!$usuario) {
            session()->destroy();
            header('Location: ' . site_url('login'));
            exit;
        }
        return $usuario;
    }

    /**
     * Dashboard / Meus Pedidos
     */
    public function index()
    {
        return $this->pedidos();
    }

    /**
     * Listagem completa de pedidos do cliente
     */
    public function pedidos()
    {
        $usuario = $this->getUsuarioLogado();
        $pedidos = $this->pedidoModel->getPedidosPorUsuario($usuario['id']);
        $itens_dos_pedidos = [];

        if (!empty($pedidos)) {
            $pedidoIds = array_column($pedidos, 'id');
            $produtos = $this->pedidoProdutoModel->getProdutosDePedidos($pedidoIds);

            foreach ($produtos as $produto) {
                $itens_dos_pedidos[$produto['pedido_id']][] = $produto;
            }
        }

        $data = [
            'title'             => 'Meus Pedidos',
            'active_tab'        => 'pedidos',
            'usuario'           => $usuario,
            'pedidos'           => $pedidos,
            'itens_dos_pedidos' => $itens_dos_pedidos,
        ];

        return view('cliente/meus_pedidos', $data);
    }

    /**
     * Visualização detalhada do pedido com Timeline Visual de 5 etapas
     */
    public function detalhesPedido($id = null)
    {
        $usuario = $this->getUsuarioLogado();
        $pedidoId = (int) $id;

        $pedido = $this->pedidoModel->find($pedidoId);
        if (!$pedido || (int)$pedido['usuario_id'] !== (int)$usuario['id']) {
            return redirect()->to('minha-conta/pedidos')->with('erro', 'Pedido não encontrado ou acesso não autorizado.');
        }

        $itens = $this->pedidoProdutoModel->getProdutosDePedidos([$pedidoId]);
        $timeline = getTimelineEtapas($pedido['status'] ?? 'pendente', $pedido['status_pagamento'] ?? null);

        $data = [
            'title'      => 'Pedido #' . $pedido['id'],
            'active_tab' => 'pedidos',
            'usuario'    => $usuario,
            'pedido'     => $pedido,
            'itens'      => $itens,
            'timeline'   => $timeline,
        ];

        return view('cliente/detalhes_pedido', $data);
    }

    /**
     * Gestão de Múltiplos Endereços
     */
    public function enderecos()
    {
        $usuario = $this->getUsuarioLogado();
        $enderecos = $this->enderecoModel->getEnderecosPorUsuario($usuario['id']);

        $data = [
            'title'      => 'Meus Endereços',
            'active_tab' => 'enderecos',
            'usuario'    => $usuario,
            'enderecos'  => $enderecos,
        ];

        return view('cliente/enderecos', $data);
    }

    /**
     * Salvar endereço (Novo ou Edição)
     */
    public function salvarEndereco()
    {
        $usuario = $this->getUsuarioLogado();
        $enderecoId = (int) $this->request->getPost('endereco_id');

        $dados = [
            'titulo'       => $this->request->getPost('titulo'),
            'destinatario' => $this->request->getPost('destinatario'),
            'cep'          => $this->request->getPost('cep'),
            'logradouro'   => $this->request->getPost('logradouro'),
            'numero'       => $this->request->getPost('numero'),
            'complemento'  => $this->request->getPost('complemento'),
            'bairro'       => $this->request->getPost('bairro'),
            'cidade'       => $this->request->getPost('cidade'),
            'uf'           => $this->request->getPost('uf'),
            'padrao'       => $this->request->getPost('padrao'),
        ];

        if (empty($dados['cep']) || empty($dados['logradouro']) || empty($dados['numero']) || empty($dados['bairro']) || empty($dados['cidade']) || empty($dados['uf'])) {
            return redirect()->back()->withInput()->with('erro', 'Por favor, preencha todos os campos obrigatórios do endereço.');
        }

        $res = $this->enderecoModel->salvarEndereco($usuario['id'], $dados, $enderecoId > 0 ? $enderecoId : null);

        if (!$res['ok']) {
            return redirect()->back()->withInput()->with('erro', $res['erro'] ?? 'Erro ao salvar endereço.');
        }

        return redirect()->to('minha-conta/enderecos')->with('sucesso', 'Endereço salvo com sucesso!');
    }

    /**
     * Definir endereço como padrão
     */
    public function definirEnderecoPadrao($id = null)
    {
        $usuario = $this->getUsuarioLogado();
        $enderecoId = (int) $id;

        $this->enderecoModel->definirComoPadrao($enderecoId, $usuario['id']);

        return redirect()->to('minha-conta/enderecos')->with('sucesso', 'Endereço padrão atualizado!');
    }

    /**
     * Excluir endereço
     */
    public function excluirEndereco($id = null)
    {
        $usuario = $this->getUsuarioLogado();
        $enderecoId = (int) $id;

        $endereco = $this->enderecoModel->where('id', $enderecoId)->where('usuario_id', $usuario['id'])->first();
        if ($endereco) {
            $this->enderecoModel->delete($enderecoId);
            return redirect()->to('minha-conta/enderecos')->with('sucesso', 'Endereço removido com sucesso.');
        }

        return redirect()->to('minha-conta/enderecos')->with('erro', 'Endereço não encontrado.');
    }

    /**
     * Tela de Perfil & Segurança
     */
    public function perfil()
    {
        $usuario = $this->getUsuarioLogado();

        $data = [
            'title'      => 'Meu Perfil & Segurança',
            'active_tab' => 'perfil',
            'usuario'    => $usuario,
        ];

        return view('cliente/perfil', $data);
    }

    /**
     * Atualizar dados cadastrais
     */
    public function salvarPerfil()
    {
        $usuario = $this->getUsuarioLogado();
        $nome = trim($this->request->getPost('nome') ?? '');

        if (mb_strlen($nome) < 3) {
            return redirect()->back()->withInput()->with('erro', 'O nome deve ter pelo menos 3 caracteres.');
        }

        $this->usuarioModel->update($usuario['id'], ['nome' => $nome]);
        session()->set('usuario_nome', $nome);

        return redirect()->to('minha-conta/perfil')->with('sucesso', 'Dados do perfil atualizados com sucesso!');
    }

    /**
     * Trocar senha de acesso
     */
    public function trocarSenha()
    {
        $usuario = $this->getUsuarioLogado();

        $senhaAtual   = (string) $this->request->getPost('senha_atual');
        $novaSenha    = (string) $this->request->getPost('nova_senha');
        $confirmSenha = (string) $this->request->getPost('confirmar_senha');

        if (!password_verify($senhaAtual, $usuario['senha_hash'])) {
            return redirect()->back()->with('erro_senha', 'A senha atual informada está incorreta.');
        }

        if (strlen($novaSenha) < 6) {
            return redirect()->back()->with('erro_senha', 'A nova senha deve ter no mínimo 6 caracteres.');
        }

        if ($novaSenha !== $confirmSenha) {
            return redirect()->back()->with('erro_senha', 'A confirmação de senha não confere com a nova senha.');
        }

        $novoHash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $this->usuarioModel->update($usuario['id'], ['senha_hash' => $novoHash]);

        return redirect()->to('minha-conta/perfil')->with('sucesso_senha', 'Senha alterada com sucesso!');
    }
}