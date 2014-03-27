<?php
   if(isset($this->_GET['action']) && isset($this->_GET['user_id'])) {
      if($this->_GET['action'] == "delete") {
         $json_array = array("call" => "users",
                             "action" => "unregister",
                             "user_id" => $this->_GET['user_id']);
      } else {
         $json_array = array("call" => "users",
                             "action" => "update");
         $this->_GET[$this->_GET['action']] = $this->_GET['value'];
         unset($this->_GET['action']);
         unset($this->_GET['value']);
         $json_array = array_merge($json_array, $this->_GET);
      }
      $this->queryApi($json_array);
   }

   if(isset($this->_POST['user_id'])) {
      if(isset($this->_POST['new'])) {
         $json_array = array("call" => "users",
                             "action" => "register");
         unset($this->_POST['new']);
      } else {
         $json_array = array("call" => "users",
                             "action" => "update");
      }
      unset($this->_POST['submit']);
      $json_array = array_merge($json_array, $this->_POST);
      $this->queryApi($json_array);
   }

   if(isset($this->_GET['action']) && isset($this->_GET['contributer_id'])) {
      if($this->_GET['action'] == "delete") {
         $json_array = array("call" => "contributers",
                             "action" => "unregister",
                             "contributer_id" => $this->_GET['contributer_id']);
      } else {
         $json_array = array("call" => "contributers",
                             "action" => "update");
         $this->_GET[$this->_GET['action']] = $this->_GET['value'];
         unset($this->_GET['action']);
         unset($this->_GET['value']);
         $json_array = array_merge($json_array, $this->_GET);
      }
      $this->queryApi($json_array);
   }

   if(isset($this->_POST['contributer_id'])) {
      if(isset($this->_POST['new'])) {
         $json_array = array("call" => "contributers",
                             "action" => "register");
         unset($this->_POST['new']);
      } else {
         $json_array = array("call" => "contributers",
                             "action" => "update");
      }
      unset($this->_POST['submit']);
      $json_array = array_merge($json_array, $this->_POST);
      $this->queryApi($json_array);
   }

   $json_array = array("call" => "users",
                       "action" => "get");
   $this->queryApi($json_array);
   if(empty($this->query[0])) {
      $this->users_list[] = (array) $this->query;
   } else {
      $this->users_list = $this->query;
   }

   $json_array = array("call" => "contributers",
                       "action" => "get");
   $this->queryApi($json_array);
   if(empty($this->query[0])) {
      $this->contributers_list[] = (array) $this->query;
   } else {
      $this->contributers_list = $this->query;
   }
?>
