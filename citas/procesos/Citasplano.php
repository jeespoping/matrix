<html>
<head>
  	<title>MATRIX Citasplano 2014-06-06</title>
  	    <title>Zapatec DHTML Calendar</title>

<!-- Loading Theme file(s) -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />

<!-- Loading Calendar JavaScript files -->
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <!-- Loading language definition file -->
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>
  	<script type="text/javascript">
	function ira()
	{
		document.Citasplano.wfec.focus();
	}
  	
	</script>
</head>
<body onload=ira() BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
// @session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='Citasplano' action='Citasplano.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<input type='HIDDEN' name= 'wtipo' value='".$wtipo."'>";
	if(!isset($wfec))
	{
		$wfec=date("Y-m-d");
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>GENERACION DE ARCHIVO PLANO PARA MENSAJES DE CITAS</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha De Proceso</td>";
		echo "<td bgcolor=#cccccc align=center valign=center><input type='TEXT' name='wfec' size=10 maxlength=10 id='wfec' readonly='readonly' value=".$wfec." class=tipo6>&nbsp;&nbsp;<IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger1'></IMG></td></tr>";
		?>
		<script type="text/javascript">//<![CDATA[
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
		//]]></script>
		<?php
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		echo "<table border=0 align=center>";
		echo "<tr><td align=center><IMG SRC='/matrix/images/medical/citas/clinica_las_americas.png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font size=4 face='tahoma'><b>GENERACION DE ARCHIVO PLANO PARA MENSAJES DE CITAS</font><font size=2 face='tahoma'> Ver 2012-06-07</font></td></tr>";
		$color="#dddddd";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Fecha De Proceso : </b>".$wfec."</td></tr>";
		echo "</tr></table><br><br>";
		if($wtipo == 1)
		{
			//                   0                          1       2                         3            4                   5         6                   7                   8
			$query  = "select Cod_med,".$empresa."_000008.Nombre,Cod_exa,".$empresa."_000006.Descripcion,Fecha,".$empresa."_000001.Hi,Nom_pac,".$empresa."_000002.Descripcion,Telefono ";
			$query .= " from ".$empresa."_000001,".$empresa."_000008,".$empresa."_000006,".$empresa."_000002 ";
			$query .= " where fecha = '".$wfec."' "; 
			$query .= "   and Cod_med = ".$empresa."_000008.Codigo "; 
			$query .= "   and Cod_exa = ".$empresa."_000006.Codigo "; 
			$query .= "   and nit_resp = ".$empresa."_000002.Nit ";  
			$query .= "   and ".$empresa."_000001.Activo = 'A' "; 
			$query .= " Group by 1,3,5,6,7,8,9 "; 
			$query .= " order by ".$empresa."_000001.hi ";
		}
		else
		{
			//                   0                          1            2                         3            4                   5         6                   7                   8
			$query  = "select Cod_equ,".$empresa."_000010.Descripcion,Cod_exa,".$empresa."_000011.Descripcion,fecha,".$empresa."_000009.hi,nom_pac,".$empresa."_000002.Descripcion,telefono "; 
			$query .= "   from ".$empresa."_000009,".$empresa."_000010,".$empresa."_000011,".$empresa."_000002 "; 
			$query .= "   where fecha = '".$wfec."' "; 
			$query .= " 	and Cod_equ = ".$empresa."_000010.Codigo "; 
			$query .= " 	and Cod_exa = ".$empresa."_000011.Codigo "; 
			$query .= " 	and nit_res = ".$empresa."_000002.Nit "; 
			$query .= " 	and ".$empresa."_000009.Activo = 'A' "; 
			$query .= "  Group by 1,3,5,6,7,8,9"; 
			$query .= " order by ".$empresa."_000009.hi "; 
		}
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<table border=0 align=center>";
		$datafile="./../../planos/Citasplano.txt"; 
		$file = fopen($datafile,"w+");
		$witem=0;
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$witem++;
			$registro  = $row[1].",";										//Medico
			$registro .= $row[3].",";										//Procedimiento
			$registro .= $row[4].",";										//Fecha Procedimiento
			$registro .= $row[5].",";										//Hora Inicio Procedimiento
			$registro .= $row[6].",";										//Nombre del Paciente
			$registro .= $row[7].",";										//Empresa Responsable
			$registro .= $row[8].",";										//Telefono del Paciente	
			$registro=$registro.chr(13).chr(10);
			fwrite ($file,$registro);
		}
		echo "<tr><td bgcolor=#999999 align=center><b>TOTAL REGISTROS : ".$witem."</b></td></tr>";	
		fclose ($file);
		echo "<tr><td bgcolor=#dddddd colspan=8 align=center><b><A href=".$datafile.">Click Derecho Para Bajar el Archivo</A></b></td></tr>";
		echo"</table>";
	}
}
?>
</body>
</html>
