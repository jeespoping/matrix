<html>
<head>
<title>MATRIX - [HOJA DE VIDA]</title>

</head>

<?php
include_once("conex.php");

session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
  $key = substr($user,2,strlen($user));
  
  include_once("root/comun.php");
  
  $conex = obtenerConexionBD("matrix");
  
  


  if(strlen($key) > 5)
	 $codigo=substr($key,strlen($key)-5);
  else
	 $codigo=$key;
	 
 //Forma
 echo "<form name='linkcolilla' action='' method=post>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$key."'/>";

 $query = " SELECT concat(subcodigo,'-',descripcion)"
           ."   FROM det_selecciones"
           ."  WHERE medico LIKE 'nomina'" 
           ."    AND codigo LIKE '01'"
           ."    AND activo =  'A'";
           
   $err3 = mysql_query($query,$conex);
   $num3 = mysql_num_rows($err3);
   $tpp  = mysql_fetch_array($err3);
   
  $query="SELECT distinct hvcodnom  
	  	    FROM nomina_000004
		   WHERE hvcodnom='".$codigo."' 
		     AND hvempresa='".$tpp[0]."'
		     AND hvacti='on'"; 
  //echo $query."<br>";
  $err = mysql_query($query,$conex);
  $num = mysql_num_rows($err);
  //echo mysql_errno() ."=". mysql_error();
  if ($num==0)
  {
   //echo "<script>document.location.href = 'http://200.24.5.118/matrix/det_registro.php?id=0&pos1=nomina&pos2=0&pos3=0&pos4=000004&pos5=0&pos6=nomina&tipo=P&Valor=&Form=000004-nomina-Hoja de vida&call=1&change=0&key=nomina&Pagina=1' ;</script>";
  
  if (!isset($pp) or $pp=='-' )
  { 
	 
   //Cuerpo de la pagina
   echo "<table align='center' border=0>";
  	
   echo "<tr>";
   echo "<td align=left colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>";
   echo "Antes de ver su colilla favor llenar este  formulario  de  HOJA  DE  VIDA: ";
   echo "</td>";
   echo "</tr>";   
   echo "<tr>";
   echo "<td align=left colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>";   
   echo "- Primero, ingrese la empresa donde labora. ";
   echo "</td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=left colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>";   
   echo "- Segundo, haga click en el botón Empresa-Nomina. ";
   echo "</td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=left colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>";
   echo "- Tercero, aperece el formulario de hoja de vida. ";
   echo "<tr>";
   echo "<td align=left colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>";
   echo "- Cuarto, ingrese su número de cédula y la ciudad de expedición, luego se da enter.";
   echo "</td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=left colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>";
   echo "- Quinto, los datos consultados  son  los  que  estan  en nómina, favor corregir e ingresar los nuevos datos.";
   echo "</td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=left colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>";
   echo "- Sexto, no ingresar caracteres especiales tales como ´,ñ,*,!,#,$ ... etc.";
   echo "</td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=left colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>";
   echo "- Septimo, al final se da click en el campo datos completos y luego el botón grabar.";
   echo "</td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=left colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>";
   echo "- Por último, Si los datos estan OK , le das click en el botón X.";
   echo "</td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=left colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>";
   echo " CUALQUIER INQUIETUD LLAMAR A LA EXT 1285, OLGA CORREA";
   echo "</td>";
   echo "</tr>";
   echo "</table>"; 
   echo "<br>";
   echo "<br>";
   echo "<br>";
   echo "<br>";
   
   
   echo "<table align='center' border=0>";
   /////////////////////////////////////////////////////////////////////////// seleccion para las empresas - nomina
   echo "<td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Empresas - Nomina:</B><br></font></b><select name='pp' id='searchinput'>";

   $query = " SELECT concat(subcodigo,'-',descripcion)"
           ."   FROM det_selecciones"
           ."  WHERE medico LIKE 'nomina'" 
           ."    AND codigo LIKE '01'"
           ."    AND activo =  'A'";
           
   $err3 = mysql_query($query,$conex);
   $num3 = mysql_num_rows($err3);
   $tpp=$pp;
   
   //echo "<option>".$tpp[0]."</option>";
   
   for ($i=1;$i<=$num3;$i++)
   {
	$row3 = mysql_fetch_array($err3);
	echo "<option>".$row3[0]."</option>";
   }
	
   echo "</select></td>";	 
   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	 

   echo "<br>";
   echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='EMPRESA-NOMINA'></td></tr>"; //submit osea el boton de OK o Aceptar
   echo "</table>";
   echo '</div>';
   echo '</div>';
   
   }
   else
   {
    $tpp=$pp;
   
    $query="SELECT distinct hvcodnom  
	    	  FROM nomina_000004
		     WHERE hvcodnom='".$codigo."' 
		       AND hvempresa='".$tpp."'
		       AND hvacti='on'"; 
  //echo $query."<br>";
    $err = mysql_query($query,$conex);
    $num = mysql_num_rows($err);
  //echo mysql_errno() ."=". mysql_error();
    if ($num==0)
    {
     echo "<script>document.location.href = 'http://200.24.5.118/matrix/det_registro.php?id=0&pos1=nomina&pos2=0&pos3=0&pos4=000004&pos5=0&pos6=nomina&tipo=P&Valor=&Form=000004-nomina-Hoja de vida&call=1&change=0&key=nomina&Pagina=1' ;</script>";
    }
    else
    {
     echo "<script>document.location.href = '000001_rep3.php';</script>";
    }
   }
  	
  }
  else
  {
   echo "<script>document.location.href = '000001_rep3.php';</script>";
  }
 }

?>