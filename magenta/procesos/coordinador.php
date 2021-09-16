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
        $(document).ready(function() {
		$("#fecha, #fecha1").datepicker({
	       showOn: "button",
	       buttonImage: "../../images/medical/root/calendar.gif",
	       buttonImageOnly: true,
	       maxDate:"+1D"
	    });
		});
</SCRIPT>
<SCRIPT LANGUAGE="JavaScript1.2">
<!--
function onLoad() {
	loadMenus();
}
//-->
</SCRIPT>
</head>
<body>
<?php
include_once("conex.php"); 

/**
 * 	LISTA DE COMENTARIOS DEL MES ASIGNADOS A UNA UNIDAD ESPCIFICA
 * 
 * Este programa permite realizar la busqueda de comentarios por mes Y por unidad,
 * 
 * @name  matrix\magenta\procesos\coordinador.php
 * @author Carolina Castaño Portilla.
 * 
 * @created 2006-04-10
 * @version 2006-01-09
 * 
 * @modified 2006-08-10 mejoras a la presentacion
 * @modified 2007-01-09  Se realiza documentacion del programa y se agrega dato de historia clinica y calendario
 * 
 * @table magenta_000017, select, 
 * @table magenta_000018, select, 
 * @table magenta_000019, select
 * @table magenta_000020, select
 * @table magenta_000021, select
 * @table magenta_000023, select
 * 
 *  @wvar $agrado, vector de comentarios de agrado
 *  @wvar $area codigo y nombre del area seleccionada para traer sus comentarios
 *  @wvar $areNomS vector con lista de areas para el drop down
 *  @wvar $asignados, vector con la lista de comentarios asignados del mes del area
 *  @wvar $aut, usuario
 *  @wvar $bandera1, indica si es la primera vez que se ingresa al programa, es decir se muestra la primera hoja
 *  @wvar $cerrados,  vector que guarda los comentarios cerrados
 *  @wvar $color, dependiendo si el comentario es de agrado o de desagrado
 *  @wvar $contador sieve como contador temporal del tamaño de los vectores de comentarios para cda estado
 *  @wvar $fecha fecha incial de l busqueda de comentarios
 *  @wvar $fecha1 fecha final de l busqueda de comentarios
 *  @wvar $idArea id del area seleccionada en tabla 000019
 *  @wvar $ ingresados vector de comentarios en estado ingresado para las fechas dadas
 *  @wvar $inicial, vector utilizado en explodes
 *  @wvar $investigados, vector de comentarios investigados en las fechas dadas
 *  @wvar $lista indica si es usado por magenta o por un coordinador de acuerdo a eso cambia el menu
 *  @wvar $pass permite restarle 2 meses a la fecha actual
 *  @wvar $semaforo, composicion entre el borde y relleno del color del semaforo del motivo
 *  @wvar $senal, indica que sucede, primera vez en entrar, indica si se encontraron comentarios en las fechas dadas, etc
 *  @wvar $tramitando, lista de comentario en tramite por el coordinador
*/
/************************************************************************************************************************* 
  Actualizaciones:
 			2016-05-02 (Arleyda Insignares C.)
 						-Se Modifican los campos de calendario fecha inicial y fecha final con utilidad jquery y se elimina
						 uso de Zapatec Calendar por errores en la clase.
						-Se cambia encabezado y titulos con ultimo formato.
*************************************************************************************************************************/

$wautor   ="Carolina Castano P.";
$wversion ='2007-01-09';
$wactualiz='2016-05-04';

