<?= $this->extend('emails/layouts/email_base') ?>
<?php $emailTitle = "Pedido #{$pedido['id']} enviado — G'Store" ?>

<?= $this->section('email_content') ?>

<!-- HERO -->
<div class="email-hero">
  <span class="status-badge badge-info">🚚 Pedido Enviado</span>
  <h1>Seu pedido está a caminho!</h1>
  <p>
    Olá, <strong><?= esc($cliente['nome'] ?? 'Cliente') ?></strong>!<br>
    Seu pedido foi despachado e está em trânsito. Acompanhe a entrega abaixo.
  </p>
</div>

<!-- BODY -->
<div class="email-body">

  <!-- Código de Rastreio -->
  <?php if (!empty($codigo_rastreio)) : ?>
  <div class="tracking-box">
    <p style="font-size:13px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:#1d4ed8;margin:0 0 8px;">
      📍 Código de Rastreamento
    </p>
    <span class="tracking-code"><?= esc($codigo_rastreio) ?></span>
    <p style="font-size:13px;color:#3b82f6;margin:0;">
      Modalidade: <strong><?= esc($pedido['frete_modalidade'] ?? 'Padrão') ?></strong>
    </p>
    <div style="margin-top:16px;">
      <a href="https://www.correios.com.br/rastreamento" target="_blank"
        style="display:inline-block;padding:10px 24px;background:#1d4ed8;color:#fff;text-decoration:none;border-radius:8px;font-size:14px;font-weight:600;">
        Rastrear pelos Correios →
      </a>
    </div>
  </div>
  <?php else : ?>
  <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:20px 24px;text-align:center;margin-bottom:24px;">
    <div style="font-size:40px;margin-bottom:8px;">🚚</div>
    <div style="font-size:16px;font-weight:600;color:#1e293b;">Pedido em Trânsito</div>
    <div style="font-size:13px;color:#64748b;margin-top:4px;">
      O código de rastreamento será disponibilizado em breve.
    </div>
  </div>
  <?php endif; ?>

  <!-- Dados do Envio -->
  <div class="info-box">
    <p class="info-box-title">📦 Dados de Entrega</p>
    <div class="info-row">
      <span class="info-label">Nº do Pedido</span>
      <span class="info-value bold">#<?= esc($pedido['id']) ?></span>
    </div>
    <div class="info-row">
      <span class="info-label">Modalidade de Frete</span>
      <span class="info-value"><?= esc($pedido['frete_modalidade'] ?? 'Padrão') ?></span>
    </div>
    <div class="info-row">
      <span class="info-label">Endereço</span>
      <span class="info-value">
        <?= esc($pedido['logradouro']) ?>, <?= esc($pedido['numero']) ?>
        <?= !empty($pedido['complemento']) ? ' — ' . esc($pedido['complemento']) : '' ?>
      </span>
    </div>
    <div class="info-row">
      <span class="info-label">Cidade / UF</span>
      <span class="info-value"><?= esc($pedido['cidade']) ?> — <?= esc($pedido['uf']) ?></span>
    </div>
    <div class="info-row">
      <span class="info-label">CEP</span>
      <span class="info-value"><?= esc($pedido['cep']) ?></span>
    </div>
  </div>

  <!-- Itens enviados -->
  <?php if (!empty($itens)) : ?>
  <p style="font-size:13px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;margin:0 0 12px;">
    🛍️ Itens Enviados
  </p>
  <table class="items-table">
    <thead>
      <tr>
        <th>Produto</th>
        <th style="text-align:center;">Qtd</th>
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
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>

  <!-- Timeline de Status -->
  <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:20px 24px;margin:24px 0;">
    <p class="info-box-title">📍 Status do Pedido</p>
    <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:12px;">
      <div style="width:28px;height:28px;background:#dcfce7;border-radius:50%;text-align:center;line-height:28px;font-size:14px;flex-shrink:0;">✅</div>
      <div>
        <div style="font-size:14px;font-weight:600;color:#1e293b;">Pedido Realizado</div>
      </div>
    </div>
    <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:12px;">
      <div style="width:28px;height:28px;background:#dcfce7;border-radius:50%;text-align:center;line-height:28px;font-size:14px;flex-shrink:0;">✅</div>
      <div>
        <div style="font-size:14px;font-weight:600;color:#1e293b;">Pagamento Aprovado</div>
      </div>
    </div>
    <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:12px;">
      <div style="width:28px;height:28px;background:#dbeafe;border-radius:50%;text-align:center;line-height:28px;font-size:14px;flex-shrink:0;">🚚</div>
      <div>
        <div style="font-size:14px;font-weight:700;color:#1d4ed8;">Em Trânsito ← Você está aqui</div>
      </div>
    </div>
    <div style="display:flex;align-items:flex-start;gap:12px;">
      <div style="width:28px;height:28px;background:#f1f5f9;border-radius:50%;text-align:center;line-height:28px;font-size:14px;flex-shrink:0;color:#94a3b8;">🏠</div>
      <div>
        <div style="font-size:14px;color:#94a3b8;">Entregue</div>
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
