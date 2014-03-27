<?php
   if(!isset($this->_GET['contribution_id'])) {
      $description = array("contribution_id" => "ID",
                           "contributer" => "Contributer",
                           "entry_name" => "Entry Name",
                           "description" => "Description",
                           "created_at" => "Created At",
                           "updated_at" => "Updated At",
                           "ean_id" => "EAN Code ID",
                           "filename" => "Filename"
                        );
      $size = array("contribution_id" => 30,
                    "contributer" => 135,
                    "entry_name" => 200,
                    "description" => 150,
                    "created_at" => 175,
                    "updated_at" => 175,
                    "filename" => 300,
                    "ean_id" => 75
                  );


      $ignore_list = array("event_id", "competition_id", "approved", "beamer_info", "description",  "thumbnail_filename");
      foreach($this->contributions as $competition_name => $compo_data) {
         $compo_id = $competition_array_r[$competition_name];
         $json_array = array("call" => "contributions",
                             "action" => "locked",
                             "competition_id" => $compo_id);
         $this->queryApi($json_array);
         $locked = $this->query[0];
         unset($menu);
         echo "<table cellpadding=0 cellspacing=0 border=0 width='100%' style='border-bottom:1px solid black;'>\n";
         if($locked == "true") {
            if($competitions[$compo_id]->locked == 1)
               $menu[] = "<a href='?action=locked&value=0&competition_id=$compo_id'>Unlock Screening</a>\n";
            else
               $menu[] = "<a href='?action=locked&value=1&competition_id=$compo_id'>Lock Screening</a>\n";
         }
         if($competitions[$compo_id]->submit_enabled == 1)
            $menu[] = "<a href='?action=submit_enabled&value=0&competition_id=$compo_id'>Disable Submit</a>\n";
         else
            $menu[] = "<a href='?action=submit_enabled&value=1&competition_id=$compo_id'>Enable Submit</a>\n";
         if($competitions[$compo_id]->hard_deadline == 1)
            $menu[] = "<a href='?action=hard_deadline&value=0&competition_id=$compo_id'>Ignore Deadline</a>\n";
         if($competitions[$compo_id]->locked == 1 && $competitions[$compo_id]->submit_enabled == 0) {
            if($competitions[$compo_id]->voting == 1)
               $menu[] = "<a href='?action=voting&value=0&competition_id=$compo_id'>Disable Voting</a>\n";
            else
               $menu[] = "<a href='?action=voting&value=1&competition_id=$compo_id'>Enable Voting</a>\n";
         }

         echo "<tr>\n<td>\n<h3>$competition_name <font size=1> ".implode(" | ", $menu)."</font></h3>\n</td>\n</tr>\n";
         echo "</table>\n";
         echo "<table cellpadding=0 cellspacing=0 border=0 width='100%' style='border-bottom:1px solid black;'>\n";

         $tmp = array_keys($compo_data);
         $keys = array_keys($compo_data[$tmp[0]]);
         echo "<tr>\n";
         foreach($keys as $key => $str) 
            if(!preg_match("/".implode("|", $ignore_list)."/", $str))
               echo "<td><font size=1>".$description[$str]."</font></td>\n";
         echo "</tr>\n";

         $id = 0;
         foreach($compo_data as $tmpid => $values) {
            echo "<tr>\n";
            $cont_id = $values['contribution_id'];
            foreach($values as $key => $str) {
               if(!preg_match("/".implode("|", $ignore_list)."/", $key)) {
                  if(strlen($str) > 13 && preg_match("/contributer/", $key))
                     $str = substr($str, 0, 14)."...";
                  if($key == "entry_name") {
                     echo "<td width='".$size[$key]."'><font size=2><a href='?edit=true&contribution_id=".$values['contribution_id']."'>$str</a></font></td>\n";
                  } elseif($key == "filename") {
                     $frontend = dirname($this->base_url)."/frontend/";
                     $ext = array_pop(explode(".", $str));
                     if(preg_match("/(jpg|gif|png|tiff|bmp)/", $ext)) {
                        $name = $values['contributer']." - ".$values['entry_name'];
                        $id++;
                        echo "<td width='".$size[$key]."'><font size=2>\n";
                        echo "<a class='fancybox' data-fancybox-group='$competition_name' title='($id) $name' href='$frontend".$this->_CONFIG['upload_path']."/$this->event_name/$competition_name/$str'>$str</a>\n";
                        echo "</font>\n</td>";
                     } else {
                        echo "<td width='".$size[$key]."'><font size=2><a href='$frontend".$this->_CONFIG['upload_path']."/$this->event_name/$competition_name/$str'>$str</a></font></td>\n";
                     }
                  } else {
                     echo "<td width='".$size[$key]."'><font size=2>$str</font></td>\n";
                  }
               }
            }
            echo "<td><font size=1>\n";
            if($competitions[$compo_id]->locked == 0) {
               if($values['approved'] == -1)
                  echo "<a href='?action=approved&value=1&contribution_id=$cont_id'>Approve</a> / <a href='?action=approved&value=0&contribution_id=$cont_id'>Deny</a>\n";
               else
                  if($values['approved'] == 1) {
                     echo "<font color=green>Approve</font> / <a href='?action=approved&value=0&contribution_id=$cont_id'>Deny</a>\n";
                  } else {
                     echo "<a href='?action=approved&value=1&contribution_id=$cont_id'>Approve</a> / <font color=red>Deny</font>\n";
                  }
            } else {
               if($values['approved'] == 1)
                  echo "<font color=green>Approved</font>";
               else
                  echo "<font color=red>Denied</font>\n";
            }
            echo "</font>\n";
            echo "</td>\n";
            echo "</tr>\n";
         }
         echo "</table>\n";
         echo "<br>";
      }
   }
   if(!isset($this->_GET['edit'])) { 
?>
<div id="flip">Add new contribution</div>
<div id="panel">
<?php
   echo "<form method=POST action='".$this->base_url.strtoupper(substr($this->page, 0, 1)).substr($this->page, 1)."/'>";
   $description = array("contributer" => "Contributer",
                        "entry_name" => "Entry name",
                        "description" => "Description",
                        "beamer_info" => "Beamer Info",
                        "filename" => "Filename",
                        "competition_id" => "Competition",
                        "ean_id" => "EAN code",
                        "event_id" => "Event");
   $textfields =  array("contributer" => "input",
                        "entry_name" => "input",
                        "description" => "textfield",
                        "beamer_info" => "input",
                        "filename" => "input",
                        "ean_id" => "input",
                        "competition_id" => "select");
   $json_array = array("call" => "competitions",
                       "action" => "get");
   $this->queryApi($json_array);
   $tmp = array();
   if(empty($this->query[0]))
      $tmp[] = $this->query;
   else
      $tmp = $this->query;
   foreach($tmp as $key => $value) { 
      $value = (array) $value;
      $select['competition_id'][$value['competition_id']] = $value['name'];
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
               if($this->event_id == $value) {
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
?>
   </form>
</div>
<?php } ?>

<?php
   if(isset($this->_GET['contribution_id']) && isset($this->_GET['edit'])) {
      foreach($this->query as $key => $value) {
         $value = (array) $value;
         if($value['contribution_id'] == $this->_GET['contribution_id'])
            $contribution = $value;
      }
      $textfields = array("contribution_id" => "none",
                    "contributer" => "input",
                    "entry_name" => "input",
                    "description" => "textfield",
                    "beamer_info" => "input",
                    "filename" => "input",
                    "ean_id" => "input",
 //                   "event_id" => "select",
                    "competition_id" => "select");
      $description = array("contribution_id" => "ID",
                           "contributer" => "Contributer",
                           "entry_name" => "Entry Name",
                           "description" => "Description",
                           "beamer_info" => "Beamer Info",
                           "created_at" => "Created At",
                           "updated_at" => "Updated At",
                           "ean_id" => "EAN Code ID",
//                           "event_id" => "Event",
                           "competition_id" => "Competition",
                           "filename" => "Filename"
                        );
      $json_array = array("call" => "competitions",
                          "action" => "get");

      $this->queryApi($json_array);
      $tmp = array();
      if(empty($this->query[0]))
         $tmp[] = $this->query;
      else
         $tmp = $this->query;
      foreach($tmp as $key => $value) {
         $value = (array) $value;
         $select['competition_id'][$value['competition_id']] = $value['name'];
      }
      $input_size = 150;
      $this->competition_id = $contribution['competition_id'];
      echo "<form method=POST action='".$this->base_url.strtoupper(substr($this->page, 0, 1)).substr($this->page, 1)."/'>";
      echo "<table cellpadding=0 cellspacing=0 border=0 width='100%'>\n";
      foreach($contribution as $key => $value) {
         switch($textfields[$key]) {
            case "input":
               echo "<tr><td width=$input_size>".$description[$key]."</td><td><input type='text' name='$key' value='$value'></td></tr>\n";
               break;
            case "timestamp":
               echo "<tr><td width=$input_size>".$description[$key]."</td><td><input type='text' name='$key' value='$value'></td></tr>\n";
               break;
            case "textfield":
               echo "<tr><td width='$input_size' style='vertical-align: top;'>".$description[$key]."</td><td><textarea cols=100 rows=11 name='$key'>$value</textarea></td></tr>\n";
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
      echo "<input type='hidden' name='contribution_id' value='".$this->_GET['contribution_id']."'>";
      echo "<tr><td></td><td><input type='submit' name='submit' value=submit></td></tr>";
      echo "</table>";
         
   }
?>

