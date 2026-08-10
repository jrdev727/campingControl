<?php
require_once 'api_init.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');
$conn->query("SET time_zone = '-03:00'");

// Verificar autenticación
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "No autenticado"]);
    exit();
}

// Verificar rol de administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    http_response_code(403);
    echo json_encode(["error" => "Acceso denegado"]);
    exit();
}

$stats = [];

try {
    // 1. Total de entradas hoy
    $sql_hoy = "SELECT COUNT(*) as total, COALESCE(SUM(precio), 0) as ingresos
                FROM entradas
                WHERE DATE(fecha_hora) = CURDATE()
                AND estado = 'activo'";
    $resultado_hoy = $conn->query($sql_hoy);
    $datos_hoy = $resultado_hoy->fetch_assoc();
    $stats['entradas_hoy'] = [
        'total' => (int)$datos_hoy['total'],
        'ingresos' => (float)$datos_hoy['ingresos']
    ];

    // 2. Total de entradas del mes
    $sql_mes = "SELECT COUNT(*) as total, COALESCE(SUM(precio), 0) as ingresos
                FROM entradas
                WHERE MONTH(fecha_hora) = MONTH(CURDATE())
                AND YEAR(fecha_hora) = YEAR(CURDATE())
                AND estado = 'activo'";
    $resultado_mes = $conn->query($sql_mes);
    $datos_mes = $resultado_mes->fetch_assoc();
    $stats['entradas_mes'] = [
        'total' => (int)$datos_mes['total'],
        'ingresos' => (float)$datos_mes['ingresos']
    ];

    // 3. Total de entradas del mes anterior (para comparación)
    $sql_mes_anterior = "SELECT COUNT(*) as total
                         FROM entradas
                         WHERE MONTH(fecha_hora) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                         AND YEAR(fecha_hora) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                         AND estado = 'activo'";
    $resultado_mes_anterior = $conn->query($sql_mes_anterior);
    $datos_mes_anterior = $resultado_mes_anterior->fetch_assoc();
    $total_mes_anterior = (int)$datos_mes_anterior['total'];

    // Calcular porcentaje de cambio
    if ($total_mes_anterior > 0) {
        $cambio_porcentaje = (($stats['entradas_mes']['total'] - $total_mes_anterior) / $total_mes_anterior) * 100;
    } else {
        $cambio_porcentaje = $stats['entradas_mes']['total'] > 0 ? 100 : 0;
    }
    $stats['entradas_mes']['cambio_porcentaje'] = round($cambio_porcentaje, 2);

    // 4. Distribución por tipo de entrada (hoy)
    $sql_tipos = "SELECT tipo_entrada, COUNT(*) as total
                  FROM entradas
                  WHERE DATE(fecha_hora) = CURDATE()
                  AND estado = 'activo'
                  GROUP BY tipo_entrada";
    $resultado_tipos = $conn->query($sql_tipos);
    $tipos_entrada = [];
    while ($row = $resultado_tipos->fetch_assoc()) {
        $tipos_entrada[$row['tipo_entrada']] = (int)$row['total'];
    }
    $stats['tipos_entrada'] = $tipos_entrada;

    // 5. Disponibilidad de recursos (Quinchos y Reposeras)
    $sql_inventario = "SELECT tipo_recurso, cantidad_disponible
                       FROM inventario
                       WHERE tipo_recurso IN ('Quincho', 'Reposera')";
    $resultado_inventario = $conn->query($sql_inventario);
    $inventario = [];
    while ($row = $resultado_inventario->fetch_assoc()) {
        $inventario[$row['tipo_recurso']] = (int)$row['cantidad_disponible'];
    }
    $stats['inventario'] = $inventario;

    // 6. Últimas 10 entradas
    $sql_ultimas = "SELECT id, dni_cliente, tipo_entrada, precio,
                    DATE_FORMAT(fecha_hora, '%d/%m/%Y %H:%i') as fecha_formateada
                    FROM entradas
                    WHERE estado = 'activo'
                    ORDER BY fecha_hora DESC
                    LIMIT 10";
    $resultado_ultimas = $conn->query($sql_ultimas);
    $ultimas_entradas = [];
    while ($row = $resultado_ultimas->fetch_assoc()) {
        $ultimas_entradas[] = [
            'id' => (int)$row['id'],
            'dni' => $row['dni_cliente'] ?: 'N/A',
            'tipo' => $row['tipo_entrada'],
            'precio' => (float)$row['precio'],
            'fecha' => $row['fecha_formateada']
        ];
    }
    $stats['ultimas_entradas'] = $ultimas_entradas;

    // 7. Ingresos por día de la última semana (para gráfico)
    $sql_semana = "SELECT DATE(fecha_hora) as fecha, COALESCE(SUM(precio), 0) as ingresos
                   FROM entradas
                   WHERE fecha_hora >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                   AND estado = 'activo'
                   GROUP BY DATE(fecha_hora)
                   ORDER BY fecha ASC";
    $resultado_semana = $conn->query($sql_semana);
    $ingresos_semana = [];
    while ($row = $resultado_semana->fetch_assoc()) {
        $ingresos_semana[] = [
            'fecha' => $row['fecha'],
            'ingresos' => (float)$row['ingresos']
        ];
    }
    $stats['ingresos_semana'] = $ingresos_semana;

    // 8. Estadísticas financieras adicionales
    // Promedio de ticket (precio promedio por entrada)
    $sql_promedio = "SELECT COALESCE(AVG(precio), 0) as promedio FROM entradas
                     WHERE DATE(fecha_hora) = CURDATE() AND estado = 'activo'";
    $resultado_promedio = $conn->query($sql_promedio);
    $datos_promedio = $resultado_promedio->fetch_assoc();
    $stats['promedio_ticket'] = round((float)($datos_promedio['promedio'] ?? 0), 2);

    // Ingresos por tipo de entrada (hoy)
    $sql_ingresos_tipo = "SELECT tipo_entrada, SUM(precio) as total FROM entradas
                          WHERE DATE(fecha_hora) = CURDATE() AND estado = 'activo'
                          GROUP BY tipo_entrada";
    $resultado_ingresos_tipo = $conn->query($sql_ingresos_tipo);
    $ingresos_por_tipo = [];
    if ($resultado_ingresos_tipo) {
        while ($row = $resultado_ingresos_tipo->fetch_assoc()) {
            $ingresos_por_tipo[$row['tipo_entrada']] = (float)$row['total'];
        }
    }
    $stats['ingresos_por_tipo'] = $ingresos_por_tipo;

    // Comparación con ayer
    $sql_ayer = "SELECT COUNT(*) as total, COALESCE(SUM(precio), 0) as ingresos
                 FROM entradas
                 WHERE DATE(fecha_hora) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
                 AND estado = 'activo'";
    $resultado_ayer = $conn->query($sql_ayer);
    $datos_ayer = $resultado_ayer->fetch_assoc();
    $stats['ayer'] = [
        'total' => (int)($datos_ayer['total'] ?? 0),
        'ingresos' => (float)($datos_ayer['ingresos'] ?? 0)
    ];

    // Entradas por hora (hoy) para gráfico
    $sql_horas = "SELECT HOUR(fecha_hora) as hora, COUNT(*) as total
                  FROM entradas
                  WHERE DATE(fecha_hora) = CURDATE()
                  AND estado = 'activo'
                  GROUP BY HOUR(fecha_hora)
                  ORDER BY hora ASC";
    $resultado_horas = $conn->query($sql_horas);
    $entradas_por_hora = [];
    while ($row = $resultado_horas->fetch_assoc()) {
        $entradas_por_hora[(int)$row['hora']] = (int)$row['total'];
    }
    $stats['entradas_por_hora'] = $entradas_por_hora;

    // Hora pico (hora con más entradas hoy)
    if (!empty($entradas_por_hora)) {
        $hora_pico = array_search(max($entradas_por_hora), $entradas_por_hora);
        $stats['hora_pico'] = $hora_pico;
    } else {
        $stats['hora_pico'] = null;
    }

    // Enviar respuesta exitosa
    echo json_encode([
        'success' => true,
        'data' => $stats
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al obtener estadísticas: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
