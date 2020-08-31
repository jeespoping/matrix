<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	$query= "select descripcion from det_formulario where medico='nosiye' and codigo='000001' AND (tipo='0' or tipo='1' or tipo='2')";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if ($num>0)
	{
		ECHO "<table border=1 width=500 align='center'><TR><TD COLSPAN=4><b>REPORTE GENERAL</TD></TR>";
		echo "<tr><td><B>CAMPO</td>";
		ECHO "<td><B>MAX</td>";
		ECHO "<td><B>MIN</td>";
		ECHO "<td><B>AVG</td></TR>";
		for ($j=0;$j<$num;$j++)
		{
			$row = mysql_fetch_array($err);
			ECHO "<tr><td>".$row[0]."</td>";
			$query1="select Max(".$row[0].") from nosiye_000001 where ".$row[0]." <> '0' and ".$row[0]." <> 'NO APLICA'";
			//echo $query1."<br>";
			$err1 = mysql_query($query1,$conex);
			$num1 = mysql_num_rows($err1);
			if ($num1>0)
			{
				$row1 = mysql_fetch_array($err1);
				ECHO "<td>".$row1[0]."</td>";
			}
			$query1="select Min(".$row[0].") from nosiye_000001 where ".$row[0]." <> '0' and ".$row[0]." <> 'NO APLICA'";
			//echo $query1."<br>";
			$err1 = mysql_query($query1,$conex);
			$num1 = mysql_num_rows($err1);
			if ($num1>0)
			{
				$row1 = mysql_fetch_array($err1);
				ECHO "<td>".$row1[0]."</td>";
			}
			$query1="select Avg(".$row[0].") from nosiye_000001 where ".$row[0]." <> '0' and ".$row[0]." <> 'NO APLICA'";
			//echo $query1."<br>";
			$err1 = mysql_query($query1,$conex);
			$num1 = mysql_num_rows($err1);
			iF ($num1>0)
			{
				$row1 = mysql_fetch_array($err1);
				ECHO "<td>".$row1[0]."</td>";
			}
			ECHO "</TR>";
		}	
		
		
	}
	
	$query= "select descripcion from det_formulario where medico='nosiye' and codigo='000001' AND (tipo='5' or tipo='9')";
	$err1 = mysql_query($query,$conex);
	$num1 = mysql_num_rows($err1);
	if ($num1>0)
	{
		for ($j=0;$j<$num1;$j++)
		{
			$row1 = mysql_fetch_array($err1);
			ECHO "<table border=1 width=500 align='center'><tr><td colspan=2><b>".strtoupper($row1[0])."</b></td></tr>";
			echo "<tr><td><B>Valor</b></td>";
			ECHO "<td><b>Frec.</b></td>";
			$query= "select Distinct(".$row1[0].") from nosiye_000001 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				for ($h=0;$h<$num;$h++)
				{
					$row = mysql_fetch_array($err);
					ECHO "<tr><td>".$row[0]."</td>";
					$query2="select Count(*) from nosiye_000001 where ".$row1[0]."='".$row[0]."'";
					//echo $query1."<br>";
					$err2 = mysql_query($query2,$conex);
					$num2 = mysql_num_rows($err2);
					if ($num2>0)
					{
						$row2 = mysql_fetch_array($err2);
						ECHO "<td>".$row2[0]."</td>";
					}
					ECHO "</TR>";
				}
			}
		}
	}
		
	
	
	
	$query= "select descripcion from det_formulario where medico='nosiye' and codigo='000001' AND (tipo='0' or tipo='1' or tipo='2')";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if ($num>0)
	{
		ECHO "<table border=1 width=500 align='center'><TR><TD COLSPAN=4><b>REPORTE PACIENTE MUERTO</TD></TR>";
		echo "<tr><td><B>CAMPO</td>";
		ECHO "<td><B>MAX</td>";
		ECHO "<td><B>MIN</td>";
		ECHO "<td><B>AVG</td></TR>";
		for ($j=0;$j<$num;$j++)
		{
			$row = mysql_fetch_array($err);
			ECHO "<tr><td>".$row[0]."</td>";
			$query1="select Max(".$row[0].") from nosiye_000001 where ".$row[0]." <> '0' and ".$row[0]." <> 'NO APLICA' and salida='02-muerto' ";
			//echo $query1."<br>";
			$err1 = mysql_query($query1,$conex);
			$num1 = mysql_num_rows($err1);
			if ($num1>0)
			{
				$row1 = mysql_fetch_array($err1);
				ECHO "<td>".$row1[0]."</td>";
			}
			$query1="select Min(".$row[0].") from nosiye_000001 where ".$row[0]." <> '0' and ".$row[0]." <> 'NO APLICA' and salida='02-muerto'";
			//echo $query1."<br>";
			$err1 = mysql_query($query1,$conex);
			$num1 = mysql_num_rows($err1);
			if ($num1>0)
			{
				$row1 = mysql_fetch_array($err1);
				ECHO "<td>".$row1[0]."</td>";
			}
			$query1="select Avg(".$row[0].") from nosiye_000001 where ".$row[0]." <> '0' and ".$row[0]." <> 'NO APLICA' and salida='02-muerto'";
			//echo $query1."<br>";
			$err1 = mysql_query($query1,$conex);
			$num1 = mysql_num_rows($err1);
			iF ($num1>0)
			{
				$row1 = mysql_fetch_array($err1);
				ECHO "<td>".$row1[0]."</td>";
			}
			ECHO "</TR>";
		}	
		
		
	}

	$query= "select descripcion from det_formulario where medico='nosiye' and codigo='000001' AND (tipo='5' or tipo='9')";
	$err1 = mysql_query($query,$conex);
	$num1 = mysql_num_rows($err1);
	if ($num1>0)
	{
		for ($j=0;$j<$num1;$j++)
		{
			$row1 = mysql_fetch_array($err1);
			ECHO "<table border=1 width=500 align='center'><tr><td colspan=2><b>".strtoupper($row1[0])."</b></td></tr>";
			echo "<tr><td><B>Valor</b></td>";
			ECHO "<td><b>Frec.</b></td>";
			$query= "select Distinct(".$row1[0].") from nosiye_000001 where salida='02-muerto'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				for ($h=0;$h<$num;$h++)
				{
					$row = mysql_fetch_array($err);
					ECHO "<tr><td>".$row[0]."</td>";
					$query2="select Count(*) from nosiye_000001 where ".$row1[0]."='".$row[0]."' and salida='02-muerto'";
					//echo $query1."<br>";
					$err2 = mysql_query($query2,$conex);
					$num2 = mysql_num_rows($err2);
					if ($num2>0)
					{
						$row2 = mysql_fetch_array($err2);
						ECHO "<td>".$row2[0]."</td>";
					}
					ECHO "</TR>";
				}
			}
		}
	}
}
?>
</body>
</html>