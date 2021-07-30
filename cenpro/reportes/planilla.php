<html>
<head>
  	<title>MATRIX PLANILLA DE INVENTARIO</title>
  	
<style type="text/css">
table.sample {
	border-width: 1px;
	border-style: solid;
	border-color: gray;
	border-collapse: separate;
}
table.sample td {
	border-width: 1px;
	border-style: solid;
	border-color: gray;
}

</style>
  	
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>PLANILLA DE INVENTARIO CENTRAL DE MEZCLAS</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b>planilla.php Ver. 1.00</b></font></tr></td></table></br>
</center> 
<?php
/********************************************************************************************************************************
 * 
 * Actualización: 	2021-07-08 - sebastian.nevado: se agrega en las validaciones S/N que se permitan las letras en minúscula.
 * 
 ********************************************************************************************************************************/

include_once("conex.php");
include_once("root/comun.php");
// se convierte en la variable empresa ya que $empresa=cenpro
$empresa = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cenmez" );
$bdMovhos  = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	

	


	echo "<form name='planilla' action='planilla.php?wemp_pmla=".$wemp_pmla."' method=post>";
	//echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";

	if (!isset($pintar))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>IMPRIMIR SALDOS (S/N)</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='pintar' value='S' size=1 ></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>ALMACENAR SALDOS (S/N)</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='almacenar' value='N' size=1 ></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>REEMPLAZAR SI EXISTE (S/N)</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='reemplazar' value='N' size=1 ></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		echo "<table border=0 align=center>";

		echo "<tr><td bgcolor=#dddddd><font face='tahoma'><b>FECHA : </b>".date('Y-m-d')."</td></tr>";
		echo "<tr><td bgcolor=#dddddd><font face='tahoma'><b>HORA : </b>".date('h:i:s')."</td></tr>";


		if ($almacenar=='S' || $almacenar=='s')
		{
			$query = "SELECT * ";
			$query .=" from ".$empresa."_000017 ";
			$query .= " where  Invfec = '".date('Y-m-d')."' ";
			$err = mysql_query($query,$conex);

			$num = mysql_num_rows($err);
			if($num>0)
			{
				if($reemplazar=='S' || $reemplazar=='s')
				{
					$query = "delete  from ".$empresa."_000017 ";
					$query .= "  where Invfec = '".date('Y-m-d')."' ";
					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				}
				else
				{
						 ?>	    
				 				<script>
				 				alert('YA EXISTE UN INVENTARIO PARA LA FECHA');
				 				</script>
			   			<?php 

			   			$almacenar='N';
				}
			}
		}


		//Buscamos los insumos primero
		$query = "SELECT Apppre, Artcom, ROUND((Appexi/Appcnv),2), Artuni, Unides";
		$query .=" from ".$empresa."_000009, ".$bdMovhos."_000026,".$bdMovhos."_000027 ";
		$query .= " where  Appest = 'on' ";
		$query .= "   and  Apppre = Artcod ";
		$query .= "   and  Artuni = Unicod ";
		$query .= "   ORDER BY 2 ";

		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);

		echo "</table></br><table align=center class='sample' cellspacing=0>";
		if ($pintar=='S' || $pintar=='s')
		{
			$colspan='6';
		}
		else
		{
			$colspan='5';
		}
		echo "<tr><td align=center bgcolor=#999999 colspan='".$colspan."'><font face='tahoma' size=2><b>INSUMOS</b></font></td>";
		echo "<tr><td align=center bgcolor=#999999><font face='tahoma' size=1><b>ARTICULO</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>UNIDAD</b></font></td>";
		if ($pintar=='S' || $pintar=='s')
		{
			echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>CANTIDAD</b></font></td>";
		}
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>CONTEO 1</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>CONTEO 2</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>CONTEO 3</b></font></td>";

		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);

			if(is_int($i/2))
			{
				$color='#FFFFFF';
			}
			else
			{
				$color="#dddddd";
			}

			echo "<tr>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[0]."-".$row[1]."</font></td>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[3]."-".$row[4]."</font></td>";
			if ($pintar=='S' || $pintar=='s')
			{
				echo "<td bgcolor=".$color." align='right'><font face='tahoma' size=2>".number_format($row[2],2,'.',',')."</font></td>";
			}
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>&nbsp;</font></td>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>&nbsp;</font></td>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>&nbsp;</font></td>";
			echo "</tr>";

			if ($almacenar=='S' || $almacenar=='s')
			{
				$query = "insert ".$empresa."_000017 (medico,fecha_data,hora_data, Invfec, Invcod, Invcan, Invpro, Seguridad) ";
				$query .= "values ('".$empresa."','".date('Y-m-d')."','".date('H:i:s')."','".date('Y-m-d')."','".$row[0]."',".$row[2].",'off','C-".$empresa."')";
				
				$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			}

		}

		//Buscamos los producto
		$query = "SELECT Karcod, Artcom, Karexi, Artuni, Unides ";
		$query .=" from ".$empresa."_000002, ".$empresa."_000005, ".$bdMovhos."_000027, ".$empresa."_000001 ";
		$query .= " where  Karexi > 0 " ;
		$query .= "   and  Artest = 'on' ";
		$query .= "   and  Karcod = Artcod ";
		$query .= "   and  Artuni = Unicod ";
		$query .= "   and  Arttip= Tipcod ";
		$query .= "   and  Tippro = 'on' ";
		$query .= "   ORDER BY 2 ";

		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);

		echo "</table></br><table class='sample' align=center cellspacing=0> ";
		echo "<tr><td align=center bgcolor=#999999 colspan='".$colspan."'><font face='tahoma' size=2><b>PRODUCTOS</b></font></td>";
		echo "<tr><td align=center bgcolor=#999999><font face='tahoma' size=1><b>ARTICULO</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>UNIDAD</b></font></td>";
		if ($pintar=='S' || $pintar=='s')
		{
			echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>CANTIDAD</b></font></td>";
		}
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>CONTEO 1</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>CONTEO 2</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>CONTEO 3</b></font></td>";

		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);

			if(is_int($i/2))
			{
				$color='#FFFFFF';
			}
			else
			{
				$color="#dddddd";
			}

			echo "<tr>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[0]."-".$row[1]."</font></td>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[3]."-".$row[4]."</font></td>";
			if ($pintar=='S' || $pintar=='s')
			{
				echo "<td bgcolor=".$color." align='right'><font face='tahoma' size=2>".number_format($row[2],2,'.',',')."</font></td>";
			}
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>&nbsp;</font></td>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>&nbsp;</font></td>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>&nbsp;</font></td>";
			echo "</tr>";

			if ($almacenar=='S' || $almacenar=='s')
			{
				$query = "insert ".$empresa."_000017 (medico,fecha_data,hora_data, Invfec, Invcod, Invcan, Invpro, Seguridad) ";
				$query .= "values ('".$empresa."','".date('Y-m-d')."','".date('H:i:s')."','".date('Y-m-d')."','".$row[0]."',".$row[2].",'on','C-".$empresa."')";
				
				$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			}
		}
	}

}
?>
</body>
</html>