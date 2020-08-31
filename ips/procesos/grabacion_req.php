
<head>
  	<title>MATRIX  Grabacion de Requerimientos - S.G.R</title>

	<script type="text/javascript">
	function enter()
	{
		document.forms.grabacion_req.submit();
	}
	</script>

</head>

<body onload=ira();>

</body>

<?php
include_once("conex.php");

/* **********************************************************
   *     PROGRAMA PARA LA GESTION Y ADMINISTRACION DE       *
   *                   REQUERIMIENTOS                       *
   **********************************************************/

//==================================================================================================================================
//PROGRAMA                   : grabacion_req.php
//AUTOR                      : Juan David Jaramillo R.
$wautor="Juan D. Jaramillo R.";
//FECHA CREACION             : Noviembre 20 de 2006
//FECHA ULTIMA ACTUALIZACION :
$wactualiz="(Version Noviembre 23 de 2006)";
//DESCRIPCION
//================================================================================================================================\\
//Este programa permite grabar a los usuarios requerimientos y nuevas necesidades al depto de sistemas y confirmando automatica-  \\
//mente su recepcion medinte correos electronicos.   Permitiendo asi dar calidad de servicio y generacion de estadisticas de      \\
//requerimientos x usuario, tiempos de atencio y oportunidad en el servicio.													  \\
//================================================================================================================================\\

//================================================================================================================================\\
//================================================================================================================================\\
//ACTUALIZACIONES                                                                                                                 \\
//================================================================================================================================\\
//                                                                                                                                \\
//________________________________________________________________________________________________________________________________\\
//X X X X X X X X X  ## DE 2006:                                                                                                  \\
//________________________________________________________________________________________________________________________________\\
//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx\\
//                                                                                                                                \\
//________________________________________________________________________________________________________________________________\\

// COLORES
$wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
$wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
$wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
$wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro
$wclcy="#A4E1E8"; //COLOR DE TITULOS  -- Cyan
$color="#999999";


// FUNCIONES

function enviar_correo($wcaso,$e_mail,$wtipreq)
{
	global $mail;
	global $email;
	global $emailhr;

	if ($wtipreq=="Software")
	   $wmail=$email;
	  else
	     $wmail=$emailhr; 
	
	$mail->IsSMTP(); // telling the class to use SMTP
	//$mail->Host = "192.168.1.100"; // SMTP server
	$mail->Host = "132.1.18.1"; // SMTP server
	$mail->From = $e_mail;
	$mail->AddAddress($wmail);
	$mail->Subject = "Se Ha Grabado Un Nuevo Requerimiento...";
	$mail->Body = "Cordial Saludo ! \n\n Se ha grabado un nuevo requerimiento con numero de caso: ".$wcaso." !!! ";
	$mail->WordWrap = 100;

	if(!$mail->Send())
		$resulMai=0;
	else
		$resulMai=1;

	return $resulMai;
}

function cargar_datos()
{
	global $wsgrcsc;
	global $wsgrnom;
	global $wsgrext;
	global $wsgrare;
	global $wsgrmai;
	global $wbasedato;
	global $conex;
	global $wusuario;

	$q= " SELECT ugrnom,ugrext,ugrare,ugrema ".
		"   FROM ".$wbasedato."_000124 ".
		"  WHERE ugrcod ='".$wusuario."'".
		"    AND ugrest = 'on'";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$row = mysql_fetch_array($res);

		$wsgrnom=$row[0];
		$wsgrext=$row[1];
		$wsgrare=$row[2];
		$wsgrmai=$row[3];

		$existe="true";
	}
	else
	{
		$existe="false";
	}

	return $existe;
}

function tomar_numcaso()
{
	global $wbasedato;
	global $conex;

	$q= " SELECT max(sgrcsc) ".
		"   FROM ".$wbasedato."_000122 ";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);
	$row = mysql_fetch_array($res);

	if ($row[0] != "")
		$wsgrcsc1=$row[0]+1;  //valor del consecutivo para el Requerimiento.

	return $wsgrcsc1;
}

