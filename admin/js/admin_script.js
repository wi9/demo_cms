// JavaScript Document
$(function()
{
     init(); // inital setting 
    
 $("#product_form").on("submit",function(e){
     e.preventDefault();
      submit_form_product($(this));
 });

   $("#catalog_form").on("submit", function(e) {
        e.preventDefault();
        

        var data_array = $(this).serializeArray();

        var catalog_data = {};

        for (var i = data_array.length; i--; ) {
            catalog_data[data_array[i].name] = data_array[i].value;
           
        }// end for
       
     
         if( catalog_data["catalog_name"] == " "  || catalog_data["catalog_name"] == null)
               {  
                  alert("Please complete all text fields !");
                  return false;
               }

           if(!catalog_data["catalog_type"] || catalog_data["catalog_type"] == '0' )
             {
                 alert("Please select Catalog type!");
                 return false;
             }
          
        
        catalog_data["show_status"] = $("#show_status").is(':checked') ? 1 : 0;
         
         var catalog_img = {} , catalog_img_id = {},catalog_img_delete={};
         var  i =0 , j=0 , k=0;
         $(".catalog_img_list").each(function(data){
            catalog_img[i] = $(this).val();
            i++;
         });
         
         $(".catalog_image_id").each(function(data){
             catalog_img_id[j] = $(this).val();
             j++
         });
         
        /* $(".delete_catalog_img_list").each(function(data){
            catalog_img_delete[k] = $(this).val();
            k++;
         });*/
         
         catalog_data["image"] = {"catalog_img_name" : catalog_img ,
                                  "catalog_img_id": catalog_img_id 
                                  
                                 };
         
        console.log(catalog_data);
        //delete catalog_data["select"];
        delete catalog_data["catalog_type_name"] // no need catalog type name edit
        delete catalog_data["catalog-imge"]  // delete temp name
        
         var  event_msg = $("#form_action_type").val(); // get  form type for insert or update
         if(!event_msg)
             event_msg=  "insert_catalog";
         
         
     
          
         $.ajax({
            url: "ajax_handle.php?class=catalog&event_msg="+event_msg,
            type: "POST",
            data: {categories: catalog_data},
            beforeSend: function(xhr) {
                xhr.overrideMimeType("text/plain; charset=x-user-defined");
                $("#msg_popup_modal").modal('show');
            }
        }).done(function(data) {
            $("#msg_popup_modal_content").empty().html(data);
            
        });  


   });  //  end  submit form


 
    onclick_delete_image();
    set_edit();

});  // end  ready function

function init(){

   $(".navigation li a").on("click" ,function(e){
      // e.preventDefault();
       //alert("c");
       $(".navigation li").removeClass("active");
       $(this).parent().addClass("active");
   });
    
    $("#add_catalog_type_btn").on("click", function() {
        $("#catalog_type_input").toggle();
    });

    $("#insert_catalog_type").on("click", function() {
        var text = $("#catalog_type_text").val();
        if (text)
            insert_catalog_type(text,"get_catalog_type");  // @param : text , page to display result
        else
            alert("Please add catalog  type name");
    });
    
    $("#insert_catalog_type_row").on("click", function() {
        var text = $("#catalog_type_text").val();
        alert("click");
        if (text)
            insert_catalog_type(text,"get_catalog_type_table");  // @param : text , page to display result
        else
            alert("Please add catalog  type name");
    });
    
    
     $("#delete_img_btn").on("click", function(e) {
        e.preventDefault();
        $("#show_catalg_img").attr("src", "");
        $(this).hide();

    });

    $("#upload_img_btn").click(function(evt) {
        upload_image();
    });


    $("#add_img_btn").on("click", function(e) {
        e.preventDefault();
       // var img_type  = "Insert image to Catalog" ;
       
        var img_type  = $(this).val();
        get_image_popup(img_type);

    });

    $("#form_upload_image").on("submit", function(e) {
        e.preventDefault();
        upload_image();
    });

    $("#images").on("change", function(e) {
        preview_image();
    });
   
   $(".remove_row_btn").on("click",function(e)
   {    e.preventDefault();
        var table = $(this).attr("id");
        if(confirm("Do you want to delete this "+table +" ?")){
            
             var array_data = new Array($(this).attr("href")) ;
                 
                 
            switch(table){
               
                case "Catalog" : delete_catalog(array_data); break;
                case "Catalog Type": delete_catalog_type(array_data);  break;
                case "product":  delete_row_data(array_data,"product"); break    
               
             }
                   
             
          $(this).parent().parent().parent().hide();
        }
   });
   
   $(".edit_row_btn").on("click",function(e)
   {      e.preventDefault();
            var catalog_type_name = $(this).attr("href");
            var str  =  $(this).attr("id");
            var data =  str.split("-");
            var catalog_type_id = data[1];
           
            $("#edit_catalog_type_name").val(catalog_type_name); 
            $("#catalog_type_id").val(catalog_type_id);
          $("#msg_popup_modal").modal("show");  
   });

   $("#save_edit_btn").on("click",function(e){
     
        update_catalog_type();
        
   });

    
} //end init


