<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container">
    <h1><?= esc($title) ?></h1>
    <p>Bem-vindo ao painel de controle da loja.</p>

    <div class="row mt-4">

    <div class="col-md-3">
    <div class="card">
        <div class="card-body text-center">
            <h5 class="card-title">Gerenciar Pedidos</h5>
            <p class="card-text">Visualize e atualize o status dos pedidos dos clientes.</p>
            <a href="<?= site_url('admin/pedidos') ?>" class="btn btn-primary">Ir para Pedidos</a>
        </div>
    </div>
</div>



        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">Gerenciar Produtos</h5>
                    <p class="card-text">Adicione, edite e remova produtos da sua loja.</p>
                    <a href="<?= site_url('admin/produtos') ?>" class="btn btn-primary">Ir para Produtos</a>
                </div>
            </div>
        </div>




        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">Gerenciar Categorias</h5>
                    <p class="card-text">Organize seus produtos em categorias.</p>
                    <a href="<?= site_url('admin/categorias') ?>" class="btn btn-primary">Ir para Categorias</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>