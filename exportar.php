<?php
$Meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
session_start();
date_default_timezone_set("Europe/Madrid");
include_once("fpdf/tfpdf.php");
include_once("config.php");
include_once("seguridad.php");
require_once("models/sqlsrvModel.php");
$con_bdsrx = new sqlsrvModel();

require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;


if (isset($_GET['pdf'])) {
	//Comprobamos si vamos a imprimir una remesa o un solo candidato
	if (isset($_SESSION['array_candidatos'])) {
		$list_candidatos = $_SESSION['array_candidatos'];
	}else{
		$info_candidato = $con_bdsrx->infoCandidato($_GET['id']);	
		$list_candidatos = array();
		$list_candidatos[] = $info_candidato;
	}
	// Creación del objeto de la clase heredada
	$pdf = new tFPDF('P');
	$pdf->SetMargins(20,20,20);
	// $pdf->SetAutoPageBreak(true, 20);
	$pdf->AliasNbPages();
	$pdf->SetFont('Arial','',12);
	$pdf->SetTextColor(0,0,0);
	$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
	foreach ($list_candidatos as $candidato) {
		//-------------------------------------------------------Portada con el DNI
		$pdf->AddPage();
		if ($candidato['foto1']!="") {
			$pdf->Image($candidato['foto1'], 20, 20, -800);
		}
		if ($candidato['foto2']!="") {
			$pdf->Image($candidato['foto2'], 20, 100, -800);
		}
		$pdf->SetXY(20, 220);
		$pdf->Cell(170, 8,'Nombre del padre: '.utf8_decode($candidato['nombre_padre']),'',1);
		$pdf->Cell(170, 8,'Nombre de la madre: '.utf8_decode($candidato['nombre_madre']),'',1);
		if ($candidato['estado_civil']==0) {
			//Estado Civil S
			$pdf->Cell(170, 8,'Estado Civil: '.utf8_decode("Soltero/a"),'',1);
		}elseif ($candidato['estado_civil']==1) {
			//Estado Civil C
			$pdf->Cell(170, 8,'Estado Civil: '.utf8_decode("Casado/a"),'',1);
		}elseif ($candidato['estado_civil']==2) {
			//Estado Civil V
			$pdf->Cell(170, 8,'Estado Civil: '.utf8_decode("Viudo/a"),'',1);
		}elseif ($candidato['estado_civil']==3) {
			//Estado Civil D
			$pdf->Cell(170, 8,'Estado Civil: '.utf8_decode("Divorciado/a"),'',1);
		}elseif ($candidato['estado_civil']==4) {
			//Estado Civil Sp
			$pdf->Cell(170, 8,'Estado Civil: '.utf8_decode("Separado/a"),'',1);
		}
		$pdf->Cell(170, 8,'Lugar de nacimiento: '.utf8_decode($candidato['lugar_nac']),'',1);
		//-------------------------------------------------------Primera Página NIE
		$pdf->AddPage();
		$pdf->Image('img/page1-ex15.jpg', 0, 0, -150);
		//1)DATOS DEL EXTRANJERO
		$pdf->SetFont('Arial','',10);
		//Pasaporte, primero intentamos mostrar la Tarjeta Nacional de identidad (7) sino el pasaporte (2)
		if ($candidato['tipo_doc']==7) {
			$pdf->Text(37,61.5,utf8_decode($candidato['valor_doc']));
		}elseif ($candidato['tipo_doc_2']==7) {
			$pdf->Text(37,61.5,utf8_decode($candidato['valor_doc_2']));
		}elseif ($candidato['tipo_doc_3']==7) {
			$pdf->Text(37,61.5,utf8_decode($candidato['valor_doc_3']));
		}elseif($candidato['tipo_doc']==2){
			$pdf->Text(37,61.5,utf8_decode($candidato['valor_doc']));
		}elseif ($candidato['tipo_doc_2']==2) {
			$pdf->Text(37,61.5,utf8_decode($candidato['valor_doc_2']));
		}elseif ($candidato['tipo_doc_3']==2) {
			$pdf->Text(37,61.5,utf8_decode($candidato['valor_doc_3']));
		}
		//NIE 
		if ($candidato['tipo_doc']==5) {
			$pdf->Text(120,61.5,utf8_decode($candidato['valor_doc']));
		}elseif ($candidato['tipo_doc_2']==5) {
			$pdf->Text(120,61.5,utf8_decode($candidato['valor_doc_2']));
		}elseif ($candidato['tipo_doc_3']==5) {
			$pdf->Text(120,61.5,utf8_decode($candidato['valor_doc_3']));
		}
		//Datos personales
		$pdf->Text(35,68,utf8_decode($candidato['apellido1']));
		$pdf->Text(140,68,utf8_decode($candidato['apellido2']));
		$pdf->Text(30,74.5,utf8_decode($candidato['nombre']));
		$pdf->SetFont('Arial','',12);
		if ($candidato['sexo']=="Femenino") {
			$pdf->Text(190.5,74.4,"X");
		}else{
			$pdf->Text(181,74.4,"X");
		}
		$pdf->SetFont('Arial','',10);
		$fecha_nac = date_format($candidato['fecha_nac'], 'd/m/Y');
		$pdf->Text(50,81,substr($fecha_nac,0,2));
		$pdf->Text(60,81,substr($fecha_nac,3,2));
		$pdf->Text(70,81,substr($fecha_nac,6,4));
		$pdf->Text(92,81,utf8_decode($candidato['lugar_nac']));
		$pdf->Text(161,81,utf8_decode($candidato['pais_nac']));
		$pdf->Text(36,87.5,utf8_decode($candidato['nacionalidad']));
		$pdf->SetFont('Arial','',12);
		if ($candidato['estado_civil']==0) {
			//Estado Civil S
			$pdf->Text(147,87,"X");
		}elseif ($candidato['estado_civil']==1) {
			//Estado Civil C
			$pdf->Text(157.5,87,"X");
		}elseif ($candidato['estado_civil']==2) {
			//Estado Civil V
			$pdf->Text(167.2,87,"X");
		}elseif ($candidato['estado_civil']==3) {
			//Estado Civil D
			$pdf->Text(177.8,87,"X");
		}elseif ($candidato['estado_civil']==4) {
			//Estado Civil Sp
			$pdf->Text(189,87,"X");
		}
		$pdf->SetFont('Arial','',10);
		$pdf->Text(42,94,utf8_decode($candidato['nombre_padre']));
		$pdf->Text(138,94,utf8_decode($candidato['nombre_madre']));
		$pdf->Text(49,100.5,utf8_decode("FINCA LA RODANA - CTRA EL ROCIO KM25"));
		$pdf->Text(37,107,utf8_decode("ALMONTE"));
		$pdf->Text(130,107,"21730");
		$pdf->Text(170,107,"HUELVA");
		//3)DOMICILIO A EFECTOS DE NOTIFICACIONES
		$pdf->Text(47,202.6,utf8_decode("FINCA LA RODANA - CTRA. EL ROCIO KM25 - AP. CORREOS 116"));
		$pdf->Text(34,208.6,"ALMONTE");
		$pdf->Text(107,208.6,"21730");
		$pdf->Text(144,208.6,"HUELVA");
		//Check consentimiento de las comunicaciones
		$pdf->SetFont('Arial','',11);
		$pdf->Text(16.8,226,"X");
		//-------------------------------------------------------Segunda Página NIE
		$pdf->AddPage();
		$pdf->Image('img/page2-ex15.jpg', 0, 0, -150);
		$pdf->SetFont('Arial','',10);
		$pdf->Text(77,27.2,utf8_decode($candidato['nombre']." ".$candidato['apellido1']." ".$candidato['apellido2']));
		$pdf->Text(40,105.7,"TRABAJO");
		$pdf->SetFont('Arial','',11);
		//4.1 Tipo de documento
		$pdf->Text(24.2,68.9,"X");
		//4.2 Motivos
		$pdf->Text(92.9,97.8,"X");
		//4.3 Lugar
		$pdf->Text(24.2,129.5,"X");
		//4.4 Situación
		$pdf->Text(24.5,150.7,"X");
		//Fecha
		$pdf->SetFont('Arial','',10);
		$pdf->Text(100,194,"Almonte");
		$pdf->Text(130,194,date('d'));
		$pdf->Text(144,194,$Meses[(date('m'))-1]);
		$pdf->Text(172,194,date('Y'));
		//Obtenemos si la imagen es horinzontal o vertical
		$size = getimagesize('data:image/png;base64,'.$candidato["firma"]);
		if ($size[0]>$size[1]) {
			$pdf->Image("data:image/png;base64,".$candidato["firma"], 110, 209, -1000, 0, 'PNG');
		}else{
			$pdf->Image("data:image/png;base64,".$candidato["firma"], 116, 208, -1000, 0, 'PNG');
			// $label = imagecreatefromstring(base64_decode($candidato["firma"]));
			// $rotated_imaged = imagerotate($label, 90, 0);
			// imagepng($rotated_imaged, 'img/rotar/pruebafirma.png');
			// $pdf->Image("img/rotar/pruebafirma.png", 110, 209, -1000, 0, 'PNG');
			// unlink('img/rotar/pruebafirma.png');
		}
		$pdf->Text(26,286.2,"DIRECTOR/A OFICINA EXTRANJERIA");
		$pdf->Text(172,286.2,"HUELVA");
		//-------------------------------------------------------Tercera Página NIE
		$pdf->AddPage();
		$pdf->Image('img/page3-ex15.jpg', 0, 0, -150);
		//-------------------------------------------------------Cuarta Página precontrato
		$pdf->AddPage();
		//Precontrato Español
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(85,5,"", 'LTR',2,'C');
		$pdf->Cell(85,5,utf8_decode("PRECONTRATO"), 'LR',2,'C');
		$pdf->Cell(85,5,utf8_decode("SUREXPORT CÍA. AGRARIA S.L."), 'LR',2,'C');
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(85,10,"REUNIDOS", 'LR',2,'C');
		$pdf->SetFont('Arial','',10);
		$pdf->Multicell(85,5,utf8_decode("De una parte, SUREXPORT COMPAÑÍA AGRARIA S.L. (en adelante la Empresa), provista de C.I.F. número B21202817 y con domicilio social en Polígono Industrial Matalagrana S/N, C.P. 21.730, Almonte provincia de Huelva, España,"), 'LR','J');
		$pdf->Cell(85,10,"Y", 'LR',2,'C');
		//Pasaporte, primero intentamos mostrar la Tarjeta Nacional de identidad (7) sino el pasaporte (2)
		if ($candidato['tipo_doc']==7) {
			$dni = utf8_decode($candidato['valor_doc']);
		}elseif ($candidato['tipo_doc_2']==7) {
			$dni = utf8_decode($candidato['valor_doc_2']);
		}elseif ($candidato['tipo_doc_3']==7) {
			$dni = utf8_decode($candidato['valor_doc_3']);
		}elseif($candidato['tipo_doc']==2){
			$dni = utf8_decode($candidato['valor_doc']);
		}elseif ($candidato['tipo_doc_2']==2) {
			$dni = utf8_decode($candidato['valor_doc_2']);
		}elseif ($candidato['tipo_doc_3']==2) {
			$dni = utf8_decode($candidato['valor_doc_3']);
		}else{
			$dni = '';
		}
		$pdf->Multicell(85,5,utf8_decode("De la otra, D. / Dª. ".$candidato['nombre']." ".$candidato['apellido1']." ".$candidato['apellido2'].", provisto/a de documento de identidad nº ".$dni.", con fecha de nacimiento ".$fecha_nac.", nombre del padre ".$candidato['nombre_padre'].", nombre de la madre ".$candidato['nombre_madre'].", actuando en su propio nombre y representación.
			\nLas partes, en la representación con que intervienen, se reconocen mutuamente y, sin excepción, plena capacidad para la suscripción del presente documento, y a tal efecto "), 'LR','J');
		$pdf->Cell(85,10,"EXPONEN", 'LR',2,'C');
		$pdf->Multicell(85,5,utf8_decode("I. Que, habiendo realizado un proceso de reclutamiento en país origen de manera directa por parte de la Empresa Surexport Cía. Agraria S.L. y gratuita para el candidato/a, es de interés de la Empresa contar con el potencial trabajador/a indicado/a en el presente documento."), 'LR','J');
		$pdf->Multicell(85,5,utf8_decode("II. Que en el período de reclutamiento al potencial trabajador/a se le ha expuesto y es conocedor/a de la información relevante de la Empresa, su localización, la actividad agrícola a la que se dedicará y el tipo de trabajo que realizará a su llegada, en caso de que se realice su llamamiento."), 'LR','J');
		$pdf->Multicell(85,5,utf8_decode("III.	Que el potencial trabajador/a es conocerdor/a de las condiciones contractuales básicas que regirán su relación laboral a su llegada, así como derechos y obligaciones. A este respecto, el candidato/a reconoce haber recibido información impresa y preparada al efecto para que los candidatos/as interesados/as en el proceso de selección conozcan de manera transparente las condiciones de su contratación."), 'LR','J');
		$pdf->Multicell(85,5,utf8_decode("IV. Que el potencial trabajador/a ha sido informado del uso y las finalidades que la Empresa dará a sus datos personales, que está de acuerdo con que la Empresa los use para la finalidad de la gestión de la selección, traslado y contratación futura y que,"), 'LR','J');
		if ($candidato['idioma']=="bul") {
			//Precontrato Bulgaro
			$pdf->SetXY(105, 20);
			$pdf->SetFont('DejaVu','',11);
			$pdf->Cell(85,5,"", 'LTR',2,'C');
			$pdf->Cell(85,5,"ПРЕДВАРИТЕЛЕН ДОГОВОР", 'LR',2,'C');
			$pdf->SetFont('Arial','B',11);
			$pdf->Cell(85,5,utf8_decode("SUREXPORT CÍA. AGRARIA S.L."), 'LR',1,'C');
			$pdf->SetFont('DejaVu','',10);
			$pdf->Cell(85,10,"", '',0);
			$pdf->Cell(85,10,"ПРЕДСТАВИТЕЛИ", 'LR',1,'C');
			$pdf->Cell(85,10,"", '',0);
			$pdf->Multicell(85,5,"От една страна, SUREXPORT COMPAÑÍA AGRARIA S.L. (наричано по-нататък Дружеството),с БУЛСТАТ номер B21202817 и със седалище на адрес Polígono Industrial Matalagrana S / N, C.P. 21.730, Алмонте, провинция Уелва, Испания", 'LR','J');
			$pdf->Cell(85,10,"", '',0);
			$pdf->Cell(85,10,"И", 'LR',2,'C');
			$pdf->Multicell(85,5,"От друга страна, г-н/г-жа. ".utf8_decode($candidato['nombre']." ".$candidato['apellido1']." ".$candidato['apellido2']).", с номер на документ за самоличност ".$dni.", с дата на раждане ".$fecha_nac.", име на баща ".utf8_decode($candidato['nombre_padre']).", име на майка ".utf8_decode($candidato['nombre_padre']).", действащ от свое име и представяйки се. \n\nПредставените страни, които взимат участие, взаимно се признават и без изключение имат пълна дееспособност да подпишат този документ и за тази цел", 'LR','J');
			$pdf->Cell(85,10,"", '',0);
			$pdf->Cell(85,10,"ДЕКЛАРИРАТ", 'LR',2,'C');
			$pdf->Multicell(85,5,"I. Че, след като е извършил процес на набиране в страната на произход директно от Surexport Cía. Agraria S.L. и безплатно за кандидата, в интерес на Дружеството е да има в предвид потенциалният работник, посочен в този документ.", 'LR','J');
			$pdf->Cell(85,10,"", '',0);
			$pdf->Multicell(85,5,"II. Че в периода на интервюто на потенциалният работник му е представена и е запознат със съответната информация за Дружеството, нейното местоположение, селскостопанската дейност и вида работа, която ще извършва при пристигането си, в случай, че бъде одобрен  ", 'LR','J');
			$pdf->Cell(85,10,"", '',0);
			$pdf->Multicell(85,5,"III. Че потенциалният работник е запознат с основните договорни условия, които ще уреждат трудовото им правоотношение при пристигането, както и правата и задълженията. В тази връзка кандидатът потвърждава, че е получил информация, отпечатана и подготвена за тази цел, така че заинтересованите от процеса на подбор кандидати да знаят по прозрачен начин условията за тяхното наемане.", 'LR','J');
			$pdf->Cell(85,10,"", '',0);
			$pdf->Multicell(85,5,"IV. Че потенциалният работник е бил информиран за използването и целите,за които Дружеството ще използва личните му данни, че е съгласен, че същото ги използва за целите на управлението на подбора,", 'LR','J');
		}elseif ($candidato['idioma']=="ru") {
			//Precontrato Rumano
			$pdf->SetXY(105, 20);
			$pdf->SetFont('DejaVu','',11);
			$pdf->Cell(85,5,"", 'LTR',2,'C');
			$pdf->Cell(85,5,"PRECONTRACT", 'LR',2,'C');
			$pdf->SetFont('Arial','B',11);
			$pdf->Cell(85,5,utf8_decode("SUREXPORT CÍA. AGRARIA S.L."), 'LR',1,'C');
			$pdf->SetFont('DejaVu','',10);
			$pdf->Cell(85,10,"", '',0);
			$pdf->Cell(85,10,"INTALNIRE", 'LR',1,'C');
			$pdf->Cell(85,10,"", '',0);
			$pdf->Multicell(85,5,"Pe de o parte, SUREXPORT COMPAÑÍA AGRARIA S.L. (denumită în continuare Societatea), prevăzută cu numărul C.I.F. B21202817 și cu sediul social in Polígono Industrial Matalagrana S/N, C.P. 21.730, provincia Almonte din Huelva, Spania,", 'LR','J');
			$pdf->Cell(85,10,"", '',0);
			$pdf->Cell(85,10,"Și", 'LR',2,'C');
			$pdf->Multicell(85,5,"Pe de altă parte, D-l. / Dª. ".utf8_decode($candidato['nombre']." ".$candidato['apellido1']." ".$candidato['apellido2']).", prevazut cu actul de identitate nº ".$dni." ,cu data nasterii ".$fecha_nac.", numele tatalui ".utf8_decode($candidato['nombre_padre']).", numele mamei ".utf8_decode($candidato['nombre_madre']).", care se prezinta in persoana si reprezentare proprie.\n\nPărțile, în reprezentarea cu care intervin, se recunosc reciproc și, fără excepție, capacitatea deplină de subscriere a prezentului document și în acest scop.", 'LR','J');
			$pdf->Cell(85,10,"", '',0);
			$pdf->Cell(85,10,"EXPUS", 'LR',2,'C');
			$pdf->Multicell(85,5,"I. Că, având efectuat un proces de recrutare în țara de origine direct de către Societatea Surexport Cía. Agraria S.L. și gratuit pentru candidat, este în interesul Societății să aibă potențialul lucrător indicat în acest document.", 'LR','J');
			$pdf->Cell(85,10,"", '',0);
			$pdf->Multicell(85,5,"II. Că în perioada de recrutare potențialul lucrător a fost expus și cunoaște informațiile relevante despre Societate, locația acesteia, activitatea agricolă căreia i se va dedica și tipul de muncă pe care o va presta la sosire, în cazul în care se face contestația dvs. ", 'LR','J');
			$pdf->Cell(85,10,"", '',0);
			$pdf->Multicell(85,5,"III. Că potențialul lucrător cunoaște condițiile contractuale de bază care îi vor guverna relația de muncă la sosire, precum și drepturile și obligațiile. În acest sens, candidatul recunoaște că a primit informații tipărite întocmite în acest scop, astfel încât candidații interesați de procesul de selecție să cunoască în mod transparent condițiile angajării lor.", 'LR','J');
			$pdf->Cell(85,10,"", '',0);
			$pdf->Multicell(85,5,"IV. Că potențialul lucrător a fost informat despre utilizarea și scopurile pe care Compania le va acorda datelor sale personale, că este de acord cu Compania să le utilizeze în scopul gestionării selecției, transferului și angajărilor viitoare și că, în orice În cazul în care, aveți drepturi de acces, rectificare, ștergere, portabilitate,", 'LR','J');
		}

		//-------------------------------------------------------Quinta Página precontrato
		//Precontrato Español
		$pdf->AddPage();
		$pdf->SetFont('Arial','',10);
		$pdf->Multicell(85,5,utf8_decode("en cualquier caso, le asisten los derechos de acceso, rectificación, supresión, portabilidad, limitación del tratamiento, supresión o, en su caso, oposición, de acuerdo con la Ley Orgánica 3/2018, de 5 de diciembre, de Protección de Datos Personales y garantía de los derechos digitales, conforme al procedimiento establecido por la Empresa. Este consiste en presentar un escrito en la dirección abajo indicada, dirigido al Delegado de Protección de Datos. Deberá especificar cuál de estos derechos solicita sea satisfecho y, a su vez, deberá acompañarse de la fotocopia del DNI/NIE o documento identificativo equivalente. Dirección postal o de correo electrónico a la que deberá dirigir el escrito:"), 'LR','J');
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(85,10,utf8_decode("SUREXPORT COMPAÑÍA AGRARIA, S.L."), 'LR',2,'C');
		$pdf->Multicell(85,5,utf8_decode("Polígono Matalagrana S/N, Apdo:116. Almonte, Huelva.
			Tel: +34 959451550
			E-mail - DPD: surexport@surexport.es"), 'LR','C');
		$pdf->SetFont('Arial','',11);
		$pdf->Multicell(85,5,utf8_decode("\nV. Que el presente documento no constituye un contrato de trabajo ni compromiso de contratación, en tanto el candidato/a no sea llamado y haya llegado a las instalaciones de la empresa sitas en el inicio de este documento, momento en el que sí cobran sentido. Todo ello al amparo de la libertad de la persona preseleccionada en finalmente concurrir o no al puesto de trabajo.\n\n\n"), 'LR','J');
		if ($candidato['idioma']=="bul") {
			//Precontrato Bulgaro
			$pdf->SetXY(105, 20);
			$pdf->SetFont('DejaVu','',10);
			$pdf->Multicell(85,5,"превоза и бъдещото наемане и че, във всеки един момент  са предоставени  правата за достъп, коригиране, прехвърляне, ограничаване на третирането, изтриване или, когато е уместно, противопоставяне, в съответствие с Органичния закон 3/2018 от 5 декември за защита на личните данни и гарантиране на цифрови права, по установения от Дружеството ред. Това се състои в изпращане на писмо на посочения по-долу адрес, адресирано до длъжностното лице по защита на данните. Трябва да посочите кои от тези права искате да бъдат приложени и от своя страна трябва да бъдат придружени от фотокопие на ЛК / NIE или еквивалентен документ за самоличност. Пощенски или имейл адрес, на който трябва да бъде адресирано писмото:", 'LR','J');
			$pdf->SetFont('Arial','B',11);
			$pdf->Cell(85,10,"", '',0);
			$pdf->Cell(85,10,utf8_decode("SUREXPORT COMPAÑÍA AGRARIA, S.L."), 'LR',2,'C');
			$pdf->Multicell(85,5,utf8_decode("Polígono Matalagrana S/N, Apdo:116. Almonte, Huelva.
				Tel: +34 959451550
				E-mail - DPD: surexport@surexport.es"), 'LR','C');
			$pdf->SetFont('DejaVu','',10);
			$pdf->Cell(85,10,"", '',0);
			$pdf->Multicell(85,5,"\nV. Че този документ не представлява договор за работа или договорен ангажимент, освен ако кандидатът не е извикан и да е пристигнал в съоръженията на компанията, разположени в началото на този документ, в който момент те имат смисъл. Всичко това след свободното избиране на предварително избрания кандидат да присъства на работата или не.", 'LR','J');
		}elseif ($candidato['idioma']=="ru") {
			//Precontrato Rumano
			$pdf->SetXY(105, 20);
			$pdf->SetFont('DejaVu','',10);
			$pdf->Multicell(85,5,"limitare a prelucrării, ștergere sau, după caz, opoziție, în conformitate cu Legea organică 3/2018, din 5 decembrie, privind protecția datelor cu caracter personal și garantarea drepturi digitale, în conformitate cu procedura stabilită de Companie. Aceasta constă în transmiterea unei scrisori la adresa indicată mai jos, adresată Responsabilului cu protecția datelor. Trebuie să specificați care dintre aceste drepturi solicitați să fie îndeplinite și, la rândul său, trebuie să fie însoțit de o fotocopie a DNI/NIE sau a unui act de identificare echivalent. Adresa poștală sau de e-mail la care trebuie să trimiteți scrisoarea:", 'LR','J');
			$pdf->SetFont('Arial','B',11);
			$pdf->Cell(85,10,"", '',0);
			$pdf->Cell(85,10,utf8_decode("SUREXPORT COMPAÑÍA AGRARIA, S.L."), 'LR',2,'C');
			$pdf->Multicell(85,5,utf8_decode("Polígono Matalagrana S/N, Apdo:116. Almonte, Huelva.
				Tel: +34 959451550
				E-mail - DPD: surexport@surexport.es"), 'LR','C');
			$pdf->SetFont('DejaVu','',10);
			$pdf->Cell(85,10,"", '',0);
			$pdf->Multicell(85,5,"\nV. Că acest document nu constituie un contract de muncă sau un angajament de angajare, atâta timp cât candidatul nu este chemat și a ajuns la unitățile companiei situate la începutul acestui document, moment în care au sens. Toate acestea sub protecția libertății persoanei preselectate de a participa sau nu în final la job.", 'LR','J');
			$pdf->Line(190, 155, 190, 180);
			$pdf->Ln(25);
		}
		$pdf->Cell(170,5,"",1,1);
		$pdf->SetFont('Arial','',10);
		$pdf->Ln(10);
		$pdf->Cell(170,10,"En prueba de conformidad, rogamos firme la presente en Bulgaria a ".date('d/m/Y'),0,2);
		$pdf->SetFont('DejaVu','',10);
		if ($candidato['idioma']=="bul") {
			$pdf->Cell(170,10,"В доказателство за съответствие, ви молим да подпишете в България на ".date('d/m/Y'),0,2);
		}elseif ($candidato['idioma']=="ru") {
			$pdf->Cell(170,10,"În dovada conformității, vă rugăm să semnați prezentul în România la ".date('d/m/Y'),0,2);
		}
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(85,5,utf8_decode("Por la Empresa\n"), '0',0,'C');
		$pdf->Cell(85,5,"Por el/la candidata/a", '0',1,'C');
		$pdf->Image('img/Firma_surex.jpg', 30, 235, -100);
		$pdf->Cell(85,5,utf8_decode("D./Dª ___________"), '0',0,'C');
		$pdf->Cell(85,5,utf8_decode("D./Dª ".$candidato['nombre']." ".$candidato['apellido1']." ".$candidato['apellido2']), '0',1,'C');
		//Comprobamos la horientación de la imagen
		$size = getimagesize('data:image/png;base64,'.$candidato["firma"]);
		if ($size[0]>$size[1]) {
			$pdf->Image("data:image/png;base64,".$candidato["firma"], 110, 235, -600, 0, 'PNG');
		}else{
			$pdf->Image("data:image/png;base64,".$candidato["firma"], 130, 230, -1000, 0, 'PNG');
		}
		//---------------------------------------------------------Sexta Página FresHuelva
		$pdf->AddPage();
		$pdf->SetFont('Arial','U',11);
		$pdf->Image('img/logo_freshuelva.png', 20, 20);
		$pdf->Ln(40);
		$pdf->Cell(170,8,utf8_decode("AUTORIZACIÓN PARA LA RECOGIDA DEL NIE"),0,1,'C');
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(170,5,'',0,1);
		$pdf->Cell(170,8,utf8_decode("El trabajador/a D/Dña. ".$candidato['nombre']." ".$candidato['apellido1']." ".$candidato['apellido2']." de "),0,1);
		$pdf->Cell(170,5,'',0,1);
		$pdf->Cell(170,8,utf8_decode("nacionalidad ".$candidato['nacionalidad'].", con pasaporte /tarjeta nº ".$dni),0,1);
		$pdf->Cell(170,5,'',0,1);
		$pdf->Cell(170,8,utf8_decode("AUTORIZA expresamente a FRESHUELVA para recoger su NIE."),0,1);
		$pdf->Cell(170,5,'',0,1);
		$pdf->Cell(170,8,utf8_decode("Firmado:"),0,1);
		//Comprobamos la horientación de la imagen
		$size = getimagesize('data:image/png;base64,'.$candidato["firma"]);
		if ($size[0]>$size[1]) {
			$pdf->Image("data:image/png;base64,".$candidato["firma"], 50, 110, -600, 0, 'PNG');
		}else{
			$pdf->Image("data:image/png;base64,".$candidato["firma"], 40, 110, -1000, 0, 'PNG');
		}
		$pdf->Ln(40);
		$pdf->SetFont('DejaVu','U',11);
		if ($candidato['idioma']=="bul") {
			$pdf->Cell(170,8,"ПЪЛНОМОЩНО ЗА ПОЛУЧАВАНЕ НА НИЕ",0,1,'C');
			$pdf->SetFont('DejaVu','',10);
			$pdf->Cell(170,5,'',0,1);
			$pdf->Cell(170,8,"Г-н/Г-жа. ".utf8_decode($candidato['nombre']." ".$candidato['apellido1']." ".$candidato['apellido2'])." с ",0,1);
			$pdf->Cell(170,5,'',0,1);
			$pdf->Cell(170,8,"националност ".$candidato['nacionalidad'].", и Паспорт/ЛК nº ".$dni,0,1);
			$pdf->Cell(170,5,'',0,1);
			$pdf->Cell(170,8,"Упълномощава единствено FRESHUELVA да получи неговото НИЕ.",0,1);
		}elseif ($candidato['idioma']=="ru") {
			$pdf->Cell(170,8,"AUTORIZAȚIE PENTRU RIDICARE NIE",0,1,'C');
			$pdf->SetFont('DejaVu','',10);
			$pdf->Cell(170,5,'',0,1);
			$pdf->Cell(170,8,"Lucrătorul Domnul/Doamna: ".utf8_decode($candidato['nombre']." ".$candidato['apellido1']." ".$candidato['apellido2'])." de ",0,1);
			$pdf->Cell(170,5,'',0,1);
			$pdf->Cell(170,8,"naționalitate ".$candidato['nacionalidad']." cu Pasaport/B.i /Numar ".$dni,0,1);
			$pdf->Cell(170,5,'',0,1);
			$pdf->Cell(170,8,"AUTORIZEAZĂ in mod expres a  FRESHUELVA pentru a ridica el  NIE.",0,1);
		}
	}	
	//Mostramos el pdf en el navegador
	$pdf->Output();


} elseif (isset($_GET['excel'])) {
	if (isset($_SESSION['array_candidatos'])) {
		
		
		
		try {
            ob_clean(); // Limpiar el buffer
            
            //Creamos el archivo
            $documento = new Spreadsheet();
            $sheet = $documento->getActiveSheet();




			//Creamos el archivo
			$documento = new Spreadsheet();
			$sheet = $documento->getActiveSheet();
			//Establecemos el nombre
			$nombreDelDocumento = "Remesa_".date('d-m-Y'). ".xlsx";
			//Creamos la hoja de Encargados
			$sheet->setTitle("Remesa");
			//Añadimos la imagen
			$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
			$drawing->setName('Paid');
			$drawing->setDescription('FresHuelva');
			$drawing->setPath('img/logo_freshuelva.png');
			$drawing->setWidth(230);
			$drawing->setCoordinates('B1');
			$drawing->setWorksheet($documento->getActiveSheet());
			//Establecemos un tamaño fijo de columna
			$sheet->getDefaultRowDimension()->setRowHeight(21);
			//$sheet->getRowDimension('1')->setRowHeight(21);
			$sheet->getColumnDimension('A')->setWidth(5);
			$sheet->getColumnDimension('B')->setWidth(30);
			$sheet->getColumnDimension('C')->setWidth(30);
			$sheet->getColumnDimension('D')->setWidth(15);
			$sheet->getColumnDimension('E')->setWidth(15);
			$sheet->getColumnDimension('F')->setWidth(15);
			$sheet->getColumnDimension('G')->setWidth(15);
			$sheet->getColumnDimension('H')->setWidth(15);
			$sheet->getColumnDimension('I')->setWidth(15);
			$sheet->getColumnDimension('J')->setWidth(15);
			$sheet->getColumnDimension('K')->setWidth(15);
			//Mostramos la cabecera
			$sheet->getStyle('A5:K5')->getFont()->setBold(true);
			$sheet->getStyle('A5:K5')->getFill()->setFillType("solid")->getStartColor()->setARGB('FFE5E5E5');
			$sheet->setCellValue('A5', '');
			$sheet->setCellValue('B5', 'APELLIDO');
			$sheet->setCellValue('C5', 'NOMBRE');
			$sheet->setCellValue('D5', 'PASAPORTE');
			$sheet->setCellValue('E5', 'F.NACIM.');
			$sheet->setCellValue('F5', 'PADRE');
			$sheet->setCellValue('G5', 'MADRE');
			$sheet->setCellValue('H5', 'E. CIVIL');
			$sheet->setCellValue('I5', 'SEXO');
			$sheet->setCellValue('J5', 'NACION.');
			$sheet->setCellValue('K5', 'NIE');
			$sheet->getStyle('A5:K5')->getAlignment()->setHorizontal('center');
			$sheet->getStyle('A5:K5')->getBorders()->getAllBorders()->setBorderStyle("thin");
			//Mostramos los datos de los candidatos
			$i=6;
			$num=1;
			foreach ($_SESSION['array_candidatos'] as $candidato) {
				$fecha_nac = date_format($candidato['fecha_nac'], 'd/m/Y');
				$sheet->setCellValue('A'.$i, $num);
				$sheet->setCellValue('B'.$i, $candidato['apellido1']);
				$sheet->setCellValue('C'.$i, $candidato['nombre']);
				//Pasaporte, primero intentamos mostrar la Tarjeta Nacional de identidad (7) sino el pasaporte (2)
				if ($candidato['tipo_doc']==7) {
					$sheet->setCellValue('D'.$i, $candidato['valor_doc']);
				}elseif ($candidato['tipo_doc_2']==7) {
					$sheet->setCellValue('D'.$i, $candidato['valor_doc_2']);
				}elseif ($candidato['tipo_doc_3']==7) {
					$sheet->setCellValue('D'.$i, $candidato['valor_doc_3']);
				}elseif($candidato['tipo_doc']==2){
					$sheet->setCellValue('D'.$i, $candidato['valor_doc']);
				}elseif ($candidato['tipo_doc_2']==2) {
					$sheet->setCellValue('D'.$i, $candidato['valor_doc_2']);
				}elseif ($candidato['tipo_doc_3']==2) {
					$sheet->setCellValue('D'.$i, $candidato['valor_doc_3']);
				}
				$sheet->setCellValue('E'.$i, $fecha_nac);
				$sheet->setCellValue('F'.$i, $candidato['nombre_padre']);
				$sheet->setCellValue('G'.$i, $candidato['nombre_madre']);
				if ($candidato['estado_civil']==0) {
					//Estado Civil S
					$sheet->setCellValue('H'.$i, 'Soltero/a');
				}elseif ($candidato['estado_civil']==1) {
					//Estado Civil C
					$sheet->setCellValue('H'.$i, 'Casado/a');
				}elseif ($candidato['estado_civil']==2) {
					//Estado Civil V
					$sheet->setCellValue('H'.$i, 'Viudo/a');
				}elseif ($candidato['estado_civil']==3) {
					//Estado Civil D
					$sheet->setCellValue('H'.$i, 'Divorciado/a');
				}elseif ($candidato['estado_civil']==4) {
					//Estado Civil Sp
					$sheet->setCellValue('H'.$i, 'Separado/a');
				}
				$sheet->setCellValue('I'.$i, $candidato['sexo']);
				$sheet->setCellValue('J'.$i, $candidato['nacionalidad']);
				$sheet->setCellValue('K'.$i, '');
				$sheet->getStyle('A'.$i.':K'.$i)->getAlignment()->setHorizontal('center');
				$sheet->getStyle('A'.$i.':K'.$i)->getBorders()->getAllBorders()->setBorderStyle("thin");
				$i++;
				$num++;
			}








			//Descargamos el archivo
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="' . $nombreDelDocumento . '"');
			header('Cache-Control: max-age=0');
			ob_end_clean(); // Limpiar el buffer antes de la salida
            $writer = IOFactory::createWriter($documento, 'Xlsx');
            $writer->save('php://output');
            exit;
        } catch (Exception $e) {
            ob_end_clean();
            echo "Error al generar el Excel: " . $e->getMessage();
        }
    }






} elseif (isset($_GET['informe_presencia_pdf'])) {
    // Creación del objeto de la clase heredada
    $pdf = new tFPDF('L');
    $pdf->SetMargins(19,20,20); // Margen de la tabla
    // $pdf->SetAutoPageBreak(true, 20);
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial','',10);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetDrawColor(0,0,0);
    $pdf->SetTextColor(153,53,58);
    $pdf->Text(20,12,utf8_decode("SUREXPORT COMPAÑÍA AGRARIA, S.L."));
    $pdf->SetTextColor(0,0,0);
    $pdf->Text(20,16,utf8_decode("Poligono Matalagrana S/N, Apdo: 116"));
    $pdf->Text(20,20,utf8_decode("CIF B21202817"));
    $pdf->Text(20,24,utf8_decode("+34 959451550 | surexport@surexport.es"));
    $pdf->Text(20,198,utf8_decode("Fecha de impresión: ".date('Y-m-d H:i:s')));
    
    // Colocar el logo en la primera página en su posición original
    $pdf->Image('img/logo-home.png', 233, 7, 44);
    
    $pdf->SetFont('Arial','B',14);
    
    // Título del documento
    $titulo= utf8_encode("Informe Presencia Campo");
    $pdf->Multicell(255,15,$titulo,0,'C');
    
    $pdf->SetFont('Arial','B',9);
    // Cabecera de la tabla
    $pdf->SetFillColor(222,222,222);
    $pdf->Cell(23,6,utf8_decode('Cod. Trab'),1,0,'C',true);
    $pdf->Cell(77,6,"Nombre y apellidos",1,0,'C',true);
    $pdf->Cell(23,6,utf8_decode('Fecha Inicio'),1,0,'C',true);
    $pdf->Cell(22,6,utf8_decode('Hora Inicio'),1,0,'C',true);
    $pdf->Cell(21,6,utf8_decode("Fecha Fin"),1,0,'C',true);
    $pdf->Cell(19,6,utf8_decode("Hora Fin"),1,0,'C',true);
    $pdf->Cell(22,6,utf8_decode("Horas Netas"),1,0,'C',true);
    $pdf->Cell(18,6,utf8_decode("Descanso"),1,0,'C',true);
    $pdf->Cell(35,6,"Finca",1,1,'C',true);
    
    $datosInforme = $con_bdsrx->informePresencia($_POST['fincas_informe'], $_POST['desde_informe'], $_POST['hasta_informe'], $_POST['sociedad_informe'], $_POST['division_informe'], $_POST['operario_informe']);
    if (!empty($datosInforme)) {
		// Función para convertir horas decimales a formato HH:mm
		function decimalToTime($decimalHour) {
			$hours = floor($decimalHour); // Parte entera: horas
			$minutes = round(($decimalHour - $hours) * 60); // Parte decimal * 60 para convertir a minutos
		    return sprintf('%02d:%02d', $hours, $minutes);
		}

		foreach ($datosInforme as $resultado) {
			// Convertir las horas de descanso y producción a formato HH:mm
			// $HorasDescanso = ($resultado['HorasDescanso'] == ".00") ? "00:00:00" : decimalToTime($resultado['HorasDescanso']);
    		// $HorasNetasProduccion = ($resultado['HorasNetasProduccion'] == ".00") ? "00:00:00" : decimalToTime($resultado['HorasNetasProduccion']);
			$pdf->SetFont('Arial', '', 9);
			$pdf->Cell(23, 6, $resultado['CodOperario'], 1, 0, 'C');
			$pdf->Cell(77, 6, $resultado['NombreOperario'], 1, 0);
			$pdf->Cell(23, 6, date_format(new DateTime($resultado['InicioPresencia']), 'd/m/Y'), 1, 0, 'C');
			$pdf->Cell(22, 6, date_format(new DateTime($resultado['InicioPresencia']), 'H:i'), 1, 0, 'C');
			$pdf->Cell(21, 6, date_format(new DateTime($resultado['FinPresencia']), 'd/m/Y'), 1, 0, 'C');
			$pdf->Cell(19, 6, date_format(new DateTime($resultado['FinPresencia']), 'H:i'), 1, 0, 'C');
			$pdf->Cell(22, 6, $resultado['HorasNetas'], 1, 0, 'C');
			$pdf->Cell(18, 6, $resultado['MinutosDescanso'], 1, 0, 'C');
			$pdf->Cell(35, 6, $resultado['Finca'], 1, 1, 'C');

			if($pdf->GetY() > 180) {// Ajusta este valor según sea necesario
                $pdf->AddPage();
                
                // Coloca el logo centrado en las páginas subsiguientes
                $logoWidth = 35; // Ancho del logo
                $pageWidth = $pdf->GetPageWidth();
                $x = ($pageWidth - $logoWidth) / 2;
                $pdf->Image('img/logo-home.png', $x, 5, $logoWidth);
            }
        }
    } else {
		// redirigimos a la página de informes
		header("Location: admin_cont.php?controller=index&action=exportar");
		exit;
	}

	//Mostramos el pdf en el navegador
	$nombreArchivo = 'Informe_Presencia_Campo_'.date('Y-m-d H:i:s').'.pdf';
	$pdf->Output('I', $nombreArchivo);


} elseif (isset($_GET['informe_presencia_excel'])) {
	if (isset($_POST['fincas_informe']) and isset($_POST['desde_informe']) and isset($_POST['hasta_informe'])) {			
		try {
            ob_clean();
            
            //Creamos el archivo
            $documento = new Spreadsheet();
            $sheet = $documento->getActiveSheet();
			//Creamos la hoja de Informe PRESENCIA
			$sheet->setTitle("INFORME PRESENCIA");
			//Establecemos el nombre
			$nombreDelDocumento = "Informe_Presencia_".$_POST['desde_informe']."_".$_POST['hasta_informe'].".xlsx";
			//Establecemos un tamaño fijo de columna
			$sheet->getColumnDimension('A')->setWidth(18);
			$sheet->getColumnDimension('B')->setWidth(35);
			// $sheet->getColumnDimension('C')->setWidth(25);
			$sheet->getColumnDimension('C')->setWidth(22);
			$sheet->getColumnDimension('D')->setWidth(22);
			$sheet->getColumnDimension('E')->setWidth(22);
			$sheet->getColumnDimension('F')->setWidth(22);
			$sheet->getColumnDimension('G')->setWidth(16);
			$sheet->getColumnDimension('H')->setWidth(13);
			$sheet->getColumnDimension('I')->setWidth(15);

			//Mostramos la cabecera
			$sheet->getStyle('A1:I1')->getFont()->setBold(true);
			$sheet->getStyle('A1:I1')->getFill()->setFillType("solid")->getStartColor()->setARGB('FFE5E5E5');
			$sheet->setCellValue('A1', 'Cod. Trab');
			$sheet->setCellValue('B1', 'Nombre');
			// $sheet->setCellValue('C1', 'Apellidos');
			$sheet->setCellValue('C1', 'FechaInicioProduccion');
			$sheet->setCellValue('D1', 'HoraInicioProduccion');
			$sheet->setCellValue('E1', 'FechaFinProduccion');
			$sheet->setCellValue('F1', 'HoraFinProduccion');
			$sheet->setCellValue('G1', 'Horas Netas');
			$sheet->setCellValue('H1', 'Descanso');
			$sheet->setCellValue('I1', 'Finca');
		
			//Consultamos los datos
			$datos_presencia = $con_bdsrx->informePresencia($_POST['fincas_informe'], $_POST['desde_informe'], $_POST['hasta_informe'], $_POST['sociedad_informe'], $_POST['division_informe'], $_POST['operario_informe']);
			if(!empty($datos_presencia)){
				$i=2;
				foreach($datos_presencia as $resultado) {

					$sheet->setCellValue('A'.$i, $resultado['CodOperario']);
					$sheet->setCellValue('B'.$i, $resultado['NombreOperario']);
					// $sheet->setCellValue('C'.$i, $resultado['Apellido1Ope']." ".$resultado['Apellido2Ope']);
					$inicio = new DateTime($resultado['InicioPresencia']);
					$fin = new DateTime($resultado['FinPresencia']);

					$sheet->setCellValue('C'.$i, $inicio->format('d/m/Y'));
					$sheet->getStyle('C'.$i)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);

					$sheet->setCellValue('D'.$i, $inicio->format('H:i:s'));

					$sheet->setCellValue('E'.$i, $fin->format('d/m/Y'));
					$sheet->getStyle('E'.$i)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);

					$sheet->setCellValue('F'.$i, $fin->format('H:i:s'));


					// Suponiendo que $resultado['HorasNetasProduccion'] contiene el valor en horas decimales
					$valor_decimal = $resultado['HorasNetas'];
					// Convertir el valor decimal a minutos
					$minutos_totales = $valor_decimal * 60;
					// Calcular las horas y los minutos
					$horas = floor($minutos_totales / 60);
					$min = round($minutos_totales % 60);
					// Formatear la cadena como "h:m"
					$horas_netas_produccion = $horas . ':' . str_pad($min, 2, '0', STR_PAD_LEFT);
					// Establecer el valor de la celda con el formato "h:m"
					$sheet->setCellValue('G' . $i, $horas_netas_produccion);
					
					//$sheet->setCellValue('H'.$i, $resultado['HorasNetasProduccion']);


					$sheet->setCellValue('H'.$i, $resultado['MinutosDescanso']);
					$sheet->setCellValue('I'.$i, $resultado['Finca']);
					// $sheet->setCellValue('W'.$i, $resultado['CodFinca']);
					$i++;
				}				
				//Pintamos el borde de la tabla
				$sheet->getStyle('A1:I'.($i-1))->getBorders()->getAllBorders()->setBorderStyle("thin");
				//Centramos las columnas
				$sheet->getStyle('A1:I'.($i-1))->getAlignment()->setHorizontal('center');
				$sheet->getStyle('A1:I'.($i-1))->getAlignment()->setVertical('center');
			}else{
				//si la consulta no tiene datos, se exporta el fichero con el texto "No se han encontrado datos de esta finca y fecha"
				$sheet->mergeCells('A3:I3');
				$sheet->setCellValue('A3', "No se han encontrado registros");
				//Pintamos el borde de la tabla
				$sheet->getStyle('A3:I3')->getBorders()->getAllBorders()->setBorderStyle("thin");
				//Centramos las columnas
				$sheet->getStyle('A3:I3')->getAlignment()->setHorizontal('center');
				$sheet->getStyle('A3:I3')->getAlignment()->setVertical('center');
			}
			//Descargamos el archivo
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $nombreDelDocumento . '"');
            header('Cache-Control: max-age=0');
            
            ob_end_clean();
            $writer = IOFactory::createWriter($documento, 'Xlsx');
            $writer->save('php://output');
            exit;
        } catch (Exception $e) {
            ob_end_clean();
            echo "Error al generar el Excel: " . $e->getMessage();
        }
    } else {
        exit("<h3 style='margin-top: 20px;'>Debe seleccionar finca, fecha de inicio y fecha de fin para poder generar el informe.</h3>");
    }


} elseif (isset($_GET['informe_presencia_ofi_pdf'])) {
    // Creación del objeto de la clase heredada
    $pdf = new tFPDF('L');
    $pdf->SetMargins(19,20,20); // Margen de la tabla
    // $pdf->SetAutoPageBreak(true, 20);
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial','',10);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetDrawColor(0,0,0);
    $pdf->SetTextColor(153,53,58);
    $pdf->Text(20,12,utf8_decode("SUREXPORT COMPAÑÍA AGRARIA, S.L."));
    $pdf->SetTextColor(0,0,0);
    $pdf->Text(20,16,utf8_decode("Poligono Matalagrana S/N, Apdo: 116"));
    $pdf->Text(20,20,utf8_decode("CIF B21202817"));
    $pdf->Text(20,24,utf8_decode("+34 959451550 | surexport@surexport.es"));
    $pdf->Text(20,198,utf8_decode("Fecha de impresión: ".date('Y-m-d H:i:s')));
    
    // Colocar el logo en la primera página en su posición original
    $pdf->Image('img/logo-home.png', 233, 7, 44);
    
    $pdf->SetFont('Arial','B',14);
    
    // Título del documento
    $titulo= utf8_encode("Informe Presencia Oficina");
    $pdf->Multicell(255,15,$titulo,0,'C');
    
    $pdf->SetFont('Arial','B',9);
    // Cabecera de la tabla
    $pdf->SetFillColor(222,222,222);
    $pdf->Cell(27,6,utf8_decode('Cod. Trab'),1,0,'C',true);
    $pdf->Cell(63,6,"Nombre y apellidos",1,0,'C',true);
    $pdf->Cell(26,6,utf8_decode('Fecha Registro'),1,0,'C',true);
	$pdf->Cell(26,6,utf8_decode('Hora Registro'),1,0,'C',true);
	$pdf->Cell(30,6,utf8_decode('Tipo Registro'),1,0,'C',true);
	$pdf->Cell(40,6,utf8_decode('Sede'),1,0,'C',true);
	$pdf->Cell(35,6,utf8_decode('Ubicación'),1,0,'C',true);
	$pdf->Cell(15,6,utf8_decode('Manual'),1,1,'C',true);
    
    $datosInforme = $con_bdsrx->informePresenciaOficina($_POST['desde_informe_ofi'], $_POST['hasta_informe_ofi'], $_POST['tipo_reg_informe_ofi'], $_POST['pernr_informe_ofi'], $_POST['reg_manual_ofi'], $_POST['sede_informe_ofi'], $_POST['ubi_informe_ofi']);
    if (!empty($datosInforme)) {
		// Función para convertir horas decimales a formato HH:mm
		function decimalToTime($decimalHour) {
			$hours = floor($decimalHour); // Parte entera: horas
			$minutes = round(($decimalHour - $hours) * 60); // Parte decimal * 60 para convertir a minutos
		    return sprintf('%02d:%02d', $hours, $minutes);
		}

		
		foreach ($datosInforme as $resultado) {

			// Inicializar $nombre para evitar el warning de variable indefinida
			$nombre = '';

			// Mostrar el nombre completo del trabajador
			if ($resultado['APELLIDO1'] != '' && $resultado['NOMBRE'] != '' ) {
				// Si existen APELLIDO1 y NOMBRE, mostrar en formato: APELLIDO1 APELLIDO2, NOMBRE
				$nombre = $resultado['APELLIDO1'];
		
				if ($resultado['APELLIDO2'] != '') {
					$nombre .= ' ' . $resultado['APELLIDO2'];
				}
		
				$nombre .= ', ' . $resultado['NOMBRE'];
			} elseif ($resultado['NOMBREYAPELLIDOS'] != '') {
				// Si existe el campo NOMBREYAPELLIDOS completo
				$nombre = $resultado['NOMBREYAPELLIDOS'];
			} else {
				// Si no hay datos disponibles
				$nombre = 'Desconocido';
			}

			if ($resultado['fecha_reg'] != '') {
				$fecha_reg = date_format($resultado['fecha_reg'], 'Y/m/d');
				$fecha_reg_hora = date_format($resultado['fecha_reg'], 'H:i:s');
			} else {
				$fecha_reg = '';
				$fecha_reg_hora = '';
			}

			// Convertir las horas de descanso y producción a formato HH:mm
			$pdf->SetFont('Arial', '', 9);
			$pdf->Cell(27, 6, $resultado['pernr'], 1, 0, 'C');
			$pdf->Cell(63, 6, utf8_decode($nombre), 1, 0);
			$pdf->Cell(26, 6, $fecha_reg, 1, 0, 'C');
			$pdf->Cell(26, 6, $fecha_reg_hora, 1, 0, 'C');
			$pdf->Cell(30, 6, utf8_decode($resultado['tipo_reg']), 1, 0, 'C');
			$pdf->Cell(40, 6, utf8_decode($resultado['sede_ubi']), 1, 0, 'C');
			$pdf->Cell(35, 6, utf8_decode($resultado['nombre_ubi']), 1, 0, 'C');
			$pdf->Cell(15, 6, utf8_decode($resultado['manual']), 1, 1, 'C');

			if($pdf->GetY() > 190) { // Ajusta este valor según sea necesario
                $pdf->AddPage();
                
                // Coloca el logo centrado en las páginas subsiguientes
                $logoWidth = 35; // Ancho del logo
                $pageWidth = $pdf->GetPageWidth();
                $x = ($pageWidth - $logoWidth) / 2;
                $pdf->Image('img/logo-home.png', $x, 5, $logoWidth);
            }
        }
    }

	
	//Mostramos el pdf en el navegador
	$nombreArchivo = 'Informe_Presencia_Oficina_'.date('Y-m-d H:i:s').'.pdf';
	$pdf->Output('I', $nombreArchivo);


} elseif (isset($_GET['informe_presencia_ofi_excel'])) {
	if (isset($_POST['tipo_reg_informe_ofi']) and isset($_POST['desde_informe_ofi']) and isset($_POST['hasta_informe_ofi'])) {			
		
		try {
            ob_clean();
            
            //Creamos el archivo
            $documento = new Spreadsheet();
            $sheet = $documento->getActiveSheet();







			//Creamos la hoja de Informe PRESENCIA
			$sheet->setTitle("INFORME PRESENCIA OFICINA");
			//Establecemos el nombre
			$nombreDelDocumento = "Informe_Presencia_Oficina_".$_POST['desde_informe_ofi']."_".$_POST['hasta_informe_ofi'].".xlsx";
			//Establecemos un tamaño fijo de columna
			$sheet->getColumnDimension('A')->setWidth(15);
			$sheet->getColumnDimension('B')->setWidth(30);
			$sheet->getColumnDimension('C')->setWidth(25);
			$sheet->getColumnDimension('D')->setWidth(20);
			$sheet->getColumnDimension('E')->setWidth(20);
			$sheet->getColumnDimension('F')->setWidth(22);
			$sheet->getColumnDimension('G')->setWidth(22);
			$sheet->getColumnDimension('H')->setWidth(12);

			//Mostramos la cabecera
			$sheet->getStyle('A1:H1')->getFont()->setBold(true);
			$sheet->getStyle('A1:H1')->getFill()->setFillType("solid")->getStartColor()->setARGB('FFE5E5E5');
			$sheet->setCellValue('A1', 'Cod. Trab');
			$sheet->setCellValue('B1', 'Nombre y apellidos');
			$sheet->setCellValue('C1', 'Fecha Registro');
			$sheet->setCellValue('D1', 'Hora Registro');
			$sheet->setCellValue('E1', 'Tipo Registro');
			$sheet->setCellValue('F1', 'Sede');
			$sheet->setCellValue('G1', 'Ubicacion');
			$sheet->setCellValue('H1', 'Manual');
			
			//Consultamos los datos
			$datos_presencia = $con_bdsrx->informePresenciaOficina($_POST['desde_informe_ofi'], $_POST['hasta_informe_ofi'], $_POST['tipo_reg_informe_ofi'], $_POST['pernr_informe_ofi'], $_POST['reg_manual_ofi'], $_POST['sede_informe_ofi'], $_POST['ubi_informe_ofi']);
			if(!empty($datos_presencia)){
				$i=2;
				foreach($datos_presencia as $resultado) {

					// Inicializar $nombre para evitar el warning de variable indefinida
					$nombre = '';

					// Mostrar el nombre completo del trabajador
					if ($resultado['APELLIDO1'] != '' && $resultado['NOMBRE'] != '' ) {
						// Si existen APELLIDO1 y NOMBRE, mostrar en formato: APELLIDO1 APELLIDO2, NOMBRE
						$nombre = $resultado['APELLIDO1'];
				
						if ($resultado['APELLIDO2'] != '') {
							$nombre .= ' ' . $resultado['APELLIDO2'];
						}
				
						$nombre .= ', ' . $resultado['NOMBRE'];
					} elseif ($resultado['NOMBREYAPELLIDOS'] != '') {
						// Si existe el campo NOMBREYAPELLIDOS completo
						$nombre = $resultado['NOMBREYAPELLIDOS'];
					} else {
						// Si no hay datos disponibles
						$nombre = 'Desconocido';
					}
					
					
					if ($resultado['fecha_reg'] != '') {
						$fecha_reg = $resultado['fecha_reg']->format('Y-m-d');
						$fecha_reg_hora = $resultado['fecha_reg']->format('H:i:s');
					} else {
						$fecha_reg = '';
						$fecha_reg_hora = '';
					}

					$sheet->setCellValue('A'.$i, (int)$resultado['pernr']);
					// Aplicar formato numérico a la celda
					$sheet->getStyle('A'.$i)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);

					$sheet->setCellValue('B'.$i, $nombre);

					$sheet->setCellValue('C'.$i, $fecha_reg);
					$sheet->getStyle('C'.$i)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);
					$sheet->setCellValue('D'.$i, $fecha_reg_hora);
					$sheet->getStyle('D'.$i)->getNumberFormat()->setFormatCode('hh:mm:ss');
					$sheet->setCellValue('E'.$i, $resultado['tipo_reg']);
					$sheet->setCellValue('F'.$i, $resultado['sede_ubi']);
					$sheet->setCellValue('G'.$i, $resultado['nombre_ubi']);
					$sheet->setCellValue('H'.$i, $resultado['manual']);

					$i++;
				}				
				//Pintamos el borde de la tabla
				$sheet->getStyle('A1:H'.($i-1))->getBorders()->getAllBorders()->setBorderStyle("thin");
				//Centramos las columnas
				$sheet->getStyle('A1:H'.($i-1))->getAlignment()->setHorizontal('center');
				$sheet->getStyle('A1:H'.($i-1))->getAlignment()->setVertical('center');
			}else{
				//si la consulta no tiene datos, se exporta el fichero con el texto "No se han encontrado registros de entrada/salida para oficina"
				$sheet->mergeCells('A3:J3');
				$sheet->setCellValue('A3', "No se han encontrado registros de entrada/salida para oficina");
				//Pintamos el borde de la tabla
				$sheet->getStyle('A3:J3')->getBorders()->getAllBorders()->setBorderStyle("thin");
				//Centramos las columnas
				$sheet->getStyle('A3:J3')->getAlignment()->setHorizontal('center');
				$sheet->getStyle('A3:J3')->getAlignment()->setVertical('center');
			}










			//Descargamos el archivo
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $nombreDelDocumento . '"');
            header('Cache-Control: max-age=0');
            
            ob_end_clean();
            $writer = IOFactory::createWriter($documento, 'Xlsx');
            $writer->save('php://output');
            exit;
        } catch (Exception $e) {
            ob_end_clean();
            echo "Error al generar el Excel: " . $e->getMessage();
        }
    }


} elseif (isset($_GET['informe_presencia_alm_pdf'])) {
    // Creación del objeto de la clase heredada
    $pdf = new tFPDF('L');
    $pdf->SetMargins(37,20,20); // Margen de la tabla
    // $pdf->SetAutoPageBreak(true, 20);
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial','',10);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetDrawColor(0,0,0);
    $pdf->SetTextColor(153,53,58);
    $pdf->Text(20,12,utf8_decode("SUREXPORT COMPAÑÍA AGRARIA, S.L."));
    $pdf->SetTextColor(0,0,0);
    $pdf->Text(20,16,utf8_decode("Poligono Matalagrana S/N, Apdo: 116"));
    $pdf->Text(20,20,utf8_decode("CIF B21202817"));
    $pdf->Text(20,24,utf8_decode("+34 959451550 | surexport@surexport.es"));
    $pdf->Text(20,198,utf8_decode("Fecha de impresión: ".date('Y-m-d H:i:s')));
    
    // Colocar el logo en la primera página en su posición original
    $pdf->Image('img/logo-home.png', 233, 7, 44);
    
    $pdf->SetFont('Arial','B',14);
    
    // Título del documento
    $titulo= utf8_decode("Informe Presencia Almacén");
    $pdf->Multicell(240,15,$titulo,0,'C');
    
    $pdf->SetFont('Arial','B',9);
    // Cabecera de la tabla
    $pdf->SetFillColor(222,222,222);
    $pdf->Cell(27,6,utf8_decode('Cod. Trab'),1,0,'C',true);
    $pdf->Cell(75,6,"Nombre y apellidos",1,0,'C',true);
    $pdf->Cell(30,6,utf8_decode('Fecha Registro'),1,0,'C',true);
	$pdf->Cell(30,6,utf8_decode('Hora Registro'),1,0,'C',true);
	$pdf->Cell(20,6,utf8_decode('ID Puerta'),1,0,'C',true);
	$pdf->Cell(40,6,utf8_decode('Nombre Puerta'),1,1,'C',true);
    
	if (isset($_POST['pernr_informe_alm'])) {
		$pernr_nombre = $_POST['pernr_informe_alm'];
	} else {
		$pernr_nombre = '';
	}
    $datosInforme = $con_bdsrx->informePresenciaAlmacen($_POST['desde_informe_alm'], $_POST['hasta_informe_alm'], $pernr_nombre, $_POST['puerta_informe_alm']);
    if (!empty($datosInforme)) {
		// Función para convertir horas decimales a formato HH:mm
		function decimalToTime($decimalHour) {
			$hours = floor($decimalHour); // Parte entera: horas
			$minutes = round(($decimalHour - $hours) * 60); // Parte decimal * 60 para convertir a minutos
		    return sprintf('%02d:%02d', $hours, $minutes);
		}

		
		foreach ($datosInforme as $resultado) {

			// Convertir las horas de descanso y producción a formato HH:mm
			$pdf->SetFont('Arial', '', 9);
			$pdf->Cell(27, 6, $resultado['EXTERNALID'], 1, 0, 'C');
			$pdf->Cell(75, 6, utf8_decode($resultado['USERNAME']), 1, 0);
			$pdf->Cell(30, 6, date_format($resultado['OPENINGDATE'], 'Y/m/d'), 1, 0, 'C');
			$pdf->Cell(30, 6, date_format($resultado['OPENINGDATE'], 'H:i:s'), 1, 0, 'C');
			$pdf->Cell(20, 6, utf8_decode($resultado['DOORID']), 1, 0, 'C');
			$pdf->Cell(40, 6, utf8_decode($resultado['DOORNAME']), 1, 1, 'C');

			if($pdf->GetY() > 180) { // Ajusta este valor según sea necesario
                $pdf->AddPage();
                
                // Coloca el logo centrado en las páginas subsiguientes
                $logoWidth = 35; // Ancho del logo
                $pageWidth = $pdf->GetPageWidth();
                $x = ($pageWidth - $logoWidth) / 2;
                $pdf->Image('img/logo-home.png', $x, 5, $logoWidth);
            }
        }
    }

	
	//Mostramos el pdf en el navegador
	$nombreArchivo = 'Informe_Presencia_Oficina_'.date('Y-m-d H:i:s').'.pdf';
	$pdf->Output('I', $nombreArchivo);


} elseif (isset($_GET['informe_presencia_alm_excel'])) {
	if (isset($_POST['desde_informe_alm']) && isset($_POST['hasta_informe_alm'])) {			
		
		ob_clean();
        
        try {
            // Crear el documento Excel
            $documento = new Spreadsheet();
            $sheet = $documento->getActiveSheet();


			//Creamos la hoja de Informe PRESENCIA
			$sheet->setTitle("INFORME PRESENCIA ALMACEN");
			//Establecemos el nombre
			$nombreDelDocumento = "Informe_Presencia_Almacen_".$_POST['desde_informe_alm']."_".$_POST['hasta_informe_alm'].".xlsx";
			//Establecemos un tamaño fijo de columna
			$sheet->getColumnDimension('A')->setWidth(15);
			$sheet->getColumnDimension('B')->setWidth(40);
			$sheet->getColumnDimension('C')->setWidth(18);
			$sheet->getColumnDimension('D')->setWidth(18);
			$sheet->getColumnDimension('E')->setWidth(12);
			$sheet->getColumnDimension('F')->setWidth(22);

			//Mostramos la cabecera
			$sheet->getStyle('A1:F1')->getFont()->setBold(true);
			$sheet->getStyle('A1:F1')->getFill()->setFillType("solid")->getStartColor()->setARGB('FFE5E5E5');
			$sheet->setCellValue('A1', 'Cod. Trab');
			$sheet->setCellValue('B1', 'Nombre y apellidos');
			$sheet->setCellValue('C1', 'Fecha Registro');
			$sheet->setCellValue('D1', 'Hora Registro');
			$sheet->setCellValue('E1', 'ID Puerta');
			$sheet->setCellValue('F1', 'Nombre Puerta');
			
			//Consultamos los datos
			if (isset($_POST['pernr_informe_alm'])) {
				$pernr_nombre = $_POST['pernr_informe_alm'];
			} else {
				$pernr_nombre = '';
			}
			$datos_presencia = $con_bdsrx->informePresenciaAlmacen($_POST['desde_informe_alm'], $_POST['hasta_informe_alm'], $pernr_nombre, $_POST['puerta_informe_alm']);
			if(!empty($datos_presencia)){
				$i=2;
				foreach($datos_presencia as $resultado) {

					
					$sheet->setCellValue('A'.$i, (int)$resultado['EXTERNALID']);
					$sheet->getStyle('A'.$i)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
					$sheet->setCellValue('B'.$i, $resultado['USERNAME']);
					$sheet->setCellValue('C'.$i, $resultado['OPENINGDATE']->format('Y-m-d'));
					$sheet->getStyle('C'.$i)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);
					$sheet->setCellValue('D'.$i, $resultado['OPENINGDATE']->format('H:i:s'));
					$sheet->getStyle('D'.$i)->getNumberFormat()->setFormatCode('hh:mm:ss');
					$sheet->setCellValue('E'.$i, $resultado['DOORID']);
					$sheet->setCellValue('F'.$i, $resultado['DOORNAME']);

					$i++;
				}				
				//Pintamos el borde de la tabla
				$sheet->getStyle('A1:F'.($i-1))->getBorders()->getAllBorders()->setBorderStyle("thin");
				//Centramos las columnas
				$sheet->getStyle('A1:F'.($i-1))->getAlignment()->setHorizontal('center');
				$sheet->getStyle('A1:F'.($i-1))->getAlignment()->setVertical('center');
			}else{
				//si la consulta no tiene datos, se exporta el fichero con el texto "No se han encontrado registros de entrada/salida para oficina"
				$sheet->mergeCells('A3:F3');
				$sheet->setCellValue('A3', "No se han encontrado registros de entrada/salida para oficina");
				//Pintamos el borde de la tabla
				$sheet->getStyle('A3:F3')->getBorders()->getAllBorders()->setBorderStyle("thin");
				//Centramos las columnas
				$sheet->getStyle('A3:F3')->getAlignment()->setHorizontal('center');
				$sheet->getStyle('A3:F3')->getAlignment()->setVertical('center');
			}








			//Descargamos el archivo
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="' . $nombreDelDocumento . '"');
			header('Cache-Control: max-age=0');

			ob_end_clean();
			$writer = IOFactory::createWriter($documento, 'Xlsx');
			$writer->save('php://output');
			exit;
		} catch (Exception $e) {
			// Manejar cualquier error que ocurra durante la generación
			ob_end_clean();
			echo "Error al generar el archivo Excel: " . $e->getMessage();
		}
	} else {
		echo "Debe proporcionar las fechas de inicio y fin";
	}
	
} elseif (isset($_GET['informe_presencia_ofi_audi_pdf'])) {
    // Creación del objeto de la clase heredada
    $pdf = new tFPDF('L');
    $pdf->SetMargins(26,20,20); // Margen de la tabla
    // $pdf->SetAutoPageBreak(true, 20);
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial','',10);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetDrawColor(0,0,0);
    $pdf->SetTextColor(153,53,58);
    $pdf->Text(20,12,utf8_decode("SUREXPORT COMPAÑÍA AGRARIA, S.L."));
    $pdf->SetTextColor(0,0,0);
    $pdf->Text(20,16,utf8_decode("Poligono Matalagrana S/N, Apdo: 116"));
    $pdf->Text(20,20,utf8_decode("CIF B21202817"));
    $pdf->Text(20,24,utf8_decode("+34 959451550 | surexport@surexport.es"));
    $pdf->Text(20,198,utf8_decode("Fecha de impresión: ".date('Y-m-d H:i:s')));
    
    // Colocar el logo en la primera página en su posición original
    $pdf->Image('img/logo-home.png', 233, 7, 44);
    
    $pdf->SetFont('Arial','B',14);
    
    // Título del documento
    $titulo= utf8_decode("Auditoría Presencia Oficina");
    $pdf->Multicell(250,15,$titulo,0,'C');


    // Cabecera de la tabla, añádelo directamente en el lugar donde corresponde en cada página
	function headerTabla($pdf) {
		$pdf->SetFont('Arial','B',9);
		$pdf->SetFillColor(222,222,222);
		$pdf->Cell(20,6,utf8_decode('Cod. Trab'),1,0,'C',true);
		$pdf->Cell(62,6,"Nombre y apellidos",1,0,'C',true);
		$pdf->Cell(20,6,utf8_decode('Fecha'),1,0,'C',true);
		$pdf->Cell(20,6,utf8_decode('Desayuno'),1,0,'C',true);
		$pdf->Cell(20,6,utf8_decode('Almuerzo'),1,0,'C',true);
		$pdf->Cell(18,6,utf8_decode('Otros'),1,0,'C',true);
		$pdf->Cell(30,6,utf8_decode('Descanso Total'),1,0,'C',true);
		$pdf->Cell(30,6,utf8_decode('Horas trabajadas'),1,0,'C',true);
		$pdf->Cell(25,6,utf8_decode('Horas Totales'),1,1,'C',true);
	}

	// Llamamos a la función que coloca la cabecera antes de los datos
	headerTabla($pdf);

	// inciarlizar variables paras sumar los segundos de los horarios
	$segundos_desayuno = 0;
	$segundos_almuerzo = 0;
	$segundos_otros = 0;
	$segundos_descanso = 0;
	$segundos_tiempo_efectivo = 0;
	$segundos_horas_totales = 0;


    $datosInforme = $con_bdsrx->informePresenciaOficina2($_POST);
    if (!empty($datosInforme)) {
		// Función para convertir horas decimales a formato HH:mm
		function decimalToTime($decimalHour) {
			$hours = floor($decimalHour); // Parte entera: horas
			$minutes = round(($decimalHour - $hours) * 60); // Parte decimal * 60 para convertir a minutos
		    return sprintf('%02d:%02d', $hours, $minutes);
		}

		
		foreach ($datosInforme as $resultado) {
			$segundos_desayuno = $segundos_desayuno + $resultado['segundos_desayuno'];
			$segundos_almuerzo = $segundos_almuerzo + $resultado['segundos_almuerzo'];
			$segundos_otros = $segundos_otros + $resultado['segundos_otros'];
			$segundos_descanso = $segundos_descanso + $resultado['segundos_descanso'];
			$segundos_tiempo_efectivo = $segundos_tiempo_efectivo + $resultado['segundos_producido'];
			$segundos_horas_totales = $segundos_horas_totales + $resultado['segundos_totales'];
			
			// Inicializar $nombre para evitar el warning de variable indefinida
			$nombre = '';

			// Mostrar el nombre completo del trabajador
			if ($resultado['NOMBREYAPELLIDOS'] != '') {
				// Si existe el campo NOMBREYAPELLIDOS completo
				$nombre = $resultado['NOMBREYAPELLIDOS'];
			} else {
				// Si no hay datos disponibles
				$nombre = 'Desconocido';
			}

			// Convertir las horas de descanso y producción a formato HH:mm
			$pdf->SetFont('Arial', '', 9);
			$pdf->Cell(20, 6, $resultado['pernr'], 1, 0, 'C');
			$pdf->Cell(62, 6, utf8_decode($nombre), 1, 0);
			 // Comprobar si 'fecha' no es NULL y es un objeto DateTime
			if (isset($resultado['fecha']) && $resultado['fecha'] instanceof DateTime) {
				$pdf->Cell(20, 6, $resultado['fecha']->format('Y/m/d'), 1, 0, 'C');
			} else {
				$pdf->Cell(20, 6, 'N/A', 1, 0, 'C');
			}
			$pdf->Cell(20, 6, utf8_decode($resultado['horas_desayuno']), 1, 0, 'C');
			$pdf->Cell(20, 6, utf8_decode($resultado['horas_almuerzo']), 1, 0, 'C');
			$pdf->Cell(18, 6, utf8_decode($resultado['horas_otros']), 1, 0, 'C');
			$pdf->Cell(30, 6, utf8_decode($resultado['horas_descanso']), 1, 0, 'C');
			$pdf->Cell(30, 6, utf8_decode($resultado['horas_producido']), 1, 0, 'C');
			$pdf->Cell(25, 6, utf8_decode($resultado['horas_totales']), 1, 1, 'C');


			if($pdf->GetY() > 180) { // Ajusta este valor según sea necesario
                $pdf->AddPage();
                
                // Coloca el logo centrado en las páginas subsiguientes
                $logoWidth = 35; // Ancho del logo
                $pageWidth = $pdf->GetPageWidth();
                $x = ($pageWidth - $logoWidth) / 2;
                $pdf->Image('img/logo-home.png', $x, 5, $logoWidth);

				// Llamamos a la función para agregar la cabecera en cada página
				headerTabla($pdf);
            }
        }

		// Creamos otra tabla separa de la principal debajo d elos registros para el caluclo de las horas
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(20,6,utf8_decode(''),0,1,'');
		$pdf->Cell(20,6,utf8_decode(''),0,0,'');
		$pdf->Cell(62,6,"",0,0,'');
		$pdf->Cell(20,6,utf8_decode(''),0,0,'');
		$pdf->Cell(20,6,utf8_decode('Desayuno'),1,0,'C',true);
		$pdf->Cell(20,6,utf8_decode('Almuerzo'),1,0,'C',true);
		$pdf->Cell(18,6,utf8_decode('Otros'),1,0,'C',true);
		$pdf->Cell(30,6,utf8_decode('Descanso Total'),1,0,'C',true);
		$pdf->Cell(30,6,utf8_decode('Horas trabajadas'),1,0,'C',true);
		$pdf->Cell(25,6,utf8_decode('Horas Totales'),1,1,'C',true);

		function convertir_a_horas($segundos) {
			$horas = floor($segundos / 3600);
			$minutos = floor(($segundos % 3600) / 60);
			return sprintf("%02d:%02d", $horas, $minutos);
		}

		// Letra normal
		$pdf->SetFont('Arial', '', 9);
		$pdf->Cell(20, 6, '', 0, 0, 'C');
		$pdf->Cell(62, 6, '', 0, 0, 'C');
		$pdf->Cell(20, 6, '', 0, 0, 'C');
		$pdf->Cell(20, 6, convertir_a_horas($segundos_desayuno), 1, 0, 'C');
		$pdf->Cell(20, 6, convertir_a_horas($segundos_almuerzo), 1, 0, 'C');
		$pdf->Cell(18, 6, convertir_a_horas($segundos_otros), 1, 0, 'C');
		$pdf->Cell(30, 6, convertir_a_horas($segundos_descanso), 1, 0, 'C');
		$pdf->Cell(30, 6, convertir_a_horas($segundos_tiempo_efectivo), 1, 0, 'C');
		$pdf->Cell(25, 6, convertir_a_horas($segundos_horas_totales), 1, 0, 'C');

	}

	
    // Mostramos el pdf en el navegador
	if (isset($_POST['fecha_inicio_ofi']) && $_POST['fecha_fin_ofi'] == '') {
		$nombreDelDocumento = "Presencia_Oficina_".$_POST['fecha_inicio_ofi'].".pdf";
	} else {
		$nombreDelDocumento = "Presencia_Oficina_".$_POST['fecha_inicio_ofi']."_".$_POST['fecha_fin_ofi'].".pdf";
	}
	$pdf->Output('I', $nombreDelDocumento);



} elseif (isset($_GET['informe_presencia_ofi_audi_excel'])) {	
		
ob_clean();

try {
    // Crear el documento Excel
    $documento = new Spreadsheet();

    // Hoja 1: INFORME PRESENCIA ALMACEN (formato original)
    $sheet1 = $documento->getActiveSheet();
    $sheet1->setTitle("INFORME PRESENCIA ALMACEN");

    // Establecemos el nombre del archivo
    if (isset($_POST['fecha_inicio_ofi']) && $_POST['fecha_fin_ofi'] == '') {
        $nombreDelDocumento = "Presencia_Oficina_".$_POST['fecha_inicio_ofi'].".xlsx";
    } else {
        $nombreDelDocumento = "Presencia_Oficina_".$_POST['fecha_inicio_ofi']."_".$_POST['fecha_fin_ofi'].".xlsx";
    }

    // Definimos columnas ancho fijo para hoja 1
    foreach (['A'=>15, 'B'=>40, 'C'=>17, 'D'=>15, 'E'=>15, 'F'=>15, 'G'=>18, 'H'=>18, 'I'=>18, 'J'=>18, 'K'=>18] as $col => $width) {
        $sheet1->getColumnDimension($col)->setWidth($width);
    }

    // Cabecera hoja 1
    $sheet1->getStyle('A1:K1')->getFont()->setBold(true);
    $sheet1->getStyle('A1:K1')->getFill()->setFillType("solid")->getStartColor()->setARGB('FFE5E5E5');
    $sheet1->setCellValue('A1', 'Cod. Trab');
    $sheet1->setCellValue('B1', 'Nombre y apellidos');
    $sheet1->setCellValue('C1', 'Fecha');
    $sheet1->setCellValue('D1', 'Inicio Jornada');
    $sheet1->setCellValue('E1', 'Fin Jornada');
    $sheet1->setCellValue('F1', 'Desayuno');
    $sheet1->setCellValue('G1', 'Almuerzo');
    $sheet1->setCellValue('H1', 'Otros');
    $sheet1->setCellValue('I1', 'Descanso Total');
    $sheet1->setCellValue('J1', 'Horas trabajadas');
    $sheet1->setCellValue('K1', 'Horas Totales');

    // Crear segunda hoja para formato base 100
    $sheet2 = $documento->createSheet();
    $sheet2->setTitle("INFORME PRESENCIA BASE 100");

    // Columnas para hoja 2 igual que hoja 1
    foreach (['A'=>15, 'B'=>40, 'C'=>17, 'D'=>15, 'E'=>15, 'F'=>15, 'G'=>18, 'H'=>18, 'I'=>18, 'J'=>18, 'K'=>18] as $col => $width) {
        $sheet2->getColumnDimension($col)->setWidth($width);
    }

    // Cabecera hoja 2
    $sheet2->getStyle('A1:K1')->getFont()->setBold(true);
    $sheet2->getStyle('A1:K1')->getFill()->setFillType("solid")->getStartColor()->setARGB('FFDDEEFF');
    $sheet2->setCellValue('A1', 'Cod. Trab');
    $sheet2->setCellValue('B1', 'Nombre y apellidos');
    $sheet2->setCellValue('C1', 'Fecha');
    $sheet2->setCellValue('D1', 'Inicio Jornada');
    $sheet2->setCellValue('E1', 'Fin Jornada');
    $sheet2->setCellValue('F1', 'Desayuno');
    $sheet2->setCellValue('G1', 'Almuerzo');
    $sheet2->setCellValue('H1', 'Otros');
    $sheet2->setCellValue('I1', 'Descanso Total');
    $sheet2->setCellValue('J1', 'Horas trabajadas');
    $sheet2->setCellValue('K1', 'Horas Totales');

    // Función para convertir hh:mm o hh:mm:ss a decimal base 100
    function tiempo_a_decimal_base100($tiempo_str) {
        if (empty($tiempo_str)) return 0;

        // Asumiendo formato hh:mm o hh:mm:ss
        $partes = explode(':', $tiempo_str);
        if (count($partes) < 2) return 0;

        $horas = (float)$partes[0];
        $minutos = (float)$partes[1];
        $segundos = isset($partes[2]) ? (float)$partes[2] : 0;

        // Convertir minutos y segundos a decimal base 100
        $decimal = $horas + (($minutos / 60) * 100 / 100) + (($segundos / 3600) * 100 / 100);

        // Simplificando:
        // minutos base 100 = minutos * 100 / 60
        $minutos_base100 = $minutos * 100 / 60;
        $segundos_base100 = $segundos * 100 / 3600;

        return round($horas + ($minutos_base100 / 100) + ($segundos_base100 / 100), 2);
    }

	// Supongamos que obtienes $datos_presencia desde otra función
	$datos_presencia = $con_bdsrx->informePresenciaOficina2($_POST);


    if (!empty($datos_presencia)) {
        $i = 2; // fila hoja 1
        $j = 2; // fila hoja 2

        foreach ($datos_presencia as $resultado) {
            // Nombre trabajador igual que antes
            $nombre = '';
            if ($resultado['NOMBREYAPELLIDOS'] != '') {
                $nombre = $resultado['NOMBREYAPELLIDOS'];
            } else {
                $nombre = 'Desconocido';
            }

            // Hoja 1 valores sin modificar
			$sheet1->setCellValue('A' . $i, $resultado['pernr']);
			$sheet1->getStyle('A' . $i)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
			$sheet1->setCellValue('B' . $i, $nombre);

			if (isset($resultado['fecha']) && $resultado['fecha'] instanceof DateTime) {
				$sheet1->setCellValue('C' . $i, $resultado['fecha']->format('Y-m-d'));
			} else {
				$sheet1->setCellValue('C' . $i, 'N/A');
			}
			$sheet1->getStyle('C' . $i)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);

			// Entrada jornada
			if (isset($resultado['primera_entrada']) && $resultado['primera_entrada'] instanceof DateTime) {
				$sheet1->setCellValue('D' . $i, $resultado['primera_entrada']->format('m-d H:i:s'));
			} else {
				$sheet1->setCellValue('D' . $i, 'N/A');
			}
			$sheet1->getStyle('D' . $i)->getNumberFormat()->setFormatCode('mm-dd hh:mm:ss');

			// Salida jornada
			if (isset($resultado['ultima_salida']) && $resultado['ultima_salida'] instanceof DateTime) {
				$sheet1->setCellValue('E' . $i, $resultado['ultima_salida']->format('m-d H:i:s'));
			} else {
				$sheet1->setCellValue('E' . $i, 'N/A');
			}
			$sheet1->getStyle('E' . $i)->getNumberFormat()->setFormatCode('mm-dd hh:mm:ss');

			$sheet1->setCellValue('F' . $i, $resultado['horas_desayuno']);
			$sheet1->setCellValue('G' . $i, $resultado['horas_almuerzo']);
			$sheet1->setCellValue('H' . $i, $resultado['horas_otros']);
			$sheet1->setCellValue('I' . $i, $resultado['horas_descanso']);
			$sheet1->setCellValue('J' . $i, $resultado['horas_producido']);
			$sheet1->setCellValue('K' . $i, $resultado['horas_totales']);

			// Hoja 2 valores convertidos a base 100
			$sheet2->setCellValue('A' . $j, $resultado['pernr']);
			$sheet2->setCellValue('B' . $j, $nombre);

			if (isset($resultado['fecha']) && $resultado['fecha'] instanceof DateTime) {
				$sheet2->setCellValue('C' . $j, $resultado['fecha']->format('Y-m-d'));
			} else {
				$sheet2->setCellValue('C' . $j, 'N/A');
			}
			$sheet2->getStyle('C' . $j)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);

			// Entrada jornada
			if (isset($resultado['primera_entrada']) && $resultado['primera_entrada'] instanceof DateTime) {
				$sheet2->setCellValue('D' . $j, $resultado['primera_entrada']->format('m-d H:i:s'));
			} else {
				$sheet2->setCellValue('D' . $j, 'N/A');
			}
			$sheet2->getStyle('D' . $j)->getNumberFormat()->setFormatCode('mm-dd hh:mm:ss');

			// Salida jornada
			if (isset($resultado['ultima_salida']) && $resultado['ultima_salida'] instanceof DateTime) {
				$sheet2->setCellValue('E' . $j, $resultado['ultima_salida']->format('m-d H:i:s'));
			} else {
				$sheet2->setCellValue('E' . $j, 'N/A');
			}
			$sheet2->getStyle('E' . $j)->getNumberFormat()->setFormatCode('mm-dd hh:mm:ss');

			// Convertir las horas a decimal base 100 para las otras columnas
			$sheet2->setCellValue('F' . $j, tiempo_a_decimal_base100($resultado['horas_desayuno']));
			$sheet2->setCellValue('G' . $j, tiempo_a_decimal_base100($resultado['horas_almuerzo']));
			$sheet2->setCellValue('H' . $j, tiempo_a_decimal_base100($resultado['horas_otros']));
			$sheet2->setCellValue('I' . $j, tiempo_a_decimal_base100($resultado['horas_descanso']));
			$sheet2->setCellValue('J' . $j, tiempo_a_decimal_base100($resultado['horas_producido']));
			$sheet2->setCellValue('K' . $j, tiempo_a_decimal_base100($resultado['horas_totales']));

            $i++;
            $j++;
        }

        // Bordes hoja 1
        $sheet1->getStyle('A1:K' . ($i - 1))->getBorders()->getAllBorders()->setBorderStyle("thin");
        $sheet1->getStyle('A1:K' . ($i - 1))->getAlignment()->setHorizontal('center');
        $sheet1->getStyle('A1:K' . ($i - 1))->getAlignment()->setVertical('center');

        // Bordes hoja 2
        $sheet2->getStyle('A1:K' . ($j - 1))->getBorders()->getAllBorders()->setBorderStyle("thin");
        $sheet2->getStyle('A1:K' . ($j - 1))->getAlignment()->setHorizontal('center');
        $sheet2->getStyle('A1:K' . ($j - 1))->getAlignment()->setVertical('center');

    } else {
        // Si no hay datos, poner mensaje en ambas hojas
        $sheet1->mergeCells('A3:I3');
        $sheet1->setCellValue('A3', "No se han encontrado registros de entrada/salida para oficina");
        $sheet1->getStyle('A3:I3')->getBorders()->getAllBorders()->setBorderStyle("thin");
        $sheet1->getStyle('A3:I3')->getAlignment()->setHorizontal('center');
        $sheet1->getStyle('A3:I3')->getAlignment()->setVertical('center');

        $sheet2->mergeCells('A3:I3');
        $sheet2->setCellValue('A3', "No se han encontrado registros de entrada/salida para oficina");
        $sheet2->getStyle('A3:I3')->getBorders()->getAllBorders()->setBorderStyle("thin");
        $sheet2->getStyle('A3:I3')->getAlignment()->setHorizontal('center');
        $sheet2->getStyle('A3:I3')->getAlignment()->setVertical('center');
    }

    // Descargar archivo
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $nombreDelDocumento . '"');
    header('Cache-Control: max-age=0');

    ob_end_clean();
    $writer = IOFactory::createWriter($documento, 'Xlsx');
    $writer->save('php://output');
    exit;
} catch (Exception $e) {
    ob_end_clean();
    echo "Error al generar el archivo Excel: " . $e->getMessage();
}
} elseif (isset($_GET['etiqueta_nfc'])) {
	// Verificar si el formulario envió datos
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['etiqueta_nfc'])) {
		// Obtener los datos enviados desde el formulario
		$documento = isset($_POST['documento']) ? $_POST['documento'] : 'Sin documento';
		$nombre = isset($_POST['nombre']) ? $_POST['nombre'] : 'Sin nombre';
		$pernr = isset($_POST['pernr']) ? $_POST['pernr'] : 'Sin número';
	
		// Crear un documento con tamaño de página personalizado en orientación horizontal
		$pdf = new tFPDF('L', 'mm', [32, 57]); // Tamaño 57x32 mm, orientación Landscape
		$pdf->AddPage();
	
		// Configurar márgenes uniformes
		$pdf->SetMargins(5, 5);
		$pdf->SetAutoPageBreak(false);
	
		// Coordenada X inicial para alinear todos los textos
		$x = 5; // Margen izquierdo
		$y = 4; // Altura inicial desde la parte superior
	
		// Documento: Negrita
		$pdf->SetFont('Arial', 'B', 11); // Fuente Arial, estilo Bold, tamaño 10
		$pdf->SetXY($x, $y); // Coordenadas específicas
		$pdf->Cell(0, 6, utf8_decode($documento), 0, 1);
	
		// Nombre: Normal, con salto de línea si es necesario
		$y += 8; // Desplazamiento hacia abajo
		$pdf->SetFont('Arial', '', 11); // Fuente Arial, estilo Normal, tamaño 10
		$pdf->SetXY($x, $y); // Misma coordenada X, ajustamos Y
		$pdf->MultiCell(47, 4, utf8_decode($nombre), 0, 'L'); // Ancho ajustado para el texto con saltos automáticos
	
		// Ajustar Y después del MultiCell
		$y += 10; // Recuperar la posición Y después del texto largo
	
		// PERNR: Negrita
		$pdf->SetFont('Arial', 'B', 11); // Fuente Arial, estilo Bold, tamaño 10
		$pdf->SetXY($x, $y); // Misma coordenada X, ajustamos Y
		$pdf->Cell(0, 6, utf8_decode($pernr), 0, 1);
	
		// Generar el PDF
		$pdf->Output();
		exit;
	}
} elseif (isset($_GET['solicitudes_pdf'])) {

	$pdf = new tFPDF('L');
    $pdf->SetMargins(26,20,20); // Margen de la tabla
    // $pdf->SetAutoPageBreak(true, 20);
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial','',10);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetDrawColor(0,0,0);
    $pdf->SetTextColor(153,53,58);
    $pdf->Text(20,12,utf8_decode("SUREXPORT COMPAÑÍA AGRARIA, S.L."));
    $pdf->SetTextColor(0,0,0);
    $pdf->Text(20,16,utf8_decode("Poligono Matalagrana S/N, Apdo: 116"));
    $pdf->Text(20,20,utf8_decode("CIF B21202817"));
    $pdf->Text(20,24,utf8_decode("+34 959451550 | surexport@surexport.es"));
    $pdf->Text(20,198,utf8_decode("Fecha de impresión: ".date('Y-m-d H:i:s')));
    
    // Colocar el logo en la primera página en su posición original
    $pdf->Image('img/logo-home.png', 233, 7, 44);
    
    $pdf->SetFont('Arial','B',14);
    
    // Título del documento
    $titulo= utf8_decode("Solicitudes de ausencia");
    $pdf->Multicell(250,15,$titulo,0,'C');


    // Cabecera de la tabla, añádelo directamente en el lugar donde corresponde en cada página
	function headerTabla($pdf) {
		$pdf->SetFont('Arial','B',9);
		$pdf->SetFillColor(222,222,222);
		$pdf->Cell(20,6,utf8_decode('Cod. Trab'),1,0,'C',true);
		$pdf->Cell(55,6,"Nombre y apellidos",1,0,'C',true);
		$pdf->Cell(35,6,utf8_decode('Fecha Solicitud'),1,0,'C',true);
		$pdf->Cell(28,6,utf8_decode('Fecha inicio'),1,0,'C',true);
		$pdf->Cell(28,6,utf8_decode('Fecha fin'),1,0,'C',true);
		$pdf->Cell(32,6,utf8_decode('Tipo Ausencia'),1,0,'C',true);
		$pdf->Cell(22,6,utf8_decode('Estado'),1,0,'C',true);
		$pdf->Cell(25,6,utf8_decode('Justificante'),1,1,'C',true);
	}

	// Llamamos a la función que coloca la cabecera antes de los datos
	headerTabla($pdf);

	$fecha_inicio = $_POST['fecha_inicio'] ?? '';
	$fecha_fin = $_POST['fecha_fin'] ?? '';
	$pernr_nom_sol = $_POST['pernr_nom_sol'] ?? '';
	$tipo_ausencia = $_POST['tipo_ausencia'] ?? '';
	$estado = $_POST['estado'] ?? '1';
	$justificante = $_POST['justificante'] ?? '';

	$datosSolicitudes = $con_bdsrx->solicitudes($fecha_inicio, $fecha_fin,  $pernr_nom_sol, $tipo_ausencia, $estado, $justificante);

	if (!empty($datosSolicitudes)) {

		foreach ($datosSolicitudes as $resultado) {

			// Convertir las horas de descanso y producción a formato HH:mm
			$pdf->SetFont('Arial', '', 9);

			// Datos trabajador
			$pdf->Cell(20, 6, $resultado['pernr'], 1, 0, 'C');
			$pdf->Cell(55, 6, $resultado['nombre']." ".$resultado['apellidos'], 1, 0);

			// Fechas
			$pdf->Cell(35, 6, date_format($resultado['fecha_solicitud'], 'Y/m/d'), 1, 0, 'C');
			$pdf->Cell(28, 6, date_format($resultado['fecha_desde'], 'Y/m/d'), 1, 0, 'C');
			$pdf->Cell(28, 6, date_format($resultado['fecha_hasta'], 'Y/m/d'), 1, 0, 'C');

			// Tipo de ausencia
			if ($resultado['tipo'] == '1') {
				$tipo = 'Vacaciones';
			} elseif ($resultado['tipo'] == '2') {
				$tipo = 'Otras ausencias';
			} elseif ($resultado['tipo'] == '3') {
				$tipo = 'Festivo local';
			} elseif ($resultado['tipo'] == '4') {
				$tipo = 'Asuntos propios';
			} else {
				$tipo = 'Otro';
			}
			$pdf->Cell(32, 6, $tipo, 1, 0, 'C'); // Cambiar $resultado['tipo'] a $tipo

			// Estado de la solicitud
			if ($resultado['estado'] == '1') {
				$estado = 'Pendiente';
			} elseif ($resultado['estado'] == '3') {
				$estado = 'Aprobada';
			} elseif ($resultado['estado'] == '4') {
				$estado = 'Rechazada';
			} elseif ($resultado['estado'] == '5') {
				$estado = 'Anulada';
			} elseif ($resultado['estado'] == '6') {
				$estado = 'Pendiente';
			} elseif ($resultado['estado'] == '7') {
				$estado = 'Pendiente anulación';
			} elseif ($resultado['estado'] == '8') {
				$estado = 'Anulación rechazada';
			} else {
				$estado = 'Desconocido';
			}
			$pdf->Cell(22, 6, $estado, 1, 0, 'C');

			// Justificante
			if ($resultado['tipo'] == '2') {
				if ($resultado['justificante'] != '') {
					$justificante = 'Entregado';
				} else {
					$justificante = 'No entregado';
				}
			} else {
				$justificante = 'No aplica';
			}
			$pdf->Cell(25, 6, $justificante, 1, 1, 'C');

		}

			if($pdf->GetY() > 180) { // Ajusta este valor según sea necesario
                $pdf->AddPage();
                
                // Coloca el logo centrado en las páginas subsiguientes
                $logoWidth = 35; // Ancho del logo
                $pageWidth = $pdf->GetPageWidth();
                $x = ($pageWidth - $logoWidth) / 2;
                $pdf->Image('img/logo-home.png', $x, 5, $logoWidth);

				// Llamamos a la función para agregar la cabecera en cada página
				headerTabla($pdf);
            }
        }
		
    // Mostramos el pdf en el navegador

	$nombreDelDocumento = "Solicitudes_ausencias.pdf";

	$pdf->Output('I', $nombreDelDocumento);

} elseif (isset($_GET['solicitudes_excel'])) {
	ob_clean();
	
	try {
		// Crear el documento Excel
		$documento = new Spreadsheet();
		$sheet = $documento->getActiveSheet();

		//Creamos la hoja de Solicitudes de ausencia
		$sheet->setTitle("Solicitudes de ausencia");
		//Establecemos el nombre
		$nombreDelDocumento = "Solicitudes_ausencias.xlsx";
		//Establecemos un tamaño fijo de columna
		$sheet->getColumnDimension('A')->setWidth(15);
		$sheet->getColumnDimension('B')->setWidth(45);
		$sheet->getColumnDimension('C')->setWidth(20);
		$sheet->getColumnDimension('D')->setWidth(20);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(25);
		$sheet->getColumnDimension('G')->setWidth(18);
		$sheet->getColumnDimension('H')->setWidth(20);


		//Mostramos la cabecera
		$sheet->getStyle('A1:H1')->getFont()->setBold(true);
		$sheet->getStyle('A1:H1')->getFill()->setFillType("solid")->getStartColor()->setARGB('FFE5E5E5');
		$sheet->setCellValue('A1', 'Cod. Trab');
		$sheet->setCellValue('B1', 'Nombre y apellidos');
		$sheet->setCellValue('C1', 'Fecha Solicitud');
		$sheet->setCellValue('D1', 'Fecha inicio');
		$sheet->setCellValue('E1', 'Fecha fin');
		$sheet->setCellValue('F1', 'Tipo Ausencia');
		$sheet->setCellValue('G1', 'Estado');
		$sheet->setCellValue('H1', 'Justificante');

		

		$fecha_inicio = $_POST['fecha_inicio'] ?? '';
		$fecha_fin = $_POST['fecha_fin'] ?? '';
		$pernr_nom_sol = $_POST['pernr_nom_sol'] ?? '';
		$tipo_ausencia = $_POST['tipo_ausencia'] ?? '';
		$estado = $_POST['estado'] ?? '1';
		$justificante = $_POST['justificante'] ?? '';

		$datosSolicitudes = $con_bdsrx->solicitudes($fecha_inicio, $fecha_fin,  $pernr_nom_sol, $tipo_ausencia, $estado, $justificante);
		if(!empty($datosSolicitudes)){ 
			$i=2;
			foreach($datosSolicitudes as $resultado) { 
				
				// Datos trabajador
				$sheet->setCellValue('A'.$i, $resultado['pernr']);

				// Nombre y apellidos
				$sheet->setCellValue('B'.$i, $resultado['nombre']." ".$resultado['apellidos']);

				// Fechas
				$sheet->setCellValue('C'.$i, date_format($resultado['fecha_solicitud'], 'Y/m/d'));
				$sheet->setCellValue('D'.$i, date_format($resultado['fecha_desde'], 'Y/m/d'));
				$sheet->setCellValue('E'.$i, date_format($resultado['fecha_hasta'], 'Y/m/d'));

				// Tipo de ausencia
				if ($resultado['tipo'] == '1') {
					$tipo = 'Vacaciones';
				} elseif ($resultado['tipo'] == '2') {
					$tipo = 'Otras ausencias';
				} elseif ($resultado['tipo'] == '3') {
					$tipo = 'Festivo local';
				} elseif ($resultado['tipo'] == '4') {
					$tipo = 'Asuntos propios';
				} else {
					$tipo = 'Otro';
				}
				$sheet->setCellValue('F'.$i, $tipo);

				// Estado de la solicitud
				if ($resultado['estado'] == '1') {
					$estado = 'Pendiente';
				} elseif ($resultado['estado'] == '3') {
					$estado = 'Aprobada';
				} elseif ($resultado['estado'] == '4') {
					$estado = 'Rechazada';
				} elseif ($resultado['estado'] == '5') {
					$estado = 'Anulada';
				} elseif ($resultado['estado'] == '6') {
					$estado = 'Pendiente';
				} elseif ($resultado['estado'] == '7') {
					$estado = 'Pendiente anulación';
				} elseif ($resultado['estado'] == '8') {
					$estado = 'Anulación rechazada';
				} else {
					$estado = 'Desconocido';
				}
				$sheet->setCellValue('G'.$i, $estado);

				// Justificante
				if ($resultado['justificante'] != '') {
					$justificante = 'Entregado';
				} else {
					$justificante = 'No entregado';
				}
				$sheet->setCellValue('H'.$i, $justificante);

				$i++;
			}				
			//Pintamos el borde de la tabla
			$sheet->getStyle('A1:H'.($i-1))->getBorders()->getAllBorders()->setBorderStyle("thin");
			//Centramos las columnas
			$sheet->getStyle('A1:H'.($i-1))->getAlignment()->setHorizontal('center');
			$sheet->getStyle('A1:H'.($i-1))->getAlignment()->setVertical('center');
		}else{
			//si la consulta no tiene datos, se exporta el fichero con el texto "No se han encontrado registros de entrada/salida para oficina"
			$sheet->mergeCells('A3:H3');
			$sheet->setCellValue('A3', "No se han encontrado registros de entrada/salida para oficina");
			//Pintamos el borde de la tabla
			$sheet->getStyle('A3:H3')->getBorders()->getAllBorders()->setBorderStyle("thin");
			//Centramos las columnas
			$sheet->getStyle('A3:H3')->getAlignment()->setHorizontal('center');
			$sheet->getStyle('A3:H3')->getAlignment()->setVertical('center');
		}


		//Descargamos el archivo
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $nombreDelDocumento . '"');
		header('Cache-Control: max-age=0');

		ob_end_clean();
		$writer = IOFactory::createWriter($documento, 'Xlsx');
		$writer->save('php://output');
		exit;
	} catch (Exception $e) {
		// Manejar cualquier error que ocurra durante la generación
		ob_end_clean();
		echo "Error al generar el archivo Excel: " . $e->getMessage();
	}
} elseif (isset($_GET['informe_presencia_ofi_pdf'])) {
    // Creación del objeto de la clase heredada
    $pdf = new tFPDF('L');
    $pdf->SetMargins(19,20,20); // Margen de la tabla
    // $pdf->SetAutoPageBreak(true, 20);
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial','',10);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetDrawColor(0,0,0);
    $pdf->SetTextColor(153,53,58);
    $pdf->Text(20,12,utf8_decode("SUREXPORT COMPAÑÍA AGRARIA, S.L."));
    $pdf->SetTextColor(0,0,0);
    $pdf->Text(20,16,utf8_decode("Poligono Matalagrana S/N, Apdo: 116"));
    $pdf->Text(20,20,utf8_decode("CIF B21202817"));
    $pdf->Text(20,24,utf8_decode("+34 959451550 | surexport@surexport.es"));
    $pdf->Text(20,198,utf8_decode("Fecha de impresión: ".date('Y-m-d H:i:s')));
    
    // Colocar el logo en la primera página en su posición original
    $pdf->Image('img/logo-home.png', 233, 7, 44);
    
    $pdf->SetFont('Arial','B',14);
    
    // Título del documento
    $titulo= utf8_encode("Informe Presencia Oficina");
    $pdf->Multicell(255,15,$titulo,0,'C');
    
    $pdf->SetFont('Arial','B',9);
    // Cabecera de la tabla
    $pdf->SetFillColor(222,222,222);
    $pdf->Cell(27,6,utf8_decode('Cod. Trab'),1,0,'C',true);
    $pdf->Cell(63,6,"Nombre y apellidos",1,0,'C',true);
    $pdf->Cell(26,6,utf8_decode('Fecha Registro'),1,0,'C',true);
	$pdf->Cell(26,6,utf8_decode('Hora Registro'),1,0,'C',true);
	$pdf->Cell(30,6,utf8_decode('Tipo Registro'),1,0,'C',true);
	$pdf->Cell(40,6,utf8_decode('Sede'),1,0,'C',true);
	$pdf->Cell(35,6,utf8_decode('Ubicación'),1,0,'C',true);
	$pdf->Cell(15,6,utf8_decode('Manual'),1,1,'C',true);
    
    $datosInforme = $con_bdsrx->informePresenciaOficina($_POST['desde_informe_ofi'], $_POST['hasta_informe_ofi'], $_POST['tipo_reg_informe_ofi'], $_POST['pernr_informe_ofi'], $_POST['reg_manual_ofi'], $_POST['sede_informe_ofi'], $_POST['ubi_informe_ofi']);
    if (!empty($datosInforme)) {
		// Función para convertir horas decimales a formato HH:mm
		function decimalToTime($decimalHour) {
			$hours = floor($decimalHour); // Parte entera: horas
			$minutes = round(($decimalHour - $hours) * 60); // Parte decimal * 60 para convertir a minutos
		    return sprintf('%02d:%02d', $hours, $minutes);
		}

		
		foreach ($datosInforme as $resultado) {

			// Inicializar $nombre para evitar el warning de variable indefinida
			$nombre = '';

			// Mostrar el nombre completo del trabajador
			if ($resultado['APELLIDO1'] != '' && $resultado['NOMBRE'] != '' ) {
				// Si existen APELLIDO1 y NOMBRE, mostrar en formato: APELLIDO1 APELLIDO2, NOMBRE
				$nombre = $resultado['APELLIDO1'];
		
				if ($resultado['APELLIDO2'] != '') {
					$nombre .= ' ' . $resultado['APELLIDO2'];
				}
		
				$nombre .= ', ' . $resultado['NOMBRE'];
			} elseif ($resultado['NOMBREYAPELLIDOS'] != '') {
				// Si existe el campo NOMBREYAPELLIDOS completo
				$nombre = $resultado['NOMBREYAPELLIDOS'];
			} else {
				// Si no hay datos disponibles
				$nombre = 'Desconocido';
			}

			if ($resultado['fecha_reg'] != '') {
				$fecha_reg = date_format($resultado['fecha_reg'], 'Y/m/d');
				$fecha_reg_hora = date_format($resultado['fecha_reg'], 'H:i:s');
			} else {
				$fecha_reg = '';
				$fecha_reg_hora = '';
			}

			// Convertir las horas de descanso y producción a formato HH:mm
			$pdf->SetFont('Arial', '', 9);
			$pdf->Cell(27, 6, $resultado['pernr'], 1, 0, 'C');
			$pdf->Cell(63, 6, utf8_decode($nombre), 1, 0);
			$pdf->Cell(26, 6, $fecha_reg, 1, 0, 'C');
			$pdf->Cell(26, 6, $fecha_reg_hora, 1, 0, 'C');
			$pdf->Cell(30, 6, utf8_decode($resultado['tipo_reg']), 1, 0, 'C');
			$pdf->Cell(40, 6, utf8_decode($resultado['sede_ubi']), 1, 0, 'C');
			$pdf->Cell(35, 6, utf8_decode($resultado['nombre_ubi']), 1, 0, 'C');
			$pdf->Cell(15, 6, utf8_decode($resultado['manual']), 1, 1, 'C');

			if($pdf->GetY() > 190) { // Ajusta este valor según sea necesario
                $pdf->AddPage();
                
                // Coloca el logo centrado en las páginas subsiguientes
                $logoWidth = 35; // Ancho del logo
                $pageWidth = $pdf->GetPageWidth();
                $x = ($pageWidth - $logoWidth) / 2;
                $pdf->Image('img/logo-home.png', $x, 5, $logoWidth);
            }
        }
    }

	
	//Mostramos el pdf en el navegador
	$nombreArchivo = 'Informe_Presencia_Oficina_'.date('Y-m-d H:i:s').'.pdf';
	$pdf->Output('I', $nombreArchivo);


} elseif (isset($_GET['sinregistros_excel'])) {
    try {
        ob_clean(); // Limpiamos antes de empezar

        $documento = new Spreadsheet();
        $sheet = $documento->getActiveSheet();
        $sheet->setTitle("Trabajadores sin registro");

        $nombreDelDocumento = "Trabajadores_sin_registro_" . ($_POST['fecha_inicio'] ?? date('Y-m-d')) . ".xlsx";

        // Ajuste de columnas
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(45);
        $sheet->getColumnDimension('D')->setWidth(20);

        $fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-d');
        $tipo = $_POST['tipo'] ?? '';
		$filtroAsistencia = $_POST['filtroAsistencia'] ?? '';
		$buscador = $_POST['buscador'] ?? '';

        $datos_presencia_sin = $con_bdsrx->trabajadores_conta($fecha_inicio, $tipo, $filtroAsistencia, $buscador);

        $fila = 1;

        if (!empty($datos_presencia_sin)) {

            // Primero pintar los registros "especiales" que NO forman parte de la tabla
            foreach ($datos_presencia_sin as $key => $resultado) {
                if (isset($resultado['ESPECIAL']) && $resultado['ESPECIAL']) {
                    $sheet->setCellValue('B' . $fila, $resultado['A1_PERNR']);
                    $sheet->setCellValue('C' . $fila, $resultado['NOMBREYAPELLIDOS']);
                    $sheet->setCellValue('D' . $fila, $resultado['Estado']);
                    $fila++;
                    unset($datos_presencia_sin[$key]); // eliminarlo para no repetir
                }
            }

            // Luego saltamos 3 filas para la tabla normal
            $fila += 3;

            // Pintamos cabecera
            $sheet->setCellValue('B' . $fila, 'Cod. Trab');
            $sheet->setCellValue('C' . $fila, 'Nombre y apellidos');

			// Escribir la fecha en C2
			$sheet->setCellValue('C2', 'Fecha ' . ($_POST['fecha_inicio'] ?? date('Y-m-d')));

			// Centrar la celda C2 horizontal y verticalmente
			$sheet->getStyle('C2')->getAlignment()->setHorizontal('center');
			$sheet->getStyle('C2')->getAlignment()->setVertical('center');

			// Opcional: poner la fecha en negrita
			$sheet->getStyle('C2')->getFont()->setBold(true);



            $sheet->setCellValue('D' . $fila, 'Asistencia');
            $sheet->getStyle('B' . $fila . ':D' . $fila)->getFont()->setBold(true);
            $sheet->getStyle('B' . $fila . ':D' . $fila)->getFill()->setFillType('solid')->getStartColor()->setARGB('FFE5E5E5');

            $fila++;

            // Pintamos datos normales
            foreach ($datos_presencia_sin as $resultado) {
                $sheet->setCellValue('B' . $fila, $resultado['A1_PERNR']);
                $sheet->setCellValue('C' . $fila, $resultado['NOMBREYAPELLIDOS']);

				if ($resultado['Estado'] === '1') {
                    $sheet->setCellValue('D' . $fila, 'Con registro');
                } else {
                    $sheet->setCellValue('D' . $fila, 'Sin registro');
                }
                $fila++;
            }

            // Bordes
            $sheet->getStyle('B4:D' . ($fila-1))->getBorders()->getAllBorders()->setBorderStyle('thin');
            $sheet->getStyle('B4:D' . ($fila-1))->getAlignment()->setHorizontal('center');
            $sheet->getStyle('B4:D' . ($fila-1))->getAlignment()->setVertical('center');

        } else {
            // Si no hay datos
            $sheet->mergeCells('B4:D4');
            $sheet->setCellValue('B4', "No se han encontrado registros de entrada/salida para oficina");
            $sheet->getStyle('B4:D4')->getBorders()->getAllBorders()->setBorderStyle('thin');
            $sheet->getStyle('B4:D4')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('B4:D4')->getAlignment()->setVertical('center');
        }

        // Descargar
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $nombreDelDocumento . '"');
        header('Cache-Control: max-age=0');

        ob_end_clean();
        $writer = IOFactory::createWriter($documento, 'Xlsx');
        $writer->save('php://output');
        exit;

    } catch (Exception $e) {
        ob_end_clean();
        echo "Error al generar el archivo Excel: " . $e->getMessage();
    }
} elseif (isset($_GET['informe_trabajadores_remesa_pdf'])) {

    // Evitar errores de salida prematura
    ob_start();

    // Comprobar y recoger los datos de POST de forma segura

	if (isset($_POST['id_remesa'], $_POST['ano_remesa'], $_POST['nombre_remesa'])) {
		$_SESSION['id_remesa'] = $_POST['id_remesa'];
		$_SESSION['ano_remesa'] = $_POST['ano_remesa'];
		$_SESSION['nombre_remesa'] = $_POST['nombre_remesa'];
	}

	$idRemesa = $_POST['id_remesa'] ?? $_SESSION['id_remesa'] ?? null;
	$anoRemesa = $_POST['ano_remesa'] ?? $_SESSION['ano_remesa'] ?? null;
	$nombreRemesa = $_POST['nombre_remesa'] ?? $_SESSION['nombre_remesa'] ?? 'SinNombre';

    if ($idRemesa === null || $anoRemesa === null) {
        ob_end_clean(); 
        die("Faltan datos obligatorios para generar el informe.");
    }

    // Crear PDF
    $pdf = new tFPDF('L');
    $pdf->SetMargins(19, 20, 20);
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->SetTextColor(153, 53, 58);
    $pdf->Text(20, 12, iconv("UTF-8", "ISO-8859-1", "SUREXPORT COMPAÑÍA AGRARIA, S.L."));
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Text(20, 16, "Poligono Matalagrana S/N, Apdo: 116");
    $pdf->Text(20, 20, "CIF B21202817");
    $pdf->Text(20, 24, "+34 959451550 | surexport@surexport.es");
    $pdf->Text(20, 198, iconv("UTF-8", "ISO-8859-1", "Fecha de impresión: " . date('Y-m-d H:i:s')));
    $pdf->Image('img/logo-home.png', 233, 7, 44);
    $pdf->SetFont('Arial', 'B', 14);

    $titulo = "Informe Trabajadores Remesa (" . $nombreRemesa . ")";
    $pdf->Multicell(255, 15, $titulo, 0, 'C');

	// Definición de tabla
    $anchoTabla = 186;
    $paginaAncho = $pdf->GetPageWidth();
    $margenIzquierdo = ($paginaAncho - $anchoTabla) / 2;

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(222, 222, 222);
	$pdf->SetX($margenIzquierdo);
    $pdf->Cell(27, 6, 'Cod. Trab', 1, 0, 'C', true);
    $pdf->Cell(63, 6, "Nombre y apellidos", 1, 0, 'C', true);
    $pdf->Cell(26, 6, 'Telefono', 1, 0, 'C', true);
    $pdf->Cell(70, 6, 'Correo', 1, 1, 'C', true);

    $datosInforme = $con_bdsrx->InfoRemesa_llama($idRemesa, $anoRemesa);

    if (!empty($datosInforme)) {
        foreach ($datosInforme as $resultado) {
            $pdf->SetFont('Arial', '', 9);
			$pdf->SetX($margenIzquierdo);
            $pdf->Cell(27, 6, $resultado['PERNR'], 1, 0, 'C');
            $pdf->Cell(63, 6, $resultado['NOMBREYAPELLIDOS'], 1, 0);
            $pdf->Cell(26, 6, $resultado['PREFIJO'] . $resultado['MOVIL'], 1, 0, 'C');
            $pdf->Cell(70, 6, $resultado['CORREO'], 1, 1, 'C');

            if ($pdf->GetY() > 190) {
                $pdf->AddPage();
                $logoWidth = 35;
                $x = ($pdf->GetPageWidth() - $logoWidth) / 2;
                $pdf->Image('img/logo-home.png', $x, 5, $logoWidth);
            }
        }
    }

    // Limpiar cualquier salida previa
    ob_end_clean();

    // Preparar headers para PDF
    $nombreArchivo = 'Informe_trabajadores_' . $nombreRemesa . '.pdf';
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . $nombreArchivo . '"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');

    $pdf->Output('I', $nombreArchivo);
} elseif (isset($_GET['informe_trabajadores_remesa_excel'])) {
	// Generar informe en Excel
	ob_clean();

	$documento = new Spreadsheet();
	$sheet = $documento->getActiveSheet();
	$sheet->setTitle("Trabajadores Remesa");
	$nombreDelDocumento = "Trabajadores_Remesa.xlsx";
	// Ajuste de columnas
	$sheet->getColumnDimension('A')->setWidth(15);
	$sheet->getColumnDimension('B')->setWidth(45);
	$sheet->getColumnDimension('C')->setWidth(20);
	$sheet->getColumnDimension('D')->setWidth(45);
	// Cabecera
	$sheet->getStyle('A1:D1')->getFont()->setBold(true);
	$sheet->getStyle('A1:D1')->getFill()->setFillType("solid")->getStartColor()->setARGB('FFE5E5E5');
	$sheet->setCellValue('A1', 'Cod. Trab');
	$sheet->setCellValue('B1', 'Nombre y apellidos');
	$sheet->setCellValue('C1', 'Telefono');
	$sheet->setCellValue('D1', 'Correo');
	// Recoger datos de la remesa
	$idRemesa = $_POST['id_remesa'] ?? $_SESSION['id_remesa'] ?? null;
	$anoRemesa = $_POST['ano_remesa'] ?? $_SESSION['ano_remesa'] ?? null;
	$nombreRemesa = $_POST['nombre_remesa'] ?? $_SESSION['nombre_remesa'] ?? 'SinNombre';
	if ($idRemesa === null || $anoRemesa === null) {
		ob_end_clean(); 
		die("Faltan datos obligatorios para generar el informe.");
	}
	$datosInforme = $con_bdsrx->InfoRemesa_llama($idRemesa, $anoRemesa);
	if (!empty($datosInforme)) {
		$fila = 2;
		foreach ($datosInforme as $resultado) {
			$sheet->setCellValue('A' . $fila, $resultado['PERNR']);
			$sheet->setCellValue('B' . $fila, $resultado['NOMBREYAPELLIDOS']);
			$sheet->setCellValue('C' . $fila, $resultado['PREFIJO'] . $resultado['MOVIL']);
			$sheet->setCellValue('D' . $fila, $resultado['CORREO']);
			$fila++;
		}
		// Bordes y alineación
		$sheet->getStyle('A1:D' . ($fila - 1))->getBorders()->getAllBorders()->setBorderStyle("thin");
		$sheet->getStyle('A1:D' . ($fila - 1))->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A1:D' . ($fila - 1))->getAlignment()->setVertical('center');
	} else {
		// Si no hay datos
		$sheet->mergeCells('A2:D2');
		$sheet->setCellValue('A2', "No se han encontrado registros de trabajadores para la remesa");
		// Bordes y alineación
		$sheet->getStyle('A2:D2')->getBorders()->getAllBorders()->setBorderStyle("thin");
		$sheet->getStyle('A2:D2')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A2:D2')->getAlignment()->setVertical('center');
	}

	// Descargar el archivo
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="' . $nombreDelDocumento . '"');
	header('Cache-Control: max-age=0');
	ob_end_clean();
	$writer = IOFactory::createWriter($documento, 'Xlsx');
	$writer->save('php://output');
	exit;

} elseif (isset($_GET['informe_trabajadores_baja_excel'])) {

	ob_clean();
	
	try {
		// Crear el documento Excel
		$documento = new Spreadsheet();
		$sheet = $documento->getActiveSheet();
		
		//Creamos la hoja de Informe trabajadores fijos discontinuos
		$sheet->setTitle("Trabajadores fijos discontinuos");
		//Establecemos el nombre
		$nombreDelDocumento = "Trabajadores_fijo_discontinuo.xlsx";
		//Establecemos un tamaño fijo de columna
		$sheet->getColumnDimension('A')->setWidth(15);
		$sheet->getColumnDimension('B')->setWidth(50);
		$sheet->getColumnDimension('C')->setWidth(20);
		$sheet->getColumnDimension('D')->setWidth(25);
		
		//Mostramos la cabecera
		$sheet->getStyle('A1:D1')->getFont()->setBold(true);
		$sheet->getStyle('A1:D1')->getFill()->setFillType("solid")->getStartColor()->setARGB('FFE5E5E5');
		$sheet->setCellValue('A1', 'Cod. Trab');
		$sheet->setCellValue('B1', 'Nombre y apellidos');
		$sheet->setCellValue('C1', 'Fecha de baja');
		$sheet->setCellValue('D1', 'Almacen');

		$datosInforme = $con_bdsrx->trabajadores_baja($_GET['ubicacion'], $_GET['fecha_ini'], $_GET['fecha_fin']);
		if (!empty($datosInforme)) {
			$fila = 2;
			foreach ($datosInforme as $resultado) {
				$sheet->setCellValue('A' . $fila, $resultado['PERNR']);
				$sheet->setCellValue('B' . $fila, $resultado['NOMBREYAPELLIDOS']);
				$sheet->setCellValue('C' . $fila, $resultado['BEGDA']->format('Y-m-d'));
				$sheet->setCellValue('D' . $fila, $resultado['DESC_ALMACEN'] ." (".$resultado['ZZLGORT'].")");
				$fila++;
			}
		}

		//Pintamos el borde de la tabla
		$sheet->getStyle('A1:D' . ($fila - 1))->getBorders()->getAllBorders()->setBorderStyle("thin");
		//Centramos las columnas
		$sheet->getStyle('A1:D' . ($fila - 1))->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A1:D' . ($fila - 1))->getAlignment()->setVertical('center');
		
		//Descargamos el archivo
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $nombreDelDocumento . '"');
		header('Cache-Control: max-age=0');
		ob_end_clean();
		$writer = IOFactory::createWriter($documento, 'Xlsx');
		$writer->save('php://output');
		exit;
	} catch (Exception $e) {
		// Manejo de errores
		echo "Error: " . $e->getMessage();
	}

} else {
	// o puedes mostrar un mensaje de error
	echo "Acción no válida.";
}

?>
