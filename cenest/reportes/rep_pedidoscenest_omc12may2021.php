<html>
<head>
<title>MATRIX - [REPORTE PEDIDOS A LA CENTRAL DE ESTERILIZACION]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_pedidoscenest.php'; 
	}
	
	function enter()

	{
	 document.forms.rep_pedidoscenest.submit();
	}
	
	function cerrarVentana()
	{
	 window.close()
	}
	
	function VolverAtras()
	{
	 history.back(1)
	}
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             REPORTE PEDIDOS A LA CENTRAL DE ESTERILIZACION                                               *
*******************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : REPORTE PEDIDOS A LA CENTRAL DE ESTERILIZACION                                                              |
//AUTOR				          : Ing. Gabriel Agudelo.                                                                                       |
//FECHA CREACION			  : Noviembre 13 de 2019.                                                                                       |
//FECHA ULTIMA ACTUALIZACION  : Noviembre 13 de 2019.                                                                                       |
//DESCRIPCION			      : Trae las cantidades solicitadas y despachadas por parte de la central y centro de costos                    |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//root_000040                 : Requerimientos                                                                                              |
//cenmat_000001               : Maestro de Articulos de la central                                                                          | 
//cenmat_000002               : Maestro de Metodos de esterilizacion                                                                        |                                                                                                                                          |
//cenmat_000004               : Movimientos de solicitados y despachados                                                                    |                                                                                                                                          |
//==========================================================================================================================================

include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="Noviembre 13 de 2019.";

$usuarioValidado = true;

if (!isset($user) || !isset($_SESSION['user'])){
	$usuarioValidado = false;
}else {
	if (strpos($user, "-") > 0)
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}
if(empty($wuser) || $wuser == ""){
	$usuarioValidado = false;
}

session_start();
//Encabezado
encabezado("REGISTRO DE PEDIDOS A LA CENTRAL DE ESTERILIZACION",$wactualiz,"clinica");

