<?php
include_once("conex.php");
include_once("root/fpdf.php");

class PDF extends FPDF
{
	//Columna actual
	var $col=0;
	
	//Ordenada de comienzo de la columna
	var $y0;
	
	//Cantidad de columnas maximas
	var $totalColumnas = 2;

	//Tama�o horizontal (X) y vertical (Y) total de la pagina en mm
	var $yTotal = 254;
	var $xTotal = 254;
	
	//Margen desde la izquierda
	var $margenXIzquierda = 10;
	
	//Para tablas multicell
	var $widths;
	var $aligns;

	function Header()
	{
		//Cabacera
		global $title;
		
		//Coordenadas y valores		
		$coordY = 0;
		$coordX = 0;
		$altoColumna1 = 5;
		$anchoColumna1 = 25;
		$inicioDatosDemogX = 60;

		//Capturo X y Y para verificar donde inicia
		$this->SetFont('Arial','',9);
		$xInicio = $this->GetX();
		$yInicio = $this->GetY();
		
		//Fuente Arial, Normal, tama�o 10
		$this->SetFont('Arial','',9);
		
		//Logo de la HCE
		$this->Image('../../images/medical/root/logoG.jpg',5,8,50,20);
		
		/**************************************************
		 * Construcci�n de la tabla de datos demograficos *
		 * ************************************************/
		
		//Paciente
		$this->SetFont('Arial','B',9);
		$textoCelda = "Paciente";
		$this->SetY(5);
		$this->SetX($inicioDatosDemogX);
		$this->SetTextColor(1); 
		$coordY = $this->GetY();	
		$this->Cell($anchoColumna1,$altoColumna1,$textoCelda,1,1,'L',false);
		
		//Nombre paciente	
		$this->SetFont('Arial','',9);	
		$textoCelda = "MAURICIO SANCHEZ CASTA�O $xInicio,$yInicio";
		$this->SetY($coordY);
		$this->SetX($inicioDatosDemogX + $anchoColumna1);
		$this->Cell(0,$altoColumna1,$textoCelda,1,1,'C',false);
		
		//Historia		
		$this->SetFont('Arial','B',9);
		$textoCelda = "Historia";
		$this->SetX($inicioDatosDemogX);
		$coordY = $this->GetY(); 
		$this->Cell($anchoColumna1,$altoColumna1,$textoCelda,1,1,'L',false);
		
		//Campo 4
		$this->SetFont('Arial','',9);
		$textoCelda = "777777";
		$this->SetY($coordY);
		$this->SetX($inicioDatosDemogX + $anchoColumna1);
		$this->Cell($anchoColumna1,$altoColumna1,$textoCelda,1,1,'C',false);
		
		//Ingreso	
		$this->SetFont('Arial','B',9);
		$longTemp = $this->GetStringWidth($textoCelda);	
		$textoCelda = "Ingreso";
		$this->SetY($coordY);
		$this->SetX($inicioDatosDemogX + $anchoColumna1 + $anchoColumna1);
		$this->Cell($anchoColumna1,$altoColumna1,$textoCelda,1,1,'L',false);
		
		//Campo 5	
		$this->SetFont('Arial','',9);
		$longTemp = $this->GetStringWidth($textoCelda);	
		$textoCelda = "7";
		$this->SetY($coordY);
		$this->SetX($inicioDatosDemogX + $anchoColumna1 * 3);
		$this->Cell($anchoColumna1,$altoColumna1,$textoCelda,1,1,'C',false);
		
		//Sexo	
		$this->SetFont('Arial','B',9);
		$longTemp = $this->GetStringWidth($textoCelda);	
		$textoCelda = "Sexo";
		$this->SetY($coordY);
		$this->SetX($inicioDatosDemogX + $anchoColumna1 * 4);
		$this->Cell($anchoColumna1-10,$altoColumna1,$textoCelda,1,1,'L',false);
		
		//Campo 5	
		$this->SetFont('Arial','',9);
		$longTemp = $this->GetStringWidth($textoCelda);	
		$textoCelda = "MASCULINO";
		$this->SetY($coordY);
		$this->SetX($inicioDatosDemogX + $anchoColumna1 * 5 -10);
		$this->Cell(0,$altoColumna1,$textoCelda,1,1,'C',false);
		
		//Identificacion
		$this->SetFont('Arial','B',9);
		$textoCelda = "Identificaci�n";
		$coordY = $this->GetY(); 
		$this->SetX($inicioDatosDemogX);
		$this->Cell($anchoColumna1,$altoColumna1,$textoCelda,1,1,'L',false);
		
		//Campo 6
		$this->SetFont('Arial','',9);
		$textoCelda = "CC - 77777777";
		$this->SetY($coordY);
		$this->SetX($inicioDatosDemogX + $anchoColumna1 * 1);
		$this->Cell($anchoColumna1 + $anchoColumna1 * 2,$altoColumna1,$textoCelda,1,1,'C',false);
		
		//Edad	
		$this->SetFont('Arial','B',9);
		$longTemp = $this->GetStringWidth($textoCelda);	
		$textoCelda = "Edad";
		$this->SetY($coordY);
		$this->SetX($inicioDatosDemogX + $anchoColumna1 * 4);
		$this->Cell($anchoColumna1-10,$altoColumna1,$textoCelda,1,1,'L',false);
		
		//Campo 7
		$this->SetFont('Arial','',9);
		$textoCelda = "777 A�os";
		$this->SetY($coordY);
		$this->SetX($inicioDatosDemogX + $anchoColumna1 * 5 -10);
		$this->Cell(0,$altoColumna1,$textoCelda,1,1,'C',false);
		
		//Ubicacion
		$this->SetFont('Arial','B',9);
		$textoCelda = "Ubicaci�n";
		$coordY = $this->GetY(); 
		$this->SetX($inicioDatosDemogX);
		$this->Cell($anchoColumna1,$altoColumna1,$textoCelda,1,1,'L',false);
		
		//Campo 9
		$this->SetFont('Arial','',9);
		$textoCelda = "HOSPITALIZACI�N PISO 7 - HABITACION 7777";
		$this->SetY($coordY);
		$this->SetX($inicioDatosDemogX + $anchoColumna1 * 1);
		$this->Cell(0,$altoColumna1,$textoCelda,1,1,'C',false);
		
		//Responsable
		$this->SetFont('Arial','B',9);
		$textoCelda = "Responsable";
		$coordY = $this->GetY(); 
		$this->SetX($inicioDatosDemogX);
		$this->Cell($anchoColumna1,$altoColumna1,$textoCelda,1,1,'L',false);
		
		//Campo 9
		$this->SetFont('Arial','',9);
		$textoCelda = "ENTIDAD SUPER 7 SUPER ASEGURADORA INCREIBLE";
		$this->SetY($coordY);
		$this->SetX($inicioDatosDemogX + $anchoColumna1 * 1);
		$this->Cell(0,$altoColumna1,$textoCelda,1,1,'C',false);
		
		//Salto de linea
		$this->Ln(10);
		
		//Guardar ordenada
		$this->y0=$this->GetY();
	}

