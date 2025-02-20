<!--coment-->
<header>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</header>

<?php
include 'bd.php';

$idRegistros = $_REQUEST['id'];
$dato1      = $_REQUEST['fila1'];
$dato2      = $_REQUEST['fila2'];
$dato3      = $_REQUEST['fila3'];
$dato4      = $_REQUEST['fila4'];
$dato6      = $_REQUEST['fila6'];
$dato8      = $_REQUEST['fila8'];
$estado     = $_REQUEST['fila11'];

$sql = ("SELECT * FROM $tabla where $fila7='$idRegistros';");
$consulta = mysqli_query($conexion, $sql);


echo $sql;
echo "<br>";
$Tabla = ucfirst($tabla);

if (mysqli_num_rows($consulta) == 0) {
    echo "No Existe en $tabla";
    echo '<script>
        Swal.fire({
            icon: "error",
            title: "No se puede editar porque ' . $fila1 . ' no existe en ' . $tabla . '",
            confirmButtonText: "OK"
    
        }).then(function() {
            window.location = "index.php";
        });
        </script>';
} else {
    echo "Existe en $tabla";
    echo "<br>";

    try {
        $conn = new PDO("mysql:host=$servidor;dbname=$basededatos", $usuario, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "UPDATE $tabla SET 
        `$fila1` ='" . $dato1 . "',
        `$fila2` ='" . $dato2 . "',
        `$fila3` ='" . $dato3 . "',
        `$fila4` ='" . $dato4 . "',
        `$fila8`='" . $dato8 . "',
        `$fila6` ='" . $dato6 . "',
        `$fila11` ='" . $estado . "'
        WHERE `$fila7`='" . $idRegistros . "'";
        $conn->exec($sql);
        echo $sql;
        echo '<script>
        Swal.fire({
            icon: "success",
            title: "Actualizando registro de ' . $dato1 . ' en ' . $Tabla . '",
            confirmButtonText: "OK"
        }).then(function() {
            window.location = "index.php";
        });
        </script>';
    } catch (PDOException $e) {
        $conn = null;
        echo $e;
        echo "<br>";
        echo $sql;
        echo '<script>
        Swal.fire({
            icon: "error",
            title: "Registro Repetido de ' . $dato1 . ' en  ' . $Tabla . '",
            confirmButtonText: "OK"
        }).then(function() {
            window.location = "index.php";
        });
        </script>';
    }

    echo "<br>";
}
    

//UPDATE `emision` SET `Capitulos` = '1' WHERE `emision`.`ID` = 19;
//SELECT SUM(Capitulos)+1 total FROM emision Where Nombre="Dragon Ball";




//$result_update = mysqli_query($conexion, $update);

//header("location:index.php");
