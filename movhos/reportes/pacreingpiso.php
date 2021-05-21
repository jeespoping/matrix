<HTML>
<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>
<HEAD>
<TITLE>PACIENTES QUE REINGRESAN A LA UNIDAD</TITLE>
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
	

	

 echo "<form name='pacreingpiso' action='pacreingpiso.php?wemp_pmla=".$wemp_pmla."' method=post>";  
 
 if (!isset($wfec1) or $wfec1=='')
 {

	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";
    echo "<tr>";
	echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>PACIENTES QUE REINGRESAN AL PISO<br></font></b>";   
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
	include_once("root/comun.php");
	$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	
	echo "<center><table border=0>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>PACIENTES REINGRESAN A CUIDADOS ESPECIALES</font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>Periodo: ".$wfec1." Al ".$wfec2."</font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=2><i>PROGRAMA: pacreingpiso.php Ver. 2017/07/17<br>AUTOR: Gabriel Agudelo</font></b><br>";
    echo "</table>";

    echo "<br>";
     $query="Select b.Fecha_data,Eyrhis,Eyring,Eyrsor,Eyrsde,Eyrhor,Eyrhde"
     ." from   ".$wmovhos."_000032 a,".$wmovhos."_000017 b "
     ." where   a.Fecha_ing between '".$wfec1."' and '".$wfec2."'"
	 ." and  a.Servicio = '1282'"
	 ." and  a.Num_ing_serv >= '2'"
	 ." and  a.Historia_clinica = b.Eyrhis"
	 ." and  a.Num_ingreso = b.Eyring"
	 ." and  b.Eyrtip = 'Entrega' "
	 ." and  b.Eyrest = 'on'"
	 ." group by Eyrhis,Eyring,Eyrsor,Eyrsde,Eyrhor,Eyrhde"
     ." order by Eyrhis,Eyring,b.Fecha_data";
     $err1 = mysql_query($query,$conex);
     $num1 = mysql_num_rows($err1);
	
		echo "<table border=1 cellspacing=1 cellpadding=1 align=center size=4>"; 
		echo "<tr>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>FECHA</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2>&nbsp<b>HISTORIA</b>&nbsp</td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2>&nbsp&nbsp<b>ING</b>&nbsp&nbsp</td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>CCO ORIGEN</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>CCO DESTINO</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>HAB ORIGEN</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>HAB DESTINO</b></td>";
		echo "</tr>";  
		$bandera = 0;
		$f1 =1;
		$temhis=0;
	for ($i=1;$i<=$num1;$i++)
	{
		$row1 = mysql_fetch_array($err1);
		
		if ($bandera == 0)
			{	
				$temhis = $row1[1];
				$bandera = 1;
			}
		if ($temhis != $row1[1])
			{
				$f1 = $f1 + 1;
				$temhis = $row1[1];
			}
		 
		 if (is_int ($f1/2))
		  {
			$wcf="F8FBFC";  // color de fondo
		  }
		 else
		  {
			//$wcf="DFF8FF"; // color de fondo 0080FF
			$wcf="E0F8EC";
		  }
	   
   	  echo "<Tr bgcolor=".$wcf.">";
      echo "<td align=center><font size=3>".$row1[0]."</font></td>";
	  echo "<td align=center><font size=3>".$row1[1]."</font></td>";
      echo "<td align=center><font size=3>".$row1[2]."</font></td>";
      echo "<td align=center><font size=3>".$row1[3]."</font></td>";
      echo "<td align=center><font size=3>".$row1[4]."</font></td>";
      echo "<td align=center><font size=3>".$row1[5]."</font></td>";
	  echo "<td align=center><font size=3>".$row1[6]."</font></td>"; 
	  echo "</tr>";  
	}
	echo "</table>"; 

 }   

echo "</BODY>";
echo "</HTML>";	

?>