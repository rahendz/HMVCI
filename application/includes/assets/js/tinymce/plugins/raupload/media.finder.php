<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="bootstrap.min.css">
<link rel="stylesheet" href="plugin.style.css">
<script src="jquery-2.1.1.min.js"></script>
<script src="bootstrap.min.js"></script>
<script src="plugin.upload.min.js"></script>
</head>
<body class="fileuploader">
	<ul class="nav nav-tabs" role="tablists">
		<li><a href="./media.form.php">Upload from Computer</a></li>
		<li class="active"><a>Choose from Library</a></li>
	</ul>
	<div class="tab-content container-fluid">
		<div class="tab-pane col-xs-12 active in">
			<form id="mediaForm" class="form-horizontal media-form" role="form">
				<div class="col-xs-9 row" id="imagesSection"></div>
				<div class="col-xs-3 row" style="position:fixed;right:45px;">
					<div class="form-group">
						<select name="align" class="form-control">
							<option value="pull-left">Left</option>
							<option value="center-block">Center</option>
							<option value="pull-right">Right</option>
							<option value="pull-none">None</option>
						</select>
					</div>
					<div class="form-group">
						<select name="size" class="form-control" placeholder="Size">
							<option value="small">Small</option>
							<option value="medium">Medium</option>
							<option value="large">Large</option>
							<option value="full">Full Size</option>
						</select>
					</div>
					<div class="form-group">
						<input type="text" name="title" class="form-control" placeholder="Title">
					</div>
					<div class="form-group">
						<input type="text" name="alt" class="form-control" placeholder="Alt Text">
					</div>
					<div class="form-group">
						<button class="btn btn-primary btn-media-insert" type="button">Insert into Post</button>
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
		$('.btn-insert,.btn-media-insert').text(args.button_insert);
	}
	post_url = args.media_finder_func + ( args.return_data == true ? '/radio' : '' );
	if ( args.return_data == true )	$('.form-control').attr('disabled','disabled');
	if ( args.upload_media_return == true ) {
		$.post ( post_url, function ( data ) { $('#imagesSection').html( data.images ); },'json' );
	}else{
		$('#imagesSection').html( '<div class="alert alert-warning text-center">There\'s no uploaded image</div>' );
	}
	$('.btn-media-insert').on('click',function(){
		var media = $(this).parents('form#mediaForm').serializeObject(),
			featureImage = $('[role="'+args.button_role+'"]',window.parent.document),
			featureImageBox = featureImage.parent();
		console.log(media);
		if ( typeof args.return_data != 'undefined' && args.return_data == true ) { attrWidth = ' width="100%"'; }
		else if ( media.size == 'small' ) { attrWidth = ' width="150"'; }
		else if ( media.size == 'medium' ) { attrWidth = ' width="300"'; }
		else if ( media.size == 'large' ) { attrWidth = ' width="800"'; }
		else { attrWidth = ''; }
		// console.log($.isArray(media.attachment));
		var loop = media.attachment.length,imgTag='';
		// console.log(loop);
		if($.isArray(media.attachment)){
			for (i=0;i<loop;i++){
				imgTag += '<img src="'+media.attachment[i]+'"'+attrWidth+' class="'+
					media.align+'" title="'+media.title+'" alt="'+media.alt+'" />';
			}
		}else{
			imgTag += '<img src="'+media.attachment+'"'+attrWidth+' class="'+
				media.align+'" title="'+media.title+'" alt="'+media.alt+'" />';
		}
		// console.log(imgTag);
		if ( typeof args.return_data != 'undefined' && args.return_data == true ) {
			featureImage.hide();
			featureImageBox.prepend(imgTag);
			featureImageBox.find('[role="removemedia"]').show();
			featureImageBox.find('#featuredImage').removeAttr('disabled').val(media.attachment.replace(args.base_url,''));
		}else{
			top.tinymce.activeEditor.selection.setContent(imgTag);
		}
		top.tinymce.activeEditor.windowManager.close();
	});
});
</script>
</body>
</html>