	function Footer()
	{
		//Pie de p�gina
		$this->SetY(-15);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'P�g. '.$this->PageNo().' de {nb}',0,0,'C');
	}

	function SetCol($col)
	{
		//Establecer la posici�n de una columna dada
		$this->col=$col;
		$x=10+$col*100;
		$this->SetLeftMargin($x);
		$this->SetX($x);
	}
	
	function SetColResultado($col,$anchoColumna)
	{
		//Establecer la posici�n de una columna dada
		$this->col = $col;		
		$x = $col*$anchoColumna;
		
		if($x == 0){
			$x = $this->margenXIzquierda;
		} else {
			$x += $this->margenXIzquierda;
		}
		
		$this->SetLeftMargin($x);
		$this->SetY($this->y0);
		$this->SetX($x);
	}

//	function AcceptPageBreak()
//	{
//		//M�todo que acepta o no el salto autom�tico de p�gina
//		if($this->col<2)
//		{
//			//Ir a la siguiente columna
//			$this->SetCol($this->col+1);
//			//Establecer la ordenada al principio
//			$this->SetY($this->y0);
//			//Seguir en esta p�gina
//			return false;
//		}
//		else
//		{
//			//Volver a la primera columna
//			$this->SetCol(0);
//			//Salto de p�gina
//			return true;
//		}
//	}

	function SetWidths($w)
{
    //Set the array of column widths
    $this->widths=$w;
}

