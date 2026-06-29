<!-- ============================================================
     Filter Panel Partial — Used in desktop sidebar & mobile offcanvas
     ============================================================ -->
<div class="filter-panel p-3 p-md-0">

    <!-- Panel header (desktop only) -->
    <div class="filter-panel-header d-none d-md-flex align-items-center justify-content-between mb-3">
        <span class="filter-panel-title">
            <i class="bi bi-funnel me-2"></i>Filtros
        </span>
        <a href="<?= site_url('/') ?>" class="filter-clear-all" id="btn-limpar-filtros">
            Limpar tudo
        </a>
    </div>

    <div class="accordion filter-accordion" id="filterAccordion">

        <!-- ===== CATEGORIAS ===== -->
        <div class="accordion-item filter-accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button filter-accordion-btn" type="button"
                    data-bs-toggle="collapse" data-bs-target="#acc-categorias"
                    aria-expanded="true" aria-controls="acc-categorias">
                    <i class="bi bi-tag me-2 text-primary"></i>Categorias
                </button>
            </h2>
            <div id="acc-categorias" class="accordion-collapse collapse show" data-bs-parent="#filterAccordion">
                <div class="accordion-body filter-accordion-body">
                    <!-- "Todas" option -->
                    <label class="filter-checkbox-label" for="cat-filter-todas">
                        <input class="filter-check" type="checkbox" id="cat-filter-todas"
                            name="categorias[]" value=""
                            <?= empty($categoriaAtivaId) ? 'checked' : '' ?>>
                        <span class="filter-checkbox-box"></span>
                        <span class="filter-checkbox-text">
                            <i class="bi bi-grid-3x3-gap me-1 text-muted"></i> Todas
                        </span>
                    </label>
                    <?php foreach ($categorias as $categoria): ?>
                        <label class="filter-checkbox-label" for="cat-filter-<?= esc($categoria['id']) ?>">
                            <input class="filter-check" type="checkbox"
                                id="cat-filter-<?= esc($categoria['id']) ?>"
                                name="categorias[]"
                                value="<?= esc($categoria['id']) ?>"
                                <?= (!empty($categoriaAtivaId) && $categoriaAtivaId == $categoria['id']) ? 'checked' : '' ?>>
                            <span class="filter-checkbox-box"></span>
                            <span class="filter-checkbox-text"><?= esc($categoria['nome']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- ===== GÊNERO ===== -->
        <div class="accordion-item filter-accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button filter-accordion-btn collapsed" type="button"
                    data-bs-toggle="collapse" data-bs-target="#acc-genero"
                    aria-expanded="false" aria-controls="acc-genero">
                    <i class="bi bi-people me-2 text-primary"></i>Gênero
                </button>
            </h2>
            <div id="acc-genero" class="accordion-collapse collapse" data-bs-parent="#filterAccordion">
                <div class="accordion-body filter-accordion-body">
                    <?php
                    $generos = [
                        ['id' => 'masculino',  'label' => 'Masculino',  'icon' => 'bi-gender-male'],
                        ['id' => 'feminino',   'label' => 'Feminino',   'icon' => 'bi-gender-female'],
                        ['id' => 'unissex',    'label' => 'Unissex',    'icon' => 'bi-gender-ambiguous'],
                        ['id' => 'infantil',   'label' => 'Infantil',   'icon' => 'bi-stars'],
                    ];
                    foreach ($generos as $g):
                    ?>
                        <label class="filter-checkbox-label" for="genero-<?= $g['id'] ?>">
                            <input class="filter-check" type="checkbox" id="genero-<?= $g['id'] ?>"
                                name="generos[]" value="<?= $g['id'] ?>">
                            <span class="filter-checkbox-box"></span>
                            <span class="filter-checkbox-text">
                                <i class="bi <?= $g['icon'] ?> me-1 text-muted"></i><?= $g['label'] ?>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- ===== PREÇO ===== -->
        <div class="accordion-item filter-accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button filter-accordion-btn collapsed" type="button"
                    data-bs-toggle="collapse" data-bs-target="#acc-preco"
                    aria-expanded="false" aria-controls="acc-preco">
                    <i class="bi bi-currency-dollar me-2 text-primary"></i>Preço
                </button>
            </h2>
            <div id="acc-preco" class="accordion-collapse collapse" data-bs-parent="#filterAccordion">
                <div class="accordion-body filter-accordion-body">

                    <!-- Dual Range Track -->
                    <div class="price-range-track-wrap mb-3">
                        <div class="price-range-track">
                            <div class="price-range-fill" id="range-fill"></div>
                        </div>
                        <input type="range" class="price-range-thumb price-range-thumb--min"
                            id="price-range-min" min="0" max="5000" value="0" step="50"
                            aria-label="Preço mínimo">
                        <input type="range" class="price-range-thumb price-range-thumb--max"
                            id="price-range-max" min="0" max="5000" value="5000" step="50"
                            aria-label="Preço máximo">
                    </div>

                    <!-- Price text inputs -->
                    <div class="d-flex gap-2 align-items-center">
                        <div class="price-input-wrap flex-1">
                            <label for="price-input-min" class="price-input-label">Mín.</label>
                            <div class="price-input-group">
                                <span class="price-input-prefix">R$</span>
                                <input type="number" class="price-input" id="price-input-min"
                                    min="0" max="5000" value="0" step="50"
                                    placeholder="0">
                            </div>
                        </div>
                        <span class="price-input-dash">—</span>
                        <div class="price-input-wrap flex-1">
                            <label for="price-input-max" class="price-input-label">Máx.</label>
                            <div class="price-input-group">
                                <span class="price-input-prefix">R$</span>
                                <input type="number" class="price-input" id="price-input-max"
                                    min="0" max="5000" value="5000" step="50"
                                    placeholder="5000">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- ===== MARCAS ===== -->
        <div class="accordion-item filter-accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button filter-accordion-btn collapsed" type="button"
                    data-bs-toggle="collapse" data-bs-target="#acc-marcas"
                    aria-expanded="false" aria-controls="acc-marcas">
                    <i class="bi bi-award me-2 text-primary"></i>Marcas
                </button>
            </h2>
            <div id="acc-marcas" class="accordion-collapse collapse" data-bs-parent="#filterAccordion">
                <div class="accordion-body filter-accordion-body">
                    <?php
                    $marcas = ['Nike', 'Adidas', 'Puma', 'Fila', 'Vans', 'New Balance', 'Converse', 'Under Armour'];
                    foreach ($marcas as $marca):
                        $id = 'marca-' . strtolower(str_replace(' ', '-', $marca));
                    ?>
                        <label class="filter-checkbox-label" for="<?= $id ?>">
                            <input class="filter-check" type="checkbox" id="<?= $id ?>"
                                name="marcas[]" value="<?= esc(strtolower($marca)) ?>">
                            <span class="filter-checkbox-box"></span>
                            <span class="filter-checkbox-text"><?= esc($marca) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div><!-- /accordion -->

    <!-- Apply button (desktop) -->
    <div class="d-none d-md-block mt-4">
        <button class="btn btn-primary w-100 rounded-pill fw-bold" id="btn-apply-filters" type="button">
            <i class="bi bi-search me-1"></i>Aplicar Filtros
        </button>
    </div>

</div><!-- /filter-panel -->
