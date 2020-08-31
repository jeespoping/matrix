<html>

<head>
  <title>AUTORIZACION V.1.00</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
   /**************************************************
	*		SOLICITUD DE AUTORIZACIÓN DE CIRUGIA	 *
	*		  PARA MEDICOS CIRUJANOS	V.1.00		 *
	*				CONEX, FREE => OK                *
	**************************************************/

session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

		// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)
	if(!isset($entidad) or !isset($diagnostico) or !isset($procedimiento) or !isset($clinica))
	{
		echo "<form action='000001_cx01.php' method=post>";
		echo "<center><table border=0 width=380>";
		echo "<tr><td align=center colspan=2><b><font color=#000066>CLÍNICA MEDICA LAS AMERICAS </font></b></td></tr>";
		echo "<tr><td align=center colspan=2><font color=#000066>TORRE MÉDICA</font></td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>PACIENTE: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='pac'</td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>DOCUMENTO: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='doc'</td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>ENTIDAD: </font></td>";	
		/* Si el medico no ha sido escogido Buscar a los oftalmologos de la seleccion para 
				construir el drop down*/
		echo "<td bgcolor=#cccccc colspan=1><select name='entidad'>";
		$query = "SELECT Descripcion FROM `det_selecciones` WHERE medico = 'cirugia' AND codigo = '004'  order by Descripcion ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num>0)
		{
			for ($j=0;$j<$num;$j++)
			{	
				$row = mysql_fetch_array($err);
				if ($row[0] == $entidad)
					echo "<option selected>".$row[0]."</option>";
				else
					echo "<option>".$row[0]."</option>";
			}
		}	// fin del if $num>0
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>DIAGNOSTICO: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><textarea ALIGN='CENTER'  ROWS='4' name='diagnostico' cols='30' ></textarea></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>PROCEDIMIENTO: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><textarea ALIGN='CENTER'  ROWS='4' name='procedimiento' cols='30' ></textarea></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>CLINICA: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='clinica'</td></tr>";
		echo "<tr><td align='center' bgcolor=#cccccc colspan=2><input type='submit' name='aceptar' value='ACEPTAR'></td><tr>";	
		
		echo "<tr></tr>";
		
	}
	else
	{
		
		
	    $date = date( "m" );
	    switch ($date)
	    {
		  case "01":$date=date("d")." de Enero de ".date("Y");    
		  break;
		  case "02":$date=date("d")." de Febrero de ".date("Y");    
		  break;
		  case "03":$date=date("d")." de Marzo de ".date("Y");    
		  break;
		  case "04":$date=date("d")." de Abril de ".date("Y");    
		  break;
	 	  case "05":$date=date("d")." de Mayo de ".date("Y");    
		  break;
		  case "06":$date=date("d")." de Junio de ".date("Y");    
		  break;
		  case "07":$date=date("d")." de Julio de ".date("Y");    
		  break;
		  case "08":$date=date("d")." de Agosto de ".date("Y");    
		  break;
		  case "09":$date=date("d")." de Septiembre de ".date("Y");    
		  break;
		  case "10":$date=date("d")." de Octubre de ".date("Y");    
		  break;
		  case "11":$date=date("d")." de Noviembre de ".date("Y");    
		  break;
	  	  case "12":$date=date("d")." de Diciembre de ".date("Y");    
		  break;
	    };
		
		echo "<table width=450 heigth=5000  border=0>";
		echo "<tr><td colspan=3><br><br><br><br><br><br><br><br><br><br><br><br><br><br></td></tr>";
		echo "<tr><td width=50></td><td><font color='black'>".$date."<br><b>Solicitud de autorización de cirugía.</b><br><br>Señores<br><b>".$entidad."</b>";
		echo "<br><br>Favor autorizar el tratamiento quirurgico al paciente <b>".$pac."</b> con documento <b>".$doc."</b> .<br><br>Diagnóstico: ".$diagnostico."<br><br>Procedimiento: ".$procedimiento;
		echo "<br><br>Clínica: ".$clinica."<br><br><br>Gracias, atte,</font></td><td width=50></td></tr>";	
		
	}
include_once("free.php");
}
?>
</body>
</html>