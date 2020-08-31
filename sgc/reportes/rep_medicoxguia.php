<html>
<head>
<title>MATRIX - [REPORTE DE MEDICOS EVALUADOS POR GUIA MEDICA POR UNIDAD]</title>


<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/LeerTablaAmericas.js" type="text/javascript"></script>
<script src="../../../include/root/amcharts/amcharts.js" type="text/javascript"></script>


<script type="text/javascript">
	function enter()
	{
		document.forms.forma.submit();
	}

	function cerrarVentana()
	{
	 window.close()
	}

	function VolverAtras()
	{
	 history.back(1)
	}
	
   //para graficar
	function pintarGrafica ()
    {
		$('#tablareporte').LeerTablaAmericas({
				empezardesdefila: 1,
				dimension : '3d' ,
				titulo : 'Medicos Evaluados Por Guia Por Unidad' ,
				tituloy: 'Cumplimiento %',
				filaencabezado : [0,1],
				datosadicionales : 'todo',
				columnaencabezadoenx: 0,
				columnadatos: 1
			});

    }
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             REPORTE DE MEDICOS EVALUADOS X GUIA MEDICA X UNIDAD                                          *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte para ver por medico evaluado por guía por unidad                                                    |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : ABRIL 8 DE 2014.                                                                                            |
//FECHA ULTIMA ACTUALIZACION  : ABRIL 8 DE 2014.                                                                                            |
//DESCRIPCION			      : Este reporte sirve para observar las guias adherencias medicas x medico x unidad                            |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//pamec_000009     : Tabla de adherencia medicas UCI.                                                                                       |
//uce_000003       : Tabla de adherencia medicas UCE.                                                                                       |
//sgc_000017       : Tabla de adherencia medicas NEONATOS.                                                                                  |
//sgc_000018       : Tabla de adherencia medicas URGENCIAS.                                                                                 |
//sgc_000019       : Tabla de adherencia medicas GINECO PAP.                                                                                |
//sgc_000020       : Tabla de adherencia medicas ONCOLOGIA.                                                                                 |
//sgc_000028       : Tabla de adherencia medicas CLINICA DEL DOLOR.                                                                         |
//sgc_000029       : Tabla de adherencia medicas TMO.                                                                                       |
//sgc_000030       : Tabla de adherencia medicas CX.                                                                                        |
//                                                                                                                                          |
//==========================================================================================================================================

include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 05-Mayo-2014";

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
encabezado("Medicos Evaluados Por Guía Por Unidad",$wactualiz,"clinica");

