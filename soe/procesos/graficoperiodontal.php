<html>

<head>
  <title>REGISTRO PERIODONTAL</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");

session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	if(!isset($medico)  or !isset($pac)  )
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
	
		echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
	}// if de los parametros no estan set
	else

	{
		$diente[0]=18;
		$diente[1]=17;
		$diente[2]=16;
		$diente[3]=15;
		$diente[4]=14;
		$diente[5]=13;
		$diente[6]=12;
		$diente[7]=11;
		$diente[8]=21;
		$diente[9]=22;
		$diente[10]=23;
		$diente[11]=24;
		$diente[12]=25;
		$diente[13]=26;
		$diente[14]=27;
		$diente[15]=28;

		$exp=explode("-",$pac);
		$query = "SELECT * FROM soe1_000005 WHERE  Identificacion ='".$exp[0]."'  and MID(Examen,1,2)='01'";
		$err = mysql_query($query,$conex);
		$numenf = mysql_num_rows($err);
		for ($i=0;$i<$numenf;$i++)
		{
			$row = mysql_fetch_array($err);
			$Enf[$i]["Diente"]=$row["Num_diente"];
			$Enf[$i]["Arr"]=$row["Arriba"];
			$Enf[$i]["Ab"]=$row["Abajo"];
			$Enf[$i]["Izq"]=$row["Izquierda"];
			$Enf[$i]["Der"]=$row["Derecha"];
		}
		
		for ($i=0; $i<count($diente);$i++)
		{
			$j=0 ;
			$ok=false;
			
			if(isset($Enf))
			{
			while ($j<count($Enf) and $ok == false )
			{
				if ($Enf[$j]["Diente"]==$diente[$i])
				{
					$placa["Placa"][$diente[$i]]['Ar']=$Enf[$j]["Arr"];
					$placa["Placa"][$diente[$i]]['Ab']=$Enf[$j]["Ab"];
					$placa["Placa"][$diente[$i]]['Iz']=$Enf[$j]["Izq"];
					$placa["Placa"][$diente[$i]]['De']=$Enf[$j]["Der"];
					$ok=true;
				}
				$j++;
			}
			}else{$Enf=0;
			}
			if ($ok==false){
				$placa["Placa"][$diente[$i]]['Ar']=" ";
				$placa["Placa"][$diente[$i]]['Ab']=" ";
				$placa["Placa"][$diente[$i]]['Iz']=" ";
				$placa["Placa"][$diente[$i]]['De']=" ";
				
			}

		}

 		$query = "SELECT * FROM soe1_000005 WHERE  Identificacion ='".$exp[0]."'  and MID(Examen,1,2)='02'";
		$err = mysql_query($query,$conex);
		$numenf = mysql_num_rows($err);
		for ($i=0;$i<$numenf;$i++)
		{
			$row = mysql_fetch_array($err);
			$Enf1[$i]["Diente"]=$row["Num_diente"];
			$Enf1[$i]["Arr"]=$row["Arriba"];
			$Enf1[$i]["Ab"]=$row["Abajo"];
			$Enf1[$i]["Izq"]=$row["Izquierda"];
			$Enf1[$i]["Der"]=$row["Derecha"];
		}
		
	
		
		for ($i=0; $i<count($diente);$i++)
		{
			$j=0 ;
			$ok=false;
			
			if(isset($Enf1))
			{
			while ($j<count($Enf1) and $ok == false )
			{
				if ($Enf1[$j]["Diente"]==$diente[$i])
				{
					$sangrado["Sangrado"][$diente[$i]]['Ar']=$Enf1[$j]["Arr"];
					$sangrado["Sangrado"][$diente[$i]]['Ab']=$Enf1[$j]["Ab"];
					$sangrado["Sangrado"][$diente[$i]]['Iz']=$Enf1[$j]["Izq"];
					$sangrado["Sangrado"][$diente[$i]]['De']=$Enf1[$j]["Der"];
					$ok=true;
				}
				$j++;
			}
			}else{$Enf1=0;
			}
			if ($ok==false){
				$sangrado["Sangrado"][$diente[$i]]['Ar']=" ";
				$sangrado["Sangrado"][$diente[$i]]['Ab']=" ";
				$sangrado["Sangrado"][$diente[$i]]['Iz']=" ";
				$sangrado["Sangrado"][$diente[$i]]['De']=" ";
				
			}

		}

		$query = "SELECT * FROM soe1_000005 WHERE  Identificacion ='".$exp[0]."'  and MID(Examen,1,2)='03'";
		$err = mysql_query($query,$conex);
		$numenf = mysql_num_rows($err);
		for ($i=0;$i<$numenf;$i++)
		{
			$row = mysql_fetch_array($err);
			$Enf2[$i]["Diente"]=$row["Num_diente"];
			$Enf2[$i]["Arr"]=$row["Arriba"];
			$Enf2[$i]["Ab"]=$row["Abajo"];
			$Enf2[$i]["Izq"]=$row["Izquierda"];
			$Enf2[$i]["Der"]=$row["Derecha"];
		}
	
		for ($i=0; $i<count($diente);$i++)
		{
			
			$j=0 ;
			$ok=false;
			
			if(isset($Enf2))
			{
				while ($j<count($Enf2) and $ok == false )
				{
					if ($Enf2[$j]["Diente"]==$diente[$i])
					{
					$profundidad["Profundidad"][$diente[$i]]['Ar']=$Enf2[$j]["Arr"];
					$profundidad["Profundidad"][$diente[$i]]['Ab']=$Enf2[$j]["Ab"];
					$profundidad["Profundidad"][$diente[$i]]['Iz']=$Enf2[$j]["Izq"];
					$profundidad["Profundidad"][$diente[$i]]['De']=$Enf2[$j]["Der"];
					$ok=true;
					}
				$j++;
				}
			}else{$Enf2=0;
			}
			if ($ok==false){
				$profundidad["Profundidad"][$diente[$i]]['Ar']=" ";
				$profundidad["Profundidad"][$diente[$i]]['Ab']=" ";
				$profundidad["Profundidad"][$diente[$i]]['Iz']=" ";
				$profundidad["Profundidad"][$diente[$i]]['De']=" ";
				
			}

		}
	
		$query = "SELECT * FROM soe1_000005 WHERE  Identificacion ='".$exp[0]."'  and MID(Examen,1,2)='04'";
		$err = mysql_query($query,$conex);
		$numenf = mysql_num_rows($err);
		for ($i=0;$i<$numenf;$i++)
		{
			$row = mysql_fetch_array($err);
			$Enf3[$i]["Diente"]=$row["Num_diente"];
			$Enf3[$i]["Arr"]=$row["Arriba"];
			$Enf3[$i]["Ab"]=$row["Abajo"];
			$Enf3[$i]["Izq"]=$row["Izquierda"];
			$Enf3[$i]["Der"]=$row["Derecha"];
		}
	
		for ($i=0; $i<count($diente);$i++)
		{
			
			$j=0 ;
			$ok=false;
			
			if(isset($Enf3))
			{
				while ($j<count($Enf3) and $ok == false )
				{
					if ($Enf3[$j]["Diente"]==$diente[$i])
					{
					$margen["Margen"][$diente[$i]]['Ar']=$Enf3[$j]["Arr"];
					$margen["Margen"][$diente[$i]]['Ab']=$Enf3[$j]["Ab"];
					$margen["Margen"][$diente[$i]]['Iz']=$Enf3[$j]["Izq"];
					$margen["Margen"][$diente[$i]]['De']=$Enf3[$j]["Der"];
					$ok=true;
					}
				$j++;
				}
			}else{$Enf3=0;
			}
			if ($ok==false){
				$margen["Margen"][$diente[$i]]['Ar']=" ";
				$margen["Margen"][$diente[$i]]['Ab']=" ";
				$margen["Margen"][$diente[$i]]['Iz']=" ";
				$margen["Margen"][$diente[$i]]['De']=" ";
				
			}

		}
		
		$query = "SELECT * FROM soe1_000005 WHERE  Identificacion ='".$exp[0]."'  and MID(Examen,1,2)='05'";
		$err = mysql_query($query,$conex);
		$numenf = mysql_num_rows($err);
		for ($i=0;$i<$numenf;$i++)
		{
			$row = mysql_fetch_array($err);
			$Enf4[$i]["Diente"]=$row["Num_diente"];
			$Enf4[$i]["Arr"]=$row["Arriba"];
		}
	
		for ($i=0; $i<count($diente);$i++)
		{
			
			$j=0 ;
			$ok=false;
			
			if(isset($Enf4))
			{
				while ($j<count($Enf4) and $ok == false )
				{
					if ($Enf4[$j]["Diente"]==$diente[$i])
					{
					$movilidad["Movilidad"][$diente[$i]]['Ar']=$Enf4[$j]["Arr"];
					$ok=true;
					}
				$j++;
				}
			}else{$Enf4=0;
			}
			if ($ok==false){
				$movilidad["Movilidad"][$diente[$i]]['Ar']=" ";
			}

		}
		
		$query = "SELECT * FROM soe1_000005 WHERE  Identificacion ='".$exp[0]."'  and MID(Examen,1,2)='06'";
		$err = mysql_query($query,$conex);
		$numenf = mysql_num_rows($err);
		for ($i=0;$i<$numenf;$i++)
		{
			$row = mysql_fetch_array($err);
			$Enf5[$i]["Diente"]=$row["Num_diente"];
			$Enf5[$i]["Arr"]=$row["Arriba"];
		}
	
		for ($i=0; $i<count($diente);$i++)
		{
			
			$j=0 ;
			$ok=false;
			
			if(isset($Enf5))
			{
				while ($j<count($Enf5) and $ok == false )
				{
					if ($Enf5[$j]["Diente"]==$diente[$i])
					{
					$bifurcacion["Bifurcacion"][$diente[$i]]['Ar']=$Enf5[$j]["Arr"];
					$ok=true;
					}
				$j++;
				}
			}else{$Enf5=0;
			}
			if ($ok==false){
				$bifurcacion["Bifurcacion"][$diente[$i]]['Ar']=" ";
			}

		}
		
		echo "<form action='' method='post'>";
		echo "<table align=center width=950 table border=1>";
		echo "<tr><td  bgcolor='#99CCFF' colspan= '9' align=center><font size=3 face='arial' ><B>FICHADO PERIODONTAL</b></td><td  bgcolor='#99CCFF' colspan= '8' align=center><font size=3 face='arial' ><B>PACIENTE:</b> ".$exp[1]." ".$exp[2]." ".$exp[3]." ".$exp[4]."</td></tr>";
		echo "<tr><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>EXAMEN INICIAL</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>18</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>17</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>16</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>15</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>14</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>13</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>12</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>11</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>21</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>22</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>23</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>24</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>25</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>26</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>27</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>28</b></td></tr>";
		echo "<tr><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>PLACA</b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['18']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['18']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['18']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['18']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['17']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['17']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['17']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['17']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['16']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['16']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['16']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['16']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['15']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['15']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['15']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['15']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['14']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['14']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['14']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['14']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['13']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['13']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['13']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['13']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['12']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['12']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['12']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['12']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['11']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['11']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['11']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['11']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['21']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['21']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['21']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['21']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['22']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['22']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['22']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['22']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['23']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['23']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['23']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['23']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['24']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['24']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['24']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['24']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['25']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['25']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['25']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['25']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['26']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['26']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['26']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['26']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['27']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['27']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['27']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['27']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['28']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['28']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['28']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['28']['Ab']."</td><td></td></tr></table></b></td></tr>";
		echo "<tr><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>SANGRADO</b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['18']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['18']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['18']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['18']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['17']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['17']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['17']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['17']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['16']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['16']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['16']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['16']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['15']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['15']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['15']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['15']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['14']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['14']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['14']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['14']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['13']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['13']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['13']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['13']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['12']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['12']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['12']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['12']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['11']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['11']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['11']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['11']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['21']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['21']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['21']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['21']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['22']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['22']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['22']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['22']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['23']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['23']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['23']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['23']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['24']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['24']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['24']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['24']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['25']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['25']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['25']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['25']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['26']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['26']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['26']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['26']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['27']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['27']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['27']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['27']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['28']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['28']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['28']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['28']['Ab']."</td><td></td></tr></table></b></td></tr>";
		echo "<tr><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>PS</b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['18']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['18']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['18']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['18']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['17']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['17']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['17']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['17']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['16']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['16']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['16']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['16']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['15']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['15']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['15']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['15']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['14']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['14']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['14']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['14']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['13']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['13']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['13']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['13']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['12']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['12']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['12']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['12']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['11']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['11']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['11']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['11']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['21']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['21']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['21']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['21']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['22']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['22']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['22']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['22']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['23']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['23']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['23']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['23']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['24']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['24']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['24']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['24']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['25']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['25']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['25']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['25']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['26']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['26']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['26']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['26']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['27']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['27']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['27']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['27']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['28']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['28']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['28']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['28']['Ab']."</td><td></td></tr></table></b></td></tr>";
		echo "<tr><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>M-UCA</b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['18']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['18']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['18']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['18']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['17']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['17']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['17']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['17']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['16']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['16']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['16']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['16']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['15']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['15']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['15']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['15']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['14']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['14']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['14']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['14']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['13']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['13']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['13']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['13']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['12']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['12']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['12']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['12']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['11']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['11']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['11']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['11']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['21']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['21']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['21']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['21']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['22']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['22']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['22']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['22']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['23']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['23']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['23']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['23']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['24']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['24']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['24']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['24']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['25']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['25']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['25']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['25']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['26']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['26']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['26']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['26']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['27']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['27']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['27']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['27']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['28']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['28']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['28']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['28']['Ab']."</td><td></td></tr></table></b></td></tr>";
		echo "<tr><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>MOVILIDAD</b></td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['18']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['17']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['16']['Ar']."&nbsp &nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['15']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['14']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['13']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['12']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['11']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['21']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['22']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['23']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['24']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['25']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['26']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['27']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['28']['Ar']."&nbsp</td></tr>";
		echo "<tr><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>BIFURCACION</b></td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['18']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['17']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['16']['Ar']."&nbsp &nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['15']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['14']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['13']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['12']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['11']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['21']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['22']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['23']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['24']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['25']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['26']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['27']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['28']['Ar']."&nbsp</td></tr>";
		echo "</table>";
		echo "<br>";
		echo "<table align=center width=950 table border=0>";
		$hyper="<A HREF='/matrix/soe/procesos/graficoperiodontal1.php?medico=".$medico."&amp;pac=".$pac."'>Dientes 48 a 38</a>";	
		echo "<tr><td  colspan= '4' align=center><font size=3 face='arial' >$hyper</td></tr>";
		echo "</table>";
		echo "</form>";
	}
			}

include_once("free.php");
?>