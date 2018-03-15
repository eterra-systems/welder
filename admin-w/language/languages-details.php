<?php
  $back_link = "languages.php";
  
  if(isset($_POST['cancel'])) {
    header("Location: $back_link");
  }
  
  if(isset($_POST['submit'])) {
    
    //echo"<pre>";print_r($_POST);exit;
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    $language_code = mysqli_real_escape_string($db_link, $_POST['language_code']);
    $language_name = mysqli_real_escape_string($db_link, $_POST['language_name']);
    $language_menu_name = mysqli_real_escape_string($db_link, $_POST['language_menu_name']);
    $language_is_active = (isset($_POST['language_is_active'])) ? 1 : 0;
    
    $query_update_language = "UPDATE `languages` SET `language_code`='$language_code',
                                                     `language_name`='$language_name',
                                                     `language_menu_name`='$language_menu_name',
                                                     `language_is_active`='$language_is_active' 
                                               WHERE `language_id` = '$current_language_for_details_page_id'";
    //echo $query_update_language;
    $all_queries .= "<br>".$query_update_language;
    $result_update_language = mysqli_query($db_link, $query_update_language);
    if(!$result_update_language) {
      echo $languages['sql_error_update']." - `languages` ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
    
    //$languages_for_file = $languages;
    $admin_languages_for_file = $_POST['admin_languages_array'];
    $filename = $_SERVER['DOCUMENT_ROOT']."/languages/languages_$language_code.php";
    file_put_contents($filename, '<?php $languages = ' . var_export($admin_languages_for_file, true) . ';');
    
    //$languages_for_file = $languages;
    $frontstore_languages_for_file = $_POST['frontstore_languages_array'];
    $filename = $_SERVER['DOCUMENT_ROOT']."/frontstore/languages/languages_$language_code.php";
    file_put_contents($filename, '<?php $languages = ' . var_export($frontstore_languages_for_file, true) . ';');
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    
    mysqli_query($db_link,"COMMIT");
    
    header("Location: $back_link");
  }
  //if(isset($_POST['submit'])
  
  $page_title = $languages['language_add_new_title'];
  $page_description = $languages['company_name']." администрация";
  
  print_html_admin_header($page_title, $page_description);
  
  $query_language = "SELECT `language_id`,`language_code`,`language_name`,`language_menu_name`,`language_menu_order`,`language_is_active` 
                       FROM `languages` 
                      WHERE `language_id` = '$current_language_for_details_page_id'";
  //echo $query_language;
  $result_language = mysqli_query($db_link, $query_language);
  if(!$result_language) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_language) > 0) {
    $language_array = mysqli_fetch_assoc($result_language);
    $language_code = stripslashes($language_array['language_code']);
    $language_name = stripslashes($language_array['language_name']);
    $language_menu_name = stripslashes($language_array['language_menu_name']);
    $language_is_active = $language_array['language_is_active'];
  }
?>

<!--navigation-->
<main id="page_details">
  <div class="inside_container">
    <section id="breadcrumbs">
      <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['header_home'];?></a>
      <span>&raquo;</span>
      <a href="<?=$back_link;?>" title="<?=$languages['title_breadcrumbs_languages'];?>"><?=$languages['header_languages'];?></a>
      <span>&raquo;</span>
      <?=$languages['header_language_edit'];?>
    </section>

    <h1 id="pagetitle"><?=$languages['header_language_edit'];?></h1>

<?php
    if($_SESSION['admin']['user_type_is_superuser'] == 1) {
?>
    <form method="post" style="position: relative;z-index: 900;top: 10px;right: 10px;" action="languages-synchronize.php">
      <button type="submit" name="go_to_synchronize" class="button blue pull-right">Синхронизирай с български</button>
      <input type="hidden" name="language_code" value="<?=$language_code;?>" />
      <input type="hidden" name="admin_or_frontstore" id="admin_or_frontstore" value="" />
    </form>
<?php
    }
