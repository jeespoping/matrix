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
<script type="text/javascript">
  
</script>    


<SCRIPT LANGUAGE="JavaScript1.2">
<!--
function onLoad() {
	loadMenus();
}
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
	if ( document.forma.camb.checked )
	{
		document.forma.camb.value='on';
		document.forma.camb.checked=true;
	}

	if ( !document.forma.camb.checked )
	{
		document.forma.camb.value='off';
		document.forma.camb.checked=false;
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
 * 	CIERRE DE COMENTARIO
 * 
 * Este programa permite cerrar un comentario, cerrando primero todos los motivos de este
 * 
 * @name  matrix\magenta\procesos\respuesta.php
 * @author Carolina Castaño Portilla.
 * 
 * @created 2006-04-16
 * @version 2006-01-05
 * 
 * @modified 2006-01-05  Se realiza documentacion del programa y se adapta para que muestre la historia clinica y se adapata para que permita cambios una vez cerrado
 
 Actualizacion: Se organiza la ruta al include semaforo  Viviana Rodas 2012-05-14
 Actualizacion: Se agrega un trim cuando se evalua si el comentario es de desagrado ya que trae espacios y no entra al if. Viviana Rodas 2012-05-18
 * 
 * @table magenta_000008, select
 * @table magenta_000016, select sobre datos del paciente
 * @table magenta_000017, select, update 
 * @table magenta_000018, select, update
 * @table magenta_000019, select
 * @table magenta_000020, select
 * @table magenta_000021, select
 * @table magenta_000024, select
 * 
 *  @wvar $acoDir, direccion del acompanana
 *  @wvar $acoEma, email del acompanante
 *  @wvar $acoNom, nombre del acompananate
 *  @wvar $acoTel, telefono del acompanante
 *  @wvar $aut, persona que ingreso el comentario
 *  @wvar $bandera1, indica si es la primera vez que se entra al programa o no, si es 2 que todos los motivos son de agrado, si es 3 hay de 
 *  @wvar $cadena, se utiliza para armar el menu e ir a la pagina de datos del paciente
 *  @wvar $cadena2, se utiliza para armar el menu e ir a la pagina de comenta del paciente
 *  @wvar $camb, nombre del check box que indica si la persona cambio de opinion y volveria a la 
 *  @wvar $color color para desplegar el tipo de usuario que es el paciente
 *  @wvar $contador cuenta si la cantidad de motivos cerrados es igual al tamaño y luego si todos los motivos son de 
 *  @wvar $dir direccion del paciente
 *  @wvar $dir2 direccion del paciente codificada para madarla por 
 *  @wvar $doc documento del paciente
 *  @wvar $ema email del paciente
 *  @wvar $emo contacto emocional
 *  @wvar $ent entidad del paciente
 *  @wvar $envio si se va a cerrae el motivo o no (checkbox)
 *  @wvar $est estado del comentario 
 *  @wvar $fecCon fecha del contacto
 *  @wvar $fecOri fecha de origen del comentario
 *  @wvar $fecRec fecha de recepcion del comentario
 *  @wvar $fRes fecha de 
 *  @wvar $his historia clinica del paciente
 *  @wvar $idCom id del comentario en tabla 000017
 *  @wvar $idMot ide del motivo en tabla 000018
 *  @wvar $idPac id del paciente en tabla 000016
 *  @wvar $inicia, indica si los datos ingresado no cumplen con las validaciones cuando esta en uno
 *  @wvar $inicial, vector utilizado en explodes
 *  @wvar $lugOri lugar de origen del comentario
 *  @wvar $motAre, area a la que se asigno el comentario
 *  @wvar $motCar, cargo del implicado
 *  @wvar $motCau, causa del motivo 
 *  @wvar $motCauS, lista de causas para el dropdown
 *  @wvar $motCla vector de clasificaciones de los motivos
 *  @wvar $motCon datos del contacto
 *  @wvar $motCoo coordinadores de los motivos
 *  @wvar $motDes vector de descripciones de los motivos
 *  @wvar $motEnv vector de las fechas de envio de los motivos
 *  @wvar $motEst vector de estados de los motivos
 *  @wvar $motImp vector de los implicados de los motivos
 *  @wvar $motNum vector de numero de los motivos
 *  @wvar $motRet vector con las fechas en que fue investigado el comentario
 *  @wvar $motTip vector de tipo de los motivos
 *  @wvar $motVer vector con el dato de verificacion de los motivos
 *  @wvar $numCom numero del comentario
 *  @wvar $perDil persona que dilignecio el comentario
 *  @wvar $pRes persona que realizo la respuesta
 *  @wvar $priApe primer apellido del paciente
 *  @wvar $priNom primer nombre del paciente
 *  @wvar $segApe segundo apellido del paciente
 *  @wvar $segNom segundo nombre del paciente
 *  @wvar $semaforo, composicion entre el borde y relleno del color del semaforo del motivo
 *  @wvar $semaforoB, color del borde del semaforo para el motivo
 *  @wvar $semaforoR, color de relleno del semaforo para el motivo
 *  @wvar $senal, indica si todos los motivos del comentario estan cerrados para poder desplegar la parte de la respuesta
 *  @wvar $tamano cantidad de motivos del comentario
 *  @wvar $tel telefono del paciente
 *  @wvar $tipDoc tipo de documento del paciente
 *  @wvar $tipUsu tipo de usuario para afinidad (AAA, BBB, VIP, no clasificado)
 *  @wvar $tRes tipo de respuesta, telefono, etc
 *  @wvar $vol dato de si volveria a la clinica
*/
/************************************************************************************************************************* 
  Actualizaciones:
 			2016-05-02 (Arleyda Insignares C.)
 						-Se Modifica el campo de calendario 'Fecha de Cierre' con utilidad jquery y se elimina
						 uso de Zapatec Calendar por errores en la clase.
						-Se cambia encabezado con ultimo diseño.

*************************************************************************************************************************/
$wautor="Carolina Castano P.";
$wversion='2006-01-04';
$wactualiz='2016-05-02';
//=================================================================================================================================

/********************************funciones************************************/


/**
 * Si se ha invocado la pagina sin enviar un id de comentario
 */
function  DisplayError()
{
	echo '<script language="Javascript">';
	echo 'alert ("NO SE HA SUMINISTRADO UN IDENTIFICADOR PARA EL COMENTARIO, AVISE A SISTEMAS")';
	echo '</script>';

}

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

/****************************PROGRAMA************************************************/
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

	/////////////////////////////////////////////////inicialización de variables///////////////////////////////////

	/**
 	* calcula el tiempo que se han demorado en investigar el comentario, para color de semaforizacion
 	*/
	include_once("magenta/semaforo.php");

	/**
	 * conexion a Matrix
	 */
	

	


	$empresa='magenta';
	$contador=0; //contara cuantos motivos estan en estado cerrado, para ver si se muestra la parte de la respuesta

	/////////////////////////////////////////////////inicialización de variables//////////////////////////

	//deben mandarme el id del comentario, con eso busco los datos del paciente y los datos del comentario
	if (isset ($idCom))
	{
		//consulto el comentario
		$query ="SELECT id_persona, ccoori, ccofori, ccofrec, cconaco, ccotusu, ccoatel, ccoadir, ccoaema, ccoaut, ccocemo, ccoent, ccovol, ccocfec, ccoest, ccotres, ccorfec, ccorhor, ccorobs, ccorper, ccohis, cconum FROM " .$empresa."_000017 where id=".$idCom." ";
		
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
			$his=$row[20];
			$numCom=$row[21];

			if (!isset($bandera1) or !isset($fRes))
			{
				$tRes=$row[15];
				$fRes=$row[16];
				if ($fRes=='0000-00-00')
				$fRes='';
				$hRes=$row[17];
				$oRes=$row[18];
				$pRes=$row[19];

				if ($vol=='camb')
				{
					$camb='on';//indica que ha cambiado de opinion en si volveria a la clinica
				}
				else
				$camb='off';
			}

			if (!isset ($camb))
			$camb='off';

			//busco los datos del paciente
			$exp=explode('-',$idPac);
			if(isset($exp[3]))
			{
				$exp[0]=$exp[0].'-'.$exp[1];
				$exp[1]=$exp[2];
				$exp[2]=$exp[3];
			}
			$query ="SELECT cpedoc, cpetdoc, cpeno1, cpeno2, cpeap1, cpeap2, cpedir, cpetel, cpeema FROM " .$empresa."_000016 where cpedoc='".$exp[0]."' and  cpetdoc='".$exp[1]."-".$exp[2]."' ";

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

			$query ="SELECT A.id_Comentario, A.cmonum, A.cmotip, A.cmodes, A.cmocla, A.cmoest, A.cmocon, A.cmoinv, A.cmofenv, A.cmonimp, A.cmocau, A.cmover, A.id_area, A.id_cargo,  B.carcod, B.carnom, C.id_responsable, D.crecod, D.crenom, A.cmofret ";
			$query= $query. "FROM " .$empresa."_000018 A, " .$empresa."_000019 B, " .$empresa."_000020 C, " .$empresa."_000021 D ";
			$query= $query. "where A.id_comentario=".$idCom." ";
			$query= $query. "and B.carcod=A.id_area ";
			$query= $query. "and C.id_area=A.id_area and carniv=1 ";
			$query= $query. "and D.crecod=C.id_responsable ";
			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);

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
					$motInv[$i]=$row[7];
					$motEnv[$i]=$row[8];
					$motCau[$i]=$row[10];
					$motVer[$i]=$row[11];
					$motAre[$i]=$row[14]."-".$row[15];
					$motCoo[$i]=$row[17]."-".$row[18];
					$motRet[$i]=$row[19];

					//busco los implicados para el motivo
					$query ="SELECT impnom, ccanom  FROM " .$empresa."_000027, " .$empresa."_000022 where impnmo=".$idMot[$i]." and impnco=".$idCom." and ccacod=impcca ";

					$erri=mysql_query($query,$conex);
					$numi=mysql_num_rows($erri);
					//echo $query;
					if  ($numi >0) //se llena los valores
					{
						for($j=0;$j<$numi;$j++)
						{
							$rowi=mysql_fetch_row($erri);
							$motImp[$i][$j]=$rowi[0];
							$motCar[$i][$j]=$rowi[1];
						}
					}else
					{
						$motImp[$i][0]='SIN IMPLICADO';
						$motCar[$i][0]='';
					}

				}
			}else
			{

				DisplayError3();
			}

		}else
		{

			DisplayError1();
		}

	}else
	{
		DisplayError();
	}


	///////////////////////////////////////////acciones que dependen si es antes o despues del submit////////////////////////////////////////////////////////

	//recorro los motivos para ver si ya se quieren cerrar
	for($i=0;$i<$tamano;$i++)
	{
		if ($motEst[$i]=='CERRADO')
		{
			$envio[$i]='on';
			$contador++;

		}
		else
		{
			$envio[$i]='off';
		}

		if (!isset ($bandera1) and (!isset($fRes) or $fRes=='') )//es la primera vez que ingreso, inicializar las variables del formulario
		{
			if(!isset($motVer[$i]))
			{
				$motVer[$i]='';
			}
			$tRes='';
			$fRes='';
			$hRes='';
			$oRes='';
			$pRes='';

		}else if (isset ($bandera1))
		{
			//valido los datos ingresados

			$inicia=0;
			eval("\$motCau[$i] = \$motCau$i;");
			eval("\$motVer[$i] = \$motVer$i;");
			eval("\$motCauT[$i] = \$motCauT$i;");
			eval("\$envio[$i] = \$envio$i;");

			//validaciones e ingreso de datos
			if ($envio[$i]=='on') //se realiza almacenamiento del motivo
			{
				//primero validamos que se halla seleccionado un cuasa
				if (($motCau[$i] == '' or $motCau[$i] == '- -') and $inicia != '1')
				{
					echo '<script language="Javascript">';
					echo 'alert ("Debe seleccionar la causa para los motivos que desea cerrar")';
					echo '</script>';
					$inicia=1; //indica que no cumplio la validacion
				}

				if ($inicia != 1)
				{
					$contador++;
					//Se almacena el cierre del comentario
					$query="update " .$empresa."_000018 set cmocau='".$motCau[$i]."', cmoVer='".$motVer[$i]."', cmoEst='CERRADO' ";
					$query=$query."where cmonum=".$motNum[$i]." and id_comentario=".$idCom."";
					//echo $query;
					$err=mysql_query($query,$conex);

					//Cambio el valor al estado del motivo
					$motEst[$i] = 'CERRADO';
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
	if  ($num >0) //se busca el color con que voy a mostrar el tipo de usuario
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

		if ( !isset ($motCauT[$i]) or $motCauT[$i] == "" )
		{
			/*No se buscara o  no se establecera una selección de opciones para cargos*/
			$motCauT[$i]="$%&@#";
		}

		//se ha seleccionado una determinada causa
		if (isset ($motCau[$i]) and $motCau[$i]!='' and $motCau[$i]!= '- -' and $motCauT[$i]=="$%&@#")
		{
			$motCauS[$i]="<option>".$motCau[$i]."</option>";
			$motCauT[$i]="$%&@#";
		}

		//se ha ingresado parte de la causa para desplegar el dropdown
		if ( $motCauT[$i] != "$%&@#")
		{

			//Basados en $motCauT buscar el cargo en la tabla de causas
			$query="select caucod, caunom  FROM " .$empresa."_000024 where caucod like '%".strtoupper($motCauT[$i])."%'";
			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);
			//echo $query;

			if  ($num >0)
			{
				$motCauS[$i]="";
				for($j=0;$j<$num;$j++)
				{
					$row=mysql_fetch_row($err);
					$motCauS[$i]=$motCauS[$i]."<option>".$row[0]."-".$row[1]."</option>";
				}
			}

			$motCauT[$i]="$%&@#";
		}

		if (!isset ($motCauS[$i]) or $motCauS[$i] == '' or $motCauS[$i] == '- -' )
		{
			$motCauS[$i]="<option>- -</option>";
			$motCauT[$i]="$%&@#";
		}
	}


	//si todos los motivos del comentario estan en estado cerrado
	//HAGO EL QUERY PARA VER SI HAY ALGUN COMENTARIO QUE NO ESTE CERRADO
	$query ="SELECT * FROM " .$empresa."_000018 where id_comentario=".$idCom." and cmoest!='CERRADO'";
	$err=mysql_query($query,$conex);
	$num=mysql_num_rows($err);
	//echo $query;
	if  ($num <=0) //se llena los valores
	{
		//indica que todos los motivos estan cerrados se puede desplegar la parte de respuesta
		$senal='1';

		//Si esta setiada la fecha de respuesta puedo cerrar el comentario
		if (isset($bandera1) and isset ($fRes) and $fRes!='')
		{
			//indica que cambiaron de opinion sobre volver a la clinica
			if ($camb=='on')
			{
				$vol='camb';
			}

			//indica que todos los motivos son de agrado, en ese caso si la respuesta es telefonica debe ponerse la persona que realzo la llamada
			if ($bandera1==2)
			{
				if (!isset($pRes))
				{
					$pRes='';
				}

				//validaciones de persona que realiza el ingreso
				if ($pRes=='' and $tRes=='Telefonico')
				{
					echo '<script language="Javascript">';
					echo 'alert ("Debe ingresar el nombre de la persona que realizo la llamada")';
					echo '</script>';
				}else if ($inicia==0)
				{
					$query="update ".$empresa."_000017 set ccoest='CERRADO',  ccotres='".$tRes."', ccorfec='".$fRes."', ccorhor='".$hRes."',  ccorobs='".$oRes."',  ccorper='".$pRes."', ccovol='".$vol."'   where id= ".$idCom." ";
					//echo $query;
					$err=mysql_query($query,$conex);
					$est='CERRADO';
				}
			}
			if ($bandera1==3 and $inicia==0)
			{

				$query="update " .$empresa."_000017 set ccoest='CERRADO',  ccotres='".$tRes."', ccorfec='".$fRes."', ccorobs='".$oRes."',  ccovol='".$vol."' where id= ".$idCom." ";
				//echo $query;
				$err=mysql_query($query,$conex);
				$est='CERRADO';
			}
		}else if (!isset ($fres))
		{
			$fres='';
		}
	}

	/////////////////////////////////////////////////encabezado general///////////////////////////////////

/*	echo "<table align='right'>" ;
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
	echo "</table></br></br>" ;*/


/*	echo "<table align='right' >\n" ;
	echo "<tr>" ;
	echo "<td VALIGN=TOP NOWRAP>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<A HREF='javascript:window.showMenu(window.mWhite1);' onMouseOver='window.showMenu(window.mWhite1);'><font color=\"#D02090\" size=\"4\"><b>Menu</A>&nbsp/</b></font></td>";
	echo "<td><b><font size=\"4\"><A HREF='ayuda.mht' target='new'><font color=\"#D02090\">Ayuda</font></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></font></td>" ;
	echo "</tr>" ;
	echo "</table>*/
	echo "</br></br>" ;
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
 
 /////////////////////////////////////////////////encabezado personal///////////////////////////////////

 /*echo "<center><b><font size=\"4\"><A HREF='respuesta.php?idCom=$idCom'><font color=\"#D02090\">CIERRE DE COMENTARIO</font></a></b></font></center>\n" ;
 echo "<center><b><font size=\"2\"><font color=\"#D02090\"> respuesta.php ver.2006/04/17</font></font></center></br></br></br>\n" ;
 */echo "\n" ;
  /////////////////////////////////////////////////presentación general///////////////////////////////////
 echo "<center>";
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
 echo "<td><b><font size='3'  color='#00008B'>Direccion: </font></b>$dir</td>";
 echo "</tr>";
 echo "<tr>";

 echo "<td><b><font size='3'  color='#00008B'> Email: </font></b>$ema</td>";
 echo "<td><b><font size='3'  color='#00008B'>AFINIDAD:</font></b> <font color='#$color'>$tipUsu</font></td>";
 echo "</tr>";
 echo "</table></fieldset></center></br></br></br>";

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

 	echo "<td>&nbsp;&nbsp;<b><font size='3' color='#00008B'>Direccion: </font></b> $acoDir</td>";
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
 echo "<td><b><font size='3' color='#00008B'> Historia: </font></b>$his</td>";
 echo "</tr>";
 echo "</table></fieldset></br></br></br>";

 echo "<center><b><font size='4'><font color='#00008B'>CIERRE PARA EL COMENTARIO NUMERO: ".$numCom."</b></font></font></center></BR>";
 echo "<center><b><font size='3'><font color='#00008B'>PERSONA QUE INGRESO EL COMENTARIO: ".$aut."</b></font></font></center>";

 //antes de enviar la forma se adecuan los checkbox para los valores de cierre
 echo "<form action='respuesta.php' method='post' name='forma' onsubmit='javascript:valorar(".$tamano.")'>";

 echo "<CENTER><b><font size='3'><font color='#00008B'>CIERRE POR MOTIVO</b></font></font></BR></BR>";


 //para contar cuantos comentarios son de agrado
 $contador=0;

 //recorremos los motivos para desplegarlos es pantalla
 for($i=0;$i<$tamano;$i++)
 {
 	//se averiguan los colores del semaforo para el motivo
 	$semaforo=semaforo($motEst[$i], $motEnv[$i], $idCom, $empresa, $conex, $motNum[$i] );
 	$inicial = explode("-",$semaforo);
 	$semaforoB=$inicial[0];
 	$semaforoR=$inicial[1];


 	echo "<fieldset  style='border:solid;border-color:".$semaforoB."; width=100%' align='center'>";
 	echo "<table align='center0' bgcolor='".$semaforoR."' width=100%'>";
 	echo "<tr>";
 	echo "<td ROWSPAN='".(2+count($motImp[$i]))."'><b><font size='3'><font color='#00008B'> MOTIVO NUMERO: ".$motNum[$i]."</font></font></td>";
 	echo "<td  colspan='1'>  <b><font size='3'>TIPO: </font></b> ".$motTip[$i]."</td>";
 	echo "<td colspan='2'><b><font size='3'>CLASIFICACION: </font></b>".$motCla[$i]."</td>";
 	echo "</tr>";
 	echo "<tr>";
 	echo "<td colspan='3'><b><font size='3'>FECHA DE ENVIO: </font></b>".$motEnv[$i]."</td>";
 	echo "</tr>";
 	echo "<tr>";
 	echo "<td ><b><font size='3'> DESCRIPCION: </font></b></td>";
 	echo "<td colspan='2'><font size='3'>".$motDes[$i]."</font></td>";
 	echo "</tr>";
 	echo "</table></fieldset>";

 	echo "<fieldset  style='border:solid;border-color:".$semaforoB."; width=100%' align='center'>";
 	echo "<table align='center' bgcolor='".$semaforoR."' width=100%'>";
 	for($j=0;$j<count($motImp[$i][$j])-1;$j++)
 	{
 		echo "<tr>";
 		echo "<td width='20%' ROWSPAN=2><b><font size='3'><font color='#00008B'> IMPLICADO:</font></font></td>";
 		echo "<td  width='40%' colspan='1'> &nbsp;&nbsp; <b><font size='3'>NOMBRE:</font></b>". $motImp[$i][$j]."</td>";
 		echo "</tr>";
 		echo "<tr>";
 		echo "<td width='40%' colspan='2'>&nbsp;&nbsp;<b><font size='3'>CARGO:</font></b>".$motCar[$i][$j]."&nbsp;";
 		echo "</tr>";
 	}
 	echo "</table></fieldset>";
 	echo "<fieldset  style='border:solid;border-color:".$semaforoB."; width=100%' align='center'>";
 	echo "<table align='center' bgcolor='".$semaforoR."' width=100%'>";
 	echo "<tr>";
 	echo "<td width='20%' ROWSPAN=2><b><font size='3'><font color='#00008B'> AREA:</font></font></td>";
 	echo "<td width='40%' colspan='2'> &nbsp;&nbsp; <b><font size='3'>NOMBRE:</font></b>".$motAre[$i]."&nbsp;";
 	echo "</tr>";
 	echo "<tr>";
 	echo "<td width='40%' colspan='2'>&nbsp;&nbsp;<b><font size='3'>COORDINADOR:</font></b>".$motCoo[$i]."</td>";
 	echo "</tr>";
 	echo "</table></fieldset>";

 	// si es de desagrado se muestra la parte de la investigacion
 	if (trim($motTip[$i])=='Desagrado')
 	{
 		echo "<fieldset  style='border:solid;border-color:$semaforoB; width=100%' align='center'>";
 		echo "<table align='center' bgcolor='$semaforoR' width=100%'>";
 		echo "<tr>";
 		echo "<td ROWSPAN=3><b><font size='3'><font color='#00008B'> DESCARGO</font></font></td>";
 		echo "<td  colspan='1'><b><font size='3'>FECHA DE RETORNO:</font></b>".$motRet[$i]."</td>";
 		echo "</tr>";
 		echo "<tr>";
 		echo "<td ><b><font size='3'> DESCRIPCION: </font></b>".$motInv[$i].". ".$motCon[$i]."</td>";
 		echo "</tr>";

 		$query =" SELECT cacdes FROM " .$empresa."_000023 where id_motivo=".$motNum[$i]." and Caccon=".$idCom." ";
 		$err=mysql_query($query,$conex);
 		$num=mysql_num_rows($err);
 		//echo $query;

 		if  ($num >0) //se llenan los vectores según el estado del comentario
 		{
 			echo "<tr>";
 			echo "<td ><b><font size='3'> ACCIONES: </font></b>";

 			for($j=0;$j<$num;$j++)
 			{
 				$row=mysql_fetch_row($err);
 				echo "<li>".$row[0]."</li></br>";

 			}
 			echo "</font></td>";
 			echo "</tr>";
 			echo "</table></fieldset>";
 		}
 	}

 	echo "<fieldset  style='border:solid;border-color:$semaforoB; width=100%' align='center'>";
 	echo "<table align='center' bgcolor='$semaforoR' width=100%'>";
 	echo "<tr>";
 	echo "<td><b><font size='3'><font color='#00008B'> CIERRE</font></font></td>";
 	echo "<td colspan='2' align='left'> &nbsp;&nbsp; <b><font size='3'>CAUSA:</font></b><select name='motCau".$i."' >".$motCauS[$i]."</select>&nbsp;";
 	echo "<input type='text' name='motCauT".$i."' value='".$motCauT[$i]."' size=10 maxlength=10></td>";
 	echo "</tr>";
 	echo "<tr>";
 	echo "<td><b><font size='3'><font color='#00008B'> &nbsp;&nbsp;</font></font></td>";
 	echo "<td  colspan='1'> &nbsp;&nbsp; <b><font size='3'>VERIFICACION:</font></b>";

 	switch ($motVer[$i])
 	{
 		case 'SI':
 		echo "<input type='radio' name='motVer".$i."' value='SI' checked > <font size='2'>COMPROBADO&nbsp;";
 		echo "<input type='radio' name='motVer".$i."' value='NO' > <font size='2'>SIN COMPROBAR&nbsp;";
 		break;
 		case 'NO':
 		echo "<input type='radio' name='motVer".$i."' value='SI'> <font size='2'>COMPROBADO&nbsp;";
 		echo "<input type='radio' name='motVer".$i."' value='NO' checked > <font size='2'>SIN COMPROBAR&nbsp;";
 		break;
 		default:
 		echo "<input type='radio' name='motVer".$i."' value='SI' checked > <font size='2'>COMPROBADO&nbsp;";
 		echo "<input type='radio' name='motVer".$i."' value='NO' > <font size='2'>SIN COMPROBAR&nbsp;";
 		break;
 	}
 	echo "<td colspan='1' align='CENTER'><B>CERRAR:</B><input type='checkbox' name='envio".$i."' value='on' >&nbsp;&nbsp;&nbsp;&nbsp;</td>";
 	echo "</tr>";
 	echo "</table></fieldset></br>";


 	echo "</br></br></br>";

 	if (isset($envio[$i]) and $envio[$i]=='on')
 	{
 		echo '<script language="Javascript">';
 		echo "activar(".$i.");";
 		echo '</script>';
 	}


 	if ($motTip[$i]=='Agrado')
 	$contador++;
 }

 echo "<input type='hidden' name='idCom' value='$idCom' />";
 echo "<input type='hidden' name='tamano' value='$tamano' />";


 if (!isset ($senal))//no se puede desplegar aun la parte de respuesta
 {
 	echo "<input type='hidden' name='bandera1' value='1' />";
 	echo "<input type='submit'  name='enviar' value='ENVIAR' onclick='javascript:valorar(".$tamano.")'></CENTER></BR>";
 	echo "</FORM>";
 }else
 {
 	echo "<CENTER><b><font size='3'><font color='#00008B'>CIERRE GENERAL</b></font></font></BR></BR>";

 	echo "<fieldset  style='border:solid;border-color:#00008B; width=100%' align='center'>";
 	echo "<table align='center0' bgcolor='#EOF0FF' width=100%'>";
 	echo "<tr>";
 	echo "<td ROWSPAN='6'><b><font size='3'><font color='#00008B'> DETALLES:</font></font></td>";

 	echo "<td  colspan='2'> <b><font size='3'>MEDIO DE RESPUESTA: </font></b>";
 	if ($contador==$tamano)//en este caso es que todos los motivos son de agrado
 	{
 		if ($est=='CERRADO')
 		echo "<select name='tRes'><option selected>".$tRes."</option></select>&nbsp;";
 		else
 		echo "<select name='tRes'><option selected>Telefonico</option><option>No respuesta</option></select>&nbsp;";
 	}else //hay algun motivo de desagrado
 	{
 		if ($est=='CERRADO')
 		echo "<select name='tRes'><option selected>".$tRes."</option></select>&nbsp;";
 		else
 		echo "<select name='tRes'><option selected>Escrito</option><option>Telefonico</option><option>Mail</option><option>Personal</option><option>No respuesta</option></select>&nbsp;";
 	}
 	echo "</td>";
 	echo "</tr>";
 	echo "<tr>";

 	$cal="calendario('fRes','1')";
 	echo "<td  colspan='2'><b><font size='3'>FECHA DE CIERRE <input type='text' readonly='readonly' id='fRes' name='fRes' value='".$fRes."' class=tipo3 ></td>";
 	//value='".date("Y-m-d")."'

	echo "<td colspan='1'>&nbsp;&nbsp;<b><font size='3'></td>";
	echo "</tr>";
	echo "<tr>";


	if ($contador==$tamano) //tambien en caso de ser de agrado todos los motivos
	{
		echo "<tr>";
		echo "<td><b><font size='3'><font color='#00008B'> &nbsp;&nbsp;</font></font></td>";
		echo "<td  colspan='1'><b><font size='3'>HORA DE RESPUESTA (HH-mm-ss): </font></b><input type='text' size='5' name='hRes' value='".$hRes."'/></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td><b><font size='3'><font color='#00008B'> &nbsp;&nbsp;</font></font></td>";
		echo "<td  colspan='2'><b><font size='3'>QUIEN REALIZO LA LLAMADA: </font></b><input type='text' size='25' name='pRes' value='".$pRes."'/></td>";
		echo "</tr>";

		echo "<input type='hidden' name='bandera1' value='2' />";
	}else
	echo "<input type='hidden' name='bandera1' value='3' />";

	echo "<tr>";
	echo "<td><b><font size='3'><font color='#00008B'> &nbsp;&nbsp;</font></font></td>";
	echo "<td colspan='1' align='right'><b><font size='3'>OBSERVACIONES: </font></b></td>";
	echo "<td colspan='1' align='left'><textarea rows='4' cols='50' name='oRes' style='font-family: Arial; font-size:14'>$oRes</textarea></td>";
	echo "</tr>";

	if ($vol=='NO' or $vol=='camb')
	{
		echo "<tr>";
		echo "<td colspan='2' align=center ><B>VOLVERIA A UTILIZAR NUESTROS SERVICIOS (CAMBIO DE OPINION):</B></td>";
		echo "<td><b><font size='3'><font color='#00008B'><input type='checkbox' name='camb' value='on' ></font></font></td>";
		echo "</tr>";
		if ($vol=='camb')
		{
			?>

			<SCRIPT LANGUAGE="JavaScript1.2">
			document.forma.camb.checked=true;
				</SCRIPT>
				<?php
 		}
 	}
 	echo "<tr>";
 	echo "<td><b><font size='3'><font color='#00008B'> &nbsp;&nbsp;</font></font></td>";
 	echo "<td><b><font size='3'><font color='#00008B'> &nbsp;&nbsp;</font></font></td>";
 	echo "<td align='right'><b><font size='3' color= '#00008B'>ESTADO:".$est." </font></b></td>";


 	echo "</tr>";
 	echo "</table></fieldset></BR></BR>";

 	echo "<input type='submit'  name='enviar' value='ENVIAR' onclick='javascript:valorar(".$tamano.")'></CENTER></BR>";
 	echo "</FORM>";
 }

 echo "<center>";

 echo "<a href='asignacion.php?idCom=".$idCom."' align='right'><<--Asignacion</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

 if ($contador==$tamano)
 echo "<a href='propuesta.phpidCom=".$idCom."' align='right'>VER RESPUESTA</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

 echo "</center></BR>";

}
?>
</body>
</html>
