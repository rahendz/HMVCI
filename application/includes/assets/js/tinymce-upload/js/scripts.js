$(document).ready(function(){
	var args = top.tinymce.activeEditor.windowManager.getParams(),
		buttonInsert, uploadDir, allowedTypes, maxFileSize, uploadFunc;

	// Button Text
	if ( typeof args.buttonInsert != 'undefined' ) { buttonInsert = args.buttonInsert; } else { buttonInsert = 'Insert into Post'; }
	// Upload Directory
	if ( typeof args.uploadDir != 'undefined' ) { uploadDir = args.uploadDir; } else { uploadDir = './uploads/'; }
	// allowedTypes
	if ( typeof args.allowedTypes != 'undefined' ) { $('#allowedTypes').text(args.allowedTypes.replace(/\,/g,', ')); allowedTypes = args.allowedTypes; } else { allowedTypes = "jpg,png,bmp,gif,tif,tiff"; }
	// fileSize
	if ( typeof args.maxFileSize != 'undefined' ) { $('#maxFileSize').text(Math.round(args.maxFileSize/1000)+'MB'); maxFileSize = args.maxFileSize*1024; } else { maxFileSize = 2*1024*1024; }
	uploadFunc = args.siteUrl + '/' + args.uploadFunc;

	// Upload Initialization
	$("[role=uploader]").uploadFile({
		url: uploadFunc, showFileCounter: false,multiple:true,
		formData: { upload_dir: uploadDir, allowed_types: allowedTypes, max_file_size: maxFileSize},
		onSuccess: function ( files, data, xhr ) {
			// console.log(data); return false;
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
			if ( data.is_image != false ) {
				$('<div id="img-uploaded" class="pull-left"></div>').css({
					'width':'20',
					'height':'20',
					'background-image':'url("'+(typeof data.file_url == 'undefined' ? data.attachment_url : data.file_url)+'")',
					'background-size':size
				}).appendTo(imgRow);
			}
			$('<div class="filename pull-left">'+
				( typeof data.client_name == 'undefined' ? ( typeof data.file_name == 'undefined' ? data.post_title : data.file_name ) : data.client_name )+
				'</div>').appendTo(imgRow);
			$('<button class="btn btn-link btn-detail pull-right">Detail</button>').appendTo(imgRow);
			formRow = $('<div class="col-xs-12 form-image collapse"></div>').appendTo(imgRow);
			formImg = $('<form id="imgAttr" class="form-horizontal"></div>').appendTo(formRow);

			if(data.is_image!=false){
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
					(typeof data.file_title == 'undefined' ? data.post_title : data.file_title)+'" />'+
					'</div></div>').appendTo(formImg);
				$('<div class="form-group"><label for="alt" class="col-xs-3 control-label">Alt Text</label>'+
					'<div class="col-xs-5"><input name="alt" placeholder="Alternate Text" class="form-control" />'+
					'</div></div>').appendTo(formImg);
				$('<div class="form-group"><label for="cap" class="col-xs-3 control-label">Caption</label>'+
					'<div class="col-xs-5"><input name="cap" placeholder="Caption" class="form-control" />'+
					'</div></div>').appendTo(formImg);
				$('<div class="form-group"><label for="ins" class="col-xs-3 control-label">&nbsp;</label>'+
					'<div class="col-xs-6"><button class="btn btn-primary btn-sm btn-insert" path="'+
					(typeof data.file_url == 'undefined' ? data.attachment_url : data.file_url)+'" type="button">'+buttonInsert+'</button> '+
					'<button type="button" class="btn btn-default btn-sm btn-update" mid="'+data.file_id+'" >Save</button>'+
					'</div></div>').appendTo(formImg);
			} else {
				$('<div class="form-group"><label for="label" class="col-xs-3 control-label">Label</label>'+
					'<div class="col-xs-5"><input name="label" placeholder="Label" class="form-control" />'+
					'</div></div>').appendTo(formImg);
				$('<div class="form-group"><label for="ins" class="col-xs-3 control-label">&nbsp;</label>'+
					'<div class="col-xs-6"><button class="btn btn-primary btn-sm btn-insert" path="'+
					args.siteUrl+'/downloads/'+data.file_name+'" type="button">'+buttonInsert+'</button> '+
					'<button type="button" class="btn btn-default btn-sm btn-update" mid="'+data.file_id+'" >Save</button>'+
					'</div></div>').appendTo(formImg);
			}

			setTimeout(function(){$('.progress-bar').remove();},2000);

			$('#detail').show();
			$('#progress').hide();

			$('.btn-detail').on('click',function(){
				$(this).next('div.form-image').collapse('toggle');
			});

			$('.btn-insert').on('click',function(){
				filePath = $(this).attr('path');
				fileAttrData = $(this).parents('form#imgAttr').serializeObject();
				if(data.is_image!=false){
					attrAlign = ' class="'+fileAttrData.align+'"';
					captionPrefix = '<figure'+attrAlign+'>';
					captionSuffix = '<figcaption>'+fileAttrData.cap+'</figcaption>';
					if ( typeof args.returnData != 'undefined' && args.returnData == true ) {
						attrWidth = '100%'; attrHeight = '100%'; attrAlign = null;
					}
					else if ( fileAttrData.size == 'small' ) {
						attrWidth = 150;
						attrHeight = Math.round ( data.image_height / ( data.image_width / attrWidth ) );
					}
					else if ( fileAttrData.size == 'medium' ) {
						attrWidth = 300;
						attrHeight = Math.round ( data.image_height / ( data.image_width / attrWidth ) );
					}
					else if ( fileAttrData.size == 'large' ) {
						attrWidth = 800;
						attrHeight = Math.round ( data.image_height / ( data.image_width / attrWidth ) );
					} else {
						attrHeight = data.image_height;
						attrWidth = data.image_width;
					}
					if ( fileAttrData.cap !== '' ) {
						attrAlign = null;
					}
					imgTag = '<img src="'+filePath+'"'+attrAlign+
						' alt="'+fileAttrData.alt+'" width="'+attrWidth+'" height="'+
						attrHeight+'" title="'+fileAttrData.title+'" />';
					if ( typeof args.returnData != 'undefined' && args.returnData == true ) {
						featureImage = $('[role="'+args.buttonRole+'"]',window.parent.document);
						featureImageBox = featureImage.parent();
						featureImage.hide();
						featureImageBox.prepend(imgTag);
						featureImageBox.find('[role="removemedia"]').show();
						featureImageBox.find('#featuredImage').removeAttr('disabled').val(filePath.replace(args.baseUrl,''));
					}else{
						if ( fileAttrData.cap !== '' ) {
							imgTag = captionPrefix+imgTag+captionSuffix;
						}
						top.tinymce.activeEditor.selection.setContent(imgTag);
					}
				} else {
					fileLabel = fileAttrData.label;
					fileTag = '<a href="'+filePath+'">'+fileLabel+'</a>';
					top.tinymce.activeEditor.selection.setContent(fileTag);
				}
				top.tinymce.activeEditor.windowManager.close();
			});

			$('.btn-update').on('click',function(){
				imgId = $(this).attr('mid');
				imgData = $(this).parents('form#imgAttr').serializeObject();
				$.post(args.uploadFunc+'/'+imgId,{'title':imgData.title,'desc':imgData.cap},function(data){
					not = data.error == true ? 'Not ' : '';
					$('.btn-update').text(not+'Saved');
					});
			});
		},
		onError: function ( files, status, errMsg ) {
			console.log ( fiels );
			console.log ( status );
			console.log ( errMsg );
			$('#progress').hide();
			$('#detail').append($('<div class="alert alert-warning alert-slim"></div>').html('Something problem with the upload server. Please contact the Administrator.')).show();
			setTimeout(function(){$('.progress-bar').remove();},2000);
			return false;
		}
	});
});
