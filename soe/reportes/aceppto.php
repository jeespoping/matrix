<html>
<head>
  <title>ACEPTACION DE PRESUPUESTO</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
  /***********************************
	*	REPORTE DE ACEPTACION		 *
	*    	 PRESUPUESTO  			 *
	***********************************/

session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

		// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente,  medico y coigo )
	if(!isset($medico)  or !isset($pac) or !isset($codigo)  )
	{
		echo "<form action='' method='post'>";
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><b><font color=#000066>PROMOTORA MEDICA LAS AMERICAS </font></b></td></tr>";
		echo "<tr><td align=center colspan=3><font color=#000066>UNIDAD ODONTOLOGICA</font></td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>ODONTOLOGO: </font></td>";	
			/* Si el ODONTOLOGO no ha sido escogido Buscar a los odontologos de la seleccion para 
				construir el drop down*/
			echo "<td bgcolor=#cccccc colspan=2><select name='medico'>";
			$query = "SELECT Subcodigo,Descripcion FROM `det_selecciones` WHERE medico = 'soe1' AND codigo = '008'  order by Subcodigo ";
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
	
		echo "</select></tr><tr><td bgcolor=#cccccc colspan=1><font color=#000066>PACIENTE: </font></td>";	
	
		/* Si el paciente no esta set construir el drop down */
		//if(isset($medico) and isset($pac1)) V1.03
		if(isset($pac1))
		{
			/* Si el medico  ya esta set traer los pacientes a los cuales les ha hecho seguimiento*/
			//$query="select DISTINCT Identificacion from soe1_000004 where  Identificacion like '%".$pac1."%' order by Identificacion";V1.03
			$query="select DISTINCT Identificacion from soe1_000008 where Identificacion like '%".$pac1."%' and Odontologo='$medico' order by Identificacion";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "</td><td bgcolor=#cccccc colspan=2><select name='pac'>";
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{	
					$row = mysql_fetch_array($err);
					if($row[0]==$pac)
						echo "<option selected>".$row[0]."</option>";
					else
						echo "<option>".$row[0]."</option>";
				}
			}	// fin $num>0
		echo "</select></td></tr>";
		echo "</td></tr><input type='hidden' name='pac1' value='".$pac1."'>";
		}	//fin isset medico
		else 
		{
			echo "</td><td bgcolor=#cccccc colspan=2><input type='text' name='pac1'>";
			echo "</td></tr>";
		}
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>CODIGO: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=2><select name='codigo'>";

		if(isset($pac))
		{
			/*Si ya se tiene el paciente buscar las fechas de los seguimiento para este y
			 construir el drop down */	
			$query = "select DISTINCT Codigo  from soe1_000008  where Identificacion='".$pac."' and Odontologo='$medico' order by Codigo";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=1;$j<=$num;$j++)
				{	
					$row = mysql_fetch_array($err);
					$fec[$j]=$row[0];
					echo "<option>".$row[0]."</option>";
				}
				
			}
			
		}
		//echo"</select>
		echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
	}// if de los parametros no estan set
	else
	/***************************************************************
		  APARTIR DE AQUI EMPIEZA LA IMPRESION DE LA HISTORIA		*/
	{
		$ini1=strpos($medico,"-");
		$nomed=substr($medico,$ini1+1);
		
		$exp=explode("-",$pac);
		
		$query="select Fecha from soe1_000008 where Identificacion like '%".$pac."%' and Odontologo='$medico' and Codigo='".$codigo."'";
			$err = mysql_query($query,$conex);
			$fec = mysql_fetch_row($err);
				
		echo "<table align=center border=1 width=725 ><tr><td rowspan=3 align='center' colspan='0'>";
		echo "<img SRC='\MATRIX\images\medical\SOE\SOE1.JPG' width='242' height='133'></td>";
		echo "<td align=center colspan='4' ><font size=4 color='#000080' face='arial'><B>CL�NICA LAS AM�RICAS</b></font></td>";
		echo "</tr><tr><td  align=center colspan='4'><font size=3 color='#000080' face='arial'><B>TORRE MEDICA</b></font>";
		echo "</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>".$nomed."";
		echo "<br> ODONTOLOGO<BR>";
		echo "</tr></table>";
		/*DATOS GENERALES ENCONTRADOS EN LA TABLA PACIENTE*/
		echo "<table align=center border=1 width=725 BGcolor='#99FFFF'>";
		echo "<tr><td nowrap><font size=3 face='arial' ><B>PACIENTE: </b>".$exp[1]." ".$exp[2]." ".$exp[3]." ".$exp[4]."</td>"; 
		echo "<td><font size=3  face='arial'><b>ODONTOLOGO:</b> ".$nomed."</td></tr>";
		echo "<tr><td><font size=3  face='arial'><b>FECHA:</b> ".$fec[0]." </td>";
		echo "<td><font size=3  face='arial'><b>CODIGO:</b> ".$codigo." </td></tr>";
		echo "</table>";
		
		$query="select Valor_presupuesto, Cuota_inicial, Num_cuotas, Periodo_cuotas, Valor_cuotas 
			from soe1_000012 
			where Identificacion like '%".$pac."%' and Odontologo='$medico' and Codigo='".$codigo."'";
			$err = mysql_query($query,$conex);
			//echo $query."<br>".mysql_errno().'='.mysql_error();
			$dat = mysql_fetch_row($err);
			
		$exp1=explode("-",$dat[3]);	
			
		echo "<table align=center border=1 width=725 >";
		echo "<tr BGcolor='#99CCFF'><td align=center colspan=2><font size=3 face='arial' ><B>VALOR DEL PRESUPUESTO</b></td>"; 
		echo "<td align=center><font size=3 face='arial' >$".number_format($dat[0],0,'',".")."</td></tr>"; 
		echo "<tr BGcolor='#DDDDDD'><td align=center colspan=2><font size=3 face='arial' ><B>CUOTA INICIAL</b></td>"; 
		echo "<td align=center><font size=3 face='arial' >$".number_format($dat[1],0,'',".")."</td></tr>"; 
		echo "<tr BGcolor='#99CCFF'><td align=center colspan=2><font size=3 face='arial' ><B>NUMERO DE CUOTAS</b></td>"; 
		echo "<td align=center><font size=3 face='arial' >".$dat[2]."</td></tr>"; 
		echo "<tr BGcolor='#DDDDDD'><td align=center colspan=2><font size=3 face='arial' ><B>PERIODO DE LAS CUOTAS</b></td>"; 
		echo "<td align=center><font size=3 face='arial' >".$exp1[1]."</td></tr>"; 
		echo "<tr BGcolor='#99CCFF'><td align=center colspan=2><font size=3 face='arial' ><B>VALOR DE CADA CUOTA</b></td>"; 
		echo "<td align=center><font size=3 face='arial' >$".number_format($dat[4],0,'',".")."</td></tr>";  
		
		
								
		echo "</table>";
				
			} //si num y es mayor que cero es decir si hay historias
		}// el for que recorre las fechas del arreglo fec	
	// todos los datos estan set
	include_once("free.php");
?>