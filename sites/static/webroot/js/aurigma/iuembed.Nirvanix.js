// Aurigma Image Uploader Dual 6.x - IUEmbed Script Library
// Copyright(c) Aurigma Inc. 2002-2009
// Version 6.1.4.0

/// <reference path="iuembed.js" />
/// <reference path="iuembed.Intellisense.js" />

//--------------------------------------------------------------------------
//NirvanixExtender class
//--------------------------------------------------------------------------

function NirvanixExtender(writer) {
	/// <summary>Extends Image Uploader with direct upload to Nirvanix storage.</summary>
	/// <param name="writer" type="ImageUploaderWriter">An instance of ImageUploaderWriter object.</param>
	
	BaseExtender.call(this, writer);
	
	this._uploadToken = "";
	this._uploadHost = "";
	this._destFolderPath = "";
	this._serviceError = null;
	
	this._writer.addEventListener("BeforeUpload", IUCommon.createDelegate(this, this._control$BeforeUpload));
	this._writer.addEventListener("PackageBeforeUpload", IUCommon.createDelegate(this, this._control$PackageBeforeUpload), true);
	this._writer.addEventListener("PackageComplete", IUCommon.createDelegate(this, this._control$PackageComplete), true);

	this._errors = {"100" : "MissingRequiredParameter", "101" : "InvalidParameter"
				, "160" : "UnsupportedOperation", "50103" : "ParameterOutOfAcceptedRange"
				, "55001" : "InvalidPayment", "55100" : "CreditCardChargeFailed"
				, "65001" : "CreateAccountFailed", "65002" : "CreateAccountContactFailed"
				, "65003" : "InvalidSlaError", "65004" : "InvalidAppKey"
				, "65005" : "InvalidAccountType", "65006" : "DuplicateUserNameUnderAppKey"
				, "65007" : "DuplicateAppName", "65008" : "PageSizeExceedsLimit"
				, "65009" : "DeletedAccount", "65010" : "InvalidStatus"
				, "65011" : "InvalidSecurityResponse", "65012" : "InvalidUserNameOrPassword"
				, "65016" : "DuplicateMasterAccountUserName", "65101" : "InvalidContactParameter"
				, "65102" : "AppNameNotFound", "65104" : "UserNameNotFound"
				, "70001" : "FolderNotFound", "70002" : "FileNotFound"
				, "70003" : "PathNotFound", "70004" : "AlreadyExists"
				, "70005" : "InvalidPath", "70006" : "DownloadPathNotFound"
				, "70007" : "MetadataDoesNotExist", "70008" : "FolderAlreadyExists"
				, "70009" : "PathTooLong", "70010" : "FileFolderNameTooLong"
				, "70101" : "NullOrEmptyPath", "70102" : "InvalidPathCharacter"
				, "70103" : "InvalidAbsolutePath", "70104" : "InvalidRelativePath"
				, "70106" : "FileIntegrityError", "70107" : "InvalidMetadata"
				, "70108" : "RangeNotSatisfiable", "70109" : "PathTooShort"
				, "70110" : "FileOffline", "70111" : "InvalidImageDimensions"
				, "80001" : "LoginFailed", "80003" : "AccessDenied"
				, "80004" : "InvalidMasterAccountID", "80005" : "InvalidBillableAccountID"
				, "80006" : "SessionNotFound", "80007" : "AccountExpired"
				, "80015" : "OutOfBytes", "80016" : "OutOfBytesNonOwner"
				, "80018" : "InvalidIPAddress", "80019" : "DownloadFileSizeLimitExceeded"
				, "80020" : "UploadBandwidthLimitExceeded", "80021" : "StorageLimitExceeded"
				, "80022" : "LimitAlreadyExceeded", "80023" : "InvalidAccessToken"
				, "80101" : "InvalidSessionToken", "80102" : "ExpiredAccessToken"
				, "80103" : "InvalidDownloadToken", "90001" : "Configuration"
				, "90002" : "SSLRequired", "100001" : "Database"};
}

NirvanixExtender.prototype = new BaseExtender;
NirvanixExtender.prototype.constructor = NirvanixExtender;		

