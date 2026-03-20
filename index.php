<?php

require 'bd.php';
// Función para clasificar según tus reglas
function obtenerTipoSerie($totalCaps)
{
    if ($totalCaps < 10) return "Corta";
    if ($totalCaps >= 10 && $totalCaps < 20) return "Mediana";
    return "Larga";
}

$sizebtn = "sm";

// Diccionario de traducción
$dias_esp = [
    'Monday'    => 'Lunes',
    'Tuesday'   => 'Martes',
    'Wednesday' => 'Miercoles',
    'Thursday'  => 'Jueves',
    'Friday'    => 'Viernes',
    'Saturday'  => 'Sabado',
    'Sunday'    => 'Domingo'
];

$dia_actual_esp = $dias_esp[date('l')]; // Ejemplo: 'Miércoles'

// SQL para aumentar 1 capítulo disponible si hoy es el día de estreno
// y no se ha actualizado hoy todavía.
$sqlAutoUpdate = "UPDATE $tabla 
                  SET Caps_Disponibles = Caps_Disponibles + 1, 
                      Ultima_Actualizacion = CURDATE() 
                  WHERE Dias = '$dia_actual_esp' 
                  AND Estado = 'Emisión' 
                  AND Caps_Disponibles < Total 
                  AND (Ultima_Actualizacion != CURDATE() OR Ultima_Actualizacion IS NULL)";

mysqli_query($conexion, $sqlAutoUpdate);



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Series</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="./css/style new.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" type="text/css" href="./css/star.css?v=<?php echo time(); ?>">
</head>

