<html>
<head>
<title>Proceso para modificar tarifas</title>
<link href="/matrix/root/tavo.css" rel="stylesheet" type="text/css" />
 <!-- UTF-8 is the recommended encoding for your pages -->
  <!--   <meta http-equiv="content-type" content="text/xml; charset=utf-8" />  -->
    <title>Zapatec DHTML Calendar</title>

  <!-- Loading Theme file(s) -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />
    
  <!-- Loading Calendar JavaScript files -->
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>

  <!-- Loading language definition file -->
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>
</head>
<font face='arial'>
<BODY TEXT="#000066">

<script type="text/javascript">
	function enter()
	{
		document.forms.pro_modtarifas.submit();
	}
	
	function cerrar_ventana()
	{
		window.close();
	}
	
	
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             PROCESO PARA MODIFICAR TARIFAS                                                               *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Proceso para la modificacion de tarifas en unix desde mercadeo.                                             |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : MAYO 13 DE 2008.                                                                                            |
//FECHA ULTIMA ACTUALIZACION  : Junio 04 DE 2008.                                                                                           |
//DESCRIPCION			      : Este proceso sirve para que modifiquen las tarifas en MATRIX tabla 26, deacuerdo a como lo necesiten        |
//                              En la tabla 26 es donde se modifica y queda el usuario que hace el incremento de la tarifa.                 |
//                              En la tabla 84 queda una copia de como estaba el articulo antes de hacer el incremento por si se tienen que |
//                              devolver la grabación, ahi queda el usuario que hizo el ultimo incremento antes de usar el programa.        |
//TABLAS UTILIZADAS 	                                                                                                                    |
//$empresa_000026    		  : Tabla de tarifas de examenes                                                                                |
//$empresa_000084    		  : Copia Tabla de tarifas de examenes                                                                          |
//==========================================================================================================================================
$wactualiz="Ver. 2008-06-04";

