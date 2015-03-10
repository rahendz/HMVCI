<!DOCTYPE html>
<html lang="en-US">
<head>
<title><?php echo $heading ?></title>
<style type="text/css">

::selection{ background-color: #E13300; color: white; }
::moz-selection{ background-color: #E13300; color: white; }
::webkit-selection{ background-color: #E13300; color: white; }

body {
	background-color: #F5F5F5;
	font: 13px/20px normal Roboto, Helvetica, Verdana, Arial, sans-serif;
	color: #666;
	margin:0;
	padding:40px;
}

a {
	color: #006CBA;
}

h1 {
	color: #006CBA;
	font-size: 19px;
	font-weight: 300;
	margin:0 0 15px 0;
}

code {
	font-family: Consolas, Monaco, Courier New, Courier, monospace;
	font-size: 12px;
	background-color: #f9f9f9;
	border: 1px dashed #DDD;
	color: #002166;
	display: block;
	margin: 14px 0 14px 0;
	padding: 10px;
	cursor:default;
}

#container {
	border: 1px solid #DDD;
	box-shadow:0 0 20px rgba(0,0,0,0.1);
	-webkit-box-shadow:0 0 20px rgba(0,0,0,0.1);
	background-color:#FFF;
	padding:30px;
}

p {
	margin: 0;
}
</style>
</head>
<body>
	<div id="container">
		<h1><?php echo $heading; ?></h1>
		<?php echo $message; ?>
	</div>
</body>
</html>