<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<title>Programa de comentarios y sugerencias</title>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>	
<script src="efecto.php"></script>
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
</script>

<SCRIPT LANGUAGE="JavaScript1.2">
<!--
function onLoad() {
	loadMenus();
}
//-->
</SCRIPT>

<script language="javascript" >
	$(document).ready(function() {

		$("#fRes").datepicker({
	       showOn: "button",
	       buttonImage: "../../images/medical/root/calendar.gif",
	       buttonImageOnly: true,
	       maxDate:"+1D"
	    });
	});  
    function valorar(tamano)
    {
    	for(i = 0; i < tamano; i++)
    	{

    		if ( !document.forma.elements['envio'+i].checked )
    		{
    			document.forma.elements['envio'+i].value='off';
    			document.forma.elements['envio'+i].checked=true;
    		}
    	}
    }

    function Seleccionar(tamano)
    {
    	valorar(tamano);
    	document.forma.submit();
    }

    function activar(j)
    {
    	document.forma.elements['envio'+j].checked=true;
    }

    </script>

</head>
<body>
<?php
include_once("conex.php"); 

/**
 * ASIGNACION DE COMENTARIO
 * 
 * Este programa permite asignar los motivos de un comentario a una unidad especifica, pueden consultarse tambien y corregirse
 * 
 * @name  matrix\magenta\procesos\asignacion.php
 * @author Carolina Castaño Portilla.
 * 
 * @created 2006-04-08
 * @version 2008-01-23
 * 
 * @modified 2006-01-05  Se realiza documentacion del programa y se adapta para que muestre la historia clinica y se simplifica la actualización del estado del comentario y de los motivos
 
 Actualizacion: Se le agrego trim a la variable $motTip que venia con espacios al final desde la base de datos. Viviana Rodas  2012-05-16
 * 
 * @table magenta_000008, select
 * @table magenta_000016, select sobre datos del paciente
 * @table magenta_000017, select, update 
 * @table magenta_000018, select, update
 * @table magenta_000019, select
 * @table magenta_000020, select
 * @table magenta_000021, select
 * @table magenta_000022, select
 * 
 *  @wvar $acoDir, direccion del acompanana
 *  @wvar $acoEma, email del acompanante
 *  @wvar $acoNom, nombre del acompananate
 *  @wvar $acoTel, telefono del acompanante
 *  @wvar $aut, persona que ingreso el comentario
 *  @wvar $bandera1, indica si es la primera vez que se entra al programa o no
 *  @wvar $cadena, se utiliza para armar el menu e ir a la pagina de datos del paciente
 *  @wvar $cadena2, se utiliza para armar el menu e ir a la pagina de comenta del paciente
 *  @wvar $color color para desplegar el tipo de usuario que es el paciente
 *  @wvar $dir direccion del paciente
 *  @wvar $dir2 direccion del paciente codificada para madarla por 
 *  @wvar $doc documento del paciente
 *  @wvar $ema email del paciente
 *  @wvar $email direccion del coordinador para avisarle del 
 *  @wvar $emo contacto emocional
 *  @wvar $ent entidad del paciente
 *  @wvar $envio si se va a asignar el motivo o no (checkbox)
 *  @wvar $est estado del comentario para saber si poner el hipervinculo respuesta
 *  @wvar $fecCon fecha del contacto
 *  @wvar $fecOri fecha de origen del comentario
 *  @wvar $fecRec fecha de recepcion del comentario
 *  @wvar $his historia clinica del paciente
 *  @wvar $idArea id del area del motivo en tabla 000019
 *  @wvar $idCargo id del cargo del implicado en tabla 000022
 *  @wvar $idCom id del comentario en tabla 000017
 *  @wvar $idMot ide del motivo en tabla 000018
 *  @wvar $idPac id del paciente en tabla 000016
 *  @wvar $indicador, variable que si es 1 indica que hay que mandar un email de aviso
 *  @wvar $inicial, indica si los datos ingresado no cumplen con las validaciones cuando esta en uno
 *  @wvar $inicial1, variable usada en cadenas y ciclos
 *  @wvar $inicial2, variable usada en cadenas y ciclos
 *  @wvar $inv variable que sirve para acomodar la investigacion cuando se asigna a otro coordinador adicional del que que ya habia escrito algo
 *  @wvar $lugOri lugar de origen del comentario
 *  @wvar $mail nombre de la clase encargada de enviar el mail
 *  @wvar $motAre, area a la que se asigno el comentario
 *  @wvar $motAreS, lista de areas para el dropdown
 *  @wvar $motAreT, cuadro de texto para ingresar parte del area a asignar
 *  @wvar $motCar, cargo del implicado
 *  @wvar $motCarS, lista de cargos para el dropdown
 *  @wvar $motCarT, cuadro de texto para ingresar parte del cargo del implicado
 *  @wvar $motCla vector de clasificaciones de los motivos
 *  @wvar $motCon datos del contacto
 *  @wvar $motCoo coordinadores de los motivos
 *  @wvar $motDes vector de descripciones de los motivos
 *  @wvar $motEnv vector de las fechas de envio de los motivos
 *  @wvar $motEst vector de estados de los motivos
 *  @wvar $motImp vector de los implicados de los motivos
 *  @wvar $motNum vector de numero de los motivos
 *  @wvar $motTip vector de tipo de los motivos
 *  @wvar $perDil persona que dilignecio el comentario
 *  @wvar $priApe primer apellido del paciente
 *  @wvar $priNom primer nombre del paciente
 *  @wvar $resulMai, indica que sucedio al enviar el email on aviso del 
 *  @wvar $segApe segundo apellido del paciente
 *  @wvar $segNom segundo nombre del paciente
 *  @wvar $semaforo, composicion entre el borde y relleno del color del semaforo del motivo
 *  @wvar $semaforoB, color del borde del semaforo para el motivo
 *  @wvar $semaforoR, color de relleno del semaforo para el motivo
 *  @wvar $tamano cantidad de motivos del comentario
 *  @wvar $tel telefono del paciente
 *  @wvar $tipDoc tipo de documento del paciente
 *  @wvar $tipUsu tipo de usuario para afinidad (AAA, BBB, VIP, no clasificado)
 *  @wvar $vol volveria o no a la clinica
*/
/************************************************************************************************************************* 
  Actualizaciones:
 			2016-05-02 (Arleyda Insignares C.)
 					Se Modifica solo diseño: Titulos, Encabezado del script.

*************************************************************************************************************************/

