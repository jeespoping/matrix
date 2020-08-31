<html>
<head>
  <title>POSTANESTESIA</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
/*****************************************************
 *          INICIALIZA UNA POSANESTESIA              *
 *	CON LOS DATOS QUE SE TIENEN DEL PACIENTE         *
 *					CONEX, FREE => OK			     *
 *****************************************************/
	session_start();
	if(!isset($_SESSION['user']))
	echo "<br>error";
	else
	{
		$key = 	substr($user,2,strlen($user));
		

		
  
		$query= "insert into salam_000008 (medico,Fecha_data,Hora_data,Anestesiologo, Paciente, Fecha_cirugia, Hora_cirugia, Info_cirugia, Anestesicos, Metodos_anest, Tipo_bloqueo, Calidad_bloqueo, Posicion, Intubacion, Tamano_tubo, Relaj_intubacion, Manejo_via_aerea, Vent_mecanico, Salida_despierto, Estado_salida, Est_sal_int_ext_va, Laringoespamo, Vomito, Arritmia, Taquicardia, Bradicardia, Paro_cardiaco, Hemorragia_shock, Dep_resp, Otras_complica, Perdida_sang, Orina, Perdida_insensible, Total_egresos, Liquidos_aplicados, Sangre_aplicada, Total_ingresos, Balance,Seguridad)";
		$query= $query."values ('salam','".date("Y-m-d")."','".date("H:i:s")."','".$med."','".$paciente."','". $fecha."','". $hi."','NO ENCONTRADO' ,'.' , 'NO APLICA', 'NO APLICA', '04-NO APLICA', '01-DC SUPINO', '00-NO APLICA','NO APLICA','NO APLICA','01-OROFARINGEA','off','off','01-ESTABLE','01-INTUBADO','off','off','off','off','off','off','off','off','NO APLICA','NO APLICA','0','0','0','500','0','500','0','C-salam' ) ";
		$err= mysql_query($query);
		$ok=mysql_affected_rows();
		if($ok >=1)
		{
			echo "<tr><td><font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>SE GENERO LA POSTANESTESIA PARA ESTA CIRUGÍA!!!</MARQUEE></FONT>";
			echo "<br><br>";
		}
		else
		{
			echo "<center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
			echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>NO SE GENERO UNA NUEVA POSTANESTESIA PARA ESTA CIRUGÍA, CERCIORESE DE QUE NO EXISTA UNA !!!</MARQUEE></FONT>";
			echo "<br><br>";
		}
	include_once("free.php");
	}

	?>
</body>
</html>