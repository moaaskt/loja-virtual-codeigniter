<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?><?= esc($title ?? 'Moderação de Avaliações') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">
            <i class="bi bi-star-half text-warning me-2"></i>Moderação de Avaliações
        </h1>
        <p class="text-muted small mb-0">Gerencie, aprove e modere as avaliações e comentários enviados pelos clientes.</p>
    </div>
</div>

<!-- ===== KPI CARDS ===== -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm rounded-3 p-3 h-100 bg-white">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">Total</span>
                <span class="badge bg-secondary-subtle text-secondary rounded-pill"><i class="bi bi-chat-quote"></i></span>
            </div>
            <div class="fs-4 fw-bold text-dark"><?= (int)($contadores['total'] ?? 0) ?></div>
            <small class="text-muted" style="font-size:0.75rem;">Todas as avaliações</small>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm rounded-3 p-3 h-100 bg-white border-start border-warning border-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-warning-emphasis small fw-semibold">Pendentes</span>
                <span class="badge bg-warning-subtle text-warning rounded-pill"><i class="bi bi-hourglass-split"></i></span>
            </div>
            <div class="fs-4 fw-bold text-warning-emphasis"><?= (int)($contadores['pendentes'] ?? 0) ?></div>
            <small class="text-muted" style="font-size:0.75rem;">Aguardando moderação</small>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm rounded-3 p-3 h-100 bg-white border-start border-success border-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-success small fw-semibold">Aprovadas</span>
                <span class="badge bg-success-subtle text-success rounded-pill"><i class="bi bi-check-circle-fill"></i></span>
            </div>
            <div class="fs-4 fw-bold text-success"><?= (int)($contadores['aprovadas'] ?? 0) ?></div>
            <small class="text-muted" style="font-size:0.75rem;">Visíveis na loja</small>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm rounded-3 p-3 h-100 bg-white border-start border-danger border-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-danger small fw-semibold">Rejeitadas</span>
                <span class="badge bg-danger-subtle text-danger rounded-pill"><i class="bi bi-x-circle-fill"></i></span>
            </div>
            <div class="fs-4 fw-bold text-danger"><?= (int)($contadores['rejeitadas'] ?? 0) ?></div>
            <small class="text-muted" style="font-size:0.75rem;">Ocultas na loja</small>
        </div>
    </div>
    <div class="col-12 col-md-8 col-xl-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 h-100 bg-white border-start border-primary border-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-primary small fw-semibold">Média Geral da Loja</span>
                <span class="badge bg-warning-subtle text-warning rounded-pill"><i class="bi bi-star-fill"></i></span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="fs-4 fw-bold text-dark"><?= number_format($contadores['media_geral'] ?? 0, 1, ',', '.') ?></div>
                <div><?= renderEstrelas((float)($contadores['media_geral'] ?? 0), 'sm') ?></div>
            </div>
            <small class="text-muted" style="font-size:0.75rem;">Calculada a partir de reviews aprovadas</small>
        </div>
    </div>
</div>

<!-- ===== BARRA DE FILTROS ===== -->
<div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
    <div class="card-body p-3">
        <?= form_open('admin/avaliacoes', ['method' => 'get', 'class' => 'row g-2 align-items-center']) ?>
            <!-- Status -->
            <div class="col-12 col-md-3">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Status: Todos</option>
                    <option value="pendente" <?= ($filtros['status'] === 'pendente') ? 'selected' : '' ?>>Pendentes</option>
                    <option value="aprovada" <?= ($filtros['status'] === 'aprovada') ? 'selected' : '' ?>>Aprovadas</option>
                    <option value="rejeitada" <?= ($filtros['status'] === 'rejeitada') ? 'selected' : '' ?>>Rejeitadas</option>
                </select>
            </div>

            <!-- Nota -->
            <div class="col-6 col-md-2">
                <select name="nota" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Nota: Todas</option>
                    <option value="5" <?= ($filtros['nota'] === '5') ? 'selected' : '' ?>>5 estrelas</option>
                    <option value="4" <?= ($filtros['nota'] === '4') ? 'selected' : '' ?>>4 estrelas</option>
                    <option value="3" <?= ($filtros['nota'] === '3') ? 'selected' : '' ?>>3 estrelas</option>
                    <option value="2" <?= ($filtros['nota'] === '2') ? 'selected' : '' ?>>2 estrelas</option>
                    <option value="1" <?= ($filtros['nota'] === '1') ? 'selected' : '' ?>>1 estrela</option>
                </select>
            </div>

            <!-- Busca Textual -->
            <div class="col-12 col-md-5">
                <div class="input-group input-group-sm">
                    <input type="text" name="busca" class="form-control" placeholder="Buscar por cliente, produto, comentário..." value="<?= esc($filtros['busca'] ?? '') ?>">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>

            <!-- Limpar Filtros -->
            <div class="col-6 col-md-2 text-end">
                <?php if (!empty($filtros['status']) || !empty($filtros['nota']) || !empty($filtros['busca'])): ?>
                    <a href="<?= site_url('admin/avaliacoes') ?>" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="bi bi-x-circle me-1"></i>Limpar
                    </a>
                <?php endif; ?>
            </div>
        <?= form_close() ?>
    </div>
