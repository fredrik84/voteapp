<table cellpadding="0" cellspacing="0" border="0">
   <tr>
      <td>
         <table cellpadding="0" cellspacing"0" border="0">
            <form method=POST>
            <?php
               $json_array = array("call" => "news",
                                   "action" => "get",
                                   );
               if(isset($this->_GET['new_id']))
                  $json_array['new_id'] = $this->_GET['new_id'];
               $this->queryApi($json_array);
               if(empty($this->_GET['new_id'])) {
                  foreach($this->query as $value) {
                     $value = (array) $value;
                     foreach($value as $key => $tmp) {
                        $this->query[0]->$key = "";
                     }
                     $this->query[0]->new_post = true;
                  }
               }
               foreach($this->query as $value) {
                  $value = (array) $value;
                  foreach($value as $key => $value) {
                     $name = strtoupper(substr($key, 0, 1)).strtolower(substr($key, 1));
                     $style = "style='padding-right: 15px; vertical-align: top;' align=right";
                     switch($key) {
                        case "new_post":
                           echo "<input type='hidden' name=new_post value='true'>";
                           break;
                        case "new_id":
                           echo "<input type='hidden' name=new_id value='$value'>";
                           break;
                        case "body":
                           echo "<tr>\n";
                           echo "<td $style>$name:</td>\n";
                           echo "<td><textarea cols=80 rows=20 name='$key'>$value</textarea></td>\n";
                           echo "</tr>\n";
                           break;
                        default:
                           echo "<tr>\n";
                           echo "<td $style>$name:</td>\n";
                           echo "<td><input type=\"text\" name=\"$key\" value=\"$value\" style='width: 300px'></td>\n";
                           echo "</tr>\n";
                           break;
                     }
                  }
                  break;
               }
            ?>
            <tr>
               <td></td>
               <td><input type="submit" name="submit" value="submit"></td>
            </tr>
            </form>
         </table>
      </td>
      <td style='padding-left: 30px'>
         <table cellpadding="0" cellspacing="0" border="0" width=350 style="border-left: 1px solid black">
            <?php
               $json_array = array("call" => "news",
                                   "action" => "get");
               $this->queryApi($json_array);
               foreach($this->query as $value) {
                  $color = ($color == "#fffaff")? "#ffffff":"#fffaff";
            ?>
            <tr>
               <td style='background-color: <?=$color;?>;width:300px;padding-left: 10px;' height=>
                  <a href='?new_id=<?=$value->new_id?>'><?=$value->title?></a> 
               </td>
               <td style='background-color: <?=$color;?>;padding-left: 10px;padding-right: 10px' height=>
                  (<a href='?delete=<?=$value->new_id?>'>Delete</a>)
               </td>
            </tr>
            <?php
               }
            ?>
         </table>
      </td>
   </tr>
</table>
