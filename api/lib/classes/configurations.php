<?php
   class configurations extends common {
      public $requiredRegister   = array("name",
                                         "value");
      public $requiredUnregister = array("configuration_id");
      public $requiredUpdate     = array("configuration_id");
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
         $q = "INSERT INTO configurations
               SET ".implode(", ", $this->q_array)." 
               ON DUPLICATE KEY UPDATE configuration_id=configuration_id";
         $this->query($q);
         $q = "SELECT configuration_id 
               FROM configurations 
               WHERE ".implode(" AND ", $this->q_array);
         $tmp = $this->query($q);
         return $tmp['configuration_id'];
      }

      public function unregister() {
         $q = "DELETE FROM configurations 
               WHERE configuration_id='".$this->_REQUEST['configuration_id']."'";
         $this->query($q);
         return "200 OK";
      }

      public function update() {
         $q = "UPDATE configurations 
               SET ".implode(", ", $this->q_array)." 
               WHERE configuration_id='".$this->_REQUEST['configuration_id']."'";
         $this->query($q);
         return "200 OK";
      }

      public function get() {
         if(count($this->q_array) > 0)
            $condition = "WHERE ".implode(" AND ", $this->q_array);
         $q = "SELECT configuration_id,
                      name,
                      value,
                      description,enabled 
               FROM configurations
               ".$condition;
         $this->data = $this->query($q);
         return $this->data;
      }

      public function __destruct() {
         unset($this);
         return true;
      }
   }

?>
