<html>
<head>
<title>Visitas pacientes nacionales e internacionales</title>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
<script>
        $.datepicker.regional['esp'] = {
            closeText: 'Cerrar',
            prevText: 'Antes',
            nextText: 'Despues',
            monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
            'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
            monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
            'Jul','Ago','Sep','Oct','Nov','Dic'],
            dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
            dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
            dayNamesMin: ['D','L','M','M','J','V','S'],
            weekHeader: 'Sem.',
            dateFormat: 'yy-mm-dd',
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['esp']);
        $(document).ready(function() {
			$("#fecha, #fecha1").datepicker({
		       showOn: "button",
		       buttonImage: "../../images/medical/root/calendar.gif",
		       buttonImageOnly: true,
		       maxDate:"+1D"
		    });
		});
</script>

<script language="javascript" >
function guardar()
{
	document.batch.action='visitas.php';
	document.batch.submit();
}
</script>

</head>
<body >

<?php
include_once("conex.php");

/******************************************** 	VISITAS NACIONALES E INTERNACIONALES   ************************************
 * 
 * Este es un reporte que muestra las visitas internacionales y nacionales entre dos fechas y donde se encontraban, fecha de ingreso, egreso etc
 * ademas tiene enlace para revisar y gestionar los datos del paciente a persona.php. se puede consultar los pacientes en el momento.
 * 
 * @name  matrix\magenta\procesos\internacionales.php
 * @author Carolina Castaño Portilla.
 * 
 * @created 2007-01-22
 * @version 2006-01-22
 * 
 * @modified  
 * 
 * @table inpac, select
 * @table inpacotr, select 
 * @table inser, select, 
 * @table india, select,
 * @table famov, select
 * @table inmun, select
 * @table inmtra, select
 * @table aymov, select
 * @table aymovotr, select
 * @table inmegr, select
 * @table inpaci, select
 * 
 *  @wvar $am indica que la estadia en hospitalizacion si esta en 1, se calcula por cada paciente
 *  @wvar $amb indica que la estadia en hospitalizacion si esta en 1, es un vector que tiene la info de cada paciente
 *  @wvar $ape1 apellido del paciente
 *  @wvar $ape2 apellido 2 del paciente
 *  @wvar $array, vector que guarda toda la info del paciente internacional 
 *  @wvar $array, vector que guarda toda la info del paciente nacional 
 *  @wvar $bandera, indica si es la primera vez que se entra al programa o no, 
 *  @wvar $ced, cedula del paciente 
 *  @wvar $cedTip tipo de cedula del paciente
 *  @wvar $codRes, reponsable de la facturacion del ingreso
 *  @wvar $color colores de depliegue de datos
 *  @wvar $diaNom diagnostico de ingreso
 *  @wvar $doc documento del paciente
 *  @wvar $fec fecha inicial de la busqueda de pacientes en el reporte en formato unix
 *  @wvar $fec1 fecha final de la busqueda de pacientes en el reporte en formato unix
 *  @wvar $fecEgr fecha de egreso del paciente
 *  @wvar $fecha fecha inicial de la busqueda de pacientes en el reporte en formato matrix
 *  @wvar $fecha1 fecha final de la busqueda de pacientes en el reporte en formato matrix
 *  @wvar $fecIng fecha de ingreso del paciente
 *  @wvar $fecNac fecha de nacimiento
 *  @wvar $fuente de donde se saco la info del paciente (aymov, inpac o impaci)
 *  @wvar $his historia clinica del paciente
 *  @wvar $horEgr hora de egreso
 *  @wvar $horIng hora de ingreso
 *  @wvar $hoy fecha de hoy en formato unix
 *  @wvar $ingresos cantidad de ingresos encontrados entre las dos fechas
 *  @wvar $m, desde que numero empieza el vector de pacientes
 *  @wvar $nom nombre del pacinete
 *  @wvar $numIng numero de ingreso del paciente
 *  @wvar $pacHos indica si esta hospitalizado (H) o no (C o A)
 *  @wvar $serAct, servicio alctual donde se encuentra el pacinete, o ultimo del que salio 
 *  @wvar $serNom nombre del servicio de ingreso
 *  @wvar $sex sexo del paciente
 *  @wvar $size1 tamaño del vector de la info del paciente
 *  @wvar $suma, permite identificar si el paciente realmente se encuentra aun en la clinica, si esta en algun servicio sin egresar
 *  @wvar $tipDoc, tipo de documento del paciente
*/

/************************************************************************************************************************* 
	Modificaciones
	2016-05-06: Arleyda Insignares C.
               -Se Modifican los campos de calendario fecha inicial y fecha final con utilidad jquery y se elimina
				uso de Zapatec Calendar por errores en la clase.
			   -Se cambia encabezado, titulos y tablas con ultimo formato.
	2013-03-23: Se agregan unions a las consultas de unix debido a que en alguno momento trae vacios. Viviana Rodas

*************************************************************************************************************************/

$wautor="Carolina Castano P.";
$wversion='2007-01-22';
$wactualiz='2016-05-06';

