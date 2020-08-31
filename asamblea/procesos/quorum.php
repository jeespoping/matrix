<?php
include_once("conex.php");
include_once("root/comun.php");
?>
<head>
  <title>QUORUM ASAMBLEAS HOLDING PROMOTORA MEDICA LAS AMERICAS</title>
</head>
<STYLE type='text/css'>
     H1.miclase {border-width: 1px; border: solid; text-align: center}
	 
	 BODY            
{
font-family: verdana;
font-size: 10pt;
height: 1024px;
width: 100%;
}
</STYLE>
<BODY TEXT="#000000" onload="startTime()">
<script type="text/javascript">
	function enter()
	{
	   document.forms.quorum.submit();
	}
	
	function startTime() {
		var today = new Date();
		var h = today.getHours();
		var m = today.getMinutes();
		var s = today.getSeconds();
		m = checkTime(m);
		s = checkTime(s);
		document.getElementById('hora').innerHTML =
		h + ":" + m + ":" + s;
		var t = setTimeout(startTime, 500);
	}
	
	function checkTime(i) {
		if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
		return i;
	}
</script>
<?php


//echo "<meta http-equiv='refresh' content='10;url=agenda.php?'>";

   /**************************************************
	*	             QUORUM ASAMBLEAS                *
	*				CONEX, FREE =>  OK				 *
	**************************************************/
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	

	

	
	$wactualiz = "2018-08-16";
	
  	$key = substr($user,2,strlen($user));
		
	if (strpos($user,"-") > 0)
       $wusuario = substr($user,(strpos($user,"-")+1),strlen($user)); 
         
    
    //====================================================================================================================================
    //COMIENZA LA FORMA      
    echo "<form name=quorum action='quorum.php' method=post>";
    
    $wano=date("Y");
    $wfecha=date("Y-m-d"); 
    $hora = (string)date("H:i:s");
    
    // echo "<HR align=center></hr>";
    
    //===================================================================================================================================================
    // QUERY PRINCIPAL 
    //===================================================================================================================================================
    // ACA TRAIGO LA EMPRESA ACTIVA O QUE ESTA EN ASAMBLEA
    //===================================================================================================================================================
    $q = "  SELECT peremp, perano, permes, empdes, emptcu, Empnas "
        ."    FROM asamblea_000003, asamblea_000004 "
	    ."   WHERE peract = 'on' "
	    ."     AND peremp = empcod "
	    ."     AND empact = 'on' ";
	$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
    
	if ($num > 0)
	   {
	    $rowemp = mysql_fetch_array($res); 
	    $wemp=$rowemp[0];
	    $wano=$rowemp[1];
	    $wmes=$rowemp[2];
	    $wnem=$rowemp[3];
	    $wtcu=$rowemp[4];    //Aca traigo el tiempo para refrescar la pantalla del quorum
	    $wnombreAsamblea=$rowemp['Empnas'];
       
	   
	   $wcliame = consultarAliasPorAplicacion($conex, $wemp, "facturacion");
	   
	
		$logo = "";
		if ( $wemp == "01")
			$logo = "logo_promotora";
		else
			$logo = "logo_promotora";
	   
		encabezado("QUORUM ". $wnombreAsamblea,$wactualiz,$logo);
	    //===================================================================================================================================================    
	    // ACA TRAIGO TOTAL GENERAL DE ACCIONES DE LA EMPRESA
	    //===================================================================================================================================================
	    $q = "  SELECT count(*), sum(accvap) "
	        ."    FROM asamblea_000001 "
		    ."   WHERE accact = 'on' "
		    ."     AND accemp = '".$wemp."'";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
	    
		if ($num > 0)
		   {
			$rowtotacc = mysql_fetch_array($res); 
			
			echo "<br>";
			
		    echo "<center><table>";
		    //echo "<tr><td align=center colspan=13><img src='/matrix/images/medical/root/clinica.jpg' WIDTH=240 HEIGHT=140></td></tr>";
		    // echo "<tr><td align=center bgcolor=CCCCCC><font size=6 text color=#993399><b>QUORUM ASAMBLEA ".$wnem." - ".$wano."</b></font></td></tr>";
		    echo "<tr><td align=center class='encabezadoTabla'><font size=6 text><b>".$wnem." - ".$wano."</b></font></td></tr>";
		    echo "</table>";
		    
		    
		    echo "<br>";
		    echo "<br>";
		    
		    $wfec=explode("-",$wfecha); //Aca separo la fecha para tomar el mes y el d�a
	        switch ($wfec[1])
	           {
		        case "01":
		           { $wmes="Enero "; }
		           break;
		        case "02":
		           { $wmes="Febrero "; }
		           break; 
		        case "03":
		           { $wmes="Marzo "; }
		           break;
		        case "04":
		           { $wmes="Abril "; }
		           break;
		        case "05":
		           { $wmes="Mayo "; }
		           break; 
		        case "06":
		           { $wmes="Junio "; }
		           break;
		        case "07":
		           { $wmes="Julio "; }
		           break;
		        case "08":
		           { $wmes="Agosto "; }
		           break; 
		        case "09":
		           { $wmes="Septiembre"; }
		           break;
		        case "10":
		           { $wmes="Octubre "; }
		           break;
		        case "11":
		           { $wmes="Noviembre "; }
		           break; 
		        case "12":
		           { $wmes="Diciembre "; }
		           break;                        
	           }       
		    
		    		    
		    echo "<center><table border='0' class='encabezadoTabla'>";
		    echo "<tr>";
		    echo "<td align=center colspan=2><font size=5.5 text><b>".$wmes.$wfec[2]." de ".$wfec[0]."</b></font></td>";
		    echo "<td>&nbsp</td>";
		    echo "<td>&nbsp</td>";
		    // echo "<td align=center colspan=2><font size=5.5 text><b> Hora: ".$hora."</b></font></td>";
		    echo "<td align=center colspan=2><font size=5.5 text><b> Hora: <span id='hora'></span></b></font></td>";
		    echo "</tr>";
		    echo "</table>";
		    
		    echo "<br>";
		    echo "<br>";
		    
		    
		    echo "<center><table>";
		    
		    echo "<th class='encabezadoTabla' align=center>&nbsp</th>";
		    echo "<th class='encabezadoTabla' align=center><font size=5>Cantidad</font></th>";
		    echo "<th class='encabezadoTabla' align=center><font size=5>% Participaci&oacute;n<br>o Acciones</font></th>";
		    echo "<th class='encabezadoTabla' align=center><font size=5>%</font></th>";
		    
		    echo "<tr class='fila1'>";
		    echo "<td align=left><font size=5><b>Total socios: </b></font></td>";
		    echo "<td align=right><font size=5><b>".$rowtotacc[0]."</b></font></td>";
		    echo "<td align=right><font size=5><b>".$rowtotacc[1]."</b></font></td>";
		    echo "<td align=right><font size=5><b>".number_format((($rowtotacc[1]/$rowtotacc[1])*100),4,'.',',')."</b></font></td>";
		    echo "</tr>";
		    
		    $wfec=explode("-",$wfecha);
		    
		    //Aca traigo los presentes
		    $q = " SELECT count(*), sum(accvap) "
		        ."   FROM asamblea_000001, asamblea_000005 "
		        ."  WHERE movemp = '".$wemp."'"
		        ."    AND movano = '".$wfec[0]."'"
		        ."    AND movmes = '".$wfec[1]."'"
		        ."    AND movcac = acccod "
		        ."    AND Accemp = '".$wemp."'"
		        ."    AND movcpa = 'PR' "
		        ."    AND movdel = 'NO' "
		        ."    AND movval = 'S' ";
		    $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
		    
			if ($num > 0)
			   {
				$rowtotquorum  = mysql_fetch_array($res);    
				$wtotcanasiste = $rowtotquorum[0];
				$wtotporasiste = $rowtotquorum[1];
			    			
				//Aca traigo los delegados    
			    $q = " SELECT count(*), sum(accvap) "
			        ."   FROM asamblea_000001, asamblea_000005 "
			        ."  WHERE movemp  = '".$wemp."'"
			        ."    AND movano  = '".$wfec[0]."'"
			        ."    AND movmes  = '".$wfec[1]."'"
			        ."    AND movcac  = acccod "
			        ."    AND Accemp = '".$wemp."'"
			        ."    AND movcpa  = 'PR' "
			        ."    AND movdel != 'NO' "
			        ."    AND movval = 'S' ";
		        $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			    $num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error())    
			    if ($num > 0)
			       {
				    $rowtotquorum  = mysql_fetch_array($res);
				    $wtotcandelegados=$rowtotquorum[0];   
				    $wtotpordelegados=$rowtotquorum[1];
			       }
			      else
			         {
			          $wtotcandelegados=0; 
			          $wtotpordelegados=0;
		             }       
			        
			    echo "<tr class='fila2'>";
			    echo "<td align=left><font size=5><b>Socios Asistentes: </b></font></td>";
			    echo "<td align=right><font size=5><b>".$wtotcanasiste."</b></font></td>";
			    echo "<td align=right><font size=5><b>".$wtotporasiste."</b></font></td>";
			    echo "<td align=right><font size=5><b>".number_format((($wtotporasiste/$rowtotacc[1])*100),4,'.',',')."</b></font></td>";
			    echo "</tr>";
			    
			    echo "<tr class='fila1'>";
			    echo "<td align=left><font size=5><b>Socios que delegaron: </b></font></td>";
			    echo "<td align=right><font size=5><b>".$wtotcandelegados."</b></font></td>";
			    echo "<td align=right><font size=5><b>".$wtotpordelegados."</b></font></td>";
			    echo "<td align=right><font size=5><b>".number_format((($wtotpordelegados/$rowtotacc[1])*100),4,'.',',')."</b></font></td>";
			    echo "</tr>";
			    
			    echo "<tr class='encabezadoTabla'>";
			    echo "<td align=left><font size=5><b>Quorum: </b></font></td>";
			    echo "<td align=right><font size=5><b>".($wtotcanasiste+$wtotcandelegados)."</b></font></td>";
			    echo "<td align=right><font size=5><b>".($wtotporasiste+$wtotpordelegados)."</b></font></td>";
			    echo "<td align=right><font size=5><b>".number_format(((($wtotporasiste+$wtotpordelegados)/$rowtotacc[1])*100),4,'.',',')."</b></font></td>";
			    echo "</tr>";
			   } 
          } //Fin del then si hay accionistas con porcentaje de participacion
      } //Fin del then si hay empresa abierta o asamblea abierta
    
   echo "</form>";
   
   echo "<meta http-equiv='refresh' content='".$wtcu.";url=quorum.php?'>";
   
}
include_once("free.php");
?>
