<?php
include_once "/var/www/html/models/menumodel.php";
class Crud_menus extends SessionController {
    function __construct(){
        parent::__construct();
        error_log('DASHBOARD-> inicio de dashboard');

    }

    function render(){
        error_log('DASHBOARD-> CARGA EL INDEX DASHBOARD');
        $this->view->render('menu/crud_menus');
    }


    public function deleteMenu(){
        if($this->existPOST(['id'])){
            $id = $this->getPOST('id');


            $menuModel = new MenuModel();
        if ($menuModel->delete($id)) {
            error_log("Enviando usuario a eliminar -----eliminando------ " . $id);
        } else {
            error_log("Error al eliminar el usuario con ID " . $id);
        }
            
        }
    }


    public function createMenu(){
        if($this->existPOST(['name', 'detail', 'price','category'])){

            $name = $_POST["name"];
            $detail = $_POST["detail"];
            $price = $_POST["price"];
            $category = $_POST["category"];

            $menuModel = new MenuModel();
            if ($menuModel->createMenu($name, $detail, $price, $category)) {
                error_log("Menu esta siendo enviado ------------creando-----------.");
            } else {
                error_log("Error al intentar enviar el menu.");
            }

        }
    }


    public function createCat(){
        if($this->existPOST(['nameCat'])){

            $nameCat = $_POST["nameCat"];

            $menuModel = new MenuModel();
            if ($menuModel->createCat($nameCat)) {
                error_log("Categoria se esta enviado ------------creando-----------.");
            } else {
                error_log("Error al intentar enviar la categoria.");
            }

        }
    }





    public function editMenu(){
        if($this->existPOST(['id','name','category', 'detail', 'price'])){
            $id = $_POST["id"];
            error_log($id);
            error_log($id);
            error_log($id);
            error_log($id);
            error_log($id);
            error_log($id);
            error_log($id);
            error_log($id);
            error_log($id);
            error_log($id);
            $name = $_POST["name"];
            error_log($name);
            // Verificar si el nuevo nombre de menu ya existe en la base de datos
            $menuModel = new MenuModel();
            if ($menuModel->nameExists($id, $name)) {
                echo "El nombre de menu ya está en uso. Por favor, elige otro nombre de menu.";
                error_log("El nombre de menu ya esta en uso");
            } else {
                    // Resto de tus variables
                    $category = $_POST["category"];
                    error_log($name);
                    $detail = $_POST["detail"];
                    error_log($surname);
                    $price = $_POST["price"];

                $menuModel = new MenuModel();
                if ($menuModel->update($id, $name, $category, $detail, $price)) {
                    echo "menu actualizando.";
                } else {
                    echo "Error al actualizar el menu.";
                }
            }
        }
    }




}

?>