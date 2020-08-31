<html>
<head>
  	<title>MATRIX Proceso de Informacion de Asambleas</title>  	      
	<style type="text/css">
	<!--
		.BlueThing
		{
			background: #99CCFF;
		}
		
		.SilverThing
		{
			background: #CCCCCC;
		}
		
		.GrayThing
		{
			background: #CCCCCC;
		}
	
	//-->
	</style>
</head>
<body onload=ira() BGCOLOR="FFFFFF" oncontextmenu = "return false" onselectstart = "return true" ondragstart = "return false">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.proceso.submit();
	}
	
	function limpiar()
	{
		location.href = 'Proceso.php?empresa=asamblea';
	}
//-->
</script>
<?php
include_once("conex.php");

include_once("root/comun.php");
encabezado( "ASAMBLEA DE ACCIONISTAS  ", $actualiz ,"clinica" ); 
/**********************************************************************************************************************  
	   PROGRAMA : Proceso.php
	   Fecha de Liberacion : 2007-03-25
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2007-03-25
	   
	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface gr�fica que permite grabar el movimiento de
	   informacion generado en las asambleas de accionistas tales como:
	   	1. registro de asistencia
	   	2. registro de delegacion
	   	3. votos para plancha de junta directiva
	   	4. otras votaciones.
	   
	   
	   REGISTRO DE MODIFICACIONES :
	   .2007-03-25
	   		Release de Versi�n Beta.
	   
***********************************************************************************************************************/

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='proceso' action='Proceso.php' method=post>";
	

	

	
	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	$OK=0;
	if(isset($wnew))
	{
		$OK=0;
		$wacc="";
		$wdel="";
	}
	if(!isset($wmarc) and !isset($wop) and isset($wacc) and $wacc!= "" and !isset($wnew))
		$OK=1;
	if(isset($wmarc) and $wacc!= "" and !isset($wnew))
	{
		if(!isset($wtip))
			$wtip=9;
		$query = "SELECT Accvxp, Acccod, Accnom from ".$empresa."_000001  where Acccod='".$wacc."' and Accemp='".$wnit."' and Accact='on' ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$row = mysql_fetch_array($err);
			$cedacc=$row[1];
			$nomacc=$row[2];
			$wvxp=$row[0];
			$query = "lock table ".$empresa."_000005 LOW_PRIORITY WRITE, ".$empresa."_000001 LOW_PRIORITY WRITE ";
			$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO MOVIMIENTO");
			switch ($wtip)
			{
				case 0:
					$query = "SELECT Movval from ".$empresa."_000005  where Movemp='".$wnit."' and Movano=".$wano." and Movmes=".$wmes." and Movcac='".$wacc."' and Movcpa='PR' ";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					if($num == 0)
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000005 (medico,fecha_data,hora_data, Movemp, Movano, Movmes, Movcac, Movcpa, Movdel, Movval, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wnit."',".$wano.",".$wmes.",'".$wacc."','PR','NO','N','C-".$key."')";
						$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					}
					else
					{
						$query =  " update ".$empresa."_000005 set Movval='N' where Movemp='".$wnit."' and Movano=".$wano." and Movmes=".$wmes." and Movcac='".$wacc."' and Movcpa='PR' ";
						$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					}
					if(!isset($wop))
					{
						echo "<center><table border=0 aling=center>";
						echo "<tr><td align=center bgcolor=#99CCFF><IMG SRC='/matrix/images/medical/root/feliz.png'>&nbsp&nbsp<font color=#000000 face='Arial'></font></TD><TD bgcolor=#99CCFF><font color=#000000 face='Arial'><b>".$cedacc." ".$nomacc." ------ MARCADO AUSENTE</b></font></td></tr>";
						echo "</table><br><br></center>";
					}
					$wdel="";
				break;
				case 1:
					$query = "SELECT Movval from ".$empresa."_000005  where Movemp='".$wnit."' and Movano=".$wano." and Movmes=".$wmes." and Movcac='".$wacc."' and Movcpa='PR' ";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					if($num == 0)
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000005 (medico,fecha_data,hora_data, Movemp, Movano, Movmes, Movcac, Movcpa, Movdel, Movval, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wnit."',".$wano.",".$wmes.",'".$wacc."','PR','NO','S','C-".$key."')";
						$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					}
					else
					{
						$query =  " update ".$empresa."_000005 set Movdel='NO',Movval='S' where Movemp='".$wnit."' and Movano=".$wano." and Movmes=".$wmes." and Movcac='".$wacc."' and Movcpa='PR' ";
						$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					}
					if(!isset($wop))
					{
						echo "<center><table border=0 aling=center>";
						echo "<tr><td align=center bgcolor=#99CCFF><IMG SRC='/matrix/images/medical/root/feliz.png'>&nbsp&nbsp<font color=#000000 face='Arial'></font></TD><TD bgcolor=#99CCFF><font color=#000000 face='Arial'><b>".$cedacc." ".$nomacc." ------ REGISTRADO PRESENTE</b></font></td></tr>";
						echo "</table><br><br></center>";
					}
					$wdel="";
				break;
				case 2:
					if($wdel != $wacc)
					{
						$query = "SELECT Movdel from ".$empresa."_000005  where Movemp='".$wnit."' and Movano=".$wano." and Movmes=".$wmes." and Movcac='".$wdel."' and Movcpa='PR' and Movval='S' ";
						$err = mysql_query($query,$conex);
						$num = mysql_num_rows($err);
						if($num > 0)
						{
							$row = mysql_fetch_array($err);
							if($row[0] == "NO")
							{
								$query = "SELECT Accvxp, Acccod, Accnom from ".$empresa."_000001  where Acccod='".$wdel."' and Accemp='".$wnit."' and Accact='on' and Accpde='on' ";
								$err1 = mysql_query($query,$conex);
								$num1 = mysql_num_rows($err1);
								$row1 = mysql_fetch_array($err1);
								if($num1 > 0)
								{
									$query = "SELECT Movval from ".$empresa."_000005  where Movemp='".$wnit."' and Movano=".$wano." and Movmes=".$wmes." and Movcac='".$wacc."' and Movcpa='PR' ";
									$err = mysql_query($query,$conex);
									$num = mysql_num_rows($err);
									if($num == 0)
									{
										$fecha = date("Y-m-d");
										$hora = (string)date("H:i:s");
										$query = "insert ".$empresa."_000005 (medico,fecha_data,hora_data, Movemp, Movano, Movmes, Movcac, Movcpa, Movdel, Movval, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wnit."',".$wano.",".$wmes.",'".$wacc."','PR','".$wdel."','S','C-".$key."')";
										$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
									}
									else
									{
										$query =  " update ".$empresa."_000005 set Movdel='".$wdel."',Movval='S' where Movemp='".$wnit."' and Movano=".$wano." and Movmes=".$wmes." and Movcac='".$wacc."' and Movcpa='PR' ";
										$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
									}
									$query = "SELECT count(*) from ".$empresa."_000005  where Movemp='".$wnit."' and Movano=".$wano." and Movmes=".$wmes." and Movdel='".$wdel."' and Movcpa='PR' ";
									$err2 = mysql_query($query,$conex);
									$num2 = mysql_num_rows($err2);
									$row2 = mysql_fetch_array($err2);
									if(!isset($wop))
									{
										echo "<center><table border=0 aling=center>";
										echo "<tr><td align=center bgcolor=#99CCFF><IMG SRC='/matrix/images/medical/root/feliz.png'>&nbsp&nbsp<font color=#000000 face='Arial'></font></TD><TD bgcolor=#99CCFF><font color=#000000 face='Arial'><b>".$cedacc." ".$nomacc." ------ DELEGO EN ".$row1[1]." ".$row1[2]." -------> TOTAL VOTOS DELEGADOS : ".$row2[0]."</b></font></td></tr>";
										echo "</table><br><br></center>";
										
										//$GLOBALS['delegados'] = "";
										
										// $query_del1  = "select Accnom from asamblea_000005,asamblea_000001 ";
										// $query_del1 .= " where movemp='".$wnit."' ";
										// $query_del1 .= "   and movano=".$wano; 
										// $query_del1 .= "   and movmes=".$wmes; 
										// $query_del1 .= "   and movdel = '".$wdel."' "; 
										// $query_del1 .= "   and movcpa='PR' ";
										// $query_del1 .= "   and Movcac = acccod ";
										// $query_del1 .= "   and accemp ='".$wnit."' ";
										// $query_del1 .= "  order by asamblea_000005.id DESC";
										// $err_del1 = mysql_query($query_del1,$conex);
										// $num_del1 = mysql_num_rows($err_del1);					
								
										// if($num_del1 > 0){
										// while($row_del1 = mysql_fetch_array($err_del1)){
											
											// $delegados1 .= $row_del1['Accnom']."<br>";
											
											
										// }										
										
										// $GLOBALS['delegados'] = $delegados1;
																				
										// }
										
										
									}
								}
								else
								{
									$OK=1;
									echo "<center><table border=0 aling=center>";
									echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
									echo "<font size=3><div align=center BGCOLOR=#00ffff LOOP=-1>ERROR !!! EL DELEGATARIO NO EXITE O ESTA INACTIVO O NO PUEDE SER DELEGATARIO!!!!</div></FONT>";
									echo "<br><br>";
								}
							}
							else
							{
								$OK=1;
								echo "<center><table border=0 aling=center>";
								echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
								echo "<font size=3><div align=center BGCOLOR=#00ffff LOOP=-1>ERROR !!! EL DELEGATARIO NO ESTA PRESENTE, DELEGO SU VOTO!!!!</div></FONT>";
								echo "<br><br>";
							}
						}
						else
						{
							$OK=1;
							echo "<center><table border=0 aling=center>";
							echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
							echo "<font size=3><div align=center BGCOLOR=#00ffff LOOP=-1>ERROR !!! EL DELEGATARIO NO ESTA PRESENTE !!!!</div></FONT>";
							echo "<br><br>";
						}
					}
					else
					{
						$OK=1;
						echo "<center><table border=0 aling=center>";
						echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
						echo "<font size=3><div align=center BGCOLOR=#00ffff LOOP=-1>ERROR !!! NO PUEDE DELEGAR EN SI MISMO !!!!</div></FONT>";
						echo "<br><br>";
					}
				break;
				default:
					$OK=1;
				break;
			}
			$query = " UNLOCK TABLES";													
			$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO");
		}
		else
		{
			$OK=1;
			echo "<center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
			echo "<font size=3><div align=center BGCOLOR=#00ffff LOOP=-1>ERROR !!! EL ACCIONISTA O COPROPIETARIO NO EXITE O ESTA INACTIVO!!!!</div></FONT>";
			echo "<br><br>";
		}
	}
	if(isset($wop) and !isset($wnew))
	{
		$wasdel=0;
		$query = "SELECT Movdel from ".$empresa."_000005  where Movemp='".$wnit."' and Movano=".$wano." and Movmes=".$wmes." and Movcac='".$wdel."' and Movcpa='PR' and Movval ='S' ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$row = mysql_fetch_array($err);
			if($row[0] != "NO")
				$wasdel=1;
		}
		else
			$wasdel=2;
		if($wdel == "")
			$wasdel=0;
		if($wdel == $wacc)
			$wasdel=3;
		$query = "SELECT Movdel, Movval from ".$empresa."_000005  where Movemp='".$wnit."' and Movano=".$wano." and Movmes=".$wmes." and Movcac='".$wacc."' and Movcpa='PR' ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$row = mysql_fetch_array($err);
			$wrep=$row[0];
			$wasis=$row[1];
		}
		
		$query = "SELECT Accvxp, Acccod, Accnom from ".$empresa."_000001  where Acccod='".$wacc."' and Accemp='".$wnit."' and Accact='on' ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$row = mysql_fetch_array($err);
			$wvxp=$row[0];
			$cedacc=$row[1];
			$nomacc=$row[2];
		}
		$wxdel=0;
		if($wdel != "")
		{
			$query = "SELECT Accvxp from ".$empresa."_000001  where Acccod='".$wdel."' and Accemp='".$wnit."' and Accact='on' and Accpde='on' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$query = "SELECT Movdel from ".$empresa."_000005  where Movemp='".$wnit."' and Movano=".$wano." and Movmes=".$wmes." and Movcac='".$wdel."' and Movcpa='PR' and Movval='S' ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num > 0)
					$wxdel=1;
			}
		}
		else
		{
			$wxdel=1;
			$wdel=$wrep;
		}
		if($wasdel == 1)
		{
			$OK=1;
			echo "<center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
			echo "<font size=3><div align=center BGCOLOR=#00ffff LOOP=-1>ERROR !!! EL DELEGATARIO NO ESTA PRESENTE, DELEGO SU VOTO!!!!</div></FONT>";
			echo "<br><br>";
		}
		elseif($wasdel == 2 and $wxdel == 1)
			{
				$OK=1;
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif'></td><tr></table></center>";
				echo "<font size=3><div align=center BGCOLOR=#00ffff LOOP=-1>ERROR !!! EL DELEGATARIO NO ESTA PRESENTE !!!!</div></FONT>";
				echo "<br><br>";
			}
			elseif($wasdel == 3)
				{
					$OK=1;
					echo "<center><table border=0 aling=center>";
					echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
					echo "<font size=3><div align=center BGCOLOR=#00ffff LOOP=-1>ERROR !!! NO PUEDE DELEGAR EN SI MISMO !!!!</div></FONT>";
					echo "<br><br>";
				}
		if($wxdel == 1 and $wasdel == 0)
		{
			$wtip="";
			$query = "SELECT Parcod, Parcie from ".$empresa."_000002  where Paremp='".$wnit."' and Paract='on' and Parcod='".$wop."' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$row = mysql_fetch_array($err);
				$wtip=substr($row[0],0,1);
				$wsw=$row[0];
				$wcie=$row[1];
			}
			$query = "lock table ".$empresa."_000005 LOW_PRIORITY WRITE ";
			$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO MOVIMIENTO");
			$query = "SELECT Movval from ".$empresa."_000005  where Movemp='".$wnit."' and Movano=".$wano." and Movmes=".$wmes." and Movcac='".$wacc."' and Movcpa='".$wop."' ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				if($wcie == "off")
				{
					if($wdel == "NO" and $wasis == "N")
					{
						$OK=1;
						echo "<center><table border=0 aling=center>";
						echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
						echo "<font size=3><div align=center BGCOLOR=#00ffff LOOP=-1>ERROR !!! NO PUEDE VOTAR NO SE REGISTRA COMO ASISTENTE!!!!</div></FONT>";
						echo "<br><br>";
					}
					else
						if(isset($wrad[$wsw]))
						{
							$ultimaop=$wrad[$wsw];
							$query =  " update ".$empresa."_000005 set Movval='".$wrad[$wsw]."', Movdel='".$wdel."' where Movemp='".$wnit."' and Movano=".$wano." and Movmes=".$wmes." and Movcac='".$wacc."' and Movcpa='".$wop."' ";
							$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
							$wdel="";
							echo "<center><table border=0 aling=center>";
							echo "<tr><td align=center bgcolor=#99CCFF><IMG SRC='/matrix/images/medical/root/feliz.png'>&nbsp&nbsp<font color=#000000 face='Arial'></font></TD><TD bgcolor=#99CCFF><font color=#000000 face='Arial'><b>".$cedacc." ".$nomacc." ------ VOTO </b></font></td></tr>";
							echo "</table><br><br></center>";
						}
						else
						{
							$OK=1;
							echo "<center><table border=0 aling=center>";
							echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
							echo "<font size=3><div align=center BGCOLOR=#00ffff LOOP=-1>ERROR !!! NO SELECIONO OPCION EL VOTO NO SE REGISTRO!!!!</div></FONT>";
							echo "<br><br>";
						}
				}
				else
				{
					$OK=1;
					echo "<center><table border=0 aling=center>";
					echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
					echo "<font size=3><div align=center BGCOLOR=#00ffff LOOP=-1>ERROR !!! LA VOTACION SE HA CERRADO!!!!</div></FONT>";
					echo "<br><br>";
				}
			}
			else
			{
				if($wcie == "off")
				{
					if($wdel == "NO" and $wasis == "N")
					{
						$OK=1;
						echo "<center><table border=0 aling=center>";
						echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
						echo "<font size=3><div align=center BGCOLOR=#00ffff LOOP=-1>ERROR !!! NO PUEDE VOTAR NO SE REGISTRA COMO ASISTENTE!!!!</div></FONT>";
						echo "<br><br>";
					}
					else
						if(isset($wrad[$wsw]))
						{
							$ultimaop=$wrad[$wsw];
							$fecha = date("Y-m-d");
							$hora = (string)date("H:i:s");
							$query = "insert ".$empresa."_000005 (medico,fecha_data,hora_data, Movemp, Movano, Movmes, Movcac, Movcpa, Movdel, Movval, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wnit."',".$wano.",".$wmes.",'".$wacc."','".$wop."','".$wdel."','".$wrad[$wsw]."','C-".$key."')";
							$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
							$wdel="";
							echo "<center><table border=0 aling=center>";
							echo "<tr><td align=center bgcolor=#99CCFF><IMG SRC='/matrix/images/medical/root/feliz.png'>&nbsp&nbsp<font color=#000000 face='Arial'></font></TD><TD bgcolor=#99CCFF><font color=#000000 face='Arial'><b>".$cedacc." ".$nomacc." ------ VOTO </b></font></td></tr>";
							echo "</table><br><br></center>";
						}
						else
						{
							$OK=1;
							echo "<center><table border=0 aling=center>";
							echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
							echo "<font size=3><div align=center BGCOLOR=#00ffff LOOP=-1>ERROR !!! NO SELECIONO OPCION EL VOTO NO SE REGISTRO!!!!</div></FONT>";
							echo "<br><br>";
						}
				}
				else
				{
					$OK=1;
					echo "<center><table border=0 aling=center>";
					echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
					echo "<font size=3><div align=center BGCOLOR=#00ffff LOOP=-1>ERROR !!! LA VOTACION SE HA CERRADO!!!!</div></FONT>";
					echo "<br><br>";
				}
			}
			$query = " UNLOCK TABLES";													
			$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO");
		}
		else
		{
			if($wxdel == 0)
			{
				$OK=1;
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><div align=center BGCOLOR=#00ffff LOOP=-1>ERROR !!! EL ACCIONISTA O COPROPIETARIO DELEGADO NO EXITE O ESTA INACTIVO O NO PUEDE RECIBIR VOTOS DELEGADOS O NO ESTA PRESENTE!!!!</div></FONT>";
				echo "<br><br>";
			}
		}
	}
	if(isset($wvxp) and $wvxp == "on")
	{
		echo "<center><table border=0 aling=center>";
		echo "<tr><td><IMG SRC='/matrix/images/medical/root/alerta.gif'></td><tr></table></center>";
		echo "<font size=3><div align=center BGCOLOR=#00ffff LOOP=-1>ALERTA ESTE REPRESENTANTE DEBE ADJUNTAR UN PODER!!!!</div></FONT>";
		echo "<br><br>";
	}
	
	echo "<table border=0 align=center>";
	$color="#dddddd";
	$color1="#000099";
	$color2="#006600";
	$color3="#cc0000";
	$color4="#CC99FF";
	$color5="#99CCFF";
	$color6="#FF9966";
	$color7="#cccccc";
	$color8="#999999";
	$query = "SELECT Empcod, Empdes from ".$empresa."_000004 where Empact='on'";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if($OK == 0)
		$wacc="";
	if ($num == 1)
	{
		$row = mysql_fetch_array($err);		
		echo "<tr><td align=right colspan=3><font size=2>Powered by :  MATRIX </font></td></tr>";
		echo "<tr><td align=center bgcolor=#000066 colspan=3><font color=#ffffff size=6><b>ASAMBLEA DE ACCIONISTAS</font><font color=#33CCFF size=4>&nbsp&nbsp&nbspVer. 2018-08-16</font></b></font></td></tr>";
		echo "<tr><td align=center bgcolor=#dddddd colspan=3><input type='submit' name='ENTER' value='GRABAR'><input type='button' name='wnew' value='REINICIAR' onclick='limpiar()'></td></tr>";
		$wnit=$row[0];
		echo "<input type='HIDDEN' name= 'wnit' value='".$wnit."'>";
		$wnnit=$row[1];
		$query = "SELECT Perano, Permes from ".$empresa."_000003 where Peremp='".$wnit."' and Peract='on' ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num == 1)
		{
			$row = mysql_fetch_array($err);
			$wano=$row[0];
			$wmes=$row[1];
			echo "<input type='HIDDEN' name= 'wano' value='".$wano."'>";
			echo "<input type='HIDDEN' name= 'wmes' value='".$wmes."'>";
			if($wacc == "")
			{
				?>
				<script>
					function ira(){document.proceso.wacc.focus();}
				</script>
				<?php
				echo "<tr><td bgcolor=".$color8." align=center colspan=3>EMPRESA : <b>".$wnnit."</b></td></tr>";
				echo "<tr><td bgcolor=".$color." align=center>ACCIONISTA/COPROPIETARIO : </td><td bgcolor=".$color." align=center colspan=2><input type='TEXT' name='wacc' size=12 maxlength=14 value='".$wacc."'> Buscar<input type='RADIO' name='wenter' value='00' onclick='enter()'></td></tr>";	
				//echo "<tr><td bgcolor=".$color." >MARCAR : <input type='checkbox' name='wmarc' checked></td>";
				$wmarc=1;
				echo "<input type='HIDDEN' name= 'wmarc' value='".$wmarc."'>";
				echo "<td bgcolor=".$color." colspan=3 align=center>";
				echo "<input type='RADIO' name='wtip' value=0 onclick='enter()'> Ausente ";
				echo "<input type='RADIO' name='wtip' value=1 onclick='enter()'> Presente ";
				if(isset($wdel) and $wdel != "" and $wdel !="NO" and !isset($wnew))
				{
					echo "<input type='RADIO' name='wtip' value=2 checked onclick='enter()'> Delega En : ";
					echo "<input type='TEXT' name='wdel' size=12 maxlength=12 value='".$wdel."'></td></tr>";
				}
				else
				{
					echo "<input type='RADIO' name='wtip' value=2 onclick='enter()'> Delega En : ";
					echo "<input type='TEXT' name='wdel' size=12 maxlength=12></td></tr>";
				}				
			}
			else
			{
				?>
				<script>
					function ira(){document.proceso.wdel.focus();}
				</script>
				<?php
				$query = "SELECT Movdel, Movval from ".$empresa."_000005  where Movemp='".$wnit."' and Movano=".$wano." and Movmes=".$wmes." and Movcac='".$wacc."' and Movcpa='PR' ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					$row = mysql_fetch_array($err);
					$wxdel=$row[0];
					$wxval=$row[1];
					//ojo
					$wdel=$wxdel;
					$query = "SELECT  Acccod, Accnom, Accvap from ".$empresa."_000001  where Acccod='".$wacc."' and Accemp='".$wnit."' and Accact='on'";
					$err1 = mysql_query($query,$conex);
					$row1 = mysql_fetch_array($err1);
					$wacc=$row1[0];
					$waccn=$row1[1];
					$wacca=$row1[2];
					
					
					$query  = "select count(*),sum(Accvap) from asamblea_000005,asamblea_000001 ";
					$query .= " where movemp='".$wnit."' ";
					$query .= "   and movano=".$wano; 
					$query .= "   and movmes=".$wmes; 
					$query .= "   and movdel = '".$wacc."' "; 
					$query .= "   and movcpa='PR' ";
					$query .= "   and Movcac = acccod ";
					$query .= "   and accemp ='".$wnit."' ";
					$err1 = mysql_query($query,$conex);
					$row1 = mysql_fetch_array($err1);
					
					// $query_del  = "select Accnom from asamblea_000005,asamblea_000001 ";
					// $query_del .= " where movemp='".$wnit."' ";
					// $query_del .= "   and movano=".$wano; 
					// $query_del .= "   and movmes=".$wmes; 
					// $query_del .= "   and movdel = '".$wacc."' "; 
					// $query_del .= "   and movcpa='PR' ";
					// $query_del .= "   and Movcac = acccod ";
					// $query_del .= "   and accemp ='".$wnit."' ";
					// $err_del = mysql_query($query_del,$conex);
					// $num_del = mysql_num_rows($err_del);					
			
					
					// while($row_del = mysql_fetch_array($err_del)){
						
						// $delegados .= $row_del['Accnom']."<br>";
						
						
					// }
					
					$wnperson=$row1[0];
					$wnaccion=$row1[1];
					if($wnaccion == "")
						$wnaccion=0;
					echo "<tr><td bgcolor=".$color8." align=center colspan=3>EMPRESA : <b>".$wnnit."</b></td></tr>";
					echo "<tr><td bgcolor=".$color." >ACCIONISTA/COPROPIETARIO  : </td><td bgcolor=".$color." align=center colspan=2><input type='TEXT' name='wacc' size=12 maxlength=12 value='".$wacc."'> Buscar<input type='RADIO' name='wenter' value='00' onclick='enter()'> </td></tr>";
					echo "<tr><td bgcolor=".$color8." colspan=3 align=center> ACCI. / COPROP. : <b>".$wacc." ".$waccn." Acci./Porc. : ".$wacca."<br> Nro. Personas Que Representa : ".$wnperson."&nbsp;&nbsp;&nbsp;Nro. Acciones Que Representa : ".$wnaccion."</b></td></tr>";	
					
					if($num_del > 0){
					
					echo "<tr><td><u>Accionistas Delegados:</u> <br>{$delegados}</td></tr>";	
					
					}
					
					if($row[0] != "NO")
					{
						$query = "SELECT  Acccod, Accnom, Accvap from ".$empresa."_000001  where Acccod='".$row[0]."' and Accemp='".$wnit."' and Accact='on'";
						$err1 = mysql_query($query,$conex);
						$row1 = mysql_fetch_array($err1);
						$wdcc=$row1[0];
						$wdccn=$row1[1];
						$wdcca=$row1[2];
						//echo "<tr><td bgcolor=".$color." >MARCAR : <input type='checkbox' name='wmarc' checked></td>";
						$wmarc=1;
						echo "<input type='HIDDEN' name= 'wmarc' value='".$wmarc."'>";
						echo "<td bgcolor=".$color." colspan=3 align=center>";
						echo "<input type='RADIO' name='wtip' value=0 onclick='enter()'> Ausente ";
						echo "<input type='RADIO' name='wtip' value=1 onclick='enter()'> Presente ";
						echo "<input type='RADIO' name='wtip' value=2 checked onclick='enter()'> Delega En : ";
						if(isset($wdel) and $wdel != "" and $wdel !="NO" and !isset($wnew))
							echo "<input type='TEXT' name='wdel' size=12 maxlength=12 value='".$wdel."'></td></tr>";
						else
							echo "<input type='TEXT' name='wdel' size=12 maxlength=12 value='".$wdcc."'></td></tr>";
						echo "<tr><td bgcolor=".$color8." colspan=3>REPRESENTADO POR : <b>".$wdcc." ".$wdccn." Acci./Porc. : ".$wdcca."</b></td></tr>";
						
					}
					else
					{
						//echo "<tr><td bgcolor=".$color." >MARCAR : <input type='checkbox' name='wmarc' checked></td>";
						$wmarc=1;
						echo "<input type='HIDDEN' name= 'wmarc' value='".$wmarc."'>";
						echo "<td bgcolor=".$color." colspan=3 align=center>";
						if($wxval == "S")
						{
							echo "<input type='RADIO' name='wtip' value=0 onclick='enter()'> Ausente ";
							echo "<input type='RADIO' name='wtip' value=1 checked onclick='enter()'> Presente ";
							echo "<input type='RADIO' name='wtip' value=2 onclick='enter()'> Delega En : ";
						}
						else
						{
							echo "<input type='RADIO' name='wtip' value=0 checked onclick='enter()'> Ausente ";
							echo "<input type='RADIO' name='wtip' value=1 onclick='enter()'> Presente ";
							echo "<input type='RADIO' name='wtip' value=2 onclick='enter()'> Delega En : ";
						}
						if(isset($wdel) and $wdel != "" and $wdel !="NO" and !isset($wnew))
							echo "<input type='TEXT' name='wdel' size=12 maxlength=12 value='".$wdel."'></td></tr>";
						else
							echo "<input type='TEXT' name='wdel' size=12 maxlength=12></td></tr>";	
					}
					$wvalpar=array();
					$query = "SELECT Vpacpa, Vpacod, Vpades from ".$empresa."_000006  where Vpaemp='".$wnit."' and Vpaact='on' ";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					if($num > 0)
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							$wvalpar[(integer)$row[0]][(integer)$row[1]]=$row[2];
						}
					$query = "SELECT Parcod, Pardes, Parval from ".$empresa."_000002  where Paremp='".$wnit."' and Paract='on' and Parcie='off'";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);					
					if($num > 0)
					{
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							$wsw=$row[0];
							$opt=explode("-",$row[2]);
							$n=count($opt);
							$query = "SELECT Movval, Movdel from ".$empresa."_000005  where Movemp='".$wnit."' and Movano=".$wano." and Movmes=".$wmes." and Movcac='".$wacc."' and Movcpa='".$row[0]."' ";
							$err1 = mysql_query($query,$conex);
							$num1 = mysql_num_rows($err1);
							if($num1 > 0)
							{
								$row1 = mysql_fetch_array($err1);
								$wval=$row1[0];
							}
							else
								$wval=0;
							echo "<tr><td bgcolor=".$color."><input type='RADIO' name='wop' value='".$wsw."' checked  readonly='readonly' >".$row[1].": </td><td bgcolor=".$color." colspan=2>";
							for ($j=0;$j<$n;$j++)
							{
								if(!isset($wvalpar[(integer)$wsw][(integer)$opt[$j]]))
									$wvaldes="OPCION : ".$opt[$j];
								else
									$wvaldes=$wvalpar[(integer)$wsw][(integer)$opt[$j]];
								if($wval == $opt[$j] or isset($ultimaop) and $ultimaop == $opt[$j])
									echo "<input type='RADIO' name='wrad[".$wsw."]' value=".$opt[$j]." checked onclick='enter()'>".$wvaldes."<BR>";
								else
									echo "<input type='RADIO' name='wrad[".$wsw."]' value=".$opt[$j]."  onclick='enter()'>".$wvaldes."<BR>";
							}
							echo "</td></tr>";
							/*
							$query = "SELECT  Acccod, Accnom, Accvap from ".$empresa."_000001  where Acccod='".$row1[1]."' and Accemp='".$wnit."' and Accact='on'";
							$err1 = mysql_query($query,$conex);
							$num1 = mysql_num_rows($err1);
							if($num1 > 0)
							{
								$row1 = mysql_fetch_array($err1);
								$wdcc=$row1[0];
								$wdccn=$row1[1];
								$wdcca=$row1[2];
								echo "<tr><td bgcolor=".$color8." colspan=3>REPRESENTADO POR : <b>".$wdcc." ".$wdccn." Acci./Porc. : ".$wdcca."</b></td></tr>";
							}*/
						}
						echo "</table>";						
					}
					
				}
				else
				{
					echo "<tr><td bgcolor=".$color8." align=center colspan=3>EMPRESA : <b>".$wnnit."</b></td></tr>";
					echo "<tr><td bgcolor=".$color." align=center >ACCIONISTA/COPROPIETARIO  : </td><td bgcolor=".$color." align=center colspan=2><input type='TEXT' name='wacc' size=12 maxlength=12 value='".$wacc."'> Buscar<input type='RADIO' name='wenter' value='00' onclick='enter()'></td></tr>";	
					//echo "<tr><td bgcolor=".$color." >MARCAR : <input type='checkbox' name='wmarc' checked></td>";
					$wmarc=1;
					echo "<input type='HIDDEN' name= 'wmarc' value='".$wmarc."'>";
					echo "<td bgcolor=".$color." colspan=3 align=center>";
					if (isset($wtip))
					{
						switch ($wtip)
						{
							case 0:
								echo "<input type='RADIO' name='wtip' value=0 checked onclick='enter()'> Ausente ";
								echo "<input type='RADIO' name='wtip' value=1 onclick='enter()'> Presente ";
								echo "<input type='RADIO' name='wtip' value=2 onclick='enter()'> Delega En : ";
							break;
							case 1:
								echo "<input type='RADIO' name='wtip' value=0 onclick='enter()'> Ausente ";
								echo "<input type='RADIO' name='wtip' value=1 checked onclick='enter()'> Presente ";
								echo "<input type='RADIO' name='wtip' value=2 onclick='enter()'> Delega En : ";
							break;
							case 2:
								echo "<input type='RADIO' name='wtip' value=0 onclick='enter()'> Ausente ";
								echo "<input type='RADIO' name='wtip' value=1 onclick='enter()'> Presente ";
								echo "<input type='RADIO' name='wtip' value=2 checked onclick='enter()'> Delega En : ";
							break;
							default:
								echo "<input type='RADIO' name='wtip' value=0 onclick='enter()'> Ausente ";
								echo "<input type='RADIO' name='wtip' value=1 onclick='enter()'> Presente ";
								echo "<input type='RADIO' name='wtip' value=2 onclick='enter()'> Delega En : ";
							break;
						}
					}
					else
					{
						echo "<input type='RADIO' name='wtip' value=0 onclick='enter()'> Ausente ";
						echo "<input type='RADIO' name='wtip' value=1 onclick='enter()'> Presente ";
						echo "<input type='RADIO' name='wtip' value=2 onclick='enter()'> Delega : ";
					}
					if(isset($wdel) and $wdel != "" and $wdel !="NO" and !isset($wnew))
					{
						echo "<input type='TEXT' name='wdel' size=12 maxlength=12 value='".$wdel."'></td></tr>";
					}
					else
						echo "<input type='TEXT' name='wdel' size=12 maxlength=12 ></td></tr>";	
				}
			}			
		}
		else
		{
			echo "<center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
			echo "<font size=3><div align=center BGCOLOR=#00ffff LOOP=-1>ERROR !!! EMPRESA SIN PERIODOS ACTIVOS O CON MAS DE UN PERIODO ACTIVADO!!!!</div></FONT>";
			echo "<br><br>";
		}
	}
	else
	{
		echo "<center><table border=0 aling=center>";
		echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
		echo "<font size=3><div align=center BGCOLOR=#00ffff LOOP=-1>ERROR !!! NO HAY UNA EMPRESA ACTIVADA O HAY MAS DE UNA!!!!</div></FONT>";
		echo "<br><br>";
	}
	
}
?>
</body>
</html>
