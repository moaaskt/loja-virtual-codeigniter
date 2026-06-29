<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastro | G'Store</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root { --brand: #6366f1; --brand-dark: #4f46e5; }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                radial-gradient(circle at 20% 20%, rgba(99,102,241,.2) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(34,211,238,.12) 0%, transparent 50%);
            pointer-events: none;
        }

        .auth-card {
            background: rgba(255,255,255,.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 20px;
            padding: 2.5rem;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 25px 50px rgba(0,0,0,.4), inset 0 1px 0 rgba(255,255,255,.1);
        }

        .auth-logo {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, var(--brand), #22d3ee);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.375rem;
            color: #fff;
            margin: 0 auto 1.25rem;
        }

        .auth-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -.03em;
            text-align: center;
            margin-bottom: .375rem;
        }

        .auth-subtitle {
            font-size: .875rem;
            color: rgba(255,255,255,.5);
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-floating > label { color: rgba(255,255,255,.5); font-size: .875rem; }

        .form-floating > .form-control {
            background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 10px;
            color: #fff;
            font-size: .9375rem;
            transition: all .2s ease;
        }

        .form-floating > .form-control::placeholder { color: transparent; }

        .form-floating > .form-control:focus {
            background: rgba(255,255,255,.1);
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(99,102,241,.25);
            color: #fff;
        }

        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            color: rgba(255,255,255,.7);
        }

        .btn-auth {
            width: 100%;
            background: linear-gradient(135deg, var(--brand), var(--brand-dark));
            border: none;
            border-radius: 10px;
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            padding: .875rem;
            transition: all .25s ease;
            box-shadow: 0 4px 15px rgba(99,102,241,.4);
        }

        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(99,102,241,.55);
            color: #fff;
        }

        .auth-link { color: rgba(255,255,255,.6); font-size: .875rem; }
        .auth-link a { color: #818cf8; text-decoration: none; font-weight: 600; }
        .auth-link a:hover { color: #fff; text-decoration: underline; }

        /* Validation errors */
        .alert-danger {
            background: rgba(239,68,68,.15);
            border: 1px solid rgba(239,68,68,.3);
            color: #fca5a5;
            border-radius: 10px;
            font-size: .875rem;
            margin-bottom: 1.25rem;
        }
    </style>
</head>

<body>

    <div class="auth-card">
        <div class="auth-logo">
            <i class="bi bi-bag-heart-fill"></i>
        </div>
        <h1 class="auth-title">Criar sua conta</h1>
        <p class="auth-subtitle">Junte-se à G'Store e comece a comprar</p>

        <?= validation_list_errors() ?>

        <?= form_open('registrar/salvar') ?>

        <div class="form-floating mb-3">
            <input type="text" name="nome" id="nome" class="form-control"
                placeholder="Nome Completo"
                value="<?= old('nome') ?>" required>
            <label for="nome"><i class="bi bi-person me-2"></i>Nome Completo</label>
        </div>

        <div class="form-floating mb-3">
            <input type="email" name="email" id="email" class="form-control"
                placeholder="seu@email.com"
                value="<?= old('email') ?>" required>
            <label for="email"><i class="bi bi-envelope me-2"></i>E-mail</label>
        </div>

        <div class="form-floating mb-3">
            <input type="password" name="senha_hash" id="senha_hash" class="form-control"
                placeholder="••••••••" required>
            <label for="senha_hash"><i class="bi bi-lock me-2"></i>Senha</label>
        </div>

        <div class="form-floating mb-4">
            <input type="password" name="password_confirm" id="password_confirm" class="form-control"
                placeholder="••••••••" required>
            <label for="password_confirm"><i class="bi bi-lock-fill me-2"></i>Confirmar Senha</label>
        </div>

        <button type="submit" class="btn btn-auth mb-3" id="btn-criar-conta">
            <i class="bi bi-person-plus me-2"></i>Criar minha conta
        </button>

        <?= form_close() ?>

        <p class="auth-link text-center mb-0">
            Já tem conta? <a href="<?= site_url('login') ?>">Faça login</a>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>