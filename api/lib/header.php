<?php
   include("config.php");
   include("lib/functions.php");

   if(!start_mysql($_CONFIG['username'], $_CONFIG['password'], $_CONFIG['database'], $_CONFIG['hostname']))
      throw new Exception("Failed to connect to database.\n<br>".mysql_error());
   else
      define("__MYSQL_IN_USE__", TRUE);


?>
