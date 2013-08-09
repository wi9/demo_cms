<?php
   include("admin/class/classDatabase.php");

  // include("class/classProduct.php");
   error_reporting(E_ERROR | E_WARNING | E_PARSE);
   
  
   
 
        
   class fontend extends database
   {
       function  fontend ()
       {
           $this->connect_db();
           
       }
       
     function get_left_menu()  // get catalog  menu link
     {
         
        $query = "SELECT categories.*  ,categories_type.catalog_type_name  , categories_type.id as catalog_type_id	
               FROM  categories JOIN  categories_type
               ON  categories_type.id  =   categories.catalog_type
               Group by categories.id
               order by categories_type.id  , categories.catalog_name 
              
                
               ";
        $result = $this->getResult($query);
        if ($result)
          while($data = mysql_fetch_object($result)):  
                  
                    $catalog[$data->catalog_type_name][$data->id] = $data->catalog_name ;
                       
          endwhile;
           echo '<ul class="nav left-menu ">
                    <h3>Products</h3>
                   ';
                      
          foreach(   $catalog as $catalog_type => $catalog){
                 //  echo $catalog_type."<br/>";
                   echo '<li class="parent-menu" ><a class="parent-menu" href="#">'.$catalog_type.'</a></li>';
                         
                 foreach ($catalog as $catalog_id => $catalog_name)
                    echo '<li><a href="'.$catalog_id.'">'.$catalog_name.'</a></li>';
          } 
          
          echo "</ul>";
          
     } // end get_left_menu
     
     
     function get_catalog($obj =NULL)
     {   
         
         if($obj["data_id"]){
           $query = "SELECT categories.*  ,categories_type.catalog_type_name  , categories_type.id as catalog_type_id	
                   FROM  categories JOIN  categories_type
                   ON  categories_type.id  =   categories.catalog_type
                   WHERE categories.id  = '".$obj["data_id"]."';
                   ";
         }
       else { $query = "SELECT categories.*  ,categories_type.catalog_type_name  , categories_type.id as catalog_type_id	
                   FROM  categories JOIN  categories_type
                   ON  categories_type.id  =   categories.catalog_type
                   order by categories.id  DESC
                   LIMIT 0,1
                   ";
           
           }
           
          // echo $query;
            $result = $this->getResult($query);
            if ($result)
            {
               $data = mysql_fetch_object($result); ?>
               
                <div id="show-content">
                               <div class="image-content">

                                        <div class="fadein">
                                             <?php $this->get_catalog_image($data->id); ?>
                                         
                                          </div>

                                </div>
                                    <div class="description-content" >
                                        <div class="description" >
                                            <h4><?php echo $data->catalog_name ; ?></h4>
                                            <p>
                                               <?php echo $data->catalog_description; ?>

                                            </p>
                                        </div>

                                    </div> <!-- end  description content -->
                </div>

                 <?php
                $this->get_product($data->id); // get product of catalog 
            }
         
         
     } //  end  get catalog
     
     
     function get_catalog_image($catalog_id)
     {
         $query = "select categories_image.image_name ,  image.image_name as catalog_image  
                  from  categories_image 
                  join  image 
                  on   image.image_id = categories_image.image_name
                  where categories_image.catalog_name = '".$catalog_id."' " ;
        // echo $query;
           $result = $this->getResult($query);
            if ($result)
            {  while($image = mysql_fetch_object($result)):
                     echo '<img src="'.FONTEND_IMAGE_PATH.$image->catalog_image. '" />';
               endwhile;
            }
           
     } //  end get catalog image
     
     function  get_product($catalog_id)
     {
           $query = "select product.* , 
                   image.image_name from product
                   left join  image 
                  on   image.image_id = product.product_image
                  where product.catalog_name = '".$catalog_id."' 
                  ORDER BY product.product_id DESC ";
                  
           
         // echo  $query;
             $result = $this->getResult($query);
            if ($result)
            {    $total_product = 1;      ?>
                  <div class="row no-space">
                        <div class="span9 ">
                           <div class="carousel jcarousel " id="myCarousel">
                        <div class="carousel-inner">
                            <div class="item active">
                                  <div class="row">
                  
                 <?php while($product = mysql_fetch_object($result)): ?>
                     
                                 <div class="span2">
                                        <div class="product-menu-list">
                                            <a class="product-menu-list-btn" href="<?php echo $product->product_id;  ?>">
                                                <img src="<?php echo FONTEND_IMAGE_PATH.$product->image_name ;?>" alt="">
                                            <div><p><?php echo $product->product_name; ?></p></div>
                                            </a>
                                        </div>
                                    </div>
                 
               <?php 
               $total_product++;
               endwhile; ?>
                                        <!-- <div class="span2">
                                        <div class="product-menu-list">
                                            <a href=""><img src="img/product/38635.jpg" alt="">
                                            <div><p>text</p></div>
                                            </a>
                                        </div>
                                    </div>  -->
                                   

                                    </div> <!-- end row -->
                                </div> <!-- end  item --> 
                            </div> <!-- end carousel-inner -->
                           <?php if($total_product > 5): ?>
                            <a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
                            <a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>
                           <? endif; ?>
                        </div> <!-- end carousel jcarousel -->

                        </div>
                    </div>   
                <?php  
                  
            }
         
         
     } //  end  get product
     function get_product_detail($obj)
     {
          $query = "select product.* , 
                   image.image_name from product
                   left join  image 
                  on   image.image_id = product.product_image
                  where product.product_id = '".$obj["data_id"]."' 
                 ";
         //echo $query;
          $result = $this->getResult($query);
            if ($result)
                $data= mysql_fetch_object ($result);
            ?>

                       <div class="image-content">

                           <img  class="product_image" src="<?php  echo FONTEND_IMAGE_PATH.$data->image_name;?>" />

                                </div>
                                    <div class="description-content" >
                                        <div class="description" >
                                            <h4><?php echo $data->product_name ; ?></h4>
                                            <p>
                                               <?php echo $data->product_description; ?>

                                            </p>
                                        </div>

                                    </div> <!-- end  description content -->
                   <?php
            
            
     }
       
       
   } //  end class
        
        
   
  ?>