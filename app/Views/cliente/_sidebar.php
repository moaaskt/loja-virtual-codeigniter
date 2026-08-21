<?php
    $activeTab = $active_tab ?? 'pedidos';
    $nomeUsuario = $usuario['nome'] ?? (session()->get('usuario_nome') ?? 'Cliente');
    $emailUsuario = $usuario['email'] ?? (session()->get('usuario_email') ?? '');
    $inicial = mb_strtoupper(mb_substr($nomeUsuario, 0, 1));
?>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <!-- Header do Usuário -->
    <div class="card-body p-4 text-center bg-light border-bottom">
        <div class="avatar-circle mx-auto mb-3 d-flex align-items-center justify-content-center bg-primary text-white shadow-sm fw-bold fs-3"
             style="width: 72px; height: 72px; border-radius: 50%;">
            <?= esc($inicial) ?>
        </div>
        <h2 class="fs-6 fw-bold mb-1 text-dark"><?= esc($nomeUsuario) ?></h2>
        <p class="text-muted small mb-0 font-monospace"><?= esc($emailUsuario) ?></p>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill mt-2 px-3 py-1 small">
            <i class="bi bi-shield-check me-1"></i>Cliente Verificado
        </span>
    </div>

    <!-- Menu de Navegação -->
    <div class="list-group list-group-flush p-2">
        <a href="<?= site_url('minha-conta/pedidos') ?>"
           class="list-group-item list-group-item-action d-flex align-items-center justify-content-between rounded-3 py-2 px-3 mb-1 border-0 <?= $activeTab === 'pedidos' ? 'bg-primary text-white active fw-semibold' : 'text-secondary' ?>">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-bag-check-fill fs-5"></i>
                <span>Meus Pedidos</span>
            </div>
            <i class="bi bi-chevron-right small opacity-75"></i>
        </a>

        <a href="<?= site_url('minha-conta/enderecos') ?>"
           class="list-group-item list-group-item-action d-flex align-items-center justify-content-between rounded-3 py-2 px-3 mb-1 border-0 <?= $activeTab === 'enderecos' ? 'bg-primary text-white active fw-semibold' : 'text-secondary' ?>">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-geo-alt-fill fs-5"></i>
                <span>Endereços de Entrega</span>
            </div>
            <i class="bi bi-chevron-right small opacity-75"></i>
        </a>

        <a href="<?= site_url('minha-conta/perfil') ?>"
           class="list-group-item list-group-item-action d-flex align-items-center justify-content-between rounded-3 py-2 px-3 mb-1 border-0 <?= $activeTab === 'perfil' ? 'bg-primary text-white active fw-semibold' : 'text-secondary' ?>">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-person-gear fs-5"></i>
                <span>Dados & Segurança</span>
            </div>
            <i class="bi bi-chevron-right small opacity-75"></i>
        </a>

        <a href="<?= site_url('/') ?>"
           class="list-group-item list-group-item-action d-flex align-items-center justify-content-between rounded-3 py-2 px-3 mb-1 border-0 text-secondary">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-shop fs-5"></i>
                <span>Continuar Comprando</span>
            </div>
            <i class="bi bi-arrow-up-right small opacity-75"></i>
        </a>

        <a href="<?= site_url('logout') ?>"
           class="list-group-item list-group-item-action d-flex align-items-center justify-content-between rounded-3 py-2 px-3 border-0 text-danger">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-box-arrow-right fs-5"></i>
                <span>Sair da Conta</span>
            </div>
            <i class="bi bi-chevron-right small opacity-75"></i>
        </a>
    </div>
</div>
