<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  //echo"<pre>";print_r($_POST);echo"</pre>";exit;
  
  check_for_csrf();
    
  if(isset($_POST['content_id'])) {
    $current_content_id = $_POST['content_id'];
  }

  mysqli_query($db_link,"BEGIN");
  $all_queries = "";

  if(isset($_POST['include_blocks'])) {

    foreach($_POST['include_blocks'] as $menu_id => $include_blocks) {

      $blocks_ids_string = implode(",", $include_blocks);
      if($_POST['content_inlude_blocks_record_exists'][$menu_id] == 1) {
        $query_update_blocks = "UPDATE `contents_inlude_blocks` SET `blocks_ids_string`='$blocks_ids_string' WHERE `content_id` = '$current_content_id' AND `menu_id` = '$menu_id'";
        $all_queries .= $query_update_blocks."<br>";
        //echo "$query_update_blocks<br>\n";
        $result_update_blocks = mysqli_query($db_link, $query_update_blocks);
        if(!$result_update_blocks) {
          echo $languages['sql_error_update']." - 2 UPDATE `contents_inlude_blocks` ".mysqli_error($db_link);
          exit;
        }
      }
      else {
        $query_insert_blocks = "INSERT INTO `contents_inlude_blocks`(`cib_id`, 
                                                                    `content_id`, 
                                                                    `menu_id`, 
                                                                    `blocks_ids_string`)
                                                              VALUES(NULL,
                                                                    '$current_content_id',
                                                                    '$menu_id',
                                                                    '$blocks_ids_string')";
        $all_queries .= $query_insert_blocks."<br>";
        mysqli_query($db_link, $query_insert_blocks);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages['sql_error_insert']." - 2 INSERT INTO `contents_inlude_blocks` ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
    }

  }
  else {

    foreach($_POST['content_inlude_blocks_record_exists'] as $menu_id => $content_inlude_blocks_record_exists) {

      /*
       * if $_POST['include_blocks'] is not set and $content_inlude_blocks_record_exists == 1,
       * that means include blocks was unchecked, so we have to delete record
       */
      if($content_inlude_blocks_record_exists == 1) {

        $query_delete = "DELETE FROM `contents_inlude_blocks` WHERE `content_id` = '$current_content_id' AND `menu_id` = '$menu_id'";
        $all_queries .= $query_delete."<br>";
        mysqli_query($db_link, $query_delete);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages['sql_error_delete']." - ".mysqli_error($db_link);
          exit;
        }
      }
    }
  }

  //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

  mysqli_query($db_link,"COMMIT");
?>