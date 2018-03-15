<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['news_id'])) {
    $current_news_id =  $_POST['news_id'];
  }
  
  $news_names_array = get_news_images($current_news_id);
  $news_images_folder = "/site/images/news/thumbs/";
?>
  <h2><?=$languages['header_news_images'];?></h2>
  <p><i><?=$languages['info_image_default'];?></i></p>
  <ul id="sortable">
<?php
  if(isset($news_names_array)) {

    foreach($news_names_array as $news_gallery_image) {
      //echo"<pre>";print_r($prod_gallery_image);
      $gallery_img_id = $news_gallery_image['news_gallery_id'];
      $gallery_image = $news_gallery_image['ng_name'];
      $gallery_img_path_small = $news_images_folder.$gallery_image;
      @$gallery_img_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$gallery_img_path_small);
      $gallery_img_dimensions = $gallery_img_params[3];
?>
      <li id="gallery_image_<?=$gallery_img_id?>" data-id="<?=$gallery_img_id?>" class="ui-state-default">
        <input type="button" class="delete_img" data-id="<?=$gallery_img_id?>" data-image="<?=$gallery_image?>" data-type="2" value="<?=$languages['btn_delete'];?>">
        <a class="move_img"><?=$languages['btn_move'];?></a>
        <div class="clearfix"></div>
        <img src="<?=$gallery_img_path_small?>" class="dbx-handle" />
      </li>
<?php
    }
  }
?>
  </ul>
  <div class="clearfix"></div>
  <p>&nbsp;</p>
  
  <h2><?=$languages['header_add_images'];?></h2>
  <script type="text/javascript">
    $(document).ready(function() {
      $("#sortable").sortable({
        placeholder: "ui-state-highlight"
      });
      $("#sortable").disableSelection();
      
      $("#modal_confirm_delete_img").dialog({
        resizable: false,
        width: 400,
        height: 200,
        autoOpen: false,
        modal: true,
        draggable: false,
        closeOnEscape: true,
        dialogClass: "modal_confirm_delete_img",
        buttons: {
          "<?=$languages['btn_delete'];?>": function() {
            var image_id = $(".delete_img.active").attr("data-id");
            var image_data = $(".delete_img.active").attr("data-image");
            var image_type = $(".delete_img.active").attr("data-type");
            //alert(image_data);
            DeleteNewsImage(image_id,image_data,image_type);
          },
          "<?=$languages['btn_cancel'];?>": function() {
            $(this).dialog("close");
          }
        }
      });

      $(".delete_img").click(function() {
        $(".delete_img").removeClass("active");
        $(this).addClass("active");
        $("#modal_confirm_delete_img").dialog("open");
      });
    });
  </script>