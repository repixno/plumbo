// Aurigma Image Uploader Dual 6.x - IUEmbed Script Library
// Copyright(c) Aurigma Inc. 2002-2009
// Version 6.1.4.0

/// <reference path="iuembed.js" />
/// <reference path="iuembed.Intellisense.js" />

//--------------------------------------------------------------------------
//AmazonS3Extender class
//--------------------------------------------------------------------------

function AmazonS3Extender(writer) {
	/// <summary>Extends Image Uploader with a direct upload to Amazon S3 storage.</summary>
	/// <param name="writer" type="ImageUploaderWriter">An instance of ImageUploaderWriter object.</param>

	BaseExtender.call(this, writer);
	
	this._AWSAccessKeyId = "";
	this._bucket = "";
	this._bucketHostName = "";	

	this._writer.addEventListener("BeforeUpload", IUCommon.createDelegate(this, this._control$BeforeUpload), true);
	this._writer.addEventListener("Error", IUCommon.createDelegate(this, this._control$Error), true);

	this._sourceFile = new AmazonS3Extender.FileSettings(this, "SourceFile");
	this._thumbnail1 = new AmazonS3Extender.FileSettings(this, "Thumbnail1");
	this._thumbnail2 = new AmazonS3Extender.FileSettings(this, "Thumbnail2");
	this._thumbnail3 = new AmazonS3Extender.FileSettings(this, "Thumbnail3");
	
	this._postFields = ["FileCount", "PackageIndex"
		, "PackageCount", "PackageGuid"
		, "Description_[ItemIndex]", "Width_[ItemIndex]"
		, "Height_[ItemIndex]", "Angle_[ItemIndex]"
		, "HorizontalResolution_[ItemIndex]", "VerticalResolution_[ItemIndex]"
		, "SourceFileSize_[ItemIndex]", "SourceFileCreatedDateTime_[ItemIndex]"
		, "SourceFileLastModifiedDateTime_[ItemIndex]", "SourceFileCreatedDateTimeLocal_[ItemIndex]"
		, "SourceFileLastModifiedDateTimeLocal_[ItemIndex]", "FileName_[ItemIndex]"
		, "UploadFile[ThumbnailIndex]CompressionMode_[ItemIndex]", "Thumbnail[ThumbnailIndex]FileSize_[ItemIndex]"
		, "Thumbnail[ThumbnailIndex]Width_[ItemIndex]", "Thumbnail[ThumbnailIndex]Height_[ItemIndex]"
		, "Thumbnail[ThumbnailIndex]Succeeded_[ItemIndex]"];

	this._postFieldsHash = {};
	
	this._postFieldsAdded = {};	
		
	for (var i = 0; i < this._postFields.length; i++) {
		this._postFieldsHash[this._postFields[i]] = 1;
	}
}

AmazonS3Extender.prototype = new BaseExtender;
AmazonS3Extender.prototype.constructor = AmazonS3Extender;

//Event listeners

AmazonS3Extender.prototype._validateFile = function(settings) {
	
	if (settings.getPolicy() == "" || settings.getKey() == ""
		|| settings.getAcl() == "" || settings.getSignature() == "") {		
		alert("You have selected to upload " +  settings._name + " In this case you should set "
			+ "the policy, key, ACL, and signature using the following methods:\n\r\n\r"
			+ "as3.get" + settings._name + "().setPolicy(...);\n\r"
			+ "as3.get" + settings._name + "().setKey(...);\n\r"
			+ "as3.get" + settings._name + "().setAcl(...);\n\r"
			+ "as3.get" + settings._name + "().setSignature(...);\n\r");
		return false;
	}
	
	this._currentSettings = settings;	
	
	return true;
}

