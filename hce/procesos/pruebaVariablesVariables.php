<?php
// $a = 'hola';
// $$a = 'mundo';

$a = "1";
$b = "2";
$c = "3";

$array = array("a","b","c");


// echo $a." ".$$a."<br>";

function prueba()
{
	// global $a;
	// global $$a;
	// echo $a." ".$$a."<br>";
	
	global $array;
	
	for ($i=0;$i<count($array);$i++){
	   
	   // // solo en version 5
	   // global $$array[$i];
	   
	   global ${$array[$i]};
	   echo ${$array[$i]};
	   
	   $$array[$i] = ${$array[$i]};
	   echo $$array[$i];
	   
	   // global ${$array}[$i];
	   // echo ${$array}[$i];
	}
}

prueba();
?>