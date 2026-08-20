<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1"><i class="bi bi-ticket-perforated text-danger me-2"></i>Relatório de Desempenho de Cupons</h1>
        <p class="text-muted small mb-0">Métricas de efetividade dos cupons de desconto, total de usos e economia concedida.</p>
    </div>
    <div>
        <a href="<?= site_url('admin/relatorios/exportar/cupons?periodo=' . $periodoInfo['periodo'] . '&data_inicio=' . $periodoInfo['data_inicio_formatada'] . '&data_fim=' . $periodoInfo['data_fim_formatada']) ?>" class="btn btn-outline-success btn-sm rounded-pill px-3">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Exportar Cupons (CSV)
        </a>
    </div>
</div>

<!-- ===== BARRA DE FILTRO POR PERÍODO ===== -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body p-3">
        <form method="GET" action="<?= site_url('admin/relatorios/cupons') ?>" class="row g-2 align-items-center">
            <div class="col-auto">
                <span class="text-muted small fw-semibold"><i class="bi bi-calendar-event me-1"></i>Período:</span>
            </div>
            <div class="col-auto d-flex flex-wrap gap-1">
                <a href="<?= site_url('admin/relatorios/cupons?periodo=hoje') ?>" class="btn btn-sm rounded-pill <?= $periodoInfo['periodo'] === 'hoje' ? 'btn-primary' : 'btn-outline-secondary' ?>">Hoje</a>
                <a href="<?= site_url('admin/relatorios/cupons?periodo=7d') ?>" class="btn btn-sm rounded-pill <?= $periodoInfo['periodo'] === '7d' ? 'btn-primary' : 'btn-outline-secondary' ?>">7 Dias</a>
                <a href="<?= site_url('admin/relatorios/cupons?periodo=30d') ?>" class="btn btn-sm rounded-pill <?= $periodoInfo['periodo'] === '30d' ? 'btn-primary' : 'btn-outline-secondary' ?>">30 Dias</a>
                <a href="<?= site_url('admin/relatorios/cupons?periodo=mes_atual') ?>" class="btn btn-sm rounded-pill <?= $periodoInfo['periodo'] === 'mes_atual' ? 'btn-primary' : 'btn-outline-secondary' ?>">Mês Atual</a>
                <a href="<?= site_url('admin/relatorios/cupons?periodo=ano_atual') ?>" class="btn btn-sm rounded-pill <?= $periodoInfo['periodo'] === 'ano_atual' ? 'btn-primary' : 'btn-outline-secondary' ?>">Ano Atual</a>
            </div>
            
            <div class="col-12 col-md-auto ms-md-auto d-flex align-items-center gap-2 mt-2 mt-md-0">
                <input type="hidden" name="periodo" value="custom">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-muted small">De</span>
                    <input type="date" class="form-control" name="data_inicio" value="<?= esc($periodoInfo['data_inicio_formatada']) ?>" required>
                </div>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-muted small">Até</span>
                    <input type="date" class="form-control" name="data_fim" value="<?= esc($periodoInfo['data_fim_formatada']) ?>" required>
                </div>
                <button type="submit" class="btn btn-primary btn-sm px-3 rounded-pill">
                    <i class="bi bi-funnel-fill"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ===== RESUMO DE CUPONS ===== -->
<div class="row g-3 mb-4">
    <div class="col-sm-6">
        <div class="card p-3 border-0 shadow-sm d-flex flex-row align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block">Total de Desconto Concedido</span>
                <span class="fs-4 fw-bold text-danger">R$ <?= number_format($kpis['total_descontos'], 2, ',', '.') ?></span>
            </div>
            <div class="badge bg-danger-subtle text-danger p-2 rounded-circle"><i class="bi bi-tag-fill fs-4"></i></div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="card p-3 border-0 shadow-sm d-flex flex-row align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block">Cupons Distintos Utilizados</span>
                <span class="fs-4 fw-bold text-primary"><?= count($cupons) ?></span>
            </div>
            <div class="badge bg-primary-subtle text-primary p-2 rounded-circle"><i class="bi bi-ticket-perforated-fill fs-4"></i></div>
        </div>
    </div>
</div>

<!-- ===== TABELA DE CUPONS ===== -->
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-muted">
                    <th>Código do Cupom</th>
                    <th class="text-center">Total de Utilizações</th>
                    <th class="text-end">Total em Descontos</th>
                    <th class="text-end">Faturamento com o Cupom</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($cupons)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-ticket-perforated fs-1 d-block mb-2 text-secondary"></i>
                            Nenhum cupom de desconto foi utilizado no período selecionado.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($cupons as $cp): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-warning-subtle text-warning-emphasis fs-6 px-3 py-2 border border-warning-subtle font-monospace">
                                        <?= esc($cp['codigo']) ?>
                                    </span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1"><?= $cp['total_usos'] ?> uso(s)</span>
                            </td>
                            <td class="text-end fw-bold text-danger fs-6">
                                - R$ <?= number_format((float) $cp['total_desconto'], 2, ',', '.') ?>
                            </td>
                            <td class="text-end fw-bold text-success fs-6">
                                R$ <?= number_format((float) $cp['total_faturamento'], 2, ',', '.') ?>
                            </td>
                            <td class="text-center">
                                <a href="<?= site_url('admin/relatorios/vendas?cupom=' . urlencode($cp['codigo']) . '&periodo=' . $periodoInfo['periodo'] . '&data_inicio=' . $periodoInfo['data_inicio_formatada'] . '&data_fim=' . $periodoInfo['data_fim_formatada']) ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" title="Ver Pedidos com este Cupom">
                                    <i class="bi bi-receipt me-1"></i> Ver Pedidos
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (!empty($pagerLinks) && !empty($cupons)): ?>
        <div class="card-footer bg-white border-top py-3 d-flex justify-content-center">
            <?= $pagerLinks ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
