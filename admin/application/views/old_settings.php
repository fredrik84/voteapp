<?php
   echo "<table cellpadding=0 cellspacing=0 border=0 style='border-bottom: 1px solid black' width='100%'>\n";
   echo "<tr><td><h3>\n";
   if(!isset($this->_GET['edit'])) {
      echo "Scripts";
   } else {
      foreach($this->query as $key => $value) {
         $value = (array) $value;
         if($value['script_id'] == $this->_GET['script_id']) {
            $competition = $value;
            break;
         }
      }
      echo $value['name'];
   }
   echo "</h3></td></tr>\n";
   echo "</table>\n";
   $type = "script";
   $description = array("script_id" => "ID",
                     "script_name" => "Competition Name",
                     "script_data" => "Script Data",
                     "event_name" => "Event",
                     "event_id" => "Event",
                     "updated_at" => "Updated at",);
   $size = array("script_id" => 30,
                 "script_name" => 300,
                 "event_name" => 175,
                 "updated_at" => 175);
   $ignore_list = array("script_data");
   $settings['ignore_list'] = $ignore_list;
   $settings['description'] = $description;
   $settings['size'] = $size;
   $settings['options'][''] = "action=delete";
   $settings['fields'] = array("script_id" => "none",
                               "script_name" => "input",
                               "script_data" => "textfield",
                               "event_id" => "select",);
   $settings[$type] = $script;
   $script = $this->query;
   $json_array = array("call" => "events",
                       "action" => "get");
   $this->queryApi($json_array);
   if(empty($this->query[0])) {
      $tmp = $this->query;
      unset($this->query);
      $this->query[] = $tmp;
   }
   foreach($this->query as $key => $value) {
      $value = (array) $value;
      $settings['select']['event_id'][$value['event_id']] = $value['event_name'];
   }

   if(!isset($this->_GET['edit'])) {
      echo "<table cellpadding=0 cellspacing=0 border=0>";
      $this->printTable($type, $settings, $script);
   } else {
      $this->editBox($type, $settings);
   }

   echo "</table>";

   if(!isset($this->_GET['edit'])) {
?>

<div id="flip">Add new script</div>
   <div id="panel">
      <form method=POST action='<?=$this->base_url.strtoupper(substr($this->page, 0, 1)).substr($this->page, 1);?>/'>
         <table cellpadding=0 cellspacing=0 border=0>
            <tr>
               <td width=150>Script name</td>
               <td><input type='text' name='script_name'></td>
            </tr><tr>
               <td style='vertical-align: top'>Script</td>
               <td><textarea cols=80 rows=20 name='script_data'></textarea></td>
            </tr><tr>
               <td style='vertical-align: top'>Event</td>
               <td>
                  <select name='event_id'>
<?php
   $json_array = array("call" => "events",
                       "action" => "get");
   $this->queryApi($json_array);
   if(empty($this->query[0])) {
      $tmp = $this->query;
      unset($this->query);
      $this->query[] = $tmp;
   }
   foreach($this->query as $key => $value) {
      $value = (array) $value;
      echo "<option value='".$value['event_id']."'> ".$value['event_name']."\n";
   }
?>
                  </select>
               </td>
            </tr><tr>
               <td></td>
               <td>
                  <input type='hidden' name='new' value=true>
                  <input type='submit' name='submit' value=submit>
               </td>
            </tr>
         </table>
      </form>
   </div>
</div>
<?php
   }
?>
