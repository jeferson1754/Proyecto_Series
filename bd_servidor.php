<?php

$usuario  = "epiz_32740026";
$password = "eJWcVk2au5gqD";
$servidor = "sql208.epizy.com";
$basededatos = "epiz_32740026_r_user";
$conexion = mysqli_connect($servidor, $usuario, $password) or die("No se ha podido conectar al Servidor");
mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");
$db = mysqli_select_db($conexion, $basededatos) or die("Upps! Error en conectar a la Base de Datos");

try {
    $connect = new PDO("mysql:host=$servidor;dbname=$basededatos", $usuario, $password);
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}


//Linea para los caracteres �

if (!mysqli_set_charset($conexion, "utf8mb4")) {
    printf("Error loading character set utf8mb4: %s\n", mysqli_error($conexion));
    exit();
}

if (mysqli_connect_errno()) {
    die("No se pudo conectar a la base de datos: " . mysqli_connect_error());
}


$max_queries_per_hour = 700;

$current_time = date("Y-m-d H:i:s", time());

// Consultamos el número de consultas realizadas en la última hora
$query = "SELECT COUNT(*) AS num_queries FROM consultas WHERE fecha > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
$result = mysqli_query($conexion, $query);

// Si la consulta falla, lanzamos un error
if (!$result) {
    die("La consulta falló: " . mysqli_error($conexion));
}

$row = mysqli_fetch_assoc($result);
$num_queries_last_hour = $row["num_queries"];

// Liberamos el resultado de la consulta
mysqli_free_result($result);

// Si se han superado las consultas permitidas, lanzamos un error
if ($num_queries_last_hour >= $max_queries_per_hour) {
    mysqli_close($conexion); // Cerramos la conexión a la base de datos
    die("Lo siento, has superado el límite de consultas por hora.");
}

$query = "INSERT INTO consultas (fecha) VALUES ('$current_time')";
$result = mysqli_query($conexion, $query);

if (!$result) {
    die("La consulta falló: " . mysqli_error($conexion));
}

try {
    $db = new PDO("mysql:host={$servidor};dbname={$basededatos}", $usuario, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception) {
    die("Connection error: " . $exception->getMessage());
}


// Establecer la zona horaria para Santiago de Chile.
date_default_timezone_set('America/Santiago');

$tabla = "series";
$tabla3 = "estado"; //Estado 
$tabla4 = "tachiyomi";
$tabla5 = "dias";
$tabla6 = "puntuacion";
$tabla2 = "estado_link";

$fila1 = "Nombre";
$fila2 = "Link";
$fila3 = "Vistos";
$fila4 = "Temporadas";
$fila6 = "Dias";
$fila8 = "Estado";
$fila7 = "ID";
$fila9 = "Calificacion";
$fila10 = "ID_Serie";
$fila11 = "Estado_Link";

$titulo1 = "Estado del Link";
