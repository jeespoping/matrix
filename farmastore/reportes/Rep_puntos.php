<html>

<head>
		<title>PROGRAMA DE PUNTOS</title>
		
<script type="text/javascript">
<!--
	function nuevoAjax()
	{ 
		var xmlhttp=false; 
		try 
		{ 
			xmlhttp=new ActiveXObject("Msxml2.XMLHTTP"); 
		}
		catch(e)
		{ 
			try
			{ 
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); 
			} 
			catch(E) { xmlhttp=false; }
		}
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') { xmlhttp=new XMLHttpRequest(); } 

		return xmlhttp; 
	}
	
		function ajaxquery(fila, entrada, id, opeAnt, criAnt)
	{
		//alert ('hola');
		var x = new Array();

	//me indica que hacer con el id, segun tipo de entrada
	
	switch(entrada)
	{

			case "1":
						document.images['status'].src='/matrix/images/medical/reloj.gif';
						x[1] = id;
						st="Rep_puntos.php?wope="+x[1];
						document.forms.busqueda.w1.value='';
						break;
						
			case "2":
						//VARIABLES RADIO
						document.images['status'].src='/matrix/images/medical/reloj.gif';
						for (i=0;i<document.forms.busqueda.nume.length;i++)
						{
								if (document.forms.busqueda.nume[i].checked==true)
								{
										x[1]=document.forms.busqueda.nume[i].value;
										x[2]=	document.getElementById("w1").value;  
										st="Rep_puntos.php?wope="+x[1]+"&wcri="+x[2];
										break;
								}
						}
						break;
					
				case "3": //para modificar los datos
									x[1] = id;
									x[2] = document.forms['resultados['+fila+']'].elements['ced['+fila+']'].value;
									x[3] = document.forms['resultados['+fila+']'].elements['nom['+fila+']'].value;
									x[4] = document.forms['resultados['+fila+']'].elements['tel['+fila+']'].value;
									x[5] = document.forms['resultados['+fila+']'].elements['tar['+fila+']'].value;
									x[6] = document.forms['resultados['+fila+']'].elements['ide['+fila+']'].value;
									x[7]= fila;
									x[8]= opeAnt;
									x[9]= criAnt;
									st="Rep_puntos.php?wacc="+x[1]+"&wced="+x[2]+"&wnom="+x[3]+"&wtel="+x[4]+"&wtar="+x[5]+"&wide="+x[6]+"&wdato="+x[7]+"&wope="+x[8]+"&wcri="+x[9];
									break;
									
				case "4": //para eliminar un registro
									document.images['status'].src='/matrix/images/medical/reloj.gif';
									x[1] = id;
									x[2] = document.forms['resultados['+fila+']'].elements['ced['+fila+']'].value;
								 x[3] = document.forms['resultados['+fila+']'].elements['cau['+fila+']'].value;
								 x[4] = document.forms['resultados['+fila+']'].elements['red['+fila+']'].value;
								 x[5] = document.forms['resultados['+fila+']'].elements['dev['+fila+']'].value;
								 x[6] = document.forms['resultados['+fila+']'].elements['acu['+fila+']'].value;
								 x[7]= fila;
								 x[8]= opeAnt;
								 x[9]= criAnt;
								 x[10] = document.forms['resultados['+fila+']'].elements['ide['+fila+']'].value;
									st="Rep_puntos.php?wacc="+x[1]+"&wced="+x[2]+"&wcau="+x[3]+"&wred="+x[4]+"&wdev="+x[5]+"&wacu="+x[6]+"&wdato="+x[7]+"&wope="+x[8]+"&wcri="+x[9]+"&wide="+x[10];
									break;
						
				case "5": //para eliminar un registro
									document.images['status'].src='/matrix/images/medical/reloj.gif';
									x[1] = id;
									x[2] = document.forms['resultados['+fila+']'].elements['ced['+fila+']'].value;
								 x[3] = document.forms['resultados['+fila+']'].elements['cau['+fila+']'].value;
								 x[4] = document.forms['resultados['+fila+']'].elements['red['+fila+']'].value;
								 x[5] = document.forms['resultados['+fila+']'].elements['dev['+fila+']'].value;
								 x[6] = document.forms['resultados['+fila+']'].elements['acu['+fila+']'].value;
								 x[7]= fila;
								 x[8]= opeAnt;
								 x[9]= criAnt;
								 x[10] = document.forms['resultados['+fila+']'].elements['ide['+fila+']'].value;
									st="Rep_puntos.php?wacc="+x[1]+"&wced="+x[2]+"&wcau="+x[3]+"&wred="+x[4]+"&wdev="+x[5]+"&wacu="+x[6]+"&wdato="+x[7]+"&wope="+x[8]+"&wcri="+x[9]+"&wide="+x[10];
									fila='2';
									break;
	}

			ajax=nuevoAjax();
		 ajax.open("GET", st, true);
		 ajax.onreadystatechange=function() 
		 {
			 
			if (ajax.readyState==4)
			{ 
				document.images['status'].src='/matrix/images/medical/blanco.png';
				document.getElementById(+fila).innerHTML=ajax.responseText;
				
			} 
		}
		ajax.send(null);
	}

