<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? 'SistemaMat' ?> — Concursos Matemáticos</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Tabler Icons -->
  <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/dist/tabler-icons.min.css" rel="stylesheet">
  <!-- Estilos propios -->
  <link href="assets/css/main.css" rel="stylesheet">
</head>
<body>

<!-- SIDEBAR -->
<nav class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <span class="brand-icon"><i class="ti ti-math"></i></span>
    <span class="brand-text">SistemaMat</span>
  </div>

  <ul class="sidebar-nav">
    <li class="nav-section">Principal</li>

    <li class="nav-item <?= ($activePage ?? '') === 'dashboard'   ? 'active' : '' ?>">
      <a href="index.php?page=dashboard">
        <i class="ti ti-home"></i><span>Dashboard</span>
      </a>
    </li>

    <li class="nav-item <?= ($activePage ?? '') === 'temas'       ? 'active' : '' ?>">
      <a href="index.php?page=temas">
        <i class="ti ti-book"></i><span>Registrar tema</span>
      </a>
    </li>

    <li class="nav-item <?= ($activePage ?? '') === 'horarios'    ? 'active' : '' ?>">
      <a href="index.php?page=horarios">
        <i class="ti ti-calendar"></i><span>Horarios</span>
      </a>
    </li>

    <li class="nav-item <?= ($activePage ?? '') === 'seguimiento' ? 'active' : '' ?>">
      <a href="index.php?page=seguimiento">
        <i class="ti ti-chart-bar"></i><span>Seguimiento</span>
      </a>
    </li>

    <?php if (Auth::isCoord()): ?>
    <li class="nav-section">Administración</li>

    <li class="nav-item <?= ($activePage ?? '') === 'docentes'    ? 'active' : '' ?>">
      <a href="index.php?page=docentes">
        <i class="ti ti-users"></i><span>Docentes</span>
      </a>
    </li>

    <li class="nav-item <?= ($activePage ?? '') === 'grupos'      ? 'active' : '' ?>">
      <a href="index.php?page=grupos">
        <i class="ti ti-users-group"></i><span>Grupos</span>
      </a>
    </li>

    <li class="nav-item <?= ($activePage ?? '') === 'areas'       ? 'active' : '' ?>">
      <a href="index.php?page=areas">
        <i class="ti ti-tag"></i><span>Áreas temáticas</span>
      </a>
    </li>

    <li class="nav-item <?= ($activePage ?? '') === 'salones'     ? 'active' : '' ?>">
      <a href="index.php?page=salones">
        <i class="ti ti-door"></i><span>Salones</span>
      </a>
    </li>
    <?php endif; ?>
  </ul>

  <div class="sidebar-footer">
    <div class="user-info">
      <div class="user-avatar"><?= strtoupper(substr(Auth::user()['nombre'], 0, 2)) ?></div>
      <div class="user-meta">
        <span class="user-name"><?= htmlspecialchars(Auth::user()['nombre']) ?></span>
        <span class="user-role"><?= Auth::user()['rol'] === 'coordinador' ? 'Coordinador' : 'Docente' ?></span>
      </div>
    </div>
    <a href="index.php?page=logout" class="btn-logout" title="Cerrar sesión">
      <i class="ti ti-logout"></i>
    </a>
  </div>
</nav>

<!-- CONTENIDO PRINCIPAL -->
<div class="main-wrapper">

  <!-- Topbar -->
  <header class="topbar">
    <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Abrir menú">
      <i class="ti ti-menu-2"></i>
    </button>
    <div class="topbar-title"><?= $pageTitle ?? '' ?></div>
    <div class="topbar-right">
      <span class="badge-role <?= Auth::isCoord() ? 'coord' : 'doc' ?>">
        <?= Auth::isCoord() ? 'Coordinador' : 'Docente' ?>
      </span>
    </div>
  </header>

  <!-- Flash messages -->
  <?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="alert-flash success">
      <i class="ti ti-circle-check"></i>
      <?= htmlspecialchars($_SESSION['flash_success']) ?>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
  <?php endif; ?>

  <?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="alert-flash error">
      <i class="ti ti-circle-x"></i>
      <?= htmlspecialchars($_SESSION['flash_error']) ?>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
  <?php endif; ?>

  <main class="content">