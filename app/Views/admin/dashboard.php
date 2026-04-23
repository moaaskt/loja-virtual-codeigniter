<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1><i class="bi bi-speedometer2 text-primary me-2"></i>Dashboard</h1>
    <span class="text-muted small">
        <i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y') ?>
    </span>
</div>

<!-- ===== STAT CARDS ===== -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="--accent:#6366f1;">
            <div class="stat-icon"><i class="bi bi-box-seam-fill"></i></div>
            <div>
                <div class="stat-value"><?= $total_produtos ?></div>
                <div class="stat-label">Total de Produtos</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="--accent:#22d3ee;">
            <div class="stat-icon"><i class="bi bi-receipt-cutoff"></i></div>
            <div>
                <div class="stat-value"><?= $total_pedidos ?></div>
                <div class="stat-label">Total de Pedidos</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="--accent:#f59e0b;">
            <div class="stat-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div>
                <div class="stat-value"><?= $baixo_estoque ?></div>
                <div class="stat-label">Baixo Estoque (≤5)</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="--accent:#ef4444;">
            <div class="stat-icon"><i class="bi bi-x-circle-fill"></i></div>
            <div>
                <div class="stat-value"><?= $sem_estoque ?></div>
                <div class="stat-label">Sem Estoque</div>
            </div>
        </div>
    </div>
</div>

<!-- ===== CHARTS ===== -->
<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span class="fw-semibold"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Estoque por Categoria</span>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="graficoEstoqueCategoria"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span class="fw-semibold"><i class="bi bi-pie-chart-fill text-primary me-2"></i>Status dos Pedidos</span>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div class="chart-container w-100">
                    <canvas id="graficoStatusPedidos"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== QUICK LINKS ===== -->
<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header fw-semibold">
                <i class="bi bi-lightning-charge-fill text-warning me-2"></i>Ações Rápidas
            </div>
            <div class="card-body d-flex flex-wrap gap-2">
                <a href="<?= site_url('admin/produtos/new') ?>" class="btn btn-primary btn-sm rounded-pill px-3" id="btn-novo-produto">
                    <i class="bi bi-plus-circle me-1"></i>Novo Produto
                </a>
                <a href="<?= site_url('admin/categorias/new') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3" id="btn-nova-categoria">
                    <i class="bi bi-tags me-1"></i>Nova Categoria
                </a>
                <a href="<?= site_url('admin/pedidos') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3" id="btn-ver-pedidos">
                    <i class="bi bi-receipt me-1"></i>Ver Pedidos
                </a>
                <a href="<?= site_url('/') ?>" target="_blank" class="btn btn-outline-success btn-sm rounded-pill px-3" id="btn-ver-loja">
                    <i class="bi bi-shop me-1"></i>Ver Loja
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header fw-semibold">
                <i class="bi bi-info-circle-fill text-primary me-2"></i>Status do Sistema
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small text-muted">Produtos com estoque baixo</span>
                    <span class="badge rounded-pill" style="background:#fffbeb; color:#92400e;">
                        <?= $baixo_estoque ?> produto(s)
                    </span>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="small text-muted">Produtos sem estoque</span>
                    <span class="badge rounded-pill" style="background:#fef2f2; color:#991b1b;">
                        <?= $sem_estoque ?> produto(s)
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const statusLabels = <?= json_encode($status_pedidos_labels) ?>;
const statusData   = <?= json_encode($status_pedidos_data) ?>;
const estoqueLabels = <?= json_encode($estoque_categoria_labels) ?>;
const estoqueData   = <?= json_encode($estoque_categoria_data) ?>;

// Doughnut — Order Status
new Chart(document.getElementById('graficoStatusPedidos'), {
    type: 'doughnut',
    data: {
        labels: statusLabels,
        datasets: [{
            data: statusData,
            backgroundColor: ['#6366f1','#10b981','#22d3ee','#f59e0b','#ef4444'],
            borderWidth: 0,
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { padding: 16, boxWidth: 12, font: { size: 12 } } }
        },
        cutout: '65%'
    }
});

// Bar — Stock by Category
new Chart(document.getElementById('graficoEstoqueCategoria'), {
    type: 'bar',
    data: {
        labels: estoqueLabels,
        datasets: [{
            label: 'Estoque',
            data: estoqueData,
            backgroundColor: 'rgba(99,102,241,.75)',
            borderRadius: 6,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 } } },
            x: { grid: { display: false }, ticks: { font: { size: 11 } } }
        }
    }
});
</script>

<?= $this->endSection() ?>