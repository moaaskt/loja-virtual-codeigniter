<?php

namespace Tests\App;

use CodeIgniter\Test\CIUnitTestCase;
use App\Controllers\PedidoController;
use CodeIgniter\HTTP\RedirectResponse;

class CheckoutPageTest extends CIUnitTestCase
{
    public function testCheckoutSemAutenticacaoRedirecionaParaLogin(): void
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
}
