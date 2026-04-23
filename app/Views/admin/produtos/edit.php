<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1><i class="bi bi-pencil-square text-primary me-2"></i><?= esc($title) ?></h1>
        <p class="text-muted small mb-0">Atualize os dados do produto</p>
    </div>
    <a href="<?= site_url('admin/produtos') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3" id="btn-voltar-produtos">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

<?php if (!empty(\Config\Services::validation()->getErrors())): ?>
    <div class="alert alert-danger mb-3">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?= \Config\Services::validation()->listErrors() ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <?= form_open_multipart('admin/produtos/update/' . $produto['id']) ?>

        <div class="row g-4">

            <div class="col-md-6">
                <label for="categoria_id" class="form-label">Categoria <span class="text-danger">*</span></label>
                <select name="categoria_id" id="categoria_id" class="form-select" required>
                    <option value="">Selecione uma categoria...</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= esc($categoria['id']) ?>"
                            <?= old('categoria_id', $produto['categoria_id']) == $categoria['id'] ? 'selected' : '' ?>>
                            <?= esc($categoria['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label for="nome" class="form-label">Nome do Produto <span class="text-danger">*</span></label>
                <input type="text" name="nome" id="nome" class="form-control"
                    value="<?= old('nome', $produto['nome']) ?>" required>
            </div>

            <div class="col-12">
                <label for="descricao" class="form-label">Descrição</label>
                <textarea name="descricao" id="descricao" class="form-control" rows="4"><?= old('descricao', $produto['descricao']) ?></textarea>
            </div>

            <div class="col-md-4">
                <label for="preco" class="form-label">Preço (R$) <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="number" step="0.01" name="preco" id="preco" class="form-control"
                        value="<?= old('preco', $produto['preco']) ?>">
                </div>
            </div>

            <div class="col-md-4">
                <label for="estoque" class="form-label">Estoque <span class="text-danger">*</span></label>
                <input type="number" name="estoque" id="estoque" class="form-control"
                    value="<?= old('estoque', $produto['estoque']) ?>" min="0">
            </div>

            <div class="col-12">
                <hr class="my-1">
                <p class="fw-semibold mb-3 text-muted" style="font-size:.8125rem; text-transform:uppercase; letter-spacing:.06em;">
                    <i class="bi bi-image me-1"></i>Imagem do Produto
                </p>
                <div class="row g-3 align-items-start">
                    <div class="col-md-6">
                        <label for="imagem" class="form-label">Substituir por Upload</label>
                        <input class="form-control" type="file" id="imagem" name="imagem" accept="image/*">
                        <label for="url_imagem" class="form-label mt-3">OU Atualizar por URL</label>
                        <input type="url" name="url_imagem" id="url_imagem" class="form-control"
                            value="<?= old('url_imagem', strpos($produto['imagem'] ?? '', 'http') === 0 ? $produto['imagem'] : '') ?>"
                            placeholder="https://exemplo.com/imagem.jpg">
                    </div>
                    <div class="col-md-6 text-center">
                        <label class="form-label d-block mb-2">Imagem Atual</label>
                        <?php if (!empty($produto['imagem'])): ?>
                            <img src="<?= strpos($produto['imagem'], 'http') === 0
                                ? esc($produto['imagem'])
                                : base_url('uploads/produtos/' . esc($produto['imagem'])) ?>"
                                alt="<?= esc($produto['nome']) ?>"
                                style="max-height:140px; border-radius:10px; object-fit:cover; border:1px solid #e2e8f0;">
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center bg-light rounded"
                                style="height:120px; border:1px dashed #cbd5e1;">
                                <span class="text-muted small"><i class="bi bi-image me-1"></i>Sem imagem</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 mt-4 pt-3 border-top">
            <button type="submit" class="btn btn-primary px-4 rounded-pill fw-semibold" id="btn-atualizar-produto">
                <i class="bi bi-save me-2"></i>Atualizar Produto
            </button>
            <a href="<?= site_url('admin/produtos') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                Cancelar
            </a>
        </div>

        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?>