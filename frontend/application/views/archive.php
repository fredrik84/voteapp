   <!-- Main Content -->
<?php
   if(count($compos) > 0) {
      foreach($compos as $key => $compo) {
         $keys[] = $compo['name'];
         $json_array = array("call" => "contributions",
                             "action" => "get",
                             "competition_id" => $compo['competition_id']);
         $this->queryApi($json_array);
         echo "<section>\n";
         echo "<header>\n";
         echo "<h2><div id='".$compo['name']."'>".$compo['name']."</div></h2>\n";
//            <h3>A generic two column layout</h3>
         echo "</header>\n";
         echo "<p>\n";
         echo "<table cellpadding=0 cellspacing=0 border=0>\n";
         echo "<tr>";
         echo "<td width='100' style='border-bottom: 1px solid black'>Position</td>";
         echo "<td width='200' style='border-bottom: 1px solid black'>Contributer</td>";
         echo "<td width='300' style='border-bottom: 1px solid black'>Entry name</td>";
         echo "<td width='300' style='border-bottom: 1px solid black'>Score</td>";
         echo "</tr>";
         foreach($this->query as $key => $value) {
            echo "<tr>";
            echo "<td>$value->placement</td>";
            echo "<td>$value->contributer</td>";
            echo "<td>$value->entry_name</td>";
            echo "<td>$value->score</td>";
            echo "</tr>";
         }
         echo "</table>";
         echo "</p>\n";
         echo "</header>";
         echo "</section>\n";
      }
   ?>
   </div>
   <div class="3u">

      <!-- Sidebar -->
         <section>
            <header>
               <h2>Events</h2>
            </header>
   <?php
      $json_array = array("call" => "events",
                          "action" => "archive");
      $this->queryApi($json_array);
      if(count($this->query) > 0) {
         echo "<ul class=\"link-list\">\n";
         foreach($this->query as $value) 
            echo "<li><a href='?event_id=$value->event_id'>$value->event_name</a></li>\n";
         echo "</ul>\n";
      } else {
         echo "<p>No events available</p>";
      }
   ?>
         </section>

<?php
   } else {
      echo "<section>\n";
      echo "<header>\n";
      echo "<h2>Archive</h2>\n";
      echo "</header>";
      echo "<p>This is not the archive you're looking for, come back after the event.</p>\n";
      echo "</section>\n";
   }
?>
