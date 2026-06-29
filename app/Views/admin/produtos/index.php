<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1><i class="bi bi-box-seam-fill text-primary me-2"></i><?= esc($title) ?></h1>
        <p class="text-muted small mb-0">Gerencie o catálogo de produtos da loja</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?= site_url('admin/produtos/trash') ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3" id="btn-lixeira">
            <i class="bi bi-trash3 me-1"></i>Lixeira
        </a>
        <a href="<?= site_url('admin/produtos/new') ?>" class="btn btn-primary btn-sm rounded-pill px-3" id="btn-novo-produto">
            <i class="bi bi-plus-circle me-1"></i>Novo Produto
        </a>
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
                        <th>Imagem</th>
                        <th>Produto</th>
                        <th>Categoria</th>
                        <th>Preço</th>
                        <th>Estoque</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($produtos) && is_array($produtos)): ?>
                        <?php foreach ($produtos as $produto): ?>
                            <?php $semEstoque = (int)$produto['estoque'] === 0; ?>
                            <tr>
                                <td>
                                    <?php if (!empty($produto['imagem'])): ?>
                                        <img src="<?= strpos($produto['imagem'], 'http') === 0
                                            ? esc($produto['imagem'])
                                            : base_url('uploads/produtos/' . esc($produto['imagem'])) ?>"
                                            alt="<?= esc($produto['nome']) ?>"
                                            class="img-thumb-table">
                                    <?php else: ?>
                                        <div class="img-thumb-table d-flex align-items-center justify-content-center bg-light text-muted" style="width:44px;height:44px;">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="fw-semibold"><?= esc($produto['nome']) ?></span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill"
                                        style="background:#eff6ff; color:#1e40af; font-weight:600;">
                                        <?= esc($produto['categoria_nome']) ?>
                                    </span>
                                </td>
                                <td class="fw-semibold">
                                    R$ <?= esc(number_format($produto['preco'], 2, ',', '.')) ?>
                                </td>
                                <td>
                                    <?php if ($semEstoque): ?>
                                        <span class="badge rounded-pill" style="background:#fef2f2;color:#991b1b;">
                                            <i class="bi bi-x-circle me-1"></i>Esgotado
                                        </span>
                                    <?php elseif ((int)$produto['estoque'] <= 5): ?>
                                        <span class="badge rounded-pill" style="background:#fffbeb;color:#92400e;">
                                            <i class="bi bi-exclamation-triangle me-1"></i><?= esc($produto['estoque']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill" style="background:#ecfdf5;color:#065f46;">
                                            <?= esc($produto['estoque']) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="<?= site_url('admin/produtos/edit/' . $produto['id']) ?>"
                                            class="btn btn-sm btn-outline-primary"
                                            style="border-radius:8px;"
                                            id="btn-editar-produto-<?= $produto['id'] ?>">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="<?= site_url('admin/produtos/delete/' . $produto['id']) ?>"
                                            method="post" class="d-inline"
                                            onsubmit="return confirm('Mover «<?= esc($produto['nome']) ?>» para a lixeira?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                style="border-radius:8px;"
                                                id="btn-excluir-produto-<?= $produto['id'] ?>">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="bi bi-box-seam d-block mb-2" style="font-size:2rem;"></i>
                                Nenhum produto cadastrado ainda.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if (!empty($produtos)): ?>
        <div class="card-body border-top py-3 d-flex justify-content-center">
            <?= $pager->links('default', 'bootstrap_pagination') ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>