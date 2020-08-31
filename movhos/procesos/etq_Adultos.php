 <html>
<head>
  <title>MATRIX</title>
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
  	

	

  	echo "<form action='etq_Adultos.php' method=post>";
  	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
  	if(!isset($wced) or !isset($wcod))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>STIKERS DE PACIENTES ADULTOS</td></tr>";
		echo "<tr><td align=center colspan=2>GENERACION DE STIKERS DE CODIGOS DE BARRAS</td></tr>";
		echo "<tr><td bgcolor=#cccccc>Cedula del Paciente</td>";
		echo "<td bgcolor=#cccccc><input type='TEXT' name='wced' size=12 maxlength=12></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Impresora</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Codigo, Descripcion  from ".$empresa."_000037 order by Codigo";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wcod'>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}
			echo "</select>";
		}
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$conex_o = odbc_connect('facturacion','','') or die("No se ralizo Conexion");
		$query = "Select pacres, pactar from inpac ";
		$query .= "  Where pacced = '".$wced."'";
		$err_o = odbc_do($conex_o,$query);
		$campos= odbc_num_fields($err_o);
		if (odbc_fetch_row($err_o))
		{
			$row_o=array();
			for($i=1;$i<=$campos;$i++)
			{
				$row_o[$i-1]=odbc_result($err_o,$i);
			}
		}
		else
		{
			$row_o[0]="SIN REGISTRO EN ADMISIONES";
			$row_o[1]="00";
		}
		//                  0        1      2        3       4      5        6       7      8
		$query  = "select Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Orihis, Oriing  from root_000036, root_000037 ";
		$query .= "   where Pacced = '".$wced."'";
		$query .= "     and Pacced = Oriced ";
		$query .= "     and Oriori = '01' ";
		$err = mysql_query($query,$conex);
		$row = mysql_fetch_array($err);
		$longC=strlen($wced);
		$longH=strlen($row[7]);
		$nombre=$row[1]." ".$row[2]." ".$row[3]." ".$row[4];
		if($row[6] == "M")
			$sexo = "MASCULINO";
		else
			$sexo = "FEMENINO";
		$paquete="";
		$wetq="1";
		$query  = "select Ip  from movhos_000037 ";
		$query .= "   where Codigo = '".substr($wcod,0,strpos($wcod,"-"))."'";
		$err1 = mysql_query($query,$conex);
		$row1 = mysql_fetch_array($err1);
		$wip=$row1[0];
		$ann=(integer)substr($row[5],0,4)*360 +(integer)substr($row[5],5,2)*30 + (integer)substr($row[5],8,2);
		$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
		$weda=(integer)(($aa - $ann)/360);
		$weda=number_format((double)$weda,0,'.','');
		$paquete=$paquete."N".chr(13).chr(10);
		$paquete=$paquete."FK".chr(34)."INGPAC".chr(34).chr(13).chr(10);
		$paquete=$paquete."FS".chr(34)."INGPAC".chr(34).chr(13).chr(10);; 
		$paquete=$paquete."V00,".$longC.",L,".chr(34)."CEDULA".chr(34).chr(13).chr(10);
		$paquete=$paquete."V01,40,L,".chr(34)."NOMBRE".chr(34).chr(13).chr(10);
		$paquete=$paquete."V02,40,L,".chr(34)."RESPONSABLE".chr(34).chr(13).chr(10);
		$paquete=$paquete."V03,40,L,".chr(34)."FECNAC".chr(34).chr(13).chr(10);
		$paquete=$paquete."V04,40,L,".chr(34)."SEXO".chr(34).chr(13).chr(10);
		$paquete=$paquete."V05,40,L,".chr(34)."HISTORIA".chr(34).chr(13).chr(10);
		$paquete=$paquete."V06,40,L,".chr(34)."INGRESO".chr(34).chr(13).chr(10);
		$paquete=$paquete."V07,".$longH.",L,".chr(34)."HISTORIAB".chr(34).chr(13).chr(10);
		$paquete=$paquete."V08,40,L,".chr(34)."EDAD".chr(34).chr(13).chr(10);
		$paquete=$paquete."V09,3,L,".chr(34)."TARIFA".chr(34).chr(13).chr(10);
		$paquete=$paquete."q650".chr(13).chr(10);
		$paquete=$paquete."S3".chr(13).chr(10);
		$paquete=$paquete."D4".chr(13).chr(10);
		$paquete=$paquete."ZT".chr(13).chr(10);
		$paquete=$paquete."TTh:m".chr(13).chr(10);
		$paquete=$paquete."TDy2.mn.dd".chr(13).chr(10);
		$paquete=$paquete."A380,300,1,2,1,1,N,".chr(34)."IDENTIFICACION : ".$row[0].chr(34).chr(13).chr(10);
		$paquete=$paquete."B360,300,1,1,2,5,70,B,V00".chr(13).chr(10);
		$paquete=$paquete."A410,600,1,4,1,1,N,".chr(34)."CLINICA LAS AMERICAS".chr(34).chr(13).chr(10);
		$paquete=$paquete."A380,600,1,2,1,1,N,V01".chr(13).chr(10);
		$paquete=$paquete."A360,600,1,1,1,1,N,V02".chr(13).chr(10);
		$paquete=$paquete."A340,600,1,1,1,1,N,V08".chr(13).chr(10);
		$paquete=$paquete."A320,600,1,1,1,1,N,V03".chr(13).chr(10);
		$paquete=$paquete."A300,600,1,1,1,1,N,V04".chr(13).chr(10);
		$paquete=$paquete."A280,600,1,1,1,1,N,V05".chr(13).chr(10);
		$paquete=$paquete."A260,600,1,1,1,1,N,V06".chr(13).chr(10);
		$paquete=$paquete."B340,850,1,1,2,5,70,B,V09".chr(13).chr(10);
		$paquete=$paquete."A250,1040,0,2,1,1,N,".chr(34)."HISTORIA".chr(34).chr(13).chr(10);
		$paquete=$paquete."B250,1060,0,1,2,5,70,B,V07".chr(13).chr(10);
		$paquete=$paquete."FE".chr(13).chr(10);
		$paquete=$paquete.".".chr(13).chr(10);
		$paquete=$paquete."FR".chr(34)."INGPAC".chr(34).chr(13).chr(10);
		$paquete=$paquete."?".chr(13).chr(10);
		$paquete=$paquete.$wced.chr(13).chr(10);
		$paquete=$paquete.$nombre.chr(13).chr(10);
		$paquete=$paquete.$row_o[0].chr(13).chr(10);
		$paquete=$paquete."F. NAC.: ".$row[5].chr(13).chr(10);
		$paquete=$paquete."SEXO: ".$sexo.chr(13).chr(10);
		$paquete=$paquete."HISTORIA: ".$row[7].chr(13).chr(10);
		$paquete=$paquete."NRo. INGRESO: ".$row[8].chr(13).chr(10);
		$paquete=$paquete.$row[7].chr(13).chr(10);
		$paquete=$paquete."EDAD: ".$weda.chr(13).chr(10);
		$paquete=$paquete.$row_o[1].chr(13).chr(10);
		$paquete=$paquete."P".$wetq.chr(13).chr(10);
		$paquete=$paquete.".".chr(13).chr(10);
		$addr=$wip;
		echo "<center><table border=0>";
		$fp = fsockopen( $addr,9100, $errno, $errstr, 30);
		if(!$fp) 
			echo "<tr><td align=center bgcolor=#cccccc>ERROR : "."$errstr ($errno)</td></tr>";
		else 
		{
			fputs($fp,$paquete);
			#echo "PAQUETE ENVIADO $errstr ($errno)<br>\n";
			echo "<tr><td align=center bgcolor=#cccccc>PAQUETE ENVIADO OK!!!! </td></tr>";
			fclose($fp);
		}
		echo "<tr><td align=center bgcolor=#cccccc><input type='submit' value='ENTER'></td></tr></table></center>";
		sleep(5);
		
		odbc_close($conex_o);
		odbc_close_all();
	}
}
?>
</body>
</html>