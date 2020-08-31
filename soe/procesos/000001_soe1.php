<html>
<head>
<title>Maestro de Pacientes</title>
</head>
<BODY TEXT="#000066">
<center>
<link rel="stylesheet" href="/styles.css" type="text/css">
	<style type="text/css">
	<!--
		.BlueThing
		{
			background: #CCCCCC;
		}
		
		.SilverThing
		{
			background: #CCCCCC;
		}
		
		.GrayThing
		{
			background: #99CCFF;
		}
	
	//-->
</style>

<script type="text/javascript">
<!--
function ejecutar(fec,hor,path,pac,user,odont)
{
	if (path==1)
	{
		ruta='/matrix/det_registro.php?id='+pac+'&amp;pos1=soe1&amp;pos2='+fec+'&amp;pos3='+hor+'&amp;pos4=000002&amp;pos5=0&amp;pos6='+user+'&amp;tipo=P&amp;Valor=&amp;Form=soe1_000002&amp;call=2&amp;change=&amp;key=".$key."&amp;Pagina=';
		window.open(ruta);
	}
	else if (path==2)
	{
		var x= pac;
		if (x.length>=6) {
			ruta='/matrix/det_registro.php?id=0&amp;pos1=soe1&amp;pos2='+fec+'&amp;pos3='+hor+'&amp;pos4=000011&amp;pos5=0&amp;pos6='+user+'&amp;tipo=P&amp;Valor=&amp;Form=soe1_000011&amp;call=2&amp;change=&amp;key=".$key."&amp;Pagina=&amp;r[2]='+pac+'&amp;r[3]='+odont;
		}else{
			ruta='/matrix/det_registro.php?id='+pac+'&amp;pos1=soe1&amp;pos2='+fec+'&amp;pos3='+hor+'&amp;pos4=000011&amp;pos5=0&amp;pos6='+user+'&amp;tipo=P&amp;Valor=&amp;Form=soe1_000011&amp;call=2&amp;change=&amp;key=".$key."&amp;Pagina=&amp;r[2]='+pac+'&amp;r[3]='+odont;
		}
		window.open( ruta);
	}
	else if (path==3)
	{
		ruta='/matrix/det_registro.php?id=0&amp;pos1=soe1&amp;pos2=0&amp;pos3=0&amp;pos4=000011&amp;pos5=0&amp;pos6='+user+'&amp;tipo=P&amp;Valor=&amp;Form=soe1_000011&amp;call=2&amp;change=&amp;key=".$key."&amp;Pagina=&amp;r[2]='+pac+'&amp;r[3]='+odont;

		window.open(ruta);
	}
	else if (path==4)
	{
		window.open('/matrix/det_registro.php?id=0&amp;pos1=soe1&amp;pos2=0&amp;pos3=0&amp;pos4=000004&amp;pos5=0&amp;pos6='+user+'&amp;tipo=P&amp;Valor=&amp;Form=soe1_000004&amp;call=2&amp;change=&amp;key=".$key."&amp;Pagina=&amp;r[2]='+pac+'&amp;r[3]='+odont);
	}
	else if (path==5)
	{
		window.open('/matrix/SOE/procesos/Odontograma.php?&amp;paciente='+pac );
	}
	else if (path==6)
	{
		window.open('/matrix/SOE/procesos/maestroperiodontal.php?&amp;pac='+pac+'&amp;medico='+user);
	}
	else if (path==7)
	{
		window.open('/matrix/det_registro.php?id=0&amp;pos1=soe1&amp;pos2=0&amp;pos3=0&amp;pos4=000009&amp;pos5=0&amp;pos6='+user+'&amp;tipo=P&amp;Valor=&amp;Form=soe1_000009&amp;call=2&amp;change=&amp;key=".$key."&amp;Pagina=&amp;r[2]='+pac+'&amp;r[3]='+odont);
	}
	else if (path==8)
	{
		window.open('/matrix/det_registro.php?id=0&amp;pos1=soe1&amp;pos2=0&amp;pos3=0&amp;pos4=000006&amp;pos5=0&amp;pos6='+user+'&amp;tipo=P&amp;Valor=&amp;Form=soe1_000006&amp;call=2&amp;change=&amp;key=".$key."&amp;Pagina=&amp;r[2]='+pac+'&amp;r[3]='+odont);
	}
	else if (path==9)
	{
		window.open('/matrix/det_registro.php?id=0&amp;pos1=soe1&amp;pos2=0&amp;pos3=0&amp;pos4=000008&amp;pos5=0&amp;pos6='+user+'&amp;tipo=P&amp;Valor=&amp;Form=soe1_000008&amp;call=2&amp;change=&amp;key=".$key."&amp;Pagina=&amp;r[2]='+pac+'&amp;r[3]='+odont);
	}
	else if(path==10)
	{
		window.open('/matrix/det_registro.php?id=0&amp;pos1=soe1&amp;pos2=0&amp;pos3=0&amp;pos4=000012&amp;pos5=0&amp;pos6='+user+'&amp;tipo=P&amp;Valor=&amp;Form=soe1_000012&amp;call=2&amp;change=&amp;key=".$key."&amp;Pagina=&amp;r[2]='+pac+'&amp;r[3]='+odont);
	}
	else if(path==11)
	{
		window.open('/matrix/det_registro.php?id=0&amp;pos1=soe1&amp;pos2=0&amp;pos3=0&amp;pos4=000007&amp;pos5=0&amp;pos6='+user+'&amp;tipo=P&amp;Valor=&amp;Form=soe1_000007&amp;call=2&amp;change=&amp;key=".$key."&amp;Pagina=&amp;r[2]='+pac+'&amp;r[3]='+odont);
	}
	else
	{
		window.open('/matrix/publicar.php?grupo=SOE&prioridad=2');
	}
}

