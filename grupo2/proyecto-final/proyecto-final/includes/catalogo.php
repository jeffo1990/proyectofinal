<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.php");
  exit();
}
$username = htmlspecialchars($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Catálogo de Libros</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body { background: #f0f2f5; font-family: 'Inter', sans-serif; }
    .header { display: flex; justify-content: space-between; align-items: center; margin: 2rem 0; }
    .header h3 { animation: bounceIn 1s; }
    .btn-animate { transition: transform .2s; }
    .btn-animate:hover { transform: scale(1.05); }
    table tbody tr { animation: fadeInUp 0.6s both; }
    table tbody tr:hover { background: #e9f5ff; transform: scale(1.02); transition: all .2s; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity:1; transform: translateY(0); } }
  </style>
</head>
<body class="container">
  <div class="header">
    <h3 class="text-primary animate__animated animate__bounceIn">
      <i class="bi bi-journal-bookmark-fill"></i> Bienvenido, <?= $username ?>
    </h3>
    
      <a href="../dashboard.php" class="btn btn-outline-danger animate__animated animate__pulse"><i class="bi bi-box-arrow-right"></i> Regresar</a>
    </a>
  </div>
  <h2 class="mb-4 animate__animated animate__fadeInUp"><i class="bi bi-book-half"></i> Catálogo de Libros</h2>
  <div class="table-responsive animate__animated animate__fadeInUp">
    <table class="table table-hover align-middle">
      <thead class="table-dark">
        <tr>
          <th>Título</th>
          <th>Autor</th>
          <th>Año</th>
          <th>Género</th>
          <th>Disponibles</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $result = $conn->query("SELECT * FROM books");
        while ($row = $result->fetch_assoc()):
        ?>
          <tr>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['author']) ?></td>
            <td><?= $row['year'] ?></td>
            <td><?= htmlspecialchars($row['genre']) ?></td>
            <td class="text-center"><?= $row['quantity'] ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
