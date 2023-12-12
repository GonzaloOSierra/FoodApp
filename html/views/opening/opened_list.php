<?php
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

?>
<!DOCTYPE html>
<html>
<head>
    <title>Aberturas</title>
</head>
<body>
<div class="container">
        <h1 class="mt-6">Aberturas</h1>
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Terminal</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Cierre</th>
                    <th>Abertura</th>
                    <th>Cierre</th>
                    <th>Monto Asignado</th>
                    <th>Monto Final</th>
                </tr>
            </thead>
            <tbody>
        <?php foreach ($openers as $open): ?>
                <tr>
                    <td><?php echo $open->getId_open(); ?></td>
                    <td><?php echo $open->getName_employee(); ?></td>
                    <td><?php echo $open->getLast_name_employee(); ?></td>
                    <td><?php echo $open->getId_terminal(); ?></td>
                    <td><?php echo $open->getStart_date(); ?></td>
                    <td><?php echo $open->getFinal_date(); ?></td>
                    <td><?php echo $open->getDate_start_E(); ?></td>
                    <td><?php echo $open->getDate_Final_E(); ?></td>
                    <td><?php echo $open->getAmount(); ?></td>
                    <td><?php echo $open->getAmount_Final(); ?></td>
                    
                </tr>
        <?php endforeach; ?>
    </tbody>
    </table>
</div>


</script>
</body>
</html>
