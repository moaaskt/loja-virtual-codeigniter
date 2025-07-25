<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4"><?= esc($title) ?></h1>
    <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-secondary btn-sm mb-3">Voltar ao Dashboard</a>
    <a href="<?= site_url('admin/produtos/new') ?>" class="btn btn-primary btn-sm mb-3">Adicionar Novo Produto</a>

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
                        <th>Estoque</th>
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
                                        <img src="<?= base_url('uploads/produtos/' . esc($produto['imagem'])) ?>" alt="<?= esc($produto['nome']) ?>" width="80">
                                    <?php else: ?>
                                        <span class="text-muted">Sem Imagem</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($produto['nome']) ?></td>
                                <td><?= esc($produto['categoria_nome']) ?></td>
                                <td>R$ <?= esc(number_format($produto['preco'], 2, ',', '.')) ?></td>
                                <td><?= esc($produto['estoque']) ?></td>
                                <td>
                                    <a href="<?= site_url('admin/produtos/edit/' . $produto['id']) ?>" class="btn btn-warning btn-sm">Editar</a>
                                    <form action="<?= site_url('admin/produtos/delete/' . $produto['id']) ?>" method="post" class="d-inline">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza?');">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Nenhum produto encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?= $pager->links('default', 'bootstrap_pagination') ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>