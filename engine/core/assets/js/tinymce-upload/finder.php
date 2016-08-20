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
		<li><a href="./form.php">Upload from Computer</a></li>
		<li<?php echo (isset($_GET['type']) AND $_GET['type']=='image') ? ' class="active"' : NULL ?>>
			<a<?php echo (isset($_GET['type']) AND $_GET['type']=='image') ? NULL : ' href="./finder.php?type=image"' ?>>Choose Images from Library</a>
		</li>
		<li<?php echo (isset($_GET['type']) AND $_GET['type']=='file') ? ' class="active"' : NULL ?>>
			<a<?php echo (isset($_GET['type']) AND $_GET['type']=='file') ? NULL : ' href="./finder.php?type=file"' ?>>Choose Files from Library</a>
		</li>
	</ul>
	<div class="tab-content container-fluid">
		<div class="tab-pane active in">
			<form id="mediaForm" class="form-horizontal media-form" role="form">
				<div class="row">
					<div class="col-xs-8 col-md-9" id="imagesSection"></div>
					<div class="col-xs-4 col-xs-offset-8 col-md-3 col-md-offset-9" id="imagesAttributes">
						<?php if ( isset ( $_GET['type'] ) AND $_GET['type'] == 'image' ) : ?>
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
						<?php else : ?>
						<div class="form-group">
							<input type="text" name="label" class="form-control" placeholder="Label">
						</div>
						<?php endif; ?>
						<div class="form-group">
							<button class="btn btn-primary btn-media-insert" type="button" data-type="<?php echo $_GET['type'] ?>">Insert into Post</button>
						</div>
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
	post_url = args.siteUrl + '/' + args.finderFunc + ( args.returnData == true ? '/radio' : '' ) + '<?php echo isset ( $_GET['type'] ) ? '?type=' . $_GET['type'] : NULL ?>';
	// console.log(post_url);
	if ( args.returnData == true )	$('.form-control').attr('disabled','disabled');
	if ( args.uploadReturn == true ) {
		$.post ( post_url, function ( data ) {
			// console.log(data);
			$('#imagesSection').html( data.images );
		},'json' );
	}else{
		$('#imagesSection').html( '<div class="alert alert-warning text-center">There\'s no uploaded media</div>' );
	}
	$('.btn-media-insert').on('click',function(){
		var media = $(this).parents('form#mediaForm').serializeObject(),
			featureImage = $('[role="'+args.buttonRole+'"]',window.parent.document),
			featureImageBox = featureImage.parent(),
			dataType = $(this).data('type'), 
			loop = media.attachment.length,
			mediaTag='',
			mediaLabel='';
		// console.log(media);
		if(dataType=='image'){
			if ( typeof args.returnData != 'undefined' && args.returnData == true ) { attrWidth = ' width="100%"'; }
			else if ( media.size == 'small' ) { attrWidth = ' width="150"'; }
			else if ( media.size == 'medium' ) { attrWidth = ' width="300"'; }
			else if ( media.size == 'large' ) { attrWidth = ' width="800"'; }
			else { attrWidth = ''; }
		}

		if(media.label!=''){
			mediaLabel = media.label; 
		}
		// console.log($.isArray(media.attachment));
		// console.log(loop);
		if($.isArray(media.attachment)){
			for (i=0;i<loop;i++){
				if(dataType=='image'){
					mediaTag += '<img src="'+media.attachment[i]+'"'+attrWidth+' class="'+
						media.align+'" title="'+media.title+'" alt="'+media.alt+'" />';
				}else{
					mediaTag += '<a href="'+media.attachment[i]+'">'+mediaLabel+'</a>';
				}
			}
		}else{
			if(dataType=='image'){
				mediaTag += '<img src="'+media.attachment+'"'+attrWidth+' class="'+
					media.align+'" title="'+media.title+'" alt="'+media.alt+'" />';
			} else {
				if(mediaLabel==''){
					mediaLabel = media.filename;
				}
				mediaTag += '<a href="'+media.attachment+'">'+mediaLabel+'</a>';
			}
		}
		// console.log(mediaTag);
		if ( typeof args.return_data != 'undefined' && args.return_data == true ) {
			featureImage.hide();
			featureImageBox.prepend(mediaTag);
			featureImageBox.find('[role="removemedia"]').show();
			featureImageBox.find('#featuredImage').removeAttr('disabled').val(media.attachment.replace(args.base_url,''));
		}else{
			top.tinymce.activeEditor.selection.setContent(mediaTag);
		}
		top.tinymce.activeEditor.windowManager.close();
	});
});
</script>
</body>
</html>