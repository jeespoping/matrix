<html>
<head>
  <title>MATRIX - CANCELACION DEL TURNO QUIRURGICO</title>
  <style type="text/css">

    	#tipoG00{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipoG01{color:#000066;background:#99CCFF;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:left;height:2em;}
    	#tipoG02{color:#000066;background:#99CCFF;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:left;height:2em;}
    	#tipoG03{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:left;height:2em;}
    	#tipoG04{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:left;height:2em;}
    	#tipoM01{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:center;height:2em;}
    	#tipoM02{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:center;height:2em;}
    	#tipoM03{color:#000066;background:#dddddd;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;height:2em;}
    	#tipoM04{color:#000066;background:#dddddd;font-size:10pt;font-family:Tahoma;font-weight:normal;text-align:center;height:2em;}

    </style>
</head>
<body onLoad="Cerrar();" BGCOLOR="#FFFFFF">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.CancelTur.submit();
	}
//-->
</script>

<?php
include_once("conex.php");
function ver($chain)
{
	if(strpos($chain,"-") === false)
		return $chain;
	else
		return substr($chain,0,strpos($chain,"-"));
}

// FUNCION DE CANCELACION DE TURNOS
function MCA_TUR($key,$conex,$wnci,$wcac,&$werr,&$e,$wpcan)
{
	if(substr($wcac,0,strpos($wcac,"-")) != 0 and strlen($wpcan) > 0)
	{
		global $empresa;
		//                 0       1        2       3       4      5       6       7       8       9        10     11      12      13      14      15     16       17      18      19      20      21      22      23      24      25      26      27      28      29      30      31      32      33      34      35      36      37      38      39      40      41
		$query = "select Turtur, Turqui, Turhin, Turhfi, Turfec, Turndt, Turdoc, Turhis, Turnin, Turnom, Turfna, Tursex, Turins, Turtcx, Turtip, Turtan, Tureps, Turuci, Turbio, Turinf, Turmat, Turmok, Turban, Turbok, Turpre, Turpan, Turpes, Turpep, Turpeq, Turper, Turpea, Turubi, Turmdo, Turtel, Turord, Turcom, Turcir, Turmed, Turequ, Turusg, Turusm, Turest from  ".$empresa."_000011 where Turtur=".$wnci;
		$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO DE TURNOS : ".mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
			$row = mysql_fetch_array($err);
			$wnci = $row[0];
			$wqui = $row[1];
			$whin = $row[2];
			$whfi = $row[3];
			$wfec = $row[4];
			$wndt = $row[5];
			$wdoc = $row[6];
			$whis = $row[7];
			$wnin = $row[8];
			$wnom = $row[9];
			$wfna = $row[10];
			$wsex = $row[11];
			$wins = $row[12];
			$wtci = $row[13];
			$wtip = $row[14];
			$wtan = $row[15];
			$weps = $row[16];
			$wuci = $row[17];
			$wbio = $row[18];
			$winf = $row[19];
			$wmat = $row[20];
			$wmok = $row[21];
			$wban = $row[22];
			$wbok = $row[23];
			$wpre = $row[24];
			$wpan = $row[25];
			$wpes = $row[26];
			$wpep = $row[27];
			$wpeq = $row[28];
			$wper = $row[29];
			$wpea = $row[30];
			$wubi = $row[31];
			$wmdo = $row[32];
			$wtel = $row[33];
			$word = $row[34];
			$wcom = $row[35];
			$wturc = $row[36];
			$wturm = $row[37];
			$wture = $row[38];
			$wcac = substr($wcac,0,strpos($wcac,"-"));
			$west = "off";

			$query = "SELECT Mcicod  from ".$empresa."_000008 where Mcitur=".$wnci;
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$wcom .= "*".$row[0];
				}
			}
			$query = "SELECT Mmemed  from ".$empresa."_000010 where Mmetur=".$wnci;
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$wcom .= "*".$row[0];
				}
			}
			$query = "SELECT Meqequ from ".$empresa."_000009 where Meqtur=".$wnci;
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$wcom .= "*".$row[0];
				}
			}
			$wcom=$wcoma.chr(10).chr(13).date("Y-m-d H:i")." |Cancelado x : ".$key."|".$wcom;
			$query =  "DELETE  from ".$empresa."_000011 where Turtur=".$wnci;
			$err1 = mysql_query($query,$conex) or die("ERROR BORRANDO TURNOS : ".mysql_errno().":".mysql_error());
			$query =  "DELETE  from ".$empresa."_000008 where Mcitur=".$wnci;
			$err1 = mysql_query($query,$conex) or die("ERROR BORRANDO CIRUGIAS : ".mysql_errno().":".mysql_error());
			$query =  "DELETE  from ".$empresa."_000010 where Mmetur=".$wnci;
			$err1 = mysql_query($query,$conex) or die("ERROR BORRANDO MEDICOS : ".mysql_errno().":".mysql_error());
			$query =  "DELETE  from ".$empresa."_000009 where Meqtur=".$wnci;
			$err1 = mysql_query($query,$conex) or die("ERROR BORRANDO EQUIPOS : ".mysql_errno().":".mysql_error());
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$query = "insert ".$empresa."_000007 (medico,fecha_data,hora_data, Mcatur, Mcaqui, Mcahin, Mcahfi, Mcafec, Mcandt, Mcadoc, Mcahis, Mcanin, Mcanom, Mcafna, Mcasex, Mcains, Mcatcx, Mcatip, Mcatan, Mcaeps, Mcauci, Mcabio, Mcainf, Mcamat, Mcamok, Mcaban, Mcabok, Mcapre, Mcapan, Mcapes, Mcapep, Mcapeq, Mcaper, Mcapea, Mcaubi, Mcamdo, Mcatel, Mcaord, Mcacom, Mcacir, Mcamed, Mcaequ, Mcacau, Mcapca, Mcausg, Mcausm, Mcaest, Seguridad) values ('";
			$query .=  $empresa."','";
			$query .=  $fecha."','";
			$query .=  $hora."',";
			$query .=  $wnci.",'";
			$query .=  $wqui."','";
			$query .=  $whin."','";
			$query .=  $whfi."','";
			$query .=  $wfec."','";
			$query .=  $wndt."','";
			$query .=  $wdoc."','";
			$query .=  $whis."',";
			$query .=  $wnin.",'";
			$query .=  $wnom."','";
			$query .=  $wfna."','";
			$query .=  $wsex."','";
			$query .=  $wins."','";
			$query .=  $wtci."','";
			$query .=  $wtip."','";
			$query .=  $wtan."','";
			$query .=  $weps."','";
			$query .=  $wuci."','";
			$query .=  $wbio."','";
			$query .=  $winf."','";
			$query .=  $wmat."','";
			$query .=  $wmok."','";
			$query .=  $wban."','";
			$query .=  $wbok."','";
			$query .=  $wpre."','";
			$query .=  $wpan."','";
			$query .=  $wpes."','";
			$query .=  $wpep."','";
			$query .=  $wpeq."','";
			$query .=  $wper."','";
			$query .=  $wpea."','";
			$query .=  $wubi."','";
			$query .=  $wmdo."','";
			$query .=  $wtel."','";
			$query .=  $word."','";
			$query .=  $wcom."','";
			$query .=  $wturc."','";
			$query .=  $wturm."','";
			$query .=  $wture."','";
			$query .=  $wcac."','";
			$query .=  $wpcan."','";
			$query .=  $key."',";
			$query .=  "'','".$west."','C-".$empresa."')";
			$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO CANCELACION : ".mysql_errno().":".mysql_error());
			$e=$e+1;
			$werr[$e]="OK! TURNO CANCELADO";
			return true;
		}
		else
		{
			$e=$e+1;
			$werr[$e]="EL TURNO NO EXISTE NO PUEDE SER CANCELADO REVISE!!!!! ";
			return false;
		}
	}
	else
	{
		$e=$e+1;
		$werr[$e]="ERROR NO ESCOGIO CAUSA DE CANCELACION  O NO ESPECIFICO QUIEN CANCELO EL TURNO REVISE!!!!! ";
		return false;
	}
}

