<?php
   $required = array("username", "firstname", "lastname", "email", "telephone", "address", "country");
   if(isset($this->_POST['submit'])) {
      $required = array("username", "firstname", "lastname", "email", "telephone", "address", "country");
      $missed = array();
      foreach($required as $key => $value) 
         if(empty($this->_POST[$value])) 
            $missed[] = $value;
      if(count($missed) == 0) {
         $json_array = array("call" => "contributers",
                             "action" => "update",
                             "ean_code" => $this->_GET['ean_code']);
         unset($this->_POST['submit']);
         foreach($this->_POST as $key => $value) 
            $json_array[$key] = $value;
         $json_array['ean_code'] = $this->_GET['ean_code'];

         $this->queryApi($json_array);
         header("Location: $this->base_url".$this->_CONFIG['default_frontend_page']."/");
      }
   }

   if(isset($this->_GET['justvoting']) && isset($this->_GET['ean_code'])) {
      $json_array = array("call" => "contributers",
                          "action" => "update",
                          "ean_code" => $this->_GET['ean_code'],
                          "voter_only" => 1);
      $this->queryApi($json_array);
      header("Location: ".$this->base_url."Vote/");
   }

   $json_array = array("call" => "contributers",
                       "action" => "status",
                       "ean_code" => $this->_GET['ean_code']);
   $this->queryApi($json_array);
?>
