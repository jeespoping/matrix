<html>
<head>
  <title>MATRIX</title>
      <style type="text/css">
    	.tipoTABLE{font-family:Ubuntu;border-style:solid;border-collapse:collapse;}
    	#tipoT{color:#000066;background:#999999;font-size:10pt;font-family:Ubuntu;font-weight:bold;text-align:center;height:1em;;border-style:solid;border-collapse:collapse;}
    	#tipoT1{color:#000066;background:#DDDDDD;font-size:10pt;font-family:Ubuntu;font-weight:bold;text-align:center;height:1em;;border-style:solid;border-collapse:collapse;}
    	#tipoTR{color:#000066;background:#999999;font-size:10pt;font-family:Ubuntu;font-weight:bold;text-align:right;height:1em;;border-style:solid;border-collapse:collapse;}
    	#tipoL1{color:#000066;background:#E8EEF7;font-size:10pt;font-family:Ubuntu;font-weight:normal;text-align:right;height:1em;;border-style:solid;border-collapse:collapse;}
    	#tipoL2{color:#000066;background:#C3D9FF;font-size:10pt;font-family:Ubuntu;font-weight:normal;text-align:right;height:1em;;border-style:solid;border-collapse:collapse;}
    	#tipoL1C{color:#000066;background:#E8EEF7;font-size:10pt;font-family:Ubuntu;font-weight:normal;text-align:center;height:1em;;border-style:solid;border-collapse:collapse;}
    	#tipoL2C{color:#000066;background:#C3D9FF;font-size:10pt;font-family:Ubuntu;font-weight:normal;text-align:center;height:1em;;border-style:solid;border-collapse:collapse;}
    </style>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Evaluacion del Istat</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro166.php Ver. 2013-02-01</b></font></td></tr></table>
</center>