AmazonS3Extender.prototype._control$BeforeUpload = function() {	
	if (this._AWSAccessKeyId == "") {
		IUCommon.showWarning("You should specify AWSAccessKeyId for using AmazonS3Extender.", 1);	
		return false;
	}
	
	if (this._bucket == "") {
		IUCommon.showWarning("You should specify a bucket for using AmazonS3Extender.", 1);	
		return false;
	}
		
	var iu = getImageUploader(this._writer.id);

	var ufc = 0;

	//Source file
	if (new String(iu.getUploadSourceFile()).toLowerCase() != "false") {
		ufc++;
		if (!this._validateFile(this._sourceFile)) {
			return false;
		}
	}
		
	//Thumbnail1
	if (new Number(iu.getUploadThumbnail1FitMode()) != 0) {
		ufc++;
		if (!this._validateFile(this._thumbnail1)) {
			return false;
		}
	}
	
	//Thumbnail2
	if (new Number(iu.getUploadThumbnail2FitMode()) != 0) {
		ufc++;
		if (!this._validateFile(this._thumbnail2)) {
			return false;
		}
	}		

	//Thumbnail3
	if (new Number(iu.getUploadThumbnail3FitMode()) != 0) {
		ufc++;
		if (!this._validateFile(this._thumbnail3)) {
			return false;
		}
	}
	
	if (ufc != 1) {
		IUCommon.showWarning("With AmazonS3Extender you can upload one (and only one) "
			+ "of the following files: SourceFile, Thumbnail1, Thumbnail2, or Thumbnail3. "
			+ "You can change it using UploadSourceFile, UploadThumbnail1FitMode, " 
			+ "UploadThumbnail2FitMode, and UploadThumbnail3FitMode properties. "	
			+ "This limitation will be removed in the future release.", 1);
		return false;
	}
				
	var hostName = (this._bucketHostName != "") ? this._bucketHostName : this._bucket.toLowerCase() + ".s3.amazonaws.com"
	iu.setAction(window.location.protocol +  "//" + hostName + "/");
	
	var m = [new String(iu.getExtractExif()), new String(iu.getExtractIptc())];
	for (var i = 0; i < 2; i++) {
		if (m[i] != "") {
			var fields = m[i].split(";");
			for (var j = 0; j < fields.length; j++) {
				this._postFields.push(fields[j] + "_1");
			}
		}
	}
		
	var s = this._currentSettings;
		
	if (IUCommon.debugLevel > 0) {	
		var p = AmazonS3Extender.Base64.decode(s._policy);
		var policy;
			
		try {
			eval("policy = " + p + ";");
		}
		catch (e) {
			IUCommon.showWarning("Can't parse " + this._currentSettings._name + " policy specified in AmazonS3Extender.", 1);
			return false;
		}
	
		//TODO: Implement detailed policy validation in the future release
	}
	
	//Add Amazon S3 fields
	iu.AddField("key", s.getKey());
	iu.AddField("acl", s.getAcl());
	iu.AddField("policy", s.getPolicy());
	iu.AddField("signature", s.getSignature());
	iu.AddField("success_action_status", "200");
	iu.AddField("AWSAccessKeyId", this._AWSAccessKeyId);	
				
	//Rename file field
	if (s._name.indexOf("Thumbnail") > -1)
		iu.RenameField("Thumbnail[ThumbnailIndex]_[ItemIndex]", "file");
	else
		iu.RenameField(s._name + "_[ItemIndex]", "file");

	//Metadata
	var fields = {};
	for (var name in s._properties) {
		var p = s._properties[name];
		if (p.predefined) {
			fields[p.field] = true;			
		}
	}
		
	//Remove not used post fields
	for (var i = 0; i < this._postFields.length; i++) {	
		if (fields[this._postFields[i]] == undefined) {						
			iu.RemoveField(this._postFields[i]);
		}
	}
		
	//Add metadata
	for (var name in s._properties) {
		if (s._properties[name].predefined) {
			iu.RenameField(s._properties[name].field, "x-amz-meta-" + name);		
		}
		else {
			iu.AddField("x-amz-meta-" + name, s._properties[name].value);
		}
	}	
}

AmazonS3Extender.prototype._control$Error = function(ErrorCode, HttpResponseCode, ErrorPage, AdditionalInfo) {
	IUCommon.showWarning("Error occured during upload to Amazon S3 service. The service returned the following answer:\n\r\n\r" + ErrorPage, 1);
}

//Override BaseExtender methods

AmazonS3Extender.prototype._beforeRender = function() {	
	if (this._writer.getParam("Action")) {
		this._writer.removeParam("Action");
		IUCommon.showWarning("Don't specify Action param directly (addParam(\"Action\", ...)) "
			+ "when using AmazonS3Extender class. AmazonS3Extender configures these settings automatically.", 1);
	}
	
	this._writer.addParam("Action", "http://localhost/");
		
	// Amazon S3 allows to upload only one file per request. One Image Uploader 
	// package can contain several files (source file, 1st thumbnail, 2nd thumbnail, 
	// and so on), so we need to configure Image Uploader in such a way, that it sends 
	// only one file per request	
	if (this._writer.getParam("FilesPerOnePackageCount")) {
		this._writer.removeParam("FilesPerOnePackageCount");	
		IUCommon.showWarning("Don't specify FilesPerOnePackageCount param directly (addParam(\"FilesPerOnePackageCount\", ...)) "
			+ "when use AmazonS3Extender class. AmazonS3Extender configures these settings automatically.", 1);
	}	

	this._writer.addParam("FilesPerOnePackageCount", "1");
}


