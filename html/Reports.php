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
  <title>Your Company</title>
  <link href="css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="container">
<div id="header"> <img src="images/logo.jpg" alt="" id="logo">
<h1 id="logo-text">Reports</h1>
</div>
<div id="nav">
<ul>
  <li><a href="main">Home</a></li>
  <li><a href="scan">Start a Scan</a></li>
  <li><a href="<?php echo "Reports.php"; ?>">View Reports</a></li>
  <li><a href="compare">Compare Reports</a></li>
  <li><a href="extra">Extra</a></li>
  <li style="border-right: medium none;"><a href="#">Links</a></li>
</ul>
</div>
<div id="site-content">
<div id="demo">



<script type="text/javascript">



<?php

if(array_key_exists('scan_id',$_GET))
{
	$scan_id = $_GET['scan_id'];
}
else
{
	die;
}

$query = "SELECT resource_id, url, start_date, response_time, http_response FROM resources WHERE scan_id = $scan_id"; 
$scans = pg_query_params($conn, $query,array($scan_id));

$js_array = "[";

while($results = pg_fetch_array($scans))
{
$js_array .= "[";
$js_array .= $results['resource_id'];
$js_array .= ",";
$js_array .= "\"";
$js_array .= $results['url'];
$js_array .= "\"";
$js_array .= ",";
$js_array .= "\"";
$js_array .= $results['start_date'];
$js_array .= "\"";
$js_array .= ",";
$js_array .= "\"";
$js_array .= $results['response_time'];
$js_array .= "\"";
$js_array .= ",";
$js_array .= $results['http_response'];
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
            { "sTitle": "Resource ID" , "sClass": "center" },
            { "sTitle": "URL" , "sClass": "center" },
            { "sTitle": "Start Date" , "sClass": "center" },
            { "sTitle": "Response Time", "sClass": "center" },
			{ "sTitle": "HTTP Response", "sClass": "center" },
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
<p class="text-2"> 00/00 Lorem Ipsum is simply dummy text of the
printing and typesetting.<br>
<br>
E.mail: abc@Lorem Ipsum<br>
<br>
Fax: 000.000.0000<br>
<br>
Phone: 000.000.0000/<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
000.000.0000 </p>
</div>
</div>
</div>
<div id="footer">
<p>@ Copyright 2010. Designed by <a target="_blank"
 href="http://www.htmltemplates.net/">HTML Templates</a></p>
<ul class="footer-nav">
  <li><a href="#">Home</a></li>
  <li><a href="#">About us</a></li>
  <li><a href="#">Recent articles</a></li>
  <li><a href="#">Email</a></li>
  <li><a href="#">Resources</a></li>
  <li><a href="#">Links</a></li>
</ul>
</div>
</div>
</body>
</html>