function set_edit() {
    // Url's for plugn
    $.cleditor.buttons.image.uploadUrl = 'image-upload.html';
    $.cleditor.buttons.image.imageListUrl = 'image-list.json';
    $.cleditor.defaultOptions.width = "100%";
    $.cleditor.defaultOptions.height = "600px";
    $("#editor01").cleditor();

}


function insert_catalog_type(data,display_page) {

    $.ajax({
        url: "ajax_handle.php?class=catalog&event_msg=insert_catalog_type",
        type: "POST",
        data: {name: data ,display_page: display_page},
        beforeSend: function(xhr) {
            xhr.overrideMimeType("text/plain; charset=x-user-defined");
        }
    }).done(function(data) {
   
        
       if( display_page == "get_catalog_type_table" ) {    
            $("#main-content").empty().html(data);
             init();
       }
       else 
        $("#list_catalog_type").empty().append(data);
        $("#catalog_type_input").hide(500);
    
    });

}
function showUploadedItem(source) {
    /*var list = document.getElementById("image-list"),
     li   = document.createElement("li"),
     img  = document.createElement("img");
     img.src = source;
     li.appendChild(img);
     list.appendChild(li);
     */

    $("#show_upload_img").attr("src", source);

}

function  preview_image() {


    if (ImageExist($("#images").val())) {
        alert(" Can not upload Image file name: " + $("#images").val() + " have exist on server");
        return false;
    }

    var input = document.getElementById("images");
    var i = 0, len = input.files.length, img, reader, file;

    for (; i < len; i++) {
        file = input.files[i];

        if (!!file.type.match(/image.*/)) {
            if (window.FileReader) {
                reader = new FileReader();
                reader.onloadend = function(e) {

                    showUploadedItem(e.target.result, file.fileName);
                }; //end onladend
                reader.readAsDataURL(file);
            } // edn  if
        }
    }

} // end preview image



