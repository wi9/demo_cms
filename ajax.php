<?php
   error_reporting(E_ERROR | E_WARNING | E_PARSE);
include("function.php");

$var_class = $_GET["class"];   //  class
	$func  = $_GET["event_msg"] ;  // methoad
    $obj = new $var_class();
	
        $param_array =  array_merge($_GET,$_POST);
        
	if(isset( $param_array) ) 
    	$obj->$func($param_array);
	else
	 $obj->$func();
   
?>
