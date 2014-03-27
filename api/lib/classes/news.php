<?php
   class news extends common {
      public $requiredRegister   = array("title",
                                         "subtitle",
                                         "body");
      public $requiredUnregister = array("new_id");
      public $requiredUpdate     = array("new_id");
      public $requiredGet        = array();

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
            default:
               throw new Exception("Unknown action\n");
               break;
         }
         return $out;
      }

      public function register() {
         $q = "INSERT INTO news 
               SET event_id=(SELECT event_id FROM events ORDER BY event_id DESC LIMIT 1), 
                   ".implode(",\n", $this->q_array);
         $this->query($q);
         return mysql_insert_id();
      }

      public function unregister() {
         $q = "DELETE FROM news 
               WHERE new_id='".$this->_REQUEST['new_id']."'";
         $this->query($q);
         return "200 OK";
      }

      public function update() {
         $q = "UPDATE news 
               SET ".implode(",\n", $this->q_array)." 
               WHERE new_id='".$this->_REQUEST['new_id']."'";
         $this->query($q);
         return "200 OK";
      }

      public function get() {
         if(count($this->q_array) == 0) {
            $conditions = "event_id=(SELECT event_id FROM events ORDER BY event_id DESC LIMIT 1)";
         } else {
            $conditions = implode("\n AND ", $this->q_array);
         }
         $q = "SELECT new_id,
                      title,
                      subtitle,
                      body 
               FROM news 
               WHERE ".$conditions." 
               ORDER BY new_id DESC";
         $this->data = $this->query($q);
         return $this->data;
      }

      public function __destruct() {
         unset($this);
         return true;
      }
   }

?>
