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
<tr><td class="texto5"><b>Tkardex.php Ver. 2007-01-09</b></tr></td></table>
</center> 
<?php
include_once("conex.php");

function calcularProducto($cantidad, $lote, $concepto, $tipo)
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
			$can=$row2[1]*$cantidad/$rowp[0];
			if ($tipo==1)
			{
				$q= "   UPDATE ".$empresa."_000014 "
				."      SET Salexi = Salexi-".$can." "
				."    WHERE  Salcod= '".$row2[2]."' "
				."    and  Salano= '2007' "
				."    and  Salmes= '9' ";
			}
			else
			{

				$q= "   UPDATE ".$empresa."_000014 "
				."      SET Salexi =  Salexi+".$can." "
				."    WHERE  Salcod= '".$row2[2]."' "
				."    and  Salano= '2007' "
				."    and  Salmes= '9' ";
			}
			$res1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ACTUALIZAR EL INSUMO EN LA TABLA DE SALDOS".mysql_error());
		}
		else
		{
			$res=calcularProducto($row2[1]*$cantidad/$rowp[0],$row2[0], $concepto, $tipo);
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

///////////////////////////////////////////////////////PROGRAMA/////////////////////////////////////////
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	


	$k=0;

	$hora = (string)date("H:i:s");
	//echo "1er Query Tiempo 1 : ".$hora."<br>";
	//consultamos el tipo del articulo para saber si debe desglosarse en insumos o no
	$query = "CREATE TEMPORARY TABLE if not exists caro1 as SELECT Mendoc, Mencon, Conind from ".$empresa."_000006 A, ".$empresa."_000008  ";
	$query .= "  where  Menano='2007' ";
	$query .= "     and   Menmes='10'";
	$query .= "     and   A.Fecha_data = '2007-10-01'";
	$query .= "     and   A.Hora_data between '01:00:00' and '07:34:44'";
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
			$res=calcularProducto($row1[3], $row1[4], $row2[0], $row1[5]);
		}
		else if($row1[1] != 'on')
		{
			$exp=explode('-',$row1[2]);

			if ($row1[5]==1)
			{
				$q= "   UPDATE ".$empresa."_000014 "
				."      SET Salexi =  salexi-".$row1[3]." "
				."    WHERE  Salcod= '".$exp[0]."' "
				."    and  Salano= '2007' "
				."    and  Salmes= '9' ";
			}
			else
			{
				$q= "   UPDATE ".$empresa."_000014 "
				."      SET Salexi = Salexi+".$row1[3]." "
				."    WHERE  Salcod= '".$exp[0]."' "
				."    and  Salano= '2007' "
				."    and  Salmes= '9' ";
			}
			$res1 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ACTUALIZAR EL INSUMO EN LA TABLA DE SALDOS ".mysql_error());
		}

		echo "REGISTRO PROCESADO NRo : ".$i."<br>";
	}
}
?>
</body>
</html>