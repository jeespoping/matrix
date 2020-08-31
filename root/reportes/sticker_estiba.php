<?php
include_once("conex.php");
	if(!isset($_SESSION['user']))
	{
	echo "error";
	return;
	}

	$key = substr($user,2,strlen($user));
	include_once("root/comun.php");
	include_once("movhos/movhos.inc.php");
	

	

	$conex_o = odbc_connect('inventarios','','') or die("No se ralizo Conexion a UNIX");
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

//Si seleccionan el boton imprimir ingresa a esta rutina
if(isset($accion) && ($accion == 'Imprimir'))
{

		$datamensaje = array('mensaje'=>'', 'error'=>0);

		//Si el articulo inicial es diferente al final, se utilizan las variables $wubi1 y $wubi2 para realizar el filtro, en caso
		//contrario consultara ese rango de articulos.
		if($wart1 != $wart2)
		{
			$query  = "SELECT artcod,artuni,artnom ";
			$query .= "  FROM ivart, ivartubi ";
			$query .= " WHERE artcod between '".$wart1."' and '".$wart2."' ";
			$query .= "   AND artubiart = artcod ";
			$query .= "   AND artubiser = '".$wcco."' ";
			$query .= "   AND artubiubi between '".substr($wubi1,0,strpos($wubi1,"-"))."' and '".substr($wubi2,0,strpos($wubi2,"-"))."' ";
		}
		else
		{
			$query  = "SELECT artcod,artuni,artnom ";
			$query .= " FROM ivart ";
			$query .= "WHERE artcod between '".$wart1."' and '".$wart2."' ";
		}
		
		$err = odbc_do($conex_o,$query);
		$campos= odbc_num_fields($err);
		$data=array();
		$num=0;

		while(odbc_fetch_row($err))
		{
			$row=array();
			for($i=1;$i<=$campos;$i++)
			{
				$row[$i-1]=odbc_result($err,$i);
			}
			$num++;
			$data[$num][0]=$row[0];  //Codigo del articulo
			$data[$num][1]=$row[1];	 //Unidad
			$data[$num][2]=$row[2];	 //Nombre
		}

		if($num % 2 != 0 and $num > 0)
		{
			$num++;
			$data[$num][0]=$row[0]; //Codigo del articulo
			$data[$num][1]=$row[1]; //Unidad
			$data[$num][2]=$row[2]; //Nombre
		}

		$num=$num / 2;


		//Si la respuesta de la consulta es cero entonces mostrara el mensaje diciendo que no se imprimio ninguna etiqueta
		if($num != 0)
		{
			$datamensaje['mensaje'] = "NUMERO DE ETIQUETAS IMPRESAS: ".$num."";
		}
		else
		{
			$datamensaje['mensaje'] = "NO SE IMPRIMIO NINGUNA ETIQUETA, FAVOR REVISAR LOS ÁRTICULOS INGRESADOS, ES POSIBLE QUE ESTÉN EN MINÚSCULA";		
		}


		if($num > 0)
		{
			for ($i=1;$i<=$num;$i++)
			{
				
				$paquete="";
				$paquete=$paquete."N".chr(13).chr(10);
				$paquete=$paquete."FK".chr(34)."ESTIBA".chr(34).chr(13).chr(10);
				$paquete=$paquete."FS".chr(34)."ESTIBA".chr(34).chr(13).chr(10);; 
				$paquete=$paquete."V00,6,L,".chr(34)."COD1".chr(34).chr(13).chr(10);
				$paquete=$paquete."V01,2,L,".chr(34)."UNI1".chr(34).chr(13).chr(10);
				$paquete=$paquete."V02,20,L,".chr(34)."NOM1".chr(34).chr(13).chr(10);
				$paquete=$paquete."V03,20,L,".chr(34)."NOM1A".chr(34).chr(13).chr(10);
				$paquete=$paquete."V04,6,L,".chr(34)."COD2".chr(34).chr(13).chr(10);
				$paquete=$paquete."V05,2,L,".chr(34)."UNI2".chr(34).chr(13).chr(10);
				$paquete=$paquete."V06,20,L,".chr(34)."NOM2".chr(34).chr(13).chr(10);
				$paquete=$paquete."V07,20,L,".chr(34)."NOM2A".chr(34).chr(13).chr(10);
				$paquete=$paquete."Q265,24".chr(13).chr(10);
				$paquete=$paquete."q400".chr(13).chr(10);
				$paquete=$paquete."A40,0,0,5,1,1,N,V00".chr(13).chr(10);
				$paquete=$paquete."A290,0,0,5,1,1,N,V01".chr(13).chr(10);
				$paquete=$paquete."A40,55,0,4,1,1,N,V02".chr(13).chr(10);
				$paquete=$paquete."A40,85,0,4,1,1,N,V03".chr(13).chr(10);
				$paquete=$paquete."A420,0,0,5,1,1,N,V04".chr(13).chr(10);
				$paquete=$paquete."A670,0,0,5,1,1,N,V05".chr(13).chr(10);
				$paquete=$paquete."A415,55,0,4,1,1,N,V06".chr(13).chr(10);
				$paquete=$paquete."A415,85,0,4,1,1,N,V07".chr(13).chr(10);
				$paquete=$paquete."FE".chr(13).chr(10);
				$paquete=$paquete.".".chr(13).chr(10);
				$paquete=$paquete."FR".chr(34)."ESTIBA".chr(34).chr(13).chr(10);
				$paquete=$paquete."?".chr(13).chr(10);
				$paquete=$paquete.$data[$i + $i -1][0].chr(13).chr(10);    // Codigo del articulo
				$paquete=$paquete.$data[$i + $i -1][1].chr(13).chr(10);	   // Presentacion de la unidad
				$paquete=$paquete.substr($data[$i + $i -1][2],0,19).chr(13).chr(10);  // Nombre 1 (Primeros 18 caracteres)
				$paquete=$paquete.substr($data[$i + $i -1][2],19).chr(13).chr(10);    // Nombre 1A (Caracteres despues del 18)
				$paquete=$paquete.$data[$i + $i][0].chr(13).chr(10);  // Codigo 2
				$paquete=$paquete.$data[$i + $i][1].chr(13).chr(10);  // Unidad 2
				$paquete=$paquete.substr($data[$i + $i][2],0,19).chr(13).chr(10); // Nombre 2 (Primeros 18 caracteres)
				$paquete=$paquete.substr($data[$i + $i][2],19).chr(13).chr(10);   // Nombre 2A (Caracteres despues del 18)
				$paquete=$paquete."P".$wnum.chr(13).chr(10);
				$paquete=$paquete.".".chr(13).chr(10);
				//Comandos de impresion
				$addr=$wip;				
				if(!(@$fp = fsockopen( $addr,9100, $errno, $errstr, 30))) 
				{
				$datamensaje['mensaje'] = "NO HUBO CONEXION CON LA IMPRESORA";
				$datamensaje['error'] = 1;
				}
				else 
				{
				    fputs($fp,$paquete);
					fclose($fp);
				}
				sleep(2);
			}
		}

  	echo json_encode($datamensaje);

   return;
}
?>
<html>

