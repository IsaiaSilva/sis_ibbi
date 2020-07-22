<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg11.php" ?>
<?php include_once "ewmysql11.php" ?>
<?php include_once "phpfn11.php" ?>
<?php include_once "caixadodiainfo.php" ?>
<?php include_once "usuariosinfo.php" ?>
<?php include_once "userfn11.php" ?>
<?php

//
// Page class
//

$caixadodia_list = NULL; // Initialize page object first

class ccaixadodia_list extends ccaixadodia {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{2B7992FC-5911-46A7-9310-01F4D4225C49}";

	// Table name
	var $TableName = 'caixadodia';

	// Page object name
	var $PageObjName = 'caixadodia_list';

	// Grid form hidden field names
	var $FormName = 'fcaixadodialist';
	var $FormActionName = 'k_action';
	var $FormKeyName = 'k_key';
	var $FormOldKeyName = 'k_oldkey';
	var $FormBlankRowName = 'k_blankrow';
	var $FormKeyCountName = 'key_count';

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

		// Table object (caixadodia)
		if (!isset($GLOBALS["caixadodia"]) || get_class($GLOBALS["caixadodia"]) == "ccaixadodia") {
			$GLOBALS["caixadodia"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["caixadodia"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "caixadodiaadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "caixadodiadelete.php";
		$this->MultiUpdateUrl = "caixadodiaupdate.php";

		// Table object (usuarios)
		if (!isset($GLOBALS['usuarios'])) $GLOBALS['usuarios'] = new cusuarios();

		// User table object (usuarios)
		if (!isset($GLOBALS["UserTable"])) $GLOBALS["UserTable"] = new cusuarios();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'caixadodia', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();

		// List options
		$this->ListOptions = new cListOptions();
		$this->ListOptions->TableVar = $this->TableVar;

		// Export options
		$this->ExportOptions = new cListOptions();
		$this->ExportOptions->Tag = "div";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Other options
		$this->OtherOptions['addedit'] = new cListOptions();
		$this->OtherOptions['addedit']->Tag = "div";
		$this->OtherOptions['addedit']->TagClassName = "ewAddEditOption";
		$this->OtherOptions['detail'] = new cListOptions();
		$this->OtherOptions['detail']->Tag = "div";
		$this->OtherOptions['detail']->TagClassName = "ewDetailOption";
		$this->OtherOptions['action'] = new cListOptions();
		$this->OtherOptions['action']->Tag = "div";
		$this->OtherOptions['action']->TagClassName = "ewActionOption";
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
		if (!$Security->CanList()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate(ew_GetUrl("index.php"));
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action

		// Get grid add count
		$gridaddcnt = @$_GET[EW_TABLE_GRID_ADD_ROW_COUNT];
		if (is_numeric($gridaddcnt) && $gridaddcnt > 0)
			$this->GridAddRowCount = $gridaddcnt;

		// Set up list options
		$this->SetupListOptions();

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

		// Setup other options
		$this->SetupOtherOptions();

		// Set "checkbox" visible
		if (count($this->CustomActions) > 0)
			$this->ListOptions->Items["checkbox"]->Visible = TRUE;
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
		global $EW_EXPORT, $caixadodia;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($caixadodia);
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

	// Class variables
	var $ListOptions; // List options
	var $ExportOptions; // Export options
	var $SearchOptions; // Search options
	var $OtherOptions = array(); // Other options
	var $DisplayRecs = 100;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $Pager;
	var $DefaultSearchWhere = ""; // Default search WHERE clause
	var $SearchWhere = ""; // Search WHERE clause
	var $RecCnt = 0; // Record count
	var $EditRowCnt;
	var $StartRowCnt = 1;
	var $RowCnt = 0;
	var $Attrs = array(); // Row attributes and cell attributes
	var $RowIndex = 0; // Row index
	var $KeyCount = 0; // Key count
	var $RowAction = ""; // Row action
	var $RowOldKey = ""; // Row old key (for copy)
	var $RecPerRow = 0;
	var $MultiColumnClass;
	var $MultiColumnEditClass = "col-sm-12";
	var $MultiColumnCnt = 12;
	var $MultiColumnEditCnt = 12;
	var $GridCnt = 0;
	var $ColCnt = 0;
	var $DbMasterFilter = ""; // Master filter
	var $DbDetailFilter = ""; // Detail filter
	var $MasterRecordExists;	
	var $MultiSelectKey;
	var $Command;
	var $RestoreSearch = FALSE;
	var $Recordset;
	var $OldRecordset;

	//
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError, $gsSearchError, $Security;

		// Search filters
		$sSrchAdvanced = ""; // Advanced search filter
		$sSrchBasic = ""; // Basic search filter
		$sFilter = "";

		// Get command
		$this->Command = strtolower(@$_GET["cmd"]);
		if ($this->IsPageRequest()) { // Validate request

			// Process custom action first
			$this->ProcessCustomAction();

			// Set up records per page
			$this->SetUpDisplayRecs();

			// Handle reset command
			$this->ResetCmd();

			// Set up Breadcrumb
			if ($this->Export == "")
				$this->SetupBreadcrumb();

			// Hide list options
			if ($this->Export <> "") {
				$this->ListOptions->HideAllOptions(array("sequence"));
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			} elseif ($this->CurrentAction == "gridadd" || $this->CurrentAction == "gridedit") {
				$this->ListOptions->HideAllOptions();
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			}

			// Hide export options
			if ($this->Export <> "" || $this->CurrentAction <> "")
				$this->ExportOptions->HideAllOptions();

			// Hide other options
			if ($this->Export <> "") {
				foreach ($this->OtherOptions as &$option)
					$option->HideAllOptions();
			}

			// Set up sorting order
			$this->SetUpSortOrder();
		}

		// Restore display records
		if ($this->getRecordsPerPage() <> "") {
			$this->DisplayRecs = $this->getRecordsPerPage(); // Restore from Session
		} else {
			$this->DisplayRecs = 100; // Load default
		}

		// Load Sorting Order
		$this->LoadSortOrder();

		// Build filter
		$sFilter = "";
		if (!$Security->CanList())
			$sFilter = "(0=1)"; // Filter all records
		ew_AddFilter($sFilter, $this->DbDetailFilter);
		ew_AddFilter($sFilter, $this->SearchWhere);

		// Set up filter in session
		$this->setSessionWhere($sFilter);
		$this->CurrentFilter = "";

		// Load record count first
		if (!$this->IsAddOrEdit()) {
			$bSelectLimit = EW_SELECT_LIMIT;
			if ($bSelectLimit) {
				$this->TotalRecs = $this->SelectRecordCount();
			} else {
				if ($this->Recordset = $this->LoadRecordset())
					$this->TotalRecs = $this->Recordset->RecordCount();
			}
		}

		// Search options
		$this->SetupSearchOptions();
	}

	// Set up number of records displayed per page
	function SetUpDisplayRecs() {
		$sWrk = @$_GET[EW_TABLE_REC_PER_PAGE];
		if ($sWrk <> "") {
			if (is_numeric($sWrk)) {
				$this->DisplayRecs = intval($sWrk);
			} else {
				if (strtolower($sWrk) == "all") { // Display all records
					$this->DisplayRecs = -1;
				} else {
					$this->DisplayRecs = 100; // Non-numeric, load default
				}
			}
			$this->setRecordsPerPage($this->DisplayRecs); // Save to Session

			// Reset start position
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Build filter for all keys
	function BuildKeyFilter() {
		global $objForm;
		$sWrkFilter = "";

		// Update row index and get row key
		$rowindex = 1;
		$objForm->Index = $rowindex;
		$sThisKey = strval($objForm->GetValue($this->FormKeyName));
		while ($sThisKey <> "") {
			if ($this->SetupKeyValues($sThisKey)) {
				$sFilter = $this->KeyFilter();
				if ($sWrkFilter <> "") $sWrkFilter .= " OR ";
				$sWrkFilter .= $sFilter;
			} else {
				$sWrkFilter = "0=1";
				break;
			}

			// Update row index and get row key
			$rowindex++; // Next row
			$objForm->Index = $rowindex;
			$sThisKey = strval($objForm->GetValue($this->FormKeyName));
		}
		return $sWrkFilter;
	}

	// Set up key values
	function SetupKeyValues($key) {
		$arrKeyFlds = explode($GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"], $key);
		if (count($arrKeyFlds) >= 1) {
			$this->Id->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->Id->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for Ctrl pressed
		$bCtrl = (@$_GET["ctrl"] <> "");

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->Tipo, $bCtrl); // Tipo
			$this->UpdateSort($this->Conta_Caixa, $bCtrl); // Conta_Caixa
			$this->UpdateSort($this->Situacao, $bCtrl); // Situacao
			$this->UpdateSort($this->Descricao, $bCtrl); // Descricao
			$this->UpdateSort($this->Receitas, $bCtrl); // Receitas
			$this->UpdateSort($this->Despesas, $bCtrl); // Despesas
			$this->UpdateSort($this->FormaPagto, $bCtrl); // FormaPagto
			$this->UpdateSort($this->N_Documento, $bCtrl); // N_Documento
			$this->UpdateSort($this->Dt_Lancamento, $bCtrl); // Dt_Lancamento
			$this->UpdateSort($this->Vencimento, $bCtrl); // Vencimento
			$this->UpdateSort($this->Centro_de_Custo, $bCtrl); // Centro_de_Custo
			$this->setStartRecordNumber(1); // Reset start position
		}
	}

	// Load sort order parameters
	function LoadSortOrder() {
		$sOrderBy = $this->getSessionOrderBy(); // Get ORDER BY from Session
		if ($sOrderBy == "") {
			if ($this->getSqlOrderBy() <> "") {
				$sOrderBy = $this->getSqlOrderBy();
				$this->setSessionOrderBy($sOrderBy);
			}
		}
	}

	// Reset command
	// - cmd=reset (Reset search parameters)
	// - cmd=resetall (Reset search and master/detail parameters)
	// - cmd=resetsort (Reset sort parameters)
	function ResetCmd() {

		// Check if reset command
		if (substr($this->Command,0,5) == "reset") {

			// Reset sorting order
			if ($this->Command == "resetsort") {
				$sOrderBy = "";
				$this->setSessionOrderBy($sOrderBy);
				$this->Tipo->setSort("");
				$this->Conta_Caixa->setSort("");
				$this->Situacao->setSort("");
				$this->Descricao->setSort("");
				$this->Receitas->setSort("");
				$this->Despesas->setSort("");
				$this->FormaPagto->setSort("");
				$this->N_Documento->setSort("");
				$this->Dt_Lancamento->setSort("");
				$this->Vencimento->setSort("");
				$this->Centro_de_Custo->setSort("");
			}

			// Reset start position
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Set up list options
	function SetupListOptions() {
		global $Security, $Language;

		// Add group option item
		$item = &$this->ListOptions->Add($this->ListOptions->GroupOptionName);
		$item->Body = "";
		$item->OnLeft = FALSE;
		$item->Visible = FALSE;

		// "view"
		$item = &$this->ListOptions->Add("view");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanView();
		$item->OnLeft = FALSE;

		// "edit"
		$item = &$this->ListOptions->Add("edit");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanEdit();
		$item->OnLeft = FALSE;

		// "delete"
		$item = &$this->ListOptions->Add("delete");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanDelete();
		$item->OnLeft = FALSE;

		// "checkbox"
		$item = &$this->ListOptions->Add("checkbox");
		$item->Visible = FALSE;
		$item->OnLeft = FALSE;
		$item->Header = "<input type=\"checkbox\" name=\"key\" id=\"key\" onclick=\"ew_SelectAllKey(this);\">";
		$item->ShowInDropDown = FALSE;
		$item->ShowInButtonGroup = FALSE;

		// "sequence"
		$item = &$this->ListOptions->Add("sequence");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = TRUE;
		$item->OnLeft = TRUE; // Always on left
		$item->ShowInDropDown = FALSE;
		$item->ShowInButtonGroup = FALSE;

		// Drop down button for ListOptions
		$this->ListOptions->UseImageAndText = TRUE;
		$this->ListOptions->UseDropDownButton = FALSE;
		$this->ListOptions->DropDownButtonPhrase = $Language->Phrase("ButtonListOptions");
		$this->ListOptions->UseButtonGroup = TRUE;
		if ($this->ListOptions->UseButtonGroup && ew_IsMobile())
			$this->ListOptions->UseDropDownButton = TRUE;
		$this->ListOptions->ButtonClass = "btn-sm"; // Class for button group

		// Call ListOptions_Load event
		$this->ListOptions_Load();
		$this->SetupListOptionsExt();
		$item = &$this->ListOptions->GetItem($this->ListOptions->GroupOptionName);
		$item->Visible = $this->ListOptions->GroupOptionVisible();
	}

	// Render list options
	function RenderListOptions() {
		global $Security, $Language, $objForm;
		$this->ListOptions->LoadDefault();

		// "sequence"
		$oListOpt = &$this->ListOptions->Items["sequence"];
		$oListOpt->Body = ew_FormatSeqNo($this->RecCnt);

		// "view"
		$oListOpt = &$this->ListOptions->Items["view"];
		if ($Security->CanView())
			$oListOpt->Body = "<a class=\"ewRowLink ewView\" title=\"" . ew_HtmlTitle($Language->Phrase("ViewLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("ViewLink")) . "\" href=\"" . ew_HtmlEncode($this->ViewUrl) . "\">" . $Language->Phrase("ViewLink") . "</a>";
		else
			$oListOpt->Body = "";

		// "edit"
		$oListOpt = &$this->ListOptions->Items["edit"];
		if ($Security->CanEdit()) {
			$oListOpt->Body = "<a class=\"ewRowLink ewEdit\" title=\"" . ew_HtmlTitle($Language->Phrase("EditLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("EditLink")) . "\" href=\"" . ew_HtmlEncode($this->EditUrl) . "\">" . $Language->Phrase("EditLink") . "</a>";
		} else {
			$oListOpt->Body = "";
		}

		// "delete"
		$oListOpt = &$this->ListOptions->Items["delete"];
		if ($Security->CanDelete())
			$oListOpt->Body = "<a class=\" btn-danger ewRowLink ewDelete\"" . "" . " title=\"" . ew_HtmlTitle($Language->Phrase("DeleteLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("DeleteLink")) . "\" href=\"" . ew_HtmlEncode($this->DeleteUrl) . "\">" . $Language->Phrase("DeleteLink") . "</a>";
		else
			$oListOpt->Body = "";

		// "checkbox"
		$oListOpt = &$this->ListOptions->Items["checkbox"];
		$oListOpt->Body = "<input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->Id->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event, this);'>";
		$this->RenderListOptionsExt();

		// Call ListOptions_Rendered event
		$this->ListOptions_Rendered();
	}

	// Set up other options
	function SetupOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
		$option = $options["addedit"];

		// Add
		$item = &$option->Add("add");
		$item->Body = "<a class=\"btn-success ewAddEdit ewAdd\" title=\"" . ew_HtmlTitle($Language->Phrase("AddLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("AddLink")) . "\" href=\"" . ew_HtmlEncode($this->AddUrl) . "\">" . $Language->Phrase("AddLink") . "</a>";
		$item->Visible = ($this->AddUrl <> "" && $Security->CanAdd());
		$option = $options["action"];

		// Set up options default
		foreach ($options as &$option) {
			$option->UseImageAndText = TRUE;
			$option->UseDropDownButton = FALSE;
			$option->UseButtonGroup = TRUE;
			$option->ButtonClass = "btn-sm"; // Class for button group
			$item = &$option->Add($option->GroupOptionName);
			$item->Body = "";
			$item->Visible = FALSE;
		}
		$options["addedit"]->DropDownButtonPhrase = $Language->Phrase("ButtonAddEdit");
		$options["detail"]->DropDownButtonPhrase = $Language->Phrase("ButtonDetails");
		$options["action"]->DropDownButtonPhrase = $Language->Phrase("ButtonActions");
	}

	// Render other options
	function RenderOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
			$option = &$options["action"];
			foreach ($this->CustomActions as $action => $name) {

				// Add custom action
				$item = &$option->Add("custom_" . $action);
				$item->Body = "<a class=\"ewAction ewCustomAction\" href=\"\" onclick=\"ew_SubmitSelected(document.fcaixadodialist, '" . ew_CurrentUrl() . "', null, '" . $action . "');return false;\">" . $name . "</a>";
			}

			// Hide grid edit, multi-delete and multi-update
			if ($this->TotalRecs <= 0) {
				$option = &$options["addedit"];
				$item = &$option->GetItem("gridedit");
				if ($item) $item->Visible = FALSE;
				$option = &$options["action"];
				$item = &$option->GetItem("multidelete");
				if ($item) $item->Visible = FALSE;
				$item = &$option->GetItem("multiupdate");
				if ($item) $item->Visible = FALSE;
			}
	}

	// Process custom action
	function ProcessCustomAction() {
		global $conn, $Language, $Security;
		$sFilter = $this->GetKeyFilter();
		$UserAction = @$_POST["useraction"];
		if ($sFilter <> "" && $UserAction <> "") {
			$this->CurrentFilter = $sFilter;
			$sSql = $this->SQL();
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			$rs = $conn->Execute($sSql);
			$conn->raiseErrorFn = '';
			$rsuser = ($rs) ? $rs->GetRows() : array();
			if ($rs)
				$rs->Close();

			// Call row custom action event
			if (count($rsuser) > 0) {
				$conn->BeginTrans();
				foreach ($rsuser as $row) {
					$Processed = $this->Row_CustomAction($UserAction, $row);
					if (!$Processed) break;
				}
				if ($Processed) {
					$conn->CommitTrans(); // Commit the changes
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage(str_replace('%s', $UserAction, $Language->Phrase("CustomActionCompleted"))); // Set up success message
				} else {
					$conn->RollbackTrans(); // Rollback changes

					// Set up error message
					if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

						// Use the message, do nothing
					} elseif ($this->CancelMessage <> "") {
						$this->setFailureMessage($this->CancelMessage);
						$this->CancelMessage = "";
					} else {
						$this->setFailureMessage(str_replace('%s', $UserAction, $Language->Phrase("CustomActionCancelled")));
					}
				}
			}
		}
	}

	// Set up search options
	function SetupSearchOptions() {
		global $Language;
		$this->SearchOptions = new cListOptions();
		$this->SearchOptions->Tag = "div";
		$this->SearchOptions->TagClassName = "ewSearchOption";

		// Button group for search
		$this->SearchOptions->UseDropDownButton = FALSE;
		$this->SearchOptions->UseImageAndText = TRUE;
		$this->SearchOptions->UseButtonGroup = TRUE;
		$this->SearchOptions->DropDownButtonPhrase = $Language->Phrase("ButtonSearch");

		// Add group option item
		$item = &$this->SearchOptions->Add($this->SearchOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Hide search options
		if ($this->Export <> "" || $this->CurrentAction <> "")
			$this->SearchOptions->HideAllOptions();
		global $Security;
		if (!$Security->CanSearch())
			$this->SearchOptions->HideAllOptions();
	}

	function SetupListOptionsExt() {
		global $Security, $Language;
	}

	function RenderListOptionsExt() {
		global $Security, $Language;
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

	// Load recordset
	function LoadRecordset($offset = -1, $rowcnt = -1) {
		global $conn;

		// Load List page SQL
		$sSql = $this->SelectSQL();

		// Load recordset
		$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
		$rs = $conn->SelectLimit($sSql, $rowcnt, $offset);
		$conn->raiseErrorFn = '';

		// Call Recordset Selected event
		$this->Recordset_Selected($rs);
		return $rs;
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
		$this->Id->setDbValue($rs->fields('Id'));
		$this->Tipo->setDbValue($rs->fields('Tipo'));
		$this->Conta_Caixa->setDbValue($rs->fields('Conta_Caixa'));
		$this->Situacao->setDbValue($rs->fields('Situacao'));
		$this->Descricao->setDbValue($rs->fields('Descricao'));
		$this->id_discipulo->setDbValue($rs->fields('id_discipulo'));
		$this->Receitas->setDbValue($rs->fields('Receitas'));
		$this->Despesas->setDbValue($rs->fields('Despesas'));
		$this->FormaPagto->setDbValue($rs->fields('FormaPagto'));
		$this->N_Documento->setDbValue($rs->fields('N_Documento'));
		$this->Dt_Lancamento->setDbValue($rs->fields('Dt_Lancamento'));
		$this->Vencimento->setDbValue($rs->fields('Vencimento'));
		$this->Centro_de_Custo->setDbValue($rs->fields('Centro_de_Custo'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->Id->DbValue = $row['Id'];
		$this->Tipo->DbValue = $row['Tipo'];
		$this->Conta_Caixa->DbValue = $row['Conta_Caixa'];
		$this->Situacao->DbValue = $row['Situacao'];
		$this->Descricao->DbValue = $row['Descricao'];
		$this->id_discipulo->DbValue = $row['id_discipulo'];
		$this->Receitas->DbValue = $row['Receitas'];
		$this->Despesas->DbValue = $row['Despesas'];
		$this->FormaPagto->DbValue = $row['FormaPagto'];
		$this->N_Documento->DbValue = $row['N_Documento'];
		$this->Dt_Lancamento->DbValue = $row['Dt_Lancamento'];
		$this->Vencimento->DbValue = $row['Vencimento'];
		$this->Centro_de_Custo->DbValue = $row['Centro_de_Custo'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("Id")) <> "")
			$this->Id->CurrentValue = $this->getKey("Id"); // Id
		else
			$bValidKey = FALSE;

		// Load old recordset
		if ($bValidKey) {
			$this->CurrentFilter = $this->KeyFilter();
			$sSql = $this->SQL();
			$this->OldRecordset = ew_LoadRecordset($sSql);
			$this->LoadRowValues($this->OldRecordset); // Load row values
		} else {
			$this->OldRecordset = NULL;
		}
		return $bValidKey;
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		$this->ViewUrl = $this->GetViewUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->InlineEditUrl = $this->GetInlineEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->InlineCopyUrl = $this->GetInlineCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();

		// Convert decimal values if posted back
		if ($this->Receitas->FormValue == $this->Receitas->CurrentValue && is_numeric(ew_StrToFloat($this->Receitas->CurrentValue)))
			$this->Receitas->CurrentValue = ew_StrToFloat($this->Receitas->CurrentValue);

		// Convert decimal values if posted back
		if ($this->Despesas->FormValue == $this->Despesas->CurrentValue && is_numeric(ew_StrToFloat($this->Despesas->CurrentValue)))
			$this->Despesas->CurrentValue = ew_StrToFloat($this->Despesas->CurrentValue);

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
		// Id

		$this->Id->CellCssStyle = "white-space: nowrap;";

		// Tipo
		// Conta_Caixa
		// Situacao
		// Descricao
		// id_discipulo
		// Receitas
		// Despesas
		// FormaPagto
		// N_Documento
		// Dt_Lancamento
		// Vencimento
		// Centro_de_Custo
		// Accumulate aggregate value

		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT && $this->RowType <> EW_ROWTYPE_AGGREGATE) {
			if (is_numeric($this->Receitas->CurrentValue))
				$this->Receitas->Total += $this->Receitas->CurrentValue; // Accumulate total
			if (is_numeric($this->Despesas->CurrentValue))
				$this->Despesas->Total += $this->Despesas->CurrentValue; // Accumulate total
		}
		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

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

			// Conta_Caixa
			if (strval($this->Conta_Caixa->CurrentValue) <> "") {
				$sFilterWrk = "`Id`" . ew_SearchString("=", $this->Conta_Caixa->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `Id`, `Conta_Caixa` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `fin_conta_caixa`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Conta_Caixa, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->Conta_Caixa->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->Conta_Caixa->ViewValue = $this->Conta_Caixa->CurrentValue;
				}
			} else {
				$this->Conta_Caixa->ViewValue = NULL;
			}
			$this->Conta_Caixa->ViewCustomAttributes = "";

			// Situacao
			if (strval($this->Situacao->CurrentValue) <> "") {
				$sFilterWrk = "`Id`" . ew_SearchString("=", $this->Situacao->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `Id`, `Situacao` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `fin_situacao`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Situacao, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->Situacao->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->Situacao->ViewValue = $this->Situacao->CurrentValue;
				}
			} else {
				$this->Situacao->ViewValue = NULL;
			}
			$this->Situacao->ViewCustomAttributes = "";

			// Descricao
			$this->Descricao->ViewValue = $this->Descricao->CurrentValue;
			$this->Descricao->ViewCustomAttributes = "";

			// id_discipulo
			if (strval($this->id_discipulo->CurrentValue) <> "") {
				$sFilterWrk = "`Id_membro`" . ew_SearchString("=", $this->id_discipulo->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `Id_membro`, `Nome` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `membro`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->id_discipulo, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `Nome` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->id_discipulo->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->id_discipulo->ViewValue = $this->id_discipulo->CurrentValue;
				}
			} else {
				$this->id_discipulo->ViewValue = NULL;
			}
			$this->id_discipulo->ViewCustomAttributes = "";

			// Receitas
			$this->Receitas->ViewValue = $this->Receitas->CurrentValue;
			$this->Receitas->ViewValue = ew_FormatNumber($this->Receitas->ViewValue, 2, -2, -2, -2);
			$this->Receitas->CellCssStyle .= "text-align: right;";
			$this->Receitas->ViewCustomAttributes = "";

			// Despesas
			$this->Despesas->ViewValue = $this->Despesas->CurrentValue;
			$this->Despesas->ViewValue = ew_FormatNumber($this->Despesas->ViewValue, 2, -2, -2, -2);
			$this->Despesas->CellCssStyle .= "text-align: right;";
			$this->Despesas->ViewCustomAttributes = "";

			// FormaPagto
			if (strval($this->FormaPagto->CurrentValue) <> "") {
				$sFilterWrk = "`Id`" . ew_SearchString("=", $this->FormaPagto->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `Id`, `Forma_Pagto` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `fin_forma_pgto`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->FormaPagto, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->FormaPagto->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->FormaPagto->ViewValue = $this->FormaPagto->CurrentValue;
				}
			} else {
				$this->FormaPagto->ViewValue = NULL;
			}
			$this->FormaPagto->ViewCustomAttributes = "";

			// N_Documento
			$this->N_Documento->ViewValue = $this->N_Documento->CurrentValue;
			$this->N_Documento->ViewCustomAttributes = "";

			// Dt_Lancamento
			$this->Dt_Lancamento->ViewValue = $this->Dt_Lancamento->CurrentValue;
			$this->Dt_Lancamento->ViewValue = ew_FormatDateTime($this->Dt_Lancamento->ViewValue, 7);
			$this->Dt_Lancamento->ViewCustomAttributes = "";

			// Vencimento
			$this->Vencimento->ViewValue = $this->Vencimento->CurrentValue;
			$this->Vencimento->ViewValue = ew_FormatDateTime($this->Vencimento->ViewValue, 7);
			$this->Vencimento->ViewCustomAttributes = "";

			// Centro_de_Custo
			if (strval($this->Centro_de_Custo->CurrentValue) <> "") {
				$sFilterWrk = "`Id`" . ew_SearchString("=", $this->Centro_de_Custo->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `Id`, `Conta` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `fin_centro_de_custo`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Centro_de_Custo, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->Centro_de_Custo->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->Centro_de_Custo->ViewValue = $this->Centro_de_Custo->CurrentValue;
				}
			} else {
				$this->Centro_de_Custo->ViewValue = NULL;
			}
			$this->Centro_de_Custo->ViewCustomAttributes = "";

			// Tipo
			$this->Tipo->LinkCustomAttributes = "";
			$this->Tipo->HrefValue = "";
			$this->Tipo->TooltipValue = "";

			// Conta_Caixa
			$this->Conta_Caixa->LinkCustomAttributes = "";
			$this->Conta_Caixa->HrefValue = "";
			$this->Conta_Caixa->TooltipValue = "";

			// Situacao
			$this->Situacao->LinkCustomAttributes = "";
			$this->Situacao->HrefValue = "";
			$this->Situacao->TooltipValue = "";

			// Descricao
			$this->Descricao->LinkCustomAttributes = "";
			$this->Descricao->HrefValue = "";
			$this->Descricao->TooltipValue = "";

			// Receitas
			$this->Receitas->LinkCustomAttributes = "";
			$this->Receitas->HrefValue = "";
			$this->Receitas->TooltipValue = "";

			// Despesas
			$this->Despesas->LinkCustomAttributes = "";
			$this->Despesas->HrefValue = "";
			$this->Despesas->TooltipValue = "";

			// FormaPagto
			$this->FormaPagto->LinkCustomAttributes = "";
			$this->FormaPagto->HrefValue = "";
			$this->FormaPagto->TooltipValue = "";

			// N_Documento
			$this->N_Documento->LinkCustomAttributes = "";
			$this->N_Documento->HrefValue = "";
			$this->N_Documento->TooltipValue = "";

			// Dt_Lancamento
			$this->Dt_Lancamento->LinkCustomAttributes = "";
			$this->Dt_Lancamento->HrefValue = "";
			$this->Dt_Lancamento->TooltipValue = "";

			// Vencimento
			$this->Vencimento->LinkCustomAttributes = "";
			$this->Vencimento->HrefValue = "";
			$this->Vencimento->TooltipValue = "";

			// Centro_de_Custo
			$this->Centro_de_Custo->LinkCustomAttributes = "";
			$this->Centro_de_Custo->HrefValue = "";
			$this->Centro_de_Custo->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_AGGREGATEINIT) { // Initialize aggregate row
			$this->Receitas->Total = 0; // Initialize total
			$this->Despesas->Total = 0; // Initialize total
		} elseif ($this->RowType == EW_ROWTYPE_AGGREGATE) { // Aggregate row
			$this->Receitas->CurrentValue = $this->Receitas->Total;
			$this->Receitas->ViewValue = $this->Receitas->CurrentValue;
			$this->Receitas->ViewValue = ew_FormatNumber($this->Receitas->ViewValue, 2, -2, -2, -2);
			$this->Receitas->CellCssStyle .= "text-align: right;";
			$this->Receitas->ViewCustomAttributes = "";
			$this->Receitas->HrefValue = ""; // Clear href value
			$this->Despesas->CurrentValue = $this->Despesas->Total;
			$this->Despesas->ViewValue = $this->Despesas->CurrentValue;
			$this->Despesas->ViewValue = ew_FormatNumber($this->Despesas->ViewValue, 2, -2, -2, -2);
			$this->Despesas->CellCssStyle .= "text-align: right;";
			$this->Despesas->ViewCustomAttributes = "";
			$this->Despesas->HrefValue = ""; // Clear href value
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
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$Breadcrumb->Add("list", $this->TableVar, $url, "", $this->TableVar, TRUE);
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

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}

	// ListOptions Load event
	function ListOptions_Load() {

		// Example:
		//$opt = &$this->ListOptions->Add("new");
		//$opt->Header = "xxx";
		//$opt->OnLeft = TRUE; // Link on left
		//$opt->MoveTo(0); // Move to first column

	}

	// ListOptions Rendered event
	function ListOptions_Rendered() {

		// Example: 
		//$this->ListOptions->Items["new"]->Body = "xxx";

	}

	// Row Custom Action event
	function Row_CustomAction($action, $row) {

		// Return FALSE to abort
		return TRUE;
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
if (!isset($caixadodia_list)) $caixadodia_list = new ccaixadodia_list();

// Page init
$caixadodia_list->Page_Init();

// Page main
$caixadodia_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$caixadodia_list->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var caixadodia_list = new ew_Page("caixadodia_list");
caixadodia_list.PageID = "list"; // Page ID
var EW_PAGE_ID = caixadodia_list.PageID; // For backward compatibility

// Form object
var fcaixadodialist = new ew_Form("fcaixadodialist");
fcaixadodialist.FormKeyCountName = '<?php echo $caixadodia_list->FormKeyCountName ?>';

// Form_CustomValidate event
fcaixadodialist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fcaixadodialist.ValidateRequired = true;
<?php } else { ?>
fcaixadodialist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fcaixadodialist.Lists["x_Conta_Caixa"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Conta_Caixa","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fcaixadodialist.Lists["x_Situacao"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Situacao","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fcaixadodialist.Lists["x_FormaPagto"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Forma_Pagto","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fcaixadodialist.Lists["x_Centro_de_Custo"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Conta","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php if ($caixadodia_list->TotalRecs > 0 && $caixadodia_list->ExportOptions->Visible()) { ?>
<?php $caixadodia_list->ExportOptions->Render("body") ?>
<?php } ?>
<?php echo $Language->SelectionForm(); ?>
<div class="ewSearchOption ewListOptionSeparator" style="white-space: nowrap;" data-name="button"><div class="btn-group ewButtonGroup">
<?php $help = ew_ExecuteScalar("Select txt from ajuda where pg = '".ew_CurrentPage()."'") ; 
if (strlen($help)>0){ ?>
	<button class="btn btn-default" type="button" title="" data-original-title="Ajuda desta p&aacute;gina" id="ajuda"><span data-phrase="SearchBtn" class="fa fa-question ewIcon" data-caption="Ajuda"></span></button>		
<?php } ?>	
</div></div>
<div class="clearfix"></div>
</div>
<?php
	$bSelectLimit = EW_SELECT_LIMIT;
	if ($bSelectLimit) {
		if ($caixadodia_list->TotalRecs <= 0)
			$caixadodia_list->TotalRecs = $caixadodia->SelectRecordCount();
	} else {
		if (!$caixadodia_list->Recordset && ($caixadodia_list->Recordset = $caixadodia_list->LoadRecordset()))
			$caixadodia_list->TotalRecs = $caixadodia_list->Recordset->RecordCount();
	}
	$caixadodia_list->StartRec = 1;
	if ($caixadodia_list->DisplayRecs <= 0 || ($caixadodia->Export <> "" && $caixadodia->ExportAll)) // Display all records
		$caixadodia_list->DisplayRecs = $caixadodia_list->TotalRecs;
	if (!($caixadodia->Export <> "" && $caixadodia->ExportAll))
		$caixadodia_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$caixadodia_list->Recordset = $caixadodia_list->LoadRecordset($caixadodia_list->StartRec-1, $caixadodia_list->DisplayRecs);

	// Set no record found message
	if ($caixadodia->CurrentAction == "" && $caixadodia_list->TotalRecs == 0) {
		if (!$Security->CanList())
			$caixadodia_list->setWarningMessage($Language->Phrase("NoPermission"));
		if ($caixadodia_list->SearchWhere == "0=101")
			$caixadodia_list->setWarningMessage($Language->Phrase("EnterSearchCriteria"));
		else
			$caixadodia_list->setWarningMessage($Language->Phrase("NoRecord"));
	}
$caixadodia_list->RenderOtherOptions();
?>
<?php $caixadodia_list->ShowPageHeader(); ?>
<?php
$caixadodia_list->ShowMessage();
?>
<?php if ($caixadodia_list->TotalRecs > 0 || $caixadodia->CurrentAction <> "") { ?>
<div class="ewGrid">
<div class="ewGridUpperPanel">
<?php if ($caixadodia->CurrentAction <> "gridadd" && $caixadodia->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="form-inline ewForm ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($caixadodia_list->Pager)) $caixadodia_list->Pager = new cPrevNextPager($caixadodia_list->StartRec, $caixadodia_list->DisplayRecs, $caixadodia_list->TotalRecs) ?>
<?php if ($caixadodia_list->Pager->RecordCount > 0) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($caixadodia_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $caixadodia_list->PageUrl() ?>start=<?php echo $caixadodia_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($caixadodia_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $caixadodia_list->PageUrl() ?>start=<?php echo $caixadodia_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $caixadodia_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($caixadodia_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $caixadodia_list->PageUrl() ?>start=<?php echo $caixadodia_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($caixadodia_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $caixadodia_list->PageUrl() ?>start=<?php echo $caixadodia_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $caixadodia_list->Pager->PageCount ?></span>
</div>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $caixadodia_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $caixadodia_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $caixadodia_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
<?php if ($caixadodia_list->TotalRecs > 0) { ?>
<div class="ewPager">
<input type="hidden" name="t" value="caixadodia">
<select name="<?php echo EW_TABLE_REC_PER_PAGE ?>" class="form-control input-sm" onchange="this.form.submit();">
<option value="100"<?php if ($caixadodia_list->DisplayRecs == 100) { ?> selected="selected"<?php } ?>>100</option>
<option value="ALL"<?php if ($caixadodia->getRecordsPerPage() == -1) { ?> selected="selected"<?php } ?>><?php echo $Language->Phrase("AllRecords") ?></option>
</select>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($caixadodia_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
<div class="clearfix"></div>
</div>
<form name="fcaixadodialist" id="fcaixadodialist" class="form-inline ewForm ewListForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($caixadodia_list->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $caixadodia_list->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="caixadodia">
<div id="gmp_caixadodia" class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php if ($caixadodia_list->TotalRecs > 0) { ?>
<table id="tbl_caixadodialist" class="table ewTable">
<?php echo $caixadodia->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$caixadodia_list->RenderListOptions();

// Render list options (header, left)
$caixadodia_list->ListOptions->Render("header", "left");
?>
<?php if ($caixadodia->Tipo->Visible) { // Tipo ?>
	<?php if ($caixadodia->SortUrl($caixadodia->Tipo) == "") { ?>
		<th data-name="Tipo"><div id="elh_caixadodia_Tipo" class="caixadodia_Tipo"><div class="ewTableHeaderCaption"><?php echo $caixadodia->Tipo->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Tipo"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $caixadodia->SortUrl($caixadodia->Tipo) ?>',2);"><div id="elh_caixadodia_Tipo" class="caixadodia_Tipo">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $caixadodia->Tipo->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($caixadodia->Tipo->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($caixadodia->Tipo->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($caixadodia->Conta_Caixa->Visible) { // Conta_Caixa ?>
	<?php if ($caixadodia->SortUrl($caixadodia->Conta_Caixa) == "") { ?>
		<th data-name="Conta_Caixa"><div id="elh_caixadodia_Conta_Caixa" class="caixadodia_Conta_Caixa"><div class="ewTableHeaderCaption"><?php echo $caixadodia->Conta_Caixa->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Conta_Caixa"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $caixadodia->SortUrl($caixadodia->Conta_Caixa) ?>',2);"><div id="elh_caixadodia_Conta_Caixa" class="caixadodia_Conta_Caixa">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $caixadodia->Conta_Caixa->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($caixadodia->Conta_Caixa->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($caixadodia->Conta_Caixa->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($caixadodia->Situacao->Visible) { // Situacao ?>
	<?php if ($caixadodia->SortUrl($caixadodia->Situacao) == "") { ?>
		<th data-name="Situacao"><div id="elh_caixadodia_Situacao" class="caixadodia_Situacao"><div class="ewTableHeaderCaption"><?php echo $caixadodia->Situacao->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Situacao"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $caixadodia->SortUrl($caixadodia->Situacao) ?>',2);"><div id="elh_caixadodia_Situacao" class="caixadodia_Situacao">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $caixadodia->Situacao->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($caixadodia->Situacao->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($caixadodia->Situacao->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($caixadodia->Descricao->Visible) { // Descricao ?>
	<?php if ($caixadodia->SortUrl($caixadodia->Descricao) == "") { ?>
		<th data-name="Descricao"><div id="elh_caixadodia_Descricao" class="caixadodia_Descricao"><div class="ewTableHeaderCaption"><?php echo $caixadodia->Descricao->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Descricao"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $caixadodia->SortUrl($caixadodia->Descricao) ?>',2);"><div id="elh_caixadodia_Descricao" class="caixadodia_Descricao">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $caixadodia->Descricao->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($caixadodia->Descricao->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($caixadodia->Descricao->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($caixadodia->Receitas->Visible) { // Receitas ?>
	<?php if ($caixadodia->SortUrl($caixadodia->Receitas) == "") { ?>
		<th data-name="Receitas"><div id="elh_caixadodia_Receitas" class="caixadodia_Receitas"><div class="ewTableHeaderCaption"><?php echo $caixadodia->Receitas->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Receitas"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $caixadodia->SortUrl($caixadodia->Receitas) ?>',2);"><div id="elh_caixadodia_Receitas" class="caixadodia_Receitas">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $caixadodia->Receitas->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($caixadodia->Receitas->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($caixadodia->Receitas->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($caixadodia->Despesas->Visible) { // Despesas ?>
	<?php if ($caixadodia->SortUrl($caixadodia->Despesas) == "") { ?>
		<th data-name="Despesas"><div id="elh_caixadodia_Despesas" class="caixadodia_Despesas"><div class="ewTableHeaderCaption"><?php echo $caixadodia->Despesas->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Despesas"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $caixadodia->SortUrl($caixadodia->Despesas) ?>',2);"><div id="elh_caixadodia_Despesas" class="caixadodia_Despesas">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $caixadodia->Despesas->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($caixadodia->Despesas->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($caixadodia->Despesas->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($caixadodia->FormaPagto->Visible) { // FormaPagto ?>
	<?php if ($caixadodia->SortUrl($caixadodia->FormaPagto) == "") { ?>
		<th data-name="FormaPagto"><div id="elh_caixadodia_FormaPagto" class="caixadodia_FormaPagto"><div class="ewTableHeaderCaption"><?php echo $caixadodia->FormaPagto->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="FormaPagto"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $caixadodia->SortUrl($caixadodia->FormaPagto) ?>',2);"><div id="elh_caixadodia_FormaPagto" class="caixadodia_FormaPagto">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $caixadodia->FormaPagto->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($caixadodia->FormaPagto->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($caixadodia->FormaPagto->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($caixadodia->N_Documento->Visible) { // N_Documento ?>
	<?php if ($caixadodia->SortUrl($caixadodia->N_Documento) == "") { ?>
		<th data-name="N_Documento"><div id="elh_caixadodia_N_Documento" class="caixadodia_N_Documento"><div class="ewTableHeaderCaption"><?php echo $caixadodia->N_Documento->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="N_Documento"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $caixadodia->SortUrl($caixadodia->N_Documento) ?>',2);"><div id="elh_caixadodia_N_Documento" class="caixadodia_N_Documento">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $caixadodia->N_Documento->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($caixadodia->N_Documento->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($caixadodia->N_Documento->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($caixadodia->Dt_Lancamento->Visible) { // Dt_Lancamento ?>
	<?php if ($caixadodia->SortUrl($caixadodia->Dt_Lancamento) == "") { ?>
		<th data-name="Dt_Lancamento"><div id="elh_caixadodia_Dt_Lancamento" class="caixadodia_Dt_Lancamento"><div class="ewTableHeaderCaption"><?php echo $caixadodia->Dt_Lancamento->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Dt_Lancamento"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $caixadodia->SortUrl($caixadodia->Dt_Lancamento) ?>',2);"><div id="elh_caixadodia_Dt_Lancamento" class="caixadodia_Dt_Lancamento">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $caixadodia->Dt_Lancamento->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($caixadodia->Dt_Lancamento->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($caixadodia->Dt_Lancamento->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($caixadodia->Vencimento->Visible) { // Vencimento ?>
	<?php if ($caixadodia->SortUrl($caixadodia->Vencimento) == "") { ?>
		<th data-name="Vencimento"><div id="elh_caixadodia_Vencimento" class="caixadodia_Vencimento"><div class="ewTableHeaderCaption"><?php echo $caixadodia->Vencimento->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Vencimento"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $caixadodia->SortUrl($caixadodia->Vencimento) ?>',2);"><div id="elh_caixadodia_Vencimento" class="caixadodia_Vencimento">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $caixadodia->Vencimento->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($caixadodia->Vencimento->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($caixadodia->Vencimento->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($caixadodia->Centro_de_Custo->Visible) { // Centro_de_Custo ?>
	<?php if ($caixadodia->SortUrl($caixadodia->Centro_de_Custo) == "") { ?>
		<th data-name="Centro_de_Custo"><div id="elh_caixadodia_Centro_de_Custo" class="caixadodia_Centro_de_Custo"><div class="ewTableHeaderCaption"><?php echo $caixadodia->Centro_de_Custo->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Centro_de_Custo"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $caixadodia->SortUrl($caixadodia->Centro_de_Custo) ?>',2);"><div id="elh_caixadodia_Centro_de_Custo" class="caixadodia_Centro_de_Custo">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $caixadodia->Centro_de_Custo->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($caixadodia->Centro_de_Custo->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($caixadodia->Centro_de_Custo->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$caixadodia_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($caixadodia->ExportAll && $caixadodia->Export <> "") {
	$caixadodia_list->StopRec = $caixadodia_list->TotalRecs;
} else {

	// Set the last record to display
	if ($caixadodia_list->TotalRecs > $caixadodia_list->StartRec + $caixadodia_list->DisplayRecs - 1)
		$caixadodia_list->StopRec = $caixadodia_list->StartRec + $caixadodia_list->DisplayRecs - 1;
	else
		$caixadodia_list->StopRec = $caixadodia_list->TotalRecs;
}
$caixadodia_list->RecCnt = $caixadodia_list->StartRec - 1;
if ($caixadodia_list->Recordset && !$caixadodia_list->Recordset->EOF) {
	$caixadodia_list->Recordset->MoveFirst();
	$bSelectLimit = EW_SELECT_LIMIT;
	if (!$bSelectLimit && $caixadodia_list->StartRec > 1)
		$caixadodia_list->Recordset->Move($caixadodia_list->StartRec - 1);
} elseif (!$caixadodia->AllowAddDeleteRow && $caixadodia_list->StopRec == 0) {
	$caixadodia_list->StopRec = $caixadodia->GridAddRowCount;
}

// Initialize aggregate
$caixadodia->RowType = EW_ROWTYPE_AGGREGATEINIT;
$caixadodia->ResetAttrs();
$caixadodia_list->RenderRow();
while ($caixadodia_list->RecCnt < $caixadodia_list->StopRec) {
	$caixadodia_list->RecCnt++;
	if (intval($caixadodia_list->RecCnt) >= intval($caixadodia_list->StartRec)) {
		$caixadodia_list->RowCnt++;

		// Set up key count
		$caixadodia_list->KeyCount = $caixadodia_list->RowIndex;

		// Init row class and style
		$caixadodia->ResetAttrs();
		$caixadodia->CssClass = "";
		if ($caixadodia->CurrentAction == "gridadd") {
		} else {
			$caixadodia_list->LoadRowValues($caixadodia_list->Recordset); // Load row values
		}
		$caixadodia->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$caixadodia->RowAttrs = array_merge($caixadodia->RowAttrs, array('data-rowindex'=>$caixadodia_list->RowCnt, 'id'=>'r' . $caixadodia_list->RowCnt . '_caixadodia', 'data-rowtype'=>$caixadodia->RowType));

		// Render row
		$caixadodia_list->RenderRow();

		// Render list options
		$caixadodia_list->RenderListOptions();
?>
	<tr<?php echo $caixadodia->RowAttributes() ?>>
<?php

// Render list options (body, left)
$caixadodia_list->ListOptions->Render("body", "left", $caixadodia_list->RowCnt);
?>
	<?php if ($caixadodia->Tipo->Visible) { // Tipo ?>
		<td data-name="Tipo"<?php echo $caixadodia->Tipo->CellAttributes() ?>>
<span<?php echo $caixadodia->Tipo->ViewAttributes() ?>>
<?php echo $caixadodia->Tipo->ListViewValue() ?></span>
<a id="<?php echo $caixadodia_list->PageObjName . "_row_" . $caixadodia_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($caixadodia->Conta_Caixa->Visible) { // Conta_Caixa ?>
		<td data-name="Conta_Caixa"<?php echo $caixadodia->Conta_Caixa->CellAttributes() ?>>
<span<?php echo $caixadodia->Conta_Caixa->ViewAttributes() ?>>
<?php echo $caixadodia->Conta_Caixa->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($caixadodia->Situacao->Visible) { // Situacao ?>
		<td data-name="Situacao"<?php echo $caixadodia->Situacao->CellAttributes() ?>>
<span<?php echo $caixadodia->Situacao->ViewAttributes() ?>>
<?php echo $caixadodia->Situacao->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($caixadodia->Descricao->Visible) { // Descricao ?>
		<td data-name="Descricao"<?php echo $caixadodia->Descricao->CellAttributes() ?>>
<span<?php echo $caixadodia->Descricao->ViewAttributes() ?>>
<?php echo $caixadodia->Descricao->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($caixadodia->Receitas->Visible) { // Receitas ?>
		<td data-name="Receitas"<?php echo $caixadodia->Receitas->CellAttributes() ?>>
<span<?php echo $caixadodia->Receitas->ViewAttributes() ?>>
<?php echo $caixadodia->Receitas->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($caixadodia->Despesas->Visible) { // Despesas ?>
		<td data-name="Despesas"<?php echo $caixadodia->Despesas->CellAttributes() ?>>
<span<?php echo $caixadodia->Despesas->ViewAttributes() ?>>
<?php echo $caixadodia->Despesas->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($caixadodia->FormaPagto->Visible) { // FormaPagto ?>
		<td data-name="FormaPagto"<?php echo $caixadodia->FormaPagto->CellAttributes() ?>>
<span<?php echo $caixadodia->FormaPagto->ViewAttributes() ?>>
<?php echo $caixadodia->FormaPagto->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($caixadodia->N_Documento->Visible) { // N_Documento ?>
		<td data-name="N_Documento"<?php echo $caixadodia->N_Documento->CellAttributes() ?>>
<span<?php echo $caixadodia->N_Documento->ViewAttributes() ?>>
<?php echo $caixadodia->N_Documento->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($caixadodia->Dt_Lancamento->Visible) { // Dt_Lancamento ?>
		<td data-name="Dt_Lancamento"<?php echo $caixadodia->Dt_Lancamento->CellAttributes() ?>>
<span<?php echo $caixadodia->Dt_Lancamento->ViewAttributes() ?>>
<?php echo $caixadodia->Dt_Lancamento->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($caixadodia->Vencimento->Visible) { // Vencimento ?>
		<td data-name="Vencimento"<?php echo $caixadodia->Vencimento->CellAttributes() ?>>
<span<?php echo $caixadodia->Vencimento->ViewAttributes() ?>>
<?php echo $caixadodia->Vencimento->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($caixadodia->Centro_de_Custo->Visible) { // Centro_de_Custo ?>
		<td data-name="Centro_de_Custo"<?php echo $caixadodia->Centro_de_Custo->CellAttributes() ?>>
<span<?php echo $caixadodia->Centro_de_Custo->ViewAttributes() ?>>
<?php echo $caixadodia->Centro_de_Custo->ListViewValue() ?></span>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$caixadodia_list->ListOptions->Render("body", "right", $caixadodia_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($caixadodia->CurrentAction <> "gridadd")
		$caixadodia_list->Recordset->MoveNext();
}
?>
</tbody>
<?php

// Render aggregate row
$caixadodia->RowType = EW_ROWTYPE_AGGREGATE;
$caixadodia->ResetAttrs();
$caixadodia_list->RenderRow();
?>
<?php if ($caixadodia_list->TotalRecs > 0 && ($caixadodia->CurrentAction <> "gridadd" && $caixadodia->CurrentAction <> "gridedit")) { ?>
<tfoot><!-- Table footer -->
	<tr class="ewTableFooter">
<?php

// Render list options
$caixadodia_list->RenderListOptions();

// Render list options (footer, left)
$caixadodia_list->ListOptions->Render("footer", "left");
?>
	<?php if ($caixadodia->Tipo->Visible) { // Tipo ?>
		<td data-name="Tipo"><span id="elf_caixadodia_Tipo" class="caixadodia_Tipo">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($caixadodia->Conta_Caixa->Visible) { // Conta_Caixa ?>
		<td data-name="Conta_Caixa"><span id="elf_caixadodia_Conta_Caixa" class="caixadodia_Conta_Caixa">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($caixadodia->Situacao->Visible) { // Situacao ?>
		<td data-name="Situacao"><span id="elf_caixadodia_Situacao" class="caixadodia_Situacao">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($caixadodia->Descricao->Visible) { // Descricao ?>
		<td data-name="Descricao"><span id="elf_caixadodia_Descricao" class="caixadodia_Descricao">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($caixadodia->Receitas->Visible) { // Receitas ?>
		<td data-name="Receitas"><span id="elf_caixadodia_Receitas" class="caixadodia_Receitas">
<span class="ewAggregate"><?php echo $Language->Phrase("TOTAL") ?></span>
<?php echo $caixadodia->Receitas->ViewValue ?>
		</span></td>
	<?php } ?>
	<?php if ($caixadodia->Despesas->Visible) { // Despesas ?>
		<td data-name="Despesas"><span id="elf_caixadodia_Despesas" class="caixadodia_Despesas">
<span class="ewAggregate"><?php echo $Language->Phrase("TOTAL") ?></span>
<?php echo $caixadodia->Despesas->ViewValue ?>
		</span></td>
	<?php } ?>
	<?php if ($caixadodia->FormaPagto->Visible) { // FormaPagto ?>
		<td data-name="FormaPagto"><span id="elf_caixadodia_FormaPagto" class="caixadodia_FormaPagto">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($caixadodia->N_Documento->Visible) { // N_Documento ?>
		<td data-name="N_Documento"><span id="elf_caixadodia_N_Documento" class="caixadodia_N_Documento">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($caixadodia->Dt_Lancamento->Visible) { // Dt_Lancamento ?>
		<td data-name="Dt_Lancamento"><span id="elf_caixadodia_Dt_Lancamento" class="caixadodia_Dt_Lancamento">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($caixadodia->Vencimento->Visible) { // Vencimento ?>
		<td data-name="Vencimento"><span id="elf_caixadodia_Vencimento" class="caixadodia_Vencimento">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($caixadodia->Centro_de_Custo->Visible) { // Centro_de_Custo ?>
		<td data-name="Centro_de_Custo"><span id="elf_caixadodia_Centro_de_Custo" class="caixadodia_Centro_de_Custo">
		&nbsp;
		</span></td>
	<?php } ?>
<?php

// Render list options (footer, right)
$caixadodia_list->ListOptions->Render("footer", "right");
?>
	</tr>
</tfoot>	
<?php } ?>
</table>
<?php } ?>
<?php if ($caixadodia->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($caixadodia_list->Recordset)
	$caixadodia_list->Recordset->Close();
?>
</div>
<?php } ?>
<?php if ($caixadodia_list->TotalRecs == 0 && $caixadodia->CurrentAction == "") { // Show other options ?>
<div class="ewListOtherOptions">
<?php
	foreach ($caixadodia_list->OtherOptions as &$option) {
		$option->ButtonClass = "";
		$option->Render("body", "");
	}
?>
</div>
<div class="clearfix"></div>
<?php } ?>
<script type="text/javascript">
fcaixadodialist.Init();
$(document).ready(function($) {	$("#ajuda").click(function() {	bootbox.dialog({title: "Informações de Ajuda", message: '<?php echo str_replace("\r\n"," ",trim($help)) ?>', buttons: { success: { label: "Fechar" }}}); });});
</script>
<?php
$caixadodia_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">
$(document).ready(function($) {
	$("#elf_caixadodia_Despesas").addClass("badge bg-red");
	$("#elf_caixadodia_Receitas").addClass("badge bg-cobalt");
});
</script>
<?php include_once "footer.php" ?>
<?php
$caixadodia_list->Page_Terminate();
?>
