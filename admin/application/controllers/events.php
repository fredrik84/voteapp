<?php
   if(isset($this->_GET['action'])) {
      if($this->_GET['action'] == "delete") {
         $json_array = array("call" => "events",
                             "action" => "unregister",
                             "event_id" => $this->_GET['event_id']);
      } else {
         $json_array = array("call" => "events",
                             "action" => "update");
         $this->_GET[$this->_GET['action']] = $this->_GET['value'];
         unset($this->_GET['action']);
         unset($this->_GET['value']);
         $json_array = array_merge($json_array, $this->_GET);
      }
      $this->queryApi($json_array);
   }

   if(isset($this->_POST['event_id'])) {
      if(isset($this->_POST['new'])) {
         $json_array = array("call" => "events",
                             "action" => "register");
         unset($this->_POST['new']);
      } else {
         $json_array = array("call" => "events",
                             "action" => "update");
      }
      unset($this->_POST['submit']);
      $json_array = array_merge($json_array, $this->_POST);
      $this->queryApi($json_array);
   }

   $json_array = array("call" => "events",
                       "action" => "get");

   $this->queryApi($json_array);
   if(!isset($this->query[0])) {
      $tmp[] = $this->query;
      $this->query = $tmp;
   }
   
?>
