<?php
require_once("include/conf.php");
require_once("include/session.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type"
 content="text/html; charset=iso-8859-1">
  <title>Your Company</title>
  <link href="css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="container">
<div id="header"> <img src="images/logo.jpg" alt="" id="logo">
<h1 id="logo-text">Forager</h1>
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
