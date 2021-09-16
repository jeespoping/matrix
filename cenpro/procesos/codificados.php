<head>
  <title>PROCESO DE CARGA DE COSTOS PROMEDIO</title>
  
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
<BODY>

<?php
include_once("conex.php");
include_once("root/comun.php");
function calcularValorProducto($cantidad, $lote, &$val)
{
	global $conex;
	global $empresa;

	//consultamos el valor del movimiento

	$query = "SELECT Mdeart, Mdecan, Mdepre from ".$empresa."_000007,  ".$empresa."_000008";
	$query .= " where  conind='-1' and congas='on' ";
	$query .= " and  Mdecon=Concod ";
	$query .= "   and  Mdenlo='".$lote."'";


	$err2 = mysql_query($query,$conex);
	$num2 = mysql_num_rows($err2);

	for ($k=0; $k<$num2; $k++)
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
			$query = "SELECT Appcos, Appcnv from ".$empresa."_000009, ".$empresa."_000002 ";
			$query .= " where  Apppre='".$row2[2]."'";
			$query .= " and  Appcod= artcod ";

			$err3 = mysql_query($query,$conex);
			$num3 = mysql_num_rows($err3);
			$row3 = mysql_fetch_array($err3);

			//echo $row3[0]*$row2[1]/($row3[1]*$rowp[0]).'-'.$cantidad.'</br>';
			$val+=$row3[0]*$row2[1]*$cantidad/($row3[1]*$rowp[0]);

		}
		else
		{
			$res=calcularValorProducto(($row2[1]*$cantidad/$rowp[0]),$row2[0],$val);
		}
	}
	if ($k>0)
	{
		return true;
	}
	else
	{
		return false;
	}
}
/////////////////////////////////////////////////////PROGRAMA
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{

	$key = substr($user,2,strlen($user));

	//area de inculdes
	

	include_once("movhos/otros.php");
	$institucion = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
	$wactualiz = "2021-08-13";
	encabezado( "CARGA DE COSTOS PORMEDIO PARA PRODUCTOS CODIFICADOS", $wactualiz, $institucion->baseDeDatos );

	echo "<form name='forma' action='' method=post>";
	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<center><table border=0 >";
	//echo "<tr><td align=center colspan=3 class='texto5'><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
	//echo "<tr><td align=center colspan=3 class='titulo1'>CARGA DE COSTOS PORMEDIO PARA PRODUCTOS CODIFICADOS DE LA CENTRAL DE MEZCLAS</td></tr>";

	if(!isset($enviado))
	{
		echo "<input type='HIDDEN' name= 'enviado' value='1'>";
		echo "<tr><td class='texto4' >Año de Proceso</td>";
		echo "<td class='texto4'><input type='TEXT' name='wano' value='".date('Y')."' size=4 maxlength=4></td></tr>";
		echo "<tr><td class='texto4'>Mes de Proceso</td>";
		echo "<td class='texto4'><input type='TEXT' name='wmes'  value='".date('m')."' size=2 maxlength=2></td></tr>";
		echo "<tr><td class='texto1' colspan=2 align=center><input type='submit' value='ENTER'></td></tr>";
	}
	else
	{
		//conexiones
		

		$bd='movhos';
		connectOdbc($conex_o, 'inventarios');
		if ($conex_o!=0)
		{
			//para poder cargar los saldos, el mes anterior debe estar guardado en la tabla de kardex por mes y ya haber tirado el
			//comprobante contable del mes anterior

			$wanoA=$wano;
			$wmesA=$wmes-1;
			echo "<tr><td align=center colspan=3 class='titulo1'>MES:'.$wmes.'ANO:'.$wano.'</td></tr>";
			if($wmesA<=0)
			{
				$wmesA=12;
				$wanoA=$wano-1;
			}

			$q = "Select * from  ".$empresa."_000011 ";
			$q .= " where kxmano = ".$wanoA;
			$q .= "     and kxmmes = ".$wmesA;
			$err = mysql_query($q,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$q = "Select * from  ".$empresa."_000012 ";
				$q .= " where Cinano = ".$wanoA;
				$q .= "   and Cinmes = ".$wmesA;
				$err = mysql_query($q,$conex);
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					//consultamos los conceptos de traslado
					$q = "Select Mdeart, Mdecan, Mdenlo, Menfec from  ".$empresa."_000008, ".$empresa."_000007, ".$empresa."_000002,  ".$empresa."_000006, ".$empresa."_000001";
					$q .= " where Contra = 'on' ";
					$q .= "   and Conest = 'on'";
					$q .= "   and Concod = Mdecon ";
					$q .= "   and Mdeart = Artcod ";
					$q .= "   and Arttip = Tipcod ";
					$q .= "   and Tipcdo = 'on' ";
					$q .= "   and Menano = '".$wano." '";
					$q .= "   and Menmes = '".$wmes."' ";
					$q .= "   and Mendoc = Mdedoc ";
					$q .= "   and Mencon = Mdecon";
					$q .= "   and Mencco = '1051' ";
					$q .= "   and Menccd = '1050' ";
					$q .= "  ORDER BY 1, 4 ";

					$err = mysql_query($q,$conex);
					$num = mysql_num_rows($err);

					$ant='';
					$j=0;
					for($i=0; $i<$num; $i++)
					{
						$row = mysql_fetch_array($err);
						//echo $row[0];
						if($ant!=$row[0])
						{

							IF ($j!=0)
							{
								$val[$j]=$val[$j]/$can[$j];
							}
							$j++;
							$art[$j]=$row[0];
							$can[$j]=$row[1];
							$val[$j]=0;
							$ant=$row[0];

							//consultamos el valor del movimiento
							calcularValorProducto($row[1], $row[2], $val[$j]);
						}
						else
						{
							$can[$j]+=$row[1];
							calcularValorProducto($row[1], $row[2], $val[$j]);
						}

					}
					$val[$j]=$val[$j]/$can[$j];
					echo '<tr><td align=center>CODIGO</td>';
					echo '<td align=center>CANTIDAD</td>';
					echo '<td align=center>COSTO PROMEDIO</td></TR>';
					for($i=1; $i<=count($art); $i++)
					{
						//grabo valor en unix
						$q = "UPDATE Ivartpro "
						."       SET Artpropro = ".$val[$i]." "
						."    WHERE Artproano = '".$wano."' "
						."      and Artpromes = '".$wmes."' "
						."      and Artproart = '".$art[$i]."' ";
						//ECHO $q;
						$err_o= odbc_do($conex_o,$q);
						$x=odbc_num_rows($err_o);
						
						if($x<=0)
						{
							echo 'NO SE HA PODIDO ACTUALIZAR EL COSTO DEL PRODUCTO'.$art[$i].'EN UNIX';
						}

						//grabo valor en matrix
						$q= "   UPDATE ".$empresa."_000005 "
						."      SET karcos = ".$val[$i]." "
						."    WHERE Karcod = '".$art[$i]."' ";

						$res1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ACTUALIZAR EL PRODUCTO ".$art[$i]." ".mysql_error());


						echo '<tr><td align=center>'.$art[$i].'</td>';
						echo '<td align=center>'.$can[$i].'</td>';
						echo '<td align="right">'.number_format((double)$val[$i],2,'.',',').'</td></TR>';
					}

					echo "</table></br>";

					echo 'SE HAN ACTUALIZADO LOS COSTOS PROMEDIOS DE '.count($art).' PRODUCTOS CODIFICADOS';

				}
				else
				{
					echo "</table></br>";
					echo"<CENTER>";
					echo "<table align='center' border=0 bordercolor=#000080 width=700>";
					echo "<tr><td colspan='2' class='texto4'><font size=3 color='#000080' face='arial' align=center><b>Primero debe generar el comprobante de inventario del mes anterior</td></tr>";
					echo "<tr><td class='texto1' colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
					echo "</table>";
				}

			}
			else
			{
				echo "</table></br>";
				echo"<CENTER>";
				echo "<table align='center' border=0 bordercolor=#000080 width=700>";
				echo "<tr><td colspan='2' class='texto4'><font size=3 color='#000080' face='arial' align=center><b>Primero debe almacenar el kardex del mes anterior</td></tr>";
				echo "<tr><td class='texto1' colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
				echo "</table>";
			}
		}
		else
		{
			echo "</table></br>";
			echo"<CENTER>";
			echo "<table align='center' border=0 bordercolor=#000080 width=700>";
			echo "<tr><td colspan='2' class='texto4'><font size=3 color='#000080' face='arial' align=center><b>En este momento no es posible conectarse con unix, por favor ingrese mas tarde</td></tr>";
			echo "<tr><td class='texto1' colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
			echo "</table>";
		}
	}
}
?>
</body>
</html>