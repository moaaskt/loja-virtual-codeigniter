<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?>Lista de Categorias<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4">Lista de Categorias</h1>
    <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-secondary btn-sm mb-3">Voltar ao Dashboard</a>
    <a href="<?= site_url('admin/categorias/new') ?>" class="btn btn-primary btn-sm mb-3">Adicionar Nova Categoria</a>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categorias)): ?>
                        <?php foreach ($categorias as $categoria): ?>
                            <tr>
                                <td><?= esc($categoria['id']) ?></td>
                                <td><?= esc($categoria['nome']) ?></td>
                                <td>
                                    <a href="<?= site_url('admin/categorias/edit/' . $categoria['id']) ?>" class="btn btn-warning btn-sm">Editar</a>
                                    <form action="<?= site_url('admin/categorias/delete/' . $categoria['id']) ?>" method="post" class="d-inline">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza?');">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">Nenhuma categoria encontrada.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?= $pager->links('default', 'bootstrap_pagination') ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>