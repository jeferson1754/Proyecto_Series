<?php

require("../bd.php");

try {
    $db = new PDO("mysql:host={$servidor};dbname={$basededatos}", $usuario, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception) {
    die("Connection error: " . $exception->getMessage());
}

$tabla = "series";
$tabla3 = "estado";//Estado 
$tabla4 = "tachiyomi";
$tabla5 = "dias";
$tabla6 = "puntuacion";
$tabla2="estado_link";

$fila1 = "Nombre";
$fila2 = "Link";
$fila3 = "Vistos";
$fila4 = "Temporadas";
$fila6 = "Dias";
$fila8 = "Estado";
$fila7 = "ID";
$fila9 = "Calificacion";
$fila10 = "ID_Serie";
$fila11="Estado_Link";

$titulo1="Estado del Link";