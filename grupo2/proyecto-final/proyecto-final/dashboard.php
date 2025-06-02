<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}
$user = $_SESSION['user'];
$roleName = $user['role_id'] == 1 ? 'Administrador' : ($user['role_id'] == 2 ? 'Bibliotecario' : 'Lector');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Administración</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="dashboard.php" class="nav-link">Inicio</a>
      </li>
    </ul>
    <ul class="navbar-nav ms-auto">
      <li class="nav-item">
        <span class="nav-link">Hola, <strong><?= htmlspecialchars($user['username']); ?></strong> (<?= $roleName ?>)</span>
      </li>
      <li class="nav-item">
        <a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
      </li>
    </ul>
  </nav>

  <!-- Sidebar -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="dashboard.php" class="brand-link">
      <i class="fas fa-book-reader brand-image img-circle elevation-3"></i>
      <span class="brand-text font-weight-light">Biblioteca</span>
    </a>

    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="info">
          <a href="#" class="d-block"><?= htmlspecialchars($user['username']); ?></a>
          <span class="text-muted small"><?= $roleName ?></span>
        </div>
      </div>

      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <li class="nav-item">
            <a href="dashboard.php" class="nav-link<?= basename($_SERVER['PHP_SELF'])=='dashboard.php'?' active':''?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>

          <?php if ($user['role_id'] == 1): ?>
            <li class="nav-item">
              <a href="includes/usuarios.php" class="nav-link<?= basename($_SERVER['PHP_SELF'])=='usuarios.php'?' active':''?>">
                <i class="nav-icon fas fa-users"></i>
                <p>Usuarios</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="includes/libros.php" class="nav-link<?= basename($_SERVER['PHP_SELF'])=='libros.php'?' active':''?>">
                <i class="nav-icon fas fa-book"></i>
                <p>Libros</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="includes/catalogo.php" class="nav-link<?= basename($_SERVER['PHP_SELF'])=='catalogo.php'?' active':''?>">
                <i class="nav-icon fas fa-book-open"></i>
                <p>Catálogo</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="comentarios.php" class="nav-link<?= basename($_SERVER['PHP_SELF'])=='comentarios.php'?' active':''?>">
                <i class="nav-icon fas fa-envelope"></i>
                <p>Comentarios</p>
              </a>
            </li>
          <?php elseif ($user['role_id'] == 2): ?>
            <li class="nav-item">
              <a href="includes/libros.php" class="nav-link<?= basename($_SERVER['PHP_SELF'])=='libros.php'?' active':''?>">
                <i class="nav-icon fas fa-book"></i>
                <p>Libros</p>
              </a>
            </li>
          <?php elseif ($user['role_id'] == 3): ?>
            <li class="nav-item">
              <a href="includes/catalogo.php" class="nav-link<?= basename($_SERVER['PHP_SELF'])=='catalogo.php'?' active':''?>">
                <i class="nav-icon fas fa-book-open"></i>
                <p>Catálogo</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="comentarios.php" class="nav-link<?= basename($_SERVER['PHP_SELF'])=='comentarios.php'?' active':''?>">
                <i class="nav-icon fas fa-envelope"></i>
                <p>Comentarios</p>
              </a>
            </li>
          <?php endif; ?>

          <li class="nav-item has-treeview<?= in_array(basename($_SERVER['PHP_SELF']),['perfil.php'])?' menu-open':''?>">
            <a href="#" class="nav-link<?= in_array(basename($_SERVER['PHP_SELF']),['perfil.php'])?' active':''?>">
              <i class="nav-icon fas fa-cog"></i>
              <p>Configuración <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="perfil.php" class="nav-link<?= basename($_SERVER['PHP_SELF'])=='perfil.php'?' active':''?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Perfil</p>
                </a>
              </li>
            </ul>
          </li>

        </ul>
      </nav>
    </div>
  </aside>

  <!-- Contenido principal -->
  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2"><div class="col-sm-6"><h1>Dashboard</h1></div></div>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="row">

          <?php if ($user['role_id'] == 1): ?>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-info">
                <div class="inner"><h3>150</h3><p>Usuarios</p></div>
                <div class="icon"><i class="fas fa-users"></i></div>
                <a href="includes/usuarios.php" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
          <?php endif; ?>

          <?php if ($user['role_id'] == 1 || $user['role_id'] == 2): ?>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-success">
                <div class="inner"><h3>53<sup style="font-size:20px">%</sup></h3><p>Libros Prestados</p></div>
                <div class="icon"><i class="fas fa-book"></i></div>
                <a href="includes/libros.php" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
          <?php endif; ?>

          <?php if ($user['role_id'] == 1 || $user['role_id'] == 3): ?>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-warning">
                <div class="inner"><h3>44</h3><p>Catálogo</p></div>
                <div class="icon"><i class="fas fa-book-open"></i></div>
                <a href="includes/catalogo.php" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-danger">
                <div class="inner"><h3>65</h3><p>Comentarios</p></div>
                <div class="icon"><i class="fas fa-envelope"></i></div>
                <a href="comentarios.php" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
          <?php endif; ?>

        </div>
      </div>
    </section>
  </div>

  <footer class="main-footer text-sm">
    <div class="float-right d-none d-sm-block"><b>Versión</b> 1.0.0</div>
    <strong>&copy; 2025 <a href="#">Mi Biblioteca</a>.</strong> Todos los derechos reservados.
  </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

</body>
</html>
