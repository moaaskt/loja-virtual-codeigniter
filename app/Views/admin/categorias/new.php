<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?>Nova Categoria<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1><i class="bi bi-plus-circle-fill text-primary me-2"></i>Nova Categoria</h1>
        <p class="text-muted small mb-0">Adicione uma nova categoria ao catálogo</p>
    </div>
    <a href="<?= site_url('admin/categorias') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3" id="btn-voltar-categorias">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

<div class="card" style="max-width:640px;">
    <div class="card-body">
        <?= validation_list_errors() ?>

        <?= form_open('admin/categorias/create') ?>

        <div class="mb-3">
            <label for="nome" class="form-label">Nome da Categoria <span class="text-danger">*</span></label>
            <input type="text" name="nome" id="nome" class="form-control"
                value="<?= old('nome') ?>" placeholder="Ex.: Calçados" required>
        </div>

        <div class="mb-4">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea name="descricao" id="descricao" class="form-control" rows="3"
                placeholder="Descreva brevemente esta categoria..."><?= old('descricao') ?></textarea>
        </div>

        <div class="d-flex gap-2 pt-2 border-top">
            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold" id="btn-salvar-categoria">
                <i class="bi bi-save me-2"></i>Salvar Categoria
            </button>
            <a href="<?= site_url('admin/categorias') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                Cancelar
            </a>
        </div>

        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>