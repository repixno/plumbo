/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{

	config.toolbar = 'Eurofoto';

	config.toolbar_Eurofoto =
	[
		['Source'],
		['Cut','Copy','Paste','PasteText','PasteFromWord','-','Scayt'],
		['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
		['Image','Flash','Table','HorizontalRule','SpecialChar','PageBreak'],
		'/',
		['Styles','Format'],
		['Bold','Italic','Strike'],
		['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
		['Link','Unlink','Anchor'],
		['Maximize','ShowBlocks'],
	];
	config.stylesCombo_stylesSet = 'blueprint:/ckeditor/styles.js';
	config.entities = false;

	config.forcePasteAsPlainText = true;

	// Microsoft Word
	config.autoDetectPasteFromWord = false;
	config.pasteFromWordRemoveStyle = true;
	config.cleanWordKeepsStructure = true;
	
	config.contentsCss = 'http://static.eurofoto.no/css/ckeditor/screen.css?100';
	config.enterMode = 'br';
	config.formatSource = false;
	config.docType = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	config.debug = true ;

	config.filebrowserBrowseUrl = '/ckfinder/ckfinder.html';
	config.filebrowserImageBrowseUrl = '/ckfinder/ckfinder.html?type=Images';
	config.filebrowserFlashBrowseUrl = '/ckfinder/ckfinder.html?type=Flash';
	config.filebrowserUploadUrl = '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files';
	config.filebrowserImageUploadUrl = '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images';
	config.filebrowserFlashUploadUrl = '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash';

};

