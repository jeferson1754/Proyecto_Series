<!--coment-->
<header>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</header>

<?php
include 'bd.php';

function alerta($alertTitle, $alertText, $alertType, $redireccion)
{

    echo '
 <script>
        Swal.fire({
            title: "' . $alertTitle . '",
            text: "' . $alertText . '",
            html: "' . $alertText . '",
            icon: "' . $alertType . '",
            showCancelButton: false,
            confirmButtonText: "OK",
            closeOnConfirm: false
        }).then(function() {
          ' . $redireccion . '  ; // Redirigir a la página principal
        });
    </script>';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $idRegistros = $_REQUEST['id'];
    $dato1      = $_REQUEST['fila1'];
    $dato2      = $_REQUEST['fila2'];
    $dato3      = $_REQUEST['fila3'];
    $dato4      = $_REQUEST['fila4'];
    $dato6      = $_REQUEST['fila6'];
    $dato8      = $_REQUEST['fila8'];
    $estado     = $_REQUEST['fila11'];
    $estado_antiguo = $_REQUEST['estado_antiguo'];
    $link         = $_REQUEST['link'];


    if ($dato8 != $estado_antiguo && in_array($dato8, ['Viendo', 'Emision'])) {
        $fecha_inicio = $fecha_actual;
        $fecha_fin    = $_REQUEST['fecha_fin'];
    } elseif ($dato8 != $estado_antiguo && in_array($dato8, ['Finalizado', 'Pendiente'])) {
        $fecha_inicio = $_REQUEST['fecha_inicio'];
        $fecha_fin    = $fecha_actual;
    } else {
        $fecha_inicio = $_REQUEST['fecha_inicio'];
        $fecha_fin    = $_REQUEST['fecha_fin'];
    }




    $sql = ("SELECT * FROM $tabla where $fila7='$idRegistros';");
    $consulta = mysqli_query($conexion, $sql);

    $Tabla = ucfirst($tabla);

    if (mysqli_num_rows($consulta) == 0) {

        echo "<br>";
        echo $sql;
        $alertTitle = '¡Error!';
        $alertText = 'No se puede editar porque ' . $fila1 . ' no existe en ' . $Tabla . '';
        $alertType = 'error';
        $redireccion = "window.location='$link'";

        alerta($alertTitle, $alertText, $alertType, $redireccion);
        die();
    } else {
        echo "Existe en $tabla";
        echo "<br>";

        try {
            $conn = new PDO("mysql:host=$servidor;dbname=$basededatos;charset=utf8", $usuario, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);

            $sql = "UPDATE $tabla SET 
                $fila1 = :dato1,
                $fila2 = :dato2,
                $fila3 = :dato3,
                $fila4 = :dato4,
                $fila8 = :dato8,
                $fila6 = :dato6,
                $fila11 = :estado,
                Fecha_Inicio = :fecha_inicio,
                Fecha_Fin = :fecha_fin
                WHERE $fila7 = :idRegistros";

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':dato1' => $dato1,
                ':dato2' => $dato2,
                ':dato3' => $dato3,
                ':dato4' => $dato4,
                ':dato8' => $dato8,
                ':dato6' => $dato6,
                ':estado' => $estado,
                ':fecha_inicio' => $fecha_inicio,
                ':fecha_fin' => $fecha_fin,
                ':idRegistros' => $idRegistros
            ]);
        } catch (PDOException $e) {
            die("Error en la actualización: " . $e->getMessage());
        }


        if ($dato8 != $estado_antiguo && in_array($dato8, ['Finalizado', 'Pendiente'])) {
            $alertTitle = '¡Agregar Calificación!';
            $alertText = 'La serie ' . $dato1 . ' T' . $dato4 . ' terminó, ¿desea clasificarla?';
            $alertType = 'info';
            $redireccion = "window.location='Calificaciones/editar_stars.php?id=" . urlencode($idRegistros) .
                "&nombre=" . urlencode($dato1) .
                "&temporada=" . urlencode($dato4) . "';";
        } else {
            $alertTitle = '¡Actualización Exitosa!';
            $alertText = 'Actualizando serie de ' . $dato1 . ' en ' . $Tabla;
            $alertType = 'success';
            $redireccion = "window.location='$link'";
        }




        alerta($alertTitle, $alertText, $alertType, $redireccion);
        die();
    }
}
