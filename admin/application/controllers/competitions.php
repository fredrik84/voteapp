<?php
   if(isset($this->_GET['action'])) {
      if($this->_GET['action'] == "delete") {
         $json_array = array("call" => "competitions",
                             "action" => "unregister",
                             "competition_id" => $this->_GET['competition_id']);
      } else {
         $json_array = array("call" => "competitions",
                             "action" => "update");
         $this->_GET[$this->_GET['action']] = $this->_GET['value'];
         unset($this->_GET['action']);
         unset($this->_GET['value']);
         $json_array = array_merge($json_array, $this->_GET);
      }
      $this->queryApi($json_array);
   }

   if(isset($this->_POST['competition_id'])) {
      if(isset($this->_POST['new'])) {
         $json_array = array("call" => "competitions",
                             "action" => "register");
         unset($this->_POST['new']);
      } else {
         $json_array = array("call" => "competitions",
                             "action" => "update");
      }
      unset($this->_POST['submit']);
      $json_array = array_merge($json_array, $this->_POST);
      $this->queryApi($json_array);
   }

   $json_array = array("call" => "events",
                       "action" => "get",
                       "enabled" => 1);
   $this->queryApi($json_array);
   $this->query = array_reverse($this->query);
   if(empty($this->query[0])) {
      $tmp = $this->query;
      $this->query[] = $tmp;
   }
   foreach($this->query as $key => $value) {
      $value = (array) $value;
      $event_id = $value['event_id'];
      if(!empty($event_id))
         break;
   }
   $this->event_id = $event_id;
   $json_array = array("call" => "competitions",
                       "action" => "get",
                       "event_id" => $event_id);

   $this->queryApi($json_array);
?>