function upload_image(img_type ,editor)
{
    if (ImageExist($("#images").val())) {
        alert(" Can not upload Image file name: " + $("#images").val() + " have exist on server");
        return false;
    }

    var input = document.getElementById("images"),
            formdata = false;


    /*if (window.FormData) {
     formdata = new FormData();
     //document.getElementById("btn").style.display = "none";
     }*/

    formdata = ieFormData();
    var image_name = $("#images").val()
    $("#catalog_img_name").val(image_name);


    //document.getElementById("response").innerHTML = "Uploading . . ."
    $("#loading_bar").show();
    var i = 0, len = input.files.length, img, reader, file;

    for (; i < len; i++) {
        file = input.files[i];

        if (!!file.type.match(/image.*/)) {
            if (window.FileReader) {
                reader = new FileReader();
                reader.onloadend = function(e) {

                    showUploadedItem(e.target.result, file.fileName);
                }; //end onladend
                reader.readAsDataURL(file);
            } // edn  if
            if (formdata) {
                formdata.append("images[]", file);
            }
        }
    }



    if (formdata) {
        $.ajax({
            url: "ajax_handle.php?class=image&event_msg=add_image",
            type: "POST",
            data: formdata,
            processData: false,
            contentType: false,
            cache: false,
            success: function(res) {
                $(".loading_bar").hide();
                $("#show_upload_img").attr("src", "");
                
               // if(img_type == "form_image" || img_type == "Add Catalog Image"  ){
                if(img_type){
                   insert_and_upload(img_type,editor);
                   $("#image_popup_modal").modal('hide');
                }
                 //data.editor.execCommand(data.command, $(this).children("img").attr("src"), null, data.button);
                //$("#delete_img_btn").show();
               get_all_image();

                $(".uploader span.filename").empty();
            }
        });
    }





} //  end  upload imgae


function ieFormData() {

    if (window.FormData == undefined)
    {
        this.processData = true;
        this.contentType = 'application/x-www-form-urlencoded';
        this.append = function(name, value) {
            this[name] = value == undefined ? "" : value;
            return true;
        }
    }
    else
    {
        var formdata = new FormData();
        formdata.processData = false;
        formdata.contentType = false;
        return formdata;
    }

}





function get_all_image()
{
    var query_type = "image_only";
    $.ajax({
        url: "ajax_handle.php?class=image&event_msg=get_image_table",
        type: "POST",
        data: {query_type: query_type},
        beforeSend: function(xhr) {
            xhr.overrideMimeType("text/plain; charset=x-user-defined");
        },
        beforeSend: function(xhr) {
            $(".loading_bar").show();
        },
                success: function(res) {
            $(".loading_bar").hide();
            //$("#delete_img_btn").show();
            $(".show_gallery").replaceWith(res);
            onclick_delete_image();
            //document.getElementById("response").innerHTML = res; 

        }
    });

}
function insert_and_upload(img_type,data){
    
     $.ajax({
        url: "ajax_handle.php?class=image&event_msg=get_last_image",
        type: "POST",
        data: {image_type: img_type},
        success: function(res) {
                // $("#show_image_popup").empty().html(res); 
                //alert(res);
                //if(img_type == "form_image" )
                    
                 
            if(img_type == "Add Catalog Image"){
                
                var count_img = $("#show_catalog_img").children("img").length;
                  var del_btn = "<button  id='catalog-img-" +count_img+ "' class='btn btn-danger delete_catalog_image' type='button'>Delete Image</button>";
                   $("#show_catalog_img").append(res).append(del_btn);
                    onclick_delete_image();   // bind event for button
                }
              else if(img_type == "Add Product Image"){
                   var count_img = $("#show_catalog_img").children("img").length;
                    var del_btn = "<button  id='catalog-img-0' class='btn btn-danger delete_catalog_image' type='button'>Delete Image</button>";
                   $("#show_catalog_img").empty().append(res).append(del_btn);
                    onclick_delete_image();   // bind event for button
                  
              }
               else
                   data.editor.execCommand(data.command, res, null, data.button);
               
              $("#image_popup_modal").modal('hide');
        }
    });
    
} //  end  


