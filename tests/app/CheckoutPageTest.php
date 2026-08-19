<?php

namespace Tests\App;

use CodeIgniter\Test\CIUnitTestCase;
use App\Controllers\PedidoController;
use App\Controllers\AuthController;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\URI;
use CodeIgniter\HTTP\UserAgent;

class CheckoutPageTest extends CIUnitTestCase
{
    public function testCheckoutSemAutenticacaoRedirecionaParaLoginComMensagemERedirectUrl(): void
    {
        session()->set('isLoggedIn', false);

        $controller = new PedidoController();
        $controller->initController(
            service('request'),
            service('response'),
            service('logger')
        );

        $response = $controller->checkout();
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(site_url('checkout'), session()->get('redirect_url'));
        $this->assertEquals('Faça login ou crie sua conta para finalizar o seu pedido.', session()->getFlashdata('info'));
    }

    public function testCheckoutComCarrinhoVazioRedirecionaParaCarrinho(): void
    {
        session()->set('isLoggedIn', true);
        session()->set('usuario_id', 1);
        session()->remove('carrinho');

        $controller = new PedidoController();
        $controller->initController(
            service('request'),
            service('response'),
            service('logger')
        );

        $response = $controller->checkout();
        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    public function testCheckoutComItensRenderizaViewComTotais(): void
    {
        session()->set('isLoggedIn', true);
        session()->set('usuario_id', 1);
        session()->set('nome', 'Cliente Teste');
        session()->set('carrinho', [
            '1_0' => [
                'id'         => 1,
                'nome'       => 'Produto Teste Checkout',
                'preco'      => 100.00,
                'quantidade' => 2,
                'imagem'     => 'produto.jpg',
                'tamanho'    => 'M',
                'cor'        => 'Azul',
            ]
        ]);

        $controller = new PedidoController();
        $controller->initController(
            service('request'),
            service('response'),
            service('logger')
        );

        $html = $controller->checkout();
        $this->assertIsString($html);
        $this->assertStringContainsString('Checkout Seguro', $html);
        $this->assertStringContainsString('Produto Teste Checkout', $html);
        $this->assertStringContainsString('R$ 200,00', $html);
        $this->assertStringContainsString('Endereço de Entrega', $html);
        $this->assertStringContainsString('Forma de Pagamento', $html);
    }

    public function testLoginComRedirectUrlRedirecionaParaUrlSalva(): void
    {
        $db = \Config\Database::connect('default');
        $usuario = $db->table('usuarios')->where('role', 'cliente')->get()->getRowArray();
        $this->assertNotEmpty($usuario);

        // Define redirect_url na sessão
        session()->set('redirect_url', site_url('checkout'));

        // Simula request de login
        $_POST['email'] = $usuario['email'];
        $_POST['senha'] = '123456'; // Senha padrão seeder

        $request = service('request');
        $request->setMethod('post');

        $controller = new AuthController();
        $controller->initController(
            $request,
            service('response'),
            service('logger')
        );

        $response = $controller->attemptLogin();
        $this->assertInstanceOf(RedirectResponse::class, $response);

        // redirect_url deve ter sido limpo da sessão após uso
        $this->assertNull(session()->get('redirect_url'));
        $this->assertTrue(session()->get('isLoggedIn'));
    }
}
