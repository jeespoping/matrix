<html>
<head>
  <title>ORDENES DE LABORATORIO</title>
</head>
<body BGCOLOR="">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Ordenes de Laboratorio</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> ordenes.php Ver. 1.00</b></font></tr></td></table>
</center>

<BODY TEXT="#000066">
<?php
include_once("conex.php");
		session_start();
		if(!isset($_SESSION['user']))
			echo"error";
		else
		{
			$key = substr($user,2,strlen($user));
			

			mysql_select_db("MATRIX");
			echo "<form action='ordenes.php' method=post>";
			if (isset($ok) and $ok=="on")
			{
				$datafile=$key."_".substr(date("H:i:s"),0,2).substr(date("H:i:s"),3,2).substr(date("H:i:s"),6,2).".txt";
				$file = fopen($datafile,"w+");
				$reg=$reg.$unidad."|".$medico."|".$registro.$exa."|";
				 fwrite ($file,$reg);
				 fclose ($file);
				$FTP_HOST ="131.1.18.6"; 
				$FTP_USER ="adm";
				$FTP_PW   ="root";
				$FTP_ROOT_DIR="/";
				$LOCAL_SERVER_DIR  = "/users/imacmicro/Documents";
				$FTP_DIR = "/users/imacmicro/Documents";

				$mode = FTP_BINARY; // or FTP_ASCII
				$conn_id = ftp_connect($FTP_HOST) or die("No se ralizo Conexion"); 
				if(ftp_login($conn_id, $FTP_USER, $FTP_PW))
				{
    				ftp_pwd($conn_id);
   					ftp_chdir($conn_id,$FTP_DIR);  
    				$files=$datafile;
    				$path="C:/Inetpub/wwwroot/MATRIX/";
    				$from = fopen($path.$files,"r");
   					 if(ftp_fput($conn_id, $files, $from, $mode))
    				{
      					 echo "ORDEN TRANSFERIDA AL LABORATORIO ! <br>";
   					 }
    				ftp_quit($conn_id);
				}
				 echo'<table border=1 width="350">';
				  echo'<tr><td align=center  width="604">**** ORDEN GRABADA ****</td></tr>';
				 echo'<tr><td align=center  width="604"><A HREF="ordenes.php" >RETORNAR</A></td></tr>';
			}
			else
			{	
				if(isset($historia) and $historia != "")
				{
					$conex_o = odbc_connect('facturacion','','');
					$query = "select pachis,pacnum,pacced,pacap1,pacap2,pacnom,paccer,pacres,pacsex,pacnac,pachab from inpac where pachis= '".$historia."'";
					$err_o = odbc_do($conex_o,$query);
					$campos= odbc_num_fields($err_o);
					if (odbc_fetch_row($err_o))
					{
						$odbc=array();
						for($m=1;$m<=$campos;$m++)
						{
							$odbc[$m-1]=odbc_result($err_o,$m);
						}
						$data="<tt><b>HISTORIA :</b> ".$odbc[0]." <b> NRO INGRESO : </b>".$odbc[1]."  <b>CEDULA : </b>".$odbc[2]."<b> NOMBRE :</b> ".$odbc[3]." ".$odbc[4]." ".$odbc[5]." <b>RESPONSABLE : </b>".$odbc[6]." - ".$odbc[7]." <b>SEXO :</b> ".$odbc[8]."  <b> FECHA NACIMIENTO :</b> ".$odbc[9]."<b> CAMA :</b> ".$odbc[10]."</tt>";
						$reg=$odbc[0]."|".$odbc[1]."|".$odbc[2]."|".$odbc[3]."|".$odbc[4]."|".$odbc[5]."|".$odbc[6]."|".$odbc[7]."|".$odbc[8]."|".$odbc[9]."|".$odbc[10]."|";
						echo "<input type='HIDDEN' name= 'reg' value='".$reg."'>";
					}
				}
				if(!isset($historia))
					$historia="";
				if(!isset($data))
					$data="";
				if(!isset($medico))
					$medico="";
				if(!isset($registro))
					$registro="";
				if(!isset($criterio))
					$criterio="";
				if(!isset($examenes))
					$examenes="";
				if(!isset($exa))
		            $exa="";
				echo'<table border=1 width="350">';
				echo'<tr><td rowspan=2 align=center  width="184">';
                echo'<img border="0" src="/MATRIX/images/medical/root/americas10.JPG" width="80" height="65"></td>';
				echo'<td align=center  width="604"><font size=3 color="#000080"><B> CLINICA LAS AMÉRICAS</font></td></tr>';
				echo'<tr><td  align=center width="604"><font size=2 color="#000080"><B>ORDEN DE LABORATORIO</font></TR>';
               	echo'<tr><td  colspan=2  width="604"><font size=2 color="#000080"><B>UNIDAD : ';
               	if (!isset($unidad))
               	{
					echo "<select name='unidad'>";
					$query = "select * from det_selecciones where medico='labora' and codigo='10' and activo='A'";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					for ($j=0;$j<$num;$j++)
					{	
						$row = mysql_fetch_array($err);
						echo "<option>".$row[2]."-".$row[3]."</option>";
					}	
				}
				else
				{
					echo $unidad;
					echo "<input type='HIDDEN' name= 'unidad' value='".$unidad."'>";
				}
				echo "</td></tr>";
               	echo"<tr><td  colspan=2><font size=2><B>HISTORIA No:</font><input type='text' name='historia' value='".$historia."'></td></tr>";
                echo"<tr><td  colspan=2><font size=3>".$data."</td></tr>";
                echo"<tr><td  colspan=2><font size=3><B>MEDICO:<input type='text' name='medico' size=50 maxlength=50 value='".$medico."'></b></font></td></tr>";
                echo"<tr><td  colspan=2><font size=3><B>REGISTRO:<input type='text' name='registro' size=10 maxlength=10 value='".$registro."'></b></td></font></tr>";
                echo"<tr><td  colspan=2><font size=3><B>CRITERIO:<input type='text' name='criterio' size=50 maxlength=50 value='".$criterio."'></b></td></font></tr>";
                echo"<tr><td  colspan=2><font size=2><B>EXAMENES : ";
                if ($criterio != "")
                {
					echo "<select name='examen'>";
					$query = "select codigo,descripcion from labora_000002 where descripcion like '%".$criterio."%'";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					for ($j=0;$j<$num;$j++)
					{	
						$row = mysql_fetch_array($err);
						echo "<option>".$row[0]."-".$row[1]."</option>";
					}	
				}
				else
				{
					echo "<select name='examen'>";
					$query = "select codigo,descripcion from labora_000002 where descripcion= '' ";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					for ($j=0;$j<$num;$j++)
					{	
						$row = mysql_fetch_array($err);
						echo "<option>".$row[0]."-".$row[1]."</option>";
					}	
				}
				echo "<input type='checkbox' name='si'>Adicionar</TD></TR>";
                echo'<tr><td  colspan=2 width="604"><font size=2 color="#000080"><B> EXAMENES SELECCIONADOS:</td></tr>';
                if(isset($examen) and strlen($examen)>0)
                {
	                if(isset($si) and $si == "on")
	                {
		                $exa=$exa."|".$examen;
                		$examenes=$examenes.chr(10).$examen;
            		}
            	}
            	echo "<input type='HIDDEN' name= 'exa' value='".$exa."'>";
                echo'<tr><td colspan=2><textarea rows="3" name="examenes" cols="45">'.$examenes.'</textarea></td></tr>'; 
                echo"<tr><td colspan=2 align=center><font size=2 color='#000080'><b>ORDEN COMPLETA : </b></font><input type='checkbox' name='ok'></td></tr>";
				echo'<tr><td colspan=2 align=center  width="604"><input type="submit" name="grabar" value="GRABAR"></td></tr>';
				echo'</table>';
			}
		}
		include_once("free.php");
?>
</body>
</html>