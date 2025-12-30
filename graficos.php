<?php

require 'bd.php';

// Diccionario de meses en español
$mesesTraduccion = [
    '01' => 'Ene',
    '02' => 'Feb',
    '03' => 'Mar',
    '04' => 'Abr',
    '05' => 'May',
    '06' => 'Jun',
    '07' => 'Jul',
    '08' => 'Ago',
    '09' => 'Sep',
    '10' => 'Oct',
    '11' => 'Nov',
    '12' => 'Dic'
];

// Colores consistentes con tus otros gráficos
$coloresEstado = [
    'Emision' => '#91cc75',
    'Viendo' => '#91cc75',
    'Finalizado' => '#fac858',
    'Pausado' => '#ee6666',
    'Pendiente' => '#73c0de'
];

// Consulta filtrando fechas inválidas y agrupando por mes
$sqlFechas = "SELECT DATE_FORMAT(Fecha_Inicio, '%Y-%m') as mes_anio, COUNT(*) as total 
              FROM $tabla 
              WHERE Fecha_Inicio IS NOT NULL AND Fecha_Inicio != '0000-00-00' 
              GROUP BY mes_anio 
              ORDER BY mes_anio ASC";

$resFechas = mysqli_query($conexion, $sqlFechas);

$labelsEspanol = [];
$datosValores = [];

while ($row = mysqli_fetch_assoc($resFechas)) {
    // Separamos el año y el mes (ej: 2025-05 -> ['2025', '05'])
    $partes = explode('-', $row['mes_anio']);
    $anio = $partes[0];
    $mesNumero = $partes[1];

    // Creamos la etiqueta: "May 2025"
    $labelsEspanol[] = $mesesTraduccion[$mesNumero] . ' ' . $anio;
    $datosValores[] = (int)$row['total'];
}
// 2. Promedio de progreso (Vistos / Total * 100) por Estado
$sqlPromedio = "SELECT Estado, AVG((Vistos/Total)*100) as promedio 
                FROM $tabla 
                GROUP BY Estado";
$resPromedio = mysqli_query($conexion, $sqlPromedio);

$labelsEstado = [];
$valoresPromedio = [];
while ($row = mysqli_fetch_assoc($resPromedio)) {
    $labelsEstado[] = $row['Estado'];
    $valoresPromedio[] = round($row['promedio'], 2);
}

// Opción A: Distribución por Estado (Para el gráfico de tarta)
$sqlEstadoCount = "SELECT $fila8 as estado, COUNT(*) as cantidad FROM $tabla GROUP BY $fila8";
$resEstadoCount = mysqli_query($conexion, $sqlEstadoCount);
$dataPie = [];
while ($row = mysqli_fetch_assoc($resEstadoCount)) {
    $dataPie[] = ['value' => (int)$row['cantidad'], 'name' => $row['estado']];
}

// Opción B: Series por Día de Emisión (Usando la columna 'Dias')
$sqlDias = "SELECT Dias, COUNT(*) as total FROM $tabla WHERE Dias != '' AND Dias != 'Indefinido' GROUP BY Dias";
$resDias = mysqli_query($conexion, $sqlDias);
$labelsDias = [];
$valoresDias = [];
while ($row = mysqli_fetch_assoc($resDias)) {
    $labelsDias[] = $row['Dias'];
    $valoresDias[] = (int)$row['total'];
}

// Opción C: Progreso Global (Suma total de vistos vs total de capítulos)
$sqlGlobal = "SELECT SUM(Vistos) as vistos, SUM(Total) as total FROM $tabla";
$resGlobal = mysqli_fetch_assoc(mysqli_query($conexion, $sqlGlobal));
$porcentajeGlobal = ($resGlobal['total'] > 0) ? round(($resGlobal['vistos'] / $resGlobal['total']) * 100, 2) : 0;

