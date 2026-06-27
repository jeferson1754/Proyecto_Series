<?php
// 1. Incluir la conexión a la base de datos
include('bd.php');
// NOTA: Si usas PDO para el `$connect` del historial, asegúrate de iniciar esa conexión aquí también.
// Si no la tienes en bd.php, puedes descomentar y ajustar la siguiente línea:
// $connect = new PDO("mysql:host=localhost;dbname=TU_BASE_DATOS", "USUARIO", "CONTRASEÑA");

// 2. Validar que lleguen los parámetros necesarios
if (!isset($_GET['id']) || !isset($_GET['action']) || $_GET['action'] !== 'completar') {
    header("Location: index.php?error=parametros_invalidos");
    exit;
}

$id_serie = intval($_GET['id']);

// 3. Obtener los datos actuales de la serie antes de actualizar
$query_select = "SELECT Nombre, Temporadas, Temp_Totales FROM `series` WHERE id = $id_serie LIMIT 1";
// Cambia 'id' por tu columna de ID real (ej: $fila7 si es el nombre de la columna)
$result_select = mysqli_query($conexion, $query_select);
$serie = mysqli_fetch_assoc($result_select);

if (!$serie) {
    header("Location: index.php?error=serie_no_encontrada");
    exit;
}


// 1. DETERMINAR LA CONSULTA DE ACTUALIZACIÓN SEGÚN LAS TEMPORADAS
if ($serie['Temporadas'] < $serie['Temp_Totales']) {
    // Si quedan temporadas: avanzar a la siguiente, resetear capítulos vistos y disponibles
    $query_update = "UPDATE `series` 
                     SET Temporadas = Temporadas + 1, 
                         Vistos = 0,
                         Caps_Disponibles = 0
                     WHERE id = $id_serie";
} else {
    // Si era la ÚLTIMA temporada: marcar la serie como 'Terminado' y poner fecha_fin
    $query_update = "UPDATE `series` 
                     SET Estado = 'Finalizado',
                     Fecha_Fin = $fecha_actual
                     WHERE id = $id_serie";
}

// 2. EJECUTAR LA CONSULTA (Ahora aseguramos que siempre exista $query_update)
if (mysqli_query($conexion, $query_update)) {

    // 3. CALCULAR LOS NUEVOS BLOQUES DE SERIES PENDIENTES
    $sql = "SELECT 
                CEIL(
                    SUM(
                        CASE 
                            WHEN (Total - Vistos) > 0 
                            THEN (Total - Vistos) / 6
                            ELSE 0
                        END
                    )
                ) AS bloques_series
            FROM series
            WHERE Estado IN ('Pendiente', 'Viendo')";

    $result = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_row($result);
    $total_actual = (int) $fila[0];

    // 4. CONSULTAR EL ÚLTIMO VALOR EN EL HISTORIAL
    $stmt_check = $connect->prepare("
        SELECT total_anterior 
        FROM estadisticas_historial 
        WHERE categoria = 'Series' 
        ORDER BY fecha_actualizacion DESC LIMIT 1
    ");
    $stmt_check->execute();
    $ultimo_registro = $stmt_check->fetchColumn();

    // 5. INSERTAR EN EL HISTORIAL SOLO SI HUBO CAMBIOS REALES
    if ($ultimo_registro === false || $total_actual != $ultimo_registro) {
        $stmt = $connect->prepare("
            INSERT INTO estadisticas_historial (categoria, total_anterior, fecha_actualizacion)
            VALUES ('Series', ?, NOW())
        ");
        $stmt->execute([$total_actual]);
    }

    // Determinar el mensaje de estado para la redirección
    $status = ($serie['Temporadas'] < $serie['Temp_Totales']) ? 'temporada_completada' : 'serie_terminada';

    // 6. REDIRECCIONAR AL DASHBOARD
    header("Location: index.php?status=" . $status . "&serie=" . urlencode($serie['Nombre']));
    exit;
} else {
    // Si falla la actualización en la base de datos
    header("Location: index.php?error=error_al_actualizar");
    exit;
}
