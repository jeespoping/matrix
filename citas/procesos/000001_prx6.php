<html>

<head>
<title>MATRIX</title>
<!-- Loading Theme file(s) -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />

<!-- Loading Calendar JavaScript files -->
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <!-- Loading language definition file -->
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>
</head>
<body BGCOLOR=''>
<BODY TEXT='#000066'>
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.Citas.submit();
	}

	window.onload = function(){

		error = false;
		formMain = document.forms.Citas;

		//Buscando campos marquee
		var mqEstado = document.getElementsByTagName( "marquee" );
		var auxdiv = document.createElement( "div" );

		//Guardando el valor inicial del Comentario
		if( !formMain.hiComentInicial ){
			auxdiv.innerHTML = "<INPUT type='hidden' name='hiComentInicial' value ='"+formMain.Coment.value+"'>";
			formMain.appendChild( auxdiv.firstChild );
		}

		auxdiv.innerHTML = "<INPUT type='hidden' name='regNuevo' value='on'>";
		formMain.appendChild( auxdiv.firstChild );

		//Si existe un marquee en el formulario
		if( mqEstado.length > 0 ){

			//Los datos estan ok
			if( mqEstado[0].innerHTML == "LOS DATOS ESTAN OK!!!!" ){

				if( formMain.Coment.value != '' ){
					formMain.elements['regNuevo'].value = 'off';
					formMain.hiComentInicial.value = formMain.Coment.value;
				}
			}

			//Los datos estan incompletos
			if( mqEstado[0].innerHTML == "LOS DATOS ESTAN INCOMPLETOS -- INTENTELO NUEVAMENTE!!!!"
				&& formMain.elements['Coment'].value != '' ){
				formMain.elements['regNuevo'].value = 'off';

				auxdiv.innerHTML = "<textarea name='taComentario' rows='5' readonly cols=60 style='background:#FFFFEE'>"+formMain.hiComentInicial.value+"</textarea>";

				formMain.Coment.innerHTML = formMain.hiComentAnt.value;

				error = true;
			}

			//Para turno Borrado
			if( mqEstado[0].innerHTML == "!!!! TURNO BORRADO !!!!" ){
				formMain.hiComentInicial.value = '';
			}
		}

		if( formMain.Coment.value == '' && formMain.elements[ 'abierto' ].value == 'on' ){
			formMain.elements['regNuevo'].value = 'on'

		}
		else if( formMain.Coment.value != '' && formMain.elements[ 'abierto' ].value == 'on' ){
			formMain.elements['regNuevo'].value = 'off'
		}

		if( error == false ){
			auxdiv.innerHTML = "<textarea name='taComentario' rows='5' readonly cols=60 style='background:#FFFFEE'>"+formMain.hiComentInicial.value+"</textarea>";
		}

		if( auxdiv.firstChild.value != '' ){
			if( formMain.elements['regNuevo'] &&  formMain.elements['regNuevo'].value != 'on' ){
				formMain.Coment.parentNode.innerHTML = auxdiv.innerHTML + "<br>" + formMain.Coment.parentNode.innerHTML;

				if( error == false ){
					formMain.Coment.value = "";
				}
			}
		}
	};
-->

	function validarEmail(email)
	{
	      var res='';
	      if (email !== '')
	      {  
	          var re=/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,3})$/;
	          
	          if (!re.exec(email)) {
	              alert('El Email digitado es incorrecto');	              
	              res= '1';
	          }    
	      }
	      return res;
	}
</script>
<?php
include_once("conex.php");
/**********************************************************************************************************************
[DOC]
	   PROGRAMA : 000001_prx6.php
	   Fecha de Liberacion : 2007-05-03
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2011-01-07

	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface grafica que permite grabar los  de las
	   las citas par los diferentes servicios hospitalarios ambulatorios.


	   REGISTRO DE MODIFICACIONES :
	   
	   .2020-04-02: Arleyda Insignares C. Se adiciona la función envioEmailCita() para implementar 
	                citas de teleorientacion y telemedicina.
	                Se adiciona campo de correo electrónico para ser diligenciado en caso de dar una cita por telemedicina, solo en este caso el programa enviará un correo informando los datos de la cita y especificaciones según parámetro 'telemedicina' en la tabla root_000051 y la tabla root_000129 para la configuración del contenido del correo.

	   .2011-01-07
	   		Se modifica el acceso a la tabla 9 de citas con el proposito de traer por cedula la ultima informacion del
	   		paciente cuando se digita la cedula.

	   .2007-05-03
	   		Release de Version Beta.

[*DOC]
***********************************************************************************************************************/

strpos($Codequ,"-") = consultarAliasPorAplicacion($conex, $wemp_pmla, "digitosSbtrAg");

function oportunidad($fechaC,$FechaP)
{
	$a1=(integer)substr($fechaC,0,4)*360 +(integer)substr($fechaC,5,2)*30 + (integer)substr($fechaC,8,2);
	$a2=(integer)substr($FechaP,0,4)*360 +(integer)substr($FechaP,5,2)*30 + (integer)substr($FechaP,8,2);
	if($a1 > $a2)
		$weda=(integer)($a1 - $a2);
	else
		$weda=0;
	return $weda;
}

