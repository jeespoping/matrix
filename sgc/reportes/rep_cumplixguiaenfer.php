<html>
<head>
<title>MATRIX - [REPORTE DE GUIAS DE ADHERENCIAS ENFERMER�A]</title>


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
				titulo : 'Guias Adherencias Enfermeria' ,
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
*                                             REPORTE GUIAS ADHERENCIAS ENFERMERIA                                                         *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte para ver promedio de cumplimiento por criterio deacuerdo a las guias medicas                        |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : ABRIL 8 DE 2014.                                                                                            |
//FECHA ULTIMA ACTUALIZACION  : ABRIL 8 DE 2014.                                                                                            |
//DESCRIPCION			      : Este reporte sirve para observar las guias adherencias de enfermeria                                        |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//sgc_000022       : Tabla de adherencia enfermer�a HOSPITALIZACION.                                                                        |
//sgc_000023       : Tabla de adherencia enfermer�a UCI.                                                                                    |
//sgc_000024       : Tabla de adherencia enfermer�a NEONATOS.                                                                               |
//sgc_000025       : Tabla de adherencia enfermer�a UCE.                                                                                    |
//sgc_000026       : Tabla de adherencia enfermer�a URGENCIAS.                                                                              |
//sgc_000027       : Tabla de adherencia enfermer�a TMO.                                                                                    |
//                                                                                                                                          |
// MODIFICACION                                                                                                                             |
//                                                                                                                                          |
// Se coloca en los query LIKE.                                                                                                                                         |
//==========================================================================================================================================

