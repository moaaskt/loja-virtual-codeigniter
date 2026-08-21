<?php

namespace Tests\App;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\UsuarioModel;
use App\Models\PedidoModel;
use App\Models\PedidoProdutoModel;
use App\Models\ProdutoModel;
use App\Models\ClienteEnderecoModel;
use App\Controllers\ClienteController;

class MinhaContaETimelineTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    private function getUsuarioTeste(): array
    {
        $db = \Config\Database::connect('default');
        $email = 'cliente_timeline_' . uniqid() . '@teste.com';

        $db->table('usuarios')->insert([
            'nome'       => 'Cliente Timeline Teste',
            'email'      => $email,
            'senha_hash' => password_hash('123456', PASSWORD_DEFAULT),
            'role'       => 'cliente',
            'ativo'      => 1,
            'criado_em'  => date('Y-m-d H:i:s'),
        ]);

        $id = (int) $db->insertID();
        return (new UsuarioModel())->find($id);
    }

    private function simularSessaoUsuario(array $usuario): void
    {
        session()->set([
            'isLoggedIn'    => true,
            'logado'        => true,
            'usuario_id'    => $usuario['id'],
            'usuario_nome'  => $usuario['nome'],
            'usuario_email' => $usuario['email'],
            'usuario_role'  => 'cliente',
        ]);
    }

    public function testAcessarPainelMinhaContaEPedidos(): void
    {
        $usuario = $this->getUsuarioTeste();
        $this->simularSessaoUsuario($usuario);

        $pedidoModel = new PedidoModel();
        $pedidoId = $pedidoModel->insert([
            'usuario_id'       => $usuario['id'],
            'valor_total'      => 299.90,
            'forma_pagamento'  => 'pix',
            'status_pagamento' => 'pago',
            'status'           => 'pago',
            'cep'              => '01310100',
            'logradouro'       => 'Avenida Paulista',
            'numero'           => '1500',
            'bairro'           => 'Bela Vista',
            'cidade'           => 'São Paulo',
            'uf'               => 'SP',
        ]);

        $controller = new ClienteController();
        $controller->initController(
            \Config\Services::request(),
            \Config\Services::response(),
            \Config\Services::logger()
        );

        $html = $controller->pedidos();
        $this->assertIsString($html);
        $this->assertStringContainsString('Meus Pedidos', $html);
        $this->assertStringContainsString('#' . $pedidoId, $html);
        $this->assertStringContainsString('299,90', $html);
    }

    public function testVisualizarDetalhesPedidoComTimeline(): void
    {
        $usuario = $this->getUsuarioTeste();
        $this->simularSessaoUsuario($usuario);

        $produtoModel = new ProdutoModel();
        $cat = (new \App\Models\CategoriaModel())->first();
        $catId = $cat ? (int)$cat['id'] : 1;

        $produtoId = $produtoModel->insert([
            'categoria_id' => $catId,
            'nome'         => 'Smartwatch Ultra Timeline',
            'descricao'    => 'Smartwatch para teste de timeline',
            'preco'        => 599.00,
            'imagem'       => 'watch.jpg',
            'estoque'      => 10,
            'ativo'        => 1,
        ]);

        $pedidoModel = new PedidoModel();
        $pedidoId = $pedidoModel->insert([
            'usuario_id'       => $usuario['id'],
            'valor_total'      => 599.00,
            'forma_pagamento'  => 'cartao_credito',
            'status_pagamento' => 'pago',
            'status'           => 'enviado',
            'codigo_rastreio'  => 'BR987654321BR',
            'cep'              => '20040002',
            'logradouro'       => 'Rua da Assembleia',
            'numero'           => '10',
            'bairro'           => 'Centro',
            'cidade'           => 'Rio de Janeiro',
            'uf'               => 'RJ',
        ]);

        $variacaoId = (new \App\Models\ProdutoVariacaoModel())->insert([
            'produto_id'     => $produtoId,
            'sku'            => 'WATCH-PRATA',
            'nome_variacao'  => 'Prata / 45mm',
            'preco'          => 599.00,
            'estoque'        => 5,
        ]);

        $pedidoProdutoModel = new PedidoProdutoModel();
        $pedidoProdutoModel->insert([
            'pedido_id'      => $pedidoId,
            'produto_id'     => $produtoId,
            'variacao_id'    => $variacaoId,
            'quantidade'     => 1,
            'preco_unitario' => 599.00,
        ]);

        $controller = new ClienteController();
        $controller->initController(
            \Config\Services::request(),
            \Config\Services::response(),
            \Config\Services::logger()
        );

        $html = $controller->detalhesPedido($pedidoId);
        $this->assertIsString($html);
        $this->assertStringContainsString('Pedido #' . $pedidoId, $html);
        $this->assertStringContainsString('BR987654321BR', $html);
        $this->assertStringContainsString('Enviado', $html);
        $this->assertStringContainsString('Smartwatch Ultra Timeline', $html);
        $this->assertStringContainsString('WATCH-PRATA', $html);
        $this->assertStringContainsString('Rua da Assembleia', $html);
    }

    public function testCrudEnderecosClienteComViaCep(): void
    {
        $usuario = $this->getUsuarioTeste();
        $enderecoModel = new ClienteEnderecoModel();

        // 1. Cadastrar primeiro endereço (deve virar padrão automaticamente)
        $res1 = $enderecoModel->salvarEndereco($usuario['id'], [
            'titulo'       => 'Minha Casa',
            'destinatario' => 'Cliente Teste',
            'cep'          => '01310-100',
            'logradouro'   => 'Avenida Paulista',
            'numero'       => '1000',
            'complemento'  => 'Apto 101',
            'bairro'       => 'Bela Vista',
            'cidade'       => 'São Paulo',
            'uf'           => 'SP',
            'padrao'       => 0,
        ]);

        $this->assertTrue($res1['ok']);
        $end1Id = (int)$res1['id'];

        $end1 = $enderecoModel->find($end1Id);
        $this->assertEquals(1, (int)$end1['padrao'], 'Primeiro endereço deve ser salvo como padrão.');

        // 2. Cadastrar segundo endereço como padrão
        $res2 = $enderecoModel->salvarEndereco($usuario['id'], [
            'titulo'       => 'Trabalho',
            'destinatario' => 'Cliente Teste Trabalho',
            'cep'          => '04571-010',
            'logradouro'   => 'Avenida das Nações Unidas',
            'numero'       => '12901',
            'bairro'       => 'Brooklin Paulista',
            'cidade'       => 'São Paulo',
            'uf'           => 'SP',
            'padrao'       => 1,
        ]);

        $this->assertTrue($res2['ok']);
        $end2Id = (int)$res2['id'];

        // Verifica se end2 virou padrão e end1 perdeu o padrão
        $end2Atualizado = $enderecoModel->find($end2Id);
        $end1Atualizado = $enderecoModel->find($end1Id);
        $this->assertEquals(1, (int)$end2Atualizado['padrao']);
        $this->assertEquals(0, (int)$end1Atualizado['padrao']);

        // 3. Tornar end1 padrão novamente
        $enderecoModel->definirComoPadrao($end1Id, $usuario['id']);
        $this->assertEquals(1, (int)$enderecoModel->find($end1Id)['padrao']);
        $this->assertEquals(0, (int)$enderecoModel->find($end2Id)['padrao']);

        // 4. Listar endereços do usuário
        $lista = $enderecoModel->getEnderecosPorUsuario($usuario['id']);
        $this->assertCount(2, $lista);
        $this->assertEquals($end1Id, $lista[0]['id'], 'O padrão deve vir em primeiro na lista.');

        // 5. Excluir endereço
        $enderecoModel->delete($end2Id);
        $this->assertCount(1, $enderecoModel->getEnderecosPorUsuario($usuario['id']));
    }

    public function testAtualizarPerfilETrocarSenha(): void
    {
        $usuario = $this->getUsuarioTeste();
        $this->simularSessaoUsuario($usuario);

        $usuarioModel = new UsuarioModel();

        // 1. Atualizar Nome
        $novoNome = 'Nome Cliente Atualizado ' . uniqid();
        $usuarioModel->update($usuario['id'], ['nome' => $novoNome]);

        $usuarioAtualizado = $usuarioModel->find($usuario['id']);
        $this->assertEquals($novoNome, $usuarioAtualizado['nome']);

        // 2. Trocar Senha com Hash Válido
        $novaSenhaPlana = 'NovaSenhaSegura@2026';
        $novoHash = password_hash($novaSenhaPlana, PASSWORD_DEFAULT);
        $usuarioModel->update($usuario['id'], ['senha_hash' => $novoHash]);

        $usuarioComNovaSenha = $usuarioModel->find($usuario['id']);
        $this->assertTrue(password_verify($novaSenhaPlana, $usuarioComNovaSenha['senha_hash']));
        $this->assertFalse(password_verify('senha_antiga_errada', $usuarioComNovaSenha['senha_hash']));
    }
}
