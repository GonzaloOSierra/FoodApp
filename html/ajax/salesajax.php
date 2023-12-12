<?php

require_once ('../controllers/sales.php');


class SalesAjax {


    public function listProducts() {
       //echo 'HOLA '.$_POST['action'];
        $product = new Sales();
        $result = $product->ctrListProduct();

       
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        

    }

    public function shearchMenu() {
        //echo 'HOLA '.$_POST['action'];
        $id_menu = $_POST['id_menu'];
         $product = new Sales();
         $result = $product->shearchMenuId($id_menu);
 
        
         echo json_encode($result, JSON_UNESCAPED_UNICODE);
         
 
     }

     public function verifyStock() {
        //echo 'HOLA '.$_POST['action'];
        $id_menu = $_POST['id_menu'];
        $cantidad = $_POST['cantidad'];
         $product = new Sales();
         $result = $product->crlVerifyStock($id_menu, $cantidad);
 
        
         echo json_encode($result, JSON_UNESCAPED_UNICODE);
         
 
     }
     public function registerVenta($array, $totalVenta, $payment) {
        $id_open = isset($_POST['id_open']) ? $_POST['id_open'] : null;

        $product = new Sales();
        $result = $product->crlRegisterVenta($array, $totalVenta, $payment, $id_open);

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    } 





}

if(isset($_POST['action']) && $_POST['action'] == '6'){
error_log("ACTION: ".$_POST['action']);
    $var = new SalesAjax();
    $var->listProducts();
}else if(isset($_POST['action']) && $_POST['action'] == '7'){
    error_log("ACTION: ".$_POST['action']);
        $var = new SalesAjax();
        $var->shearchMenu();
}if(isset($_POST['action']) && $_POST['action'] == '8'){
    error_log("ACTION: ".$_POST['action']);
        $var = new SalesAjax();
        $var->verifyStock();
}
if(isset($_POST['arr'])) {
    $var = new SalesAjax();
    $var->registerVenta($_POST['arr'], $_POST['total_venta'], $_POST['payment']);
}









?>