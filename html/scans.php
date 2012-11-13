<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
require_once('include/secure.php');
require_once('include/conf.php');
?>

<script src="/javascript/jquery/jquery.js">
</script>
<script src="/js/jquery.dataTables.js">
</script>
<head>
	  <meta http-equiv="Content-Type"
 content="text/html; charset=iso-8859-1">
  <title>Forager</title>
  <link href="css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="container">
<div id="header"> <img src="images/logo.jpg" alt="" id="logo">
<h1 id="logo-text">Reports</h1>
</div>
<div id="nav">
<ul>
  <li><a href="main.php">Home</a></li>
  <li><a href="start.php">Start a Scan</a></li>
  <li><a href="scans.php">View Reports</a></li>
  <li><a href="compare">Compare Reports</a></li>
  <li><a href="extra">Extra</a></li>
  <li style="border-right: medium none;"><a href="#">Links</a></li>
</ul>
</div>
<div id="site-content">
<div id="demo">



<script type="text/javascript">



<?php

$query = "SELECT scan_id, date_trunc('minutes', start_time) AS start_time, date_trunc('minutes', end_time) AS end_time , date_trunc('minutes', end_time - start_time) as elapsed_time FROM scans"; 
$scans = pg_query($conn, $query);

$js_array = "[";

while($results = pg_fetch_array($scans))
{
$js_array .= "[";
$js_array .= "\"<a href='Reports.php?scan_id=$results[scan_id]'> $results[scan_id]</a>\"";
$js_array .= ",";
$js_array .= "\"";
$js_array .= $results['start_time'];
$js_array .= "\"";
$js_array .= ",";
$js_array .= "\"";
$js_array .= $results['end_time'];
$js_array .= "\"";
$js_array .= ",";
$js_array .= "\"";
$js_array .= $results['elapsed_time'];
$js_array .= "\"";
$js_array .= "]";
$js_array .= ",";
}


$js_array .= "]";

?>






$(document).ready(function() {
    $('#demo').html( '<table cellpadding="0" cellspacing="0" border="0" class="display" id="example"></table>' );
    $('#example').dataTable( {
        "aaData": <?php echo $js_array; ?> ,
        "aoColumns": [
            { "sTitle": "Scan ID" , "sClass": "center" },
            { "sTitle": "Start Time" , "sClass": "center" },
            { "sTitle": "End TIme" , "sClass": "center" },
            { "sTitle": "Run Time", "sClass": "center" },
        ]
    } );   
} );


</script>








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
</p>
</div>
</div>
</div>
<div id="footer">
<p>@ Copyright 2010. Designed by <a target="_blank"
 href="http://www.htmltemplates.net/">HTML Templates</a></p>
 <!--Yes we know this copyright is here. We left it in to show to you that we used a
 template online, and changed it to our needs. This was to make sure that you know that 
 we are not trying to pass this off as 100% our work!!-->
<ul class="footer-nav">
  <li><a href="main.php">Home</a></li>
  <li><a href="start.php">Start a Scan</a></li>
  <li><a href="scans.php">View Reports</a></li>
  <li><a href="compare">Compare Reports</a></li>
  <li><a href="extra">Extra</a></li>
</ul>
</div>
</div>
</body>
</html>
