// Aurigma Image Uploader Dual 6.x - IUEmbed Script Library
// Traditional Chinese Localization
// Copyright(c) Huidu LLC 2005-2008 (http://www.evget.com/)
// Version 6.1.4.0

// 將以下參數加入到控制項中，可以完成對該控制項資訊的漢化

//--------------------------------------------------------------------------
//zh_resources class
//--------------------------------------------------------------------------

zh_resources = {
    addParams: IULocalization.addParams,

    Language: "Traditional Chinese",

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
        AddFolderDialogButtonSkipAllText: "全部跳過",
        AddFolderDialogButtonSkipText: "跳過",
        AddFolderDialogTitleText: "新建目錄...",
        AuthenticationRequestBasicText: "[Name]需要授權認證.",
        AuthenticationRequestButtonCancelText: "取消",
        AuthenticationRequestButtonOkText: "確認",
        AuthenticationRequestDomainText: "功能變數名稱:",
        AuthenticationRequestLoginText: "登錄:",
        AuthenticationRequestNtlmText: "[Name]需要授權認證.",
        AuthenticationRequestPasswordText: "密碼:",
        ButtonAddAllToUploadListText: "加入所有檔案",
        ButtonAddFilesText: "加入檔案...",
        ButtonAddFoldersText: "加入數據夾...",
        ButtonAddToUploadListText: "加入",
        ButtonAdvancedDetailsCancelText: "取消",

        ButtonCheckAllText: "選擇所有圖片",
        ButtonDeleteFilesText: "", //"刪除檔"
        ButtonDeselectAllText: "移除所有圖片",
        ButtonPasteText: "", //"貼上"
        ButtonRemoveAllFromUploadListText: "移除所有檔案",
        ButtonRemoveFromUploadListText: "移除",
        ButtonSelectAllText: "選擇所有圖片",
        ButtonSendText: "圖片上傳",
        ButtonStopText: "", //"停止上傳"

        ButtonUncheckAllText: "移除所有圖片",
        //REVIEW		
        CmykImagesAreNotAllowedText: "File is CMYK",
        DescriptionEditorButtonCancelText: "取消",
        DescriptionEditorButtonOkText: "確定",

        //To be supplied
        DeleteFilesDialogTitleText: "Confirm File Delete",
        //To be supplied
        DeleteSelectedFilesDialogMessageText: "Are you sure you want to permanently delete selected items?",
        //To be supplied
        DeleteUploadedFilesDialogMessageText: "Are you sure you want to permanently delete uploaded items?",
        DimensionsAreTooLargeText: "圖片太大",
        DimensionsAreTooSmallText: "圖片太小",
        DropFilesHereText: "將要上傳的圖片拖到此處",
        EditDescriptionText: "編輯",

        //To be supplied
        ErrorDeletingFilesDialogMessageText: "Could not delete [Name]",
        FileIsTooLargeText: "文件太大",
        FileIsTooSmallText: "文件太小",
        HoursText: "小時",
        IncludeSubfoldersText: "包含子數據夾",
        KilobytesText: "K",
        //REVIEW
        LargePreviewGeneratingPreviewText: "Generating preview...",
        //REVIEW
        LargePreviewIconTooltipText: "Preview Thumbnail",
        //REVIEW
        LargePreviewNoPreviewAvailableText: "No preview available.",
        ListColumnFileNameText: "檔案名",
        ListColumnFileSizeText: "文件大小",
        ListColumnFileTypeText: "文件類型",
        ListColumnLastModifiedText: "上一次修改時間",
        ListKilobytesText: "K",
        LoadingFilesText: "正在裝載文件...",
        MegabytesText: "M",
        MenuAddAllToUploadListText: "將所有圖片加到上傳區",
        MenuAddToUploadListText: "加到上傳區",
        MenuArrangeByModifiedText: "已修改",
        MenuArrangeByNameText: "名",
        MenuArrangeByPathText: "路徑",
        MenuArrangeBySizeText: "大小",
        MenuArrangeByText: "排列圖示",
        MenuArrangeByTypeText: "類型",
        MenuArrangeByUnsortedText: "未排序",
        MenuDeselectAllText: "移除所有圖片",
        MenuDetailsText: "詳細格式",
        MenuIconsText: "圖示格式",
        MenuInvertSelectionText: "反選選中圖片",
        MenuListText: "列表格式",
        MenuRefreshText: "刷新",
        MenuRemoveAllFromUploadListText: "從上傳區移除所有圖片",
        MenuRemoveFromUploadListText: "從上傳區移除圖片",
        MenuSelectAllText: "選擇所有檔",
        MenuThumbnailsText: "圖片預覽格式",
        MessageBoxTitleText: "圖片上傳插件",
        MessageCannotConnectToInternetText: "無法連上互聯網，請檢查網路配置",
        //REVIEW		
        MessageCmykImagesAreNotAllowedText: "CMYK images are not allowed",
        MessageDimensionsAreTooLargeText: "圖檔 [Name] 無法選取.因為解析度 ([OriginalImageWidth]x[OriginalImageHeight]) 超過限定的大小. 圖檔解析度應該要低於 [MaxImageWidth]x[MaxImageHeight].",
        MessageDimensionsAreTooSmallText: "圖檔 [Name] 無法選取.因為解析度 ([OriginalImageWidth]x[OriginalImageHeight]) 太低. 圖檔解析度應該要大於 [MinImageWidth]x[MinImageHeight].",
        MessageFileSizeIsTooSmallText: "檔[Name]無法選中，該檔大小小於最小限制的([Limit]kb)。",
        MessageMaxFileCountExceededText: "檔[Name]無法選中，檔總數超過限制的[Limit]個檔。",
        MessageMaxFileSizeExceededText: "檔[Name]無法選中，該檔大小超過限制的[Limit]kb。",
        MessageMaxTotalFileSizeExceededText: "檔[Name]無法選中，上載資料總數超過限制的[Limit]kb。",
        MessageNoInternetSessionWasEstablishedText: "網路會話沒有建立。",
        MessageNoResponseFromServerText: "伺服器無回應。",
        MessageRetryOpenFolderText: "最後訪問的檔夾未找到。可能是因為位於可移動存器體，插入此移動存器體並擊重試按鈕或點擊取消按鈕繼續。",
        MessageServerNotFoundText: "伺服器沒有找到",
        MessageSwitchAnotherFolderWarningText: "你正在切換到另一個檔夾。此操作會放棄所有已選中的檔。\n\n確認放棄所選檔並繼續執行點擊確認。\nTo 保持所選檔，繼續停留在當前檔夾請點擊取消。",
        MessageUnexpectedErrorText: "圖像上傳因故失敗，請聯繫網站管理員解決此問題。",
        MessageUploadCancelledText: "取消上傳",
        MessageUploadCompleteText: "上傳圖片完成",
        MessageUploadFailedText: "上傳圖片失敗",
        MessageUserSpecifiedTimeoutHasExpiredText: "已經超過了您自訂的傳輸時間.",
        MinutesText: "分鐘",
        ProgressDialogCancelButtonText: "取消",
        ProgressDialogCloseButtonText: "關閉",
        ProgressDialogCloseWhenUploadCompletesText: "上傳結束後是否關閉此對話方塊？",
        ProgressDialogEstimatedTimeText: "估計剩餘時間為[Current],總共需要時間為 [Total]",
        ProgressDialogPreparingDataText: "正在準備上傳資料",
        ProgressDialogSentText: "已經上傳[Current]檔，總共檔大小為[Total]",
        ProgressDialogTitleText: "上傳圖片",
        ProgressDialogWaitingForResponseFromServerText: "等待伺服器響應...",
        ProgressDialogWaitingForRetryText: "正在等待重試...",
        RemoveIconTooltipText: "刪除",
        RotateIconClockwiseTooltipText: "順時針方向旋轉",
        RotateIconCounterclockwiseTooltipText: "反時針方向旋轉",
        SecondsText: "秒",
        UnixFileSystemRootText: "檔案系統根目錄(Root)",
        UnixHomeDirectoryText: "檔案系統有效目錄(Home)"
    }
}

