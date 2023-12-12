<?php

require_once '../libs/database.php';
include_once '/var/www/html/libs/model.php';
require_once '../libs/conection.php';
class SalesModel {

    function __constructor(){
       
    }


    /*===================================================================
    LISTAR NOMBRE DE PRODUCTOS PARA INPUT DE AUTOCOMPLETADO
    ====================================================================*/
    public function mdlListProduct()
    {
       
        $query = Conection::connect()->prepare(
                                        "SELECT Concat(								
    										m.id_menu,' / ',
                                            m.name_menu,' / ',
                                            m.precio)  as descripcion_producto
                                        FROM menu m 
                                       ");

        $query->execute();

        return $query->fetchAll();
    }
/*===================================================================
    buscar menu para agregar a la tabla
    ====================================================================*/
    public function mdShearchMenuId($id_menu)
    {
       
        $query = Conection::connect()->prepare(
                                        "SELECT id_menu, name_menu, detalle, precio, c.categoria_name,
                                         CONCAT('$ ',CONVERT(ROUND(1*precio,2), CHAR)) as total
                                        FROM menu p
                                        JOIN categorias c ON c.id_categoria = p.id_categoria
                                        WHERE id_menu = :id_menu");
        
        $query->bindParam(':id_menu', $id_menu, PDO::PARAM_STR);

        $query->execute();

        return $query->fetch(PDO::FETCH_OBJ);
    }

    /*===================================================================
    buscar menu para agregar a la tabla
    ====================================================================*/
    public function mdVerifyStock($id_menu, $cantidad_combos)
    {
       
        $query = Conection::connect()->prepare(
                                        " SELECT
                                        CASE
                                            WHEN SUM(IF(p.stock >= mi.cantidad_requerida * :cantidad_combo, 1, 0)) = COUNT(*) THEN 1
                                            ELSE 0
                                        END AS existe
                                    FROM menu m
                                    JOIN menu_ingredientes mi ON m.id_menu = mi.id_menu
                                    JOIN products p ON mi.id_product = p.id_product
                                    WHERE m.id_menu = :id_menu;");
        
        $query->bindParam(':id_menu', $id_menu, PDO::PARAM_INT);
        $query->bindParam(':cantidad_combo', $cantidad_combos, PDO::PARAM_INT);
        $query->execute();
        
        return $query->fetch(PDO::FETCH_OBJ);
    }
    /*===================================================================
    buscar menu para agregar a la tabla
    ====================================================================*/
    public function mdlRegisterVenta($array, $totalVenta, $payment, $id_open)
        {
        $allInsertionsSuccessful = true; // Variable para rastrear el estado de las inserciones
    
        foreach ($array as $item) {
            $list = explode(',', $item);
    
            if (count($list) === 3) {
                $id_menu = $list[0];
                $cantidad = $list[1];
                $totalVenta = $list[2];
    
                // Preparar la sentencia para registrar la venta en detalle_ventas
                $query = Conection::connect()->prepare(
                    "INSERT INTO detalle_ventas (id_menu, cantidad, precio_total, id_payment)
                    VALUES (:id_menu, :cantidad, :totalVenta, :id_payment)"
                );
    
                $id_payment = $payment; // Asumiendo que $payment contiene el valor que deseas usar
    
                $query->bindParam(':id_menu', $id_menu, PDO::PARAM_INT);
                $query->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
                $query->bindParam(':totalVenta', $totalVenta, PDO::PARAM_INT);
                $query->bindParam(':id_payment', $id_payment, PDO::PARAM_INT);
    
                // Realizar la inserción en detalle_ventas
                if ($query->execute()) {
                    // Actualizar el stock de productos descontando los ingredientes
                    $menuIngredients = Conection::connect()->query(
                                                                    "SELECT id_product, cantidad_requerida
                                                                    FROM menu_ingredientes
                                                                    WHERE id_menu = $id_menu"
                    )->fetchAll(PDO::FETCH_ASSOC);
    
                    foreach ($menuIngredients as $ingredient) {
                        $id_product = $ingredient['id_product'];
                        $cantidad_requerida = $ingredient['cantidad_requerida'] * $cantidad;
    
                        // Actualizar el stock de productos
                        $updateQuery = Conection::connect()->prepare(
                            "UPDATE products
                             SET stock = stock - :cantidad_requerida
                             WHERE id_product = :id_product"
                        );
    
                        $updateQuery->bindParam(':cantidad_requerida', $cantidad_requerida, PDO::PARAM_INT);
                        $updateQuery->bindParam(':id_product', $id_product, PDO::PARAM_INT);
                        
                        if (!$updateQuery->execute()) {
                            // Si una actualización falla, establece la variable $allInsertionsSuccessful a false
                            $allInsertionsSuccessful = false;
                            break; // Salir del bucle si hay una actualización fallida
                        }
                    }
                } else {
                    // Si una inserción en detalle_ventas falla, establece la variable $allInsertionsSuccessful a false
                    $allInsertionsSuccessful = false;
                    break; // Salir del bucle si hay una inserción fallida
                }
                
                if($id_payment == 1){
                    $updateBoxCash = Conection::connect()->prepare(
                        "UPDATE box_cash
                        SET amount_credit_final = amount_credit_final + :totalVenta
                        WHERE id_open = :id_open"
                    );
                    
                    $updateBoxCash->bindParam(':totalVenta', $totalVenta, PDO::PARAM_INT);
                    $updateBoxCash->bindParam(':id_open', $id_open, PDO::PARAM_INT);
                    
                    if (!$updateBoxCash->execute()) {
                        // Si una actualización en Box_cash falla, establece la variable $allInsertionsSuccessful a false
                        $allInsertionsSuccessful = false;
                        break; // Salir del bucle si hay una actualización fallida en Box_cash
                    }
                }else if($id_payment == 2){
                    $updateBoxCash = Conection::connect()->prepare(
                        "UPDATE box_cash
                        SET amount_efective_final = amount_efective_final + :totalVenta
                        WHERE id_open = :id_open"
                    );
                    
                    $updateBoxCash->bindParam(':totalVenta', $totalVenta, PDO::PARAM_INT);
                    $updateBoxCash->bindParam(':id_open', $id_open, PDO::PARAM_INT);
                    
                    if (!$updateBoxCash->execute()) {
                        // Si una actualización en Box_cash falla, establece la variable $allInsertionsSuccessful a false
                        $allInsertionsSuccessful = false;
                        break; // Salir del bucle si hay una actualización fallida en Box_cash
                    }
                }else if($id_payment == 3){
                    $updateBoxCash = Conection::connect()->prepare(
                        "UPDATE box_cash
                        SET amount_qr_final = amount_qr_final + :totalVenta
                        WHERE id_open = :id_open"
                    );
                    
                    $updateBoxCash->bindParam(':totalVenta', $totalVenta, PDO::PARAM_INT);
                    $updateBoxCash->bindParam(':id_open', $id_open, PDO::PARAM_INT);
                    
                    if (!$updateBoxCash->execute()) {
                        // Si una actualización en Box_cash falla, establece la variable $allInsertionsSuccessful a false
                        $allInsertionsSuccessful = false;
                        break; // Salir del bucle si hay una actualización fallida en Box_cash
                    }
                }else if($id_payment == 4){
                    $updateBoxCash = Conection::connect()->prepare(
                        "UPDATE box_cash
                        SET amount_debito_final = amount_debito_final + :totalVenta
                        WHERE id_open = :id_open"
                    );
                    
                    $updateBoxCash->bindParam(':totalVenta', $totalVenta, PDO::PARAM_INT);
                    $updateBoxCash->bindParam(':id_open', $id_open, PDO::PARAM_INT);
                    
                    if (!$updateBoxCash->execute()) {
                        // Si una actualización en Box_cash falla, establece la variable $allInsertionsSuccessful a false
                        $allInsertionsSuccessful = false;
                        break; // Salir del bucle si hay una actualización fallida en Box_cash
                    }
                }

            } else {
                // Si la cadena no se dividió en tres partes, establece la variable $allInsertionsSuccessful a false
                $allInsertionsSuccessful = false;
                break; // Salir del bucle si hay un error
            }
        }
    
        if ($allInsertionsSuccessful) {
            $response = "Venta Registrada con Éxito";
        } else {
            $response = "ERROR AL REGISTRAR VENTA";
        }
    
        return $response;
    }
    


   

}
