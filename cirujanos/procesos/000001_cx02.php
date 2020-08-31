<html>

<head>
  <title>HOSPITALIZACION V.1.00</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
   /**************************************************
	*		ORDEN DE HOSPITALIZACION DE CIRUGIA	 	 *
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
	if(!isset($diagnostico) or !isset($procedimiento) or !isset($clinica) or !isset($fecha) or !isset($hora) or !isset($h_entrada) or !isset($comida))
	{
		echo "<form action='000001_cx02.php' method=post>";
		echo "<center><table border=0 width=380>";
		echo "<tr><td align=center colspan=2><b><font color=#000066>CLÍNICA MEDICA LAS AMERICAS </font></b></td></tr>";
		echo "<tr><td align=center colspan=2><font color=#000066>TORRE MÉDICA</font></td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>PACIENTE: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='pac'</td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>DOCUMENTO: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='doc'</td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>DIAGNOSTICO: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><textarea ALIGN='CENTER'  ROWS='4' name='diagnostico' cols='30' ></textarea></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>PROCEDIMIENTO: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><textarea ALIGN='CENTER'  ROWS='4' name='procedimiento' cols='30' ></textarea></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>CLINICA: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='clinica'</td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>FECHA: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='fecha'</td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>HORA: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='hora'</td></tr>";	
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>PRESENTARSE EN LA CLINICA: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='h_entrada'</td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>DESAYUNO: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=1><input type='text' name='comida'</td></tr>";
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
		echo "<tr><td width=50></td><td><font color='black'><b>".$date."<br>Orden de hospitalización.</b><br><br>";
		echo "Paciente: ".$pac."<br>Documento: ".$doc."<br><br>";
		echo "Diagnóstico: ".$diagnostico."<br><br>Procedimiento: ".$procedimiento;
		echo "<br><br>Clínica: ".$clinica."<br><br>Fecha: ".$fecha."<br><br>Hora: ".$hora."<br><br>Presentarse en la clínica: ".$h_entrada;
		echo "<br><br>Desayuno liviano: ".$comida."<br><br>Favor presentar ésta nota al ingresar al quirofano.</font></td><td width=50></td></tr>";	
		
	}
include_once("free.php");
}
?>
</body>