<?php
   class votes extends common {
      public $requiredRegister      = array("contribution_id",
                                            "ean_id",
                                            "result");
      public $requiredUnregister    = array("competition_id",
                                            "contribution_id",
                                            "ean_id");
      public $requiredUpdate        = array("vote_id",
                                            "result");
      public $requiredGet           = array();
      public $requiredVotesbyeanid  = array("ean_id");

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
            case preg_match("/^votesbyeanid/", $this->action):
               $out = $this->votesbyeanid();
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
         if(empty($this->_REQUEST['event_id']))
            $condition = "event_id=(SELECT event_id FROM events ORDER BY event_id DESC LIMIT 1), ";
         if(empty($this->_REQUEST['competition_id']))
            $condition2 = "competition_id=(SELECT competition_id FROM contributions WHERE contribution_id='".$this->_REQUEST['contribution_id']."'), "; 
         $q = "INSERT INTO votes 
               SET $condition 
                   $condition2 
                   ".implode(",\n", $this->q_array);
         $this->query($q);
         return mysql_insert_id();
      }

      public function unregister() {
         $q = "DELETE FROM votes 
               WHERE user_id='".$this->_REQUEST['user_id']."'";
         $this->query($q);
         return "200 OK";
      }

      public function update() {
         if(empty($this->_REQUEST['event_id']))
            $this->q_array[] = "event_id=(SELECT event_id FROM events ORDER BY event_id DESC LIMIT 1)";
         $q = "UPDATE votes 
               SET ".implode(",\n", $this->q_array)." 
               WHERE vote_id='".$this->_REQUEST['vote_id']."'";
         $this->query($q);
         var_dump($q);
         return $out;
      }

      public function get() {
         if(count($this->q_array) == 0) {
            $conditions = "event_id=(SELECT event_id FROM events ORDER BY event_id DESC LIMIT 1)";
         } else {
            $conditions = implode(" AND ", $this->q_array);
         }
         $q = "SELECT vote_id,
                      competition_id,
                      contribution_id,
                      ean_id,
                      result 
               FROM votes 
               WHERE ".$conditions;
         $this->data = $this->query($q);
         return $this->data;
      }

      public function votesbyeanid() {
         $q = "SELECT vote_id,
                      competition_id,
                      contribution_id,
                      ean_id,
                      result
               FROM votes 
               WHERE ean_id='".$this->_REQUEST['ean_id']."'";
         $this->data = $this->query($q);
         return $this->data;
      }


      public function __destruct() {
         unset($this);
         return true;
      }
   }

?>
