<?php 

if ( !defined('KW_ERROR_FILE') ) die;

$html = <<<HTML
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<title>There was a problem</title>
<style type="text/css">
body { font-family:sans-serif; color:#933; }
</style>
</head>

<body>
<h2>We're sorry.</h2>
<h4>There was a problem with your request.  Our engineers have been notified.</h4>
<a href="/">click here to return to home page</a>
</body>
</html>
HTML;

echo $html;
 
?>
