<?php

// Identify if full name exists in he abstract

$link = mysqli_connect("localhost","","","");

if (mysqli_connect_errno($link)) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}	
$query = "SELECT * FROM search_results"
or die("Error in the consult.." . mysqli_error($link));

if(!$result = $link->query($query)) {
	die('There was an error running this first query. [' . $link->error . ']');
}

while ($row = mysqli_fetch_array($result)) {
	$corpwiki = $row['corpwiki'];
	$prof_id = $row['prof_id'];
	
	$result2 = $link->query("SELECT * FROM profs WHERE prof_id = '$prof_id'");
		while ($row2 = mysqli_fetch_array($result2)) {
			$name = $row2['name'];
		}
		
	if (preg_match("~\b" . $name . "\b~",$corpwiki)) {
		$link->query("UPDATE profs SET corpwiki = 1 WHERE prof_id = '$prof_id'");
	}
}