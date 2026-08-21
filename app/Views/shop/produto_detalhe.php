<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
    $imagemPrincipal = !empty($produto['imagem'])
        ? (strpos($produto['imagem'], 'http') === 0
            ? esc($produto['imagem'])
            : base_url('uploads/produtos/' . esc($produto['imagem'])))
        : base_url('uploads/produtos/sem_imagem.png');

    // Galeria Real + Imagem Principal
    $galeria = [$imagemPrincipal];
    if (!empty($imagens) && is_array($imagens)) {
        foreach ($imagens as $img) {
            $isUrl = (strpos($img['caminho_imagem'], 'http://') === 0 || strpos($img['caminho_imagem'], 'https://') === 0);
            $galeria[] = $isUrl ? esc($img['caminho_imagem']) : base_url('uploads/produtos/' . esc($img['caminho_imagem']));
        }
    }

    $esgotado = (int)$produto['estoque'] === 0;

    // Processamento Dinâmico de Variações Multi-Atributos
    $variacoesData = [];
    $mapaAtributos = [];
    $coresDisponiveis = [];

    if (!empty($variacoes) && is_array($variacoes)) {
        foreach ($variacoes as $var) {
            $p = (!empty($var['preco']) && (float)$var['preco'] > 0) ? (float)$var['preco'] : (float)$produto['preco'];
            
            // Atributos estruturados
            $atributos = $var['atributos'] ?? [];
            if (empty($atributos) && !empty($var['atributos_json'])) {
                $decoded = json_decode($var['atributos_json'], true);
                if (is_array($decoded)) $atributos = $decoded;
            }

            // Fallback para variações legadas
            if (empty($atributos)) {
                if (!empty($var['cor'])) {
                    $atributos['Cor'] = $var['cor'];
                }
                if (!empty($var['tamanho'])) {
                    $atributos['Tamanho / Opção'] = $var['tamanho'];
                }
            }

            // Mapeia todos os eixos e valores
            foreach ($atributos as $nomeAttr => $valAttr) {
                $nome = trim($nomeAttr);
                $val  = trim($valAttr);
                if ($nome !== '' && $val !== '') {
                    if (!isset($mapaAtributos[$nome])) {
                        $mapaAtributos[$nome] = [];
                    }
                    if (!in_array($val, $mapaAtributos[$nome])) {
                        $mapaAtributos[$nome][] = $val;
                    }
                }
            }

            // Tratamento especial para Swatches de Cor
            $c = trim($var['cor'] ?? ($atributos['Cor'] ?? ''));
            $hex = trim($var['cor_hex'] ?? '');
            if ($c !== '') {
                $found = false;
                foreach ($coresDisponiveis as $cd) {
                    if ($cd['nome'] === $c) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $coresDisponiveis[] = [
                        'nome' => $c,
                        'hex'  => $hex
                    ];
                }
            }

            $imgVar = !empty($var['imagem_url']) ? $var['imagem_url'] : $imagemPrincipal;

            $variacoesData[] = [
                'id'            => (int)$var['id'],
                'sku'           => $var['sku'] ?? '',
                'nome_variacao' => $var['nome_variacao'] ?? implode(' / ', array_values($atributos)),
                'atributos'     => $atributos,
                'tamanho'       => $var['tamanho'] ?? ($atributos['Tamanho / Opção'] ?? ''),
                'cor'           => $c,
                'cor_hex'       => $hex,
                'preco'         => $p,
                'estoque'       => (int)$var['estoque'],
                'imagem_url'    => $imgVar,
            ];
        }
    }

    if (!function_exists('resolverCorCss')) {
        function resolverCorCss(string $cor, string $corHex = ''): string {
            if (!empty($corHex) && preg_match('/^#[0-9a-fA-F]{3,6}$/i', $corHex)) {
                return $corHex;
            }
            $c = mb_strtolower(trim($cor), 'UTF-8');
            $map = [
                'preto'            => '#000000',
                'black'            => '#000000',
                'branco'           => '#ffffff',
                'white'            => '#ffffff',
                'off-white'        => '#faf0e6',
                'cinza'            => '#6c757d',
                'cinza espacial'   => '#4a4d52',
                'space gray'       => '#4a4d52',
                'gray'             => '#6c757d',
                'prata'            => '#d1d5db',
                'silver'           => '#d1d5db',
                'dourado'          => '#f59e0b',
                'ouro'             => '#f59e0b',
                'gold'             => '#f59e0b',
                'vermelho'         => '#ef4444',
                'red'              => '#ef4444',
                'azul'             => '#3b82f6',
                'blue'             => '#3b82f6',
                'azul marinho'     => '#1e3a8a',
                'navy'             => '#1e3a8a',
                'azul celeste'     => '#38bdf8',
                'azul titânio'     => '#2e3a4e',
                'azul titanio'     => '#2e3a4e',
                'verde'            => '#10b981',
                'green'            => '#10b981',
                'verde escuro'     => '#065f46',
                'amarelo'          => '#eab308',
                'yellow'           => '#eab308',
                'laranja'          => '#f97316',
                'orange'           => '#f97316',
                'rosa'             => '#ec4899',
                'pink'             => '#ec4899',
                'roxo'             => '#8b5cf6',
                'purple'           => '#8b5cf6',
                'marrom'           => '#78350f',
                'brown'            => '#78350f',
                'bege'             => '#fef3c7',
                'beige'            => '#fef3c7',
                'titânio'          => '#878681',
                'titanio'          => '#878681',
                'titânio natural'  => '#9a948d',
                'titanio natural'  => '#9a948d',
                'titânio preto'    => '#2e2e2e',
                'titanio preto'    => '#2e2e2e',
                'preto titânio'    => '#2e2e2e',
                'preto titanio'    => '#2e2e2e',
                'titânio branco'   => '#e5e5e5',
                'titanio branco'   => '#e5e5e5',
            ];

            if (isset($map[$c])) {
                return $map[$c];
            }
            if (preg_match('/^#([a-f0-9]{3}){1,2}\b/i', $cor)) {
                return $cor;
            }
            if (preg_match('/^[a-z]+$/i', $c)) {
                return $c;
            }
            return '#6c757d';
        }
    }
