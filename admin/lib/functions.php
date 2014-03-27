<?php
   function start_mysql($username, $password, $database, $hostname = "localhost") {
      if(mysql_connect($hostname,$username,$password)) {
         if(!mysql_select_db($database)) {
            return false;
         } else {
            return true;
         }
      } else {
         return true;
      }
   }

   function __autoload($class_name) {
      $full_path = "lib/classes/$class_name.php";
      if(!file_exists($full_path))
         die("Failed to include class $class_name\n");
      require_once($full_path);
   }
?>
