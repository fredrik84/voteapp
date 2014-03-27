<table cellpadding=0 cellspacing=0 border=0 style='border-bottom: 1px solid black' width='100%'>
<tr><td><h3>
<?php
   if(!isset($this->_GET['edit'])) {
      echo "Events";
   } else {
      foreach($this->query as $key => $value) {
         $value = (array) $value;
         if($value['event_id'] == $this->_GET['event_id']) {
            $competition = $value;
            break;
         }
      }
      echo $value['event_name'];
   }
?>
</h3></td></tr>
</table>
<table cellpadding=0 cellspacing=0 border=0>
<?php
   $description = array("event_id" => "ID",
                        "fake_event_id" => "Fake Event ID",
                        "event_name" => "Event Name",
                        "start_time" => "Start Time",
                        "end_time" => "End Time");
   if(!isset($this->_GET['edit'])) {
      $size = array("event_id" => 30,
                    "fake_event_id" => 90,
                    "event_name" => 450,
                    "start_time" => 175,
                    "end_time" => 175);
      $ignore_list = array("enabled");
      $x = 0;
      foreach($this->query as $key => $value) {
         $value = (array) $value;
         echo "\t<tr>\n";
         $keys = array_keys($value);
         if($x++ == 0) {
            foreach($keys as $str) {
               if(preg_match("/(".implode("|", $ignore_list).")/", $str))
                  continue;
               echo "\t\t<td width=".$size[$str]."><font size='1'>".$description[$str]."</font></td>\n";

            }
            echo "\t\t<td width=120><font size=1>Enabled</font></td>\n";
            echo "\t</tr>\n";
            echo "\t<tr>\n";
         }
         $event_id = $value['event_id'];
         foreach($value as $column_name => $col_value) {
            if(preg_match("/(".implode("|", $ignore_list).")/", $column_name))
               continue;
            if(preg_match("/name/", $column_name)) {
               echo "\t\t<td><a href='?event_id=$event_id&edit=true'>$col_value</td>\n";
            } else {
               echo "\t\t<td>$col_value</td>\n";
            }
         }
         foreach($ignore_list as $col_name) {
            if(preg_match("/description/", $col_name))
               continue;
            unset($checked);
            if($value[$col_name])
               $checked = "checked";
            $input_value = ($value[$col_name] == 0)? 1:0;
            if($input_value > 0) {
               echo "<td><font size=1><a href='?action=$col_name&value=1&event_id=$event_id' style='color: red'>Disabled</a> | <a href='?action=delete&event_id=$event_id'>Delete</a></font></td>";
            } else {
               echo "<td><font size=1><a href='?action=$col_name&value=0&event_id=$event_id' style='color: green'>Enabled</a> | <a href='?action=delete&event_id=$event_id'>Delete</a></font></td>";
            }
         }
         echo "\t</tr>\n";
         unset($enabled);
         unset($submit);
         unset($deadline);
      }
   } else {
      $type = array("event_id" => "none",
                    "event_name" => "input",
                    "start_time" => "timestamp",
                    "fake_event_id" => "input",
                    "end_time" => "timestamp");
      echo "<form method=post action='".$this->base_url.strtoupper(substr($this->page, 0, 1)).substr($this->page, 1)."/'>";
      $input_size = 150;
      foreach($competition as $key => $value) {
         switch($type[$key]) {
            case "input":
               echo "<table cellpadding=0 cellspacing=0 border=0><tr>";
               echo "<td width=$input_size>".$description[$key]."</td><td><input type='text' name='$key' value='$value'></td>";
               echo "</tr></table>";
               break;
            case "timestamp":
               echo "<table cellpadding=0 cellspacing=0 border=0><tr>";
               echo "<td width=$input_size>".$description[$key]."</td><td><input type='text' name='$key' value='$value'></td>";
               echo "</tr></table>";
               break;
            case "textfield":
               echo "<table cellpadding=0 cellspacing=0 border=0><tr>";
               echo "<td width='$input_size' style='vertical-align: top;'>".$description[$key]."</td><td><textarea cols=100 rows=20 name='$key'>$value</textarea></td>";
               echo "</tr></table>";
               break;
         }
      }
      echo "<input type='hidden' name='event_id' value='".$this->_GET['event_id']."'>";
      echo "<input type='submit' name='submit' value=submit>";
      echo "</form>";
   }
?>
</table><br>
<?php if(!isset($this->_GET['edit'])) { ?>
<div id="flip">Add new event</div>
<div id="panel">
   <form method=POST>
<?php
   $description = array("event_name" => "Event Name",
                        "start_time" => "Start Time",
                        "end_time" => "End Time",
                        "fake_event_id" => "Fake event ID");
   $textfields =  array("event_name" => "input",
                        "start_time" => "timestamp",
                        "end_time" => "timestamp",
                        "fake_event_id" => "input");
   $json_array = array("call" => "events",
                       "action" => "get");
   $this->queryApi($json_array);
   foreach($this->query as $key => $value)
      $this->query[$key] = (array) $value;
   if(empty($this->query[0]))
      $tmp[] = $this->query;
   else
      $tmp = $this->query;
   foreach($tmp as $key => $value) 
      $select['event_id'][$value['event_id']] = $value['event_name'];
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
               echo "$option - $value\n";
               echo "<option value='$value'> $option\n";
            }
            echo "</select>";
            echo "</td></tr>";
            break;
      }
   }
   echo "<input type='hidden' name='event_id' value='".$this->_GET['event_id']."'>";
   echo "<input type='hidden' name='new' value='true'>";
   echo "<tr><td></td><td><input type='submit' name='submit' value=submit></td></tr>";
   echo "</table>";
?>
   </form>
</div>
<?php } ?>
