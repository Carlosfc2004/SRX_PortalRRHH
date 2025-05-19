<?php 
include_once("header.php");
?>

<?php 
// USUARIOS
$curl = curl_init();
$url = "http://192.168.200.210/surexport/users_integracion.php?operator=Cies&pass=Cies$2019&function=usersGetAll";

curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json'
    ]
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

$usuarios_registrados = [];

// Función para extraer el valor correcto del array o string
function obtenerValor($dato) {
    if (is_array($dato)) {
        // Si es un array con un solo elemento
        if (count($dato) === 1 && isset($dato[0])) {
            return $dato[0];
        }
        // Si es un array con múltiples elementos
        return implode(', ', array_filter($dato));
    }
    // Si es null o no existe
    if ($dato === null) {
        return '';
    }
    // Si es un string u otro tipo
    return strval($dato);
}

if($response === false) {
    $error = curl_error($curl);
    echo "<div class='alert alert-danger'>Error cURL: $error</div>";
} elseif($httpCode != 200) {
    echo "<div class='alert alert-danger'>Error en la llamada de la API. Código: $httpCode</div>";
} else {
    $xmlObject = simplexml_load_string($response);
    if ($xmlObject === false) {
        echo "<div class='alert alert-danger'>Error al procesar XML</div>";
    } else {
        $json = json_encode($xmlObject, JSON_UNESCAPED_UNICODE);
        $datos = json_decode($json, true);
        
        if (isset($datos['USER'])) {
            // Si solo hay un usuario, convertirlo en array
            $usuarios_registrados = isset($datos['USER']['EXTERNALID']) ? 
                [$datos['USER']] : $datos['USER'];
        }
    }
}
?>

<div class="pagetitle">
    <h1>Usuarios de TESA</h1>
</div>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item">TESA</li>
            <li class="breadcrumb-item active"><?php echo $lang['menu14'] ?></li>
        </ol>
    </nav>

