<!DOCTYPE HTML>
<html>
<head>
<title><?=$this->_CONFIG['website_title'];?> ADMIN</title>
<meta charset="utf-8">
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,700|Open+Sans+Condensed:700" rel="stylesheet">
<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script src="<?=$this->base_url?>css/5grid/init.js?use=mobile,desktop,1000px&amp;mobileUI=1&amp;mobileUI.theme=none&amp;mobileUI.titleBarOverlaid=1"></script>
<script type="text/javascript" src="<?=$this->base_url?>js/jquery.fancybox.js?v=2.1.5"></script> 
<script type="text/javascript" language="javascript">
$(document).ready(function(){
     $("#flip").click(function(){
            $("#panel").slideToggle("slow");
              });
     });
</script>
<script type="text/javascript">
   $(document).ready(function() {
      $(".fancybox").fancybox({
         openEffect  : 'none',
         closeEffect : 'none'
      });
   });
</script>

<noscript>
<link rel="stylesheet" href="<?=$this->base_url?>css/5grid/core.css">
<link rel="stylesheet" href="<?=$this->base_url?>css/5grid/core-desktop.css">
<link rel="stylesheet" href="<?=$this->base_url?>css/5grid/core-1200px.css">
<link rel="stylesheet" href="<?=$this->base_url?>css/5grid/core-noscript.css">
<link rel="stylesheet" href="<?=$this->base_url?>css/style-1000px.css">
<link rel="stylesheet" href="<?=$this->base_url?>css/style-desktop.css">
<link rel="stylesheet" href="<?=$this->base_url?>css/voteapp.css">
</noscript>
<link rel="stylesheet" type="text/css" href="<?=$this->base_url?>css/jquery.fancybox.css?v=2.1.5" media="screen" />
<style type="text/css"> 
#panel
{
   padding: 5px;
   background-color:#f5ffff;
   border:solid 1px #f3f3f3;
}
#flip
{
   padding:5px;
   text-align: center;
   background-color:#f5ffff;
   border:solid 1px #f3f3f3;
}
#panel
{
   padding:50px;
   display:none;
}
</style>
<!--[if lte IE 9]>
<link rel="stylesheet" href="css/ie9.css">
<![endif]-->
<!--[if lte IE 8]>
<link rel="stylesheet" href="css/ie8.css">
<![endif]-->
<!--[if lte IE 7]>
<link rel="stylesheet" href="css/ie7.css">
<![endif]-->
</head>
<body class="homepage">
<nav id="nav" class="mobileUI-site-nav">
  <ul>
   <?php
      $menu = array("" => "Home",
                    "Events/" => "Events",
                    "Competitions/" => "Competitions",
                    "Contributions/" => "Contributions",
                    "Results/" => "Results",
                    "Users/" => "Users",
                    "News/" => "News",
                    "Settings/" => "Settings",
                    "Logout/" => "Logout",
                   );
      foreach($menu as $url => $item) {
         if(empty($this->_CONFIG['menu_access'][$item]))
            $this->_CONFIG['menu_access'][$item] = $this->_CONFIG['default_access'];
         if($this->_CONFIG['menu_access'][$item] > $this->security->access)
            continue;
         if(strtolower($url) == $this->page."/") {
            echo "<li class='current_page_item'><a href='".$this->base_url.$url."'>$item</a></li>\n";
         } else {
            echo "<li><a href=\"".$this->base_url.$url."\">$item</a></li>\n";
         }
      }
   ?>
  </ul>
</nav>
<div id="main-wrapper">
   <div id="main"  class="5grid-layout">
      <div class="row">
<!--         <div class="9u mobileUI-main-content">-->
