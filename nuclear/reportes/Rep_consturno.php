<html>
<head>
<title>Consulta de Turnos - Nuclear</title>
<link href="/matrix/root/tavo.css" rel="stylesheet" type="text/css" />
</head>
<font face='arial'>

<script type="text/javascript">
	function enter()
	{
		document.forms.rep_consturno.submit();
	}
</script>


<?php
include_once("conex.php");

//<body TEXT="#000066" BGCOLOR="ffffff" oncontextmenu = "return false" onselectstart = "return false" ondragstart = "return false">
//<body TEXT="#000066" BGCOLOR="ffffff" oncontextmenu = "return false" onselectstart = "return false" ondragstart = "return false">
//Esta instrucción sirve para apagar el mouse y que no deje hacer nada en la pagina, para que no copie alguna imagen etc etc.

/*******************************************************************************************************************************************
*                                    REPORTE PARA CONSULTAR EL TURNO DE NUCLEAR                                                            *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				     : Consulta De Entrega de Turnos.                                                                               |
//AUTOR				         : Ing. Gustavo Alberto Avendano Rivera.                                                                        |
//FECHA CREACION			 : OCTUBRE 3 DE 2007.                                                                                           |
//FECHA ULTIMA ACTUALIZACION : 03 de Octubre de 2007.                                                                                       |
//DESCRIPCION			     : Este sirve para consultar turnos asignados.                                                                  |
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
 echo "<tr><td align=center colspan=2 bgcolor=#006699><font size=5 text color=#FFFFFF><b> CONSULTA DE TURNOS </b></font><br><font size=2 text color=#FFFFFF><b>".$wactualiz."</b></font></td></tr>";

if (!isset($code) or $code == '' or $fec1 == '' )
  {
  echo "<form name='rep_consturno' action='' method=post>";

  ///////////////////////////////////////////////////////////////////////////////////////// seleccion el empleado de acuerdo al centro de costos.
  echo "<td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=3> Codigo - Empleado Que Recibio Turno : <br></font></b><select name='code' >";

   $query = " SELECT codigo,descripcion "
	        ."  FROM usuarios "
	        ." WHERE Ccostos = '".$wcco."' ";
	        

   $err4 = mysql_query($query,$conex) or die("Ierr4 : ".mysql_error());
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
   
   if (isset($fec1))
    {
     $fec1=$fec1;
    }
   else
    {
   	 $fec1=$hoy;
    }
    
   echo "<tr><td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=3> <i>Fecha Recibio&nbsp(AAAA-MM-DD):</font></b><INPUT TYPE='text' NAME='fec1' VALUE='".$fec1."'></tr>";
	        
 /////////////////////////////////////////////////////////////////////////////////////// Boton de OK o Grabar, consultar etc.
  echo "<tr><td align=center bgcolor=#cccccc colspan=1><input type='submit' value='Consultar'></td></tr>";          //submit osea el boton de OK o Aceptar  }

  }
  else // Cuando ya estan todos los datos escogidos
  {
   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA EL INSERT A LA TABLA nuclear_000005 
  
   $cod  = explode('-',$code);
    
   echo "<tr><td align=center bgcolor=#006699><font text color=#FFFFFF><b>CODIGO</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF><b>NOMBRE QUIEN RECIBE</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF><b>FECHA RECIBE</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF><b>HORA RECIBE</b></font></td>" .
		"<td align=center bgcolor=#006699><font text color=#FFFFFF><b>NOVEDAD</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF><b>COD ENTREGA</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF><b>NOMBRE QUIEN ENTREGA</b></font></td></tr>";
   
   $query1 =" SELECT Codent, Noment, Noveda, Codrec, Nomrec,Fecrec,Horrec"
           ."   FROM nuclear_000006"
           ."  WHERE codrec = '".$cod[0]."'"
           ."    AND fecrec = '".$fec1."'";
           
  //echo $query1;
  
   $err1 = mysql_query($query1,$conex) or die("Imposible : ".mysql_error());
   $num1 = mysql_num_rows($err1);
   
   for ($i=1;$i<=$num1;$i++)
	{
	 if (is_int ($i/2))
	  {
	   $wcf="DDDDDD";  // color de fondo
	  }
	 else
	  {
	   $wcf="CCFFFF"; // color de fondo
	  }

	 $row2 = mysql_fetch_array($err1);
				 
	 echo "<tr  bgcolor=".$wcf."><td align=center>".$row2[3]."</td><td align=center>".$row2[4]."</td><td align=center>".$row2[5]."</td><td align=center>".$row2[6]."</td><td align=center>".$row2[2]."</td><td align=center>".$row2[0]."</td><td align=center>".$row2[1]."</td></tr>";
	
	}
   
  }// cierre del else donde empieza la impresión

}
?>
