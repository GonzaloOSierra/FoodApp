<?php
include_once '/var/www/html/libs/imodel.php';
include_once '/var/www/html/libs/model.php';
require_once '/var/www/html/libs/imodel.php';
require_once '/var/www/html/libs/model.php';

class KitchenModel extends Model{
    private $d_Id_Venta;
    private $id_menu;
    private $d_Cant;
    private $d_TPrice;
    private $d_id_Payment;
    private $id_order;
    private $order_state;
    private $name_menu;
    private $detail;



    public function __construct() {
        parent::__construct();
        $this->d_Id_Venta = 0;
        $this->id_menu = 0;
        $this->d_Cant = 0;
        $this->d_TPrice = 0.0;
        $this->d_Id_Payment = 0;
        $this->id_order = 0;
        $this->order_state = 0;
        $this->id_menu = 0;
        $this->id_order = 0;
        $this->name_menu = 0;
        $this->detail = 0;



    }


    public function getAll(){
        $items = [];
        try {
            $query = $this->query('SELECT o.*, d.*, m.name_menu AS name_menu, m.detalle AS detalle_menu, m.precio AS precio_menu
                                    FROM `order` as o
                                    JOIN `detalle_ventas` as d ON d.id_order = o.id_order
                                    JOIN menu as m ON m.id_menu = d.id_menu
                                ');
    
            while($p = $query->fetch(PDO::FETCH_ASSOC)){
                $item = new KitchenModel();
                $item->setO_id_Order($p['id_order']);
                $item->setO_order_State($p['order_state']);
                $item->setD_id_venta($p['id_detalle_venta']);
                $item->setD_id_menu($p['id_menu']);
                $item->setD_Cant($p['cantidad']);
                $item->setD_tPrice($p['precio_total']);
                $item->setD_id_Payment($p['id_payment']);
                $item->setD_name_menu($p['name_menu']);
                $item->setD_detail($p['detalle_menu']);
    
                array_push($items, $item);
            }
    
            return $items;
        } catch(PDOException $e){
            error_log('KITCHENMODEL::getAll-> PDOException '.$e);
        }
    }
    
     



  
    public function editState($orderId, $state) {
        try {
            $query = $this->prepare('UPDATE `order` 
                SET order_state = :order_state
                WHERE id_order = :id_order');

            $query->execute([
                'id_order' => $orderId,
                'order_state' => $state,
            ]);
    
            error_log('KITCHENMODEL::-> ACÁ ESTOY, SI APAREZCO LLEGA AL MODELO');
            return true;
        } catch(PDOException $e) {
            error_log('KITCHENMODEL::-> PDOException ' . $e);
            return false;
        }
    }





     public function setD_id_venta($d_id_Venta){             $this->d_id_Venta = $d_id_Venta;  }
     public function setD_id_menu($id_menu){                 $this->id_menu = $id_menu;  }
     public function setD_Cant($d_Cant){                     $this->d_Cant = $d_Cant;  }
     public function setD_tPrice($d_TPrice){                 $this->d_TPrice = $d_TPrice;  }
     public function setD_id_Payment($d_Id_Payment){         $this->d_Id_Payment = $d_Id_Payment;  }
     public function setO_id_Order($id_order){               $this->id_order = $id_order;  }
     public function setO_order_State($order_state){         $this->order_state = $order_state;  }
     public function setD_name_menu($name_menu){             $this->name_menu = $name_menu;  }
     public function setD_detail($detail){                   $this->detail = $detail;  }


     public function getD_id_venta(){                        return $this->d_id_Venta;}
     public function getD_id_menu(){                         return $this->id_menu;}
     public function getD_Cant(){                            return $this->d_Cant;}
     public function getD_tPrice(){                          return $this->d_TPrice;}
     public function getD_id_Payment(){                      return $this->d_Id_Payment;}
     public function getO_id_Order(){                        return $this->id_order;}
     public function getO_order_State(){                     return $this->order_state;}
     public function getD_name_menu(){                       return $this->name_menu;}
     public function getD_detail(){                          return $this->detail;}


}
?>