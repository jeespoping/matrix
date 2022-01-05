<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tipos de turnero</title>
	<!--  -->
    <link href="bootstrap.min.css" rel="stylesheet">
    <link href="facSer_style.css" rel="stylesheet">
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link href="http://mtx.lasamericas.com.co/matrix/soporte/procesos/stylehelpDesk.css" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="facSer_js.js" type="text/javascript"></script>
	
	<!-- dejar como definitivo -->
	<!--
    <link href="http://mtx.lasamericas.com.co/matrix/soporte/procesos/stylehelpDesk.css" rel="stylesheet">
    <link href="http://mtx.lasamericas.com.co/include/root/matrix.css" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="facSer_js.js" type="text/javascript"></script>
	-->


	
	<!-- asi está en triage -->
	<!--
		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
		
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
		<script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
		<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
		<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
		<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
	-->


	<!-- PESTAÑAS 
    <script>
        $( function() {
            $( "#tabs" ).tabs();
        } );
    </script> 
	-->
    <script>
	
	    function onChange(nomObj)
        {
			document.getElementById("accion")
			document.getElementById("accion").value = 'crear';
			document.getElementById("codNom").value = "Nuevo turnero";
			document.getElementById("codCco").value = "";
			document.getElementById("lblMsg").innerHTML  = '';
		}
		
		// Activa modo de creación de turnero
        function activarNueva()
        {
			document.getElementById("accion").value = 'crear';
			document.getElementById("accionCat").value = "";	
			document.getElementById("codNom").value = "Nuevo turnero";
			document.getElementById("codCco").value = "";
			document.getElementById("codigoCat").value = "";
			document.getElementById("nombreCat").value = "";
			document.getElementById("ordenCat").value = "";
			document.getElementById("prefijoCat").value = "";
			document.getElementById("codigoSubct").value = "";
			document.getElementById("nombreSubct").value = "";
			// Eliminar datos que no sean útiles para el turnero que se va a crear.
			document.getElementById("lblMsg").innerHTML  = '';
			document.getElementById("lblId").innerHTML = '';
			document.getElementById("lstSubCat").innerHTML = "";
			document.getElementById("lstCat").innerHTML = "";
			var arrCat = [];
			txt = JSON.stringify(arrCat);
			document.getElementById("arrCat" ).value = txt;
		}

		function onChecked (objChk)
		{
			if (objChk == "chkCat") {
			  var divCategorias = document.getElementById("divCategorias");
			  if (document.getElementById("chkCat").checked == true){
				divCategorias.style.display = "block";
			  } else {
				divCategorias.style.display = "none";
			  }
			}
			else if (objChk == "chkTdo") {
			  var chkTtd = document.getElementById("chkTtd");
			  if (document.getElementById("chkTdo").checked == true){
				chkTtd.checked = true;
			  } else {
				chkTtd.checked = false;
			  }
			}

		}
		
		// Activa modo de creación de categoría
		function AddCat(cat="cat")
		{
			if (cat=="cat")
			{
				document.getElementById("accionCat").value = "ADDCAT";
				var codigoCat = document.getElementById("codigoCat");
				var nombreCat = document.getElementById("nombreCat");
				var prefijoCat = document.getElementById("prefijoCat");
				var ordenCat = document.getElementById("ordenCat");
				nombreCat.value = "";
				codigoCat.value = "";
				prefijoCat.value = "";
				ordenCat.value = "";
				codigoCat.disabled = false;
				codigoCat.focus();
			}
			else
			{
				document.getElementById("accionCat").value = "ADDSCT";
				var codigoCat = document.getElementById("codigoSubct");
				var nombreCat = document.getElementById("nombreSubct");
				nombreCat.value = "";
				// crear código aleatorio de subcategoria
				// con una N de prefijo para saber que debe crearse en db.
				codigoCat.value = "N" + (Math.floor(Math.random() * (10000 - 0))).toString();
				codigoCat.disabled = true;
				nombreCat.focus();
			}
		}

		// Registra la categoría que se está creando
		function RegCat(cat="cat")
		{
			// alert ("-");
			var codigoCat = document.getElementById("codigoCat");
			var nombreCat = document.getElementById("nombreCat");
			var prefijoCat = document.getElementById("prefijoCat");
			var ordenCat = document.getElementById("ordenCat");
			var lstCat = document.getElementById("lstCat");
			
			if (document.getElementById("accionCat").value == "ADDCAT") {
				if (ExisteCategoria (codigoCat.value))
				{
					alert ("Ya existe una categoría con ese código");
					codigoCat.focus();
				}
				else
				{
					// Actualizar datos en pantalla
					var oOption = document.createElement("OPTION");
					lstCat.options.add(oOption);
					oOption.text = nombreCat.value +" ("+ codigoCat.value +")";
					oOption.value = codigoCat.value;					
					
					// Mostrar las subcategorias en el onclick
					oOption.onclick = (function(cod,nom,ord,pre) {
						return function() {							
							onclickCat(cod,nom,ord,pre)
						};
					})(codigoCat.value,nombreCat.value,ordenCat.value,prefijoCat.value);
					
					// arrCat[iterator]['sercod'],arrCat[iterator]['sernom'],arrCat[iterator]['serord'],arrCat[iterator]['serpre'],arrCat[iterator]['Subcat']
					
					// Agregar cat en el arreglo
					var txt = document.getElementById("arrCat").value;
					var arrCat = JSON.parse(txt);
					// cargar el arreglo de cat-subcat
					arrCat.push({"sercod":codigoCat.value,"sernom":nombreCat.value,"serord":ordenCat.value,"serpre":prefijoCat.value,"Subcat":[{"codSct":"","nomSct":""}]});
					// Actualizar el json con la cat
					txt = JSON.stringify(arrCat);
					document.getElementById("arrCat" ).value = txt;
					//alert (txt);
					document.getElementById("accionCat").value = "";					
					
					// alert ("Categoría creada");
				}

			}
			else if (document.getElementById("accionCat").value == "ADDSCT") {  // AGREGAR SUBCATEGORIA
				//return;
				var codigoSct = document.getElementById("codigoSubct");
				var nombreSct = document.getElementById("nombreSubct");

				// No se valida código de subcat, porque se crea un
				// consecutivo global dentro de las subcat.
				
				/*
				if (ExisteCategoria (codigoCat.value, codigoSubct))
				{
					alert ("Ya existe una subcategoría con ese código");
					codigoSct.focus();
				}
				else
				*/
				
					// Actualizar datos en pantalla

					var lstSubCat = document.getElementById("lstSubCat");
					var oOption = document.createElement("OPTION");
					lstSubCat.options.add(oOption);
					oOption.text = nombreSct.value ; // +" ("+ codigoSct.value +")";
					oOption.value = codigoSct.value;								
										
					//-----
					
					// Actualizar el json con la subcat
					var txt = document.getElementById("arrCat").value;
					var arrCat = JSON.parse(txt);
					// buscar la categoría editada.
					for (iterator = 0; iterator < arrCat.length; iterator++) {
						if (arrCat[iterator]['sercod'].toUpperCase()== codigoCat.value.toUpperCase())
						{
							// Agregar subcat
							arrCat[iterator]['Subcat'].push({"codSct":codigoSct.value,"nomSct":nombreSct.value});
						}
					}
					// Actualizar el json con las cat-subcat
					txt = JSON.stringify(arrCat);
					document.getElementById("arrCat" ).value = txt;
					//alert (txt);
					document.getElementById("accionCat").value = "";	
					
					// alert ("Subcategoría creada");
				

			}
			document.getElementById("accionCat").value = "";

		}

		// Actualiza la categoría seleccionada,
		// con lo que el usr digite en los campos.
		function updateCat(cat="cat")
		{
			if (document.getElementById("accionCat").value == "ADDCAT" || document.getElementById("accionCat").value == "ADDSCT")
				return;
			
			// Actualizar datos en pantalla
			document.getElementById("accionCat").value = "";

			var e = document.getElementById("lstCat");
			var codigoCat = document.getElementById("codigoCat");
			codigoCat.disabled = true;
			var nombreCat = document.getElementById("nombreCat");
			if (cat=="cat")
				e.options[e.selectedIndex].text = nombreCat.value + " (" + codigoCat.value + ")";

			var e = document.getElementById("lstSubCat");
			var codigoSct = document.getElementById("codigoSubct");
			codigoSct.disabled = true;
			var nombreSct = document.getElementById("nombreSubct");
			if (cat=="sct")
				e.options[e.selectedIndex].text = nombreSct.value; // + " (" + codigoSct.value + ")";
			
			// Actualizar el json con las cat-subcat
			//var txt = document.getElementById("selTema").value;
			var txt = document.getElementById("arrCat").value;
			//alert (txt);
			// var tema = JSON.parse(txt);
			// cargar el arreglo de cat-subcat
			// var arrCat = tema['arrCat']; 
			var arrCat = JSON.parse(txt);
			// buscar la categoría editada.
			// var codigoCatEdit = document.getElementById("codigoCatEdit");
			for (iterator = 0; iterator < arrCat.length; iterator++) {
				if (arrCat[iterator]['sercod'].toUpperCase()== codigoCat.value.toUpperCase())
				{
					if (cat=="cat") 
					{
						arrCat[iterator]['sernom'] = nombreCat.value;
						arrCat[iterator]['serord'] = document.getElementById("ordenCat").value;
						arrCat[iterator]['serpre'] = document.getElementById("prefijoCat").value;
					}
					else
					{
						// Buscar la subcategoría
						for (i = 0; i < arrCat[iterator]['Subcat'].length; i++) {
							if (arrCat[iterator]['Subcat'][i]['codSct'].toUpperCase()== codigoSct.value.toUpperCase())
							{
								arrCat[iterator]['Subcat'][i]['nomSct'] = nombreSct.value;
							}
						}
					}
				}
			}
			
			//tema["arrCat"] = arrCat;
			//txt = JSON.stringify(tema);
			txt = JSON.stringify(arrCat);
			//alert (txt);
			//document.getElementById("selTema").value = txt;	
			// document.getElementById("txTem" + tema['Codtem']).value = txt;
			//document.getElementById("txTema").value = txt;
			//alert (document.getElementById("txTema").value);
			document.getElementById("arrCat").value = txt;
		}

		// Valida si el código de categoría existe.
		function ExisteCategoria (cat,sct="")
		{
			
			// cargar el arreglo de cat-subcat
			var txt = document.getElementById("arrCat").value;
			var arrCat = JSON.parse(txt);
			// buscar la categoría.
			if (Array.isArray(arrCat))
			{
				for (iterator = 0; iterator < arrCat.length; iterator++) {
					if (arrCat[iterator]['sercod'].toUpperCase()== cat.toUpperCase())
					{
						return true;
					}
				}
			}			
			return false;
		}

		function DelCat(cat="cat")
		{

			//alert ("DELCAT " + cat);
			document.getElementById("accionCat").value = "";
			
			var codigoCat = document.getElementById("codigoCat");
			if ( codigoCat.value.trim()=="")
				return;
			
			var txt = document.getElementById("arrCat").value;
			//alert (txt);
			var arrCat = JSON.parse(txt);
			// buscar la categoría editada.
			var index = -1;
			for (iterator = 0; iterator < arrCat.length; iterator++) {
				if (arrCat[iterator]['sercod'].toUpperCase()== codigoCat.value.toUpperCase())
				{
					index = iterator;
					break;
				}
			}
			if (index > -1) {
				if ( cat=="cat" ) // Borrar categoría
				{
					arrCat.splice(index, 1);  // elimina el elemento "index"
					var selectobject = document.getElementById("lstCat");
					for (var i=0; i<selectobject.length; i++) {
						if (selectobject.options[i].value.toUpperCase() == codigoCat.value.toUpperCase())
							selectobject.remove(i);
					}
				}
				else // Borrar subcategoría
				{
					// alert ("Delsct de cat " + arrCat[index]['sercod']);
					var codigoSct = document.getElementById("codigoSubct");
					if ( codigoSct.value.trim()=="")
						return;
					var i = -1;
					for (iterator = 0; iterator < arrCat[index]['Subcat'].length; iterator++) {
						if (arrCat[index]['Subcat'][iterator]['codSct'].toUpperCase()== codigoSct.value.toUpperCase())
						{
							//alert ("Delsubcat " + codigoSct.value);
							i = iterator;
						}
					}
					if (i > -1) {
						//alert ("splice " + i.toString());
						arrCat[index]['Subcat'].splice(i, 1);  // elimina el elemento "index"
						selectobject = document.getElementById("lstSubCat");
						for (var i=0; i<selectobject.length; i++) {
							if (selectobject.options[i].value.toUpperCase() == codigoSct.value.toUpperCase())
								selectobject.remove(i);
						}
					}
				}
			}
			txt = JSON.stringify(arrCat);
			//alert (txt);
			document.getElementById("arrCat").value = txt;
			document.getElementById("accionCat").value = "";
			document.getElementById("codigoCat").value="";
			document.getElementById("nombreCat").value="";
			document.getElementById("prefijoCat").value="";
			document.getElementById("ordenCat").value="";
			document.getElementById("codigoSubct").value="";
			document.getElementById("nombreSubct").value="";
			
		}
		
        function cargarTema()
        {
			document.getElementById("accionCat").value = "";
			
			var i;
			//var txt = document.getElementById("txTem" + tema.value).value;
			var txt = document.getElementById("selTema").value;
			//alert (txt);
			var tema = JSON.parse(txt);
			document.getElementById("accion").value = 'grabar';
			document.getElementById("lblMsg").innerHTML  = '';
			document.getElementById("lblId").innerHTML  = tema['Codtem'] + "-";
			// Textos
			document.getElementById("codTem").value = tema['Codtem'];
			document.getElementById("codNom").value = tema['Codnom'];
			document.getElementById("codSgt").value = tema['Codsgt'];
			document.getElementById("codCco").value = tema['Codcco'];
			document.getElementById("txtMbv").value = tema['Codmbv'];
			document.getElementById("txtMlc").value = tema['Codmlc'];
			document.getElementById("txtMim").value = tema['Codmim'];
			document.getElementById("txtMtd").value = tema['Codmtd'];
			document.getElementById("txtMdp").value = tema['Codmdp'];
			document.getElementById("txtMtg").value = tema['Codmtg'];
			document.getElementById("txtMct").value = tema['Codmct'];
			document.getElementById("txtMst").value = tema['Codmst'];
			document.getElementById("txtMsn").value = tema['Codmsn'];
			document.getElementById("txtSno").value = tema['Codsno'];
			document.getElementById("txtMse").value = tema['Codmse'];
			document.getElementById("txtSte").value = tema['Codste'];
			document.getElementById("txtMsc").value = tema['Codmsc'];
			document.getElementById("txtMnd").value = tema['Codmnd'];
			document.getElementById("txtMno").value = tema['Codmno'];
			document.getElementById("txtMed").value = tema['Codmed'];
			document.getElementById("txtMsu").value = tema['Codmsu'];
			document.getElementById("txtMpr").value = tema['Codmpr'];
			document.getElementById("txtMsl").value = tema['Codmsl'];
			// Checkbox
			document.getElementById("chkEst").checked = (tema['Codest']=='on') ;
			document.getElementById("chkLec").checked = (tema['Codlec']=='on') ;
			document.getElementById("chkMan").checked = (tema['Codman']=='on') ;
			document.getElementById("chkCat").checked = (tema['Codcat']=='on') ;
			document.getElementById("chkVtu").checked = (tema['Codvtu']=='on') ;
			document.getElementById("chkPri").checked = (tema['Codpri']=='on') ;
			document.getElementById("chkTsc").checked = (tema['Codtsc']=='on') ;
			document.getElementById("chkTtd").checked = (tema['Codttd']=='on') ;
			document.getElementById("chkTdo").checked = (tema['Codtdo']=='on') ;
			document.getElementById("chkTno").checked = (tema['Codtno']=='on') ;
			document.getElementById("chkTed").checked = (tema['Codted']=='on') ;
			document.getElementById("chkIpp").checked = (tema['Codipp']=='on') ;
			document.getElementById("chkUrg").checked = (tema['Codurg']=='on') ;
			document.getElementById("chkTci").checked = (tema['Codtci']=='on') ;
			
			// Turneros a redireccionar.
			var trd = tema['Codtrd'];
			// alert (trd);
			var arrTrd = trd.split(',');
			arrTrd.forEach( function(codtem, indice, array) {
				if (codtem!='')
				{
					//alert("En el índice " + indice + " hay este valor: chktur" + codtem);
					document.getElementById("chktur" + codtem).checked = true ;
				}
			});

			var arrChkTur = document.getElementsByClassName('chkTur');			
			for (i = 0; i < arrChkTur.length; i++) {
				var cod = arrChkTur[i].id.replace("lblchktur", ""); 
				if (cod==tema['Codtem'])
				{
					arrChkTur[i].checked = false ;
					arrChkTur[i].style.display = "none";
					//document.getElementById("chktur" + cod).disabled = true;
				}
				else
					arrChkTur[i].style.display = "block";
			//document.getElementById("lblchktur" + tema['Codtem']).style.display = "none";
			}
						
			// Llenar las categorias-subcategorías.
			onChecked ("chkCat");
			var lstCat = document.getElementById("lstCat");
			lstCat.innerHTML = "";
			var lstSct = document.getElementById("lstSubCat");
			lstSct.innerHTML = "";
			document.getElementById("arrCat").value = "";
			if (tema['Codcat']=='on') {
				var arrCat = tema['arrCat'];
				document.getElementById("arrCat").value = JSON.stringify(arrCat);
				//var myJsonString = JSON.stringify(arrCat);
				//alert (myJsonString);
				for (iterator = 0; iterator < arrCat.length; iterator++) {
					//alert (arrCat[iterator]);
					var oOption = document.createElement("OPTION");
					lstCat.options.add(oOption);
					oOption.text = arrCat[iterator]['sernom']+" ("+arrCat[iterator]['sercod']+")";
					oOption.value = arrCat[iterator]['sercod'];
					jsonSct = JSON.stringify(arrCat[iterator]['Subcat']);
					oOption.data = jsonSct;
					// Mostrar las subcategorias en el onclick
					oOption.onclick = (function(cod,nom,ord,pre) {
						return function() {
							
							onclickCat(cod,nom,ord,pre);

						};
					})(arrCat[iterator]['sercod'],arrCat[iterator]['sernom'],arrCat[iterator]['serord'],arrCat[iterator]['serpre']);
				}
			}

			
        }
		
		
		function onclickCat (cod,nom,ord,pre)
		{
					//alert(cod+" " + nom + " " + ord + " " + pre);
					var e = document.getElementById("lstCat");
					var codigoCat = document.getElementById("codigoCat");
					var nombreCat = document.getElementById("nombreCat");
					var ordenCat = document.getElementById("ordenCat");
					var prefijoCat = document.getElementById("prefijoCat");
							
					// codigoCat.value= e.value;
					codigoCat.value= cod;
					nombreCat.value= nom;
					ordenCat.value= ord;
					prefijoCat.value= pre;
							
					// inicializar campos de subcategoria
					document.getElementById("codigoSubct").value ="";
					document.getElementById("nombreSubct").value ="";

					//var codigoCatEdit = document.getElementById("codigoCatEdit"); // se utiliza como vlr tmp para actualizar el array de car-subcat
					//codigoCatEdit.value=codigoCat.value= e.value;
					var nom=e.options[e.selectedIndex].text;
					var idx = nom.lastIndexOf(" (");
					if (idx>=0)
							nom = nom.substring(0, idx).trim();
					nombreCat.value=nom;
							
					var lstSct = document.getElementById("lstSubCat");
					lstSct.innerHTML = "";

					var txt = document.getElementById("arrCat").value;
					var arrCat = JSON.parse(txt);
					// buscar la categoría editada.
					for (iterator = 0; iterator < arrCat.length; iterator++) {
						if (arrCat[iterator]['sercod'].toUpperCase()== cod.toUpperCase())
						{
							// leer las subcat
							sct = arrCat[iterator]['Subcat'];
							for (i = 0; i < sct.length; i++) {
								if (sct[i]['codSct'].trim() == "")
									continue;
								//alert (arrSct[i]);
								var oOpt = document.createElement("OPTION");
								lstSct.options.add(oOpt);
								oOpt.text = sct[i]['nomSct']; // + " ("+sct[i]['codSct']+")";
								oOpt.value = sct[i]['codSct'];
								oOpt.onclick = 
								(function(sct) {
									return function() {
										onclickSubCat(sct);							
									};
								})(sct[i]['codSct']);
							}	

						}
					}
		}

		function onclickCatANT (cod,nom,ord,pre,sct)
		{
							//json = JSON.stringify(sct);
							//alert(json);
							var e = document.getElementById("lstCat");
							var codigoCat = document.getElementById("codigoCat");
							var nombreCat = document.getElementById("nombreCat");
							var ordenCat = document.getElementById("ordenCat");
							var prefijoCat = document.getElementById("prefijoCat");
							
							// codigoCat.value= e.value;
							codigoCat.value= cod;
							nombreCat.value= nom;
							ordenCat.value= ord;
							prefijoCat.value= pre;
							
							// inicializar campos de subcategoria
							document.getElementById("codigoSubct").value ="";
							document.getElementById("nombreSubct").value ="";

							//var codigoCatEdit = document.getElementById("codigoCatEdit"); // se utiliza como vlr tmp para actualizar el array de car-subcat
							//codigoCatEdit.value=codigoCat.value= e.value;
							var nom=e.options[e.selectedIndex].text;
							var idx = nom.lastIndexOf(" (");
							if (idx>=0)
								nom = nom.substring(0, idx).trim();
							nombreCat.value=nom;
							
							var lstSct = document.getElementById("lstSubCat");
							lstSct.innerHTML = "";
						   						   
							for (i = 0; i < sct.length; i++) {
								//alert (arrSct[i]);
								var oOpt = document.createElement("OPTION");
								lstSct.options.add(oOpt);
								oOpt.text = sct[i]['nomSct']; // + " ("+sct[i]['codSct']+")";
								oOpt.value = sct[i]['codSct'];
								oOpt.onclick = 
								(function(sct) {
									return function() {
										onclickSubCat(sct);							
									};
								})(sct[i]['codSct']);
							}	
			
		}

		function onclickSubCat (sct)
		{
							//json = JSON.stringify(sct);
							//alert(json);
							var e = document.getElementById("lstSubCat");
							var codigoCat = document.getElementById("codigoSubct");
							var nombreCat = document.getElementById("nombreSubct");
							codigoCat.value= e.value;
							//var codigoCatEdit = document.getElementById("codigoCatEdit"); // se utiliza como vlr tmp para actualizar el array de car-subcat
							//codigoCatEdit.value=codigoCat.value= e.value;
							var nom=e.options[e.selectedIndex].text;
							/*
							var idx = nom.lastIndexOf("(");
							if (idx>=0)
								nom = nom.substring(0, idx).trim();
							*/
							nombreCat.value=nom;
			
		}

        function modificar(idRegistro,accion,Coddispo,ccoUnidad)
        {
            // definimos la anchura y altura de la ventana
            var altura=300;
            var anchura=800;
            // calculamos la posicion x e y para centrar la ventana
            var y=parseInt((window.screen.height/2)-(altura/2));
            var x=parseInt((window.screen.width/2)-(anchura/2));
            // mostramos la ventana centrada

            window.open("TrazProcess.php?accion="+accion.value+'&idRegistro='+idRegistro+'&Coddispo='+Coddispo+'&codCcoDispo='+ccoUnidad,
                target="blank","width="+anchura+",height="+altura+",top="+y+",left="+x+",toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,directories=no,resizable=no");
        }
		
    </script> <!-- VENTANAS -->
    <script type="text/javascript">
        $(document).ready(function(){
        });
    </script> 
    <style type="text/css">
		.chkTur {
		}

		input[type="checkbox"]:disabled {
		  background: #dddddd;
		}
		
		.lblMsgTxt {
			width:100%;
			color:DimGray;
			font-size:22px; 
			font-weight:normal; 
			text-align:center;
			background-color:white;
		}
		
		.inputTxt {
			border-radius:10px;
			border:1px solid #AFAFAF;
			width:100%;
			color:DimGray;
			font-size:22px; 
			font-weight:normal; 
			text-align:center;
			background-color:Lavender;
		}

		.btnAzul {
			-moz-border-radius: 12px;
			-webkit-border-radius: 12px;
			border-radius: 12px;
			color:white;
			font-size:18px; 
			font-weight:normal; 
			text-align:center;
			background-color:#428bca;
		}	

		.btnRojo {
			-moz-border-radius: 12px;
			-webkit-border-radius: 12px;
			border-radius: 12px;
			color:white;
			font-size:18px; 
			font-weight:normal; 
			text-align:center;
			background-color:red;
		}	

		.btnOk {
			-moz-border-radius: 12px;
			-webkit-border-radius: 12px;
			border-radius: 12px;
			color: rgba(0, 0, 0, 0);
			font-size:18px; 
			font-weight:normal; 
			text-align:center;
			background-color:green;
			background-image: url("ok.jpg");
			background-repeat: no-repeat;
			background-size: 30px 25px; /* contain */
		}	
		
		.container {
		  display: block;
		  position: relative;
		  padding-left: 35px;
		  margin-bottom: 12px;
		  cursor: pointer;
		  font-size: 20px;
		  font-weight: normal ; 
		  -webkit-user-select: none;
		  -moz-user-select: none;
		  -ms-user-select: none;
		  user-select: none;
		}
		
		.fondoGris {
			width:100%;
			color:DimGray !important;
			font-weight:bold; 
			text-align:center;
			background-color:Lavender !important;
		}
		
		/* Hide the browser's default checkbox */
		.container input {
		  position: absolute;
		  opacity: 0;
		  cursor: pointer;
		  height: 0;
		  width: 0;
		}

		/* Create a custom checkbox */
		.checkmark {
			 position: absolute;
			 top: 0;
			 left: 0;
			 height: 25px;
			 width: 25px;
			 background-color: #eee;
			 border-style: solid;
			 border-width: 1px;
			 border-color: #2196F3;
		}

		/* On mouse-over, add a grey background color */
		.container:hover input ~ .checkmark {
		  background-color: #ccc;
		}

		/* When the checkbox is checked, add a blue background */
		.container input:checked ~ .checkmark {
		  background-color: #2196F3;
		}

		/* Create the checkmark/indicator (hidden when not checked) */
		.checkmark:after {
		  content: "";
		  position: absolute;
		  display: none;
		}

		h1, .h1 {
			color: 				#000000;
			font-size: 			18px !important;
			font-family: 		verdana;
		}	
		
		/* Show the checkmark when checked */
		.container input:checked ~ .checkmark:after {
		  display: block;
		}

		/* Style the checkmark/indicator */
		.container .checkmark:after {
		  left: 9px;
		  top: 5px;
		  width: 5px;
		  height: 10px;
		  border: solid white;
		  border-width: 0 3px 3px 0;
		  -webkit-transform: rotate(45deg);
		  -ms-transform: rotate(45deg);
		  transform: rotate(45deg);
		}
		hxxx {
			color: 				#000000;
			font-size: 			14pt;
			font-family: 		verdana;
		}		
		.encabezadoTabla {
			background-color: 	#2a5db0;
			color: 				#ffffff;
			font-size: 			8pt;
			padding:			1px;
			font-family: 		verdana;
		}
		fieldset{
			border: 2px solid #e0e0e0;
		}
		legend{
			border: 2px solid #e0e0e0;
			border-top: 0px;
			font-family: Verdana;
			background-color: #e6e6e6;
			font-size: 11pt;
		}
		.titulopagina2
		{
			border-bottom-width: 1px;
			/*border-color: <?=$bordemenu?>;*/
			border-left-width: 1px;
			border-top-width: 1px;
			font-family: verdana;
			font-size: 18pt;
			font-weight: bold;
			height: 30px;
			margin: 2pt;
			overflow: hidden;
			text-transform: uppercase;
		}
		.wn
		{
			font-weight: normal;
		}
		#tooltip{font-family: verdana;font-weight:normal;color: #ffffff;font-size: 7pt;position:absolute;z-index:3000;border:1px solid #000000;background-color:#000000;padding:3px;opacity:1;border-radius: 4px;}
		#tooltip div{margin:0; width:auto;}
    </style>
    <?php

    include("conex.php");
    include("root/comun.php");

	$wactualiz='2021-12-13';

    if(!isset($_SESSION['user']))
    {
        ?>
        <div align="center">
            <label>Usuario no autenticado en el sistema.<br />Recargue la pagina principal de Matrix o inicie sesion nuevamente.</label>
        </div>
        <?php
        return;
    }

    $user_session = explode('-', $_SESSION['user']);
    $wuse = $user_session[1];
    mysql_select_db("matrix");
    $conex = obtenerConexionBD("matrix");

	$wemp = "01";
	if (isset($wemp_pmla))
		$wemp = $wemp_pmla;
	$wemp_pmla = $wemp;
	
	//echo "<br>wemp $wemp<br>";
	$wbasedatoCliame = consultarAliasPorAplicacion($conex, $wemp, 'cliame');
	//echo "<br>wbasedatoCliame $wbasedatoCliame";
	
    //*/
    //include('../MATRIX/include/root/conex.php'); //publicacion local
    //include('../MATRIX/include/root/comun.php'); //publicacion local
    //mysql_select_db('matrix'); //publicacion local
    //$conex = obtenerConexionBD('matrix'); //publicacion local
    //$wuse = '0100463';  //publicacion local
    $fuente = '20';
    $fecha_Actual = date('Y-m-d');  $hora_Actual = date('H:m:s');
    $ano_Actual = date('Y');        $mes_Actual = date('m');

	txtLog("", true);
	
	//txtLog("load FacSer_01-php: wuse $wuse, cCostos $cCostos", true);
	//$nuevaFactura = obtenerNumFactura($fuente,$cCostos ,$conex_o);  // para pruebas.
	
    $accion = $_POST['accion']; 
    $codTem = $_POST['codTem']; 
	
	$codNom = mysql_real_escape_string($_POST['codNom']); 
    $codSgt = mysql_real_escape_string($_POST['codSgt']); 
    $codCco = mysql_real_escape_string($_POST['codCco']); 
    $txtMbv = mysql_real_escape_string($_POST['txtMbv']); 
    $txtMlc = mysql_real_escape_string($_POST['txtMlc']); 
	$txtMim = mysql_real_escape_string($_POST['txtMim']); 
	$txtMtd = mysql_real_escape_string($_POST['txtMtd']); 
	$txtMdp = mysql_real_escape_string($_POST['txtMdp']); 
	$txtMtg = mysql_real_escape_string($_POST['txtMtg']); 
	$txtMct = mysql_real_escape_string($_POST['txtMct']); 
	$txtMst = mysql_real_escape_string($_POST['txtMst']); 
	$txtMsn = mysql_real_escape_string($_POST['txtMsn']); 
	$txtSno = mysql_real_escape_string($_POST['txtSno']); 
	$txtMse = mysql_real_escape_string($_POST['txtMse']); 
	$txtSte = mysql_real_escape_string($_POST['txtSte']); 
	$txtMsc = mysql_real_escape_string($_POST['txtMsc']); 
	$txtMnd = mysql_real_escape_string($_POST['txtMnd']); 
	$txtMno = mysql_real_escape_string($_POST['txtMno']); 
	$txtMed = mysql_real_escape_string($_POST['txtMed']); 
	$txtMsu = mysql_real_escape_string($_POST['txtMsu']); 
	$txtMpr = mysql_real_escape_string($_POST['txtMpr']); 
	$txtMsl = mysql_real_escape_string($_POST['txtMsl']); 
	$chkEst = (strtolower($_POST['chkEst'])=='on'?'on':'off');
	$chkLec = (strtolower($_POST['chkLec'])=='on'?'on':'off');
	$chkMan = (strtolower($_POST['chkMan'])=='on'?'on':'off');
	$chkCat = (strtolower($_POST['chkCat'])=='on'?'on':'off');
	$chkVtu = (strtolower($_POST['chkVtu'])=='on'?'on':'off');
	$chkPri = (strtolower($_POST['chkPri'])=='on'?'on':'off');
	$chkTsc = (strtolower($_POST['chkTsc'])=='on'?'on':'off');
	$chkTtd = (strtolower($_POST['chkTtd'])=='on'?'on':'off');
	$chkTdo = (strtolower($_POST['chkTdo'])=='on'?'on':'off');
	$chkTno = (strtolower($_POST['chkTno'])=='on'?'on':'off');
	$chkTed = (strtolower($_POST['chkTed'])=='on'?'on':'off');
	$chkIpp = (strtolower($_POST['chkIpp'])=='on'?'on':'off');
	$chkUrg = (strtolower($_POST['chkUrg'])=='on'?'on':'off');
	$chkTci = (strtolower($_POST['chkTci'])=='on'?'on':'off');
	
	// Turneros marcados a redireccionar.
	//echo "<br>Turneros marcados a redireccionar";
	$codTrd = "";
	$arr = getOptTemas('on');
	foreach ($arr as $reg) {
		$chk = 'chktur' . $reg['Codtem'];
		$chk = (strtolower($_POST[$chk])=='on'?'on':'off');
		if ($chk=='on') {
			$codTrd .= "," . $reg['Codtem'];
		}
	}
	if (strlen($codTrd)>0)
	{
		$codTrd = substr($codTrd,1);
	}

		
	txtLog("datos: accion $accion");
	//txtLog("datos: codTem $codTem");
	//txtLog("datos: txtMbv $txtMbv");
	//txtLog("datos: chkEst $chkEst");
	//txtLog("datos: chkLec $chkLec");
	if($accion == 'crear') {
		$sgte = getSgteTema();
		$codTem = str_pad($sgte, 2, '0', STR_PAD_LEFT);
		$sql = "insert into " . $wbasedatoCliame . "_000305 
					( Medico, Fecha_data, Hora_data, Seguridad, codtem )
			values	( 'cliame', curdate(), curtime(), 'C-cliame', '$codTem' )
		";
		txtLog($sql);
		mysql_query($sql, $conex) or die("<br><b>ERROR EN QUERY MATRIX($sql):</b>".mysql_error());		
	}
	
	if($accion == 'grabar' || $accion == 'crear') 
	{
		
		$sql = "update " . $wbasedatoCliame . "_000305 
			set codnom = '$codNom',
				codsgt = '$codSgt',
				codcco = '$codCco',
				codmbv = '$txtMbv',
				codmlc = '$txtMlc',
				codmim = '$txtMim', 
				codmtd = '$txtMtd',
				codmdp = '$txtMdp', 
				codmtg = '$txtMtg',
				codmct = '$txtMct', 
				codmst = '$txtMst', 
				codmsn = '$txtMsn',
				codsno = '$txtSno', 
				codmse = '$txtMse',
				codste = '$txtSte', 
				codmsc = '$txtMsc',
				codmnd = '$txtMnd',
				codmno = '$txtMno',
				codmed = '$txtMed',
				codmsu = '$txtMsu',
				codmpr = '$txtMpr',
				codmsl = '$txtMsl',
				codest = '$chkEst',
				codlec = '$chkLec',
				codman = '$chkMan',
				codcat = '$chkCat',
				codvtu = '$chkVtu',
				codpri = '$chkPri',
				codtsc = '$chkTsc',
				codttd = '$chkTtd',
				codtdo = '$chkTdo',
				codtno = '$chkTno',
				codted = '$chkTed',
				codipp = '$chkIpp',
				codurg = '$chkUrg',
				codtci = '$chkTci',
				codtrd = '$codTrd'
			where Codtem = '$codTem'
		";
		txtLog($sql);
		mysql_query($sql, $conex) or die("<br><b>ERROR EN QUERY MATRIX($sql):</b>".mysql_error());
		
		// Administrar categorías-subcategorías.
		// Cargar el arreglo de categorías.
		//$txTema = json_decode($_POST['txTem' . $codTem],true); 
		txtLog("arrCat: " . $_POST['arrCat']);
		$arrCat = json_decode($_POST['arrCat'],true); 		
		if (!is_array($arrCat))
			$arrCat = array();
		$CodCats = "''";
		foreach($arrCat as $cat) {
			$cod = $cat['sercod'];
			$nom = mysql_real_escape_string($cat['sernom']);
			$ord = $cat['serord'];
			$pre = $cat['serpre'];
			$CodCats .=  ",'$cod'"; // lista de categorías.
			// Insertar categoría si no existe
			$sql = "INSERT INTO " . $wbasedatoCliame . "_000298 
						(Medico,Fecha_data,Hora_data,Sertem,Sercod,Serest,Serord,Sercan,Serbus,Serpis,Seguridad)
					SELECT * FROM (
						SELECT 'cliame',date(NOW()),time(NOW()),'$codTem' AS sertem, '$cod' AS sercod, 'on' AS serest,
								99 AS serord, 'NO APLICA' AS sercan,'off' AS serbus,
								'NO APLICA' AS serpis, 'C-cliame' AS Seguridad
					) AS tmp
					WHERE NOT EXISTS (
						SELECT Sercod FROM " . $wbasedatoCliame . "_000298 as c WHERE c.sertem='$codTem' AND c.Sercod = '$cod'
					) LIMIT 1;
			";
			txtLog("Insertar cat: " . $sql);
			mysql_query($sql, $conex) or die("<br><b>ERROR EN QUERY MATRIX($sql):</b>".mysql_error());
			
			// Actualizar datos de la categoría
			$sql = "update " . $wbasedatoCliame . "_000298 
					set Sernom = '$nom', Serpre = '$pre', Serord = '$ord'
					WHERE Sertem='$codTem' AND Sercod = '$cod'
			";
			txtLog("Upd cat: " . $sql);
			mysql_query($sql, $conex) or die("<br><b>ERROR EN QUERY MATRIX($sql):</b>".mysql_error());
			
			//==============================================
			// Registrar las subcategorías.
			//==============================================
			$CodSubcats = "''";
			$arrSubCat = $cat['Subcat'];
			foreach($arrSubCat as $subcat) {				
				$nomSct = mysql_real_escape_string($subcat['nomSct']);
				$codSct = mysql_real_escape_string($subcat['codSct']);
				if (trim($codSct) == "" || trim($nomSct) == "")
					continue;
	
				if (substr($codSct,0,1) == "N")
				{
					// Si ES NUEVA, insertar.
					$sqlSct = "INSERT INTO " . $wbasedatoCliame . "_000309
								(Medico,Fecha_data,Hora_data,Seccod,Secnom,Secest,Seguridad)
						SELECT 'cliame',date(NOW()),time(NOW()),'$codSct' AS Seccod, '$nomSct' AS Secnom, 'on' AS Secest,'C-cliame' AS Seguridad
					";
					txtLog("insert subcat: " . $sqlSct);
					mysql_query($sqlSct, $conex) or die("<br><b>ERROR EN QUERY MATRIX($sql):</b>".mysql_error());
					// Ver que id le tocó en consecutivo.
					$sqlSct = "select id from " . $wbasedatoCliame . "_000309 where Seccod='$codSct'";
					txtLog("ver id subcat: " . $sqlSct);
					$rs = mysql_query($sqlSct, $conex) or die("<br><b>ERROR EN QUERY MATRIX($sql):</b>".mysql_error());
					$id = -1;
					if($rowDoc = mysql_fetch_array($rs))
						$id = $rowDoc['id'];
					if ($id >= 0)
					{
						$id = trim(strval($id));
						$CodSubcats .=  ",'$id'"; // lista de subcategorías.
						// Crear reg de enlace entre tema-categoría y subcategoria
						$sqlSct = "INSERT INTO " . $wbasedatoCliame . "_000310
									(Medico,Fecha_data,Hora_data,Rsstem, Rssser,Rsssec,Rssest,Seguridad)
							SELECT 'cliame',date(NOW()),time(NOW()),'$codTem' as Rsstem,'$cod' AS Rssser, '$id' AS Rsssec, 'on' AS Secest,'C-cliame' AS Seguridad
						";
						txtLog("insert enlace subcat: " . $sqlSct);
						mysql_query($sqlSct, $conex) or die("<br><b>ERROR EN QUERY MATRIX($sql):</b>".mysql_error());	
						// Actualizar el código de la subcat, con el id obtenido.
						$sqlSct = "update " . $wbasedatoCliame . "_000309 set Seccod='$id' where Seccod='$codSct'";
						txtLog("upd cod subcat: " . $sqlSct);
						mysql_query($sqlSct, $conex) or die("<br><b>ERROR EN QUERY MATRIX($sql):</b>".mysql_error());
					}
						
				}
				else
				{
					$CodSubcats .=  ",'$codSct'"; // lista de subcategorías.
					// Si existe, actualizar.
					// Seccod COD SUBCT, Rssser CATEGORIA
					$sqlSct = "
						UPDATE " . $wbasedatoCliame . "_000310 AS r 
						INNER JOIN " . $wbasedatoCliame . "_000309 AS s ON (r.Rsssec = s.Seccod) 
						SET s.Secnom = '$nomSct'
						WHERE r.Rsstem = '$codTem'
						AND s.Seccod = '$codSct'
						AND r.Rssser = '$cod'
					";
					txtLog("Upd subcat: " . $sqlSct);
					mysql_query($sqlSct, $conex) or die("<br><b>ERROR EN QUERY MATRIX($sql):</b>".mysql_error());		
				}
				
			}
			// Marcar para borrar los enlaces de subcat no existentes en el administrador
			$sql = "UPDATE " . $wbasedatoCliame . "_000310
				SET Medico = 'x'
				WHERE Rsstem='$codTem'
				AND Rssser = '$cod'
				AND Rsssec not in ($CodSubcats)
			";
			txtLog("Marcar para borrar subcat del enlace: " . $sql);
			mysql_query($sql, $conex) or die("<br><b>ERROR EN QUERY MATRIX($sql):</b>".mysql_error());
			// Marcar para borrar las subcat del maestro
			$sql = "UPDATE " . $wbasedatoCliame . "_000310 as r 
				inner join " . $wbasedatoCliame . "_000309 as s on (r.Rsssec = s.Seccod)
				SET s.Medico = 'x'
				WHERE r.Medico = 'x'
			";
			txtLog("Marcar para borrar subcat del enlace: " . $sql);
			mysql_query($sql, $conex) or die("<br><b>ERROR EN QUERY MATRIX($sql):</b>".mysql_error());

			// Borrar subcategorías eliminadas en el administrador.
			$sql = "DELETE FROM " . $wbasedatoCliame . "_000309	WHERE Medico = 'x'";
			txtLog("Borrar subcat del maestro: " . $sql);
			mysql_query($sql, $conex) or die("<br><b>ERROR EN QUERY MATRIX($sql):</b>".mysql_error());
			
			$sql = "DELETE FROM " . $wbasedatoCliame . "_000310	WHERE Medico = 'x'";
			txtLog("Borrar subcat del enlace: " . $sql);
			mysql_query($sql, $conex) or die("<br><b>ERROR EN QUERY MATRIX($sql):</b>".mysql_error());
						
		}		
		//txtLog("arrCat: " . json_encode($arrCat));
		
		// Borrar categorías eliminadas
		// OJO: HACER CASCADA DE SUBCATEGORIAS
		$sql = "DELETE FROM " . $wbasedatoCliame . "_000298 AS c 
			WHERE c.sertem='$codTem'
			AND c.Sercod not in ($CodCats)
		";
		txtLog("Borrar cat: " . $sql);
		mysql_query($sql, $conex) or die("<br><b>ERROR EN QUERY MATRIX($sql):</b>".mysql_error());

		
		
	}


    ?>
