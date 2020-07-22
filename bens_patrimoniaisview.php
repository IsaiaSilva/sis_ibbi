<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg11.php" ?>
<?php include_once "ewmysql11.php" ?>
<?php include_once "phpfn11.php" ?>
<?php include_once "bens_patrimoniaisinfo.php" ?>
<?php include_once "usuariosinfo.php" ?>
<?php include_once "userfn11.php" ?>
<?php

//
// Page class
//

$bens_patrimoniais_view = NULL; // Initialize page object first

class cbens_patrimoniais_view extends cbens_patrimoniais {

	// Page ID
	var $PageID = 'view';

	// Project ID
	var $ProjectID = "{2B7992FC-5911-46A7-9310-01F4D4225C49}";

	// Table name
	var $TableName = 'bens_patrimoniais';

	// Page object name
	var $PageObjName = 'bens_patrimoniais_view';

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

		// Table object (bens_patrimoniais)
		if (!isset($GLOBALS["bens_patrimoniais"]) || get_class($GLOBALS["bens_patrimoniais"]) == "cbens_patrimoniais") {
			$GLOBALS["bens_patrimoniais"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["bens_patrimoniais"];
		}
		$KeyUrl = "";
		if (@$_GET["Id_Patri"] <> "") {
			$this->RecKey["Id_Patri"] = $_GET["Id_Patri"];
			$KeyUrl .= "&amp;Id_Patri=" . urlencode($this->RecKey["Id_Patri"]);
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
			define("EW_TABLE_NAME", 'bens_patrimoniais', TRUE);

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
			$this->Page_Terminate(ew_GetUrl("bens_patrimoniaislist.php"));
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
		global $EW_EXPORT, $bens_patrimoniais;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($bens_patrimoniais);
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
			if (@$_GET["Id_Patri"] <> "") {
				$this->Id_Patri->setQueryStringValue($_GET["Id_Patri"]);
				$this->RecKey["Id_Patri"] = $this->Id_Patri->QueryStringValue;
			} else {
				$sReturnUrl = "bens_patrimoniaislist.php"; // Return to list
			}

			// Get action
			$this->CurrentAction = "I"; // Display form
			switch ($this->CurrentAction) {
				case "I": // Get a record to display
					if (!$this->LoadRow()) { // Load record based on key
						if ($this->getSuccessMessage() == "" && $this->getFailureMessage() == "")
							$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
						$sReturnUrl = "bens_patrimoniaislist.php"; // No matching record, return to list
					}
			}
		} else {
			$sReturnUrl = "bens_patrimoniaislist.php"; // Not page request, return to list
		}
		if ($sReturnUrl <> "")
			$this->Page_Terminate($sReturnUrl);

		// Render row
		$this->RowType = EW_ROWTYPE_VIEW;
		$this->ResetAttrs();
		$this->RenderRow();
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
		$this->Id_Patri->setDbValue($rs->fields('Id_Patri'));
		$this->Localidade->setDbValue($rs->fields('Localidade'));
		$this->Descricao->setDbValue($rs->fields('Descricao'));
		$this->DataAquisao->setDbValue($rs->fields('DataAquisao'));
		$this->Tipo->setDbValue($rs->fields('Tipo'));
		$this->Estado_do_bem->setDbValue($rs->fields('Estado_do_bem'));
		$this->Valor_estimado->setDbValue($rs->fields('Valor_estimado'));
		$this->Situacao->setDbValue($rs->fields('Situacao'));
		$this->Anotacoes->setDbValue($rs->fields('Anotacoes'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->Id_Patri->DbValue = $row['Id_Patri'];
		$this->Localidade->DbValue = $row['Localidade'];
		$this->Descricao->DbValue = $row['Descricao'];
		$this->DataAquisao->DbValue = $row['DataAquisao'];
		$this->Tipo->DbValue = $row['Tipo'];
		$this->Estado_do_bem->DbValue = $row['Estado_do_bem'];
		$this->Valor_estimado->DbValue = $row['Valor_estimado'];
		$this->Situacao->DbValue = $row['Situacao'];
		$this->Anotacoes->DbValue = $row['Anotacoes'];
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

		// Convert decimal values if posted back
		if ($this->Valor_estimado->FormValue == $this->Valor_estimado->CurrentValue && is_numeric(ew_StrToFloat($this->Valor_estimado->CurrentValue)))
			$this->Valor_estimado->CurrentValue = ew_StrToFloat($this->Valor_estimado->CurrentValue);

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
		// Id_Patri
		// Localidade
		// Descricao
		// DataAquisao
		// Tipo
		// Estado_do_bem
		// Valor_estimado
		// Situacao
		// Anotacoes

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// Localidade
			if (strval($this->Localidade->CurrentValue) <> "") {
				$sFilterWrk = "`Id_igreja`" . ew_SearchString("=", $this->Localidade->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `Id_igreja`, `Igreja` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `igrejas`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Localidade, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `Igreja` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->Localidade->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->Localidade->ViewValue = $this->Localidade->CurrentValue;
				}
			} else {
				$this->Localidade->ViewValue = NULL;
			}
			$this->Localidade->ViewCustomAttributes = "";

			// Descricao
			$this->Descricao->ViewValue = $this->Descricao->CurrentValue;
			$this->Descricao->ViewCustomAttributes = "";

			// DataAquisao
			$this->DataAquisao->ViewValue = $this->DataAquisao->CurrentValue;
			$this->DataAquisao->ViewValue = ew_FormatDateTime($this->DataAquisao->ViewValue, 7);
			$this->DataAquisao->ViewCustomAttributes = "";

			// Tipo
			if (strval($this->Tipo->CurrentValue) <> "") {
				switch ($this->Tipo->CurrentValue) {
					case $this->Tipo->FldTagValue(1):
						$this->Tipo->ViewValue = $this->Tipo->FldTagCaption(1) <> "" ? $this->Tipo->FldTagCaption(1) : $this->Tipo->CurrentValue;
						break;
					case $this->Tipo->FldTagValue(2):
						$this->Tipo->ViewValue = $this->Tipo->FldTagCaption(2) <> "" ? $this->Tipo->FldTagCaption(2) : $this->Tipo->CurrentValue;
						break;
					default:
						$this->Tipo->ViewValue = $this->Tipo->CurrentValue;
				}
			} else {
				$this->Tipo->ViewValue = NULL;
			}
			$this->Tipo->ViewCustomAttributes = "";

			// Estado_do_bem
			if (strval($this->Estado_do_bem->CurrentValue) <> "") {
				$sFilterWrk = "`Id_est_patri`" . ew_SearchString("=", $this->Estado_do_bem->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `Id_est_patri`, `Estado_do_Bem` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `estado_patrimonio`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Estado_do_bem, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->Estado_do_bem->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->Estado_do_bem->ViewValue = $this->Estado_do_bem->CurrentValue;
				}
			} else {
				$this->Estado_do_bem->ViewValue = NULL;
			}
			$this->Estado_do_bem->ViewCustomAttributes = "";

			// Valor_estimado
			$this->Valor_estimado->ViewValue = $this->Valor_estimado->CurrentValue;
			$this->Valor_estimado->ViewValue = ew_FormatCurrency($this->Valor_estimado->ViewValue, 2, -2, -2, -2);
			$this->Valor_estimado->CellCssStyle .= "text-align: right;";
			$this->Valor_estimado->ViewCustomAttributes = "";

			// Situacao
			if (strval($this->Situacao->CurrentValue) <> "") {
				switch ($this->Situacao->CurrentValue) {
					case $this->Situacao->FldTagValue(1):
						$this->Situacao->ViewValue = $this->Situacao->FldTagCaption(1) <> "" ? $this->Situacao->FldTagCaption(1) : $this->Situacao->CurrentValue;
						break;
					case $this->Situacao->FldTagValue(2):
						$this->Situacao->ViewValue = $this->Situacao->FldTagCaption(2) <> "" ? $this->Situacao->FldTagCaption(2) : $this->Situacao->CurrentValue;
						break;
					default:
						$this->Situacao->ViewValue = $this->Situacao->CurrentValue;
				}
			} else {
				$this->Situacao->ViewValue = NULL;
			}
			$this->Situacao->ViewCustomAttributes = "";

			// Anotacoes
			$this->Anotacoes->ViewValue = $this->Anotacoes->CurrentValue;
			$this->Anotacoes->ViewCustomAttributes = "";

			// Localidade
			$this->Localidade->LinkCustomAttributes = "";
			$this->Localidade->HrefValue = "";
			$this->Localidade->TooltipValue = "";

			// Descricao
			$this->Descricao->LinkCustomAttributes = "";
			$this->Descricao->HrefValue = "";
			$this->Descricao->TooltipValue = "";

			// DataAquisao
			$this->DataAquisao->LinkCustomAttributes = "";
			$this->DataAquisao->HrefValue = "";
			$this->DataAquisao->TooltipValue = "";

			// Tipo
			$this->Tipo->LinkCustomAttributes = "";
			$this->Tipo->HrefValue = "";
			$this->Tipo->TooltipValue = "";

			// Estado_do_bem
			$this->Estado_do_bem->LinkCustomAttributes = "";
			$this->Estado_do_bem->HrefValue = "";
			$this->Estado_do_bem->TooltipValue = "";

			// Valor_estimado
			$this->Valor_estimado->LinkCustomAttributes = "";
			$this->Valor_estimado->HrefValue = "";
			$this->Valor_estimado->TooltipValue = "";

			// Situacao
			$this->Situacao->LinkCustomAttributes = "";
			$this->Situacao->HrefValue = "";
			$this->Situacao->TooltipValue = "";

			// Anotacoes
			$this->Anotacoes->LinkCustomAttributes = "";
			$this->Anotacoes->HrefValue = "";
			$this->Anotacoes->TooltipValue = "";
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, "bens_patrimoniaislist.php", "", $this->TableVar, TRUE);
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
if (!isset($bens_patrimoniais_view)) $bens_patrimoniais_view = new cbens_patrimoniais_view();

// Page init
$bens_patrimoniais_view->Page_Init();

// Page main
$bens_patrimoniais_view->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$bens_patrimoniais_view->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var bens_patrimoniais_view = new ew_Page("bens_patrimoniais_view");
bens_patrimoniais_view.PageID = "view"; // Page ID
var EW_PAGE_ID = bens_patrimoniais_view.PageID; // For backward compatibility

// Form object
var fbens_patrimoniaisview = new ew_Form("fbens_patrimoniaisview");

// Form_CustomValidate event
fbens_patrimoniaisview.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fbens_patrimoniaisview.ValidateRequired = true;
<?php } else { ?>
fbens_patrimoniaisview.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fbens_patrimoniaisview.Lists["x_Localidade"] = {"LinkField":"x_Id_igreja","Ajax":null,"AutoFill":false,"DisplayFields":["x_Igreja","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fbens_patrimoniaisview.Lists["x_Estado_do_bem"] = {"LinkField":"x_Id_est_patri","Ajax":null,"AutoFill":false,"DisplayFields":["x_Estado_do_Bem","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php $bens_patrimoniais_view->ExportOptions->Render("body") ?>
<?php
	foreach ($bens_patrimoniais_view->OtherOptions as &$option)
		$option->Render("body");
?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php $bens_patrimoniais_view->ShowPageHeader(); ?>
<?php
$bens_patrimoniais_view->ShowMessage();
?>
<form name="fbens_patrimoniaisview" id="fbens_patrimoniaisview" class="form-inline ewForm ewViewForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($bens_patrimoniais_view->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $bens_patrimoniais_view->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="bens_patrimoniais">
<table class="table table-bordered table-striped ewViewTable">
<?php if ($bens_patrimoniais->Localidade->Visible) { // Localidade ?>
	<tr id="r_Localidade">
		<td><span id="elh_bens_patrimoniais_Localidade"><?php echo $bens_patrimoniais->Localidade->FldCaption() ?></span></td>
		<td<?php echo $bens_patrimoniais->Localidade->CellAttributes() ?>>
<span id="el_bens_patrimoniais_Localidade" class="form-group">
<span<?php echo $bens_patrimoniais->Localidade->ViewAttributes() ?>>
<?php echo $bens_patrimoniais->Localidade->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($bens_patrimoniais->Descricao->Visible) { // Descricao ?>
	<tr id="r_Descricao">
		<td><span id="elh_bens_patrimoniais_Descricao"><?php echo $bens_patrimoniais->Descricao->FldCaption() ?></span></td>
		<td<?php echo $bens_patrimoniais->Descricao->CellAttributes() ?>>
<span id="el_bens_patrimoniais_Descricao" class="form-group">
<span<?php echo $bens_patrimoniais->Descricao->ViewAttributes() ?>>
<?php echo $bens_patrimoniais->Descricao->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($bens_patrimoniais->DataAquisao->Visible) { // DataAquisao ?>
	<tr id="r_DataAquisao">
		<td><span id="elh_bens_patrimoniais_DataAquisao"><?php echo $bens_patrimoniais->DataAquisao->FldCaption() ?></span></td>
		<td<?php echo $bens_patrimoniais->DataAquisao->CellAttributes() ?>>
<span id="el_bens_patrimoniais_DataAquisao" class="form-group">
<span<?php echo $bens_patrimoniais->DataAquisao->ViewAttributes() ?>>
<?php echo $bens_patrimoniais->DataAquisao->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($bens_patrimoniais->Tipo->Visible) { // Tipo ?>
	<tr id="r_Tipo">
		<td><span id="elh_bens_patrimoniais_Tipo"><?php echo $bens_patrimoniais->Tipo->FldCaption() ?></span></td>
		<td<?php echo $bens_patrimoniais->Tipo->CellAttributes() ?>>
<span id="el_bens_patrimoniais_Tipo" class="form-group">
<span<?php echo $bens_patrimoniais->Tipo->ViewAttributes() ?>>
<?php echo $bens_patrimoniais->Tipo->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($bens_patrimoniais->Estado_do_bem->Visible) { // Estado_do_bem ?>
	<tr id="r_Estado_do_bem">
		<td><span id="elh_bens_patrimoniais_Estado_do_bem"><?php echo $bens_patrimoniais->Estado_do_bem->FldCaption() ?></span></td>
		<td<?php echo $bens_patrimoniais->Estado_do_bem->CellAttributes() ?>>
<span id="el_bens_patrimoniais_Estado_do_bem" class="form-group">
<span<?php echo $bens_patrimoniais->Estado_do_bem->ViewAttributes() ?>>
<?php echo $bens_patrimoniais->Estado_do_bem->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($bens_patrimoniais->Valor_estimado->Visible) { // Valor_estimado ?>
	<tr id="r_Valor_estimado">
		<td><span id="elh_bens_patrimoniais_Valor_estimado"><?php echo $bens_patrimoniais->Valor_estimado->FldCaption() ?></span></td>
		<td<?php echo $bens_patrimoniais->Valor_estimado->CellAttributes() ?>>
<span id="el_bens_patrimoniais_Valor_estimado" class="form-group">
<span<?php echo $bens_patrimoniais->Valor_estimado->ViewAttributes() ?>>
<?php echo $bens_patrimoniais->Valor_estimado->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($bens_patrimoniais->Situacao->Visible) { // Situacao ?>
	<tr id="r_Situacao">
		<td><span id="elh_bens_patrimoniais_Situacao"><?php echo $bens_patrimoniais->Situacao->FldCaption() ?></span></td>
		<td<?php echo $bens_patrimoniais->Situacao->CellAttributes() ?>>
<span id="el_bens_patrimoniais_Situacao" class="form-group">
<span<?php echo $bens_patrimoniais->Situacao->ViewAttributes() ?>>
<?php echo $bens_patrimoniais->Situacao->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($bens_patrimoniais->Anotacoes->Visible) { // Anotacoes ?>
	<tr id="r_Anotacoes">
		<td><span id="elh_bens_patrimoniais_Anotacoes"><?php echo $bens_patrimoniais->Anotacoes->FldCaption() ?></span></td>
		<td<?php echo $bens_patrimoniais->Anotacoes->CellAttributes() ?>>
<span id="el_bens_patrimoniais_Anotacoes" class="form-group">
<span<?php echo $bens_patrimoniais->Anotacoes->ViewAttributes() ?>>
<?php echo $bens_patrimoniais->Anotacoes->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
</table>
</form>
<script type="text/javascript">
fbens_patrimoniaisview.Init();
</script>
<?php
$bens_patrimoniais_view->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$bens_patrimoniais_view->Page_Terminate();
?>
