<?php
   class users extends common {
      public $requiredRegister   = array("username",
                                         "password",
                                         "access",
                                         "event_id");
      public $requiredUnregister = array("user_id");
      public $requiredUpdate     = array("user_id");
      public $requiredGet        = array();
      public $requiredVerify     = array("password");

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
            case preg_match("/^verify/", $this->action):
               $out = $this->verify();
               break;
            default:
               throw new Exception("Unknown action\n");
               break;
         }
         return $out;
      }


      public function register() {
         $this->_REQUEST['salt'] = $this->generateSalt();
         $this->_REQUEST['password'] = $this->hashPassword($this->_REQUEST['salt'], $this->_REQUEST['password']);
         unset($this->q_array);
         $this->getRequestForQuery();
         $this->verified = $this->verifyUser();
         if(empty($this->_REQUEST['event_id']))
            $condition = "event_id=(SELECT event_id FROM events ORDER BY event_id DESC LIMIT 1), ";

         $q = "INSERT INTO users 
               SET $condition ".implode(", ", $this->q_array)." 
               ON DUPLICATE KEY UPDATE user_id=user_id";
         $this->query($q);
         $q = "SELECT user_id 
               FROM users 
               WHERE ".implode(" AND ", $this->q_array);
         $tmp = $this->query($q);
         return $tmp['user_id'];
      }

      public function unregister() {
         $q = "DELETE FROM users 
               WHERE user_id='".$this->_REQUEST['user_id']."'";
         $this->query($q);
         return "200 OK";
      }

      public function update() {
         $this->_REQUEST['salt'] = $this->generateSalt();
         $this->_REQUEST['password'] = $this->hashPassword($this->_REQUEST['salt'], $this->_REQUEST['password']);
         unset($this->q_array);
         $this->getRequestForQuery();
         $q = "UPDATE users 
               SET ".implode(",\n", $this->q_array)."
               WHERE user_id='".$this->_REQUEST['user_id']."'";
         $this->query($q);
         return "200 OK";
      }

      public function get() {
         if(count($this->q_array) > 0) {
            $conditions = "WHERE ".implode(" AND ", $this->q_array);
         }
         $q = "SELECT user_id,
                      username,
                      access,
                      password,
                      event_id,
                      created_at,
                      updated_at 
               FROM users
               ".$conditions;
         $this->data = $this->query($q);
         return $this->data;
      }

      public function verify() {
         $this->password = $this->_REQUEST['password'];
         $this->username = $this->_REQUEST['username'];
         unset($this->_REQUEST['password']);
         unset($this->q_array);
         $this->getRequestForQuery();
         if(count($this->q_array) > 0) {
            $conditions = "WHERE ".implode(" AND ", $this->q_array);
         }
         $q = "SELECT user_id,
                      password,
                      salt 
               FROM users ".$conditions;
         $this->data = $this->query($q);
         $this->salt_key = $this->data['salt'];
         $this->hashPassword();
         if($this->hashedPassword == $this->data['password'])
            return "true";
         else
            return "false";
      }

      public function hashPassword($salt = NULL, $password = NULL) {
         if(!empty($salt))
            $this->salt_key = $salt;
         if(!empty($password))
            $this->password = $password;
         if(empty($this->salt_key) || empty($this->password))
            return false;
         $this->hash = hash("sha256", $this->password);
         $this->hashedPassword = hash("sha256", $this->hash.$this->salt_key);
         return $this->hashedPassword;
      }

      public function generateSalt($size = 64) {
         $possible = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
         for($i = 1; $i <= $size; $i++)
            $string .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
         return $string;
      }

      public function __destruct() {
         unset($this);
         return true;
      }
   }

?>
