<html>
<head>
  <title>MATRIX</title>
  <!-- 
	  Se incluye script para encriptar en JS
		@date: 2021/09/15
		@by:	sebastian.nevado
				marlon.osorio
				daniel.corredor
  -->
  <script type="text/javascript" src="../../../include/root/cifrado/crypto-js.min.js"></script>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Consulta de Informacion x Query</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> consultas_matrix.php Ver. 2010-03-01</b></font></tr></td></table>
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
function buscar($data,$w,$it)
{
	$wsw=0;
	for ($i=0;$i<=$w;$i++)
	{
		if($data[$i] == $it)
			$wsw=1;
	}
	if($wsw == 1)
		return false;
	else
		return true;
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
	echo "<form action='consultas_matrix.php' method=post>";
	echo "<input type='HIDDEN' name= 'key' value='".$key."'>";
	

	

	if(isset($qwhere))
		$qwhere=stripslashes($qwhere);
	if(!isset($wc[0]))
	{
		$wc=array();
		$nc=-1;
	}
	if(!isset($radio) or (isset($radio) and $radio != "Radio7"))
	{
		if(!isset($consulta))
			$consulta="";
		if(isset($Tablas) and $Tablas != "Ninguna" and isset($radio) and $radio == "Radio1")
		{
			$ini=strpos($Tablas,"_");
			if(isset($Delete) and substr($Tablas,$ini+1,1) == "t")
			{
				$ini=strpos($Tablas,":");
				$query = "drop table ".substr($Tablas,0,$ini);
				$err = mysql_query($query,$conex);
				$ini1=strpos($Tablas,"_");
				$ini2=strpos($Tablas,":");
				$query = "delete from formulario where medico = '".substr($Tablas,0,$ini1)."' and codigo = '".substr($Tablas,$ini1+1,$ini2-$ini1-1)."'";
				$err = mysql_query($query,$conex);
			}
			else
				if(strlen($qtablas) > 0)
				{
					$tt=strpos($qtablas,substr($Tablas, 0,strpos($Tablas,":")));
					if($tt === false)
						$qtablas=$qtablas.",".substr($Tablas, 0,strpos($Tablas,":"));
				}
				else
				{
					$tt=strpos($qtablas,substr($Tablas, 0,strpos($Tablas,":")));
					if($tt === false)
						$qtablas=$qtablas.substr($Tablas, 0,strpos($Tablas,":"));
				}
		}
		if(isset($Campos) and $Campos != "Ninguno" and isset($radio) and $radio == "Radio2")
		{
			if($tipo == "1")
			{
				if(strlen($qcampos) > 0 and substr($qcampos,strlen($qcampos)-1,1) != "(")
					$qcampos=$qcampos.",".$Campos;
				else
					$qcampos=$qcampos.$Campos;
			}
			else
				$qwhere= " ".$qwhere.$Campos." ";
		}
		if(isset($radio) and $radio == "Radio3")
		{
			switch ($Tipoind)
			{
				case "q1":
					$consulta="select ";
					if (strlen($qcampos) > 0)
						$consulta=$consulta.$qcampos;
					else
						$consulta=$consulta." * ";
					$consulta=$consulta." from ".$qtablas;
					$qwhere =strtolower($qwhere);
					$qwhere = str_replace(" y "," and ",$qwhere);
					$qwhere = str_replace(" o "," or ",$qwhere);
					$qwhere =ltrim($qwhere);
					if(strlen($qwhere) > 0)
						if((!is_int(strpos($qwhere,"order ")) and !is_int(strpos($qwhere,"group "))) or strpos($qwhere,"order ") > 0 or strpos($qwhere,"group ") > 0)
							$consulta=$consulta." where ".$qwhere;
						else
							$consulta=$consulta." ".$qwhere;
				break;
				case "q2":
					$qcampos=str_replace($qtablas.".","",$qcampos);
					$qcampos="(".$qcampos.")";
					$consulta="create unique index ".$Nomindex." on ".$qtablas." ".$qcampos;
				break;
				case "q3":
					$qcampos=str_replace($qtablas.".","",$qcampos);
					$qcampos="(".$qcampos.")";
					$consulta="create index ".$Nomindex." on ".$qtablas." ".$qcampos;
				break;
			}
		}
		if(isset($radio) and $radio == "Radio4")
		{
			switch ($Operacion)
			{
				case "SUMAR":
					if(strlen($qcampos) > 0)
						$qcampos=$qcampos.",sum(";
					else
						$qcampos=$qcampos."sum(";
					break;
				case "CONTAR":
					if(strlen($qcampos) > 0)
						$qcampos=$qcampos.",count(*)";
					else
						$qcampos=$qcampos."count(*)";
					break;
				case "AGRUPAR":
					$qwhere=$qwhere." group by ";
					break;
				case "ORDENAR":
					$qwhere=$qwhere." order by ";
					break;
			}
		}
		if(isset($radio) and $radio == "Radio5")
		{
			$ini=strpos($Consultas,":");
			$query = "select * from consultas where medico='".$key."' and codigo ='".substr($Consultas,0,$ini)."'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num > 0)
			{
				$row = mysql_fetch_array($err);
				$consulta=$row[3];
			}
		}
		if(isset($radio) and $radio == "Radio6")
		{
			$query = "select * from consultas where medico='".$key."' and codigo ='".$Codigo."'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num > 0)
				$wpar=1;
			elseif(strlen($consulta) > 0)
						$wpar=2;
					else
						$wpar=0;
			switch ($wpar)
			{
				case 1:
					if(strlen($Descripcion) > 0 and strlen($consulta) > 0)
					{
						$query = "update consultas set descripcion='".$Descripcion."', query='".$consulta."' where medico='".$key."' and codigo='".$Codigo."'";
						$err = mysql_query($query,$conex);
					}
				break;
				case 2:
					if(strlen($Codigo) > 0 and strlen($Descripcion) > 0 and strlen($consulta) > 0)
					{
						$query = "insert consultas values ('".$key."','".$Codigo."','".$Descripcion."','".$consulta."')";
						$err = mysql_query($query,$conex);
					}
				break;
			}
		}
		echo "<table border=0 align=center>";
		echo "<tr><td bgcolor=#cccccc>TABLAS</td><td bgcolor=#cccccc><input type='RADIO' name=radio value=Radio1></td><td bgcolor=#cccccc> "; 
		if ($key != "root")
			$query = "select * from formulario where medico='".$key."'";
		else
			$query = "select * from formulario order by medico,codigo";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
			echo "<select name='Tablas'>";
			echo "<option>Ninguna</option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if (isset($Tablas) and $row[1] == substr($Tablas, strpos($Tablas,"_")+1, 6) and $row[0] == substr($Tablas,0,strpos($Tablas,"_")))
					echo "<option selected>".$row[0]."_".$row[1].":".$row[2]."</option>";
				else
					echo "<option>".$row[0]."_".$row[1].":".$row[2]."</option>";
			}
		}
		echo "</select> Borrar Tabla Temporal <input type='checkbox' name=Delete></td></tr>";	
		echo "<tr><td bgcolor=#cccccc>CAMPOS</td><td bgcolor=#cccccc><input type='RADIO' name=radio value=Radio2></td><td bgcolor=#cccccc> "; 
		echo "<select name='Campos'>";
		echo "<option>Ninguno</option>";
		if(isset($Tablas) and $Tablas != "Ninguna")
		{
			$TAB=substr($Tablas, 0,strpos($Tablas,":"));
			$ini=strpos($Tablas,"_");
			if (substr($Tablas,$ini+1,1) == "t")
				$query="show columns from ".substr($Tablas, 0,strpos($Tablas,":"));
			else
				if($key != "root")
					$query = "select * from det_formulario where medico='".$key."' and codigo='".substr($Tablas, strpos($Tablas,"_")+1, 6)."'";
				else
					$query = "select * from det_formulario where medico='".substr($Tablas,0,strpos($Tablas,"_"))."' and codigo='".substr($Tablas, strpos($Tablas,"_")+1, 6)."'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if (substr($Tablas,$ini+1,1) == "t")
						$item=$row[0];
					else
						$item=$row[3];
					$it=$TAB.".".$item;
					if(buscar($wc,$nc,$it) and isset($radio) and $radio == "Radio1")
					{
						$nc++;
						$wc[$nc]=$TAB.".".$item;
					}
				}
				for ($i=0;$i<=$nc;$i++)
				{	
					if (isset($Campos) and $wc[$i] == $Campos)
						echo "<option selected>".$wc[$i]."</option>";
					else
						echo "<option>".$wc[$i]."</option>";
				}
			}
		}
		echo "</select>  En Campos<input type='RADIO' name=tipo value=1 checked> En Condiciones<input type='RADIO' name=tipo value=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc>CONFORMAR QUERY</td><td bgcolor=#cccccc><input type='RADIO' name=radio value=Radio3></td><td bgcolor=#cccccc> "; 
		echo "Query Normal<input type='RADIO' name=Tipoind value=q1 checked>&nbspIndice Unico<input type='RADIO' name=Tipoind value=q2>&nbspIndice<input type='RADIO' name=Tipoind value=q3>Nombre Indice&nbsp<input type='TEXT' name='Nomindex' size=20 maxlength=20></td></tr>";
		echo "<tr><td bgcolor=#cccccc>OPERACIONES</td><td bgcolor=#cccccc><input type='RADIO' name=radio value=Radio4></td><td bgcolor=#cccccc> "; 
		echo "<select name='Operacion'>";
		echo "<option>Ninguno</option>";
		echo "<option>SUMAR</option>";
		echo "<option>CONTAR</option>";
		echo "<option>AGRUPAR</option>";
		echo "<option>ORDENAR</option>";
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc>CONSULTAS</td><td bgcolor=#cccccc><input type='RADIO' name=radio value=Radio5></td><td bgcolor=#cccccc> "; 
		$query = "select * from consultas where medico='".$key."'";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
			echo "<select name='Consultas'>";
			echo "<option>Ninguna</option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if (isset($Consultas) and $row[1] == substr($Consultas, strpos($Tablas,"_")+1, 6))
					echo "<option selected>".$row[1].":".$row[2]."</option>";
				else
					echo "<option>".$row[1].":".$row[2]."</option>";
			}
		}
		echo "</td></tr>";	
		echo "<tr><td bgcolor=#cccccc>GRABAR CONSULTA</td><td bgcolor=#cccccc><input type='RADIO' name=radio value=Radio6></td><td bgcolor=#cccccc>&nbspCodigo&nbsp<input type='TEXT' name='Codigo' size=6 maxlength=6>&nbspDescripcion&nbsp<input type='TEXT' name='Descripcion' size=20 maxlength=20></td></tr>";
		echo "<tr><td bgcolor=#cccccc>CONSULTA COMPLETA</td><td bgcolor=#cccccc><input type='RADIO' name=radio value=Radio7></td><td bgcolor=#cccccc> Crear Tabla Temporal <input type='checkbox' name=new>&nbspNombre : <input type='TEXT' name='Nombre' size=30 maxlength=30></td></tr>";
		/**
		 * Se agrega funcion encriponclick para encriptar la consulta antes de enviarla
		 * @date: 2021/09/15 
		 * @by: sebastian.nevado
		 * 		marlon.osorio
		 * 		daniel.corredor
		 *  */ 
		echo "<tr><td bgcolor='#cccccc' align=center colspan=3><input type='submit' onclick='encriponclick(\"consulta\")' onlvalue='IR'></td></tr></table>";
		
		echo "<table border=0 align=center cellpadding=3>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>TABLAS</td><td bgcolor=#cccccc>CAMPOS</td><td bgcolor=#cccccc>CONDICIONES</td></tr>";
		echo "<tr><td bgcolor=#cccccc> "; 
		if(isset($qtablas))
			echo "<textarea name='qtablas' cols=20 rows=5>".$qtablas."</textarea></td>";
		else
			echo "<textarea name='qtablas' cols=20 rows=5></textarea></td>";
		echo "<td bgcolor=#cccccc> "; 
		if(isset($qcampos))
			echo "<textarea name='qcampos' cols=20 rows=5>".$qcampos."</textarea></td>";
		else
			echo "<textarea name='qcampos' cols=20 rows=5></textarea></td>";
		echo "<td bgcolor=#cccccc> "; 
		if(isset($qwhere))
			echo "<textarea name='qwhere' cols=20 rows=5>".$qwhere."</textarea></td></tr>";
		else
			echo "<textarea name='qwhere' cols=20 rows=5></textarea></td></tr>";
		echo "<br><br></table>";
		echo "<table border=0 align=center cellpadding=3>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>CONSULTA</td></tr>";
		echo "<tr><td bgcolor=#cccccc> "; 
		if(isset($consulta))
			echo "<textarea name='consulta' id='consulta' cols=60 rows=5>".$consulta."</textarea></td></tr>";
		else
			echo "<textarea name='consulta' id='consulta' cols=60 rows=5></textarea></td></tr>";
		echo "<br><br></table>";
		$qry="";
		echo "<input type='HIDDEN' name= 'nc' value='".$nc."'>";	
		echo "<input type='HIDDEN' name= 'qry' value='".$qry."'>";	
		for ($i=0;$i<=$nc;$i++)
			echo "<input type='HIDDEN' name= 'wc[".$i."]' value='".$wc[$i]."'>";	
	}
	else
	{
		/**
		 * Se agrega funcion MyDecrypt para desencriptar la consulta y poderla ejecutar
		 * @date: 2021/09/15 
		 * @by: sebastian.nevado
		 * 		marlon.osorio
		 * 		daniel.corredor
		 *  */ 
		$consulta = Cifrado::myDecrypt($consulta );
		
		if(isset($new))
		{
			$query =strtolower($consulta);
			$query=stripslashes($query);
			$query="create table ".$key."_t".substr(date("his"),0,5)." as ".$query;
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$ini=strpos($query,"_t");
			$Codigo=substr($query,$ini+1,strlen($query));
			$query = "insert formulario values ('".$key."','".$Codigo."','".$Nombre."','T','A')";
			$err = mysql_query($query,$conex);
		}
		echo "<input type='HIDDEN' name= 'radio' value='".$radio."'>";	
		$consulta=stripslashes($consulta);
		$cons_new="";
		for ($i=0;$i<strlen($consulta);$i++)
		{
			if(substr($consulta,$i,1)=="'")
				$cons_new=$cons_new." ";
			else
				$cons_new=$cons_new.substr($consulta,$i,1);
		}
		$qry=stripslashes($qry);
		//echo "CONSULTA:".$cons_new."<br>";
		//echo "qry:".$qry."<br>";
		if(stripslashes($qry) != $cons_new)
		{
			$qry=$cons_new;
			unset($Inicial);
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
		echo "<input type='HIDDEN' name= 'Final' value='".$Final."'>";	
		echo "<input type='HIDDEN' name= 'qry' value='".$qry."'>";	
		echo "<table border=0 align=center cellpadding=3>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>CONSULTA</td></tr>";
		echo "<tr><td bgcolor=#cccccc> "; 
		echo "<textarea name='consulta' id='consulta' cols=60 rows=5>".$consulta."</textarea></td></tr>";
		/**
		 * Se agrega funcion encriponclick para encriptar la consulta antes de enviarla
		 * @date: 2021/09/15 
		 * @by: sebastian.nevado
		 * 		marlon.osorio
		 * 		daniel.corredor
		 *  */ 
		echo "<tr><td bgcolor='#cccccc' align=center><input type='submit' onclick='encriponclick(\"consulta\")' value='IR'>";
		if(isset($back))
			echo "<input type='checkbox' name=back checked>Back</td></tr>";
		else
			echo "<input type='checkbox' name=back>Back</td></tr>";
		echo "<br><br></table>";
		echo "<br><br>";
		echo "<table border=0 align=center cellpadding=3>";
		echo "<tr><td bgcolor='#999999' align=center colspan=4><B>PROCESOS</b></td></tr>";
		echo "<tr><td bgcolor='#dddddd' align=center><b>Generacion de Archivo Plano</b></td><td bgcolor='#dddddd' align=center><A HREF='dump.php?consulta=".$consulta."&key=".$key."' target='main'><IMG SRC='/MATRIX/images/medical/root/ap.png' ></A></td>";
		echo "<td bgcolor='#dddddd' align=center><b>Generacion de Reporte</b></td><td bgcolor='#dddddd' align=center><A HREF='genrep.php?consulta=".$consulta."&key=".$key."' target='main'><IMG SRC='/MATRIX/images/medical/root/rep.png' ></A></td></tr>";
		echo "</table><br>";
		$query =strtolower($consulta);
		$query=stripslashes($query);
		$inif=strpos($query,"from");
		$iniw=strpos($query,"where");
		if(is_int($inif) and $inif > 0 and is_int($iniw) and $iniw > 0)
			$subquery=substr($query,$inif,$iniw-$inif);
		elseif(is_int($inif) and $inif > 0 and !is_int($iniw))
			$subquery=substr($query,$inif,strlen($query));
		$priv=0;
		$priv=$priv + substr_count($query,"consultas");
		$priv=$priv + substr_count($query,"formulario");
		$priv=$priv + substr_count($query,"det_formulario");
		$priv=$priv + substr_count($query,"selecciones");
		$priv=$priv + substr_count($query,"det_selecciones");
		$priv=$priv + substr_count($query,"numeracion");
		$priv=$priv + substr_count($query,"procesos");
		$priv=$priv + substr_count($query,"seguridad");
		$priv=$priv + substr_count($query,"usuarios");
		#echo $priv;
		if (((substr($query,0,6)=="select" or substr($query,0,19)=="create unique index" or substr($query,0,12)=="create index") and $priv == 0 and $key != "root") or $key == "root" )
		{
			$querytime_before = array_sum(explode(' ', microtime()));
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$querytime_after = array_sum(explode(' ', microtime(true)));
			if (substr($query,0,6)=="select")
			{
				$num = mysql_num_rows($err) or die("Sin Registros");
				$Totales=$num;
				$DIFF=$querytime_after - $querytime_before;
				echo "Tiempo : ".$DIFF." Segundo(s)<br>";
			}
			else
				$Totales=0;
		}
		else
			$Totales=0;
		echo "<input type='HIDDEN' name= 'Totales' value='".$Totales."'>";
		//$query=stripslashes($query);
		//$query=strtolower($query);
		#echo $query."<br>";
		if (substr($query,0,6)=="select")
			$query = $query." limit ".$Inicial.",30";
		if((substr($query,0,6)=="select"  and $priv == 0 and $key != "root") or $key == "root")
		{
			if (substr($query,0,6)=="select")
			{
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err) or die("Sin Registros");
			}
			else
				$num=0;
		}
		else
			$num=0;
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
			for ($i=0;$i<$k;$i++)
			{
  				echo "<td bgcolor=".$color."><font size=2><b>It-".$i."</b></font></td>";
  			}
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
					echo "</tr>";
				}
				echo "</tabla>";
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
}
?>
</body>
</html>