?>

<div class="container py-4">

    <!-- ===== BREADCRUMB ===== -->
    <nav aria-label="breadcrumb" class="mb-4" id="breadcrumb-nav">
        <ol class="breadcrumb breadcrumb-custom">
            <li class="breadcrumb-item">
                <a href="<?= site_url('/') ?>"><i class="bi bi-house-door-fill me-1"></i>Início</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= site_url('categoria/' . esc($produto['categoria_id'])) ?>"><?= esc($produto['categoria_nome']) ?></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page"><?= esc($produto['nome']) ?></li>
        </ol>
    </nav>

    <!-- ===== TOP: PRODUTO (GALERIA + INFO) ===== -->
    <div class="row g-4 align-items-start">

        <!-- Coluna da Esquerda (col-12 col-lg-7): Galeria de Imagens -->
        <div class="col-12 col-lg-7">
            <div class="pdp-gallery">
                <!-- Main Image -->
                <div class="pdp-gallery-main mb-3 position-relative overflow-hidden rounded-4 bg-white border shadow-sm">
                    <img src="<?= $galeria[0] ?>"
                         alt="<?= esc($produto['nome']) ?>"
                         class="pdp-main-img"
                         id="pdp-main-img"
                         style="transition: opacity 0.25s ease-in-out; object-fit: contain; width: 100%; max-height: 520px;">
                    
                    <?php if ($produto['frete_gratis']): ?>
                        <span class="badge bg-success position-absolute top-0 start-0 m-3 px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.8125rem;">
                            <i class="bi bi-truck me-1"></i>Frete Grátis
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Thumbnails -->
                <?php if (count($galeria) > 1): ?>
                <div class="pdp-thumbnails" id="pdp-thumbnails">
                    <?php foreach ($galeria as $index => $thumb): ?>
                        <button type="button"
                                class="pdp-thumb-btn <?= $index === 0 ? 'active' : '' ?>"
                                data-img="<?= $thumb ?>"
                                aria-label="Miniatura <?= $index + 1 ?>">
                            <img src="<?= $thumb ?>" alt="Miniatura <?= $index + 1 ?>" loading="lazy">
                        </button>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Coluna da Direita (col-12 col-lg-5): Informações do Produto -->
        <div class="col-12 col-lg-5">
            <div class="pdp-info">

                <!-- Category & Rating Summary -->
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                    <a href="<?= site_url('categoria/' . esc($produto['categoria_id'])) ?>" class="pdp-category-tag" id="pdp-category-link">
                        <i class="bi bi-tag-fill me-1"></i><?= esc($produto['categoria_nome']) ?>
                    </a>
                    <a href="#secao-avaliacoes" class="text-decoration-none d-inline-flex align-items-center gap-1 small text-dark" id="pdp-rating-badge-top">
                        <?= renderEstrelas($estatisticasAvaliacao['media'] ?? 0, 'sm', true) ?>
                        <span class="text-muted ms-1">(<?= (int)($estatisticasAvaliacao['total'] ?? 0) ?> <?= ((int)($estatisticasAvaliacao['total'] ?? 0) === 1) ? 'avaliação' : 'avaliações' ?>)</span>
                    </a>
                </div>

                <h1 class="pdp-title mb-1" id="pdp-title"><?= esc($produto['nome']) ?></h1>
                
                <!-- SKU Badge -->
                <div class="mb-3">
                    <span class="badge bg-light text-secondary border font-monospace py-1 px-2" id="pdp-sku-badge" style="display: none;">
                        SKU: <span id="pdp-sku-val"></span>
                    </span>
                </div>

                <!-- Price Block -->
                <div class="pdp-price-wrap mb-3 p-3 bg-light rounded-4 border-0">
                    <div class="price-tag mb-0" id="pdp-price">
                        R$ <?= esc(number_format($produto['preco'], 2, ',', '.')) ?>
                    </div>
                    <div class="pdp-installments small text-muted mt-1" id="pdp-installments">
                        em até <strong>10x de R$ <?= esc(number_format($produto['preco'] / 10, 2, ',', '.')) ?></strong> sem juros
                    </div>
                    <div class="pdp-pix-discount small text-success fw-semibold mt-1">
                        <i class="bi bi-qr-code me-1"></i>ou R$ <?= esc(number_format($produto['preco'] * 0.95, 2, ',', '.')) ?> no Pix (5% OFF)
                    </div>
                </div>

                <!-- Stock Indicator -->
                <?php if (!$esgotado): ?>
                    <div class="pdp-stock-badge pdp-stock-available mb-3" id="pdp-stock-badge">
                        <i class="bi bi-check-circle-fill"></i>
                        <span id="pdp-stock-text"><?= esc($produto['estoque']) ?> unidades disponíveis</span>
                    </div>
                <?php else: ?>
                    <div class="pdp-stock-badge pdp-stock-unavailable mb-3" id="pdp-stock-badge">
                        <i class="bi bi-x-circle-fill"></i>
                        <span id="pdp-stock-text">Esgotado</span>
                    </div>
                <?php endif; ?>

                <!-- ===== SELETORES DINÂMICOS DE ATRIBUTOS & SKUS ===== -->
                <?php if (!empty($mapaAtributos)): ?>
                    <div class="pdp-attributes-container mb-3">
                        <?php foreach ($mapaAtributos as $nomeAtributo => $valoresAtributo): ?>
                            <?php 
                            $isCor = (bool) preg_match('/cor|color/i', $nomeAtributo);
                            ?>

                            <?php if ($isCor): ?>
                                <!-- Swatches Visuais de Cor -->
                                <div class="pdp-variant-section mb-3">
                                    <p class="pdp-variant-label mb-2 fw-semibold text-dark">
                                        <?= esc($nomeAtributo) ?>: <span class="selected-attr-name fw-bold text-primary ms-1" data-target-attr="<?= esc($nomeAtributo) ?>"></span>
                                    </p>
                                    <div class="pdp-variant-options d-flex flex-wrap gap-2 align-items-center attr-group" data-attr-name="<?= esc($nomeAtributo) ?>">
                                        <?php foreach ($valoresAtributo as $corNome): ?>
                                            <?php 
                                            // Busca hex se cadastrado
                                            $hexCor = '';
                                            foreach ($coresDisponiveis as $cd) {
                                                if ($cd['nome'] === $corNome) { $hexCor = $cd['hex']; break; }
                                            }
                                            $cssColor = resolverCorCss($corNome, $hexCor); 
                                            ?>
                                            <label class="pdp-color-chip variant-color-label attr-option" data-attr-name="<?= esc($nomeAtributo) ?>" data-attr-val="<?= esc($corNome) ?>" title="<?= esc($corNome) ?>">
                                                <input type="radio" name="attr_<?= md5($nomeAtributo) ?>" value="<?= esc($corNome) ?>" class="attr-radio d-none" data-attr-name="<?= esc($nomeAtributo) ?>">
                                                <span class="pdp-color-swatch border shadow-sm" style="background-color: <?= esc($cssColor) ?>;" title="<?= esc($corNome) ?>">
                                                    <i class="bi bi-check2"></i>
                                                </span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                            <?php else: ?>
                                <!-- Chips / Pills Interativos para outros Atributos (Armazenamento, RAM, Voltagem, Tamanho) -->
                                <div class="pdp-variant-section mb-3">
                                    <p class="pdp-variant-label mb-2 fw-semibold text-dark">
                                        <?= esc($nomeAtributo) ?>: <span class="selected-attr-name fw-bold text-primary ms-1" data-target-attr="<?= esc($nomeAtributo) ?>"></span>
                                    </p>
                                    <div class="pdp-variant-options d-flex flex-wrap gap-2 attr-group" data-attr-name="<?= esc($nomeAtributo) ?>">
                                        <?php foreach ($valoresAtributo as $valorItem): ?>
                                            <label class="pdp-variant-chip variant-pill-label attr-option" data-attr-name="<?= esc($nomeAtributo) ?>" data-attr-val="<?= esc($valorItem) ?>">
                                                <input type="radio" name="attr_<?= md5($nomeAtributo) ?>" value="<?= esc($valorItem) ?>" class="attr-radio d-none" data-attr-name="<?= esc($nomeAtributo) ?>">
                                                <span><?= esc($valorItem) ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Description -->
                <div class="pdp-description mb-4">
                    <h2 class="pdp-variant-label mb-1">Descrição</h2>
                    <p class="text-secondary small mb-0 lh-base"><?= esc($produto['descricao']) ?></p>
                </div>

                <!-- Add to Cart -->
                <div class="pdp-cart-actions">
                    <?php if (!$esgotado): ?>
                        <?= form_open('carrinho/adicionar', ['id' => 'form-add-cart']) ?>
                            <input type="hidden" name="produto_id" value="<?= esc($produto['id']) ?>">
                            <input type="hidden" name="variacao_id" id="variacao_id" value="">
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <div class="pdp-qty-wrap">
                                    <button type="button" class="pdp-qty-btn" id="qty-minus" aria-label="Diminuir">
                                        <i class="bi bi-dash-lg"></i>
                                    </button>
                                    <input type="number" name="quantidade" id="quantidade"
                                           class="pdp-qty-input"
                                           value="1" min="1"
                                           max="<?= esc($produto['estoque']) ?>">
                                    <button type="button" class="pdp-qty-btn" id="qty-plus" aria-label="Aumentar">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>

                                <button type="submit" class="pdp-add-to-cart flex-grow-1 shadow-sm" id="btn-add-cart">
                                    <i class="bi bi-bag-plus-fill me-2"></i>
                                    Adicionar ao Carrinho
                                </button>

                                <button type="button" class="btn btn-outline-danger rounded-4 px-3 py-2 btn-favorite-toggle btn-favorite-pdp d-flex align-items-center justify-content-center shadow-xs"
                                        data-produto-id="<?= esc($produto['id']) ?>" title="Salvar nos Favoritos" aria-label="Salvar nos Favoritos" style="height: 48px; min-width: 48px;">
                                    <i class="bi bi-heart fs-5"></i>
                                </button>
                            </div>
                        <?= form_close() ?>
                    <?php else: ?>
                        <div class="d-flex align-items-center gap-2">
                            <button class="pdp-add-to-cart pdp-add-to-cart--disabled flex-grow-1" disabled>
                                <i class="bi bi-bag-x me-2"></i>Produto Esgotado
                            </button>
                            <button type="button" class="btn btn-outline-danger rounded-4 px-3 py-2 btn-favorite-toggle btn-favorite-pdp d-flex align-items-center justify-content-center shadow-xs"
                                    data-produto-id="<?= esc($produto['id']) ?>" title="Salvar nos Favoritos" aria-label="Salvar nos Favoritos" style="height: 48px; min-width: 48px;">
                                <i class="bi bi-heart fs-5"></i>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Trust Badges -->
                <div class="pdp-trust-badges mt-4">
                    <?php if ($produto['frete_gratis']): ?>
                    <div class="pdp-trust-item">
                        <i class="bi bi-truck"></i>
                        <span>Frete Grátis</span>
                    </div>
                    <?php endif; ?>
                    <div class="pdp-trust-item">
                        <i class="bi bi-arrow-return-left"></i>
                        <span>Devolução Grátis</span>
                    </div>
                    <div class="pdp-trust-item">
                        <i class="bi bi-shield-check"></i>
                        <span>Compra Segura</span>
                    </div>
                </div>

                <!-- ===== SIMULADOR DE FRETE ===== -->
                <?php if (empty($produto['frete_gratis'])): ?>
                <div class="pdp-frete-card card border-0 shadow-sm mt-4 p-3 rounded-4 bg-light">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold fs-6">
                            <i class="bi bi-truck text-primary me-2"></i>Calcular Frete e Prazo
                        </span>
                        <a href="https://buscacepinter.correios.com.br/app/endereco/index.php" target="_blank" class="small text-muted text-decoration-none">
                            Não sei meu CEP
                        </a>
                    </div>
                    <div class="input-group mb-2">
                        <input type="text" id="cep-calculo" class="form-control rounded-start-pill font-monospace"
                               placeholder="00000-000" maxlength="9" aria-label="CEP para entrega">
                        <button class="btn btn-primary rounded-end-pill px-4 fw-semibold shadow-sm" type="button" id="btn-calcular-frete">
                            Calcular
                        </button>
                    </div>
                    <div id="resultado-frete-pdp" class="mt-2"></div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- ===== SEÇÃO: AVALIAÇÕES E REVIEWS ===== -->
    <section class="pdp-reviews-section mt-5 pt-4 border-top" id="secao-avaliacoes">
        <h2 class="fs-4 fw-bold mb-4 d-flex align-items-center gap-2">
            <i class="bi bi-star-fill text-warning"></i>
            Avaliações de Clientes
        </h2>

        <!-- Flash Messages de Avaliação -->
        <?php if (session()->getFlashdata('avaliacao_sucesso')): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= session()->getFlashdata('avaliacao_sucesso') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('avaliacao_erro')): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= session()->getFlashdata('avaliacao_erro') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Coluna da Esquerda (4 colunas no desktop): Resumo de Notas e Barras de Distribuição -->
            <div class="col-12 col-lg-4">
                <div class="pdp-rating-summary-card shadow-sm h-100">
                    <div class="score-display">
                        <span class="score-number"><?= number_format((float)($estatisticasAvaliacao['media'] ?? 0), 1, ',', '.') ?></span>
                        <div class="score-stars my-2">
                            <?= renderEstrelas((float)($estatisticasAvaliacao['media'] ?? 0), 'md') ?>
                        </div>
                        <p class="text-muted small mb-0">
                            Baseado em <strong><?= (int)($estatisticasAvaliacao['total'] ?? 0) ?></strong> <?= ((int)($estatisticasAvaliacao['total'] ?? 0) === 1) ? 'avaliação' : 'avaliações' ?>
                        </p>
                    </div>

                    <div class="rating-bars mt-4">
                        <?php for ($estrela = 5; $estrela >= 1; $estrela--): ?>
                            <?php
                                $qtd = (int)($estatisticasAvaliacao['distribuicao'][$estrela] ?? 0);
                                $pct = (float)($estatisticasAvaliacao['porcentagens'][$estrela] ?? 0);
                            ?>
                            <div class="rating-bar-row">
                                <span class="bar-label"><?= $estrela ?> <i class="bi bi-star-fill text-warning"></i></span>
                                <div class="progress progress-rating flex-grow-1">
                                    <div class="progress-bar bg-warning" role="progressbar"
                                         style="width: <?= $pct ?>%;"
                                         aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="bar-count"><?= $qtd ?></span>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

            <!-- Coluna da Direita (8 colunas no desktop): Formulário (acima) + Lista de Comentários (abaixo) -->
            <div class="col-12 col-lg-8">

                <!-- Acima: Formulário de Avaliação (exclusivo para Compradores Verificados ou Admins) -->
                <?php
                    $isLoggedIn = session()->get('logado');
                    $podeAvaliar = !empty($statusPermissaoAvaliacao['pode_avaliar']);
                ?>

                <?php if (!$isLoggedIn): ?>
                    <!-- Não logado -->
                    <div class="pdp-review-form-card shadow-sm text-center py-4 mb-4">
                        <i class="bi bi-person-lock text-muted mb-2 d-block" style="font-size: 2rem;"></i>
                        <h4 class="fs-6 fw-bold mb-1">Faça login para avaliar este produto</h4>
                        <p class="text-muted small mb-3">Apenas clientes que compraram e receberam o produto podem deixar uma avaliação.</p>
                        <a href="<?= site_url('login?redirect=' . current_url()) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-4" id="btn-login-avaliar">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Fazer Login
                        </a>
                    </div>
                <?php elseif ($podeAvaliar): ?>
                    <!-- Comprador Verificado ou Admin Logado: Exibir Formulário -->
                    <div class="pdp-review-form-card shadow-sm mb-4" id="card-formulario-avaliacao">
                        <h3 class="fs-5 fw-bold mb-1">
                            <?= !empty($statusPermissaoAvaliacao['ja_avaliou']) ? 'Editar sua Avaliação' : 'Avalie este Produto' ?>
                        </h3>
                        <p class="text-muted small mb-3">
                            <i class="bi bi-shield-check text-success me-1"></i>
                            Você é um <strong>Comprador Verificado</strong> deste produto. Sua opinião ajuda outros clientes!
                        </p>

                        <?= form_open('avaliacao/salvar', ['id' => 'form-avaliacao']) ?>
                            <input type="hidden" name="produto_id" value="<?= esc($produto['id']) ?>">

                            <!-- Seleção de Estrelas Interativa -->
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted d-block mb-1">
                                    Sua Nota <span class="text-danger">*</span>
                                </label>
                                <div class="star-rating-picker" id="star-picker">
                                    <?php
                                        $notaAtual = (int)($statusPermissaoAvaliacao['avaliacao_existente']['nota'] ?? old('nota', 5));
                                    ?>
                                    <?php for ($n = 5; $n >= 1; $n--): ?>
                                        <input type="radio" name="nota" value="<?= $n ?>" id="star-<?= $n ?>"
                                               <?= $n === $notaAtual ? 'checked' : '' ?> required>
                                        <label for="star-<?= $n ?>" title="<?= $n ?> estrelas" data-rating="<?= $n ?>">
                                            <i class="bi bi-star-fill"></i>
                                        </label>
                                    <?php endfor; ?>
                                </div>
                                <span class="rating-label-text ms-2 small text-muted fw-semibold" id="star-rating-label"></span>
                            </div>

                            <!-- Título Opcional -->
                            <div class="mb-3">
                                <label for="avaliacao-titulo" class="form-label small fw-bold text-muted mb-1">
                                    Título da Avaliação <span class="text-muted fw-normal">(Opcional)</span>
                                </label>
                                <input type="text" name="titulo" id="avaliacao-titulo" class="form-control rounded-3"
                                       placeholder="Ex: Excelente qualidade, superou as expectativas!"
                                       maxlength="150"
                                       value="<?= esc($statusPermissaoAvaliacao['avaliacao_existente']['titulo'] ?? old('titulo')) ?>">
                            </div>

                            <!-- Comentário Obrigatório -->
                            <div class="mb-3">
                                <label for="avaliacao-comentario" class="form-label small fw-bold text-muted mb-1">
                                    Seu Comentário <span class="text-danger">*</span>
                                </label>
                                <textarea name="comentario" id="avaliacao-comentario" class="form-control rounded-3" rows="3"
                                          placeholder="Conte o que achou do produto, acabamento, entrega..."
                                          required minlength="5" maxlength="2000"><?= esc($statusPermissaoAvaliacao['avaliacao_existente']['comentario'] ?? old('comentario')) ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm" id="btn-enviar-avaliacao">
                                <i class="bi bi-send-fill me-1"></i><?= !empty($statusPermissaoAvaliacao['ja_avaliou']) ? 'Atualizar Avaliação' : 'Enviar Avaliação' ?>
                            </button>
                        <?= form_close() ?>
                    </div>
                <?php elseif ($isLoggedIn): ?>
                    <!-- Usuário Logado mas sem compra confirmada -->
                    <div class="alert alert-light border text-muted small py-3 px-3 mb-4 rounded-3 d-flex align-items-center gap-2" id="alerta-apenas-compradores">
                        <i class="bi bi-info-circle text-primary fs-5 flex-shrink-0"></i>
                        <span>Apenas clientes que adquiriram este produto podem enviar uma avaliação.</span>
                    </div>
                <?php endif; ?>

                <!-- Abaixo: Lista "Comentários e Experiências" -->
                <div class="reviews-feed">
                    <h3 class="fs-5 fw-bold mb-3">
                        Comentários e Experiências (<?= count($avaliacoes) ?>)
                    </h3>

                    <?php if (!empty($avaliacoes)): ?>
                        <div class="row g-3" id="lista-avaliacoes">
                            <?php foreach ($avaliacoes as $av): ?>
                                <div class="col-12">
                                    <div class="review-card shadow-sm">
                                        <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-2">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="review-avatar">
                                                    <?= mb_strtoupper(mb_substr($av['usuario_nome'] ?? 'C', 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <strong class="text-dark"><?= esc($av['usuario_nome'] ?? 'Cliente') ?></strong>
                                                        <?php if (!empty($av['compra_verificada'])): ?>
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle small px-2 py-0" style="font-size:0.75rem;">
                                                                <i class="bi bi-patch-check-fill me-1"></i>Compra Verificada
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2 mt-1">
                                                        <?= renderEstrelas((float)$av['nota'], 'sm') ?>
                                                        <small class="text-muted" style="font-size:0.75rem;">
                                                             <?= date('d/m/Y', strtotime($av['created_at'])) ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if (!empty($av['titulo'])): ?>
                                            <h4 class="fs-6 fw-bold text-dark mt-2 mb-1"><?= esc($av['titulo']) ?></h4>
                                        <?php endif; ?>

                                        <p class="text-secondary small mb-0 lh-base">
                                            <?= nl2br(esc($av['comentario'])) ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="card border-0 bg-light p-4 text-center rounded-4 shadow-sm" id="empty-reviews-card">
                            <div class="py-3">
                                <i class="bi bi-chat-heart text-muted mb-2 d-block" style="font-size: 2.5rem;"></i>
                                <h4 class="fs-6 fw-bold text-dark mb-1">Ainda não há avaliações para este produto.</h4>
                                <p class="text-muted small mb-0">Seja o primeiro a compartilhar sua experiência com outros compradores!</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== RECOMENDAÇÕES ("Você também pode gostar") ===== -->
    <?php if (!empty($relacionados)): ?>
    <section class="pdp-related-section mt-5 pt-4 border-top" id="produtos-relacionados">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h2 class="fs-4 fw-bold mb-0">Você também pode gostar</h2>
            <a href="<?= site_url('categoria/' . esc($produto['categoria_id'])) ?>"
               class="text-decoration-none text-primary fw-semibold">
                Ver mais <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="row row-cols-2 row-cols-md-4 g-3">
            <?php foreach ($relacionados as $rel): ?>
                <?php $relEsgotado = (int)$rel['estoque'] === 0; ?>
                <div class="col d-flex align-items-stretch">
                    <article class="product-card w-100 h-100 d-flex flex-column <?= $relEsgotado ? 'opacity-65' : '' ?>">
                        <div class="product-card-img-wrap">
                            <button type="button" class="btn-favorite-card btn-favorite-toggle" data-produto-id="<?= esc($rel['id']) ?>" title="Favoritar Produto" aria-label="Favoritar Produto">
                                <i class="bi bi-heart"></i>
                            </button>
                            <?php if (!empty($rel['imagem'])): ?>
                                <img src="<?= strpos($rel['imagem'], 'http') === 0
                                    ? esc($rel['imagem'])
                                    : base_url('uploads/produtos/' . esc($rel['imagem'])) ?>"
                                    alt="<?= esc($rel['nome']) ?>" loading="lazy">
                            <?php else: ?>
                                <img src="<?= base_url('uploads/produtos/sem_imagem.png') ?>"
                                    alt="Sem Imagem" loading="lazy">
                            <?php endif; ?>
                            <?php if ($relEsgotado): ?>
                                <span class="product-card-badge">Esgotado</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-card-body flex-grow-1">
                            <p class="product-card-category"><?= esc($rel['categoria_nome']) ?></p>
                            <h3 class="product-card-title"><?= esc($rel['nome']) ?></h3>
                            <p class="product-card-price">
                                R$ <?= esc(number_format($rel['preco'], 2, ',', '.')) ?>
                            </p>
                        </div>
                        <div class="product-card-footer mt-auto">
                            <a href="<?= site_url('produto/' . $rel['id']) ?>"
                               class="btn-details"
                               id="ver-relacionado-<?= esc($rel['id']) ?>">
                                <i class="bi bi-bag-plus"></i> Ver Detalhes
                            </a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const mainImg    = document.getElementById('pdp-main-img');
    const thumbsWrap = document.getElementById('pdp-thumbnails');

    if (thumbsWrap && mainImg) {
        thumbsWrap.addEventListener('click', function (e) {
            const btn = e.target.closest('.pdp-thumb-btn');
            if (!btn) return;

            thumbsWrap.querySelectorAll('.pdp-thumb-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            mainImg.style.opacity = '0';
            setTimeout(() => {
                mainImg.src = btn.dataset.img;
                mainImg.style.opacity = '1';
            }, 180);
        });
    }

    // --- Seletor de Estrelas Interativo (PDP) ---
    const starPicker = document.getElementById('star-picker');
    const starRatingLabel = document.getElementById('star-rating-label');
    const ratingTexts = {
        1: 'Péssimo',
        2: 'Ruim',
        3: 'Regular',
        4: 'Muito Bom',
        5: 'Excelente!'
    };

    if (starPicker && starRatingLabel) {
        const updateStarLabel = (rating) => {
            starRatingLabel.textContent = ratingTexts[rating] || '';
        };

        const checkedRadio = starPicker.querySelector('input:checked');
        if (checkedRadio) {
            updateStarLabel(checkedRadio.value);
        }

        starPicker.querySelectorAll('label').forEach(label => {
            label.addEventListener('mouseenter', function () {
                const rating = this.dataset.rating;
                updateStarLabel(rating);
            });
        });

        starPicker.addEventListener('mouseleave', function () {
            const currentChecked = starPicker.querySelector('input:checked');
            if (currentChecked) {
                updateStarLabel(currentChecked.value);
            }
        });

        starPicker.querySelectorAll('input').forEach(input => {
            input.addEventListener('change', function () {
                updateStarLabel(this.value);
            });
        });
    }

    const qtyInput = document.getElementById('quantidade');
    const btnMinus = document.getElementById('qty-minus');
    const btnPlus  = document.getElementById('qty-plus');

    if (qtyInput && btnMinus && btnPlus) {
        btnMinus.addEventListener('click', () => {
            const current = parseInt(qtyInput.value) || 1;
            if (current > 1) qtyInput.value = current - 1;
        });

        btnPlus.addEventListener('click', () => {
            const current = parseInt(qtyInput.value) || 1;
            const max     = parseInt(qtyInput.max) || 999;
            if (current < max) qtyInput.value = current + 1;
        });
    }

    // =========================================================================
    // ENGINE REATIVO DE VARIAÇÕES MULTI-ATRIBUTOS & SKUS
    // =========================================================================
    const variacoes = <?= json_encode($variacoesData ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const mapaAtributos = <?= json_encode($mapaAtributos ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const basePreco = <?= (float) $produto['preco'] ?>;
    const baseEstoque = <?= (int) $produto['estoque'] ?>;

    const btnAddCart = document.getElementById('btn-add-cart');
    const inputVariacaoId = document.getElementById('variacao_id');
    const priceDisplay = document.getElementById('pdp-price');
    const installmentsDisplay = document.getElementById('pdp-installments');
    const stockText = document.getElementById('pdp-stock-text');
    const skuBadge = document.getElementById('pdp-sku-badge');
    const skuVal = document.getElementById('pdp-sku-val');
    const formAddCart = document.getElementById('form-add-cart');

    function formatMoney(valor) {
        return 'R$ ' + Number(valor).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function trocarImagemPrincipal(novaUrl) {
        if (!mainImg || !novaUrl) return;
        if (mainImg.src === novaUrl) return;

        mainImg.style.opacity = '0.3';
        setTimeout(() => {
            mainImg.src = novaUrl;
            mainImg.style.opacity = '1';
        }, 150);
    }

    function getAtributosSelecionados() {
        const selecionados = {};
        document.querySelectorAll('.attr-radio:checked').forEach(radio => {
            const nome = radio.getAttribute('data-attr-name');
            if (nome) {
                selecionados[nome] = radio.value;
            }
        });
        return selecionados;
    }

    function checkVariations() {
        if (variacoes.length === 0) return;

        const selecionados = getAtributosSelecionados();
        const totalEixos = Object.keys(mapaAtributos).length;

        // Atualizar labels dos atributos com o valor selecionado
        document.querySelectorAll('.selected-attr-name').forEach(span => {
            const attr = span.getAttribute('data-target-attr');
            span.textContent = selecionados[attr] ? selecionados[attr] : '';
        });

        // 1. Validar disponibilidade cruzada de cada opção
        document.querySelectorAll('.attr-option').forEach(optionLabel => {
            const attrName = optionLabel.getAttribute('data-attr-name');
            const attrVal  = optionLabel.getAttribute('data-attr-val');

            // Verifica se existe alguma variação com estoque > 0 compatível com as OUTRAS seleções
            const isDisponivel = variacoes.some(v => {
                if (v.estoque <= 0) return false;
                
                // Valida o próprio atributo
                const valNoSku = v.atributos ? v.atributos[attrName] : (attrName === 'Cor' ? v.cor : v.tamanho);
                if (valNoSku !== attrVal) return false;

                // Valida os outros atributos já selecionados
                for (const [outroNome, outroVal] of Object.entries(selecionados)) {
                    if (outroNome === attrName) continue;
                    const outroValSku = v.atributos ? v.atributos[outroNome] : (outroNome === 'Cor' ? v.cor : v.tamanho);
                    if (outroValSku !== outroVal) return false;
                }

                return true;
            });

            const radio = optionLabel.querySelector('input');
            if (!isDisponivel) {
                optionLabel.classList.add('disabled-combination');
                optionLabel.style.opacity = '0.35';
                optionLabel.style.cursor = 'not-allowed';
                if (optionLabel.classList.contains('variant-pill-label')) {
                    optionLabel.style.textDecoration = 'line-through';
                }
            } else {
                optionLabel.classList.remove('disabled-combination');
                optionLabel.style.opacity = '1';
                optionLabel.style.cursor = 'pointer';
                if (optionLabel.classList.contains('variant-pill-label')) {
                    optionLabel.style.textDecoration = 'none';
                }
            }

            // Atualiza classe ativa
            if (radio && radio.checked) {
                optionLabel.classList.add('active-selected');
                if (optionLabel.classList.contains('variant-color-label')) {
                    optionLabel.querySelector('.pdp-color-swatch').classList.add('active');
                }
            } else {
                optionLabel.classList.remove('active-selected');
                if (optionLabel.classList.contains('variant-color-label')) {
                    optionLabel.querySelector('.pdp-color-swatch').classList.remove('active');
                }
            }
        });

        // 2. Encontrar o SKU exato se todos os eixos estiverem selecionados
        let matchingVar = null;
        const qtdSelecionada = Object.keys(selecionados).length;

        if (qtdSelecionada === totalEixos || totalEixos === 0) {
            matchingVar = variacoes.find(v => {
                for (const [nome, val] of Object.entries(selecionados)) {
                    const valSku = v.atributos ? v.atributos[nome] : (nome === 'Cor' ? v.cor : v.tamanho);
                    if (valSku !== val) return false;
                }
                return true;
            });
        }

        // 3. Atualizar estado visual e formulário de compra
        if (matchingVar && matchingVar.estoque > 0) {
            inputVariacaoId.value = matchingVar.id;
            if (btnAddCart) {
                btnAddCart.disabled = false;
                btnAddCart.classList.remove('pdp-add-to-cart--disabled');
            }

            // Atualizar preço
            if (priceDisplay) {
                priceDisplay.innerText = formatMoney(matchingVar.preco);
            }
            if (installmentsDisplay) {
                const parcela = matchingVar.preco / 10;
                installmentsDisplay.innerHTML = `em até <strong>10x de ${formatMoney(parcela)}</strong> sem juros`;
            }

            // Atualizar SKU badge
            if (skuBadge && skuVal) {
                if (matchingVar.sku) {
                    skuVal.innerText = matchingVar.sku;
                    skuBadge.style.display = 'inline-block';
                } else {
                    skuBadge.style.display = 'none';
                }
            }

            // Atualizar Estoque
            if (stockText) {
                stockText.innerText = `${matchingVar.estoque} unidades disponíveis`;
            }
            if (qtyInput) {
                qtyInput.max = matchingVar.estoque;
                if (parseInt(qtyInput.value) > matchingVar.estoque) {
                    qtyInput.value = matchingVar.estoque;
                }
            }

            // Troca de Foto da Variação
            if (matchingVar.imagem_url) {
                trocarImagemPrincipal(matchingVar.imagem_url);
            }

        } else {
            inputVariacaoId.value = '';
            if (btnAddCart) {
                btnAddCart.disabled = true;
                btnAddCart.classList.add('pdp-add-to-cart--disabled');
            }
            if (priceDisplay) {
                priceDisplay.innerText = formatMoney(basePreco);
            }
            if (skuBadge) {
                skuBadge.style.display = 'none';
            }
            if (stockText) {
                stockText.innerText = baseEstoque > 0 ? `${baseEstoque} unidades disponíveis` : 'Esgotado';
            }
        }
    }

    // Auto-seleção inteligente da primeira combinação disponível ao abrir a página
    function autoSelecionarPrimeiroDisponivel() {
        if (variacoes.length === 0) return;

        const primeiroEmEstoque = variacoes.find(v => v.estoque > 0) || variacoes[0];
        if (!primeiroEmEstoque) return;

        if (primeiroEmEstoque.atributos) {
            for (const [nomeAttr, valAttr] of Object.entries(primeiroEmEstoque.atributos)) {
                const radio = document.querySelector(`.attr-radio[data-attr-name="${nomeAttr}"][value="${valAttr}"]`);
                if (radio) radio.checked = true;
            }
        } else {
            if (primeiroEmEstoque.cor) {
                const rCor = document.querySelector(`.attr-radio[data-attr-name="Cor"][value="${primeiroEmEstoque.cor}"]`);
                if (rCor) rCor.checked = true;
            }
            if (primeiroEmEstoque.tamanho) {
                const rTam = document.querySelector(`.attr-radio[data-attr-name="Tamanho / Opção"][value="${primeiroEmEstoque.tamanho}"]`);
                if (rTam) rTam.checked = true;
            }
        }

        checkVariations();
    }

    // Eventos de clique nas opções
    document.querySelectorAll('.attr-option').forEach(option => {
        option.addEventListener('click', function(e) {
            const radio = this.querySelector('.attr-radio');
            if (radio && !radio.checked) {
                radio.checked = true;
                checkVariations();
            }
        });
    });

    if (formAddCart) {
        formAddCart.addEventListener('submit', function(e) {
            if (variacoes.length > 0 && !inputVariacaoId.value) {
                e.preventDefault();
                alert('Por favor, selecione todas as opções disponíveis antes de adicionar ao carrinho.');
            }
        });
    }

    if (variacoes.length > 0) {
        autoSelecionarPrimeiroDisponivel();
    }

    // --- Simulador de Frete PDP ---
    const cepInput = document.getElementById('cep-calculo');
    const btnCalcularFrete = document.getElementById('btn-calcular-frete');
    const resultadoFrete = document.getElementById('resultado-frete-pdp');

    if (cepInput && btnCalcularFrete && resultadoFrete) {
        cepInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').replace(/^(\d{5})(\d)/, '$1-$2').substring(0, 9);
        });

        cepInput.addEventListener('keyup', function (e) {
            if (e.key === 'Enter') {
                btnCalcularFrete.click();
            }
        });

        btnCalcularFrete.addEventListener('click', async function () {
            const cep = cepInput.value.replace(/\D/g, '');
            if (cep.length !== 8) {
                resultadoFrete.innerHTML = `
                    <div class="alert alert-warning py-2 px-3 small mb-0 rounded-3">
                        <i class="bi bi-exclamation-triangle me-1"></i>Digite um CEP válido com 8 dígitos.
                    </div>`;
                return;
            }

            resultadoFrete.innerHTML = `
                <div class="text-center py-3 text-muted small">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Calculando opções de frete...
                </div>`;

            try {
                const formData = new FormData();
                formData.append('cep', cep);
                formData.append('produto_id', '<?= esc($produto['id']) ?>');
                formData.append('quantidade', qtyInput ? qtyInput.value : 1);

                const response = await fetch('<?= site_url('api/frete/calcular') ?>', {
                    method: 'POST',
                    body: formData,
                });

                const data = await response.json();

                if (!data.ok) {
                    resultadoFrete.innerHTML = `
                        <div class="alert alert-danger py-2 px-3 small mb-0 rounded-3">
                            <i class="bi bi-exclamation-circle me-1"></i>${data.erro || 'Não foi possível calcular o frete.'}
                        </div>`;
                    return;
                }

                let html = `<div class="list-group list-group-flush rounded-3 border">`;
                data.opcoes.forEach(op => {
                    const precoFmt = op.valor === 0
                        ? `<span class="badge bg-success-subtle text-success border border-success-subtle fw-bold fs-6">GRÁTIS</span>`
                        : `<strong class="text-success fs-6">R$ ${op.valor.toFixed(2).replace('.', ',')}</strong>`;

                    html += `
                        <div class="list-group-item d-flex align-items-center justify-content-between py-2 px-3 ${op.destaque ? 'bg-success-subtle bg-opacity-25' : ''}">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi ${op.icone} text-primary fs-5"></i>
                                <div>
                                    <div class="fw-semibold small">${op.nome}</div>
                                    <small class="text-muted" style="font-size:0.75rem;">Chega em ${op.prazo}</small>
                                </div>
                            </div>
                            <div class="text-end">
                                ${precoFmt}
                            </div>
                        </div>`;
                });
                html += `</div>`;
                resultadoFrete.innerHTML = html;
            } catch (err) {
                console.error(err);
                resultadoFrete.innerHTML = `
                    <div class="alert alert-danger py-2 px-3 small mb-0 rounded-3">
                        <i class="bi bi-exclamation-circle me-1"></i>Erro ao calcular frete. Tente novamente.
                    </div>`;
            }
        });
    }
});
</script>
<?= $this->endSection() ?>