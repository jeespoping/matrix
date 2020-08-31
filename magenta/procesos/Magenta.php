<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>Cliente Vital</title>
	
	     <script type="text/javascript">
	     function hacerFoco()
	     {
	     	if (document.magenta.elements[0].value=='')
	     	{
	     		document.magenta.elements[0].focus();
	     	}
	     	else
	     	{
	     		document.producto.elements[3].focus();
	     	}
	     }

    </script>
</head>

<?php
include_once("conex.php");

/**
 * PORTAL INICIAL DE AFINIDAD
 * 
 * Este programa es el ingreso a la aplicación PRINCIPAL de magenta, la que permite identificar y 
 * actualizar un paciente que esta clasificado, esta aplicacion contiene los siguientes script php:
 * magenta.php
 * persona.php
 * actualizar.php
 * procesoActualizar.php (include)
 * comentarios.php
 * busqueda_info.php (include)
 *
 * Este programa Pinta una tabla que solicita el documento de identidad (doc) y el tipo de documento 
 * Tras el ingreso de documento de identidad direcciona vuelve a si misma con (doc) set y redirecciona a dos frames:
 * persona.php
 * comentarios.php
 * 
 * @name matrix\Magenta\procesos\magenta.php
 * @author Ing. Ana María Betancur Vargas
 * @created 2006-01-07
 * @version 2007-01-19
 * 
 * @modified 2006-01-11  Documentacion, Carolina Castaño
 * @modified 2007-01-19  Documentacion, Carolina Castaño
 * 
 * 
 * @wvar $doc, documento del paciente
 * @wvar $tipDoc, tipo de documento del paciente
 * 
 */
/************************************************************************************************************************* 
  Actualizaciones:
 			2016-05-02 (Arleyda Insignares C.)
						-Se cambia encabezado con ultimo formato.
*************************************************************************************************************************/
$wautor="Ana Maria Betancur";
$wmodificado="Carolina Castaño";
$wversion='2007-01-19';
$wactualiz='2016-05-06';

/*************************************************************************************************************************/

session_start();
if(!isset($_SESSION['user']))
    echo "error";
