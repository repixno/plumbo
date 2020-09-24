// Aurigma Image Uploader Dual 6.x - IUEmbed Script Library
// Copyright(c) Aurigma Inc. 2002-2009
// Version 6.1.4.0

function _initIUIntellisenseWarning() {
	function showWarning() {
		var w = document.createElement("div");
		w.innerHTML = "You have linked <strong>iuembed.Intellisense.js</strong> script to the page. This script is required for development/debug purposes only and you should remove reference to it on production Web-site. <a href='#' onclick='this.parentNode.style.display=\"none\";return false;'>[Close]</a>";
		w.style.backgroundColor = "#fdf0b3";
		w.style.color = "#000";
		w.style.fontFamily = "arial";
		w.style.fontSize = "9pt";
		w.style.border = "solid 1px #f0b384";
		w.style.padding = "5px";
		w.style.marginBottom = "5px";

		var b = document.body;
		if (b.childNodes.length > 0) {
			b.insertBefore(w, b.childNodes[0]);
		}
		else {
			b.appendChild(w);
		}
	}

	if (window.attachEvent) {
		window.attachEvent("onload", showWarning);
	}
	else {
		var r = window.addEventListener ? window : (document.addEventListener ? document : null);
		if (r) {
			r.addEventListener("load", showWarning, false);
		}
	}
}


_initIUIntellisenseWarning()


function ImageUploader() {
	/// <summary>
	///   Main Image Uploader control/applet.
	///   Don't create the instance of this class explicitly. You should use ImageUploaderWriter class instead.
	/// </summary>
}

