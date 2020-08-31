<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1");







if (isset($accion) and $accion == 'enviarBusqueda')
{  
	
	$data = array('error'=>0,'mensaje'=>'','html'=>'');
	$color="#999999"; 
		
		
		 $query = "select Nom_pac,cod_equ, ".$empresa."_000003.descripcion, cod_exa, ".$empresa."_000006.descripcion, fecha, ".$empresa."_000001.hi, ".$empresa."_000001.hf, ".$empresa."_000001.Asistida, ".$empresa."_000001.Usuario,".$empresa."_000001.Cedula 
				  from ".$empresa."_000001, ".$empresa."_000003, ".$empresa."_000006 
		          where Nom_pac like '%".utf8_decode($wpac)."%'
		          and cod_equ = ".$empresa."_000003.codigo 
		          and cod_exa = ".$empresa."_000006.codigo 
		          and ".$empresa."_000001.cedula like '%".utf8_decode($wced)."%'";
		if ($wper1 != "")
		{
			$query.=" and fecha >= '".$wper1."' ";
		}
		 $query .= " and ".$empresa."_000001.Activo = 'A' ";
		 $query .= "Group by Nom_pac,cod_equ, cod_exa
			Order by fecha,hi		 
		           ";
		//$query .= "  fecha between '".$wper1."' and '".$wper2."' ";
		//$query .= "  where fecha = '".$wper1."' ";
		
		if($err = mysql_query( $query,$conex ))
		{
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				$data['html'] ="<table border=0 align=center>";
				$data['html'] .="<tr class='encabezadotabla'><td align=center colspan=9>CONSULTA DE TURNOS X PACIENTE</td></tr>";
				$data['html'] .="<tr class='encabezadotabla'>";
				$data['html'] .="<td <b>Paciente</b></td>";
				$data['html'] .="<td <b>Cedula</b></td>";
				$data['html'] .="<td <b>Equipo</b></td>";
				$data['html'] .="<td <b>Examen</b></td>";
				$data['html'] .="<td <b>Fecha</b></td>";	
				$data['html'] .="<td <b>Hora Inicial</b></td>";	
				$data['html'] .="<td <b>Hora Final</b></td>";
				$data['html'] .="<td <b>Usuario</b></td>";
				$data['html'] .="<td <b>Asiste</b></td>";
				$data['html'] .="</tr>";
				
				for ($i=0; $row = mysql_fetch_array($err); $i++)
				{
					
					if( $i%2 == 0 )
					{
						$class = "class='fila1'";
					}
					else
					{
						$class = "class='fila2'";
					}
					$data['html'] .="<tr $class><td><font size=2>".utf8_encode($row[0])."</font></td>";
					$data['html'] .="<td><font size=2>".utf8_encode($row['Cedula'])."</font></td>";
					$data['html'] .="<td><font size=2>".utf8_encode($row[1])."-".utf8_encode($row[2])."</font></td>";
					$data['html'] .="<td><font size=2>".utf8_encode($row[3])."-".utf8_encode($row[4])."</font></td>";
					$data['html'] .="<td><font size=2>".utf8_encode($row[5])."</font></td>";
					if(substr($row[6],0,2) > "12")
					{
						$hr1 ="". (string)((integer)substr($row[6],0,2) - 12).":".substr($row[6],2,2). " pm ";
						$data['html'] .="<td align=center><font size=2>".$hr1."</font></td>";
					}
					else
						$data['html'] .="<td align=center><font size=2>".substr($row[6],0,2).":".substr($row[6],2)."</font></td>";
					if(substr($row[7],0,2) > "12")
					{
						$hr1 ="". (string)((integer)substr($row[7],0,2) - 12).":".substr($row[7],2,2). " pm ";
						$data['html'] .="<td align=center><font size=2>".$hr1."</font></td>";
					}
					else
					{
						$data['html'] .="<td align=center><font size=2>".substr($row[7],0,2).":".substr($row[7],2)."</font></td>";
					}
					$data['html'] .="<td><font size=2>".$row['Usuario']."</font></td>";
					if($row['Asistida'] == 'on')
					{
						$asiste="Si";
					}
					else
					{
						$asiste="";
					}
					$data['html'] .= "<td align=center><font size=2>".$asiste."</font></td>";
					$data['html'] .= "</tr>";
				}
			}
			else
			{
				$data['html'] ="<table border=0 align=center>";
				$data['html'] .="<tr class='encabezadotabla'><td align=center colspan=9>No se encontraron registros</td></tr>";
			}
		}
		else
		{
			$data['mensaje'] = "No se pudo realizar la consulta.";
			$data['error'] = 1;
		}
		
		$data['html'] .="</table>";
		
		
		// echo "<br><br><center><a href='busqPacientesEqu.php?empresa=".$empresa."'>Retornar</a></center>";
		// echo "<div align='center'><br /><br /><input type='button' value='Cerrar' style='width:100' onclick='javascript: cerrarVentana();'></div>";
	echo json_encode($data);
	return;
}
?>
<html>
<head>
<title>CONSULTA DE TURNOS X PACIENTE</title>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
			<script type="text/javascript">
			function enviar(empresa)
			{ 
				
				var wced=$("#wced").val();
				var wpac=$("#wpac").val();
				var wper1="";
				if ($("#chkHabilitar").is(':checked')) 
				{ 
					wper1=$("#wper1").val();
				}
				
				$.post("busqPacientesEqu.php",
					{
						accion      :'enviarBusqueda',
						consultaAjax:'',
						empresa     : empresa,
						wced        : wced,
						wpac        :wpac,
						wper1       :wper1
						
					},
					function(data){
						if(data.error == 1)
						{
							alert(data.mensaje);
						}
						else
						{
							$("#div_respuesta").html(data.html);
							
						}
					},
					"json"
				);
			}
	
	function habilitar()
	{ 
		if ($("#chkHabilitar").is(':checked')) //si esta chequeado se habilita la caja de texto
		{ 
			$("#wper1").attr("disabled", false);
			$("#btnwper1").attr("disabled", false);
		}
		else
		{
			$("#wper1").attr("disabled", true);
			$("#btnwper1").attr("disabled", true);
			
		}
	}
	
	window.onload = function()
	{
		
		$("#wper1").attr("disabled", true);
		$("#btnwper1").attr("disabled", true);
		
	}
