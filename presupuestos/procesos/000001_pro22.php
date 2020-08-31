<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Montaje de Informacion de Translados del Almacen</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro22.php Ver. 2016-12-22</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro22.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	

	

	if(!isset($wanop) or !isset($wper1) or !isset($wemp) or $wemp == "Seleccione")
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>MONTAJE DE INFORMACION DE TRANSLADOS DEL ALMACEN</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o  Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Empresa de  Proceso</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Empcod,Empdes  from ".$empresa."_000153 order by Empcod";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wemp'>";
			echo "<option>Seleccione</option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}
			echo "</select>";
		}
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=2><input type='RADIO' name='wtipoi' value=1 checked>Unix<input type='RADIO' name='wtipoi' value=2>Matrix</td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		if($wtipoi == 1)
		{
			$er = -1;
			$error=array();
			$wemp = substr($wemp,0,2);
			$query = "delete from ".$empresa."_000002  ";
			$query = $query."  where almano =  ".$wanop;
			$query = $query."    and almmes =  ".$wper1;
			$query = $query."    and almemp = '".$wemp."' ";
			$err = mysql_query($query,$conex);
			$query = "SELECT query, Odbc from ".$empresa."_000049  ";
			$query = $query."  where codigo =  5";
			$query = $query."    and Empresa = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Query Especificado Consulte la tabla ".$empresa."_000049");
			$row = mysql_fetch_array($err);
			$ODBC = $row[1];
			$conex_o = odbc_connect($ODBC,'','');
			$query = $row[0];
			$query=str_replace("ANO",$wanop,$query);
			$query=str_replace("MES",$wper1,$query);
			$err_o = odbc_do($conex_o,$query);
			$campos= odbc_num_fields($err_o);
			$count=0;
			while (odbc_fetch_row($err_o))
			{
				$odbc=array();
				for($m=1;$m<=$campos;$m++)
				{
					$odbc[$m-1]=odbc_result($err_o,$m);
				}
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$query = "SELECT query, Odbc from ".$empresa."_000049  ";
				$query = $query."  where codigo = 6";
				$query = $query."    and Empresa = '".$wemp."' ";
				$err = mysql_query($query,$conex) or die("No Existe el Query Especificado Consulte la tabla ".$empresa."_000049");
				$row = mysql_fetch_array($err);
				$ODBC = $row[1];
				$conex_o = odbc_connect($ODBC,'','');
				$query = $row[0];
				$query=str_replace("ANO",$wanop,$query);
				$query=str_replace("MES",$wper1,$query);
				$query=str_replace("ART",$odbc[5],$query);
				$err_o1 = odbc_do($conex_o,$query);
				$promedio=odbc_result($err_o1,1);
				if($odbc[0] == "102")
				{
					$odbc[8]=$odbc[8]*(-1);
					$odbc[9]=$promedio*(-1);
					$odbc[10]=$odbc[8]*$promedio;
				}
				else
				{
					if($promedio == "")
						$promedio=0;
					$odbc[9]=$promedio;
					$odbc[10]=$odbc[8]*$promedio;
				}
				
				$query = "SELECT Paarpn, Paaccf from ".$empresa."_000120 ";
				$query = $query." where Paacue = '".$odbc[7]."'";
				$query = $query. "  and Paacci = '".$odbc[1]."'";
				$query = $query."   and Paaemp = '".$wemp."' ";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if ($num1 >  0)
				{
					$row1 = mysql_fetch_array($err1);
					$wcodpre = $row1[0];
					$odbc[1] = $row1[1];
				}
				else
				{
					$er++;
					$error[$er][0] = $odbc[7];
					$error[$er][1] = $odbc[1];
					$wcodpre=".";
				}
				$query = "insert ".$empresa."_000002 (medico,fecha_data,hora_data,almemp,almcco,almano,almmes,almcpr,almcod,almdes,almcue,almcan,almcun,almcto,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$odbc[1]."',".$odbc[2].",".$odbc[3].",'".$wcodpre."','".$odbc[5]."','".$odbc[6]."','".$odbc[7]."',".$odbc[8].",".$odbc[9].",".$odbc[10].",'C-".$empresa."')";
				$err1 = mysql_query($query,$conex) or die("Error en insercion Tabla 22  ".$query."  ".mysql_errno().":".mysql_error());
				$count++;
				echo "  REGISTROS INSERTADOS : ".$count."<BR>";
			}
			echo "<B>REGISTROS ADICIONADOS : ".$count."</B><BR><BR>";
			echo "Numero de Errores : ".$er."<br>";
			for($m=0;$m<=$er;$m++)
			{
				echo "<B> CUENTA : </B>".$error[$m][0]." <B>CENTRO DE COSTOS : </B>".$error[$m][1]."<BR><BR>";
			}
		}
		else
		{
			$er = -1;
			$error=array();
			$wemp = substr($wemp,0,2);
			$query = "delete from ".$empresa."_000002  ";
			$query = $query."  where almano =  ".$wanop;
			$query = $query."    and almmes =  ".$wper1;
			$query = $query."    and almemp = '".$wemp."' ";
			$err = mysql_query($query,$conex);
			$query = "SELECT query, Odbc from ".$empresa."_000049  ";
			$query = $query."  where codigo =  5";
			$query = $query."    and Empresa = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Query Especificado Consulte la tabla ".$empresa."_000049");
			$row = mysql_fetch_array($err);
			$query = $row[0];
			$query=str_replace("ANO",$wanop,$query);
			$query=str_replace("MES",$wper1,$query);
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "SELECT Paarpn, Paaccf from ".$empresa."_000120 ";
					$query = $query." where Paacue = '".$row[5]."'";
					$query = $query. "  and Paacci = '".$row[2]."'";
					$query = $query."   and Paaemp = '".$wemp."' ";
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					if ($num1 >  0)
					{
						$row1 = mysql_fetch_array($err1);
						$wcodpre = $row1[0];
						$row[0] = $row1[1];
					}
					else
					{
						$er++;
						$error[$er][0] = $row[5];
						$error[$er][1] = $row[2];
						$wcodpre=".";
					}
					$query = "insert ".$empresa."_000002 (medico,fecha_data,hora_data,almemp,almcco,almano,almmes,almcpr,almcod,almdes,almcue,almcan,almcun,almcto,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row[2]."',".$wanop.",".$wper1.",'".$wcodpre."','".$row[3]."','".$row[4]."','".$row[5]."',".$row[6].",".$row[8].",".$row[7].",'C-".$empresa."')";
					$err1 = mysql_query($query,$conex) or die("Error en insercion Tabla 22  ".$query."  ".mysql_errno().":".mysql_error());
					$count++;
					echo "  REGISTROS INSERTADOS : ".$count."<BR>";
				}
			}
			echo "<B>REGISTROS ADICIONADOS : ".$count."</B><BR><BR>";
			echo "Numero de Errores : ".$er."<br>";
			for($m=0;$m<=$er;$m++)
			{
				echo "<B> CUENTA : </B>".$error[$m][0]." <B>CENTRO DE COSTOS : </B>".$error[$m][1]."<BR><BR>";
			}
		}
	}
}
?>
</body>
</html>
