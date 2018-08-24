<?php
error_reporting(E_ERROR | E_PARSE);
function getsnippet($str, $arr) {
    if ($str == "") {
      return "";
    }
    $res = "";
    foreach ($arr as $w) {
      $ind = strpos($str, $w);
      if ($ind < 0) {
        continue;
      }
      if ($ind == 0 || $str[$ind - 1] == " ") {
          $res = substr($str,0, $ind)."<B>".$w."</B>".substr($str, $ind + strlen($w), strlen($str) - $index - strlen($w));
          break;
      }
    }
    return $res;
}
 ?>
<?php
	header('Content-Type: text/html; charset=utf-8');
	include 'SpellCorrector1.php';
	include 'simple_html_dom.php';
		error_reporting(0);
	
	$limit = 10;
	$limit = 10;
	$query = isset($_REQUEST['q']) ? $_REQUEST['q'] : false; $results = false;
	$pagerank = isset($_REQUEST['way']) ? $_REQUEST['way'] : false;
    $correct = isset($_REQUEST['way1']) ? $_REQUEST['way1'] : false;
	
	$file = fopen('/Users/Zhenguo/Desktop/CNNData/mapCNNDataFile.csv', 'r');
	$map = array();
	while($data = fgetcsv($file)) {
		$map[$data[0]] = $data[1];
	}
	

	if ($query)
	{
	
		// The Apache Solr Client library should be on the include path // which is usually most easily accomplished by placing in the
		// same directory as this script ( . or current directory is a default
		// php include path entry in the php.ini)
		require_once('solr-php-client-master/Apache/Solr/Service.php');
		
		// create a new solr service instance - host, port, and corename
		// path (all defaults in this example)
		$solr = new Apache_Solr_Service('localhost', 8983, '/solr/myexample/');
		// if magic quotes is enabled then stripslashes will be needed
		if (get_magic_quotes_gpc() == 1) {
			$query = stripslashes($query); 
		}
		
		// in production code you'll always want to use a try /catch for any // possible exceptions emitted by searching (i.e. connection
		// problems or a query parsing error)
		try
		{
			//additional parameters
			if($pagerank == "with_Pagerank") {	
				$additionalParameters = array(
					'fq' => 'a filtering query',
					'facet' => 'true',
					// notice I use an array for a multi-valued parameter 'facet.field' => array(
					'sort' => 'pageRankFile desc',
				);
				$results = $solr->search($query, 0, $limit, $additionalParameters);	
			}
			else {
				$results = $solr->search($query, 0, $limit);
			}	
		}
		catch (Exception $e) {
			// in production you'd probably log or email this error to an admin
			// and then show a special message to the user but for this example
			// we're going to show the full exception
			die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");
		} 
	
	}
?> 


<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script>
	$(function() {
		$('#q').autocomplete({
			source: 'auto.php',
			minLength: 1
		});
	});
</script>


