  <?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
 
  $page_title = $languages['text_users'];
  $page_description = $languages['company_name']." администрация";
  
  print_html_admin_header($page_title, $page_description);
?>
<!--main-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages['text_users'];?>
      </section>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>

      <!--begin of left_col-->
      <div id="left_column">
        <table id="choose_user_type" class="list_container margin_bottom">
          <thead>
            <tr><th><?=$languages['header_choose_user_group'];?></th></tr>
          </thead>
          <tbody>
<?php
          $user_type_is_superuser = $_SESSION['admin']['user_type_is_superuser'];
          $and_superuser_only = ($user_type_is_superuser == 1) ? "" : " AND `users_types`.`user_type_is_superuser` = '0' ";
          
          $query = "SELECT `users_types`.`user_type_id`,`users_types_descriptions`.`user_type_name` 
                    FROM `users_types` 
                    INNER JOIN `users_types_descriptions` ON `users_types_descriptions`.`user_type_id` = `users_types`.`user_type_id`
                    WHERE `users_types_descriptions`.`language_id` = '$current_language_id' $and_superuser_only
                    ORDER BY `users_types`.`user_type_sort_order` ASC";
          //echo $query;
          $users_result = mysqli_query($db_link, $query);
          if(!$users_result) echo mysqli_error($db_link);
          if(mysqli_num_rows($users_result) > 0) {
            while ($user_details = mysqli_fetch_assoc($users_result)) {
              $user_type_id = $user_details['user_type_id'];
              $user_type_name = $user_details['user_type_name'];

              echo "<tr><td class='text_left'><a data-id='$user_type_id' class='red_link'>$user_type_name</a></td></tr>";
            }
          }
          else {   
?>
            <tr><td><?=$languages['no_user_types_yet'];?></td></tr>
<?php    
          }
?>
          </tbody>
        </table>

      </div>
      <!--end of left_col-->

      <div id="right_column" class="list_container">
        <form method="post" name="edit_users_type_default_rights" action="javascript:;">
          <table id="users_type_default_rights">
            <thead>
              <tr>
                <th width="25%" style="text-align: left;"><?=$languages['header_user_rights_page'];?></th>
                <th width="15%"><?=$languages['header_user_rights_page_access'];?></th>
                <th width="15%"><?=$languages['header_user_rights_page_add'];?></th>
                <th width="15%"><?=$languages['header_user_rights_page_edit'];?></th>
                <th width="15%"><?=$languages['header_user_rights_page_delete'];?></th>
                <th width="15%"><?=$languages['header_user_rights_page_subpages'];?></th>
              </tr>
            </thead>
            <tbody>

            </tbody>
          </table>
        </form>
        <p>&nbsp;</p>
        <a href="javascript:;" onclick="" class="users_type_default_rights button green" style="display: none;">
          <i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?>
        </a>
      </div>
      
      <div class="clearfix"></div>
      <script type="text/javascript">
        $(document).ready(function() {
          $("#choose_user_type a").click(function() {
            $("#choose_user_type td").removeClass("selected_user_type")
            $(this).parent().addClass("selected_user_type");
            GetUsersTypesDefaultRights();
          });
        });
      </script>
    </div>
  </main>
<!--main-->
<?php 
    print_html_admin_footer();
?>
</body>
</html>