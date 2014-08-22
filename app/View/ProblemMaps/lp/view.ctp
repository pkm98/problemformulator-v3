<?php
	//print_r($ProblemMap);
	foreach($ProblemMap['Entities'] as &$e){
		echo "" . $e['type'] . "(" . $e['id'] . ").\n";
		if($e['decomposition_id'] != '')
			echo "decomposes(" . $e['decomposition_id'] . "," . $e['id'] . ").\n";
	}
	foreach($ProblemMap['Links'] as &$l){
		echo "" . str_replace(" ","_",$l['type']) . "(" . $l['from_entity_id'] . "," . $l['to_entity_id'] . ").\n";
	}
	foreach($ProblemMap['Decompositions'] as &$d){
		echo "decomposition(" . $d['id'] . "," . $d['entity_id'] . ").\n";
	}

?>