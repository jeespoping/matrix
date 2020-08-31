<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<title>Programa de comentarios y sugerencias</title>

<!-- UTF-8 is the recommended encoding for your pages -->
    <meta http-equiv="content-type" content="text/xml; charset=utf-8" />
    <title>Zapatec DHTML Calendar</title>

<!-- Loading Theme file(s) -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />

<!-- Loading Calendar JavaScript files -->
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>

 <!-- Loading language definition file -->
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>

<script type="text/javascript">
<!--

function calendario(id,vrl)
{
	if (vrl == "1")
	{
		Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:"12",electric:false,inputField:"fecOri",button:"envio3",ifFormat:"%Y-%m-%d",daFormat:"%Y/%m/%d"});
	}
	if (vrl == "2")
	{
		Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:"12",electric:false,inputField:"fecRec",button:"envio4",ifFormat:"%Y-%m-%d",daFormat:"%Y/%m/%d"});
	}
}

//-->

</script>    
    
<script language="javascript" >



function valorar()
{
	if ( !document.forma.envio.checked )
	{
		document.forma.envio.value='off';
		document.forma.envio.checked=true;
	}
}


function activar()
{
	document.forma.envio.checked=true;
}

    </script>

</head>
<body>
<?php
include_once("conex.php"); 

/**
 * INVESTIGACION DE COMENTARIO POR MAGENTA
 * 
 * Este programa permite INVESTIGAR un comentario para magenta cuando desee hacerlo
 * 
 * @name  matrix\magenta\procesos\investigacion2.php
 * @author Carolina Castaño Portilla.
 * 
 * @created 2006-04-04
 * @version 2006-01-09
 * 
 * @modified 2006-01-09  Se realiza documentacion del programa y se adapta para que muestre la historia clinica
 * 
 * @table magenta_000016, select sobre datos del paciente
 * @table magenta_000017, select, update 
 * @table magenta_000018, select, update
 * @table magenta_000019, select
 * @table magenta_000020, select
 * @table magenta_000021, select
 * @table magenta_000022, select
 * @table magenta_000022, select, insert, update
 * 
 *  @var $accAno acoDir, direccion del acompanana
 *  @var $acoEma, email del acompanante
 *  @var $acoNom, nombre del acompananate
 *  @var $acoTel, telefono del acompanante
 *  @var $aut, persona que ingreso el comentario
 *  @var $bandera1, indica si es la primera vez que se entra al programa o no
 *  @var $cadena, se utiliza para armar el menu e ir a la pagina de datos del paciente
 *  @var $cadena2, se utiliza para armar el menu e ir a la pagina de comenta del paciente
 *  @var $color color para desplegar el tipo de usuario que es el paciente
 *  @var $dir direccion del paciente
 *  @var $dir2 direccion del paciente codificada para madarla por 
 *  @var $doc documento del paciente
 *  @var $ema email del paciente
 *  @var $email direccion del coordinador para avisarle del 
 *  @var $emo contacto emocional
 *  @var $ent entidad del paciente
 *  @var $envFec fecha de envio del comentario
 *  @var $envio si se va a asignar el motivo o no (checkbox)
 *  @var $est estado del comentario para saber si poner el hipervinculo respuesta
 *  @var $fecCon fecha del contacto
 *  @var $fecOri fecha de origen del comentario
 *  @var $fecRec fecha de recepcion del comentario
 *  @var $his historia clinica del paciente
 *  @var $idArea id del area del motivo en tabla 000019
 *  @var $idCargo id del cargo del implicado en tabla 000022
 *  @var $idCom id del comentario en tabla 000017
 *  @var $idMot ide del motivo en tabla 000018
 *  @var $idPac id del paciente en tabla 000016
 *  @var $indicador, variable que si es 1 indica que hay que mandar un email de aviso
 *  @var $inicial, indica si los datos ingresado no cumplen con las validaciones cuando esta en uno
 *  @var $inicial1, variable usada en cadenas y ciclos
 *  @var $inicial2, variable usada en cadenas y ciclos
 *  @var $inv variable que sirve para acomodar la investigacion cuando se asigna a otro coordinador adicional del que que ya habia escrito algo
 *  @var $lugOri lugar de origen del comentario
 *  @var $mail nombre de la clase encargada de enviar el mail
 *  @var $motAre, area a la que se asigno el comentario
 *  @var $motAreS, lista de areas para el dropdown
 *  @var $motAreT, cuadro de texto para ingresar parte del area a asignar
 *  @var $motCar, cargo del implicado
 *  @var $motCarS, lista de cargos para el dropdown
 *  @var $motCarT, cuadro de texto para ingresar parte del cargo del implicado
 *  @var $motCla vector de clasificaciones de los motivos
 *  @var $motCon datos del contacto
 *  @var $motCoo coordinadores de los motivos
 *  @var $motDes vector de descripciones de los motivos
 *  @var $motEnv vector de las fechas de envio de los motivos
 *  @var $motEst vector de estados de los motivos
 *  @var $motImp vector de los implicados de los motivos
 *  @var $motNum vector de numero de los motivos
 *  @var $motTip vector de tipo de los motivos
 *  @var $perDil persona que dilignecio el comentario
 *  @var $priApe primer apellido del paciente
 *  @var $priNom primer nombre del paciente
 *  @var $resFec fecha de respuesta del comentario 
 *  @var $resulMai, indica que sucedio al enviar el email on aviso del 
 *  @var $segApe segundo apellido del paciente
 *  @var $segNom segundo nombre del paciente
 *  @var $semaforo, composicion entre el borde y relleno del color del semaforo del motivo
 *  @var $semaforoB, color del borde del semaforo para el motivo
 *  @var $semaforoR, color de relleno del semaforo para el motivo
 *  @var $tamano cantidad de motivos del comentario
 *  @var $tel telefono del paciente
 *  @var $tipDoc tipo de documento del paciente
 *  @var $tipUsu tipo de usuario para afinidad (AAA, BBB, VIP, no clasificado)
 *  @var $vol volveria o no a la clinica
*/

