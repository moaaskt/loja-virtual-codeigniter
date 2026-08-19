<?= $this->extend('emails/layouts/email_base') ?>
<?php $emailTitle = "Pedido #{$pedido['id']} recebido — G'Store" ?>

<?= $this->section('email_content') ?>

<!-- HERO -->
<div class="email-hero">
  <span class="status-badge badge-purple">🛒 Pedido Recebido</span>
  <h1>Seu pedido foi realizado!</h1>
  <p>
    Olá, <strong><?= esc($cliente['nome'] ?? 'Cliente') ?></strong>! Recebemos seu pedido com sucesso.<br>
    Assim que o pagamento for confirmado, você receberá um novo e-mail.
  </p>
</div>

<!-- BODY -->
<div class="email-body">

  <!-- Dados do Pedido -->
  <div class="info-box">
    <p class="info-box-title">📋 Dados do Pedido</p>
    <div class="info-row">
      <span class="info-label">Nº do Pedido</span>
      <span class="info-value bold">#<?= esc($pedido['id']) ?></span>
    </div>
    <div class="info-row">
      <span class="info-label">Data</span>
      <span class="info-value"><?= date('d/m/Y H:i', strtotime($pedido['criado_em'])) ?></span>
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
      <span class="info-value" style="color:#d97706;font-weight:700;">⏳ Aguardando Pagamento</span>
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
        <th style="text-align:right;">Preço</th>
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
        <td style="text-align:center;">
          <span class="item-qty"><?= esc($item['quantidade']) ?>x</span>
        </td>
        <td style="text-align:right;" class="item-price">
          R$ <?= number_format((float)($item['preco_unitario'] ?? 0), 2, ',', '.') ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>

  <!-- Totais -->
  <div class="totals-box">
    <?php $subtotal = array_sum(array_map(fn($i) => ($i['preco_unitario'] ?? 0) * ($i['quantidade'] ?? 1), $itens ?? [])); ?>
    <div class="totals-row">
      <span class="totals-label">Subtotal</span>
      <span class="totals-value">R$ <?= number_format($subtotal, 2, ',', '.') ?></span>
    </div>
    <?php if (!empty($pedido['desconto_valor']) && (float)$pedido['desconto_valor'] > 0) : ?>
    <div class="totals-row">
      <span class="totals-label">Desconto (<?= esc($pedido['cupom_codigo']) ?>)</span>
      <span class="totals-value discount">− R$ <?= number_format((float)$pedido['desconto_valor'], 2, ',', '.') ?></span>
    </div>
    <?php endif; ?>
    <?php if (!empty($pedido['frete_valor']) && (float)$pedido['frete_valor'] > 0) : ?>
    <div class="totals-row">
      <span class="totals-label">Frete (<?= esc($pedido['frete_modalidade'] ?? 'Padrão') ?>)</span>
      <span class="totals-value">R$ <?= number_format((float)$pedido['frete_valor'], 2, ',', '.') ?></span>
    </div>
    <?php endif; ?>
    <div class="totals-row">
      <span class="totals-label">Total</span>
      <span class="totals-value">R$ <?= number_format((float)$pedido['valor_total'], 2, ',', '.') ?></span>
    </div>
  </div>

  <!-- Endereço de Entrega -->
  <div class="info-box">
    <p class="info-box-title">📦 Endereço de Entrega</p>
    <div class="info-row">
      <span class="info-label">Endereço</span>
      <span class="info-value">
        <?= esc($pedido['logradouro']) ?>, <?= esc($pedido['numero']) ?>
        <?= !empty($pedido['complemento']) ? ' — ' . esc($pedido['complemento']) : '' ?>
      </span>
    </div>
    <div class="info-row">
      <span class="info-label">Bairro</span>
      <span class="info-value"><?= esc($pedido['bairro']) ?></span>
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

  <!-- CTA -->
  <div class="cta-center">
    <a href="<?= site_url('minha-conta/pedidos') ?>" class="cta-btn">
      Ver Meus Pedidos →
    </a>
  </div>

  <p style="font-size:13px;color:#94a3b8;text-align:center;margin:0;">
    Dúvidas? Responda a este e-mail ou fale com nosso suporte em<br>
    <a href="mailto:suporte@gstore.com.br" style="color:#6366f1;">suporte@gstore.com.br</a>
  </p>

</div><!-- /email-body -->

<?= $this->endSection() ?>
