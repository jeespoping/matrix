<html>
<head>
<title>Generar Archivo Plano Para GRD</title>
</head>

<script>

    function ira()
    {
	 document.gengrd01.$wfec1.focus();
	}
</script>

<BODY  onload=ira() BGCOLOR="" TEXT="#000066">

  <!-- Loading Calendar JavaScript files -->  <!-- Loading Theme file(s) OJO: El calendario NO funciona en programas que se ubiquen en la raiz www -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>

<script type="text/javascript">

	function enter()
	{
		document.forms.gengrd01.submit();   // Ojo para la funcion gengrd01 <> Gengrd01  (sencible a mayusculas)
	}
</script>

<?php
include_once("conex.php");
/*
   Este programa permite Generar Archivo Plano definido para GRD y como trae informacion de Unix en nulo
   no lo puedo publicar en Matrix Asi que INVECLA lo ejecuta desde mi equipo directamente El link para su
   ejecucion es:
    http://132.1.20.13/matrix/soporte/reportes/gengrd01.php
	
   Por lo que hay que mantener actualizado la tabla hce_000238 generando un plano desde matrix con:
    http://mx.lasamericas.com.co/matrix/socios/procesos/generasql.php
   Luego borramos la tabla en mi Marix con TRUNCATE TABLE 
   y subiendolo a mi matrix_local con:  http://localhost/archivos/cargaG.php	Seleccionamos la tabla hce_000238, Separdor de campos el | y copiamos 
   el archivo plano01.txt en la C:\ de mi equipo  y <Send File>
	
	*************************************************************************************************************************************************************
	MODIFICACION: En Enero 6 de 2016 para no tener que estar copiando la tabla  hce_000238 o sea realizar los pasos anteriores
	              Abro la conexion directamente desde matrix asi:
				  $conex = mysql_connect('192.168.120.2','root','q6@nt6m') or die("No se realizo Conexion");
				  y listo!!! 
	*************************************************************************************************************************************************************
	
   OTRA FORMA DE ENTRAR A LAS TABLAS DE MATRIX PRODUCCION: instalo en mi equipo el odbc mysql bajado de la pagina https://dev.mysql.com/downloads/connector/odbc/  
   instalando el archivo mysql-connector-odbc-5.3.4-win32.msi y luego por Panel de Control -> Herramientas Administrativas
   --> Origenes de Datos ODBC  --> Agregue el que aparece como MySQL ODBC 5.3 ANSI Driver y lo configure asi:
   
   Data Source Name : AMatrix
   Descripcion      : ODBC Para entrar a Matrix por ODBC
   TCP/IP Server    : 192.168.120.2
   Port             : 3306
   User             : Root
   Password         : q6@nt6m
   Database         : Matrix
  
   y asi ya le pego a las tablas de Matrix en produccion por este ODBC  
   
   //Conexion a MySQL Matrix por ODBC creada en el "DSN de Sistema"
   $conex = odbc_connect('AMatrix','','') or die("No se realizo Conexion con la BD de Matrix Produccion");
	
   ***************************************************************************************************************************************************************	
  
*/	
   
/*
session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");
*/

function CalculaEdad( $fecha )
{
    list($Y,$m,$d) = explode("-",$fecha);
    return( date("md") < $m.$d ? date("Y")-$Y-1 : date("Y")-$Y );
}

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

function BNombre($s,$conexW)
{
	$resultadoW = mysql_query($s,$conexW); 
    $nroreg = mysql_num_rows($resultadoW);
	if ($nroreg > 0)
	{
	 $registroW = mysql_fetch_row($resultadoW);
	 return $registroW[0];
	}
    else
     return "";
}

   // Conexion a Matrix 
    

    mysql_select_db("matrix") or die("No se selecciono la base de datos");    

   // Conexion a Matrix Local
   // $conex = mysql_connect('localhost','root','q6@nt6m') or die("No se realizo Conexion");
   // mysql_select_db("matrix") or die("No se selecciono la base de datos");    
 
   // Conexion a Matrix Produccion
   // $conex = mysql_connect('192.168.120.2','root','q6@nt6m') or die("No se realizo Conexion");  
   // mysql_select_db("matrix") or die("No se selecciono la base de datos");  
   
   //Conexion a Informix por ODBC Creada en el "DSN del Sistema"
     $conexN = odbc_connect('facturacion','','') or die("No se realizo Conexion con la BD de facturacion en Informix");