</script>
</head>
<?php
/**********************************************************************
Modificaciones: 
2013-07-23 (Viviana Rodas): Se agrega codificacion utf8 encode y decode en la consulta y en la parte que se muestran los datos.
2013-05-07 (Viviana Rodas): Se modifica la consulta que lista las citas de un paciente, se agrega Activo = A y en el order by se
agrego hora inicial hi.
2012-10-03 (Viviana Rodas): Se agrega para la busqueda, el calendario para que el usuario no digite la fecha, tambien se agrega
la busqueda por la cedula del paciente, ademas se pone botones de cerrar y un retornar.
2012-10-22 (Viviana Rodas): Se quita el campo fecha final del proceso.
**********************************************************************/
include_once("root/comun.php");
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wbasedato1 = strtolower( $institucion->baseDeDatos );
$wactualiz="2013-04-24";

echo "<body BGCOLOR=''>";
echo "<BODY TEXT='#000066'>";

$wfec=date("Y-m-d");
echo "<form action='busqPacientesEqu.php' method=post>";
session_start();
if(!isset($_SESSION['user']))
	echo "Error Usuario NO Registrado";
else
{		
	
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	//if (!isset($wpac) or !isset($wper1) or !isset($wper2) or !isset($wced))
	encabezado("CONSULTA DE TURNOS POR PACIENTE ",$wactualiz, "".$wbasedato1);
	
	echo "<center><table border=0>";
	echo "<tr><td class='fila1' align=center>Paciente : </td>";
	echo "<td class='fila2' align=center><input type='TEXT' name='wpac' id='wpac' size=50 maxlength=60></td></tr>";
	echo "<tr><td class='fila1' align=center>Cedula : </td>";
	echo "<td class='fila2' align=left><input type='TEXT' name='wced' id='wced' size=20 maxlength=60></td></tr>";
	//echo "<tr><td bgcolor=#cccccc align=center>Fecha Inicial de Proceso</td>";
	echo "<tr><td class='fila1' align=center>Fecha del Proceso</td>";
	echo "<td class='fila2'>";$c=campoFechaDefecto("wper1",$wfec);"";
	echo"&nbsp;Buscar por fecha<input type='checkbox' id='chkHabilitar' name='chkHabilitar' onclick='habilitar();'></td>";
	echo"</tr>";
	// echo "<tr><td bgcolor=#cccccc align=center>Fecha Final de Proceso</td>";
	// echo "<td bgcolor=#cccccc>";$d=campoFechaDefecto("wper2",$wfec);"</td></tr>";
	
	echo "<tr><td class='fila2'  align=center colspan=2><input type='button' id='btn_ir' name='btn_ir' value='IR' onclick='enviar(\"".$empresa."\");'>";
	echo "</td></tr></table><br>";
	
	echo"<div id='div_respuesta'></div>";
	
	echo "<div align='center'><br /><br /><input type='button' value='Cerrar' style='width:100' onclick='javascript: cerrarVentana();'></div>";

	echo "</body>";
	echo "</html>";
	
}
?>
