<?php

require_once "/var/www/html/models/salesmodel.php";

class Sales{
    function __construct(){
        
        error_log('Login-> inicio de login');

    }

    public function render() {
        error_log('DASHBOARD -> CARGA EL INDEX DASHBOARD');
    }

    /*===================================================================
    LISTAR NOMBRE DE PRODUCTOS PARA INPUT DE AUTOCOMPLETADO
    ====================================================================*/
    public function ctrListProduct() {
        error_log('Products::ctrListarNombreProductos');
        $producto = Salesmodel::mdlListProduct();

        return $producto;
    }
    public function shearchMenuId($id_menu) {
        error_log('Products::ctrListarNombreProductos');
        $producto = Salesmodel::mdShearchMenuId($id_menu);

        return $producto;
    }

    public function crlVerifyStock($id_menu, $cantidad) {
        error_log('Products::ctrListarNombreProductos');
        $producto = Salesmodel::mdVerifyStock($id_menu, $cantidad);

        return $producto;
    }
    
    public function crlRegisterVenta($array, $totalVenta, $payment, $id_open) {
        error_log('Sales::crlRegisterVenta');

        error_log($id_open);
    
        // Crear un nuevo id_order
        $orderId = Salesmodel::createOrderId();
    
        // Obtener el último registro de la tabla order
        $lastOrder = Salesmodel::getOrder();
    
        if ($lastOrder !== false) {
            // Obtener el id_order del último registro
            $lastOrderId = $lastOrder['id_order'];
    
            // Llamar al método mdlRegisterVenta con el nuevo id_order
            $producto = Salesmodel::mdlRegisterVenta($array, $totalVenta, $payment, $id_open, $lastOrderId);
            return $producto;
        } else {
            // Manejar el caso en que no se pudo obtener el último registro
            return "Error al obtener el último registro de la tabla 'order'";
        }
    }
    

}

?>
