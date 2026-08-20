<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1"><i class="bi bi-graph-up-arrow text-primary me-2"></i>Relatórios & Métricas Analíticas</h1>
        <p class="text-muted small mb-0">Visão consolidada de faturamento, pedidos, clientes e performance no período.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <div class="dropdown">
            <button class="btn btn-outline-success btn-sm rounded-pill dropdown-toggle px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Exportar Dados (CSV)
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li><a class="dropdown-item small" href="<?= site_url('admin/relatorios/exportar/resumo_kpis?periodo=' . $periodoInfo['periodo'] . '&data_inicio=' . $periodoInfo['data_inicio_formatada'] . '&data_fim=' . $periodoInfo['data_fim_formatada']) ?>"><i class="bi bi-speedometer2 text-primary me-2"></i>Resumo Geral (KPIs)</a></li>
                <li><a class="dropdown-item small" href="<?= site_url('admin/relatorios/exportar/vendas?periodo=' . $periodoInfo['periodo'] . '&data_inicio=' . $periodoInfo['data_inicio_formatada'] . '&data_fim=' . $periodoInfo['data_fim_formatada']) ?>"><i class="bi bi-cash-stack text-success me-2"></i>Todas as Vendas</a></li>
                <li><a class="dropdown-item small" href="<?= site_url('admin/relatorios/exportar/produtos?periodo=' . $periodoInfo['periodo'] . '&data_inicio=' . $periodoInfo['data_inicio_formatada'] . '&data_fim=' . $periodoInfo['data_fim_formatada']) ?>"><i class="bi bi-box-seam text-info me-2"></i>Produtos Mais Vendidos</a></li>
                <li><a class="dropdown-item small" href="<?= site_url('admin/relatorios/exportar/clientes?periodo=' . $periodoInfo['periodo'] . '&data_inicio=' . $periodoInfo['data_inicio_formatada'] . '&data_fim=' . $periodoInfo['data_fim_formatada']) ?>"><i class="bi bi-people text-warning me-2"></i>Ranking de Clientes</a></li>
                <li><a class="dropdown-item small" href="<?= site_url('admin/relatorios/exportar/cupons?periodo=' . $periodoInfo['periodo'] . '&data_inicio=' . $periodoInfo['data_inicio_formatada'] . '&data_fim=' . $periodoInfo['data_fim_formatada']) ?>"><i class="bi bi-ticket-perforated text-danger me-2"></i>Uso de Cupons</a></li>
            </ul>
        </div>
    </div>
</div>

<!-- ===== BARRA DE FILTRO POR PERÍODO ===== -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body p-3">
        <form method="GET" action="<?= site_url('admin/relatorios') ?>" class="row g-2 align-items-center" id="form-filtro-periodo">
            <div class="col-auto">
                <span class="text-muted small fw-semibold"><i class="bi bi-calendar-event me-1"></i>Período:</span>
            </div>
            <div class="col-auto d-flex flex-wrap gap-1">
                <a href="<?= site_url('admin/relatorios?periodo=hoje') ?>" class="btn btn-sm rounded-pill <?= $periodoInfo['periodo'] === 'hoje' ? 'btn-primary' : 'btn-outline-secondary' ?>">Hoje</a>
                <a href="<?= site_url('admin/relatorios?periodo=7d') ?>" class="btn btn-sm rounded-pill <?= $periodoInfo['periodo'] === '7d' ? 'btn-primary' : 'btn-outline-secondary' ?>">7 Dias</a>
                <a href="<?= site_url('admin/relatorios?periodo=30d') ?>" class="btn btn-sm rounded-pill <?= $periodoInfo['periodo'] === '30d' ? 'btn-primary' : 'btn-outline-secondary' ?>">30 Dias</a>
                <a href="<?= site_url('admin/relatorios?periodo=mes_atual') ?>" class="btn btn-sm rounded-pill <?= $periodoInfo['periodo'] === 'mes_atual' ? 'btn-primary' : 'btn-outline-secondary' ?>">Mês Atual</a>
                <a href="<?= site_url('admin/relatorios?periodo=ano_atual') ?>" class="btn btn-sm rounded-pill <?= $periodoInfo['periodo'] === 'ano_atual' ? 'btn-primary' : 'btn-outline-secondary' ?>">Ano Atual</a>
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
                    <i class="bi bi-funnel-fill"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ===== STAT CARDS (KPIs) ===== -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="--accent:#10b981;">
            <div class="stat-icon"><i class="bi bi-currency-dollar"></i></div>
            <div>
                <div class="stat-value">R$ <?= number_format($kpis['faturamento_total'], 2, ',', '.') ?></div>
                <div class="stat-label">Faturamento Líquido</div>
                <small class="text-muted" style="font-size:.75rem;"><?= $kpis['pedidos_pagos'] ?> pedido(s) faturado(s)</small>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="--accent:#6366f1;">
            <div class="stat-icon"><i class="bi bi-bag-check-fill"></i></div>
            <div>
                <div class="stat-value"><?= $kpis['total_pedidos'] ?></div>
                <div class="stat-label">Total de Pedidos</div>
                <small class="text-muted" style="font-size:.75rem;">Taxa de conversão: <?= $kpis['taxa_conversao'] ?>%</small>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="--accent:#22d3ee;">
            <div class="stat-icon"><i class="bi bi-receipt"></i></div>
            <div>
                <div class="stat-value">R$ <?= number_format($kpis['ticket_medio'], 2, ',', '.') ?></div>
                <div class="stat-label">Ticket Médio</div>
                <small class="text-muted" style="font-size:.75rem;"><?= $kpis['total_itens_vendidos'] ?> itens vendidos</small>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="--accent:#f59e0b;">
            <div class="stat-icon"><i class="bi bi-person-plus-fill"></i></div>
            <div>
                <div class="stat-value"><?= $kpis['novos_clientes'] ?></div>
                <div class="stat-label">Novos Clientes</div>
                <small class="text-muted" style="font-size:.75rem;">Cadastrados no período</small>
            </div>
        </div>
    </div>
