<HTML>
<HEAD>
<TITLE>MEDICOS QUE ATENDIERON LOS PACIENTES EN UN RANGO DE HORA</TITLE>
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
        
 

 mysql_select_db("matrix") or die("No se selecciono la base de datos");    
 
// Para Pruebas pero entran por parametro desde el programa ringxhor.php 
//   $i="9";                   // la hora inicial 
//   $wfec1="2015-03-01";      // Fecha Inicial
//   $wfec2="2015-03-01";      // Fecha Final


 //Forma
 echo "<form name='pacatendxmed' action='pacatendxmed.php' method=post>";  
 
	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";
    echo "<tr>";
	echo "<tr><td align=CENTER colspan=10 bgcolor=#DDDDDD><b><font text color=#003366 size=2>MEDICOS QUE ATENDIERON LOS PACIENTES<br></font></b>";   
	echo "</tr>";

    echo "<tr><td align=center bgcolor=#DDDDDD colspan=10><b><font text color=#003366 size=4><i>Periodo: ".$wfec1." Al ".$wfec2."</font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=10><b><font text color=#003366 size=2><i>PROGRAMA: pacatendxmed.php Ver. 2015/11/27<br>AUTOR: JairS</font></b><br>";
    echo "<br>";
    echo "<tr>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>HISTORIA-ING</td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>HORA<b></td>";
	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>ENTIDAD<b></td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>MEDICO<b></td>";
	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>ALTA DEFINITIVA<b></td>";
    echo "</tr>"; 


    $query="SELECT a.inghis, a.ingnin, a.inghin, a.ingsei, ubiald, b.ingnre, mtrmed, descripcion "
    ." FROM cliame_000101 a, movhos_000018, movhos_000016 b, hce_000022 "
    ." LEFT JOIN usuarios ON mtrmed = Codigo "
    //." WHERE ingfei BETWEEN '2015-03-01' AND '2015-03-01' "
    ." WHERE a.ingfei BETWEEN '".$wfec1."' and '".$wfec2."'"
    ." AND a.ingsei = '1130' "
    ." AND a.inghis = ubihis"
    ." AND a.ingnin = ubiing"
	." AND a.inghis = b.inghis"
    ." AND a.ingnin = b.inging"
    ." AND a.inghis = mtrhis"
    ." AND a.ingnin = mtring"
    ." AND a.inghin BETWEEN '".$i.":00:00' AND '".$i.":59:59' "
    ." ORDER BY a.inghin ";

    $resultado = mysql_query($query,$conex) or die("ERROR EN QUERY1");             // Ejecuto el query 
    $nroreg = mysql_num_rows($resultado);

	$n = 0;
    while ($n < $nroreg)       		
	 {         
	     $registro = mysql_fetch_row($resultado);	 // Lee registro	
	     $n++;
			 
	     if (is_int ($n/2))  // Cuando la variable $i es par coloca este color
	      $wcf="DDDDDD";  
	   	 else
	   	  $wcf="CCFFFF";    	
	    
	   	 echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[0]."-".$registro[1]."</td>";
		 echo "<td colspan=2 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[2]."</td>";
		 echo "<td colspan=2 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[5]."</td>";
         echo "<td colspan=2 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=3>(".$registro[6].") ".$registro[7]."</td>";
		 if ($registro[4] == 'on' )
		     echo "<td colspan=2 align=CENTER   bgcolor=".$wcf."><font text color=#003366 size=3><b>SI</b></td>";
		 else
			 echo "<td colspan=2 align=CENTER   bgcolor=".$wcf."><font text color=#003366 size=3>NO</td>";
		 echo "</tr>";  
		          
      }
     echo "</table>";  

echo "</BODY>";
echo "</HTML>";	

?>