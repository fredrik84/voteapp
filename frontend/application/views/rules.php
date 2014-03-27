   <!-- Main Content -->
<?php
   foreach($compos as $key => $compo) {
      $keys[] = $compo['name'];
      echo "<section>\n";
      echo "<header>\n";
      echo "<h2><div id='".$compo['name']."'>".$compo['name']."</div></h2>\n";
//            <h3>A generic two column layout</h3>
      echo "</header>\n";
      echo "<p>\n";
      echo "<pre>\n";
      echo $compo['description'];
      echo "<div style='padding-left: 15px'>";
      echo "<table cellpadding=0 cellspacing=0 border=0>";
      foreach($compo['rules'] as $value) {
         echo "<tr><td width=15>-</td><td>";
         echo substr(preg_replace("/^\s+/", "", $value), 1)."\n";
         echo "</td></tr>";
      }
      echo "</table>";
      echo "</div>";
      echo "</pre>\n";
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
            <h2>Competitions</h2>
         </header>
         <ul class="link-list">
<?php
   foreach($keys as $value) {
      echo "<li><a href='#$value'>$value</a></li>\n";
   }
?>
         </ul>
      </section>

