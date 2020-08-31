<html>

<head>
  <title>MEDICAMENTOS</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000000"><?php
include_once("conex.php");
  /**************************************************
   * 			IMPRESION DE						*
   *       FORMULARIO MEDICAMENTOS OFTALMOLOGIA     *
   *				CONEX, FREE => OK               *
   **************************************************/

session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

		// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)
	if(!isset($medico)  or !isset($pac) or !isset($fecha) )
	{
		echo "<form action='000005_of01.php' method=post>";
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><font color=#000066><b>CLÍNICA MEDICA LAS AMERICAS </b></td></tr>";
		echo "<tr><td align=center colspan=3><font color=#000066>TORRE MÉDICA</td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>MEDICO: </td>";	
			/* Si el medico no ha sido escogido Buscar a los oftalmologos de la seleccion para 
				construir el drop down*/
			echo "<td bgcolor=#cccccc colspan=2><select name='medico'>";
			$query = "SELECT Subcodigo,Descripcion FROM `det_selecciones` WHERE medico = 'oftalmo' AND codigo = '002'  order by Descripcion ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{	
					$row = mysql_fetch_array($err);
					if (($row[0]."-".$row[1])==$medico)
						echo "<option selected>".$row[0]."-".$row[1]."</option>";
					else
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
			}	// fin del if $num>0
		echo "</select></tr><tr><td bgcolor=#cccccc colspan=1><font color=#000066>PACIENTE: </td>";	

		/* Si el paciente no esta set construir el drop down */
		
		
		if(isset($pac1))
		{
			echo "</td><td bgcolor=#cccccc colspan=2><select name='pac'>";
			/* Si el medico  ya esta set traer los pacientes a los cuales les ha hecho seguimiento*/
			$query="select Paciente from oftalmo_000005 where Oftalmologo='".$medico."' and Paciente like '%".$pac1."%' order by Paciente";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
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
			echo "</select><input type='hidden' name='pac1' value='".$pac1."'>";
			
		}	//fin isset medico
		else
			echo "</td><td bgcolor=#cccccc colspan=2><input type='text' name='pac1'>";
		echo "</td></tr><tr><td bgcolor=#cccccc colspan=1><font color=#000066>FECHA: </td>";	
		echo "</td><td bgcolor=#cccccc colspan=2><select name='fecha'>";

		if(isset($pac))
		{
			/*Si ya se tiene el paciente buscar las fechas de los seguimiento para este y
			 construir el drop down */	
			$query = "select Fecha  from oftalmo_000005 where Paciente='".$pac."' and Oftalmologo='".$medico."' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<=$num;$j++)
				{	
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."</option>";
				}
			}
		}
		echo"</select></td></tr>";
		echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
	}// if de los parametros no estan set
	else
	/******************************** 
	  * TODOS LOS PARAMETROS ESTAN SET *
	  ********************************/
	{
		$ini1=strpos($pac,"-");
		$ini2=strpos($pac,"-",$ini1+1);
		$ini3=strpos($pac,"-",$ini2+1);
		$ini4=strpos($pac,"-",$ini3+1);
		$ini5=strpos($pac,"-",$ini4+1);
		$pacn="<b>".substr($pac,$ini1+1,$ini2-$ini1-1)." ".substr($pac,$ini2+1,$ini3-$ini2-1)." ".substr($pac,$ini3+1,$ini4-$ini3-1)." ".substr($pac,$ini4+1,$ini5-$ini4-1)."</b>";
		$pacn=$pacn."<br><b>DOCUMENTO: </b>".substr($pac,$ini5+1);
		$query="select Formula from oftalmo_000005 where Paciente='".$pac."' and Oftalmologo='".$medico."' and Fecha='".$fecha."' ";
		$err = mysql_query($query,$conex);
		if($err)
		{
			$row = mysql_fetch_array($err);
			$med=array();	
			$p=0;
			$medicamentos="";
			do
			{
				$pos=strpos($row[0],">");
				$med[$p]=substr($row[0],0,$pos+1);
				$pos2=strpos($med[$p],"-");
				//echo "pos2=".$pos2;
				if($pos2>0)
					$med[$p]=substr($med[$p],$pos2+1);
				$pos3=strpos($med[$p],"*");
				$med[$p]=substr($med[$p],0,$pos3)."<br>".substr($med[$p],$pos3+1);
				$pos2=strpos($med[$p],"-");
				$row[0]=substr($row[0],$pos+1,strlen($row[0]));
				$medicamentos=$medicamentos.$med[$p]."<br>";
				$p++;
			}while(is_int($pos));
		}
		else
			echo "NO EXISTE FORMULA PARA ESE PACIENTE EN ESA FECHA";
		
		echo "<table border=0 width=800><tr><td ><b>FECHA: </b>".$fecha."</td></tr><tr><td >".$pacn."</td></tr><tr><td><br><br></td></tr>";
		echo "</td><tr><td ><b>MEDICAMENTOS:</b></td></tr>";
		echo "<tr><td ><br>".$medicamentos."</td></tr>";
		echo"</table>";
	}
	include_once("free.php");
}
?></HTML>