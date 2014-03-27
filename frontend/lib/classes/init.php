<?php
   class init extends common {
      public $extension = "php";

      public function __construct() {
         $this->getConfig();
         $this->handle();
         $this->security = new security($this->base_url, $this->page);
         $this->security->verifyAccess(1);
         $this->load();
         return true;
      }

      // Schnygga till input automagiskt :)
      public function __set($name, $value) {
         if(preg_match("/_CONFIG|query/", $name)) {
            $this->$name = $value;
            return true;
         }
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
                  if(is_object($str)) {
                     $str = (array) $str;
                     foreach($str as $key2 => $str2) {
                        if(__MYSQL_IN_USE__)
                           $str2 = mysql_real_escape_string($str2);
                        $str2 = strip_tags($str2, $this->except_list);
                        $str[$key2] = html_entity_decode($str2);

                     }
                  } else {
                     if(__MYSQL_IN_USE__)
                        $str = mysql_real_escape_string($str);
                     $str = strip_tags($str, $this->except_list);
                     $value[$key] = html_entity_decode($str);
                  }
               }
            }
         }
         $this->$name = $value;
         return true;
      }

      public function getConfig() {
         $json_array = array("call" => "configurations",
                             "action" => "get");
         $this->queryApi($json_array);
         foreach($this->query as $config_item) {
            $config_item = (array) $config_item;
            $config_item['value'] = unserialize($config_item['value']);
            if(preg_match("/(allowed_to_query|verbosity)/", $config_item['name']))
               $config_item['value'] = preg_split("/\s*,\s*/", $config_item['value']);
            $this->_CONFIG[$config_item['name']] = $config_item['value'];
         }
         return true;
      }

      public function handle() {
         global $_SERVER;
         // Want to see/use password in $this object, comment next line out...
         $this->_CONFIG['password'] = str_repeat("x", 8);
         $this->base_url = dirname($_SERVER['PHP_SELF'])."/";
         $this->base_path = dirname($_SERVER['SCRIPT_FILENAME']);
         $base = preg_replace("/\//", "\\\/", $this->base_url);
         $this->request_page = preg_replace("/^$base/", "", $_SERVER['REQUEST_URI']);
         if(preg_match("/^\?/", basename($this->request_page))) {
            $this->page = strtolower(dirname($this->request_page));
            $_GET = basename($this->request_page);
            $_GET = explode("&", substr($_GET, 1));
            foreach($_GET as $keyvalue) {
               list($key, $value) = preg_split("/=/", $keyvalue, 2);
               $this->_GET[$key] = $value;
            }
         } else {
            $this->page = strtolower(substr($this->request_page, 0, count($this->page)-1));
         }

         if(count($_POST) > 0) {
            foreach($_POST as $key => $value)
               $this->_POST[$key] = $value;
         }
      }

      public function load() {
         $this->header_file = $this->base_path."/".$this->_CONFIG['header_file'];
         $this->footer_file = $this->base_path."/".$this->_CONFIG['footer_file'];

         global $_SESSION;
         if(empty($this->page))
            $this->page = "index";
         $this->controller_file = $this->base_path."/".$this->_CONFIG['controllers_path'].$this->page.".".$this->extension;
         $this->page_file = $this->base_path."/".$this->_CONFIG['views_path'].$this->page.".".$this->extension;
         if(is_file($this->controller_file))
            require_once($this->controller_file);
         else
            $this->error_message[] = "Failed to include $this->controller_file";
         ob_start();
         if(is_file($this->header_file))
            require_once($this->header_file);
         else
            $this->error_message[] = "Failed to include header file ($this->header_file)";
         $page_name = strtoupper(substr($this->page, 0, 1)).strtolower(substr($this->page, 1));
         if(is_file($this->page_file)) 
            require_once($this->page_file);
         else
            if(!is_file($this->page_file))
               $this->error_message[] = "Failed to include $this->page_file";
            else
               $this->error_message[] = "You're not allowed to see this...";
         if(count($this->error_message) > 0) {
            require_once($this->_CONFIG['404_page']);
            return false;
         }
         if(is_file($this->footer_file))
            require_once($this->footer_file);
         else
            $this->error_message[] = "Failed to include header file ($this->footer_file)";
         $out = ob_get_clean();
         echo $out;
         return true;
      }

      static public function log($string, $verbosity) {
         $date = date("[%Y-%m-%d %H:%i:%s]");
         $fh = fopen(dirname($_SERVER['SCRIPT_FILENAME'])."/logs/uploads.txt", "a+");
         fwrite($fh, $date." ($verbosity) ".$string."\n");
         fclose($fh);
         return true;
      }


      public function __destruct() {
         unset($this);
         return true;
      }
   }
?>
