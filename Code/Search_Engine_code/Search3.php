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
	$query = isset($_REQUEST['qq']) ? $_REQUEST['qq'] : false; $results = false;
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
		$('#qq').autocomplete({
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
			<label for="qq">Search:</label>
			<input id="qq" name="qq" type="text" value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'utf-8'); ?>"/> <input type="submit"/><br>
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
		
		if(isset($_REQUEST['qq']) && $pagerank == "with_Pagerank") {
			echo "SpellCorrect: ";

            ini_set('memory_limit','-1');
            ini_set('max_execution_time', 300);
            $correct = SpellCorrector::correct($query);
            if(strtolower($correct) == strtolower($query)) {
				echo "<br>Show Result with PageRank";
			}
			else {
				echo "<a href='http://localhost:8090/search.php?q=" . $correct . "&way=with_Pagerank'>$correct</a>";
				echo "<br>Show Result with PageRank";
			}
		}
		if(isset($_REQUEST['qq']) && $pagerank == "without_Pagerank") {
			echo "SpellCorrect: ";

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
								$pos = "/Users/Zhenguo/Desktop/CNNData/CNNDownloadData/" . $id;
                                $html= new simple_html_dom();
                                $html->load_file($pos);
								$s = $html->plaintext; 
                
								$start = strpos(strtolower($s), strtolower($query));
								if ($start != false) {
									$snippet = substr($s, $start, 200);
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
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		