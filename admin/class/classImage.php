<?php
@session_start();
include_once("classDatabase.php");

class image extends database {

    //public $path ;

    function image() {

        $this->connect_db();
    }

//  end construtor

    function add_image($obj = NULL) {


        //print_r($_FILES);

        foreach ($_FILES["images"]["error"] as $key => $error) {
            if ($error == UPLOAD_ERR_OK) {
                $img_name = $_FILES["images"]["name"][$key];



                $destination_path = getcwd() . DIRECTORY_SEPARATOR;
                if ($path)
                    $destination_path = $destination_path . $path;
                else
                    $destination_path = $destination_path . IMAGE_PATH;


                $target_path = IMAGE_PATH . basename($img_name);

                move_uploaded_file($_FILES["images"]["tmp_name"][$key], $target_path);


                $query = "insert into  image (image_name) value ('" . $img_name . "')";
                //echo $query;
                $result = $this->getResult($query);
                echo $result;
            }
        }


        /*
          $newId = $this->getLastID('image','image_id');
          $query = "insert into image (image_id,image_name,showStatus,image_desc)
          value('$newId','$image_name','$status','$image_desc')"; // add image to database

          $result =  $this->getResult($query);
          //echo $query;
          if($result){
          if($this->uploadImage($image_name,$temp)) // move file to Server
          return 1;
          else
          return 0;
          }
          else
          return  0;

         */
    }

    function get_select_image($obj =NULL) {

        $obj_array = $this->fetch_image($obj);  // get image from database and calculate page
        ?>

        <div class="span12">

            <div class="block-fluid tabs ui-tabs ui-widget ui-widget-content ui-corner-all">

                <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
                    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active">
                        <a href="#tabs-1">View All Image on Server</a></li>
                    <li class="ui-state-default ui-corner-top"><a href="#tabs-2">Uploade image to Server</a></li>

                </ul>                        

                <div class="ui-tabs-panel ui-widget-content ui-corner-bottom" id="tabs-1">
                    <div class="block gallery clearfix" id="list_select_img"  >

        <?php
        if ($obj_array["result"])
            while ($data = mysql_fetch_object($obj_array["result"])) :
                ?>    
                                <a  href="<?php echo IMAGE_PATH . $data->image_name; ?>" class="s_img"  >
                                    <img   id="<?php echo $data->image_id; ?> " src="<?php echo IMAGE_PATH . $data->image_name; ?>" ></a>

            <?php endwhile; ?>    

                     



                    </div>



                </div>                        

                <div class="ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide" id="tabs-2">

                    <div class="block-fluid">     

                        <div class="row-form clearfix" id="show_img">  
                            <img  class="thumb200" id="show_upload_img" />
                            <!--
                            <div class="progress progress-striped active" id="loading_bar">
                                  <div data-original-title="100%" class="bar tipb" style="width: 100%; display:none;"></div>
                            </div>
                            -->

                        </div>
                        <input type="hidden" name="image_name" id="catalog_img_name" />


                        <div class="row-form clearfix">        
                            <div class="span4 offset4">       
                                <div class="uploader">
                                    <input size="19" style="opacity: 0;" name="file_image" type="file" id="images" >
                                    <span style="-moz-user-select: none;" class="filename">No file selected</span>
                                    <span style="-moz-user-select: none;" class="action">Choose File</span><
                                </div>   
                            </div>
                            <div class="span6">
                                <input type="button"  id="upload_img_btn" 
                                       value="<?php echo $obj["image_type"];  ?>"  class="btn btn-success" />


                            </div>
                        </div>


                    </div> <!-- end block-fluid -->
                </div>


            </div>

        </div>
        </div>
        <?
    }
    
    
    function get_last_image($obj){
        $query = "select * from image
                 Order by image_id DESC
		 LIMIT 0,1";
        $result = $this->getResult($query);
        $data = mysql_fetch_object($result);
        ?>
            <?php if($obj["image_type"] == "Add Catalog Image" ||  $obj["image_type"] == "Add Product Image" ): ?>
                <img  id="<?php echo $data->image_id; ?>" src="<?php echo IMAGE_PATH .$data->image_name; ?>" />
                <input type='hidden' name='catalog-img' value='<?php echo $data->image_id; ?>' class='catalog_img_list' /> 
           
           <?php  else:   
                 echo IMAGE_PATH.$data->image_name;    
           endif;
        
        
    } // end 

