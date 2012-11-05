<?php
/* 
 * start.php
 * -Lee Hall Sat 03 Nov 2012 06:50:57 PM EDT
 */
require_once('include/conf.php');
require_once('include/session.php');

$procs_sql="SELECT pid FROM scans WHERE end_time IS NULL;";
$procs_res=pg_query($procs_sql);
while($procs_row=pg_fetch_assoc($procs_res)){
	/* Send SIGCONT to every crawler process that doesn't have an end-time in
	 * the db. False means there's no process with that id running. This can 
	 * fail weirdly with recycled pids, but as long as we can accurately 
     * record when a process dies, there shouldn't be any user facing issues.
 	 */
	trigger_error("Checking if process $procs_row[pid] is still running.");
	if(posix_kill($procs_row['pid'], 18)){
		trigger_error("Scan already running with pid $procs_row[pid].");
		die("Scan with pid $procs_row[pid] is still running.");
	}	
}
trigger_error("Starting scanning process.");
exec("/usr/local/src/forager/bin/crawler.py >/dev/null 2>&1 &");
die("Started webcrawler.");
header("Location: main.php");
?>
