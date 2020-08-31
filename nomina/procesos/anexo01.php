<html>
<head>
<title>Actualizacion de datos para retencion en la fuente</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <link href="encuesta01_style.css" rel="stylesheet">
</head>

<script>
    function ira()
    {
	 document.anexo01.wnom.focus();
	}
</script>

<!--<BODY onload=ira() BGCOLOR="" TEXT="#000066">  -->
<BODY onload=ira() >

  <!-- Loading Calendar JavaScript files -->  <!-- Loading Theme file(s) -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script> 

<script type="text/javascript">

	function enter()
	{
		document.forms.anexo01.submit();   // Ojo para la funcion anexo01 <> Anexo01  (sencible a mayusculas)
	}

    
 	// Fn que solo deja digitar los nros del 0 al 9, el . 
	function numeros(e)
	{
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    letras = " 0123456789";
    especiales = [8,37,39,46];
 
    tecla_especial = false
    for(var i in especiales)
	{
     if(key == especiales[i])
	 {
      tecla_especial = true;
      break;
     }
    }
 
    if(letras.indexOf(tecla)==-1 && !tecla_especial)
        return false;
	}
	
 </script>

<?php
include_once("conex.php");

//==========================================================================================================================================
//PROGRAMA				      :Actualizacion de datos para retencion en la fuente ANEXO 1                                                                 
//AUTOR				          :Jair  Saldarriaga Orozco.                                                                        
//FECHA CREACION			  :Marzo 19 de 2015
//FECHA ULTIMA ACTUALIZACION  :Marzo 19 de 2015.  Abril 4 de 2016 se modificaron las preguntas
//FECHA DE ACTUALIZACION      :Abril 9 de 2018 - se modificaron las preguntas por Angela Ocampo
//FECHA DE ACTUALIZACION      :Feb 25 2020 - se modifican las conexiones de acuerdo al parametro, tambien se adicionan y quitan algunas preguntas

$wactualiz="PROGRAMA: anexo01.php Ver. Febrero 24 de 2020 ";

session_start();


if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");

//echo "empresa:".$wemp_pmla; para saber con cual empresa viene

//Function validar_datos($ciu,$fec,$nom,$ced,$re1,$re2,$dep,$re3,$re4,$re5,$re6,$re7,$re8)//aqui agregue las otras respuestas desde $dep al final
Function validar_datos($ciu,$fec,$nom,$ced,$dep,$re3,$re4,$re5,$re6,$re7,$re8,$re9)//aqui agregue las otras respuestas desde $dep al final

{  global $todok;
   global $msgerr;
   
   $todok = true;
   $msgerr = "";
   
   if (empty($ciu))
   {
      $todok = false;
      $msgerr=$msgerr." Debe especificar la ciudad de diligenciamieno del anexo.";   
   }
   
   if (empty($fec))
   {
      $todok = false;
      $msgerr=$msgerr." Debe existir fecha de diligenciamiento del anexo.";  
   }
 
   if (empty($nom))
   {
      $todok = false;
      $msgerr=$msgerr." El nombre no puede estar en blanco.";   
   }
   
   if (empty($ced))
   {
      $todok = false;
      $msgerr=$msgerr." Debe digitar su numero de cedula.";   
   } 
     
  /*if (empty($re1))
   {
      $todok = false; 
      $msgerr=$msgerr." La pregunta nro 1 debe tener respuesta.";
   }
   
    if (empty($re2))
   {
      $todok = false; 
      $msgerr=$msgerr." La pregunta nro 2 debe tener respuesta.";
   }*/


	//---- copio esta parte que corresponde a las preguntas que pase


	if ( trim($dep) == "" )
		//- esta es en la lista la respuesta 1
	{
		$todok = false;
		$msgerr=$msgerr." Debe especificar el nro de dependientes o cero si no tiene.";
	}

	if (empty($re3)) 
		//esta es en la lista la respuesta 2
	{
		$todok = false;
		$msgerr=$msgerr." La pregunta nro 2 debe tener respuesta.";
	}

		if (empty($re4))
		// esta es en la lista la respuesta 3

	{
		$todok = false;
		$msgerr=$msgerr." La pregunta nro 3 debe tener respuesta.";
	}

	if (empty($re5))
		//esta es en la lista la resapuesta 4
	{
		$todok = false;
		$msgerr=$msgerr." La pregunta nro 4 debe tener respuesta.";
	}

	if (empty($re6))
		//esta es en la lista la resapuesta 5
	{
		$todok = false;
		$msgerr=$msgerr." La pregunta nro 5 debe tener respuesta.";
	}

	if (empty($re8))
		//esta es en la lista la resapuesta 7
	{
		$todok = false;
		$msgerr=$msgerr." La pregunta nro 7 debe tener respuesta.";
	}
	
	if (empty($re9))
		//esta es en la lista la resapuesta 8
	{
		$todok = false;
		$msgerr=$msgerr." La pregunta nro 8 debe tener respuesta.";
	}

   return $msgerr;   
}  

