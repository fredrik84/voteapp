<?php
   try {
      // Include all needed classes/libraries/configuration
      include("lib/header.php");
      // Initiate the site object
      $vote = new init();
   } catch(Exception $e) {
      echo $e->getMessage();
   }
?>
