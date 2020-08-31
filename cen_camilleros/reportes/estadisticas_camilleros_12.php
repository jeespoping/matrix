<head>
  <title>ESTADISTICAS DE CAMILLEROS</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000000">
<?php
include_once("conex.php");

//echo "<meta http-equiv='refresh' content='10;url=central_de_camilleros.php?'>";

  /***************************************************
	*	        ESTADISTICAS DE CAMILLEROS           *
	*				CONEX, FREE => OK				 *
	**************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	

	

	

						or die("No se ralizo Conexion");
    

    
	//$conexunix = odbc_pconnect('facturacion','facadm','1201')
  	//				    or die("No se ralizo Conexion con el Unix");
  					    
  					    
  	$key = substr($user,2,strlen($user));
		
	if (strpos($user,"-") > 0)
          $wusuario = substr($user,(strpos($user,"-")+1),strlen($user)); 
    
          
    //====================================================================================================================================
    //COMIENZA LA FORMA      
    echo "<form action='estadisticas_camilleros_12.php' method=post>";
    
    $wfecha=date("Y-m-d"); 
    $hora = (string)date("H:i:s");
    
    echo "<center><table border=2>";
    echo "<tr><td align=center bgcolor=#fffffff colspan=2><font size=5 text color=#CC0000><b>CLINICA LAS AMERICAS</b></font></td></tr>";
    echo "<tr><td align=center bgcolor=#fffffff colspan=2><font size=4 text color=#CC0000><b>ESTADISTICAS SERVICIO DE CAMILLEROS</b></font></td></tr>";
    
    $wcolor="dddddd";
    $wcolorfor="666666";
    
    if (!isset($wfecha_i) or !isset($wfecha_f))
       {
	    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //FECHA INICIAL Y FINAL
        echo "<tr>";
        echo "<td align=left bgcolor=".$wcolor."><b>Fecha Inicial: </b><INPUT TYPE='text' NAME='wfecha_i' VALUE=".$wfecha."></td>";  
        echo "<td align=left bgcolor=".$wcolor."><b>Fecha Final: </b><INPUT TYPE='text' NAME='wfecha_f' VALUE=".$wfecha."></td>";  
	    echo "</tr>";
	    
	    echo "<tr>";
        echo "<td align=center bgcolor=#cccccc colspan=2><input type='submit' value='Consultar'></td>";   
        echo "</tr>";
       }
      else
         {
	        echo "<tr>";
	        echo "<td align=center bgcolor=fffffff colspan=2><b>Período de Consulta: </b>".$wfecha_i."<b> al </b>".$wfecha_f."</td>";  
	        echo "</tr>";
	        
	        echo "<tr>";
	        echo "<td align=center bgcolor=fffffff colspan=2>&nbsp</td>";  
	        echo "</tr>";
	        	    
			   
			echo "<tr></tr>";   
		    //===================================================================================================================================================
		    // QUERY: CANTIDAD DE SERVICIOS ** POR MOTIVO DESDE LAS DOCE EN ADELANTE** EN EL PERIODO
		    //===================================================================================================================================================
		    $q=   "  SELECT Motivo, COUNT(*) "
		         ."    FROM cencam_000003 "
		         ."   WHERE Anulada = 'No' " 
		         ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		         ."     AND Hora_respuesta != '00:00:00' "
		         ."     AND Hora_data >= '12:00:00' "
		         ."   GROUP BY 1 "
		         ."   ORDER BY 2 desc ";
			$res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
		    
		    if ($num > 0)
			   {
				echo "<tr bgcolor=ffcc66>";
				echo "<td colspan=2><font size=4>Solicitudes por Motivo desde las doce (12) en adelante: </font></td>";
				echo "</tr>";
				echo "<tr></tr>";
				$wtotal=0;
				for ($i=1;$i<=$num;$i++)
				   {
					$row = mysql_fetch_array($res); 
					echo "<tr bgcolor=".$wcolor.">";
					echo "<td colspan=1>".$row[0]."</td>";
				    echo "<td colspan=1>".$row[1]."</td>";
				    $wtotal=$wtotal+$row[1];   
				   }
				echo "<tr bgcolor=ffcc66>";
			    echo "<td><font size=4>Total servicios: </font></td>";
				echo "<td colspan=1>".$wtotal."</td>"; 
				echo "</tr>";   
			   } 
		 }   	      	     
	   
   echo "</center></table>";
   
   echo "</form>";
   
}
include_once("free.php");
?>
