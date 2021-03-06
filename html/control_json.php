<?php
/* 
 * start.php
 * -Lee Hall Sat 03 Nov 2012 06:50:57 PM EDT
 */
require_once('include/conf.php');
require_once('include/json_session.php');

define('SIGBREAK', 2);      //SIGINT
define('SIGPAUSE', 10);     //SIGUSR1
define('SIGALIVE', 12);     //SIGUSR2
define('SIGCONT', 18);

if (!array_key_exists('a', $_GET)){
    die("No action specified.");
}


switch ($_GET['a']){
    case 'start':
        $params=array();
        //Set the timeout
        if (array_key_exists('t', $_GET)){
            $timeout=intval($_GET['t']);
            if ($timeout==0){
                trigger_error("Ignoring illegal timeout value");
            } else {
                $params['t']=$timeout * 60;
            }
        }
        //Start a difference scan
        if (array_key_exists('d', $_GET)){
            $scan_id=intval($_GET['d']);
            if ($scan_id==0){
                trigger_error("Ignoring illegal scan_id value");
            } else {
                $params['d']=$scan_id;
            }
        }
        start($params);
        break;
    case 'stop':
        stop();
        break;
    case 'pause':
        pause();
        break;
    case 'resume':
        resume();
        break;
    case 'status':
        break;
    default:
        die("No legal action specified");
        break;
}

echo json_encode(get_status());
die();

function resume(){
    signal_running(SIGCONT);
}

function pause(){
    signal_running(SIGPAUSE);
}

function stop(){
    signal_running(SIGBREAK);
}

function start($params){
    $scan_id=get_running();
    if ($scan_id > 0){
        trigger_error("Scan already running with scan_id $scan_id.");
        return;
    }

    $cmd="/usr/local/src/forager/bin/crawler.py";
    foreach ($params as $key => $value){
        $cmd.=" -$key $value";
    }
    trigger_error("Starting scanning process with $cmd");
    exec($cmd);

    $pass=0;
    while ($scan_id < 0 and $pass < 5) {
        $scan_id=get_running();
        $pass++;
        if ($scan_id < 0){
            trigger_error("No scan running on pass $pass.");
        }
        sleep(1);
    }

    if ($scan_id < 0){
        trigger_error("Failed to start scan.");
    }
}

function signal_running($signal){
    $scan=get_status();
    if ($scan['scan_id'] < 0){
        trigger_error("No scan running.");
        return;
    }
    
    if (!posix_kill($scan['pid'], $signal)){
        trigger_error("Failed sending signal to scanning process");
    }
    return;
}


function get_status(){
    $scan_id=get_running();
    $scan_sql="SELECT scan_id,pid FROM scans WHERE scan_id=$1;";
    $scan_res=pg_query_params($scan_sql, array($scan_id,));

    if (pg_num_rows($scan_res)==0){
       $pid=0;
    } else {
       $scan_row=pg_fetch_array($scan_res);
       $pid=$scan_row['pid'];
    }

    return
        array(
            'valid_session' => True,
            'scan_id'   => $scan_id,
            'pid'       => $pid
        );
}

function get_running(){
    $procs_sql="SELECT scan_id,pid FROM scans WHERE end_time IS NULL;";
    $clear_sql="UPDATE scans SET end_time=NOW() WHERE scan_id=$1";

    $procs_res=pg_query($procs_sql);
    while($procs_row=pg_fetch_assoc($procs_res)){
        trigger_error("Checking if process $procs_row[pid] is still running.");
        if(runningp($procs_row['pid'])){
            $scan_id=$procs_row['scan_id'];
            pg_free_result($procs_res);
            return $scan_id;
        } else {
            //Cleanup unreaped processes.
            $clear_res=pg_query_params($clear_sql, array($procs_row['scan_id']));
            if (pg_affected_rows($clear_res) != 1){
                $msg="Error updating status for scan_id: $procs_row[scan_id].";
                trigger_error($msg);
            }
            pg_free_result($clear_res);
        }
    }
    pg_free_result($procs_res);
    return -1;
}

function runningp($pid){
    /* Send SIGUSR2 to a process.
     * False means there's no process with that id running. This can 
     * fail weirdly with recycled pids, but as long as we can accurately 
     * record when a process dies, there shouldn't be any user facing issues.
     */
    return posix_kill($pid, SIGALIVE);
}

?>
