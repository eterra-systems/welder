<?php
  include_once '../../../../config.php';
  include_once "../../../../languages/languages_$current_lang.php";
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //print_array_for_debug($_POST);
  
  if(isset($_POST['category_id'])) {
    $category_id = $_POST['category_id'];
  }
  if(isset($_POST['old_default_category_id'])) {
    // the old default category
    $old_default_category_id =  $_POST['old_default_category_id'];
  }
  if(isset($_POST['new_default_category_id'])) {
    // the new default category
    $new_default_category_id =  $_POST['new_default_category_id'];
  }
  if(isset($_POST['categories'])) {
    $category_categories =  $_POST['categories'];
  }
  if(isset($_POST['category_ids'])) {
    $category_ids_arr =  $_POST['category_ids'];
  }
  if(isset($_POST['category_root_ids'])) {
    $category_root_ids =  $_POST['category_root_ids'];
  }
  if(isset($_POST['category_hierarchy_levels'])) {
    $category_hierarchy_levels =  $_POST['category_hierarchy_levels'];
  }
  if(isset($_POST['category_hierarchy_ids'])) {
    $category_hierarchy_ids_arr =  $_POST['category_hierarchy_ids'];
  }
  if(isset($_POST['category_categories_names'])) {
    $category_categories_names =  $_POST['category_categories_names'];
  }
  if(!empty($_POST['old_categories_list'])) {
    /*
     * removing the last string element, because it's a comma
     * and we need only the ids
     */
    $old_categories_list = substr($_POST['old_categories_list'], 0, -1);
    $old_categories_ids_array = explode(",",$old_categories_list);
  }
  $categories_ids_array = array();
  if(!empty($_POST['categories_list'])) {
    /*
     * removing the last string element, because it's a comma
     * and we need only the ids
     */
    $categories_list = substr($_POST['categories_list'], 0, -1);
    $categories_ids_array = explode(",",$categories_list);
    //echo"<pre>";print_r($categories_ids_array);echo "</pre>";exit;
  }
  
  if(!empty($_POST)) {
      
    $all_queries = "";
    mysqli_query($db_link,"BEGIN");
    
    /*
     * first we gonna check if some categories was unchecked
     * wich means we have to delete them from table `category_to_category`
     */
    foreach($old_categories_ids_array as $category_tree_id) {
      
      if(!in_array($category_tree_id, $categories_ids_array)) {
        
        $category_parent_id = $category_ids_arr[$category_tree_id];
        $category_root_id = $category_root_ids[$category_tree_id];
        
        $query_delete = "DELETE FROM `category_to_category` WHERE `category_id` = '$category_id' AND `category_root_id` = '$category_root_id' AND `category_parent_id` = '$category_parent_id'";
        $all_queries .= "<br>\n".$query_delete;
        //echo $query_delete;
        mysqli_query($db_link, $query_delete);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages['sql_error_delete']." - 1 delete `category_to_category` ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
        
        /*
         * we need to check if the old parent has any children left, and if not - setting it's `category_has_children` parameter to 0
         */
        $query_categories_siblings = "SELECT `category_id` FROM `category_to_category` WHERE `category_parent_id` = '$category_parent_id' AND `category_root_id` = '$category_root_id'";
        $all_queries .= "<br>\n".$query_categories_siblings;
        $result_categories_siblings = mysqli_query($db_link, $query_categories_siblings);
        if(!$result_categories_siblings) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_categories_siblings) <= 0) {

          $query_update_parent = "UPDATE `category_to_category` SET `category_has_children` = '0' WHERE `category_id` = '$category_parent_id' AND `category_root_id` = '$category_root_id'";
          $all_queries .= "<br>\n".$query_update_parent;
          $result_update_parent = mysqli_query($db_link, $query_update_parent);
          if(!$result_update_parent) {
            echo $languages['sql_error_update']." - 2 update `category_to_category` ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
          mysqli_free_result($result_categories_siblings);
        }
        
      }
    }
  
    // not ok
    if($new_default_category_id != $old_default_category_id) {
//      $query_update_category = "UPDATE `categories` SET `category_parent_id`='$new_default_category_id',`category_date_modified`=NOW() WHERE `category_id` = '$category_id'";
//      $all_queries .= "<br>\n".$query_update_category;
//      $result_update_category = mysqli_query($db_link, $query_update_category);
//      if(!$result_update_category) {
//        echo $languages['sql_error_update']." - 3 update `categories` ".mysqli_error($db_link);
//        mysqli_query($db_link,"ROLLBACK");
//        exit;
//      }
    }
    
    $category_ids_list = "";
    $there_is_new_parents = false;
    
    foreach($category_categories as $category_tree_id) {
      
      $category_ids[] = $category_tree_id;
      $category_ids_list .= "$category_tree_id,";
      
      if(!in_array($category_tree_id, $old_categories_ids_array)) {
        
        $there_is_new_parents = true;
        $category_parent_id = $category_ids_arr[$category_tree_id];
        $category_root_id = $category_root_ids[$category_tree_id];
        $category_hierarchy_level = $category_hierarchy_levels[$category_tree_id]+1;
        $category_hierarchy_ids = $category_hierarchy_ids_arr[$category_tree_id].".$category_id";
        $category_hierarchy_ids_arr_update[] = $category_hierarchy_ids_arr[$category_tree_id];
        $category_sort_order = get_category_sort_order_value($category_root_id,$category_parent_id);
        $category_has_children = 0;
        $category_is_active = 1;
        $category_show_in_menu = 1;
        $category_is_collapsed = 1;
        
        $query_insert_ctc = "INSERT INTO `category_to_category`(`category_id`, 
                                                                `category_parent_id`, 
                                                                `category_root_id`, 
                                                                `category_hierarchy_level`, 
                                                                `category_hierarchy_ids`, 
                                                                `category_sort_order`, 
                                                                `category_has_children`,
                                                                `category_is_active`,
                                                                `category_show_in_menu`,
                                                                `category_is_collapsed`)
                                                        VALUES ('$category_id',
                                                                '$category_parent_id',
                                                                '$category_root_id',
                                                                '$category_hierarchy_level',
                                                                '$category_hierarchy_ids',
                                                                '$category_sort_order',
                                                                '$category_has_children',
                                                                '$category_is_active',
                                                                '$category_show_in_menu',
                                                                '$category_is_collapsed')";
        //echo $query_insert_ctc."<br>";
        $all_queries .= "<br>\n".$query_insert_ctc;
        $result_insert_ctc = mysqli_query($db_link, $query_insert_ctc);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages['sql_error_insert']." - 4 insert `category_to_category`".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
    }
    
    if($there_is_new_parents) {
      
      //update the parent column `category_has_children` to 1, wich means it has children
      //no matter if it was set to 1 or 0
      $query_update_parent = "UPDATE `category_to_category` SET `category_has_children` = '1' 
                               WHERE `category_hierarchy_ids` = '".implode("' OR `category_hierarchy_ids` = '", $category_hierarchy_ids_arr_update)."'";
      //echo $query_update_parent;
      $all_queries .= "<br>\n".$query_update_parent;
      $result_update_parent = mysqli_query($db_link, $query_update_parent);
      if(!$result_update_parent) {
        echo $languages['sql_error_update']." - 5 update `category_to_category` ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    
    echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    mysqli_query($db_link,"COMMIT");
?> 
    <div>
      <label for="category_categories" class="title"><?=$languages['header_categories'];?><span class="red">*</span></label>
      <input type="checkbox" name="select_all" class="select_all"> Избери всички
      <div class="tree">
        <ul>
          <?php list_categories_with_checkboxes($category_parent_id = 0,$category_root_id = 0, $category_ids); ?>
        </ul>
      </div>
      <input type="hidden" name="old_categories_list" id="old_categories_list" value="<?=$category_ids_list;?>" />
      <input type="hidden" name="categories_list" id="categories_list" value="<?=$category_ids_list;?>" />
    </div>
    <div class="clearfix"></div>

    <div>
      <label for="category_categories" class="title"><?=$languages['header_default_category'];?></label>
      <select name="new_default_category_id" id="new_default_category_id" style="width: 200px;">
<?php
      foreach($category_categories as $category_id) {

        $category_cat_name = $category_categories_names[$category_id];
        $selected = ($new_default_category_id == $category_id) ? 'selected="selected"' : "";
?>
        <option value="<?=$category_id;?>" <?=$selected;?>><?=$category_cat_name;?></option>
<?php
      }
?>
      </select>
      <input type="hidden" name="old_default_category_id" id="old_default_category_id" value="<?=$new_default_category_id;?>" />
    </div>
    <div class="clearfix">&nbsp;</div>
    <script type="text/javascript">
      $(document).ready(function() {
        //start family tree
        CalculateSelectedSubcategories();
        $('.select_all').on('click', function (e) {
            var state = $(this).is(":checked");
            if(state) $("#new_default_category_id").html("");
            var checkboxes = document.getElementsByClassName("categories");
            for (var i=0; i<checkboxes.length ; i++) {
              if(checkboxes[i].type == "checkbox") {
                var category_id = checkboxes[i].value;
                var category_name = $(".tree li#"+category_id+" .category_name").html();
                if(state) {
                  $("#new_default_category_id").append("<option value='"+category_id+"'>"+category_name+"</option>");
                  $("input.category_name_"+category_id).attr("disabled",false);
                }
                else {
                  $('#new_default_category_id option[value='+category_id+']').remove();
                  $("input.category_name_"+category_id).attr("disabled",true);
                }
                checkboxes[i].checked = state;
              }
            }
            CalculateSelectedSubcategories();
        });
        $('.tree input[type="checkbox"]').on('click', function (e) {
            var state = $(this).is(":checked");
            var category_id = $(this).val();
            var category_name = $(".tree li#"+category_id+" .category_name").html();
            var categories_ids = $("#categories_list").val();
            //console.log(state);return;
            if(state) {
              categories_ids = $("#old_categories_list").val();
              var is_selected = categories_ids.search(category_id+","); // the method search() returns -1 if no match was found
              //console.log(is_selected);
              if(is_selected != '-1') {
                categories_ids = $("#categories_list").val();
                $("#categories_list").val(category_id + "," + categories_ids); 
              }
              $("#new_default_category_id").append("<option value='"+category_id+"'>"+category_name+"</option>");
              $("input.category_name_"+category_id).attr("disabled",false);
            }
            else {
              var new_categories_ids = categories_ids.replace(category_id+",","");
              $("#categories_list").val(new_categories_ids);
              $('#new_default_category_id option[value='+category_id+']').remove();
              $("input.category_name_"+category_id).attr("disabled",true);
            }
            CalculateSelectedSubcategories();
            e.stopPropagation();
        });
        $('.tree li.expandable .fa, .tree li.expandable .dropdown_link').on('click', function (e) {
            var current_tree_parent = $(this).parent('.expandable');
            var current_tree_id = current_tree_parent.attr('id');
            var child_ul = $(this).parent('.expandable').find(".expandable_ul_"+current_tree_id);
            if(child_ul.is(":visible")) {
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
    </script>
<?php
  }
  