<HTML>

<head>
  <title>MATRIX</title>
  <script language="javascript">
      function NewDrop()
      {
       	document.usuarios.submit();
      }
      
  </script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></head>

<body BGCOLOR="">
	<font size=2>  
	<BODY TEXT="#000066">
 	
		<table border=0 align=center>
			<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Documentación de campos de formularios</font></a></tr></td>
			<tr><td align=center bgcolor="#cccccc"><font size=2> <b> backup.php Ver. 1.00</b></font></tr></td></table>
		</table>


	<?php
include_once("conex.php"); 
	
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
	{

		

		


		//error_reporting(E_ERROR);

		
		//******************* Primera Pantalla:Seleccioón de Formularios*******************
		if (isset($accion))
				{
	
					$campo = $HTTP_POST_VARS['campo'];
					$textos = $HTTP_POST_VARS['texto'];
					//$ids = $HTTP_POST_VARS['id'];

					for ($i=0; $i< count($textos);$i++)
					{
						if ( $textos[$i] ) 
						{
	
							$query ="SELECT id FROM `root_000030` WHERE Dic_Usuario='$usuario' and Dic_campo='$campo[$i]' and Dic_Formulario='$formulario'";
							$hacer = mysql_query($query);
							//echo $query;
							if ($resulta=mysql_fetch_row($hacer))
							{
								$query ="UPDATE `root_000030` SET Dic_Descripcion='$textos[$i]' WHERE Dic_Usuario='$usuario' and Dic_Formulario='$formulario' and Dic_Campo='$campo[$i]'";
								$resultado = mysql_query($query);
								//echo $query;
							}
							else
							{
								$f=date("Y-m-d");  
								$s=date("h:m:s"); 
								$query = "INSERT INTO `root_000030` ( `Medico` , `Fecha_data` , `Hora_data` , `Dic_Usuario` , `Dic_Formulario` , `Dic_Campo` , `Dic_Descripcion` , `Seguridad` , `id` )"; 
								$query = $query."VALUES ('root', '$f', '$s', '$usuario', '$formulario', '$campo[$i]', '$textos[$i]', 'C-root', '')";
								if ( !mysql_query( $query) )
								{
									echo "error ejecutando $query";
								}
								//echo $query;
							}
		
						}
				
						else
						{
							//echo "Estoy nulo o vacío";
						}
				}
			}	
			
		
		if (!isset($cuenta))
			{
				//variables que se reciben la segunda vez

				//$valor = $HTTP_POST_VARS['accion'];
				//$medico = $HTTP_POST_VARS['usuario'];
				

				//echo "valor $valor <br/>";
				//echo "medico $medico <br/> ";

				//cuando se mete por segunda vez, la bandera debe ser 2
				if (isset($valor))
				{
					$bandera=2;
					//echo "bandera $bandera <br/>";
				}
				else
				{
					$bandera=1;
					//echo "bandera $bandera <br/>";
	
				}
	
	
				//primera consulta, por usuario
				$query ="SELECT medico FROM formulario GROUP BY medico";
				$result = mysql_query($query);
				//$cantidad=mysql_num_rows($result);
				//echo "numero filas $cantidad <br>";

	
				//inicialización de variables, número para el while y valor para enviar por POST
				$numero=0;
				$valor=1;	

	
				//Pintar el dropdown
				echo "<br><br>";	
				echo " <center><font size='5'>Selección de Usuario y formulario para documentación de campos</font size='5'></center>";
				echo "<table align=center border=0 >";
					echo "<form NAME='usuarios' ACTION='SeleForm.php' METHOD='POST'>";
					echo "<INPUT TYPE='hidden' NAME='valor' VALUE='$valor'>";
					echo "<tr>";
					echo "<td bgcolor='#cccccc' align=center>Usuario:</td>";
					echo "<td bgcolor='#cccccc'><select name='usuario' onChange='NewDrop()'>";
				While ( $resulta = mysql_fetch_row($result) )
  				{ 
     				$usuarios[$numero]=$resulta[0];
					$option = "<option value='$resulta[0]'";

						if ( !strcmp($usuario,$resulta[0]) and !$accion )
						{
							$option = $option." selected";
							$porDefecto = $resulta[0];
						}

					echo $option.">".$resulta[0]."</option>";
					$numero++;
   				} 
   		
  				if ( !$porDefecto )  
  				{
	  				$usuario = $usuarios[0];
	  			}
	  			//echo "usuario por defecto $usuario";
		  	
				echo "</select></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td bgcolor='#cccccc'> formulario: </td>";
				echo "<td bgcolor='#cccccc'>";
				echo "</form>";
				mysql_free_result($result);
				
	
				// En caso de ser la primera vez que entra se consulta y se pinta dropdown sin seleccion
				if ($bandera==1)
				{
					$numero=0;
					$cuenta=1;
					$query ="SELECT codigo, nombre FROM formulario WHERE medico='".$usuarios[0]."'";
					//echo $query;
					$result = mysql_query($query);
					//$cantidad=mysql_num_rows($result);
					//echo "numero filas $cantidad <br>";

					echo "<form NAME='formularios' ACTION='SeleForm.php' METHOD='POST'>";
					echo "<select name='formulario'>";
	
					While ($resulta=mysql_fetch_row($result))
   					{ 
     					$formulario[$numero]=$resulta[0];
     					$nombre[$numero]=$resulta[1];
     					$completo[$numero]=$resulta[0]."-".$resulta[1];
						echo "<option value='$resulta[0]'>$completo[$numero]</option>";
						$numero++;
   					} 
   					echo "</select> </td><br/><br/>";
   					echo "</tr>";
   					echo "<input type='hidden' name='usuario' value='$usuario' />";
   					echo "<input type='hidden' name='cuenta' value='$cuenta' />";
   					echo "<tr>";
   					echo "<td bgcolor='#cccccc'> </td>";
   					echo "<td bgcolor='#cccccc' align=center>";
   					echo "<input type='submit' value='Aceptar' />";
   					echo "</td>";
   					echo "</tr>";
					echo "</form>";
					mysql_free_result($result);	
					mysql_close($conex);				
				}	
				if ($bandera==2)
	
				{
					$query ="SELECT codigo, nombre FROM formulario WHERE medico='$usuario'";
					$result = mysql_query($query);
					//$cantidad=mysql_num_rows($result);
					//echo "numero filas $cantidad <br>";
					//echo $query;

					echo "<form NAME='formularios' ACTION='SeleForm.php' METHOD='POST'>";
					echo "<select name='formulario'>";
	
					$numero=0;
					While ($resulta=mysql_fetch_row($result))
   					{ 
	  
     					$formulario[$numero]=$resulta[0];
     					$nombre[$numero]=$resulta[1];
     					$completo[$numero]=$formulario[$numero]."-".$nombre[$numero];
						echo "<option value='$formulario[$numero]'>$completo[$numero]</option>";
						$numero++;
   					} 
   					$cuenta=1;
   					echo "</select></td><br/><br/>";
   					echo "</tr>";
   					echo "<input type='hidden' name='usuario' value='$usuario' />";
   					echo "<input type='hidden' name='cuenta' value='$cuenta' />";
   					echo "<tr>";
   					echo "<td bgcolor='#cccccc'>  </td>";
   					echo "<td bgcolor='#cccccc' align=center>";
   					echo "<input type='submit' value='Aceptar' />";
   					echo "</td >";
   					echo "</tr>";
					echo "</form>";
					mysql_free_result($result);
					mysql_close($conex);
				} 
				
				echo "</table>";
			}
	//*******************Segunda Pantalla:Documentacion de campos*******************
			else
			{
	
 				//$formulario= $HTTP_GET_VARS['formulario'];
 				//$nombre= $HTTP_POST_VARS['usuario'];
 				//$accion= $HTTP_POST_VARS['accion'];
 

				// Función de mostrar
				function mostrar($formulario, $usuario)
				{
					//echo "codigo $codigo <br/>";
 					//echo "medico $nombre <br/>";
					$query ="SELECT id,campo,descripcion,tipo FROM det_formulario WHERE codigo='$formulario' and medico='$usuario'";
					$result = mysql_query($query);
					//$cantidad=mysql_num_rows($result);
					//echo "cantidad $cantidad <br/>";
	
					$numero=0;
					echo "<form name='forma' ACTION='SeleForm.php' method='POST'>";
					echo "</br>";
					echo "<h2 align=center>Documentación de los campos del Formulario: $formulario para el Usuario: $usuario</h2></br>";
					echo "<table align=center>";
							echo "<tr>";
	  							echo'<td width="25%"  align="center" style="color: black; border-left: .75pt solid silver; border-right: .75pt solid silver; border-top: .75pt solid silver; border-bottom: .75pt solid silver; background-color: #999999">&nbsp;';
	  							echo '<font size="5">Campo</font></td>';
	  							echo'<td width="25%" align="center" style="color: black; border-left: .75pt solid silver; border-right: .75pt solid silver; border-top: .75pt solid silver; border-bottom: .75pt solid silver; background-color: #999999">&nbsp;';
	  							echo '<font size="5">Descripción</font></td>';
	  							echo'<td width="25%" align="center" style="color: black; border-left: .75pt solid silver; border-right: .75pt solid silver; border-top: .75pt solid silver; border-bottom: .75pt solid silver; background-color: #999999">&nbsp;';
	  							echo '<font size="5">Tipo</font></td>';
	  							echo'<td width="25%" align="center" style="color: black; border-left: .75pt solid silver; border-right: .75pt solid silver; border-top: .75pt solid silver; border-bottom: .75pt solid silver; background-color: #999999">&nbsp;';
	  							echo '<font size="5">Comentario</font></td>';
	  						echo "</tr>";
	
	  						$i=0;	
							while ($resulta=mysql_fetch_row($result))
							{
								if ($i==0)
								{
									$color="white";
									$i=1;
								}
								else
								{
									$color="#cccccc";
									$i=0;
								}
								//echo "<input type='hidden' name='id[]' value='$resulta[0]' />";
								echo "<tr>";
	  								echo'<td width="25%" align="center" style="color: black; border-left: .75pt solid silver; border-right-style: none; border-top-style: none; border-bottom: .75pt solid silver; background-color:'. $color.'">&nbsp;';
	  								echo $resulta[1].'</td>';
	  								echo "<input type='hidden' name='campo[]' value='$resulta[1]' />";
	  								echo'<td width="25%" align="center" style="color: black; border-left: .75pt solid silver; border-right-style: none; border-top-style: none; border-bottom: .75pt solid silver; background-color: '. $color.'">&nbsp;';
	  								echo $resulta[2].'</td>';
	  								echo'<td width="25%" align="center" style="color: black; border-left: .75pt solid silver; border-right-style: none; border-top-style: none; border-bottom: .75pt solid silver; background-color: '. $color.'">&nbsp;';
	  								echo $resulta[3].'</td>';
	  		
	  								$query ="SELECT Dic_Descripcion FROM `root_000030` WHERE Dic_Usuario='$usuario' and Dic_campo='$resulta[1]' and Dic_Formulario='$formulario'";
									$hacer = mysql_query($query);
									$resultado=mysql_fetch_row($hacer);
	  								echo'<td width="25%" align="center" style="color: black; border-left: .75pt solid silver; border-right-style: none; border-top-style: none; border-bottom: .75pt solid silver; background-color: '. $color.'">&nbsp;';
	  								echo "<input type='text' name='texto[]' value='$resultado[0]' />";
	  							echo "</tr>";
	  		
							}
							
						$accion=1;
					
	
						echo "</table>";
						echo "<input type='hidden' name='formulario' value='$formulario' />";
						echo "<input type='hidden' name='usuario' value='$usuario' />";
						echo "<input type='hidden' name='accion' value='$accion' />";
				
						echo "</br> <table align=center>"; 
						echo "<input type='submit' value='enviar'/>";
						echo "</table>";
						echo "</form>";
				}
					
					mostrar($formulario,$usuario);
					mysql_close($conex);
		
		}

	}
?>
</body> 
</html> 





