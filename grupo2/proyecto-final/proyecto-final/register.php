<?php 
// Incluye el archivo de conexión a la base de datos
include('includes/db.php');
// Inicia o retoma la sesión para poder almacenar datos de usuario
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro de Usuario</title>
  <!-- Enlace a Bootstrap 5 para estilos prediseñados -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Enlace a la fuente 'Inter' desde Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    /* Reset de box-sizing y tipografía general */
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      /* Degradado animado de fondo */
      background: linear-gradient(135deg, #2980b9, #6dd5fa);
      overflow: hidden;
    }

    /* Animación para desplazar lentamente el fondo */
    @keyframes bgShift {
      0%   { background-position: 0% 50%; }
      50%  { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    body {
      background-size: 200% 200%;
      animation: bgShift 15s ease infinite;
    }

    /* Animación de aparición de la tarjeta */
    @keyframes cardAppear {
      0%   { opacity: 0; transform: scale(0.8) translateY(30px); }
      60%  { opacity: 1; transform: scale(1.05) translateY(-10px); }
      100% { transform: scale(1) translateY(0); }
    }
    /* Estilos de la tarjeta de registro */
    .register-card {
      background: #ffffffee;
      border: none;
      border-radius: 1rem;
      padding: 2rem;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 1rem 2rem rgba(0,0,0,0.2);
      animation: cardAppear 0.8s ease-out both;
    }

    /* Animación de rebote para el encabezado */
    @keyframes headerBounce {
      0%,100% { transform: translateY(0); }
      50%     { transform: translateY(-8px); }
    }
    .register-card h2 {
      animation: headerBounce 2s ease infinite;
    }

    /* Estilos para campos con etiqueta flotante */
    .form-floating {
      position: relative;
      margin-bottom: 1.25rem;
    }
    .form-floating input,
    .form-floating select {
      width: 100%;
      padding: 1.25rem 1rem 0.5rem;
      border: 1px solid #ccc;
      border-radius: .5rem;
      transition: border-color .3s, box-shadow .3s;
    }
    .form-floating label {
      position: absolute;
      top: 1rem; left: 1rem;
      color: #888;
      pointer-events: none;
      transition: all .2s ease;
    }
    /* Efecto de foco y ocultar etiqueta al escribir */
    .form-floating input:focus,
    .form-floating select:focus {
      border-color: #2980b9;
      box-shadow: 0 0 0 .2rem rgba(41,128,185,.25);
      outline: none;
    }
    .form-floating input:not(:placeholder-shown) + label,
    .form-floating input:focus + label,
    .form-floating select:focus + label {
      opacity: 0;
      visibility: hidden;
      transform: translateY(-1rem) scale(0.8);
    }

    /* Botón de registro con animaciones */
    .btn-register {
      position: relative;
      width: 100%;
      padding: .75rem;
      font-weight: 600;
      border-radius: .5rem;
      transition: transform .2s ease, background .3s ease;
      overflow: hidden;
    }
    .btn-register:hover {
      background: #217dbb;
      transform: scale(1.03);
    }
    .btn-register:active {
      transform: scale(0.97);
    }
    .btn-register:after {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(255,255,255,0.3);
      opacity: 0;
      transition: opacity .2s ease;
    }
    .btn-register:active:after {
      opacity: 1;
      transition: none;
    }

    /* Estilos para enlaces y textos secundarios */
    .text-link {
      font-size: .9rem;
      color: #555;
      transition: color .2s;
    }
    .text-link:hover {
      color: #000;
    }
  </style>
</head>
<body>

  <!-- Tarjeta principal de creación de cuenta -->
  <div class="register-card">
    <!-- Encabezado con título y animación -->
    <h2 class="text-center mb-4">Crear Cuenta</h2>
    <!-- Formulario de registro -->
    <form action="" method="POST" novalidate>
      <!-- Campo de usuario con etiqueta flotante -->
      <div class="form-floating">
        <input type="text" name="username" id="username" class="form-control" placeholder="nombre de usuario" required>
        <label for="username">Usuario</label>
      </div>
      <!-- Campo de correo electrónico -->
      <div class="form-floating">
        <input type="email" name="email" id="email" class="form-control" placeholder="correo@dominio.com" required>
        <label for="email">Correo electrónico</label>
      </div>
      <!-- Campo de contraseña -->
      <div class="form-floating">
        <input type="password" name="password" id="password" class="form-control" placeholder="contraseña" required>
        <label for="password">Contraseña</label>
      </div>
      <!-- Selector de rol de usuario -->
      <div class="form-floating">
        <select name="role_id" id="role_id" class="form-control" required>
          <option value="" disabled selected>Selecciona un rol</option>
          <option value="1">Administrador</option>
          <option value="2">Bibliotecario</option>
          <option value="3">Lector</option>
        </select>
        <label for="role_id">Rol de usuario</label>
      </div>
      <!-- Botón para enviar el formulario -->
      <button type="submit" name="register" class="btn btn-primary btn-register mb-3">
        Registrarse
      </button>
      <!-- Enlace a la página de inicio de sesión -->
      <div class="text-center">
        <a href="login.php" class="text-link">¿Ya tienes cuenta? Inicia sesión</a>
      </div>
    </form>

    <?php
    // Procesamiento al enviar el formulario
    if (isset($_POST['register'])) {
      // Recoge y encripta los datos ingresados
      $username = $_POST['username'];
      $email    = $_POST['email'];
      $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
      $role_id  = $_POST['role_id'];

      // Inserta el nuevo usuario en la tabla 'users'
      $sql = "INSERT INTO users (username, email, password, role_id) 
              VALUES ('$username','$email','$password','$role_id')";
      if ($conn->query($sql) === TRUE) {
        // Muestra mensaje de éxito
        echo "<div class='alert alert-success mt-3'>✅ Usuario registrado con éxito.</div>";
      } else {
        // Muestra error si falla la inserción
        echo "<div class='alert alert-danger mt-3'>⚠️ Error: " . $conn->error . "</div>";
      }
    }
    ?>
  </div>

  <!-- Inclusión del bundle de JavaScript de Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
