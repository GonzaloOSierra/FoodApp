<?php

session_start();

?>

<!-- Main Sidebar Container -->
 <aside class="main-sidebar sidebar-dark-primary elevation-4">
     <!-- Brand Logo -->
     <a href="index.php" class="brand-link">
         <img src="views/assets/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
         <span class="brand-text  font-weight-bold">APP FOOD</span>
     </a>

    

     <!-- Sidebar -->
     <div class="sidebar">

        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="views/assets/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <h6 class="text-warning"><?php
                
                echo $_SESSION["user_roleName"]. ': '.$_SESSION["user_N"] ?></h6>
           
            </div>
        </div>
        
         <!-- Sidebar Menu -->
         <nav class="mt-2">

             <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
            
           

                 <li class="nav-item">
                     <a style="cursor: pointer;" class="nav-link active" onclick="CargarContenido('views/dashboard/dashboard.php','content-wrapper')">
                         <i class="nav-icon fas fa-th text-success"></i>
                         <p>
                             Tablero Principal
                         </p>
                     </a>
                 </li>
                 <?php
                 
                                     
                 if ($_SESSION["user_role"]==1) { ?>
                
                 <?php }  if ($_SESSION["user_role"]==2 or $_SESSION["user_role"]==1) { ?>
                 <li class="nav-item">
                     <a href="#" class="nav-link">
                     <i class="nav-icon fas fa-store-alt text-warning"></i> 
                         <p>Ventas<i class="right fas fa-angle-left"></i></p>
                     </a>
                     <ul class="nav nav-treeview">
                         <li class="nav-item">
                             <a href="#" class="nav-link" style="cursor:pointer;" onclick="CargarContenido('views/sales/index.php?session_id=<?= session_id() ?>','content-wrapper')">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>Punto de Venta</p>
                             </a>
                         </li>
                         
                     </ul>
                 </li>

                 <?php }  if ($_SESSION["user_role"]==2 or $_SESSION["user_role"]==1) { ?>
                 <li class="nav-item">
                     <a href="#" class="nav-link">
                     <i class="nav-icon fas fa-store-alt text-warning"></i> 
                         <p>Cocinas<i class="right fas fa-angle-left"></i></p>
                     </a>
                     <ul class="nav nav-treeview">
                         <li class="nav-item">
                             <a href="#" class="nav-link" style="cursor:pointer;" onclick="CargarContenido('views/kitchen/kitchen.php','content-wrapper')">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>Cocina</p>
                             </a>
                         </li>
                         
                     </ul>
                 </li>

                 <?php }  if ($_SESSION["user_role"]==1) { ?>
                 <li class="nav-item">
                     <a href="#" class="nav-link">
                     
                     <i class="nav-icon fas fa-utensils text-warning"></i>
                         <p>Caja<i class="right fas fa-angle-left"></i></p>
                     </a>
                     <ul class="nav nav-treeview">
                         <li class="nav-item">
                             <a href="#" class="nav-link" style="cursor: pointer;" onclick="CargarContenido('views/opening/opening.php','content-wrapper')">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>Administrar Caja</p>
                             </a>
                         </li>
                         <li class="nav-item">
                             <a href="#" class="nav-link" style="cursor: pointer;" onclick="CargarContenido('views/opening/opened_list.php','content-wrapper')">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>Aberturas</p>
                             </a>
                         </li>
                     </ul>
                 </li>

                 <?php }  if ($_SESSION["user_role"]==1) { ?>
                 <li class="nav-item">
                     <a href="#" class="nav-link">
                     
                     <i class="nav-icon fas fa-utensils text-warning"></i>
                         <p>Menu<i class="right fas fa-angle-left"></i></p>
                     </a>
                     <ul class="nav nav-treeview">
                         <li class="nav-item">
                             <a href="#" class="nav-link" style="cursor: pointer;" onclick="CargarContenido('views/menu/crud_menus.php','content-wrapper')">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>Administrar Menu</p>
                             </a>
                         </li>
                         <li class="nav-item">
                             <a href="#" class="nav-link" style="cursor:pointer;" onclick="CargarContenido('views/menu/create_menu.php','content-wrapper')">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>Agregar Menu</p>
                             </a>
                         </li>
                     </ul>
                 </li>
                 <?php }  if ($_SESSION["user_role"]==1) { ?>
                 <li class="nav-item">
                     <a href="#" class="nav-link">
                     <i class="nav-icon fas fa-list text-warning"></i>
                         <p>Categorias<i class="right fas fa-angle-left"></i></p>
                     </a>
                     <ul class="nav nav-treeview">
                         <li class="nav-item">
                             <a href="#" class="nav-link" style="cursor:pointer;" onclick="CargarContenido('views/menu/crud_categorys.php','content-wrapper')">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>Administrar Categoria</p>
                             </a>
                         </li>
                         <li class="nav-item">
                             <a href="#" class="nav-link" style="cursor:pointer;" onclick="CargarContenido('views/menu/create_category.php','content-wrapper')">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>Agregar Categoria</p>
                             </a>
                         </li>
                     </ul>
                 </li>
                  <?php }  if ($_SESSION["user_role"]==1) { ?>
                 <li class="nav-item">
                     <a href="#" class="nav-link">
                     <i class="nav-icon fas fa-users text-warning"></i> 
                         <p>Empleados<i class="right fas fa-angle-left"></i></p>
                     </a>
                     <ul class="nav nav-treeview">
                         <li class="nav-item">
                             <a href="#" class="nav-link" style="cursor:pointer;" onclick="CargarContenido('views/users/crud_users.php','content-wrapper')">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>Administrar</p>
                             </a>
                         </li>
                         <li class="nav-item">
                             <a href="#" class="nav-link" style="cursor:pointer;" onclick="CargarContenido('views/users/create_user.php','content-wrapper')">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>Agregar Empleados</p>
                             </a>
                         </li>
                     </ul>
                 </li>
                 <?php }  if ($_SESSION["user_role"]==1) { ?>
                 <li class="nav-item">
                     <a href="#" class="nav-link">
                     <i class="nav-icon fas fa-hamburger text-warning"></i>
                         <p>Productos<i class="right fas fa-angle-left"></i></p>
                     </a>
                     <ul class="nav nav-treeview">
                         <li class="nav-item">
                             <a href="#" class="nav-link" style="cursor:pointer;" onclick="CargarContenido('views/products/crud_products.php','content-wrapper')">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>Administrar Productos</p>
                             </a>
                         </li>
                         <li class="nav-item">
                             <a href="#" class="nav-link" style="cursor:pointer;" onclick="CargarContenido('views/products/create_products.php','content-wrapper')">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>Agregar Productos</p>
                             </a>
                         </li>
                     </ul>
                 </li>
                 <?php }  if ($_SESSION["user_role"]==1) { ?>
                 <li class="nav-item">
                     <a href="#" class="nav-link">
                     <i class="nav-icon fas fa-truck text-warning"></i>
                         <p>Proveedores<i class="right fas fa-angle-left"></i></p>
                     </a>
                     <ul class="nav nav-treeview">
                         <li class="nav-item">
                             <a href="#" class="nav-link" style="cursor:pointer;" onclick="CargarContenido('views/providers/crud_providers.php','content-wrapper')">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>Administrar</p>
                             </a>
                         </li>
                         <li class="nav-item">
                             <a href="#" class="nav-link" style="cursor:pointer;" onclick="CargarContenido('views/providers/create_providers.php','content-wrapper')">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>Agregar</p>
                             </a>
                         </li>
                     </ul>
                 </li>

                 <?php }  if ($_SESSION["user_role"]==1) { ?>
                 <li class="nav-item">
                     <a href="#" class="nav-link">
                     
                     <i class="nav-icon fas fa-utensils text-warning"></i>
                         <p>Establecimiento<i class="right fas fa-angle-left"></i></p>
                     </a>
                     <ul class="nav nav-treeview">
                         <li class="nav-item">
                             <a href="#" class="nav-link" style="cursor: pointer;" onclick="CargarContenido('views/establishment/create_terminal.php','content-wrapper')">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>Agregar Terminal</p>
                             </a>
                         </li>
                     </ul>
                 </li>
               
                 <li class="nav-item">
                     <a style="cursor: pointer;" class="nav-link sessiondestroid" href="#">
                         <i class="nav-icon fas fa-sign-out-alt text-danger"></i>
                         <p>
                             Cerrar Sesion
                         </p>
                     </a>
                 </li>
                 <?php } ?>
             </ul>
         </nav>
         <!-- /.sidebar-menu -->
         
     </div>
   
 </aside>

 <script>
     $(".nav-link").on('click', function() {
         $(".nav-link").removeClass('active');
         $(this).addClass('active');
     })
 </script>
 <script>
        $(document).ready(function() {
            // Cuando se hace clic en el enlace "Cerrar Sesión"
            $("a.sessiondestroid").click(function(event) {
                event.preventDefault();

                // Muestra una ventana de confirmación con SweetAlert2
                Swal.fire({
                    title: '¿Cerrar Sesión?',
                    text: '¿Estás seguro de que deseas cerrar sesión?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Realizar una solicitud AJAX al script de cierre de sesión
                        $.ajax({
                            type: "POST",
                            url: "ajax/sessiondestroid.php", // Ruta al script de cierre de sesión
                            success: function(response) {
                                // La sesión se ha cerrado con éxito, puedes redirigir a una página de inicio de sesión u otra acción
                                window.location.href = "dashboard";
                            }
                        });
                    }
                });
            });
        });
    </script>