<?php
include_once "/var/www/html/models/establishmodel.php";

$establishModel = new EstablishModel();
$terminals = $establishModel->getAll();

// Obtener el último ID de terminal
$ultimoIdTerminal = 0;
if (!empty($terminals)) {
    $ultimaTerminal = end($terminals);
    $ultimoIdTerminal = $ultimaTerminal->getId_terminal();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario</title>
</head>
<body>
    <div class="container text-center">
        <br>
        <br>
        <br>
        <br>

        <h2 class="mb-4">Adición de productos</h2>

        <!-- Mostrar el texto y el último ID de terminal -->
        <p>La próxima terminal creada será la número: <?php echo $ultimoIdTerminal + 1; ?></p>

        <form method="post" action="<?php echo constant('URL') ?>crud_terminal/createTerminal" id="miFormulario">
            <!-- Agrega un campo oculto para enviar el dato -->
            <input type="hidden" name="dato" value="valor_de_dato">

            <button type="submit" name="submit" class="btn btn-primary">Enviar</button>
        </form>
    </div>
</body>
</html>
<script>

    document.querySelector('form').addEventListener('submit', function(event) {
        event.preventDefault();

            const formulario = document.getElementById('miFormulario');

            formulario.addEventListener('submit', function (event) {
            event.preventDefault();

            const formData = new FormData(formulario);

            const xhr = new XMLHttpRequest();

            xhr.open('POST', '<?php echo constant('URL') ?>crud_opening/createOpen', true);

            xhr.onload = function () {
                if (xhr.status === 200) {

                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: 'Formulario enviado con éxito'
                }).then(() => {
                    formulario.reset();
                });
                } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al enviar el formulario',
                });
                }
            };

            xhr.send(formData);
            });

        }
    );
        
</script>