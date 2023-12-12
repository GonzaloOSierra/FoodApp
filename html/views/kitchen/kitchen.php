<?php
include_once "/var/www/html/models/kitchenmodel.php";
require_once "/var/www/html/models/openmodel.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$kitchenModel = new KitchenModel();
$ventas = $kitchenModel->getAll();

// Ordenar el array $ventas por ID de forma descendente
usort($ventas, function ($a, $b) {
    return $b->getO_id_Order() <=> $a->getO_id_Order();
});

$currentOrderId = null; // Inicializar $currentOrderId
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Detalles de Ventas</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* Puedes agregar estilos personalizados aquí */
        body {
            background-color: #f4f6f9; /* o el color gris que prefieras */
        }
    </style>
</head>
<body>
<?php
if (!empty($ventas)) {
    echo '<div class="container mt-5">
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID Orden</th>
                        <th>Nombre del Menú, Cantidad y Detalles</th>
                        <th>Procesar</th>
                        <th>Finalizar</th>
                    </tr>
                </thead>
                <tbody>';

    foreach ($ventas as $venta) {
        // Saltar registros con getO_order_State igual a 3
        if ($venta->getO_order_State() == 3) {
            continue;
        }

        if ($venta->getO_id_Order() !== $currentOrderId) {
            // Nueva fila para un nuevo id_order
            if ($currentOrderId !== null) {
                echo '<tr id="userRow' . htmlspecialchars($currentOrderId) . '">';
                echo '<td>' . htmlspecialchars($currentOrderId) . '</td>';
                echo '<td>' . htmlspecialchars($currentDetails) . '</td>';

                if ($currentButtonState == 2) {
                    echo '<td><button class="btn btn-danger" onclick="unprocesate(' . htmlspecialchars($currentOrderId) . ')">Absolver</button></td>';
                } else {
                    echo '<td><button class="btn btn-warning" onclick="procesate(' . htmlspecialchars($currentOrderId) . ')">Procesar</button></td>';
                }

                echo '<td><button class="btn btn-success" onclick="finalizate(' . htmlspecialchars($currentOrderId) . ')">Finalizar</button></td>';
                echo '</tr>';
            }

            $currentOrderId = $venta->getO_id_Order();
            $currentDetails = $venta->getD_name_menu() . ': ' . $venta->getD_Cant() . ' (' . $venta->getD_detail() . ')';
            $currentButtonState = $venta->getO_order_State();
        } else {
            // Concatena detalles para el mismo id_order
            $currentDetails .= ' + ' . $venta->getD_name_menu() . ': ' . $venta->getD_Cant() . ' (' . $venta->getD_detail() . ')';
        }
    }

    // Imprime la última fila después de imprimir todos los botones
    echo '<tr id="userRow' . htmlspecialchars($currentOrderId) . '">';
    echo '<td>' . htmlspecialchars($currentOrderId) . '</td>';
    echo '<td>' . htmlspecialchars($currentDetails) . '</td>';

    if ($currentButtonState == 2) {
        echo '<td><button class="btn btn-danger" onclick="unprocesate(' . htmlspecialchars($currentOrderId) . ')">Absolver</button></td>';
    } else {
        echo '<td><button class="btn btn-warning" onclick="procesate(' . htmlspecialchars($currentOrderId) . ')">Procesar</button></td>';
    }

    echo '<td><button class="btn btn-success" onclick="finalizate(' . htmlspecialchars($currentOrderId) . ')">Finalizar</button></td>';
    echo '</tr>';

    echo '</tbody></table></div>';
} else {
    echo '<p class="mt-5 text-center">No hay detalles de ventas disponibles.</p>';
}
?>

<script>
function finalizate(orderId) {
    var state = 3;
    var data = "id=" + orderId + "&state=" + state;
    Swal.fire({
        title: '¿Estás seguro de que deseas finalizar este menú?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, finalizar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "<?php echo constant('URL') ?>kitchen/editState", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                if (xhr.status === 200) {
                    console.log(xhr.responseText);
                    var row = document.getElementById("userRow" + orderId);
                    if (row) {
                        row.remove();
                    }
                    CargarContenido('views/kitchen/kitchen.php', 'content-wrapper');
                } else {
                    console.error('Error en la solicitud: ' + xhr.status);
                }
            };
            xhr.onerror = function () {
                console.error('Error de red al intentar realizar la solicitud.');
            };
            xhr.send(data);
        }
    });
}

function procesate(orderId) {
    var state = 2;
    var data = "id=" + orderId + "&state=" + state;
    Swal.fire({
        title: '¿Estás seguro de que deseas procesar este menú?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, procesar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "<?php echo constant('URL') ?>kitchen/editState", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                if (xhr.status === 200) {
                    console.log(xhr.responseText);
                    var row = document.getElementById("userRow" + orderId);
                    if (row) {
                        row.remove();
                    }
                    CargarContenido('views/kitchen/kitchen.php', 'content-wrapper');
                } else {
                    console.error('Error en la solicitud: ' + xhr.status);
                }
            };
            xhr.onerror = function () {
                console.error('Error de red al intentar realizar la solicitud.');
            };
            xhr.send(data);
        }
    });
}

function unprocesate(orderId) {
    var state = 1;
    var data = "id=" + orderId + "&state=" + state;
    Swal.fire({
        title: '¿Estás seguro de que deseas revertir el procesamiento de este menú?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, revertir procesamiento',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "<?php echo constant('URL') ?>kitchen/editState", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                if (xhr.status === 200) {
                    console.log(xhr.responseText);
                    var row = document.getElementById("userRow" + orderId);
                    if (row) {
                        row.remove();
                    }
                    CargarContenido('views/kitchen/kitchen.php', 'content-wrapper');
                } else {
                    console.error('Error en la solicitud: ' + xhr.status);
                }
            };
            xhr.onerror = function () {
                console.error('Error de red al intentar realizar la solicitud.');
            };
            xhr.send(data);
        }
    });
}
</script>
</body>
</html>
