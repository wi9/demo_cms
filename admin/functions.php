<?php
   include("class/classCatalog.php");
   include("class/classImage.php");
   include("class/classProduct.php");
   error_reporting(E_ERROR | E_WARNING | E_PARSE);
   
   
   if(!isset($_GET["page"]))
      {
		   //include("dashboard.php");
                $catalog = new catalog();
                $catalog->get_catalog_table();
		   die();
	  }
   
    $var_class = $_GET["page"];   //  class
	$func  = $_GET["event_msg"] ;  // methoad
    $obj = new $var_class();
	
        $param_array =  array_merge($_GET,$_POST);
        
	if(isset( $param_array) ) 
    	$obj->$func($param_array);
	else
		$obj->$func();
   
   
  ?>