<html>
<head>
  	<title>MATRIX PLANILLA DE INVENTARIO</title>
  	
<style type="text/css">
table.sample {
	border-width: 1px;
	border-style: solid;
	border-color: gray;
	border-collapse: separate;
}
table.sample td {
	border-width: 1px;
	border-style: solid;
	border-color: gray;
}

</style>
  	
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>CONTEO DE INVENTARIO CENTRAL DE MEZCLAS</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b>planilla2.php Ver. 1.00</b></font></tr></td></table></br>
</center> 
<?php
include_once("conex.php");
include_once("root/comun.php");
// se convierte en la variable empresa ya que $empresa=cenpro
$empresa = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cenmez" );
$bdMovhos  = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");


session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	

	


	echo "<form name='planilla' action='planilla2.php?wemp_pmla=".$wemp_pmla."' method=post>";
	echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
	//echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";

	if (!isset($pintar))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha de inventario</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='fec' value='".date('Y-m-d')."' size=10 ></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>IMPRIMIR SALDOS (S/N)</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='pintar' value='S' size=1 ></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>INGRESAR CONTEO (S/N)</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='almacenar' value='N' size=1 ></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>MODIFICAR CONTEO SI EXISTE (S/N)</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='reemplazar' value='N' size=1 ></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		if (isset($grabar))
		{
			for ($i=0;$i<count($insumo);$i++)
			{
				$q = " UPDATE " . $empresa . "_000017 "
				. "    SET invcon='".$insumo[$i]['can']."' "
				. "  WHERE invcod='".$insumo[$i]['cod']."' "
				. "  and invfec='".$fec."' ";
				$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
			}
			for ($i=0;$i<count($producto);$i++)
			{
				$q = " UPDATE " . $empresa . "_000017 "
				. "    SET invcon='".$producto[$i]['can']."' "
				. "  WHERE invcod='".$producto[$i]['cod']."' "
				. "  and invfec='".$fec."' ";
				$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
			}
		}
		echo "<table border=0 align=center>";

		echo "<tr><td bgcolor=#dddddd><font face='tahoma'><b>FECHA : </b>".$fec."</td></tr>";

		//Buscamos los insumos primero
		$query = "SELECT Invcod, Artcom, Invcan, Artuni, Unides, Invcon, Appcos";
		$query .=" from ".$empresa."_000017, ".$bdMovhos."_000026, ".$bdMovhos."_000027, ".$empresa."_000009 ";
		$query .= " where  Invfec = '".$fec."' " ;
		$query .= "   and  Invpro = 'off' ";
		$query .= "   and  Invcod = Artcod ";
		$query .= "   and  Artuni = Unicod ";
		$query .= "   and  Apppre = Invcod ";
		$query .= "   and  Appest = 'on' ";
		$query .= "   ORDER BY 2 ";

		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);

		echo "</table></br><table align=center class='sample' cellspacing=0>";
		if ($pintar=='S' || $pintar=='s')
		{
			$colspan='9';
		}
		else
		{
			$colspan='7';
		}
		echo "<tr><td align=center bgcolor=#999999 colspan='".$colspan."'><font face='tahoma' size=2><b>INSUMOS</b></font></td>";
		echo "<tr><td align=center bgcolor=#999999><font face='tahoma' size=1><b>ARTICULO</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>UNIDAD</b></font></td>";
		if ($pintar=='S' || $pintar=='s')
		{
			echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>CANTIDAD</b></font></td>";
		}
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>CONTEO 1</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>CONTEO 2</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>CONTEO 3</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>DIFERENCIA</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>COSTO FALTANTE</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>COSTO SOBRANTE</b></font></td>";

		$sum=0;
		$sob1=0;
		$fal1=0;

		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);

			if(is_int($i/2))
			{
				$color='#FFFFFF';
			}
			else
			{
				$color="#dddddd";
			}

			echo "<tr>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[0]."-".$row[1]."</font></td>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[3]."-".$row[4]."</font></td>";
			if ($pintar=='S' || $pintar=='s')
			{
				echo "<td bgcolor=".$color." align='right'><font face='tahoma' size=2>".number_format($row[2],2,'.',',')."</font></td>";
			}
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>&nbsp;</font></td>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>&nbsp;</font></td>";

			echo "<input type='hidden' name='insumo[".$i."][cod]' value='".$row[0]."'></font></td>";

			if (($almacenar=='S' || $almacenar=='s') and $row[5]==0)
			{
				echo "<td bgcolor=".$color."><font face='tahoma' size=2><input type='TEXT' name='insumo[".$i."][can]' size=10 onkeypress='if ((event.keyCode < 46 || event.keyCode > 57) & event.keyCode != 13) event.returnValue = false'></font></td>";
			}
			else if (($almacenar=='S' || $almacenar=='s') and ($reemplazar=='S' || $reemplazar=='s'))
			{
				echo "<td bgcolor=".$color."><font face='tahoma' size=2><input type='TEXT' name='insumo[".$i."][can]' value='".$row[5]."' size=10 onkeypress='if ((event.keyCode < 46 || event.keyCode > 57) & event.keyCode != 13) event.returnValue = false'></font></td>";
			}
			else if ((($almacenar=='S' || $almacenar=='s') and ($reemplazar!='S' && $reemplazar!='s' )) or ($almacenar!='S' && $almacenar!='s'))
			{
				echo "<td bgcolor=".$color."><font face='tahoma' size=2>".number_format($row[5],2,'.',',')."</font></td>";
			}
			else
			{
				echo "<td bgcolor=".$color."><font face='tahoma' size=2>&nbsp;</font></td>";
			}

			$dif=$row[2]-$row[5];

			echo "<td bgcolor=".$color." align='right'><font face='tahoma' size=2>".number_format($dif,2,'.',',')."</font></td>";


			if($dif>=0)
			{

				echo "<td bgcolor=".$color." align='right' ><font face='tahoma' size=2>".number_format(($dif*$row[6]),2,'.',',')."</font></td>";
				echo "<td bgcolor=".$color." align='right' ><font face='tahoma' size=2>".number_format(0,2,'.',',')."</font></td>";
				$fal1=$fal1+($dif*$row[6]);
			}
			else
			{
				echo "<td bgcolor=".$color." align='right' ><font face='tahoma' size=2>".number_format(0,2,'.',',')."</font></td>";
				echo "<td bgcolor=".$color." align='right' ><font face='tahoma' size=2>".number_format(($dif*$row[6]*-1),2,'.',',')."</font></td>";
				$sob1=$sob1+($dif*$row[6]*-1);
			}

			echo "</tr>";

			$sum=$sum+$row[5];
		}

		echo "<tr><td align=center bgcolor=#999999 colspan='".($colspan-2)."'><font face='tahoma' size=1><b>TOTAL</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>".number_format($fal1,2,'.',',')."</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>".number_format($sob1,2,'.',',')."</b></font></td></tr>";

		//Buscamos los productos
		$query = "SELECT Invcod, Artcom, Invcan, Artuni, Unides, Invcon, karcos ";
		$query .=" from ".$empresa."_000002, ".$empresa."_000017,".$bdMovhos."_000027, ".$empresa."_000005 ";
		$query .= " where  Invfec = '".$fec."' " ;
		$query .= "   and  Invpro = 'on' ";
		$query .= "   and  Invcod = Artcod ";
		$query .= "   and  Artuni = Unicod ";
		$query .= "   and  Karcod = Artcod";
		$query .= "   ORDER BY 2 ";

		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);

		echo "</table></br><table class='sample' align=center cellspacing=0> ";
		echo "<tr><td align=center bgcolor=#999999 colspan='".$colspan."'><font face='tahoma' size=2><b>PRODUCTOS</b></font></td>";
		echo "<tr><td align=center bgcolor=#999999><font face='tahoma' size=1><b>ARTICULO</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>UNIDAD</b></font></td>";
		if ($pintar=='S' || $pintar=='s')
		{
			echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>CANTIDAD</b></font></td>";
		}
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>CONTEO 1</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>CONTEO 2</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>CONTEO 3</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>DIFERENCIA</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>COSTO FALTANTE</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=1><b>COSTO SOBRANTE</b></font></td>";


		$sob2=0;
		$fal2=0;


		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);

			if(is_int($i/2))
			{
				$color='#FFFFFF';
			}
			else
			{
				$color="#dddddd";
			}

			echo "<tr>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[0]."-".$row[1]."</font></td>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[3]."-".$row[4]."</font></td>";
			if ($pintar=='S' || $pintar=='s')
			{
				echo "<td bgcolor=".$color." align='right'><font face='tahoma' size=2>".number_format($row[2],2,'.',',')."</font></td>";
			}
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>&nbsp;</font></td>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>&nbsp;</font></td>";

			echo "<input type='hidden' name='producto[".$i."][cod]' value='".$row[0]."'></font></td>";

			if (($almacenar=='S' || $almacenar=='s') and $row[5]==0)
			{
				echo "<td bgcolor=".$color."><font face='tahoma' size=2><input type='TEXT' name='producto[".$i."][can]' size=10 onkeypress='if ((event.keyCode < 46 || event.keyCode > 57) & event.keyCode != 13) event.returnValue = false'></font></td>";
			}
			else if (($almacenar=='S' || $almacenar=='s') and ($reemplazar=='S' || $reemplazar=='s'))
			{
				echo "<td bgcolor=".$color."><font face='tahoma' size=2><input type='TEXT' name='producto[".$i."][can]' value='".$row[5]."' size=10 onkeypress='if ((event.keyCode < 46 || event.keyCode > 57) & event.keyCode != 13) event.returnValue = false'></font></td>";
			}
			else if ((($almacenar=='S' || $almacenar=='s') and ($reemplazar!='S' && $reemplazar!='s')) or ($almacenar!='S' && $almacenar!='s'))
			{
				echo "<td bgcolor=".$color."><font face='tahoma' size=2>".number_format($row[5],2,'.',',')."</font></td>";
			}
			else
			{
				echo "<td bgcolor=".$color."><font face='tahoma' size=2>&nbsp;</font></td>";
			}

			$dif=$row[2]-$row[5];

			echo "<td bgcolor=".$color." align='right'><font face='tahoma' size=2>".number_format($dif,2,'.',',')."</font></td>";

			if($row[6]==0)
			{
				//debo buscar el costo promedio de dosis adaptadas o nutriciones
				//averiguo las cosas de las que esta echa esa dosis adaptada

				$query = "SELECT Pdeins, Pdecan ";
				$query .=" from ".$empresa."_000003 ";
				$query .= " where  Pdepro = '".$row[0]."' " ;

				$errp = mysql_query($query,$conex);
				$nump = mysql_num_rows($errp);

				for ($j=0;$j<$nump;$j++)
				{
					$rowp = mysql_fetch_array($errp);

					//para cada insumo averiguo un psoble costo
					$query = "SELECT Appcos, Appcnv ";
					$query .=" from ".$empresa."_000009 ";
					$query .= " where  Appcod = '".$rowp[0]."' " ;

					$erri = mysql_query($query,$conex);
					$rowi = mysql_fetch_array($erri);
					$row[6] = (isset($rowi[1])) ? $row[6]+($rowi[0]*$rowp[1]/$rowi[1]) : null;
				}
			}

			if($dif>=0)
			{

				echo "<td bgcolor=".$color." align='right' ><font face='tahoma' size=2>".number_format(($dif*$row[6]),2,'.',',')."</font></td>";
				echo "<td bgcolor=".$color." align='right' ><font face='tahoma' size=2>".number_format(0,2,'.',',')."</font></td>";
				$fal2=$fal2+($dif*$row[6]);
			}
			else
			{
				echo "<td bgcolor=".$color." align='right' ><font face='tahoma' size=2>".number_format(0,2,'.',',')."</font></td>";
				echo "<td bgcolor=".$color." align='right' ><font face='tahoma' size=2>".number_format(($dif*$row[6]*-1),2,'.',',')."</font></td>";
				$sob2=$sob2+($dif*$row[6]*-1);
			}

			echo "</tr>";

			$sum=$sum+$row[5];
		}

		$sob=$sob1+$sob2;
		$fal=$fal1+$fal2;

		echo "<tr><td align=center bgcolor=#999999 colspan='".($colspan-2)."'><font face='tahoma' size=1><b>TOTAL</b></font></td>";
		echo "<td  bgcolor=#999999 align='right' ><font face='tahoma' size=1><b>".number_format($fal2,2,'.',',')."</b></font></td>";
		echo "<td bgcolor=#999999 align='right' ><font face='tahoma' size=1><b>".number_format($sob2,2,'.',',')."</b></font></td></tr>";

		echo "<tr><td align=center bgcolor=#999999 colspan='".($colspan-2)."'><font face='tahoma' size=1><b>TOTAL INSUMOS + PRODUCTOS</b></font></td>";
		echo "<td  bgcolor=#999999 align='right' ><font face='tahoma' size=1><b>".number_format($fal,2,'.',',')."</b></font></td>";
		echo "<td  bgcolor=#999999 align='right' ><font face='tahoma' size=1><b>".number_format($sob,2,'.',',')."</b></font></td></tr>";

		if(($almacenar=='S' || $almacenar=='s') and  $sum==0)
		{
			echo "<input type='hidden' name='grabar' value='1'></font></td>";
			echo "<input type='hidden' name='fec' value='".$fec."'></font></td>";
			echo "<input type='hidden' name='pintar' value='".$pintar."'></font></td>";
			echo "<input type='hidden' name='almacenar' value='S'></font></td>";
			echo "<input type='hidden' name='reemplazar' value='S'></font></td>";
			echo "<tr><td bgcolor=#cccccc  colspan='".$colspan."' align=center><input type='submit' value='Grabar'></td></tr></table>";
		}
		else if (($almacenar=='S' || $almacenar=='s') and ($reemplazar=='S' || $reemplazar=='s'))
		{
			echo "<input type='hidden' name='grabar' value='1'></font></td>";
			echo "<input type='hidden' name='grabar' value='1'></font></td>";
			echo "<input type='hidden' name='fec' value='".$fec."'></font></td>";
			echo "<input type='hidden' name='pintar' value='".$pintar."'></font></td>";
			echo "<input type='hidden' name='almacenar' value='S'></font></td>";
			echo "<input type='hidden' name='reemplazar' value='S'></font></td>";
			echo "<tr><td bgcolor=#cccccc  colspan='".$colspan."' align=center><input type='submit' value='Modificar'></td></tr></table>";
		}

	}

}
?>
</body>
</html>