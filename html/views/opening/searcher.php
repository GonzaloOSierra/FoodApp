<?php
include_once "/var/www/html/models/openmodel.php";

$openModel = new OpenModel();
$openers = $openModel->getAll();

$startDate = isset($_POST["fechaInicio"]) ? $_POST["fechaInicio"] : '';
$finalDate = isset($_POST["fechaFin"]) ? $_POST["fechaFin"] : '';
$selectedName = isset($_POST["employee_name"]) ? $_POST["employee_name"] : '';

// Crear un vector con los años en el rango especificado
$yearsInRange = range(date('Y', strtotime($startDate)), date('Y', strtotime($finalDate)));

// Bucle principal
foreach ($yearsInRange as $currentYear) {
    // Filtrar los registros del año actual
    $filteredOpeners = array_filter($openers, function ($open) use ($startDate, $finalDate, $selectedName, $currentYear) {
        $start = strtotime($open->getStart_date());

        $nameFilter = empty($selectedName) || $selectedName == $open->getName_employee() . ' ' . $open->getLast_name_employee();

        return ($start >= strtotime($startDate) && $start <= strtotime($finalDate) && $nameFilter && date('Y', $start) == $currentYear);
    });

    // Agrupa los resultados por semana y calcula el total por semana, por mes y por año
    $semanas = [];
    $totalesMesCredit = [];
    $totalesMesEfective = [];
    $totalesMesQr = [];
    $totalesMesDebito = [];
    $totalesAnioCredit = 0;
    $totalesAnioEfective = 0;
    $totalesAnioQr = 0;
    $totalesAnioDebito = 0;
    $totalAnio = 0;
    $anioAnterior = null;

    if (is_array($filteredOpeners)) {
        $numeroSemanaAnterior = null;

        $html = ''; // Inicializa la variable HTML

        foreach ($filteredOpeners as $open) {
            $fechaInicio = new DateTime($open->getStart_date());
            $numeroSemana = $fechaInicio->format('W');
            $mes = $fechaInicio->format('F');
            $anio = $fechaInicio->format('o');

            if (!isset($semanas[$numeroSemana])) {
                // Agrega una fila adicional al cambiar de semana con el total de la semana anterior
                if ($numeroSemanaAnterior !== null) {
                    $html .= "<tr class='table-secondary'>
                    <td colspan='2'>Semana {$numeroSemanaAnterior} - Mes: {$semanas[$numeroSemanaAnterior]['mes']} - Año: {$semanas[$numeroSemanaAnterior]['anio']} -</td>
                    <td colspan='2'>Total Semana (Credit): {$semanas[$numeroSemanaAnterior]['total_credit']} -</td>
                    <td colspan='2'>Total Semana (Efective): {$semanas[$numeroSemanaAnterior]['total_efective']} -</td>
                    <td colspan='2'>Total Semana (QR): {$semanas[$numeroSemanaAnterior]['total_qr']} -</td>
                    <td colspan='2'>Total Semana (Debito): {$semanas[$numeroSemanaAnterior]['total_debito']}</td>
                    <td colspan='2'></td>
                    <td colspan='2'></td>
                    <td colspan='1'></td>
                    </tr>";
                }

                // Agrega una fila de encabezado para indicar el año actual
                if ($anio != $anioAnterior) {
                    if (!empty($html)) {
                        $html .= "</table>"; // Cierra la tabla si hay datos anteriores
                        $html .= "</div>";
                        echo $html;
                    }

                    $html = "<div class='year-results'>";
                    $html .= "<table class='table container-fluid'>"; // Agrega la clase container-fluid al formulario
                    $html .= "<tr class='table-secondary'><td colspan='15'>Año: $anio</td></tr>";
                    $anioAnterior = $anio;
                }

                $semanas[$numeroSemana] = [
                    'resultados' => [],
                    'total_credit' => 0,
                    'total_efective' => 0,
                    'total_qr' => 0,
                    'total_debito' => 0,
                    'mes' => $mes,
                    'anio' => $anio,
                ];
            }

            if (!isset($totalesMesCredit[$mes])) {
                $totalesMesCredit[$mes] = 0;
                $totalesMesEfective[$mes] = 0;
                $totalesMesQr[$mes] = 0;
                $totalesMesDebito[$mes] = 0;
            }

            if (!isset($totalesAnioCredit)) {
                $totalesAnioCredit = 0;
                $totalesAnioEfective = 0;
                $totalesAnioQr = 0;
                $totalesAnioDebito = 0;
            }

            $semanas[$numeroSemana]['resultados'][] = $open;
            $semanas[$numeroSemana]['total_credit'] += $open->getAmount_credit_final();
            $semanas[$numeroSemana]['total_efective'] += $open->getAmount_efective_final();
            $semanas[$numeroSemana]['total_qr'] += $open->getAmount_qr_final();
            $semanas[$numeroSemana]['total_debito'] += $open->getAmount_debito_final();

            // Calcula el total del mes
            $totalesMesCredit[$mes] += $open->getAmount_credit_final();
            $totalesMesEfective[$mes] += $open->getAmount_efective_final();
            $totalesMesQr[$mes] += $open->getAmount_qr_final();
            $totalesMesDebito[$mes] += $open->getAmount_debito_final();

            // Calcula el total del año
            $totalesAnioCredit += $open->getAmount_credit_final();
            $totalesAnioEfective += $open->getAmount_efective_final();
            $totalesAnioQr += $open->getAmount_qr_final();
            $totalesAnioDebito += $open->getAmount_debito_final();
            $totalAnio += $open->getAmount_credit_final();

            // Actualiza la semana anterior
            $numeroSemanaAnterior = $numeroSemana;

            $html .= "<tr>
            <td>{$open->getId_open()}</td>
            <td>{$open->getName_employee()} {$open->getLast_name_employee()}</td>
            <td>{$open->getId_terminal()}</td>
            <td>{$open->getStart_date()}</td>
            <td>{$open->getFinal_date()}</td>
            <td>{$open->getDate_start_E()}</td>
            <td>{$open->getDate_Final_E()}</td>
            <td>{$open->getAmount_credit()}</td>
            <td>{$open->getAmount_credit_final()}</td>
            <td>{$open->getAmount_efective()}</td>
            <td>{$open->getAmount_efective_final()}</td>
            <td>{$open->getAmount_qr()}</td>
            <td>{$open->getAmount_qr_final()}</td>
            <td>{$open->getAmount_debito()}</td>
            <td>{$open->getAmount_debito_final()}</td>
        </tr>";
        }
// Agrega la última fila adicional con el total de la última semana
if ($numeroSemanaAnterior !== null) {
    $html .= "<tr class='table-secondary responsive'>";
    $html .= "<td colspan='2'>Semana {$numeroSemanaAnterior}</td>";
    $html .= "<td colspan='2'>Mes: {$semanas[$numeroSemanaAnterior]['mes']}</td>";
    $html .= "<td colspan='2'>Año: {$semanas[$numeroSemanaAnterior]['anio']}</td>";
    $html .= "<td colspan='2'>Total Semana (Credit): {$semanas[$numeroSemanaAnterior]['total_credit']}</td>";
    $html .= "<td colspan='2'>Total Semana (Efective): {$semanas[$numeroSemanaAnterior]['total_efective']}</td>";
    $html .= "<td colspan='2'>Total Semana (QR): {$semanas[$numeroSemanaAnterior]['total_qr']}</td>";
    $html .= "<td colspan='2'>Total Semana (Debito): {$semanas[$numeroSemanaAnterior]['total_debito']}</td>";
    $html .= "<td colspan='1'></td>";
    $html .= "</tr>";
}


        // Cierra la tabla si hay datos al final del bucle
        if (!empty($html)) {
            $html .= "</table>";
            $html .= "</div>";
            echo $html;
        }
    } else {
        echo "No hay resultados.";
    }

    // Ordena el array de totalesMesCredit de mayor a menor según el total
    arsort($totalesMesCredit);

// ...

// Muestra el total del mes y del año al final del resultado
echo "<div class='year-results'>";
echo "<table>";
echo "<tr class='table-secondary'>";

$numCols = 0; // Nueva variable para realizar un seguimiento del número de columnas

foreach ($totalesMesCredit as $mes => $total) {
    echo "<td colspan='2'>Total de $mes (Credit): $total - </td>";
    $numCols += 2;

    // Si se supera el límite de columnas, cierra la fila actual y comienza una nueva
    if ($numCols >= 15) {
        echo "</tr><tr class='table-secondary'>";
        $numCols = 0;
    }
}

// Repite el proceso para otras categorías
foreach ($totalesMesEfective as $mes => $total) {
    echo "<td colspan='2'>Total de $mes (Efective): $total - </td>";
    $numCols += 2;
    if ($numCols >= 15) {
        echo "</tr><tr class='table-secondary'>";
        $numCols = 0;
    }
}

foreach ($totalesMesQr as $mes => $total) {
    echo "<td colspan='2'>Total de $mes (QR): $total - </td>";
    $numCols += 2;
    if ($numCols >= 15) {
        echo "</tr><tr class='table-secondary'>";
        $numCols = 0;
    }
}

foreach ($totalesMesDebito as $mes => $total) {
    echo "<td colspan='2'>Total de $mes (Debito): $total - </td>";
    $numCols += 2;
    if ($numCols >= 15) {
        echo "</tr><tr class='table-secondary'>";
        $numCols = 0;
    }
}

// Añade celdas vacías hasta alcanzar 15
while ($numCols < 15) {
    echo "<td colspan='2'></td>";
    $numCols += 2;
}

// Cierra la última fila
echo "</tr>";
// ...


// Muestra los totales por año
echo "<tr class='table-secondary responsive'>";
echo "<td colspan='2'>Total del Año $anioAnterior (Credit):</td>";
echo "<td colspan='2'>$totalesAnioCredit</td>";
echo "<td colspan='2'>Total del Año $anioAnterior (Efective):</td>";
echo "<td colspan='2'>$totalesAnioEfective</td>";
echo "<td colspan='2'>Total del Año $anioAnterior (QR):</td>";
echo "<td colspan='2'>$totalesAnioQr</td>";
echo "<td colspan='2'>Total del Año $anioAnterior (Debito):</td>";
echo "<td colspan='2'>$totalesAnioDebito</td>";
echo "</tr>";
echo "</table>";
echo "</div>";

}
?>
