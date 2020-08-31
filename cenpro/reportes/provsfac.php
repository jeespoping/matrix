<?php
include_once("conex.php");
//*************************************************************************************************************************************************************
//--------------------------------------------------------ACTUALIZACIONES-------------------------------------------------------------------------------------
//*************************************************************************************************************************************************************

//-2018-07-04- Juan Felipe Balcero L.
//-Se agrega la opción de exportar a Excel y el nombre de los productos al reporte.
//-Se actualiza el encabezado, los colores de las tablas, se agrega un botón de cerrado y botones para elegir la fecha en los inputs.
?>
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
  	<title>MATRIX PRODUCIDO VS FACTURADO CENTRAL DE MEZCLAS</title>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />   	
<style type="text/css">
body{
    width : 100%!important;
	margin:	0;
}
.titulopagina2{
    border-bottom-width: 1px;
    border-left-width: 1px;
    border-top-width: 1px;
    font-family: verdana;
    font-size: 18pt;
    font-weight: bold;
    height: 30px;
    margin: 2pt;
    overflow: hidden;
    text-transform: uppercase;
}
table.sample {
	border-width: 1px;
	border-style: solid;
	border-color: gray;
	border-collapse: separate;
	table-layout:fixed;
}
table.sample td {
	border-width: 1px;
	border-style: solid;
	border-color: gray;
	width:7em;
	height:1.2em;
	
}

table.sample1 {
	border-width: 1px;
	border-style: solid;
	border-color: gray;
	border-collapse: separate;
	table-layout:fixed;
}
table.sample1 td {
	border-width: 1px;
	border-style: solid;
	border-color: gray;
	width:7em;
	
}

table.sample2 {
	border-width: 1px;
	border-style: solid;
	border-color: gray;
	border-collapse: separate;
	table-layout:fixed;
}

#tipo1{
	border-width: 1px;
	border-style: solid;
	border-color: gray;
	width:28em;
	height:1.2em;
	text-align:left;
	color:#000000;
	font-family:Tahoma;
	font-weight:bold;
}

#tipo2{
	border-width: 1px;
	border-style: solid;
	border-color: gray;
	width:7em;
	height:1.2em;
	text-align:right;
	color:#000000;
	font-family:Tahoma;
	font-weight:bold;
}
th{
    background-color: #2a5db0;
    color: #ffffff;
    padding: 5px;
}
tr.entrada:nth-child(even){
    background-color:   #E8EEF7;
    text-transform:     uppercase;
}
tr.entrada:nth-child(odd){
    background-color:   #C3D9FF;
    text-transform:     uppercase;
}
tr#totales{
    background-color:   #ffffcc;
    text-transform:     uppercase;
	color			:	black;
}
#totales td{
	font-weight	:	bold;
}
.centrado{
    margin: 0 auto;
}

</style>
<script src="../../../include/gentelella/vendors/jquery/dist/jquery.min.js" ></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>  
<script type="text/javascript">
$(function(){
	
	var fechaActual = new Date();
	$("#wfec1,#wfec2").datepicker({
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
		yearSuffix: '',
		showOn: "button",
		buttonImage: "../../images/medical/root/calendar.gif",
		buttonImageOnly: true,
		changeMonth: true,
		changeYear: true,
		maxDate:fechaActual
    });	 
	
		 
}); 
        
function Exportar(){
	//Función que exporta los resultados a excel y permite la descarga
     
    //Creamos un Elemento Temporal en forma de enlace
    var tmpElemento = document.createElement('a');
    var data_type = 'data:application/vnd.ms-excel'; //Formato anterior xls

    // Obtenemos la información de la tabla
    var tabla_div = document.getElementById('tablaExcel');
    var tabla_html = tabla_div.outerHTML.replace(/ /g, '%20');
            
    tmpElemento.href = data_type + ', ' + tabla_html;
    //Asignamos el nombre a nuestro EXCEL
    tmpElemento.download = 'reporte_producido_vs_facturado.xls';
    // Simulamos el click al elemento creado para descargarlo
    tmpElemento.click();

}

function Cerrar(){
	var confirmacion = confirm("Esta seguro que desea salir?");
	if(confirmacion == true)
	{
		window.open('','_parent',''); 
		window.close();
	}
}

</script>

</head>
<body BGCOLOR="FFFFFF" width=100%>
<BODY TEXT="#000066">
 
<?php

$wactualiz = '2018-07-04';