function iniciar()
{
	//$wsgrcsc="";
	$wsgrapl="";
	$wsgrmen="";
	$wsgrpro="";
	$wsgrtpr="";
	$wsgrurg="";
	$wdes="";

	//echo "<input type='HIDDEN' name= 'wsgrcsc' value='".$wsgrcsc."'>";
	echo "<input type='HIDDEN' name= 'wsgrapl' value='".$wsgrapl."'>";
	echo "<input type='HIDDEN' name= 'wsgrmen' value='".$wsgrmen."'>";
	echo "<input type='HIDDEN' name= 'wsgrpro' value='".$wsgrpro."'>";
	echo "<input type='HIDDEN' name= 'wsgrtpr' value='".$wsgrtpr."'>";
	echo "<input type='HIDDEN' name= 'wsgrurg' value='".$wsgrurg."'>";
	echo "<input type='HIDDEN' name= 'wdes' value='".$wdes."'>";
}

// INICIALIZACION DE VARIABLES
require("class.phpmailer.php");
$mail = new PHPMailer();

$valido=1;
$wsgresc='off';
$wsgrreshr='JUAN ARLEY';
$emailhr='sistemas@clinicadelsur.com';    //Este correo se utiliza cuando el requerimiento es de hardware

$wsgrres='JUAN CARLOS HERNANDEZ';
$email='juanc@pmamericas.com';            //Este correo se utiliza para recibir los requerimientos de Software

session_start();
if (!isset($user))
{
	if(!isset($_SESSION['user']))
		session_register("user");
}

if(!isset($_SESSION['user']))
	echo "Error, Usuario NO Registrado";
