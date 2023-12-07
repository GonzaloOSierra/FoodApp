<?php
include_once "/var/www/html/models/establishmodel.php";
class Crud_terminal extends SessionController {
    function __construct(){
        parent::__construct();
        error_log('DASHBOARD-> inicio de dashboard');

    }

    function render(){
        error_log('DASHBOARD-> CARGA EL INDEX DASHBOARD');
        $this->view->render('establishment/create_terminal');
    }

    public function createTerminal(){
        $establishModel = new EstablishModel();
    
        if ($this->existPOST(['dato'])) {
            $id = $_POST["dato"];
        
            if ($establishModel->createTerminal($id)) {
                echo "Terminal creada exitosamente.";
                error_log('DMANDOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO');
            } else {
                error_log('NO MANDOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO');
            }
        }
    }
    

}

?>