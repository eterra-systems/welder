<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  $back_link = "categories-options.php";
  
  if(isset($_POST['cancel'])) {
    header("Location: $back_link");
  }
  
  $languages_array = get_languages();

  //we gonna set default values
  $category_ids  = array();
  $select_all = 0;
  $option_values_count = 1;
    
  if(isset($_POST['submit_product_option'])) {
   
//    echo"<pre>";print_r($_POST);"</pre>";
//    exit;
    
    mysqli_query($db_link,"BEGIN");
    
    $product_option_errors = array();
    $all_queries = "";
      
    if(isset($_POST['option_values_count'])) {
      $option_values_count = $_POST['option_values_count'];
    }
    $option_sort_order = get_option_lаst_child_sort_order()+1;
    $option_is_frontend_sortable = (isset($_POST['option_is_frontend_sortable'])) ? 1 : 0;
    $option_use_only_for_sorting = (isset($_POST['option_use_only_for_sorting'])) ? 1 : 0;
    $option_use_for_tag_mfr = (isset($_POST['option_use_for_tag_mfr'])) ? 1 : 0;
    
    foreach($_POST['opt_backend_name'] as $language_id => $opt_backend_name) {
      if(empty($opt_backend_name)) $product_option_errors['opt_backend_name'][$language_id] = $languages['required_field_error'];
      if(empty($_POST['opt_frontend_name'][$language_id])) $product_option_errors['opt_frontend_name'][$language_id] = $languages['required_field_error'];
      
      $opt_backend_names_array[$language_id] = $_POST['opt_backend_name'][$language_id];
      $opt_frontend_names_array[$language_id] = $_POST['opt_frontend_name'][$language_id];
    }
    
    if(isset($_POST['option_value'])) {
      foreach($_POST['option_value'] as $option_value_key => $option_value_row) {

        $option_values_array[$option_value_key]['option_value_id'] = $option_value_row['option_value_id'];
        $option_values_array[$option_value_key]['ov_sort_order'] = $option_value_row['ov_sort_order'];

        foreach($option_value_row['ovd_value'] as $language_id => $ovd_value) {
          if(empty($ovd_value)) $product_option_errors[$option_value_key]['ovd_value'][$language_id] = $languages['required_field_error'];

          $ovd_values_array[$option_value_key][$language_id] = $ovd_value;
        }
      }
    }
    
    $select_all = (isset($_POST['select_all'])) ? 1 : 0;
    if(isset($_POST['categories'])) {
      
      $category_ids = $_POST['categories'];
    }
    else {
      $product_option_errors['categories'] = $languages['error_choosen_category'];
    }
    
    if(empty($product_option_errors)) {
      //if there are no form errors we can insert the information
     
      $query_insert_option = "INSERT INTO `options`(`option_id`,`option_sort_order`,`option_is_frontend_sortable`,`option_use_only_for_sorting`,`option_use_for_tag_mfr`) 
                                            VALUES (NULL,'$option_sort_order','$option_is_frontend_sortable','$option_use_only_for_sorting','$option_use_for_tag_mfr')";
      //echo $query_insert_option;
      $all_queries .= "<br>".$query_insert_option;
      $result_insert_option = mysqli_query($db_link, $query_insert_option);
      if(!$result_insert_option) {
        echo $languages['sql_error_insert']." - 1 `options` ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      $option_id = mysqli_insert_id($db_link);
      
      foreach($_POST['categories'] as $category_id) {
        $query_insert_opt_to_cat = "INSERT INTO `option_to_category`(`option_id`, `category_id`) 
                                                            VALUES ('$option_id','$category_id')";
        //echo $query_insert_opt_to_cat;
        $all_queries .= "<br>".$query_insert_opt_to_cat;
        $result_insert_opt_to_cat = mysqli_query($db_link, $query_insert_opt_to_cat);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages['sql_error_insert']." - 2 `option_to_category`".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
        
      foreach($opt_backend_names_array as $language_id => $opt_backend_name) {
        
        $opt_backend_name = mysqli_real_escape_string($db_link, $opt_backend_name);
        $opt_frontend_name = mysqli_real_escape_string($db_link, $opt_frontend_names_array[$language_id]);

        $query_insert_opt_backend_name = "INSERT INTO `option_description`(`option_id`, `language_id`, `opt_backend_name`,`opt_frontend_name`) 
                                                                  VALUES ('$option_id','$language_id','$opt_backend_name','$opt_frontend_name')";
        //echo $query_insert_opt_backend_name;
        $all_queries .= "<br>".$query_insert_opt_backend_name;
        $result_insert_opt_backend_name = mysqli_query($db_link, $query_insert_opt_backend_name);
        if(!$result_insert_opt_backend_name) {
          echo $languages['sql_error_insert']." - 4 `option_description`".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
        
      }
      
      if(isset($_POST['option_value'])) {
        foreach($_POST['option_value'] as $option_value_key => $option_value_row) {

          $ov_is_custom = 0;
          $ov_sort_order = $option_value_row['ov_sort_order'];

          $query_insert_option_value = "INSERT INTO `option_value`(`option_value_id`, 
                                                                  `option_id`, 
                                                                  `ov_is_custom`,
                                                                  `ov_sort_order`) 
                                                          VALUES (NULL,
                                                                  '$option_id',
                                                                  '$ov_is_custom',
                                                                  '$ov_sort_order')";
          //echo $query_insert_option_value."<br>";
          $all_queries .= "<br>".$query_insert_option_value;
          $result_insert_option_value = mysqli_query($db_link, $query_insert_option_value);
          if(mysqli_affected_rows($db_link) <= 0) {
            echo $languages['sql_error_insert']." - 5 `option_value`".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }

          $option_value_id = mysqli_insert_id($db_link);

          foreach($option_value_row['ovd_value'] as $language_id => $ovd_value) {

            $ovd_value = mysqli_real_escape_string($db_link, $ovd_value);

            if(!empty($ovd_value)) {
              $query_insert_ovd_value = "INSERT INTO `option_value_description`(`option_value_id`, 
                                                                                `language_id`, 
                                                                                `option_id`, 
                                                                                `ovd_value`) 
                                                                        VALUES ('$option_value_id',
                                                                                '$language_id',
                                                                                '$option_id',
                                                                                '$ovd_value')";
              //echo $query_insert_ovd_value."<br>";
              $all_queries .= "<br>".$query_insert_ovd_value;
              $result_insert_ovd_value = mysqli_query($db_link, $query_insert_ovd_value);
              if(mysqli_affected_rows($db_link) <= 0) {
                echo $languages['sql_error_insert']." - 6 `option_value_description`".mysqli_error($db_link);
                mysqli_query($db_link,"ROLLBACK");
                exit;
              }
            } 

          }

        } //foreach($_POST['option_value'] as $option_value_key => $option_value_row)
      }
    
      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header("Location: $back_link");
    }//if(empty($product_option_errors))
    
  }//if(isset($_POST['submit_product_option']))
  
  $page_title = $languages['page_title_add_new_categories_option'];
  $page_description = $languages['company_name']." администрация";
  
  print_html_admin_header($page_title, $page_description);
?>
  <main id="page_details">
    <div class="inside_container">
      <div id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['header_home'];?></a>
        <span>&raquo;</span>
        <a href="<?=$back_link;?>" title="<?=$languages['title_breadcrumbs_option'];?>"><?=$languages['header_categories_options'];?></a>
        <span>&raquo;</span>
        <?=$languages['header_categories_option_add_new'];?>
      </div>
      
<?php if(isset($product_option_errors) && !empty($product_option_errors)) echo '<div class="warning">Моля проверете дали всички задължителни полета са попълнени</div>';?>
      
      <h1 id="pagetitle"><?=$languages['header_categories_option_add_new'];?></h1>
        
      <ul class="option_tabs tabs">
        <li><a href="#option_main_tab"><?=$languages['header_main_tab'];?></a></li>
        <li <?php if(isset($product_option_errors['categories'])) echo "class='red error'"?>><a href="#option_categories_tab"><?=$languages['header_categories_tab'];?></a></li>
      </ul>
      <div class="clearfix">&nbsp;</div>
        
      <form method="post" name="add_product_option" enctype="multipart/form-data" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>" id="add_product_option" class="input_form row">
      
      <!--option_main_tab-->
      <div id="option_main_tab" class="option_tab tab row">
        
        <div>
          <label for="option_is_frontend_sortable" class="title"><?=$languages['header_option_is_frontend_sortable'];?></label>
          <?php
            if(isset($option_is_frontend_sortable) && $option_is_frontend_sortable == 0) {
              echo '<input type="checkbox" name="option_is_frontend_sortable" id="option_is_frontend_sortable" />';
            }
            else echo '<input type="checkbox" name="option_is_frontend_sortable" id="option_is_frontend_sortable" />';
          ?>
        </div>
        
        <div>
          <label for="option_use_only_for_sorting" class="title"><?=$languages['header_option_use_only_for_sorting'];?></label>
          <?php
            if(isset($option_use_only_for_sorting) && $option_use_only_for_sorting == 0) {
              echo '<input type="checkbox" name="option_use_only_for_sorting" id="option_use_only_for_sorting" />';
            }
            else echo '<input type="checkbox" name="option_use_only_for_sorting" id="option_use_only_for_sorting" />';
          ?>
        </div>
        
        <div>
          <label for="option_use_for_tag_mfr" class="title"><?=$languages['header_option_use_for_tag_mfr'];?></label>
          <?php
            if(isset($option_use_for_tag_mfr) && $option_use_for_tag_mfr == 0) {
              echo '<input type="checkbox" name="option_use_for_tag_mfr" id="option_use_for_tag_mfr" />';
            }
            else echo '<input type="checkbox" name="option_use_for_tag_mfr" id="option_use_for_tag_mfr" />';
          ?>
        </div>
<?php
        if(!empty($languages_array)) {
            
          for($i= 1; $i < 3; $i++) {

            $key = 0;
            
            foreach($languages_array as $row_languages) {

              $language_id = $row_languages['language_id'];
              $language_code = $row_languages['language_code'];
              $language_menu_name = $row_languages['language_menu_name'];

              if($i == 1) {
                /*
                 * first print option_backend_name
                 * then print option_frontend_name
                 */
?>
            <div class="col-lg-6 col-md-10 col-sm-12 col-xs-12">
              <?php
                if($key == 0) {
              ?>
                <label for="opt_backend_name" class="title"><?=$languages['header_option_backend_name'];?><span class="red">*</span></label>
              <?php
                }
                if(isset($product_option_errors['opt_backend_name'][$language_id])) {
                  echo "<div class='error'>".$product_option_errors['opt_backend_name'][$language_id]."</div>";
                }
              ?>
              <input type="text" name="opt_backend_name[<?=$language_id;?>]" class="opt_backend_name" style="width:92%" value="<?php if(isset($opt_backend_names_array[$language_id])) echo $opt_backend_names_array[$language_id];?>" />
              &nbsp;&nbsp;<img src="/<?=$_SESSION['admin_dir_name'];?>/images/flags/<?=$language_code;?>.png" title="<?=$language_menu_name;?>" />
            </div>
            <p class="clearfix"></p>
<?php     
              }
              else {
?>
            <div class="col-lg-6 col-md-10 col-sm-12 col-xs-12">
              <?php
                if($key == 0) {
              ?>
                <label for="opt_frontend_name" class="title"><?=$languages['header_option_frontend_name'];?><span class="red">*</span></label>
              <?php
                }
                if(isset($product_option_errors['opt_frontend_name'][$language_id])) {
                  echo "<div class='error'>".$product_option_errors['opt_frontend_name'][$language_id]."</div>";
                }
              ?>
              <input type="text" name="opt_frontend_name[<?=$language_id;?>]" class="opt_frontend_name" style="width:92%" value="<?php if(isset($opt_frontend_names_array[$language_id])) echo $opt_frontend_names_array[$language_id];?>" />
              &nbsp;&nbsp;<img src="/<?=$_SESSION['admin_dir_name'];?>/images/flags/<?=$language_code;?>.png" title="<?=$language_menu_name;?>" />
            </div>
            <p class="clearfix"></p>
<?php
              }
              $key++;
            } //foreach($languages_array as $key => $row_languages)
          } //for($i= 1; $i < 3; $i++)
        }
?>
        <div id="option_values" style="padding-top: 20px;">
          <table class="border">
            <thead>
              <tr>
                <th style="width:55%" class="text_left"><?=$languages['header_option_value'];?></th>
                <th style="width:15%">ID</th>
                <th style="width:15%"><?=$languages['header_reorder'];?></th>
                <th style="width:15%"><?=$languages['header_actions'];?></th>
              </tr>
            </thead>
<?php
        if(!empty($option_values_array)) {
          
          foreach($option_values_array as $option_key => $option_value) {
            
            $option_value_id = $option_value['option_value_id'];
            $ov_sort_order = $option_value['ov_sort_order'];
?>
            <tbody id="option_value_row_<?=$option_key;?>">
              <tr>
                <td class="text_left">         
<?php
            foreach($languages_array as $key => $row_languages) {

              $language_id = $row_languages['language_id'];
              $language_code = $row_languages['language_code'];
              $language_menu_name = $row_languages['language_menu_name'];
?>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <input type="hidden" name="option_value[<?=$option_key;?>][option_value_id]" value="<?=$option_value_id;?>" style="width:92%" />
              <input type="text" name="option_value[<?=$option_key;?>][ovd_value][<?=$language_id;?>]" value="<?php if(isset($ovd_values_array[$option_key][$language_id])) echo $ovd_values_array[$option_key][$language_id];?>" />
              &nbsp;&nbsp;<img src="/<?=$_SESSION['admin_dir_name'];?>/images/flags/<?=$language_code;?>.png" title="<?=$language_menu_name;?>" />
              <?php
                if(isset($product_option_errors[$option_key]['ovd_value'][$language_id])) {
                  echo "<div class='error'>".$product_option_errors[$option_key]['ovd_value'][$language_id]."</div>";
                }
              ?>
            </div>
            <p class="clearfix"></p>
<?php
            } // foreach($languages_array)
?>
                </td>
                <td></td>
                <td>
                  <input type="text" name="option_value[<?=$option_key;?>][ov_sort_order]" value="<?=$ov_sort_order;?>" style="width: 20px;">
                </td>
                <td>
<?php if($option_key != 0) { ?><a onclick="$('#option_value_row_<?=$option_key;?>').remove();" class="button red"><?=$languages['btn_delete'];?></a><?php } ?>
                </td>
              </tr>
            </tbody> 
<?php
          } // foreach($option_values_array)
        } // if(!empty($option_values_array))
        
?>
              <tfoot>
                <tr>
                  <td class="text_left">
                    <a class="button green" onClick="AddOptionValue()"><i class="icon icon_plus_sign"></i><?=$languages['btn_add_new_option_row'];?></a>
                  </td>
                  <td></td>
                  <td></td>
                  <td></td>
                </tr>
              </tfoot>
            </table>
            <input type="hidden" name="option_values_count" id="option_values_count" value="<?=$option_values_count;?>" />
          </div>
          <div class="clearfix">
            <p>&nbsp;</p>
          </div>
        </div>
        <!--option_main_tab-->

        <!--option_categories_tab-->
        <div id="option_categories_tab" class="option_tab tab row">
          <div>
            <label for="option_categories" class="title"><?=$languages['header_categories'];?><span class="red">*</span></label>
            <?php
              if(isset($product_option_errors['categories'])) {
                echo "<div class='error'>".$product_option_errors['categories']."</div>";
              }
            ?>
            <input type="checkbox" name="select_all" class="select_all" <?php if($select_all == 1) echo 'checked="checked"';?>> <?=$languages['text_select_all'];?>
            <div class="tree">
              <ul>
                <?php list_categories_with_checkboxes($category_parent_id = 0, $category_ids); ?>
              </ul>
            </div>
          </div>
          <div class="clearfix">
            <p>&nbsp;</p>
          </div>
        </div>
        <!--option_categories_tab-->
        
        <div>
          <button type="submit" name="submit_product_option" class="button green"><i class="icon icon_save_sign"></i><?=$languages['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="icon icon_cancel_sign"></i><?=$languages['btn_cancel'];?></button>
        </div>
        <div class="clearfix">&nbsp;</div>
        
      </form>
      <div class="clearfix">&nbsp;</div>
    </div>
  </main>
<?php
 
  print_html_admin_footer();
  
?>
  <script type="text/javascript">
    $(document).ready(function() {

      // options tab switcher
      $(".option_tabs li").removeClass("active");
      $(".option_tab").hide();
      $(".option_tabs li:first").addClass("active");
      $(".option_tab:first").show();
      $(".option_tabs a").click(function() {
        var this_link = $(this);
        var clicked_tab = this_link.attr("href");
        $(".option_tabs li").removeClass("active");
        this_link.parent().addClass("active");
        $(".option_tab").hide();
        $(clicked_tab).fadeIn();
        event.preventDefault();
      });
      // end options tab switcher
     
      //start family tree
      CalculateSelectedSubcategories();
      $('.select_all').on('click', function (e) {
          var state = $(this).is(":checked");
          var checkboxes = document.getElementsByClassName("categories");
          for (var i=0; i<checkboxes.length ; i++) {
            if(checkboxes[i].type == "checkbox") {
              checkboxes[i].checked = state;
            }
          }
          CalculateSelectedSubcategories();
      });
      $('.tree input[type="checkbox"]').on('click', function (e) {
          CalculateSelectedSubcategories();
      });
      $('.tree li.expandable .fa, .tree li.expandable .dropdown_link').on('click', function (e) {
          var current_tree_parent = $(this).parent('.expandable');
          var current_tree_id = current_tree_parent.attr('id');
          var child_ul = $(this).parent('.expandable').find(".expandable_ul_"+current_tree_id);
          if (child_ul.is(":visible")) {
            child_ul.hide('fast');
            current_tree_parent.removeClass("active_parent_tree");
            current_tree_parent.find(".fa_"+current_tree_id).removeClass("fa-minus-square-o").addClass("fa-plus-square-o");
          }
          else {
            child_ul.show('fast');
            current_tree_parent.addClass("active_parent_tree");
            current_tree_parent.find(".fa_"+current_tree_id).removeClass("fa-plus-square-o").addClass("fa-minus-square-o");
          }
          e.stopPropagation();
      });
      //end family tree
    });
    function AddOptionValue() {
      var option_value_row = $("#option_values_count").val();

      var html  = '<tbody id="option_value_row_'+option_value_row+'">';
      html += '  <tr>';	
      html += '    <td class="text_left">';
<?php
      foreach($languages_array as $row_languages) {

        $language_id = $row_languages['language_id'];
        $language_code = $row_languages['language_code'];
        $language_menu_name = $row_languages['language_menu_name'];
?>
      html += '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
      html += '<input type="hidden" name="option_value['+option_value_row+'][new_entry]" value="1" />';
      html += '<input type="hidden" name="option_value['+option_value_row+'][option_value_id]" value="" />';
      html += '<input type="text" name="option_value['+option_value_row+'][ovd_value][<?=$language_id;?>]" style="width: 92%;" />&nbsp;&nbsp;';
      html += '<img src="/<?=$_SESSION['admin_dir_name'];?>/images/flags/<?=$language_code;?>.png" title="<?=$language_menu_name;?>" />';
      html += '</div><p class="clearfix"></p>';
<?php
      } // foreach($languages_array)
?>
      html += '    </td>';
      html += '    <td></td>';
      html += '    <td><input type="text" name="option_value['+option_value_row+'][ov_sort_order]" value="" style="width: 20px;"></td>';
      html += '    <td><a onclick="RemoveOptionValue('+option_value_row+')" class="button red"><?=$languages['btn_delete'];?></a></td>';
      html += '  </tr>';	
      html += '</tbody>';

      $("#option_values table tfoot").before(html);

      option_value_row++;
      $("#option_values_count").val(option_value_row);
    }
    function RemoveOptionValue(option_value_row) {
      $('#option_value_row_'+option_value_row).remove();
    }
  </script>
</body>
</html>