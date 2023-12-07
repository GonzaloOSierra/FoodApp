<?php
include_once "/var/www/html/models/usermodel.php";
include_once "/var/www/html/models/establishmodel.php";

$userModel = new UserModel();
$users = $userModel->getAll();

$establishModel = new EstablishModel();
$terminals = $establishModel->getAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario</title>
</head>
    <body>
        <div class="container">
            <h2 class="text-center mb-4">Asignar Caja</h2>
            <form method="post" action="<?php echo constant('URL') ?>crud_opening/createOpen" id="miFormulario">
                <div class="mb-3">
                    <label for="username" class="form-label">Empleado:</label>
                    <select class="form-select form-control" name="username" aria-label="Default select example">
                        <?php
                        foreach ($users as $user) {
                            echo '<option value="' . $user->getId() . '">' . $user->getUsername() . '</option>';
                        }
                        ?>
                     </select>
                </div>
                <div class="mb-3">
                    <label for="terminal" class="form-label">Terminal:</label>
                    <select class="form-select form-control" name="terminal" aria-label="Default select example">
                        <?php
                        foreach ($terminals as $term) {
                            echo '<option value="' . $term->getId_terminal() . '">' . $term->getId_terminal() . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                <div class="mb-3">
                    <label for="amount" class="form-label">Monto en efectivo:</label>
                    <input type="text" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="form-control"  id="amount" name="amount" required>
                </div>
                <div class="mb-3">
                    <label for="fecha">Selecciona fecha de inicio:</label>
                    <input type="datetime-local" id="start_date" name="start_date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="fecha">Selecciona fecha de cierre:</label>
                    <input type="datetime-local" id="final_date" name="final_date" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">Enviar</button>
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