$wautor="Carolina Castano P.";
$wversion='2008-01-23';
$wactualiz='2016-05-03';

include_once("root/comun.php");

/////////////////////////////////////////////////encabezado general///////////////////////////////////
$titulo = "SISTEMA DE COMENTARIOS Y SUGERENCIAS";

// Se muestra el encabezado del programa
encabezado($titulo,$wactualiz, "clinica");  


/********************************funciones************************************/
/**
 * no se ha encontrado el comentario para el id dado
 *
 */
function  DisplayError1()
{
	echo '<script language="Javascript">';
	echo 'alert ("NO SE HA ENCONTRADO EL COMENTARIO DADO, EN LA BASE DE DATOS, AVISE A SISTEMAS")';
	echo '</script>';

}

/**
 * No se ha encontrado el paciente asigando al comentario
 *
 */
function  DisplayError2()
{
	echo '<script language="Javascript">';
	echo 'alert ("NO SE HA ENCONTRADO EL PACIENTE ASIGNADO AL COMENTARIO, EN LA BASE DE DATOS, AVISE A SISTEMAS")';
	echo '</script>';

}

/**
 * No se han encontrado motivos para el comentario dado
 *
 */
function  DisplayError3()
{
	echo '<script language="Javascript">';
	echo 'alert ("NO SE HAN ENCONTRADO MOTIVOS ASOCIADOS A COMENTARIO, AVISE A SISTEMAS")';
	echo '</script>';

}

/**
 * No se ha encontrado el coordinador de una unidad
 *
 */
function  DisplayError4()
{
	echo '<script language="Javascript">';
	echo 'alert ("SE HAN ENCONTRADO INCOSISTENCIAS ENTRE LAS UNIDADES Y SUS COORDINADORES, AVISE A SISTEMAS")';
	echo '</script>';

}

/**
 * No se ha encontrado algun dato de un motivo del comentario
 *
 */
function  DisplayError5()
{
	echo '<script language="Javascript">';
	echo 'alert ("SE HAN ENCONTRADO INCOSISTENCIAS EN LA INFORMACION DE LOS MOTIVOS DEL COMENTARIO")';
	echo '</script>';

}

