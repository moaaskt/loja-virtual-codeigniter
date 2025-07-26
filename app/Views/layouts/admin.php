<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Painel Admin') ?> | Minha Loja</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
     <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
</head>
<body>

<div class="d-flex">
    <div class="sidebar bg-dark text-white p-3">
        <a href="<?= site_url('admin/dashboard') ?>" class="d-flex align-items-center mb-3 text-white text-decoration-none">
            <i class="bi bi-box-seam-fill me-2 fs-4"></i>
            <span class="fs-4">LojaAdmin</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="<?= site_url('admin/dashboard') ?>" class="nav-link text-white">
                    <i class="bi bi-grid-fill me-2"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/pedidos') ?>" class="nav-link text-white">
                    <i class="bi bi-receipt-cutoff me-2"></i> Pedidos
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/produtos') ?>" class="nav-link text-white">
                    <i class="bi bi-box-fill me-2"></i> Produtos
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/categorias') ?>" class="nav-link text-white">
                    <i class="bi bi-tags-fill me-2"></i> Categorias
                </a>
            </li>
        </ul>
        <hr>
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle fs-4 me-2"></i>
                <strong><?= esc(session()->get('nome')) ?></strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                <li><a class="dropdown-item" href="<?= site_url('/') ?>">Ver Loja</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="<?= site_url('logout') ?>">Sair</a></li>
            </ul>
        </div>
    </div>

    <div class="main-content flex-grow-1 p-4">
        <?= $this->renderSection('content') ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>