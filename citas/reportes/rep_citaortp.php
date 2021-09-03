<HTML>
<HEAD>
<TITLE>REPORTE DE CITAS ORTOPEDIA URGENCIA</TITLE>
</HEAD>
<BODY>

<?php
include_once("conex.php");

session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");
 include_once("root/comun.php"); 
 $institucion = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
 $wactualiz = 1;
 encabezado( "AGENDA PACIENTES ORTOPEDIA URGENCIAS", $wactualiz, $institucion->baseDeDatos );

	

 
 //Forma
 echo "<form name='rep_citaortp' action='rep_citaortp.php' method=post>";  
 echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
 
 if (!isset($wfec1) or $wfec1=='')
 {

	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";
    echo "<tr>";
	//echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>AGENDA PACIENTES ORTOPEDIA URGENCIAS<br></font></b>";   
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

  if (!isset($wfec2))   // Si no esta seteada entonces la inicializo con la fecha actual
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
	
	$query = "   SELECT Cedula, Nom_pac, Descripcion, Comentario   "
               		   ."    FROM citaortp_000009,citaortp_000002  "
				       ."    WHERE Fecha between '".$wfec1."' and '".$wfec2."' "
					   ."     and  Nit_res = Nit   ";
	        
		        $err = mysql_query($query,$conex);
		   		$num = mysql_num_rows($err);

	echo "<center><table border=0>";
    //echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>AGENDA PACIENTES ORTOPEDIA URGENCIAS</font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><font text color=#003366 size=4><i>Periodo: ".$wfec1." Al ".$wfec2."</font><br>";
    echo "</table>";

    echo "<br>";
	echo "<table border=1 align=center>";
	echo "<tr>";
	echo "<td align=center bgcolor=#DBDFF8><b>CEDULA</b></td>";
	echo "<td align=center bgcolor=#DBDFF8><b>HISTORIA</b></td>";
	echo "<td align=center bgcolor=#DBDFF8><b>NOMBRE PACIENTE</b></td>";
	echo "<td align=center bgcolor=#DBDFF8><b>RESPONSABLE</b></td>";
	echo "<td align=center bgcolor=#DBDFF8><b>COMENTARIOS</b></td>";
	echo "</tr>";

	for ($i=0;$i<$num;$i++)
	   {
		if ($i==0){
				$s=$i;
			}
		$row = mysql_fetch_array($err);
		$pac1=explode("-", $row[1]);
		echo "<tr>";
			echo "<td align=center>".$row[0]."</td>";
			echo "<td align=center>".$pac1[0]."</td>";
			echo "<td align=center>".$pac1[1]."</td>";
			echo "<td align=center>".$row[2]."</td>";
			echo "<td align=left>".$row[3]."</td>";
		echo "</tr>";
		$s = $s + 1;				    
	   }
	echo "<td colspan=4 align=center bgcolor=#DBDFF8><b>TOTAL PACIENTES AGENDADOS: </b></td>";
	echo "<td colspan=1 align=center bgcolor=#DBDFF8><b>".$s."</b></td>";
	echo "</table>"; 
 }   

echo "</BODY>";
echo "</HTML>";	

?>