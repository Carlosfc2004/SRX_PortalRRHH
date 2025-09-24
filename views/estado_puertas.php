<?php 
include_once("header.php");
?>

<?php

//PUERTAS
$curl = curl_init();
$url = "http://192.168.200.210/surexport/users_integracion.php?operator=Cies&pass=Cies$2019&function=doorsGetAll";
$data = array("key1" => "value1", "key2" => "value2", "key3" => "value3");
curl_setopt($curl, CURLOPT_URL, $url); // URL a la cual conectarse
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Retornar el resultado como una cadena en lugar de imprimirlo
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: Bearer your_api_token'
)); // Configurar encabezados
curl_setopt($curl, CURLOPT_POST, true); // Configurar método POST
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data)); // Configurar datos del POST

$response = curl_exec($curl);
if($response === false) {
    $error = curl_error($curl);
    // echo "cURL Error: $error";
} else {
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE); // Código de respuesta HTTP
    
    if($httpCode != 200){
        echo $response = "Error en la llamada de la API. ".$httpCode."";
    }else{
        //echo $response;
        $xmlObject = simplexml_load_string($response); 
        $json = json_encode($xmlObject, JSON_UNESCAPED_UNICODE);
        $puertas_registradas = json_decode($json, true);

    }
        

    // echo "HTTP Code: $httpCode\n";
    // echo "Response: $response\n";

}

// Cerrar la sesión cURL
curl_close($curl);

?>


<section>
    <div class="pagetitle">
        <h1>Estado puertas TESA</h1>
    </div>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item">TESA</li>
            <li class="breadcrumb-item active">Estado puertas</li>
        </ol>
    </nav>
        <?php
        if (isset($error) && !empty($error)) {
            echo '<div class="col"><div class="alert alert-danger" role="alert">Error al cargar los datos de las puertas: ' . htmlspecialchars($error) . '</div></div>';
        } elseif (isset($puertas_registradas['DOOR']) && is_array($puertas_registradas['DOOR'])) {
            // Ejemplo de datos de formularios
            $formularios = $puertas_registradas;
        ?>

            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-4">
            <?php
            foreach ($formularios['DOOR'] as $formulario) {
        ?>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column align-items-center">
                        <h5 class="card-title text-center"><?php echo $formulario["DOORNAME"]; ?></h5>
                        <?php
                        if(isset($formulario['BATTERYPERCENTAGE'])) {
                            $batteryLevel = $formulario['BATTERYPERCENTAGE'];
                            $batteryClass = '';
                            $batteryImg = '';
                            
                            if($batteryLevel > 75) {
                                $batteryImg = '100.png';
                            } else if($batteryLevel > 50) {
                                $batteryImg = '75.png';
                            } else if($batteryLevel > 25) {
                                $batteryImg = '50.png';
                            } else if($batteryLevel > 0) {
                                $batteryImg = '25.png';
                            } else if($batteryLevel == 0) {
                                $batteryImg = '0.png';
                            }
                        ?>
                            <div class="text-center">
                                <img class="bateria mb-2" src="img/<?php echo $batteryImg;?>" width="52px">
                                <div>
                                    <span class="badge bg-secondary">
                                        <?php echo $formulario["BATTERYPERCENTAGE"]; ?>%
                                    </span>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php
        }
        } else {
            echo '<div class="col"><div class="alert alert-warning" role="alert">No se encontraron datos de puertas registradas.</div></div>';
        }
        ?>
    </div>
</section>



<?php 
include_once("footer.php");
?>