// Conteo de series por día para el Treemap
$sqlTreemap = "SELECT Dias, COUNT(*) as total FROM $tabla WHERE Dias != '' AND Dias != 'Indefinido' GROUP BY Dias";
$resTreemap = mysqli_query($conexion, $sqlTreemap);
$dataTreemap = [];
while ($row = mysqli_fetch_assoc($resTreemap)) {
    $dataTreemap[] = ['name' => $row['Dias'], 'value' => (int)$row['total']];
}

// Obtener conteo de series iniciadas por cada fecha específica
// Cambia tu consulta por esta:
// Consulta que agrupa por fecha y concatena los nombres de las series
// Consulta con GROUP_CONCAT para traer los nombres de las series
// 1. Datos de Inicio (Azul)
$sqlIni = "SELECT Fecha_Inicio, COUNT(*) as total, GROUP_CONCAT(Nombre SEPARATOR ', ') as nombres 
           FROM $tabla 
           WHERE Fecha_Inicio IS NOT NULL AND Fecha_Inicio != '0000-00-00' 
           GROUP BY Fecha_Inicio";
$resIni = mysqli_query($conexion, $sqlIni);
$dataInicio = [];
while ($row = mysqli_fetch_assoc($resIni)) {
    $dataInicio[] = [$row['Fecha_Inicio'], (int)$row['total'], $row['nombres']];
}

// 2. Datos de Fin (Rojo)
$sqlFin = "SELECT Fecha_Fin, COUNT(*) as total, GROUP_CONCAT(Nombre SEPARATOR ', ') as nombres 
           FROM $tabla 
           WHERE Fecha_Fin IS NOT NULL AND Fecha_Fin != '0000-00-00' 
           GROUP BY Fecha_Fin";
$resFin = mysqli_query($conexion, $sqlFin);
$dataFin = [];
while ($row = mysqli_fetch_assoc($resFin)) {
    $dataFin[] = [$row['Fecha_Fin'], (int)$row['total'], $row['nombres']];
}


// Estructura jerárquica: Estado > Nombres
$sqlSun = "SELECT $fila8 as estado, $fila1 as nombre FROM $tabla";
$resSun = mysqli_query($conexion, $sqlSun);
$hierData = [];

while ($row = mysqli_fetch_assoc($resSun)) {
    $estado = $row['estado'];
    if (!isset($hierData[$estado])) {
        $hierData[$estado] = [
            'name' => $estado,
            'itemStyle' => ['color' => $coloresEstado[$estado] ?? '#6c757d'],
            'children' => []
        ];
    }
    $hierData[$estado]['children'][] = [
        'name' => $row['nombre'],
        'value' => 1
    ];
}
$sunburstData = array_values($hierData);


// Consulta para obtener el promedio de días y el promedio de capítulos por día
$sqlMetricas = "SELECT 
                    AVG(DATEDIFF(Fecha_Fin, Fecha_Inicio)) as promedio_dias,
                    AVG(Vistos / NULLIF(DATEDIFF(Fecha_Fin, Fecha_Inicio), 0)) as caps_por_dia
                FROM $tabla 
                WHERE Estado = 'Finalizado' 
                AND Fecha_Fin IS NOT NULL 
                AND Fecha_Inicio IS NOT NULL 
                AND Fecha_Fin != '0000-00-00' 
                AND Fecha_Inicio != '0000-00-00'";

$resMetricas = mysqli_query($conexion, $sqlMetricas);
$dataMetrica = mysqli_fetch_assoc($resMetricas);

$promedioDias = round($dataMetrica['promedio_dias'], 1);
$promedioHoras = round($promedioDias * 24, 0);
$capsPorDia = round($dataMetrica['caps_por_dia'], 2);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graficos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="./css/style new.css?v=<?php echo time(); ?>">

    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>

</head>

