<!DOCTYPE HTML>
<!--
   Halcyonic 3.1 by HTML5 UP
   html5up.net | @n33co
   Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
   <head>
      <title><?=$this->_CONFIG['website_title']?></title>
      <meta http-equiv="content-type" content="text/html; charset=utf-8" />
      <meta name="description" content="" />
      <meta name="keywords" content="" />
      <script src="<?=$this->base_url;?>js/other/prototype.js"></script> 
      <script src="<?=$this->base_url;?>js/other/skel.min.js"></script>
      <script src="<?=$this->base_url;?>js/other/skel-panels.min.js"></script>

<?php
   if($this->page == "vote") {
?>
      <script>
         window._skel_config = {
            preset: 'standard',
            prefix: '<?=$this->base_url?>css/bootstrap/style',
            resetCSS: true,
            breakpoints: {
               '1000px': {
                  grid: {
                     gutters: 25
                  }
               }
            }
         };

         window._skel_panels_config = {
            preset: 'standard'
         };
      </script>
      <script>
         function hilightStar(contribution_id, star_id) {
            for(i = 1;i <= 5;i++) {
               $('star' + contribution_id + '[' + i + ']').src = '<?=$this->base_url?>images/vote_unfilled.png';
            }
            for(i = 1;i <= star_id;i++) {
               $('star' + contribution_id + '[' + i + ']').src = '<?=$this->base_url?>images/vote_filled.png';
            }  
         }

         function dehilightStars(contribution_id) {
            for(i = 1;i <= 5;i++) {
               $('star' + contribution_id + '[' + i + ']').src = '<?=$this->base_url?>images/vote_unfilled.png';
            }
            for(i = 1;i <= contributions[contribution_id];i++) {
               $('star' + contribution_id + '[' + i + ']').src = '<?=$this->base_url?>images/vote_filled.png';
            }
         }

         function castVote(contribution_id, star_id) {
            for(i = 1;i <= star_id;i++) {
               $('star' + contribution_id + '[' + i + ']').src = '<?=$this->base_url?>images/vote_filled.png';
            }
            new Ajax.Request('<?=$this->base_url?>castvote/?contribution_id=' + contribution_id + '&value=' + star_id, { method:'get' });
            contributions[contribution_id] = star_id;
         }

         var contributions = new Array();
      </script>
<?php } ?>
      <link rel="stylesheet" href="<?=$this->base_url;?>css/bootstrap/skel-noscript.css" />
      <link rel="stylesheet" href="<?=$this->base_url;?>css/bootstrap/bootstrap.min.css"> 
      <link rel="stylesheet" href="<?=$this->base_url;?>css/bootstrap/style.css" />
      <link rel="stylesheet" href="<?=$this->base_url;?>css/bootstrap/style-desktop.css" />
      <link rel="stylesheet" href="<?=$this->base_url;?>css/fileupload/jquery.fileupload.css">
      <link rel="stylesheet" type="text/css" href="<?=$this->base_url?>css/fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />
      <style>
      pre {
       white-space: pre-wrap;       /* css-3 */
       white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
       white-space: -pre-wrap;      /* Opera 4-6 */
       white-space: -o-pre-wrap;    /* Opera 7 */
       word-wrap: break-word;       /* Internet Explorer 5.5+ */
      }
      </style>
      <!--[if lte IE 9]><link rel="stylesheet" href="<?=$this->base_url;?>css/bootstrap/ie9.css" /><![endif]-->
      <!--[if lte IE 8]><script src="<?=$this->base_url;?>js/other/html5shiv.js"></script><![endif]-->
   </head>
   <body class="subpage">

      <!-- Header -->
         <div id="header-wrapper">
            <header id="header" class="container">
               <div class="row">
                  <div class="12u">

                     <!-- Logo -->
                     <h1><a href="#" id="logo"><?=$this->_CONFIG['website_title']?></a></h1>

                     <!-- Nav -->

<?php
   $menu_list = array("" => "Home",
                      "Vote/" => "Vote",
                      "Contribute/" => "Contribute",
                      "Rules/" => "Rules",
                      "Archive/" => "Archive",
                     );
   echo "<nav id=\"nav\">\n";
   foreach($menu_list as $path => $title) 
      echo "<a href='$this->base_url$path'>$title</a>\n";
   if(isset($_SESSION['contributer_id']))
      echo "<a href='".$this->base_url."Logout/'>Logout</a>\n";
   else
      echo "<a href='".$this->base_url."Login/'>Login</a>\n";
   echo "</nav>\n";
?>

                  </div>
               </div>
            </header>
         </div>

      <!-- Content -->
         <div id="content-wrapper">
            <div id="content">
               <div class="container">
                  <div class="row"> 
<?php
   if($this->page == "vote") {
?>
                     <div class="12u">
<?php } else { ?>
                     <div class="9u">
<?php } ?>