function comentariosAnteriores( $id )
{

	global $conex;
	global $empresa;

	$val = '';

	$sql = "SELECT
				 Comentario
			FROM
				{$empresa}_000009
			WHERE
				id = $id
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows[0];
	}
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Función para enviar el correo electrónico llamando la librería phpmailer (no se usa la función del comun porque tiene envio de un solo adjunto) 
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function sendToEmail( $wasunto, $mensaje, $altbody, $wremitente, $wdestinatarios, $ruta_archivo = "", $nombre_adjunto = "" )
{
	
	include_once("root/PHPMailer/PHPMailerAutoload.php");

	$data 				= array('error'=>0,'mensError'=>"");	
	$fecha         		= date("Y-m-d");
    $hora          		= date("H:i:s");

	//Inicializando objeto Mail
	$mail 				= new PHPMailer();
	
	//Configuraciones generales del correo
	//Se asume que siempre los correos van a hacer enviados por gmail
	$mail->IsSMTP();
	$mail->SMTPDebug	= 0;
	$mail->SMTPAuth 	= true;
	$mail->SMTPSecure 	= "ssl";
	$mail->Host 		= "smtp.gmail.com";
	$mail->Port 		= 465;
	
	//Validaciones del remitente
	if( empty( $wremitente[ 'from' ] ) ){
		$wremitente[ 'from' ] = $wremitente[ 'email' ];
	}
	
	if( empty( $wremitente[ 'fromName' ] ) ){
		$wremitente[ 'fromName' ] = "";
	}
	
	//Datos del remitente
	$mail->Username 	= $wremitente[ 'email' ];
	$mail->Password 	= $wremitente[ 'password' ];
	$mail->From 		= $wremitente[ 'from' ];
	$mail->FromName 	= $wremitente[ 'fromName' ];
	 

	//Datos del correo
	$mail->Subject 		= $wasunto;
	$msghtml 			= $mensaje;
	$mail->AltBody 		= $altbody;
	$mail->MsgHTML( $msghtml );
	
	if ($ruta_archivo !== "") {
		for($i=1; $i<count($nombre_adjunto); $i++)
	    {
		    $mail->AddAttachment($ruta_archivo.$nombre_adjunto[$i],$nombre_adjunto[$i]);
	    }
	}
	
	//Correos de los destinatarios
	//Puede llevar un nombre de destino por posición y debe ser separado por - entre el correo y el nombre
	for($i=0; $i<count($wdestinatarios); $i++)
	{
		list( $wemail_destino, $wnombre_destino ) 	= explode( "-", $wdestinatarios[$i] );
		$wnombre_destino 							= empty( $wnombre_destino ) ? "": $wnombre_destino;
		$mail->AddAddress($wemail_destino, $wnombre_destino );
	}
	$mail->IsHTML(true);
	
	if(!$mail->Send()) {
		$data[ 'Error' ] 	 =  "0";
		$data[ 'mensError' ] =  "No se envió el correo";
	}
	else{
		$accion 	    	 = "envio email";
		$tabla		    	 = "";
		$descripcion    	 = "Se envio un correo Electronico a: {$wemail_destino}";
		
		$data[ 'Error' ] 	 =  "1";
		$data[ 'mensError' ] =  "Correo enviado";
	}
	
	return $data;
}

function consultarAliasPorAplicacion($conexion, $codigoInstitucion, $nombreAplicacion){
	$q = " SELECT
				Detval
			FROM
				root_000051
			WHERE
				Detemp = '".$codigoInstitucion."'
				AND Detapl = '".$nombreAplicacion."'";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$alias = "";
	if ($num > 0)
	{
		$rs = mysql_fetch_array($res);

		$alias = $rs['Detval'];
	} else {
		echo("La institucion con el codigo :".$codigoInstitucion." no se encuentra, ni la aplicacion: ".$nombreAplicacion.".  Por favor verifique el valor de wemp_pmla");
	}
	return $alias;
}

$wccoUrl = ( isset( $wcco ) ) ? "&wcco=".$wcco : "";
echo "<center>";
echo "<table border=0 align=center>";
echo "<tr><td align=center bgcolor='#cccccc'><A NAME='Arriba'><font size=5><b>Registro de Informacion del Turno </b></font></a></tr></td>";
echo "<tr><td align=center bgcolor='#cccccc'><font size=2> <b> 000001_prx6.php Ver. 2011-01-07</b></font></tr></td></table>";
echo "</center>";
echo "<form  name='Citas' action='000001_prx6.php' method=post>";


if( isset($hiComentInicial) ){
	echo "<INPUT type='hidden' name='hiComentInicial' value ='$hiComentInicial'>";
}

if( isset($Coment) ){
	echo "<INPUT type='hidden' name='hiComentAnt' value ='$Coment'>";
}

