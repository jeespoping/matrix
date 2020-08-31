<html>
<head>
<title>Generar Archivo Plano Resolucion 2175</title>
</head>

<script>

    function ira()
    {
	 document.genres2175v2.$wfec1.focus();
	}
</script>
<?php
	include_once("conex.php");
    
    if(!isset($_SESSION['user']))
    {
        ?>
        <div align="center">
            <label>Usuario no autenticado en el sistema.<br />Recargue la pagina principal de Matrix o inicie sesion nuevamente<?php echo 'USER='.$_SESSION['user'];?></label>
        </div>
        <?php
        return;
    }
    else
    {
        $user_session = explode('-', $_SESSION['user']);
        $wuse = $user_session[1];
		include_once("root/comun.php");		
        mysql_select_db("matrix");

        $conex = obtenerConexionBD("matrix");
		$conexN = odbc_connect('informix','','') or die("No se realizo Conexion con la BD de facturacion en Informix");
        //$conexN = odbc_connect('facturacion','','') or die("No se realizo conexión con la BD de Facturación");
    }
	session_start();
?>


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
		document.forms.genres2175v2.submit();   // Ojo para la funcion genres2175 <> Genres2175  (sencible a mayusculas)
	}
</script>

<?php
/*
   Este programa permite Generar Archivo Plano segun  Resolucion 2175 de 2015 y como trae informacion de Unix en nulo
   no lo puedo publicar en Matrix Asi que registros medicos lo ejecuta desde mi equipo directamente El link para su
   ejecucion es:
    http://132.1.20.13/matrix/soporte/reportes/genres2175.php
   Por lo que hay que mantener actualizada la tabla hce_000238 generando un plano desde matrix con:
    http://mx.lasamericas.com.co/matrix/socios/procesos/generasql.php
   y subiendolo a mi matrix_local previo borrado de la tabla con:
    http://localhost/archivos/cargaG.php	
	
	*************************************************************************************************************************************************************
	MODIFICACION: En Enero 6 de 2016 para no tener que estar copiando la tabla  hce_000238 o sea realizar los pasos anteriores
	              Abro la conexion directamente desde matrix asi:
				  
  			      $conex = mysql_connect('192.168.120.2','root','q6@nt6m') or die("No se realizo Conexion");  
                  mysql_select_db("matrix") or die("No se selecciono la base de datos");  

				  y listo!!! 
	*************************************************************************************************************************************************************
	
   OTRA FORMA DE ENTRAR A LAS TABLAS DE MATRIX PRODUCCION: instalo en mi equipo el odbc mysql bajado de la pagina https://dev.mysql.com/downloads/connector/odbc/  
   instalando el archivo mysql-connector-odbc-5.3.4-win32.msi y luego por Panel de Control -> Herramientas Administrativas
   --> Origenes de Datos ODBC  --> Agregue el que aparece como MySQL ODBC 5.3 ANSI Driver en DSN de Sistema configurandolo asi:
   
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
if(!session_is_registered("user"))
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

   //Conexion a MATRIX Local
   //  $conex = mysql_connect('localhost','root','q6@nt6m') or die("No se realizo Conexion");
   
   //Conexion directa a MATRIX Produccion
   //  $conex = mysql_connect('192.168.120.2','root','q6@nt6m') or die("No se realizo Conexion"); 
   
   //Conexion directa a MATRIXText 
   //  $conex = mysql_connect('132.1.18.13','root','q6@nt6m') or die("No se realizo Conexion");  
   
   //Conexion directa a MATRIX Desarrollo (132.1.18.12)
   //  $conex = mysql_connect('matrixdllo','root','q6@nt6m') or die("No se realizo Conexion");

	//Conexion directa a MATRIX Financiero (132.1.18.80)
     //$conex = mysql_connect('132.1.18.80','root','q6@nt6m') or die("No se realizo Conexion");  //SE COMENTA PORQUE YA NO ES LOCAL, ESTA FUNCION SE LLAMA AL INICIO (WILLIAM 07/06/2019) 
	//mysql_select_db("matrix") or die("No se selecciono la base de datos");   //SE COMENTA PORQUE YA NO ES LOCAL, ESTA FUNCION SE LLAMA AL INICIO (WILLIAM 07/06/2019)
   
   /*************************************************************************************************************************************************/
   
   //Conexion a INFORMIX por ODBC Creada en el "DSN del Sistema"
     //$conexN = odbc_connect('facturacion','','') or die("No se realizo Conexion con la BD de facturacion en Informix");
	 //$conexN = odbc_connect('informix','','') or die("No se realizo Conexion con la BD de facturacion en Informix");		SE PASA ESTA FUCNION A LA PARTE SUPERIOR (WILLIAM 07/06/2019)

   //Conexion a INFORMIX por ODBC desde un string de conexion
   //  $cadenaconex = "Driver={IBM INFORMIX 3.82 32 BIT};Database=/u5/datos/faccar/faccia1/basdat;Host=132.1.18.2;Server=americas;Service=1541;Protocol=sesoctcp;UID=informix;Password=sco;"; 
 
       //putenv("INFORMIXDIR=/usr/informix");
       //putenv("ODBCINI=/etc/odbc.ini");
			   
    //$conexN = @odbc_connect( $cadenaconex, '', ''); 
    //if (!$conexN) exit( "Error al conectar a basdat");

 

