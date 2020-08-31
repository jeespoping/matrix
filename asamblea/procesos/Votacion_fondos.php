<html>
<head>
  	<title>MATRIX Proceso de Votación Asambleas Fondos</title>
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
<body onload=ira() BGCOLOR="FFFFFF" oncontextmenu = "return false" onselectstart = "return true" ondragstart = "return false">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.proceso.submit();
	}
//-->
</script>
<?php
include_once("conex.php");
/**********************************************************************************************************************  
	   PROGRAMA : Proceso.php
	   Fecha de Liberación : 2007-03-25
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2007-03-25
	   
	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface gráfica que permite grabar el movimiento de
	   informacion generado en las asambleas de accionistas tales como:
	   	1. registro de asistencia
	   	2. registro de delegacion
	   	3. votos para plancha de junta directiva
	   	4. otras votaciones.
	   
	   
	   REGISTRO DE MODIFICACIONES :
	   .2007-03-25
	   		Release de Versión Beta.
	   
***********************************************************************************************************************/

session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='proceso' action='Votacion_fondos.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	
	$wfecha = date("Y-m-d");
	$whora = (string)date("H:i:s");
	
	if (!isset($wcont))
	   $wcont=0;
	     
	   
	//FUNCIONES
	function presente()
	   {
		global $empresa;
		global $conex;
		global $wano;
		global $wmes;
		global $wsocio;
		global $wnit;
		global $wfecha;
		global $whora;
		 
		   
		//Verifico que este presente   
		$q = " SELECT COUNT(*) "
		    ."   FROM ".$empresa."_000005 "
		    ."  WHERE movemp = '".$wnit."'"
		    ."    AND movano = ".$wano
		    ."    AND movmes = ".$wmes
		    ."    AND movcpa = 'PR' "
		    ."    AND movcac = '".$wsocio."'"; 
		$res = mysql_query($q,$conex);
		$row = mysql_fetch_array($res);
		
		if ($row[0] > 0)
		   return true;
		  else
		     return false; 
       }
       
       
    function no_ha_votado($wtipvot)
	   {
		global $empresa;
		global $conex;
		global $wano;
		global $wmes; 
		global $wsocio;
		global $wnit;  
		   
		//Verifico que este presente   
		$q = " SELECT COUNT(*) "
		    ."   FROM ".$empresa."_000005 "
		    ."  WHERE movemp = '".$wnit."'"
		    ."    AND movano = ".$wano
		    ."    AND movmes = ".$wmes
		    ."    AND movcpa = '".$wtipvot."'"
		    ."    AND movcac = '".$wsocio."'"; 
		$res = mysql_query($q,$conex);
		$row = mysql_fetch_array($res);
		
		if ($row[0] == 0)
		   return true;
		  else
		     return false; 
	   }   
	
	$color="#dddddd";
	
	//Busco que empresa esta activa, porque solo puede haber una activa al mismo tiempo
	$q = "SELECT Empcod, Empdes "
	    ."  FROM ".$empresa."_000004 "
	    ." WHERE Empact='on' ";
	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);
	if ($num == 1)                                   //SI Entra indica que hay una empresa activa
	  {
		$row = mysql_fetch_array($res);
		
		echo "<center><table>";
		
		
		$wnit=$row[0];
		$wnomemp=$row[1];
		echo "<input type='HIDDEN' name= 'wnit' value='".$wnit."'>";
		$wnnit=$row[1];
		
		//Traigo el periodo que esta abierto para la empresa activa
		$q = " SELECT Perano, Permes "
		    ."   FROM ".$empresa."_000003 "
		    ."  WHERE Peremp='".$wnit."' "          //Empresa activa
		    ."    AND Peract='on' ";
		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);
		if ($num == 1)                              //SI entra Indica que hay un periodo abierto
		   {
			$row = mysql_fetch_array($res);
			
			$wano=$row[0];
			$wmes=$row[1];  
			
			echo "<tr><td align=center colspan=3><IMG SRC='/matrix/images/medical/asamblea/logo_".$wnit.".png'></td></tr>";
			echo "<tr><td align=right colspan=3><font size=2>Powered by :  MATRIX </font></td></tr>";
			echo "<tr><td align=center bgcolor=#000066 colspan=3><font color=#ffffff size=6><b>".$wnomemp."</font></td></tr>";
			echo "<tr><td align=center bgcolor=".$color." colspan=3><font color=#000066 size=5><b>ASAMBLEA GENERAL DE SOCIOS</font></b></font></td></tr>";
			echo "<tr><td align=center bgcolor=".$color." colspan=3><font color=#000066 size=5><b>Registro de Votación</font><font color=#33CCFF size=2>&nbsp&nbsp&nbspVer. 2010-03-16</font></td></tr>"; 
			   
			//Traigo todas votaciones abiertas para la empresa activa
			$q = " SELECT Parcod, Pardes, Parval "
			    ."   FROM ".$empresa."_000002 "
			    ."  WHERE Paremp='".$wnit."' "
			    ."    AND Paract='on' "
			    ."    AND Parcie='off' ";
			$res = mysql_query($q,$conex);
			$num = mysql_num_rows($res);
			if ($num > 0)
			  {
				if (isset($wsocio) and trim($wsocio)!="")
				   {
					if (presente())
					   {
						$row = mysql_fetch_array($res);   //solo para saber que codigo de votacion es
						if (no_ha_votado($row[0]))        //Averiguo si no ha votado, para el tipo de votacion que se le envia
						   {
							//Inserto la votacion   
							$q = "INSERT ".$empresa."_000005 (   medico        ,fecha_data  ,   hora_data  ,   Movemp  ,  Movano , Movmes  ,   Movcac    ,   Movcpa    , Movdel,   Movval     , seguridad) "
							    ."                    VALUES ('".$empresa."','".$wfecha."'  ,'".$whora."'  ,'".$wnit."',".$wano.",".$wmes.",'".$wsocio."','".$row[0]."', 'NO'  ,'".$wrad[(integer)$row[0]]."','C-".$key."')";   
							$res1 = mysql_query($q,$conex);  
							
							$wcont++;  
						   }
					      else
					         {
						      ?>	    
						       <script>
						          alert ("YA VOTO");     
						       </script>
						      <?php    
					         }
					    mysql_data_seek($res,0);   	     //Devuelvo el puntero  
				       }	   
					  else
					     {
					      ?>	    
					       <script>
					          alert ("NO ESTA PRESENTE NI REPRESENTADO");     
					       </script>
					      <?php    
				         }
					
			       }
			     	   
				  
				for ($i=0;$i<$num;$i++)
				  {
					$row = mysql_fetch_array($res);
					
					$wsw=(integer)$row[0];
					$opt=explode("-",$row[2]);
					$n  =count($opt);
					
					echo "<tr><td bgcolor=".$color." align=center><b>".$row[1].":</b></td><td bgcolor=".$color.">";
					for ($j=0;$j<$n;$j++)
					  { 
						$wvaldes="OPCION ".$opt[$j]; 
						
					    //Busco si las opciones tiene discriminado el nombre con que se deben mostrar en pantalla, si si, muestro ese nombre, si no,
					    //por que el nombre OPCION...seguido del nro de la opción.
					    $q = " SELECT vpades "
					        ."   FROM ".$empresa."_000006 "
					        ."  WHERE vpaemp = '".$wnit."'"
					        ."    AND vpacpa = '".$row[0]."'"
					        ."    AND vpacod = '".$opt[$j]."'"
					        ."    AND vpaact = 'on' ";
					    $res_op = mysql_query($q,$conex);
					    $num_op = mysql_num_rows($res_op);
					    if ($num_op > 0)
					       {
						    $row_op = mysql_fetch_array($res_op); 
					        $wvaldes=$row_op[0];
				           } 
					    
					    if (isset($wrad[$wsw]) and $j==($wrad[$wsw]-1))  //Si la j y el valor que viene por el metodo pos son iguales lo coloco como valor por defecto
						   echo "<input type='RADIO' name='wrad[".$wsw."]' value=".$opt[$j]." checked>".$wvaldes." <br>";
						  else
						     echo "<input type='RADIO' name='wrad[".$wsw."]' value=".$opt[$j]." >".$wvaldes."<br>";
					  }
					echo "</td></tr>";
				  }
				  
				?>
				<script>
					function ira(){document.proceso.wsocio.focus();}
				</script>
				<?php
				  
				echo "<tr>";
				echo "<td align=center bgcolor=".$color." colspan=3><b>DOCUMENTO SOCIO: <INPUT TYPE='text' NAME='wsocio'></b></td>";
				echo "</tr>";
			  }
			 else
			    {
				 echo "<tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr>";   
			     echo "<tr><td align=center><font size=5><b>NO HAY VOTACION ABIERTA</b></font></td></tr>"; 
		        } 
			}
	    echo "</table>";	   
	    
	    echo "<br><br><br>";
	    
	    echo "<center><table>";
	    echo "<tr><td><font size=5>Cantidad de Votos Registrados : ".$wcont."</font></td></tr>";
	    echo "</table>";
	    
	    echo "<input type='HIDDEN' name='wcont' value='".$wcont."'>";
	  }
}
?>
</body>
</html>