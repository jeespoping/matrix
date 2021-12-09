<html>
<head>
<title>Ingreso de Novedades - Examenes Especiales</title>
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
//Esta instrucci�n sirve para apagar el mouse y que no deje hacer nada en la pagina, para que no copie alguna imagen etc etc.

/*******************************************************************************************************************************************
*                                    PROCESO PARA INGRESAR NOVEDADES DE EXAMENES DE NUCLEAR                                                *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				     : Proceso Novedades Examenes Especiales.                                                                       |
//AUTOR				         : Ing. Gustavo Alberto Avendano Rivera.                                                                        |
//FECHA CREACION			 : SEPTIEMBRE 27 DE 2007.                                                                                       |
//FECHA ULTIMA ACTUALIZACION : 27 de Septiembre de 2007.                                                                                    |
//DESCRIPCION			     :Este sirve para ingresar las novedades de complicaciones,dosis 1 metro y contaminacion que tienen los examenes|
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//amenuclear        : Tabla de Ingreso de novedades de ayudas de nuclear.                                                                   |
//==========================================================================================================================================
$wactualiz="Ver. 2007-09-27";

session_start();
if(!isset($_SESSION['user']))
 {
  echo "error";
 }
else
{
 $empresa='root';

 

 

 
 echo "<center><table border=1>";
 echo "<tr><td align=center colspan=2 bgcolor=#006699><font size=5 text color=#FFFFFF><b> NOVEDADES EXAMENES ESPECIALES - MEDICINA NUCLEAR </b></font><br><font size=2 text color=#FFFFFF><b>".$wactualiz."</b></font></td></tr>";

 
if (!isset($fte) or $fte == '' or !isset($doc) or $doc == '' )
  {
  echo "<form name='proc_inovexa' action='' method=post>";

  if (isset($fte))
    {
     $fte=$fte;
    }
   else
    {
   	 $fte='';
    }
    
   if (isset($doc))
    {
     $doc=$doc;
    }
   else
    {
   	 $doc='';
    }
    
    if (isset($compli))
    {
     $compli=$compli;
    }
   else
    {
   	 $compli='';
    }
    
    if (isset($dosis))
    {
     $dosis=$dosis;
    }
   else
    {
   	 $dosis='';
    }
    
   if (isset($conta))
    {
     $conta=$conta;
    }
   else
    {
   	 $conta='';
    }
    
 /////////////////////////////////////////////////////////////////////////////////////// seleccion para la Fuente y el Ingreso.
  echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=3> <i>Fuente :</font></b><INPUT TYPE='text' NAME='fte' VALUE='".$fte."'>
	    <b><font text color=#003366 size=3> <i>Nro Ingreso<i> :</font></b><INPUT TYPE='text' NAME='doc' VALUE='".$doc."'></td></tr>";

 /////////////////////////////////////////////////////////////////////////////////////// seleccion para las complicaciones.
  echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3> Complicaciones: </br><textarea name='compli' cols='90' rows='4'>".$compli."</textarea></td></tr>";
   
 /////////////////////////////////////////////////////////////////////////////////////// seleccion para las complicaciones.
  echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3> Dosis 1 Metro (Mr/Hora): </br><textarea name='dosis' cols='90' rows='4'>".$dosis."</textarea></td></tr>";
   
 /////////////////////////////////////////////////////////////////////////////////////// seleccion para las complicaciones.
  echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3> Contaminaci�n : </br><textarea name='conta' cols='90' rows='4'>".$conta."</textarea></td></tr>";

 /////////////////////////////////////////////////////////////////////////////////////// Boton de OK o Grabar.
  echo "<tr><td align=center bgcolor=#cccccc colspan=2><input type='submit' value='Grabar'></td></tr>";          //submit osea el boton de OK o Aceptar  }
  }
  else // Cuando ya estan todos los datos escogidos
  {
   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
  
   
   $conexi=odbc_connect('facturacion','','')
	    or die("No se realizo conexi�n con la BD Facturaci�n");

   $fte1=strtoupper($fte);
   $compli1=strtoupper($compli);
   $dosis1=strtoupper($dosis);
   $conta1=strtoupper($conta);
	    
   $query1 =" INSERT INTO amenuclear (fuenuc,ingnuc,comnuc,dosnuc,connuc)"
          ."  VALUES ('".$fte1."','".$doc."','".$compli1."','".$dosis1."','".$conta1."') ";
          
  //echo $quer1;
  //echo odbc_error() ."=". odbc_errormsg();
 
  
   $err1 = odbc_do($conexi,$query1);

   if ($err1)
    {
     echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#03FFFF size=3>Se hizo el insert con exito<br></font></b></td></tr>";
     echo "<tr><td align=center color:#000066 font-size:12pt font-family:Courier New font-weight:bold ><A HREF='//clinica.pmamericas.com/matrix/nuclear/procesos/proc_inovexa.php'>Insertar Mas</a></td></tr>";
     //echo "<tr><td align=center color:#000066 font-size:12pt font-family:Courier New font-weight:bold ><A HREF='http://132.1.20.246/matrix/nuclear/procesos/proc_inovexa.php'>Insertar Mas</a></td></tr>";

    } 
   else 
    {
     echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#03FFFF size=3>ERROR en el insert, ya existe o no hay UNIX<br></font></b></td></tr>";
     echo "<tr><td align=center color:#000066 font-size:12pt font-family:Courier New font-weight:bold ><A HREF='//clinica.pmamericas.com/matrix/nuclear/procesos/proc_inovexa.php'>Insertar Mas</a></td></tr>";
   	 //echo "<tr><td align=center color:#000066 font-size:12pt font-family:Courier New font-weight:bold ><A HREF='http://132.1.20.246/matrix/nuclear/procesos/proc_inovexa.php'>Insertar Mas</a></td></tr>";
 
    }
       
	odbc_close($conexi);
	odbc_close_all();

 }// cierre del else donde empieza la impresi�n

}
?>