<section class="section">
    <?php
        if ($_SESSION["tipo_user_surexport_appreclu"] != 'Supervisor') {
            if (in_array(22, $_SESSION["permisos_surexport_appreclu"])) {
                ?>
            <div class="card">
                <br>
                <div class="card-body">
                    <div class='col-md-3 align-c mt-2'>
                    <style>
                        /* Contenedor del enlace */
                        .link-container {
                            display: flex;
                            flex-direction: column; 
                            align-items: center;     
                            text-align: center;   
                            padding: 10px;
                            text-decoration: none;
                            color: #333;
                            transition: all 0.3s ease;
                            border-radius: 8px;
                            width: fit-content;   
                            cursor: pointer;   
                        }

                        /* Efecto hover en el contenedor */
                        .link-container:hover {
                            background-color: #f0f0f0;
                            transform: translateY(-2px);
                            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                        }

                        /* Estilo de la imagen */
                        .icon-hover {
                            width: 40px;           
                            height: 36px;
                            margin-bottom: 8px;    
                            transition: all 0.3s ease;
                        }

                        

            
                    </style>

                    <a data-bs-toggle='modal' data-bs-target='#verticalycentered' class="link-container">
                        <img src='https://cdn-icons-png.flaticon.com/512/327/327628.png' alt="icon" class="icon-hover">
                        <span class="link-text">Añadir Usuario</span>
                    </a>
                    </div>
                    <div class="modal fade" id="verticalycentered" tabindex="-1" style="display: none;" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" style="max-width: 900px;">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Nuevo usuario</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="admin_cont.php?controller=index&action=tesa_usuarios&nuevo" method="post" id="userForm">
                                    <input type="hidden" name="newusu_tesa">
                                        <div class="row">
                                            <!-- Primera fila -->
                                            <div class="col-md-6">
                                                <label for="nombre">Nombre:</label>
                                                <input type="text" id="nombre" name="nombre" class="form-control">
                                            </div>

                                            <div class="col-md-3">
                                                <label for="id_tarjeta">ID Tarjeta:</label>
                                                <input type="text" id="id_tarjeta" name="id_tarjeta" class="form-control">
                                            </div>

                                            <div class="col-md-3">
                                                <label for="id_usuario">ID Usuario:</label>
                                                <input type="text" id="id_usuario" name="id_usuario" class="form-control">
                                            </div>
                                            <div class="clear"><br></div>

                                            <!-- Segunda fila -->
                                            <div class="col-md-4 ">
                                                <label for="grupo">Grupo:</label>
                                                <select class="form-select" name="grupo">
                                                    <option value="">--</option>
                                                    <option value="">INFORMATICA</option>
                                                    <option value="">PRL</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                            </div>

                                            <div class="col-md-6">
                                                <label for="email">E-mail:</label>
                                                <input type="email" id="email" name="email" class="form-control">
                                            </div>
                                            <div class="clear"><br></div>

                                            <!-- Tercera fila -->
                                            <div class="col-md-4">
                                                <label for="caducidad">Caducidad:</label>
                                                <input type="datetime-local" id="caducidad" name="caducidad" class="form-control">
                                            </div>

                                            <div class="col-md-4">
                                                <label for="activacion">Activación:</label>
                                                <input type="datetime-local" id="activacion" name="activacion" class="form-control">
                                            </div>

                                            <div class="col-md-2">
                                                <label for="pin">PIN:</label>
                                                <input type="text" 
                                                    id="pin" 
                                                    name="pin" 
                                                    class="form-control" 
                                                    maxlength="6" 
                                                    pattern="[0-9]*" 
                                                    inputmode="numeric">
                                            </div>
                                            <div class="clear"><br></div>

                                            <!-- Checkboxes -->
                                            <div class="col-md-3">
                                                <div class="checkbox-group">
                                                    <input type="checkbox" id="vence_bloqueo" name="vence_bloqueo" class="form-check-input">
                                                    <label for="vence_bloqueo" class="form-check-label">Vence bloqueo</label>
                                                </div>
                                            </div>
                                            <div class="clear"></div>
                                            <div class="col-md-3">
                                                <div class="checkbox-group">
                                                    <input type="checkbox" id="discapacitado" name="discapacitado" class="form-check-input">
                                                    <label for="discapacitado" class="form-check-label">Discapacitado</label>
                                                </div>
                                            </div>
                                            <div class="clear"><br></div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php 
                        } else {
                            
                        }
                    }
                ?>
            </div>
        </div>
    </div>

    <div class="card">
        <br>
        <div class="card-body">
            <table class="table datatable">
                <thead>
                    <tr>
                        <th class="col-md-1">PERNR</th>
                        <th class="col-md-4">Nombre</th>
                        <th class="col-md-2">USERID</th>
                        <th class="col-md-2">NFC</th>
                        <?php
                        if (in_array(24, $_SESSION["permisos_surexport_appreclu"])) {
                            echo "<th class='col-md-1'></th>";
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if (!empty($usuarios_registrados)) {
                            foreach ($usuarios_registrados as $usuario) {
                                // Extraer valores de manera segura
                                $externalId = obtenerValor($usuario['EXTERNALID'] ?? '');
                                $username = obtenerValor($usuario['USERNAME'] ?? '');
                                $userid = obtenerValor($usuario['USERID'] ?? '');
                                $cardId = obtenerValor($usuario['USERCARDID'] ?? '');
                        ?>
                                <tr>
                                    <td><?php echo $externalId; ?></td>
                                    <td><?php echo $username; ?></td>
                                    <td><?php echo $userid; ?></td>
                                    <td><?php echo $cardId; ?></td>
                                    <?php
                                    if (in_array(24, $_SESSION["permisos_surexport_appreclu"])) {
                                    ?>
                                    <td>
                                        <?php 
                                            if ($externalId != '') {
                                                ?>
                                                    <a href="admin_cont.php?controller=index&action=tesa_update_usu&id=<?php echo $externalId; ?>">
                                                        <div class="hvr-icon" >
                                                            <i class="bi bi-pencil-square fs-4" style="color: #012970;"></i>
                                                        </div>
                                                    </a>
                                                <?php
                                            }
                                        ?>
                                    </td>
                                    <?php
                                    }
                                    ?>
                                </tr>
                        <?php 
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</section>



<?php 
include_once("footer.php");
?>