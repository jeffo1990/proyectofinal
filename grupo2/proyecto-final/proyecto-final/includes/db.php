<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "biblioteca_online";
#conexion de la base de datos
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Conexión fallida: " . $conn->connect_error);
}
?>
