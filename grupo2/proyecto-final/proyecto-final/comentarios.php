<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

require_once 'includes/db.php'; 

$user = $_SESSION['user'];
$roleName = $user['role_id'] == 1 ? 'Administrador' : ($user['role_id'] == 2 ? 'Bibliotecario' : 'Lector');

// Insertar comentario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['comentario'])) {
    $comentario = trim($_POST['comentario']);

    // Preparar e insertar usando mysqli correctamente
    $stmt = $conn->prepare("INSERT INTO comentarios (usuario_id, comentario) VALUES (?, ?)");
    if ($stmt) {
        $stmt->bind_param("is", $user['id'], $comentario); // i = integer, s = string
        $stmt->execute();
        $stmt->close();
    } else {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Comentarios</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- Navbar -->
  <?php include 'navbar.php'; ?>

  <!-- Sidebar -->
  <?php include 'sidebar.php'; ?>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <h1>Comentarios</h1>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <!-- Botón de regreso -->
        <div class="text-end mb-3">
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Regresar al Dashboard
            </a>
        </div>

        <!-- Formulario de comentario -->
        <form method="POST" class="mb-4">
          <div class="form-group">
            <label>Escribe tu comentario</label>
            <textarea name="comentario" class="form-control" rows="3" required></textarea>
          </div>
          <button type="submit" class="btn btn-primary mt-2">Enviar</button>
        </form>

        <!-- Lista de comentarios -->
        <div class="card">
          <div class="card-header bg-primary text-white">
            Comentarios recientes
          </div>
          <div class="card-body">
            <?php
            $sql = "SELECT c.comentario, c.fecha, u.username 
                    FROM comentarios c 
                    JOIN users u ON c.usuario_id = u.id 
                    ORDER BY c.fecha DESC";
            $result = $conn->query($sql);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <div class="border-bottom mb-3 pb-2">
                        <strong><?= htmlspecialchars($row['username']) ?></strong>
                        <span class="text-muted float-end"><?= $row['fecha'] ?></span>
                        <p class="mt-1"><?= nl2br(htmlspecialchars($row['comentario'])) ?></p>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No se pudieron cargar los comentarios: " . $conn->error . "</p>";
            }
            ?>
          </div>
        </div>

      </div>
    </section>
  </div>

  <!-- Footer -->
  <footer class="main-footer text-sm">
    <div class="float-right d-none d-sm-block">
      <b>Versión</b> 1.0.0
    </div>
    <strong>&copy; 2025 <a href="#">Mi Biblioteca</a>.</strong> Todos los derechos reservados.
  </footer>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
