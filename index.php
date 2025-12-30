<?php

require 'bd.php';

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
                        <th><?php echo $fila7 ?></th>
                        <th><?php echo $fila1 ?></th>
                        <th>Vistos</th>
                        <th>Progreso</th>
                        <th><?php echo $fila4 ?></th>
                        <th><?php echo $fila8 ?></th>
                        <th><?php echo $fila6 ?></th>


                        <th style="text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql1 = "SELECT * FROM $tabla $where";

                    $result = mysqli_query($conexion, $sql1);
                    //echo $sql1;


                    while ($mostrar = mysqli_fetch_array($result)) {
                        $iden = $mostrar[$fila7];

                        $faltantes = $mostrar['Total'] - $mostrar['Vistos'];
                        $porcentaje = ($mostrar['Vistos'] / $mostrar['Total']) * 100;

                    ?>
                        <tr>
                            <td class="fw-500"><?php echo $mostrar[$fila7] ?></td>
                            <td class="fw-500"><a href="<?php echo $mostrar[$fila2] ?>" title="<?php echo $mostrar[$fila11] ?>" target="_blanck" style="text-decoration: none;"><?php echo $mostrar[$fila1] ?></a></td>
                            <td>
                                <div class="progress-cell">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1">
                                            <div class="progress-bar bg-<?php echo $porcentaje == 100 ? 'success' : 'primary' ?>"
                                                role="progressbar"
                                                style="width: <?php echo $porcentaje ?>%"
                                                aria-valuenow="<?php echo $porcentaje ?>"
                                                aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                        <span class="small"><?php echo $mostrar['Vistos'] ?>/<?php echo $mostrar['Total'] ?></span>
                                    </div>
                                </div>
                                <div class="progress-celu">
                                    <span class="small"><?php echo $mostrar['Vistos'] ?>/<?php echo $mostrar['Total'] ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="episode-badge <?php echo $faltantes > 0 ? 'episode-pending' : 'episode-watched' ?>">
                                    <?php echo $faltantes > 0 ? $faltantes . ' pendientes' : 'Al día' ?>
                                </span>
                            </td>
                            <td class="fw-500">Temporada <?php echo $mostrar[$fila4] ?></td>
                            <td>
                                <span class="status-badge 
                                    <?php
                                    $display = 'none';
                                    if ($mostrar[$fila8] == 'Emision' || $mostrar[$fila8] == 'Viendo') {
                                        echo 'status-en-emision';
                                        $display = 'flex';
                                    } elseif ($mostrar[$fila8] == 'Finalizado') {
                                        echo 'status-finalizado';
                                    } elseif ($mostrar[$fila8] == 'Pendiente') {
                                        echo 'status-pendiente';
                                    } elseif ($mostrar[$fila8] == 'Pausado') {
                                        echo 'status-pausado';
                                    }
                                    ?>">
                                    <?php echo $mostrar[$fila8] ?>
                                </span>

                            </td>
                            <td><span class="day-badge"><?php echo $mostrar['Dias'] ?></span></td>


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