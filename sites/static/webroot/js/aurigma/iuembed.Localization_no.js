// Aurigma Image Uploader Dual 6.x - IUEmbed Script Library
// Norwegian Localization
// Copyright(c) Aurigma Inc. 2002-2007
// Version 6.1.4.0

//--------------------------------------------------------------------------
//no_resources class
//--------------------------------------------------------------------------

no_resources = {
    addParams: IULocalization.addParams,

    Language: "Norwegian",

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
        AddFolderDialogButtonSkipAllText: "Avbryt alle",
        AddFolderDialogButtonSkipText: "Hopp over",
        AddFolderDialogTitleText: "Legger til mappe...",
        AuthenticationRequestBasicText: "[Name] krever godkjenning.",
        AuthenticationRequestButtonCancelText: "Avbryt",
        //IGNORE
        AuthenticationRequestButtonOkText: "OK",
        AuthenticationRequestDomainText: "Domene:",
        AuthenticationRequestLoginText: "Logg inn:",
        AuthenticationRequestNtlmText: "[Name] krever godkjenning.",
        AuthenticationRequestPasswordText: "Passord:",
        ButtonAddAllToUploadListText: "Legg til alle",
        ButtonAddFilesText: "Legg til filer...",
        ButtonAddFoldersText: "Legg til mapper...",
        ButtonAddToUploadListText: "Legg til",
        ButtonAdvancedDetailsCancelText: "Avbryt",

        ButtonCheckAllText: "Marker alle",
        ButtonDeleteFilesText: "", //"Slett filer"
        ButtonDeselectAllText: "Fjern markering",
        ButtonPasteText: "", //"Paste"
        ButtonRemoveAllFromUploadListText: "Fjern alle",
        ButtonRemoveFromUploadListText: "Fjern",
        ButtonSelectAllText: "Marker alle",
        //REVIEW
        ButtonSendText: "Start overføring",
        ButtonStopText: "", //"Avbryt"

        ButtonUncheckAllText: "Fjern markering",
        //REVIEW
        CmykImagesAreNotAllowedText: "Filen er i CMYK",
        DescriptionEditorButtonCancelText: "Avbryt",
        //IGNORE
        DescriptionEditorButtonOkText: "OK",

        //To be supplied
        DeleteFilesDialogTitleText: "Bekfeft sletting",
        //To be supplied
        DeleteSelectedFilesDialogMessageText: "Are you sure you want to permanently delete selected items?",
        //To be supplied
        DeleteUploadedFilesDialogMessageText: "Are you sure you want to permanently delete uploaded items?",
        DimensionsAreTooLargeText: "Bildet er for stort",
        DimensionsAreTooSmallText: "Bildet er for lite",
        DropFilesHereText: "Dra og slipp filer som skal lastes opp her",
        EditDescriptionText: "Rediger beskrivelse...",

        //To be supplied
        ErrorDeletingFilesDialogMessageText: "Could not delete [Name]",
        FileIsTooLargeText: "Filen er for stor.",
        FileIsTooSmallText: "Filen er for liten",
        HoursText: "timer",
        IncludeSubfoldersText: "Inkluder undermapper",
        KilobytesText: "kilobyte",
        //REVIEW
        LargePreviewGeneratingPreviewText: "Generating preview...",
        //REVIEW
        LargePreviewIconTooltipText: "Preview Thumbnail",
        //REVIEW
        LargePreviewNoPreviewAvailableText: "Ingen forhåndsvisning tilgjengelig",
        ListColumnFileNameText: "Navn",
        ListColumnFileSizeText: "Størrelse",
        //IGNORE
        ListColumnFileTypeText: "Type",
        ListColumnLastModifiedText: "Sist endret",
        //IGNORE
        ListKilobytesText: "KB",
        LoadingFilesText: "Laster filer...",
        MegabytesText: "megabyte",
        MenuAddAllToUploadListText: "Legg til alle",
        MenuAddToUploadListText: "Legg til",
        MenuArrangeByModifiedText: "Endret",
        MenuArrangeByNameText: "Navn",
        MenuArrangeByPathText: "Bane",
        MenuArrangeBySizeText: "Størrelse",
        MenuArrangeByText: "Arranger ikoner etter",
        //IGNORE
        MenuArrangeByTypeText: "Type",
        MenuArrangeByUnsortedText: "Usoretert",
        MenuDeselectAllText: "Avmarker alle",
        MenuDetailsText: "Detaljert liste",
        MenuIconsText: "Ikoner",
        MenuInvertSelectionText: "Inverter markering",
        MenuListText: "Liste",
        MenuRefreshText: "Oppdater",
        MenuRemoveAllFromUploadListText: "Fjern alle",
        MenuRemoveFromUploadListText: "Fjern",
        MenuSelectAllText: "Marker alle",
        MenuThumbnailsText: "Miniatyrer",
        //IGNORE
        MessageBoxTitleText: "Image Uploader",
        MessageCannotConnectToInternetText: "Kan ikke oppnå kontakt med serveren.",
        //REVIEW
        MessageCmykImagesAreNotAllowedText: "CMYK images are not allowed",
        MessageDimensionsAreTooLargeText: "Bildet [Name] kan ikke velges. Bildets dimensjoner ([OriginalImageWidth]x[OriginalImageHeight]) er for store. Bildet må være mindre enn [MaxImageWidth]x[MaxImageHeight].",
        MessageDimensionsAreTooSmallText: "Bildet [Name] kan ikke velges. Bildets dimensjoner ([OriginalImageWidth]x[OriginalImageHeight]) er for små. Bildet må være større enn [MinImageWidth]x[MinImageHeight].",
        MessageFileSizeIsTooSmallText: "Filen [Name] kan ikke velges. Filens størrelse er mindre enn minimums grensen ([Limit] kb).", //Maksimalt antal filer er oppnådd.
        MessageMaxFileCountExceededText: "Filen [Name] kan ikke velges. Antallet overstiger maksimums grensen som er ([Limit] filer).",
        MessageMaxFileSizeExceededText: "Filen [Name] kan ikke velges. Størrelsen overstiger ([Limit] Kb).", //Maksimal samlet mengde filer er oppnådd.
        MessageMaxTotalFileSizeExceededText: "Filen [Name] kan ikke velges. Den totale opplastnings størrelse overstiger grensen på ([Limit] kb).",
        MessageNoInternetSessionWasEstablishedText: "Det oppnås ikke kontakt med serveren via internet.",
        MessageNoResponseFromServerText: "Serveren svarer ikke.",
        MessageRetryOpenFolderText: "Den forrige mappen er utilgjengelig. Det er mulig den var tilgjengelig på en ekstern lagringsplass. Forsøk å koble til den eksterne lagringsplassen på nytt og klikk på Forsøk på nytt eller klikk på Avbryt for å fortsette.",
        MessageServerNotFoundText: "Serveren eller proxy [Name] ble ikke funnet.",
        MessageSwitchAnotherFolderWarningText: "Du bytter nå mappe. Dette vil fjerne alle valgene dine.\n\nFor å fortsette og miste alle valgene klikk OK.\nFor å beholde valgene og bli værende i samme mappe, klikk Avbryt.",
        MessageUnexpectedErrorText: "Opplastingen ble avbrutt, du kan forsøke å fortsette opplastingen med Overfør knappen.",
        MessageUploadCancelledText: "Opplasting avbrutt.",
        MessageUploadCompleteText: "Overføringen er ferdig!",
        MessageUploadFailedText: "Opplasting misslyktes. (Kontakten med serveren ble avbrutt).",
        MessageUserSpecifiedTimeoutHasExpiredText: "Brukerdefinert timeout har utløpt.",
        MinutesText: "minutter",
        ProgressDialogCancelButtonText: "Avbryt",
        ProgressDialogCloseButtonText: "Lukk",
        ProgressDialogCloseWhenUploadCompletesText: "Avslutt når overføringen er ferdig.",
        ProgressDialogEstimatedTimeText: "Beregnet gjenstående  tid:  [Current] av [Total]",
        ProgressDialogPreparingDataText: "Forbereder data...",
        ProgressDialogSentText: "Byte sendt: [Current] av [Total]",
        ProgressDialogTitleText: "Overfør Filer",
        ProgressDialogWaitingForResponseFromServerText: "Venter på svar fra server...",
        ProgressDialogWaitingForRetryText: "Venter på nytt forsøk...",
        RemoveIconTooltipText: "Fjern",
        RotateIconClockwiseTooltipText: "Roter med klokken",
        RotateIconCounterclockwiseTooltipText: "Roter mot klokken",
        SecondsText: "sekunder",
        //REVIEW
        UnixFileSystemRootText: "Filesystem",
        //REVIEW
        UnixHomeDirectoryText: "Home directory"
    }
}