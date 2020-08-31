<html>
<head>
  <title>REPORTE DE FACTURACION GENERAL</title>
<script type="text/javascript">

	 //Redirecciona a la pagina inicial
	 function inicioReporte(wfecini,wfecfin,wtip,aux1,wemp,wemp_pmla,bandera,west,hi,mi,si,hf,mf,sf,aux2){
	 	document.location.href='RepFacGen.php?wfecini='+wfecini+'&wfecfin='+wfecfin+'&wtip='+wtip+'&wccocod='+aux1+'&wccocon='+aux2+'&wemp='+wemp+'&wemp_pmla='+wemp_pmla+'&bandera='+bandera+'&west='+west+'&hi='+hi+'&mi='+mi+'&si='+si+'&hf='+hf+'&mf='+mf+'&sf='+sf;
	 }

	function onLoad() {
		loadMenus();
	}

	function Seleccionar()
	{
		var fecini = document.forma.wfecini.value;
		var fecfin = document.forma.wfecfin.value;

		//Valida que la fecha final sea mayor o igual a la incial
		if(!esFechaMenorIgual(fecini,fecfin))
		{
		   alert("La fecha inicial no puede ser mayor que la fecha final");
		   document.forma.wfecini.focus();
		   return false;
		}

		var hi = document.forma.hi.value;
		var hf = document.forma.hf.value;

		//Valida que la fecha final sea mayor o igual a la incial
		if(hi>hf)
		{
		   alert("La hora inicial no puede ser mayor que la hora final");
		   document.forma.hi.focus();
		   return false;
		}

		document.forma.submit();
	}
</script>

</head>
<?php
include_once("conex.php");
 /*************************************************************************************
   *     REPORTE DE FACTURACION POR CENTRO DE COSTO Y POR EMPRESA                    *
   *                               I P S                                             *
 *************************************************************************************/
//====================================================================================
//PROGRAMA: RepFacGen.php
//AUTOR: Gabriel Agudelo.
  $wautor="Gabriel Agudelo.";
//TIPO DE SCRIPT: principal
//RUTA DEL SCRIPT: matrix\ips\reportes\RepFacGen.php

//-------------------I------------------------I--------------------------------I
//	  FECHA          I     AUTOR              I   MODIFICACION
//-------------------I------------------------I--------------------------------I
//  2006-09-25       I Gabriel Agudelo        I creación del script.
//-------------------I------------------------I--------------------------------I

$wactualiz="2013-12-16";

/*DESCRIPCION:Este reporte presenta la lista de facturas por centro(s) de costo(s) y por empresa(s)

MODIFICACIONES:
2013-12-16 (Camilozz) Se modificó el script para que incluya las notas débito
2013-11-06 Se agrega un filtro para consultar por el centro de costos de los conceptos.
2011-11-03 Se incluyeron las columnas Notas crédito DEscuento y Valor facturado neto
2011-03-22 Se modificó el query para que tome también las horas en la consulta
2011-01-19 Se hacen cambios en la interfaz para mejor visualización del formulario y los resultsdos.
2007-04-13 Se modifico el query de consulta para que consultara por centro de costos.
		   Se limito la seleccion del centro de costos unicamente a los centros donde se generan facturas.
2007-02-28 Se agregaron los campos de hora para hacer cortes por dichos.

TABLAS QUE UTILIZA:
 $wbasedato."_000003: Maestro de centro de costos, select
 $wbasedato."_000018: encabezado de factura, select
 $wbasedato."_000024: maestro de empresas, select

 INCLUDES:
  conex.php = include para conexión mysql

 VARIABLES:
 $wbasedato= variable que permite el codigo multiempresa, se incializa desde invocación de programa
 $wini= lleva el estado del documento, si se esta abriendo por primera vez o no, se incializa desde invocación de programa con 'S'
 $senal= Indica el mensaje de alerta que se debe presentar segun los errores
 $wfecha=date("Y-m-d");
 $wfecini)= fecha inicial del reporte
 $wfecfin = fecha final del reporte
 $wccocod = centro de costos
 $wemp = empresa
 $wtip = variable que nos dice si es por codigo o nit
 $resultado =
 $bandera1= controla que sea la primera vez que entra en el ciclo para el codigo
 $bandera2= controla que sea la primera vez que entra en el ciclo para la empresa
 $j=1 sirve como variable de control para intercambiar colores
====================================================================================================*/

include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

//Validación de usuario
$usuarioValidado = true;
if (!isset($user) || !isset($_SESSION['user'])){
	$usuarioValidado = false;
}else {
	if (strpos($user, "-") > 0)
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}
if(empty($wuser) || $wuser == ""){
	$usuarioValidado = false;
}

session_start();

//Llamo a la función para formar el encabezado del reporte llevándole Título, Fecha e Imagen o logo
encabezado("REPORTE GENERAL DE FACTURACIÓN ",$wactualiz,"clinica");

