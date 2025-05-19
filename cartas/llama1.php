<?php
include_once("../config.php");
require_once("../models/sqlsrvModel.php");

$meses = array(
    "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
);

$mes = (int)date('m');

$m = new sqlsrvModel();
if (isset($_GET['pernr'])) {
    $pernr = $_GET['pernr'];
}


// Registro de acceso al link
$ip = $_SERVER['REMOTE_ADDR'];
$fecha = date('Y-m-d H:i:s'); 
$num_llama = 1;




$datos = $m->datos_trab_carta($pernr);

if ($m->trazabilidad_llama($pernr, $fecha, $num_llama, $ip)) {
    $params['resultado'] = 'OK';
} else {
    $params['resultado'] = 'ERROR';
}

setlocale(LC_TIME, 'es_ES.UTF-8');
// var_dump($datos);

$dia_inc = $datos[0]['fecha_incorporacion']->format('d');
$mes_inc = (int)$datos[0]['fecha_incorporacion']->format('m');
$anio_inc = $datos[0]['fecha_incorporacion']->format('Y');
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carta 1º Llamamiento - Surexport</title>
    <link rel="shortcut icon" type="image/x-icon" href="logo.png">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 10px;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }
        .datos_ini_trab {
            font-weight: bold; 
            font-size: 14px;
        }
        .fecha_lugar{
            font-weight: bold; 
            font-size: 14px; 
            text-align: right; 
            margin-top: 30px; 
            margin-bottom: 30px;
        }
        .container {
            width: 88%;
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .logo {
            display: block;
            margin: 0 auto 20px;
            width: 80px;
        }
        .signature {
            margin: 13px 0;
            width: 170px;
        }
        .footer {
            font-size: 0.9em;
            text-align: center;
            color: #666;
        }
        .footer a {
            color: #007BFF;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }

        h3 {
            text-align: center;
            color: #444;
        }
        .justificado {
           text-align: justify; 
           margin: 10px;
        }
        .gt_switcher_wrapper {
            position: absolute !important; 
        }
    </style>
</head>
<body>
    <div class="container">

        <div class="gtranslate_wrapper" ></div>
        <script>window.gtranslateSettings = {"default_language":"es","detect_browser_language":true,"languages":["es","fr","pt","ar","bg","ro"],"wrapper_selector":".gtranslate_wrapper","flag_size":24,"horizontal_position":"right","vertical_position":"top"}</script>
        <script src="https://cdn.gtranslate.net/widgets/latest/popup.js" defer></script>

        <img src="logo.png" alt="Logo Surexport" class="logo">
        <h3>1º Llamamiento de incorporación a su puesto de trabajo</h3>

        <p class="datos_ini_trab">
            D./Dª <?php echo $datos[0]['NOMBREYAPELLIDOS']; ?><br>
            D.N.I. <?php echo $datos[0]['DNI']; ?><br>
            Tlf. <?php echo $datos[0]['MOVIL']; ?><br>
        </p>

        <p class="fecha_lugar">
            En Almonte (Huelva), a <?php 
                
                // Obtenemos el día, mes y año de la fecha_remesa
                $dia_rem = $datos[0]['fecha_remesa']->format('d');
                $mes_rem = (int)$datos[0]['fecha_remesa']->format('m');
                $anio_rem = $datos[0]['fecha_remesa']->format('Y');
                
                // Formateamos la fecha completa
                echo $dia_rem . ' de ' . $meses[$mes_rem - 1] . ' de ' . $anio_rem;
            ?>
        </p>

        <p class="justificado">Estimado/a Sr./Sra.:</p>
        <p class="justificado">
            Conforme lo establecido en el art. 16 del Estatuto de los Trabajadores, y en su condición de trabajador 
            fijo-discontinuo de la empresa <b>Surexport Compañía Agraria, S.L.</b> (en adelante, la “empresa”), con C.I.F. 
            nº B21202817, por medio del presente le comunicamos que a partir del <b>próximo día <?php echo $dia_inc ." de " . $meses[$mes_inc - 1] ?></b> la empresa tiene 
            prevista la reanudación de los trabajos que son objeto de su contrato laboral.
        </p>
        <p class="justificado">
            Es por ello que, en atención a lo dispuesto en la normativa vigente, por este medio y con una 
            antelación de al menos quince (15 días), <b>se le comunica su llamamiento para que el día <?php echo $dia_inc ." de " . $meses[$mes_inc - 1] ?> se 
            incorpore a su centro de trabajo.</b>
        </p>
        <p class="justificado"> 
            Al objeto de poder coordinar las incorporaciones y los trabajos, <b>rogamos se ponga en contacto en un plazo máximo de cinco 
            días desde la recepción de esta comunicación</b> con D./Dª <b><?php echo $datos[0]['nombre']. " ". $datos[0]['apellidos']; ?></b> en el número de 
            teléfono: <b><?php echo $datos[0]['telefono']; ?></b> y/o en el siguiente correo: <b><?php echo $datos[0]['usr_login']; ?></b>
        </p>
        <p class="justificado">
            Le recordamos que, en caso de no poder asistir por causa justificada, deberá justificar su ausencia a la mayor brevedad 
            posible y siempre antes de la fecha de su reincorporación. En caso contrario, se entenderá que ha renunciado a su puesto 
            de trabajo y a su contrato y por tanto la empresa procederá a su baja voluntaria en seguridad social.
        </p>
        <p class="justificado">Sin otro particular, reciba un cordial saludo.</p>


        <img src="Firma_surex.jpg" alt="Firma" class="signature">
        <div class="footer">
            <p>Polígono Industrial Matalagrana, s/n - 21730 Almonte (Huelva)</p>
            <p>Telf.: 959451550 | <a href="http://www.surexport.es" target="_blank">www.surexport.es</a></p>
        </div>
    </div>
</body>
</html>
