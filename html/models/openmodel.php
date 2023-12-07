<?php
include_once '/var/www/html/libs/imodel.php';
include_once '/var/www/html/libs/model.php';
require_once '/var/www/html/libs/imodel.php';
require_once '/var/www/html/libs/model.php';

class OpenModel extends Model{
    private $id_open;
    private $id_employee;
    private $amount;
    private $start_date;
    private $final_date;
    private $id_terminal;
    private $date_start_emp;
    private $date_final_emp;
    private $opened;
    private $name_employee;
    private $last_name_employee;
    private $id_cash;
    private $amount_final;


    public function __construct() {
        parent::__construct();
        $this->id_open = 0;
        $this->id_employee = 0;
        $this->amount = 0;
        $this->start_date = new DateTime();
        $this->final_date = new DateTime(); 
        $this->id_terminal = 0;
        $this->date_start_emp = new DateTime(); 
        $this->date_final_emp = new DateTime();
        $this->opened = 0;
        $this->name_employee = "";
        $this->last_name_employee = "";
        $this->id_cash = 0;
        $this->amount_final = 0;


    }

    public function getAll() {
        $items = [];
        try {
            $query = $this->query('SELECT b.*, a.name, a.last_name, c.id_cash, c.amount_final
                                    FROM opening as b
                                    JOIN employees as a ON b.id_employee = a.id_employee
                                    JOIN box_cash as c ON b.id_open = c.id_open;');
            while ($p = $query->fetch(PDO::FETCH_ASSOC)) {
                $item = new OpenModel();
                $item->setId_open($p['id_open']);
                $item->setId_employee($p['id_employee']);
                $item->setAmount($p['amount']);
                $item->setStart_date($p['start_date']);
                $item->setFinal_date($p['final_date']);	
                $item->setId_terminal($p['id_terminal']);
                $item->setDate_start_E($p['date_start_employee']);
                $item->setDate_final_E($p['date_final_employee']);
                $item->setOpened($p['opened']);
                $item->setName_employee($p['name']);
                $item->setLast_name_employee($p['last_name']);
                $item->setId_cash($p['id_cash']);
                $item->setAmount_final($p['amount_final']);
    
                // Agregar el objeto $item al array $items
                $items[] = $item;
            }
            return $items;
        } catch (PDOException $e) {
            error_log('OPENMODEL::getAll-> PDOException '.$e);
            return []; // Devuelve un array vacío en caso de error
        }
    }
    
    
    public function createOpen($id_employee, $amount, $start_date, $final_date, $terminal, $amount_final) {
        try {
            $db = new Database(); // Crear una instancia de la clase Database
            $pdo = $db->connect(); // Obtener la conexión PDO
    
            // Insertar en la tabla 'opening'
            $query = $pdo->prepare('INSERT INTO opening (id_employee, amount, start_date, final_date, id_terminal) 
                VALUES (:id_employee, :amount, :start_date, :final_date, :id_terminal)');
            $query->execute([
                'id_employee' => $id_employee,
                'id_terminal' => $terminal,
                'amount'      => $amount,
                'start_date'  => $start_date,
                'final_date'  => $final_date,
            ]);
    
            // Obtener el ID recién insertado en la tabla 'opening'
            $lastInsertId = $pdo->lastInsertId();
    
            // Insertar en la tabla 'box_cash' usando el ID de 'opening'
            $query = $pdo->prepare('INSERT INTO box_cash (id_open, amount_final) VALUES (:id_open, :amount_final)');
            $query->execute([
                'id_open'      => $lastInsertId,
                'amount_final' => $amount_final,
            ]);
    
            // Actualizar el estado del empleado
            $state = 1;
            $query = $pdo->prepare('UPDATE employees 
                SET state = :state 
                WHERE id_employee = :id_employee');
            $query->execute([
                'state'       => $state,
                'id_employee' => $id_employee,
            ]);
    
            return true;
        } catch (PDOException $e) {
            error_log('OPENMODEL::createOpen-> PDOException ' . $e);
            return false;
        }
    }
    
    
    
    
    
    
    public function editOpened($id_open, $id_employee, $amount, $start_date, $final_date, $id_terminal, $fecha_opened, $date_final_employee, $opened) {
        try {
            $query = $this->prepare('UPDATE opening 
                SET opened = :opened, date_start_employee = :date_start_employee
                WHERE id_open = :id_open');
    
            $query->execute([
                'id_open' => $id_open,
                'opened' => $opened,
                'date_start_employee' => $fecha_opened,

            ]);
    
            error_log('OPENMODEL::createOpen-> ACÁ ESTOY, SI APAREZCO LLEGA AL MODELO');
            return true;
        } catch(PDOException $e) {
            error_log('OPENMODEL::createOpen-> PDOException ' . $e);
            return false;
        }
    }
    






    public function editClosed($id_open, $id_employee, $amount, $start_date, $final_date, $id_terminal, $fecha_opened, $fecha_closed, $opened) {
        try {
            $query = $this->prepare('UPDATE opening 
                SET opened = :opened, date_final_employee = :date_final_employee
                WHERE id_open = :id_open');
    
            $query->execute([
                'id_open' => $id_open,
                'opened' => $opened,
                'date_final_employee' => $fecha_closed,

            ]);
    
            error_log('OPENMODEL::createOpen-> ACÁ ESTOY, SI APAREZCO LLEGA AL MODELO');
            return true;
        } catch(PDOException $e) {
            error_log('OPENMODEL::createOpen-> PDOException ' . $e);
            return false;
        }
    }
    








     public function setId_open($id_open){                          $this->id_open = $id_open;  }
     public function setId_employee($id_employee){                  $this->id_employee = $id_employee;  }
     public function setAmount($amount){                            $this->amount = $amount;  }
     public function setStart_date($start_date){                    $this->start_date = $start_date;  }
     public function setFinal_date($final_date){                    $this->final_date = $final_date;  }
     public function setId_terminal($id_terminal){                  $this->id_terminal = $id_terminal;  }
     public function setDate_start_E($date_start_emp){              $this->date_start_emp = $date_start_emp;  }
     public function setDate_final_E($date_final_emp){              $this->date_final_emp = $date_final_emp;  }
     public function setOpened($opened){                            $this->opened = $opened;  }
     public function setName_employee($name_employee){              $this->name_employee = $name_employee;  }
     public function setLast_name_employee($last_name_employee){    $this->last_name_employee = $last_name_employee;  }
     public function setId_cash($id_cash){                          $this->id_cash = $id_cash;  }
     public function setAmount_final($amount_final){                $this->amount_final = $amount_final;  }


     public function getId_open(){                                  return $this->id_open;}
     public function getId_employee(){                              return $this->id_employee;}
     public function getAmount(){                                   return $this->amount;}
     public function getId_terminal(){                              return $this->id_terminal;}
     public function getStart_date(){                               return $this->start_date;}
     public function getFinal_date(){                               return $this->final_date;}
     public function getDate_start_E(){                             return $this->date_start_emp;}
     public function getDate_final_E(){                             return $this->date_final_emp;}
     public function getOpened(){                                   return $this->opened;}
     public function getName_employee(){                            return $this->name_employee;}
     public function getLast_name_employee(){                       return $this->last_name_employee;}
     public function getId_cash(){                                  return $this->id_cash;}
     public function getAmount_final(){                             return $this->amount_final;}


}