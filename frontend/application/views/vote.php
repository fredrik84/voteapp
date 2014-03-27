   <!-- Main Content -->
<?php

   $show_list = array("Contributer" => "contributer",
                      "Entry Name" => "entry_name");

   if(count($contributions) > 0) {
      foreach($contributions as $compo_name => $entries) {   
?>
      <section>
         <header>
            <h2><?=$compo_name?></h2>
         </header>
         <p>
            <div style="padding-left: 50px">
            <table cellpadding=0 cellspacing=0 border=0>
            <?php
               $x = 0;
               foreach($entries as $contribution_id => $entry) {
                  $entry = (array) $entry;
                  if($x++ == 0) {
                     echo "<tr>";
                     echo "<td width=130>Placement</td>";
                     foreach($show_list as $key => $value)
                        echo "<td width=230>".$key."</td>";
                     echo "<td width=130>Download</td>";
                     echo "<td width=200>Thumbnail</td>";
                     echo "<td>Vote</td>";
                     echo "</tr>";

                  }
                  echo "<tr>";
                  echo "<td></td>";
                  foreach($entry as $key => $value) {
                     if(in_array($key, $show_list))
                        echo "<td>$value</td>";
                  }
                  echo "<td><a href='".$this->base_url."".$this->_CONFIG['upload_path']."".$entry['event_name']."/".$compo_name."/".$entry['filename']."'>download</a></td>";
                  echo "<td><a href='".$this->base_url."".$this->_CONFIG['upload_path']."".$entry['event_name']."/".$compo_name."/".$entry['thumbnail_filename']."'>thumbnail</a></td>";
                  echo "<td>\n";
                  for($i = 1; $i <= 5; $i++) {
                     ?><img src="<?=$this->base_url;?>images/vote_unfilled.png" alt="<?=$i?>" id="star<?=$contribution_id?>[<?=$i?>]" onmouseover="hilightStar(<?=$contribution_id?>, <?=$i?>);" onmouseout="dehilightStars(<?=$contribution_id ?>);" onclick="castVote(<?=$contribution_id ?>, <?=$i?>);"/><?php echo "\n";
                  }
                  ?>
                  <script>
<?php if($votes[$contribution_id] > 0) { ?>
                     contributions[<?=$contribution_id?>] = <?=$votes[$contribution_id]?>; 
                     hilightStar(<?=$contribution_id ?>, <?=$votes[$contribution_id]?>);
<? } else { ?>
                     contributions[<?=$contribution_id?>] = 0; 
<? } ?>
                  </script>
                  <?php

                  echo "</td>";
                  echo "</tr>";
               }
            ?>
            </table>
            </div>
            <?php
               $json_array = array("call" => "contributions",
                                   "action" => "disqualified",
                                   "competition_id" => $entry['competition_id']);
               $this->queryApi($json_array);
               if(!empty($this->query[0])) {
                  foreach($this->query as $value)
                     $screened[] = $value->contributer." - ".$value->entry_name;
                  echo "<br><pre style='line-height: 15px'><font size=2>A preselection occured for this competition:\n".implode(", ", $screened)."</font></pre>";
               }
            ?>
         </p>
      </section>
<?php
      }
   } else {
?>
      <section>
         <header>
            <h2>Voting</h2>
            <h3></h3>
         </header>
         <p>
            There is no competition to vote on yet
         </p>
      </section>
<?php
   }
?>
</div>
<div class="3u">