</head>

	
<!--=====================================================================================================================================================================     
	E S T I L O S 
=====================================================================================================================================================================-->
	<style type="text/css">

	</style>
<!--=====================================================================================================================================================================     
	F I N   E S T I L O S 
=====================================================================================================================================================================-->

<body>
<?php
	// -->	ENCABEZADO
	//txtLog("ENCABEZADO... $wactualiz clinica");
	encabezado("<div class='titulopagina2'>TIPOS DE TURNERO</div>", $wactualiz, 'clinica');
	//encabezado("TIPOS DE TURNERO", $wactualiz, 'clinica', $wemp);
?>
<div class="panel panel-info contenido" style="width: 98%; padding:20px;">

	<!--
    <div style="border-radius:10px;border:1px solid #B0D201">
        <div style="background-color:#B0D201;color:white;font-size:30px; font-weight:bold; text-align:center">
			TIPOS DE TURNERO
		</div>
    </div>
	-->
	
    <div align="center" class="panel panel-info divGeneral">
            <div class="divDatos" style="margin-top: 20px">
                    <form id="formHome" name="formHome" method="post" action="adm_temas.php" style="margin-top: 10px">
						<div id="divTemas">
							<!-- <label for="selTema"><h4>TURNERO</h4></label> -->
							<input type="hidden" id="accion" name="accion" value="grabar">
							<input type="hidden" id="codTem" name="codTem" value="">
							<input type="hidden" id="accionCat" name="accionCat">
							<input type="hidden" id="arrCat" name="arrCat">

							<label id="lblMsg" name="lblMsg" style="width:100%;color:DimGray;font-size:22px; font-weight:normal; text-align:center;
											background-color:white;">
											<?php 
												if ($accion=="crear") 
													echo"Tema creado correctamente"; 
												else if ($accion=="grabar") 
													echo"Tema actualizado correctamente";
												else
													echo"";
											?>
							</label>
							<table style="width: 100%;" cellspacing="20">
								<colgroup>
									<col span="1" style="width: 80%;">
									<col span="1" style="width: 20%;">
								</colgroup>
								<tr>
									<td>
										<select id="selTema" name="selTema" 
											style="border-radius:10px;border:1px solid #AFAFAF;width:90%;color:DimGray;
													font-size:25px; font-weight:bold; text-align:center;
													background-color:white; margin:0 10px 0 50px" 
													onchange="cargarTema()"
										>
											<option disabled selected value> -- Seleccione turnero -- </option>
											<?php
											// Llenar las opciones
											$arr = getOptTemas();
											foreach ($arr as $reg) {
													$cod = $reg['Codtem'];
													$nom = $reg['Codtem'] .'-'. $reg['Codnom'];
													$json = json_encode($reg);
													echo "<option value='$json' style='text-align:center;text-align-last: center;'>$nom</option>";
											}
											?>
										</select>
										<?php
											// Guardar los datos en formato json en un hidden
											foreach ($arr as $reg) {  //JSON_UNESCAPED_SLASHES
												$json = json_encode($reg);
												$name = "txTem" . $reg['Codtem'] ;
												echo "<input type='hidden' id='$name' name='$name' value='$json'>";								}
										?>
									</td>
									<td>
										<input type="button" id="btnAdd" style="border-radius:10px;border:1px solid #428bca;color:white;
													font-size:20px; font-weight:normal; text-align:center; width:80%;
													padding:0 5px 0 5px;  
													background-color:#428bca;"  value="Adicionar" onclick="activarNueva()">
									</td>
								</tr>
							</table>
						
							<!--
							<input type="button" id="btnDel" style="border-radius:10px;border:1px solid #FF8970;color:white;
										font-size:20px; font-weight:normal; text-align:center; 
										padding:0 5px 0 5px; margin:0 5px 0 5px;
										background-color:#FF8970;"  value="Borrar" onclick="">
							-->
							
						</div>
						<br>
						<table style="width:90%;" cellspacing="5">
							<colgroup>
								<col span="1" style="width: 12%;">
								<col span="1" style="width: 5%;">
								<col span="1" style="width: 55%;">
								<col span="1" style="width: 15%;">
								<col span="1" style="width: 12%;">
							</colgroup>
							<tr>
								<td>
								<label style="width:100%;color:DimGray;font-size:22px; font-weight:normal; text-align:center;
											background-color:white;">
										 Nombre
								</label>
								</td>
								<td>
								<label id="lblId" name="lblId" style="width:100%;color:DimGray;font-size:22px; font-weight:normal; text-align:right;
											background-color:white;">
										 00-
								</label>
								</td>
								<td>
								<input type="text" id="codNom" name="codNom" class="inputTxt" value="">
								</td>
								<td>
								<label style="width:100%;color:DimGray;font-size:22px; font-weight:normal; text-align:center;
											background-color:white;">
										 C.Costos
								</label>
								</td>
								<td>
								<input type="text" id="codCco" name="codCco" class="inputTxt" value="">
								</td>
							</tr>
						</table>
						<br>
						
						<label class="fondoGris" style="font-size:20px;">Configuraci&oacute;n general</label>
						<table>
							<colgroup>
								<col span="1" style="width: 25%;">
								<col span="1" style="width: 25%;">
								<col span="1" style="width: 25%;">
								<col span="1" style="width: 25%;">
							</colgroup>
							<tr>
								<td>
									<label class="container">Activo<br>&nbsp;
											<input id="chkEst" name="chkEst" type="checkbox">
											<span class="checkmark"></span>
									</label>
								</td>
								<td>
									<label class="container" >Ingreso<br>por pasos
										<input id="chkIpp" name="chkIpp" type="checkbox">
										<span class="checkmark"></span>
									</label>
								</td>
								<td>
									<label class="container">Ingreso<br>Manual
										<input id="chkMan" name="chkMan" type="checkbox">
										<span class="checkmark"></span>
									</label>
								</td>
								<td>
									<label class="container">Lector<br>c&eacute;dula
										<input id="chkLec" name="chkLec" type="checkbox" checked="checked">
										<span class="checkmark"></span>
									</label>
								</td>
							</tr>
							<tr>
								<td>
									<label class="container">Urgencias
											<input id="chkUrg" name="chkUrg" type="checkbox">
											<span class="checkmark"></span>
									</label>
								</td>
								<td>
									<label class="container">Con cita
											<input id="chkTci" name="chkTci" type="checkbox">
											<span class="checkmark"></span>
									</label>
								</td>
								<td>
								<label style="width:100%;font-size:20px; font-weight:normal; text-align:left;
											background-color:white;">
										 Soluci&oacute;n citas
								</label>
								</td>
								<td>
								<input type="text" id="txtMsl" name="txtMsl" class="inputTxt" value="">
								</td>

							</tr>
						</table>
						<br>
						<label class="fondoGris" style="font-size:20px;">Datos a ingresar</label>
						<table>
							<colgroup>
								<col span="1" style="width: 25%;">
								<col span="1" style="width: 25%;">
								<col span="1" style="width: 25%;">
								<col span="1" style="width: 25%;">
							</colgroup>
							<tr>
								<td>
									<label class="container">Tiene<br>tipo documento
										<input id="chkTtd" name="chkTtd" type="checkbox">
										<span class="checkmark"></span>
									</label>
								</td>
								<td>
									<label class="container">Tiene<br>documento
										<input id="chkTdo" name="chkTdo" type="checkbox" checked="checked" onclick="onChecked('chkTdo')">
										<span class="checkmark"></span>
									</label>
								</td>
								<td>
									<label class="container">Tiene<br>nombre
										<input id="chkTno" name="chkTno" type="checkbox" checked="checked">
										<span class="checkmark"></span>
									</label>
								</td>
								<td>
									<label class="container">Tiene<br>edad
										<input id="chkTed" name="chkTed" type="checkbox" checked="checked">
										<span class="checkmark"></span>
									</label>
								</td>
							</tr>
							<tr>
								<td>
									<label class="container">Validar<br>Existe Turno
										<input id="chkVtu" name="chkVtu" type="checkbox">
										<span class="checkmark"></span>
									</label>
								</td>
								<td>
									<label class="container">Tiene<br>Categor&iacute;as
										<input id="chkCat" name="chkCat" type="checkbox" onclick="onChecked('chkCat')">
										<span class="checkmark"></span>
									</label>
								</td>
								<td>
									<label class="container">Tiene<br>Subcategor&iacute;as
										<input id="chkTsc" name="chkTsc" type="checkbox">
										<span class="checkmark"></span>
									</label>
								</td>
								<td>
									<label class="container">Tiene<br>prioridad
										<input id="chkPri" name="chkPri" type="checkbox">
										<span class="checkmark"></span>
									</label>
								</td>
							</tr>
						</table>
						<br>

						<label class="fondoGris" style="font-size:20px;">Personalizaci&oacute;n de mensajes</label>
						<br><br>
						<table style="width:90%;" cellspacing="5">
							<colgroup>
								<col span="1" style="width: 100%;">
							</colgroup>
							<tr>
								<td>
								<label class="lblMsgTxt">Texto Bienvenida</label>
								<br>
								<textarea id="txtMbv" name="txtMbv" rows="2" class="inputTxt" value=""></textarea>
								<!--
								<input type="text" id="txtMbv" name="txtMbv" style="border-radius:10px;border:1px solid #AFAFAF;
											width:100%;color:DimGray;
											font-size:22px; font-weight:normal; text-align:center;
											background-color:Lavender;" 
											value="">
								-->
								</td>
							</tr>
							<tr>
								<td>
								<label class="lblMsgTxt">Texto Lector</label>
								<br>
								<textarea id="txtMlc" name="txtMlc" rows="2" class="inputTxt" value=""></textarea>
								</td>
							</tr>
							<tr>
								<td>
								<label class="lblMsgTxt">Texto Ingreso Manual</label>
								<br>
								<textarea id="txtMim" name="txtMim" rows="2" class="inputTxt" value=""></textarea>
								</td>
							</tr>
							<tr style="display:none;">
								<td>
								<label class="lblMsgTxt">Texto Tipo documento</label>
								<br>
								<textarea id="txtMtd" name="txtMtd" rows="2" class="inputTxt" value=""></textarea>
								</td>
							</tr>
							<tr>
								<td>
								<label class="lblMsgTxt">Texto Tipo y N&uacute;mero Documento
								</label>
								<br>
								<textarea id="txtMnd" name="txtMnd" rows="2" class="inputTxt" value=""></textarea>
								</td>
							</tr>
							<tr>
								<td>
								<label class="lblMsgTxt">Texto Nombre</label>
								<br>
								<textarea id="txtMno" name="txtMno" rows="2" class="inputTxt" value=""></textarea>
								</td>
							</tr>
							<tr>
								<td>
								<label class="lblMsgTxt">Texto Edad</label>
								<br>
								<textarea id="txtMed" name="txtMed" rows="2" class="inputTxt" value=""></textarea>
								</td>
							</tr>
							<tr>
								<td>
								<label class="lblMsgTxt">Texto Categor&iacute;as</label>
								<br>
								<textarea id="txtMct" name="txtMct" rows="2" class="inputTxt" value=""></textarea>
								</td>
							</tr>
							<tr>
								<td>
								<label class="lblMsgTxt">Texto Subcategor&iacute;as</label>
								<br>
								<textarea id="txtMsu" name="txtMsu" rows="2" class="inputTxt" value=""></textarea>
								</td>
							</tr>
							<tr>
								<td>
								<label class="lblMsgTxt">Texto Prioridad</label>
								<br>
								<textarea id="txtMpr" name="txtMpr" rows="2" class="inputTxt" value=""></textarea>
								</td>
							</tr>
							<tr>
								<td>
								<label class="lblMsgTxt">Texto Turno Generado</label>
								<br>
								<textarea id="txtMtg" name="txtMtg" rows="2" class="inputTxt" value=""></textarea>
								</td>
							</tr>
							<tr style="display:none;">
								<td>
								<label class="lblMsgTxt">Texto Sin Tipo Documento</label>
								<br>
								<textarea id="txtMst" name="txtMst" rows="2" class="inputTxt" value=""></textarea>
								</td>
							</tr>
							<tr>
								<td>
								<label class="lblMsgTxt">Texto Sin N&uacute;mero Documento
								</label>
								<br>
								<textarea id="txtMsn" name="txtMsn" rows="2" class="inputTxt" value=""></textarea>
								</td>
							</tr>
							<tr>
								<td>
								<label class="lblMsgTxt">Texto Sin Nombre</label>
								<br>
								<textarea id="txtSno" name="txtSno" rows="2" class="inputTxt" value=""></textarea>
								</td>
							</tr>
							<tr>
								<td>
								<label class="lblMsgTxt">Texto Sin Edad</label>
								<br>
								<textarea id="txtMse" name="txtMse" rows="2" class="inputTxt" value=""></textarea>
								</td>
							</tr>
							<tr>
								<td>
								<label class="lblMsgTxt">Texto Sin Categor&iacute;a</label>
								<br>
								<textarea id="txtMsc" name="txtMsc" rows="2" class="inputTxt" value=""></textarea>
								</td>
							</tr>

							
							<tr style="display:none;">
								<td>
								<label class="lblMsgTxt">Texto Datos personales</label>
								<br>
								<textarea id="txtMdp" name="txtMdp" rows="2" class="inputTxt" value=""></textarea>
								</td>
							</tr>
							<tr style="display:none;">
								<td>
								<label class="lblMsgTxt">Texto Sin Tipo Edad</label>
								<br>
								<textarea id="txtSte" name="txtSte" rows="2" class="inputTxt" value=""></textarea>
								</td>
							</tr>
							<tr><td><br></td></tr>
							<tr style="display:none;">
								<td>
								<label class="lblMsgTxt">script generaci&oacute;n turno</label>
								<br>
								<!--  <textarea name="Text1" cols="40" rows="5"></textarea> -->
								<textarea id="codSgt" name="codSgt" rows="2" class="inputTxt" value=""></textarea>
								</td>
							</tr>
							<tr><td><br></td></tr>

						</table>
						<br>
						<!-- CATEGORÍAS Y SUBCATEGORÍAS -->
						<div id="divCategorias" name="divCategorias">
							<label class="fondoGris" style="font-size:5px;">.</label>
							<table style="width:95%">
								<colgroup>
									<col span="1" style="width: 50%;">
									<col span="1" style="width: 2%;">
									<col span="1" style="width: 35%;">
								</colgroup>
								<tr>
								<td>
								<div class="fondoGris" >
									<label style="font-size:25px;width: 70%;">Categor&iacute;as</label>
									<!-- -->
									<label class="btnAzul" style="width: 5%;" onclick="AddCat()">+</label>
									<label class="btnRojo" style="width: 5%;" onclick="DelCat()">x</label>									
									<label class="btnOk" style="width: 4%;" onclick="RegCat()">.</label>									
								</div>
								</td>
								<td>
								</td>
								<td>
								<div class="fondoGris" >
									<label style="font-size:25px;width: 70%;">Subcategor&iacute;as</label>
									<!-- -->
									<label class="btnAzul" style="width: 8%;" onclick="AddCat('sct')">+</label>
									<label class="btnRojo" style="width: 8%;" onclick="DelCat('sct')">x</label>									
									<label class="btnOk" style="width: 7%;" onclick="RegCat('sct')">.</label>									
								</div>
								</td>
								</tr>
								<tr>
									<td>
										<select id="lstCat" name="lstCat" size="8" style="width:100%;color:DimGray;font-size:20px; font-weight:normal;
												background-color:white;">
										</select>
									</td>
									<td></td>
									<td>
										<select id="lstSubCat" name="lstSubCat" size="8" style="width:100%;color:DimGray;font-size:20px; font-weight:normal;
												background-color:white;">
										</select>
									</td>
								</tr>
								<tr>
									<td>
										<table>
											<tr>
												<td style="width:9%;">
													<input type="text" id="codigoCat" name="codigoCat" class="inputTxt" title="CÓDIGO" value="" disabled>
												</td>
												<td style="width:9%; padding-left:5px;">
													<input type="text" id="ordenCat" name="ordenCat" class="inputTxt" title="ORDEN" onkeyup="updateCat()" value="" >
												</td>
												<td style="width:9%; padding-left:5px;">
													<input type="text" id="prefijoCat" name="prefijoCat" class="inputTxt" title="PREFIJO" onkeyup="updateCat()" value="" >
												</td>
												<td style="width:70%; padding-left:10px;">
													<input type="text" id="nombreCat" name="nombreCat" class="inputTxt" title="DESCRIPCIÓN" 
														style="text-align:left;" onkeyup="updateCat()" value="">
												</td>
											</tr>
										</table>
									</td>
									<td></td>
									<td>
										<table style="width:100%;">
											<tr>											
												<!--
												<td style="width:0%;" >
													<input type="text" id="codigoSubct" name="codigoSubct" class="inputTxt" value="" disabled>
												</td>
												-->
												<td style="width:100%; padding-left:10px;">
													<input id="codigoSubct" name="codigoSubct" style="display:none;" value="" disabled>
													<input type="text" id="nombreSubct" name="nombreSubct" class="inputTxt" 
														style="text-align:left;width:100%;" onkeyup="updateCat('sct')" value=""></td>
											</tr>
										</table>
									</td>
								</tr>
									
							</table>
							<br>
						</div>
						<label class="fondoGris" style="font-size:25px;">Redirecci&oacute;n
						</label>
						<table style="width:35%">
							<colgroup>
								<col span="1" style="width: 100%;">
							</colgroup>
							<tr><td>
							</td></tr>
							
							<?php
								// Checkbox con los turneros disponibles
								$arr = getOptTemas('on');
								// Contar los que se podrán mostrar
								$cant = 0;
								foreach ($arr as $reg) {
									if (trim($reg['Codnom'])!='') $cant++;
								}

								//echo "cant arr $cant mitad " . ceil($cant/2);
								foreach ($arr as $reg) {
									$nom = trim($reg['Codnom']);
									//echo "$nom,";
									if ($nom!='') {
										$cod = $reg['Codtem'];
										$json = json_encode($reg);
										echo "<tr><td>
											<label id='lblchktur$cod' name='lblchktur$cod' class='container chkTur'>$nom
												<input id='chktur$cod' name='chktur$cod' type='checkbox' class='chk'>
												<span class='checkmark'></span>
											</label>
										</td></tr>";
									}
								}
							?>

								
						</table>
						<br>
						<input type="submit" style="border-radius:10px;border:1px solid #428bca;color:white;
										font-size:20px; font-weight:normal; text-align:center; 
										padding:8px; background-color:#428bca;width:20%;display:block;margin:auto;" value="GRABAR">
                    </form>
            </div>
			
    </div>
