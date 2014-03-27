<?php
   class contributions extends common {
      public $requiredRegister         = array("competition_id",
                                         "contributer",
                                         "entry_name",
                                         "ean_id");
      public $requiredUnregister       = array("contribution_id");
      public $requiredUpdate           = array("contribution_id");
      public $requiredGet              = array();
      public $requiredUpdatefilename   = array();
      public $requiredUpdatethumbnail  = array();
      public $requiredVoting           = array("event_id");
      public $requiredLocked           = array("competition_id");
      public $requiredDisqualified     = array("competition_id");

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
            case preg_match("/^updatefilename/", $this->action):
               $out = $this->updatefilename();
               break;
            case preg_match("/^updatethumbnail/", $this->action):
               $out = $this->updatethumbnail();
               break;
            case preg_match("/^update/", $this->action):
               $out = $this->update();
               break;
            case preg_match("/^geteanfromid/", $this->action):
               $out = $this->geteanfromid();
               break;
            case preg_match("/^voting/", $this->action):
               $out = $this->voting();
               break;
            case preg_match("/^locked/", $this->action):
               $out = $this->locked();
               break;
            case preg_match("/^disqualified/", $this->action):
               $out = $this->disqualified();
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
         $q = "SELECT competition_id 
               FROM competitions 
               WHERE submit_enabled=1 
                 AND contribution_id='".$this->_REQUEST['competition_id']."'";
         $tmp = $this->query($q);
         $this->num_rows = count($tmp);
         if($this->num_rows > 0) {
            $q = "SELECT contribution_id 
                  FROM contributions 
                  WHERE competition_id='".$this->_REQUEST['competition_id']."' 
                    AND ean_id='".$this->_REQUEST['ean_id']."' 
                    AND event_id=(SELECT event_id FROM events ORDER BY event_id DESC LIMIT 1)";
            $tmp = $this->query($q);
            $this->num_rows = count($tmp);
            if($this->num_rows > 0)
               return "Duplicate entry!";
            if(empty($this->_REQUEST['event_id']))
               $condition = "event_id=(SELECT event_id FROM events ORDER BY event_id DESC LIMIT 1), created_at=now(), ";
            $q = "INSERT INTO contributions 
                  SET $condition ".implode(", ", $this->q_array)."";
            $tmp = $this->query($q);
            $contribution_id = mysql_insert_id();
            if(preg_match("/^1062\sERROR/", $tmp))
               return "Duplicate entry";
            return $contribution_id;
         } else {
            return "500 ERROR";
         }
      }

      public function unregister() {
         $q = "SELECT competition_id 
               FROM competitions 
               WHERE submit_enabled=1 
                 AND contribution_id='".$this->_REQUEST['competition_id']."'";
         $tmp = $this->query($q);
         $this->num_rows = count($tmp);
         if($this->num_rows > 0) {
            $q = "DELETE FROM contributions 
                  WHERE contribution_id='".$this->_REQUEST['contribution_id']."'";
            $this->query($q);
            return "200 OK";
         } else {
            return "500 ERROR";
         }
      }

      public function update() {
         $keys = array_keys($this->_REQUEST);
         switch(true) {
            case in_array("approved", $keys):
               $q = "UPDATE contributions 
                     SET approved='".$this->_REQUEST['approved']."' 
                     WHERE contribution_id='".$this->_REQUEST['contribution_id']."'";
               $this->query($q);
               return true;
               break;
            default:
               $q = "SELECT competition_id 
                     FROM competitions 
                     WHERE submit_enabled=1 
                       AND contribution_id='".$this->_REQUEST['competition_id']."'";
               $tmp = $this->query($q);
               $this->num_rows = count($tmp);
               if($this->num_rows > 0) {
                  $q = "SELECT * 
                        FROM contributions 
                        WHERE contribution_id='".$this->_REQUEST['contribution_id']."'";
                  $entry = $this->query($q);
                  foreach($entry as $tmp)
                     foreach($tmp as $key => $value) 
                        if(empty($this->_REQUEST[$key]))
                           $this->_REQUEST[$key] = $value;
                  unset($this->_REQUEST['event_id']);
                  $q = "DELETE FROM contributions 
                        WHERE contribution_id='".$this->_REQUEST['contribution_id']."'";
                  $this->query($q);
                  $old_id = $this->_REQUEST['contribution_id'];
                  unset($this->_REQUEST['contribution_id']);
                  unset($this->q_array);
                  $this->getRequestForQuery();
                  $q = "INSERT INTO contributions 
                        SET event_id=(SELECT event_id FROM events ORDER BY event_id DESC LIMIT 1), 
                        ".implode(", ", $this->q_array);
                  $this->query($q);
                  $new_id = mysql_insert_id();
                  $q = "UPDATE votes 
                        SET contribution_id='$new_id' 
                        WHERE contribution_id='$old_id'";
                  $this->query($q);
                  return $new_id;
               } else {
                  return "500 ERROR";
               }
               break;
         }
      }

      public function updatefilename() {
         $q = "UPDATE contributions 
               SET filename='".$this->_REQUEST['filename']."',
                   approved=0
               WHERE contribution_id='".$this->_REQUEST['contribution_id']."'";
         $this->query($q);
         return "200 OK";
      }

      public function updatethumbnail() {
         $q = "UPDATE contributions 
               SET thumbnail_filename='".$this->_REQUEST['thumbnail']."' 
               WHERE contribution_id='".$this->_REQUEST['contribution_id']."'";
         $this->query($q);
         return "200 OK";
      }

      public function get() {
         if(count($this->q_array) == 0) {
            $conditions = "event_id=(SELECT event_id FROM events ORDER BY event_id DESC LIMIT 1)";
         } else {
            $conditions = implode(" AND ", $this->q_array);
         }
         $q = "SELECT contribution_id,
                      contributer,
                      entry_name,
                      description,
                      filename,
                      thumbnail_filename,
                      beamer_info,
                      event_id,
                      competition_id,
                      ean_id,
                      approved,
                      created_at,
                      updated_at 
               FROM contributions 
               WHERE ".$conditions;
         $this->data = $this->query($q);
         return $this->data;
      }

      public function geteanfromid() {
         $q = "SELECT ean_id 
               FROM contributions 
               WHERE contribution_id='".$this->_REQUEST['contribution_id']."'";
         $this->data = $this->query($q);
         return $this->data;
      }

      public function voting() {
         $q = "SELECT a.contribution_id, 
                      a.contributer, 
                      a.entry_name, 
                      a.description, 
                      a.beamer_info, 
                      a.filename, 
                      a.event_id, 
                      a.competition_id,
                      b.name,
                      c.event_name
               FROM contributions AS a, competitions AS b, events AS c
               WHERE a.competition_id=b.competition_id
                 AND a.event_id=c.event_id
                 AND a.event_id='".$this->_REQUEST['event_id']."'
                 AND voting=1
                 AND compo_enabled=1
                 AND submit_enabled=0
                 AND locked=1
                 AND approved=1
               ORDER BY a.competition_id DESC";
         $this->data = $this->query($q);
         if(count($this->data) == 0)
            return "false";
         return $this->data;
      }

      public function disqualified() {
         $q = "SELECT contribution_id,
                      contributer,
                      entry_name
               FROM contributions
               WHERE approved = 0
                 AND competition_id='".$this->_REQUEST['competition_id']."'";
         $this->data = $this->query($q);
         return $this->data;
      }

      public function locked() {
         $q = "SELECT count(contribution_id) AS count 
               FROM contributions 
               WHERE approved=-1 
                 AND competition_id='".$this->_REQUEST['competition_id']."'";
         $this->data = $this->query($q);
         if($this->data[0]['count'] > 0) {
            return "false";
         } else {
            return "true";
         }
      }

      public function __destruct() {
         unset($this);
         return true;
      }
   }

?>
