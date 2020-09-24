// Aurigma Image Uploader Dual 6.x - IUEmbed Script Library
// Copyright(c) Aurigma Inc. 2002-2009
// Version 6.1.4.0

/// <reference path="iuembed.js" />
/// <reference path="iuembed.Intellisense.js" />

//--------------------------------------------------------------------------
//InstallationProgressExtender class
//--------------------------------------------------------------------------

function InstallationProgressExtender(writer) {
    /// <summary>Extends Image Uploader with loading progress for ActiveX.</summary>
    /// <param name="writer" type="ImageUploaderWriter">An instance of ImageUploaderWriter object.</param>

    this._splashscreentimeout = 120000;

    BaseExtender.call(this, writer);

    if (writer == undefined) {
        return;
    }

    if (new String(writer.width).indexOf("%") != -1 || new String(writer.height).indexOf("%") != -1) {
        IUCommon.showWarning("Current version of InstallationProgressExtender class supports ImageUploaderWriter object  "
			+ "which width and height are specified in pixels only (not in percents)", 1);
        this._compatible = false;
    }
    else {
        this._compatible = true;
    }

    //hide splash screen and install screen after timeout ot after InitComplete event;
    if (this._writer && this._writer.id && this._compatible) {
        var self = this;
        var f = function() {
            var splash_screen = document.getElementById(self._writer.id + "-splash");
            var installsplash_screen = document.getElementById(self._writer.id + "-installsplash");
            var iu_wrapper = document.getElementById(self._writer.id + "-innerwrapper");
            if (installsplash_screen)
                installsplash_screen.style.display = "none";
            if (splash_screen)
                splash_screen.style.display = "none";
            if (iu_wrapper) {
                iu_wrapper.style.position = "";
                iu_wrapper.style.left = "0";
                iu_wrapper.style.visibility = "";
            }
        };

        this._writer.addEventListener("InitComplete", f);

        //show control after 2 min timeout
        setTimeout(f, this._splashscreentimeout);
    }

    //CSS classes
    this._progressCssClass = "";
    this._instructionsCssClass = "";

    //Common description
    this._commonHtml = "<p>Aurigma Image Uploader ActiveX control is necessary to upload "
		+ "your files quickly and easily. You will be able to select multiple images "
		+ "in user-friendly interface instead of clumsy input fields with <strong>Browse</strong> button.</p>";

    //Installation progress
    this._progressHtml = "<p><img src=\"{0}\" />"
		+ "<br />"
		+ "Loading Aurigma Image Uploader ActiveX...</p>";
    this._javaProgressHtml = "<p><img src=\"{0}\" />"
		+ "<br />"
		+ "Loading Aurigma Image Uploader Java Applet...</p>";
    this._progressImageUrl = "Scripts/InstallationProgress.gif";

    //Before IE 6 Windows XP SP2
    this._beforeIE6XPSP2ProgressHtml = "<p>To install Image Uploader, please wait until the control will be loaded and click "
		+ "the <strong>Yes</strong> button when you see the installation dialog.</p>";
    this._beforeIE6XPSP2InstructionsHtml = "<p>To install Image Uploader, please reload the page and click "
		+ "the <strong>Yes</strong> button when you see the control installation dialog. "
		+ "If you don't see installation dialog, please check your security settings.</p>";

    //IE 6 Windows XP SP2
    this._IE6XPSP2ProgressHtml = "<p>Please wait until the control will be loaded.</p>"
    this._IE6XPSP2InstructionsHtml = "<p>To install Image Uploader, please click on the <strong>Information Bar</strong> and select "
		+ "<strong>Install ActiveX Control</strong> from the dropdown menu. After page reload click <strong>Install</strong> when "
		+ "you see the control installation dialog. If you don't see Information Bar, please try to reload the page and/or check your security settings.</p>";

    //IE 7
    this._IE7ProgressHtml = this._IE6XPSP2ProgressHtml;
    this._IE7InstructionsHtml = "<p>To install Image Uploader, please click on the <strong>Information Bar</strong> "
		+ "and select <strong>Install ActiveX Control</strong> or <strong>Run ActiveX Control</strong> from the dropdown menu.</p>"
		+ "<p>Then either click <strong>Run</strong> or after the page reload click <strong>Install</strong> "
		+ "when you see the control installation dialog. If you don't see Information Bar, please try to reload the page and/or check your security settings.</p>";

    //IE 8
    this._IE8ProgressHtml = this._IE6XPSP2ProgressHtml;
    this._IE8InstructionsHtml = "<p>To install Image Uploader, please click on the <strong>Information Bar</strong> "
		+ "and select <strong>Install This Add-on</strong> or <strong>Run Add-on</strong> from the dropdown menu.</p>"
		+ "<p>Then either click <strong>Run</strong> or after the page reload click <strong>Install</strong> "
		+ "when you see the control installation dialog. If you don't see Information Bar, please try to reload the page and/or check your security settings.</p>";

    //Alternative standalone installer
    this._altInstallerEnabled = false;
    this._altInstallerHtml = "<p>You can also download <a href=\"{0}\">standalone installator</a>.</p>";
    this._altInstallerUrl = "ImageUploaderStandalone.zip";

    /************************************
    *Update ActiveX control instructions*
    *************************************/
    this._updateInstructions = "You need to update Image Uploader ActiveX control. Click <strong>Install</strong> or <strong>Run</strong> button when you see the control installation dialog."
        + " If you don't see installation dialog, please try to reload the page.";

    /***************************
    *Install java instructions*
    ***************************/
    //Common description
    this._commonInstallJavaHtml = "<p>You need to install Java for running Image Uploader.</p>";

    //Before IE 6 Windows XP SP2
    this._beforeIE6XPSP2InstallJavaHtml = "<p>To install Java, please reload the page and click "
		+ "the <strong>Yes</strong> button when you see the control installation dialog. "
		+ "If you don't see installation dialog, please check your security settings.</p>";

    //IE 6 Windows XP SP2
    this._IE6XPSP2InstallJavaHtml = "<p>To install Java, please click on the <strong>Information Bar</strong> and select "
		+ "<strong>Install ActiveX Control</strong> from the dropdown menu. After page reload click <strong>Install</strong> when "
		+ "you see the control installation dialog. If you don't see Information Bar, please try to reload the page and/or check your security settings.</p>";

    //IE 7
    this._IE7InstallJavaHtml = "<p>To install Java, please click on the <strong>Information Bar</strong> "
		+ "and select <strong>Install ActiveX Control</strong> or <strong>Run ActiveX Control</strong> from the dropdown menu.</p>"
		+ "<p>Then either click <strong>Run</strong> or after the page reload click <strong>Install</strong> "
		+ "when you see the control installation dialog. If you don't see Information Bar, please try to reload the page and/or check your security settings.</p>";

    //IE 8
    this._IE8InstallJavaHtml = "<p>To install Java, please click on the <strong>Information Bar</strong> "
		+ "and select <strong>Install This Add-on</strong> or <strong>Run Add-on</strong> from the dropdown menu.</p>"
		+ "<p>Then either click <strong>Run</strong> or after the page reload click <strong>Install</strong> "
		+ "when you see the control installation dialog. If you don't see Information Bar, please try to reload the page and/or check your security settings.</p>";

    //Mac
    //On Mac install/update Java from Software Update
    this._MacInstallJavaHtml = "<p>Use the <a href=\"http://support.apple.com/kb/HT1338\">Software Update</a> feature "
        + "(available on the Apple menu) to check that you have the most up-to-date version of Java for your Mac.</p>";

    //Other browsers
    this._miscBrowsersInstallJavaHtml = "<p>Please <a href=\"http://www.java.com/en/download/\">download</a> and install Java.</p>";
}

