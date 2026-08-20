<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1"><i class="bi bi-people text-warning me-2"></i>Relatório de Clientes & Compras (LTV)</h1>
        <p class="text-muted small mb-0">Análise de clientes com maior volume financeiro, frequência de compras e ticket médio.</p>
    </div>
    <div>
        <a href="<?= site_url('admin/relatorios/exportar/clientes?periodo=' . $periodoInfo['periodo'] . '&data_inicio=' . $periodoInfo['data_inicio_formatada'] . '&data_fim=' . $periodoInfo['data_fim_formatada']) ?>" class="btn btn-outline-success btn-sm rounded-pill px-3">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Exportar Clientes (CSV)
        </a>
    </div>
</div>

<!-- ===== BARRA DE FILTRO POR PERÍODO ===== -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body p-3">
        <form method="GET" action="<?= site_url('admin/relatorios/clientes') ?>" class="row g-2 align-items-center">
            <div class="col-auto">
                <span class="text-muted small fw-semibold"><i class="bi bi-calendar-event me-1"></i>Período:</span>
            </div>
            <div class="col-auto d-flex flex-wrap gap-1">
                <a href="<?= site_url('admin/relatorios/clientes?periodo=hoje') ?>" class="btn btn-sm rounded-pill <?= $periodoInfo['periodo'] === 'hoje' ? 'btn-primary' : 'btn-outline-secondary' ?>">Hoje</a>
                <a href="<?= site_url('admin/relatorios/clientes?periodo=7d') ?>" class="btn btn-sm rounded-pill <?= $periodoInfo['periodo'] === '7d' ? 'btn-primary' : 'btn-outline-secondary' ?>">7 Dias</a>
                <a href="<?= site_url('admin/relatorios/clientes?periodo=30d') ?>" class="btn btn-sm rounded-pill <?= $periodoInfo['periodo'] === '30d' ? 'btn-primary' : 'btn-outline-secondary' ?>">30 Dias</a>
                <a href="<?= site_url('admin/relatorios/clientes?periodo=mes_atual') ?>" class="btn btn-sm rounded-pill <?= $periodoInfo['periodo'] === 'mes_atual' ? 'btn-primary' : 'btn-outline-secondary' ?>">Mês Atual</a>
                <a href="<?= site_url('admin/relatorios/clientes?periodo=ano_atual') ?>" class="btn btn-sm rounded-pill <?= $periodoInfo['periodo'] === 'ano_atual' ? 'btn-primary' : 'btn-outline-secondary' ?>">Ano Atual</a>
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

<!-- ===== TABELA DE CLIENTES ===== -->
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-muted">
                    <th class="text-center" style="width:60px;">Posição</th>
                    <th>Cliente</th>
                    <th>E-mail</th>
                    <th class="text-center">Qtd. Pedidos</th>
                    <th class="text-end">Ticket Médio</th>
                    <th class="text-end">Total Gasto</th>
                    <th>Último Pedido</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($clientes)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 d-block mb-2 text-secondary"></i>
                            Nenhum cliente com pedidos no período selecionado.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($clientes as $index => $c): ?>
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-light text-muted rounded-circle px-2 py-1">#<?= $index + 1 ?></span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="sidebar-avatar" style="width:34px;height:34px;font-size:.8rem;">
                                        <?= mb_strtoupper(mb_substr($c['nome'] ?? 'C', 0, 1)) ?>
                                    </span>
                                    <div class="fw-semibold text-dark"><?= esc($c['nome']) ?></div>
                                </div>
                            </td>
                            <td class="small text-muted"><?= esc($c['email']) ?></td>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1"><?= $c['total_pedidos'] ?> pedido(s)</span>
                            </td>
                            <td class="text-end small text-muted">
                                R$ <?= number_format((float) $c['ticket_medio'], 2, ',', '.') ?>
                            </td>
                            <td class="text-end fw-bold text-success fs-6">
                                R$ <?= number_format((float) $c['total_gasto'], 2, ',', '.') ?>
                            </td>
                            <td class="small text-muted">
                                <?= $c['ultimo_pedido'] ? date('d/m/Y H:i', strtotime($c['ultimo_pedido'])) : 'N/A' ?>
                            </td>
                            <td class="text-center">
                                <a href="<?= site_url('admin/clientes/show/' . $c['id']) ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1" title="Ver Perfil do Cliente">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
