<!DOCTYPE html>
<html lang="en">
<head>
<title>404 Page Not Found</title>
<style type="text/css">html{font-size:1em;line-height:1.5;height:100%;}body{font:1em/1.5 normal Helvetica, Arial, sans-serif;padding:0;margin:0;height:100%;}h1.error-code{font-size:8em;font-weight:300;line-height:1;margin:0;padding:0;}h3{margin:0;padding:0;}#container-wrapper{color:#555;text-align:center;display:table;width:100%;height:100%;}.container{display:table-cell;vertical-align:middle;}</style>
</head>
<body>
	<div id="container-wrapper"><div class="container"><?php
		$code = substr($heading, 0, 3);

		if (is_numeric($code)) {
			$status = substr($heading, 3);
			echo '<h1 class="error-code">'.$code.'</h1>';
		}

		if (isset($status)) {
			echo '<h3 class="error-status">'.$status.'</h3>';
		} else {
			echo '<h1>'.$heading.'</h1>';
		}
		?>
		<p class="error-message"><?php echo $message; ?></p>
	</div></div>
</body>
</html>