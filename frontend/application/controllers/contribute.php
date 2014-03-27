<?php
   $json_array = array("call" => "events",
                       "action" => "current");
   $this->queryApi($json_array);
   $this->event_id = $this->query[0]->event_id;
   $this->event_name = $this->query[0]->event_name;

   if(isset($this->_POST['submit'])) {
      if(!isset($this->_GET['contribution_id'])) {
         $required = array("contributer", "entry_name");
         foreach($required as $value)
            if(empty($this->_POST[$value]))
               $error_message[] = "Missing $value";
         if(empty($error_message)) {
            $json_array = array("call" => "contributions",
                                "action" => "register",
                               );
            unset($this->_POST['submit']);
            foreach($this->_POST as $key => $value) 
               $json_array[$key] = $value; 
            $json_array['ean_id'] = $_SESSION['ean_id'];
            $this->queryApi($json_array);
            if(preg_match("/Duplicate entry/", $this->query[0]))
               $error_message[] = "You can't have more then 1 contribution per competition";
            else
               header("Location: ".$this->base_url."Contribute/?contribution_id=".$this->query[0]."");
         }
      }
   }

   if(isset($this->_GET['delete'])) {
      $json_array = array("call" => "contributions",
                          "action" => "geteanfromid",
                          "contribution_id" => $this->_GET['delete']);
      $this->queryApi($json_array);
      if($this->query[0]->ean_id == $_SESSION['ean_id']) {
         $json_array = array("call" => "contributions",
                             "action" => "unregister",
                             "contribution_id" => $this->_GET['delete']);
         $this->queryApi($json_array);
      } else {
         $error_message[] = "Is this why we can't have nice things? Trying to delete other peoples entries... such a waste :(";
      }
   }

   if(isset($this->_GET['contribution_id'])) {
      $json_array = array("call" => "contributions",
                          "action" => "geteanfromid",
                          "contribution_id" => $this->_GET['contribution_id']);
      $this->queryApi($json_array);
      if($this->query[0]->ean_id != $_SESSION['ean_id']) 
         $error_message[] = "Trying to sneak a peek huh? no can do mate";
   }

   if(isset($this->_POST['action'])) {
      unset($this->_POST['action']);
      unset($this->_POST['files']);
      unset($this->_POST['submit']);
      if(!empty($this->_POST['contribution_id']))
         $contribution_id = $this->_POST['contribution_id'];
      elseif(!empty($this->_GET['contribution_id']))
         $contribution_id = $this->_GET['contribution_id'];
      else
         $error_message[] = "Missing contribution id";


      $json_array = array("call" => "contributions",
                          "action" => "geteanfromid",
                          "contribution_id" => $contribution_id);
      $this->queryApi($json_array);
      if($this->query[0]->ean_id == $_SESSION['ean_id']) {
         $json_array = array("call" => "contributions",
                             "action" => "get",
                             "contribution_id" => $this->_POST['contribution_id']);
         $this->queryApi($json_array);
         $this->old_competition_id = $this->query[0]->competition_id;
         if($this->_POST['competition_id'] != $this->old_competition_id) {
            $this->filename = $this->query[0]->filename;
            $this->thumbnail = $this->query[0]->thumbnail_filename;
            $json_array = array("call" => "competitions",
                                "action" => "get");
            $this->queryApi($json_array);
            foreach($this->query as $value) 
               $competitions[$value->competition_id] = $value->name;
            $this->new_competition = $competitions[$this->_POST['competition_id']];
            $this->old_competition = $competitions[$this->old_competition_id];
            $json_array = array("call" => "configurations",
                                "action" => "get",
                                "name" => "upload_path");
            $this->queryApi($json_array);
            $this->upload_path = unserialize($this->query[0]->value);
            $this->old_filename = $this->upload_path.$this->event_name."/".$this->old_competition."/".$this->filename;
            $this->new_filename = $this->upload_path.$this->event_name."/".$this->new_competition."/".$this->filename;
            $this->old_thumbnail = $this->upload_path.$this->event_name."/".$this->old_competition."/".$this->thumbnail;
            $this->new_thumbnail = $this->upload_path.$this->event_name."/".$this->new_competition."/".$this->thumbnail;
            if(!is_dir(dirname($this->new_filename)))
               mkdir(dirname($this->new_filename), 0777, true);
            rename($this->old_filename, $this->new_filename);
            rename($this->old_thumbnail, $this->new_thumbnail);
         }
         $json_array = array("call" => "contributions",
                             "action" => "update");
         foreach($this->_POST as $key => $value) 
            $json_array[$key] = $value;
         $json_array['ean_id'] = $_SESSION['ean_id'];
         $this->queryApi($json_array);
         $this->_POST['contribution_id'] = $this->query[0];
         header("Location: ".$this->base_url."Contribute/?contribution_id=".$this->_POST['contribution_id']);
      } else {
         $error_message[] = "Ohh you bad bastard... trying to modify someone elses stuff huh? it's been logged :P";
      }
   }

   if($_SESSION['voter_only'] == 1) {
      $json_array = array("call" => "contributers",
                          "action" => "geteanfromeanid",
                          "ean_id" => $_SESSION['ean_id']);
      $this->queryApi($json_array);
      $_SESSION['ean_code'] = $this->query[0]->ean_code;
      header("Location: ".$this->base_url."Signup/?ean_code=".$this->query[0]->ean_code."");
   }
   if(empty($_SESSION['loggedin'])) {
      header("Location: ".$this->base_url."Login/?return=Contribute");
   }

   $json_array = array("call" => "contributions",
                       "action" => "get",
                       "ean_id" => $_SESSION['ean_id']);
   $this->queryApi($json_array);
   $contributions = $this->query;

   $json_array = array("call" => "competitions",
                       "action" => "current");
   $this->queryApi($json_array);
   foreach($this->query as $key => $value) 
      $competitions[$value->competition_id] = $value->name;

   if(!empty($this->_GET['contribution_id'])) {
      $json_array = array("call" => "contributions",
                          "action" => "get",
                          "ean_id" => $_SESSION['ean_id'],
                          "contribution_id" => $this->_GET['contribution_id']);
      $this->queryApi($json_array);
      $contrib = $this->query[0];
      $contrib->entry_name = htmlentities($contrib->entry_name);
      $json_array = array("call" => "configurations",
                          "action" => "get",
                          "name" => "upload_path");
      $this->queryApi($json_array);
      $this->upload_path = unserialize($this->query[0]->value).$this->event_name."/".$competitions[$contrib->competition_id]."/";
      $this->thumbnail = $this->base_url.$this->upload_path.$contrib->thumbnail_filename;
      $this->filename_path = $this->base_url.$this->upload_path.$contrib->filename;
      if($contrib->ean_id != $_SESSION['ean_id'])
         unset($contrib);
      $competition_name = $competitions[$contrib->competition_id];
   }
   $json_array = array("call" => "competitions",
                       "action" => "nonsubmitted",
                       "ean_id" => $_SESSION['ean_id']);
   $this->queryApi($json_array);
?>
