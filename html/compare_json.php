<?php
require_once("include/conf.php");
require_once("include/json_session.php");

if(array_key_exists('compare_one',$_GET)) {
	$scan_id1 = $_GET['compare_one'];
} else {
	die("Please specify a scan_id");
}

if(array_key_exists('scan_id',$_GET)) {
	$scan_id2 = $_GET['scan_id'];
} else {
	die("Please specify a scan_id");
}

$query = "SELECT  url, 
        COALESCE(response_name, resource1.http_response || ' (Unknown Response)') 
            AS http_response1
		COALESCE(response_name, http_response || ' (Unknown Response)') 
            AS http_response2
    FROM resources AS resource1
	OUTER JOIN resources AS resource2
    LEFT JOIN http_responses AS r1 ON(resource1.http_response=r1.http_responce) AS  
    LEFT JOIN http_responses AS r2 ON(resource1.http_response=r2.http_responce) AS  
	WHERE resource1.scan_id = $1 AND resource2.scan_id=$2"; 
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

