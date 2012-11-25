<?php
/* 
 * remaining_json.php
 * -Lee Hall Wed 21 Nov 2012 05:27:46 AM EST
 */

require_once("include/conf.php");
require_once("include/json_session.php");

if(array_key_exists("scan_id", $_GET)){
    $scan_id=$_GET['scan_id'];
} else {
    die("Please specify a scan_id.");
}

$sql="SELECT DISTINCT start_time, 
        COUNT(*) OVER (PARTITION BY scan_id) AS total
    FROM resources JOIN scans USING(scan_id)
    WHERE scan_id=$1";
$res=pg_query_params($conn, $sql, array($scan_id));
$row=pg_fetch_assoc($res);
$row['valid_session']= True;
echo json_encode($row);
?>
