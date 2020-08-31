<?php
include_once("conex.php");PHP
    
	//generate the headers to help a browser choose the correct application
	/**/
    header( "Content-type: application/msword" );
    header( "Content-Disposition: inline, filename=000003_hd03.rtf");
    

	

	$fecha=$year."-".$month."-".$day;
	$query="select * from hemo_000003 where Paciente='".$pac."' and Fecha='".$fecha."' ";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if($num>0)
	{
		$filename= "000003_hd03.rtf";
    	$fp = fopen ( $filename, "r" );
   		 //read our template into a variable
    	$output = fread( $fp, filesize( $filename ) );
  
    	fclose ( $fp );
		$row = mysql_fetch_array($err);
		/*separar el registro del nombre del cardiologo*/
		$ini1=strpos($row["Cardiologo"],"-");
		$registro=substr($row["Cardiologo"],0,$ini1);     
		$cardiologo=substr($row["Cardiologo"],$ini1+1);
		/*separar la informacion del paciente*/
		$ini1=strpos($row["Paciente"],"-");
		$documento=substr($row["Paciente"],$ini1+1);
		$paciente=substr($row["Paciente"],0,$ini1);
		$ini1=strpos($documento,"-");
		$historia=substr($documento,$ini1+1);
		$documento=substr($documento,0,$ini1);
		$tci="";
		if($row["Obs_TCI"] != ".")
			$tci=$row["Obs_TCI"].chr(10).chr(13)."   ";
		if($row["Tercio_P_11"] != "01-Normal")
		{
			$tci=$tci."El vaso posee una ".substr($row["Tercio_P_11"],3)." en el tercio proximal";
			$r=1;
		}
		if($row["Tercio_M_11"] != "01-Normal")
		{
			if($r != 1)
			{
				$tci=$tci."El vaso posee una ".substr($row["Tercio_M_11"],3)." en el tercio medial";
				$r=1;
			}
			else
			 $tci=$tci.", una ".substr($row["Tercio_M_11"],3)." en el tercio medial";
			
		}
		if($row["Tercio_D_11"] != "01-Normal")
		{
			if($r != 1)
			{
				$tci=$tci."El vaso posee una ".substr($row["Tercio_D_11"],3)." en el tercio distal";
				$r=1;
			}
			else
			 $tci=$tci.", una ".substr($row["Tercio_D_11"],3)." en el tercio distal";
		}
		$tci=$tci.".   ".chr(10).chr(13)."Se observa un lecho distal ".substr($row["Lecho_D_TCI"],3).".";
		$output = str_replace( "<<tci>>",$tci, $output );
		$output = str_replace( "<<cardiologo>>", $cardiologo, $output );
     	$output = str_replace( "<<registro>>", $registro, $output );
     	$output = str_replace( "<<paciente>>", $paciente, $output );
     	$output = str_replace( "<<documento>>", $documento, $output );
		echo $output;
	}
	else
		ECHO "LA INFORMACIÓN NO FUE ENCONTRADA";
?>