//Public

//AWSAccessKeyId property
AmazonS3Extender.prototype.getAWSAccessKeyId = function() {
	/// <summary>AWS Access Key ID of the owner of the bucket.</summary>
	/// <returns type="String"></returns>

	return this._AWSAccessKeyId;
}

AmazonS3Extender.prototype.setAWSAccessKeyId = function(value) {
	/// <summary>AWS Access Key ID of the owner of the bucket.</summary>
	/// <param name="value" type="String">A value of property.</param>
		
	this._AWSAccessKeyId = value;
}

//Bucket property
AmazonS3Extender.prototype.getBucket = function() {
	/// <summary>Bucket name.</summary>
	/// <returns type="String"></returns>

	return this._bucket;
}

AmazonS3Extender.prototype.setBucket = function(value) {
	/// <summary>Bucket name.</summary>
	/// <param name="value" type="String">A value of property.</param>
		
	this._bucket = value;
}

//BucketHostName property
AmazonS3Extender.prototype.getBucketHostName = function() {
	/// <summary>Custom bucket host name.</summary>
	/// <returns type="String"></returns>

	return this._bucketHostName;
}

AmazonS3Extender.prototype.setBucketHostName = function(value) {
	/// <summary>Custom bucket host name.</summary>
	/// <param name="value" type="String">A value of property.</param>
		
	this._bucketHostName = value;
}

//SourceFile property
AmazonS3Extender.prototype.getSourceFile = function() {
	/// <summary>Source file upload settings.</summary>
	/// <returns type="AmazonS3Extender.FileSettings"></returns>

	return this._sourceFile;
}

//SourceFile property
AmazonS3Extender.prototype.getSourceFile = function() {
	/// <summary>Source file upload settings.</summary>
	/// <returns type="AmazonS3Extender.FileSettings"></returns>

	return this._sourceFile;
}

//Thumbnail1 property
AmazonS3Extender.prototype.getThumbnail1 = function() {
	/// <summary>First thumbnail upload settings.</summary>
	/// <returns type="AmazonS3Extender.FileSettings"></returns>

	return this._thumbnail1;
}

//Thumbnail2 property
AmazonS3Extender.prototype.getThumbnail2 = function() {
	/// <summary>Second thumbnail upload settings.</summary>
	/// <returns type="AmazonS3Extender.FileSettings"></returns>

	return this._thumbnail2;
}

//Thumbnail3 property
AmazonS3Extender.prototype.getThumbnail3 = function() {
	/// <summary>Third thumbnail upload settings.</summary>
	/// <returns type="AmazonS3Extender.FileSettings"></returns>

	return this._thumbnail3;
}


//--------------------------------------------------------------------------
//AmazonS3Extender.FileSettings class
//--------------------------------------------------------------------------

AmazonS3Extender.FileSettings = function(parent, name) {
	this._parent = parent;
	this._name = name;
	this._policy = "";	
	this._signature = "";
	this._acl = "";
	this._key = "";	
	
	this._properties = {};
	this._fields = {};
}

