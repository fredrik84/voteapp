<?php
   echo "<table cellpadding=0 cellspacing=0 border=0 style='border-bottom: 1px solid black' width='100%'>\n";
   echo "<tr><td><h3>\n";
   if(!isset($this->_GET['edit'])) {
      echo "Competitions";
   } else {
      foreach($this->query as $key => $value) {
         $value = (array) $value;
         if($value['competition_id'] == $this->_GET['competition_id']) {
            $competition = $value;
            break;
         }
      }
      echo $value['name'];
   }
   echo "</h3></td></tr>\n";
   echo "</table>\n";

   $type = "competition";
   $description = array("competition_id" => "ID",
                     "name" => "Competition Name",
                     "start_time" => "Start Time",
                     "end_time" => "End Time",
                     "locked" => "Locked",
                     "voting" => "Voting",
                     "show_time" => "Show Time");
   $size = array("competition_id" => 30,
                 "name" => 300,
                 "start_time" => 175,
                 "end_time" => 175,
                 "show_time" => 175);

   $ignore_list = array("compo_enabled",
                        "submit_enabled",
                        "hard_deadline",
                        "locked",
                        "voting",
                        "description");
   $settings['ignore_list'] = $ignore_list;
   $settings['description'] = $description;
   $settings['size'] = $size;
   $settings[$type] = $competition;
   $settings['options']['Enabled'] = "action=compo_enabled";
   $settings['options']['Submit'] = "action=submit_enabled";
   $settings['options']['Deadline'] = "action=hard_deadline";
   $settings['options']['Locked'] = "action=locked";
   $settings['options']['Voting'] = "action=voting";
   $settings['options'][''] = "action=delete";
   $settings['fields'] = array("competition_id" => "none",
                               "name" => "input",
                               "description" => "textfield",
                               "start_time" => "timestamp",
                               "end_time" => "timestamp",
                               "show_time" => "timestamp");

   echo "<table cellpadding=0 cellspacing=0 border=0>\n";
   if(!isset($this->_GET['edit'])) {
      $this->printTable($type, $settings, $this->query);
   } else {
      $this->editBox($type, $settings);
   }
   echo "</table>";
   echo "<br>";

   if(!isset($this->_GET['edit'])) { 
      echo "<form method=POST action='".$this->base_url.strtoupper(substr($this->page, 0, 1)).substr($this->page, 1)."/'>";
      $settings['description'] = array("name" => "Competition Name",
                           "description" => "Description",
                           "start_time" => "Start Time",
                           "end_time" => "End Time",
                           "show_time" => "Show Time",
                           "event_id" => "Event");
      $settings['fields'] =  array("name" => "input",
                           "description" => "textfield",
                           "start_time" => "timestamp",
                           "end_time" => "timestamp",
                           "show_time" => "timestamp",
                           "event_id" => "select");
      $json_array = array("call" => "events",
                          "action" => "get");
      $this->queryApi($json_array);
      unset($tmp);
      if(empty($this->query[0]))
         $tmp[] = $this->query;
      else
         $tmp = $this->query;
      foreach($tmp as $key => $value) { 
         $value = (array) $value;
         $settings['select']['event_id'][$value['event_id']] = $value['event_name'];
      }
      $this->addBox($type, $settings);
   }
?>
