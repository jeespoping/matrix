<html>
<head>
<title>DOCMED</title>
</head>
<body >
<font face='arial'>
<BODY TEXT="#000066">
<?php
include_once("conex.php");

/********************************************************
*     			DOCMED						*
*														*
*********************************************************/

//==================================================================================================================================
//PROGRAMA						:Buscador de Informacion
//AUTOR							:Juan David Londoño
//FECHA ULTIMA ACTUALIZACION 	:28 de Marzo de 2006
//DESCRIPCION					:Este programa se encarga en busca en la base de datos articulos por temas o por autor
//TABLAS						:Usa las tablas biblio_000001, biblio_000002, biblio_000003
//==================================================================================================================================



session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	$empresa='biblio';

	

	


	echo "<form action='' method=post>";

	if(!isset($nume))
	{

		echo "<table border=0 align=center >";
		echo "<tr><td align=center><img SRC='/MATRIX/images/medical/general/logo_promo.GIF'></td>";
		echo "<tr><td align=center><font size='5'><br>DOCMED</td></tr>";
		echo "<tr><td align=center ><font size=2 > buscador.php Ver. 2006-03-28</font></td></tr>";
		echo "</table>";
		echo "<br>";

		echo "<table border=(1) align=center>";
		echo "<tr><td colspan=3 align=center><input type='TEXT' name='nume' size=30 maxlength=60 ></td></tr>";
		echo "<tr><td><input type='radio' name='bus' value='aut' checked>Autor&nbsp&nbsp&nbsp&nbsp&nbsp</td>
			<td><input type='radio' name='bus' value='tem'>Tema&nbsp&nbsp&nbsp&nbsp&nbsp</td></tr>";
		echo "<tr><td colspan=3 align=center><input type='submit' value='ACEPTAR'></td></tr></table>";
		echo "</table>";
	}else

	{


		////////////////////////////////////////////////////////////////////////////////////////////////////////////////


		if ($bus=='aut')
		{
			echo "<table border=0 align=center >";
			echo "<tr><td align=center><img SRC='/MATRIX/images/medical/general/logo_promo.GIF'></td>";
			echo "<tr><td align=center><font size='5'><br>DOCMED</td></tr>";
			echo "<tr><td align=center ><font size=2 > buscador.php Ver. 2006-03-28</font></td></tr>";
			echo "</table>";
			echo "<br>";
			echo "<table border=(1) align=center>";

			if(isset($nume))
			{
				$query = "SELECT DISTINCT Nomautar1, Nomautar2, Apautar1, Apautar2
			FROM ".$empresa."_000003
			WHERE Nomautar1 like '%".$nume."%' or Nomautar2 like '%".$nume."%' or Apautar1 like'%".$nume."%' or Apautar2 like'%".$nume."%'
			ORDER by Nomautar1, Nomautar2, Apautar1, Apautar2";
				$err = mysql_query($query,$conex);

				$num= mysql_num_rows($err);
				echo "</td><td colspan=2><select name='autor'>";
				{
					for ($j=0;$j<$num;$j++)
					{
						$row = mysql_fetch_array($err);
						if($row[0]==$pac)
						echo "<option selected>".$row[0]."</option>";
						else
						echo "<option>".$row[0]." ".$row[1]." ".$row[2]." ".$row[3]."</option>";
					}
				}
				echo "</select></td></tr>";
				echo "</td></tr><input type='hidden' name='nume' value='".$nume."'>";
			}	else
			{
				echo "</td><td  colspan=2><input type='text' name='nume'>";
				echo "</td></tr>";
			}
			echo "<tr><td><input type='radio' name='bus' value='aut' checked>Autor&nbsp&nbsp&nbsp&nbsp&nbsp</td>
		<td><input type='radio' name='bus' value='tem'>Tema&nbsp&nbsp&nbsp&nbsp&nbsp</td></tr>";
			echo "<tr><td colspan=3 align=center><input type='submit' value='ACEPTAR'></td></tr></table>";
			echo "</table>";

			if(isset($autor))
			{

				$datos=explode(" ",$autor);

				$query = "SELECT id, Titart, Fecart, Nomautar1, Nomautar2, Apautar1, Apautar2, Art
			FROM ".$empresa."_000003
			WHERE Nomautar1 = '".$datos[0]."' and Nomautar2 = '".$datos[1]."' and Apautar1 ='".$datos[2]."' and Apautar2 ='".$datos[3]."'
			ORDER by Nomautar1, Nomautar2, Apautar1, Apautar2";
				$err = mysql_query($query,$conex);
				$num= mysql_num_rows($err);

				echo "<br>";
				echo "<br>";
				echo "<table border=1 align=center >";
				echo "<tr><td colspan=3><font size=2  font color=#4B4B4B><b><i>ARTICULOS ENCONTRADOS</font></td><td align=right><font size=2 font color=#4B4B4B><b><i>$num</font></td></tr>"; 
				echo "<tr><td align=center bgcolor=#99CCFF><font size='4'>ARTICULO</td><td align=center bgcolor=#99CCFF><font size='4'>TITULO</td>
		<td align=center bgcolor=#99CCFF><font size='4'>AUTOR</td><td align=center bgcolor=#99CCFF><font size='4'>AÑO DE PUBLICACION</td></tr>";

				for ($j=0;$j<$num;$j++)
				{
					if (is_int ($j/2))
					$wcf="DDDDDD";
					else
					$wcf="CCFFFF";
					$row = mysql_fetch_array($err);
					$hyper="<A HREF='/matrix/det_registro.php?id=".$row[0]."&pos1=biblio&pos2=2006-03-27&pos3=17:41:54&pos4=000003&pos5=0&pos6=biblio&tipo=P&Valor=&Form=000003-biblio-C-articulos&call=2&change=0&key=biblio&Pagina=1'>Ver</a>";
					echo "<tr><td align=left bgcolor=".$wcf.">$hyper</td><td align=left bgcolor=".$wcf.">$row[1]</td><td align=left bgcolor=".$wcf.">".$row[3]." ".$row[4]." ".$row[5]." ".$row[6]."</td>
					<td align=right bgcolor=".$wcf.">$row[2]</td></tr>";

				}
			}

		}
		
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		else if ($bus=='tem')
		{
			echo "<table border=0 align=center >";
			echo "<tr><td align=center><img SRC='/MATRIX/images/medical/general/logo_promo.GIF'></td>";
			echo "<tr><td align=center><font size='5'><br>DOCMED</td></tr>";
			echo "<tr><td align=center ><font size=2 > buscador.php Ver. 2006-03-28</font></td></tr>";
			echo "</table>";
			echo "<br>";
			echo "<table border=(1) align=center>";
			
			if(isset($nume))
			{
				$query = "SELECT DISTINCT Codtem, Nomtem
				FROM ".$empresa."_000001
				WHERE Nomtem like '%".$nume."%' 
				ORDER by Codtem";
				$err = mysql_query($query,$conex);
				$num= mysql_num_rows($err);
				echo "<tr><td colspan=2><select name='tema'>";
				
				{
					for ($j=0;$j<$num;$j++)
					{
						$row = mysql_fetch_array($err);
						echo "<option>".$row[0]."-".$row[1]."</option>";
					}
				}
				echo "</select></td>";
				echo "<input type='hidden' name='nume' value='".$nume."'>";
			}	else
			{
				echo "</td><td  colspan=2><input type='text' name='nume'>";
				echo "</td></tr>";
			}
			echo "<td><input type='radio' name='bus' value='aut' >Autor&nbsp&nbsp&nbsp&nbsp&nbsp</td>
			<td><input type='radio' name='bus' value='tem' checked>Tema&nbsp&nbsp&nbsp&nbsp&nbsp</td>";
			echo "<td colspan=3 align=center><input type='submit' value='ACEPTAR'></td></tr></table>";
			echo "</table>";
			
		if(isset($tema))
			{
				echo "<table border=0 align=center >";
			echo "<tr><td align=center><font size='5'><br>Subtemas Asociados</td></tr>";
			echo "</table>";
			echo "<table border=(1) align=center>";
				$query = "SELECT Codsub, Subtem
				FROM ".$empresa."_000002
				WHERE Relatemsub = '".$tema."' 
				ORDER by Codsub";
				$err = mysql_query($query,$conex);
				$num= mysql_num_rows($err);
				echo "</td><td colspan=2><select name='subtema'>";
				
				{
					for ($j=0;$j<$num;$j++)
					{
						$row = mysql_fetch_array($err);
						if($row[0]==$pac)
						echo "<option selected>".$row[0]."</option>";
						else
						echo "<option>".$row[0]."-".$row[1]."</option>";
					}
				echo "</select></td></tr>";
				}
				echo "</table>";
			}
			
			if(isset($tema) and isset($subtema))
			{
			$query = "SELECT id, Titart, Fecart, Nomautar1, Nomautar2, Apautar1, Apautar2, Art
			FROM ".$empresa."_000003
			WHERE Temart = '".$tema."' and Subart = '".$subtema."' 
			ORDER by Nomautar1, Nomautar2, Apautar1, Apautar2";
			$err = mysql_query($query,$conex);
				$num= mysql_num_rows($err);

				echo "<br>";
				echo "<br>";
				echo "<table border=1 align=center >";
				echo "<tr><td colspan=3><font size=2  font color=#4B4B4B><b><i>ARTICULOS ENCONTRADOS</font></td><td align=right><font size=2 font color=#4B4B4B><b><i>$num</font></td></tr>"; 
				echo "<tr><td align=center bgcolor=#99CCFF><font size='4'>ARTICULO</td><td align=center bgcolor=#99CCFF><font size='4'>TITULO</td>
		<td align=center bgcolor=#99CCFF><font size='4'>AUTOR</td><td align=center bgcolor=#99CCFF><font size='4'>AÑO DE PUBLICACION</td></tr>";

				for ($j=0;$j<$num;$j++)
				{
					if (is_int ($j/2))
					$wcf="DDDDDD";
					else
					$wcf="CCFFFF";
					$row = mysql_fetch_array($err);
					$hyper="<A HREF='/matrix/det_registro.php?id=".$row[0]."&pos1=biblio&pos2=2006-03-27&pos3=17:41:54&pos4=000003&pos5=0&pos6=biblio&tipo=P&Valor=&Form=000003-biblio-C-articulos&call=2&change=0&key=biblio&Pagina=1'>Ver</a>";
					echo "<tr><td align=left bgcolor=".$wcf.">$hyper</td><td align=left bgcolor=".$wcf.">$row[1]</td><td align=left bgcolor=".$wcf.">".$row[3]." ".$row[4]." ".$row[5]." ".$row[6]."</td>
					<td align=right bgcolor=".$wcf.">$row[2]</td></tr>";

				}
			}
		}
		
		
	}

}
?>	