/*Si se ha invocado la pagina sin enviar un id de comentario*/
function  DisplayError()
{
	echo '<script language="Javascript">';
	echo 'alert ("NO SE HA SUMINISTRADO UN IDENTIFICADOR PARA EL COMENTARIO, AVISE A SISTEMAS")';
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
	$contador=0;

	/**
	 * clase encargada de realizar el envio de correo
	 * se invoca de una libreria instalada en el c
	 */
	require("class.phpmailer.php");
	$mail = new PHPMailer();

	$mail->SMTPAuth = true;							//Indica que se requiere Autenticación

	$mail->Username = "magenta";					//Indica el nombre del usuario con el que se realiza la Autenticación
	$mail->Password = "servmagenta";

	/////////////////////////////////////////////////inicialización de variables//////////////////////////

	//deben mandarme el id del comentario, con eso busco los datos del paciente y los datos del comentario
	if (isset ($idCom))
	{
		//consulto el comentario
		$query ="SELECT id_persona, ccoori, ccofori, ccofrec, cconaco, ccotusu, ccoatel, ccoadir, ccoaema, ccoaut, ccocemo, ccoent, ccovol, ccocfec, ccoest, ccohis, cconum FROM " .$empresa."_000017 where id=".$idCom." ";
		$err=mysql_query($query,$conex);
		$num=mysql_num_rows($err);
		//echo $query;
		if  ($num >0) //se llena los valores
		{
			$row=mysql_fetch_row($err);
			$idPac=$row[0];
			$lugOri=$row[1];
			$fecOri=$row[2];
			$fecRec=$row[3];
			$ent=$row[9];
			$perDil=$row[5];
			$acoNom=$row[4];
			$acoDir=$row[7];
			$acoTel=$row[6];
			$acoEma=$row[8];
			$aut=$row[9];
			$emo=$row[10];
			$ent=$row[11];
			$vol=$row[12];
			$fecCon=$row[13];
			$est=$row[14];
			$his=$row[15];
			$numCom=$row[16];

			//busco los datos del paciente
			$exp=explode('-',$idPac);
			if(isset($exp[3]))
			{
				$exp[0]=$exp[0].'-'.$exp[1];
				$exp[1]=$exp[2];
				$exp[2]=$exp[3];
			}
			$query ="SELECT cpedoc, cpetdoc, cpeno1, cpeno2, cpeap1, cpeap2, cpedir, cpetel, cpeema FROM " .$empresa."_000016 where cpedoc='".$exp[0]."' and cpetdoc='".$exp[1]."-".$exp[2]."' ";

			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);
			//echo $query;
			if  ($num >0) //se llena los valores
			{
				$row=mysql_fetch_row($err);
				$doc=$row[0];
				$tipDoc=$row[1];
				$priNom=$row[2];
				$segNom=$row[3];
				$priApe=$row[4];
				$segApe=$row[5];
				$dir=$row[6];
				$tel=$row[7];
				$ema=$row[8];
			}else
			{
				DisplayError2();
			}

			//busco los datos de los motivos para el comentario
			$query ="SELECT id_Comentario, cmonum, cmotip, cmodes, cmocau, cmoest, cmocon FROM " .$empresa."_000018 where id_comentario=".$idCom." ";
			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);
			//echo $query;
			if  ($num >0) //se llena los valores
			{
				$tamano = $num;

				for($i=0;$i<$num;$i++)
				{
					$row=mysql_fetch_row($err);
					$idMot[$i]=$row[0];
					$motNum[$i]=$row[1];
					$motTip[$i]=$row[2];
					$motDes[$i]=$row[3];
					$motCla[$i]=$row[4];
					$motEst[$i]=$row[5];
					$motCon[$i]=$row[6];
					$envio[$i]='on';

				}

			}else
			{
				//en caso de que no se encuentre ningun motivo para el comentario
				DisplayError3();
			}
		}else
		{
			//en caso de que el comentario no sea encontrado para el id

			DisplayError1();
		}

	}else
	{
		//en caso de que no se pase el id como parametro
		DisplayError();
	}



	///////////////////////////////////////////acciones que dependen si es antes o despues del submit////////////////////////////////////////////////////////

	for($i=0;$i<$tamano;$i++) //recorro los motivos actualizando la asignación si es necesario
	{
		if ($motEst[$i]!='INGRESADO' and !isset ($bandera1))//ya ha sido asignado el motivo y es la primera vez que me meto
		{

			$query ="SELECT A.cmofenv, '', A.id_area, '',  B.carcod, B.carnom, C.id_responsable, D.crecod, D.crenom ";
			$query= $query. "FROM " .$empresa."_000018 A, " .$empresa."_000019 B, " .$empresa."_000020 C, " .$empresa."_000021 D ";
			$query= $query. "where A.id_Comentario=".$idMot[$i]." ";
			$query= $query. "and   A.cmonum=".$motNum[$i]." ";
			$query= $query. "and B.Carcod=A.id_area ";
			$query= $query. "and C.id_area=A.id_area and carniv=1 ";
			$query= $query. "and D.Crecod=C.id_responsable ";
			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);
			//echo $query;
			if  ($num >0) //se llena los valores
			{

				$row=mysql_fetch_row($err);
				$motEnv[$i]=$row[0];
				$motAre[$i]=$row[4]."-".$row[5];
				$motCoo[$i]=$row[7]."-".$row[8];
				$motAre[$i]=$row[4]."-".$row[5];
				$envio[$i]='on'; //variable que lleva el valor del check box
			}else
			{
				DisplayError5();
			}


			$query ="SELECT Impnom, Impcca, Ccanom ";
			$query= $query. "FROM " .$empresa."_000027 A, " .$empresa."_000022 B ";
			$query= $query. "where Impnco=".$idMot[$i]." ";
			$query= $query. "and   Impnmo=".$motNum[$i]." ";
			$query= $query. "and Impcca=Ccacod ";

			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);

			for($j=0;$j<$num;$j++) //recorro los motivos actualizando la asignación si es necesario
			{
				$row=mysql_fetch_row($err);
				$motImp[$i][$j]=$row[0];
				$motCar[$i][$j]=$row[1].'-'.$row[2];
				$motCarT[$i][$j]='';
				$motImpT[$i][$j]='';
			}
			$contador++;
		}else
		{
			if (!isset ($bandera1))//es la primera vez que ingreso, y el motivo esta apenas ingresado
			{
				$motEnv[$i]='Sin enviar';
				$motImp[$i][0]='';
				$motCar[$i][0]='';
				$motAre[$i]='';
				$motCoo[$i]='';
				$envio[$i]='off'; //variable que lleva el valor del check box
			}else // segunda vez que me meto, no importa el estado del motivo
			{
				//valido los datos ingresados

				$inicial=0; //me indicara cuando no se cumple alguna validacion, para sacar un mensaje de alerta

				eval("\$motEnv[$i] = \$motEnv$i;");
				eval("\$motAre[$i] = \$motAre$i;");
				eval("\$motAreT[$i] = \$motAreT$i;");
				eval("\$motCoo[$i] = \$motCoo$i;");
				eval("\$envio[$i] = \$envio$i;");
				eval("\$motNum[$i] = \$motNum$i;");


				//validaciones e ingreso de datos

				if ($envio[$i]=='on' ) //se realiza almacenamiento y envio de emails porque el checkbox esta en on
				{
					//primero validamos que se halla seleccionado un area
					//echo $inicial;

					if (($motAre[$i] == '' or $motAre[$i] == '- -') and $inicial != '1')
					{
						echo '<script language="Javascript">';
						echo 'alert ("Debe seleccionar primero la unidad para cada uno de los motivos")';
						echo '</script>';
						$inicial=1;
						$envio[$i]='off';
					}

					if ($inicial != 1) //se pasaron las validaciones
					{
						//Doy valor a la fecha de envío
						$motEnv[$i] = date ('Y-m-d');
						$inicial= explode ('-', $motAre[$i]);
						$idArea=$inicial[0];

						$indicador=0; //indica si hay necesidad de mandar un nuevo email de aviso

						if ($motEst[$i]!='INGRESADO')//miramos si el id del area a cambiado para alguno que ya había sido asignado
						{
							for($j=0;$j<count($motImp[$i]);$j++) //recorro los motivos actualizando los implicados
							{
								$inicial= explode ('-', $motCar[$i][$j]);
								$idCargo=$inicial[0];

								//si el implicado de esta diferente los actualizamos
								$query ="SELECT Impnom FROM " .$empresa."_000027 where Impnco=".$idMot[$i]." and  Impnmo=".$motNum[$i]." and Impcod=".$j." ";
								$err2=mysql_query($query,$conex);
								$num2=mysql_num_rows($err2);
								//echo $query;
								if  ($num2 >0) //se llena los valores
								{
									$row=mysql_fetch_row($err2);
									$id2=$row[0];
									if ($id2!=$motImp[$i])
									{
										$query="update " .$empresa."_000027 set Impnom='".$motImp[$i][$j]."' ";
										$query=$query." where Impnco=".$idMot[$i]." and  Impnmo=".$motNum[$i]." and Impcod=".$j." ";

										$err=mysql_query($query,$conex);
									}

									//si el cargo de esta diferente los actualizamos
									$query ="SELECT Impcca FROM " .$empresa."_000027 where Impnco=".$idMot[$i]." and  Impnmo=".$motNum[$i]." and Impcod=".$j." ";
									$err=mysql_query($query,$conex);
									$row=mysql_fetch_row($err);
									$id2=$row[0];
									if ($id2!=$idCargo)
									{
										$query="update " .$empresa."_000027 set Impcca='".$idCargo."' ";
										$query=$query." where Impnco=".$idMot[$i]." and  Impnmo=".$motNum[$i]." and Impcod=".$j." ";

										$err=mysql_query($query,$conex);
									}
								}
								else
								{
									$query= " INSERT INTO  " .$empresa."_000027 (medico, Fecha_data, Hora_data, impnco, impnmo, impcod, impnom, impcca, seguridad)";
									$query= $query. "VALUES ('magenta','".date("Y-m-d")."','".date("h:i:s")."',".$idMot[$i].", ".$motNum[$i].",'".$j."', '".$motImp[$i][$j]."','".$idCargo."','A-magenta') ";

									$err=mysql_query($query,$conex);
								}
							}

							//buscamos si el area cambio
							$query ="SELECT id_area FROM " .$empresa."_000018 where id_Comentario=".$idMot[$i]." and Cmonum=".$motNum[$i]." ";
							$err=mysql_query($query,$conex);
							$row=mysql_fetch_row($err);
							$id2=$row[0];
							if ($id2!=$idArea)
							$indicador=1;

						}else
						{
							for($j=0;$j<count($motImp[$i]);$j++) //recorro los motivos insertando los implicados
							{
								$inicial= explode ('-', $motCar[$i][$j]);
								$idCargo=$inicial[0];

								$query= " INSERT INTO  " .$empresa."_000027 (medico, Fecha_data, Hora_data, impnco, impnmo, impcod, impnom, impcca, seguridad)";
								$query= $query. "VALUES ('magenta','".date("Y-m-d")."','".date("h:i:s")."',".$idMot[$i].", ".$motNum[$i].",'".$j."', '".$motImp[$i][$j]."','".$idCargo."','A-magenta') ";
								//echo $query;
								$err=mysql_query($query,$conex);
							}


							$indicador=1;
						}

						if ($indicador==1)
						{
							//Mandar email
							//Busco el email del responsable
							$inicial= explode ('-', $motCoo[$i]);
							$query ="SELECT cremai FROM " .$empresa."_000021 where crecod='".$inicial[0]."' and crenom='".$inicial[1]."' ";
							$err=mysql_query($query,$conex);
							$num=mysql_num_rows($err);
							//echo $query;
							if  ($num >0) //se llena los valores
							{
								$row=mysql_fetch_row($err);
								$email=$row[0];
								//echo $email;

							}else
							{
								$email='ccastano@pmamericas.com';
							}
							//echo $email;

							if (trim($motTip[$i])=='Desagrado')
							$resulMai=0;
							else
							$resulMai=3;

							//solo se envian correos a comentarios que son de desagrado
							if ($resulMai==0)
							{

								$mail->IsSMTP(); // telling the class to use SMTP
								$mail->Host = "132.1.18.1"; // SMTP server
								$mail->From = "magenta@lasamericas.com.co";

								$mail->AddAddress($email);

								$mail->Subject = "Nuevo Comentario";
								$mail->Body = "Cordial Saludo ! \n\n Hoy ha recibido un nuevo comentario (".$numCom."-".$motNum[$i].") en su portal de COMENTARIOS Y SUGERENCIAS, lo invitamos a tramitarlo!";
								$mail->WordWrap = 100;
								if(!@$mail->Send())
								{
									$resulMai=1;
								}
								else
								{
									$resulMai=2;
								}
							}
							$mail->ClearAddresses();
							$contador++; //indica que hay un nuevo comentario asignado
	
							if ($motEst[$i]!='CERRADO' and $motEst[$i]!='INVESTIGADO')
							{
								//Se almacena en base de datos la asignacion del nuevo contacto
								if ( trim($motTip[$i])=='Desagrado')
								{
									$query="update " .$empresa."_000018 set cmofenv='".$motEnv[$i]."', id_area='".$idArea."', cmoest='ASIGNADO', cmoinv='' ";
									$query=$query."where id_comentario=".$idCom." and cmonum=".$motNum[$i]." ";
									//echo $query;
									$motEst[$i] = 'ASIGNADO';

								}else
								{
									$query="update " .$empresa."_000018 set cmofenv='".$motEnv[$i]."', id_area='".$idArea."', cmoest='INVESTIGADO' ";
									$query=$query."where id_comentario=".$idCom." and cmonum=".$motNum[$i]." ";
									//echo $query;
									$motEst[$i] = 'INVESTIGADO';
								}
								$err=mysql_query($query,$conex);

								//Cambio el valor al estado del comentario

							}
							else
							{
								//Se almacena en base de datos la asignacion del nuevo contacto
								if (trim($motTip[$i])=='Desagrado')
								{
									//primero debo coger la investigacion del motivo y de acuerdo con eso completarla con los datos del cambio
									$query ="SELECT A.cmoinv, A.cmofret, B.carnom, D.crenom  ";
									$query= $query. "FROM " .$empresa."_000018 A, " .$empresa."_000019 B, " .$empresa."_000020 C, " .$empresa."_000021 D ";
									$query= $query. "where A.id_comentario=".$idCom." ";
									$query= $query. "and A.cmonum=".$motNum[$i]." ";
									$query= $query. "and B.Carcod=A.id_area ";
									$query= $query. "and C.id_area=A.id_area and carniv=1 ";
									$query= $query. "and D.Crecod=C.id_responsable ";


									$err=mysql_query($query,$conex);
									$row=mysql_fetch_row($err);

									$inv=$row[0].' Respondido por: '.$row[3]. ' Coordinador de: '.$row[2].' el:'.$row[1];

									$query="update " .$empresa."_000018 set cmofenv='".$motEnv[$i]."', id_area='".$idArea."', cmoest='ASIGNADO', cmoinv='".$inv."' ";
									$query=$query."where id_comentario=".$idCom." and cmonum=".$motNum[$i]." ";
									//echo $query;

									$motEst[$i] = 'ASIGNADO';

								}else
								{
									$query="update " .$empresa."_000018 set cmofenv='".$motEnv[$i]."', id_area='".$idArea."', cmoest='".$motEst[$i]."' ";
									$query=$query."where id_comentario=".$idCom." and cmonum=".$motNum[$i]." ";
									//echo $query;
								}
								$err=mysql_query($query,$conex);

								//Cambio el valor al estado del comentario

							}
						}
					}

				}

			}
		}

	}

	///////////////////////////////////////////acciones que dependen si es antes o despues del submit////////////////////////////////////////////

	//Busco en base de datos de afinidad el tipo de Usuario de la persona, para mostrar
	$query ="SELECT clitip FROM " .$empresa."_000008 where clidoc='$doc' and clitid='$tipDoc' ";
	$err=mysql_query($query,$conex);
	$num=mysql_num_rows($err);
	//echo $query;
	if  ($num >0)
	{
		$row=mysql_fetch_row($err);

		$inicial = explode ('-',$row[0]);
		if ($inicial[2] == '1' and $inicial[0] != 'VIP')
		$color='EA198E';
		else
		$color='0000FF';

		if ($inicial[0] == 'VIP')
		$tipUsu=$inicial[0];
		else
		$tipUsu=$inicial[1];
	}else
	{
		$tipUsu='NO CLASIFICADO';
		$color='0000FF';
	}


	// inicialización de selecciones
	for($i=0;$i<$tamano;$i++)
	{
		if ( !isset ($motAreT[$i]) or $motAreT[$i] == ""  )
		{
			/*No se buscara o  no se establecera una selección de opciones para areas*/
			$motAreT[$i]="$%&@#";
		}

		//en este caso se ha seleccionado un area del drop down, hay que buscar el coordinador del area
		if (isset ($motAre[$i]) and $motAre[$i]!= '' and $motAre[$i]!= '- -' and $motAreT[$i]=="$%&@#")
		{
			$motAreS[$i]="<option>".$motAre[$i]."</option>";

			$inicial1=explode('-',$motAre[$i]);
			//Buscamos el coordinador del area
			$query ="SELECT A.id, B.id_responsable, C.crecod, C.crenom ";
			$query= $query. "FROM  " .$empresa."_000019 A, " .$empresa."_000020 B, " .$empresa."_000021 C ";
			$query= $query. "where A.carcod='".$inicial1[0]."' and A.carnom='".$inicial1[1]."' ";
			$query= $query. "and B.id_area=A.Carcod ";
			$query= $query. "and C.Crecod=B.id_responsable and creest='on' and relest='on' ";

			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);
			//echo $query;
			if  ($num >0) //se llena los valores
			{
				$row=mysql_fetch_row($err);
				$motCoo[$i]=$row[2]."-".$row[3];
			}else
			{
				DisplayError4();
			}

		}

		//Se ha ingresado parte del nombre de una unidad
		if ( $motAreT[$i] != "$%&@#")
		{
			//	ECHO 'MEMETO';
			//Basados en $motCarT buscar el cargo en la tabla de areas
			$query="select carcod, carnom   FROM " .$empresa."_000019 where carnom like '%".strtoupper($motAreT[$i])."%' and carsem>0 and carest='on'  ";
			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);

			if  ($num >0)
			{
				$motAreS[$i]="";
				for($j=0;$j<$num;$j++)
				{
					$row=mysql_fetch_row($err);
					$motAreS[$i]=$motAreS[$i]."<option>".$row[0]."-".$row[1]."</option>";

					if ($j==0)
					{
						$inicial1=$row[0];
						$inicial2=$row[1];
					}
				}

				//Buscamos el coordinador del area
				$query ="SELECT A.id, B.id_responsable, C.crecod, C.crenom ";
				$query= $query. "FROM  " .$empresa."_000019 A, " .$empresa."_000020 B, " .$empresa."_000021 C ";
				$query= $query. "where A.carcod='".$inicial1."' and A.carnom='".$inicial2."' ";
				$query= $query. "and B.id_area=A.Carcod ";
				$query= $query. "and C.Crecod=B.id_responsable";

				$err=mysql_query($query,$conex);
				$num=mysql_num_rows($err);
				//echo $query;
				if  ($num >0) //se llena los valores
				{
					$row=mysql_fetch_row($err);
					$motCoo[$i]=$row[2]."-".$row[3];
				}else
				{
					DisplayError();
				}

			}

			$motAreT[$i]="$%&@#";
		}

		if (!isset ($motAreS[$i]) or $motAreS[$i] == '' or $motAreS[$i] == '- -' )
		{
			$motAreS[$i]="<option>- -</option>";
			$motCoo[$i]='- -';
			$motAreT[$i]="$%&@#";
		}

		if ( !isset ($motCarT[$i][0]))
		{

			$motCarT[$i][0]="$%&@#";
			$motImpT[$i][0]="$%&@#";

			$motCarS[$i][0]="<option>999-SIN CARGO</option>";

			$motImpS[$i][0]="<option>- -</option>";

		}
		else
		{
			for($j=0;$j<count($motCarT[$i]);$j++)
			{
				if ($motCarT[$i][$j] == "" )
				{
					/*No se buscara o  no se establecera una selección de opciones para cargos*/
					$motCarT[$i][$j]="$%&@#";
				}

				//en este caso ya se ha seleccionado el cargo en el drop down
				if (isset ($motCar[$i][$j]) and $motCar[$i][$j]!='' and $motCar[$i][$j]!= '999-SIN CARGO' and $motCarT[$i][$j]=="$%&@#")
				{
					$motCarS[$i][$j]="<option>".$motCar[$i][$j]."</option>";
					$motCarT[$i][$j]="$%&@#";

				}
				//en este caso se ha introducido parte del cargo en el cuadrito del lado del dropdown
				if ( $motCarT[$i][$j] != "$%&@#")
				{
					//Basados en $motCarT buscar el cargo en la tabla de cargos
					$query="select ccacod, ccanom  FROM " .$empresa."_000022 where ccanom like '%".strtoupper($motCarT[$i][$j])."%' and ccaest='on' ";
					$err=mysql_query($query,$conex);
					$num=mysql_num_rows($err);

					if  ($num >0)
					{
						$motCarS[$i][$j]="";
						for($k=0;$k<$num;$k++)
						{
							$row=mysql_fetch_row($err);
							$motCarS[$i][$j]=$motCarS[$i][$j]."<option>".$row[0]."-".$row[1]."</option>";
						}

					}

					$motCarT[$i][$j]="$%&@#";
				}

				if ($motImpT[$i][$j] == ""  )
				{
					/*No se buscara o  no se establecera una selección de opciones para areas*/
					$motImpT[$i][$j]="$%&@#";
				}


				if (isset ($motImp[$i][$j]) and $motImp[$i][$j]!= '' and $motImp[$i][$j]!= '- -' and $motImpT[$i][$j]=="$%&@#")
				{
					$motImpS[$i][$j]="<option>".$motImp[$i][$j]."</option>";

					if(!isset($motCarT[$i][$j+1]))
					{
						$motCarT[$i][$j+1]="$%&@#";
						$motImpT[$i][$j+1]="$%&@#";

						$motCarS[$i][$j+1]="<option>999-SIN CARGO</option>";

						$motImpS[$i][$j+1]="<option>- -</option>";
					}


				}

				//Se ha ingresado parte del nombre de el medico
				if ( $motImpT[$i][$j] != "$%&@#")
				{
					//	ECHO 'MEMETO';
					//Basados en $motCarT buscar el cargo en la tabla de areas

					$q = " SELECT detapl, detval, empdes "
					. "   FROM root_000050, root_000051 "
					. "  WHERE empcod = '01'"
					. "    AND empest = 'on' "
					. "    AND empcod = detemp ";
					$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					$num = mysql_num_rows($res);

					if ($num > 0)
					{
						for ($g = 1;$g <= $num;$g++)
						{
							$row = mysql_fetch_array($res);

							if ($row[0] == "medicos")
							$wmedicos = $row[1];
						}
					}

					if (isset ($wmedicos))
					{
						$query="select Nombres_y_apellidos FROM ".$wmedicos." where Nombres_y_apellidos like '%".strtoupper($motImpT[$i][$j])."%' order by Nombres_y_apellidos ";

						$err=mysql_query($query,$conex);
						$num=mysql_num_rows($err);

						if  ($num >0)
						{
							$motImpS[$i][$j]="";
							for($k=0;$k<$num;$k++)
							{
								$row=mysql_fetch_row($err);
								$motImpS[$i][$j]=$motImpS[$i][$j]."<option>".$row[0]."</option>";

								if ($k==0)
								{
									$iniciali1=$row[0];
								}
							}
						}
					}

					$query="select descripcion  FROM usuarios where descripcion like '%".strtoupper($motImpT[$i][$j])."%' order by descripcion";

					$err=mysql_query($query,$conex);
					$num=mysql_num_rows($err);

					if  ($num >0)
					{
						if (!isset($wmedicos))
						{
							$motImpS[$i][$j]="";
						}
						for($k=0;$k<$num;$k++)
						{
							$row=mysql_fetch_row($err);
							$motImpS[$i][$j]=$motImpS[$i][$j]."<option>".$row[0]."</option>";

							if ($k==0 and !isset($wmedicos))
							{
								$iniciali1=$row[0];
							}
						}
					}


					if(!isset($motCarT[$i][$j+1]))
					{
						$motCarT[$i][$j+1]="$%&@#";
						$motImpT[$i][$j+1]="$%&@#";

						$motCarS[$i][$j+1]="<option>999-SIN CARGO</option>";

						$motImpS[$i][$j+1]="<option>- -</option>";
					}

					$motImpT[$i][$j]="$%&@#";
				}



				if (!isset ($motCarS[$i][$j]) or $motCarS[$i][$j] == '' or $motCarS[$i][$j] == '999-SIN CARGO' )
				{
					$motCarS[$i][$j]="<option>999-SIN CARGO</option>";
					$motCarT[$i][$j]="$%&@#";
				}



				if (!isset ($motImpS[$i][$j]) or $motImpS[$i][$j] == '' or $motImpS[$i][$j] == '- -' )
				{
					$motImpS[$i][$j]="<option>- -</option>";
					$motImpT[$i][$j]="$%&@#";
				}
			}
		}
	}

	//actualizacion o revision del estado del comentario

	$query ="SELECT id FROM " .$empresa."_000018 where id_comentario=".$idCom." and cmoest='INGRESADO' ";
	$err=mysql_query($query,$conex);
	$num=mysql_num_rows($err);
	//echo $query;
	if  ($num >0) //se llena los valores
	{
		$query="update " .$empresa."_000017 set ccoest='INGRESADO' where Ccoid= ".$idCom." ";
		$err=mysql_query($query,$conex);
		$est = 'INGRESADO';
	}
	else
	{
		$query ="SELECT id FROM " .$empresa."_000018 where id_comentario=".$idCom." and cmoest='ASIGNADO' ";
		$err=mysql_query($query,$conex);
		$num=mysql_num_rows($err);
		if  ($num >0) //se llena los valores
		{
			$query="update " .$empresa."_000017 set ccoest='ASIGNADO' where Ccoid= ".$idCom." ";
			$err=mysql_query($query,$conex);
			$est = 'ASIGNADO';
		}
		else
		{
			$query ="SELECT id FROM " .$empresa."_000018 where id_comentario=".$idCom." and cmoest='TRAMITANDO' ";
			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);
			if  ($num >0) //se llena los valores
			{
				$query="update " .$empresa."_000017 set ccoest='ASIGNADO' where Ccoid= ".$idCom." ";
				$err=mysql_query($query,$conex);
				$est = 'ASIGNADO';
			}
			else
			{
				$query ="SELECT id FROM " .$empresa."_000018 where id_comentario=".$idCom." and cmoest='INVESTIGADO' ";
				$err=mysql_query($query,$conex);
				$num=mysql_num_rows($err);
				if  ($num >0) //se llena los valores
				{
					$query="update " .$empresa."_000017 set ccoest='INVESTIGADO' where Ccoid= ".$idCom." ";
					$err=mysql_query($query,$conex);
					$est== 'INVESTIGADO';
				}
				else
				{
					$query ="SELECT ccoest FROM " .$empresa."_000017 where Ccoid=".$idCom." ";
					$err=mysql_query($query,$conex);
					$row=mysql_fetch_row($err);
					$est = $row[0];
				}
			}
		}
	}



	/////////////////////////////////////////////////encabezado general///////////////////////////////////


	$dir2=urlencode($dir);
	$cadena="'pagina1.php?matrix=".$doc."-".$tipDoc."&bandera=3&his=-&res=-&ser=-'";
	$cadena2="'comentario.php?doc=".$doc."&tipDoc=".$tipDoc."&priNom=".$priNom."&segNom=".$segNom."&priApe=".$priApe."&segApe=".$segApe."&tel=".$tel."&dir=".$dir2."&ema=".$ema."&his=-&res=-&ser=-'";

	//echo $cadena;
  ?>
  <SCRIPT LANGUAGE="JavaScript1.2">

  if (document.all) {

  	window.myMenu = new Menu();
  	myMenu.addMenuItem("my menu item A");
  	myMenu.addMenuItem("my menu item B");
  	myMenu.addMenuItem("my menu item C");
  	myMenu.addMenuItem("my menu item D");

  	window.mWhite1 = new Menu("White");
  	mWhite1.addMenuItem("Ingreso de Comentarios", "self.window.location='pagina1.php'");
  	mWhite1.addMenuItem("Lista de comentarios", "self.window.location='listaMagenta.php'");
  	<?php
  	echo 'mWhite1.addMenuItem("Datos del paciente", "self.window.location='.$cadena.'");';
  	echo 'mWhite1.addMenuItem("Comentarios del paciente", "self.window.location='.$cadena2.'");';
  	?>
  	mWhite1.bgColor = "#ADD8E6";
  	mWhite1.menuItemBgColor = "white";
  	mWhite1.menuHiliteBgColor = "#336699";

  	myMenu.writeMenus();
  }

	</SCRIPT>
 <?php

 ////////////////////////////////////////////////////////////////encabezado personal///////////////////////////////////////////////////////////////////

 echo "<br></br>";
 echo "<div align='center'><div align='center' class='fila1' style='width:480px;'><font size='4'><b>ASIGNACION DE RESPONSABILIDADES</b></font></div></div></BR>";
 echo "<br></br>";
 //echo "<center><b><font size=\"4\"><A HREF='asignacion.php?idCom=$idCom'><font color=\"#D02090\">ASIGNACION DE RESPONSABILIDADES</font></a></b></font></center>\n" ;
 //echo "<center><b><font size=\"2\"><font color=\"#D02090\"> asignacion.php</font></font></center></br></br></br>\n" ;
 echo "\n" ;

 if (isset ($resulMai) and $resulMai==1)
 {
 	echo "<CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
 	echo "<table align='center' border=0 bordercolor=#000080 width=340 style='border:solid;'>";
 	echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b> NO SE HAN ENVIADO CORRECTAMENTE LOS EMAIL ". $mail->ErrorInfo."</td><tr>";
 	echo "</table></fieldset></center></br></br>";
 }else if (isset ($resulMai) and $resulMai==2)
 {
 	echo "<CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
 	echo "<table align='center' border=0 bordercolor=#000080 width=340 style='border:solid;'>";
 	echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>SE HAN ENVIADO CORRECTAMENTE LOS EMAIL </td><tr>";
 	echo "</table></fieldset></center></br></br>";
 }

 if (isset ($resulMai) and $resulMai==3)
 {
 	echo "<CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
 	echo "<table align='center' border=0 bordercolor=#000080 width=340 style='border:solid;'>";
 	echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b> SE HAN ALMACENADO CORRECTAMENTE LOS EMAIL DE AGRADO</td><tr>";
 	echo "</table></fieldset></center></br></br>";
 }
 
 /////////////////////////////////////////////////presentación general///////////////////////////////////

 echo "<fieldset  style='border:solid;border-color:#00008B; width=100%' align='center'>";
 echo "<table align='center'  width='100%'>";
 echo "<tr>";
 echo "<td rowspan=4><b><font size='3'><font color='#00008B'> DATOS DEL PACIENTE:</font></font></td>";
 echo "<td> <b><font size='3'  color='#00008B'>Documento:</font></b>$doc-".substr($tipDoc,0,2)."</td>";

 echo "<td><b><font size='3'  color='#00008B'>Nombre:&nbsp;</font></b>$priNom ";

 if ($segNom != ' ' and $segNom != '' and  $segNom != '- -')
 echo "$segNom ";

 echo "$priApe ";

 if ($segApe != ' ' and $segApe != '' and  $segApe != '- -')
 echo "$segApe";

 echo "</td>";
 echo "</tr>";

 echo "<tr>";
 echo "<td><b><font size='3'  color='#00008B'> Telefono: </font></b>$tel</td>";
 echo "<td><b><font size='3'  color='#00008B'>Dirección: </font></b>$dir</td>";
 echo "</tr>";
 echo "<tr>";

 echo "<td><b><font size='3'  color='#00008B'> Email: </font></b>$ema</td>";
 echo "<td><b><font size='3'  color='#00008B'>AFINIDAD:</font></b> <font color='#$color'>$tipUsu</font></td>";
 echo "</tr>";
 echo "</table></fieldset></br></br></br>";

 if ($perDil == 'Acompanante')
 {
 	echo "<fieldset  style='border:solid;border-color:#00008B; width=100%' align='center'>";
 	echo "<table align='center' width=100%'>";
 	echo "<tr>";
 	echo "<td rowspan=2><b><font size='3'><font color='#00008B'> DATOS DEL ACOMPAÑANTE:</font></font></td>";
 	echo "<td>&nbsp;&nbsp;<b><font size='3' color='#00008B'>Nombre: </font></b>$acoNom</td>";
 	echo "<td><b><font size='3'>&nbsp;</font></font></td>";
 	echo "<td>&nbsp;&nbsp;<b><font size='3' color='#00008B'>Telefono: </font></b>$acoTel</font></td>";
 	echo "<td><b><font size='3'>&nbsp;</font></font></td>";
 	echo "<td><b><font size='3'>&nbsp;</font></font></td>";
 	echo "</tr>";
 	echo "<tr>";

 	echo "<td>&nbsp;&nbsp;<b><font size='3' color='#00008B'>Dirección: </font></b> $acoDir</td>";
 	echo "<td><b><font size='3'>&nbsp;</font></font></td>";
 	echo "<td>&nbsp;&nbsp;<b><font size='3' color='#00008B'> Email: </font></b>$acoEma</td>";
 	echo "<td><b><font size='3'>&nbsp;</font></font></td>";
 	echo "<td><b><font size='3'>&nbsp;</font></font></td>";
 	echo "</tr>";
 	echo "</table></fieldset></br></br>";
 }

 echo "<fieldset  style='border:solid;border-color:#00008B; width=100%' align='center'>";
 echo "<table align='center'  width=100%'>";
 echo "<tr>";
 echo "<td rowspan=2><b><font size='3' ><font color='#00008B'> DATOS COMENTARIO:</font></font></td>";
 echo "<td colspan='1'><b><font size='3' color='#00008B'>Fecha Origen: </font></b>$fecOri</td>";
 echo "<td  colspan='1'> <b><font size='3' color='#00008B'>Lugar Origen: </font></b> $lugOri</td>";

 echo "</tr>";

 echo "<tr>";
 echo "<td><b><font size='3' color='#00008B'> Fecha Rec.: </font></b>$fecRec</td>";
 echo "<td><b><font size='3' color='#00008B'>Entidad:</font></b>$ent</td>";

 echo "</tr>";
 echo "<tr>";
 echo "<td><b><font size='3'>&nbsp;</font></font></td>";
 echo "<td><b><font size='3' color='#00008B'> Volveria: </font></b>$vol</td>";
 echo "<td><b><font size='3' color='#00008B'> Historia Clinica: </font></b>$his</td>";
 echo "</tr>";
 echo "</table></fieldset></br></br></br>";

 echo "<center><b><font size='4'><font color='#00008B'>ASIGNACION DE RESPONSABILIDADES PARA EL COMENTARIO NUMERO: ".$numCom."</b></font></font></center></BR>";
 echo "<center><b><font size='3'><font color='#00008B'>PERSONA QUE INGRESÓ EL COMENTARIO: ".$aut."</b></font></font></center>";

 echo "<form action='asignacion.php' method='post' name='forma'>";

 echo "<CENTER><b><font size='3'><font color='#00008B'>ASIGNACION POR MOTIVO</b></font></font></BR></BR>";

 // pinto cada uno de los motivos
 for($i=0;$i<$tamano;$i++)
 {
 	//consulto el estado de semaforo del motivo
 	if (trim($motTip[$i])=='Desagrado')
 	{
 		$semaforo=semaforo($motEst[$i], $motEnv[$i], $idCom, $empresa, $conex, $motNum[$i]);
 		$inicial = explode("-",$semaforo);
 		$semaforoB=$inicial[0];
 		$semaforoR=$inicial[1];
 	}else
 	{
 		$semaforoB='#008000';
 		$semaforoR='#EOFFFF';
 	}

 	echo "<fieldset  style='border:solid;border-color:".$semaforoB."; width=100%' align='center'>";
 	echo "<table align='center0' bgcolor='".$semaforoR."' width=100%'>";
 	echo "<tr>";
 	echo "<td rowspan=3><b><font size='3'><font color='#00008B'> MOTIVO NUMERO: ".$motNum[$i]."</font></font></td>";
 	echo "<td  colspan='1'><b><font size='3'>TIPO: </font></b> ".$motTip[$i]."</td>";
 	echo "<td colspan='2'><b><font size='3'>CAUSA: </font></b>".$motCla[$i]."</td>";
 	echo "</tr>";
 	echo "<tr>";
 	echo "<td colspan='2'><b><font size='3'>FECHA DE ENVÍO: </font></b>".$motEnv[$i]."</td>";
 	echo "<td><b><font size='3'>&nbsp;</font></font></td>";
 	echo "</tr>";
 	echo "<tr>";
 	echo "<td ><b><font size='3'> DESCRIPCION: </font></b></td>";
 	echo "<td colspan='3'><font size='3'>".$motDes[$i].". ".$motCon[$i]."</font></td>";
 	echo "</tr>";
 	echo "</table></fieldset>";

 	echo "<fieldset  style='border:solid;border-color:".$semaforoB."; width=100%' align='center'>";
 	echo "<table align='center' bgcolor='".$semaforoR."' width=100%'>";


 	echo "<tr>";
 	echo "<td rowspan='".count($motCarT[$i])."' ><b><font size='3'><font color='#00008B'> IMPLICADO:</font></td>";
 	for($j=0;$j<count($motCarT[$i]);$j++)
 	{
 		echo "<td  colspan='3'><b><font size='3'>NOMBRE:</font></b><select name='motImp[".$i."][".$j."]' onChange='Seleccionar(".$tamano.")'>".$motImpS[$i][$j]."</select>";
 		echo "<input type='text' name='motImpT[".$i."][".$j."]' value='".$motImpT[$i][$j]."' size=10 ></td>";

 		echo "<td colspan='2'>&nbsp;&nbsp;<b><font size='3'>CARGO:</font></b><select name='motCar[".$i."][".$j."]' onChange='Seleccionar(".$tamano.")'>".$motCarS[$i][$j]."</select>&nbsp;";
 		echo "<input type='text' name='motCarT[".$i."][".$j."]' value='".$motCarT[$i][$j]."' size=10 ></td>";
 		echo "</tr>";
 	}
 	echo "</tr>";
 	echo "</table></fieldset>";
 	echo "<fieldset  style='border:solid;border-color:".$semaforoB."; width=100%' align='center'>";
 	echo "<table align='center' bgcolor='".$semaforoR."' width=100%'>";
 	echo "<tr>";
 	echo "<td width='20%'><b><font size='3'><font color='#00008B'> AREA:</font></font></td>";
 	echo "<td width='40%' colspan='1'> &nbsp;&nbsp; <b><font size='3'>NOMBRE:</font></b><select name='motAre".$i."' onChange='Seleccionar(".$tamano.")'>".$motAreS[$i]."</select>&nbsp;";
 	echo "<input type='text' name='motAreT".$i."' value='".$motAreT[$i]."' size=10 ></td>";
 	echo "<td width='40%' colspan='2'>&nbsp;&nbsp;<b><font size='3'>COORDINADOR:</font></b>".$motCoo[$i]."</td>";
 	echo "</tr>";
 	echo "<tr>";
 	echo "<td width='20%'><b><font size='3'><font color='#00008B'>&nbsp;&nbsp; </font></font></td>";
 	echo "<td width='40%' colspan='1'> &nbsp;&nbsp; <b><font size='3'>&nbsp;&nbsp;</font></b>";
 	echo "<td colspan='2' align='CENTER'><B>ENVIAR:</B><input type='checkbox' name='envio".$i."' value='on' >&nbsp;&nbsp;&nbsp;&nbsp;</td>";
 	echo "</tr>";
 	echo "</table></fieldset></br></BR>";

 	echo "<input type='hidden' name='motEnv".$i."' value='$motEnv[$i]' />";
 	echo "<input type='hidden' name='motCoo".$i."' value='$motCoo[$i]' />";
 	echo "<input type='hidden' name='motNum".$i."' value='$motNum[$i]' />";

 	if ($envio[$i]=='on')
 	{
 		echo '<script language="Javascript">';
 		echo "activar(".$i.");";
 		echo '</script>';
 	}
 }

 echo "<input type='hidden' name='idCom' value='".$idCom."' />";
 echo "<input type='hidden' name='bandera1' value='1' />";
 echo "<input type='hidden' name='tamano' value='$tamano' />";



 echo "<input type='submit'  name='enviar' value='ENVIAR' onclick='javascript:valorar(".$tamano.")'></CENTER></BR>";
 echo "</FORM>";

 echo "<center>";
 echo "<a href='detalleComentario.php?idCom=".$idCom."' align='right'><<--Ingreso</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

 //Para saber si muestro el siguiente paso que es la respuesta del comentario, deberia de ya haberse investigado
 if ($est == 'INVESTIGADO' or $est == 'CERRADO')
 echo "<a href='respuesta.php?idCom=".$idCom."' align='right'>Respuesta-->></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

 echo "</center></BR>";
}

?>


</body>

</html>




