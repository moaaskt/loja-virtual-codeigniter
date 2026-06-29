<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1><i class="bi bi-trash3-fill text-danger me-2"></i><?= esc($title) ?></h1>
        <p class="text-muted small mb-0">Produtos removidos — restaure se necessário</p>
    </div>
    <a href="<?= site_url('admin/produtos') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3" id="btn-voltar-produtos">
        <i class="bi bi-arrow-left me-1"></i>Voltar aos Produtos
    </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success mb-3"><i class="bi bi-check-circle me-2"></i><?= session()->getFlashdata('success') ?></div>
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
                        <th>Excluído em</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($produtos) && is_array($produtos)): ?>
                        <?php foreach ($produtos as $produto): ?>
                            <tr class="opacity-75">
                                <td>
                                    <?php if (!empty($produto['imagem'])): ?>
                                        <img src="<?= strpos($produto['imagem'], 'http') === 0
                                            ? esc($produto['imagem'])
                                            : base_url('uploads/produtos/' . esc($produto['imagem'])) ?>"
                                            alt="<?= esc($produto['nome']) ?>"
                                            class="img-thumb-table opacity-50">
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-decoration-line-through text-muted">
                                    <?= esc($produto['nome']) ?>
                                </td>
                                <td class="text-muted"><?= esc($produto['categoria_nome']) ?></td>
                                <td class="text-muted">R$ <?= esc(number_format($produto['preco'], 2, ',', '.')) ?></td>
                                <td>
                                    <span class="badge rounded-pill" style="background:#fef2f2;color:#991b1b; font-size:.75rem;">
                                        <i class="bi bi-clock me-1"></i>
                                        <?= esc(date('d/m/Y', strtotime($produto['deleted_at']))) ?>
                                    </span>
                                </td>
                                <td>
                                    <form action="<?= site_url('admin/produtos/restore/' . $produto['id']) ?>"
                                        method="post" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-3"
                                            id="btn-restaurar-<?= $produto['id'] ?>">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>Restaurar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="bi bi-trash3 d-block mb-2" style="font-size:2rem; opacity:.4;"></i>
                                A lixeira está vazia.
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
