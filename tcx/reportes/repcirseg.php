<HTML>
<HEAD>
<TITLE>SEGUIMIENTO POSTQUIRURGICO PACIENTES CIRUGIA</TITLE>
</HEAD>
<BODY>

  <!-- Estas 5 lineas es para que funcione el Calendar al capturar fechas --
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>  -->
    
<?php
include_once("conex.php");
include_once("root/comun.php");

$institucion = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
$wactualiz = "2021-08-13";
encabezado( "SEGUIMIENTO POSTQUIRURGICO PACIENTES CIRUGIA", $wactualiz, $institucion->baseDeDatos );
session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");
        
 $key = substr($user,2,strlen($user));
	

	

 echo "<form name='repcirseg' action='repcirseg.php?wemp_pmla=".$wemp_pmla."' method=post>";
 
 if (!isset($wfec1) or $wfec1=='')
 {

	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";

	
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
	$whce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
	$wcliame = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
	$wtcx = consultarAliasPorAplicacion($conex, $wemp_pmla, "tcx");

	echo "<center><table border=1>";
    //echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>SEGUIMIENTO PACIENTES DE CIRUGIA</font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>Periodo: ".$wfec1." Al ".$wfec2."</font></b><br>";
    //echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=2><i>PROGRAMA: repcirseg.php Ver. 2018/05/16<br>AUTOR: Angela Ocampo V.</font></b><br>";
    echo "</table>";



	echo "<br>";


	 $query= "Select pactdo,pacdoc,turfna,pacno1,pacno2,pacap1,pacap2,Turtel,turhis,turnin,
                     Tureps,Empnom,turtur,Turtcx,Turmed,turfec,
                     turcir,egrcae,codigo,nombre,movcon,movdat
             from ".$wtcx."_000011 left join ".$wcliame."_000024 on (tureps=Empcod)
                             left join ".$wcliame."_000108 on (turhis=Egrhis and turnin=egring)
                             left join ".$whce."_000107 on (turhis=movhis and turnin=moving and movcon=298)
                             left join ".$wcliame."_000110 on (turhis=prohis and turnin=proing)
                             left join root_000012 on (Procod=codigo), ".$wcliame."_000100
            where turhis=pachis
                 and turfec between '".$wfec1."' and '".$wfec2."'
	               group by turtur";
                 //group by turhis, turnin";

	 $err1 = mysql_query($query,$conex);
     $num1 = mysql_num_rows($err1);


		echo "<table border=1 cellspacing=0 cellpadding=0 align=center size='300'>";
		echo "<tr>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>TDOCUMENTO</b></td>";
	    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>DOCUMENTO</b></td>";
	    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>FECHA NACIMIENTO</b></td>";
	    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>NOMBRE1</b></td>";
	    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>NOMBRE2</b></td>";
	    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>APELLIDO1</b></td>";
	    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>APELLIDO2</b></td>";
	    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>TELEFONO</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2>&nbsp<b>HISTORIA</b>&nbsp</td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2>&nbsp&nbsp<b>ING</b>&nbsp&nbsp</td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>CODIGO EPS</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>NOMBRE</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>TURNO</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>TIPO CX</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>MEDICO</b></td>";
	    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>FECHA CX</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>CIRUGIA</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>TIPO EGRESO</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>CODIGO CUPS</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>NOMBRE</b></td>";
		echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>ALTO COSTO</b></td>";
	    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>OBSERVACION</b></td>";


	for ($i=1;$i<=$num1;$i++)
	{
	 if (is_int ($i/2))
	  {
	    $wcf="F8FBFC";  // color de fondo
	  }
	 else
	  {
	    $wcf="DFF8FF"; // color de fondo
	  }

		$row1 = mysql_fetch_array($err1);
	   
   	  echo "<Tr bgcolor=".$wcf.">";
      echo "<td align=center><font size=1>".$row1[0]."</font></td>";
	  echo "<td align=center><font size=1>".$row1[1]."</font></td>";
      echo "<td align=center><font size=1>".$row1[2]."</font></td>";
      echo "<td align=center><font size=1>".$row1[3]."</font></td>";
      echo "<td align=center><font size=1>".$row1[4]."</font></td>";
      echo "<td align=center><font size=1>".$row1[5]."</font></td>";
	  echo "<td align=center><font size=1>".$row1[6]."</font></td>"; 
	  echo "<td align=center><font size=1>".$row1[7]."</font></td>";
	  echo "<td align=center><font size=1>".$row1[8]."</font></td>";
      echo "<td align=center><font size=1>".$row1[9]."</font></td>";
	  echo "<td align=center><font size=1>".$row1[10]."</font></td>";
	  echo "<td align=center><font size=1>".$row1[11]."</font></td>";
	  echo "<td align=center><font size=1>".$row1[12]."</font></td>";
	  echo "<td align=center><font size=1>".$row1[13]."</font></td>";
	  echo "<td align=center><font size=1>".$row1[14]."</font></td>";
	  echo "<td align=center><font size=1>".$row1[15]."</font></td>";
	  echo "<td align=center><font size=1>".$row1[16]."</font></td>";
	  echo "<td align=center><font size=1>".$row1[17]."</font></td>";
	  echo "<td align=center><font size=1>".$row1[18]."</font></td>";
	  echo "<td align=center><font size=1>".$row1[19]."</font></td>";
	  echo "<td align=center><font size=1>".$row1[20]."</font></td>";
	  echo "<td align=center><font size=1>".$row1[21]."</font></td>";


      echo "</tr>";  
	}
	echo "</table>"; 

 }   

echo "</BODY>";
echo "</HTML>";	

?>