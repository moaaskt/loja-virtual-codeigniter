<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1"><i class="bi bi-box-seam text-info me-2"></i>Produtos Mais Vendidos</h1>
        <p class="text-muted small mb-0">Ranking de desempenho comercial, receita gerada e nível de estoque de produtos.</p>
    </div>
    <div>
        <a href="<?= site_url('admin/relatorios/exportar/produtos?periodo=' . $periodoInfo['periodo'] . '&data_inicio=' . $periodoInfo['data_inicio_formatada'] . '&data_fim=' . $periodoInfo['data_fim_formatada']) ?>" class="btn btn-outline-success btn-sm rounded-pill px-3">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Exportar Ranking (CSV)
        </a>
    </div>
</div>

<!-- ===== BARRA DE FILTRO POR PERÍODO ===== -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body p-3">
        <form method="GET" action="<?= site_url('admin/relatorios/produtos') ?>" class="row g-2 align-items-center">
            <div class="col-auto">
                <span class="text-muted small fw-semibold"><i class="bi bi-calendar-event me-1"></i>Período:</span>
            </div>
            <div class="col-auto d-flex flex-wrap gap-1">
                <a href="<?= site_url('admin/relatorios/produtos?periodo=hoje') ?>" class="btn btn-sm rounded-pill <?= $periodoInfo['periodo'] === 'hoje' ? 'btn-primary' : 'btn-outline-secondary' ?>">Hoje</a>
                <a href="<?= site_url('admin/relatorios/produtos?periodo=7d') ?>" class="btn btn-sm rounded-pill <?= $periodoInfo['periodo'] === '7d' ? 'btn-primary' : 'btn-outline-secondary' ?>">7 Dias</a>
                <a href="<?= site_url('admin/relatorios/produtos?periodo=30d') ?>" class="btn btn-sm rounded-pill <?= $periodoInfo['periodo'] === '30d' ? 'btn-primary' : 'btn-outline-secondary' ?>">30 Dias</a>
                <a href="<?= site_url('admin/relatorios/produtos?periodo=mes_atual') ?>" class="btn btn-sm rounded-pill <?= $periodoInfo['periodo'] === 'mes_atual' ? 'btn-primary' : 'btn-outline-secondary' ?>">Mês Atual</a>
                <a href="<?= site_url('admin/relatorios/produtos?periodo=ano_atual') ?>" class="btn btn-sm rounded-pill <?= $periodoInfo['periodo'] === 'ano_atual' ? 'btn-primary' : 'btn-outline-secondary' ?>">Ano Atual</a>
            </div>
            
            <div class="col-12 col-md-auto ms-md-auto d-flex align-items-center gap-2 mt-2 mt-md-0">
                <input type="hidden" name="periodo" value="custom">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-muted small">De</span>
                    <input type="date" class="form-control" name="data_inicio" value="<?= esc($periodoInfo['data_inicio_formatada']) ?>" required>
                </div>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-muted small">Até</span>
                    <input type="date" class="form-control" name="data_fim" value="<?= esc($periodoInfo['data_fim_formatada']) ?>" required>
                </div>
                <button type="submit" class="btn btn-primary btn-sm px-3 rounded-pill">
                    <i class="bi bi-funnel-fill"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ===== TABELA DE PRODUTOS ===== -->
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-muted">
                    <th class="text-center" style="width:60px;">Posição</th>
                    <th>Produto</th>
                    <th>Categoria</th>
                    <th class="text-center">Estoque Atual</th>
                    <th class="text-center">Qtd. Vendida</th>
                    <th class="text-end">Receita Gerada</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($produtos)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-box-seam fs-1 d-block mb-2 text-secondary"></i>
                            Nenhum produto vendido no período selecionado.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php 
                    $curPage = $page ?? 1;
                    $perP = $perPage ?? 20;
                    foreach ($produtos as $index => $p): 
                        $globalIndex = (($curPage - 1) * $perP) + $index + 1;
                    ?>
                        <tr>
                            <td class="text-center">
                                <?php if ($globalIndex === 1): ?>
                                    <span class="badge bg-warning text-dark rounded-circle p-2 fs-6"><i class="bi bi-trophy-fill"></i></span>
                                <?php elseif ($globalIndex === 2): ?>
                                    <span class="badge bg-secondary text-white rounded-circle p-2 fs-6"><i class="bi bi-award-fill"></i></span>
                                <?php elseif ($globalIndex === 3): ?>
                                    <span class="badge bg-bronze rounded-circle p-2 fs-6" style="background:#b45309; color:#fff;"><i class="bi bi-award"></i></span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted rounded-circle px-2 py-1">#<?= $globalIndex ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <?php if (!empty($p['imagem'])): ?>
                                        <img src="<?= base_url('uploads/' . $p['imagem']) ?>" alt="<?= esc($p['nome']) ?>" class="rounded" style="width:40px;height:40px;object-fit:cover;">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted" style="width:40px;height:40px;">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="fw-semibold text-dark"><?= esc($p['nome']) ?></div>
                                        <small class="text-muted" style="font-size:.75rem;">ID: #<?= $p['id'] ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= esc($p['categoria_nome']) ?></span></td>
                            <td class="text-center">
                                <?php if ((int)$p['estoque'] <= 0): ?>
                                    <span class="badge bg-danger-subtle text-danger">Sem Estoque</span>
                                <?php elseif ((int)$p['estoque'] <= 5): ?>
                                    <span class="badge bg-warning-subtle text-warning-emphasis"><?= $p['estoque'] ?> un (Baixo)</span>
                                <?php else: ?>
                                    <span class="badge bg-success-subtle text-success"><?= $p['estoque'] ?> un</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold text-primary fs-6"><?= $p['total_vendido'] ?></span>
                                <small class="text-muted d-block" style="font-size:.75rem;">unidades</small>
                            </td>
                            <td class="text-end fw-bold text-success fs-6">
                                R$ <?= number_format((float) $p['receita_total'], 2, ',', '.') ?>
                            </td>
                            <td class="text-center">
                                <a href="<?= site_url('admin/produtos/edit/' . $p['id']) ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1" title="Editar Produto">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (!empty($pagerLinks) && !empty($produtos)): ?>
        <div class="card-footer bg-white border-top py-3 d-flex justify-content-center">
            <?= $pagerLinks ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