echo "<form action='genres2175v2.php' method=post>";

			echo "<center><table border=0>";
			echo "<tr><td align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center>GENERACION DE ARCHIVO RESOLUCION 2175<b></td></tr>";
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

//  *************************************** CASOS DE RECIEN NACIDOS  *****************************


// PACIENTES EN EL DIARIO DE PARTOS A REPORTAR POR RESOLUCION 2175 DE 2015

	  //    '           1     2      3     4      5       6      7       8              9
      $query="Select pactid,pacced,pacap1,pacap2,pacnom,pacnac,pacsex,diaparhis HIS,diaparnum ING,"
      //    '10         '11	          '12            '13             '14        '15    '16
      ." diapard1 FEC,diapard14 SEX,diapard12 PESO,diapard13 TALLA,diapard2 CTR,egring,egregr"       // FechaDelParto,TiempoGestacionEnSemanas,Peso,Talla,HizoControlPrenatal
      ." From indiapar,inmegr,inpaci"
      ." Where diaparhis=egrhis"
      ." And diaparnum=egrnum"
      ." AND egregr Between '".$wfec1."' And '".$wfec2."'"
      ." And diaparhis=pachis"
	  ." And diapard8 <> 'A' "      // No reporto los Abortos
      ." Order by 8,9";
	  
          $resultado = odbc_do($conexN,$query);               // Ejecuto el query

          // Genero los datos
          while (odbc_fetch_row($resultado))
	      { 
	  
		   // Fecha de Naci/to del niño
		   $fecha = substr(odbc_result($resultado,10),0,4)."-".substr(odbc_result($resultado,10),5,2)."-".substr(odbc_result($resultado,10),8,2);

		   // De Matrix traigo el Nro de registro de Nacido Vivo
		   $query="SELECT movdat FROM hce_000238 WHERE movhis=".TRIM(odbc_result($resultado,8))." And moving=".TRIM(odbc_result($resultado,9))." And movcon=48";
		   $wnacviv = BNombre($query,$conex);
		   
		   //$wnacviv = ereg_replace("[^0-9]", "", $wnacviv);     //  *** FUNCION QUE DEVUELVE DE UN STRING SOLO LOS NUMEROS  *** / 
		   $wnacviv = preg_match("[^0-9]", "", $wnacviv);     //  *** FUNCION QUE DEVUELVE DE UN STRING SOLO LOS NUMEROS  *** / 

		   // Ajusto el Sexo del recien nacido
		   if (TRIM(odbc_result($resultado,11)) == "F")
		    $wsex = "M";
		   else
			 if (TRIM(odbc_result($resultado,11)) == "M")
		      $wsex = "H";
			 else
			  $wsex = "I";

		   // De Matrix traigo si se le hizo tamizaje neonatal
		   $query="SELECT movdat FROM hce_000238 WHERE movhis=".TRIM(odbc_result($resultado,8))." And moving=".TRIM(odbc_result($resultado,9))." And movcon=43";
		   if (BNombre($query,$conex) == "001-Si")             // Es un campo Seleccion y para decir SI o NO viene asi: 001-Si
		    $wtamizaje="1";
		   else
			$wtamizaje="2";

		   $wtotal++;
		   $LineaDatos = "2"."|".$wtotal."|"."NV"."|".$wnacviv."|".$fecha."|".$wsex."|"."6"."|".TRIM(odbc_result($resultado,3))."|".TRIM(odbc_result($resultado,4))."|HIJO DE||".$wtamizaje."|";

          fwrite($archivo, $LineaDatos.chr(13).chr(10) );
         }


	    odbc_close($conexN);

//  *************************************** CASOS DE DETALLE DEL PARTO  *****************************