//-->
 </script>


<?php
include_once("conex.php");

/********************************************************
*   MAESTRO DE PACIENTES PARA LA UNIDAD ODONTOLOGICA	*
*														*
*********************************************************/

/*==================================================================================================================================
PROGRAMA						:Maneja el maestro de pacientes de la unidad odontologica

AUTOR							:Juan David Londoño A.

FECHA CREACION					:DICIEMBRE 2005

FECHA ULTIMA ACTUALIZACION 		:FEBRERO 1 de 2006

DESCRIPCION						:Este programa es el encargado de construir y manejar un panel con
								los diversos formularios que deben ser diligenciados por la Unidad
								Odontologica.
								Tiene tambien dos procesos asociados, Odontograma y Regitro Periodontal


TABLAS QUE USA					:Datos Generales (soe1_000002)
								Antecedentes (soe1_000011)
								Consulta (soe1_000004)
								Registro de Tratamiento (soe1_000007)
								Diagnostico (soe1_000009)
								Plan Tratamiento (soe1_000006)
								Presupuesto (soe1_000008)
								Aceptacion de presupuesto (soe1_000012)

//==================================================================================================================================*/


session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)
	echo "<center><table border=0 width=400>";
	echo "<tr><td colspan=3 align=center><img SRC='\MATRIX\images\medical\SOE\SOE1.JPG' width='242' height='133'></td>";



	if(!isset($pac) )
	{
		echo "<form action='' method=post>";
		echo "</select></tr><tr><td bgcolor=#99CCFF colspan=1 align=center><font color=#000066 font face='tahoma'><b>PACIENTE: </b></font></td>";

		/* Si el paciente no esta set construir el drop down */
		//if(isset($medico) and isset($pac1)) V1.03
		if(isset($pac1))
		{
			/* Si el medico  ya esta set traer los pacientes a los cuales les ha hecho seguimiento*/

			$query="select DISTINCT Identificacion, Nombre1, Nombre2,  Apellido1,Apellido2  from soe1_000002 "
			." where Identificacion  like '%".$pac1."%' or Nombre1 like '%".$pac1."%'"
			." or Nombre2 like '%".$pac1."%' or Apellido1 like '%".$pac1."%' or  Apellido2  like '%".$pac1."%' order by Identificacion";
			echo "</td><td bgcolor=#99CCFF colspan=2><select name='pac'>";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_array($err);
					if($row[0]==$pac)
					echo "<option selected>".$row[0]."</option>";
					else
					echo "<option>".$row[0]."-".$row[1]."-".$row[2]."-".$row[3]."-".$row[4]."</option>";
				}
			}	// fin $num>0
			echo "</select></td></tr>";
			echo "</td></tr><input type='hidden' name='pac1' value='".$pac1."'>";
		}	//fin isset medico
		else
		{
			echo "</td><td bgcolor=#99CCFF colspan=2><input type='text' name='pac1'>";
			echo "</td></tr>";
		}

		echo"<tr><td align=center bgcolor=#99CCFF colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";

		echo"<tr><td align=center colspan=3 ><A HREF ='/matrix/det_registro.php?id=0&amp;pos1=soe1&amp;pos2=0&amp;pos3=0&amp;pos4=000002&amp;pos5=0&amp;pos6=".substr($user,2)."&amp;tipo=P&amp;Valor=&amp;Form=soe1_000002&amp;call=2&amp;change=&amp;key=".$key."&amp;Pagina='>Ingresar nuevo paciente</a></td></tr>";
	}// if de los parametros no estan set

	else
	{
		$datos=explode("-",$pac);
		$query="select id  FROM	soe1_000002 where Identificacion like '%".$datos[0]."%' and Nombre1='".$datos[1]."' and Nombre2 = '".$datos[2]."' and Apellido1='".$datos[3]."' and Apellido2 = '".$datos[4]."'";
		$res = mysql_query($query,$conex);
		$num = mysql_num_rows($res);
		if ($num > 0)
		{
			$row=mysql_fetch_row ($res);
			$id= $row[0];
		}
		

		$query="select subcodigo, Descripcion  FROM soe1_000010, det_selecciones where Usuario='".substr($user,2)."' and subcodigo= soe1_000010.Codigo  and det_selecciones.medico='soe1' and  det_selecciones.codigo='008'";  //ponerlo igual al codigo usuario de root
		$soe = mysql_query($query,$conex);
		$num = mysql_num_rows($soe);
		if ($num > 0)
		{

			$row=mysql_fetch_row ($soe);
			$usua= $row[0]."-".$row[1];
			//echo "1".$usua."<br>";
		}
		
		
		
		
		$query="select id FROM	soe1_000011 where Identificacion like'%".$pac."%' and Odontologo='".$usua."' order by codigo desc";
		$soe = mysql_query($query,$conex);
		/*echo $query/*mysql_errno() ."=". mysql_error()*/;
		$num = mysql_num_rows($soe);
		if ($num > 0)
		{
			$row=mysql_fetch_row($soe);
			$idant= $row[0];
			// echo $row[0]. "<br>" .$row[1];
		}
		else
		{
			$idant=$pac;
		}

		

		$exp=explode("-",$pac);

		$exci=explode("-",$pac);    // para poner el nombre del paciente en el formulario de citas


		$query="select Tel_Residencia  FROM	soe1_000002 where Identificacion='".$pac."'";
		$soe = mysql_query($query,$conex);
		$num = mysql_num_rows($soe);
		if ($num > 0)
		{
			$row=mysql_fetch_row($soe);
			$tel= $row[0];
		}

		//$pacitas=$exci[1]."-".$exci[2]."-".$exci[3]."-".$exci[4]."-".$exci[0]."-".$tel;

		$fecha=date("Y-m-d");
		$hora=date("H:i:s");

		
/*echo $idant;
echo "<br>";
echo $pac;*/
		echo"<table border=0>";
		echo "<tr><td colspan=2 align=center><font face='tahoma' font color=#FF0066 font size=5>MAESTRO DE PACIENTES</td></tr>";
		echo"<table/ >";
		echo"<br>";
		echo"<table border=0 align=center>";
		echo "<tr><td colspan=1 align=center><font face='tahoma' font color=#330066><b>ODONTOLOGO:</b></td>";
		echo "<td colspan=1 align=center><font face='tahoma' font color=#330066><b><i>".substr($usua,3)."</i></b></td></tr>";
		echo"<table/ >";

		echo"<table border=0 align=center>";
		echo "<tr><td colspan=1 align=center><font face='tahoma' font color=#330066><b>PACIENTE:</b></td>";
		echo "<td colspan=1 align=left><font face='tahoma' font color=#330066><b><i>$exp[1] $exp[2] $exp[3] $exp[4]</i></b></td></tr>";
		echo"</table><br>";
		echo"<table border=0 align=center>";
		echo"<tr>";
		echo "<td bgcolor=#99CCFF align=center onclick='ejecutar(".chr(34).$fecha.chr(34).",".chr(34).$hora.chr(34).",1,".chr(34).$id.chr(34).",".chr(34).substr($user,2).chr(34).")' onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><b><font face='tahoma'>DATOS GENERALES DEL PACIENTE</font></b></td>";
		echo "<td bgcolor=#99CCFF align=center onclick='ejecutar(".chr(34).$fecha.chr(34).",".chr(34).$hora.chr(34).",2,".chr(34).$idant.chr(34).",".chr(34).substr($user,2).chr(34).",".chr(34).$usua.chr(34).")'onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><b><font face='tahoma'>ANTECEDENTES</font></b></td>";
		echo"</tr>";
		echo"<tr>";
		echo "<td bgcolor=#99CCFF align=center onclick='ejecutar(0,0,4,".chr(34).$pac.chr(34).",".chr(34).substr($user,2).chr(34).",".chr(34).$usua.chr(34).")' onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><b><font face='tahoma'>CONSULTA</font></b></td>";
		//echo "<td bgcolor=#99CCFF align=center onclick='ejecutar(0,0,11,".chr(34).$pac.chr(34).",".chr(34).substr($user,2).chr(34).",".chr(34).$usua.chr(34).")'onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><b><font face='tahoma'>REGISTRO TRATAMIENTO</font></b></td>";
		echo "<td bgcolor=#99CCFF align=center onclick='ejecutar(0,0,11,".chr(34).$pac.chr(34).",".chr(34).substr($user,2).chr(34).",".chr(34).$usua.chr(34).")'onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><b><font face='tahoma'>REGISTRO TRATAMIENTO</font></b></td>";
		echo"</tr>";
		echo"<tr>";
		echo "<td bgcolor=#99CCFF align=center onclick='ejecutar(0,0,5,".chr(34).$exci[0].chr(34).")'onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><b><font face='tahoma'>ODONTOGRAMA</font></b></td>";
		echo "<td bgcolor=#99CCFF align=center onclick='ejecutar(0,0,6,".chr(34).$pac.chr(34).",".chr(34).$usua.chr(34).")'onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><b><font face='tahoma'>REGISTRO PERIODONTAL</font></b></td>";
		echo"</tr>";
		echo"<tr>";
		echo "<td bgcolor=#99CCFF align=center onclick='ejecutar(0,0,7,".chr(34).$pac.chr(34).",".chr(34).substr($user,2).chr(34).",".chr(34).$usua.chr(34).")'onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><b><font face='tahoma'>DIAGNOSTICO</font></b></td>";
		echo "<td bgcolor=#99CCFF align=center onclick='ejecutar(0,0,8,".chr(34).$pac.chr(34).",".chr(34).substr($user,2).chr(34).",".chr(34).$usua.chr(34).")'onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><b><font face='tahoma'>PLAN TRATAMIENTO</font></b></td>";
		echo"</tr>";
		echo"<tr>";
		echo "<td bgcolor=#99CCFF align=center onclick='ejecutar(0,0,9,".chr(34).$pac.chr(34).",".chr(34).substr($user,2).chr(34).",".chr(34).$usua.chr(34).")'onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><b><font face='tahoma'>PRESUPUESTO</font></b></td>";
		echo "<td bgcolor=#99CCFF align=center onclick='ejecutar(0,0,10,".chr(34).$pac.chr(34).",".chr(34).substr($user,2).chr(34).",".chr(34).$usua.chr(34).")'onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><b><font face='tahoma'>ACEPTACION DE PRESUPUESTO</font></b></td>";
		echo"</tr>";
		echo"<tr>";
		echo "<td bgcolor=#99CCFF align=center onclick='ejecutar(0,0,12)'onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><b><font face='tahoma'>CARGAR IMAGENES</font></b></td>";
		echo "<td bgcolor=#99CCFF align=center onclick='ejecutar(0,0,3,".chr(34).$pac.chr(34).",".chr(34).substr($user,2).chr(34).",".chr(34).$usua.chr(34).")'onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><b><font face='tahoma'>NUEVO TRATAMIENTO</font></b></td>";
		echo"</tr>";
		echo"</table>";
		echo"<br>";
		echo"<tr><td align=center colspan=3 ><A HREF ='/matrix/SOE/procesos/000001_soe1.php'>Volver</a></td></tr>";
	}
}
?>