else
{
	echo "<form name='grabacion_req' action='grabacion_req.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'wbasedato' value='".$wbasedato."'>";

	$pos = strpos($user,"-");
    $wusuario = substr($user,$pos+1,strlen($user));

    $wfecha=date("Y-m-d");
    $whora = (string)date("H:i:s");

    // Inicio de captura de datos en formulario

	echo "<p align=right><font size=2><b>Autor: ".$wautor."</b></font></p>";
   	echo "<table border=0 ALIGN=CENTER width=70%>";
	echo "<tr><td align=center><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' height='100' width='350'></td></tr>";
	echo "<tr><td><br></td></tr>";
	echo "<tr><td align=center bgcolor=".$wcf2."><font size=5 text color=#FFFFFF><b>GESTION DE REQUERIMIENTOS (S.G.R)</b></font></td></tr>";
	echo "<tr><td><br></td></tr>";
	echo "<tr><td align=center bgcolor=".$wcf."><b><font text color=".$wclfg.">Grabacion del Requerimiento</font></b></td></tr>";
	echo "</table>";

	echo "<br>";

	echo "<table border=1 ALIGN=CENTER width=70%>";
	echo "<tr>";
	if (isset($wsgrcsc))
		echo "<td align=center bgcolor=".$wcf."><font text color=".$wclfg." size=2><b>Nro Requerimiento: </b></font><font text color=".$wclfg." size=5><b><INPUT TYPE='text' NAME='wsgrcsc' VALUE='".$wsgrcsc."' onchange='enter()'></b></font></td>";
	else
		echo "<td align=center bgcolor=".$wcf."><font text color=".$wclfg." size=2><b>Nro Requerimiento: </b></font><font text color=".$wclfg." size=5><b><INPUT TYPE='text' NAME='wsgrcsc' onchange='enter()'></td>";
	echo "<td align=center bgcolor=".$wcf."><b><font text color=".$wclfg." size=2> Usuario: </font></b>".$wusuario."</td>";
	echo "<td align=center bgcolor=".$wcf."><b><font text color=".$wclfg." size=2> Fecha del Requerimiento: </font></b><br>".$wfecha."</td>";
	echo "<td align=center bgcolor=".$wcf."><b><font text color=".$wclfg." size=2> Hora: </font></b><br>".$whora."</td>";
	echo "</tr>";
	echo "</table>";

	echo "<br>";

	$existe=cargar_datos();

	if ($existe=="false")
	{
		echo "<table border=0 ALIGN=CENTER width=70%>";
		echo "<tr><td colspan=3 align=center bgcolor=".$wcf."><b><font text color=".$wclfg." size=3> Informacion del Usuario </font></b></td></tr>";
		if (!isset($wsgrnom))
		$wsgrnom="";
		else $wsgrnom=strtoupper($wsgrnom);
		echo "<td align=center bgcolor=".$wcf."><b><font text color=".$wclfg." size=3> Nombre: </font></b><br>
   		  <input type='TEXT' name='wsgrnom' value='".$wsgrnom."' size=40 maxlength=40 onchange='enter'()></td>";
		if (!isset($wsgrext))
		$wsgrext="";
		echo "<td align=center left bgcolor=".$wcf."><b><font text color=".$wclfg." size=3> Extension: </font></b><br>
   		  <input type='TEXT' name='wsgrext' value='".$wsgrext."' size=20 maxlength=20 onchange='enter'()></td>";
		if (!isset($wsgrare))
		$wsgrare="";
		else $wsgrare=strtoupper($wsgrare);
		echo "<td align=center bgcolor=".$wcf."><b><font text color=".$wclfg." size=3> Area: </font></b><br>
   		  <input type='TEXT' name='wsgrare' value='".$wsgrare."' size=30 maxlength=30 onchange='enter'()></td></tr>";
		echo "<tr>";
		if (!isset($wsgrmai))
		$wsgrmai="";
		else $wsgrmai=strtolower($wsgrmai);
		echo "<td colspan=1 align=center bgcolor=".$wcf."><b><font text color=".$wclfg." size=3> E-mail: </font></b></td>";
   		echo "<td colspan=2 left=center bgcolor=".$wcf."><input type='TEXT' name='wsgrmai' value='".$wsgrmai."' size=45 maxlength=45 onchange='enter'()></td>";
		echo "</tr>";
	}
	else
	{
		echo "<table border=0 ALIGN=CENTER width=70%>";
		echo "<tr><td colspan=3 align=center bgcolor=".$wcf."><b><font text color=".$wclfg." size=3> Informacion del Usuario </font></b></td></tr>";
		echo "<td align=center bgcolor=".$wcf."><b><font text color=".$wclfg." size=3> Nombre: </font></b><br>".$wsgrnom."</td>";
		echo "<td align=center left bgcolor=".$wcf."><b><font text color=".$wclfg." size=3> Extension: </font></b><br>".$wsgrext."</td>";
		echo "<td align=center bgcolor=".$wcf."><b><font text color=".$wclfg." size=3> Area: </font></b><br>".$wsgrare."</td></tr>";
		echo "<tr><td colspan=1 align=center bgcolor=".$wcf."><b><font text color=".$wclfg." size=3> E-mail: </font></b></td>";
		echo "<td align=center colspan=2 bgcolor=".$wcf."><font text size=4>".$wsgrmai."</font></td></tr>";
	}
	echo "<tr><td><br></td></tr>";
   	echo "<tr><td colspan=3 align=center bgcolor=".$wcf."><b><font text color=".$wclfg." size=3> Identificacion del Requerimiento </font></b></td></tr>";
	echo "<tr>";
	echo "<td colspan=3	 align=left bgcolor=".$wcf."><b><font text color=".$wclfg." size=2> Clase de Requerimiento: </font></b>
		  <select name='wclareq' onchange='enter()' ondblclick='enter()'>";
    if (isset($wclareq))
    {
    	if($wclareq== "Software")
    	{	echo "<option selected>Software</option>";
    		echo "<option>Hardware</option>";
    	}
    	else
    	{
    		echo "<option>Software</option>";
    		echo "<option selected>Hardware</option>";
    	}
    }
    else
    {
    	echo "<option></option>";
    	echo "<option>Software</option>";
    	echo "<option>Hardware</option>";
    }
   	echo "</select></td>";
   	echo "</tr>";

   	echo "<tr>";
   	echo "<td colspan=3 align=left bgcolor=".$wcf."><table align=center cellspacing=0 cellpadding=0 border=1>";
   	if (!isset($wclareq))
   		$wclareq="";
   	if ($wclareq== "Software")
   	{
   		$wsgrclr="S";
   		echo "<tr>";
   		if (!isset($wsgrapl))
   			$wsgrapl="";
   		echo "<td align=left bgcolor=".$wcf."><b><font text color=".$wclfg." size=2> Sistema:</b></font>
		  	  <font size=2><input type='radio' name='wsgrapl' value=MATRIX><b>Matrix	 &nbsp&nbsp&nbsp
        				   <input type='radio' name='wsgrapl' value=CLISUR-OTROS><b>Otros	 &nbsp&nbsp&nbsp</font></td>";
   		if (!isset($wsgrmen))
   			$wsgrmen="";
   		else $wsgrmen=strtoupper($wsgrmen);
   		echo "<td align=left bgcolor=".$wcf."><b><font text color=".$wclfg." size=2> Menu: </font></b>
   		   	  <input type='TEXT' name='wsgrmen' value='".$wsgrmen."' size=30 maxlength=30 onchange='enter'()></td>";
   		if (!isset($wsgrpro))
   			$wsgrpro="";
   		else $wsgrpro=strtoupper($wsgrpro);
   		echo "<td align=left bgcolor=".$wcf."><b><font text color=".$wclfg." size=2> Programa : </font></b>
   		   	  <input type='TEXT' name='wsgrpro' value='".$wsgrpro."' size=30 maxlength=30 onchange='enter'()></td></tr>";
		if (!isset($wsgrtpr))
   			$wsgrtpr="";
   		echo "<td colspan=1 align=center bgcolor=".$wcf."><b><font text color=".$wclfg." size=2> Tipo de Requerimiento:</b></font>
				<font size=2><input type='radio' name='wsgrtpr' value=S><b>Solicitud	 &nbsp&nbsp&nbsp
        		<input type='radio' name='wsgrtpr' value=E><b>Error		 &nbsp&nbsp&nbsp</font></td>";
   		if (!isset($wsgrurg))
   			$wsgrurg="";
   		echo "<td colspan=2 align=center bgcolor=".$wcf."><b><font text color=".$wclfg." size=2> Prioridad:</b></font>
				<font size=2><input type='radio' name='wsgrurg' value=A><b>Alta	 &nbsp&nbsp&nbsp
				<input type='radio' name='wsgrurg' value=M><b>Media	 &nbsp&nbsp&nbsp
        		<input type='radio' name='wsgrurg' value=B><b>Baja	 &nbsp&nbsp&nbsp</font></td></tr>";
   		echo "<tr>";
		echo "<td bgcolor='".$wcf."' colspan=3 align=center><b><font text color=".$wclfg." size=2>Descripcion del Requerimiento:</font></b></td>";
		echo "</tr>";
		if (!isset($wdes))
			$wdes='';
		echo "<tr>";
		echo "<td bgcolor='".$wcf."' colspan=3 align=center><b><textarea name='wdes' cols='80' rows='8'>".$wdes."</textarea></td>";
		echo "</tr>";
   	}
	else
	{
		$wsgrclr="H";
		$wsgrapl="CLISUR -OTROS";
		$wsgrmen="";
		$wsgrpro="";
		$wsgrtpr="";
		$wsgrurg="";

		echo "<tr>";
		echo "<td bgcolor='".$wcf."'align=center><b><font text color=".$wclfg." size=2>Descripcion del Requerimiento:</font></b></td>";
		echo "</tr>";
		if (!isset($wdes))
			$wdes='';
		echo "<tr>";
		echo "<td bgcolor='".$wcf."' align=center><b><textarea name='wdes' cols='80' rows='8'>".$wdes."</textarea></td>";
		echo "</tr>";
	}
	echo "</table>";
	echo "</tr>";

	echo "<tr><td align=center bgcolor=#dddddd colspan=3><font color=#000066 size=3><b>Grabar el Requerimiento</b><input type='checkbox' name='wgrabar'></font></td></tr>";
	echo "<td align=center bgcolor=#cccccc colspan=3><input type='submit' value='OK'></td>";
	echo "</table>";

	if (isset($wgrabar))
	{
		if((!isset($wsgrnom) or ($wsgrnom == "")) and $valido==1)
		{
		?>
			<script>
			alert ("Debe Ingresar un Nombre para el Usuario");
			function ira(){document.grabacion_req.wsgrnom.focus();}
			</script>
		<?php
		$valido=0;
		}

		if((!isset($wsgrext) or ($wsgrext == "")) and $valido==1)
		{
		?>
			<script>
			alert ("Debe Ingresar una Extension de Contacto");
			function ira(){document.grabacion_req.wsgrext.focus();}
			</script>
		<?php
		$valido=0;
		}

		if((!isset($wsgrare) or ($wsgrare == "")) and $valido==1)
		{
		?>
			<script>
			alert ("Debe Ingresar el Area de Trabajo");
			function ira(){document.grabacion_req.wsgrare.focus();}
			</script>
		<?php
		$valido=0;
		}

		if((!isset($wsgrmai) or ($wsgrmai == "")) and $valido==1)
		{
		?>
			<script>
			alert ("Debe Ingresar un Correo de Contacto");
			function ira(){document.grabacion_req.wsgrmai.focus();}
			</script>
		<?php
		$valido=0;
		}

		if($wsgrclr=="S")
		{
			if((!isset($wsgrapl) or ($wsgrapl== "")) and $valido==1)
			{
			?>
				<script>
				alert ("Debe Ingresar un Sistema para el Programa");
				function ira(){document.grabacion_req.wsgrapl.focus();}
				</script>
			<?php
			$valido=0;
			}
			if((!isset($wsgrmen) or ($wsgrmen== "")) and $valido==1)
			{
			?>
				<script>
				alert ("Debe Ingresar un Menu para el Programa");
				function ira(){document.grabacion_req.wsgrmen.focus();}
				</script>
			<?php
			$valido=0;
			}
			if((!isset($wsgrpro) or ($wsgrpro== "")) and $valido==1)
			{
			?>
				<script>
				alert ("Debe Ingresar el Nombre del Programa");
				function ira(){document.grabacion_req.wsgrpro.focus();}
				</script>
			<?php
			$valido=0;
			}
			if((!isset($wsgrtpr) or ($wsgrtpr== "")) and $valido==1)
			{
			?>
				<script>
				alert ("Debe Seleccionar el Tipo de Requerimiento");
				function ira(){document.grabacion_req.wsgrtpr.focus();}
				</script>
			<?php
			$valido=0;
			}

			if((!isset($wsgrurg) or ($wsgrurg== "")) and $valido==1)
			{
			?>
				<script>
				alert ("Debe Seleccionar la Prioridad del Requerimiento");
				function ira(){document.grabacion_req.wsgrurg.focus();}
				</script>
			<?php
			$valido=0;
			}
		}

		if((!isset($wdes) or ($wdes == "")) and $valido==1)
		{
		?>
			<script>
			alert ("Debe Ingresar una Descripcion del Requerimiento");
			function ira(){document.grabacion_req.wdes.focus();}
			</script>
		<?php
		$valido=0;
		}

		if ($valido==1)
		{
			//Aca traigo el consecutivo para el Requerimiento
			$wsgrcsc=tomar_numcaso();
			echo "<input type='HIDDEN' name= 'wsgrcsc' value='".$wsgrcsc."'>";

			$valido=enviar_correo($wsgrcsc,$wsgrmai,$wclareq);
			
			if ($wclareq== "Software")
			   $wresponsable=$wsgrres;
			  else
			     $wresponsable=$wsgrreshr; 
			
			if ($valido==1)
			{
				$q= " INSERT INTO ".$wbasedato."_000122 (Medico,Fecha_data,Hora_data,sgrcsc,sgrnom,sgrext,sgrare,sgrmai,sgrclr,sgrapl, ".
		    		                                    " sgrmen,sgrpro,sgrtre,sgrurg,sgrdes,sgresc,sgrres,sgrest,Seguridad) ".
  					" VALUES ('".$wbasedato."','".$wfecha."','".$whora."',".$wsgrcsc.",'".$wsgrnom."','".$wsgrext."','".$wsgrare.
	  				       "','".$wsgrmai."','".$wsgrclr."','".$wsgrapl."','".$wsgrmen."','".$wsgrpro."','".$wsgrtpr."','".$wsgrurg.
  					       "','".$wdes."','".$wsgresc."','".$wresponsable."','S','C-".$user."')";
        		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

        		echo "<tr>";
				echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
				echo "<tr><td align='center'><font size=2 color='#000080' face='arial'><b>Su Requerimiento fue Grabado Exitosamente, en poco tiempo el Dpto. de Sistemas se Comunicara con Usted.</td><tr>";
				//echo "<td align=center bgcolor=".$wcf."><INPUT TYPE='button' NAME='buscador' VALUE='OK' onclick='enter()'></td>";
				echo "</table>";

				if ($existe=="false")
				{
					$q= " INSERT INTO ".$wbasedato."_000124 (Medico,Fecha_data,Hora_data,ugrcod,ugrnom,ugrext,ugrare,ugrema,ugrest,Seguridad) ".
  						" VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wusuario."','".$wsgrnom."','".$wsgrext."','".$wsgrare."','".$wsgrmai."','on','C-".$user."')";
  		      		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				}
				iniciar();
				$valido=0;
			}
			else
			{
        		echo "<tr>";
				echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
				echo "<tr><td align='center'><font size=2 color='#000080' face='arial'><b>Su Requerimiento NO fue Grabado, Favor Verifique Su Cuenta De Correo Que Sea Valida!!!</td><tr>";
				echo "</table>";
				$valido=0;
			}
		}
	}
}

?>