</div>

<!-- Linha de KPIs Secundários -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card p-3 border-0 shadow-sm d-flex flex-row align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block">Descontos Concedidos (Cupons)</span>
                <span class="fw-bold text-danger fs-5">R$ <?= number_format($kpis['total_descontos'], 2, ',', '.') ?></span>
            </div>
            <div class="badge bg-danger-subtle text-danger p-2 rounded-circle"><i class="bi bi-ticket-perforated fs-5"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 border-0 shadow-sm d-flex flex-row align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block">Total Arrecadado em Frete</span>
                <span class="fw-bold text-primary fs-5">R$ <?= number_format($kpis['total_frete'], 2, ',', '.') ?></span>
            </div>
            <div class="badge bg-primary-subtle text-primary p-2 rounded-circle"><i class="bi bi-truck fs-5"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 border-0 shadow-sm d-flex flex-row align-items-center justify-content-between">
            <div>
                <span class="text-muted small d-block">Volume Total de Itens Vendidos</span>
                <span class="fw-bold text-success fs-5"><?= $kpis['total_itens_vendidos'] ?> un.</span>
            </div>
            <div class="badge bg-success-subtle text-success p-2 rounded-circle"><i class="bi bi-box-seam fs-5"></i></div>
        </div>
    </div>
</div>

<!-- ===== CHARTS ROW 1 ===== -->
<div class="row g-4 mb-4">
    <!-- Gráfico de Evolução de Vendas -->
    <div class="col-lg-8">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-0">
                <h6 class="fw-semibold mb-0"><i class="bi bi-graph-up text-primary me-2"></i>Evolução de Vendas & Faturamento</h6>
                <span class="badge bg-light text-muted"><?= esc($periodoInfo['label']) ?></span>
            </div>
            <div class="card-body">
                <div style="height: 280px; position: relative;">
                    <canvas id="chartEvolucaoVendas"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Formas de Pagamento -->
    <div class="col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-semibold mb-0"><i class="bi bi-credit-card text-success me-2"></i>Formas de Pagamento</h6>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <div style="height: 240px; width: 100%; position: relative;">
                    <canvas id="chartFormasPagamento"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== CHARTS ROW 2 ===== -->
<div class="row g-4 mb-4">
    <!-- Gráfico de Faturamento por Categoria -->
    <div class="col-lg-7">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-semibold mb-0"><i class="bi bi-tags-fill text-indigo me-2"></i>Faturamento por Categoria</h6>
            </div>
            <div class="card-body">
                <div style="height: 250px; position: relative;">
                    <canvas id="chartFaturamentoCategoria"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Status dos Pedidos -->
    <div class="col-lg-5">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-semibold mb-0"><i class="bi bi-pie-chart-fill text-info me-2"></i>Status dos Pedidos</h6>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <div style="height: 240px; width: 100%; position: relative;">
                    <canvas id="chartStatusPedidos"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== TABELAS DE DESTAQUES (TOP PRODUTOS & TOP CLIENTES) ===== -->