/*  
  // Conexion a Base de datos Access por ODBC Creada en el "DSN del Sistema" Por el panel de control de windows
  // $conexA = odbc_connect('cominf','','') or die("No se realizo Conexion con la BD Access ");

   // Conexion a Base de datos Access por string de conexcion
 

   // Se especifica la ubicación de la base de datos Access (directorio actual)
    $db = getcwd() . "\\" . 'CAPTACION1_bs.mdb';
  // Se define la cadena de conexión
    $dsn = "DRIVER={Microsoft Access Driver (*.mdb)};DBQ=$db";
  // Se realiza la conexón con los datos especificados anteriormente
    $conexA = odbc_connect( $dsn, '', '' );
    if (!$conexA) 
	 exit( "Error al conectar: " . $conn);
    
   // Se define la consulta que va a ejecutarse
    $sql = "SELECT * FROM CAPTACION";
   // Se ejecuta la consulta y se guardan los resultados en el recordset rs
    $rs = odbc_exec( $conexA, $sql );
    if ( !$rs ) 
	  exit( "Error en la consulta SQL" );
    
    // Se muestran los resultados
    while ( odbc_fetch_row($rs) ) 
    { $resultado=odbc_result($rs,"Nombre"); 
      echo $resultado;
      echo "<br>";
    }

	// Se cierra la conexión
    odbc_close( $conn );

*/
	 
