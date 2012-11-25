<?php
require_once("include/conf.php");
require_once('include/json_session.php');

$query = "SELECT scan_id, 
    DATE_TRUNC('minutes', start_time) AS start_time,
    DATE_TRUNC('minutes', end_time) AS end_time, 
    DATE_TRUNC('minutes', end_time - start_time) AS elapsed_time 
    FROM scans"; 
$scans = pg_query($conn, $query);

$columns=array(
    array("sTitle" => "Scan ID",     "sClass" => "table_num"),
    array("sTitle" => "Start Time",  "sClass" => "table_time"),
    array("sTitle" => "End Time",    "sClass" => "table_time"),
    array("sTitle" => "Run Time",    "sClass" => "table_time")
);

$data = array();
while($row = pg_fetch_array($scans)) {
    $data[]=array(
    "<a href='javascript:select_scanId($row[scan_id]);'>$row[scan_id]</a>",
    $row['start_time'], $row['end_time'], $row['elapsed_time']);
}
pg_free_result($scans);
echo json_encode(
    array (
        "aoColumns" => $columns,
        "aaData" => $data,
        "valid_session" => True
    ));
?>