if( !isset($abierto) ){
	echo "<INPUT type='hidden' name='abierto' value='on'>";
}
else{
	echo "<INPUT type='hidden' name='abierto' value='off'>";
}

if( !isset($taComentario) ){
	$taComentario = '';
}

@session_start();
if(!isset($_SESSION['user']))
	echo "Error Usuario NO Registrado";
else
{
	include_once("citas/funcionesAgendaCitas.php");

	$key = substr($user,2,strlen($user));
	$fechaAct      = date("Y-m-d");

	echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";    

	$wtelemedicina = consultarAliasPorAplicacion($conex, '01', "telemedicina");

	$manejaSedes = false;
	if($empresa == "citasoe" and isset( $wemp_pmla) )
	{
		$query = " SELECT detval
		             FROM root_000051
		            WHERE detapl = 'manejaSedes'
		              AND detemp = '$wemp_pmla'";
		$rs    = mysql_query( $query, $conex );
		$row   = mysql_fetch_assoc( $rs );
		if( $row['detval'] == "on"){
			$manejaSedes = true;
			$campoCco = ( $manejaSedes ) ? " centroCostos, " : "";
			$valorCco = ( $manejaSedes ) ? " '{$wcco}', " : "";
		}
	}

	//arle
	// Obtener nombre del profesional
	$qMedico = "select descripcion from ".$empresa."_000010 where codigo='".substr($pos2,0,strpos($pos2,"-"))."'";
	$execMed = mysql_query($qMedico,$conex);
	$rowMed  = mysql_fetch_array($execMed);
	$nommed  = $rowMed[0];


	if( isset( $wcco ) ){
		echo " <input type='hidden' name='wcco' value='{$wcco}'> ";

		$sql = " SELECT Detval
				   FROM root_000051
				  WHERE detemp = '{$wemp_pmla}'
				    AND detapl = 'facturacion'";
		$rs  = mysql_query( $sql, $conex );
		$row = mysql_fetch_assoc( $rs );
		$wbasedato = $row['Detval'];

		$query = " SELECT Ccocod as cco, ccodes as descripcion
		             FROM {$wbasedato}_000003
		            WHERE Ccotip = 'A'
		              AND Ccocod = '{$wcco}'";
		$rs    = mysql_query( $query, $conex );
		while( $row2 = mysql_fetch_assoc( $rs ) ){
			$nombreCco = preg_replace('/\FACTURACION \b/u', '', $row2['descripcion']);
		}
	}
	if( isset( $wemp_pmla ) ){
		echo " <input type='hidden' name='wemp_pmla' value='{$wemp_pmla}'> ";
	}
	$wccoUrl      = ( isset( $wcco ) ) ? "&wcco=".$wcco : "";
	$wemp_pmlaUrl = ( isset( $wemp_pmla ) ) ? "&wemp_pmla=".$wemp_pmla : "";

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(isset($wesp))
		echo "<input type='HIDDEN' name= 'wesp' value='".$wesp."'>";

	$pos = strpos(strtoupper($Codexa), $wtelemedicina);

	if (isset($wpar) and !isset($ok))
	{
		if (substr($Estado,0,1) == "A")
		{
			if(strpos($Hf,"p") !== false)
				$Hf=(string)((integer)substr($Hf,0,strpos($Hf,":")) + 12).substr($Hf,strpos($Hf,":")+1,2);
			else
				$Hf=substr($Hf,0,strpos($Hf,":")).substr($Hf,strpos($Hf,":")+1,2);
			//**** VALIDACIONES ****
			$tiperr =0;
			if(isset($Fijo))
			{
				$query = "select preparacion from ".$empresa."_000011 where cod_equipo='".substr($pos2,0,strpos($pos2,"-"))."' and codigo = '".substr($Codexa,0,strpos($Codexa,"-"))."'";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				$row1 = mysql_fetch_array($err1);
				$Coment=$Coment.$row1[0];
			}
			if($wpar == 1)
			{
				$query = "update ".$empresa."_000009 set activo='I' where  cod_equ='".$Codequ."' and fecha='".$Fecha."' and hi='".$Hi."' ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			}
			// Verificacion de la Ocupacion del Medico
			$query = "select hi from ".$empresa."_000009 where Cod_equ = '".substr($pos2,0,strpos($pos2,"-"))."'";
			$query = $query." and fecha = '".$wfec."'";
			$query = $query." and ((hi <= '".$Hi."' and hf <= '".$Hf."' and hf > '".$Hi."')";
			$query = $query."  or  (hi >= '".$Hi."' and hf >= '".$Hf."' and hi < '".$Hf."')";
			$query = $query."  or  (hi <= '".$Hi."' and hf >= '".$Hf."')";
			$query = $query."  or  (hi >= '".$Hi."' and hf <= '".$Hf."'))";
			$query = $query." and activo = 'A'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num > 0 )
				$tiperr = 2;
			else
				if($tiperr == 0)
					$tiperr = 0;
			
		
			// Verificacion de los datos de texto
			if (strlen($Nompac)==0 or strlen($Tel)==0 or $Edad==0 or strlen($Codexa)<=1 or strlen($Nitres)<=1)
				$tiperr = 3;
			else
			{
			    if( $Email == '' && $pos == true)
                {
                    $tiperr = 3;
                    $errmen = 'CORREO ELECTRONICO';
                }
                else
                {
					if( $tiperr == 0)
						$tiperr = 0;
			    }	
			}
			
            
			if($wpar == 1)
			{
				if($tiperr == 0)
				{
					$query = "select id from ".$empresa."_000009 where  cod_equ='".substr($Codequ,0,  strpos($Codequ,"-"))."' and fecha='".$Fecha."' and hi='".$Hi."'";
					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$row = mysql_fetch_array($err);
					$OLDID=$row[0];
					$query = "delete from ".$empresa."_000009 where  cod_equ= '".substr($Codequ,0, strpos($Codequ,"-"))."' and fecha='".$Fecha."' and hi='".$Hi."'";
					$err = mysql_query($query,$conex);
				}
				else
				{
					$query = "update ".$empresa."_000009 set activo='A' where  cod_equ='".$Codequ."' and fecha='".$Fecha."' and hi='".$Hi."' ";
					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());

				}
			}
		}
		else //Insertar una nueva cita
		{
			$tiperr = 0;
			$wpar = 3;
		}
		if ($tiperr == 0)
		{
			$posicion = strpos($Nitres,'-');
			$Nitres = substr($Nitres,$posicion+1,strlen($Nitres));
			switch ($wpar)
			{
				case 1:
					$Nompac = strtoupper($Nompac);
					$Nompac = ucwords($Nompac);
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$Coment = trim($taComentario."\n\n".$Coment);
					if(isset($Asistida))
						$Asistida="on";
					else
						$Asistida="off";
					$wdiasOp=oportunidad($Fecha,$wfep);
					$query = "insert ".$empresa."_000009 (medico,fecha_data,hora_data,cod_equ,cod_exa,fecha,hi,hf,Cedula,nom_pac,nit_res,telefono,edad,comentario,Asistida,Email,usuario,activo,dias_Opor,fecsol,{$campoCco} seguridad) values ('".$empresa."','".$fecha."','".$hora."','".substr($Codequ,0, strpos($Codequ,"-"))."','".substr($Codexa,0,strpos($Codexa,"-"))."','".$Fecha."','".$Hi."','".$Hf."','".ucwords($Cedula)."','".ucwords($Nompac)."','".$Nitres."','".$Tel."',".$Edad.",'".$Coment."','".$Asistida."','".$Email."','".substr($user,2,strlen($user))."','".substr($Estado,0,1)."',".$wdiasOp.",'".$wfep."',{$valorCco}'C-".$empresa."')";
				
					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());                    

					$query = "select id from ".$empresa."_000009 where  cod_equ='".substr($Codequ,0, strpos($Codequ,"-"))."' and fecha='".$Fecha."' and hi='".$Hi."'";
					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$row = mysql_fetch_array($err);
					$query = "update ".$empresa."_000008 set cita=".$row[0]." where  cita= ".$OLDID;
					$err = mysql_query($query,$conex);
					
					//Enviar correo electronico para citas de telemedicina
					if ($Email != '' && $pos==true)
                    {
					    envioEmailCita($conex,$wemp_pmla,$Fecha,$Hi,ucwords($Cedula),ucwords($Nompac),$Email,'',$nommed,$empresa,$Codexa,1);
                    }
					echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCFF LOOP=-1>LOS DATOS ESTAN OK!!!!</MARQUEE></FONT>";
					echo "<br><br>";
				break;
				case 2:
					$Nompac = strtoupper($Nompac);
					$Nompac = ucwords($Nompac);
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					if(isset($Asistida))
						$Asistida="on";
					else
						$Asistida="off";
					$wdiasOp=oportunidad($Fecha,$wfep);

					$query = "insert ".$empresa."_000009 (medico,fecha_data,hora_data,cod_equ,cod_exa,fecha,hi,hf,Cedula,nom_pac,nit_res,telefono,edad,comentario,Asistida,Email,usuario,activo,dias_Opor,fecsol,{$campoCco} seguridad) values ('".$empresa."','".$fecha."','".$hora."','".substr($Codequ,0, strpos($Codequ,"-"))."','".substr($Codexa,0,strpos($Codexa,"-"))."','".$Fecha."','".$Hi."','".$Hf."','".ucwords($Cedula)."','".ucwords($Nompac)."','".$Nitres."','".$Tel."',".$Edad.",'".$Coment."','".$Asistida."','".$Email."','".substr($user,2,strlen($user))."','".substr($Estado,0,1)."',".$wdiasOp.",'".$wfep."',{$valorCco}'C-".$empresa."')";
					
					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					if(isset($wsw1))
					{
						$query = "select id from ".$empresa."_000009 where  cod_equ='".substr($Codequ,0, strpos($Codequ,"-"))."' and fecha='".$Fecha."' and hi='".$Hi."'";
						$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$row = mysql_fetch_array($err);
						$query = "insert ".$empresa."_000008 (medico,fecha_data,hora_data, Fecha, Hora, Cita, Identificacion, Paciente, Edad, Sexo, Historia, Sgs, Atencion, Diagnostico1, Dx_Nuevo1, Nrosotf, Nrosatf, Nrosoto, Nrosato, Nrosotl, Nrosatl, Terapeuta, Ri_pac, Ri_ter, Alta, Control, Observaciones, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$Fecha."','".substr($Hi,0,2).":".substr($Hi,2,2).":00','".$row[0]."','".ucwords($Cedula)."','".ucwords($Nompac)."',".$Edad.",'NO APLICA',0,0,'NO APLICA','NO APLICA','NO APLICA','NO',0,0,0,0,0,0,'NO APLICA','NO APLICA','NO APLICA','NO APLICA','.','C-".$empresa."')";
						$err = mysql_query($query,$conex) or die("ERROR GRABANDO HISTORIA : ".mysql_errno().":".mysql_error());
					}

					if ($Email != '' && $pos==true)
                    {
					    envioEmailCita($conex,$wemp_pmla,$Fecha,$Hi,ucwords($Cedula),ucwords($Nompac),$Email,'',$nommed,$empresa,$Codexa,1);
                    }

					echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCFF LOOP=-1>LOS DATOS ESTAN OK!!!!</MARQUEE></FONT>";
					echo "<br><br>";
				break;
				case 3:
					$query = "select id from ".$empresa."_000009 where  cod_equ='".substr($Codequ,0, strpos($Codequ,"-"))."' and fecha='".$Fecha."' and hi='".$Hi."'";
					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$row = mysql_fetch_array($err);
					$query = "delete from ".$empresa."_000008 where  cita= ".$row[0];
					$err = mysql_query($query,$conex);
					$query = "delete from ".$empresa."_000009 where  cod_equ= '".substr($Codequ,0, strpos($Codequ,"-"))."' and fecha='".$Fecha."' and hi='".$Hi."'";
					$err = mysql_query($query,$conex);
					echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#00FF00 LOOP=-1>!!!! TURNO BORRADO !!!!</MARQUEE></FONT>";
					echo "<br><br>";
					unset($Codmed);
					unset($Codexa);
					unset($Nompac);
					unset($Nitres);
					unset($Email);
					unset($nommed);
					unset($Tel);
					unset($Edad);
					unset($Coment);
					unset($Estado);
				break;
			}
		}
		else
		{
			echo "<center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
			switch ($tiperr)
			{
				case 2:
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33cc33 LOOP=-1>EL MEDICO YA TIENE UNA CITA ASIGNADA INCOMPATIBLE CON LA QUE USTED DESEA -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
				echo "<br><br>";
				break;
				case 3:
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#CCCCFF LOOP=-1>LOS DATOS ESTAN INCOMPLETOS -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
				echo "<br><br>";
				break;
			}
		}
	}
	$year = (integer)substr($pos4,0,4);
	$month = (integer)substr($pos4,5,2);
	$day = (integer)substr($pos4,8,2);
	$nomdia=mktime(0,0,0,$month,$day,$year);
	$nomdia = strftime("%w",$nomdia);
	switch ($nomdia)
	{
		case 0:
			$diasem = "DOMINGO";
			break;
		case 1:
			$diasem = "LUNES";
			break;
		case 2:
			$diasem = "MARTES";
			break;
		case 3:
			$diasem = "MIERCOLES";
			break;
		case 4:
			$diasem = "JUEVES";
			break;
		case 5:
			$diasem = "VIERNES";
			break;
		case 6:
			$diasem = "SABADO";
			break;
	}
	if(isset($wsw1))
		echo "<input type='HIDDEN' name= 'wsw1' value='".$wsw1."'>";
	echo "<input type='HIDDEN' name= 'pos2' value='".$pos2."'>";
	echo "<input type='HIDDEN' name= 'pos3' value='".$pos3."'>";
	echo "<input type='HIDDEN' name= 'pos4' value='".$pos4."'>";
	echo "<input type='HIDDEN' name= 'pos5' value='".$pos5."'>";
	echo "<input type='HIDDEN' name= 'pos6' value='".$pos6."'>";
	echo "<input type='HIDDEN' name= 'pos7' value='".$pos7."'>";
	echo "<input type='HIDDEN' name= 'pos8' value='".$pos8."'>";
	echo "<input type='HIDDEN' name= 'pos9' value='".$pos9."'>";
	echo "<input type='HIDDEN' name= 'Diasem' value='".$diasem."'>";
	//                  0       1      2   3  4     5      6       7       8      9      10        11       12      13   14   15
	$query = "select cod_equ,cod_exa,fecha,hi,hf,Cedula,nom_pac,nit_res,telefono,edad,comentario,Asistida,usuario,activo,id,fecsol,Email from ".$empresa."_000009 where  cod_equ='".substr($pos2,0,strpos($pos2,"-"))."' and fecha='".$pos4."' and hi='".$pos5."'";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	//echo $query;
	echo "<table border=0 align=center>";
	if(isset($wsw1))
		if(isset($wesp))
			echo "<li><A HREF='000001_prx5.php?wequ=".$pos2."&wfec=".$pos4."&empresa=".$empresa."&wsw1=".$wsw1."&wesp=".$wesp."{$wccoUrl}{$wemp_pmlaUrl}'>Retornar</A><br>";
		else
			echo "<li><A HREF='000001_prx5.php?wequ=".$pos2."&wfec=".$pos4."&empresa=".$empresa."&wsw1=".$wsw1."{$wccoUrl}{$wemp_pmlaUrl}'>Retornar</A><br>";
	else
		if(isset($wesp))
			echo "<li><A HREF='000001_prx5.php?wequ=".$pos2."&wfec=".$pos4."&empresa=".$empresa."&wesp=".$wesp."{$wccoUrl}{$wemp_pmlaUrl}'>Retornar</A><br>";
		else
			echo "<li><A HREF='000001_prx5.php?wequ=".$pos2."&wfec=".$pos4."&empresa=".$empresa."{$wccoUrl}{$wemp_pmlaUrl}'>Retornar</A><br>";
	//if ($num > 0)
		//echo "<li><A HREF='000001_prx4.php?par1=".$pos2."&par2=".$pos4."&par3=".$pos5."&empresa=".$empresa."' target = '_blank'>Imprimir</A><br>";
	if ($num > 0)
	{
		$row = mysql_fetch_array($err);
		$pos6=$row[4];
		$pos3=$row[1];
		if(isset($wsw1))
		{
			$query = "select fecha_data, hora_data, id from ".$empresa."_000008 where  cita=".$row[14];
			$err1 = mysql_query($query,$conex);
			$row1 = mysql_fetch_array($err1);
			echo "<li><A HREF='/matrix/det_registro.php?id=".$row1[2]."&pos1=".$empresa."&pos2=".$row1[0]."&pos3=".$row1[1]."&pos4=000008&pos5=0&pos6=".$empresa."&tipo=P&Valor=&Form=000008-".$empresa."-C-Estadisticas&call=2&key=".$empresa."&Pagina=&amp;change=1{$wccoUrl}{$wemp_pmlaUrl}' target='_blank'>Estadisticas</a>";
		}
		echo "<input type='HIDDEN' name= 'wpar' value='1'>";
	}
	else
	{
		$row[0]=substr($pos2,0,strpos($pos2,"-"));			//Equipo
		if(!isset($Codexa))
			$row[1]="0";				//Examen
		else
			$row[1]=$Codexa;
		$row[2]=$pos4;			//Fecha
		$row[3]=$pos5;			//Hi
		if(isset($Hf))
			$pos6=$Hf;
		//$row[4]=$pos6;			//Hf
		if(!isset($Cedula))
		{
			$row[5]="";		//Cedula
			echo "<input type='HIDDEN' name= 'ok' value='1'>";
		}
		else
		{
			$row[5]=$Cedula;
			if(isset($ok))
			{
				$row[5]=$Cedula;
				$query = "select Nom_pac, Nit_res, Descripcion, Telefono, Edad, Fecha, Email from ".$empresa."_000009,".$empresa."_000002 where  Cedula='".$Cedula."' and Nit_res=Nit order by Fecha desc ";
				$err2  = mysql_query($query,$conex);
				$row2  = mysql_fetch_array($err2);
				$Nompac=$row2[0];
				$Nitres=$row2[1];
				$Tel   =$row2[3];
				$Edad  =$row2[4];
				$Email = $row2[6];

			}
		}
		if(!isset($Nompac))
			$row[6]="";					//Paciente
		else
			$row[6]=$Nompac;
		if(!isset($Nitres))
			$row[7]="";					//Responsable
		else
			$row[7]=$Nitres;
		if(!isset($Tel))
			$row[8]="";					//Telefono
		else
			$row[8]=$Tel;
		if(!isset($Edad))
			$row[9]="";					//Edad
		else
			$row[9]=$Edad;
		if(!isset($Coment))
			$row[10]="";				//Cometarios
		else
			$row[10]=$Coment;
		if(!isset($Asistida))
			$row[11]="off";				//Asistida
		else
			$row[11]="on";

		if(!isset($Email))
			$row[16]="";				//Email
		else
			$row[16]=$Email;

		$row[12]="";
		$row[13]="";
		$row[15]=date("Y-m-d");			//Fecha de Peticion
		echo "<input type='HIDDEN' name= 'wpar' value='2'>";
	}
	echo "<input type='HIDDEN' name= 'Codequ' value='".$row[0]."'>";
	echo "<input type='HIDDEN' name= 'Codexa' value='".$row[1]."'>";
	echo "<input type='HIDDEN' name= 'Fecha' value='".$row[2]."'>";
	echo "<input type='HIDDEN' name= 'Hi' value='".$row[3]."'>";
	//echo "<input type='HIDDEN' name= 'Hf' value='".$row[4]."'>";
	echo "<input type='HIDDEN' name= 'wfec' value='".$pos4."'>";
	echo "<input type='HIDDEN' name= 'wequ' value='".$row[0]."'>";
	echo "<tr>";
	echo "<td bgcolor=#999999><b>Item</td></b>";
	echo "<td bgcolor=#999999><b>Valor</b></td>";
	echo "</tr>";
	$query = "select Codigo, Descripcion  from ".$empresa."_000010 where codigo='".substr($pos2,0,strpos($pos2,"-"))."' group by Codigo, Descripcion";
	$err1 = mysql_query($query,$conex);
	$num1 = mysql_num_rows($err1);
	$row1 = mysql_fetch_array($err1);

	if( isset($wcco) ){
		echo "<input type='hidden' name='wcco' value='{$wcco}'>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Sede</td>";
		echo "<td bgcolor=#cccccc>".$nombreCco."</td>";
		echo "</tr>";
	}

	echo "<tr>";
	echo "<td bgcolor=#cccccc>Medico</td>";
	echo "<td bgcolor=#cccccc>".$row1[0]."-".$row1[1]."</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Examen</td>";
	echo "<td bgcolor=#cccccc>";
	echo "<select name='Codexa'>";
	$query = "select codigo,descripcion,preparacion,cod_equipo,activo from ".$empresa."_000011 where cod_equipo='".substr($pos2,0,strpos($pos2,"-"))."' and codigo != '0' order by codigo";
	$err1 = mysql_query($query,$conex);
	$num1 = mysql_num_rows($err1);
	for ($i=0;$i<$num1;$i++)
	{
		$row1 = mysql_fetch_array($err1);
		if(($pos3 != "0" and $pos3 == $row1[0]) or (isset($Codexa) and substr($Codexa,0,strpos($Codexa,"-")) == $row1[0]))
			echo "<option selected>".$row1[0]."-".$row1[1]."</option>";
		else
			echo "<option>".$row1[0]."-".$row1[1]."</option>";
	}
	echo "</td></tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Fecha</td>";
	echo "<td bgcolor=#cccccc>".$diasem." ".$pos4."</td>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Hora Inicio</td>";
	if(substr($pos5,0,2) > "12")
	{
		$hr1 ="". (string)((integer)substr($pos5,0,2) - 12).":".substr($pos5,2,2). " pm ";
		echo "<td bgcolor=#cccccc><font size=2>".$hr1."</font></td>";
	}
	else
		echo "<td bgcolor=#cccccc><font size=2>".substr($pos5,0,2).":".substr($pos5,2,2)."</font></td>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	if(strpos($pos6,"p") !== false)
	{
		$pos6=substr($pos6,0,strpos($pos6," "));
		$pos6=(string)((integer)substr($pos6,0,2) + 12).substr($pos6,2);
	}
	if(strpos($pos6,":") !== false)
		if(substr($pos6,0,strpos($pos6,":")) < 10)
			$pos6="0".substr($pos6,0,strpos($pos6,":")).substr($pos6,strpos($pos6,":")+1);
		else
			$pos6=substr($pos6,0,strpos($pos6,":")).substr($pos6,strpos($pos6,":")+1);
	echo "<td bgcolor=#cccccc>Hora Final</td><td bgcolor=#cccccc><select name='Hf'>";
	//echo "<td bgcolor=#cccccc>Hora Final</td><td bgcolor=#cccccc>";
	$whi = $pos5;
	$inc = $pos9;
	$part1 = (int)substr($whi,0,2);
	$part2 = (int)substr($whi,2,2);
	$part2 = $part2 + $inc;
	while ($part2 >= 60)
	{
		$part2 = $part2 - 60;
		$part1 = $part1 + 1;
	}
	$whf = (string)$part1.(string)$part2;
	if ($part1 < 10)
		$whf = "0".$whf;
	if ($part2 < 10)
		$whf = substr($whf,0,2)."0".substr($whf,2,1);
	$whi = $whf;
	$wul = $pos7;
	$part1 = (int)substr($whi,0,2);
	$part2 = (int)substr($whi,2,2);
	$part2 = $part2 + $inc;
	while ($part2 >= 60)
	{
		$part2 = $part2 - 60;
		$part1 = $part1 + 1;
	}
	$whf = (string)$part1.(string)$part2;
	if ($part1 < 10)
		$whf = "0".$whf;
	if ($part2 < 10)
		$whf = substr($whf,0,2)."0".substr($whf,2,1);
	$weq=0;
	while ($whi <= $wul)
	{
		if($whi == $wul)
			$weq=1;
		if(substr($whi,0,2) > "12")
		{
			$hr1 ="". (string)((integer)substr($whi,0,2) - 12).":".substr($whi,2,2). " pm ";
			if($pos6 == $whi)
				echo "<option selected><font size=2>".$hr1."</font></option>";
			else
				echo "<option><font size=2>".$hr1."</font></option>";
		}
		else
			if($pos6 == $whi)
				echo "<option selected><font size=2>".substr($whi,0,2).":".substr($whi,2,2)."</font></option>";
			else
				echo "<option><font size=2>".substr($whi,0,2).":".substr($whi,2,2)."</font></option>";
		$whi = $whf;
		$part1 = (int)substr($whi,0,2);
		$part2 = (int)substr($whi,2,2);
		$part2 = $part2 + $inc;
		while ($part2 >= 60)
		{
			$part2 = $part2 - 60;
			$part1 = $part1 + 1;
		}
		$whf = (string)$part1.(string)$part2;
		if ($part1 < 10)
			$whf = "0".$whf;
		if ($part2 < 10)
			$whf = substr($whf,0,2)."0".substr($whf,2,1);
	}
	if($weq == 0)
	if(substr($whi,0,2) > "12")
	{
		$hr1 ="". (string)((integer)substr($whi,0,2) - 12).":".substr($whi,2,2). " pm ";
		if($pos6 == $whi)
			echo "<option selected><font size=2>".$hr1."</font></option>";
		else
			echo "<option><font size=2>".$hr1."</font></option>";
	}
	else
		if($pos6 == $whi)
			echo "<option selected><font size=2>".substr($whi,0,2).":".substr($whi,2,2)."</font></option>";
		else
			echo "<option><font size=2>".substr($whi,0,2).":".substr($whi,2,2)."</font></option>";
	echo "</td>";
	echo "</tr>";

	//*******************************************************************
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Fecha de Petici&oacute;n</td>";
	echo "<td bgcolor=#cccccc><input type='TEXT' name='wfep' size=12 maxlength=12 value='".$row[15]."' id='wfep' readonly=readonly>&nbsp&nbsp<IMG SRC='/matrix/images/medical/citas/calendar.png' id='trigger1' style='vertical-align:middle;'></td>";
	?>
	<script type="text/javascript">//<![CDATA[
		Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfep',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
	//]]></script>
	<?php
	echo "</td>";

	echo "<tr>";
	echo "<td bgcolor=#cccccc>Cedula</td>";
	echo "<td bgcolor=#cccccc><input type='TEXT' name='Cedula' size=12 maxlength=15 value='".$row[5]."' onblur='enter()'></td>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Paciente</td>";
	echo "<td bgcolor=#cccccc><input type='TEXT' name='Nompac' size=50 maxlength=50 value='".$row[6]."'></td>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Responsable_Cuenta</td>";
	echo "<td bgcolor=#cccccc>";
	echo "<select name='Nitres'>";
	$query = "select descripcion,Empresa from ".$empresa."_000013,".$empresa."_000002 ";
	$query .= "  WHERE Medico_Tratante =  '".substr($pos2,0,strpos($pos2,"-"))."' ";
	$query .= "       AND Empresa =  Nit ";
	$query .= "  order by descripcion ";
	$err1 = mysql_query($query,$conex);
	$num1 = mysql_num_rows($err1);
	for ($i=0;$i<$num1;$i++)
	{
		$row1 = mysql_fetch_array($err1);
		if ($row[7] == $row1[1])
			echo "<option selected>".$row1[0]."-".$row1[1]."</option>";
		else
			echo "<option>".$row1[0]."-".$row1[1]."</option>";
	}
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Telefono</td>";
	echo "<td bgcolor=#cccccc><input type='TEXT' name='Tel' size=30 maxlength=30 value='".$row[8]."'></td>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Correo electr&oacutenico</td>";
	echo "<td bgcolor=#cccccc><input type='TEXT' id='Email' name='Email' size=60 maxlength=80 value='".$row[16]."' onChange='validarEmail(this.value);'></td>";	
	echo "</tr>";	
	echo "<tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Edad</td>";
	echo "<td bgcolor=#cccccc><input type='TEXT' name='Edad' size=3 maxlength=3 value='".$row[9]."'></td>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Comentarios</td>";
	echo "<td><textarea name='Coment' cols=60 rows=5>".$row[10]."</textarea>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Insertar <br> Comentario Fijo</td>";
	echo "<td bgcolor=#cccccc><input type='checkbox' name='Fijo'>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Asistio</td>";
	if($row[11] == "on")
		echo "<td bgcolor=#cccccc><input type='checkbox' name='Asistida' checked>";
	else
		echo "<td bgcolor=#cccccc><input type='checkbox' name='Asistida'>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Activo</td>";
	echo "<td bgcolor=#cccccc>";
	echo "<select name='Estado'>";
	if ($row[13] == substr("A-Activo", 0, 1))
		echo "<option selected>A-Activo</option>";
	else
		echo "<option>A-Activo</option>";
	if ($row[13] == substr("I-Inactivo", 0, 1))
		echo "<option selected>I-Inactivo</option>";
	else
		echo "<option>I-Inactivo</option>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc colspan=2 align=center><input type='submit' value='GRABAR'></td>";
	echo "</tr>";
	echo "</table>";
	echo "<table border=0 align=center>";
	echo "<tr><td><A HREF='#Arriba'><B>Arriba</B></A></td></tr></table>";
	include_once("free.php");
}
?>
</body>
</html>
