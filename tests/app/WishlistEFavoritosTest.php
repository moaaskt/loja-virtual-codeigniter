<?php

namespace Tests\App;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\UsuarioModel;
use App\Models\ProdutoModel;
use App\Models\CategoriaModel;
use App\Models\ClienteFavoritoModel;
use App\Controllers\FavoritoController;
use App\Controllers\ClienteController;

class WishlistEFavoritosTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    private function getUsuarioTeste(): array
    {
        $db = \Config\Database::connect('default');
        $email = 'cliente_wishlist_' . uniqid() . '@teste.com';

        $db->table('usuarios')->insert([
            'nome'       => 'Cliente Wishlist Teste',
            'email'      => $email,
            'senha_hash' => password_hash('123456', PASSWORD_DEFAULT),
            'role'       => 'cliente',
            'ativo'      => 1,
            'criado_em'  => date('Y-m-d H:i:s'),
        ]);

        $id = (int) $db->insertID();
        return (new UsuarioModel())->find($id);
    }

    private function getProdutoTeste(string $nome = 'Fone Bluetooth Pro'): array
    {
        $cat = (new CategoriaModel())->first();
        $catId = $cat ? (int)$cat['id'] : 1;

        $produtoModel = new ProdutoModel();
        $id = $produtoModel->insert([
            'categoria_id' => $catId,
            'nome'         => $nome . ' ' . uniqid(),
            'descricao'    => 'Fone com cancelamento de ruído',
            'preco'        => 349.90,
            'imagem'       => 'fone.jpg',
            'estoque'      => 15,
            'ativo'        => 1,
        ]);

        return $produtoModel->find($id);
    }

    private function simularSessaoUsuario(array $usuario): void
    {
        $session = \Config\Services::session();
        $session->set([
            'isLoggedIn'    => true,
            'logado'        => true,
            'usuario_id'    => (int) $usuario['id'],
            'usuario_nome'  => $usuario['nome'],
            'usuario_email' => $usuario['email'],
            'usuario_role'  => 'cliente',
        ]);

        $_SESSION['usuario_id']    = (int) $usuario['id'];
        $_SESSION['usuario_nome']  = $usuario['nome'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_role']  = 'cliente';
        $_SESSION['isLoggedIn']    = true;
        $_SESSION['logado']        = true;
    }

    public function testToggleFavoritoModelAdicionaERemove(): void
    {
        $usuario = $this->getUsuarioTeste();
        $produto = $this->getProdutoTeste();
        $model   = new ClienteFavoritoModel();

        // 1. Adicionar aos favoritos
        $res1 = $model->toggleFavorito($usuario['id'], $produto['id']);
        $this->assertTrue($res1['ok']);
        $this->assertTrue($res1['adicionado']);
        $this->assertTrue($res1['favorito']);
        $this->assertEquals(1, $res1['total']);
        $this->assertTrue($model->isFavorito($usuario['id'], $produto['id']));

        // 2. Remover dos favoritos no segundo toggle
        $res2 = $model->toggleFavorito($usuario['id'], $produto['id']);
        $this->assertTrue($res2['ok']);
        $this->assertFalse($res2['adicionado']);
        $this->assertFalse($res2['favorito']);
        $this->assertEquals(0, $res2['total']);
        $this->assertFalse($model->isFavorito($usuario['id'], $produto['id']));
    }

    public function testListarProdutosFavoritosDoUsuario(): void
    {
        $usuario1 = $this->getUsuarioTeste();
        $usuario2 = $this->getUsuarioTeste();
        $prod1    = $this->getProdutoTeste('Monitor UltraWide 34');
        $prod2    = $this->getProdutoTeste('Teclado Mecânico RGB');
        $model    = new ClienteFavoritoModel();

        // Usuário 1 favorita 2 produtos
        $model->toggleFavorito($usuario1['id'], $prod1['id']);
        $model->toggleFavorito($usuario1['id'], $prod2['id']);

        // Usuário 2 favorita 1 produto
        $model->toggleFavorito($usuario2['id'], $prod1['id']);

        // Valida listagem do usuário 1
        $favs1 = $model->getFavoritosPorUsuario($usuario1['id']);
        $this->assertCount(2, $favs1);
        $this->assertEquals($prod2['id'], $favs1[0]['id'], 'O mais recente deve vir em primeiro.');
        $this->assertNotEmpty($favs1[0]['categoria_nome']);

        $ids1 = $model->getIdsFavoritosPorUsuario($usuario1['id']);
        $this->assertContains((int)$prod1['id'], $ids1);
        $this->assertContains((int)$prod2['id'], $ids1);

        // Valida contagem do usuário 2
        $this->assertEquals(1, $model->getTotalFavoritos($usuario2['id']));
    }

    public function testFavoritoControllerApiToggleSemLoginRetorna401(): void
    {
        $controller = new FavoritoController();
        $controller->initController(
            \Config\Services::request(),
            \Config\Services::response(),
            \Config\Services::logger()
        );

        $response401 = $controller->toggle();
        $this->assertEquals(401, $response401->getStatusCode());
        $body401 = json_decode($response401->getBody(), true);
        $this->assertFalse($body401['ok']);
        $this->assertTrue($body401['auth_required']);
    }

    public function testFavoritoControllerApiToggleComLoginEIds(): void
    {
        $usuario = $this->getUsuarioTeste();
        $produto = $this->getProdutoTeste();
        $this->simularSessaoUsuario($usuario);

        $request = \Config\Services::request();
        $request->setGlobal('post', ['produto_id' => $produto['id']]);

        $controllerLogado = new FavoritoController();
        $controllerLogado->initController(
            $request,
            \Config\Services::response(),
            \Config\Services::logger()
        );

        $response200 = $controllerLogado->toggle();
        $this->assertEquals(200, $response200->getStatusCode());
        $body200 = json_decode($response200->getBody(), true);
        $this->assertTrue($body200['ok']);
        $this->assertTrue($body200['adicionado']);
        $this->assertEquals(1, $body200['total']);

        // Obter IDs via API
        $responseIds = $controllerLogado->ids();
        $bodyIds = json_decode($responseIds->getBody(), true);
        $this->assertTrue($bodyIds['ok']);
        $this->assertContains((int)$produto['id'], $bodyIds['ids']);
        $this->assertEquals(1, $bodyIds['total']);
    }

    public function testClienteControllerFavoritosERemover(): void
    {
        $usuario = $this->getUsuarioTeste();
        $produto = $this->getProdutoTeste('Mouse Gamer 16000DPI');
        $this->simularSessaoUsuario($usuario);

        $favoritoModel = new ClienteFavoritoModel();
        $favoritoModel->toggleFavorito($usuario['id'], $produto['id']);

        $controller = new ClienteController();
        $controller->initController(
            \Config\Services::request(),
            \Config\Services::response(),
            \Config\Services::logger()
        );

        // 1. Renderizar tela de favoritos
        $html = $controller->favoritos();
        $this->assertIsString($html);
        $this->assertStringContainsString('Minha Lista de Desejos', $html);
        $this->assertStringContainsString($produto['nome'], $html);

        // 2. Remover favorito via controller
        $redirect = $controller->removerFavorito($produto['id']);
        $this->assertInstanceOf(\CodeIgniter\HTTP\RedirectResponse::class, $redirect);
        $this->assertFalse($favoritoModel->isFavorito($usuario['id'], $produto['id']));
    }
}