InstallationProgressExtender.prototype = new BaseExtender;
InstallationProgressExtender.prototype.constructor = InstallationProgressExtender;

//Override BaseExtender methods

InstallationProgressExtender.prototype._beforeRender = function() {
    if (this._writer.getControlType() == "ActiveX" && this._compatible) {
        this._writer.instructionsEnabled = true;
        this._writer.instructionsVista = "";
        this._writer.instructionsXPSP2 = "";
        this._writer.instructionsNotXPSP2 = "";
        this._writer.instructionsCommon2 = "";

        var sb = new IUCommon.StringBuilder();

        sb.add("<div " + " id=\"" + this._writer.id + "-installsplash\" " + " style=\"position:absolute;");
        sb.addCssAttr("width", this._writer.width + "px");
        sb.addCssAttr("height", this._writer.height + "px");
        var bc = this._writer.getParam("BackgroundColor");
        sb.addCssAttr("background-color", bc == undefined ? "#c3daf9" : bc);
        sb.add("z-index:1001;\">");
        sb.add("<div style=\"height:100%;margin:0;\" ");
        sb.addCssClass(this._instructionsCssClass);
        sb.add(">");
        sb.add(this._commonHtml);

        if (this._writer.getActiveXInstalledToUpdate()) {
            sb.add(this._getUpdateInstructionsHtml());
        } else {

            if (IUCommon.browser.isBeforeIE6XPSP2) {
                sb.add(this._beforeIE6XPSP2InstructionsHtml)
            }
            else if (IUCommon.browser.isIE6XPSP2) {
                sb.add(this._IE6XPSP2InstructionsHtml)
            }
            else if (IUCommon.browser.isIE7) {
                sb.add(this._IE7InstructionsHtml)
            }
            else { //IUCommon.browser.isIE8 - for future compatibility
                sb.add(this._IE8InstructionsHtml)
            }

            if (this._altInstallerEnabled) {
                sb.add(IUCommon.formatString(this._altInstallerHtml, this._altInstallerUrl));
            }
        }
        sb.add("</div>");

        sb.add("</div>");

        this._writer.instructionsCommon = sb.toString();
    } else if (this._writer.getControlType() == "Java" && this._compatible && IUCommon.browser.isIE) {
        var isJavaInstalled = this._writer.getJREInstalled();
        if (isJavaInstalled < 0) {
            this._writer.installJavaIEInstructions = this._getInstallJavaHtml();
        }
    }
}

