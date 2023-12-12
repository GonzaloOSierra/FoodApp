<?php
include_once "/var/www/html/models/kitchenmodel.php";
class Kitchen extends SessionController {
    function __construct(){
        parent::__construct();
        error_log('DASHBOARD-> inicio de dashboard');

    }

    function render(){
        error_log('DASHBOARD-> CARGA EL INDEX DASHBOARD');
        $this->view->render('kitchen/kitchen');
    }



    public function editState(){
        if($this->existPOST(['id', 'state'])){
            $orderId = $_POST["id"];
            $state = $_POST["state"];


            $kitchenModel = new KitchenModel();
            if ($kitchenModel->editState($orderId, $state)) {
                echo "Menu editado exitosamente.";
            } else {
                echo "Error al editar menu.";
            }
        }
        
    }





}

?>