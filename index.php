<!DOCTYPE html>
<html lang="en">
    <head>        
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />


        <title>Demo</title>

        <link href="css/bootstrap.css"  rel="stylesheet" type="text/css" /> 
        <link href="css/bootstrap-responsive.css"  rel="stylesheet" type="text/css" /> 
        <link href="css/style.css" rel="stylesheet" type="text/css" />


      

        <!-- <script type='text/javascript' src='js/jcarousel/lib/jquery.jcarousel.js'></script> -->
       
        
    <body>

        <!-- end header --> 

        <div class="container  g_background" >

            <header class="row ">
                <div class="navbar">
                    <nav class="navbar-inner">
                        <a href="index.php" class="brand brand-bootbus"><h1>Demo cms</h1></a>
                        <!-- Below button used for responsive navigation -->
                        <ul class="nav ">
                           <!-- <li><a href="index.php">Home</a></li>
                            <li><a href="article.php">Article</a></li> -->
                        </ul>

                    </nav>

                </div>

            </header> <!-- end header -->  


            <div class="row ">
          <?php include("function.php"); 
             $fontend = new fontend();
          
          ?>

                <div class="span3 black-gradiant-background nav-left"  >
                     
                    <div class="nav">
                         
                       <?php  $fontend->get_left_menu(); ?>
                    </div><!-- nav -->
                </div> <!-- end  span3 -->
                
               
                
                <div class="span9 main-content "  id="main-content">
                    
            
                
                    
                    <?php $fontend->get_catalog(); ?>
                 

                    
                     
                    </div> <!-- end  main-content -->

                </div> <!-- end  main-contnent -->

            </div>


        </div>



    </body>

    
    
      <script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.js'></script>
      <script type='text/javascript' src='js/bootstrap.js'></script>
      <script type='text/javascript' src='js/jscript.js'></script>
    
</html>

