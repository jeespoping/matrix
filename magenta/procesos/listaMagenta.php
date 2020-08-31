<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<title>Programa de comentarios y sugerencias</title>
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
</script>
<SCRIPT LANGUAGE="JavaScript1.2">
<!--
function onLoad() {
	loadMenus();
}

$(document).ready(function() {

	$("#fecIni, #fecFin").datepicker({
       showOn: "button",
       buttonImage: "../../images/medical/root/calendar.gif",
       buttonImageOnly: true,
       maxDate:"+1D"
    });
});

</SCRIPT>


</head>
<body>
<?php
include_once("conex.php"); 

/*********************************************************************************************************************************
 * 	LISTA DE COMENTARIOS DEL MES
 * 
 * Este programa permite realizar la busqueda de comentarios por mes o por unidad, por causa por respuesta o por entidad
 * 
 * @name  matrix\magenta\procesos\listaMagenta.php
 * @author Carolina Castaño Portilla.
 * 
 * @created 2006-04-10
 * @version 2006-01-05
 * 
 * @modified 2006-07-10 se agrega de la 290 a 314  busqueda de comentario
 * @modified 2007-01-05  Se realiza documentacion del programa y se agrega busqueda por entidad
 * @modified 2007-01-18  Se cambia el query sobre la busqueda de un comentario especifico, para que traiga el mas reciente
 * 
 * @table magenta_000017, select, 
 * @table magenta_000018, select, 
 * @table magenta_000019, select
 * @table magenta_000020, select
 * @table magenta_000021, select
 * 
 *  @wvar $abrir, indica que programa debe abrir para la consulta de un comentario segun su estado
 *  @wvar $acoDir, direccion del acompanana
 *  @wvar $acoEma, email del acompanante
 *  @wvar $acoNom, nombre del acompananate
 *  @wvar $acoTel, telefono del acompanante
 *  @wvar $asignados, vector con la lista de comentarios asignados del mes
 *  @wvar $aut, persona que ingreso el comentario
 *  @wvar $bandera1, indica cual estado es el que se quiere consultar
 *  @wvar $cerrados,  vector que guarda los comentarios cerrados
 *  @wvar $color, dependiendo si el comentario es de agrado o de desagrado
 *  @wvar $com, indica si se ingreso un comentario especifico para buscar
 *  @wvar $contador sieve como contador temporal del tamaño de los vectores de comentarios para cda estado
 *  @wvar $est estado del comentario 
 *  @wvar $fecIni fecha incial de l busqueda de comentario
 *  @wvar $fecFin fecha final de l busqueda de comentario
 *  @wvar $idCom id del comentario en tabla 000017
 *  @wvar $ingresados vector de comentarios en estado ingresado para las fechas dadas
 *  @wvar $inicial, vector utilizado en explodes
 *  @wvar $investigados, vector de comentarios investigados en las fechas dadas
 *  @wvar $numMot, numero del motivo
 *  @wvar $semaforo, composicion entre el borde y relleno del color del semaforo del motivo
 *  @wvar $senal, indica si se encontraron comentarios en las fechas dadas
*/
/************************************************************************************************************************* 
  Actualizaciones:
 			2016-05-02 (Arleyda Insignares C.)
 						-Se Modifican los campos de calendario fecha inicial y fecha final con utilidad jquery y se elimina
						 uso de Zapatec Calendar por errores en la clase.
						-Se cambia encabezado con ultimo diseño y se configura color de titulos con la clase 'fila1'
*************************************************************************************************************************/

