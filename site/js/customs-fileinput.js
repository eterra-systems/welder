
jQuery(function($) {

	"use strict";


	/**
	* Fileinput
	*/
	$("#form-register-photo").fileinput({
		dropZoneTitle: '<i class="fa fa-photo"></i><span>Upload Photo</span>',
		uploadUrl: '/',
		maxFileCount: 1,
		showUpload: true,
		browseLabel: 'Browse',
		browseIcon: '',
		removeLabel: 'Remove',
		removeIcon: '',
		uploadLabel: 'Upload',
		uploadIcon: '',
		autoReplace: true,
		showCaption: false,
		allowedFileTypes: ['image' ],
		allowedFileExtensions: ['jpg', 'gif', 'png', 'tiff'],
			initialPreview: [
				'<img src="images/man/01.jpg" class="file-preview-image" alt="The Moon" title="The Moon">',
		],
		overwriteInitial: true,
	});
	
	$("#profile_image").fileinput({
		dropZoneTitle: '<i class="fa fa-photo"></i><span>'+text_upload_photo+'</span>',
		uploadUrl: $("#upload_url").val(),
		maxFileCount: 1,
		showUpload: true,
		browseLabel: btn_browse,
		browseIcon: '',
		removeLabel: btn_remove,
		removeIcon: '',
		uploadLabel: btn_upload,
		uploadIcon: '',
		resizeImage: true,
//                maxImageWidth: 100,
//                maxImageHeight: 100,
                resizePreference: 'width',
		autoReplace: true,
		showCaption: false,
		allowedFileTypes: ['image'],
		allowedFileExtensions: ["jpg", "jpeg", "png", "gif"],
                uploadExtraData: {current_lang:$("#current_lang").val()},
                initialPreview: ['<img src="'+$("#profile_image_preview").val()+'" class="file-preview-image">'],
		overwriteInitial: true,
	});
        $('#profile_image').on('fileuploaded', function(event, data, previewId, index) {
            var form = data.form, files = data.files, extra = data.extra,
                response = data.response, reader = data.reader;
            //console.log(response);
            $(".admin-user-item .image img").attr("src",response.image);
        });

	$("#form-photos").fileinput({
		dropZoneTitle: '<i class="fa fa-photo"></i><span>Upload Photos</span>',
		uploadUrl: '/',
		maxFileCount: 5,
		browseLabel: 'Browse',
		browseIcon: '',
		removeLabel: 'Remove',
		removeIcon: '',
		uploadLabel: 'Upload',
		uploadIcon: '',
		autoReplace: false,
		allowedFileTypes: ['image' ],
		allowedFileExtensions: ['jpg', 'gif', 'png', 'tiff'],
		showCaption: false,
	});
	
	$("#input-ficons-3").fileinput({
		uploadUrl: "/",
		previewFileIcon: '<i class="fa fa-file"></i>',
		allowedPreviewTypes: ['image'], // allow only preview of image & text files
		previewFileIconSettings: {
				'docx': '<i class="fa fa-file-word-o text-primary"></i>',
				'xlsx': '<i class="fa fa-file-excel-o text-success"></i>',
				'pptx': '<i class="fa fa-file-powerpoint-o text-danger"></i>',
				'pdf': '<i class="fa fa-file-pdf-o text-danger"></i>',
				'zip': '<i class="fa fa-file-archive-o text-muted"></i>',
		},
		allowedFileExtensions: ['pdf', 'jpg', 'gif', 'png', 'tiff', 'doc', 'docx', 'zip', 'rar' ]
	});

	
	
});




