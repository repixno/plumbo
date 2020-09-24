﻿// Aurigma Image Uploader Dual 6.x - IUEmbed Script Library
// Copyright(c) Aurigma Inc. 2002-2009
// Version 6.1.4.0

//--------------------------------------------------------------------------
//en_resources class
//--------------------------------------------------------------------------

en_resources = {
    addParams: IULocalization.addParams,

    Language: "English",

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
        AddFolderDialogButtonCancelText: "Cancel",
        AddFolderDialogButtonSkipAllText: "Skip All",
        AddFolderDialogButtonSkipText: "Skip",
        AddFolderDialogTitleText: "Adding folder...",
        AuthenticationRequestBasicText: "[Name] requires authentication.",
        AuthenticationRequestButtonCancelText: "Cancel",
        AuthenticationRequestButtonOkText: "OK",
        AuthenticationRequestDomainText: "Domain:",
        AuthenticationRequestLoginText: "Login:",
        AuthenticationRequestNtlmText: "[Name] requires authentication.",
        AuthenticationRequestPasswordText: "Password:",
        ButtonAddAllToUploadListText: "Add All",
        ButtonAddFilesText: "Add Files...",
        ButtonAddFoldersText: "Add Folders...",
        ButtonAddToUploadListText: "Add",

        ButtonAdvancedDetailsCancelText: "Cancel",

        ButtonCheckAllText: "Check All",

        ButtonDeleteFilesText: "", //"Delete Files"
        ButtonDeselectAllText: "Deselect All",
        ButtonPasteText: "", //"Paste"
        ButtonRemoveAllFromUploadListText: "Remove All",
        ButtonRemoveFromUploadListText: "Remove",
        ButtonSelectAllText: "Select All",
        ButtonSendText: "Send",

        ButtonStopText: "",

        ButtonUncheckAllText: "Uncheck All",

        CmykImagesAreNotAllowedText: "File is CMYK",
        DescriptionEditorButtonCancelText: "Cancel",
        DescriptionEditorButtonOkText: "OK",

        DeleteFilesDialogTitleText: "Confirm File Delete",

        DeleteSelectedFilesDialogMessageText: "Are you sure you want to permanently delete selected items?",

        DeleteUploadedFilesDialogMessageText: "Are you sure you want to permanently delete uploaded items?",
        DimensionsAreTooLargeText: "Image is too large",
        DimensionsAreTooSmallText: "Image is too small",
        DropFilesHereText: "Drop files here",
        EditDescriptionText: "Edit description...",

        ErrorDeletingFilesDialogMessageText: "Could not delete [Name]",
        FileIsTooLargeText: "File is too large",
        FileIsTooSmallText: "File is too small",
        HoursText: "hours",
        IncludeSubfoldersText: "Include subfolders",
        KilobytesText: "kilobytes",
        LargePreviewGeneratingPreviewText: "Generating preview...",
        LargePreviewIconTooltipText: "Preview Thumbnail",
        LargePreviewNoPreviewAvailableText: "No preview available.",
        ListColumnFileNameText: "Name",
        ListColumnFileSizeText: "Size",
        ListColumnFileTypeText: "Type",
        ListColumnLastModifiedText: "Modified",
        ListKilobytesText: "KB",
        LoadingFilesText: "Loading files...",
        MegabytesText: "megabytes",
        MenuAddAllToUploadListText: "Add All to Upload List",
        MenuAddToUploadListText: "Add to Upload List",
        MenuArrangeByModifiedText: "Modified",
        MenuArrangeByNameText: "Name",
        MenuArrangeByPathText: "Path",
        MenuArrangeBySizeText: "Size",
        MenuArrangeByText: "Arrange Icons By",
        MenuArrangeByTypeText: "Type",
        MenuArrangeByUnsortedText: "Unsorted",
        MenuDeselectAllText: "Deselect All",
        MenuDetailsText: "Details",
        MenuIconsText: "Icons",
        MenuInvertSelectionText: "Invert Selection",
        MenuListText: "List",
        MenuRefreshText: "Refresh",
        MenuRemoveAllFromUploadListText: "Remove All from Upload List",
        MenuRemoveFromUploadListText: "Remove from Upload List",
        MenuSelectAllText: "Select All",
        MenuThumbnailsText: "Thumbnails",
        MessageBoxTitleText: "Image Uploader",
        MessageCannotConnectToInternetText: "The attempt to connect to the Internet has failed.",
        MessageCmykImagesAreNotAllowedText: "CMYK images are not allowed",
        MessageDimensionsAreTooLargeText: "The image [Name] cannot be selected. This image dimensions ([OriginalImageWidth]x[OriginalImageHeight]) are too large. The image should be smaller than [MaxImageWidth]x[MaxImageHeight].",
        MessageDimensionsAreTooSmallText: "The image [Name] cannot be selected. This image dimensions ([OriginalImageWidth]x[OriginalImageHeight]) are too small. The image should be larger than [MinImageWidth]x[MinImageHeight].",
        MessageFileSizeIsTooSmallText: "The file [Name] cannot be selected. This file size is smaller than the limit ([Limit] KB).",
        MessageMaxFileCountExceededText: "The file [Name] cannot be selected. Amount of files exceeds the limit ([Limit] files).",
        MessageMaxFileSizeExceededText: "The file [Name] cannot be selected. This file size exceeds the limit ([Limit] KB).",
        MessageMaxTotalFileSizeExceededText: "The file [Name] cannot be selected. Total upload data size exceeds the limit ([Limit] KB).",
        MessageNoInternetSessionWasEstablishedText: "No Internet session was established.",
        MessageNoResponseFromServerText: "No response from server.",
        MessageRetryOpenFolderText: "Last visited folder is not available. It is possible it is located on a removable media. Insert the media and click Retry button or click Cancel button to continue.",
        MessageServerNotFoundText: "The server or proxy [Name] not found.",
        MessageSwitchAnotherFolderWarningText: "You are about to switch to another folder. This will discard selection from selected files.\n\nTo proceed and lose selection click OK.\nTo keep the selection and stay in the current folder, click Cancel.",
        MessageUnexpectedErrorText: "Image Uploader encountered some problem. If you see this message, contact web master.",
        MessageUploadCancelledText: "Upload is cancelled.",
        MessageUploadCompleteText: "Upload complete.",
        MessageUploadFailedText: "Upload failed (the connection was interrupted).",
        MessageUserSpecifiedTimeoutHasExpiredText: "User-specified timeout has expired.",
        MinutesText: "minutes",
        ProgressDialogCancelButtonText: "Cancel",
        ProgressDialogCloseButtonText: "Close",
        ProgressDialogCloseWhenUploadCompletesText: "Close this dialog box when upload completes",
        ProgressDialogEstimatedTimeText: "Estimated time: [Current] of [Total]",
        ProgressDialogPreparingDataText: "Preparing data...",
        ProgressDialogSentText: "Sent: [Current] of [Total]",
        ProgressDialogTitleText: "Upload Files",
        ProgressDialogWaitingForResponseFromServerText: "Waiting for response from server...",
        ProgressDialogWaitingForRetryText: "Waiting for retry...",
        RemoveIconTooltipText: "Remove",
        RotateIconClockwiseTooltipText: "Rotate Clockwise",
        RotateIconCounterclockwiseTooltipText: "Rotate Counterclockwise",
        SecondsText: "seconds",
        UnixFileSystemRootText: "Filesystem",
        UnixHomeDirectoryText: "Home directory"
    }
}