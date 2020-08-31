<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1");







if (isset($accion) and $accion == 'enviarBusqueda')
{  
	
	$data = array('error'=>0,'mensaje'=>'','html'=>'');
		
		$query = "select Nom_pac, cod_equ, cod_exa, fecha, hi, hf, usuario , Asistida ,Cedula
		from ".$empresa."_000009 ";
		$query .= "       where Nom_pac like '%".utf8_decode($wpac)."%'";
		$query .= "       and cedula like '%".utf8_decode($wced)."%'";  //agregado
		
		if ($wper1 != "")
		{
			$query.="and fecha >= '".$wper1."' ";
		}
		$query .= " and Activo = 'A'";
	    $query .= " Order by fecha,hi ";
		
		//$query .= "  where fecha between '".$wper1."' and '".$wper2."' ";
		//$query .= "  where fecha = '".$wper1."' ";
		
		if ($err = mysql_query($query,$conex)) 
		{	
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$data['html'] = "<table border=0 align=center>";
				$data['html'] .= "<tr class='encabezadotabla'><td align=center colspan=9>CONSULTA DE TURNOS X PACIENTE</td></tr>";
				$data['html'] .= "<tr class='encabezadotabla'>";
				$data['html'] .= "<td><b>Paciente</b></td>";
				$data['html'] .= "<td><b>Cedula</b></td>";
				$data['html'] .= "<td><b>Equipo</b></td>";
				$data['html'] .= "<td><b>Examen</b></td>";
				$data['html'] .= "<td><b>Fecha</b></td>";
				$data['html'] .= "<td><b>Hora Inicial</b></td>";
				$data['html'] .= "<td><b>Hora Final</b></td>";
				$data['html'] .= "<td><b>Usuario</b></td>";
				$data['html'] .= "<td><b>Asiste</b></td>";
				$data['html'] .= "</tr>";
				
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
					$query = "select codigo,descripcion from ".$empresa."_000010 where codigo='".$row[1]."' group by 1";
					$err1 = mysql_query($query,$conex);
					$row1 = mysql_fetch_array($err1);
					$query = "select codigo,descripcion from ".$empresa."_000011 where codigo='".$row[2]."' group by 1";
					$err2 = mysql_query($query,$conex);
					$row2 = mysql_fetch_array($err2);
					$data['html'] .= "<tr $class ><td><font size=2>".utf8_encode($row[0])."</td>";
					$data['html'] .= "<td>".utf8_encode($row['Cedula'])."</td>";
					$data['html'] .= "<td>".utf8_encode($row[1])."-".utf8_encode($row1[1])."</td>";
					$data['html'] .= "<td><font size=2>".utf8_encode($row[2])."-".utf8_encode($row2[1])."</td>";
					$data['html'] .= "<td><font size=2>".utf8_encode($row[3])."</td>";
					if(substr($row[4],0,2) > "12")
					{
						$hr1 ="". (string)((integer)substr($row[4],0,2) - 12).":".substr($row[4],2,2). " pm ";
						$data['html'] .= "<td align=center>".$hr1."</td>";
					}
					else
						$data['html'] .= "<td align=center>".substr($row[4],0,2).":".substr($row[4],2)."</td>";
					if(substr($row[5],0,2) > "12")
					{
						$hr1 ="". (string)((integer)substr($row[5],0,2) - 12).":".substr($row[5],2,2). " pm ";
						$data['html'] .= "<td align=center>".$hr1."</td>";
					}
					else
					{
						$data['html'] .= "<td align=center>".substr($row[5],0,2).":".substr($row[5],2)."</td>";
					}
					$data['html'] .= "<td>".$row[6]."</td>";
					if($row['Asistida'] == 'on')
					{
						$asiste="Si";
					}
					else
					{
						$asiste="";
					}
					$data['html'] .= "<td align=center>".$asiste."</td>";
					$data['html'] .="</tr>";
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
			$data['mensaje'] = "No se pudo realizar la consulta. ";
			$data['error'] = 1;
		}
	
		$data['html'] .= "</table>";
		
		// echo "<br><br><center><a href='busqPacientesMed.php?empresa=".$empresa."'>Retornar</a></center>";
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
				
				$.post("busqPacientesMed.php",
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
2012-09-20 (Viviana Rodas): Se agrega para la busqueda, el calendario para que el usuario no digite la fecha, tambien se agrega
la busqueda por la cedula del paciente ademas botones de cerrar.
**********************************************************************/
include_once("root/comun.php");
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wbasedato1 = strtolower( $institucion->baseDeDatos );
$wactualiz="2013-04-24";

echo"<body BGCOLOR=''>";
echo"<BODY TEXT='#000066'>";


$wfec=date("Y-m-d");
echo "<form action='busqPacientesMed.php' method=post>";
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
	echo"Buscar por fecha<input type='checkbox' id='chkHabilitar' name='chkHabilitar' onclick='habilitar();'></td>";
	echo"</tr>";
	// echo "<tr><td bgcolor=#cccccc align=center>Fecha Final de Proceso</td>";
	// echo "<td bgcolor=#cccccc>";$d=campoFechaDefecto("wper2",$wfec);"</td></tr>";
	
	echo "<tr><td class='fila2' align=center colspan=2><input type='button' id='btn_ir' name='btn_ir' value='IR' onclick='enviar(\"".$empresa."\");'>";
	echo "</td></tr></table><br>";
	
	echo"<div id='div_respuesta'></div>";
	
	echo "<div align='center'><br /><br /><input type='button' value='Cerrar' style='width:100' onclick='javascript: cerrarVentana();'></div>";
	
	echo "</body>";
	echo "</html>";
	
}
?>
