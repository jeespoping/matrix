<html>

<head>
  <title>LABORATORIOS</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000000"><?php
include_once("conex.php");
  /**************************************************
   * 			IMPRESION DE						*
   *       FORMULARIO LABORATORIOS                  *
   *	      CONEX, FREE => OK                     *
   **************************************************/

session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

		// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)
		
	$empresa='hcgmv';
	
	if(!isset($medico)  or !isset($pac) or !isset($fecha) )
	{
		echo "<form action='rep_hcgmvlab.php' method=post>";
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><font color=#000066><b>CL�NICA MEDICA LAS AMERICAS </b></td></tr>";
		echo "<tr><td align=center colspan=3><font color=#000066>TORRE M�DICA</td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>MEDICO: </td>";	
			/* Si el medico no ha sido escogido Buscar a los oftalmologos de la seleccion para 
				construir el drop down*/
			echo "<td bgcolor=#cccccc colspan=2><select name='medico'>";
			$query = "SELECT Subcodigo,Descripcion FROM `det_selecciones` WHERE medico = '".$empresa."' AND codigo = '002'  order by Descripcion ";
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
			$query="select Paciente from ".$empresa."_000005 where Cirujano='".$medico."' and Paciente like '%".$pac1."%' order by Paciente";
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
			$query = "select Fecha  from ".$empresa."_000005 where Paciente='".$pac."' and Cirujano='".$medico."' ";
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
		$doc=explode("-",$pac);
		$ini1=strpos($pac,"-");
		$ini2=strpos($pac,"-",$ini1+1);
		$ini3=strpos($pac,"-",$ini2+1);
		$ini4=strpos($pac,"-",$ini3+1);
		$ini5=strpos($pac,"-",$ini4+1);
		$pacn="<b>".substr($pac,$ini1+1,$ini2-$ini1-1)." ".substr($pac,$ini2+1,$ini3-$ini2-1)." ".substr($pac,$ini3+1,$ini4-$ini3-1)." ".substr($pac,$ini4+1,$ini5-$ini4-1)."</b>";
		$pacn=$pacn."<br><b>DOCUMENTO: </b>".$doc[0];
		$query="select * from ".$empresa."_000005 where Paciente='".$pac."' and Cirujano='".$medico."' and Fecha='".$fecha."' ";
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
		
		echo "<table border=0 width=800>";
		echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<img SRC='/matrix/images/medical/pediatra/logotorre.jpg' width='180' height='117'></td>";
		echo "</tr><tr><td  colspan='4'><font size=2 color='#000080' face='arial'>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<B>Dr. Gonzalo Mejia Velez";
		echo "</tr><tr><td  colspan='4'><font size=2 color='#000080' face='arial'>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<B>Medico Internista Nefrologo";
		echo "</tr><tr><td  colspan='4'><font size=2 color='#000080' face='arial'>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<B>Torre medica Las Americas";
		echo "</tr><tr><td  colspan='4'><font size=2 color='#000080' face='arial'>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<B>Reg. 2881-73";
		echo "<tr><td><br></td></tr>";		
		echo "<tr><td ><b>FECHA: </b>".$fecha."</td></tr><tr><td >".$pacn."</td></tr><tr><td><br><br></td></tr>";
		echo "</td><tr><td ><b>LABORATORIOS:</b></td></tr><br>";
		if ($row['Hemograma']!='off')
			echo "<tr><td >Hemograma<br></td></tr>";
		if ($row['Bun']!='off')
			echo "<tr><td >Bun<br></td></tr>";
		if ($row['Creatinina']!='off')
			echo "<tr><td >Creatinina<br></td></tr>";
		if ($row['Calcio']!='off')
			echo "<tr><td >Calcio<br></td></tr>";
		if ($row['Acido_urico']!='off')
			echo "<tr><td >Acido urico<br></td></tr>";
		if ($row['Fosforo']!='off')
			echo "<tr><td >Fosforo<br></td></tr>";
		if ($row['Potasio']!='off')
			echo "<tr><td >Potasio<br></td></tr>";
		if ($row['Glicemia']!='off')
			echo "<tr><td >Glicemia<br></td></tr>";
		if ($row['Colesterol']!='off')
			echo "<tr><td >Colesterol<br></td></tr>";
		if ($row['Trigliceridos']!='off')
			echo "<tr><td >Trigliceridos<br></td></tr>";
		if ($row['Hdl']!='off')
			echo "<tr><td >HDL<br></td></tr>";
		if ($row['Ldl']!='off')
			echo "<tr><td >LDL<br></td></tr>";
		if ($row['Proteinas_totales']!='off')
			echo "<tr><td >Proteinas totales<br></td></tr>";
		if ($row['Albumina']!='off')
			echo "<tr><td >Albumina<br></td></tr>";
		if ($row['Tsh']!='off')
			echo "<tr><td >TSH<br></td></tr>";
		if ($row['Ferritina']!='off')
			echo "<tr><td >Ferritina<br></td></tr>";
		if ($row['Citoquimico_orina']!='off')
			echo "<tr><td >Citoquimico de orina<br></td></tr>";
		if ($row['Depuracion_creatinina']!='off')
			echo "<tr><td >Depuracion de creatinina<br></td></tr>";
		if ($row['Proteinuria_orina_24h	']!='off')
			echo "<tr><td >Proteinuria en orina de 24 horas<br></td></tr>";
		if ($row['Otros_examenes']!='.')
			echo "<tr><td >".$row['Otros_examenes']."<br></td></tr>";
		echo"</table>";
	}
	include_once("free.php");
}
?></HTML>