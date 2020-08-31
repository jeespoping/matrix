<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion Automatica de Reportes</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> genrep.php Ver. 3.00</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
function constante($cadena)
{
	$posa=strpos($cadena,"&");
	$pos1=strpos($cadena,"and");
	if($pos1 !== false)
		$posa=$pos1;
	$pos1=strpos($cadena,"or");
	if ($pos1 !== false and substr($cadena,$pos1,5) == "order")
		$pos1=false;
	if($pos1 !== false and $posa === false)
		$posa=$pos1;
	$pos1=strpos($cadena,"group");
	if($pos1 !== false and $posa === false)
		$posa=$pos1;
	$pos1=strpos($cadena,"order");
	if($pos1 !== false and $posa === false)
		$posa=$pos1;
	if($posa == -1)
		$posa=strpos($cadena,"&");
	$posb=strpos($cadena,".");
	if($posa !== false and $posb !== false and $posb > $posa)
		return 1;
	if($posa !== false and $posb !== false and $posb < $posa)
		return 0;
	if($posa === false and $posb !== false)
		return 0;
	if($posa !== false and $posb !== false and $posb > $posa)
		return 1;
	if($posb === false)
		return 1;
}
function buscar($query, &$x)
{
	$k=-1;
	$posa=strpos($query,"where");
	$posb=strpos($query,"and");
	$posc=strpos($query,"or");
	if ($posc !== false and substr($query,$posc,5) == "order")
			$posc=false;
	if(($posc !== false and $posb !== false and $posc < $posb) or ($posb === false and $posc !== false))
		$posb=$posc;
	while($posa !== false or $posb !== false)
	{
		if($posa !== false)
		{
			$pos=$posa;
			$inc=5;
		}
		else
		{
			$pos=$posb;
			$inc=3;
		}
		$posj=-1;
		$posf=-1;
		$pos1=strpos($query,"<=");
		if($pos1 !== false)
			$posj =$pos1 + 1;
		$pos1=strpos($query,">=");
		if(($pos1 !== false and $pos1 < $posj and $posj > 0) or ($pos1 !== false and $posj == -1))
			$posj =$pos1 + 1;
		$pos1=strpos($query,"!=");
		if(($pos1 !== false and $pos1 < $posj and $posj > 0) or ($pos1 !== false and $posj == -1))
			$posj =$pos1 + 1;
		$pos1=strpos($query,">");
		if((($pos1 !== false and $pos1 < $posj and $posj > 0) or ($pos1 !== false and $posj == -1)) and substr($query,$pos1+1,1) != "=")
			$posj =$pos1;
		$pos1=strpos($query,"<");
		if((($pos1 !== false and $pos1 < $posj and $posj > 0) or ($pos1 !== false and $posj == -1)) and substr($query,$pos1+1,1) != "=")
			$posj =$pos1;
		$pos1=strpos($query,"=");
		if(($pos1 !== false and $pos1 < $posj and $posj > 0) or ($pos1 !== false and $posj == -1))
			$posj =$pos1;
		$pos1=strpos($query,"between");
		if(($pos1 !== false and $pos1 < $posj and $posj > 0) or ($pos1 !== false and $posj == -1))
		{
			$posf=strpos(substr($query,$pos1),"and");
			$posj =$pos1;
		}
		if($posj != -1)
		{
			if($posf == -1)
				$r=constante(substr($query,$posj));
			if($posf != -1 or $r == 1)
			{
				$k++;
				$x[$k][0]=trim(substr($query,$pos+$inc,$posj-$pos-$inc));
				if($posf != -1)
					$x[$k][1]=1;
				else
					$x[$k][1]=0;
			}
			if($posf != -1)
				$posj = $posf + $pos1;
			$query=substr($query,$posj+1);
		}
		else
			$query="";
		$posa=strpos($query,"where");
		$posb=strpos($query,"and");
		$posc=strpos($query,"or");
		if ($posc !== false and substr($query,$posc,5) == "order")
			$posc=false;
		
		if(($posc !== false and $posb !== false and $posc < $posb) or ($posb === false and $posc !== false))
			$posb=$posc;
	}
	return $k;
}
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$superglobals = array($_SESSION,$_REQUEST);
	foreach ($superglobals as $keySuperglobals => $valueSuperglobals)
	{
		foreach ($valueSuperglobals as $variable => $dato)
		{
			$$variable = $dato; 
		}
	}
	if(!isset($key))
		$key = substr($user,2,strlen($user));
	echo "<form action='genrep.php' method=post>";
	echo "<input type='HIDDEN' name= 'key' value='".$key."'>";
	

	

	if(isset($regfile))
		if($regfile == 1)
		{
			$ini=strpos($formulario,"-");
			$query = "insert reportes (medico,formulario,codigo,descripcion,nombre,nivel) values ('".$key."','".substr($formulario,0,$ini)."','".$num."','".$Titulo."','".$Codigo."',1)";
			$err1 = mysql_query($query,$conex) or die("Error en la Insercion del Reporte");
			 echo "<table border=0 align=center>";
			 echo "<tr><td bgcolor=#dddddd><IMG SRC='/MATRIX/images/medical/root/clinica.png' ></td><td  align=center bgcolor=#dddddd ><font size=7><b>GENERADOR DE REPORTES</b></font></td></tr>";
			 echo "<tr><td  align=center bgcolor=#dddddd colspan=2><font size=2><b>REPORTE REGISTRADO</b></font></td></tr></table>";
		}
		else
		{
			unlink($datafile);
		   	echo "<table border=0 align=center>";
		  	 echo "<tr><td bgcolor=#dddddd><IMG SRC='/MATRIX/images/medical/root/clinica.png' ></td><td  align=center bgcolor=#dddddd ><font size=7><b>GENERADOR DE REPORTES</b></font></td></tr>";
			 echo "<tr><td  align=center bgcolor=#dddddd colspan=2><font size=2><b>REPORTE BORRADO</b></font></td></tr></table>";
		 }
	if(!isset($regfile))
	{
		if (isset($delfile))
		{
			$query = "select codigo,nivel from reportes where nombre = '".$Codigo."' and medico = '".$key."'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num > 0 and $row[1] == 1)
			{
				$query = "delete from reportes where nombre = '".$Codigo."' and medico = '".$key."'";
				$err = mysql_query($query,$conex) or die("Error en el borrado del Reporte");;
				unlink($datafile);
			}
			else
			{
				echo "<center><IMG SRC='/matrix/images/medical/root/cabeza.gif' ><br><br>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>ESTE REPORTE NO FUE ESCRITO X EL GENERADOR.  NO!! SE PUEDE BORRAR</MARQUEE></FONT><br><br>";
			}
		}
		if(isset($consulta))
			$consulta=stripslashes($consulta);
		$wsw=0;
		if(!isset($ok))
		{
			$pos1=strpos($consulta,"select");
			if($pos1 === false)
				$wsw=1;
			$pos2=strpos($consulta,"from");
			if($pos2 === false)
				$wsw=1;
			if($wsw == 0)
			{
				$var=substr($consulta,$pos1+7,($pos2 - 1) - ($pos1+7));
				if(strlen($var) > 3)
				{
					$nvar=array();
					$k=-1;
					while(strlen($var) > 0)
					{
						$ini=strpos($var,",");
						$k++;
						if($ini === false)
						{
							$nvar[$k]=$var;
							$var="";
						}
						else
						{
							$nvar[$k]=substr($var,0,$ini);
							$var=substr($var,$ini+1);
						}
					}
					echo "<table border=0 align=center>";
					 echo "<tr><td bgcolor=#cccccc align=center><IMG SRC='/MATRIX/images/medical/root/clinica.png' ></td><td  align=center bgcolor=#cccccc colspan=3><font size=7><b>GENERADOR DE REPORTES</b></font></td></tr>";
					echo "<tr><td bgcolor=#999999 colspan=4 align=center><b>NOMBRE Y TITULOS</b></td></tr>";
					echo "<tr><td bgcolor=#dddddd><b>CODIGO REPORTE</b></td><td bgcolor=#dddddd colspan=3><input type='TEXT' name='Codigo' size=16 maxlength=16 value='NO ESPECIFICADO'></td></tr>";
					echo "<tr><td bgcolor=#dddddd><b>TITULO REPORTE</b></td><td bgcolor=#dddddd colspan=3><input type='TEXT' name='Titulo' size=60 maxlength=60 value='NO ESPECIFICADO'></td></tr>";
					echo "<tr><td bgcolor=#dddddd><b>SUBTITULO REPORTE</b></td><td bgcolor=#dddddd colspan=3><input type='TEXT' name='Subtit' size=60 maxlength=60 value='NO ESPECIFICADO'></td></tr>";
					echo "<tr><td bgcolor=#999999 colspan=4 align=center><b>VARIABLES A IMPRIMIR EN EL REPORTE</b></td></tr>";
					echo "<tr><td bgcolor=#cccccc><b> Nombre del Campo</b></td><td bgcolor=#cccccc><b>Nombre en el Reporte</b></td><td bgcolor=#cccccc align=center><b>Numerico</b></td><td bgcolor=#cccccc align=center><b>Totalizar</b></td></tr>";
					for($i=0;$i<=$k;$i++)
					{
						if(substr($nvar[$i],0,4) != "sum(" and substr($nvar[$i],0,6) != "count(" )
						{
							$ini1=strpos($nvar[$i],"_");
							$ini2=strpos($nvar[$i],".");
							$us=substr($nvar[$i],0,$ini1);
							$tb=substr($nvar[$i],$ini1+1,6);
							$ds=substr($nvar[$i],$ini2+1);
							$query="SELECT tipo FROM  det_formulario WHERE medico = '".$us."' AND codigo = '".$tb."' AND descripcion = '".$ds."'";
							$err = mysql_query($query,$conex);
							$row = mysql_fetch_array($err);
							if($row[0] == 1 or $row[0] == 2 or $row[0] == 6)
								echo "<tr><td bgcolor=#dddddd>".$ds."</td><td bgcolor=#dddddd><input type='TEXT' name='name[".$i."]' size=20 maxlength=20 value='NO ESPECIFICADO'></td><td bgcolor=#dddddd align=center><input type='checkbox' name='num[".$i."]'></td><td bgcolor=#dddddd align=center><input type='checkbox' name='sum[".$i."]'></td></tr>";
							else
								echo "<tr><td bgcolor=#dddddd>".$ds."</td><td bgcolor=#dddddd><input type='TEXT' name='name[".$i."]' size=20 maxlength=20 value='NO ESPECIFICADO'></td><td bgcolor=#dddddd></td><td bgcolor=#dddddd></td></tr>";
						}
						else
							echo "<tr><td bgcolor=#dddddd>".$nvar[$i]."</td><td bgcolor=#dddddd><input type='TEXT' name='name[".$i."]' size=20 maxlength=20 value='NO ESPECIFICADO'></td><td bgcolor=#dddddd align=center><input type='checkbox' name='num[".$i."]'></td><td bgcolor=#dddddd align=center><input type='checkbox' name='sum[".$i."]'></td></tr>";
					}
					echo "</table>";
					$w=buscar($consulta, $x);
					if($w != -1)
					{
						echo "<table border=0 align=center>";
						echo "<tr><td bgcolor=#999999 colspan=5 align=center><b>VARIABLES A PEDIR X PANTALLA</b></td></tr>";
						echo "<tr><td bgcolor=#cccccc><b> Nombre del Campo</b></td><td bgcolor=#cccccc><b>Nombre en Pantalla</b></td><td bgcolor=#cccccc align=center><b>Activo</b></td><td bgcolor=#cccccc align=center><b>Alfanumerico</b></td><td bgcolor=#cccccc align=center><b>Longitud</b></td></tr>";
						for($i=0;$i<=$w;$i++)
						{
							$x[$i][0]=str_replace("<","",$x[$i][0]);
							$x[$i][0]=str_replace(">", "",$x[$i][0]);
							$x[$i][0]=str_replace("!","",$x[$i][0]);
							echo "<input type='HIDDEN' name= 'x[".$i."][0]' value='".$x[$i][0]."'>";
							echo "<input type='HIDDEN' name= 'x[".$i."][1]' value='".$x[$i][1]."'>";
							echo "<tr><td bgcolor=#dddddd>".$x[$i][0]."</td><td bgcolor=#dddddd><input type='TEXT' name='namev[".$i."]' size=20 maxlength=20 value='NO ESPECIFICADO'></td><td bgcolor=#dddddd align=center><input type='checkbox' name='act[".$i."]'></td><td bgcolor=#dddddd align=center><input type='checkbox' name='alf[".$i."]'></td><td bgcolor=#dddddd align=center><input type='TEXT' name='lon[".$i."]' size=3 maxlength=3 value=1></td></tr>";
						}
					}
					$ok="on";
					echo "<input type='HIDDEN' name= 'w' value='".$w."'>";
					echo "<input type='HIDDEN' name= 'k' value='".$k."'>";
					echo "<input type='HIDDEN' name= 'ok' value='".$ok."'>";
					echo "<input type='HIDDEN' name= 'consulta' value='".$consulta."'>";
					echo "<td bgcolor=#cccccc colspan=5 align=center><input type='submit' value='GENERAR'></td><tr>";
					echo"</table>";
				}
				else
				{
					echo "<center><table border=0 aling=center>";
					echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
					echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>CONSULTA SIN CAMPOS ESPECIFICADOS PARA GENERACION DE REPORTE -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
					echo "<br><br>";
				}
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>CONSULTA NO ADECUADA PARA GENERACION DE REPORTE -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
				echo "<br><br>";
			}
		}
		else
		{
			$Q=$consulta;
			$pregunta="";
			$consulta1=$consulta;
			$z=-1;
			$j=0;
			for($i=0;$i<=$w;$i++)
			{
				if(isset($act[$i]) and  $act[$i] == "on")
				{
					#echo "ACTIVO ".$act[$i]."<br>";
					$bet=0;
					$ini=strpos($consulta,$x[$i][0]);
					if($j >= $ini)
					{
						$j=strpos(substr($consulta,$j+1),$x[$i][0]) + 1 + $j;
						$ini=$j;
					}
					else
						$j=$ini;
					$posa=strpos(substr($consulta,$ini),"&");
					$pos1=strpos(substr($consulta,$ini),"and");
					if($pos1 !== false)
						$posa=$pos1;
					$pos1=strpos(substr($consulta,$ini),"group");
					if($pos1 !== false and $posa === false)
						$posa=$pos1;
					$pos1=strpos(substr($consulta,$ini),"order");
					if($pos1 !== false and $posa === false)
						$posa=$pos1;
					$posb=strpos(substr($consulta,$ini),"&");
					$pos1=strpos(substr($consulta,$ini),"<=");
					if($pos1 !== false )
						$posb=$pos1+2;
					$pos1=strpos(substr($consulta,$ini),">=");
					if(($pos1 !== false and $posb === false) or ($pos1 !== false and $posb !== false and $pos1 < $posb))
						$posb=$pos1+2;
					$pos1=strpos(substr($consulta,$ini),"!=");
					if(($pos1 !== false and $posb === false) or ($pos1 !== false and $posb !== false and $pos1 < $posb))
						$posb=$pos1+2;
					$pos1=strpos(substr($consulta,$ini),"=");
					if((($pos1 !== false and $posb === false) or ($pos1 !== false and $posb !== false and $pos1 < $posb)) and substr(substr($consulta,$ini),$pos1 - 1,1) != ">" and substr(substr($consulta,$ini),$pos1 - 1,1) != "<" and substr(substr($consulta,$ini),$pos1 - 1,1) != "!")
						$posb=$pos1+1;
					$pos1=strpos(substr($consulta,$ini),">");
					if((($pos1 !== false and $posb === false) or ($pos1 !== false and $posb !== false and $pos1 < $posb)) and substr(substr($consulta,$ini),$pos1+1,1) != "=")
						$posb=$pos1+1;
					$pos1=strpos(substr($consulta,$ini),"<");
					if((($pos1 !== false and $posb === false) or ($pos1 !== false and $posb !== false and $pos1 < $posb)) and substr(substr($consulta,$ini),$pos1+1,1) != "=")
						$posb=$pos1+1;
					$pos1=strpos(substr($consulta,$ini),"between");
					if(($pos1 !== false and $posb === false) or ($pos1 !== false and $posb !== false and $pos1 < $posb))
					{
						$bet=1;
						$posb=$pos1+8;
					}
					$posb=$ini + $posb;
					if($posa === false)
						$posa=strlen($consulta);
					else
						$posa=$ini + $posa;
					#echo $x[$i]." INI : ".$ini."  POS B : ".$posb."  ".substr($consulta,$posb,3)." POS A : ".$posa."  ".substr($consulta,$posa,3)."<br>";
					if($bet == 0)
					{
						$z++;
						if(strlen($pregunta) == 0)
							$pregunta.="!isset(".chr(36)."v".$z.")";
						else
							$pregunta.=" or !isset(".chr(36)."v".$z.")";
						if(isset($alf[$i]))
							$consulta1=substr($consulta1,0,$posb)." '".chr(34).".".chr(36)."v".$z.".".chr(34)."' ".substr($consulta1,$posa);
						else
							$consulta1=substr($consulta1,0,$posb)." ".chr(34).".".chr(36)."v".$z.".".chr(34)." ".substr($consulta1,$posa);
						$consulta=$consulta1;
					}
					else
					{
						$posw=strpos(substr($consulta,$posa+3),"&");
						$pos1=strpos(substr($consulta,$posa+3),"and");
						if($pos1 !== false)
							$posw=$pos1;
						$pos1=strpos(substr($consulta,$posa+3),"group");
						if($pos1 !== false and $posw === false)
							$posw=$pos1;
						$pos1=strpos(substr($consulta,$posa+3),"order");
						if($pos1 !== false and $posw === false)
							$posw=$pos1;
						if($posw === false)
							$posa=strlen($consulta);
						else
							$posa=$posw + $posa+3;
						#echo $x[$i]."2  INI : ".$ini."  POS B : ".$posb."  ".substr($consulta,$posb,3)." POS A : ".$posa."  ".substr($consulta,$posa,3)."<br>";
						$z++;
						if(strlen($pregunta) == 0)
							$pregunta.="!isset(".chr(36)."v".$z.")";
						else
							$pregunta.=" or !isset(".chr(36)."v".$z.")";
						if(isset($alf[$i]))
							$consultaw=substr($consulta1,0,$posb)." '".chr(34).".".chr(36)."v".$z.".".chr(34)."' and  '";
						else
							$consultaw=substr($consulta1,0,$posb)." ".chr(34).".".chr(36)."v".$z.".".chr(34)." and  ";
						$z++;
						if(strlen($pregunta) == 0)
							$pregunta.="!isset(".chr(36)."v".$z.")";
						else
							$pregunta.=" or !isset(".chr(36)."v".$z.")";
						if(isset($alf[$i]))
							$consulta1=$consultaw.chr(34).".".chr(36)."v".$z.".".chr(34)."' ".substr($consulta1,$posa);
						else
							$consulta1=$consultaw.chr(34).".".chr(36)."v".$z.".".chr(34)." ".substr($consulta1,$posa);
						$consulta=$consulta1;
					}
				}
				#echo $consulta."<br>";
			}
			#echo $consulta1."<br>";
			#echo $pregunta."<br>";
			#for($i=0;$i<=$w;$i++)
				#echo $x[$i][0]."--".$x[$i][1]."<br>";
			$query="SELECT grupo FROM  usuarios WHERE codigo = '".$key."'";
			$err = mysql_query($query,$conex);
			$row = mysql_fetch_array($err);
			$datafile="./".$row[0]."/reportes/".$Codigo; 
			if(!file_exists($datafile))
			{
				$file = fopen($datafile,"w+");
				$REP="<html>".chr(13);
				$REP.="<head>".chr(13);
		  		$REP.="<title>MATRIX</title>".chr(13);
				$REP.="</head>".chr(13);
				$REP.="<body BGCOLOR=".chr(34)."".chr(34).">".chr(13);
				$REP.="<BODY TEXT=".chr(34)."#000066".chr(34).">".chr(13);
				$REP.="<center>".chr(13);
				$REP.="<table border=0 align=center>".chr(13);
				$REP.="<tr><td align=center bgcolor=".chr(34)."#cccccc".chr(34)."><A NAME=".chr(34)."Arriba".chr(34)."><font size=5>".$Titulo."</font></a></tr></td>".chr(13);
				$REP.="<tr><td align=center bgcolor=".chr(34)."#cccccc".chr(34)."><font size=2> <b> ".$Codigo."</b></font></tr></td></table>".chr(13);
				$REP.="</center>".chr(13);
				$REP.="<?php".chr(13);
				$REP.=" session_start();".chr(13);
				// $REP.=" if(!session_is_registered(".chr(34)."user".chr(34)."))".chr(13);
				$REP.=' if(!$_SESSION['.chr(34)."user".chr(34)."])".chr(13);
				$REP.=" echo ".chr(34)."error".chr(34).";".chr(13);
				$REP.=" else".chr(13);
				$REP.=" { ".chr(13);
				$REP.=chr(36)."key = substr(".chr(36)."user,2,strlen(".chr(36)."user));".chr(13);
				$REP.="include_once(".chr(34)."conex.php".chr(34).");".chr(13);
				$REP.="mysql_select_db(".chr(34)."matrix".chr(34).");".chr(13);
				$REP.="echo ".chr(34)."<form action='".$Codigo."' method=post>".chr(34).";".chr(13);
				$wz=-1;
				if($z > -1)
				{
					$REP.="if(".$pregunta.")".chr(13);
					$REP.="{".chr(13);
					$REP.=" echo  ".chr(34)."<center><table border=0>".chr(34).";".chr(13);
					$REP.=" echo ".chr(34)."<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>".chr(34).";".chr(13);
					$REP.=" echo ".chr(34)."<tr><td colspan=2 align=center><b>".$Titulo."</b></td></tr>".chr(34).";".chr(13);
					for($i=0;$i<=$w;$i++)
					{
						if(isset($act[$i]))
							if($x[$i][1] == 0)
							{
								if($lon[$i] < 0 or $lon[$i] > 50)
									$lon[$i] = 20;
								$wz++;
								$REP.=" echo  ".chr(34)."<tr><td bgcolor=#cccccc align=center>".ucfirst($namev[$i])."</td>".chr(34).";".chr(13);
								$REP.=" echo  ".chr(34)."<td bgcolor=#cccccc align=center><input type='TEXT' name='v".$wz."' size=".$lon[$i]." maxlength=".$lon[$i]."></td></tr>".chr(34).";".chr(13);
							}
							else
							{
								$wz++;
								$REP.=" echo  ".chr(34)."<tr><td bgcolor=#cccccc align=center>".ucfirst($namev[$i])." Inicial</td>".chr(34).";".chr(13);
								$REP.=" echo  ".chr(34)."<td bgcolor=#cccccc align=center><input type='TEXT' name='v".$wz."' size=20 maxlength=20></td></tr>".chr(34).";".chr(13);
								$wz++;
								$REP.=" echo  ".chr(34)."<tr><td bgcolor=#cccccc align=center>".ucfirst($namev[$i])." Final</td>".chr(34).";".chr(13);
								$REP.=" echo  ".chr(34)."<td bgcolor=#cccccc align=center><input type='TEXT' name='v".$wz."' size=20 maxlength=20></td></tr>".chr(34).";".chr(13);
							}
					}
					$REP.="echo ".chr(34)."<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>".chr(34).";".chr(13);
					$REP.="}".chr(13);
					$REP.="else".chr(13);
					$REP.="{".chr(13);
				}
				$REP.=chr(36)."query = ".chr(34).$consulta1.chr(34).";".chr(13);
				$REP.=chr(36)."err = mysql_query(".chr(36)."query,".chr(36)."conex);".chr(13);
				$REP.=chr(36)."num = mysql_num_rows(".chr(36)."err);".chr(13);
				$REP.=" echo ".chr(34)."<table border=1>".chr(34).";".chr(13);
				$fil=$k+1;
				$REP.=" echo ".chr(34)."<tr><td colspan=".$fil." align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>".chr(34).";".chr(13);
				$REP.=" echo ".chr(34)."<tr><td colspan=".$fil." align=center><b>DIRECCION DE INFORMATICA</b></td></tr>".chr(34).";".chr(13);
				$REP.=" echo ".chr(34)."<tr><td colspan=".$fil." align=center><b>".$Titulo."</b></td></tr>".chr(34).";".chr(13);
				$REP.=" echo ".chr(34)."<tr><td colspan=".$fil." align=center><b>".$Subtit."</b></td></tr>".chr(34).";".chr(13);
				$REP.=" echo ".chr(34)."<tr>".chr(34).";".chr(13);
				for($i=0;$i<=$k;$i++)
					if(isset($num[$i]))
						$REP.=" echo ".chr(34)."<td align=right bgcolor=#cccccc><b>".$name[$i]."</b></td>".chr(34).";".chr(13);
					else
						$REP.=" echo ".chr(34)."<td bgcolor=#cccccc><b>".$name[$i]."</b></td>".chr(34).";".chr(13);
				$REP.=" echo ".chr(34)."</tr>".chr(34)."; ".chr(13);
				$REP.=chr(36)."t=array();".chr(13);
				$wsw=0;
				for($i=0;$i<=$k;$i++)
				{
					if(isset($sum[$i]))
						$wsw=1;
					$REP.=chr(36)."t[".$i."] = 0;".chr(13);
				}
				$REP.="for (".chr(36)."i=0;".chr(36)."i<".chr(36)."num;".chr(36)."i++)".chr(13);
				$REP.="{".chr(13);
				$REP.=chr(36)."row = mysql_fetch_array(".chr(36)."err);".chr(13);
				$REP.=" echo ".chr(34)."<tr>".chr(34).";".chr(13);
				for($i=0;$i<=$k;$i++)
				{
					if(isset($sum[$i]))
					{
						$REP.=chr(36)."t[".$i."]+=".chr(36)."row[".$i."];".chr(13);
						$REP.=" echo ".chr(34)."<td align=right>".chr(34).".number_format(".chr(36)."row[".$i."],2,'.',',').".chr(34)."</td>".chr(34).";".chr(13);
					}
					else
						if(isset($num[$i]))
							$REP.=" echo ".chr(34)."<td align=right>".chr(34).".number_format(".chr(36)."row[".$i."],2,'.',',').".chr(34)."</td>".chr(34).";".chr(13);
						else
							$REP.=" echo ".chr(34)."<td>".chr(34).".".chr(36)."row[".$i."].".chr(34)."</td>".chr(34).";".chr(13);
				}
				$REP.=" echo ".chr(34)."</tr>".chr(34)."; ".chr(13);
				$REP.="}".chr(13);
				if($wsw == 1)
				{
					$REP.=" echo ".chr(34)."<tr><td  bgcolor=#FFCC66 colspan=".$fil." align=center><b>TOTALES</b></td></tr>".chr(34).";".chr(13);
					$REP.=" echo ".chr(34)."<tr>".chr(34).";".chr(13);
					for($i=0;$i<=$k;$i++)
						if(isset($sum[$i]))
							$REP.=" echo ".chr(34)."<td bgcolor=#99CCFF align=right><b>".chr(34).".number_format(".chr(36)."t[".$i."],2,'.',',').".chr(34)."</b></td>".chr(34).";".chr(13);
						else
							$REP.=" echo ".chr(34)."<td align=center bgcolor=#99CCFF><b> - </b></td>".chr(34).";".chr(13);
					$REP.=" echo ".chr(34)."</tr>".chr(34)."; ".chr(13);
				}
				$REP.=" echo ".chr(34)."</table>".chr(34)."; ".chr(13);
				if($z > -1)
					$REP.="}".chr(13);
				$REP.="}".chr(13);
				$REP.="?>".chr(13);
				$REP.="</body>".chr(13);
				$REP.="</html>".chr(13);
				 fwrite ($file,$REP);
				 fclose ($file);
				 echo "<table border=0 align=center>";
				 echo "<tr><td bgcolor=#dddddd><IMG SRC='/MATRIX/images/medical/root/clinica.png' ></td><td  align=center bgcolor=#dddddd ><font size=7><b>GENERADOR DE REPORTES</b></font></td></tr>";
				 echo "<tr><td align=center bgcolor=#dddddd colspan=2><font size=5>Reporte<b> ".$Codigo."</b> Generado</font></td></tr>";
				 echo "<tr><td bgcolor=#dddddd>VISTA PRELIMINAR</td><td bgcolor=#dddddd align=center><A HREF='".$datafile."' target = '_blank'><IMG SRC='/MATRIX/images/medical/root/VP.png' ></a></td></tr>";
				 echo "<tr><td bgcolor=#999999 colspan=2  align=center><font size=5><b>OPCIONES DE REGISTRO DEL REPORTE</b></font></td></tr>";
				$query="SELECT codigo,nombre FROM  formulario WHERE medico = '".$key."' order by codigo";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				echo "<td bgcolor=#dddddd>Formulario Asociado</td>";
				echo "<td bgcolor=#dddddd>";
				echo "<select name='formulario'>";
				for($f=0;$f<$num;$f++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</td></tr>";
				$query="SELECT codigo FROM  reportes WHERE medico = '".$key."' order by codigo desc";
				$err = mysql_query($query,$conex);
				$row = mysql_fetch_array($err);
				$num=$row[0];
				$num++;
				while(strlen($num) < 4)
					$num="0".$num;
				echo "<tr><td bgcolor=#dddddd>Numero del Reporte</td><td bgcolor=#dddddd>".$num."</td></tr>";
				echo "<tr><td bgcolor=#dddddd colspan=2 align=center>Desea Registrar Este Reporte ? <input type='RADIO' name=regfile value=1 checked> SI <input type='RADIO' name=regfile value=2> NO </td></tr>";
				echo "<td bgcolor=#cccccc align=center colspan=2><input type='submit' value='REGISTRAR'></td><tr></table>";
				echo "<input type='HIDDEN' name= 'num' value='".$num."'>";
				echo "<input type='HIDDEN' name= 'Titulo' value='".$Titulo."'>";
				echo "<input type='HIDDEN' name= 'Codigo' value='".$Codigo."'>";
				echo "<input type='HIDDEN' name= 'datafile' value='".$datafile."'>";
			 }
			 else
			 {
				 echo "<input type='HIDDEN' name= 'datafile' value='".$datafile."'>";
				 echo "<input type='HIDDEN' name= 'consulta' value='".$Q."'>";
				 echo "<input type='HIDDEN' name= 'Codigo' value='".$Codigo."'>";
				 echo "<center><IMG SRC='/matrix/images/medical/root/cabeza.gif' ><br><br>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>YA EXISTE UN REPORTE CON ESTE NOMBRE</MARQUEE></FONT><br><br>";
				echo "<table border=0 aling=center>";
				echo "<tr><td bgcolor=#dddddd  align=center>Desea Borrar Este Reporte ? <input type='checkbox' name='delfile'></td></tr>";
				echo "<td bgcolor=#cccccc align=center><input type='submit' value='BORRAR'></td><tr>";
				echo "</table></center>";			
			}
		}
	}
}
?>
</body>
</html>
