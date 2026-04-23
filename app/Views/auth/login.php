<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | G'Store</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --brand: #6366f1;
            --brand-dark: #4f46e5;
        }

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

        /* Animated bg dots */
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
            max-width: 420px;
            position: relative;
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

        /* Floating labels dark theme */
        .form-floating > label {
            color: rgba(255,255,255,.5);
            font-size: .875rem;
        }

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

        .input-icon {
            position: absolute;
            right: .875rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,.3);
            z-index: 5;
            cursor: pointer;
            font-size: .9375rem;
            transition: color .2s;
        }

        .input-icon:hover { color: rgba(255,255,255,.7); }

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
            letter-spacing: .01em;
        }

        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(99,102,241,.55);
            color: #fff;
        }

        .btn-auth:active { transform: translateY(0); }

        .auth-link { color: rgba(255,255,255,.6); font-size: .875rem; }
        .auth-link a { color: #818cf8; text-decoration: none; font-weight: 600; }
        .auth-link a:hover { color: #fff; text-decoration: underline; }

        .divider {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin: 1.25rem 0;
            color: rgba(255,255,255,.25);
            font-size: .75rem;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,.12);
        }

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
        <h1 class="auth-title">Bem-vindo(a) de volta</h1>
        <p class="auth-subtitle">Acesse sua conta G'Store</p>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-circle-fill"></i>
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?= form_open('auth/attempt-login') ?>

        <div class="form-floating mb-3 position-relative">
            <input type="email" name="email" id="email" class="form-control"
                placeholder="seu@email.com"
                value="<?= old('email') ?? '' ?>" required>
            <label for="email"><i class="bi bi-envelope me-2"></i>E-mail</label>
        </div>

        <div class="form-floating mb-4 position-relative">
            <input type="password" name="senha" id="senha" class="form-control"
                placeholder="••••••••" required>
            <label for="senha"><i class="bi bi-lock me-2"></i>Senha</label>
            <span class="input-icon" id="toggle-senha">
                <i class="bi bi-eye" id="eye-icon"></i>
            </span>
        </div>

        <button type="submit" class="btn btn-auth mb-3" id="btn-login">
            Entrar na minha conta
        </button>

        <?= form_close() ?>

        <div class="divider">ou</div>

        <p class="auth-link text-center mb-1">
            Não tem conta? <a href="<?= site_url('registrar') ?>">Criar agora</a>
        </p>
        <p class="text-center">
            <a href="#" class="auth-link" style="font-size:.8125rem; color:rgba(255,255,255,.35); text-decoration:none;">
                Esqueceu a senha?
            </a>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const senhaInput = document.getElementById('senha');
        const eyeIcon    = document.getElementById('eye-icon');
        document.getElementById('toggle-senha').addEventListener('click', function () {
            const isText = senhaInput.type === 'text';
            senhaInput.type = isText ? 'password' : 'text';
            eyeIcon.className = isText ? 'bi bi-eye' : 'bi bi-eye-slash';
        });
    </script>
</body>

</html>