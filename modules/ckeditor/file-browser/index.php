<!DOCTYPE html>
<html>
  <head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <title>File browser</title>

    <!-- Include our stylesheet -->
    <link href="assets/css/styles.css" rel="stylesheet"/>

  </head>
  <body>

    <div class="filemanager">

      <div class="search">
        <input type="search" placeholder="Find a file.." />
      </div>

      <div class="breadcrumbs"></div>

      <ul class="data">
        <?php
          include_once 'scan.php';
        ?>
      </ul>

      <div class="nothingfound">
        <div class="nofiles"></div>
        <span>No files here.</span>
      </div>

    </div>

    <!-- Include our script files -->
    <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script>
      $(function() {
        $("ul.data a").bind('click', function() {
          var funcNum = getUrlParam( 'CKEditorFuncNum' );
          var fileUrl = $(this).attr("href");
          //alert(fileUrl);return false;
          window.opener.CKEDITOR.tools.callFunction( funcNum, fileUrl );
          window.close();
        });
      });
      // Helper function to get parameters from the query string.
      function getUrlParam( paramName ) {
          var reParam = new RegExp( '(?:[\?&]|&)' + paramName + '=([^&]+)', 'i' );
          var match = window.location.search.match( reParam );

          return ( match && match.length > 1 ) ? match[1] : null;
      }
      // Simulate user action of selecting a file to be returned to CKEditor.
      function returnFileUrl(fileUrl) {
          var funcNum = getUrlParam( 'CKEditorFuncNum' );
          //var fileUrl = link.attr("href");
          window.opener.CKEDITOR.tools.callFunction( funcNum, fileUrl );
          window.close();
      }
    </script>

  </body>
</html>