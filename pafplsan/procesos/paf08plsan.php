<HTML>
<HEAD>
<TITLE>Consolidado general del programa Plus Sanitas en un periodo</TITLE>
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
//Conexion a Informix Creada en el "DSN de Sistema"
$conexN = odbc_connect('Facturacion','','') or die("No se realizo Conexion con la BD facturacion en Informix");  

//Forma
echo "<form name='paf08plsan' action='paf08plsan.php' method=post>";  
 
 if (!isset($wfec1) or !isset($wfec2))
 {

	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";
    echo "<tr>";
	echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Consolidado general del programa PLUS SANITAS en un periodo<br></font></b>";   
	echo "</tr>";

	
  if (!isset($wfec1))   // Si no esta seteada entonces la inicializo en el primer dia del mes actual con formato aaaa-mm-dd
  {
    $hoy = date("Y-mm-dd");
    $wfec1=substr($hoy,0,4)."-".substr($hoy,5,2)."-01";
  } 
    echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Fecha Inicial<br></font></b>";   
   	$cal="calendario('wfec1','1')";
	echo "<input type='TEXT' name='wfec1' size=10 maxlength=10  id='wfec1'  value=".$wfec1." class=tipo3><button id='trigger1' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec1',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php

  if (!isset($wfec2))   // Si no esta seteada entonces la inicializo en el ultimo dia del mes actual con formato aaaa-mm-dd
  {
    $hoy = date("Y-mm-dd");
    $wfec2=substr($hoy,0,4)."-".substr($hoy,5,2)."-".UltimoDia( substr($hoy,0,4),(substr($hoy,5,2) ) );
  }  
    echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Fecha Final  <br></font></b>";   
   	$cal="calendario('wfec2','1')";
	echo "<input type='TEXT' name='wfec2' size=10 maxlength=10  id='wfec2' readonly='readonly' value=".$wfec2." class=tipo3><button id='trigger2' onclick=".$cal.">...</button></td>";
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
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>Estadisticas generales del programa Plus Sanitas</font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>Periodo: ".$wfec1." Al ".$wfec2."</font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=2><i>PROGRAMA: paf08plsan.php Ver. 2020/02/25<br>AUTOR: JairS</font></b><br>";
    echo "</table>";

    echo "<br>";
    echo "<table border=0>";
    echo "<tr>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Descripcion<b></td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Cantidad<b></td>";
    echo "</tr>"; 

    $query="SELECT count(*) as Ordenes From pafplsan_000001 "
    ."  WHERE paffec BETWEEN '".$wfec1."' AND '".$wfec2."' AND pafest='A'"; 
    $resultado = mysql_query($query);
    $nroreg = mysql_num_rows($resultado);
    if ($nroreg > 0)      //  Encontro 
    {
     $registro = mysql_fetch_row($resultado);  	   
     echo "<td colspan=2 align=center bgcolor='CCFFFF'><font text color=#003366 size=3>TOTAL ORDENES ACTIVAS GENERADAS</td>";
	 echo "<td colspan=2 align=LEFT   bgcolor='CCFFFF'><font text color=#003366 size=3>".$registro[0]."</td>";
	 echo "</tr>";    
    }

    //  NRO DE ORDENES SIN REPETIR PACIENTES   OJO: Es el nro de registros que genere el query 
    $query="Select  pafced,count(*)"
    ." from pafplsan_000001"
    ." where pafest='A' "
    ." and paffec BETWEEN '".$wfec1."' AND '".$wfec2."' AND pafest='A'" 
    ." group by pafced"
    ." order by 2 desc";
    $resultado = mysql_query($query);
    $nroreg = mysql_num_rows($resultado);
      echo "<td colspan=2 align=center bgcolor='CCFFFF'><font text color=#003366 size=3>NRO DE ORDENES SIN REPETIR PACIENTES</td>";
	 echo "<td colspan=2 align=LEFT   bgcolor='CCFFFF'><font text color=#003366 size=3>".$nroreg."</td>";
	 echo "</tr>";    
    
    
    $query=" Select cconom,Count(*) as IngAmb"
    ." From aymov,cocco"
    ." Where movtar='PK' "
    ." And movfec BETWEEN '".$wfec1."' AND '".$wfec2."'" 
    ." And movtip = 'E' "     // Pacientes Externos o Ambulatorios en las unidades de Ayudas Dx
    ." And movanu = '0' "
    ." And movcco=ccocod"
    ." GROUP BY 1"
    ." ORDER BY 2 DESC ";

 
    $resultado = odbc_do($conexN,$query);            // Ejecuto el query  
    echo "<td colspan=4 align=center bgcolor='DDDDDD'><font text color=#003366 size=3>Ingresos Ambulatorios por </td>";  
    echo "</tr>"; 
    
    $wsuma=0;
    while (odbc_fetch_row($resultado))
    {
        echo "<td colspan=2 align=center bgcolor='CCFFFF'><font text color=#003366 size=3>".odbc_result($resultado,1)."</td>";
        echo "<td colspan=2 align=LEFT   bgcolor='CCFFFF'><font text color=#003366 size=3>".odbc_result($resultado,2)."</td>";
        echo "</tr>";
        $wsuma=$wsuma+odbc_result($resultado,2);
    }
     echo "<td colspan=2 align=center bgcolor='#CC99CC'><font text color=#003366 size=3>TOTAL: </td>";
     echo "<td colspan=2 align=LEFT   bgcolor='#CC99CC'><font text color=#003366 size=3>".$wsuma."</td>";
     echo "</tr>";


	$query=" Select sernom,Count(*) as IngHos"
    ." From inmegr,inser"
    ." Where egring BETWEEN '".$wfec1."' AND '".$wfec2."'" 
    ." And egremp = 'E' "     
    ." And egrcer = '800251440CV' "
    ." And egrtar = 'PK' "
    ." And egrsin = sercod"
//  ." And egrhos = 'H' "      // Si quisiera solo egresos hospitalarios
    ." GROUP BY 1"
    ." ORDER BY 2 DESC ";
    
    $resultado = odbc_do($conexN,$query);            // Ejecuto el query  
    echo "<td colspan=4 align=center bgcolor='DDDDDD'><font text color=#003366 size=3>Ingresos/Egresos en el periodo:</td>";  
    echo "</tr>"; 
    $wsuma=0; 
    while (odbc_fetch_row($resultado))
    {
        echo "<td colspan=2 align=center bgcolor='CCFFFF'><font text color=#003366 size=3>".odbc_result($resultado,1)."</td>";
        echo "<td colspan=2 align=LEFT   bgcolor='CCFFFF'><font text color=#003366 size=3>".odbc_result($resultado,2)."</td>";
        echo "</tr>";
        $wsuma=$wsuma+odbc_result($resultado,2); 
    }  
     echo "<td colspan=2 align=center bgcolor='#CC99CC'><font text color=#003366 size=3>TOTAL: </td>";
     echo "<td colspan=2 align=LEFT   bgcolor='#CC99CC'><font text color=#003366 size=3>".$wsuma."</td>";
     echo "</tr>";

	$query=" Select sernom,Count(*) as IngAct"
    ." From inpac,inser"
    ." Where pacemp = 'E' "     
    ." And paccer = '800251440CV' "
    ." And pactar = 'PK' "
    ." And pacser = sercod"
    ." GROUP BY 1"
    ." ORDER BY 2 DESC ";
     
   
    $resultado = odbc_do($conexN,$query);            // Ejecuto el query  
    echo "<td colspan=4 align=center bgcolor='DDDDDD'><font text color=#003366 size=3>Ubicacion de los pacientes actualmente Activos: </td>";  
    echo "</tr>"; 
    $wsuma=0;  
    while (odbc_fetch_row($resultado))
    {
        echo "<td colspan=2 align=center bgcolor='CCFFFF'><font text color=#003366 size=3>".odbc_result($resultado,1)."</td>";
        echo "<td colspan=2 align=LEFT   bgcolor='CCFFFF'><font text color=#003366 size=3>".odbc_result($resultado,2)."</td>";
        echo "</tr>"; 
        $wsuma=$wsuma+odbc_result($resultado,2); 
    } 
     echo "<td colspan=2 align=center bgcolor='#CC99CC'><font text color=#003366 size=3>TOTAL: </td>";
     echo "<td colspan=2 align=LEFT   bgcolor='#CC99CC'><font text color=#003366 size=3>".$wsuma."</td>";
     echo "</tr>";


	$query=" Select cconom,Count(*) as IngAmb"
    ." From aymov,cocco"
    ." Where movtar='PK' "
    ." And movfec BETWEEN '".$wfec1."' AND '".$wfec2."'" 
    ." And movtip = 'I' "     // Pacientes Internos con examenes grabados desde Ayudas Dx
    ." And movanu = '0' "
    ." And movcco=ccocod"
    ." GROUP BY 1"
    ." ORDER BY 2 DESC ";

    $resultado = odbc_do($conexN,$query);            // Ejecuto el query  
    echo "<td colspan=4 align=center bgcolor='DDDDDD'><font text color=#003366 size=3>Procedimientos realizados a Pacientes Internos desde:</td>";  
    echo "</tr>"; 
    $wsuma=0;     
    while (odbc_fetch_row($resultado))
    {
        echo "<td colspan=2 align=center bgcolor='CCFFFF'><font text color=#003366 size=3>".odbc_result($resultado,1)."</td>";
        echo "<td colspan=2 align=LEFT   bgcolor='CCFFFF'><font text color=#003366 size=3>".odbc_result($resultado,2)."</td>";
        echo "</tr>"; 
        $wsuma=$wsuma+odbc_result($resultado,2);  
    }
     echo "<td colspan=2 align=center bgcolor='#0099CC'><font text color=#003366 size=3>TOTAL: </td>";
     echo "<td colspan=2 align=LEFT   bgcolor='#0099CC'><font text color=#003366 size=3>".$wsuma."</td>";
     echo "</tr>";

     echo "<td colspan=4 align=center bgcolor='DDDDDD'><font text color=#003366 size=3>Ingresos por urgencias en el periodo:</td>";  
     echo "</tr>"; 
     $query="Select pachis,pacnum,pachor,pacser,'A' tipo"
     ." From inpac"
     ." Where pacfec  between '".$wfec1."' and '".$wfec2."'"
     ." And pactar='PK' "
     ." UNION"
     ." Select egrhis,egrnum,egrhoi,egrsin,'E' tipo"
     ." From inmegr"
     ." Where egring  between '".$wfec1."' and '".$wfec2."'"
     ." And egrtar='PK' ";     

     $resultado = odbc_do($conexN,$query);            // Ejecuto el query 
	 $total=0;
	 $totalact=0;
     while (odbc_fetch_row($resultado))              // Lee registros
	 { 
       if (odbc_result($resultado,4) <> "04")       // No tiene ahora a Urgencias como servicio pero como este lo modifican busco si ingreso por Urgencias
       {   
	      $query="Select logusu,logfec,EXTEND(logfec,hour to hour) From iflog"
	      ." Where logva1='".odbc_result($resultado,1)."' And logva2='".odbc_result($resultado,2)."'"
	      ." And logtab='inpac' And logope='GRABAR' ";
	      $resultado2 = odbc_do($conexN,$query) or die("No se encontro registro en archivo de LOG");           
	      $registro2 = odbc_fetch_row($resultado2);       
	      $wusuario=substr(odbc_result($resultado2,1), 0, 3);  
	      if ($wusuario=="urg")                    // Si Lo grabo un usuario de urgencias acumulo
	      { $total++; 
	        $whora=odbc_result($resultado2,3);
	        if (odbc_result($resultado,5) == "A") 
             $totalact++;
          }
       }
       else     // Tiene a Urgencias como servicio de ingreso   
       {
         $total++; 
         if (odbc_result($resultado,5) == "A") 
          $totalact++;
       }      
     }     
     echo "<td colspan=2 align=center bgcolor='#0099CC'><font text color=#003366 size=3>TOTAL: </td>";
     echo "<td colspan=2 align=LEFT   bgcolor='#0099CC'><font text color=#003366 size=3>".$total."</td>";
     echo "</tr>";
     echo "<td colspan=2 align=center bgcolor='#0099CC'><font text color=#003366 size=3>ACTUALMENTE ESTAN ACTIVOS: </td>";
     echo "<td colspan=2 align=LEFT   bgcolor='#0099CC'><font text color=#003366 size=3>".$totalact."</td>";
     echo "</tr>";

     echo "<td colspan=4 align=center bgcolor='DDDDDD'><font text color=#003366 size=3>Historias con regingreso por el mismo DX en el periodo:</td>";  
     echo "</tr>"; 
     echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Historia<b></td>";
     echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Diagnostico<b></td>";
     echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>F.Ingreso<b></td>";
     echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>F.Egreso<b></td>";
     echo "</tr>"; 
     
// REINGRESOS PROGRAMA PAF POR LA MISMA PATOLOGIA PPAL
  $query="Select mdiahis HIS,mdiadia DIA,count(*) TOT"
  ." From inmegr,inmdia"
  ." Where egring between '".$wfec1."' and '".$wfec2."'"
  ." and egrhos = 'H'"
  ."  and egrtar = 'PK'"
  ."  and egrhis = mdiahis"
  ."  and egrnum = mdianum"
  ."  and mdiatip = 'P'"
  ." Group by mdiahis,mdiadia"
  ." Having count(*) > 1"
  ." Order by mdiahis,mdiadia"
  ." into temp tmp";
    $resultado = odbc_do($conexN,$query);            // Ejecuto el query    
//  Adiciono a estos la fecha de Ingreso 
  $query="Select mdiahis,dianom,egring,egregr"
  ." From inmegr,inmdia,tmp,india"
  ." Where egregr between '".$wfec1."' and '".$wfec2."'"
  ." and egrhos = 'H'"
  ." and egrtar = 'PK'"
  ." and egrhis = mdiahis"
  ." and egrnum = mdianum"
  ." and mdiatip = 'P'"
  ." and mdiahis = HIS"
  ." and mdiadia = DIA"
  ." and mdiadia = diacod"
  ." Order by 1,2,3";     
     $resultado = odbc_do($conexN,$query);            // Ejecuto el query 
     $wtot=0;
     while (odbc_fetch_row($resultado))              // Lee registros
	 { 
        echo "<td colspan=1 align=center bgcolor='CCFFFF'><font text color=#003366 size=3>".odbc_result($resultado,1)."</td>";
        echo "<td colspan=1 align=LEFT   bgcolor='CCFFFF'><font text color=#003366 size=3>".odbc_result($resultado,2)."</td>";
        echo "<td colspan=1 align=center bgcolor='CCFFFF'><font text color=#003366 size=3>".odbc_result($resultado,3)."</td>";
        echo "<td colspan=1 align=LEFT   bgcolor='CCFFFF'><font text color=#003366 size=3>".odbc_result($resultado,4)."</td>";
        echo "</tr>"; 
        $wtot++;
     }       
     echo "</tr>";
     if ( $wtot==0 )
      echo "<td colspan=4 align=center bgcolor='CCFFFF'><font text color=#003366 size=3>NO SE PRESENTARON CASOS...</td>";
     echo "</tr>";
     
 // {PACIENTES HOSPITALIZADOS EN EL PERIODO   OJO: Es el nro de registros que genere el query}
 $query="Select trahis,count(*)"
 ." from inmtra"
 ." where traing between '".$wfec1."' and '".$wfec2."'"
 ." and tratar='PK' "
 ." group by 1"
 ." order by 2 desc" ;

     $resultado = odbc_do($conexN,$query);
     $nroreg=0;            // Ejecuto el query 
     while (odbc_fetch_row($resultado))              // Cuenta los registros
	  $nroreg++;

	 echo "<td colspan=2 align=center bgcolor='#0099CC'><font text color=#003366 size=3>PACIENTES HOSPITALIZADOS EN EL PERIODO</td>";
	 echo "<td colspan=2 align=LEFT   bgcolor='#0099CC'><font text color=#003366 size=3>".$nroreg."</td>";
	 echo "</tr>";    

 // { DIAS CAMAS PROGRAMA PAF  PACIENTES CON EGRESO DE CAMA}
 $query="Select count(*)  nroreg  from inmtra"
 ." where traing Between '".$wfec1."' and '".$wfec2."'"
 ." and tratar='PK' "
 ." and traegr is NOT null";    // Este query lo hago para evitar una cancelacion por Nulo                    
 $resultado = odbc_do($conexN,$query) or die("No se encontraron registros");   
 if (odbc_result($resultado,1) > 0 )
 {
  $query="Select sum(traegr-traing)  dias"
  ." from inmtra"
  ." where traing Between '".$wfec1."' and '".$wfec2."'"
  ." and tratar='PK'"
  ." and traegr is NOT null";
   $resultado = odbc_do($conexN,$query) or die("No se encontraron registros");           
   $registro = odbc_fetch_row($resultado);       
   echo "<td colspan=2 align=center bgcolor='#0099CC'><font text color=#003366 size=3>DIAS CAMA PACIENTES PAF CON EGRESO DE CAMA</td>";
   echo "<td colspan=2 align=LEFT   bgcolor='#0099CC'><font text color=#003366 size=3>".odbc_result($resultado,1)."</td>";
   echo "</tr>";    
  }
  else
  {
   echo "<td colspan=2 align=center bgcolor='#0099CC'><font text color=#003366 size=3>DIAS CAMA PACIENTES PAF CON EGRESO DE CAMA</td>";
   echo "<td colspan=2 align=LEFT   bgcolor='#0099CC'><font text color=#003366 size=3> 0 </td>";
   echo "</tr>";    
  }  
 
 
// { + DIAS CAMAS PROGRAMA PAF PACIENTES SIN EGRESOS DE CAMA}
  $query="Select sum('".$wfec2."'-traing) dias"
  ." from inmtra"
  ." where traing Between '".$wfec1."' and '".$wfec2."'"
  ." and tratar='PK' "
  ." and traegr is null";

   $resultado = odbc_do($conexN,$query) or die("No se encontraron registros");           
   $registro = odbc_fetch_row($resultado);       
 
   echo "<td colspan=2 align=center bgcolor='#0099CC'><font text color=#003366 size=3>DIAS CAMA PACIENTES PAF SIN EGRESO DE CAMA</td>";
   echo "<td colspan=2 align=LEFT   bgcolor='#0099CC'><font text color=#003366 size=3>".$registro[0]."</td>";
   echo "</tr>"; 

  
    //odbc_close($conexN);
    echo "</table>";
 }   
mysql_close(); 
echo "</BODY>";
echo "</HTML>";	

odbc_close($conexN);
odbc_close_all();

?>