function SetAligns($a)
{
    //Set the array of column alignments
    $this->aligns=$a;
}

function Row($data)
{
    //Calculate the height of the row
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    $h=5*$nb;
    //Issue a page break first if needed
    $this->CheckPageBreak($h);
    //Draw the cells of the row
    for($i=0;$i<count($data);$i++)
    {
        $w=$this->widths[$i];
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
        //Save the current position
        $x=$this->GetX();
        $y=$this->GetY();
        //Draw the border
        $this->Rect($x,$y,$w,$h);
        //Print the text
        $this->MultiCell($w,5,$data[$i],0,$a);
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h);
}

function CheckPageBreak($h)
{
    //If the height h would cause an overflow, add a new page immediately
    if($this->GetY()+$h>$this->PageBreakTrigger)
        $this->AddPage($this->CurOrientation);
}

function NbLines($w,$txt)
{
    //Computes the number of lines a MultiCell of width w will take
    $cw=&$this->CurrentFont['cw'];
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s=str_replace("\r",'',$txt);
    $nb=strlen($s);
    if($nb>0 and $s[$nb-1]=="\n")
        $nb--;
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
    {
        $c=$s[$i];
        if($c=="\n")
        {
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
            continue;
        }
        if($c==' ')
            $sep=$i;
        $l+=$cw[$c];
        if($l>$wmax)
        {
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
            }
            else
                $i=$sep+1;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
        }
        else
            $i++;
    }
    return $nl;
}

function GenerateWord()
{
    //Get a random word
    $nb=rand(3,10);
    $w='';
    for($i=1;$i<=$nb;$i++)
        $w.=chr(rand(ord('a'),ord('z')));
    return $w;
}

function GenerateSentence()
{
    //Get a random sentence
    $nb=rand(1,10);
    $s='';
    for($i=1;$i<=$nb;$i++)
        $s.=$this->GenerateWord().' ';
    return substr($s,0,-1);
}


}

//Generacion del documento
$pdf=new PDF();
$pdf->AliasNbPages();
//$pdf->SetAutoPageBreak(true,10);

//Titulo del documento
$title='Impresi�n de Historia Clinica';
$pdf->SetTitle($title);

//Autor del documento
$pdf->SetAuthor('Cl�nica las Am�ricas');

//Adiciono pagina 1 de prueba
$pdf->AddPage();

//Titulo de la seccion
$xInicio = $pdf->GetX();
$yInicio = $pdf->GetY();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(40,10,"Seccion a UNA columna ($xInicio,$yInicio)");
$pdf->Ln();

/*Seccion a una columna
 * 
 */
$pdf->SetFont('Arial','',10);

