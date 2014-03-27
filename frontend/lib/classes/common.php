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
   }
?>
