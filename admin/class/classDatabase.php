<?php

define("IMAGE_PATH","../img/img_content/");
define("MEDIA_PATH","../media/");
define("SHOW_IMAGE_PATH","../images/img_content/");
define("SHOW_MEDIA_PATH","/media/");

define("BACKEND_IMAGE_PATH","img/img_content/");
define("FONTEND_IMAGE_PATH","img/img_content/");
define("HEADER_IMAGE_PATH","images/img_content/");

 //error_reporting(E_ERROR | E_WARNING | E_PARSE);


class database {
   
    // config database
 
	 private $dbhost = "localhost";
	 private $dbname = "demo_cms" ;
        private $dbusername = "root" ;
	 private $dbpassword = "1234";
         /*
         private $dbhost = "localhost";
	 private $dbname = "windevzc_demo1001" ;
         private $dbusername = "windevzc_1001" ;
	 private $dbpassword = "7Js5oWan";
	 */
	 public static $link ;
         

	 
	 
     public function connect_db()
	 {
		  self::$link =  mysql_connect($this->dbhost,$this->dbusername,$this->dbpassword);
		
	
		 if(self::$link)
		   {
		      mysql_select_db($this->dbname);
			  mysql_query("SET NAMES UTF8");
			  //echo "connect OK";
	
			 return 1;
		    }
		  else {
		     echo "can not connect";
		     return 0;
		 }

	 } 
	 

	
	 
	 public function  getResult($query)
	 {
			
		$results = mysql_query($query,self::$link );
		if(!$results) {
		      echo mysql_error(self::$link );
			  return 0;
		}
		return $results;
		 
	 }
	 
	 
	  
	  
	  function insert_db($obj)
	  {
		if(!$obj) return 0;
		   $table_key=  key($obj);
		   $total =  count( $obj[$table_key]);
		   $index  =1;  
			foreach($obj as $table => $attr)
			   {  $query =  "INSERT INTO $table (";
				   foreach($attr as $key => $val)
					{
					  $query .= 	$key.","; 
					 if($index ==  $total )  { 
					 
					  $query = rtrim($query,',');
						$query .=")VALUES (";
					 }
					  $index++;
					}
					foreach($attr as $val){
					  $val = mysql_escape_string($val);
					  $query .=  "'$val'".",";
					  
					}
			   }
				$query = rtrim($query,',');
				$query .=")";
        
		
		//echo $query."<br/>";
		
	  $result  = $this->getResult($query);
	  if($result)
	       return 1;
	  else
	      return 0;
		  
	  } // end  insert_db
	  
   function delete_data_table($obj)
	  {
		  
		  if(!$obj) return 0;
		   $table_key=  key($obj);
		   $total =  count( $obj[$table_key]);
		   $index  =1;  
			foreach($obj as $table => $attr)
			   {  $query =  "DELETE  FROM $table where ";
				   foreach($attr as $key => $val)
					{
					  $query .=  $key."=".$val;
					}
					
			   }
			
		
	  $result  = $this->getResult($query);
	  if($result)
	       return 1;
	  else
	      return 0;
		  
		  
		  
	  }
	  
 
  
 function excerpt_content($content,$maxchars = 500   ){  
	      //$maxchars  = 500;
		 $content = substr($content, 0, $maxchars);
		$pos = strrpos($content, " ");
		if ($pos > 0) {
		$content = substr($content, 0, $pos);
		}
		return $content;
   } // end excerpt_content
 

	  

	  
	  
	

	 
} // end Class
?>