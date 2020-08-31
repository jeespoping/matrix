<html>
<head>
  	<title>MATRIX Generacion Automatica de revalorizacion del mes</title>
  	
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
<tr><td class="titulo1"><font size=5>PROCESO DE REVALORIZACION</font></tr></td>
<tr><td class="texto5"><b>reval.php Ver. 2007-01-09</b></tr></td></table>
</center> 
<?php
/******************************************************************************************************************************************
 * Modificaciones:
 *
 * 2020-03-20:	Se hacen modifaciones varias para registrar en tabla nueva (cenpro_000024) el detalle de los articulos
 ******************************************************************************************************************************************/


include_once("conex.php");

function calcularProducto($cantidad, $lote, &$ins, $concepto)
{
	global $conex;
	global $empresa;

	$query = "SELECT Mdeart, Mdecan, Mdepre from ".$empresa."_000007 ";
	$query .= " where  Mdecon='".$concepto."'";
	$query .= "   and  Mdenlo='".$lote."'";
	//echo $query;
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
			//echo $cantidad;
			$puesto=bi($ins,count($ins),$row2[2],'cod');
			$ins[$puesto]['exi']=$ins[$puesto]['exi']+$row2[1]*$cantidad/$rowp[0];
		}
		else
		{
			$res=calcularProducto($row2[1]*$cantidad/$rowp[0],$row2[0], $ins, $concepto);
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
	echo "<form name='reval' action='reval.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
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
		$articulos = [];
		
		$query = "Select * from  ".$empresa."_000012 ";
		$query .= " where cinano = ".$wano;
		$query .= "     and cinmes = ".$wmes;
		$query .= "     and (cincon = '98' or cincon = '99' )";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num == 0)
		{
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
			// //consultamos en la tabla de saldos de inventarios, el saldo inicial
			// $query = "SELECT  Salcod, Salexi, Salvuc, Tipmat, Salcco, Salpro from ".$empresa."_000014, ".$empresa."_000009, ".$empresa."_000002,".$empresa."_000001 ";
			// $query .= " where  Salano=".$wanoa;
			// $query .= "     and   Salmes= ".$wmesa;
			// $query .= "     and   Salcod= Apppre ";
			// $query .= "     and   Appcod= Artcod ";
			// $query .= "     and   Arttip= Tipcod ";
			// $query .= "     and   Salins= Appcod ";
			// $query .= "     ORDER BY  Salcod ";
			
			$query  = "SELECT  Salcod, Salexi, Salvuc, Tipmat, Salcco, Salpro from ".$empresa."_000014 a, ".$empresa."_000002 b,".$empresa."_000001 c";
			$query .= " where a.Salano =".$wanoa;
			$query .= "   and a.Salmes = ".$wmesa;
			$query .= "   and a.Salins = b.Artcod ";
			$query .= "   and b.Arttip = c.Tipcod ";
			$query .= "   and a.Salest = 'on' ";
			$query .= "ORDER BY  Salcod ";

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
				$d[$i][5]=$row[5];
			}

			$dsac=array();
			// //consultamos en la tabla de saldos de inventarios el saldo actual
			// $query = "SELECT  Salcod, Salexi, Salvuc, Tipmat, Salcco, Salpro  from ".$empresa."_000014, ".$empresa."_000009, ".$empresa."_000002, ".$empresa."_000001 ";
			// $query .= " where  Salano=".$wano;
			// $query .= "     and   Salmes=".$wmes;
			// $query .= "     and   Salcod= Apppre ";
			// $query .= "     and   Appcod= Artcod ";
			// $query .= "     and   Arttip= Tipcod ";
			// $query .= "     and   Salins= Appcod ";
			// $query .= "     ORDER BY  Salcod ";
			
			//consultamos en la tabla de saldos de inventarios el saldo actual
			$query  = "SELECT Salcod, Salexi, Salvuc, Tipmat, Salcco, Salpro  from ".$empresa."_000014 a, ".$empresa."_000002 b, ".$empresa."_000001 c";
			$query .= " where a.Salano =".$wano;
			$query .= "   and a.Salmes =".$wmes;
			$query .= "   and a.Salins = b.Artcod ";
			$query .= "   and b.Arttip = c.Tipcod ";
			$query .= "   and a.Salest = 'on' ";
			$query .= "ORDER BY  Salcod ";
			//echo $query;
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$totsac=$num;
			$j=0;
			$k=0;
			$row = mysql_fetch_array($err);
			//al mismo tiempo que se cargan se procura que los vectores sean iguales
			for ($i=0;$i<$num;$i++)
			{
				if (isset($d[$k][0]))
				{
					if(strtoupper($d[$k][0])==strtoupper($row[0]))
					{
						$dsac[$j][0]=$row[0];
						$dsac[$j][1]=$row[1];
						$dsac[$j][2]=$row[2];
						$dsac[$j][3]=$row[3];
						$dsac[$j][4]=$row[4];
						$dsac[$j][5]=$row[5];

						$dsan[$j][0]=$d[$k][0];
						$dsan[$j][1]=$d[$k][1];
						$dsan[$j][2]=$d[$k][2];
						$dsan[$j][3]=$d[$k][3];
						$dsan[$j][4]=$d[$k][4];
						$dsan[$j][5]=$d[$k][5];
						$j++;
						$k++;
						$row = mysql_fetch_array($err);
					}
					else if (isset($d[$k+1][0]))
					{
						if (strtoupper($row[0])>=strtoupper($d[$k+1][0]))
						{
							$dsac[$j][0]=$d[$k][0];
							$dsac[$j][1]=0;
							$dsac[$j][2]=0;
							$dsac[$j][3]=$d[$k][3];
							$dsac[$j][4]=$d[$k][4];
							$dsac[$j][5]=$d[$k][5];

							$dsan[$j][0]=$d[$k][0];
							$dsan[$j][1]=$d[$k][1];
							$dsan[$j][2]=$d[$k][2];
							$dsan[$j][3]=$d[$k][3];
							$dsan[$j][4]=$d[$k][4];
							$dsan[$j][5]=$d[$k][5];
							
							$j++;
							$k++;
							$i--;
						}
						else if (strtoupper($row[0])<strtoupper($d[$k+1][0]))
						{
							$dsac[$j][0]=$row[0];
							$dsac[$j][1]=$row[1];
							$dsac[$j][2]=$row[2];
							$dsac[$j][3]=$row[3];
							$dsac[$j][4]=$row[4];
							$dsac[$j][5]=$row[5];

							$dsan[$j][0]=$row[0];
							$dsan[$j][1]=0;
							$dsan[$j][2]=0;
							$dsan[$j][3]=$row[3];
							$dsan[$j][4]=$row[4];
							$dsan[$j][5]=$row[5];
							$j++;
							$row = mysql_fetch_array($err);
						}
					}
					else
					{
						if (strtoupper($row[0])>=strtoupper($d[$k][0]))
						{
							$dsac[$j][0]=$d[$k][0];
							$dsac[$j][1]=0;
							$dsac[$j][2]=0;
							$dsac[$j][3]=$d[$k][3];
							$dsac[$j][4]=$d[$k][4];
							$dsac[$j][5]=$d[$k][5];

							$dsan[$j][0]=$d[$k][0];
							$dsan[$j][1]=$d[$k][1];
							$dsan[$j][2]=$d[$k][2];
							$dsan[$j][3]=$d[$k][3];
							$dsan[$j][4]=$d[$k][4];
							$dsan[$j][5]=$d[$k][5];
							
							$j++;
							$k++;
							$i--;
						}
						else if (strtoupper($row[0])<strtoupper($d[$k][0]))
						{
							$dsac[$j][0]=$row[0];
							$dsac[$j][1]=$row[1];
							$dsac[$j][2]=$row[2];
							$dsac[$j][3]=$row[3];
							$dsac[$j][4]=$row[4];
							$dsac[$j][5]=$row[5];

							$dsan[$j][0]=$row[0];
							$dsan[$j][1]=0;
							$dsan[$j][2]=0;
							$dsan[$j][3]=$row[3];
							$dsan[$j][4]=$row[4];
							$dsan[$j][5]=$row[5];
							
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
					$dsac[$j][5]=$row[5];

					$dsan[$j][0]=$row[0];
					$dsan[$j][1]=0;
					$dsan[$j][2]=0;
					$dsan[$j][3]=$row[3];
					$dsan[$j][4]=$row[4];
					$dsan[$j][5]=$row[5];
					$j++;
					$row = mysql_fetch_array($err);
				}
			}

			$k=0;
			$contador=1;

			for ($i=0;$i<count($dsan);$i++)
			{
				$rev = 0;
				$articulos = [];
				
				if($dsan[$i][5]>0)
				{
					$rev=($dsac[$i][2]-$dsan[$i][2])*$dsan[$i][1]/$dsan[$i][5];
					$articulos[ $dsan[$i][0] ] = $rev;
				}
				//echo $dsac[$i][0].'-'.$rev.'-'.$dsan[$i][1].'-'.$dsan[$i][5].'-'.$dsac[$i][2].'-'.$dsan[$i][2].'</br>';

				if($dsac[$i][3]=='on')
				{
					if ($rev>0)
					{
						$query = "SELECT  Icccon, Iccfue, Icccde, Iccccd, Iccted, Iccccr, Iccccc, Icctec, Icclin, Iccnig, Iccbad, Iccbac from ".$empresa."_000013 ";
						$query .= " where  Icccon='98' and Icclin='02' ";
					}
					if ($rev<0)
					{
						$query = "SELECT   Icccon, Iccfue, Icccde, Iccccd, Iccted, Iccccr, Iccccc, Icctec, Icclin, Iccnig, Iccbad, Iccbac from ".$empresa."_000013 ";
						$query .= " where  Icccon='99' and Icclin='02' ";
					}
				}
				else
				{
					if ($rev>0)
					{
						$query = "SELECT  Icccon, Iccfue, Icccde, Iccccd, Iccted, Iccccr, Iccccc, Icctec, Icclin, Iccnig, Iccbad, Iccbac from ".$empresa."_000013 ";
						$query .= " where  Icccon='98' and Icclin='01' ";
					}
					if ($rev<0)
					{
						$query = "SELECT  Icccon, Iccfue, Icccde, Iccccd, Iccted, Iccccr, Iccccc, Icctec, Icclin, Iccnig, Iccbad, Iccbac from ".$empresa."_000013 ";
						$query .= " where  Icccon='99' and Icclin='01' ";
					}
				}

				//almacenamos los datos
				if ($rev!=0)
				{
					$errli = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
					$rowli = mysql_fetch_array($errli);

					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");

					if($rowli[9] == "on")
					$wbased="S";
					else
					$wbased="N";

					$query = "insert ".$empresa."_000012 (medico,fecha_data,hora_data, Cinsec, Cinano, Cinmes,     Cinfue,   Cincon,    Cincco, Cinnit, Cincue, Cinval, Cinnat, Cinbaj, Cinest, seguridad) ";
					$query .= "values ('".$empresa."','".$fecha."','".$hora."',".$contador.",".$wano.",".$wmes.",'".$rowli[1]."','".$rowli[0]."','".$dsan[$i][4]."','".$rowli[9]."','".$rowli[2]."',".number_format((double)abs($rev),2,'.','').",'1','".$wbased."','on','C-".$empresa."')";
	
					$err3 = mysql_query($query,$conex);
					if ($err3 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
				
					
					if( count( $articulos ) > 0 ){
						
						$query = "insert ".$empresa."_000024 (medico,fecha_data,hora_data, Cinsec, Cinano, Cinmes, Cinfue, Cincon, Cincco, Cinnit, Cincue, Cinart, Cinval, Cinnat, Cinbaj, Cinest, seguridad) values";
						
						$poner_coma = false;
						foreach( $articulos as $cod_articulo => $valor )
						{
							if( $poner_coma )
								$query .= ",";
							
							$poner_coma = true;
							$query .=  "('".$empresa."','".$fecha."','".$hora."',".$contador.",".$wano.",".$wmes.",'".$rowli[1]."','".$rowli[0]."','".$dsan[$i][4]."','".$rowli[9]."','".$rowli[2]."', '".$cod_articulo."',".number_format((double)abs($valor),2,'.','').",'1','".$wbased."','on','C-".$empresa."')";
							$query .= ",('".$empresa."','".$fecha."','".$hora."',".$contador.",".$wano.",".$wmes.",'".$rowli[1]."','".$rowli[0]."','".$dsan[$i][4]."','".$rowli[9]."','".$rowli[2]."', '".$cod_articulo."',".number_format((double)abs($valor),2,'.','').",'2','".$wbased."','on','C-".$empresa."')";
						}
						
						$err_arts = mysql_query($query,$conex);
						if( !$err_arts )
							echo mysql_errno()." - Error al insertar articulos - ".mysql_error()."<br>";
					}
					
					
					


					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");

					//si tiene base credito
					if($rowli[10] == "on")
					$wbasec="S";
					else
					$wbasec="N";

					$query = "insert ".$empresa."_000012 (medico,fecha_data,hora_data, Cinsec, Cinano, Cinmes,     Cinfue,   Cincon,    Cincco, Cinnit, Cincue, Cinval, Cinnat, Cinbaj, Cinest, seguridad) ";
					$query .= "values ('".$empresa."','".$fecha."','".$hora."',".$contador.",".$wano.",".$wmes.",'".$rowli[1]."','".$rowli[0]."','".$dsan[$i][4]."','".$rowli[9]."','".$rowli[5]."',".number_format((double)abs($rev),2,'.','').",'2','".$wbasec."','on','C-".$empresa."')";
					$err3 = mysql_query($query,$conex);
					if ($err3 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
					else
					{
						$contador++;
						$k++;
						echo "REGISTRO INSERTADO NRo : ".$k."<br>";
					}
				}

			}
			echo "<b>TOTAL REGISTROS INSERTADOS  : </b>".$k."<br>";
		}
		else
		{
			echo "<br><br><center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table>";
			echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>AÑO -- MES  : YA FUE GENERADO  !!!!</MARQUEE></FONT>";
			echo "<input type='submit' value='Continuar'></center>";
			echo "<br><br>";
		}
	}
}
?>
</body>
</html>
