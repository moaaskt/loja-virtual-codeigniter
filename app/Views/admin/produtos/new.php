<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1><i class="bi bi-plus-circle-fill text-primary me-2"></i><?= esc($title) ?></h1>
        <p class="text-muted small mb-0">Preencha os dados do novo produto</p>
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
        <form action="<?= site_url('admin/produtos/create') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

        <div class="row g-4">

            <div class="col-md-6">
                <label for="categoria_id" class="form-label">Categoria <span class="text-danger">*</span></label>
                <select name="categoria_id" id="categoria_id" class="form-select" required>
                    <option value="">Selecione uma categoria...</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= esc($categoria['id']) ?>"
                            <?= old('categoria_id') == $categoria['id'] ? 'selected' : '' ?>>
                            <?= esc($categoria['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label for="nome" class="form-label">Nome do Produto <span class="text-danger">*</span></label>
                <input type="text" name="nome" id="nome" class="form-control"
                    value="<?= old('nome') ?>" placeholder="Ex.: Tênis Air Max" required>
            </div>

            <div class="col-12">
                <label for="descricao" class="form-label">Descrição</label>
                <textarea name="descricao" id="descricao" class="form-control" rows="4"
                    placeholder="Descreva o produto..."><?= old('descricao') ?></textarea>
            </div>

            <div class="col-md-4">
                <label for="preco" class="form-label">Preço (R$) <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="number" step="0.01" name="preco" id="preco" class="form-control"
                        value="<?= old('preco') ?>" placeholder="0,00">
                </div>
            </div>

            <div class="col-12 mt-3">
                <div class="p-3 bg-light border rounded">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <p class="fw-semibold mb-0 text-muted" style="font-size:.8125rem; text-transform:uppercase; letter-spacing:.06em;">
                            <i class="bi bi-box-seam me-1"></i>Variações de Estoque (SKUs)
                        </p>
                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" id="btn-add-variacao">
                            <i class="bi bi-plus-lg me-1"></i>Adicionar Variação
                        </button>
                    </div>
                    
                    <div class="table-responsive border rounded bg-white">
                        <table class="table table-hover align-middle mb-0" id="tabela-variacoes">
                            <thead class="table-light">
                                <tr>
                                    <th>Tamanho <span class="text-danger">*</span></th>
                                    <th>Cor <span class="text-danger">*</span></th>
                                    <th>Estoque <span class="text-danger">*</span></th>
                                    <th class="text-center" style="width: 80px;">Ação</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-variacoes">
                                <!-- Será preenchido via JS -->
                            </tbody>
                        </table>
                        <div id="variacoes-empty" class="text-center p-4 text-muted">
                            Nenhuma variação adicionada. Clique em "Adicionar Variação" para começar.
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="frete_gratis" id="frete_gratis" value="1" <?= old('frete_gratis') ? 'checked' : '' ?>>
                    <label class="form-check-label fw-semibold" for="frete_gratis">Habilitar Frete Grátis para este produto</label>
                </div>
            </div>

            <div class="col-12">
                <hr class="my-1">
                <p class="fw-semibold mb-3 text-muted" style="font-size:.8125rem; text-transform:uppercase; letter-spacing:.06em;">
                    <i class="bi bi-image me-1"></i>Imagens do Produto
                </p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="imagem" class="form-label">Imagem Principal <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" id="imagem" name="imagem" accept="image/*">
                        <small class="text-muted">Upload ou URL abaixo</small>
                        <input type="url" name="url_imagem" class="form-control mt-2" placeholder="Ou cole a URL aqui..." value="<?= old('url_imagem') ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="imagens" class="form-label">Galeria de Imagens (Opcional)</label>
                        <input class="form-control" type="file" id="imagens" name="imagens[]" accept="image/*" multiple>
                        <small class="text-muted">Selecione várias fotos para a galeria</small>
                        <div id="gallery-preview" class="row g-2 mt-2"></div>
                        <textarea name="imagens_url" class="form-control mt-2" rows="3" placeholder="Ou cole as URLs das imagens aqui (uma por linha)"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 mt-4 pt-3 border-top">
            <button type="submit" class="btn btn-primary px-4 rounded-pill fw-semibold" id="btn-salvar-produto">
                <i class="bi bi-save me-2"></i>Salvar Produto
            </button>
            <a href="<?= site_url('admin/produtos') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                Cancelar
            </a>
        </div>

        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnAddVariacao = document.getElementById('btn-add-variacao');
    const tbodyVariacoes = document.getElementById('tbody-variacoes');
    const variacoesEmpty = document.getElementById('variacoes-empty');
    let variacaoIndex = 0;

    function checkEmptyState() {
        if (tbodyVariacoes.children.length === 0) {
            variacoesEmpty.style.display = 'block';
            document.getElementById('tabela-variacoes').style.display = 'none';
        } else {
            variacoesEmpty.style.display = 'none';
            document.getElementById('tabela-variacoes').style.display = 'table';
        }
    }

    btnAddVariacao.addEventListener('click', function() {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <select name="variacoes[${variacaoIndex}][tamanho]" class="form-select form-select-sm" required>
                    <option value="">Selecione...</option>
                    <option value="Único">Único</option>
                    <option value="P">P</option>
                    <option value="M">M</option>
                    <option value="G">G</option>
                    <option value="GG">GG</option>
                </select>
            </td>
            <td>
                <input type="text" name="variacoes[${variacaoIndex}][cor]" class="form-control form-control-sm" placeholder="Ex: Preto" required>
            </td>
            <td>
                <input type="number" name="variacoes[${variacaoIndex}][estoque]" class="form-control form-control-sm" placeholder="0" min="0" required>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remover-variacao" title="Remover">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        tbodyVariacoes.appendChild(tr);
        variacaoIndex++;
        checkEmptyState();
    });

    tbodyVariacoes.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remover-variacao')) {
            e.target.closest('tr').remove();
            checkEmptyState();
        }
    });

    // Iniciar estado vazio
    checkEmptyState();

    // ---- LÓGICA DE PREVIEW DE IMAGENS (GALERIA) ----
    const inputGaleria = document.getElementById('imagens');
    const galleryPreview = document.getElementById('gallery-preview');

    if (inputGaleria && galleryPreview) {
        inputGaleria.addEventListener('change', function() {
            galleryPreview.innerHTML = ''; // Limpa a galeria atual
            
            const files = Array.from(this.files);
            
            files.forEach((file) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-3 col-md-2 position-relative';
                    
                    col.innerHTML = `
                        <img src="${e.target.result}" class="img-thumbnail w-100" style="object-fit: cover; aspect-ratio: 1/1;" alt="Preview">
                    `;
                    
                    galleryPreview.appendChild(col);
                };
                reader.readAsDataURL(file);
            });
        });
    }
});
</script>

<?= $this->endSection() ?>