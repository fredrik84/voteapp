<?php
   class events extends common {
      public $requiredRegister   = array("event_name",
                                         "start_time",
                                         "end_time");
      public $requiredUnregister = array("event_id");
      public $requiredUpdate     = array("event_id");
      public $requiredGet        = array();
      public $requiredCurrent    = array();
      public $requiredArchive    = array();

      public function __construct($request) {
         $this->_REQUEST = $request;
         return true;
      }

      public function process() {
         $this->call = $this->_REQUEST['call'];
         $this->action = $this->_REQUEST['action'];
         if(!$this->required())
            throw new Exception("Missing required arguments\n");
         unset($this->q_array);
         $this->getRequestForQuery();

         switch(true) {
            case preg_match("/^register/", $this->action):
               $out = $this->register();
               break;
            case preg_match("/^unregister/", $this->action):
               $out = $this->unregister();
               break;
            case preg_match("/^update/", $this->action):
               $out = $this->update();
               break;
            case preg_match("/^get/", $this->action):
               $out = $this->get();
               break;
            case preg_match("/^current/", $this->action):
               $out = $this->current();
               break;
            case preg_match("/^archive/", $this->action):
               $out = $this->archive();
               break;
            default:
               throw new Exception("Unknown action\n");
               break;
         }
         return $out;
      }

      public function register() {
         $q = "INSERT INTO events 
               SET $condition ".implode(",\n", $this->q_array)." 
               ON DUPLICATE KEY UPDATE event_id=event_id";
         $this->query($q);
         $q = "SELECT event_id 
               FROM events 
               WHERE ".implode("\n AND ", $this->q_array);
         $tmp = $this->query($q);
         return $tmp['event_id'];
      }

      public function unregister() {
         $q = "DELETE FROM events 
               WHERE event_id='".$this->_REQUEST['event_id']."'";
         $this->query($q);
         return "200 OK";
      }

      public function update() {
         $q = "UPDATE events 
               SET ".implode(",\n", $this->q_array)." 
               WHERE event_id='".$this->_REQUEST['event_id']."'";
         $this->query($q);
         return "200 OK";
      }

      public function get() {
         if(count($this->q_array) > 0) 
            $conditions = "WHERE ".implode(" AND ", $this->q_array);
         $q = "SELECT event_id,
                      fake_event_id,
                      event_name,
                      start_time,
                      end_time,
                      enabled 
               FROM events
               ".$conditions;
         $this->data = $this->query($q);
         return $this->data;
      }

      public function current() {
         $q = "SELECT event_id,
                      fake_event_id,
                      event_name 
               FROM events 
               WHERE enabled=1 
               ORDER BY event_id DESC LIMIT 1";
         $this->data = $this->query($q);
         return $this->data;
      }

      public function archive() {
         $q = "SELECT event_id,
                      fake_event_id,
                      event_name 
               FROM events 
               WHERE enabled=1 
                 AND event_id != (SELECT MAX(event_id) FROM events) 
               ORDER BY event_id DESC";
         $this->data = $this->query($q);
         return $this->data;
      }

      public function __destruct() {
         unset($this);
         return true;
      }
   }
?>
