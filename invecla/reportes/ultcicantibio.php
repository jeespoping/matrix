<HTML>
<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>
<HEAD>
<TITLE>MUESTRA EL ULTIMO CICLO DE ANTIBIOTICOS POR PACIENTES POR CENTRO DE COSTOS</TITLE>
</HEAD>
<BODY>
<?php
include_once("conex.php");
include_once("root/comun.php");

$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");
$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
session_start();

if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");
      


 
 //$conex = mysql_connect('192.168.120.2','root','q6@nt6m') or die("No se realizo Conexion");
 //ok  $conex = mysql_connect('132.1.18.12','root','q6@nt6m') or die("No se realizo Conexion");   

 mysql_select_db("matrix") or die("No se selecciono la base de datos");    
 
 // Dos variables para totales generales
 $total=0;
 $totala=0;
 //Forma
 echo "<form name='ultcicantibio' action='ultcicantibio.php' method=post>";  
 echo "<input type='hidden' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'/>";
 
 if (!isset($wcco) or $wcco=='')
 {
	//Cuerpo de la pagina
   echo "<center><h4>MUESTRA EL ULTIMO CICLO DE ANTIBIOTICOS POR PACIENTES POR CENTRO DE COSTO</h4></center>";
   echo "<table align='center' border=0>";
   echo "<tr>";
   echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>PACIENTES CON ANTIBIOTICOS<br></font></b>";   
   
   echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Centro de costos:</font></b><br>"; 
   $query = "SELECT ccocod,cconom FROM costosyp_000005 WHERE ccoclas = 'PR' AND ccoest = 'on' ORDER BY cconom";   
   echo "<select name='wcco' >"; 
   echo "<option></option>"; 
   $resultadoB = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultadoB);
   $i = 1;
   While ($i <= $nroreg)
	  {
		$registroB = mysql_fetch_row($resultadoB);  
		$c3=explode('-',$wcco); 				  
  		if($c3[0] == $registroB[0])
	      echo "<option selected>".$registroB[0]."- ".$registroB[1]."</option>";
	    else
	      echo "<option>".$registroB[0]."- ".$registroB[1]."</option>"; 
	    $i++; 
      }   
    echo "</select></td>";  
	
   echo "<tr><td align=center bgcolor=#cccccc colspan=4><input type='submit' value='Generar'></td>";          //submit osea el boton de Generar o Aceptar
   echo "</tr>";
   echo "</table>";
   
 }	
 else      // Cuando ya esta seleccionado el CCosto
 {
	echo "<center><table border=0>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>PACIENTES CON ANTIBIOTICOS</font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i> ".$wcco."</font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=2><i>PROGRAMA: ultcicantibio.php Ver. 2016/04/12<br>AUTOR: JairS</font></b><br>";
    echo "</table>";

    echo "<br>";
    echo "<table border=0>";
    echo "<tr>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>HABITACION<b></td>";
	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>HISTORIA<b></td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>INGRESO<b></td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>NOMBRE<b></td>";
    
    echo "</tr>"; 

	
    //*****************************************************************
    //     Genero tabla temporal con los antibioticos	             **
	//*****************************************************************
	//Aunque la tabla se crea como temporal y se borra automaticamente 
	//al salir del scrip, por si algo doy DROP
	$query = "DROP TABLE IF EXISTS tmpa";
	$resultado = mysql_query($query, $conex);
	
	// Creacion de tabla temporal con indice 
	// $query = "CREATE TEMPORARY TABLE IF NOT EXISTS tmpa (INDEX idx(art))

   /*
    //Creacion de tabla temporal con clave UNICA y definicion de campos segun el SELECT
	//Pero como este query genera duplicados aqui no aplica
	
	$query = "CREATE TEMPORARY TABLE IF NOT EXISTS tmpa ( PRIMARY KEY (art) ) 
				SELECT Artcod art, Artcom nom
				  FROM movhos_000026
				 WHERE artgru like 'J00%'
				   AND artest='on'
				 UNION ALL
				SELECT Pdepro art, Artcom nom
				  FROM cenpro_000003, cenpro_000002
				 WHERE Pdeins like 'MA%'
				   AND pdeest = 'on'
				   AND artcod = Pdepro
				 GROUP BY Pdepro";
    */		 
	
	// Creo una temporal con clave primaria y un index asi:
	$query = "CREATE TEMPORARY TABLE IF NOT EXISTS tmpa 
             (Id INT NOT NULL AUTO_INCREMENT, 
             art varchar(10) NOT NULL, 
             nom varchar(80) NOT NULL, 
			 PRIMARY KEY ( Id ),
             INDEX ( art ) ) 
				SELECT Artcod art, Artcom nom
				  FROM ".$wmovhos."_000026
				 WHERE artgru like 'J00%'
				   AND artest='on'
				 UNION ALL
				SELECT Pdepro art, Artcom nom
				  FROM ".$wcenmez."_000003, ".$wcenmez."_000002
				 WHERE Pdeins like 'MA%'
				   AND pdeest = 'on'
				   AND artcod = Pdepro
				 GROUP BY 1
				 ORDER BY 1";
 
	$resultado = mysql_query($query, $conex) or die(mysql_error());
	
	// Pacientes con Antibioticos en ese ccosto
	$c1=explode('-',$wcco);   
	$query="Select habcco,habcod,habhis,habing From ".$wmovhos."_000020,".$wmovhos."_000054,tmpa
          WHERE habcco='".$c1[0]."'  
          AND habdis = 'off' 
          AND habhis = kadhis 
          AND habing = kading 
          AND kadart = art 
          GROUP BY habcco,habcod,habhis,habing 
          ORDER BY habcco,habcod ";
		
   $resultado = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultado);  

	$n = 0;
    while ($n < $nroreg)       		
	{   
	     $registro = mysql_fetch_row($resultado); 
	     $n++;

	     if (is_int ($n/2))  // Cuando la variable $i es par coloca este color
	      $wcf="DDDDDD";  
	   	 else
	   	  $wcf="CCFFFF";    	
	    
	   	 echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[1]."</td>";
         echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[2]."</td>";
		 echo "<td colspan=2 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[3]."</td>";
		 
		$wnombre ="";
        $query="SELECT Oriced, Oritid  FROM root_000037"
              ." WHERE Orihis = ".$registro[2]
              ."   AND Oriing = ".$registro[3];
         $resultadoB = mysql_query($query);            // Ejecuto el query 
			$encontro = mysql_num_rows($resultadoB);
		 if ($encontro > 0)
		{ $registroB = mysql_fetch_row($resultadoB); 
          $query="SELECT Pacno1, Pacno2, Pacap1, Pacap2 FROM root_000036"
                ." WHERE pacced = '".$registroB[0]."'"
                ."   AND pactid = '".$registroB[1]."'";	
				
          $resultadoB = mysql_query($query);            // Ejecuto el query 
          $encontro = mysql_num_rows($resultadoB);
		  if ($encontro > 0)
		  {
            $registroB = mysql_fetch_row($resultadoB); 			  
			$wnombre = $registroB[0]." ".$registroB[1]." ".$registroB[2]." ".$registroB[3];
		  }
		 }
		 
		 //Si tiene antibioticos a la fecha muestro el nombre en amarillo, esto lo hace un poco mas demorado pero...
		 $wfecact = date("Y-m-d");  
		 $query="Select kadfec From ".$wmovhos."_000054, tmpa"
          ." WHERE kadhis = ".$registro[2] 
          ."   AND kading = ".$registro[3]
          ."   AND kadart = art "
		  ."   AND kadfec = '".$wfecact."'";
		  
         $resultadoB = mysql_query($query);            // Ejecuto el query 
         $encontro = mysql_num_rows($resultadoB);		  
		 if ($encontro > 0)  
		  echo "<td colspan=2 align=LEFT   bgcolor=#FFFF00><font text color=#003366 size=3>".$wnombre."</td>";
		 else
		  echo "<td colspan=2 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=3>".$wnombre."</td>";
		  
  	     echo "<td colspan=1 align=center color=#FFFFFF bgcolor=".$wcf.">";
         echo "<A HREF='detcicantibio.php?wemp_pmla=".$wemp_pmla."&whis=".$registro[2]."&wnum=".$registro[3]."&wnom=".$wnombre."' TARGET='_blank'>Detallar</A></td>";           

		 echo "</tr>";  
		          
     }
     echo "</table>";

     echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4> <i>En Amarillo con antibioticos a la Fecha.   Total Pacientes: ".$n."</font></b><br>";

 }   
echo "</BODY>";
echo "</HTML>";	

?>