<?

include_once("classDatabase.php");

class product extends database
{
	
	function  product()
	{
	    $this->connect_db();
	}
 
 
 


  


function fetch_product($obj)
{ 
  if(!$showpage)
       $showpage  = 1;
    $query = "select product_id from product";
    $result = $this->getResult($query);
    $totalrow  = mysql_num_rows($result);
    $pagesize = 5; 
    $totalpage = (int)($totalrow/$pagesize);  
		// ถ้าจำนวนหน้าเป็นเศษให้ปัดขึ้นไป
   if(($totalrow%$pagesize)!=0){
		$totalpage+= 1;
		} 
		// หา record แรกที่จะแสดงของแต่ละหน้า
	if($showpage){
		$pageno = $showpage;
		$start = $pagesize*($pageno-1);
		//echo $showpage;
		}
	else{
		$pageno = 1;
		$start = 0;
        }
  
   $query = "select product.* , categories.catalog_name  as catalog_name ,image.image_name
             from product join categories 
             on product.catalog_name = categories.id
             left join image on product.product_image = image.image_id
             group by product_id
             order by product_id DESC
	     LIMIT $start,$pagesize ";
			
   $result =   $this->getResult($query);
   
   return  $result;
   
} //  end getProduct


function  get_product_data($obj)
{
    
       $query = "select product.* , categories.catalog_name ,categories.id as catalog_id ,image.image_name ,  image.image_id
             from product join categories 
             on product.catalog_name = categories.id
             left join image on image.image_id =  product.product_image
             where product.product_id = '".$obj["product_id"]."' 
             order by product_id DESC
	     LIMIT 0,1 ";
			
     $result = $this->getResult($query);
        if ($result)
            $data = mysql_fetch_object($result);

        return $data;
    
}


function get_product_table($obj)
{
     $result = $this->fetch_product($obj);
   
     if ($result) {
         
            echo "<input type='hidden' name='currentPage' id='currentPage' value='" . $pageno . "' />";
            echo "<input type='hidden' name='totalPage' id='totalPage' value='" . $totalpage . "' />";
            ?>
            <a href="index.php?page=product&event_msg=get_form_product" class="btn btn-success" >Add News product</a>
            <div class="head clearfix">
                <div class="isw-grid"></div>
                <h1>Sortable table</h1>                               
            </div>
            <div class="block-fluid table-sorting clearfix">

                <table cellpadding="0" cellspacing="0" width="100%" class="table" id="tSortable">
                    <thead>
                        <tr>
                            <th width="3%"><input type="checkbox" name="checkall"class="checkall"  /></th>

                         
                            <th width="25%">Products Name</th>
                              <th width="25%">Categories</th>
                               <th width="25%">Products Description</th>
                              <!--  <th width="25%">Products Image</th> -->
                           
                            <th width="25%">Edit</th>


                        </tr>
                    </thead>
                    <tbody>

                        <?php while ($product = mysql_fetch_object($result)) { ?>
                            <tr>
                                <td><input type="checkbox" name="checkbox" class="select_row"  value="<?php echo $product->product_id; ?>" /></td>

                                
                                <td><?php
                                   
                                     echo $product->product_name; 
                                ?></td>
                                <td><?php echo $product->catalog_name; ?></td>
                                <td ><?php echo $this->excerpt_content($product->product_description); ?></td>
                             
                                <td>
                                    <div class="controls">
                                        <a class="icon-pencil tip"
                                           href="index.php?page=product&event_msg=get_form_product&product_id=<?php echo $product->product_id; ?>"
                                           data-original-title="Edit"></a>
                                        <a class="icon-trash tip  remove_row_btn" href="<?php echo $product->product_id;  ?>" 
                                           id="product"  data-original-title="Remove"></a>
                                    </div>
                                </td> 


                            </tr>
                        <?php } ?>		

                    </tbody>
                </table>
                  <div class="row-form clearfix">
                    <div class="span3 ">
                        <select name="select_action" id="select_action">
                            <option>..Bluk Action..</option>
                            <option value="delete" >Delete</option>
                        </select>
                    </div>
                    <div class="span2"> 
                        <button class="btn btn-success" type="button" id="select_action_btn" value="product_table" >Action</button>
                    </div>
                </div>
                
            </div>
          


            <?php
        }//  end while
    
}  //  end get product table

function get_catalog_list($data){
    
    $query = "select catalog_name ,id   from categories";
    $result = $this->getResult($query);
    
     ?> <select name="catalog_name" id="select_catalog"> <?php
      if ($data->catalog_name) :
                    echo '<option value="'.$data->catalog_id . '" >' . $data->catalog_name . '</option> ';
                else:
                    echo '<option value="0">Choose a option...</option>';
                endif;
        if ($result):
          
              while($data = mysql_fetch_object($result)):
                 ?>
                    <option value="<?php echo $data->id; ?>"><?php echo $data->catalog_name; ?></option>

                <?php
            
              endwhile;
       endif;  
       echo "</select>";
}

function  get_form_product($obj)
{
      $product  =  $this->get_product_data($obj);
   ?>   
     <!-------------------------------- pop up image  modal ------------------------------------>
        <div style="display: none;" id="image_popup_modal" class="modal hide fade" tabindex="-1" role="dialog" 
             aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel">Modal form</h3>
            </div>        
            <div class="row-fluid">
                <div class="block-fluid" id="show_image_popup" >

                </div>                
            </div>                    
            <div class="modal-footer">
               
                <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>            
            </div>
        </div>
          <!-------------------------------- pop up  modal ------------------------------------>
         <div style="display:none;" id="msg_popup_modal" class="modal hide fade in" tabindex="-1" role="dialog"
              aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="" aria-hidden="true">x</button>
            <h3 id="myModalLabel">Status Message</h3>
        </div>
        <div class="modal-body" id="msg_popup_modal_content">
           
               <div class="span8 offset2 loading_bar" style="display: block;">
                    <h3><center> Loading...  </center></h3>
                    <div class="progress progress-striped active ">
                        <div data-original-title="100%" class="bar tipb" style="width: 100%;"></div>
                    </div>
                </div>
        </div>
        <div class="modal-footer">
            <a href="index.php?page=product&event_msg=get_product_table" class="btn" aria-hidden="true">View product</a>  
            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>            
        </div>
    </div>
                
                

        <a href="index.php?page=product&event_msg=get_product_table" class="btn btn-success" >View product</a>
        <div class="row-fluid">  <!-- begin  fourm box -->
            
            <form  action="#"  id="product_form" class="" >
                
                 
                <div class="span8">

                    <div class="head clearfix">
                        <div class="isw-documents"></div>
                        <h1>Add product</h1>
                    </div>
                    <div class="block-fluid">                        


                        <div class="row-form clearfix">
                            <div class="span3">product Name:</div>
                            <div class="span6"><input  name="product_name" value="<?php echo $product->product_name; ?> " type="text"></div>
                        </div> 


                        <div class="row-form clearfix">
                            <div class="span3">Textarea field:</div>
                            <div class="span9">
                           <!-- <textarea name="description" class='wysi' cols="60" rows="200" >
                            Some text in textarea field...</textarea>
                                -->
                                <div class="block-fluid" id="wysiwyg_container">

                                    <textarea id="editor01" name="product_description" style="height: 600px;"  rows="200">
                                        <?php echo $product->product_description; ?>

                                    </textarea>

                                </div>

                            </div>
                        </div>    



                        <div class="row-form clearfix">
                            <div class="span3">Show This product </div>
                            <div class="span6">
                                <span class="checked">

                        <?php  if($product):
                             if ($product->show_status == 1) : ?>
                                   <input  name="show_status" id="show_status"  checked="checked" type="checkbox" >
                        <?php else:  ?>  
                               <input  name="show_status" id="show_status" type="checkbox" >

                        <?php endif; 
                          else:  
                               echo '<input  name="show_status" id="show_status"  checked="checked" type="checkbox" >';
                          endif; 
                          
                        
                        ?>
                                                        
                                                        

                                </span>
                            </div>
                        </div>        




                        <div class="row-form clearfix">
                            <?php if($product): ?>
                                 <input type="submit" value="Update"  class="btn btn-success" />
                                 <input type="hidden" id ="form_action_type"   value="update_product" name="action_type" />
                                  <input type="hidden" id ="product_id"   value="<?php echo $product->product_id; ?>" name="product_id" />
                                 <?php  else: ?>
                                 <input type="submit" value="Submit"  class="btn btn-success" />
                               <?php  endif; ?>
                              
                        </div>  


                    </div>

                </div> <!-- end  fourm box -->



          <div class="span4">
              
              
              <div class="head clearfix">
                        <div class="isw-list"></div>
                        <h1>Catalogry</h1>
                    </div>
                    <div class="block-fluid">                        

                      
                        <div class="row-form clearfix" id="catalog_type_input">

                            <div class="span4">Catalogry:</div>
                            <div class="span6"><input value="" type="text"  id="catalog_type_text" name="catalog_type_name">
                            </div>

                            <div class="span12"></div>

                        </div> 

                        <div class="row-form clearfix">

                            <div class="span5">Select Categories:</div>
                            <div class="span7" id="list_catalog_type">
                             <?php $this->get_catalog_list($product); ?>

                            </div>
                        </div> <!--row-form clearfix -->


                    </div>
              
              
              
              
               <div class="head clearfix">
                        <div class="isw-list"></div>
                        <h1>Product Image</h1>
                    </div>
                    <div class="block-fluid">                        
                        <div class="row-form clearfix" id="show_catalog_img">  
                         <?php if($product->product_image): ?>
                           <img id="107 " src="<?php echo IMAGE_PATH.$product->image_name; ?>" />
                           <input class="catalog_img_list" type="hidden" value="<?php echo $product->product_image; ?>" 
                                  name="catalog-img" />
                           <button id="catalog-img-0" class="btn btn-danger delete_catalog_image" type="button">Delete</button>
                          <?php endif; ?>
                        </div>

                        <div class="row-form clearfix">
                     

                            <div class="span4">
                                <input type="button"   id="add_img_btn" 
                                       value="Add Product Image"  class="btn btn-success " />


                            </div>
                        </div>


                    </div> <!-- end block-fluid -->
                     

                </div> <!-- end span4 -->
              
             
         
            </form>

        </div> <!--end row-fluid-->

     <?
    
    
} //  end  get form  product





function insert_product($obj)
{
    $query =" INSERT INTO  product (

            product_name ,
            product_description ,
            catalog_name ,
            product_image ,
            show_status 
            )
    VALUES (
          '".$obj["product"]["product_name"]."', 
           '".mysql_real_escape_string($obj["product"]["product_description"])."',
           '".$obj["product"]["catalog_name"]."',
           '".$obj["product"]["image"]["img_name"][0]."',
           '".$obj["product"]["show_status"]."'  
     ) ";
    