InstallationProgressExtender.prototype._getBeforeHtml = function() {
    var sb = new IUCommon.StringBuilder();

    sb.add("<div style=\"position:relative;")
    sb.addCssAttr("width", this._writer.width + "px");
    sb.addCssAttr("height", this._writer.height + "px");
    sb.add("\">");

    if (this._writer.getControlType() == "ActiveX" && this._compatible) {
        sb.add("<div " + " id=\"" + this._writer.id + "-splash\" " + " style=\"position:absolute;");
        sb.addCssAttr("width", (this._writer.width) + "px");
        sb.addCssAttr("height", (this._writer.height) + "px");
        var bc = this._writer.getParam("BackgroundColor");
        sb.addCssAttr("background-color", bc == undefined ? "#c3daf9" : bc);
        sb.add("overflow:hidden;top:0;left:0;z-index:1000;\">");
        sb.add("<div style=\"height:100%;margin:0;\" ");
        sb.addCssClass(this._progressCssClass);
        sb.add(">");
        sb.add(IUCommon.formatString(this._progressHtml, this._progressImageUrl));
        sb.add(this._commonHtml);

        if (this._writer.getActiveXInstalledToUpdate()) {
            sb.add(this._getUpdateInstructionsHtml());
        } else {
            if (IUCommon.browser.isBeforeIE6XPSP2) {
                sb.add(this._beforeIE6XPSP2ProgressHtml)
            }
            else if (IUCommon.browser.isIE6XPSP2) {
                sb.add(this._IE6XPSP2ProgressHtml)
            }
            else if (IUCommon.browser.isIE7) {
                sb.add(this._IE7ProgressHtml)
            }
            else { //IUCommon.browser.isIE8 - for future compatibility
                sb.add(this._IE8ProgressHtml)
            }
        }
        sb.add("</div>");
        sb.add("</div>");
    } else if (this._writer.getControlType() == "Java" && this._compatible) {

        var isJavaInstalled = this._writer.getJREInstalled();

        if (!IUCommon.browser.isIE && isJavaInstalled < 0) {
            sb.add(this._getInstallJavaHtml());
        }

        //show loading screen if we know that java installed
        if (isJavaInstalled >= 0) {
            //loading screen html
            sb.add("<div ");
            sb.add(" id=\"" + this._writer.id + "-splash\" ");
            sb.add(" style=\"position:absolute;");
            sb.addCssAttr("width", (this._writer.width) + "px");
            sb.addCssAttr("height", (this._writer.height) + "px");
            var bc = this._writer.getParam("BackgroundColor");
            sb.addCssAttr("background-color", bc == undefined ? "#c3daf9" : bc);
            sb.add("overflow:hidden;top:0;left:0;z-index:1001;\">");
            sb.add("<div style=\"height:100%;margin:0;\" ");
            if (this._progressCssClass)
                sb.addCssClass(this._progressCssClass);
            sb.add(">");

            sb.add(IUCommon.formatString(this._javaProgressHtml, this._progressImageUrl));

            sb.add("</div>");
            sb.add("</div>");
        }
        sb.add("<div id=\"" + this._writer.id + "-innerwrapper\" style=\"");

        //In IE "visibility:hidden" doesn't work, in FF3 "position:absolute;left:-10000px;" doesn't work properly
        if (IUCommon.browser.isIE) {
            if (isJavaInstalled > 0) {
                sb.add("position:absolute;left:-10000px;");
            } else {
                // render java install instructions inside <object> tag
            }
        }
        else {
            sb.add("visibility:hidden;");
        }

        sb.add("\" >");
    }

    return sb.toString();
}

