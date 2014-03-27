<?php
   include("lib/header.php");
   try {
      $base = new init();
   } catch (Exception $e) {
      echo $e->getMessage();
   }
?>