session_start();
if(!isset($_SESSION['user']))
{
 echo "error";
}
else
{
	
 

 


 echo '<div id="header">';
 echo '<div id="logo">';
 echo '<h1><a href="pro_modtarifas.php">MODIFICAR TARIFAS</a></h1>';
 echo '<h2>PROMOTORA MEDICA LAS AMERICAS <b>' . $wactualiz . '</h2>';
 echo '</div>';
 echo '</div></br></br></br></br></br>';
 $empresa='root';
 
 /////////////////////////////////////////////////////////////////////////////////////// seleccion para saber la Base de Datos

 $query = " SELECT Detapl,Detval"
	       ."   FROM ".$empresa."_000051"
	       ."  WHERE Detemp='".$wemp."'"
	       ."  Order by Id"; //Hay varias empresas con cod 09, por eso se le coloco el order by por Id (Gabriel Agudelo)
	 
 $err = mysql_query($query,$conex);
 $num = mysql_num_rows($err);

 $row = mysql_fetch_array($err);

 $empre1=$row[1];

 if (!isset($fec1) or !isset($arti) or !isset($art) or !isset($cco) or !isset($tar) or !isset($vlrcol) or !isset($pordes) or $vlrcol=='' or $arti=='' or $art=='' or $cco=='' or $tar=='' ) 
 {
  echo "<form name='pro_modtarifas' action='' method=post>";

  echo '<table align=center cellspacing="10" >';
  
 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////Articulo a subir o todos
  
  echo "<Tr>";
  echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2><i>Codigo o Nombre del Articulo a Buscar o * Todos <i><br></font></td>";
  echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2><i>Articulo <i><br></font></td>";
  echo "</Tr>";
   
  if (isset($arti))
   {
    $arti=$arti;
   }
  else 
   {
    $arti='';	
   }
     
   echo "<td bgcolor='#dddddd' aling=center><input type='TEXT' name='arti' size=60 maxlength=50 id='arti' value='".$arti."' onchange='enter()'></td>";
  
   
   /////////////////////////////////////////////////////////////////////////// selecciono el articulo o todos los articulos de la tabla 26
   echo "<td align=CENTER colspan=1 bgcolor=#DDDDDD><select name='art' id='searchinput' onchange='enter()'>";

   IF ($arti=='')
   {
   	 echo "<option></option>";
   }
   else 
   {
    IF ($arti != '*')
    {
     $query = " SELECT Mtaart "
	          ."  FROM ".$empre1."_000026 "
	          ." WHERE mtaart like '%$arti%' " 
	          ." Group by 1";
	          
	$err = mysql_query($query,$conex);
    $num = mysql_num_rows($err);
        
    if ($arti == '')
     { 
      echo "<option></option>";
     }
   else 
    {
   	 echo "<option>".$art."</option>";
    } 
   
   for ($i=1;$i<=$num;$i++)
	{
	$row = mysql_fetch_array($err);
	echo "<option>".$row[0]."</option>";
	}
    echo "<option>TODOS</option>";
    
   }         

   else 
   
   {
    $query = " SELECT Mtaart "
	         ."  FROM ".$empre1."_000026 "
	         ." GROUP BY 1"
	         ." ORDER BY 1" ;
    	
        
    $err = mysql_query($query,$conex);
    $num = mysql_num_rows($err);
    
   
    if ($arti == '')
     { 
      echo "<option></option>";
     }
    else 
     {
   	  echo "<option>".$art."</option>";
     }  
   
    for ($i=1;$i<=$num;$i++)
	{
	 $row = mysql_fetch_array($err);
	 echo "<option>".$row[0]."</option>";
  	}
     
   }
  }
  echo "</select></td>";
  
  echo "<Tr>";
  echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2><i>Centro de Costos <i><br></font></td>";
  echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2><i>Tarifa a Modificar<i><br></font></td>";
  echo "</Tr>";
    
  /////////////////////////////////////////////////////////////////////////// selecciono el centro de costos que tiene el articulo de la tabla 26
  echo "<td align=CENTER colspan=1 bgcolor=#DDDDDD><select name='cco' id='searchinput' onchange='enter()' >";
  
  if ($art=='TODOS')
  {
  	$query1 = " SELECT mtacco "
	          ."  FROM ".$empre1."_000026 "
	          ." GROUP BY 1"
	          ." ORDER BY 1";
	          
    $err1 = mysql_query($query1,$conex);
    $num1 = mysql_num_rows($err1);
   
    if ($arti == '')
     { 
      echo "<option></option>";
     }
    else 
     {
  	   echo "<option>".$cco."</option>";
     } 
   
    for ($i=1;$i<=$num1;$i++)
    {
     $row1 = mysql_fetch_array($err1);
     echo "<option>".$row1[0]."</option>";
    }
    echo "<option>TODOS</option>";
    
  }
  else 
  {
    $query1 = " SELECT mtacco "
	          ."  FROM ".$empre1."_000026 "
	          ." WHERE mtaart = '".$art."' " 
	          ." GROUP BY 1";
	          
    $err1 = mysql_query($query1,$conex);
    $num1 = mysql_num_rows($err1);
   
    if ($arti == '')
     { 
      echo "<option></option>";
     }
    else 
     {
  	   echo "<option>".$cco."</option>";
     } 
   
    for ($i=1;$i<=$num1;$i++)
    {
     $row1 = mysql_fetch_array($err1);
     echo "<option>".$row1[0]."</option>";
    }
    echo "<option>TODOS</option>";
     
  }
  
  echo "</select></td>"; 
  
  
  /////////////////////////////////////////////////////////////////////////// selecciono la tarifa segun el centro de costos y el articulo tabla 26
  echo "<td align=CENTER colspan=1 bgcolor=#DDDDDD><select name='tar' id='searchinput' onchange='enter()' >";
  
  if ($cco=='TODOS')
  {
  	$query1 = " SELECT mtatar "
	          ."  FROM ".$empre1."_000026 "
	          ." WHERE mtaart = '".$art."' "
	          ." GROUP BY 1"
	          ." ORDER BY 1";
	          
    $err1 = mysql_query($query1,$conex);
    $num1 = mysql_num_rows($err1);
   
    if ($arti == '')
     { 
      echo "<option></option>";
     }
    else 
     {
  	   echo "<option>".$tar."</option>";
     } 
   
    for ($i=1;$i<=$num1;$i++)
    {
     $row1 = mysql_fetch_array($err1);
     echo "<option>".$row1[0]."</option>";
    }
    echo "<option>TODOS</option>";
    
  }
  else 
  {
    $query1 = " SELECT mtatar "
	          ."  FROM ".$empre1."_000026 "
	          ." WHERE mtaart = '".$art."' "
	          ."   AND mtacco = '".$cco."' " 
	          ." GROUP BY 1";
	          
    $err1 = mysql_query($query1,$conex);
    $num1 = mysql_num_rows($err1);
   
    if ($arti == '')
     { 
      echo "<option></option>";
     }
    else 
     {
  	   echo "<option>".$tar."</option>";
     } 
   
    for ($i=1;$i<=$num1;$i++)
    {
     $row1 = mysql_fetch_array($err1);
     echo "<option>".$row1[0]."</option>";
    }
    echo "<option>TODOS</option>";
     
  }
  
  echo "</select></td>"; 
  
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////RADIO BOTON PARA ESCOJER ENTRE VALOR Y PORCENTAJE 
  echo "<table border=(1) align=center>";
  echo "<tr><td colspan=2 align=center><input type='TEXT' name='vlrcol' size=15 maxlength=15 ></td></tr>";
  echo "<tr><td bgcolor='#dddddd'><input type='radio' name='vlrco' value='vlrc'><font text color=#003366 size=2>Valor a Colocar&nbsp&nbsp&nbsp&nbsp&nbsp</td>
		<td bgcolor='#dddddd'><input type='radio' name='vlrco' value='poraum'><font text color=#003366 size=2>Porcentaje Aumento&nbsp&nbsp&nbsp&nbsp&nbsp</td></tr>
		</table>";
		
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////PORCENTAJE DESCUENTO  
  echo '<table align=center cellspacing="10" >';
  echo "<Tr>";
  echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2><i>Porcentaje Descuento<i><br></font></td>";
  echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2><i>Fecha A Partir de Cuando Suben<i><br></font></td>";
  echo "</Tr>";
  
  
  if (isset($pordes))
   {
    $pordes=$pordes;
   }
  else 
   {
    $pordes=0;	
   }
     
  echo "<td bgcolor='#dddddd' aling=center><input type='TEXT' name='pordes' size=15 maxlength=25 id='pordes' value='".$pordes."' ></td>";
  
  
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////FECHA A PARTIR DE CUANDO SUBEN LAS TARIFAS
  
  $hoy=date("Y-m-d");
  if (!isset($fec1))
    $fec1=$hoy;
     
   	$cal="calendario('fec1','1')";
   	//echo "<tr>";
	echo "<td bgcolor='#dddddd' align=center><input type='TEXT' name='fec1' size=10 maxlength=10  id='fec1' readonly='readonly' value=".$fec1." class=tipo3><button id='trigger1' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'fec1',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
   <?php
  
   
   
   echo "<table border=(1) align=center>";
   echo "<Tr>";
   echo "<td align=center bgcolor=#DDDDDD colspan=4><b><font text color=#003366 size=2><i>REDONDEAR <i><br></font></td>";
   echo "</Tr>";
   echo "<tr><td bgcolor='#dddddd'><input type='radio' name='redon' value='peso'><font text color=#003366 size=2>Al Peso&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</td>
		<td bgcolor='#dddddd'><input type='radio' name='redon' value='dece'><font text color=#003366 size=2>A La Decena&nbsp&nbsp&nbsp&nbsp</td>
		<td bgcolor='#dddddd'><input type='radio' name='redon' value='cente'><font text color=#003366 size=2>A La Centena&nbsp&nbsp&nbsp</td>
		<td bgcolor='#dddddd'><input type='radio' name='redon' value='mile'><font text color=#003366 size=2>A La Milesima&nbsp&nbsp&nbsp</td></tr>
		</table>";
   
   echo "<table border=(1) align=center>";
   echo "<tr><td bgcolor='#dddddd'><font text color=#003366 size=2>42559.43 = 42559&nbsp</td>
		<td bgcolor='#dddddd'><font text color=#003366 size=2>42559.43 = 42560&nbsp&nbsp&nbsp&nbsp</td>
		<td bgcolor='#dddddd'><font text color=#003366 size=2>42559.43 = 42600&nbsp&nbsp&nbsp&nbsp&nbsp</td>
		<td bgcolor='#dddddd'><font text color=#003366 size=2>42559.43 = 43000&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</td></tr>
		</table>";
   
   echo '<table align=center cellspacing="10" >';
   echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='CAMBIAR TARIFA'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
   echo '</div>';
   echo '</div>';
   echo '</div>';
   
  }
 
else // Cuando ya estan todos los datos escogidos
 {
  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA EL UPDATE

 $cod=explode('-',$user); //Aca traigo de la variable global $user el codigo del empleado por el cual ingreso al matrix. 

 $hoy=date("Y-m-d");
 $hora=date("H:i:s"); 
 $cod1='C-'.$cod[1];
 
 if ($art=='TODOS')
 {
  if ($cco=='TODOS')
  {	  	
  	$quer1 = "INSERT INTO ".$empre1."_000084 (medico,fecha_data,hora_data,mtatar,mtacco,mtaart,mtavan,mtafec,mtavac,mtapde,mtaest,seguridad) "
	         ." SELECT medico,'".$hoy."','".$hora."',mtatar,mtacco,mtaart,mtavan,mtafec,mtavac,mtapde,mtaest,seguridad"
             ."   FROM ".$empre1."_000026 "
             ."  WHERE ".$empre1."_000026.mtatar='".$tar."' "
             ."    AND ".$empre1."_000026.mtaest='on'";
          
    //echo $quer1."<br>";
    $err4 = mysql_query($quer1,$conex) or die("ERROR EN QUERY"); 
	 			
    $num1 = mysql_affected_rows();
    
    echo "<table>";
    echo "<tr><td align=center bgcolor =#DDDDDD><b>Cantidad de Artículos Insertados : ".$num1."</b></td></tr>";
    echo "</table>";	
   
    if ($vlrco=='poraum')
    {
     switch ($redon)
 	 {
	  case "peso":
	  {
	    $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=round((mtavac*(($vlrcol/100)+1)),0),seguridad='".$cod1."' "
	           ." WHERE ".$empre1."_000026.mtatar='".$tar."' "
               ."   AND ".$empre1."_000026.mtaest='on'";
	  
	       break;
	  }  
	  case "dece":
	  {
	  	
	  	$query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=round((mtavac*(($vlrcol/100)+1)),-1),seguridad='".$cod1."' "
	           ." WHERE ".$empre1."_000026.mtatar='".$tar."' "
               ."   AND ".$empre1."_000026.mtaest='on'";
           break;
	  }
	  case "cente":
	  {
	    $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=round((mtavac*(($vlrcol/100)+1)),-2),seguridad='".$cod1."' "
	           ." WHERE ".$empre1."_000026.mtatar='".$tar."' "
               ."   AND ".$empre1."_000026.mtaest='on'";
	      break;
	  }
	 
	  case "mile":
	  {
	    $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=round((mtavac*(($vlrcol/100)+1)),-3),seguridad='".$cod1."' "
	           ." WHERE ".$empre1."_000026.mtatar='".$tar."' "
               ."   AND ".$empre1."_000026.mtaest='on'";
	      break;
	  }
	 }
    }
    else  //else de vlrco=='poraum'
    {
       $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=$vlrcol,seguridad='".$cod1."' "
	          ." WHERE ".$empre1."_000026.mtatar='".$tar."' "
              ."   AND ".$empre1."_000026.mtaest='on'";	
    }
    
   $err1 = mysql_query($query1,$conex);
   $num1 = mysql_affected_rows();
   
   echo "<table>";
   echo "<tr><td align=center bgcolor =#DDDDDD><b>Cantidad de Artículos Cambiados : ".$num1."</b></td></tr>";
   echo "</table>";	            
   
   
   echo "<table border=0 align=center size=100%>";
   
   echo "<Tr >";
   echo "<td bgcolor='#006699'align=center width=20%><font size=3 text color=#FFFFFF>TARIFA</font></td>";
   echo "<td bgcolor='#006699'align=center width=20%><font size=3 text color=#FFFFFF>CENTRO DE COSTOS</font></td>";
   echo "<td bgcolor='#006699'align=center width=20%><font size=3 text color=#FFFFFF>ARTICULO</font></td>";
   echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF>VALOR ANTERIOR</font></td>";
   echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF>FECHA TARIFA</font></td>";
   echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF>VALOR ACTUAL</font></td>";
   echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF>PORC_DESC</font></td>";
   echo "</Tr >";
   
   $query2= " SELECT mtatar,mtacco,mtaart,mtavan,mtafec,mtavac,mtapde "
	        .  "FROM ".$empre1."_000026 " 
	        ." WHERE ".$empre1."_000026.mtatar='".$tar."' "
	        ."   AND ".$empre1."_000026.mtaest='on'"
	        ." ORDER BY mtacco,mtatar,mtaart"; 
   
   $err2 = mysql_query($query2,$conex);
   $num2 = mysql_num_rows($err2);
   
   for ($i=1;$i<=$num2;$i++)
	{
	 if (is_int ($i/2))
	  {
	   $wcf="DDDDDD";  // color de fondo
	  }
	 else
	  {
	   $wcf="CCFFFF"; // color de fondo
	  }

	 $row2 = mysql_fetch_array($err2);
	   
	 echo "<Tr >";
	 echo "<td  bgcolor='$wcf' align=left><font size=2>$row2[0]</font></td>";
	 echo "<td  bgcolor='$wcf' align=left><font size=2>$row2[1]</font></td>";
	 echo "<td  bgcolor='$wcf' align=left><font size=2>$row2[2]</font></td>";
	 echo "<td  bgcolor='$wcf' align=center><font size=2>".number_format($row2[3],2,'.',',')."</font></td>";
	 echo "<td  bgcolor='$wcf' align=left><font size=2>$row2[4]</font></td>";
	 echo "<td  bgcolor='$wcf' align=center><font size=2>".number_format($row2[5],2,'.',',')."</font></td>";
	 echo "<td  bgcolor='$wcf' align=center><font size=2>$row2[6]</font></td>";
	 echo "<Tr >";
   }
   echo "</table>";
   
  }
  else //else de $cco=='todos'
  {
   if ($tar=='TODOS')
   {
  	$quer1 = "INSERT INTO ".$empre1."_000084 (medico,fecha_data,hora_data,mtatar,mtacco,mtaart,mtavan,mtafec,mtavac,mtapde,mtaest,seguridad) "
	         ." SELECT medico,'".$hoy."','".$hora."',mtatar,mtacco,mtaart,mtavan,mtafec,mtavac,mtapde,mtaest,seguridad"
             ."   FROM ".$empre1."_000026 "
             ."  WHERE ".$empre1."_000026.mtacco='".$cco."' "
             ."    AND ".$empre1."_000026.mtaest='on'";
          
    //echo $quer1."<br>";
    $err4 = mysql_query($quer1,$conex) or die("ERROR EN QUERY"); 
	 			
    $num1 = mysql_affected_rows();
    
    echo "<table>";
    echo "<tr><td align=center bgcolor =#DDDDDD><b>Cantidad de Artículos Insertados : ".$num1."</b></td></tr>";
    echo "</table>";	
   
    if ($vlrco=='poraum')
    {
     switch ($redon)
 	 {
	  case "peso":
	  {
	    $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=round((mtavac*(($vlrcol/100)+1)),0),seguridad='".$cod1."' "
	           ." WHERE ".$empre1."_000026.mtacco='".$cco."' "
               ."   AND ".$empre1."_000026.mtaest='on'";
	  
	       break;
	  }  
	  case "dece":
	  {
	  	
	  	$query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=round((mtavac*(($vlrcol/100)+1)),-1),seguridad='".$cod1."' "
	           ." WHERE ".$empre1."_000026.mtacco='".$cco."' "
               ."   AND ".$empre1."_000026.mtaest='on'";
           break;
	  }
	  case "cente":
	  {
	    $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=round((mtavac*(($vlrcol/100)+1)),-2),seguridad='".$cod1."' "
	           ." WHERE ".$empre1."_000026.mtacco='".$cco."' "
               ."   AND ".$empre1."_000026.mtaest='on'";
	      break;
	  }
	 
	  case "mile":
	  {
	    $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=round((mtavac*(($vlrcol/100)+1)),-3),seguridad='".$cod1."' "
	           ." WHERE ".$empre1."_000026.mtacco='".$cco."' "
               ."   AND ".$empre1."_000026.mtaest='on'";
	      break;
	  }
	 }
    }
    else  //else de vlrco=='poraum'
    {
        $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=$vlrcol,seguridad='".$cod1."' "
	           ." WHERE ".$empre1."_000026.mtacco='".$cco."' "
               ."   AND ".$empre1."_000026.mtaest='on'";	
    }
    
    $err1 = mysql_query($query1,$conex);
    $num1 = mysql_num_rows($err1);
   
    echo "<table>";
    echo "<tr><td align=center bgcolor =#DDDDDD><b>Cantidad de Artículos Cambiados : ".$num1."</b></td></tr>";
    echo "</table>";	            
      	
    $query2= " SELECT mtatar,mtacco,mtaart,mtavan,mtafec,mtavac,mtapde "
	         .  "FROM ".$empre1."_000026 " 
	         ." WHERE ".$empre1."_000026.mtacco='".$cco."' "
	         ."   AND ".$empre1."_000026.mtaest='on'"
	         ." ORDER BY mtacco,mtatar,mtaart"; 
   
    $err2 = mysql_query($query2,$conex);
    $num2 = mysql_num_rows($err2);
   
    for ($i=1;$i<=$num2;$i++)
	{
	 if (is_int ($i/2))
	  {
	   $wcf="DDDDDD";  // color de fondo
	  }
	 else
	  {
	   $wcf="CCFFFF"; // color de fondo
	  }

	 $row2 = mysql_fetch_array($err2);
	   
	 echo "<Tr >";
	 echo "<td  bgcolor='$wcf' align=left><font size=2>$row2[0]</font></td>";
	 echo "<td  bgcolor='$wcf' align=left><font size=2>$row2[1]</font></td>";
	 echo "<td  bgcolor='$wcf' align=left><font size=2>$row2[2]</font></td>";
	 echo "<td  bgcolor='$wcf' align=center><font size=2>".number_format($row2[3],2,'.',',')."</font></td>";
	 echo "<td  bgcolor='$wcf' align=left><font size=2>$row2[4]</font></td>";
	 echo "<td  bgcolor='$wcf' align=center><font size=2>".number_format($row2[5],2,'.',',')."</font></td>";
	 echo "<td  bgcolor='$wcf' align=center><font size=2>$row2[6]</font></td>";
	 echo "<Tr >";
    }
    echo "</table>";
   
   }
   else // else de $tar=='TODOS' 
   {
  	$quer1 = "INSERT INTO ".$empre1."_000084 (medico,fecha_data,hora_data,mtatar,mtacco,mtaart,mtavan,mtafec,mtavac,mtapde,mtaest,seguridad) "
	         ." SELECT medico,'".$hoy."','".$hora."',mtatar,mtacco,mtaart,mtavan,mtafec,mtavac,mtapde,mtaest,seguridad"
             ."   FROM ".$empre1."_000026 "
             ."  WHERE ".$empre1."_000026.mtacco='".$cco."' "
             ."    AND ".$empre1."_000026.mtatar='".$tar."' "
             ."    AND ".$empre1."_000026.mtaest='on'";
          
    //echo $quer1."<br>";
    $err4 = mysql_query($quer1,$conex) or die("ERROR EN QUERY"); 
	 			
    $num1 = mysql_affected_rows();
    
    echo "<table>";
    echo "<tr><td align=center bgcolor =#DDDDDD><b>Cantidad de Artículos Insertados : ".$num1."</b></td></tr>";
    echo "</table>";	
   
    if ($vlrco=='poraum')
    {
     switch ($redon)
 	 {
	  case "peso":
	  {
	       $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=round((mtavac*(($vlrcol/100)+1)),0),seguridad='".$cod1."' "
	              ." WHERE ".$empre1."_000026.mtacco='".$cco."' "
	              ."   AND ".$empre1."_000026.mtatar='".$tar."' "
                  ."   AND ".$empre1."_000026.mtaest='on'";
	  
	       break;
	  }  
	  case "dece":
	  {
	  	
	       $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=round((mtavac*(($vlrcol/100)+1)),-1),seguridad='".$cod1."' "
	              ." WHERE ".$empre1."_000026.mtacco='".$cco."' "
	              ."   AND ".$empre1."_000026.mtatar='".$tar."' "
                  ."   AND ".$empre1."_000026.mtaest='on'";
           break;
	  }
	  case "cente":
	  {
	       $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=round((mtavac*(($vlrcol/100)+1)),-2),seguridad='".$cod1."' "
	              ." WHERE ".$empre1."_000026.mtacco='".$cco."' "
	              ."   AND ".$empre1."_000026.mtatar='".$tar."' "
                  ."   AND ".$empre1."_000026.mtaest='on'";
	      break;
	  }
	 
	  case "mile":
	  {
	       $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=round((mtavac*(($vlrcol/100)+1)),-3),seguridad='".$cod1."' "
	              ." WHERE ".$empre1."_000026.mtacco='".$cco."' "
	              ."   AND ".$empre1."_000026.mtatar='".$tar."' "
                  ."   AND ".$empre1."_000026.mtaest='on'";
	      break;
	  }
	 }
    }
    else  //else de vlrco=='poraum'
    {
           $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=$vlrcol,seguridad='".$cod1."' "
	              ." WHERE ".$empre1."_000026.mtacco='".$cco."' "
	              ."   AND ".$empre1."_000026.mtatar='".$tar."' "
                  ."   AND ".$empre1."_000026.mtaest='on'";	
    }
    
    $err1 = mysql_query($query1,$conex);
    $num1 = mysql_affected_rows();
   
    echo "<table>";
    echo "<tr><td align=center bgcolor =#DDDDDD><b>Cantidad de Artículos Cambiados : ".$num1."</b></td></tr>";
    echo "</table>";	            

   
    echo "<table border=0 align=center size=100%>";
   
    echo "<Tr >";
    echo "<td bgcolor='#006699'align=center width=20%><font size=3 text color=#FFFFFF>TARIFA</font></td>";
    echo "<td bgcolor='#006699'align=center width=20%><font size=3 text color=#FFFFFF>CENTRO DE COSTOS</font></td>";
    echo "<td bgcolor='#006699'align=center width=20%><font size=3 text color=#FFFFFF>ARTICULO</font></td>";
    echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF>VALOR ANTERIOR</font></td>";
    echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF>FECHA TARIFA</font></td>";
    echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF>VALOR ACTUAL</font></td>";
    echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF>PORC_DESC</font></td>";
    echo "</Tr >";
   
    $query2= " SELECT mtatar,mtacco,mtaart,mtavan,mtafec,mtavac,mtapde "
	        .  "FROM ".$empre1."_000026 " 
	        ." WHERE ".$empre1."_000026.mtatar='".$tar."' "
	        ."   AND ".$empre1."_000026.mtacco='".$cco."' "
	        ."   AND ".$empre1."_000026.mtaest='on'"
	        ." ORDER BY mtacco,mtatar,mtaart"; 
   
    $err2 = mysql_query($query2,$conex);
    $num2 = mysql_num_rows($err2);
   
    for ($i=1;$i<=$num2;$i++)
	 {
	 if (is_int ($i/2))
	  {
	   $wcf="DDDDDD";  // color de fondo
	  }
	 else
	  {
	   $wcf="CCFFFF"; // color de fondo
	  }

	 $row2 = mysql_fetch_array($err2);
	   
	 echo "<Tr >";
	 echo "<td  bgcolor='$wcf' align=left><font size=2>$row2[0]</font></td>";
	 echo "<td  bgcolor='$wcf' align=left><font size=2>$row2[1]</font></td>";
	 echo "<td  bgcolor='$wcf' align=left><font size=2>$row2[2]</font></td>";
	 echo "<td  bgcolor='$wcf' align=center><font size=2>".number_format($row2[3],2,'.',',')."</font></td>";
	 echo "<td  bgcolor='$wcf' align=left><font size=2>$row2[4]</font></td>";
	 echo "<td  bgcolor='$wcf' align=center><font size=2>".number_format($row2[5],2,'.',',')."</font></td>";
	 echo "<td  bgcolor='$wcf' align=center><font size=2>$row2[6]</font></td>";
	 echo "<Tr >";
    }
    echo "</table>";   
   
   }
  }
 }
 else // IF $ART='TODOS' 
 { 
  if ($cco=='TODOS')
  {	  	
   if ($tar=='TODOS')
   {   
  	$quer1 = "INSERT INTO ".$empre1."_000084 (medico,fecha_data,hora_data,mtatar,mtacco,mtaart,mtavan,mtafec,mtavac,mtapde,mtaest,seguridad) "
	         ." SELECT medico,'".$hoy."','".$hora."',mtatar,mtacco,mtaart,mtavan,mtafec,mtavac,mtapde,mtaest,seguridad"
             ."   FROM ".$empre1."_000026 "
             ."  WHERE ".$empre1."_000026.mtaart='".$art."' "
             ."    AND ".$empre1."_000026.mtaest='on'";
          
    //echo $quer1."<br>";
    $err4 = mysql_query($quer1,$conex) or die("ERROR EN QUERY"); 
	 			
    $num1 = mysql_affected_rows();
    
    echo "<table>";
    echo "<tr><td align=center bgcolor =#DDDDDD><b>Cantidad de Artículos Insertados : ".$num1."</b></td></tr>";
    echo "</table>";	
    
    if ($vlrco=='poraum')
    {
     switch ($redon)
 	 {
	  case "peso":
	  {
	       $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=round((mtavac*(($vlrcol/100)+1)),0),seguridad='".$cod1."' "
	              ." WHERE ".$empre1."_000026.mtaart='".$art."' "
                  ."   AND ".$empre1."_000026.mtaest='on'";
	  
	       break;
	  }  
	  case "dece":
	  {
	  	
	  	   $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=round((mtavac*(($vlrcol/100)+1)),-1),seguridad='".$cod1."' "
	              ." WHERE ".$empre1."_000026.mtaart='".$art."' "
                  ."   AND ".$empre1."_000026.mtaest='on'";
           break;
	  }
	  case "cente":
	  {
	       $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=round((mtavac*(($vlrcol/100)+1)),-2),seguridad='".$cod1."' "
	              ." WHERE ".$empre1."_000026.mtaart='".$art."' "
                  ."   AND ".$empre1."_000026.mtaest='on'";
	      break;
	  }
	 
	  case "mile":
	  {
	       $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=round((mtavac*(($vlrcol/100)+1)),-3),seguridad='".$cod1."' "
	              ." WHERE ".$empre1."_000026.mtaart='".$art."' "
                  ."   AND ".$empre1."_000026.mtaest='on'";
	      break;
	  }
	 }
    }
    else  //else de vlrco=='poraum'
    {
           $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=$vlrcol,seguridad='".$cod1."' "
	              ." WHERE ".$empre1."_000026.mtaart='".$art."' "
                  ."   AND ".$empre1."_000026.mtaest='on'";	
    }
    
    $err1 = mysql_query($query1,$conex);
    $num1 = mysql_affected_rows();
   
    echo "<table>";
    echo "<tr><td align=center bgcolor =#DDDDDD><b>Cantidad de Artículos Cambiados : ".$num1."</b></td></tr>";
    echo "</table>";	            

    echo "<table border=0 align=center size=100%>";
   
    echo "<Tr >";
    echo "<td bgcolor='#006699'align=center width=20%><font size=3 text color=#FFFFFF>TARIFA</font></td>";
    echo "<td bgcolor='#006699'align=center width=20%><font size=3 text color=#FFFFFF>CENTRO DE COSTOS</font></td>";
    echo "<td bgcolor='#006699'align=center width=20%><font size=3 text color=#FFFFFF>ARTICULO</font></td>";
    echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF>VALOR ANTERIOR</font></td>";
    echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF>FECHA TARIFA</font></td>";
    echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF>VALOR ACTUAL</font></td>";
    echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF>PORC_DESC</font></td>";
    echo "</Tr >";
   
    $query2= " SELECT mtatar,mtacco,mtaart,mtavan,mtafec,mtavac,mtapde "
	        .  "FROM ".$empre1."_000026 " 
	        ." WHERE ".$empre1."_000026.mtaart='".$art."' "
	        ."   AND ".$empre1."_000026.mtaest='on'"
	        ." ORDER BY mtacco,mtatar,mtaart"; 
   
    $err2 = mysql_query($query2,$conex);
    $num2 = mysql_num_rows($err2);
   
    for ($i=1;$i<=$num2;$i++)
	 {
	 if (is_int ($i/2))
	  {
	   $wcf="DDDDDD";  // color de fondo
	  }
	 else
	  {
	   $wcf="CCFFFF"; // color de fondo
	  }

	 $row2 = mysql_fetch_array($err2);
	   
	 echo "<Tr >";
	 echo "<td  bgcolor='$wcf' align=left><font size=2>$row2[0]</font></td>";
	 echo "<td  bgcolor='$wcf' align=left><font size=2>$row2[1]</font></td>";
	 echo "<td  bgcolor='$wcf' align=left><font size=2>$row2[2]</font></td>";
	 echo "<td  bgcolor='$wcf' align=center><font size=2>".number_format($row2[3],2,'.',',')."</font></td>";
	 echo "<td  bgcolor='$wcf' align=left><font size=2>$row2[4]</font></td>";
	 echo "<td  bgcolor='$wcf' align=center><font size=2>".number_format($row2[5],2,'.',',')."</font></td>";
	 echo "<td  bgcolor='$wcf' align=center><font size=2>$row2[6]</font></td>";
	 echo "<Tr >";
    }
    echo "</table>";   

   }
   else //else de $tar=='TODOS' 
   {
   	
  	$quer1 = "INSERT INTO ".$empre1."_000084 (medico,fecha_data,hora_data,mtatar,mtacco,mtaart,mtavan,mtafec,mtavac,mtapde,mtaest,seguridad) "
	         ." SELECT medico,'".$hoy."','".$hora."',mtatar,mtacco,mtaart,mtavan,mtafec,mtavac,mtapde,mtaest,seguridad"
             ."   FROM ".$empre1."_000026 "
             ."  WHERE ".$empre1."_000026.mtaart='".$art."' "
             ."    AND ".$empre1."_000026.mtatar='".$tar."' "
             ."    AND ".$empre1."_000026.mtaest='on'";
          
    //echo $quer1."<br>";
    $err4 = mysql_query($quer1,$conex) or die("ERROR EN QUERY"); 
	 			
    $num1 = mysql_affected_rows();
    
    echo "<table>";
    echo "<tr><td align=center bgcolor =#DDDDDD><b>Cantidad de Artículos Insertados : ".$num1."</b></td></tr>";
    echo "</table>";	
    
    if ($vlrco=='poraum')
    {
     switch ($redon)
 	 {
	  case "peso":
	  {
	       $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=round((mtavac*(($vlrcol/100)+1)),0),seguridad='".$cod1."' "
	              ." WHERE ".$empre1."_000026.mtaart='".$art."' "
	              ."   AND ".$empre1."_000026.mtatar='".$tar."' "
                  ."   AND ".$empre1."_000026.mtaest='on'";
	  
	       break;
	  }  
	  case "dece":
	  {
	  	
	  	   $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=round((mtavac*(($vlrcol/100)+1)),-1),seguridad='".$cod1."' "
	              ." WHERE ".$empre1."_000026.mtaart='".$art."' "
	              ."   AND ".$empre1."_000026.mtatar='".$tar."' "
                  ."   AND ".$empre1."_000026.mtaest='on'";
           break;
	  }
	  case "cente":
	  {
	       $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=round((mtavac*(($vlrcol/100)+1)),-2),seguridad='".$cod1."' "
	              ." WHERE ".$empre1."_000026.mtaart='".$art."' "
	              ."   AND ".$empre1."_000026.mtatar='".$tar."' "
                  ."   AND ".$empre1."_000026.mtaest='on'";
	      break;
	  }
	 
	  case "mile":
	  {
	       $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=round((mtavac*(($vlrcol/100)+1)),-3),seguridad='".$cod1."' "
	              ." WHERE ".$empre1."_000026.mtaart='".$art."' "
	              ."   AND ".$empre1."_000026.mtatar='".$tar."' "
                  ."   AND ".$empre1."_000026.mtaest='on'";
	      break;
	  }
	 }
    }
    else  //else de vlrco=='poraum'
    {
           $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=$vlrcol,seguridad='".$cod1."' "
	              ." WHERE ".$empre1."_000026.mtaart='".$art."' "
	              ."   AND ".$empre1."_000026.mtatar='".$tar."' "
                  ."   AND ".$empre1."_000026.mtaest='on'";	
    }
    
   $err1 = mysql_query($query1,$conex);
   $num1 = mysql_affected_rows();
   
   echo "<table>";
   echo "<tr><td align=center bgcolor =#DDDDDD><b>Cantidad de Artículos Cambiados : ".$num1."</b></td></tr>";
   echo "</table>";

   echo "<table border=0 align=center size=100%>";
   
    echo "<Tr >";
    echo "<td bgcolor='#006699'align=center width=20%><font size=3 text color=#FFFFFF>TARIFA</font></td>";
    echo "<td bgcolor='#006699'align=center width=20%><font size=3 text color=#FFFFFF>CENTRO DE COSTOS</font></td>";
    echo "<td bgcolor='#006699'align=center width=20%><font size=3 text color=#FFFFFF>ARTICULO</font></td>";
    echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF>VALOR ANTERIOR</font></td>";
    echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF>FECHA TARIFA</font></td>";
    echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF>VALOR ACTUAL</font></td>";
    echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF>PORC_DESC</font></td>";
    echo "</Tr >";
   
    $query2= " SELECT mtatar,mtacco,mtaart,mtavan,mtafec,mtavac,mtapde "
	        .  "FROM ".$empre1."_000026 " 
	        ." WHERE ".$empre1."_000026.mtaart='".$art."' "
	        ."   AND ".$empre1."_000026.mtatar='".$tar."' "
	        ."   AND ".$empre1."_000026.mtaest='on'"
	        ." ORDER BY mtacco,mtatar,mtaart"; 
   
    $err2 = mysql_query($query2,$conex);
    $num2 = mysql_num_rows($err2);
   
    for ($i=1;$i<=$num2;$i++)
	 {
	 if (is_int ($i/2))
	  {
	   $wcf="DDDDDD";  // color de fondo
	  }
	 else
	  {
	   $wcf="CCFFFF"; // color de fondo
	  }

	 $row2 = mysql_fetch_array($err2);
	   
	 echo "<Tr >";
	 echo "<td  bgcolor='$wcf' align=left><font size=2>$row2[0]</font></td>";
	 echo "<td  bgcolor='$wcf' align=left><font size=2>$row2[1]</font></td>";
	 echo "<td  bgcolor='$wcf' align=left><font size=2>$row2[2]</font></td>";
	 echo "<td  bgcolor='$wcf' align=center><font size=2>".number_format($row2[3],2,'.',',')."</font></td>";
	 echo "<td  bgcolor='$wcf' align=left><font size=2>$row2[4]</font></td>";
	 echo "<td  bgcolor='$wcf' align=center><font size=2>".number_format($row2[5],2,'.',',')."</font></td>";
	 echo "<td  bgcolor='$wcf' align=center><font size=2>$row2[6]</font></td>";
	 echo "<Tr >";
    }
    echo "</table>";   
	        
   }
  }
  else //else de $cco=='todos'
  {
   if ($tar=='TODOS')
   {
  	$quer1 = "INSERT INTO ".$empre1."_000084 (medico,fecha_data,hora_data,mtatar,mtacco,mtaart,mtavan,mtafec,mtavac,mtapde,mtaest,seguridad) "
	         ." SELECT medico,'".$hoy."','".$hora."',mtatar,mtacco,mtaart,mtavan,mtafec,mtavac,mtapde,mtaest,seguridad"
             ."   FROM ".$empre1."_000026 "
             ."  WHERE ".$empre1."_000026.mtacco='".$cco."' "
             ."    AND ".$empre1."_000026.mtaart='".$art."' "
             ."    AND ".$empre1."_000026.mtaest='on'";
          
    //echo $quer1."<br>";
    $err4 = mysql_query($quer1,$conex) or die("ERROR EN QUERY"); 
	 			
    $num1 = mysql_affected_rows();
    
    echo "<table>";
    echo "<tr><td align=center bgcolor =#DDDDDD><b>Cantidad de Artículos Insertados : ".$num1."</b></td></tr>";
    echo "</table>";	
    
    if ($vlrco=='poraum')
    {
     switch ($redon)
 	 {
	  case "peso":
	  {
	       $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=round((mtavac*(($vlrcol/100)+1)),0),seguridad='".$cod1."' "
	              ." WHERE ".$empre1."_000026.mtacco='".$cco."' "
	              ."   AND ".$empre1."_000026.mtaart='".$art."' "
                  ."   AND ".$empre1."_000026.mtaest='on'";
	  
	       break;
	  }  
	  case "dece":
	  {
	  	
	  	   $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=round((mtavac*(($vlrcol/100)+1)),-1),seguridad='".$cod1."' "
	              ." WHERE ".$empre1."_000026.mtacco='".$cco."' "
	              ."   AND ".$empre1."_000026.mtaart='".$art."' "
                  ."   AND ".$empre1."_000026.mtaest='on'";
           break;
	  }
	  case "cente":
	  {
	       $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=round((mtavac*(($vlrcol/100)+1)),-2),seguridad='".$cod1."' "
	              ." WHERE ".$empre1."_000026.mtacco='".$cco."' "
	              ."   AND ".$empre1."_000026.mtaart='".$art."' "
                  ."   AND ".$empre1."_000026.mtaest='on'";
	      break;
	  }
	 
	  case "mile":
	  {
	       $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=round((mtavac*(($vlrcol/100)+1)),-3),seguridad='".$cod1."' "
	              ." WHERE ".$empre1."_000026.mtacco='".$cco."' "
	              ."   AND ".$empre1."_000026.mtaart='".$art."' "
                  ."   AND ".$empre1."_000026.mtaest='on'";
	      break;
	  }
	 }
    }
    else  //else de vlrco=='poraum'
    {
           $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=$vlrcol,seguridad='".$cod1."' "
	              ." WHERE ".$empre1."_000026.mtacco='".$cco."' "
	              ."   AND ".$empre1."_000026.mtaart='".$art."' "
                  ."   AND ".$empre1."_000026.mtaest='on'";	
    }
    
   $err1 = mysql_query($query1,$conex);
   $num1 = mysql_affected_rows();
   
   echo "<table>";
   echo "<tr><td align=center bgcolor =#DDDDDD><b>Cantidad de Artículos Cambiados : ".$num1."</b></td></tr>";
   echo "</table>";	            
      	
   echo "<table border=0 align=center size=100%>";
   
    echo "<Tr >";
    echo "<td bgcolor='#006699'align=center width=20%><font size=3 text color=#FFFFFF>TARIFA</font></td>";
    echo "<td bgcolor='#006699'align=center width=20%><font size=3 text color=#FFFFFF>CENTRO DE COSTOS</font></td>";
    echo "<td bgcolor='#006699'align=center width=20%><font size=3 text color=#FFFFFF>ARTICULO</font></td>";
    echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF>VALOR ANTERIOR</font></td>";
    echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF>FECHA TARIFA</font></td>";
    echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF>VALOR ACTUAL</font></td>";
    echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF>PORC_DESC</font></td>";
    echo "</Tr >";
   
    $query2= " SELECT mtatar,mtacco,mtaart,mtavan,mtafec,mtavac,mtapde "
	        .  "FROM ".$empre1."_000026 " 
	        ." WHERE ".$empre1."_000026.mtacco='".$cco."' "
	        ."   AND ".$empre1."_000026.mtaart='".$art."' "
	        ."   AND ".$empre1."_000026.mtaest='on'"
	        ." ORDER BY mtacco,mtatar,mtaart"; 
   
    $err2 = mysql_query($query2,$conex);
    $num2 = mysql_num_rows($err2);
   
    for ($i=1;$i<=$num2;$i++)
	 {
	 if (is_int ($i/2))
	  {
	   $wcf="DDDDDD";  // color de fondo
	  }
	 else
	  {
	   $wcf="CCFFFF"; // color de fondo
	  }

	 $row2 = mysql_fetch_array($err2);
	   
	 echo "<Tr >";
	 echo "<td  bgcolor='$wcf' align=left><font size=2>$row2[0]</font></td>";
	 echo "<td  bgcolor='$wcf' align=left><font size=2>$row2[1]</font></td>";
	 echo "<td  bgcolor='$wcf' align=left><font size=2>$row2[2]</font></td>";
	 echo "<td  bgcolor='$wcf' align=center><font size=2>".number_format($row2[3],2,'.',',')."</font></td>";
	 echo "<td  bgcolor='$wcf' align=left><font size=2>$row2[4]</font></td>";
	 echo "<td  bgcolor='$wcf' align=center><font size=2>".number_format($row2[5],2,'.',',')."</font></td>";
	 echo "<td  bgcolor='$wcf' align=center><font size=2>$row2[6]</font></td>";
	 echo "<Tr >";
    }
    echo "</table>";
    
   }
   else // else de $tar=='TODOS' 
   {
   	
  	$quer1 = "INSERT INTO ".$empre1."_000084 (medico,fecha_data,hora_data,mtatar,mtacco,mtaart,mtavan,mtafec,mtavac,mtapde,mtaest,seguridad) "
	         ." (SELECT medico,'".$hoy."','".$hora."',mtatar,mtacco,mtaart,mtavan,mtafec,mtavac,mtapde,mtaest,seguridad"
             ."   FROM ".$empre1."_000026 "
             ."  WHERE ".$empre1."_000026.mtacco='".$cco."' "
             ."    AND ".$empre1."_000026.mtatar='".$tar."' "
             ."    AND ".$empre1."_000026.mtaart='".$art."' "
             ."    AND ".$empre1."_000026.mtaest='on')";
          
    //echo $quer1."<br>";
    $err4 = mysql_query($quer1,$conex) or die("ERROR EN QUERY"); 
	 			
    $num1 = mysql_affected_rows();
    
    echo "<table>";
    echo "<tr><td align=center bgcolor =#DDDDDD><b>Cantidad de Artículos Insertados : ".$num1."</b></td></tr>";
    echo "</table>";	
   
    if ($vlrco=='poraum')
    {
     switch ($redon)
 	 {
	  case "peso":
	  {
	       $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=round((mtavac*(($vlrcol/100)+1)),0),seguridad='".$cod1."' "
	              ." WHERE ".$empre1."_000026.mtacco='".$cco."' "
	              ."   AND ".$empre1."_000026.mtatar='".$tar."' "
	              ."   AND ".$empre1."_000026.mtaart='".$art."' "
                  ."   AND ".$empre1."_000026.mtaest='on'";
	  
	       break;
	  }  
	  case "dece":
	  {
	  	
	  	   $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=round((mtavac*(($vlrcol/100)+1)),-1),seguridad='".$cod1."' "
	              ." WHERE ".$empre1."_000026.mtacco='".$cco."' "
	              ."   AND ".$empre1."_000026.mtatar='".$tar."' "
	              ."   AND ".$empre1."_000026.mtaart='".$art."' "
                  ."   AND ".$empre1."_000026.mtaest='on'";
           break;
	  }
	  case "cente":
	  {
	       $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=round((mtavac*(($vlrcol/100)+1)),-2),seguridad='".$cod1."' "
	              ." WHERE ".$empre1."_000026.mtacco='".$cco."' "
	              ."   AND ".$empre1."_000026.mtatar='".$tar."' "
	              ."   AND ".$empre1."_000026.mtaart='".$art."' "
                  ."   AND ".$empre1."_000026.mtaest='on'";
	      break;
	  }
	 
	  case "mile":
	  {
	       $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=round((mtavac*(($vlrcol/100)+1)),-3),seguridad='".$cod1."' "
	              ." WHERE ".$empre1."_000026.mtacco='".$cco."' "
	              ."   AND ".$empre1."_000026.mtatar='".$tar."' "
	              ."   AND ".$empre1."_000026.mtaart='".$art."' "
                  ."   AND ".$empre1."_000026.mtaest='on'";
	      break;
	  }
	 }
    }
    else  //else de vlrco=='poraum'
    {
           $query1="UPDATE ".$empre1."_000026 set mtavan=mtavac,mtafec='".$fec1."',mtavac=$vlrcol,seguridad='".$cod1."' "
	              ." WHERE ".$empre1."_000026.mtacco='".$cco."' "
	              ."   AND ".$empre1."_000026.mtatar='".$tar."' "
	              ."   AND ".$empre1."_000026.mtaart='".$art."' "
                  ."   AND ".$empre1."_000026.mtaest='on'";	
    }
    
    $err1 = mysql_query($query1,$conex);
    $num1 = mysql_affected_rows();
   
    //echo $query1."<br>";
    
    echo "<table>";
    echo "<tr><td align=center bgcolor =#DDDDDD><b>Cantidad de Artículos Cambiados : ".$num1."</b></td></tr>";
    echo "</table>";	            

    echo "<table border=0 align=center size=100%>";
   
    echo "<Tr >";
    echo "<td bgcolor='#006699'align=center width=17%><font size=3 text color=#FFFFFF>TARIFA</font></td>";
    echo "<td bgcolor='#006699'align=center width=27%><font size=3 text color=#FFFFFF>CENTRO DE COSTOS</font></td>";
    echo "<td bgcolor='#006699'align=center width=27%><font size=3 text color=#FFFFFF>ARTICULO</font></td>";
    echo "<td bgcolor='#006699'align=center width=8%><font size=3 text color=#FFFFFF>VALOR ANTERIOR</font></td>";
    echo "<td bgcolor='#006699'align=center width=8%><font size=3 text color=#FFFFFF>FECHA TARIFA</font></td>";
    echo "<td bgcolor='#006699'align=center width=8%><font size=3 text color=#FFFFFF>VALOR ACTUAL</font></td>";
    echo "<td bgcolor='#006699'align=center width=5%><font size=3 text color=#FFFFFF>PORC_DESC</font></td>";
    echo "</Tr >";
   
    $query2= " SELECT mtatar,mtacco,mtaart,mtavan,mtafec,mtavac,mtapde "
	        .  "FROM ".$empre1."_000026 " 
	        ." WHERE ".$empre1."_000026.mtacco='".$cco."' "
	        ."   AND ".$empre1."_000026.mtatar='".$tar."' "
	        ."   AND ".$empre1."_000026.mtaart='".$art."' "
	        ."   AND ".$empre1."_000026.mtaest='on'"
	        ." ORDER BY mtacco,mtatar,mtaart"; 
   
    $err2 = mysql_query($query2,$conex);
    $num2 = mysql_num_rows($err2);
   
    for ($i=1;$i<=$num2;$i++)
	 {
	 if (is_int ($i/2))
	  {
	   $wcf="DDDDDD";  // color de fondo
	  }
	 else
	  {
	   $wcf="CCFFFF"; // color de fondo
	  }

	 $row2 = mysql_fetch_array($err2);
	   
	 echo "<Tr >";
	 echo "<td  bgcolor='$wcf' align=left><font size=2>$row2[0]</font></td>";
	 echo "<td  bgcolor='$wcf' align=left><font size=2>$row2[1]</font></td>";
	 echo "<td  bgcolor='$wcf' align=left><font size=2>$row2[2]</font></td>";
	 echo "<td  bgcolor='$wcf' align=center><font size=2>".number_format($row2[3],2,'.',',')."</font></td>";
	 echo "<td  bgcolor='$wcf' align=left><font size=2>$row2[4]</font></td>";
	 echo "<td  bgcolor='$wcf' align=center><font size=2>".number_format($row2[5],2,'.',',')."</font></td>";
	 echo "<td  bgcolor='$wcf' align=center><font size=2>$row2[6]</font></td>";
	 echo "<Tr >";
    }
    echo "</table>";
      	
    }
   }
  }
  
  echo "<table>";

  echo "<tr>"; 
  echo "<td><input type=button value='Cerrar_Ventana' onclick='Cerrar Ventana()'></td>";
  echo "</tr>";
  echo "</table>"; 
    
 }  
}
?>