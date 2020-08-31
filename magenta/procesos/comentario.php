<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<title>PROGRAMA DE COMENTARIOS Y SUGERENCIAS</title>

<script src="efecto.php"></script>
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
 * Comentarios de un paciente
 * 
 * Este programa muestra los comentarios que ha realizado un paciente seleccionado para ser consultados y 
 * ofrece la posibilidad de crear un nuevo comentario. Debe llamarse a travez del script pagina1.php porque
 * siempre recibira por invocacion las variables doc y tipdoc es decir documento de identidad y tipo completo (CC-Cedula de ciudadania)
 * tambien la historia (his), el servicio (ser)y la entidad responsable (res)que pueden estar inicializados en:'-'
 * *
 * @name matrix\magenta\procesos\comentario.php
 * @author ccastano
 * @created 2006-04-03
 * @version 2006-01-03
 * 
 * @modified 2006-04-20  Mejora de la interfaz de usuario
 * @modified 2006-01-03  Se realiza documentacion del programa
 
 Actualizacion: Se cambio la ruta y el nombre del include_once(magenta/semaforo.php) Viviana Rodas 14-05-2012
 * 
 * @table magenta_000008, select para ver si el paciente pertenece a afinidad es AAA o BBB o VIP
 * @table magenta_000016, select
 * @table magenta_000017, select
 * @table magenta_000018, select
 * @table magenta_000019, select
 * @table magenta_000020, select
 * @table magenta_000021, select
 * 
 * @wvar $asignados, vector que contiene los comentarios del paciente en estado asignado
 * @wvar $cadena, cadena para enviar variables por get a los demas programas que se enlazan
 * @wvar $cerrados, vector que contiene los comentarios del paciente en estado cerrado
 * @wvar $color, color con el que se mostrara el tipo de usuario en pantalla, AAA magenta, el resto azul
 * @wvar $contador, es una variable que mantiene valores para asignarlos a otras variables
 * @wvar $dir, direccion del paciente
 * @wvar $dir2, se utiliza para codificar la direccion y mandarla mejor por get
 * @wvar $doc, documento de identidad del paciente
 * @wvar $ema, email del paciente
 * @wvar $his, historia clinica del paciente
 * @wvar $idPac, id del paciente en tabla 000016
 * @wvar $ingresados, vector que contiene los comentarios del paciente en estado ingresado
 * @wvar $inicial, variable donde pongo datos del explode
 * @wvar $investigados, vector que contiene los comentarios del paciente en estado investigados
 * @wvar $priApe, primer apellido del paciente
 * @wvar $priNom, primer nombre del pacienre
 * @wvar $res, entidad responsable, se envia de otro prrgrama para pasarlo al crear un nuevo comentario 
 * @wvar $segApe, segundo apellido del paciente
 * @wvar $segNom, segundo nombre del paciente
 * @wvar $semaforo, color del motivo para semaforizacion
 * @wvar $senal, indica si el paciente tiene comentarios o no
 * @wvar $ser, servicio en que estuvo ultimamente el comentario, se envia de otro prrgrama para pasarlo al crear un nuevo comentario
 * @wvar $tel, telefono del paciente
 * @wvar $tipDoc, tipo de documento del paciente
 * @wvar $tipUsu, tipo de usuario que es el paciente para afinidad
*/

$wautor="Carolina Castano P.";
$wversion='2006-01-03';