InstallationProgressExtender.prototype._getAfterHtml = function() {
    var sb = new IUCommon.StringBuilder();

    //add additional wrapper container for java applet
    if (this._writer.getControlType() == "Java" && this._compatible)
        sb.add("</div>");

    sb.add("</div>");

    return sb.toString();
}

InstallationProgressExtender.prototype._getInstallJavaHtml = function() {
    var sb = new IUCommon.StringBuilder();

    if (IUCommon.browser.isIE) {
        sb.add("<div style=\"position:absolute;");
        sb.addCssAttr("width", this._writer.width + "px");
        sb.addCssAttr("height", this._writer.height + "px");
        var bc = this._writer.getParam("BackgroundColor");
        sb.addCssAttr("background-color", bc == undefined ? "#c3daf9" : bc);
        sb.add("z-index:1001;\">");
        sb.add("<div style=\"height:100%;margin:0;\" ");
        sb.addCssClass(this._instructionsCssClass);
        sb.add(">");
        sb.add(this._commonInstallJavaHtml);

        if (IUCommon.browser.isBeforeIE6XPSP2) {
            sb.add(this._beforeIE6XPSP2InstallJavaHtml);
        }
        else if (IUCommon.browser.isIE6XPSP2) {
            sb.add(this._IE6XPSP2InstallJavaHtml);
        }
        else if (IUCommon.browser.isIE7) {
            sb.add(this._IE7InstallJavaHtml);
        }
        else { //IUCommon.browser.isIE8 - for future compatibility
            sb.add(this._IE8InstallJavaHtml);
        }

        sb.add("</div>");

        sb.add("</div>");
    } else {
        //add java plugin required html
        sb.add("<div ");
        sb.add(" id=\"" + this._writer.id + "-installsplash\" ");
        sb.add(" style=\"position:absolute;");
        sb.addCssAttr("width", (this._writer.width) + "px");
        sb.addCssAttr("height", (this._writer.height) + "px");
        var bc = this._writer.getParam("BackgroundColor");
        sb.addCssAttr("background-color", bc == undefined ? "#c3daf9" : bc);
        sb.add("overflow:hidden;top:0;left:0;z-index:1002;\">");
        sb.add("<div style=\"height:100%;margin:0;\" ");
        if (this._instructionsCssClass)
            sb.addCssClass(this._instructionsCssClass);
        sb.add(">");
        sb.add(this._commonInstallJavaHtml);

        if (navigator.platform.toLowerCase().indexOf("mac") > -1) {
            sb.add(this._MacInstallJavaHtml);
        } else {
            sb.add(this._miscBrowsersInstallJavaHtml);
        }

        sb.add("</div>");
        sb.add("</div>");
    }
    return sb.toString();
}

InstallationProgressExtender.prototype._getUpdateInstructionsHtml = function() {
    return this._updateInstructions;
}

//Public

//CSS classes

//ProgressCssClass property
InstallationProgressExtender.prototype.getProgressCssClass = function() {
    /// <summary>Installation progress CSS class name.</summary>
    /// <returns type="String"></returns>

    return this._progressCssClass;
}

InstallationProgressExtender.prototype.setProgressCssClass = function(value) {
    /// <summary>Installation progress CSS class name.</summary>
    /// <param name="value" type="String">A value of property.</param>

    this._progressCssClass = value;
}

//InstructionsCssClass property
InstallationProgressExtender.prototype.getInstructionsCssClass = function() {
    /// <summary>User instructions CSS class name.</summary>
    /// <returns type="String"></returns>

    return this._instructionsCssClass;
}

InstallationProgressExtender.prototype.setInstructionsCssClass = function(value) {
    /// <summary>User instructions CSS class name.</summary>
    /// <param name="value" type="String">A value of property.</param>

    this._instructionsCssClass = value;
}

