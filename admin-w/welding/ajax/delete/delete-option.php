<?php
  
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //print_r($_POST);EXIT;
  if(isset($_POST['option_id'])) {
    $option_id = $_POST['option_id'];
  }
  if(isset($_POST['step'])) {
    $step = $_POST['step'];
  }
  
  /*
   * we gonna use a variable step, wich if equals to first
   * we gonna check if some categories have this option and
   * if so tell the user and then if is sure to delete it (second step)
   * delete the option with it's values and records for the categories
   */
 
  mysqli_query($db_link,"BEGIN");
  $all_queries= "";
  
  if($step == "first") {
    $query_product_option = "SELECT `option_id` FROM `categories_options` WHERE `option_id` = '$option_id' LIMIT 1";
    //echo $query_product_option;
    $result_product_option = mysqli_query($db_link, $query_product_option);
    if(!$result_product_option) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_product_option) > 0) {
  ?>
    <div style="display:none;" id="modal_confirm_delete_option" class="clearfix" title="<?=$languages['text_are_you_sure']?>">
      <p style="padding:0;margin:0;width:100%;float:left;">
        <?=$languages['warning_option_is_assigned_to_categories'];?>
      </p>
    </div>
    <input type="hidden" name="current_option_id" class="delete_option_link active" data-id="<?=$option_id;?>" >
    <script>
    $(function() {
      $("#modal_confirm_delete_option").dialog({
        resizable: false,
        width: 400,
        height: 200,
        autoOpen: false,
        modal: true,
        draggable: false,
        closeOnEscape: true,
        dialogClass: "modal_confirm",
        buttons: {
          "<?=$languages['btn_delete'];?>": function() {
            DeleteOption('second');
          },
          "<?=$languages['btn_cancel'];?>": function() {
            $(".delete_option_link").removeClass("active");
            $(this).dialog("close");
          }
        }
      });
      $("#modal_confirm_delete_option").dialog("open");
    });
    </script>
  <?php
      list_categories_options();

      exit;
    }
  }
  else {
    $query = "DELETE FROM `categories_options` WHERE `option_id` = '$option_id'";
    $all_queries .= $query."\n<br>";
    //echo $query;exit;
    $result = mysqli_query($db_link, $query);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages['sql_error_delete']." - 1 `categories_options` ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
  }
  
  $query = "DELETE FROM `options` WHERE `option_id` = '$option_id'";
  $all_queries .= $query."\n<br>";
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages['sql_error_delete']." - 3 `options` ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
  
  $query = "DELETE FROM `option_description` WHERE `option_id` = '$option_id'";
  $all_queries .= $query."\n<br>";
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages['sql_error_delete']." - 4 `option_description`".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
  
  $query = "DELETE FROM `option_to_category` WHERE `option_id` = '$option_id'";
  $all_queries .= $query."\n<br>";
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages['sql_error_delete']." - 5 `option_to_category` ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
  
  $query_select_ovd = "SELECT `option_value_id` FROM `option_value` WHERE `option_id` = '$option_id' LIMIT 1";
  $all_queries .= $query_select_ovd."\n<br>";
  //echo $query;exit;
  $result_select_ovd = mysqli_query($db_link, $query_select_ovd);
  if(mysqli_num_rows($result_select_ovd) > 0) {
    
    $query = "DELETE FROM `option_value` WHERE `option_id` = '$option_id'";
    $all_queries .= $query."\n<br>";
    //echo $query;exit;
    $result = mysqli_query($db_link, $query);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages['sql_error_delete']." - 6 `option_value` ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
    
    $query = "DELETE FROM `option_value_description` WHERE `option_id` = '$option_id'";
    $all_queries .= $query."\n<br>";
    //echo $query;exit;
    $result = mysqli_query($db_link, $query);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages['sql_error_delete']." - 7 `option_value_description` ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
  }
  
  //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
  mysqli_query($db_link,"COMMIT");

  list_categories_options();
?>