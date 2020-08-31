<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Valoracion del Proceso de Interrelacion (Desarrollo Organizacional)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro2.php Ver. 2006-07-12</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro2.php' method=post>";
	

	

	if(isset($ok))
	{
		$total=0;
		for ($i=0;$i<$num;$i++)
			if(isset($data[$i][1]))
				$total+=1;
		if($total == $num)
		{
			for ($i=0;$i<$num;$i++)
			{
				$query = "UPDATE proceso_000009 set Codpes=".$data[$i][1]." where Codufu='".$data[$i][0]."' and Coduob='".substr($wusuf,0,strpos($wusuf,"-"))."'";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			}
			echo "<center><table border=0 align=center>";
			echo "<tr><td bgcolor=#999999 align=center><IMG SRC='/MATRIX/images/medical/root/clinica.jpg' ></td><td  align=center bgcolor=#cccccc><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
			echo "<tr><td bgcolor=#999999 colspan=2 align=center><b>VALORACION DEL PROCESO DE INTERRELACION (DESARROLLO ORGANIZACIONAL)</b></td></tr>";
			echo "<tr><td bgcolor=#999999 colspan=2 align=center><b>VALORACION GRABADA</b></td></tr>";
		}
		else
		{
			echo "<input type='HIDDEN' name= 'wusuf' value='".$wusuf."'>";
			echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>VALORACION INCOMPLETA  REVISE !!!!!</MARQUEE></FONT>";
			echo "<center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td></tr>";
		}
		echo "<td bgcolor=#cccccc colspan=7 align=center><input type='submit' value='CONTINUAR'></td><tr>";
		echo"</table></center>";
		unset($ok);
	}
	else
	{
		if(!isset($wusuf))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>VALORACION DEL PROCESO DE INTERRELACION (DESARROLLO ORGANIZACIONAL).</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidades a Cargo</td>";
			echo "<td bgcolor=#cccccc align=center>";
			$query = "SELECT Codigo, Descripcion  from proceso_000008,proceso_000009 ";
			$query .= " where usuario = '".$key."' ";
			$query .= "   and codigo=coduob ";
			$query .= "   and Codpes=0 ";
			$query .= " group by Codigo, Descripcion ";
			$query .= " order by Codigo ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num > 0)
			{
				echo "<select name='wusuf'>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
			}
			else
				echo "TODOS HAN SIDO CALIFICADOS</td></tr>";
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			echo "<table border=0 align=center>";
			echo "<tr><td bgcolor=#999999 align=center><IMG SRC='/MATRIX/images/medical/root/clinica.jpg' ></td><td  align=center bgcolor=#cccccc colspan=6><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
			echo "<tr><td bgcolor=#999999 colspan=7 align=center><b>VALORACION DEL PROCESO DE INTERRELACION (DESARROLLO ORGANIZACIONAL)</b></td></tr>";
			echo "<tr><td bgcolor=#dddddd colspan=7 align=center><b>UNIDAD : ".$wusuf."</b></td></tr>";
			echo "<tr><td bgcolor=#CCCCCC align=center colspan=3><b>UNIDADES</b></td><td bgcolor=#CCCCCC align=center colspan=6><b>GRADO DE INTERRELACION</b></td></tr>";
			echo "<tr><td bgcolor=#999999 align=center><b>NUMERO</b></td><td bgcolor=#999999 align=center><b>UNIDAD </b></td><td bgcolor=#999999 align=center><b>BAJO </b></td><td bgcolor=#999999 align=center><b>MEDIO<br>BAJO</b></td><td bgcolor=#999999 align=center><b>MEDIO</b></td><td bgcolor=#999999 align=center><b>MEDIO<br>ALTO</b></td><td bgcolor=#999999 align=center><b>ALTO</b></td></tr>";
			$query = "SELECT Codufu, Descripcion, Codpes from proceso_000009, proceso_000008 ";
			$query = $query."  where Coduob ='".substr($wusuf,0,strpos($wusuf,"-"))."'";
			$query = $query."      and Codufu = Codigo ";
			$query = $query." order by Codufu";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				$data=array();
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$data[$i][0]=$row[0];
					if($i % 2 == 0)
						$color="#dddddd";
					else
						$color="#cccccc";
					$k=$i + 1;
					echo "<tr><td bgcolor=".$color." align=center>".$k."</td><td bgcolor=".$color.">".$row[1]."</td>";
					if($row[2] == 1)
						echo "<td bgcolor=".$color." align=center>1 <input type='RADIO' name='data[".$i."][1]' value=1 checked></td>";
					else
						echo "<td bgcolor=".$color." align=center>1 <input type='RADIO' name='data[".$i."][1]' value=1></td>";
					if($row[2] == 2)
						echo "<td bgcolor=".$color." align=center>2 <input type='RADIO' name='data[".$i."][1]' value=2 checked></td>";
					else
						echo "<td bgcolor=".$color." align=center>2 <input type='RADIO' name='data[".$i."][1]' value=2></td>";
					if($row[2] == 3)
						echo "<td bgcolor=".$color." align=center>3 <input type='RADIO' name='data[".$i."][1]' value=3 checked></td>";
					else
						echo "<td bgcolor=".$color." align=center>3 <input type='RADIO' name='data[".$i."][1]' value=3></td>";
					if($row[2] == 4)
						echo "<td bgcolor=".$color." align=center>4 <input type='RADIO' name='data[".$i."][1]' value=4 checked></td>";
					else
						echo "<td bgcolor=".$color." align=center>4 <input type='RADIO' name='data[".$i."][1]' value=4></td>";
					if($row[2] == 5)
						echo "<td bgcolor=".$color." align=center>5 <input type='RADIO' name='data[".$i."][1]' value=5 checked></td></tr>";
					else
						echo "<td bgcolor=".$color." align=center>5 <input type='RADIO' name='data[".$i."][1]' value=5></td></tr>";
				}
				echo "<td bgcolor=#cccccc colspan=9 align=center>DATOS OK!! <input type='checkbox' name='ok'></td><tr>";
				echo "<input type='HIDDEN' name= 'wusuf' value='".$wusuf."'>";
				echo "<input type='HIDDEN' name= 'num' value='".$num."'>";
				for ($i=0;$i<$num;$i++)
					echo "<input type='HIDDEN' name= 'data[".$i."][0]' value='".$data[$i][0]."'>";
			}
			echo "<td bgcolor=#999999 colspan=9 align=center><input type='submit' value='ACTUALIZAR'></td><tr>";
			echo"</table>";
		}
	}
}
?>
</body>
</html>