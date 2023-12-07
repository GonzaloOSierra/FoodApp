<?php
session_start();

include_once "/var/www/html/models/usermodel.php";
include_once "/var/www/html/models/openmodel.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$userModel = new UserModel();


?>


<?php



include_once "/var/www/html/clases/session.php";

$session = new Session();

// Verifica si existe una sesión y obtén el ID del usuario
if ($session->exists()) {
    $userId = $session->getCurrentUserId();
} else {
    echo "No hay sesión iniciada.";
}

$openModelInstance = new OpenModel();

try {
    $items = $openModelInstance->getAll();
    $lastItem = null;

    // Verificamos si hay datos
    if (!empty($items)) {
        // Recorremos los resultados con un bucle foreach
        foreach ($items as $item) {
            // Verificamos si el id_employee coincide con userId
            if ($item->getId_employee() == $userId) {
                // Almacenamos el último registro correspondiente a userId
                $lastItem = $item;
            }
        }

    } else {
        echo "No se encontraron datos.";
    }
} catch (PDOException $e) {
    echo "Error al obtener datos: " . $e->getMessage();
}

$idOpen = $lastItem->getId_open();

date_default_timezone_set('America/Buenos_Aires'); // Reemplaza con la zona horaria de tu ubicación
$fechaActual = new DateTime();
$f_actual_str = $fechaActual->format('Y-m-d H:i:s');


$state = 3;


try {
    $usuarios = $userModel->getAll();

    foreach ($usuarios as $usuario) {
        // Verificamos si el usuario actual tiene id_employee igual a userId y state igual a 1
        if ($usuario->getId() == $userId && $usuario->getState() == 1) {         
            $state = 1;
        }
    }
} catch (PDOException $e) {
    echo "Error al obtener datos: " . $e->getMessage();
}


?>


<form>

<!-- Muestra los botones de apertura y cierre del empleado. -->


