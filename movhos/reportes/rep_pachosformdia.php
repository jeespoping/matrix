<html>
<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>
<head>
<title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066" onload=ira()>
<!-- Estas 5 lineas es para que funcione el Calendar al capturar fechas -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>    
    
<center>
<?php
include_once("conex.php");
/************************************************************
*            REPORTE PACIENTES HOSPITALIZADOS SIN           * 
*    FORMULARIO DILIGENCIADO DEL DIA EN HCE 000367 O 000328 *
************************************************************/
//=======================================================
//AUTOR			:Gabriel Agudelo Zapata
$wautor="Gabriel Agudelo Zapata";
//FECHA CREACION : Mayo 21 de 2018
//FECHA ULTIMA ACTUALIZACION 	:
$wactualiz="(Versión 2018-05-21)";

 session_start();
 if (!isset($_SESSION['user']))
    echo "error";
   else
	 { 
			$key = substr($user,2,strlen($user));
			
			include_once("root/comun.php");

			$whce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
			$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

			$wfecha1=date("Y-m-d");
			//$wfecha2=date("Y-m-d", strtotime("$fecha_inicio + 3 month"));
			echo "<form action='rep_pachosformdia.php' name=rep_pachosformdia method=post>";
		
			{
                $query = "   select m20.Habcod,m20.Habcco,m20.Habhis,m20.Habing,h36.Fecha_data,h36.Firpro,h36.Firusu,u.Descripcion    
               		         from ".$wmovhos."_000020 as m20 
							      left join 
							      ".$whce."_000036 as h36 on (m20.Habhis = h36.Firhis and m20.Habing = Firing 
							                       and h36.Fecha_data = '".$wfecha1."' and Firpro in ('000367','000328') ) 
								  left join 
								  usuarios as u on (h36.firusu = u.codigo)
					        where m20.Habhis != '' 
								  and m20.Habcco != '1130' 
								  Order by  h36.Fecha_data,m20.Habcco,m20.Habcod  ";	
				      
				        
		        $err = mysql_query($query,$conex);
		   		$num = mysql_num_rows($err);
		   	
				echo "<table border=1 align=center>";
				echo "<tr><td colspan=14 align=center bgcolor=#DFF8FF><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
				echo "<tr>";
				echo "<td colspan=15 align=center bgcolor=#DFF8FF><b>PACIENTES HOSPITALIZADOS CON FORMULARIO DE EVOLUCION O INTERCONSULTA DILIGENCIADO (".$wfecha1.")</b></td>";
				echo "</tr>"; 	
		
				echo "<tr>";
				echo "<td align=center bgcolor=#DFF8FF><b>HABITACION</b></td>";
				echo "<td align=center bgcolor=#DFF8FF><b>CENTRO DE COSTOS</b></td>";
				echo "<td align=center bgcolor=#DFF8FF><b>HISTORIA</b></td>";
				echo "<td align=center bgcolor=#DFF8FF><b>INGRESO</b></td>";
				echo "<td align=center bgcolor=#DFF8FF><b>FECHA</b></td>";
				echo "<td align=center bgcolor=#DFF8FF><b>FORMULARIO</b></td>";
				echo "<td align=center bgcolor=#DFF8FF><b>USUARIO</b></td>";
				echo "<td align=center bgcolor=#DFF8FF><b>NOMBRE</b></td>";
				echo "</tr>";
			
				for ($i=0;$i<$num;$i++)
				   {
					if (is_int ($i/2))
					  {
						$wcf="F8FBFC";  // color de fondo
					  }
					 else
					  {
						$wcf="DFF8FF"; // color de fondo
					  }
					$row = mysql_fetch_array($err);
					echo "<Tr bgcolor=".$wcf.">";
						echo "<td align=center>".$row[0]."</td>";
						echo "<td align=center>".$row[1]."</td>";
						echo "<td align=center>".$row[2]."</td>";
						echo "<td align=center>".$row[3]."</td>";
						echo "<td align=center>".$row[4]."</td>";
						echo "<td align=center>".$row[5]."</td>";
						echo "<td align=center>".$row[6]."</td>";
						echo "<td align=center>".$row[7]."</td>";
					echo "</tr>";
				    				    
				   }
			   
				echo "</table>"; 
		    }
	 }
	
?>
</body>
</html>
