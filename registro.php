<html>
<head>
    <title>MATRIX</title>
	<style type="text/css">
	<!--
		.BlueThing
		{
			background: #CCCCFF;
		}

		.SilverThing
		{
			background: #CCCCCC;
		}

		.GrayThing
		{
			background: #999999;
		}

	//-->
	</style>
</head>
<body BGCOLOR="FFFFFF">

<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Registro y Consulta de Informacion</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> registro.php Ver. 2019-06-26 </b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
/**********************************************************************************************************************
     Programa :  registro.php
     Fecha de Liberación : 2003-09-30
   	 Realizado por : Pedro Ortiz Tamayo
     Version Actual : 2014-02-10

    OBJETIVO GENERAL :  Este programa permite seleccionar los formularios del usuario especificado por login o por parametro ($key) o los formularios publicos
    e interactuar con ellos para adicion, modificacion y borrado, de acuerdo a las prioridades establecidas.
	 La variable call o de llamado puede tener los siguientes parametros:
		1. call = 0  llamado standard, graba los registros con el usuario propietario de la tabla
		2. call = 1  llamado especifico, se llama el programa especificando un solo formulario para interactuar con el.
		3. call = 2  llamado directo, se pasa al programa det_registro.php sin pasar por el programa registro.php
		4. call = 3  llamado con cambio de usuario. El programa llama a det_registro.php y se graban los registros con el usuario de login.

        Cuando el programa se invoca con la opcion call = 1 la estructura del hipervinculo es:
        registro.php?call=1&Form=<  Codigo de la Forma, Tipo (C cerrado/A abierto), Descripcion por Ej:000005-costosyp-C-ptocco - Centros de Costos>&Frm=0&tipo=P  Formulario Privado&key=costosyp usuario

   REGISTRO DE MODIFICACIONES :
   .2019-06-26
		En el href a det_registro.php se cambia $row[$items+4] por $row['id'] para evitar que no se envíe el id y por tal motivo 
		no muestre los datos a editar si la tabla no tiene el diccionario completo
   .2014-02-10
		Se modifica la consulta de registros ya que el calculo del numero de registros totales se estaba haciendo de forma muy ineficiente. Se cambia un
		SELECT * por un SELECT COUNT(*) para calcular el total de registros de la consulta.

   .2007-04-24
   		Se modifico el programa para considerar una precondicion en la variable "PC" que filtra los registros que el usuario puede ver antes de mostrarlos
   		por primera vez. La precondicion si existe, permanece durante toda la ejecucion del programa.
   		Se pueden crear otras condiciones que filtraran sobre los registros previamente filtrados de la precondicion.

   .2006-03-10
   		Se modifico la conformacion del la consulta cuando el usuario define una condicion de busqueda.

   .2006-01-05
   		Se modifico la estrctura de la tabla para mostrarla correctamente cuando el usuario no tiene permisos sobre ella.

   .2006-01-04
   		Se crea la tabla de root numero 30 donde se encuentra el diccionario de datos. Si el campo este en el diccionario muestra su descripcion, en caso

    .2005-02-21
    	Ultima Modificacion Registrada.
***********************************************************************************************************************/

