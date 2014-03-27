<?php
   if(isset($this->_GET['id'])) {
      if($this->_GET['approved'] == "true")
         $approved = 1;
      else
         $approved = 0;
      $json_data = array("call" => "contributions",
                         "action" => "update",
                         "contribution_id" => $this->_GET['id'],
                         "approved" => $approved);
      $this->queryApi($json_data);
   }

   if(isset($this->_POST['contribution_id'])) {
      if(isset($this->_POST['new'])) {
         $json_array = array("call" => "contributions",
                             "action" => "register");
         unset($this->_POST['new']);
      } else {
         $json_array = array("call" => "contributions",
                             "action" => "update");
      }
      unset($this->_POST['submit']);
      $json_array = array_merge($json_array, $this->_POST);
      $this->queryApi($json_array);
   }

   if(isset($this->_GET['action'])) {
      if($this->_GET['action'] == "approved") {
         $json_array = array("call" => "contributions",
                             "action" => "update");
      } else {
         $json_array = array("call" => "competitions",
                             "action" => "update");
      }
      $this->_GET[$this->_GET['action']] = $this->_GET['value'];
      unset($this->_GET['action']);
      unset($this->_GET['value']);
      $json_array = array_merge($json_array, $this->_GET);
      unset($this->_GET);
      $this->queryApi($json_array);
   }

   $json_array = array("call" => "events",
                       "action" => "current");
   $this->queryApi($json_array);
   $this->event_name = $this->query[0]->event_name;


   $json_array = array("call" => "competitions",
                       "action" => "get");
   $this->queryApi($json_array);
   foreach($this->query as $key => $value) {
      $competitions[$value->competition_id] = $value;
      $value = (Array) $value;
      $competition_array[$value['competition_id']] = $value['name'];
      $competition_array_r[$value['name']] = $value['competition_id'];
   }

   $json_array = array("call" => "contributions",
                       "action" => "get");
   $this->queryApi($json_array);
   foreach($this->query as $key => $value) {
      $value = (Array) $value;
      $this->contributions[$competition_array[$value['competition_id']]][] = $value;
   }
   foreach($this->contributions as $compoid => $compo) {
      foreach($compo as $ekey => $entry) {
         if(isset($entry['approved']))
            $this->completed_compo[$compoid]++;
      }
   }
?>
