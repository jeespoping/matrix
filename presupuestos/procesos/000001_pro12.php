<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro12.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($confirm))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr>";
			echo "<tr><td align=center colspan=2><font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#ff0000 LOOP=-1>ESTA SEGURO DE EJECUTAR ESTE PROCESO ??????</MARQUEE></FONT></td><tr>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTOS</td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION DE $ VARIABLES PRESUPUESTADOS</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A�o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<td bgcolor=#cccccc align=center>CONFIRMACION (S/N)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='confirm' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			if ($confirm=="S" or $confirm=="s")
			{
			$query = "SELECT rescco, resano,resper, sum(resmon) as toting from ".$empresa."_000043 ";
   			$query = $query." WHERE rescpr = '100' ";
   			 $query = $query."   and resano = ".$wanop;
   			$query = $query." GROUP BY rescco, resper ";
  			$query = $query." ORDER BY rescco, resper ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$k=0;
   			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$k++;
					 $row = mysql_fetch_array($err);
					 $query = "SELECT vblccv,vblpor from ".$empresa."_000047 ";
           			 $query = $query." where vblcco = '".$row[0]."'";
           			 $query = $query."   and vblano = ".$wanop;
           			 $err1 = mysql_query($query,$conex);
					 $num1 = mysql_num_rows($err1);
					 $row1 = mysql_fetch_array($err1);
					 $fecha = date("Y-m-d");
					 $hora = (string)date("H:i:s");
					 $monto=$row[3]*$row1[1];
					 $query = "insert ".$empresa."_000043 (medico,fecha_data,hora_data,rescco,rescpr,resano,resper,resmon,resind,seguridad) values ('".$key."','".$fecha."','".$hora."','".$row[0]."','".$row1[0]."',".$row[1].",".$row[2].",".$monto.",'12','C-".$empresa."')";
					 $err1 = mysql_query($query,$conex);
        		}
        		 echo "NUMERO DE REGISTROS ACTUALIZADOS : ".$k."<br>";
        	}
        	else
        	{
				 echo "NUMERO DE REGISTROS ACTUALIZADOS : ".$k."<br>";
        	}
        	}
        }
}		
?>
</body>
</html>
