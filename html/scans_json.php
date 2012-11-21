<?php
require_once("include/conf.php");

$query = "SELECT scan_id, 
    DATE_TRUNC('minutes', start_time) AS start_time,
    DATE_TRUNC('minutes', end_time) AS end_time, 
    DATE_TRUNC('minutes', end_time - start_time) AS elapsed_time 
    FROM scans"; 
$scans = pg_query($conn, $query);

$columns=[
    ["sTitle" => "Scan ID",     "sClass" => "center"],
    ["sTitle" => "Start Time",  "sClass" => "center"],
    ["sTitle" => "End Time",    "sClass" => "center"],
    ["sTitle" => "Run Time",    "sClass" => "center"]
];

$data = array();
while($row = pg_fetch_array($scans)) {
    $data[]=array(
    "<a href='javascript:show_scan($row[scan_id]);'>$row[scan_id]</a>",
    $row['start_time'], $row['end_time'], $row['elapsed_time']);
}
pg_free_result($scans);
echo json_encode(array ("aoColumns" => $columns, "aaData" => $data));
?>