/***********************************funciones***************************************/

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
    include_once("root/comun.php");
	/////////////////////////////////////////////////encabezado general///////////////////////////////////
    $titulo = "SISTEMA DE COMENTARIOS Y SUGERENCIAS";

    // Se muestra el encabezado del programa
    encabezado($titulo,$wactualiz, "clinica");  

	if (!isset($lista))//indica si la esta usando el coordinador o magenta, si es magenta y viene de lista magenta, lista esta setiada
	{
		echo "<table align='right' >\n" ;
		echo "<tr>" ;
		echo "<td><b><font size=\"4\"><A HREF='ayuda1.mht' target='new'><font color=\"#0033CC\">Ayuda</font></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></font></td>" ;
		echo "</tr>" ;
		echo "</table></br></br></br>" ;
	}else
	{
		echo "<table align='right' >\n" ;
		echo "<tr>" ;
		echo "<td><b><font size=\"4\"><A HREF='ayuda.htm' target='new'><font color=\"#0033CC\">Ayuda</font></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></font></td>" ;
		echo "</tr>" ;
		echo "</table></br></br>" ;

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
  		mWhite1.bgColor = "#ADD8E6";
  		mWhite1.menuItemBgColor = "white";
  		mWhite1.menuHiliteBgColor = "#336699";

  		myMenu.writeMenus();
  	}

	</SCRIPT>
 <?php
	}


	/////////////////////////////////////////////////encabezado general///////////////////////////////////
	/////////////////////////////////////////////////inicialización de variables///////////////////////////////////
	/**
 	* calcula el tiempo que se han demorado en investigar el comentario, para color de semaforizacion
 	*/
	include_once("magenta/semaforo.php");
	/**
	 * conexion a Matrix
	 */
	

	


	$empresa='magenta';
	$senal='0';

	/////////////////////////////////////////////////inicialización de variables//////////////////////////
	/////////////////////////////////////////////////acciones concretas///////////////////////////////////

	//Busco en base de datos el area de interes segun el codigo de la persona que esta accediendo a matrix
	//si el codigo no está o es magenta se preguntara el codigo del area de interes
	// o si el coordinador tiene varias áreas se traerá la lista de areas que se requieran

	$inicial=strpos($user,"-");
	$aut=substr($user, $inicial+1, strlen($user));

	// iniciacion de fechas
	if (!isset ($fecha))
	{
		$pass= intval(date('m'))-2;

		if ($pass<10)
		$pass='0'.$pass;

		$fecha=date('Y').'-'.$pass.'-'.date('d');

		if ($pass==0)
		{
			$fecha= intval (date('Y'))-1;
			$fecha=$fecha.'-12-'.date('d');

		}
		if ($pass<0)
		{
			$fecha= intval (date('Y'))-1;
			$fecha=$fecha.'-11-'.date('d');

		}

		$fecha1=date('Y-m-d');
	}

	//si es la primera vez que se entra se muestra la lista de areas segun el usuario
	if (!isset ($bandera1))
	{
		$query ="SELECT '', B.id_area, C.carcod, C.carnom FROM ".$empresa."_000021 A, ".$empresa."_000020 B, ".$empresa."_000019 C  where A.crecod='".$aut."' and B.id_responsable=A.crecod and Carcod=B.id_area ";
		$err=mysql_query($query,$conex);
		$num=mysql_num_rows($err);
		//echo $query;
		if  ($num >0) //se llena los valores y un vector con los resultados
		{
			if ($num >1)//en este caso es un display con varias areas
			{
				$senal=1; //indica que aun no se tiene el id del area
				for($i=0;$i<$num;$i++)
				{
					$row=mysql_fetch_row($err);
					if($i==0)
					$areNomS="<option selected>".$row[2]."-".$row[3]."</option>";
					else
					$areNomS=$areNomS."<option>".$row[2]."-".$row[3]."</option>";
				}

			}else //es una sola area entonces se selecciona por defecto
			{
				$row=mysql_fetch_row($err);
				$idArea=$row[1];
				$area=$row[2].'-'.$row[3];
			}
		}else //forma para seleccionar el área de la lista de áreas
		{
			$senal = 2; //indicar que no tiene areas el usuario que tenga derecho a conocer
		}

	} else if (!isset ($idArea) and isset ($area))
	{
		$inicial = explode ('-', $area);
		$idArea=$inicial[0];
	}

	If ($senal==0) //no se ha indicado nada extraño y se tiene el id del area
	{
		// Busco los motivos del area y los voy guardando en vectores segun su estado

		//inicializo el tamaño de los vectores
		$asignados[0]['id']=0;
		$tramitando[0]['id']=0;
		$investigados[0]['id']=0;
		$cerrados[0]['id']=0;
		$agrado[0]['id']=0;


		$query ="SELECT A.id_Comentario, A.cmonum, A.cmofenv, A.cmotip, A.cmocla, A.cmoest, A.cmofret, A.id_comentario, B.ccofori, B.ccoori, B.ccoent, B.ccohis, B.cconum  ";
		$query= $query. "FROM " .$empresa."_000018 A, " .$empresa."_000017 B  where A.id_area='".$idArea."' and A.cmofenv between '".$fecha."'  and '".$fecha1."'  and B.Ccoid =A.id_comentario ORDER BY A.cmofenv";

		$err=mysql_query($query,$conex);
		$num=mysql_num_rows($err);
		$tamano = $num;
		//echo $query;
		if  ($num >0) //se llenan los vectores según el estado del comentario
		{
			for($i=0;$i<$num;$i++)
			{
				$row=mysql_fetch_row($err);

				if ($row[3]=='Agrado')
				{
					$contador=$agrado[0]['id'];
					$contador++;
					$agrado[$contador]['id']=$row[0];
					$agrado[$contador]['motNum']=$row[1];
					$agrado[$contador]['motEnv']=$row[2];
					$agrado[$contador]['motTip']=$row[3];
					$agrado[$contador]['motCla']=$row[4];
					$agrado[$contador]['motRet']=$row[6];
					$agrado[$contador]['idCom']=$row[7];
					$agrado[$contador]['fecCom']=$row[8];
					$agrado[$contador]['lugCom']=$row[9];
					$agrado[$contador]['entidad']=$row[10];
					$agrado[$contador]['his']=$row[11];
					$agrado[$contador]['num']=$row[12];
					$agrado[0]['id']=$contador;

				}else
				{

					switch ($row[5])
					{
						case 'ASIGNADO':
						$contador=$asignados[0]['id'];
						$contador++;
						$asignados[$contador]['id']=$row[0];
						$asignados[$contador]['motNum']=$row[1];
						$asignados[$contador]['motEnv']=$row[2];
						$asignados[$contador]['motTip']=$row[3];
						$asignados[$contador]['motCla']=$row[4];
						$asignados[$contador]['motRet']=$row[6];
						$asignados[$contador]['idCom']=$row[7];
						$asignados[$contador]['fecCom']=$row[8];
						$asignados[$contador]['lugCom']=$row[9];
						$asignados[$contador]['entidad']=$row[10];
						$asignados[$contador]['his']=$row[11];
						$asignados[$contador]['num']=$row[12];
						$asignados[0]['id']=$contador;
						break;
						case 'TRAMITANDO':
						$contador=$tramitando[0]['id'];
						$contador++;
						$tramitando[$contador]['id']=$row[0];
						$tramitando[$contador]['motNum']=$row[1];
						$tramitando[$contador]['motEnv']=$row[2];
						$tramitando[$contador]['motTip']=$row[3];
						$tramitando[$contador]['motCla']=$row[4];
						$tramitando[$contador]['motSem']=$row[4];
						$tramitando[$contador]['motRet']=$row[6];
						$tramitando[$contador]['idCom']=$row[7];
						$tramitando[$contador]['fecCom']=$row[8];
						$tramitando[$contador]['lugCom']=$row[9];
						$tramitando[$contador]['entidad']=$row[10];
						$tramitando[$contador]['his']=$row[11];
						$tramitando[$contador]['num']=$row[12];
						$tramitando[0]['id']=$contador;
						break;
						case 'INVESTIGADO':
						$contador=$investigados[0]['id'];
						$contador++;
						$investigados[$contador]['id']=$row[0];
						$investigados[$contador]['motNum']=$row[1];
						$investigados[$contador]['motEnv']=$row[2];
						$investigados[$contador]['motTip']=$row[3];
						$investigados[$contador]['motCla']=$row[4];
						$investigados[$contador]['motSem']=$row[4];
						$investigados[$contador]['motRet']=$row[6];
						$investigados[$contador]['idCom']=$row[7];
						$investigados[$contador]['fecCom']=$row[8];
						$investigados[$contador]['lugCom']=$row[9];
						$investigados[$contador]['entidad']=$row[10];
						$investigados[$contador]['his']=$row[11];
						$investigados[$contador]['num']=$row[12];
						$investigados[0]['id']=$contador;
						break;
						case 'CERRADO':
						$contador=$cerrados[0]['id'];
						$contador++;
						$cerrados[$contador]['id']=$row[0];
						$cerrados[$contador]['motNum']=$row[1];
						$cerrados[$contador]['motEnv']=$row[2];
						$cerrados[$contador]['motTip']=$row[3];
						$cerrados[$contador]['motCla']=$row[4];
						$cerrados[$contador]['motSem']=$row[4];
						$cerrados[$contador]['motRet']=$row[6];
						$cerrados[$contador]['idCom']=$row[7];
						$cerrados[$contador]['fecCom']=$row[8];
						$cerrados[$contador]['lugCom']=$row[9];
						$cerrados[$contador]['entidad']=$row[10];
						$cerrados[$contador]['his']=$row[11];
						$cerrados[$contador]['num']=$row[12];
						$cerrados[0]['id']=$contador;
						break;
					}
				}
			}
		}else
		{
			$senal='3';
		}

	}



	////////////////////////////////////////////////////////////////encabezado personal////////////////////////////////////////////////////

	echo "<div align='center'><div align='center' class='fila1' style='width:520px;'><font size='4'><b>CONSULTA Y RESPUESTA A COMENTARIOS</b></font></div></div></BR>";

	/*if (!isset($lista))
	echo "<center><b><font size=\"4\"><A HREF='coordinador.php'><font color=\"#D02090\">CONSULTA Y RESPUESTA A COMENTARIOS</font></a></b></font></center>\n" ;	
	else	
	echo "<center><b><font size=\"4\"><A HREF='coordinador.php?lista=1'><font color=\"#D02090\">CONSULTA Y RESPUESTA A COMENTARIOS</font></a></b></font></center>\n" ;
	echo "<center><b><font size=\"2\"><font color=\"#D02090\"> coordinador.php</font></font></center></br></br></br>\n" ;
	echo "\n" ;*/
	//////////////////////////////////////////////////////////////////presentación general/////////////////////////////////////////////////

	//apenas voy a mostrar el dropdown para poder tener el id del area
	if ($senal ==1)
	{
		echo "<center><font color='#00008B'>SELECCIONE LA UNIDAD DE CONSULTA</font></center></BR>";

		echo "<fieldset style='border:solid;border-color:#00008B; width=800' align=center></br>";

		echo "<form NAME='ingreso' ACTION='coordinador.php' METHOD='POST'>";
		echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
		echo "<table align='center'>";
		echo "<tr>";

		echo "<td align=center width='100'><font size=3  color='#00008B' face='arial'><b>UNIDAD:&nbsp</b></td>";
		echo "<td align='center' colspan='2'><select name='area'>$areNomS</select></td>";
		echo "<input type='hidden' name='bandera1' value='1' />";
		if (isset($lista))
		echo "<input type='hidden' name='lista' value='1' />";

		echo "</td>";
		echo "</tr></TABLE></br>";
		echo "<TABLE align=center><tr>";
		echo "<tr><td align=center colspan=14><font size='2'  align=center face='arial'><input type='submit' name='aceptar' value='ACEPTAR' ></td></tr>";
		echo "</TABLE>";
		echo "</td>";
		echo "</tr>";
		echo "</form>";
		echo "</fieldset>";
	}
	if ($senal ==2) //no se encuentra area asignada para el usuario
	{
		echo "<CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
		echo "<table align='center' border=0 bordercolor=#000080 width=340 style='border:solid;'>";
		echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>NO EXISTE NINGUNA UNIDAD ASIGNADA PARA EL USUARIO</td><tr>";
		echo "</table></fieldset></center></br></br>";
	}
	if ($senal ==3) //no se ecnontro ningun comentario para la unidad
	{
		echo "<CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
		echo "<table align='center' border=0 bordercolor=#000080 width=340 style='border:solid;'>";
		echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>LA UNIDAD ".$area." NO TIENE NINGUN COMENTARIO REGISTRADOS EN EL RANGO DE FECHAS SELECCIONADO</td><tr>";
		echo "</table></fieldset></center></br></br>";
	}

	if ($senal ==0) //se pasa a mostrar la lista de comentarios de la unidad
	{
		echo "<center><font color='#00008B'><B>UNIDAD: ".$area." </B></font></center></BR>";
	}
	////////////////////////////////////COMENTARIOS ASIGNADOS/////////////////////////////////////////////////////

	if ($senal==0)
	{
		echo "<center><b><font size='4'><font color='#00008B'>COMENTARIOS POR INVESTIGAR</b></font></font></center></br>";

		if ($asignados[0]['id'] > 0) // Si el paciente tiene comentarios en estado ingresados se buscan los motivos para mostrar
		{

			echo "<table align='center' border='1' width='100%'>";
			echo "<tr>";
			echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>N Comentario</font></td>";
			echo "	<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>Fecha de origen</font></td>";
			echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>Lugar de origen</font></td>";
			echo "<td bgcolor='#336699'width='10%' align='center'><font color='#ffffff'>Fecha de envio</font></td>";
			echo "<td bgcolor='#336699' width='10%' align='center'><font color='#ffffff'>Entidad</font></td>";
			echo "<td bgcolor='#336699' width='10%' align='center'><font color='#ffffff'>Historia</font></td>";
			echo "<td bgcolor='#336699' width='3%' align='center'><font color='#ffffff'>Sem.</font></td>";
			echo "</tr>";

			//recorro vector de comantarios pintando y buscando la semaforizacion
			for($i=1;$i<=$asignados[0]['id'];$i++)
			{
				if ($asignados[$i]['motTip']=='Agrado')
				$color='#EOFFFF';
				else
				$color="#cccccc";


				$semaforo=semaforo('ASIGNADO', $asignados[$i]['motEnv'], $asignados[$i]['id'], $empresa, $conex, $asignados[$i]['motNum']);
				$inicial = explode("-",$semaforo);
				$semaforo=$inicial[0];

				echo "<tr>";
				if (!isset($lista))
				echo "<td bgcolor='$color' width='8%' align='center'><font color='#ffffff'><a href='investigacion.php?idMot=".$asignados[$i]['motNum']."&idCom=".$asignados[$i]['idCom']."' align='right'>".$asignados[$i]['num']."-".$asignados[$i]['motNum']."<A/></font></td>";
				else
				echo "<td bgcolor='$color' width='8%' align='center'><font color='#ffffff'><a href='investigacion.php?idMot=".$asignados[$i]['motNum']."&idCom=".$asignados[$i]['idCom']."&lista=1' align='right'>".$asignados[$i]['num']."-".$asignados[$i]['motNum']."<A/></font></td>";
				echo "<td bgcolor='$color' width='8%' align='center'>".$asignados[$i]['fecCom']."</td>";
				echo "<td bgcolor='$color' width='8%' align='center'>".$asignados[$i]['lugCom']."</td>";
				echo "<td bgcolor='$color' width='10%' align='center'>".$asignados[$i]['motEnv']."</td>";
				echo "<td bgcolor='$color' width='10%' align='center'>".$asignados[$i]['entidad']."</td>";
				echo "<td bgcolor='$color' width='10%' align='center'>".$asignados[$i]['his']."</td>";
				echo "<td bgcolor='$semaforo' width='3%' align='center'>&nbsp;</td>";
				echo "</tr>";

			}
			echo "</table></br></br>";

		}else
		{
			echo "<CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
			echo "<table align='center' border=0 bordercolor=#000080 width=340 style='border:solid;'>";
			echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>LA UNIDAD NO TIENE COMENTARIOS POR INVESTIGAR</td><tr>";
			echo "</table></fieldset></center></br></br>";
		}
		////////////////////////////////////COMENTARIOS TRAMITANDO/////////////////////////////////////////////////////
		echo "<center><b><font size='4'><font color='#00008B'>COMENTARIOS EN INVESTIGACION</b></font></font></center></br>";
		if ($tramitando[0]['id'] > 0) // Si el paciente tiene comentarios en estado ingresados se buscan los motivos para mostrar
		{


			echo "<table align='center' border='1' width='100%'>";
			echo "<tr>";
			echo "<td bgcolor='#336699'width='8%' align='center'><font color='#ffffff'>N Comentario</font></td>";
			echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>Fecha de origen</font></td>";
			echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>Lugar de origen</font></td>";
			echo "<td bgcolor='#336699' width='10%' align='center'><font color='#ffffff'>Fecha de envio</font></td>";
			echo "<td bgcolor='#336699' width='10%' align='center'><font color='#ffffff'>Entidad</font></td>";
			echo "<td bgcolor='#336699' width='10%' align='center'><font color='#ffffff'>Historia</font></td>";
			echo "<td bgcolor='#336699' width='3%' align='center'><font color='#ffffff'>Sem.</td>";
			echo "</tr>";

			//recorro vector de comantarios pintando y buscando la semaforizacion
			for($i=1;$i<=$tramitando[0]['id'];$i++)
			{
				if ($tramitando[$i]['motTip']=='Agrado')
				$color='#EOFFFF';
				else
				$color="#cccccc";

				$semaforo=semaforo('ASIGNADO', $tramitando[$i]['motEnv'], $tramitando[$i]['id'], $empresa, $conex, $tramitando[$i]['motNum']);
				$inicial = explode("-",$semaforo);
				$semaforo=$inicial[0];

				echo "<tr>";
				if (!isset($lista))
				echo "<td bgcolor='$color' width='8%' align='center'><font color='#ffffff'><a href='investigacion.php?idMot=".$tramitando[$i]['motNum']."&idCom=".$tramitando[$i]['idCom']."' align='right'>".$tramitando[$i]['num']."-".$tramitando[$i]['motNum']."<A/></font></td>";
				else
				echo "<td bgcolor='$color' width='8%' align='center'><font color='#ffffff'><a href='investigacion.php?idMot=".$tramitando[$i]['motNum']."&idCom=".$tramitando[$i]['idCom']."&lista=1' align='right'>".$tramitando[$i]['num']."-".$tramitando[$i]['motNum']."<A/></font></td>";
				echo "<td bgcolor='$color' width='8%' align='center'>".$tramitando[$i]['fecCom']."</td>";
				echo "<td bgcolor='$color' width='8%' align='center'>".$tramitando[$i]['lugCom']."</td>";
				echo "<td bgcolor='$color' width='10%' align='center'>".$tramitando[$i]['motEnv']."</td>";
				echo "<td bgcolor='$color' width='10%' align='center'>".$tramitando[$i]['entidad']."</td>";
				echo "<td bgcolor='$color' width='10%' align='center'>".$tramitando[$i]['his']."</td>";
				echo "<td bgcolor='$semaforo' width='3%' align='center'>&nbsp;</td>";
				echo "</tr>";

			}
			echo "</table></br></br>";

		}else
		{
			echo "<CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
			echo "<table align='center' border=0 bordercolor=#000080 width=340 style='border:solid;'>";
			echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>LA UNIDAD NO TIENE COMENTARIOS EN INVESTIGACION</td><tr>";
			echo "</table></fieldset></center></br></br>";
		}
		////////////////////////////////////COMENTARIOS DE AGRADO /////////////////////////////////////////////////////
		echo "<center><b><font size='4'><font color='#00008B'>COMENTARIOS DE AGRADO</b></font></font></center></br>";
		if ($agrado[0]['id'] > 0) // Si el paciente tiene comentarios en estado ingresados se buscan los motivos para mostrar
		{

			echo "<table align='center' border='1' width ='100%'>";
			echo "<tr>";
			echo "<td bgcolor='#336699'width='8%' align='center'><font color='#ffffff'>N Comentario</font></td>";
			echo "	<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>Fecha de origen</font></td>";
			echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>Lugar de origen</font></td>";
			echo "<td bgcolor='#336699' width='10%' align='center'><font color='#ffffff'>Fecha de envio</font></td>";
			echo "<td bgcolor='#336699' width='10%' align='center'><font color='#ffffff'>Entidad</font></td>";
			echo "<td bgcolor='#336699' width='10%' align='center'><font color='#ffffff'>Historia</font></td>";
			echo "</tr>";

			//recorro vector de comantarios pintando y buscando la semaforizacion
			for($i=1;$i<=$agrado[0]['id'];$i++)
			{

				echo "<tr>";
				if (!isset($lista))
				echo "<td bgcolor='#EOFFFF' width='8%' align='center'><font color='#ffffff'><a href='investigacion.php?idMot=".$agrado[$i]['motNum']."&idCom=".$agrado[$i]['idCom']."' align='right'>".$agrado[$i]['num']."-".$agrado[$i]['motNum']."<A/></font></td>";
				else
				echo "<td bgcolor='#EOFFFF' width='8%' align='center'><font color='#ffffff'><a href='investigacion.php?idMot=".$agrado[$i]['motNum']."&idCom=".$agrado[$i]['idCom']."&lista=1' align='right'>".$agrado[$i]['num']."-".$agrado[$i]['motNum']."<A/></font></td>";
				echo "<td bgcolor='#EOFFFF' width='8%' align='center'>".$agrado[$i]['fecCom']."</td>";
				echo "<td bgcolor='#EOFFFF' width='8%' align='center'>".$agrado[$i]['lugCom']."</td>";
				echo "<td bgcolor='#EOFFFF' width='10%' align='center'>".$agrado[$i]['motEnv']."</td>";
				echo "<td bgcolor='#EOFFFF' width='10%' align='center'>".$agrado[$i]['entidad']."</td>";
				echo "<td bgcolor='#EOFFFF' width='10%' align='center'>".$agrado[$i]['his']."</td>";
				echo "</tr>";

			}
			echo "</table></br></br>";

		}else
		{
			echo "<CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
			echo "<table align='center' border=0 bordercolor=#000080 width=340 style='border:solid;'>";
			echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>LA UNIDAD NO TIENE COMENTARIOS EN INVESTIGACION</td><tr>";
			echo "</table></fieldset></center></br></br>";
		}

		////////////////////////////////////COMENTARIOS INVESTIGADOS/////////////////////////////////////////////////////
		echo "<center><b><font size='4'><font color='#00008B'>ACCIONES POR IMPLEMENTAR</b></font></font></center></br>";
		$k=0;
		if ($investigados[0]['id'] > 0 or $cerrados[0]['id'] > 0) // Si el paciente tiene comentarios en estado investigado se buscan las acciones para mostrar
		{
			//recorro vector de comantarios pintando y buscando la semaforizacion
			for($i=1;$i<=$investigados[0]['id'];$i++)
			{
				$query =" SELECT cacnum, cacres, cacfver FROM " .$empresa."_000023 where caccon=".$investigados[$i]['id']." and id_motivo=".$investigados[$i]['motNum']." and  cacest='INGRESADO' and cacfver > '".date('Y-m-d')."' ";
				$err=mysql_query($query,$conex);
				$num=mysql_num_rows($err);
				//echo $query;


				if  ($num >0) //se llenan los vectores según el estado del comentario
				{
					echo "<table align='center' border='1' width='100%'>";
					echo "<tr>";
					echo "<td bgcolor='#336699' width='15%' ><a href='investigacion.php?idMot=".$investigados[$i]['motNum']."&idCom=".$investigados[$i]['idCom']."' align='right'><font color='#ffffff'>N Comentario: ".$investigados[$i]['num']."-".$investigados[$i]['motNum']."</font></a></td>";
					echo "<td  bgcolor='#336699' width='35%'  colspan='2'><font color='#ffffff'>Fecha/Lugar de origen: ".$investigados[$i]['fecCom']."&nbsp;".$investigados[$i]['lugCom']."</font></td>";
					echo "<td bgcolor='#336699'  width='15%' ><font color='#ffffff'>Fecha envio: ".$investigados[$i]['motEnv']." </font></td>";
					echo "<td bgcolor='#336699' width='15%' ><font color='#ffffff'>Entidad: ".$investigados[$i]['entidad']."</font></td>";
					echo "<td bgcolor='#336699' width='20%' ><font color='#ffffff'>Histora: ".$investigados[$i]['his']."</font></td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td><b><font size='3'  align='center'>&nbsp;</font></font></td>";
					echo "<td bgcolor='#336699' align='center' width='15%'><font color='#ffffff'>N Accion</font></td>";
					echo "<td bgcolor='#336699'  align='center' colspan='2'><font color='#ffffff'>Responsable</font></td>";
					echo "<td bgcolor='#336699' align='center' colspan='2'><font color='#ffffff'>Fecha esperada de verifiacacion</font></td>";
					echo "</tr>";

					for($j=0;$j<$num;$j++)
					{
						$row=mysql_fetch_row($err);

						echo "<tr>";
						echo "<td><b><font size='3'  align='center'>&nbsp;</font></font></td>";
						echo "<td bgcolor='#cccccc'  align='center' width='15%'>$row[0]</td>";
						echo "<td bgcolor='#cccccc' align='center' colspan='2'>$row[1]</td>";
						echo "<td bgcolor='#cccccc'  align='center' colspan='2'>$row[2]</td>";
						echo "</tr>";
						$k++;

					}
					echo "</table>";
				}
			}

			//recorro vector de comantarios pintando y buscando la semaforizacion
			for($i=1;$i<=$cerrados[0]['id'];$i++)
			{

				$query =" SELECT cacnum, cacres, cacfver FROM " .$empresa."_000023 where caccon=".$cerrados[$i]['id']." and id_motivo=".$cerrados[$i]['motNum']." and  cacest='INGRESADO' and cacfver > '".date('Y-m-d')."' ";
				$err=mysql_query($query,$conex);
				$num=mysql_num_rows($err);
				//echo $query;

				if  ($num >0) //se llenan los vectores según el estado del comentario
				{
					echo "<table align='center' border='1' width='100%'>";
					echo "<tr>";
					echo "<td bgcolor='#336699'width='15%' ><a href='investigacion.php?idMot=".$cerrados[$i]['motNum']."&idCom=".$cerrados[$i]['idCom']."' align='right'><font color='#ffffff'>N Comentario: ".$cerrados[$i]['num']."-".$cerrados[$i]['motNum']."</font></a></td>";
					echo "<td  bgcolor='#336699' width='35%'  colspan='2'><font color='#ffffff'>Fecha/Lugar de origen: ".$cerrados[$i]['fecCom']."&nbsp;".$cerrados[$i]['lugCom']."</font></td>";
					echo "<td bgcolor='#336699'  width='15%' ><font color='#ffffff'>Fecha envio: ".$cerrados[$i]['motEnv']." </font></td>";
					echo "<td bgcolor='#336699' width='15%' ><font color='#ffffff'>Entidad: ".$cerrados[$i]['entidad']."</font></td>";
					echo "<td bgcolor='#336699'width='20%' ><font color='#ffffff'>Historia: ".$cerrados[$i]['his']."</font></td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td><b><font size='3'  align='center'>&nbsp;</font></font></td>";
					echo "<td bgcolor='#336699' align='center' width='15%'><font color='#ffffff'>N Accion</td></font>";
					echo "<td bgcolor='#336699'  align='center' colspan='2'><font color='#ffffff'>Responsable</font></td>";
					echo "<td bgcolor='#336699' align='center' colspan='2'><font color='#ffffff'>Fecha esperada de verifiacacion</font></td>";
					echo "</tr>";

					for($j=0;$j<$num;$j++)
					{
						$k++;
						$row=mysql_fetch_row($err);


						echo "<tr>";
						echo "<td><b><font size='3'  align='center'>&nbsp;</font></font></td>";
						echo "<td bgcolor='#cccccc'  align='center' width='15%'>$row[0]</td>";
						echo "<td bgcolor='#cccccc' align='center' colspan='2'>$row[1]</td>";
						echo "<td bgcolor='#cccccc'  align='center' colspan='2'>$row[2]</td>";
						echo "</tr>";

					}
					echo "</table>";
				}
			}
		}

		if ($k==0)
		{
			echo "<CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
			echo "<table align='center' border=0 bordercolor=#000080 width=340 style='border:solid;'>";
			echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>EL AREA NO TIENE ACCIONES PENDIENTES POR VERIFICAR </td><tr>";
			echo "</table></fieldset></center></br></br>";
		}

		////////////////////////////////////COMENTARIOS CERRADOS/////////////////////////////////////////////////////
		echo "</br></br><center><b><font size='4'><font color='#00008B'>ACCIONES IMPLEMENTADAS</b></font></font></center></br>";
		$k=0;
		if ($investigados[0]['id'] > 0 or $cerrados[0]['id'] > 0) // Si el paciente tiene comentarios en estado cerrados se buscan las acciones para mostrar
		{


			//recorro vector de comantarios pintando y buscando la semaforizacion
			for($i=1;$i<=$investigados[0]['id'];$i++)
			{
				$query =" SELECT cacnum, cacres, cacfver FROM " .$empresa."_000023 where Caccon=".$investigados[$i]['id']." and id_motivo=".$investigados[$i]['motNum']." and cacfver <= '".date('Y-m-d')."' ";
				$err=mysql_query($query,$conex);
				$num=mysql_num_rows($err);
				//echo $query;

				if  ($num >0) //se llenan los vectores según el estado del comentario
				{
					echo "<table align='center' border='1' width='1000'>";
					echo "<tr>";
					echo "<td bgcolor='#336699' width='15%' ><a href='investigacion.php?idMot=".$investigados[$i]['motNum']."&idCom=".$investigados[$i]['idCom']."' align='right'><font color='#ffffff'>N Comentario: ".$investigados[$i]['num']."-".$investigados[$i]['motNum']."</a></font ></td>";
					echo "<td  bgcolor='#336699' width='35%'  colspan='2'><font color='#ffffff'>Fecha/Lugar de origen: ".$investigados[$i]['fecCom']."&nbsp;".$investigados[$i]['lugCom']."</font ></td>";
					echo "<td bgcolor='#336699'  width='15%' ><font color='#ffffff'>Fecha envio: ".$investigados[$i]['motEnv']." </font ></td>";
					echo "<td bgcolor='#336699' width='15%' ><font color='#ffffff'>Entidad: ".$investigados[$i]['entidad']."</font ></td>";
					echo "<td bgcolor='#336699' width='20%' ><font color='#ffffff'>Historia: ".$investigados[$i]['motCla']."</font ></td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td><b><font size='3'  align='center'>&nbsp;</font></font></td>";
					echo "<td bgcolor='#336699' align='center' width='15%'><font color='#ffffff'N Accion</font></td>";
					echo "<td bgcolor='#336699'  align='center' colspan='2'><font color='#ffffff'Responsable</font></td>";
					echo "<td bgcolor='#336699' align='center' colspan='2'><font color='#ffffff'Fecha esperada de verifiacacion</font></td>";
					echo "</tr>";

					for($j=0;$j<$num;$j++)
					{
						$k++;
						$row=mysql_fetch_row($err);

						echo "<tr>";
						echo "<td><b><font size='3'  align='center'>&nbsp;</font></font></td>";
						echo "<td bgcolor='#cccccc'  align='center' width='15%'>$row[0]</td>";
						echo "<td bgcolor='#cccccc' align='center' colspan='2'>$row[1]</td>";
						echo "<td bgcolor='#cccccc'  align='center' colspan='2'>$row[2]</td>";
						echo "</tr>";

					}
					echo "</table></br></br>";
				}
			}

			//recorro vector de comantarios pintando y buscando la semaforizacion
			for($i=1;$i<=$cerrados[0]['id'];$i++)
			{
				$query =" SELECT cacnum, cacres, cacfver FROM " .$empresa."_000023 where Caccon=".$cerrados[$i]['id']." and id_motivo=".$cerrados[$i]['motNum']."  and cacfver <= '".date('Y-m-d')."' ";

				$err=mysql_query($query,$conex);
				$num=mysql_num_rows($err);
				//echo $query;

				if  ($num >0) //se llenan los vectores según el estado del comentario
				{
					echo "<table align='center' border='1'>";
					echo "<tr>";
					echo "<td bgcolor='#336699' width='15%' ><a href='investigacion.php?idMot=".$cerrados[$i]['motNum']."&idCom=".$cerrados[$i]['idCom']."' align='right'><font color='#ffffff'>N Comentario: ".$cerrados[$i]['num']."-".$cerrados[$i]['motNum']."</a></font></td>";
					echo "<td  bgcolor='#336699' width='35%'  colspan='2'><font color='#ffffff'>Fecha/Lugar de origen: ".$cerrados[$i]['fecCom']."&nbsp;".$cerrados[$i]['lugCom']."</font></td>";
					echo "<td bgcolor='#336699' width='15%' ><font color='#ffffff'>Fecha envio: ".$cerrados[$i]['motEnv']." </font></td>";
					echo "<td bgcolor='#336699' width='15%' ><font color='#ffffff'>Entidad: ".$cerrados[$i]['entidad']."</font></td>";
					echo "<td bgcolor='#336699' width='20%' ><font color='#ffffff'>Historia: ".$cerrados[$i]['his']."</font></td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td><b><font size='3'  align='center' color='#ffffff'>&nbsp;</font></font></td>";
					echo "<td bgcolor='#336699' align='center' width='15%' ><font color='#ffffff'>N Accion</font></td>";
					echo "<td bgcolor='#336699'  align='center' colspan='2' ><font color='#ffffff'>Responsable</font></td>";
					echo "<td bgcolor='#336699' align='center' colspan='2' ><font color='#ffffff'>Fecha esperada de verifiacacion</font></td>";
					echo "</tr>";

					for($j=0;$j<$num;$j++)
					{
						$k++;
						$row=mysql_fetch_row($err);

						echo "<tr>";
						echo "<td><b><font size='3'  align='center'>&nbsp;</font></font></td>";
						echo "<td bgcolor='#cccccc'  align='center' width='15%'>$row[0]</td>";
						echo "<td bgcolor='#cccccc' align='center' colspan='2'>$row[1]</td>";
						echo "<td bgcolor='#cccccc'  align='center' colspan='2'>$row[2]</td>";
						echo "</tr>";

					}
					echo "</table></br></br>";
				}
			}



		}
		if ($k==0)
		{
			echo "<CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
			echo "<table align='center' border=0 bordercolor=#000080 width=340 style='border:solid;'>";
			echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>EL AREA NO TIENE ACCIONES VERIFICADAS </td><tr>";
			echo "</table></fieldset></center></br></br>";
		}
	}

	// Busqueda de comentario entre dos fechas

	if (isset ($bandera1))
	{
		echo "<center><font color='#00008B'>INGRESE POR FAVOR EL RANGO DE FECHAS EN EL CUAL DESEA CONSULTAR LOS COMENTARIOS:</font></center></BR>";

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
		echo "<input type='hidden' name='bandera1' value='1' />";
		echo "<input type='hidden' name='area' value='$area' />";
		if (isset($lista))
		echo "<input type='hidden' name='lista' value='1' />";
		echo "<td align=center colspan=14><font size='2'  align=center face='arial'><input type='submit' name='aceptar' value='BUSCAR' ></td></tr>";
		echo "</TABLE>";
		echo "</td>";
		echo "</tr>	";
		echo "</form>";
		echo "</fieldset>";

}

}

?>

</body>

</html>







