<html>
<head>
<title>Control de ingreso de emplados</title>
</head>

<script>
function ira(){document.control02.cedula.focus();}
</script>

<body  onload=ira() BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
//==========================================================================================================================================
//PROGRAMA				      :Control de ingreso de empleados.                                                                 
//AUTOR				          :Jair  Saldarriaga Orozco.                                                                                   
//FECHA CREACION			  :JUNIO 26 DE 2007.                                                                                           
//FECHA ULTIMA ACTUALIZACION  :10 de Diciembre de 2007.                                                                                        
//DESCRIPCION			      :Este programa graba en una tabla mediante un lector de codigo de barras los horarios
//                             de entrada y salida de empleados

//session_start();
//if(!isset($_SESSION['user']))
// 	echo "error";
// else
//	{
//		$key = substr($user,2,strlen($user));
		

	
 		
	
	mysql_select_db("matrix") or die("No se selecciono la base de datos matrix en Mysql");    
			
		echo "<form name='control02' action='control02.php' method=post>";  //Se debe definir asi para que funcion ira()

		echo "<center><table border=1>";
		echo "<tr><td rowspan=4 align=center><IMG SRC='/matrix/images/medical/pos/logo_clisur.png' ></td>";				
		echo "<tr><td align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center><b>DIRECCION DE INFORMATICA CLINICA<b></td></tr>";
		echo "<tr><td align=center><b>CONTROL DE INGRESO Y EGRESO DE EMPLEADOS<b></td></tr>";

		echo "<tr><td colspan=2 bgcolor=#cccccc align=center>Codigo Empleado</td></tr>";
		echo "<tr><td colspan=2 bgcolor=#cccccc align=center><input type='TEXT' name='cedula' size=15 maxlength=12></td></tr>";
		echo "<tr><td colspan=2 bgcolor=#cccccc align=center><input type='submit' value='Aceptar'></td></tr>";
		echo "<tr><td colspan=2 align=center><b>Nota: Para marcar salida pase el carnet dos veces<b></td></tr>";
		echo "</table><br><br>";		
			
		if(isset($cedula))      // Si la variable ya ha sido seteada
		{	
			$l = strlen($cedula);
		//	if ($l <= 6)  
			if ($l <= 8)  
			{
				// Es empleado propio de Promotora	 
				$Tipoe = "P";
 				//	El codigo de barras en el carnet viene asi 007012 pero en la BD esta 07012 
 				//	=> Ignoro el primer digito
				//$query = "Select Codigo,Descripcion,Ccostos,Cconom";    //Para Clinica las Americas 
				//$query=$query."  From usuarios,clisur_000003 Where Codigo = '".substr($cedula,1)."'";
				$query = "Select Codigo,Descripcion,Ccostos,Ccodes";
				$query=$query."  From usuarios,clisur_000003 Where Codigo = '".$cedula."'";
				$query=$query."  And Ccostos = Ccocod";
				
			}	
			else
			{
				// Es empledo Externo a promotora
				$Tipoe = "E";
			    $query = "Select pexced,pexnom,pexfec,pexest FROM escara_000003"
	              		." Where pexced = '".$cedula."'";
            }      		
			
            
			$resultado = mysql_query($query,$conex);  
			//if ($resultado)
			//{ 
             $nroreg = mysql_num_rows($resultado);  
			 $campos = mysql_num_fields($resultado);             // Tomo el nro de campos	        
		    //} 
		    //else
		    // $nroreg = 0;

			if ( $nroreg > 0 )
			{
			  $row=array();                           // Defino un arreglo
			  
				for($i=1;$i<=$campos;$i++)  
					$row[$i-1]=mysql_result($resultado,0,$i-1);    // Lleno el arreglo con cada campo
		  	  
		      //if ($l <= 6)		
				if ($l <= 8)  							
				{
				 $nombre=$row[1];
				 echo "<table border=1>";				
				 echo "<tr><td bgcolor=#cccccc colspan=2 align=center><b>EMPLEADO : ".$row[0]."-".$nombre."<b></td></tr>";
				 //echo "<tr><td bgcolor=#cccccc colspan=2 align=center>CEDULA : ".$row[7]." CARGO : ".$row[8]."-".$row[9]."</td></tr>";
				 echo "<tr><td bgcolor=#cccccc colspan=2 align=center>CENTRO DE COSTOS : ".$row[2]."-".$row[3]."</td></tr>";
				 $FechaHora=date("Y-m-d H:i:s a");
				 echo "<tr><td bgcolor=#33FFFF colspan=2 align=center>FECHA-HORA: ".$FechaHora;
				 echo "<br>";
				}
				Else
				{
				 $row[2] = "2034";
				 echo "<table border=1>";				
				 echo "<tr><td bgcolor=#cccccc colspan=2 align=center><b>EMPLEADO : ".$row[1]."<b></td></tr>";
				 echo "<tr><td bgcolor=#cccccc colspan=2 align=center>CEDULA : ".$row[0]." CARGO : Empleado externo </td></tr>";
				 echo "<tr><td bgcolor=#cccccc colspan=2 align=center>CENTRO DE COSTOS : 2034-SERVICIOS GENERALES P.M.A </td></tr>";
				 $FechaHora=date("Y-m-d H:i:s a");
				 echo "<tr><td bgcolor=#33FFFF colspan=2 align=center>FECHA-HORA: ".$FechaHora;
				 echo "<br>";
				} 

				
				//Si un empleado pasa el carnet una vez es que esta ingresando si lo pasa 
				//dos veces esta saliendo. En una tabla siempre tengo el ultimo codigo ingresado
				$query = "Select ultcod,ultfec from escara_000002 where ultcod = '".$row[0]."'";
                 
				$resultado = mysql_query($query,$conex);
		   		$nroreg = mysql_num_rows($resultado);
		   		if ( $nroreg > 0 )   //Encontro ==> Es el mismo
				{
	 				// El ultimo codigo ES el mismo que se esta leyendo --> VA SALIENDO
					// Cambio el indicador de E por S en la tabla de horarios 
					$query = "UPDATE escara_000001 SET hortip = 'S' WHERE horcod = '".mysql_result($resultado,0,0)."'";
					$query = $query." AND horfec = '".mysql_result($resultado,0,1)."'";	
					$tipo = "S";
				}
				else
				{
				   // El ultimo codigo NO es el mismo que se esta leyendo --> VA ENTRANDO
				   // Grabo los datos en la tabla de 'horarios'  
				   $fecha = date("Y-m-d");
	               $hora = (string)date("H:i:s");
				   $query = "INSERT INTO escara_000001 (medico,fecha_data,hora_data,horcod,horfec,hortip,horcco,seguridad) ";
				   $query = $query." VALUES ('escara','".$fecha."','".$hora."','".$row[0]."','".$FechaHora."','E','".$row[2]."','C-escara')";
				   $tipo = "E";	
				}
									
				$resultado = mysql_query($query,$conex);  
				if ($resultado)
				
				{
				  if ( $tipo == 'S' ) 	
				       echo "<tr><td bgcolor=#33FFFF colspan=2 align=center>S A L I E N D O . . .";
				  else
				       echo "<tr><td bgcolor=#33FFFF colspan=2 align=center>E N T R A N D O . . ."; 
				}       
					
				//Actualizo ultimo codigo y su hora	
				$query = "UPDATE escara_000002 SET ultcod = '".$row[0]."', ultfec = '".$FechaHora."'";	
				$resultado = mysql_query($query,$conex); 
				if (!$resultado)
					echo "Error al actualizar ultima lectura...";
					
				//Si es un camillero lo actualizo en la BD matrix indicando si esta o no en turno
				$query = "select nombre from cencam_000002 where codced = '".$row[0]."'";	
				$escamillero = mysql_query($query,$conex);
				$nroreg = mysql_num_rows($escamillero);
				if ($nroreg > 0 )       // Encontro
				{
				  $wnomcam = mysql_result($escamillero,0);		
			  	  if ( $tipo == 'S' )
			  	  {
			  	   $query = "UPDATE cencam_000002 SET EnTurno = 'off' Where codced = '".$row[0]."'";
			  	   $resultado = mysql_query($query,$conex);
			  	   echo "<tr><td bgcolor=#33FFFF colspan=2 align=center>El camillero ".$wnomcam." queda Inactivo . . .";
		  	  	  } 
				  else
				  {
				   $query = "UPDATE cencam_000002 SET EnTurno = 'on'  Where codced = '".$row[0]."'";	
				   $resultado = mysql_query($query,$conex);
				   echo "<tr><td bgcolor=#33FFFF colspan=2 align=center>El camillero ".$wnomcam." queda Activo . . .";
			  	  } 
				}  
					
				
				Mysql_close($conex);
				echo "<br><br>";	
				unset($cedula);       // Destruyo la variable 'cedula'
			}
			else
			{
					echo "<center><table border=1>";		
					echo "<tr><td rowspan=2 align=center><IMG SRC='./apache.png' ></td>";  // una imagen centrada
					echo "</table></center>"; 	
					echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>ERROR NO EXISTE EL EMPLEADO !!!!</MARQUEE></FONT>";				
					echo "<br><br>";
			}
			echo "</table>";
		}
		
		echo "<li><A HREF='SALIDA.php'>Salir del programa</A>";
        
//   }
?>
</body>
</html>
