<?php
include_once("conex.php");

/*MODIFICACION:
2013-02-28 Se cambia la opcion de separado por soltero en la lista del estado civil Viviana Rodas
2012-08-30 Se agrega el campo paccor que es el correo electronico del paciente. Viviana Rodas.
2012-08-29 Se agregan a la impresion los datos del responsable del usuario y la ocupacion del paciente. 
		   Campos: pacnou, pacpau, pacdiu, pacteu, Pacofi. Viviana Rodas.
2012-08-26 Se agrega los campos ingdie, ingcem en la consulta que trae los datos del ingreso. Viviana Rodas

*/
echo "<html>";
echo "<head>";
echo "<title>INFORME DE ADMISION DE PACIENTES</title>";
echo "</head>";
echo "<body TEXT='#000066'>";

$wfec=date("Y-m-d");
$hora=(string)date("H:i:s");

//$color="#dddddd";
$color="FFFFFF";
$color1="#cccccc";
$color2="#999999";

function valdxamb($whis,$wing)
{
	global $empresa;
	global $conex;

	$query= "SELECT pardxi ".
	"  FROM ".$empresa."_000120, ".$empresa."_000101".
	" WHERE partdx = 'P'".
	"   AND inghis = '".$whis."'".
	"   AND ingnin = '".$wing."'".
	"   AND parcco = ingsei";

	$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	if($num > 0)
	{
		$row = mysql_fetch_array($err);
		$wvaldxi=$row[0];
		return $wvaldxi;
	}
}

session_start();
if(!isset($_SESSION['user']))
	echo "Error Usuario NO Registrado";
