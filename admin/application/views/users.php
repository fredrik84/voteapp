<?php if(!isset($this->_GET['user_id']) && !isset($this->_GET['contribution_id'])) { ?>
<table cellpadding=0 cellspacing=0 border=0 style='border-bottom: 1px solid black' width='100%'>
<tr><td><h3>
<?php
   if(!isset($this->_GET['user_id'])) {
      echo "Users";
   } else {
      foreach($this->users_list as $key => $value) {
         $value = (array) $value;
         if($value['user_id'] == $this->_GET['user_id']) {
            $user = $value;
            break;
         }
      }
      echo $value['name'];
   }
?>
</h3></td></tr>
</table>
<table cellpadding=0 cellspacing=0 border=0>
<?php
   if(!isset($this->_GET['user_id']) && !isset($this->_GET['contributer_id'])) {
      $description = array("user_id" => "ID",
                           "username" => "Username",
                           "access" => "Level",
                           "event_id" => "Event ID",
                           "updated_at" => "Updated at",
                           "created_at" => "Created at");
      $size = array("user_id" => 30,
                    "username" => 150,
                    "access" => 50,
                    "event_id" => 200,
                    "updated_at" => 325,
                    "created_at" => 525);
      $ignore_list = array("created_at", "password");

      $x = 0;
      foreach($this->users_list as $key => $value) {
         $value = (array) $value;
         echo "\t<tr>\n";
         $keys = array_keys($value);
         if($x == 0) 
            foreach($keys as $v2) 
               if(!preg_match("/(".implode("|", $ignore_list).")/", $v2))
                  echo "<td width='".$size[$v2]."'>".$description[$v2]."</td>\n";

         if($x++ == 0) {
            echo "\t\t<td width=80><font size=1>Options</font></td>\n";
            echo "\t</tr>\n";
            echo "\t<tr>\n";
         }
         $user_id = $value['user_id'];
         foreach($value as $column_name => $col_value) {
            if(preg_match("/(".implode("|", $ignore_list).")/", $column_name))
               continue;
            if(preg_match("/username/", $column_name)) {
               echo "\t\t<td><a href='?user_id=$user_id'>$col_value</td>\n";
            } else {
               echo "\t\t<td>$col_value</td>\n";
            }
         }
         echo "<td width=55><font size=1><a href='?action=delete&user_id=$user_id'>Delete</a></font></td>\n";
         echo "\t</tr>\n";
         unset($enabled);
         unset($submit);
         unset($deadline);
      }
?>
</table>
<?php if(empty($this->_GET['edit'])) { ?>
<div id="flip">Add new admin user</div>
<div id="panel">
<?php
   echo "<form method=POST action='".$this->base_url.strtoupper(substr($this->page, 0, 1)).substr($this->page, 1)."/'>";
   $description = array("username" => "Username",
                        "password" => "Password",
                        "access" => "Level",
                        "event_id" => "Event");
   $textfields =  array("username" => "input",
                        "password" => "input",
                        "access" => "select",
                        "event_id" => "select");
   $json_array = array("call" => "events",
                       "action" => "get");
   for($i=0; $i < 5; $i++)
      $select['access'][$i] = $i;
   $this->queryApi($json_array);
   $tmp = array();
   if(empty($this->query[0]))
      $tmp[] = $this->query;
   else
      $tmp = $this->query;
   foreach($tmp as $key => $value) {
      $value = (array) $value;
      $select['event_id'][$value['event_id']] = $value['event_name'];
   }
   $input_size = 150;
   echo "<table cellpadding=0 cellspacing=0 border=0 width='100%'>\n";
   foreach($textfields as $key => $value) {
      switch($value) {
         case "input":
            echo "<tr><td width=$input_size>".$description[$key]."</td><td><input type='text' name='$key'></td></tr>\n";
            break;
         case "timestamp":
            echo "<tr><td width=$input_size>".$description[$key]."</td><td><input type='text' name='$key'></td></tr>\n";
            break;
         case "textfield":
            echo "<tr><td width='$input_size' style='vertical-align: top;'>".$description[$key]."</td><td><textarea cols=100 rows=11 name='$key'></textarea></td></tr>\n";
            break;
         case "select":
            echo "<tr><td width='$input_size'>".$description[$key]."</td><td>";
            echo "<select name='$key'>";
            foreach($select[$key] as $value => $option) {
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
   echo "<input type='hidden' name='user_id' value='".$this->_GET['user_id']."'>";
   echo "<input type='hidden' name='new' value='true'>";
   echo "<tr><td></td><td><input type='submit' name='submit' value=submit></td></tr>";
   echo "</table>";
?>
   </form>
</div>
<?php } ?>
<br><br>
<table cellpadding=0 cellspacing=0 border=0 style='border-bottom: 1px solid black' width='100%'>
<tr><td><h3>
<?php
   if(!isset($this->_GET['contributer_id'])) {
      echo "Contributers";
   } else {
      foreach($this->query as $key => $value) {
         $value = (array) $value;
         if($value['contributer_id'] == $this->_GET['contributer_id']) {
            $competition = $value;
            break;
         }
      }
      echo $value['contributer'];
   }
?>
</h3></td></tr>
</table>
<table cellpadding=0 cellspacing=0 border=0>
<?php

      $description = array("contributer_id" => "ID",
                           "username" => "Contributer",
                           "fullname" => "Name",
                           "email" => "email");
      $size = array("contributer_id" => 30,
                    "username" => 170,
                    "fullname" => 250,
                    "email" => 300);
      $ignore_list = array("telephone", "firstname", "lastname");
      

      $x = 0;
      foreach($this->contributers_list as $key => $value) {
         $value = (array) $value;
         $value['fullname'] = implode(" ", array($value['firstname'], $value['lastname']));
         echo "\t<tr>\n";
         $keys = array_keys($value);
         if($x == 0) 
            foreach($keys as $v2) 
               if(!preg_match("/(".implode("|", $ignore_list).")/", $v2))
                  echo "<td width='".$size[$v2]."'>".$description[$v2]."</td>";
         if($x++ == 0) {
            echo "\t\t<td><font size=1>Options</font></td>\n";
            echo "\t</tr>\n";
            echo "\t<tr>\n";
         }
         $contributer_id = $value['contributer_id'];
         foreach($value as $column_name => $col_value) {
            if(preg_match("/(".implode("|", $ignore_list).")/", $column_name))
               continue;
            if(preg_match("/username/", $column_name)) {
               echo "\t\t<td><a href='?contributer_id=$contributer_id'>$col_value</td>\n";
            } else {
               echo "\t\t<td>$col_value</td>\n";
            }
         }
         if(!empty($value['ean_id'])) {
            echo "<td width=50><font size=1><a href='?action=approve&contributer_id=$contributer_id'>Approve</a> |</td>";
         } else {
            echo "<td width=50><font size=1>Verified |</td>";
         }
         echo "<td width=50><font size=1><a href='?action=delete&contributer_id=$contributer_id'>Delete</a></td>";
         echo "\t</tr>\n";
         unset($enabled);
         unset($submit);
         unset($deadline);
      }
   }
   echo "</table>";
}
   if(isset($this->_GET['user_id'])) {
?>
<table cellpadding=0 cellspacing=0 border=0 style='border-bottom: 1px solid black' width='100%'>
<tr><td><h3>
<?php echo $user['username']; ?>
</h3></td></tr>
</table>
<?php
      echo "<form method=POST action='".$this->base_url.strtoupper(substr($this->page, 0, 1)).substr($this->page, 1)."/'>";
      $description = array("user_id" => "ID",
                           "username" => "Username",
                           "password" => "Password",
                           "access" => "Level",
                           "event_id" => "Event");
      $type = array("user_id" => "none",
                    "username" => "input",
                    "password" => "input",
                    "access" => "select",
                    "event_id" => "select");
      $json_array = array("call" => "events",
                          "action" => "get");
      $this->queryApi($json_array);
      $tmp = array();
      if(empty($this->query[0]))
         $tmp[] = $this->query;
      else
         $tmp = $this->query;
      foreach($tmp as $key => $value) {
         $value = (array) $value;
         $select['event_id'][$value['event_id']] = $value['event_name'];
      }
      for($i = 0; $i < 5; $i++)
         $select['access'][$i] = $i;
      foreach($this->users_list as $key => $value) {
         $value = (array) $value;
         if($value['user_id'] == $this->_GET['user_id']) {
            $user = $value;
            break;
         }
      }

      $this->event_id = $user['event_id'];
      $this->access = $user['access']; 
      $input_size = 150;
      echo "<table cellpadding=0 cellspacing=0 border=0 width='100%'>\n";
      foreach($user as $key => $value) {
         if(preg_match("/password/", $key))
            $value = "";
         switch($type[$key]) {
            case "input":
               echo "<tr><td width=$input_size>".$description[$key]."</td><td><input type='text' name='$key' value='".$value."'></td></tr>\n";
               break;
            case "timestamp":
               echo "<tr><td width=$input_size>".$description[$key]."</td><td><input type='text' name='$key'></td></tr>\n";
               break;
            case "textfield":
               echo "<tr><td width='$input_size' style='vertical-align: top;'>".$description[$key]."</td><td><textarea cols=100 rows=11 name='$key'></textarea></td></tr>\n";
               break;
            case "select":
               echo "<tr><td width='$input_size'>".$description[$key]."</td><td>";
               echo "<select name='$key'>";
               foreach($select[$key] as $value => $option) {
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
      echo "<input type='hidden' name='user_id' value='".$this->_GET['user_id']."'>";
      echo "<tr><td></td><td><input type='submit' name='submit' value=submit></td></tr>";
      echo "</table>";
   } elseif(isset($this->_GET['contributer_id'])) {
      echo "<form method=POST action='".$this->base_url.strtoupper(substr($this->page, 0, 1)).substr($this->page, 1)."/'>";
      $description = array("contributer_id" => "ID",
                           "contributer" => "Contributer",
                           "firstname" => "Firstname",
                           "lastname" => "Lastname",
                           "telephone" => "Telephone",
                           "country" => "Country",
                           "address" => "Address",
                           "email" => "E-mail");
      $type = array("contributer_id" => "none",
                    "contributer" => "input",
                    "firstname" => "input",
                    "lastname" => "input",
                    "telephone" => "input",
                    "country" => "input",
                    "address" => "input",
                    "email" => "input");
      foreach($this->contributers_list as $key => $value) {
         $value = (Array) $value;
         if($value['contributer_id'] == $this->_GET['contributer_id']) {
            $contributer = $value;
            break;
         }
      }
      krsort($contributer);
      $input_size = 150;
      echo "<table cellpadding=0 cellspacing=0 border=0 width='100%'>\n";
      foreach($contributer as $key => $value) {
         switch($type[$key]) {
            case "input":
               echo "<tr><td width=$input_size>".$description[$key]."</td><td><input type='text' name='$key' value='".$value."'></td></tr>\n";
               break;
            case "timestamp":
               echo "<tr><td width=$input_size>".$description[$key]."</td><td><input type='text' name='$key'></td></tr>\n";
               break;
            case "textfield":
               echo "<tr><td width='$input_size' style='vertical-align: top;'>".$description[$key]."</td><td><textarea cols=100 rows=11 name='$key'></textarea></td></tr>\n";
               break;
            case "select":
               echo "<tr><td width='$input_size'>".$description[$key]."</td><td>";
               echo "<select name='$key'>";
               foreach($select[$key] as $value => $option) {
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
      echo "<input type='hidden' name='competition_id' value='".$this->_GET['competition_id']."'>";
      echo "<input type='hidden' name='new' value='true'>";
      echo "<tr><td></td><td><input type='submit' name='submit' value=submit></td></tr>";
      echo "</table>";
   }
?>
</table>
</div>
