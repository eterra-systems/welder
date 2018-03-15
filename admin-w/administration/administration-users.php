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
      
      <section class="contents_options" style="padding-left: 21%;display: none;">
        <a class="pageoptions add_new_link" href="/<?=$_SESSION['admin_dir_name'];?>/administration/administration-users-add-new.php">
          <img src="/<?=$_SESSION['admin_dir_name'];?>/images/newobject.gif" class="systemicon" width="16" height="16" alt="<?=$languages['text_add_new_user'];?>" />
          <?=$languages['text_add_new_user'];?>
        </a>
      </section>

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
          if (!$users_result) echo mysqli_error($db_link);
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

      <div id="right_column" class="list_container" style="display: none;">
        <table>
          <thead>
            <tr>
              <th width="5%"><?=$languages['btn_save'];?></th>
              <th width="15%"><?=$languages['header_user_username'];?></th>
              <th width="10%"><?=$languages['header_user_password'];?></th>
              <th width="10%"><?=$languages['header_user_firstname'];?></th>
              <th width="10%"><?=$languages['header_user_lastname'];?></th>
              <th width="9%"><?=$languages['header_user_rights'];?></th>
              <th width="5%"><?=$languages['header_user_is_active'];?></th>
              <th width="5%"><?=$languages['header_user_logs'];?></th>
              <th width="5%"><?=$languages['header_user_is_ip_in_use'];?></th>
              <th width="5%"><?=$languages['header_user_reset_ip'];?></th>
              <th width="5%"><?=$languages['btn_delete']; ?></th>
            </tr>
          </thead>
        </table>
        <div id="users_list" class="list_container">

        </div>
      </div>
      
      <div class="clearfix"></div>
      <script type="text/javascript">
        $(document).ready(function() {
          $("#choose_user_type a").click(function() {
            $("#choose_user_type td").removeClass("selected_user_type")
            $(this).parent().addClass("selected_user_type");
            GetUsersForType();
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