function  get_image_popup(img_type)
{
  
    $.ajax({
        url: "ajax_handle.php?class=image&event_msg=get_select_image",
        type: "POST",
        data: {image_type: img_type},
        success: function(res) {
                 

            $("#show_image_popup").empty().html(res);
            $("#image_popup_modal").modal('show');
            $(".tabs").tabs();


            $("a.s_img").on('click', function(ev) {
                ev.preventDefault();
                $("#image_popup_modal").modal('hide');
                //if(img_type == "catalog_img")
                var c_img = $(this).children("img");
                var count_img = $("#show_catalog_img").children("img").length;
                var img_src = c_img.attr("src");
                var img_id  = c_img.attr("id");

                src_array = img_src.split('/');
                img_src = src_array[src_array.length - 1];


                var img_name = "<input type='hidden' name='catalog-img' value='" + img_id + "' class='catalog_img_list' /> ";
               
               if(img_type == "Add Catalog Image" )  { 
                  var del_btn = "<button  id='catalog-img-" + count_img + "' class='btn btn-danger delete_catalog_image' type='button'>Delete Image</button>";
                  $("#show_catalog_img").append($(this).html()).append(img_name).append(del_btn);
               }
               else {
                    var del_btn = "<button  id='catalog-img-0' class='btn btn-danger delete_catalog_image' type='button'>Delete Image</button>";
                   $("#show_catalog_img").empty().append($(this).html()).append(img_name).append(del_btn);  
               }

                
                onclick_delete_image();   // bind event for button
      

                

            }); // end  click image

            $("#images").on("change", function(e) {
                preview_image();
            });

               $("#upload_img_btn").click(function(e) {
                e.preventDefault();
                upload_image(img_type);
            });


        }
    });

}


function onclick_delete_image()
{
    $("a.delete_image").on("click", function(e) {
        e.preventDefault();

        delete_image($(this));
    });

    $("#select_all_img").on("change", function(e) {
        if ($(this).attr("checked"))
            $(".show_status").attr("checked", "checked");
        else
            $(".show_status").attr("checked", false);
    });
    
    $(".checkall").on("change", function(e) {
        if ($(this).attr("checked"))
            $(".select_row").attr("checked", "checked");
        else
            $(".select_row").attr("checked", false);
    })
    
    $(".select_row").on("change", function(e) {
        if ($(this).attr("checked"))
              $(this).attr("checked", "checked");
        else
            $(this).attr("checked", false);
    });

    $("#select_action_btn").on("click", function(e) {
        e.preventDefault();
        var action_type , action_for_table;
         action_for_table = $(this).val();

        $("#select_action option:selected").each(function() {
            action_type = $(this).val();
        });
        // console.log(action_type);
        if (action_type == "delete") {
            
            var array_data =new Array();
           var i =0;
           console.log(action_for_table);
         
            switch (action_for_table){
            case "image_table":    delete_image(null); break;
            case "catalog_table": { 
                         if(confirm("Do you want to delete seleted catalog ?"))
                                {     
                                 $(".select_row").each(function(data)
                                 {   if($(this).attr("checked")) {
                                        array_data[i] = $(this).val();
                                        i++;
                                        $(this).parent().parent().remove();
                                     }
                                 }); 

                            delete_catalog(array_data) ;  
                            break;
                           }
            
                         } // end  catalog 
             case "catalog_type_table": {
                                
                                 if(confirm("Do you want to delete seleted catalog Type ?"))
                                {     
                                 $(".select_row").each(function(data)
                                 {   
                                     if($(this).attr("checked")) {
                                        array_data[i] = $(this).val();
                                        i++;
                                        $(this).parent().parent().remove();
                                     }
                                 }); 

                                delete_catalog_type(array_data) ;  
                                break;
                               }
             }
             
              case "product_table": {
                                
                                 if(confirm("Do you want to delete seleted products ?"))
                                {     
                                 $(".select_row").each(function(data)
                                 {   
                                     if($(this).attr("checked")) {
                                        array_data[i] = $(this).val();
                                        i++;
                                        $(this).parent().parent().remove();
                                     }
                                 }); 

                                delete_row_data(array_data) ;  
                                break;
                               }
             }
            } // edn swicth
                
            
        }
    });
    
    
    $(".delete_catalog_image").on("click", function(e) {
                    e.preventDefault();
                    //console.log("delete");
                   // if(confirm("Do You want to Delete this catalog image item ?"))
                    {
                        var src_id = $(this).attr("id");
                        var src_array = src_id.split('-');
                        var img_id = src_array[src_array.length - 1];
                        var img_data  =  $("#show_catalog_img").children("img:eq(" + img_id + ")");

                        var img_name =img_data.attr("src");
                        src_array = img_data.attr("src").split('/');
                        var img_src = src_array[src_array.length - 1];
                        var image = {"image_id" : img_data.attr("id") , "image_name": img_src };

                        // var  img_delete = '<input class="delete_catalog_img_list" type="hidden" value="'+img_data.attr("id")+'" name="detele-img" />';

                         $(".catalog_img_list:eq(" + img_id + ")").val("");


                        img_data.hide();
                        $(this).remove();
                    }
                   
                    

                });
    
    
}




