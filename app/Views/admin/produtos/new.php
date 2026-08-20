<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1 text-gray-800"><i class="bi bi-plus-circle-fill text-primary me-2"></i><?= esc($title) ?></h1>
        <p class="text-muted small mb-0">Cadastre um novo produto com suporte a múltiplos atributos e SKUs complexos</p>
    </div>
    <a href="<?= site_url('admin/produtos') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm" id="btn-voltar-produtos">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

<?php if (!empty(\Config\Services::validation()->getErrors())): ?>
    <div class="alert alert-danger shadow-sm mb-4 border-0 border-start border-4 border-danger">
        <div class="d-flex align-items-center mb-1">
            <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
            <strong class="text-danger">Atenção! Corrija os erros abaixo:</strong>
        </div>
        <?= \Config\Services::validation()->listErrors() ?>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-body p-4">
        <form action="<?= site_url('admin/produtos/create') ?>" method="post" enctype="multipart/form-data" id="form-produto">
            <?= csrf_field() ?>

        <div class="row g-4">

            <!-- Categoria e Nome -->
            <div class="col-md-5">
                <label for="categoria_id" class="form-label fw-semibold">Categoria <span class="text-danger">*</span></label>
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

            <div class="col-md-7">
                <label for="nome" class="form-label fw-semibold">Nome do Produto <span class="text-danger">*</span></label>
                <input type="text" name="nome" id="nome" class="form-control"
                    value="<?= old('nome') ?>" placeholder="Ex.: Smartphone Galaxy S24 Ultra, Tênis Nike Air, etc." required>
            </div>

            <div class="col-12">
                <label for="descricao" class="form-label fw-semibold">Descrição</label>
                <textarea name="descricao" id="descricao" class="form-control" rows="3"
                    placeholder="Descreva as características e diferenciais do produto..."><?= old('descricao') ?></textarea>
            </div>

            <div class="col-md-4">
                <label for="preco" class="form-label fw-semibold">Preço Base (R$) <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light fw-bold text-muted">R$</span>
                    <input type="number" step="0.01" min="0" name="preco" id="preco" class="form-control"
                        value="<?= old('preco') ?>" placeholder="0,00" required>
                </div>
                <small class="text-muted">Preço padrão caso a variação não tenha preço específico.</small>
            </div>

            <div class="col-md-8">
                <div class="form-check form-switch mt-4 pt-2">
                    <input class="form-check-input" type="checkbox" name="frete_gratis" id="frete_gratis" value="1" <?= old('frete_gratis') ? 'checked' : '' ?>>
                    <label class="form-check-label fw-semibold text-dark" for="frete_gratis">
                        <i class="bi bi-truck text-success me-1"></i>Habilitar Frete Grátis para este produto
                    </label>
                </div>
            </div>

            <!-- SEÇÃO DE VARIAÇÕES MULTI-ATRIBUTOS -->
            <div class="col-12 mt-4">
                <div class="border rounded-3 p-4 bg-light shadow-sm">
                    
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark d-flex align-items-center">
                                <i class="bi bi-diagram-3-fill text-primary me-2"></i>Variações Multi-Atributos & Grade de SKUs
                            </h5>
                            <p class="text-muted small mb-0">Crie múltiplos eixos (ex: Cor, Armazenamento, RAM, Voltagem, Tamanho) e gere as combinações com 1 clique.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle rounded-pill px-3" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-lightning-charge me-1"></i>Presets Rápidos
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li><button type="button" class="dropdown-item" id="preset-smartphone"><i class="bi bi-phone me-2 text-primary"></i>Smartphones / Celulares (Cor + Armaz. + RAM)</button></li>
                                    <li><button type="button" class="dropdown-item" id="preset-moda"><i class="bi bi-tag me-2 text-success"></i>Moda & Vestuário (Cor + Tamanho)</button></li>
                                    <li><button type="button" class="dropdown-item" id="preset-calcados"><i class="bi bi-shoe-prints me-2 text-warning"></i>Calçados & Tênis (Cor + Numeração)</button></li>
                                    <li><button type="button" class="dropdown-item" id="preset-eletro"><i class="bi bi-plug me-2 text-danger"></i>Eletrodomésticos (Cor + Voltagem)</button></li>
                                </ul>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" id="btn-add-atributo">
                                <i class="bi bi-plus-circle me-1"></i>Adicionar Atributo
                            </button>
                        </div>
                    </div>

                    <!-- CONSTRUTOR DE ATRIBUTOS (GRADE) -->
                    <div id="container-atributos" class="p-3 bg-white border rounded-3 mb-3" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold text-secondary small text-uppercase" style="letter-spacing: .05em;">
                                <i class="bi bi-sliders me-1"></i>Configurador de Eixos e Valores
                            </span>
                            <small class="text-muted">Separe múltiplos valores por vírgula (Ex: 128GB, 256GB)</small>
                        </div>
                        
                        <div id="lista-atributos" class="row g-3 mb-3">
                            <!-- Inserido dinamicamente via JS -->
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="small text-muted" id="resumo-combinacoes">Nenhum atributo configurado.</span>
                            <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold shadow-sm" id="btn-gerar-grade">
                                <i class="bi bi-magic me-1"></i>⚡ Gerar Grade de Combinações (SKUs)
                            </button>
                        </div>
                    </div>

                    <!-- BARRA DE AÇÕES EM LOTE (BULK EDIT) -->
                    <div id="bulk-edit-bar" class="p-2 px-3 bg-white border rounded-3 mb-3 d-flex flex-wrap align-items-center justify-content-between gap-2" style="display: none;">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="small fw-semibold text-muted"><i class="bi bi-check2-all text-primary me-1"></i>Ações em Lote:</span>
                            
                            <div class="input-group input-group-sm" style="width: 220px;">
                                <span class="input-group-text">Preço R$</span>
                                <input type="number" step="0.01" min="0" id="bulk-preco-input" class="form-control" placeholder="Ex: 1999.00">
                                <button type="button" class="btn btn-outline-secondary" id="btn-apply-bulk-preco" title="Aplicar a todos">Aplicar</button>
                            </div>

                            <div class="input-group input-group-sm" style="width: 190px;">
                                <span class="input-group-text">Estoque</span>
                                <input type="number" min="0" id="bulk-estoque-input" class="form-control" placeholder="Ex: 10">
                                <button type="button" class="btn btn-outline-secondary" id="btn-apply-bulk-estoque" title="Aplicar a todos">Aplicar</button>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="btn-add-variacao-manual">
                                <i class="bi bi-plus-lg me-1"></i>Adicionar Manualmente
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2" id="btn-limpar-todas-variacoes" title="Limpar grade">
                                <i class="bi bi-trash"></i> Limpar
                            </button>
                        </div>
                    </div>

                    <!-- TABELA DE SKUS / VARIAÇÕES GERADAS -->
                    <div class="table-responsive border rounded-3 bg-white shadow-sm">
                        <table class="table table-hover align-middle mb-0" id="tabela-variacoes">
                            <thead class="table-light">
                                <tr class="text-secondary small">
                                    <th style="min-width: 200px;">Variação / SKU</th>
                                    <th style="min-width: 180px;">Cor / Swatch <span class="text-muted fw-normal">(Opcional)</span></th>
                                    <th style="min-width: 150px;">Preço (R$) <span class="text-muted fw-normal">(Opcional)</span></th>
                                    <th style="min-width: 120px;">Estoque <span class="text-danger">*</span></th>
                                    <th style="min-width: 220px;">Foto da Variação <span class="text-muted fw-normal">(URL)</span></th>
                                    <th class="text-center" style="width: 70px;">Ação</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-variacoes">
                                <!-- Preenchido dinamicamente via JS -->
                            </tbody>
                        </table>
                        <div id="variacoes-empty" class="text-center p-5 text-muted">
                            <i class="bi bi-boxes fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            <h6 class="fw-semibold text-secondary">Nenhuma variação gerada</h6>
                            <p class="small text-muted mb-3">Utilize os <strong>Presets Rápidos</strong> acima ou clique em <strong>Adicionar Atributo</strong> para gerar as combinações automaticamente.</p>
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-4" id="btn-start-presets">
                                <i class="bi bi-magic me-1"></i>Abrir Configurador de Atributos
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <!-- IMAGENS DO PRODUTO (GALERIA) -->
            <div class="col-12 mt-4">
                <hr class="my-2">
                <h5 class="fw-bold mb-3 text-dark">
                    <i class="bi bi-images text-primary me-2"></i>Imagens Principais e Galeria
                </h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <label for="imagem" class="form-label fw-semibold">Imagem Principal (Capa) <span class="text-danger">*</span></label>
                            <input class="form-control" type="file" id="imagem" name="imagem" accept="image/*">
                            <small class="text-muted d-block mt-1">Upload de arquivo ou cole a URL abaixo:</small>
                            <input type="url" name="url_imagem" id="url_imagem" class="form-control mt-2" placeholder="https://exemplo.com/imagem.jpg" value="<?= old('url_imagem') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <label for="imagens" class="form-label fw-semibold">Galeria de Fotos Extras (Opcional)</label>
                            <input class="form-control" type="file" id="imagens" name="imagens[]" accept="image/*" multiple>
                            <div id="gallery-preview" class="row g-2 mt-2"></div>
                            <textarea name="imagens_url" class="form-control mt-2" rows="2" placeholder="Ou cole as URLs das imagens extras (uma por linha)"></textarea>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="d-flex gap-2 mt-4 pt-3 border-top justify-content-end">
            <a href="<?= site_url('admin/produtos') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                Cancelar
            </a>
            <button type="submit" class="btn btn-primary px-5 rounded-pill fw-semibold shadow-sm" id="btn-salvar-produto">
                <i class="bi bi-save me-2"></i>Salvar Produto
            </button>
        </div>

        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const containerAtributos = document.getElementById('container-atributos');
    const listaAtributos     = document.getElementById('lista-atributos');
    const btnAddAtributo     = document.getElementById('btn-add-atributo');
    const btnGerarGrade      = document.getElementById('btn-gerar-grade');
    const tbodyVariacoes     = document.getElementById('tbody-variacoes');
    const variacoesEmpty     = document.getElementById('variacoes-empty');
    const bulkEditBar        = document.getElementById('bulk-edit-bar');
    const resumoCombinacoes  = document.getElementById('resumo-combinacoes');
    const selectCategoria    = document.getElementById('categoria_id');
    const inputNomeProduto   = document.getElementById('nome');
    const inputPrecoBase     = document.getElementById('preco');

    let variacaoIndex = 0;
    let atributosDefinidos = []; // [{ nome: 'Cor', valores: ['Azul', 'Vermelho'] }, ...]

    // Mapa de cores comuns para Hexadecimal
    const corNomesMap = {
        'preto': '#000000', 'black': '#000000',
        'branco': '#ffffff', 'white': '#ffffff',
        'cinza': '#6c757d', 'cinza espacial': '#4a4d52', 'space gray': '#4a4d52', 'gray': '#6c757d',
        'prata': '#d1d5db', 'silver': '#d1d5db',
        'dourado': '#f59e0b', 'ouro': '#f59e0b', 'gold': '#f59e0b',
        'vermelho': '#ef4444', 'red': '#ef4444',
        'azul': '#3b82f6', 'blue': '#3b82f6', 'azul marinho': '#1e3a8a', 'navy': '#1e3a8a',
        'verde': '#10b981', 'green': '#10b981',
        'amarelo': '#eab308', 'yellow': '#eab308',
        'laranja': '#f97316', 'orange': '#f97316',
        'rosa': '#ec4899', 'pink': '#ec4899',
        'roxo': '#8b5cf6', 'purple': '#8b5cf6',
        'titanio': '#878681', 'titânio': '#878681', 'titanio natural': '#9a948d'
    };

    function renderAtributos() {
        listaAtributos.innerHTML = '';
        if (atributosDefinidos.length === 0) {
            containerAtributos.style.display = 'none';
            return;
        }
        containerAtributos.style.display = 'block';

        atributosDefinidos.forEach((attr, idx) => {
            const col = document.createElement('div');
            col.className = 'col-md-6';
            col.innerHTML = `
                <div class="card border p-2 bg-light shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <input type="text" class="form-control form-control-sm fw-semibold attr-nome" data-idx="${idx}" value="${attr.nome}" placeholder="Nome (Ex: Cor, Armazenamento, Tamanho)" style="max-width: 180px;">
                        <button type="button" class="btn btn-sm text-danger p-0 ms-2 btn-remove-attr" data-idx="${idx}" title="Remover Atributo">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                    </div>
                    <div>
                        <input type="text" class="form-control form-control-sm attr-valores" data-idx="${idx}" value="${attr.valores.join(', ')}" placeholder="Valores separados por vírgula (Ex: 128GB, 256GB, 512GB)">
                    </div>
                </div>
            `;
            listaAtributos.appendChild(col);
        });

        atualizarResumoCombinacoes();
    }

    function atualizarResumoCombinacoes() {
        let total = 1;
        let validos = 0;
        atributosDefinidos.forEach(a => {
            if (a.nome.trim() && a.valores.length > 0) {
                total *= a.valores.length;
                validos++;
            }
        });
        if (validos === 0) {
            resumoCombinacoes.innerHTML = 'Nenhum atributo válido.';
        } else {
            resumoCombinacoes.innerHTML = `<strong>${validos}</strong> atributo(s) gerará <strong>${total}</strong> combinação(ões) de SKUs.`;
        }
    }

    function checkEmptyState() {
        if (tbodyVariacoes.children.length === 0) {
            variacoesEmpty.style.display = 'block';
            document.getElementById('tabela-variacoes').style.display = 'none';
            bulkEditBar.style.display = 'none';
        } else {
            variacoesEmpty.style.display = 'none';
            document.getElementById('tabela-variacoes').style.display = 'table';
            bulkEditBar.style.display = 'flex';
        }
    }

    function addAtributo(nome = '', valores = []) {
        atributosDefinidos.push({ nome: nome, valores: valores });
        renderAtributos();
    }

    // Eventos do Configurador de Atributos
    btnAddAtributo.addEventListener('click', () => addAtributo('Novo Atributo', []));
    document.getElementById('btn-start-presets').addEventListener('click', () => {
        containerAtributos.style.display = 'block';
        if (atributosDefinidos.length === 0) {
            addAtributo('Cor', ['Preto', 'Azul']);
            addAtributo('Armazenamento', ['128GB', '256GB']);
        }
    });

    listaAtributos.addEventListener('input', function(e) {
        const idx = e.target.getAttribute('data-idx');
        if (idx !== null) {
            if (e.target.classList.contains('attr-nome')) {
                atributosDefinidos[idx].nome = e.target.value;
            } else if (e.target.classList.contains('attr-valores')) {
                atributosDefinidos[idx].valores = e.target.value.split(',').map(s => s.trim()).filter(Boolean);
            }
            atualizarResumoCombinacoes();
        }
    });

    listaAtributos.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-remove-attr');
        if (btn) {
            const idx = parseInt(btn.getAttribute('data-idx'));
            atributosDefinidos.splice(idx, 1);
            renderAtributos();
        }
    });

    // Presets
    document.getElementById('preset-smartphone').addEventListener('click', function() {
        atributosDefinidos = [
            { nome: 'Cor', valores: ['Azul Titânio', 'Preto Titânio', 'Branco'] },
            { nome: 'Armazenamento', valores: ['128GB', '256GB', '512GB'] },
            { nome: 'Memória RAM', valores: ['8GB', '12GB'] }
        ];
        renderAtributos();
    });

    document.getElementById('preset-moda').addEventListener('click', function() {
        atributosDefinidos = [
            { nome: 'Cor', valores: ['Preto', 'Branco', 'Azul Marinho'] },
            { nome: 'Tamanho', valores: ['P', 'M', 'G', 'GG'] }
        ];
        renderAtributos();
    });

    document.getElementById('preset-calcados').addEventListener('click', function() {
        atributosDefinidos = [
            { nome: 'Cor', valores: ['Preto', 'Branco'] },
            { nome: 'Numeração', valores: ['38', '39', '40', '41', '42', '43'] }
        ];
        renderAtributos();
    });

    document.getElementById('preset-eletro').addEventListener('click', function() {
        atributosDefinidos = [
            { nome: 'Cor', valores: ['Inox', 'Preto', 'Branco'] },
            { nome: 'Voltagem', valores: ['110V', '220V', 'Bivolt'] }
        ];
        renderAtributos();
    });

    // Algoritmo de Produto Cartesiano
    function produtoCartesiano(arrays) {
        return arrays.reduce((acc, curr) => {
            const res = [];
            acc.forEach(a => {
                curr.forEach(b => {
                    res.push([...a, b]);
                });
            });
            return res;
        }, [[]]);
    }

    // Gerador de Grade Cartesiana de SKUs
    btnGerarGrade.addEventListener('click', function() {
        const attrValidos = atributosDefinidos.filter(a => a.nome.trim() && a.valores.length > 0);
        if (attrValidos.length === 0) {
            alert('Configure ao menos um atributo com valores para gerar a grade.');
            return;
        }

        const nomes = attrValidos.map(a => a.nome.trim());
        const arraysValores = attrValidos.map(a => a.valores);
        const combinacoes = produtoCartesiano(arraysValores);

        const precoPadrao = inputPrecoBase ? inputPrecoBase.value : '';
        const nomeProd = inputNomeProduto ? inputNomeProduto.value.trim() : 'PROD';
        const prefixoSKU = (nomeProd ? nomeProd.replace(/[^a-zA-Z0-9]/g, '').substring(0, 6).toUpperCase() : 'SKU');

        // Limpar tabela existente ou adicionar
        tbodyVariacoes.innerHTML = '';
        variacaoIndex = 0;

        combinacoes.forEach((combo) => {
            const attrObj = {};
            let corVal = '';
            let tamanhoVal = '';
            let corHex = '#000000';

            nomes.forEach((nomeAttr, i) => {
                const valAttr = combo[i];
                attrObj[nomeAttr] = valAttr;
                if (/cor|color/i.test(nomeAttr)) {
                    corVal = valAttr;
                    const cLower = valAttr.toLowerCase().trim();
                    if (corNomesMap[cLower]) {
                        corHex = corNomesMap[cLower];
                    }
                } else if (/tamanho|capacidade|voltagem|numeracao|modelo/i.test(nomeAttr) && !tamanhoVal) {
                    tamanhoVal = valAttr;
                }
            });

            const nomeVariacao = combo.join(' / ');
            const skuSugestao = `${prefixoSKU}-${combo.map(c => c.replace(/[^a-zA-Z0-9]/g, '').toUpperCase()).join('-')}`;
            const attrJson = JSON.stringify(attrObj);

            adicionarLinhaVariacao({
                nome_variacao: nomeVariacao,
                sku: skuSugestao,
                atributos_json: attrJson,
                tamanho: tamanhoVal,
                cor: corVal,
                cor_hex: corHex,
                preco: precoPadrao,
                estoque: 10,
                imagem_url: ''
            });
        });

        checkEmptyState();
    });

    function adicionarLinhaVariacao(dados = {}) {
        const tr = document.createElement('tr');
        const idx = variacaoIndex;
        const nomeVar = dados.nome_variacao || '';
        const sku = dados.sku || '';
        const attrJson = dados.atributos_json || '';
        const cor = dados.cor || '';
        const corHex = dados.cor_hex || '#000000';
        const preco = dados.preco || '';
        const estoque = (dados.estoque !== undefined) ? dados.estoque : 10;
        const imagemUrl = dados.imagem_url || '';
        const tamanho = dados.tamanho || '';

        tr.innerHTML = `
            <td>
                <input type="hidden" name="variacoes[${idx}][nome_variacao]" value="${escapeHtml(nomeVar)}">
                <input type="hidden" name="variacoes[${idx}][atributos_json]" value="${escapeHtml(attrJson)}">
                <input type="hidden" name="variacoes[${idx}][tamanho]" value="${escapeHtml(tamanho)}">
                
                <div class="fw-semibold text-dark mb-1" style="font-size:.875rem;">
                    ${escapeHtml(nomeVar || 'Variação Padrão')}
                </div>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-muted" style="font-size: .75rem;">SKU</span>
                    <input type="text" name="variacoes[${idx}][sku]" class="form-control form-control-sm font-monospace" value="${escapeHtml(sku)}" placeholder="Ex: SKU-123">
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="color" name="variacoes[${idx}][cor_hex]" class="form-control form-control-color p-1 color-picker-input" value="${escapeHtml(corHex)}" style="width: 36px; height: 31px; cursor: pointer;" title="Cor">
                    <input type="text" name="variacoes[${idx}][cor]" class="form-control form-control-sm color-text-input" value="${escapeHtml(cor)}" placeholder="Ex: Azul, Preto">
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">R$</span>
                    <input type="number" step="0.01" min="0" name="variacoes[${idx}][preco]" class="form-control form-control-sm var-preco-input" value="${escapeHtml(preco)}" placeholder="Base">
                </div>
            </td>
            <td>
                <input type="number" name="variacoes[${idx}][estoque]" class="form-control form-control-sm var-estoque-input" value="${escapeHtml(estoque)}" placeholder="0" min="0" required>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="url" name="variacoes[${idx}][imagem_url]" class="form-control form-control-sm var-img-input" value="${escapeHtml(imagemUrl)}" placeholder="https://... foto da cor">
                    <button type="button" class="btn btn-outline-secondary btn-preview-var-img" title="Preview">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remover-variacao" title="Remover SKU">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;

        tbodyVariacoes.appendChild(tr);
        variacaoIndex++;
        checkEmptyState();
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // Botão Adicionar Manual
    document.getElementById('btn-add-variacao-manual').addEventListener('click', function() {
        adicionarLinhaVariacao({
            nome_variacao: 'Opção Manual',
            sku: '',
            atributos_json: '',
            cor: '',
            cor_hex: '#000000',
            preco: inputPrecoBase ? inputPrecoBase.value : '',
            estoque: 10
        });
    });

    // Limpar todas
    document.getElementById('btn-limpar-todas-variacoes').addEventListener('click', function() {
        if (confirm('Deseja realmente remover todas as variações geradas?')) {
            tbodyVariacoes.innerHTML = '';
            checkEmptyState();
        }
    });

    // Remoção individual
    tbodyVariacoes.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remover-variacao')) {
            e.target.closest('tr').remove();
            checkEmptyState();
        } else if (e.target.closest('.btn-preview-var-img')) {
            const tr = e.target.closest('tr');
            const imgInput = tr.querySelector('.var-img-input');
            if (imgInput && imgInput.value) {
                window.open(imgInput.value, '_blank');
            } else {
                alert('Insira uma URL de imagem válida para visualizar.');
            }
        }
    });

    // Ações em Lote (Bulk Apply)
    document.getElementById('btn-apply-bulk-preco').addEventListener('click', function() {
        const val = document.getElementById('bulk-preco-input').value;
        if (val !== '') {
            document.querySelectorAll('.var-preco-input').forEach(input => input.value = val);
        }
    });

    document.getElementById('btn-apply-bulk-estoque').addEventListener('click', function() {
        const val = document.getElementById('bulk-estoque-input').value;
        if (val !== '') {
            document.querySelectorAll('.var-estoque-input').forEach(input => input.value = val);
        }
    });

    // Sincronização Color Picker
    tbodyVariacoes.addEventListener('input', function(e) {
        if (e.target.classList.contains('color-picker-input')) {
            const group = e.target.closest('.input-group');
            const textInput = group ? group.querySelector('.color-text-input') : null;
            if (textInput && (!textInput.value || /^#[0-9a-fA-F]{3,6}$/i.test(textInput.value))) {
                textInput.value = e.target.value;
            }
        } else if (e.target.classList.contains('color-text-input')) {
            const group = e.target.closest('.input-group');
            const pickerInput = group ? group.querySelector('.color-picker-input') : null;
            const val = e.target.value.trim().toLowerCase();
            
            if (pickerInput) {
                if (/^#[0-9a-fA-F]{6}$/i.test(val)) {
                    pickerInput.value = val;
                } else if (corNomesMap[val]) {
                    pickerInput.value = corNomesMap[val];
                }
            }
        }
    });

    // Preview de Imagens Extras (Galeria)
    const inputGaleria = document.getElementById('imagens');
    const galleryPreview = document.getElementById('gallery-preview');
    if (inputGaleria && galleryPreview) {
        inputGaleria.addEventListener('change', function() {
            galleryPreview.innerHTML = '';
            Array.from(this.files).forEach((file) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-3 position-relative';
                    col.innerHTML = `<img src="${e.target.result}" class="img-thumbnail w-100 rounded" style="object-fit: cover; aspect-ratio: 1/1;" alt="Preview">`;
                    galleryPreview.appendChild(col);
                };
                reader.readAsDataURL(file);
            });
        });
    }

    checkEmptyState();
});
</script>

<?= $this->endSection() ?>