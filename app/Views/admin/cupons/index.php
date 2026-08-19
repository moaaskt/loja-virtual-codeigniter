<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1><i class="bi bi-ticket-perforated-fill text-primary me-2"></i>Cupons de Desconto</h1>
        <p class="text-muted small mb-0">Gerencie códigos promocionais e regras de desconto</p>
    </div>
    <a href="<?= site_url('admin/cupons/new') ?>" class="btn btn-primary btn-sm rounded-pill px-3" id="btn-novo-cupom">
        <i class="bi bi-plus-circle me-1"></i>Novo Cupom
    </a>
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
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Código</th>
                        <th>Desconto</th>
                        <th>Pedido Mínimo</th>
                        <th>Usos</th>
                        <th>Validade</th>
                        <th>Status</th>
                        <th style="width:140px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($cupons)): ?>
                        <?php foreach ($cupons as $cupom): ?>
                            <?php
                                $isExpirado = !empty($cupom['data_validade']) && date('Y-m-d') > $cupom['data_validade'];
                                $isEsgotado = !empty($cupom['limite_uso']) && (int)$cupom['vezes_usado'] >= (int)$cupom['limite_uso'];
                                $isAtivo    = (int)$cupom['ativo'] === 1 && !$isExpirado && !$isEsgotado;
                            ?>
                            <tr>
                                <td class="text-muted" style="font-size:.8125rem;"><?= esc($cupom['id']) ?></td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-bold fs-6 font-monospace">
                                        <i class="bi bi-tag-fill me-1"></i><?= esc($cupom['codigo']) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong>
                                        <?php if ($cupom['tipo'] === 'porcentagem'): ?>
                                            <?= esc(number_format($cupom['valor'], 0)) ?>% OFF
                                        <?php else: ?>
                                            R$ <?= esc(number_format($cupom['valor'], 2, ',', '.')) ?>
                                        <?php endif; ?>
                                    </strong>
                                </td>
                                <td class="text-muted">
                                    <?php if ((float)$cupom['valor_minimo_pedido'] > 0): ?>
                                        R$ <?= esc(number_format($cupom['valor_minimo_pedido'], 2, ',', '.')) ?>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border">Sem mínimo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?= esc($cupom['vezes_usado']) ?> / <?= !empty($cupom['limite_uso']) ? esc($cupom['limite_uso']) : '∞' ?>
                                    </span>
                                    <?php if ($isEsgotado): ?>
                                        <span class="badge bg-danger ms-1">Esgotado</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($cupom['data_validade'])): ?>
                                        <span class="<?= $isExpirado ? 'text-danger fw-bold' : 'text-muted' ?>">
                                            <?= date('d/m/Y', strtotime($cupom['data_validade'])) ?>
                                        </span>
                                        <?php if ($isExpirado): ?>
                                            <span class="badge bg-danger ms-1">Expirado</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">Indeterminada</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form action="<?= site_url('admin/cupons/toggle/' . $cupom['id']) ?>" method="post" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm <?= (int)$cupom['ativo'] === 1 ? 'btn-success' : 'btn-secondary' ?> rounded-pill px-2 py-0"
                                            style="font-size:.75rem;" title="Clique para alternar status">
                                            <i class="bi <?= (int)$cupom['ativo'] === 1 ? 'bi-check-circle-fill' : 'bi-dash-circle' ?> me-1"></i>
                                            <?= (int)$cupom['ativo'] === 1 ? 'Ativo' : 'Inativo' ?>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="<?= site_url('admin/cupons/edit/' . $cupom['id']) ?>"
                                            class="btn btn-sm btn-outline-primary"
                                            style="border-radius:8px;"
                                            title="Editar Cupom"
                                            id="btn-editar-cupom-<?= $cupom['id'] ?>">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="<?= site_url('admin/cupons/delete/' . $cupom['id']) ?>"
                                            method="post" class="d-inline"
                                            onsubmit="return confirm('Tem certeza que deseja excluir o cupom «<?= esc($cupom['codigo']) ?>»?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                style="border-radius:8px;"
                                                title="Excluir Cupom"
                                                id="btn-excluir-cupom-<?= $cupom['id'] ?>">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-ticket-perforated d-block mb-2" style="font-size:2.5rem;opacity:.4;"></i>
                                Nenhum cupom cadastrado até o momento.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if (!empty($cupons)): ?>
        <div class="card-body border-top py-3 d-flex justify-content-center">
            <?= $pager->links('default', 'bootstrap_pagination') ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
