<?php
require_once("include/conf.php");

if(array_key_exists('scan_id',$_GET)) {
	$scan_id = $_GET['scan_id'];
} else {
	die("Please specify a scan_id");
}

$query = "SELECT resource_id, url, 
    date_trunc('minutes', start_date) AS start_date, 
        response_time, http_response 
    FROM resources WHERE scan_id = $1"; 
$reports = pg_query_params($conn, $query,array($scan_id));

$columns=array(
    array("sTitle" => "Resource ID",     "sClass" => "table_num"),
    array("sTitle" => "URL",             "sClass" => "table_url"),
    array("sTitle" => "Response Time",   "sClass" => "table_time"),
    array("sTitle" => "HTTP Response",   "sClass" => "table_num")
);

$data = array();
while($row = pg_fetch_array($reports)) {
    $data[]=array($row['resource_id'], $row['url'], 
        $row['response_time'], $row['http_response']);
}
pg_free_result($reports);
echo json_encode(array ("aoColumns" => $columns, "aaData" => $data));
?>