<div class="text-center">
    <?php
    if ($state == 1 && 
        $lastItem->getStart_date() < $f_actual_str && 
        $f_actual_str < $lastItem->getFinal_date() && 
        $lastItem->getOpened() == 0) {
            echo '<button id="openedBtn' . $usuario->getId() . '" class="btn btn-success d-block mx-auto mb-2" type="button" onclick="confirmOpened(' . $usuario->getId() . ', ' . $lastItem->getId_open() . ', \'' . $f_actual_str . '\')">APERTURA DE CAJA</button>';
    }

    if($state == 1 && 
        $lastItem->getStart_date() < $f_actual_str && 
        $f_actual_str < $lastItem->getFinal_date() && 
        $lastItem->getOpened() == 1) {
            echo '<button id="CloseBtn' . $usuario->getId() . '" class="btn btn-danger d-block mx-auto" type="button" onclick="confirmClosed(' . $usuario->getId() . ', ' . $lastItem->getId_open() . ', \'' . $f_actual_str . '\')">CIERRE DE CAJA</button>';
    
    ?>
</div>


<!-- Grabala idOpen como variable global.-->

<script>

    var idOpen = <?php echo $idOpen; ?>;
    realizarVenta();

</script>





<!-- Content Header (Page header) -->
<div class="content-header">
<div class="container-fluid">

    <div class="row mb-2">

        <div class="col-sm-6">

            <h2 class="m-0">Punto de Venta</h2>

        </div><!-- /.col -->

        <div class="col-sm-6">

            <ol class="breadcrumb float-sm-right">

                <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>

                <li class="breadcrumb-item active">Ventas</li>

            </ol>

        </div><!-- /.col -->

    </div><!-- /.row -->

</div><!-- /.container-fluid -->

</div>
<!-- /.content-header -->

<!-- Main content -->
<div class="content">

<div class="container-fluid">

    <div class="row mb-3">

        <div class="col-md-9">

            <div class="card card-gray shadow">

                <div class="card-body p-3">

                    <div class="row">
                        <!-- INPUT PARA INGRESO DEL CODIGO DE BARRAS O DESCRIPCION DEL PRODUCTO -->
                        <div class="col-md-12 mb-3">

                            <div class="form-group mb-2">

                                <label class="col-form-label" for="iptCodigoVenta">
                                    <i class="fas fa-barcode fs-6"></i>
                                    <span class="small">Menus</span>
                                </label>

                                <input type="text" class="form-control form-control-sm" id="iptCodigoVenta" placeholder="Ingrese nombre del menu a buscar">
                            </div>

                        </div>

                        <!-- ETIQUETA QUE MUESTRA LA SUMA TOTAL DE LOS PRODUCTOS AGREGADOS AL LISTADO -->
                        <div class="col-md-7 mb-3 rounded-3" style="background-color: gray;color: white;text-align:center;border:1px solid gray;">
                            <h2 class="fw-bold m-0">$ <span class="fw-bold" id="totalVenta">0.00</span></h2>
                        </div>

                        <!-- BOTONES PARA VACIAR LISTADO Y COMPLETAR LA VENTA -->
                        <div class="col-md-5 text-right">
                            <button class="btn btn-primary" id="btnIniciarVenta">
                                <i class="fas fa-shopping-cart"></i> Realizar Venta
                            </button>
                            <button class="btn btn-danger" id="btnVaciarListado">
                                <i class="far fa-trash-alt"></i> Vaciar Listado
                            </button>
                        </div>

                        <!-- LISTADO QUE CONTIENE LOS PRODUCTOS QUE SE VAN AGREGANDO PARA LA COMPRA -->
                        <div class="col-md-12">

                            <table id="lstProductosVenta" class="display nowrap table-striped w-100 shadow ">
                                <thead class="bg-gray text-left fs-6">
                                    <tr>
                                         <th>Id</th>
                                        <th>Menu ID</th> 
                                        <th>Menu</th>
                                        <th>Categoria</th>
                                        <th>Detalle</th>
                                        <th>cantidad</th>
                                        <th>precio</th>
                                        <th>Total</th>
                                        <th class="text-center">Opciones</th>
                                     
                                    </tr>
                                </thead>
                                <tbody class="small text-left fs-6">
                                </tbody>
                            </table>
                            <!-- / table -->
                        </div>
                        <!-- /.col -->
                    </div>
                </div> <!-- ./ end card-body -->
            </div>

        </div>

        <div class="col-md-3">

            <div class="card card-gray shadow">

              

                <div class="card-body p-2">


                    <!-- SELECCIONAR TIPO DE PAGO -->
                    <div class="form-group mb-2">

                        <label class="col-form-label p-0" for="selCategoriaReg">
                            <i class="fas fa-money-bill-alt fs-6"></i>
                            <span class="small">Tipo Pago</span><span class="text-danger">*</span>
                        </label>

                        <select class="form-select form-select-sm" aria-label=".form-select-sm example" id="selTipoPago">
                            <option value="0">Seleccione Tipo Pago</option>
                            <option value="1" selected="true">Efectivo</option>
                            <option value="2">Debito</option>
                            <option value="3">QR</option>
                            <option value="4">Transferencia</option>
                        </select>

                        <span id="validate_categoria" class="text-danger small fst-italic" style="display:none">
                            Debe Ingresar tipo de pago
                        </span>

                    </div>

                    <!-- INPUT DE EFECTIVO ENTREGADO -->
                    <div class="form-group">
                        <label for="iptEfectivoRecibido" class="p-0 m-0">Efectivo recibido</label>
                        <input type="number" min="0" name="iptEfectivo" id="iptEfectivoRecibido" class="form-control form-control-sm" placeholder="Cantidad de efectivo recibida">
                    </div>

                    <!-- INPUT CHECK DE EFECTIVO EXACTO -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="chkEfectivoExacto">
                        <label class="form-check-label" for="chkEfectivoExacto">
                            Efectivo Exacto
                        </label>
                    </div>

                    <!-- MOSTRAR MONTO EFECTIVO ENTREGADO Y EL VUELTO -->
                    <div class="row mt-2">

                        <div class="col-12">
                            <h6 class="text-start fw-bold">Monto Efectivo: $ <span id="EfectivoEntregado">0.00</span></h6>
                        </div>

                        <div class="col-12">
                            <h6 class="text-start text-danger fw-bold">Vuelto: $ <span id="Vuelto">0.00</span>
                            </h6>
                        </div>

                    </div>

                    <!-- MOSTRAR EL SUBTOTAL, IGV Y TOTAL DE LA VENTA -->
                    <div class="row fw-bold">

                        <div class="col-md-7">
                            <span>SUBTOTAL</span>
                        </div>
                        <div class="col-md-5 text-right">
                            $ <span class="" id="boleta_subtotal">0.00</span>
                        </div>

                        <div class="col-md-7">
                            <span>TOTAL</span>
                        </div>
                        <div class="col-md-5 text-right">
                            $ <span class="" id="boleta_total">0.00</span>
                        </div>
                    </div>

                </div><!-- ./ CARD BODY -->

            </div><!-- ./ CARD -->
        </div>

    </div>
</div>

</div>

<script>
var table;
var items = []; // SE USA PARA EL INPUT DE AUTOCOMPLETE
var itemProducto = 1;

var Toast = Swal.mixin({
    toast: true,
    position: 'top',
    showConfirmButton: false,
    timer: 3000
});

$(document).ready(function() {

    /* ======================================================================================
    TRAER EL NRO DE BOLETA
    ======================================================================================*/
    CargarNroBoleta();

    /* ======================================================================================
    EVENTO PARA VACIAR EL CARRITO DE COMPRAS
    =========================================================================================*/
    $("#btnVaciarListado").on('click', function() {
        vaciarListado();
    })

    /* ======================================================================================
    INICIALIZAR LA TABLA DE VENTAS
    ======================================================================================*/
    table = $('#lstProductosVenta').DataTable({
        "columns": [
            {
                "data": "id"
            },
            {
                "data": "id_menu"
            },
            {
                "data": "name_menu"
            },
            {
                "data": "categoria_name"
            },
            {
                "data": "detalle"
            },
            {
                "data": "cantidad"
            },
            {
                "data": "precio"
            },
            {
                "data": "total"
            },
            {
                "data": "acciones"
            },
            
        ],
        columnDefs: [
            {
                targets: 0,
                visible: false
            },
            {
                targets: 1,
                visible: false
            },
           
            {
                targets: 4,
                visible: false
            },
            {
                targets: 6,
                visible: false
            },
          
        ],
        "order": [
            [0, 'desc']
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-in$1.10.20/i18n/Spanish.json"
        }
    });

    /* ======================================================================================
    TRAER LISTADO DE PRODUCTOS PARA INPUT DE AUTOCOMPLETADO
    ======================================================================================*/
    
    $.ajax({
        async: false,
        url: "ajax/salesajax.php",
        method: "POST",
        data: {
            'action': 6
        },
        dataType: 'json',

        // console.log(respuesta);
        success: function(respuesta) {

            console.log(respuesta);
            for (let i = 0; i < respuesta.length; i++) {
                items.push(respuesta[i]['descripcion_producto'])
            }

            $("#iptCodigoVenta").autocomplete({

                source: items,
                select: function(event, ui) {

                    CargarProductos(ui.item.value);

                    $("#iptCodigoVenta").val("");

                    $("#iptCodigoVenta").focus();

                    return false;
                }
            })

        },error: function(jqXHR, textStatus, errorThrown) {
                // Maneja el error aquí
                console.log("Error en la solicitud AJAX: " + textStatus, errorThrown);
                // alert("Error al cargar el formulario de edición.");
            }
    });


    /* ======================================================================================
    EVENTO QUE REGISTRA EL PRODUCTO EN EL LISTADO CUANDO SE INGRESA EL CODIGO DE BARRAS
    ======================================================================================*/
    $("#iptCodigoVenta").change(function() {
        CargarProductos();
    });

    /* ======================================================================================
    EVENTO PARA ELIMINAR UN PRODUCTO DEL LISTADO
    ======================================================================================*/
    $('#lstProductosVenta tbody').on('click', '.btnEliminarproducto', function() {
        table.row($(this).parents('tr')).remove().draw();
        recalcularTotales();
    });

    /* ======================================================================================
    EVENTO PARA AUMENTAR LA CANTIDAD DE UN PRODUCTO DEL LISTADO
    ====================================================================================== */
    $('#lstProductosVenta tbody').on('click', '.btnAumentarCantidad', function() {

        var data = table.row($(this).parents('tr')).data(); //Recuperar los datos de la fila

        var idx = table.row($(this).parents('tr')).index(); // Recuperar el Indice de la Fila

        var codigo_producto = data['id_menu'];
        var cantidad = data['cantidad'];

        $.ajax({
            async: false,
            url: "ajax/salesajax.php",
            method: "POST",
            data: {
                'action': 8,
                'id_menu': codigo_producto,
                'cantidad': cantidad
            },

            dataType: 'json',
            success: function(respuesta) {
                console.log(respuesta);
                if (parseInt(respuesta['existe']) == 0) {

                    Toast.fire({
                        icon: 'error',
                        title: ' El producto ' + data['name_menu'] + ' ya no tiene stock'
                    })

                    $("#iptCodigoVenta").val("");
                    $("#iptCodigoVenta").focus();

                } else {

                    cantidad = parseInt(data['cantidad']) + 1;

                    table.cell(idx, 5).data(cantidad + ' Und(s)').draw();

                    NuevoPrecio = (parseInt(data['cantidad']) * data['precio'].replace("$ ", "")).toFixed(2);
                    NuevoPrecio = "$ " + NuevoPrecio;

                    table.cell(idx, 7).data(NuevoPrecio).draw();

                    recalcularTotales();
                }
            }
        });

    });

    /* ======================================================================================
    EVENTO PARA DESMINUIR LA CANTIDAD DE UN PRODUCTO DEL LISTADO
    ======================================================================================*/
    $('#lstProductosVenta tbody').on('click', '.btnDisminuirCantidad', function() {

        var data = table.row($(this).parents('tr')).data();

        if (data['cantidad'].replace('Und(s)', '') >= 2) {

            cantidad = parseInt(data['cantidad'].replace('Und(s)', '')) - 1;

            var idx = table.row($(this).parents('tr')).index();

            table.cell(idx, 5).data(cantidad + ' Und(s)').draw();

            NuevoPrecio = (parseInt(data['cantidad']) * data['precio'].replace("$ ", "")).toFixed(2);
            NuevoPrecio = "$ " + NuevoPrecio;

            table.cell(idx, 7).data(NuevoPrecio).draw();

        }

        recalcularTotales();
    });

    /* ======================================================================================
    EVENTO PARA INGRESAR EL PESO DEL PRODUCTO
    ====================================================================================== */
    $('#lstProductosVenta tbody').on('click', '.btnIngresarPeso', function() {

        var data = table.row($(this).parents('tr')).data();

        Swal.fire({
            title: "",
            text: "Peso del Producto (Grms):",
            input: 'text',
            width: 300,
            confirmButtonText: 'Aceptar',
            showCancelButton: true,
        }).then((result) => {

            if (result.value) {

                cantidad = result.value;

                var idx = table.row($(this).parents('tr')).index();

                table.cell(idx, 5).data(cantidad + ' Kg(s)').draw();

                NuevoPrecio = ((parseFloat(data['cantidad']) * data['precio'].replace("$ ", "")).toFixed(2));
                NuevoPrecio = "$ " + NuevoPrecio;

                table.cell(idx, 7).data(NuevoPrecio).draw();

                recalcularTotales();

            }

        });


    });

    /* ======================================================================================
    EVENTO PARA MODIFICAR EL PRECIO DE VENTA DEL PRODUCTO
    ======================================================================================*/
    $('#lstProductosVenta tbody').on('click', '.dropdown-item', function() {

        codigo_producto = $(this).attr("codigo");
        console.log("🚀 ~ file: ventas.php:527 ~ $ ~ codigo_producto", codigo_producto)
        precio_venta = parseFloat($(this).attr("precio").replaceAll("$ ", "")).toFixed(2);

        recalcularMontos(codigo_producto, precio_venta);
    });


    /* ======================================================================================
    EVENTO PARA MODIFICAR LA CANTIDAD DE PRODUCTOS A COMPRAR
    ======================================================================================*/
    $('#lstProductosVenta tbody').on('change', '.iptCantidad', function() {

        var data = table.row($(this).parents('tr')).data();

        cantidad_actual = $(this)[0]['value'];
        cod_producto_actual = $(this)[0]['attributes'][2]['value'];

        console.log("cantidad", $(this)[0]['value'])
        console.log("codigo Producto", $(this)[0]['attributes'][2]['value'])

        if (!$.isNumeric($(this)[0]['value']) || $(this)[0]['value'] <= 0) {

            mensajeToast('error', 'INGRESE UN VALOR NUMERICO Y MAYOR A 0');

            $(this)[0]['value'] = "1";

            $("#iptCodigoVenta").val("");
            $("#iptCodigoVenta").focus();
            return;
        }

        console.log(cantidad_actual)

        table.rows().eq(0).each(function(index) {

            var row = table.row(index);

            var data = row.data();

            if (data['id_menu'] == cod_producto_actual) {

                $.ajax({
                    async: false,
                    url: "ajax/salesajax.php",
                    method: "POST",
                    data: {
                        'action': 8,
                        'id_menu': cod_producto_actual,
                        'cantidad': cantidad_actual
                    },
                    dataType: 'json',
                    success: function(respuesta) {

                        if (parseInt(respuesta['existe']) == 0) {

                            mensajeToast('error', ' El producto ' + data['name'] + ' ya no tiene stock');

                            table.cell(index, 5).data('<input type="text" style="width:80px;" codigoProducto = "' + cod_producto_actual + '" class="form-control text-center iptCantidad m-0 p-0" value="1">').draw();

                            $("#iptCodigoVenta").val("");
                            $("#iptCodigoVenta").focus();

                            // ACTUALIZAR EL NUEVO PRECIO DEL ITEM DEL LISTADO DE VENTA
                            NuevoPrecio = (parseFloat(1) * data['precio'].replaceAll("$", "")).toFixed(2);
                            NuevoPrecio = "$ " + NuevoPrecio;
                            table.cell(index, 7).data(NuevoPrecio).draw();

                            // RECALCULAMOS TOTALES
                            recalcularTotales();

                        } else {

                            // AUMENTAR EN 1 EL VALOR DE LA CANTIDAD
                            table.cell(index, 5).data('<input type="text" style="width:80px;" codigoProducto = "' + cod_producto_actual + '" class="form-control text-center iptCantidad m-0 p-0" value="' + cantidad_actual + '">').draw();


                            // ACTUALIZAR EL NUEVO PRECIO DEL ITEM DEL LISTADO DE VENTA
                            NuevoPrecio = (parseFloat(cantidad_actual) * data['precio'].replaceAll("$ ", "")).toFixed(2);
                            NuevoPrecio = "$ " + NuevoPrecio;
                            table.cell(index, 7).data(NuevoPrecio).draw();

                            // RECALCULAMOS TOTALES
                            recalcularTotales();
                        }
                    }
                });



            }


        });

    });


    /* =======================================================================================
    EVENTO QUE PERMITE CHECKEAR EL EFECTIVO CUANDO ES EXACTO
    =========================================================================================*/
    $("#chkEfectivoExacto").change(function() {

        if ($("#chkEfectivoExacto").is(':checked')) {

            var vuelto = 0;
            var totalVenta = $("#totalVenta").html();

            $("#iptEfectivoRecibido").val(totalVenta);

            $("#EfectivoEntregado").html(totalVenta);

            var EfectivoRecibido = parseFloat($("#EfectivoEntregado").html().replace("$ ", ""));

            vuelto = parseFloat(totalVenta) - parseFloat(EfectivoRecibido);

            $("#Vuelto").html(vuelto.toFixed(2));

        } else {

            $("#iptEfectivoRecibido").val("")
            $("#EfectivoEntregado").html("0.00");
            $("#Vuelto").html("0.00");

        }
    })

    /* ======================================================================================
    EVENTO QUE SE DISPARA AL DIGITAR EL MONTO EN EFECTIVO ENTREGADO POR EL CLIENTE
    =========================================================================================*/
    $("#iptEfectivoRecibido").keyup(function() {
        actualizarVuelto();
    });

    /* ======================================================================================
    EVENTO PARA INICIAR EL REGISTRO DE LA VENTA
    ====================================================================================== */
    $("#btnIniciarVenta").on('click', function() {
        realizarVenta();
    })


}) //FIN DOCUMENT READY

/*===================================================================*/
//FUNCION PARA CARGAR EL NRO DE BOLETA
/*===================================================================*/
function CargarNroBoleta() {

    $.ajax({
        async: false,
        url: "ajax/salesajax.php",
        method: "POST",
        data: {
            'action': 1
        },
        dataType: 'json',
        success: function(respuesta) {

            serie_boleta = respuesta["serie_boleta"];
            nro_boleta = respuesta["nro_venta"];

            $("#iptNroSerie").val(serie_boleta);
            $("#iptNroVenta").val(nro_boleta);
        }
    });
}

/*===================================================================*/
//FUNCION PARA LIMPIAR TOTALMENTE EL CARRITO DE VENTAS
/*===================================================================*/
function vaciarListado() {
    table.clear().draw();
    LimpiarInputs();
}

/*===================================================================*/
//FUNCION PARA LIMPIAR LOS INPUTS DE LA BOLETA Y LABELS QUE TIENEN DATOS
/*===================================================================*/
function LimpiarInputs() {
    $("#totalVenta").html("0.00");
    $("#totalVentaRegistrar").html("0.00");
    $("#boleta_total").html("0.00");
    $("#iptEfectivoRecibido").val("");
    $("#EfectivoEntregado").html("0.00");
    $("#Vuelto").html("0.00");
    $("#chkEfectivoExacto").prop('checked', false);
    $("#boleta_subtotal").html("0.00");
    $("#boleta_igv").html("0.00")
} /* FIN LimpiarInputs */

/*===================================================================*/
//FUNCION PARA ACTUALIZAR EL VUELTO
/*===================================================================*/
function actualizarVuelto() {

    var totalVenta = $("#totalVenta").html();

    $("#chkEfectivoExacto").prop('checked', false);

    var efectivoRecibido = $("#iptEfectivoRecibido").val();

    if (efectivoRecibido > 0) {

        $("#EfectivoEntregado").html(parseFloat(efectivoRecibido).toFixed(2));

        vuelto = parseFloat(efectivoRecibido) - parseFloat(totalVenta);

        $("#Vuelto").html(vuelto.toFixed(2));

    } else {

        $("#EfectivoEntregado").html("0.00");
        $("#Vuelto").html("0.00");

    }
}


function recalcularMontos(codigo_producto, precio_venta) {

    table.rows().eq(0).each(function(index) {

        var row = table.row(index);

        var data = row.data();


        if (data['id_menu'] == codigo_producto) {

            // AUMENTAR EN 1 EL VALOR DE LA CANTIDAD
            table.cell(index, 6).data("S./ " + parseFloat(precio_venta).toFixed(2)).draw();

            // cantidad_actual = 
            console.log("🚀 ~ file: ventas.php:744 ~ table.rows ~ data", parseFloat($.parseHTML(data['cantidad'])[0]['value']))
            cantidad_actual = parseFloat($.parseHTML(data['cantidad'])[0]['value']);

            // ACTUALIZAR EL NUEVO PRECIO DEL ITEM DEL LISTADO DE VENTA
            NuevoPrecio = (parseFloat(cantidad_actual) * data['precio_venta_producto'].replaceAll("S./ ", "")).toFixed(2);
            NuevoPrecio = "S./ " + NuevoPrecio;
            table.cell(index, 7).data(NuevoPrecio).draw();

        }


    });

    // RECALCULAMOS TOTALES
    recalcularTotales();

}

/*===================================================================*/
//FUNCION PARA RECALCULAR LOS TOTALES DE VENTA
/*===================================================================*/
function recalcularTotales() {

    var TotalVenta = 0.00;

    table.rows().eq(0).each(function(index) {

        var row = table.row(index);
        var data = row.data();

        TotalVenta = parseFloat(TotalVenta) + parseFloat(data['total'].replace("$ ", ""));

    });

    $("#totalVenta").html("");
    $("#totalVenta").html(TotalVenta.toFixed(2));

    var totalVenta = $("#totalVenta").html();
    // var igv = parseFloat(totalVenta) * 0.18
    // var subtotal = parseFloat(totalVenta) - parseFloat(igv);

    $("#totalVentaRegistrar").html(totalVenta);

    // $("#boleta_subtotal").html(parseFloat(subtotal).toFixed(2));
    // $("#boleta_igv").html(parseFloat(igv).toFixed(2));
    $("#boleta_total").html(parseFloat(totalVenta).toFixed(2));

    //limpiamos el input de efectivo exacto; desmarcamos el check de efectivo exacto
    //borramos los datos de efectivo entregado y vuelto
    $("#iptEfectivoRecibido").val("");
    $("#chkEfectivoExacto").prop('checked', false);
    $("#EfectivoEntregado").html("0.00");
    $("#Vuelto").html("0.00");

    $("#iptCodigoVenta").val("");
    $("#iptCodigoVenta").focus();
}


/*===================================================================*/
//FUNCION PARA CARGAR PRODUCTOS EN EL DATATABLE
/*===================================================================*/
function CargarProductos(producto = "") {

    if (producto != "") {
        var codigo_producto = producto;
        

    } else {
        var codigo_producto = $("#iptCodigoVenta").val();
    }

    codigo_producto = $.trim(codigo_producto.split('/')[0]);
  

    var producto_repetido = 0;

    /*===================================================================*/
    // AUMENTAMOS LA CANTIDAD SI EL PRODUCTO YA EXISTE EN EL LISTADO
    /*===================================================================*/
    table.rows().eq(0).each(function(index) {

        var row = table.row(index);
        var data = row.data();
        console.log("🚀 ~ file: ventas.php:829 ~ table.rows ~ data", $.parseHTML(data['cantidad'])[0]['value'])
        
        if (codigo_producto == data['id_menu']) {

            producto_repetido = 1;

            
            cantidad_a_comprar = parseFloat($.parseHTML(data['cantidad'])[0]['value']) + 1;
            console.log('cantidad aqui: '+cantidad_a_comprar);
            $.ajax({
                async: false,
                url: "ajax/salesajax.php",
                method: "POST",
                data: {
                    'action': 8,
                    'id_menu': codigo_producto,
                    'cantidad': cantidad_a_comprar
                },
                dataType: 'json',
                success: function(respuesta) {
                    console.log(respuesta);
                    if (parseInt(respuesta['existe']) == 0) {

                        mensajeToast('error', ' El producto ' + data['name_menu'] + ' ya no tiene stock');

                        $("#iptCodigoVenta").val("");
                        $("#iptCodigoVenta").focus();

                    } else {

                        // AUMENTAR EN 1 EL VALOR DE LA CANTIDAD
                        table.cell(index, 5).data('<input type="text" style="width:80px;" codigoProducto = "' + codigo_producto + '" class="form-control text-center iptCantidad m-0 p-0" value="' + cantidad_a_comprar + '">').draw();


                        // ACTUALIZAR EL NUEVO PRECIO DEL ITEM DEL LISTADO DE VENTA
                        NuevoPrecio = (parseFloat(cantidad_a_comprar) * data['precio'].replaceAll("$ ", "")).toFixed(2);
                        NuevoPrecio = "$ " + NuevoPrecio;
                        table.cell(index, 7).data(NuevoPrecio).draw();

                        // RECALCULAMOS TOTALES
                        recalcularTotales();
                    }
                }
            });

        }
    });

    

    if (producto_repetido == 1) {
        return;
    }

    console.log(codigo_producto);

    $.ajax({
        url: "ajax/salesajax.php",
        method: "POST",
        data: {
            'action': 7, //BUSCAR PRODUCTOS POR SU CODIGO DE BARRAS
            'id_menu': codigo_producto
        },
        dataType: 'json',
        success: function(respuesta) {

            console.log(respuesta);
            /*===================================================================*/
            //SI LA RESPUESTA ES VERDADERO, TRAE ALGUN DATO
            /*===================================================================*/
            if (respuesta) {

                var TotalVenta = 0.00;

                table.row.add({
                    'id': itemProducto,
                    'id_menu': respuesta['id_menu'],
                    'name_menu': respuesta['name_menu'],
                    'categoria_name': respuesta['categoria_name'],
                    'precio': respuesta['precio'],
                    'detalle': respuesta['detalle'],
                  
                    'cantidad': '<input type="text" style="width:80px;" codigoProducto = "' + respuesta['id_menu'] + '" class="form-control text-center iptCantidad p-0 m-0" value="1">',
                
                    'total': respuesta['total'],
                    'acciones': "<center>" +
                        "<span class='btnEliminarproducto text-danger px-1'style='cursor:pointer;' data-bs-toggle='tooltip' data-bs-placement='top' title='Eliminar producto'> " +
                        "<i class='fas fa-trash fs-5'> </i> " +
                        "</span>" 
                        
                }).draw();

                itemProducto = itemProducto + 1;

                //  Recalculamos el total de la venta
                recalcularTotales();

                /*===================================================================*/
                //SI LA RESPUESTA ES FALSO, NO TRAE ALGUN DATO
                /*===================================================================*/
            } else {

                mensajeToast('error', 'EL PRODUCTO NO EXISTE O NO TIENE STOCK');

                $("#iptCodigoVenta").val("");
                $("#iptCodigoVenta").focus();
            }

        }
    });

} /* FIN CargarProductos */

/*===================================================================*/
//REALIZAR LA VENTA
/*===================================================================*/
function realizarVenta() {

var count = 0;
var totalVenta = $("#totalVenta").html();
var nro_boleta = $("#iptNroVenta").val();
var paymetMethod = $("#selTipoPago").val();

table.rows().eq(0).each(function(index) {
    count = count + 1;
});

if (count > 0) {

    if ($("#iptEfectivoRecibido").val() > 0 && $("#iptEfectivoRecibido").val() != "") {

        if ($("#iptEfectivoRecibido").val() < parseFloat(totalVenta)) {

            mensajeToast('error', 'EL EFECTIVO ES MENOR EL COSTO TOTAL DE LA VENTA');

            return false;
        }
        
        var formData = new FormData();
        var arr = [];

        table.rows().eq(0).each(function(index) {

            var row = table.row(index);

            var data = row.data();

            arr[index] = data['id_menu'] + "," + parseFloat($.parseHTML(data['cantidad'])[0]['value']) + "," + data['total'].replace("$ ", "");

            formData.append('arr[]', arr[index]);
           
        });

        // formData.append('nro_boleta', nro_boleta);
        // formData.append('descripcion_venta', 'Venta realizada con Nro Boleta: ' + nro_boleta);
            formData.append('total_venta', parseFloat(totalVenta));
            formData.append('payment', paymetMethod);
            formData.append('id_open', idOpen);


        formData.forEach(function(value, key) {
console.log(key + ': ' + value);
});
        $.ajax({
            url: "ajax/salesajax.php",
            method: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(respuesta) {

                
                mensajeToast('success', respuesta);

                table.clear().draw();

                LimpiarInputs();

                //CargarNroBoleta();

             //  window.open('http://localhost:8080/ajax/salesajax.php?nro_boleta='+nro_boleta);

            }
        });


    } else {

        mensajeToast('error', 'INGRESE EL MONTO EN EFECTIVO');
    }

} else {

    mensajeToast('error', 'NO HAY PRODUCTOS EN EL LISTADO');

}

$("#iptCodigoVenta").focus();

} /* FIN realizarVenta */
</script>



<?php
    }

