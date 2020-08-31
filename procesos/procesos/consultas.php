<html>

<head>
		<title>PROGRAMA DE PUNTOS</title>
		
		
		<script type="text/javascript">
<!--
	function nuevoAjax()
	{ 
		var xmlhttp=false; 
		try 
		{ 
			xmlhttp=new ActiveXObject("Msxml2.XMLHTTP"); 
		}
		catch(e)
		{ 
			try
			{ 
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); 
			} 
			catch(E) { xmlhttp=false; }
		}
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') { xmlhttp=new XMLHttpRequest(); } 

		return xmlhttp; 
	}
	
		function Buscar(fila, lista)
	{
					
					var x = new Array();
	
						x[2] = document.consultas.selecc.value;
						x[3] = document.consultas.ano.value;
						x[4] = document.consultas.mes.value;
						x[5] = document.consultas.dia.value;
						x[6] = document.consultas.ano2.value;
						x[7] = document.consultas.mes2.value;
						x[8] = document.consultas.dia2.value;
										
						st="consultas.php?lista="+lista+"&selecc="+x[2]+"&ano="+x[3]+"&mes="+x[4]+"&dia="+x[5]+"&ano2="+x[6]+"&mes2="+x[7]+"&dia2="+x[8]+"&bandera=1";
						
						ajax=nuevoAjax();
		 			ajax.open("GET", st, true);
		 			ajax.onreadystatechange=function() 
		 			{
			 
							if (ajax.readyState==4)
							{ 

									document.getElementById(+fila).innerHTML=ajax.responseText;
							} 
				}
		ajax.send(null);
			
	}


	
//-->

</script>
</head>

<body>

<?php
include_once("conex.php");

// bandera= indica si es la primera ves que se entra al programa
// lista= me indica que consulta se va a hacer, se recibe por get la primera vez, despues por hidden





