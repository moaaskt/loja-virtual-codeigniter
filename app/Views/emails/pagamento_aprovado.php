<?= $this->extend('emails/layouts/email_base') ?>
<?php $emailTitle = "Pagamento aprovado — Pedido #{$pedido['id']} — G'Store" ?>

<?= $this->section('email_content') ?>

<!-- HERO -->
<div class="email-hero">
  <span class="status-badge badge-success">✅ Pagamento Aprovado</span>
  <h1>Pagamento confirmado!</h1>
  <p>
    Ótimas notícias, <strong><?= esc($cliente['nome'] ?? 'Cliente') ?></strong>!<br>
    Seu pagamento foi aprovado e seu pedido já está sendo preparado para envio.
  </p>
</div>

<!-- BODY -->
<div class="email-body">

  <!-- Status Visual -->
  <div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1px solid #bbf7d0;border-radius:12px;padding:20px 24px;text-align:center;margin-bottom:24px;">
    <div style="font-size:48px;margin-bottom:8px;">✅</div>
    <div style="font-size:20px;font-weight:700;color:#16a34a;">Pagamento Aprovado!</div>
    <div style="font-size:14px;color:#15803d;margin-top:6px;">
      Pedido #<?= esc($pedido['id']) ?> · <?= date('d/m/Y \à\s H:i', strtotime($pedido['criado_em'])) ?>
    </div>
  </div>

  <!-- Dados do Pedido -->
  <div class="info-box">
    <p class="info-box-title">📋 Resumo do Pedido</p>
    <div class="info-row">
      <span class="info-label">Nº do Pedido</span>
      <span class="info-value bold">#<?= esc($pedido['id']) ?></span>
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
      <span class="info-label">Valor Total</span>
      <span class="info-value bold green">R$ <?= number_format((float)$pedido['valor_total'], 2, ',', '.') ?></span>
    </div>
    <div class="info-row">
      <span class="info-label">Status do Pedido</span>
      <span class="info-value" style="color:#16a34a;font-weight:700;">✅ Pago — Em Preparação</span>
    </div>
  </div>

  <!-- Itens do Pedido -->
  <?php if (!empty($itens)) : ?>
  <p style="font-size:13px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;margin:0 0 12px;">
    🛍️ Itens Confirmados
  </p>
  <table class="items-table">
    <thead>
      <tr>
        <th>Produto</th>
        <th style="text-align:center;">Qtd</th>
        <th style="text-align:right;">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($itens as $item) : ?>
      <tr>
        <td>
          <span class="item-name"><?= esc($item['produto_nome'] ?? $item['nome'] ?? 'Produto') ?></span>
          <?php if (!empty($item['tamanho'])) : ?>
            <span class="item-attr">📐 <?= esc($item['tamanho']) ?></span>
          <?php endif; ?>
          <?php if (!empty($item['cor'])) : ?>
            <span class="item-attr">🎨 <?= esc($item['cor']) ?></span>
          <?php endif; ?>
        </td>
        <td style="text-align:center;" class="item-qty"><?= esc($item['quantidade']) ?>x</td>
        <td style="text-align:right;" class="item-price">
          R$ <?= number_format((float)($item['preco_unitario'] ?? 0) * (int)($item['quantidade'] ?? 1), 2, ',', '.') ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>

  <!-- Próximos Passos -->
  <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:20px 24px;margin-bottom:24px;">
    <p class="info-box-title">🚀 Próximos Passos</p>
    <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:12px;">
      <div style="width:28px;height:28px;background:#dcfce7;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;">✅</div>
      <div>
        <div style="font-size:14px;font-weight:600;color:#1e293b;">Pagamento Confirmado</div>
        <div style="font-size:13px;color:#64748b;">Seu pagamento foi aprovado com sucesso.</div>
      </div>
    </div>
    <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:12px;">
      <div style="width:28px;height:28px;background:#dbeafe;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;">📦</div>
      <div>
        <div style="font-size:14px;font-weight:600;color:#1e293b;">Separação e Embalagem</div>
        <div style="font-size:13px;color:#64748b;">Estamos preparando seu pedido com cuidado.</div>
      </div>
    </div>
    <div style="display:flex;align-items:flex-start;gap:12px;">
      <div style="width:28px;height:28px;background:#f3e8ff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;">🚚</div>
      <div>
        <div style="font-size:14px;font-weight:600;color:#1e293b;">Envio</div>
        <div style="font-size:13px;color:#64748b;">Você receberá um e-mail com o código de rastreamento.</div>
      </div>
    </div>
  </div>

  <!-- CTA -->
  <div class="cta-center">
    <a href="<?= site_url('minha-conta/pedidos') ?>" class="cta-btn">
      Acompanhar Pedido →
    </a>
  </div>

</div><!-- /email-body -->

<?= $this->endSection() ?>