function MsgOkCancel(fila, entrada, id, opeAnt, criAnt) 
{ 
var fRet; 

	switch(id)
	{

			case "1":
				fRet = confirm('Estas seguro que desea modificar la cedula o sus puntos?'); 
					break;
			case "2":
				fRet = confirm('Estas seguro que desea eliminar el registro?'); 
					break;
	}
if (fRet==true)
{
	ajaxquery(fila, entrada, id, opeAnt, criAnt);
}
} 
	
//-->

</script>
</head>

<body>

<?php
include_once("conex.php");
	
			$wbasedato='farstore';

/****************************PROGRAMA************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		

		


			// inicializacion de variables
			$index= "<table border=0 align=RIGHT WIDTH='40%'>";
  	$index= $index. "<tr><td align=LEFT ><img src='/matrix/images/medical/eliminar1.png' >&nbsp;<B><font color='006699' >ELIMINAR REGISTRO</B></td>";
  	$index= $index. "<td align=LEFT >	<img src='/matrix/images/medical/modificar1.png' >&nbsp;<B><font color='006699' >MOVER PUNTOS</B></td>";
  	$index= $index."<td  align=LEFT ><img src='/matrix/images/medical/cambiar1.png' >&nbsp;<B><font color='006699' >CAMBIAR DATOS</B></td><tr>";
			$index= $index. "</table></br></br>";

	//acciones sobre los resultados
	
	if (isset ($wacc) ) //se ha seleccionado una forma de busqueda
	{
			$query = "SELECT clidoc, clite1 FROM farstore_000041 WHERE  id = '".$wide."' ";
   $res=mysql_query($query,$conex);
   $ant = mysql_fetch_array($res);
   
			switch ($wacc) 
				{
     			case '1':
         		
         		if ($ant[0]==$wced)
         		{
	         				//se modifican los puntos para la cedula
	         				$saldo=$wcau+$wred-$wdev;
	         				echo $index;
	         				if ($saldo==$wacu)
	         				{
	         			 		$query="update farstore_000060 set salcau='".$wcau."', salred='".$wred."', saldev='".$wdev."', salsal='".$wacu."'  where saldto='".$ant[0]."' ";
		         	 			$res=mysql_query($query,$conex);
	         						echo "<font size=3 color=#FFFFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#009999 LOOP=-1>Se han modificado los puntos para la cedula: ".$ant[0]."</MARQUEE></FONT></br></br>";
	         	 		}else
		         	 			echo "<font size=3 color=#000080><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#ffcc99 LOOP=-1>No pueden modificarse los puntos para la cedula: ".$ant[0]." pues el saldo no concuerda</MARQUEE></FONT></br></br>";
         		}else
	         	{
		         	 	$query = "SELECT salcau, salred, saldev, salsal FROM farstore_000060 WHERE  saldto='".$wced."'  ";
         					$err=mysql_query($query,$conex);
         					$num3 = mysql_num_rows($err);
         					$row = mysql_fetch_array($err);
         						if ($num3>0)
         						{	//se suman puntos a una cedula ya existente, desaparece la anterior
         								$salcau=$row[0]+$wcau;
         								$salred=$row[1]+$wred;
         								$saldev=$row[2]+$wdev;
         								$salsal=$row[3]+$wacu;
	         							$query="update farstore_000060 set salcau='".$salcau."', salred='".$salred."', saldev='".$saldev."', salsal='".$salsal."'  where saldto='".$wced."' ";
		         	 				$res=mysql_query($query,$conex);
		         	 				$query="delete from farstore_000060 where saldto='".$ant[0]."' ";
		         	 				$res=mysql_query($query,$conex);
         								$query="delete from farstore_000041 where clidoc='".$ant[0]."' ";
		         	 				$res=mysql_query($query,$conex);
		         	 				echo $index;
		         	 				echo "</br>";
	         							echo "<font size=3 color=#FFFFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#009999 LOOP=-1>Se han trasladado los puntos de la cedula: ".$ant[0]." a la cedula: ".$wced."</MARQUEE></FONT></br></br>";
	         					}else
	         					{  // se cambia el numero de una cedula para el registro
		         						$query="update farstore_000041 set clidoc='".$wced."' where clidoc=".$ant[0]." ";
		         	 				$res=mysql_query($query,$conex);
	         							$query="delete from farstore_000060 where saldto='".$ant[0]."' ";
		         	 				$res=mysql_query($query,$conex);
  																$query= " INSERT INTO  farstore_000060 (medico, Fecha_data, Hora_data, saldto, salcau, salred, saldev, salsal, seguridad)"; 
																		$query= $query. "VALUES ('farstore','".date("Y-m-d")."','".date("h:i:s")."','".$wced."', '".$wcau."','".$wred."', '".$wdev."','".$wacu."', '".$user."') ";
		         	 					$res=mysql_query($query,$conex);
		         	 					echo $index;
	         								echo "<font size=3 color=#FFFFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#009999 LOOP=-1>Se ha modificado el numero de la cedula ".$ant[0]." a la cedula ".$wced." </MARQUEE></FONT></br>";
		         				}
		         				$query="update farstore_000059 set pundto='".$wced."' where pundto='".$ant[0]."' ";
		         	 		$res=mysql_query($query,$conex);
		         }
         		break;
        	  case '2': //se elimina el registro y si es el unico el numero de cedula
         								$query = "SELECT * FROM farstore_000041 WHERE  clidoc = '".$ant[0]."' ";
         								$res=mysql_query($query,$conex);
         								$num2 = mysql_num_rows($res);
         								$query="delete from farstore_000041 where id = '".$wide."' ";
		         	 				$res=mysql_query($query,$conex);
         								if ($num2==1)
         								{
	         								$query="delete from farstore_000060 where saldto='".$ant[0]."' ";
		         	 					$res=mysql_query($query,$conex);
		         	 					echo "<font size=3 color=#FFFFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#009999 LOOP=-1>La cedula ".$ant[0]." ha sido eliminada</MARQUEE></FONT></br>";
         								}else
         									echo "<font size=3 color=#FFFFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#009999 LOOP=-1>Se ha eliminado el registro con cedula ".$ant[0]." y telefono: ".$ant[1]." </MARQUEE></FONT></br>";
         		$senal=1;
         		break;
         case '3':
         		$query = "SELECT * FROM farstore_000041 WHERE  clidoc='".$ant[0]."' and clite1='".$wtel."' and id <> '".$wide."' ";
         		$res=mysql_query($query,$conex);
         		$num2 = mysql_num_rows($res);
         		if ($num2>0)
         		{
	         			echo "<font size=3 color=#000080><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#ffcc99 LOOP=-1>Ya existe un registro con la cedula y el telefono ingresado, por favor unifique los datos realizando la consulta por cedula</MARQUEE></FONT></br></br>";
	         			
	         	}else
	         	{
		         	 $query="update farstore_000041 set clinom='".$wnom."', clite1='".$wtel."', clipun='".$wtar."' where id=".$wide." ";
		         	 $res=mysql_query($query,$conex);
         				echo "<font size=3 color=#FFFFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#009999 LOOP=-1>Se han modificado los datos para cedula:".$wced." y telefono:".$wtel."</MARQUEE></FONT></br></br>";
         		}
         				$query = "SELECT A.saldto, A.salcau, A.salred, A.saldev, A.salsal, B.clinom, B.clite1, B.clipun, B.id FROM farstore_000060 A, farstore_000041 B WHERE  B.id=".$wide." and A.Saldto=B.Clidoc ";
         		  $err = mysql_query($query,$conex);
													$num = mysql_num_rows($err);
													$senal=1;
         		break;
     }
	}else
		$wdato=3;

		
if (isset ($wope) and !isset($senal)) //se ha seleccionado una forma de busqueda
	{
		
			switch ($wope) 
				{
     			case '1':
     						$query = "SELECT A.saldto, A.salcau, A.salred, A.saldev, A.salsal, B.clinom, B.clite1, B.clipun, B.id FROM farstore_000060 A, farstore_000041 B WHERE  A.Saldto=B.Clidoc and B.Clipun<>'' and B.Clipun<>'000000' order by B.clidoc";
         		break;
        	case '2':				
										$query = "SELECT A.saldto, A.salcau, A.salred, A.saldev, A.salsal, B.clinom, B.clite1, B.clipun, B.id FROM farstore_000060 A, farstore_000041 B WHERE  A.Saldto=B.Clidoc and B.clidoc='".$wcri."' and B.Clipun<>'' and B.Clipun<>'000000' order by B.clidoc";
         		break;
         case '3':
									$query = "SELECT A.saldto, A.salcau, A.salred, A.saldev, A.salsal, B.clinom, B.clite1, B.clipun, B.id FROM farstore_000060 A, farstore_000041 B WHERE  A.Saldto=B.Clidoc and  B.clipun='".$wcri."' and B.Clipun<>'' and B.Clipun<>'000000' order by B.clidoc";
         		break;
         case '4':
									$query = "SELECT A.saldto, A.salcau, A.salred, A.saldev, A.salsal, B.clinom, B.clite1, B.clipun, B.id FROM farstore_000060 A, farstore_000041 B WHERE  A.Saldto=B.Clidoc and B.clite1='".$wcri."' and B.Clipun<>'' and B.Clipun<>'000000' order by B.clidoc";
         		break;
         case '5':
									$query = "SELECT A.saldto, A.salcau, A.salred, A.saldev, A.salsal, B.clinom, B.clite1, B.clipun, B.id FROM farstore_000060 A, farstore_000041 B WHERE  A.Saldto=B.Clidoc and B.clinom like '%".$wcri."%' and B.Clipun<>'' and B.Clipun<>'000000' order by B.clidoc";
         		break;		   		
     }	

				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
	}

if (!isset ($wope) and !isset ($wacc)) //no se ha realizado ninguna accion sobre la pagina, carga la primera vez
{

			// PRESENTACION HTML
			
	echo "<div id='1'>";
 	
 		echo "<center><table border=2>";
  			echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
  			echo "<tr><td align=center bgcolor='006699'><font size=6 text color=#FFFFFF ><b>PROGRAMA DE PUNTOS</b></font></td></tr>";
  	echo "</table></BR>";
  
  	echo "<form name='busqueda'  method=post>";
  	$fila='ajaxquery("2","2","0", "0", "0")';
   echo "<table border=0 align=center >";
  			echo "<tr><td><table border=(1) align=center bgcolor='006699'>";
  					echo "<tr><td colspan=4 align=center ><font color=#FFFFFF ><B>BUSCAR :</B></td></tr>";
  					echo "<tr><td colspan=4 align=center ><input type='TEXT' name='w1'  id ='w1' size=30 maxlength=30 ></td></tr>";
							echo "<tr><td><input type='radio' name='nume' value=2 onclick='".$fila."' ><font color=#FFFFFF >Cedula&nbsp&nbsp&nbsp&nbsp&nbsp</td>";
							echo "<td><input type='radio' name='nume' onclick='".$fila."' value=3 color=#FFFFFF><font color=#FFFFFF >Numero de tarjeta&nbsp&nbsp&nbsp&nbsp&nbsp</td>";
							echo "<td><input type='radio' name='nume' onclick='".$fila."' value=4 checked><font color=#FFFFFF >Teléfono&nbsp&nbsp&nbsp&nbsp&nbsp</td>";
							echo "<td><input type='radio' name='nume' onclick='".$fila."' value=5 checked><font color=#FFFFFF >Nombre&nbsp&nbsp&nbsp&nbsp&nbsp</td></tr>";
					echo "</table></td>";
				echo "</form>";
		
		 		echo "<td><table border=(1) align=center bgcolor='006699'>";
		 		$fila='ajaxquery("2","1","1", "0", "0")';
  				echo "<tr><td colspan=3 align=center><a href='#' onclick='".$fila."'><b><font color=#FFFFFF >VISULAIZAR LISTA DE TODOS LOS CLIENTES</font></B></a></td></tr>";

					echo "</table></td></tR>";
			echo "</table>";
		echo "	<img src='/matrix/images/medical/blanco.png' name='status' WIDTH=50 HEIGHT=50>";

		echo "</div>";
}

if (!isset ($wcri))
	$wcri=0;
	
	if (!isset ($wope))
	$wope=0;

	
echo "<div id='2'>";

	if (isset ($num) and $num>0) // CASO DE CONSULTA DE TODOS LOS CLIENTES
	{	
		if (!isset($wacc))
		{
				echo $index;
				echo "<center ><B><font color='006699' >RESULTADOS (".$num.")</B></center>"; 	
				echo "<hr></br></br>";
		}
			
			for ($i=$wdato;$i<$num+$wdato;$i++)
				{
					
							$row = mysql_fetch_array($err);
							echo "<div id='".$i."'>";
									
									echo "<form name='resultados[".$i."]' action='Rep_puntos.php' method=post>";
							
									if (is_int ($i/2))
									{
											$wcf="#6699cc";
											$wcf2='#99cccc';
									}
									else
									{
								
											$wcf='006699';
											$wcf2='#009999';
									}
						
						echo "<table border=(1) align=center id='".$i."' >";
								$fila='MsgOkCancel("'.$i.'","4", "2", "'.$wope.'", "'.$wcri.'")';
								echo "<tr><td  align=left bgcolor='".$wcf."' ><font ><a  onclick='".$fila."'><B><img src='/matrix/images/medical/eliminar1.png' ></a>&nbsp&nbsp&nbsp&nbsp<font color=#FFFFFF ><B>CEDULA:</B><input type='TEXT' name='ced[".$i."]'    size=15 value='".$row[0]."'></td>";
								echo "<td bgcolor='".$wcf."'  align=center colspan=5><font color=#FFFFFF ><B> PTS CAUSADOS:&nbsp;</B><input type='TEXT' name='cau[".$i."]'  size=5 value='". $row[1]."'>";
  						echo "<B>&nbsp;&nbsp;REDIMIDOS:&nbsp;</B><input type='TEXT'  name='red[".$i."]'  size=5 value='". $row[2]."'>";
								echo "<B>&nbsp;&nbsp;DEVUELTOS:&nbsp;</B><input type='TEXT' name='dev[".$i."]'  size=5 value='". $row[3]."'>";
								echo "<B>&nbsp;&nbsp;ACUMULADOS:&nbsp;</B><input type='TEXT' name='acu[".$i."]'  size=5 value='". $row[4]."'> </td>";
								$fila='MsgOkCancel("'.$i.'","5", "1", "'.$wope.'", "'.$wcri.'")';
								echo "<td align=center ><font ><a onclick='".$fila."'><B><img src='/matrix/images/medical/modificar1.png' ></a></B></td>";
								
								
					
								echo "<tr  bgcolor='".$wcf2."'>";
								echo "<td  align=center COLSPAN=4><font color=#FFFFFF ><B>NOMBRE</B></td>";
								echo "<td  align=center ><font color=#FFFFFF ><B>TELFONO</B></td>";
								echo "<td  align=center ><font color=#FFFFFF ><B>N TARJETA</B></td>";
								echo "<td  align=center COLSPAN='2'><font color=#FFFFFF ><B>&nbsp;</B></td>";
								echo "</TR>";
						
					
								echo "<tr  bgcolor='#FFFFFF' >";
  						echo "<td  align=center COLSPAN=4><font color=#FFFFFF ><B><input type='TEXT' name='nom[".$i."]'  size=40 value='". $row[5]."'></B></td>";
								echo "<td  align=center ><font color=#FFFFFF ><B><input type='TEXT' name='tel[".$i."]'  size=20 value='". $row[6]."'></B></td>";
								echo "<td  align=center ><font color=#FFFFFF ><B><input type='TEXT' name='tar[".$i."]'  size=20 value='". $row[7]."'></B></td>";
								echo "<input type='HIDDEN' name= 'ide[".$i."]'  value='".$row[8]."'>";
								
								$fila='ajaxquery("'.$i.'","3","3", "'.$wope.'", "'.$wcri.'")';
								echo "<td align=center  colspan=2><font color=#FFFFFF ><a onclick='".$fila."'><B><img src='/matrix/images/medical/cambiar1.png' ></a></B></td>";
							

								
						echo "</table></td></br>";
						echo "</form>";
								echo "</div>";
								
								echo "<input type='HIDDEN' name= 'wope'  value='".$wope."'>";
								echo "<input type='HIDDEN' name= 'wcri'  value='".$wcri."'>";
				}
}else if (isset($wope) and $wope!=0 and !isset($senal))
{	
			echo"<CENTER><fieldset style='border:solid;border-color:006699; width=330' ; color=#000080>";
			echo "<table align='center' border=0 bordercolor=006699 width=340 style='border:solid;'>";
			echo "<tr><td  align=center><font size=3 color='#000080' face='arial'><b>No se ha encontrado nungun cliente con los datos ingresados, intente otra busqueda</td><tr>";
			echo "</table></fieldset></form>";
}

echo "</div>";

}
?>
</body>