mysql_select_db("matrix") or die("No se selecciono la base de datos");  

	$key = substr($user,2,strlen($user));
	if (strlen($key)==5)
	  $codigo=$key;
	else
	  $codigo=substr($key,2,5);

//conexion a la base de datos de acuerdo a la empresa
if ($wemp_pmla == '01')
		$conexN = odbc_connect("queryx7","","") or die(odbc_errormsg());  //Promotora
if ($wemp_pmla == '05')
	{
		$conexN = odbc_connect("queryx7LMLA","","") or die(odbc_errormsg());  //Laboratorio
		//Para los usuarios del laboratorio que comienzan con L se las quito
		if (strlen($key)==5)
			$codigo=$key;
		else
			$codigo=substr($key,1,5);
	}
if ($wemp_pmla == '04')
		$conexN  = odbc_connect("queryx7PAT","","") or die(odbc_errormsg());  //Patologia
if ($wemp_pmla == '02')
		$conexN  = odbc_connect("queryx7CS","","") or die(odbc_errormsg());  //Clisur conexion
if ($wemp_pmla == '10')
		$conexN  = odbc_connect("queryx7IDC","","") or die(odbc_errormsg());  //IDC

echo "<form name='anexo01' action='anexo01.php?".$wemp_pmla."' method=post >";  
echo "<input type='HIDDEN' name= 'wemp_pmla' value='".$wemp_pmla."'>";
// Almaceno el Id del registro enviado
$wid=trim($wid); 
		echo "<center><table style='border:3px'>";
		echo "<td align=center bgcolor=#FCFCFC colspan=1><b>";
		echo "<font text color=#003366 size=5>CERTIFICACION PARA EMPLEADOS<br>";
		echo "<font text color=#003366 size=4><b>(Diligencie esta certificación con la ayuda de su contador o asesor tributario)";
		echo "<br>";
		echo "</font></b><br></td>";
		echo "<br>";

