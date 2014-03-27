<?php
   class results extends common {
      public $requiredRegister   = array();
      public $requiredUnregister = array();
      public $requiredUpdate     = array();
      public $requiredGet        = array("competition_id");
      public $requiredPrint      = array();

      public function __construct($request) {
         $this->_REQUEST = $request;
         return true;
      }

      public function process() {
         $this->call = $this->_REQUEST['call'];
         $this->action = $this->_REQUEST['action'];
         if(!$this->required())
            throw new Exception("Missing required arguments\n");
         unset($this->q_array);
         $this->getRequestForQuery();
         
         switch(true) {
            case preg_match("/^get/", $this->action):
               $out = $this->get();
               break;
            case preg_match("/^calculate/", $this->action):
               $out = $this->calculate();
               break;
            case preg_match("/^print/", $this->action):
               $out = $this->printResult();
               break;
            default:
               throw new Exception("Unknown action\n");
               break;
         }
         return $out;
      }

      public function get($compo_id = NULL) {
         if(!empty($compo_id)) {
            unset($this->q_array);
            $this->q_array[] = "a.competition_id=$compo_id";
         }

         if(count($this->q_array) == 0) {
            $conditions = "event_id=(SELECT z.event_id FROM events as z ORDER BY z.event_id DESC LIMIT 1)";
         } else {
            $conditions = "AND ".implode("\n AND ", $this->q_array);
         }
         $q = "SELECT a.contribution_id,
                      a.competition_id,
                      a.event_id,
                      b.name AS competition_name,
                      c.contributer,
                      c.entry_name AS contribution_name,
                      a.score,
                      a.voters,
                      CONVERT(score/(SELECT SUM(score) FROM view_results WHERE competition_id=a.competition_id)*100, DECIMAL(5,2)) AS procent,
                      CONVERT((SELECT AVG(result) FROM votes WHERE contribution_id=a.contribution_id), DECIMAL(2,1)) AS average
               FROM   view_results AS a,
                      competitions AS b,
                      contributions AS c
               WHERE  a.contribution_id=c.contribution_id
                  AND a.competition_id=b.competition_id
                      $conditions
               ORDER BY competition_id ASC,
                        score DESC";
         $this->data = $this->query($q);
         $x = 1;
         if(isset($this->data['competition_id'])) {
            $tmp = $this->data;
            unset($this->data);
            $this->data[] = $tmp;
         }
         if(is_array($this->data)) {
            foreach($this->data as $key => $value) {
               $value['placement'] = $x++;
               $this->data[$key] = $value;
            }
         }
         return $this->data;
      }

      public function printResult() {
         if(empty($this->_REQUEST['event_id'])) {
            $q = "SELECT event_id 
                  FROM events 
                  ORDER BY event_id DESC LIMIT 1";
            $tmp = $this->query($q);
            $this->_REQUEST['event_id'] = $tmp[0]['event_id'];
         }
         $this->event_id = $this->_REQUEST['event_id'];
         $q = "SELECT event_name 
               FROM events 
               WHERE event_id=$this->event_id";
         $event_name = $this->query($q);
         $q = "SELECT competition_id,
                      name 
               FROM competitions 
               WHERE event_id=$this->event_id";
         $competition_id = $this->query($q);
         foreach($competition_id as $id) { 
            $data[$id['competition_id']] = $this->get($id['competition_id']);
            $data[$id['competition_id']]['competition_name'] = $id['name'];
         }
         
         $top = ".";
         for($i = 1; $i < 80; $i++)
            $top .= "-";
         $top .= ".";
         $bottom = "`";
         for($i = 1; $i < 80; $i++)
            $bottom .= "-";
         $bottom .= "'";
         $title = "Offical results from:";
         $event = str_split($event_name['event_name'], 1);
         $event = implode(" ", $event);
         ob_start();
         echo str_repeat(" ", (80-strlen($title))/2).$title."\n";
         echo "\n";
         echo str_repeat(" ", (80-strlen($event))/2).$event."\n";
         echo "\n\n\n";
         foreach($data as $competition => $entries) {
            $competition_name = $entries['competition_name'];
            array_pop($entries);
            $count = count($entries);
            $title = ".".str_repeat("-", 2)."($competition_name)-[ $count entries ]";
            $top = $title.str_repeat("-", 80-strlen($title)).".\n";
            echo $top; 
            foreach($entries as $entry) {
               if($entry['placement'] < 10)
                  $entry['placement'] = "0".$entry['placement'];
               printf(" %.7s:%9.s  %s / %s\n", $entry['placement'], $entry['procent']."%", $entry['contribution_name'], $entry['contributer']);
            }
            echo "`".str_repeat("-", 79)."'\n\n\n";
         }
         $q = "SELECT count(vote_id) AS count 
               FROM votes 
               WHERE event_id=$this->event_id";
         $tmp = $this->query($q);
         $count = $tmp[0]['count'];
         $q = "SELECT count(vote_id) AS uniq 
               FROM votes 
               WHERE event_id=$this->event_id 
               GROUP BY ean_id";
         $tmp = $this->query($q);
         $unique = $tmp[0]['uniq'];
         $q = "SELECT count(contribution_id) AS entries 
               FROM contributions 
               WHERE event_id=$this->event_id";
         $tmp = $this->query($q);
         $entries = $tmp[0]['entries'];
         echo "We had total of $count votes with $unique unique voters on $entries entries\n";
         $out = ob_get_contents();
         ob_end_clean();
         return $out;
      }

      public function __destruct() {
         unset($this);
         return true;
      }
   }

?>
