<?php
include_once "/var/www/html/models/openmodel.php";
class Crud_opening extends SessionController {
    function __construct(){
        parent::__construct();
        error_log('DASHBOARD-> inicio de dashboard');

    }

    function render(){
        error_log('DASHBOARD-> CARGA EL INDEX DASHBOARD');
        $this->view->render('opening/opening');
    }

    function render2(){
        error_log('DASHBOARD-> CARGA EL INDEX DASHBOARD');
        $this->view->render('sales/index.php');
    }

    public function createOpen(){
        if($this->existPOST(['username', 'terminal', 'amount', 'start_date','final_date'])){
            $id_employee   = $_POST["username"];
            $terminal      = $_POST["terminal"];
            $amount        = $_POST["amount"];
            $start_date    = $_POST["start_date"];
            $final_date    = $_POST["final_date"];
            $amount_final  = $_POST["amount"];


            $openModel = new OpenModel();
            if ($openModel->createOpen($id_employee, $amount, $start_date, $final_date, $terminal,$amount_final)) {
                echo "Caja asignada exitosamente.";
            } else {
                echo "Error al asignar caja.";
            }
            
        }
    }




    public function editOpened(){
        if($this->existPOST(['id_user', 'id_open', 'fecha_actual'])){
            $id_open = $_POST["id_open"];
            $id_user = $_POST["id_user"];
            $fecha_opened = $_POST["fecha_actual"];

            $openModel = new OpenModel();
            if ($openModel->editOpened($id_open, $id_employee, $amount, $start_date, $final_date, $id_terminal, $fecha_opened, $date_final_employee, 1)) {
                echo "Caja editada exitosamente.";
            } else {
                echo "Error al editar caja.";
            }
        }
        
    }





    public function editClosed(){
        if($this->existPOST(['id_user', 'id_open', 'fecha_actual'])){
            $id_open = $_POST["id_open"];
            $id_user = $_POST["id_user"];
            $fecha_closed = $_POST["fecha_actual"];

            $openModel = new OpenModel();
            if ($openModel->editClosed($id_open, $id_employee, $amount, $start_date, $final_date, $id_terminal, $fecha_opened, $fecha_closed, 0)) {
                echo "Caja editada exitosamente.";
            } else {
                echo "Error al editar caja.";
            }
        }
        
    }





}

?>