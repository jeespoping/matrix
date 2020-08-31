<html>
<head>
<title>MATRIX - [REPORTE GRAFICA PROCESOS PRIORITARIOS]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='grafpropriuce.php'; 
	}
	
	function enter()
	{
		document.forms.grafpropriuce.submit();
	}
	
	function cerrarVentana()
	{
	 window.close()
	}
	
	function VolverAtras()
	{
	 history.back(1)
	}
	
</script>

<?php
include_once("conex.php");

function pintar($cant,$tot,$nomb,$mes)
{
	$alto1=0;
	$alto2=0;

	$final = count($mes);

	IF ($final==1)
	{

		$alto1=$cant[0];
		$alto2=$tot[0];

		if ($alto2==0)
		{
			$porc0=0;
		}
		else
		{
			$porc0=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<table border=0 cellspacing=0 cellpadding=0 size='550'>";
		echo "<tr>";
		echo "<td align=center colspan='1' width=400><font text color=#000000 size=1></td>";
		echo "<td align=CENTER width=30><font size='1' color='#000000'><b>$alto1</b></font></td>";
		echo "<td align=CENTER width=30><font size='1' color='#000000'><b>$alto2</b></font></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td width=400><font size=1 color=#003366><b>$nomb[0]</b></font></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=center colspan='1' width=400><font text color=#000000 size=1></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[0]&nbsp;$porc0%</b></font></td>";
		echo "<tr><td>&nbsp;</td></tr>";
		echo "</tr>";
		echo "</table>";
	}

	IF ($final==2)
	{
		echo "<table border=0 colspan='5' cellspacing=0 cellpadding=0 size='500'>";
		echo "<tr>";
		echo "<td align=center colspan='1' width=380><font text color=#000000 size=1></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[0]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[0]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[1]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[1]</b></font></td>";
		echo "</tr>";

		$alto1=$cant[0];
		$alto2=$tot[0];

		if ($alto2==0)
		{
			$porc0=0;
		}
		else
		{
			$porc0=number_format(($alto1/$alto2)*100);
		}

		echo "<tr>";
		echo "<td width=400><font size=1 color=#003366><b>$nomb[0]</b></font></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[1];
		$alto2=$tot[1];

		if ($alto2==0)
		{
			$porc1=0;
		}
		else
		{
			$porc1=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=center colspan='1' width=400><font text color=#000000 size=1></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[0]&nbsp;$porc0%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[1]&nbsp;$porc1%</b></font></td>";
		echo "<tr><td>&nbsp;</td></tr>";
		echo "</tr>";

		echo "</table>";
	}

	IF ($final==3)
	{
		echo "<table border=0 colspan='7' cellspacing=0 cellpadding=0 size='550'>";
		echo "<tr>";
		echo "<td align=center colspan='1' width=370><font text color=#000000 size=1></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[0]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[0]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[1]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[1]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[2]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[2]</b></font></td>";
		echo "</tr>";

		$alto1=$cant[0];
		$alto2=$tot[0];

		if ($alto2==0)
		{
			$porc0=0;
		}
		else
		{
			$porc0=number_format(($alto1/$alto2)*100);
		}

		echo "<tr>";
		echo "<td width=400><font size=1 color=#003366><b>$nomb[0]</b></font></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[1];
		$alto2=$tot[1];

		if ($alto2==0)
		{
			$porc1=0;
		}
		else
		{
			$porc1=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[2];
		$alto2=$tot[2];

		if ($alto2==0)
		{
			$porc2=0;
		}
		else
		{
			$porc2=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=center colspan='1' width=400><font text color=#000000 size=1></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[0]&nbsp;$porc0%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[1]&nbsp;$porc1%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[2]&nbsp;$porc2%</b></font></td>";
		echo "<tr><td>&nbsp;</td></tr>";
		echo "</tr>";

		echo "</table>";
	}

	IF ($final==4)
	{
		echo "<table border=0 colspan='9' cellspacing=0 cellpadding=0 size='610'>";
		echo "<tr>";
		echo "<td align=center colspan='1' width=370><font text color=#000000 size=1></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[0]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[0]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[1]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[1]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[2]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[2]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[3]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[3]</b></font></td>";
		echo "</tr>";

		$alto1=$cant[0];
		$alto2=$tot[0];

		if ($alto2==0)
		{
			$porc0=0;
		}
		else
		{
			$porc0=number_format(($alto1/$alto2)*100);
		}

		echo "<tr>";
		echo "<td width=400><font size=1 color=#003366><b>$nomb[0]</b></font></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[1];
		$alto2=$tot[1];

		if ($alto2==0)
		{
			$porc1=0;
		}
		else
		{
			$porc1=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[2];
		$alto2=$tot[2];

		if ($alto2==0)
		{
			$porc2=0;
		}
		else
		{
			$porc2=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[3];
		$alto2=$tot[3];

		if ($alto2==0)
		{
			$porc3=0;
		}
		else
		{
			$porc3=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=center colspan='1' width=400><font text color=#000000 size=1></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[0]&nbsp;$porc0%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[1]&nbsp;$porc1%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[2]&nbsp;$porc2%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[3]&nbsp;$porc3%</b></font></td>";
		echo "<tr><td>&nbsp;</td></tr>";
		echo "</tr>";

		echo "</table>";
	}

	IF ($final==5)
	{
		echo "<table border=0 colspan='11' cellspacing=0 cellpadding=0 size='670'>";
		echo "<tr>";
		echo "<td align=center colspan='1' width=370><font text color=#000000 size=1></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[0]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[0]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[1]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[1]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[2]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[2]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[3]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[3]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[4]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[4]</b></font></td>";
		echo "</tr>";

		$alto1=$cant[0];
		$alto2=$tot[0];

		if ($alto2==0)
		{
			$porc0=0;
		}
		else
		{
			$porc0=number_format(($alto1/$alto2)*100);
		}

		echo "<tr>";
		echo "<td width=400><font size=1 color=#003366><b>$nomb[0]</b></font></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[1];
		$alto2=$tot[1];

		if ($alto2==0)
		{
			$porc1=0;
		}
		else
		{
			$porc1=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[2];
		$alto2=$tot[2];

		if ($alto2==0)
		{
			$porc2=0;
		}
		else
		{
			$porc2=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[3];
		$alto2=$tot[3];

		if ($alto2==0)
		{
			$porc3=0;
		}
		else
		{
			$porc3=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[4];
		$alto2=$tot[4];

		if ($alto2==0)
		{
			$porc4=0;
		}
		else
		{
			$porc4=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=center colspan='1' width=400><font text color=#000000 size=1></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[0]&nbsp;$porc0%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[1]&nbsp;$porc1%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[2]&nbsp;$porc2%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[3]&nbsp;$porc3%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[4]&nbsp;$porc4%</b></font></td>";
		echo "<tr><td>&nbsp;</td></tr>";
		echo "</tr>";

		echo "</table>";
	}

	IF ($final==6)
	{
		echo "<table border=0 colspan='13' cellspacing=0 cellpadding=0 size='730'>";
		echo "<tr>";
		echo "<td align=center colspan='1' width=370><font text color=#000000 size=1></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[0]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[0]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[1]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[1]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[2]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[2]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[3]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[3]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[4]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[4]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[5]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[5]</b></font></td>";
		echo "</tr>";

		$alto1=$cant[0];
		$alto2=$tot[0];

		if ($alto2==0)
		{
			$porc0=0;
		}
		else
		{
			$porc0=number_format(($alto1/$alto2)*100);
		}

		echo "<tr>";
		echo "<td width=400><font size=1 color=#003366><b>$nomb[0]</b></font></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[1];
		$alto2=$tot[1];

		if ($alto2==0)
		{
			$porc1=0;
		}
		else
		{
			$porc1=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[2];
		$alto2=$tot[2];

		if ($alto2==0)
		{
			$porc2=0;
		}
		else
		{
			$porc2=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[3];
		$alto2=$tot[3];

		if ($alto2==0)
		{
			$porc3=0;
		}
		else
		{
			$porc3=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[4];
		$alto2=$tot[4];

		if ($alto2==0)
		{
			$porc4=0;
		}
		else
		{
			$porc4=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[5];
		$alto2=$tot[5];

		if ($alto2==0)
		{
			$porc5=0;
		}
		else
		{
			$porc5=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=center colspan='1' width=400><font text color=#000000 size=1></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[0]&nbsp;$porc0%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[1]&nbsp;$porc1%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[2]&nbsp;$porc2%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[3]&nbsp;$porc3%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[4]&nbsp;$porc4%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[5]&nbsp;$porc5%</b></font></td>";
		echo "<tr><td>&nbsp;</td></tr>";
		echo "</tr>";

		echo "</table>";
	}

	IF ($final==7)
	{
		echo "<table border=0 colspan='15' cellspacing=0 cellpadding=0 size='790'>";
		echo "<tr>";
		echo "<td align=center colspan='1' width=370><font text color=#000000 size=1></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[0]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[0]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[1]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[1]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[2]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[2]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[3]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[3]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[4]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[4]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[5]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[5]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[6]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[6]</b></font></td>";
		echo "</tr>";

		$alto1=$cant[0];
		$alto2=$tot[0];

		if ($alto2==0)
		{
			$porc0=0;
		}
		else
		{
			$porc0=number_format(($alto1/$alto2)*100);
		}

		echo "<tr>";
		echo "<td width=400><font size=1 color=#003366><b>$nomb[0]</b></font></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[1];
		$alto2=$tot[1];

		if ($alto2==0)
		{
			$porc1=0;
		}
		else
		{
			$porc1=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[2];
		$alto2=$tot[2];

		if ($alto2==0)
		{
			$porc2=0;
		}
		else
		{
			$porc2=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[3];
		$alto2=$tot[3];

		if ($alto2==0)
		{
			$porc3=0;
		}
		else
		{
			$porc3=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[4];
		$alto2=$tot[4];

		if ($alto2==0)
		{
			$porc4=0;
		}
		else
		{
			$porc4=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[5];
		$alto2=$tot[5];

		if ($alto2==0)
		{
			$porc5=0;
		}
		else
		{
			$porc5=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[6];
		$alto2=$tot[6];

		if ($alto2==0)
		{
			$porc6=0;
		}
		else
		{
			$porc6=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=center colspan='1' width=400><font text color=#000000 size=1></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[0]&nbsp;$porc0%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[1]&nbsp;$porc1%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[2]&nbsp;$porc2%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[3]&nbsp;$porc3%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[4]&nbsp;$porc4%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[5]&nbsp;$porc5%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[6]&nbsp;$porc6%</b></font></td>";
		echo "<tr><td>&nbsp;</td></tr>";
		echo "</tr>";

		echo "</table>";
	}

	IF ($final==8)
	{
		echo "<table border=0 colspan='17' cellspacing=0 cellpadding=0 size='850'>";
		echo "<tr>";
		echo "<td align=center colspan='1' width=370><font text color=#000000 size=1></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[0]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[0]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[1]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[1]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[2]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[2]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[3]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[3]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[4]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[4]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[5]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[5]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[6]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[6]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[7]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[7]</b></font></td>";
		echo "</tr>";

		$alto1=$cant[0];
		$alto2=$tot[0];

		if ($alto2==0)
		{
			$porc0=0;
		}
		else
		{
			$porc0=number_format(($alto1/$alto2)*100);
		}

		echo "<tr>";
		echo "<td width=400><font size=1 color=#003366><b>$nomb[0]</b></font></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[1];
		$alto2=$tot[1];

		if ($alto2==0)
		{
			$porc1=0;
		}
		else
		{
			$porc1=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[2];
		$alto2=$tot[2];

		if ($alto2==0)
		{
			$porc2=0;
		}
		else
		{
			$porc2=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[3];
		$alto2=$tot[3];

		if ($alto2==0)
		{
			$porc3=0;
		}
		else
		{
			$porc3=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[4];
		$alto2=$tot[4];

		if ($alto2==0)
		{
			$porc4=0;
		}
		else
		{
			$porc4=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[5];
		$alto2=$tot[5];

		if ($alto2==0)
		{
			$porc5=0;
		}
		else
		{
			$porc5=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[6];
		$alto2=$tot[6];

		if ($alto2==0)
		{
			$porc6=0;
		}
		else
		{
			$porc6=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[7];
		$alto2=$tot[7];

		if ($alto2==0)
		{
			$porc7=0;
		}
		else
		{
			$porc7=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=center colspan='1' width=400><font text color=#000000 size=1></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[0]&nbsp;$porc0%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[1]&nbsp;$porc1%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[2]&nbsp;$porc2%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[3]&nbsp;$porc3%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[4]&nbsp;$porc4%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[5]&nbsp;$porc5%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[6]&nbsp;$porc6%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[7]&nbsp;$porc7%</b></font></td>";
		echo "<tr><td>&nbsp;</td></tr>";
		echo "</tr>";

		echo "</table>";
	}

	IF ($final==9)
	{
		echo "<table border=0 colspan='19' cellspacing=0 cellpadding=0 size='910'>";
		echo "<tr>";
		echo "<td align=center colspan='1' width=370><font text color=#000000 size=1></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[0]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[0]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[1]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[1]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[2]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[2]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[3]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[3]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[4]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[4]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[5]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[5]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[6]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[6]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[7]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[7]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[8]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[8]</b></font></td>";
		echo "</tr>";

		$alto1=$cant[0];
		$alto2=$tot[0];

		if ($alto2==0)
		{
			$porc0=0;
		}
		else
		{
			$porc0=number_format(($alto1/$alto2)*100);
		}

		echo "<tr>";
		echo "<td width=400><font size=1 color=#003366><b>$nomb[0]</b></font></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[1];
		$alto2=$tot[1];

		if ($alto2==0)
		{
			$porc1=0;
		}
		else
		{
			$porc1=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[2];
		$alto2=$tot[2];

		if ($alto2==0)
		{
			$porc2=0;
		}
		else
		{
			$porc2=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[3];
		$alto2=$tot[3];

		if ($alto2==0)
		{
			$porc3=0;
		}
		else
		{
			$porc3=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[4];
		$alto2=$tot[4];

		if ($alto2==0)
		{
			$porc4=0;
		}
		else
		{
			$porc4=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[5];
		$alto2=$tot[5];

		if ($alto2==0)
		{
			$porc5=0;
		}
		else
		{
			$porc5=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[6];
		$alto2=$tot[6];

		if ($alto2==0)
		{
			$porc6=0;
		}
		else
		{
			$porc6=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[7];
		$alto2=$tot[7];

		if ($alto2==0)
		{
			$porc7=0;
		}
		else
		{
			$porc7=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[8];
		$alto2=$tot[8];

		if ($alto2==0)
		{
			$porc8=0;
		}
		else
		{
			$porc8=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=center colspan='1' width=400><font text color=#000000 size=1></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[0]&nbsp;$porc0%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[1]&nbsp;$porc1%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[2]&nbsp;$porc2%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[3]&nbsp;$porc3%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[4]&nbsp;$porc4%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[5]&nbsp;$porc5%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[6]&nbsp;$porc6%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[7]&nbsp;$porc7%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[8]&nbsp;$porc8%</b></font></td>";
		echo "<tr><td>&nbsp;</td></tr>";
		echo "</tr>";

		echo "</table>";
	}

	IF ($final==10)
	{
		echo "<table border=0 colspan='21' cellspacing=0 cellpadding=0 size='970'>";
		echo "<tr>";
		echo "<td align=center colspan='1' width=370><font text color=#000000 size=1></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[0]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[0]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[1]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[1]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[2]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[2]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[3]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[3]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[4]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[4]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[5]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[5]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[6]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[6]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[7]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[7]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[8]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[8]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[9]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[9]</b></font></td>";
		echo "</tr>";

		$alto1=$cant[0];
		$alto2=$tot[0];

		if ($alto2==0)
		{
			$porc0=0;
		}
		else
		{
			$porc0=number_format(($alto1/$alto2)*100);
		}

		echo "<tr>";
		echo "<td width=400><font size=1 color=#003366><b>$nomb[0]</b></font></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[1];
		$alto2=$tot[1];

		if ($alto2==0)
		{
			$porc1=0;
		}
		else
		{
			$porc1=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[2];
		$alto2=$tot[2];

		if ($alto2==0)
		{
			$porc2=0;
		}
		else
		{
			$porc2=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[3];
		$alto2=$tot[3];

		if ($alto2==0)
		{
			$porc3=0;
		}
		else
		{
			$porc3=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[4];
		$alto2=$tot[4];

		if ($alto2==0)
		{
			$porc4=0;
		}
		else
		{
			$porc4=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[5];
		$alto2=$tot[5];

		if ($alto2==0)
		{
			$porc5=0;
		}
		else
		{
			$porc5=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[6];
		$alto2=$tot[6];

		if ($alto2==0)
		{
			$porc6=0;
		}
		else
		{
			$porc6=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[7];
		$alto2=$tot[7];

		if ($alto2==0)
		{
			$porc7=0;
		}
		else
		{
			$porc7=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[8];
		$alto2=$tot[8];

		if ($alto2==0)
		{
			$porc8=0;
		}
		else
		{
			$porc8=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[9];
		$alto2=$tot[9];

		if ($alto2==0)
		{
			$porc9=0;
		}
		else
		{
			$porc9=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=center colspan='1' width=400><font text color=#000000 size=1></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[0]&nbsp;$porc0%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[1]&nbsp;$porc1%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[2]&nbsp;$porc2%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[3]&nbsp;$porc3%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[4]&nbsp;$porc4%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[5]&nbsp;$porc5%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[6]&nbsp;$porc6%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[7]&nbsp;$porc7%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[8]&nbsp;$porc8%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[9]&nbsp;$porc9%</b></font></td>";
		echo "<tr><td>&nbsp;</td></tr>";
		echo "</tr>";

		echo "</table>";
	}

	IF ($final==11)
	{
		echo "<table border=0 colspan='23' cellspacing=0 cellpadding=0 size='1030'>";
		echo "<tr>";
		echo "<td align=center colspan='1' width=370><font text color=#000000 size=1></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[0]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[0]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[1]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[1]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[2]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[2]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[3]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[3]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[4]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[4]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[5]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[5]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[6]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[6]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[7]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[7]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[8]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[8]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[9]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[9]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[10]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[10]</b></font></td>";
		echo "</tr>";

		$alto1=$cant[0];
		$alto2=$tot[0];

		if ($alto2==0)
		{
			$porc0=0;
		}
		else
		{
			$porc0=number_format(($alto1/$alto2)*100);
		}

		echo "<tr>";
		echo "<td width=400><font size=1 color=#003366><b>$nomb[0]</b></font></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[1];
		$alto2=$tot[1];

		if ($alto2==0)
		{
			$porc1=0;
		}
		else
		{
			$porc1=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[2];
		$alto2=$tot[2];

		if ($alto2==0)
		{
			$porc2=0;
		}
		else
		{
			$porc2=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[3];
		$alto2=$tot[3];

		if ($alto2==0)
		{
			$porc3=0;
		}
		else
		{
			$porc3=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[4];
		$alto2=$tot[4];

		if ($alto2==0)
		{
			$porc4=0;
		}
		else
		{
			$porc4=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[5];
		$alto2=$tot[5];

		if ($alto2==0)
		{
			$porc5=0;
		}
		else
		{
			$porc5=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[6];
		$alto2=$tot[6];

		if ($alto2==0)
		{
			$porc6=0;
		}
		else
		{
			$porc6=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[7];
		$alto2=$tot[7];

		if ($alto2==0)
		{
			$porc7=0;
		}
		else
		{
			$porc7=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[8];
		$alto2=$tot[8];

		if ($alto2==0)
		{
			$porc8=0;
		}
		else
		{
			$porc8=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[9];
		$alto2=$tot[9];

		if ($alto2==0)
		{
			$porc9=0;
		}
		else
		{
			$porc9=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[10];
		$alto2=$tot[10];

		if ($alto2==0)
		{
			$porc10=0;
		}
		else
		{
			$porc10=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=center colspan='1' width=400><font text color=#000000 size=1></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[0]&nbsp;$porc0%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[1]&nbsp;$porc1%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[2]&nbsp;$porc2%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[3]&nbsp;$porc3%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[4]&nbsp;$porc4%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[5]&nbsp;$porc5%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[6]&nbsp;$porc6%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[7]&nbsp;$porc7%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[8]&nbsp;$porc8%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[9]&nbsp;$porc9%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[10]&nbsp;$porc10%</b></font></td>";
		echo "<tr><td>&nbsp;</td></tr>";
		echo "</tr>";

		echo "</table>";
	}

	IF ($final==12)
	{
		echo "<table border=0 colspan='25' cellspacing=0 cellpadding=0 size='1090'>";
		echo "<tr>";
		echo "<td align=center colspan='1' width=370><font text color=#000000 size=1></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[0]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[0]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[1]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[1]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[2]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[2]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[3]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[3]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[4]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[4]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[5]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[5]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[6]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[6]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[7]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[7]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[8]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[8]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[9]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[9]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[10]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[10]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$cant[11]</b></font></td>";
		echo "<td align=CENTER colspan='1' width=30><font size='1' color='#000000'><b>$tot[11]</b></font></td>";
		echo "</tr>";

		$alto1=$cant[0];
		$alto2=$tot[0];

		if ($alto2==0)
		{
			$porc0=0;
		}
		else
		{
			$porc0=number_format(($alto1/$alto2)*100);
		}

		echo "<tr>";
		echo "<td width=400><font size=1 color=#003366><b>$nomb[0]</b></font></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[1];
		$alto2=$tot[1];

		if ($alto2==0)
		{
			$porc1=0;
		}
		else
		{
			$porc1=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[2];
		$alto2=$tot[2];

		if ($alto2==0)
		{
			$porc2=0;
		}
		else
		{
			$porc2=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[3];
		$alto2=$tot[3];

		if ($alto2==0)
		{
			$porc3=0;
		}
		else
		{
			$porc3=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[4];
		$alto2=$tot[4];

		if ($alto2==0)
		{
			$porc4=0;
		}
		else
		{
			$porc4=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[5];
		$alto2=$tot[5];

		if ($alto2==0)
		{
			$porc5=0;
		}
		else
		{
			$porc5=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[6];
		$alto2=$tot[6];

		if ($alto2==0)
		{
			$porc6=0;
		}
		else
		{
			$porc6=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[7];
		$alto2=$tot[7];

		if ($alto2==0)
		{
			$porc7=0;
		}
		else
		{
			$porc7=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[8];
		$alto2=$tot[8];

		if ($alto2==0)
		{
			$porc8=0;
		}
		else
		{
			$porc8=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[9];
		$alto2=$tot[9];

		if ($alto2==0)
		{
			$porc9=0;
		}
		else
		{
			$porc9=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[10];
		$alto2=$tot[10];

		if ($alto2==0)
		{
			$porc10=0;
		}
		else
		{
			$porc10=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";

		$alto1=$cant[11];
		$alto2=$tot[11];

		if ($alto2==0)
		{
			$porc11=0;
		}
		else
		{
			$porc11=number_format(($alto1/$alto2)*100);
		}
		 
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_azul.gif'    BORDER=1 width=20 height=$alto1 ALIGN='bottom'></td>";
		echo "<td align=center valign=bottom><img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=20 height=$alto2 ALIGN='bottom'></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=center colspan='1' width=400><font text color=#000000 size=1></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[0]&nbsp;$porc0%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[1]&nbsp;$porc1%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[2]&nbsp;$porc2%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[3]&nbsp;$porc3%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[4]&nbsp;$porc4%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[5]&nbsp;$porc5%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[6]&nbsp;$porc6%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[7]&nbsp;$porc7%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[8]&nbsp;$porc8%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[9]&nbsp;$porc9%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[10]&nbsp;$porc10%</b></font></td>";
		echo "<td align=CENTER colspan='2' width=60><font size='1' color='#000000'><b>$mes[11]&nbsp;$porc11%</b></font></td>";
		echo "<tr><td>&nbsp;</td></tr>";
		echo "</tr>";

		echo "</table>";
	}



	$cant=Array();
	$mes=Array();
	$nomb=Array();
	$tot=Array();

}



/*******************************************************************************************************************************************
 *                                             REPORTE PARA GRAFICAR PROCESOS PRIORITARIOS                                                  *
 ********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte para graficar los procesos prioritarios                                                             |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : MARZO 30 DE 2010.                                                                                           |
//FECHA ULTIMA ACTUALIZACION  : MARZO 30 DE 2010.                                                                                           |
//DESCRIPCION			      : Este reporte sirve para graficar.                                                                           |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//pamec_000008     : Tabla de Bases de datos totales por proceso prioritario.                                                               |
//pamec_000001     : Tabla de movimiento procesos prioritarios.                                                                             |
//                                                                                                                                          |
//==========================================================================================================================================
include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");

$wactualiz=" 1.0 05-Abril-2010";

$usuarioValidado = true;

if (!isset($user) || !isset($_SESSION['user'])){
	$usuarioValidado = false;
}else {
	if (strpos($user, "-") > 0)
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}
if(empty($wuser) || $wuser == ""){
	$usuarioValidado = false;
}

session_start();
//Encabezado
encabezado("Grafica Procesos Prioritarios",$wactualiz,"clinica");

if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{

	$empre1='uce';

	//Forma
	echo "<form name='forma' action='grafpropriuce.php' method='post'>";
	echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";

	if (!isset($fec1) or $fec1 == '' or !isset($fec2) or $fec2 == '')
	{
		echo "<form name='grafpropriuce' action='' method=post>";

		//Cuerpo de la pagina
		echo "<table align='center' border=0>";

		//Ingreso de fecha de consulta
		echo '<span class="subtituloPagina2">';
		echo 'Ingrese los parámetros de consulta';
		echo "</span>";
		echo "<br>";
		echo "<br>";

		//Fecha inicial
		echo "<tr>";
		echo "<td class='fila1' width=190>Fecha Inicial</td>";
		echo "<td class='fila2' align='center' width=150>";
		campoFecha("fec1");
		echo "</td></tr>";
			
		//Fecha final
		echo "<tr>";
		echo "<td class='fila1'>Fecha Final</td>";
		echo "<td class='fila2' align='center'>";
		campoFecha("fec2");
		echo "</td></tr>";

		echo "<tr><td align=center colspan=2><br><input type='submit' id='searchsubmit' value='Graficar'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar

		echo "</table>";
		echo '</div>';
		echo '</div>';
		echo '</div>';
			
	}else{

		$mesi=SUBSTR(".$fec1.",6,2);
		$mesf=SUBSTR(".$fec2.",6,2);

		$quer1 = "CREATE TEMPORARY TABLE if not exists tempora1 as "
		."SELECT pames as mes,papp as pp,ppproc as proc,patotal as total"
		."  FROM ".$empre1."_000002 left join ".$empre1."_000001"
		."    ON papp=ppproc"
		."   AND pames=SUBSTRING(ppfecha,6,2)"
		."   AND paano=SUBSTRING(ppfecha,1,4)"
		." WHERE pames between '".$mesi."' and '".$mesf."'"
		."   AND paano = SUBSTRING('".$fec2."',1,4)"
		." GROUP BY 1,2,3,4"
		." ORDER by 1,2";

		//echo $quer1."<br>";

		$err4 = mysql_query($quer1, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $quer1 . " - " . mysql_error());

		$query1 ="SELECT SUBSTRING(ppfecha,6,2) as mes,ppproc as pp,count(*) as cant,patotal as total"
		."   FROM ".$empre1."_000001 left join ".$empre1."_000002"
		."     ON ppproc=papp"
		." AND SUBSTRING(ppfecha,6,2)=pames"
		." AND SUBSTRING(ppfecha,1,4)=paano"
		." WHERE ppfecha between '".$fec1."' and '".$fec2."'"
		." GROUP by 1,2,4"
		." UNION ALL"
		." SELECT mes,pp,0 as cant,total"
		."   FROM tempora1"
		."  WHERE proc IS NULL"
		." ORDER by 2,1";

		//echo $query1."<br>";
			
		$err1 = mysql_query($query1,$conex);
		$num1 = mysql_num_rows($err1);

		echo "<table border=0 align=center size='500'>";  //border=0 no muestra la cuadricula en 1 si.
		echo "<tr>";
		echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>PROCESOS PRIORITARIOS</b></font></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>TOTAL EVALUADOS &nbsp;&nbsp;&nbsp;<img src='/MATRIX/images/tavo/cuadro_azul.gif' BORDER=1 width=10 height=10>";
		echo "&nbsp;&nbsp;&nbsp TOTAL META &nbsp;&nbsp;&nbsp;<img src='/MATRIX/images/tavo/cuadro_magenta.gif' BORDER=1 width=10 height=10></b></td>";
		echo "</table>";
		 
		echo "<table border=0 cellspacing=0 cellpadding=0 size='500'>";
		echo "<tr>";
		echo "<td width=500><font text color=#000000 size=2><b>NOMBRE PROCESO PRIORITARIO</b></td>";
		echo "</tr>";
		echo "</table>";
		 
		$swtitulo='SI';
		$ppant='';
		$j=0;
		$k=0;
		$arrecant=Array();
		$arremes=Array();
		$arrenomb=Array();
		$arretot=Array();
		 
		for ($i=1;$i<=$num1;$i++)
		{
			$row1 = mysql_fetch_array($err1);

			IF ($swtitulo=='SI')
			{
				$ppant=$row1[1];
				$swtitulo='NO';
				 
				$arrenomb[$k]=$row1[1];
				$k=$k+1;
			}
			 
			IF ($ppant==$row1[1])
			{
				$nombremes='';
				switch ($row1[0])
				{
					case '01':
						$nombremes='ENE';
						break;
					case '02':
						$nombremes='FEB';
						break;
					case '03':
						$nombremes='MAR';
						break;
					case '04':
						$nombremes='ABR';
						break;
					case '05':
						$nombremes='MAY';
						break;
					case '06':
						$nombremes='JUN';
						break;
					case '07':
						$nombremes='JUL';
						break;
					case '08':
						$nombremes='AGO';
						break;
					case '09':
						$nombremes='SEP';
						break;
					case '10':
						$nombremes='OCT';
						break;
					case '11':
						$nombremes='NOV';
						break;
					case '12':
						$nombremes='DIC';
						break;
				}
				 
				$arremes[$j]=$nombremes;
				$arrecant[$j]=$row1[2];
				$arretot[$j]=$row1[3];
				$j=$j+1;

			}
			ELSE
			{
				pintar($arrecant,$arretot,$arrenomb,$arremes);
				 
				$arrecant=Array();
				$arremes=Array();
				$arrenomb=Array();
				$arretot=Array();

				$j=0;
				$arrecant[$j]=$row1[2];
				$arretot[$j]=$row1[3];

				$nombremes='';
				switch ($row1[0])
				{
					case '01':
						$nombremes='ENE';
						break;
					case '02':
						$nombremes='FEB';
						break;
					case '03':
						$nombremes='MAR';
						break;
					case '04':
						$nombremes='ABR';
						break;
					case '05':
						$nombremes='MAY';
						break;
					case '06':
						$nombremes='JUN';
						break;
					case '07':
						$nombremes='JUL';
						break;
					case '08':
						$nombremes='AGO';
						break;
					case '09':
						$nombremes='SEP';
						break;
					case '10':
						$nombremes='OCT';
						break;
					case '11':
						$nombremes='NOV';
						break;
					case '12':
						$nombremes='DIC';
						break;
				}
				 
				$arremes[$j]=$nombremes;
				 
				$j=$j+1;
				 
				$k=0;
				$arrenomb[$k]=$row1[1];
				$k=$k+1;

			}

			$ppant=$row1[1];
			$cantant=$row1[2];
			$totalant=$row1[3];
			$mesant=$row1[0];

		}// fin del for

		pintar($arrecant,$arretot,$arrenomb,$arremes);
		 
		echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
		echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
		echo "</table>";

	}// cierre del else donde empieza la impresión

}
?>