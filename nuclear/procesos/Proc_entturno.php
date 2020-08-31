<html>
<head>
<title>Entrega de Turnos - Nuclear</title>
<link href="/matrix/root/tavo.css" rel="stylesheet" type="text/css" />
</head>
<font face='arial'>

<script type="text/javascript">
	function enter()
	{
		document.forms.proc_inovexa.submit();
	}
</script>


<?php
include_once("conex.php");

//<body TEXT="#000066" BGCOLOR="ffffff" oncontextmenu = "return false" onselectstart = "return false" ondragstart = "return false">
//<body TEXT="#000066" BGCOLOR="ffffff" oncontextmenu = "return false" onselectstart = "return false" ondragstart = "return false">
//Esta instrucción sirve para apagar el mouse y que no deje hacer nada en la pagina, para que no copie alguna imagen etc etc.

/*******************************************************************************************************************************************
*                                    PROCESO PARA INGRESAR NOVEDADES DE EXAMENES DE NUCLEAR                                                *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				     : Proceso De Entrega de Turnos.                                                                                |
//AUTOR				         : Ing. Gustavo Alberto Avendano Rivera.                                                                        |
//FECHA CREACION			 : OCTUBRE 2 DE 2007.                                                                                           |
//FECHA ULTIMA ACTUALIZACION : 02 de Octubre de 2007.                                                                                       |
//DESCRIPCION			     :Este sirve para ingresar las entregas de turnos de nuclear.                                                   |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//nuclear_000006    : Tabla de Entrega de Turnos.                                                                                           |
//==========================================================================================================================================
$wactualiz="Ver. 2007-10-03";

session_start();
if(!isset($_SESSION['user']))
 {
  echo "error";
 }
else
{
 $empresa='root';

 

 

 
 echo "<center><table border=1>";
 echo "<tr><td align=center colspan=2 bgcolor=#006699><font size=5 text color=#FFFFFF><b> ENTREGA DE TURNOS </b></font><br><font size=2 text color=#FFFFFF><b>".$wactualiz."</b></font></td></tr>";

if (!isset($code) or $code == '' or $fec1 == '' or $hora1 == '' or !isset($code1) or $code1 == '' or $fec2 == '' or $hora2 == '' )
  {
  echo "<form name='proc_entturno' action='' method=post>";

  ///////////////////////////////////////////////////////////////////////////////////////// seleccion el empleado de acuerdo al centro de costos.
  echo "<td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=3> Codigo - Empleado: <br></font></b><select name='code' >";

   $query = " SELECT codigo,descripcion "
	        ."  FROM usuarios "
	        ." WHERE Ccostos = '".$wcco."' ";
	        

   $err4 = mysql_query($query,$conex);
   $num4 = mysql_num_rows($err4);
   $cod  = explode('-',$code);
   
   $codigo=$cod[0];
   
   if ($codigo == "")
    { 
   	 echo "<option></option>";
   	 $codigo = "";
    }
   else 
    {
     echo "<option>".$cod[0]."-".$cod[1]."</option>";
    }  

   for ($i=1;$i<=$num4;$i++)
	{
	 $row4 = mysql_fetch_array($err4);
	 echo "<option>".$row4[0]."-".$row4[1]."</option>";
	}
   
   echo "</select></td></tr>";
  
   $hoy=date("Y-m-d");
   $hora=(string)date("H:i:s");
   
   if (isset($fec1))
    {
     $fec1=$fec1;
    }
   else
    {
   	 $fec1=$hoy;
    }

   if (isset($hora1))
    {
     $hora1=$hora1;
    }
   else
    {
   	 $hora1=$hora;
    }
    
   echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=3> <i>Fecha Entrega&nbsp(AAAA-MM-DD):</font></b><INPUT TYPE='text' NAME='fec1' VALUE='".$fec1."'>
	    <b><font text color=#003366 size=3> <i>Hora Entrega&nbsp<i>(HH:MM:SS):</font></b><INPUT TYPE='text' NAME='hora1' VALUE='".$hora1."'></td></tr>";
    
   if (isset($nove))
    {
     $nove=$nove;
    }
   else
    {
   	 $nove='';
    }
    
    
 /////////////////////////////////////////////////////////////////////////////////////// seleccion para las complicaciones.
  echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3> Observaciones - Novedades </br><textarea name='nove' cols='90' rows='4'>".$nove."</textarea></td></tr>";

  
  ///////////////////////////////////////////////////////////// seleccion los empleados deacuerdo al centro de costos.
  echo "<td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3> Codigo - Empleado: <br></font></b><select name='code1' >";

  $query5 = " SELECT codigo,descripcion "
	       ."   FROM usuarios "
	       ."  WHERE ccostos = '".$wcco."' ";
	        
  $err5 = mysql_query($query5,$conex);
  $num5 = mysql_num_rows($err5);

  $cod1 = explode('-',$code1);
  $codigo1 = $cod1[0];
   
   
  if ($codigo1 == "")
   { 
   	echo "<option></option>";
  	$codigo1 = "";
   }
  else 
   {
    echo "<option>".$cod1[0]."-".$cod1[1]."</option>";
   }  

  for ($i=1;$i<=$num5;$i++)
   {
	$row5 = mysql_fetch_array($err5);
	echo "<option>".$row5[0]."-".$row5[1]."</option>";
   }
  echo "</select></td></tr>";
    
  if (isset($fec2))
   {
	$fec2=$fec2;
   }
  else
   {
    $fec2=$hoy;
   }

  if (isset($hora2))
   {
    $hora2=$hora2;
   }
  else
   {
  	$hora2=$hora;
   }

  echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=3> <i>Fecha Recibe&nbsp(AAAA-MM-DD):</font></b><INPUT TYPE='text' NAME='fec2' VALUE='".$fec2."'>
	    <b><font text color=#003366 size=3> <i>Hora Recibe&nbsp<i>(HH:MM:SS):</font></b><INPUT TYPE='text' NAME='hora2' VALUE='".$hora2."'></td></tr>";
   
    
 /////////////////////////////////////////////////////////////////////////////////////// Boton de OK o Grabar.
  echo "<tr><td align=center bgcolor=#cccccc colspan=2><input type='submit' value='Grabar'></td></tr>";          //submit osea el boton de OK o Aceptar  }

  }
  else // Cuando ya estan todos los datos escogidos
  {
   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA EL INSERT A LA TABLA nuclear_000005 
  
   $nove1= strtoupper($nove);
   $cod  = explode('-',$code);
   $cod1 = explode('-',$code1);
    
   $query1 =" INSERT INTO nuclear_000006 (   Medico,   Fecha_data       ,                   Hora_data,       Codent,        Noment,       Fecent,        Horent,       Noveda,          Codrec,        Nomrec,     fecrec,      horrec,      seguridad)"
           ."                     VALUES ('Nuclear', '".date('Y-m-d')."', '".(string)date("H:i:s")."','".$cod[0]."', '".$cod[1]."', '".$fec1."' , '".$hora1."' , '".$nove1."', '".$cod1[0]."' ,'".$cod1[1]."','".$fec2."','".$hora2."','C-".$cod[0]."')";
         
          
  //echo $query1;
  
   $err1 = mysql_query($query1,$conex);

   if ($err1)
    {
     echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#03FFFF size=3>Se Grabo Con Exito<br></font></b></td></tr>";

    } 
   else 
    {
     echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#03FFFF size=3>ERROR al insertar en la tabla de turnos<br></font></b></td></tr>";
 
    }
       
  }// cierre del else donde empieza la impresión

}

?>