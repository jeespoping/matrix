<head>
    <title>Reporte de devoluciones por descarte</title>
	<script type='text/javascript' src='../../../include/root/jquery-1.3.2.js'></script>
	<script type='text/javascript' src='../../../include/root/jquery.blockUI.min.js'></script>
    <style type="text/css">

        A	{text-decoration: none;color: #000066;}
        .tipo3V{color:#000066;background:#dddddd;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo3V:hover {color: #000066; background: #999999;}
    </style>

</head>

<script type="text/javascript"> 

 
   function cerrarVentana()
    {
        window.close()		  
    }
     
	function enter()
	{
	 document.descarte.submit();
	}
	
	 //FUNCION QUE PERMITE GENERAR UNA VENTANA EMERGENTE CON UN PATH ESPECIFICO
    function ejecutar(path)
    {
    window.open(path,'','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
    } 
		
</script>

<body>

    <?php
include_once("conex.php");
    /** ********************************************************
     *                   REPORTE DESCARTES 				       *
     *                                      				   *
     *     				                   					   *
     * ******************************************************* */
//==================================================================================================================================
//PROGRAMA                   : devoluciones_descarte.php
//AUTOR                      : Jonatan Lopez Aguirre.

//FECHA CREACION             : 10 Abril de 2012
//FECHA ULTIMA ACTUALIZACION :
    $wactualiz = "(Febrero 12 de 2013)";
	
//Actualizacion:
//Febrero 12 de 2013
//Se modifica la consulta a los articulos devueltos por rango de fecha, historia e ingreso, ya que solamente se estaba buscando por 
//consecutivo.
//Julio 10 de 2012 Viviana Rodas.  Se agrega el llamado a las funciones consultaCentrosCostos que hace la consulta de los centros de costos 
// de un grupo seleccionado y dibujarSelect que dibuja el select con los centros de costos obtenidos de la primera funcion.	
	
//DESCRIPCION
//========================================================================================================================================\\
//========================================================================================================================================\\
//     Programa usado para saber quienes han hecho devoluciones por descarte, en un lapse de tiempo determinado.                          \\
   
//------------------------------------------------------------------------\\

    
    session_start();

 if (!isset($_SESSION['user']))
        echo "error";
else 
	{            
        $pos = strpos($user, "-");
        $wusuario	= substr($user, $pos + 1, strlen($user));       
		
		include_once("movhos/movhos.inc.php");
		
		$wtabcco = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tabcco');
        $wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
                     
        echo "<br>";
        echo "<br>";

        //*******************************************************************************************************************************************
        //F U N C I O N E S
        //===========================================================================================================================================
		function traer_nombre_articulo($wcodigo) 
             {

                global $conex;                
                global $whce;
                global $wmovhos;
				global $wnombre_articulo;
				
               
                $query =     "SELECT Artcom "
                             . "FROM ".$wmovhos."_000026 "
                            . "WHERE Artcod = '" . $wcodigo . "'";

                $res = mysql_query($query, $conex) or die("Error: " . mysql_errno() . " - en el query: ". $query ." - " . mysql_error());
                $row = mysql_fetch_array($res);  
				
                return $row['Artcom'];
           
             }   
		
		function traer_motivo_descarte($wcodigo_motivo) 
             {

                global $conex;                
                global $whce;
                global $wmovhos;
                global $wnombre_motivo;
				
                $query =     "SELECT Jusdes "
                             . "FROM ".$wmovhos."_000023 "
                            . "WHERE Juscod = '" . $wcodigo_motivo . "'
								 AND Justip = 'D'";

                $res = mysql_query($query, $conex) or die("Error: " . mysql_errno() . " - en el query: ". $query ." - " . mysql_error());
                $row = mysql_fetch_array($res);  
				
                return $row['Jusdes'];
           
             }   
		
		
		function consultarArticulos($wconsecutivo, $wfecha_inicio, $wfecha_fin, $whistoria, $wingreso) 
        {
            
            global $conex;
            global $wmovhos;                       
            global $wemp_pmla;
			global $wconsecutivo;
			global $whis;
			global $wing;
			global $wcco;
		
			//Selecciono todos los pacientes del servicio seleccionado
            $query =  " SELECT " . $wmovhos . "_000031.Fecha_data, " . $wmovhos . "_000031.Hora_data, Descon, Descco, Desart, Descan, Desdes, " . $wmovhos . "_000031.Seguridad "
                    . "   FROM " . $wmovhos . "_000031, " . $wmovhos . "_000035 "
                    . "  WHERE " . $wmovhos . "_000031.Fecha_data BETWEEN '".$wfecha_inicio."' AND '".$wfecha_fin."'"
                    . "    AND Descon = Dencon "
                    . "    AND Denhis = '".$whistoria."'"
                    . "    AND Dening = '".$wingreso."'"
					. " ORDER BY Descco DESC";
					
            $res = mysql_query($query, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $query . " - " . mysql_error());
            $num = mysql_num_rows($res);
			
			
            if ($num > 0)           
            {
				echo "<table align=center>
                      <tbody>                
                      <tr class='fila1'>                     
                      
                      <td align='center'>
                      <font size='3' textaling= center><b>Historia : ".$whistoria."</b></font>
                      </td>
                     
                      <td align='center'>
                      <font size='3' textaling= center><b>Ingreso : ".$wingreso."</b></font>
                      </td>
					  
					   <td align='center'>
                      <font size='3' textaling= center><b>Centro de Costos : ".$wcco."</b></font>
                      </td>
                      </tr>
                      </tbody>  
                      </table>";
				
				echo "<table align=center>";
                echo "<br>";                
                echo "<tr class=encabezadoTabla>";				
								
                echo "<th><font size=3>Árticulo</font></th>";
                echo "<th><font size=3>Cantidad devuelta <br> descarte</font></th>";
				echo "<th><font size=3>Motivo</font></th>"; 				
                echo "</tr>";     

                    for ($i = 1; $i <= $num; $i++) 
                    {
                        $row = mysql_fetch_array($res);
                        
                        if (is_integer($i / 2))
                            $wclass = "fila1";
                        else
                            $wclass = "fila2";									
                       
						$wcodigo = $row['Desart'];						
                        $wnom_articulo = traer_nombre_articulo($wcodigo);
						
						$wcodigo_descarte = $row['Desdes'];
						$wmotivodesc = traer_motivo_descarte($wcodigo_descarte);
						
						$wcantidad = $row['Descan']; 
                        //========================================================================================================== 						
						echo "<tr class=" . $wclass . ">";                       
                        echo "<td align=center>" . $wnom_articulo ."</td>";
						echo "<td align=center><b>" . $wcantidad . "</b></td>";
                        echo "<td align=center><b>" . $wmotivodesc . "</b></td>";	
                        echo "</tr>";
                    }
            }
            else
                echo "<br><b>NO HAY REGISTROS</b><br>";
            echo "</table>";
		
		}
        
        function devoluciones_descarte_cco() 
        {
            
            global $conex;
            global $wmovhos;                       
            global $wemp_pmla;
            global $wusuario;
            global $wtabcco;
            global $wcco;          
			global $wfechainicial;
			global $wfechafinal;
            
            $wcco1 = explode("-", $wcco); 
            global $wbasedato;
			$wbasedato=$wmovhos;
            
            
            echo "<center><table>";
            echo "<tr class=fila1>";
			echo "<table class=fila1>";
			echo "<tr>";
            echo "<td colspan=2>Fecha inicial: <br>";
			campoFechaDefecto( "wfechainicial", $wfechainicial);
			echo "</td>";  
			echo "<td>Fecha final: <br>";
			campoFechaDefecto( "wfechafinal", $wfechafinal);
			echo "</td>";
			echo "</tr>";
			echo "</table>";
			echo "</tr>";
			echo "<br>";
			
			//Seleccionar CENTRO DE COSTOS
			
			//**************** llamada a la funcion consultaCentrosCostos y dibujarSelect************
	 	    $cco="Ccohos";  // filtros para la consulta
		    $sub="on";
		    $tod="Todos";
		    $ipod="off";  //para que pinte le select mediano
		    //$cco="Todos";
		    $filtro="--";
		    $centrosCostos = consultaCentrosCostos($cco);  
		 
		
		    echo "<table align='center' border=0>";		
		    $dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod);
					
		    echo $dib;
		    echo "</table>";
			
			echo "<table>";
			echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
			echo "</table>";
			
			if ($wcco1[0] == '%')
			{
			 //Selecciono todos los pacientes del servicio seleccionado cuando el centro de costos es %
            $query =  " SELECT " . $wmovhos . "_000035.Fecha_data, " . $wmovhos . "_000035.Hora_data, Descon, Descco, Desart, Descan, Desdes, Denusu, Denhis, Dening"
                    . "   FROM " . $wmovhos . "_000031, " . $wmovhos . "_000035"
                    . "  WHERE Dencon = Descon"
					."     AND Denori LIKE '" . $wcco1[0] . "'"
                    . "    AND " . $wmovhos . "_000035.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."'"
					."GROUP BY Denhis, Dening "
					."ORDER BY " . $wmovhos . "_000035.Fecha_data";
			
			}
			else
			{
            //Selecciono todos los pacientes del servicio seleccionado
            $query =  " SELECT " . $wmovhos . "_000035.Fecha_data, " . $wmovhos . "_000035.Hora_data, Descon, Descco, Desart, Descan, Desdes, Denusu, Denhis, Dening "
                    . "   FROM " . $wmovhos . "_000031, " . $wmovhos . "_000035"
                    . "  WHERE Dencon = Descon"
					."     AND Denori LIKE '" . $wcco1[0] . "'"
                    . "    AND " . $wmovhos . "_000035.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."'"
					."GROUP BY Denhis, Dening "
					."ORDER BY " . $wmovhos . "_000035.Fecha_data";
			}
				
            $res = mysql_query($query, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $query . " - " . mysql_error());
            $num = mysql_num_rows($res);           
           
			
            if ($num > 0) 
            
            {
				echo "<table style='width: 800px;'>";
                echo "<br>";                
                echo "<tr class=encabezadoTabla>";                            
                echo "<th><font size=3 >Historia - Ingreso</font></th>";
                echo "<th><font size=3 >Nombre del responsable</font></th>";
                echo "<th><font size=3 >Fecha</font></th>";
                echo "<th><font size=3>Hora</font></th>";
				echo "<th><font size=3>Articulos</font></th>";
                echo "</tr>";     

                    for ($i = 1; $i <= $num; $i++) 
                    {
                        $row = mysql_fetch_array($res);
                        
                        if (is_integer($i / 2))
                            $wclass = "fila1";
                        else
                            $wclass = "fila2";
						
						$whis = $row['Denhis'];
						$wing = $row['Dening'];
						$wusuario = $row['Denusu'];
						
						if ($wusuario != '')
							{
							//Consulta el nombre del usuario que grabo la devolucion por descarte						
							$queryuser =  " SELECT Codigo, Descripcion"
										. "   FROM usuarios" 
										. "  WHERE Codigo = ".$wusuario."";					
							$resuser = mysql_query($queryuser, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $query . " - " . mysql_error());
							$rowuser = mysql_fetch_array($resuser); 
							$wuserresponsable = $rowuser['Descripcion'];							
							}
						else
							{
							$wuserresponsable = '';
							}
							
						$wfechadescarte = $row['Fecha_data']; //Fecha del descarte
						$whoradescarte = $row['Hora_data']; // Hora del descarte
                       						
						echo "<tr class=".$wclass.">";
                        echo "<td align=center><b>". $whis . "-" .$wing. "</b></td>";
                        echo "<td align=left>" . $wuserresponsable ."</td>";
                        echo "<td align=center><b>" . $wfechadescarte . "</b></td>";                       
                        echo "<td align=center>" . $whoradescarte . "</td>";
						// ENLACE VER PARA ACCEDER A LA INFORMACION DE LOS MEDICAMENTOS DESCARTADOS	
						$path = "/matrix/movhos/reportes/rep_devoluciones_descarte.php?wemp_pmla=".$wemp_pmla."&wconsecutivo=".$row['Descon']."&wfecha_inicio=".$wfechainicial."&wfecha_fin=".$wfechafinal."&whistoria=".$whis."&wingreso=".$wing."&wcco=".$wcco1[0]."";
						echo "<td align=center><A HREF=# onclick='ejecutar(".chr(34).$path.chr(34).")'>Ver</A></td>";                         
                        echo "</tr>";
                    }
            }
            else
                echo "<br><b>NO HAY REGISTROS</b><br>";
            echo "</table>";
            
           
        }

    //===========================================================================================================================================
    // P R I N C I P A L 
    //===========================================================================================================================================
		global $wemp_pmla;
		
        echo "<form name='descarte' action='' method=post>";        
        echo "<input type='HIDDEN' name='wemp_pmla' value='" . $wemp_pmla . "'>";

        encabezado("Reporte de devoluciones por descarte", $wactualiz, "clinica");
        
        if (isset($wconsecutivo) and isset($wfecha_inicio) and isset($wfecha_fin) and isset($whistoria) and isset($wingreso)) 
            {
                  
             consultararticulos($wconsecutivo, $wfecha_inicio ,$wfecha_fin, $whistoria, $wingreso);
             echo "<br><br>";
			 
            }
            else 
            {
            // LLAMADO A LA FUNCION QUE PERMITE OBSERVAR EL LISTADO DE ARTICULOS DEVUELTOS POR DESCARTE                        
             devoluciones_descarte_cco();
        
			echo "<br>";
			echo "<table align=center>";
			echo "</table>";
            }
        
        echo "<table align=center>";
		echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";			
        echo "</table>";   
                
    }
    ?>