else
{		
 	include_once("root/comun.php"); 	
	/**
    * funciones de afinidad
    */
	include_once("magenta/incVisitas.php");
	
	

	
	



	if (isset($cco) and $cco!='')
	{
		$exp=explode('.', $cco);
		if(isset($exp[1]))
		{
			$cco=$exp[1];
		}

		$q= " SELECT Cconom "
		."       FROM costosyp_000005 "
		."    WHERE Ccocod = '".$cco ."' ";

		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);
		if($num<1)
		{
			$cco='';
		}
	}
	
	if(!isset($doc) or !isset($cco) or $cco=='') //presento el portal inicial de magenta
	{
/*		echo "<table align='right'>" ;
		echo "<tr>" ;
		echo "<td><font color=\"#D02090\" size='2'>Autor: ".$wautor."</font></td>";
		echo "</tr>" ;
		echo "<tr>" ;
		echo "<td><font color=\"#D02090\" size='2'>Modificado por: ".$wmodificado."</font></td>" ;
		echo "</tr>" ;
		echo "<tr>" ;
		echo "<td><font color=\"#D02090\" size='2'>Version: ".$wversion."</font></td>" ;
		echo "</tr>" ;
		echo "<tr>" ;
		echo "<td><A href='index_magenta.htm' TARGET='_new'>AYUDA</A></font></td>" ;
		echo "</tr>" ;
		echo "</table></br></br></br>" ;*/

		if (isset($cco) and $cco=='')
		{
			?>
	
			<script>
			alert ("DEBE INGRESAR UN CENTRO DE COSTOS VALIDO");
         	</script>
			<?php
		}


		/*<body background="\matrix\images\medical\Magenta\medico_bebe.jpg"  >
		<body background="medico_bebe.jpg"  >
		<body background="/matrix/images/medical/Magenta/m.jpg"  >
		background="/matrix/images/medical/Magenta/m1.jpg"
		*/
		?>

<body onload="hacerFoco()">
</br>
	    <?php
			/////////////////////////////////////////////////encabezado general///////////////////////////////////
		    $titulo = "SISTEMA AAA";
		    // Se muestra el encabezado del programa
		    encabezado($titulo,$wactualiz, "clinica");  
	    ?>
   
		<form method="post" action="" name='magenta'>
  			<table width="613" height="492" border="0" background="/matrix/images/medical/Magenta/m4.jpg" align="center">
    		<tr> 
      			<td width="1%" height="10%">&nbsp;</td>
      			<td width="24%">&nbsp;</td>
      			<td width="46%">&nbsp;</td>
      			<td width="26%">&nbsp;</td>
      			<td width="3%">&nbsp;</td>
    		</tr>
    		<tr> 
      			<td height="26%">&nbsp;</td>
      			<td colspan="3"><font color="#003399" size="+2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Se&ntilde;or(a)<br>
        		Bienvenido a Clínica Las Américas </strong></font></td>
      			<td>&nbsp;</td>
    		</tr>
    		<tr> 
      			<td height="14%">&nbsp;</td>
      			<td colspan="3"><font color="#003399" size="+2" face="Verdana, Arial, Helvetica, sans-serif"><strong>
      			En qué le Podemos Servir...</strong></font></td>
      			<td>&nbsp;</td>
    		</tr>
    		<tr> 
      			<td height="14%">&nbsp;</td>
      			<td colspan="3"><font color="#003399" size="+2" face="Verdana, Arial, Helvetica, sans-serif"><strong>
      			Con mucho gusto...</strong></font></td>
      			<td>&nbsp;</td>
    		</tr>
    		<tr> 
      			<td height="14%">&nbsp;</td>
      			<td colspan="3"><font color="#003399" size="+2" face="Verdana, Arial, Helvetica, sans-serif"><strong>
      			Me permite su número de documento por favor</strong></font></td>
     			<td>&nbsp;</td>
    		</tr>
    		<tr> 
     			<td height="12%">&nbsp;</td>
      			<td><font size="+3"> 
        		<input type="text" name="doc">
        		</font></td>
      			<td><font size="+3"> 
        		<select name="tipDoc" style="font-face=arial">
         		 	<option selected>CC-CEDULA DE CIUDADANIA</option>
          			<option>TI-Tarjeta de Identidad</option>
          			<option>NU-Numero Unico de Identificación</option>
          			<option>CE-Cedula de Extrangeria</option>
          			<option>PA-Pasaporte</option>
          			<option>RC-Registro Civil</option>
          			<option>AS-Adulto Sin Identificación</option>
          			<option>MS-Menor Sin Identificación</option>
        		</select>
        		</font></td>
      			<td><font size="+3"> 
				<input type="hidden" name="ced" value="1">
				
				<?php


				//consulto el centro de  costos por defecto del usuario
				$pos = strpos($user,"-");
				$wusuario = substr($user,$pos+1,strlen($user)); //extraigo el codigo del usuario
				$q= " SELECT Ccostos, Cconom "
				."       FROM usuarios, costosyp_000005 "
				."    WHERE Codigo = '".$wusuario ."' "
				."       AND Activo = 'A' "
				."       AND Ccocod=Ccostos ";

				$res = mysql_query($q,$conex);
				$num = mysql_num_rows($res);
				$row = mysql_fetch_array($res);

				if ($row[0]=='' or $row[0]=='0999' )
				{
					$cco='';
					$nom='';
				}
				else
				{
					$cco=$row[0];
					$nom=$row[1];
				}
				echo "<tr>";
				echo '<td height="12%">&nbsp;</td>';
				echo '<td height="12%"><font color="#003399" face="Verdana, Arial, Helvetica, sans-serif">Centro de costos:</font></td>';
				echo '<td height="12%"><input type="password" name="cco" value='.$cco.' size="5">&nbsp;<font size=2 color="#003399">'.$nom.'</font></td>';
				echo '<td><font size="+3">';
				?>
				
        		<input type="submit" name="aceptar" value="ACEPTAR">
        		</font></td>
      			<td>&nbsp;</td>
    		</tr>
    		<tr> 
      			<td height="10%">&nbsp;</td>
      			<td>&nbsp;</td>
      			<td>&nbsp;</td>
      			<td>&nbsp;</td>
     			<td>&nbsp;</td>
    		</tr>
  			</table>
		</form>
</body>
<?php
	}
	else //si metieron el documento del paciente redirecciono a los frames
	{
		/*echo "<frameset cols=20%,80% frameborder=0 framespacing=2>";
		echo "<frame src='comentarios.php?doc=".$doc."&amp;tipDoc=".$tipDoc."' name='comentarios' marginwidth=0 marginheiht=0>";
		echo "<frameset rows='80,*' frameborder=0 framespacing=0>";
		echo "<frame src='persona.php?dos=".$doc."&amp;tipDoc=".$tipDoc."' name='titulos' marginwidth=0 marginheiht=0>";
		echo "<frame src='persona.php?dos=".$doc."&amp;tipDoc=".$tipDoc."' name='main' marginwidth=0 marginheiht=0>";*/
		echo "<frameset rows='*,150' frameborder='NO' border='0' framespacing='2'>";
		echo "<frame src='persona.php?doc=".$doc."&amp;tipDoc=".$tipDoc."&amp;cco=".$cco."' name='Persona'>";
		echo "<frameset cols='*,50' frameborder='NO' border='0' framespacing='0'> ";
		echo "<frame src='comentarios.php?doc=".$doc."&amp;tipDoc=".$tipDoc."&amp;cco=".$cco."'' name='bottomFrame' >";
		//echo "<frame src='familia.php?doc=".$doc."&amp;tipDoc=".$tipDoc."' name='rightFrame'>";
		echo "</frameset>";
		echo "</frameset>";
	}
}
?>
</html>
