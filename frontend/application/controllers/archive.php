<?php
   $json_array = array("call" => "events",
                       "action" => "current");
   $this->queryApi($json_array);
   $event_id = $this->query[0]->event_id;
   if(isset($this->_GET['event_id']) && $this->_GET['event_id'] != $this->query[0]->event_id) {
      $json_array = array("call" => "competitions",
                          "action" => "get",
                          "event_id" => $this->_GET['event_id'],
                         );
   } else {
      $json_array = array("call" => "competitions",
                          "action" => "archive");
   }
   $this->queryApi($json_array);

   if(isset($this->query[0]->name)) {
      foreach($this->query as $key_num => $compo) {
         $compo = (array) $compo;
         $desc = explode("\n", $compo['description']);
         unset($rules);
         foreach($desc as $key => $value) {
            if(preg_match("/^\s+\*/", $value)) {
               $rules[] = $value;
               unset($desc[$key]);
            }
         }
         if(empty($rules))
            $rules = array();
         $compo['description'] = implode("\n", $desc);
         $compo['rules'] = $rules;
         $compos[] = $compo;
      }
   }
   
?>
