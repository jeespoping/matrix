<?php
include_once("conex.php");
include_once("root/comun.php");

if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

$conex = obtenerConexionBD("matrix");

$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

$wbasedato = strtolower($institucion->baseDeDatos);
$wentidad = $institucion->nombre;
	
echo "<html>";
echo "<head>";
echo "<title>CONSULTA DE RECIBOS</title>";
echo "</head>";
echo "<body TEXT='#000066'>";

echo "<center>";
echo "<table border=0 align=center>";
echo "<tr><td align=center colspan=4><IMG width=210 height=100 SRC='/matrix/images/medical/pos/logo_".$wbasedato.".png'></td></tr>";
echo "<tr><td align=center bgcolor='#cccccc' colspan=4 ><A NAME='Arriba'><font size=5><b>CONSULTA DE RECIBOS</b></font></a></td></tr>";
echo "<tr align='center'><td><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td></tr>";
echo "</table>";
echo "</center>";

echo "<form action='r004_con_recibos.php' method=post>";

$wfec=date("Y-m-d");
$whor=(string)date("H:i:s");

if(!isset($wbasedato)){
		$wbasedato='clisur';	
}

$wcolor1="006699";

	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";

			echo "<table align=center border=0 width=50%>";
			echo "<tr>";
			echo "<th bgcolor=".$wcolor1."><font size=3 text color= #FFFFFF>Fuente</font></th>";
			echo "<th bgcolor=".$wcolor1."><font size=3 text color= #FFFFFF>Numero</font></th>";
		    echo "<th bgcolor=".$wcolor1."><font size=3 text color= #FFFFFF>Fecha</font></th>";
		    echo "<th bgcolor=".$wcolor1."><font size=3 text color= #FFFFFF>Nit</font></th>";
			echo "<th bgcolor=".$wcolor1."><font size=3 text color= #FFFFFF>Empresa</font></th>";
			echo "<th bgcolor=".$wcolor1."><font size=3 text color= #FFFFFF>Id_Pac</font></th>";
			echo "<th bgcolor=".$wcolor1."><font size=3 text color= #FFFFFF>Nom_Paciente</font></th>";
			echo "<th bgcolor=".$wcolor1."><font size=3 text color= #FFFFFF>Valor</font></th>";
			echo "<th bgcolor=".$wcolor1."><font size=3 text color= #FFFFFF>Estado</font></th>";
			echo "</tr>";


		 	$query = "create temporary table if not exists tmprec as ".
		 	         "  select a.renfue,a.rennum,a.fecha_data,a.rencco,a.rencod codigo,a.rennom,a.renvca,a.renest, ".
		 	         "         b.rdeffa,b.rdefac,b.rdehis ".
		 	         "    from clisur_000020 a,clisur_000021 b ".
		 	         "   where a.fecha_data between '2006-12-01' and '2007-01-12' ".
		 	         "     and a.renfue = '30' ".
		 	         "     and b.rdefue = a.renfue and b.rdenum = a.rennum ".
		 	         "     and b.rdecco = a.rencco ".
		 	         "   union all ".
		 	         "  select a.renfue,a.rennum,a.fecha_data,a.rencco,a.rencod codigo,a.rennom,a.renvca,a.renest,".
		 	         "         b.rdeffa,b.rdefac,b.rdehis ".
		 	         "    from clisur_000020 a,clisur_000021 b ".
		 	         "    where a.fecha_data between '2006-12-01' and '2006-12-31' ".
		 	         "      and a.renfue in ('31','32') ".
		 	         "      and b.rdefue = a.renfue and b.rdenum = a.rennum ".
		 	         "      and b.rdecco = a.rencco";

		 	$res = mysql_query($query,$conex) or die (mysql_errno()." - ".mysql_error());

		 	$query = "select distinct renfue,rennum,fecha_data,rdeffa,rdefac,renvca,renest ".
		 	         "  from tmprec ";

		 	$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    		$num = mysql_num_rows($err);

			if($num > 0)
			{
				for ($i=1;$i<=$num;$i++)
	        	{

					$wnit="";
					$wnom="";
					$wpdoc="";
					$wpnom="";

	        		$row = mysql_fetch_array($err);

	        		if ($i%2==0)
	            		$wcolor="#cccccc";
	            	else
		            	$wcolor="#999999";

					$query= "SELECT fennit,empnom,fendpa,fennpa  ".
		            		"  FROM clisur_000018,clisur_000024 WHERE fenffa ='".$row[3].
		            		    "' and fenfac='".$row[4]."' and empnit = fennit ";

   					$err1= mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    				$num1= mysql_num_rows($err1);

    				if($num1 > 0)
					{
						$row1 = mysql_fetch_array($err1);
						$wnit = $row1[0];
						$wnom = $row1[1];
						$wpdoc = $row1[2];
						$wpnom = $row1[3];
					}

		 		    echo "<tr>";
				    echo "<td align=left  bgcolor='".$wcolor."'><font size=2>".$row[0]."</font></td>";
				    echo "<td align=left  bgcolor='".$wcolor."'><font size=2>".$row[1]."</font></td>";
				    echo "<td align=left  bgcolor='".$wcolor."'><font size=2>".$row[2]."</font></td>";
			    	echo "<td align=left  bgcolor='".$wcolor."'><font size=2>".$wnit."</font></td>";
			     	echo "<td align=left  bgcolor='".$wcolor."'><font size=2>".$wnom."</font></td>";
			     	echo "<td align=left  bgcolor='".$wcolor."'><font size=2>".$wpdoc."</font></td>";
			     	echo "<td align=left  bgcolor='".$wcolor."'><font size=2>".$wpnom."</font></td>";
			     	echo "<td align=left  bgcolor='".$wcolor."'><font size=2>".$row[5]."</font></td>";
			     	echo "<td align=left  bgcolor='".$wcolor."'><font size=2>".$row[6]."</font></td>";
			     	echo "</tr>";
				}
				echo $i;
	        }
	        echo "<tr><td><div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div></td></tr>";
			echo "</table>";

	echo "</body>";
	echo "</html>";
	liberarConexionBD($conex);
?>
</body>
</html>
