// Aurigma Image Uploader Dual 6.x - IUEmbed Script Library
// Swedish Localization
// Copyright(c) Carl Henrikson 2007
// Version 6.1.4.0

//--------------------------------------------------------------------------
//sv_resources class
//--------------------------------------------------------------------------

sv_resources = {
    addParams: IULocalization.addParams,

    Language: "Swedish",

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
        AddFolderDialogButtonCancelText: "Avbryt",
        AddFolderDialogButtonSkipAllText: "Hoppa över alla",
        AddFolderDialogButtonSkipText: "Hoppa över",
        AddFolderDialogTitleText: "Lägger till mappen...",
        AuthenticationRequestBasicText: "[Name] kräver inloggning.",
        AuthenticationRequestButtonCancelText: "Avbryt",
        //IGNORE
        AuthenticationRequestButtonOkText: "OK",
        AuthenticationRequestDomainText: "Domän:",
        AuthenticationRequestLoginText: "Login-namn:",
        AuthenticationRequestNtlmText: "[Name] kräver inloggning.",
        AuthenticationRequestPasswordText: "Lösenord:",
        ButtonAddAllToUploadListText: "Lägg till alla",
        ButtonAddFilesText: "Lägg till filer...",
        ButtonAddFoldersText: "Lägg till mappar...",
        ButtonAddToUploadListText: "Lägg till",
        ButtonAdvancedDetailsCancelText: "Avbryt",

        ButtonCheckAllText: "Markera alla",
        ButtonDeleteFilesText: "", //"Radera filer"
        ButtonDeselectAllText: "Avmarkera alla",
        ButtonPasteText: "", //"Klistra in"
        ButtonRemoveAllFromUploadListText: "Ta bort alla",
        ButtonRemoveFromUploadListText: "Ta bort",
        ButtonSelectAllText: "Markera alla",
        ButtonSendText: "Skicka",
        ButtonStopText: "", //"Avbryt"

        ButtonUncheckAllText: "Avmarkera alla",
        //REVIEW
        CmykImagesAreNotAllowedText: "File is CMYK",
        DescriptionEditorButtonCancelText: "Avbryt",
        //IGNORE
        DescriptionEditorButtonOkText: "OK",

        //To be supplied
        DeleteFilesDialogTitleText: "Confirm File Delete",
        //To be supplied
        DeleteSelectedFilesDialogMessageText: "Are you sure you want to permanently delete selected items?",
        //To be supplied
        DeleteUploadedFilesDialogMessageText: "Are you sure you want to permanently delete uploaded items?",
        DimensionsAreTooLargeText: "Bilden är för stor",
        DimensionsAreTooSmallText: "Bilden är för liten",
        DropFilesHereText: "Släpp filer som ska laddas upp här",
        EditDescriptionText: "Redigera beskrivning...",

        //To be supplied
        ErrorDeletingFilesDialogMessageText: "Could not delete [Name]",
        FileIsTooLargeText: "Filen är för stor.",
        FileIsTooSmallText: "Filen är för liten",
        HoursText: "timmar",
        IncludeSubfoldersText: "Inkludera undermappar",
        KilobytesText: "kilobyte",
        //REVIEW
        LargePreviewGeneratingPreviewText: "Generating preview...",
        //REVIEW
        LargePreviewIconTooltipText: "Preview Thumbnail",
        //REVIEW
        LargePreviewNoPreviewAvailableText: "No preview available.",
        ListColumnFileNameText: "Namn",
        ListColumnFileSizeText: "Storlek",
        ListColumnFileTypeText: "Typ",
        ListColumnLastModifiedText: "Senast ändrad",
        //IGNORE
        ListKilobytesText: "KB",
        LoadingFilesText: "Laddar filer...",
        MegabytesText: "Megabyte",
        MenuAddAllToUploadListText: "Lägg till alla filerna i uppladdningslistan",
        MenuAddToUploadListText: "Lägg till i uppladdningslistan",
        MenuArrangeByModifiedText: "Ändrad",
        MenuArrangeByNameText: "Namn",
        MenuArrangeByPathText: "Sökväg",
        MenuArrangeBySizeText: "Storlek",
        MenuArrangeByText: "Sortera ikoner efter",
        MenuArrangeByTypeText: "Typ",
        MenuArrangeByUnsortedText: "Osorterad",
        MenuDeselectAllText: "Avmarkera alla",
        MenuDetailsText: "Detaljerad lista",
        MenuIconsText: "Ikoner",
        MenuInvertSelectionText: "Invertera markering",
        MenuListText: "Lista",
        MenuRefreshText: "Uppdatera",
        MenuRemoveAllFromUploadListText: "Ta bort alla filer från uppladdningslistan",
        MenuRemoveFromUploadListText: "Ta bort från uppladdningslistan",
        MenuSelectAllText: "Markera alla",
        MenuThumbnailsText: "Miniatyrer",
        //IGNORE
        MessageBoxTitleText: "Image Uploader",
        MessageCannotConnectToInternetText: "Kan inte få kontakt med servern.",
        //REVIEW
        MessageCmykImagesAreNotAllowedText: "CMYK images are not allowed",
        MessageDimensionsAreTooLargeText: "Bilden [Name] kan inte väljas. Bildens dimensioner ([OriginalImageWidth]x[OriginalImageHeight]) är för stora. Bilden skall vara mindre än [MaxImageWidth]x[MaxImageHeight].",
        MessageDimensionsAreTooSmallText: "Bilden [Name] kan inte väljas. Bildens dimensioner ([OriginalImageWidth]x[OriginalImageHeight]) are too small. är för små. Bilden skall större än [MinImageWidth]x[MinImageHeight].",
        MessageFileSizeIsTooSmallText: "Filen [Name] kan inte väljas. Filens storlek är mindre än gränsen ([Limit] kb).",
        MessageMaxFileCountExceededText: "Filen [Name] kan inte väljas. Maximalt antal filer ([Limit]st. filer) har uppnåtts.",
        MessageMaxFileSizeExceededText: "Filen [Name] kan inte väljas. Storleken överstiger ([Limit] Kb).",
        MessageMaxTotalFileSizeExceededText: "Filen [Name] kan inte väljas. Sammanlagda mängden data för uppladdning överskrider gränsen ([Limit] kb).",
        MessageNoInternetSessionWasEstablishedText: "Det går inte att nå servern via internet.",
        MessageNoResponseFromServerText: "Servern svarar inte.",
        MessageRetryOpenFolderText: "Den senast använda mappen är inte tillgänglig. Detta kan bero på att mappen finns på ett lagringsmedium som inte är anslutet. Sätt i mediet igen och klicka på Försök igen eller på Avbryt för att gå vidare",
        MessageServerNotFoundText: "Kunde inte ansluta till servern eller proxyservern [Name]",
        MessageSwitchAnotherFolderWarningText: "Om du fortsätter kommer du att välja en annan mapp. Du förlorar då markeringen av de nu markerade filerna.\n\n För att fortsätta och förlora markeringen, klicka OK.\n För att behålla markeringen och stanna i den aktuella mappen välj Avbryt.",
        MessageUnexpectedErrorText: "Image Uploader stötte på ett problem. Var vänlig kontakta webmastern.",
        MessageUploadCancelledText: "Uppladdningen avbröts",
        MessageUploadCompleteText: "Överföringen klar!",
        MessageUploadFailedText: "Uppladdning misslyckaes. (Kontakten med servern avbröts).",
        MessageUserSpecifiedTimeoutHasExpiredText: "Den användardefinierade gränsen för timeout har nåtts",
        MinutesText: "minuter",
        ProgressDialogCancelButtonText: "Avbryt",
        ProgressDialogCloseButtonText: "Stäng",
        ProgressDialogCloseWhenUploadCompletesText: "Stäng denna dialogruta när överföringen är klar.",
        ProgressDialogEstimatedTimeText: "Beräknad tid kvar:  [Current] av [Total]",
        ProgressDialogPreparingDataText: "Förbereder bilder...",
        ProgressDialogSentText: "Byte sända: [Current] av [Total]",
        ProgressDialogTitleText: "Överför Filer",
        ProgressDialogWaitingForResponseFromServerText: "Väntar på svar från server...",
        ProgressDialogWaitingForRetryText: "väntar för att försöka igen...",
        RemoveIconTooltipText: "Ta bort",
        RotateIconClockwiseTooltipText: "Rotera medsols",
        RotateIconCounterclockwiseTooltipText: "Rotera motsols",
        SecondsText: "sekunder",
        UnixFileSystemRootText: "Filsystem",
        UnixHomeDirectoryText: "Hemmamapp"
    }
}