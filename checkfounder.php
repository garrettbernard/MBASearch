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
	$abstract = $row['abstracts'];
	$prof_id = $row['prof_id'];
		
	if (preg_match("~\bfounder\b~",$abstract)) {
		$link->query("UPDATE profs SET founder = 1 WHERE prof_id = '$prof_id'");
	}
}