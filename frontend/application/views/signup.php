      <section>
         <header>
            <h2>Sign up!</h2>
            <h3>we need more information about you</h3>
         </header>
         <p>
         <form method=POST action="?ean_code=<?=$this->_GET['ean_code']?>">
<table cellpadding=0 cellspacing=0 border=0 width=50%>
   <?php
      $missing = (array) $this->query;
      foreach($missing as $name) {
         $title = strtoupper(substr($name, 0, 1)).strtolower(substr($name, 1));
         echo "<tr>\n";
         echo "<td align=right style='padding-right: 15px'>$title:</td>\n";
         echo "<td><input type='text' name='$name'></td>\n";
         echo "</tr>\n";
      }
      echo "<tr>\n";
      echo "<td align=right style='padding-right: 15px'></td>\n";
      echo "<td><input type='submit' name='submit' value='sign up'></td>\n";
      echo "</tr>\n";
   ?>
   <tr>
      <td></td><td><br><a href='?justvoting=true&ean_code=<?=$this->_GET['ean_code']?>'>I just want to vote, skip signing up!</a></td>
   </tr>
</table>
</form><br>
         </p>
      </section>
</div>

