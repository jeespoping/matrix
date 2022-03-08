<html>
<head>
  <title>Utilidad para Apuntar tablas en contabilidad</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066"> 
<?php
$wemp_pmla = $_REQUEST['wemp_pmla'];
include_once("conex.php"); 	
include_once("root/comun.php");

/***************************************************************
/* Programa que permite apuntar tablas en UNIX para generar los
/* comprobantes a contabilidad NIIF desde cualquiera de las 
/* empresas del holding y segun las aplicaciones que usen

/* Este script se ejecuta con parametro ejemplo asi:
/* http://localhost/matrix/socios/procesos/coniifxx.php?wemp=1
/*
/* donde wemp=1 es usuario de PMLA
         wemp=2 es usuario de LABORATORIO
		 wemp=3 es usuario de CLINICA DEL SUR
		 wemp=4 es usuario de PATOLOGIA
		 wemp=5 es usuario de FARMASTORE
----------------------------------------------------------------
  ACTUALIZACIÓN
----------------------------------------------------------------
->08/03/2022-Brigith Lagares : Se estandariza wemp_pmla  

****************************************************************/
$wactualiz = '2022-02-24';
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wbasedato1 = strtolower( $institucion->baseDeDatos );
encabezado("PROMOTORA MEDICA LAS AMERICAS S.A. ",$wactualiz, $wbasedato1);