function catalog_image_handle(image)
{   
   
    
    $.ajax({
        url: "ajax_handle.php?class=image&event_msg=deleteImage",
        type: "POST",
        data: {image: image},
        beforeSend: function(xhr) {
            xhr.overrideMimeType("text/plain; charset=x-user-defined");
        },
        success: function(status) {
            // $("#loading_bar").hide();
            //alert(res);
            //	if(status == 1)
            //element.parent().parent().remove();  // remove image
            //else
            //alert("Can not delete image");

        }


    });
}


function delete_image(element)
{

    var i = 0;
    var image_id = {}, image_name = {};
    var image = {};
    $("input.show_status").each(function() {
        if ($(this).attr("checked"))
        {
            str = $(this).attr("id"); //  image id
            image[i] = {"image_id": str.split("-")[1], "image_name": $(this).val()};

            i++;

        } //  end if

    }) //  end each

    console.log(image.length)


    if (element) {
        image[i] = {"image_id": element.attr("id"), "image_name": element.attr("href")};
    }


    if ($.isEmptyObject(image))
    {
        alert("Please select image for delete !");
        return false;
    }
    if (confirm("Do you want to delete this image ? "))
        $.ajax({
            url: "ajax_handle.php?class=image&event_msg=deleteImage",
            type: "POST",
            data: {image: image},
            beforeSend: function(xhr) {
                xhr.overrideMimeType("text/plain; charset=x-user-defined");
            },
            success: function(status) {
                // $("#loading_bar").hide();
                //alert(res);
                //	if(status == 1)
                //element.parent().parent().remove();  // remove image
                //else
                //alert("Can not delete image");
               // alert(status);
                if (status == 1)
                    get_all_image();


            }
        });


}



function ImageExist(filename)
{
    var file_path = "../img/img_content/"
    var img = new Image();
    img.src = file_path + filename;

    return img.height != 0;
}

/*------------------------- Catalog handle  -------------------------------*/
function add_catalog()
{
    
     $.ajax({
        url: "ajax_handle.php?class=image&event_msg=deleteImage",
        type: "POST",
        data: {image: image},
        beforeSend: function(xhr) {
            xhr.overrideMimeType("text/plain; charset=x-user-defined");
        },
        success: function(status) {
            // $("#loading_bar").hide();
            //alert(res);
            //	if(status == 1)
            //element.parent().parent().remove();  // remove image
            //else
            //alert("Can not delete image");
            
            $("#show_image_popup").empty().html(status);
            $("#image_popup_modal").modal('show');

        }
    
}); // end  ajax

}// end  add catalog 


function delete_row_data(param_data,table)  // detele row any table
{
    
    $.ajax({
        url: "ajax_handle.php?class=product&event_msg=delete_product",
        type: "POST",
        data: { product_id:param_data},
        beforeSend: function(xhr) {
            xhr.overrideMimeType("text/plain; charset=x-user-defined");
            $("#msg_popup_modal").modal('show');
        },
        success: function(data) {
            //$("#msg_popup_modal_content").empty().html(data);
           // alert(data);
           if(data != '1')
                alert("Error: Can not delete data");
               
        }
    });
    
}  // end  delete row