<body>

    <?php include('menu.php'); ?>
    <div>
        <div class="container mt-4">
            <h2 class="mb-4 text-center">Gráficos de Series</h2>

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white shadow-sm border-0">
                        <div class="card-body text-center">
                            <i class="fas fa-calendar-check fa-2x mb-2"></i>
                            <h6 class="text-uppercase small">Tiempo en Días</h6>
                            <h2 class="fw-bold"><?php echo $promedioDias; ?> <small style="font-size: 15px;">días</small></h2>
                            <p class="mb-0 opacity-75">Promedio por serie</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-success text-white shadow-sm border-0">
                        <div class="card-body text-center">
                            <i class="fas fa-clock fa-2x mb-2"></i>
                            <h6 class="text-uppercase small">Tiempo en Horas</h6>
                            <h2 class="fw-bold"><?php echo number_format($promedioHoras); ?> <small style="font-size: 15px;">hrs</small></h2>
                            <p class="mb-0 opacity-75">Transcurridas en total</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-info text-white shadow-sm border-0">
                        <div class="card-body text-center">
                            <i class="fas fa-play-circle fa-2x mb-2"></i>
                            <h6 class="text-uppercase small">Intensidad</h6>
                            <h2 class="fw-bold"><?php echo $capsPorDia; ?> <small style="font-size: 15px;">caps/día</small></h2>
                            <p class="mb-0 opacity-75">Ritmo de visualización</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-lg-6">
                    <div class="card shadow-sm p-3">
                        <h5 class="text-secondary"><i class="fas fa-calendar-alt"></i> Series Iniciadas por Mes</h5>
                        <div id="chartFechas" style="width: 100%; height: 350px;"></div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow-sm p-3">
                        <h5 class="text-secondary"><i class="fas fa-chart-line"></i> % Progreso Promedio por Estado</h5>
                        <div id="chartPromedio" style="width: 100%; height: 350px;"></div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card shadow-sm p-3">
                        <h5 class="text-secondary text-center">Distribución de Estados</h5>
                        <div id="chartRose" style="width: 100%; height: 300px;"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm p-3">
                        <h5 class="text-secondary text-center">Emisiones por Día</h5>
                        <div id="chartDias" style="width: 100%; height: 300px;"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm p-3">
                        <h5 class="text-secondary text-center">Completado Total del Catálogo</h5>
                        <div id="chartGauge" style="width: 100%; height: 300px;"></div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card shadow-sm p-3">
                        <h5 class="text-center">Distribución por Día (Treemap)</h5>
                        <div id="chartTreemap" style="height: 350px;"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm p-3">
                        <h5 class="text-center">Jerarquía de Contenido (Sunburst)</h5>
                        <div id="chartSun" style="height: 350px;"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm p-3">
                        <h5 class="text-center">Historial de Actividad (Calendario)</h5>
                        <div id="chartCal" style="height: 250px;"></div>
                    </div>
                </div>
            </div>
        </div>
</body>