<body>

    <?php include('menu.php'); ?>

    <div class="main-container">
        <!--- Formulario para registrar Cliente --->
        <form action="" method="GET" class="d-flex gap-2 flex-wrap">

            <button type="button" class="btn btn-<?php echo $sizebtn ?> btn-custom btn-primary vista-celu" data-bs-toggle="modal" data-bs-target="#new">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nuevo <?php echo ucfirst($tabla); ?>
            </button>

            <button type="button" class="btn btn-custom btn-info btn-<?php echo $sizebtn ?>" onclick="toggleFilter('myDIV')">
                <i class="fas fa-filter"></i>
                <span>Filtrar</span>
            </button>

            <button type="button" class="btn btn-info btn-custom btn-<?php echo $sizebtn ?>" onclick="toggleFilter('myDIV2')">
                <i class="fas fa-search"></i> Buscar
            </button>

            <button type="submit" name="link" class="btn btn-warning btn-custom btn-<?php echo $sizebtn ?>" style="text-decoration: none;">
                <i class="fas fa-unlink"></i> Sin Link
            </button>

            <button class="btn btn-custom btn-secondary btn-<?php echo $sizebtn ?> " type="submit" name="borrar">
                <i class="fas fa-eraser"></i>
                <span>Borrar Filtros</span>
            </button>
        </form>

        <div id="myDIV" class="filter-section" style="display:none;">
            <form action="" method="GET" class="row g-3">
                <div class="col-md-4">
                    <select name="estado" class="form-control" style="max-width: 100% !important;">
                        <option value="">Seleccione:</option>
                        <option value="Viendo">Viendo</option>
                        <?php
                        $query = $conexion->query("SELECT * FROM $tabla3;");
                        while ($valores = mysqli_fetch_array($query)) {
                            echo '<option value="' . $valores['Estado'] . '">' . $valores['Estado'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <input type="hidden" name="accion" value="Filtro">
                <br>

                <div class="col-md-4">
                    <button class="btn btn-primary" type="submit" name="filtrar">
                        <i class="fas fa-check"></i> Aplicar Filtro
                    </button>
                    <button class="btn btn-secondary" type="submit" name="borrar">
                        <i class="fas fa-times"></i> Borrar
                    </button>
                </div>
            </form>
        </div>
        <div class="filter-section" id="myDIV2" style="display:none;">
            <form action="" method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="search" class="form-control" name="busqueda" placeholder="Nombre de la Serie...">
                </div>

                <div class="col-md-4">
                    <button class="btn btn-primary" type="submit" name="buscar">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                    <button class="btn btn-secondary" type="submit" name="borrar">
                        <i class="fas fa-times"></i> Limpiar
                    </button>
                </div>
            </form>
        </div>

        <?php

        include('./ModalCrear.php');

        $where = "WHERE $fila8!='Finalizado' ORDER BY FIELD(Estado, 'Viendo', 'Emisión', 'Pausado', 'Pendiente')  limit 100";
        $busqueda = "";

        if (isset($_GET['borrar'])) {


            $where = "WHERE $fila8!='Finalizado' ORDER BY FIELD(Estado, 'Viendo', 'Emisión', 'Pausado', 'Pendiente')  ASC limit 100";
        } else if (isset($_GET['filtrar'])) {
            if (isset($_GET['estado'])) {
                $estado   = $_REQUEST['estado'];

                $where = "WHERE $fila8='$estado' ORDER BY `$tabla`.`$fila7` DESC  limit 100";
            }
        } else if (isset($_GET['buscar'])) {
            if (isset($_GET['busqueda'])) {
                $busqueda   = $_REQUEST['busqueda'];


                $where = "WHERE $fila1 LIKE '%$busqueda%' ORDER BY `$tabla`.`$fila7` DESC  limit 100";
            }
        } else if (isset($_GET['link'])) {

            $where = "WHERE Link = '' or Estado_Link != 'Correcto' ORDER BY `$tabla`.`Estado` ASC  limit 100";
        }

        ?>

    </div>
    <h1 class="text-center text-primary fw-bold">
        <?php echo ucfirst($tabla) ?>
    </h1>
    <div class="content-card">
        <div class="table-container table-responsive">
            <table id="example" class="table custom-table">
                <thead>
                    <tr>
                        <th>Serie</th>
                        <th>Progreso Episodios</th>
                        <th>Bloques</th>
                        <th>Temporadas</th>
                        <th>Estado</th>
                        <th>Dias</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // SQL que ya trae la clasificación calculada
                    $sql1 = "SELECT *, 
                        CASE 
                            WHEN Total < 10 THEN 'Corta'
                            WHEN Total >= 10 AND Total < 20 THEN 'Mediana'
                            ELSE 'Larga'
                        END as Categoria_Tamano
                        FROM $tabla $where";

                    $result = mysqli_query($conexion, $sql1);
                    //echo $sql1;


                    while ($mostrar = mysqli_fetch_array($result)) {

                        // Si 'Total' es mayor a 0, calcula el porcentaje. Si es 0, el porcentaje es 0.
                        $porcentaje = ($mostrar['Total'] > 0) ? ($mostrar['Vistos'] / $mostrar['Total']) * 100 : 0;

                        $tipo = $mostrar['Categoria_Tamano'];

                        // Lógica de Bloques (Regla 2)
                        if ($tipo == "Corta") $numBloques = 1;
                        elseif ($tipo == "Mediana") $numBloques = 2;
                        else $numBloques = ceil($mostrar['Total'] / 6);

                        $bloquesCompletos = ($mostrar['Total'] > 0) ? floor(($mostrar['Vistos'] / $mostrar['Total']) * $numBloques) : 0;
                    ?>


                        <tr>
                            <td>
                                <a href="<?= $mostrar[$fila2] ?>" target="_blank" class="fw-bold text-decoration-none">
                                    <?= $mostrar[$fila1] ?>
                                </a>
                                <small class="d-block text-muted"><?= $tipo ?></small>
                            </td>
                            <td style="min-width: 150px;">
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar <?= $porcentaje == 100 ? 'bg-success' : '' ?>" style="width: <?= $porcentaje ?>%"></div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <small class="fw-bold"><?= $mostrar['Vistos'] ?> / <?= $mostrar['Total'] ?> caps</small>

                                    <?php
                                    // LÓGICA DE CAPÍTULOS DISPONIBLES (NUEVO)
                                    $nuevos =  $mostrar['Caps_Disponibles'] - $mostrar['Vistos'];

                                    if ($nuevos > 0 && $mostrar[$fila8] == 'Emision'): ?>
                                        <span class="badge bg-info text-white pulse-new" style="font-size: 0.65rem; border-radius: 50px; padding: 4px 8px;">
                                            <i class="fas fa-plus me-1" style="font-size: 0.6rem;"></i><?= $nuevos ?> por ver
                                        </span>
                                    <?php endif; ?>
                                </div>

                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <?php for ($i = 0; $i < $numBloques; $i++): ?>
                                        <div class="rounded-pill <?= ($i < $bloquesCompletos) ? 'bg-success' : 'bg-light border' ?>"
                                            style="width: 25px; height: 8px;"
                                            data-bs-toggle="tooltip" title="Bloque <?= $i + 1 ?>"></div>
                                    <?php endfor; ?>
                                </div>
                            </td>
                            <td>
                                <?php
                                $tActual = $mostrar[$fila4] ?? 0; // Temporada que estás viendo
                                $tTotal = $mostrar['Temp_Totales'] ?? 1; // Total de temporadas

                                // Calculamos la resta (Temporadas faltantes)
                                $resta = $tTotal - $tActual;

                                // Lógica de color basada en la RESTA:
                                // 3 o más faltantes: Peligro (Rojo)
                                // 1 o 2 faltantes: Advertencia (Naranja)
                                // 0 o menos: Info (Azul)
                                $vistos = $mostrar['Vistos'] ?? 0;

                                $colorT = ($resta >= 5) ? 'danger' : (($resta >= 3) ? 'warning text-dark' : (($resta >= 1) ? 'info text-white' : (($vistos >= 1) ? 'success text-white' : 'info text-white')));
                                ?>

                                <span class="badge bg-<?= $colorT ?>">
                                    T-<?= $tActual ?> / Total: <?= $tTotal ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($mostrar[$fila8] == 'Emision' || $mostrar[$fila8] == 'Viendo'): ?>
                                    <span class="badge w-100 status-en-emision"><i class="fas fa-broadcast-tower"></i> <?= $mostrar[$fila8] ?></span>
                                <?php else: ?>
                                    <span class="status-badge status-<?= strtolower($mostrar[$fila8]) ?> w-100">
                                        <?= $mostrar[$fila8] ?>
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td class="text-center" style="vertical-align: middle;">
                                <div class="d-flex flex-column align-items-center justify-content-center" style="position: relative; min-width: 80px;">
                                    <?php if ($mostrar['Dias'] == $dia_actual_esp): ?>
                                        <div class="estreno-hoy-badge" title="¡Estrena hoy!">
                                            <i class="fas fa-calendar-check pulse-icon"></i>
                                        </div>
                                        <span class="fw-bold text-primary" style="font-size: 0.85rem; letter-spacing: 0.5px;">
                                            <?= $mostrar['Dias'] ?>
                                        </span>
                                        <small class="text-primary fw-bold" style="font-size: 0.6rem; text-transform: uppercase;">Hoy</small>
                                    <?php else: ?>
                                        <span class="day-badge" style="font-size: 0.8rem; opacity: 0.7;">
                                            <?= $mostrar['Dias'] ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>




                            <td data-label="Acciones" style="text-align: center; vertical-align: middle;">
                                <div class="action-buttons" style="display: inline-flex; gap: 5px;">
                                    <button type="button"
                                        class="action-button bg-info"
                                        style="display: <?= $display; ?>;"
                                        data-toggle="modal"
                                        data-target="#caps<?= $mostrar[$fila7]; ?>"
                                        aria-label="Aprobar">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <button type="button"
                                        class="action-button bg-primary"
                                        data-tooltip="Editar"
                                        data-toggle="modal"
                                        data-target="#edit<?= $mostrar[$fila7]; ?>"
                                        aria-label="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button"
                                        class="action-button bg-danger"
                                        data-tooltip="Eliminar"
                                        data-toggle="modal"
                                        data-target="#delete<?= $mostrar[$fila7]; ?>"
                                        aria-label="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>

                        </tr>

                    <?php
                        include('Modal-Caps.php');
                        include('ModalEditar.php');
                        include('ModalDelete.php');
                    } ?>
                </tbody>
            </table>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
            <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <script>
                $(document).ready(function() {
                    $('#example').DataTable({
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                        },
                        responsive: true,
                        order: [],
                    });
                });

                function toggleFilter(filterId) {
                    const filter = document.getElementById(filterId);
                    filter.style.display = filter.style.display === 'none' ? 'block' : 'none';
                }

                function actualizarValorMunicipioInm() {
                    let municipio = document.getElementById("municipio").value;
                    //Se actualiza en municipio inm
                    document.getElementById("municipio_inm").value = municipio;
                }
            </script>
</body>

</html>