include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 14-Mayo-2014";

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
encabezado("Guias Adherencias De Enfermer�a",$wactualiz,"clinica");

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
 echo "<form name='forma' action='rep_cumplixguiaenfer.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";

 if (!isset($pp) or $pp=='-' or !isset($ccos) or $ccos=='-' or !isset($anofec) )
  {
  	echo "<form name='rep_cumplixguiaenfer' action='' method=post>";

	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";

	//Centro de costos
	
    echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Centros De Costos:</B><br></font></b><select name='ccos' id='searchinput' onchange='enter()'>";

    $query = " SELECT evunidad"
           ."   FROM ".$empre1."_000022"
           ."  WHERE evunidad<>'*'"
		   ." GROUP BY 1"
           ." UNION ALL"
           ." SELECT evunidad"
           ."   FROM ".$empre1."_000023"
           ."  WHERE evunidad<>'*'"
		   ." GROUP BY 1"
           ." UNION ALL"
		   ." SELECT evunidad"
           ."   FROM ".$empre1."_000024"
           ."  WHERE evunidad<>'*'"
		   ." GROUP BY 1"
           ." UNION ALL"
		    ." SELECT evunidad"
           ."   FROM ".$empre1."_000025"
           ."  WHERE evunidad<>'*'"
		   ." GROUP BY 1"
           ." UNION ALL"
		    ." SELECT evunidad"
           ."   FROM ".$empre1."_000026"
           ."  WHERE evunidad<>'*'"
		   ." GROUP BY 1"
           ." UNION ALL"
		    ." SELECT evunidad"
           ."   FROM ".$empre1."_000027"
           ."  WHERE evunidad<>'*'"
		   ." GROUP BY 1"
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
   
   
	//Ingreso de A�o del Reporte
	 
    echo "<Tr>";
    echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2><i>A�o Reporte Gu�a<i><br></font><bgcolor='#dddddd' aling=center><input type='input' name='anofec' size=9 maxlength=4 id='anofec'></td>";
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
           ."  WHERE medico LIKE 'sgc'" 
           ."    AND codigo LIKE '008'"
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
           ."  WHERE medico LIKE 'sgc'" 
           ."    AND codigo LIKE '010'"
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
           ."    AND codigo LIKE '009'"
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
           ."    AND codigo LIKE '011'"
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
           ."    AND codigo LIKE '012'"
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
           ."    AND codigo LIKE '007'"
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
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>GUIAS ADHERENCIAS DE ENFERMER�A</b></font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366>$pp</font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>A�O DEL REPORTE: <i>".$anofec."</i></b></font></td>";
   echo "</tr>";
   echo "</table>";

   
   IF ($tpcc[0]=='1020')
   {
   
   $query = " SELECT evunidad,evdesc1sino,evdesc2sino,evdesc3sino,evdesc4sino,evdesc5sino"
           ."   FROM ".$empre1."_000023"
           ."  WHERE evfecha LIKE '".$anofec."%' "
		   ."    AND evguia = '".$pp."'"
           ."  ORDER BY evunidad";

    $err1 = mysql_query($query,$conex);
    $num1 = mysql_num_rows($err1);

    //echo $query."<br>".mysql_errno().'='.mysql_error();

    $arrecrit=Array();

    $query2 = "SELECT evunidad"
           ."    FROM ".$empre1."_000023"
           ."   WHERE evfecha LIKE '".$anofec."%' "
		   ."     AND evguia = '".$pp."'"
           ."   GROUP BY evunidad"
		   ."   ORDER BY evunidad";

    $err2 = mysql_query($query2,$conex);
    $num2 = mysql_num_rows($err2);

	//echo $query2."<br>".mysql_errno().'='.mysql_error();
	}
	
   IF ($tpcc[0]=='1282')
   {
   
   $query = " SELECT evunidad,evdesc1sino,evdesc2sino,evdesc3sino,evdesc4sino,evdesc5sino"
           ."   FROM ".$empre1."_000025"
           ."  WHERE evfecha LIKE '".$anofec."%' "
		   ."    AND evguia LIKE  '%".$pp."%'"
           ."  ORDER BY evunidad";

    $err1 = mysql_query($query,$conex);
    $num1 = mysql_num_rows($err1);

    //echo $query."<br>".mysql_errno().'='.mysql_error();

    $arrecrit=Array();

    $query2 = "SELECT evunidad"
           ."    FROM ".$empre1."_000025"
           ."   WHERE evfecha LIKE '".$anofec."%' "
		   ."    AND evguia LIKE '%".$pp."%'"
           ."   GROUP BY evunidad"
		   ."   ORDER BY evunidad";

    $err2 = mysql_query($query2,$conex);
    $num2 = mysql_num_rows($err2);

	//echo $query2."<br>".mysql_errno().'='.mysql_error();
	}
	
   IF ($tpcc[0]=='1190')
   {
   
   $query = " SELECT evunidad,evdesc1sino,evdesc2sino,evdesc3sino,evdesc4sino,evdesc5sino"
           ."   FROM ".$empre1."_000024"
           ."  WHERE evfecha LIKE '".$anofec."%' "
		   ."    AND evguia = '".$pp."'"
           ."  ORDER BY evunidad";

    $err1 = mysql_query($query,$conex);
    $num1 = mysql_num_rows($err1);

    //echo $query."<br>".mysql_errno().'='.mysql_error();

    $arrecrit=Array();

    $query2 = "SELECT evunidad"
           ."    FROM ".$empre1."_000024"
           ."   WHERE evfecha LIKE '".$anofec."%' "
		   ."     AND evguia = '".$pp."'"
           ."   GROUP BY evunidad"
		   ."   ORDER BY evunidad";

    $err2 = mysql_query($query2,$conex);
    $num2 = mysql_num_rows($err2);

	//echo $query2."<br>".mysql_errno().'='.mysql_error();
	}
	
   IF ($tpcc[0]=='1130')
   {
   
   $query = " SELECT evunidad,evdesc1sino,evdesc2sino,evdesc3sino,evdesc4sino,evdesc5sino"
           ."   FROM ".$empre1."_000026"
           ."  WHERE evfecha LIKE '".$anofec."%' "
		   ."    AND evguia = '".$pp."'"
           ."  ORDER BY evunidad";

    $err1 = mysql_query($query,$conex);
    $num1 = mysql_num_rows($err1);

    //echo $query."<br>".mysql_errno().'='.mysql_error();

    $arrecrit=Array();

    $query2 = "SELECT evunidad"
           ."    FROM ".$empre1."_000026"
           ."   WHERE evfecha LIKE '".$anofec."%' "
		   ."     AND evguia = '".$pp."'"
           ."   GROUP BY evunidad"
		   ."   ORDER BY evunidad";

    $err2 = mysql_query($query2,$conex);
    $num2 = mysql_num_rows($err2);

	//echo $query2."<br>".mysql_errno().'='.mysql_error();
	}
	
   IF ($tpcc[0]=='1189')
   {
   
   $query = " SELECT evunidad,evdesc1sino,evdesc2sino,evdesc3sino,evdesc4sino,evdesc5sino"
           ."   FROM ".$empre1."_000027"
           ."  WHERE evfecha LIKE '".$anofec."%' "
		   ."    AND evguia = '".$pp."'"
           ."  ORDER BY evunidad";

    $err1 = mysql_query($query,$conex);
    $num1 = mysql_num_rows($err1);

    //echo $query."<br>".mysql_errno().'='.mysql_error();

    $arrecrit=Array();

    $query2 = "SELECT evunidad"
           ."    FROM ".$empre1."_000027"
           ."   WHERE evfecha LIKE '".$anofec."%' "
		   ."     AND evguia = '".$pp."'"
           ."   GROUP BY evunidad"
		   ."   ORDER BY evunidad";

    $err2 = mysql_query($query2,$conex);
    $num2 = mysql_num_rows($err2);

	//echo $query2."<br>".mysql_errno().'='.mysql_error();
	}
	
   IF ($tpcc[0]=='1284')
   {
   
   $query = " SELECT evunidad,evdesc1sino,evdesc2sino,evdesc3sino,evdesc4sino,evdesc5sino"
           ."   FROM ".$empre1."_000022"
           ."  WHERE evfecha LIKE '".$anofec."%' "
		   ."    AND evguia = '".$pp."'"
           ."  ORDER BY evunidad,evguia";

    $err1 = mysql_query($query,$conex);
    $num1 = mysql_num_rows($err1);

    //echo $query."<br>".mysql_errno().'='.mysql_error();

    $arrecrit=Array();

    $query2 = "SELECT evunidad"
           ."    FROM ".$empre1."_000022"
           ."   WHERE evfecha LIKE '".$anofec."%' "
		   ."     AND evguia = '".$pp."'"
           ."   GROUP BY evunidad"
		   ."   ORDER BY evunidad";

    $err2 = mysql_query($query2,$conex);
    $num2 = mysql_num_rows($err2);

	//echo $query2."<br>".mysql_errno().'='.mysql_error();
	}
	
	echo "<table border=1 cellpadding='0' cellspacing='0' align=center size='400' id='tablareporte'>";
    echo "<tr>";
	echo "<td bgcolor='#FFFFFF'align=center nowrap='nowrap'><font text color=#000000 size=1><b>CENTRO DE COSTOS-UNIDAD</b></td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1><b>ELEMENTO EVALUADO_1</b></td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1><b>ELEMENTO EVALUADO_2</b></td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1><b>ELEMENTO EVALUADO_3</b></td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1><b>ELEMENTO EVALUADO_4</b></td>";
	echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1><b>ELEMENTO EVALUADO_5</b></td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1><b>CANTIDAD HISTORIAS EVALUADAS</b></td>";
	echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1><b>PROMEDIO TOTAL ADHERENCIA</b></td>";
	echo "</tr>";

    $swtitulo='SI';
	$ccoant='';
	$canti=0;

    for ($j=1; $j <=6; $j++)
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
          echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrita[5]/$canti)*100)."%</font></td>";	     
		  echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($canti)."</font></td>";
		  echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(( (($arrecrita[1]/$canti)*100)+(($arrecrita[2]/$canti)*100)+(($arrecrita[3]/$canti)*100)+(($arrecrita[4]/$canti)*100)+(($arrecrita[5]/$canti)*100) )/5 )."%</font></td>";
          echo "</tr >";

	      $canti=0;
	      for ($j=1; $j <=6; $j++)
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
	  IF ($row2[5]=='on')
      {
      	$arrecrit[5]=$arrecrit[5]+1;
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
	  echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrit[5]/$canti)*100)."%</font></td>";
	  echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($canti)."</font></td>";
	  echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(( (($arrecrit[1]/$canti)*100)+(($arrecrit[2]/$canti)*100)+(($arrecrit[3]/$canti)*100)+(($arrecrit[4]/$canti)*100) + (($arrecrit[5]/$canti)*100) )/5 )."%</font></td>";
      echo "</tr >";

	  $canti=0;
	  for ($j=1; $j <=6; $j++)
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
	  IF ($row2[5]=='on')
      {
      	$arrecrit[5]=$arrecrit[5]+1;
        $arrecrita[5]=$arrecrita[5]+1;
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
   	echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrit[5]/$canti)*100)."%</font></td>";
	echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($canti)."</font></td>";
	echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(( (($arrecrit[1]/$canti)*100)+(($arrecrit[2]/$canti)*100)+(($arrecrit[3]/$canti)*100)+(($arrecrit[4]/$canti)*100) + (($arrecrit[5]/$canti)*100))/5 )."%</font></td>";
    echo "</tr >";

	echo "</table>";
  }

    echo "<table border=0 align=CENTER size=100%>";
    echo "<Tr >";
    echo "<td align=CENTER bgcolor=#FFFFFF ><font size=2 color=#000000><b>TOTAL DE HISTORIAS EVALUADAS POR EL CENTRO DE COSTOS: </b></font></td>";
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

 }// cierre del else donde empieza la impresi�n

}
?>