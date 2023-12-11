<?php
session_start();

include_once "/var/www/html/models/openmodel.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$openModel = new OpenModel();

$openers = $openModel->getAll();

// Define una función de comparación para ordenar los ID
function cmp($b, $a) {
    return $a->getId_open() - $b->getId_open();
}

usort($openers, "cmp");


// Obtén las fechas límite del rango si están definidas
$fechaInicioRango = isset($_POST['fechaInicio']) ? $_POST['fechaInicio'] : null;
$fechaFinRango = isset($_POST['fechaFin']) ? $_POST['fechaFin'] : null;


?>
<!DOCTYPE html>
<html>
<head>
<title>Aberturas</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <style>
        /* Agrega estilos según sea necesario */
        body {
    background-color: #f4f6f9; /* o el color gris que prefieras */
}
    </style>
</head>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

<body>
<div class="container_fluid">
    <h1 class="mt-6">Aberturas</h1>


    
    <div class="row mt-3">
        <div class="col-md-3">
            <label for="fechaInicio" class="col-form-label">Fecha de Inicio:</label>
            <input type="date" id="fechaInicio" name="fechaInicio" class="form-control">
        </div>

        <div class="col-md-3">
            <label for="fechaFin" class="col-form-label">Fecha de Fin:</label>
            <input type="date" id="fechaFin" name="fechaFin" class="form-control">
        </div>

        <label for="employee_name">Nombre(Opcional):</label>
        <div class="col-md-3">

        <select name="employee_name" id="employee_name" class="form-select">
            <option value="">Seleccionar Nombre</option>
            <?php
            $addedNames = array(); // Array para almacenar nombres agregados

            foreach ($openers as $open):
                $fullName = $open->getName_employee() . ' ' . $open->getLast_name_employee();

                // Verificar si el nombre ya se agregó antes
                if (!in_array($fullName, $addedNames)):
                    ?>
                    <option value="<?php echo $fullName; ?>">
                        <?php echo $fullName; ?>
                    </option>
                    <?php
                    // Agregar el nombre a la lista de nombres agregados
                    $addedNames[] = $fullName;
                endif;
            endforeach;
            ?>
        </select>

        </div>
        </div>
            <button onclick="buscar()" class="btn btn-primary mt-4">Buscar</button>
        </div>
    
    <table class="table table-striped mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre completo</th>
                <th>Terminal</th>
                <th>Fecha Inicio</th>
                <th>Fecha Cierre</th>
                <th>Abertura</th>
                <th>Cierre</th>
                <th>Credito Asignado</th>
                <th>Credito Final</th>
                <th>Efectivo Asignado</th>
                <th>Efectivo Final</th>
                <th>QR Asignado</th>
                <th>QR Final</th>
                <th>Debito Asignado</th>
                <th>Debito Final</th>
            </tr>
        </thead>
        <tbody id="resultadoTabla">
            <?php foreach ($openers as $open): ?>
                <?php
                // Verifica si las fechas de inicio y fin están definidas
                $fechaActual = $open->getStart_date();
                if ((!$fechaInicioRango || $fechaActual >= $fechaInicioRango) && 
                    (!$fechaFinRango || $fechaActual <= $fechaFinRango)):
                ?>
                    <tr>
                        <td><?php echo $open->getId_open(); ?></td>
                        <td><?php echo $open->getName_employee() . ' ' . $open->getLast_name_employee(); ?></td>
                        <td><?php echo $open->getId_terminal(); ?></td>
                        <td><?php echo $open->getStart_date(); ?></td>
                        <td><?php echo $open->getFinal_date(); ?></td>
                        <td><?php echo $open->getDate_start_E(); ?></td>
                        <td><?php echo $open->getDate_Final_E(); ?></td>
                        <td><?php echo $open->getAmount_credit(); ?></td>
                        <td><?php echo $open->getAmount_credit_final(); ?></td>
                        <td><?php echo $open->getAmount_efective(); ?></td>
                        <td><?php echo $open->getAmount_efective_final(); ?></td>
                        <td><?php echo $open->getAmount_qr(); ?></td>
                        <td><?php echo $open->getAmount_qr_final(); ?></td>
                        <td><?php echo $open->getAmount_debito(); ?></td>
                        <td><?php echo $open->getAmount_debito_final(); ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


<script>
    function buscar() {
        var fechaInicio = document.getElementById("fechaInicio").value;
        var fechaFin = document.getElementById("fechaFin").value;
        var selectedName = $("#employee_name").val();

        $.ajax({
            type: 'POST',
            url: 'views/opening/searcher.php',
            data: {
                fechaInicio: fechaInicio,
                fechaFin: fechaFin,
                employee_name: selectedName
            },
            success: function(data) {
                $("#resultadoTabla").html(data);
                // Mostrar el botón de descarga solo si hay resultados
                if ($.trim(data).length > 0) {
                    $("button[name='downloadPDF']").show();
                } else {
                    $("button[name='downloadPDF']").hide();
                }
            }
        });
    }
</script>


</body>
</html>