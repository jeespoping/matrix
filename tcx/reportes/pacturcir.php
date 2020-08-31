<HTML>
<HEAD>
<TITLE>PACIENTES ATENDIDOS PROGRAMA DE TURNOS CIRUGIA 2017-10-18</TITLE>
</HEAD>
<BODY>

  <!-- Estas 5 lineas es para que funcione el Calendar al capturar fechas -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>    
    
<?php
include_once("conex.php");

session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");
        
 $key = substr($user,2,strlen($user));
	

	

 echo "<form name='pacturcir' action='pacturcir.php' method=post>";  
 
 if (!isset($wfec1) or $wfec1=='')
 {

	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";
    echo "<tr>";
	echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>PACIENTES ATENDIDOS TURNOS DE CIRUGIA<br></font></b>";   
	echo "</tr>";

	
  if (!isset($wfec1))   // Si no esta seteada entonces la inicializo con la fecha actual
    $wfec1=date("Y-m-d");
    
    echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Fecha Inicial<br></font></b>";   
   	$cal="calendario('wfec1','1')";
	echo "<input type='TEXT' name='wfec1' size=10 maxlength=10  id='wfec1'  value=".$wfec1." class=tipo3><button id='trigger1' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec1',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php

  if (!isset($wfec2))   // Si no esta seteada entonces la inicializo en el ultimo dia del mes actual con formato aaaa-mm-dd
    $wfec2=date("Y-m-d");
  
    echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Fecha Final  <br></font></b>";   
   	$cal="calendario('wfec2','1')";
	echo "<input type='TEXT' name='wfec2' size=10 maxlength=10  id='wfec2' value=".$wfec2." class=tipo3><button id='trigger2' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec2',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php
	
   echo "<tr><td align=center bgcolor=#cccccc colspan=4><input type='submit' value='Generar'></td>";          //submit osea el boton de Generar o Aceptar
   echo "</tr>";
   echo "</table>";
   
 }	
 else      // Cuando ya estan todos los datos escogidos
 {
	echo "<center><table border=0>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>PACIENTES TURNOS DE CIRUGIA</font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>Periodo: ".$wfec1." Al ".$wfec2."</font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=2><i>PROGRAMA: pacturcir.php Ver. 2014/09/18<br>AUTOR: Gabriel Agudelo</font></b><br>";
    echo "</table>";

    echo "<br>";
	 //$query="Select Turfec,Orihis ,Oriing ,Turdoc,Turnom,Tureps,Empnom,Turcir,Turmed"
	 //." from   tcx_000011 left join root_000037 ON (Turdoc = Oriced and Oriori = '01'),cliame_000024 "
	 //." where  Turfec between '".$wfec1."' and '".$wfec2."'"
	 //." and   Turest = 'on' and  Tureps = Empcod";
	 //                  0      1      2      3      4      5       6       7       8      9     10     11     12     13     14   
	 $query  = "Select Turtur,Turqui,Turhin,Turhfi,Turfec,Turhis ,Turnin ,Turtcx,Turdoc,Turnom,Empnom,Turcir,Mednom,Medesp,Espdet ";
	 $query .= " from  tcx_000011, tcx_000010, tcx_000006, tcx_000005, cliame_000024 ";
	 $query .= " where Turfec between '".$wfec1."' and '".$wfec2."'";
	 $query .= "   and Turest = 'on' "; 
	 $query .= "   and Turtur = Mmetur ";
	 $query .= "   and Mmemed = Medcod ";
	 $query .= "   and Medesp = Espcod ";
	 $query .= "   and Tureps = Empcod ";
	 $query .= " order by 1 ";
	 $err1 = mysql_query($query,$conex);
	 $num1 = mysql_num_rows($err1);
	echo "<table border=0 align=center size='300'>"; 
	echo "<tr>";
	echo "<td bgcolor='#DDDDDD' align=center><font text color=#000000 size=2><b>TURNO</b></td>";
	echo "<td bgcolor='#DDDDDD' align=center><font text color=#000000 size=2><b>QX</b></td>";
	echo "<td bgcolor='#DDDDDD' align=center><font text color=#000000 size=2><b>HI</b></td>";
	echo "<td bgcolor='#DDDDDD' align=center><font text color=#000000 size=2><b>HF</b></td>";
	echo "<td bgcolor='#DDDDDD' align=center><font text color=#000000 size=2><b>FECHA</b></td>";
	echo "<td bgcolor='#DDDDDD' align=center><font text color=#000000 size=2>&nbsp<b>HISTORIA</b>&nbsp</td>";
	echo "<td bgcolor='#DDDDDD' align=center><font text color=#000000 size=2>&nbsp&nbsp<b>ING</b>&nbsp&nbsp</td>";
	echo "<td bgcolor='#DDDDDD' align=center><font text color=#000000 size=2><b>TCX</b></td>";
	echo "<td bgcolor='#DDDDDD' align=center><font text color=#000000 size=2><b>DOCUMENTO</b></td>";
	echo "<td bgcolor='#DDDDDD' align=center><font text color=#000000 size=2><b>PACIENTE</b></td>";
	echo "<td bgcolor='#DDDDDD' align=center><font text color=#000000 size=2><b>ENTIDAD</b></td>";
	echo "<td bgcolor='#DDDDDD' align=center><font text color=#000000 size=2><b>PROCEDIMIENTO</b></td>";
	echo "<td bgcolor='#DDDDDD' align=center><font text color=#000000 size=2><b>MEDICO</b></td>";
	echo "<td bgcolor='#DDDDDD' align=center><font text color=#000000 size=2><b>ESPECIALIDAD</b></td>";
	echo "</tr>";  
	$klave = "";
	$co = 0;
	for ($i=1;$i<=$num1;$i++)
	{
		$row1 = mysql_fetch_array($err1);
		if($row1[0] != $klave)
		{
			if($i > 1)
			{
				$co++;
				if (is_int ($co/2))
				{
					$wcf="F8FBFC";  // color de fondo
				}
				else
				{
					$wcf="DFF8FF"; // color de fondo
				}
				echo "<Tr bgcolor=".$wcf.">";
				echo "<td align=center><font size=1>".$waux[0]."</font></td>";
				echo "<td align=center><font size=1>".$waux[1]."</font></td>";
				echo "<td align=center><font size=1>".$waux[2]."</font></td>";
				echo "<td align=center><font size=1>".$waux[3]."</font></td>";
				echo "<td align=center><font size=1>".$waux[4]."</font></td>";
				echo "<td align=center><font size=1>".$waux[5]."</font></td>";
				echo "<td align=center><font size=1>".$waux[6]."</font></td>"; 
				echo "<td align=center><font size=1>".$waux[7]."</font></td>";
				echo "<td align=center><font size=1>".$waux[8]."</font></td>";
				echo "<td align=center><font size=1>".$waux[9]."</font></td>";
				echo "<td><font size=1>".$waux[10]."</font></td>";
				echo "<td><font size=1>".$waux[11]."</font></td>";
				if($wmed != "")
				{
					echo "<td><font size=1>".$wmed."</font></td>";
					echo "<td><font size=1>".$wesp2."</font></td>";
				}
				else
				{
					echo "<td><font size=1>".$wane."</font></td>";
					echo "<td><font size=1>".$wesp1."</font></td>";
				}
				echo "</tr>"; 
			}
			$klave = $row1[0]; 
			$wmed = "";
			$wane = "";
			$wesp1 = "";
			$wesp2 = "";
			$waux = array();
			for ($j=0;$j<15;$j++)
				$waux[$j] = $row1[$j];
		}
		if($row1[13] == "021")
		{
			$wane .= $row1[12];
			$wesp1 .= $row1[13]." ".$row1[14];
		}
		else
		{
			$wmed .= $row1[12];
			$wesp2 .= $row1[13]." ".$row1[14];
		}
	}
	$co++;
	if (is_int ($co/2))
	{
		$wcf="F8FBFC";  // color de fondo
	}
	else
	{
		$wcf="DFF8FF"; // color de fondo
	}
	echo "<Tr bgcolor=".$wcf.">";
	echo "<td align=center><font size=1>".$waux[0]."</font></td>";
	echo "<td align=center><font size=1>".$waux[1]."</font></td>";
	echo "<td align=center><font size=1>".$waux[2]."</font></td>";
	echo "<td align=center><font size=1>".$waux[3]."</font></td>";
	echo "<td align=center><font size=1>".$waux[4]."</font></td>";
	echo "<td align=center><font size=1>".$waux[5]."</font></td>";
	echo "<td align=center><font size=1>".$waux[6]."</font></td>"; 
	echo "<td align=center><font size=1>".$waux[7]."</font></td>";
	echo "<td align=center><font size=1>".$waux[8]."</font></td>";
	echo "<td align=center><font size=1>".$waux[9]."</font></td>";
	echo "<td><font size=1>".$waux[10]."</font></td>";
	echo "<td><font size=1>".$waux[11]."</font></td>";
	if($wmed != "")
	{
		echo "<td><font size=1>".$wmed."</font></td>";
		echo "<td><font size=1>".$wesp2."</font></td>";
	}
	else
	{
		echo "<td><font size=1>".$wane."</font></td>";
		echo "<td><font size=1>".$wesp1."</font></td>";
	}
	echo "</tr>"; 
	$wcf="#999999";
	echo "<Tr bgcolor=".$wcf.">";
	echo "<td colspan=14><font size=2><b>TOTAL ACTOS QUIRURGICOS : ".$co."</b></font></td>";
	echo "</table>"; 

 }   

echo "</BODY>";
echo "</HTML>";	

?>
