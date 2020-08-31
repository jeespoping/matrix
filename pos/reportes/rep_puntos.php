<html>

<head>
		<title>PROGRAMA DE PUNTOS</title>
		
<script type="text/javascript" src="Rep_puntosAjax.js">
</script>
</head>

<body>

<?php
include_once("conex.php");

$empresa='farpmla';

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
	$index= $index. "</table></br></br></br>";

	//acciones sobre los resultados

	if (isset ($wacc) ) //se ha seleccionado una forma de busqueda
	{
		$inicial=strpos($user,"-");
		$aut=substr($user, $inicial+1, strlen($user));


		$query = "SELECT clidoc, clite1 FROM ".$empresa."_000041 WHERE  id = '".$wide."' ";
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
					$query="update ".$empresa."_000060 set salcau='".$wcau."', salred='".$wred."', saldev='".$wdev."', salsal='".$wacu."', seguridad='C-".$aut."'  where saldto='".$ant[0]."' ";
					$res=mysql_query($query,$conex);
					echo "<font size=3 color=#FFFFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#009999 LOOP=-1>Se han modificado los puntos para la cedula: ".$ant[0]."</MARQUEE></FONT></br></br>";
				}else
				echo "<font size=3 color=#000080><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#ffcc99 LOOP=-1>No pueden modificarse los puntos para la cedula: ".$ant[0]." pues el saldo no concuerda</MARQUEE></FONT></br></br>";
			}else //se cambia la cedula
			{
				$query = "SELECT salcau, salred, saldev, salsal FROM ".$empresa."_000060 WHERE  saldto='".$wced."'  ";
				$err=mysql_query($query,$conex);
				$num3 = mysql_num_rows($err);
				$row = mysql_fetch_array($err);
				if ($num3>0)
				{	//se suman puntos a una cedula ya existente, desaparece la anterior
					$salcau=$row[0]+$wcau;
					$salred=$row[1]+$wred;
					$saldev=$row[2]+$wdev;
					$salsal=$row[3]+$wacu;
					$query="update ".$empresa."_000060 set salcau='".$salcau."', salred='".$salred."', saldev='".$saldev."', salsal='".$salsal."', seguridad='C-".$aut."'  where saldto='".$wced."' ";
					$res=mysql_query($query,$conex);
					$query="delete from ".$empresa."_000060 where saldto='".$ant[0]."' ";
					$res=mysql_query($query,$conex);
					$query="delete from ".$empresa."_000041 where clidoc='".$ant[0]."' ";
					$res=mysql_query($query,$conex);
					echo $index;
					echo "</br>";
					echo "<font size=3 color=#FFFFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#009999 LOOP=-1>Se han transladado los puntos de la cedula: ".$ant[0]." a la cedula: ".$wced."</MARQUEE></FONT></br></br>";
				}else
				{  // se cambia el numero de una cedula para el registro
					$query="update ".$empresa."_000041 set clidoc='".$wced."', seguridad='C-".$aut."' where clidoc=".$ant[0]." ";
					$res=mysql_query($query,$conex);
					$query="delete from ".$empresa."_000060 where saldto='".$ant[0]."' ";
					$res=mysql_query($query,$conex);
					$query= " INSERT INTO  ".$empresa."_000060 (medico, Fecha_data, Hora_data, saldto, salcau, salred, saldev, salsal, seguridad)";
					$query= $query. "VALUES ('".$empresa."','".date("Y-m-d")."','".date("h:i:s")."','".$wced."', '".$wcau."','".$wred."', '".$wdev."','".$wacu."', '".$user."') ";
					$res=mysql_query($query,$conex);
					echo $index;
					echo "<font size=3 color=#FFFFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#009999 LOOP=-1>Se ha modificado el numero de la cedula ".$ant[0]." a la cedula ".$wced." </MARQUEE></FONT></br>";
				}

				$query="update ".$empresa."_000059 set pundto='".$wced."' where pundto='".$ant[0]."' ";
				$res=mysql_query($query,$conex);

				$query="update ".$empresa."_000016 set vennit='".$wced."' where vennit='".$ant[0]."' ";
				$res=mysql_query($query,$conex);
			}
			break;
			case '2': //se elimina el registro y si es el unico el numero de cedula
			$query = "SELECT * FROM ".$empresa."_000041 WHERE  clidoc = '".$ant[0]."' ";
			$res=mysql_query($query,$conex);
			$num2 = mysql_num_rows($res);
			$query="delete from ".$empresa."_000041 where id = '".$wide."' ";
			$res=mysql_query($query,$conex);
			if ($num2==1)
			{
				$query="delete from ".$empresa."_000060 where saldto='".$ant[0]."' ";
				$res=mysql_query($query,$conex);
				echo "<font size=3 color=#FFFFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#009999 LOOP=-1>La cedula ".$ant[0]." ha sido eliminada</MARQUEE></FONT></br>";
			}else
			echo "<font size=3 color=#FFFFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#009999 LOOP=-1>Se ha eliminado el registro con cedula ".$ant[0]." y telefono: ".$ant[1]." </MARQUEE></FONT></br>";
			$senal=1;
			break;
			case '3':
			$query = "SELECT * FROM ".$empresa."_000041 WHERE  clidoc='".$ant[0]."' and clite1='".$wtel."' and id <> '".$wide."' ";
			$res=mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
			$num2 = mysql_num_rows($res);
			if ($num2>0)
			{
				echo "<font size=3 color=#000080><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#ffcc99 LOOP=-1>Ya existe un registro con la cedula y el telefono ingresado, por favor unifique los datos realizando la consulta por cedula</MARQUEE></FONT></br></br>";

			}else
			{
				$query="update ".$empresa."_000041 set clinom='".$wnom."', clite1='".$wtel."', clipun='".$wtar."', climai='".$wmai."', clidir='".$wdir."', cliemp='".$wemp."', clicar='".$wcar."', cliocu='".$wocu."', seguridad='C-".$aut."' where id=".$wide." ";
				$res=mysql_query($query,$conex);
				echo "<font size=3 color=#FFFFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#009999 LOOP=-1>Se han modificado los datos para cedula:".$wced." y telefono:".$wtel."</MARQUEE></FONT></br></br>";
			}
			$query = "SELECT A.saldto, A.salcau, A.salred, A.saldev, A.salsal, B.clinom, B.clite1, B.clipun, B.id, B.climai, B.clidir, B.cliemp, B.clicar, B.cliocu FROM ".$empresa."_000060 A, ".$empresa."_000041 B WHERE  B.id=".$wide." and A.Saldto=B.Clidoc ";
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
			$query = "SELECT A.saldto, A.salcau, A.salred, A.saldev, A.salsal, B.clinom, B.clite1, B.clipun, B.id, B.climai, B.clidir, B.cliemp, B.clicar, B.cliocu FROM ".$empresa."_000060 A, ".$empresa."_000041 B WHERE  A.Saldto=B.Clidoc  order by B.clidoc";
			break;
			case '2':
			$query = "SELECT A.saldto, A.salcau, A.salred, A.saldev, A.salsal, B.clinom, B.clite1, B.clipun, B.id, B.climai, B.clidir, B.cliemp, B.clicar, B.cliocu   FROM ".$empresa."_000060 A, ".$empresa."_000041 B WHERE  A.Saldto=B.Clidoc and B.clidoc='".$wcri."'  order by B.clidoc";
			break;
			case '3':
			$query = "SELECT A.saldto, A.salcau, A.salred, A.saldev, A.salsal, B.clinom, B.clite1, B.clipun, B.id, B.climai, B.clidir, B.cliemp, B.clicar, B.cliocu   FROM ".$empresa."_000060 A, ".$empresa."_000041 B WHERE  A.Saldto=B.Clidoc and  B.clipun='".$wcri."'  order by B.clidoc";
			break;
			case '4':
			$query = "SELECT A.saldto, A.salcau, A.salred, A.saldev, A.salsal, B.clinom, B.clite1, B.clipun, B.id, B.climai, B.clidir, B.cliemp, B.clicar, B.cliocu   FROM ".$empresa."_000060 A, ".$empresa."_000041 B WHERE  A.Saldto=B.Clidoc and B.clite1='".$wcri."'  order by B.clidoc";
			break;
			case '5':
			$query = "SELECT A.saldto, A.salcau, A.salred, A.saldev, A.salsal, B.clinom, B.clite1, B.clipun, B.id, B.climai, B.clidir, B.cliemp, B.clicar, B.cliocu   FROM ".$empresa."_000060 A, ".$empresa."_000041 B WHERE  A.Saldto=B.Clidoc and B.clinom like '%".$wcri."%'  order by B.clidoc";
			break;
			case '6':
			$query = "SELECT A.saldto, A.salcau, A.salred, A.saldev, A.salsal, B.clinom, B.clite1, B.clipun, B.id, B.climai, B.clidir, B.cliemp, B.clicar, B.cliocu   FROM ".$empresa."_000060 A, ".$empresa."_000041 B WHERE  A.Saldto=B.Clidoc and B.climai<>'' and B.climai<>'SIN DATO' and B.climai<>'NO APLICA' and B.climai<>'.' order by B.clidoc";
			break;
			case '7':
			$query = "SELECT A.saldto, A.salcau, A.salred, A.saldev, A.salsal, B.clinom, B.clite1, B.clipun, B.id, B.climai, B.clidir, B.cliemp, B.clicar, B.cliocu   FROM ".$empresa."_000060 A, ".$empresa."_000041 B WHERE  A.Saldto=B.Clidoc and B.climai='".$wcri."'  order by B.clidoc";
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
		echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_".$empresa.".png' WIDTH=340 HEIGHT=100></td></tr>";
		echo "<tr><td align=center bgcolor='006699'><font size=6 text color=#FFFFFF ><b>PROGRAMA DE PUNTOS</b></font></td></tr>";
		echo "</table></BR>";

		echo "<form name='busqueda' action='rep_puntos.php'  method=post>";
		$fila='ajaxquery("2","2","0", "0", "0")';
		
		echo "<table border=0 align=center >";
		echo "<tr><td><table border=(1) align=center bgcolor='006699'>";
		echo "<tr><td colspan=5 align=center ><font color=#FFFFFF ><B>BUSCAR :</B></td></tr>";
		echo "<tr><td colspan=5 align=center ><input type='TEXT' name='w1'  id ='w1' size=30 maxlength=30 ></td></tr>";
		echo "<tr><td><input type='radio' name='nume' value=2 onclick='".$fila."' checked><font color=#FFFFFF >Cedula&nbsp&nbsp&nbsp&nbsp&nbsp</td>";
		echo "<td><input type='radio' name='nume' onclick='".$fila."' value=3 color=#FFFFFF><font color=#FFFFFF >Numero de tarjeta&nbsp&nbsp&nbsp&nbsp&nbsp</td>";
		echo "<td><input type='radio' name='nume' onclick='".$fila."' value=4 ><font color=#FFFFFF >Teléfono&nbsp&nbsp&nbsp&nbsp&nbsp</td>";
		echo "<td><input type='radio' name='nume' onclick='".$fila."' value=5 ><font color=#FFFFFF >Nombre&nbsp&nbsp&nbsp&nbsp&nbsp</td>";
		echo "<td><input type='radio' name='nume' onclick='".$fila."' value=7 ><font color=#FFFFFF >Email&nbsp&nbsp&nbsp&nbsp&nbsp</td></tr>";
		echo "</table></td>";
		echo "</form>";

		echo "<td><table align=center >";
		$fila='ajaxquery("2","1","1", "0", "0")';
		$fila2='ajaxquery("2","1","6", "0", "0")';
		echo "<tr><td colspan=3 align=center><b><font color='006699'>VISUALIZAR LISTA:</font></B></td></tr>";
		echo "<tr><td colspan=3 align=LEFT color='006699'><a href='#' onclick='".$fila."'><b><font ><LI>DE TODOS LOS CLIENTES</LI></font></B></a></td></tr>";
		echo "<tr><td colspan=3 align=LEFT color='006699'><a href='#' onclick='".$fila2."'><b><font  ><LI>DE CLIENTES CON EMAIL</LI></font></B></a></td></tr>";

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

			echo "<form name='resultados[".$i."]' action='rep_puntos.php' method=post>";

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
			echo "<td bgcolor='".$wcf."'  align=center colspan=6><font color=#FFFFFF ><B> PTS CAUSADOS:&nbsp;</B><input type='TEXT' name='cau[".$i."]'  size=5 value='". $row[1]."'>";
			echo "<B>&nbsp;&nbsp;REDIMIDOS:&nbsp;</B><input type='TEXT'  name='red[".$i."]'  size=5 value='". $row[2]."'>";
			echo "<B>&nbsp;&nbsp;DEVUELTOS:&nbsp;</B><input type='TEXT' name='dev[".$i."]'  size=5 value='". $row[3]."'>";
			echo "<B>&nbsp;&nbsp;ACUMULADOS:&nbsp;</B><input type='TEXT' name='acu[".$i."]'  size=5 value='". $row[4]."'> </td>";
			$fila='MsgOkCancel("'.$i.'","5", "1", "'.$wope.'", "'.$wcri.'")';
			echo "<td align=center ><font ><a onclick='".$fila."'><B><img src='/matrix/images/medical/modificar1.png' ></a></B></td>";

			echo "<tr  bgcolor='".$wcf2."'>";
			echo "<td  align=center COLSPAN=4><font color=#FFFFFF ><B>NOMBRE</B></td>";
			echo "<td  align=center ><font color=#FFFFFF ><B>TELFONO</B></td>";
			echo "<td  align=center ><font color=#FFFFFF ><B>N TARJETA</B></td>";
			echo "<td  align=center ><font color=#FFFFFF ><B>EMAIL</B></td>";
			echo "<td  align=center ><font color=#FFFFFF ><B>&nbsp;</B></td>";
			echo "</TR>";

			echo "<tr  bgcolor='#FFFFFF' >";
			echo "<td  align=center COLSPAN=4><font color=#FFFFFF ><B><input type='TEXT' name='nom[".$i."]'  size=40 value='". $row[5]."'></B></td>";
			echo "<td  align=center ><font color=#FFFFFF ><B><input type='TEXT' name='tel[".$i."]'  size=20 value='". $row[6]."'></B></td>";
			echo "<td  align=center ><font color=#FFFFFF ><B><input type='TEXT' name='tar[".$i."]'  size=20 value='". $row[7]."'></B></td>";
			echo "<td  align=center ><font color=#FFFFFF ><B><input type='TEXT' name='mai[".$i."]'  size=30 value='". $row[9]."'></B></td>";
			echo "<input type='HIDDEN' name= 'ide[".$i."]'  value='".$row[8]."'>";

			$fila='ajaxquery("'.$i.'","3","3", "'.$wope.'", "'.$wcri.'")';
			echo "<td align=center  colspan=2><font color=#FFFFFF ><a onclick='".$fila."'><B><img src='/matrix/images/medical/cambiar1.png' ></a></B></td>";

			echo "<tr  bgcolor='".$wcf2."'>";
			echo "<td  align=center COLSPAN=4><font color=#FFFFFF ><B>DIRECCION</B></td>";
			echo "<td  align=center ><font color=#FFFFFF ><B>EMPRESA</B></td>";
			echo "<td  align=center ><font color=#FFFFFF ><B>CARGO</B></td>";
			echo "<td  align=center ><font color=#FFFFFF ><B>OCUPACION</B></td>";
			echo "<td  align=center ><font color=#FFFFFF ><B>&nbsp;</B></td>";
			echo "</TR>";

			echo "<tr  bgcolor='#FFFFFF' >";
			echo "<td  align=center COLSPAN=4><font color=#FFFFFF ><B><input type='TEXT' name='dir[".$i."]'  size=40 value='". $row[10]."'></B></td>";
			echo "<td  align=center ><font color=#FFFFFF ><B><input type='TEXT' name='emp[".$i."]'  size=20 value='". $row[11]."'></B></td>";
			echo "<td  align=center ><font color=#FFFFFF ><B><input type='TEXT' name='car[".$i."]'  size=20 value='". $row[12]."'></B></td>";
			echo "<td  align=center ><font color=#FFFFFF ><B><input type='TEXT' name='ocu[".$i."]'  size=30 value='". $row[13]."'></B></td>";

			echo "</table></td></br>";
			echo "</form>";
			echo "</div>";

			echo "<input type='HIDDEN' name= 'wope'  value='".$wope."'>";
			echo "<input type='HIDDEN' name= 'wcri'  value='".$wcri."'>";
		}
	}else if (isset($wope) and $wope!=0 and !isset($senal))
	{
		if ($wope==2)
		{
			$query = "SELECT A.saldto, A.salcau, A.salred, A.saldev, A.salsal, B.mednom FROM ".$empresa."_000060 A, ".$empresa."_000051 B WHERE  A.Saldto=B.Meddoc and B.meddoc='".$wcri."' ";

			$err = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err);

			if ($num2>0)
			{
				$row = mysql_fetch_array($err);
				
				$wcf='006699';
				$wcf2='#009999';
				
				echo "<table border=(1) align=center >";
				echo "<tr><td  align=left bgcolor='".$wcf."' ><font color=#FFFFFF ><B>CEDULA:".$row[0]."</B></font ></td>";
				echo "<td bgcolor='".$wcf."'  align=center colspan=5><font color=#FFFFFF ><B> PTS CAUSADOS:". $row[1];
				echo "<B>&nbsp;&nbsp;REDIMIDOS:". $row[2];
				echo "<B>&nbsp;&nbsp;DEVUELTOS:". $row[3];
				echo "<B>&nbsp;&nbsp;ACUMULADOS:". $row[4]."</td></tr>";

				echo "<tr  bgcolor='".$wcf2."'>";
				echo "<td  align=center COLSPAN=8><font color=#FFFFFF ><B>NOMBRE</B></td>";
		
				echo "</TR>";


				echo "<tr  bgcolor='#FFFFFF' >";
				echo "<td  align=center COLSPAN=8>". $row[5]."</td></tr>";


				echo "</table></td></br>";
				echo "</form>";
				echo "</div>";
			}
			else
			{
				echo"<CENTER><fieldset style='border:solid;border-color:006699; width=330' ; color=#000080>";
				echo "<table align='center' border=0 bordercolor=006699 width=340 style='border:solid;'>";
				echo "<tr><td  align=center><font size=3 color='#000080' face='arial'><b>No se ha encontrado nungun cliente con los datos ingresados, intente otra busqueda</td><tr>";
				echo "</table></fieldset></form>";
			}
		}else
		{
			echo"<CENTER><fieldset style='border:solid;border-color:006699; width=330' ; color=#000080>";
			echo "<table align='center' border=0 bordercolor=006699 width=340 style='border:solid;'>";
			echo "<tr><td  align=center><font size=3 color='#000080' face='arial'><b>No se ha encontrado nungun cliente con los datos ingresados, intente otra busqueda</td><tr>";
			echo "</table></fieldset></form>";
		}
	}

	echo "</div>";

}
?>
</body>