 //  echo $query;
     $result = $this->getResult($query);
         if($result)
                   echo "<center><h3>Add New Product Complete</h3></center>";
               else
                   echo "<center><h3>Error : Can't add  Product </h3></center>";
                 
    
} //end indsert product


function update_product($obj)
{
    
    $query ="UPDATE product
                 SET  product_description =  '".mysql_real_escape_string($obj["product"]["product_description"])."',
                      product_name   =  '".$obj["product"]["product_name"]."',
                      catalog_name   =  '".$obj["product"]["catalog_name"]."',
                      product_image =   '".$obj["product"]["image"]["img_name"][0]."',
                      show_status    =   '".$obj["product"]["show_status"]."'     
                      WHERE product_id = '".$obj["product"]["product_id"]."' 
                          ";
        
       
       // echo $query;
         $result = $this->getResult($query);
                    if ($result)
                        echo "<center><h3> Update Product Complete</h3></center>";
                     else
                   echo "<center><h3>Error : Can't Update  Product </h3></center>";
       
    
    
} //  end update product


function delete_product($obj){
   //echo  $this->delete_data_table($obj);
   
     foreach($obj["product_id"] as $id )
      $query = "delete from product where product_id ='".$id."' ";
    //echo $query;
$result =   $this->getResult($query);
    if($result) 
	    echo 1;
    else
	    echo 0;
    //print_r($obj);
}



	
	
	
} //  end class define


?>