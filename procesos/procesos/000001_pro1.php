<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Grabacion de Encuestas (Desarrollo Organizacional)</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro1.php Ver. 2010-06-21</b></font></td></tr></table>
</center>
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro1.php' method=post>";
	

	

	if(isset($ok))
	{
		$total=0;
		$err=array();
		for ($i=0;$i<$num;$i++)
			if(isset($data[$i][1]) and (($data[$i][1] <= 3 and strlen($data[$i][2]) > 10) or $data[$i][1] > 3))
			{
				$total+=1;
				$bad[$i]=0;
			}
			else
				$bad[$i]=1;
		if($total == $num)
		{
			for ($i=0;$i<$num;$i++)
			{
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$query = "insert proceso_000007 (medico,Fecha_data, Hora_data, Codufu, Coduob, Codenc, Ano, Mes, Codpre, Respuesta, Justificacion, Seguridad ) values ('proceso','".$fecha."','".$hora."','".substr($wusuf,0,strpos($wusuf,"-"))."','".substr($wusuo,0,strpos($wusuo,"-"))."',".substr($wenc,0,strpos($wenc,"-")).",".$wano.",".$wmes.",".$data[$i][0].",".$data[$i][1].",'".$data[$i][2]."','C-proceso')";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			}
			echo "<center><table border=0 align=center>";
			echo "<tr><td bgcolor=#999999 align=center><IMG SRC='/MATRIX/images/medical/root/clinica.png' ></td><td  align=center bgcolor=#cccccc><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
			echo "<tr><td bgcolor=#999999 colspan=2 align=center><b>GRABACION DE ENCUESTAS (DESARROLLO ORGANIZACIONAL)</b></td></tr>";
			echo "<tr><td bgcolor=#999999 colspan=2 align=center><b>ENCUESTA GRABADA</b></td></tr>";
			echo "<input type='HIDDEN' name= 'wenc' value='".$wenc."'>";
			echo "<input type='HIDDEN' name= 'wusuf' value='".$wusuf."'>";
		}
		//echo "<td bgcolor=#cccccc colspan=8 align=center><input type='submit' value='CONTINUAR'></td><tr>";
		echo"</table></center>";
		unset($ok);
		if($total == $num)
			unset($wusuo);
	}
	if(!isset($wenc) or !isset($wusuf) or !isset($wusuo) or $wenc == "")
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>GRABACION DE ENCUESTAS (DESARROLLO ORGANIZACIONAL).</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Encuesta</td>";
		if(!isset($wenc))
		{
			echo "<td bgcolor=#cccccc align=center>";
			$query = "SELECT Codenc,texto from proceso_000004, proceso_000006 where codigo = Codenc and prioridad=1 order by Codenc";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wenc'>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
		}
		else
		{
			echo "<input type='HIDDEN' name= 'wenc' value='".$wenc."'>";
			echo "<td bgcolor=#cccccc align=center>".$wenc."</td></tr>";
		}
		echo "<tr><td bgcolor=#cccccc align=center>Usuario Calificador</td>";
		if(!isset($wusuf))
		{
			echo "<td bgcolor=#cccccc align=center>";
			if($key != "proceso")
				$query = "SELECT Codigo, Descripcion  from proceso_000008 where usuario = '".$key."' order by Codigo";
			else
				$query = "SELECT Codigo, Descripcion  from proceso_000008 order by Codigo";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wusuf'>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
		}
		else
		{
			echo "<input type='HIDDEN' name= 'wusuf' value='".$wusuf."'>";
			echo "<td bgcolor=#cccccc align=center>".$wusuf."</td></tr>";
		}
		if(isset($wusuf) and !isset($wusuo) and isset($wenc) and $wenc != "")
		{
			echo "<tr><td bgcolor=#cccccc align=center>Usuario a Calificar</td>";
			echo "<td bgcolor=#cccccc align=center>";
			$query = "SELECT Ano, Mes  from proceso_000006 where Codenc = '".substr($wenc,0,strpos($wenc,"-"))."' and prioridad=1 ";
			$err = mysql_query($query,$conex);
			$row = mysql_fetch_array($err);
			$wano=$row[0];
			$wmes=$row[1];
			$query = "SELECT proceso_000009.Coduob, Descripcion  from proceso_000009, proceso_000008 where proceso_000009.Codufu='".substr($wusuf,0,strpos($wusuf,"-"))."'  and proceso_000009.Coduob=codigo ";
			$query = $query." and proceso_000009.Coduob not in (";
			$query = $query."SELECT proceso_000007.Coduob from proceso_000007 ";
			$query = $query."  where proceso_000007.Codufu = '".substr($wusuf,0,strpos($wusuf,"-"))."'";
			$query = $query."      and Codenc = '".substr($wenc,0,strpos($wenc,"-"))."'";
			$query = $query."      and Ano = ".$wano;
			$query = $query."      and Mes = ".$wmes.")";
			$query = $query." order by proceso_000009.Coduob";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wusuo'>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
				echo "</td></tr>";
			}
			else
				echo "TODOS HAN SIDO ENCUESTADOS</td></tr>";
		}
		else
		{
			if(isset($wusuo))
			{
				echo "<input type='HIDDEN' name= 'wusuo' value='".$wusuo."'>";
				echo "<td bgcolor=#cccccc align=center>".$wusuo."</td></tr>";
			}
		}
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		echo "<table border=0 align=center>";
		echo "<tr><td bgcolor=#999999 align=center><IMG SRC='/MATRIX/images/medical/root/clinica.png'></td><td  align=center bgcolor=#cccccc colspan=9><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=10 align=center><b>GRABACION DE ENCUESTAS (DESARROLLO ORGANIZACIONAL)</b></td></tr>";
		echo "<tr><td bgcolor=#dddddd colspan=10 align=center><b>ENCUESTA : ".$wenc."</b></td></tr>";
		echo "<tr><td bgcolor=#dddddd colspan=10 align=center><b>CALIFICADOR : ".$wusuf."</b></td></tr>";
		echo "<tr><td bgcolor=#dddddd colspan=10 align=center><b>CALIFICADO : ".$wusuo."</b></td></tr>";
		echo "<tr><td bgcolor=#dddddd colspan=10 align=center><b>Por favor califique la encuesta con su grupo de trabajo. Al responder la encuesta procure que la evaluación corresponda a un juicio generalizado e imparcial del proceso y NO a juicios puntuales o parcializados de las personas.</b></td></tr>";
		echo "<tr><td bgcolor=#CCCCCC align=center colspan=3><b>PREGUNTAS</b></td><td bgcolor=#CCCCCC align=center colspan=6><b>GRADO</b></td><td bgcolor=#CCCCCC align=center><b>JUSTIFICACION</b></td></tr>";
		echo "<tr><td bgcolor=#999999 align=center><b>NUMERO</b></td><td bgcolor=#999999 align=center><b>ATRIBUTO </b></td><td bgcolor=#999999 align=center><b>TEXTO </b></td><td bgcolor=#999999 align=center><b>BAJO </b></td><td bgcolor=#999999 align=center><b>MEDIO BAJO</b></td><td bgcolor=#999999 align=center><b>MEDIO</b></td><td bgcolor=#999999 align=center><b>MEDIO ALTO</b></td><td bgcolor=#999999 align=center><b>ALTO</b></td><td bgcolor=#999999 align=center><b>SIN INFORMACION</b></td><td bgcolor=#999999 align=center><b>MINIMO 10 CARACTERES</b></td></tr>";
		$query = "SELECT Ano, Mes  from proceso_000006 where Codenc = '".substr($wenc,0,strpos($wenc,"-"))."' and prioridad=1 ";
		$err = mysql_query($query,$conex);
		$row = mysql_fetch_array($err);
		$wano=$row[0];
		$wmes=$row[1];
		$query = "SELECT count(*) from proceso_000007 ";
		$query = $query."  where Codufu = '".substr($wusuf,0,strpos($wusuf,"-"))."'";
		$query = $query."      and Coduob = '".substr($wusuo,0,strpos($wusuo,"-"))."'";
		$query = $query."      and Codenc = '".substr($wenc,0,strpos($wenc,"-"))."'";
		$query = $query."      and Ano = ".$wano;
		$query = $query."      and Mes = ".$wmes;
		$err = mysql_query($query,$conex);
		$row = mysql_fetch_array($err);
		if ($row[0] == 0)
		{
			$query = "SELECT Codpre, Atributo, Texto from proceso_000005 ";
			$query = $query."  where Codenc ='".substr($wenc,0,strpos($wenc,"-"))."'";
			$query = $query."      and Activo = 'on' ";
			$query = $query." order by Codpre";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				if(!isset($data[0][0]))
					$data=array();
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if(!isset($data[$i][0]))
					{
						$data[$i][0]=$row[0];
						$data[$i][2]="";
					}
					if($i % 2 == 0)
						$color="#dddddd";
					else
						$color="#cccccc";
					$k=$i + 1;
					if(!isset($bad[$i]) or (isset($bad[$i]) and $bad[$i] == 0))
						echo "<tr><td bgcolor=".$color." align=center>".$k."</td><td bgcolor=".$color.">".$row[1]."</td><td bgcolor=".$color.">".$row[2]."</td>";
					else
						echo "<tr><td bgcolor=".$color." align=center>".$k."</td><td bgcolor=#FF0000>".$row[1]."</td><td bgcolor=".$color.">".$row[2]."</td>";
					if(!isset($data[$i][1]))
						echo "<td bgcolor=".$color." align=center>1 <input type='RADIO' name='data[".$i."][1]' value=1></td><td bgcolor=".$color." align=center>2 <input type='RADIO' name='data[".$i."][1]' value=2></td><td  bgcolor=".$color." align=center>3 <input type='RADIO' name='data[".$i."][1]' value=3></td><td  bgcolor=".$color." align=center>4 <input type='RADIO' name='data[".$i."][1]' value=4></td><td  bgcolor=".$color." align=center>5 <input type='RADIO' name='data[".$i."][1]' value=5></td><td  bgcolor=".$color." align=center>6 <input type='RADIO' name='data[".$i."][1]' value=6></td><td  bgcolor=".$color." align=center><textarea name='data[".$i."][2]' cols=40 rows=4>".$data[$i][2]."</textarea></td></tr>";
					else
					{
						if($data[$i][1] == 1)
							echo "<td bgcolor=".$color." align=center>1 <input type='RADIO' name='data[".$i."][1]' checked value=1></td>";
						else
							echo "<td bgcolor=".$color." align=center>1 <input type='RADIO' name='data[".$i."][1]' value=1></td>";
						if($data[$i][1] == 2)
							echo "<td bgcolor=".$color." align=center>2 <input type='RADIO' name='data[".$i."][1]' checked value=2></td>";
						else	
							echo "<td bgcolor=".$color." align=center>2 <input type='RADIO' name='data[".$i."][1]' value=2></td>";
						if($data[$i][1] == 3)
							echo "<td bgcolor=".$color." align=center>3 <input type='RADIO' name='data[".$i."][1]' checked value=3></td>";
						else
							echo "<td bgcolor=".$color." align=center>3 <input type='RADIO' name='data[".$i."][1]' value=3></td>";
						if($data[$i][1] == 4)
							echo "<td bgcolor=".$color." align=center>4 <input type='RADIO' name='data[".$i."][1]' checked value=4></td>";
						else
							echo "<td bgcolor=".$color." align=center>4 <input type='RADIO' name='data[".$i."][1]' value=4></td>";
						if($data[$i][1] == 5)
							echo "<td bgcolor=".$color." align=center>5 <input type='RADIO' name='data[".$i."][1]' checked value=5></td>";
						else
							echo "<td bgcolor=".$color." align=center>5 <input type='RADIO' name='data[".$i."][1]' value=5></td>";
						if($data[$i][1] == 6)
							echo "<td bgcolor=".$color." align=center>6 <input type='RADIO' name='data[".$i."][1]' checked value=6></td>";
						else
							echo "<td bgcolor=".$color." align=center>6 <input type='RADIO' name='data[".$i."][1]' value=6></td>";
						echo "<td  bgcolor=".$color." align=center><textarea name='data[".$i."][2]' cols=40 rows=4>".$data[$i][2]."</textarea></td></tr>";
					}
				}
				echo "<td bgcolor=#cccccc colspan=10 align=center>DATOS OK!! <input type='checkbox' name='ok'></td><tr>";
				echo "<input type='HIDDEN' name= 'wenc' value='".$wenc."'>";
				echo "<input type='HIDDEN' name= 'wusuf' value='".$wusuf."'>";
				echo "<input type='HIDDEN' name= 'wusuo' value='".$wusuo."'>";
				echo "<input type='HIDDEN' name= 'wano' value='".$wano."'>";
				echo "<input type='HIDDEN' name= 'wmes' value='".$wmes."'>";
				echo "<input type='HIDDEN' name= 'num' value='".$num."'>";
				for ($i=0;$i<$num;$i++)
					echo "<input type='HIDDEN' name= 'data[".$i."][0]' value='".$data[$i][0]."'>";
			}
			echo "<td bgcolor=#999999 colspan=10 align=center><input type='submit' value='ACTUALIZAR'></td><tr>";
			echo"</table>";
		}
		else
		{
			echo "<td bgcolor=#999999 colspan=10 align=center><input type='submit' value='ACTUALIZAR'></td><tr>";
			echo"</table>";
			echo "<br><br><font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>ESTA UNIDAD YA FUE ENCUESTADA POR ESTE USUARIO</MARQUEE></FONT>";
			echo "<input type='HIDDEN' name= 'wenc' value='".$wenc."'>";
			echo "<input type='HIDDEN' name= 'wusuf' value='".$wusuf."'>";
			unset($wusuo);
		}
	}
}
?>
</body>
</html>