<html>
	<head>
		<title>PHP Solr Client Example</title>
	</head> 
	<body>
		<form accept-charset="utf-8" method="get">
			<label for="q">Search:</label>
			<input id="q" name="q" type="text" value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'utf-8'); ?>"/> <input type="submit"/><br>
			<input type="radio" name="way" value="without_Pagerank" checked="checked">without Pagerank<br>
			<input type="radio" name="way" value="with_Pagerank">with Pagerank<br>
		</form>
		
		<?php
			// display results
			if ($results) {
			$total = (int) $results->response->numFound; $start = min(1, $total);
			$end = min($limit, $total);
		?>
		
		
		<?php
		
		if(isset($_REQUEST['q']) && $pagerank == "with_Pagerank") {
			echo "SpellCorrect: ";
//			$termarr = explode(" ", $query);
//			$output = "";
//			foreach($termarr as $t) {
//				$output = $output . " " . SpellCorrector::correct($t);
//			}
//			$output = trim($output);
            ini_set('memory_limit','-1');
            ini_set('max_execution_time', 300);
            $correct = SpellCorrector::correct($query);
//			if(strtolower($output) == strtolower($query)) {
//				echo "<br>Show Result with PageRank";
//			}
//            echo $correct;
            if(strtolower($correct) == strtolower($query)) {
				echo "<br>Show Result with PageRank";
			}
			else {
				echo "<a href='http://localhost:8090/search.php?q=" . $correct . "&way=with_Pagerank'>$correct</a>";
				echo "<br>Show Result with PageRank";
			}
		}
		if(isset($_REQUEST['q']) && $pagerank == "without_Pagerank") {
			echo "SpellCorrect: ";
//			$termarr = explode(" ", $query);
//			$output = "";
//			foreach($termarr as $t) {
//				$output = $output . " " . SpellCorrector::correct($t);
//			}
//			$output = trim($output);
            ini_set('memory_limit','-1');
            ini_set('max_execution_time', 300);
            $correct = SpellCorrector::correct($query);
			if(strtolower($correct) == strtolower($query)) {
				echo "<br>Show Result without PageRank";
			}
			else {
				echo "<a href='http://localhost:8090/search.php?q=" . $correct . "&way=without_Pagerank'>$correct</a>";
				echo "<br>Show Result without PageRank";
			}
		}
		?>
		
		<div>Results <?php echo $start; ?> - <?php echo $end;?> of <?php echo $total; ?>:</div> 
		<ol>
		
		<?php
			// iterate result documents
			foreach ($results->response->docs as $doc)
			{ 	
		?>
		
			<li>
				<table style="border: 1px solid black; text-align: left">
					
		<?php
			foreach ($doc as $field => $value)
			{ 

				if(htmlspecialchars($field, ENT_NOQUOTES, 'utf-8') != "id") {
                    
					continue;
				}
		?>
					<tr>
						<td><?php 
							if(htmlspecialchars($field, ENT_NOQUOTES, 'utf-8') == "id") {
                                $str = "/Users/Zhenguo/Desktop/CNNData/CNNDownloadData/";
                                $len = strlen($str);
                                $id = substr($value,$len);
							    echo "<a href='" . $map[$id] . "'>" . $map[$id] . "</a>";

							}

							?></td>
					</tr>
                    
                    
					
		<?php
			} 
		?>
                <tr>
						<td><?php
//								echo "Snippet: ";
								$pos = "/Users/Zhenguo/Desktop/CNNData/CNNDownloadData/" . $id;
//                                $arr = explode(" ", trim($query));
//                                $html = strtolower(file_get_contents("/Users/Zhenguo/Desktop/CNNData/CNNDownloadData/" . $id));
//                                $doc = new DOMDocument();
//                                $doc->loadHTML($html);
//                                $items3 = $doc->getElementsByTagName('p');
//                                $items1 = $doc->getElementsByTagName('head');
//                                // $items2 = $doc->getElementsByTagName('body');
//                                $isfound = false;
//                                $res = "";
//                                for ($i = 0; $i < $items3->length; $i++) {
//                                        $var = $items3->item($i)->nodeValue;
//                                        foreach($arr as $q) {
//                                          if (strpos($var, $q) > -1) {
//                                              $res = $var;
//                                              $isfound = true;
//                                              break;
//                                          }
//                                      }
//                                      if ($isfound) {
//                                        break;
//                                      }
//                                }
////                                echo "<tr>";
//                                echo "<I>".getsnippet($res, $arr)."</I>";
//                                echo "</tr>";
//                                echo "<b>".$pos."</b>";
                                $html= new simple_html_dom();
                                $html->load_file($pos);
//								$html = file_get_html($pos);
								$s = $html->plaintext; 
//                                echo "<b>".$s."</b>";
                
								$start = strpos(strtolower($s), strtolower($query));
								if ($start != false) {
									$snippet = substr($s, $start, 200);
//                                    echo "<b>".$snippet."</b>";
									echo str_ireplace($query, "<b>" . $query . "</b>", $snippet);
								}
						?></td>
					</tr>
				</table> 
			</li>
		
		<?php 
			}
		?>
		
		</ol>
		
		<?php 
			}
		?>
		
	</body> 
</html>
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		