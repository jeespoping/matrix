<html>
<head>
  	<title>MATRIX Generacion Automatica de Kardex</title>
  	
  	 <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
      	.titulo1{color:#FFFFFF;background:#006699;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo2{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.titulo3{color:#003366;background:#A4E1E8;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto1{color:#003366;background:#FFDBA8;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}	
    	.texto2{color:#003366;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto3{color:#003366;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto4{color:#003366;background:#f5f5dc;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto6{color:#FFFFFF;background:#006699;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
      	.texto5{color:#003366;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
   </style>
  	 
</head>
<body>
<center>
<table border=0 align=center>
<tr><td class="titulo1"><font size=5>Generacion Automatica de Kardex</font></tr></td>
<tr><td class="texto5"><b>tkardex.php Ver. 2007-01-09</b></tr></td></table>
</center> 
<?php
include_once("conex.php");
include_once("root/comun.php");
//se convierte en la variable empresa ya que $empresa=cenpro
$empresa = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cenmez" );
$bdMovhos  = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

function calcularProducto($cantidad, $lote, &$vector, $concepto, $tipo)
{
	global $conex;
	global $empresa;

	$query = "SELECT Mdeart, Mdecan, Mdepre from ".$empresa."_000007 ";
	$query .= " where  Mdecon='".$concepto."'";
	$query .= "   and  Mdenlo='".$lote."'";

	$err2 = mysql_query($query,$conex);
	$num2 = mysql_num_rows($err2);

	for ($i=0; $i<$num2; $i++)
	{
		$row2 = mysql_fetch_array($err2);

		$exp=explode('-',$lote);
		$query = "SELECT plocin from ".$empresa."_000004 ";
		$query .= " where  plopro='".$exp[1]."' and plocod='".$exp[0]."' ";
		$errp = mysql_query($query,$conex);
		$nump = mysql_num_rows($errp);
		$rowp = mysql_fetch_array($errp);

		if($row2[2]!='')
		{
			$puesto=bi($vector,count($vector),$row2[2],1);
			if ($tipo==1)
			{
				$vector[$puesto]['entradas']=$vector[$puesto]['entradas']+$row2[1]*$cantidad/$rowp[0];
			}
			else
			{
				$vector[$puesto]['salidas']=$vector[$puesto]['salidas']+$row2[1]*$cantidad/$rowp[0];
			}
		}
		else
		{
			$res=calcularProducto($row2[1]*$cantidad/$rowp[0],$row2[0], $vector, $concepto, $tipo);

		}
	}

	if ($i>0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function comparacion($vec1,$vec2)
{
	if($vec1[8] > $vec2[8])
	return 1;
	elseif ($vec1[8] < $vec2[8])
	return -1;
	else
	return 0;
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

///////////////////////////////////////////////////////PROGRAMA/////////////////////////////////////////
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='tkardex' action='tkardex.php?wemp_pmla=".$wemp_pmla."' method=post>";
	

	

	//echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
	if(!isset($wano) or !isset($wmes))
	{
		$wlinant="";
		echo "<input type='HIDDEN' name= 'wlinant' value='".$wlinant."'>";
		echo "<center><table border=0>";
		echo "<tr><td class='texto4'>Año de Proceso</td>";
		echo "<td class='texto4'><input type='TEXT' name='wano' size=4 maxlength=4></td></tr>";
		echo "<tr><td class='texto4'>Mes de Proceso</td>";
		echo "<td class='texto4'><input type='TEXT' name='wmes' size=2 maxlength=2></td></tr>";
		echo "<tr><td class='texto4' colspan=2 ><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$query = "delete  from ".$empresa."_000011 ";
		$query .= "  where Kxmano = ".$wano;
		$query .= "       and Kxmmes = ".$wmes;
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$k=0;
		if($wmes == 1)
		{
			$wmesa = 12;
			$wanoa = $wano - 1;
		}
		else
		{
			$wmesa = $wmes -1;
			$wanoa = $wano;
		}
		echo "<table border=0 align=center>";
		echo "<tr><td class='texto4'><font face='tahoma'><b>AÑO DE PROCESO : </b>".$wano."</td></tr>";
		echo "<tr><td class='texto4'><font face='tahoma'><b>MES DE PROCESO : </b>".$wmes."</td></tr></table><br><br>";
		$dsan=array();
		//consultamos en la tabla de saldos de inventarios, el saldo inicial
		$query = "SELECT  Salcco, Salcod, Salexi, Salvuc,  Artcom, Artuni, Unides, Salpro from ".$empresa."_000014 , ".$bdMovhos."_000026, ".$bdMovhos."_000027  ";
		$query .= " where  Salano=".$wanoa;
		$query .= "     and   Salmes= ".$wmesa;
		$query .= "     and   Salcod =Artcod ";
		$query .= "     and   Artuni= Unicod ";
		$query .= "     ORDER BY  Salcco, Salcod ";

		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$totsan=$num;
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$d[$i][0]=$row[0];
			$d[$i][1]=$row[1];
			$d[$i][2]=$row[2];
			$d[$i][3]=$row[3];
			$d[$i][4]=$row[4];
			$d[$i][5]=$row[5].'-'.$row[6];
			$d[$i][6]=$row[7];
		}

		$dsac=array();
		//consultamos en la tabla de saldos de inventarios el saldo actual
		$query = "SELECT  Salcco, Salcod, Salexi, Salvuc, Artcom, Artuni, Unides, Salpro  from ".$empresa."_000014, ".$bdMovhos."_000026, ".$bdMovhos."_000027 ";
		$query .= " where  Salano=".$wano;
		$query .= "     and   Salmes=".$wmes;
		$query .= "     and   Salcod =Artcod ";
		$query .= "     and   Artuni= Unicod ";
		$query .= "     ORDER BY  Salcco, Salcod ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$totsac=$num;
		$j=0;
		$k=0;
		$row = mysql_fetch_array($err);
		//al mismo tiempo que se cargan se procura que los vectores sean iguales
		for ($i=0;$i<$num;$i++)
		{
			if (isset($d[$k][1]))
			{
				if(strtoupper($d[$k][1])==strtoupper($row[1]))
				{
					$dsac[$j][0]=$row[0];
					$dsac[$j][1]=$row[1];
					$dsac[$j][2]=$row[2];
					$dsac[$j][3]=$row[3];
					$dsac[$j][4]=$row[4];
					$dsac[$j][5]=$row[5].'-'.$row[6];
					$dsac[$j][6]=$row[7];

					$dsan[$j][0]=$d[$k][0];
					$dsan[$j][1]=$d[$k][1];
					$dsan[$j][2]=$d[$k][2];
					$dsan[$j][3]=$d[$k][3];
					$dsan[$j][4]=$d[$k][4];
					$dsan[$j][5]=$d[$k][5];
					$dsan[$j][6]=$d[$k][6];
					$j++;
					$k++;
					$row = mysql_fetch_array($err);
				}
				else if (isset($d[$k+1][1]))
				{
					if (strtoupper($row[1])>=strtoupper($d[$k+1][1]))
					{
						$dsac[$j][0]=$d[$k][0];
						$dsac[$j][1]=$d[$k][1];
						$dsac[$j][2]=0;
						$dsac[$j][3]=0;
						$dsac[$j][4]=$d[$k][4];
						$dsac[$j][5]=$d[$k][5];
						$dsac[$j][6]=$d[$k][6];

						$dsan[$j][0]=$d[$k][0];
						$dsan[$j][1]=$d[$k][1];
						$dsan[$j][2]=$d[$k][2];
						$dsan[$j][3]=$d[$k][3];
						$dsan[$j][4]=$d[$k][4];
						$dsan[$j][5]=$d[$k][5];
						$dsan[$j][6]=$d[$k][6];
						$j++;
						$k++;
						$i--;
					}
					else if (strtoupper($row[1])<strtoupper($d[$k+1][1]))
					{
						$dsac[$j][0]=$row[0];
						$dsac[$j][1]=$row[1];
						$dsac[$j][2]=$row[2];
						$dsac[$j][3]=$row[3];
						$dsac[$j][4]=$row[4];
						$dsac[$j][5]=$row[5].'-'.$row[6];
						$dsac[$j][6]=$row[7];

						$dsan[$j][0]=$row[0];
						$dsan[$j][1]=$row[1];
						$dsan[$j][2]=0;
						$dsan[$j][3]=0;
						$dsan[$j][4]=$row[4];
						$dsan[$j][5]=$row[5].'-'.$row[6];
						$dsan[$j][6]=$row[7];
						$j++;
						$row = mysql_fetch_array($err);
					}
				}
				else
				{
					if (strtoupper($row[1])>=strtoupper($d[$k][1]))
					{
						$dsac[$j][0]=$d[$k][0];
						$dsac[$j][1]=$d[$k][1];
						$dsac[$j][2]=0;
						$dsac[$j][3]=0;
						$dsac[$j][4]=$d[$k][4];
						$dsac[$j][5]=$d[$k][5];
						$dsac[$j][6]=$d[$k][6];

						$dsan[$j][0]=$d[$k][0];
						$dsan[$j][1]=$d[$k][1];
						$dsan[$j][2]=$d[$k][2];
						$dsan[$j][3]=$d[$k][3];
						$dsan[$j][4]=$d[$k][4];
						$dsan[$j][5]=$d[$k][5];
						$dsan[$j][6]=$d[$k][6];
						$j++;
						$k++;
						$i--;
					}
					else if (strtoupper($row[1])<strtoupper($d[$k][1]))
					{
						$dsac[$j][0]=$row[0];
						$dsac[$j][1]=$row[1];
						$dsac[$j][2]=$row[2];
						$dsac[$j][3]=$row[3];
						$dsac[$j][4]=$row[4];
						$dsac[$j][5]=$row[5].'-'.$row[6];
						$dsac[$j][6]=$row[7];

						$dsan[$j][0]=$row[0];
						$dsan[$j][1]=$row[1];
						$dsan[$j][2]=0;
						$dsan[$j][3]=0;
						$dsan[$j][4]=$row[4];
						$dsan[$j][5]=$row[5].'-'.$row[6];
						$dsan[$j][6]=$row[7];
						$j++;
						$row = mysql_fetch_array($err);
					}
				}
			}
			else
			{
				$dsac[$j][0]=$row[0];
				$dsac[$j][1]=$row[1];
				$dsac[$j][2]=$row[2];
				$dsac[$j][3]=$row[3];
				$dsac[$j][4]=$row[4];
				$dsac[$j][5]=$row[5].'-'.$row[6];
				$dsac[$j][6]=$row[7];

				$dsan[$j][0]=$row[0];
				$dsan[$j][1]=$row[1];
				$dsan[$j][2]=0;
				$dsan[$j][3]=0;
				$dsan[$j][4]=$row[4];
				$dsan[$j][5]=$row[5].'-'.$row[6];
				$dsan[$j][6]=$row[7];
				$j++;
				$row = mysql_fetch_array($err);
			}

			//echo $dsan[$j-1][1].'</br>';
			$rev=0;
			if($dsan[$j-1][6]>0)
			{
				$rev=($dsac[$j-1][3]-$dsan[$j-1][3])*$dsan[$j-1][2]/$dsan[$j-1][6];
				
			}
			// echo $rev.'</br>';
			if($rev>0)
			{
				$dsac[$j-1]['reve']=$rev;
				$dsac[$j-1]['revs']=0;
			}
			else if ($rev<0)
			{
				$dsac[$j-1]['reve']=0;
				$dsac[$j-1]['revs']=abs($rev);
			}
			else
			{
				$dsac[$j-1]['reve']=0;
				$dsac[$j-1]['revs']=0;
			}
			$dsac[$j-1]['entradas']=0;
			$dsac[$j-1]['salidas']=0;
		}

		echo "<b>Registros Totales Kardex : ".count($dsan)."</b><br><br>";

		$hora = (string)date("H:i:s");
		//echo "1er Query Tiempo 1 : ".$hora."<br>";
		//consultamos el tipo del articulo para saber si debe desglosarse en insumos o no
		$query = "CREATE TEMPORARY TABLE if not exists caro1 as SELECT Mendoc, Mencon, Conind from ".$empresa."_000006, ".$empresa."_000008  ";
		$query .= "  where  Menano=".$wano;
		$query .= "     and   Menmes=".$wmes;
		$query .= "     and   Mencon= Concod ";
		$query .= "     and   congec= 'on' ";

		$err1 = mysql_query($query,$conex);

		$query = "CREATE TEMPORARY TABLE if not exists caro2 as SELECT Mdeart, Mdepre, Mdecan, Mdenlo, Conind, Mdedoc, Mdecon from caro1, ".$empresa."_000007 ";
		$query .= "     where   Mendoc= Mdedoc ";
		$query .= "     and   Mencon= Mdecon ";
		$err2 = mysql_query($query,$conex);
		if ($err2 != 1)
		echo mysql_errno().":".mysql_error()."<br>";
		$hora = (string)date("H:i:s");
		//echo "1er Query Tiempo 1 : ".$hora."<br>";

		//consultamos el tipo del articulo para saber si debe desglosarse en insumos o no
		$query = "SELECT Mdeart, Tippro, Mdepre, Mdecan, Mdenlo, Conind, Mdedoc, Mdecon from caro2, ".$empresa."_000001,".$empresa."_000002 ";
		$query .= "  where  Mdeart= Artcod ";
		$query .= "     and   Arttip= Tipcod ";

		$err1 = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err1);

		echo "<b>Registros de movimientos : ".$num1."</b><br><br>";
		for ($i=0;$i<$num1;$i++)
		{
			$row1 = mysql_fetch_array($err1);

			if($row1[1]=='on')
			{
				//consultamos el movimiento de fabricación del lotes
				$query = "SELECT concod from   ".$empresa."_000008 ";
				$query .= " where  conind='-1' and congas='on' ";

				$err2 = mysql_query($query,$conex);
				$row2 = mysql_fetch_array($err2);

				//consultamos los valores para productos codificados
				//para esto hay que desglosarlo primero en insumos
				$res=calcularProducto($row1[3], $row1[4], $dsac, $row2[0], $row1[5]);
			}
			else if($row1[1] != 'on')
			{
				$exp=explode('-',$row1[2]);

				$puesto=bi($dsan,count($dsan),$exp[0],1);

				if ($row1[5]==1)
				{
					$dsac[$puesto]['entradas']=$dsac[$puesto]['entradas']+$row1[3];
				}
				else
				{
					$dsac[$puesto]['salidas']=$dsac[$puesto]['salidas']+$row1[3];
				}
			}

			echo "REGISTRO PROCESADO NRo : ".$i."<br>";
		}

		$k=0;
		$contador=1;
		for ($i=0;$i<count($dsan);$i++)
		{
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$dif=$dsac[$i][2]-$dsac[$i]['entradas']+$dsac[$i]['salidas']-$dsan[$i][2];
			$difv=($dsac[$i][2]*$dsac[$i][3]/$dsan[$i][6])-($dsac[$i]['entradas']*$dsac[$i][3]/$dsan[$i][6]+$dsac[$i]['reve'])+($dsac[$i]['salidas']*$dsac[$i][3]/$dsan[$i][6]+$dsac[$i]['revs'])-($dsan[$i][2]*$dsan[$i][3]/$dsan[$i][6]);
			$query = "insert ".$empresa."_000011 (medico,fecha_data,hora_data, Kxmcon, Kxmano, Kxmmes,     Kxmcco,             Kxmcod,             Kxmdes,           Kxmuni,           Kxmgru,      Kxmcsi, Kxmvsi,                 Kxmcen,                   Kxmven,                Kxmcsa,                   Kxmvsa,               Kxmcsf,           Kxmvsf,         Kxmcdi, Kxmvdi, Kxmvro, Kxmdro, Kxmind, seguridad) ";
			$query .= "values ('".$empresa."','".$fecha."','".$hora."',".$contador.",".$wano.",".$wmes.",'".$dsac[$i][0]."','".$dsac[$i][1]."','".$dsac[$i][4]."','".$dsac[$i][5]."',   '',".$dsan[$i][2].",".($dsan[$i][2]*$dsan[$i][3]/$dsan[$i][6]).",".$dsac[$i]['entradas'].",".(($dsac[$i]['entradas']*$dsac[$i][3]/$dsan[$i][6])+$dsac[$i]['reve']).",".$dsac[$i]['salidas'].",".(($dsac[$i]['salidas']*$dsac[$i][3]/$dsan[$i][6])+$dsac[$i]['revs']).",".$dsac[$i][2].",".($dsac[$i][2]*$dsac[$i][3]/$dsan[$i][6]).",".$dif.",".$difv.",'','','on','C-".$empresa."')";
			$err3 = mysql_query($query,$conex);
			if ($err3 != 1)
			echo mysql_errno().":".mysql_error()."<br>";
			else
			{
				$k++;
				echo "REGISTRO INSERTADO NRo : ".$k."<br>";
			}
			$contador++;
		}
		echo "<b>TOTAL REGISTROS INSERTADOS  : </b>".$k."<br>";
	}
}
?>
</body>
</html>
