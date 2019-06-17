<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="bootstrap.min.css">
<link rel="stylesheet" href="plugin.style.css">
<link rel="stylesheet" href="css/font-awesome.min.css">
<script src="jquery-2.1.1.min.js"></script>
<script src="bootstrap.min.js"></script>
<script src="plugin.upload.min.js"></script>
</head>
<body class="fileuploader">
	<ul class="nav nav-tabs" role="tablists">
		<li class="active"><a>Upload from Computer</a></li>
		<li><a href="./files.finder.php">Choose from Library</a></li>
	</ul>
	<div class="tab-content container-fluid">
		<div class="tab-pane col-xs-12 active in" id="upload">
			<div role="uploader">Select File</div>
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
		url: args.upload_func+'_files',
		allowedTypes:"zip,rar,7z,ppt,pptx,xls,xlsx,doc,docx,txt,rtf,pdf,pub,pubx",
		showFileCounter: false,
		formData: {upload_dir:args.upload_dir,download_path:args.download_path},
		onSuccess:function(files,data,xhr){
			// console.log(data);
			$('.alert').remove();
			if ( typeof data.error != 'undefined' ) {
				$('#progress').hide();
				$('#detail').append($('<div class="alert alert-danger alert-slim"></div>').html(data.msg)).show();
				setTimeout(function(){$('.progress-bar').remove();},2000);
				return false;
			}
			filesRow = $('<div id="fileslists" class="row"></div>').appendTo('#detail');
			$('<div id="files-uploaded" class="pull-left"><i class="fa fa-file'+data.file_icon+'-o"></i></div>').appendTo(filesRow);
			$('<div class="filename pull-left">'+
				( typeof data.orig_name == 'undefined' ? data.file_name : data.orig_name )+
				'</div>').appendTo(filesRow);
			$('<button class="btn btn-link btn-detail pull-right">Detail</button>').appendTo(filesRow);
			formRow = $('<div class="col-xs-12 form-files collapse"></div>').appendTo(filesRow);
			formImg = $('<form id="filesAttr" class="form-horizontal"></div>').appendTo(formRow);
			$('<div class="form-group"><label for="style" class="col-xs-3 control-label">Style</label>'+
				'<div class="col-xs-3"><select name="style" class="form-control"><option value="primary">Blue</option>'+
				'<option value="info">Light Blue</option><option value="success">Green</option>'+
				'<option value="warning">Yellow</option><option value="danger">Red</option>'+
				'<option value="default">White</option></select></div></div>').appendTo(formImg);
			$('<div class="form-group"><label for="title" class="col-xs-3 control-label">Title</label>'+
				'<div class="col-xs-5"><input name="title" placeholder="Title" class="form-control" value="'+
				data.file_title+'" /></div></div>').appendTo(formImg);
			$('<div class="form-group"><label for="alt" class="col-xs-3 control-label">Alt Text</label>'+
				'<div class="col-xs-5"><input name="alt" placeholder="Alternate Text" class="form-control" />'+
				'</div></div>').appendTo(formImg);
			$('<div class="form-group"><label for="label" class="col-xs-3 control-label">Label</label>'+
				'<div class="col-xs-5"><input name="label" placeholder="Label" class="form-control" value="Download Now" />'+
				'</div></div>').appendTo(formImg);
			$('<div class="form-group"><label for="ins" class="col-xs-3 control-label">&nbsp;</label>'+
				'<div class="col-xs-6"><button class="btn btn-primary btn-sm btn-insert" path="'+
				data.file_get_uri+'/'+data.file_id+'&'+data.file_name.replace(data.file_ext,'')+'" type="button">'+button_insert_text+'</button> '+
				'<button type="button" class="btn btn-default btn-sm btn-update" fid="'+data.file_id+'" >Save</button>'+
				'</div></div>').appendTo(formImg);
			setTimeout(function(){$('.progress-bar').remove();},2000);
			$('#detail').show();$('#progress').hide();
			$('.btn-detail').on('click',function(){
				$(this).next('div.form-files').collapse('toggle');
			});
			$('.btn-insert').on('click',function(){
				filesPath = $(this).attr('path');
				filesAttrData = $(this).parents('form#filesAttr').serializeObject();
				filesTag = '<a href="'+filesPath+'" class="btn btn-'+filesAttrData.style+
					'" alt="'+filesAttrData.alt+'" title="'+filesAttrData.title+'">'+
					filesAttrData.label+'</a>';
				top.tinymce.activeEditor.selection.setContent(filesTag);
				top.tinymce.activeEditor.windowManager.close();
			});
			$('.btn-update').on('click',function(){
				filesId = $(this).attr('fid');
				filesData = $(this).parents('form#filesAttr').serializeObject();
				$.post(args.upload_func+'_files/'+filesId,{'title':filesData.title},function(data){
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