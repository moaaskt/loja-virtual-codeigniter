<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?>Nova Categoria<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4">Nova Categoria</h1>
    <a href="<?= site_url('admin/categorias') ?>" class="btn btn-secondary btn-sm mb-3">Voltar</a>

    <div class="card">
        <div class="card-body">
            <?= validation_list_errors() ?>
            <?= form_open('admin/categorias/create') ?>
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome da Categoria</label>
                    <input type="text" name="nome" class="form-control" value="<?= old('nome') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea name="descricao" class="form-control"><?= old('descricao') ?></textarea>
                </div>
                <button type="submit" class="btn btn-success">Salvar</button>
            <?= form_close() ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>