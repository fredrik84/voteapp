<?php
   if(!empty($_FILES['files']['name'])) {
      $upload = new upload();
   }
   if(!empty($_FILES['thumbnail']['name'])) {
      $upload = new upload('thumbnail');
   }

?>
