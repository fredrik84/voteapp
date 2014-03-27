<?php
   $json_array = array("call" => "news",
                       "action" => "get");
   $this->queryApi($json_array);

   foreach($this->query as $key => $value)
      $short_list[$value->new_id] = $value->title;

?>
