<html>
<?php
include_once("conex.php");
echo "<head>";
echo "<title>INFORME DE MEDIO MAGNETIVO PARA SUSALUD</title>";
echo "</head>";
echo "<body BGCOLOR=''>";
echo "<BODY TEXT='#000066'>";

echo "<center>";
echo "<table border=0 align=center>";
echo "<tr><td align=center colspan=4><IMG width=210 height=100 SRC='/matrix/images/medical/Citas/logo_citascs.png'></td></tr>";
echo "<tr><td align=center bgcolor='#cccccc' colspan=4 ><A NAME='Arriba'><font size=5><b>Generacion Medio Magnetico Para Susalud</b></font></a></td></tr>";
echo "<tr><td align=center bgcolor='#cccccc' colspan=4 ><font size=2><b> r002_Mmag_susalud.php Ver. 1.00</b></font></td></tr>";
echo "<tr><td align=center><font size=2><b>NIT:</b></font><font size=2>".$wnit."</font></td>";
echo "<td align=center><font size=2><b>SUCURSAL: </b></font><font size=2>".$wsuc." - ".$wdsuc."</font></td>";
echo "<td align=center><font size=2><b>TIPO DOCUMENTO: NI</b></font></td>";
echo "<td align=center><font size=2><b>NIT IPS: </b></font><font size=2>".$wnitips." - ".$wdnit."</font></td></tr>";
echo "</table>";
echo "</center>";

echo "<form action='r002_mmag-susalud.php' method=post>";

$wcolor1="006699";
$wfec=date("Y-m-d");
$wconenv=$pos2;

session_start();
if(!isset($_SESSION['user']))
	echo "Error Usuario NO Registrado";
else
{		
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";


	if(!isset($wfec))
		echo "Error Usuario NO Registrado";
	else
	{		
		if (isset($wconenv))
		{
			echo "<table align=center border=0 width=50%>";
			echo "<tr>";
			echo "<th bgcolor=".$wcolor1."><font size=3 text color= #FFFFFF>Consecutivo</font></th>";
			echo "<th bgcolor=".$wcolor1."><font size=3 text color= #FFFFFF>Fuente</font></th>";
		    echo "<th bgcolor=".$wcolor1."><font size=3 text color= #FFFFFF>Factura</font></th>";
		    echo "<th bgcolor=".$wcolor1."><font size=3 text color= #FFFFFF>Fecha</font></th>";
			echo "<th bgcolor=".$wcolor1."><font size=3 text color= #FFFFFF>Registros del Detalle</font></th>";
			echo "<th bgcolor=".$wcolor1."><font size=3 text color= #FFFFFF>Estado</font></th>";
			echo "</tr>";
			
		 	$query = "SELECT magssafue, magssadoc, magssafec, magssareg, magssaedo ".
	 	         	 "  FROM ".$empresa."_000067 ".
		         	 " WHERE magssacon = ".$wconenv;
		        
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    		$num = mysql_num_rows($err);
    	
    		if($num > 0)
			{
				$wreg=0;
				for ($i=1;$i<=$num;$i++)
	        	{
	        		$row = mysql_fetch_array($err);
	        		
					if ($i%2==0)
	            	   	$wcolor="#cccccc";
	            	else
		               	$wcolor="#999999"; 
	                
				    echo "<tr>";
				    echo "<td align=center   bgcolor='".$wcolor."'><font size=2>".$wconenv.	"</font></td>";                             
				    echo "<td align=left   bgcolor='".$wcolor."'><font size=2>".$row[0]."</font></td>";                             
				    echo "<td align=left   bgcolor='".$wcolor."'><font size=2>".$row[1]."</font></td>";                             
			    	echo "<td align=left   bgcolor='".$wcolor."'><font size=2>".$row[2]."</font></td>";                             
			     	echo "<td align=center bgcolor='".$wcolor."'><font size=2>".$row[3]."</font></td>";                             
			     	echo "<td align=left   bgcolor='".$wcolor."'><font size=2>".$row[4]."</font></td>";    
			     	echo "</tr>";
			     	
			     	$wreg=$wreg+$row[3];	        	}
	        	
	        	echo "<tr><td align=left><font size=2><b>Numero de Facturas Enviadas: </b></font><font size=2>".$num."</font></td></tr>";
	        	echo "<tr><td align=left><font size=2><b>Numero de Registros Enviados: </b></font><font size=2>".$wreg."</font></td></tr>";

			}
			echo "</table>";
	  	}
	}
	echo "</body>";
	echo "</html>";
	include_once("free.php");
}
?>
</body>
</html>