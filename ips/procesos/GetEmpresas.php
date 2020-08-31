<html>
<head>
<title>EMPRESAS</title>
	<meta http-equiv="Content-Type" content="no-cache; text/html; charset=UTF-8">
 	<script type="text/javascript" src="../../zpgrid/utils/zapatec.js"></script>
	<script type="text/javascript" src="../../zpgrid/src/zpgrid.js"></script>
</head>

<body BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return true">
<a href="http://www.zapatec.com"></a>
<?php
include_once("conex.php");
	
	echo "<div id='divTablaEmp' style='display: none'>";
		echo "<table id='TablaEmpresas'>";
		

		

		$query = "select Empcod ,Empnom  from ".$empresa."_000024 where Empcod=Empres ";
		echo $query;
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			echo "<tr><td>CODIGO</td><td>NOMBRE</td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<tr><td>".$row[0]."</td><td>".$row[1]."</td></tr>";
			}
		}
		echo "</table>";
	echo "</div>";
	echo "<form>";
    echo "<input type='button' value='Seleccionar' onclick='seleccionarEmp()' />";
  	echo "</form>";
  	
	echo "<div id='GridEmpresas'></div>";
?>
<script language="javascript" >
	var objTabla= document.getElementById('TablaEmpresas');
	var objDivGrid= document.getElementById('GridEmpresas');
	var objGrid = new Zapatec.Grid
	({
     	source: objTabla,
     	sourceType: 'html',
     	theme: '../../zpgrid/themes/lightblue.css',
     	container: objDivGrid,
     	// Do not mark selected cells with different color
     	selectCells: false,
     	multipleSelect:false,
     	rowsPerPage: 10
     });
     	
	function seleccionarEmp() 
	{
    	var arrRows = objGrid.getSelectedRows();
		//------------------------------------------------------------------------------
    	// Verificar que el usuario haya seleccionado algun paciente. Si  no lo ha hecho
    	// Cerrar la pagina de busqueda y regresar a la pantalla que la  invoco.
		//------------------------------------------------------------------------------
    	if (arrRows.length > 0) 
    	{
			//------------------------------------------------------------------------------
    		// Asignar las variables seleccionadas, se asume que solo hay  seleccionado un
    		// paciente por tal motivo se usa solamente la fila 0.
			//------------------------------------------------------------------------------
    		iRow=0;
	     	codigo	 = objGrid.getCellValueString(objGrid.getCellByRow(arrRows [iRow], 0))
	     	nombre   = objGrid.getCellValueString(objGrid.getCellByRow (arrRows[iRow], 1))
			//------------------------------------------------------------------------------
    		// Si la ventana padre no esta abierta no realizar la operacion  de retornar los
    		// datos, de lo contrario asignar los datos del paciente a las  variables del
    		// formulario de recepcion.
			//------------------------------------------------------------------------------
    		if (window.opener && !window.opener.closed) 
    		{
				//------------------------------------------------------------------------------
    			// Asignar a las variables del formulario los valores que se  obtuvieron con la
    			// busqueda.
    			//  ------------------------------------------------------------------------------
    			window.opener.document.Rips.wcode.value = codigo;
	     		window.opener.document.Rips.wnome.value = nombre;
				window.close();
     		}
    	} 
    	else 
    	{
    		window.close();
    	}
	}
</script>

</body>
</html>



