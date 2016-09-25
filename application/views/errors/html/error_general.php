<!DOCTYPE html>
<html lang="en">
<head>
<title>Error</title>
<style type="text/css">

body {
	font: 13px/20px normal Helvetica, Arial, sans-serif;
	padding:20px;
	margin:0;
}

h3 {
	margin: 0 0 10px 0;
	padding: 0;
}

#container {
	border: 1px solid #FAEBCC;
	background-color:#FCF8E3;
	color:#8A6D3B;
	padding:15px;
	margin-bottom:15px;
}

a {
	color:#8A6D3B;
	font-weight:bold;
	pointer-events:none;
}

p {
	margin:0 0 3px 0;
}

p:last-child {
	margin: 0;
}
</style>
</head>
<body>
	<div id="container">
		<h3><?php echo $heading; ?></h3>
		<?php echo $message; ?>
	</div>
</body>
</html>