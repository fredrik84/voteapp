<?php
   abstract class common {
      // Schnygga till input automagiskt :)
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

      public function queryApi($query) {
         include("config.php");
         $this->query_array = $query;
         if($_CONFIG['require_ssl'])
            $protocol = "https";
         else
            $protocol = "http";
         $base_url = preg_replace("/^[^:]+:/", "$protocol:", $_CONFIG['api_url']);
         $this->tmp = urlencode(json_encode($query));
         $url = preg_replace("/\s/", "%20", "$base_url?data=".urlencode(json_encode($query)));
         $this->api_url = $url;
         $ch = curl_init($url);
         $setopt = array(CURLOPT_HEADER => false,
                         CURLOPT_RETURNTRANSFER => true,
                         CURLOPT_SSL_VERIFYPEER => false,
                         CURLOPT_SSL_VERIFYHOST => false,
                        );
         curl_setopt_array($ch, $setopt);
         if(!$raw_query = curl_exec($ch)) {
            $this->query[] = curl_error($ch);
            $this->query[] = "$url";
            return $this->query;
         }
         $this->query = (array) json_decode($raw_query);
         $this->raw_query = $raw_query;
         curl_close($ch);
         return $this->query;
      }

      public function printTable($type, $settings, $query) {
         if(empty($query[0])) {   
            $tmp = (array) $query;
            unset($query);
            $query = $tmp;
         }
         $headers = 0;
         foreach($query as $item) {
            $item = (array) $item;
            $key = $type."_id";
            $id = $item[$key];
            if($headers++ == 0) {
               $keys = array_keys($item);
               echo "<tr>\n";
               foreach($keys as $header) {
                  if(preg_match("/(".implode("|", $settings['ignore_list']).")/", $header))
                     continue;
                  echo "\t\t<td width=".$settings['size'][$header]."><font size='1'>".$settings['description'][$header]."</font></td>\n";
               }
               foreach($settings['options'] as $key => $value) 
                  echo "\t\t<td width=".$settings['size'][$key.'_option']."><font size=1'>$key</font></td>\n";
               echo "\t</tr>\n";
            }
            echo "<tr>\n";
            foreach($item as $column_name => $column_value) {
               if(preg_match("/(".implode("|", $settings['ignore_list']).")/", $column_name))
                  continue;
               if(preg_match("/name/", $column_name)) {
                  echo "\t\t<td><a href='?".$type."_id=$id&edit=true'>$column_value</td>\n";
               } else {
                  echo "\t\t<td>$column_value</td>\n";
               }
            }
            foreach($settings['ignore_list'] as $column_name) {
               if(preg_match("/(description|script_data)/", $column_name))
                  continue;
               unset($checked);
               if($item[$column_name])
                  $checked = "checked";
               $input_value = ($item[$column_name] == 0)? 1:0;
               if($input_value > 0) {
                  echo "<td><font size=1><a href='?action=$column_name&value=1&".$type."_id=$id' style='color: red'>Disabled</a> |</font></td>\n";
               } else {
                  echo "<td><font size=1><a href='?action=$column_name&value=0&".$type."_id=$id' style='color: green'>Enabled</a> |</font></td>\n";
               }
            }
            echo "<td><font size=1><a href='?action=delete&".$type."_id=$id'>Delete</a></font></td>\n";
            echo "</tr>\n";
            unset($enabled);
            unset($submit);
            unset($deadline);
         }
      }

      public function editBox($type, $settings) {
         $query = $settings[$type];
         $id = $query[$type."_id"];
         echo "<form name='edit' method=POST action='".$this->base_url.strtoupper(substr($this->page, 0, 1)).substr($this->page, 1)."/'>\n";
         $input_size = 150;
         echo "<table cellpadding=0 cellspacing=0 border=0 width='100%'>\n";
         foreach($settings['fields'] as $key => $value) {
            if(preg_match("/password/", $key))
               $value = "";
            switch($settings['fields'][$key]) {
               case "input":
                  echo "<tr><td width=$input_size>".$settings['description'][$key]."</td><td><input type='text' name='$key' value='".$query[$key]."'></td></tr>\n";
                  break;
               case "timestamp":
                  echo "<tr><td width=$input_size>".$settings['description'][$key]."</td><td><input type='text' name='$key' value='".$query[$key]."'></td></tr>\n";
                  break;
               case "textfield":
                  echo "<tr><td width='$input_size' style='vertical-align: top;'>".$settings['description'][$key]."</td><td>\n";
                  echo "<textarea cols=100 rows=11 name='$key'>".$query[$key]."</textarea>\n</td></tr>\n";
                  break;
               case "select":
                  echo "<tr><td width='$input_size'>".$settings['description'][$key]."</td><td>\n";
                  echo "<select name='$key'>\n";
                  foreach($settings['select'][$key] as $value => $option) {
                     if($this->$key == $value) {
                        echo "<option value='$value' selected> $option\n";
                     } else {
                        echo "<option value='$value'> $option\n";
                     }
                  }
                  echo "</select>\n";
                  echo "</td></tr>\n";
                  break;
            }
         }
         echo "<tr><td><input type='hidden' name='".$type."_id' value='$id'></td></tr>\n";
         echo "<tr><td></td><td><input type='submit' name='submit' value=submit></td></tr>\n";
         echo "</table>\n";
      }

      public function addBox($type, $settings) {
         echo "<div id=\"flip\">Add new competition</div>\n";
         echo "<div id=\"panel\">\n";
         echo "<form name='add' method=POST action='".$this->base_url.strtoupper(substr($this->page, 0, 1)).substr($this->page, 1)."/'>";
         $input_size = 150;
         echo "<table cellpadding=0 cellspacing=0 border=0 width='100%'>\n";
         foreach($settings['fields'] as $key => $value) {
            switch($value) {
               case "input":
                  echo "<tr><td width=$input_size>".$settings['description'][$key]."</td><td><input type='text' name='$key'></td></tr>\n";
                  break;
               case "timestamp":
                  echo "<tr><td width=$input_size>".$settings['description'][$key]."</td><td><input type='text' name='$key'></td></tr>\n";
                  break;
               case "textfield":
                  echo "<tr><td width='$input_size' style='vertical-align: top;'>".$settings['description'][$key]."</td><td><textarea cols=100 rows=11 name='$key'></textarea></td></tr>\n";
                  break;
               case "select":
                  echo "<tr><td width='$input_size'>".$settings['description'][$key]."</td><td>";
                  echo "<select name='$key'>";
                  foreach($settings['select'][$key] as $value => $option) {
                     if($this->$key == $value) {
                        echo "<option value='$value' selected> $option\n";
                     } else {
                        echo "<option value='$value'> $option\n";
                     }
                  }
                  echo "</select>";
                  echo "</td></tr>";
                  break;
            }
         }
         echo "<input type='hidden' name='competition_id' value='".$this->_GET[$type.'_id']."'>";
         echo "<input type='hidden' name='new' value='true'>";
         echo "<tr><td></td><td><input type='submit' name='submit' value=submit></td></tr>";
         echo "</table>";
         echo "</form>";
         echo "</div>";

      }
   }
?>
