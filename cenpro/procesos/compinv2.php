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
include_once("conex.php");
function calcularValorProducto($cantidad, $lote, &$concepto1, &$documento1, $concepto)
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

			$query = "SELECT Appcos, Appcnv, Tipmat from ".$empresa."_000009, ".$empresa."_000001, ".$empresa."_000002 ";
			$query .= " where  Apppre='".$row2[2]."'";
			$query .= " and  Appcod= artcod ";
			$query .= " and  Arttip= tipcod ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if( $row[2]!='on')
			{

				echo "<tr>";
				echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".$concepto1."*</b></font></td>";
				echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".$documento1."</b></font></td>";
				echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".$lote."</b></font></td>";
				echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".$row2[2]."</b></font></td>";
				echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".$row2[1]*$cantidad/$rowp[0]."</b></font></td>";
				echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".$row[1]."</b></font></td>";
				echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".$row[0]."</b></font></td>";
				echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".number_format((double)($row2[1]*$cantidad*$row[0]/($row[1]*$rowp[0])),2,'.',',')."</b></font></td>";
				echo "</tr>";
			}
		}
		else
		{
			$res=calcularValorProducto(($row2[1]*$cantidad/$rowp[0]),$row2[0],$concepto1, $documento1, $concepto);
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
	echo "<form name='compinv' action='compinv2.php' method=post>";
	

	

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

		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>CONCEPTO</b></font></td>";
		echo "<td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>MOVIMIENTO</b></font></td>";
		echo "<td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>ARTICULO</b></font></td>";
		echo "<td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>INSUMO</b></font></td>";
		echo "<td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>CANTIDAD</b></font></td>";
		echo "<td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>FACTOR</b></font></td>";
		echo "<td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>COSTO</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>VALOR</b></font></td></tr>";


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
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);

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
					$res=calcularValorProducto($row1[3],$row1[4],$row[0], $row[2], $row2[0]);

				}
				else if($row1[1] != 'on')
				{
					$exp=explode('-',$row1[2]);
					//consultamos los valores para insumos
					$query = "SELECT Appcos, Appcnv from ".$empresa."_000009 ";
					$query .= " where  Apppre='".$exp[0]."'";
					$err2 = mysql_query($query,$conex);
					$num2 = mysql_num_rows($err2);
					$row2 = mysql_fetch_array($err2);
					echo "<tr>";
					echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".$row[0]."</b></font></td>";
					echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".$row[2]."</b></font></td>";
					echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".$row1[0]."</b></font></td>";
					echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".$row1[2]."</b></font></td>";
					echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".$row1[3]."</b></font></td>";
					echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".$row2[1]."</b></font></td>";
					echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".$row2[0]."</b></font></td>";
					echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".number_format((double)($row1[3]*$row2[0]/$row2[1]),2,'.',',')."</b></font></td>";
					echo "</tr>";

				}
			}

		}
		echo"</table>";
	}
}
?>
</body>
</html>