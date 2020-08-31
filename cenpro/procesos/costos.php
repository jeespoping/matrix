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
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{

	$key = substr($user,2,strlen($user));

	//area de inculdes
	

	include_once("movhos/otros.php");

	echo "<form name='forma' action='' method=post>";
	echo "<input type='HIDDEN' name= 'wbasedato' value='".$wbasedato."'>";
	echo "<center><table border=0>";
	echo "<tr><td align=center colspan=2 class='texto5'><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
	echo "<tr><td align=center colspan=2 class='titulo1'>CARGA DE COSTOS PORMEDIO PARA INSUMOS DE LA CENTRAL DE MEZCLAS</td></tr>";

	if(!isset($enviado))
	{
		echo "<input type='HIDDEN' name= 'enviado' value='1'>";
		echo "<tr><td class='texto4' >Año de Proceso</td>";
		echo "<td class='texto4'><input type='TEXT' name='wano' value='".date('Y')."' size=4 maxlength=4></td></tr>";
		echo "<tr><td class='texto4'>Mes de Proceso</td>";
		echo "<td class='texto4'><input type='TEXT' name='wmes'  value='".date('m')."' size=2 maxlength=2></td></tr>";
		echo "<tr><td class='texto1' colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
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
			if($wmesA<=0)
			{
				$wmesA=12;
				$wanoA=$wano-1;
			}

			$q = "Select * from  ".$wbasedato."_000011 ";
			$q .= " where kxmano = ".$wanoA;
			$q .= "     and kxmmes = ".$wmesA;
			$err = mysql_query($q,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$q = "Select * from  ".$wbasedato."_000012 ";
				$q .= " where Cinano = ".$wanoA;
				$q .= "   and Cinmes = ".$wmesA;
				$err = mysql_query($q,$conex);
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					//consultamos todos los insumos codificados
					// $q = "Select Apppre from  ".$wbasedato."_000009 ";
					$q = "Select Apppre from  ".$wbasedato."_000009 where Appest='on';";
					$err = mysql_query($q,$conex);
					$num = mysql_num_rows($err);
					for($i=0; $i<$num; $i++)
					{
						$row = mysql_fetch_array($err);
						//consultamos el valor del costo promedio para el mes
						$q= "SELECT artpropro "
						."     FROM ivartpro "
						."    WHERE artproano = '".$wano."' "
						."      and artpromes = '".$wmes."' "
						."      and artproart =  '".$row[0]."'  ";

						// echo $q."<br>";
						$err_o= odbc_do($conex_o,$q);
						if(odbc_fetch_row($err_o))
						{
							$ins[$i]['cod']=$row[0];
							$ins[$i]['val']=odbc_result($err_o,1);
						}
						// else
						// {
							// $i=$num+1;
							// $para=0;
							// echo '<script language="Javascript">';
							// echo 'alert ("NO SE ENCONTRO SALDO PROMEDIO PARA EL INSUMO '.$row[0].'")';
							// echo '</script>';
						// }
					}

					//si esta setiado para fue por que no se encontro una tarifa para algun articulo, hay que averiguar por que
					if (!isset($para))
					{
						//consultamos todos los productos codificados
						$q = "Select Artcod from  ".$wbasedato."_000002, ".$wbasedato."_000001 "
						.    "Where arttip=tipcod and tippro='on' and tipcdo='on' ";
						$err = mysql_query($q,$conex);
						$num = mysql_num_rows($err);

						for($i=0; $i<$num; $i++)
						{
							$row = mysql_fetch_array($err);
							//consultamos el valor del costo promedio para el mes
							$q= "SELECT artpropro "
							."     FROM ivartpro "
							."    WHERE artproano = '".$wano."' "
							."      and artpromes = '".$wmes."' "
							."      and artproart =  '".$row[0]."'  ";

							// echo $q."<br>";
							$err_o= odbc_do($conex_o,$q);
							if(odbc_fetch_row($err_o))
							{
								$pro[$i]['cod']=$row[0];
								$pro[$i]['val']=odbc_result($err_o,1);
							}
							// else
							// {
								// $i=$num+1;
								// $para=0;
								// echo '<script language="Javascript">';
								// echo 'alert ("NO SE ENCONTRO SALDO PROMEDIO PARA EL PRODUCTO '.$row[0].'")';
								// echo '</script>';
							// }
						}

						//si esta setiado para fue por que no se encontro una tarifa para algun producto, hay que averiguar por que
						if (!isset($para))
						{
							//si todo se encontro perfectamente, vamos a actualizar los costos promedios

							//para insumos
							for($i=0; $i<count($ins); $i++)
							{
								if($ins[$i]['cod']!="")
								{
									$q= "   UPDATE ".$wbasedato."_000009 "
									."      SET Appcos = ".$ins[$i]['val']." "
									."    WHERE Apppre = '".$ins[$i]['cod']."' ";

									$res1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ACTUALIZAR EL INSUMO ".$ins[$i]['cod']." ".mysql_error());

									$q= "   UPDATE ".$wbasedato."_000014 "
									."      SET Salvuc = ".$ins[$i]['val']." "
									."    WHERE  Salcod= '".$ins[$i]['cod']."' "
									."    and  Salano= '".$wano."' "
									."    and  Salmes= '".$wmes."' ";

									$res1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ACTUALIZAR EL INSUMO EN LA TABLA DE SALDOS ".$ins[$i]['cod']." ".mysql_error());
								}
								
							}

							//para productos codificados
							for($i=0; $i<count($pro); $i++)
							{
								if($pro[$i]['cod']!="")
								{
									$q= "   UPDATE ".$wbasedato."_000005 "
									."      SET karcos = ".$pro[$i]['val']." "
									."    WHERE Karcod = '".$pro[$i]['cod']."' ";

									$res1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ACTUALIZAR EL PRODUCTO ".$pro[$i]['cod']." ".mysql_error());
								}
							}

							echo "</table></br>";
							echo"<CENTER>";
							echo "<table align='center' border=0 bordercolor=#000080 width=700>";
							echo "<tr><td colspan='2' class='texto4'><font size=3 color='#000080' face='arial' align=center><b>SE HAN ACTUALIZADO LOS COSTOS EXITOSAMENTE</td></tr>";
							echo "<tr><td class='texto1' colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
							echo "</table>";

						}
					}

					if (isset($para))
					{
						echo "</table></br>";
						echo"<CENTER>";
						echo "<table align='center' border=0 bordercolor=#000080 width=700>";
						echo "<tr><td colspan='2' class='texto4'><font size=3 color='#000080' face='arial' align=center><b>NO SE HAN ACTUALIZADO LOS COSTOS, DEBE SOLUCIONAR PRIMERO LOS PROBLEMAS DETECTADOS</td></tr>";
						echo "<tr><td class='texto1' colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
						echo "</table>";
					}
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