?>

<!-- Warning de confirmar abrir caja -->

<script>
function confirmOpened(userId, idOpen, fechaActual) {
    Swal.fire({
        title: '¿Estás seguro de que deseas abrir caja?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, abrir',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "<?php echo constant('URL') ?>crud_opening/editOpened", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                if (xhr.status === 200) {
                    console.log(xhr.responseText);
                    CargarContenido('views/sales/index.php', 'content-wrapper');
                } else {
                    console.error('Error en la solicitud: ' + xhr.status);
                }
            };
            
            // Envía id_user, id_open y fecha_actual como parámetros
            xhr.send("id_user=" + userId + "&id_open=" + idOpen + "&fecha_actual=" + fechaActual);
        }
    });
}
</script>


<!-- Warning de confirmar cierre de caja -->


<script>
function confirmClosed(userId, idOpen, fechaActual) {
    Swal.fire({
        title: '¿Estás seguro de que deseas cerrar caja?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, cerrar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "<?php echo constant('URL') ?>crud_opening/editClosed", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                if (xhr.status === 200) {
                    console.log(xhr.responseText);
                    CargarContenido('views/sales/index.php', 'content-wrapper');
                } else {
                    console.error('Error en la solicitud: ' + xhr.status);
                }
            };
            
            // Envía id_user, id_open y fecha_actual como parámetros
            xhr.send("id_user=" + userId + "&id_open=" + idOpen + "&fecha_actual=" + fechaActual);
        }
    });
}
</script>

