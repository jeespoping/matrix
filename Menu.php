<html>
<head>
  	<title>MATRIX Programa General de Menu de Opciones</title>
<!-- Hojas de Estilo -->

    <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	#tipo1{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	#tipo2{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	.tipo3{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	.tipo4{color:#000066;background:#dddddd;font-size:6pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo5{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:bold;}
    	.tipo6{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo7{color:#FFFFFF;background:#000066;font-size:12pt;font-family:Tahoma;font-weight:bold;width:30em;}
    	#tipo8{color:#99CCFF;background:#000066;font-size:6pt;font-family:Tahoma;font-weight:bold;}
    	#tipo9{color:#660000;background:#dddddd;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo10{color:#FFFFFF;background:#000066;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo11{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo12{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo13{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo14{color:#FFFFFF;background:#000066;font-size:14pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo15{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo16{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	
    	#tipoG00{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoG01{color:#FFFFFF;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:8.5em;text-align:center;height:3em;}
    	#tipoG54{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:8.5em;text-align:center;height:3em;}
    	#tipoG55{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:59.5em;text-align:center;height:3em;}
    	#tipoG11{color:#FFFFFF;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:8.5em;text-align:center;height:3em;}
    	#tipoG21{color:#FFFFFF;background:#CC3333;font-size:7pt;font-family:Tahoma;font-weight:bold;width:8.5em;text-align:center;height:3em;}
    	#tipoG32{color:#FF0000;background:#FFFF66;font-size:7pt;font-family:Tahoma;font-weight:bold;width:8.5em;text-align:center;height:3em;}
    	#tipoG33{color:#006600;background:#FFFF66;font-size:7pt;font-family:Tahoma;font-weight:bold;width:8.5em;text-align:center;height:3em;}
    	#tipoG34{color:#000066;background:#FFFF66;font-size:7pt;font-family:Tahoma;font-weight:bold;width:8.5em;text-align:center;height:3em;}
    	#tipoG42{color:#FF0000;background:#00CC66;font-size:7pt;font-family:Tahoma;font-weight:bold;width:8.5em;text-align:center;height:3em;}
    	#tipoG41{color:#FFFFFF;background:#00CC66;font-size:7pt;font-family:Tahoma;font-weight:bold;width:8.5em;text-align:center;height:3em;}
    	#tipoG44{color:#000066;background:#00CC66;font-size:7pt;font-family:Tahoma;font-weight:bold;width:8.5em;text-align:center;height:3em;}
    	
    	#tipoM00{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Arial;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoM01{color:#000066;background:#C3D9FF;font-size:12pt;font-family:Arial;font-weight:bold;width:60em;text-align:left;height:4em;}
    	#tipoM02{color:#000066;background:#E8EEF7;font-size:12pt;font-family:Arial;font-weight:bold;width:60em;text-align:left;height:4em;}
    	#tipoM03{color:#000066;background:#FFFFFF;font-size:12pt;font-family:Arial;font-weight:bold;width:60em;text-align:left;height:4em;border-style:solid;border-color:#000066;}
    	
    </style>
</head>
<body BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return true">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	
	function nuevoAjax()
	{ 
		var xmlhttp=false; 
		try 
		{ 
			xmlhttp=new ActiveXObject("Msxml2.XMLHTTP"); 
		}
		catch(e)
		{ 
			try
			{ 
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); 
			} 
			catch(E) { xmlhttp=false; }
		}
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') { xmlhttp=new XMLHttpRequest(); } 

		return xmlhttp; 
	}

	function ajaxquery(fila,root,swiches,tabla)
	{
				
		st="root="+root+"&swiches="+swiches+"&tabla="+tabla;
	
		//alert("st="+st);
		try
		{
			ajax=nuevoAjax();
			ajax.open("POST", "Menu.php",true);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(st);
			
			ajax.onreadystatechange=function() 
			{ 
				if (ajax.readyState==4 && ajax.status==200)
				{ 
					//alert(ajax.responseText);
					document.getElementById(+fila).innerHTML=ajax.responseText;
				} 
			}
			if ( !estaEnProceso(ajax) ) 
			{
				ajax.send(null);
			}
		}catch(e){ }
	}
	
	function enter()
	{
		document.forms.Menu.submit();
	}
	function teclado()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57  || event.keyCode == 13) & event.keyCode != 46) event.returnValue = false;
	}
	function teclado1()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57 ) & event.keyCode != 46 & event.keyCode != 13)  event.returnValue = false;
	}
	function teclado2()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57 ) & (event.keyCode < 65 || event.keyCode > 90 ) & (event.keyCode < 97 || event.keyCode > 122 ) & event.keyCode != 13) event.returnValue = false;
	}
	function teclado3()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57 ) & (event.keyCode < 65 || event.keyCode > 90 ) & (event.keyCode < 97 || event.keyCode > 122 ) & event.keyCode != 13 & event.keyCode != 45) event.returnValue = false;
	}

//-->
</script>
<?php
include_once("conex.php");
echo "<div id='1'>";
/**********************************************************************************************************************  
	   PROGRAMA : Menu.php
	   Fecha de Liberacion : 2007-08-01
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2007-05-03
	   
	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface gr�fica que permite grabar los  de las
	   cirugias en los diversos quirofanos y en las horas especificadas por los cirujanos.
	   El programa valida :
	   						1. Que el quirofano este disponible para la cirugia.
	   						2. Que el cirujano NO este ocupado en otras cirugias (Cirugias Montadas).
	   						3. Que los equipos necesarios esten disponibles.
	   						4. Que las cirugias esten grabadas.
	   
	   Esta informacion sirvira para generar informacion en los distintos procesos de gestion en la unidad de cirugia
	   
	   
	   REGISTRO DE MODIFICACIONES :
	   		
	   .2007-05-03
	   		Release de Versi�n Beta.
	   		
***********************************************************************************************************************/