//Common description

//CommonHtml property
InstallationProgressExtender.prototype.getCommonHtml = function() {
    /// <summary>Common Image Uploader description.</summary>
    /// <returns type="String"></returns>

    return this._commonHtml;
}

InstallationProgressExtender.prototype.setCommonHtml = function(value) {
    /// <summary>Common Image Uploader description.</summary>
    /// <param name="value" type="String">A value of property.</param>

    this._commonHtml = value;
}

//Installation progress

//ProgressHtml property
InstallationProgressExtender.prototype.getProgressHtml = function() {
    /// <summary>Installation progress HTML.</summary>
    /// <returns type="String"></returns>

    return this._progressHtml;
}

InstallationProgressExtender.prototype.setProgressHtml = function(value) {
    /// <summary>Installation progress HTML.</summary>
    /// <param name="value" type="String">A value of property.</param>

    this._progressHtml = value;
}

//JavaProgressHtml property
InstallationProgressExtender.prototype.getJavaProgressHtml = function() {
    /// <summary>Installation progress HTML.</summary>
    /// <returns type="String"></returns>

    return this._javaProgressHtml;
}

InstallationProgressExtender.prototype.setJavaProgressHtml = function(value) {
    /// <summary>Installation progress HTML.</summary>
    /// <param name="value" type="String">A value of property.</param>

    this._javaProgressHtml = value;
}

//ProgressImageUrl  property
InstallationProgressExtender.prototype.getProgressImageUrl = function() {
    /// <summary>Progress image URL.</summary>
    /// <returns type="String"></returns>

    return this._progressImageUrl;
}

InstallationProgressExtender.prototype.setProgressImageUrl = function(value) {
    /// <summary>Progress image URL.</summary>
    /// <param name="value" type="String">A value of property.</param>

    this._progressImageUrl = value;
}

//Before IE 6 Windows XP SP2

//BeforeIE6XPSP2ProgressHtml property
InstallationProgressExtender.prototype.getBeforeIE6XPSP2ProgressHtml = function() {
    /// <summary>Before Internet Explorer 6 Windows XP SP 2 installation progress HTML.</summary>
    /// <returns type="String"></returns>

    return this._beforeIE6XPSP2ProgressHtml;
}

InstallationProgressExtender.prototype.setBeforeIE6XPSP2ProgressHtml = function(value) {
    /// <summary>Before Internet Explorer 6 Windows XP SP 2 installation progress HTML.</summary>
    /// <param name="value" type="String">A value of property.</param>

    this._beforeIE6XPSP2ProgressHtml = value;
}

//BeforeIE6XPSP2InstructionsHtml property
InstallationProgressExtender.prototype.getBeforeIE6XPSP2InstructionsHtml = function() {
    /// <summary>Before Internet Explorer 6 Windows XP SP user instructions HTML.</summary>
    /// <returns type="String"></returns>

    return this._beforeIE6XPSP2InstructionsHtml;
}

InstallationProgressExtender.prototype.setBeforeIE6XPSP2InstructionsHtml = function(value) {
    /// <summary>Before Internet Explorer 6 Windows XP SP user instructions HTML.</summary>
    /// <param name="value" type="String">A value of property.</param>

    this._beforeIE6XPSP2InstructionsHtml = value;
}

//IE 6 Windows XP SP2

//IE6XPSP2ProgressHtml property
InstallationProgressExtender.prototype.getIE6XPSP2ProgressHtml = function() {
    /// <summary>Internet Explorer 6 Windows XP SP 2 installation progress HTML.</summary>
    /// <returns type="String"></returns>

    return this._IE6XPSP2ProgressHtml;
}

InstallationProgressExtender.prototype.setIE6XPSP2ProgressHtml = function(value) {
    /// <summary>Internet Explorer 6 Windows XP SP 2 installation progress HTML.</summary>
    /// <returns type="String"></returns>

    this._IE6XPSP2ProgressHtml = value;
}

//IE6XPSP2InstructionsHtml property
InstallationProgressExtender.prototype.getIE6XPSP2InstructionsHtml = function() {
    /// <summary>Internet Explorer 6 Windows XP SP user instructions HTML.</summary>
    /// <returns type="String"></returns>

    return this._IE6XPSP2InstructionsHtml;
}

