<?php
include_once "/var/www/html/models/kitchenmodel.php";
require_once "/var/www/html/models/openmodel.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$kitchenModel = new KitchenModel();
$ventas = $kitchenModel->getAll();

// Almacenar datos en un array
$rows = [];
$currentOrderId = null; // Inicializar $currentOrderId

foreach ($ventas as $venta) {
    if ($venta->getO_id_Order() !== $currentOrderId) {
        if ($currentOrderId !== null) {
            $row = [
                'id' => $currentOrderId,
                'details' => $currentDetails,
                'state' => $currentButtonState,
                'estado' => $currentEstado,
            ];
            $rows[] = $row;
        }

        $currentOrderId = $venta->getO_id_Order();
        $currentDetails = $venta->getD_name_menu() . ': ' . $venta->getD_Cant() . ' (' . $venta->getD_detail() . ')';
        $currentButtonState = $venta->getO_order_State();
        if ($currentButtonState == 1) {
            $currentEstado = 'En Espera';
        } elseif ($currentButtonState == 2) {
            $currentEstado = 'En Proceso';
        } elseif ($currentButtonState == 3) {
            $currentEstado = 'Terminado';
        }
    } else {
        $currentDetails .= ' + ' . $venta->getD_name_menu() . ': ' . $venta->getD_Cant() . ' (' . $venta->getD_detail() . ')';
    }
}

// Agregar la última fila al array
$row = [
    'id' => $currentOrderId,
    'details' => $currentDetails,
    'state' => $currentButtonState,
    'estado' => $currentEstado,
];
$rows[] = $row;

// Ordenar el array por ID de forma descendente
usort($rows, function($a, $b) {
    return $b['id'] - $a['id'];
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Detalles de Ventas</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            background-color: #f4f6f9;
        }
        .estado-espera {
            /* Estilos para el estado En Espera */
        }
        .estado-proceso {
            background-color: #DCDC41; /* Color amarillo para el estado En Proceso */
        }
        .estado-terminado {
            background-color: #54DC41; /* Color verde para el estado Terminado */
        }
    </style>
</head>
<body>
<?php
if (!empty($rows)) {
    echo '<div class="container mt-5">
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID Orden</th>
                        <th>Nombre del Menú, Cantidad y Detalles</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>';

    foreach ($rows as $row) {
        // Agregar clase de estilo condicionalmente según el estado
        $class = '';
        if ($row['state'] == 2) {
            $class = 'estado-proceso';
        } elseif ($row['state'] == 3) {
            $class = 'estado-terminado';
        }
        echo '<tr id="userRow' . htmlspecialchars($row['id']) . '" class="' . $class . '">';
        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['details']) . '</td>';
        echo '<td>' . htmlspecialchars($row['estado']) . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table></div>';
} else {
    echo '<p class="mt-5 text-center">No hay detalles de ventas disponibles.</p>';
}
?>
</body>
</html>
