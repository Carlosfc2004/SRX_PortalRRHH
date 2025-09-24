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
$num_llama = 2;


if ($m->trazabilidad_llama($pernr, $fecha, $num_llama, $ip)) {
    $params['resultado'] = 'OK';
} else {
    $params['resultado'] = 'ERROR';
}


$datos = $m->datos_trab_carta($pernr);


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
    <title>Carta 2º Llamamiento - Surexport</title>
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
            width: 110px;
        }
        .signature {
            margin: 20px 0;
            width: 170px;
        }
        .footer {
            font-size: 0.9em;
            text-align: center;
            margin-top: 20px;
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
        <h3>2º Llamamiento de incorporación a su puesto de trabajo</h3>

        <p class="datos_ini_trab">
            D./Dª <?php echo $datos[0]['NOMBREYAPELLIDOS']; ?><br>
            D.N.I. <?php echo $datos[0]['DNI']; ?><br>
            Tlf. <?php echo $datos[0]['MOVIL']; ?><br>
        </p>

        <p class="fecha_lugar">
            En Almonte (Huelva), a <?php 
                // Clonamos y sumamos 15 días
                $fecha_mas_15 = clone $datos[0]['fecha_remesa'];
                $fecha_mas_15->modify('+15 days');

                // Obtenemos el día, mes y año de la nueva fecha
                $dia_rem = $fecha_mas_15->format('d');
                $mes_rem = (int)$fecha_mas_15->format('m');
                $anio_rem = $fecha_mas_15->format('Y');

                // Formateamos la fecha
                echo $dia_rem . ' de ' . $meses[$mes_rem - 1] . ' de ' . $anio_rem;
            ?>
        </p>

        <p class="justificado">Estimado/a Sr./Sra.:</p>
        <p class="justificado">
            Conforme lo establecido en el art. 16 del Estatuto de los Trabajadores, y en su condición de trabajador 
            fijo-discontinuo de la empresa <b>Surexport Compañía Agraria, S.L.</b> (en adelante, la “empresa”), con C.I.F. nº B21202817, 
            por medio del presente le volvemos a comunicar su llamamiento ya que a partir del próximo 
            día <b>próximo día <?php echo $dia_inc ." de " . $meses[$mes_inc - 1] ?></b> se reanudan los trabajos que son objeto de su contrato laboral, por 
            lo que debe reincorporarse a su puesto de trabajo.
        </p>
        <p class="justificado">
            Con anterioridad a esta comunicación la Empresa le trasladó un primer aviso de llamamiento en el que le requeríamos para que, 
            en un plazo de quince días desde dicha comunicación, contactara con el/la responsable para coordinar su reincorporación y los 
            trabajos, sin que hasta la fecha nos conste que Ud. haya complido con dicho trámite.
        </p>
        <p class="justificado">
            Por tanto, por medio del presente se realiza un <b>segundo aviso de llamamiento para que el día <?php echo $dia_inc ." de " . $meses[$mes_inc - 1] ?></b> 
            se incorpore a su centro de trabajo.
        </p>
        <p class="justificado">
            Al objeto de poder coordinar las incorporaciones y los trabajos, le volvemos a rogar que se ponga en contacto de forma inmediata con 
            D./Dª <b><?php echo $datos[0]['nombre']. " ". $datos[0]['apellidos']; ?></b> en el número de teléfono: <b><?php echo $datos[0]['telf']; ?></b> 
            y/o en el siguiente correo: <b><?php echo $datos[0]['usr_login']; ?></b>
        </p>
        <p class="justificado">
            Por medio del presente, también se le indica que, si en llegado el día de su reincorporación Ud. no ha contactado con la persona 
            anteriormente indicada y/o no comparece a su puesto de trabajo, la empresa entenderá que desiste de incorporarse al mismo y su contrato 
            de trabajo y, por ello, procederá a formalizar su baja voluntaria en la empresa y comunicará esta a las autoridades competentes, a los 
            efectos oportunos.
        </p>
        <p class="justificado">
            Sin otro particular, reciba un cordial saludo.
        </p>


        <img src="Firma_surex.jpg" alt="Firma" class="signature">
        <div class="footer">
            <p>Polígono Industrial Matalagrana, s/n - 21730 Almonte (Huelva)</p>
            <p>Telf.: 959451550 | <a href="http://www.surexport.es" target="_blank">www.surexport.es</a></p>
        </div>
    </div>
</body>
</html>
