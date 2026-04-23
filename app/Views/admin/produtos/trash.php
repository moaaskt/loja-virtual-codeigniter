<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4"><?= esc($title) ?></h1>
    <a href="<?= site_url('admin/produtos') ?>" class="btn btn-secondary btn-sm mb-3">← Voltar aos Produtos</a>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Imagem</th>
                        <th>Nome do Produto</th>
                        <th>Categoria</th>
                        <th>Preço</th>
                        <th>Excluído em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($produtos) && is_array($produtos)): ?>
                        <?php foreach ($produtos as $produto): ?>
                            <tr>
                                <td><?= esc($produto['id']) ?></td>
                                <td>
                                    <?php if (!empty($produto['imagem'])): ?>
                                        <img src="<?= strpos($produto['imagem'], 'http') === 0 ? esc($produto['imagem']) : base_url('uploads/produtos/' . esc($produto['imagem'])) ?>"
                                            alt="<?= esc($produto['nome']) ?>" width="60" class="opacity-50">
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-decoration-line-through text-muted"><?= esc($produto['nome']) ?></td>
                                <td><?= esc($produto['categoria_nome']) ?></td>
                                <td>R$ <?= esc(number_format($produto['preco'], 2, ',', '.')) ?></td>
                                <td><?= esc($produto['deleted_at']) ?></td>
                                <td>
                                    <form action="<?= site_url('admin/produtos/restore/' . $produto['id']) ?>" method="post" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-success btn-sm">Restaurar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">A lixeira está vazia.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?= $pager->links('default', 'bootstrap_pagination') ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
