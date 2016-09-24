<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="../../css/bootstrap.min.css">
<link rel="stylesheet" href="css/plugin.style.css">
<script src="../jquery.js"></script>
<script src="../bootstrap.min.js"></script>
<script src="js/plugin.upload.min.js"></script>
</head>
<body class="fileuploader">
	<ul class="nav nav-tabs" role="tablists">
		<li class="active"><a>Upload from Computer</a></li>
		<li><a href="./finder.php?type=image">Choose Images from Library</a></li>
		<li><a href="./finder.php?type=file">Choose Files from Library</a></li>
	</ul>
	<div class="tab-content container-fluid">
		<div class="tab-pane col-xs-12 active in" id="upload">
			<div role="uploader">Select Images or Files</div>
			<small class="help-block text-muted"><em>
				<strong>Allowed File Type :</strong> <span id="allowedTypes"></span>&nbsp;
				<strong>Max File Size :</strong> <span id="maxFileSize"></span>
			</em></small>
			<div id="progress"></div>
			<!-- <div id="status"></div> -->
			<div id="detail" style="display:none;"></div>
		</div>
	</div>
<script src="js/serialize.js"></script>
<script src="js/scripts.js"></script>
</body>
</html>