if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{
	
 $empre1='cenest';
 //Conexion base de datos
 
 //Forma
 echo "<form name='forma' action='rep_pedidoscenest.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 echo "<input type='HIDDEN' NAME= 'tabla' value='".$tabla."'/>";
 
if (!isset($fec1) or $fec1 == '' or !isset($fec2) or $fec2 == '')
  {
  	echo "<form name='rep_pedidoscenest' action='' method=post>";
  
	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";

	//Ingreso de fecha de consulta
 	echo '<span class="subtituloPagina2">';
 	echo 'Ingrese los parámetros de consulta';
 	echo "</span>";
 	echo "<br>";
 	echo "<br>";

 	//Fecha inicial
 	echo "<tr>";
 	echo "<td class='fila1' width=190>Fecha Inicial</td>";
 	echo "<td class='fila2' align='center' width=150>";
 	campoFecha("fec1");
 	echo "</td></tr>";
 		
 	//Fecha final
 	echo "<tr>";
 	echo "<td class='fila1'>Fecha Final</td>";
 	echo "<td class='fila2' align='center'>";
 	campoFecha("fec2");
 	echo "</td></tr>";
 
    ///Clasificacion
	echo "<tr>";
 	echo "<td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Clasificacion:</B></font></td>";
	echo "</tr>";
	echo "<tr>";
 	echo "<td align=Left colspan=2 bgcolor=#DDDDDD><INPUT TYPE='RADIO' NAME='clas' VALUE='1' CHECKED>Todos<br>";
	echo "<INPUT TYPE='RADIO' NAME='clas' VALUE='2'>Material Medico Quirurgico y Ropa<br>";
	echo "<INPUT TYPE='RADIO' NAME='clas' VALUE='3'>Solicitud Instrumental y Equipos</td>";
 	echo "</td></tr>";
  
   /////////////////////////////////////////////////////////////////////////// seleccion para los Responsables
    echo "<td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Ccostos Solicita:</B><br></font></b><select name='pp' id='searchinput'>";

  	$query = " SELECT DISTINCT r40.Reqccs,c5.Cconom,SUBSTRING(r40.Reqccs,5,8) 
               FROM   root_000040 r40
						left join 
						costosyp_000005 c5 on (SUBSTRING(r40.Reqccs,5,8) = c5.Ccocod ) 
               WHERE Reqcco = '(01)1082' 
				AND   Reqtip = '13' 
				AND   (Reqcla='42' OR Reqcla='43')
				AND   Reqest='05'
				AND   Reqccs !=''  ";
           
           
    $err3 = mysql_query($query,$conex);
    $num3 = mysql_num_rows($err3);
    $tpp=$pp;
   
    if (!isset($pp))
    { 
     echo "<option></option>";
    }
    else 
    {
     echo "<option>".$tpp[0]."-".$tpp[1]."</option>";
    } 
   
    for ($i=1;$i<=$num3;$i++)
	 {
	 $row3 = mysql_fetch_array($err3);
	 echo "<option>".$row3[0]."-".$row3[1]."</option>";
	 }
	echo "<option>TODOS</option>";
    echo "</select></td>";
 	
   echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='OK'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
   echo '</div>';
   echo '</div>';
   echo '</div>';
   
  }
 else // Cuando ya estan todos los datos escogidos
  {
	$tpp=explode('-',$pp); 
	// Abro el archivo
   	   $archivo = fopen("rep_pedidoscenest.txt","w"); 
	
	if ($clas == 1){
		$clas1 = "%";  //Todos
	    $clas2 = "Todas Las Clasificaciones";
	}
     if ($clas == 2){
		$clas1 = "42";  //Material Medico Quirurgico y Ropa
		$clas2 = "42 - Material Medico Quirurgico y Ropa";
	 }
	 if ($clas == 3){
		$clas1 = "43";  //Solicitud Instrumental y Equipos
		$clas2 = "43 - Solicitud Instrumental y Equipos";
	 }
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
    echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='300'>";  //border=0 no muestra la cuadricula en 1 si.
    echo "<tr>";
    echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>REPORTE DE PEDIDOS A LA CENTRAL DE ESTERILIZACION</b></font></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
    echo "</tr>";
	echo "<tr>";
    echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>CLASIFICACION: <i>".$clas2."</i></b></font></b></font></td>";
    echo "</tr>";
	echo "<tr><br><td></td></tr>";
    echo "</table>";
	echo "<br>";
    echo "<table border=1 cellspacing=0 cellpadding=0 align=center size='300'>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Fecha</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Numero Requerimiento</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Descripcion</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Observacion</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Recibe a Satisfaccion</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Ccostos Solicita</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Clasificacion</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Cod Producto</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Descripcion</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Cantidad Solicitada</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Cantidad Despachada</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Cod Metodo</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Descripcion</b></td>";
	echo "</tr>";
    
  	 
	 IF ($tpp[0]=="TODOS")
    {
				
		$query = " select r40.Reqfec,r40.Reqnum,r40.Reqdes,r40.Reqobe,r40.Reqsat,r40.Reqccs,r40.Reqcla,c4.Reqpro,c1.Prodes,
		                  c4.Reqcas,c4.Reqcad,c4.Reqmet,c2.Metdes  
				   from   root_000040 r40,cenmat_000004 c4,cenmat_000001 c1,cenmat_000002 c2  
				   where  r40.Reqcco = '(01)1082' 
				     and  r40.Reqfec between '".$fec1."' and '".$fec2."' 
				     and  r40.Reqcla like '".$clas1."' 
					 and  r40.Reqest = '05' 
					 and  r40.Reqnum = c4.Reqnum 
					 and  r40.Reqcla = c4.Reqcla 
					 and  c4.Reqcla = c1.Procla 
					 and  c4.Reqpro = c1.Procod 
					 and  c4.Reqmet = c2.Metcod 
					order by r40.Reqccs,r40.Reqnum ";
    }
	ELSE
	{
		$query = " select r40.Reqfec,r40.Reqnum,r40.Reqdes,r40.Reqobe,r40.Reqsat,r40.Reqccs,r40.Reqcla,c4.Reqpro,c1.Prodes,
		                  c4.Reqcas,c4.Reqcad,c4.Reqmet,c2.Metdes  
				   from   root_000040 r40,cenmat_000004 c4,cenmat_000001 c1,cenmat_000002 c2  
				   where  r40.Reqcco = '(01)1082' 
				     and  r40.Reqfec between '".$fec1."' and '".$fec2."' 
				     and  r40.Reqcla in ('42','43') 
					 and  r40.Reqccs like '%".$tpp[0]."%'  
					 and  r40.Reqest = '05' 
					 and  r40.Reqnum = c4.Reqnum 
					 and  r40.Reqcla = c4.Reqcla 
					 and  c4.Reqcla = c1.Procla 
					 and  c4.Reqpro = c1.Procod 
					 and  c4.Reqmet = c2.Metcod 
					order by r40.Reqccs,r40.Reqnum ";
		
	}
    $err1 = mysql_query($query,$conex);
    $num1 = mysql_num_rows($err1);
	 
	echo "<li><A href='rep_pedidoscenest.txt'>Presione Clic Derecho Para Bajar El Archivo ...</A>";
    echo "<br>";
    echo "<li>Registros generados: ".$num1;
    
	// Detalle o titulos de los campos de la tabla
	   fwrite($archivo, "Fecha|Numero Requerimiento|Descripcion|Observacion|Recibe a Satisfaccion|Ccostos|Clasificacion|Cod Producto|Descripcion|Cantidad Solicitada|Cantidad Despachada|Metodo|Descripcion|" ); 
	   fwrite($archivo, chr(13).chr(10) );   
	
    $swtitulo='SI';
      
	for ($i=1;$i<=$num1;$i++)
	{
		 if (is_int ($i/2))
		  {
		  // $wcf="DDDDDD";  // color de fondo
		   $wcf="EFF8FB";  // color de fondo
		  }
		 else
		  {
		   //$wcf="CCFFFF"; // color de fondo 
		   $wcf="A9E2F3"; // color de fondo
		  }

		$row1 = mysql_fetch_array($err1);
	   
   		    	  
	  echo "<Tr  bgcolor=".$wcf.">";
      echo "<td  align=center><font size=1>$row1[0]</font></td>";
      echo "<td  align=center><font size=1>$row1[1]</font></td>";
      echo "<td  align=center><font size=1>$row1[2]</font></td>";
	  echo "<td  align=center><font size=1>$row1[3]</font></td>";
      echo "<td  align=center><font size=1>$row1[4]</font></td>";
      echo "<td  align=center><font size=1>$row1[5]</font></td>";
	  echo "<td  align=center><font size=1>$row1[6]</font></td>";
      echo "<td  align=center><font size=1>$row1[7]</font></td>";
      echo "<td  align=center><font size=1>$row1[8]</font></td>";
	  echo "<td  align=center><font size=1>$row1[9]</font></td>";
      echo "<td  align=center><font size=1>$row1[10]</font></td>";
      echo "<td  align=center><font size=1>$row1[11]</font></td>";
	  echo "<td  align=center><font size=1>$row1[12]</font></td>";
	  echo "</tr>";
	  
	  $LineaDatos = "";
	  for ($j = 0; $j <= 12; $j++)
		  {
			$row1[$j]= str_replace("|", ' ',$row1[$j]);
			$LineaDatos=$LineaDatos.$row1[$j]."|";
			$lineadatos = str_replace(chr(13).chr(10) , ' ',$lineadatos); 
		    $lineadatos = str_replace("\n", ' ', $lineadatos);
		  }
	  fwrite($archivo,$LineaDatos.chr(13).chr(10) );
	}
   echo "</table>"; 
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   fclose($archivo);
	echo "<li><A href='rep_transcardio.txt'>Presione Clic Derecho Para Bajar El Archivo ...</A>";
	echo "<br>";
	echo "<li>Registros generados: ".$num1;
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
  } // cierre del else donde empieza la impresión

}
?>