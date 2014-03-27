<?php
   class init extends common {
      public function __construct() {
         $tmp = init::getConfig();
         foreach($tmp as $key => $value) 
            $this->_CONFIG[$key] = $value;
         global $_CONFIG;
         $_CONFIG = $this->_CONFIG;
         // new call functions needs to be added here (it's the class name, the file needs to be named the same aswell)
         // Should be changed to database managed, but we need a frontend for that first. (admin page!)
         $this->commands = array("users",
                                 "competitions",
                                 "events",
                                 "contributions",
                                 "contributers",
                                 "votes",
                                 "results",
                                 "configurations",
                                 "news",
                              );
         $this->args();
         if(!in_array(strtolower($this->_REQUEST['call']), $this->commands)) {
            init::log("Unknown call ".$this->_REQUEST['call'].", exiting", 1);
            throw new Exception("Unknown call ".$this->_REQUEST['call']);
         }
         $this->security = new security($_CONFIG, 'INIT');
         $this->init();
         $this->manage();
         return true;
      }

      public function init() {
         if(is_array($this->commands)) {
            foreach($this->commands as $class)
               $this->$class = new $class($this->_REQUEST);
         }
         $this->populate();
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

      static public function log($string, $verbosity) {
         global $_CONFIG;
         $string = mysql_real_escape_string($string);
         $security = new security($_CONFIG);
         $security->remoteProfile();
         //$list = explode(",", preg_replace("/\s+/", "", $_CONFIG['verbosity']));
         if(is_array($_CONFIG['verbosity'])) {
            if(in_array($verbosity, $_CONFIG['verbosity'])) {
               $call = $security->_REQUEST['call'];
               $q = "INSERT INTO logs 
                     SET date=now(), 
                         remote_addr='$security->remote_address', 
                         method_call='$call ($security->request_method)', 
                         severity='$verbosity', 
                         string='$string'";
               if(!mysql_query($q)) 
                  throw new Exception("Failed to query database: ".mysql_error()."\n<br>Query: $q\n<br>");
            }
         }
         return true;
      }

      public function populate() {
         $this->args();
         if(!empty($this->_REQUEST['event_id'])) {
            $this->event_id = $this->_REQUEST['event_id'];
            $q = "SELECT fake_event_id 
                  FROM events 
                  WHERE event_id='$this->event_id'";
            $tmp = $this->query($q);
            $this->fake_event_id = $tmp['fake_event_id'];
         } else {
            $q = "SELECT fake_event_id,
                         event_id 
                  FROM events 
                  WHERE event_id 
                  ORDER BY event_id DESC LIMIT 1";
            $tmp = $this->query($q);
            $this->event_id = $tmp['event_id'];
            $this->fake_event_id = $tmp['fake_event_id'];
         }
         if(!empty($this->_REQUEST['ean_code'])) {
            $this->ean_code = $this->_REQUEST['ean_code'];
         }
         return true;
      }

      public function manage() {
         $call = $this->_REQUEST['call'];
         if(!empty($this->_REQUEST['call'])) {
            if(!$this->preg_array_match("/$call/", $this->commands))
               return false;
            init::log("Processing $call", 5);
            $out = $this->$call->process();
         }
         echo json_encode($out);
      }    
   
      public function __destruct() {
         unset($this);
         return true;
      }
   }
?>
