<html>
<script type="text/javascript">

    // Abre el archivo PDF
  function abrir()
	{
		location.href = "/matrix/images/medical/soporte/";
	}
 
</script>

<?php
include_once("conex.php");
/******************************************************************
	   PROGRAMA : nompdf1.php
	   Autor : Gabriel Agudelo
	   Version Actual : 2013-11-13
	   
	   OBJETIVO GENERAL : 
	   Este programa permite visualizar los instructivos en pdf para la solicitud de clave  
	   y el consulta de saldo y retiros por internet.
	   
********************************************************************/
//session_start();
if(!$_SESSION["user"])
	echo "error";
else
{

	$key = substr($user,2,strlen($user));
	

	

	$key = substr($user,2,strlen($user));
		$cedula=$key;
		if(strlen($cedula) == 7)
			$cedula=substr($cedula,2);
		$conexUnix = odbc_connect('nomina','informix','sco')
			or die("No se pudo lograr conexion");
    
		$query = "	  SELECT percod,perap1,perap2,perno1,perno2,percco "
				 ." 	FROM noper Where percod = '".$cedula."'"
				 ."  	 AND peretr ='A'"
				 ."  	 AND peruni = '02' ";
		
			$err = odbc_do($conexUnix,$query);
			$campos= odbc_num_fields($err);
					
			if (odbc_fetch_row($err))
				{
					//echo "	<iframe width=100% height=100% align=center src='http://mx.lasamericas.com.co/matrix/images/medical/soporte/Instructivo retiros por Internet y consultas de saldos.pdf'></iframe>";	
					if(!isset($radio1) )
						{
							echo "<form action='nomina_instpdf.php' method=post>";
							echo "<center><table border=0 width=300>";
							echo "<tr><td align=center colspan=2><b>CLÍNICA LAS AMERICAS </b></td></tr>";
							echo "<tr><td align=center colspan=2>INSTRUCTIVO PARA EMPLEADOS CON SALARIO FLEXIBLE</td></tr>";
							echo"<tr><td align=center bgcolor=#cccccc ><INPUT TYPE = 'Radio'  NAME = 'radio1'  VALUE = 1'><b> Instructivo solicitud de clave <b></td></tr>";
							echo"<tr><td align=center bgcolor=#cccccc ><INPUT TYPE = 'Radio'  NAME = 'radio1'  VALUE = 2'><b> Instructivo retiros y consultas de saldos <b></td></tr>";
							echo"<tr><td align=center bgcolor=#cccccc colspan=2><input type='submit' value='IR'></td></tr></form>";
						}
					else
						{
						switch ($radio1)
							{
							case 1:
								echo "	<iframe width=100% height=100% align=center src='http://mx.lasamericas.com.co/matrix/images/medical/soporte/Instructivo solicitud de clave.pdf'></iframe>";	
								break;
							case 2:
								echo "	<iframe width=100% height=100% align=center src='http://mx.lasamericas.com.co/matrix/images/medical/soporte/Instructivo retiros por Internet y consultas de saldos.pdf'></iframe>";	
								break;
							}
						}
					/*  ?>
						<script type="text/javascript"> abrir(); </script>  
					<?php */
				
				}
			else
				{
					echo "<center><table border=10 width=300>";
						echo"<tr><td>ESTE INSTRUCTIVO SOLO APLICA PARA LOS EMPLEADOS QUE ESTAN EN SALARIO FLEXIBLE</td></tr></table>";
				}
				
				odbc_close($conexUnix);
				odbc_close_all();
}
?>