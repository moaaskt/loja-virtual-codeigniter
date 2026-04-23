<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1><i class="bi bi-people-fill text-primary me-2"></i><?= esc($title) ?></h1>
        <p class="text-muted small mb-0">Lista completa de clientes cadastrados</p>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success mb-3"><i class="bi bi-check-circle me-2"></i><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger mb-3"><i class="bi bi-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>E-mail</th>
                        <th>Cadastro</th>
                        <th class="text-center">Pedidos</th>
                        <th>Último Pedido</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($clientes) && is_array($clientes)): ?>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr class="<?= !$cliente['ativo'] ? 'opacity-60' : '' ?>">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <!-- Avatar inicial -->
                                        <span class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0 fw-bold"
                                            style="width:36px;height:36px;font-size:.75rem;background:<?= $cliente['ativo'] ? 'rgba(99,102,241,.12)' : '#f1f5f9' ?>;color:<?= $cliente['ativo'] ? '#6366f1' : '#94a3b8' ?>;">
                                            <?= mb_strtoupper(mb_substr($cliente['nome'], 0, 1)) ?>
                                        </span>
                                        <span class="fw-semibold"><?= esc($cliente['nome']) ?></span>
                                    </div>
                                </td>
                                <td class="text-muted" style="font-size:.875rem;"><?= esc($cliente['email']) ?></td>
                                <td class="text-muted" style="font-size:.8125rem;">
                                    <?= esc(date('d/m/Y', strtotime($cliente['criado_em']))) ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill"
                                        style="background:#eff6ff;color:#1e40af;font-weight:700;min-width:28px;">
                                        <?= (int)$cliente['total_pedidos'] ?>
                                    </span>
                                </td>
                                <td class="text-muted" style="font-size:.8125rem;">
                                    <?= $cliente['ultimo_pedido']
                                        ? esc(date('d/m/Y H:i', strtotime($cliente['ultimo_pedido'])))
                                        : '<span class="text-muted">—</span>' ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($cliente['ativo']): ?>
                                        <span class="badge rounded-pill" style="background:#ecfdf5;color:#065f46;">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill" style="background:#f1f5f9;color:#64748b;">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <a href="<?= site_url('admin/clientes/show/' . $cliente['id']) ?>"
                                            class="btn btn-sm btn-outline-primary"
                                            style="border-radius:8px;"
                                            id="btn-detalhes-cliente-<?= $cliente['id'] ?>">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <form action="<?= site_url('admin/clientes/toggle/' . $cliente['id']) ?>"
                                            method="post" class="d-inline"
                                            onsubmit="return confirm('<?= $cliente['ativo'] ? 'Desativar' : 'Reativar' ?> a conta de <?= esc($cliente['nome']) ?>?')">
                                            <?= csrf_field() ?>
                                            <?php if ($cliente['ativo']): ?>
                                                <button type="submit" class="btn btn-sm btn-outline-warning"
                                                    style="border-radius:8px;"
                                                    id="btn-toggle-cliente-<?= $cliente['id'] ?>">
                                                    <i class="bi bi-person-dash"></i>
                                                </button>
                                            <?php else: ?>
                                                <button type="submit" class="btn btn-sm btn-outline-success"
                                                    style="border-radius:8px;"
                                                    id="btn-toggle-cliente-<?= $cliente['id'] ?>">
                                                    <i class="bi bi-person-check"></i>
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-people d-block mb-2" style="font-size:2rem;opacity:.4;"></i>
                                Nenhum cliente cadastrado.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if (!empty($clientes)): ?>
        <div class="card-body border-top py-3 d-flex justify-content-center">
            <?= $pager->links('default', 'bootstrap_pagination') ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