//  PACIENTES EN EL DIARIO DE PARTOS A REPORTAR POR RESOLUCION 2175 DE 2015

	  //    '           1     2      3     4      5       6      7       8              9
      $query="Select pactid,pacced,pacap1,pacap2,pacnom,pacnac,pacsex,diaparhis HIS,diaparnum ING,"
      //    '10         '11	          '12            '13             '14          '15            '16     '17
      ." diapard1 FEC,diapard14 SEX,diapard12 PESO,diapard13 TALLA,diapard2 CTR,diapard8 TERMINA,egring,egregr"       // FechaDelParto,TiempoGestacionEnSemanas,Peso,Talla,HizoControlPrenatal
      ." From indiapar,inmegr,inpaci"
      ." Where diaparhis=egrhis"
      ." And diaparnum=egrnum"
      //." AND egregr Between '".$wfec1."' And '".$wfec2."'"
	  ." AND egregr Between '2019-04-01' And '2019-04-30'"
      ." And diaparhis=pachis"
	  ." And diapard8 <> 'A' "      // No reporto los Abortos
      ." Order by 8,9";

          $resultado = odbc_do($conexN,$query);               // Ejecuto el query

          // Genero los datos
          while (odbc_fetch_row($resultado))
	      {

	  	   if ( strlen(odbc_result($resultado,4)) < 2 )       // Si no tiene 2do apellido   <2 por si tiene un punto o un espacio
		     $wape2="";
		   else
		     $wape2=TRIM(odbc_result($resultado,4));

		   $x1=explode(' ',odbc_result($resultado,5));         // Como los nombres de Unix estan en un solo campo lo parto
		   $wnom1=TRIM($x1[0]);

		   if ( strlen( TRIM($x1[1]) ) < 2 )                   // Si no tiene 2do Nombre   <2 por si tiene un punto o un espacio
		    $wnom2="";
		   else
		    $wnom2=TRIM($x1[1]);


		   // Fecha del parto
		   $fecha = substr(odbc_result($resultado,10),0,4)."-".substr(odbc_result($resultado,10),5,2)."-".substr(odbc_result($resultado,10),8,2);

		  	// Codigo CUPS del Procedimiento
		    if (TRIM(odbc_result($resultado,15)) == "E")
		     $wcup = "735301";
		    else
			  if (TRIM(odbc_result($resultado,15)) == "I")
		       $wcup = "721003";
			  else
			 	if (TRIM(odbc_result($resultado,15)) == "C")
		          $wcup = "740001";

		   // De Matrix traigo el Nro de registro de Nacido Vivo
		   $query="SELECT movdat FROM hce_000238 WHERE movhis=".TRIM(odbc_result($resultado,8))." And moving=".TRIM(odbc_result($resultado,9))." And movcon=48";
		   $wnacviv = BNombre($query,$conex);

		   // De Matrix traigo si se le hizo tamizaje neonatal
		   $query="SELECT movdat FROM hce_000238 WHERE movhis=".TRIM(odbc_result($resultado,8))." And moving=".TRIM(odbc_result($resultado,9))." And movcon=43";
		   if (BNombre($query,$conex) == "001-Si")             // Es un campo Seleccion y para decir SI o NO viene asi: 001-Si
		    $wtamizaje="1";
		   else
			$wtamizaje="2";

		   $wtotal++;
		   $LineaDatos = "7"."|".$wtotal."|".TRIM(odbc_result($resultado,1))."|".TRIM(odbc_result($resultado,2))."|".TRIM(odbc_result($resultado,3))."|".$wape2."|".$wnom1."|".$wnom2."|6|"
		               .$fecha."|01|".$wcup."||||2||2|||2|";

           fwrite($archivo, $LineaDatos.chr(13).chr(10) );
         }

        fclose($archivo);
	    odbc_close($conexN);



// ****************************  GENERO EL ARCHIVO DEFINITIVO CON EL REGISTRO DE ENCABEZADO  *************************

         /* Quito el separador a las fechas */
		 $wfecsin1 = substr($wfec1, 0, 4).str_pad(substr($wfec1, 5, 2), 2, '0', STR_PAD_LEFT).str_pad(substr($wfec1, 8, 2), 2, '0', STR_PAD_LEFT);

		 $wfecsin2 = substr($wfec2, 0, 4).str_pad(substr($wfec2, 5, 2), 2, '0', STR_PAD_LEFT).str_pad(substr($wfec2, 8, 2), 2, '0', STR_PAD_LEFT);

		 /* Nombre con el que generaremos el archivo a enviar  */

	     $fcorte=$wfec2;
	     $fcorte=substr($wfec2,0,4).substr($wfec2,5,2).substr($wfec2,8,2);

		 $nomarc="REC165ATGE".$fcorte."NI000800067065.txt";
	     $archivo = fopen($nomarc,"w");
         /*  Abrimos el archivo plano temporal  */
         $file = fopen("res2175tmp.txt","r");

		 $LineaDatos = "1|NI|800067065|050010212601|".$wfec1."|".$wfec2."|".$wtotal;    // Grabo el registro tipo 1 de Encabezado
         fwrite($archivo, $LineaDatos.chr(13).chr(10) );

         // Paso los registros
         While (!feof($file) )
         {
             $LineaDatos=fgets($file);
	         fwrite($archivo, $LineaDatos);
         }
       	fclose($archivo);
       	fclose($file);

	   	//Si en el href lo hago a la variable $ruta me mostrara todos los
	   	//archivos generados alli, pero si $ruta tiene el path completo
	   	//con el archivo generado lo bajaria directamente y no mostraria
	   	//otros archivos
	   	//echo "<li><A href='REC165ATGE".$fcorte."NI000800067065.txt'>Presione Clic Derecho Para Bajar El Archivo ...</A>";

		echo "<li><A href='".$nomarc."'>Presione Clic Derecho Para Bajar El Archivo ...</A>";
       	echo "<br>";
       	echo "<li>Registros generados: ".$wtotal;


 }
echo "</form>";
 odbc_close_all();
?>
</body>
</html>