<?php
ob_start(); // Inicia buffer de salida

// Incluye la conexión a la base de datos
include('includes/db.php');
// Inicia o retoma la sesión para manejar datos de usuario
session_start();

$error = '';

// Procesamiento del formulario de login antes de generar HTML
if (isset($_POST['login'])) {
  // Recoge los datos enviados
  $email    = $_POST['email'];
  $password = $_POST['password'];

  // Consulta a la BD para buscar un usuario con ese correo
  $sql    = "SELECT * FROM users WHERE email = '$email'";
  $result = $conn->query($sql);

  if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();
    // Verifica la contraseña
    if (password_verify($password, $user['password'])) {
      // Almacena datos del usuario en la sesión
      $_SESSION['user']     = $user;
      $_SESSION['user_id']  = $user['id'];
      $_SESSION['username'] = $user['username'];
      $_SESSION['role_id']  = $user['role_id'];
      header("Location: dashboard.php");
      exit;
    } else {
      $error = "✔️ Contraseña incorrecta";
    }
  } else {
    $error = "❌ Correo no registrado";
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar Sesión</title>
  <!-- Enlace a Bootstrap 5 para estilos -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Enlace a Google Fonts (Inter) -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    /* Cuerpo de la página */
    body {
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #4e54c8, #8f94fb);
      overflow: hidden;
    }
    @keyframes cardPop {
      0%   { opacity: 0; transform: scale(0.8) translateY(20px); }
      60%  { opacity: 1; transform: scale(1.05) translateY(-10px); }
      100% { transform: scale(1) translateY(0); }
    }
    .login-card {
      animation: cardPop 0.8s ease-out both;
      border: none;
      border-radius: 1rem;
      box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.2);
      max-width: 400px;
      width: 100%;
      padding: 2rem;
    }
    .form-floating { position: relative; }
    .form-floating input {
      padding: 1.5rem 1rem .5rem;
    }
    .form-floating label {
      position: absolute;
      top: 1rem; left: 1rem;
      transition: all .2s ease-in-out;
      opacity: 1;
      pointer-events: none;
      color: #6c757d;
    }
    .form-floating input:not(:placeholder-shown) + label,
    .form-floating input:focus + label {
      opacity: 0;
      visibility: hidden;
      transform: translateY(-1rem) scale(0.8);
    }
    .form-control {
      transition: box-shadow .2s, border-color .2s;
    }
    .form-control:focus {
      border-color: #4e54c8;
      box-shadow: 0 0 0 .2rem rgba(78,84,200,.25);
    }
    .btn-primary {
      position: relative;
      overflow: hidden;
      transition: background .3s ease, transform .1s ease;
    }
    .btn-primary:hover { background: #3b3fc1; transform: scale(1.02); }
    .btn-primary:active { transform: scale(0.98); }
    .btn-primary:after {
      content: "";
      position: absolute;
      width: 100%; height: 100%;
      top: 0; left: 0;
      background: rgba(255,255,255,0.2);
      opacity: 0;
      transition: opacity .3s ease;
    }
    .btn-primary:active:after { opacity: 1; transition: none; }
    .register-link { font-size: 0.9rem; }
  </style>
</head>
<body>

  <div class="card login-card">
    <div class="text-center mb-4">
      <h3 class="fw-bold text-black">Bienvenido</h3>
      <p class="text-black-50">Ingresa tus credenciales</p>
    </div>

    <form action="" method="POST" novalidate>
      <div class="form-floating mb-3">
        <input 
          type="email" 
          name="email" 
          class="form-control" 
          id="floatingEmail" 
          placeholder="correo@ejemplo.com" 
          required>
        <label for="floatingEmail">Correo electrónico</label>
      </div>
      <div class="form-floating mb-4">
        <input 
          type="password" 
          name="password" 
          class="form-control" 
          id="floatingPassword" 
          placeholder="••••••••" 
          required>
        <label for="floatingPassword">Contraseña</label>
      </div>
      <button 
        type="submit" 
        name="login" 
        class="btn btn-primary w-100 mb-3 py-2">
        Iniciar Sesión
      </button>
      <div class="text-center register-link">
        <a href="register.php" class="text-black-50">¿No tienes cuenta? Regístrate aquí</a>
      </div>
    </form>

    <?php if ($error): ?>
      <div class="alert alert-danger mt-3"><?= $error ?></div>
    <?php endif; ?>
  </div>

  <!-- Script de Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php ob_end_flush(); // Envía el buffer al navegador ?>
