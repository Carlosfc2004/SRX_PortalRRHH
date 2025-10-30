<?php
// var_dump($_SESSION["menu_surexport_appreclu"]);
// die;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Surexport RR.HH.</title>
	<link rel="shortcut icon" type="image/x-icon" href="img/logo.ico">
	<link rel="stylesheet" href="alertify.css">
	<link rel="stylesheet" href="style.css">
	<!-- Vendor CSS Files -->
	<link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
	<link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
	<link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
	<link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
	<link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
	<link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">
	<!-- Template Main CSS File -->
	<link href="assets/css/style.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

</head>

<script src="js/script.js"></script>


<body>
<div id="ancla"></div>

<div class="loader"></div>
<header id="header" class="header fixed-top d-flex align-items-center">
  <div class="d-flex align-items-center justify-content-between">
    <a href="admin_cont.php?controller=index&action=home" class="logo d-flex align-items-center">
      <img src="img/logo.png" alt="">
      <span class="d-none d-lg-block">surexport</span>
    </a>
    <i class="bi bi-list toggle-sidebar-btn"></i>
  </div>
  <nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center">
      <!-- <li class="nav-item d-block d-lg-none">
        <a class="nav-link nav-icon search-bar-toggle" href="#">
          <i class="bi bi-search"></i>
        </a>
      </li> -->


      <?php 
      
      $num_alertas = 0;

      if (in_array(4, $_SESSION["permisos_surexport_appreclu"])) {
      // Sumar al contador por cada variable de sesión mayor a 0
        if ($_SESSION["trab_sinrespuesta"] > 0) {
          $num_alertas++;
        }

        // if ($_SESSION["trab_aceptados_baja"] > 0) {
        //   $num_alertas++;
        // }
        
        if (count($_SESSION['trab_sinllama']) > 0) {
          $num_alertas++;
        }
      }

      // if (count($_SESSION["cumples"]) > 0) {
      //   $num_alertas++;
      // }

      // if (count($_SESSION["dni_caducados"]) > 0) {
      //   $num_alertas++;
      // }






      if ($num_alertas == 0) {

      } else  {
      ?>


      <li class="nav-item dropdown">
        <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
          <i class="bi bi-bell"></i>
          <span class="badge bg-primary badge-number"><?php echo $num_alertas; ?></span>
        </a>

        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
          <li class="dropdown-header">
          <?php
            if ($num_alertas == 0) {
              echo $lang['noti1'];
            } else {
              echo $lang['noti2']."
              <li>
                <hr class='dropdown-divider'>
              </li>";
            }
          ?>
          </li>
          <?php 
          if (in_array(4, $_SESSION["permisos_surexport_appreclu"])) {
            // Trabajadores en remesa sin respuesta
              if (!empty($_SESSION["trab_sinrespuesta"])) { ?>
                <li class="notification-item d-flex justify-content-between align-items-center">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-x-circle"></i>
                    <div style="margin-left: 10px;">
                      <h6><?php echo $lang['noti3']; ?></h6>
                      <p><?php echo $lang['noti4']." ". $_SESSION["trab_sinrespuesta"]. " en remesas"; ?></p>
                      <p>(2º <?php echo $lang['llama']; ?>)</p>
                    </div>
                  </div>
                  <a href="admin_cont.php?controller=index&action=registros_llama&filtro=sin_respuesta" class="text-end">
                    <i class="bi bi-box-arrow-right"></i>
                  </a> 
                </li>
              <?php 
              }
            ?>

          <!-- Trabajadores aceptados que siguen de baja -->
          <?php 
            // if (!empty($_SESSION["trab_aceptados_baja"])) { ?>
              <!-- <li>
                <hr class='dropdown-divider'>
              </li>
              <li class="notification-item d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                  <i class="bi bi-check-circle"></i>
                  <div style="margin-left: 10px;">
                    <h6>Llamamientos aceptados</h6>
                    <p><?php echo "Siguen de baja ". $_SESSION["trab_aceptados_baja"]. " trabajadores"; ?></p>
                  </div>
                </div>
                <a href="admin_cont.php?controller=index&action=reg_alertas&aceptados" class="text-end">
                  <i class="bi bi-box-arrow-right"></i>
                </a>
              </li> -->
            <?php 
            // }
          ?>
          
          <!-- Trabajadores en remesa sin llamamiento -->
          <?php 
            if (!empty($_SESSION["trab_sinllama"])) { ?>
              <li>
                <hr class='dropdown-divider'>
              </li>
              <li class="notification-item d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                  <i class="bx bxs-phone-off"></i>
                  <div style="margin-left: 10px;">
                    <h6>Llamamiento pendiente</h6>
                    <p>
                      <?php 
                        echo count($_SESSION["trab_sinllama"]); 
                        if (count($_SESSION["trab_sinllama"]) == 1){
                          echo " trabajador sin llamamiento en una remesa";
                        } else {
                          echo " trabajadores sin llamamientos en una remesa";
                        }
                      ?>
                    </p>
                  </div>
                </div>
                <a href="admin_cont.php?controller=index&action=reg_alertas&llama" class="text-end">
                  <i class="bi bi-box-arrow-right"></i>
                </a>
              </li>
            <?php 
            }
          ?>

          <?php 
          }
          ?>
          

          <!-- Cumpleaños -->
          
          <?php 
            // if (!empty($_SESSION["cumples"])) { ?>
              <!-- <li>
                <hr class='dropdown-divider'>
              </li>
              <li class="notification-item d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                  <i class="ri-cake-2-line"></i>
                  <div style="margin-left: 10px;">
                    <h6>Cumpleaños</h6>
                      <p>Hoy 
                        <?php 
                          // echo count($_SESSION["cumples"]); 
                          // if (count($_SESSION["cumples"]) == 1){
                          //   echo " trabajador cumple años";
                          // } else {
                          //   echo " trabajadores cumplen años";
                          // }
                        ?>
                      </p>
                  </div>
                </div>
                <a href="admin_cont.php?controller=index&action=reg_alertas&cumple" class="text-end">
                  <i class="bi bi-box-arrow-right"></i>
                </a>
              </li> -->
            <?php 
            // }
          ?>

          <!-- DNI caducados o por caducar -->
          <?php 
            // if (!empty($_SESSION["dni_caducados"])) { ?>
              <!-- <li>
                <hr class='dropdown-divider'>
              </li>
              <li class="notification-item d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                  <i class="bx bx-id-card"></i>
                  <div style="margin-left: 10px;">
                    <h6>Renovación Documentos</h6>
                    <p>
                      <?php 
                        // echo count($_SESSION["dni_caducados"]); 
                        // if (count($_SESSION["dni_caducados"]) == 1){
                        //   echo " trabajador debe renovar el documento";
                        // } else {
                        //   echo " trabajadores deben renovar el documento";
                        // }
                      ?>
                    </p>
                  </div>
                </div>
                <a href="admin_cont.php?controller=index&action=reg_alertas&doc" class="text-end">
                  <i class="bi bi-box-arrow-right"></i>
                </a>
              </li> -->
            <?php 
            // }
          ?>

        </ul> 
      </li>

      <?php } ?>

      <!-- End Notification Nav -->

      <li class="nav-item dropdown pe-3">
        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
          <!-- <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle"> -->
          <i class="bx bxs-user-circle" style="font-size: 35px;"></i>
          <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $_SESSION["nombre_user_surexport_appreclu"] ?></span>
        </a><!-- End Profile Iamge Icon -->
        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
          <li class="dropdown-header">
            <h6><?php echo $_SESSION["nombre_user_surexport_appreclu"] ?></h6>
            <span>
              <?php 
                if ($_SESSION["tipo_user_surexport_appreclu"] === 'Administrador') {
                  echo $lang['admin'];
                } elseif ($_SESSION["tipo_user_surexport_appreclu"] === 'Usuario') {
                  echo $lang['usu'];
                } elseif ($_SESSION["tipo_user_surexport_appreclu"] === 'Supervisor') {
                  echo "Supervisor";
                }
              ?>
            </span>
            <br>
            <a href="" class="idioma" data-idioma="es"><img src="img/espana.png" style="max-height: 30px; margin: 10px 5px;"></a>
            <a href="" class="idioma" data-idioma="en"><img src="img/ingles.png" style="max-height: 30px; margin: 10px 5px;"></a>
            <a href="" class="idioma" data-idioma="fr"><img src="img/francia.png" style="max-height: 30px; margin: 10px 5px;"></a>
            <a href="" class="idioma" data-idioma="pt"><img src="img/portugal.png" style="max-height: 30px; margin: 10px 5px;"></a>
          </li>
          <li>
            <hr class="dropdown-divider">
          </li>
          <li>
            <a class="dropdown-item d-flex align-items-center" href="admin_cont.php?controller=index&action=miperfil">
              <i class="bi bi-person"></i>
              <span><?php echo $lang['menu17']; ?></span>
            </a>
          </li>

          <li>
            <hr class="dropdown-divider">
          </li>
          <li>
            <a class="dropdown-item d-flex align-items-center" href="admin_cont.php?controller=index&action=salir">
              <i class="bi bi-box-arrow-right"></i>
              <span><?php echo $lang['menu19']; ?></span>
            </a>
          </li>
        </ul>
      </li>
    </ul>
  </nav>
