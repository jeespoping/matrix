<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Borrado de Informacion</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> delete.php Ver. 2.00</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
/**
 * Se incluyen los scripts de encripcion y desencripcion
 * @date: 2021/09/15
 * @by: sebastian.nevado
 * 		marlon.osorio
 * 		daniel.corredor
 */
include_once("root/cifrado/cifrado.php");
include_once("root/cifrado/cifradoJS.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='delete.php' method=post>";

	if(!isset($wpassdel) or (!isset($ok) and isset($borrar) and $borrar != "N"))
	{
		if(!isset($wpassdel))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>BORRADO DE REGISTROS X TABLA</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>Digite su Password de Borrado</td>";
			echo "<td bgcolor=#cccccc align=center><input type='password' name='wpassdel' size=8 maxlength=8></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
			echo "<input type='HIDDEN' name= 'tabla' value='".$tabla."'>";
			echo "<input type='HIDDEN' name= 'consulta' value='".$consulta."'>";
			echo "<input type='HIDDEN' name= 'kc' value=".$kc.">";
			for ($w=0;$w<$kc;$w++)
			{
				echo "<input type='HIDDEN' name= 'cons[".$w."]' value='".$cons[$w]."'>";
			}
		}
		else
		{
			if ($borrar == "T")
				$contador=$Totales;
			else
			{
				$contador=0;
				for ($i=0;$i<$Totales;$i++)
					if($R[$i][1] == 1)
						$contador++;
				for ($i=0;$i<$numero;$i++)
					if(isset($del[$i]))
						$contador++;
			}
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>CONFIRMACION DE BORRADO</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center colspan=2><h1>Sen van a BORRAR : ".$contador." De : ".$Totales."</h1></td>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center><h1>Confirme Si Desea Borrar La Informacion Selecionada ? (S/N)</h1></td>";
			echo "<td bgcolor=#cccccc align=center><input type='text' name='ok' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
			echo "<input type='HIDDEN' name= 'borrar' value='".$borrar."'>";
			echo "<input type='HIDDEN' name= 'wpassdel' value=".$wpassdel.">";
			echo "<input type='HIDDEN' name= 'Totales' value='".$Totales."'>";
			echo "<input type='HIDDEN' name= 'numero' value='".$numero."'>";
			echo "<input type='HIDDEN' name= 'Inicio' value='".$Inicial."'>";
			for ($w=0;$w<$Totales;$w++)
			{
				echo "<input type='HIDDEN' name= 'R[".$w."][0]' value='".$R[$w][0]."'>";
				echo "<input type='HIDDEN' name= 'R[".$w."][1]' value='".$R[$w][1]."'>";
			}
			for ($w=0;$w<$numero;$w++)
			{
				if(isset($del[$w]))
					echo "<input type='HIDDEN' name= 'del[".$w."]' value='".$del[$w]."'>";
			}
		}
		echo "<input type='HIDDEN' name= 'tabla' value='".$tabla."'>";
        /**
         * Se agrega funcion MyDecrypt para desencriptar la consulta y poderla ejecutar
         * @date: 22/10/2021
         * @by: Jesus.Lopez
         *  */
        $consulta = Cifrado::myDecrypt($consulta );
        $consulta=stripslashes($consulta);
        $consulta = Cifrado::myCrypt($consulta);
	 	echo "<input type='HIDDEN' name= 'consulta' value='".$consulta."'>";
		echo "<input type='HIDDEN' name= 'kc' value=".$kc.">";
		for ($w=0;$w<$kc;$w++)
		{
			echo "<input type='HIDDEN' name= 'cons[".$w."]' value='".$cons[$w]."'>";
		}
	}
	else
	{
		if($key != $usera)
			$query = "select count(*)  from usuarios where codigo='".$usera."' and passdel ='".$wpassdel."' and tablas like '%".$tabla."%'";
		else
			$query = "select count(*)  from usuarios where codigo='".$key."' and passdel ='".$wpassdel."' and tablas like '%".$tabla."%'";
		$err = mysql_query($query,$conex);
		$row = mysql_fetch_array($err);
		if ($row[0] > 0)
		{
            /**
             * Se agrega funcion MyDecrypt para desencriptar la consulta y poderla ejecutar
             * @date: 22/10/2021
             * @by: Jesus.Lopez
             *  */
            $consulta = Cifrado::myDecrypt($consulta );
			$consulta=stripslashes($consulta);
			$consulta=str_replace("|","%",$consulta);
			$query =strtolower($consulta);
			$query=stripslashes($query);
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err) or die("Sin Registros");
			if ($num > 0)
				$Totales=$num;
			else
				$Totales=0;
			if(isset($Inicio) and isset($numero))
			{
				for ($i=0;$i<$numero;$i++)
					if(isset($del[$i]))
						$R[$Inicio+$i][1]=1;
					else
						$R[$Inicio+$i][1]=0;
			}
			else
			{
				$R=array();
				for ($i=0;$i<$Totales;$i++)
				{
					$R[$i][0]=0;
					$R[$i][1]=0;
				}
			}
			if(isset($borrar) and $borrar == "T" and $ok=="S")
			{
				$query =strtolower($consulta);
				$query=stripslashes($query);
				$ini=strpos($query,"*");
				$query1="delete ".substr($query,$ini+1);
				$res = mysql_query($query1,$conex) or die(mysql_errno().":".mysql_error());
			}
			elseif(isset($borrar) and $borrar == "P" and $ok=="S")
					{
						
						$query =strtolower($consulta);
						$query=stripslashes($query);
						$ini=strpos($query,"*");
						$Totales1=$Totales;
						for ($i=0;$i<$Totales1;$i++)
						{
							if($R[$i][1] == 1)
							{
								$query1="delete from ".$tabla." where Id=".$R[$i][0];
								$res = mysql_query($query1,$conex) or die(mysql_errno().":".mysql_error());
								$Totales--;
							}
						}
						for ($i=0;$i<$Totales;$i++)
						{
							if($R[$i][1] == 1)
							{
								$R[$i][1] = 0;
							}
						}
					}
			if(isset($Pagina) and $Pagina > 0 and isset($Totales))
			{
				$Paginas=(integer)($Totales / 30);
				if($Paginas * 30 < $Totales)
					$Paginas++;
				if($Pagina > $Paginas)
					$Pagina=$Paginas;
				$Inicial=($Pagina - 1 ) * 30;
				$Final= $Inicial + 30;
			}
			else
			{
				if (!isset($Inicial))
				{
					$Inicial=0;
					$Final=30;
				}
				else
					if(!isset($back) and isset($Totales))
					{
						if($Final < $Totales)
						{
							$Inicial = $Final;
							$Final=$Final+30;
						}
					}
					else
					{
						if($Inicial >= 30)
						{
							$Final = $Inicial;
							$Inicial=$Inicial-30;
						}
					}
				}
			echo "<input type='HIDDEN' name= 'Inicial' value='".$Inicial."'>";
			echo "<input type='HIDDEN' name= 'tabla' value='".$tabla."'>";
			echo "<input type='HIDDEN' name= 'Final' value='".$Final."'>";	
			echo "<table border=0 align=center cellpadding=3>";
			echo "<tr><td bgcolor=#cccccc align=center>CRITERIO DE BORRADO</td></tr>";
			echo "<tr><td bgcolor=#cccccc> <input type='radio' name= 'borrar' value='T'>Borra Todos&nbsp<input type='radio' name= 'borrar' value='P'>Borra Seleccionados&nbsp<input type='radio' name= 'borrar' value='N' checked>NO Borrar</td></tr>";
			echo "<tr><td bgcolor='#cccccc' align=center><input type='submit' value='IR'>";
			if(isset($back))
				echo "<input type='checkbox' name=back checked>Back</td></tr>";
			else
				echo "<input type='checkbox' name=back>Back</td></tr>";
			echo "<br><br></table>";
			echo "<br><br>";
			echo "<input type='HIDDEN' name= 'Totales' value='".$Totales."'>";
			$consulta=stripslashes($consulta);
            /**
             * Se agrega funcion MyDecrypt para desencriptar la consulta y poderla ejecutar
             * @date: 22/10/2021
             * @by: Jesus.Lopez
             *  */
            $consulta = Cifrado::myCrypt($consulta );
			echo "<input type='HIDDEN' name= 'consulta' value='".$consulta."'>";
			$query = $query."  limit ".$Inicial.",30";
			#$query = $query." order by id limit ".$Inicial.",30";
			#echo $query."<br>";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err) or die("Sin Registros");
			if ($num> 0)
			{
				$color="#999999";
				echo "<table border=0 align=center>";
				echo "<tr>";
				$row = mysql_fetch_array($err);
				$k=0;
				for ($i=0;$i<sizeof($row);$i++)
				{
  					if (isset($row[$i]))
  						$k++;
  				}
				for ($i=0;$i<$kc;$i++)
				{
  					echo "<td bgcolor=".$color."><font size=2><b>".$cons[$i]."</b></font></td>";
  				}
  				echo "<td bgcolor=".$color."><font size=2><b>Registro</b></font></td>";
  				echo "<td bgcolor=".$color."><font size=2><b>Opcion</b></font></td>";
				echo "</tr>";
				$r=0;
				if($num>0)
				{
					for ($i=0;$i<$num;$i++)
					{
						if ($r == 0)
						{
							$color="#CCCCCC";
							$r = 1;
						}
						else
						{
							$color="#999999";
							$r = 0;
						}
						if ($i > 0)
							$row = mysql_fetch_array($err);
						for ($j=0;$j<$k;$j++)
						{
							echo "<td bgcolor=".$color."><font size=2>".$row[$j]."</font></td>";	
						}
						$R[$Inicial+$i][0]=$row[$k-1];
						if($R[$Inicial+$i][1] == 1)
							echo "<td bgcolor=".$color."><input type='checkbox' name=del[".$i."] checked>Borrar</td>";
						else
							echo "<td bgcolor=".$color."><input type='checkbox' name=del[".$i."] >Borrar</td>";
						echo "</tr>";
					}
					echo "</tabla>";
					for ($w=0;$w<$Totales;$w++)
					{
						echo "<input type='HIDDEN' name= 'R[".$w."][0]' value='".$R[$w][0]."'>";
						echo "<input type='HIDDEN' name= 'R[".$w."][1]' value='".$R[$w][1]."'>";
					}
					for ($w=0;$w<$kc;$w++)
					{
						echo "<input type='HIDDEN' name= 'cons[".$w."]' value='".$cons[$w]."'>";
					}
					echo "<input type='HIDDEN' name= 'kc' value=".$kc.">";
					echo "<input type='HIDDEN' name= 'numero' value='".$num."'>";
					echo "<input type='HIDDEN' name= 'Inicio' value='".$Inicial."'>";
					echo "<input type='HIDDEN' name= 'wpassdel' value=".$wpassdel.">";
					echo "<br><br>";
					echo "Registros :<b>".$Inicial."</b> a <b>".$Final."</b>&nbsp &nbsp";
					echo "De :<b>".$Totales."</b>&nbsp &nbsp";
					$Paginas=(integer)($Totales / 30);
					if($Paginas * 30 < $Totales)
						$Paginas++;
					echo "Paginas :<b>".$Paginas."</b>&nbsp &nbsp  <b>Vaya a la Pagina NRo :</b> <input type='TEXT' name='Pagina' size=10 maxlength=10 value=0>&nbsp &nbsp<input type='submit' value='IR'><br>";
					echo "<table border=0 align=center>";
					echo "<tr><td><A HREF='#Arriba'><B>Arriba</B></A></td></tr></table>";	
	 	 		}
	 	 		else
	 	 		{
	 	 			echo "</tabla>";
	  				echo "Sin Registros"."<br>";
  				}
  		}
		else
		{
			echo " CONSULTA SIN REGISTROS ASOCIADOS";
		}
		include_once("free.php");
	 }
	 else
	 {
		 echo "<center><table border=0 aling=center>";
		 echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
		 echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>PASSWORD DE BORRADO INCORRECTO O NO TIENE PRIORIDAD DE BORRADO SOBRE ESTA TABLA !!!</MARQUEE></FONT>";
		 echo "<br><br>";			
	 }		
	}
}
?>
<!--
	  Se incluye script para encriptar en JS
		@date: 2021/10/22
		@by:	Jesus.Lopez
  -->
<script type="text/javascript" src="../../../include/root/cifrado/crypto-js.min.js"></script>
</body>
</html>