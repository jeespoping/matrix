<head>
  <title>GENERACION INGRESOS FARMACIAS</title>
</head>
<body>
<script type="text/javascript">
	function enter()
	{
	 document.forms.profacnet.submit();
	}
	
	function cerrarVentana()
	 {
      window.close()		  
     }
</script>	
<?php
include_once("conex.php");
  /***********************************************
   *       GENERACION INGRESOS FARMACIAS         *
   *     		  CONEX, FREE => OK		         *
   ***********************************************/
session_start();

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
if(!isset($_SESSION['user']))
	echo "error";
else
{	            
 	
  

  

  include_once("root/comun.php");
 
  
  if (strpos($user,"-") > 0)
     $wusuario = substr($user,(strpos($user,"-")+1),strlen($user));
     
  
 	                                            // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="2010-05-31";                      // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                            // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
                           
	               
  $wfecha=date("Y-m-d");   
  $whora = (string)date("H:i:s");	                                                           
	                                                                                                       
  $q = " SELECT empdes "
      ."   FROM root_000050 "
      ."  WHERE empcod = '".$wemp_pmla."'"
      ."    AND empest = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $row = mysql_fetch_array($res); 
  
  $wnominst=$row[0];
  
  //Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos    
  $q = " SELECT detapl, detval, empdes "
      ."   FROM root_000050, root_000051 "
      ."  WHERE empcod = '".$wemp_pmla."'"
      ."    AND empest = 'on' "
      ."    AND empcod = detemp "; 
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res); 
  
  if ($num > 0 )
     {
	  for ($i=1;$i<=$num;$i++)
	     {   
	      $row = mysql_fetch_array($res);
	      
	      if ($row[0] == "cenmez")
	         $wcenmez=$row[1];
	         
	      if ($row[0] == "afinidad")
	         $wafinidad=$row[1];
	         
	      if ($row[0] == "movhos")
	         $wbasedato=$row[1];
	         
	      if ($row[0] == "tabcco")
	         $wtabcco=$row[1];
	         
	      if ($row[0] == "camilleros")
	         $wcencam=$row[1];  
	         
	      if ($row[0] == "invecla")
	         $winvecla=$row[1];    
	         
	      $winstitucion=$row[2];   
         }  
     }
    else
       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";      
  
  encabezado("GENERACION INGRESOS FARMACIAS",$wactualiz, "clinica");  
       
   
    
  //FORMA ================================================================
  echo "<form name='profacnet' action='profacnetFS.php' method=post>";
  
  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  
  //===============================================================================================================================================
  //ACA COMIENZA EL MAIN DEL PROGRAMA   
  //===============================================================================================================================================
   if (!isset($wfec_i) or trim($wfec_i) == "" or !isset($wfec_f) or trim($wfec_f) == "" )
     {     
	  echo "<center><table cellspacing=1>";
	  echo "<tr class=seccion1>";
      echo "<td align=center><b>Fecha Inicial</b><br>";
      campoFecha("wfec_i");
      echo "</td>";
  	  echo "<td align=center><b>Fecha Final</b><br>";
      campoFecha("wfec_f");
      echo "</td>";
      echo "</tr>";
      echo "</table>";
	  
	  echo "<br>";
	  
	  echo "<center><table cellspacing=1>";	    
	  echo "<tr><td align=center bgcolor=cccccc colspan=2></b><input type='submit' value='ENTRAR'></b></td></tr></center>";
	  echo "</table>";
     }
    else 
       {
	       
	    $q = " CREATE TEMPORARY TABLE if not exists TEMPO1 as "
			." SELECT YEAR(fenfec) ano, MONTH(fenfec) mes, fencco cco, SUM(fenval-fenviv-fendes) valor "
			."   FROM farpmla_000018 "
			."  WHERE fenfec BETWEEN '".$wfec_i."' AND '".$wfec_f."'"
			."    AND fenest = 'on' "
			."  GROUP BY 1,2,3 ";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		$q = " CREATE TEMPORARY TABLE if not exists TEMPO2 as "
			." SELECT year(menfec) ano, month(menfec) mes, mencco cco, "
			."        SUM(ROUND((Mdecan*vdevun/(1+(vdepiv/100))),0)-vdedes) valor "
			."   FROM farpmla_000010, farpmla_000011, farpmla_000016, farpmla_000017 "
			."  WHERE Menfec BETWEEN '".$wfec_i."' AND '".$wfec_f."'"
			."    AND mencon = '801' "
			."    AND mendoc = mdedoc "
			."    AND mencon = mdecon "
			."    AND menfac = vennum "
			."    AND vennum = vdenum "
			."    AND mdeart = vdeart "
			."  GROUP BY 1,2 ";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		$q = " DELETE costosyp_000062 "
			."   FROM costosyp_000062, TEMPO1 "
			."  WHERE mifano = TEMPO1.ano "
			."    AND mifmes = TEMPO1.mes "
			."    AND mifcco = TEMPO1.cco "
			."    AND mifcla = 'CARGPFS' ";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


		$q = " INSERT INTO costosyp_000062 "
			." SELECT 'costosyp', CURDATE(), CURTIME(), A.ano, A.mes, '99', '4151', A.cco, '0' mifint, SUM(A.valor-IFNULL(B.valor,'0')), SUM(A.valor-IFNULL(B.valor,'0')) mifito, 'CARGPFS', 'C-costosyp', '' "
			."   FROM TEMPO1 A LEFT JOIN TEMPO2 B "
			."     ON A.ano = B.ano "
			."    AND A.mes = B.mes "
			."    AND A.cco = B.cco "
			."  GROUP BY 1,2,3,4,5,6,7,8,9,12,13,14 ";
	    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
	    
	    
	    echo "<br><br><br><br>";
	    echo "<center><table>"; 
        echo "<tr>";  
        echo "<td align=center>EL PROCESO TERMINO</td>"; 
        echo "</tr>";
        echo "</table>";	
       }    
	   
    echo "</form>";
	  
	echo "<br>";
    echo "<center><table>"; 
    echo "<tr><td align=center><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
    echo "</table>";
    
} // if de register

include_once("free.php");

?>