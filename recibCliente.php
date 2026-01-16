<!--coment-->
<header>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</header>

<?php
include 'bd.php';
$dato1      = $_REQUEST['fila1'];
$dato2      = $_REQUEST['fila2'];
$dato3      = $_REQUEST['fila3'];
$dato8      = $_REQUEST['fila8'];
$dato6      = $_REQUEST['fila6'];
$epi_totales = $_REQUEST['epi_totales'];
$temp_totales = $_REQUEST['temp_totales'];

$sql      = ("SELECT * FROM $tabla where $fila1='$dato1';");
$consulta = mysqli_query($conexion, $sql);

echo $sql;
echo "<br>";
echo $fila1;
echo "<br>";
echo $dato1;
echo "<br>";
$Tabla = ucfirst($tabla);

if ($dato2 == "") {
    $estado = "Faltante";
} else {
    $estado = "Correcto";
}



if (mysqli_num_rows($consulta) == 0) {

    echo "$dato1 no existe en $tabla";
    echo "<br>";

    try {
        $sql = "INSERT INTO $tabla(`$fila1`,`$fila2`,`$fila3`, `$fila6`,`$fila8`,`$fila11`,`Temp_Totales`,`Total`) VALUES( '" . $dato1 . "','" . $dato2 . "','" . $dato3 . "','" . $dato6 . "','" . $dato8 . "','" . $estado . "', '" . $temp_totales . "', '" . $epi_totales . "')";


        echo $sql;
        echo "<br>";
        $result = mysqli_query($conexion, $sql);
    } catch (PDOException $e) {
        echo $sql;
        echo "<br>";
        echo $e;
    }

    echo '<script>
        Swal.fire({
            icon: "success",
            title: "Creando Registro de ' . $dato1 . '  en  ' . $Tabla . '",
            confirmButtonText: "OK"
        }).then(function() {
            window.location = "index.php";
        });
        </script>';

    echo "<br>";
} else {

    echo "$dato1 existe en $tabla";

    echo '<script>
    Swal.fire({
        icon: "error",
        title: "Registro de ' . $dato1 . ' Existe en  ' . $Tabla . '",
        confirmButtonText: "OK"
    }).then(function() {
        window.location = "index.php";
    });
    </script>';
}