//Si el usuario no es válido se informa y no se abre el reporte
if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
} // Fin IF si el usuario no es válido
else //Si el usuario es válido comenzamos con el reporte
{  //Inicio ELSE reporte

 //Conexion base de datos
 



 // Consulto los datos de la empresa actual y los asigno a la variable $empresa
 $consulta = consultarInstitucionPorCodigo($conex, $wemp_pmla);
 $empresa = $consulta->baseDeDatos;


  $q = " SELECT empdes "
      ."   FROM root_000050 "
      ."  WHERE empcod = '".$wemp_pmla."'"
      ."    AND empest = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $row = mysql_fetch_array($res);

  $wentidad=$row[0];

  //Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos
  $q = " SELECT detapl, detval, empdes "
      ."   FROM root_000050, root_000051 "
      ."  WHERE empcod = '".$wemp_pmla."'"
      ."    AND empest = 'on' "
      ."    AND empcod = detemp ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res);

 $institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

 $wbasedato = strtolower($institucion->baseDeDatos);
 $wentidad = $institucion->nombre;




  echo "<form name='forma' action='RepFacGen.php' method=post onSubmit='return valida_enviar(this);'>";
  $hora = (string)date("H:i:s");
  $wfecha = date("Y-m-d");

  echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='HIDDEN' NAME= 'form' value='forma'>";

if (!isset($form) or $form == '')
{
	//INGRESO DE VARIABLES PARA EL REPORTE//
	if (!isset ($bandera))
	{
		 $wfecini=$wfecha;
		 $wfecfin=$wfecha;
		 $wccocod="%-Todos los centros de costos";
		 $wccocon="%-Todos los centros de costos";
		 $wemp='% - Todas las empresas';
		 $wempcod="CODIGO";
		 $westado="on-Activo";
	}

	//Inicio tabla de ingreso de parametros
 	echo "<table align='center' border='2' bordercolor='ffffff'>";

 	//Petición de ingreso de parametros
 	echo "<tr>";
 	echo "<td height='37' colspan='5'>";
 	echo '<p align="left" class="titulo"><strong> &nbsp; Seleccione los datos a consultar &nbsp;  &nbsp; </strong></p>';
 	echo "</td></tr>";

 	//Solicitud fecha inicial de facturación
 	echo "<tr>";
 	echo "<td class='fila1' width=301 align='right'> &nbsp; Fecha inicial de facturación &nbsp; </td>";
 	echo "<td class='fila2' align='left' width=171>";
 	campoFechaDefecto("wfecini",$wfecini);
 	echo "</td>";
 	echo "<td class='fila1' width='371' align='right'> &nbsp; Hora inicial de facturación (hh:mm:ss) &nbsp; </td>";
 	echo "<td class='fila2' align='left' width='141'>";
    echo "<select name='hi'>";
	if(isset($hi) && $hi) {
	  echo "<option>".$hi."</option>";
	}
    for ($i=0;$i<24;$i++)
	   {
		  if ($i<10)
		   	echo "<option>0".$i."</option>";
		  else
		      echo "<option>".$i."</option>";
		}
    echo "</select>";
	echo "<select name='mi'>";
	if(isset($mi) && $mi) {
	  echo "<option>".$mi."</option>";
	}
	for ($i=0;$i<60;$i++)
	   {
		  if ($i<10)
		   	echo "<option>0".$i."</option>";
		   else
		      echo "<option>".$i."</option>";
		}
    echo "</select>";
	echo "<select name='si'>";
	if(isset($si) && $si) {
	  echo "<option>".$si."</option>";
	}
	for ($i=0;$i<60;$i++)
	   {
		  if ($i<10)
		   	echo "<option>0".$i."</option>";
		   else
		      echo "<option>".$i."</option>";
		}
    echo "</select>";
 	echo "</td></tr>";

 	//Solicitud fecha final de facturación
 	echo "<tr>";
 	echo "<td class='fila1' align=right> &nbsp; Fecha final de facturación &nbsp; </td>";
 	echo "<td class='fila2' align='left' width='141'>";
 	campoFechaDefecto("wfecfin",$wfecfin);
 	echo "</td>";
 	echo "<td class='fila1' align='right'> &nbsp; Hora final de facturación (hh:mm:ss) &nbsp; </td>";
 	echo "<td class='fila2' align='left' width='141'>";
	echo "<select name='hf'>";
	if(isset($hf) && $hf) {
	  echo "<option>".$hf."</option>";
	}
    for ($i=0;$i<24;$i++)
	   {
		  if ($i<10)
		   	echo "<option>0".$i."</option>";
		   else
		      echo "<option>".$i."</option>";
		}
    echo "</select>";
	echo "<select name='mf'>";
	if(isset($mf) && $mf) {
	  echo "<option>".$mf."</option>";
	}
	for ($i=0;$i<60;$i++)
	   {
		  if ($i<10)
		   	echo "<option>0".$i."</option>";
		   else
		      echo "<option>".$i."</option>";
		}
    echo "</select>";
	echo "<select name='sf'>";
	if(isset($sf) && $sf) {
	  echo "<option>".$sf."</option>";
	}
	for ($i=0;$i<60;$i++)
	   {
		  if ($i<10)
		   	echo "<option>0".$i."</option>";
		   else
		      echo "<option>".$i."</option>";
		}
    echo "</select>";
 	echo "</td></tr>";

	echo "<tr>";

	//SELECCIONAR tipo de reporte

	echo "<tr><td class=fila1 align=right> &nbsp; Parámetros del reporte &nbsp; </td>";
	echo "<td class=fila2>";
	echo "<select name='wtip'>";

	if (isset ($wtip))
	   {
      	if ($wtip=='CODIGO')
			{
				echo "<option>CODIGO</option>";
				echo "<option>NIT</option>";
			}
		if ($wtip=='NIT')
			{
				echo "<option>NIT</option>";
				echo "<option>CODIGO</option>";
			}
	   }
	else
		{
			echo "<option>CODIGO</option>";
			echo "<option>NIT</option>";
		}
	echo "</select> Empresa</td>";
	echo "<td class=fila1 align=right> &nbsp; Estado &nbsp; </td>";
	echo "<td class=fila2>";
	echo "<select name='west'>";
		$actsel = '';
		$anusel = '';
			if (isset($west) && $west=='on-Activo') $actsel = 'selected';
     		if (isset($west) && $west=='off-Anulado') $anusel = 'selected';
			echo "<option ".$actsel.">on-Activo</option>";
			echo "<option ".$anusel.">off-Anulado</option>";
	echo "</select></td></tr>";


	//SELECCIONAR CENTRO DE COSTOS
	if (isset($wccocod))
	   {
		echo "<td class=fila1 align=right> &nbsp; Centro de costos que generó factura &nbsp; </td>";
		echo "<td class=fila2 colspan=3>";
		echo "<select name='wccocod'>";
  		// este query se modifico limitandolo solo para los centros de costos donde se generan facturas (2007-04-13)
  		$q= "   SELECT ccocod, ccodes "
 	       ."     FROM ".$wbasedato."_000003, ".$wbasedato."_000040 "
 	       ."    WHERE Ccoffa=Carfue"
 	       ."      AND Carfac='on'"
 	       ."    ORDER by 1";
		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);

  		if ($num1 > 0 )
      	  {
      		echo "<option selected>".$wccocod."</option>";
      		if ($wccocod!='%-Todos los centros de costos')
			   echo "<option>%-Todos los centros de costos</option>";
		    for ($i=1;$i<=$num1;$i++)
	           {
	            $row1 = mysql_fetch_array($res1);
	            echo "<option>".$row1[0]." - ".$row1[1]." - ".$row1[2]."</option>";
	           }
          }
     	echo "</select></td></tr>";
	  }

	  echo "<tr><td class=fila1 align=right > &nbsp; Centro de costos del concepto</td>";
		echo "<td class=fila2 colspan=3>";
		echo "<select name='wccocon'>";
  		// este query se modifico limitandolo solo para los centros de costos donde se generan facturas (2007-04-13)
  		$q= "   SELECT ccocod, ccodes "
 	       ."     FROM ".$wbasedato."_000003"
		   ."    WHERE ccoest = 'on'"
 	       ."    ORDER by 1";
		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);

  		if ($num1 > 0 )
      	  {
      		echo "<option selected>".$wccocon."</option>";
      		if ($wccocon!='%-Todos los centros de costos')
			   echo "<option>%-Todos los centros de costos</option>";
		    for ($i=1;$i<=$num1;$i++)
	           {
	            $row1 = mysql_fetch_array($res1);
	            echo "<option>".$row1[0]." - ".$row1[1]."</option>";
	           }
          }
     	echo "</select></td></tr>";


	echo "<tr>";

	//SELECCIONAR EMPRESA
	echo "<td class=fila1 align=right> &nbsp; Responsable: &nbsp; </td>";
	echo "<td class=fila2 colspan=3>";
    echo "<select name='wemp'>";
		 $q= "   SELECT empcod, empnit, empnom "
	        ."     FROM ".$wbasedato."_000024 "
	        ."    WHERE empcod = empres "
	        ."    order by 2";
		 $res1 = mysql_query($q,$conex);
		 $num1 = mysql_num_rows($res1);

	     if ($num1 > 0 )
	        {
		     echo "<option selected>".$wemp."</option>";
		     if ($wemp!='% - Todas las empresas')
			    echo "<option>% - Todas las empresas</option>";

	   		 for ($i=1;$i<=$num1;$i++)
		       	{
			     $row1 = mysql_fetch_array($res1);
	  		     echo "<option>".$row1[0]." - ".$row1[1]." - ".$row1[2]."</option>";
	       		}
		    }
    echo "</select></td>";
	echo "</tr>";

	// Botones de seleccion de tipo de reporte, tambien sirven como submit para enviar los datos de consulta
	echo "<tr><td colspan=2 class=fila2 align=center> ";
	echo "<input type='radio' name='vol' value='SI' onclick='Seleccionar()' checked> &nbsp; Desplegar reporte detallado ";
	echo "</td><td colspan=2 class=fila2 align=center> ";
	echo "<input type='radio' name='vol' value='NO'  onclick='Seleccionar()' > &nbsp; Desplegar reporte resumido ";
    echo "</td></tr>";

	echo "<tr><td align=center class=fila1 colspan=4> * * * EL RANGO DE HORAS APLICA PARA CADA DIA ENTRE EL RANGO DE FECHAS ESPECIFICADO * * * </td></tr>";

	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
	echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
	echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";


	echo "</table></br>";

	echo "<p align='center'><input type='button' id='searchsubmit' value='Consultar' onclick='Seleccionar()'> &nbsp; | &nbsp; <input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></p>";

}

