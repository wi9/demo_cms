<?php 
 //error_reporting(0);
 error_reporting(E_ERROR | E_WARNING | E_PARSE);
  include("class/classCatalog.php");
 include("class/classImage.php");
 include("class/classProduct.php");
  
  
    $var_class = $_GET["class"];   //  class
	$func  = $_GET["event_msg"] ;  // methoad
    $obj = new $var_class();
	
	
	if(isset($_POST)) { 
    	$obj->$func($_POST);
	   	
	}
	else
		$obj->$func();
  

?>