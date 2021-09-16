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
include_once("root/comun.php");
function calcularValorProducto($cantidad, $lote, &$concepto1, &$documento1, $concepto)
{
	global $conex;
	global $empresa;

	$query = "SELECT Mdeart, Mdecan, Mdepre from ".$wcenpro."_000007 ";
	$query .= " where  Mdecon='".$concepto."'";
	$query .= "   and  Mdenlo='".$lote."'";
	//echo $query;
	$err2 = mysql_query($query,$conex);
	$num2 = mysql_num_rows($err2);

	for ($i=0; $i<$num2; $i++)
	{
		$row2 = mysql_fetch_array($err2);

		$exp=explode('-',$lote);
		$query = "SELECT plocin from ".$wcenpro."_000004 ";
		$query .= " where  plopro='".$exp[1]."' and plocod='".$exp[0]."' ";
		$errp = mysql_query($query,$conex);
		$nump = mysql_num_rows($errp);
		$rowp = mysql_fetch_array($errp);

		if($row2[2]!='')
		{

			$query = "SELECT Appcos, Appcnv, Tipmat from ".$wcenpro."_000009, ".$wcenpro."_000001, ".$wcenpro."_000002 ";
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
	$institucion = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
	encabezado( "REPORTE DE MOVIMIENTOS DE UN INSUMO", $wactualiz, $institucion->baseDeDatos );
	$key = substr($user,2,strlen($user));
	echo "<form name='Mov1' action='Mov1.php?wemp_pmla=".$wemp_pmla."' method=post>";
	$conex = obtenerConexionBD("matrix");
	$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wcenpro = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wfeci) or !isset($wfecf) or !isset($wins))
	{
		echo "<center><table border=0>";
		//echo "<tr><td class='texto5' colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		//echo "<tr><td class='titulo1' colspan=2>REPORTE DE MOVIMIENTOS DE UN INSUMO</td></tr>";
		echo "<tr><td class='texto4'>Codigo de Articulo</td>";
		echo "<td class='texto4'><input type='TEXT' name='wins' size=10 maxlength=10></td></tr>";
		echo "<tr><td class='texto4'>Fecha Inicial</td>";
		echo "<td class='texto4'><input type='TEXT' name='wfeci' size=10 maxlength=10></td></tr>";
		echo "<tr><td class='texto4'>Fecha Final</td>";
		echo "<td class='texto4'><input type='TEXT' name='wfecf' size=10 maxlength=10></td></tr>";
		echo "<tr><td class='texto1'  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$query = "SELECT   Artcom from ".$wmovhos."_000026  ";
		$query .= " where  Artcod='".$wins."' ";
		$query .= "     and   Artest='on' ";
		$query .= " UNION ";
		$query .= "SELECT  Artcom from ".$wcenpro."_000002  ";
		$query .= " where  Artcod='".$wins."' ";
		$query .= "     and   Artest='on' ";

		$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
		$row = mysql_fetch_array($err);

		$query = "SELECT   Appcnv, Appcod from ".$wcenpro."_000009  ";
		$query .= " where  Apppre='".$wins."' ";
		$query .= " UNION ";
		$query .= "SELECT  0 as appcnv,artcod from ".$wcenpro."_000002 ";
		$query .= " where  artcod='".$wins."' ";

		$err2 = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
		$row2 = mysql_fetch_array($err2);

		$wfac=$row2[0];
		echo "<table border=0 align=center>";
		//echo "<tr><td class='titulo1'><b>REPORTE DE MOVIMIENTOS DE UN INSUMO</font> Ver 1.0</b></font></td></tr>";
		echo "<tr><td class='texto4'><font face='tahoma'><b>Insumo: </b>".$wins."-".$row[0]."</td></tr>";
		echo "<tr><td class='texto4'><font face='tahoma'><b>Fecha Inicial : </b>".$wfeci."</td></tr>";
		echo "<tr><td class='texto4'><font face='tahoma'><b>Fecha Final : </b>".$wfecf."</td></tr>";
		echo "<tr><td class='texto4'><font face='tahoma'><b>Factor de conversion: </b>".$wfac."</td></tr>";
		echo "</tr></table><br><br>";

		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>FECHA</b></font></td>";
		echo "<td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>CONCEPTO</b></font></td>";
		echo "<td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>MOVIMIENTO</b></font></td>";
		echo "<td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>ORIGEN</b></font></td>";
		echo "<td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>DESTINO</b></font></td>";
		echo "<td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>LOTE</b></font></td>";
		echo "<td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>COD_RES</b></font></td>";
		echo "<td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>RESPONSABLE</b></font></td>";
		echo "<td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>ENTRADAS (UNIDAD MINIMA)</b></font></td>";
		echo "<td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>SALIDAS  (UNIDAD MINIMA)</b></font></td></tr>";

		//2008-10-06
        //ACA CREO UNA TABLA TEMPORAL CON TODOS LOS MOVIMIENTOS
		$query = "SELECT   Mencon, Menfec, Mendoc, Mdecan, Conind, Connom, Mencco, Menccd, Mdenlo,".$wcenpro."_000007.seguridad,descripcion  from ".$wcenpro."_000006, ".$wcenpro."_000007 , ".$wcenpro."_000008 , usuarios ";
		$query .= " where  Menfec between '".$wfeci."' and '".$wfecf."'";
		$query .= "     and   Mencon=Mdecon ";
		$query .= "     and   Mdedoc=Mendoc ";
		$query .= "     and   Mdeart='".$row2[1]."' ";
		$query .= "     and   Mdepre like '%".$wins."%' ";
		$query .= "     and   Mdecon=Concod ";
		$query .= "     and   Conane<>'on'";
		//$query .= "     and   Mid( ".$wcenpro."_000007.seguridad, 3, instr( ".$empresa."_000007.seguridad, '-' ) + 3 ) = codigo";
		$query .= "		and SUBSTRING( ".$wcenpro."_000007.seguridad FROM INSTR( ".$wcenpro."_000007.seguridad, '-' ) + 1 ) = codigo";
		$query .= " UNION ";
		$query .= "SELECT   Mencon, Menfec, Mendoc, Mdecan, Conind, Connom, Mencco, Menccd, Mdenlo,".$wcenpro."_000007.seguridad,descripcion  from ".$wcenpro."_000006, ".$wcenpro."_000007 , ".$wcenpro."_000008 , usuarios ";
		$query .= " where  Menfec between '".$wfeci."' and '".$wfecf."'";
		$query .= "     and   Mencon=Mdecon ";
		$query .= "     and   Mdedoc=Mendoc ";
		$query .= "     and   Mdeart='".$wins."' ";
		$query .= "     and   Mdecon=Concod ";
		$query .= "     and   Conane<>'on'";
		//$query .= "     and   Mid( ".$wcenpro."_000007.seguridad, 3, instr( ".$empresa."_000007.seguridad, '-' ) + 3 ) = codigo";
		$query .= "		and SUBSTRING( ".$wcenpro."_000007.seguridad FROM INSTR( ".$wcenpro."_000007.seguridad, '-' ) + 1 ) = codigo";
		$query .= "    Order by Menfec ";
		
		$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		echo "<b>Movimientos Totales  : ".$num."</b><br><br>";
		$wtotsa=0;
		$wtoten=0;
		$k=0;
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);

			echo "<tr>";
			echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".$row[1]."</b></font></td>";
			echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".$row[0]."-".$row[5]."</b></font></td>";
			echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".$row[2]."</b></font></td>";
			echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".$row[6]."</b></font></td>";
			echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".$row[7]."</b></font></td>";
			echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".$row[8]."</b></font></td>";
            echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".$row[9]."</b></font></td>";
            echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".$row[10]."</b></font></td>";
			if($row[4]=='1')
			{
				echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".$row[3]."</b></font></td>";
				echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>&nbsp;</b></font></td>";
				$wtoten=$wtoten+$row[3];
			}
			else
			{
				echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>&nbsp;</b></font></td>";
				echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".$row[3]."</b></font></td>";
				$wtotsa=$wtotsa+$row[3];
			}
			echo "</tr>";
		}
		echo "<tr>";
		echo "<td bgcolor=#cccccc align=right COLSPAN=8><font face='tahoma' size=2><b>TOTALES</b></font></td>";
		echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".$wtoten."</b></font></td>";
		echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".$wtotsa."</b></font></td>";
		echo "</tr>";
		echo"</table>";
	}
}
?>
</body>
</html>