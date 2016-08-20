<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="bootstrap.min.css">
<link rel="stylesheet" href="css/font-awesome.min.css">
<link rel="stylesheet" href="plugin.style.css">
<script src="jquery-2.1.1.min.js"></script>
<script src="bootstrap.min.js"></script>
<script src="plugin.upload.min.js"></script>
</head>
<body class="fileuploader">
	<ul class="nav nav-tabs" role="tablists">
		<li><a href="./files.form.php">Upload from Computer</a></li>
		<li class="active"><a>Choose from Library</a></li>
	</ul>
	<div class="tab-content container-fluid">
		<div class="tab-pane col-xs-12 active in">
			<form id="filesForm" class="form-horizontal files-form" role="form">
				<div class="col-xs-9 row" id="filesSection"></div>
				<div class="col-xs-3 row" style="position:fixed;right:45px;">
					<div class="form-group">
						<input type="text" name="label" class="form-control" placeholder="Label Text" value="Download Now">
					</div>
					<div class="form-group">
						<input type="text" name="alt" class="form-control" placeholder="Alt Text">
					</div>
					<div class="form-group">
						<select name="button_color" class="form-control" placeholder="Button Color">
							<option value="btn-primary">Blue</option>
							<option value="btn-info">Light Blue</option>
							<option value="btn-success">Green</option>
							<option value="btn-warning">Orange</option>
							<option value="btn-danger">Red</option>
							<option value="btn-default">default</option>
						</select>
					</div>
					<div class="form-group">
						<button class="btn btn-primary btn-files-insert" type="button">Insert into Post</button>
					</div>
				</div>
			</form>
		</div>
	</div>
<script>
$.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};
$(document).ready(function(){
	var args = top.tinymce.activeEditor.windowManager.getParams();
	if ( typeof args.button_insert != 'undefined' ) {
		$('.btn-insert,.btn-files-insert').text(args.button_insert);
	}
	post_url = args.media_finder_func + ( args.return_data == true ? '/radio' : '' );
	if ( args.upload_media_return == true ) {
		$.post ( post_url, function ( data ) { $('#filesSection').html( data.files ); }, 'json' );
	}else{
		$('#filesSection').html( '<div class="alert alert-warning text-center">There\'s no uploaded files</div>' );
	}
	$('.btn-files-insert').on('click',function(){
		var files = $(this).parents('form#filesForm').serializeObject(),
			featureFiles = $('[role="'+args.button_role+'"]',window.parent.document),
			featureFilesBox = featureFiles.parent();
		// console.log(files);
		// console.log($.isArray(media.attachment));
		var loop = files.attachment.length, filesTag='';
		// console.log(loop);
		if ( $.isArray ( files.attachment ) ) {
			for ( i=0; i<loop; i++ ) {
				filesTag += '<a href="'+files.attachment[i]+'" class="btn '+files.button_color+
					'" alt="'+files.alt+'">'+files.label+'</a>';
			}
		}else{
			filesTag += '<a href="'+files.attachment+'" class="btn '+files.button_color+
				'" alt="'+files.alt+'">'+files.label+'</a>';
		}
		// console.log(imgTag);
		/*if ( typeof args.return_data != 'undefined' && args.return_data == true ) {
			featureImage.hide();
			featureImageBox.prepend(imgTag);
			featureImageBox.find('[role="removemedia"]').show();
			featureImageBox.find('#featuredImage').removeAttr('disabled').val(media.attachment.replace(args.base_url,''));
		}else{
			top.tinymce.activeEditor.selection.setContent(imgTag);
		}*/
		top.tinymce.activeEditor.selection.setContent(filesTag);
		top.tinymce.activeEditor.windowManager.close();
	});
});
</script>
</body>
</html>