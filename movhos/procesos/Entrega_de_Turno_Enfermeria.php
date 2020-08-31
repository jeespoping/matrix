<html>
<head>
	<title>ENTREGA DE TURNO</title>
	<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
	<style type="text/css">

		 A {text-decoration: none;color: #000066;}
        .tipo3V{color:#000066;background:#dddddd;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo3V:hover {color: #000066; background: #999999;}
        .tipo4V{color:#000066;background:#dddddd;font-size:15pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo4V:hover {color: #000066; background: #999999;}
        .tipo3VTurno{color:#000066;background:#FFFFCC;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo3VTurno:hover {color: #000066; background: #999999;}
        .tipoTA{color:#000066;background:#FFFFCC;font-size:10pt;font-family:Arial;font-weight:bold;text-align:left;}
        .tipoMx{color:#000066;background:#FFFFCC;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}
        <!--.tipoTA:hover {color: #000066; background: #999999;} -->

    </style>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		document.onkeydown = mykeyhandler;

		function mykeyhandler(event)
		{
			//keyCode 116 = F5
			//keyCode 122 = F11
			//keyCode 8 = Backspace
			//keyCode 37 = LEFT ROW
			//keyCode 78 = N
			//keyCode 39 = RIGHT ROW
			//keyCode 67 = C
			//keyCode 86 = V
			//keyCode 85 = U
			//keyCode 45 = Insert

			event = event || window.event;
			var tgt = event.target || event.srcElement;
			if  ((event.altKey && event.keyCode==37) || (event.altKey && event.keyCode==39) ||
				(event.ctrlKey && event.keyCode==78)|| (event.ctrlKey && event.keyCode==67)||
				(event.ctrlKey && event.keyCode==86)|| (event.ctrlKey && event.keyCode==85)||
				(event.ctrlKey && event.keyCode==45)|| (event.shiftKey && event.keyCode==45)){
					event.cancelBubble = true;
					event.returnValue = false;
					alert("Función no permitida");
					return false;
			}

			if(event.keyCode==18 && tgt.type != "text" && tgt.type != "password" && tgt.type != "textarea")
			{
				return false;
			}

			if (event.keyCode == 8 && tgt.type != "text" && tgt.type != "password" && tgt.type != "textarea")
			{
				return false;
			}

			if ((event.keyCode == 116) || (event.keyCode == 122))
			{
				if (navigator.appName == "Microsoft Internet Explorer")
				{
				window.event.keyCode=0;
				}
				return false;
			}
		}

		function deshabilitar_teclas()
		//document.onkeydown = function()
		{
			if(window.event && window.event.keyCode == 116 )
			{
				window.event.keyCode = 505;
			}
			if(window.event && window.event.keyCode == 505)
			{
				return false;
			}
		}

		window.onload=function(){setInterval("parpadear()",500)};

		function enter()
		{
			document.forms.entrega.submit();
		}

		function cerrarVentana()
		{
			window.close()
		}

		function recarga()
		{
			var dvAux = document.createElement( "div" );

			dvAux.innerHTML = "<INPUT type='hidden' name='foto'>";
			dvAux.firstChild.value = document.getElementById( "entregaTurno" ).innerHTML;
			document.forms[0].appendChild( dvAux.firstChild );
			document.forms[0].submit();
		}

        $(document).ready(function(){
            if( $("#accesoHce").val() == "on" ){
                var historia = $("#whis").val();
                var ingreso  = $("#wing").val();
				var href = $(".tipo3v[historia='"+historia+"'][ingreso='"+ingreso+"']").attr("href");
				window.location.href = href;
            }
        });

	</script>
</head>

<body>

<?php
include_once("conex.php");
  /*********************************************************
   *               ENTREGA DE TURNO ENFERMERIA             *
   *    EN LA UNIDAD EN DONDE SE ENCUENTRE EL PACIENTE     *
   *                    CONEX, FREE => OK                  *
   *********************************************************/
//==================================================================================================================================
//PROGRAMA                   : Entrega_de_Turno_Enfermeria.php
//AUTOR                      : Juan Carlos Hernández M.
//$wautor="Juan C. Hernandez M. ";
//FECHA CREACION             : Agosto 30 de 2010
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz="2020-05-20";
//DESCRIPCION
//========================================================================================================================================\\
//========================================================================================================================================\\
//     Programa usado para la entrega de turno de enfermería.                                                                             \\
//     ** Funcionamiento General:                                                                                                         \\
//     Se selecciona el servicio en el que se esta, se despliega la lista de paciente en el servicio y se va ingresando a cada uno de     \\
//     ellos y dando click en checkbox 'Grabar' y luego enter, cuando se terminen de grabar todos los pacientes, al final de la lista de  \\
//     los pacientes se ingresa el código de la enfermera que recibe y si es valido se da click en checkbos 'Grabar' y queda entrega el   \\
//     turno de todos los pacientes.                                                                                                      \\
//     Si se ingresa con una fecha diferente a la actual o en un turno ya entrega no da la posibilidad de modificar o grabar nada         \\
//     Tabla(s) : movhos_000096                                                                                                           \\
//         * En esta tabla se graba c/u de los pacientes con la "foto" de lo que tenia el kardex en el momento de la entrega.             \\
//========================================================================================================================================\\
//========================================================================================================================================\\
// Mayo 20 de 2020: Jessica Madrid Mejía 
// Se valida si en la tabla movhos_000282 el centro de costos tiene restricción y de ser así si el usuario esta habilitado para acceder 
// a los pacientes de dicho centro de costos.
//========================================================================================================================================\\
//Septiembre 06 de 2016 : Jonatan Lopez
// Se agrega a la consulta de la descripcion del dextromenter la tabla movhos_000043 ya que para ordenes se utiliza esta tabla.
//========================================================================================================================================\\
//Agosto 12 2014 : Camilo Zapata
// se hacen las modificaciones para que se acceda directamente desde la historia clínica, se pregunta porl existencia del
//  parámetro "accesoHce", y si este existe el programa se mueve directo a los datos de la historia consultada
//========================================================================================================================================\\

//Febrero 4 2013 : Juan C. Hernández :
// Se modifica la consulta de medico tratante para que tenga en cuenta la especialidad del médico que se registro en el kardex de Enfermeria
//========================================================================================================================================\\
//Noviembre 06 de 2012 Jonatan Lopez  :   Se agrega la columna de observaciones en la seccion de medicamentos, ademas se corrige el ciclo para que muestre
//todos los liquidos endodenosos.
//========================================================================================================================================\\
//Julio 10 de 2012 Viviana Rodas.  Se agrega el llamado a las funciones consultaCentrosCostos que hace la consulta de los centros de costos\\
// 	de un grupo seleccionado y dibujarSelect que dibuja el select con los centros de costos obtenidos de la primera funcion.               \\

//========================================================================================================================================\\
//
//Mayo 10 de 2012   :   Se adiciona la columna de Dietas para verlas directamente en el listado general y evitar ingresar siempre         \\
//                      paciente por paciente para verificar la Dieta.                                                                    \\
//========================================================================================================================================\\
//Enero 24 de 2011                                                                                                                        \\
//========================================================================================================================================\\
//Se adiciona el campo de alertas que se registra en el Kardex                                                                            \\
//========================================================================================================================================\\
session_start();

if (!isset($user))
    if(!isset($_SESSION['user']))
      session_register("user");


if(!isset($_SESSION['user']))
    echo "error";
else
{

    

    include_once("root/comun.php");
    include_once("movhos/movhos.inc.php");
    


    $pos = strpos($user,"-");
    $wusuario = substr($user,$pos+1,strlen($user));


    echo "<br>";
    echo "<br>";

    //*******************************************************************************************************************************************
    //F U N C I O N E S
    //===========================================================================================================================================
    function mostrar_empresa($wemp_pmla)
    {
        global $user;
        global $conex;
        global $wcenmez;
        global $wafinidad;
        global $wbasedato;
        global $wtabcco;
        global $winstitucion;
        global $wactualiz;

        //Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos
        $q = " SELECT detapl, detval, empdes "
        ."   FROM root_000050, root_000051 "
        ."  WHERE empcod = '".$wemp_pmla."'"
        ."    AND empest = 'on' "
        ."    AND empcod = detemp ";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

        if ($num > 0 )
        {
            for ($i=1;$i<=$num;$i++)
            {
                $row = mysql_fetch_array($res);

                if ($row[0] == "cenmez")
                $wcenmez=$row[1];

                if ($row[0] == "afinidad")
                $wafinidad=$row[1];

                if ($row[0] == "movhos")
                $wbasedato=$row[1];

                if ($row[0] == "tabcco")
                $wtabcco=$row[1];
            }
        }
        else
        { echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA"; }

        $winstitucion=$row[2];

        encabezado("Entrega de Turno",$wactualiz, "clinica");
    }



    function traer_medico_tte($whis, $wing, $wfecha, &$i)
    {
        global $conex;
        global $wbasedato;

        $q = " SELECT Medno1, Medno2, Medap1, Medap2  "
        ."   FROM ".$wbasedato."_000047, ".$wbasedato."_000048 "
        ."  WHERE methis = '".$whis."'"
        ."    AND meting = '".$wing."'"
        ."    AND metest = 'on' "
        ."    AND metfek = '".$wfecha."'"
        ."    AND mettdo = medtdo "
        ."    AND metdoc = meddoc "
		."    AND metesp = substr(medesp,1,6) ";

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $wnum = mysql_num_rows($res);

        if ($wnum > 0)
        {
            $wmed="";
            for ($i=1; $i <= $wnum;$i++)
            {
                $row = mysql_fetch_array($res);

                if ($i < $wnum)
                { $wmed = $wmed.$row[0]." ".$row[1]." ".$row[2]." ".$row[3]."<br>"; }
                else
                { $wmed = $wmed.$row[0]." ".$row[1]." ".$row[2]." ".$row[3]; }
            }
            return $wmed;
        }
        else
        { return "Sin Médico"; }
    }

    function traer_LEV($whis, $wing, $wfecha, $wnum)
    {
        global $conex;
        global $wbasedato;

        global $wlev_des;
        global $wlev_obs;

        $q = " SELECT inkdes, inkobs "
            ."   FROM ".$wbasedato."_000051 A"
            ."  WHERE inkhis = '".$whis."'"
            ."    AND inking = '".$wing."'"
            ."    AND inkfec = '".$wfecha."'";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $wnum = mysql_num_rows($res);

        if ($wnum > 0)
        {
            for ($i=0; $i <= $wnum;$i++)
            {

            $row = mysql_fetch_array($res);

            if($row[0] == "")
                {
                $wlev_des[$i] = '';
                $wlev_obs[$i]=$row[1];
                }
            else
                {
                $wlev=str_replace(";","<br>",$row[0]);
                $wlev[$i]=str_replace("\n","<br>",$wlev);
                $wlev_des[$i]=$wlev;
                $wlev_obs[$i]=$row[1];
                }
            }
        }
        else
        {
            $wlev_des="";
            $wlev_obs="";
        }
    }

    function traer_medicamentos($whis, $wing, $wfecha, &$i)
    {
        global $conex;
        global $wbasedato;
        global $wcenmez;

        global $wartic;
        global $wdosis;
        global $wfrecu;
        global $wfecin;
        global $whorai;
        global $wcondi;
        global $wobserv;

        //Traigo los Kardex GENERADOS con articulos de DISPENSACION
        $q = " SELECT B.artcom, A.kadcfr, A.kadper, A.kadfin, A.kadhin, C.percan, C.peruni, A.kadufr, A.kadcnd, A.fecha_data, A.kadobs "
            ."   FROM ".$wbasedato."_000054 A, ".$wbasedato."_000026 B, ".$wbasedato."_000043 C,".$wbasedato."_000053 "
            ."  WHERE kadhis  = '".$whis."'"
            ."    AND kading  = '".$wing."'"
            ."    AND kadfec  = '".$wfecha."'"
            ."    AND kadest  = 'on' "
            ."    AND kadart  = artcod "
            ."    AND kadori  = 'SF' "
            ."    AND kadper  = percod "
            ."    AND kadhis  = karhis "
            ."    AND kading  = karing "
            ."    AND karcon  = 'on' "
            ."    AND karcco  = kadcco "
            ."    AND kadsus != 'on' "
            //."    AND kadare  = 'on' "
            ." UNION "
            //Traigo los Kardex GENERADOS con articulos de CENTRAL DE MEZCLAS
            ." SELECT B.artcom, A.kadcfr, A.kadper, A.kadfin, A.kadhin, C.percan, C.peruni, A.kadufr, A.kadcnd, A.fecha_data, A.kadobs "
            ."   FROM ".$wbasedato."_000054 A, ".$wcenmez."_000002 B, ".$wbasedato."_000043 C,".$wbasedato."_000053 D "
            ."  WHERE kadhis  = '".$whis."'"
            ."    AND kading  = '".$wing."'"
            ."    AND kadfec  = '".$wfecha."'"
            ."    AND kadest  = 'on' "
            ."    AND kadart  = artcod "
            ."    AND kadori  = 'CM' "
            ."    AND kadper  = percod "
            ."    AND kadhis  = karhis "
            ."    AND kading  = karing "
            ."    AND karcon  = 'on' "
            ."    AND karcco  = kadcco "
            ."    AND kadsus != 'on' "
            //."    AND kadare  = 'on' "
            ." UNION "
            //Traigo los Kardex en TEMPORAL (000060) con articulos de DISPENSACION
            ." SELECT B.artcom, A.kadcfr, A.kadper, A.kadfin, A.kadhin, C.percan, C.peruni, A.kadufr, A.kadcnd, A.fecha_data, A.kadobs "
            ."   FROM ".$wbasedato."_000060 A, ".$wbasedato."_000026 B, ".$wbasedato."_000043 C,".$wbasedato."_000053 D "
            ."  WHERE kadhis  = '".$whis."'"
            ."    AND kading  = '".$wing."'"
            ."    AND kadfec  = '".$wfecha."'"
            ."    AND kadest  = 'on' "
            ."    AND kadart  = artcod "
            ."    AND kadori  = 'SF' "
            ."    AND kadper  = percod "
            ."    AND kadhis  = karhis "
            ."    AND kading  = karing "
            ."    AND karcon  = 'on' "
            ."    AND karcco  = kadcco "
            ."    AND kadsus != 'on' "
            //."    AND kadare  = 'on' "
            ." UNION "
            //Traigo los Kardex en TEMPORAL (000060) con articulos de CENTRAL DE MEZCLAS
            ." SELECT B.artcom, A.kadcfr, A.kadper, A.kadfin, A.kadhin, C.percan, C.peruni, A.kadufr, A.kadcnd, A.fecha_data, A.kadobs "
            ."   FROM ".$wbasedato."_000060 A, ".$wcenmez."_000002 B, ".$wbasedato."_000043 C,".$wbasedato."_000053 D "
            ."  WHERE kadhis  = '".$whis."'"
            ."    AND kading  = '".$wing."'"
            ."    AND kadfec  = '".$wfecha."'"
            ."    AND kadest  = 'on' "
            ."    AND kadart  = artcod "
            ."    AND kadori  = 'CM' "
            ."    AND kadper  = percod "
            ."    AND kadhis  = karhis "
            ."    AND kading  = karing "
            ."    AND karcon  = 'on' "
            ."    AND karcco  = kadcco "
            ."    AND kadsus != 'on' ";
            //."    AND kadare  = 'on' "
            //."  ORDER BY 6 ";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $wnum = mysql_num_rows($res);

        if ($wnum > 0)
        {
            for ($i=1; $i <= $wnum;$i++)
            {
                $row = mysql_fetch_array($res);

                $wartic[$i] = $row[0];                                 //Medicamento
                $wdosis[$i] = $row[1]." ".$row[7];                     //Dosis y fracciones de la dosis
                if ($row[5] > 1)
                { $wfrecu[$i]="Cada ".$row[5]."&nbsp;".$row[6]."S"; }  //Descripcion de la FRECUENCIA
                else
                { $wfrecu[$i]="Cada ".$row[5]."&nbsp;".$row[6]; }      //Descripcion de la FRECUENCIA
                $wfecin[$i] = $row[3];                                 //Fecha de Inicio
                $whorai[$i] = $row[4];                                 //Hora de Inicio

                if (trim($row[8]) != "")            //Tiene Condicion
                {
                    $q = " SELECT condes "
                        ."   FROM ".$wbasedato."_000042 "
                        ."  WHERE concod = '".$row[8]."'";
                    $rescon = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

                    $row = mysql_fetch_array($rescon);

                    $wcondi[$i] = $row[0];          //Condicion
                }
                else
                { $wcondi[$i]=""; }
                $wobserv[$i]  = $row[10];           //Observaciones
            }
        }
        else
        { return "Sin Medicamentos"; }
    }

    function traer_examenes($whis, $wing, $wfecha, &$i)
    {
        global $conex;
        global $wbasedato;

        global $wser;
        global $wexa;
        global $wfes;

        $q = " SELECT cconom, ekaobs, ekafes "
            ."   FROM ".$wbasedato."_000050, ".$wbasedato."_000011 "
            ."  WHERE ekahis = '".$whis."'"
            ."    AND ekaing = '".$wing."'"
            ."    AND ekafec = '".$wfecha."'"
            ."    AND ekaest = 'P' "              //Solo traigo los pendientes
            ."    AND ekacod = ccocod "
            ."  ORDER BY 1, 2, 3 ";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $wnum = mysql_num_rows($res);

        if ($wnum > 0)
        {
            for ($i=1; $i <= $wnum;$i++)
            {
                $row = mysql_fetch_array($res);

                $wser[$i] = $row[0];
                $wexa[$i] = $row[1];
                $wfes[$i] = $row[2];
            }
        }
    }

    function traer_nombre_usuario($wusuario)
    {
        global $conex;

        $q = " SELECT descripcion "
        ."   FROM usuarios "
        ."  WHERE codigo = '".$wusuario."'"
        ."    AND activo = 'A' ";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $wnum = mysql_num_rows($res);

        if ($wnum > 0)
        {
            $row = mysql_fetch_array($res);
            return $row[0];
        }
        else
        { return ""; }
    }

    function traer_dietas($whis, $wing, $wfecha, &$i)
    {
        global $conex;
        global $wbasedato;

        global $wdie;

        $q = " SELECT diedes "
            ."   FROM ".$wbasedato."_000052, ".$wbasedato."_000041 "
            ."  WHERE dikhis = '".$whis."'"
            ."    AND diking = '".$wing."'"
            ."    AND dikfec = '".$wfecha."'"
            ."    AND dikest = 'on' "
            ."    AND dikcod = diecod ";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $wnum = mysql_num_rows($res);

        if ($wnum > 0)
        {
            for ($i=1; $i <= $wnum;$i++)
            {
                $row = mysql_fetch_array($res);
                $wdie[$i] = $row[0];
            }
        }
    }


    function traer_dextrometer($whis, $wing, $wfecha, &$i)
    {
        global $conex;
        global $wbasedato;

        global $wime;
        global $wima;
        global $wdos;
        global $wuni;
        global $wobs;
        global $wvia;
        global $wart;
        global $wfre;


        //Traigo los intervalos del dextrometer, con la vía, el articulo (insulina) y la condición (horario)
        //000071: Dextrometer por historia
        //000027: Maestro de Unidades
        //000040: Vias de admon
        //000070: Informacion unica por Kardex; similar al encabezado del Kardex
        //000042: Condiciones de suministro de medicamento "I": indica que son Insulinas
        //000026: Maestro de Articulos (medicamentos).

        //Busco en la tabla del encabezado de dextrometer si tiene
        $q = " SELECT infade, inffde, infcde "
            ."   FROM ".$wbasedato."_000070 "
            ."  WHERE infhis = '".$whis."'"
            ."    AND infing = '".$wing."'"
            ."    AND inffec = '".$wfecha."'";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $wnum = mysql_num_rows($res);

        if ($wnum > 0)
        {
            $row = mysql_fetch_array($res);

            //Traigo el nombre del articulo
            $q= " SELECT artcom FROM ".$wbasedato."_000026 WHERE artcod = '".$row[0]."' ";
            $resart = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $rowart = mysql_fetch_array($resart);

            $wart = $rowart[0];        //Nombre del Articulo (insulina)

            //Traigo la descripcion de la frecuencia
            $q= " SELECT * FROM (
					SELECT condes AS descrip_dex FROM ".$wbasedato."_000042 WHERE concod = '".$row[1]."' AND contip = 'I' 
				     UNION 
				    SELECT peruni AS descrip_dex FROM ".$wbasedato."_000043 WHERE percod = '".$row[1]."' AND pertip = 'I'
					) as t 
				  GROUP BY descrip_dex";
            $rescon = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $rowcon = mysql_fetch_array($rescon);
            $wfre = $rowcon[0];        //Descripcion Frecuencia

            //Query para traer el esquema, si es que lo tiene
            $q = " SELECT indime, indima, inddos, unides, indobs, viades "
                ."   FROM ".$wbasedato."_000071, ".$wbasedato."_000027, ".$wbasedato."_000040 "
                ."  WHERE indhis = '".$whis."'"
                ."    AND inding = '".$wing."'"
                ."    AND indfec = '".$wfecha."'"
                ."    AND indudo = unicod "
                ."    AND indvia = viacod ";
            $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $wnum = mysql_num_rows($res);

            if ($wnum > 0)
            for ($i=1; $i <= $wnum;$i++)
            {
                $row = mysql_fetch_array($res);
                $wime[$i] = $row[0];
                $wima[$i] = $row[1];
                $wdos[$i] = $row[2];
                $wuni[$i] = $row[3];
                $wobs[$i] = $row[4];
                $wvia[$i] = $row[5];
            }
        }
    }



    function elegir_historia($wturno)
    {
        global $user;
        global $conex;
        global $wcenmez;
        global $wafinidad;
        global $wbasedato;
        global $wtabcco;
        global $winstitucion;
        global $wactualiz;
        global $wemp_pmla;

        global $wcco;
        global $wnomcco;

        global $whab;
        global $whis;
        global $wing;
        global $wpac;
        global $wtid;                                      //Tipo documento paciente
        global $wdpa;
        global $weda;

        global $wfec;
        global $wfecha;

        global $wmed;
        global $wdiag;

        global $whora_par_actual;

        $wcco1=explode("-",$wcco);


        echo "<center><table>";
        echo "<tr>";
        echo "<td align=right  class=fila1>Fecha </td>";
        echo "<td align=center class=fila2>";

        if (!isset($wfec))
        { campofechaSubmit("wfec",$wfecha); }
        else
        { campofechaSubmit("wfec",$wfec); }

        echo "</td>";
        echo "</tr>";
        echo "</table>";

        //Seleccionar CENTRO DE COSTOS
		//**************** llamada a la funcion consultaCentrosCostos y dibujarSelect************
		$cco="Ccohos";
		$sub="on";
		$tod="";
		$ipod="off";
		//$cco=" ";
		$centrosCostos = consultaCentrosCostos($cco);

		echo "<table align='center' border=0>";
		$dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod);

		echo $dib;
		echo "</table>";

		$usuario = explode('-',$user);
	
		$usuarioHabilitado = true;
		if($wcco1[0]!="")
		{
			// Funciones definidas en include/movhos/movhos.inc.php
			$ccoConRestriccion = consultarCcoRestriccion($conex, $wbasedato, $wcco1[0]);
			if($ccoConRestriccion)
			{
				$usuarioHabilitado = consultarUsuarioPermitido($conex, $wbasedato, $wcco1[0], $usuario[1]);
			}
		}
		
		if($usuarioHabilitado)
		{
			//Selecciono todos los pacientes del servicio seleccionado
			$q = " SELECT habcod, habhis, habing, pacno1, pacno2, pacap1, pacap2, pacnac, pactid, pacced "
				."   FROM ".$wbasedato."_000020, ".$wbasedato."_000018, root_000036, root_000037 "
				."  WHERE habcco  = '".$wcco1[0]."'"
				."    AND habali != 'on' "            //Que no este para alistar
				."    AND habdis != 'on' "            //Que no este disponible, osea que este ocupada
				."    AND habcod  = ubihac "
				."    AND ubihis  = orihis "
				."    AND ubiing  = oriing "
				."    AND ubiald != 'on' "
				."    AND ubiptr != 'on' "
				."    AND ubisac  = '".$wcco1[0]."'"
				."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
				."    AND oriced  = pacced "
				."    AND oritid  = pactid "
				."    AND habhis  = ubihis "
				."    AND habing  = ubiing "
				."  GROUP BY 1,2,3,4,5,6,7 "
				."  ORDER BY Habord, Habcod ";
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($res);

			echo "<center><table>";
			echo "<tr class=encabezadoTabla>";
			echo "<th><font size=4>Habitacion</font></th>";
			echo "<th><font size=4>Historia</font></th>";
			echo "<th><font size=4>Paciente</font></th>";
			echo "<th><font size=4>Médico(s) Tratante(s)</font></th>";
			echo "<th><font size=4>Mensajes Sin leer<br>en el Kardex</font></th>";
			echo "<th width='20%'><font size=4>DIETA</font></th>";
			echo "</tr>";

			$whabant = "";
			if ($num > 0)
			{
				for($i=1;$i<=$num;$i++)
				{
					$row = mysql_fetch_array($res);

					$wclass_entregado="";
					if (is_integer($i/2))
					{ $wclass="fila1"; }
					else
					{ $wclass="fila2"; }

					$whab = $row[0];
					$whis = $row[1];
					$wing = $row[2];
					$wpac = $row[3]." ".$row[4]." ".$row[5]." ".$row[6];

					$wnac=$row[7];
					//Calculo la edad
					$wfnac=(integer)substr($wnac,0,4)*365 +(integer)substr($wnac,5,2)*30 + (integer)substr($wnac,8,2);
					$wfhoy=(integer)date("Y")*365 +(integer)date("m")*30 + (integer)date("d");
					$weda=(($wfhoy - $wfnac)/365);
					if ($weda < 1)
					{ $weda = number_format(($weda*12),0,'.',',')."<b> Meses</b>"; }
					else
					{ $weda=number_format($weda,0,'.',',')." Años"; }

					$wtid = $row[8];                                      //Tipo documento paciente
					$wdpa = $row[9];                                      //Documento del paciente

					//==========================================================================================================
					//Busco si ya se recibio el turno, cambio el fondo de la linea
					//==========================================================================================================
					$q = " SELECT COUNT(*) "
						."   FROM ".$wbasedato."_000096 "
						."  WHERE etufec = '".$wfec."'"
						."    AND etuhis = '".$whis."'"
						."    AND etuing = '".$wing."'"
						."    AND etutur = '".$wturno."'";
					$res_tur = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$row_tur = mysql_fetch_array($res_tur);
					if ($row_tur[0] > 0)
					{
						$wclass_entregado="fondoAmarillo";
						$wturgra=$wturno;
					}
					//==========================================================================================================

					echo "<tr class=".$wclass.">";
					echo "<td align=center><b>".$whab."</b></td>";
					echo "<td align=center>".$whis."</td>";
					echo "<td align=left  >".$wpac."</td>";

					$wdiag=traer_diagnostico($whis, $wing, $wfec);
					if ($wdiag=="Sin Diagnostico")    //Si no lo encontro en el Kardex actual, lo busco en el Kardex de día anterior
					{
						$dia = time()-(1*24*60*60);   //Resta un dia (2*24*60*60) Resta dos y //asi...
						$wayer = date('Y-m-d', $dia); //Formatea dia
						$wdiag=traer_diagnostico($whis, $wing, $wayer);
					}

					$wmed=traer_medico_tte($whis, $wing, $wfec, $j);
					if ($wmed=="Sin Médico")         //Si no lo encontro en el Kardex actual, lo busco en el Kardex de día anterior
					{
						$dia = time()-(1*24*60*60);   //Resta un dia (2*24*60*60) Resta dos y //asi...
						$wayer = date('Y-m-d', $dia); //Formatea dia
						$wmed=traer_medico_tte($whis, $wing, $wayer, $j);
					}
					echo "<td align=left  ><b>".$wmed."</b></td>";

					$wmsg=consultarMensajesSinLeer( $conex, $wbasedato, 'Kardex', $whis, $wing );
					if ( $wmsg > 0)
					{ echo "<td align=center><b><blink>".$wmsg."</blink></b></td>"; }
					else
					{ echo "<td align=left><b> </b></td>"; }


					// Inicio columna dietas -- Dietas, Se adiciona columa para ver la dieta en el listado de entrega de turnos. 2012-05-09
					$j=0;
					$num_kd = 0;
					$desc_diet = '';
					$wmensajek = '';
					$wfec_conk=$wfec;

					$kq = " SELECT kardie"
						 ."   FROM ".$wbasedato."_000053 A "
						 ."  WHERE karhis = '".$whis."'"
						 ."    AND karing = '".$wing."'"
						 ."    AND karest = 'on' "
						 ."    AND A.fecha_data = '".$wfec_conk."'";
					$resk = mysql_query($kq,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$kq." - ".mysql_error());
					$num_kd = mysql_num_rows($resk);

					// Se valída que si para el dia actual no hay Kardex se muestra el del día anterior y se informa que el Kardex no esta actualizado.
					if ($num_kd == 0)                               //Si no se encuentra Kardex Confirmado en la fecha actual, traigo kardex del dia anterior
					{
						$dia = time()-(1*24*60*60);                 //Resta un dia (2*24*60*60) Resta dos y //asi...
						$wayerk = date('Y-m-d', $dia);              //Formatea dia

						$wfec_conk=$wayerk;                         //Fecha a consultar para todas los datos del kardex
						$wmensajek="Kardex SIN Actulizar a la fecha";

						$kq = " SELECT kardie"
							 ."   FROM ".$wbasedato."_000053 A "
							 ."  WHERE karhis = '".$whis."'"
							 ."    AND karing = '".$wing."'"
							 ."    AND karest = 'on' "
							 ."    AND A.fecha_data = '".$wfec_conk."'";
						$resk = mysql_query($kq,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$kq." - ".mysql_error());
						$num_kd = mysql_num_rows($resk);
					}

					if ($num_kd > 0)
					{
						$row_kd = mysql_fetch_array($resk);
						$desc_diet =    ($row_kd['kardie'] != '')
										? "<font size='1'>(".$row_kd['kardie'].")<br><strong>$wmensajek</strong></font>"
										: "<font size='1'><strong>$wmensajek</strong></font>";
					}
					else
					{ $desc_diet = "<font size='1'><strong>$wmensajek</strong></font>"; }

					echo "<td align='left'>";
					traer_dietas($whis, $wing, $wfec_conk, $j);
					global $wdie;
					if ($j > 0)
					{
						for ($k=1; $k < $j; $k++)
						{
							echo "* ".$wdie[$k]."<br>";
						}
					}
					else
					{ echo "&nbsp;"; }
					echo $desc_diet." ";
					echo "</td>";
					// Fin columna dietas --
					$wdiag = str_replace( '\'', '', $wdiag );
					$wdiag = str_replace( '\"', '', $wdiag );
					echo "<td align=center><a HREF='Entrega_de_Turno_Enfermeria.php?wemp_pmla=".$wemp_pmla."&user=".$user."&whis=".$whis."&wing=".$wing."&wcco=".$wcco."&wfec=".$wfec."&whab=".$whab."&wpac=".$wpac."&wtid=".$wtid."&wdpa=".$wdpa."&weda=".$weda."&wdiag=".$wdiag."&wmed=".$wmed."' class='tipo3V' historia='".$whis."' ingreso='".$wing."'>Ver</a></td>";
					//================================================================================================================================================
					/* OJO OJO OJO OJO OJO OJO OJO OJO **************************************************************
					/*   Esto se quita temporalmente, mientras las enfermeras se acostumbran a la entrega del turno por este programa
					if ($wclass_entregado=="fondoAmarillo")  //Si entra aca es porque ya esta recibido el turno
					{
					if ($wturgra=="MAÑANA")
					  {
					   echo "<td align=center><A HREF='Entrega_de_Turno_Enfermeria.php?wemp_pmla=".$wemp_pmla."&user=".$user."&whis=".$whis."&wing=".$wing."&wcco=".$wcco."&wfec=".$wfec."&whab=".$whab."&wpac=".$wpac."&wtid=".$wtid."&wdpa=".$wdpa."&weda=".$weda."&wdiag=".$wdiag."&wmed=".$wmed."&wturno=MAÑANA' class=tipo3VTurno>Mañana</A></td>";
					   echo "<td align=center><A HREF='Entrega_de_Turno_Enfermeria.php?wemp_pmla=".$wemp_pmla."&user=".$user."&whis=".$whis."&wing=".$wing."&wcco=".$wcco."&wfec=".$wfec."&whab=".$whab."&wpac=".$wpac."&wtid=".$wtid."&wdpa=".$wdpa."&weda=".$weda."&wdiag=".$wdiag."&wmed=".$wmed."&wturno=NOCHE' class=tipo3V>Noche</A></td>";
					  }
					 else
						{
						 echo "<td align=center><A HREF='Entrega_de_Turno_Enfermeria.php?wemp_pmla=".$wemp_pmla."&user=".$user."&whis=".$whis."&wing=".$wing."&wcco=".$wcco."&wfec=".$wfec."&whab=".$whab."&wpac=".$wpac."&wtid=".$wtid."&wdpa=".$wdpa."&weda=".$weda."&wdiag=".$wdiag."&wmed=".$wmed."&wturno=MAÑANA' class=tipo3V>Mañana</A></td>";
						 echo "<td align=center><A HREF='Entrega_de_Turno_Enfermeria.php?wemp_pmla=".$wemp_pmla."&user=".$user."&whis=".$whis."&wing=".$wing."&wcco=".$wcco."&wfec=".$wfec."&whab=".$whab."&wpac=".$wpac."&wtid=".$wtid."&wdpa=".$wdpa."&weda=".$weda."&wdiag=".$wdiag."&wmed=".$wmed."&wturno=NOCHE' class=tipo3VTurno>Noche</A></td>";
						}
					}
					else
					{
					 echo "<td align=center><A HREF='Entrega_de_Turno_Enfermeria.php?wemp_pmla=".$wemp_pmla."&user=".$user."&whis=".$whis."&wing=".$wing."&wcco=".$wcco."&wfec=".$wfec."&whab=".$whab."&wpac=".$wpac."&wtid=".$wtid."&wdpa=".$wdpa."&weda=".$weda."&wdiag=".$wdiag."&wmed=".$wmed."&wturno=MAÑANA' class=tipo3V>Mañana</A></td>";
					 echo "<td align=center><A HREF='Entrega_de_Turno_Enfermeria.php?wemp_pmla=".$wemp_pmla."&user=".$user."&whis=".$whis."&wing=".$wing."&wcco=".$wcco."&wfec=".$wfec."&whab=".$whab."&wpac=".$wpac."&wtid=".$wtid."&wdpa=".$wdpa."&weda=".$weda."&wdiag=".$wdiag."&wmed=".$wmed."&wturno=NOCHE' class=tipo3V>Noche</A></td>";
					}
					*/

					//======================================================================================================
					//En este procedimiento pregunto si el paciente es cliente AFIN o no, y de que tipo
					/* $wafin=clienteMagenta($wdpa,$wtid,&$wtpa,&$wcolorpac);
					if ($wafin)
					{
					echo "<td align=center><font color=".$wcolorpac."><b>".$wtpa."<b></font></td>";
					}
					else
					echo "<td>&nbsp</td>";
					*/
					//======================================================================================================

					echo "</tr>";
				}
			}
			else
			echo "NO HAY HABITACIONES OCUPADAS";
			echo "</table>";
		}
		else
		{
			echo "	<br/>
					<div style='align-content:center;justify-content:center;display:flex;align-items:center; font-size:15pt; color: #000066;'>
						El usuario no esta habilitado para acceder a pacientes en este centro de costos.
					</div>";
		}
	}

    function mostrar_foto($whis, $wing, $wturno, $wfecha)
    {
        global $conex;
        global $wbasedato;
        global $wcenmez;
        global $wemp_pmla;

        $q = " SELECT etufot, etuuse, etuusr "
            ."   FROM ".$wbasedato."_000096 A, usuarios"
            ."  WHERE etuhis = '".$whis."'"
            ."    AND etuing = '".$wing."'"
            ."    AND etutur = '".$wturno."'"
            ."    AND etufec = '".$wfecha."'";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

        if ($num > 0)
        {
            $row = mysql_fetch_array($res);
            echo $row[0];

            $wnom_ent=traer_nombre_usuario($row[1]);
            $wnom_rec=traer_nombre_usuario($row[2]);

            echo "<center><table>";
            echo "<tr class=encabezadoTabla>";
            echo "<th>Entrega el Turno</th>";
            echo "<th colspan=2>Recibe el Turno</th>";
            echo "</tr>";
            echo "<tr class=fila1>";
            echo "<td>".$row[1]." <b>".$wnom_ent."</b></td>";
            echo "<td>".$row[2]." <b>".$wnom_rec."</b></td>";
            echo "</tr>";
            echo "</table>";
        }
    }

    function query_kardex($whis, $wing, $wfec, &$res)
    {
        global $conex;
        global $wbasedato;
        global $wcenmez;
        global $wemp_pmla;

        $q = " SELECT karobs, kardia, kartal, karpes, karale, karcui, karter, karson, karcur, "
             ."        karint, kardie, karmez, kardem, karcip, kartef, karrec, karanp, karais "
             ."   FROM ".$wbasedato."_000053 A "
             ."  WHERE karhis = '".$whis."'"
             ."    AND karing = '".$wing."'"
             ."    AND karest = 'on' "
             ."    AND A.fecha_data = '".$wfec."'";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    }

    function query_existe_entrega(&$res)
    {
        global $conex;
        global $wbasedato;
        global $wcenmez;
        global $wemp_pmla;
        global $wturno;
        global $whis;
        global $wing;
        global $wturno;
        global $wfec;


        $q = " SELECT etucco, etuhab, etuuse, etuusr, etuobs, etuobc, etufot "
            ."   FROM ".$wbasedato."_000096 A "
            ."  WHERE etuhis = '".$whis."'"
            ."    AND etuing = '".$wing."'"
            ."    AND etufec = '".$wfec."'"
            ."    AND etutur = '".$wturno."'";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

        if ($num > 0)
        { return true; }
        else
        { return false; }
    }
    //===========================================================================================================================================
    //*******************************************************************************************************************************************



    //===========================================================================================================================================
    //===========================================================================================================================================
    // P R I N C I P A L
    //===========================================================================================================================================
    //===========================================================================================================================================
    echo "<form name='entrega' action='Entrega_de_Turno_Enfermeria.php' method=post>";
    echo "<input type='hidden' id='accesoHce' name='accesoHce' value='".$accesoHce."'>";
    if( $accesoHce == "on" ){
        mostrar_empresa($wemp_pmla);
        $query = " SELECT concat( Ccocod, '-', Cconom )
                     FROM {$wbasedato}_000018 a, {$wbasedato}_000011
                    WHERE Ccocod = Ubisac
                      AND Ubihis = '{$whis}'
                      AND Ubiing = '{$wing}'";
        $rs    = mysql_query( $query, $conex );
        $row   = mysql_fetch_array( $rs );
        $wcco = $row[0];

        $wfec = date('Y-m-d');
        echo "<input type='hidden' id='wcco' name='wcco' value='".$wcco."'>";
        echo "<input type='hidden' id='wfec' name='wfec' value='".$wfec."'>";
		echo "<input type='HIDDEN' name='whis' id='whis' value='".$whis."'>";
        echo "<input type='HIDDEN' name='wing' id='wing' value='".$wing."'>";
	}

    ?>
        <script>
        deshabilitar_teclas();
        </script>
    <?php


    if (!isset($wfecha)) { $wfecha = date("Y-m-d"); }
    $whora  = (string)date("H:i:s");


    echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";

    mostrar_empresa($wemp_pmla);

    if ( date( "H" ) > "7" and date( "H" ) < "19" )
    { $wtur_grabar="MAÑANA"; }
    else
    { $wtur_grabar="NOCHE"; }

    //=====================================================================================================
    //UPDATE para actualizar la entrega de turno de todo el servicio
    if (isset($wgrabar_todos) and $wgrabar_todos=="on")
    {
        $wcco1=explode("-",$wcco);
        $q = "UPDATE ".$wbasedato."_000096 "
              ."   SET etuusr = '".$wusurec."'"
              ." WHERE etufec = '".$wfecha."'"
              ."   AND etucco = '".$wcco1[0]."'"
              ."   AND etuuse = '".$wusuario."'"
              ."   AND etutur = '".$wtur_grabar."'"
              ."   AND etuusr = '' ";
        $res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error());
    }
    //=====================================================================================================

    if ((isset($wgrabar) and $wgrabar == "on"))
    {
        $wcco1=explode("-",$wcco);

        $q = "INSERT INTO ".$wbasedato."_000096 (     medico      ,    fecha_data ,    hora_data,    etufec    ,    etuhor   ,    etucco      ,    etuhab  ,    etuhis  ,    etuing  ,    etuuse      , etuusr,    etuobs  , etuobc ,    Etufot  ,    etutur    , seguridad          ) "
          ."           VALUES ( '".$wbasedato."', '".$wfecha."' , '".$whora."', '".$wfecha."', '".$whora."', '".$wcco1[0]."', '".$whab."', '".$whis."', '".$wing."', '".$wusuario."', ''    , '".$wobs."',''      , '".$foto."', '".$wturno."', 'C-".$wbasedato."' ) ";
        $res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error());

        mostrar_foto($whis, $wing, $wturno, $wfec);

        echo "<br><br>";
        echo "<center><table>";
        echo "<tr><td><A HREF='Entrega_de_Turno_Enfermeria.php?wemp_pmla=".$wemp_pmla."&wcco=".$wcco."&wfec=".$wfec."' class=tipo4V>Retornar</A></td></tr>";
        echo "</table>";
    }
    else
    {
        if (isset($whis) and isset($wcco) and !isset($accesoHce) )
        {
            echo "<input type='HIDDEN' name='wcco' VALUE='".$wcco."'>";
            echo "<input type='HIDDEN' name='wfec' VALUE='".$wfec."'>";
            if (isset($wdiag)) echo "<input type='HIDDEN' name='wdiag' VALUE='".$wdiag."'>";
            if (isset($wmed))  echo "<input type='HIDDEN' name='wmed' VALUE='".$wmed."'>";

            if (isset($wturno)) { echo "<input type='HIDDEN' name='wturno' value='".$wturno."'>"; }
            echo "<input type='HIDDEN' name='whis' id='whis' value='".$whis."'>";
            echo "<input type='HIDDEN' name='wing' id='wing' value='".$wing."'>";
            echo "<input type='HIDDEN' name='whab' id='whab' value='".$whab."'>";
            echo "<input type='HIDDEN' name='wpac' id='wpac' value='".$wpac."'>";
            echo "<input type='HIDDEN' name='wtid' id='wtid' value='".$wtid."'>";
            echo "<input type='HIDDEN' name='wdpa' id='wdpa' value='".$wdpa."'>";
            echo "<input type='HIDDEN' name='weda' id='weda' value='".$weda."'>";

            if ($wfec == $wfecha)
            {
                $wok=query_existe_entrega($wres);

                if ($wok==false)  //Si no hay turno entregado
                {
                    $wfec_con=$wfec;                              //Fecha a consultar para todas los datos del kardex
                    $wmensaje="Kardex Actulizado a la fecha";

                    query_kardex($whis, $wing, $wfec_con, $res);
                    $num = mysql_num_rows($res);

                    if ($num == 0)                                //Si no se encuentra Kardex Confirmado en la fecha actual, traigo kardex del dia anterior
                    {
                        $dia = time()-(1*24*60*60);               //Resta un dia (2*24*60*60) Resta dos y //asi...
                        $wayer = date('Y-m-d', $dia);             //Formatea dia

                        $wfec_con=$wayer;                         //Fecha a consultar para todas los datos del kardex
                        $wmensaje="Kardex SIN Actulizar a la fecha";

                        query_kardex($whis, $wing, $wfec_con, $res);
                        $num = mysql_num_rows($res);
                    }

                    if ($num > 0)
                    {
                        $row = mysql_fetch_array($res);

                        echo "<div id='entregaTurno'>";

                        echo "<center><table>";
                        echo "<tr class=encabezadoTabla>";
                        echo "<th><font size=6 color='FFFF33'><b>".$wfec."&nbsp;&nbsp;&nbsp;".$wturno."</b></font></th>";
                        echo "</tr>";
                        echo "<tr class=encabezadoTabla>";
                        echo "<th><font size=3 color='FFFF33'><b>".$wmensaje." ".$wfec_con."</b></font></th>";
                        echo "</tr>";

                        echo "</table>";

                        echo "<center><table>";
                        echo "<tr class=fila1>";
                        echo "<th><font size=3>Habitación "."</font></th>";
                        echo "<th><font size=3>Documento</font></th>";
                        echo "<th><font size=3>Historía</font></th>";
                        echo "<th><font size=3>Nombre</font></th>";
                        echo "<th><font size=3>Edad</font></th>";
                        echo "<th><font size=3>Talla</font></th>";
                        echo "<th><font size=3>Peso</font></th>";
                        echo "</tr>";
                        echo "<tr class=fila2>";
                        echo "<td bgcolor=333399 align=center><b><font size=5 color='00FF00'>".$whab."</font></b></td>";
                        echo "<td align=center>".$wdpa."</td>";
                        echo "<td align=center>".$whis."</td>";
                        echo "<td align=center><font size=4><b>".$wpac."&nbsp&nbsp</b></font></td>";
                        echo "<td align=center><font size=4><b>".$weda."</b></td>";
                        echo "<td align=center>".$row["kartal"]."</td>";
                        echo "<td align=center>".$row["karpes"]." Kg</td>";
                        echo "</tr>";
                        echo "</table>";

                        echo "<br>";
                        echo "<center><table>";

                        //Diagnostico y Medico tratante
                        echo "<tr class=encabezadoTabla>";
                        echo "<td align=center colspan=3>Diagnostico(s)</td>";
                        echo "<td align=center colspan=4>Médico(s) Tratantes</td>";
                        echo "</tr>";
                        echo "<tr class=fila2>";
                        echo "<td align=center colspan=3><textarea rows=3 cols=60 readonly class=tipoTA>".$wdiag."</textarea></td>";
                        //echo "<td align=center colspan=3><textarea rows=3 cols=60 readonly class=tipoTA>".str_replace('<br>','\r\n',$wmed)."</textarea></td>";
                        echo "<td align=center colspan=4 class=tipoMx>".$wmed."</td>";
                        echo "</tr>";

                        //Antecedentes Personales
                        if (trim($row["karanp"]) != "" or trim($row["karale"]) != "")
                        {
                            echo "<tr class=encabezadoTabla>";
                            echo "<td colspan=3 align=center><b>ANTECEDENTES PERSONALES</b></td>";
                            echo "<td colspan=4 align=center><b>ANTECEDENTES ALERGICOS</b></td>";
                            echo "</tr><tr class=fila2>";
                            //$wanp=str_replace("\n","<br>",htmlentities($row["karanp"],ENT_QUOTES));
                            echo "<td align=center colspan=3><textarea rows=3 cols=60 readonly class=tipoTA>".$row["karanp"]."</textarea></td>";
                            echo "<td align=center colspan=4><textarea rows=3 cols=60 readonly class=tipoTA>".$row["karale"]."</textarea></td>";
                            echo "</tr><tr class=fila2>";
                            //$wanp=str_replace("\n","<br>",htmlentities($row["karanp"],ENT_QUOTES));

                            echo "</tr>";
                        }

                        $j=0;
                        traer_examenes($whis, $wing, $wfec_con, $j);   //Esto lo hago aca arriba porque necesito saber si tiene examenes para sacar o no el titulo de CONTROLES
                        traer_LEV($whis, $wing, $wfec_con, $wnum);

                        if ($j > 0 or $wnum > 0)
                        {
                            //Controles ********************
                            echo "<tr class=encabezadoTabla>";
                            echo "<td colspan=7 align=center><font size=4>CONTROLES</font></td>";
                            echo "</tr>";
                            echo "<tr class=fila1>";
                            echo "<td colspan=7 align=center><b>LIQUIDOS ENDOVENOSOS</b></td>";
                            echo "</tr>";
                            echo "<tr class=fila1>";
                            echo "<td align=center colspan=3><b>Componentes</b></td>";
                            echo "<td align=center colspan=4><b>Observaciones</b></td>";
                            echo "</tr>";

                            for ($k=0; $k < $wnum; $k++)
                            {
                           if($wlev_des[$k] == NULL)
                            {
                                $wlev_des[$k] = '';
                            }

                            echo "<tr class=fila2>";
                            echo "<td colspan=3>".$wlev_des[$k]."</td>";
                            //echo "<td colspan=3>".$wlev_obs."</td>";
                            //echo "<td colspan=3 align=center><textarea rows=3 cols=60 readonly class=tipoTA>".$wlev_des."</textarea></td>";
                            echo "<td colspan=4 align=center><textarea rows=3 cols=60 readonly class=tipoTA>".$wlev_obs[$k]."</textarea></td>";
                            echo "</tr>";
                            }
                            //Examenes
                            //traer_examenes($whis, $wing, $wfec_con, &$j);
                            if ($j > 0)
                            {
                                echo "<tr class=encabezadoTabla>";
                                echo "<td colspan=7 align=center><b>EXAMENES</b></td>";
                                echo "</tr>";
                                echo "<tr class=fila1>";
                                echo "<td align=center colspan=2><b>Examén</b></td>";
                                echo "<td align=center colspan=2><b>Observaciones</b></td>";
                                echo "<td align=center><b>Fecha</b></td>";
                                echo "<td align=center colspan=2><b>Estado</b></td>";
                                echo "</tr>";

                                if ($j > 0)
                                {
                                    for ($k=1; $k < $j; $k++)
                                    {
                                        echo "<tr class=fila2>";
                                        echo "<td colspan=2>".$wser[$k]."</td>";
                                        echo "<td colspan=2><textarea rows=2 cols=60 readonly class=tipoTA>".$wexa[$k]."</textarea></td>";
                                        echo "<td align=center>".$wfes[$k]."</td>";
                                        echo "<td align=center colspan=2>Pendiente</td>";
                                        echo "</tr>";
                                    }
                                }
                            }
                        }

                        //Pendientes ********************
                        echo "<tr class=encabezadoTabla>";
                        echo "<td colspan=7 align=center><font size=4>PENDIENTES</font></td>";
                        echo "</tr>";

                        $j=0;
                        //Dietas
                        traer_dietas($whis, $wing, $wfec_con, $j);
                        if ($j > 0)
                        {
                            echo "<tr class=fila1>";
                            echo "<td><font size=4><b>DIETA: </b></font></td>";
                            echo "<td><table>";
                            for ($k=1; $k<$j; $k++)
                            {
                                echo "<tr>";
                                echo "<td>** ".$wdie[$k]." **</td>";
                                echo "</tr>";
                            }
                            echo "</table>";
                            echo "</td>";
                            echo "<td align=left colspan=5><textarea rows=2 cols=60 readonly class=tipoTA>".$row["kardie"]."</textarea></td>";
                            echo "</tr>";
                        }

                        //Sondas y Curaciones
                        if (trim($row["karson"]) != "" or trim($row["karcur"]) != "")
                        {
                            echo "<tr class=fila1>";
                            echo "<td colspan=4 align=center><b>SONDAS</b></td>";
                            echo "<td colspan=3 align=center><b>CURACIONES</b></td>";
                            echo "</tr>";
                            echo "<tr class=fila2>";
                            echo "<td align=center colspan=4><textarea rows=3 cols=60 readonly class=tipoTA>".$row["karson"]."</textarea></td>";
                            echo "<td align=center colspan=3><textarea rows=3 cols=60 readonly class=tipoTA>".$row["karcur"]."</textarea></td>";
                            echo "</tr>";
                        }

                        //Cuidados de Enfermeria y Aislamientos
                        if (trim($row["karcui"]) != "" or trim($row["karais"]) != "")
                        {
                            echo "<tr class=fila1>";
                            echo "<td colspan=4 align=center><b>CUIDADOS DE ENFERMERIA</b></td>";
                            echo "<td colspan=3 align=center><b>AISLAMIENTOS</b></td>";
                            echo "</tr>";
                            echo "<tr class=fila2>";
                            //Cuidados de Enfermeria
                            echo "<td align=center colspan=4><textarea rows=7 cols=60 readonly class=tipoTA align=left>".$row["karcui"]."</textarea></td>";
                            //Aislamientos
                            echo "<td align=center colspan=3><textarea rows=7 cols=60 readonly class=tipoTA align=left>".$row["karais"]."</textarea></td>";
                            echo "</tr>";
                        }


                        $j=0;
                        //Dextrometer
                        traer_Dextrometer($whis, $wing, $wfec_con, $j);

                        //Mezclas y Dextrometer
                        if (trim($wart) != "" or trim($wfre) != "" or $j > 0 or trim($row["kardem"])!="")
                        {
                            echo "<tr class=fila1>";
                            echo "<td colspan=3 align=center><b>MEZCLAS</b></td>";
                            echo "<td colspan=4 align=center><b>DEXTROMETER</b></td>";
                            echo "</tr>";

                            if ($wart != "" or $wfre != "")
                            {
                                echo "<tr class=fila1>";
                                echo "<td colspan=3 align=center><b>&nbsp</b></td>";
                                echo "<td colspan=3 align=center><b>Insulina: </b><br>".$wart."</td>";
                                echo "<td colspan=1 align=center><b>Frecuencia: </b><br>".$wfre."</td>";
                                echo "</tr>";
                            }
                            echo "<tr class=fila2>";
                            echo "<td align=left colspan=3>".$row["karmez"]."</td>";

                            //Dextrometer
                            echo "<td colspan=4>";

                            echo "<center><table>";
                            if ($j > 0)
                            {
                                echo "<tr class=fila1>";
                                echo "<td align=center><b>Int.Menor</b></td>";
                                echo "<td align=center><b>Int.Mayor</b></td>";
                                echo "<td align=center><b>Dosis</b></td>";
                                echo "<td align=center><b>Unidad</b></td>";
                                echo "<td align=center><b>Observación</b></td>";
                                echo "<td align=center><b>Vía Admón</b></td>";
                                echo "</tr>";

                                for ($k=1; $k < $j; $k++)
                                {
                                    if (is_int ($k / 2))
                                    {$wclass = "fila1";}
                                    else
                                    {$wclass = "fila2";}

                                    echo "<tr class=".$wclass.">";
                                    echo "<td align=center>".$wime[$k]."</td>";
                                    echo "<td align=center>".$wima[$k]."</td>";
                                    echo "<td align=center>".$wdos[$k]."</td>";
                                    echo "<td align=center>".$wuni[$k]."</td>";
                                    echo "<td align=center>".$wobs[$k]."</td>";
                                    echo "<td align=center>".$wvia[$k]."</td>";
                                    echo "</tr>";
                                }
                            }

                            if (trim($row["kardem"]) != "")
                            {
                                echo "<tr>";
                                echo "<td align=center colspan=7><textarea rows=5 cols=60 readonly class=tipoTA>".$row["kardem"]."</textarea></td>";
                                echo "</tr>";
                            }
                            echo "</table>";

                            echo "</td>";
                            //echo "<td align=center colspan=1 rowspan=($j-1)><textarea rows=7 cols=60 readonly class=tipoTA>".$row["kardem"]."</textarea></td>";
                            echo "</tr>";
                            ////////////////////////////
                        }

                        //Cirugias e Interconsultas
                        if (trim($row["karcip"]) != "" or trim($row["karint"]) != "")
                        {
                            echo "<tr class=fila1>";
                            echo "<td colspan=4 align=center><b>CIRUGIAS</b></td>";
                            echo "<td colspan=3 align=center><b>INTERCONSULTAS</b></td>";
                            echo "</tr>";
                            echo "<tr class=fila2>";
                            echo "<td align=center colspan=4><textarea rows=3 cols=60 readonly class=tipoTA>".$row["karcip"]."</textarea></td>";
                            echo "<td align=center colspan=3><textarea rows=3 cols=60 readonly class=tipoTA>".$row["karint"]."</textarea></td>";
                            echo "</tr>";
                        }

                        //Rehabilitacion Cardiaca y Antecedentes Personales
                        if (trim($row["karter"]) != "" or trim($row["karrec"]) != "" or trim($row["kartef"]) != "")
                        {
                            echo "<tr class=fila1>";
                            echo "<td colspan=3 align=center><b>TERAPIA RESPIRATORIA</b></td>";
                            echo "<td colspan=2 align=center><b>REHABILITACION CARDIACA</b></td>";
                            echo "<td colspan=2 align=center><b>TERAPIA FISICA</b></td>";

                            $wterres=str_replace("\n","<br>",htmlentities($row["karter"],ENT_QUOTES));
                            $wreacar=str_replace("\n","<br>",htmlentities($row["karrec"],ENT_QUOTES));
                            $wterfis=str_replace("\n","<br>",htmlentities($row["kartef"],ENT_QUOTES));

                            echo "</tr>";
                            echo "<tr class=fila2>";
                            echo "<td align=left colspan=3>".$wterres."</td>";
                            echo "<td align=left colspan=2>".$wreacar."</td>";
                            echo "<td align=left colspan=2>".$wterfis."</td>";
                            echo "</tr>";
                        }

                        $j=0;
                        //Medicamentos
                        traer_medicamentos($whis, $wing, $wfec_con, $j);
                        echo "<tr class=encabezadoTabla>";
                        echo "<td colspan=7 align=center><font size=4><b>MEDICAMENTOS</b></font></td>";
                        echo "</tr>";
                        echo "<tr class=fila1>";
                        echo "<td align=center><b>Medicamento</b></td>";
                        echo "<td align=center><b>Dosis</b></td>";
                        echo "<td align=center><b>Frecuencia</b></td>";
                        echo "<td align=center><b>Fecha Inicial</b></td>";
                        echo "<td align=center><b>Hora de Inicio</b></td>";
                        echo "<td align=center><b>Condición</b></td>";
                        echo "<td align=center><b>Observaciones</b></td>";
                        echo "</tr>";

                        if ($j > 0)
                        {
                            for ($k=1; $k < $j; $k++)
                            {
                                $whora1 = explode(":",$whorai[$k]);           //Para solo mostrar el numero de la hora, sin los ceros (00:00)

                                //////////
                                /*
                                $arrAplicacion = obtenerVectorAplicacionMedicamentos(date("Y-m-d"),$articulo->fechaInicioAdministracion,$articulo->horaInicioAdministracion,$horasPeriodicidad);
                                $horaArranque = 0;
                                $aplicaGraficaSuministro = true;

                                $cont1 = 1;
                                $cont2 = $horaArranque; //Desplazamiento desde la hora inicial
                                $caracterMarca = "*";
                                $claseGrafica = "";

                                $articulo->suspendido == 'on' ? $claseGrafica = "suspendido" : $claseGrafica = "fondoVerde";

                                while($cont1 <= 24)
                                   {
                                    if (isset($arrAplicacion[$cont2]) && $arrAplicacion[$cont2] == $caracterMarca && $aplicaGraficaSuministro)
                                       {
                                        echo "<td class='$claseGrafica' align='center'>";
                                        echo $caracterMarca;
                                        echo "</td>";
                                       }
                                      else
                                        {
                                         echo "<td>&nbsp;</td>";
                                        }

                                    if ($cont2 == 24)
                                       {
                                        $cont2 = 0;
                                       }

                                    $cont1++;
                                    $cont2++;

                                    if ($cont2 % 2 != 0)
                                       {
                                        $cont2++;
                                       }
                                    if ($cont1 % 2 != 0)
                                       {
                                        $cont1++;
                                       }

                                    if ($cont2 == $horaArranque)
                                       {
                                        break;
                                       }
                                   }
                                */
                                //////////

                                if (is_int ($k / 2))
                                {$wclass = "fila1";}
                                else
                                {$wclass = "fila2";}

                                echo "<tr class=".$wclass.">";
                                echo "<td>".$wartic[$k]."</td>";               //Articulo
                                echo "<td align=center>".$wdosis[$k]."</td>";  //Dosis
                                echo "<td align=center>".$wfrecu[$k]."</td>";  //Frecuencia
                                echo "<td align=center>".$wfecin[$k]."</td>";  //Fecha de Inicio
                                echo "<td align=center>".$whora1[0]."</td>";   //Hora de Inicio
                                echo "<td align=center>".$wcondi[$k]."</td>";  //Condicion
								$wobserv[$k] = preg_replace('/<[^>]*>/', '', $wobserv[$k]);
                                echo "<td align=center><textarea row=3 col=30 readonly>".$wobserv[$k]."</textarea></td>";                //Observacion
                                echo "</tr>";
                            }
                        }

                        //Observaciones Generales
                        if (trim($row["karobs"]) != "")
                        {
                            echo "<tr class=fila1>";
                            echo "<td colspan=7 align=center><b>OBSERVACIONES GENERALES</b></td>";

                            //$wobsgal=str_replace("\n","<br>",htmlentities($row["karobs"],ENT_QUOTES));
                            echo "</tr>";
                            echo "<tr class=fila2>";
                            echo "<td align=center colspan=7><textarea rows=3 cols=120 readonly class=tipoTA>".$row["karobs"]."</textarea></td>";
                            echo "</tr>";
                        }

                        echo "<tr class=fila1>";
                        echo "<td colspan=7><b>&nbsp</b></td>";
                        echo "</tr>";

                        echo "</table>";

                        echo "<br>";

                        //================================================================================================================================================
                        /* OJO OJO OJO OJO OJO OJO OJO OJO **************************************************************

                        /*   Esto se quita temporalmente, mientras las enfermeras se acostumbran a la entrega del turno por este programa
                        //====================================================================================================================================================================
                        //Seccion GRABAR
                        //====================================================================================================================================================================
                        echo "<center><table>";
                        echo "<tr class=fila2>";
                        if (isset($wobs) and trim($wobs) != "")
                           echo "<td colspan=3 align=center><b>Observaciones</b><br><textarea name=wobs rows=5 cols=60>".$wobs."</textarea></td>";
                          else
                             echo "<td colspan=3 align=center><b>Observaciones</b><br><textarea name=wobs rows=5 cols=60></textarea></td>";
                        echo "</tr>";
                        echo "</table>";
                        echo "</div>";

                        echo "<center><table>";
                        echo "<tr class=encabezadoTabla>";
                        echo "<td colspan=3 align=center><input type=checkbox name=wgrabar>Grabar&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type='button' onclick='recarga()' value='ENTRAR'></b></td>";
                        echo "</tr>";
                        echo "</table>";
                        */
                    }
                    else  //del 2do if ($num > 0)
                    {
                         echo "<table>";
                         echo "<tr class=encabezadoTabla>";
                         echo "<td>No existen datos para este paciente</td>";
                         echo "</tr>";
                         echo "</table>";
                    }
                }
                else  //del if ($wok==false)
                { mostrar_foto($whis, $wing, $wturno, $wfec); }
            }
            else  //del if ($wfec == $wfecha)
            { mostrar_foto($whis, $wing, $wturno, $wfec); }

            echo "<br><br>";
            echo "<table>";
            echo "<tr><td><A HREF='Entrega_de_Turno_Enfermeria.php?wemp_pmla=".$wemp_pmla."&wcco=".$wcco."&wfec=".$wfec."' class=tipo4V>Retornar</A></td></tr>";
            echo "</table>";
        }
        else
        {
            elegir_historia($wtur_grabar);

              //================================================================================================================================================
              /* OJO OJO OJO OJO OJO OJO OJO OJO **************************************************************
              /*   Esto se quita temporalmente, mientras las enfermeras se acostumbran a la entrega del turno por este programa
              /*
              if ($wfec == $wfecha)
                 {
                  $wnomusu=traer_nombre_usuario($wusuario);

                  echo "<center><table>";

                  echo "<tr class=encabezadoTabla>";
                  echo "<th>Entrega el Turno</th>";
                  echo "<th colspan=3>Recibe el Turno</th>";
                  echo "</tr>";
                  if (isset($wusurec) and $wusurec != "" and $wusurec != $wusuario )
                     $wnomrec=traer_nombre_usuario($wusurec);
                  echo "<tr class=fila1>";
                  echo "<td>".$wusuario." <b>".$wnomusu."</b></td>";
                  if (!isset($wusurec)) $wusurec="";
                    echo "<td align=center><input type=text name=wusurec value='".$wusurec."' size=8></td>";
                  if (isset($wnomrec) and $wnomrec != "")
                     echo "<td><b>".$wnomrec."</b></td>";
                    else
                       echo "<td align=center><input type='submit' value='ENTRAR'></td>";
                  echo "</tr>";
                  echo "</table>";


                  if (isset($wnomrec) and trim($wnomrec) != "")
                     {
                      echo "<center><table>";
                      echo "<tr class=encabezadoTabla>";
                      echo "<td colspan=3 align=center><input type=checkbox name=wgrabar_todos>Grabar&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type='button' onclick='enter()' value='ENTRAR'></b></td>";
                      echo "</tr>";
                      echo "</table>";
                     }
                    else
                      if (isset($wusurec) and $wusurec != "" and trim($wnomrec) == "")
                         {
                          ?>
                            <script>
                              alert ("El Usuario NO existe o NO esta activo");
                            </script>
                          <?php
                         }
                 }
              */
            echo "<br>";
            echo "<center><table>";
            echo "<tr><td><A HREF='Entrega_de_Turno_Enfermeria.php?wemp_pmla=".$wemp_pmla."' class=tipo4V>Retornar</A></td></tr>";
            echo "</table>";
        }
    }

    echo "<br><br>";
    echo "<table>";
    echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
    echo "</table>";
} // if de register

?>
</body>
</html>