$pdf->MultiCell(0,5,"Este m�todo permite imprimir texto con saltos de l�nea. Estos pueden ser autom�ticos (tan pronto como el texto alcanza el borde derecho de la celda) o expl�cito (via el car�cter ). Tantas celdas como sean necesarias son creadas, uno debajo de otra.
El texto puede ser alineado, centrado o justificado. El bloque de celda puede ser enmarcado y el fondo impreso",'','J','');

$pdf->Ln();

$tempc1Y = $pdf->GetY();

$pdf->MultiCell(90,5,"Este m�todo permite imprimir texto con saltos de l�nea. Estos pueden ser autom�ticos (tan pronto como el texto alcanza el borde derecho de la celda) o expl�cito (via el car�cter ). Tantas celdas como sean necesarias son creadas, uno debajo de otra. 
El texto puede ser alineado, centrado o justificado. El bloque de celda puede ser enmarcado y el fondo impreso In essence, on July 20, 1866, the steamer Governor Higginson, from the Calcutta & BurnachSteam Navigation Co., presence of an unknown reef; he was even about to fix",'','J','');

$tempc3Y = $pdf->GetY();

$pdf->SetCol(1);
$pdf->SetY($tempc1Y);

$pdf->MultiCell(90,5,"Para comprobar la configuraci�n de su conexi�n, haga clic en el men� Herramientas y despu�s en Opciones de Internet. Haga clic en Configuraci�n en la ficha Conex The relevant data on this apparition, as recorded in various logbooks, agreed pretty closely as to the structure of the object or creature in question, its unprecedented speed of movement, its startling",'','J','');

$tempc2Y = $pdf->GetY();

$pdf->SetCol(0);

if($tempc2Y > $tempc3Y){
	$pdf->SetY($tempc2Y);		
} else {
	$pdf->SetY($tempc3Y);
}

$pdf->Ln();
$pdf->MultiCell(0,5,"Indica si los bordes deben ser dibujados alrededor del bloque la celda. Indica si los bordes deben ser dibujados alrededor del bloque la celda.",'','J','');
$pdf->Ln();

/**
 * ESTA ES UNA TABLA CON RESULTADOS A CUATRO COLUMNAS!!!!!!!!... DINAMIZAR EN METODOS DESPUES
 */
//Contenido de una linea
$descripcion = "Un examen cuya descripcion contiene demasiados caracteres";
$lectura = "49.5Twitter, la popular red social de internet, qued� fuera de servicio este jueves tras un ataque de piratas inform�ticos. Ordenada de la esquina superior izquierda. Si no se especifica o es igual a null, se utilizar� la ordenada actual, adem�s, un salto de p�gina es invocado primero si es necesario (en caso de que est� habilitado el salto de p�gina autom�tico) y, despu�s de la llamada, la ordenada actual se mueve a la parte inferior de la imagen. ";
$unidadMedida = "Unidades Internacionales";
$valorReferencia = "[1,20] Unidades Internacionales Un examen cuya descripcion contiene demasiados caracteres";

//Tabla con multicell
$pdf->SetWidths(array(50,50,30,40));
//Encabezado tabla
$pdf->SetFont('Arial','B',8);
$pdf->Row(array("Descripcion","Lectura","Unidad de Medida","Valor de referencia"));

$pdf->SetFont('Arial','',8);
$pdf->Row(array($descripcion,$lectura,$unidadMedida,$valorReferencia));

//Imagen
$pdf->Ln();

$pdf->SetX($pdf->margenXIzquierda);

//INSERTO OTRA IMAGEN
list($width, $height, $type, $attr) = getimagesize("../../images/medical/root/logoG.jpg");

$anchoMm = $width / 3.75;
$altoMm = $height / 3.75; 

$ytotal = $pdf->yTotal;
$yactual = $pdf->GetY();

$altoDisponible = ($ytotal-$yactual) / 3.75;

if( $altoMm < $altoDisponible ){		
	$pdf->MultiCell(0,5,"$width, $height, $type, $attr y actual $yactual altoMm $altoMm  < altoDisponible $altoDisponible",'','J','');	
} else {
	if( ($altoMm/2) > $altoDisponible ){
		$pdf->AddPage();		
	}
	$pdf->MultiCell(0,5,"$width, $height, $type, $attr y actual $yactual altoMm $altoMm  > altoDisponible $altoDisponible ytotal $ytotal yactual $yactual",'','J','');
}

//Antes de poner una imagen verifico que DE PIXEL A mm DIVIDIR POR 3.75.  HAY QUE QUITAR LAS COMILLAS DOBLES
//$pdf->Image('../../images/imagen1.jpg',10,$pdf->GetY(),'',80);
$pdf->Image('../../images/medical/root/logoG.jpg',10,$pdf->GetY(),'',80);

$pdf->SetY($pdf->GetY() + 80);

//MAS TEXTO
$pdf->Ln();

//Texto en la pagina
$pdf->MultiCell(0,5,"La palabra exponer remite, b�sicamente, a la idea de explicar algo o hablar e algo para que los dem�s lo conozcan. As� pues, podemos definir la exposici�n como el tipo de discurso que tiene por objeto transmitir informaci�n. El texto que cumple este objetivo se denomina texto expositivo.
Se utiliza para explicar un tema de estudio, para informar a alguien de nuestras ideas, para informar a alguien de nuestras ideas, para dar una noticia... Y tambi�n son textos expositivos los tratados cient�ficos y t�cnicos, los libros did�cticos, los manuales de instrucciones, y todos aquellos textos cuya finalidad consiste en informar sobre conceptos, sobre hechos o sobre la manera como se realiza un proceso.
Puesto que el texto expositivo busca informar y hacer comprensible la informaci�n, debe presentar los contenidos de una forma clara y ordenada. Claridad, orden y objetividad son las principales caracter�sticas de los textos expositivos.\n
Existen diversas formas de presentar ideas o acontecimientos. A los textos que cumplen con esta funci�n se les denomina textos expositivos. Cuando la finalidad del texto es contar o narrar acontecimientos en los que intervienen personajes, tenemos un texto narrativo.
Los hechos o acontecimientos que componen el texto narrativo se desarrollan en un tiempo y en un espacio que pueden ser reales o virtuales.
Ejemplo: \n
Suced�a ese amanecer h�medo. El salitre ven�a con el aire y se quedaba enredado en los cabellos y en la piel cada vez que se escurr�a la s�bana. Tambi�n estaba en la silla al lado de la cama con la l�mpara, unos libros y un paquete comenzado de cigarrillos. Era uno de los amaneceres m�s h�medos del mundo.
'El texto narrativo se caracteriza por tener un estilo propio del autor que lo produce. En esta sentido se habla de un estilo literario dado que la presentaci�n de los acontecimientos es organizada de una manera particular'. 
Roberto Burgos Cantor. \n

El principal objetivo de los medios de comunicaci�n es proporcionar informaci�n. Para alcanzar ese objetivo se utilizan diversos g�neros period�sticos, es decir diferentes tipos de textos.

Los periodistas informan sobre la realidad de distintas formas: narran los acontecimientos recientes en forma objetiva: las noticias, o exponen la informaci�n en forma ampliada, presentado un punto de vista o una interpretaci�n particular: las cr�nicas. Tanto en las noticias como en las cr�nicas, los hechos est�n enlazados por conectores temporales. Por otra parte, tanto la noticia como la cr�nica se caracterizan por ordenar la informaci�n en forma decreciente, es decir, los datos de mayor inter�s se presentan al comienzo con el fin de captar la atenci�n de los lectores.
PRESENTACI�N

Es posible propiciar el pensar desde la misma Universidad. Es decir, ense�ar a pensar para valorar la vida. Tambi�n es posible generar una nueva actitud de pensar que promueva el respeto por los principios y valores universales, si ense�amos de tal forma que orientemos hacia un proceso aut�ntico y aut�nomo de pensar, si dejamos de controlar excesivamente, si permitimos que el alumno pueda pensar por cuenta propia. �C�mo debi�ramos proceder para que realmente el estudiante piense, y que no siga ejecutando los mismos modelos o estereotipos que sigue repitiendo en forma rutinaria en el colegio? �Qu� debemos hacer o c�mo debemos cambiar para que efectivamente valore el pensar como su principal tarea como educando y descubra por s� mismo el valor formativo del pensar?.

Cuando un estudiante tiene la grata oportunidad de disfrutar del pensar por s� mismo, es como si descubriera un mundo que hab�a estado oculto en forma inexplicable para �l, ya que el complejo institucional -su marco de referencia- en el que supuestamente se ha educado, no ha estado orientado o m�s bien lo ha excluido- porque no lo exig�a o promov�a o porque directamente lo reprim�a- impidiendo pensar, criticar e interrogar.

Uno de los retos mayores que enfrenta la educaci�n moderna, est� relacionada con los procesos del pensamiento y promoci�n del desarrollo integral del educando. En tal sentido, el presente libro aparte de los criterios pedag�gicos ofrece a los estudiantes una metododolog�a para la comprensi�n de la lectura y la adquisici�n de nuevos conocimientos, que se resume en el 'como ingresar al mundo del texto y salir de �l sin lastimarse'.

La principal actividad de la universidad debe ser ense�ar a pensar, a comprender e interpretar el mundo, y es la lectura el medio privilegiado, indispensable para el desarrollo de las operaciones intelectuales. La lectura de rese�as, relatorias, informes, art�culos cient�ficos y ensayos, requiere de m�ltiples habilidades relacionadas con las operaciones del pensamiento; fundamentales para el desarrollo intelectual del joven estudiante.

Teniendo en cuenta los anteriores conceptos he elaborado el presente manual y tiene como objetivo ayudar a aquellas personas que necesitan mejorar la competencia de lectura y escritura durante los primeros semestres en la universidad. 

La primera parte del libro presenta los elementos b�sicos para interpretar, comprender y asumir el texto como instrumento de conocimiento, se conceptuaaliza en torno al texto argumentativo. La segunda parte aborda el estudio te�rico y pr�ctico de la producci�n de ensayo; se presentan trece estrategias para su composici�n. La tercera parte es toda una reflexi�n sobre la universidad que so�amos: integral, humanista, comprometida con la ciencia y la academia. La cuarta parte es un manual para la formaci�n de lectores y ejercicios para pensar con todo el cerebro. La quinta parta el ordenador de ideas. 

Las reflexiones presentes, no pretenden responder los interrogantes arriba mencionados, tampoco contribuir propiamente a una respuesta concreta a los mismos, sino continuar la discusi�n en torno a una Universidad razonante y poder, adem�s, enunciar otros problemas impl�citos en el proceso lector y que en nuestro medio est�n evidenci�ndose cada vez m�s, haciendo ineludible una reforma curricular y el logro de un nuevo proyecto educativo. Centrado en el hacer acad�mico y en la responsabilidad intelectual.

Al hacerse cada vez m�s evidente la crisis que el asumir esta reflexi�n implica para la Universidad se plantea adem�s lo que ha afectado y condicionado el cuestionado modelo de ense�ar vigente al estudiante que hoy tenemos: acr�tico, indisciplinado, despolitizado, con la inercia manifiesta de su minor�a de edad y con una actitud contestar�a y aversiva hacia todo lo que para �l representa academia y estudio.

El Autor

OBJETVO GENERAL DEL MANUAL

Hacer de la lectura y la escritura herramientas fundamentales en los procesos preliminares de la investigaci�n, el dise�o de proyectos de vida, construcci�n de conocimiento e interpretaci�n de contextos sociales o pedag�gicos.


",'','J','');

//Adiciono pagina 3 de prueba para margenes
$pdf->AddPage();
$pdf->SetFont('Arial','',8);

//Coordenadas clave
$pdf->SetY(0);
$pdf->SetX(0);
$pdf->Cell(8,5,". (0,0)");

$pdf->SetY(10);
$pdf->SetX(10);
$pdf->Cell(8,5,". (10,10)");

$pdf->SetY(0);
$pdf->SetX(50);
$pdf->Cell(8,5,". (50,0)");

$pdf->SetY(50);
$pdf->SetX(50);
$pdf->Cell(8,5,". (50,50)");

$pdf->SetY(0);
$pdf->SetX(100);
$pdf->Cell(8,5,". (100,0)");

$pdf->SetY(0);
$pdf->SetX(150);
$pdf->Cell(8,5,". (150,0)");

$pdf->SetY(105);
$pdf->SetX(0);
$pdf->Cell(8,5,". (0,105)");

$pdf->SetY(105);
$pdf->SetX(254);
$pdf->Cell(8,5,". (254,105)");

$pdf->SetY(0);
$pdf->SetX(200);
$pdf->Cell(8,5,". (200,0)");

$pdf->SetY(0);
$pdf->SetX(210);
$pdf->Cell(8,5,". (210,0)");

$pdf->SetY(100);
$pdf->SetX(100);
$pdf->Cell(8,5,". (100,100)");

$pdf->SetY(200);
$pdf->SetX(200);
$pdf->Cell(8,5,". (200,200)");

$pdf->SetY(250);
$pdf->SetX(200);
$pdf->Cell(8,5,". (200,250)");

$pdf->SetY(254);
$pdf->SetX(0);
$pdf->Cell(8,5,". (0,254)");

$pdf->SetY(254);
$pdf->SetX(50);
$pdf->Cell(8,5,". (50,254)");

$pdf->SetY(254);
$pdf->SetX(100);
$pdf->Cell(8,5,". (100,254)");

$pdf->SetY(254);
$pdf->SetX(150);
$pdf->Cell(8,5,". (150,254)");

$pdf->SetY(254);
$pdf->SetX(200);
$pdf->Cell(8,5,". (200,254)");

$pdf->SetY(127);
$pdf->SetX(105);
$pdf->Cell(8,5,". (105,127) Centro");

$pdf->SetY($pdf->yTotal);
$pdf->SetX(210);
$pdf->Cell(8,5,". (254,254) Punto final");

$pdf->SetX(10);

$pdf->Output('PruebaImpresion.pdf','I');
?>

