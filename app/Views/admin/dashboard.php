<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
    <h1 class="mb-4">Dashboard</h1>
    <div class="row">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total de Produtos</h5>
                    <p class="card-text fs-4"><?= $total_produtos ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total de Pedidos</h5>
                    <p class="card-text fs-4"><?= $total_pedidos ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Baixo Estoque (<=5)</h5>
                    <p class="card-text fs-4"><?= $baixo_estoque ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger mb-3">
                <div class="card-body">
                    <h5 class="card-title">Sem Estoque</h5>
                    <p class="card-text fs-4"><?= $sem_estoque ?></p>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <p>Seja bem-vindo ao painel de controle da sua loja. Utilize o menu à esquerda para navegar.</p>
<?= $this->endSection() ?>