NirvanixExtender.prototype._control$BeforeUpload = function() {
	if (this._uploadToken == "" || this._uploadHost == "" || this._destFolderPath == "") {
		IUCommon.showWarning("You should specify UploadToken, UploadHost, and DestFolderPath for using NirvanixExtender", 1);
		return false;
	}
}

NirvanixExtender.prototype._control$PackageBeforeUpload = function(PackageIndex) {
	var iu = getImageUploader(this._writer.id);

	iu.setAction(window.location.protocol +  "//" + this._uploadHost + "/Upload.ashx");
		
	iu.AddField("uploadToken", this._uploadToken);
	iu.AddField("destFolderPath", this._destFolderPath);	
}

NirvanixExtender.prototype._control$PackageComplete = function(PackageIndex, ResponsePage) {
	var rp = new String(ResponsePage);
	var re = /<ResponseCode>\s*(\d+)\s*<\/ResponseCode>/ig;
	
	if (re.test(rp)) {
		var responseCode= new Number(RegExp.$1);
						
		if (responseCode != 0) {
			if (this._serviceError != null && this._serviceError(responseCode)) {
				return false;
			}
			else {
				var d = this._errors[responseCode];
				if (d == undefined) {
					d = "Unknown"
				}
			
				IUCommon.showWarning("Error occured (#" + responseCode + ", " + d + ") during an upload to Nirvanix service.", 1);	
				return true;
			}
		}
	}
	else {
		IUCommon.showWarning("Unexpected response from Nirvanix service.", 1);
		return true;
	}
}

//Override BaseExtender methods

NirvanixExtender.prototype._beforeRender = function() {	
	if (this._writer.getParam("Action")) {
		this._writer.removeParam("Action");
		IUCommon.showWarning("Don't specify Action param directly (addParam(\"Action\", ...)) "
			+ "when using NirvanixExtender class. NirvanixExtender configures these settings automatically.", 1);
	}
	
	this._writer.addParam("Action", "http://localhost/");
}

//Public

//UploadToken property
NirvanixExtender.prototype.getUploadToken = function() {
	/// <summary>An access token to pass into one of the upload methods when performing an upload.</summary>
	/// <returns type="String"></returns>

	return this._uploadToken;
}

NirvanixExtender.prototype.setUploadToken = function(value) {
	/// <summary>An access token to pass into one of the upload methods when performing an upload.</summary>
	/// <param name="value" type="String">A value of property.</param>
		
	this._uploadToken = value;
}

//UploadHost property
NirvanixExtender.prototype.getUploadHost = function() {
	/// <summary>The server to upload to.</summary>
	/// <returns type="String"></returns>

	return this._uploadHost;
}

NirvanixExtender.prototype.setUploadHost = function(value) {
	/// <summary>The server to upload to.</summary>
	/// <param name="value" type="String">A value of property.</param>
		
	this._uploadHost = value;
}

//DestFolderPath property
NirvanixExtender.prototype.getDestFolderPath = function() {
	/// <summary>The destination path of the files being uploaded.</summary>
	/// <returns type="String"></returns>

	return this._destFolderPath;
}

NirvanixExtender.prototype.setDestFolderPath = function(value) {
	/// <summary>The destination path of the files being uploaded.</summary>
	/// <param name="value" type="String">A value of property.</param>
		
	this._destFolderPath = value;
}


//ServiceError event
NirvanixExtender.prototype.addServiceError = function(listener) {
	/// <summary>Raised if some error occurred during upload to Nirvanix service.</summary>
	/// <param name="listener" type="Function">An event listener.</param>
	
	if (this._serviceError != null) {
		IUCommon.showWarning("You can attach only one event listener on NirvanixExtender.ServiceError event.", 1);
	}
	else {
		this._serviceError = listener;
	}
}

NirvanixExtender.prototype.removeServiceError = function(listener) {
	/// <summary>Raised if some error occurred during upload to Nirvanix service.</summary>
	/// <param name="listener" type="Function">An event listener.</param>
		
	if (this._serviceError == listener) {
		this._serviceError = null;
	}
}

