    <?php  
    require("OAuth.php"); 

$link = mysqli_connect("localhost","","","");

if (mysqli_connect_errno($link)) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

 $i=0;
 while ($i<1) {
	

$query = "SELECT * FROM profs WHERE checked = 0 ORDER BY prof_id ASC LIMIT 1"
or die("Error in the consult.." . mysqli_error($link));

if(!$result = $link->query($query)) {
	die('There was an error running this first query. [' . $link->error . ']');
}

while ($row = mysqli_fetch_array($result)) {
	$sid = $row['schoolid'];
	$name = $row['name'];
	$prof_id = $row['prof_id'];
	$query2 = "SELECT * FROM schools WHERE schoolid = {$sid}";
	$result2 = $link->query($query2);
		while ($row2 = mysqli_fetch_array($result2)) {
			$schoolname = $row2['name'];
		}
	}
	
	$built_query = "'" . $name . "'+'" . $schoolname . "'+('ceo' or 'founder' or 'president' or 'coo')";
			
      
    $cc_key  = "";  
    $cc_secret = "";  
    $url = "http://yboss.yahooapis.com/ysearch/limitedweb";  
    $args = array();  
    $args["q"] = $built_query;  
    $args["format"] = "json";  
	$args["count"] = "3";

    $consumer = new OAuthConsumer($cc_key, $cc_secret);  
    $request = OAuthRequest::from_consumer_and_token($consumer, NULL,"GET", $url, $args);  
    $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, NULL);  
    $url = sprintf("%s?%s", $url, OAuthUtil::build_http_query($args));  
    $ch = curl_init();  
    $headers = array($request->to_header());  
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);  
    curl_setopt($ch, CURLOPT_URL, $url);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);  
    $rsp = curl_exec($ch);  
    $results = json_decode($rsp, true);  
   // print_r($results);  


$urls = "";
$abstracts = "";

foreach($results['bossresponse']['limitedweb']['results'] as $result)
{
    $urls .= $result['url'].'\n';
	$abstracts .= $result['abstract'].'\n';
}

	$abstracts = strip_tags($abstracts);
	$abstracts = htmlentities($abstracts, ENT_QUOTES);
	
	
	$insert_query = ("INSERT INTO search_results (urls, abstracts, prof_id) VALUES ('$urls','$abstracts','$prof_id')")
or die("Error in the consult.." . mysqli_error($link));

if(!$result = $link->query($insert_query)) {
	die('There was an error running this insert query. [' . $link->error . ']');
}

	$link->query("UPDATE profs SET checked = 1 WHERE prof_id = '$prof_id'")
	or die("Error in the consult.." . mysqli_error($link));
	
	$i++;
	print $i . "<br />";
	usleep(400000);

}

    ?>