<head>
  <title>MATRIX Sticker de Articulos Para Las Estibas del Almacen General</title>
</head>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script type="text/javascript">

	function enter()
	{
	 document.forms.estibas.submit();
	}

    function imprimirstickers()
    {
    	$('#div_resultado_comparacion').html('<div align="center"><img src="../../images/medical/ajax-loader5.gif"/></div>');
        $.post("sticker_estiba.php",
            {
                consultaAjax: '',
                wemp_pmla       : $("#wemp_pmla").val(),
                wcco         	: $("#wcco").val(),
                wart1         	: $("#wart1").val(),
                wart2         	: $("#wart2").val(),
                wnum         	: $("#wnum").val(),
                wip          	: $("#wip").val(),
                wubi1			: $("#wubi1 option:selected").text(),
                wubi2			: $("#wubi2 option:selected").text(),               			
                accion 			: 'Imprimir'
            }
            ,function(data_json) {
            	if (data_json.error == 1)
            	{
            		alert(data_json.mensaje);
            		enter();
            	}
            	else
            	{
                $('#div_resultado_comparacion').html('');
                alert(data_json.mensaje);
                enter();	
            	}
              
            },
            "json"
        );
    }

   

</script>
<BODY>
	<?php
		
		/*     * *******************************************************
     *              STICKERS ESTIBAS  					*    
     * ******************************************************* */
//==================================================================================================================================
//PROGRAMA                   : sticker_estiba.php
//AUTOR                      : Jonatan Lopez Aguirre.
//FECHA CREACION             : 
//FECHA ULTIMA ACTUALIZACION : Noviembre 06 de 2012