$wautor   ="Carolina Castano P.";
$wversion ='2016-05-02';
$wactualiz='2016-05-02';

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
	$senal='0';

	/////////////////////////////////////////////////acciones concretas///////////////////////////////////

	//Iniciacion de fechas
	IF (!isset($fecIni))
	{
		$fecIni=date('Y').'-'.date('m').'-01';
		$fecFin=date('Y-m-d');
	}

	// Busco los motivos y los voy guardando en vectores segun el estado

	//inicializo el tamaño de cada vector
	$ingresados[0]['id']=0;
	$asignados[0]['id']=0;
	$investigados[0]['id']=0;
	$cerrados[0]['id']=0;

	$query ="SELECT Ccoid, ccoori, ccofori, ccofrec, ccohis, ccoest, cconum FROM " .$empresa."_000017 where fecha_data between '".$fecIni."'  and '".$fecFin."' ";
	$err=mysql_query($query,$conex);
	$num=mysql_num_rows($err);
	//echo $query;
	if  ($num >0) //se llenan los vectores según el estado del comentario
	{
		for($i=0;$i<$num;$i++)
		{
			$row=mysql_fetch_row($err);

			switch ($row[5])
			{
				case 'INGRESADO':
				$contador=$ingresados[0]['id'];
				$contador++;
				$ingresados[$contador]['id']=$row[0];
				$ingresados[$contador]['lugOri']=$row[1];
				$ingresados[$contador]['fecOri']=$row[2];
				$ingresados[$contador]['fecRec']=$row[3];
				$ingresados[$contador]['aut']=$row[4];
				$ingresados[$contador]['num']=$row[6];
				$ingresados[0]['id']=$contador;
				break;
				case 'ASIGNADO':
				$contador=$asignados[0]['id'];
				$contador++;
				$asignados[$contador]['id']=$row[0];
				$asignados[$contador]['lugOri']=$row[1];
				$asignados[$contador]['fecOri']=$row[2];
				$asignados[$contador]['fecRec']=$row[3];
				$asignados[$contador]['aut']=$row[4];
				$asignados[$contador]['num']=$row[6];
				$asignados[0]['id']=$contador;
				break;
				case 'INVESTIGADO':
				$contador=$investigados[0]['id'];
				$contador++;
				$investigados[$contador]['id']=$row[0];
				$investigados[$contador]['lugOri']=$row[1];
				$investigados[$contador]['fecOri']=$row[2];
				$investigados[$contador]['fecRec']=$row[3];
				$investigados[$contador]['aut']=$row[4];
				$investigados[$contador]['num']=$row[6];
				$investigados[0]['id']=$contador;
				break;
				case 'CERRADO':
				$contador=$cerrados[0]['id'];
				$contador++;
				$cerrados[$contador]['id']=$row[0];
				$cerrados[$contador]['lugOri']=$row[1];
				$cerrados[$contador]['fecOri']=$row[2];
				$cerrados[$contador]['fecRec']=$row[3];
				$cerrados[$contador]['aut']=$row[4];
				$cerrados[$contador]['num']=$row[6];
				$cerrados[0]['id']=$contador;
				break;
			}
		}
	}else
	{
		$senal='3'; //indica que no hay comentarios en las fechas seleccionadas
	}


	/////////////////////////////////////////////////encabezado general///////////////////////////////////

	if (!isset($bandera1))
	{
		/*echo "<table align='right' >\n" ;
		echo "<tr>" ;
		echo "<td VALIGN=TOP NOWRAP>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<A HREF='javascript:window.showMenu(window.mWhite1);' onMouseOver='window.showMenu(window.mWhite1);'><font color=\"#D02090\" size=\"4\"><b>Menu</A>&nbsp/</b></font></td>";
		echo "<td><b><font size=\"4\"><A HREF='ayuda.mht' target='new'><font color=\"#D02090\">Ayuda</font></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></font></td>" ;
		echo "</tr>" ;
		echo "</table></br></br>" ;*/

  ?>
  <SCRIPT LANGUAGE="JavaScript1.2">

  if (document.all) {

  	window.myMenu = new Menu();
  	myMenu.addMenuItem("my menu item A");
  	myMenu.addMenuItem("my menu item B");
  	myMenu.addMenuItem("my menu item C");
  	myMenu.addMenuItem("my menu item D");

  	window.mWhite1 = new Menu("White");
  	mWhite1.addMenuItem("Enviar correos", "self.window.location='pagina1.php'");
  	mWhite1.addMenuItem("Ingreso de comentarios", "self.window.location='pagina1.php'");

  	mWhite1.bgColor = "#ADD8E6";
  	mWhite1.menuItemBgColor = "white";
  	mWhite1.menuHiliteBgColor = "#336699";

  	myMenu.writeMenus();
  }

	</SCRIPT>
 <?php
	}else
	{
		/*echo "<table align='right' >\n" ;
		echo "<tr>" ;
		echo "<td VALIGN=TOP NOWRAP>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<A HREF='javascript:window.showMenu(window.mWhite1);' onMouseOver='window.showMenu(window.mWhite1);'><font color=\"#D02090\" size=\"4\"><b>Menu</A>&nbsp/</b></font></td>";
		echo "<td><b><font size=\"4\"><A HREF='ayuda.htm' target='new'><font color=\"#D02090\">Ayuda</font></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></font></td>" ;
		echo "</tr>" ;
		echo "</table></br></br>" ;
*/
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

	/////////////////////////////////////////////////////////encabezado personal///////////////////////////////////////////////////
	echo "<br></br>";
    echo "<div align='center'><div align='center' class='fila1' style='width:520px;'><font size='4'><b>CONSULTA E INGRESO DE COMENTARIOS</b></font></div></div></BR>";
	//echo "<center><b><font size=\"4\"><A HREF='listaMagenta.php'><font color=\"#D02090\">CONSULTA E INGRESO DE COMENTARIOS</font></a></b></font></center>\n" ;
	//echo "<center><b><font size=\"2\"><font color=\"#D02090\"> listaMagenta.php </font></font></center></br></br></br>\n" ;
	echo "\n" ;

	///////////////////////////////////////////// Busqueda de comentario un comentario especifico /////////////////////////////////

	echo "<form NAME='rapida'  METHOD='POST'>";
	echo "<br></br>";
	echo "<table align=center>";
	echo "<tr>";
	echo "<td align=center class='fila1'><font face='arial' color='#00008B' ><b>INGRESE EL NUMERO DEL COMENTARIO QUE DESEA BUSCAR:</b>&nbsp</td>";
	echo "<td align=center bgcolor='#336699' ><font size='2'  align=center face='arial' color='#ffffff'><input type='text' name='idCom'  size='6'></td>";
	echo "<td align=center class='fila1'><font face='arial' color='#00008B' ><b>MOTIVO:</b>&nbsp</td>";
	echo "<td align=center bgcolor='#336699' ><font size='2'  align=center face='arial' color='#ffffff'><input type='text' name='numMot'  size='6'></td>";
	echo "<td align=center colspan=14><font size='2'  align=center face='arial'><input type='submit' name='aceptar' value='BUSCAR' ></td></tr>";
	echo "<input type='hidden' name='com' value='1' />";
	echo "</TABLE></br>";
	echo "</form>";
	echo "<hr>";
	echo "<br>";

	//com me indica que ingresaron un comentario para buscar, y redirecciona a esa pagina
	if (isset ($com))
	{
		$query ="SELECT ccoest, Ccoid FROM " .$empresa."_000017 where cconum=".$idCom." order by fecha_data desc";
		$err=mysql_query($query,$conex);
		@$num=mysql_num_rows($err);

		if  ($num >0) //se llena los valores
		{
			$row=mysql_fetch_row($err);
			$est=$row[0];
			$id=$row[1];
			switch ($est)
			{
				case "INGRESADO": $abrir="asignacion.php";
				break;
				case "ASIGNADO": $abrir="investigacion2.php";
				break;
				case "INVESTIGADO": $abrir="respuesta.php";
				break;
				case "CERRADO": $abrir="respuesta.php";
				break;
			}
			if(!isset($numMot)or $numMot=='')
			$numMot=1;

			  ?>
  			<SCRIPT LANGUAGE="JavaScript1.2">
  			<?php
  			echo "document.rapida.idCom.value=".$id.";";
  			echo "document.rapida.numMot.value=".$numMot.";";
  			echo "document.rapida.action='".$abrir."';";
  			?>
  			document.rapida.submit();
			</SCRIPT>
			<?php
		}
	}

	/////////////////////////////////////////////////inicialización de variables//////////////////////////
	/////////////////////////////////////////////////presentación general///////////////////////////////////

	echo "<table align='center' border='0' width='700'>";
	echo "<tr align='CENT'>";
	echo "<ul >";

	if (!ISSET ($bandera1) and $senal !=3)//SI SE HAN ECONTRADO COMENTARIOS Y NO SE HA SELECCIONADO OPCION
	{
		echo "<center><font color='#00008B'><b>SELECCIONE LA OPCION DE BUSQUEDA DESEADA:</font></b></center></BR>";



		if ($ingresados[0]['id'] > 0)
		echo "<li ><a href='listaMagenta.php?bandera1=1&fecIni=$fecIni&fecFin=$fecFin' align='CENTER'>COMENTARIOS INGRESADOS (".$ingresados[0]['id'].")</a></li></br></br>";
		else
		echo "<li ><font color='#0000ff'>COMENTARIOS INGRESADOS (".$ingresados[0]['id'].")</font></li></br></br>";

		if ($asignados[0]['id'] > 0)
		echo "<li><a href='listaMagenta.php?bandera1=2&fecIni=$fecIni&fecFin=$fecFin' align='CENTER'>COMENTARIOS ASIGNADOS (esperando respuesta del coordinador) (".$asignados[0]['id'].")</a></li></br></br>";
		else
		echo "<li><font color='#0000ff'>COMENTARIOS ASIGNADOS (".$asignados[0]['id'].")</font></li></br></br>";

		if ($investigados[0]['id'] > 0)
		echo "<li><a href='listaMagenta.php?bandera1=3&fecIni=$fecIni&fecFin=$fecFin' align='CENTER'>COMENTARIOS PENDIENTES DE RESPUESTA AL USUARIO POR MAGENTA (".$investigados[0]['id'].")</a></li></br></br>";
		else
		echo "<li><font color='#0000ff'>COMENTARIOS PENDIENTES DE RESPUESTA AL USUARIO POR MAGENTA (".$investigados[0]['id'].")</font></li></br></br>";

		if ($cerrados[0]['id'] > 0)
		echo "<li><a href='listaMagenta.php?bandera1=4&fecIni=$fecIni&fecFin=$fecFin' align='CENTER'>COMENTARIOS CERRADOS DEL MES (".$cerrados[0]['id'].")</a></li></br></br>";
		else
		echo "<li><font color='#0000ff'>COMENTARIOS CERRADOS DEL MES (".$cerrados[0]['id'].")</font></li></br></br>";

		echo "<li><a href='consultas.php?lista=1' align='CENTER'>COMENTARIOS DE UNA UNIDAD </a></li></br></br>";
		echo "<li><a href='consultas.php?lista=2' align='CENTER'>COMENTARIOS POR CAUSA </a></li></br></br>";
		echo "<li><a href='consultas.php?lista=3' align='CENTER'>COMENTARIOS POR RESPUESTA </a></li></br></br>";
		echo "<li><a href='consultas.php?lista=4' align='CENTER'>COMENTARIOS POR ENTIDAD </a></li></br></br>";

	}
	echo "<li><font color='#0000ff'>COMENTARIOS ENTRE DOS FECHAS:</font></li></br></br>";
	echo "</ul>";
	echo "</tr>	";
	echo "</table>	";

	if ($senal ==3)//NO SE HAN ECNONTRADO COMENTARIOS EN EL RANGO DE FECHAS
	{
		echo "<CENTER>";
		echo "<table align='center' border=1 bordercolor=#000080 width=340 >";
		echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b> NO EXISTE NINGUN COMENTARIO REGISTRADO ESTE MES</td><tr>";
		echo "</table></center></br></br>";
	}

	////////////////////////////////////COMENTARIOS INGRESADOS/////////////////////////////////////////////////////

	if (isset ($bandera1)) //INDICA que estado de comentario fue seleccionado para leer
	{
		if ($bandera1==1) // Si el paciente tiene comentarios en estado ingresados se buscan los motivos para mostrar
		{
			echo "<center><b><font size='4'><font color='#00008B'>COMENTARIOS INGRESADOS</b></font></font></center></br>";
			echo "<table align='center' border='1' width='100%'>";


			for($i=1;$i<=$ingresados[0]['id'];$i++)
			{
				echo "<tr>";
				echo "<td bgcolor='#336699' width='11%' ><a href='detalleComentario.php?idCom=".$ingresados[$i]['id']."' align='right'><font color='#ffffff'>N Comentario: ".$ingresados[$i]['num']."</font></a></td>";
				echo "<td  bgcolor='#336699' width='48%'  colspan='3'><font color='#ffffff'>Fecha/Lugar de origen: ".$ingresados[$i]['fecOri']."&nbsp;".$ingresados[$i]['lugOri']."</font></td>";
				echo "<td bgcolor='#336699'  width='20%' colspan='1'><font color='#ffffff'>Fecha de recepcion: ".$ingresados[$i]['fecRec']." </font></td>";
				echo "<td bgcolor='#336699' width='21%' colspan='3'><font color='#ffffff'>Historia: ".$ingresados[$i]['aut']."</font></td>";
				echo "</tr>";

				$query =" SELECT A.id_Comentario, A.cmonum, A.cmotip, A.cmocla, A.cmofenv, A.cmoest, A.cmofret, A.id_area, B.carnom ,C.id_responsable, D.crenom ";
				$query= $query. " FROM " .$empresa."_000018 A, " .$empresa."_000019 B, " .$empresa."_000020 C, " .$empresa."_000021 D ";
				$query= $query. " where A.id_comentario=".$ingresados[$i]['id']." and A.cmoest<>'INGRESADO' ";
				$query= $query. " and B.Carcod=A.id_area ";
				$query= $query. " and C.id_area=A.id_area and carniv=1 ";
				$query= $query. " and D.Crecod=C.id_responsable ";

				//echo $query;

				$err=mysql_query($query,$conex);
				$num=mysql_num_rows($err);

				if  ($num >0) //se llenan los vectores según el estado del comentario
				{
					echo "<tr>";
					echo "<td><b><font size='3' width='11%' align='center'>&nbsp;</font></font></td>";
					echo "<td bgcolor='#336699' width='8%' align='center' ><font color='#ffffff'>N Motivo</font></td>";
					echo "<td bgcolor='#336699' width='10%' align='center' ><font color='#ffffff'>Tipo</font></td>";
					echo "<td bgcolor='#336699' width='30%' align='center' ><font color='#ffffff'>Clasificacion</font></td>";
					echo "<td bgcolor='#336699' width='20%' align='center' ><font color='#ffffff'>Area responsable</font></td>";
					echo "<td bgcolor='#336699' width='10%' align='center' ><font color='#ffffff'>Fecha Envio</font></td>";
					echo "<td bgcolor='#336699' width='3%' align='center' ><font color='#ffffff'>Sem.</font></td>";
					echo "<td bgcolor='#336699' width='8%' align='center' ><font color='#ffffff'>Estado</font></td>";
					echo "</tr>";


					for($j=0;$j<$num;$j++)
					{
						$row=mysql_fetch_row($err);

						if ($row[2]=='Agrado')
						$color='#EOFFFF';
						else
						$color="#cccccc";

						$semaforo=Semaforo($row[5], $row[4], $row[0], $empresa, $conex, $row[1]);
						$inicial = explode("-",$semaforo);
						$semaforo=$inicial[0];

						echo "<tr>";
						echo "<td><b><font size='3' width='11%' align='center'>&nbsp;</font></font></td>";
						echo "<td bgcolor='$color' width='8%' align='center'>$row[1]</td>";
						echo "<td bgcolor='$color' width='10%' align='center'>$row[2]</td>";
						echo "<td bgcolor='$color' width='30%' align='center'>$row[3]</td>";
						echo "<td bgcolor='$color' width='20%' align='center'>$row[8]</td>";
						echo "<td bgcolor='$color' width='10%' align='center'>$row[4]</td>";



						echo "<td bgcolor='$semaforo' width='3%' align='center'>&nbsp;</td>";
						echo "<td bgcolor='$color' width='8%' align='center'>$row[5]</td>";
						echo "</tr>";
					}
				} // puede que no tenga area aun pero si este ingresado, osea estado ingresado

				$query =" SELECT A.id_comentario, A.cmonum, A.cmotip, A.cmocla, A.cmofenv, A.cmosem, A.cmoest FROM " .$empresa."_000018 A  ";
				$query= $query. " where A.id_comentario=".$ingresados[$i]['id']." and A.cmoest='INGRESADO' ";

				//echo $query;

				$err=mysql_query($query,$conex);
				$num=mysql_num_rows($err);

				if  ($num >0) //se llenan los vectores según el estado del comentario
				{
					echo "<tr>";
					echo "<td><b><font size='3' width='11%' align='center'>&nbsp;</font></font></td>";
					echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>N Motivo</font></td>";
					echo "<td bgcolor='#336699' width='10%' align='center'><font color='#ffffff'>Tipo</font></td>";
					echo "<td bgcolor='#336699' width='30%' align='center'><font color='#ffffff'>Clasificacion</font></td>";
					echo "<td bgcolor='#336699' width='20%' align='center'><font color='#ffffff'>Area responsable</font></td>";
					echo "<td bgcolor='#336699' width='10%' align='center'><font color='#ffffff'>Fecha Envio</font></td>";
					echo "<td bgcolor='#336699' width='3%' align='center'><font color='#ffffff'>Sem.</font></td>";
					echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>Estado</font></td>";
					echo "</tr>";


					for($j=0;$j<$num;$j++)
					{
						$row=mysql_fetch_row($err);

						if ($row[2]=='Agrado')
						$color='#EOFFFF';
						else
						$color="#cccccc";


						echo "<tr>";
						echo "<td><b><font size='3' width='11%' align='center'>&nbsp;</font></font></td>";
						echo "<td bgcolor='$color' width='8%' align='center'>$row[1]</td>";
						echo "<td bgcolor='$color' width='10%' align='center'>$row[2]</td>";
						echo "<td bgcolor='$color' width='30%' align='center'>$row[3]</td>";
						echo "<td bgcolor='$color' width='20%' align='center'>Sin asignar</td>";
						echo "<td bgcolor='$color' width='10%' align='center'>$row[4]</td>";
						echo "<td bgcolor='#008000' width='3%' align='center'>&nbsp;</td>";
						echo "<td bgcolor='$color' width='8%' align='center'>$row[6]</td>";
						echo "</tr>";
					}
				}else
				{
					echo "<tr>";
					echo "<td><b><font size='3' width='11%' align='center'>&nbsp;</font></font></td>";
					echo "<td  width='89%' colspan='7' align='center' ><b><font size='3' >No se han ingresado motivos para este comentario</font></font></td>";
					echo "</tr>";
				}

			}
			echo "</table></br></br>";

		}

		////////////////////////////////////COMENTARIOS ASIGNADOS/////////////////////////////////////////////////////

		if ($bandera1==2) // Si el paciente tiene comentarios en estado ingresados se buscan los motivos para mostrar
		{
			echo "<center><b><font size='4'><font color='#00008B'>COMENTARIOS ASIGNADOS, PENDIENTES DE RESPUESTA POR PARTE DEL COORDINADOR</b></font></font></center></br>";
			echo "<table align='center' border='1' width='100%'>";

			for($i=1;$i<=$asignados[0]['id'];$i++)
			{
				echo "<tr>";
				echo "<td bgcolor='#336699' width='11%' ><a href='asignacion.php?idCom=".$asignados[$i]['id']."' align='right'><font color='#ffffff'>N Comentario:" .$asignados[$i]['num']."</font></a></td>";
				echo "<td  bgcolor='#336699' width='48%'  colspan='3'><font color='#ffffff'>Fecha/Lugar de origen :".$asignados[$i]['fecOri']."&nbsp;".$asignados[$i]['lugOri']."</font></td>";
				echo "<td bgcolor='#336699'  width='30%' colspan='1'><font color='#ffffff'>Fecha de recepcion: ".$asignados[$i]['fecRec']." </font></td>";
				echo "<td bgcolor='#336699' width='11%' colspan='3'><font color='#ffffff'>Historia: ".$asignados[$i]['aut']."</font></td>";
				echo "</tr>";

				$query =" SELECT A.id_Comentario, A.cmonum, A.cmotip, A.cmocla, A.cmofenv, A.cmoest, A.id_area, B.carnom ,C.id_responsable, D.crenom ";
				$query= $query. " FROM " .$empresa."_000018 A, " .$empresa."_000019 B, " .$empresa."_000020 C, " .$empresa."_000021 D ";
				$query= $query. " where A.id_comentario=".$asignados[$i]['id']." ";
				$query= $query. " and B.carcod=A.id_area ";
				$query= $query. " and C.id_area=A.id_area and carniv=1 and C.relest='on' ";
				$query= $query. " and D.crecod=C.id_responsable ";
				$err=mysql_query($query,$conex);
				$num=mysql_num_rows($err);

				if  ($num >0) //se llenan los vectores según el estado del comentario
				{
					echo "<tr>";
					echo "<td><b><font size='3' width='11%' align='center'>&nbsp;</font></font></td>";
					echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>N Motivo</font></td>";
					echo "<td bgcolor='#336699' width='10%' align='center'><font color='#ffffff'>Tipo</font></td>";
					echo "<td bgcolor='#336699' width='30%' align='center'><font color='#ffffff'>Clasificacion</font></td>";
					echo "<td bgcolor='#336699' width='20%' align='center'><font color='#ffffff'>Area responsable</font></td>";
					echo "<td bgcolor='#336699' width='10%' align='center'><font color='#ffffff'>Fecha Envio</font></td>";
					echo "<td bgcolor='#336699' width='3%' align='center'><font color='#ffffff'>Sem.</font></td>";
					echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>Estado</font></td>";
					echo "</tr>";


					for($j=0;$j<$num;$j++)
					{
						$row=mysql_fetch_row($err);

						if ($row[2]=='Agrado')
						$color='#EOFFFF';
						else
						$color="#cccccc";

						$semaforo=Semaforo($row[5], $row[4], $row[0], $empresa, $conex, $row[1]);
						$inicial = explode("-",$semaforo);
						$semaforo=$inicial[0];

						echo "<tr>";
						echo "<td><b><font size='3' width='11%' align='center'>&nbsp;</font></font></td>";
						echo "<td bgcolor='$color' width='8%' align='center'>$row[1]</td>";
						echo "<td bgcolor='$color' width='10%' align='center'>$row[2]</td>";
						echo "<td bgcolor='$color' width='30%' align='center'>$row[3]</td>";
						echo "<td bgcolor='$color' width='20%' align='center'>$row[7]</td>";
						echo "<td bgcolor='$color' width='10%' align='center'>$row[4]</td>";


						echo "<td bgcolor='$semaforo' width='10%' align='center'>&nbsp;</td>";
						echo "<td bgcolor='$color' width='8%' align='center'>$row[5]</td>";
						echo "</tr>";
					}
				}else
				{
					DisplayError();
				}
			}

			echo "</table></br></br>";

		}

		////////////////////////////////////COMENTARIOS INVESTIGADOS/////////////////////////////////////////////////////

		if ($bandera1==3) // Si el paciente tiene comentarios en estado ingresados se buscan los motivos para mostrar
		{
			echo "<center><b><font size='4'><font color='#00008B'>COMENTARIOS PENDIENTES DE RESPUESTA AL USUARIO POR MAGENTA</b></font></font></center></br>";
			echo "<table align='center' border='1' width='100%'>";


			for($i=1;$i<=$investigados[0]['id'];$i++)
			{
				echo "<tr>";
				echo "<td bgcolor='#336699' width='12%' ><a href='respuesta.php?idCom=".$investigados[$i]['id']."' align='right'><font color='#ffffff'>N Comentario: ".$investigados[$i]['num']."</font></a></td>";
				echo "<td  bgcolor='#336699' width='28%'  colspan='3'><font color='#ffffff'>Fecha/Lugar de origen :".$investigados[$i]['fecOri']."&nbsp;".$investigados[$i]['lugOri']."</font></td>";
				echo "<td bgcolor='#336699'  width='30%' colspan='1'><font color='#ffffff'>Fecha de recepcion: ".$investigados[$i]['fecRec']." </font></td>";
				echo "<td bgcolor='#336699' width='40%' colspan='3'><font color='#ffffff'>Historia: ".$investigados[$i]['aut']."</font></td>";
				echo "</tr>";

				$query =" SELECT A.id_Comentario, A.cmonum, A.cmotip, A.cmocla, A.cmofenv, A.cmoest, A.cmofret, A.id_area, B.carnom ,C.id_responsable, D.crenom ";
				$query= $query. " FROM " .$empresa."_000018 A, " .$empresa."_000019 B, " .$empresa."_000020 C, " .$empresa."_000021 D ";
				$query= $query. " where A.id_comentario=".$investigados[$i]['id']." ";
				$query= $query. " and B.carcod=A.id_area ";
				$query= $query. " and C.id_area=A.id_area and carniv=1 ";
				$query= $query. " and D.crecod=C.id_responsable ";
				$err=mysql_query($query,$conex);
				$num=mysql_num_rows($err);



				if  ($num >0) //se llenan los vectores según el estado del comentario
				{
					echo "<tr>";
					echo "<td><b><font size='3' width='11%' align='center'>&nbsp;</font></font></td>";
					echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>N Motivo</font></td>";
					echo "<td bgcolor='#336699' width='10%' align='center'><font color='#ffffff'>Tipo</font></td>";
					echo "<td bgcolor='#336699' width='30%' align='center'><font color='#ffffff'>Clasificacion</font></td>";
					echo "<td bgcolor='#336699' width='20%' align='center'><font color='#ffffff'>Area responsable</font></td>";
					echo "<td bgcolor='#336699' width='10%' align='center'><font color='#ffffff'>Fecha Envio</font></td>";
					echo "<td bgcolor='#336699' width='3%' align='center'><font color='#ffffff'>Sem.</font></td>";
					echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>Estado</font></td>";
					echo "</tr>";


					for($j=0;$j<$num;$j++)
					{
						$row=mysql_fetch_row($err);

						if ($row[2]=='Agrado')
						$color='#EOFFFF';
						else
						$color="#cccccc";

						$semaforo=Semaforo($row[5], $row[4], $row[0], $empresa, $conex, $row[1]);
						$inicial = explode("-",$semaforo);
						$semaforo=$inicial[0];

						echo "<tr>";
						echo "<td><b><font size='3' width='11%' align='center'>&nbsp;</font></font></td>";
						echo "<td bgcolor='$color' width='8%' align='center'>$row[1]</td>";
						echo "<td bgcolor='$color' width='10%' align='center'>$row[2]</td>";
						echo "<td bgcolor='$color' width='30%' align='center'>$row[3]</td>";
						echo "<td bgcolor='$color' width='20%' align='center'>$row[8]</td>";
						echo "<td bgcolor='$color' width='10%' align='center'>$row[4]</td>";


						echo "<td bgcolor='$semaforo' width='10%' align='center'>&nbsp;</td>";
						echo "<td bgcolor='$color' width='8%' align='center'>$row[5]</td>";
						echo "</tr>";
					}
				}else
				{
					DisplayError();
				}
			}

			echo "</table></br></br>";

		}
		////////////////////////////////////COMENTARIOS CERRADOS/////////////////////////////////////////////////////

		if ($bandera1==4) // Si el paciente tiene comentarios en estado ingresados se buscan los motivos para mostrar
		{
			echo "<center><b><font size='4'><font color='#00008B'>COMENTARIOS CERRADOS</b></font></font></center></br>";
			echo "<table align='center' border='1' width='100%'>";

			for($i=1;$i<=$cerrados[0]['id'];$i++)
			{
				echo "<tr>";
				echo "<td bgcolor='#336699' width='12%' ><a href='respuesta.php?idCom=".$cerrados[$i]['id']."' align='right'><font color='#ffffff'>N Comentario: ".$cerrados[$i]['num']."</font></a></td>";
				echo "<td  bgcolor='#336699' width='28%'  colspan='3'><font color='#ffffff'>Fecha/Lugar de origen :".$cerrados[$i]['fecOri']."&nbsp;".$cerrados[$i]['lugOri']."</font></td>";
				echo "<td bgcolor='#336699'  width='30%' colspan='1'><font color='#ffffff'>Fecha de recepcion: ".$cerrados[$i]['fecRec']." </font></td>";
				echo "<td bgcolor='#336699' width='40%' colspan='3'><font color='#ffffff'>Historia: ".$cerrados[$i]['aut']."</font></td>";
				echo "</tr>";

				$query =" SELECT A.id_Comentario, A.cmonum, A.cmotip, A.cmocla, A.cmofenv, A.cmoest, A.cmofret, A.id_area, B.carnom ,C.id_responsable, D.crenom ";
				$query= $query. " FROM " .$empresa."_000018 A, " .$empresa."_000019 B, " .$empresa."_000020 C, " .$empresa."_000021 D ";
				$query= $query. " where A.id_comentario=".$cerrados[$i]['id']." ";
				$query= $query. " and B.carcod=A.id_area ";
				$query= $query. " and C.id_area=A.id_area and carniv=1 ";
				$query= $query. " and D.crecod=C.id_responsable ";
				$err=mysql_query($query,$conex);
				$num=mysql_num_rows($err);

				if  ($num >0) //se llenan los vectores según el estado del comentario
				{
					echo "<tr>";
					echo "<td><b><font size='3' width='11%' align='center'>&nbsp;</font></font></td>";
					echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>N Motivo</font></td>";
					echo "<td bgcolor='#336699' width='10%' align='center'><font color='#ffffff'>Tipo</font></td>";
					echo "<td bgcolor='#336699' width='30%' align='center'><font color='#ffffff'>Clasificacion</font></td>";
					echo "<td bgcolor='#336699' width='20%' align='center'><font color='#ffffff'>Area responsable</font></td>";
					echo "<td bgcolor='#336699' width='10%' align='center'><font color='#ffffff'>Fecha Envio</font></td>";
					echo "<td bgcolor='#336699' width='3%' align='center'><font color='#ffffff'>Sem.</font></td>";
					echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>Estado</font></td>";
					echo "</tr>";


					for($j=0;$j<$num;$j++)
					{
						$row=mysql_fetch_row($err);

						if ($row[2]=='Agrado')
						$color='#EOFFFF';
						else
						$color="#cccccc";

						$semaforo=Semaforo($row[5], $row[4], $row[0], $empresa, $conex, $row[1]);
						$inicial = explode("-",$semaforo);
						$semaforo=$inicial[0];

						echo "<tr>";
						echo "<td><b><font size='3' width='11%' align='center'>&nbsp;</font></font></td>";
						echo "<td bgcolor='$color' width='8%' align='center'>$row[1]</td>";
						echo "<td bgcolor='$color' width='10%' align='center'>$row[2]</td>";
						echo "<td bgcolor='$color' width='30%' align='center'>$row[3]</td>";
						echo "<td bgcolor='$color' width='20%' align='center'>$row[8]</td>";
						echo "<td bgcolor='$color' width='10%' align='center'>$row[4]</td>";


						echo "<td bgcolor='$semaforo' width='10%' align='center'>&nbsp;</td>";
						echo "<td bgcolor='$color' width='8%' align='center'>$row[5]</td>";
						echo "</tr>";
					}
				}else
				{
					DisplayError();
				}
			}

			echo "</table></br></br>";

		}

	}else //no se ha seleccionado ninguna opcion para leer, muestro calendario de fechas
	{
		// Busqueda de comentario entre dos fechas

		echo "<fieldset border='0' align=center></br>";
		echo "<form NAME='ingreso' ACTION='' METHOD='POST'>";
		echo "<table align='center'>";
		echo "<tr>";
		$cal="calendario('fecIni','1')";
		echo "<td align=center bgcolor=#336699 ><font size=3  face='arial' color='#ffffff'>FECHA INICIAL:</font><input type='text' readonly='readonly' id='fecIni' name='fecIni' value='".date("Y-m")."-01' class=tipo3> </td>";
		echo "<td align=center bgcolor=#336699><font size=3  face='arial' color='#ffffff'>FECHA FINAL:</font><input type='text' readonly='readonly' id='fecFin' name='fecFin' value='".date("Y-m-d")."' class=tipo3></td>";
		echo "</td>";
		echo "</tr></TABLE></br>";
		echo "<TABLE align=center><tr>";
		echo "<tr>";
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