session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='CancelTur' action='CancelTur.php' method=post>";




	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<center><input type='HIDDEN' name= 'MENSAGE' value='".$MENSAGE."'>";
	if(isset($ok))
	{
		$werr=array();
		$e=-1;
		$color4="#CC99FF";
		$color5="#99CCFF";
		echo "<br><br><center><table border=0 aling=center>";
		if(MCA_TUR($key,$conex,$MENSAGE,$wcac,$werr,$e,$wpcan))
			echo "<tr><td align=center id=tipoM04>PROCESO EXITOSO</td></tr>";
		else
			echo "<tr><td align=center id=tipoM04>PROCESO FALLIDO</td></tr>";
		echo "</table><br><br></center>";
		if(isset($werr) and isset($e) and $e > -1)
		{
			echo "<br><br><center><table border=0 aling=center id=tipo2>";
			echo "<tr><td align=center colspan=2><IMG SRC='/matrix/images/medical/TCX/logo_".$empresa.".png'></td></tr>";
			for ($i=0;$i<=$e;$i++)
				if(substr($werr[$i],0,3) == "OK!")
					echo "<tr><td align=center bgcolor=".$color5."><IMG SRC='/matrix/images/medical/root/feliz.png'>&nbsp&nbsp<font color=#000000 face='tahoma'></font></TD><TD bgcolor=".$color5."><font color=#000000 face='tahoma'><b>".$werr[$i]."</b></font></td></tr>";
				else
					echo "<tr><td align=center bgcolor=".$color4."><IMG SRC='/matrix/images/medical/root/Malo.png'>&nbsp&nbsp<font color=#000000 face='tahoma'></font></TD><TD bgcolor=".$color4."><font color=#000000 face='tahoma'><b>".$werr[$i]."</b></font></td></tr>";
			echo "</table><br><br></center>";
		}
	}
	else
	{
		//                 0      1       2         3       4      5      6        7       8       9       10      11     12       13     14       15     16      17       18     19      20      21      22      23       24      25      26
		$query = "SELECT Turtur, Turqui, Turhin, Turhfi, Turest, Turord, Turcir, Turtcx, Turtip, Turnom, Turfna, Turtel, Turtcx, Turtip, Turtan, Tureps, Turmed, Turequ, Turuci, Turbio, Turinf, Turmat, Turban, Turins, Entdes, Turpre, Turmdo  from ".$empresa."_000011, ".$empresa."_000003 ";
		$query .= " where Turtur = '".$MENSAGE."' ";
		$query .= "   and Tureps = Entcod ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<center><table border=0 align=center id=tipoG00>";
			echo "<tr><td align=center colspan=2><IMG SRC='/matrix/images/medical/TCX/logo_".$empresa.".png'></td></tr>";
			echo "<tr><td id=tipoM03 colspan=2>CANCELACION DEL TURNO QUIRURGICO</td></tr>";
			echo "<tr><td id=tipoM01>ITEM</td><td id=tipoM02>DESCRIPCION</td></tr>";
			$row = mysql_fetch_array($err);
			echo "<tr><td id=tipoG01>NOMBRE</td><td id=tipoG02>".$row[9]."</td></tr>";
			echo "<tr><td id=tipoG03>FECHA DE NACIMIENTO</td><td id=tipoG04>".$row[10]."</td></tr>";
			echo "<tr><td id=tipoG01>TELEFONO</td><td id=tipoG02>".$row[11]."</td></tr>";
			echo "<tr><td id=tipoG03>CIRUGIA</td><td id=tipoG04>".$row[6]."</td></tr>";
			echo "<tr><td id=tipoG01>MEDICOS</td><td id=tipoG02>".$row[16]."</td></tr>";
			echo "<tr><td id=tipoG03>EQUIPOS</td><td id=tipoG04>".$row[17]."</td></tr>";
			echo "<tr><td id=tipoG01>RESPONSABLE</td><td id=tipoG02>".$row[24]."</td></tr>";
			echo "<td bgcolor=#cccccc align=center colspan=2><b>CANCELACION DE TURNO</b><input type='RADIO' name=ok disabled value=6 onclick='enter()'><br>";
			echo "<input type='TEXT' name='wcacw' size=10 maxlength=30 OnBlur='enter()' class=tipo3> - ";
			echo "<select name='wcac' id=tipo1>";
			if(isset($wcacw) and $wcacw != "")
			{
				$query = "SELECT Cancod, Candes from ".$empresa."_000001 where Cancod = '".$wcacw."' and Canest='on' order by Candes";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num == 0)
				{
					$query = "SELECT Cancod, Candes from ".$empresa."_000001 where Candes like '%".$wcacw."%' and Canest='on' order by Candes";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
				}
				if ($num>0)
				{
					echo "<option>SELECCIONE</option>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$wcac=ver($wcac);
						if($wcac == $row[0])
							echo "<option>".$row[0]."-".$row[1]."</option>";
						else
							echo "<option>".$row[0]."-".$row[1]."</option>";
					}
				}
				else
					echo "<option>0-NO APLICA</option>";
			}
			else
			{
				if(isset($wcac))
				{
					$wcac=ver($wcac);
					$query = "SELECT Cancod, Candes  from ".$empresa."_000001 where Cancod = '".$wcac."' and Canest='on' order by Candes";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					if ($num>0)
					{
						$row = mysql_fetch_array($err);
						echo "<option>".$row[0]."-".$row[1]."</option>";
					}
					else
						echo "<option>0-NO APLICA</option>";
				}
				else
					echo "<option>0-NO APLICA</option>";
			}
			echo "</select>";
			if(isset($wpcan) and strlen($wpcan) > 0)
				echo "<br>PERSONA QUE CANCELA :&nbsp;<input type='TEXT' name='wpcan' size=30 maxlength=30 value='".$wpcan."'  class=tipo3></td></tr>";
			else
				echo "<br>PERSONA QUE CANCELA :&nbsp;<input type='TEXT' name='wpcan' size=30 maxlength=30  class=tipo3></td></tr>";
			echo "<tr><td id=tipoM03 colspan=2><IMG SRC='/matrix/images/medical/TCX/nak.jpg' style='vertical-align:middle;'>&nbsp;&nbsp;<input type='checkbox' name='ok' OnClick='enter()'></td>";
			echo "</table></center>";
		}
		else
			echo "LA IDENTIFICACION DEL TURNO NO EXISTE EN LA BASE DE DATOS<BR> CONSULTE CON SISTEMAS";
	}
}
?>
</body>
</html>