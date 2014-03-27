<?php
   if(!isset($_SESSION['ean_id']))
      die();
   $json_array = array("call" => "votes",
                       "action" => "get",
                       "ean_id" => $_SESSION['ean_id'],
                       "contribution_id" => $this->_GET['contribution_id']);
   $this->queryApi($json_array);
   if(!empty($this->query[0])) {
      $json_array = array("call" => "votes",
                          "action" => "update",
                          "vote_id" => $this->query[0]->vote_id,
                          "result" => $this->_GET['value']);
   } else {
      $json_array = array("call" => "votes",
                          "action" => "register",
                          "ean_id" => $_SESSION['ean_id'],
                          "contribution_id" => $this->_GET['contribution_id'],
                          "result" => $this->_GET['value'],
                        );
   }
   $this->queryApi($json_array);
   print_r($this->query);
   echo preg_replace("/\\\\/", "", $this->api_url);
?>