function  delete_catalog(catalog_data){
     
    // console.log(catalog_data);
     $.ajax({
        url: "ajax_handle.php?class=catalog&event_msg=delete_catalog",
        type: "POST",
        data: {catalog_id: catalog_data},
        beforeSend: function(xhr) {
            xhr.overrideMimeType("text/plain; charset=x-user-defined");
            $("#msg_popup_modal").modal('show');
        },
        success: function(data) {
            //$("#msg_popup_modal_content").empty().html(data);
           // alert(data);
        }

}); // end  ajax

    
} //   end  delete_Catalog


function  delete_catalog_type(catalog_data)
{
       $.ajax({
        url: "ajax_handle.php?class=catalog&event_msg=delete_catalog_type",
        type: "POST",
        data: {catalog_type_id: catalog_data},
        beforeSend: function(xhr) {
            xhr.overrideMimeType("text/plain; charset=x-user-defined");
            $("#msg_popup_modal").modal('show');
        },
        success: function(data) {
           // alert(data);
            //$("#msg_popup_modal_content").empty().html(data);
        
        }

}); // end  ajax
    
}

function update_catalog_type()
{        
      var catalog_data= {"catalog_type_name":  $("#edit_catalog_type_name").val(), 
                          "id" :  $("#catalog_type_id").val()};

        $.ajax({
        url: "ajax_handle.php?class=catalog&event_msg=update_catalog_type",
        type: "POST",
        data: {catalog_type: catalog_data},
        beforeSend: function(xhr) {
            xhr.overrideMimeType("text/plain; charset=x-user-defined");
            //$("#msg_popup_modal").modal('show');
        },
        success: function(data) {
          
           if(data != 0){
          
            $("#msg_popup_modal").modal('hide');
              $("#main-content").empty().html(data);
                init();
           }
        }
        });
    
    
} // endc function update


///------------------------------ product ------------------------------
function  submit_form_product(element)
{

  var data_array = element.serializeArray();

        var param_data = {};

        for (var i = data_array.length; i--; ) {
           param_data[data_array[i].name] = data_array[i].value;
           
        }// end for
        
           
     
         if( param_data["product_name"] == " "  || param_data["product_name"] == null)
               {  
                  alert("Please complete all text fields !");
                  return false;
               }


           if(!param_data["catalog_name"] || param_data["catalog_name"] == '0' )
             {
                 alert("Please select Catalog type!");
                 return false;
             }
          
        
        param_data["show_status"] = $("#show_status").is(':checked') ? 1 : 0;
         
         var  param_img = {} ,  param_img_id = {}, param_img_delete={};
         var  i =0 , j=0 , k=0;
         $(".catalog_img_list").each(function(data){
            param_img[i] = $(this).val();
            i++;
         });
         
         $(".catalog_image_id").each(function(data){
              param_img_id[j] = $(this).val();
             j++
         });
         
        /* $(".delete_catalog_img_list").each(function(data){
            catalog_img_delete[k] = $(this).val();
            k++;
         });*/
         
        param_data["image"] = {"img_name" :  param_img ,
                                "img_id":  param_img_id 
                                  
                                 };
         
        console.log(param_data);
        //delete catalog_data["select"];
        delete param_data["catalog_type_name"] // no need catalog type name edit
        delete param_data["catalog-imge"]  // delete temp name
        
         var  event_msg = $("#form_action_type").val(); // get  form type for insert or update
         if(!event_msg)
             event_msg=  "insert_product";
         
         
     
          
         $.ajax({
            url: "ajax_handle.php?class=product&event_msg="+event_msg,
            type: "POST",
            data: {product: param_data},
            beforeSend: function(xhr) {
                xhr.overrideMimeType("text/plain; charset=x-user-defined");
                $("#msg_popup_modal").modal('show');
            }
        }).done(function(data) {
            $("#msg_popup_modal_content").empty().html(data);
           // alert(data);
        });  

} //  end