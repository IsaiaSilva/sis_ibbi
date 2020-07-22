<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg11.php" ?>
<?php include_once "ewmysql11.php" ?>
<?php include_once "phpfn11.php" ?>
<?php include_once "igrejasinfo.php" ?>
<?php include_once "usuariosinfo.php" ?>
<?php include_once "membrogridcls.php" ?>
<?php include_once "userfn11.php" ?>
<?php

//
// Page class
//

$igrejas_view = NULL; // Initialize page object first

class cigrejas_view extends cigrejas {

	// Page ID
	var $PageID = 'view';

	// Project ID
	var $ProjectID = "{2B7992FC-5911-46A7-9310-01F4D4225C49}";

	// Table name
	var $TableName = 'igrejas';

	// Page object name
	var $PageObjName = 'igrejas_view';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}

	// Page URLs
	var $AddUrl;
	var $EditUrl;
	var $CopyUrl;
	var $DeleteUrl;
	var $ViewUrl;
	var $ListUrl;

	// Export URLs
	var $ExportPrintUrl;
	var $ExportHtmlUrl;
	var $ExportExcelUrl;
	var $ExportWordUrl;
	var $ExportXmlUrl;
	var $ExportCsvUrl;
	var $ExportPdfUrl;

	// Custom export
	var $ExportExcelCustom = FALSE;
	var $ExportWordCustom = FALSE;
	var $ExportPdfCustom = FALSE;
	var $ExportEmailCustom = FALSE;

	// Update URLs
	var $InlineAddUrl;
	var $InlineCopyUrl;
	var $InlineEditUrl;
	var $GridAddUrl;
	var $GridEditUrl;
	var $MultiDeleteUrl;
	var $MultiUpdateUrl;

	// Message
	function getMessage() {
		return @$_SESSION[EW_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
	}

	// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-info ewInfo\">" . $sMessage . "</div>";
			$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EW_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-danger ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EW_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<div class=\"ewMessageDialog\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") { // Header exists, display
			echo "<p>" . $sHeader . "</p>";
		}
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") { // Footer exists, display
			echo "<p>" . $sFooter . "</p>";
		}
	}

	// Validate page request
	function IsPageRequest() {
		global $objForm;
		if ($this->UseTokenInUrl) {
			if ($objForm)
				return ($this->TableVar == $objForm->GetValue("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == $_GET["t"]);
		} else {
			return TRUE;
		}
	}
	var $Token = "";
	var $CheckToken = EW_CHECK_TOKEN;
	var $CheckTokenFn = "ew_CheckToken";
	var $CreateTokenFn = "ew_CreateToken";

	// Valid Post
	function ValidPost() {
		if (!$this->CheckToken || !ew_IsHttpPost())
			return TRUE;
		if (!isset($_POST[EW_TOKEN_NAME]))
			return FALSE;
		$fn = $this->CheckTokenFn;
		if (is_callable($fn))
			return $fn($_POST[EW_TOKEN_NAME]);
		return FALSE;
	}

	// Create Token
	function CreateToken() {
		global $gsToken;
		if ($this->CheckToken) {
			$fn = $this->CreateTokenFn;
			if ($this->Token == "" && is_callable($fn)) // Create token
				$this->Token = $fn();
			$gsToken = $this->Token; // Save to global variable
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $Language;
		$GLOBALS["Page"] = &$this;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (igrejas)
		if (!isset($GLOBALS["igrejas"]) || get_class($GLOBALS["igrejas"]) == "cigrejas") {
			$GLOBALS["igrejas"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["igrejas"];
		}
		$KeyUrl = "";
		if (@$_GET["Id_igreja"] <> "") {
			$this->RecKey["Id_igreja"] = $_GET["Id_igreja"];
			$KeyUrl .= "&amp;Id_igreja=" . urlencode($this->RecKey["Id_igreja"]);
		}
		$this->ExportPrintUrl = $this->PageUrl() . "export=print" . $KeyUrl;
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html" . $KeyUrl;
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel" . $KeyUrl;
		$this->ExportWordUrl = $this->PageUrl() . "export=word" . $KeyUrl;
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml" . $KeyUrl;
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv" . $KeyUrl;
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf" . $KeyUrl;

		// Table object (usuarios)
		if (!isset($GLOBALS['usuarios'])) $GLOBALS['usuarios'] = new cusuarios();

		// User table object (usuarios)
		if (!isset($GLOBALS["UserTable"])) $GLOBALS["UserTable"] = new cusuarios();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'view', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'igrejas', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();

		// Export options
		$this->ExportOptions = new cListOptions();
		$this->ExportOptions->Tag = "div";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Other options
		$this->OtherOptions['action'] = new cListOptions();
		$this->OtherOptions['action']->Tag = "div";
		$this->OtherOptions['action']->TagClassName = "ewActionOption";
		$this->OtherOptions['detail'] = new cListOptions();
		$this->OtherOptions['detail']->Tag = "div";
		$this->OtherOptions['detail']->TagClassName = "ewDetailOption";
	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsCustomExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

		// Security
		$Security = new cAdvancedSecurity();
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
		if (!$Security->IsLoggedIn()) {
			$Security->SaveLastUrl();
			$this->Page_Terminate(ew_GetUrl("login.php"));
		}
		$Security->TablePermission_Loading();
		$Security->LoadCurrentUserLevel($this->ProjectID . $this->TableName);
		$Security->TablePermission_Loaded();
		if (!$Security->IsLoggedIn()) {
			$Security->SaveLastUrl();
			$this->Page_Terminate(ew_GetUrl("login.php"));
		}
		if (!$Security->CanView()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate(ew_GetUrl("igrejaslist.php"));
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Check token
		if (!$this->ValidPost()) {
			echo $Language->Phrase("InvalidPostRequest");
			$this->Page_Terminate();
			exit();
		}

		// Process auto fill
		if (@$_POST["ajax"] == "autofill") {

			// Process auto fill for detail table 'membro'
			if (@$_POST["grid"] == "fmembrogrid") {
				if (!isset($GLOBALS["membro_grid"])) $GLOBALS["membro_grid"] = new cmembro_grid;
				$GLOBALS["membro_grid"]->Page_Init();
				$this->Page_Terminate();
				exit();
			}
			$results = $this->GetAutoFill(@$_POST["name"], @$_POST["q"]);
			if ($results) {

				// Clean output buffer
				if (!EW_DEBUG_ENABLED && ob_get_length())
					ob_end_clean();
				echo $results;
				$this->Page_Terminate();
				exit();
			}
		}

		// Create Token
		$this->CreateToken();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $conn, $gsExportFile, $gTmpImages;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();

		// Export
		global $EW_EXPORT, $igrejas;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($igrejas);
				$doc->Text = $sContent;
				if ($this->Export == "email")
					echo $this->ExportEmail($doc->Text);
				else
					$doc->Export();
				ew_DeleteTmpImages(); // Delete temp images
				exit();
			}
		}
		$this->Page_Redirecting($url);

		 // Close connection
		$conn->Close();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}
	var $ExportOptions; // Export options
	var $OtherOptions = array(); // Other options
	var $DisplayRecs = 1;
	var $DbMasterFilter;
	var $DbDetailFilter;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $RecCnt;
	var $RecKey = array();
	var $membro_Count;
	var $Recordset;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;

		// Load current record
		$bLoadCurrentRecord = FALSE;
		$sReturnUrl = "";
		$bMatchRecord = FALSE;

		// Set up Breadcrumb
		if ($this->Export == "")
			$this->SetupBreadcrumb();
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET["Id_igreja"] <> "") {
				$this->Id_igreja->setQueryStringValue($_GET["Id_igreja"]);
				$this->RecKey["Id_igreja"] = $this->Id_igreja->QueryStringValue;
			} else {
				$sReturnUrl = "igrejaslist.php"; // Return to list
			}

			// Get action
			$this->CurrentAction = "I"; // Display form
			switch ($this->CurrentAction) {
				case "I": // Get a record to display
					if (!$this->LoadRow()) { // Load record based on key
						if ($this->getSuccessMessage() == "" && $this->getFailureMessage() == "")
							$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
						$sReturnUrl = "igrejaslist.php"; // No matching record, return to list
					}
			}
		} else {
			$sReturnUrl = "igrejaslist.php"; // Not page request, return to list
		}
		if ($sReturnUrl <> "")
			$this->Page_Terminate($sReturnUrl);

		// Render row
		$this->RowType = EW_ROWTYPE_VIEW;
		$this->ResetAttrs();
		$this->RenderRow();

		// Set up detail parameters
		$this->SetUpDetailParms();
	}

	// Set up other options
	function SetupOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
		$option = &$options["action"];

		// Add
		$item = &$option->Add("add");
		$item->Body = "<a class=\"ewAction ewAdd\" title=\"" . ew_HtmlTitle($Language->Phrase("ViewPageAddLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("ViewPageAddLink")) . "\" href=\"" . ew_HtmlEncode($this->AddUrl) . "\">" . $Language->Phrase("ViewPageAddLink") . "</a>";
		$item->Visible = ($this->AddUrl <> "" && $Security->CanAdd());

		// Edit
		$item = &$option->Add("edit");
		$item->Body = "<a class=\"ewAction ewEdit\" title=\"" . ew_HtmlTitle($Language->Phrase("ViewPageEditLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("ViewPageEditLink")) . "\" href=\"" . ew_HtmlEncode($this->EditUrl) . "\">" . $Language->Phrase("ViewPageEditLink") . "</a>";
		$item->Visible = ($this->EditUrl <> "" && $Security->CanEdit());

		// Delete
		$item = &$option->Add("delete");
		$item->Body = "<a class=\"ewAction ewDelete\" title=\"" . ew_HtmlTitle($Language->Phrase("ViewPageDeleteLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("ViewPageDeleteLink")) . "\" href=\"" . ew_HtmlEncode($this->DeleteUrl) . "\">" . $Language->Phrase("ViewPageDeleteLink") . "</a>";
		$item->Visible = ($this->DeleteUrl <> "" && $Security->CanDelete());
		$option = &$options["detail"];
		$DetailTableLink = "";
		$DetailViewTblVar = "";
		$DetailCopyTblVar = "";
		$DetailEditTblVar = "";

		// "detail_membro"
		$item = &$option->Add("detail_membro");
		$body = $Language->Phrase("DetailLink") . $Language->TablePhrase("membro", "TblCaption");
		$body .= str_replace("%c", $this->membro_Count, $Language->Phrase("DetailCount"));
		$body = "<a class=\"btn btn-primary btn-sm ewRowLink ewDetail\" data-action=\"list\" href=\"" . ew_HtmlEncode("membrolist.php?" . EW_TABLE_SHOW_MASTER . "=igrejas&fk_Id_igreja=" . urlencode(strval($this->Id_igreja->CurrentValue)) . "") . "\"><i class='glyphicon glyphicon-th-list'></i> " . $body . "</a>";
		$links = "";
		if ($GLOBALS["membro_grid"] && $GLOBALS["membro_grid"]->DetailView && $Security->CanView() && $Security->AllowView(CurrentProjectID() . 'membro')) {
			$links .= "<li><a class=\"ewRowLink ewDetailView\" data-action=\"view\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("MasterDetailViewLink")) . "\" href=\"" . ew_HtmlEncode($this->GetViewUrl(EW_TABLE_SHOW_DETAIL . "=membro")) . "\">" . ew_HtmlImageAndText($Language->Phrase("MasterDetailViewLink")) . "</a></li>";
			if ($DetailViewTblVar <> "") $DetailViewTblVar .= ",";
			$DetailViewTblVar .= "membro";
		}
		if ($GLOBALS["membro_grid"] && $GLOBALS["membro_grid"]->DetailEdit && $Security->CanEdit() && $Security->AllowEdit(CurrentProjectID() . 'membro')) {
			$links .= "<li><a class=\"ewRowLink ewDetailEdit\" data-action=\"edit\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("MasterDetailEditLink")) . "\" href=\"" . ew_HtmlEncode($this->GetEditUrl(EW_TABLE_SHOW_DETAIL . "=membro")) . "\">" . ew_HtmlImageAndText($Language->Phrase("MasterDetailEditLink")) . "</a></li>";
			if ($DetailEditTblVar <> "") $DetailEditTblVar .= ",";
			$DetailEditTblVar .= "membro";
		}
		if ($links <> "") {
			$body .= "<button class=\"dropdown-toggle btn btn-default btn-sm ewDetail\" data-toggle=\"dropdown\"><b class=\"caret\"></b></button>";
			$body .= "<ul class=\"dropdown-menu\">". $links . "</ul>";
		}
		$body = "<div class=\"btn-group\">" . $body . "</div>";
		$item->Body = $body;
		$item->Visible = $Security->AllowList(CurrentProjectID() . 'membro');
		if ($item->Visible) {
			if ($DetailTableLink <> "") $DetailTableLink .= ",";
			$DetailTableLink .= "membro";
		}
		if ($this->ShowMultipleDetails) $item->Visible = FALSE;

		// Multiple details
		if ($this->ShowMultipleDetails) {
			$body = $Language->Phrase("MultipleMasterDetails");
			$body = "<div class=\"btn-group\">";
			$links = "";
			if ($DetailViewTblVar <> "") {
				$links .= "<li><a class=\"ewRowLink ewDetailView\" data-action=\"view\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("MasterDetailViewLink")) . "\" href=\"" . ew_HtmlEncode($this->GetViewUrl(EW_TABLE_SHOW_DETAIL . "=" . $DetailViewTblVar)) . "\">" . ew_HtmlImageAndText($Language->Phrase("MasterDetailViewLink")) . "</a></li>";
			}
			if ($DetailEditTblVar <> "") {
				$links .= "<li><a class=\"ewRowLink ewDetailEdit\" data-action=\"edit\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("MasterDetailEditLink")) . "\" href=\"" . ew_HtmlEncode($this->GetEditUrl(EW_TABLE_SHOW_DETAIL . "=" . $DetailEditTblVar)) . "\">" . ew_HtmlImageAndText($Language->Phrase("MasterDetailEditLink")) . "</a></li>";
			}
			if ($DetailCopyTblVar <> "") {
				$links .= "<li><a class=\"ewRowLink ewDetailCopy\" data-action=\"add\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("MasterDetailCopyLink")) . "\" href=\"" . ew_HtmlEncode($this->GetCopyUrl(EW_TABLE_SHOW_DETAIL . "=" . $DetailCopyTblVar)) . "\">" . ew_HtmlImageAndText($Language->Phrase("MasterDetailCopyLink")) . "</a></li>";
			}
			if ($links <> "") {
				$body .= "<button class=\"dropdown-toggle btn btn-default btn-sm ewMasterDetail\" title=\"" . ew_HtmlTitle($Language->Phrase("MultipleMasterDetails")) . "\" data-toggle=\"dropdown\">" . $Language->Phrase("MultipleMasterDetails") . "<b class=\"caret\"></b></button>";
				$body .= "<ul class=\"dropdown-menu ewMenu\">". $links . "</ul>";
			}
			$body .= "</div>";

			// Multiple details
			$oListOpt = &$option->Add("details");
			$oListOpt->Body = $body;
		}

		// Set up detail default
		$option = &$options["detail"];
		$options["detail"]->DropDownButtonPhrase = $Language->Phrase("ButtonDetails");
		$option->UseImageAndText = TRUE;
		$ar = explode(",", $DetailTableLink);
		$cnt = count($ar);
		$option->UseDropDownButton = ($cnt > 1);
		$option->UseButtonGroup = TRUE;
		$item = &$option->Add($option->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Set up action default
		$option = &$options["action"];
		$option->DropDownButtonPhrase = $Language->Phrase("ButtonActions");
		$option->UseImageAndText = TRUE;
		$option->UseDropDownButton = FALSE;
		$option->UseButtonGroup = TRUE;
		$item = &$option->Add($option->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;
	}

	// Set up starting record parameters
	function SetUpStartRec() {
		if ($this->DisplayRecs == 0)
			return;
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET[EW_TABLE_START_REC] <> "") { // Check for "start" parameter
				$this->StartRec = $_GET[EW_TABLE_START_REC];
				$this->setStartRecordNumber($this->StartRec);
			} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
				$PageNo = $_GET[EW_TABLE_PAGE_NO];
				if (is_numeric($PageNo)) {
					$this->StartRec = ($PageNo-1)*$this->DisplayRecs+1;
					if ($this->StartRec <= 0) {
						$this->StartRec = 1;
					} elseif ($this->StartRec >= intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1) {
						$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1;
					}
					$this->setStartRecordNumber($this->StartRec);
				}
			}
		}
		$this->StartRec = $this->getStartRecordNumber();

		// Check if correct start record counter
		if (!is_numeric($this->StartRec) || $this->StartRec == "") { // Avoid invalid start record counter
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} elseif (intval($this->StartRec) > intval($this->TotalRecs)) { // Avoid starting record > total records
			$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to last page first record
			$this->setStartRecordNumber($this->StartRec);
		} elseif (($this->StartRec-1) % $this->DisplayRecs <> 0) {
			$this->StartRec = intval(($this->StartRec-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to page boundary
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Load row based on key values
	function LoadRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();

		// Call Row Selecting event
		$this->Row_Selecting($sFilter);

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$res = FALSE;
		$rs = ew_LoadRecordset($sSql);
		if ($rs && !$rs->EOF) {
			$res = TRUE;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
		}
		return $res;
	}

	// Load row values from recordset
	function LoadRowValues(&$rs) {
		global $conn;
		if (!$rs || $rs->EOF) return;

		// Call Row Selected event
		$row = &$rs->fields;
		$this->Row_Selected($row);
		$this->Id_igreja->setDbValue($rs->fields('Id_igreja'));
		$this->Igreja->setDbValue($rs->fields('Igreja'));
		$this->CNPJ->setDbValue($rs->fields('CNPJ'));
		$this->Endereco->setDbValue($rs->fields('Endereco'));
		$this->Bairro->setDbValue($rs->fields('Bairro'));
		$this->Cidade->setDbValue($rs->fields('Cidade'));
		$this->UF->setDbValue($rs->fields('UF'));
		$this->CEP->setDbValue($rs->fields('CEP'));
		$this->Telefone1->setDbValue($rs->fields('Telefone1'));
		$this->Telefone2->setDbValue($rs->fields('Telefone2'));
		$this->Fax->setDbValue($rs->fields('Fax'));
		$this->DirigenteResponsavel->setDbValue($rs->fields('DirigenteResponsavel'));
		$this->_Email->setDbValue($rs->fields('Email'));
		$this->Site_Igreja->setDbValue($rs->fields('Site_Igreja'));
		$this->Email_da_igreja->setDbValue($rs->fields('Email_da_igreja'));
		$this->Modelo->setDbValue($rs->fields('Modelo'));
		$this->Data_de_Fundacao->setDbValue($rs->fields('Data_de_Fundacao'));
		if (!isset($GLOBALS["membro_grid"])) $GLOBALS["membro_grid"] = new cmembro_grid;
		$sDetailFilter = $GLOBALS["membro"]->SqlDetailFilter_igrejas();
		$sDetailFilter = str_replace("@Da_Igreja@", ew_AdjustSql($this->Id_igreja->DbValue), $sDetailFilter);
		$GLOBALS["membro"]->setCurrentMasterTable("igrejas");
		$sDetailFilter = $GLOBALS["membro"]->ApplyUserIDFilters($sDetailFilter);
		$this->membro_Count = $GLOBALS["membro"]->LoadRecordCount($sDetailFilter);
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->Id_igreja->DbValue = $row['Id_igreja'];
		$this->Igreja->DbValue = $row['Igreja'];
		$this->CNPJ->DbValue = $row['CNPJ'];
		$this->Endereco->DbValue = $row['Endereco'];
		$this->Bairro->DbValue = $row['Bairro'];
		$this->Cidade->DbValue = $row['Cidade'];
		$this->UF->DbValue = $row['UF'];
		$this->CEP->DbValue = $row['CEP'];
		$this->Telefone1->DbValue = $row['Telefone1'];
		$this->Telefone2->DbValue = $row['Telefone2'];
		$this->Fax->DbValue = $row['Fax'];
		$this->DirigenteResponsavel->DbValue = $row['DirigenteResponsavel'];
		$this->_Email->DbValue = $row['Email'];
		$this->Site_Igreja->DbValue = $row['Site_Igreja'];
		$this->Email_da_igreja->DbValue = $row['Email_da_igreja'];
		$this->Modelo->DbValue = $row['Modelo'];
		$this->Data_de_Fundacao->DbValue = $row['Data_de_Fundacao'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		$this->AddUrl = $this->GetAddUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();
		$this->ListUrl = $this->GetListUrl();
		$this->SetupOtherOptions();

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
		// Id_igreja
		// Igreja
		// CNPJ
		// Endereco
		// Bairro
		// Cidade
		// UF
		// CEP
		// Telefone1
		// Telefone2
		// Fax
		// DirigenteResponsavel
		// Email
		// Site_Igreja
		// Email_da_igreja
		// Modelo
		// Data_de_Fundacao

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// Igreja
			$this->Igreja->ViewValue = $this->Igreja->CurrentValue;
			$this->Igreja->ViewCustomAttributes = "";

			// CNPJ
			$this->CNPJ->ViewValue = $this->CNPJ->CurrentValue;
			$this->CNPJ->ViewCustomAttributes = "";

			// Endereco
			$this->Endereco->ViewValue = $this->Endereco->CurrentValue;
			$this->Endereco->ViewCustomAttributes = "";

			// Bairro
			$this->Bairro->ViewValue = $this->Bairro->CurrentValue;
			$this->Bairro->ViewCustomAttributes = "";

			// Cidade
			$this->Cidade->ViewValue = $this->Cidade->CurrentValue;
			$this->Cidade->ViewCustomAttributes = "";

			// UF
			$this->UF->ViewValue = $this->UF->CurrentValue;
			$this->UF->ViewCustomAttributes = "";

			// CEP
			$this->CEP->ViewValue = $this->CEP->CurrentValue;
			$this->CEP->ViewCustomAttributes = "";

			// Telefone1
			$this->Telefone1->ViewValue = $this->Telefone1->CurrentValue;
			$this->Telefone1->ViewCustomAttributes = "";

			// Telefone2
			$this->Telefone2->ViewValue = $this->Telefone2->CurrentValue;
			$this->Telefone2->ViewCustomAttributes = "";

			// Fax
			$this->Fax->ViewValue = $this->Fax->CurrentValue;
			$this->Fax->ViewCustomAttributes = "";

			// DirigenteResponsavel
			$this->DirigenteResponsavel->ViewValue = $this->DirigenteResponsavel->CurrentValue;
			$this->DirigenteResponsavel->ViewCustomAttributes = "";

			// Email
			$this->_Email->ViewValue = $this->_Email->CurrentValue;
			$this->_Email->ViewCustomAttributes = "";

			// Site_Igreja
			$this->Site_Igreja->ViewValue = $this->Site_Igreja->CurrentValue;
			$this->Site_Igreja->ViewCustomAttributes = "";

			// Email_da_igreja
			$this->Email_da_igreja->ViewValue = $this->Email_da_igreja->CurrentValue;
			$this->Email_da_igreja->ViewCustomAttributes = "";

			// Modelo
			if (strval($this->Modelo->CurrentValue) <> "") {
				$sFilterWrk = "`Id`" . ew_SearchString("=", $this->Modelo->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `Id`, `Modelo` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `modelo_igreja`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Modelo, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `Modelo` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->Modelo->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->Modelo->ViewValue = $this->Modelo->CurrentValue;
				}
			} else {
				$this->Modelo->ViewValue = NULL;
			}
			$this->Modelo->ViewCustomAttributes = "";

			// Data_de_Fundacao
			$this->Data_de_Fundacao->ViewValue = $this->Data_de_Fundacao->CurrentValue;
			$this->Data_de_Fundacao->ViewValue = ew_FormatDateTime($this->Data_de_Fundacao->ViewValue, 7);
			$this->Data_de_Fundacao->ViewCustomAttributes = "";

			// Igreja
			$this->Igreja->LinkCustomAttributes = "";
			$this->Igreja->HrefValue = "";
			$this->Igreja->TooltipValue = "";

			// CNPJ
			$this->CNPJ->LinkCustomAttributes = "";
			$this->CNPJ->HrefValue = "";
			$this->CNPJ->TooltipValue = "";

			// Endereco
			$this->Endereco->LinkCustomAttributes = "";
			$this->Endereco->HrefValue = "";
			$this->Endereco->TooltipValue = "";

			// Bairro
			$this->Bairro->LinkCustomAttributes = "";
			$this->Bairro->HrefValue = "";
			$this->Bairro->TooltipValue = "";

			// Cidade
			$this->Cidade->LinkCustomAttributes = "";
			$this->Cidade->HrefValue = "";
			$this->Cidade->TooltipValue = "";

			// UF
			$this->UF->LinkCustomAttributes = "";
			$this->UF->HrefValue = "";
			$this->UF->TooltipValue = "";

			// CEP
			$this->CEP->LinkCustomAttributes = "";
			$this->CEP->HrefValue = "";
			$this->CEP->TooltipValue = "";

			// Telefone1
			$this->Telefone1->LinkCustomAttributes = "";
			$this->Telefone1->HrefValue = "";
			$this->Telefone1->TooltipValue = "";

			// Telefone2
			$this->Telefone2->LinkCustomAttributes = "";
			$this->Telefone2->HrefValue = "";
			$this->Telefone2->TooltipValue = "";

			// Fax
			$this->Fax->LinkCustomAttributes = "";
			$this->Fax->HrefValue = "";
			$this->Fax->TooltipValue = "";

			// DirigenteResponsavel
			$this->DirigenteResponsavel->LinkCustomAttributes = "";
			$this->DirigenteResponsavel->HrefValue = "";
			$this->DirigenteResponsavel->TooltipValue = "";

			// Email
			$this->_Email->LinkCustomAttributes = "";
			$this->_Email->HrefValue = "";
			$this->_Email->TooltipValue = "";

			// Site_Igreja
			$this->Site_Igreja->LinkCustomAttributes = "";
			$this->Site_Igreja->HrefValue = "";
			$this->Site_Igreja->TooltipValue = "";

			// Email_da_igreja
			$this->Email_da_igreja->LinkCustomAttributes = "";
			$this->Email_da_igreja->HrefValue = "";
			$this->Email_da_igreja->TooltipValue = "";

			// Modelo
			$this->Modelo->LinkCustomAttributes = "";
			$this->Modelo->HrefValue = "";
			$this->Modelo->TooltipValue = "";

			// Data_de_Fundacao
			$this->Data_de_Fundacao->LinkCustomAttributes = "";
			$this->Data_de_Fundacao->HrefValue = "";
			$this->Data_de_Fundacao->TooltipValue = "";
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Set up detail parms based on QueryString
	function SetUpDetailParms() {

		// Get the keys for master table
		if (isset($_GET[EW_TABLE_SHOW_DETAIL])) {
			$sDetailTblVar = $_GET[EW_TABLE_SHOW_DETAIL];
			$this->setCurrentDetailTable($sDetailTblVar);
		} else {
			$sDetailTblVar = $this->getCurrentDetailTable();
		}
		if ($sDetailTblVar <> "") {
			$DetailTblVar = explode(",", $sDetailTblVar);
			if (in_array("membro", $DetailTblVar)) {
				if (!isset($GLOBALS["membro_grid"]))
					$GLOBALS["membro_grid"] = new cmembro_grid;
				if ($GLOBALS["membro_grid"]->DetailView) {
					$GLOBALS["membro_grid"]->CurrentMode = "view";

					// Save current master table to detail table
					$GLOBALS["membro_grid"]->setCurrentMasterTable($this->TableVar);
					$GLOBALS["membro_grid"]->setStartRecordNumber(1);
					$GLOBALS["membro_grid"]->Da_Igreja->FldIsDetailKey = TRUE;
					$GLOBALS["membro_grid"]->Da_Igreja->CurrentValue = $this->Id_igreja->CurrentValue;
					$GLOBALS["membro_grid"]->Da_Igreja->setSessionValue($GLOBALS["membro_grid"]->Da_Igreja->CurrentValue);
				}
			}
		}
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, "igrejaslist.php", "", $this->TableVar, TRUE);
		$PageId = "view";
		$Breadcrumb->Add("view", $PageId, $url);
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Page Redirecting event
	function Page_Redirecting(&$url) {

		// Example:
		//$url = "your URL";

	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	function Page_DataRendering(&$header) {

		//$header = $this->setMessage("your header");
	}

	function Page_DataRendered(&$footer) {

		//$footer = $this->setMessage("your footer");
	}

	// Page Exporting event
	// $this->ExportDoc = export document object
	function Page_Exporting() {

		//$this->ExportDoc->Text = "my header"; // Export header
		//return FALSE; // Return FALSE to skip default export and use Row_Export event

		return TRUE; // Return TRUE to use default export and skip Row_Export event
	}

	// Row Export event
	// $this->ExportDoc = export document object
	function Row_Export($rs) {

	    //$this->ExportDoc->Text .= "my content"; // Build HTML with field value: $rs["MyField"] or $this->MyField->ViewValue
	}

	// Page Exported event
	// $this->ExportDoc = export document object
	function Page_Exported() {

		//$this->ExportDoc->Text .= "my footer"; // Export footer
		//echo $this->ExportDoc->Text;

	}
}
?>
<?php ew_Header(TRUE) ?>
<?php

// Create page object
if (!isset($igrejas_view)) $igrejas_view = new cigrejas_view();

// Page init
$igrejas_view->Page_Init();

// Page main
$igrejas_view->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$igrejas_view->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var igrejas_view = new ew_Page("igrejas_view");
igrejas_view.PageID = "view"; // Page ID
var EW_PAGE_ID = igrejas_view.PageID; // For backward compatibility

// Form object
var figrejasview = new ew_Form("figrejasview");

// Form_CustomValidate event
figrejasview.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
figrejasview.ValidateRequired = true;
<?php } else { ?>
figrejasview.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
figrejasview.Lists["x_Modelo"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Modelo","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php $igrejas_view->ExportOptions->Render("body") ?>
<?php
	foreach ($igrejas_view->OtherOptions as &$option)
		$option->Render("body");
?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php $igrejas_view->ShowPageHeader(); ?>
<?php
$igrejas_view->ShowMessage();
?>
<form name="figrejasview" id="figrejasview" class="form-inline ewForm ewViewForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($igrejas_view->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $igrejas_view->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="igrejas">
<table class="table table-bordered table-striped ewViewTable">
<?php if ($igrejas->Igreja->Visible) { // Igreja ?>
	<tr id="r_Igreja">
		<td><span id="elh_igrejas_Igreja"><?php echo $igrejas->Igreja->FldCaption() ?></span></td>
		<td<?php echo $igrejas->Igreja->CellAttributes() ?>>
<span id="el_igrejas_Igreja" class="form-group">
<span<?php echo $igrejas->Igreja->ViewAttributes() ?>>
<?php echo $igrejas->Igreja->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($igrejas->CNPJ->Visible) { // CNPJ ?>
	<tr id="r_CNPJ">
		<td><span id="elh_igrejas_CNPJ"><?php echo $igrejas->CNPJ->FldCaption() ?></span></td>
		<td<?php echo $igrejas->CNPJ->CellAttributes() ?>>
<span id="el_igrejas_CNPJ" class="form-group">
<span<?php echo $igrejas->CNPJ->ViewAttributes() ?>>
<?php echo $igrejas->CNPJ->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($igrejas->Endereco->Visible) { // Endereco ?>
	<tr id="r_Endereco">
		<td><span id="elh_igrejas_Endereco"><?php echo $igrejas->Endereco->FldCaption() ?></span></td>
		<td<?php echo $igrejas->Endereco->CellAttributes() ?>>
<span id="el_igrejas_Endereco" class="form-group">
<span<?php echo $igrejas->Endereco->ViewAttributes() ?>>
<?php echo $igrejas->Endereco->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($igrejas->Bairro->Visible) { // Bairro ?>
	<tr id="r_Bairro">
		<td><span id="elh_igrejas_Bairro"><?php echo $igrejas->Bairro->FldCaption() ?></span></td>
		<td<?php echo $igrejas->Bairro->CellAttributes() ?>>
<span id="el_igrejas_Bairro" class="form-group">
<span<?php echo $igrejas->Bairro->ViewAttributes() ?>>
<?php echo $igrejas->Bairro->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($igrejas->Cidade->Visible) { // Cidade ?>
	<tr id="r_Cidade">
		<td><span id="elh_igrejas_Cidade"><?php echo $igrejas->Cidade->FldCaption() ?></span></td>
		<td<?php echo $igrejas->Cidade->CellAttributes() ?>>
<span id="el_igrejas_Cidade" class="form-group">
<span<?php echo $igrejas->Cidade->ViewAttributes() ?>>
<?php echo $igrejas->Cidade->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($igrejas->UF->Visible) { // UF ?>
	<tr id="r_UF">
		<td><span id="elh_igrejas_UF"><?php echo $igrejas->UF->FldCaption() ?></span></td>
		<td<?php echo $igrejas->UF->CellAttributes() ?>>
<span id="el_igrejas_UF" class="form-group">
<span<?php echo $igrejas->UF->ViewAttributes() ?>>
<?php echo $igrejas->UF->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($igrejas->CEP->Visible) { // CEP ?>
	<tr id="r_CEP">
		<td><span id="elh_igrejas_CEP"><?php echo $igrejas->CEP->FldCaption() ?></span></td>
		<td<?php echo $igrejas->CEP->CellAttributes() ?>>
<span id="el_igrejas_CEP" class="form-group">
<span<?php echo $igrejas->CEP->ViewAttributes() ?>>
<?php echo $igrejas->CEP->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($igrejas->Telefone1->Visible) { // Telefone1 ?>
	<tr id="r_Telefone1">
		<td><span id="elh_igrejas_Telefone1"><?php echo $igrejas->Telefone1->FldCaption() ?></span></td>
		<td<?php echo $igrejas->Telefone1->CellAttributes() ?>>
<span id="el_igrejas_Telefone1" class="form-group">
<span<?php echo $igrejas->Telefone1->ViewAttributes() ?>>
<?php echo $igrejas->Telefone1->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($igrejas->Telefone2->Visible) { // Telefone2 ?>
	<tr id="r_Telefone2">
		<td><span id="elh_igrejas_Telefone2"><?php echo $igrejas->Telefone2->FldCaption() ?></span></td>
		<td<?php echo $igrejas->Telefone2->CellAttributes() ?>>
<span id="el_igrejas_Telefone2" class="form-group">
<span<?php echo $igrejas->Telefone2->ViewAttributes() ?>>
<?php echo $igrejas->Telefone2->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($igrejas->Fax->Visible) { // Fax ?>
	<tr id="r_Fax">
		<td><span id="elh_igrejas_Fax"><?php echo $igrejas->Fax->FldCaption() ?></span></td>
		<td<?php echo $igrejas->Fax->CellAttributes() ?>>
<span id="el_igrejas_Fax" class="form-group">
<span<?php echo $igrejas->Fax->ViewAttributes() ?>>
<?php echo $igrejas->Fax->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($igrejas->DirigenteResponsavel->Visible) { // DirigenteResponsavel ?>
	<tr id="r_DirigenteResponsavel">
		<td><span id="elh_igrejas_DirigenteResponsavel"><?php echo $igrejas->DirigenteResponsavel->FldCaption() ?></span></td>
		<td<?php echo $igrejas->DirigenteResponsavel->CellAttributes() ?>>
<span id="el_igrejas_DirigenteResponsavel" class="form-group">
<span<?php echo $igrejas->DirigenteResponsavel->ViewAttributes() ?>>
<?php echo $igrejas->DirigenteResponsavel->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($igrejas->_Email->Visible) { // Email ?>
	<tr id="r__Email">
		<td><span id="elh_igrejas__Email"><?php echo $igrejas->_Email->FldCaption() ?></span></td>
		<td<?php echo $igrejas->_Email->CellAttributes() ?>>
<span id="el_igrejas__Email" class="form-group">
<span<?php echo $igrejas->_Email->ViewAttributes() ?>>
<?php echo $igrejas->_Email->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($igrejas->Site_Igreja->Visible) { // Site_Igreja ?>
	<tr id="r_Site_Igreja">
		<td><span id="elh_igrejas_Site_Igreja"><?php echo $igrejas->Site_Igreja->FldCaption() ?></span></td>
		<td<?php echo $igrejas->Site_Igreja->CellAttributes() ?>>
<span id="el_igrejas_Site_Igreja" class="form-group">
<span<?php echo $igrejas->Site_Igreja->ViewAttributes() ?>>
<?php echo $igrejas->Site_Igreja->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($igrejas->Email_da_igreja->Visible) { // Email_da_igreja ?>
	<tr id="r_Email_da_igreja">
		<td><span id="elh_igrejas_Email_da_igreja"><?php echo $igrejas->Email_da_igreja->FldCaption() ?></span></td>
		<td<?php echo $igrejas->Email_da_igreja->CellAttributes() ?>>
<span id="el_igrejas_Email_da_igreja" class="form-group">
<span<?php echo $igrejas->Email_da_igreja->ViewAttributes() ?>>
<?php echo $igrejas->Email_da_igreja->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($igrejas->Modelo->Visible) { // Modelo ?>
	<tr id="r_Modelo">
		<td><span id="elh_igrejas_Modelo"><?php echo $igrejas->Modelo->FldCaption() ?></span></td>
		<td<?php echo $igrejas->Modelo->CellAttributes() ?>>
<span id="el_igrejas_Modelo" class="form-group">
<span<?php echo $igrejas->Modelo->ViewAttributes() ?>>
<?php echo $igrejas->Modelo->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($igrejas->Data_de_Fundacao->Visible) { // Data_de_Fundacao ?>
	<tr id="r_Data_de_Fundacao">
		<td><span id="elh_igrejas_Data_de_Fundacao"><?php echo $igrejas->Data_de_Fundacao->FldCaption() ?></span></td>
		<td<?php echo $igrejas->Data_de_Fundacao->CellAttributes() ?>>
<span id="el_igrejas_Data_de_Fundacao" class="form-group">
<span<?php echo $igrejas->Data_de_Fundacao->ViewAttributes() ?>>
<?php echo $igrejas->Data_de_Fundacao->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
</table>
<?php
	if (in_array("membro", explode(",", $igrejas->getCurrentDetailTable())) && $membro->DetailView) {
?>
<?php if ($igrejas->getCurrentDetailTable() <> "") { ?>
<h4 class="ewDetailCaption"><?php echo $Language->TablePhrase("membro", "TblCaption") ?></h4>
<?php } ?>
<?php include_once "membrogrid.php" ?>
<?php } ?>
</form>
<script type="text/javascript">
figrejasview.Init();
</script>
<?php
$igrejas_view->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$igrejas_view->Page_Terminate();
?>
