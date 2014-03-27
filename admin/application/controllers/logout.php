<?php
   unset($_SESSION);
   session_destroy();
   header("Location: $this->base_url");
?>
