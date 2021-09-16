<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
include_once("root/comun.php");
$institucion = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
$wactualiz = "1";
encabezado( "USUARIOS DEL GRUPO AMERICAS (MATRIX) NO PRESENTES EN NOMINA", $wactualiz, $institucion->baseDeDatos );
function ldif($f,$c,$p,$d)
{
	$ant = "";
	$correcto="";
	for ($n=0;$n<strlen($d);$n++)
	{
		if((ord($ant) == 32 and ord(substr($d,$n,1)) != 32 ) or ord($ant) != 32)
			$correcto .= substr($d,$n,1);
		$ant=substr($d,$n,1);
	}
	$d=ucwords(strtolower($correcto));
	$nombres = explode(" ",$d);
	$K = count($nombres);
	if((strlen($c) == 5 or strlen($c) == 7) and $K > 2)
	{
		if(strlen($c) == 5)
		{
			$cod = "1".$c;
		}
		else
		{
			$cod = "1".substr($c,2);
		}
		$nom="";
		for ($n=0;$n<$K-3;$n++)
		{
			if(ord(substr($nombres[$n],0,1)) != 0)
			$nom .= $nombres[$n]." ";
		}
		$nom .= $nombres[$K-3];
		$ape = $nombres[$K-2]." ".$nombres[$K-1];
		$registro="dn: cn=".$d.",ou=users,dc=lasamericas,dc=com,dc=co".chr(13).chr(10);
		fwrite ($f,$registro);
		$registro="givenName: ".$nom.chr(13).chr(10);
		fwrite ($f,$registro);
		$registro="sn: ".$ape.chr(13).chr(10);
		fwrite ($f,$registro);
		$registro="cn:".$d.chr(13).chr(10);
		fwrite ($f,$registro);
		$registro="uid: ".$cod.chr(13).chr(10);
		fwrite ($f,$registro);
		$registro="uidNumber: ".$cod.chr(13).chr(10);
		fwrite ($f,$registro);
		$registro="sambaSID: S-1-5-21-3704606595-612890695-93358287-".$cod.chr(13).chr(10);
		fwrite ($f,$registro);
		$registro="userPassword:: ";
		fwrite ($f,$registro);
		$base64 = stream_filter_append($f,"convert.base64-encode");
		$registro=$p;
		fwrite ($f,$registro);
		stream_filter_remove($base64);
		$registro=chr(13).chr(10);
  		fwrite ($f,$registro);
		$registro="gidNumber: 20001".chr(13).chr(10);
		fwrite ($f,$registro);
		$registro="sambaPrimaryGroupSID: S-1-5-21-3704606595-612890695-93358287-513-".$cod.chr(13).chr(10);
		fwrite ($f,$registro);
		$registro="homeDirectory: /home/users/".$cod.chr(13).chr(10);
		fwrite ($f,$registro);
		$registro="sambaAcctFlags: [U]".chr(13).chr(10);
		fwrite ($f,$registro);
		$registro="objectClass: inetOrgPerson".chr(13).chr(10);
		fwrite ($f,$registro);
		$registro="objectClass: sambaSamAccount".chr(13).chr(10);
		fwrite ($f,$registro);
		$registro="objectClass: posixAccount".chr(13).chr(10);
		fwrite ($f,$registro);
		$registro="objectClass: top".chr(13).chr(10);
		fwrite ($f,$registro);
		$registro="structuralObjectClass: inetOrgPerson".chr(13).chr(10);
		fwrite ($f,$registro);
		$registro="creatorsName: cn=admin,dc=lasamericas,dc=com,dc=co".chr(13).chr(10);
		fwrite ($f,$registro);
		$registro="modifiersName: cn=admin,dc=lasamericas,dc=com,dc=co".chr(13).chr(10);
		fwrite ($f,$registro);
		$registro=" ".chr(13).chr(10);
  		fwrite ($f,$registro);
	}
}
function bi($d,$n,$k,$i)
{
	$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			if(strtoupper($k) == strtoupper($d[$lm][$i]))
				return $lm;
			elseif(strtoupper($k) < strtoupper($d[$lm][$i]))
						$ls=$lm;
					else
						$li=$lm;
		}
		if(strtoupper($k) == strtoupper($d[$li][$i]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls][$i]))
					return $ls;
				else
					return -1;
	}
	else
		return -1;
}
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		$conex_O = odbc_connect('nomina','','')
			or die("No se ralizo Conexion");
		echo "<form action='Usuarios.php?wemp_pmla=".$wemp_pmla."' method=post>";
		echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
		$query  = "Select percod,perap1,perap2,perno1,perno2,percco,perced,perofi,peretr From noper ";
		//$query .= "  Where peretr ='A'";
		$query .= "  Order By percod";
		$err = odbc_do($conex_O,$query);
		$campos= odbc_num_fields($err);
		$k=-1;
		$data=array();
		while (odbc_fetch_row($err))
		{
			$k=$k+1;
			for($i=1;$i<=$campos;$i++)
				$data[$k][$i-1]=odbc_result($err,$i);
		}
		echo "<table border=1>";				
		//echo "<tr><td colspan=4 align=center><font size=4>PROMOTORA MEDICA LAS AMERICAS S.A.</font></td></tr>";
		//echo "<tr><td colspan=4  align=center><font size=4>USUARIOS DEL GRUPO AMERICAS (MATRIX) NO PRESENTES EN NOMINA</font><td></tr>";
		echo "<tr><td><b>Codigo</b></td><td><b>Nombre</b></td><td><b>Centro de <br>Costos</b></td><td><b>Empresa-Estado</b></td></tr>";
		//                 0         1         2       3       4
		$query = "select codigo,descripcion,ccostos,empresa,password from usuarios ";
		$query .= "  where activo = 'A' ";
		//$query .= "  And empresa = '01' ";
		$query .= " order by empresa,codigo";
		$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error()."<br>");
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
			$datafile="../../planos/Ldif.txt";
			$file = fopen($datafile,"w+");
			$inin=0;
			$inret=0;
			$out=0;
			$ext=0;
			$emp=array();
			$query="select Empcod, Empdes from root_000050 where Empest='on' ";		
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			for ($j=0;$j<$num1;$j++)
			{
				$row1 = mysql_fetch_array($err1);
				$emp[$j][0]=0;
				$emp[$j][1]=$row1[0];
				$emp[$j][2]=$row1[1];
			}
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				for ($j=0;$j<$num1;$j++)
					if($emp[$j][1] == $row[3])
						$emp[$j][0]++;
				if ($row[2] == "")
					$row[2] = "SIN ASIGNACION";
				if(strlen($row[0]) > 5)
					$ncod=substr($row[0],2);
				else
					$ncod=$row[0];
				if($row[3] == "01")
				{
					$j=bi($data,$k+1,$ncod,0);
					if($j == -1)
					{
						$out++;
						echo "<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td></tr>";
					}
					else
					{
						if($data[$j][$campos-1] == "A")
						{
							$inin++;
							ldif($file,$row[0],$row[4],$row[1]);
						}
						else
							$inret++;
					}
				}
				else
				{
					$ext++;
					echo "<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td></tr>";
				}
			}
			fclose ($file);
	   		$ruta="..\..\planos";
		}
		$tot = $inin + $inret + $out + $ext;
		for ($j=0;$j<$num1;$j++)
		{
			echo "<tr><td colspan=3>".$emp[$j][1]."-".$emp[$j][2]."</td><td>".number_format($emp[$j][0],0,".",",")."</td></tr>";
		}
		echo "<tr><td colspan=3>EN NOMINA ACTIVOS</td><td>".number_format($inin,0,".",",")."</td></tr>";
		echo "<tr><td colspan=3>EN NOMINA INACTIVOS</td><td>".number_format($inret,0,".",",")."</td></tr>";
		echo "<tr><td colspan=3>FUERA DE NOMINA</td><td>".number_format($out,0,".",",")."</td></tr>";
		echo "<tr><td colspan=3>EXTERNO</td><td>".number_format($ext,0,".",",")."</td></tr>";
		echo "<tr><td colspan=3>TOTAL USUARIOS</td><td>".number_format($tot,0,".",",")."</td></tr>";
		echo "<tr><td bgcolor=#dddddd  colspan=4 align=center><b><A href=".$ruta.">Haga Click Para Bajar el Archivo</A></b></td></tr>";
		echo "</table>";
	}
?>
</body>
</html>