<div class="row g-4 mb-4">
    <!-- Top 5 Produtos -->
    <div class="col-lg-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-0">
                <h6 class="fw-semibold mb-0"><i class="bi bi-trophy-fill text-warning me-2"></i>Top 5 Produtos Mais Vendidos</h6>
                <a href="<?= site_url('admin/relatorios/produtos?periodo=' . $periodoInfo['periodo'] . '&data_inicio=' . $periodoInfo['data_inicio_formatada'] . '&data_fim=' . $periodoInfo['data_fim_formatada']) ?>" class="btn btn-link btn-sm text-decoration-none p-0">Ver todos &rarr;</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-muted">
                            <th>Produto</th>
                            <th class="text-center">Qtd.</th>
                            <th class="text-end">Receita</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($topProdutos)): ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted small">Nenhuma venda registrada no período.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($topProdutos as $index => $prod): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-light text-dark rounded-circle px-2 py-1" style="font-size:.75rem;">#<?= $index + 1 ?></span>
                                            <div>
                                                <div class="fw-medium text-dark small text-truncate" style="max-width: 220px;"><?= esc($prod['nome']) ?></div>
                                                <small class="text-muted" style="font-size:.75rem;"><?= esc($prod['categoria_nome']) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center"><span class="badge bg-primary-subtle text-primary rounded-pill"><?= $prod['total_vendido'] ?> un</span></td>
                                    <td class="text-end fw-semibold text-success small">R$ <?= number_format($prod['receita_total'], 2, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top 5 Clientes -->
    <div class="col-lg-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-0">
                <h6 class="fw-semibold mb-0"><i class="bi bi-star-fill text-warning me-2"></i>Top 5 Clientes (Maior Volume)</h6>
                <a href="<?= site_url('admin/relatorios/clientes?periodo=' . $periodoInfo['periodo'] . '&data_inicio=' . $periodoInfo['data_inicio_formatada'] . '&data_fim=' . $periodoInfo['data_fim_formatada']) ?>" class="btn btn-link btn-sm text-decoration-none p-0">Ver todos &rarr;</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-muted">
                            <th>Cliente</th>
                            <th class="text-center">Pedidos</th>
                            <th class="text-end">Total Gasto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($topClientes)): ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted small">Nenhum cliente com pedidos pagos no período.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($topClientes as $index => $cli): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-light text-dark rounded-circle px-2 py-1" style="font-size:.75rem;">#<?= $index + 1 ?></span>
                                            <div>
                                                <div class="fw-medium text-dark small"><?= esc($cli['nome']) ?></div>
                                                <small class="text-muted" style="font-size:.75rem;"><?= esc($cli['email']) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center"><span class="badge bg-info-subtle text-info-emphasis rounded-pill"><?= $cli['total_pedidos'] ?></span></td>
                                    <td class="text-end fw-semibold text-success small">R$ <?= number_format($cli['total_gasto'], 2, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Gráfico de Evolução de Vendas
    const ctxEvolucao = document.getElementById('chartEvolucaoVendas');
    if (ctxEvolucao) {
        new Chart(ctxEvolucao, {
            type: 'line',
            data: {
                labels: <?= json_encode($evolucao['labels']) ?>,
                datasets: [
                    {
                        label: 'Faturamento (R$)',
                        data: <?= json_encode($evolucao['faturamento']) ?>,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.12)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.35,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Pedidos Pagos',
                        data: <?= json_encode($evolucao['pagos']) ?>,
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.08)',
                        borderWidth: 2,
                        borderDash: [4, 4],
                        fill: false,
                        tension: 0.35,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top', labels: { boxWidth: 12, font: { size: 12 } } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (context.dataset.yAxisID === 'y') {
                                    return 'Faturamento: R$ ' + context.parsed.y.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                }
                                return 'Pedidos: ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: function(value) { return 'R$ ' + value.toLocaleString('pt-BR'); },
                            font: { size: 11 }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        grid: { drawOnChartArea: false },
                        ticks: { stepSize: 1, font: { size: 11 } }
                    },
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } }
                }
            }
        });
    }

    // 2. Gráfico de Formas de Pagamento
    const ctxPagamento = document.getElementById('chartFormasPagamento');
    if (ctxPagamento) {
        new Chart(ctxPagamento, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($pagamentos['labels']) ?>,
                datasets: [{
                    data: <?= json_encode($pagamentos['totais']) ?>,
                    backgroundColor: ['#10b981', '#6366f1', '#f59e0b', '#22d3ee', '#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 12, boxWidth: 12, font: { size: 11 } } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': R$ ' + context.parsed.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            }
                        }
                    }
                }
            }
        });
    }

    // 3. Gráfico de Faturamento por Categoria
    const ctxCategoria = document.getElementById('chartFaturamentoCategoria');
    if (ctxCategoria) {
        new Chart(ctxCategoria, {
            type: 'bar',
            data: {
                labels: <?= json_encode($categorias['labels']) ?>,
                datasets: [{
                    label: 'Faturamento (R$)',
                    data: <?= json_encode($categorias['totais']) ?>,
                    backgroundColor: 'rgba(99, 102, 241, 0.8)',
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Faturamento: R$ ' + context.parsed.y.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: function(value) { return 'R$ ' + value.toLocaleString('pt-BR'); },
                            font: { size: 11 }
                        }
                    },
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } }
                }
            }
        });
    }

    // 4. Gráfico de Status dos Pedidos
    const ctxStatus = document.getElementById('chartStatusPedidos');
    if (ctxStatus) {
        new Chart(ctxStatus, {
            type: 'pie',
            data: {
                labels: <?= json_encode($statusDist['labels']) ?>,
                datasets: [{
                    data: <?= json_encode($statusDist['data']) ?>,
                    backgroundColor: ['#f59e0b', '#10b981', '#6366f1', '#22d3ee', '#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 12, boxWidth: 12, font: { size: 11 } } }
                }
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