InstallationProgressExtender.prototype.setIE6XPSP2InstructionsHtml = function(value) {
    /// <summary>Internet Explorer 6 Windows XP SP user instructions HTML.</summary>
    /// <returns type="String"></returns>

    this._IE6XPSP2InstructionsHtml = value;
}

//IE 7

//IE7ProgressHtml property
InstallationProgressExtender.prototype.getIE7ProgressHtml = function() {
    /// <summary>Internet Explorer 7 installation progress HTML.</summary>
    /// <returns type="String"></returns>

    return this._IE7ProgressHtml;
}

InstallationProgressExtender.prototype.setIE7ProgressHtml = function(value) {
    /// <summary>Internet Explorer 7 installation progress HTML.</summary>
    /// <param name="value" type="String">A value of property.</param>

    this._IE7ProgressHtml = value;
}


//IE7InstructionsHtml property
InstallationProgressExtender.prototype.getIE7InstructionsHtml = function() {
    /// <summary>Internet Explorer 7 user instructions HTML.</summary>
    /// <returns type="String"></returns>

    return this._IE7InstructionsHtml;
}

InstallationProgressExtender.prototype.setIE7InstructionsHtml = function(value) {
    /// <summary>Internet Explorer 7 user instructions HTML.</summary>
    /// <param name="value" type="String">A value of property.</param>

    this._IE7InstructionsHtml = value;
}


//IE 8

//IE8ProgressHtml property
InstallationProgressExtender.prototype.getIE8ProgressHtml = function() {
    /// <summary>Internet Explorer 8 installation progress HTML.</summary>
    /// <returns type="String"></returns>

    return this._IE8ProgressHtml;
}

InstallationProgressExtender.prototype.setIE8ProgressHtml = function(value) {
    /// <summary>Internet Explorer 8 installation progress HTML.</summary>
    /// <param name="value" type="String">A value of property.</param>

    this._IE8ProgressHtml = value;
}


//IE8InstructionsHtml property
InstallationProgressExtender.prototype.getIE8InstructionsHtml = function() {
    /// <summary>Internet Explorer 8 user instructions HTML.</summary>
    /// <returns type="String"></returns>

    return this._IE8InstructionsHtml;
}

InstallationProgressExtender.prototype.setIE8InstructionsHtml = function(value) {
    /// <summary>Internet Explorer 8 user instructions HTML.</summary>
    /// <param name="value" type="String">A value of property.</param>

    this._IE8InstructionsHtml = value;
}

//Alternative standalone installer

//AltInstallerEnabled property
InstallationProgressExtender.prototype.getAltInstallerEnabled = function() {
    /// <summary>Specifies whether to display alternative standalone installer user instructions.</summary>
    /// <returns type="Boolean"></returns>

    return this._altInstallerEnabled;
}

InstallationProgressExtender.prototype.setAltInstallerEnabled = function(value) {
    /// <summary>Specifies whether to display alternative standalone installer user instructions.</summary>
    /// <param name="value" type="Boolean">A value of property.</param>

    this._altInstallerEnabled = value;
}

//AltInstallerHtml property
InstallationProgressExtender.prototype.getAltInstallerHtml = function() {
    /// <summary>Alternative standalone installer user instructions HTML.</summary>
    /// <returns type="String"></returns>

    return this._altInstallerHtml;
}

InstallationProgressExtender.prototype.setAltInstallerHtml = function(value) {
    /// <summary>Alternative standalone installer user instructions HTML.</summary>
    /// <param name="value" type="String">A value of property.</param>

    this._altInstallerHtml = value;
}

//AltInstallerUrl property
InstallationProgressExtender.prototype.getAltInstallerUrl = function() {
    /// <summary>Alternative standalone installer URL.</summary>
    /// <returns type="String"></returns>

    return this._altInstallerUrl;
}

InstallationProgressExtender.prototype.setAltInstallerUrl = function(value) {
    /// <summary>Alternative standalone installer URL.</summary>
    /// <param name="value" type="String">A value of property.</param>

    this._altInstallerUrl = value;
}

//Update ActiveX control instructions
InstallationProgressExtender.prototype.setUpdateInstructions = function(value) {
    /// <summary>Update ActiveX control instructions.</summary>
    /// <param name="value" type="String">A value of property.</param>

    this._updateInstructions = value;
}

