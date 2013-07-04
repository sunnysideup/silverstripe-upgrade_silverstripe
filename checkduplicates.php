<?php
	require_once('ReplacementData.php');

	$r = new ReplacementData();
	$arr["3.0"] = $r->getReplacementArrays("3.0");
	$arr["3.1"] = $r->getReplacementArrays("3.1");

	foreach($arr["3.0"]["php"] as $search_30) 
		foreach($arr["3.1"]["php"] as $search_31) 
			//Check whether 3.0's search term equals 3.1's search term.. OR 3.0s replace term equals 3.1s search term to check the transitive relationship
			if($search_30[1] == $search_31[0] || $search_30[0] == $search_31[0]) 
				printf("%s\n", $search_30[0]);
?>