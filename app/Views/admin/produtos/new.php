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
                    value="<?= old('nome') ?>" placeholder="Ex.: iPhone 15 128GB, Tênis Esportivo, etc." required>
            </div>

            <div class="col-12">
                <label for="descricao" class="form-label">Descrição</label>
                <textarea name="descricao" id="descricao" class="form-control" rows="4"
                    placeholder="Descreva o produto..."><?= old('descricao') ?></textarea>
            </div>

            <div class="col-md-4">
                <label for="preco" class="form-label">Preço Base (R$) <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="number" step="0.01" name="preco" id="preco" class="form-control"
                        value="<?= old('preco') ?>" placeholder="0,00" required>
                </div>
                <small class="text-muted">Preço padrão caso a variação não tenha preço específico.</small>
            </div>

            <div class="col-12 mt-3">
                <div class="p-3 bg-light border rounded">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <p class="fw-semibold mb-0 text-dark" style="font-size:.875rem; text-transform:uppercase; letter-spacing:.05em;">
                                <i class="bi bi-boxes text-primary me-1"></i>Variações de Estoque & SKUs
                            </p>
                            <small class="text-muted" id="variacoes-helper-text">Adicione opções como tamanho/numeração ou capacidade/voltagem, cor opcional e preços individuais.</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" id="btn-add-variacao">
                            <i class="bi bi-plus-lg me-1"></i>Adicionar Variação
                        </button>
                    </div>
                    
                    <div class="table-responsive border rounded bg-white">
                        <table class="table table-hover align-middle mb-0" id="tabela-variacoes">
                            <thead class="table-light">
                                <tr>
                                    <th id="th-variacao-titulo">Variação / Atributo</th>
                                    <th style="min-width: 190px;">Cor <span class="text-muted small fw-normal">(Opcional)</span></th>
                                    <th style="min-width: 160px;">Preço Individual <span class="text-muted small fw-normal">(Opcional)</span></th>
                                    <th style="min-width: 120px;">Estoque <span class="text-danger">*</span></th>
                                    <th class="text-center" style="width: 80px;">Ação</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-variacoes">
                                <!-- Será preenchido via JS -->
                            </tbody>
                        </table>
                        <div id="variacoes-empty" class="text-center p-4 text-muted">
                            <i class="bi bi-box-seam fs-3 d-block mb-2 text-secondary opacity-50"></i>
                            Nenhuma variação adicionada. Clique em "<strong>Adicionar Variação</strong>" para cadastrar SKUs.
                        </div>
                    </div>
                </div>
            </div>

            <datalist id="variacao-sugestoes">
                <option value="Único"></option>
                <option value="P"></option>
                <option value="M"></option>
                <option value="G"></option>
                <option value="GG"></option>
                <option value="128GB"></option>
                <option value="256GB"></option>
                <option value="512GB"></option>
                <option value="1TB"></option>
                <option value="110V"></option>
                <option value="220V"></option>
                <option value="Bivolt"></option>
                <option value="36"></option>
                <option value="37"></option>
                <option value="38"></option>
                <option value="39"></option>
                <option value="40"></option>
                <option value="41"></option>
                <option value="42"></option>
                <option value="43"></option>
                <option value="44"></option>
            </datalist>

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
    const selectCategoria = document.getElementById('categoria_id');
    const thVariacaoTitulo = document.getElementById('th-variacao-titulo');
    const datalistSugestoes = document.getElementById('variacao-sugestoes');
    let variacaoIndex = 0;

    // ---- CONFIGURAÇÃO DINÂMICA DE CATEGORIAS ----
    const configCategorias = {
        moda: {
            titulo: 'Tamanho / Numeração',
            placeholder: 'Ex: P, M, G, 38, 41, Único',
            sugestoes: ['PP', 'P', 'M', 'G', 'GG', 'XG', 'XGG', 'Único', '34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45']
        },
        eletronicos: {
            titulo: 'Capacidade / Voltagem / Atributo',
            placeholder: 'Ex: 128GB, 256GB, 110V, 220V, Bivolt',
            sugestoes: ['128GB', '256GB', '512GB', '1TB', '2TB', '8GB RAM', '16GB RAM', '32GB RAM', '110V', '220V', '127V', 'Bivolt']
        },
        geral: {
            titulo: 'Variação / Atributo',
            placeholder: 'Ex: 128GB, P, 110V, 41',
            sugestoes: ['Único', 'P', 'M', 'G', 'GG', '128GB', '256GB', '512GB', '1TB', '110V', '220V', 'Bivolt', '38', '39', '40', '41', '42']
        }
    };

    let currentCategoryConfig = configCategorias.geral;

    function getCategoryConfig() {
        if (!selectCategoria || selectCategoria.selectedIndex <= 0) {
            return configCategorias.geral;
        }
        const texto = selectCategoria.options[selectCategoria.selectedIndex].text;
        const normalizado = texto.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();

        if (/moda|roupa|vestuario|calcado|tenis|sapato|camis|calca|short|vestido|calcados/i.test(normalizado)) {
            return configCategorias.moda;
        }
        if (/eletron|informat|computad|celular|smartphone|eletro|tv|audio|som|gamer|fone/i.test(normalizado)) {
            return configCategorias.eletronicos;
        }
        return configCategorias.geral;
    }

    function atualizarInterfaceCategoria() {
        currentCategoryConfig = getCategoryConfig();

        // 1. Atualiza cabeçalho da tabela
        if (thVariacaoTitulo) {
            thVariacaoTitulo.innerHTML = currentCategoryConfig.titulo;
        }

        // 2. Atualiza datalist de sugestões
        if (datalistSugestoes) {
            datalistSugestoes.innerHTML = currentCategoryConfig.sugestoes
                .map(opt => `<option value="${opt}"></option>`)
                .join('');
        }

        // 3. Atualiza placeholders dos inputs existentes
        document.querySelectorAll('#tbody-variacoes input[name*="[tamanho]"]').forEach(input => {
            input.placeholder = currentCategoryConfig.placeholder;
        });
    }

    if (selectCategoria) {
        selectCategoria.addEventListener('change', atualizarInterfaceCategoria);
    }

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
                <input type="text" name="variacoes[${variacaoIndex}][tamanho]" class="form-control form-control-sm" placeholder="${currentCategoryConfig.placeholder}" list="variacao-sugestoes">
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="color" class="form-control form-control-color p-1 color-picker-input" value="#000000" style="width: 38px; height: 31px; cursor: pointer;" title="Escolha a cor">
                    <input type="text" name="variacoes[${variacaoIndex}][cor]" class="form-control form-control-sm color-text-input" placeholder="Ex: #000000 ou Preto">
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">R$</span>
                    <input type="number" step="0.01" min="0" name="variacoes[${variacaoIndex}][preco]" class="form-control form-control-sm" placeholder="Preço base">
                </div>
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

    // Sincronização inteligente entre o Color Picker e o Input Text de Cor
    tbodyVariacoes.addEventListener('input', function(e) {
        if (e.target.classList.contains('color-picker-input')) {
            const group = e.target.closest('.input-group');
            const textInput = group ? group.querySelector('.color-text-input') : null;
            if (textInput) {
                textInput.value = e.target.value;
            }
        } else if (e.target.classList.contains('color-text-input')) {
            const group = e.target.closest('.input-group');
            const pickerInput = group ? group.querySelector('.color-picker-input') : null;
            if (pickerInput && /^#[0-9A-F]{6}$/i.test(e.target.value)) {
                pickerInput.value = e.target.value;
            }
        }
    });

    // Iniciar estado e categoria inicial
    atualizarInterfaceCategoria();
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