//Update ActiveX control instructions
InstallationProgressExtender.prototype.getUpdateInstructions = function() {
    /// <summary>Update ActiveX control instructions.</summary>
    /// <returns type="String"></returns>

    return this._updateInstructions;
}

//Common install Java description
InstallationProgressExtender.prototype.setCommonInstallJavaHtml = function(value) {
    /// <summary>Common install Java description.</summary>
    /// <param name="value" type="String">A value of property.</param>

    this._commonInstallJavaHtml = value;
}

//Common install Java description
InstallationProgressExtender.prototype.getCommonInstallJavaHtml = function() {
    /// <summary>Common install Java description.</summary>
    /// <returns type="String"></returns>

    return this._commonInstallJavaHtml;
}

//Before IE 6 Windows XP SP2 installation Java instructions
InstallationProgressExtender.prototype.setBeforeIE6XPSP2InstallJavaHtml = function(value) {
    /// <summary>Before IE 6 Windows XP SP2 installation Java instructions.</summary>
    /// <param name="value" type="String">A value of property.</param>

    this._beforeIE6XPSP2InstallJavaHtml = value;
}

//Before IE 6 Windows XP SP2 installation java instructions
InstallationProgressExtender.prototype.getBeforeIE6XPSP2InstallJavaHtml = function() {
    /// <summary>Before IE 6 Windows XP SP2 installation Java instructions.</summary>
    /// <returns type="String"></returns>

    return this._beforeIE6XPSP2InstallJavaHtml;
}

//IE 6 Windows XP SP2 installation Java instructions
InstallationProgressExtender.prototype.setIE6XPSP2InstallJavaHtml = function(value) {
    /// <summary>IE 6 Windows XP SP2 installation Java instructions.</summary>
    /// <param name="value" type="String">A value of property.</param>

    this._IE6XPSP2InstallJavaHtml = value;
}

//IE 6 Windows XP SP2 installation Java instructions
InstallationProgressExtender.prototype.getIE6XPSP2InstallJavaHtml = function() {
    /// <summary>IE 6 Windows XP SP2 installation Java instructions.</summary>
    /// <returns type="String"></returns>

    return this._IE6XPSP2InstallJavaHtml;
}

//IE 7 installation Java instructions
InstallationProgressExtender.prototype.setIE7InstallJavaHtml = function(value) {
    /// <summary>IE 7 installation Java instructions.</summary>
    /// <param name="value" type="String">A value of property.</param>

    this._IE7InstallJavaHtml = value;
}

//IE 7 installation Java instructions
InstallationProgressExtender.prototype.getIE7InstallJavaHtml = function() {
    /// <summary>IE 7 installation Java instructions.</summary>
    /// <returns type="String"></returns>

    return this._IE7InstallJavaHtml;
}

//IE 8 installation Java instructions
InstallationProgressExtender.prototype.setIE8InstallJavaHtml = function(value) {
    /// <summary>IE 8 installation Java instructions.</summary>
    /// <param name="value" type="String">A value of property.</param>

    this._IE8InstallJavaHtml = value;
}

//IE 8 installation Java instructions
InstallationProgressExtender.prototype.getIE8InstallJavaHtml = function() {
    /// <summary>IE 8 installation Java instructions.</summary>
    /// <returns type="String"></returns>

    return this._IE8InstallJavaHtml;
}

//Mac OS installation Java instructions
InstallationProgressExtender.prototype.setMacInstallJavaHtml = function(value) {
    /// <summary>Mac OS installation Java instructions.</summary>
    /// <param name="value" type="String">A value of property.</param>

    this._MacInstallJavaHtml = value;
}

//Mac OS installation Java instructions
InstallationProgressExtender.prototype.getMacInstallJavaHtml = function() {
    /// <summary>Mac OS installation Java instructions.</summary>
    /// <returns type="String"></returns>

    return this._MacInstallJavaHtml;
}

//Other browsers installation Java instructions
InstallationProgressExtender.prototype.setMiscBrowsersInstallJavaHtml = function(value) {
    /// <summary>Other browsers installation Java instructions.</summary>
    /// <param name="value" type="String">A value of property.</param>

    this._miscBrowsersInstallJavaHtml = value;
}

//Other browsers installation Java instructions
InstallationProgressExtender.prototype.getMiscBrowsersInstallJavaHtml = function() {
    /// <summary>Other browsers installation Java instructions.</summary>
    /// <returns type="String"></returns>

    return this._miscBrowsersInstallJavaHtml;
}