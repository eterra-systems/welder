<?php

  include_once '../../../../config.php';
  include_once "../../../../languages/languages_$current_lang.php";
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //echo"<pre>";print_r($_POST);echo"</pre>";exit;
  
  if(isset($_POST['news_id'])) {
    $news_id = $_POST['news_id'];
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
    $news_categories =  $_POST['categories'];
  }
  if(isset($_POST['news_categories_names'])) {
    $news_categories_names =  $_POST['news_categories_names'];
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
    
    $old_default_category_was_deleted = false;
    
    /*
     * first we gonna check if some categories was unchecked
     * wich means we have to delete them from table `news_to_news_category`
     */
    foreach($old_categories_ids_array as $category_id) {
      
      if(!in_array($category_id, $categories_ids_array)) {
        
        if($old_default_category_id == $category_id) $old_default_category_was_deleted = true;
        
        $query_delete = "DELETE FROM `news_to_news_category` WHERE `news_id` = '$news_id' AND `news_category_id` = '$category_id'";
        $all_queries .= "<br>\n".$query_delete;
        //echo $query;exit;
        mysqli_query($db_link, $query_delete);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages['sql_error_delete']." - 1 delete `news_to_news_category` ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
    }
  
    if($new_default_category_id != $old_default_category_id) {
      
      $query_update_news = "UPDATE `news` SET `news_category_id`='$new_default_category_id',`news_modified_date`=NOW() WHERE `news_id` = '$news_id'";
      $all_queries .= "<br>\n".$query_update_news;
      $result_update_news = mysqli_query($db_link, $query_update_news);
      if(!$result_update_news) {
        echo $languages['sql_error_update']." - 2 update `news` ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      /*
       * new default category was set
       * if the old category was not unchecked and so not deleted
       * update the existing row and set `cat_is_default`='0'
       * then if the new default category is not a new checked category
       * update the existing row and set `cat_is_default`='1'
       */
      if(!$old_default_category_was_deleted) {
        $query_update_news = "UPDATE `news_to_news_category` SET `cat_is_default`='0' WHERE `news_category_id` = $old_default_category_id AND `news_id` = '$news_id'";
        $all_queries .= "<br>\n".$query_update_news;
        $result_update_news = mysqli_query($db_link, $query_update_news);
        if(!$result_update_news) {
          echo $languages['sql_error_update']." - 3 update `news_to_news_category` ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
        
      if(in_array($new_default_category_id, $old_categories_ids_array)) {
        $query_update_news = "UPDATE `news_to_news_category` SET `cat_is_default`='1' WHERE `news_category_id` = $new_default_category_id AND `news_id` = '$news_id'";
        $all_queries .= "<br>\n".$query_update_news;
        $result_update_news = mysqli_query($db_link, $query_update_news);
        if(!$result_update_news) {
          echo $languages['sql_error_update']." - 4 update `news_to_news_category` ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
    }
    
    $category_ids_list = "";
    
    foreach($news_categories as $category_id) {
      
      $category_ids_list .= "$category_id,";
      
      if(!in_array($category_id, $old_categories_ids_array)) {
        
        $cat_is_default = ($new_default_category_id == $category_id) ? 1 : 0;
        $news_sort_order = get_news_highest_order_value_for_category($category_id)+1;
        $query_insert_news_to_cat = "INSERT INTO `news_to_news_category`(`news_id`, `news_category_id`,`cat_is_default`,`news_sort_order`) 
                                                                  VALUES ('$news_id','$category_id','$cat_is_default','$news_sort_order')";
        //echo $query_insert_news_to_cat."<br>";
        $all_queries .= "<br>\n".$query_insert_news_to_cat;
        $result_insert_news_to_cat = mysqli_query($db_link, $query_insert_news_to_cat);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages['sql_error_insert']." - 5 insert `news_to_news_category`".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
    }
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    mysqli_query($db_link,"COMMIT");
?> 
    <div>
      <label for="news_categories" class="title"><?=$languages['header_news_categories'];?><span class="red">*</span></label>
      <div class="tree">
        <ul>
          <?php list_news_categories_with_checkboxes($cat_parent_id = 0, $category_ids = $news_categories); ?>
        </ul>
      </div>
      <input type="hidden" name="old_categories_list" id="old_categories_list" value="<?=$category_ids_list;?>" />
      <input type="hidden" name="categories_list" id="categories_list" value="<?=$category_ids_list;?>" />
    </div>
    <div class="clearfix"></div>

    <div>
      <label for="news_categories" class="title"><?=$languages['header_news_default_category'];?></label>
      <select name="new_default_category_id" id="new_default_category_id" style="width: 200px;">
<?php
      foreach($news_categories as $category_id) {

        $news_cat_name = $news_categories_names[$category_id];
        $selected = ($new_default_category_id == $category_id) ? 'selected="selected"' : "";
?>
        <option value="<?=$category_id;?>" <?=$selected;?>><?=$news_cat_name;?></option>
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
        $.each($(".tree li.expandable"), function(){
            var checked_cat = 0;
            var current_list = $(this);
            var current_list_level = current_list.attr("data-level");
            var checkboxes = current_list.find("input[class!='level_1']");
            $.each($(checkboxes), function(){
                if($(this).is(":checked")) checked_cat++;
            });
            if(checked_cat > 0) {
              current_list.find("a.dropdown_link_"+current_list_level+" .news_cat_count_box").show();
              current_list.find("a.dropdown_link_"+current_list_level+" .news_cat_count_digits").html(checked_cat);
              if(checked_cat > 1) {
                current_list.find("a.dropdown_link_"+current_list_level+" .news_cat_count_text").html("подкатегории избрани");
              }
              else {
                current_list.find("a.dropdown_link_"+current_list_level+" .news_cat_count_text").html("подкатегория избрана");
              }
            }
        });
        $('.tree input[type="checkbox"]').on('click', function (e) {
            var state = $(this).is(":checked");
            var category_id = $(this).val();
            var category_name = $(".tree li#"+category_id+" .category_name").html();
            var categories_ids = $("#categories_list").val();
            //console.log(state);return;
            if(state) {
              var categories_ids = $("#old_categories_list").val();
              var is_selected = categories_ids.search(category_id+","); // the method search() returns -1 if no match was found
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
            e.stopPropagation();
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
    </script>
<?php
  }
  