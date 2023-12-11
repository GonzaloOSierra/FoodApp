<?php
include_once '/var/www/html/libs/imodel.php';
include_once '/var/www/html/libs/model.php';
require_once '/var/www/html/libs/imodel.php';
require_once '/var/www/html/libs/model.php';

class OpenModel extends Model{
    private $id_open;
    private $id_employee;
    private $amount_credit;
    private $amount_efective;
    private $amount_qr;
    private $amount_debito;
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
        $this->amount_credit = 0;
        $this->amount_efective = 0;
        $this->amount_qr = 0;
        $this->amount_debito = 0;
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
            $query = $this->query('SELECT b.*, a.name, a.last_name, c.id_cash, c.amount_credit_final, c.amount_efective_final,
                                    c.amount_qr_final, c.amount_debito_final
                                    FROM opening as b
                                    JOIN employees as a ON b.id_employee = a.id_employee
                                    JOIN box_cash as c ON b.id_open = c.id_open;');
            while ($p = $query->fetch(PDO::FETCH_ASSOC)) {
                $item = new OpenModel();
                $item->setId_open($p['id_open']);
                $item->setId_employee($p['id_employee']);
                $item->setAmount_credit($p['amount_credit']);
                $item->setAmount_efective($p['amount_efective']);
                $item->setAmount_qr($p['amount_qr']);
                $item->setAmount_debito($p['amount_debito']);
                $item->setStart_date($p['start_date']);
                $item->setFinal_date($p['final_date']);	
                $item->setId_terminal($p['id_terminal']);
                $item->setDate_start_E($p['date_start_employee']);
                $item->setDate_final_E($p['date_final_employee']);
                $item->setOpened($p['opened']);
                $item->setName_employee($p['name']);
                $item->setLast_name_employee($p['last_name']);
                $item->setId_cash($p['id_cash']);
                $item->setAmount_credit_final($p['amount_credit_final']);
                $item->setAmount_efective_final($p['amount_efective_final']);
                $item->setAmount_qr_final($p['amount_qr_final']);
                $item->setAmount_debito_final($p['amount_debito_final']);

    
                // Agregar el objeto $item al array $items
                $items[] = $item;
            }
            return $items;
        } catch (PDOException $e) {
            error_log('OPENMODEL::getAll-> PDOException '.$e);
            return []; // Devuelve un array vacío en caso de error
        }
    }
    
    
    public function createOpen($id_employee, $amount_credit, $amount_efective, $amount_qr, $amount_debito, $start_date, $final_date, $terminal, $amount_final) {
        try {
            $db = new Database(); // Crear una instancia de la clase Database
            $pdo = $db->connect(); // Obtener la conexión PDO
    
            // Insertar en la tabla 'opening'
            $query = $pdo->prepare('INSERT INTO opening (id_employee, amount_credit, amount_efective, amount_qr, amount_debito, start_date, final_date, id_terminal) 
                VALUES (:id_employee, :amount_credit, :amount_efective, :amount_qr, :amount_debito, :start_date, :final_date, :id_terminal)');
            $query->execute([
                'id_employee'        => $id_employee,
                'id_terminal'        => $terminal,
                'amount_credit'      => $amount_credit,
                'amount_efective'    => $amount_efective,
                'amount_qr'          => $amount_qr,
                'amount_debito'      => $amount_debito,
                'start_date'         => $start_date,
                'final_date'         => $final_date,
            ]);
    
            // Obtener el ID recién insertado en la tabla 'opening'
            $lastInsertId = $pdo->lastInsertId();
    
            // Insertar en la tabla 'box_cash' usando el ID de 'opening'
            $query = $pdo->prepare('INSERT INTO box_cash (id_open, amount_credit_final, amount_efective_final, amount_qr_final, amount_debito_final) VALUES (:id_open, :amount_credit_final, :amount_efective_final, :amount_qr_final, :amount_debito_final)');
            $query->execute([
                'id_open'      => $lastInsertId,
                'amount_credit_final' => $amount_credit,
                'amount_efective_final' => $amount_efective,
                'amount_qr_final' => $amount_qr,
                'amount_debito_final' => $amount_debito,

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
    



     public function setId_open($id_open){                                  $this->id_open = $id_open;  }
     public function setId_employee($id_employee){                          $this->id_employee = $id_employee;  }
     public function setAmount_credit($amount_credit){                      $this->amount_credit = $amount_credit;  }
     public function setAmount_efective($amount_efective){                  $this->amount_efective = $amount_efective;  }
     public function setAmount_qr($amount_qr){                              $this->amount_qr = $amount_qr;  }
     public function setAmount_debito($amount_debito){                      $this->amount_debito = $amount_debito;  }
     public function setStart_date($start_date){                            $this->start_date = $start_date;  }
     public function setFinal_date($final_date){                            $this->final_date = $final_date;  }
     public function setId_terminal($id_terminal){                          $this->id_terminal = $id_terminal;  }
     public function setDate_start_E($date_start_emp){                      $this->date_start_emp = $date_start_emp;  }
     public function setDate_final_E($date_final_emp){                      $this->date_final_emp = $date_final_emp;  }
     public function setOpened($opened){                                    $this->opened = $opened;  }
     public function setName_employee($name_employee){                      $this->name_employee = $name_employee;  }
     public function setLast_name_employee($last_name_employee){            $this->last_name_employee = $last_name_employee;  }
     public function setId_cash($id_cash){                                  $this->id_cash = $id_cash;  }
     public function setAmount_credit_final($amount_credit_final){          $this->amount_credit_final = $amount_credit_final;  }
     public function setAmount_efective_final($amount_efective_final){      $this->amount_efective_final = $amount_efective_final;  }
     public function setAmount_qr_final($amount_qr_final){                  $this->amount_qr_final = $amount_qr_final;  }
     public function setAmount_debito_final($amount_debito_final){          $this->amount_debito_final = $amount_debito_final;  }



     public function getId_open(){                                  return $this->id_open;}
     public function getId_employee(){                              return $this->id_employee;}
     public function getAmount_credir(){                            return $this->amount_credit;}
     public function getAmount_efective(){                          return $this->amount_efective;}
     public function getAmount_qr(){                                return $this->amount_qr;}
     public function getAmount_debito(){                            return $this->amount_debito;}
     public function getId_terminal(){                              return $this->id_terminal;}
     public function getStart_date(){                               return $this->start_date;}
     public function getFinal_date(){                               return $this->final_date;}
     public function getDate_start_E(){                             return $this->date_start_emp;}
     public function getDate_final_E(){                             return $this->date_final_emp;}
     public function getOpened(){                                   return $this->opened;}
     public function getName_employee(){                            return $this->name_employee;}
     public function getLast_name_employee(){                       return $this->last_name_employee;}
     public function getId_cash(){                                  return $this->id_cash;}
     public function getAmount_credit_final(){                      return $this->amount_credit_final;}
     public function getAmount_efective_final(){                    return $this->amount_efective_final;}
     public function getAmount_qr_final(){                          return $this->amount_qr_final;}
     public function getAmount_debito_final(){                      return $this->amount_debito_final;}



}