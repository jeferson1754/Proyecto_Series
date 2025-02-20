<?php

$usuario  = "root";
$password = "";
$servidor = "localhost";
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
