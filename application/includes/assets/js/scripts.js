$(document).ready(function(){
// tinymce block
try {
	tinyMCE.baseURL = $('meta[name=assets_path]').attr('content')+'/js/tinymce';
	tinymce.init({
		relative_urls:false,convert_urls:false,remove_script_host:false,
		theme_url:$('meta[name=assets_path]').attr('content')+'/js/tinymce/themes/modern/theme.min.js',
		selector:"[role=tinymce]",menubar:false,statusbar:false,skin:'rahendz',autoresize_min_height:230,autoresize_max_height:550,resize:false,
		plugins:'table hr autoresize wordcount code image link',
		content_css:$('meta[name=stylesheet_url]').attr('content')+'?'+new Date().getTime(),
		toolbar: 'bold italic alignleft aligncenter alignright alignjustify bullist numlist hr blockquote table code image link formatselect'
	});
} catch ( err ) { console.log ( 'tinymce not found.' ); }

// upload media tinymce plugin
try {
	$('[role="tinymce-addmedia"]').on('click',function(){
		var siteUrl = $('meta[name=site_url]').attr('content'),
			baseUrl = siteUrl.replace('index.php',''),
			assetsUrl = $('meta[name=assets_path]').attr('content'),
			templateUrl = $('meta[name=template_directory_uri]').attr('content'),
			pluginPath = assetsUrl + '/js/tinymce-upload/',
			uploadFunc = 'uploads',
			finder = 'finder.php?type=image',
			finderFunc = uploadFunc + '/find',
			form = 'form.php',
			uploadPage = pluginPath + form,
			uploadReturn = true,
			allowedTypes = "jpg,jpeg,png,gif,txt,rtf,pdf,doc,docx,xls,xlsx,ppt,pptx,zip",
			maxFileSize = 20*1024; //KB

		$.ajax({
			url: siteUrl + '/' + finderFunc, async: false,
			success: function ( data ) {
				// console.log(data); return false;
				if ( data.status == false ) uploadReturn = false;
				else uploadPage = pluginPath + finder;
			},
			dataType: 'json'
		});
		// console.log($('body').innerHeight());
		var minHeight = $('body').innerHeight();
		if ( minHeight < 600 ) {
			wTinymce = screen.width;
			hTinymce = screen.height;
			$('body').css('overflow','hidden');
		} else {
			wTinymce = $('body').innerWidth()-50;
			hTinymce = $('body').innerHeight()-90;
		}
		tinymce.activeEditor.windowManager.open({
			title: 'Insert Media', url: uploadPage, width: wTinymce, height: hTinymce
		},{
			baseUrl: baseUrl, siteUrl: siteUrl, pluginPath: pluginPath,
			uploadFunc: uploadFunc, returnData: false,
			buttonRole: $(this).attr('role'), uploadReturn: uploadReturn,
			finderFunc: finderFunc, allowedTypes: allowedTypes, maxFileSize: maxFileSize
		});
	});
} catch ( err ) {
	console.log('tinymce unloaded. '+ err );
}

// select2
try {
	$('[role="select2"],[role="select2"][search="off"]').select2({
		placeholder:$(this).attr('placeholder'),
		minimumResultsForSearch:999
	});
	$('[role="select2"][search="on"]').select2({
		placeholder:$(this).attr('placeholder')
	});
} catch ( err ) { console.log ( 'select2 not loaded.' ); }

// nestable
try {
	$('[role="nestable"]').nestable({
		maxDepth:3,
		expandBtnHTML:'<button type="button" data-action="expand" class="btn btn-link btn-dd"><span class="glyphicon glyphicon-menu-down"></span></button>',
		collapseBtnHTML:'<button type="button" data-action="collapse" class="btn btn-link btn-dd"><span class="glyphicon glyphicon-menu-up"></span</button>'
	}).on('change',function(e){
		var lists = $(this),
			data = window.JSON.stringify(lists.nestable('serialize'));
		$('[role="nestable-data"]').val(data);
	});

	$('[role="nestable-submit"]').on('click',function(e){
		// e.preventDefault();
		// $(this).off('submit');
		var dataMenus = window.JSON.stringify($('[role="nestable"]').nestable('serialize'));
		if ( $('[role="nestable-data"]').val() == "" ) {
			$('[role="nestable-data"]').val(dataMenus);
		}
		// console.log(dataMenus);
		$(this).attr('type','submit');
		$('[role="nestable-form"]').submit();
	});

	$('.lists-trash').on('click',function(e){
		e.preventDefault();

		var li = $(this).parent('li.dd-item'),
			ol = li.parent('ol.dd-list'),
			btn = ol.siblings('button.btn-dd'),
			dataMenus;
		
		if ( ol.find('li.dd-item').length < 2 ) {
			ol.remove();
			btn.remove();
		} else {
			li.remove();
		}

		dataMenus = window.JSON.stringify($('[role="nestable"]').nestable('serialize'));
		$('[role="nestable-data"]').val(dataMenus);
	});
} catch ( err ) { console.log ( 'nestable not callable.' ); }

});