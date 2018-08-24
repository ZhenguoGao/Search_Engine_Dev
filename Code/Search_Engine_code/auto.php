<?php
	
	if (isset($_GET['term'])){
		
		$num = 10;
	    $q = (in_array('term', array_keys($_GET))) ? $_GET['term'] : '';
	    $q = strtolower($q);
	    
	    if(strlen($q) == 1) {
		    $num = 10;
	    }
        
	    else if(strlen($q) == 2) {
		    $num = 6;
	    }
        
	    else {
		    $num = 3;
	    }
	    //single word
	    if(count(explode(" ", $q)) == 1) {
            
		    $url = 'http://localhost:8983/solr/myexample/suggest?q=' . urlencode($q) . '&wt=json';
            
		    $json = file_get_contents($url);
            
		    $obj=json_decode($json);
		    
		    $rest = array();
            
		    $suggest = $obj->suggest->suggest->$q->suggestions;
            
		    $count = 1;
			foreach($suggest as $tt) {
                
				if($count > $num) {
					break;
				}
				if(stripos($tt->term, '.') === false && strpos($tt->term, ':') === false && strpos($tt->term, '_') === false) {

					array_push($rest, $tt->term);
					$count++;
				}
			}
			echo json_encode($rest);
		}
		//phrase
		else {
			$arr = explode(" ", $q);
            
			$resArr = array();
			
			foreach($arr as $term) {
				$url = 'http://localhost:8983/solr/myexample/suggest?q=' . urlencode($term) . '&wt=json';
			    $json = file_get_contents($url);
			    $obj=json_decode($json);
			    
			    $smallres = array();
                
			    $suggest = $obj->suggest->suggest->$term->suggestions;
                
			    $count = 1;
                
				foreach($suggest as $termobj) {
					if($count > $num) {
						break;
					}
					if(stripos($tt->term, '.') === false && strpos($tt->term, ':') === false && strpos($tt->term, '_') === false) {
						
						array_push($smallres, $termobj->term);
						$count++;
					}
				}
				array_push($resArr, $smallres);
			}
			
			$output = array();
            
			$leastnum = 10;
            
			foreach($resArr as $a) {
                
				if(count($a) < $leastnum) {
					$leastnum = count($a);
				}
                
			}
			for($m=0; $m<$leastnum; $m++) {
				$s = '';
				for($j=0; $j<count($resArr); $j++) {
					$s = $s . ' ' .  $resArr[$j][$m];
				}
				array_push($output, $s);
			}
			echo json_encode($output);
		}
	}
	
?>