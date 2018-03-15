<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  $back_link = "languages.php";
  
  if(isset($_POST['cancel'])) {
    header("Location: $back_link");
  }
  
  if(isset($_POST['submit'])) {
    
    //echo"<pre>";print_r($_POST);
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    $language_code = mysqli_real_escape_string($db_link, $_POST['language_code']);
    $language_name = mysqli_real_escape_string($db_link, $_POST['language_name']);
    $language_menu_name = mysqli_real_escape_string($db_link, $_POST['language_menu_name']);
    
    $language_menu_order = get_lаst_language_menu_order_value()+1;
    
    // if $language_menu_order == 1, that means this is the first entry,
    // so make this language default
    $language_is_default = ($language_menu_order == 1) ? 1 : 0;
    $language_is_active = 0;
    if(isset($_POST['language_is_active'])) $language_is_active = $_POST['language_is_active'];
    
    $query_insert_language = "INSERT INTO `languages`(`language_id`, 
                                                    `language_code`,  
                                                    `language_name`, 
                                                    `language_menu_name`, 
                                                    `language_menu_order`, 
                                                    `language_is_default_frontend`, 
                                                    `language_is_default_backend`, 
                                                    `language_is_active`) 
                                            VALUES (NULL,
                                                    '$language_code',
                                                    '$language_name',
                                                    '$language_menu_name',
                                                    '$language_menu_order',
                                                    '$language_is_default',
                                                    '$language_is_default',
                                                    '$language_is_active')";
    $all_queries .= "<br>".$query_insert_language;
    $result_insert_language = mysqli_query($db_link, $query_insert_language);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages['sql_error_insert']." - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    
    mysqli_query($db_link,"COMMIT");
    
    header("Location: $back_link");
  }
  //if(isset($_POST['submit'])
  
  $page_title = $languages['language_add_new_title'];
  $page_description = $languages['company_name']." администрация";
  
  print_html_admin_header($page_title, $page_description);
?>

<!--navigation-->
  <main id="page_details">
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['header_home'];?></a>
        <span>&raquo;</span>
        <a href="<?=$back_link;?>" title="<?=$languages['title_breadcrumbs_languages'];?>"><?=$languages['header_languages'];?></a>
        <span>&raquo;</span>
        <?=$languages['header_language_add_new'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages['header_language_add_new'];?></h1>
      
      <form method="post" class="input_form row" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>">
          
        <div>
          <p class="title"><?=$languages['header_language_code'];?><span class="red">*</span></p>
          <input type="text" name="language_code" id="language_code" style="width: 100px;" /> &nbsp;&nbsp;&nbsp;
          <a href="http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" title="<?=$languages['title_check_iso_language_codes'];?>" target="_blank">
            <img src="/<?=$_SESSION['admin_dir_name'];?>/images/info.gif" class="systemicon" width="16" height="16" alt="<?=$languages['alt_language_check_iso_codes'];?>" />
          </a>
        </div>
        <div class="clearfix"></div>

        <div class="col-lg-4 col-md-6 col-sm-10 col-xs-12">
          <p class="title"><?=$languages['header_language_name'];?><span class="red">*</span></p>
          <input type="text" name="language_name" id="language_name" />
        </div>
        <div class="clearfix"></div>

        <div class="col-lg-4 col-md-6 col-sm-10 col-xs-12">
          <p class="title"><?=$languages['header_language_menu_name'];?><span class="red">*</span></p>
          <input type="text" name="language_menu_name" id="language_menu_name" />
        </div>
        <div class="clearfix"></div>
          
        <div>
          <p class="title"><?=$languages['header_is_active'];?></p>
          <input type="checkbox" name="language_is_active" id="language_is_active" value="1" checked="checked" />
        </div>
        <div class="clearfix"></div>
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>

        <div>
          <button type="submit" name="submit" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="fa fa-undo" aria-hidden="true"></i> <?=$languages['btn_cancel'];?></button>
        </div>
        <div class="clearfix"></div>
        
      </form>
      <div class="clearfix"></div>
    </div>
  </main>
<!--navigation-->

<?php
 
  print_html_admin_footer();
  
?>
</body>
</html>