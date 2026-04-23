<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="G'Store — Os melhores produtos com entrega rápida.">

    <title>G'Store | <?= $this->renderSection('title') ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/shop.css') ?>">
</head>

<body>

    <!-- ===== NAVBAR ===== -->
    <nav class="navbar-gstore">
        <div class="container d-flex align-items-center justify-content-between gap-3 flex-wrap">

            <!-- Brand -->
            <a class="navbar-brand-gstore flex-shrink-0" href="<?= site_url('/') ?>">
                <span class="brand-logo-icon"><i class="bi bi-bag-heart-fill"></i></span>
                G'Store
            </a>

            <!-- Search -->
            <div class="search-form flex-grow-1" style="max-width:380px;">
                <?= form_open('busca', ['class' => 'd-flex position-relative', 'method' => 'get', 'id' => 'form-busca']) ?>
                    <i class="bi bi-search search-icon"></i>
                    <input class="form-control" type="search" name="termo" id="input-busca"
                        placeholder="Buscar produtos..."
                        aria-label="Buscar"
                        value="<?= esc(isset($termoBusca) ? $termoBusca : '') ?>">
                <?= form_close() ?>
            </div>

            <!-- Actions -->
            <div class="d-flex align-items-center gap-2 flex-shrink-0">

                <!-- Cart -->
                <?php
                $carrinho    = session()->get('carrinho') ?? [];
                $total_itens = count($carrinho);
                ?>
                <a href="<?= site_url('carrinho') ?>" class="btn-cart" id="btn-cart-nav">
                    <i class="bi bi-bag-fill"></i>
                    <span>Carrinho</span>
                    <?php if ($total_itens > 0): ?>
                        <span class="cart-badge"><?= $total_itens ?></span>
                    <?php endif; ?>
                </a>

                <!-- User -->
                <?php if (session()->get('isLoggedIn')): ?>
                    <div class="dropdown">
                        <button class="btn btn-light btn-sm rounded-pill d-flex align-items-center gap-2 px-3"
                            type="button" data-bs-toggle="dropdown" aria-expanded="false" id="btn-user-dropdown">
                            <i class="bi bi-person-circle fs-5"></i>
                            <span class="d-none d-md-inline fw-semibold"><?= esc(session()->get('nome')) ?></span>
                            <i class="bi bi-chevron-down small"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" style="min-width:200px;">
                            <li class="px-3 py-2 border-bottom">
                                <small class="text-muted d-block">Logado como</small>
                                <strong class="fs-6"><?= esc(session()->get('nome')) ?></strong>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                    href="<?= site_url('minha-conta/pedidos') ?>">
                                    <i class="bi bi-receipt text-primary"></i> Meus Pedidos
                                </a>
                            </li>
                            <?php if (session()->get('role') === 'admin'): ?>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                        href="<?= site_url('admin/dashboard') ?>">
                                        <i class="bi bi-speedometer2 text-indigo"></i> Painel Admin
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger"
                                    href="<?= site_url('logout') ?>">
                                    <i class="bi bi-box-arrow-right"></i> Sair
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?= site_url('login') ?>"
                        class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold" id="btn-login-nav">
                        <i class="bi bi-person me-1"></i>Entrar
                    </a>
                    <a href="<?= site_url('registrar') ?>"
                        class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold d-none d-md-inline-flex" id="btn-register-nav">
                        Criar Conta
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- ===== FLASH MESSAGES ===== -->
    <?php if (session()->getFlashdata('success') || session()->getFlashdata('error')): ?>
        <div class="container mt-3">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="flash-alert alert-success alert d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="flash-alert alert-danger alert d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- ===== CONTENT ===== -->
    <main class="container py-4">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- ===== FOOTER ===== -->
    <footer class="border-top mt-5 py-4 bg-white">
        <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
            <span class="text-muted small">© <?= date('Y') ?> G'Store — Todos os direitos reservados.</span>
            <div class="d-flex gap-3">
                <a href="#" class="text-muted small text-decoration-none">Privacidade</a>
                <a href="#" class="text-muted small text-decoration-none">Termos</a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>