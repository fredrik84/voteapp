<?php
   if(isset($this->_POST['submit'])) {
      if(isset($this->_POST['new_post'])) {
         $json_array = array("call" => "news",
                             "action" => "register");
         unset($this->_POST['new_post']);
         unset($this->_POST['submit']);
         unset($this->_POST['new_id']);
      } else {
         $json_array = array("call" => "news",
                             "action" => "update",
                             "new_id" => $this->_POST['new_id']);
         unset($this->_POST['new_id']);
         unset($this->_POST['submit']);
      
      }
      foreach($this->_POST as $key => $value) 
         $json_array[$key] = $value;
      $this->queryApi($json_array);
   }

   if(isset($this->_GET['delete'])) {
      $json_array = array("call" => "news",
                          "action" => "unregister",
                          "new_id" => $this->_GET['delete']);
      $this->queryApi($json_array);
   }
?>
