<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Painel Admin') ?> | G'Store Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<!-- Overlay for mobile sidebar -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ===== SIDEBAR ===== -->
<aside class="sidebar" id="sidebar">

    <a href="<?= site_url('admin/dashboard') ?>" class="sidebar-brand">
        <span class="brand-icon"><i class="bi bi-bag-heart-fill"></i></span>
        G'Store
    </a>

    <span class="sidebar-section-label">Principal</span>
    <nav>
        <ul class="nav flex-column">
            <li>
                <a href="<?= site_url('admin/dashboard') ?>"
                    class="nav-link <?= (uri_string() === 'admin/dashboard') ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>
            </li>
        </ul>
    </nav>

    <span class="sidebar-section-label">Gestão</span>
    <nav>
        <ul class="nav flex-column">
            <li>
                <a href="<?= site_url('admin/pedidos') ?>"
                    class="nav-link <?= str_starts_with(uri_string(), 'admin/pedidos') ? 'active' : '' ?>">
                    <i class="bi bi-receipt-cutoff"></i>
                    Pedidos
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/produtos') ?>"
                    class="nav-link <?= str_starts_with(uri_string(), 'admin/produtos') ? 'active' : '' ?>">
                    <i class="bi bi-box-seam-fill"></i>
                    Produtos
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/categorias') ?>"
                    class="nav-link <?= str_starts_with(uri_string(), 'admin/categorias') ? 'active' : '' ?>">
                    <i class="bi bi-tags-fill"></i>
                    Categorias
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/clientes') ?>"
                    class="nav-link <?= str_starts_with(uri_string(), 'admin/clientes') ? 'active' : '' ?>">
                    <i class="bi bi-people-fill"></i>
                    Clientes
                </a>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <div class="dropdown">
            <a class="sidebar-user dropdown-toggle" href="#" data-bs-toggle="dropdown"
                aria-expanded="false" id="sidebar-user-menu">
                <span class="sidebar-avatar">
                    <?= mb_strtoupper(mb_substr(session()->get('nome') ?? 'A', 0, 1)) ?>
                </span>
                <div class="lh-1">
                    <div class="text-white fw-semibold" style="font-size:.8125rem;">
                        <?= esc(session()->get('nome')) ?>
                    </div>
                    <small style="font-size:.6875rem; color:rgba(255,255,255,.4);">Administrador</small>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark border-0 shadow mb-2" style="min-width:190px;">
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2"
                        href="<?= site_url('/') ?>" target="_blank">
                        <i class="bi bi-shop"></i> Ver Loja
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 text-danger"
                        href="<?= site_url('logout') ?>">
                        <i class="bi bi-box-arrow-right"></i> Sair
                    </a>
                </li>
            </ul>
        </div>
    </div>
</aside>

<!-- ===== MAIN CONTENT ===== -->
<div class="main-content">

    <!-- Mobile topbar -->
    <div class="d-lg-none d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
        <button class="btn btn-sm btn-outline-secondary" id="sidebarToggle" aria-label="Abrir menu">
            <i class="bi bi-list fs-5"></i>
        </button>
        <span class="fw-bold">G'Store Admin</span>
        <span class="sidebar-avatar" style="width:32px;height:32px;font-size:.75rem;">
            <?= mb_strtoupper(mb_substr(session()->get('nome') ?? 'A', 0, 1)) ?>
        </span>
    </div>

    <?= $this->renderSection('content') ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Mobile sidebar toggle
    const sidebar        = document.getElementById('sidebar');
    const overlay        = document.getElementById('sidebarOverlay');
    const toggleBtn      = document.getElementById('sidebarToggle');

    function openSidebar()  { sidebar.classList.add('sidebar-open');  overlay.classList.add('active'); }
    function closeSidebar() { sidebar.classList.remove('sidebar-open'); overlay.classList.remove('active'); }

    toggleBtn?.addEventListener('click', openSidebar);
    overlay.addEventListener('click', closeSidebar);
</script>
</body>
</html>