</div>

<?php
////////////FUNCIONES:

// Busca el siguiente código disponible de tema
function getSgteTema()
{
	global $conex, $wemp, $wbasedatoCliame;
	
	$sql = "select (Codtem * 1) as maxcod
		from " . $wbasedatoCliame . "_000305 
		order by (Codtem * 1) desc
	";
	txtLog ("$sql");
	$max = 0;
	$rs = mysql_query($sql, $conex) or die("<br><b>ERROR EN QUERY MATRIX($sql):</b>".mysql_error());
	if($rowDoc = mysql_fetch_array($rs)){
		txtLog (json_encode($rowDoc));
		$max = $rowDoc['maxcod'] + 1;
	}
	txtLog ("max $max");
	return $max;
}

// Carga las categorías-subcategorías de un tema y retorna arreglo con los campos
function getCategorias($tema,$estado='')
{
	global $conex, $wemp, $wbasedatoCliame; // Codsgt, Codtrd
	
	$sql = "select sercod, sernom, serord, serpre
		from " . $wbasedatoCliame . "_000298
		where sertem ='$tema'
		and serest = 'on'
	    order by sernom asc";

		//txtLog ("$sql");
		//echo "$sql";
	$arr = array();
	$rs = mysql_query($sql, $conex) or die("<br><b>ERROR EN QUERY MATRIX($sql):</b>".mysql_error());
	while($rowDoc = mysql_fetch_array($rs)){
		//txtLog (json_encode($rowDoc));
		//txtLog ("SGT: " . $rowDoc['Codsgt']);
		//$arr[] = $rowDoc;
		
		$categoria = utf8_encode($rowDoc['sercod']);
		
		// Cargar las subcategorias
		$sqlSct = "
		SELECT cat.Sercod AS codCat, cat.Sernom AS nomCat, s.Seccod AS codSct, s.Secnom AS nomSct
		FROM " . $wbasedatoCliame . "_000298 AS cat
		LEFT JOIN " . $wbasedatoCliame . "_000310 AS r ON(cat.Sertem = r.Rsstem AND cat.Sercod = r.Rssser AND r.Rssest = 'on')
		LEFT JOIN " . $wbasedatoCliame . "_000309 AS s ON(r.Rsssec = s.Seccod AND s.Secest = 'on') 
		WHERE cat.Sertem = '$tema'
		AND cat.Sercod = '$categoria'
		AND cat.Serest = 'on'
		order by cat.Sernom, s.Secnom
		";
		$arrSct = array();
		$rsSct = mysql_query($sqlSct, $conex) or die("<br><b>ERROR EN QUERY MATRIX($sql):</b>".mysql_error());
		while($rowSct = mysql_fetch_array($rsSct)){
			// Guardar las subcategorías en un arreglo
			$arrSct[] = array (
				"codSct" => utf8_encode($rowSct['codSct']),
				"nomSct" => utf8_encode(rplcSpecialChar($rowSct['nomSct'])),
			);
		}
		
		// Guardar la categoría en el arreglo
		$arr[] = array (
				"sercod" => $categoria,
				"sernom" => utf8_encode(rplcSpecialChar($rowDoc['sernom'])),
				"serord" => $rowDoc['serord'],
				"serpre" => utf8_encode(rplcSpecialChar($rowDoc['serpre'])),
				"Subcat" => $arrSct,
		);

		//echo "<br>tem " . utf8_encode($rowDoc['Codnom']) . " url: " . utf8_encode($rowDoc['Codsgt']);
	}
	// echo "<br>" . json_encode($arr, JSON_PRETTY_PRINT);
	//txtLog (json_encode($arr, JSON_PRETTY_PRINT));
	return $arr;
}


