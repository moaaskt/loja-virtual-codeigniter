<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?>Lista de Categorias<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1><i class="bi bi-tags-fill text-primary me-2"></i>Categorias</h1>
        <p class="text-muted small mb-0">Organize os produtos em categorias</p>
    </div>
    <a href="<?= site_url('admin/categorias/new') ?>" class="btn btn-primary btn-sm rounded-pill px-3" id="btn-nova-categoria">
        <i class="bi bi-plus-circle me-1"></i>Nova Categoria
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
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Nome da Categoria</th>
                        <th style="width:130px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categorias)): ?>
                        <?php foreach ($categorias as $categoria): ?>
                            <tr>
                                <td class="text-muted" style="font-size:.8125rem;"><?= esc($categoria['id']) ?></td>
                                <td>
                                    <span class="d-flex align-items-center gap-2">
                                        <span class="d-inline-block rounded-circle"
                                            style="width:8px;height:8px;background:#6366f1;flex-shrink:0;"></span>
                                        <strong><?= esc($categoria['nome']) ?></strong>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="<?= site_url('admin/categorias/edit/' . $categoria['id']) ?>"
                                            class="btn btn-sm btn-outline-primary"
                                            style="border-radius:8px;"
                                            id="btn-editar-categoria-<?= $categoria['id'] ?>">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="<?= site_url('admin/categorias/delete/' . $categoria['id']) ?>"
                                            method="post" class="d-inline"
                                            onsubmit="return confirm('Excluir «<?= esc($categoria['nome']) ?>»?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                style="border-radius:8px;"
                                                id="btn-excluir-categoria-<?= $categoria['id'] ?>">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted py-5">
                                <i class="bi bi-tags d-block mb-2" style="font-size:2rem;opacity:.4;"></i>
                                Nenhuma categoria cadastrada.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if (!empty($categorias)): ?>
        <div class="card-body border-top py-3 d-flex justify-content-center">
            <?= $pager->links('default', 'bootstrap_pagination') ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>