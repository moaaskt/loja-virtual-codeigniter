<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4"><?= esc($title) ?></h1>
    <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-secondary btn-sm mb-3">Voltar ao Dashboard</a>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
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
                            <tr class="<?= !$cliente['ativo'] ? 'table-secondary text-muted' : '' ?>">
                                <td><?= esc($cliente['id']) ?></td>
                                <td><?= esc($cliente['nome']) ?></td>
                                <td><?= esc($cliente['email']) ?></td>
                                <td><?= esc(date('d/m/Y', strtotime($cliente['criado_em']))) ?></td>
                                <td class="text-center">
                                    <span class="badge bg-primary"><?= (int)$cliente['total_pedidos'] ?></span>
                                </td>
                                <td>
                                    <?= $cliente['ultimo_pedido']
                                        ? esc(date('d/m/Y H:i', strtotime($cliente['ultimo_pedido'])))
                                        : '<span class="text-muted">—</span>' ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($cliente['ativo']): ?>
                                        <span class="badge bg-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <form action="<?= site_url('admin/clientes/toggle/' . $cliente['id']) ?>" method="post" class="d-inline">
                                        <?= csrf_field() ?>
                                        <?php if ($cliente['ativo']): ?>
                                            <button type="submit" class="btn btn-warning btn-sm"
                                                onclick="return confirm('Desativar a conta de <?= esc($cliente['nome']) ?>?')">
                                                Desativar
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" class="btn btn-success btn-sm">
                                                Reativar
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">Nenhum cliente cadastrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?= $pager->links('default', 'bootstrap_pagination') ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
