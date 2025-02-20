<!---->
<header>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</header>

<?php
include 'bd.php';
$idRegistros = $_REQUEST['id'];
$nombre      = $_REQUEST['nombre'];
$vistos      = $_REQUEST['vistos'];
$caps        = $_REQUEST['capitulos'];

$sql = ("SELECT * FROM $tabla WHERE $fila7='$idRegistros';");

$emision = mysqli_query($conexion, $sql);




//UPDATE `emision` SET `Capitulos` = '1' WHERE `emision`.`ID` = 19;
//UPDATE `emision` SET `Capitulos`=Capitulos+1 WHERE Nombre="Dragon Ball";

echo $idRegistros;
echo "<br>";
echo $nombre;
echo "<br>";
echo $vistos;
echo "<br>";
echo $sql;
echo "<br>";
echo $caps;
echo "<br>";


if (mysqli_num_rows($emision) == 0) {
    echo "No Existe en $tabla";
    echo '<script>
        Swal.fire({
            icon: "error",
            title: "No se puede editar porque ' . $nombre . ' no existe en ' . $tabla . '",
            confirmButtonText: "OK"
    
        }).then(function() {
            window.location = "index.php";
        });
        </script>';
} else {
    echo "Esta bien  ";
    echo "<br>";
    echo "<br>";
    try {
        $conn = new PDO("mysql:host=$servidor;dbname=$basededatos", $usuario, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "UPDATE $tabla SET `$fila3` ='" . $caps . "'+'" . $vistos . "' WHERE $fila1='" . $nombre . "';";
        $conn->exec($sql);
        echo $sql;
        echo "<br>";
        echo "<br>";
        echo '<script>
            Swal.fire({
                icon: "success",
                title: "Actualizando Capitulos  de ' . $nombre . '",
                confirmButtonText: "OK"
            }).then(function() {
                window.location = "index.php";
            });
            </script>';
    } catch (PDOException $e) {
        $conn = null;
    }



    echo "<br>";
} 


//$result_update = mysqli_query($conexion, $update);

//header("location:index.php");