// Carga los temas activos y retorna arreglo con los campos
function getOptTemas($estado='')
{
	global $conex, $wemp, $wbasedatoCliame; // Codsgt, Codtrd
	
	/*
	$sql = "select Codtem,Codnom,Codest,Codmbv,Codlec,Codman,Codcat,Codvtu,Codpri,
			Codmlc,Codmim,Codmtd,Codmdp,Codmct,Codmtg,Codmst,Codmsn,Codsno,Codmse,
			Codste,Codmsc, Codsgt, Codtrd, Codtsc, Codttd, Codtdo, Codtno, Codted,Codipp
		from " . $wbasedatoCliame . "_000305 
	" . ($estado==''?'':" where Codest='$estado'") 
	. " order by Codtem asc";
	*/
	
	$sql = "select *
		from " . $wbasedatoCliame . "_000305 
	" . ($estado==''?'':" where Codest='$estado'") 
	. " order by Codtem asc";
	txtLog ("$sql");
	$arr = array();
	$rs = mysql_query($sql, $conex) or die("<br><b>ERROR EN QUERY MATRIX($sql):</b>".mysql_error());
	while($rowDoc = mysql_fetch_array($rs)){
		//txtLog (json_encode($rowDoc));
		txtLog (utf8_encode(rplcSpecialChar($rowDoc['Codnom'])));
		//txtLog ("SGT: " . $rowDoc['Codsgt']);
		//$arr[] = $rowDoc;
		$arrCat = getCategorias($rowDoc['Codtem']);
		$arr[] = array (
			"Codtem" => utf8_encode($rowDoc['Codtem']),
			"Codnom" => utf8_encode(rplcSpecialChar($rowDoc['Codnom'])),
			"Codest" => utf8_encode($rowDoc['Codest']),
			"Codmbv" => utf8_encode(rplcSpecialChar($rowDoc['Codmbv'])),
			"Codlec" => utf8_encode($rowDoc['Codlec']),
			"Codman" => utf8_encode($rowDoc['Codman']),
			"Codcat" => utf8_encode($rowDoc['Codcat']),
			"Codtsc" => utf8_encode($rowDoc['Codtsc']),
			"Codvtu" => utf8_encode($rowDoc['Codvtu']),
			"Codpri" => utf8_encode($rowDoc['Codpri']),
			"Codttd" => utf8_encode($rowDoc['Codttd']),
			"Codtdo" => utf8_encode($rowDoc['Codtdo']),
			"Codtno" => utf8_encode($rowDoc['Codtno']),
			"Codted" => utf8_encode($rowDoc['Codted']),
			"Codmlc" => utf8_encode(rplcSpecialChar($rowDoc['Codmlc'])),
			"Codmim" => utf8_encode(rplcSpecialChar($rowDoc['Codmim'])),
			"Codmtd" => utf8_encode(rplcSpecialChar($rowDoc['Codmtd'])),
			"Codmdp" => utf8_encode(rplcSpecialChar($rowDoc['Codmdp'])),
			"Codmtg" => utf8_encode(rplcSpecialChar($rowDoc['Codmtg'])),
			"Codste" => utf8_encode(rplcSpecialChar($rowDoc['Codste'])),
			"Codmse" => utf8_encode(rplcSpecialChar($rowDoc['Codmse'])),
			"Codsno" => utf8_encode(rplcSpecialChar($rowDoc['Codsno'])),
			"Codmst" => utf8_encode(rplcSpecialChar($rowDoc['Codmst'])),
			"Codmct" => utf8_encode(rplcSpecialChar($rowDoc['Codmct'])),
			"Codmsc" => utf8_encode(rplcSpecialChar($rowDoc['Codmsc'])),
			"Codmsn" => utf8_encode(rplcSpecialChar($rowDoc['Codmsn'])),
			"Codsgt" => utf8_encode(rplcSpecialChar($rowDoc['Codsgt'])),
			"Codmnd" => utf8_encode(rplcSpecialChar($rowDoc['Codmnd'])),
			"Codmno" => utf8_encode(rplcSpecialChar($rowDoc['Codmno'])),
			"Codmed" => utf8_encode(rplcSpecialChar($rowDoc['Codmed'])),
			"Codmsu" => utf8_encode(rplcSpecialChar($rowDoc['Codmsu'])),
			"Codmpr" => utf8_encode(rplcSpecialChar($rowDoc['Codmpr'])),
			"Codtrd" => utf8_encode($rowDoc['Codtrd']),
			"Codipp" => utf8_encode($rowDoc['Codipp']),
			"Codurg" => utf8_encode($rowDoc['Codurg']),
			"Codcco" => utf8_encode($rowDoc['Codcco']),
			"Codtci" => utf8_encode($rowDoc['Codtci']),
			"Codmsl" => utf8_encode($rowDoc['Codmsl']),
			"arrCat" => $arrCat,

			/*


			*/
		);
		//echo "<br>tem " . utf8_encode($rowDoc['Codnom']) . " url: " . utf8_encode($rowDoc['Codsgt']);
	}
	// echo "<br>" . json_encode($arr, JSON_PRETTY_PRINT);
	// txtLog (json_encode($arr, JSON_PRETTY_PRINT));
	return $arr;
}

