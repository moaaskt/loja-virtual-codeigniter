<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Loja Virtual | <?= $this->renderSection('title') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>

   <nav class="navbar navbar-expand-lg bg-white shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand" href="<?= site_url('/') ?>">
            <strong>Minha Loja</strong>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="mx-auto my-2 my-lg-0">
                <?= form_open('busca', ['class' => 'd-flex', 'method' => 'get', 'id' => 'form-busca']) ?>
                    <input class="form-control me-2" type="search" name="termo" placeholder="Buscar produtos..." aria-label="Buscar" value="<?= esc(isset($termoBusca) ? $termoBusca : '') ?>">
                    <button class="btn btn-outline-success" type="submit">Buscar</button>
                <?= form_close() ?>
            </div>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a href="<?= site_url('carrinho') ?>" class="btn btn-outline-primary me-2">
                        <i class="bi bi-cart-fill"></i> Carrinho 
                        <?php
                            $carrinho = session()->get('carrinho') ?? [];
                            $total_itens = count($carrinho);
                        ?>
                        <?php if ($total_itens > 0): ?>
                            <span class="badge bg-danger ms-1"><?= $total_itens ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php if (session()->get('isLoggedIn')): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            Olá, <strong><?= esc(session()->get('nome')) ?></strong>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= site_url('minha-conta/pedidos') ?>">Meus Pedidos</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= site_url('logout') ?>">Sair (Logout)</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a href="<?= site_url('login') ?>" class="btn btn-success">Login / Cadastrar</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

    <div class="container mt-4">

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success" role="alert">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger" role="alert">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
        <?= $this->renderSection('scripts') ?>
</body>
</html>