/****************************PROGRAMA************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		

		


// realizo query para dropdown según lo enviado ////////////////////////////////////////////	
		if (!isset ($bandera))
		{
			
								/////////////////////////////////////////////////encabezado general///////////////////////////////////
 										echo "<table align='center' border='3' bgcolor='#336699' >\n" ;
  									echo "<tr>" ;
											echo "<td><img src='/matrix/images/medical/root/magenta.gif' height='61' width='113'></td>";
													echo "<td><font color=\"#ffffff\"><font size=\"5\"><b>&nbsp;SISTEMA DE COMENTARIOS Y SUGERENCIAS &nbsp;</br></b></font></font></td>" ;
  									echo "</tr>" ;
  									echo "</table></br></br>" ;
 	
  
  									echo "<table align='right' >\n" ;
  									echo "<tr>" ;
													echo "<td><b><font size=\"4\"><A HREF='listaMagenta.php' ><font color=\"#D02090\"><Lista de comentarios</font></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></font></td>" ;
													echo "<td><b><font size=\"4\"><A HREF='ayuda.mht' target='new'><font color=\"#D02090\">Ayuda</font></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></font></td>" ;
  											echo "</tr>" ;
  									echo "</table></br></br>" ;
  
  
/////////////////////////////////////////////////encabezado general///////////////////////////////////
														switch ($lista) 
														{
     															case 1: //consultar por unidad
     																			echo "<center><b><font size=\"4\" color=\"#D02090\">CONSULTA DE COMENTARIOS POR UNIDAD</font></b></center>";
     																			$query="select carcod, carnom, id from magenta_000019 where carest='on' ";
     																			$err = mysql_query($query,$conex);
     																			$num = mysql_num_rows($err);
     																			for($i=0;$i<$num;$i++)	
																								{	
																										$row=mysql_fetch_row($err);
																										$nombre[$i]=$row[1];
																										$codigo[$i]=$row[0];
																										$id[$i]=$row[2];
																								}
     																			break;
        												case 2:		//consultar por causa
																								echo "<center><b><font size=\"4\" color=\"#D02090\">CONSULTA DE COMENTARIOS POR CAUSA</font></b></center>";
																								$query='select caucod, caunom, id from magenta_000024 ';
																								$err = mysql_query($query,$conex);
     																			$num = mysql_num_rows($err);
     																				for($i=0;$i<$num;$i++)	
																								{	
																										$row=mysql_fetch_row($err);
																										$nombre[$i]=$row[1];
																										$codigo[$i]=$row[0];
																										$id[$i]=$row[0].'-'.$row[1];
																								}
																								break;
         												case 3:  //consultar por respuesta
         															echo "<center><b><font size=\"4\" color=\"#D02090\">CONSULTA DE COMENTARIOS POR RESPUESTA</font></b></center>";
																										$nombre[0]='telefonica';
																										$codigo[0]='1';
																										$id[0]='telefonico';
																										$nombre[1]='escrita';
																										$codigo[1]='2';
																										$id[1]='Escrito';
																										$nombre[2]='email';
																										$codigo[2]='3';
																										$id[2]='Email';
																										$nombre[3]='personal';
																										$codigo[3]='4';
																										$id[3]='Personal';
																										$nombre[4]='No respuesta';
																										$codigo[4]='5';
																										$id[4]='No respuesta';
																										$num=4;
         															break;
     									}	
// pinto dropdown de busqueda ////////////////////////////////////////////	
  				echo '<form name="consultas" action="consultas.php" method="post">';
								echo '<div id=1 align="center">';
        echo '<p>';
          echo '<font color="#000084">';
         	 echo '<br><br><strong>SELECCIONE LA OPCION DE BUSQUEDA:</strong>';
          echo '</font>';
        echo '</p>';
        echo '<p>';
          echo '<select name="selecc" size="1"/ on>';
          					for($i=0;$i<$num;$i++)	
															{	
																 echo '<option value="'.$id[$i].'">'.$codigo[$i].'-'.$nombre[$i].'</option>';
															}
    						echo '</select></p>';
    						
    						echo "<table align='center'>";
														echo "<tr>";
																		echo "<td align=center bgcolor='#336699'  width='100'><font size=3  face='arial' color='#ffffff'>Fecha inicial:&nbsp</td>";
																		echo "<td align=center bgcolor='#336699'><font size=2  face='arial' color='#ffffff'>Año:</b>&nbsp</td>";
																		echo "<td align=center bgcolor='#336699' ><font size='2'  align=center face='arial' color='#ffffff'><input type='text' name='ano' size='2'></td>";
																		echo "<td align=center bgcolor='#336699'><font size=2  face='arial' color='#ffffff'>Mes:</b>&nbsp</td>";
																		echo "<td align=center bgcolor='#336699' ><font size='2'  align=center face='arial' color='#ffffff'><input type='text' name='mes' size='1'></td>";
																		echo "<td align=center bgcolor='#336699'><font size=2  face='arial' color='#ffffff'>Día:</b>&nbsp</td>";
																		echo "<td align=center bgcolor='#336699' ><font size='2'  align=center face='arial' color='#ffffff'><input type='text' name='dia' size='1'></td>";
																		echo "<td align=center >&nbsp;&nbsp;&nbsp;&nbsp;</td>";
																		echo "<td align=center bgcolor='#336699'  width='100'><font size=3  face='arial' color='#ffffff'>Fecha final:&nbsp</td>";
																		echo "<td align=center bgcolor='#336699'><font size=2  face='arial' color='#ffffff'>Año:</b>&nbsp</td>";
																		echo "<td align=center bgcolor='#336699' ><font size='2'  align=center face='arial' color='#ffffff'><input type='text' name='ano2' size='2'></td>";
																		echo "<td align=center bgcolor='#336699'><font size=2  face='arial' color='#ffffff'>Mes:</b>&nbsp</td>";
																		echo "<td align=center bgcolor='#336699' ><font size='2'  align=center face='arial' color='#ffffff'><input type='text' name='mes2' size='1'></td>";
																		echo "<td align=center bgcolor='#336699'><font size=2  face='arial' color='#ffffff'>Día:</b>&nbsp</td>";
																		echo "<td align=center bgcolor='#336699' ><font size='2'  align=center face='arial' color='#ffffff'><input type='text' name='dia2' size='1'></td>";
																		echo "</td>";
										echo "</tr></TABLE></br>";
    						
    						$fila='Buscar(2,"'.$lista.'")';
    						echo "<input type='button' name='buscar' value='BUSCAR'  onclick='".$fila."' />";
     echo '</div>';
  				echo '</form>	';		

  				echo '<div id=2 align="center">';
  							echo '<hr>';
  				echo '</div>';
  //////////////////////////////////////////////////////////////////////////////////
  
		}else
		{
				echo '<hr>';
				switch ($lista) 
				{
     				case '1': //consultar por unidad
     											$query="select A.id, A.ccoori, A.ccofrec, A.ccoent, B.id,  B.cmonum, B.cmotip, b.cmocla, b.cmocau, b.cmoest, B.id_area from magenta_000017 A, magenta_000018 B where A.fecha_data between '".$ano."-".$mes."-".$dia."' and '".$ano2."-".$mes2."-".$dia2."' and  B.id_area=".$selecc." and A.id=B.id_comentario order by cmocau";
     											$mostrar='Causa';
     											$mostrar2='Estado';
     											break;
     						
        	case '2':				//consultar por causa
        							$query="select A.id, A.ccoori, A.ccofrec, A.ccoent, B.id, B.cmonum, B.cmotip, b.cmocla, b.cmoest, c.carnom  from magenta_000017 A, magenta_000018 B, magenta_000019 C where A.fecha_data between '".$ano."-".$mes."-".$dia."' and '".$ano2."-".$mes2."-".$dia2."' and B.cmocau='".$selecc."' and A.id=B.id_comentario and C.id=B.id_area ";
        							$mostrar='Estado';
        							$mostrar2='Causa';
        							break;
								
         case '3': //consultar por respuesta
         						$query="select A.id, A.ccoori, A.ccofrec, A.ccoent, B.id,  B.cmonum, B.cmotip, b.cmocla, b.cmocau, B.id_area  from magenta_000017 A, magenta_000018 B, magenta_000019 C where A.fecha_data between '".$ano."-".$mes."-".$dia."' and '".$ano2."-".$mes2."-".$dia2."'  and A.ccotres='".$selecc."' and b.cmoest='CERRADO' and A.id=B.id_comentario and C.id=B.id_area  ";
         						$mostrar='Causa';
        							$mostrar2='Unidad';
         						break;
    }

    $err = mysql_query($query,$conex);
    $num = mysql_num_rows($err);

    
    if ($num>0)
    {	 
    	 echo "<center><b><font size='4'><font color='#00008B'>COMENTARIOS INGRESADOS</b></font></font></center></br>";	
    	 	echo "<table border=1><tr>";
											echo "<td bgcolor='#336699' width='11%' ><font color='#ffffff'>Nº Comentario</font></a></td>";
											echo "<td bgcolor='#336699' width='11%' ><font color='#ffffff'>Nº Motivo</font></a></td>";
											echo "<td bgcolor='#336699' width='11%' ><font color='#ffffff'>Lugar de Origen</font></a></td>";
											echo "<td bgcolor='#336699' width='11%' ><font color='#ffffff'>Fecha de recepcion</font></a></td>";
											echo "<td bgcolor='#336699' width='11%' ><font color='#ffffff'>Entidad</font></a></td>";
											echo "<td bgcolor='#336699' width='11%' ><font color='#ffffff'>tipo</font></a></td>";
											echo "<td bgcolor='#336699' width='11%' ><font color='#ffffff'>clase</font></a></td>";
											echo "<td bgcolor='#336699' width='11%' ><font color='#ffffff'>".$mostrar."</font></a></td>";
											echo "<td bgcolor='#336699' width='11%' ><font color='#ffffff'>".$mostrar2."</font></a></td>";

							echo "</tr>";
				}
	 		for($i=1;$i<=$num;$i++)	
				{
							$row=mysql_fetch_row($err);
							
								echo "<tr>";
											echo "<td  width='11%' align='center'><a href='detalleComentario.php?idCom=".$row[0]."' align='right'><font >".$row[0]."</font></a></td>";
											echo "<td  width='11%' ><font >".$row[5]."</font></a></td>";
											echo "<td  width='11%' ><font >".$row[1]."</font></a></td>";
											echo "<td  width='11%' ><font >".$row[2]."</font></a></td>";
											echo "<td  width='11%' ><font >".$row[3]."</font></a></td>";
											echo "<td  width='11%' ><font >".$row[6]."</font></a></td>";
											echo "<td width='11%' ><font >".$row[7]."</font></a></td>";
											echo "<td  width='11%' ><font >".$row[8]."</font></a></td>";
											echo "<td  width='11%' ><font >".$row[9]."</font></a></td>";
								echo "</tr>";
					}
	echo "</table></br></br>";		

	}
		
		
}

?>
</body>