?>
    
    
    <form method="post" class="input_form row" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>">

      <div>
        <button type="submit" name="submit" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
        <button type="submit" name="cancel" class="button blue"><i class="fa fa-undo" aria-hidden="true"></i> <?=$languages['btn_cancel'];?></button>
      </div>
      <div class="clearfix"></div>
      
      <div>
        <p class="title"><?=$languages['header_language_code'];?><span class="red">*</span></p>
        <input type="text" name="language_code" id="language_code" value="<?=$language_code;?>" style="width: 100px;" /> &nbsp;&nbsp;&nbsp;
        <a href="http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" title="<?=$languages['title_check_iso_language_codes'];?>" target="_blank">
          <img src="/<?=$_SESSION['admin_dir_name'];?>/images/info.gif" class="systemicon" width="16" height="16" alt="<?=$languages['alt_language_check_iso_codes'];?>" />
        </a>
      </div>
      <div class="clearfix"></div>

      <div class="col-lg-4 col-md-6 col-sm-10 col-xs-12">
        <p class="title"><?=$languages['header_language_name'];?><span class="red">*</span></p>
        <input type="text" name="language_name" id="language_name" value="<?=$language_name;?>" />
      </div>
      <div class="clearfix"></div>

      <div class="col-lg-4 col-md-6 col-sm-10 col-xs-12">
        <p class="title"><?=$languages['header_language_menu_name'];?><span class="red">*</span></p>
        <input type="text" name="language_menu_name" id="language_menu_name" value="<?=$language_menu_name;?>" />
      </div>
      <div class="clearfix"></div>

      <div>
        <p class="title"><?=$languages['header_is_active'];?></p>
        <input type="checkbox" name="language_is_active" id="language_is_active" value="<?=$language_is_active;?>" checked="checked" />
      </div>
      <div class="clearfix"></div>
      <div class="clearfix">
        <p>&nbsp;</p>
      </div>

      <h4>Езикови преводи</h4>

      <ul class="translations_tabs tabs">
        <li><a href="#translations_admin_tab"><?=$languages['header_translations_admin_tab'];?></a></li>
        <li><a href="#translations_frontend_tab"><?=$languages['header_translations_frontend_tab'];?></a></li>
      </ul>
      <div class="clearfix">&nbsp;</div>

      <div class="input_form">
        
        <div id="translations_admin_tab" class="translation_tab tab">
<?php
      $current_languages = $languages;
      require_once($_SERVER['DOCUMENT_ROOT']."/languages/languages_$language_code.php");
      $blocks_counter = 1;
      $array_counter = 1;
      $array_count = count($languages);
      foreach($languages as $field_name => $translation) {

        if($blocks_counter == 1) echo '<div class="row margin_bottom">';
?>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
          <div><b><?=$field_name;?></b></div>
          <input type="text" name="admin_languages_array[<?=$field_name;?>]" value='<?=$translation;?>' />
        </div>
<?php
        if($blocks_counter == 2 || $array_counter == $array_count) {
          echo '</div>';
          $blocks_counter = 0;
        }

        $array_counter++;
        $blocks_counter++;
      }
      $languages = $current_languages;
?>
        </div>
        
        <div id="translations_frontend_tab" class="translation_tab tab">
<?php
      require_once($_SERVER['DOCUMENT_ROOT']."/frontstore/languages/languages_$language_code.php");
      $blocks_counter = 1;
      $array_counter = 1;
      $array_count = count($languages);
      foreach($languages as $field_name => $translation) {

        if($blocks_counter == 1) echo '<div class="row margin_bottom">';
?>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
          <div><b><?=$field_name;?></b></div>
          <input type="text" name="frontstore_languages_array[<?=$field_name;?>]" value='<?=$translation;?>' />
        </div>
<?php
        if($blocks_counter == 2 || $array_counter == $array_count) {
          echo '</div>';
          $blocks_counter = 0;
        }

        $array_counter++;
        $blocks_counter++;
      }
      $languages = $current_languages;
?>
        </div>
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
<script type="text/javascript">
  $(document).ready(function() {
    // translations tab switcher
    $(".translations_tabs li").removeClass("active");
    $(".translation_tab").hide();
    $(".translations_tabs li:first").addClass("active");
    $(".translation_tab:first").show();
    $(".translations_tabs a").click(function() {
      var this_link = $(this);
      var clicked_tab = this_link.attr("href");
      if(clicked_tab == "#translations_admin_tab") {
        $("#admin_or_frontstore").val("");
      }
      else {
        $("#admin_or_frontstore").val("frontstore");
      }
      $(".translations_tabs li").removeClass("active");
      this_link.parent().addClass("active");
      $(".translation_tab").hide();
      $(clicked_tab).fadeIn();
      event.preventDefault();
    });
    // end translations tab switcher
  });
</script>

<?php
 
  print_html_admin_footer();
  
?>
</body>
</html>