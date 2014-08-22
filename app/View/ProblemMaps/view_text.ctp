<?php
	/* print_r($ProblemMap);*/
	$ProblemMapId = $ProblemMap['ProblemMap'];
	// $localAddress = "C:\\Users\\mdinar\\Documents\\Research\\Creative IT\\Empirical material\\Fall 13\\D1\\Predicates\\";
	// $serverAddress = "domains/problemformulator.com/public_html/app/Predicates/";
	// $myFile = $serverAddress . strval($ProblemMapId['id']) . ".lp";
	// $fh = fopen($myFile, 'w') or die("can't open file");
	
	foreach($ProblemMap['Entities'] as &$e) {
		$stringData = "" . $e['type'] . "(" . $e['name'] . "," . $e['created'] . ").\n";
		echo $stringData; 
		// fwrite($fh, $stringData);
		
		if($e['decomposition_id'] != '')
			$stringData = "child_in(" . $e['id'] . "," . $e['decomposition_id'] . ").\n";
			echo $stringData; 
			// fwrite($fh, $stringData);
	}
	
	// the third version of the tool does not have any attributes filled in
	/*
	$emparr=array(""," ");
	foreach($ProblemMap['Attributes'] as &$a) {
		$emptydesc=$a['description'];
		if ($emptydesc=="") $emptydesc="na";
		$stringData = "" . $a['type'] . "(" . $a['entity_id'] . "," . str_replace(" ","_",$emptydesc) . "," 
			. substr(strtotime($a['created']),-9)  . ").\n";
		echo $stringData; 
		// fwrite($fh, $stringData);
		// fwrite($fh, "\n");
	}
	*/
	
	$linksQuery = "select distinct 
					entities1.type,
					entities1.name as from_entity,
					link.type, 
					entities2.type,
					entities2.name as to_entity, 
					link.created,
					link.problem_map_id
					from Links link left outer join
					  Entities entities1
						on link.from_entity_id = entities1.id left outer join
					  Entities entities2
						on link.to_entity_id = entities2.id
					where link.problem_map_id=4
					order by link.created";
	$linksResult = mysql_query($linksQuery) or die(mysql_error());
		while ($row = mysql_fetch_array($linksResult)) {
		
		echo $row;

		 }
		 
		
	// foreach($ProblemMap['Links'] as &$l) {
		// $stringData = "" . str_replace(" ","_",$l['type']) . "(" . $l['from_entity_id'] . "," . $l['to_entity_id']
			// . "," . substr(strtotime($l['created']),-9) . ").\n";
		// echo $stringData; 
		// // fwrite($fh, $stringData);
	// }
	foreach($ProblemMap['Decompositions'] as &$d) {
		$stringData = "parent_in(" . $d['entity_id'] . "," . $d['id'] . "," . substr(strtotime($d['created']),-9) . ").\n";
		echo $stringData; 
		// fwrite($fh, $stringData);
	}
	foreach($ProblemMap['PartialOrderings'] as &$p) {
		$stringData = "" . $p['type'] . "(" . $p['entity_id'] . "," . $p['other_entity_id'] . 
			 "," . substr(strtotime($p['created']),-9) . ").\n";
		echo $stringData; 
		// fwrite($fh, $stringData);
	}
	
	// fwrite($fh,"\n");
?>