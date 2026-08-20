<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?= esc($emailTitle ?? "G'Store") ?></title>
<!--[if mso]>
<noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript>
<![endif]-->
<style>
  /* Reset */
  body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
  table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
  img { -ms-interpolation-mode: bicubic; }
  img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
  table { border-collapse: collapse !important; }
  body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; }
  a[x-apple-data-detectors] { color: inherit !important; text-decoration: none !important; font-size: inherit !important; font-family: inherit !important; font-weight: inherit !important; line-height: inherit !important; }

  /* Base */
  body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    background-color: #f1f5f9;
    color: #1e293b;
    margin: 0;
    padding: 0;
  }
  .email-wrapper {
    width: 100%;
    background-color: #f1f5f9;
    padding: 32px 16px;
    box-sizing: border-box;
  }
  .email-container {
    max-width: 600px;
    margin: 0 auto;
    background-color: #ffffff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 24px rgba(0,0,0,0.08);
  }

  /* Header */
  .email-header {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    padding: 32px 40px;
    text-align: center;
  }
  .email-brand {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
  }
  .brand-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
  }
  .brand-name {
    font-size: 24px;
    font-weight: 700;
    color: #ffffff;
    letter-spacing: -0.5px;
  }
  .brand-name span {
    color: #a78bfa;
  }

  /* Hero / Status badge */
  .email-hero {
    padding: 40px 40px 32px;
    text-align: center;
    border-bottom: 1px solid #f1f5f9;
  }
  .status-badge {
    display: inline-block;
    padding: 8px 20px;
    border-radius: 50px;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.5px;
    margin-bottom: 20px;
  }
  .badge-success { background-color: #dcfce7; color: #16a34a; }
  .badge-info    { background-color: #dbeafe; color: #1d4ed8; }
  .badge-warning { background-color: #fef3c7; color: #d97706; }
  .badge-danger  { background-color: #fee2e2; color: #dc2626; }
  .badge-purple  { background-color: #ede9fe; color: #7c3aed; }

  .email-hero h1 {
    font-size: 26px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 12px;
    line-height: 1.3;
  }
  .email-hero p {
    font-size: 15px;
    color: #64748b;
    margin: 0;
    line-height: 1.6;
  }

  /* Body content */
  .email-body {
    padding: 32px 40px;
  }

  /* Info box */
  .info-box {
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 24px;
  }
  .info-box-title {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: #94a3b8;
    margin: 0 0 12px;
  }
  .info-row {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
    font-size: 14px;
    border-bottom: 1px solid #e2e8f0;
  }
  .info-row:last-child { border-bottom: none; }
  .info-label { color: #64748b; }
  .info-value { color: #1e293b; font-weight: 500; }
  .info-value.bold { font-weight: 700; }
  .info-value.green { color: #16a34a; }
  .info-value.red   { color: #dc2626; }

  /* Order items table */
  .items-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 24px;
  }
  .items-table th {
    background-color: #f8fafc;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: #94a3b8;
    padding: 10px 12px;
    text-align: left;
    border-bottom: 2px solid #e2e8f0;
  }
  .items-table td {
    padding: 14px 12px;
    font-size: 14px;
    color: #374151;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: top;
  }
  .items-table tr:last-child td { border-bottom: none; }
  .item-name {
    font-weight: 600;
    color: #1e293b;
    display: block;
    margin-bottom: 4px;
  }
  .item-attr {
    font-size: 12px;
    color: #94a3b8;
    display: block;
  }
  .item-qty {
    font-weight: 600;
    color: #6366f1;
  }
  .item-price { font-weight: 500; }

  /* Totals */
  .totals-box {
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 24px;
  }
  .totals-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 20px;
    font-size: 14px;
    border-bottom: 1px solid #e2e8f0;
  }
  .totals-row:last-child {
    border-bottom: none;
    background-color: #1e293b;
    border-radius: 0 0 11px 11px;
  }
  .totals-row:last-child .totals-label,
  .totals-row:last-child .totals-value {
    color: #ffffff;
    font-weight: 700;
    font-size: 16px;
  }
  .totals-label { color: #64748b; }
  .totals-value { font-weight: 600; color: #1e293b; }
  .totals-value.discount { color: #16a34a; }

  /* CTA Button */
  .cta-center { text-align: center; margin: 28px 0; }
  .cta-btn {
    display: inline-block;
    padding: 14px 36px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #ffffff !important;
    text-decoration: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    letter-spacing: 0.3px;
    box-shadow: 0 4px 14px rgba(99,102,241,0.35);
  }

  /* Tracking box */
  .tracking-box {
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    border: 1px solid #bfdbfe;
    border-radius: 12px;
    padding: 20px 24px;
    text-align: center;
    margin-bottom: 24px;
  }
  .tracking-code {
    font-family: 'Courier New', Courier, monospace;
    font-size: 22px;
    font-weight: 700;
    color: #1d4ed8;
    letter-spacing: 3px;
    display: block;
    margin: 12px 0 8px;
  }

  /* Divider */
  .divider {
    height: 1px;
    background-color: #f1f5f9;
    margin: 28px 0;
  }

  /* Footer */
  .email-footer {
    background-color: #1e293b;
    padding: 32px 40px;
    text-align: center;
  }
  .footer-brand {
    font-size: 18px;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 16px;
  }
  .footer-links {
    margin-bottom: 20px;
  }
  .footer-links a {
    color: #94a3b8;
    text-decoration: none;
    font-size: 13px;
    margin: 0 12px;
  }
  .footer-links a:hover { color: #ffffff; }
  .footer-text {
    font-size: 12px;
    color: #64748b;
    line-height: 1.6;
    margin: 0;
  }
  .footer-text a { color: #6366f1; text-decoration: none; }

  /* Responsive */
  @media only screen and (max-width: 620px) {
    .email-header, .email-hero, .email-body, .email-footer { padding-left: 20px !important; padding-right: 20px !important; }
    .email-hero h1 { font-size: 22px; }
    .info-row, .totals-row { font-size: 13px; }
  }
</style>
</head>
<body>
<div class="email-wrapper">
  <div class="email-container">

    <!-- HEADER -->
    <div class="email-header">
      <div class="email-brand">
        <div class="brand-icon">🛍️</div>
        <span class="brand-name">G<span>'</span>Store</span>
      </div>
    </div>

    <!-- CONTEÚDO DINÂMICO -->
    <?= $this->renderSection('email_content') ?>

    <!-- FOOTER -->
    <div class="email-footer">
      <div class="footer-brand">G'Store</div>
      <div class="footer-links">
        <a href="<?= site_url('/') ?>">Loja</a>
        <a href="<?= site_url('minha-conta/pedidos') ?>">Meus Pedidos</a>
        <a href="<?= site_url('login') ?>">Minha Conta</a>
      </div>
      <p class="footer-text">
        Este é um e-mail automático. Por favor, não responda a esta mensagem.<br>
        Dúvidas? Entre em contato: <a href="mailto:suporte@gstore.com.br">suporte@gstore.com.br</a><br><br>
        © <?= date('Y') ?> G'Store — Todos os direitos reservados.
      </p>
    </div>

  </div><!-- /email-container -->
</div><!-- /email-wrapper -->
</body>
</html>
