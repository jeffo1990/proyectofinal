<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
$username = htmlspecialchars($_SESSION['user']['username']);

// Load roles
$roles = [];
$roles_res = $conn->query("SELECT id,name FROM roles");
while ($r = $roles_res->fetch_assoc()) {
    $roles[$r['id']] = $r['name'];
}

// Handle add/update/delete
if (isset($_POST['save_user'])) {
    $id = $_POST['id'] ?: null;
    $uname = $_POST['username'];
    $email = $_POST['email'];
    $role_id = $_POST['role_id'];
    if ($_POST['password']) {
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }
    if ($id) {
        if (isset($pass)) {
            $stmt = $conn->prepare("UPDATE users SET username=?,email=?,role_id=?,password=? WHERE id=?");
            $stmt->bind_param('ssisi', $uname, $email, $role_id, $pass, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username=?,email=?,role_id=? WHERE id=?");
            $stmt->bind_param('ssii', $uname, $email, $role_id, $id);
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username,email,role_id,password) VALUES(?,?,?,?)");
        $stmt->bind_param('ssis', $uname, $email, $role_id, $pass);
    }
    $stmt->execute();
    header('Location: usuarios.php'); exit;
}
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM users WHERE id=$id");
    header('Location: usuarios.php'); exit;
}

// Fetch users
$users = $conn->query(
    "SELECT u.id,u.username,u.email,r.name AS role_name,u.role_id FROM users u JOIN roles r ON u.role_id=r.id"
)->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Gestión de Usuarios</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
  <style>
    body { background: #f0f2f5; font-family: 'Inter', sans-serif; }
    .header { display:flex; justify-content:space-between; align-items:center; margin:2rem 0; }
    .table tbody tr { animation: fadeInUp 0.6s both; }
    @keyframes fadeInUp { from{opacity:0;transform:translateY(20px);} to{opacity:1;transform:translateY(0);} }
  </style>
</head>
<body class="container">
  <div class="header">
    <h3 class="text-primary animate__animated animate__bounceIn"><i class="bi bi-people-fill"></i> Hola, <?= htmlspecialchars($username) ?></h3>
    <div>
      <button class="btn btn-primary animate__animated animate__pulse me-2" data-bs-toggle="modal" data-bs-target="#userModal">
        <i class="bi bi-person-plus-fill"></i> Agregar Usuario
      </button>
      
        <a href="../dashboard.php" class="btn btn-outline-danger animate__animated animate__pulse"><i class="bi bi-box-arrow-right"></i> Regresar</a>
      </a>
    </div>
  </div>
  <div class="table-responsive animate__animated animate__fadeInUp">
    <table class="table table-hover align-middle">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>Usuario</th>
          <th>Email</th>
          <th>Rol</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $i => $u): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= htmlspecialchars($u['username']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= htmlspecialchars($u['role_name']) ?></td>
          <td>
            <button class="btn btn-sm btn-warning me-1 animate__animated animate__pulse btnEdit" 
              data-id="<?= $u['id'] ?>"
              data-username="<?= htmlspecialchars($u['username']) ?>"
              data-email="<?= htmlspecialchars($u['email']) ?>"
              data-role="<?= $u['role_id'] ?>"
              data-bs-toggle="modal" data-bs-target="#userModal">
              <i class="bi bi-pencil-fill"></i>
            </button>
            <a href="?delete=<?= $u['id'] ?>" class="btn btn-sm btn-danger animate__animated animate__pulse" onclick="return confirm('¿Eliminar usuario?');">
              <i class="bi bi-trash-fill"></i>
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- User Modal -->
  <div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered animate__animated animate__zoomIn">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="userModalLabel"><i class="bi bi-person-plus-fill"></i> Agregar Usuario</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST" action="usuarios.php">
          <div class="modal-body">
            <input type="hidden" name="id" id="userId">
            <div class="mb-3">
              <label class="form-label">Usuario</label>
              <input type="text" name="username" id="userUsername" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" id="userEmail" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Rol</label>
              <select name="role_id" id="userRole" class="form-select" required>
                <option value="">-- Selecciona rol --</option>
                <?php foreach ($roles as $rid => $rname): ?>
                  <option value="<?= $rid ?>"><?= htmlspecialchars($rname) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Contraseña</label>
              <input type="password" name="password" id="userPassword" class="form-control" placeholder="Dejar vacío no cambia" >
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" name="save_user" class="btn btn-primary" id="userSubmitBtn">
              <i class="bi bi-save"></i> Guardar
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.querySelectorAll('.btnEdit').forEach(btn => btn.addEventListener('click', () => {
      document.getElementById('userModalLabel').innerHTML = '<i class="bi bi-pencil-fill"></i> Editar Usuario';
      document.getElementById('userSubmitBtn').innerHTML = '<i class="bi bi-pencil-fill"></i> Actualizar';
      document.getElementById('userId').value = btn.dataset.id;
      document.getElementById('userUsername').value = btn.dataset.username;
      document.getElementById('userEmail').value = btn.dataset.email;
      document.getElementById('userRole').value = btn.dataset.role;
      document.getElementById('userPassword').value = '';
    }));
    document.getElementById('userModal').addEventListener('show.bs.modal', e => {
      if (!e.relatedTarget.classList.contains('btnEdit')) {
        document.getElementById('userModalLabel').innerHTML = '<i class="bi bi-person-plus-fill"></i> Agregar Usuario';
        document.getElementById('userSubmitBtn').innerHTML = '<i class="bi bi-save"></i> Guardar';
        ['userId','userUsername','userEmail','userRole','userPassword'].forEach(id => document.getElementById(id).value = '');
      }
    });
  </script>
</body>
</html>