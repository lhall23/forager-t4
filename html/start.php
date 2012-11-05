<?php
/* 
 * start.php
 * -Lee Hall Sat 03 Nov 2012 06:50:57 PM EDT
 */
require_once('include/conf.php');
require_once('include/session.php');
exec("/usr/local/src/forager/bin/crawler.py&");
header("Location: main.php");

?>