<script>
    // --- GRÁFICO 1: LÍNEA DE TIEMPO (Series por Fecha) ---
    var chartFechas = echarts.init(document.getElementById('chartFechas'));

    var optionFechas = {
        tooltip: {
            trigger: 'axis',
            backgroundColor: 'rgba(255, 255, 255, 0.9)',
            textStyle: {
                color: '#666'
            }
        },
        xAxis: {
            type: 'category',
            // Aquí pasamos las etiquetas ya traducidas desde PHP
            data: <?php echo json_encode($labelsEspanol); ?>,
            axisLabel: {
                color: '#999',
                fontSize: 12
            }
        },
        yAxis: {
            type: 'value',
            splitLine: {
                lineStyle: {
                    type: 'dashed'
                }
            }
        },
        series: [{
            name: 'Series Iniciadas',
            data: <?php echo json_encode($datosValores); ?>,
            type: 'line',
            smooth: true, // Esto hace que la línea sea curva como en tu imagen
            symbolSize: 8,
            areaStyle: {
                // Gradiente para el área bajo la curva
                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                        offset: 0,
                        color: 'rgba(84, 112, 198, 0.4)'
                    },
                    {
                        offset: 1,
                        color: 'rgba(84, 112, 198, 0.1)'
                    }
                ])
            },
            itemStyle: {
                color: '#5470c6'
            }
        }]
    };

    chartFechas.setOption(optionFechas);

    // --- GRÁFICO 2: BARRA DE PROGRESO (Promedio de Vistas) ---
    var chartPromedio = echarts.init(document.getElementById('chartPromedio'));
    var optionPromedio = {
        // 1. Agregar Leyenda
        tooltip: {
            trigger: 'item',
            formatter: '{b}: {c}%'
        },
        // 2. Ajustar márgenes para que los nombres de los estados no se corten
        grid: {
            left: '5%', // Espacio para nombres como "Finalizado"
            right: '15%', // Espacio para la etiqueta de porcentaje a la derecha
            bottom: '15%', // Espacio para la leyenda
            containLabel: true
        },
        xAxis: {
            type: 'value',
            max: 100,
            axisLabel: {
                formatter: '{value}%'
            }
        },
        yAxis: {
            type: 'category',
            data: <?php echo json_encode($labelsEstado); ?>,
            axisLabel: {
                fontSize: 12,
                fontWeight: 'bold'
            }
        },
        series: [{
            name: 'Progreso Promedio', // Debe coincidir con la leyenda
            data: <?php echo json_encode($valoresPromedio); ?>,
            type: 'bar',
            showBackground: true,
            backgroundStyle: {
                color: 'rgba(180, 180, 180, 0.2)'
            },
            itemStyle: {
                color: function(params) {
                    // Mapeo de colores coherente con tu interfaz
                    var estado = params.name;
                    if (estado === 'Viendo' || estado === 'Emision') return '#91cc75'; // Verde
                    if (estado === 'Finalizado') return '#fac858'; // Amarillo/Dorado
                    if (estado === 'Pausado') return '#ee6666'; // Rojo
                    if (estado === 'Pendiente') return '#73c0de'; // Azul claro
                    return '#6c757d'; // Gris por defecto
                }
            },
            label: {
                show: true,
                position: 'right',
                formatter: '{c}%',
                fontWeight: 'bold'
            }
        }]
    };
    chartPromedio.setOption(optionPromedio);

    // Hacer los gráficos responsivos
    window.addEventListener('resize', function() {
        chartFechas.resize();
        chartPromedio.resize();
    });
    // --- 1. NIGHTINGALE ROSE (Distribución por Estado) ---
    var chartRose = echarts.init(document.getElementById('chartRose'));
    chartRose.setOption({
        legend: {
            bottom: '0'
        },
        tooltip: {
            trigger: 'item',
            formatter: '{b}: {c} ({d}%)' // Muestra nombre, cantidad y porcentaje al pasar el mouse
        },
        series: [{
            type: 'pie',
            radius: [20, 100],
            roseType: 'area',
            itemStyle: {
                borderRadius: 5
            },
            // 1. Ocultar el texto (etiquetas) que sale de los sectores
            label: {
                show: false
            },
            // 2. Ocultar las líneas de conexión (las que quieres quitar)
            labelLine: {
                show: false
            },
            data: <?php echo json_encode($dataPie); ?>
        }]
    });
    // --- 2. GRÁFICO DE BARRAS POLAR (Días de Emisión) ---
    var chartDias = echarts.init(document.getElementById('chartDias'));
    chartDias.setOption({
        polar: {
            radius: [30, '80%']
        },
        angleAxis: {
            type: 'category',
            data: <?php echo json_encode($labelsDias); ?>,
            startAngle: 75
        },
        radiusAxis: {},
        tooltip: {},
        series: [{
            type: 'bar',
            data: <?php echo json_encode($valoresDias); ?>,
            coordinateSystem: 'polar',
            itemStyle: {
                color: '#fac858'
            }
        }]
    });

    // --- 3. GAUGE (Progreso General) ---
    var chartGauge = echarts.init(document.getElementById('chartGauge'));
    chartGauge.setOption({
        series: [{
            type: 'gauge',
            progress: {
                show: true,
                width: 18
            },
            axisLine: {
                lineStyle: {
                    width: 18
                }
            },
            axisTick: {
                show: false
            },
            splitLine: {
                length: 15,
                lineStyle: {
                    width: 2,
                    color: '#999'
                }
            },
            anchor: {
                show: true,
                showAbove: true,
                size: 25,
                itemStyle: {
                    borderWidth: 10
                }
            },
            title: {
                show: false
            },
            detail: {
                valueAnimation: true,
                fontSize: 30,
                offsetCenter: [0, '70%'],
                formatter: '{value}%'
            },
            data: [{
                value: <?php echo $porcentajeGlobal; ?>
            }]
        }]
    });

    // Ajuste responsivo para todos
    window.addEventListener('resize', function() {
        chartRose.resize();
        chartDias.resize();
        chartGauge.resize();
    });

    var chartTreemap = echarts.init(document.getElementById('chartTreemap'));
    chartTreemap.setOption({
        tooltip: {
            trigger: 'item'
        },
        series: [{
            type: 'treemap',
            data: <?php echo json_encode($dataTreemap); ?>,
            leafDepth: 1,
            levels: [{
                itemStyle: {
                    borderColor: '#fff',
                    borderWidth: 2,
                    gapWidth: 2
                }
            }]
        }]
    });

    var chartCal = echarts.init(document.getElementById('chartCal'));

    var optionCal = {
        tooltip: {
            trigger: 'item',
            formatter: function(p) {
                // p.seriesName nos dirá si es Inicio o Fin
                var tipo = p.seriesName;
                var fecha = p.data[0];
                var nombres = p.data[2] || "Sin nombre";
                var color = tipo === 'Inicio' ? 'blue' : 'red';
                return `<b style="color:${color}">${tipo}</b><br/>${fecha} - ${nombres}`;
            }
        },
        legend: {
            data: ['Inicio', 'Fin'],
            top: 10
        },
        calendar: {
            top: 80,
            left: 30,
            right: 30,
            cellSize: ['auto', 13],
            range: '2025',
            itemStyle: {
                borderWidth: 0.5,
                borderColor: '#eee'
            },
            dayLabel: {
                firstDay: 1,
                nameMap: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb']
            },
            monthLabel: {
                nameMap: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']
            }
        },
        series: [{
                name: 'Inicio',
                type: 'scatter', // Usamos scatter sobre el calendario para que puedan solaparse
                coordinateSystem: 'calendar',
                symbolSize: 10,
                itemStyle: {
                    color: '#007bff'
                }, // Azul
                data: <?php echo json_encode($dataInicio); ?>
            },
            {
                name: 'Fin',
                type: 'scatter',
                coordinateSystem: 'calendar',
                symbolSize: 10, // Un poco más pequeño para que si coinciden se vean ambos
                itemStyle: {
                    color: '#dc3545'
                }, // Rojo
                data: <?php echo json_encode($dataFin); ?>
            }
        ]
    };

    chartCal.setOption(optionCal);

    chartCal.setOption(optionCal);

    var chartSun = echarts.init(document.getElementById('chartSun'));

    chartSun.setOption({
        // Leyenda para identificar los estados

        tooltip: {
            trigger: 'item',
            formatter: function(params) {
                // Si es una serie, muestra "Estado > Nombre"
                return params.treePathInfo.length > 2 ?
                    params.treePathInfo[1].name + ':<br/><b>' + params.name + '</b>' :
                    'Estado: <b>' + params.name + '</b>';
            }
        },
        series: {
            type: 'sunburst',
            data: <?php echo json_encode($sunburstData); ?>,
            radius: [0, '95%'],
            sort: null,
            emphasis: {
                focus: 'descendant'
            },
            levels: [{},
                {
                    // Nivel 1: Estados (Anillo interior)
                    r0: '0%',
                    r: '35%',
                    label: {
                        rotate: 'tangential',
                        fontSize: 10,
                        fontWeight: 'bold'
                    }
                },
                {
                    // Nivel 2: Series (Anillo exterior)
                    r0: '35%',
                    r: '90%',
                    label: {
                        // OCULTAMOS las etiquetas aquí para limpiar el gráfico
                        show: false
                    }
                }
            ]
        }
    });
</script>

</html>