//DESCRIPCION
//========================================================================================================================================\\
//========================================================================================================================================\\
// Programa que permite imprimir uno varios stickers para las estibas de servicios farmaceuticos.
// 
//Modificaciones 16 Mayo 2012:
//Se modifica la etiqueta para que imprima en una mas grande y en la parte superior de la misma, ademas se actualiza la interfaz de usuario.
//========================================================================================================================================\\	



		                                         // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
	$wactualiz="(Noviembre 06 de 2012)";                     // Aca se coloca la ultima fecha de actualizacion de este programa //
                                             // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //		

	echo "<form action='sticker_estiba.php' name='estibas' method=post>";
	encabezado("STICKER DE ARTICULOS PARA LAS ESTIBAS DEL ALMACEN GENERAL",$wactualiz, "clinica");
	echo "<center><table border=0>";
	
	echo "<tr class='fila1'><td><font size='3'>Centro de Costos</font></td><td align=center><input type='TEXT' id='wcco' name='wcco' size=4 maxlength=4></td></tr>";
	echo "<tr class='fila1'><td><font size='3'>&Aacute;rticulo Inicial</font></td><td align=center><input type='TEXT' id='wart1' name='wart1' size=7 maxlength=7></td></tr>";
	echo "<tr class='fila1'><td><font size='3'>&Aacute;rticulo Final</font></td><td align=center><input type='TEXT' id='wart2' name='wart2' size=7 maxlength=7></td></tr>";
	echo "<tr class='fila1'><td><font size='3'>Ubicaci&oacute;n Inicial</font></td><td align=center>";
	echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' VALUE='".$wemp_pmla."'>";	
	$query = "select ubicod,ubinom from ivubi ORDER BY ubicod ";
	$err = odbc_do($conex_o,$query);
	$campos= odbc_num_fields($err);

	echo "<select id='wubi1' name='wubi1'>";	
		
		while(odbc_fetch_row($err))
		{
			$row=array();
			for($i=1;$i<=$campos;$i++)
			{
				$row[$i-1]=odbc_result($err,$i);
			}
			echo "<option>".$row[0]."-".$row[1]."</option>";
		}
	echo "</select>";

	echo "</td></tr>";

	echo "<tr class='fila1'><td><font size='3'>Ubicaci&oacute;n Final</font></td><td align=center>";

	$query = "select ubicod,ubinom from ivubi ORDER BY ubicod ";
	$err = odbc_do($conex_o,$query);
	$campos= odbc_num_fields($err);

	echo "<select id='wubi2' name='wubi2'>";

	while(odbc_fetch_row($err))
	{
		$row=array();
		for($i=1;$i<=$campos;$i++)
		{
			$row[$i-1]=odbc_result($err,$i);
		}
		echo "<option>".$row[0]."-".$row[1]."</option>";
	}

	echo "</select>";
	echo "</td></tr>";

	echo "<tr class='fila1'><td><font size='3'>N&uacute;mero de Sticker</font></td><td align=center><input type='TEXT' id='wnum' name='wnum' size=3 maxlength=3></td></tr>";
	echo "<tr class='fila1'><td><font size='3'>IP Impresora</font></td>";

	 $wipimpresora = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ImpresoraStickersDispen'); // Extrae la ip de dispensacion

	 //Se consultan todas las impresoras pero queda seleccionada especialmente la de dispensacion.
	 $sql = "SELECT	Descripcion, Ip
	    	  FROM  ".$wbasedato."_000037
			 WHERE  activo = 'on'";				
	  $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	  echo "<td class='fila2'>";
	  echo "<select id='wip' name='wip'>";
		
	  for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ )
	    {		

	    	if( $rows['Ip'] == $wipimpresora )
	    	{
				echo "<option value='".$rows['Ip']."' selected>{$rows['Descripcion']}</option>";
			}
			else
			{
				echo "<option value='".$rows['Ip']."'>{$rows['Descripcion']}</option>";
			}
		}
		
	
    echo "</select></tr>";
	echo "<tr class='fila2'><td align=center colspan=2><input type='button' id='btn_imprimir' value='IMPRIMIR' onclick='imprimirstickers();'></td></tr></table>";
	echo "<div id='div_resultado_comparacion' style='text-align:left;'></div>";
      
	?>
</body>
</html>