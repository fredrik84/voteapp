<?php
   class security extends common {
      public function __construct($_CONFIG, $method = NULL) {
         switch($method) {
            case "INIT":
               $this->remoteProfile();
               $this->logAccess();
               $this->checkAllowedAccess($_CONFIG);
               break;
            default:
               $this->remoteProfile();
               $this->args();
         }
         return true;
      }

      // Schnygga till input automagiskt :)
      public function __set($name, $value) {
         if(!is_array($value) && !is_object($value)) {
            if(__MYSQL_IN_USE__)
               $value = mysql_real_escape_string($value);
            $value = strip_tags($value, $this->except_list);
            $value = html_entity_decode($value);
         } else {
            if(!is_object($value)) {
               foreach($value as $key => $str) {
                  if(is_array($str))
                     return false;
                  if(__MYSQL_IN_USE__)
                     $str = mysql_real_escape_string($str);
                  $str = strip_tags($str, $this->except_list);
                  $value[$key] = html_entity_decode($str);
               }
            }
         }
         $this->$name = $value;
         return true;
      }

      public function checkAllowedAccess($_CONFIG) {
         if(!in_array($this->remote_address, $_CONFIG['allowed_to_query'])) {
            init::log("Access denied for $this->remote_address", 1);
            throw new Exception("Access denied\n");
         }
         if($_CONFIG['require_ssl'] == "true") {
            global $_SERVER;
            if(empty($_SERVER['HTTPS']))
               throw new Exception("We require SSL!");
         }
      }

      public function args() {
         global $_REQUEST;
         global $_GET;
         global $_POST;
         if(!empty($_GET)) 
            $this->_GET = $_GET;
         else
            $this->_POST = $_POST;
         $this->_REQUEST = $_REQUEST;
         return $this->_REQUEST;
      }

      public function remoteProfile() {
         $this->remote_address = $_SERVER['REMOTE_ADDR'];
         $this->user_agent = $_SERVER['HTTP_USER_AGENT'];
         $this->remote_port = $_SERVER['REMOTE_PORT'];
         $this->server_protocol = $_SERVER['SERVER_PROTOCOL'];
         $this->request_uri = $_SERVER['REQUEST_URI'];
         $this->request_time = $_SERVER['REQUEST_TIME'];
         $this->request_method = $_SERVER['REQUEST_METHOD'];
      }

      public function logAccess() {
         init::log("Access $this->request_uri from $this->remote_address/$this->server_protocol", 4);
         init::log("Using '$this->user_agent'", 7);
      }

      public function __destruct() {
         unset($this);
         return true;
      }
   }
?>