$titulo = 'PRODUCIDO VS FACTURADO CENTRAL DE MEZCLAS';
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	

	

	include_once("root/comun.php");
            
    encabezado("<div class='titulopagina2'>{$titulo}</div>", $wactualiz, "clinica");

	include_once("movhos/otros.php");
	$bd='movhos';
	connectOdbc($conex_o, 'inventarios');
	if ($conex_o!=0)
	{
		echo "<form name='provsfac' action='provsfac.php' method=post>";
		echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";

		if( !isset($wfec1) or !isset($wfec2) or $wfec1=='' or $wfec2=='')
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Fecha inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='text' id='wfec1' name='wfec1' size='10' readonly value='".date('Y-m')."-01'></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Fecha final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='text' id='wfec2' name='wfec2' size='10' readonly value='".date('Y-m-d')."'></td></tr>";
			echo "<td bgcolor=#cccccc align=center colspan=2><input type='checkbox' name='det' checked>Imprimir detallado</td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='Generar'></td></tr>";
			echo "<td bgcolor=#cccccc  colspan=2 align=center><input type='button' id='cancel_edit' onclick='Cerrar()' value='Cerrar' class='centrado'></td></tr></table>";
			echo "<input type='hidden' id='ready'>";
		}
		else
		{
			//se trabaja con el mes de la primera fecha del rango, si es el mes actual, se trabaja con el costo del mes pasado
			$exp=explode ('-', $wfec1);
			if($exp[1]<date('m') and $exp[0]<=date('Y'))
			{
				$mes=$exp[1];
				$ano=$exp[0];
			}
			else
			{
				$mes=$exp[1]-1;
				if($mes<=0)
				{
					$mes=12;
					$ano=$exp[0]-1;
				}
				else
				{
					$ano=$exp[0];
				}
			}

			if(strlen($mes)==1)
			{
				$mes='0'.$mes;
			}
			$producido=0;
			$interno=0;
			$externo=0;
			$cantidad=0;

			echo "<table border=0 align=center>";

			echo "<tr><td bgcolor=#dddddd colspan='3'><font face='tahoma'><b>FECHA INICIAL: </b>".$wfec1."</td></tr>";
			echo "<tr><td bgcolor=#dddddd colspan='3'><font face='tahoma'><b>FECHA FINAL : </b>".$wfec2."</td></tr>";
			echo "<tr><td style='text-align: center;'><input type='button' id='exportar' onclick='Exportar()' value='Exportar a Excel' class='centrado'></td>";
			echo "<td style='text-align: center;'><input type='button' id='cancel_edit' onclick='Cerrar()' value='Cerrar' class='centrado'></td>";
			echo "<td colspan=2 align=center><input type='submit' value='Retornar'></td></tr>";

			//traigo la cantidad dosis adaptadas, las nutriciones y los productos codificados
			//consulto los diferentes tipos de producto

			//Dosis adaptadas
			$query = "SELECT Plocod, Plopro, Plocin, Artcom ";
			$query .=" from ".$empresa."_000001, ".$empresa."_000002, ".$empresa."_000004 L ";
			$query .= " where  tippro = 'on' " ;
			$query .= " and   L.Fecha_data between '".$wfec1."' and '".$wfec2."' " ;
			$query .= " and   plopro=Artcod " ;
			$query .= " and   ploest='on' " ;
			$query .= " and   Arttip=tipcod " ;
			$query .= " and   tipcdo='off' " ;
			$query .= " and   tipnco='on' " ;
			$query .= " order by 2, 1 " ;

			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);

			echo "</table></br><table align=center class='sample1' cellspacing=0>";
			echo "<tr><th align=center><font face='tahoma' size=1><b>CODIGO</b></font></th>";
			echo "<th align=center style='width:25%;'><font face='tahoma' size=1><b>PRODUCTO</b></font></th>";
			echo "<th align=center><font face='tahoma' size=1><b>LOTE</b></font></th>";
			echo "<th align=center><font face='tahoma' size=1><b>INSUMO</b></font></th>";
			echo "<th align=center><font face='tahoma' size=1><b>CANTIDAD USADA</b></font></th>";
			echo "<th align=center><font face='tahoma' size=1><b>COSTO PRODUCCION</b></font></th>";
			echo "<th align=center><font face='tahoma' size=1><b>CANTIDAD FACTURACION INTERNA</b></font></th>";
			echo "<th align=center><font face='tahoma' size=1><b>VALOR FACTURA INTERNA</b></font></th>";
			echo "<th align=center><font face='tahoma' size=1><b>CANTIDAD FACTURACION EXTERNA</b></font></th>";
			echo "<th align=center><font face='tahoma' size=1><b>VALOR FACTURA EXTERNA</b></font></th></tr>";

			$dataExcel = "  <thead><tr> ";
            $dataExcel .="      <th>Codigo</th> ";
            $dataExcel .="      <th>Producto</th> ";
            $dataExcel .="      <th>Lote</th> ";
            $dataExcel .="      <th>Insumo</th> ";
            $dataExcel .="      <th>Cantidad_usada</th> ";
            $dataExcel .="      <th>Costo_produccion</th> ";
            $dataExcel .="      <th>Cantidad_facturacion_interna</th> ";
            $dataExcel .="      <th>Valor_factura_interna</th> ";
            $dataExcel .="      <th>Cantidad_facturacion_externa</th> ";
            $dataExcel .="      <th>Valor_factura_externa</th> ";
            $dataExcel .="      </tr></thead> ";
            $dataExcel .="      <tbody> ";

			$tot=0;
			$fac1=0;
			$fac2=0;
			$fac3=0;
			$list1=0;
			$list2=0;
			
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);

				$tot=$tot+$row[2];

				if(is_int($i/2))
				{
					$color='#FFFFFF';
				}
				else
				{
					$color="#dddddd";
				}

				$query  = "SELECT Mdepre, Mdecan ";
				$query .=" from ".$empresa."_000007, ".$empresa."_000008 ";
				$query .= " where  congas = 'on' " ;
				$query .= "   and  conind = '-1' ";
				$query .= "   and  mdecon = concod ";
				$query .= "   and  mdeest = 'on' ";
				$query .= "   and  mdepre <> '' ";
				$query .= "   and  mdenlo='".$row[0]."-".$row[1]."' ";

				$err2 = mysql_query($query,$conex);
				$num2 = mysql_num_rows($err2);

				if(isset($det))
				{
					$prod1=0;
					$prod2=0;
					$prod3=0;
					$prod4=0;
					$prod5=0;
					$prod6=0;

				}

				for ($j=0;$j<$num2;$j++)
				{
					$row2 = mysql_fetch_array($err2);
					$query  = " SELECT Mdenlo, sum(A.Mdecan*C.Conind) ";
					$query .= " from ".$empresa."_000007 A, ".$empresa."_000008 C ";
					$query .= " where  C.conane = 'on' " ;
					$query .= "   and  A.mdecon = C.concod ";
					$query .= "   and  mid(A.Mdepre, 1,instr(A.Mdepre,'-')-1) = '".$row2[0]."' ";
					$query .= "   and  mdenlo='".$row[0]."-".$row[1]."' ";
					$query .= "   group by  1 ";


					$err1 = mysql_query($query,$conex);
					$row1 = mysql_fetch_array($err1);

					$query = "SELECT salvuc, salpro ";
					$query .=" from ".$empresa."_000014  ";
					$query .= " where  salano = ".$ano." " ;
					$query .= "   and  salmes = ".$mes." " ;
					$query .= "   and  salcod = '".$row2[0]."' " ;

					$errv = mysql_query($query,$conex);
					$rowv = mysql_fetch_array($errv);

					$q= "SELECT arttarval"
					."     FROM ivarttar "
					."    WHERE arttartar = '*' "
					."    and arttarcod = '".$row2[0]."' ";

					//echo $q;
					$err_o= odbc_do($conex_o,$q);
					if(odbc_fetch_row($err_o))
					{
						$rowv2[0]=odbc_result($err_o,1);
					}
					else
					{
						$rowv2[0]=$rowv[0];
					}

					if(isset($det))
					{
						$query  = " SELECT sum(A.Mdecan*C.Conind) ";
						$query .= " from ".$empresa."_000007 A, ".$empresa."_000008 C ";
						$query .= " where  C.concar = 'on' " ;
						$query .= "   and  A.mdecon = C.concod ";
						$query .= "   and  mdenlo='".$row[0]."-".$row[1]."' ";

						$errc = mysql_query($query,$conex);
						$rowc = mysql_fetch_array($errc);

						$prod1=$row[2];
						if($rowv[1] == 0)
						{
							$rowv[1] = 1;
						}	
						$prod2=$prod2+$row2[1]*$rowv[0]/$rowv[1];
						$prod3=$rowc[0]*-1;
						$prod4=$prod4+$row1[1]*$rowv2[0];
						$prod5=0;
						$prod6=0;

					}
					else
					{
						if($rowv[1] == 0)
						{
							$rowv[1] = 1;
						}
						echo "<tr class='entrada'>";
						echo "<td><font face='tahoma' size=2>".$row[1]."</font></td>";
						echo "<td><font face='tahoma' size=2>".$row[3]."</font></td>";
						echo "<td><font face='tahoma' size=2>".$row[0]."</font></td>";
						echo "<td><font face='tahoma' size=2>".$row2[0]."</font></td>";
						echo "<td align='right'><font face='tahoma' size=2 >".number_format(($row2[1]),2,'.',',')."</font></td>";
						echo "<td align='right'><font face='tahoma' size=2>".number_format(($row2[1]*$rowv[0]/$rowv[1]),2,'.',',')."</font></td>";
						echo "<td align='right'><font face='tahoma' size=2>".number_format(($row1[1]*$rowv[1]),2,'.',',')."</font></td>";
						echo "<td align='right'><font face='tahoma' size=2 >".number_format(($row1[1]*$rowv2[0]),2,'.',',')."</font></td>";
						echo "<td align='right'><font face='tahoma' size=2>".number_format(0,2,'.',',')."</font></td>";
						echo "<td align='right'><font face='tahoma' size=2>".number_format(0,2,'.',',')."</font></td>";
						echo "</tr>";

						$dataExcel .= "<tr>";
						$dataExcel .= "<td>".$row[1]."</td>";
						$dataExcel .= "<td>".$row[3]."</td>";
						$dataExcel .= "<td>".$row[0]."</td>";
						$dataExcel .= "<td>".$row2[0]."</td>";
						$dataExcel .= "<td>".number_format(($row2[1]),2,'.',',')."</td>";
						$dataExcel .= "<td>".number_format(($row2[1]*$rowv[0]/$rowv[1]),2,'.',',')."</td>";
						$dataExcel .= "<td>".number_format(($row1[1]*$rowv[1]),2,'.',',')."</td>";
						$dataExcel .= "<td>".number_format(($row1[1]*$rowv2[0]),2,'.',',')."</td>";
						$dataExcel .= "<td>".number_format(0,2,'.',',')."</td>";
						$dataExcel .= "<td>".number_format(0,2,'.',',')."</td>";
						$dataExcel .= "</tr>";
					}

					$fac1=$fac1+($row2[1]*$rowv[0]/$rowv[1]);
					$fac2=$fac2+($row1[1]*$rowv2[0]);
					$fac3=0;
				}

				if(isset($det))
				{
					echo "<tr class='entrada'>";
					echo "<td><font face='tahoma' size=2>".$row[1]."</font></td>";
					echo "<td><font face='tahoma' size=2>".$row[3]."</font></td>";
					echo "<td><font face='tahoma' size=2>".$row[0]."</font></td>";
					echo "<td ><font face='tahoma' size=2>&nbsp;</font></td>";
					echo "<td align='right'><font face='tahoma' size=2 >".number_format($prod1,2,'.',',')."</font></td>";
					echo "<td align='right'><font face='tahoma' size=2>".number_format(($prod2),2,'.',',')."</font></td>";
					echo "<td align='right'><font face='tahoma' size=2>".number_format(($prod3),2,'.',',')."</font></td>";
					echo "<td align='right'><font face='tahoma' size=2 >".number_format(($prod4),2,'.',',')."</font></td>";
					echo "<td align='right'><font face='tahoma' size=2>".number_format(0,2,'.',',')."</font></td>";
					echo "<td align='right'><font face='tahoma' size=2>".number_format(0,2,'.',',')."</font></td>";
					echo "</tr>";
					$list1=$list1+$prod3;
					$list2=$list2+$prod5;

					$dataExcel .= "<tr>";
					$dataExcel .= "<td>".$row[1]."</td>";
					$dataExcel .= "<td>".$row[3]."</td>";
					$dataExcel .= "<td>".$row[0]."</td>";
					$dataExcel .= "<td></td>";
					$dataExcel .= "<td>".number_format($prod1,2,'.',',')."</td>";
					$dataExcel .= "<td>".number_format(($prod2),2,'.',',')."</td>";
					$dataExcel .= "<td>".number_format(($prod3),2,'.',',')."</td>";
					$dataExcel .= "<td>".number_format(($prod4),2,'.',',')."</td>";
					$dataExcel .= "<td>".number_format(0,2,'.',',')."</td>";
					$dataExcel .= "<td>".number_format(0,2,'.',',')."</td>";
					$dataExcel .= "</tr>";
				}

			}			

			if($num>0)
			{
				echo "<tr id='totales'><b>";
				echo "<td colspan='5'><font face='tahoma' size=2>DOSIS(".$tot." Un.)</font></td>";
				echo "<td align='right'><font face='tahoma' size=2>".number_format($fac1,2,'.',',')."</font></td>";
				$dataExcel .= "<tr>";
				$dataExcel .= "<td colspan='5'>DOSIS(".$tot." Un.)</td>";
				$dataExcel .= "<td>".number_format($fac1,2,'.',',')."</td>";
				if(isset($det))
				{
					echo "<td align='right'><font face='tahoma' size=2><font face='tahoma' size=2>".number_format(($list1),2,'.',',')."</font></td>";
					$dataExcel .= "<td>".number_format(($list1),2,'.',',')."</td>";
				}
				else
				{
					echo "<td align='right'><font face='tahoma' size=2>&nbsp;</font></td>";
					$dataExcel .= "<td></td>";
				}
				echo "<td align='right'><font face='tahoma' size=2 >".number_format($fac2,2,'.',',')."</font></td>";
				echo "<td align='right'><font face='tahoma' size=2>&nbsp;</font></td>";
				echo "<td align='right'><font face='tahoma' size=2>".number_format($fac3,2,'.',',')."</font></td>";
				echo "</b></tr>";

				
				$dataExcel .= "<td>".number_format($fac2,2,'.',',')."</td>";
				$dataExcel .= "<td></td>";
				$dataExcel .= "<td>".number_format($fac3,2,'.',',')."</td>";
				$dataExcel .= "</tr>";
				$dataExcel .= "<tr></tr>";

				$cantidad=$cantidad+$tot;
				$producido=$producido+$fac1;
				$interno=$interno+$fac2;
				$externo=$externo+$fac3;
			}
			echo "</table></br>";

			//Productos codificados
			$query = "SELECT Plocod, Plopro, Plocin, Artcom ";
			$query .=" from ".$empresa."_000001, ".$empresa."_000002, ".$empresa."_000004 L ";
			$query .= " where  tippro = 'on' " ;
			$query .= " and   L.Fecha_data between '".$wfec1."' and '".$wfec2."' " ;
			$query .= " and   plopro=Artcod " ;
			$query .= " and   ploest='on' " ;
			$query .= " and   Arttip=tipcod " ;
			$query .= " and   tipcdo='on' " ;
			$query .= " order by 2, 1 " ;

			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);

			echo "<table align=center class='sample1' cellspacing=0>";
			echo "<tr><th align=center bgcolor=#999999><font face='tahoma' size=1><b>CODIGO</b></font></th>";
			echo "<th align=center bgcolor=#999999 width='25%'><font face='tahoma' size=1><b>PRODUCTO</b></font></th>";
			echo "<th align=center bgcolor=#999999 ><font face='tahoma' size=1><b>LOTE</b></font></th>";
			echo "<th align=center bgcolor=#999999 ><font face='tahoma' size=1><b>INSUMO</b></font></th>";
			echo "<th align=center bgcolor=#999999 ><font face='tahoma' size=1><b>CANTIDAD USADA</b></font></th>";
			echo "<th align=center bgcolor=#999999 ><font face='tahoma' size=1><b>COSTO PRODUCCION</b></font></th>";
			echo "<th align=center bgcolor=#999999 ><font face='tahoma' size=1><b>CANTIDAD FACTURACION INTERNA</b></font></th>";
			echo "<th align=center bgcolor=#999999 ><font face='tahoma' size=1><b>VALOR FACTURA INTENRA</b></font></th>";
			echo "<th align=center bgcolor=#999999 ><font face='tahoma' size=1><b>CANTIDAD FACTURACION EXTERNA</b></font></th>";
			echo "<th align=center bgcolor=#999999 ><font face='tahoma' size=1><b>VALOR FACTURA EXTERNA</b></font></th>";

			$dataExcel .= "  <tr> ";
            $dataExcel .="      <td>Codigo</td> ";
            $dataExcel .="      <td>Producto</td> ";
            $dataExcel .="      <td>Lote</td> ";
            $dataExcel .="      <td>Insumo</td> ";
            $dataExcel .="      <td>Cantidad_usada</td> ";
            $dataExcel .="      <td>Costo_produccion</td> ";
            $dataExcel .="      <td>Cantidad_facturacion_interna</td> ";
            $dataExcel .="      <td>Valor_factura_interna</td> ";
            $dataExcel .="      <td>Cantidad_facturacion_externa</td> ";
            $dataExcel .="      <td>Valor_factura_externa</td> ";
            $dataExcel .="      </tr> ";

			$tot=0;
			$tot1=0;
			$tot2=0;
			$fac1=0;
			$fac2=0;
			$fac3=0;
			$fac4=0;
			$fac5=0;
			$list1=0;
			$list2=0;


			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);

				$tot=$tot+$row[2];
				if(is_int($i/2))
				{
					$color='#FFFFFF';
				}
				else
				{
					$color="#dddddd";
				}

				$query = " SELECT Mdecan ";
				$query .=" from ".$empresa."_000007 A, ".$empresa."_000008 C ";
				$query .= " where  C.conven = 'on' " ;
				$query .= "   and  C.conind = '-1' " ;
				$query .= "   and  A.Mdecon   = C.concod  ";
				$query .= "   and  mdenlo='".$row[0]."-".$row[1]."' ";

				$errv = mysql_query($query,$conex);
				$numv = mysql_num_rows($errv);

				if ($numv>0)
				{
					$rowv = mysql_fetch_array($errv);
					$venta='si';
					$tra='no';
					$can=$rowv[0];
					$tot1=$tot1+$row[2];
					$can1=0;
				}
				else
				{
					$tot2=$tot2+$row[2];
					$venta='no';
					$tra='no';
					$can=0;
					$can1=0;
				}

				$query = " SELECT sum(Mdecan*Conind*-1) ";
				$query .=" from ".$empresa."_000007 A, ".$empresa."_000008 C ";
				$query .= " where  C.contra = 'on' " ;
				$query .= "   and  A.Mdecon   = C.concod  ";
				$query .= "   and  mdenlo='".$row[0]."-".$row[1]."' ";

				$errt = mysql_query($query,$conex);
				$numt = mysql_num_rows($errt);

				if ($numt>0)
				{
					$rowt = mysql_fetch_array($errt);
					$tra='si';
					$venta='no';
					$can1=$rowt[0];
					$tot2=$tot2+$row[2];
				}


				$query  = " SELECT sum(A.Mdecan*C.Conind*-1) ";
				$query .= " from ".$empresa."_000007 A, ".$empresa."_000008 C ";
				$query .= " where  C.concar = 'on' " ;
				$query .= "   and  A.mdecon = C.concod ";
				$query .= "   and  mdenlo='".$row[0]."-".$row[1]."' ";

				$errc = mysql_query($query,$conex);
				$numc = mysql_num_rows($errc);

				if ($numc>0)
				{
					$rowc = mysql_fetch_array($errc);
					$tra='no';
					$venta='no';
					$can1=$can1+$rowc[0];
					$tot2=$tot2+$row[2];
				}


				$query  = "SELECT Mdepre, Mdecan ";
				$query .=" from ".$empresa."_000007, ".$empresa."_000008 ";
				$query .= " where  congas = 'on' " ;
				$query .= "   and  conind = '-1' ";
				$query .= "   and  mdecon = concod ";
				$query .= "   and  mdeest = 'on' ";
				$query .= "   and  mdepre <> '' ";
				$query .= "   and  mdenlo='".$row[0]."-".$row[1]."' ";

				$err2 = mysql_query($query,$conex);
				$num2 = mysql_num_rows($err2);


				$q= "SELECT arttarval"
				."     FROM ivarttar "
				."    WHERE arttartar = '*' "
				."    and arttarcod = '".strtoupper($row[1])."' ";

				//echo $q;
				$err_o= odbc_do($conex_o,$q);
				if(odbc_fetch_row($err_o))
				{
					$valp=odbc_result($err_o,1);
				}
				else
				{
					$valp=odbc_result($err_o,1);
				}

				if(isset($det))
				{
					$prod1=0;
					$prod2=0;
					$prod3=0;
					$prod4=0;
					$prod5=0;
					$prod6=0;
				}

				$list1=$list1+$can1;
				$list2=$list2+$can;
				$fac2=$fac2+($can1*$valp);
				$fac3=$fac3+($can*$valp);

				for ($j=0;$j<$num2;$j++)
				{
					$row2 = mysql_fetch_array($err2);

					//VALOR DEL INSUMO
					$query = "SELECT salvuc, salpro ";
					$query .=" from ".$empresa."_000014  ";
					$query .= " where  salano = ".$ano." " ;
					$query .= "   and  salmes = ".$mes." " ;
					$query .= "   and  salcod = '".$row2[0]."' " ;

					$errv = mysql_query($query,$conex);
					$rowv = mysql_fetch_array($errv);

					//consulto si el producto fue vendido cuanto de eso fue vendido

					if(isset($det))
					{
						$prod1=$row[2];
						$prod2=$prod2+$row2[1]*$rowv[0]/$rowv[1];
						$prod3=$can1;
						$prod4=$can1*$valp;
						$prod5=$can;
						$prod6=$can*$valp;
					}
					else
					{
						echo "<tr class='entrada'>";
						echo "<td><font face='tahoma' size=2>".$row[1]."</font></td>";
						echo "<td><font face='tahoma' size=2>".$row[3]."</font></td>";
						echo "<td><font face='tahoma' size=2>".$row[0]."</font></td>";
						echo "<td><font face='tahoma' size=2>".$row2[0]."</font></td>";
						echo "<td align='right'><font face='tahoma' size=2 >".number_format(($row2[1]),2,'.',',')."</font></td>";
						echo "<td align='right'><font face='tahoma' size=2>".number_format(($row2[1]*$rowv[0]/$rowv[1]),2,'.',',')."</font></td>";
						echo "<td align='right'><font face='tahoma' size=2>".number_format(($can1),2,'.',',')."</font></td>";
						echo "<td align='right'><font face='tahoma' size=2 >".number_format(($can1*$valp),2,'.',',')."</font></td>";
						echo "<td align='right'><font face='tahoma' size=2>".number_format($can,2,'.',',')."</font></td>";
						echo "<td align='right'><font face='tahoma' size=2>".number_format($can*$valp,2,'.',',')."</font></td>";
						echo "</tr>";

						$dataExcel .= "<tr>";
						$dataExcel .= "<td>".$row[1]."</td>";
						$dataExcel .= "<td>".$row[3]."</td>";
						$dataExcel .= "<td>".$row[0]."</td>";
						$dataExcel .= "<td>".$row2[0]."</td>";
						$dataExcel .= "<td>".number_format(($row2[1]),2,'.',',')."</td>";
						$dataExcel .= "<td>".number_format(($row2[1]*$rowv[0]/$rowv[1]),2,'.',',')."</td>";
						$dataExcel .= "<td>".number_format(($can1),2,'.',',')."</td>";
						$dataExcel .= "<td>".number_format(($can1*$valp),2,'.',',')."</td>";
						$dataExcel .= "<td>".number_format($can,2,'.',',')."</td>";
						$dataExcel .= "<td>".number_format($can*$valp,2,'.',',')."</td>";
						$dataExcel .= "</tr>";
					}

					$fac1=$fac1+($row2[1]*$rowv[0]/$rowv[1]);

				}

				if(isset($det))
				{
					echo "<tr class='entrada'>";
					echo "<td><font face='tahoma' size=2>".$row[1]."</font></td>";
					echo "<td><font face='tahoma' size=2>".$row[3]."</font></td>";
					echo "<td><font face='tahoma' size=2>".$row[0]."</font></td>";
					echo "<td><font face='tahoma' size=2>&nbsp;</font></td>";
					echo "<td align='right'><font face='tahoma' size=2 >".number_format($prod1,2,'.',',')."</font></td>";
					echo "<td align='right'><font face='tahoma' size=2>".number_format(($prod2),2,'.',',')."</font></td>";
					echo "<td align='right'><font face='tahoma' size=2>".number_format(($can1),2,'.',',')."</font></td>";
					echo "<td align='right'><font face='tahoma' size=2 >".number_format(($can1*$valp),2,'.',',')."</font></td>";
					echo "<td align='right'><font face='tahoma' size=2>".number_format($prod5,2,'.',',')."</font></td>";
					echo "<td align='right'><font face='tahoma' size=2>".number_format($prod6,2,'.',',')."</font></td>";
					echo "</tr>";

					$dataExcel .= "<tr>";
					$dataExcel .= "<td>".$row[1]."</td>";
					$dataExcel .= "<td>".$row[3]."</td>";
					$dataExcel .= "<td>".$row[0]."</td>";
					$dataExcel .= "<td></td>";
					$dataExcel .= "<td>".number_format($prod1,2,'.',',')."</td>";
					$dataExcel .= "<td>".number_format(($prod2),2,'.',',')."</td>";
					$dataExcel .= "<td>".number_format(($can1),2,'.',',')."</td>";
					$dataExcel .= "<td>".number_format(($can1*$valp),2,'.',',')."</td>";
					$dataExcel .= "<td>".number_format($prod5,2,'.',',')."</td>";
					$dataExcel .= "<td>".number_format($prod6,2,'.',',')."</td>";
					$dataExcel .= "</tr>";
				}

			}

			if($num>0)
			{
				echo "<tr id='totales'>";
				echo "<td id='tipo1' colspan='5'><font face='tahoma' size=2>CODIFICADOS (".$tot." Un.)</font></td>";
				echo "<td align='right' id='tipo2'><font face='tahoma' size=2>".number_format($fac1,2,'.',',')."</font></td>";
				echo "<td align='right' id='tipo2'><font face='tahoma' size=2>".number_format(($list1),2,'.',',')."</font></td>";
				echo "<td align='right' id='tipo2'><font face='tahoma' size=2 >".number_format($fac2,2,'.',',')."</font></td>";
				echo "<td align='right' id='tipo2'><font face='tahoma' size=2>".number_format(($list2),2,'.',',')."</font></td>";
				echo "<td align='right' id='tipo2'><font face='tahoma' size=2>".number_format($fac3,2,'.',',')."</font></td>";
				echo "</tr>";

				$dataExcel .= "<tr>";
				$dataExcel .= "<td colspan='5'>CODIFICADOS (".$tot." Un.)</td>";
				$dataExcel .= "<td>".number_format($fac1,2,'.',',')."</td>";
				$dataExcel .= "<td>".number_format(($list1),2,'.',',')."</td>";
				$dataExcel .= "<td>".number_format($fac2,2,'.',',')."</td>";
				$dataExcel .= "<td>".number_format(($list2),2,'.',',')."</td>";
				$dataExcel .= "<td>".number_format($fac3,2,'.',',')."</td>";
				$dataExcel .= "</tr>";
				$dataExcel .= "<tr></tr>";

				$cantidad=$cantidad+$tot;
				$producido=$producido+$fac1;
				$interno=$interno+$fac2;
				$externo=$externo+$fac3;
			}

			//Nutriciones
			$query = "SELECT Plocod, Plopro, Plocin, Artcom ";
			$query .=" from ".$empresa."_000001, ".$empresa."_000002, ".$empresa."_000004 L ";
			$query .= " where  tippro = 'on' " ;
			$query .= " and   L.Fecha_data between '".$wfec1."' and '".$wfec2."' " ;
			$query .= " and   plopro=Artcod " ;
			$query .= " and   ploest='on' " ;
			$query .= " and   Arttip=tipcod " ;
			$query .= " and   tipcdo='off' " ;
			$query .= " and   tipnco='off' " ;
			$query .= " order by 2, 1 " ;

			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);

			echo "</table></br><table align=center class='sample1' cellspacing=0>";
			echo "<tr><th><font face='tahoma' size=1><b>CODIGO</b></font></th>";
			echo "<th width='18%'><font face='tahoma' size=1><b>PRODUCTO</b></font></th>";
			echo "<th><font face='tahoma' size=1><b>LOTE</b></font></th>";
			echo "<th><font face='tahoma' size=1><b>INSUMO</b></font></th>";
			echo "<th><font face='tahoma' size=1><b>CANTIDAD USADA</b></font></th>";
			echo "<th><font face='tahoma' size=1><b>COSTO PRODUCCION</b></font></th>";
			echo "<th><font face='tahoma' size=1><b>CANTIDAD FACTURACION INTERNA</b></font></th>";
			echo "<th><font face='tahoma' size=1><b>VALOR FACTURA INTENRA</b></font></th>";
			echo "<th><font face='tahoma' size=1><b>CANTIDAD FACTURACION EXTERNA</b></font></th>";
			echo "<th><font face='tahoma' size=1><b>VALOR FACTURA EXTERNA</b></font></th>";

			$dataExcel .= "  <tr> ";
            $dataExcel .="      <td>Codigo</td> ";
            $dataExcel .="      <td>Producto</td> ";
            $dataExcel .="      <td>Lote</td> ";
            $dataExcel .="      <td>Insumo</td> ";
            $dataExcel .="      <td>Cantidad_usada</td> ";
            $dataExcel .="      <td>Costo_produccion</td> ";
            $dataExcel .="      <td>Cantidad_facturacion_interna</td> ";
            $dataExcel .="      <td>Valor_factura_interna</td> ";
            $dataExcel .="      <td>Cantidad_facturacion_externa</td> ";
            $dataExcel .="      <td>Valor_factura_externa</td> ";
            $dataExcel .="      </tr> ";

			$tot=0;
			$tot1=0;
			$tot2=0;
			$fac1=0;
			$fac2=0;
			$fac3=0;
			$fac4=0;
			$fac5=0;
			$list1=0;
			$list2=0;


			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);

				$tot=$tot+$row[2];
				if(is_int($i/2))
				{
					$color='#FFFFFF';
				}
				else
				{
					$color="#dddddd";
				}

				$query = " SELECT Mdecan ";
				$query .=" from ".$empresa."_000007 A, ".$empresa."_000008 C ";
				$query .= " where  C.conven = 'on' " ;
				$query .= "   and  C.conind = '-1' " ;
				$query .= "   and  A.Mdecon   = C.concod  ";
				$query .= "   and  mdenlo='".$row[0]."-".$row[1]."' ";

				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);

				if ($num1>0)
				{
					$row1 = mysql_fetch_array($err1);
					$venta='si';
					$can=$row1[0];
					$tot1=$tot1+$row[2];
				}
				else
				{
					$tot2=$tot2+$row[2];
					$venta='no';
					$can=0;
				}

				$query  = "SELECT Mdepre, Mdecan ";
				$query .=" from ".$empresa."_000007, ".$empresa."_000008 ";
				$query .= " where  congas = 'on' " ;
				$query .= "   and  conind = '-1' ";
				$query .= "   and  mdecon = concod ";
				$query .= "   and  mdeest = 'on' ";
				$query .= "   and  mdepre <> '' ";
				$query .= "   and  mdenlo='".$row[0]."-".$row[1]."' ";

				$err2 = mysql_query($query,$conex);
				$num2 = mysql_num_rows($err2);


				if(isset($det))
				{
					$prod1=0;
					$prod2=0;
					$prod3=0;
					$prod4=0;
					$prod5=0;
					$prod6=0;
				}

				for ($j=0;$j<$num2;$j++)
				{
					$row2 = mysql_fetch_array($err2);

					$query = "SELECT salvuc, salpro ";
					$query .=" from ".$empresa."_000014  ";
					$query .= " where  salano = ".$ano." " ;
					$query .= "   and  salmes = ".$mes." " ;
					$query .= "   and  salcod = '".$row2[0]."' " ;

					$errv = mysql_query($query,$conex);
					$rowv = mysql_fetch_array($errv);


					$q= "SELECT arttarval"
					."     FROM ivarttar "
					."    WHERE arttartar = '*' "
					."    and arttarcod = '".$row2[0]."' ";

					$err_o= odbc_do($conex_o,$q);
					if(odbc_fetch_row($err_o))
					{
						$rowv2[0]=odbc_result($err_o,1);
					}
					else
					{
						$rowv2[0]=$rowv[0];
					}

					//consulto si el producto fue vendido cuanto de eso fue vendido

					if($venta=='si')
					{
						//consultamos en matrix el codigo en unix en ml
						$query  = " SELECT Tarcpo, Appcnv, Taruni ";
						$query .= " from ".$empresa."_000009 A, ".$empresa."_000018 C ";
						$query .= " where  Apppre = '".$row2[0]."' " ;
						$query .= "   and  Appcod = Tarcce ";

						$erru = mysql_query($query,$conex);
						$rowu = mysql_fetch_array($erru);


						$q= "SELECT arttarval"
						."     FROM ivarttar "
						."    WHERE arttartar = 'NP' "
						."    and arttarcod = '".$row2[0]."' ";

						//echo $q;
						$err_o= odbc_do($conex_o,$q);
						if(odbc_fetch_row($err_o))
						{
							$val=odbc_result($err_o,1);
							$can1=$row2[1]*$can/$row[2];
							if($rowu[2]=='on')
							{
								$val1=$can1*$val/$rowu[1];
							}
							else
							{
								$val1=$can1*$val;
							}
						}
						else
						{
							$can1=$row2[1]*$can/$row[2];
							$val1=$can1*$rowv[0]/$rowv[1];
						}
						$row1[1]=0;

					}
					else
					{
						$query  = " SELECT Mdenlo, sum(A.Mdecan*C.Conind) ";
						$query .= " from ".$empresa."_000007 A, ".$empresa."_000008 C ";
						$query .= " where  C.conane = 'on' " ;
						$query .= "   and  A.mdecon = C.concod ";
						$query .= "   and  mid(A.Mdepre, 1,instr(A.Mdepre,'-')-1) = '".$row2[0]."' ";
						$query .= "   and  mdenlo='".$row[0]."-".$row[1]."' ";
						$query .= "   group by  1 ";


						$err1 = mysql_query($query,$conex);
						$row1 = mysql_fetch_array($err1);

						$can1=0;
						$val1=0;
					}

					if(isset($det))
					{
						$query  = " SELECT sum(A.Mdecan*C.Conind) ";
						$query .= " from ".$empresa."_000007 A, ".$empresa."_000008 C ";
						$query .= " where  C.concar = 'on' " ;
						$query .= "   and  A.mdecon = C.concod ";
						$query .= "   and  mdenlo='".$row[0]."-".$row[1]."' ";

						$errc = mysql_query($query,$conex);
						$rowc = mysql_fetch_array($errc);

						$prod1=$row[2];
						$prod2=$prod2+$row2[1]*$rowv[0]/$rowv[1];
						$prod3=$rowc[0]*-1;
						$prod4=$prod4+$row1[1]*$rowv2[0];
						$prod5=$can;
						$prod6=$prod6+$val1;

					}
					else
					{
						echo "<tr class='entrada'>";
						echo "<td><font face='tahoma' size=2>".$row[1]."</font></td>";
						echo "<td><font face='tahoma' size=2>".$row[3]."</font></td>";
						echo "<td><font face='tahoma' size=2>".$row[0]."</font></td>";
						echo "<td><font face='tahoma' size=2>".$row2[0]."</font></td>";
						echo "<td align='right'><font face='tahoma' size=2 >".number_format(($row2[1]),2,'.',',')."</font></td>";
						echo "<td align='right'><font face='tahoma' size=2>".number_format(($row2[1]*$rowv[0]/$rowv[1]),2,'.',',')."</font></td>";
						echo "<td align='right'><font face='tahoma' size=2>".number_format(($row1[1]*$rowv[1]),2,'.',',')."</font></td>";
						echo "<td align='right'><font face='tahoma' size=2 >".number_format(($row1[1]*$rowv2[0]),2,'.',',')."</font></td>";
						echo "<td align='right'><font face='tahoma' size=2>".number_format($can1,2,'.',',')."</font></td>";
						echo "<td align='right'><font face='tahoma' size=2>".number_format($val1,2,'.',',')."</font></td>";
						echo "</tr>";

						$dataExcel .= "<tr>";
						$dataExcel .= "<td>".$row[1]."</td>";
						$dataExcel .= "<td>".$row[3]."</td>";
						$dataExcel .= "<td>".$row[0]."</td>";
						$dataExcel .= "<td>".$row2[0]."</td>";
						$dataExcel .= "<td>".number_format(($row2[1]),2,'.',',')."</td>";
						$dataExcel .= "<td>".number_format(($row2[1]*$rowv[0]/$rowv[1]),2,'.',',')."</td>";
						$dataExcel .= "<td>".number_format(($row1[1]*$rowv[1]),2,'.',',')."</td>";
						$dataExcel .= "<td>".number_format(($row1[1]*$rowv2[0]),2,'.',',')."</td>";
						$dataExcel .= "<td>".number_format($can1,2,'.',',')."</td>";
						$dataExcel .= "<td>".number_format($val1,2,'.',',')."</td>";
						$dataExcel .= "</tr>";
					}

					$fac1=$fac1+($row2[1]*$rowv[0]/$rowv[1]);
					if($venta=='si')
					{
						$fac4=$fac4+($row2[1]*$rowv[0]/$rowv[1]);
					}
					else
					{
						$fac5=$fac5+($row2[1]*$rowv[0]/$rowv[1]);
					}
					$fac2=$fac2+($row1[1]*$rowv2[0]);
					$fac3=$fac3+$val1;

				}

				if(isset($det))
				{
					echo "<tr class='entrada'>";
					echo "<td><font face='tahoma' size=2>".$row[1]."</font></td>";
					echo "<td><font face='tahoma' size=2>".$row[3]."</font></td>";
					echo "<td><font face='tahoma' size=2>".$row[0]."</font></td>";
					echo "<td><font face='tahoma' size=2>&nbsp;</font></td>";
					echo "<td align='right'><font face='tahoma' size=2 >".number_format($prod1,2,'.',',')."</font></td>";
					echo "<td align='right'><font face='tahoma' size=2>".number_format(($prod2),2,'.',',')."</font></td>";
					echo "<td align='right'><font face='tahoma' size=2>".number_format(($prod3),2,'.',',')."</font></td>";
					echo "<td align='right'><font face='tahoma' size=2 >".number_format(($prod4),2,'.',',')."</font></td>";
					echo "<td align='right'><font face='tahoma' size=2>".number_format($prod5,2,'.',',')."</font></td>";
					echo "<td align='right'><font face='tahoma' size=2>".number_format($prod6,2,'.',',')."</font></td>";
					echo "</tr>";

					$dataExcel .= "<tr>";
					$dataExcel .= "<td>".$row[1]."</td>";
					$dataExcel .= "<td>".$row[3]."</td>";
					$dataExcel .= "<td>".$row[0]."</td>";
					$dataExcel .= "<td></td>";
					$dataExcel .= "<td>".number_format($prod1,2,'.',',')."</td>";
					$dataExcel .= "<td>".number_format(($prod2),2,'.',',')."</td>";
					$dataExcel .= "<td>".number_format(($prod3),2,'.',',')."</td>";
					$dataExcel .= "<td>".number_format(($prod4),2,'.',',')."</td>";
					$dataExcel .= "<td>".number_format($prod5,2,'.',',')."</td>";
					$dataExcel .= "<td>".number_format($prod6,2,'.',',')."</td>";
					$dataExcel .= "</tr>";

					$list1=$list1+$prod3;
					$list2=$list2+$prod5;
				}

			}

			if($num>0)
			{
				echo "<tr id='totales'>";
				echo "<td id='tipo1' colspan='5'><font face='tahoma' size=2>TOTALES NUTRICIONES INTERNAS(".$tot2." Un.)</font></td>";
				echo "<td align='right' id='tipo2'><font face='tahoma' size=2>".number_format($fac5,2,'.',',')."</font></td>";
				$dataExcel .= "<tr>";
				$dataExcel .= "<td colspan='5'>TOTALES NUTRICIONES INTERNAS(".$tot2." Un.)</td>";
				$dataExcel .= "<td>".number_format($fac5,2,'.',',')."</td>";
				if(isset($det))
				{
					echo "<td align='right' id='tipo2'><font face='tahoma' size=2>".number_format(($list1),2,'.',',')."</font></td>";
					$dataExcel .= "<td>".number_format(($list1),2,'.',',')."</td>";
				}
				else
				{
					echo "<td align='right' id='tipo2'><font face='tahoma' size=2>&nbsp;</font></td>";
					$dataExcel .= "<td></td>";
				}
				echo "<td align='right' id='tipo2'><font face='tahoma' size=2 >".number_format($fac2,2,'.',',')."</font></td>";
				echo "<td align='right' id='tipo2'><font face='tahoma' size=2>&nbsp;</font></td>";
				echo "<td align='right' id='tipo2'><font face='tahoma' size=2>".number_format(0,2,'.',',')."</font></td>";
				echo "</tr>";

				$dataExcel .= "<td>".number_format($fac2,2,'.',',')."</td>";
				$dataExcel .= "<td></td>";
				$dataExcel .= "<td>".number_format(0,2,'.',',')."</td>";
				$dataExcel .= "</tr>";

				echo "<tr id='totales'>";
				echo "<td id='tipo1' colspan='5'><font face='tahoma' size=2>TOTALES NUTRICIONES EXTERNAS(".$tot1." Un.)</font></td>";
				echo "<td align='right' id='tipo2'><font face='tahoma' size=2>".number_format($fac4,2,'.',',')."</font></td>";
				echo "<td align='right' id='tipo2'><font face='tahoma' size=2>&nbsp;</font></td>";
				echo "<td align='right' id='tipo2'><font face='tahoma' size=2 >".number_format(0,2,'.',',')."</font></td>";

				$dataExcel .= "<tr>";
				$dataExcel .= "<td colspan='5'>TOTALES NUTRICIONES EXTERNAS(".$tot1." Un.)</td>";
				$dataExcel .= "<td>".number_format($fac4,2,'.',',')."</td>";
				$dataExcel .= "<td></td>";
				$dataExcel .= "<td>".number_format(0,2,'.',',')."</td>";

				if(isset($det))
				{
					echo "<td align='right' id='tipo2'><font face='tahoma' size=2>".number_format(($list2),2,'.',',')."</font></td>";
					$dataExcel .= "<td>".number_format(($list2),2,'.',',')."</td>";
				}
				else
				{
					echo "<td align='right' id='tipo2'><font face='tahoma' size=2>&nbsp;</font></td>";
					$dataExcel .= "<td></td>";
				}
				echo "<td align='right' id='tipo2'><font face='tahoma' size=2>".number_format($fac3,2,'.',',')."</font></td>";
				echo "</tr>";

				$dataExcel .= "<td>".number_format($fac3,2,'.',',')."</td>";
				$dataExcel .= "</tr>";

				echo "<tr id='totales'>";
				echo "<td id='tipo1' colspan='5'><font face='tahoma' size=2>TOTALES NUTRICIONES (".$tot." Un.)</font></td>";
				echo "<td align='right' id='tipo2'><font face='tahoma' size=2>".number_format($fac1,2,'.',',')."</font></td>";
				echo "<td align='right' id='tipo2'><font face='tahoma' size=2>&nbsp;</font></td>";
				echo "<td align='right' id='tipo2'><font face='tahoma' size=2 >".number_format($fac2,2,'.',',')."</font></td>";
				echo "<td align='right' id='tipo2'><font face='tahoma' size=2>&nbsp;</font></td>";

				echo "<td align='right' id='tipo2'><font face='tahoma' size=2>".number_format($fac3,2,'.',',')."</font></td>";
				echo "</tr>";

				$dataExcel .= "<tr>";
				$dataExcel .= "<td colspan='5'>TOTALES NUTRICIONES (".$tot." Un.)</td>";
				$dataExcel .= "<td>".number_format($fac1,2,'.',',')."</td>";
				$dataExcel .= "<td></td>";
				$dataExcel .= "<td>".number_format($fac2,2,'.',',')."</td>";
				$dataExcel .= "<td></td>";
				$dataExcel .= "<td>".number_format($fac3,2,'.',',')."</td>";
				$dataExcel .= "</tr>";

				$cantidad=$cantidad+$tot;
				$producido=$producido+$fac1;
				$interno=$interno+$fac2;
				$externo=$externo+$fac3;
			}

			if($producido>0)
			{
				echo "<tr id='totales'>";
				echo "<td id='tipo1' colspan='5'><font face='tahoma' size=2>TOTALES PRODUCTOS  (".$cantidad." Un.)</font></td>";
				echo "<td align='right' id='tipo2'><font face='tahoma' size=2>".number_format($producido,2,'.',',')."</font></td>";
				echo "<td align='right' id='tipo2' ><font face='tahoma' size=2>&nbsp;</font></td>";
				echo "<td align='right' id='tipo2'><font face='tahoma' size=2 >".number_format($interno,2,'.',',')."</font></td>";
				echo "<td align='right' id='tipo2'><font face='tahoma' size=2>&nbsp;</font></td>";
				echo "<td align='right' id='tipo2'><font face='tahoma' size=2>".number_format($externo,2,'.',',')."</font></td>";
				echo "</tr></table>";

				$dataExcel .= "<tr>";
				$dataExcel .= "<td colspan='5'>TOTALES PRODUCTOS  (".$cantidad." Un.)</td>";
				$dataExcel .= "<td>".number_format($producido,2,'.',',')."</td>";
				$dataExcel .= "<td></td>";
				$dataExcel .= "<td>".number_format($interno,2,'.',',')."</td>";
				$dataExcel .= "<td></td>";
				$dataExcel .= "<td>".number_format($externo,2,'.',',')."</td>";
				$dataExcel .= "</tr>";
			}

			echo "<table id='tablaExcel' style='display:none;'>".$dataExcel."</table> ";
		}
	}
	else
	{
		echo"<CENTER>";
		echo "<table align='center' border=0 bordercolor=#000080 width=700>";
		echo "<tr><td colspan='2' class='texto4'><font size=3 color='#000080' face='arial' align=center><b>En este momento no es posible conectarse con unix, por favor ingrese mas tarde</td></tr>";
		echo "<tr><td class='texto1' colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		echo "</table>";
	}
}
?>
</body>
</html>