if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{

 $empre1='sgc';

 

 


 //Forma
 echo "<form name='forma' action='rep_medicoxguia.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";

 if (!isset($pp) or $pp=='-' or !isset($ccos) or $ccos=='-' or !isset($anofec) )
  {
  	echo "<form name='rep_medicoxguia' action='' method=post>";

	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";

	//Centro de costos
	
    echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Centros De Costos:</B><br></font></b><select name='ccos' id='searchinput' onchange='enter()'>";

    $query = " SELECT evunidad"
           ."   FROM pamec_000009"
           ."  WHERE evunidad<>'*'"
		   ." GROUP BY 1"
           ." UNION ALL"
           ." SELECT evunidad"
           ."   FROM uce_000003"
           ."  WHERE evunidad<>'*'"
		   ." GROUP BY 1"
           ." UNION ALL"
		   ." SELECT evunidad"
           ."   FROM sgc_000017"
           ."  WHERE evunidad<>'*'"
		   ." GROUP BY 1"
           ." UNION ALL"
		    ." SELECT evunidad"
           ."   FROM sgc_000018"
           ."  WHERE evunidad<>'*'"
		   ." GROUP BY 1"
           ." UNION ALL"
		    ." SELECT evunidad"
           ."   FROM sgc_000019"
           ."  WHERE evunidad<>'*'"
		   ." GROUP BY 1"
           ." UNION ALL"
		    ." SELECT evunidad"
           ."   FROM sgc_000020"
           ."  WHERE evunidad<>'*'"
		   ." GROUP BY 1"
		   ." UNION ALL"
		    ." SELECT evunidad"
           ."   FROM sgc_000028"
           ."  WHERE evunidad<>'*'"
		   ." GROUP BY 1"
		   ." UNION ALL"
		    ." SELECT evunidad"
           ."   FROM sgc_000029"
           ."  WHERE evunidad<>'*'"
		   ." GROUP BY 1"
           ." UNION ALL"
		   ." SELECT evunidad"
           ."   FROM sgc_000030"
           ."  WHERE evunidad<>'*'"
		   ."  GROUP BY 1"          
		  ." ORDER BY 1";
           
    $err3 = mysql_query($query,$conex);
    $num3 = mysql_num_rows($err3);
    $tcc=$ccos;
   
    if ($tcc == '')
    { 
     echo "<option></option>";
    }
    else 
    {
   	 echo "<option value='".$ccos."'>".$ccos."</option>";
    } 
   
    for ($i=1;$i<=$num3;$i++)
	{
	 $row3 = mysql_fetch_array($err3);
	 echo "<option>".$row3[0]."</option>";
	}
     echo "</select></td></tr>";
   
   
	//Ingreso de Año del Reporte
	 
    echo "<Tr>";
    echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2><i>Año Reporte Guía<i><br></font><bgcolor='#dddddd' aling=center><input type='input' name='anofec' size=9 maxlength=4 id='anofec'></td>";
    echo "</Tr>";
   
   if (isset($anofec))
   {
    $anofec=$anofec;
   }
   else 
   {
    $anofec='';	
   }
	
 	
   $tpcc=explode('-',$ccos);
   
    
   /////////////////////////////////////////////////////////////////////////// seleccion para las listas de guia segun centros de costos
   IF ($tpcc[0]=='')
   {
   echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Lista de Guias:</B><br></font></b><select name='pp' id='searchinput'>";

   $query = " SELECT concat('-')"
           ."   FROM det_selecciones"
           ." GROUP BY 1";
           
   $err3 = mysql_query($query,$conex);
   $num3 = mysql_num_rows($err3);
   $tpp=$pp;
   
   if ($pp == '')
    { 
     echo "<option>-</option>";
    }
   else 
    {
   	 echo "<option value='".$pp[0]."'>".$pp[0]."</option>";
    } 
   
   for ($i=1;$i<=$num3;$i++)
	{
	$row3 = mysql_fetch_array($err3);
	echo "<option>".$row3[0]."</option>";
	}
   echo "</select></td></tr>";
   
   /////////////////////////////////////////////////////////////////////////// termina seleccion
   }
   
   
   IF ($tpcc[0]=='1020')
   {
   echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Lista de Guias:</B><br></font></b><select name='pp' id='searchinput'>";

   $query = " SELECT concat(subcodigo,'-',descripcion)"
           ."   FROM det_selecciones"
           ."  WHERE medico LIKE 'pamec'" 
           ."    AND codigo LIKE '021'"
           ."    AND activo =  'A'";
           
   $err3 = mysql_query($query,$conex);
   $num3 = mysql_num_rows($err3);
   $tpp=$pp;
   
   if ($pp == '')
    { 
     echo "<option></option>";
    }
   else 
    {
   	 echo "<option value='".$pp[0]."'>".$pp[0]."</option>";
    } 
   
   for ($i=1;$i<=$num3;$i++)
	{
	$row3 = mysql_fetch_array($err3);
	echo "<option>".$row3[0]."</option>";
	}
   echo "</select></td></tr>";
   
   /////////////////////////////////////////////////////////////////////////// termina seleccion
   }
   
   IF ($tpcc[0]=='1282')
   {
   echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Lista de Guias:</B><br></font></b><select name='pp' id='searchinput'>";

   $query = " SELECT concat(subcodigo,'-',descripcion)"
           ."   FROM det_selecciones"
           ."  WHERE medico LIKE 'uce'" 
           ."    AND codigo LIKE '002'"
           ."    AND activo =  'A'";
           
   $err3 = mysql_query($query,$conex);
   $num3 = mysql_num_rows($err3);
   $tpp=$pp;
   
   if ($pp == '')
    { 
     echo "<option></option>";
    }
   else 
    {
   	 echo "<option value='".$pp[0]."'>".$pp[0]."</option>";
    } 
   
   for ($i=1;$i<=$num3;$i++)
	{
	$row3 = mysql_fetch_array($err3);
	echo "<option>".$row3[0]."</option>";
	}
   echo "</select></td></tr>";
   
   /////////////////////////////////////////////////////////////////////////// termina seleccion
   }
   
   IF ($tpcc[0]=='1190')
   {
   echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Lista de Guias:</B><br></font></b><select name='pp' id='searchinput'>";

   $query = " SELECT concat(subcodigo,'-',descripcion)"
           ."   FROM det_selecciones"
           ."  WHERE medico LIKE 'sgc'" 
           ."    AND codigo LIKE '001'"
           ."    AND activo =  'A'";
           
   $err3 = mysql_query($query,$conex);
   $num3 = mysql_num_rows($err3);
   $tpp=$pp;
   
   if ($pp == '')
    { 
     echo "<option></option>";
    }
   else 
    {
   	 echo "<option value='".$pp[0]."'>".$pp[0]."</option>";
    } 
   
   for ($i=1;$i<=$num3;$i++)
	{
	$row3 = mysql_fetch_array($err3);
	echo "<option>".$row3[0]."</option>";
	}
   echo "</select></td></tr>";
   
   /////////////////////////////////////////////////////////////////////////// termina seleccion
   }
   
   IF ($tpcc[0]=='1130')
   {
   echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Lista de Guias:</B><br></font></b><select name='pp' id='searchinput'>";

   $query = " SELECT concat(subcodigo,'-',descripcion)"
           ."   FROM det_selecciones"
           ."  WHERE medico LIKE 'sgc'" 
           ."    AND codigo LIKE '003'"
           ."    AND activo =  'A'";
           
   $err3 = mysql_query($query,$conex);
   $num3 = mysql_num_rows($err3);
   $tpp=$pp;
   
   if ($pp == '')
    { 
     echo "<option></option>";
    }
   else 
    {
   	 echo "<option value='".$pp[0]."'>".$pp[0]."</option>";
    } 
   
   for ($i=1;$i<=$num3;$i++)
	{
	$row3 = mysql_fetch_array($err3);
	echo "<option>".$row3[0]."</option>";
	}
   echo "</select></td></tr>";
   
   /////////////////////////////////////////////////////////////////////////// termina seleccion
   }
   
   IF ($tpcc[0]=='1182')
   {
   echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Lista de Guias:</B><br></font></b><select name='pp' id='searchinput'>";

   $query = " SELECT concat(subcodigo,'-',descripcion)"
           ."   FROM det_selecciones"
           ."  WHERE medico LIKE 'sgc'" 
           ."    AND codigo LIKE '004'"
           ."    AND activo =  'A'";
           
   $err3 = mysql_query($query,$conex);
   $num3 = mysql_num_rows($err3);
   $tpp=$pp;
   
   if ($pp == '')
    { 
     echo "<option></option>";
    }
   else 
    {
   	 echo "<option value='".$pp[0]."'>".$pp[0]."</option>";
    } 
   
   for ($i=1;$i<=$num3;$i++)
	{
	$row3 = mysql_fetch_array($err3);
	echo "<option>".$row3[0]."</option>";
	}
   echo "</select></td></tr>";
   
   /////////////////////////////////////////////////////////////////////////// termina seleccion
   }
   
   IF ($tpcc[0]=='1284')
   {
   echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Lista de Guias:</B><br></font></b><select name='pp' id='searchinput'>";

   $query = " SELECT concat(subcodigo,'-',descripcion)"
           ."   FROM det_selecciones"
           ."  WHERE medico LIKE 'sgc'" 
           ."    AND codigo LIKE '005'"
           ."    AND activo =  'A'";
           
   $err3 = mysql_query($query,$conex);
   $num3 = mysql_num_rows($err3);
   $tpp=$pp;
   
   if ($pp == '')
    { 
     echo "<option></option>";
    }
   else 
    {
   	 echo "<option value='".$pp[0]."'>".$pp[0]."</option>";
    } 
   
   for ($i=1;$i<=$num3;$i++)
	{
	$row3 = mysql_fetch_array($err3);
	echo "<option>".$row3[0]."</option>";
	}
   echo "</select></td></tr>";
   
   /////////////////////////////////////////////////////////////////////////// termina seleccion
   }
   
   IF ($tpcc[0]=='1160')
   {
   echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Lista de Guias:</B><br></font></b><select name='pp' id='searchinput'>";

   $query = " SELECT concat(subcodigo,'-',descripcion)"
           ."   FROM det_selecciones"
           ."  WHERE medico LIKE 'sgc'" 
           ."    AND codigo LIKE '013'"
           ."    AND activo =  'A'";
           
   $err3 = mysql_query($query,$conex);
   $num3 = mysql_num_rows($err3);
   $tpp=$pp;
   
   if ($pp == '')
    { 
     echo "<option></option>";
    }
   else 
    {
   	 echo "<option value='".$pp[0]."'>".$pp[0]."</option>";
    } 
   
   for ($i=1;$i<=$num3;$i++)
	{
	$row3 = mysql_fetch_array($err3);
	echo "<option>".$row3[0]."</option>";
	}
   echo "</select></td></tr>";
   
   /////////////////////////////////////////////////////////////////////////// termina seleccion
   }
   
   IF ($tpcc[0]=='1189')
   {
   echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Lista de Guias:</B><br></font></b><select name='pp' id='searchinput'>";

   $query = " SELECT concat(subcodigo,'-',descripcion)"
           ."   FROM det_selecciones"
           ."  WHERE medico LIKE 'sgc'" 
           ."    AND codigo LIKE '014'"
           ."    AND activo =  'A'";
           
   $err3 = mysql_query($query,$conex);
   $num3 = mysql_num_rows($err3);
   $tpp=$pp;
   
   if ($pp == '')
    { 
     echo "<option></option>";
    }
   else 
    {
   	 echo "<option value='".$pp[0]."'>".$pp[0]."</option>";
    } 
   
   for ($i=1;$i<=$num3;$i++)
	{
	$row3 = mysql_fetch_array($err3);
	echo "<option>".$row3[0]."</option>";
	}
   echo "</select></td></tr>";
   
   /////////////////////////////////////////////////////////////////////////// termina seleccion
   }
   
   IF ($tpcc[0]=='1016')
   {
   echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Lista de Guias:</B><br></font></b><select name='pp' id='searchinput'>";

   $query = " SELECT concat(subcodigo,'-',descripcion)"
           ."   FROM det_selecciones"
           ."  WHERE medico LIKE 'sgc'" 
           ."    AND codigo LIKE '015'"
           ."    AND activo =  'A'";
           
   $err3 = mysql_query($query,$conex);
   $num3 = mysql_num_rows($err3);
   $tpp=$pp;
   
   if ($pp == '')
    { 
     echo "<option></option>";
    }
   else 
    {
   	 echo "<option value='".$pp[0]."'>".$pp[0]."</option>";
    } 
   
   for ($i=1;$i<=$num3;$i++)
	{
	$row3 = mysql_fetch_array($err3);
	echo "<option>".$row3[0]."</option>";
	}
   echo "</select></td></tr>";
   
   /////////////////////////////////////////////////////////////////////////// termina seleccion
   }
   
   
   echo "<br>";
   echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='GENERAR'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
   echo '</div>';
   echo '</div>';
   echo '</div>';

  }
 ELSE // Cuando ya estan todos los datos escogidos
  {
   
   $tpcc=explode('-',$ccos);

   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
   echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='200'>";  //border=0 no muestra la cuadricula en 1 si.
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>MEDICOS EVALUADOS POR GUIA MEDICA POR UNIDAD</b></font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366>$pp</font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>AÑO DEL REPORTE: <i>".$anofec."</i></b></font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>CENTRO DE COSTOS - UNIDAD: <i>".$ccos."</i></b></font></td>";
   echo "</tr>";
   echo "</table>";

   
   IF ($tpcc[0]=='1020')
   {
   
   $query = " SELECT evmedico,evdesc1sino,evdesc2sino,evdesc3sino,evdesc4sino"
           ."   FROM pamec_000009"
           ."  WHERE evfecha LIKE '".$anofec."%' "
		   ."    AND evguia = '".$pp."'"
           ."  ORDER BY evmedico";

    $err1 = mysql_query($query,$conex);
    $num1 = mysql_num_rows($err1);

    //echo $query."<br>".mysql_errno().'='.mysql_error();

    $arrecrit=Array();

    $query2 = "SELECT evmedico"
           ."    FROM pamec_000009"
           ."   WHERE evfecha LIKE '".$anofec."%' "
		   ."     AND evguia = '".$pp."'"
           ."   GROUP BY evmedico"
		   ."   ORDER BY evmedico";

    $err2 = mysql_query($query2,$conex);
    $num2 = mysql_num_rows($err2);

	//echo $query2."<br>".mysql_errno().'='.mysql_error();
	}
	
   IF ($tpcc[0]=='1282')
   {
   
   $query = " SELECT evmedico,evdesc1sino,evdesc2sino,evdesc3sino,evdesc4sino"
           ."   FROM uce_000003"
           ."  WHERE evfecha LIKE '".$anofec."%' "
		   ."    AND evguia = '".$pp."'"
           ."  ORDER BY evmedico";

    $err1 = mysql_query($query,$conex);
    $num1 = mysql_num_rows($err1);

    //echo $query."<br>".mysql_errno().'='.mysql_error();

    $arrecrit=Array();

    $query2 = "SELECT evmedico"
           ."    FROM uce_000003"
           ."   WHERE evfecha LIKE '".$anofec."%' "
		   ."    AND evguia = '".$pp."'"
           ."   GROUP BY evmedico"
		   ."   ORDER BY evmedico";

    $err2 = mysql_query($query2,$conex);
    $num2 = mysql_num_rows($err2);

	//echo $query2."<br>".mysql_errno().'='.mysql_error();
	}
	
   IF ($tpcc[0]=='1190')
   {
   
   $query = " SELECT evmedico,evdesc1sino,evdesc2sino,evdesc3sino,evdesc4sino"
           ."   FROM ".$empre1."_000017"
           ."  WHERE evfecha LIKE '".$anofec."%' "
		   ."    AND evguia = '".$pp."'"
           ."  ORDER BY evmedico";

    $err1 = mysql_query($query,$conex);
    $num1 = mysql_num_rows($err1);

    //echo $query."<br>".mysql_errno().'='.mysql_error();

    $arrecrit=Array();

    $query2 = "SELECT evmedico"
           ."    FROM ".$empre1."_000017"
           ."   WHERE evfecha LIKE '".$anofec."%' "
		   ."     AND evguia = '".$pp."'"
           ."   GROUP BY evmedico"
		   ."   ORDER BY evmedico";

    $err2 = mysql_query($query2,$conex);
    $num2 = mysql_num_rows($err2);

	//echo $query2."<br>".mysql_errno().'='.mysql_error();
	}
	
   IF ($tpcc[0]=='1130')
   {
   
   $query = " SELECT evmedico,evdesc1sino,evdesc2sino,evdesc3sino,evdesc4sino"
           ."   FROM ".$empre1."_000018"
           ."  WHERE evfecha LIKE '".$anofec."%' "
		   ."    AND evguia = '".$pp."'"
           ."  ORDER BY evmedico";

    $err1 = mysql_query($query,$conex);
    $num1 = mysql_num_rows($err1);

    //echo $query."<br>".mysql_errno().'='.mysql_error();

    $arrecrit=Array();

    $query2 = "SELECT evmedico"
           ."    FROM ".$empre1."_000018"
           ."   WHERE evfecha LIKE '".$anofec."%' "
		   ."     AND evguia = '".$pp."'"
           ."   GROUP BY evmedico"
		   ."   ORDER BY evmedico";

    $err2 = mysql_query($query2,$conex);
    $num2 = mysql_num_rows($err2);

	//echo $query2."<br>".mysql_errno().'='.mysql_error();
	}
	
   IF ($tpcc[0]=='1182')
   {
   
   $query = " SELECT evmedico,evdesc1sino,evdesc2sino,evdesc3sino,evdesc4sino"
           ."   FROM ".$empre1."_000019"
           ."  WHERE evfecha LIKE '".$anofec."%' "
		   ."    AND evguia = '".$pp."'"
           ."  ORDER BY evmedico";

    $err1 = mysql_query($query,$conex);
    $num1 = mysql_num_rows($err1);

    //echo $query."<br>".mysql_errno().'='.mysql_error();

    $arrecrit=Array();

    $query2 = "SELECT evmedico"
           ."    FROM ".$empre1."_000019"
           ."   WHERE evfecha LIKE '".$anofec."%' "
		   ."     AND evguia = '".$pp."'"
           ."   GROUP BY evmedico"
		   ."   ORDER BY evmedico";

    $err2 = mysql_query($query2,$conex);
    $num2 = mysql_num_rows($err2);

	//echo $query2."<br>".mysql_errno().'='.mysql_error();
	}
	
   IF ($tpcc[0]=='1284')
   {
   
   $query = " SELECT evmedico,evdesc1sino,evdesc2sino,evdesc3sino,evdesc4sino"
           ."   FROM ".$empre1."_000020"
           ."  WHERE evfecha LIKE '".$anofec."%' "
		   ."    AND evguia = '".$pp."'"
           ."  ORDER BY evmedico,evguia";

    $err1 = mysql_query($query,$conex);
    $num1 = mysql_num_rows($err1);

    //echo $query."<br>".mysql_errno().'='.mysql_error();

    $arrecrit=Array();

    $query2 = "SELECT evmedico"
           ."    FROM ".$empre1."_000020"
           ."   WHERE evfecha LIKE '".$anofec."%' "
		   ."     AND evguia = '".$pp."'"
           ."   GROUP BY evmedico"
		   ."   ORDER BY evmedico";

    $err2 = mysql_query($query2,$conex);
    $num2 = mysql_num_rows($err2);

	//echo $query2."<br>".mysql_errno().'='.mysql_error();
	}
	
   IF ($tpcc[0]=='1160')
   {
   
   $query = " SELECT evmedico,evdesc1sino,evdesc2sino,evdesc3sino,evdesc4sino"
           ."   FROM ".$empre1."_000028"
           ."  WHERE evfecha LIKE '".$anofec."%' "
		   ."    AND evguia = '".$pp."'"
           ."  ORDER BY evmedico,evguia";

    $err1 = mysql_query($query,$conex);
    $num1 = mysql_num_rows($err1);

    //echo $query."<br>".mysql_errno().'='.mysql_error();

    $arrecrit=Array();

    $query2 = "SELECT evmedico"
           ."    FROM ".$empre1."_000028"
           ."   WHERE evfecha LIKE '".$anofec."%' "
		   ."     AND evguia = '".$pp."'"
           ."   GROUP BY evmedico"
		   ."   ORDER BY evmedico";

    $err2 = mysql_query($query2,$conex);
    $num2 = mysql_num_rows($err2);

	//echo $query2."<br>".mysql_errno().'='.mysql_error();
	}
	
   IF ($tpcc[0]=='1189')
   {
   
   $query = " SELECT evmedico,evdesc1sino,evdesc2sino,evdesc3sino,evdesc4sino"
           ."   FROM ".$empre1."_000029"
           ."  WHERE evfecha LIKE '".$anofec."%' "
		   ."    AND evguia = '".$pp."'"
           ."  ORDER BY evmedico,evguia";

    $err1 = mysql_query($query,$conex);
    $num1 = mysql_num_rows($err1);

    //echo $query."<br>".mysql_errno().'='.mysql_error();

    $arrecrit=Array();

    $query2 = "SELECT evmedico"
           ."    FROM ".$empre1."_000029"
           ."   WHERE evfecha LIKE '".$anofec."%' "
		   ."     AND evguia = '".$pp."'"
           ."   GROUP BY evmedico"
		   ."   ORDER BY evmedico";

    $err2 = mysql_query($query2,$conex);
    $num2 = mysql_num_rows($err2);

	//echo $query2."<br>".mysql_errno().'='.mysql_error();
	}
	
   IF ($tpcc[0]=='1016')
   {
   
   $query = " SELECT evmedico,evdesc1sino,evdesc2sino,evdesc3sino,evdesc4sino"
           ."   FROM ".$empre1."_000030"
           ."  WHERE evfecha LIKE '".$anofec."%' "
		   ."    AND evguia = '".$pp."'"
           ."  ORDER BY evmedico,evguia";

    $err1 = mysql_query($query,$conex);
    $num1 = mysql_num_rows($err1);

    //echo $query."<br>".mysql_errno().'='.mysql_error();

    $arrecrit=Array();

    $query2 = "SELECT evmedico"
           ."    FROM ".$empre1."_000030"
           ."   WHERE evfecha LIKE '".$anofec."%' "
		   ."     AND evguia = '".$pp."'"
           ."   GROUP BY evmedico"
		   ."   ORDER BY evmedico";

    $err2 = mysql_query($query2,$conex);
    $num2 = mysql_num_rows($err2);

	//echo $query2."<br>".mysql_errno().'='.mysql_error();
	}
	
	echo "<table border=1 cellpadding='0' cellspacing='0' align=center size='400' id='tablareporte'>";
    echo "<tr>";
	echo "<td bgcolor='#FFFFFF'align=center nowrap='nowrap'><font text color=#000000 size=1><b>MEDICO EVALUADO</b></td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1><b>ELEMENTO EVALUADO_1</b></td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1><b>ELEMENTO EVALUADO_2</b></td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1><b>ELEMENTO EVALUADO_3</b></td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1><b>ELEMENTO EVALUADO_4</b></td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1><b>CANTIDAD HISTORIAS EVALUADAS</b></td>";
	echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1><b>PROMEDIO TOTAL ADHERENCIA</b></td>";
	echo "</tr>";

    $swtitulo='SI';
	$ccoant='';
	$canti=0;

    for ($j=1; $j <=5; $j++)
    {
     $arrecrit[$j]=0;
	}

    for ($i=1;$i<=$num1;$i++)
	{
	 $row2 = mysql_fetch_array($err1);

	 IF ($swtitulo=='SI')
	  {
	   IF ($canti==0)
	   {
	    $ccoant   = $row2[0];
	    $swtitulo='NO';
	   }
       ELSE
	   {
        IF ($ccoant<>$row2[0])
	    {
		  echo "<Tr >";
	      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".$ccoant."</font></td>";
          echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrita[1]/$canti)*100)."%</font></td>";
          echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrita[2]/$canti)*100)."%</font></td>";
          echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrita[3]/$canti)*100)."%</font></td>";
          echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrita[4]/$canti)*100)."%</font></td>";
	      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($canti)."</font></td>";
		  echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(( (($arrecrita[1]/$canti)*100)+(($arrecrita[2]/$canti)*100)+(($arrecrita[3]/$canti)*100)+(($arrecrita[4]/$canti)*100))/3 )."%</font></td>";
          echo "</tr >";

	      $canti=0;
	      for ($j=1; $j <=5; $j++)
          {
           $arrecrit[$j]=0;
		   $arrecrita[$j]=0;
          }

		  $ccoant   = $row2[0];
        }
		ELSE
		{
		  $ccoant   = $row2[0];
		}

	   }

	   $swtitulo='NO';

	  }

	  IF ($ccoant==$row2[0])
	  {

	  IF ($row2[1]=='on')
      {
      	$arrecrit[1]=$arrecrit[1]+1;
      }
      IF ($row2[2]=='on')
      {
      	$arrecrit[2]=$arrecrit[2]+1;
      }
      IF ($row2[3]=='on')
      {
      	$arrecrit[3]=$arrecrit[3]+1;
      }
	  IF ($row2[4]=='on')
      {
      	$arrecrit[4]=$arrecrit[4]+1;
      }
	  	  
	  $canti=$canti+1;

	 }
	 ELSE
	 {

	  echo "<Tr >";
	  echo "<td  bgcolor=#FFFFFF align=center><font size=1>".$ccoant."</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrit[1]/$canti)*100)."%</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrit[2]/$canti)*100)."%</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrit[3]/$canti)*100)."%</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrit[4]/$canti)*100)."%</font></td>";
	  echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($canti)."</font></td>";
	  echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(( (($arrecrit[1]/$canti)*100)+(($arrecrit[2]/$canti)*100)+(($arrecrit[3]/$canti)*100)+(($arrecrit[4]/$canti)*100))/3 )."%</font></td>";
      echo "</tr >";

	  $canti=0;
	  for ($j=1; $j <=8; $j++)
      {
        $arrecrit[$j]=0;
		$arrecrita[$j]=0;
      }

	  IF ($row2[1]=='on')
      {
      	$arrecrit[1]=$arrecrit[1]+1;
		$arrecrita[1]=$arrecrita[1]+1;
      }
      IF ($row2[2]=='on')
      {
      	$arrecrit[2]=$arrecrit[2]+1;
		$arrecrita[2]=$arrecrita[2]+1;
      }
      IF ($row2[3]=='on')
      {
      	$arrecrit[3]=$arrecrit[3]+1;
		$arrecrita[3]=$arrecrita[3]+1;
      }
	  IF ($row2[4]=='on')
      {
      	$arrecrit[4]=$arrecrit[4]+1;
        $arrecrita[4]=$arrecrita[4]+1;
	  }
	  	 
	  $canti=$canti+1;
	  $swtitulo='SI';
	  $ccoant   = $row2[0];

	 }

  } // cierre del for

  IF ($canti==0)
   {
     echo "</table>";
   }
  ELSE
  {
    echo "<Tr >";
	echo "<td  bgcolor=#FFFFFF align=center><font size=1>".$row2[0]."</b></font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrit[1]/$canti)*100)."%</font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrit[2]/$canti)*100)."%</font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrit[3]/$canti)*100)."%</font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrit[4]/$canti)*100)."%</font></td>";
   	echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($canti)."</font></td>";
	echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(( (($arrecrit[1]/$canti)*100)+(($arrecrit[2]/$canti)*100)+(($arrecrit[3]/$canti)*100)+(($arrecrit[4]/$canti)*100))/3 )."%</font></td>";
    echo "</tr >";

	echo "</table>";
  }

    echo "<table border=0 align=CENTER size=100%>";
    echo "<Tr >";
    echo "<td align=CENTER bgcolor=#FFFFFF ><font size=2 color=#000000><b>TOTAL DE HISTORIAS EVALUADAS POR MEDICOS EVALUADOS: </b></font></td>";
    echo "<td align=CENTER bgcolor=#FFFFFF ><font size=2 color=#000000>&nbsp;<b>$num1</b></font></td>";
    echo "</tr >";
    echo "</table>";

   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Cerrar ventana' onclick='cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";


   echo "<br>";
   echo "<br>";
   echo "<table>";
   echo "<tr><td align='center'><input type='button' value='Graficar' onclick='pintarGrafica()'></td></tr>";
   echo "<tr>
			<td align='center'>
				<div id='amchart1' style='width:1000px; height:600px;' align='center'></div>
			</td>
		</tr>";
   echo "</table>";

 }// cierre del else donde empieza la impresión

}
?>