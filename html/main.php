<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
require_once('include/conf.php');
?>

<script src="/javascript/jquery/jquery.js"></script>
<script src="js/jquery.dataTables.js"></script>
<head>
	  <meta http-equiv="Content-Type"
 content="text/html; charset=iso-8859-1">
  <title>Group 4</title>
  <link href="css/style.css" rel="stylesheet" type="text/css">

  <script type="text/javascript">
//Holds the ID of the scan currently being displayed.
var current_scan;
var current_data;

function fetch_table(url){
        $.getJSON(
            url,
            function(data) {
                $('#display_table').dataTable({
                    "bDestroy": true,
                    "sPaginationType": "full_numbers",
                    "aoColumns": data['aoColumns'],
                    "aaData": data['aaData']
                });   
            }
        );
}

function show_home() {
    $('#welcome_div').show();
    $('#data_div').hide();
}

function show_list(){
    $('#welcome_div').hide();
    $('#data_div').show();
    fetch_table('scans_json.php');
}

function show_scan(scan_id){
    $('#welcome_div').hide();
    $('#data_div').show();
    fetch_table('reports_json.php?scan_id=' + scan_id);
}

function start_scan(){
    $('#welcome_div').hide();
    $('#data_div').show();
    $('#progress_div').show();
    $.getJSON(
        'start_json.php',
        function(data) {
            current_scan=data['scan_id'];
            fetch_table('reports_json.php?scan_id=' + current_scan);
        }
    );
}

$(document).ready(function() {
        fetch_table('scans_json.php');
});

  </script>
</head>
<body>
<div id="header"></div>
<div id="nav">
<ul>
  <li><a href="javascript:show_home();">Home</a></li>
  <li><a href="start.php">Start a Scan</a></li>
  <li><a href="javascript:show_list()">View Reports</a></li>
  <li><a href="compare">Compare Reports</a></li>
  <li><a href="extra">Extra</a></li>
</ul>
</div>
<div id="welcome_div" class="site-content" style="display: inline">
<div id="col-left">
<h1 class="h-text-1">WELCOME</h1>
<p class="text-1"><strong>Group 4 is an entity that strives to give its customer the best  software agent technology that is available. Our product is called Forager and it provides you with the following capabilities:</strong></p>
<ul class="list-1">
  <li>Scan any web site</li>
  <li>Generate reports</li>
  <li>Sort reports</li>
  <li>Print reports</li>
  <li>Run timed scans</li>
</ul>
<p class="text-1">Forager is a web crawler that scan, sorts and generates the reports that your company needs to maintain a efficient and secure web site for your customers.</p>
<p class="border-1">&nbsp;</p>
<h2 class="h-text-2">About us</h2>
<p class="text-1">Group 4 is made up of professionals with over 20 years of joint experience in software development and database technologies. Based in Marietta, Georgia, Group 4 as been a staple in the web development community since mid-2012.</p>
<p class="text-1">&nbsp;</p>
</div>
<div id="col-right">
<div style="padding: 30px 10px 10px;">
<h2 class="h-text-2">Latest News</h2>
<h3 class="h-text-3">Forager Version 1.0</h3>
<p class="text-2">Version 1.0 has been released. At the moment, forager is capable of searching the websites and populating a report that lists off the erros encountered.</p>
</div>
<div>&nbsp;</div>
<div style="padding: 5px 10px;">
<h2 class="h-text-2">Contact Info</h2>
</div>
<div
 style="padding: 5px 10px 15px; background: rgb(216, 214, 215) none repeat scroll 0%; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial;">
<p class="text-2"> Southern Polytechnic State University.<br>
<br>
E.mail: Spsu@Spsu.edu<br>
<br>
Fax: 678-915-7778<br>
<br>
Phone: 678-915-7778<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</p></div>
</div>
</div>
<div id="data_div" class="site-content" style="display: none">
  <table cellpadding="0" cellspacing="0" border="0" class="display" 
	  id="display_table">
	<thead></thead>
	<tbody></tbody>
  </table>
  <p class="text-1">&nbsp;</p>
</div>
<div id="footer">
  <p>@ Copyright 2010. Designed by <a target="_blank"
    href="http://www.htmltemplates.net/">HTML Templates</a></p>
  <!--Yes we know this copyright is here. We left it in to show to you that we
  used a template online, and changed it to our needs. This was to make sure
  that you know that we are not trying to pass this off as 100% our work!!-->
  <ul class="footer-nav">
      <li><a href="javascript:show_home();">Home</a></li>
      <li><a href="javascript:show_list()">View Reports</a></li>
      <li><a href="compare">Compare Reports</a></li>
      <li><a href="extra">Extra</a></li>
  </ul>
</div>
</body>
</html>
