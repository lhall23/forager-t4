<?php
require_once("include/conf.php");
require_once("include/json_session.php");

if(array_key_exists('firstId',$_GET)) {
	$scan_id1 = $_GET['firstId'];
} else {
	die("Please specify a scan_id");
}

if(array_key_exists('secondId',$_GET)) {
	$scan_id2 = $_GET['secondId'];
} else {
	die("Please specify a scan_id");
}

$query = "SELECT url, 
        COALESCE(r1.response_name, resource1.http_response ||
                ' (Unknown Response)')
            AS http_response1,
        COALESCE(r2.response_name, resource2.http_response ||
                ' (Unknown Response)')
            AS http_response2
    FROM (SELECT * FROM resources WHERE scan_id=$1) AS resource1
    FULL JOIN (SELECT * FROM resources WHERE scan_id=$2) AS resource2
        USING(url)
    LEFT JOIN http_responses AS r1 ON(resource1.http_response=r1.http_response)
    LEFT JOIN http_responses AS r2 ON(resource2.http_response=r2.http_response)
    WHERE resource1.http_response != resource2.http_response OR 
        resource1.http_response IS NULL OR resource2.http_response IS NULL;";
		//AND resource1.http_response != resource2.http_response"; 
$reports = pg_query_params($conn, $query,array($scan_id1, $scan_id2));

$columns=array(
    array("sTitle" => "URL",             "sClass" => "table_text"),
    array("sTitle" => "First Report Response",   "sClass" => "table_text"),
	array("sTitle" => "Second Report Response",  "sClass" => "table_text")
);

$data = array();
while($row = pg_fetch_array($reports)) {
    $data[]=array($row['url'], $row['http_response1'], 
        $row['http_response2']);
}
pg_free_result($reports);
echo json_encode(
    array (
        "aoColumns" => $columns, 
        "aaData" => $data,
        "valid_session" => true
    ));
?>

