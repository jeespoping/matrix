<html>

<head>
		<title>PROGRAMA DE COMENTARIOS Y SUGERENCIAS</title>
		
		<!-- UTF-8 is the recommended encoding for your pages -->
    <meta http-equiv="content-type" content="text/xml; charset=utf-8" />
    <title>Zapatec DHTML Calendar</title>

	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
	<script type="text/javascript">
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
			$("#fecIni, #fecFin").datepicker({
		       showOn: "button",
		       buttonImage: "../../images/medical/root/calendar.gif",
		       buttonImageOnly: true,
		       maxDate:"+1D"
		    });
		});
	   </script>
	   <script type="text/javascript">
		<!--
		function nuevoAjax()
		{
			var xmlhttp=false;
			try
			{
				xmlhttp=new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch(e)
			{
				try
				{
					xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				catch(E) { xmlhttp=false; }
			}
			if (!xmlhttp && typeof XMLHttpRequest!='undefined') { xmlhttp=new XMLHttpRequest(); }

			return xmlhttp;
		}

		function Buscar(fila, lista)
		{

			var x = new Array();

			x[2] = document.consultas.selecc.value;
			x[3] = document.consultas.fecIni.value;
			x[4] = document.consultas.fecFin.value;


			st="consultas.php?lista="+lista+"&selecc="+x[2]+"&fecIni="+x[3]+"&fecFin="+x[4]+"&bandera=1";

			ajax=nuevoAjax();
			ajax.open("GET", st, true);
			ajax.onreadystatechange=function()
			{

				if (ajax.readyState==4)
				{

					document.getElementById(+fila).innerHTML=ajax.responseText;
				}
			}
			ajax.send(null);

		}

		//-->

</script>
</head>

<body>

<?php
include_once("conex.php");


/**
 * 	CONSULTA DE COMENTARIOS
 * 
 * Este programa permite realizar la busqueda de comentarios por causa, entidad, unidad y respuesta, dirigidas desde listaMagenta.php
 * 
 * @name  matrix\magenta\procesos\consultas.php
 * @author Carolina Castaño Portilla.
 * 
 * @created 2006-01-09
 * @version 2006-01-09
 *
 * @modified 2018-04-11  Se modifica consulta por Entidad, extrayendo el primero dato del string , de esta forma consulta
 *                       solamente por código sin tener en cuenta el nombre de la Entidad. 
 * @modified 2007-01-09  Se realiza documentacion del programa y calendario
 * @modified 2007-02-05  Se realiza busqueda de entidades que no aparezcan en la tabla 000025, es decir que esten mal ingresadas
 * 							mas adelante como mejora habra que validar que el sistema no deje ingresar las entidades asi en detalleComentario.php
 Actualizacion: Se organizan los campos de algunas consultas que tenian error al llamar los campos.  Viviana Rodas 15-05-2012
 * 
 * @table magenta_000017, select, 
 * @table magenta_000018, select, 
 * @table magenta_000019, select
 * @table magenta_000024, select
 * @table magenta_000025, select
 * 
 *  @var $bandera, indica si es la primera vez que se ingresa al programa
 *  @var $codigo codigos del patron de busqueda, del area o la entidad etc
 *  @var $fecIni fecha incial de l busqueda de comentarios
 *  @var $fecFin fecha final de l busqueda de comentario
 *  @var $fila, organiza lo que se pasa a ajax, el patron de busqueda y el numero que identifica al formulario sobre el cual se va a reescribir
 *  @var $id del parametro de busqueda en la tabla en la que se almacena
 *  @var $lista indica que parametro de busqueda es el que se esta utilizando se pasa desde listaMagenta.php
 *  @var $mostrar se guarda el titulo de lo que se va desplegar de datos del comentario segun el parametro de busqueda
 *  @var $mostrar2 se guarda el titulo de lo que se va desplegar de datos del comentario segun el parametro de busqueda
 *  @var $selecc es la opcion de busqueda seleccionada del dropdown
*/

$wautor="Carolina Castano P.";
$wversion='2012-10-30';
//=================================================================================================================================
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	/**
	 * conexion a Matrix
	 */
	

	


	// si entro por primera vez realizo query para dropdown según lo enviado ////////////////////////////////////////////
	if (!isset ($bandera))
	{

		/////////////////////////////////////////////////encabezado general///////////////////////////////////
		echo "<table align='right'>" ;
		echo "<tr>" ;
		echo "<td><font color=\"#D02090\" size='2'></font></td>";
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
		echo "<td align=right><b><font size=\"4\"><A HREF='listaMagenta.php' ><font color=\"#D02090\">Lista de comentarios</font></a>&nbsp;&nbsp;&nbsp;&nbsp;</b></font></td>" ;
		echo "<td><b><font size=\"4\"><A HREF='ayuda.mht' target='new'><font color=\"#D02090\">Ayuda</font></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></font></td>" ;
		echo "</tr>" ;
		echo "</table></br></br></br></br>" ;


		/////////////////////////////////////////////////encabezado general///////////////////////////////////
		//segun lo que me pase lista magenta
		switch ($lista)
		{
			case 1: //consultar por unidad
			echo "<center><b><font size=\"4\" color=\"#D02090\">CONSULTA DE COMENTARIOS POR UNIDAD</font></b></center>";
			echo "<center><b><font size=\"2\"><font color=\"#D02090\"> consultas.php</font></font></center></br></br></br>\n" ;
			$query="select carcod, carnom, carcod from magenta_000019 where carest='on' ";
			$err = mysql_query($query,$conex) or die (mysql_errno()." - en el query ".$query. " ". mysql_error());
			$num = mysql_num_rows($err);
			for($i=0;$i<$num;$i++)
			{
				$row=mysql_fetch_row($err);
				$nombre[$i]=$row[1];
				$codigo[$i]=$row[0];
				$id[$i]=$row[2];
			}
			break;
			case 2:		//consultar por causa
			echo "<center><b><font size=\"4\" color=\"#D02090\">CONSULTA DE COMENTARIOS POR CAUSA</font></b></center>";
			$query='select caucod, caunom, id from magenta_000024 ';
			$err = mysql_query($query,$conex)or die (mysql_errno()." - en el query ".$query. " ". mysql_error());
			$num = mysql_num_rows($err);
			for($i=0;$i<$num;$i++)
			{
				$row=mysql_fetch_row($err);
				$nombre[$i]=$row[1];
				$codigo[$i]=$row[0];
				$id[$i]=$row[0].'-'.$row[1];
			}
			break;
			case 3:  //consultar por respuesta
			echo "<center><b><font size=\"4\" color=\"#D02090\">CONSULTA DE COMENTARIOS POR RESPUESTA</font></b></center>";
			$nombre[0]='telefonica';
			$codigo[0]='1';
			$id[0]='telefonico';
			$nombre[1]='escrita';
			$codigo[1]='2';
			$id[1]='Escrito';
			$nombre[2]='Email';
			$codigo[2]='3';
			$id[2]='Mail';
			$nombre[3]='personal';
			$codigo[3]='4';
			$id[3]='Personal';
			$nombre[4]='No respuesta';
			$codigo[4]='5';
			$id[4]='No respuesta';
			$num=4;
			break;

			case 4:  //consultar por Entidad
			echo "<center><b><font size=\"4\" color=\"#D02090\">CONSULTA DE COMENTARIOS POR ENTIDAD</font></b></center>";
			$query="select cempcod, cempnom, id from magenta_000025 where cempest='on' order by cempnom ";
			$err = mysql_query($query,$conex) or die (mysql_errno()." - en el query ". $query." ". mysql_error());
			$num = mysql_num_rows($err);
			for($i=0;$i<$num;$i++)
			{
				$row=mysql_fetch_row($err);
				$nombre[$i]=$row[1];
				$codigo[$i]=$row[0];
				$id[$i]=$row[2];
			}
			$nombre[$i]='mal ingresados';
			$codigo[$i]='00';
			$id[$i]='00';
			$num=$num+1;
			break;
		}

		if (!isset ($fecIni))
		{
			$fecIni=date('Y').'-'.date('m').'-01';
			$fecFin=date('Y-m-d');
		}

		// pinto dropdown de busqueda ////////////////////////////////////////////
		echo '<form name="consultas" action="consultas.php" method="post">';
		echo '<div id=1 align="center">';
		echo '<p>';
		echo '<font color="#000084">';
		echo '<br><br><strong>SELECCIONE LA OPCION DE BUSQUEDA:</strong>';
		echo '</font>';
		echo '</p>';
		echo '<p>';
		echo '<select name="selecc" size="1"/ on>';
		for($i=0;$i<$num;$i++)
		{
			echo '<option value="'.$id[$i].'">'.$codigo[$i].'-'.$nombre[$i].'</option>';
		}
		echo '</select></p>';


		echo "<table align='center'>";
		echo "<tr>";
		$cal="calendario('fecIni','1')";
		echo "<td align=center bgcolor=#336699 ><font size=3  face='arial' color='#ffffff'>FECHA INICIAL:</font><input type='text' readonly='readonly' id='fecIni' name='fecIni' value='".$fecIni."' class=tipo3 ></td>";

		echo "<td align=center bgcolor=#336699><font size=3  face='arial' color='#ffffff'>FECHA FINAL:</font><input type='text' readonly='readonly' id='fecFin' name='fecFin' value='".$fecFin."' class=tipo3 ></td>";
		
		echo "</td>";
		echo "</tr></table></br>";

		$fila='Buscar(2,"'.$lista.'")';
		echo "<input type='button' name='buscar' value='BUSCAR'  onclick='".$fila."' />";
		echo '</div>';
		echo '</form>	';

		echo '<div id=2 align="center">';
		echo '<hr>';
		echo '</div>';
		//////////////////////////////////////////////////////////////////////////////////

}else //se manda por ajax la segunda peticion al programa
{
	echo '<hr>';
	switch ($lista)
	{
		case '1': //consultar por unidad
		$query="select A.id, A.ccoori, A.ccofrec, A.ccoent, B.id,  B.cmonum, B.cmotip, B.Cmocla, B.Cmocau, B.cmoest, B.id_area, A.cconum  
				  from magenta_000017 A, magenta_000018 B 
				 where A.Ccofori between '".$fecIni."' and '".$fecFin."' 
				   and  B.id_area='".$selecc."' 
				   and A.id=B.id_comentario 
				 order by A.id ";
		$mostrar='Causa';
		$mostrar2='Estado';
		break;

		case '2':				//consultar por causa
		$query="select A.id, A.ccoori, A.ccofrec, A.ccoent, B.id, B.cmonum, B.cmotip, B.Cmocla, B.cmoest, C.carnom,  A.ccoori, A.cconum   
		          from magenta_000017 A, magenta_000018 B, magenta_000019 C 
				 where A.Ccofori between '".$fecIni."' and '".$fecFin."' 
				   and B.cmocau='".$selecc."' 
				   and A.id=B.id_comentario 
				   and C.carcod=B.id_area 
				 order by A.id ";
		$mostrar='Estado';
		$mostrar2='Causa';
		break;

		case '3': //consultar por respuesta
		$query="select A.id, A.ccoori, A.ccofrec, A.ccoent, B.id,  B.cmonum, B.cmotip, B.Cmocla, B.Cmocau, B.id_area, A.ccoori, A.cconum   
				  from magenta_000017 A, magenta_000018 B, magenta_000019 C 
				 where A.Ccofori between '".$fecIni."' and '".$fecFin."'  
				   and A.ccotres='".$selecc."' 
				   and B.Cmoest='CERRADO' 
				   and A.id=B.id_comentario 
				   and C.carcod=B.id_area 
				 order by A.id  ";
		$mostrar='Causa';
		$mostrar2='Unidad';
		break;

		case '4': //consultar por empresa

		if ($selecc<>'00')
		{
			$query="select cempcod, cempnom, id from magenta_000025 where id='".$selecc."' ";
			$err = mysql_query($query,$conex);
			$row=mysql_fetch_row($err);
			$selecc=$row[0].'-'.$row[1];

			$query=" SELECT A.id, A.ccoori, A.ccofrec, B.cmocau, B.id,  B.cmonum, B.cmotip, B.Cmocla, B.cmoest, C.carnom, A.ccoori, A.cconum  
					   FROM magenta_000017 A, magenta_000018 B, magenta_000019 C 
					  WHERE A.Ccofori between '".$fecIni."' and '".$fecFin."'  
					    AND substring_index(A.ccoent,'-',1) = substring_index('".$selecc."','-',1)
						AND A.id=B.id_comentario 
						AND C.carcod=B.id_area order by A.id  ";
			$mostrar='Estado';
			$mostrar2='Unidad';
		}
		else
		{
			$query="SELECT A.id, A.ccoori, A.ccofrec, B.cmocau, B.id,  B.cmonum, B.cmotip, B.Cmocla, B.cmoest, C.carnom, A.ccoori, A.cconum  
					  FROM magenta_000017 A, magenta_000018 B, magenta_000019 C  
					 WHERE A.Ccofori between '".$fecIni."' and '".$fecFin."'  
					   AND A.id=B.id_comentario 
					   AND C.carcod=B.id_area 
					   AND mid(A.ccoent,1,1) not in (select mid(cempcod,1,1) from magenta_000025) 
				  ORDER BY A.id  ";
			$mostrar='Estado';
			$mostrar2='Unidad';
		}
		break;
	}

	$err = mysql_query($query,$conex) or die (mysql_errno()." - en el query ".$query. " ". mysql_error());
	$num = mysql_num_rows($err);


	//se muestra la lista de resultados resultante y sus motivos
	if ($num>0)
	{
		echo "<center><b><font size='4'><font color='#00008B'>Resultados (".$num.")</b></font></font></center></br>";
		echo "<table border=1><tr>";
		echo "<td bgcolor='#336699' width='11%' ><font color='#ffffff'>Nº Comentario</font></a></td>";
		echo "<td bgcolor='#336699' width='11%' ><font color='#ffffff'>Nº Motivo</font></a></td>";
		echo "<td bgcolor='#336699' width='11%' ><font color='#ffffff'>Lugar de Origen</font></a></td>";
		echo "<td bgcolor='#336699' width='11%' ><font color='#ffffff'>Fecha de recepcion</font></a></td>";
		if ($lista!=4)
		{
			echo "<td bgcolor='#336699' width='11%' ><font color='#ffffff'>Entidad</font></a></td>";
		}
		else
		{
			echo "<td bgcolor='#336699' width='11%' ><font color='#ffffff'>Causa</font></a></td>";
		}
		echo "<td bgcolor='#336699' width='11%' ><font color='#ffffff'>tipo</font></a></td>";
		echo "<td bgcolor='#336699' width='11%' ><font color='#ffffff'>clase</font></a></td>";
		echo "<td bgcolor='#336699' width='11%' ><font color='#ffffff'>".$mostrar."</font></a></td>";
		echo "<td bgcolor='#336699' width='11%' ><font color='#ffffff'>".$mostrar2."</font></a></td>";

		echo "</tr>";
	}
	else
	{
		echo "<center><b><font size='4'><font color='#00008B'>Resultados (0)</b></font></font></center></br>";
	}
	for($i=1;$i<=$num;$i++)
	{
		$row=mysql_fetch_row($err);

		echo "<tr>";
		echo "<td  width='11%' align='center'><a href='detalleComentario.php?idCom=".$row[0]."' align='right'><font >".$row[11]."</font></a></td>";
		echo "<td  width='11%' ><font >".$row[5]."</font></a></td>";
		echo "<td  width='11%' ><font >".$row[1]."</font></a></td>";
		echo "<td  width='11%' ><font >".$row[2]."</font></a></td>";
		echo "<td  width='11%' ><font >".$row[3]."</font></a></td>";
		echo "<td  width='11%' ><font >".$row[6]."</font></a></td>";
		echo "<td width='11%' ><font >".$row[7]."</font></a></td>";
		echo "<td  width='11%' ><font >".$row[8]."</font></a></td>";
		echo "<td  width='11%' ><font >".$row[9]."</font></a></td>";
		echo "</tr>";
	}
	echo "</table></br></br>";

}
}

?>
</body>