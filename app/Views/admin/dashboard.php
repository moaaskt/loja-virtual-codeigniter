<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
    <h1 class="mb-4">Dashboard</h1>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total de Produtos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_produtos ?></div>
                        </div>
                        <div class="col-auto"><i class="bi bi-box-seam fs-2 text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total de Pedidos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_pedidos ?></div>
                        </div>
                        <div class="col-auto"><i class="bi bi-receipt fs-2 text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Baixo Estoque (≤5)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $baixo_estoque ?></div>
                        </div>
                        <div class="col-auto"><i class="bi bi-exclamation-triangle-fill fs-2 text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Sem Estoque</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $sem_estoque ?></div>
                        </div>
                        <div class="col-auto"><i class="bi bi-x-circle-fill fs-2 text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Estoque por Categoria</h6>
                </div>
                <div class="card-body">
                    <canvas id="graficoEstoqueCategoria"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status dos Pedidos</h6>
                </div>
                <div class="card-body">
                    <canvas id="graficoStatusPedidos"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Produtos Críticos (Estoque Baixo)</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                       </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
             <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Últimos Pedidos</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        </table>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Passa os dados do PHP para o JavaScript
    const statusLabels = <?= json_encode($status_pedidos_labels) ?>;
    const statusData = <?= json_encode($status_pedidos_data) ?>;

    const estoqueLabels = <?= json_encode($estoque_categoria_labels) ?>;
    const estoqueData = <?= json_encode($estoque_categoria_data) ?>;

    // Gráfico de Pizza: Status dos Pedidos
    const ctxStatus = document.getElementById('graficoStatusPedidos');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                label: '# de Pedidos',
                data: statusData,
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // Gráfico de Barras: Estoque por Categoria
    const ctxEstoque = document.getElementById('graficoEstoqueCategoria');
    new Chart(ctxEstoque, {
        type: 'bar',
        data: {
            labels: estoqueLabels,
            datasets: [{
                label: 'Quantidade em Estoque',
                data: estoqueData,
                backgroundColor: '#4e73df',
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });
    </script>

<?= $this->endSection() ?>