function Stablas($usuario1, $usuario2, &$Tablas,$conex)
{
	$query = "SELECT tablas from usuarios ";
	$query = $query." where codigo = '".$usuario2."'";
	$err = mysql_query($query,$conex);
	$row = mysql_fetch_array($err);
	$tabla=$row[0];
	$Tablas=explode(chr(13).chr(10),$tabla);
}
function Sbuscar($criterio, &$Tablas)
{
	for ($j=0;$j<sizeof($Tablas);$j++)
		if($criterio == $Tablas[$j])
			return true;
	return false;
}
function validar($chain,$type)
{
	switch ($type)
	{
		case 1:
				$regular="/^(\+|-_:.)?([[:digit:]]+)$/";
				if (preg_match($regular,$chain,$occur))
					if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
						return false;
					else
						return true;
				else
					return false;
				break;
		case 2:
				$decimal ="/^(\+|-)?([[:digit:]]+)\.([[:digit:]]+)$/";
				$cientifica ="/^(\+|-)?([[:digit:]]+)\.([[:digit:]]+)e(\+|-)?([[:digit:]]+)$/i";
				if (preg_match($decimal,$chain,$occur))
					if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
						return false;
					else
						return true;
				else
					if(preg_match($cientifica,$chain,$occur))
						if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
							return false;
						else
							return true;
					else
						return false;
				break;
	}
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$superglobals = array($_SESSION,$_REQUEST);
	foreach ($superglobals as $keySuperglobals => $valueSuperglobals)
	{
		foreach ($valueSuperglobals as $variable => $dato)
		{
			$$variable = $dato; 
		}
	}
	if(!isset($key))
		$key = substr($user,2,strlen($user));
	echo "<form action='registro.php' method=post>";
	echo "<input type='HIDDEN' name= 'key' value='".$key."'>";
	

	

	if(!isset($call))
		$call=0;
	echo "<input type='HIDDEN' name= 'call' value='".$call."'>";
	if(!isset($change))
		$change=0;
	echo "<input type='HIDDEN' name= 'change' value='".$change."'>";
	if($key != $usera)
	{
		$Tablas=array();
		Stablas($key,$usera,$Tablas,$conex);
	}
	if(!isset($tipo))
	{
		echo "<table border=0 align=center cellpadding=3>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Tipo de Formulario</td>";
		echo "<td bgcolor=#cccccc><input type='radio' name= 'tipo' value='C'>Compartidos - ";
		echo "<input type='radio' name= 'tipo' value='P' checked>Propios</td>";
		$Form="0";
	}
	else
	{
		echo "<input type='HIDDEN' name= 'tipo' value='".$tipo."'>";
		//$query = "select * from formulario where medico='".$key."' and tipo !='T' order by codigo";
		if($tipo=="P")
			$query = "select * from formulario where medico='".$key."' or tipo ='A' order by tipo desc,medico,codigo";
		else
			if(!isset($Form))
				$query = "select formulario.medico,codigo,nombre,tipo,seguridad.medico,grabacion,modificacion,lectura,reportes from seguridad,formulario where seguridad.usuario='".$key."' and seguridad.medico=formulario.medico and seguridad.formulario=formulario.codigo order by codigo";
			else
				$query = "select formulario.medico,codigo,nombre,tipo,seguridad.medico,grabacion,modificacion,lectura,reportes from seguridad,formulario where seguridad.usuario='".$key."' and seguridad.medico=formulario.medico and seguridad.formulario=formulario.codigo and formulario.codigo='".substr($Form,0,6)."' order by codigo";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$row = mysql_fetch_array($err);
		// Verificacion de cambio de usuario dependiendo del valor de la variable call
		if($key == $usera or (($call >= 0 and $call <= 2) and (isset($change) and $change == 0)))
			$USER=$key;
		else
			$USER=$usera;
		if(!isset($Form))
		{
			$Form="0";
			$Frm="";
		}
		else
		{
			if($Frm != $Form)
			{
				$Frm = $Form;
				unset($Inicial);
			}
		}
		echo "<input type='HIDDEN' name= 'Frm' value='".$Frm."'>";
		$ini=strpos($Form,"-");
		$o=substr($Form,$ini+1);
		$ini1=strpos($o,"-");
		$TF=substr($o,$ini1+1,1);
		if(!isset($owner))
			$owner=$key;
		if(isset($Form) and $Form != "0" and isset($owner) and substr($Form,$ini+1,$ini1) != $owner)
			$owner=substr($Form,$ini+1,$ini1);
		$ini = strpos($Form,"-");
		$query = "show tables ";
		$err1 = mysql_query($query,$conex);
		$numtables = mysql_num_rows($err1);
		$tables=array();
		for ($h=0;$h<$numtables;$h++)
		{
			$row1 = mysql_fetch_array($err1);
			$tables[$h]=$row1[0];
		}
		echo "<table border=0 align=center cellpadding=3>";
		echo "<tr>";
		echo "<td bgcolor='#cccccc'><font size=2><b>Formularios :</b></font></td>";
		if($call == 1)
		{
			echo "<td bgcolor='#cccccc' align=center>".$Form;
			echo "<input type='HIDDEN' name= 'Form' value='".$Form."'>";
		}
		else
		{
			echo "<td bgcolor='#cccccc'><select name='Form'>";
			for ($i=0;$i<$num;$i++)
			{
				$wsw=0;
				for ($h=0;$h<$numtables;$h++)
				{
					if($tables[$h]==$row[0]."_".$row[1])
						$wsw=1;
				}
				if($wsw==1)
				{
					if ($row[1] == substr($Form,0,$ini) and $row[0]==$owner)
						echo "<option selected>".$row[1]."-".$row[0]."-".$row[3]."-".$row[2]."</option>";
					else
						echo "<option>".$row[1]."-".$row[0]."-".$row[3]."-".$row[2]."</option>";
				}
				$row = mysql_fetch_array($err);
			}
		}
		echo "</td>";
	}
	if(isset($Form) and $Form!="0")
	{
		$ini = strpos($Form,"-");
		for ($i=$ini+1;$i<strlen($Form);$i++)
		{
			if(substr($Form,$i,1)=="-")
			{
				$ini1=$i;
				$i=strlen($Form);
			}
		}
		$owner=substr($Form,$ini+1,$ini1-$ini-1);
	}
	echo "<td bgcolor='#cccccc'><input type='submit' value='IR'></tr>";
	if($Form != "0")
	{
		$query = "select * from seguridad where medico='".$owner."' and usuario= '".$key."' and formulario='".substr($Form,0,$ini)."'";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$row = mysql_fetch_array($err);
		$grabacion=$row[3];
		$modificacion=$row[4];
		$lectura=$row[5];
		$reportes=$row[6];
		echo "<input type='HIDDEN' name= 'owner' value='".$owner."'>";
		echo "<input type='HIDDEN' name= 'grabacion' value='".$grabacion."'>";
		echo "<input type='HIDDEN' name= 'modificacion' value='".$modificacion."'>";
		echo "<input type='HIDDEN' name= 'lectura' value='".$lectura."'>";
		echo "<input type='HIDDEN' name= 'reportes' value='".$reportes."'>";
		if (!isset($Valor))
		{
			$Valor="";
			$Vlr="";
		}
		else
		{
			$valor1="";
			$Valor=stripslashes($Valor);
			if($Vlr != $Valor)
			{
				$Vlr = $Valor;
				unset($Inicial);
			}
			for($w=0;$w<strlen($Valor);$w++)
			{
				if( ($w==0 and substr($Valor,$w,1) != " ") or ($w>0 and substr($Valor,$w,1) != " ") or ($w>0 and substr($Valor,$w,1) == " " and substr($Valor,$w-1,1) != " " ) )
					$valor1=$valor1.substr($Valor,$w,1);
			}
			$Valor=$valor1;
		}
		echo "<input type='HIDDEN' name= 'Vlr' value='".$Vlr."'>";
		echo "<tr><td bgcolor=#cccccc><font size=2><b>Condicion :</b></td>";
		echo "<td bgcolor=#cccccc><input type='TEXT' name='Valor' size=80 maxlength=80 value='".$Valor."'></td>";
		if(isset($back))
			echo "<td bgcolor=#cccccc><input type='checkbox' name=back checked>Back</td></tr>";
		else
			echo "<td bgcolor=#cccccc><input type='checkbox' name=back>Back</td></tr>";
		// ************ Adicion 2007-04-24 ************
		if(isset($CP))
		{
			echo "<input type='HIDDEN' name= 'CP' value='".$CP."'>";
			if(strpos($Valor,"=") !== False)
				$Valor=$CP." y ".$Valor;
			else
				$Valor=$CP." ".$Valor;
		}
	}
	echo "</table><br>";
	if($Form != "0")
	{
		if(!isset($Pagina))
			if(!isset($Inicial))
				$Pagina=1;
			else
				$Pagina=$Inicial / 30 + 1;
		echo "<table border=0 align=left nowrap='nowrap' class='tab'>";
		if(($tipo=="C" and $grabacion=="1") or ($tipo=="P" and ($TF != "A" or ($key == $owner and $TF == "A"))))
			if($key == $usera or ($key != $usera and Sbuscar($owner."-".substr($Form,0,$ini), $Tablas)))
				echo "<tr><td align=center><A HREF='det_registro.php?id=0&amp;pos1=".$owner."&amp;pos2=0&amp;pos3=0&amp;pos4=".substr($Form,0,$ini)."&amp;pos5=0&amp;pos6=".$USER."&amp;tipo=".$tipo."&amp;Valor=".$Valor."&amp;Form=".$Form."&amp;call=".$call."&amp;change=".$change."&amp;key=".$key."&amp;Pagina=".$Pagina."'>Nuevo</td></tr></table><BR>";
		else
			echo "</table><br>";
	}
	if ($Form!="0")
	{
		$wsw1=0;
		$query = "select * from det_formulario where medico='".$owner."' and codigo='".substr($Form,0,$ini)."'  and tipo != '13' and tipo !='17' order by posicion";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$items=$num;
		$color="#999999";
		echo "<table border=0 align=center>";
		echo "<tr>";
		if(($tipo=="C" and $modificacion=="1") or  ($tipo=="P"  and ($TF != "A" or ($key == $owner and $TF == "A"))))
  			if($key == $usera or ($key != $usera and Sbuscar($owner."-".substr($Form,0,$ini), $Tablas)))
  				echo "<td bgcolor=".$color."><b><font size=2>Seleccion</font></b></td>";
		echo "<td bgcolor=".$color."><b><font size=2>Fecha_data</font></b></td>";
		echo "<td bgcolor=".$color."><b><font size=2>Hora_data</font></b></td>";
		$kc=$num;
		$tab=$owner."_".substr($Form,0,$ini);
		$cons[0]=$owner;
		$cons[1]="Fecha_Data";
		$cons[2]="Hora_Data";
		$cons[$kc+3]="Seguridad";
		$diccionary=array();
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$diccionary[$i]=$row[2];
			$cons[$i+3]=$row[3];
			$tip[$i+3]=$row[4];
  			echo "<td bgcolor=".$color."><font size=2><b>".$row[3]."</b></font></td>";
  		}
  		echo "<td bgcolor=".$color."><b><font size=2>Seguridad</font></b></td>";
  		if(($tipo=="C" and $modificacion=="1") or  ($tipo=="P"  and ($TF != "A" or ($key == $owner and $TF == "A"))))
  			if($key == $usera or ($key != $usera and Sbuscar($owner."-".substr($Form,0,$ini), $Tablas)))
  				echo "<td bgcolor=".$color."><b><font size=2>Seleccion</font></b></td>";
		echo "</tr>";
		if(($tipo=="C" and $modificacion=="1") or  ($tipo=="P"  and ($TF != "A" or ($key == $owner and $TF == "A"))))
  			if($key == $usera or ($key != $usera and Sbuscar($owner."-".substr($Form,0,$ini), $Tablas)))
				echo "<tr><td bgcolor=".$color." colspan=3 align=center><b><font size=2>Diccionario de Datos : </font></b></td>";
			else
				echo "<tr><td bgcolor=".$color." colspan=2 align=center><b><font size=2>Diccionario de Datos : </font></b></td>";
		else
			echo "<tr><td bgcolor=".$color." colspan=2 align=center><b><font size=2>Diccionario de Datos : </font></b></td>";
		for ($i=0;$i<$items;$i++)
		{
			$query = "select Dic_Descripcion from root_000030 where Dic_Usuario='".$owner."' and Dic_Formulario='".substr($Form,0,$ini)."'  and Dic_Campo='".$diccionary[$i]."'";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			if($num2 > 0)
			{
				$row2 = mysql_fetch_array($err2);
				echo "<td bgcolor=".$color." ><b><font size=2>".$row2[0]."</font></b></td>";
			}
			else
				echo "<td bgcolor=".$color." align=center><b><font size=2>.</font></b></td>";
		}
		if(($tipo=="C" and $modificacion=="1") or  ($tipo=="P"  and ($TF != "A" or ($key == $owner and $TF == "A"))))
  			if($key == $usera or ($key != $usera and Sbuscar($owner."-".substr($Form,0,$ini), $Tablas)))
				echo "<td bgcolor=".$color." align=center colspan=2><b><font size=2>&nbsp </font></b></td>";
			else
				echo "<td bgcolor=".$color." align=center><b><font size=2>&nbsp </font></b></td>";
		else
			echo "<td bgcolor=".$color." align=center><b><font size=2>&nbsp </font></b></td>";
		echo "</tr>";
		$addWhere = true;
		$query = "select * from ".$owner."_".substr($Form,0,$ini)."";

		if($Valor != "")
		{
			$mysql1=array();
			$query1="SHOW COLUMNS FROM ".$owner."_".substr($Form,0,$ini);
			$err1 = mysql_query($query1,$conex);
			$num1 = mysql_num_rows($err1);
			for ($i=0;$i<$num1;$i++)
			{
				$row = mysql_fetch_array($err1);
				$mysql1[$i][0]=$row[0];
				$mysql1[$i][1]=$row[1];
			}
			$k=-1;
			$mysql2=array();
			$Valor=$Valor." ";
			$Ordenar="";
			$ini=strpos($Valor," ");
			while($ini>0)
			{
				$encontro=-1;
				if(substr(substr($Valor,0,$ini),strlen(substr($Valor,0,$ini))-1,1)=="<" or substr(substr($Valor,0,$ini),strlen(substr($Valor,0,$ini))-1,1)==">")
				{
					if(substr(substr($Valor,0,$ini),strlen(substr($Valor,0,$ini))-1,1)==">")
						$sort=" DESC ";
					else
						$sort="";
					for ($j=0;$j<$num1;$j++)
					{
						if(ucfirst(strtolower(substr($Valor,0,$ini-1)))== $mysql1[$j][0])
						{
							$encontro=$j;
							if(strlen($Ordenar)==0)
								$Ordenar=" Order by ".substr($Valor,0,$ini-1).$sort;
							else
								$Ordenar=$Ordenar.",".substr($Valor,0,$ini-1).$sort;
						}
					}
				}
				if ($encontro == -1)
				{
					$k=$k+1;
					$mysql2[$k][0]=substr($Valor,0,$ini);
					$mysql2[$k][1]=strlen(substr($Valor,0,$ini));
				}
				$Valor=substr($Valor,$ini+1,strlen($Valor));
				$ini=strpos($Valor," ");
			}
			$correcto=0;
			if($k>-1){
				$SQL=" where ";
				$addWhere = false;
			}else
				$SQL="";
			for ($i=0;$i<=$k;$i++)
			{
				if($mysql2[$i][1]==1 and strtolower($mysql2[$i][0]) =="y" and $i > 0 and $i < $k and $mysql2[$i-1][1] > 1 and $mysql2[$i+1][1] > 1)
					$SQL=$SQL. " AND ";
				else
					if($mysql2[$i][1]>1)
					{
						$encontro=-1;
						for ($j=0;$j<$num1-1;$j++)
						{
							if(ucfirst(strtolower( substr($mysql2[$i][0],0,strlen($mysql1[$j][0]))))==ucfirst(strtolower($mysql1[$j][0])))
								$encontro=$j;
						}
						if($encontro != -1 and (substr($mysql1[$encontro][1],0,4)=="varc" or substr($mysql1[$encontro][1],0,4)=="char" or substr($mysql1[$encontro][1],0,4)=="text"  or substr($mysql1[$encontro][1],0,4)=="date"  or substr($mysql1[$encontro][1],0,4)=="time"  or substr($mysql1[$encontro][1],0,4)=="long" ))
						{
							$ig=strpos($mysql2[$i][0],"=");
							if($ig>0)
								$SQL=$SQL. substr($mysql2[$i][0],0,$ig)." = '".substr($mysql2[$i][0],$ig+1,strlen($mysql2[$i][0]))."'";
							else
							{
								$ig=strpos($mysql2[$i][0],"&");
								if($ig>0)
									$SQL=$SQL. substr($mysql2[$i][0],0,$ig)." like '%".substr($mysql2[$i][0],$ig+1,strlen($mysql2[$i][0]))."%'";
								else
									$correcto=1;
							}
						}
						else
						if($encontro != -1)
						{
							$numerica = substr($mysql2[$i][0],strlen($mysql1[$encontro][0])+1,strlen($mysql2[$i][0]));
							if(substr($mysql1[$encontro][1],0,3)=="int")
								if(validar($numerica,1))
									$SQL=$SQL." ".$mysql2[$i][0]." ";
								else
									$correcto=1;
							else
								if(validar($numerica,2))
									$SQL=$SQL." ".$mysql2[$i][0]." ";
								else
									$correcto=1;
						}
						else
							$correcto=1;
					}
				    else
				    	$correcto=1;
			}

			if($correcto== 0)
			{
				$query=$query.$SQL;
			}
			else
			{
				echo "CONDICION NO APLICABLE "."<br>";
				$wsw1=1;
			}
		}
		$whereNuevo = ( $addWhere ) ? 'WHERE' : 'AND';
		if($tipo=="C" or $call == 3 or $change == 1)
			$query=$query." {$whereNuevo} (seguridad like 'A-%'  or seguridad like '%".$USER."%')";
		$query1 = str_replace("*","count(*)",$query);
		$err = mysql_query($query1,$conex);
		$num = mysql_num_rows($err);
		$row = mysql_fetch_array($err);
		$Totales=$row[0];
		if(isset($Pagina) and $Pagina > 0)
		{
			$Paginas=(integer)($Totales / 30);
			if($Paginas * 30 < $Totales)
				$Paginas++;
			if($Pagina > $Paginas)
				$Pagina=$Paginas;
			if($Pagina == 0)
				$Pagina++;
			$Inicial=($Pagina - 1 ) * 30;
			$Final= $Inicial + 30;
		}
		else
		{
			if (!isset($Inicial))
			{
				$Inicial=0;
				$Final=30;
			}
			else
				if(!isset($back))
				{
					if($Final < $Totales)
					{
						$Inicial = $Final;
						$Final=$Final+30;
					}
				}
				else
				{
					if($Inicial >= 30)
					{
						$Final = $Inicial;
						$Inicial=$Inicial-30;
					}
				}
				$Pagina=$Inicial / 30 + 1;
			}
		if(!isset($Ordenar))
			$Ordenar="";
		$query=$query.$Ordenar;
		$string ="tabla=".$tab."&amp;consulta=".$query;
		$string=str_replace(chr(39),chr(34),$string);
		$string=str_replace("%","|",$string);
		$kc=$kc+4;
		for ($z=0;$z<$kc;$z++)
			$string = $string."&amp;cons[".$z."]=".$cons[$z];
		$string = $string."&amp;kc=".$kc;
		#echo $string."<br>";
		if($tipo=="P" and $TF != "A")
		{
			$ini = strpos($Form,"-");
			if($key == $usera or ($key != $usera and Sbuscar($owner."-".substr($Form,0,$ini), $Tablas)))
				echo "<li><A HREF='delete.php?".$string."'>Borrar Seleccion</A><br>";
		}
		/*if(!isset($Ordenar))
			$Ordenar="";
		$query=$query.$Ordenar;*/
		$query = $query." limit ".$Inicial.",30";
		$query=stripslashes($query);
		#echo $query."<br>";

		echo "<input type='HIDDEN' name= 'Inicial' value='".$Inicial."'>";
		echo "<input type='HIDDEN' name= 'Final' value='".$Final."'>";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<input type='HIDDEN' name= 'Totales' value='".$Totales."'>";
		$r=0;
		if($num>0 and $wsw1 == 0)
		{
			for ($i=0;$i<$num;$i++)
			{
				if ($r == 0)
				{
					$color="#CCCCCC";
					$r = 1;
				}
				else
				{
					$color="#999999";
					$r = 0;
				}
				$row = mysql_fetch_array($err);
				if($r == 0)
					echo "<tr onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)." bgcolor=".$color.">";
				else
					echo "<tr onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='SilverThing';".chr(34)." bgcolor=".$color.">";
				if(($tipo=="C" and $modificacion=="1") or  ($tipo=="P" and ($TF != "A" or ($key == $owner and $TF == "A"))))
				{
					$ini = strpos($Form,"-");
					if($key == $usera or ($key != $usera and Sbuscar($owner."-".substr($Form,0,$ini), $Tablas)))
						echo "<td align=center><A HREF='det_registro.php?id=".$row['id']."&amp;pos1=".$owner."&amp;pos2=".$row[1]."&amp;pos3=".$row[2]."&amp;pos4=".substr($Form,0,$ini)."&amp;pos5=0&amp;pos6=".$USER."&amp;tipo=".$tipo."&amp;Valor=".$Vlr."&amp;Form=".$Form."&amp;call=".$call."&amp;change=".$change."&amp;key=".$key."&amp;Pagina=".$Pagina."'><font size=2>Editar</font></td>";
						// echo "<td align=center><A HREF='det_registro.php?id=".$row[$items+4]."&amp;pos1=".$owner."&amp;pos2=".$row[1]."&amp;pos3=".$row[2]."&amp;pos4=".substr($Form,0,$ini)."&amp;pos5=0&amp;pos6=".$USER."&amp;tipo=".$tipo."&amp;Valor=".$Vlr."&amp;Form=".$Form."&amp;call=".$call."&amp;change=".$change."&amp;key=".$key."&amp;Pagina=".$Pagina."'><font size=2>Editar</font></td>";
				}
				for ($j=1;$j<$items+4;$j++)
				{
					if(isset($tip[$j]) and $tip[$j] == "16")
						$row[$j]="********";
					if(strlen($row[$j]) > 80)
						$row[$j]=substr($row[$j],0,80)."  ......";
					echo "<td><font size=2>".$row[$j]."</font></td>";
				}
				if(($tipo=="C" and $modificacion=="1") or  ($tipo=="P" and ($TF != "A" or ($key == $owner and $TF == "A"))))
				{
					$ini = strpos($Form,"-");
					if($key == $usera or ($key != $usera and Sbuscar($owner."-".substr($Form,0,$ini), $Tablas)))
						echo "<td align=center><A HREF='det_registro.php?id=".$row['id']."&amp;pos1=".$owner."&amp;pos2=".$row[1]."&amp;pos3=".$row[2]."&amp;pos4=".substr($Form,0,$ini)."&amp;pos5=0&amp;pos6=".$USER."&amp;tipo=".$tipo."&amp;Valor=".$Vlr."&amp;Form=".$Form."&amp;call=".$call."&amp;change=".$change."&amp;key=".$key."&amp;Pagina=".$Pagina."'><font size=2>Editar</font></td>";
						// echo "<td align=center><A HREF='det_registro.php?id=".$row[$items+4]."&amp;pos1=".$owner."&amp;pos2=".$row[1]."&amp;pos3=".$row[2]."&amp;pos4=".substr($Form,0,$ini)."&amp;pos5=0&amp;pos6=".$USER."&amp;tipo=".$tipo."&amp;Valor=".$Vlr."&amp;Form=".$Form."&amp;call=".$call."&amp;change=".$change."&amp;key=".$key."&amp;Pagina=".$Pagina."'><font size=2>Editar</font></td>";
				}
				echo "</tr>";
			}
			echo "</tabla>";
			echo "Registros :<b>".$Inicial."</b> a <b>".$Final."</b>&nbsp &nbsp";
			echo "De :<b>".$Totales."</b>&nbsp &nbsp";
			$Paginas=(integer)($Totales / 30);
			if($Paginas * 30 < $Totales)
				$Paginas++;
			echo " Pagina Nro :<b> ".$Pagina."</b>&nbsp &nbspDe :<b>".$Paginas."</b>&nbsp &nbsp <b>Vaya a la Pagina Nro :</b> <input type='TEXT' name='Pagina' size=10 maxlength=10 value=0>&nbsp &nbsp<input type='submit' value='IR'><br>";
			echo "<table border=0 align=center>";
			echo "<tr><td><A HREF='#Arriba'><B>Arriba</B></A></td></tr></table>";
	 	 }
	 	 else
	 	 {
	 	 	echo "</tabla>";
	  		echo "Sin Registros"."<br>";
  		}
  	}
	else
	{
		echo "Seleccione Formulario y Campo para la Consulta ".$Form;
	}
	include_once("free.php");
  }
?>
</body>
</html>
