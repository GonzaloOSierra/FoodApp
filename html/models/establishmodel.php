<?php
include_once '/var/www/html/libs/imodel.php';
include_once '/var/www/html/libs/model.php';
require_once '/var/www/html/libs/imodel.php';
require_once '/var/www/html/libs/model.php';

class EstablishModel extends Model{
    private $id_terminal;


    public function __construct() {
        parent::__construct();
        $this->d_Id_Venta = "";


    }


    public function getAll() {
        $items = [];

        try {
            $query = $this->query('SELECT terminal.*
                                    FROM terminal
                                    ');

            while ($p = $query->fetch(PDO::FETCH_ASSOC)) {
                $item = new EstablishModel();
                $item->setId_terminal($p['id_terminal']);
                $items[] = $item;
            }

            return $items;
        } catch (PDOException $e) {
            error_log('ESTABLISHMODEL::getAll-> PDOException ' . $e);
        }
    }


    public function createTerminal($id) {
        try {
            $query = $this->prepare('INSERT INTO terminal () VALUES ()');
            $query->execute();
    
            return true;
        } catch (PDOException $e) {
            error_log('ESTABLISHMODEL::createTerminal-> PDOException ' . $e);
            return false;
        }
    }
    





     public function setId_terminal($id_terminal){             $this->id_terminal = $id_terminal;  }


     public function getId_terminal(){                return $this->id_terminal;}

}//cierra Clase