function Deep($menu,$limit,$nivel,$exp,$tabla,$swiches,$key)
{
	for($i=1;$i<=$limit;$i++)
	{
		if($exp % 2 == 0)
			$tipM="tipoM01";
		else
			$tipM="tipoM02";
		$blank="";
		for($j=1;$j<=$exp*2;$j++)
			$blank .= "&nbsp;";
		$root=($nivel*100)+$i;
		$itemx=explode(".",$swiches);
		$itemy=array();
		for ($j=1;$j<count($itemx);$j++)
			$itemy[(string)substr($itemx[(string)$j],0,strpos($itemx[(string)$j],"-"))]=substr($itemx[(string)$j],strpos($itemx[(string)$j],"-")+1);
		if($menu[(string)($nivel*100+$i)][0] > 0 and $itemy[(string)($nivel*100+$i)] == 1 and strpos($menu[(string)($nivel*100+$i)][3],$key) !== false)
		{
			$id="ajaxquery('1','".$root."','".$swiches."','".$tabla."')";
			echo "<tr><td id=".$tipM." OnClick=".$id.">".$blank."-".$menu[(string)($nivel*100+$i)][1]."</td></tr>";
			Deep(&$menu,$menu[(string)($nivel*100+$i)][0],($nivel*100+$i),$exp+1,$tabla,$swiches,$key);
		}
		else
			if($menu[(string)($nivel*100+$i)][0] > 0 and strpos($menu[(string)($nivel*100+$i)][3],$key) !== false)
			{
				$id="ajaxquery('1','".$root."','".$swiches."','".$tabla."')";
				echo "<tr><td id=".$tipM." OnClick=".$id.">".$blank."+".$menu[(string)($nivel*100+$i)][1]."</td></tr>";
			}
			else
			{
				if(strpos($menu[(string)($nivel*100+$i)][3],$key) !== false)
				{
					$tipM="tipoM03";
					$blank .= "&nbsp;&nbsp;";
					if($menu[(string)($nivel*100+$i)][2] != "NO")
					{
						$id="ajaxquery('1','".$root."','".$swiches."','".$tabla."')";
						echo "<tr><td id=".$tipM." OnClick=".$id.">".$blank."<A HREF='".$menu[(string)($nivel*100+$i)][2]."' target='_blank'>".$menu[(string)($nivel*100+$i)][1]."</td></tr>";
					}
					else
					{
						$id="ajaxquery('1','".$root."','".$swiches."','".$tabla."')";
						echo "<tr><td id=".$tipM." OnClick=".$id.">".$blank."".$menu[(string)($nivel*100+$i)][1]."</td></tr>";
					}
				}
			}
	}
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='Menu' action='Menu.php' method=post>";
	

	

	echo "<center><input type='HIDDEN' name= 'tabla' value='".$tabla."'>";
	echo "<td align=center valign=top><table border=0 align=center id=tipoM00>";
	$menu=array();
	$esquema=array();
	$query = "SELECT Mencod, Mendes, Mentre, Menurl, Menusr from root_000005 ";
	$query .= " where Menapl = '".$tabla."' ";
	$query .= "   and Menest = 'on' ";
	$query .= " Order by Mencod ";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$menu[(string)$row[0]][0]=$row[2];
			$menu[(string)$row[0]][1]=$row[1];
			$menu[(string)$row[0]][2]=$row[3];
			$menu[(string)$row[0]][3]=$row[4];
			$esquema[$i]=$row[0];
		}
	}
	else
	{
		$menu[(string)$row[0]][0]=0;
		$menu[(string)$row[0]][1]="Inicio";
	}
	if(!isset($swiches) or $root == 1)
	{
		$swiches = "";
		for ($i=0;$i<$num;$i++)
			if($i == 0)
				$swiches .= ".".$esquema[$i]."-1";
			else
				$swiches .= ".".$esquema[$i]."-0";
	}
	if(isset($root))
	{
		?>
		<script>
			function ira(){document.turfis.wfecha.focus();}
		</script>
		<?php
		$itemx=explode(".",$swiches);
		$itemy=array();
		for ($i=1;$i<count($itemx);$i++)
			$itemy[(string)substr($itemx[(string)$i],0,strpos($itemx[(string)$i],"-"))]=substr($itemx[(string)$i],strpos($itemx[(string)$i],"-")+1);
		if($itemy[(string)$root] == 0)
			$itemy[(string)$root]=1;
		else
			$itemy[(string)$root]=0;
		$swiches = "";
		for ($i=1;$i<count($itemx);$i++)
			$swiches .= ".".substr($itemx[(string)$i],0,strpos($itemx[(string)$i],"-"))."-".$itemy[(string)substr($itemx[(string)$i],0,strpos($itemx[(string)$i],"-"))];
	}
	else
		$root=1;
	$id="ajaxquery('1','1','".$swiches."','".$tabla."')";
	echo "<tr><td id=tipoM01 OnClick=".$id.">".$menu['1'][1]."</td></tr>";
	Deep(&$menu,$menu[1][0],1,1,$tabla,$swiches,$key);
	echo "</table></td>";
	echo "</table>";
	echo"</form>";
}
echo "</div>";
?>
</body>
</html>
