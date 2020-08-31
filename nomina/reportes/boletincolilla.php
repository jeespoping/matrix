<html>
<script type="text/javascript">

    // Abre el archivo PDF
  function abrir()
	{
		location.href = "/matrix/images/medical/comubol/boletincolilla.pdf";
	}
 
</script>

<?php
include_once("conex.php");
/******************************************************************
	   PROGRAMA : boletincolilla.php
	   Autor : Gabriel Agudelo
	   Version Actual : 2012-10-10
	   
	   OBJETIVO GENERAL : 
	   Este programa permite visualizar el boletin Hola Clinica 
	   el cual contiene un Link para ver la colilla de pago
	   
2013-02-18
		Se cambia el programa para que verifique en la tabla nomcolilla y
		verifica para que muestre solamente una vez al mes por usuario el boletin
		de clinica.
********************************************************************/
//session_start();
if(!$_SESSION["user"])
	echo "error";
else
{

	$key = substr($user,2,strlen($user));
	

	

	$pos = strpos($user,"-");
    $wusuario = substr($user,$pos+1,strlen($user));
    $fecha = date("Y-m-d");
	$fec = explode("-",$fecha);
	$anio = $fec[0];
	$mes = $fec[1];
	$dia = $fec[2];
	

	$antes = 0;
	if ($antes == 0)
	{
		$q =  " SELECT Nomusu,Nomano,Nommes,Nomdia from nomcolilla "
			 ."  WHERE  Nomusu = '".$wusuario."' ";
		  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $num = mysql_num_rows($res);
		  
		   if ($num > 0 )
			 {
			  for ($i=1;$i<=$num;$i++)
				 {   
				  $row = mysql_fetch_array($res);
				  
				  if ($row[0] == $wusuario and $row[1] == $anio and $row[2] == $mes )
					  {
						//include_once("000001_rep3.php");
						// Abre el Link Nomina Nueva
						header ("Location:https://www.sqlsoftware.nom.co:9443/AtgPMEDICA");
						
					  }
				  else 
					  {
						$q =  " UPDATE nomcolilla SET Nomano = '".$anio."',Nommes = '".$mes."',Nomdia = '".$dia."' "
							 ."  WHERE  Nomusu = ".$wusuario." ";
						$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						?>
						<script type="text/javascript"> abrir(); </script>  
						<?php
													
					  }
				 }  
			 }
			else
			   {
				 $query = "INSERT INTO nomcolilla VALUES ('".$wusuario."','".$anio."','".$mes."','".$dia."')";
				 $resultado = mysql_query($query,$conex)or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
				?>
				<script type="text/javascript"> abrir(); </script>  
				<?php
			   }
	}
	if($antes ==1)
	{
	  $q =    " SELECT  Nomusu,Nomano,Nommes,Nomdia from nomcolilla "
			 ."  WHERE  Nomusu = '".$wusuario."' "
			 ."    AND  Nomano = '".$anio."' "
			 ."    AND  Nommes = '".$mes."' ";
			 
			 
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);
	  
	  if ($num > 0 )
	  {
		//include_once("000001_rep3.php");
			// Abre el Link Nomina Nueva
			header ("Location:https://www.sqlsoftware.nom.co:9443/AtgPMEDICA");
		
	  }
	  else
	  {
		$q =  " SELECT Nomusu,Nomano,Nommes,Nomdia from nomcolilla "
			 ."  WHERE  Nomusu = '".$wusuario."' ";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);
		
		if ($num > 0 )
		{
			$q =  " UPDATE nomcolilla SET Nomano = '".$anio."',Nommes = '".$mes."',Nomdia = '".$dia."' "
								 ."  WHERE  Nomusu = ".$wusuario." ";
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		}
		else
		{
			$query = "INSERT INTO nomcolilla VALUES ('".$wusuario."','".$anio."','".$mes."','".$dia."')";
			
			$resultado = mysql_query($query,$conex)or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
		}
		?>
		<script type="text/javascript"> abrir(); </script>  
		<?php
													
	  }
	
	}
}
?>