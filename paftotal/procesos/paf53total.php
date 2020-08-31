<HTML>
<HEAD>
<TITLE>Reporte de atenciones Programa cardiovascular SALUD TOTAL</TITLE>
</HEAD>
<BODY>

 <!-- Loading Calendar JavaScript files -->  <!-- Loading Theme file(s) -->
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

function UltimoDia($anho,$mes)
{ 
   if (((fmod($anho,4)==0) and (fmod($anho,100)!=0)) or (fmod($anho,400)==0)) { 
       $dias_febrero = 29; 
   } else { 
       $dias_febrero = 28; 
   }
    
   switch($mes) { 
       case "01": return 31; break; 
       case "02": return $dias_febrero; break; 
       case "03": return 31; break; 
       case "04": return 30; break; 
       case "05": return 31; break; 
       case "06": return 30; break; 
       case "07": return 31; break; 
       case "08": return 31; break; 
       case "09": return 30; break; 
       case "10": return 31; break; 
       case "11": return 30; break; 
       case "12": return 31; break; 
   } 
} 



mysql_select_db("matrix") or die("No se selecciono la base de datos");    

//Forma
echo "<form name='paf53total' action='paf53total.php' method=post>";  

 if ($wproceso=="Borrar")
 {   
	 $query = "DELETE FROM paftotal_000003 Where ateced = '".$wced."' And atefec = '".$wfec."' And atehor = '".$whor."'";   
     $resultado = mysql_query($query,$conex);  
	 if ($resultado)
       print "<script>alert('Registro Borrado.... ')</script>";   
 }  
 
 if (!isset($wfec1) or !isset($wfec2))
 {
	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";
    echo "<tr>";
	echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Reporte de atenciones programa cardiovascular NUEVA EPS<br></font></b>";   
	echo "</tr>";

	
  if (!isset($wfec1))   // Si no esta seteada entonces la inicializo con la fecha actual
    $wfec1 = date("Y-m-d");
   
    echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Fecha Inicial<br></font></b>";   
   	$cal="calendario('wfec1','1')";
	echo "<input type='TEXT' name='wfec1' size=10 maxlength=10  id='wfec1'  value=".$wfec1." class=tipo3><button id='trigger1' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec1',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php

  if (!isset($wfec2))   // Si no esta seteada entonces la inicializo en la misma Inicial
    $wfec2=$wfec1;
  
    echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Fecha Final  <br></font></b>";   
   	$cal="calendario('wfec2','1')";
	echo "<input type='TEXT' name='wfec2' size=10 maxlength=10  id='wfec2' readonly='readonly' value=".$wfec2." class=tipo3><button id='trigger2' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec2',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php
	
/*****/	
   echo "<tr><td align=center bgcolor=#C0C0C0 colspan=2><b><font text color=#003366 size=2>Medico que atendio:</font></b><br>";   
   $query = "SELECT codigo,Descripcion FROM citapaft_000010 Group By codigo,Descripcion Order By codigo"; 	         
   $resultadoB = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultadoB);

   echo "<select name='wmed'>"; 
   echo "<option>%%%-Todos</option>"; 
   $i = 1;
   While ($i <= $nroreg)
   {
	  $registroB = mysql_fetch_row($resultadoB);  
	  $c4=explode('-',$wmed); 				  
  	  if( trim($c4[0]) == trim($registroB[0]) )
	    echo "<option selected>".$registroB[0]."- ".$registroB[1]." ".$registroB[2]." ".$registroB[3]." ".$registroB[4]."</option>"; 
	  else
	    echo "<option>".$registroB[0]."- ".$registroB[1]." ".$registroB[2]." ".$registroB[3]." ".$registroB[4]."</option>"; 
	  $i++; 
   }   
   echo "</select></td>";   

