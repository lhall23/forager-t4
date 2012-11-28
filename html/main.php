<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
require_once('include/conf.php');
?>

<script src="/javascript/jquery/jquery.js"></script>
<script src="js/jquery.dataTables.min.js"></script>
<head>
	  <meta http-equiv="Content-Type"
 content="text/html; charset=iso-8859-1">
  <title>Group 4</title>
  <link href="css/style.css" rel="stylesheet" type="text/css">
  <link href="css/buttons.css" rel="stylesheet" type="text/css">

  <script type="text/javascript">
//Holds the ID of the scan currently being displayed.
var current_scan;
var current_data;
var current_table;
var current_table_url;
var compare_one;
var compare;
var refresh;

function printpage()
{
window.print();
}

function update_table(url){
        $.getJSON(
            url,
            function(data) {
                if (! data['valid_session']){
                    window.location.href = "login.php";
					return;
                }
				//This autorefresh has been preempted
				if (url != current_table_url){
					return;
				}

                current_table.fnClearTable(false);
				current_table.fnAddData(data['aaData']);
                current_table.fnAdjustColumnSizing();
            }
        );
}

function fetch_table(url){
        $.getJSON(
            url,
            function(data) {
                if (! data['valid_session']){
                    window.location.href = "login.php";
					return;
                }

                current_table=$('#display_table').dataTable({
                    "bDestroy": true,
                    "sPaginationType": "full_numbers",
                    "aoColumns": data['aoColumns'],
                    "aaData": data['aaData']
                });   
                current_table.fnAdjustColumnSizing();
            }
        );
}

function show_home() {
	window.clearInterval(refresh);
    $('#welcome_div').show();
    $('#message_div').hide();
    $('#data_div').hide();	
    $('#controller_div').hide();
}

function refresh_scan(){
	update_table('reports_json.php?scan_id=' + current_scan);
}

function show_control() {
    $('#welcome_div').hide();
    $('#message_div').hide();
    $('#data_div').hide();	
    $('#controller_div').show();
    $.getJSON(
        'control_json.php?a=status',
        function(data) {
            if (! data['valid_session']){
                window.location.href = "login.php";
            }
            if( data['scan_id'] > 0){
                current_scan=data['scan_id'];
                $('#data_div').show();	
                $('#start-button').hide();
                $('#stop-button').show();
                $('#pause-button').show();
                $('#resume-button').hide();
                $('#controller-params').hide(); 
				current_table_url='reports_json.php?scan_id=' + current_scan;
                fetch_table(current_table_url);
				refresh=window.setInterval(refresh_scan, 1000);
            } else {
                compare=3;
                $('#start-button').show();
                $('#stop-button').hide();
                $('#pause-button').hide();
                $('#resume-button').hide();
                $('#controller-params').show(); 
				window.clearInterval(refresh);
            }
        }
    );
}

function show_comparison_list(){
	window.clearInterval(refresh);
    $('#welcome_div').hide();
    $('#data_div').show();
    $('#controller_div').show();
    $('#message_div').hide();
    compare = 3;// 3 = Starting comparison scan
                // 2 = no comparing, 1 = in compare state no links clicked, 
                // 0 = In compare state one link clicked!!
	current_table_url='scans_json.php';
    fetch_table(current_table_url);
}

function show_list(){
	window.clearInterval(refresh);
    $('#welcome_div').hide();
    $('#data_div').show();
    $('#controller_div').hide();
    $('#message_div').hide();
    compare = 2;// 3 = Starting comparison scan
                // 2 = no comparing, 1 = in compare state no links clicked, 
                // 0 = In compare state one link clicked!!
	current_table_url='scans_json.php';
    fetch_table(current_table_url);
}
function compare_list(){
	window.clearInterval(refresh);
    $('#welcome_div').hide();
	$('#data_div').show();
    $('#message_div').show();
    $('#controller_div').hide();

	current_table_url='scans_json.php';
    fetch_table(current_table_url);
	
	$('#message_div').text("Select the first scan to compare");
	compare = 1; // 1 = in compare state, 0 = Not in compare state!!

}

function select_scanId(scan_id)
{
if (compare == 0)
	{	
	current_table_url='compare_json.php?firstId=' + compare_one  + '&secondId=' + scan_id ;
	fetch_table(current_table_url);  
	}

if(compare == 1)
	{
	compare_one = scan_id;
	$('#message_div').text("Select the second scan to compare");
	compare = 0;
	}

if(compare == 2)
	{
	show_scan(scan_id);
	}

    if(compare==3){
        $('#base_scan').val(scan_id);
    }

}

function show_scan(scan_id){
    $('#welcome_div').hide();
    $('#data_div').show();
	current_table_url='reports_json.php?scan_id=' + scan_id;
    fetch_table(current_table_url);
}

