<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container">
    <h1><?= esc($title) ?></h1>
    <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-secondary btn-sm mb-3">Voltar ao Dashboard</a>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID do Pedido</th>
                <th>Cliente</th>
                <th>Data</th>
                <th>Valor Total</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
       <tbody>
    <?php if (!empty($pedidos)): ?>
        <?php foreach ($pedidos as $pedido): ?>
            <tr>
                <td>#<?= esc($pedido['id']) ?></td>
                <td><?= esc($pedido['cliente_nome']) ?></td>
                <td><?= esc(date('d/m/Y H:i', strtotime($pedido['criado_em']))) ?></td>
                <td>R$ <?= esc(number_format($pedido['valor_total'], 2, ',', '.')) ?></td>
                <td>
                    <span class="badge <?= getStatusColorClass($pedido['status']) ?>"><?= esc(ucfirst($pedido['status'])) ?></span>
                </td>
                <td>
                    <a href="<?= site_url('admin/pedidos/detalhe/' . $pedido['id']) ?>" class="btn btn-primary btn-sm">Gerenciar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="6" class="text-center">Nenhum pedido encontrado.</td>
        </tr>
    <?php endif; ?>
</tbody>
    </table>
    <?= $pager->links('default', 'bootstrap_pagination') ?>
</div>
<?= $this->endSection() ?>