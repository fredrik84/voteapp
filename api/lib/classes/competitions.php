<?php
   class competitions extends common {
      public $requiredRegister      = array("name",
                                            "description",
                                            "start_time",
                                            "end_time",);
      public $requiredUnregister    = array("competition_id");
      public $requiredUpdate        = array("competition_id");
      public $requiredGet           = array();
      public $requiredArchive       = array();
      public $requiredCurrent       = array();
      public $requiredNonsubmitted  = array("ean_id");

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
            case preg_match("/^archive/", $this->action):
               $out = $this->archive();
               break;
            case preg_match("/^current/", $this->action):
               $out = $this->current();
               break;
            case preg_match("/^nonsubmitted/", $this->action):
               $out = $this->nonsubmitted();
               break;
            default:
               throw new Exception("Unknown action\n");
               break;
         }
         if($out) 
            return $out;
         else
            return false;
      }

      public function register() {
         if(empty($this->_REQUEST['event_id']))
            $condition = "event_id=(SELECT event_id FROM events ORDER BY event_id DESC LIMIT 1), ";
         if(empty($this->_REQUEST['event_id']))
            $event_condition = "event_id=(SELECT event_id FROM events ORDER BY event_id DESC LIMIT 1)";
         else
            $event_condition = "event_id='".$this->_REQUEST['event_id']."'";
         $q = "SELECT competition_id 
               FROM competitions 
               WHERE name='".$this->_REQUEST['name']."' 
                 AND $event_condition";
         $this->query($q);
         if($this->num_rows > 0)
            return false;
         $q = "INSERT INTO competitions 
               SET $condition ".implode(", ", $this->q_array)." 
               ON DUPLICATE KEY UPDATE competition_id=competition_id";
         $this->query($q);
         $q = "SELECT competition_id 
               FROM competitions 
               WHERE ".implode(" AND ", $this->q_array);
         $tmp = $this->query($q);
         return $tmp['competition_id'];
      }

      public function unregister() {
         $q = "DELETE FROM competitions 
               WHERE competition_id='".$this->_REQUEST['competition_id']."'";
         $this->query($q);
         return "200 OK";
      }

      public function update() {
         $q = "UPDATE competitions 
               SET ".implode(", ", $this->q_array)." 
               WHERE competition_id='".$this->_REQUEST['competition_id']."'";
         $this->query($q);
         return "200 OK";
      }

      public function get() {
         if(count($this->q_array) == 0) {
            $conditions = "event_id=(SELECT event_id FROM events ORDER BY event_id DESC LIMIT 1)";
         } else {
            $conditions = implode(" AND ", $this->q_array);
         }
         $q = "SELECT competition_id, 
                      name, 
                      description, 
                      start_time, 
                      end_time, 
                      show_time,
                      compo_enabled,
                      submit_enabled,
                      hard_deadline,
                      voting,
                      locked
               FROM competitions 
               WHERE ".$conditions." 
               ORDER BY name ASC";
         $this->data = $this->query($q);
         return $this->data;
      }

      public function current() {
         $q = "SELECT competition_id,
                      name,
                      description,
                      start_time,
                      end_time,
                      show_time,
                      compo_enabled,
                      submit_enabled,
                      hard_deadline 
               FROM competitions 
               WHERE event_id=(SELECT event_id FROM events ORDER BY event_id DESC LIMIT 1) 
                 AND compo_enabled=1 
                 AND start_time < now() 
                 AND end_time > now()";
         $this->data = $this->query($q);
         return $this->data;
      }

      public function archive() {
         if(count($this->q_array) == 0) {
            $conditions = "event_id != (SELECT MAX(event_id) FROM events)";
         } else {
            $conditions = implode(" AND ", $this->q_array);
         }
         $q = "SELECT competition_id,
                      name 
               FROM competitions 
               WHERE ".$conditions;
         $this->data = $this->query($q);
         return $this->data;
      }

      public function nonsubmitted() {
         $q = "SELECT competition_id,
                      name,
                      submit_enabled
               FROM competitions 
               WHERE competition_id NOT IN (SELECT competition_id FROM contributions WHERE ean_id='".$this->_REQUEST['ean_id']."')
                 AND event_id=(SELECT event_id FROM events ORDER BY event_id DESC LIMIT 1)";
         $this->data = $this->query($q);
         return $this->data;
      }

      public function __destruct() {
         unset($this);
         return true;
      }
   }

?>
