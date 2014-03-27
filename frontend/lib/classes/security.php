<?php
   class security extends common {
      public function __construct($base_url, $page) {
         $this->base_url = $base_url;
         $this->page = $page;
         global $_SESSION;
         $this->_SESSION = $_SESSION;
         $this->access = $_SESSION['access'];
         $this->user_id = $_SESSION['user_id'];
         $this->_SESSION['access'] = 5;
      }

      public function verifyBracelet($ean_code) {
         $json_array = array("call" => "events",
                             "action" => "current");
         $this->queryApi($json_array);
         $this->event_id = $this->query[0]->fake_event_id;
         $json_array = array("call" => "contributers",
                             "action" => "verify",
                             "ean_code" => $ean_code,
                             "fake_event_id" => $this->event_id);
         $this->queryApi($json_array);
         if($this->query[0] == "200 OK") {
            $this->status = true;
            return true;
         } else {
            $this->status = false;
            return false;
         }
      }

      // Disabled
      public function verifyAccess($access) {
         return true;
         if(!isset($this->_SESSION['loggedin']) && $this->page != "login") {
            header("Location: ".$this->base_url."Login/");
         }
         if($this->_SESSION['access'] >= $access) {
            return true;
         } else {
            return false;
         }
      }

      public function login($ean_code) {
         global $_SESSION;
         if(!$this->verifyBracelet($ean_code))
            return false;
         $json_array = array("call" => "contributers",
                             "action" => "getidfromean",
                             "ean_code" => $ean_code); 
         $this->queryApi($json_array);
         $this->ean_id = $this->query[0]->ean_id;
         if(!empty($this->query[0]->contributer_id))
            $this->status = true;
         else
            $this->status = false;
         if($this->status == "true") {
            $_SESSION['loggedin'] = true;
            $_SESSION['ean_id'] = $this->ean_id;
            $_SESSION['contributer_id'] = $this->query[0]->contributer_id;
            $_SESSION['voter_only'] = $this->query[0]->voter_only;
         } else {
            return false;
         }
         return true;
      }

      public function __destruct() {
         unset($this);
         return true;
      }  
   }
?>