AmazonS3Extender.FileSettings.prototype = {
	addProperty : function(name, value) {
		/// <summary>Adds metadata property.</summary>
		/// <param name="name" type="String">A name of property.</param>
		/// <param name="value" type="String">A value of property.</param>
		
		if (this._properties[name]) {
			IUCommon.showWarning("The name '" + name + "' of property passed to AmazonS3Extender.addProperty "
				+ "method is incorrect. You have already added this property.", 1);
		}		
		
		this._properties[name] = {predefined : false, value : value};
	},
	
	addPredefinedProperty : function(name, field) {
		/// <summary>Adds metadata property with predefined value.</summary>	
		/// <param name="name" type="String">A name of property.</param>
		/// <param name="field" type="String">A HTTP POST field name (Description_1, Width_1, Height_1, and so on).</param>
		
		if (this._parent._postFieldsHash[field] == undefined) {
			IUCommon.showWarning("The name '" + field + "' of POST field passed to AmazonS3Extender.addPredefinedProperty "
				+ "method is incorrect. You should specify one of the following field names:\n\r\n\r"
				+ this._parent._postFields.join(", "), 1);
			return;		
		}
		
		if (this._parent._postFieldsAdded[field]) {
			IUCommon.showWarning("The name '" + field + "' of POST field passed to AmazonS3Extender.addPredefinedProperty "
				+ "method is incorrect. You have already specified this field name when added another property.", 1);
			return;		
		}		
		
		if (this._properties[name]) {
			IUCommon.showWarning("The name '" + name + "' of property passed to AmazonS3Extender.addPredefinedProperty "
				+ "method is incorrect. You have already added this property.", 1);
			return;
		}
				
		this._parent._postFieldsAdded[field] = 1;
		
		this._properties[name] = {predefined : true, field : field};
	},
	
	removeProperty: function(name) {
		/// <summary>Removes metadata property.</summary>
		/// <param name="name" type="String">A name of property.</param>

		var p = this._properties[name];
		if (p && p.predefined) {
			delete this._parent._postFieldsAdded[p.field];
		}
		
		delete this._properties[name];
	},
	
	getProperty : function(name) {
		/// <summary>Returns a value of metadata property.</summary>
		/// <param name="name" type="String">A name of property.</param>
	
		var p = this._properties[name];		
		if (p) {
			if (p.predefined) {
				IUCommon.showWarning("The name '" + name + "' of property passed to AmazonsS3Extender.getProperty method is incorrect as this property is predefined and its value is available on the server side only.", 1);
				return null;
			}
			else {
				return p.value;
			}
		}
		else {
			return null;
		}
	},
	
	//Properties
	
	//Policy property
	getPolicy : function() {
		/// <summary>Security Policy describing what is permitted in the request. Requests without security policy are considered anonymous and only work on publicly writable buckets.</summary>
		/// <returns type="String"></returns>

		return this._policy;
	},

	setPolicy : function(value) {
		/// <summary>Security Policy describing what is permitted in the request. Requests without security policy are considered anonymous and only work on publicly writable buckets.</summary>
		/// <param name="value" type="String">A value of property.</param>
			
		this._policy = value;
	},

	//Signature property
	getSignature : function() {
		/// <summary>The HMAC signature constructed using the secret key of the provided AWSAccessKeyID.</summary>
		/// <returns type="String"></returns>

		return this._signature;
	},

	setSignature : function(value) {
		/// <summary>The HMAC signature constructed using the secret key of the provided AWSAccessKeyID.</summary>
		/// <param name="value" type="String">A value of property.</param>
			
		this._signature = value;
	},

	//Acl property
	getAcl : function() {
		/// <summary>Specifies an Amazon S3 access control list. Options include private, public-read, public-read-write,  and authenticated-read.</summary>
		/// <returns type="String"></returns>

		return this._acl;
	},

	setAcl : function(value) {
		/// <summary>Specifies an Amazon S3 access control list. Options include private, public-read, public-read-write, and authenticated-read.</summary>
		/// <param name="value" type="String">A value of property.</param>
			
		this._acl = value;
	},
	
	//Key property
	getKey : function() {
		/// <summary>Specifies an Amazon S3 file key.</summary>
		/// <returns type="String"></returns>

		return this._key;
	},

	setKey : function(value) {
		/// <summary>Specifies an Amazon S3 file key</summary>
		/// <param name="value" type="String">A value of property.</param>
			
		this._key = value;
	}		
}


//--------------------------------------------------------------------------
//AmazonS3Extender.Base64 class (http://www.webtoolkit.info/)
//--------------------------------------------------------------------------

AmazonS3Extender.Base64 = {
	_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
	
	_utf8_decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;

		while ( i < utftext.length ) {

			c = utftext.charCodeAt(i);

			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}

		}

		return string;
	},
	
	decode : function (input) {
		var output = "";
		var chr1, chr2, chr3;
		var enc1, enc2, enc3, enc4;
		var i = 0;

		input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

		while (i < input.length) {

			enc1 = this._keyStr.indexOf(input.charAt(i++));
			enc2 = this._keyStr.indexOf(input.charAt(i++));
			enc3 = this._keyStr.indexOf(input.charAt(i++));
			enc4 = this._keyStr.indexOf(input.charAt(i++));

			chr1 = (enc1 << 2) | (enc2 >> 4);
			chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
			chr3 = ((enc3 & 3) << 6) | enc4;

			output = output + String.fromCharCode(chr1);

			if (enc3 != 64) {
				output = output + String.fromCharCode(chr2);
			}
			if (enc4 != 64) {
				output = output + String.fromCharCode(chr3);
			}

		}

		output = AmazonS3Extender.Base64._utf8_decode(output);

		return output;
	}
};
