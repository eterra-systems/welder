<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  //print_array_for_debug($_SESSION);
  $customer_id = $_SESSION['customer_id'];
  $customer_fullname = $_SESSION['customer_name'];
  
  $query_categories = "SELECT `category_hierarchy_ids` FROM `customers_to_categories` WHERE `customer_id` = '$customer_id'";
  //echo $query_categories;
  $result_categories = mysqli_query($db_link, $query_categories);
  if(!$result_categories) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_categories) > 0) {
    while($categories = mysqli_fetch_assoc($result_categories)) {
      
      $category_ids_tree[] = str_replace(".", "", $categories['category_hierarchy_ids']);
    }
  }
  
  if(isset($_POST['update_profile'])) {
    //echo "<pre>";print_r($_POST);
    
    
    if(isset($_POST['categories'])) {
      $categories = $_POST['categories'];
      $category_ids_tree = $categories;
    }
    else $errors['categories'] = $languages['error_choosen_category'];
    
    if(empty($errors)) {
      
      foreach($categories as $key) {
        
        $category_hierarchy_ids = $_POST['category_hierarchy_ids'][$key];
        
        $q_insert_ctc = "INSERT INTO `customers_to_categories`(`customer_id`, `category_hierarchy_ids`) VALUES ('$customer_id','$category_hierarchy_ids')";
        $all_queries .= "<br>".$q_insert_ctc;
        $result_insert_ctc = mysqli_query($db_link, $q_insert_ctc);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages['sql_error_insert']." - 3 `customers_to_categories` ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
      $success = true;
    }
  }
?>
    <form name="user_profile_skills" id="user_profile_skills" class="form-group" method="post" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>">
<?php
    if(isset($success)) {
?>
  <div class="row">
    <p class="alert alert-success">Промерните бяха запазени успешно</p>
  </div>
<?php
    }
    if(!empty($errors)) {

      //foreach($errors as $error) echo "<div class='warning_field'>$error</div>";
    }
?>
      <input type="hidden" name="customer_id" id="customer_id" value="<?=$customer_id;?>">
      
      <?php if(!empty($errors['categories'])) { ?><span class="alert alert-danger"><?=$errors['categories'];?></span><?php } ?>
      <div class="tree row">
        <ul>
          <?php list_categories_with_checkboxes($category_parent_id = 0, $category_root_id = 0, $category_ids_tree) ;?>
        </ul>
      </div>
      <p class="clearfix">&nbsp;</p>

      <div class="clearfix">&nbsp;</div>

      <div class="row">
        <button type="submit" name="update_profile" class="button2"><?=$languages['btn_save'];?></button>
      </div>
      <div class="clearfix">&nbsp;</div>
    </form>
  <script>
  $(function() {
    
    //start family tree
    $('.select_all').on('click', function (e) {
      var state = true;
      var root = $(this).attr("data-root");
      if($(this).hasClass("active")) {
        $(this).removeClass("active")
        state = false;
      }
      else {
        $(this).addClass("active")
        state = true;
      }
      var checkboxes = document.getElementsByClassName("categories_"+root);
      for(var i=0; i<checkboxes.length ; i++) {
        if(checkboxes[i].type == "checkbox") {
          checkboxes[i].checked = state;
        }
      }
    });
    $('.tree li.expandable label').on('click', function (e) {
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