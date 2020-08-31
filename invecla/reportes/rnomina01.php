<?php
include_once("conex.php");
//Nueva conexion con la Nomina Contratada modificada por Gabriel Agudelo 2018-01-31


$conexD = odbc_connect("queryx7","","") or die(odbc_errormsg());  //Promotora
$wactualiz="1.0 15-Febrero-2018 ";

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
//encabezado("PERSONAL ACTIVO EN CLINICA LAS AMERICAS",$wactualiz,"clinica");

if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{
	echo "<HTML>";
	echo "<HEAD>";
	echo "<TITLE>BIENVENIDO A MATRIX</TITLE>";
	echo "</HEAD>";
	echo "<BODY>";

	function add_ceros($numero,$ceros) 
	{
	 // si numero = 1   y ceros = 3 => 001
	 // si numero = 15  y ceros = 3 => 015
	 // si numero = 154 y ceros = 3 >>> Retorna ERROR pues hay no hay nada que ajustar por eso se controla con el if (strlen($numero) < $ceros)
	 
	 if (strlen($numero) < $ceros)
	 {
	  $order_diez = explode(".",$numero); 
	  $dif_diez = $ceros - strlen($order_diez[0]); 
	  for($m = 0 ; $m < $dif_diez;  $m++) 
	  { 
	   @$insertar_ceros .= 0;
	  } 
	  return $insertar_ceros .= $numero; 
	 }
	 else
	 {
	  $add_ceros = $numero;
	  return $add_ceros;
	 } 
	}
	/*
	putenv("INFORMIXDIR=/informixcsdk");
				putenv("ODBCINI=/etc/odbc.ini");
	$conexD = odbc_connect('nomina','','') or die("No se ralizo Conexion con la base de datos de Nomina en Unix");
	*/
	

	echo "<center><table border=0>";
	echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=4> <i>INFORME GENERAL DE PERSONAL ACTIVO</font></b><br>";
	echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2> <i>PROGRAMA: rnomina01.php Ver. 2018/01/31<br>AUTOR: JairS</font></b><br>";
	echo "</table>";
		   
	echo "<br>";
	echo "<table border=0>";
	echo "<tr>";
	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Nro<b></td>";
	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Cedula<b></td>";
	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Codigo<b></td>";
	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>1er Apellido<b></td>";
	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>2do Apellido<b></td>";
	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>1er Nombre<b></td>";
	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>2do Nombre<b></td>";
	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Fec.Ingreso<b></td>";
	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>CCosto<b></td>";
	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Cargo<b></td>";

	echo "</tr>"; 

	$i = 1;
	$query = "Select perced,percod,perap1,perap2,perno1,perno2,perfin,cconom,ofinom";
	$query = $query." From noper,cocco,noofi";
	$query = $query." Where percco = ccocod  and";
	$query = $query."       perofi = oficod  and";
	$query = $query."       peretr = 'A'";
	$query = $query."       order by perfin,cconom,perap1 desc";
	
	$resultado = odbc_do($conexD,$query);		
while (odbc_fetch_row($resultado)) 
{  
	       if (is_int ($i/2))  // Cuando la variable $i es par coloca este color 
	        $wcf="#99CCCC";  
	   	   else
	   	    $wcf="#66CCCC";    	
	   	    
		   echo "<td colspan=2 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=3>".$i."</td>";	    		 	
		   echo "<td colspan=2 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=3>".odbc_result($resultado,1)."</td>";
		   echo "<td colspan=2 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=3>".odbc_result($resultado,2)."</td>";	  	   
		   echo "<td colspan=2 align=CENTER bgcolor=".$wcf."><font text color=#003366 size=3>".odbc_result($resultado,3)."</td>";
		   echo "<td colspan=2 align=CENTER bgcolor=".$wcf."><font text color=#003366 size=3>".odbc_result($resultado,4)."</td>";
		   echo "<td colspan=2 align=CENTER bgcolor=".$wcf."><font text color=#003366 size=3>".odbc_result($resultado,5)."</td>";
		   echo "<td colspan=2 align=CENTER bgcolor=".$wcf."><font text color=#003366 size=3>".odbc_result($resultado,6)."</td>";
		   echo "<td colspan=2 align=CENTER bgcolor=".$wcf."><font text color=#003366 size=3>".odbc_result($resultado,7)."</td>";
		   echo "<td colspan=2 align=CENTER bgcolor=".$wcf."><font text color=#003366 size=3>".odbc_result($resultado,8)."</td>";
		   echo "<td colspan=2 align=CENTER bgcolor=".$wcf."><font text color=#003366 size=3>".odbc_result($resultado,9)."</td>";

           echo "</tr>";	   
           $i++;  
           
           
         
}
	echo "</table>";
	echo "</HTML>";	
	echo "</BODY>";
	// odbc_close($conexD);

	odbc_close($conexD);
	odbc_close_all();
}    
?>


