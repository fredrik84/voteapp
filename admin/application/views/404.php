<h4>IT FAILED :( </h4>
<pre>
<?php
   if(count($this->error_message) > 1) {
      print_r($this->error_message);
   } else {
      echo $this->error_message[0];
   }
?>
</pre>
