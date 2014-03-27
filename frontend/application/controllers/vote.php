<?php
   if(!isset($_SESSION['loggedin'])) {
      header("Location: ".$this->base_url."Login/?return=Vote");
   }

   $json_array = array("call" => "events",
                       "action" => "current");
   $this->queryApi($json_array);
   $this->event_id = $this->query[0]->event_id;

   $json_array = array("call" => "contributions",
                       "action" => "voting",
                       "event_id" => $this->event_id,
                    );
   $this->queryApi($json_array);
   if($this->query[0] == "false")
      $contributions = array();
   else
      foreach($this->query as $value) 
         $contributions[$value->name][$value->contribution_id] = $value;

   $json_array = array("call" => "votes",
                       "action" => "votesbyeanid",
                       "ean_id" => $_SESSION['ean_id']);
   $this->queryApi($json_array);

   foreach($this->query as $value) {
      $votes[$value->contribution_id] = $value->result;
   }
?>

