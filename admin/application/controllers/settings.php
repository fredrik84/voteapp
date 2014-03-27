<?php
   if(isset($this->_POST['name'])) {
      if(isset($this->_POST['new'])) {
         $json_array = array("call" => "configurations",
                             "action" => "register");
         unset($this->_POST['new']);
         if($this->_POST['name'] == "allowed_to_query") {
            $allowed = preg_split("/\s*,\s*/", $this->_POST['value']);
            $tmp = serialize($allowed);

         }
      } else {
         $json_array = array("call" => "configurations",
                             "action" => "update");
      }
      unset($this->_POST['submit']);
      $this->_POST['value'] = serialize($this->_POST['value']);
      $json_array = array_merge($json_array, $this->_POST);
      $this->queryApi($json_array);
   }

   if(isset($this->_GET['action']) && isset($this->_GET['configuration_id'])) {
      if($this->_GET['action'] == "delete") {
         $json_array = array("call" => "configurations",
                             "action" => "unregister",
                             "configuration_id" => $this->_GET['configuration_id']);
      } else {
         $json_array = array("call" => "configurations",
                             "action" => "update");
         $this->_GET[$this->_GET['action']] = $this->_GET['value'];
         unset($this->_GET['action']);
         unset($this->_GET['value']);
         $json_array = array_merge($json_array, $this->_GET);
      }
      $this->queryApi($json_array);
   }


   $json_array = array("call" => "configurations",
                       "action" => "get");
   $this->queryApi($json_array);
   if(empty($this->query[0])) {
      $tmp = (array) $this->query;
      unset($this->query);
      $this->query[] = $tmp;
   }

   foreach($this->query as $key => $value) {
      $this->query[$key] = (array) $this->query[$key];
      $value = (array) $value;
      if(is_array($value)) {
         foreach($value as $key2 => $value2) {
            $value2 = (string) $value2;
            if(preg_match("/value/", $key2)) {
               $this->query[$key][$key2] = unserialize($value2);
               if(is_array($this->query[$key][$key2]))
                  $this->query[$key][$key2] = implode(", ", $this->query[$key][$key2]);
            }
         }
      } else {
         if(preg_match("/value/", $key)) {
            $this->query[$key] = unserialize($value);
            if(is_array($this->query[$key]))
               $this->query[$key] = implode(", ", $this->query[$key]);
         }
      }
      if($this->_GET['configuration_id'] == $value['configuration_id']) {
         $script = $value;
         break;
      }
   }

?>
