<html>
<head>
  	<title>MATRIX  Comprobante de Inventarios</title>
  	
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
<BODY>
<?php

/******************************************************************************************************************************************
 * Modificaciones
 * 
 * 2020-03-20	Edwin MG : Se registra en tabla nueva DETALLE ARTICULO - GENERACION DE INVENTARIO (cenpro_000024) el comprobante 
 *						   de inventario detallada por articulos
 ******************************************************************************************************************************************/
 
include_once("conex.php");
function calcularValorProducto($cantidad, $lote, &$debito1, &$credito1, &$debito2, &$credito2, $concepto, $numli, &$articulos )
{
	global $conex;
	global $empresa;
	global $articulos_por_presentacion;

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
			if( empty( $articulos_por_presentacion[ $row2[2] ][ $row2[0] ] ) || empty( $articulos_por_presentacion[ $row2[2] ][ $row2[0] ]['mat'] ) ){
				//Se debe consultar tambien el codigo del insumo para encontrar el valor correcto de conversion (Appcnv)
				$query = "SELECT Appcos, Appcnv, Tipmat from ".$empresa."_000009, ".$empresa."_000001, ".$empresa."_000002 ";
				$query .= "where  Apppre='".$row2[2]."'";
				$query .= "  and  Appcod='".$row2[0]."' ";
				$query .= "  and  Appcod= artcod ";
				$query .= "  and  Arttip= tipcod ";
				
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				$row = mysql_fetch_array($err);
			}
			else{
				$row[0] = $articulos_por_presentacion[ $row2[2] ][ $row2[0] ]['cos'];
				$row[1] = $articulos_por_presentacion[ $row2[2] ][ $row2[0] ]['cnv'];
				$row[2] = $articulos_por_presentacion[ $row2[2] ][ $row2[0] ]['mat'];
			}

			if($row[2]=='on' and $numli>1)
			{
				
				$debito2 +=$row[0]*$row2[1]*$cantidad/($row[1]*$rowp[0]);
				$credito2 +=$row[0]*$row2[1]*$cantidad/($row[1]*$rowp[0]);
				
				$articulos['fuente2'][ $row2[2] ] += $row[0]*$row2[1]*$cantidad/($row[1]*$rowp[0]);
			}
			else
			{
				$debito1 +=$row[0]*$row2[1]*$cantidad/($row[1]*$rowp[0]);
				$credito1 +=$row[0]*$row2[1]*$cantidad/($row[1]*$rowp[0]);
				
				$articulos['fuente1'][ $row2[2] ] += $row[0]*$row2[1]*$cantidad/($row[1]*$rowp[0]);
			}
		}
		else
		{
			$res=calcularValorProducto(($row2[1]*$cantidad/$rowp[0]),$row2[0],$debito1, $credito1, $debito2, $credito2, $concepto, $numli, $articulos );
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

session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='compinv' action='compinv.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wfeci) or !isset($wfecf) or !isset($wano) or !isset($wmes))
	{
		echo "<center><table border=0>";
		echo "<tr><td class='texto5' colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td class='titulo1' colspan=2>GENERACION ARCHIVO MENSUAL DEL COMPROBANTE CONTABLE</td></tr>";
		echo "<tr><td class='texto4'>Año de Proceso</td>";
		echo "<td class='texto4'><input type='TEXT' name='wano' size=4 maxlength=4></td></tr>";
		echo "<tr><td class='texto4'>Mes de Proceso</td>";
		echo "<td class='texto4'><input type='TEXT' name='wmes' size=2 maxlength=2></td></tr>";
		echo "<tr><td class='texto4'>Fecha Inicial</td>";
		echo "<td class='texto4'><input type='TEXT' name='wfeci' size=10 maxlength=10></td></tr>";
		echo "<tr><td class='texto4'>Fecha Final</td>";
		echo "<td class='texto4'><input type='TEXT' name='wfecf' size=10 maxlength=10></td></tr>";
		echo "<tr><td class='texto1'  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		echo "<table border=0 align=center>";
		echo "<tr><td class='titulo1'><b>GENERACION DE COMPROBANTE CONTABLE</font> Ver 1.0</b></font></td></tr>";
		echo "<tr><td class='texto4'><font face='tahoma'><b>Fecha Inicial : </b>".$wfeci."</td></tr>";
		echo "<tr><td class='texto4'><font face='tahoma'><b>Fecha Final : </b>".$wfecf."</td></tr>";
		echo "</tr></table><br><br>";
		
		//Guardar articulos por presentación
		$articulos_por_presentacion = [];

		$ultreg=1;
		$query = "Select * from  ".$empresa."_000012 ";
		$query .= " where cinano = ".$wano;
		$query .= "     and cinmes = ".$wmes;
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num == 0)
		{
			//ACA CREO UNA TABLA TEMPORAL CON TODOS LOS MOVIMIENTOS
			$query = "SELECT   Mencon, Menfec, Mendoc, Mencco, Menccd, Congec from ".$empresa."_000006,".$empresa."_000008 ";
			$query .= " where  Menfec between '".$wfeci."' and '".$wfecf."'";
			$query .= "     and   Mencon=Concod ";
			$query .= "     and   Congec='on' ";
			$query .= "    Order by Mencon, Mendoc ";

			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());

			/*		//ACA CREO UNA TABLA TEMPORAL CON TODOS LOS MOVIMIENTOS
			$query = "SELECT   Mencon, Menfec, Mendoc, Mencco, Menccd, Iccfue, Icccde, Iccccd, Iccted, Iccccr, Iccccc, Icctec, congec, Iccnig, Iccbad, Iccbac from ".$empresa."_000006,".$empresa."_000008,".$empresa."_000013 ";
			$query .= " where  Menfec between '".$wfeci."' and '".$wfecf."'";
			$query .= "     and   Mencon=Concod ";
			$query .= "     and   Congec='on' ";
			$query .= "     and   Mencon=Icccon ";
			$query .= "    Order by Iccfue,Mencon, Mendoc ";*/

			$num = mysql_num_rows($err);
			echo "<b>Movimientos Totales  : ".$num."</b><br><br>";
			$wtotd1=0;
			$wtotc1=0;
			$wtotd2=0;
			$wtotc2=0;
			$k=0;
			$wconant="";
			$wfueant="";
			$cl=0;
			
			$articulos = [];
			
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);

				$wconant = $row[0];
				$wtotd1=0;
				$wtotc1=0;
				$wtotd2=0;
				$wtotc2=0;
				
				$articulos = [];

				//consultamos las diferentes cuentas involucradas
				$query = "SELECT  Iccfue, Icccde, Iccccd, Iccted, Iccccr, Iccccc, Icctec, Icclin, Iccnig, Iccbad, Iccbac from ".$empresa."_000013 ";
				$query .= " where  Icccon='".$row[0]."' ";
				$query .= "    Order by Icclin ";

				$errli = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
				$numli = mysql_num_rows($errli);
				$rowli = mysql_fetch_array($errli);

				//si tine base debito
				if($rowli[9] == "on")
				$wbased1="S";
				else
				$wbased1="N";

				//si tiene base credito
				if($rowli[10] == "on")
				$wbasec1="S";
				else
				$wbasec1="N";
				$wfuente1=$rowli[0];

				//el debito que centro de costos es
				switch ($rowli[2])
				{
					case "origen":
					$wccde1=$row[3];
					break;
					case "destino":
					$wccde1=$row[4];
					break;
					case "no":
					$wccde1="00";
					break;
				}

				//nit del tercero
				$wnitde1="0";
				if($rowli[3] == "on")
				$wnitde1=$rowli[8];

				switch ($rowli[5])
				{
					case "origen":
					$wcccr1=$row[3];
					break;
					case "destino":
					$wcccr1=$row[4];
					break;
					case "no":
					$wcccr1="00";
					break;
				}

				$wnitcr1="0";
				if($rowli[6] == "on")
				$wnitcr1=$rowli[8];

				$wcdb1=$rowli[1];
				$wccr1=$rowli[4];

				if($numli>1)
				{
					$rowli = mysql_fetch_array($errli);

					//si tine base debito
					if($rowli[9] == "on")
					$wbased2="S";
					else
					$wbased2="N";

					//si tiene base credito
					if($rowli[10] == "on")
					$wbasec2="S";
					else
					$wbasec2="N";
					$wfuente2=$rowli[0];

					//el debito que centro de costos es
					switch ($rowli[2])
					{
						case "origen":
						$wccde2=$row[3];
						break;
						case "destino":
						$wccde2=$row[4];
						break;
						case "no":
						$wccde2="00";
						break;
					}

					//nit del tercero
					$wnitde2="0";
					if($rowli[3] == "on")
					$wnitde2=$rowli[8];

					switch ($rowli[5])
					{
						case "origen":
						$wcccr2=$row[3];
						break;
						case "destino":
						$wcccr2=$row[4];
						break;
						case "no":
						$wcccr2="00";
						break;
					}

					$wnitcr2="0";
					if($rowli[6] == "on")
					$wnitcr2=$rowli[8];

					$wcdb2=$rowli[1];
					$wccr2=$rowli[4];
				}

				//consultamos el tipo del articulo para saber si debe desglosarse en insumos o no
				$query = "SELECT Mdeart, Tippro, Mdepre, Mdecan, Mdenlo, Tipmat from ".$empresa."_000007,".$empresa."_000001,".$empresa."_000002  ";
				$query .= " where  Mdecon='".$row[0]."'";
				$query .= "     and   Mdedoc='".$row[2]."'";
				$query .= "     and   Mdeart=artcod ";
				$query .= "     and   Arttip=Tipcod ";

				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);

				for ($j=0;$j<$num1;$j++)
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
						$res=calcularValorProducto($row1[3],$row1[4],$wtotd1, $wtotc1, $wtotd2, $wtotc2, $row2[0], $numli, $articulos );

					}
					else if($row1[1] != 'on')
					{
						$exp=explode( '-', strtoupper( $row1[2] ) );	//presentacion del insumo
						$cod=explode( '-', strtoupper( $row1[0] ) );	//codigo del insumo
						
						/******************************************************************************
						 * Esto se hace para darle mayor velocidad al programa
						 ******************************************************************************/
						if( empty( $articulos_por_presentacion[ $exp[0] ][ $cod[0] ] ) ){
						
							//consultamos los valores para insumos
							//Para encontrar el valor correcto de conversion(Appcnv) debe incluir el codigo del articulo
							$query = "SELECT Appcos, Appcnv from ".$empresa."_000009 ";
							$query .= " where  Apppre='".$exp[0]."'";
							$query .= "   and  Appcod='".$cod[0]."'";	
							
							$err2 = mysql_query($query,$conex);
							$num2 = mysql_num_rows($err2);
							$row2 = mysql_fetch_array($err2);
							
							$articulos_por_presentacion[ $exp[0] ][ $cod[0] ]['cos'] = $row2[0];
							$articulos_por_presentacion[ $exp[0] ][ $cod[0] ]['cnv'] = $row2[1];
						}
						else{
							$row2[0] = $articulos_por_presentacion[ $exp[0] ][ $cod[0] ]['cos'];
							$row2[1] = $articulos_por_presentacion[ $exp[0] ][ $cod[0] ]['cnv'];
						}

						if($row1[5]=='on' and $numli>1)
						{
							$wtotd2 +=$row2[0]*$row1[3]/$row2[1];
							$wtotc2 +=$row2[0]*$row1[3]/$row2[1];
							
							$articulos[ 'fuente2' ][ $exp[0] ] +=$row2[0]*$row1[3]/$row2[1];
						}
						else
						{
							$wtotd1 +=$row2[0]*$row1[3]/$row2[1];
							$wtotc1 +=$row2[0]*$row1[3]/$row2[1];
							
							$articulos[ 'fuente1' ][ $exp[0] ] +=$row2[0]*$row1[3]/$row2[1];
						}
					}
				}

				//guardamos los datos
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");

				IF($wtotd1>0)
				{
					$query = "insert ".$empresa."_000012 (medico,fecha_data,hora_data, Cinsec, Cinano, Cinmes, Cinfue, Cincon, Cincco, Cinnit, Cincue, Cinval, Cinnat, Cinbaj, Cinest, seguridad) ";
					$query .= "values ('".$empresa."','".$fecha."','".$hora."',".$ultreg.",".$wano.",".$wmes.",'".$wfuente1."','".$wconant."','".$wccde1."','".$wnitde1."','".$wcdb1."',".number_format((double)$wtotd1,2,'.','').",'1','".$wbased1."','on','C-".$empresa."')";

					$err3 = mysql_query($query,$conex);
					if ($err3 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
					
					if( count( $articulos['fuente1'] ) > 0 ){
						
						$query = "insert ".$empresa."_000024 (medico,fecha_data,hora_data, Cinsec, Cinano, Cinmes, Cinfue, Cincon, Cincco, Cinnit, Cincue, Cinart, Cinval, Cinnat, Cinbaj, Cinest, seguridad) values";
						
						$poner_coma = false;
						foreach( $articulos['fuente1'] as $cod_articulo => $valor )
						{
							if( $poner_coma )
								$query .= ",";
							
							$poner_coma = true;
							$query .= "('".$empresa."','".$fecha."','".$hora."',".$ultreg.",".$wano.",".$wmes.",'".$wfuente1."','".$wconant."','".$wccde1."','".$wnitde1."','".$wcdb1."','".$cod_articulo."',".number_format((double)$valor,2,'.','').",'1','".$wbased1."','on','C-".$empresa."')";
						}
						
						$err_arts = mysql_query($query,$conex);
						if( !$err_arts )
							echo mysql_errno()." - Error al insertar articulos - ".mysql_error()."<br>";
					}
					
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert ".$empresa."_000012 (medico,fecha_data,hora_data, Cinsec, Cinano, Cinmes, Cinfue, Cincon, Cincco, Cinnit, Cincue, Cinval, Cinnat, Cinbaj, Cinest, seguridad) ";
					$query .= "values ('".$empresa."','".$fecha."','".$hora."',".$ultreg.",".$wano.",".$wmes.",'".$wfuente1."','".$wconant."','".$wcccr1."','".$wnitcr1."','".$wccr1."',".number_format((double)$wtotc1,2,'.','').",'2','".$wbasec1."','on','C-".$empresa."')";
					$err3 = mysql_query($query,$conex);
					if ($err3 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
					
					if( count( $articulos['fuente1'] ) > 0 ){
						
						$query = "insert ".$empresa."_000024 (medico,fecha_data,hora_data, Cinsec, Cinano, Cinmes, Cinfue, Cincon, Cincco, Cinnit, Cincue, Cinart, Cinval, Cinnat, Cinbaj, Cinest, seguridad) values";
						
						$poner_coma = false;
						foreach( $articulos['fuente1'] as $cod_articulo => $valor )
						{
							if( $poner_coma )
								$query .= ",";
							
							$poner_coma = true;
							$query .= "('".$empresa."','".$fecha."','".$hora."',".$ultreg.",".$wano.",".$wmes.",'".$wfuente1."','".$wconant."','".$wcccr1."','".$wnitcr1."','".$wccr1."','".$cod_articulo."',".number_format((double)$valor,2,'.','').",'2','".$wbasec1."','on','C-".$empresa."')";
						}
						$err_arts = mysql_query($query,$conex);
						if( !$err_arts )
							echo mysql_errno()." - Error al insertar articulos - ".mysql_error()."<br>";
					}
					
					$ultreg++;
				}

				IF($wtotd2>0)
				{
					$query = "insert ".$empresa."_000012 (medico,fecha_data,hora_data, Cinsec, Cinano, Cinmes, Cinfue, Cincon, Cincco, Cinnit, Cincue, Cinval, Cinnat, Cinbaj, Cinest, seguridad) ";
					$query .= "values ('".$empresa."','".$fecha."','".$hora."',".$ultreg.",".$wano.",".$wmes.",'".$wfuente2."','".$wconant."','".$wccde2."','".$wnitde2."','".$wcdb2."',".number_format((double)$wtotd2,2,'.','').",'1','".$wbased2."','on','C-".$empresa."')";

					$err3 = mysql_query($query,$conex);
					if ($err3 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
					
					if( count( $articulos['fuente2'] ) > 0 )
					{
						$query = "insert ".$empresa."_000024 (medico,fecha_data,hora_data, Cinsec, Cinano, Cinmes, Cinfue, Cincon, Cincco, Cinnit, Cincue, Cinart, Cinval, Cinnat, Cinbaj, Cinest, seguridad) values";
						
						$poner_coma = false;
						foreach( $articulos['fuente2'] as $cod_articulo => $valor )
						{
							if( $poner_coma )
								$query .= ",";
							
							$poner_coma = true;
							
							$query .= "('".$empresa."','".$fecha."','".$hora."',".$ultreg.",".$wano.",".$wmes.",'".$wfuente2."','".$wconant."','".$wccde2."','".$wnitde2."','".$wcdb2."', '".$cod_articulo."',".number_format((double)$valor,2,'.','').",'1','".$wbased2."','on','C-".$empresa."')";
						}
						
						$err_arts = mysql_query($query,$conex);
						if( !$err_arts )
							echo mysql_errno()." - Error al insertar articulos - ".mysql_error()."<br>";
					}
					
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert ".$empresa."_000012 (medico,fecha_data,hora_data, Cinsec, Cinano, Cinmes, Cinfue, Cincon, Cincco, Cinnit, Cincue, Cinval, Cinnat, Cinbaj, Cinest, seguridad) ";
					$query .= "values ('".$empresa."','".$fecha."','".$hora."',".$ultreg.",".$wano.",".$wmes.",'".$wfuente2."','".$wconant."','".$wcccr2."','".$wnitcr2."','".$wccr2."',".number_format((double)$wtotc2,2,'.','').",'2','".$wbasec2."','on','C-".$empresa."')";
					$err3 = mysql_query($query,$conex);
					if ($err3 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
					
					if( count( $articulos['fuente2'] ) > 0 )
					{
						$query = "insert ".$empresa."_000024 (medico,fecha_data,hora_data, Cinsec, Cinano, Cinmes, Cinfue, Cincon, Cincco, Cinnit, Cincue, Cinart, Cinval, Cinnat, Cinbaj, Cinest, seguridad) values";
						
						$poner_coma = false;
						foreach( $articulos['fuente2'] as $cod_articulo => $valor )
						{	
							if( $poner_coma )
								$query .= ",";
							
							$poner_coma = true;
						
							$query .= "('".$empresa."','".$fecha."','".$hora."',".$ultreg.",".$wano.",".$wmes.",'".$wfuente2."','".$wconant."','".$wcccr2."','".$wnitcr2."','".$wccr2."', '".$cod_articulo."',".number_format((double)$valor,2,'.','').",'2','".$wbasec2."','on','C-".$empresa."')";
						}
						
						$err_arts = mysql_query($query,$conex);
						if( !$err_arts )
							echo mysql_errno()." - Error al insertar articulos - ".mysql_error()."<br>";
					}
					
					$ultreg++;
				}

				echo "MOVIMIENTO INSERTADO NRo : ".$i."<br>";
			}
			echo "<b>TOTAL REGISTROS INSERTADOS  : </b>".($ultreg-1)."<br>";
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