<?php
include_once("conex.php");
function comparacion($vec1,$vec2)
{
	if($vec1[1] < $vec2[1])
		return 1;
	elseif ($vec1[1] > $vec2[1])
				return -1;
			else
				return 0;
}
function incluido($str1,$str2)
{
	global $GENERAL;
	$GENERAL="";
	$vector=explode("-",$str2);
	for ($i=0;$i<count($vector);$i++)
		if(strpos($str1,$vector[$i]) === false)
		{
			if(strlen($GENERAL) == 0)
				$GENERAL .= $vector[$i];
			else
				$GENERAL .= "-".$vector[$i];
		}
	if(strlen($GENERAL) == 0)
		return 1;
	else
		return 0;
}
function smart($str1,$str2)
{
	if(strcmp($str1,$str2) == 0)
		return 0;
	else if(incluido($str1,$str2) == 1)
			return 1;
		else
			return 2;
}

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro166.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wcco1)  or !isset($wcco2) or !isset($wnum))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>EVALUACION DEL ISTAT</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Registro Nro (0 Todos)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wnum' size=7 maxlength=7></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$before = array_sum(explode(' ', microtime()));
			$examenes=array();
			//                  0      1  
			$query  = "SELECT Pexcex,Pexpos from ".$empresa."_000149 ";
			$query .= "   order by 2 ";
			$err = mysql_query($query,$conex);
			$numexa = mysql_num_rows($err);
			for ($i=0;$i<$numexa;$i++)
			{
				$row = mysql_fetch_array($err);
				$examenes[$i][0]=$row[0];
				$examenes[$i][1]=dechex($i);
			}
			$kits=array();
			$Nkits=array();
			$Tkits=array();
			$Wkits=array();
			//                  0       1  
			$query  = "SELECT Kitcod, Kitval from ".$empresa."_000147 ";
			$query .= " where Kitest = 'on' ";
			$query .= "   order by 1 ";
			$err = mysql_query($query,$conex);
			$numkits = mysql_num_rows($err);
			for ($i=0;$i<$numkits;$i++)
			{
				$row = mysql_fetch_array($err);
				$kits[$i][0]=$row[0];
				$kits[$i][1]=$row[1];
				$Nkits[$i][0]="";
				$Nkits[$i][1]=0;
				$Nkits[$i][2]=0;
				$Nkits[$i][3]=$row[0];
				$Nkits[$i][4]=$row[1];
				$Wkits[$i][0]="";
				$Wkits[$i][1]=0;
				$Wkits[$i][2]=0;
				$Wkits[$i][3]=$row[0];
				$Wkits[$i][4]=$row[1];
				$Tkits[$row[0]][0]=0;
				$Tkits[$row[0]][1]=$row[1];
				$Tkits[$row[0]][2]=0;
			}
			$relacion=array();
			//                  0       1  
			$query  = "SELECT Rkecex, Rkekit from ".$empresa."_000148 ";
			$query .= " where Rkeest = 'on'";
			$query .= "   order by 2 ";
			$err = mysql_query($query,$conex);
			$numrel = mysql_num_rows($err);
			for ($i=0;$i<$numrel;$i++)
			{
				$row = mysql_fetch_array($err);
				$relacion[$row[0]][$row[1]]=1;
			}

			$query  = "SELECT Stacon,Staexa from ".$empresa."_000150 ";
			$query .= " where Stacco between '".$wcco1."' and '".$wcco2."' ";
			if($wnum != 0)
				$query .= " and Stacon = ".$wnum;
			$query .= "   order by 1 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$vector=explode("-",$row[1]);
					for ($i1=0;$i1<$numkits;$i1++)
						for ($i2=0;$i2<$numexa;$i2++)
						{
							if($vector[$i2] > "0")
							{
								if(isset($relacion[$examenes[$i2][0]][$kits[$i1][0]]))
								{
									if(strlen($Nkits[$i1][0]) == 0)
										$Nkits[$i1][0] .= $examenes[$i2][1];
									else
										$Nkits[$i1][0] .= "-".$examenes[$i2][1];
									$Nkits[$i1][1] = strlen($Nkits[$i1][0]);
								}
							}
						}
					usort($Nkits,'comparacion');
					$Nkits[0][2]=1;
					for ($j=1;$j<$numkits;$j++)
					{
						if($Nkits[$j][1] > 0)
						{
							for ($k=0;$k<$j;$k++)
							{
								if($Nkits[$k][2] == 1)
								{
									$valido=0;
									$valor=smart($Nkits[$k][0],$Nkits[$j][0]);
									if($valor == 0)
									{
										if($Nkits[$j][4] < $Nkits[$k][4])
										{
											$Nkits[$k][2]=0;
											$valido=1;
										}
										break;
									}
									elseif($valor == 1)
										{
											$valido=0;
											break;
										}
										else
										{
											$valido=1;
											$Nkits[$j][0]=$GENERAL;
										}
								}
							}
							if($valido == 1)
							{
								$Nkits[$j][2]=1;
							}
						}
					}
					for ($j=0;$j<$numkits;$j++)
					{
						if($Nkits[$j][1] > 0 and $Nkits[$j][2] == 1)
						{
							$vectorE=explode("-",$Nkits[$j][0]);
							for ($k=0;$k<count($vectorE);$k++)
								if($vector[hexdec($vectorE[$k])] == 2)
									$Nkits[$j][2]++;
						}
					}
					if($wnum != 0)
					{
						$tl=-1;
						echo "<br><center><table  class=tipoTABLE>";
						echo "<tr><td colspan=4 id=tipoT1>EVALUACION DE LA PETICION ".$wnum."</td></tr>";
						echo "<tr><td id=tipoT>EXAMENES</td><td id=tipoT>CANTIDAD DE <br>EXAMENES</td><td id=tipoT>SELECCIONADO</td><td id=tipoT>KIT</td></tr>";
						for ($j=0;$j<$numkits;$j++)
						{
							$tl++;
							if($tl % 2 == 0)
								$tipo = "tipoL1";
							else
								$tipo = "tipoL2";
							echo "<tr><td id=".$tipo."C>".$Nkits[$j][0]."</td><td id=".$tipo.">".$Nkits[$j][1]."</td><td id=".$tipo."C>".$Nkits[$j][2]."</td><td id=".$tipo."C>".$Nkits[$j][3]."</td></tr>";
						}
						echo "</center></table>";
					}
					for ($j=0;$j<$numkits;$j++)
					{
						if($Nkits[$j][2] > 0)
							$Tkits[$Nkits[$j][3]][0] += $Nkits[$j][2];
						else
							$Tkits[$Nkits[$j][3]][2]++;
					}
					$Nkits=array();
					for ($i1=0;$i1<$numkits;$i1++)
					{
						$Nkits[$i1][0]=$Wkits[$i1][0];
						$Nkits[$i1][1]=$Wkits[$i1][1];
						$Nkits[$i1][2]=$Wkits[$i1][2];	
						$Nkits[$i1][3]=$Wkits[$i1][3];
						$Nkits[$i1][4]=$Wkits[$i1][4];
					}
				}
				$GT=0;
				echo "<br><center><table class=tipoTABLE>";
				echo "<tr><td colspan=5 id=tipoT1>EVALUACION DEL ISTAT</td></tr>";
				echo "<tr><td id=tipoT>KIT</td><td id=tipoT>USOS</td><td id=tipoT>NO USOS</td><td id=tipoT>VALOR<br>UNITARIO</td><td id=tipoT>VALOR<br>TOTAL</td></tr>";
				$tl=-1;
				foreach($Tkits as $indice => $valor)
				{
					$tl++;
					$tot=$valor[0]*$valor[1];
					$GT += $tot;
					if($tl % 2 == 0)
						$tipo = "tipoL1";
					else
						$tipo = "tipoL2";
					echo "<tr><td id=".$tipo."C>".$indice."</td><td  id=".$tipo.">".number_format((double)$valor[0],0,'.',',')."</td><td  id=".$tipo.">".number_format((double)$valor[2],0,'.',',')."</td><td  id=".$tipo.">".number_format((double)$valor[1],0,'.',',')."</td><td  id=".$tipo.">".number_format((double)$tot,0,'.',',')."</td></tr>";
				}
				echo "<tr><td colspan=4 id=tipoT>TOTAL</td><td id=tipoTR>".number_format((double)$GT,0,'.',',')."</td></tr>";
				echo "</center></table>";
				$after = array_sum(explode(' ', microtime(true)));
				$DIFF=$after - $before;
				echo "Tiempo : ".$DIFF." Segundo(s)<br>";
			}
		}
	}
?>
</body>
</html>