/****/	
		
   echo "<tr><td align=center bgcolor=#cccccc colspan=4><input type='submit' value='Generar'></td>";          //submit o sea el boton de Generar o Aceptar
   echo "</tr>";
   echo "</table>";
   
 }	
 else      // Cuando ya estan todos los datos escogidos
 {
	echo "<center><table border=0>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>Reporte de atenciones programa cardiovascular Salud Total</font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>Periodo: ".$wfec1." Al ".$wfec2."</font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=2><i>PROGRAMA: paf53total.php Ver. 2016/06/22<br>AUTOR: JairS</font></b><br>";
    echo "</table>";

    echo "<br>";
    echo "<table border=0>";
    echo "<tr>";
	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Fecha<b></td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Hora<b></td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Identificacion<b></td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Paciente<b></td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Historia<b></td>";	
	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Carnet<b></td>";
	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Orden<b></td>";
	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Conducta<b></td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Cita<b></td>";
	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Control<b></td>";
	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Hora<b></td>";
	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Examen1<b></td>";
	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Examen2<b></td>";
	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Observaciones<b></td>";
	
    echo "</tr>"; 
	
    // TOMO EL CODIGO DEL MEDICO	
	if (isset($wmed))  
     $c3=explode('-',$wmed);
 
    $wtotgrl = 0;	
    $query="SELECT atemed, atefec, atehor, ateced, atenom, atehis, atecar, ateord, atecon, atecit, atefpr, atehpr, ateex1, ateex2, ateobs, id"
          ." FROM paftotal_000003"
        //  ." WHERE atefec BETWEEN '2015-10-01' AND '2015-10-30' "
		  ." WHERE atefec BETWEEN '".$wfec1."' AND '".$wfec2."'"
		  ."   AND atemed  LIKE '".$c3[0]."'"
          ." Order by atemed,atefec,atehor";

    $resultado = mysql_query($query);
    $nroreg = mysql_num_rows($resultado);
    $k = 1;
	$registro = mysql_fetch_row($resultado);  	 // Leo 1er registro
    While ($k <= $nroreg)
    {   
        $wtotmed = 0;	
        // TOMO EL NOMBRE DEL MEDICO
        $query = "SELECT Descripcion FROM citapaft_000010 WHERE codigo='".$registro[0]."' Group By Descripcion"; 
        $resultadoB=mysql_query($query); 
        if ( $registroB = mysql_fetch_row($resultadoB) )      //  Encontro 
          echo "<td align=left  colspan=28><b><font text color=#003366 size=3><i>MEDICO: (".$registro[0].") ".$registroB[0]."</font></b><tr>";
         
		 $medant=$registro[0];                 // VAriable para el 1er Rompimiento de control por medico
		 While ( ($k <= $nroreg) AND ($medant==$registro[0]) )
		 {		
            echo "<tr><td colspan=2 align=center bgcolor='CCFFFF'><font text color=#003366 size=1>".$registro[1]."</td>";
			echo "<td colspan=2 align=center bgcolor='CCFFFF'><font text color=#003366 size=1>".$registro[2]."</td>";
			echo "<td colspan=2 align=LEFT   bgcolor='CCFFFF'><font text color=#003366 size=1>".$registro[3]."</td>";
			echo "<td colspan=2 align=center bgcolor='CCFFFF'><font text color=#003366 size=1>".$registro[4]."</td>";
			echo "<td colspan=2 align=LEFT   bgcolor='CCFFFF'><font text color=#003366 size=1>".$registro[5]."</td>";
			echo "<td colspan=2 align=LEFT   bgcolor='CCFFFF'><font text color=#003366 size=1>".$registro[6]."</td>";
			echo "<td colspan=2 align=LEFT   bgcolor='CCFFFF'><font text color=#003366 size=1>".$registro[7]."</td>";
            switch (Trim($registro[8]))
			{ 
             case "1":
              $wcon = "Alta";
              break;
             case "2":
              $wcon = "Hospitalizacion";
              break;
             case "3":
              $wcon = "No Asitio";
			  break;
			 case "4":
              $wcon = "Asitio";
              break;
            }			
			 			 
			echo "<td colspan=2 align=LEFT   bgcolor='CCFFFF'><font text color=#003366 size=1>".$wcon."</td>";
            If ( Trim( $registro[9] == "1") )
			 $wcit = "Control";
            else
             $wcit = "1ra Vez";
     
         		
		
			echo "<td colspan=2 align=center bgcolor='CCFFFF'><font text color=#003366 size=1>".$wcit ."</td>";
			echo "<td colspan=2 align=LEFT   bgcolor='CCFFFF'><font text color=#003366 size=1>".$registro[10]."</td>";
			echo "<td colspan=2 align=LEFT   bgcolor='CCFFFF'><font text color=#003366 size=1>".$registro[11]."</td>";
	        
	        
	         // TOMO EL NOMBRE DEL EXAMEN 1  
			 $query = "SELECT codigo,nombre FROM root_000012 Where codigo ='".$registro[12]."'";
             $resultadoB=mysql_query($query); 
             if ( $registroB = mysql_fetch_row($resultadoB) )      //  Encontro 
               echo "<td colspan=2 align=LEFT   bgcolor='CCFFFF'><font text color=#003366 size=1>(".$registroB[0].") ".$registroB[1]."</td>";
			 else
			   echo "<td colspan=2 align=LEFT   bgcolor='CCFFFF'></td>";			 
		   
	         // TOMO EL NOMBRE DEL EXAMEN 2 
			 $query = "SELECT codigo,nombre FROM root_000012 Where codigo ='".$registro[13]."'";
             $resultadoB=mysql_query($query); 
             if ( $registroB = mysql_fetch_row($resultadoB) )      //  Encontro 
               echo "<td colspan=2 align=LEFT   bgcolor='CCFFFF'><font text color=#003366 size=1>(".$registroB[0].") ".$registroB[1]."</td>";
			 else
               echo "<td colspan=2 align=LEFT   bgcolor='CCFFFF'></td>";				 
			   
			echo "<td colspan=2 align=LEFT   bgcolor='CCFFFF'><font text color=#003366 size=1>".$registro[14]."</td>";
			
            echo "<td><A HREF='paf51total.php?wfec=".$registro[1]."&whor=".$registro[2]."&wced=".$registro[3]."&windicador=PrimeraVez&wproceso=Modificar' TARGET='_blank' >Editar</A>";	     
            echo "<td><A HREF='paf53total.php?wfec=".$registro[1]."&whor=".$registro[2]."&wced=".$registro[3]."&windicador=PrimeraVez&wproceso=Borrar' >Borrar</A></td></tr>";	     
			
			$wtotmed=$wtotmed+1;
			$registro = mysql_fetch_row($resultado);  	 // Leo Nuevo registro
			$k=$k+1;
     	}  
		$wtotgrl=$wtotgrl+$wtotmed;
        echo "<td colspan=22 align=center bgcolor='#CC99CC'><font text color=#003366 size=1>TOTAL PACIENTES ATENDIDOS: </td>";
        echo "<td colspan=6 align=LEFT   bgcolor='#CC99CC'><font text color=#003366 size=1>".$wtotmed."</td>";
	    echo "</tr>";
	} 
	echo "<td colspan=22 align=center bgcolor='#CC99CC'><font text color=#003366 size=1>TOTAL GENERAL PACIENTES ATENDIDOS: </td>";
    echo "<td colspan=6 align=LEFT   bgcolor='#CC99CC'><font text color=#003366 size=1>".$wtotgrl."</td>";
	echo "</tr>";
    echo "</table>";
 }  
echo "</Form>"; 
echo "</BODY>";
echo "</HTML>";	

?>
