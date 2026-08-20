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

    // Processar Variações reais do Banco
    $tamanhosDisponiveis = [];
    $coresDisponiveis = [];
    $variacoesData = [];

    if (!empty($variacoes) && is_array($variacoes)) {
        foreach ($variacoes as $var) {
            $t = trim($var['tamanho'] ?? '');
            $c = trim($var['cor'] ?? '');
            $hex = trim($var['cor_hex'] ?? '');
            $p = (!empty($var['preco']) && (float)$var['preco'] > 0) ? (float)$var['preco'] : (float)$produto['preco'];
            
            $variacoesData[] = [
                'id'      => (int)$var['id'],
                'tamanho' => $t,
                'cor'     => $c,
                'cor_hex' => $hex,
                'preco'   => $p,
                'estoque' => (int)$var['estoque']
            ];
            
            if ($t !== '' && !in_array($t, $tamanhosDisponiveis)) {
                $tamanhosDisponiveis[] = $t;
            }
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
        }
    }

    // Determinar Rótulo Dinâmico para a Variação / Atributo
    $rotuloVariacao = 'Variação / Opção';
    if (!empty($tamanhosDisponiveis)) {
        $isVoltagem = true;
        $isCapacidade = true;
        $isTamanho = true;
        foreach ($tamanhosDisponiveis as $itemVal) {
            $valUpper = strtoupper(trim($itemVal));
            if (!preg_match('/^(110V|220V|127V|BIVOLT|\d+V)$/i', $valUpper)) {
                $isVoltagem = false;
            }
            if (!preg_match('/^\d+\s*(GB|TB|MB|G|T)$/i', $valUpper)) {
                $isCapacidade = false;
            }
            if (!preg_match('/^(PP|P|M|G|GG|XG|XGG|XXG|ÚNICO|UNICO|\d{2})$/i', $valUpper)) {
                $isTamanho = false;
            }
        }
        if ($isCapacidade) {
            $rotuloVariacao = 'Capacidade / Modelo';
        } elseif ($isVoltagem) {
            $rotuloVariacao = 'Voltagem';
        } elseif ($isTamanho) {
            $rotuloVariacao = 'Tamanho';
        } else {
            $rotuloVariacao = 'Variação / Opção';
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
                'grey'             => '#6c757d',
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
                'titânio azul'     => '#2e3a4e',
                'titanio azul'     => '#2e3a4e',
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
                <div class="pdp-gallery-main mb-3">
                    <img src="<?= $galeria[0] ?>"
                         alt="<?= esc($produto['nome']) ?>"
                         class="pdp-main-img"
                         id="pdp-main-img">
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

                <h1 class="pdp-title" id="pdp-title"><?= esc($produto['nome']) ?></h1>

                <div class="price-tag mb-3" id="pdp-price">
                    R$ <?= esc(number_format($produto['preco'], 2, ',', '.')) ?>
                </div>

                <!-- Stock Indicator -->
                <?php if (!$esgotado): ?>
                    <div class="pdp-stock-badge pdp-stock-available" id="pdp-stock-badge">
                        <i class="bi bi-check-circle-fill"></i>
                        <span id="pdp-stock-text"><?= esc($produto['estoque']) ?> unidades em estoque</span>
                    </div>
                <?php else: ?>
                    <div class="pdp-stock-badge pdp-stock-unavailable" id="pdp-stock-badge">
                        <i class="bi bi-x-circle-fill"></i>
                        <span id="pdp-stock-text">Esgotado</span>
                    </div>
                <?php endif; ?>

                <!-- Variation / Attribute Selector -->
                <?php if (!empty($tamanhosDisponiveis)): ?>
                <div class="pdp-variant-section">
                    <p class="pdp-variant-label" id="pdp-variant-type-label"><?= esc($rotuloVariacao) ?></p>
                    <div class="pdp-variant-options" id="size-options">
                        <?php foreach ($tamanhosDisponiveis as $index => $tamanho): ?>
                            <label class="pdp-variant-chip variant-size-label" data-size="<?= esc($tamanho) ?>">
                                <input type="radio" name="tamanho" value="<?= esc($tamanho) ?>" class="variant-size-selector">
                                <span><?= esc($tamanho) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Color Selector (Optional visual swatch) -->
                <?php if (!empty($coresDisponiveis)): ?>
                <div class="pdp-variant-section">
                    <p class="pdp-variant-label">Cor: <span id="selected-color-name" class="fw-semibold text-primary ms-1"></span></p>
                    <div class="pdp-variant-options d-flex flex-wrap gap-2 align-items-center" id="color-options">
                        <?php foreach ($coresDisponiveis as $index => $corItem): ?>
                            <?php 
                            $corNome = is_array($corItem) ? ($corItem['nome'] ?? '') : (string)$corItem;
                            $corHex  = is_array($corItem) ? ($corItem['hex'] ?? '') : '';
                            $cssColor = resolverCorCss($corNome, $corHex); 
                            ?>
                            <label class="pdp-color-chip variant-color-label" data-color="<?= esc($corNome) ?>" title="<?= esc($corNome) ?>">
                                <input type="radio" name="cor" value="<?= esc($corNome) ?>" class="variant-color-selector">
                                <span class="pdp-color-swatch border shadow-sm" style="background-color: <?= esc($cssColor) ?>;" title="<?= esc($corNome) ?>">
                                    <i class="bi bi-check2"></i>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Description -->
                <div class="pdp-description">
                    <h2 class="pdp-variant-label">Descrição</h2>
                    <p><?= esc($produto['descricao']) ?></p>
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

                                <button type="submit" class="pdp-add-to-cart flex-grow-1" id="btn-add-cart">
                                    <i class="bi bi-bag-plus-fill me-2"></i>
                                    Adicionar ao Carrinho
                                </button>
                            </div>
                        <?= form_close() ?>
                    <?php else: ?>
                        <button class="pdp-add-to-cart pdp-add-to-cart--disabled w-100" disabled>
                            <i class="bi bi-bag-x me-2"></i>Produto Esgotado
                        </button>
                    <?php endif; ?>
                </div>

                <!-- Trust Badges -->
                <div class="pdp-trust-badges">
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
                        <button class="btn btn-primary rounded-end-pill px-4 fw-semibold" type="button" id="btn-calcular-frete">
                            Calcular
                        </button>
                    </div>
                    <div id="resultado-frete-pdp" class="mt-2"></div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- ===== SEÇÃO DE AVALIAÇÕES & REVIEWS (#secao-avaliacoes) ===== -->
    <section class="pdp-reviews-section mt-5 pt-4 border-top" id="secao-avaliacoes">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
            <div>
                <h2 class="fs-4 fw-bold mb-1">
                    <i class="bi bi-star-half text-warning me-2"></i>Avaliações dos Clientes
                </h2>
                <p class="text-muted small mb-0">
                    Opiniões reais de quem já comprou e testou este produto.
                </p>
            </div>
            <?php if (!empty($estatisticasAvaliacao['total'])): ?>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark border px-3 py-2 fs-6 fw-semibold">
                        <i class="bi bi-chat-quote me-1 text-primary"></i>
                        <?= $estatisticasAvaliacao['total'] ?> <?= ($estatisticasAvaliacao['total'] === 1) ? 'avaliação' : 'avaliações' ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>

        <div class="row g-4 mt-2">
            <!-- Coluna da Esquerda (col-12 col-lg-4): Resumo da Reputação -->
            <div class="col-12 col-lg-4">
                <div class="review-summary-card shadow-sm sticky-lg-top" style="top: 90px;">
                    <h3 class="fs-6 fw-bold text-uppercase tracking-wider text-muted mb-3">Resumo da Reputação</h3>
                    
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="review-score-big text-primary">
                            <?= number_format($estatisticasAvaliacao['media'] ?? 0, 1, ',', '.') ?>
                        </div>
                        <div>
                            <div class="mb-1">
                                <?= renderEstrelas($estatisticasAvaliacao['media'] ?? 0, 'md') ?>
                            </div>
                            <div class="small text-muted">
                                Média baseada em <?= (int)($estatisticasAvaliacao['total'] ?? 0) ?> <?= ((int)($estatisticasAvaliacao['total'] ?? 0) === 1) ? 'opinião' : 'opiniões' ?>
                            </div>
                        </div>
                    </div>

                    <!-- Distribuição de Estrelas (5 a 1) -->
                    <div class="review-distribution">
                        <?php for ($estrela = 5; $estrela >= 1; $estrela--): ?>
                            <?php 
                                $qtd = $estatisticasAvaliacao['distribuicao'][$estrela] ?? 0;
                                $pct = $estatisticasAvaliacao['percentuais'][$estrela] ?? 0;
                            ?>
                            <div class="review-progress-row">
                                <span class="text-muted fw-semibold" style="width: 45px; font-size: 0.75rem;">
                                    <?= $estrela ?> <i class="bi bi-star-fill text-warning"></i>
                                </span>
                                <div class="progress" role="progressbar" aria-label="<?= $estrela ?> estrelas" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar" style="width: <?= $pct ?>%;"></div>
                                </div>
                                <span class="text-muted small text-end" style="width: 40px; font-size: 0.75rem;">
                                    <?= $qtd ?>
                                </span>
                            </div>
                        <?php endfor; ?>
                    </div>

                    <?php if (($estatisticasAvaliacao['total'] ?? 0) > 0): ?>
                        <div class="mt-4 pt-3 border-top d-flex align-items-center gap-2 text-success small fw-semibold">
                            <i class="bi bi-hand-thumbs-up-fill fs-5"></i>
                            <span><?= $estatisticasAvaliacao['recomendacao_percentual'] ?>% dos compradores recomendam este produto</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Coluna da Direita (col-12 col-lg-8): Formulário de Avaliação + Feed de Reviews -->
            <div class="col-12 col-lg-8">
                <!-- Topo: Card "Deixar sua Avaliação" / Form -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                    <?php if (!session()->get('isLoggedIn')): ?>
                        <!-- Usuário Deslogado -->
                        <div class="text-center py-4 my-auto">
                            <div class="mb-3">
                                <i class="bi bi-shield-lock-fill text-primary" style="font-size: 2.5rem;"></i>
                            </div>
                            <h4 class="fs-5 fw-bold mb-2">Já comprou ou conhece este produto?</h4>
                            <p class="text-muted small mb-4 mx-auto" style="max-width: 420px;">
                                Faça login em sua conta para avaliar com estrelas e deixar seu comentário para a comunidade.
                            </p>
                            <a href="<?= site_url('login') ?>" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Entrar para Avaliar
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Usuário Logado: Formulário de Envio -->
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h3 class="fs-6 fw-bold mb-0">
                                    <?= !empty($statusPermissaoAvaliacao['ja_avaliou']) ? 'Atualizar sua Avaliação' : 'Deixar sua Avaliação' ?>
                                </h3>
                                <?php if (!empty($statusPermissaoAvaliacao['comprou'])): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle small">
                                        <i class="bi bi-patch-check-fill me-1"></i>Compra Confirmada
                                    </span>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($statusPermissaoAvaliacao['ja_avaliou'])): ?>
                                <div class="alert alert-info py-2 px-3 small rounded-3 mb-3">
                                    <i class="bi bi-info-circle me-1"></i>Você já enviou uma avaliação para este produto. Ao enviar novamente, ela será atualizada e passará pela moderação.
                                </div>
                            <?php endif; ?>

                            <?= form_open('avaliacao/enviar', ['id' => 'form-avaliacao-produto']) ?>
                                <input type="hidden" name="produto_id" value="<?= esc($produto['id']) ?>">
                                
                                <!-- Seletor de Estrelas Interativo -->
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted d-block mb-1">
                                        Sua Nota (1 a 5 estrelas) <span class="text-danger">*</span>
                                    </label>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="star-rating-picker" id="star-picker">
                                            <?php 
                                                $notaAtual = (int)($statusPermissaoAvaliacao['avaliacao_existente']['nota'] ?? old('nota') ?? 5);
                                            ?>
                                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                                <input type="radio" name="nota" value="<?= $i ?>" id="star-<?= $i ?>" <?= ($notaAtual === $i) ? 'checked' : '' ?> required>
                                                <label for="star-<?= $i ?>" title="<?= $i ?> estrelas" data-rating="<?= $i ?>">
                                                    <i class="bi bi-star-fill"></i>
                                                </label>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="star-rating-text" id="star-rating-label">Excelente!</span>
                                    </div>
                                </div>

                                <!-- Título Opcional -->
                                <div class="mb-3">
                                    <label for="avaliacao-titulo" class="form-label small fw-bold text-muted mb-1">
                                        Título da Avaliação <small class="fw-normal text-muted">(opcional)</small>
                                    </label>
                                    <input type="text" name="titulo" id="avaliacao-titulo" class="form-control rounded-3"
                                           placeholder="Ex: Excelente qualidade, superou expectativas!"
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

                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold" id="btn-enviar-avaliacao">
                                    <i class="bi bi-send-fill me-1"></i><?= !empty($statusPermissaoAvaliacao['ja_avaliou']) ? 'Atualizar Avaliação' : 'Enviar Avaliação' ?>
                                </button>
                            <?= form_close() ?>
                        </div>
                    <?php endif; ?>
                </div>

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

        // Rótulo inicial baseado no input checado
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

    // --- Lógica de Variações ---
    const variacoes = <?= json_encode($variacoesData ?? []) ?>;
    const basePreco = <?= (float) $produto['preco'] ?>;
    const baseEstoque = <?= (int) $produto['estoque'] ?>;
    const btnAddCart = document.getElementById('btn-add-cart');
    const inputVariacaoId = document.getElementById('variacao_id');
    const priceDisplay = document.getElementById('pdp-price');
    const stockText = document.getElementById('pdp-stock-text');
    const selectedColorName = document.getElementById('selected-color-name');
    
    const formAddCart = document.getElementById('form-add-cart');
    if (formAddCart) {
        formAddCart.addEventListener('submit', function(e) {
            if (variacoes.length > 0 && !inputVariacaoId.value) {
                e.preventDefault();
                alert('Por favor, selecione uma opção disponível antes de adicionar ao carrinho.');
            }
        });
    }
    const sizeRadios = document.querySelectorAll('.variant-size-selector');
    const colorRadios = document.querySelectorAll('.variant-color-selector');
    const hasSizes = sizeRadios.length > 0;
    const hasColors = colorRadios.length > 0;

    function formatMoney(valor) {
        return 'R$ ' + Number(valor).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function checkVariations() {
        if (!hasSizes && !hasColors) return;

        let selectedSize = null;
        let selectedColor = null;

        if (hasSizes) {
            const checkedSize = document.querySelector('.variant-size-selector:checked');
            if (checkedSize) selectedSize = checkedSize.value;
        }

        if (hasColors) {
            const checkedColor = document.querySelector('.variant-color-selector:checked');
            if (checkedColor) selectedColor = checkedColor.value;
        }

        if (selectedColorName) {
            selectedColorName.innerText = selectedColor ? selectedColor : '';
        }

        if (hasSizes) {
            document.querySelectorAll('.variant-size-label').forEach(label => {
                const sizeVal = label.dataset.size;
                const available = variacoes.some(v => v.tamanho === sizeVal && (!hasColors || !selectedColor || v.cor === selectedColor) && v.estoque > 0);
                if (!available) {
                    label.style.opacity = '0.4';
                    label.style.textDecoration = 'line-through';
                    const input = label.querySelector('input');
                    if(input && input.checked) { input.checked = false; selectedSize = null; }
                } else {
                    label.style.opacity = '1';
                    label.style.textDecoration = 'none';
                }
            });
        }

        if (hasColors) {
            document.querySelectorAll('.variant-color-label').forEach(label => {
                const colorVal = label.dataset.color;
                const available = variacoes.some(v => v.cor === colorVal && (!hasSizes || !selectedSize || v.tamanho === selectedSize) && v.estoque > 0);
                if (!available) {
                    label.style.opacity = '0.3';
                    const input = label.querySelector('input');
                    if(input && input.checked) { input.checked = false; selectedColor = null; }
                } else {
                    label.style.opacity = '1';
                }
            });
        }

        let matchingVar = null;
        if ((!hasSizes || selectedSize) && (!hasColors || selectedColor)) {
            matchingVar = variacoes.find(v => 
                (!hasSizes || v.tamanho === selectedSize) && 
                (!hasColors || v.cor === selectedColor)
            );
        }

        if (matchingVar && matchingVar.estoque > 0) {
            inputVariacaoId.value = matchingVar.id;
            btnAddCart.disabled = false;
            btnAddCart.classList.remove('pdp-add-to-cart--disabled');
            
            // Atualizar preço dinâmico na tela
            if (priceDisplay) {
                priceDisplay.innerText = formatMoney(matchingVar.preco);
            }
            if (stockText) {
                stockText.innerText = matchingVar.estoque + ' unidades em estoque';
            }
            if(qtyInput) {
                qtyInput.max = matchingVar.estoque;
                if(parseInt(qtyInput.value) > matchingVar.estoque) {
                    qtyInput.value = matchingVar.estoque;
                }
            }
        } else {
            inputVariacaoId.value = '';
            btnAddCart.disabled = true;
            btnAddCart.classList.add('pdp-add-to-cart--disabled');
            if (priceDisplay) {
                priceDisplay.innerText = formatMoney(basePreco);
            }
            if (stockText) {
                stockText.innerText = baseEstoque + ' unidades em estoque';
            }
        }
    }

    sizeRadios.forEach(radio => radio.addEventListener('change', checkVariations));
    colorRadios.forEach(radio => radio.addEventListener('change', checkVariations));

    if (variacoes.length > 0 && btnAddCart) {
        btnAddCart.disabled = true;
        btnAddCart.classList.add('pdp-add-to-cart--disabled');
        checkVariations();
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