function control_scan(action){
    url='control_json.php?a=' + action;
	$('#message_div').text("Select a basis scan.");
    if (action=='start'){
        $('#start-button').hide();
        $('#stop-button').show();
        $('#pause-button').show();
        $('#resume-button').hide();
        $('#controller-params').hide(); 
        $('#data_div').show();
        timeout=$('#timeout').val();
        if(timeout){
            if( (+timeout) == parseInt(timeout)){
                url+='&t=' + timeout;
            } else {
                alert("Non-integer timeouts will be ignored.");
                $('#timeout').val("");
            }
        }
        base_scan=$('#base_scan').val();
        if(base_scan){
            if( (+base_scan) == parseInt(base_scan)){
                url+='&d=' + base_scan;
            } else {
                $('#base_scan').val("");
                alert("Non-integer scan ids will be ignored.");
            }
        }
    } else if (action=='stop'){
        compare=3;
        $('#start-button').show();
        $('#stop-button').hide();
        $('#pause-button').hide();
        $('#resume-button').hide();
        $('#controller-params').show(); 
    } else if (action=='pause'){
        $('#pause-button').hide();
        $('#resume-button').show();
    } else if (action=='resume'){
        $('#pause-button').show();
        $('#resume-button').hide();
    }

    $.getJSON(
        url,
        function(data) {
            if (! data['valid_session']){
                window.location.href = "login.php";
            }
            if (action=='start'){
                current_scan=data['scan_id'];
				current_table_url='reports_json.php?scan_id=' + current_scan;
                fetch_table(current_table_url);
            }
        }
    );
}

  </script>
</head>
<body>
<div id="header"></div>
<div id="nav">
<ul>
  <li><a href="javascript:show_home();">Home</a></li>
  <li><a id="scan_control" href="javascript:show_control()">
          Control a Scan</a></li>
  <li><a href="javascript:show_list()">View Reports</a></li>
  <li><a href="javascript:compare_list()">Compare Reports</a></li>
  <li><a href="javascript:printpage()">Print</a></li>
</ul>
</div>
<div id="message_div" class="site-content" style="display: none">
</div>
<div id="welcome_div" class="site-content" style="display: block">
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
			<h3 class="h-text-3">Forager Version 2.0</h3>
			<p class="text-2">Version 2.0 has been released as of 11/29/2012. Forager is now capable of controling a scan from the website. This includes runtime limits, starting, pausing and stopping scans. You can also run a scan based on a previous scan. Comparing two reports to each other is also functioning.</p>	
			<h4 class="h-text-3">Forager Version 1.0</h4>
			<p class="text-2">Version 1.0 has been released. At the moment, forager is capable of searching the websites and populating a report that lists off the errors encountered.</p>
		</div>
		<div>&nbsp;</div>
		<div style="padding: 5px 10px;">
			<h2 class="h-text-2">Contact Info</h2>
		</div>
		<div style="padding: 5px 10px 15px; background: rgb(216, 214, 215) none repeat scroll 0%; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial;">
			<p class="text-2"> Southern Polytechnic State University.<br>
			<br>
			E.mail: Spsu@Spsu.edu<br>
			<br>
			Fax: 678-915-7778<br>
			<br>
			Phone: 678-915-7778<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			</p>
		</div>
	</div>
</div>
<div id="controller_div" class="site-content" style="display: none">
	<div id="main-controls">
		<a id="resume-button" href="javascript:control_scan('resume');" 
				class="regular">
			<button class="control-button" name="resume">
				<img src="images/bluedash.png" alt=""/>
				Resume
			</button>
		</a>

		<a id="pause-button" href="javascript:control_scan('pause');" 
				class="regular">
			<button class="control-button" name="pause">
				<img src="images/greencheck.jpg" alt=""/>
				Pause
			</button>
		</a>

		<a id="start-button" href="javascript:control_scan('start');" 
				class="regular">
			<button class="control-button" name="pause">
				<img src="images/redx.jpg" alt=""/>
				Start
			</button>
		</a>

		<a id="stop-button" href="javascript:control_scan('stop');" 
				class="negative">
			<button class="control-button" name="pause">
				<img src="images/redx.jpg" alt=""/>
				Stop
			</button>
		</a>
	</div>
    <div id="controller-params" class="site-content">
        <table>
            <tr>
                <td>Timeout (minutes):</td> 
                <td><input type="text" id="timeout"></td>
            </tr>
            <tr>
                <td>Recheck Errors from Scan:</td> 
                <td><input type="text" id="base_scan"></td>
                <td>
                    <a href="javascript:show_comparison_list()">
                        (List Scans)</a>
                </td>
            </tr>
        </table>
    </div>
</div>
<div id="data_div" class="site-content" style="display: none">
  <table cellpadding="0" cellspacing="0" border="0" class="display" 
	  id="display_table" width="100%">
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
