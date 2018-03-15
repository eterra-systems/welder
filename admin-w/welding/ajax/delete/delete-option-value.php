<?php
  
  include_once '../../../../config.php';
  include_once "../../../../languages/languages_$current_lang.php";
  include_once '../../../functions/include-functions.php';
  
  check_for_csrf();
  
  //print_r($_POST);exit;
  if(isset($_POST['option_value_id'])) {
    $option_value_id = $_POST['option_value_id'];
  }
  if(isset($_POST['option_value_row'])) {
    $option_value_row = $_POST['option_value_row'];
  }
  if(isset($_POST['step'])) {
    $step = $_POST['step'];
  }
  
  /*
   * we gonna use a variable step, wich if equals to first
   * we gonna check if some categories have this option value and
   * if so tell the user and then if is sure to delete it (second step)
   * delete the option value with it's records for the categories
   */
  
  mysqli_query($db_link,"BEGIN");
  $all_queries= "";
  
  if($step == "first") {
    $query_pov = "SELECT `option_value_id` FROM `categories_options` WHERE `option_value_id` = '$option_value_id' LIMIT 1";
    //echo $query_pov;
    $result_pov = mysqli_query($db_link, $query_pov);
    if(!$result_pov) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_pov) > 0) {
  ?>
    <div style="display:none;" id="modal_confirm_delete_option_value" class="clearfix" title="<?=$languages['text_are_you_sure']?>">
      <p style="padding:0;margin:0;width:100%;float:left;">
        <?=$languages['warning_option_value_is_assigned_to_categories'];?>
      </p>
    </div>
    <input type="hidden" name="current_option_value_id" class="delete_option_value_link active" data-id="<?=$option_value_id;?>" data-row="<?=$option_value_row;?>" >
    <script>
    $(function() {
      $("#modal_confirm_delete_option_value").dialog({
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
            DeleteOptionValue('second');
          },
          "<?=$languages['btn_cancel'];?>": function() {
            $(".delete_option_value_link").removeClass("active");
            $(this).dialog("close");
          }
        }
      });
      $("#modal_confirm_delete_option_value").dialog("open");
    });
    </script>
  <?php
      exit;
    }
  }
  else {
    $query = "DELETE FROM `categories_options` WHERE `option_value_id` = '$option_value_id'";
    $all_queries .= $query."\n<br>";
    //echo $query;exit;
    $result = mysqli_query($db_link, $query);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages['sql_error_delete']." - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
  }
  
  $query = "DELETE FROM `option_value` WHERE `option_value_id` = '$option_value_id'";
  $all_queries .= $query."\n<br>";
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages['sql_error_delete']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
  
  $query = "DELETE FROM `option_value_description` WHERE `option_value_id` = '$option_value_id'";
  $all_queries .= $query."\n<br>";
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages['sql_error_delete']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
  
  //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
  mysqli_query($db_link,"COMMIT");
?>