</header>








<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav" id="sidebar-nav">
    <li class="nav-item">
      <a class="nav-link hvr-grow <?php if ($_GET['action']!="home") {echo 'collapsed';} ?>" href="admin_cont.php?controller=index&action=home">
        <i class="bi bi-house-door"></i><span>Home</span></i>
      </a>
    </li>

  <!-- Menu dinamico -->
    
  <?php
    //Pintamos el menú
    if(isset($_SESSION["menu_surexport_appreclu"])){
      // echo '<pre>';
      // print_r($_SESSION["menu_surexport_appreclu"]);
      // echo '</pre>';

      foreach($_SESSION["menu_surexport_appreclu"] as $value){
        //Generamos un array para los permisos
        $array_perm = array();
        $array_url = array();

        if(isset($value['children'])){
          $array_perm = array_keys($value['children']);
          $array_url = array_column($value['children'], 'action');
        }
        $array_perm[] = $value['id_hijo'];
        $array_url[] = $value['action'];
        
        // Comenzamos pintando el menú si está permitido 
        if(!empty(array_intersect($array_perm, $_SESSION["permisos_surexport_appreclu"])) && !($value['sap'] == 1 && $_SESSION["sap_surexport_appreclu"] != 0) ){
          ?>
          <li class="nav-item">
            <?php
            echo '<a class="nav-link hvr-grow ';
            //Añadimos un nuevo nivel en las URL para poder poner los menús desplegados
            if (isset($_GET['sub'])){
              if (!in_array($_GET['action'], $array_url) && !in_array($_GET['sub'], $array_url)) {echo 'collapsed';}
            }else{
              if (!in_array($_GET['action'], $array_url)) {echo 'collapsed';}
            }
            echo '"';
            if($value['action']==null) {echo 'data-bs-target="#submenu_'.$value['id_hijo'].'" data-bs-toggle="collapse" ';} ?>
             href="<?php if($value['action']!=null){echo "admin_cont.php?controller=index&action=".$value['action'];}else{echo "#";} ?>">
              <!-- Cambiamos el icono   -->
              <?php
              if($value['id_hijo']==1){
                echo '<i class="bi bi-people-fill"></i>';
              }elseif($value['id_hijo']==8){
                echo '<i class="bi bi-person-check"></i>';
              }elseif($value['id_hijo']==2){
                echo '<i class="bi bi-save"></i>';
              }elseif($value['id_hijo']==3){
                echo '<i class="bi bi-megaphone"></i>';
              }elseif($value['id_hijo']==6){
                echo '<i class="bi bi-people"></i>';
              }elseif($value['id_hijo']==15){
                echo '<i class="bi bi-person-lines-fill"></i>';
              }elseif($value['id_hijo']==16){
                echo '<i class="bi bi-tablet-fill"></i>';
              }elseif($value['id_hijo']==20){
                echo '<i class="bi bi-key-fill"></i>';
              }elseif($value['id_hijo']==25){
                echo '<i class="bi bi-calendar-week"></i>';
              }elseif($value['id_hijo']==15){
                echo '<i class="bi bi-gear"></i>';
              }elseif ($value['id_hijo']==28){
                echo '<i class="bi bi-calendar2-check"></i>';
              }elseif ($value['id_hijo']==30) {
                echo '<i class="bi bi-clock-fill"></i>';
              }
              ?>
              <!-- Título -->
              <span><?php echo $value["tit_".$_SESSION['idioma_surexport_appreclu']] ?></span>
              <?php
              // Si tiene hijos ponemos el icono de desplegar
              if(isset($value['children'])){ echo '<i class="bi bi-chevron-down ms-auto"></i>';}
              ?>
            </a>
            <?php
            //Recorremos los hijos
            if(isset($value['children'])){
              echo '<ul id="submenu_'.$value['id_hijo'].'" class="nav-content collapse" data-bs-parent="#sidebar-nav">';
              foreach($value['children'] as $value_c){
                if (in_array($value_c['id_hijo'], $_SESSION["permisos_surexport_appreclu"]) && !($value_c['sap'] == 1 && $_SESSION["sap_surexport_appreclu"] != 0) ) {
                  ?>
                  <li class="hvr-icon-back">
                    <a href="admin_cont.php?controller=index&action=<?php echo $value_c['action']; ?>" class="submenu">
                      <i class="bi bi-circle hvr-icon"></i><span><?php echo $value_c['tit_'.$_SESSION['idioma_surexport_appreclu']] ?></span>
                    </a>
                  </li>
                  <?php 
                }
                
              }
              echo '</ul>';
            }
          echo '</li>';
        }
      }
      foreach ($_SESSION["menu_surexport_appreclu"] as $menu_item) {
        if ($_SESSION["tipo_user_surexport_appreclu"] == 1 || isset($menu_item['id_hijo']) && $menu_item['id_hijo'] == 25) {
          ?>
            <li class="nav-item">
              <a class="nav-link hvr-grow <?php if ($_GET['action']!="configuracion") {echo 'collapsed';} ?>" href="admin_cont.php?controller=index&action=configuracion">
                <i class='bi bi-gear'></i>
                <span><?php echo $lang['menu18']; ?></span>
              </a>
            </li>
          <?php
          break; 
        }
      }
    }
  ?>
</aside>
<main id="main" class="main">