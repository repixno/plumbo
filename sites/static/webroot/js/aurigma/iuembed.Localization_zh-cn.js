// Aurigma Image Uploader Dual 6.x - IUEmbed Script Library
// Simplified Chinese Localization
// Copyright(c) Huidu LLC 2005-2007 (http://www.evget.com/)
// Version 6.1.4.0

// 将以下参数加入到控件中，可以完成对该控件信息的汉化

//--------------------------------------------------------------------------
//zh_cn_resources class
//--------------------------------------------------------------------------

zh_cn_resources = {
    addParams: IULocalization.addParams,

    Language: "Simplified Chinese",

    ImageUploaderWriter: {
        //REVIEW
        instructionsCommon: "<p>Image Uploader ActiveX control is necessary to upload your files quickly and easily. You will be able to select multiple images in user-friendly interface instead of clumsy input fields with <strong>Browse</strong> button. Installation will take up to few minutes, please be patient. To install Image Uploader, ",
        //REVIEW
        instructionsNotWinXPSP2: "please reload the page and click the <strong>Yes</strong> button when you see the control installation dialog.",
        //REVIEW
        instructionsWinXPSP2: "please click on the <strong>Information Bar</strong> and select <strong>Install ActiveX Control</strong> from the dropdown menu. After page reload click <strong>Install</strong> when you see the control installation dialog.",
        //REVIEW
        instructionsVista: "please click on the <strong>Information Bar</strong> and select <strong>Install ActiveX Control</strong> from the dropdown menu. After page reload click <strong>Continue</strong> and then <strong>Install</strong> when you see the control installation dialog.",
        instructionsCommon2: "</p>"
    },

    ImageUploader: {
        AddFolderDialogButtonCancelText: "取消",
        AddFolderDialogButtonSkipAllText: "全部跳过",
        AddFolderDialogButtonSkipText: "跳过",
        AddFolderDialogTitleText: "新建目录...",
        AuthenticationRequestBasicText: "[Name]需要授权认证.",
        AuthenticationRequestButtonCancelText: "取消",
        AuthenticationRequestButtonOkText: "确认",
        AuthenticationRequestDomainText: "域名:",
        AuthenticationRequestLoginText: "登录:",
        AuthenticationRequestNtlmText: "[Name]需要授权认证.",
        AuthenticationRequestPasswordText: "密码:",
        ButtonAddAllToUploadListText: "加入所有档案",
        ButtonAddFilesText: "加入档案...",
        ButtonAddFoldersText: "加入数据夹...",
        ButtonAddToUploadListText: "加入",
        ButtonAdvancedDetailsCancelText: "取消",

        ButtonCheckAllText: "选择所有图片",
        ButtonDeleteFilesText: "", //"删除文件"
        ButtonDeselectAllText: "移除所有图片",
        ButtonPasteText: "", //"贴上"
        ButtonRemoveAllFromUploadListText: "移除所有档案",
        ButtonRemoveFromUploadListText: "移除",
        ButtonSelectAllText: "选择所有图片",
        ButtonSendText: "图片上传",
        ButtonStopText: "", //"停止上传"

        ButtonUncheckAllText: "移除所有图片",
        //REVIEW
        CmykImagesAreNotAllowedText: "File is CMYK",
        DescriptionEditorButtonCancelText: "取消",
        DescriptionEditorButtonOkText: "确定",

        //To be supplied
        DeleteFilesDialogTitleText: "Confirm File Delete",
        //To be supplied
        DeleteSelectedFilesDialogMessageText: "Are you sure you want to permanently delete selected items?",
        //To be supplied
        DeleteUploadedFilesDialogMessageText: "Are you sure you want to permanently delete uploaded items?",
        DimensionsAreTooLargeText: "图片太大",
        DimensionsAreTooSmallText: "图片太小",
        DropFilesHereText: "将要上传的图片拖到此处",
        EditDescriptionText: "编辑",

        //To be supplied
        ErrorDeletingFilesDialogMessageText: "Could not delete [Name]",
        FileIsTooLargeText: "文件太大",
        FileIsTooSmallText: "文件太小",
        HoursText: "小时",
        IncludeSubfoldersText: "包含子数据夹",
        KilobytesText: "K",
        //REVIEW
        LargePreviewGeneratingPreviewText: "Generating preview...",
        //REVIEW
        LargePreviewIconTooltipText: "Preview Thumbnail",
        //REVIEW
        LargePreviewNoPreviewAvailableText: "No preview available.",
        ListColumnFileNameText: "文件名",
        ListColumnFileSizeText: "文件大小",
        ListColumnFileTypeText: "文件类型",
        ListColumnLastModifiedText: "上一次修改时间",
        ListKilobytesText: "K",
        LoadingFilesText: "正在装载文件...",
        MegabytesText: "M",
        MenuAddAllToUploadListText: "将所有图片加到上传区",
        MenuAddToUploadListText: "加到上传区",
        MenuArrangeByModifiedText: "已修改",
        MenuArrangeByNameText: "名",
        MenuArrangeByPathText: "路径",
        MenuArrangeBySizeText: "大小",
        MenuArrangeByText: "排列图标",
        MenuArrangeByTypeText: "类型",
        MenuArrangeByUnsortedText: "未排序",
        MenuDeselectAllText: "移除所有图片",
        MenuDetailsText: "详细格式",
        MenuIconsText: "图标格式",
        MenuInvertSelectionText: "反选选中图片",
        MenuListText: "列表格式",
        MenuRefreshText: "刷新",
        MenuRemoveAllFromUploadListText: "从上传区移除所有图片",
        MenuRemoveFromUploadListText: "从上传区移除图片",
        MenuSelectAllText: "选择所有文件",
        MenuThumbnailsText: "图片预览格式",
        MessageBoxTitleText: "图片上传插件",
        MessageCannotConnectToInternetText: "无法连上互联网，请检查网络配置",
        //REVIEW		
        MessageCmykImagesAreNotAllowedText: "CMYK images are not allowed",
        MessageDimensionsAreTooLargeText: "图档 [Name] 无法选取.因为分辨率 ([OriginalImageWidth]x[OriginalImageHeight]) 超过限定的大小. 图文件分辨率应该要低于 [MaxImageWidth]x[MaxImageHeight].",
        MessageDimensionsAreTooSmallText: "图档 [Name] 无法选取.因为分辨率 ([OriginalImageWidth]x[OriginalImageHeight]) 太低. 图文件分辨率应该要大于 [MinImageWidth]x[MinImageHeight].",
        MessageFileSizeIsTooSmallText: "文件[Name]无法选中，该文件大小小于最小限制的([Limit]kb)。",
        MessageMaxFileCountExceededText: "文件[Name]无法选中，文件总数超过限制的[Limit]个文件。",
        MessageMaxFileSizeExceededText: "文件[Name]无法选中，该文件大小超过限制的[Limit]kb。",
        MessageMaxTotalFileSizeExceededText: "文件[Name]无法选中，上载数据总数超过限制的[Limit]kb。",
        MessageNoInternetSessionWasEstablishedText: "网络会话没有建立。",
        MessageNoResponseFromServerText: "服务器无响应。",
        MessageRetryOpenFolderText: "最后访问的文件夹未找到。可能是因为位于可移动存器体，插入此移动存器体并击重试按钮或点击取消按钮继续。",
        MessageServerNotFoundText: "服务器没有找到",
        MessageSwitchAnotherFolderWarningText: "你正在切换到另一个文件夹。此操作会放弃所有已选中的文件。\n\n确认放弃所选文件并继续执行点击确认。\nTo 保持所选文件，继续停留在当前文件夹请点击取消。",
        MessageUnexpectedErrorText: "图像上传因故失败，请联系网站管理员解决此问题。",
        MessageUploadCancelledText: "取消上传",
        MessageUploadCompleteText: "上传图片完成",
        MessageUploadFailedText: "上传图片失败",
        MessageUserSpecifiedTimeoutHasExpiredText: "已经超过了您自订的传输时间.",
        MinutesText: "分钟",
        ProgressDialogCancelButtonText: "取消",
        ProgressDialogCloseButtonText: "关闭",
        ProgressDialogCloseWhenUploadCompletesText: "上传结束后是否关闭此对话框？",
        ProgressDialogEstimatedTimeText: "估计剩余时间为[Current],总共需要时间为 [Total]",
        ProgressDialogPreparingDataText: "正在准备上传数据",
        ProgressDialogSentText: "已经上传[Current]文件，总共文件大小为[Total]",
        ProgressDialogTitleText: "上传图片",
        ProgressDialogWaitingForResponseFromServerText: "等待服务器响应...",
        ProgressDialogWaitingForRetryText: "正在等待重试...",
        RemoveIconTooltipText: "删除",
        RotateIconClockwiseTooltipText: "顺时针方向旋转",
        RotateIconCounterclockwiseTooltipText: "反时针方向旋转",
        SecondsText: "秒",
        UnixFileSystemRootText: "档案系统根目录(Root)",
        UnixHomeDirectoryText: "档案系统有效目录(Home)"
    }
}