//MUESTRA DE DATOS DEL REPORTE
else
  {
  ////////////////////////////HORAS
  $hori=$hi.":".$mi.":".$si;
  $horf=$hf.":".$mf.":".$sf;


  //Inicio tabla de ingreso de parametros
    echo "<table border=0 cellspacing=2 cellpadding=0 align=center>";

  //Petición de ingreso de parametros
 	echo "<tr>";
		if ($vol=='SI')
			echo "<td height='37' colspan='2' class='titulo'><p align='left'><strong> &nbsp; Reporte general de facturación detallado &nbsp;  &nbsp; </strong></p></td>";
		else
			echo "<td height='37' colspan='2' class='titulo'><p align='left'><strong> &nbsp; Reporte general de facturación resumido &nbsp;  &nbsp; </strong></p></td>";
 	echo "</tr>";
 	echo "<tr>";
	echo "<td height='11'>&nbsp;</td>";
 	echo "</tr>";


	//Muestro los parámetros que se ingresaron en la consulta
    echo "<tr class='fila2'>";
    echo "<td align=left><strong> &nbsp; Fecha inicial facturación: </strong>&nbsp;".$wfecini." &nbsp; </td>";
    echo "<td align=left><strong> &nbsp; Fecha final facturación: </strong>&nbsp;".$wfecfin." &nbsp; </td>";
    echo "</tr>";
	if ($horf!="00:00:00")
	{
		echo "<tr class='fila2'>";
		echo "<td align=left><strong> &nbsp; Hora inicial: </strong>&nbsp;".$hori." &nbsp; </td>";
		echo "<td align=left><strong> &nbsp; Hora final: </strong>&nbsp;".$horf." &nbsp; </td>";
		echo "</tr>";
	}
	echo "<tr class='fila2'>";
    echo "<td align=left><strong> &nbsp; Estado: </strong>&nbsp;".$west." &nbsp; </td>";
    echo "<td align=left><strong> &nbsp; Clasificado por: </strong>&nbsp;".$wtip." &nbsp; </td>";
    echo "</tr>";
    echo "<tr class='fila2'>";
    echo "<td align=left colspan=2><strong> &nbsp; Centro de costos: </strong>&nbsp;".$wccocod." &nbsp; </td>";
    echo "</tr>";
	echo "<tr class='fila2'>";
    echo "<td align=left colspan=2><strong> &nbsp; Centro de costos concepto: </strong>&nbsp;".$wccocon." &nbsp; </td>";
    echo "</tr>";
    echo "<tr class='fila2'>";
    echo "<td align=left colspan=2><strong> &nbsp; Empresa: </strong>&nbsp;".$wemp." &nbsp; </td>";
    echo "</tr>";

 	echo "<tr>";
	echo "<td height='11'>&nbsp;</td>";
 	echo "</tr>";
    echo "</table>";

	$aux1=$wccocod;
	$aux2=$wccocon;

	// Botones de retornar y cerrar ventana
	echo "<p align='center'><input type='button' value='Retornar' onClick='javascript:inicioReporte(\"$wfecini\",\"$wfecfin\",\"$wtip\",\"$aux1\",\"$wemp\",\"$wemp_pmla\",\"$bandera\",\"$west\",\"$hi\",\"$mi\",\"$si\",\"$hf\",\"$mf\",\"$sf\",\"$aux2\");'>&nbsp;|&nbsp;<input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></p>";

    echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."'>";
    echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'>";
    echo "<input type='HIDDEN' NAME= 'wemp' value='".$wemp."'>";
 	echo "<input type='HIDDEN' NAME= 'wtip' value='".$wtip."'>";
    echo "<input type='HIDDEN' NAME= 'wccocod' value='".$wccocod."'>";
    echo "<input type='HIDDEN' NAME= 'wccocon' value='".$wccocon."'>";
    echo "<input type='HIDDEN' NAME= 'west' value='".$west."'>";
    echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
/***********************************Consulto lo pedido ********************/

// si la empresa es diferente a todas las empresas, la meto en el vector solo
// si es todas las empresas meto todas en un vector para luego preguntarlas en un for
$pr=explode('-', $west);
$west1[0]=trim ($pr[0]);
if ($wemp !='% - Todas las empresas')
	{

		$print=explode('-', $wemp);
		$wempcod[0]=trim ($print[0]);
		$wempnit[0]=trim ($print[1]);
		$empnom[0]=trim ($print[2]);
	//	$empresa[0]=$empCod[0]." - ".$empNit[0]." - ".$empNom[0];
		$num2=1;

	}
else
	{
		$wempcod='%';
		$wempnit='%';
		$num2=2;
	}

// si el centro de costos es diferente a todas los centros de costos, la meto en el vector solo
// si son todos los costos los empresas meto todas en un vector para luego preguntarlas en un for

if ($wccocod !='%-Todos los centros de costos')
	{
     $wcco=explode('-', $wccocod);
	 $wccostos[0]=trim ($wcco[0]);
	 $descrip[0]=trim ($wcco[1]);
	 $costos1[0]=$wccostos[0]." - ".$descrip[0];
	 $num1=1;
    }
   else
      {
	   $wccostos[0]='%';
	   $num1=2;
	  }
	if ($wccocon !='%-Todos los centros de costos')
	{
     $wccon=explode('-', $wccocon);
	 $wccostosn[0]=trim ($wccon[0]);
	 $descripn[0]=trim ($wccon[1]);
	// $costos1[0]=$wccostos[0]." - ".$descrip[0];
	// $num1=1;
    }
   else
      {
	   $wccostosn[0]='%';
	   //$num1=2;
	  }
// este query fue el que se modifico para que tomara tambien los centros de costos (2007-04-13)
	if ($horf=="00:00:00")
	{
		$q = " SELECT Ccocod, ccodes, empcod, empnom, empnit,fenffa, fenfac, (fenval+fenabo+fencmo+fencop+fendes) as valor, "
			." 		  fenfec, fensal, concat_ws('-',fenhis,fening), fendpa, fennpa, ".$wbasedato."_000018.Seguridad, fenval+fenabo+fencmo+fencop-fenvnc+fenvnd, fenvnc, fendes, fenvnd "
			."   FROM  ".$wbasedato."_000018,".$wbasedato."_000003,".$wbasedato."_000024 ,".$wbasedato."_000065 "
			." 	WHERE  fenfec between '".$wfecini."'"
			."    AND '".$wfecfin."'"
			."    AND fencco like '".$wccostos[0]."' "
		    ."    AND fencod like '".$wempcod[0]."' "
			."	  AND fdecco like '".$wccostosn[0]."' "
		    ."    AND fenest = '".$west1[0]."' "
		    ."    AND fencco=ccocod "
		    ."    AND fencod=empcod "
		    ."    AND fenffa=fdefue "
		    ."    AND fenfac=fdedoc "
		    ."  GROUP BY  ccocod, ccodes, empcod, empnom, empnit,fenffa, fenfac, valor, "
			." 		  fenfec, fensal, concat_ws('-',fenhis,fening), fendpa, fennpa, ".$wbasedato."_000018.Seguridad, fenval+fenabo+fencmo+fencop-fenvnc+fenvnd, fenvnc, fendes, fenvnd "
           // ."  HAVING (valor > 0)"
		    ."  ORDER BY ccocod,empcod,empnit,fenfac ";
		$err = mysql_query($q,$conex);

	}
	else
	{
		// este query fue el que se modifico para que tomara tambien los centros de costos (2007-04-13)
		$q = " SELECT ccocod, ccodes, empcod, empnom, empnit,fenffa, fenfac, ( fenval+fenabo+fencmo+fencop+fendes ) as valor, "
			." 		  fenfec, fensal, concat_ws('-',fenhis,fening), fendpa, fennpa, ".$wbasedato."_000018.Seguridad, fenval+fenabo+fencmo+fencop-fenvnc+fenvnd, fenvnc, fendes, fenvnd "
			."   FROM  ".$wbasedato."_000018,".$wbasedato."_000003,".$wbasedato."_000024 ,".$wbasedato."_000065 "
			." 	WHERE  fenfec between '".$wfecini."'"
			."    AND '".$wfecfin."'"
			." 	  AND  ".$wbasedato."_000018.Hora_data between '".$hori."'"
			."    AND '".$horf."'"
			."    AND fencco like '".$wccostos[0]."' "
		    ."    AND fencod like '".$wempcod[0]."' "
			."	  AND fdecco like '".$wccostosn[0]."' "
		    ."    AND fenest = '".$west1[0]."' "
		    ."    AND fencco=ccocod "
		    ."    AND fencod=empcod "
			."    AND fenffa=fdefue "
		    ."    AND fenfac=fdedoc "
			 ."  GROUP BY  ccocod, ccodes, empcod, empnom, empnit,fenffa, fenfac, fenval+fenabo+fencmo+fencop+fendes, "
			." 		  fenfec, fensal, concat_ws('-',fenhis,fening), fendpa, fennpa, ".$wbasedato."_000018.Seguridad, fenval+fenabo+fencmo+fencop-fenvnc+fenvnd, fenvnc, fendes, fenvnd"
           // ."  HAVING (valor > 0)"
		    ."  ORDER BY ccocod,empcod,empnit,fenfac ";
		$err = mysql_query($q,$conex);

	}
	//echo $q;
	$num = mysql_num_rows($err);
	//echo $q;
    $wtotal     = 0;
    $wtotcre    = 0;
    $wtotdes    = 0;
    $wtotsal    = 0;
    $wtotnet    = 0;
    $ccostos    =' ';
    $wemptot    = 0;
    $wempcre    = 0;
    $wempdes    = 0;
    $wempsal    = 0;
    $wempnet    = 0;
    $bandera1   =0;
    $bandera2   =0;
    $wtotfac    =0;
    $wcredito   =0;
    $wdebito    =0;
    $wdescuento =0;
    $wsaldo     =0;
    $wneto      =0;
    $wtotgenfac =0;
    $wtotgencre =0;
    $wtotgendes =0;
    $wtotgensal =0;
    $wtotgennet =0;
    $clase      ='fila1';
    $j          =1;
    $k          =1;
    $wcf6       =1;
    $i          =1;
    // Genero la tyabla que muestra el resultado de la consulta
	echo "<table border=0 cellspacing=2 cellpadding=0 align=center>";

	while ($i <= $num)
		{
			$row = mysql_fetch_array($err);
			if ($bandera1==0)
				{
					$wccocod=$row[0];
					$wccodes=$row[1];
					if ($vol!='SI')
					{
						echo "<tr class='encabezadoTabla'>";
						echo "<td align=left colspan=7><strong> &nbsp; Empresa </strong> &nbsp; </td>";
						echo "<td align=left><strong> &nbsp; Total facturado </strong> &nbsp; </td>";
						echo "<td align=left><strong> &nbsp; Total saldo </strong> &nbsp; </td>";
                        echo "<td align=left><strong> &nbsp; Notas crédito </strong> &nbsp; </td>";
						echo "<td align=left><strong> &nbsp; Notas D&eacute;bito </strong> &nbsp; </td>";
						echo "<td align=left><strong> &nbsp; Descuento </strong> &nbsp; </td>";
						echo "<td align=left><strong> &nbsp; Total fac. neto </strong> &nbsp; </td>";
						echo "</tr>";
					}
				}
			if ($bandera2==0)
			 	{
		  			$wempcod=$row[2];
		  			$wempnom=$row[3];
		  			$wempnit=$row[4];
		 		}
		 	if (($wempcod!=$row[2]) or ($wempcod=$row[2] and $wccocod!=$row[0]))
		 		{
			 		if ($num2==1 and $num1!=1)
					  {
						$wemptot=$wemptot + $wtotfac;
                        $wempcre=$wempcre + $wcredito;
						$wempdeb=$wempdeb + $wdebito;
						$wempdes=$wempdes + $wdescuento;
						$wempsal=$wempsal + $wsaldo;
						$wempnet=$wempnet + $wneto;
				  	  }
				  	else
				  	  {
					  	if ($vol=='SI')
					  		{
								echo "<tr class='encabezadoTabla'>";
								echo "<td align=left colspan=7><strong> &nbsp; Total empresa </strong> &nbsp; </td>";
								echo "<td align=right><strong>".number_format($wtotfac,0,'.',',')." </strong></td>";
								echo "<td align=right><strong>".number_format($wsaldo,0,'.',',')."</strong> &nbsp; </td>";
                                echo "<td align=right><strong>".number_format($wcredito,0,'.',',')." </strong></td>";
								echo "<td align=right><strong>".number_format($wdebito,0,'.',',')." </strong></td>";
								echo "<td align=right><strong>".number_format($wdescuento,0,'.',',')." </strong></td>";
								echo "<td align=right><strong>".number_format($wneto,0,'.',',')."</strong> &nbsp; </td>";
								echo "</tr>";
								echo "<tr><td align='left' colspan='13'>&nbsp;</td></tr>";
							}
				 		else
				 			{
					 			if (is_int ($j/2))
	   									$clase='fila1';
   								else
	   									$clase='fila2';
 								$j=$j+1;

								if ($wtip=='CODIGO')
		  							{
										echo "<td align=left class=$clase colspan='7'> ".$wempcod." - ".$wempnom."</td>";
									}
								if ($wtip=='NIT')
									{
										echo "<td align=left class=$clase colspan='7'> ".$wempnit." - ".$wempnom."</td>";
									}

				 				echo "<td align=right class=$clase>".number_format($wtotfac,0,'.',',')."</td>";
				 				echo "<td align=right class=$clase>".number_format($wsaldo,0,'.',',')."</td>";
                                echo "<td align=right class=$clase>".number_format($wcredito,0,'.',',')."</td>";
				 				echo "<td align=right class=$clase>".number_format($wdebito,0,'.',',')."</td>";
				 				echo "<td align=right class=$clase>".number_format($wdescuento,0,'.',',')."</td>";
				 				echo "<td align=right class=$clase>".number_format($wneto,0,'.',',')."</td></tr>";
			 				}

					}
		    			$wtotal = $wtotal+$wtotfac;
                        $wtotcre = $wtotcre+$wcredito;
						$wtotdeb = $wtotdeb+$wdebito;
						$wtotdes = $wtotdes+$wdescuento;
						$wtotsal = $wtotsal+$wsaldo;
						$wtotnet = $wtotnet+$wneto;

		    			$wtotfac=0;
                        $wcredito=0;
		    			$wdebito=0;
		    			$wdescuento=0;
		    			$wsaldo=0;
		    			$wneto=0;

		 		}
			if ($wccocod!=$row[0])
				{
					if (($num2==1 and $num1==2) and ($vol!="SI"))
						{
					 		if (is_int ($j/2))
	   							$clase='fila1';
  							else
  								$clase='fila2';
 							$j=$j+1;
							echo "<tr><td align=left class=$clase colspan='7'>".$wccocod." - ".$wccodes."</td>";
							echo "<td align=right class=$clase >".number_format($wtotal,0,'.',',')."</td>";
		        			echo "<td align=right class=$clase >".number_format($wtotsal,0,'.',',')."</td>";
                            echo "<td align=right class=$clase >".number_format($wtotcre,0,'.',',')."</td>";
		        			echo "<td align=right class=$clase >".number_format($wtotdeb,0,'.',',')."</td>";
		        			echo "<td align=right class=$clase >".number_format($wtotdes,0,'.',',')."</td>";
		        			echo "<td align=right class=$clase >".number_format($wtotnet,0,'.',',')."</td></tr>";
						}
					else
						{
							echo "<tr><td align='left' colspan='7'>&nbsp;</td></tr>";
							echo "<tr class='encabezadoTabla'  height='24'>";
							echo"<td align=left colspan='7'> &nbsp; TOTAL CENTRO DE COSTOS</td>";
							echo "<td align=right >".number_format($wtotal,0,'.',',')."</td>";
		        			echo "<td align=right >".number_format($wtotsal,0,'.',',')."</td>";
                            echo "<td align=right >".number_format($wtotcre,0,'.',',')."</td>";
		        			echo "<td align=right >".number_format($wtotdeb,0,'.',',')."</td>";
		        			echo "<td align=right >".number_format($wtotdes,0,'.',',')."</td>";
		        			echo "<td align=right >".number_format($wtotnet,0,'.',',')."</td></tr>";
						}

					$wtotal=0;
                    $wtotcre=0;
					$wtotdeb=0;
					$wtotdes=0;
					$wtotsal=0;
					$wtotnet=0;
				}
			if (($bandera1==0) or ($wccocod!=$row[0]))
				{
					$waux=$wccocod;
					$wccocod=$row[0];
					$wccodes=$row[1];
					$bandera1=1;
					$pinto=0;
					if (($num2==1 and $num1==2) and ($vol!="SI"))
						{
							$wcf6=1;
						}
					else
						{
							echo "<tr><td align='left' class='titulo' colspan='13'> &nbsp; Centro de Costos que factura ".$wccocod." - ".$wccodes."</td></tr>";
						}
				}

			if ($num2==1 and $num1==2)
				echo " ";
		 	else
		 	{
		 	if (($bandera2==0) or ($wempcod!=$row[2]) or ($wempcod=$row[2] and $waux!=$row[0]) )
		 		{
				 	$wempcod=$row[2];
		  			$wempnom=$row[3];
		  			$wempnit=$row[4];
		  			$bandera2=1;
		  			$pinto=0;
		  			$waux=$row[0];
		  		if ($vol=='SI')
		  			{
		  				if ($wtip=='CODIGO')
		  					{
								echo "<tr><td colspan=13 class='titulo'><b> ".$wempcod." - ".$wempnom."</b></td></tr>";
							}
						if ($wtip=='NIT')
							{
								echo "<tr><td colspan=13 class='titulo'><b> ".$wempnit." - ".$wempnom."</b></td></tr>";
							}
					}

	  			}
	  		}

							if ($vol=='SI')
								{
					 			if (is_int ($k/2))
	   								$clase='fila1';
   								else
	   								$clase='fila2';
  								$k=$k+1;

								if ($pinto==0)
			  							{
											echo "<tr class='encabezadoTabla'>";
								  			echo "<td align=CENTER>Fuente factura</td>";
						        			echo "<td align=CENTER>Nro factura</td>";
						        			echo "<td align=CENTER>Historia</td>";
						        			echo "<td align=CENTER>Identificación</td>";
						        			echo "<td align=CENTER>Nombre paciente</td>";
						        			echo "<td align=CENTER>Usuario Matrix</td>";
						        			echo "<td align=CENTER>Fecha factura</td>";
						        			echo "<td align=CENTER>Vlr factura</td>";
						        			echo "<td align=CENTER>Saldo factura</td>";
                                            echo "<td align=CENTER>Notas crédito</td>";
						        			echo "<td align=CENTER>Notas D&eacute;bito</td>";
						        			echo "<td align=CENTER>Descuento</td>";
						        			echo "<td align=CENTER>Valor fac. neto</td>";
											echo "</tr>";
											$pinto=1;
					   					}

										$seguridad = explode( "-", $row[13] );
										//echo $row[13]." - ".$seguridad[1]."<br>";
										$qu = "SELECT 	Descripcion
												FROM 	usuarios
												WHERE 	Codigo = '".$seguridad[1]."'";
										$resu = mysql_query( $qu ) or die( mysql_errno()." - Error en el query $qu -".mysql_error() );
										$responsable = mysql_fetch_array( $resu );
										echo '<tr>';
										echo "<td align=right class=".$clase.">".$row[5]."</td>";
										echo "<td align=right class=".$clase.">".$row[6]."</td>";
										echo "<td align=right class=".$clase.">".$row[10]."</td>";
										echo "<td align=right class=".$clase.">".$row[11]."</td>";
										echo "<td align=left class=".$clase.">".$row[12]."</td>";
										echo "<td align=left class=".$clase.">".$responsable['Descripcion']."</td>";
										echo "<td align=right class=".$clase.">".$row[8]."</td>";
										echo "<td align=right class=".$clase.">".number_format($row[7],0,'.',',')."</td>";
										echo "<td align=right class=".$clase.">".number_format($row[9],0,'.',',')."</td>";
                                        echo "<td align=right class=".$clase.">".number_format($row[15],0,'.',',')."</td>";
                                        echo "<td align=right class=".$clase.">".number_format($row['fenvnd'],0,'.',',')."</td>";
                                        echo "<td align=right class=".$clase.">".number_format($row[16],0,'.',',')."</td>";
										echo "<td align=right class=".$clase.">".number_format($row[14],0,'.',',')."</td>";
										echo '</tr>';
								}
				$wtotgenfac=$wtotgenfac + $row[7];
                $wtotgencre=$wtotgencre + $row[15];
				$wtotgendeb=$wtotgendeb + $row['fenvnd'];
				$wtotgendes=$wtotgendes + $row[16];
				$wtotgensal=$wtotgensal + $row[9];
				$wtotgennet=$wtotgennet + $row[14];

				$wtotfac = $wtotfac+$row[7];
                $wcredito = $wcredito+$row[15];
				$wdebito = $wdebito+$row['fenvnd'];
				$wdescuento = $wdescuento+$row[16];
				$wsaldo = $wsaldo+$row[9];
				$wneto = $wneto+$row[14];
				$i= $i + 1;
		}
	if ($wtotfac==0)
		{
			echo "<table align='center' border=0 bordercolor=#000080 width=570 style='border:solid;'>";
			echo "<tr><td colspan='2' align='center'><b>Sin ningún documento en el rango de fechas seleccionado</b></td><tr>";
		}
	else
		{
		if ($num2==1 and $num1!=1)
			{
				$wtotal = $wtotal+$wtotfac;
                $wtotcre = $wtotcre+$wcredito;
				$wtotdeb = $wtotdeb+$wdebito;
				$wtotdes = $wtotdes+$wdescuento;
				$wtotsal = $wtotsal+$wsaldo;
				$wtotnet = $wtotnet+$wneto;
				if ($vol!="SI")
					{
						if (is_int ($j/2))
								$clase='fila1';
   						else
	   							$clase='fila2';
   						$j=$j+1;

   						echo "<tr><td align=left class=$clase colspan='7'>".$wccocod." - ".$wccodes."</td>";
						echo "<td align=right class=$clase >".number_format($wtotal,0,'.',',')."</td>";
	        			echo "<td align=right class=$clase>".number_format($wtotsal,0,'.',',')."</td>";
                        echo "<td align=right class=$clase>".number_format($wtotcre,0,'.',',')."</td>";
	        			echo "<td align=right class=$clase>".number_format($wtotdeb,0,'.',',')."</td>";
	        			echo "<td align=right class=$clase>".number_format($wtotdes,0,'.',',')."</td>";
	        			echo "<td align=right class=$clase>".number_format($wtotnet,0,'.',',')."</td></tr>";
					}
				else
					{

						echo "<tr><td align='left' colspan='7'>&nbsp;</td></tr>";
						echo "<tr height='24'><td align=left class='encabezadoTabla' colspan='7'> &nbsp; TOTAL CENTRO DE COSTOS </td>";
				    	echo "<td align=right class='encabezadoTabla'>".number_format($wtotal,0,'.',',')."</td>";
				    	echo "<td align=right class='encabezadoTabla'>".number_format($wtotsal,0,'.',',')."</td>";
                        echo "<td align=right class='encabezadoTabla'>".number_format($wtotcre,0,'.',',')."</td>";
				    	echo "<td align=right class='encabezadoTabla'>".number_format($wtotdeb,0,'.',',')."</td>";
				    	echo "<td align=right class='encabezadoTabla'>".number_format($wtotdes,0,'.',',')."</td>";
				    	echo "<td align=right class='encabezadoTabla'>".number_format($wtotnet,0,'.',',')."</td></tr>";
			    	}
				$wemptot=$wemptot + $wtotfac;
                $wempcre=$wempcre + $wcredito;
				$wempdeb=$wempdeb + $wdebito;
				$wempdes=$wempdes + $wdescuento;
				$wempsal=$wempsal + $wsaldo;
				$wempnet=$wempnet + $wneto;
				echo "<tr><td align='left' colspan='7'>&nbsp;</td></tr>";
				echo "<tr height='27'><td align=left class='encabezadoTabla' colspan='7'> &nbsp; TOTAL GENERAL EMPRESA</td>";
				echo "<td align=right class='encabezadoTabla'>".number_format($wemptot,0,'.',',')."</td>";
				echo "<td align=right class='encabezadoTabla'>".number_format($wempsal,0,'.',',')."</td>";
                echo "<td align=right class='encabezadoTabla'>".number_format($wempcre,0,'.',',')."</td>";
				echo "<td align=right class='encabezadoTabla'>".number_format($wempdeb,0,'.',',')."</td>";
				echo "<td align=right class='encabezadoTabla'>".number_format($wempdes,0,'.',',')."</td>";
				echo "<td align=right class='encabezadoTabla'>".number_format($wempnet,0,'.',',')."</td></tr>";

         	}
         else
         	{
	         	$wtotal = $wtotal+$wtotfac;
                $wtotcre = $wtotcre+$wcredito;
				$wtotdeb = $wtotdeb+$wdebito;
				$wtotdes = $wtotdes+$wdescuento;
				$wtotsal = $wtotsal+$wsaldo;
				$wtotnet = $wtotnet+$wneto;
				if ($vol=='SI')
					{
						echo "<td align=left class='encabezadoTabla' colspan='7'> &nbsp; TOTAL EMPRESA</td>";
					 	echo "<td align=right class='encabezadoTabla'>".number_format($wtotfac,0,'.',',')."</td>";
					 	echo "<td align=right class='encabezadoTabla'>".number_format($wsaldo,0,'.',',')."</td>";
                        echo "<td align=right class='encabezadoTabla'>".number_format($wcredito,0,'.',',')."</td>";
					 	echo "<td align=right class='encabezadoTabla'>".number_format($wdebito,0,'.',',')."</td>";
					 	echo "<td align=right class='encabezadoTabla'>".number_format($wdescuento,0,'.',',')."</td>";
					 	echo "<td align=right class='encabezadoTabla'>".number_format($wneto,0,'.',',')."</td></tr>";
			 		}
				 		else
				 			{
					 			if (is_int ($j/2))
   									$clase='fila1';
   								else
   									$clase='fila2';
  								$j=$j+1;
   								if ($wtip=='CODIGO')
		  							{
										echo "<td align=left class=$clase colspan='7'>".$wempcod." - ".$wempnom."</td>";
									}
								if ($wtip=='NIT')
									{
										echo "<td align=left class=$clase colspan='7'>".$wempnit." - ".$wempnom."</td>";
									}
				 				echo "<td align=right class=$clase>".number_format($wtotfac,0,'.',',')."</td>";
				 				echo "<td align=right class=$clase>".number_format($wsaldo,0,'.',',')."</td>";
                                echo "<td align=right class=$clase>".number_format($wcredito,0,'.',',')."</td>";
				 				echo "<td align=right class=$clase>".number_format($wdebito,0,'.',',')."</td>";
				 				echo "<td align=right class=$clase>".number_format($wdescuento,0,'.',',')."</td>";
				 				echo "<td align=right class=$clase>".number_format($wneto,0,'.',',')."</td></tr>";
			 				}

			 				if ($vol=='SI')
							{
								echo "<tr><td align='left' colspan='7'>&nbsp;</td></tr>";
								echo "<tr height='24'><td align=left class='encabezadoTabla' colspan='7'> &nbsp; TOTAL CENTRO DE COSTOS </td>";
						    	echo "<td align=right class='encabezadoTabla'>".number_format($wtotal,0,'.',',')."</td>";
								echo "<td align=right class='encabezadoTabla'>".number_format($wtotsal,0,'.',',')."</td>";
                                echo "<td align=right class='encabezadoTabla'>".number_format($wtotcre,0,'.',',')."</td>";
								echo "<td align=right class='encabezadoTabla'>".number_format($wtotdeb,0,'.',',')."</td>";
								echo "<td align=right class='encabezadoTabla'>".number_format($wtotdes,0,'.',',')."</td>";
								echo "<td align=right class='encabezadoTabla'>".number_format($wtotnet,0,'.',',')."</td></tr>";
							}
							else
							{
								echo "<tr><td align='left' colspan='7'>&nbsp;</td></tr>";
								echo "<tr height='24'><td align=left class='encabezadoTabla' colspan='7'> &nbsp; TOTAL CENTRO DE COSTOS </td>";
						    	echo "<td align=right class='encabezadoTabla'>".number_format($wtotal,0,'.',',')."</td>";
								echo "<td align=right class='encabezadoTabla'>".number_format($wtotsal,0,'.',',')."</td>";
                                echo "<td align=right class='encabezadoTabla'>".number_format($wtotcre,0,'.',',')."</td>";
								echo "<td align=right class='encabezadoTabla'>".number_format($wtotdeb,0,'.',',')."</td>";
								echo "<td align=right class='encabezadoTabla'>".number_format($wtotdes,0,'.',',')."</td>";
								echo "<td align=right class='encabezadoTabla'>".number_format($wtotnet,0,'.',',')."</td></tr>";
							}



			}

		}

		if ($num1==2 and $num2==2 and $wtotgenfac != 0)
			{
				if ($vol=='SI')
				{
					echo "<tr><td align='left' colspan='7'>&nbsp;</td></tr>";
					echo "<tr  height='27'><td align=left class='encabezadoTabla' colspan='7'> &nbsp; TOTAL GENERAL</td>";
					echo "<td align=right class='encabezadoTabla'>".number_format($wtotgenfac,0,'.',',')."</td>";
					echo "<td align=right class='encabezadoTabla'>".number_format($wtotgensal,0,'.',',')."</td>";
                    echo "<td align=right class='encabezadoTabla'>".number_format($wtotgencre,0,'.',',')."</td>";
					echo "<td align=right class='encabezadoTabla'>".number_format($wtotgendeb,0,'.',',')."</td>";
					echo "<td align=right class='encabezadoTabla'>".number_format($wtotgendes,0,'.',',')."</td>";
					echo "<td align=right class='encabezadoTabla'>".number_format($wtotgennet,0,'.',',')."</td></tr>";
				}
				else
				{
					echo "<tr><td align='left' colspan='7' height='27'>&nbsp;</td></tr>";
					echo "<td align='left' class='encabezadoTabla' colspan='7'> &nbsp; TOTAL GENERAL</td>";
					echo "<td align=right class='encabezadoTabla'>".number_format($wtotgenfac,0,'.',',')."</td>";
					echo "<td align=right class='encabezadoTabla'>".number_format($wtotgensal,0,'.',',')."</td>";
                    echo "<td align=right class='encabezadoTabla'>".number_format($wtotgencre,0,'.',',')."</td>";
					echo "<td align=right class='encabezadoTabla'>".number_format($wtotgendeb,0,'.',',')."</td>";
					echo "<td align=right class='encabezadoTabla'>".number_format($wtotgendes,0,'.',',')."</td>";
					echo "<td align=right class='encabezadoTabla'>".number_format($wtotgennet,0,'.',',')."</td></tr>";
				}

			}
    echo "</table>";
	$bandera=1;

	// Botones de retornar y cerrar ventana
	echo "<br /><p align='center'><input type='button' value='Retornar' onClick='javascript:inicioReporte(\"$wfecini\",\"$wfecfin\",\"$wtip\",\"$aux1\",\"$wemp\",\"$wemp_pmla\",\"$bandera\",\"$west\",\"$hi\",\"$mi\",\"$si\",\"$hf\",\"$mf\",\"$sf\",\"$aux2\");'>&nbsp;|&nbsp;<input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></p>";

	}
}
?>
</body>
</html>
