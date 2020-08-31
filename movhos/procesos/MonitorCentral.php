<html>
<head>
<title>MONITOR CENTRAL</title>
</head>
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	

	

	$itemx=explode("-",$wpar);
	$wpor="";
	if (isset($wdiv))                    //Este parametro se envia en opciones de Matrix con el nombre 'wdiv'
	   {
		$wdivx=explode("-",$wdiv);
		for ($i=0;$i<count($itemx);$i++)
			$wpor .= $wdivx[$i]."%,";
		$wpor = substr($wpor,0,strlen($wpor)-1);
	   }
	
	$wnp=0;
	$paths=array();
	//For segun la cantidad de opciones que vienen en el parametro
	for ($i=0;$i<count($itemx);$i++)
	   {
		$q = " SELECT ruta, programa "
		    ."   FROM root_000021 "
			."  WHERE Codopt=".$itemx[$i];
		$err = mysql_query($q,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num > 0)
		  {
			$row = mysql_fetch_array($err);
			$wnp++;                                      //Número del programa o consecutivo según los parametros
			$paths[$wnp] = $row[0].$row[1]."&wsup=on";   //Ruta, le agrega la variable wsup, para indicar que es un superusuario o usuario supervisor
			                                             //esto con el fin de c/u de los programas evalue esta variable y puede hacer cosas que otros
														 //usuarios no.
		  }
	   }
	   
	if (!isset($wdiv))
	   {
		$wpor="";
		$wcalculo=0;
		for ($i=0;$i<$wnp-1;$i++)
		   {
			$wcalculo += (integer)(100 / $wnp);            
			$wpor .= (string)((integer)(100 / $wnp))."%,";
		   }
		$wpor .= (string)((integer)(100 - $wcalculo))."%";
	  }
	  
	/*
	echo "<frameset rows=".$wpor." frameborder=1 framespacing=2 bordercolor='#FF0000'>";
	for ($i=1;$i<=$wnp;$i++)
		echo "<frame src='".$paths[$i]."' name='prog".$i."' marginwidth=0 marginheiht=0>";
	*/	
		
	switch (count($itemx))
	   {
	    case 2:
		   echo "<frameset cols=50%,* frameborder=1 framespacing=2 bordercolor='#FF0000'>";
		   for ($i=1;$i<=$wnp;$i++)
		       echo "<frame src='".$paths[$i]."' name='prog".$i."' marginwidth=0 marginheiht=0>";
		   break;
	    case 3:
		   echo "<frameset rows=50%,* frameborder=1 framespacing=2 bordercolor='#FF0000'>";
		   $j=1;
		       echo "<frame src='".$paths[$j]."' name='prog".$j."' marginwidth=0 marginheiht=0>";
		       $j++;
               echo "<frameset cols=50%,* frameborder=1 framespacing=2 bordercolor='#FF0000'>";
			   for ($i=$j;$i<=$wnp;$i++)
			       echo "<frame src='".$paths[$i]."' name='prog".$i."' marginwidth=0 marginheiht=0>";
			   echo "</frameset>";  
		   echo "</frameset>";	   
		   break;
		case 4:
		   echo "<frameset rows=50%,* cols=50%,* frameborder=1 framespacing=2 bordercolor='#FF0000'>";
		   for ($i=1;$i<=$wnp;$i++)
			    echo "<frame src='".$paths[$i]."' name='prog".$i."' marginwidth=0 marginheiht=0>";
		   echo "</frameset>";	   
		   break;
		case 5:
           echo "<frameset rows=50%,* frameborder=1 framespacing=2 bordercolor='#FF0000'>";
		      echo "<frameset cols=50%,* frameborder=1 framespacing=2 bordercolor='#FF0000'>";
			  for ($i=1;$i<=2;$i++)
			     echo "<frame src='".$paths[$i]."' name='prog".$i."' marginwidth=0 marginheiht=0>";
			  echo "</frameset>";
			  
		      echo "<frameset cols=33%,33%,* frameborder=1 framespacing=2 bordercolor='#FF0000'>";
			  for ($i=3;$i<=$wnp;$i++)
			     echo "<frame src='".$paths[$i]."' name='prog".$i."' marginwidth=0 marginheiht=0>";
			  echo "</frameset>";  
		   echo "</frameset>";	   
		   break;   
		
        case 6:
		   echo "<frameset rows=50%,* cols=33%,33%,* frameborder=1 framespacing=2 bordercolor='#FF0000'>";
		   for ($i=1;$i<=$wnp;$i++)
			    echo "<frame src='".$paths[$i]."' name='prog".$i."' marginwidth=0 marginheiht=0>";
		   echo "</frameset>";	   
		   break;
		   
		default:
           echo "<frameset rows=100% cols=100% frameborder=1 framespacing=2 bordercolor='#FF0000'>";
		   for ($i=1;$i<=$wnp;$i++)
			    echo "<frame src='".$paths[$i]."' name='prog".$i."' marginwidth=0 marginheiht=0>";
		   echo "</frameset>";	   
		   break;	
	   }
		
}
?>	
</html>