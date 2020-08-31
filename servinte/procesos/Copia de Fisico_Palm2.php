<html>
<head>
  	<title>MATRIX Programa Para Inventario Fisico x Palm</title>
  	      <link rel="stylesheet" href="/styles.css" type="text/css">
	<style type="text/css">
	<!--
		.BlueThing
		{
			background: #99CCFF;
		}
		
		.SilverThing
		{
			background: #CCCCCC;
		}
		
		.GrayThing
		{
			background: #CCCCCC;
		}
	
	//-->
	</style>
</head>
<body  onload=ira() BGCOLOR="FFFFFF" oncontextmenu = "return false" onselectstart = "return true" ondragstart = "return false">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.Fisico_Palm2.submit();
	}
//-->
</script>
<BODY TEXT="#000066">
<?php

function number_pad($number,$n) {
return str_pad((int) $number,$n,"0",STR_PAD_LEFT); }


/**********************************************************************************************************************  
	   PROGRAMA : Fisico_Palm2.php
	   Fecha de Liberación : 2010-06-10
	   Autor : Ing. Jair Saldarriaga Orozco
	   Version Actual : 2010-06-10
	   
	   OBJETIVO GENERAL : 
	   Este programa permite la grabacion de un inventario fisico x conteo y graba por ODBC en las tablas de Servinte.
	   
	   
	   REGISTRO DE MODIFICACIONES :
	   
	   .2006-06-10
	  	 	Programa Nuevo
	  	 	
***********************************************************************************************************************/
/*
session_start();
if(!session_is_registered("user"))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
*/	
	echo "<form name='Fisico_Palm2' action='Fisico_Palm2.php' method=post>";
	/*
	include("conex.php");
	mysql_select_db("matrix");
	*/
	$conexinv = odbc_connect('inventarios','','') or die("No se ralizo Conexion");

	
	/*echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";*/
	if(isset($ok) and $wcan >= 0 and $wart != "" and $wdes != "")
	{
		$wsw=0;
		/*$query = "SELECT Artnom from ".$empresa."_000001 where Artcod='".$wart."'"; */
		$query = "SELECT Artnom from ivart where Artcod='".$wart."'"; 		
		$err = odbc_do($conexinv,$query);
		if (odbc_fetch_row($err))
			$wsw=1;
			
		if($wsw == 1)   
		{
		    $fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");			
			 /*$query = " INSERT ".$empresa."_000015(Medico,Fecha_data,Hora_data, Fisano, Fismes, Fiscco, Fisfec, Fisart, Fiscon, Fiscan, Fisest, Seguridad) values ('".$empresa."','".$fecha."','".$hora."','".date("Y")."','".date("m")."','".substr($wcco,0,strpos($wcco,"-"))."','".$wfec."','".$wart."',".$wcon.",".$wcan.", 'on'  , 'C-".$key."')";*/
              if ($wcon == 1)    /* Es primera vez ==> Inserto	*/
              {
                //Verifico que no exista informacion para este articulo en este anño,mes,servicio,articulo y tipo
                $query = "SELECT * FROM ivfis WHERE fisano='".date("Y")."' and fismes='".date("m")."'";
			    $query = $query." and fisser='".substr($wcco,0,strpos($wcco,"-"))."' and fisart='".$wart."' and fistip='G'";	
			    $err = odbc_do($conexinv,$query);	
                if (odbc_fetch_row($err))
                 echo "<font size=1><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF9900 LOOP=-1>ERROR YA EXISTE CONTEO 1 PARA EL ARTICULO!!!!</MARQUEE></FONT>";
                else
                {
                  //Busco en que nro va la tarjeta para este año mes
                  $query = "SELECT MAX(fistaj) FROM ivfis WHERE fisano='".date("Y")."' AND fismes='".date("m")."'";
                  $err = odbc_do($conexinv,$query);	
                  if (odbc_fetch_row($err))
                   $tj=odbc_result($err,1)+1;
                  else
                   $tj=1;	
                   
                   $tj = number_pad($tj,6);  //Funcion para rellenar con ceros a la izquierda
              
			       $query = " INSERT INTO ivfis(Fisano, Fismes, Fistaj, Fisser, Fisart, Fiscaa, Fisval, Fistip)";			  
			       $query = $query." VALUES ('".date("Y")."','".date("m")."','".$tj."','".substr($wcco,0,strpos($wcco,"-"))."','".$wart."',".$wcan.",0,'G')"; 
			       				       
			       $err2 = odbc_do($conexinv,$query);
			       if ($err2)
				     echo "<font size=1><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCFF LOOP=-1>DATOS OK!!!!</MARQUEE></FONT>";
				   else
				     echo "<font size=1><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF9900 LOOP=-1>ERROR AL ACTUALIZAR ARTICULO!!!!</MARQUEE></FONT>";
			    }    
			  }
			  else
			  {

                //Verifico que EXISTA informacion para este articulo en este anño,mes,servicio,articulo y tipo
                $query = "SELECT * FROM ivfis WHERE fisano='".date("Y")."' and fismes='".date("m")."'";
			    $query = $query." and fisser='".substr($wcco,0,strpos($wcco,"-"))."' and fisart='".$wart."' and fistip='G'";	
			    $err = odbc_do($conexinv,$query);	
                if (odbc_fetch_row($err))
                {                 
			   		if ($wcon == 2){   /* Conteo 2 ==> UPDATE	*/
			    	 	$query = " UPDATE ivfis SET fiscab = ".$wcan." Where fisano='".date("Y")."' and fismes='".date("m")."'";
				 		$query = $query." and fisser='".substr($wcco,0,strpos($wcco,"-"))."' and fisart='".$wart."' and fistip='G'";
				 		
			   		}
			   		else{	 /* Conteo 3 ==> UPDATE	*/
						$query = " UPDATE ivfis SET fiscan = ".$wcan." Where fisano='".date("Y")."' and fismes='".date("m")."'";
						$query = $query." and fisser='".substr($wcco,0,strpos($wcco,"-"))."' and fisart='".$wart."' and fistip='G'";	
						
				 	}    
					$err2 = odbc_do($conexinv,$query);
					if ($err2)
				 		echo "<font size=1><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCFF LOOP=-1>DATOS OK!!!!</MARQUEE></FONT>";
					else
						echo "<font size=1><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF9900 LOOP=-1>ERROR AL ACTUALIZAR ARTICULO!!!!</MARQUEE></FONT>";
				 }
				 else
				  echo "<font size=1><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF9900 LOOP=-1>ERROR NO EXISTE CONTEO 1 PARA EL ARTICULO!!!!</MARQUEE></FONT>";
			  }
		}
		else
			echo "<font size=1><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>ARTICULO NO EXISTE -- INTENTELO NUEVAMENTE!</MARQUEE></FONT>";
		unset($wart);
		unset($wcan);
	}
	else
		if(isset($wfec) and isset($wcon) and isset($ok))
			echo "<font size=1><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FFFF00 LOOP=-1>ERROR EN LOS DATOS -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
	unset($ok);
	if (!isset($wfec) or !isset($wcon) or $wcon < 1 or $wcon > 3)
	{
		echo "<table border=0 align=left>";
		echo "<tr><td align=center bgcolor=#999999><font size=1><b>GRABACION DE INVENTARIO FISICO</font></td></tr>";
		echo "<tr><td align=center bgcolor=#cccccc><font size=1><b>Ver. 2006-03-01</font></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center><font size=1>FECHA : </font></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center><font size=1><input type='TEXT' name='wfec' size=10 maxlength=10 value='".date("Y-m-d")."'></font></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center><font size=1>CONTEO : </font></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center><font size=1><input type='TEXT' name='wcon' size=2 maxlength=2></font></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center><font size=1>C. COSTOS : </font></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>";
		$query = "SELECT count(*) from ivser where seract='S'";
		$err = odbc_do($conexinv,$query);
		$num = odbc_result($err,1);	
	
		if ($num>0)
		{
			$query = "SELECT sercod, sernom  from ivser where seract='S' order by sercod";
			$err = odbc_do($conexinv,$query);

			echo "<select name='wcco'>";
			for ($i=0;$i<$num;$i++)
			{
				$row = odbc_fetch_row($err);
				if($wccoo == $row[1]."-".$row[2])
					echo "<option selected><font size=1>".odbc_result($err,1)."-".odbc_result($err,2)."</font></option>";
				else
					echo "<option><font size=1>".odbc_result($err,1)."-".odbc_result($err,2)."</font></option>";
			}
			echo "</select>";
		}
		echo "</td></tr>";
		echo "<tr><td align=center bgcolor=#cccccc><input type='submit' value='Ok'></td></tr>"; 
		echo "</table>";  
	}
	else
	{
		$wdes="";
		$wcolor="#cccccc";
		echo "<table border=0 align=leff>";
		echo "<tr><td align=center bgcolor=#999999><font size=1><b>GRABACION DE INVENTARIO FISICO</font></td></tr>";
		echo "<tr><td align=center bgcolor=#cccccc colspan=1><font size=1><b>Ver. 2006-03-01</font></td></tr>";
		echo "<tr><td bgcolor=#dddddd align=center><font size=1>ARTICULO : </font></td></tr>";
		if(isset($wart) and $wart != "")
		{
			?>
			<script>
				function ira(){document.Fisico_Palm2.wcan.focus();}
			</script>
			<?php
			echo "<tr><td bgcolor=#dddddd align=center><input type='TEXT' name='wart' size=20 maxlength=20 value='".$wart."'></td></tr>";
			/*$query = "SELECT Artnom from ".$empresa."_000001 where Artcod='".$wart."'";*/
			$query = "SELECT Artnom from ivart where Artcod='".$wart."'";
			$err = odbc_do($conexinv,$query);
			if (odbc_fetch_row($err))			
			{
				echo "<tr><td bgcolor=#cccccc align=center><font size=1>DESCRIPCION : ".odbc_result($err,1)."</font></td></tr>";
				$wdes=odbc_result($err,1);
				
			}
			else
			{
				// ***** AQUI BUSQUEDA X CODIGO DE PROVEEDOR *****
				/*$query = "SELECT Axpart from ".$empresa."_000009 where Axpcpr='".$wart."'";*/
				
				$query = "SELECT count(*) from ivartcba where artcbacba='".$wart."'";
				$err = odbc_do($conexinv,$query);
				if (odbc_fetch_row($err)) 
				{
				 $num = odbc_result($err,1);	
				
				 $query = "SELECT artcbaart,artcbanom from ivartcba where artcbacba='".$wart."'";
				 $err = odbc_do($conexinv,$query);
				 if ($num>0)
				 {
					for ($i=0;$i<$num;$i++)
					{						
						echo "<tr><td bgcolor=#cccccc align=center><font size=1>DESCRIPCION : ".odbc_result($err,2)."</font></td></tr>";
						//$wart=substr(odbc_result($err,1),0,strpos(odbc_result($err,1),"-"));
						//$wdes=substr(odbc_result($err,1),strpos(odbc_result($err,1),"-")+1);
						 $wart=odbc_result($err,1);
						 $wdes=odbc_result($err,2);;
						echo "<input type='HIDDEN' name= 'wart' value='".$wart."'>";
					}
				 }
				 else
				 {
					echo "<tr><td bgcolor=#cccccc align=center><font size=1>ERROR - ARTICULO NO EXISTE !!!! </font></td></tr>";
					$wart="";
					$wdes="";
					echo "<input type='HIDDEN' name= 'wcan' value='".$wart."'>";
				 }
                }
				else
				{
					echo "<tr><td bgcolor=#cccccc align=center><font size=1>ERROR - ARTICULO NO EXISTE !!!! </font></td></tr>";
					$wart="";
					$wdes="";
					echo "<input type='HIDDEN' name= 'wcan' value='".$wart."'>";
				}
			}
		}
		else
		{
			?>
			<script>
				function ira(){document.Fisico_Palm2.wart.focus();}
			</script>
			<?php
			echo "<tr><td bgcolor=#dddddd align=center><input type='TEXT' name='wart' size=20 maxlength=20></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center><font size=1>DESCRIPCION : </font></td></tr>";
		}
		echo "<tr><td bgcolor=#dddddd align=center><font size=1>CANTIDAD : </font></td></tr>";
		$wuni="";
		if(isset($wart) and $wart != "")
		{
			$query = "SELECT Artuni from ivart where Artcod='".$wart."'";
			
			$err = odbc_do($conexinv,$query);
			if (odbc_fetch_row($err))			
			{
					$wuni=odbc_result($err,1);
			}
		}
		if(!isset($wcan))
			echo "<tr><td bgcolor=#dddddd align=center><input type='TEXT' name='wcan' size=5 maxlength=10><font size=1> Unidad : ".$wuni."</font></td></tr>";
		else
			echo "<tr><td bgcolor=#dddddd align=center><input type='TEXT' name='wcan' size=5 maxlength=10 value=".$wcan."><font size=1> Unidad : ".$wuni."</font></td></tr>";
		echo "<input type='HIDDEN' name= 'wfec' value='".$wfec."'>";
		echo "<input type='HIDDEN' name= 'wcon' value='".$wcon."'>";
		echo "<input type='HIDDEN' name= 'wcco' value='".$wcco."'>";
		echo "<input type='HIDDEN' name= 'wdes' value='".$wdes."'>";
		echo "<tr><td align=center bgcolor=#dddddd><font size=1><b>GRABAR</b></font><input type='checkbox' name='ok'  onclick='enter()'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><input type='submit' value='Ok'></td></tr>";
		echo "</table><br><br>"; 
	}
//}
?>