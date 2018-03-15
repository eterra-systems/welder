<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  if(isset($_GET['currency_id'])) {
    $current_currency_id = $_GET['currency_id'];
    
    include_once 'administration-currency-details.php';
  }
  else {
    
    $page_title = $languages['header_currency'];
    $page_description = $languages['e_shop_cms']." администрация";

    print_html_admin_header($page_title, $page_description);
?>
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages['header_currency'];?>
      </section>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>
      
      <section class="contents_options">
        <a class="pageoptions add_new_link" href="administration-currency-add-new.php" title="<?=$languages['title_add_new_currency'];?>">
          <img src="/<?=$_SESSION['admin_dir_name'];?>/images/newobject.gif" class="systemicon" width="16" height="16" alt="<?=$languages['alt_add_new_attributes_group'];?>" />
          <?=$languages['link_add_new_currency'];?>
        </a>
      </section>
      
      <table>
        <thead>
          <tr>
            <th width="20%" class="text_left"><?=$languages['header_currency_code'];?></th>
            <th width="20%" class="text_left"><?=$languages['header_name'];?></th>
            <th width="10%" class="text_left"><?=$languages['header_currency_exchange_rate'];?></th>
            <th width="5%"><?=$languages['header_is_active'];?></th>
            <th width="15%"><?=$languages['header_is_default'];?></th>
            <th width="10%" colspan="2"><?=$languages['header_actions'];?></th>
          </tr>
        </thead>
      </table>
      <div id="currencies_list" class="list_container">
<?php
        list_currencies();
?>
      </div>
    </div>
  </main>
<?php 
    print_html_admin_footer();
?>
</body>
</html>
<?php
 
  }