<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1><i class="bi bi-person-circle text-primary me-2"></i><?= esc($title) ?></h1>
        <p class="text-muted small mb-0">Informações completas do cliente</p>
    </div>
    <a href="<?= site_url('admin/clientes') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3" id="btn-voltar-clientes">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

<div class="row g-4">

    <!-- ===== CLIENT PROFILE ===== -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center pt-4">

                <!-- Avatar -->
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle fw-bold"
                    style="width:72px;height:72px;font-size:1.75rem;background:rgba(99,102,241,.12);color:#6366f1;">
                    <?= mb_strtoupper(mb_substr($cliente['nome'], 0, 1)) ?>
                </div>

                <h2 class="fs-5 fw-bold mb-1"><?= esc($cliente['nome']) ?></h2>
                <p class="text-muted small mb-3"><?= esc($cliente['email']) ?></p>

                <?php if ($cliente['ativo']): ?>
                    <span class="badge rounded-pill px-3 py-2 mb-3"
                        style="background:#ecfdf5;color:#065f46;font-size:.8125rem;">
                        <i class="bi bi-check-circle-fill me-1"></i>Conta Ativa
                    </span>
                <?php else: ?>
                    <span class="badge rounded-pill px-3 py-2 mb-3"
                        style="background:#f1f5f9;color:#64748b;font-size:.8125rem;">
                        <i class="bi bi-slash-circle me-1"></i>Conta Inativa
                    </span>
                <?php endif; ?>

                <hr>

                <dl class="row g-2 text-start">
                    <dt class="col-5 text-muted small fw-normal">ID</dt>
                    <dd class="col-7 mb-0 small fw-semibold">#<?= esc($cliente['id']) ?></dd>

                    <dt class="col-5 text-muted small fw-normal">Cadastro</dt>
                    <dd class="col-7 mb-0 small">
                        <?= date('d/m/Y H:i', strtotime($cliente['criado_em'])) ?>
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- ===== PURCHASE HISTORY ===== -->
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header fw-semibold">
                <i class="bi bi-clock-history text-primary me-2"></i>Histórico de Compras
                <span class="badge rounded-pill ms-2"
                    style="background:#eff6ff;color:#1e40af;font-weight:700;">
                    <?= count($pedidos) ?>
                </span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($pedidos)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-bag-x d-block mb-2" style="font-size:2rem;opacity:.4;"></i>
                        Nenhum pedido encontrado para este cliente.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Pedido</th>
                                    <th>Data</th>
                                    <th>Valor Total</th>
                                    <th>Status</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pedidos as $pedido): ?>
                                    <?php
                                    $statusStyle = 'background:#f1f5f9;color:#64748b;';
                                    if ($pedido['status'] === 'pago')      $statusStyle = 'background:#ecfdf5;color:#065f46;';
                                    elseif ($pedido['status'] === 'cancelado') $statusStyle = 'background:#fef2f2;color:#991b1b;';
                                    elseif ($pedido['status'] === 'pendente')  $statusStyle = 'background:#fffbeb;color:#92400e;';
                                    ?>
                                    <tr>
                                        <td class="fw-semibold text-primary">
                                            #<?= esc($pedido['id']) ?>
                                        </td>
                                        <td class="text-muted" style="font-size:.8125rem;">
                                            <?= date('d/m/Y H:i', strtotime($pedido['criado_em'])) ?>
                                        </td>
                                        <td class="fw-semibold">
                                            R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill" style="<?= $statusStyle ?>">
                                                <?= esc(ucfirst($pedido['status'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?= site_url('admin/pedidos/detalhe/' . $pedido['id']) ?>"
                                                class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                                id="btn-ver-pedido-<?= $pedido['id'] ?>">
                                                <i class="bi bi-eye me-1"></i>Ver
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
