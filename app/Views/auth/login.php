<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | Minha Loja</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      /* background: url('https://images.unsplash.com/photo-1512436991641-6745cdb1723f?q=80&w=2070&auto=format&fit=crop') no-repeat center center fixed; */
      background-size: cover;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-card {
      background: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 16px;
      padding: 40px;
      max-width: 420px;
      width: 100%;
      color: white;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.4);
    }

    .form-control,
    .input-group-text {
      background: rgba(255, 255, 255, 0.1);
      border: none;
      color: white;
    }

    .form-control:focus {
      background: rgba(255, 255, 255, 0.2);
      color: white;
      box-shadow: none;
    }

    .form-label {
      color: rgba(255, 255, 255, 0.9);
    }

    .form-control::placeholder {
      color: rgba(255, 255, 255, 0.6);
    }

    .btn-neon {
      background-color: #0d6efd;
      font-weight: 600;
      border: none;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(13, 110, 253, 0.5);
      transition: all 0.3s ease;
    }

    .btn-neon:hover {
      box-shadow: 0 0 15px #0d6efd, 0 0 30px #0d6efd;
      transform: scale(1.02);
    }

    a {
      color: #cce6ff;
      text-decoration: none;
    }

    a:hover {
      color: #ffffff;
      text-decoration: underline;
    }
  </style>
</head>

<body>

  <div class="login-card">
    <div class="text-center mb-4">
      <h2 class="fw-bold">Bem-vindo(a)</h2>
      <p class="text-white-50">Acesse sua conta para continuar</p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?= form_open('auth/attempt-login') ?>
    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
        <input type="email" name="email" id="email" class="form-control" placeholder="seu@email.com"
          value="<?= old('email') ?? 'admin@teste.com' ?>" required>
      </div>
    </div>

    <div class="mb-4">
      <label for="senha" class="form-label">Senha</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
        <input type="password" name="senha" id="senha" class="form-control" placeholder="••••••••" required>
      </div>
    </div>

    <div class="d-grid mb-3">
      <button type="submit" class="btn btn-neon btn-lg">Entrar</button>
    </div>

    
    <div class="text-center mt-3">
      <p>Não tem uma conta? <a href="<?= site_url('registrar') ?>">Crie uma agora</a></p>
    </div>


    <div class="text-center">
      <a href="#">Esqueceu sua senha?</a>
    </div>
    <?= form_close() ?>
  </div>

</body>

</html>