</div>

<!-- ===== TABELA DE MODERAÇÃO ===== -->
<div class="card border-0 shadow-sm rounded-3 bg-white">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;" class="ps-3">ID</th>
                        <th style="width: 220px;">Produto</th>
                        <th style="width: 180px;">Cliente</th>
                        <th>Avaliação / Comentário</th>
                        <th style="width: 120px;">Status</th>
                        <th style="width: 100px;">Data</th>
                        <th style="width: 150px;" class="text-end pe-3">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($avaliacoes)): ?>
                        <?php foreach ($avaliacoes as $av): ?>
                            <tr>
                                <td class="ps-3 text-muted fw-semibold">#<?= $av['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if (!empty($av['produto_imagem'])): ?>
                                            <img src="<?= strpos($av['produto_imagem'], 'http') === 0 ? esc($av['produto_imagem']) : base_url('uploads/produtos/' . esc($av['produto_imagem'])) ?>"
                                                 alt="<?= esc($av['produto_nome'] ?? '') ?>"
                                                 style="width: 40px; height: 40px; object-fit: cover;" class="rounded border">
                                        <?php else: ?>
                                            <div class="bg-light rounded border d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="bi bi-box text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="text-truncate" style="max-width: 160px;">
                                            <a href="<?= site_url('produto/' . $av['produto_id']) ?>" target="_blank" class="fw-semibold text-dark text-decoration-none small" title="<?= esc($av['produto_nome'] ?? '') ?>">
                                                <?= esc($av['produto_nome'] ?? 'Produto #' . $av['produto_id']) ?>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold small"><?= esc($av['usuario_nome'] ?? 'Usuário #' . $av['usuario_id']) ?></div>
                                    <small class="text-muted d-block" style="font-size:0.75rem;"><?= esc($av['usuario_email'] ?? '') ?></small>
                                    <?php if (!empty($av['compra_verificada'])): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-1 py-0 mt-1" style="font-size:0.6875rem;">
                                            <i class="bi bi-patch-check-fill me-1"></i>Compra Verificada
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="mb-1">
                                        <?= renderEstrelas((float)$av['nota'], 'sm') ?>
                                        <span class="badge bg-light text-dark border ms-1 fw-bold" style="font-size:0.75rem;"><?= $av['nota'] ?>/5</span>
                                    </div>
                                    <?php if (!empty($av['titulo'])): ?>
                                        <div class="fw-bold small text-dark mb-1"><?= esc($av['titulo']) ?></div>
                                    <?php endif; ?>
                                    <p class="text-secondary small mb-0" style="max-width: 450px; line-height: 1.4;">
                                        <?= nl2br(esc($av['comentario'])) ?>
                                    </p>
                                </td>
                                <td>
                                    <?= getBadgeStatusAvaliacao($av['status']) ?>
                                </td>
                                <td>
                                    <small class="text-muted"><?= date('d/m/Y', strtotime($av['created_at'])) ?></small>
                                    <small class="text-muted d-block" style="font-size:0.7rem;"><?= date('H:i', strtotime($av['created_at'])) ?></small>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <?php if ($av['status'] !== 'aprovada'): ?>
                                            <?= form_open('admin/avaliacoes/aprovar/' . $av['id'], ['class' => 'd-inline']) ?>
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Aprovar Avaliação">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            <?= form_close() ?>
                                        <?php endif; ?>

                                        <?php if ($av['status'] !== 'rejeitada'): ?>
                                            <?= form_open('admin/avaliacoes/rejeitar/' . $av['id'], ['class' => 'd-inline']) ?>
                                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Rejeitar Avaliação">
                                                    <i class="bi bi-slash-circle"></i>
                                                </button>
                                            <?= form_close() ?>
                                        <?php endif; ?>

                                        <?= form_open('admin/avaliacoes/delete/' . $av['id'], ['class' => 'd-inline', 'onsubmit' => "return confirm('Tem certeza que deseja excluir permanentemente esta avaliação?');"]) ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir Avaliação">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?= form_close() ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-chat-square-quote fs-2 d-block mb-2 text-secondary"></i>
                                <span>Nenhuma avaliação encontrada com os filtros selecionados.</span>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($pager && $pager->getPageCount() > 1): ?>
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-center">
                <?= $pager->links() ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
