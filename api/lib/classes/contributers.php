<?php
   class contributers extends common {
      public $requiredRegister         = array("username",
                                               "firstname",
                                               "lastname",
                                               "telephone",
                                               "email",
                                               "ean_code");
      public $requiredUnregister       = array("contributer_id");
      public $requiredUpdate           = array("ean_code");
      public $requiredGet              = array();
      public $requiredVerify           = array("ean_code");
      public $requiredGetidfromean     = array("ean_code");
      public $requiredStatus           = array("ean_code");
      public $requiredGeteanfromeanid  = array("ean_id");

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
            case preg_match("/^geteanfromeanid/", $this->action):
               $out = $this->geteanfromeanid();
               break;
            case preg_match("/^getidfromean/", $this->action):
               $out = $this->getidfromean();
               break;
            case preg_match("/^get/", $this->action):
               $out = $this->get();
               break;
            case preg_match("/^verify/", $this->action):
               $out = $this->verify();
               break;
            case preg_match("/^status/", $this->action):
               $out = $this->status();
               break;
            default:
               throw new Exception("Unknown action $this->action\n");
               break;
         }
         return $out;
      }

      public function register() {
         $this->verified = $this->verifyUser();
         $q = "INSERT INTO EANs 
               SET ean_code='$this->ean_code', 
                   verified='$this->verified', 
                   event_id=(SELECT event_id FROM events ORDER BY event_id DESC LIMIT 1) 
               ON DUPLICATE KEY UPDATE ean_id=ean_id";
         $this->query($q);
         $q = "SELECT ean_id 
               FROM EANs 
               WHERE ean_code='$this->ean_code' 
               ORDER BY ean_id DESC LIMIT 1";
         $tmp = $this->query($q);
         $ean_id = $tmp['ean_id'];
         $this->q_array[] = "ean_id='$ean_id'";
         foreach($this->q_array as $key => $value) 
            if(preg_match("/ean_code/", $value))
               unset($this->q_array[$key]);
         if(empty($this->_REQUEST['event_id']))
            $condition = "event_id=(SELECT event_id FROM events ORDER BY event_id DESC LIMIT 1), ";
         $q = "INSERT INTO contributers 
               SET $condition ".implode(",\n", $this->q_array)." 
               ON DUPLICATE KEY UPDATE contributer_id=contributer_id";
         $this->query($q);
         $q = "SELECT contributer_id 
               FROM contributers 
               WHERE ".implode(" AND ", $this->q_array);
         $tmp = $this->query($q);
         return $tmp['contributer_id'];
      }

      public function unregister() {
         $q = "DELETE FROM contributers 
               WHERE user_id='".$this->_REQUEST['user_id']."'";
         $this->query($q);
         return "200 OK";
      }

      public function update() {
         $ean_code = $this->_REQUEST['ean_code'];
         unset($this->_REQUEST['ean_code']);
         unset($this->q_array);
         $this->getRequestForQuery();
         $q = "UPDATE contributers 
               SET ".implode(", ", $this->q_array)." 
               WHERE ean_id=(SELECT ean_id FROM EANs WHERE ean_code='".$ean_code."')";
         $this->query($q);
         return "200 OK";
      }

      public function verify() {
         $this->verified = $this->verifyUser();
         if($this->verified > 0) {
            $this->ean_code = $this->_REQUEST['ean_code'];
            $q = "SELECT ean_id 
                  FROM EANs 
                  WHERE ean_code='$this->ean_code'";
            $tmp = $this->query($q);
            $this->num_rows = count($tmp);
            $this->ean_id = $tmp[0]['ean_id'];
            if($this->num_rows > 0) {
               $q = "UPDATE EANs 
                     SET verified='$this->verified' 
                     WHERE ean_code='".$this->ean_code."'";     
               $this->query($q);
               $q = "SELECT contributer_id 
                     FROM contributers 
                     WHERE ean_id='$this->ean_id'";
               $tmp = $this->query($q);
               $this->num_rows = count($tmp);
               if($this->num_rows == 0) {
                  $q = "INSERT INTO contributers 
                        SET event_id=(SELECT event_id FROM events ORDER BY event_id DESC LIMIT 1), 
                            ean_id='$this->ean_id'";      
                  $this->query($q);
               } 
            } else {
               $q = "INSERT INTO EANs SET ean_code='$this->ean_code',event_id=(SELECT event_id FROM events ORDER BY event_id DESC LIMIT 1),verified=1";
               $this->query($q);
               $this->ean_id = mysql_insert_id();
               $q = "SELECT contributer_id FROM contributers WHERE ean_id=$this->ean_id";
               $tmp = $this->query($q);
               $this->num_rows = count($tmp);
               if($this->num_rows == 0) {
                  $q = "INSERT INTO contributers SET event_id=(SELECT event_id FROM events ORDER BY event_id DESC LIMIT 1), ean_id='$this->ean_id'";
                  $this->query($q);
                  $this->contributer_id = mysql_insert_id();
               } else {
                  $this->contributer_id = $tmp[0]['contributer_id'];
               }
               $q = "UPDATE contributers SET ean_id='$this->ean_id' WHERE contributer_id='$this->contributer_id'";
               $this->query($q);
            }
            return "200 OK";
         } else {
            $q = "INSERT INTO EANs SET ean_code='$this->ean_code', event_id=(SELECT event_id FROM events ORDER BY event_id DESC LIMIT 1), verified=0";
            $this->query($q);
            return "500 ERROR";
         }
      }

      public function getidfromean() {
         $q = "SELECT contributer_id,ean_id,voter_only FROM contributers WHERE ean_id=(SELECT ean_id FROM EANs WHERE ean_code='".$this->_REQUEST['ean_code']."')";
         $this->data = $this->query($q);
         return $this->data;
      }

      public function status() {
         $q = "SELECT username, firstname, lastname, email, telephone, address, country FROM contributers WHERE ean_id=(SELECT ean_id FROM EANs WHERE ean_code='".$this->_REQUEST['ean_code']."')";
         $this->data = $this->query($q);
         foreach($this->data as $tmp) {
            foreach($tmp as $key => $value) {
               if(empty($value))
                  $missing[] = $key;
            }
         }
         return $missing;
      }

      public function geteanfromeanid() {
         $q = "SELECT ean_code FROM EANs WHERE ean_id='".$this->_REQUEST['ean_id']."'";
         $this->data = $this->query($q);
         return $this->data;
      }

      public function get() {
         if(!$this->required())
            throw new Exception("Missing required arguments\n");
         $this->getRequestForQuery();
         if(count($this->q_array) > 0) {
            $conditions = "WHERE ".implode(" AND ", $this->q_array);
         }
         $q = "SELECT contributer_id,username,firstname,lastname,email,telephone,address,country FROM contributers ".$conditions;
         $this->data = $this->query($q);
         return $this->data;
      }

      public function __destruct() {
         unset($this);
         return true;
      }
   }
?>