echo "<form name='gengrd01' action='gengrd01.php' method=post>";

			echo "<center><table border=0>";
			echo "<tr><td align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center>GENERACION DE ARCHIVO PLANO PARA GRD<b></td></tr>";
			echo "<tr>";

   echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>";

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
	echo "<input type='TEXT' name='wfec2' size=10 maxlength=10  id='wfec2' value=".$wfec2." class=tipo3><button id='trigger2' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec2',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
	   //]]></script>
	<?php

     echo "<tr><td bgcolor=#cccccc align=center><input type=checkbox name=conf>Confirmar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
     echo "<input type='submit' value='Generar'></td></tr></table>";

 if ( $conf == "on" )      // Ya hay datos seleccionados
 {
	   // Inicialmente generamos todo en un archivo plano temporal
   	   $ruta=  "../archivos";
   	   //  ./ para que lo genere en un subdirectorio apartir de la ruta donde estan los fuentes
   	   $archivo = fopen("res2175tmp.txt","w");

   	   $wtotal=0;

//  ***************************************************************************************************
   
   /*   
      $query = "Select * from CAPTACION where OT=2310937";	
      $resultado = odbc_do($conexA,$query);               // Ejecuto el query
	  if (odbc_fetch_row($resultado)) 
		 echo "RESULTADO: ".odbc_result($resultado,1);
	  
   */
   
	  //    '           1     2      3     4      5       6      7      8      9     10      11 
      $query="Select pactid,pacced,egrhis,egrnum,pacnac,pacsex,pacmun,egring,egrsin,egregr,egrcau"
      ." From inmegr,inpaci"
      ." Where egregr Between '".$wfec1."' And '".$wfec2."'"
      ." And egrhis=pachis"
	  ." Order by 3,4";
	  
          $resultado = odbc_do($conexN,$query);               // Ejecuto el query

          // Genero los datos
          while (odbc_fetch_row($resultado))
	      {
		   
           // De Hipocrates Tomo maximo 4 posibles responsables 		   
		   $q="Select pacmreemp,pacmrecer From inpacmre where pacmrehis=".TRIM(odbc_result($resultado,3))." And pacmrenum=".TRIM(odbc_result($resultado,4));
echo $q."<br>";
           $result2 = odbc_do($conexN,$q);                   // Ejecuto el query
            
			$wresponsables="";
			$i=1;
            while ( $i<=4 )
	        {
				if ( odbc_fetch_row($result2) )
			    {
                 // Busco el plan de beneficios o tipo de empresa
    			 $q="Select emptip From inemp Where empcod='".odbc_result($result2,2)."'";
echo $q."<br>";				 
				 $result3 = odbc_do($conexN,$q);              // Ejecuto el query
				 if (odbc_fetch_row($result3))
				   $wplan=odbc_result($result3,1);
                 else
				   $wplan="";
			   
			     if (odbc_result($result2,1) == "E" )         // Si el responsable es empresa
				   $wres=odbc_result($result2,2);
                 else
                   $wres="999999";                            // Si el responsable es particular
			   
				 $wresponsables=$wresponsables."|".$wres."|".$wplan;
				} 
				else
				 $wresponsables=$wresponsables."||";       // Ojo dos campos entonces dos pipe
                
				$i++;			 
			}
			
	        // De Matrix traigo hasta 10 medicos que hayan atendido el paciente (Segun el order by el medico con especialidad ppal queda de 1ro)
		    $q="SELECT esptip,espmed FROM cliame_000111 WHERE esphis=".TRIM(odbc_result($resultado,3))." And esping=".TRIM(odbc_result($resultado,4))." order by esptip ";
	
	        $result3 = mysql_query($q);          // Ejecuto el query   
	        $nroreg = mysql_num_rows($result3);
			
			$wmedicos="";
			$i=1;
            while ( $i<=10 )
	        {
			  $registro = mysql_fetch_row($result3);  	
			  
              if ( $i <= $nroreg )
			    $wmedicos=$wmedicos."|".$registro[1];
		      else
			    $wmedicos=$wmedicos."|";
			
			 $i++;		
	        }
			
			
			
			// De Matrix traigo hasta 30 DIAGNOSTICOS (Segun el order by el Dx Ppal queda de 1ro)
		    $q="SELECT diatip,diacod FROM cliame_000109 WHERE diahis=".TRIM(odbc_result($resultado,3))." And diaing =".TRIM(odbc_result($resultado,4))." order by diatip ";
	
	        $result3 = mysql_query($q);          // Ejecuto el query   
	        $nroreg = mysql_num_rows($result3);
			
			$wdx="";
			$i=1;
            while ( $i<=30 )
	        {
			  $registro = mysql_fetch_row($result3);  	
			  
              if ( $i <= $nroreg )
			    $wdx=$wdx."|".$registro[1];
		      else
			    $wdx=$wdx."|";
			
			 $i++;		
	        }
			
			// De Matrix traigo los datos de hasta 45 PROCEDIMIENTOS (Segun el order by el Procedimiento Ppal queda de 1ro)
		    $q="SELECT procod,profec,promed,proser FROM cliame_000110 WHERE prohis=".TRIM(odbc_result($resultado,3))." And proing =".TRIM(odbc_result($resultado,4))." order by protip ";
	
	        $result3 = mysql_query($q);          // Ejecuto el query   
	        $nroreg = mysql_num_rows($result3);
			
			$wcx="";
			$i=1;
            while ( $i<=45 )
	        {
			  $registro = mysql_fetch_row($result3);  	
			  
              if ( $i <= $nroreg )
			  {	  
			    $wcx=$wcx."|".$registro[0]."|".$registro[1]."|".$registro[2]."|".$registro[3];
	  		    // De matrix traigo el quirofano para esa historia-ingreso el dia de la cirugia ( hay un problem y es si tuvo varios procedimientos el mismo dia, una reintervencion por ejemplo )
     		    $q="SELECT turqui FROM tcx_000011 WHERE turhis=".TRIM(odbc_result($resultado,3))." And turnin=".TRIM(odbc_result($resultado,4))." And turfec='".$registro[1]."'";
	    	    $wquirofano=BNombre($q,$conex);
                $wcx=$wcx."|".$wquirofano;
			  }	
		      else
			    $wcx=$wcx."|||||";
			
			 $i++;		
	        }
			
			// De Hipocrates traigo los datos de facturacion
            $q="Select movdetcon,conagr,SUM(movdetval) From famov,famovdet,facon"
              ." Where movhis=".TRIM(odbc_result($resultado,3))." And movnum=".TRIM(odbc_result($resultado,4))
			  ." And movfue='20' And movanu=0 "
              ." And movfue=movdetfue And movdoc=movdetdoc"
              ." And movdetcon=concod "
              ." Group by movdetcon,conagr ";
echo $q."<br>";			  
			  $result2 = odbc_do($conexN,$q);               // Ejecuto el query
			  
			  $sumtot=0;
              $summed=0;
			  $summmq=0;
			  $sumpro=0;
			  $sumhos=0;
			  
			  $i=1;
              while ( $i<=4 )
	          {
				if ( odbc_fetch_row($result2) )
				{	
				  $sumtot=$sumtot + odbc_result($result2,3);          //Suma Total
				  
				  if ((odbc_result($result2,1)=="0616") or (odbc_result($result2,1)=="0169"))
					$summed = $summed + odbc_result($result2,3);      //Total Medicamentos
				  else
 				   if ((odbc_result($result2,1)=="0626") or (odbc_result($result2,1)=="0168"))
					 $summmq = $summmq + odbc_result($result2,3);     //Total materiales
				   else	  
				     if ((odbc_result($result2,2)=="04") or (odbc_result($result2,2)=="07"))
					   $sumpro = $sumpro + odbc_result($result2,3);   //Total Procedimientos				  
				     else
				      if (odbc_result($result2,2)=="06")
					    $sumhos = $sumhos + odbc_result($result2,3);  // Total Hospitalizacion o estancia
	
				}    
				$i++;			 
			 }


					   
		   //  $wnacviv = ereg_replace("[^0-9]", "", $wnacviv);     //  *** FUNCION QUE DEVUELVE DE UN STRING SOLO LOS NUMEROS  *** / 

		   // OJO En conclusion al sumar una variable string NO VA  pipe ."|".    Ejemplo:  odbc_result($resultado,7).$wresponsables   O    odbc_result($resultado,11).$wmedicos.$wdx
		   
		   $wtotal++;
		   $LineaDatos = odbc_result($resultado,1)."|".odbc_result($resultado,2)."|".odbc_result($resultado,3)."|".odbc_result($resultado,4)."|".odbc_result($resultado,5)."|".odbc_result($resultado,6)."|"
		                .substr(odbc_result($resultado,7),0,2)."|".substr(odbc_result($resultado,7),2,3)."|".odbc_result($resultado,7).$wresponsables."|".odbc_result($resultado,8)."|"
						.odbc_result($resultado,9)."|".odbc_result($resultado,10)."|".odbc_result($resultado,11).$wmedicos.$wdx.$wcx."|".$sumtot."|".$sumpro."|".$summed."|".$summmq."|".$sumhos;

          // echo $LineaDatos."<br>";

          fwrite($archivo, $LineaDatos.chr(13).chr(10) );
         }


	    odbc_close($conexN);

//  **************************************************************************************************************************************************

	   	//Si en el href lo hago a la variable $ruta me mostrara todos los
	   	//archivos generados alli, pero si $ruta tiene el path completo
	   	//con el archivo generado lo bajaria directamente y no mostraria
	   	//otros archivos
	   	//echo "<li><A href='REC165ATGE".$fcorte."NI000800067065.txt'>Presione Clic Derecho Para Bajar El Archivo ...</A>";

		echo "<li><A href='".$archivo."'>Presione Clic Derecho Para Bajar El Archivo ...</A>";
       	echo "<br>";
       	echo "<li>Registros generados: ".$wtotal;

 }
echo "</form>";
 odbc_close_all();
?>
</body>
</html>