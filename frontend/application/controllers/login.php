<?php
   include("config.php");

   if(!empty($this->_POST['ean_code'])) {
      if(!$tmp = $this->security->login($this->_POST['ean_code']))
         $error_message = "Bracelet code is not correct or failed to verify";
      else {
         $json_array = array("call" => "contributers",
                             "action" => "status",
                             "ean_code" => $this->_POST['ean_code']);
         $this->queryApi($json_array);
         if(count($this->query[0]) == 0)
            if(!isset($this->_GET['return']))
               header("Location: ".$this->base_url.$this->_CONFIG['default_frontend_page']."/");
            else
               header("Location: ".$this->base_url.$this->_GET['return']."/");
         else
            header("Location: ".$this->base_url."signup/?ean_code=".$this->_POST['ean_code']."");
      }
   } else {
      $error_message = "Bracelet code is missing";
   }
?>
