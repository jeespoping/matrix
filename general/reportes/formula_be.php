<html>

<head>
  <title>FORMULA V1.00</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#5E2F00">
<?php
include_once("conex.php");
  /***************************************************
	*	REPORTE DE HISTORIAS CLINICAS Y SEGUIMIENTOS *
	*			PARA MEDICOS BIOENERGETICOS	V.1.00	 *
	*					CONEX, FREE => OK			 *
	**************************************************/

session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

		// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)
	if(!isset($medico)  or !isset($pac) or !isset($fecha))
	{
		echo "<form action='formula_be.php' method=post>";
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><b><font color=#000066>CLÍNICA MEDICA LAS AMERICAS </font></b></td></tr>";
		echo "<tr><td align=center colspan=3><font color=#000066>TORRE MÉDICA</font></td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#CCCCCC colspan=1><font color=#000066>MEDICO: </font></td>";	
			/* Si el medico no ha sido escogido Buscar a los oftalmologos de la seleccion para 
				construir el drop down*/
			echo "<td bgcolor=#CCCCCC colspan=2><select name='medico'>";
			$query = "SELECT Subcodigo,Descripcion FROM `det_selecciones` WHERE medico = 'energia' AND codigo = '003'  order by Descripcion ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{	
					$row = mysql_fetch_array($err);
					if (($row[0]."-".$row[1]) == $medico)
						echo "<option selected>".$row[0]."-".$row[1]."</option>";
					else
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
			}	// fin del if $num>0
		echo "</select></tr><tr><td bgcolor=#CCCCCC colspan=1><font color=#000066>PACIENTE: </font></td>";	
	
		/* Si el paciente no esta set construir el drop down */
		//if(isset($medico) and isset($pac1)) V1.03
		if(isset($doc1))
		{
			/* Si el medico  ya esta set traer los pacientes a los cuales les ha hecho seguimiento*/
			//$query="select DISTINCT Paciente from oftalmo_000003 where Oftalmologo='".$medico."'and Paciente like '%".$pac1."%' order by Paciente";V1.03
			$query="select Documento,Nombre1,Nombre2,Apellido1,Apellido2 from energia_000001 where  Documento like '%".$doc1."%' or Nombre1 like '%".$doc1."%' or Nombre2 like '%".$doc1."%' or Apellido1 like '%".$doc1."%' or Apellido2 like '%".$doc1."%' order by Nombre1";
			echo "</td><td bgcolor=#CCCCCC colspan=2><select name='pac'>";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{	
					$row = mysql_fetch_array($err);
					$paci=$row["Nombre1"]."-".$row["Nombre2"]."-".$row["Apellido1"]."-".$row["Apellido2"]."-".$row["Documento"];
					if($paci==$pac)
						echo "<option selected>".$paci."</option>";
					else
						echo "<option>".$paci."</option>";
				}
			}	// fin $num>0
			echo "</select></td></tr>";
			if(isset($pac))
			{
				$ini=strpos($pac,"-");
				$doc1=substr($pac,0,$ini);
				echo "</td></tr><input type='hidden' name='doc1' value='".substr($pac,0,$ini)."'>";
			}
			else
				echo "</td></tr><input type='hidden' name='doc1' value='".$doc1."'>";
		}	//fin isset medico
		else 
		{
			echo "</td><td bgcolor=#CCCCCC colspan=2><input type='text' name='doc1'>";
			echo "</td></tr>";
		}
		if(isset($pac))
		{
			/*Si ya se tiene el paciente buscar las fechas de los seguimiento para este y
			 construir el drop down */	
			echo "<tr><td bgcolor=#CCCCCC colspan=1><font color=#000066>Fecha: </font></td>";	
			$ini=strpos($pac,"-");
			$query = "select Fecha from energia_000002 where Paciente='".$pac."' ";
			echo $query;
			echo "<td bgcolor=#CCCCCC colspan=2><select name='fecha'>";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_array($err);
					$fec[$j]=$row[0];
					echo "<option>".$row[0]."</option>";
				}
			}
			$query = "select Fecha from energia_000003 where Paciente='".$pac."' ";//and Oftalmologo='".$medico."' "; V 1.03
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_array($err);
					$fec[$j]=$row[0];
					echo "<option>".$row[0]."</option>";
				}
			}
			echo "</select></td></tr>";
		}
		//echo"</select>
		echo"<tr><td align=center bgcolor=#CCCCCC colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
	}// if de los parametros no estan set
	else
	{
		$ini1=strpos($pac,"-");
		$n1=substr($pac,0,$ini1);
		$ini2=strpos($pac,"-",$ini1+1);
		$n2=substr($pac,$ini1+1,$ini2-$ini1-1);
		$ini3=strpos($pac,"-",$ini2+1);
		$ap1=substr($pac,$ini2+1,$ini3-$ini2-1);
		$ini4=strpos($pac,"-",$ini3+1);
		$ap2=substr($pac,$ini3+1,$ini4-$ini3-1);
		$id=substr($pac,$ini4+1,strlen($pac));
		$pacn=$n1." ".$n2." ".$ap1." ".$ap2;
		//$pacn=$ap2." ".$n1." ".$n2." ".$id;
		echo "<table width=450 heigth=5000  border=0>";
		echo "<tr><td colspan=3><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br></td></tr>";
		echo"<tr><td width=50></td><td><font color='black'>FECHA ".date('Y')."-".date('m')."-".date('d')."<br></font></td><td width=50></td></tr>";
		/*Buscar los seguimientos del paciente*/
		if(!isset($row[0]))
		{
			$query= "select Formulacion from energia_000002 where Paciente ='".$pac."' and Fecha='".$fecha."'";
			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);
			if($num > 0)
			{
				$row=mysql_fetch_array($err);
				$posul=strrpos($row[0],"<BR>");
				$low=strtolower($row[0]);
				
			}
			else
			{
				$query= "select Formulacion from energia_000001 where Documento='".$doc1."' and Fecha_consulta='".$fecha."'";
				$err=mysql_query($query,$conex);
				$num=mysql_num_rows($err);
				if($num > 0)
				{
					$row=mysql_fetch_array($err);
					$posul=strrpos($row[0],"<BR>");
					$low=strtolower($row[0]);
				}
				else
					echo "NO EXISTE FORMULA PARA LOS DATOS INGRESADOS";
			}
		}
		if(isset($posul))
		{
			$pos=0;
			$br=1;
			while($pos<$posul)
			{
				$pos=strpos($low,"<br>",$pos);
				$br++;
				$pos=$pos+4;
			}
			$br=26-$br;
			echo"<tr><td width=50></td><td><font color='black'><b>PACIENTE: ".$pacn."</b><br></td></font><td width=50></td></tr>";
			echo"<tr><td width=50></td><td><font color='black'><b>DOCUMENTO: ".$id."</b><br></td></font><td width=50></td></tr>";
			echo"<tr><td width=40 heigth=2000></td><td><BR><font color='black'>".$row[0]."</font>";
			
			for($i=1;$i<$br;$i++)
				echo"<br>";
			echo "</font></td><td width=40></td></tr>";
		}
		
	}
	include_once("free.php");
}
?>
</body>
</html>