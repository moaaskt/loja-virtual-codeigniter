<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1><i class="bi bi-receipt-cutoff text-primary me-2"></i><?= esc($title) ?></h1>
        <p class="text-muted small mb-0">Acompanhe e gerencie todos os pedidos</p>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Pedido</th>
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
                                <td>
                                    <span class="fw-semibold text-primary">#<?= esc($pedido['id']) ?></span>
                                </td>
                                <td><?= esc($pedido['cliente_nome']) ?></td>
                                <td class="text-muted" style="font-size:.8125rem;">
                                    <?= esc(date('d/m/Y H:i', strtotime($pedido['criado_em']))) ?>
                                </td>
                                <td class="fw-semibold">
                                    R$ <?= esc(number_format($pedido['valor_total'], 2, ',', '.')) ?>
                                </td>
                                <td>
                                    <span class="badge rounded-pill <?= getStatusColorClass($pedido['status']) ?>">
                                        <?= esc(ucfirst($pedido['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= site_url('admin/pedidos/detalhe/' . $pedido['id']) ?>"
                                        class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                        id="btn-gerenciar-pedido-<?= $pedido['id'] ?>">
                                        <i class="bi bi-sliders me-1"></i>Gerenciar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="bi bi-receipt d-block mb-2" style="font-size:2rem;opacity:.4;"></i>
                                Nenhum pedido encontrado.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if (!empty($pedidos)): ?>
        <div class="card-body border-top py-3 d-flex justify-content-center">
            <?= $pager->links('default', 'bootstrap_pagination') ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>