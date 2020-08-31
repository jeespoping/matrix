<HTML>
<HEAD>
<title>REPORTE FACTURA V1.0</title>
</HEAD>
<BODY TEXT="#000066">
<?php
include_once("conex.php");

  /***************************************************
	*	REPORTE DE medicamentos y material grabado por la pda *
	**************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	if(!isset($hist))
	{
		echo "<form action='reporte_por_historia.php' method=post>";
		echo "<center><table border=0 width=300>";
		echo "<tr><td align=center colspan=2><b>CLÍNICA MEDICA LAS AMERICAS </b></td></tr>";
		echo "<tr><td align=center colspan=2>SEGUIMIENTO DE FACTURAS Y AUDITORIA</td></tr>";
		echo"<tr><td align=center bgcolor=#cccccc >HISTORIA N°:</TD><td align=center bgcolor=#cccccc ><input type='input' name='hist'></td></tr>";
		echo"<tr><td align=center bgcolor=#cccccc >INGRESO N°:</TD><td align=center bgcolor=#cccccc ><input type='input' name='ingr'></td></tr>";
		echo"<tr><td align=center bgcolor=#cccccc colspan=2><input type='submit' value='ACEPTAR'></td></tr></form>";
	}
	else
	{
		$query = "SELECT * FROM calidad_000004 where Historia='".$hist."' and Ingreso='".$ingr."'";
		$err1 = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err1);
		if($num1>0)
		{
			$row1 = mysql_fetch_array($err1);
			echo "<CENTER><table align=center border=1 width=680 >";
			echo "<tr><td bgcolor=#006699 colspan=2><font face='Arial' size='2'COLOR=#FFFFFF><b>PREFACTURA ".$row1["Codigo"]."  ".$row1["Fecha_data"]." </td></tr>";
			
			echo "<tr><td ><font face='Arial' size='2' color='#004477'><b>Paciente: </b><font face='Arial' size='2' color='#00000'>".$row1["Paciente"]."</td>";
			echo "<td><font face='Arial' size='2' color='#004477'><B>Historia</b> <font face='Arial' size='2' color='#00000'>".$row1["Historia"]." - ".$row1["Ingreso"]."</td></tr>";
			
			echo "<tr><td ><font face='Arial' size='2' color='#004477'><B>Responsable:</b> <font face='Arial' size='2' color='#00000'>".$row1["Responsable"]."</td>";
			echo "<td ><font face='Arial' size='2' color='#004477'><b>Valor: </b><font face='Arial' size='2' color='#00000'>".$row1["Valor"]."</td></tr>";
			
			echo "<tr><td><font face='Arial' size='2' color='#004477'><B>Estado:</b> <font face='Arial' size='2' color='#00000'>".$row1["Estado"]."</td>";
			echo "<td ><font face='Arial' size='2' color='#004477'><b>Unidad: </b><font face='Arial' size='2' color='#00000'>".$row1["Unidad_audito"]."</td></tr>";
			
			echo "<tr><td colspan=2><font face='Arial' size='2' color='#004477'><B>Auditor:</b> <font face='Arial' size='2' color='#00000'>".$row1["Auditor"]."</td></table>";
			$query2 = "SELECT * FROM calidad_000005 where Historia='".$hist."' and Ingreso='".$ingr."'";
			$err2 = mysql_query($query2,$conex);
			$num2 = mysql_num_rows($err2);
			if($num2>0)
			{
				echo "<CENTER><table align=center border=1 width=680 >";
					/*Existe inconsistencias para la prefactura*/
				for ($j=0;$j<$num2;$j++)
				{
					$row2 = mysql_fetch_array($err2);
					if(($j % 2)==0)
					{
						$color1='#558888';
						$color2='#335555';
					}
					else
					{
						
						$color1='#999999';
						$color2='#666666';
					}
					
					echo "<tr><td bgcolor=".$color1." colspan=3><font face='Arial' size='2'COLOR=#FFFFFF><b>INFORME INCONSISTENCIAS ".$row2["Codigo"]." PREFACTURA </td></tr>";
					
					echo "<tr><td ><font face='Arial' size='2' color='".$color2."'><B>Recibida:</b> <font face='Arial' size='2' color='#00000'>".$row2["Recibido"]."</td>";						
					echo "<td ><font face='Arial' size='2' color='".$color2."'><B>Auditada:</b> <font face='Arial' size='2' color='#00000'>".$row2["Audito"]."</td>";
					echo "<td ><font face='Arial' size='2' color='".$color2."'><B>Fecha Inc.:</b> <font face='Arial' size='2' color='#00000'>".$row2["Fecha_inc"]."</td></tr>";
					
					echo "<tr><td colspan='2'><font face='Arial' size='2' color='".$color2."'><B>Tipo Inconsistencia:</b> <font face='Arial' size='2' color='#00000'>".$row2["Tipo_inc"]."</td>";
					echo "<td><font face='Arial' size='2' color='".$color2."'><B>Valor Unitario:</b> <font face='Arial' size='2' color='#00000'>".$row2["Valor_unitario"]."</td></tr>";
					
					echo "<tr><td><font face='Arial' size='2' color='".$color2."'><B>Cantidad:</b> <font face='Arial' size='2' color='#00000'>".$row2["Cantidad"]."</td>";
					echo "<td><font face='Arial' size='2' color='".$color2."'><B>Tipo Nota:</b> <font face='Arial' size='2' color='#00000'>".$row2["Tipo_nota"]."</td>";
					echo "<td><font face='Arial' size='2' color='".$color2."'><B>Valor Nota:</b> <font face='Arial' size='2' color='#00000'>".$row2["Valor_nota"]."</td></tr>";

					
					echo "<tr><td colspan=3><font face='Arial' size='2' color='".$color2."'><B>Unidad Responsable:</b> <font face='Arial' size='2' color='#00000'>".$row2["Unidad_inc"]."</td></tr>";
					
					echo "<tr><td colspan=3><font face='Arial' size='2' color='".$color2."'><B>Responsable:</b> <font face='Arial' size='2' color='#00000'>".$row2["Responsable_inc"]."</td></tr>";
					
					echo "<tr><td colspan=3><font face='Arial' size='2' color='".$color2."'><B>Causa:</b> <font face='Arial' size='2' color='#00000'>".$row2["Causa"]."</td>";
					
					echo "<tr><td width=45% align=left  bgcolor='".$color1."' rowspan='2'><font size='2' face='arial'color=#FFFFFF><b>Observaciones</b>";
					echo "<fieldset  style='background-color: #FFFFFF' cols=40><font size='2' face='arial'color=#000000>".$row2["Observaciones"]."</fieldset></TD>";
					echo "<td ><font face='Arial' size='2' color='".$color2."'><B>Concilio:</b> <font face='Arial' size='2' color='#00000'>".$row2["Concilio"]."</td>";
					echo "<td ><font face='Arial' size='2' color='".$color2."'><B>Devolvio:</b> <font face='Arial' size='2' color='#00000'>".$row2["Devolvio"]."</td></tr>";
					
					echo "<tr><td colspan=3><font face='Arial' size='2' color='".$color2."'><B>Unidad Devolvio:</b> <font face='Arial' size='2' color='#00000'>".$row2["Unidad_devolvio"]."</td></tr>";
					
					echo "<tr><td colspan=3><font face='Arial' size='2' color='".$color2."'><B>Unidad Audito:</b> <font face='Arial' size='2' color='#00000'>".$row2["Unidad_audito"]."</td></tr>";
					
					echo "<tr><td colspan=3><font face='Arial' size='2' color='".$color2."'><B>Auditor:</b> <font face='Arial' size='2' color='#00000'>".$row2["Auditor_a"]."</td></tr>";
				}
				echo "</table>";
			}
			$query = "SELECT * FROM calidad_000001 where Historia='".$hist."' and Ingreso='".$ingr."' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_array($err);
					/*IMPRESION EN PANTALLA DE LA INFO GENERAL DE LA FACTURA*/
					echo "<CENTER><table align=center border=1 width=680 >";
					echo "<tr><td bgcolor=#006699 colspan=3><font face='Arial' size='2'COLOR=#FFFFFF><b>INFORMACIÓN GENERAL FACTURA  ".$row[4]." </td></tr>";
					
					echo "<tr><td width=40%><font face='Arial' size='2' color='#004477'><B>Año / Mes:</b> <font face='Arial' size='2' color='#00000'>".$row[5]."</td>";
					echo "<td><font face='Arial' size='2' color='#004477'><b>Fecha: </b><font face='Arial' size='2' color='#00000'>".$row[6]."</td>";
					echo "<td><font face='Arial' size='2' color='#004477'><b>Fecha Vence: </b><font face='Arial' size='2' color='#00000'>".$row[7]."</td></tr>";
					
					echo "<tr><td colspan=3><font face='Arial' size='2' color='#004477'><B>Facturador:</b> <font face='Arial' size='2' color='#00000'>".$row[8]."</td>";
					
					if(!isset($row1[4]))
					{
						echo "<tr><td colspan=2><font face='Arial' size='2' color='#004477'><b>Paciente: </b><font face='Arial' size='2' color='#00000'>".$row[11]."</td>";
						echo "<td><font face='Arial' size='2' color='#004477'><B>Historia</b> <font face='Arial' size='2' color='#00000'>".$row[9]." - ".$row[10]."</td></tr>";
					}
					
					echo "<tr><td colspan=2><font face='Arial' size='2' color='#004477'><B>Responsable:</b> <font face='Arial' size='2' color='#00000'>".$row[12]."</td>";
					echo "<td ><font face='Arial' size='2' color='#004477'><b>Valor: </b><font face='Arial' size='2' color='#00000'>".$row[13]."</td></tr>";
					
					echo "<tr><td bgcolor=#558888 colspan=3><font face='Arial' size='2'COLOR=#FFFFFF><b>AUDITORIA ".$row[1]." </td></tr>";
					
					echo "<tr><td><font face='Arial' size='2' color='#335555'><B>Estado:</b> <font face='Arial' size='2' color='#00000'>".$row[14]."</td>";
					echo "<td  colspan=2><font face='Arial' size='2' color='#335555'><b>Unidad: </b><font face='Arial' size='2' color='#00000'>".$row[15]."</td></tr>";
					
					echo "<tr><td colspan=3><font face='Arial' size='2' color='#335555'><B>Auditor:</b> <font face='Arial' size='2' color='#00000'>".$row[16]."</td></tr>";
					
					/*BUSQUEDA DE INCONSISTENCIAS PARA LA FACTURA*/
					$query2 = "SELECT * FROM calidad_000003 where factura='".$row[4]."'";
					$err2 = mysql_query($query2,$conex);
					$num2 = mysql_num_rows($err2);
					if($num2>0)
					{
						for ($P=0;$P<$num2;$P++)
						{
							if(($P % 2)==0)
							{
								$color1='#999999';
								$color2='#666666';
							}
							else
							{
								$color1='#558888';
								$color2='#335555';
							}
							echo "<CENTER><table align=center border=1 width=680 >";
							$row2 = mysql_fetch_array($err2);
							echo "<tr><td bgcolor=".$color1." colspan=3><font face='Arial' size='2'COLOR=#FFFFFF><b>INFORME INCONSISTENCIAS ".$row2["Codigo"]." </td></tr>";
							
							echo "<tr><td ><font face='Arial' size='2' color='".$color2."'><B>Recibida:</b> <font face='Arial' size='2' color='#00000'>".$row2["Recibido"]."</td>";						
							echo "<td ><font face='Arial' size='2' color='".$color2."'><B>Auditada:</b> <font face='Arial' size='2' color='#00000'>".$row2["Audito"]."</td>";
							echo "<td ><font face='Arial' size='2' color='".$color2."'><B>Fecha Inc.:</b> <font face='Arial' size='2' color='#00000'>".$row2["Fecha_inconsistencia"]."</td></tr>";
							
							echo "<tr><td colspan='2'><font face='Arial' size='2' color='".$color2."'><B>Tipo Inconsistencia:</b> <font face='Arial' size='2' color='#00000'>".$row2["Tipo_inc"]."</td>";
							echo "<td><font face='Arial' size='2' color='".$color2."'><B>Valor Unitario:</b> <font face='Arial' size='2' color='#00000'>".$row2["Valor_unitario"]."</td></tr>";
							
							echo "<tr><td><font face='Arial' size='2' color='".$color2."'><B>Cantidad:</b> <font face='Arial' size='2' color='#00000'>".$row2["Cantidad"]."</td>";
							echo "<td><font face='Arial' size='2' color='".$color2."'><B>Tipo Nota:</b> <font face='Arial' size='2' color='#00000'>".$row2["Tipo_nota"]."</td>";
							echo "<td><font face='Arial' size='2' color='".$color2."'><B>Valor Nota:</b> <font face='Arial' size='2' color='#00000'>".$row2["Valor_nota"]."</td></tr>";
							
							echo "<tr><td colspan=3><font face='Arial' size='2' color='".$color2."'><B>Unidad Responsable:</b> <font face='Arial' size='2' color='#00000'>".$row2["Unidad_inc"]."</td></tr>";
							
							echo "<tr><td colspan=3><font face='Arial' size='2' color='".$color2."'><B>Responsable:</b> <font face='Arial' size='2' color='#00000'>".$row2["Responsable_inc"]."</td></tr>";
							
							echo "<tr><td colspan=3><font face='Arial' size='2' color='".$color2."'><B>Causa:</b> <font face='Arial' size='2' color='#00000'>".$row2["Causa"]."</td>";
							
							echo "<tr><td width=45% align=left  bgcolor='".$color1."' rowspan='2'><font size='2' face='arial'color=#FFFFFF><b>Observaciones</b>";
							echo "<fieldset  style='background-color: #FFFFFF' cols=40><font size='2' face='arial'color=#000000>".$row2["Observaciones"]."</fieldset></TD>";
							echo "<td colspan=2><font face='Arial' size='2' color='".$color2."'><B>Estado:</b> <font face='Arial' size='2' color='#00000'>".$row2["Estado_factura"]."</td>";
							
							echo "<tr><td ><font face='Arial' size='2' color='".$color2."'><B>Concilio:</b> <font face='Arial' size='2' color='#00000'>".$row2["Concilio"]."</td>";
							echo "<td ><font face='Arial' size='2' color='".$color2."'><B>Devolvio:</b> <font face='Arial' size='2' color='#00000'>".$row2["Devolvio"]."</td></tr>";
							
							echo "<tr><td><font face='Arial' size='2' color='".$color2."'><B>Unidad Audito:</b> <font face='Arial' size='2' color='#00000'>".$row2["Unidad_audito"]."</td>";
							echo "<td colspan=2><font face='Arial' size='2' color='".$color2."'><B>Unidad Devolvio:</b> <font face='Arial' size='2' color='#00000'>".$row2["Unidad_devolvio"]."</td></tr>";
							
							echo "<tr><td colspan=3><font face='Arial' size='2' color='".$color2."'><B>Auditor:</b> <font face='Arial' size='2' color='#00000'>".$row2["Auditor_a"]."</td></tr>";
							
						}
					}
				}
			}
		}
		else
			echo "ASEGURESE DE QUE LA FACTURA HALLA SIDO REGISTRADA";
	}
}
include_once("free.php");
?>
</body>
</html>