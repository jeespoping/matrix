<head>
  <title>VENTAS AL PUBLICO - FARSTORE</title>
</head>
<body onload=ira()>
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.ventas.submit();
	}
//-->
</script>
<?php
include_once("conex.php");
  /***************************************************
   *     PROGRAMA PARA LA FACTURACION DE LAS VENTAS  *
   *             A EMPRESAS EN FARMASTORE            *
   ***************************************************/
   
//==================================================================================================================================
//PROGRAMA                   : factura_emp.php
//AUTOR                      : Juan Carlos Hernández M.
//FECHA CREACION             : Abril 28 de 2005
//FECHA ULTIMA ACTUALIZACION :

//DESCRIPCION
//==================================================================================================================================
//Este programa se hace con el objetivo de registrar las ventas de la empresa FARMASTORE, en donde se pueda luego realizar una
//facturación individual o por empresa y además de tener en cuenta que luego de poder facturar se generen los RIPS, además este
//programa tiene en cuenta la actualización del Inventario en línea, grabando también el movimiento de consumo en el inventario,
//El programa en general, tiene en cuenta el tipo de cliente, el responsable de la cuenta, las tarifas de los articulos según la
//empresa y el centro de costo (sucursal). tambien se tiene en cuenta que si la venta es para un particular o el paciente de 
//empresa tiene que pagar salga una ventana en donde se le pide registrar un recibo de caja por el valor pagado.
//==================================================================================================================================
session_start();

if (!isset($user))
	{
	 if(!isset($_SESSION['user']))
		session_register("user");
	}

if(!isset($_SESSION['user']))
	echo "error";
else
{	            
  

			      or die("No se ralizo Conexion");
  

 
  //$conexunix = odbc_pconnect('facturacion','infadm','1201')
  //					    or die("No se ralizo Conexion con el Unix");
  					    

  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user)); 
  
  	                                                           // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="(Versión Abril 28 de 2005)";                     // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                                           // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
	                                                           
  $wfecha=date("Y-m-d");   
  $hora = (string)date("H:i:s");	              
  
  
  echo "<form name='fact_emp' action='factura_emp.php' method=post>";
  
  $wfecha_tempo=$wfecha;
  $whora_tempo=$hora;
  
       
  //===========================================================================================================================================
  //INICIO DEL PROGRAMA   
  //===========================================================================================================================================
  
  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  $wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro
  $wcol=3;
       
  //=======================================================================================================================================
  echo "<center><table border>";
  echo "<tr><td align=center rowspan=2 colspan=1><img src='/matrix/images/medical/farmastore/logo farmastore.png' WIDTH=340 HEIGHT=100></td></tr>";
  //echo "<tr><td align=center colspan=".$wcol." bgcolor=".$wcf2."><font size=5 text color=#FFFFFF><b>FARMASTORE</b></font></td></tr>";
  echo "<tr><td align=center colspan=".$wcol." bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>FACTURACION DE EMPRESAS</b></font></td></tr>";
  echo "<tr>";
  
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //FECHA INICIAL DE LA VENTA
  echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg.">Fecha Inicial de la venta (AAAA-MM-DD): </font></b><INPUT TYPE='text' NAME='wfecha_i'></td>";
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //FECHA FINAL DE LA VENTA
  echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg.">Fecha Final de la venta (AAAA-MM-DD): </font></b><INPUT TYPE='text' NAME='wfecha_f'></td>";
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //SUCURSAL
  //echo "<td align=left bgcolor=".$wcf." colspan=".($wcol-7)."><b><font text color=".$wclfg.">Sucursal: </font></b>".$wnomcco."</td>";
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////   function ira(){document.ventas.wvalfpa[0].focus();}
  
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //EMPRESA A FACTURAR
  $q =  " SELECT empcod, empnit, empnom "
       ."   FROM farstore_000024 "
       ."  ORDER BY empcod ";
        
  $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
  $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;
  echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Empresa a Facturar: <br></font></b><select name='wempresa'>";
  for ($i=1;$i<=$num;$i++)
     {
      $row = mysql_fetch_array($res); 
      echo "<option>".$row[0]." - ".$row[1]." - ".$row[2]."</option>";
     }
  echo "</select></td>";
  
  echo "<tr><td align=center bgcolor=#cccccc colspan=3><input type='submit' value='Ok'></td></tr>";                                   //submit 
  
  echo "</table>";       
}
?>
