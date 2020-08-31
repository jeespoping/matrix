<html>
<head>
  <title>MATRIX Hoja de Identificacion De Pacientes</title>
      <style type="text/css">
		A	{text-decoration: none;color: #000066;}
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	
    	#tipo01{color:#000000;background:#CCCCCC;font-size:45pt;font-family:Arial;font-weight:bold;text-align:center;height:10em;}
    	#tipo02{color:#000000;background:#DDDDDD;font-size:25pt;font-family:Arial;font-weight:bold;text-align:center;height:5em;}
    	#tipo03{color:#000000;background:#999999;font-size:25pt;font-family:Arial;font-weight:bold;text-align:center;height:2em;}
    	
    </style>
</head>
<body BGCOLOR="">
<BODY TEXT="#000000">
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='ID_PAC.php' method=post>";
	

	

	if(!isset($whis))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>HOJA DE IDENTIFICACION DE PACIENTES Ver. 2010-02-15</b></td></tr>";
		echo "<tr><td bgcolor=#cccccc>Numero De Historia</td><td bgcolor=#cccccc align=center><input type='TEXT' name='whis' size=12 maxlength=12></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center colspan=2><input type='submit' value='IR'></td></tr></table>";
	}
	else
	{
		//                  0        1       2       3       4       5       6    
		$query  = "select pachis, pacnum, pacnom, pacap1, pacap2, pactid, pacced from inpac ";
		$query .= " where pachis = ".$whis;
		$conex_o = odbc_connect('admisiones','','');
		$err_o = odbc_do($conex_o,$query);
		$campos= odbc_num_fields($err_o);
		$count=0;
		if(odbc_fetch_row($err_o))
		{
			$count++;
			$odbc=array();
			for($m=1;$m<=$campos;$m++)
			{
				$odbc[$m-1]=odbc_result($err_o,$m);
			}
			$ann=(integer)substr($odbc[5],0,4)*360 +(integer)substr($odbc[5],5,2)*30 + (integer)substr($odbc[5],8,2);
			$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
			$EDAD=(integer)($aa - $ann)/360;
			$NOM=trim($odbc[3]).chr(32).trim($odbc[4]).chr(32).trim($odbc[2]);
			echo "<center><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><table border=0>";
			echo "<tr><td  id=tipo01 colspan=2>".$NOM."</td></tr>";
			echo "<tr><td  id=tipo03 colspan=2></td></tr>";
			echo "<tr><td  id=tipo02>Historia: ".$odbc[0]."</td><td  id=tipo02>Ingreso: ".$odbc[1]."</td></tr>";
			echo "<tr><td  id=tipo02>Tipo ID: ".$odbc[5]."</td><td id=tipo02>Identificacion: ".$odbc[6]."</td></tr>";
			echo "</table>";
		}
		
		odbc_close($conex_o);
		odbc_close_all();

	}
}
?>
</body>
</html>