    function get_image_table($obj) {
        $obj_array = $this->fetch_image($obj);  // get image from database and calculate page
        ?>

        <div class="span8 show_gallery">
            <div class="head clearfix">
                <div class="isw-picture"></div>
                <h1>Gallery</h1>
            </div>
            <div class="block  thumbs clearfix">

                <div class="span8 offset2 loading_bar" style="display:none;">
                    <h3><center> Loading...  </center></h3>
                    <div class="progress progress-striped active ">
                        <div data-original-title="100%" class="bar tipb" style="width: 100%;"></div>
                    </div>
                </div>



        <?php
        if ($obj_array["result"])
            while ($data = mysql_fetch_object($obj_array["result"])) :
                ?>



                        <div  class="thumbnail">
                            <a class="fancybox" rel="group" 
                               href="<?php echo IMAGE_PATH . $data->image_name; ?>">
                                <img src="<?php echo IMAGE_PATH . $data->image_name; ?>" class="img-polaroid" alt="text">

                            </a>
                            <div class="caption">
                                <span>
                                    <p><?php echo $data->image_name ;?> </p>
                                    <input  name="show_status" class="show_status"  id="image_id-<?php echo $data->image_id; ?>"
                                            value="<?php echo $data->image_name; ?>"  type="checkbox" >
                                </span>
                                <a  class="icon-trash tip delete_image" href="<?php echo $data->image_name; ?>"
                                    id="<?php echo $data->image_id; ?>" data-original-title="Remove">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Remove</a>
                            </div>
                        </div>   


            <?php endwhile; ?>

                <!-- 
                 <a  class="fancybox" rel="group" href="img/example_full.jpg">
                 <img src="img/example.jpg" class="img-polaroid" ></a>
                <!-- <a style="margin: 9px;" class="fancybox" rel="group" href="img/example_full.jpg">
                 <img src="img/example.jpg" class="img-polaroid"></a>-->
        <?php if ($obj_array["total_row"]): ?>    

                    <!------------------       Image Tool      ----------------------->
                    <div class="row-form clearfix">
                        <div class="span1">Select all </div>

                        <span class="checked">
                            <input  name="show_status" id="select_all_img" 
                                    value=""  type="checkbox" >
                        </span>

                    </div>


                    <div class="row-form clearfix">
                        <div class="span3 ">
                            <select name="select_action" id="select_action">
                                <option>..Bluk Action..</option>
                                <option value="delete">Delete</option>
                            </select>
                        </div>
                        <div class="span2"> 
                            <button class="btn btn-success" type="button" id="select_action_btn" value="image_table" >Action</button>
                        </div>
                    </div>
                    <!-----------------------------end Image Tool  ----------------------------------------- -->
        <?php endif; ?>     


            </div> <!-- end thumb -->




        </div><!-- end  span12 -->


        <?php if ($obj["query_type"] != "image_only"): ?>
            <div class="span4">

                <div class="head clearfix">
                    <div class="isw-list"></div>
                    <h1>Image Upload Preview</h1>
                </div>
                <div class="block-fluid">     
                    <form  id="form_upload_image">
                        <div class="row-form clearfix">  
                            <img  id="show_upload_img" />

                        </div>
                        <input type="hidden" name="image_name" />


                        <div class="row-form clearfix">        
                            <div class="span6">       
                                <div class="uploader">
                                    <input size="19" style="opacity: 0;" name="file_image" type="file" id="images" >
                                    <span style="-moz-user-select: none;" class="filename">No file selected</span>
                                    <span style="-moz-user-select: none;" class="action">Choose File</span><
                                </div>
                            </div>
                            <div class="span3 pull-right">
                                <input type="submit" value="Upload" class="btn btn-success" /> 
                            </div>         
                        </div>



                        <form>   


                            </div> <!-- end block-fluid -->
                            </div>



        <?php
        endif;
    }

//  end  get iamge table

    function uploadImage($img, $temp, $path = NULL) {
        $destination_path = getcwd() . DIRECTORY_SEPARATOR;
        if ($path)
            $destination_path = $destination_path . $path;
        else
            $destination_path = $destination_path . IMAGE_PATH;

        if (isset($img)) {
            $target_path = $destination_path . basename($img);
            //echo $target_path;
            if (@move_uploaded_file($temp, $target_path)) {
                //echo "move ok";
                $status = 1;
            }
            else
                $status = 0;
        }
        else {
            $status = 0;
        }

        return $status;
    }

//  end addImage 

   

    function deleteImage($param_array = NULL) {

        //print_r($param_array);
        $status = 0;
        foreach ($param_array["image"] as $data) {
            $query = "delete from image where image_id='" . $data["image_id"] . "'";

            $result = $this->getResult($query);
            if ($result) {
                if (unlink(IMAGE_PATH . $data["image_name"]))
                    $status = 1;
                else
                    $status = 0;
            }
            else
                $status = 0;
        } //  end for

        echo $status;
    }

//*****************************************************

    

    function fetch_image($obj = NULL) {
        $query = "select image_id from image";

        $result = $this->getResult($query);
        $totalrow = mysql_num_rows($result)
        ;
        //if(!$totalrow ) return 0;
        $numItem = 100;

        if ($type == "formImage")
            $pagesize = 20;
        else if ($type == "headerImage")
            $pagesize = 10;
        else
            $pagesize = $numItem;  // แสดงจำนวนกระทู้ในแต่ละหน้า ในที่นี้จะแสดง 20 กระทู้
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

        //------------------------------------------------------------------------
        $query = "select * from image
                 Order by image_id DESC
		 LIMIT $start,$pagesize ";

        $result = $this->getResult($query);
        $index = 0;  // index

        $obj_array = array("start_page" => $start,
            "page_size" => $pagesize,
            "total_page" => $totalpage,
            "show_page" => $showpage,
            "result" => $result,
            "total_row" => $totalrow
        );
        return $obj_array;
    }

//  end  fetch image
}

//  end  class define
?>