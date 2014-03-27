<?php
   echo "<table cellpadding=0 cellspacing=0 border=0 style='border-bottom: 1px solid black' width='100%'>\n";
   echo "<tr><td><h3>\n";
   if(!isset($this->_GET['edit'])) {
      echo "Configuration";
   } else {
      foreach($this->query as $key => $value) {
         $value = (array) $value;
         if($value['configuration_id'] == $this->_GET['configuration_id']) {
            $configuration = $value;
            break;
         }
      }
      echo $value['name'];
   }
   echo "</h3></td></tr>\n";
   echo "</table>\n";
   $type = "configuration";
   $description = array("configuration_id" => "ID",
                     "name" => "Configuration Name",
                     "value" => "Value",
                     "description" => "Description");
   $size = array("configuration_id" => 30,
                 "name" => 130,
                 "value" => 600,
                 "description" => 350);
   $ignore_list = array("enabled");
   $settings['ignore_list'] = $ignore_list;
   $settings['description'] = $description;
   $settings['size'] = $size;
   $settings['options']['Options'] = "";
   $settings['fields'] = array("configuration_id" => "none",
                               "name" => "input",
                               "value" => "input",
                               "description" => "input",);
   $settings[$type] = $configuration;
   foreach($this->query as $key => $value) 
      $this->query[$key] = (Array) $value;
   $configuration = $this->query;
   
   if(!isset($this->_GET['edit'])) {
      echo "<table cellpadding=0 cellspacing=0 border=0>";
      $this->printTable($type, $settings, $configuration);
      echo "</table>";
   } else {
      $this->editBox($type, $settings);
   }

   echo "</table>";

   if(!isset($this->_GET['edit'])) {
?>

<div id="flip">Add new configuration</div>
   <div id="panel">
      <form method=POST action='<?=$this->base_url.strtoupper(substr($this->page, 0, 1)).substr($this->page, 1);?>/'>
         <table cellpadding=0 cellspacing=0 border=0>
            <tr>
               <td width=150>Configuration name</td>
               <td><input type='text' name='name'></td>
            </tr><tr>
               <td style='vertical-align: top'>Value</td>
               <td><input type="text" name="value"></td>
            </tr><tr>
               <td style='vertical-align: top'>Description</td>
               <td><input type="text" name="description"></td>
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