ImageUploader.prototype = {
	
	
	getAction : function() {
		/// <summary>
		///   URL of the page to which files selected for upload in Image Uploader are sent to.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setAction : function(value) {
		/// <summary>
		///   URL of the page to which files selected for upload in Image Uploader are sent to.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getAddFolderDialogButtonCancelText : function() {
		/// <summary>
		///   Caption of the Cancel button on the dialog displayed when the folders are being added to the upload list.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setAddFolderDialogButtonCancelText : function(value) {
		/// <summary>
		///   Caption of the Cancel button on the dialog displayed when the folders are being added to the upload list.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getAddFolderDialogButtonSkipAllText : function() {
		/// <summary>
		///   Caption of the Skip All button on the dialog displayed when the folders are being added to the upload list.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setAddFolderDialogButtonSkipAllText : function(value) {
		/// <summary>
		///   Caption of the Skip All button on the dialog displayed when the folders are being added to the upload list.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getAddFolderDialogButtonSkipText : function() {
		/// <summary>
		///   Caption of the Skip button on the dialog displayed when folders are being added to the upload list.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setAddFolderDialogButtonSkipText : function(value) {
		/// <summary>
		///   Caption of the Skip button on the dialog displayed when folders are being added to the upload list.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getAddFolderDialogTitleText : function() {
		/// <summary>
		///   Title of the dialog displayed when folders are being uploaded to the upload list.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setAddFolderDialogTitleText : function(value) {
		/// <summary>
		///   Title of the dialog displayed when folders are being uploaded to the upload list.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getAdditionalFormName : function() {
		/// <summary>
		///   Name of the HTML form with additional information which should be sent to the server along with files.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setAdditionalFormName : function(value) {
		/// <summary>
		///   Name of the HTML form with additional information which should be sent to the server along with files.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getAdvancedDetailsFailedItemBackgroundColor : function() {
		/// <summary>
		///   Background color of an item in AdvancedDetails view which indicates that this item failed to be uploaded.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setAdvancedDetailsFailedItemBackgroundColor : function(value) {
		/// <summary>
		///   Background color of an item in AdvancedDetails view which indicates that this item failed to be uploaded.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getAdvancedDetailsGridLineColor : function() {
		/// <summary>
		///   Color of grid lines in AdvancedDetails view when the grid is enabled.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setAdvancedDetailsGridLineColor : function(value) {
		/// <summary>
		///   Color of grid lines in AdvancedDetails view when the grid is enabled.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getAdvancedDetailsGridLineStyle : function() {
		/// <summary>
		///   Style of the grid in AdvancedDetails view.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setAdvancedDetailsGridLineStyle : function(value) {
		/// <summary>
		///   Style of the grid in AdvancedDetails view.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (Invisible - 0, Solid - 1, Dash - 2, Dot - 3, DashDot - 4, or DashDotDot - 5).
		/// </param>
	}
	,
	
	
	getAdvancedDetailsIdleItemBackgroundColor : function() {
		/// <summary>
		///   Background color of an item in AdvancedDetails view which indicates items which are pending for upload.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setAdvancedDetailsIdleItemBackgroundColor : function(value) {
		/// <summary>
		///   Background color of an item in AdvancedDetails view which indicates items which are pending for upload.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getAdvancedDetailsInProcessItemBackgroundColor : function() {
		/// <summary>
		///   Background color of an item in AdvancedDetails view which indicates items which are being uploaded.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setAdvancedDetailsInProcessItemBackgroundColor : function(value) {
		/// <summary>
		///   Background color of an item in AdvancedDetails view which indicates items which are being uploaded.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getAdvancedDetailsPreviewThumbnailSize : function() {
		/// <summary>
		///   Size of preview thumbnail in AdvancedDetails view mode.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setAdvancedDetailsPreviewThumbnailSize : function(value) {
		/// <summary>
		///   Size of preview thumbnail in AdvancedDetails view mode.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getAllowAutoRotate : function() {
		/// <summary>
		///   Switch that enables automatic EXIF-based photo rotation feature.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setAllowAutoRotate : function(value) {
		/// <summary>
		///   Switch that enables automatic EXIF-based photo rotation feature.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getAllowCmykImages : function() {
		/// <summary>
		///   Switch that specifies whether it is possible to select CMYK images for the upload.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setAllowCmykImages : function(value) {
		/// <summary>
		///   Switch that specifies whether it is possible to select CMYK images for the upload.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getAllowDisproportionalExifThumbnails : function() {
		/// <summary>
		///   Switch that specifies whether Image Uploader should use an EXIF thumbnail in the thumbnail list even through it is out of proportion to the original image.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setAllowDisproportionalExifThumbnails : function(value) {
		/// <summary>
		///   Switch that specifies whether Image Uploader should use an EXIF thumbnail in the thumbnail list even through it is out of proportion to the original image.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getAllowFolderUpload : function() {
		/// <summary>
		///   Switch that specifies whether it is possible to upload not only separate files, but also entire folders.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setAllowFolderUpload : function(value) {
		/// <summary>
		///   Switch that specifies whether it is possible to upload not only separate files, but also entire folders.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getAllowLargePreview : function() {
		/// <summary>
		///   Switch that enables large preview of an image in a separate window.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setAllowLargePreview : function(value) {
		/// <summary>
		///   Switch that enables large preview of an image in a separate window.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getAllowMultipleRemove : function() {
		/// <summary>
		///   Switch that specifies whether clicking on remove icon of a list item will remove all selected items.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setAllowMultipleRemove : function(value) {
		/// <summary>
		///   Switch that specifies whether clicking on remove icon of a list item will remove all selected items.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getAllowMultipleRotate : function() {
		/// <summary>
		///   Switch that specifies whether clicking on rotate icon of a list item will rotate all selected items.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setAllowMultipleRotate : function(value) {
		/// <summary>
		///   Switch that specifies whether clicking on rotate icon of a list item will rotate all selected items.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getAllowMultipleSelection : function() {
		/// <summary>
		///   Switch that specifies whether the user is allowed to select multiple files at one time.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setAllowMultipleSelection : function(value) {
		/// <summary>
		///   Switch that specifies whether the user is allowed to select multiple files at one time.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getAllowRotate : function() {
		/// <summary>
		///   Switch that specifies whether to display rotate icons on preview thumbnails.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setAllowRotate : function(value) {
		/// <summary>
		///   Switch that specifies whether to display rotate icons on preview thumbnails.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getAllowTreePaneWidthChange : function() {
		/// <summary>
		///   Switch that specifies whether the user can resize the tree pane.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setAllowTreePaneWidthChange : function(value) {
		/// <summary>
		///   Switch that specifies whether the user can resize the tree pane.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getAuthenticationRequestBasicText : function() {
		/// <summary>
		///   Text of the message on the authentication dialog when basic authentication is used.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setAuthenticationRequestBasicText : function(value) {
		/// <summary>
		///   Text of the message on the authentication dialog when basic authentication is used.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getAuthenticationRequestButtonCancelText : function() {
		/// <summary>
		///   Text of the Cancel button on the authentication dialog.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setAuthenticationRequestButtonCancelText : function(value) {
		/// <summary>
		///   Text of the Cancel button on the authentication dialog.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getAuthenticationRequestButtonOkText : function() {
		/// <summary>
		///   Text of the OK button on the authentication dialog.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setAuthenticationRequestButtonOkText : function(value) {
		/// <summary>
		///   Text of the OK button on the authentication dialog.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getAuthenticationRequestDomainText : function() {
		/// <summary>
		///   Text of the Domain label on the authentication dialog.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setAuthenticationRequestDomainText : function(value) {
		/// <summary>
		///   Text of the Domain label on the authentication dialog.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getAuthenticationRequestLoginText : function() {
		/// <summary>
		///   Text of the Login label on the authentication dialog.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setAuthenticationRequestLoginText : function(value) {
		/// <summary>
		///   Text of the Login label on the authentication dialog.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getAuthenticationRequestNtlmText : function() {
		/// <summary>
		///   Text of the message on the authentication dialog when NTLM authentication is used.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setAuthenticationRequestNtlmText : function(value) {
		/// <summary>
		///   Text of the message on the authentication dialog when NTLM authentication is used.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getAuthenticationRequestPasswordText : function() {
		/// <summary>
		///   Text of the Password label on the authentication dialog.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setAuthenticationRequestPasswordText : function(value) {
		/// <summary>
		///   Text of the Password label on the authentication dialog.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getAuthenticationType : function() {
		/// <summary>
		///   Authentication type configured on the server.
		/// </summary>
		/// <returns type="AuthenticationType"></returns>
	}
	,
	
	setAuthenticationType : function(value) {
		/// <summary>
		///   Authentication type configured on the server.
		/// </summary>
		/// <param name="value" type="AuthenticationType">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getAutoRecoverMaxTriesCount : function() {
		/// <summary>
		///   Number of tries that should be performed to submit files.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setAutoRecoverMaxTriesCount : function(value) {
		/// <summary>
		///   Number of tries that should be performed to submit files.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getAutoRecoverTimeOut : function() {
		/// <summary>
		///   Interval in which Image Uploader should try to resume the upload if it was interrupted.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setAutoRecoverTimeOut : function(value) {
		/// <summary>
		///   Interval in which Image Uploader should try to resume the upload if it was interrupted.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getBackgroundColor : function() {
		/// <summary>
		///   Background color of the Image Uploader surface.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setBackgroundColor : function(value) {
		/// <summary>
		///   Background color of the Image Uploader surface.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getButtonAddAllToUploadListImageFormat : function() {
		/// <summary>
		///   List of images that specify different states of the Add All button.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setButtonAddAllToUploadListImageFormat : function(value) {
		/// <summary>
		///   List of images that specify different states of the Add All button.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getButtonAddAllToUploadListText : function() {
		/// <summary>
		///   Text of the button which adds all files in the current folder to upload list.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setButtonAddAllToUploadListText : function(value) {
		/// <summary>
		///   Text of the button which adds all files in the current folder to upload list.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getButtonAddFilesImageFormat : function() {
		/// <summary>
		///   List of images that specify different states of the Add Files button.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setButtonAddFilesImageFormat : function(value) {
		/// <summary>
		///   List of images that specify different states of the Add Files button.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getButtonAddFilesText : function() {
		/// <summary>
		///   Text of the button which displays the Open File dialog to select the files for the upload.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setButtonAddFilesText : function(value) {
		/// <summary>
		///   Text of the button which displays the Open File dialog to select the files for the upload.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getButtonAddFoldersImageFormat : function() {
		/// <summary>
		///   List of images that specify different states of the Add Folders button.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setButtonAddFoldersImageFormat : function(value) {
		/// <summary>
		///   List of images that specify different states of the Add Folders button.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getButtonAddFoldersText : function() {
		/// <summary>
		///   Text of the button which opens the standard Open Folder dialog to add the entire folder content to the upload list.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setButtonAddFoldersText : function(value) {
		/// <summary>
		///   Text of the button which opens the standard Open Folder dialog to add the entire folder content to the upload list.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getButtonAddToUploadListImageFormat : function() {
		/// <summary>
		///   List of images that specify different states of the Add button (which adds selected files from folder pane to upload pane).
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setButtonAddToUploadListImageFormat : function(value) {
		/// <summary>
		///   List of images that specify different states of the Add button (which adds selected files from folder pane to upload pane).
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getButtonAddToUploadListText : function() {
		/// <summary>
		///   Text of the button which adds selected files to the upload list.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setButtonAddToUploadListText : function(value) {
		/// <summary>
		///   Text of the button which adds selected files to the upload list.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getButtonAdvancedDetailsCancelImageFormat : function() {
		/// <summary>
		///   List of images that specify different states of the Cancel button which is displayed in the AdvancedDetails view during upload.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setButtonAdvancedDetailsCancelImageFormat : function(value) {
		/// <summary>
		///   List of images that specify different states of the Cancel button which is displayed in the AdvancedDetails view during upload.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getButtonAdvancedDetailsCancelText : function() {
		/// <summary>
		///   Text of the Cancel button which is displayed in the AdvancedDetails view during upload.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setButtonAdvancedDetailsCancelText : function(value) {
		/// <summary>
		///   Text of the Cancel button which is displayed in the AdvancedDetails view during upload.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getButtonDeleteFilesImageFormat : function() {
		/// <summary>
		///   List of images that specify different states of the button which deletes selected files from the folder pane.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setButtonDeleteFilesImageFormat : function(value) {
		/// <summary>
		///   List of images that specify different states of the button which deletes selected files from the folder pane.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getButtonDeleteFilesText : function() {
		/// <summary>
		///   Text of the Delete Files button, which deletes selected files from the folder pane.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setButtonDeleteFilesText : function(value) {
		/// <summary>
		///   Text of the Delete Files button, which deletes selected files from the folder pane.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getButtonDeselectAllImageFormat : function() {
		/// <summary>
		///   List of images that specify different states of the button which unchecks all files in the folder pane in TwoPanes layout and deselects them in ThreePanes.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setButtonDeselectAllImageFormat : function(value) {
		/// <summary>
		///   List of images that specify different states of the button which unchecks all files in the folder pane in TwoPanes layout and deselects them in ThreePanes.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getButtonDeselectAllText : function() {
		/// <summary>
		///   Text of the button which unchecks all files in the folder pane in TwoPanes layout and deselects them in ThreePanes.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setButtonDeselectAllText : function(value) {
		/// <summary>
		///   Text of the button which unchecks all files in the folder pane in TwoPanes layout and deselects them in ThreePanes.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getButtonFont : function() {
		/// <summary>
		///   Font format for all Image Uploader buttons.
		/// </summary>
		/// <returns type="FontInfo"></returns>
	}
	,
	
	setButtonFont : function(value) {
		/// <summary>
		///   Font format for all Image Uploader buttons.
		/// </summary>
		/// <param name="value" type="FontInfo">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getButtonPasteImageFormat : function() {
		/// <summary>
		///   List of images that specify different states of the Paste button.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setButtonPasteImageFormat : function(value) {
		/// <summary>
		///   List of images that specify different states of the Paste button.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getButtonPasteText : function() {
		/// <summary>
		///   Text for the Paste button.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setButtonPasteText : function(value) {
		/// <summary>
		///   Text for the Paste button.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getButtonRemoveAllFromUploadListImageFormat : function() {
		/// <summary>
		///   List of images that specify different states of the Remove All button which removes all files from the upload list.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setButtonRemoveAllFromUploadListImageFormat : function(value) {
		/// <summary>
		///   List of images that specify different states of the Remove All button which removes all files from the upload list.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getButtonRemoveAllFromUploadListText : function() {
		/// <summary>
		///   Text of the button which removes all files from the upload list.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setButtonRemoveAllFromUploadListText : function(value) {
		/// <summary>
		///   Text of the button which removes all files from the upload list.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getButtonRemoveFromUploadListImageFormat : function() {
		/// <summary>
		///   List of images that specify different states of the Remove button which removes selected files from the upload list.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setButtonRemoveFromUploadListImageFormat : function(value) {
		/// <summary>
		///   List of images that specify different states of the Remove button which removes selected files from the upload list.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getButtonRemoveFromUploadListText : function() {
		/// <summary>
		///   Text of the button which removes selected files from the upload list.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setButtonRemoveFromUploadListText : function(value) {
		/// <summary>
		///   Text of the button which removes selected files from the upload list.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getButtonSelectAllImageFormat : function() {
		/// <summary>
		///   List of images that specify different states of the button which checks all files in the folder pane in TwoPanes layout and selects them in ThreePanes.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setButtonSelectAllImageFormat : function(value) {
		/// <summary>
		///   List of images that specify different states of the button which checks all files in the folder pane in TwoPanes layout and selects them in ThreePanes.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getButtonSelectAllText : function() {
		/// <summary>
		///   Text of the button which checks all files in the folder pane in TwoPanes layout and selects them in ThreePanes.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setButtonSelectAllText : function(value) {
		/// <summary>
		///   Text of the button which checks all files in the folder pane in TwoPanes layout and selects them in ThreePanes.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getButtonSendImageFormat : function() {
		/// <summary>
		///   List of images that specify different states of the Send button.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setButtonSendImageFormat : function(value) {
		/// <summary>
		///   List of images that specify different states of the Send button.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getButtonSendText : function() {
		/// <summary>
		///   Text of the button which starts the upload of selected files.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setButtonSendText : function(value) {
		/// <summary>
		///   Text of the button which starts the upload of selected files.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getButtonStopImageFormat : function() {
		/// <summary>
		///   List of images that specify different states of the Stop button.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setButtonStopImageFormat : function(value) {
		/// <summary>
		///   List of images that specify different states of the Stop button.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getButtonStopText : function() {
		/// <summary>
		///   Text of the button which aborts the upload.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setButtonStopText : function(value) {
		/// <summary>
		///   Text of the button which aborts the upload.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getCacheGuiGraphics : function() {
		/// <summary>
		///   Switch that enables/disables caching of image buttons (when XXXImageFormat properties are used).
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setCacheGuiGraphics : function(value) {
		/// <summary>
		///   Switch that enables/disables caching of image buttons (when XXXImageFormat properties are used).
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getCharset : function() {
		/// <summary>
		///   Charset used to encode the text data submitted by Image Uploader.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setCharset : function(value) {
		/// <summary>
		///   Charset used to encode the text data submitted by Image Uploader.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getCheckFilesBySelectAllButton : function() {
		/// <summary>
		///   Switch that defines behavior of the Select All and Deselect All buttons	in TwoPanes layout (whether these buttons should check/uncheck or select/deselect files when clicked).
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setCheckFilesBySelectAllButton : function(value) {
		/// <summary>
		///   Switch that defines behavior of the Select All and Deselect All buttons	in TwoPanes layout (whether these buttons should check/uncheck or select/deselect files when clicked).
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getCmykImagesAreNotAllowedText : function() {
		/// <summary>
		///   Text which appears instead of the description text field if the image is CMYK when CMYK images are not allowed for the upload.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setCmykImagesAreNotAllowedText : function(value) {
		/// <summary>
		///   Text which appears instead of the description text field if the image is CMYK when CMYK images are not allowed for the upload.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getCurrentConnectionId : function() {
		/// <summary>
		///   Identifier of the upload stream the event is raised for.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	
	getDeleteFilesDialogTitleText : function() {
		/// <summary>
		///   Title of the dialog which asks the user to confirm that they really want to delete uploaded or selected files.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setDeleteFilesDialogTitleText : function(value) {
		/// <summary>
		///   Title of the dialog which asks the user to confirm that they really want to delete uploaded or selected files.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getDeleteSelectedFilesDialogMessageText : function() {
		/// <summary>
		///   The text of the message box which asks the user to confirm that they really want to delete selected files.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setDeleteSelectedFilesDialogMessageText : function(value) {
		/// <summary>
		///   The text of the message box which asks the user to confirm that they really want to delete selected files.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getDeleteUploadedFiles : function() {
		/// <summary>
		///   Switch that defines whether successfully uploaded files should be deleted.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setDeleteUploadedFiles : function(value) {
		/// <summary>
		///   Switch that defines whether successfully uploaded files should be deleted.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getDeleteUploadedFilesDialogMessageText : function() {
		/// <summary>
		///   The text of the message box which asks the user to confirm that they really want to delete uploaded files.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setDeleteUploadedFilesDialogMessageText : function(value) {
		/// <summary>
		///   The text of the message box which asks the user to confirm that they really want to delete uploaded files.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getDeniedFileMask : function() {
		/// <summary>
		///   File mask for the files that are denied to be displayed and uploaded.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setDeniedFileMask : function(value) {
		/// <summary>
		///   File mask for the files that are denied to be displayed and uploaded.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getDescriptionEditorButtonCancelText : function() {
		/// <summary>
		///   Text for the Cancel button of the description editor.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setDescriptionEditorButtonCancelText : function(value) {
		/// <summary>
		///   Text for the Cancel button of the description editor.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getDescriptionEditorButtonOkText : function() {
		/// <summary>
		///   Text on the OK button of the description editor.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setDescriptionEditorButtonOkText : function(value) {
		/// <summary>
		///   Text on the OK button of the description editor.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getDescriptionTooltipText : function() {
		/// <summary>
		///   Text of the tooltip which is displayed when mouse pointer is hovering over the description text field.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setDescriptionTooltipText : function(value) {
		/// <summary>
		///   Text of the tooltip which is displayed when mouse pointer is hovering over the description text field.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getDescriptionsReadOnly : function() {
		/// <summary>
		///   Indicator which specifies whether the user is allowed to edit descriptions.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setDescriptionsReadOnly : function(value) {
		/// <summary>
		///   Indicator which specifies whether the user is allowed to edit descriptions.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getDimensionsAreTooLargeText : function() {
		/// <summary>
		///   Text of the label displayed when image dimensions are larger than specified with the and properties.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setDimensionsAreTooLargeText : function(value) {
		/// <summary>
		///   Text of the label displayed when image dimensions are larger than specified with the and properties.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getDimensionsAreTooSmallText : function() {
		/// <summary>
		///   Text of the label displayed when image dimensions are smaller than specified with the and properties.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setDimensionsAreTooSmallText : function(value) {
		/// <summary>
		///   Text of the label displayed when image dimensions are smaller than specified with the and properties.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getDisplayNameActiveSelectedTextColor : function() {
		/// <summary>
		///   Color of the item display name when this item is selected and the focus is set on the pane which contains this item.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setDisplayNameActiveSelectedTextColor : function(value) {
		/// <summary>
		///   Color of the item display name when this item is selected and the focus is set on the pane which contains this item.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getDisplayNameActiveUnselectedTextColor : function() {
		/// <summary>
		///   Color of the item display name when this item is not selected and the focus is set on the pane which contains this item.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setDisplayNameActiveUnselectedTextColor : function(value) {
		/// <summary>
		///   Color of the item display name when this item is not selected and the focus is set on the pane which contains this item.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getDisplayNameInactiveSelectedTextColor : function() {
		/// <summary>
		///   Color of the item display name when this item is selected and the focus is out of the pane which contains this item.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setDisplayNameInactiveSelectedTextColor : function(value) {
		/// <summary>
		///   Color of the item display name when this item is selected and the focus is out of the pane which contains this item.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getDisplayNameInactiveUnselectedTextColor : function() {
		/// <summary>
		///   Color of the item display name when this item is not selected and the focus is out of the pane which contains this item.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setDisplayNameInactiveUnselectedTextColor : function(value) {
		/// <summary>
		///   Color of the item display name when this item is not selected and the focus is out of the pane which contains this item.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getDropFilesHereImageFormat : function() {
		/// <summary>
		///   Image displayed on the upload pane when no files are selected for the upload.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setDropFilesHereImageFormat : function(value) {
		/// <summary>
		///   Image displayed on the upload pane when no files are selected for the upload.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getDropFilesHereText : function() {
		/// <summary>
		///   Text of the label on the upload pane displayed when no files are selected for the upload.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setDropFilesHereText : function(value) {
		/// <summary>
		///   Text of the label on the upload pane displayed when no files are selected for the upload.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getEditDescriptionText : function() {
		/// <summary>
		///   Text of the label displayed instead of the description when it is empty.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setEditDescriptionText : function(value) {
		/// <summary>
		///   Text of the label displayed instead of the description when it is empty.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getEditDescriptionTextColor : function() {
		/// <summary>
		///   Color of the text label displayed instead of the description when it is empty.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setEditDescriptionTextColor : function(value) {
		/// <summary>
		///   Color of the text label displayed instead of the description when it is empty.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getEditDescriptionTooltipText : function() {
		/// <summary>
		///   Text of the tooltip which is displayed when mouse pointer is hovering over the Edit Description label-hyperlink.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setEditDescriptionTooltipText : function(value) {
		/// <summary>
		///   Text of the tooltip which is displayed when mouse pointer is hovering over the Edit Description label-hyperlink.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getEnableFileViewer : function() {
		/// <summary>
		///   Switch that indicates whether to open the file in associated viewer software when it is double-clicked in both folder and upload pane.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setEnableFileViewer : function(value) {
		/// <summary>
		///   Switch that indicates whether to open the file in associated viewer software when it is double-clicked in both folder and upload pane.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getEnableInstantUpload : function() {
		/// <summary>
		///   Switch that specifies whether to upload files instantly when they were added to the upload pane.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setEnableInstantUpload : function(value) {
		/// <summary>
		///   Switch that specifies whether to upload files instantly when they were added to the upload pane.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getEnableRemoveIcon : function() {
		/// <summary>
		///   Switch that specifies whether to display remove icons on preview thumbnails.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setEnableRemoveIcon : function(value) {
		/// <summary>
		///   Switch that specifies whether to display remove icons on preview thumbnails.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getErrorDeletingFilesDialogMessageText : function() {
		/// <summary>
		///   Text of the error message which states that the specified file cannot be deleted.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setErrorDeletingFilesDialogMessageText : function(value) {
		/// <summary>
		///   Text of the error message which states that the specified file cannot be deleted.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getExtractExif : function() {
		/// <summary>
		///   List of EXIF fields which should be extracted and uploaded along with other data.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setExtractExif : function(value) {
		/// <summary>
		///   List of EXIF fields which should be extracted and uploaded along with other data.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getExtractIptc : function() {
		/// <summary>
		///   List of IPTC fields which should be extracted and uploaded along with other data.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setExtractIptc : function(value) {
		/// <summary>
		///   List of IPTC fields which should be extracted and uploaded along with other data.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getFileIsTooLargeText : function() {
		/// <summary>
		///   Text which appears instead of the description text field if the file exceeds the value.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setFileIsTooLargeText : function(value) {
		/// <summary>
		///   Text which appears instead of the description text field if the file exceeds the value.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getFileIsTooSmallText : function() {
		/// <summary>
		///   Text which appears instead of the description text field if the file is smaller than value.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setFileIsTooSmallText : function(value) {
		/// <summary>
		///   Text which appears instead of the description text field if the file is smaller than value.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getFileMask : function() {
		/// <summary>
		///   File mask for the files which are allowed to be displayed and uploaded.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setFileMask : function(value) {
		/// <summary>
		///   File mask for the files which are allowed to be displayed and uploaded.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getFilesPerOnePackageCount : function() {
		/// <summary>
		///   Number of files which should be sent with a single request.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setFilesPerOnePackageCount : function(value) {
		/// <summary>
		///   Number of files which should be sent with a single request.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getFolderPaneAllowRotate : function() {
		/// <summary>
		///   Switch that specifies whether to display rotate icons on thumbnails in the folder pane.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setFolderPaneAllowRotate : function(value) {
		/// <summary>
		///   Switch that specifies whether to display rotate icons on thumbnails in the folder pane.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getFolderPaneBackgroundColor : function() {
		/// <summary>
		///   Background color of the folder pane.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setFolderPaneBackgroundColor : function(value) {
		/// <summary>
		///   Background color of the folder pane.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getFolderPaneBorderStyle : function() {
		/// <summary>
		///   Border style of the folder pane.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setFolderPaneBorderStyle : function(value) {
		/// <summary>
		///   Border style of the folder pane.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (None - 0, Fixed3D - 1, or FixedSingle - 2).
		/// </param>
	}
	,
	
	
	getFolderPaneHeight : function() {
		/// <summary>
		///   Height of the folder pane.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setFolderPaneHeight : function(value) {
		/// <summary>
		///   Height of the folder pane.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getFolderPaneShowDescriptions : function() {
		/// <summary>
		///   Switch that specifies whether Edit Description elements should be displayed under each thumbnail in the folder list.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setFolderPaneShowDescriptions : function(value) {
		/// <summary>
		///   Switch that specifies whether Edit Description elements should be displayed under each thumbnail in the folder list.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getFolderPaneSortMode : function() {
		/// <summary>
		///   Sort mode of the folder pane.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setFolderPaneSortMode : function(value) {
		/// <summary>
		///   Sort mode of the folder pane.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (Unsorted - 0, Name - 1, Size - 2, Type - 3, Modified - 4, or Path - 5).
		/// </param>
	}
	,
	
	
	getFolderView : function() {
		/// <summary>
		///   View mode for the folder pane.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setFolderView : function(value) {
		/// <summary>
		///   View mode for the folder pane.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getGuiGraphicsVersion : function() {
		/// <summary>
		///   Version indicator of images used as buttons and icons of Image Uploader user interface.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setGuiGraphicsVersion : function(value) {
		/// <summary>
		///   Version indicator of images used as buttons and icons of Image Uploader user interface.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getHashAlgorithm : function() {
		/// <summary>
		///   Algorithm to generate a hash value for each file to upload to the server.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setHashAlgorithm : function(value) {
		/// <summary>
		///   Algorithm to generate a hash value for each file to upload to the server.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getHoursText : function() {
		/// <summary>
		///   Text for the hours unit in the progress dialog.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setHoursText : function(value) {
		/// <summary>
		///   Text for the hours unit in the progress dialog.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getIncludeSubfolders : function() {
		/// <summary>
		///   Switch that specifies whether a folder should be added to upload list including its subfolders.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setIncludeSubfolders : function(value) {
		/// <summary>
		///   Switch that specifies whether a folder should be added to upload list including its subfolders.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getIncludeSubfoldersText : function() {
		/// <summary>
		///   Text of the Include Subfolders checkbox in the Open Folder dialog.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setIncludeSubfoldersText : function(value) {
		/// <summary>
		///   Text of the Include Subfolders checkbox in the Open Folder dialog.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getIncorrectFileActiveSelectedTextColor : function() {
		/// <summary>
		///   Color of the text caption displayed for the item which cannot be uploaded (e.g. because of file filters) when this item is selected and the focus is set on the pane which contains this item.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setIncorrectFileActiveSelectedTextColor : function(value) {
		/// <summary>
		///   Color of the text caption displayed for the item which cannot be uploaded (e.g. because of file filters) when this item is selected and the focus is set on the pane which contains this item.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getIncorrectFileActiveUnselectedTextColor : function() {
		/// <summary>
		///   Color of the text caption displayed for the item which cannot be uploaded (e.g. because of file filters) when this item is not selected and the focus is set on the pane which contains this item.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setIncorrectFileActiveUnselectedTextColor : function(value) {
		/// <summary>
		///   Color of the text caption displayed for the item which cannot be uploaded (e.g. because of file filters) when this item is not selected and the focus is set on the pane which contains this item.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getIncorrectFileInactiveSelectedTextColor : function() {
		/// <summary>
		///   Color of the text caption displayed for the item which cannot be uploaded (e.g. because of file filters) when this item is selected and the focus is out of the pane which contains this item.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setIncorrectFileInactiveSelectedTextColor : function(value) {
		/// <summary>
		///   Color of the text caption displayed for the item which cannot be uploaded (e.g. because of file filters) when this item is selected and the focus is out of the pane which contains this item.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getIncorrectFileInactiveUnselectedTextColor : function() {
		/// <summary>
		///   Color of the text caption displayed for the item which cannot be uploaded (e.g. because of file filters) when this item is not selected and the focus is out of the pane which contains this item.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setIncorrectFileInactiveUnselectedTextColor : function(value) {
		/// <summary>
		///   Color of the text caption displayed for the item which cannot be uploaded (e.g. because of file filters) when this item is not selected and the focus is out of the pane which contains this item.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getJreVersion : function() {
		/// <summary>
		///   Version number of Java Runtime Environment (JRE) installed on current machine.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	
	getKilobytesText : function() {
		/// <summary>
		///   Text for the kilobytes unit in the progress dialog.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setKilobytesText : function(value) {
		/// <summary>
		///   Text for the kilobytes unit in the progress dialog.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getLargePreviewBackgroundColor : function() {
		/// <summary>
		///   Background color of the preview window.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setLargePreviewBackgroundColor : function(value) {
		/// <summary>
		///   Background color of the preview window.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getLargePreviewGeneratingPreviewText : function() {
		/// <summary>
		///   Text that appears in the preview window while a preview is being generated.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setLargePreviewGeneratingPreviewText : function(value) {
		/// <summary>
		///   Text that appears in the preview window while a preview is being generated.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getLargePreviewHeight : function() {
		/// <summary>
		///   Height of the preview window.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setLargePreviewHeight : function(value) {
		/// <summary>
		///   Height of the preview window.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getLargePreviewIconImageFormat : function() {
		/// <summary>
		///   List of images that specify different states of the icon which is displayed on a thumbnail and opens a larger preview of that image.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setLargePreviewIconImageFormat : function(value) {
		/// <summary>
		///   List of images that specify different states of the icon which is displayed on a thumbnail and opens a larger preview of that image.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getLargePreviewIconTooltipText : function() {
		/// <summary>
		///   Text of the tooltip which is displayed when mouse pointer is hovering over the image preview icon.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setLargePreviewIconTooltipText : function(value) {
		/// <summary>
		///   Text of the tooltip which is displayed when mouse pointer is hovering over the image preview icon.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getLargePreviewNoPreviewAvailableText : function() {
		/// <summary>
		///   Text that appears in the preview window if no preview could be generated.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setLargePreviewNoPreviewAvailableText : function(value) {
		/// <summary>
		///   Text that appears in the preview window if no preview could be generated.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getLargePreviewWidth : function() {
		/// <summary>
		///   Width of the preview window.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setLargePreviewWidth : function(value) {
		/// <summary>
		///   Width of the preview window.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getLicenseKey : function() {
		/// <summary>
		///   License key which removes evaluation version limitations for purchased domain(s)/IP(s).
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setLicenseKey : function(value) {
		/// <summary>
		///   License key which removes evaluation version limitations for purchased domain(s)/IP(s).
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getListColumnFileNameText : function() {
		/// <summary>
		///   Title of the file name column in the Details view.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setListColumnFileNameText : function(value) {
		/// <summary>
		///   Title of the file name column in the Details view.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getListColumnFileSizeText : function() {
		/// <summary>
		///   Title of the file size column in the Details view.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setListColumnFileSizeText : function(value) {
		/// <summary>
		///   Title of the file size column in the Details view.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getListColumnFileTypeText : function() {
		/// <summary>
		///   Title of the file type column in the Details view.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setListColumnFileTypeText : function(value) {
		/// <summary>
		///   Title of the file type column in the Details view.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getListColumnLastModifiedText : function() {
		/// <summary>
		///   Title of the last modification date column in the Details view.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setListColumnLastModifiedText : function(value) {
		/// <summary>
		///   Title of the last modification date column in the Details view.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getListKilobytesText : function() {
		/// <summary>
		///   Text for kilobytes unit in the file size column of the Details view.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setListKilobytesText : function(value) {
		/// <summary>
		///   Text for kilobytes unit in the file size column of the Details view.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getLoadingFilesText : function() {
		/// <summary>
		///   Text on the folder pane which is displayed while Image Uploader scans the folder content and builds the file list.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setLoadingFilesText : function(value) {
		/// <summary>
		///   Text on the folder pane which is displayed while Image Uploader scans the folder content and builds the file list.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getLookAndFeel : function() {
		/// <summary>
		///   Look and feel of the Image Uploader Java applet.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setLookAndFeel : function(value) {
		/// <summary>
		///   Look and feel of the Image Uploader Java applet.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMaxConnectionCount : function() {
		/// <summary>
		///   Number of simultaneous connections files are uploaded with.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setMaxConnectionCount : function(value) {
		/// <summary>
		///   Number of simultaneous connections files are uploaded with.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMaxDescriptionTextLength : function() {
		/// <summary>
		///   Maximum allowed number of characters in descriptions entered by the user.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setMaxDescriptionTextLength : function(value) {
		/// <summary>
		///   Maximum allowed number of characters in descriptions entered by the user.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMaxFileCount : function() {
		/// <summary>
		///   Maximum number of files allowed for upload per one session.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setMaxFileCount : function(value) {
		/// <summary>
		///   Maximum number of files allowed for upload per one session.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMaxFileSize : function() {
		/// <summary>
		///   Maximum file size which is allowed for upload to the server.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setMaxFileSize : function(value) {
		/// <summary>
		///   Maximum file size which is allowed for upload to the server.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMaxImageHeight : function() {
		/// <summary>
		///   Maximum image height allowed for upload.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setMaxImageHeight : function(value) {
		/// <summary>
		///   Maximum image height allowed for upload.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMaxImageWidth : function() {
		/// <summary>
		///   Maximum image width allowed for upload.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setMaxImageWidth : function(value) {
		/// <summary>
		///   Maximum image width allowed for upload.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMaxTotalFileSize : function() {
		/// <summary>
		///   Maximum total file size allowed for upload.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setMaxTotalFileSize : function(value) {
		/// <summary>
		///   Maximum total file size allowed for upload.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMegabytesText : function() {
		/// <summary>
		///   Text for the megabytes unit in the progress dialog.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMegabytesText : function(value) {
		/// <summary>
		///   Text for the megabytes unit in the progress dialog.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMenuAddAllToUploadListText : function() {
		/// <summary>
		///   Caption of the Add All To Upload List context menu item.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMenuAddAllToUploadListText : function(value) {
		/// <summary>
		///   Caption of the Add All To Upload List context menu item.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMenuAddToUploadListText : function() {
		/// <summary>
		///   Caption of the Add To Upload List context menu item.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMenuAddToUploadListText : function(value) {
		/// <summary>
		///   Caption of the Add To Upload List context menu item.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMenuArrangeByModifiedText : function() {
		/// <summary>
		///   Caption of the Modified context menu item inside Arrange Icons By group.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMenuArrangeByModifiedText : function(value) {
		/// <summary>
		///   Caption of the Modified context menu item inside Arrange Icons By group.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMenuArrangeByNameText : function() {
		/// <summary>
		///   Caption of the Name context menu item inside Arrange Icons By group.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMenuArrangeByNameText : function(value) {
		/// <summary>
		///   Caption of the Name context menu item inside Arrange Icons By group.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMenuArrangeByPathText : function() {
		/// <summary>
		///   Caption of the Path context menu item inside Arrange Icons By group.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMenuArrangeByPathText : function(value) {
		/// <summary>
		///   Caption of the Path context menu item inside Arrange Icons By group.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMenuArrangeBySizeText : function() {
		/// <summary>
		///   Caption of the Size context menu item inside Arrange Icons By group.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMenuArrangeBySizeText : function(value) {
		/// <summary>
		///   Caption of the Size context menu item inside Arrange Icons By group.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMenuArrangeByText : function() {
		/// <summary>
		///   Caption of the Arrange Icons By context menu.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMenuArrangeByText : function(value) {
		/// <summary>
		///   Caption of the Arrange Icons By context menu.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMenuArrangeByTypeText : function() {
		/// <summary>
		///   Caption of the Type context menu item inside Arrange Icons By group.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMenuArrangeByTypeText : function(value) {
		/// <summary>
		///   Caption of the Type context menu item inside Arrange Icons By group.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMenuArrangeByUnsortedText : function() {
		/// <summary>
		///   Caption of the Unsorted context menu item inside Arrange Icons By group.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMenuArrangeByUnsortedText : function(value) {
		/// <summary>
		///   Caption of the Unsorted context menu item inside Arrange Icons By group.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMenuDeselectAllText : function() {
		/// <summary>
		///   Caption of the Deselect All context menu item.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMenuDeselectAllText : function(value) {
		/// <summary>
		///   Caption of the Deselect All context menu item.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMenuDetailsText : function() {
		/// <summary>
		///   Caption of the Details context menu item.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMenuDetailsText : function(value) {
		/// <summary>
		///   Caption of the Details context menu item.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMenuIconsText : function() {
		/// <summary>
		///   Caption of the Icons context menu item.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMenuIconsText : function(value) {
		/// <summary>
		///   Caption of the Icons context menu item.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMenuInvertSelectionText : function() {
		/// <summary>
		///   Caption of the Invert Selection context menu item.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMenuInvertSelectionText : function(value) {
		/// <summary>
		///   Caption of the Invert Selection context menu item.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMenuListText : function() {
		/// <summary>
		///   Caption of the List context menu item.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMenuListText : function(value) {
		/// <summary>
		///   Caption of the List context menu item.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMenuRefreshText : function() {
		/// <summary>
		///   Caption of the Refresh context menu item.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMenuRefreshText : function(value) {
		/// <summary>
		///   Caption of the Refresh context menu item.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMenuRemoveAllFromUploadListText : function() {
		/// <summary>
		///   Caption of the Remove All From Upload List context menu item.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMenuRemoveAllFromUploadListText : function(value) {
		/// <summary>
		///   Caption of the Remove All From Upload List context menu item.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMenuRemoveFromUploadListText : function() {
		/// <summary>
		///   Caption of the Remove From Upload List context menu item.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMenuRemoveFromUploadListText : function(value) {
		/// <summary>
		///   Caption of the Remove From Upload List context menu item.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMenuSelectAllText : function() {
		/// <summary>
		///   Caption of the Select All context menu item.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMenuSelectAllText : function(value) {
		/// <summary>
		///   Caption of the Select All context menu item.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMenuThumbnailsText : function() {
		/// <summary>
		///   Caption of the Thumbnails context menu item.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMenuThumbnailsText : function(value) {
		/// <summary>
		///   Caption of the Thumbnails context menu item.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMessageBoxTitleText : function() {
		/// <summary>
		///   Title of the message box which appears when some error occurs or upload is finished.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMessageBoxTitleText : function(value) {
		/// <summary>
		///   Title of the message box which appears when some error occurs or upload is finished.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMessageCannotConnectToInternetText : function() {
		/// <summary>
		///   Text of the error message which states that Image Uploader failed to connect to Internet.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMessageCannotConnectToInternetText : function(value) {
		/// <summary>
		///   Text of the error message which states that Image Uploader failed to connect to Internet.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMessageCmykImagesAreNotAllowedText : function() {
		/// <summary>
		///   Text of the error message which states that the selected image is CMYK when CMYK images are not allowed for the upload.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMessageCmykImagesAreNotAllowedText : function(value) {
		/// <summary>
		///   Text of the error message which states that the selected image is CMYK when CMYK images are not allowed for the upload.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMessageDimensionsAreTooLargeText : function() {
		/// <summary>
		///   Text of the error message which states that the width or height of the selected image is too large.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMessageDimensionsAreTooLargeText : function(value) {
		/// <summary>
		///   Text of the error message which states that the width or height of the selected image is too large.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMessageDimensionsAreTooSmallText : function() {
		/// <summary>
		///   Text of the error message which states that the width or height of selected image is too small.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMessageDimensionsAreTooSmallText : function(value) {
		/// <summary>
		///   Text of the error message which states that the width or height of selected image is too small.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMessageFileSizeIsTooSmallText : function() {
		/// <summary>
		///   Text of the error message which states that the user tries to select too small file.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMessageFileSizeIsTooSmallText : function(value) {
		/// <summary>
		///   Text of the error message which states that the user tries to select too small file.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMessageMaxFileCountExceededText : function() {
		/// <summary>
		///   Text of the error message which states that the user tries to select too many files.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMessageMaxFileCountExceededText : function(value) {
		/// <summary>
		///   Text of the error message which states that the user tries to select too many files.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMessageMaxFileSizeExceededText : function() {
		/// <summary>
		///   Text of the error message which states that the user tries to select too large file.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMessageMaxFileSizeExceededText : function(value) {
		/// <summary>
		///   Text of the error message which states that the user tries to select too large file.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMessageMaxTotalFileSizeExceededText : function() {
		/// <summary>
		///   Text of the error message which states that the total size of selected files is too large.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMessageMaxTotalFileSizeExceededText : function(value) {
		/// <summary>
		///   Text of the error message which states that the total size of selected files is too large.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMessageNoInternetSessionWasEstablishedText : function() {
		/// <summary>
		///   Text of the error message which states that there is no Internet session available.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMessageNoInternetSessionWasEstablishedText : function(value) {
		/// <summary>
		///   Text of the error message which states that there is no Internet session available.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMessageNoResponseFromServerText : function() {
		/// <summary>
		///   Text of the error message which states that an error was encountered when sending a request to the server and the server did not return any response.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMessageNoResponseFromServerText : function(value) {
		/// <summary>
		///   Text of the error message which states that an error was encountered when sending a request to the server and the server did not return any response.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMessageRetryOpenFolderText : function() {
		/// <summary>
		///   Text of the message which is displayed when Image Uploader tries to restore last visited folder and it is not available.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMessageRetryOpenFolderText : function(value) {
		/// <summary>
		///   Text of the message which is displayed when Image Uploader tries to restore last visited folder and it is not available.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMessageServerNotFoundText : function() {
		/// <summary>
		///   Text of the error message which states that the server with the name, specified in the parameter, cannot be found.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMessageServerNotFoundText : function(value) {
		/// <summary>
		///   Text of the error message which states that the server with the name, specified in the parameter, cannot be found.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMessageSwitchAnotherFolderWarningText : function() {
		/// <summary>
		///   Text of the warning message which is displayed when the user changes the current folder and some items are selected for upload in this folder.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMessageSwitchAnotherFolderWarningText : function(value) {
		/// <summary>
		///   Text of the warning message which is displayed when the user changes the current folder and some items are selected for upload in this folder.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMessageUnexpectedErrorText : function() {
		/// <summary>
		///   Text of the user-friendly error message for errors which are meaningful to website developer only.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMessageUnexpectedErrorText : function(value) {
		/// <summary>
		///   Text of the user-friendly error message for errors which are meaningful to website developer only.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMessageUploadCancelledText : function() {
		/// <summary>
		///   Text of the error message which states that the user cancelled the upload.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMessageUploadCancelledText : function(value) {
		/// <summary>
		///   Text of the error message which states that the user cancelled the upload.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMessageUploadCompleteText : function() {
		/// <summary>
		///   Text of the message box which notifies that the upload is successfully finished.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMessageUploadCompleteText : function(value) {
		/// <summary>
		///   Text of the message box which notifies that the upload is successfully finished.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMessageUploadFailedText : function() {
		/// <summary>
		///   Text of the error message which states that the upload failed because the connection was broken.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMessageUploadFailedText : function(value) {
		/// <summary>
		///   Text of the error message which states that the upload failed because the connection was broken.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMessageUserSpecifiedTimeoutHasExpiredText : function() {
		/// <summary>
		///   Text of the error message which states that the client-side timeout has expired. To disable this message, set it to an empty string.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMessageUserSpecifiedTimeoutHasExpiredText : function(value) {
		/// <summary>
		///   Text of the error message which states that the client-side timeout has expired. To disable this message, set it to an empty string.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMetaDataSeparator : function() {
		/// <summary>
		///   String used to separate multiple metadata fields (such as IptcKeyword).
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMetaDataSeparator : function(value) {
		/// <summary>
		///   String used to separate multiple metadata fields (such as IptcKeyword).
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMinFileCount : function() {
		/// <summary>
		///   Minimum number of files allowed for upload per one session.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setMinFileCount : function(value) {
		/// <summary>
		///   Minimum number of files allowed for upload per one session.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMinFileSize : function() {
		/// <summary>
		///   Minimum file size allowed for upload.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setMinFileSize : function(value) {
		/// <summary>
		///   Minimum file size allowed for upload.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMinImageHeight : function() {
		/// <summary>
		///   Minimum image height allowed for upload.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setMinImageHeight : function(value) {
		/// <summary>
		///   Minimum image height allowed for upload.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMinImageWidth : function() {
		/// <summary>
		///   Minimum image width allowed for upload.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setMinImageWidth : function(value) {
		/// <summary>
		///   Minimum image width allowed for upload.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getMinutesText : function() {
		/// <summary>
		///   Text for the minutes unit in the progress dialog.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setMinutesText : function(value) {
		/// <summary>
		///   Text for the minutes unit in the progress dialog.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getPaneBackgroundColor : function() {
		/// <summary>
		///   Background color of all Image Uploader panes.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setPaneBackgroundColor : function(value) {
		/// <summary>
		///   Background color of all Image Uploader panes.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getPaneFont : function() {
		/// <summary>
		///   Font format for all Image Uploader panes and labels.
		/// </summary>
		/// <returns type="FontInfo"></returns>
	}
	,
	
	setPaneFont : function(value) {
		/// <summary>
		///   Font format for all Image Uploader panes and labels.
		/// </summary>
		/// <param name="value" type="FontInfo">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getPaneItemCanBeUploaded : function(Index) {
		/// <summary>
		///   Value that shows if the specified item can be uploaded at all.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A non-negative integer that specifies an index of a thumbnail item on a pane (zero-based). It should not exceed - 1 for the implied pane. Depending on the layout mode, the following panes are implied:	In the one-pane layout mode, the upload pane is implied.	In the two-pane layout mode, the folder pane is implied.	In the three-pane layout mode, the folder pane is implied.
		/// </param>
		/// <returns type="Boolean"></returns>
	}
	,
	
	
	getPaneItemChecked : function(Index) {
		/// <summary>
		///   Value that shows if the specified item is selected for upload.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A non-negative integer that specifies an index of a thumbnail item on a pane (zero-based). It should not exceed - 1 for the implied pane. Depending on the layout mode, the following panes are implied:	In the one-pane layout mode, the upload pane is implied.	In the two-pane layout mode, the folder pane is implied.	In the three-pane layout mode, the folder pane is implied.
		/// </param>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setPaneItemChecked : function(Index, value) {
		/// <summary>
		///   Value that shows if the specified item is selected for upload.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A non-negative integer that specifies an index of a thumbnail item on a pane (zero-based). It should not exceed - 1 for the implied pane. Depending on the layout mode, the following panes are implied:	In the one-pane layout mode, the upload pane is implied.	In the two-pane layout mode, the folder pane is implied.	In the three-pane layout mode, the folder pane is implied.
		/// </param>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getPaneItemCount : function(Pane) {
		/// <summary>
		///   Number of items on the specified pane.
		/// </summary>
		/// <param name="Pane" type="String">
		///   A member of the following enumeration: FolderPane - 0, UploadPane - 1.
		/// </param>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	
	getPaneItemDesign : function(Pane, Index) {
		/// <summary>
		///   Settings for thumbnail item appearance.
		/// </summary>
		/// <param name="Pane" type="String">
		///   A member of the following enumeration: FolderPane - 0, UploadPane - 1.
		/// </param>
		/// <param name="Index" type="Number" integer="true">
		///   A non-negative integer that specifies an index of a thumbnail item on a pane (one-based). It should not exceed - 1 for the specified pane.
		/// </param>
		/// <returns type="String"></returns>
	}
	,
	
	setPaneItemDesign : function(Pane, Index, value) {
		/// <summary>
		///   Settings for thumbnail item appearance.
		/// </summary>
		/// <param name="Pane" type="String">
		///   A member of the following enumeration: FolderPane - 0, UploadPane - 1.
		/// </param>
		/// <param name="Index" type="Number" integer="true">
		///   A non-negative integer that specifies an index of a thumbnail item on a pane (one-based). It should not exceed - 1 for the specified pane.
		/// </param>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getPaneItemEnabled : function(Pane, Index) {
		/// <summary>
		///   Value that shows if the specified item on the specified pane is enabled.
		/// </summary>
		/// <param name="Pane" type="String">
		///   A member of the following enumeration: FolderPane - 0, UploadPane - 1.
		/// </param>
		/// <param name="Index" type="Number" integer="true">
		///   A non-negative integer that specifies an index of a thumbnail item on a pane (one-based). It should not exceed - 1 for the specified pane.
		/// </param>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setPaneItemEnabled : function(Pane, Index, value) {
		/// <summary>
		///   Value that shows if the specified item on the specified pane is enabled.
		/// </summary>
		/// <param name="Pane" type="String">
		///   A member of the following enumeration: FolderPane - 0, UploadPane - 1.
		/// </param>
		/// <param name="Index" type="Number" integer="true">
		///   A non-negative integer that specifies an index of a thumbnail item on a pane (one-based). It should not exceed - 1 for the specified pane.
		/// </param>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getPaneItemSelected : function(Pane, Index) {
		/// <summary>
		///   Value that shows if the specified item on the specified pane is selected (as in group selection).
		/// </summary>
		/// <param name="Pane" type="String">
		///   A member of the following enumeration: FolderPane - 0, UploadPane - 1.
		/// </param>
		/// <param name="Index" type="Number" integer="true">
		///   A non-negative integer that specifies an index of a thumbnail item on a pane (one-based). It should not exceed - 1 for the specified pane.
		/// </param>
		/// <returns type="Boolean"></returns>
	}
	,
	
	
	getPaneLayout : function() {
		/// <summary>
		///   Layout of Image Uploader panes.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setPaneLayout : function(value) {
		/// <summary>
		///   Layout of Image Uploader panes.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getPasteFileNameTemplate : function() {
		/// <summary>
		///   File name template for files pasted from clipboard.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setPasteFileNameTemplate : function(value) {
		/// <summary>
		///   File name template for files pasted from clipboard.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getPostFormat : function() {
		/// <summary>
		///   POST request format used by Image Uploader to upload files.
		/// </summary>
		/// <returns type="PostFormat"></returns>
	}
	,
	
	setPostFormat : function(value) {
		/// <summary>
		///   POST request format used by Image Uploader to upload files.
		/// </summary>
		/// <param name="value" type="PostFormat">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getPreviewThumbnailActiveDropHighlightedColor : function() {
		/// <summary>
		///   Color of the thumbnail box selection when this thumbnail belongs to a focused pane and the user drags some items over it.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setPreviewThumbnailActiveDropHighlightedColor : function(value) {
		/// <summary>
		///   Color of the thumbnail box selection when this thumbnail belongs to a focused pane and the user drags some items over it.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getPreviewThumbnailActiveSelectionColor : function() {
		/// <summary>
		///   Color of the item selection frame when the parent pane is in focus.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setPreviewThumbnailActiveSelectionColor : function(value) {
		/// <summary>
		///   Color of the item selection frame when the parent pane is in focus.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getPreviewThumbnailBorderColor : function() {
		/// <summary>
		///   Color of the preview thumbnail box frame.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setPreviewThumbnailBorderColor : function(value) {
		/// <summary>
		///   Color of the preview thumbnail box frame.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getPreviewThumbnailBorderHoverColor : function() {
		/// <summary>
		///   Color of the preview box frame when the cursor is hovered over the thumbnail.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setPreviewThumbnailBorderHoverColor : function(value) {
		/// <summary>
		///   Color of the preview box frame when the cursor is hovered over the thumbnail.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getPreviewThumbnailInactiveDropHighlightedColor : function() {
		/// <summary>
		///   Color of the thumbnail box selection when this thumbnail belongs to a pane which is out of focus and the user drags some items over it.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setPreviewThumbnailInactiveDropHighlightedColor : function(value) {
		/// <summary>
		///   Color of the thumbnail box selection when this thumbnail belongs to a pane which is out of focus and the user drags some items over it.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getPreviewThumbnailInactiveSelectionColor : function() {
		/// <summary>
		///   Color of item selection frame when the parent pane is out of focus.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setPreviewThumbnailInactiveSelectionColor : function(value) {
		/// <summary>
		///   Color of item selection frame when the parent pane is out of focus.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getPreviewThumbnailResizeQuality : function() {
		/// <summary>
		///   Resize quality of preview thumbnails.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setPreviewThumbnailResizeQuality : function(value) {
		/// <summary>
		///   Resize quality of preview thumbnails.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (Medium - 0 or High - 1).
		/// </param>
	}
	,
	
	
	getPreviewThumbnailSize : function() {
		/// <summary>
		///   Size of thumbnails in the preview area when Thumbnails view mode is used.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setPreviewThumbnailSize : function(value) {
		/// <summary>
		///   Size of thumbnails in the preview area when Thumbnails view mode is used.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getProgressDialogCancelButtonText : function() {
		/// <summary>
		///   Caption of the Cancel button on the progress dialog.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setProgressDialogCancelButtonText : function(value) {
		/// <summary>
		///   Caption of the Cancel button on the progress dialog.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getProgressDialogCloseButtonText : function() {
		/// <summary>
		///   Caption of the Close button on the progress dialog.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setProgressDialogCloseButtonText : function(value) {
		/// <summary>
		///   Caption of the Close button on the progress dialog.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getProgressDialogCloseWhenUploadCompletesText : function() {
		/// <summary>
		///   Caption of the Close When Upload Completes checkbox in the progress dialog.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setProgressDialogCloseWhenUploadCompletesText : function(value) {
		/// <summary>
		///   Caption of the Close When Upload Completes checkbox in the progress dialog.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getProgressDialogEstimatedTimeText : function() {
		/// <summary>
		///   Caption of the label on the progress dialog which displays estimated time of upload.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setProgressDialogEstimatedTimeText : function(value) {
		/// <summary>
		///   Caption of the label on the progress dialog which displays estimated time of upload.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getProgressDialogPreparingDataText : function() {
		/// <summary>
		///   Caption of the Preparing Data label on the progress dialog.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setProgressDialogPreparingDataText : function(value) {
		/// <summary>
		///   Caption of the Preparing Data label on the progress dialog.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getProgressDialogPreviewThumbnailSize : function() {
		/// <summary>
		///   Size of a thumbnail in the progress dialog.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setProgressDialogPreviewThumbnailSize : function(value) {
		/// <summary>
		///   Size of a thumbnail in the progress dialog.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getProgressDialogSentText : function() {
		/// <summary>
		///   Caption of the label on the progress dialog which specifies how many files have already been sent.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setProgressDialogSentText : function(value) {
		/// <summary>
		///   Caption of the label on the progress dialog which specifies how many files have already been sent.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getProgressDialogTitleText : function() {
		/// <summary>
		///   Title of the progress dialog.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setProgressDialogTitleText : function(value) {
		/// <summary>
		///   Title of the progress dialog.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getProgressDialogWaitingForResponseFromServerText : function() {
		/// <summary>
		///   Text string which appears in the progress dialog when the upload completes and Image Uploader is waiting for a response from the server.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setProgressDialogWaitingForResponseFromServerText : function(value) {
		/// <summary>
		///   Text string which appears in the progress dialog when the upload completes and Image Uploader is waiting for a response from the server.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getProgressDialogWaitingForRetryText : function() {
		/// <summary>
		///   Text string which appears in the progress dialog when the upload was interrupted and Image Uploader is waiting before retrying to recover the upload.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setProgressDialogWaitingForRetryText : function(value) {
		/// <summary>
		///   Text string which appears in the progress dialog when the upload was interrupted and Image Uploader is waiting before retrying to recover the upload.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getProgressDialogWidth : function() {
		/// <summary>
		///   Width of the progress dialog.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setProgressDialogWidth : function(value) {
		/// <summary>
		///   Width of the progress dialog.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getQualityMeterAcceptableQualityColor : function() {
		/// <summary>
		///   Color of the quality meter element which corresponds to the maximum acceptable print format.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setQualityMeterAcceptableQualityColor : function(value) {
		/// <summary>
		///   Color of the quality meter element which corresponds to the maximum acceptable print format.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getQualityMeterBackgroundColor : function() {
		/// <summary>
		///   Background color of the quality meter.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setQualityMeterBackgroundColor : function(value) {
		/// <summary>
		///   Background color of the quality meter.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getQualityMeterBorderColor : function() {
		/// <summary>
		///   Color of the quality meter border.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setQualityMeterBorderColor : function(value) {
		/// <summary>
		///   Color of the quality meter border.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getQualityMeterFormats : function() {
		/// <summary>
		///   A value which defines print formats for the quality meter.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setQualityMeterFormats : function(value) {
		/// <summary>
		///   A value which defines print formats for the quality meter.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getQualityMeterHeight : function() {
		/// <summary>
		///   Height of the quality meter element.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setQualityMeterHeight : function(value) {
		/// <summary>
		///   Height of the quality meter element.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getQualityMeterHighQualityColor : function() {
		/// <summary>
		///   Color of the quality meter element which corresponds to the recommended print format.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setQualityMeterHighQualityColor : function(value) {
		/// <summary>
		///   Color of the quality meter element which corresponds to the recommended print format.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getQualityMeterLowQualityColor : function() {
		/// <summary>
		///   Color of the quality meter element corresponding to the print format which is not recommended for printing.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setQualityMeterLowQualityColor : function(value) {
		/// <summary>
		///   Color of the quality meter element corresponding to the print format which is not recommended for printing.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getRedirectUrl : function() {
		/// <summary>
		///   URL to which the user will be redirected when the upload successfully completes.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setRedirectUrl : function(value) {
		/// <summary>
		///   URL to which the user will be redirected when the upload successfully completes.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getRememberLastVisitedFolder : function() {
		/// <summary>
		///   Switch that indicates whether Image Uploader should remember the last visited folder.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setRememberLastVisitedFolder : function(value) {
		/// <summary>
		///   Switch that indicates whether Image Uploader should remember the last visited folder.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getRemoveIconImageFormat : function() {
		/// <summary>
		///   List of images that specify different states of icon displayed on the item which removes it from the upload list.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setRemoveIconImageFormat : function(value) {
		/// <summary>
		///   List of images that specify different states of icon displayed on the item which removes it from the upload list.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getRemoveIconTooltipText : function() {
		/// <summary>
		///   Text of the tooltip which is displayed when mouse pointer is hovering over the icon on an item which removes this item from the upload list.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setRemoveIconTooltipText : function(value) {
		/// <summary>
		///   Text of the tooltip which is displayed when mouse pointer is hovering over the icon on an item which removes this item from the upload list.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getRotateIconClockwiseImageFormat : function() {
		/// <summary>
		///   List of images that specify different states of icon displayed on the item which rotates it clockwise.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setRotateIconClockwiseImageFormat : function(value) {
		/// <summary>
		///   List of images that specify different states of icon displayed on the item which rotates it clockwise.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getRotateIconClockwiseTooltipText : function() {
		/// <summary>
		///   Text of the tooltip which is displayed when mouse pointer is hovering over the icon on an item which rotates this item clockwise.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setRotateIconClockwiseTooltipText : function(value) {
		/// <summary>
		///   Text of the tooltip which is displayed when mouse pointer is hovering over the icon on an item which rotates this item clockwise.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getRotateIconCounterclockwiseImageFormat : function() {
		/// <summary>
		///   List of images that specify different states of icon displayed on the item which rotates it counter-clockwise.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setRotateIconCounterclockwiseImageFormat : function(value) {
		/// <summary>
		///   List of images that specify different states of icon displayed on the item which rotates it counter-clockwise.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getRotateIconCounterclockwiseTooltipText : function() {
		/// <summary>
		///   Text of the tooltip which is displayed when mouse pointer is hovering over the icon on an item which rotates this item counter-clockwise.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setRotateIconCounterclockwiseTooltipText : function(value) {
		/// <summary>
		///   Text of the tooltip which is displayed when mouse pointer is hovering over the icon on an item which rotates this item counter-clockwise.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getSecondsText : function() {
		/// <summary>
		///   Text for seconds unit in the progress dialog.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setSecondsText : function(value) {
		/// <summary>
		///   Text for seconds unit in the progress dialog.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getSendDefaultFields : function() {
		/// <summary>
		///   Switch that specifies whether to send standard text POST fields during the upload process.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setSendDefaultFields : function(value) {
		/// <summary>
		///   Switch that specifies whether to send standard text POST fields during the upload process.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getShowButtons : function() {
		/// <summary>
		///   Switch that specifies whether Stop, Select All, Deselect All, and Send buttons should be visible.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setShowButtons : function(value) {
		/// <summary>
		///   Switch that specifies whether Stop, Select All, Deselect All, and Send buttons should be visible.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getShowContextMenu : function() {
		/// <summary>
		///   Switch that specifies if the context menu should be available by a right-click on a folder or upload pane.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setShowContextMenu : function(value) {
		/// <summary>
		///   Switch that specifies if the context menu should be available by a right-click on a folder or upload pane.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getShowDebugWindow : function() {
		/// <summary>
		///   Switch that specifies whether to display an error page returned from the server if some error occurs on the page specified with the property.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setShowDebugWindow : function(value) {
		/// <summary>
		///   Switch that specifies whether to display an error page returned from the server if some error occurs on the page specified with the property.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getShowDescriptions : function() {
		/// <summary>
		///   Switch that specifies whether to display Edit Description elements under the thumbnails.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setShowDescriptions : function(value) {
		/// <summary>
		///   Switch that specifies whether to display Edit Description elements under the thumbnails.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getShowFileNames : function() {
		/// <summary>
		///   Switch that specifies whether to display filenames in the folder and upload pane.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setShowFileNames : function(value) {
		/// <summary>
		///   Switch that specifies whether to display filenames in the folder and upload pane.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getShowSubfolders : function() {
		/// <summary>
		///   Switch that specifies whether to display subfolders in the folder pane along with files.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setShowSubfolders : function(value) {
		/// <summary>
		///   Switch that specifies whether to display subfolders in the folder pane along with files.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getShowUploadListButtons : function() {
		/// <summary>
		///   Switch that specifies whether buttons which add or remove files to or from the upload list should be visible.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setShowUploadListButtons : function(value) {
		/// <summary>
		///   Switch that specifies whether buttons which add or remove files to or from the upload list should be visible.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getSilentMode : function() {
		/// <summary>
		///   Switch that specifies whether to hide Image Uploader progress bar and all the messages during the upload.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setSilentMode : function(value) {
		/// <summary>
		///   Switch that specifies whether to hide Image Uploader progress bar and all the messages during the upload.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getSplitterLineColor : function() {
		/// <summary>
		///   Color of the splitter line (if enabled).
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setSplitterLineColor : function(value) {
		/// <summary>
		///   Color of the splitter line (if enabled).
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getSplitterLineStyle : function() {
		/// <summary>
		///   Style of splitter line.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setSplitterLineStyle : function(value) {
		/// <summary>
		///   Style of splitter line.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (Invisible - 0, Solid - 1, Dash - 2, Dot - 3, DashDot - 4, or DashDotDot - 5).
		/// </param>
	}
	,
	
	
	getThumbnailHorizontalSpacing : function() {
		/// <summary>
		///   Horizontal distance between thumbnails in the folder and upload panes.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setThumbnailHorizontalSpacing : function(value) {
		/// <summary>
		///   Horizontal distance between thumbnails in the folder and upload panes.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getThumbnailVerticalSpacing : function() {
		/// <summary>
		///   Vertical distance between thumbnails in the folder and upload panes.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setThumbnailVerticalSpacing : function(value) {
		/// <summary>
		///   Vertical distance between thumbnails in the folder and upload panes.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getTimeFormat : function() {
		/// <summary>
		///   Format for time display in progress dialogs.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setTimeFormat : function(value) {
		/// <summary>
		///   Format for time display in progress dialogs.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getTimeOut : function() {
		/// <summary>
		///   Timeout of the HTTP connection.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setTimeOut : function(value) {
		/// <summary>
		///   Timeout of the HTTP connection.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getTotalFileSize : function() {
		/// <summary>
		///   Total size of all files selected for the upload.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	
	getTreePaneBackgroundColor : function() {
		/// <summary>
		///   Background color of the tree pane.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setTreePaneBackgroundColor : function(value) {
		/// <summary>
		///   Background color of the tree pane.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getTreePaneBorderStyle : function() {
		/// <summary>
		///   Border style of the tree pane.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setTreePaneBorderStyle : function(value) {
		/// <summary>
		///   Border style of the tree pane.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (None - 0, Fixed3D - 1, or FixedSingle - 2).
		/// </param>
	}
	,
	
	
	getTreePaneWidth : function() {
		/// <summary>
		///   Width of the folder tree pane.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setTreePaneWidth : function(value) {
		/// <summary>
		///   Width of the folder tree pane.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUncheckUploadedFiles : function() {
		/// <summary>
		///   Switch that specifies whether to uncheck files (in the two-pane layout) or remove them from the upload list (in the one-pane or three-pane layout) after they are successfully uploaded.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setUncheckUploadedFiles : function(value) {
		/// <summary>
		///   Switch that specifies whether to uncheck files (in the two-pane layout) or remove them from the upload list (in the one-pane or three-pane layout) after they are successfully uploaded.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUnixFileSystemRootText : function() {
		/// <summary>
		///   Caption of the folder tree node which specifies the system directory at *NIX systems.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setUnixFileSystemRootText : function(value) {
		/// <summary>
		///   Caption of the folder tree node which specifies the system directory at *NIX systems.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUnixHomeDirectoryText : function() {
		/// <summary>
		///   Caption of the folder tree node which specifies the home directory at *NIX systems.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setUnixHomeDirectoryText : function(value) {
		/// <summary>
		///   Caption of the folder tree node which specifies the home directory at *NIX systems.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadFileAngle : function(Index) {
		/// <summary>
		///   Rotation angle of the specified item of the upload list.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer which specifies an index of the upload list item (i.e. the upload file). An index is 1-based. In other words valid value is in range [1, ].
		/// </param>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setUploadFileAngle : function(Index, value) {
		/// <summary>
		///   Rotation angle of the specified item of the upload list.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer which specifies an index of the upload list item (i.e. the upload file). An index is 1-based. In other words valid value is in range [1, ].
		/// </param>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadFileCount : function() {
		/// <summary>
		///   Total number of the files in the upload list.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	
	getUploadFileDescription : function(Index) {
		/// <summary>
		///   Description of the specified item of the upload list.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer which specifies an index of the upload list item (i.e. the upload file). An index is 1-based. In other words valid value is in range [1, ].
		/// </param>
		/// <returns type="String"></returns>
	}
	,
	
	setUploadFileDescription : function(Index, value) {
		/// <summary>
		///   Description of the specified item of the upload list.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer which specifies an index of the upload list item (i.e. the upload file). An index is 1-based. In other words valid value is in range [1, ].
		/// </param>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadFileFocused : function(Index) {
		/// <summary>
		///   Indicator that tells whether the specified item in the upload list is in focus.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer which specifies an index of the upload list item (i.e. the upload file). An index is 1-based. In other words valid value is in range [1, ].
		/// </param>
		/// <returns type="Boolean"></returns>
	}
	,
	
	
	getUploadFileFormat : function(Index) {
		/// <summary>
		///   File format of the specified item of the upload list.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer which specifies an index of the upload list item (i.e. the upload file). An index is 1-based. In other words valid value is in range [1, ].
		/// </param>
		/// <returns type="String"></returns>
	}
	,
	
	
	getUploadFileGuid : function(Index) {
		/// <summary>
		///   Unique identifier of the specified item in the upload list.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer which specifies an index of the upload list item (i.e. the upload file). An index is 1-based. In other words valid value is in range [1, ].
		/// </param>
		/// <returns type="String"></returns>
	}
	,
	
	
	getUploadFileHeight : function(Index) {
		/// <summary>
		///   Height of the image represented by the specified item of the upload list.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer which specifies an index of the upload list item (i.e. the upload file). An index is 1-based. In other words valid value is in range [1, ].
		/// </param>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	
	getUploadFileHorizontalResolution : function(Index) {
		/// <summary>
		///   Horizontal resolution of the image represented by the specified item of the upload list.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer which specifies an index of the upload list item (i.e. the upload file). An index is 1-based. In other words valid value is in range [1, ].
		/// </param>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	
	getUploadFileName : function(Index) {
		/// <summary>
		///   File name of the specified item of the upload list.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer which specifies an index of the upload list item (i.e. the upload file). An index is 1-based. In other words valid value is in range [1, ].
		/// </param>
		/// <returns type="String"></returns>
	}
	,
	
	
	getUploadFileSelected : function(Index) {
		/// <summary>
		///   Indicator that tells whether the specified item of the upload list is selected.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer which specifies an index of the upload list item (i.e. the upload file). An index is 1-based. In other words valid value is in range [1, ].
		/// </param>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setUploadFileSelected : function(Index, value) {
		/// <summary>
		///   Indicator that tells whether the specified item of the upload list is selected.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer which specifies an index of the upload list item (i.e. the upload file). An index is 1-based. In other words valid value is in range [1, ].
		/// </param>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadFileSize : function(Index) {
		/// <summary>
		///   File size of the specified item of the upload list.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer which specifies an index of the upload list item (i.e. the upload file). An index is 1-based. In other words valid value is in range [1, ].
		/// </param>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	
	getUploadFileType : function(Index) {
		/// <summary>
		///   File type of the specified item of the upload list.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer which specifies an index of the upload list item (i.e. the upload file). An index is 1-based. In other words valid value is in range [1, ].
		/// </param>
		/// <returns type="String"></returns>
	}
	,
	
	
	getUploadFileVerticalResolution : function(Index) {
		/// <summary>
		///   Vertical resolution of the image represented by the specified item of the upload list.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer which specifies an index of the upload list item (i.e. the upload file). An index is 1-based. In other words valid value is in range [1, ].
		/// </param>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	
	getUploadFileWidth : function(Index) {
		/// <summary>
		///   Width of the image represented by the specified item of the upload list.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer which specifies an index of the upload list item (i.e. the upload file). An index is 1-based. In other words valid value is in range [1, ].
		/// </param>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	
	getUploadPaneAllowRotate : function() {
		/// <summary>
		///   Switch that specifies whether to display rotate icons on thumbnails in the upload pane.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setUploadPaneAllowRotate : function(value) {
		/// <summary>
		///   Switch that specifies whether to display rotate icons on thumbnails in the upload pane.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadPaneBackgroundColor : function() {
		/// <summary>
		///   Background color of the upload pane.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setUploadPaneBackgroundColor : function(value) {
		/// <summary>
		///   Background color of the upload pane.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getUploadPaneBackgroundImageFormat : function() {
		/// <summary>
		///   Background image of the upload pane.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setUploadPaneBackgroundImageFormat : function(value) {
		/// <summary>
		///   Background image of the upload pane.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadPaneBorderStyle : function() {
		/// <summary>
		///   Border style of the upload pane.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setUploadPaneBorderStyle : function(value) {
		/// <summary>
		///   Border style of the upload pane.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (None - 0, Fixed3D - 1, or FixedSingle - 2).
		/// </param>
	}
	,
	
	
	getUploadPaneShowDescriptions : function() {
		/// <summary>
		///   Switch that specifies whether Edit Description elements should be displayed under each thumbnail in the upload list.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setUploadPaneShowDescriptions : function(value) {
		/// <summary>
		///   Switch that specifies whether Edit Description elements should be displayed under each thumbnail in the upload list.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadPaneSortMode : function() {
		/// <summary>
		///   Sort mode of the upload list.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setUploadPaneSortMode : function(value) {
		/// <summary>
		///   Sort mode of the upload list.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (Unsorted - 0, Name - 1, Size - 2, Type - 3, Modified - 4, or Path - 5).
		/// </param>
	}
	,
	
	
	getUploadSourceFile : function() {
		/// <summary>
		///   Switch that specifies whether original file should be uploaded or it is necessary to upload only resized copies.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setUploadSourceFile : function(value) {
		/// <summary>
		///   Switch that specifies whether original file should be uploaded or it is necessary to upload only resized copies.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnail1BackgroundColor : function() {
		/// <summary>
		///   Background color of the first thumbnail.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setUploadThumbnail1BackgroundColor : function(value) {
		/// <summary>
		///   Background color of the first thumbnail.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getUploadThumbnail1CompressOversizedOnly : function() {
		/// <summary>
		///   Switch that specifies whether an original file should be uploaded as the first thumbnail in case when original image dimensions and file size are not greater than dimensions and file size of the first thumbnail.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setUploadThumbnail1CompressOversizedOnly : function(value) {
		/// <summary>
		///   Switch that specifies whether an original file should be uploaded as the first thumbnail in case when original image dimensions and file size are not greater than dimensions and file size of the first thumbnail.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnail1CompressionMode : function() {
		/// <summary>
		///   Compression mode of the first thumbnail.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setUploadThumbnail1CompressionMode : function(value) {
		/// <summary>
		///   Compression mode of the first thumbnail.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnail1CopyExif : function() {
		/// <summary>
		///   Switch that specifies whether the first thumbnail should contain EXIF metadata copied from the original file.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setUploadThumbnail1CopyExif : function(value) {
		/// <summary>
		///   Switch that specifies whether the first thumbnail should contain EXIF metadata copied from the original file.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnail1CopyIptc : function() {
		/// <summary>
		///   Switch that specifies whether the first thumbnail should contain IPTC metadata copied from the original file.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setUploadThumbnail1CopyIptc : function(value) {
		/// <summary>
		///   Switch that specifies whether the first thumbnail should contain IPTC metadata copied from the original file.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnail1FitMode : function() {
		/// <summary>
		///   Fit mode of the first thumbnail.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setUploadThumbnail1FitMode : function(value) {
		/// <summary>
		///   Fit mode of the first thumbnail.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (Off - 0, Fit - 1, Width - 2, Height - 3, Icon - 4, or ActualSize - 5).
		/// </param>
	}
	,
	
	
	getUploadThumbnail1Height : function() {
		/// <summary>
		///   Height restriction of the first thumbnail.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setUploadThumbnail1Height : function(value) {
		/// <summary>
		///   Height restriction of the first thumbnail.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnail1JpegQuality : function() {
		/// <summary>
		///   JPEG quality for the first thumbnail.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setUploadThumbnail1JpegQuality : function(value) {
		/// <summary>
		///   JPEG quality for the first thumbnail.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnail1ResizeQuality : function() {
		/// <summary>
		///   Resize quality of the first thumbnail.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setUploadThumbnail1ResizeQuality : function(value) {
		/// <summary>
		///   Resize quality of the first thumbnail.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (Medium - 0 or High - 1).
		/// </param>
	}
	,
	
	
	getUploadThumbnail1Resolution : function() {
		/// <summary>
		///   Image resolution of the first thumbnail.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setUploadThumbnail1Resolution : function(value) {
		/// <summary>
		///   Image resolution of the first thumbnail.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnail1Watermark : function() {
		/// <summary>
		///   Watermark for the first thumbnail.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setUploadThumbnail1Watermark : function(value) {
		/// <summary>
		///   Watermark for the first thumbnail.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnail1Width : function() {
		/// <summary>
		///   Width restriction of the first thumbnail.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setUploadThumbnail1Width : function(value) {
		/// <summary>
		///   Width restriction of the first thumbnail.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnail2BackgroundColor : function() {
		/// <summary>
		///   Background color of the second thumbnail.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setUploadThumbnail2BackgroundColor : function(value) {
		/// <summary>
		///   Background color of the second thumbnail.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getUploadThumbnail2CompressOversizedOnly : function() {
		/// <summary>
		///   Switch that specifies whether an original file should be uploaded as the second thumbnail in case when original image dimensions and file size are not greater than dimensions and file size of the second thumbnail.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setUploadThumbnail2CompressOversizedOnly : function(value) {
		/// <summary>
		///   Switch that specifies whether an original file should be uploaded as the second thumbnail in case when original image dimensions and file size are not greater than dimensions and file size of the second thumbnail.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnail2CompressionMode : function() {
		/// <summary>
		///   Compression mode of the second thumbnail.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setUploadThumbnail2CompressionMode : function(value) {
		/// <summary>
		///   Compression mode of the second thumbnail.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnail2CopyExif : function() {
		/// <summary>
		///   Switch that specifies whether the second thumbnail should contain EXIF metadata copied from the original file.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setUploadThumbnail2CopyExif : function(value) {
		/// <summary>
		///   Switch that specifies whether the second thumbnail should contain EXIF metadata copied from the original file.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnail2CopyIptc : function() {
		/// <summary>
		///   Switch that specifies whether the second thumbnail should contain IPTC metadata copied from the original file.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setUploadThumbnail2CopyIptc : function(value) {
		/// <summary>
		///   Switch that specifies whether the second thumbnail should contain IPTC metadata copied from the original file.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnail2FitMode : function() {
		/// <summary>
		///   Fit mode of the second thumbnail.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setUploadThumbnail2FitMode : function(value) {
		/// <summary>
		///   Fit mode of the second thumbnail.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (Off - 0, Fit - 1, Width - 2, Height - 3, Icon - 4, or ActualSize - 5).
		/// </param>
	}
	,
	
	
	getUploadThumbnail2Height : function() {
		/// <summary>
		///   Height restriction of the second thumbnail.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setUploadThumbnail2Height : function(value) {
		/// <summary>
		///   Height restriction of the second thumbnail.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnail2JpegQuality : function() {
		/// <summary>
		///   JPEG quality for the second thumbnail.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setUploadThumbnail2JpegQuality : function(value) {
		/// <summary>
		///   JPEG quality for the second thumbnail.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnail2ResizeQuality : function() {
		/// <summary>
		///   Resize quality of the second thumbnail.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setUploadThumbnail2ResizeQuality : function(value) {
		/// <summary>
		///   Resize quality of the second thumbnail.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (Medium - 0 or High - 1).
		/// </param>
	}
	,
	
	
	getUploadThumbnail2Resolution : function() {
		/// <summary>
		///   Image resolution of the second thumbnail.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setUploadThumbnail2Resolution : function(value) {
		/// <summary>
		///   Image resolution of the second thumbnail.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnail2Watermark : function() {
		/// <summary>
		///   Watermark for the second thumbnail.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setUploadThumbnail2Watermark : function(value) {
		/// <summary>
		///   Watermark for the second thumbnail.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnail2Width : function() {
		/// <summary>
		///   Width restriction of the second thumbnail.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setUploadThumbnail2Width : function(value) {
		/// <summary>
		///   Width restriction of the second thumbnail.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnail3BackgroundColor : function() {
		/// <summary>
		///   Background color of the third thumbnail.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setUploadThumbnail3BackgroundColor : function(value) {
		/// <summary>
		///   Background color of the third thumbnail.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getUploadThumbnail3CompressOversizedOnly : function() {
		/// <summary>
		///   Switch that specifies whether an original file should be uploaded as the third thumbnail in case when original image dimensions and file size are not greater than dimensions and file size of the third thumbnail.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setUploadThumbnail3CompressOversizedOnly : function(value) {
		/// <summary>
		///   Switch that specifies whether an original file should be uploaded as the third thumbnail in case when original image dimensions and file size are not greater than dimensions and file size of the third thumbnail.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnail3CompressionMode : function() {
		/// <summary>
		///   Compression mode of the third thumbnail.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setUploadThumbnail3CompressionMode : function(value) {
		/// <summary>
		///   Compression mode of the third thumbnail.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnail3CopyExif : function() {
		/// <summary>
		///   Switch that specifies whether the third thumbnail should contain EXIF metadata copied from the original file.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setUploadThumbnail3CopyExif : function(value) {
		/// <summary>
		///   Switch that specifies whether the third thumbnail should contain EXIF metadata copied from the original file.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnail3CopyIptc : function() {
		/// <summary>
		///   Switch that specifies whether the third thumbnail should contain IPTC metadata copied from the original file.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setUploadThumbnail3CopyIptc : function(value) {
		/// <summary>
		///   Switch that specifies whether the third thumbnail should contain IPTC metadata copied from the original file.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnail3FitMode : function() {
		/// <summary>
		///   Fit mode of the third thumbnail.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setUploadThumbnail3FitMode : function(value) {
		/// <summary>
		///   Fit mode of the third thumbnail.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (Off - 0, Fit - 1, Width - 2, Height - 3, Icon - 4, or ActualSize - 5).
		/// </param>
	}
	,
	
	
	getUploadThumbnail3Height : function() {
		/// <summary>
		///   Height restriction of the third thumbnail.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setUploadThumbnail3Height : function(value) {
		/// <summary>
		///   Height restriction of the third thumbnail.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnail3JpegQuality : function() {
		/// <summary>
		///   JPEG quality for the third thumbnail.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setUploadThumbnail3JpegQuality : function(value) {
		/// <summary>
		///   JPEG quality for the third thumbnail.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnail3ResizeQuality : function() {
		/// <summary>
		///   Resize quality of the third thumbnail.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setUploadThumbnail3ResizeQuality : function(value) {
		/// <summary>
		///   Resize quality of the third thumbnail.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (Medium - 0 or High - 1).
		/// </param>
	}
	,
	
	
	getUploadThumbnail3Resolution : function() {
		/// <summary>
		///   Image resolution of the third thumbnail.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setUploadThumbnail3Resolution : function(value) {
		/// <summary>
		///   Image resolution of the third thumbnail.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnail3Watermark : function() {
		/// <summary>
		///   Watermark for the third thumbnail.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setUploadThumbnail3Watermark : function(value) {
		/// <summary>
		///   Watermark for the third thumbnail.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnail3Width : function() {
		/// <summary>
		///   Width restriction of the third thumbnail.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setUploadThumbnail3Width : function(value) {
		/// <summary>
		///   Width restriction of the third thumbnail.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnailActualCount : function() {
		/// <summary>
		///   Actual number of thumbnails which will be sent to the server.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	
	getUploadThumbnailBackgroundColor : function(Index) {
		/// <summary>
		///   Background color of the specified upload thumbnail.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer that specifies an index of the upload thumbnail (one-based). It should not exceed	. If extra upload thumbnails are required,	use the method to add them.
		/// </param>
		/// <returns type="String"></returns>
	}
	,
	
	setUploadThumbnailBackgroundColor : function(Index, value) {
		/// <summary>
		///   Background color of the specified upload thumbnail.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer that specifies an index of the upload thumbnail (one-based). It should not exceed	. If extra upload thumbnails are required,	use the method to add them.
		/// </param>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getUploadThumbnailCompressOversizedOnly : function(Index) {
		/// <summary>
		///   Switch that specifies whether an original file should be uploaded as the specified thumbnail in case when original image dimensions and file size are not greater than dimensions and file size of the thumbnail.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer that specifies an index of the upload thumbnail (one-based). It should not exceed	. If extra upload thumbnails are required,	use the method to add them.
		/// </param>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setUploadThumbnailCompressOversizedOnly : function(Index, value) {
		/// <summary>
		///   Switch that specifies whether an original file should be uploaded as the specified thumbnail in case when original image dimensions and file size are not greater than dimensions and file size of the thumbnail.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer that specifies an index of the upload thumbnail (one-based). It should not exceed	. If extra upload thumbnails are required,	use the method to add them.
		/// </param>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnailCompressionMode : function(Index) {
		/// <summary>
		///   Compression mode of the specified thumbnail.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer that specifies an index of the upload thumbnail (one-based). It should not exceed	. If extra upload thumbnails are required,	use the method to add them.
		/// </param>
		/// <returns type="String"></returns>
	}
	,
	
	setUploadThumbnailCompressionMode : function(Index, value) {
		/// <summary>
		///   Compression mode of the specified thumbnail.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer that specifies an index of the upload thumbnail (one-based). It should not exceed	. If extra upload thumbnails are required,	use the method to add them.
		/// </param>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnailCopyExif : function(Index) {
		/// <summary>
		///   Switch that specifies whether the EXIF information should be copied from original file to the specified upload thumbnail.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer that specifies an index of the upload thumbnail (one-based). It should not exceed	. If extra upload thumbnails are required,	use the method to add them.
		/// </param>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setUploadThumbnailCopyExif : function(Index, value) {
		/// <summary>
		///   Switch that specifies whether the EXIF information should be copied from original file to the specified upload thumbnail.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer that specifies an index of the upload thumbnail (one-based). It should not exceed	. If extra upload thumbnails are required,	use the method to add them.
		/// </param>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnailCopyIptc : function(Index) {
		/// <summary>
		///   Switch that specifies whether the IPTC information should be copied from original file to the specified upload thumbnail.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer that specifies an index of the upload thumbnail (one-based). It should not exceed	. If extra upload thumbnails are required,	use the method to add them.
		/// </param>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setUploadThumbnailCopyIptc : function(Index, value) {
		/// <summary>
		///   Switch that specifies whether the IPTC information should be copied from original file to the specified upload thumbnail.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer that specifies an index of the upload thumbnail (one-based). It should not exceed	. If extra upload thumbnails are required,	use the method to add them.
		/// </param>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnailCount : function() {
		/// <summary>
		///   Total number of elements in the list of upload thumbnail settings.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	
	getUploadThumbnailFitMode : function(Index) {
		/// <summary>
		///   Fit mode of the specified upload thumbnail.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer that specifies an index of the upload thumbnail (one-based). It should not exceed	. If extra upload thumbnails are required,	use the method to add them.
		/// </param>
		/// <returns type="String"></returns>
	}
	,
	
	setUploadThumbnailFitMode : function(Index, value) {
		/// <summary>
		///   Fit mode of the specified upload thumbnail.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer that specifies an index of the upload thumbnail (one-based). It should not exceed	. If extra upload thumbnails are required,	use the method to add them.
		/// </param>
		/// <param name="value" type="String">
		///   A value of property (Off - 0, Fit - 1, Width - 2, Height - 3, Icon - 4, or ActualSize - 5).
		/// </param>
	}
	,
	
	
	getUploadThumbnailHeight : function(Index) {
		/// <summary>
		///   Height restriction of the specified upload thumbnail.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer that specifies an index of the upload thumbnail (one-based). It should not exceed	. If extra upload thumbnails are required,	use the method to add them.
		/// </param>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setUploadThumbnailHeight : function(Index, value) {
		/// <summary>
		///   Height restriction of the specified upload thumbnail.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer that specifies an index of the upload thumbnail (one-based). It should not exceed	. If extra upload thumbnails are required,	use the method to add them.
		/// </param>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnailJpegQuality : function(Index) {
		/// <summary>
		///   JPEG quality for the specified upload thumbnail.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer that specifies an index of the upload thumbnail (one-based). It should not exceed	. If extra upload thumbnails are required,	use the method to add them.
		/// </param>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setUploadThumbnailJpegQuality : function(Index, value) {
		/// <summary>
		///   JPEG quality for the specified upload thumbnail.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer that specifies an index of the upload thumbnail (one-based). It should not exceed	. If extra upload thumbnails are required,	use the method to add them.
		/// </param>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnailResizeQuality : function(Index) {
		/// <summary>
		///   Resize quality of thumbnails.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer that specifies an index of the upload thumbnail (one-based). It should not exceed	. If extra upload thumbnails are required,	use the method to add them.
		/// </param>
		/// <returns type="String"></returns>
	}
	,
	
	setUploadThumbnailResizeQuality : function(Index, value) {
		/// <summary>
		///   Resize quality of thumbnails.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer that specifies an index of the upload thumbnail (one-based). It should not exceed	. If extra upload thumbnails are required,	use the method to add them.
		/// </param>
		/// <param name="value" type="String">
		///   A value of property (Medium - 0 or High - 1).
		/// </param>
	}
	,
	
	
	getUploadThumbnailResolution : function(Index) {
		/// <summary>
		///   Image resolution of the specified thumbnail.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer that specifies an index of the upload thumbnail (one-based). It should not exceed	. If extra upload thumbnails are required,	use the method to add them.
		/// </param>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setUploadThumbnailResolution : function(Index, value) {
		/// <summary>
		///   Image resolution of the specified thumbnail.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer that specifies an index of the upload thumbnail (one-based). It should not exceed	. If extra upload thumbnails are required,	use the method to add them.
		/// </param>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnailWatermark : function(Index) {
		/// <summary>
		///   Watermark for the specified thumbnail.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer that specifies an index of the upload thumbnail (one-based). It should not exceed	. If extra upload thumbnails are required,	use the method to add them.
		/// </param>
		/// <returns type="String"></returns>
	}
	,
	
	setUploadThumbnailWatermark : function(Index, value) {
		/// <summary>
		///   Watermark for the specified thumbnail.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer that specifies an index of the upload thumbnail (one-based). It should not exceed	. If extra upload thumbnails are required,	use the method to add them.
		/// </param>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadThumbnailWidth : function(Index) {
		/// <summary>
		///   Width restriction of the specified upload thumbnail.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer that specifies an index of the upload thumbnail (one-based). It should not exceed	. If extra upload thumbnails are required,	use the method to add them.
		/// </param>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setUploadThumbnailWidth : function(Index, value) {
		/// <summary>
		///   Width restriction of the specified upload thumbnail.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer that specifies an index of the upload thumbnail (one-based). It should not exceed	. If extra upload thumbnails are required,	use the method to add them.
		/// </param>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUploadView : function() {
		/// <summary>
		///   View mode for the upload pane.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setUploadView : function(value) {
		/// <summary>
		///   View mode for the upload pane.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getUseSystemColors : function() {
		/// <summary>
		///   Switch that specifies whether Image Uploader should use system-dependent colors for its elements or use color specified via appropriate properties.
		/// </summary>
		/// <returns type="Boolean"></returns>
	}
	,
	
	setUseSystemColors : function(value) {
		/// <summary>
		///   Switch that specifies whether Image Uploader should use system-dependent colors for its elements or use color specified via appropriate properties.
		/// </summary>
		/// <param name="value" type="Boolean">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getVersion : function() {
		/// <summary>
		///   Version number of Image Uploader installed at current machine.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	
	AddCookie : function(Cookie) {
		/// <summary>
		///   Adds specified cookies to the upload request.
		/// </summary>
		/// <param name="Cookie" type="String">
		///   String value that specifies HTTP-only semicolon-separated cookies to attach it to the upload request.
		/// </param>
	}
	,
	
	
	AddField : function(Name, Value) {
		/// <summary>
		///   Adds a POST field with specified name and value to the request submitted to the server.
		/// </summary>
		/// <param name="Name" type="String">
		///   Name of the POST field (an analogue to the name attribute of an &lt;input&gt; HTML element). If you try to add several fields with the same name, the latest one will overwrite the previous value.
		/// </param>
		/// <param name="Value" type="String">
		///   Value of the POST field (an analogue to the value attribute of an &lt;input&gt; HTML element).
		/// </param>
	}
	,
	
	
	AddFiles : function() {
		/// <summary>
		///   Opens the standard Open File dialog so that the user could select files for upload. In other words it works in the same manner as if the Add Files button were clicked.
		/// </summary>
	}
	,
	
	
	AddFolders : function() {
		/// <summary>
		///   Opens the standard Open Folder dialog so that the user can select the folder for the upload. In other words it works in the same manner as if the Add Folders button were clicked.
		/// </summary>
	}
	,
	
	
	CanGoToFolder : function(Folder) {
		/// <summary>
		///   Verifies whether the specified special folder is available.
		/// </summary>
		/// <param name="Folder" type="String">
		///   A member of this enumeration that specifies a special folder to check: BitBucket, Desktop, MyComputer, MyDocuments, MyMusic, MyPictures, MyVideo, Network, Recent.
		/// </param>
		/// <returns type="Boolean">
		///   A boolean value (true or false). If the specified folder is available (and Image Uploader can navigate it), the method returns true, otherwise it returns false. This method works only for special folders. You cannot verify physical path on the local machine with this method.
		/// </returns>
	}
	,
	
	
	DeselectAll : function() {
		/// <summary>
		///   Clears the selection from all files.
		/// </summary>
	}
	,
	
	
	GoToFolder : function(Folder) {
		/// <summary>
		///   Navigates to a specified folder. The parameter can contain either a path to the target folder (for example, C:/Images) or a member of enumeration which specifies a special folder (for example, My Documents, My Pictures, etc.).
		/// </summary>
		/// <param name="Folder" type="String">
		///   A path to the target folder or a member of the following enumeration which specifies a special folder to navigate to: BitBucket, Desktop, MyComputer, MyDocuments, MyMusic, MyPictures, MyVideo, Network, Recent.
		/// </param>
		/// <returns type="Boolean">
		///   A boolean value (true or false) which specifies whether it succeeded (true) or not (false). The method will fail if you specify the given folder does not exist on the computer.
		/// </returns>
	}
	,
	
	
	GoToParentFolder : function() {
		/// <summary>
		///   Navigates to the parent folder.
		/// </summary>
		/// <returns type="Boolean">
		///   A boolean value (true or false) which specifies whether it succeeded (true) or not (false). It will fail if the current folder is the Desktop node of the folder tree (which is always the root node).
		/// </returns>
	}
	,
	
	
	GoToPreviousFolder : function() {
		/// <summary>
		///   Navigates to the previous visited folder.
		/// </summary>
		/// <returns type="Boolean">
		///   A boolean value (true or false) which specifies whether it succeeded (true) or not (false). For example, it will return false if the user did not navigate any folder yet (i.e. the control is just loaded).
		/// </returns>
	}
	,
	
	
	LoadUploadList : function(ID) {
		/// <summary>
		///   Loads the upload list with the specified .
		/// </summary>
		/// <param name="ID" type="Number" integer="true">
		///   Unique integer value to load the upload list with this identifier.
		/// </param>
	}
	,
	
	
	Refresh : function() {
		/// <summary>
		///   Refreshes the folder pane content.
		/// </summary>
	}
	,
	
	
	RemoveAllFromUploadList : function() {
		/// <summary>
		///   Removes all items from the upload list.
		/// </summary>
	}
	,
	
	
	RemoveField : function(Name) {
		/// <summary>
		///   Removes POST field with specified name from the upload.
		/// </summary>
		/// <param name="Name" type="String">
		///   String value that specifies a name of the POST field to remove.
		/// </param>
	}
	,
	
	
	RemoveFromUploadList : function() {
		/// <summary>
		///   Removes selected items from the upload list.
		/// </summary>
	}
	,
	
	
	RenameField : function(OldName, NewName) {
		/// <summary>
		///   Renames the specified standard POST field to the new name.
		/// </summary>
		/// <param name="OldName" type="String">
		///   String value that specifies a name of the standard POST field to rename.
		/// </param>
		/// <param name="NewName" type="String">
		///   String value that specifies a new field name.
		/// </param>
	}
	,
	
	
	ResetUploadList : function(ID) {
		/// <summary>
		///   Resets the upload list with the specified .
		/// </summary>
		/// <param name="ID" type="Number" integer="true">
		///   Unique integer value to reset the upload list with this identifier.
		/// </param>
	}
	,
	
	
	SaveUploadList : function(ID) {
		/// <summary>
		///   Saves the upload list with the specified .
		/// </summary>
		/// <param name="ID" type="Number" integer="true">
		///   Unique integer value to save the current upload list with this identifier.
		/// </param>
	}
	,
	
	
	SelectAll : function() {
		/// <summary>
		///   Selects all the files in the folder pane.
		/// </summary>
	}
	,
	
	
	Send : function() {
		/// <summary>
		///   Starts the upload process.
		/// </summary>
	}
	,
	
	
	Stop : function() {
		/// <summary>
		///   Stops the upload process.
		/// </summary>
	}
	,
	
	
	UploadFileRemove : function(Index) {
		/// <summary>
		///   Removes the specified item from the upload list.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer which specifies an index of the upload list item (i.e. the upload file). An index is 1-based. In other words valid value is in range [1, ].
		/// </param>
	}
	,
	
	
	UploadThumbnailAdd : function(FitMode, Width, Height) {
		/// <summary>
		///   Adds new element to the list of upload thumbnails.
		/// </summary>
		/// <param name="FitMode" type="String">
		///   A member of the enumeration that specifies a fit mode of new upload thumbnail: Off - 0, Fit - 1, Width - 2, Height - 3, Icon - 4, or ActualSize - 5.
		/// </param>
		/// <param name="Width" type="Number" integer="true">
		///   A non-negative integer value that specifies a width of new upload thumbnail.
		/// </param>
		/// <param name="Height" type="Number" integer="true">
		///   A non-negative integer value that specifies a height of new upload thumbnail.
		/// </param>
		/// <returns type="Number" integer="true">
		///   An integer value that specifies an index of newly added item.
		/// </returns>
	}
	,
	
	
	UploadThumbnailRemove : function(Index) {
		/// <summary>
		///   Removes an element from upload thumbnail settings list.
		/// </summary>
		/// <param name="Index" type="Number" integer="true">
		///   A positive integer that specifies an index of the upload thumbnail (one-based) to remove. It should not exceed .
		/// </param>
	}
}

ImageUploader._params = {"Action" : 1, "AddFolderDialogButtonCancelText" : 1, "AddFolderDialogButtonSkipAllText" : 1, "AddFolderDialogButtonSkipText" : 1, "AddFolderDialogTitleText" : 1, "AdditionalFormName" : 1, "AdvancedDetailsFailedItemBackgroundColor" : 1, "AdvancedDetailsGridLineColor" : 1, "AdvancedDetailsGridLineStyle" : 1, "AdvancedDetailsIdleItemBackgroundColor" : 1, "AdvancedDetailsInProcessItemBackgroundColor" : 1, "AdvancedDetailsPreviewThumbnailSize" : 1, "AllowAutoRotate" : 1, "AllowCmykImages" : 1, "AllowDisproportionalExifThumbnails" : 1, "AllowFolderUpload" : 1, "AllowLargePreview" : 1, "AllowMultipleRemove" : 1, "AllowMultipleRotate" : 1, "AllowMultipleSelection" : 1, "AllowRotate" : 1, "AllowTreePaneWidthChange" : 1, "AuthenticationRequestBasicText" : 1, "AuthenticationRequestButtonCancelText" : 1, "AuthenticationRequestButtonOkText" : 1, "AuthenticationRequestDomainText" : 1, "AuthenticationRequestLoginText" : 1, "AuthenticationRequestNtlmText" : 1, "AuthenticationRequestPasswordText" : 1, "AuthenticationType" : 1, "AutoRecoverMaxTriesCount" : 1, "AutoRecoverTimeOut" : 1, "BackgroundColor" : 1, "ButtonAddAllToUploadListImageFormat" : 1, "ButtonAddAllToUploadListText" : 1, "ButtonAddFilesImageFormat" : 1, "ButtonAddFilesText" : 1, "ButtonAddFoldersImageFormat" : 1, "ButtonAddFoldersText" : 1, "ButtonAddToUploadListImageFormat" : 1, "ButtonAddToUploadListText" : 1, "ButtonAdvancedDetailsCancelImageFormat" : 1, "ButtonAdvancedDetailsCancelText" : 1, "ButtonDeleteFilesImageFormat" : 1, "ButtonDeleteFilesText" : 1, "ButtonDeselectAllImageFormat" : 1, "ButtonDeselectAllText" : 1, "ButtonFont" : 1, "ButtonPasteImageFormat" : 1, "ButtonPasteText" : 1, "ButtonRemoveAllFromUploadListImageFormat" : 1, "ButtonRemoveAllFromUploadListText" : 1, "ButtonRemoveFromUploadListImageFormat" : 1, "ButtonRemoveFromUploadListText" : 1, "ButtonSelectAllImageFormat" : 1, "ButtonSelectAllText" : 1, "ButtonSendImageFormat" : 1, "ButtonSendText" : 1, "ButtonStopImageFormat" : 1, "ButtonStopText" : 1, "CacheGuiGraphics" : 1, "Charset" : 1, "CheckFilesBySelectAllButton" : 1, "CmykImagesAreNotAllowedText" : 1, "DeleteFilesDialogTitleText" : 1, "DeleteSelectedFilesDialogMessageText" : 1, "DeleteUploadedFiles" : 1, "DeleteUploadedFilesDialogMessageText" : 1, "DeniedFileMask" : 1, "DescriptionEditorButtonCancelText" : 1, "DescriptionEditorButtonOkText" : 1, "DescriptionTooltipText" : 1, "DescriptionsReadOnly" : 1, "DimensionsAreTooLargeText" : 1, "DimensionsAreTooSmallText" : 1, "DisplayNameActiveSelectedTextColor" : 1, "DisplayNameActiveUnselectedTextColor" : 1, "DisplayNameInactiveSelectedTextColor" : 1, "DisplayNameInactiveUnselectedTextColor" : 1, "DropFilesHereImageFormat" : 1, "DropFilesHereText" : 1, "EditDescriptionText" : 1, "EditDescriptionTextColor" : 1, "EditDescriptionTooltipText" : 1, "EnableFileViewer" : 1, "EnableInstantUpload" : 1, "EnableRemoveIcon" : 1, "ErrorDeletingFilesDialogMessageText" : 1, "ExtractExif" : 1, "ExtractIptc" : 1, "FileIsTooLargeText" : 1, "FileIsTooSmallText" : 1, "FileMask" : 1, "FilesPerOnePackageCount" : 1, "FolderPaneAllowRotate" : 1, "FolderPaneBackgroundColor" : 1, "FolderPaneBorderStyle" : 1, "FolderPaneHeight" : 1, "FolderPaneShowDescriptions" : 1, "FolderPaneSortMode" : 1, "FolderView" : 1, "GuiGraphicsVersion" : 1, "HashAlgorithm" : 1, "HoursText" : 1, "IncludeSubfolders" : 1, "IncludeSubfoldersText" : 1, "IncorrectFileActiveSelectedTextColor" : 1, "IncorrectFileActiveUnselectedTextColor" : 1, "IncorrectFileInactiveSelectedTextColor" : 1, "IncorrectFileInactiveUnselectedTextColor" : 1, "KilobytesText" : 1, "LargePreviewBackgroundColor" : 1, "LargePreviewGeneratingPreviewText" : 1, "LargePreviewHeight" : 1, "LargePreviewIconImageFormat" : 1, "LargePreviewIconTooltipText" : 1, "LargePreviewNoPreviewAvailableText" : 1, "LargePreviewWidth" : 1, "LicenseKey" : 1, "ListColumnFileNameText" : 1, "ListColumnFileSizeText" : 1, "ListColumnFileTypeText" : 1, "ListColumnLastModifiedText" : 1, "ListKilobytesText" : 1, "LoadingFilesText" : 1, "LookAndFeel" : 1, "MaxConnectionCount" : 1, "MaxDescriptionTextLength" : 1, "MaxFileCount" : 1, "MaxFileSize" : 1, "MaxImageHeight" : 1, "MaxImageWidth" : 1, "MaxTotalFileSize" : 1, "MegabytesText" : 1, "MenuAddAllToUploadListText" : 1, "MenuAddToUploadListText" : 1, "MenuArrangeByModifiedText" : 1, "MenuArrangeByNameText" : 1, "MenuArrangeByPathText" : 1, "MenuArrangeBySizeText" : 1, "MenuArrangeByText" : 1, "MenuArrangeByTypeText" : 1, "MenuArrangeByUnsortedText" : 1, "MenuDeselectAllText" : 1, "MenuDetailsText" : 1, "MenuIconsText" : 1, "MenuInvertSelectionText" : 1, "MenuListText" : 1, "MenuRefreshText" : 1, "MenuRemoveAllFromUploadListText" : 1, "MenuRemoveFromUploadListText" : 1, "MenuSelectAllText" : 1, "MenuThumbnailsText" : 1, "MessageBoxTitleText" : 1, "MessageCannotConnectToInternetText" : 1, "MessageCmykImagesAreNotAllowedText" : 1, "MessageDimensionsAreTooLargeText" : 1, "MessageDimensionsAreTooSmallText" : 1, "MessageFileSizeIsTooSmallText" : 1, "MessageMaxFileCountExceededText" : 1, "MessageMaxFileSizeExceededText" : 1, "MessageMaxTotalFileSizeExceededText" : 1, "MessageNoInternetSessionWasEstablishedText" : 1, "MessageNoResponseFromServerText" : 1, "MessageRetryOpenFolderText" : 1, "MessageServerNotFoundText" : 1, "MessageSwitchAnotherFolderWarningText" : 1, "MessageUnexpectedErrorText" : 1, "MessageUploadCancelledText" : 1, "MessageUploadCompleteText" : 1, "MessageUploadFailedText" : 1, "MessageUserSpecifiedTimeoutHasExpiredText" : 1, "MetaDataSeparator" : 1, "MinFileCount" : 1, "MinFileSize" : 1, "MinImageHeight" : 1, "MinImageWidth" : 1, "MinutesText" : 1, "PaneBackgroundColor" : 1, "PaneFont" : 1, "PaneLayout" : 1, "PasteFileNameTemplate" : 1, "PostFormat" : 1, "PreviewThumbnailActiveDropHighlightedColor" : 1, "PreviewThumbnailActiveSelectionColor" : 1, "PreviewThumbnailBorderColor" : 1, "PreviewThumbnailBorderHoverColor" : 1, "PreviewThumbnailInactiveDropHighlightedColor" : 1, "PreviewThumbnailInactiveSelectionColor" : 1, "PreviewThumbnailResizeQuality" : 1, "PreviewThumbnailSize" : 1, "ProgressDialogCancelButtonText" : 1, "ProgressDialogCloseButtonText" : 1, "ProgressDialogCloseWhenUploadCompletesText" : 1, "ProgressDialogEstimatedTimeText" : 1, "ProgressDialogPreparingDataText" : 1, "ProgressDialogPreviewThumbnailSize" : 1, "ProgressDialogSentText" : 1, "ProgressDialogTitleText" : 1, "ProgressDialogWaitingForResponseFromServerText" : 1, "ProgressDialogWaitingForRetryText" : 1, "ProgressDialogWidth" : 1, "QualityMeterAcceptableQualityColor" : 1, "QualityMeterBackgroundColor" : 1, "QualityMeterBorderColor" : 1, "QualityMeterFormats" : 1, "QualityMeterHeight" : 1, "QualityMeterHighQualityColor" : 1, "QualityMeterLowQualityColor" : 1, "RedirectUrl" : 1, "RememberLastVisitedFolder" : 1, "RemoveIconImageFormat" : 1, "RemoveIconTooltipText" : 1, "RotateIconClockwiseImageFormat" : 1, "RotateIconClockwiseTooltipText" : 1, "RotateIconCounterclockwiseImageFormat" : 1, "RotateIconCounterclockwiseTooltipText" : 1, "SecondsText" : 1, "SendDefaultFields" : 1, "ShowButtons" : 1, "ShowContextMenu" : 1, "ShowDebugWindow" : 1, "ShowDescriptions" : 1, "ShowFileNames" : 1, "ShowSubfolders" : 1, "ShowUploadListButtons" : 1, "SilentMode" : 1, "SplitterLineColor" : 1, "SplitterLineStyle" : 1, "ThumbnailHorizontalSpacing" : 1, "ThumbnailVerticalSpacing" : 1, "TimeFormat" : 1, "TimeOut" : 1, "TotalFileSize" : 1, "TreePaneBackgroundColor" : 1, "TreePaneBorderStyle" : 1, "TreePaneWidth" : 1, "UncheckUploadedFiles" : 1, "UnixFileSystemRootText" : 1, "UnixHomeDirectoryText" : 1, "UploadPaneAllowRotate" : 1, "UploadPaneBackgroundColor" : 1, "UploadPaneBackgroundImageFormat" : 1, "UploadPaneBorderStyle" : 1, "UploadPaneShowDescriptions" : 1, "UploadPaneSortMode" : 1, "UploadSourceFile" : 1, "UploadThumbnail1BackgroundColor" : 1, "UploadThumbnail1CompressOversizedOnly" : 1, "UploadThumbnail1CompressionMode" : 1, "UploadThumbnail1CopyExif" : 1, "UploadThumbnail1CopyIptc" : 1, "UploadThumbnail1FitMode" : 1, "UploadThumbnail1Height" : 1, "UploadThumbnail1JpegQuality" : 1, "UploadThumbnail1ResizeQuality" : 1, "UploadThumbnail1Resolution" : 1, "UploadThumbnail1Watermark" : 1, "UploadThumbnail1Width" : 1, "UploadThumbnail2BackgroundColor" : 1, "UploadThumbnail2CompressOversizedOnly" : 1, "UploadThumbnail2CompressionMode" : 1, "UploadThumbnail2CopyExif" : 1, "UploadThumbnail2CopyIptc" : 1, "UploadThumbnail2FitMode" : 1, "UploadThumbnail2Height" : 1, "UploadThumbnail2JpegQuality" : 1, "UploadThumbnail2ResizeQuality" : 1, "UploadThumbnail2Resolution" : 1, "UploadThumbnail2Watermark" : 1, "UploadThumbnail2Width" : 1, "UploadThumbnail3BackgroundColor" : 1, "UploadThumbnail3CompressOversizedOnly" : 1, "UploadThumbnail3CompressionMode" : 1, "UploadThumbnail3CopyExif" : 1, "UploadThumbnail3CopyIptc" : 1, "UploadThumbnail3FitMode" : 1, "UploadThumbnail3Height" : 1, "UploadThumbnail3JpegQuality" : 1, "UploadThumbnail3ResizeQuality" : 1, "UploadThumbnail3Resolution" : 1, "UploadThumbnail3Watermark" : 1, "UploadThumbnail3Width" : 1, "UploadView" : 1, "UseSystemColors" : 1};


function ShellComboBox() {
	/// <summary>
	///   Dropdown list with folder tree.
	///   Don't create the instance of this class explicitly. You should use ShellComboBoxWriter class instead.
	/// </summary>
}

ShellComboBox.prototype = {
	
	
	getBackgroundColor : function() {
		/// <summary>
		///   Background color of the shell combobox.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setBackgroundColor : function(value) {
		/// <summary>
		///   Background color of the shell combobox.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getParentControlName : function() {
		/// <summary>
		///   Name of the control instance this combo box is associated with.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setParentControlName : function(value) {
		/// <summary>
		///   Name of the control instance this combo box is associated with.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}

}

ShellComboBox._params = {"BackgroundColor" : 1, "ParentControlName" : 1};


function Thumbnail() {
	/// <summary>
	///   Standalone thumbnail bound with some upload list item of Image Uploader.
	///   Don't create the instance of this class explicitly. You should use ThumbnailWriter class instead.
	/// </summary>
}

Thumbnail.prototype = {
	
	
	getBackgroundColor : function() {
		/// <summary>
		///   Background color of this instance.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setBackgroundColor : function(value) {
		/// <summary>
		///   Background color of this instance.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property (hex representation of the RGB triad in HTML-style syntax (#rrggbb) or color name ("white" and so on).
		/// </param>
	}
	,
	
	
	getGuid : function() {
		/// <summary>
		///   Identifier (GUID) of the item which is represented with this control.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setGuid : function(value) {
		/// <summary>
		///   Identifier (GUID) of the item which is represented with this control.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getIndex : function() {
		/// <summary>
		///   Index of the upload list item which is represented with this control.
		/// </summary>
		/// <returns type="Number" integer="true"></returns>
	}
	,
	
	setIndex : function(value) {
		/// <summary>
		///   Index of the upload list item which is represented with this control.
		/// </summary>
		/// <param name="value" type="Number" integer="true">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getParentControlName : function() {
		/// <summary>
		///   Name of the control instance this thumbnail is associated with.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setParentControlName : function(value) {
		/// <summary>
		///   Name of the control instance this thumbnail is associated with.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}
	,
	
	
	getView : function() {
		/// <summary>
		///   View mode of this instance.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setView : function(value) {
		/// <summary>
		///   View mode of this instance.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}

}

Thumbnail._params = {"BackgroundColor" : 1, "Guid" : 1, "ParentControlName" : 1, "View" : 1};


function UploadPane() {
	/// <summary>
	///   Standalone upload pane. 
	///   Don't create the instance of this class explicitly. You should use UploadPaneWriter class instead.
	/// </summary>
}

UploadPane.prototype = {
	
	
	getParentControlName : function() {
		/// <summary>
		///   Name of the control instance this upload pane is associated with.
		/// </summary>
		/// <returns type="String"></returns>
	}
	,
	
	setParentControlName : function(value) {
		/// <summary>
		///   Name of the control instance this upload pane is associated with.
		/// </summary>
		/// <param name="value" type="String">
		///   A value of property.
		/// </param>
	}

}

UploadPane._params = {"ParentControlName" : 1};