else
{
	echo "<form name='r001_admision' action='r001_admision.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";

	if(!isset($user))
		echo "Error Usuario NO Registrado";
	else
	{
		if (isset($wpachi))
		{
		$query ="SELECT pacap1, pacap2, pacno1, pacno2, ingsei, ".
				"pacsex, pacfna, paciu,  pacdoc, pacest, ".
				"pacdir, pactel, ingent, ingtee, ".
	 	        "pacnoa, pacpaa, pacdia, pactea, ingnin, ".
	 	        "ingfei, ingtin, ingcai, pacdep, Ingdie, Ingcem, ".
				"pacnru, pacpru, pacdru, pactru, Pacofi, Paccor ".
		        "FROM ".$empresa."_000100, ".$empresa."_000101 ".
		        "WHERE pachis = '".$wpachi."'".
		        "AND ingnin = '".$wingni."'".
	            "AND inghis = pachis";

			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    		$num = mysql_num_rows($err);

    		if($num > 0)
			{
				$row = mysql_fetch_array($err);
		     	$wpacap1=$row[0];
		     	$wpacap2=$row[1];
		     	$wpacno1=$row[2];
		     	$wpacno2=$row[3];
			 	$wpacser=$row[4];
			 	$wpacsex=$row[5];
			 	$wpacfna=$row[6];
			 	$wpacciu=$row[7];
			 	$wpacdoc=$row[8];
			 	$wpacest=$row[9];
			 	$wpacdir=$row[10];
			 	$wpactel=$row[11];
			 	$wingent=$row[12];
			 	$wingtee=$row[13];
				$wpacnoa=$row[14];
				$wpacpaa=$row[15];
				$wpacdia=$row[16];
				$wpactea=$row[17];
				$wingnin=$row[18];
				$wingfei=$row[19];
				$wingtin=$row[20];
				$wingcai=$row[21];
				$wpacdep=$row[22];
				$wingdie=$row[23];  //direccion entidad
				$wingcem=$row[24]; //nit o cedula
				$wpacnou=$row[25]; //datos responsable del usuario
				$wpacpau=$row[26]; //agregados campos
				$wpacdiu=$row[27];
				$wpacteu=$row[28];
				$wpacofi=$row[29];
				$wpaccor=$row[30];

				if (isset($wpacser))
				{
					$query= "SELECT ccodes ".
		            	    "  FROM ".$empresa."_000003".
		         			" WHERE ccocod = '".$wpacser."'";

					$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    				$num = mysql_num_rows($err);

    				if($num > 0)
					{
						$row = mysql_fetch_array($err);
		     			$wdesser=$row[0];
					}
				}

				if (isset($wpacciu))
				{
					$query= "SELECT nombre ".
		            	    "  FROM root_000006".
		         			" WHERE codigo = '".$wpacciu."'";

					$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    				$num = mysql_num_rows($err);

    				if($num > 0)
					{
						$row = mysql_fetch_array($err);
			     		$wdesciu=$row[0];
					}
				}

				if (isset($wpacdep))
				{
					$query= "SELECT descripcion ".
		            	    "  FROM root_000002".
		         			" WHERE codigo = '".$wpacdep."'";

					$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    				$num = mysql_num_rows($err);

    				if($num > 0)
					{
						$row = mysql_fetch_array($err);
			     		$wdesdep=$row[0];
					}
				}

				$query= "SELECT egrdxi ".
		           	    "  FROM ".$empresa."_000108".
		           	    " WHERE egrhis = '".$wpachi."'".
		         		"   AND egring = '".$wingni."'";

				$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    			$num = mysql_num_rows($err);

    			if($num > 0)
				{
						$row = mysql_fetch_array($err);
		     			$wegrdxi=$row[0];

		     			$wvaldxi=valdxamb($wpachi,$wingni);
		     			if($wegrdxi == $wvaldxi)
		     				$wamb="on";
		     			else $wamb="off";
				}
				else $wamb="off";

				echo "<table align=left border=0 width=40%>";
				if ($empresa !='clisur')// para volverlo multiempresa
				{
					echo "<tr><td align=center ><IMG width=190 height=80 src='/matrix/images/medical/pos/logo_".$empresa.".png'></td>";
				}
				else
				{
					echo "<tr><td align=center><IMG width=210 height=100 SRC='/matrix/images/medical/Citas/logo_citascs.png'></td>";
				}
				echo "<tr><td align=center colspan=1><b>INSCRIPCION - INGRESO - EGRESO </b></tr>";
       	 		echo "<tr><td align=center colspan=1><font size=4><b>R.I.P.S. - URGENCIAS </b></td></tr>";
				echo "</table>";

				echo "<table align=center border=1 width=50%>";
				echo "<tr>";
				if($wamb=="on")
				{
					echo "<td bgcolor=".$color."><font size=2><b>Historia - Ingreso No. </font></b><br><font size=3>".$wpachi."-".$wingnin."</font></td>";
					echo "<td align=center bgcolor=".$color."><font size=2><b>Paciente </b></font><br><font size=4><b>***AMBULATORIO***</b></font></font></td>";
				}
				else
					echo "<td bgcolor=".$color." colspan=2><font size=2><b>Historia - Ingreso No. </font></b><br><font size=3>".$wpachi."-".$wingnin."</font></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td bgcolor=".$color." width=40%><font size=2><b>Primer Apellido </b></font><br><font size=3>".$wpacap1."</font></td>";
	  			echo "<td bgcolor=".$color." width=40%><font size=2><b>Segundo Apellido </b></font><br><font size=3>".$wpacap2."</font></td>";
	  			echo "</tr>";
	  			echo "<tr><td bgcolor=".$color." colspan=2><font size=2><b>Nombre </b></font><br><font size=3>".$wpacno1." ".$wpacno2."</font></td></tr>";
	  			echo "<tr>";
	  			echo "<td bgcolor=".$color."><font size=2><b>Servicio </b></font><br><font size=3>".$wdesser."</font></td>";
	  			echo "<td bgcolor=".$color."><font size=2><b>Cama No.</b></font><font size=3>";
	  			echo "</tr>";
	  			echo "</table>";

	  	    	echo "<br>";

	  	    	echo "<table align=center border=1 width=90%>";
	  			echo "<tr>";

	  			if ($wpacsex=="F")
	  		   		echo "<td bgcolor=".$color." colspan=1><font size=2><b>Sexo <br> Femenino <b><input type='radio' name='wfem' checked disabled></b>Masculino <b><input type='radio' name='wfem' disabled></b></b></td>";
		  		if ($wpacsex=="M")
		  	   		echo "<td bgcolor=".$color." colspan=1><font size=2><b>Sexo <br> Femenino <input type='radio' name='wfem' disabled>Masculino <input type='radio' name='wfem' checked disabled></b></td>";

		  	   	echo "<td bgcolor=".$color."><font size=2><b>Fecha Nacimiento</font></b><br><font size=3>".$wpacfna."</font></td>";
	    		echo "<td bgcolor=".$color." align=left><font size=2><b>Documento de Identidad </font></b><br><font size=3>".$wpacdoc."</font></td>";
	  			$ann=(integer)substr($wpacfna,0,4)*360 +(integer)substr($wpacfna,5,2)*30 + (integer)substr($wpacfna,8,2);
				$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
				$weda=($aa - $ann)/360;
				echo "<td bgcolor=".$color."><font size=2><b>Edad </font></b><br><font size=3>".number_format((double)$weda,0,'.','')."</font></td>";
	  			echo "</tr>";
	  			echo "<tr>";
	  			if ($wpacest=="C")
	  			{
 	  				echo "<td bgcolor=".$color." align=left colspan=2><font size=2><b>Estado Civil </font></b><input type= 'Checkbox' name= 'opc1' checked disabled> Casado
 		        	<input type= 'Checkbox' name= 'opc2' disabled> Divorciado
  		          	<input type= 'Checkbox' name= 'opc3' disabled> Soltero
  		          	<input type= 'Checkbox' name= 'opc4' disabled> Sin Dato
  				  	<input type= 'Checkbox' name= 'opc5' disabled> Union Libre
  				    <input type= 'Checkbox' name= 'opc6' disabled> Viudo</td>";
	  			}
	  			if ($wpacest=="D")
	  			{
 	  				echo "<td bgcolor=".$color." align=left colspan=2><font size=2><b>Estado Civil </font></b>
 	  			  		  <input type= 'Checkbox' name= 'opc1' disabled> Casado
 		          		  <input type= 'Checkbox' name= 'opc2' checked disabled> Divorciado
  		          		  <input type= 'Checkbox' name= 'opc3' disabled> Soltero
  		          		  <input type= 'Checkbox' name= 'opc4' disabled> Sin Dato
  				  		  <input type= 'Checkbox' name= 'opc5' disabled> Union Libre
  				  		  <input type= 'Checkbox' name= 'opc6' disabled> Viudo</td>";
	  			}
	  			if ($wpacest=="S")
	  			{
 	  				echo "<td bgcolor=".$color." align=left colspan=2><font size=2><b>Estado Civil </font></b>
 	  			  		  <input type= 'Checkbox' name= 'opc1' disabled> Casado
 		          		  <input type= 'Checkbox' name= 'opc2' disabled> Divorciado
  		          		  <input type= 'Checkbox' name= 'opc3' checked disabled> Soltero
  		          		  <input type= 'Checkbox' name= 'opc4' disabled> Sin Dato
  				  		  <input type= 'Checkbox' name= 'opc5' disabled> Union Libre
  				  		  <input type= 'Checkbox' name= 'opc6' disabled> Viudo</td>";
	  			}
				if ($wpacest=="SD")
	  			{
 	  				echo "<td bgcolor=".$color." align=left colspan=2><font size=2><b>Estado Civil </font></b>
 	  			  		  <input type= 'Checkbox' name= 'opc1' disabled> Casado
 		          		  <input type= 'Checkbox' name= 'opc2' disabled> Divorciado
  		          		  <input type= 'Checkbox' name= 'opc3' disabled> Soltero
  		          		  <input type= 'Checkbox' name= 'opc4' checked disabled> Sin Dato
  				  	  	  <input type= 'Checkbox' name= 'opc5' disabled> Union Libre
  				  		  <input type= 'Checkbox' name= 'opc6' disabled> Viudo</td>";
	  			}
	  			if ($wpacest=="U")
	  			{
 	  				echo "<td bgcolor=".$color." align=left colspan=2><font size=2><b>Estado Civil </font></b>
 	  			  		  <input type= 'Checkbox' name= 'opc1' disabled> Casado
 		          		  <input type= 'Checkbox' name= 'opc2' disabled> Divorciado
  		          		  <input type= 'Checkbox' name= 'opc3' disabled> Soltero
  		          		  <input type= 'Checkbox' name= 'opc4' disabled> Sin Dato
  				  		  <input type= 'Checkbox' name= 'opc5' checked disabled> Union Libre
  				  		  <input type= 'Checkbox' name= 'opc6' disabled> Viudo</td>";
	  			}
	  			if ($wpacest=="V")
	  			{
 	  				echo "<td bgcolor=".$color." align=left colspan=2><font size=2><b>Estado Civil </font></b>
 	  			  		  <input type= 'Checkbox' name= 'opc1' disabled> Casado
 		          		  <input type= 'Checkbox' name= 'opc2' disabled> Divorciado
  		          		  <input type= 'Checkbox' name= 'opc3' disabled> Soltero
  		          		  <input type= 'Checkbox' name= 'opc4' disabled> Sin Dato
  				  		  <input type= 'Checkbox' name= 'opc5' disabled> Union Libre
  				  		  <input type= 'Checkbox' name= 'opc6' checked disabled> Viudo</td>";
	  			}
				
				//consulta para traer la descripion de la profesion
				$query1 = "SELECT Descripcion from root_000003 where Codigo = '".$wpacofi."' ";
				$err1 = mysql_query($query1,$conex) or die(mysql_errno()." error buscando la profesion ".mysql_error());
				$row1 = mysql_fetch_array($err1);
				$wofi = $row1[0];
				
				echo "<td bgcolor=".$color." align=left colspan=1><font size=2><b>Oficio/Ocupacion</font></b><br><font size=3>".$wofi."</font></td>";
				echo "<td bgcolor=".$color." align=left colspan=1><font size=2><b>Correo Electronico</font></b><br><font size=3>".$wpaccor."</font></td>";
				
				echo "</tr>";
				
				echo "<tr>";
				echo "<td bgcolor=".$color1." align=left colspan=1><font size=2><b>Entidad Responsable</font></b><br><font size=3>".$wingent."</font></td>";
				echo "<td bgcolor=".$color1." align=left colspan=1><font size=2><b>Nit</font></b><br><font size=3>".$wingcem."</font></td>";
				$wingdie=strtoupper($wingdie);
				echo "<td bgcolor=".$color1." align=left colspan=1><font size=2><b>Direcci&oacute;n</font></b><br><font size=3>".$wingdie."</font></td>";
	  			echo "<td bgcolor=".$color1." align=left colspan=1><font size=2><b>Telefono </font></b><br><font size=3>".$wingtee."</font></td></tr>";

	  			echo "<tr><td bgcolor=".$color." align=left colspan=1><font size=2><b>Direccion Actual Paciente</font></b><br><font size=3>".$wpacdir."</font></td>";
	  			echo "<td bgcolor=".$color." align=left colspan=1><font size=2><b>Telefono </font></b><br><font size=3>".$wpactel."</font></td>";
	  			echo "<td bgcolor=".$color." align=left colspan=1><font size=2><b>Municipio </font></b><br><font size=3>".$wdesciu."</font></td>";
	  			echo "<td bgcolor=".$color." align=left colspan=1><font size=2><b>Dpto. </font></b><br><font size=3>".$wdesdep."</font></td></tr>";

	  			// echo "<tr>";
	  			// echo "<td bgcolor=".$color." align=left colspan=1><font size=2><b>En Caso Urgente Avisar A: </font></b><br><font size=3>".$wpacnoa."</font></td>";
	  			// echo "<td bgcolor=".$color." align=left colspan=1><font size=2><b>Parentesco </font></b><br><font size=3>".$wpacpaa."</font></td>";
	  			// echo "<td bgcolor=".$color." align=left colspan=1><font size=2><b>Direcci&oacute;n </font></b><br><font size=3>".$wpacdia."</font></td>";
	  			// echo "<td bgcolor=".$color." align=left colspan=1><font size=2><b>Telefono </font></b><br><font size=3>".$wpactea."</font></td>";
	  			// echo "</tr>";
				
				//datos del acompañante
				echo "<tr>";
	  			echo "<td bgcolor=".$color1." align=left colspan=1><font size=2><b>Datos del Acompa&ntilde;ante: </font></b><br><font size=3>".$wpacnoa."</font></td>";
	  			echo "<td bgcolor=".$color1." align=left colspan=1><font size=2><b>Parentesco </font></b><br><font size=3>".$wpacpaa."</font></td>";
	  			echo "<td bgcolor=".$color1." align=left colspan=1><font size=2><b>Direccion </font></b><br><font size=3>".$wpacdia."</font></td>";
	  			echo "<td bgcolor=".$color1." align=left colspan=1><font size=2><b>Telefono </font></b><br><font size=3>".$wpactea."</font></td>";
	  			echo "</tr>";
				
				//datos del responsable del usuario
				$wpacnou=strtoupper($wpacnou);
				$wpacpau=strtoupper($wpacpau);
				$wpacdiu=strtoupper($wpacdiu);
				$wpacteu=strtoupper($wpacteu);
				echo "<tr>";
	  			echo "<td bgcolor=".$color1." align=left colspan=1><font size=2><b>Responsable del Usuario: </font></b><br><font size=3>".$wpacnou."</font></td>";
	  			echo "<td bgcolor=".$color1." align=left colspan=1><font size=2><b>Parentesco </font></b><br><font size=3>".$wpacpau."</font></td>";
	  			echo "<td bgcolor=".$color1." align=left colspan=1><font size=2><b>Direccion </font></b><br><font size=3>".$wpacdiu."</font></td>";
	  			echo "<td bgcolor=".$color1." align=left colspan=1><font size=2><b>Telefono </font></b><br><font size=3>".$wpacteu."</font></td>";
	  			echo "</tr>";
				
				echo "<tr><td colspan=4></td></tr>";
				echo "</table>";

				echo "<table align=center border=1 width=90%>";
	  			echo "<tr><td align=LEFT colspan=7 bgcolor=".$color2."><b>INGRESO</b></td>";
	  			echo "<tr>";

	  			$cadena= explode("-",$wingfei);
	  			$ano=$cadena[0];
	  			$mes=$cadena[1];
	  			$dia=$cadena[2];

	  			if (isset($wingtin))
				{
					$query = "SELECT Seldes ".
				         	 "  FROM ".$empresa."_000105 ".
	                     	 " WHERE Seltip= '07' ".
	                     	 "   AND Selcod= '".$wingtin."'".
	                     	 "   AND Selest= 'on'";

					$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    				$num = mysql_num_rows($err);

    				if($num > 0)
					{
						$row = mysql_fetch_array($err);
		     			$wdestin=$row[0];
					}
					else
						$wdestin= "NO APLICA";
				}

				if (isset($wingcai))
				{
	        		$query = "SELECT Seldes ".
	                		 "  FROM ".$empresa."_000105 ".
	               	 		 " WHERE Seltip='08' ".
	               	 		 "   AND Selcod= '".$wingcai."'".
	               	 		 "   AND Selest= 'on'";

	            	$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    				$num = mysql_num_rows($err);

    				if($num > 0)
					{
						$row = mysql_fetch_array($err);
		     			$wdescai=$row[0];
					}
					else
						$wdescai= "NO APLICA";
				}

			  	echo "<td bgcolor=".$color."><font size=2><b>Dia </font></b><br><font size=3>".$dia."</font></td>";
	  			echo "<td bgcolor=".$color."><font size=2><b>Mes </font></b><br><font size=3>".$mes."</font></td>";
	  			echo "<td bgcolor=".$color."><font size=2><b>Año </font></b><br><font size=3>".$ano."</font></td>";
	  			echo "<td bgcolor=".$color."><font size=2><b>Hora de Ingreso </font></b><br><font size=3>".$hora."</font></td>";
	  			echo "<td bgcolor=".$color."><font size=2><b>Tipo de Ingreso </font></b><br><font size=3>".$wdestin."</font></td>";
	  			echo "<td bgcolor=".$color."><font size=2><b>Causa de Ingreso </font></b><br><font size=3>".$wdescai."</font></td>";
	  			echo "<td bgcolor=".$color."><font size=2><b>Entidad </font></b><br><font size=3>".$wingent."</font></td>";
	  			echo "</tr>";
	  			echo "</table>";

	  			echo "<table align=center border=1 width=90%>";
	  			echo "<tr><td width=5% align=center rowspan=7 bgcolor=".$color2."><b>INFO.<br>MEDICA</b></td></tr>";
	  			echo "<tr><td colspan=4 bgcolor=".$color."><font size=2><b>No. De Autorizacion: </font></b></td></tr>";
 	  			echo "<tr><td colspan=4 bgcolor=".$color."><font size=2><b>Causa Externa: </font></b></td></tr>";
 	  			echo "<tr>";
 	  			echo "<td bgcolor=".$color."><font size=1><input type= 'Checkbox' name= 'opc1' disabled> ACCIDENTE DE TRABAJO </font></td>";
 	  			echo "<td bgcolor=".$color."><font size=1><input type= 'Checkbox' name= 'opc2' disabled> OTRO TIPO DE ACCIDENTE </font></td>";
				echo "<td bgcolor=".$color."><font size=1><input type= 'Checkbox' name= 'opc3' disabled> SOSPECHA MALTRATO FISICO </font></td>";
 		    	echo "<td bgcolor=".$color."><font size=1><input type= 'Checkbox' name= 'opc4' disabled> ENFERMEDAD GENERAL </font></td></tr>";
 		    	echo "<tr>";
 		    	echo "<td bgcolor=".$color."><font size=1><input type= 'Checkbox' name= 'opc5' disabled> ACCIDENTE DE TRANSITO </font></td>";
 		    	echo "<td bgcolor=".$color."><font size=1><input type= 'Checkbox' name= 'opc6' disabled> EVENTO CATASTROFICO   </font></td>";
				echo "<td bgcolor=".$color."><font size=1><input type= 'Checkbox' name= 'opc7' disabled> SOSPECHA ABUSO SEXUAL </font></td>";
  		    	echo "<td bgcolor=".$color."><font size=1><input type= 'Checkbox' name= 'opc8' disabled> ENFERMEDAD PROFESIONAL </font></td></tr>";
  				echo "<tr>";
  				echo "<td bgcolor=".$color."><font size=1><input type= 'Checkbox' name= 'opc9' disabled> ACCIDENTE RABICO </font></td>";
  				echo "<td bgcolor=".$color."><font size=1><input type= 'Checkbox' name= 'opc10' disabled> LESION POR AGRESION </font></td>";
  				echo "<td bgcolor=".$color."><font size=1><input type= 'Checkbox' name= 'opc11' disabled> SOSPECHA VIOLACION SEXUAL </font></td>";
  				echo "<td bgcolor=".$color."><font size=1><input type= 'Checkbox' name= 'opc12' disabled> OTRA </font></td></tr>";
  				echo "<tr>";
  				echo "<td bgcolor=".$color."><font size=1><input type= 'Checkbox' name= 'opc13' disabled> ACCIDENTE OFIDICO </font></td>";
  				echo "<td bgcolor=".$color."><font size=1><input type= 'Checkbox' name= 'opc14' disabled> LESION AUTOINFLINGIDA </font></td>";
  				echo "<td colspan=2 bgcolor=".$color."><font size=1><input type= 'Checkbox' name= 'opc15' disabled> SOSPECHA MALTRATO EMOCIONAL </font></td></tr>";
  				echo "</table>";

  				echo "<table align=center border=1 width=90%>";
  				echo "<tr><td width=5% align=center rowspan=10 bgcolor=".$color2."><b>INFO.<br>MEDICA</b></td></tr>";
  				echo "<tr>";
				echo "<td colspan=2 bgcolor=".$color."><font size=2><b>Diagnostico De Egreso: </font></b></td>";
				echo "<td bgcolor=".$color."><font size=2><b>Codigo </font></b></td>";
				echo "<td width= 20 bgcolor=".$color1.">&nbsp&nbsp</td>";
				echo "<td width= 20 bgcolor=".$color1.">&nbsp&nbsp</td>";
				echo "<td width= 20 bgcolor=".$color1.">&nbsp&nbsp</td>";
				echo "<td width= 20 bgcolor=".$color1.">&nbsp&nbsp</td>";
				echo "</tr>";

				echo "<tr>";
	  			echo "<td colspan=2 bgcolor=".$color."><font size=2><b>1. </font></b></td>";
				echo "<td bgcolor=".$color."><font size=2><b>Codigo </font></b></td>";
				echo "<td width= 20 bgcolor=".$color1.">&nbsp&nbsp</td>";
				echo "<td width= 20 bgcolor=".$color1.">&nbsp&nbsp</td>";
				echo "<td width= 20 bgcolor=".$color1.">&nbsp&nbsp</td>";
				echo "<td width= 20 bgcolor=".$color1.">&nbsp&nbsp</td>";
				echo "</tr>";

				echo "<tr><td rowspan=3 bgcolor=".$color."><font size=2><b>Diagnostico Relacionado </font></b></td></tr>";
	  			echo "<tr>";
	  			echo "<td bgcolor=".$color."><font size=2><b>2. _____________________________________________</font></b><br></td>";
				echo "<td bgcolor=".$color."><font size=2><b>Codigo </font></b></td>";
				echo "<td width= 20 bgcolor=".$color1.">&nbsp&nbsp</td>";
				echo "<td width= 20 bgcolor=".$color1.">&nbsp&nbsp</td>";
				echo "<td width= 20 bgcolor=".$color1.">&nbsp&nbsp</td>";
				echo "<td width= 20 bgcolor=".$color1.">&nbsp&nbsp</td>";
				echo "</tr>";
				echo "<tr>";
	  			echo "<td bgcolor=".$color."> <font size=2><b>3. _____________________________________________</font></b><br></td>";
				echo "<td bgcolor=".$color."> <font size=2><b>Codigo </font></b></td>";
				echo "<td width= 20 bgcolor=".$color1.">&nbsp&nbsp</td>";
				echo "<td width= 20 bgcolor=".$color1.">&nbsp&nbsp</td>";
				echo "<td width= 20 bgcolor=".$color1.">&nbsp&nbsp</td>";
				echo "<td width= 20 bgcolor=".$color1.">&nbsp&nbsp</td>";
				echo "</tr>";

				echo "<tr><td colspan=7 bgcolor=".$color." align=left><font size=2><b>Destino Del Usuario a la Salida de Observacion: </font></b><br>
			      	  <input type= 'Checkbox' name= 'opc1' disabled> Alta Urgencias
 		          	  <input type= 'Checkbox' name= 'opc2' disabled> Remision a Otro Nivel de Complejidad
  		          	  <input type= 'Checkbox' name= 'opc3' disabled> Hospitalizacion </td></tr>";
				echo "<tr><td colspan=1 bgcolor=".$color." align=left><font size=2><b>Estado de la Salida: </font></b><br>
			    	  <input type= 'Checkbox' name= 'opc1' disabled> Vivo
 		          	  <input type= 'Checkbox' name= 'opc2' disabled> Muerto </td>";
 		        echo "<td colspan=6 bgcolor=".$color." align=left><font size=2><b>Tipo de diagnostico principal: </font></b><br>
			    	  <input type= 'Checkbox' name= 'opc1' disabled> Impresion Diagnostica
 		          	  <input type= 'Checkbox' name= 'opc2' disabled> Confirmado nuevo 
 		          	  <input type= 'Checkbox' name= 'opc3' disabled> Confirmado confirmado repetido </td></tr>";
 				echo "<tr>";

				echo "<td colspan=2 bgcolor=".$color." ><font size=2><b>Causa Basica de la Muerte en Urgencias: </font></b></td>";
				echo "<td bgcolor=".$color."><font size=2><b>Codigo </font></b></td>";
				echo "<td width= 20 bgcolor=".$color1.">&nbsp&nbsp</td>";
				echo "<td width= 20 bgcolor=".$color1.">&nbsp&nbsp</td>";
				echo "<td width= 20 bgcolor=".$color1.">&nbsp&nbsp</td>";
				echo "<td width= 20 bgcolor=".$color1.">&nbsp&nbsp</td>";
				echo "</tr>";
				echo "</table>";

				echo "<table align=center border=1 width=90%>";
				echo "<td width=30% bgcolor=".$color."><font size=3><b>Fecha de </font></b><br><font size=2><b> Salida </font></b></td>";
				echo "<td bgcolor=".$color."><font size=2><b>Dia </font></b></td>";
				echo "<td bgcolor=".$color."><font size=2><b>Mes </font></b></td>";
				echo "<td bgcolor=".$color."><font size=2><b>Año </font></b></td>";
				echo "<td width=30% bgcolor=".$color."><font size=2><b>Hora de Salida: </font></b></td>";
				echo "</tr>";
				echo "</table>";

	  			echo "<table align=center border=1 width=90%>";
	  			echo "<tr><td align=center bgcolor=".$color2."><b>CONSULTA</b></td></tr>";
	  			echo "<tr><td align=center bgcolor=".$color." ><font size=3><b>DEBE REMITIRSE A FORMATO HONORARIOS MEDICOS - RIPS </font></b><br>
	  		          <font size=3><b>CADA VEZ QUE REALICE UNA VISITA </font></b></td></tr>";
			}
			else
			{
				?>
				<script>
					alert ("No se encontro ingreso para la Historia");
					window.close();
					//function ira(){document.r001_admision.wpachi.focus();}
				</script>
				<?php
			}
	  	}
	}
	echo "</body>";
	echo "</html>";
	include_once("free.php");
}
?>