session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");


 //Defino y lleno dos matrices
 $aniff = array();

 //PMLA 
 $aniff[1][1]="/u8/datos/contab/concian/basdat.dbs/ameco00230";    // amecocue ctas actuales x terceo
 $aniff[1][2]="/u8/datos/contab/concian/basdat.dbs/comov00152";    // comov
 $aniff[1][3]="/u8/datos/contab/concian/basdat.dbs/comov00153";    // comovenc
 //LABORATORIO
 $aniff[2][1]="/u3/datos/contab/conlabn/basdat.dbs/cocue00196";    // cocue
 $aniff[2][2]="/u3/datos/contab/conlabn/basdat.dbs/comov00152";    // comov
 $aniff[2][3]="/u3/datos/contab/conlabn/basdat.dbs/comov00153";    // comovenc
 //CLINICA DEL SUR
 $aniff[3][1]="/u8/datos/contab/consurn/basdat.dbs/cocue00196";    // cocue
 $aniff[3][2]="/u8/datos/contab/consurn/basdat.dbs/comov00152";    // comov
 $aniff[3][3]="/u8/datos/contab/consurn/basdat.dbs/comov00153";    // comovenc
 //PATOLOGIA
 $aniff[4][1]="/u8/datos/contab/conpatn/basdat.dbs/cocue00233";    // cocue
 $aniff[4][2]="/u8/datos/contab/conpatn/basdat.dbs/comov00152";    // comov
 $aniff[4][3]="/u8/datos/contab/conpatn/basdat.dbs/comov00153";    // comovenc
 //FARMASTORE
 $aniff[5][1]="/u8/datos/contab/conston/basdat.dbs/cocue00196";    // cocue
 $aniff[5][2]="/u8/datos/contab/conston/basdat.dbs/comov00152";    // comov
 $aniff[5][3]="/u8/datos/contab/conston/basdat.dbs/comov00153";    // comovenc
 
 $apuch = array();

 //PMLA
 $apuch[1][1]="/u8/datos/contab/conciap/basdat.dbs/cocue00374";    // cocue
 $apuch[1][2]="/u8/datos/contab/conciap/basdat.dbs/comov00295";    // comov
 $apuch[1][3]="/u8/datos/contab/conciap/basdat.dbs/comov00296";    // comovenc
 //LABORATORIO
 $apuch[2][1]="/u3/datos/contab/conlab6/basdat.dbs/cocue00309";    // cocue
 $apuch[2][2]="/u3/datos/contab/conlab6/basdat.dbs/comov00311";    // comov
 $apuch[2][3]="/u3/datos/contab/conlab6/basdat.dbs/comov00256";    // comovenc
 //CLINICA DEL SUR
 $apuch[3][1]="/u3/datos/contab/concsur/basdat.dbs/cocue00208";    // cocue
 $apuch[3][2]="/u3/datos/contab/concsur/basdat.dbs/comov00160";    // comov
 $apuch[3][3]="/u3/datos/contab/concsur/basdat.dbs/comov00161";    // comovenc
 //PATOLOGIA
 $apuch[4][1]="/u3/datos/contab/concia7/basdat.dbs/cocue00196";    // cocue
 $apuch[4][2]="/u3/datos/contab/concia7/basdat.dbs/comov00150";    // comov
 $apuch[4][3]="/u3/datos/contab/concia7/basdat.dbs/comov00151";    // comovenc
 //FARMASTORE
 $apuch[5][1]="/u3/datos/contab/constor/basdat.dbs/cocue00211";    // cocue
 $apuch[5][2]="/u3/datos/contab/constor/basdat.dbs/comov00161";    // comov
 $apuch[5][3]="/u3/datos/contab/constor/basdat.dbs/comov00162";    // comovenc
 
   

   mysql_select_db("matrix") or die("No se selecciono la base de datos");    
 
   echo "<form action='coniifxx.php?wemp_pmla=".$wemp_pmla."' method=post>";

	if (!isset($wrec) )
	{
			echo "<center><table border=0>";
			//echo "<tr><td align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center>Apuntar tablas a:</td></tr>";
			echo "<tr>";
			
		    // para que se sostenga la seleccion hecha despues de un submit entonces:
            // (Si esta seteada y con valor dos) or si no esta seteada
	        if ((isset($wmon) and $wmon == "1") or !isset($wmon))
	        {
   		     echo "<tr><td><INPUT TYPE = 'Radio' NAME = 'wmon' VALUE = '1' CHECKED>Contabilidad NIIF<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
		     echo "<INPUT TYPE = 'Radio' NAME = 'wmon' VALUE = '2' >Contabilidad Actual<b>&nbsp;&nbsp;&nbsp;</b></INPUT></td></tr>";       		 
	        } 
	        else
	        {
    	        if ((isset($wmon) and $wmon == "2") or !isset($wmon))
                {
         	     echo "<tr><td><INPUT TYPE = 'Radio' NAME = 'wmon' VALUE = '1' >Contabilidad NIIF<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
		         echo "<INPUT TYPE = 'Radio' NAME = 'wmon' VALUE = '2' CHECKED>Contabilidad Actual<b>&nbsp;&nbsp;&nbsp;</b></INPUT></td></tr>";       
                }
		    }
		    
			//Llenamos una lista de seleccion con nombre de la conexion ODBC definida en matrix y la descripcion de las aplicaciones de servinte
			//segun la empresa que ejecute el programa
		    echo "<tr>";
            echo "<tr><td align=center colspan=4 bgcolor=#C0C0C0 ><b><font text color=#003366 size=2>Aplicativo a procesar:</font></b><br>"; 
            echo "<select name='wrec'>"; 
            echo "<option></option>"; 
			
			switch($wemp_pmla) 
		    {
            case 1:
			  echo "<option>facturacion-Facturacion y cartera PMLA</option>"; 
              echo "<option>activos-Activos Fijos PMLA</option>";
              echo "<option>nomina-Nomina PMLA</option>";
              echo "<option>cuepag-Cuentas por pagar PMLA</option>";
              echo "<option>cajban-Caja y Bancos PMLA</option>";
              echo "<option>inventarios-Inventarios y Suministros PMLA</option>";
              break;
            case 2:
              echo "<option>actlab-Activos Fijos LABORATORIO</option>";
              echo "<option>nomlab-Nomina LABORATORIO</option>";
              break;
            case 3:
              echo "<option>cuesur-Activos CLINICA SUR</option>";
              echo "<option>cybsur-Caja y Bancos CLINICA SUR</option>";
              echo "<option>nomsur-Nomina CLINICA SUR</option>";
              break;
            case 4:
              echo "<option>actpat-Activos Fijos PATOLOGIA</option>";
              echo "<option>nompat-Nomina PATOLOGIA</option>";
              break;
            case 5:
			  echo "<option>nomsto-nomsto FARMASTORE</option>";
              echo "<option>cuestor-Cuentas por pagar FARMASTORE</option>";
              echo "<option>cybstor-Caja y Bancos FARMASTORE</option>";
              break;

            }
			echo "</select></td>";
			
            echo "<INPUT TYPE = 'hidden' NAME='wemp_pmla' VALUE='".$wemp_pmla."'></INPUT>"; 
			
            echo "<tr><td bgcolor=#cccccc align=center><input type='submit' value='PROCESAR'></td></tr></table>";	  
    }
    else
    {
  		 	   
  		//Del nombre seleccionado la primera parte es el nombre de la conexion ODBC a la base de datos en INFORMIX
  		$c1=explode('-',$wrec);  
        $conexN = odbc_connect($c1[0],'informix','sco') or die("No se realizo Conexion con la BD ".$c1[1]." en Informix");  
         
  		if ($wmon == "1" )
        {
		 //  ***********************************************************************************************************
	     // Apunto a Contabilidad NIIF para poder generar comprobantes desde las aplicaciones a esta nueva contabilidad.
		 //  ***********************************************************************************************************
		 
	       echo "Apuntando ".$c1[1]." a contabilidad NIIF. ODBC de conexion utilizado ".$c1[0];   

           // cocue en NIFF que esta abierta x tercero
           $query="UPDATE systables SET dirpath='".$aniff[$wemp_pmla][1]."' WHERE tabname='cocue' ";
           $resultado = odbc_do($conexN,$query);            // Ejecuto el query  
           if ( !($resultado) )
            echo "<br><tr><td bgcolor=#33FFFF colspan=6 align=center>Error, La tabla cocue no se proceso</td></tr>";
            // comov 
            $query="UPDATE systables SET dirpath='".$aniff[$wemp_pmla][2]."' WHERE tabname='comov' ";
            $resultado = odbc_do($conexN,$query);            // Ejecuto el query  
            if ( !($resultado) )
             echo "<br><tr><td bgcolor=#33FFFF colspan=6 align=center>Error, La tabla comov no se proceso</td></tr>";
             // comovenc
             $query="UPDATE systables SET dirpath='".$aniff[$wemp_pmla][3]."' WHERE tabname='comovenc' ";
             $resultado = odbc_do($conexN,$query);            // Ejecuto el query  
             if ( !($resultado) )
              echo "<br><tr><td bgcolor=#33FFFF colspan=6 align=center>Error, La tabla comovenc no se proceso</td></tr>";
                 
              if ( ( $c1[0]=="nomina" ) or  ( $c1[0]=="nomlab" ) or ( $c1[0]=="nomsur" ) or ( $c1[0]=="nompat" ) or ( $c1[0]=="nomstor" ) ) // Es la aplicacion de nomina
              {
               // -- Modifico relacion Ctos-Ctas
               $query="UPDATE noccc SET cccini='2',cccnit='6604',cccinp='2',cccnpr='6604',cccipp='1' WHERE ccccon IN ('0022','0023','0028','0029','0090')";
               $resultado = odbc_do($conexN,$query); 
               if ( !($resultado) )
                echo "<br><tr><td bgcolor=#33FFFF colspan=6 align=center>Error, La tabla noccc no se actualizo</td></tr>";       
          
                $query="UPDATE noccc SET cccini='2',cccnit='6604',cccinp='2',cccnpr='6604' WHERE ccccon IN ('0091','0092','0093','0094')";
                $resultado = odbc_do($conexN,$query); 
                if ( !($resultado) )
                 echo "<br><tr><td bgcolor=#33FFFF colspan=6 align=center>Error, La tabla noccc no se actualizo</td></tr>";
              }
			  echo "<br>OK";
		}  
        else
        { 
          //  *******************************************************************
          //  Apunto a  Contabilidad ACTUAL Para dejarla como estaba            *
          //  *******************************************************************
          
            echo "Apuntando ".$c1[1]." a contabilidad ACTUAL. ODBC de conexion utilizado ".$c1[0];         
		  
            // cocue  
            $query = "UPDATE systables SET dirpath='".$apuch[$wemp_pmla][1]."' WHERE tabname='cocue' ";
            $resultado = odbc_do($conexN,$query); 
            if ( !($resultado) )
             echo "<br><tr><td bgcolor=#33FFFF colspan=6 align=center>Error, La tabla cocue no se proceso</td></tr>";
            // comov 
            $query = "UPDATE systables SET dirpath='".$apuch[$wemp_pmla][2]."' WHERE tabname='comov' ";
            $resultado = odbc_do($conexN,$query); 
            if ( !($resultado) )
             echo "<br><tr><td bgcolor=#33FFFF colspan=6 align=center>Error, La tabla comov no se proceso</td></tr>";
            // comovenc 
            $query = "UPDATE systables SET dirpath='".$apuch[$wemp_pmla][3]."' WHERE tabname='comovenc' ";
            $resultado = odbc_do($conexN,$query); 
            if ( !($resultado) )
             echo "<br><tr><td bgcolor=#33FFFF colspan=6 align=center>Error, La tabla comovenc no se proceso</td></tr>";
                 
            if ( ( $c1[0]=="nomina" ) or  ( $c1[0]=="nomlab" ) or ( $c1[0]=="nomsur" ) or ( $c1[0]=="nompat" )  or ( $c1[0]=="nomstor" ) )  // Es la aplicacion de nomina
            {
	          $query="UPDATE noccc SET cccini='0',cccnit='',cccinp='0',cccnpr='',cccipp='0' WHERE ccccon IN ('0022','0023','0028','0029','0090')";
              $resultado = odbc_do($conexN,$query); 
              if ( !($resultado) )
               echo "<br><tr><td bgcolor=#33FFFF colspan=6 align=center>Error, La tabla noccc no se actualizo</td></tr>";
 	        
              $query="UPDATE noccc SET cccini='0',cccnit='',cccinp='0',cccnpr='' WHERE ccccon IN ('0091','0092','0093','0094')";
               $resultado = odbc_do($conexN,$query); 
               if ( !($resultado) )
                echo "<br><tr><td bgcolor=#33FFFF colspan=6 align=center>Error, La tabla noccc no se actualizo</td></tr>";
            }
            echo "<br>OK";
         }
         
         odbc_close($conexN);   
    }
     
    echo "</form>"; 
?>
</body>
</html>