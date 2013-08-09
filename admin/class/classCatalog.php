<?php
include_once("classDatabase.php");

class catalog extends database {

    function catalog() {
        $this->connect_db();
        //$this->event_listener($event_message);
    }

    function event_listener($event_type = NULL, $param = 0) {
        $method = $event_type;
        $this->$method($param);
    }

// end event listener

    function get_catalog_table($param =NULL) {

        //print_r($param);

        if (!$showpage)
            $showpage = 1;

        $query = "select id from categories";
        $result = $this->getResult($query);
        $totalrow = mysql_num_rows($result);
        $pagesize = 5;  // แสดงจำนวนกระทู้ในแต่ละหน้า ในที่นี้จะแสดง 20 กระทู้
        $totalpage = (int) ($totalrow / $pagesize);  // หาจำนวนหน้าทั้งหมด
        // ถ้าจำนวนหน้าเป็นเศษให้ปัดขึ้นไป
        if (($totalrow % $pagesize) != 0) {
            $totalpage+= 1;
        }
        // หา record แรกที่จะแสดงของแต่ละหน้า
        if ($showpage) {
            $pageno = $showpage;
            $start = $pagesize * ($pageno - 1);
            //echo $showpage;
        } else {
            $pageno = 1;
            $start = 0;
        }

       /* $query = "SELECT categories.*  , categories_image.image_name
				 FROM  categories  
                                  JOIN categories_image
                                  ON  categories_image.catalog_name = categories.id 
                                   JOIN  image
                                   ON categories_image.image_name = image.image_id
				 ORDER BY id DESC
				 LIMIT $start,$pagesize ";*/
       
          $query = "SELECT categories.*  , categories_type.catalog_type_name
				 FROM  categories
                                 JOIN categories_type
                                 ON categories_type.id = categories.catalog_type
				 ORDER BY id DESC
				 LIMIT $start,$pagesize ";


        $result = $this->getResult($query);
        if ($result) {
            echo "<input type='hidden' name='currentPage' id='currentPage' value='" . $pageno . "' />";
            echo "<input type='hidden' name='totalPage' id='totalPage' value='" . $totalpage . "' />";
            ?>
            <a href="index.php?page=catalog&event_msg=get_form_catalog" class="btn btn-success" >Add News Catalog</a>
            <div class="head clearfix">
                <div class="isw-grid"></div>
                <h1>Categories table</h1>                               
            </div>
            <div class="block-fluid table-sorting clearfix">

                <table cellpadding="0" cellspacing="0" width="100%" class="table" id="tSortable">
                    <thead>
                        <tr>
                            <th width="3%"><input type="checkbox" name="checkall"class="checkall"  /></th>

                         
                            <th width="25%">Categories Name</th>
                              <th width="25%">Categories Type</th>
                            <th width="25%" style="display:none" >Description</th>
                            <th width="25%">Edit</th>


                        </tr>
                    </thead>
                    <tbody>

                        <?php while ($catalog = mysql_fetch_object($result)) { ?>
                            <tr>
                                <td><input type="checkbox" name="checkbox" class="select_row"  value="<?php echo $catalog->id; ?>" /></td>

                             
                                <td><?php echo $catalog->catalog_name; ?></td>
                                <td><?php echo $catalog->catalog_type_name; ?></td>
                                <td style="display:none" ><?php echo $this->excerpt_content($catalog->catalog_description); ?></td>
                                <td>
                                    <div class="controls">
                                        <a class="icon-pencil tip"
                                           href="index.php?page=catalog&event_msg=get_form_catalog&catalog_id=<?php echo $catalog->id; ?>"
                                           data-original-title="Edit"></a>
                                        <a class="icon-trash tip  remove_row_btn" href="<?php echo $catalog->id; ?>" 
                                           id="Catalog"  data-original-title="Remove"></a>
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
                        <button class="btn btn-success" type="button" id="select_action_btn" value="catalog_table" >Action</button>
                    </div>
                </div>
                
            </div>
           <!-- </div> -->  


            <?php
        }//  end while
    }

// end get catalog table

    function get_catalog_data($obj) {


        $query = "SELECT categories.*  ,categories_type.catalog_type_name  , categories_type.id as catalog_type_id	
               FROM  categories JOIN  categories_type
               ON  categories_type.id  =   categories.catalog_type
               WHERE categories.id ='" . $obj["catalog_id"] . "'
                   
               ";
        $result = $this->getResult($query);
        if ($result)
            $data = mysql_fetch_object($result);

        return $data;
    }

//  end get catalog data

    function get_catalog_image($obj) {
        $query = "SELECT categories_image.* ,image.image_name ,image.image_id
              FROM   categories_image  
              JOIN  categories ON  categories.id = categories_image.catalog_name
              JOIN  image ON  categories_image.image_name = image_id
              WHERE categories_image.catalog_name = '" . $obj->id . "'
              ";
        $result = $this->getResult($query);
        //echo $query."<br/>";
        $index = 0;
        if ($result)
            while ($img = mysql_fetch_object($result)):
            
                ?>
                <img id="<? echo $img->image_id; ?>" src="<?php echo IMAGE_PATH . $img->image_name; ?>" />
                <input  type="hidden"  name="catalog_image_id" class="catalog_image_id"  value="<?php echo $img->id ;?>" />
                <input class="catalog_img_list" type="hidden" value="<? echo $img->image_id; ?>" name="catalog-img" />
                <button id="catalog-img-<?php echo $index; ?>" class="btn btn-danger delete_catalog_image" type="button">Delete</button>
                <?php
                $index++;
            endwhile;
    }

// end get catalog image

   function get_form_catalog($obj) {

        $catalog = $this->get_catalog_data($obj);
        //$catalog_image = $this->get_catalog_image($catalog);
        // print_r($catalog_image);
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
            <a href="index.php?page=catalog&event_msg=get_catalog_table" class="btn" aria-hidden="true">View Catalog</a>  
            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>            
        </div>
    </div>
                
                
                



        <a href="index.php?page=catalog&event_msg=get_catalog_table" class="btn btn-success" >View Catalog</a>
        <div class="row-fluid">  <!-- begin  fourm box -->
            
            <form  action="#"  id="catalog_form" class="" >
                
                 
                <div class="span8">

                    <div class="head clearfix">
                        <div class="isw-documents"></div>
                        <h1>Add Catalog</h1>
                    </div>
                    <div class="block-fluid">                        


                        <div class="row-form clearfix">
                            <div class="span3">Catalog Name:</div>
                            <div class="span6"><input  name="catalog_name" value="<?php echo $catalog->catalog_name; ?> " type="text"></div>
                        </div> 


                        <div class="row-form clearfix">
                            <div class="span3">Textarea field:</div>
                            <div class="span9">
                           <!-- <textarea name="description" class='wysi' cols="60" rows="200" >
                            Some text in textarea field...</textarea>
                                -->
                                <div class="block-fluid" id="wysiwyg_container">

                                    <textarea id="editor01" name="catalog_description" style="height: 600px;"  rows="200">
                                        <?php echo $catalog->catalog_description; ?>

                                    </textarea>

                                </div>

                            </div>
                        </div>    



                        <div class="row-form clearfix">
                            <div class="span3">Show This Catalog </div>
                            <div class="span6">
                                <span class="checked">

                        <?php  if($catalog):
                             if ($catalog->show_status == 1) : ?>
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
                            <?php if($catalog): ?>
                                 <input type="submit" value="Update"  class="btn btn-success" />
                                 <input type="hidden" id ="form_action_type"   value="update_catalog" name="action_type" />
                                  <input type="hidden" id ="catalog_id"   value="<?php echo $catalog->id; ?>" name="catalog_id" />
                                 <?php  else: ?>
                                 <input type="submit" value="Submit"  class="btn btn-success" />
                               <?php  endif; ?>
                              
                        </div>  


                    </div>

                </div> <!-- end  fourm box -->



                <div class="span4">


                    <!----------------------------------begin eleminet --------------------->
                    <div class="head clearfix">
                        <div class="isw-list"></div>
                        <h1>Catalog Type</h1>
                    </div>
                    <div class="block-fluid">                        

                        <div class="row-form clearfix">        
                            <input type="button"   id="add_catalog_type_btn" 
                                   value="+Add New Category Type"  class=" btn btn-info" />
                        </div>
                        <div class="row-form clearfix" id="catalog_type_input">

                            <div class="span4">Catalogry type :</div>
                            <div class="span6"><input value="" type="text"  id="catalog_type_text" name="catalog_type_name">
                            </div>

                            <div class="span12"></div>

                            <div class="span6">
                                <input type="button"   id="insert_catalog_type" 
                                       value="Add New Category  Type"  class="btn btn-success" />
                            </div>

                        </div> 

                        <div class="row-form clearfix">

                            <div class="span5">Select Catalog Type:</div>
                            <div class="span7" id="list_catalog_type">
                             <?php $this->get_catalog_type($catalog); ?>

                            </div>
                        </div> <!--row-form clearfix -->


                    </div> <!-- end block-fluid -->


                    <div class="row-form clearfix">

                    </div>  

                    <div class="head clearfix">
                        <div class="isw-list"></div>
                        <h1>Catalog Image</h1>
                    </div>
                    <div class="block-fluid">     

                        <div class="row-form clearfix" id="show_catalog_img">  
                            <?php
                            if ($catalog)
                                $this->get_catalog_image($catalog);
                            ?>

                        </div>

                        <div class="row-form clearfix">
                      <!--  <input type="hidden" name="image_name" id="catalog_img_name" /> -->

                            <div class="span4">
                                <input type="button"   id="add_img_btn" 
                                       value="Add Catalog Image"  class="btn btn-success " />


                            </div>
                        </div>


                    </div> <!-- end block-fluid -->


                </div> <!-- end span4 -->

            </form>

        </div> <!--end row-fluid-->

        <div id="show_text"></div>


        <?php
    }

//  end  add catalog
       

    function get_catalog_type_table($data = NULL) {


        $query = "select * from categories_type order by  id DESC";
        $result = $this->getResult($query);
        ?>
        <!-- <a href="index.php?page=catalog&event_msg=add_catalog" class="btn btn-success" >Add News Catalog</a> -->
        
        <!-------------------------------- pop up  modal ------------------------------------>
       
          
        
        
        <div class="span8">
              <div style="display:none;" id="msg_popup_modal" class="modal hide fade in" tabindex="-1" role="dialog"
              aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="" aria-hidden="true">x</button>
                        <h3 id="myModalLabel">Edit catalog  type name</h3>
                    </div>
                    <div class="modal-body" id="msg_popup_modal_content">
                            <!--
                           <div class="span8 offset2 loading_bar" style="display: block;">
                                <h3><center> Loading...  </center></h3>
                                <div class="progress progress-striped active ">
                                    <div data-original-title="100%" class="bar tipb" style="width: 100%;"></div>
                                </div>
                            </div>  -->
                        
                          <div class="row-form clearfix">   
                             <div class="span4">Catalogry type :</div>
                            <div class="span6"><input value="" type="text"  id="edit_catalog_type_name" 
                                              name="catalog_type_name" id="catalog_type_name"  />
                                <input type="hidden" name="catalog_type_id"   id="catalog_type_id" />
                           </div>
                            
                        </div>
                        
                        
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success" aria-hidden="true" id="save_edit_btn" >Save</button>
                        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>       
                      
                    </div>
                </div
         </div>
            
            
            <div class="head clearfix">
                <div class="isw-grid"></div>
                <h1>Catalogry type table</h1>                               
            </div>
            <div class="block-fluid table-sorting clearfix">
                <table cellpadding="0" cellspacing="0" width="100%" class="table" id="tSortable">
                    <thead>
                        <tr>
                            <th  width="3%"><input type="checkbox" name="checkall"  class="checkall" /></th>
                            <th width="3%" style="display:none"  >ID</th>
                            <th width="25%">Catalog type name</th> 
                            <th width="25%">Edit</th>   
                            <th width="25%"  style="display:none" ></th>                                 
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        while ($type = mysql_fetch_object($result)) {
                            ?>
                            <tr>
                                <td><input type="checkbox" name="checkbox" class="select_row" value="<?php echo $type->id; ?>"  /></td>
                                <td style="display:none"  ><?php echo $type->id; ?></td>
                                <td><?php echo $type->catalog_type_name ?></td> 
                                <td>
                                    <div class="controls">
                                        <a class="icon-pencil tip edit_row_btn" id="edit_row_btn-<?php echo $type->id; ?>"   
                                            href="<?php echo $type->catalog_type_name; ?>" data-original-title="Edit"></a>
                                        <a class="icon-trash tip remove_row_btn" href="<?php echo $type->id; ?>"
                                            id="Catalog Type" data-original-title="Remove"></a>
                                    </div>

                                </td>
                                <td style="display:none" >


                                </td>                                
                            </tr>


                            <?php
                        } //end  while
                        ?>
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
                        <button class="btn btn-success" type="button" id="select_action_btn" value="catalog_type_table" >Action</button>
                    </div>
                </div>
            </div>  
            
        </div>
            
            
            <div class="span4">


                    <!----------------------------------begin element --------------------->
                    <div class="head clearfix">
                        <div class="isw-list"></div>
                        <h1>Catalog Type</h1>
                    </div>
                    <div class="block-fluid">                        

                        <div class="row-form clearfix">        
                            <input type="button"   id="add_catalog_type_btn" 
                                   value="+Add New Category Type"  class=" btn btn-info" />
                        </div>
                        <div class="row-form clearfix" id="catalog_type_input">

                            <div class="span4">Catalogry type :</div>
                            <div class="span6"><input value="" type="text"  id="catalog_type_text" name="catalog_type_name">
                            </div>

                            <div class="span12"></div>

                            <div class="span6">
                                <input type="button"   id="insert_catalog_type_row" 
                                       value="Add New Category  Type"  class="btn btn-success " />
                            </div>

                        </div> 

                  


                    </div> <!-- end block-fluid -->
            </div>   

            <?php
        }

// end  get catalog type table

        function get_catalog_type($data) {


            $query = "select * from categories_type  order by id DESC";
            $result = $this->getResult($query);
            ?>

            <select name="catalog_type" id="select_catalog_type">


                <?php
                if ($data->catalog_type_name) :
                    echo '<option value="'.$data->catalog_type_id . '" >' . $data->catalog_type_name . '</option> ';
                else:
                    echo '<option value="0">choose a option...</option>';
                endif;

                while ($type = mysql_fetch_object($result)) {
                    //$data[] = $entry;
                    ?>
                    <option value="<?php echo $type->id; ?>"><?php echo $type->catalog_type_name; ?></option>

                    <?php
                }
                echo "</select>";
            }

// end get product type

            function insert_catalog_type($data) {
           
                
                $query = "INSERT INTO  categories_type (catalog_type_name) VALUES ('" . $data["name"] . "')";
                $result = self::getResult($query);

                if ($result){
                    $func =  $data["display_page"];
                    $this->$func($data);
                }
                else
                    echo 0; 
                 
                 
            }

// add catalog type

            function insert_catalog($obj) {
                //print_r($obj["catalog_data"]);
                //echo $obj["catalog_data"]["calalog_name"];
                //$total =  count($obj[]);
                //$status = $this->insert_db($obj);
                $status = 0;

                $query = "INSERT INTO categories
                         (catalog_name,catalog_description,catalog_type,show_status)
                          VALUES ('" . $obj["categories"]["catalog_name"] . "',
                                  '" .mysql_real_escape_string($obj["categories"]["catalog_description"]). "',
                                  '" . $obj["categories"]["catalog_type"] . "',
                                  '" . $obj["categories"]["show_status"] . "'
                                  )";

                //echo $query;
                
                
                $result = $this->getResult($query);
                if (!$result) {
                    $status = 0;
                    return false;
                }
                else $status =1;

             
            if(isset($obj["categories"]["image"])):
                  
            
                $query = "SELECT max(id) as last_id FROM categories";
                $result = $this->getResult($query);
                if ($result)
                    $catalog_id = mysql_fetch_object($result);
                else
                    $status = 0;

                if (!$catalog_id)
                    return false;

                foreach ($obj["categories"]["image"]["catalog_img_name"] as $catalog_img) {
                    if($catalog_img != NULL):
                    $query = "INSERT INTO categories_image (catalog_name ,image_name)
                                VALUES ('" . $catalog_id->last_id . "', '" . $catalog_img . "')";
                    //echo $query."\n\r";    
                    $result = $this->getResult($query);
                        if ($result)
                            $status =  1;
                        else
                            $status =  0;
                      
                   endif;
                } // end  for
            
            
                endif;  
                

               if($status)
                   echo "<center><h3>Add New Catalog Complete</h3></center>";
               else
                   echo "<center><h3>Error : Can't add  Catalog</h3></center>";
                 
                 
            }

//  end  func  insert catalog
            
    function  update_catalog($obj)
    {
       // print_r($obj);
        
        $query ="UPDATE categories
                 SET  catalog_description = '".mysql_real_escape_string($obj["categories"]["catalog_description"])."' , 
                      catalog_name   = '".$obj["categories"]["catalog_name"]."' ,
                      catalog_type   = '".$obj["categories"]["catalog_type"]."' ,
                      show_status    = '".$obj["categories"]["show_status"]."'    
                      WHERE categories.id = '".$obj["categories"]["catalog_id"]."' 
                          ";
        
       
        //echo $query;
          $result = $this->getResult($query);
                    if ($result)
                         $this->update_catalog_image($obj);
                    else
                         echo 0;
        
    } //  end  update catalog
    
    function  update_catalog_image($obj)
    {    
        foreach ($obj["categories"]["image"]["catalog_img_name"] as $key => $image_name):
            
            if (isset ($obj["categories"]["image"]["catalog_img_id"][$key])) :  // if image have exist in  table  just update
             
                if($image_name != NULL):    
                 $query ="UPDATE categories_image 
                     SET  image_name = '".$image_name."'
                     WHERE categories_image.id  = '".$obj["categories"]["image"]["catalog_img_id"][$key]."'
                           AND  categories_image.catalog_name = '".$obj["categories"]["catalog_id"]."' ";
                  else:
                 
                       $query = "DELETE FROM categories_image
                                WHERE categories_image.id  =".$obj["categories"]["image"]["catalog_img_id"][$key]." ";
                  endif;

            elseif($image_name != NULL):
                 $query = "INSERT INTO categories_image (catalog_name ,image_name)
                  VALUES ('" .$obj["categories"]["catalog_id"]. "', '" .$image_name. "')";
                  
                  
            else :  // if image not exist on  catagories image table
                  $query ="";
                
            
            endif;
            
             $result = $this->getResult($query);
                       if ($result)
                            $status =  '<center><h3>Update catalog complete</h3></center>
                                

                                    ';
                        else
                            $status = "<center><h3>Error: Can't update content</h3></center>";
                       
          //echo $query."\n\r";
        endforeach;
        
             echo $status;       

        
    } //  edn  update catalog image

    function delete_image_catalog($obj) {
                $query = "DELETE FROM categories_image WHERE catalog_name ='" . $obj["catalog_id"] . "' ";
                $result = self::getResult($query);

                if ($result)
                    echo 1;
                else
                    echo 0;
            }
// end  delete image catalog
    function delete_catalog($obj){
        
        
        foreach ($obj["catalog_id"] as $catalog_id):
        $query = "DELETE FROM categories WHERE  categories.id ='".$catalog_id."' ";
        
       // echo $query;
         $result = $this->getResult($query);
                     if ($result)
                            $status =  '<center><h3>Update catalog complete</h3></center> ';
                        else
                            $status = "<center><h3>Error: Can't update content</h3></center>";
        endforeach;
        
    } //end  delete catalog 
    function  delete_catalog_type($obj)
    {  
      
         foreach ($obj["catalog_type_id"] as $catalog__type_id):
        $query = "DELETE FROM categories_type WHERE  categories_type.id ='".$catalog__type_id."' ";
        
        //echo $query."\r\n";
         $result = $this->getResult($query);
                     if ($result)
                            $status =  '<center><h3>Delete catalog type complete</h3></center> ';
                        else
                            $status = "<center><h3>Error: Can't update content</h3></center>";
        endforeach;
        
        
        
    } // end  delete catalog type
    
    function update_catalog_type($obj){
         $query ="UPDATE categories_type 
                 SET catalog_type_name ='".$obj["catalog_type"]["catalog_type_name"]."' 
                  WHERE id = '".$obj["catalog_type"]["id"]."'
                  ";
        
          $result = $this->getResult($query);
                     if ($result)
                            $this->get_catalog_type_table();
                        else
                            echo 0;
    } // end  update_catalogg_type
            
}
 //  end class
        ?>