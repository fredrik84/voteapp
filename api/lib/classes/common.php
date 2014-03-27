<?php
   abstract class common {
      static public function getConfig() {
         include("config.php");
         $q = "SELECT name,
                      value 
               FROM configurations 
               WHERE enabled=1 
               ORDER BY name";
         foreach(init::query($q) as $config_item) {
            $key = $config_item['name'];
            $value = unserialize($config_item['value']);
            if(preg_match("/(allowed_to_query|verbosity)/", $key))
               $_CONFIG[$key] = preg_split("/\s*,\s*/", $value);
            else
               $_CONFIG[$key] = $value;
         }
         return $_CONFIG;
      }

      static public function query($q) {
         if(preg_match("/^SELECT/", $q)) {
            list($operation) = explode(" ", $q);
            init::log("$operation: $q", 4);
            if(!$query = mysql_query($q)) {
               init::log("Failed to query database: ".mysql_error(), 4);
               return "500 ERROR";
            }
            if(isset($this)) {
               if(($this->num_rows = mysql_num_rows($query)) > 1) {
                  while($res = mysql_fetch_array($query, MYSQL_ASSOC))
                     $data[] = $res;
               } else {
                  $data = mysql_fetch_array($query, MYSQL_ASSOC);
               }
            } else {
               while($res = mysql_fetch_array($query, MYSQL_ASSOC))
                  $data[] = $res;
            }
         } else {
            list($operation) = explode(" ", $q);
            init::log("$operation: $q", 4);
            if(!mysql_query($q)) {
               $a = mysql_errno();
               init::log("Failed to query database: ".mysql_error(), 4);
               return $a." ERROR";
            }
         }
//         $this->raw_query_data = $data;
         return $data;
      }

      public function required() {
         $function = "required".strtoupper(substr($this->action, 0, 1)).substr($this->action, 1);
         $keys = array_keys($this->_REQUEST);
         if(count($this->$function) > 0) {
            foreach($this->$function as $required) {
               if(!in_array($required, $keys)) {
                  init::log("Missing $required argument when passed to $this->call/$this->action", 3);
                  return false;
               }
            }
         }
         return true;
      }

      public function preg_array_match($needle, $haystack) {
         if($this->depth == 4)
            die();
         $results = array();
         foreach($haystack as $key => $value) {
            if(preg_match($needle, $value)) {
               $results[$key] = $value;
            }
         }
         if(count($results) == 0 && $results != false) {
            $tmp = explode(".", $needle);
            if(count($tmp) == 1)
               return false;
            array_pop($tmp);
            $needle = implode(".", $tmp);
            $results = $this->preg_array_match($needle, $haystack);
            $this->depth++;
            init::log("Recursive depth ($needle): $this->depth", 5);
         }
         if(!empty($results)) {
            return $results;
         } else {
            return false;
         }
      }

      public function getRequestForQuery() {
         if(!isset($this->_REQUEST['data'])) {
            foreach($this->_REQUEST as $key => $value) {
               if(preg_match("/^(call|action)/", $key))
                  continue;
               $value = mysql_real_escape_string($value);
               $key = mysql_real_escape_string($key);
               $this->q_array[] = "$key='$value'";
            }
         } else {
            $this->raw_query_data = $this->decode($this->_REQUEST['data']);
            die("Oh well this shouldn't happen... how did you get here?!!?!");
         }
         return $this->q_array;
      }

      public function verifyUser() {
         $q = "SELECT value 
               FROM configurations 
               WHERE name='verify_url'";
         $tmp = $this->query($q);
         $verify_url = preg_replace("/(^[^\"]+\"|\";$)/", "", $tmp[0]['value']);
         while(true) {
            if(preg_match("/\|/", $verify_url)) {
               list($nl, $tmp) = explode("|", $verify_url);
               $variables[] = $tmp;
               $verify_url = preg_replace("/\|$tmp\|/", "\$this->$tmp", $verify_url);
               init::log("Extracted $tmp, url: $verify_url", 5);
            } else {
               break;
            }
         }
         foreach($variables as $value) 
            $this->$value = $this->_REQUEST[$value];
         eval("\$url = \"$verify_url\";");
         $out = $this->curl($url);
         if($out == "true") {
            return 1;
         } else {
            return 0;
         }
      }

      public function curl($url) {
         $ch = curl_init($url);
         $setopt = array(CURLOPT_HEADER => false,
                         CURLOPT_RETURNTRANSFER => true);

         curl_setopt_array($ch, $setopt);
         $out = curl_exec($ch);
         curl_close($ch);
         return $out;

      }

      public function decode($string) {
         if(is_string($string)) {
            $json_string = json_decode($string);
         }
         return $json_string;
      }

      public function encode($string) {
         $json_string = json_encode($string);
         return $json_string;
      }

      public function decompress($string) {
         if(is_string($string)) {
            $decompressed = gzuncompress($string);
         }
         return $decompressed;
      }

      public function compress($string) {
         include("config.php");
         if(is_string($string)) {
            if(empty($_CONFIG['compress_level']))
               $_CONFIG['compress_level'] = 6;
            $compressed = gzcompress($string, $_CONFIG['compress_level']);
         }
         return $compressed;
      }

      public function args() {
         global $_REQUEST;
         global $_GET;
         global $_POST;
         if(!empty($_GET)) 
            $this->_GET = $_GET;
         else
            $this->_POST = $_POST;
         $json = (array) json_decode($_REQUEST['data']);
         $this->_REQUEST = $_REQUEST;
         foreach($json as $key => $value)
            $this->_REQUEST[$key] = $value;
         $this->raw_data = $this->_REQUEST['data'];
         unset($this->_REQUEST['data']);
         return $this->_REQUEST;
      }
   }
?>
