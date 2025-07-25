<?= $this->extend('layouts/main') ?>
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
                            <?= form_open('admin/pedidos/atualizar-status/' . $pedido['id'], ['class' => 'd-flex']) ?>
                            <select name="status" class="form-select form-select-sm">
                                <?php foreach ($status_options as $status): ?>
                                    <option value="<?= $status ?>" <?= ($pedido['status'] === $status) ? 'selected' : '' ?>>
                                        <?= ucfirst($status) // Deixa a primeira letra maiúscula ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm ms-2">Atualizar</button>
                            <?= form_close() ?>
                        </td>
                        <td>
                            <a href="#" class="btn btn-primary btn-sm">Ver Detalhes</a>
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