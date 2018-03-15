<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
//  echo"<pre>";print_r($_POST);echo"</pre>";
  $back_link = "languages.php";
  
  if(isset($_POST['cancel'])) {
    header("Location: $back_link");
  }
    
  if(isset($_POST['go_to_synchronize'])) {
    $language_code = $_POST['language_code'];
    $admin_or_frontstore = $_POST['admin_or_frontstore'];
  }
  
  if(isset($_POST['submit'])) {
    
    //echo"<pre>";print_r($_POST);exit;
    
    $language_code = $_POST['language_code'];
    $admin_or_frontstore = $_POST['admin_or_frontstore'];
    
    //$languages_for_file = $languages;
    $languages_for_file = $_POST['languages_array'];
    $filename = $_SERVER['DOCUMENT_ROOT']."$admin_or_frontstore/languages/languages_$language_code.php";
    file_put_contents($filename, '<?php $languages = ' . var_export($languages_for_file, true) . ';');
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    
    mysqli_query($db_link,"COMMIT");
    
    header("Location: $back_link");
  }
  //if(isset($_POST['submit'])
  
  if(!isset($language_code) && !isset($admin_or_frontstore)) {
    header("Location: $back_link");
  }
    
  $page_title = "Синхронизиране";
  $page_description = $languages['company_name']." администрация";
  
  print_html_admin_header($page_title, $page_description);
?>

<!--navigation-->
  <main id="page_details">
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['header_home'];?></a>
        <span>&raquo;</span>
        <a href="<?=$back_link;?>" title="<?=$languages['title_breadcrumbs_languages'];?>"><?=$languages['header_languages'];?></a>
        <span>&raquo;</span>
        Синхронизиране
      </section>
      
      <h1 id="pagetitle">Синхронизиране</h1>
      
      <form method="post" class="input_form row" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>">
        <input type="hidden" name="language_code" value="<?=$language_code;?>" />
        <input type="hidden" name="admin_or_frontstore" value="<?=$admin_or_frontstore;?>" />
<?php
      $current_languages = $languages;
      require_once($_SERVER['DOCUMENT_ROOT']."$admin_or_frontstore/languages/languages_bg.php");
      $languages_bg = $languages;
      require_once($_SERVER['DOCUMENT_ROOT']."$admin_or_frontstore/languages/languages_$language_code.php");
//      echo "$admin_or_frontstore/languages/languages_$language_code.php <br>";print_r($languages_bg);echo "<br><br>";print_r($languages);
      $blocks_counter = 1;
      if(count($languages_bg) > count($languages)) {
        foreach($languages_bg as $field_name => $translation) {

          if(isset($languages[$field_name])) {
            $translation = $languages[$field_name];
          }

          if($blocks_counter == 1) echo '<div class="row margin_bottom">';
?>
          <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div><b><?=$field_name;?></b></div>
            <input type="text" name="languages_array[<?=$field_name;?>]" value='<?=$translation;?>' />
          </div>
<?php
          if($blocks_counter == 2) {
            echo '</div>';
            $blocks_counter = 0;
          }

          $blocks_counter++;
        }
      }
      else {
?>
        <h3 class="alert alert-success">Всички думи за <?=(!empty($admin_or_frontstore)) ? "$admin_or_frontstore/" : "";?>languages_<?=$language_code;?>.php са попълени</h3>
<?php
      }
      //echo "Всички думи за languages_$language_code.php са $words_counter";
      
      $languages = $current_languages;
?>
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