<html>
<head>
<title>MATRIX Frames</title>
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
	if(isset($wdiv))
	{
		$wdivx=explode("-",$wdiv);
		for ($i=0;$i<count($itemx);$i++)
			$wpor .= $wdivx[$i]."%,";
		$wpor = substr($wpor,0,strlen($wpor)-1);
	}
	
	$wnp=0;
	$paths=array();
	for ($i=0;$i<count($itemx);$i++)
	{
		$query = "select ruta, programa from root_000021 where Codopt=".$itemx[$i];
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			$row = mysql_fetch_array($err);
			$wnp++;
			$paths[$wnp] = $row[0].$row[1];
			//echo $paths[$wnp]."<br>";
		}
	}
	if(!isset($wdiv))
	{
		$wpor="";
		$wsuma=0;
		for ($i=0;$i<$wnp-1;$i++)
		{
			$wsuma += (integer)(100 / $wnp);
			$wpor .= (string)((integer)(100 / $wnp))."%,";
		}
		$wpor .= (string)((integer)(100 - $wsuma))."%";
	}
	echo "<frameset rows=".$wpor." frameborder=1 framespacing=2 bordercolor='#FF0000'>";
	for ($i=1;$i<=$wnp;$i++)
		echo "<frame src='".$paths[$i]."' name='prog".$i."' marginwidth=0 marginheiht=0>";
}
?>	
</html>