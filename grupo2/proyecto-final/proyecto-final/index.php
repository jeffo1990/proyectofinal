<!-- index.php -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <!-- Meta para adaptar el viewport en dispositivos móviles -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Biblioteca Online</title>
  <!-- Enlace al CSS de Bootstrap 5 desde CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Enlace a la fuente 'Inter' de Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    /* Aplica box-sizing a todos los elementos para incluir padding y borde en el ancho total */
    * { box-sizing: border-box; }
    /* Estilos del body:
       - Sin margen
       - Fuente 'Inter'
       - Altura completa de la ventana
       - Centrado de contenido con Flexbox
       - Degradado de fondo animado */
    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #4facfe, #00f2fe);
      background-size: 200% 200%;
      animation: bgShift 15s ease infinite;
    }

    /* Animación para desplazar el fondo */
    @keyframes bgShift {
      0%   { background-position: 0% 50%; }
      50%  { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    /* Animación de aparición de la tarjeta */
    @keyframes cardAppear {
      0%   { opacity: 0; transform: scale(0.8) translateY(30px); }
      60%  { opacity: 1; transform: scale(1.05) translateY(-10px); }
      100% { transform: scale(1) translateY(0); }
    }

    /* Animación de rebote para el encabezado */
    @keyframes headerBounce {
      0%,100% { transform: translateY(0); }
      50%     { transform: translateY(-8px); }
    }

    /* Estilos de la tarjeta de bienvenida:
       - Fondo semitransparente
       - Bordes redondeados
       - Sombra
       - Ancho máximo
       - Animación de aparición */
    .welcome-card {
      background:rgba(253, 253, 255, 0.93);
      border-radius: 1rem;
      padding: 2.5rem 2rem;
      box-shadow: 0 1rem 2rem rgba(0,0,0,0.2);
      text-align: center;
      animation: cardAppear 0.8s ease-out both;
      max-width: 500px;
      width: 100%;
    }

    /* Encabezado dentro de la tarjeta:
       - Fuente más gruesa
       - Animación de rebote continua
       - Espacio inferior */
    .welcome-card h1 {
      font-weight: 600;
      animation: headerBounce 2s ease infinite;
      margin-bottom: 1rem;
    }

    /* Botones grandes (btn-lg):
       - Padding aumentado
       - Fuente más gruesa
       - Bordes redondeados
       - Transiciones al hover/active */
    .btn-lg {
      padding: 0.75rem 1.25rem;
      font-weight: 600;
      border-radius: 0.5rem;
      transition: transform 0.2s ease, background 0.3s ease;
    }
    .btn-lg:hover {
      transform: scale(1.03);
    }
    .btn-lg:active {
      transform: scale(0.97);
    }

    /* Estilos del botón primario */
    .btn-primary {
      background: #007bff;
      border: none;
    }
    .btn-primary:hover {
      background: #0069d9;
    }

    /* Hover para botones outline */
    .btn-outline-secondary:hover {
      background-color: #f1f1f1;
      color: #333;
    }
  </style>
</head>
<body>

  <!-- Tarjeta de bienvenida centrada -->
  <div class="welcome-card">
    <!-- Título con emoji -->
    <h1>📚 Bienvenido a la Biblioteca Online</h1>
    <p class="mb-4">¿Qué deseas hacer?</p>
    <!-- Botones de acción en columna con espacio entre ellos -->
    <div class="d-grid gap-3">
      <!-- Enlace a registro -->
      <a href="register.php" class="btn btn-primary btn-lg">Registrarse</a>
      <!-- Enlace a inicio de sesión -->
      <a href="login.php" class="btn btn-outline-secondary btn-lg">Iniciar Sesión</a>
    </div>
  </div>

  <!-- Inclusión del bundle de JavaScript de Bootstrap (incluye Popper) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
