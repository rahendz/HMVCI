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
		<li class="active"><a>Upload from Computer</a></li>
		<li><a href="./media.finder.php">Choose from Library</a></li>
	</ul>
	<div class="tab-content container-fluid">
		<div class="tab-pane col-xs-12 active in" id="upload">
			<div role="uploader">Select Image</div>
			<div id="progress"></div>
			<!-- <div id="status"></div> -->
			<div id="detail" style="display:none;"></div>
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
		button_insert_text = args.button_insert;
	}else{
		button_insert_text = 'Insert into Post';
	}
	$("[role=uploader]").uploadFile({
		url: args.upload_func+'_image',
		allowedTypes:"jpg,png,bmp,gif,tif",
		showFileCounter: false,
		formData: {upload_dir:args.upload_dir},
		onSuccess:function(files,data,xhr){
			console.log(data);
			$('.alert').remove();
			if ( typeof data.error != 'undefined' ) {
				$('#progress').hide();
				$('#detail').append($('<div class="alert alert-danger alert-slim"></div>').html(data.msg)).show();
				setTimeout(function(){$('.progress-bar').remove();},2000);
				return false;
			}
			if ( data.image_width > data.image_height ) { size = 'auto 100%'; }
			else { size = '100% auto'; }
			imgRow = $('<div id="imglists" class="row"></div>').appendTo('#detail');
			$('<div id="img-uploaded" class="pull-left"></div>').css({
				'width':'20',
				'height':'20',
				'background-image':'url("'+data.file_url+'/'+data.file_name+'")',
				'background-size':size
			}).appendTo(imgRow);
			$('<div class="filename pull-left">'+
				( typeof data.orig_name == 'undefined' ? data.file_name : data.orig_name )+
				'</div>').appendTo(imgRow);
			$('<button class="btn btn-link btn-detail pull-right">Detail</button>').appendTo(imgRow);
			formRow = $('<div class="col-xs-12 form-image collapse"></div>').appendTo(imgRow);
			formImg = $('<form id="imgAttr" class="form-horizontal"></div>').appendTo(formRow);
			$('<div class="form-group"><label for="align" class="col-xs-3 control-label">Alignment</label>'+
				'<div class="col-xs-3"><select name="align" class="form-control"><option value="pull-left">Left</option>'+
				'<option value="center-block">Center</option><option value="pull-right">Right</option>'+
				'<option value="pull-none">None</option></select></div></div>').appendTo(formImg);
			$('<div class="form-group"><label for="size" class="col-xs-3 control-label">Size</label>'+
				'<div class="col-xs-3"><select name="size" class="form-control"><option value="small">Small</option>'+
				'<option value="mediumm">Medium</option><option value="large">Large</option>'+
				'<option value="fullsize">Full Size</option></select></div></div>').appendTo(formImg);
			$('<div class="form-group"><label for="title" class="col-xs-3 control-label">Title</label>'+
				'<div class="col-xs-5"><input name="title" placeholder="Title" class="form-control" value="'+
				data.file_title+'" />'+
				'</div></div>').appendTo(formImg);
			$('<div class="form-group"><label for="alt" class="col-xs-3 control-label">Alt Text</label>'+
				'<div class="col-xs-5"><input name="alt" placeholder="Alternate Text" class="form-control" />'+
				'</div></div>').appendTo(formImg);
			$('<div class="form-group"><label for="cap" class="col-xs-3 control-label">Caption</label>'+
				'<div class="col-xs-5"><input name="cap" placeholder="Caption" class="form-control" />'+
				'</div></div>').appendTo(formImg);
			$('<div class="form-group"><label for="ins" class="col-xs-3 control-label">&nbsp;</label>'+
				'<div class="col-xs-6"><button class="btn btn-primary btn-sm btn-insert" path="'+
				data.file_url+'/'+data.file_name+'" type="button">'+button_insert_text+'</button> '+
				'<button type="button" class="btn btn-default btn-sm btn-update" mid="'+data.image_id+'" >Save</button>'+
				'</div></div>').appendTo(formImg);
			setTimeout(function(){$('.progress-bar').remove();},2000);
			$('#detail').show();$('#progress').hide();
			$('.btn-detail').on('click',function(){
				$(this).next('div.form-image').collapse('toggle');
			});
			$('.btn-insert').on('click',function(){
				imgPath = $(this).attr('path');
				imgAttrData = $(this).parents('form#imgAttr').serializeObject();
				if ( typeof args.return_data != 'undefined' && args.return_data == true ) {
					attrWidth = '100%'; attrHeight = '100%';
				}
				else if ( imgAttrData.size == 'small' ) {
					attrWidth = 150;
					attrHeight = Math.round ( data.image_height / ( data.image_width / attrWidth ) );
				}
				else if ( imgAttrData.size == 'medium' ) {
					attrWidth = 300;
					attrHeight = Math.round ( data.image_height / ( data.image_width / attrWidth ) );
				}
				else if ( imgAttrData.size == 'large' ) {
					attrWidth = 800;
					attrHeight = Math.round ( data.image_height / ( data.image_width / attrWidth ) );
				} else {
					attrHeight = data.image_height;
					attrWidth = data.image_width;
				}
				imgTag = '<img src="'+imgPath+'" class="'+imgAttrData.align+
					'" alt="'+imgAttrData.alt+'" width="'+attrWidth+'" height="'+
					attrHeight+'" title="'+imgAttrData.title+'" />';
				if ( typeof args.return_data != 'undefined' && args.return_data == true ) {
					featureImage = $('[role="'+args.button_role+'"]',window.parent.document);
					featureImageBox = featureImage.parent();
					featureImage.hide();
					featureImageBox.prepend(imgTag);
					featureImageBox.find('[role="removemedia"]').show();
					featureImageBox.find('#featuredImage').removeAttr('disabled').val(imgPath.replace(args.base_url,''));
				}else{
					top.tinymce.activeEditor.selection.setContent(imgTag);
				}
				top.tinymce.activeEditor.windowManager.close();
			});
			$('.btn-update').on('click',function(){
				imgId = $(this).attr('mid');
				imgData = $(this).parents('form#imgAttr').serializeObject();
				$.post(args.upload_func+'/'+imgId,{'title':imgData.title,'desc':imgData.cap},function(data){
					not = data.error == true ? 'Not ' : '';
					$('.btn-update').text(not+'Saved');
					});
			});
		}
	});
});
</script>
</body>
</html>