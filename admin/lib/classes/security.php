<?php
   class security extends common {
      public function __construct($base_url, $page) {
         $this->base_url = $base_url;
         $this->page = $page;
         global $_SESSION;
         $this->_SESSION = $_SESSION;
         $this->access = $_SESSION['access'];
         $this->user_id = $_SESSION['user_id'];
      }

      public function verifyAccess($access) {
         if(!isset($this->_SESSION['loggedin']) && $this->page != "login") {
            header("Location: ".$this->base_url."Login/");
         }
         if($this->_SESSION['access'] >= $access) {
            return true;
         } else {
            return false;
         }
      }

      public function login($username, $password) {
         global $_SESSION;
         $json_array = array("call" => "users",
                             "action" => "verify",
                             "username" => $username,
                             "password" => $password,);
         $this->queryApi($json_array);
         $this->status = $this->query[0];
         $json_array = array("call" => "users",
                             "action" => "get",
                             "username" => $username);
         $this->queryApi($json_array);
         if(empty($this->query[0])) {
            $tmp = (array) $this->query;
            unset($this->query);
            $this->query = $tmp;
         } else {
            if(count($this->query) == 1)
               $this->query = (array) array_shift($this->query);
         }

         $this->user_id = $this->query['user_id'];
         $this->access = $this->query['access'];
         if($this->status == "true") {
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $this->user_id;
            $_SESSION['access'] = $this->access;
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
