<html>
<head>
<script language="Javascript"></script>
	<script>
	function nuevoAjax()
	{ 
		var xmlhttp=false; 
		try 
		{ 
			xmlhttp=new ActiveXObject("Msxml2.XMLHTTP"); 
		}
		catch(e)
		{ 
			try
			{ 
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); 
			} 
			catch(E) { xmlhttp=false; }
		}
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') { xmlhttp=new XMLHttpRequest(); } 

		return xmlhttp; 
	}

	function ajaxquery(fila)
	{
		var x1,x2,x3
		//alert("fila="+fila);
		x1 = document.getElementById("x1").value;
		x2 = document.getElementById("x2").value;
		x3 = document.getElementById("x3").value;
		ajax=nuevoAjax();
		ajax.open("GET", "reporteajax.php?wanop="+x1+"&wper1="+x2+"&wcco1="+x3, true);
		ajax.onreadystatechange=function() 
		{ 
			if (ajax.readyState==4)
			{
				alert ("Procesado");
				document.getElementById(+fila).innerHTML=ajax.responseText;
			} 
		}
		ajax.send(null);
	}
	</script>
</head>
<body>
<?php
include_once("conex.php");
	echo "<div id='1'>";
	if(!isset($wcco1))
	{
		//document.getElementById(+fila).innerHTML=ajax.responseText;
		echo "<center> ";
		echo "<table border=0> ";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr> ";
		echo "<tr><td align=center colspan=2>INFORME DE INSUMOS PARA CONSUMO X UNIDAD</td></tr> ";
		echo "<tr><td bgcolor=#cccccc align=center>Año de Proceso</td> ";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' id='x1' size=4 maxlength=4></td></tr> ";
		echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td> ";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' id='x2' size=2 maxlength=2></td></tr> ";
		echo "<tr><td bgcolor=#cccccc align=center>Centro Costos Inicial</td> ";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' id='x3' size=4 maxlength=4></td></tr> ";
		$id="ajaxquery('1')";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='button' value='ENTER' Onclick=".$id."></td></tr>";
		echo "</table><br><br>  ";
		echo "</center> ";
		//echo "<div id='1'><IMG SRC='/matrix/images/medical/root/pronto.gif'></div>";
	}
	else
	{
		echo "<center> ";
		

		

		$query = "SELECT ccocod, cconom from costosyp_000005 ";
		$query .= "  where ccocod = '".$wcco1."' ";
		$err = mysql_query($query,$conex);
		$row = mysql_fetch_array($err);
		echo "<IMG SRC='/matrix/images/medical/root/pronto.gif'>";
		echo "<table border=0>";
		echo "<tr><td align=center colspan=2><font size=2>PROMOTORA MEDICA LAS AMERICAS S.A.</font></td></tr>";
		echo "<tr><td align=center colspan=2><font size=2>DIRECCION DE INFORMATICA</font></td></tr>";
		echo "<tr><td align=center colspan=2><font size=2>INFORME DE INSUMOS PARA CONSUMO X UNIDAD</font></td></tr>";
		echo "<tr><td align=center bgcolor=#999999 colspan=2><font size=2><b>UNIDAD : ".$row[0]."-".$row[1]."</b></font></td></tr>";
		echo "<tr><td bgcolor=#CCCCCC align=center><b>CODIGO</b></td><td bgcolor=#CCCCCC align=center><b>DESCRIPCION</b></td></tr>";
		$query = "select costosyp_000002.Almcod,costosyp_000002.Almdes from costosyp_000002 ";
		$query .= " where costosyp_000002.almcco = '".$wcco1."' ";
		$query .= "   and costosyp_000002.almano = ".$wanop;
	    $query .= "   and costosyp_000002.almmes = ".$wper1;
		$query .= "   and costosyp_000002.Almcod not in  ";
		$query .= "   (select costosyp_000099.Pqucod from costosyp_000099 where costosyp_000099.Pqucco='".$wcco1."' and costosyp_000099.Pqutip='2'  ";
		$query .= "    union  ";
		$query .= "    select costosyp_000100.Procod from costosyp_000100 where costosyp_000100.Procco='".$wcco1."' and costosyp_000100.Protip='2')  ";
			//$query .= "    union  ";
			//$query .= "    select costosyp_000130.Ifains from costosyp_000130 where costosyp_000130.Ifacco='".$wcco1."') ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		for ($i=0;$i<$num;$i++)
		{
			if($i % 2 == 0)
				$color="#99CCFF";
			else
				$color="#ffffff";
			$row = mysql_fetch_array($err);
			echo "<tr><td bgcolor=".$color."><font size=2>".$row[0]."</font></td><td bgcolor=".$color."><font size=2>".$row[1]."</font></td></tr>";
		}
		echo "</table>";
		echo "</center>";
	}
	echo"</div>";
?>
</body> 
</html> 