/*************************************************     PROGRAMA         ************************************************/
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
    include_once("root/comun.php");
	/////////////////////////////////////////////////encabezado general///////////////////////////////////
    $titulo = "SISTEMA DE COMENTARIOS Y SUGERENCIAS";

    // Se muestra el encabezado del programa
    encabezado($titulo,$wactualiz, "clinica");  
	/**
	 * conexion con matrix
	 */
	

	

	$bd='facturacion';
	/**
	 * conexion con unix
	 */
	include_once("magenta/socket2.php");
	//entro a programa cuando la conexión se realizó
	if($conex_o != false)
	{
		/**
		 * fnciones citadas en este programa como las de ordenar vectores
		 */
		include_once("magenta/incVisitas.php");
		$hoy=date('Y/m/d');

		// echo "<table align='right'>" ;
		// echo "<tr>" ;
		// echo "<td><font color=\"#D02090\" size='2'>Autor: ".$wautor."</font></td>";
		// echo "</tr>" ;
		// echo "<tr>" ;
		// echo "<td><font color=\"#D02090\" size='2'>Version: ".$wversion."</font></td>" ;
		// echo "</tr>" ;
		// echo "</table></br></br></br>" ;		
		
		//////////////////////////////////////////////////////////PARTE INICIAL DE ENTRADA DE PARAMETROS DEL REPORTE////////////////////////////

		ECHO "</br><table border=0 align=center size=100%>";
		ECHO "<tr class='fila1'><td align=center ><b><font size=5 >CONSULTA DE VISITAS NACIONALES E INTERNACIONALES </font></a></b></td></tr></br>";
		ECHO "</table></br></br>";

		if (!isset($bandera))
		{
			$fecha= date ('Y-m-d');
			$fecha1=  date ('Y-m-d');
			echo "<center><font color='#00008B'>INGRESE POR FAVOR EL RANGO DE FECHAS EN EL CUAL DESEA CONSULTAR LAS VISITAS:</font></center></BR>";

			// Busqueda de comentario entre dos fechas
			echo "<fieldset style='border:solid;border-color:#00008B; width=700' align=center></br>";
			echo "<form NAME='ingreso' ACTION='' METHOD='POST'>";
			echo "<table align='center'>";
			echo "<tr>";
			//$cal="calendario('fecha','1')";
			echo "<td align=center bgcolor=#336699 ><font size=3  face='arial' color='#ffffff'>FECHA INICIAL:</font><input type='text' readonly='readonly' id='fecha' name='fecha' value='".$fecha."' class=tipo3 ></td>";
			echo "<td align=center bgcolor=#336699><font size=3  face='arial' color='#ffffff'>FECHA FINAL:</font><input type='text' readonly='readonly' id='fecha1' name='fecha1' value='".$fecha1."' class=tipo3 ></td>";
			echo "</td>";
			echo "</tr></TABLE></br>";
			echo "<TABLE align=center><tr>";
			echo "<tr>";
			echo "<input type='hidden' name='bandera' value='1' />";
			echo "<td align=center colspan=14><font size='2'  align=center face='arial'><input type='submit' name='aceptar' value='BUSCAR' ></td></tr>";
			echo "</TABLE>";
			echo "</td>";
			echo "</tr>	";
			echo "</form>";
			echo "</fieldset>";
		}
else
{
	/////////////////////////////////////////////busqueda de datos de pacientes internacionales//////

	$i=0;

	$fec=str_replace ( '-',  '/', $fecha );
	$fec1=str_replace ( '-',  '/', $fecha1 );

	//Busco inicialmente en inpac quienes han venido hoy que sea paciente internacional, en caso de que la fecha final del reporte sea hoy
	
	if ($fecha1>=date('Y-m-d'))
	{

		// $query="select A.pachis, A.pacnum, A.pachor, A.pachos, A.pacced, A.pactid, A.pacser, A.pacnom, A.pacap1, A.pacap2, A.pacfec, A.pacnac, A.pacsex, A.paccer, B.pacotrbar, A.pacdin, C.sernom, E.dianom,  G.munnom  ";
		// $query= $query."from inpac A , Outer inpacotr B, Outer inser C, Outer india E,  inmun G ";
		// $query= $query."where B.pacotrhis=A.pachis and B.pacotrnum=A.pacnum ";
		// $query= $query."and C.sercod=A.pacser ";
		// $query= $query."and E.diacod=A.pacdin  ";
		// $query= $query." and G.muncod=A.pacmun and G.mundep='01' ";
		$query="select A.pachis, A.pacnum, A.pachor, A.pachos, A.pacced, A.pactid, A.pacser, A.pacnom, A.pacap1, A.pacap2, A.pacfec, A.pacnac, A.pacsex, A.paccer, B.pacotrbar, A.pacdin, C.sernom, E.dianom,  G.munnom  ";
		$query= $query."from inpac A , Outer inpacotr B, Outer inser C, Outer india E,  inmun G ";
		$query= $query."where B.pacotrhis=A.pachis and B.pacotrnum=A.pacnum ";
		$query= $query."and C.sercod=A.pacser ";
		$query= $query."and E.diacod=A.pacdin  ";
		$query= $query." and G.muncod=A.pacmun and G.mundep='01' ";
		$query= $query."and A.pacap2 is not null ";
		$query= $query." union ";
		$query= $query."select A.pachis, A.pacnum, A.pachor, A.pachos, A.pacced, A.pactid, A.pacser, A.pacnom, A.pacap1, '.' as pacap2, A.pacfec, A.pacnac, A.pacsex, A.paccer, B.pacotrbar, A.pacdin, C.sernom, E.dianom,  G.munnom  ";
		$query= $query."from inpac A , Outer inpacotr B, Outer inser C, Outer india E,  inmun G ";
		$query= $query."where B.pacotrhis=A.pachis and B.pacotrnum=A.pacnum ";
		$query= $query."and C.sercod=A.pacser ";
		$query= $query."and E.diacod=A.pacdin  ";
		$query= $query." and G.muncod=A.pacmun and G.mundep='01' ";
		$query= $query."and A.pacap2 is null ";

		$err_o = odbc_exec($conex_o,$query);

		while (odbc_fetch_row ($err_o))
		{
			switch (odbc_result($err_o,4))
			{
				case 'H':
				$am=1;
				break;
				case 'C':
				$am=0;
				break;
				case 'A':
				$am=0;
				break;
			}

			//verifico que realmente esten en un servicio de la clinicia y no que no los hayan sacado aun de inpac
			$query="select A.traser, A.trahab   ";
			$query= $query."from inmtra A  ";
			$query= $query."where A.trahis='".odbc_result($err_o,1)."' and A.tranum='".odbc_result($err_o,2)."' and A.traegr is null ";

			$err_1 = odbc_exec($conex_o,$query);
			$suma=0;

			while (odbc_fetch_row ($err_1))
			{
				$suma++;
			}

			//ademas si estan en inpac y es ambulatorio y tienen fecha que no es la de hoy, es porque no estan en la clinica sino que no los han movido de inpac
			if (($am==0 and odbc_result($err_o,11)==date('Y/m/d')) or ($am==1 and $suma>0) or $fecha!=$fecha1)
			{
				$his [$i]= odbc_result($err_o,1);
				$numIng [$i]= odbc_result($err_o,2);
				$ced [$i]= odbc_result($err_o,5);
				$cedTip [$i]= odbc_result($err_o,6);
				$nom [$i]= odbc_result($err_o,8);
				$ape1 [$i]= odbc_result($err_o,9);
				$ape2 [$i]= odbc_result($err_o,10);
				$fecIng [$i]= odbc_result($err_o,11);
				$fecNac [$i]= odbc_result($err_o,12);
				$sex[$i]=odbc_result($err_o,13);
				$codRes [$i]= odbc_result($err_o,14);
				$bar [$i]= odbc_result($err_o,15);
				$serNom [$i]= odbc_result($err_o,17);
				$diaNom [$i]= odbc_result($err_o,18);
				$horIng [$i]= odbc_result($err_o,3);
				$pacHos [$i]= odbc_result($err_o,4);
				$mun[$i]= odbc_result($err_o,19);
				$fuente[$i]='inpac';

				switch ($pacHos[$i])
				{
					case 'H':
					$amb[$i]=1;
					$serAct [$i]='';
					break;
					case 'C':
					$amb[$i]=0;
					$serAct [$i]= odbc_result($err_o,17);
					break;
					case 'A':
					$amb[$i]=0;
					$serAct [$i]= odbc_result($err_o,17);
					break;
				}


				$i++;
			}
		}
	}
	
	//búsco en aymov
	// $query="select A.movdoc, A.movfue, A.movhor, A.movnum, A.movced, A.movtid, A.movsin, A.movnom, A.movape, A.movap2, A.movfec, A.movnac, A.movsex, A.movcer, B.movotrbar, A.movdia, C.sernom, E.dianom, G.munnom   ";
	// $query= $query."from aymov A , aymovotr B, Outer inser C, Outer india E, inmun G ";
	// $query= $query."where A.movfec>= '$fec' and A.movfec<= '$fec1'  ";
	// $query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue and movtip <> 'I' and movfue <> 'PO' ";
	// $query= $query."and C.sercod=A.movsin ";
	// $query= $query."and E.diacod=A.movdia and A.movmun=G.muncod and G.mundep='01' order by A.movhor  ";
	
	$query="select A.movdoc, A.movfue, A.movhor, A.movnum, A.movced, A.movtid, A.movsin, A.movnom, A.movape, A.movap2, A.movfec, A.movnac, A.movsex, A.movcer, B.movotrbar, A.movdia, C.sernom, E.dianom, G.munnom   ";
	$query= $query."from aymov A , aymovotr B, Outer inser C, Outer india E, inmun G ";
	$query= $query."where A.movfec>= '$fec' and A.movfec<= '$fec1'  ";
	$query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue and movtip <> 'I' and movfue <> 'PO' ";
	$query= $query."and C.sercod=A.movsin ";
	$query= $query."and A.movnum is not null ";
	$query= $query."and A.movap2 is not null ";
	$query= $query."and E.diacod=A.movdia and A.movmun=G.muncod and G.mundep='01' ";
	$query= $query." union ";
	$query= $query."select A.movdoc, A.movfue, A.movhor, 0 as movnum, A.movced, A.movtid, A.movsin, A.movnom, A.movape, '.' as movap2, A.movfec, A.movnac, A.movsex, A.movcer, B.movotrbar, A.movdia, C.sernom, E.dianom, G.munnom   ";
	$query= $query."from aymov A , aymovotr B, Outer inser C, Outer india E, inmun G ";
	$query= $query."where A.movfec>= '$fec' and A.movfec<= '$fec1'  ";
	$query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue and movtip <> 'I' and movfue <> 'PO' ";
	$query= $query."and C.sercod=A.movsin ";
	$query= $query."and A.movnum is null ";
	$query= $query."and A.movap2 is null ";
	$query= $query."and E.diacod=A.movdia and A.movmun=G.muncod and G.mundep='01' ";
	$query= $query." union ";
	$query= $query."select A.movdoc, A.movfue, A.movhor, movnum, A.movced, A.movtid, A.movsin, A.movnom, A.movape, '.' as movap2, A.movfec, A.movnac, A.movsex, A.movcer, B.movotrbar, A.movdia, C.sernom, E.dianom, G.munnom   ";
	$query= $query."from aymov A , aymovotr B, Outer inser C, Outer india E, inmun G ";
	$query= $query."where A.movfec>= '$fec' and A.movfec<= '$fec1'  ";
	$query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue and movtip <> 'I' and movfue <> 'PO' ";
	$query= $query."and C.sercod=A.movsin ";
	$query= $query."and A.movnum is not null ";
	$query= $query."and A.movap2 is null ";
	$query= $query."and E.diacod=A.movdia and A.movmun=G.muncod and G.mundep='01' ";
	$query= $query." union ";
	$query= $query."select A.movdoc, A.movfue, A.movhor, 0 as movnum, A.movced, A.movtid, A.movsin, A.movnom, A.movape, A.movap2, A.movfec, A.movnac, A.movsex, A.movcer, B.movotrbar, A.movdia, C.sernom, E.dianom, G.munnom   ";
	$query= $query."from aymov A , aymovotr B, Outer inser C, Outer india E, inmun G ";
	$query= $query."where A.movfec>= '$fec' and A.movfec<= '$fec1'  ";
	$query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue and movtip <> 'I' and movfue <> 'PO' ";
	$query= $query."and C.sercod=A.movsin ";
	$query= $query."and A.movnum is null ";
	$query= $query."and A.movap2 is not null ";
	$query= $query."and E.diacod=A.movdia and A.movmun=G.muncod and G.mundep='01' ";
	$query= $query." order by 3";

	$err_o = odbc_exec($conex_o,$query);


	while (odbc_fetch_row ($err_o))
	{


		$his [$i]= odbc_result($err_o,1);
		$numIng [$i]= odbc_result($err_o,2);
		$ced [$i]= odbc_result($err_o,5);
		$cedTip [$i]= odbc_result($err_o,6);
		$nom [$i]= odbc_result($err_o,8);
		$ape1 [$i]= odbc_result($err_o,9);
		$ape2 [$i]= odbc_result($err_o,10);
		$fecIng [$i]= odbc_result($err_o,11);
		$fecNac [$i]= odbc_result($err_o,12);
		$sex[$i]=odbc_result($err_o,13);
		$codRes [$i]= odbc_result($err_o,14);
		$bar [$i]= odbc_result($err_o,15);
		$serNom [$i]= odbc_result($err_o,17);
		$diaNom [$i]= odbc_result($err_o,18);
		$horIng [$i]= odbc_result($err_o,3);
		$serAct [$i]= odbc_result($err_o,17);
		$fecEgr [$i]= '';
		$horEgr [$i]='';
		$amb[$i]=0;
		$mun[$i]= odbc_result($err_o,19);
		$fuente[$i]='aymov';
		$i++;

	}

	//búsqueda en inpaci,entre las dos fechas

	// $query="select D.egrhis, D.egrnum, A.pacced, A.pactid, A.pacnom, A.pacap1, A.pacap2, A.pacnac, A.pacsex,  B.pacotrbar, C.sernom,  D.egrcer, D.egrdin, D.egring, D.egrhoi, D.egrhoe, D.egregr, D.egrsin, E.dianom, D.egrhos, D.egrseg, F.munnom   ";
	// $query= $query."from inpaci A , inpacotr B, OUTER inser C, inmegr D, OUTER india E, inmun F ";
	// $query= $query."WHERE D.egring>='$fec' and  D.egring<='$fec1' ";
	// $query= $query."and A.pachis=D.egrhis and A.pacnum=D.egrnum ";
	// $query= $query."and B.pacotrhis=D.egrhis and B.pacotrnum=D.egrnum ";
	// $query= $query."and C.sercod=D.egrsin ";
	// $query= $query."and E.diacod=D.egrdin and A.pacmun=F.muncod and F.mundep='01'  order by D.egrhoi  ";

	$query="select D.egrhis, D.egrnum, A.pacced, A.pactid, A.pacnom, A.pacap1, A.pacap2, A.pacnac, A.pacsex,  B.pacotrbar, C.sernom,  D.egrcer, D.egrdin, D.egring, D.egrhoi, D.egrhoe, D.egregr, D.egrsin, E.dianom, D.egrhos, D.egrseg, F.munnom   ";
	$query= $query."from inpaci A , inpacotr B, OUTER inser C, inmegr D, OUTER india E, inmun F ";
	$query= $query."WHERE D.egring>='$fec' and  D.egring<='$fec1' ";
	$query= $query."and A.pachis=D.egrhis and A.pacnum=D.egrnum ";
	$query= $query."and B.pacotrhis=D.egrhis and B.pacotrnum=D.egrnum ";
	$query= $query."and C.sercod=D.egrsin ";
	$query= $query."and A.pacap2 is not null ";
	$query= $query."and E.diacod=D.egrdin and A.pacmun=F.muncod and F.mundep='01' "; 
	$query= $query." union ";
	$query= $query."select D.egrhis, D.egrnum, A.pacced, A.pactid, A.pacnom, A.pacap1, '.' as pacap2, A.pacnac, A.pacsex,  B.pacotrbar, C.sernom,  D.egrcer, D.egrdin, D.egring, D.egrhoi, D.egrhoe, D.egregr, D.egrsin, E.dianom, D.egrhos, D.egrseg, F.munnom   ";
	$query= $query."from inpaci A , inpacotr B, OUTER inser C, inmegr D, OUTER india E, inmun F ";
	$query= $query."WHERE D.egring>='$fec' and  D.egring<='$fec1' ";
	$query= $query."and A.pachis=D.egrhis and A.pacnum=D.egrnum ";
	$query= $query."and B.pacotrhis=D.egrhis and B.pacotrnum=D.egrnum ";
	$query= $query."and C.sercod=D.egrsin ";
	$query= $query."and A.pacap2 is null ";
	$query= $query."and E.diacod=D.egrdin and A.pacmun=F.muncod and F.mundep='01' "; 
	$query= $query." order by 15 ";
	
	$err_o = odbc_exec($conex_o,$query);

	while (odbc_fetch_row ($err_o))
	{
		$his [$i]= odbc_result($err_o,1);
		$numIng [$i]= odbc_result($err_o,2);
		$ced [$i]= odbc_result($err_o,3);
		$cedTip [$i]= odbc_result($err_o,4);
		$nom [$i]= odbc_result($err_o,5);
		$ape1 [$i]= odbc_result($err_o,6);
		$ape2 [$i]= odbc_result($err_o,7);
		$fecNac [$i]= odbc_result($err_o,8);
		$sex[$i]=odbc_result($err_o,9);
		$codRes [$i]= odbc_result($err_o,10);
		$fecIng [$i]= odbc_result($err_o,14);
		$fecEgr [$i]= odbc_result($err_o,15);
		$horIng [$i]= odbc_result($err_o,16);
		$horEgr [$i]= odbc_result($err_o,17);
		$bar [$i]= odbc_result($err_o,17).'-a';
		$serNom [$i]= odbc_result($err_o,11);
		$diaNom [$i]= odbc_result($err_o,19);
		$mun[$i]= odbc_result($err_o,22);
		$pacHos[$i]=odbc_result($err_o,20);
		$fuente[$i]='inpaci';

		switch ($pacHos[$i])
		{
			case 'H':
			$amb[$i]=1;
			$serAct [$i]=odbc_result($err_o,21);
			break;
			case 'C':
			$amb[$i]=0;
			$serAct [$i]= odbc_result($err_o,11);
			break;
			case 'A':
			$amb[$i]=0;
			$serAct [$i]= odbc_result($err_o,11);
			break;
		}
		$i++;
	}
	//echo $i;
	$ingresos= $i;


	//////////////////////////////////////////////////////////Fin Busqueda principal en db////////////////////////////

	//////////////////////////////////////////////////////////presentacion de clientes internacionales////////////////////////////


	ECHO "<table border=0 align=center size=100%>";
	ECHO "<tr><td align=center >LISTA DE VISITAS PARA: ".$fec." - ".$fec1."</font></a></td></tr>";
	ECHO "</table></br>";

	echo "<center><font size=4 color='#0000cc'>PACIENTES INTERNACIONALES</center></font></br></br>";

	$k=0;
	for ($j=0; $j<$ingresos; $j++)
	{
		$array[$k][0]=trim($ced[$j]).' '.trim($cedTip[$j]);
		$array[$k][1]=$his [$j];
		$array[$k][2]=$nom [$j].$ape1 [$j].$ape2 [$j];
		$array[$k][3]=$fecIng [$j];
		$array[$k][4]=$serNom [$j];
		$array[$k][5]= $horIng [$j];
		$array[$k][6]=$serAct [$j];
		$array[$k][7]=$numIng [$j];
		$array[$k][8]=$fuente [$j];
		$array[$k][9]=$fecNac [$j];
		$array[$k][10]=$amb [$j];
		$array[$k][11]=$ced [$j];
		$array[$k][12]=$cedTip[$j];
		$array[$k][13]=$mun[$j];
		$k++;
	}

	if ($k !=0)
	{
		//utilizo el include incvisitas para ordenar el vector por fecha de ingreso
		$m=$k-1;
		$array=ordenador (3, $array, $m, 14);
		$array=ordenador2  (5, 3, $array, $m, 14);
		$size1=count($array);

		echo "<table border=0 align=center size=100%>";
		echo "<tr><td align=center ><font size=3 color='blue'>Numero de Visitas internacionales: $size1</font></td></tr>";
		echo "</table></br>";
		echo '<TABLE border=1 align=center>';
		echo "<Tr class='encabezadotabla'>";
		echo "<td align=center><font size=2>Documento</font></td>";
		echo "<td align=center><font size=2>N Historia</font></td>";
		echo "<td align=center width=15%><font size=2>Nombre</font></td>";
		echo "<td align=center><font size=2>Fecha de ingreso</font></td>";
		echo "<td align=center width=10%><font size=2>Unidad de ingreso</font></td>";
		echo "<td align=center width=5%><font size=2>Hora de Ingreso</font></td>";
		echo "<td align=center width=10%><font size=2>Servicio Actual</font></td>";
		echo "<td align=center width=5%><font size=2>Hab.</font></td>";
		echo "<td align=center><font size=2>Amb.</font></td>";
		echo "<td align=center><font size=2>Ciudad</font></td>";
		echo "</Tr >";

		for ($i=0; $i<$size1; $i++)
		{
			$doc=trim($array[$i][11]);
			//convierto el tipo de documento para mandarlo por el hipervinculo
			$tipDoc=trim($array[$i][12]);
			switch ($tipDoc)
			{
				case "CC": $tipDoc="CC-CEDULA DE CIUDADANIA";
				break;
				case "TI": $tipDoc="TI-Tarjeta de Identidad";
				break;
				case "NU": $tipDoc="NU-Numero Unico de Identificacion";
				break;
				case "CE": $tipDoc="CE-Cedula de Extrangeria";
				break;
				case "PA": $tipDoc="PA-Pasaporte";
				break;
				case "RC": $tipDoc="RC-Registro Civil";
				break;
				case "AS": $tipDoc="AS-Adulto Sin Identificacion";
				break;
				case "MS": $tipDoc="MS-Menor Sin Identificacion";
				break;
			}

			if (is_int ($i/2))
			$color='fila1';
			else
			$color='fila2';

			echo "<Tr class='$color'>";
			echo "<td align=center><a href='/Matrix/Magenta/procesos/Magenta.php?ced=1&doc=".$doc."&tipDoc=".$tipDoc."' target='_blank'><font size=2>".$array[$i][0]."</font></a></td>";
			for ($j=1; $j<6; $j++)
			{
				echo "<td align=center><font size=2>".$array[$i][$j].'</font></td>';
			}

			//se investiga para hospitalizado el ultimo servicio en que se encontro el paciente
			switch ($array[$i][10])
			{
				case 0:
				echo "<td align=center ><font size=2>".$array[$i][6].'</font></td>';
				echo "<td align=center >&nbsp;</td>";
				echo "<td ><input type='checkbox' name='Ambulatorio'  checked ></td>";
				break;
				case 1:

				if ($array[$i][8]=='inpac')
				{
					$query="select A.traser, A.trahab, B.sernom    ";
					$query= $query."from inmtra A , inser B ";
					$query= $query."where A.trahis='".$array[$i][1]."' and A.tranum='".$array[$i][7]."' and A.traegr is null ";
					$query= $query."and B.sercod=A.traser ";
				}
				else
				{
					$query="select A.traser, A.trahab, B.sernom    ";
					$query= $query."from inmtra A , inser B ";
					$query= $query."where A.trahis='".$array[$i][1]."' and A.tranum='".$array[$i][7]."' and A.traser='".$array[$i][6]."' ";
					$query= $query."and B.sercod=A.traser ";
				}

				$err_o = odbc_exec($conex_o,$query);
				$resulta=odbc_result($err_o,3);
				echo "<td align=center><font size=2>".$resulta."</font></td>";
				$resulta=odbc_result($err_o,2);
				echo "<td align=center><font size=2>".$resulta."</font></td>";
				echo "<td ><input type='checkbox' name='Ambulatorio'></td>";
				break;
			}

			echo "<td align=center ><font size=2>".$array[$i][13].'</font></td>';
			echo "</Tr >";
		}

		echo "</table>";
	}ELSE
	{	
	echo "<CENTER><fieldset style='border:solid;border-color:#ADD8E6; width=700' ><table>";
	echo "<tr><td colspan='2'><font size=3  face='arial'><b>NINGUNO PACIENTE INTERNACIONAL HA INGRESADO EL DIA DE HOY</td><tr>";
	echo "</table></fieldset>";
	}


	//////////////////////////////////////////////////////////Fin selección de clientes AAA////////////////////////////
	//////////////////////////////////////////////////////////Busqueda principal en db////////////////////////////


	$i= 0;
	//Busco inicialmente en inpac quienes han venido hoy que sea paciente nacional

	if ($fecha1>=date('Y-m-d'))
	{
		// $query="select A.pachis, A.pacnum, A.pachor, A.pachos, A.pacced, A.pactid, A.pacser, A.pacnom, A.pacap1, A.pacap2, A.pacfec, A.pacnac, A.pacsex, A.paccer, B.pacotrbar, A.pacdin, C.sernom, E.dianom,  G.munnom  ";
		// $query= $query."from inpac A , inpacotr B, Outer inser C, Outer india E,  inmun G ";
		// $query= $query."where B.pacotrhis=A.pachis and B.pacotrnum=A.pacnum ";
		// $query= $query."and C.sercod=A.pacser ";
		// $query= $query."and E.diacod=A.pacdin  ";
		// $query= $query."and G.muncod=A.pacmun and G.mundep<>'01' and G.mundep<>'05'  ";
		
		$query="select A.pachis, A.pacnum, A.pachor, A.pachos, A.pacced, A.pactid, A.pacser, A.pacnom, A.pacap1, A.pacap2, A.pacfec, A.pacnac, A.pacsex, A.paccer, B.pacotrbar, A.pacdin, C.sernom, E.dianom,  G.munnom  ";
		$query= $query."from inpac A , inpacotr B, Outer inser C, Outer india E,  inmun G ";
		$query= $query."where B.pacotrhis=A.pachis and B.pacotrnum=A.pacnum ";
		$query= $query."and C.sercod=A.pacser ";
		$query= $query."and E.diacod=A.pacdin  ";
		$query= $query."and A.pacap2 is not null ";
		$query= $query."and G.muncod=A.pacmun and G.mundep<>'01' and G.mundep<>'05'  ";
		$query= $query." union ";
		$query= $query."select A.pachis, A.pacnum, A.pachor, A.pachos, A.pacced, A.pactid, A.pacser, A.pacnom, A.pacap1, '.' as pacap2, A.pacfec, A.pacnac, A.pacsex, A.paccer, B.pacotrbar, A.pacdin, C.sernom, E.dianom,  G.munnom  ";
		$query= $query."from inpac A , inpacotr B, Outer inser C, Outer india E,  inmun G ";
		$query= $query."where B.pacotrhis=A.pachis and B.pacotrnum=A.pacnum ";
		$query= $query."and C.sercod=A.pacser ";
		$query= $query."and E.diacod=A.pacdin  ";
		$query= $query."and A.pacap2 is null ";
		$query= $query."and G.muncod=A.pacmun and G.mundep<>'01' and G.mundep<>'05'  ";

		$err_o = odbc_exec($conex_o,$query);


		while (odbc_fetch_row ($err_o))
		{
			switch (odbc_result($err_o,4))
			{
				case 'H':
				$am=1;
				break;
				case 'C':
				$am=0;
				break;
				case 'A':
				$am=0;
				break;
			}

			$query="select A.traser, A.trahab   ";
			$query= $query."from inmtra A  ";
			$query= $query."where A.trahis='".odbc_result($err_o,1)."' and A.tranum='".odbc_result($err_o,2)."' and A.traegr is null ";

			$err_1 = odbc_exec($conex_o,$query);
			$suma=0;

			while (odbc_fetch_row ($err_1))
			{
				$suma++;
			}

			if (($am==0 and odbc_result($err_o,11)==date('Y/m/d')) or ($am==1 and $suma>0) or $fecha!=$fecha1)
			{
				$his [$i]= odbc_result($err_o,1);
				$numIng [$i]= odbc_result($err_o,2);
				$ced [$i]= odbc_result($err_o,5);
				$cedTip [$i]= odbc_result($err_o,6);
				$nom [$i]= odbc_result($err_o,8);
				$ape1 [$i]= odbc_result($err_o,9);
				$ape2 [$i]= odbc_result($err_o,10);
				$fecIng [$i]= odbc_result($err_o,11);
				$fecNac [$i]= odbc_result($err_o,12);
				$sex[$i]=odbc_result($err_o,13);
				$codRes [$i]= odbc_result($err_o,14);
				$bar [$i]= odbc_result($err_o,15);
				$serNom [$i]= odbc_result($err_o,17);
				$diaNom [$i]= odbc_result($err_o,18);
				$horIng [$i]= odbc_result($err_o,3);
				$pacHos [$i]= odbc_result($err_o,4);
				$mun[$i]= odbc_result($err_o,19);
				$fuente[$i]='inpac';

				switch ($pacHos[$i])
				{
					case 'H':
					$amb[$i]=1;
					$serAct [$i]='';
					break;
					case 'C':
					$amb[$i]=0;
					$serAct [$i]= odbc_result($err_o,17);
					break;
					case 'A':
					$amb[$i]=0;
					$serAct [$i]= odbc_result($err_o,17);
					break;
				}


				$i++;
			}
		}

	}
	//búsco en aymov
	// $query="select A.movdoc, A.movfue, A.movhor, A.movnum, A.movced, A.movtid, A.movsin, A.movnom, A.movape, A.movap2, A.movfec, A.movnac, A.movsex, A.movcer, B.movotrbar, A.movdia, C.sernom, E.dianom, G.munnom   ";
	// $query= $query."from aymov A , aymovotr B, Outer inser C, Outer india E, inmun G ";
	// $query= $query."where A.movfec>= '$fec' and A.movfec<= '$fec1'  ";
	// $query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue and movtip <> 'I' and movfue <> 'PO' ";
	// $query= $query."and C.sercod=A.movsin ";
	// $query= $query."and E.diacod=A.movdia and A.movmun=G.muncod and G.mundep<>'01' and G.mundep<>'05' order by A.movhor  ";
	
	$query="select A.movdoc, A.movfue, A.movhor, A.movnum, A.movced, A.movtid, A.movsin, A.movnom, A.movape, A.movap2, A.movfec, A.movnac, A.movsex, A.movcer, B.movotrbar, A.movdia, C.sernom, E.dianom, G.munnom   ";
	$query= $query."from aymov A , aymovotr B, Outer inser C, Outer india E, inmun G ";
	$query= $query."where A.movfec>= '$fec' and A.movfec<= '$fec1'  ";
	$query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue and movtip <> 'I' and movfue <> 'PO' ";
	$query= $query."and C.sercod=A.movsin ";
	$query= $query."and A.movap2 is not null ";
	$query= $query."and A.movnum is not null ";
	$query= $query."and E.diacod=A.movdia and A.movmun=G.muncod and G.mundep<>'01' and G.mundep<>'05'  ";
	$query= $query." union ";
	$query= $query."select A.movdoc, A.movfue, A.movhor, 0 as movnum, A.movced, A.movtid, A.movsin, A.movnom, A.movape, '.' as movap2, A.movfec, A.movnac, A.movsex, A.movcer, B.movotrbar, A.movdia, C.sernom, E.dianom, G.munnom   ";
	$query= $query."from aymov A , aymovotr B, Outer inser C, Outer india E, inmun G ";
	$query= $query."where A.movfec>= '$fec' and A.movfec<= '$fec1'  ";
	$query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue and movtip <> 'I' and movfue <> 'PO' ";
	$query= $query."and C.sercod=A.movsin ";
	$query= $query."and A.movap2 is null ";
	$query= $query."and A.movnum is null ";
	$query= $query."and E.diacod=A.movdia and A.movmun=G.muncod and G.mundep<>'01' and G.mundep<>'05' ";
	$query= $query." union ";
	$query= $query."select A.movdoc, A.movfue, A.movhor, A.movnum, A.movced, A.movtid, A.movsin, A.movnom, A.movape, '.' as movap2, A.movfec, A.movnac, A.movsex, A.movcer, B.movotrbar, A.movdia, C.sernom, E.dianom, G.munnom   ";
	$query= $query."from aymov A , aymovotr B, Outer inser C, Outer india E, inmun G ";
	$query= $query."where A.movfec>= '$fec' and A.movfec<= '$fec1'  ";
	$query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue and movtip <> 'I' and movfue <> 'PO' ";
	$query= $query."and C.sercod=A.movsin ";
	$query= $query."and A.movap2 is null ";
	$query= $query."and A.movnum is not null ";
	$query= $query."and E.diacod=A.movdia and A.movmun=G.muncod and G.mundep<>'01' and G.mundep<>'05' ";
	$query= $query." union ";
	$query= $query."select A.movdoc, A.movfue, A.movhor, 0 as movnum, A.movced, A.movtid, A.movsin, A.movnom, A.movape, A.movap2, A.movfec, A.movnac, A.movsex, A.movcer, B.movotrbar, A.movdia, C.sernom, E.dianom, G.munnom   ";
	$query= $query."from aymov A , aymovotr B, Outer inser C, Outer india E, inmun G ";
	$query= $query."where A.movfec>= '$fec' and A.movfec<= '$fec1'  ";
	$query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue and movtip <> 'I' and movfue <> 'PO' ";
	$query= $query."and C.sercod=A.movsin ";
	$query= $query."and A.movap2 is not null ";
	$query= $query."and A.movnum is null ";
	$query= $query."and E.diacod=A.movdia and A.movmun=G.muncod and G.mundep<>'01' and G.mundep<>'05' ";
	$query= $query." order by 3 ";

	$err_o = odbc_exec($conex_o,$query);


	while (odbc_fetch_row ($err_o))
	{


		$his [$i]= odbc_result($err_o,1);
		$numIng [$i]= odbc_result($err_o,2);
		$ced [$i]= odbc_result($err_o,5);
		$cedTip [$i]= odbc_result($err_o,6);
		$nom [$i]= odbc_result($err_o,8);
		$ape1 [$i]= odbc_result($err_o,9);
		$ape2 [$i]= odbc_result($err_o,10);
		$fecIng [$i]= odbc_result($err_o,11);
		$fecNac [$i]= odbc_result($err_o,12);
		$sex[$i]=odbc_result($err_o,13);
		$codRes [$i]= odbc_result($err_o,14);
		$bar [$i]= odbc_result($err_o,15);
		$serNom [$i]= odbc_result($err_o,17);
		$diaNom [$i]= odbc_result($err_o,18);
		$horIng [$i]= odbc_result($err_o,3);
		$serAct [$i]= odbc_result($err_o,17);
		$fecEgr [$i]= '';
		$horEgr [$i]='';
		$amb[$i]=0;
		$mun[$i]= odbc_result($err_o,19);
		$fuente[$i]='aymov';
		$i++;

	}


	//búsqueda en inpaci,algunas veces los de el mismo día los egresan de una vez

	// $query="select D.egrhis, D.egrnum, A.pacced, A.pactid, A.pacnom, A.pacap1, A.pacap2, A.pacnac, A.pacsex,  B.pacotrbar, C.sernom,  D.egrcer, D.egrdin, D.egring, D.egrhoi, D.egrhoe, D.egregr, D.egrsin, E.dianom, F.munnom, D.egrhos, D.egrseg   ";
	// $query= $query."from inpaci A , inpacotr B, OUTER inser C, inmegr D, OUTER india E, inmun F ";
	// $query= $query."WHERE D.egring>='$fec' and  D.egring<='$fec1' ";
	// $query= $query."and A.pachis=D.egrhis and A.pacnum=D.egrnum ";
	// $query= $query."and B.pacotrhis=D.egrhis and B.pacotrnum=D.egrnum ";
	// $query= $query."and C.sercod=D.egrsin ";
	// $query= $query."and E.diacod=D.egrdin and A.pacmun=F.muncod and F.mundep<>'01' and F.mundep<>'05' order by D.egrhoi  ";
	
	$query="select D.egrhis, D.egrnum, A.pacced, A.pactid, A.pacnom, A.pacap1, A.pacap2, A.pacnac, A.pacsex,  B.pacotrbar, C.sernom,  D.egrcer, D.egrdin, D.egring, D.egrhoi, D.egrhoe, D.egregr, D.egrsin, E.dianom, F.munnom, D.egrhos, D.egrseg   ";
	$query= $query."from inpaci A , inpacotr B, OUTER inser C, inmegr D, OUTER india E, inmun F ";
	$query= $query."WHERE D.egring>='$fec' and  D.egring<='$fec1' ";
	$query= $query."and A.pachis=D.egrhis and A.pacnum=D.egrnum ";
	$query= $query."and B.pacotrhis=D.egrhis and B.pacotrnum=D.egrnum ";
	$query= $query."and C.sercod=D.egrsin ";
	$query= $query."and A.pacap2 is not null ";
	$query= $query."and E.diacod=D.egrdin and A.pacmun=F.muncod and F.mundep<>'01' and F.mundep<>'05' ";
	$query= $query." union ";
	$query= $query."select D.egrhis, D.egrnum, A.pacced, A.pactid, A.pacnom, A.pacap1, '.' as pacap2, A.pacnac, A.pacsex,  B.pacotrbar, C.sernom,  D.egrcer, D.egrdin, D.egring, D.egrhoi, D.egrhoe, D.egregr, D.egrsin, E.dianom, F.munnom, D.egrhos, D.egrseg   ";
	$query= $query."from inpaci A , inpacotr B, OUTER inser C, inmegr D, OUTER india E, inmun F ";
	$query= $query."WHERE D.egring>='$fec' and  D.egring<='$fec1' ";
	$query= $query."and A.pachis=D.egrhis and A.pacnum=D.egrnum ";
	$query= $query."and B.pacotrhis=D.egrhis and B.pacotrnum=D.egrnum ";
	$query= $query."and C.sercod=D.egrsin ";
	$query= $query."and A.pacap2 is null ";
	$query= $query."and E.diacod=D.egrdin and A.pacmun=F.muncod and F.mundep<>'01' and F.mundep<>'05'   ";
	$query= $query." order by 15 ";

	$err_o = odbc_exec($conex_o,$query);

	while (odbc_fetch_row ($err_o))
	{
		$his [$i]= odbc_result($err_o,1);
		$numIng [$i]= odbc_result($err_o,2);
		$ced [$i]= odbc_result($err_o,3);
		$cedTip [$i]= odbc_result($err_o,4);
		$nom [$i]= odbc_result($err_o,5);
		$ape1 [$i]= odbc_result($err_o,6);
		$ape2 [$i]= odbc_result($err_o,7);
		$fecNac [$i]= odbc_result($err_o,8);
		$sex[$i]=odbc_result($err_o,9);
		$codRes [$i]= odbc_result($err_o,10);
		$fecIng [$i]= odbc_result($err_o,14);
		$fecEgr [$i]= odbc_result($err_o,15);
		$horIng [$i]= odbc_result($err_o,16);
		$horEgr [$i]= odbc_result($err_o,17);
		$bar [$i]= odbc_result($err_o,17).'-a';
		$serNom [$i]= odbc_result($err_o,11);
		$diaNom [$i]= odbc_result($err_o,19);
		$mun[$i]= odbc_result($err_o,20);
		$pacHos[$i]=odbc_result($err_o,21);
		$fuente[$i]='inpaci';

		switch ($pacHos[$i])
		{
			case 'H':
			$amb[$i]=1;
			$serAct [$i]=odbc_result($err_o,22);
			break;
			case 'C':
			$amb[$i]=0;
			$serAct [$i]= odbc_result($err_o,11);
			break;
			case 'A':
			$amb[$i]=0;
			$serAct [$i]= odbc_result($err_o,11);
			break;
		}
		$i++;
	}
	//echo $i;
	$ingresos= $i;


	//////////////////////////////////////////////////////////Fin Busqueda principal en db////////////////////////////

	//////////////////////////////////////////////////////////selección de clientes AAA////////////////////////////

	echo "</br></br><center><font size=4 color='#0000cc'>PACIENTES NACIONALES</center></font></br></br>";

	$k=0;
	for ($j=0; $j<$ingresos; $j++)
	{
		$array2[$k][0]=trim($ced[$j]).' '.trim($cedTip[$j]);
		$array2[$k][1]=$his [$j];
		$array2[$k][2]=$nom [$j].$ape1 [$j].$ape2 [$j];
		$array2[$k][3]=$fecIng [$j];
		$array2[$k][4]=$serNom [$j];
		$array2[$k][5]= $horIng [$j];
		$array2[$k][6]=$serAct [$j];
		$array2[$k][7]=$numIng [$j];
		$array2[$k][8]=$fuente [$j];
		$array2[$k][9]=$fecNac [$j];
		$array2[$k][10]=$amb [$j];
		$array2[$k][11]=$ced [$j];
		$array2[$k][12]=$cedTip[$j];
		$array2[$k][13]=$mun[$j];
		$k++;
	}

	if ($k !=0)
	{
		$m=$k-1;
		$array2=ordenador (3, $array2, $m, 14);
		$array2=ordenador2  (5, 3, $array2, $m, 14);
		$size1=count($array2);

		ECHO "<table border=0 align=center size=100%>";
		ECHO "<tr><td align=center ><font size=3 color='blue'>Numero de Visitas Nacionales: $size1</font></td></tr>";
		ECHO "</table></br>";


		ECHO '<TABLE border=1 align=center>';
		ECHO "<Tr class='encabezadotabla'>";

		echo "<td align=center><font size=2>Documento</font></td>";
		echo "<td align=center><font size=2>N Historia</font></td>";
		echo "<td align=center width=15%><font size=2>Nombre</font></td>";
		echo "<td align=center><font size=2>Fecha de ingreso</font></td>";
		echo "<td align=center width=10%><font size=2>Unidad de ingreso</font></td>";
		echo "<td align=center width=5%><font size=2>Hora de Ingreso</font></td>";
		echo "<td align=center width=10%><font size=2>Servicio Actual</font></td>";
		echo "<td align=center width=5%><font size=2>Hab.</font></td>";
		echo "<td align=center><font size=2>Amb.</font></td>";
		echo "<td align=center><font size=2>Ciudad</font></td>";

		ECHO "</Tr >";

		for ($i=0; $i<$size1; $i++)
		{
			$doc=trim($array2[$i][11]);
			$tipDoc=trim($array2[$i][12]);
			switch ($tipDoc)
			{
				case "CC": $tipDoc="CC-CEDULA DE CIUDADANIA";
				break;
				case "TI": $tipDoc="TI-Tarjeta de Identidad";
				break;
				case "NU": $tipDoc="NU-Numero Unico de Identificacion";
				break;
				case "CE": $tipDoc="CE-Cedula de Extrangeria";
				break;
				case "PA": $tipDoc="PA-Pasaporte";
				break;
				case "RC": $tipDoc="RC-Registro Civil";
				break;
				case "AS": $tipDoc="AS-Adulto Sin Identificacion";
				break;
				case "MS": $tipDoc="MS-Menor Sin Identificacion";
				break;
			}

			if (is_int ($i/2))
			$color='fila1';
			else
			$color='fila2';

			ECHO "<Tr class='$color'>";
			echo "<td align=center><a href='/Matrix/Magenta/procesos/Magenta.php?ced=1&doc=".$doc."&tipDoc=".$tipDoc."' target='_blank'><font size=2>".$array2[$i][0]."</font></a></td>";
			for ($j=1; $j<6; $j++)
			{
				echo "<td align=center><font size=2>".$array2[$i][$j].'</font></td>';
			}

			switch ($array2[$i][10])
			{
				case 0:
				echo "<td align=center ><font size=2>".$array2[$i][6].'</font></td>';
				echo "<td align=center >&nbsp;</td>";
				echo "<td ><input type='checkbox' name='Ambulatorio'  checked ></td>";
				break;
				case 1:
				if ($array2[$i][8]=='inpac')
				{
					$query="select A.traser, A.trahab, B.sernom    ";
					$query= $query."from inmtra A , inser B ";
					$query= $query."where A.trahis='".$array2[$i][1]."' and A.tranum='".$array2[$i][7]."' and A.traegr is null ";
					$query= $query."and B.sercod=A.traser ";
				}
				else
				{
					$query="select A.traser, A.trahab, B.sernom    ";
					$query= $query."from inmtra A , inser B ";
					$query= $query."where A.trahis='".$array2[$i][1]."' and A.tranum='".$array2[$i][7]."' and A.traser='".$array2[$i][6]."' ";
					$query= $query."and B.sercod=A.traser ";
				}
				$err_o = odbc_exec($conex_o,$query);
				$resulta=odbc_result($err_o,3);
				echo "<td align=center><font size=2>".$resulta."</font></td>";
				$resulta=odbc_result($err_o,2);
				echo "<td align=center><font size=2>".$resulta."</font></td>";
				echo "<td ><input type='checkbox' name='Ambulatorio'></td>";
				break;
			}

			echo "<td align=center ><font size=2>".$array2[$i][13].'</font></td>';

			ECHO "</Tr >";
		}

		echo "</table>";
	}ELSE
	{	
	echo "<CENTER><fieldset style='border:solid;border-color:#ADD8E6; width=700' ><table>";
	echo "<tr><td colspan='2'><font size=3  face='arial'><b>NINGUNO PACIENTE NACIONAL HA INGRESADO EL DIA DE HOY</td><tr>";
	echo "</table></fieldset>";
	}

}
//////////////////////////////////////////////////////////Fin selección de clientes AAA////////////////////////////
}else
{
	echo "ERROR : "."$errstr ($errno)<br>\n";
}

/**
 * cerrar conexiones
 */
include_once("free.php");
odbc_close_all();
}
?>