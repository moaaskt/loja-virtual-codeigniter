<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1><i class="bi bi-receipt-cutoff text-primary me-2"></i><?= esc($title) ?></h1>
        <p class="text-muted small mb-0">Detalhes e gerenciamento do pedido</p>
    </div>
    <a href="<?= site_url('admin/pedidos') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3" id="btn-voltar-pedidos">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

<div class="row g-4">

    <!-- ===== ORDER ITEMS ===== -->
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header fw-semibold">
                <i class="bi bi-cart3 text-primary me-2"></i>Itens do Pedido
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php foreach ($produtos as $produto): ?>
                        <li class="list-group-item d-flex align-items-center justify-content-between py-3 px-4">
                            <div class="d-flex align-items-center gap-3">
                                <img src="<?= strpos($produto['imagem'], 'http') === 0
                                    ? esc($produto['imagem'])
                                    : base_url('uploads/produtos/' . esc($produto['imagem'])) ?>"
                                    alt="<?= esc($produto['nome']) ?>"
                                    class="img-thumb-table">
                                <div>
                                    <p class="fw-semibold mb-0"><?= esc($produto['nome']) ?></p>
                                    <small class="text-muted">
                                        <?= esc($produto['quantidade']) ?> × R$ <?= esc(number_format($produto['preco_unitario'], 2, ',', '.')) ?>
                                    </small>
                                </div>
                            </div>
                            <strong class="text-nowrap">
                                R$ <?= esc(number_format($produto['preco_unitario'] * $produto['quantidade'], 2, ',', '.')) ?>
                            </strong>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- ===== ORDER DETAILS ===== -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header fw-semibold">
                <i class="bi bi-person-fill text-primary me-2"></i>Cliente & Pedido
            </div>
            <div class="card-body">
                <dl class="row g-2 mb-3">
                    <dt class="col-5 text-muted small fw-normal">Cliente</dt>
                    <dd class="col-7 fw-semibold mb-0"><?= esc($pedido['cliente_nome']) ?></dd>

                    <dt class="col-5 text-muted small fw-normal">E-mail</dt>
                    <dd class="col-7 mb-0" style="font-size:.875rem; word-break:break-all;"><?= esc($pedido['cliente_email']) ?></dd>

                    <dt class="col-5 text-muted small fw-normal">Data</dt>
                    <dd class="col-7 mb-0" style="font-size:.875rem;">
                        <?= esc(date('d/m/Y H:i', strtotime($pedido['criado_em']))) ?>
                    </dd>

                    <dt class="col-5 text-muted small fw-normal">Total</dt>
                    <dd class="col-7 mb-0 fs-5 fw-bold text-success">
                        R$ <?= esc(number_format($pedido['valor_total'], 2, ',', '.')) ?>
                    </dd>
                </dl>

                <hr>

                <p class="fw-semibold mb-2" style="font-size:.875rem;">Atualizar Status</p>
                <?= form_open('admin/pedidos/atualizar-status/' . $pedido['id']) ?>
                    <div class="d-flex gap-2">
                        <select name="status" class="form-select form-select-sm" id="select-status-pedido">
                            <?php foreach ($status_options as $status): ?>
                                <option value="<?= $status ?>"
                                    <?= ($pedido['status'] === $status) ? 'selected' : '' ?>>
                                    <?= ucfirst($status) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 flex-shrink-0" id="btn-salvar-status">
                            Salvar
                        </button>
                    </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>