// Obtiene de $cadena, la linea $num, de $longitud máxima
function lineaDeString ($cadena, $num, $longitud)
{
	$linea = 1;
	$str = "";
	$arr = explode(' ',$cadena);
	foreach($arr as $palabra) {
		if (strlen(trim($str . " " . $palabra)) > $longitud) {
			if ($linea == $num) {  // retornar la línea construída
				break;
			}
			$linea++;
			$str = $palabra;
		}
		else
			$str = $str . ' ' . $palabra;
		//echo("<br>$str");
	}
	if ($linea < $num)
		$str = "";
	return $str;
}

function obtenerDatosUsuario($parametro,$wuse,$conex)
{
    switch($parametro)
    {
        case 1:
            $query1 = "select * from usuarios WHERE Codigo = '$wuse'";
            $commit1 = mysql_query($query1, $conex) or die (mysql_errno()." - en el query: ".$query1." - ".mysql_error());
            $dato1 = mysql_fetch_array($commit1);   $cCostosUsuario = $dato1['Ccostos'];
            return $cCostosUsuario;
            break;
    }
}


function txtLog($txt, $inicializar=false)
{
        try {
                $l = date('H:i:s', time()) . ' ' . $txt . "\n";
				if ($inicializar)
					file_put_contents('log_la.txt', $l, LOCK_EX);
				else
					file_put_contents('log_la.txt', $l, FILE_APPEND | LOCK_EX);
        } catch (\Exception $e) {
        }
}

