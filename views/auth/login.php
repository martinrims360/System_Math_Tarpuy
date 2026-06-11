<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar sesión — SistemaMat</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/dist/tabler-icons.min.css" rel="stylesheet">
  <style>
    :root {
      --primary:   #534AB7;
      --primary-d: #3C3489;
      --primary-l: #EEEDFE;
    }
    *, *::before, *::after { box-sizing: border-box; }
    body {
      margin: 0;
      min-height: 100vh;
      background: #F0F0FB;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Inter', system-ui, sans-serif;
      padding: 20px;
    }
    .login-card {
      background: #fff;
      border-radius: 18px;
      padding: 40px 40px 36px;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 4px 32px rgba(83,74,183,.10);
    }
    .login-brand {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 28px;
    }
    .login-brand .icon {
      width: 40px; height: 40px;
      background: var(--primary);
      border-radius: 10px;
      display: grid; place-items: center;
      color: #fff; font-size: 20px;
    }
    .login-brand h1 {
      font-size: 17px;
      font-weight: 700;
      color: #1E1B4B;
      margin: 0;
      line-height: 1.2;
    }
    .login-brand p {
      font-size: 12px;
      color: #888;
      margin: 0;
    }
    .form-label {
      font-size: 12.5px;
      font-weight: 500;
      color: #555;
      margin-bottom: 5px;
    }
    .form-control {
      font-size: 14px;
      border: 1.5px solid #E2E0F8;
      border-radius: 10px;
      padding: 9px 12px;
      transition: border-color .15s, box-shadow .15s;
    }
    .form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(83,74,183,.12);
    }
    .btn-login {
      width: 100%;
      padding: 10px;
      background: var(--primary);
      color: #fff;
      border: none;
      border-radius: 10px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      margin-top: 8px;
      transition: background .15s;
    }
    .btn-login:hover { background: var(--primary-d); }
    .alert-err {
      background: #FAECE7;
      border-left: 4px solid #C0392B;
      color: #712B13;
      padding: 10px 14px;
      border-radius: 8px;
      font-size: 13px;
      margin-bottom: 18px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .mb-3 { margin-bottom: 14px; }
    .input-group-text {
      background: #F8F7FE;
      border: 1.5px solid #E2E0F8;
      border-right: none;
      color: var(--primary);
    }
    .input-group .form-control { border-left: none; }
  </style>
</head>
<body>

<div class="login-card">
  <div class="login-brand">
    <div class="icon"><i class="ti ti-math"></i></div>
    <div>
      <h1>SistemaMat</h1>
      <p>Control de temas para concursos matemáticos</p>
    </div>
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert-err">
      <i class="ti ti-circle-x"></i>
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <form method="POST" action="?page=login">
    <div class="mb-3">
      <label class="form-label">Correo institucional</label>
      <div class="input-group">
        <span class="input-group-text"><i class="ti ti-mail"></i></span>
        <input type="email" name="correo" class="form-control"
               placeholder="correo@colegio.edu.pe"
               value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>"
               required autofocus>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Contraseña</label>
      <div class="input-group">
        <span class="input-group-text"><i class="ti ti-lock"></i></span>
        <input type="password" name="password" class="form-control"
               placeholder="••••••••" required>
      </div>
    </div>

    <button type="submit" class="btn-login">
      <i class="ti ti-login" style="vertical-align:-2px;margin-right:5px"></i>
      Ingresar al sistema
    </button>
  </form>
</div>

</body>
</html>