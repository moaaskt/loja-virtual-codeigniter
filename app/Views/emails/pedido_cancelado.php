<?= $this->extend('emails/layouts/email_base') ?>
<?php $emailTitle = "Pedido #{$pedido['id']} cancelado — G'Store" ?>

<?= $this->section('email_content') ?>

<!-- HERO -->
<div class="email-hero">
  <span class="status-badge badge-danger">❌ Pedido Cancelado</span>
  <h1>Seu pedido foi cancelado</h1>
  <p>
    Olá, <strong><?= esc($cliente['nome'] ?? 'Cliente') ?></strong>.<br>
    Infelizmente o seu pedido precisou ser cancelado. Veja os detalhes abaixo.
  </p>
</div>

<!-- BODY -->
<div class="email-body">

  <!-- Motivo do Cancelamento -->
  <div style="background:linear-gradient(135deg,#fff5f5,#fee2e2);border:1px solid #fecaca;border-radius:12px;padding:20px 24px;margin-bottom:24px;">
    <p style="font-size:12px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:#dc2626;margin:0 0 8px;">
      ⚠️ Motivo do Cancelamento
    </p>
    <p style="font-size:15px;color:#7f1d1d;margin:0;line-height:1.6;">
      <?= esc($motivo ?? 'Pedido cancelado pelo administrador.') ?>
    </p>
  </div>

  <!-- Dados do Pedido Cancelado -->
  <div class="info-box">
    <p class="info-box-title">📋 Dados do Pedido Cancelado</p>
    <div class="info-row">
      <span class="info-label">Nº do Pedido</span>
      <span class="info-value bold">#<?= esc($pedido['id']) ?></span>
    </div>
    <div class="info-row">
      <span class="info-label">Data do Pedido</span>
      <span class="info-value"><?= date('d/m/Y H:i', strtotime($pedido['criado_em'])) ?></span>
    </div>
    <div class="info-row">
      <span class="info-label">Valor Total</span>
      <span class="info-value">R$ <?= number_format((float)$pedido['valor_total'], 2, ',', '.') ?></span>
    </div>
    <div class="info-row">
      <span class="info-label">Forma de Pagamento</span>
      <span class="info-value">
        <?php
        $fp = $pedido['forma_pagamento'] ?? 'pix';
        echo match($fp) {
            'pix'            => '🏦 Pix',
            'cartao_credito' => '💳 Cartão de Crédito',
            default          => esc($fp),
        };
        ?>
      </span>
    </div>
    <div class="info-row">
      <span class="info-label">Status</span>
      <span class="info-value red" style="font-weight:700;">❌ Cancelado</span>
    </div>
  </div>

  <!-- Itens do Pedido -->
  <?php if (!empty($itens)) : ?>
  <p style="font-size:13px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;margin:0 0 12px;">
    🛍️ Itens do Pedido
  </p>
  <table class="items-table">
    <thead>
      <tr>
        <th>Produto</th>
        <th style="text-align:center;">Qtd</th>
        <th style="text-align:right;">Preço Unit.</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($itens as $item) : ?>
      <tr>
        <td>
          <span class="item-name" style="text-decoration:line-through;color:#94a3b8;">
            <?= esc($item['produto_nome'] ?? $item['nome'] ?? 'Produto') ?>
          </span>
          <?php if (!empty($item['tamanho'])) : ?>
            <span class="item-attr">📐 <?= esc($item['tamanho']) ?></span>
          <?php endif; ?>
          <?php if (!empty($item['cor'])) : ?>
            <span class="item-attr">🎨 <?= esc($item['cor']) ?></span>
          <?php endif; ?>
        </td>
        <td style="text-align:center;color:#94a3b8;"><?= esc($item['quantidade']) ?>x</td>
        <td style="text-align:right;color:#94a3b8;">
          R$ <?= number_format((float)($item['preco_unitario'] ?? 0), 2, ',', '.') ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>

  <!-- Informações de Reembolso -->
  <div style="background:#fefce8;border:1px solid #fde68a;border-radius:12px;padding:20px 24px;margin:24px 0;">
    <p style="font-size:13px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:#d97706;margin:0 0 8px;">
      💰 Informações sobre Reembolso
    </p>
    <p style="font-size:14px;color:#78350f;margin:0;line-height:1.7;">
      Caso o pagamento já tenha sido processado, o estorno será realizado automaticamente
      em até <strong>5 a 10 dias úteis</strong> para o método de pagamento utilizado.<br>
      Para pagamentos via Pix, o prazo é de até <strong>2 dias úteis</strong>.
    </p>
  </div>

  <!-- Suporte -->
  <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:20px 24px;margin-bottom:24px;text-align:center;">
    <div style="font-size:32px;margin-bottom:8px;">💬</div>
    <div style="font-size:16px;font-weight:600;color:#1e293b;margin-bottom:8px;">Precisa de ajuda?</div>
    <p style="font-size:14px;color:#64748b;margin:0 0 16px;">
      Nossa equipe está disponível para esclarecer dúvidas sobre o cancelamento.
    </p>
    <a href="mailto:suporte@gstore.com.br"
      style="display:inline-block;padding:10px 24px;background:#64748b;color:#fff;text-decoration:none;border-radius:8px;font-size:14px;font-weight:600;">
      Falar com Suporte
    </a>
  </div>

  <!-- CTA Continuar Comprando -->
  <div class="cta-center">
    <a href="<?= site_url('/') ?>" class="cta-btn" style="background:linear-gradient(135deg,#1e293b,#334155);">
      Continuar Comprando →
    </a>
  </div>

</div><!-- /email-body -->

<?= $this->endSection() ?>
