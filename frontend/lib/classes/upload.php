<?php
   class upload extends common {
      public function __construct($type = NULL) {
         global $_FILES;
         global $_REQUEST;
         $this->_REQUEST = $_REQUEST;
         $this->ean_id = $this->_REQUEST['ean_id'];
         $this->type = $type;
         if(empty($this->type))
            $this->type = "filename";
         switch($this->type) {
            case "thumbnail":
               $this->_FILES = $_FILES['thumbnail'];
               $this->filename = $this->_FILES['name'];
               init::log("Request received for ".$this->_FILES['name'], 3);
               init::log("Content of \$_FILES:\n".print_r($_FILES, true), 6);
               init::log("Content of \$_REQUEST:\n".print_r($this->_REQUEST, true), 6);
               if(!empty($this->_FILES['name']) && !empty($this->_REQUEST['contribution_id'])) {
                  $this->extension = strtolower(array_pop(explode(".", $this->_FILES['name'])));
                  $this->initialize();
                  $this->copyFiles();
                  $this->updateEntry();
                  $this->resizeThumbnail();
               }

               break;
            default:
               $this->_FILES = $_FILES['files'];
               $this->filename = $this->_FILES['name'];
               init::log("Request received for ".$this->_FILES['name'], 3);
               init::log("Content of \$_FILES:\n".print_r($_FILES, true), 6);
               init::log("Content of \$_REQUEST:\n".print_r($this->_REQUEST, true), 6);
               if(!empty($this->_FILES['name']) && !empty($this->_REQUEST['contribution_id'])) {
                  $this->extension = strtolower(array_pop(explode(".", $this->_FILES['name'])));
                  $this->initialize();
                  $this->copyFiles();
                  $this->updateEntry();
               } else {
                  init::log("Failed!?!", 5);
               }
               break;
         }
         return true;
      }

      public function __set($name, $value) {
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

      public function checkValidExtensions() {
         switch($this->type) {
            case "thumbnail":
               $json_Array = array("call" => "configurations",
                                   "action" => "get",
                                   "name" => "thumbnail_extensions");
               $this->queryApi($json_array);
               break;
            default:
               $json_array = array("call" => "configurations",
                                   "action" => "get",
                                   "name" => "upload_extensions");
               $this->queryApi($json_array);
               break;
         }
//         $a = preg_replace("/(^[^\"]+\"|\";$)/", "", $this->query[0]->value);
         $ext_array = preg_split("/\s*,\s*/", strtolower(unserialize($this->query[0]->value)));
         if(in_array(strtolower($this->extension), $ext_array)) {
            return true;
         } else {
            init::log("Check failed :(", 5);
            return false;
         }
      }

      public function checkThumbnailRequirements() {
         $json_array = array("call" => "configurations",
                             "action" => "get",
                             "name" => "thumbnail_size");
         $this->queryApi($json_array);
         
         $json_array = array("call" => "configurations",
                             "action" => "get",
                             "name" => "thumbnail_resolution");
         $this->queryApi($json_array);
      }

      public function getPath() {
         $json_array = array("call" => "configurations",
                             "action" => "get",
                             "name" => "upload_path");
         $this->queryApi($json_array);
         return preg_replace("/(^[^\"]+\"|\";$)/", "", $this->query[0]->value);
      }

      public function initialize() {
         if(!$this->checkValidExtensions() && $this->type != "thumbnail") 
            return false;
         if($this->type == "thumbnail")
            $this->checkThumbnailRequirements();
         init::log("Getting contribution values", 6);
         $json_array = array("call" => "contributions",
                             "action" => "get",
                             "contribution_id" => $this->_REQUEST['contribution_id']);
         $this->queryApi($json_array);
         $id_number = str_pad($this->query[0]->contribution_id, 4, "0", STR_PAD_LEFT);
         switch($this->type) {
            case "thumbnail":
               $this->filename = $id_number."_".preg_replace("/\s+/", "_", $this->query[0]->contributer)."_-_".preg_replace("/\s+/", "_", $this->query[0]->entry_name)."_thumb.$this->extension";
               break;
            default:
               $this->filename = $id_number."_".preg_replace("/\s+/", "_", $this->query[0]->contributer)."_-_".preg_replace("/\s+/", "_", $this->query[0]->entry_name).".$this->extension";
               break;
         }
         $compo_id = $this->query[0]->competition_id;
         $event_id = $this->query[0]->event_id;
         $json_array = array("call" => "configurations",
                             "action" => "get",
                             "name" => "remove_filename_characters");
         $this->queryApi($json_array);
         $remove_characters = implode(preg_split("/\s*,\s*/", preg_replace("/(^[^\"]+\"|\";$)/", "", $this->query[0]->value)));
         $this->filename = preg_replace("/[$remove_characters]/", "", $this->filename);
         init::log("Constructing filename $this->filename", 6);
         init::log(print_r($this->query, true), 5);
         $json_array = array("call" => "events",
                             "action" => "current");
         $this->queryApi($json_array);
         $event_name = $this->query[0]->event_name;
         $json_array = array("call" => "competitions",
                             "action" => "get",
                             "competition_id" => $compo_id);
         $this->queryApi($json_array);
         init::log(print_r($this->query, true), 5);
         if($this->query[0]->submit_enabled == 0)
            return false;
         $competition_name = $this->query[0]->name;
         // Full path
         $this->path = $this->getPath().$event_name."/".$competition_name."/";
         // Path in ID's.. should be symlinked later...
         $this->id_path = $this->getPath()."paths_by_id/".$event_id."/".$compo_id."/";
         $this->validateDirectories();
         return true;
      } 

      public function copyFiles() {
         if(is_file($this->_FILES['tmp_name'])) {
            init::log("Copy ".$this->_FILES['tmp_name']." to $this->path$this->filename", 5);
            copy($this->_FILES['tmp_name'], $this->path.$this->filename);
         } else {
            init::log("Not copying the file... ".$this->_FILES['tmp_name']." is not a file?!!", 5);
            return false;
         }
      }

      public function validateDirectories() {
         if(!is_dir($this->path))
            mkdir($this->path, 0777, true);
         return true;
      }

      public function updateEntry() {
         $this->contribution_id = $this->_REQUEST['contribution_id'];
         $json_array = array("call" => "contributions",
                             "action" => "get",
                             "contribution_id" => $this->contribution_id);
         $this->queryApi($json_array);
         $this->old_filename = $this->query[0]->filename;
         $json_array = array("call" => "contributions",
                             "action" => "update".$this->type,
                             "contribution_id" => $this->contribution_id,
                             $this->type => $this->filename);
         if(is_file($this->old_filename) && $this->filename != $this->old_filename) 
            unlink($this->old_filename);
         $this->queryApi($json_array);
         print_r($json_array);
         init::log(preg_replace("/\\\\/", "", $this->api_url), 5);
         return true;
      }

      public function resizeThumbnail() {
         $tmp = file_get_contents($this->path.$this->filename);
         $imghandler = imagecreatefromstring($tmp);
         unset($tmp);
         $width = imagesx($imghandler);
         $height = imagesy($imghandler);
         init::log("Creating old image handler", 5);
         $json_array = array("call" => "configurations",
                             "action" => "get",
                             "name" => "thumbnail_resolution");
         $this->queryApi($json_array);
         init::log("Getting thumbnail width/size option: ".$this->query[0]->value, 5);
         list($thumb_width, $thumb_height) = explode("x", unserialize($this->query[0]->value));
         if($width > $height) {
            $aspect = $height/$width;
            $thumb_height = round($thumb_width*$aspect);
            init::log("Figured out that the width > height, AR: $aspect, W: $thumb_width, H: $thumb_height", 5);
         } else {
            $aspect = $width/$height;
            $thumb_width = round($thumb_height*$aspect);
            init::log("Figured out that the width < height, AR: $aspect, W: $thumb_width, H: $thumb_height", 5);
         }
         $newimg = imagecreatetruecolor($thumb_width, $thumb_height);
         imagefill($newimg, 0, 0, imagecolorallocate($newimg, 255, 255, 255));
         imagealphablending($newimg, TRUE);
         init::log("Created new image handler");
         imagecopyresized($newimg, $imghandler, 0,0,0,0, $thumb_width, $thumb_height, $width, $height);
         init::log("Resizing image: ".$width."x".$height." to ".$thumb_width."x".$thumb_height, 5);
         unlink($this->filename);
         init:log("Deleting $this->filename", 5);
         $tmp = explode(".", $this->filename);
         array_pop($tmp);
         $this->filename = implode(".", $tmp).".png";
         header('Content-Type: image/png');
         imagepng($newimg, $this->path.$this->filename, 0);
         init::log("Recreated $this->filename with the resized image", 5);
         $json_array = array("call" => "contributions",
                             "action" => "update".$this->type,
                             "contribution_id" => $this->contribution_id,
                             $this->type => $this->filename);
         $this->queryApi($json_array);
         init::log("Updating filename", 5);
         return true;
      }


      public function __destruct() {
         unset($this);
         return true;
      }
   }
?>