//echo "Codigo:".$codigo; este era para verificar el codigo del empleado que  llega

	  //Tomo los datos de NOPER
	  $query="Select perced,perno1,perno2,perap1,perap2,perbme "
           ." From noper Where percod = '".$codigo."'";

	  $resultado = odbc_do($conexN,$query);                    // Ejecuto el query  
      if (odbc_fetch_row($resultado))                         // Encontro 
	  {
        $wnom=TRIM(odbc_result($resultado,2))." ".TRIM(odbc_result($resultado,3))." ".TRIM(odbc_result($resultado,4))." ".TRIM(odbc_result($resultado,5));
	    $wced=odbc_result($resultado,1);
		$wsalario=odbc_result($resultado,6);
	  }	
	  else
	  {
		if ($wemp_pmla == '01') //Promotora
			header('Location:https://www.sqlsoftware.nom.co:9443/AtgPMEDICA');
		if ($wemp_pmla == '05')	//Laboratorio
			header('Location:https://www.sqlsoftware.nom.co:9443/AtgLAMERICAS');
		if ($wemp_pmla == '04') //Patologia
			header('Location:https://www.sqlsoftware.nom.co:9443/AtgPAMERICAS');
		if ($wemp_pmla == '02') //Clinica del Sur
			header('Location:https://www.sqlsoftware.nom.co:9443/AtgCSAMERICAS');
		if ($wemp_pmla == '10')	//IDC
			header('Location:https://www.sqlsoftware.nom.co:9449/AtgIDC');
	  }
      
	  //Esto es para controlar que solo diligencien la encuesta las personas que ganen igual o mas de 3676000
	  if ($wsalario < 3676000){
		  if ($wemp_pmla == '01') //Promotora
			header('Location:https://www.sqlsoftware.nom.co:9443/AtgPMEDICA');
		  if ($wemp_pmla == '05')	//Laboratorio
			header('Location:https://www.sqlsoftware.nom.co:9443/AtgLAMERICAS');
		  if ($wemp_pmla == '04') //Patologia
			header('Location:https://www.sqlsoftware.nom.co:9443/AtgPAMERICAS');
		  if ($wemp_pmla == '02') //Clinica del Sur
			header('Location:https://www.sqlsoftware.nom.co:9443/AtgCSAMERICAS');
		  if ($wemp_pmla == '10')	//IDC
			header('Location:https://www.sqlsoftware.nom.co:9449/AtgIDC');
		  
	  }
		  
	  //Con esa Cedula busco si ya lleno diligencio el Anexo 1
	  $anio = date("Y");                                        // Y=Devuelve 4 digitos (2015) y=devuelve 2 digitos(15)
	  //$query = "SELECT anxano,anxced,anxciu,anxfec,anxnom,anxre1,anxre2,anxdep,anxsal,anxviv,anxafc,anxent,anxvlr" // aqui la variable anxent va la pregunta"AportesVoluntarios-a Fondos de Pnensiones y en anxvrl va el valor de la retención en la fuente
	  //                    0      1      2       3     4      5     6      7      8      9      10     11      12
	  //$query = "SELECT anxano,anxced,anxciu,anxfec,anxnom,anxdep,anxsal,anxviv,anxafc,anxent,anxvlr,anxavr,anxicetex" // aqui la variable anxent va la pregunta"AportesVoluntarios-a Fondos de Pnensiones y en anxvrl va el valor de la retención en la fuente
	  $query = "SELECT anxano,anxced,anxciu,anxfec,anxnom,anxdep,anxsal,anxviv,anxafc,anxent,anxvlr,anxavr,anxicetex" // aqui la variable anxent va la pregunta"AportesVoluntarios-a Fondos de Pnensiones y en anxvrl va el valor de la retención en la fuente
	         ." FROM nomina_000019 Where anxano='".$anio."' And anxced=".$wced;	
			
	  $resultado = mysql_query($query);
      $nroreg = mysql_num_rows($resultado);

    if ($nroreg > 0)

	  {
	    if ($wemp_pmla == '01') //Promotora
			header('Location:https://www.sqlsoftware.nom.co:9443/AtgPMEDICA');
		if ($wemp_pmla == '05')	//Laboratorio
			header('Location:https://www.sqlsoftware.nom.co:9443/AtgLAMERICAS');
		if ($wemp_pmla == '04') //Patologia
			header('Location:https://www.sqlsoftware.nom.co:9443/AtgPAMERICAS');
		if ($wemp_pmla == '02') //Clinica del Sur
			header('Location:https://www.sqlsoftware.nom.co:9443/AtgCSAMERICAS');
		if ($wemp_pmla == '10')	//IDC
			header('Location:https://www.sqlsoftware.nom.co:9449/AtgIDC');
	  }

	else {

		if (($nroreg > 0) and ($windicador == "PrimeraVez"))                               // Ya lleno el anexo 1
		{
			$registro = mysql_fetch_row($resultado);
			$wciu = $registro[2];
			$wfec = $registro[3];
			$wnom = $registro[4];
			$wced = $registro[1];
			$wdep = $registro[5];
			$wnro6 = $registro[6];
			$wnro7 = $registro[7];
			$wnro8 = $registro[8];
			$wnro9 = $registro[9];
			$wret = $registro[10]; //aqui para colocar el valor de retencion en la fuente
			$wnro10 = $registro[11];
			$wnro11 = $registro[12];

		}

		echo "<tr><td align=center bgcolor=#FCFCFC colspan=1><b><font text color=#003366 size=3>Ciudad:</font></b>";
		if (isset($wciu))
			echo "<INPUT TYPE='text' NAME='wciu' size=30 maxlength=30 readonly='readonly' onKeyUp='this.value = this.value.toUpperCase();' VALUE='" . $wciu . "'></INPUT>";
		else
			echo "<INPUT TYPE='text' NAME='wciu' size=30 maxlength=30 readonly='readonly' onKeyUp='this.value = this.value.toUpperCase();' VALUE='MEDELLIN'></INPUT>";

		if (!isset($wfec) or $wfec == "")   // Si no esta seteada entonces la inicializo con fecha actual
			$wfec = date("Y-m-d");

		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha: ";
		$cal = "calendario('wfec','1')";
		echo "<input type='TEXT' name='wfec' size=10 maxlength=10  id='wfec' readonly='readonly' value=" . $wfec . " class=tipo3><button id='trigger2' disabled onclick=" . $cal . ">...</button>";
		?>
		<script type="text/javascript">//<![CDATA[
			Zapatec.Calendar.setup({
				weekNumbers: false,
				showsTime: true,
				timeFormat: '12',
				electric: false,
				inputField: 'wfec',
				button: 'trigger2',
				ifFormat: '%Y-%m-%d',
				daFormat: '%Y/%m/%d'
			});
			//]]></script>
		<?php

		echo "<br><br>Yo, ";
		if (isset($wnom))
			echo "<INPUT TYPE='text' NAME='wnom' size=60 maxlength=60 disabled onKeyUp='this.value = this.value.toUpperCase();' VALUE='" . $wnom . "')' ></INPUT>";
		else
			echo "<INPUT TYPE='text' NAME='wnom' size=60 maxlength=60 disabled onKeyUp='this.value = this.value.toUpperCase();'></INPUT>";

		echo "<br><br>Identificado con Cédula de Ciudadanía No. ";


		if (isset($wced))
			echo "<INPUT TYPE='text' NAME='wced' size=15 maxlength=15 disabled VALUE='" . $wced . "' onkeypress='return numeros(event);'></INPUT> <br>";


		else
			echo "<INPUT TYPE='text' NAME='wced' size=15 maxlength=15 disabled onkeypress='return numeros(event);'></INPUT> <br>";

		echo "<br>";

		echo "<tr><td align=LEFT bgcolor=#FCFCFC colspan=1><font text color=#003366 size=3>";

		echo "<br>";
		//echo "<font text size=4>Solicito depurar mis ingresos brutos con los siguientes conceptos que se tratan como ingresos no constitutivos de renta.</font> ";

		//-----------------------------
		/*


        echo "<br><br>1. Recibo ingresos por concepto de rentas de trabajo según la definición del Art. 103 de Estatuto Tributario, así:  se consideran ";
        echo "<br> &nbsp;&nbsp;&nbsp; rentas exclusivas de trabajo, las obtenidas por personas naturales por concepto de salarios, comisiones, prestaciones sociales,";
        echo "<br> &nbsp;&nbsp;&nbsp; viáticos, gastos de representación, honorarios, emolumentos eclesiásticos, compensaciones recibidas por el trabajo asociado ";
        echo "<br> &nbsp;&nbsp;&nbsp; cooperativo y, en general, las compensaciones por servicios personales.";
        echo "<br>";

        echo "<br>";
        */

		//------------------------------------

	/*	echo "<br><br><b><font text size=3> 1. Cotizaciones voluntarias al Régimen de Ahorro Individual con Solidaridad.</font></b>";

		echo "<br>";

		echo "<br>";


		// para que se sostenga la seleccion hecha despues de un submit entonces:

		if (isset($wnro1)) {
			if ($wnro1 == "1") {
				echo "<INPUT TYPE = 'Radio' NAME = 'wnro1' VALUE = 1 CHECKED>Si</INPUT>";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<INPUT TYPE = 'Radio' NAME = 'wnro1' VALUE = 2></INPUT>No";
			} else {
				echo "<INPUT TYPE = 'Radio' NAME = 'wnro1' VALUE = 1>Si</INPUT>";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<INPUT TYPE = 'Radio' NAME = 'wnro1' VALUE = 2 CHECKED>No</INPUT>";
			}
		} else {
			echo "<INPUT TYPE = 'Radio' NAME = 'wnro1' VALUE = 1>Si</INPUT>";
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "<INPUT TYPE = 'Radio' NAME = 'wnro1' VALUE = 2></INPUT>No";
		}

		echo "<br>";
		echo "<br>";


		echo "<b> <font text size=3> 2. Aportes voluntarios a fondos de pensiones obligatorias del Régimen de Ahorro Individual con Solidaridad.</font></b>";

		echo "<br>";
		echo "<br>";

		if (isset($wnro2)) {
			if ($wnro2 == "1") {
				echo "<INPUT TYPE = 'Radio' NAME = 'wnro2' VALUE = 1 CHECKED>Si</INPUT>";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<INPUT TYPE = 'Radio' NAME = 'wnro2' VALUE = 2></INPUT>No";
			} else {
				echo "<INPUT TYPE = 'Radio' NAME = 'wnro2' VALUE = 1>Si</INPUT>";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<INPUT TYPE = 'Radio' NAME = 'wnro2' VALUE = 2 CHECKED>No</INPUT>";
			}
		} else {
			echo "<INPUT TYPE = 'Radio' NAME = 'wnro2' VALUE = 1>Si</INPUT>";
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "<INPUT TYPE = 'Radio' NAME = 'wnro2' VALUE = 2>No</INPUT>";
		}

		echo "<br>";
		echo "<br>";*/

		//--------------------------------------------------------------------------------------------

		echo "<font text size=4><br>Como percibo <b>rentas de trabajo</b>, solicito depurar los ingresos netos, con las siguientes deducciones y aportes";
		echo "<br>que generan rentas exentas:</font>";
		echo "<br>";


//------, esta me la traje del anexo 02, pues tiene los campos que necesito

		echo "<br><br><b><font text size=3> 1.	Dependientes.</font></b>";
		echo "<br>";
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";


		echo "<br><font text size=3>Número de personas a mi cargo ";
		//-voy a copiar este pedacito de código en la pregunta 6
		if (isset($wdep))
			echo "<INPUT TYPE='text' NAME='wdep' align=center size=3 maxlength=2 VALUE='" . $wdep . "' onkeypress='return numeros(event);'></INPUT>";
		else
			echo "<INPUT TYPE='text' NAME='wdep' align=center size=3 maxlength=2 onkeypress='return numeros(event);'></INPUT>";
		//hasta aqui

		echo " (ver definición de dependientes en <a class='p' href='leame01.pdf' TARGET='_blank'><font face='serif' color=#003366><b>Informacion Adicional).</b></a> Adjuntar certificados.</font></b>";
		echo "<br>";

		//------------------

		echo "<br>";
		echo "<b> <font text size=3> 2. Certificado por salud prepagada - póliza de salud.</font></b>";
		echo "<br>";
		echo "<br>";
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

		// para que se sostenga la seleccion hecha despues de un submit entonces:

		if (isset($wnro6) and ($wnro6 != '')) {
			if ($wnro6 == "1") {
				echo "<INPUT TYPE = 'Radio' NAME = 'wnro6' VALUE = 1 CHECKED>Si</INPUT>";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<INPUT TYPE = 'Radio' NAME = 'wnro6' VALUE = 2></INPUT>No";
			} else {
				echo "<INPUT TYPE = 'Radio' NAME = 'wnro6' VALUE = 1>Si</INPUT>";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<INPUT TYPE = 'Radio' NAME = 'wnro6' VALUE = 2 CHECKED>No</INPUT>";
			}
		} else {
			echo "<INPUT TYPE = 'Radio' NAME = 'wnro6' VALUE = 1>Si</INPUT>";
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "<INPUT TYPE = 'Radio' NAME = 'wnro6' VALUE = 2></INPUT>No";
		}

		echo "<br>Adjuntar la certificación del pago realizado durante el " . (date("Y") - 1) . ", emitido por la respectiva entidad.<br>";

		echo "<br>";

		//_______________________________________________________________________________________________

		echo "<b><font text size=3> 3. Certificado por pagos de intereses sobre préstamos de vivienda de habitación, o costos financieros </b>";
		echo "<br><b>de leasing habitacional.</font></b>";
		echo "<br>";
		echo "<br>";
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

		if (isset($wnro7) and ($wnro7 != '')) {
			if ($wnro7 == "1") {
				echo "<INPUT TYPE = 'Radio' NAME = 'wnro7' VALUE = 1 CHECKED>Si</INPUT>";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<INPUT TYPE = 'Radio' NAME = 'wnro7' VALUE = 2></INPUT>No";
			} else {
				echo "<INPUT TYPE = 'Radio' NAME = 'wnro7' VALUE = 1>Si</INPUT>";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<INPUT TYPE = 'Radio' NAME = 'wnro7' VALUE = 2 CHECKED>No</INPUT>";
			}
		} else {
			echo "<INPUT TYPE = 'Radio' NAME = 'wnro7' VALUE = 1>Si</INPUT>";
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "<INPUT TYPE = 'Radio' NAME = 'wnro7' VALUE = 2>No</INPUT>";
		}
		echo "<br><font text size=3>Adjuntar la certificación del pago realizado durante el " . (date("Y") - 1) . ", emitido por la respectiva entidad bancaria.</font>";

		echo "<br>";

		//_________________________________________________________________________________________________


		echo "<br><b><font text size=3> 4. Ahorros voluntarios en cuentas AFC o AVC</b>";
		echo "<br>";
		echo "<br>";
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

		if (isset($wnro8) and ($wnro8 != '')) {
			if ($wnro8 == "1") {
				echo "<INPUT TYPE = 'Radio' NAME = 'wnro8' VALUE = 1 CHECKED>Si</INPUT>";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<INPUT TYPE = 'Radio' NAME = 'wnro8' VALUE = 2></INPUT>No";
			} else {
				echo "<INPUT TYPE = 'Radio' NAME = 'wnro8' VALUE = 1>Si</INPUT>";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<INPUT TYPE = 'Radio' NAME = 'wnro8' VALUE = 2 CHECKED>No</INPUT>";
			}
		} else {
			echo "<INPUT TYPE = 'Radio' NAME = 'wnro8' VALUE = 1>Si</INPUT>";
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "<INPUT TYPE = 'Radio' NAME = 'wnro8' VALUE = 2>No</INPUT>";
		}

		//echo "<br><b>Adjuntar la certificación del pago realizado durante el ".(date("Y") - 1).", emitido por la respectiva entidad bancaria.</b>";


		echo "<br>";

		//---------------------------------------------------

		echo "<br><b><font text size=3> 5. Aportes voluntarios a fondos de pensiones voluntarias.</b>";
		echo "<br>";
		echo "<br>";

		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

		if (isset($wnro9) and ($wnro9 != ''))// como esta parte es una copia aqui cambio el numero que ya sería $wnro9
		{

			if ($wnro9 == "1") {

				echo "<INPUT TYPE = 'Radio' NAME = 'wnro9' VALUE = 1 CHECKED>Si</INPUT>";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<INPUT TYPE = 'Radio' NAME = 'wnro9' VALUE = 2></INPUT>No";
			} else {
				echo "<INPUT TYPE = 'Radio' NAME = 'wnro9' VALUE = 1>Si</INPUT>";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<INPUT TYPE = 'Radio' NAME = 'wnro9' VALUE = 2 CHECKED>No</INPUT>";
			}
		} else {
			echo "<INPUT TYPE = 'Radio' NAME = 'wnro9' VALUE = 1>Si</INPUT>";
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "<INPUT TYPE = 'Radio' NAME = 'wnro9' VALUE = 2>No</INPUT>";
		}

		//echo "<br><b>Adjuntar la certificación del pago realizado durante el ".(date("Y") - 1).", emitido por la respectiva entidad bancaria.</b>";


		echo "<br>";

		//-------------------------------------------------------


		echo "<br><b><font text size=3> 6. Autorizo aplicar a mis pagos la retención en la fuente del";
		echo "&nbsp;&nbsp;";
		if (isset($wret))
			echo "<INPUT TYPE='text' NAME='wret' align=center size=3 maxlength=2 VALUE='" . $wret . "' onkeypress='return numeros(event);'></INPUT>";
		else
			echo "<INPUT TYPE='text' NAME='wret' align=center size=3 maxlength=2 onkeypress='return numeros(event);'></INPUT>";

		echo "&nbsp;";
		echo "% que es porcentaje superior al que generan mis pagos ";
		echo "<br>periódicos, de conformidad con lo permitido en el Parág. 3º del Art. 383 E.T. </b>";
		echo "<br> ";
		
		//--------------------------------------------------------------------------------------------
		echo "<br>";
		
		echo "<br><b><font text size=3> 7. Aportes Voluntarios al régimen de Ahorro individual con solidaridad.</b>";
		echo "<br>";
		echo "<br>";

		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

		if (isset($wnro10) and ($wnro10 != ''))// como esta parte es una copia aqui cambio el numero que ya sería $wnro10
		{

			if ($wnro10 == "1") {

				echo "<INPUT TYPE = 'Radio' NAME = 'wnro10' VALUE = 1 CHECKED>Si</INPUT>";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<INPUT TYPE = 'Radio' NAME = 'wnro10' VALUE = 2></INPUT>No";
			} else {
				echo "<INPUT TYPE = 'Radio' NAME = 'wnro10' VALUE = 1>Si</INPUT>";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<INPUT TYPE = 'Radio' NAME = 'wnro10' VALUE = 2 CHECKED>No</INPUT>";
			}
		} else {
			echo "<INPUT TYPE = 'Radio' NAME = 'wnro10' VALUE = 1>Si</INPUT>";
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "<INPUT TYPE = 'Radio' NAME = 'wnro10' VALUE = 2>No</INPUT>";
		}

//--------------------------------------------------------------------------------------------
		echo "<br>";
		
		echo "<br><b><font text size=3> 8. Certificado por pagos de intereses sobre préstamos de educación superior pagados al ICETEX</b>";
		echo "<br>";
		echo "<br>";

		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

		if (isset($wnro11) and ($wnro11 != ''))// como esta parte es una copia aqui cambio el numero que ya sería $wnro11
		{

			if ($wnro11 == "1") {

				echo "<INPUT TYPE = 'Radio' NAME = 'wnro11' VALUE = 1 CHECKED>Si</INPUT>";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<INPUT TYPE = 'Radio' NAME = 'wnro11' VALUE = 2></INPUT>No";
			} else {
				echo "<INPUT TYPE = 'Radio' NAME = 'wnro11' VALUE = 1>Si</INPUT>";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<INPUT TYPE = 'Radio' NAME = 'wnro11' VALUE = 2 CHECKED>No</INPUT>";
			}
		} else {
			echo "<INPUT TYPE = 'Radio' NAME = 'wnro11' VALUE = 1>Si</INPUT>";
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "<INPUT TYPE = 'Radio' NAME = 'wnro11' VALUE = 2>No</INPUT>";
		}
		//--------------------------------------------------------------------------------------------
		echo "<br>Si durante el presente año varían mis condiciones tributarias sobre todos o algunos de los aspectos contemplados ";
		echo "<br>anteriormente, enviaré una nueva comunicación avisando oportunamente el cambio sobre los mismos.";
		
		//echo "<br>Si por planeación tributaria, usted necesita que Promotora Medica Las Américas le practique una retención en la fuente superior a la ";
		//echo "<br>calculada con la aplicación de la Ley 1819 de 2016, por favor enviar comunicación con su solicitud al área de nómina.";

		echo "<tr><td align=center colspan=6 bgcolor=#FCFCFC>";
		echo "<br>";
		echo "<input type='submit' value='Enviar'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<input type=checkbox name=conf><B>Confirmar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";


		$anio = date("Y");    // Y=Devuelve 4 digitos (2015) y=devuelve 2 digitos(15)
		$query = "SELECT * FROM nomina_000019 Where anxano='" . $anio . "' And anxced=" . $wced;
		$resultado = mysql_query($query);
		$nroreg = mysql_num_rows($resultado);
		if ($nroreg > 0) {

			echo "<br>";


			$query = "Select ((perbas*100)/65)*perhco ACT,((perbaa*100)/65)*perhco ANT"
				. " From noper "
				. " Where peretr = 'A' "
				. " AND PERUNI = '02' "    //EN SALARIO FLEXIBLE
				. " AND percod = '" . $codigo . "'"
				. " UNION ALL "
				. " Select (perbas*perhco) ACT,(perbaa*perhco) ANT "
				. " From noper "
				. " Where peretr = 'A' "
				. " AND PERUNI <> '02' "   //EN SALARIO NORMAL
				. " AND percod = '" . $codigo . "'";

			$resultado = odbc_do($conexN, $query);                    // Ejecuto el query
			//Este proceso no lo quito porque pueden volver sea solicitar que muestre el formulario de acuerdo al salario 
			if (odbc_fetch_row($resultado)) {
				if (odbc_result($resultado, 2) > 0)   //Si el año anterior ganaba mas de 3000000 diligencia el Anexo2
				{
					//   echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class='p' href='anexo02.php?windicador=PrimeraVez'><font face='serif' color='blue'>:Diligenciar el anexo 2:</a></font>";
					if ($wemp_pmla == '01') //Promotora
						header('Location:https://www.sqlsoftware.nom.co:9443/AtgPMEDICA');
					if ($wemp_pmla == '05')	//Laboratorio
						header('Location:https://www.sqlsoftware.nom.co:9443/AtgLAMERICAS');
					if ($wemp_pmla == '04') //Patologia
						header('Location:https://www.sqlsoftware.nom.co:9443/AtgPAMERICAS');
					if ($wemp_pmla == '02') //Clinica del Sur
						header('Location:https://www.sqlsoftware.nom.co:9443/AtgCSAMERICAS');
					if ($wemp_pmla == '10')	//IDC
						header('Location:https://www.sqlsoftware.nom.co:9449/AtgIDC');
					//echo "<br>";
					//echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class='p' href='../reportes/boletincolilla.php?wemp_pmla=01'><font face='serif' color='blue'>:Ir a la colilla de pago:</a></font>";
				} else
					 if ($wemp_pmla == '01') //Promotora
							header('Location:https://www.sqlsoftware.nom.co:9443/AtgPMEDICA');
					 if ($wemp_pmla == '05')	//Laboratorio
							header('Location:https://www.sqlsoftware.nom.co:9443/AtgLAMERICAS');
					 if ($wemp_pmla == '04') //Patologia
							header('Location:https://www.sqlsoftware.nom.co:9443/AtgPAMERICAS');
					 if ($wemp_pmla == '02') //Clinica del Sur
							header('Location:https://www.sqlsoftware.nom.co:9443/AtgCSAMERICAS');
					 if ($wemp_pmla == '10')	//IDC
							header('Location:https://www.sqlsoftware.nom.co:9449/AtgIDC');
					//echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class='p' href='../reportes/boletincolilla.php?wemp_pmla=01'><font face='serif' color='blue'>::Ir a la colilla de pago:</a></font>";
				//echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class='p' href='http://mx.lasamericas.com.co/matrix/nomina/reportes/000001_rep3.php?wemp_pmla=01'><font face='serif' color='blue'>::Ir a la colilla de pago::</a></font>";
			}
		}
		
		echo "</td></tr>";

		if ($conf == "on") {
			//validar_datos($wciu, $wfec, $wnom, $wced, $wnro1, $wnro2, $wdep, $wnro6, $wnro7, $wnro8, $wnro9, $wret,$wnro10);
			validar_datos($wciu, $wfec, $wnom, $wced, $wdep, $wnro6, $wnro7, $wnro8, $wnro9, $wret,$wnro10,$wnro11);
			if (strlen($msgerr) > 5)    //Si retorna de la funcion con mensaje entonces lo muestro
				print "<script>alert('$msgerr')</script>";
			else {
				$anio = date("Y");    // Y=Devuelve 4 digitos (2015) y=devuelve 2 digitos(15)
				$query = "SELECT * FROM nomina_000019 Where anxano='" . $anio . "' And anxced=" . $wced;
				$resultado = mysql_query($query);
				$nroreg = mysql_num_rows($resultado);
				if ($nroreg == 0) {
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");

					//$query = "INSERT INTO nomina_000019 (Medico,Fecha_data,Hora_data,anxano,anxced,anxciu,anxfec,anxnom,anxre1,anxre2,anxdep,anxsal,anxviv,anxafc,anxent,anxvlr,anxavr,Seguridad,id) ";
					$query = "INSERT INTO nomina_000019 (Medico,Fecha_data,Hora_data,anxano,anxced,anxciu,anxfec,anxnom,anxdep,anxsal,anxviv,anxafc,anxent,anxvlr,anxavr,anxicetex,anxemp,Seguridad,id) ";
					$query = $query . " VALUES ('nomina','" . $fecha . "','" . $hora . "','" . $anio . "','" . trim($wced) . "','" . $wciu . "','" . $wfec . "','" . $wnom . "',";
					//$query = $query."'".$wnro1."','".$wnro2."','".$key."','')";
					$query = $query . "'" . $wdep . "','" . $wnro6 . "','" . $wnro7 . "','" . $wnro8 . "','" . $wnro9 . "','" . $wret . "','" . $wnro10 . "','" . $wnro11 . "','" . $wemp_pmla . "','" . $key . "','')";
					//echo "query de insercion a la base de datos :".$query;

					$resultado = mysql_query($query, $conex);
					if ($resultado)
						print "<script>alert('Su informacion ha sido enviada correctamente!!!!')</script>";
					else
						print "<script>alert('Atencion!!! Se produjo un error al enviar la informacion')</script>";

					print "<script>enter()</script>";    // Para que me haga un submit y refresque
				} else {
					// print "<script>alert('Atencion!!! Ud ya diligencio este Anexo')</script>";

					//$query = "UPDATE nomina_000019 SET anxre1='" . $wnro1 . "',anxre2='" . $wnro2 . "',anxdep='" . $wdep . "',anxsal='" . $wnro6 . "',anxviv='" . $wnro7 . "',anxafc='" . $wnro8 . "',anxent='" . $wnro9 . "',anxvlr='" . $wret . "',anxavr='" . $wnro10 . "'";
					$query = "UPDATE nomina_000019 SET anxdep='" . $wdep . "',anxsal='" . $wnro6 . "',anxviv='" . $wnro7 . "',anxafc='" . $wnro8 . "',anxent='" . $wnro9 . "',anxvlr='" . $wret . "',anxavr='" . $wnro10 . "',anxicetex='" . $wnro11 . "'";


					//$query = "UPDATE nomina_000019 SET anxre1='".$wnro1."',anxre2='".$wnro2."'";
					$query = $query . " WHERE anxano='" . $anio . "' And anxced=" . $wced;


					$resultado = mysql_query($query, $conex);
					if ($resultado)
						print "<script>alert('La informacion ha sido modificada correctamente!!!!')</script>";
					else
						print "<script>alert('Atencion!!! Se produjo un error al modificar la informacion')</script>";

				}
			}
		}

		echo "</table>";
		echo "</Form>";
	}
odbc_close($conexN);
odbc_close_all();
  
?>
</BODY>
</html>