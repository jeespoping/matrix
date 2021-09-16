<html>
<head>
<title>MATRIX Reporte De Existencias x Sede x Laboratorio (UVGlobal)</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
//MODIFICACIONES:
//2014-11-20    Juan C. Hdez : Se modifica el query ppal porque tenia en el from la _000008, la cual no se usaba en el query
//2013-07-02    Jonatan Lopez: Se agrega la columna de ultimo precio de venta.
//2012-08-06	Camilo Zapata: Se le di� al reporte la hoja de estilos que tienen los demas programas de matrix.
//2012-08-02	Camilo Zapata: Se modific� el script para que agregue la fecha de la �ltima compra de cada articulo cuando se hace la consulta del reporte de manera detallada.
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	include_once("root/comun.php");
	$wactualiz='2014-11-20';
	encabezado("REPORTE DE EXISTENCIAS X SEDE X LABORATORIO (UVGlobal)",$wactualiz,$empresa);
	$key = substr($user,2,strlen($user));
	echo "<form action='uvgkarxlab.php' method=post>";
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wcco) or !isset($wtip) or (strtoupper($wtip) != "R" and strtoupper($wtip) != "D") or !isset($wcla) or (strtoupper($wcla) != "T" and strtoupper($wcla) != "C"))
	{
		echo "<center><table border=0>";
		echo "<tr class='encabezadotabla'><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr class='fila1'><td align=center>Centro de Costos</td><td align=center>";
		$query = "SELECT Ccocod, Ccodes    from ".$empresa."_000003 order by Ccocod";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wcco'>";
			echo "<option>0-TODAS LAS SEDES</option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}
			echo "</select>";
		}
		echo "</td></tr>";
		echo "<tr class='fila2'><td align=center>Tipo (R-Resumido/D-Detallado)</td>";
		echo "<td align=center><input type='TEXT' name='wtip' size=1 maxlength=1></td></tr>";
		echo "<tr class='fila1'><td align=center>Seleccion (T-Todos/C-Cantidades Mayores a Cero)</td>";
		echo "<td align=center><input type='TEXT' name='wcla' size=1 maxlength=1></td></tr>";
		echo "<tr class='fila2'><td colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
	$qcon = "  SELECT Concod "
			."   FROM ".$empresa."_000008 "
			."	WHERE Conind = 1"		//que sea de ingreso
			."	  AND Conaca = 'on'"	//que afecte cantidad
			."	  AND Conaco = 'on'"	//afecta costo
			."	  AND Conmin = 'on'"	//mueve inventario
			."	  AND Condan = 'on'"	//utiliza documento anexo
			."	  AND Concan != 0" 		//tipo de documento anexo distinto de cero documento anexo
			."	  AND Conpro = 'on'"	//requiere proveedor
			."	  AND Conauc = 'on'"	//afecta �ltima compra
			."	  AND Congec = 'on'"	//genera comprobante
			."	  AND Conest = 'on'";
		$rs= mysql_query($qcon,$conex);
		$row=mysql_fetch_array($rs);
		$concepto = $row[0];

		$qaux = "DROP TABLE IF EXISTS tmpMovimiento";
		$resdr = mysql_query($qaux,$conex) or die (mysql_errno().":".mysql_error());
		$qtemp = "CREATE TEMPORARY TABLE IF NOT EXISTS tmpMovimiento "
				."(INDEX idx(Mdeart))"
				." SELECT Mdeart, MAX(Menfec) as ultfec"
				."   FROM ".$empresa."_000010, ".$empresa."_000011  "
				."	WHERE Mencon = '".$concepto."' "
				."	  AND Mencon = Mdecon "
				."	  AND Mendoc = Mdedoc "
				."	GROUP BY 1 ";
		$rs	=	mysql_query($qtemp, $conex) or die (mysql_errno()." - ".mysql_error());

		$tablaTemporal='';
		$mostraFecha='';
		$joinTemporal='';
			if(strtoupper($wtip) == "D")
			{
				$tablaTemporal=", tmpMovimiento a";
				$joinTemporal="  and Artcod = a.Mdeart";
				$mostrarFecha=", a.ultfec";
			}

		//                      0          1      2        3       4       5       6       7		8
		$query  = "SELECT mid(Artcod,1,2),Artcod, Artnom, Karexi, Karpro, Karvuc, Karcco, Ccodes".$mostrarFecha." from ".$empresa."_000001, ".$empresa."_000007, ".$empresa."_000003".$tablaTemporal." ";
		$query .= "  where Artcod = Karcod ";
		$query .= "".$joinTemporal."";
		$query .= "    and Karcco = Ccocod ";
		if(substr($wcco,0,strpos($wcco,"-")) != "0")
			$query .= "   and Karcco = '".substr($wcco,0,strpos($wcco,"-"))."' ";
		if(strtoupper($wcla) == "C")
			$query .= "   and Karexi > 0 ";
		$query .= "   Order by 1,mid(Artcod,1,5),CAST(mid(Artcod,6,LENGTH(Artcod)) as  UNSIGNED)  ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);

        $array_ult_venta = array();

		if(strtoupper($wtip) == "D")
		{
			//Aqui se consulta el ultimo precio de venta del articulo.
			$query_upv = "SELECT Mtaart, Mtacco, Mtavac
							FROM ".$empresa."_000026
						   WHERE Mtatar = 'TP-TARIFAS PARTICULARES'";
			$res_upv = mysql_query($query_upv,$conex);

			while($row_upv = mysql_fetch_array($res_upv))
					{
						if(!array_key_exists($row_upv['Mtavac'], $array_ult_venta))
						{
							$array_ult_venta[$row_upv['Mtaart']][$row_upv['Mtacco']] = $row_upv['Mtavac'];
						}
					}
		}

		echo "<center><table border=0>";
		//echo "<tr><td colspan=7 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
		echo "<tr><td colspan=7 align=center><b>DIRECCION DE INFORMATICA</b></td></tr>";
		//echo "<tr><td colspan=7 align=center><b>REPORTE DE EXISTENCIAS X SEDE X LABORATORIO (UVGlobal)</b></td></tr>";
		echo "<tr><td colspan=7 align=center><b>SEDE : ".$wcco."</b></td></tr>";
		echo "<tr><td colspan=7 align=center><b>A : ".date("Y-m-d")."</b></td></tr>";
		echo "<tr><td colspan=7 align=center><b>Generado a las : ".date("H:i:s")."</b></td></tr>";
		echo "<tr class='encabezadotabla'>";
		echo "<td><b>Codigo</b></td>";
		echo "<td><b>Descripcion</b></td>";
		echo "<td align=right><b>Existencias</b></td>";
		echo "<td align=right><b>Costo<br>Promedio</b></td>";
		echo "<td align=right><b>Costo<br>Total</b></td>";
		echo "<td align=right><b>Valor<BR>Ultima<BR>Compra</b></td>";
		if(strtoupper($wtip) == "D")
        {
			echo "<td align=right><b>Fecha<BR>Ultima<BR>Compra</b></td>";
            echo "<td align=right><b>Ultimo<br>Precio<br>Venta</td>";
        }
		echo "<td align=right><b>Sede</b></td>";
		echo "</tr>";
		$wlab="";
		$tot=array();
		$totG=array();
		$tot[0]=0;
		$tot[1]=0;
		$tot[2]=0;
		$tot[3]=0;
		$totG[0]=0;
		$totG[1]=0;
		$totG[2]=0;
		$totG[3]=0;
		for ($i=0;$i<$num;$i++)
		{
			if($i % 2 == 0)
				$color="fila1";
			else
				$color="fila2";
			$row = mysql_fetch_array($err);
			if($wlab != $row[0])
			{
				if($i > 0)
					echo "<tr class='fila1'><td colspan=2><b> TOTAL LABORATORIO : ".$wlab."</b></td><td align=right><b>".number_format($tot[0],2,'.',',')."</b></td><td align=right><b>".number_format($tot[1],2,'.',',')."</b></td><td align=right><b>".number_format($tot[3],2,'.',',')."</b></td><td align=right><b>".number_format($tot[2],2,'.',',')."</b></td><td colspan=3></td></tr>";
				echo "<tr class='encabezadotabla'><td colspan=9><b>LABORATORIO : ".$row[0]."</b></td></tr>";
				$tot[0]=0;
				$tot[1]=0;
				$tot[2]=0;
				$tot[3]=0;
				$wlab=$row[0];
			}
			$costoT=$row[3] * $row[4];
			if($row[3] > 0)
			{
				$tot[0] += $row[3];
				$tot[1] += $row[4];
				$tot[2] += $row[5];
				$tot[3] += $costoT;
				$totG[0] += $row[3];
				$totG[1] += $row[4];
				$totG[2] += $row[5];
				$totG[3] += $costoT;
			}
			if(strtoupper($wtip) == "D")
			{


				echo "<tr class='".$color."'>";
				echo "<td>".$row[1]."</td>";
				echo "<td>".$row[2]."</td>";
				echo "<td align=right>".number_format($row[3],2,'.',',')."</td>";
				echo "<td align=right>".number_format($row[4],2,'.',',')."</td>";
				echo "<td align=right>".number_format($costoT,2,'.',',')."</td>";
				echo "<td align=right>".number_format($row[5],2,'.',',')."</td>";
				if(strtoupper($wtip) == "D")
                {
					echo "<td align=right>".$row[8]."</td>";

                    $wvalor_ult_venta = '';
                    $cod_art = $row['Artcod']."-".$row['Artnom']; //Se estructura el codigo del articulo con el nombre
                    $cco = $row['Karcco']."-".$row['Ccodes']; //Se estructura el codigo del cco con el nombre del cco

                    //Verifica si el articulo asociado al cco esta en el arreglo
                    if(array_key_exists($cod_art, $array_ult_venta) && array_key_exists($cco, $array_ult_venta[$cod_art]))
					{
						$wvalor_ult_venta = $array_ult_venta[$cod_art][$cco];
					}
					else
					{
						$wvalor_ult_venta = 0;
					}

                    echo "<td align=right>".number_format($wvalor_ult_venta,2,'.',',')."</td>"; //Ultimo precio de venta

                }


				echo "<td>".$row[7]."</td>";
				echo "</tr>";
			}
		}
		echo "<tr class='fila1'><td colspan=2><b> TOTAL LABORATORIO : ".$wlab."</b></td><td align=right><b>".number_format($tot[0],2,'.',',')."</b></td><td align=right><b>".number_format($tot[1],2,'.',',')."</b></td><td align=right><b>".number_format($tot[3],2,'.',',')."</b></td><td align=right><b>".number_format($tot[2],2,'.',',')."</b></td><td colspan=3 ></td></tr>";
		echo "<tr  class='encabezadotabla'><td colspan=2><b> TOTAL GENERAL : </b></td><td align=right><b>".number_format($totG[0],2,'.',',')."</b></td><td align=right><b>".number_format($totG[1],2,'.',',')."</b></td><td align=right><b>".number_format($totG[3],2,'.',',')."</b></td><td align=right><b>".number_format($totG[2],2,'.',',')."</b></td><td colspan=3 ></td></tr>";
		echo "</table></center>";
	}
}
?>
</body>
</html>