//=================================================================================================================================

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
	echo "<td VALIGN=TOP NOWRAP>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<A HREF='javascript:window.showMenu(window.mWhite1);' onMouseOver='window.showMenu(window.mWhite1);'><font color=\"#D02090\" size=\"4\"><b>Menu</A>&nbsp/</b></font></td>";
	echo "<td><b><font size=\"4\"><A HREF='ayuda.mht' target='new'><font color=\"#D02090\">Ayuda</font></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></font></td>" ;
	echo "</tr>" ;
	echo "</table></br></br>" ;

	//cuando voy a datos del paciente que debo enviarle para que muestre los datos

	$cadena="'pagina1.php?matrix=".$doc."-".$tipDoc."&bandera=3&his=".$his."&res=".$res."&ser=".$ser."'";

	//se estructura el menu de javascript
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
  		?>
  		mWhite1.bgColor = "#ADD8E6";
  		mWhite1.menuItemBgColor = "white";
  		mWhite1.menuHiliteBgColor = "#336699";

  		myMenu.writeMenus();
  	}

	</SCRIPT>
 	<?php
 	/////////////////////////////////////////////////encabezado general///////////////////////////////////

 	/////////////////////////////////////////////////encabezado personal///////////////////////////////////

 	echo "<center><b><font size=\"4\"><A HREF='comentario.php?doc=$doc&tipDoc=$tipDoc&his=$his&ser=$ser&res=$res'><font color=\"#D02090\">CONSULTA E INGRESO DE COMENTARIOS POR PACIENTE</font></a></b></font></center>\n" ;
 	echo "<center><b><font size=\"2\"><font color=\"#D02090\">comentario.php</font></font></center></br></br></br>\n" ;
 	/////////////////////////////////////////////////encabezado personal///////////////////////////////////

 	/////////////////////////////////////////////////inicialización de variables///////////////////////////////////

 	/**
  * Este programita calcula el numero de dias en que fue investigado el comentario y por tanto su estado de
  * semaforizacion
  *
  */
 	include_once("magenta/semaforo.php");

 	/**
  * Include conexion a bd Matrix
  *
  */
 	

 	


 	$empresa='magenta';
 	$senal='0';

 	/////////////////////////////////////////////////inicialización de variables//////////////////////////

 	/////////////////////////////////////////////////acciones concretas///////////////////////////////////

 	//siempre recibira por invocacion las variables doc y tipdoc es decir documento de identidad y tipo completo (CC-Cedula de ciudadania)

 	//Busco en base de datos de afinidad el tipo de Usuario de la persona
 	$query ="SELECT clitip FROM " .$empresa."_000008 where clidoc='$doc' and clitid='$tipDoc' ";
 	$err=mysql_query($query,$conex);
 	$num=mysql_num_rows($err);
 	//echo $query;
 	if  ($num >0) //se llena los valores y un vector con los resultados y colores segun el tipo de usuario
 	{
 		$row=mysql_fetch_row($err);

 		//se determian el color segun es AAA, BBB, VIP
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

 	//BUSCO DEMAS DATOS DEL PACIENTE
 	$query ="SELECT cpedoc, cpetdoc, cpeno1, cpeno2, cpeap1, cpeap2, cpedir, cpetel, cpeema FROM " .$empresa."_000016 where cpedoc='$doc' and cpetdoc='$tipDoc'";
 	$err=mysql_query($query,$conex);
 	$num=mysql_num_rows($err);
 	//echo $query;
 	if  ($num >0) //se llena los valores
 	{
 		$row=mysql_fetch_row($err);
 		$priNom=$row[2];
 		$segNom=$row[3];
 		$priApe=$row[4];
 		$segApe=$row[5];
 		$dir=$row[6];
 		$tel=$row[7];
 		$ema=$row[8];
 	}
 	else
 	{
 		DisplayError();
 	}

 	// Busco los comentarios del paciente y los voy guardando en vectores

 	//inicializo los vectores, cada vector representa un estado, esta llevara el tamaño de los vectores
 	$ingresados[0]['id']=0;
 	$asignados[0]['id']=0;
 	$investigados[0]['id']=0;
 	$cerrados[0]['id']=0;

 	$query ="SELECT Ccoid, ccoori, ccofori, ccofrec, ccohis, ccoest, cconum FROM " .$empresa."_000017 where id_persona='".$doc."-".$tipDoc."' order by ccofori";
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
 	}else //para indicar que aun no hay comentarios del paciente
 	{
 		$senal='1';
 	}

 	/////////////////////////////////////////////////presentación general///////////////////////////////////

 	//tablas de resultados de comentarios

 	echo "<fieldset  style='border:solid;border-color:#00008B; width=100%' color=#000080 align='center'>";
 	echo "<table align='center'  width='100%'>";

 	//datos del paciente
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


 	echo "<table align='center'>";
 	echo "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
 	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
 	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
 	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
 	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
 	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

 	//enlace para crear un nuevo comentario
 	$dir2=urlencode($dir);

 	echo "<a href='detalleComentario.php?doc=$doc&tipDoc=$tipDoc&priNom=$priNom&segNom=$segNom&priApe=$priApe&segApe=$segApe&dir=$dir2&tel=$tel&ema=$ema&his=$his&res=$res&ser=$ser' align='right'>NUEVO COMENTARIO</a></td></tr>";
 	echo "</table></br>";

 	if ($senal ==1) //se sabe que no se encontro ningun comentario e matrix
 	{
 		echo "<CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
 		echo "<table align='center' border=0 bordercolor=#000080 width=340 style='border:solid;'>";
 		echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>EL PACIENTE AUN NO TIENE COMENTARIOS REGISTRADOS EN LA BASE DE DATOS </td><tr>";
 		echo "</table></fieldset></center>";
 	}

 	////////////////////////////////////COMENTARIOS INGRESADOS/////////////////////////////////////////////////////
 	if ($senal==0)//indica que si hay comentarios, debe ponerse el titulo
 	echo "<center><b><font size='4'><font color='#00008B'>COMENTARIOS INGRESADOS</b></font></font></center></br>";

 	if ($ingresados[0]['id'] > 0) // Si el paciente tiene comentarios en estado ingresados se buscan los motivos para mostrar
 	{

 		echo "<table align='center' border='1' width='100%'>";


 		for($i=1;$i<=$ingresados[0]['id'];$i++)
 		{
 			echo "<tr>";
 			echo "<td bgcolor='#336699' width='15%' align='center'><a href='detalleComentario.php?idCom=".$ingresados[$i]['id']."' align='center'><font color='#ffffff'>Nº Comentario: ".$ingresados[$i]['num']."</a></font></td>";
 			echo "<td  bgcolor='#336699' width='40%'  colspan='2'><font color='#ffffff'>Fecha/Lugar de origen: ".$ingresados[$i]['fecOri']."&nbsp;".$ingresados[$i]['lugOri']."</font></td>";
 			echo "<td bgcolor='#336699'  width='15%' colspan='1'><font color='#ffffff'>Fecha de recepción: ".$ingresados[$i]['fecRec']." </font></td>";
 			echo "<td bgcolor='#336699' width='30%' colspan='3'><font color='#ffffff'>Historia: ".$ingresados[$i]['aut']."</font></td>";
 			echo "</tr>";

 			//buscamos los motivos del coemntario en la tabla 18 en estado ingresado
 			$query =" SELECT A.id_comentario, A.cmonum, A.cmotip, A.cmocla, A.cmofenv, A.cmosem, A.cmoest FROM " .$empresa."_000018 A  ";
 			$query= $query. " where A.id_comentario=".$ingresados[$i]['id']." and A.cmoest='INGRESADO' ";

 			$err=mysql_query($query,$conex);
 			$nun=mysql_num_rows($err);


 			if  ($nun >0) //se muestran los resultado
 			{
 				echo "<tr>";
 				echo "<td bgcolor='#336699' width='15%' align='center'><font color='#ffffff'>Nº Motivo</font></td>";
 				echo "<td bgcolor='#336699' width='10%' align='center'><font color='#ffffff'>Tipo</font></td>";
 				echo "<td bgcolor='#336699' width='30%' align='center'><font color='#ffffff'>Clasificación</font></td>";
 				echo "<td bgcolor='#336699' width='15%' align='center'><font color='#ffffff'>Area responsable</font></td>";
 				echo "<td bgcolor='#336699' width='15%' align='center'><font color='#ffffff'>Fecha Envío</font></td>";
 				echo "<td bgcolor='#336699' width='5%' align='center'><font color='#ffffff'>Sem.</font></td>";
 				echo "<td bgcolor='#336699' width='10%' align='center'><font color='#ffffff'>Estado</font></td>";
 				echo "</tr>";

 				for($j=0;$j<$nun;$j++)
 				{
 					$row=mysql_fetch_row($err);

 					//se escoje un color dependiendo del tipo de comentario
 					if ($row[2]=='Agrado')
 					$color='#EOFFFF';
 					else
 					$color="#cccccc";

 					echo "<tr>";
 					echo "<td bgcolor='$color' width='15%' align='center'>$row[1]</td>";
 					echo "<td bgcolor='$color' width='10%' align='center'>$row[2]</td>";
 					echo "<td bgcolor='$color' width='30%' align='center'>$row[3]</td>";
 					echo "<td bgcolor='$color' width='15%' align='center'>Sin asignar</td>";
 					echo "<td bgcolor='$color' width='15%' align='center'>$row[4]</td>";
 					echo "<td bgcolor='#008000' width='5%' align='center'>&nbsp;</td>";
 					echo "<td bgcolor='$color' width='10%' align='center'>$row[6]</td>";
 					echo "</tr>";
 				}
 			}

 			//buscamos los motivos del comentario en la tabla 18 que ya tienen area responsable
 			$query =" SELECT A.id_comentario, A.cmonum, A.cmotip, A.cmocla, A.cmofenv, A.cmoest, A.cmofret, A.id_area, B.carnom ,C.id_responsable, D.crenom ";
 			$query= $query. " FROM " .$empresa."_000018 A, " .$empresa."_000019 B, " .$empresa."_000020 C, " .$empresa."_000021 D ";
 			$query= $query. " where A.id_comentario=".$ingresados[$i]['id']." and A.cmoest<>'INGRESADO' ";
 			$query= $query. " and B.Carcod=A.id_area ";
 			$query= $query. " and C.id_area=A.id_area and carniv=1 ";
 			$query= $query. " and D.Crecod=C.id_responsable ";

 			//echo $query;

 			$err=mysql_query($query,$conex);
 			$num=mysql_num_rows($err);

 			if  ($num >0) //se muestran los resultados
 			{
 				if ($nun<=0)
 				{
 					echo "<tr>";
 					echo "<td bgcolor='#336699' width='15%' align='center'><font color='#ffffff'>Nº Motivo</font></td>";
 					echo "<td bgcolor='#336699' width='10%' align='center'><font color='#ffffff'>Tipo</font></td>";
 					echo "<td bgcolor='#336699' width='30%' align='center'><font color='#ffffff'>Clasificación</font></td>";
 					echo "<td bgcolor='#336699' width='15%' align='center'><font color='#ffffff'>Area responsable</font></td>";
 					echo "<td bgcolor='#336699' width='15%' align='center'><font color='#ffffff'>Fecha Envío</font></td>";
 					echo "<td bgcolor='#336699' width='5%' align='center'><font color='#ffffff'>Sem.</font></td>";
 					echo "<td bgcolor='#336699' width='10%' align='center'><font color='#ffffff'>Estado</font></td>";
 					echo "</tr>";
 				}

 				for($j=0;$j<$num;$j++)
 				{
 					$row=mysql_fetch_row($err);

 					if ($row[2]=='Agrado')
 					$color='#EOFFFF';
 					else
 					$color="#cccccc";

 					$semaforo=semaforo($row[5], $row[4], $row[0], $empresa, $conex, $row[1]);

 					$inicial = explode("-",$semaforo);
 					$semaforo=$inicial[0];

 					echo "<tr>";
 					echo "<td bgcolor='$color' width='15%' align='center'>$row[1]</td>";
 					echo "<td bgcolor='$color' width='10%' align='center'>$row[2]</td>";
 					echo "<td bgcolor='$color' width='30%' align='center'>$row[3]</td>";
 					echo "<td bgcolor='$color' width='15%' align='center'>$row[8]</td>";
 					echo "<td bgcolor='$color' width='15%' align='center'>$row[4]</td>";
 					echo "<td bgcolor='$semaforo' width='5%' align='center'>&nbsp;</td>";
 					echo "<td bgcolor='$color' width='10%' align='center'>$row[5]</td>";
 					echo "</tr>";
 				}
 			}

 			//en caso de que no se encuentren motivos para el comentario
 			if ($nun<=0 and $num<=0)
 			{
 				echo "<tr>";
 				echo "<td><b><font size='3' width='11%' align='center'>&nbsp;</font></font></td>";
 				echo "<td  width='89%' colspan='7' align='center' ><b><font size='3' >No se han ingresado motivos para este comentario</font></font></td>";
 				echo "</tr>";
 			}

 		}
 		echo "</table></br></br>";

 	}else if ($senal != 1)//en caso de que no se encuentren motivos en estado ingresado pero si hay comentarios
 	{
 		echo "<CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
 		echo "<table align='center' border=0 bordercolor=#000080 width=340 style='border:solid;'>";
 		echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>EL PACIENTE NO TIENE COMENTARIOS EN ESTADO INGRESADO EN LA BASE DE DATOS </td><tr>";
 		echo "</table></fieldset></center></br></br>";
 	}

 	////////////////////////////////////COMENTARIOS ASIGNADOS/////////////////////////////////////////////////////
 	if ($senal==0) //indica que si hay comentarios, debe ponerse el titulo
 	echo "<center><b><font size='4'><font color='#00008B'>COMENTARIOS ASIGNADOS A UNIDADES</b></font></font></center></br>";

 	if ($asignados[0]['id'] > 0) // Si el paciente tiene comentarios en estado asignado se buscan los motivos para mostrar
 	{

 		echo "<table align='center' border='1' width='100%'>";


 		for($i=1;$i<=$asignados[0]['id'];$i++)
 		{
 			echo "<tr>";
 			echo "<td bgcolor='#336699' width='15%' ><a href='asignacion.php?idCom=".$asignados[$i]['id']."' align='right'><font color='#ffffff'>Nº Comentario: ".$asignados[$i]['num']."</a></font></td>";
 			echo "<td  bgcolor='#336699' width='40%'  colspan='2'><font color='#ffffff'>Fecha/Lugar de origen: ".$asignados[$i]['fecOri']."&nbsp;".$asignados[$i]['lugOri']."</font></td>";
 			echo "<td bgcolor='#336699'  width='15%' colspan='1'><font color='#ffffff'>Fecha de recepción: ".$asignados[$i]['fecRec']." </font></td>";
 			echo "<td bgcolor='#336699' width='30%' colspan='3'><font color='#ffffff'>Historia: ".$asignados[$i]['aut']."</font></td>";
 			echo "</tr>";

 			$query =" SELECT A.id_comentario, A.cmonum, A.cmotip, A.cmocla, A.cmofenv, A.cmoest, A.cmofret, A.id_area, B.carnom ,C.id_responsable, D.crenom ";
 			$query= $query. " FROM " .$empresa."_000018 A, " .$empresa."_000019 B, " .$empresa."_000020 C, " .$empresa."_000021 D ";
 			$query= $query. " where A.id_comentario=".$asignados[$i]['id']." ";
 			$query= $query. " and B.Carcod=A.id_area ";
 			$query= $query. " and C.id_area=A.id_area and carniv=1 ";
 			$query= $query. " and D.Crecod=C.id_responsable ";

 			//echo $query;

 			$err=mysql_query($query,$conex);
 			$num=mysql_num_rows($err);
 			//echo $num;

 			if  ($num >0) //se muestran los motivos encontrados
 			{
 				echo "<tr>";
 				echo "<td bgcolor='#336699' width='15%' align='center'><font color='#ffffff'>Nº Motivo</font></td>";
 				echo "<td bgcolor='#336699' width='10%' align='center'><font color='#ffffff'>Tipo</font></td>";
 				echo "<td bgcolor='#336699' width='30%' align='center'><font color='#ffffff'>Clasificación</font></td>";
 				echo "<td bgcolor='#336699' width='15%' align='center'><font color='#ffffff'>Area responsable</font></td>";
 				echo "<td bgcolor='#336699' width='15%' align='center'><font color='#ffffff'>Fecha Envío</font></td>";
 				echo "<td bgcolor='#336699' width='5%' align='center'><font color='#ffffff'>Sem.</font></td>";
 				echo "<td bgcolor='#336699' width='10%' align='center'><font color='#ffffff'>Estado</font></td>";
 				echo "</tr>";


 				for($j=0;$j<$num;$j++)
 				{
 					$row=mysql_fetch_row($err);

 					if ($row[2]=='Agrado')
 					$color='#EOFFFF';
 					else
 					$color="#cccccc";

 					//se utiliza el include semaforo con la funcion semaforo, para calcular la semaforizacion
 					$semaforo=semaforo($row[5], $row[4], $row[0], $empresa, $conex, $row[1]);
 					$inicial = explode("-",$semaforo);
 					$semaforo=$inicial[0];

 					echo "<tr>";
 					echo "<td bgcolor='$color' width='15%' align='center'>$row[1]</td>";
 					echo "<td bgcolor='$color' width='10%' align='center'>$row[2]</td>";
 					echo "<td bgcolor='$color' width='30%' align='center'>$row[3]</td>";
 					echo "<td bgcolor='$color' width='215%' align='center'>$row[8]</td>";
 					echo "<td bgcolor='$color' width='15%' align='center'>$row[4]</td>";

 					echo "<td bgcolor='$semaforo' width='5%' align='center'>&nbsp;</td>";
 					echo "<td bgcolor='$color' width='10%' align='center'>$row[5]</td>";
 					echo "</tr>";
 				}
 			}
 		}
 		echo "</table></br></br>";

 	}else if ($senal != 1)
 	{
 		echo "<CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
 		echo "<table align='center' border=0 bordercolor=#000080 width=340 style='border:solid;'>";
 		echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>EL PACIENTE NO TIENE COMENTARIOS EN ESTADO ASIGNADO </td><tr>";
 		echo "</table></fieldset></center></br></br>";
 	}
 	////////////////////////////////////COMENTARIOS ASIGNADOS/////////////////////////////////////////////////////
 	////////////////////////////////////COMENTARIOS INVESTIGADOS/////////////////////////////////////////////////////
 	if ($senal==0)
 	echo "<center><b><font size='4'><font color='#00008B'>COMENTARIOS PENDINTES DE RESPUESTA POR PARTE DE MAGENTA</b></font></font></center></br>";

 	if ($investigados[0]['id'] > 0) // Si el paciente tiene comentarios en estado ingresados se buscan los motivos para mostrar
 	{

 		echo "<table align='center' border='1' width='100%'>";


 		for($i=1;$i<=$investigados[0]['id'];$i++)
 		{
 			echo "<tr>";
 			echo "<td bgcolor='#336699' width='15%' ><a href='respuesta.php?idCom=".$investigados[$i]['id']."' align='right'><font color='#ffffff'>Nº Comentario: ".$investigados[$i]['num']."</a></font></td>";
 			echo "<td  bgcolor='#336699' width='40%'  colspan='2'><font color='#ffffff'>Fecha/Lugar de origen: ".$investigados[$i]['fecOri']."&nbsp;".$investigados[$i]['lugOri']."</font></td>";
 			echo "<td bgcolor='#336699'  width='15%' colspan='1'><font color='#ffffff'>Fecha de recepción: ".$investigados[$i]['fecRec']." </font></td>";
 			echo "<td bgcolor='#336699' width='30%' colspan='3'><font color='#ffffff'>Historia: ".$investigados[$i]['aut']."</font></td>";
 			echo "</tr>";

 			$query =" SELECT A.id_Comentario, A.cmonum, A.cmotip, A.cmocla, A.cmofenv, A.cmoest, A.cmofret, A.id_area, B.carnom ,C.id_responsable, D.crenom ";
 			$query= $query. " FROM " .$empresa."_000018 A, " .$empresa."_000019 B, " .$empresa."_000020 C, " .$empresa."_000021 D ";
 			$query= $query. " where A.id_comentario=".$investigados[$i]['id']." ";
 			$query= $query. " and B.Carcod=A.id_area ";
 			$query= $query. " and C.id_area=A.id_area and carniv=1 ";
 			$query= $query. " and D.Crenom=C.id_responsable ";

 			//echo $query;

 			$err=mysql_query($query,$conex);
 			$num=mysql_num_rows($err);

 			if  ($num >0) //se muestran los motivos encontrados
 			{
 				echo "<tr>";
 				echo "<td bgcolor='#336699' width='15%' align='center'><font color='#ffffff'>Nº Motivo</font></td>";
 				echo "<td bgcolor='#336699' width='10%' align='center'><font color='#ffffff'>Tipo</font></td>";
 				echo "<td bgcolor='#336699' width='30%' align='center'><font color='#ffffff'>Clasificación</font></td>";
 				echo "<td bgcolor='#336699' width='15%' align='center'><font color='#ffffff'>Area responsable</font></td>";
 				echo "<td bgcolor='#336699' width='15%' align='center'><font color='#ffffff'>Fecha Envío</font></td>";
 				echo "<td bgcolor='#336699' width='5%' align='center'><font color='#ffffff'>Sem.</font></td>";
 				echo "<td bgcolor='#336699' width='10%' align='center'><font color='#ffffff'>Estado</font></td>";
 				echo "</tr>";


 				for($j=0;$j<$num;$j++)
 				{
 					$row=mysql_fetch_row($err);

 					if ($row[2]=='Agrado')
 					$color='#EOFFFF';
 					else
 					$color="#cccccc";

 					$semaforo=semaforo($row[5], $row[4], $row[0], $empresa, $conex, $row[1]);
 					$inicial = explode("-",$semaforo);
 					$semaforo=$inicial[0];

 					echo "<tr>";
 					echo "<td bgcolor='$color' width='15%' align='center'>$row[1]</td>";
 					echo "<td bgcolor='$color' width='10%' align='center'>$row[2]</td>";
 					echo "<td bgcolor='$color' width='30%' align='center'>$row[3]</td>";
 					echo "<td bgcolor='$color' width='15%' align='center'>$row[8]</td>";
 					echo "<td bgcolor='$color' width='15%' align='center'>$row[4]</td>";



 					echo "<td bgcolor='$semaforo' width='5%' align='center'>&nbsp;</td>";
 					echo "<td bgcolor='$color' width='10%' align='center'>$row[5]</td>";
 					echo "</tr>";
 				}
 			}
 		}
 		echo "</table></br></br>";

 	}else if ($senal != 1)
 	{
 		echo "<CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
 		echo "<table align='center' border=0 bordercolor=#000080 width=340 style='border:solid;'>";
 		echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>EL PACIENTE NO TIENE COMENTARIOS EN ESTADO INVESTIGADO </td><tr>";
 		echo "</table></fieldset></center></br></br>";
 	}
 	////////////////////////////////////COMENTARIOS CERRADOS/////////////////////////////////////////////////////
 	if ($senal==0)
 	echo "<center><b><font size='4'><font color='#00008B'>COMENTARIOS CERRADOS</b></font></font></center></br>";

 	if ($cerrados[0]['id'] > 0) // Si el paciente tiene comentarios en estado ingresados se buscan los motivos para mostrar
 	{

 		echo "<table align='center' border='1' width='100%'>";


 		for($i=1;$i<=$cerrados[0]['id'];$i++)
 		{
 			echo "<tr>";
 			echo "<td bgcolor='#336699' width='15%' ><a href='respuesta.php?idCom=".$cerrados[$i]['id']."' align='right'><font color='#ffffff'>Nº Comentario: ".$cerrados[$i]['num']."</a></font></td>";
 			echo "<td  bgcolor='#336699' width='40%'  colspan='2'><font color='#ffffff'>Fecha/Lugar de origen: ".$cerrados[$i]['fecOri']."&nbsp;".$cerrados[$i]['lugOri']."</font></td>";
 			echo "<td bgcolor='#336699'  width='15%' colspan='1'><font color='#ffffff'>Fecha de recepción: ".$cerrados[$i]['fecRec']." </font></td>";
 			echo "<td bgcolor='#336699' width='30%' colspan='3'><font color='#ffffff'>Historia: ".$cerrados[$i]['aut']."</font></td>";
 			echo "</tr>";

 			$query =" SELECT A.id_Comentario, A.cmonum, A.cmotip, A.cmocla, A.cmofenv, A.cmoest, A.cmofret, A.id_area, B.carnom ,C.id_responsable, D.crenom ";
 			$query= $query. " FROM " .$empresa."_000018 A, " .$empresa."_000019 B, " .$empresa."_000020 C, " .$empresa."_000021 D ";
 			$query= $query. " where A.id_comentario=".$cerrados[$i]['id']." ";
 			$query= $query. " and B.Carcod=A.id_area ";
 			$query= $query. " and C.id_area=A.id_area and carniv=1 ";
 			$query= $query. " and D.Crenom=C.id_responsable ";

 			//echo $query;

 			$err=mysql_query($query,$conex);
 			$num=mysql_num_rows($err);

 			if  ($num >0) //se muestran los motivos encontrados
 			{
 				echo "<tr>";
 				echo "<td bgcolor='#336699' width='15%' align='center'><font color='#ffffff'>Nº Motivo</font></td>";
 				echo "<td bgcolor='#336699' width='10%' align='center'><font color='#ffffff'>Tipo</font></td>";
 				echo "<td bgcolor='#336699' width='30%' align='center'><font color='#ffffff'>Clasificación</font></td>";
 				echo "<td bgcolor='#336699' width='15%' align='center'><font color='#ffffff'>Area responsable</font></td>";
 				echo "<td bgcolor='#336699' width='15%' align='center'><font color='#ffffff'>Fecha Envío</font></td>";
 				echo "<td bgcolor='#336699' width='5%' align='center'><font color='#ffffff'>Sem.</font></td>";
 				echo "<td bgcolor='#336699' width='10%' align='center'><font color='#ffffff'>Estado</font></td>";
 				echo "</tr>";


 				for($j=0;$j<$num;$j++)
 				{
 					$row=mysql_fetch_row($err);

 					if ($row[2]=='Agrado')
 					$color='#EOFFFF';
 					else
 					$color="#cccccc";

 					$semaforo=semaforo($row[5], $row[4], $row[0], $empresa, $conex, $row[1]);
 					$inicial = explode("-",$semaforo);
 					$semaforo=$inicial[0];

 					echo "<tr>";
 					echo "<td bgcolor='$color' width='15%' align='center'>$row[1]</td>";
 					echo "<td bgcolor='$color' width='10%' align='center'>$row[2]</td>";
 					echo "<td bgcolor='$color' width='30%' align='center'>$row[3]</td>";
 					echo "<td bgcolor='$color' width='15%' align='center'>$row[8]</td>";
 					echo "<td bgcolor='$color' width='15%' align='center'>$row[4]</td>";



 					echo "<td bgcolor='$semaforo' width='5%' align='center'>&nbsp;</td>";
 					echo "<td bgcolor='$color' width='10%' align='center'>$row[5]</td>";
 					echo "</tr>";
 				}
 			}
 		}
 		echo "</table></br></br>";

 	}else if ($senal != 1)
 	{
 		echo "<CENTER><fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080>";
 		echo "<table align='center' border=0 bordercolor=#000080 width=340 style='border:solid;'>";
 		echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>EL PACIENTE NO TIENE COMENTARIOS EN ESTADO CERRADO</td><tr>";
 		echo "</table></fieldset></center></br></br>";
 	}

}

?>
</body>
</html>