//=================================================================================================================================
$wautor="Carolina Castano P.";
$wversion='2007-01-09';

/********************************funciones************************************/

/*Si la variable tiene como valor dato no encontrado  retorna vacío*/
function  DisplayError()
{
	echo '<script language="Javascript">';
	echo 'alert ("Error al conectarse con la base de datos, intente más tarde")';
	echo '</script>';

}
/****************************PROGRAMA************************************************/
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{


	/////////////////////////////////////////////////inicialización de variables///////////////////////////////////
	/**
 	* calcula el tiempo que se han demorado en investigar el comentario, para color de semaforizacion
 	*/
	include_once("magenta/semaforo.php");
	/**
	 * conexion a Matrix
	 */
	

	


	$empresa='magenta';
	/////////////////////////////////////////////////inicialización de variables//////////////////////////
	/////////////////////////////////////////////////acciones concretas///////////////////////////////////


	if (isset($bandera1) and $bandera1==1 and ($motEst=='ASIGNADO' or $motEst=='TRAMITANDO')  )
	{

		for($i=0;$i<=$tamano;$i++)
		{
			eval("\$accNum[$i] = \$accNum$i;");
			eval("\$accRes[$i] = \$accRes$i;");
			eval("\$accDes[$i] = \$accDes$i;");
			eval("\$accAno[$i] = \$accAno$i;");
			eval("\$accMes[$i] = \$accMes$i;");
			eval("\$accDia[$i] = \$accDia$i;");
			eval("\$tipAcc[$i] = \$tipAcc$i;");

		}

		$tipAcc[$tamano+1]='CORRECCION';
		//validaciones de ingreso de datos

		$inicial=0;

		if ($motInv=='')
		$inicial=1;


		$i=0;

		//si la investigacion no esta vacia, valido que haya una accion
		while ($inicial == 0 )
		{
			//echo "MEMETO";
			if ($accRes[$i]=='' and  $accDes[$i]!='')
			$inicial=2;

			if ($inicial==0 and  $accDes[$i]!='')
			{
				if ((strlen ($accAno[$i]) != 4) or (strlen ($accMes[$i])!=2) or (strlen ($accDia[$i])!=2))
				$inicial=3;
				else
				$accFec[$i]=$accAno[$i]."-".$accMes[$i]."-".$accDia[$i];
			}

			if ($i==$tamano and $inicial==0)
			$inicial=4;

			$i++;
		}

		switch ($inicial)
		{
			case 1:
			echo '<script language="Javascript">';
			echo 'alert ("Debe ingresar la investigacion para el comentario")';
			echo '</script>';
			break;
			case 2:
			echo '<script language="Javascript">';
			echo 'alert ("Debe ingresar el responsable de la accion")';
			echo '</script>';
			break;
			case 3:
			echo '<script language="Javascript">';
			echo 'alert ("El formato para ingresar las fechas es como el siguiente ejemplo: Año:2006  Mes:02  Día:25 ")';
			echo '</script>';
			break;
		}

		if ($inicial==4)// podemos ingresar la investigacion del motivo y las diferentes acciones
		{
			$query="update " .$empresa."_000018 set cmoinv='".$motInv."', cmoest='TRAMITANDO' where cmonum= ".$idMot."  and id_Comentario=".$idCom." ";
			$err=mysql_query($query,$conex);


			$inicial=$tamano;


			for($i=0;$i<=$inicial;$i++)
			{
				if ($accDes[$i]!='' and $i==$inicial)
				{
					$query= " INSERT INTO  " .$empresa."_000023 (medico, Fecha_data, Hora_data, caccon, id_motivo, cacnum, cacdes, cacres, cacfver, cactip, cacest, seguridad)";
					$query= $query. "VALUES ('magenta','".date("Y-m-d")."','".date("h:i:s")."',".$idCom." ,".$idMot.", ".$accNum[$i].",'".strtoupper($accDes[$i])."', '".strtoupper($accRes[$i])."','".$accFec[$i]."','".$tipAcc[$i]."','INGRESADO', 'A-magenta') ";
					;
					$err=mysql_query($query,$conex);
					$tamano++;

				}else if ($accDes[$i]!='' and $i !=$inicial)
				{
					$query="update " .$empresa."_000023 set cacdes='".strtoupper($accDes[$i])."', cacres='".strtoupper($accRes[$i])."', cacfver='".$accFec[$i]."', cactip='".$tipAcc[$i]."', cacest='INGRESADO'  ";
					$query=$query."where caccon=".$idCom." and id_motivo=".$idMot." and cacnum=".$accNum[$i]." ";
					//echo $query;
					$err=mysql_query($query,$conex);
				}
			}
		}
		//echo $tamano;

		if ($envio=='on')
		{
			//ACTUALIZO EL ESTADO DEL MOTIVO
			$query="update " .$empresa."_000018 set cmoest='INVESTIGADO', cmofret='".date('Y-m-d')."' where cmonum= ".$idMot." and id_Comentario=".$idCom."  ";
			$err=mysql_query($query,$conex);
			$est='INVESTIGADO';
			//REVISO LOS MOTIVOS PARA ESE MISMO COMENTARIO A VER SI TODO ESTÁN EN INVESTIGADO, PARA PASAR EL
			//COMENTARIO A INVESTIGADO

			$query ="SELECT cmoest FROM " .$empresa."_000018 where id_comentario=".$idCom." ";

			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);
			//echo $query;
			if  ($num >0) //se llena los valores
			{
				$inicial=0;
				for($i=0;$i<$num;$i++)
				{
					$row=mysql_fetch_row($err);
					if ($row[0]=='INVESTIGADO')
					$inicial++;
				}

				if ($inicial ==$num)
				{
					$query="update " .$empresa."_000017 set ccoest='INVESTIGADO' where id= ".$idCom."  ";
					$err=mysql_query($query,$conex);
				}
			}
		}

	}

	//me mandan el id del comentario y me mandan el id del motivo

	if (isset ($idCom) and !isset ($bandera1) and !isset($idMot) and isset($numMot))
	{
		$query ="SELECT cmonum FROM " .$empresa."_000018 where id_comentario=".$idCom." and cmonum=".$numMot." ";
		$err=mysql_query($query,$conex);
		$num=mysql_num_rows($err);
		//echo $query;
		if  ($num >0) //se llena los valores
		{
			$row=mysql_fetch_row($err);
			$idMot=$row[0];
		}
		else
		{
			$query ="SELECT cmonum FROM " .$empresa."_000018 where id_comentario=".$idCom." order by id desc ";
			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);
			if  ($num >0) //se llena los valores
			{
				$row=mysql_fetch_row($err);
				$idMot=$row[0];
			}
		}
	}

	//me mandan el id del comentario y me mandan el id del motivo

	if (isset ($idCom) and (!isset ($bandera1) or $motEst=='INVESTIGADO' ))
	{
		$tamano=0;

		//consulto el comentario a investigar
		$query ="SELECT ccoori, ccofori, ccofrec, ccocemo, ccoent, id_persona, ccohis, cconum FROM " .$empresa."_000017 where id=".$idCom." ";
		$err=mysql_query($query,$conex);
		$num=mysql_num_rows($err);
		//echo $query;
		if  ($num >0) //se llena los valores
		{
			$row=mysql_fetch_row($err);
			$lugOri=$row[0];
			$fecOri=$row[1];
			$fecRec=$row[2];
			$emo=$row[3];
			$entidad=$row[4];
			$his=$row[6];
			$numCom=$row[7];

			$exp=explode('-',$row[5]);
			if(isset($exp[3]))
			{
				$exp[0]=$exp[0].'-'.$exp[1];
				$exp[1]=$exp[2];
				$exp[2]=$exp[3];
			}

			$query ="SELECT cpedoc, cpetdoc, cpeno1, cpeno2, cpeap1, cpeap2 FROM " .$empresa."_000016 where cpedoc='".$exp[0]."' and  cpetdoc='".$exp[1]."-".$exp[2]."' ";
			$err=mysql_query($query,$conex);
			$row=mysql_fetch_row($err);
			$exp=explode('-',$row[1]);
			$cedula=$row[0].'-'.$exp[0];
			$nombre=$row[2].' '.$row[3].' '.$row[4].' '.$row[5];

			//busco los datos de los motivos para el comentario
			$query ="SELECT A.cmonum, A.cmotip, A.cmodes, A.cmocla, A.cmoest, A.cmocon, '', A.cmofenv, A.cmoinv, A.id_area, '',  B.carcod, B.carnom, C.id_responsable, D.crecod, D.crenom, A.cmocau, A.cmofret, A.cmofenv ";
			$query= $query. "FROM " .$empresa."_000018 A, " .$empresa."_000019 B, " .$empresa."_000020 C, " .$empresa."_000021 D ";
			$query= $query. "where A.cmonum=".$idMot." ";
			$query= $query. "and A.id_Comentario=".$idCom." ";
			$query= $query. "and B.carcod=A.id_area ";
			$query= $query. "and C.id_area=A.id_area and carniv=1 ";
			$query= $query. "and D.crecod=C.id_responsable ";
			
			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);

			if  ($num >0) //se llena los valores
			{

				$row=mysql_fetch_row($err);
				$motNum=$row[0];
				$motTip=$row[1];
				$motDes=$row[2];
				$motCla=$row[3];
				$motEst=$row[4];
				$motCon=$row[5];
				$motEnv=$row[7];
				$motAre=$row[11]."-".$row[12];
				$motCoo=$row[14]."-".$row[15];
				$motInv=$row[8];
				$resFec=$row[17];
				$motCau=$row[16];
				$envFec=$row[18];

				//si aun no se ha gravado fecha la inicializo con la fecha de hoy
				if ($resFec=='0000-00-00')
				{
					$resFec=date('Y-m-d');
				}
				if ($envFec=='0000-00-00')
				{
					$envFec=date('Y-m-d');
				}

				//busco las acciones para el motivo
				$query ="SELECT cacnum, cacdes, cacres, cacfver, cactip, cacest FROM " .$empresa."_000023 where id_motivo=".$idMot." and caccon=".$idCom." ";

				$err=mysql_query($query,$conex);
				$num=mysql_num_rows($err);
				//echo $query;
				if  ($num >0) //se llena los valores
				{
					$tamano = $num;

					for($i=0;$i<$num;$i++)
					{
						$row=mysql_fetch_row($err);
						$accNum[$i]=$row[0];
						$accDes[$i]=$row[1];
						$accRes[$i]=$row[2];
						$accFec[$i]=$row[3];
						$tipAcc[$i]=$row[4];
						$accEst[$i]=$row[5];
						$inicial= explode ('-',$accFec[$i]);
						$accAno[$i]=$inicial[0];
						$accMes[$i]=$inicial[1];
						$accDia[$i]=$inicial[2];


					}
					$tipAcc[$tamano]='CORRECCION';
				}else
				{
					for($i=0;$i<=$tamano;$i++)
					{
						$tipAcc[$i]='CORRECCION';
					}
				}

				//busco los implicados para el motivo
				$query ="SELECT impnom, ccanom  FROM " .$empresa."_000027, " .$empresa."_000022 where impnmo=".$idMot." and impnco=".$idCom." and ccacod=impcca ";

				$erri=mysql_query($query,$conex);
				$numi=mysql_num_rows($erri);
				//echo $query;
				if  ($numi >0) //se llena los valores
				{
					for($i=0;$i<$numi;$i++)
					{
						$rowi=mysql_fetch_row($erri);
						$motImp[$i]=$rowi[0];
						$motCar[$i]=$rowi[1];
					}
				}else
				{
					$motImp[$i]='';
					$motCar[$i]='';
				}
			}
		}else
		{
			DisplayError();
		}
	}

	/////////////////////////////////////////////////encabezado general///////////////////////////////////
	echo "<table align='right'>" ;
	echo "<tr>" ;
	echo "<td><font color=\"#D02090\" size='2'>Autor: ".$wautor."</font></td>";
	echo "</tr>" ;
	echo "<tr>" ;
	echo "<td><font color=\"#D02090\" size='2'>Version: ".$wversion."</font></td>" ;
	echo "</tr>" ;
	echo "</table></br></br></br>" ;

	echo "<table align='center' border='3' bgcolor='#336699' >\n" ;
	echo "<tr>" ;
	echo "<td><img src='/matrix/images/medical/root/magenta.gif' height='61' width='113'></td>";
	echo "<td><font color=\"#ffffff\"><font size=\"5\"><b>&nbsp;SISTEMA DE COMENTARIOS Y SUGERENCIAS &nbsp;</br></b></font></font></td>" ;
	echo "</tr>" ;
	echo "</table></br></br>" ;


	echo "<table align='right' >\n" ;
	echo "<tr>" ;

	echo "<td><b><font size=\"4\"><A HREF='listaMagenta.php'><font color=\"#D02090\">Lista de comentarios</a>&nbsp;/&nbsp;</b></font></font></td>" ;

	echo "<td><b><font size=\"4\"><A HREF='ayuda1.mht' target='new'><font color=\"#D02090\">Ayuda</font></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></font></td>" ;
	echo "</tr>" ;
	echo "</table></br></br>" ;
	//////////////////////////////////////encabezado general///////////////////////////////////
	/////////////////////////////////////////////////encabezado personal///////////////////////////////////

	echo "<center><b><font size=\"4\"><A HREF='investigacion2.php?idCom=$idCom&idMot=$idMot'><font color=\"#D02090\">INVESTIGACION DE COMENTARIO</font></a></b></font></center>\n" ;
	echo "<center><b><font size=\"2\"><font color=\"#D02090\"> investigacion2.php</font></font></center></br></br></br>\n" ;
	echo "\n" ;
	/////////////////////////////////////////////////encabezado personal///////////////////////////////////
	/////////////////////////////////////////////////presentación general///////////////////////////////////

	//busco el semaforo del comentario
	IF (isset ($envFec) and isset($est) and $est=='INVESTIGADO')
	$semaforo=semaforo($est, $envFec, $idCom, $empresa, $conex, $idMot);
	else
	$semaforo=semaforo($motEst, $motEnv, $idCom, $empresa, $conex, $idMot);
	$inicial = explode("-",$semaforo);
	$semaforoB=$inicial[0];
	$semaforoR=$inicial[1];
	if (isset($est) and $est=='INVESTIGADO')
	{
		switch ($semaforoR)
		{
			case '#EOFFFF':
			$sem='verde';
			break;
			case '#ffffcc':
			$sem='amarillo';
			break;
			case '#ffcccc':
			$sem='rojo';
			break;
		}

		$query="update " .$empresa."_000018 set cmosem='".$sem."' ";
		$query=$query."where cmonum=".$idMot."  and id_Comentario=".$idCom." ";

		$err=mysql_query($query,$conex);

	}

	echo "<fieldset  style='border:solid;border-color:$semaforoB; width=100%' align='center'>";
	echo "<table align='center' bgcolor='$semaforoR' width='100%'>";

	echo "<tr>";
	echo "<td rowspan='".(8+count($motImp))."' align='center'><b><font size='3' ><font color='#00008B'> DATOS COMENTARIO:</font></font></td>";
	echo "<td colspan='1'><b><font size='3'>Identificacion del paciente:</font></b>$cedula</td>";
	echo "<td  colspan='2'> <b><font size='3'>Nombre del Paciente:</font></b> $nombre</td>";
	echo "</tr>";
	echo "<td colspan='1'><b><font size='3'>Entidad:</font></b>$entidad</td>";
	echo "<td  colspan='2'> <b><font size='3'>Lugar de origen:</font></b> $lugOri</td>";
	echo "</tr>";
	echo "</tr>";
	echo "<td colspan='1'><b><font size='3'>Historia Clinica:</font></b>$his</td>";
	echo "<td  colspan='2'> <b><font size='3'>&nbsp;</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td ><b><font size='3'>Fecha de origen:</font></b>$fecOri</td>";
	echo "<td colspan='2'><b><font size='3'> Fecha de envio: </font></b>$motEnv</td>";

	echo "</tr>";
	
	echo "</tr>";
	echo "<tr>";
	echo "<td clospan='2'><b><font size='3'>Tipo:</font></b>$motTip</td>";
	echo "<td clospan='2'><b><font size='3'>Causa:</font></b>$motCau</td>";

	echo "</tr>";
	echo "<tr>";
	echo "<td colspan='3' ><b><font size='3'> Descripcion: </font></b><font size='3'>".$motDes."</br>".$motCon."</font></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td ><b><font size='3' > Clasificacion: </font></b>$motCla</td>";
	echo "<td  colspan='2' > <b><font size='3'>Area:&nbsp;</font></b>$motAre</td>";
	echo "</tr>";
	for($i=0;$i<count($motImp)-1;$i++)
	{
		echo "<tr>";
		echo "<td  colspan='2'><b><font size='3'>Implicado:&nbsp;</font></b>".$motCar[$i]."-".$motImp[$i]."</td>";
		echo "<td  > <b><font size='3'>&nbsp;</font></b></td>";
		echo "</tr>";
	}
	echo "<tr>";
	echo "<td  colspan='2'> <b><font size='3'>Coordinador:&nbsp;</font></b>$motCoo</td>";
	echo "<td> <b><font size='3'>&nbsp;</font></b></td>";
	echo "</tr>";
	echo "</table></fieldset></br></br></br>";


	if ($motTip!='Agrado')
	{
		echo "<center><b><font size='4'><font color='#00008B'>INVESTIGACION DEL COMENTARIO NUMERO: ".$numCom."-".$motNum."</b></font></font></center></BR>";

		echo "<form action='investigacion2.php' name='forma' method='post'>";

		echo "<CENTER><b><font size='3'>INVESTIGACION POR PARTE DEL COORDINADOR:</b></font></BR>";
		echo "<textarea rows='4' cols='50' name='motInv'>$motInv</textarea></BR></BR>";

		$cal="calendario('envFec','1')";
		echo "<td  >FECHA DE ENVIO AL COORDINADOR: <input type='text' readonly='readonly' name='envFec' value='".$envFec."' class=tipo3 ><input type='button' name='envio3' value='...' onclick=".$cal."' size=10 maxlength=10></td>";
		?>
										<script type="text/javascript">//<![CDATA[
		Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'envFec',button:'envio3',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
		//]]></script>
		<?php

		echo "<td><b><font size='3'>&nbsp;</font></font></td>";
		echo "<td  >FECHA DE RESPUESTA DEL COORDINADOR: <input type='text' readonly='readonly' name='resFec' value='".$resFec."' class=tipo3 ><input type='button' name='envio4' value='...' onclick=".$cal."' size=10 maxlength=10></td>";
		?>
		<script type="text/javascript">//<![CDATA[
		Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'resFec',button:'envio4',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
		//]]></script>
		<?php

		echo "</br></br></br>";


		for($i=0;$i<=$tamano;$i++)
		{
			if ($i==$tamano)
			{
				$senal=$tamano+1;
				echo "<CENTER><b><font size='3'><font color='#00008B'>ACCION NUMERO: ".$senal." </b></font></font></BR></BR>";
				echo "<input type='hidden' name='accNum".$i."' value='$senal' />";
				echo "<input type='hidden' name='accEst".$i."' value='INGRESADO' />";
				echo "<CENTER><b><font size='3'>RESPONSABLE: </b></font><input type='text' name='accRes".$i."' />";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size='3'>FECHA DE IMPLEMENTACION: ";
				echo "AAAA&nbsp;<input type='text'  size='2' name='accAno".$i."' />";
				echo "&nbsp;MM&nbsp;<input type='text'  size='2' name='accMes".$i."'/>";
				echo "&nbsp;DD&nbsp;<input type='text'  size='2' name='accDia".$i."' /></BR></BR>";


				switch ($tipAcc[$i])
				{
					case 'CORRECCION':
					echo "<CENTER><b><font size='3'>TIPO DE ACCION:</b></font>";
					echo "<input type='radio' name='tipAcc".$i."' value='CORRECCION' checked > <font size='2'>CORRECCION&nbsp;";
					echo "<input type='radio' name='tipAcc".$i."' value='CORRECTIVA' > <font size='2'>CORRECTIVA&nbsp;";
					break;
					case 'CORRECTIVA':
					echo "<CENTER><b><font size='3'>VOLVERIA A LA CLINICA:</b></font>";
					echo "<input type='radio' name='tipAcc".$i."' value='CORRECCION'> <font size='2'>CORRECCION&nbsp;";
					echo "<input type='radio' name='tipAcc".$i."' value='CORRECTIVA' checked > <font size='2'>CORRECTIVA&nbsp;";
					break;
				}

				echo "</BR></BR><CENTER><b><font size='3'>DESCRIPCION:</font>	</BR>";
				echo "<textarea rows='4' cols='50' name='accDes".$i."' style='font-family: Arial; font-size:14'></textarea></BR></BR>";

			}else
			{

				echo "<CENTER><b><font size='3'><font color='#00008B'>ACCION NUMERO: ".$accNum[$i]." </b></font></font></BR></BR>";
				echo "<input type='hidden' name='accNum".$i."' value='".$accNum[$i]."' />";
				echo "<input type='hidden' name='accEst".$i."' value='INGRESADO' />";
				echo "<CENTER><b><font size='3'>RESPONSABLE: </b></font><input type='text' name='accRes".$i."' value='".$accRes[$i]."'/>";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size='3'>FECHA VERIFICACION: ";
				echo "AAAA&nbsp;<input type='text'  size='2' name='accAno".$i."' value = '".$accAno[$i]."'/>";
				echo "&nbsp;MM&nbsp;<input type='text'  size='2' name='accMes".$i."' value = '".$accMes[$i]."'/>";
				echo "&nbsp;DD&nbsp;<input type='text'  size='2' name='accDia".$i."' value = '".$accDia[$i]."'/></BR></BR>";
				switch ($tipAcc[$i])
				{
					case 'CORRECCION':
					echo "<CENTER><b><font size='3'>TIPO DE ACCION:</b></font>";
					echo "<input type='radio' name='tipAcc".$i."' value='CORRECCION' checked > <font size='2'>CORRECCION&nbsp;";
					echo "<input type='radio' name='tipAcc".$i."' value='CORRECTIVA' > <font size='2'>CORRECTIVA&nbsp;";
					break;
					case 'CORRECTIVA':
					echo "<CENTER><b><font size='3'>VOLVERIA A LA CLINICA:</b></font>";
					echo "<input type='radio' name='tipAcc".$i."' value='CORRECCION'> <font size='2'>CORRECCION&nbsp;";
					echo "<input type='radio' name='tipAcc".$i."' value='CORRECTIVA' checked > <font size='2'>CORRECTIVA&nbsp;";
					break;
				}

				echo "</BR></BR><CENTER><b><font size='3'>DESCRIPCION:</font>	</BR>";
				echo "<textarea rows='4' cols='50' name='accDes".$i."' style='font-family: Arial; font-size:14'>".$accDes[$i]."</textarea></BR></BR>";
			}

		}


		echo "<input type='hidden' name='tamano' value='$tamano' />";
		echo "<input type='hidden' name='idCom' value='$idCom' />";
		echo "<input type='hidden' name='idMot' value='$idMot' />";
		echo "<input type='hidden' name='lugOri' value='$lugOri' />";
		echo "<input type='hidden' name='fecOri' value='$fecOri' />";
		echo "<input type='hidden' name='motEnv' value='$motEnv' />";
		echo "<input type='hidden' name='motTip' value='$motTip' />";
		echo "<input type='hidden' name='motCla' value='$motCla' />";
		echo "<input type='hidden' name='motDes' value='$motDes' />";
		echo "<input type='hidden' name='motCon' value='$motCon' />";
		echo "<input type='hidden' name='motAre' value='$motAre' />";
		echo "<input type='hidden' name='motCoo' value='$motCoo' />";
		echo "<input type='hidden' name='motNum' value='$motNum' />";
		for($i=0;$i<count($motImp);$i++)
		{
			echo "<input type='hidden' name='motImp[".$i."]' value='".$motImp[$i]."' />";
			echo "<input type='hidden' name='motCar[".$i."]' value='".$motCar[$i]."' />";
		}
		echo "<input type='hidden' name='bandera1' value='1' />";
		echo "<input type='hidden' name='motEst' value='$motEst' />";
		echo "<input type='hidden' name='entidad' value='$entidad' />";
		echo "<input type='hidden' name='motCau' value='$motCau' />";
		echo "<input type='hidden' name='nombre' value='$nombre' />";
		echo "<input type='hidden' name='cedula' value='$cedula' />";
		echo "<input type='hidden' name='his' value='$his' />";
		echo "<input type='hidden' name='numCom' value='$numCom' />";

		echo "<td align=center colspan=14><font size='2'  align=center face='arial'><input type='checkbox' name='envio' value='on'/>ENVIAR</td>&nbsp;&nbsp;";

		if ($motEst=='INVESTIGADO' or $motEst=='CERRADO' or $motEst=='RESPONDIDO' or $motEst=='VERIFICADO')
		{
			echo '<script language="Javascript">';
			echo "activar();";
			echo '</script>';
		}

		echo "<input type='submit'  name='GUARDAR' value='GUARDAR' onclick='javascript:valorar()'/></CENTER></BR>";

		echo "</FORM>";
}

/*echo "<center>";

if ($motEst=='INVESTIGADO')
echo " <a href='verificacion.php' align='right'>Verificacion de acciones-->></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

echo "</center>";*/

}
?>

</body>

</html>




