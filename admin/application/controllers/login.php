<?php
   include("config.php");


   if(!empty($this->_POST['username']) && !empty($this->_POST['password'])) {
      if(!$this->security->login($this->_POST['username'], $this->_POST['password']))
         $error_message = "Username or password is wrong";
      else {
         header("Location: ".$this->base_url.$this->_CONFIG['default_page']."/");
      }
   } else {
      $error_message = "Username or password missing";
   }
?>
