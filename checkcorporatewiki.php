    <?php  
    require("OAuth.php"); 

$link = mysqli_connect("localhost","","","");

if (mysqli_connect_errno($link)) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

 $i=0;
 while ($i<2) {
	

$query = "SELECT * FROM profs WHERE has_biz_background = 1 AND founder = 1 AND corpwiki = 0 ORDER BY prof_id ASC LIMIT 1"
or die("Error in the consult.." . mysqli_error($link));

if(!$result = $link->query($query)) {
	die('There was an error running this first query. [' . $link->error . ']');
}

$row_cnt = $result->num_rows;
printf("Result set has %d rows.\n", $row_cnt);

while ($row = mysqli_fetch_array($result)) {
	$name = $row['name'];
	$prof_id = $row['prof_id'];
}
	
	$built_query = '"'. $name . '" site:corporationwiki.com';
			
       
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
   # print_r($results);  


$urls = "";
$abstracts = "";

foreach($results['bossresponse']['limitedweb']['results'] as $result)
{
    $urls .= $result['url'].'\n';
	$abstracts .= $result['abstract'].'\n';
}

	$abstracts = strip_tags($abstracts);
	$abstracts = htmlentities($abstracts, ENT_QUOTES);
	
	
	$insert_query = ("UPDATE search_results SET corpwiki = '$abstracts' WHERE prof_id = '$prof_id'")
or die("Error in the consult.." . mysqli_error($link));

if(!$result = $link->query($insert_query)) {
	die('There was an error running this insert query. [' . $link->error . ']');
}

	$link->query("UPDATE profs SET corpwiki = 1 WHERE prof_id = '$prof_id'")
	or die("Error in the consult.." . mysqli_error($link));
	
	$i++;
	print $i . "<br />";
	usleep(400000);
	
}

    ?>