function rplcSpecialChar($txt)
{
    $t = str_replace("Ñ", "&Ntilde;", $txt);
    $t = str_replace("ñ", "&ntilde;", $t);
    $t = str_replace("Á", "&Aacute;", $t);
    $t = str_replace("á", "&aacute;", $t);
    $t = str_replace("É", "&Eacute;", $t);
    $t = str_replace("é", "&eacute;", $t);
    $t = str_replace("Í", "&Iacute;", $t);
    $t = str_replace("í", "&iacute;", $t);
    $t = str_replace("Ó", "&Oacute;", $t);
    $t = str_replace("ó", "&oacute;", $t);
    $t = str_replace("Ú", "&Uacute;", $t);
    $t = str_replace("ú", "&uacute;", $t);
    return $t;
}
?>
<script>
    const number = document.querySelector('.tvc2');
    function formatNumber (n) {
        n = String(n).replace(/\D/g, "");
        return n === '' ? n : Number(n).toLocaleString('en');
    }
    number.addEventListener('focus', (e) => {
        const element = e.target;
    const value = element.value;
    element.value = formatNumber(value);
    })

    const number2 = document.querySelector('.tvd2');
    function formatNumber2 (n) {
        n = String(n).replace(/\D/g, "");
        return n === '' ? n : Number(n).toLocaleString('en');
    }
    number2.addEventListener('focus', (e) => {
        const element = e.target;
    const value2 = element.value;
    element.value = formatNumber2(value2);
    })

    const number3 = document.querySelector('.tvn2');
    function formatNumber3 (n) {
        n = String(n).replace(/\D/g, "");
        return n === '' ? n : Number(n).toLocaleString('en');
    }
    number3.addEventListener('focus', (e) => {
        const element = e.target;
    const value3 = element.value;
    element.value = formatNumber3(value3);
    })
</script>
</body>
</html>