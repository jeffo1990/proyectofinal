<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];

$stmt = $conn->prepare("SELECT u.id, u.username, u.email, u.foto, r.name AS rol FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = ?");
if ($stmt === false) {
    die("Error al preparar la consulta: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$resultado = $stmt->get_result();
$perfil = $resultado->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_info'])) {
        $nuevoUsername = trim($_POST['username']);
        $nuevoEmail = trim($_POST['email']);

        $fotoPerfil = $perfil['foto'];
        if (!empty($_FILES['foto']['name'])) {
            $nombreFoto = basename($_FILES['foto']['name']);
            $rutaFoto = 'uploads/' . time() . '_' . $nombreFoto;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaFoto)) {
                $fotoPerfil = $rutaFoto;
            }
        }

        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, foto = ? WHERE id = ?");
        if ($stmt === false) {
            die("Error al preparar la consulta: " . $conn->error);
        }
        $stmt->bind_param("sssi", $nuevoUsername, $nuevoEmail, $fotoPerfil, $user_id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['user']['username'] = $nuevoUsername;
        $_SESSION['user']['email'] = $nuevoEmail;

        header("Location: perfil.php?actualizado=1");
        exit();
    }

    if (isset($_POST['update_pass'])) {
        $nuevaPass = $_POST['password'];
        $confirmarPass = $_POST['confirm_password'];

        if ($nuevaPass === $confirmarPass) {
            $hash = password_hash($nuevaPass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            if ($stmt === false) {
                die("Error al preparar la consulta: " . $conn->error);
            }
            $stmt->bind_param("si", $hash, $user_id);
            $stmt->execute();
            $stmt->close();

            header("Location: perfil.php?clave=1");
            exit();
        } else {
            $error = "Las contraseñas no coinciden.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mi Perfil</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <?php include 'navbar.php'; ?>
  <?php include 'sidebar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>Mi Perfil</h1>
            <a href="dashboard.php" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
        </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <?php if (isset($_GET['actualizado'])): ?>
          <div class="alert alert-success">Información actualizada correctamente.</div>
        <?php elseif (isset($_GET['clave'])): ?>
          <div class="alert alert-success">Contraseña actualizada correctamente.</div>
        <?php elseif (!empty($error)): ?>
          <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header bg-primary text-white">Información del Perfil</div>
              <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                  <input type="hidden" name="update_info" value="1">
                  <div class="mb-3">
                    <label>Nombre de Usuario</label>
                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($perfil['username']) ?>" required>
                  </div>
                  <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($perfil['email']) ?>" required>
                  </div>
                  <div class="mb-3">
                    <label>Foto de perfil</label><br>
                    <?php if ($perfil['foto']): ?>
                      <img src="<?= $perfil['foto'] ?>" alt="Foto de perfil" class="img-thumbnail mb-2" width="100">
                    <?php endif; ?>
                    <input type="file" name="foto" class="form-control">
                  </div>
                  <div class="mb-3">
                    <label>Rol</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($perfil['rol']) ?>" readonly>
                  </div>
                  <button type="submit" class="btn btn-success">Guardar Cambios</button>
                </form>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="card">
              <div class="card-header bg-warning text-dark">Cambiar Contraseña</div>
              <div class="card-body">
                <form method="POST">
                  <input type="hidden" name="update_pass" value="1">
                  <div class="mb-3">
                    <label>Nueva Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                  </div>
                  <div class="mb-3">
                    <label>Confirmar Contraseña</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                  </div>
                  <button type="submit" class="btn btn-warning">Actualizar Contraseña</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <footer class="main-footer text-sm">
    <strong>&copy; 2025 Biblioteca Online</strong> - Todos los derechos reservados.
  </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
