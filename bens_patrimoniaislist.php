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

$bens_patrimoniais_list = NULL; // Initialize page object first

class cbens_patrimoniais_list extends cbens_patrimoniais {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{2B7992FC-5911-46A7-9310-01F4D4225C49}";

	// Table name
	var $TableName = 'bens_patrimoniais';

	// Page object name
	var $PageObjName = 'bens_patrimoniais_list';

	// Grid form hidden field names
	var $FormName = 'fbens_patrimoniaislist';
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

		// Table object (bens_patrimoniais)
		if (!isset($GLOBALS["bens_patrimoniais"]) || get_class($GLOBALS["bens_patrimoniais"]) == "cbens_patrimoniais") {
			$GLOBALS["bens_patrimoniais"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["bens_patrimoniais"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "bens_patrimoniaisadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "bens_patrimoniaisdelete.php";
		$this->MultiUpdateUrl = "bens_patrimoniaisupdate.php";

		// Table object (usuarios)
		if (!isset($GLOBALS['usuarios'])) $GLOBALS['usuarios'] = new cusuarios();

		// User table object (usuarios)
		if (!isset($GLOBALS["UserTable"])) $GLOBALS["UserTable"] = new cusuarios();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'bens_patrimoniais', TRUE);

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

		// Get export parameters
		$custom = "";
		if (@$_GET["export"] <> "") {
			$this->Export = $_GET["export"];
			$custom = @$_GET["custom"];
		} elseif (@$_POST["export"] <> "") {
			$this->Export = $_POST["export"];
			$custom = @$_POST["custom"];
		} elseif (ew_IsHttpPost()) {
			if (@$_POST["exporttype"] <> "")
				$this->Export = $_POST["exporttype"];
			$custom = @$_POST["custom"];
		} else {
			$this->setExportReturnUrl(ew_CurrentUrl());
		}
		$gsExportFile = $this->TableVar; // Get export file, used in header

		// Get custom export parameters
		if ($this->Export <> "" && $custom <> "") {
			$this->CustomExport = $this->Export;
			$this->Export = "print";
		}
		$gsCustomExport = $this->CustomExport;
		$gsExport = $this->Export; // Get export parameter, used in header

		// Update Export URLs
		if (defined("EW_USE_PHPEXCEL"))
			$this->ExportExcelCustom = FALSE;
		if ($this->ExportExcelCustom)
			$this->ExportExcelUrl .= "&amp;custom=1";
		if (defined("EW_USE_PHPWORD"))
			$this->ExportWordCustom = FALSE;
		if ($this->ExportWordCustom)
			$this->ExportWordUrl .= "&amp;custom=1";
		if ($this->ExportPdfCustom)
			$this->ExportPdfUrl .= "&amp;custom=1";
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action

		// Get grid add count
		$gridaddcnt = @$_GET[EW_TABLE_GRID_ADD_ROW_COUNT];
		if (is_numeric($gridaddcnt) && $gridaddcnt > 0)
			$this->GridAddRowCount = $gridaddcnt;

		// Set up list options
		$this->SetupListOptions();

		// Setup export options
		$this->SetupExportOptions();

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

	// Class variables
	var $ListOptions; // List options
	var $ExportOptions; // Export options
	var $SearchOptions; // Search options
	var $OtherOptions = array(); // Other options
	var $DisplayRecs = 10;
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

			// Get default search criteria
			ew_AddFilter($this->DefaultSearchWhere, $this->BasicSearchWhere(TRUE));
			ew_AddFilter($this->DefaultSearchWhere, $this->AdvancedSearchWhere(TRUE));

			// Get basic search values
			$this->LoadBasicSearchValues();

			// Get and validate search values for advanced search
			$this->LoadSearchValues(); // Get search values
			if (!$this->ValidateSearch())
				$this->setFailureMessage($gsSearchError);

			// Restore search parms from Session if not searching / reset / export
			if (($this->Export <> "" || $this->Command <> "search" && $this->Command <> "reset" && $this->Command <> "resetall") && $this->CheckSearchParms())
				$this->RestoreSearchParms();

			// Call Recordset SearchValidated event
			$this->Recordset_SearchValidated();

			// Set up sorting order
			$this->SetUpSortOrder();

			// Get basic search criteria
			if ($gsSearchError == "")
				$sSrchBasic = $this->BasicSearchWhere();

			// Get search criteria for advanced search
			if ($gsSearchError == "")
				$sSrchAdvanced = $this->AdvancedSearchWhere();
		}

		// Restore display records
		if ($this->getRecordsPerPage() <> "") {
			$this->DisplayRecs = $this->getRecordsPerPage(); // Restore from Session
		} else {
			$this->DisplayRecs = 10; // Load default
		}

		// Load Sorting Order
		$this->LoadSortOrder();

		// Load search default if no existing search criteria
		if (!$this->CheckSearchParms()) {

			// Load basic search from default
			$this->BasicSearch->LoadDefault();
			if ($this->BasicSearch->Keyword != "")
				$sSrchBasic = $this->BasicSearchWhere();

			// Load advanced search from default
			if ($this->LoadAdvancedSearchDefault()) {
				$sSrchAdvanced = $this->AdvancedSearchWhere();
			}
		}

		// Build search criteria
		ew_AddFilter($this->SearchWhere, $sSrchAdvanced);
		ew_AddFilter($this->SearchWhere, $sSrchBasic);

		// Call Recordset_Searching event
		$this->Recordset_Searching($this->SearchWhere);

		// Save search criteria
		if ($this->Command == "search" && !$this->RestoreSearch) {
			$this->setSearchWhere($this->SearchWhere); // Save to Session
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} else {
			$this->SearchWhere = $this->getSearchWhere();
		}

		// Build filter
		$sFilter = "";
		if (!$Security->CanList())
			$sFilter = "(0=1)"; // Filter all records
		ew_AddFilter($sFilter, $this->DbDetailFilter);
		ew_AddFilter($sFilter, $this->SearchWhere);

		// Set up filter in session
		$this->setSessionWhere($sFilter);
		$this->CurrentFilter = "";

		// Export data only
		if ($this->CustomExport == "" && in_array($this->Export, array("html","word","excel","xml","csv","email","pdf"))) {
			$this->ExportData();
			$this->Page_Terminate(); // Terminate response
			exit();
		}

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
					$this->DisplayRecs = 10; // Non-numeric, load default
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
			$this->Id_Patri->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->Id_Patri->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Advanced search WHERE clause based on QueryString
	function AdvancedSearchWhere($Default = FALSE) {
		global $Security;
		$sWhere = "";
		if (!$Security->CanSearch()) return "";
		$this->BuildSearchSql($sWhere, $this->Localidade, $Default, FALSE); // Localidade
		$this->BuildSearchSql($sWhere, $this->Descricao, $Default, FALSE); // Descricao
		$this->BuildSearchSql($sWhere, $this->DataAquisao, $Default, FALSE); // DataAquisao
		$this->BuildSearchSql($sWhere, $this->Tipo, $Default, FALSE); // Tipo
		$this->BuildSearchSql($sWhere, $this->Estado_do_bem, $Default, FALSE); // Estado_do_bem
		$this->BuildSearchSql($sWhere, $this->Valor_estimado, $Default, FALSE); // Valor_estimado
		$this->BuildSearchSql($sWhere, $this->Situacao, $Default, FALSE); // Situacao
		$this->BuildSearchSql($sWhere, $this->Anotacoes, $Default, FALSE); // Anotacoes

		// Set up search parm
		if (!$Default && $sWhere <> "") {
			$this->Command = "search";
		}
		if (!$Default && $this->Command == "search") {
			$this->Localidade->AdvancedSearch->Save(); // Localidade
			$this->Descricao->AdvancedSearch->Save(); // Descricao
			$this->DataAquisao->AdvancedSearch->Save(); // DataAquisao
			$this->Tipo->AdvancedSearch->Save(); // Tipo
			$this->Estado_do_bem->AdvancedSearch->Save(); // Estado_do_bem
			$this->Valor_estimado->AdvancedSearch->Save(); // Valor_estimado
			$this->Situacao->AdvancedSearch->Save(); // Situacao
			$this->Anotacoes->AdvancedSearch->Save(); // Anotacoes
		}
		return $sWhere;
	}

	// Build search SQL
	function BuildSearchSql(&$Where, &$Fld, $Default, $MultiValue) {
		$FldParm = substr($Fld->FldVar, 2);
		$FldVal = ($Default) ? $Fld->AdvancedSearch->SearchValueDefault : $Fld->AdvancedSearch->SearchValue; // @$_GET["x_$FldParm"]
		$FldOpr = ($Default) ? $Fld->AdvancedSearch->SearchOperatorDefault : $Fld->AdvancedSearch->SearchOperator; // @$_GET["z_$FldParm"]
		$FldCond = ($Default) ? $Fld->AdvancedSearch->SearchConditionDefault : $Fld->AdvancedSearch->SearchCondition; // @$_GET["v_$FldParm"]
		$FldVal2 = ($Default) ? $Fld->AdvancedSearch->SearchValue2Default : $Fld->AdvancedSearch->SearchValue2; // @$_GET["y_$FldParm"]
		$FldOpr2 = ($Default) ? $Fld->AdvancedSearch->SearchOperator2Default : $Fld->AdvancedSearch->SearchOperator2; // @$_GET["w_$FldParm"]
		$sWrk = "";

		//$FldVal = ew_StripSlashes($FldVal);
		if (is_array($FldVal)) $FldVal = implode(",", $FldVal);

		//$FldVal2 = ew_StripSlashes($FldVal2);
		if (is_array($FldVal2)) $FldVal2 = implode(",", $FldVal2);
		$FldOpr = strtoupper(trim($FldOpr));
		if ($FldOpr == "") $FldOpr = "=";
		$FldOpr2 = strtoupper(trim($FldOpr2));
		if ($FldOpr2 == "") $FldOpr2 = "=";
		if (EW_SEARCH_MULTI_VALUE_OPTION == 1 || $FldOpr <> "LIKE" ||
			($FldOpr2 <> "LIKE" && $FldVal2 <> ""))
			$MultiValue = FALSE;
		if ($MultiValue) {
			$sWrk1 = ($FldVal <> "") ? ew_GetMultiSearchSql($Fld, $FldOpr, $FldVal) : ""; // Field value 1
			$sWrk2 = ($FldVal2 <> "") ? ew_GetMultiSearchSql($Fld, $FldOpr2, $FldVal2) : ""; // Field value 2
			$sWrk = $sWrk1; // Build final SQL
			if ($sWrk2 <> "")
				$sWrk = ($sWrk <> "") ? "($sWrk) $FldCond ($sWrk2)" : $sWrk2;
		} else {
			$FldVal = $this->ConvertSearchValue($Fld, $FldVal);
			$FldVal2 = $this->ConvertSearchValue($Fld, $FldVal2);
			$sWrk = ew_GetSearchSql($Fld, $FldVal, $FldOpr, $FldCond, $FldVal2, $FldOpr2);
		}
		ew_AddFilter($Where, $sWrk);
	}

	// Convert search value
	function ConvertSearchValue(&$Fld, $FldVal) {
		if ($FldVal == EW_NULL_VALUE || $FldVal == EW_NOT_NULL_VALUE)
			return $FldVal;
		$Value = $FldVal;
		if ($Fld->FldDataType == EW_DATATYPE_BOOLEAN) {
			if ($FldVal <> "") $Value = ($FldVal == "1" || strtolower(strval($FldVal)) == "y" || strtolower(strval($FldVal)) == "t") ? $Fld->TrueValue : $Fld->FalseValue;
		} elseif ($Fld->FldDataType == EW_DATATYPE_DATE) {
			if ($FldVal <> "") $Value = ew_UnFormatDateTime($FldVal, $Fld->FldDateTimeFormat);
		}
		return $Value;
	}

	// Return basic search SQL
	function BasicSearchSQL($arKeywords, $type) {
		$sWhere = "";
		$this->BuildBasicSearchSQL($sWhere, $this->Localidade, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Descricao, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Anotacoes, $arKeywords, $type);
		return $sWhere;
	}

	// Build basic search SQL
	function BuildBasicSearchSql(&$Where, &$Fld, $arKeywords, $type) {
		$sDefCond = ($type == "OR") ? "OR" : "AND";
		$sCond = $sDefCond;
		$arSQL = array(); // Array for SQL parts
		$arCond = array(); // Array for search conditions
		$cnt = count($arKeywords);
		$j = 0; // Number of SQL parts
		for ($i = 0; $i < $cnt; $i++) {
			$Keyword = $arKeywords[$i];
			$Keyword = trim($Keyword);
			if (EW_BASIC_SEARCH_IGNORE_PATTERN <> "") {
				$Keyword = preg_replace(EW_BASIC_SEARCH_IGNORE_PATTERN, "\\", $Keyword);
				$ar = explode("\\", $Keyword);
			} else {
				$ar = array($Keyword);
			}
			foreach ($ar as $Keyword) {
				if ($Keyword <> "") {
					$sWrk = "";
					if ($Keyword == "OR" && $type == "") {
						if ($j > 0)
							$arCond[$j-1] = "OR";
					} elseif ($Keyword == EW_NULL_VALUE) {
						$sWrk = $Fld->FldExpression . " IS NULL";
					} elseif ($Keyword == EW_NOT_NULL_VALUE) {
						$sWrk = $Fld->FldExpression . " IS NOT NULL";
					} elseif ($Fld->FldDataType != EW_DATATYPE_NUMBER || is_numeric($Keyword)) {
						$sFldExpression = ($Fld->FldVirtualExpression <> $Fld->FldExpression) ? $Fld->FldVirtualExpression : $Fld->FldBasicSearchExpression;
						$sWrk = $sFldExpression . ew_Like(ew_QuotedValue("%" . $Keyword . "%", EW_DATATYPE_STRING));
					}
					if ($sWrk <> "") {
						$arSQL[$j] = $sWrk;
						$arCond[$j] = $sDefCond;
						$j += 1;
					}
				}
			}
		}
		$cnt = count($arSQL);
		$bQuoted = FALSE;
		$sSql = "";
		if ($cnt > 0) {
			for ($i = 0; $i < $cnt-1; $i++) {
				if ($arCond[$i] == "OR") {
					if (!$bQuoted) $sSql .= "(";
					$bQuoted = TRUE;
				}
				$sSql .= $arSQL[$i];
				if ($bQuoted && $arCond[$i] <> "OR") {
					$sSql .= ")";
					$bQuoted = FALSE;
				}
				$sSql .= " " . $arCond[$i] . " ";
			}
			$sSql .= $arSQL[$cnt-1];
			if ($bQuoted)
				$sSql .= ")";
		}
		if ($sSql <> "") {
			if ($Where <> "") $Where .= " OR ";
			$Where .=  "(" . $sSql . ")";
		}
	}

	// Return basic search WHERE clause based on search keyword and type
	function BasicSearchWhere($Default = FALSE) {
		global $Security;
		$sSearchStr = "";
		if (!$Security->CanSearch()) return "";
		$sSearchKeyword = ($Default) ? $this->BasicSearch->KeywordDefault : $this->BasicSearch->Keyword;
		$sSearchType = ($Default) ? $this->BasicSearch->TypeDefault : $this->BasicSearch->Type;
		if ($sSearchKeyword <> "") {
			$sSearch = trim($sSearchKeyword);
			if ($sSearchType <> "=") {
				$ar = array();

				// Match quoted keywords (i.e.: "...")
				if (preg_match_all('/"([^"]*)"/i', $sSearch, $matches, PREG_SET_ORDER)) {
					foreach ($matches as $match) {
						$p = strpos($sSearch, $match[0]);
						$str = substr($sSearch, 0, $p);
						$sSearch = substr($sSearch, $p + strlen($match[0]));
						if (strlen(trim($str)) > 0)
							$ar = array_merge($ar, explode(" ", trim($str)));
						$ar[] = $match[1]; // Save quoted keyword
					}
				}

				// Match individual keywords
				if (strlen(trim($sSearch)) > 0)
					$ar = array_merge($ar, explode(" ", trim($sSearch)));
				$sSearchStr = $this->BasicSearchSQL($ar, $sSearchType);
			} else {
				$sSearchStr = $this->BasicSearchSQL(array($sSearch), $sSearchType);
			}
			if (!$Default) $this->Command = "search";
		}
		if (!$Default && $this->Command == "search") {
			$this->BasicSearch->setKeyword($sSearchKeyword);
			$this->BasicSearch->setType($sSearchType);
		}
		return $sSearchStr;
	}

	// Check if search parm exists
	function CheckSearchParms() {

		// Check basic search
		if ($this->BasicSearch->IssetSession())
			return TRUE;
		if ($this->Localidade->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Descricao->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->DataAquisao->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Tipo->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Estado_do_bem->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Valor_estimado->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Situacao->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Anotacoes->AdvancedSearch->IssetSession())
			return TRUE;
		return FALSE;
	}

	// Clear all search parameters
	function ResetSearchParms() {

		// Clear search WHERE clause
		$this->SearchWhere = "";
		$this->setSearchWhere($this->SearchWhere);

		// Clear basic search parameters
		$this->ResetBasicSearchParms();

		// Clear advanced search parameters
		$this->ResetAdvancedSearchParms();
	}

	// Load advanced search default values
	function LoadAdvancedSearchDefault() {
		return FALSE;
	}

	// Clear all basic search parameters
	function ResetBasicSearchParms() {
		$this->BasicSearch->UnsetSession();
	}

	// Clear all advanced search parameters
	function ResetAdvancedSearchParms() {
		$this->Localidade->AdvancedSearch->UnsetSession();
		$this->Descricao->AdvancedSearch->UnsetSession();
		$this->DataAquisao->AdvancedSearch->UnsetSession();
		$this->Tipo->AdvancedSearch->UnsetSession();
		$this->Estado_do_bem->AdvancedSearch->UnsetSession();
		$this->Valor_estimado->AdvancedSearch->UnsetSession();
		$this->Situacao->AdvancedSearch->UnsetSession();
		$this->Anotacoes->AdvancedSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore basic search values
		$this->BasicSearch->Load();

		// Restore advanced search values
		$this->Localidade->AdvancedSearch->Load();
		$this->Descricao->AdvancedSearch->Load();
		$this->DataAquisao->AdvancedSearch->Load();
		$this->Tipo->AdvancedSearch->Load();
		$this->Estado_do_bem->AdvancedSearch->Load();
		$this->Valor_estimado->AdvancedSearch->Load();
		$this->Situacao->AdvancedSearch->Load();
		$this->Anotacoes->AdvancedSearch->Load();
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for Ctrl pressed
		$bCtrl = (@$_GET["ctrl"] <> "");

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->Localidade, $bCtrl); // Localidade
			$this->UpdateSort($this->Descricao, $bCtrl); // Descricao
			$this->UpdateSort($this->DataAquisao, $bCtrl); // DataAquisao
			$this->UpdateSort($this->Tipo, $bCtrl); // Tipo
			$this->UpdateSort($this->Estado_do_bem, $bCtrl); // Estado_do_bem
			$this->UpdateSort($this->Valor_estimado, $bCtrl); // Valor_estimado
			$this->UpdateSort($this->Situacao, $bCtrl); // Situacao
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

			// Reset search criteria
			if ($this->Command == "reset" || $this->Command == "resetall")
				$this->ResetSearchParms();

			// Reset sorting order
			if ($this->Command == "resetsort") {
				$sOrderBy = "";
				$this->setSessionOrderBy($sOrderBy);
				$this->Localidade->setSort("");
				$this->Descricao->setSort("");
				$this->DataAquisao->setSort("");
				$this->Tipo->setSort("");
				$this->Estado_do_bem->setSort("");
				$this->Valor_estimado->setSort("");
				$this->Situacao->setSort("");
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
		$oListOpt->Body = "<input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->Id_Patri->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event, this);'>";
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
				$item->Body = "<a class=\"ewAction ewCustomAction\" href=\"\" onclick=\"ew_SubmitSelected(document.fbens_patrimoniaislist, '" . ew_CurrentUrl() . "', null, '" . $action . "');return false;\">" . $name . "</a>";
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

		// Search button
		$item = &$this->SearchOptions->Add("searchtoggle");
		$SearchToggleClass = ($this->SearchWhere <> "") ? " active" : "";
		$item->Body = "<button type=\"button\" class=\"btn btn-warning ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $Language->Phrase("SearchPanel") . "\" data-caption=\"" . $Language->Phrase("SearchPanel") . "\" data-toggle=\"button\" data-form=\"fbens_patrimoniaislistsrch\">" . $Language->Phrase("SearchBtn") . "</button>";
		$item->Visible = TRUE;

		// Show all button
		$item = &$this->SearchOptions->Add("showall");
		$item->Body = "<a class=\"btn btn-default ewShowAll\" title=\"" . $Language->Phrase("ShowAll") . "\" data-caption=\"" . $Language->Phrase("ShowAll") . "\" href=\"" . $this->PageUrl() . "cmd=reset\">" . $Language->Phrase("ShowAllBtn") . "</a>";
		$item->Visible = ($this->SearchWhere <> $this->DefaultSearchWhere && $this->SearchWhere <> "0=101");

		// Advanced search button
		$item = &$this->SearchOptions->Add("advancedsearch");
		$item->Body = "<a class=\"btn btn-default ewAdvancedSearch\" title=\"" . $Language->Phrase("AdvancedSearch") . "\" data-caption=\"" . $Language->Phrase("AdvancedSearch") . "\" href=\"bens_patrimoniaissrch.php\">" . $Language->Phrase("AdvancedSearchBtn") . "</a>";
		$item->Visible = TRUE;

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

	// Load basic search values
	function LoadBasicSearchValues() {
		$this->BasicSearch->Keyword = @$_GET[EW_TABLE_BASIC_SEARCH];
		if ($this->BasicSearch->Keyword <> "") $this->Command = "search";
		$this->BasicSearch->Type = @$_GET[EW_TABLE_BASIC_SEARCH_TYPE];
	}

	//  Load search values for validation
	function LoadSearchValues() {
		global $objForm;

		// Load search values
		// Localidade

		$this->Localidade->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Localidade"]);
		if ($this->Localidade->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Localidade->AdvancedSearch->SearchOperator = @$_GET["z_Localidade"];

		// Descricao
		$this->Descricao->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Descricao"]);
		if ($this->Descricao->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Descricao->AdvancedSearch->SearchOperator = @$_GET["z_Descricao"];

		// DataAquisao
		$this->DataAquisao->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_DataAquisao"]);
		if ($this->DataAquisao->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->DataAquisao->AdvancedSearch->SearchOperator = @$_GET["z_DataAquisao"];

		// Tipo
		$this->Tipo->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Tipo"]);
		if ($this->Tipo->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Tipo->AdvancedSearch->SearchOperator = @$_GET["z_Tipo"];

		// Estado_do_bem
		$this->Estado_do_bem->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Estado_do_bem"]);
		if ($this->Estado_do_bem->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Estado_do_bem->AdvancedSearch->SearchOperator = @$_GET["z_Estado_do_bem"];

		// Valor_estimado
		$this->Valor_estimado->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Valor_estimado"]);
		if ($this->Valor_estimado->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Valor_estimado->AdvancedSearch->SearchOperator = @$_GET["z_Valor_estimado"];

		// Situacao
		$this->Situacao->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Situacao"]);
		if ($this->Situacao->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Situacao->AdvancedSearch->SearchOperator = @$_GET["z_Situacao"];

		// Anotacoes
		$this->Anotacoes->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Anotacoes"]);
		if ($this->Anotacoes->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Anotacoes->AdvancedSearch->SearchOperator = @$_GET["z_Anotacoes"];
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

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("Id_Patri")) <> "")
			$this->Id_Patri->CurrentValue = $this->getKey("Id_Patri"); // Id_Patri
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
		if ($this->Valor_estimado->FormValue == $this->Valor_estimado->CurrentValue && is_numeric(ew_StrToFloat($this->Valor_estimado->CurrentValue)))
			$this->Valor_estimado->CurrentValue = ew_StrToFloat($this->Valor_estimado->CurrentValue);

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
		// Id_Patri

		$this->Id_Patri->CellCssStyle = "white-space: nowrap;";

		// Localidade
		// Descricao
		// DataAquisao
		// Tipo
		// Estado_do_bem
		// Valor_estimado
		// Situacao
		// Anotacoes
		// Accumulate aggregate value

		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT && $this->RowType <> EW_ROWTYPE_AGGREGATE) {
			if (is_numeric($this->Valor_estimado->CurrentValue))
				$this->Valor_estimado->Total += $this->Valor_estimado->CurrentValue; // Accumulate total
		}
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
		} elseif ($this->RowType == EW_ROWTYPE_AGGREGATEINIT) { // Initialize aggregate row
			$this->Valor_estimado->Total = 0; // Initialize total
		} elseif ($this->RowType == EW_ROWTYPE_AGGREGATE) { // Aggregate row
			$this->Valor_estimado->CurrentValue = $this->Valor_estimado->Total;
			$this->Valor_estimado->ViewValue = $this->Valor_estimado->CurrentValue;
			$this->Valor_estimado->ViewValue = ew_FormatCurrency($this->Valor_estimado->ViewValue, 2, -2, -2, -2);
			$this->Valor_estimado->CellCssStyle .= "text-align: right;";
			$this->Valor_estimado->ViewCustomAttributes = "";
			$this->Valor_estimado->HrefValue = ""; // Clear href value
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Validate search
	function ValidateSearch() {
		global $gsSearchError;

		// Initialize
		$gsSearchError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return TRUE;

		// Return validate result
		$ValidateSearch = ($gsSearchError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateSearch = $ValidateSearch && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsSearchError, $sFormCustomError);
		}
		return $ValidateSearch;
	}

	// Load advanced search
	function LoadAdvancedSearch() {
		$this->Localidade->AdvancedSearch->Load();
		$this->Descricao->AdvancedSearch->Load();
		$this->DataAquisao->AdvancedSearch->Load();
		$this->Tipo->AdvancedSearch->Load();
		$this->Estado_do_bem->AdvancedSearch->Load();
		$this->Valor_estimado->AdvancedSearch->Load();
		$this->Situacao->AdvancedSearch->Load();
		$this->Anotacoes->AdvancedSearch->Load();
	}

	// Set up export options
	function SetupExportOptions() {
		global $Language;

		// Printer friendly
		$item = &$this->ExportOptions->Add("print");
		$item->Body = "<a href=\"" . $this->ExportPrintUrl . "\" class=\"ewExportLink ewPrint\" title=\"" . ew_HtmlEncode($Language->Phrase("PrinterFriendlyText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("PrinterFriendlyText")) . "\">" . $Language->Phrase("PrinterFriendly") . "</a>";
		$item->Visible = TRUE;

		// Export to Excel
		$item = &$this->ExportOptions->Add("excel");
		$item->Body = "<a href=\"" . $this->ExportExcelUrl . "\" class=\"ewExportLink ewExcel\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToExcelText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToExcelText")) . "\">" . $Language->Phrase("ExportToExcel") . "</a>";
		$item->Visible = TRUE;

		// Export to Word
		$item = &$this->ExportOptions->Add("word");
		$item->Body = "<a href=\"" . $this->ExportWordUrl . "\" class=\"ewExportLink ewWord\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToWordText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToWordText")) . "\">" . $Language->Phrase("ExportToWord") . "</a>";
		$item->Visible = TRUE;

		// Export to Html
		$item = &$this->ExportOptions->Add("html");
		$item->Body = "<a href=\"" . $this->ExportHtmlUrl . "\" class=\"ewExportLink ewHtml\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToHtmlText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToHtmlText")) . "\">" . $Language->Phrase("ExportToHtml") . "</a>";
		$item->Visible = FALSE;

		// Export to Xml
		$item = &$this->ExportOptions->Add("xml");
		$item->Body = "<a href=\"" . $this->ExportXmlUrl . "\" class=\"ewExportLink ewXml\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToXmlText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToXmlText")) . "\">" . $Language->Phrase("ExportToXml") . "</a>";
		$item->Visible = FALSE;

		// Export to Csv
		$item = &$this->ExportOptions->Add("csv");
		$item->Body = "<a href=\"" . $this->ExportCsvUrl . "\" class=\"ewExportLink ewCsv\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToCsvText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToCsvText")) . "\">" . $Language->Phrase("ExportToCsv") . "</a>";
		$item->Visible = FALSE;

		// Export to Pdf
		$item = &$this->ExportOptions->Add("pdf");
		$item->Body = "<a href=\"" . $this->ExportPdfUrl . "\" class=\"ewExportLink ewPdf\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToPDFText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToPDFText")) . "\">" . $Language->Phrase("ExportToPDF") . "</a>";
		$item->Visible = FALSE;

		// Export to Email
		$item = &$this->ExportOptions->Add("email");
		$url = "";
		$item->Body = "<button id=\"emf_bens_patrimoniais\" class=\"ewExportLink ewEmail\" title=\"" . $Language->Phrase("ExportToEmailText") . "\" data-caption=\"" . $Language->Phrase("ExportToEmailText") . "\" onclick=\"ew_EmailDialogShow({lnk:'emf_bens_patrimoniais',hdr:ewLanguage.Phrase('ExportToEmailText'),f:document.fbens_patrimoniaislist,sel:false" . $url . "});\">" . $Language->Phrase("ExportToEmail") . "</button>";
		$item->Visible = FALSE;

		// Drop down button for export
		$this->ExportOptions->UseButtonGroup = TRUE;
		$this->ExportOptions->UseImageAndText = TRUE;
		$this->ExportOptions->UseDropDownButton = FALSE;
		if ($this->ExportOptions->UseButtonGroup && ew_IsMobile())
			$this->ExportOptions->UseDropDownButton = TRUE;
		$this->ExportOptions->DropDownButtonPhrase = $Language->Phrase("ButtonExport");

		// Add group option item
		$item = &$this->ExportOptions->Add($this->ExportOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;
	}

	// Export data in HTML/CSV/Word/Excel/XML/Email/PDF format
	function ExportData() {
		$utf8 = (strtolower(EW_CHARSET) == "utf-8");
		$bSelectLimit = EW_SELECT_LIMIT;

		// Load recordset
		if ($bSelectLimit) {
			$this->TotalRecs = $this->SelectRecordCount();
		} else {
			if ($rs = $this->LoadRecordset())
				$this->TotalRecs = $rs->RecordCount();
		}
		$this->StartRec = 1;

		// Export all
		if ($this->ExportAll) {
			set_time_limit(EW_EXPORT_ALL_TIME_LIMIT);
			$this->DisplayRecs = $this->TotalRecs;
			$this->StopRec = $this->TotalRecs;
		} else { // Export one page only
			$this->SetUpStartRec(); // Set up start record position

			// Set the last record to display
			if ($this->DisplayRecs <= 0) {
				$this->StopRec = $this->TotalRecs;
			} else {
				$this->StopRec = $this->StartRec + $this->DisplayRecs - 1;
			}
		}
		if ($bSelectLimit)
			$rs = $this->LoadRecordset($this->StartRec-1, $this->DisplayRecs <= 0 ? $this->TotalRecs : $this->DisplayRecs);
		if (!$rs) {
			header("Content-Type:"); // Remove header
			header("Content-Disposition:");
			$this->ShowMessage();
			return;
		}
		$this->ExportDoc = ew_ExportDocument($this, "h");
		$Doc = &$this->ExportDoc;
		if ($bSelectLimit) {
			$this->StartRec = 1;
			$this->StopRec = $this->DisplayRecs <= 0 ? $this->TotalRecs : $this->DisplayRecs;
		} else {

			//$this->StartRec = $this->StartRec;
			//$this->StopRec = $this->StopRec;

		}

		// Call Page Exporting server event
		$this->ExportDoc->ExportCustom = !$this->Page_Exporting();
		$ParentTable = "";
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		$Doc->Text .= $sHeader;
		$this->ExportDocument($Doc, $rs, $this->StartRec, $this->StopRec, "");
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		$Doc->Text .= $sFooter;

		// Close recordset
		$rs->Close();

		// Export header and footer
		$Doc->ExportHeaderAndFooter();

		// Call Page Exported server event
		$this->Page_Exported();

		// Clean output buffer
		if (!EW_DEBUG_ENABLED && ob_get_length())
			ob_end_clean();

		// Write debug message if enabled
		if (EW_DEBUG_ENABLED)
			echo ew_DebugMsg();

		// Output data
		$Doc->Export();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$Breadcrumb->Add("list", $this->TableVar, $url, "", $this->TableVar, TRUE);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'bens_patrimoniais';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
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
if (!isset($bens_patrimoniais_list)) $bens_patrimoniais_list = new cbens_patrimoniais_list();

// Page init
$bens_patrimoniais_list->Page_Init();

// Page main
$bens_patrimoniais_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$bens_patrimoniais_list->Page_Render();
?>
<?php include_once "header.php" ?>
<?php if ($bens_patrimoniais->Export == "") { ?>
<script type="text/javascript">

// Page object
var bens_patrimoniais_list = new ew_Page("bens_patrimoniais_list");
bens_patrimoniais_list.PageID = "list"; // Page ID
var EW_PAGE_ID = bens_patrimoniais_list.PageID; // For backward compatibility

// Form object
var fbens_patrimoniaislist = new ew_Form("fbens_patrimoniaislist");
fbens_patrimoniaislist.FormKeyCountName = '<?php echo $bens_patrimoniais_list->FormKeyCountName ?>';

// Form_CustomValidate event
fbens_patrimoniaislist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fbens_patrimoniaislist.ValidateRequired = true;
<?php } else { ?>
fbens_patrimoniaislist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fbens_patrimoniaislist.Lists["x_Localidade"] = {"LinkField":"x_Id_igreja","Ajax":null,"AutoFill":false,"DisplayFields":["x_Igreja","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fbens_patrimoniaislist.Lists["x_Estado_do_bem"] = {"LinkField":"x_Id_est_patri","Ajax":null,"AutoFill":false,"DisplayFields":["x_Estado_do_Bem","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
var fbens_patrimoniaislistsrch = new ew_Form("fbens_patrimoniaislistsrch");

// Init search panel as collapsed
if (fbens_patrimoniaislistsrch) fbens_patrimoniaislistsrch.InitSearchPanel = true;
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php if ($bens_patrimoniais->Export == "") { ?>
<div class="ewToolbar">
<?php if ($bens_patrimoniais->Export == "") { ?>
<?php $Breadcrumb->Render(); ?>
<?php } ?>
<?php if ($bens_patrimoniais_list->TotalRecs > 0 && $bens_patrimoniais_list->ExportOptions->Visible()) { ?>
<?php $bens_patrimoniais_list->ExportOptions->Render("body") ?>
<?php } ?>
<?php if ($bens_patrimoniais_list->SearchOptions->Visible()) { ?>
<?php $bens_patrimoniais_list->SearchOptions->Render("body") ?>
<?php } ?>
<?php if ($bens_patrimoniais->Export == "") { ?>
<?php echo $Language->SelectionForm(); ?>
<?php } ?>
<div class="ewSearchOption ewListOptionSeparator" style="white-space: nowrap;" data-name="button"><div class="btn-group ewButtonGroup">
<?php $help = ew_ExecuteScalar("Select txt from ajuda where pg = '".ew_CurrentPage()."'") ; 
if (strlen($help)>0){ ?>
	<button class="btn btn-default" type="button" title="" data-original-title="Ajuda desta p&aacute;gina" id="ajuda"><span data-phrase="SearchBtn" class="fa fa-question ewIcon" data-caption="Ajuda"></span></button>		
<?php } ?>	
</div></div>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php
	$bSelectLimit = EW_SELECT_LIMIT;
	if ($bSelectLimit) {
		if ($bens_patrimoniais_list->TotalRecs <= 0)
			$bens_patrimoniais_list->TotalRecs = $bens_patrimoniais->SelectRecordCount();
	} else {
		if (!$bens_patrimoniais_list->Recordset && ($bens_patrimoniais_list->Recordset = $bens_patrimoniais_list->LoadRecordset()))
			$bens_patrimoniais_list->TotalRecs = $bens_patrimoniais_list->Recordset->RecordCount();
	}
	$bens_patrimoniais_list->StartRec = 1;
	if ($bens_patrimoniais_list->DisplayRecs <= 0 || ($bens_patrimoniais->Export <> "" && $bens_patrimoniais->ExportAll)) // Display all records
		$bens_patrimoniais_list->DisplayRecs = $bens_patrimoniais_list->TotalRecs;
	if (!($bens_patrimoniais->Export <> "" && $bens_patrimoniais->ExportAll))
		$bens_patrimoniais_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$bens_patrimoniais_list->Recordset = $bens_patrimoniais_list->LoadRecordset($bens_patrimoniais_list->StartRec-1, $bens_patrimoniais_list->DisplayRecs);

	// Set no record found message
	if ($bens_patrimoniais->CurrentAction == "" && $bens_patrimoniais_list->TotalRecs == 0) {
		if (!$Security->CanList())
			$bens_patrimoniais_list->setWarningMessage($Language->Phrase("NoPermission"));
		if ($bens_patrimoniais_list->SearchWhere == "0=101")
			$bens_patrimoniais_list->setWarningMessage($Language->Phrase("EnterSearchCriteria"));
		else
			$bens_patrimoniais_list->setWarningMessage($Language->Phrase("NoRecord"));
	}
$bens_patrimoniais_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($bens_patrimoniais->Export == "" && $bens_patrimoniais->CurrentAction == "") { ?>
<form name="fbens_patrimoniaislistsrch" id="fbens_patrimoniaislistsrch" class="form-inline ewForm" action="<?php echo ew_CurrentPage() ?>">
<?php $SearchPanelClass = ($bens_patrimoniais_list->SearchWhere <> "") ? " in" : ""; ?>
<div id="fbens_patrimoniaislistsrch_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="bens_patrimoniais">
	<div class="ewBasicSearch">
<div id="xsr_1" class="ewRow">
	<div class="ewQuickSearch input-group">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="form-control" value="<?php echo ew_HtmlEncode($bens_patrimoniais_list->BasicSearch->getKeyword()) ?>">
	<input type="hidden" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="<?php echo ew_HtmlEncode($bens_patrimoniais_list->BasicSearch->getType()) ?>">
	<div class="input-group-btn">
		<!-- <button type="button" data-toggle="dropdown" class="btn btn-default"><span id="searchtype"><?php echo $bens_patrimoniais_list->BasicSearch->getTypeNameShort() ?></span><span class="caret"></span></button>
		<ul class="dropdown-menu pull-right" role="menu">
			<li<?php if ($bens_patrimoniais_list->BasicSearch->getType() == "") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this)"><?php echo $Language->Phrase("QuickSearchAuto") ?></a></li>
			<li<?php if ($bens_patrimoniais_list->BasicSearch->getType() == "=") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'=')"><?php echo $Language->Phrase("QuickSearchExact") ?></a></li>
			<li<?php if ($bens_patrimoniais_list->BasicSearch->getType() == "AND") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'AND')"><?php echo $Language->Phrase("QuickSearchAll") ?></a></li>
			<li<?php if ($bens_patrimoniais_list->BasicSearch->getType() == "OR") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'OR')"><?php echo $Language->Phrase("QuickSearchAny") ?></a></li>
		</ul> -->
	<button class="btn btn-primary ewButton" name="btnsubmit" id="btnsubmit" type="submit"><i class='glyphicon glyphicon-search'></i>&nbsp;<?php echo $Language->Phrase("QuickSearchBtn") ?></button>
	</div>
	</div>
</div>
	</div>
</div>
</form>
<?php } ?>
<?php } ?>
<?php $bens_patrimoniais_list->ShowPageHeader(); ?>
<?php
$bens_patrimoniais_list->ShowMessage();
?>
<?php if ($bens_patrimoniais_list->TotalRecs > 0 || $bens_patrimoniais->CurrentAction <> "") { ?>
<div class="ewGrid">
<?php if ($bens_patrimoniais->Export == "") { ?>
<div class="ewGridUpperPanel">
<?php if ($bens_patrimoniais->CurrentAction <> "gridadd" && $bens_patrimoniais->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="form-inline ewForm ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($bens_patrimoniais_list->Pager)) $bens_patrimoniais_list->Pager = new cPrevNextPager($bens_patrimoniais_list->StartRec, $bens_patrimoniais_list->DisplayRecs, $bens_patrimoniais_list->TotalRecs) ?>
<?php if ($bens_patrimoniais_list->Pager->RecordCount > 0) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($bens_patrimoniais_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $bens_patrimoniais_list->PageUrl() ?>start=<?php echo $bens_patrimoniais_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($bens_patrimoniais_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $bens_patrimoniais_list->PageUrl() ?>start=<?php echo $bens_patrimoniais_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $bens_patrimoniais_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($bens_patrimoniais_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $bens_patrimoniais_list->PageUrl() ?>start=<?php echo $bens_patrimoniais_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($bens_patrimoniais_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $bens_patrimoniais_list->PageUrl() ?>start=<?php echo $bens_patrimoniais_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $bens_patrimoniais_list->Pager->PageCount ?></span>
</div>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $bens_patrimoniais_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $bens_patrimoniais_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $bens_patrimoniais_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
<?php if ($bens_patrimoniais_list->TotalRecs > 0) { ?>
<div class="ewPager">
<input type="hidden" name="t" value="bens_patrimoniais">
<select name="<?php echo EW_TABLE_REC_PER_PAGE ?>" class="form-control input-sm" onchange="this.form.submit();">
<option value="10"<?php if ($bens_patrimoniais_list->DisplayRecs == 10) { ?> selected="selected"<?php } ?>>10</option>
<option value="20"<?php if ($bens_patrimoniais_list->DisplayRecs == 20) { ?> selected="selected"<?php } ?>>20</option>
<option value="50"<?php if ($bens_patrimoniais_list->DisplayRecs == 50) { ?> selected="selected"<?php } ?>>50</option>
<option value="ALL"<?php if ($bens_patrimoniais->getRecordsPerPage() == -1) { ?> selected="selected"<?php } ?>><?php echo $Language->Phrase("AllRecords") ?></option>
</select>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($bens_patrimoniais_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
<div class="clearfix"></div>
</div>
<?php } ?>
<form name="fbens_patrimoniaislist" id="fbens_patrimoniaislist" class="form-inline ewForm ewListForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($bens_patrimoniais_list->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $bens_patrimoniais_list->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="bens_patrimoniais">
<div id="gmp_bens_patrimoniais" class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php if ($bens_patrimoniais_list->TotalRecs > 0) { ?>
<table id="tbl_bens_patrimoniaislist" class="table ewTable">
<?php echo $bens_patrimoniais->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$bens_patrimoniais_list->RenderListOptions();

// Render list options (header, left)
$bens_patrimoniais_list->ListOptions->Render("header", "left");
?>
<?php if ($bens_patrimoniais->Localidade->Visible) { // Localidade ?>
	<?php if ($bens_patrimoniais->SortUrl($bens_patrimoniais->Localidade) == "") { ?>
		<th data-name="Localidade"><div id="elh_bens_patrimoniais_Localidade" class="bens_patrimoniais_Localidade"><div class="ewTableHeaderCaption"><?php echo $bens_patrimoniais->Localidade->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Localidade"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $bens_patrimoniais->SortUrl($bens_patrimoniais->Localidade) ?>',2);"><div id="elh_bens_patrimoniais_Localidade" class="bens_patrimoniais_Localidade">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $bens_patrimoniais->Localidade->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($bens_patrimoniais->Localidade->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($bens_patrimoniais->Localidade->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($bens_patrimoniais->Descricao->Visible) { // Descricao ?>
	<?php if ($bens_patrimoniais->SortUrl($bens_patrimoniais->Descricao) == "") { ?>
		<th data-name="Descricao"><div id="elh_bens_patrimoniais_Descricao" class="bens_patrimoniais_Descricao"><div class="ewTableHeaderCaption"><?php echo $bens_patrimoniais->Descricao->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Descricao"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $bens_patrimoniais->SortUrl($bens_patrimoniais->Descricao) ?>',2);"><div id="elh_bens_patrimoniais_Descricao" class="bens_patrimoniais_Descricao">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $bens_patrimoniais->Descricao->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($bens_patrimoniais->Descricao->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($bens_patrimoniais->Descricao->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($bens_patrimoniais->DataAquisao->Visible) { // DataAquisao ?>
	<?php if ($bens_patrimoniais->SortUrl($bens_patrimoniais->DataAquisao) == "") { ?>
		<th data-name="DataAquisao"><div id="elh_bens_patrimoniais_DataAquisao" class="bens_patrimoniais_DataAquisao"><div class="ewTableHeaderCaption"><?php echo $bens_patrimoniais->DataAquisao->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="DataAquisao"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $bens_patrimoniais->SortUrl($bens_patrimoniais->DataAquisao) ?>',2);"><div id="elh_bens_patrimoniais_DataAquisao" class="bens_patrimoniais_DataAquisao">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $bens_patrimoniais->DataAquisao->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($bens_patrimoniais->DataAquisao->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($bens_patrimoniais->DataAquisao->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($bens_patrimoniais->Tipo->Visible) { // Tipo ?>
	<?php if ($bens_patrimoniais->SortUrl($bens_patrimoniais->Tipo) == "") { ?>
		<th data-name="Tipo"><div id="elh_bens_patrimoniais_Tipo" class="bens_patrimoniais_Tipo"><div class="ewTableHeaderCaption"><?php echo $bens_patrimoniais->Tipo->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Tipo"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $bens_patrimoniais->SortUrl($bens_patrimoniais->Tipo) ?>',2);"><div id="elh_bens_patrimoniais_Tipo" class="bens_patrimoniais_Tipo">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $bens_patrimoniais->Tipo->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($bens_patrimoniais->Tipo->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($bens_patrimoniais->Tipo->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($bens_patrimoniais->Estado_do_bem->Visible) { // Estado_do_bem ?>
	<?php if ($bens_patrimoniais->SortUrl($bens_patrimoniais->Estado_do_bem) == "") { ?>
		<th data-name="Estado_do_bem"><div id="elh_bens_patrimoniais_Estado_do_bem" class="bens_patrimoniais_Estado_do_bem"><div class="ewTableHeaderCaption"><?php echo $bens_patrimoniais->Estado_do_bem->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Estado_do_bem"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $bens_patrimoniais->SortUrl($bens_patrimoniais->Estado_do_bem) ?>',2);"><div id="elh_bens_patrimoniais_Estado_do_bem" class="bens_patrimoniais_Estado_do_bem">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $bens_patrimoniais->Estado_do_bem->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($bens_patrimoniais->Estado_do_bem->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($bens_patrimoniais->Estado_do_bem->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($bens_patrimoniais->Valor_estimado->Visible) { // Valor_estimado ?>
	<?php if ($bens_patrimoniais->SortUrl($bens_patrimoniais->Valor_estimado) == "") { ?>
		<th data-name="Valor_estimado"><div id="elh_bens_patrimoniais_Valor_estimado" class="bens_patrimoniais_Valor_estimado"><div class="ewTableHeaderCaption"><?php echo $bens_patrimoniais->Valor_estimado->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Valor_estimado"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $bens_patrimoniais->SortUrl($bens_patrimoniais->Valor_estimado) ?>',2);"><div id="elh_bens_patrimoniais_Valor_estimado" class="bens_patrimoniais_Valor_estimado">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $bens_patrimoniais->Valor_estimado->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($bens_patrimoniais->Valor_estimado->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($bens_patrimoniais->Valor_estimado->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($bens_patrimoniais->Situacao->Visible) { // Situacao ?>
	<?php if ($bens_patrimoniais->SortUrl($bens_patrimoniais->Situacao) == "") { ?>
		<th data-name="Situacao"><div id="elh_bens_patrimoniais_Situacao" class="bens_patrimoniais_Situacao"><div class="ewTableHeaderCaption"><?php echo $bens_patrimoniais->Situacao->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Situacao"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $bens_patrimoniais->SortUrl($bens_patrimoniais->Situacao) ?>',2);"><div id="elh_bens_patrimoniais_Situacao" class="bens_patrimoniais_Situacao">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $bens_patrimoniais->Situacao->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($bens_patrimoniais->Situacao->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($bens_patrimoniais->Situacao->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$bens_patrimoniais_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($bens_patrimoniais->ExportAll && $bens_patrimoniais->Export <> "") {
	$bens_patrimoniais_list->StopRec = $bens_patrimoniais_list->TotalRecs;
} else {

	// Set the last record to display
	if ($bens_patrimoniais_list->TotalRecs > $bens_patrimoniais_list->StartRec + $bens_patrimoniais_list->DisplayRecs - 1)
		$bens_patrimoniais_list->StopRec = $bens_patrimoniais_list->StartRec + $bens_patrimoniais_list->DisplayRecs - 1;
	else
		$bens_patrimoniais_list->StopRec = $bens_patrimoniais_list->TotalRecs;
}
$bens_patrimoniais_list->RecCnt = $bens_patrimoniais_list->StartRec - 1;
if ($bens_patrimoniais_list->Recordset && !$bens_patrimoniais_list->Recordset->EOF) {
	$bens_patrimoniais_list->Recordset->MoveFirst();
	$bSelectLimit = EW_SELECT_LIMIT;
	if (!$bSelectLimit && $bens_patrimoniais_list->StartRec > 1)
		$bens_patrimoniais_list->Recordset->Move($bens_patrimoniais_list->StartRec - 1);
} elseif (!$bens_patrimoniais->AllowAddDeleteRow && $bens_patrimoniais_list->StopRec == 0) {
	$bens_patrimoniais_list->StopRec = $bens_patrimoniais->GridAddRowCount;
}

// Initialize aggregate
$bens_patrimoniais->RowType = EW_ROWTYPE_AGGREGATEINIT;
$bens_patrimoniais->ResetAttrs();
$bens_patrimoniais_list->RenderRow();
while ($bens_patrimoniais_list->RecCnt < $bens_patrimoniais_list->StopRec) {
	$bens_patrimoniais_list->RecCnt++;
	if (intval($bens_patrimoniais_list->RecCnt) >= intval($bens_patrimoniais_list->StartRec)) {
		$bens_patrimoniais_list->RowCnt++;

		// Set up key count
		$bens_patrimoniais_list->KeyCount = $bens_patrimoniais_list->RowIndex;

		// Init row class and style
		$bens_patrimoniais->ResetAttrs();
		$bens_patrimoniais->CssClass = "";
		if ($bens_patrimoniais->CurrentAction == "gridadd") {
		} else {
			$bens_patrimoniais_list->LoadRowValues($bens_patrimoniais_list->Recordset); // Load row values
		}
		$bens_patrimoniais->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$bens_patrimoniais->RowAttrs = array_merge($bens_patrimoniais->RowAttrs, array('data-rowindex'=>$bens_patrimoniais_list->RowCnt, 'id'=>'r' . $bens_patrimoniais_list->RowCnt . '_bens_patrimoniais', 'data-rowtype'=>$bens_patrimoniais->RowType));

		// Render row
		$bens_patrimoniais_list->RenderRow();

		// Render list options
		$bens_patrimoniais_list->RenderListOptions();
?>
	<tr<?php echo $bens_patrimoniais->RowAttributes() ?>>
<?php

// Render list options (body, left)
$bens_patrimoniais_list->ListOptions->Render("body", "left", $bens_patrimoniais_list->RowCnt);
?>
	<?php if ($bens_patrimoniais->Localidade->Visible) { // Localidade ?>
		<td data-name="Localidade"<?php echo $bens_patrimoniais->Localidade->CellAttributes() ?>>
<span<?php echo $bens_patrimoniais->Localidade->ViewAttributes() ?>>
<?php echo $bens_patrimoniais->Localidade->ListViewValue() ?></span>
<a id="<?php echo $bens_patrimoniais_list->PageObjName . "_row_" . $bens_patrimoniais_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($bens_patrimoniais->Descricao->Visible) { // Descricao ?>
		<td data-name="Descricao"<?php echo $bens_patrimoniais->Descricao->CellAttributes() ?>>
<span<?php echo $bens_patrimoniais->Descricao->ViewAttributes() ?>>
<?php echo $bens_patrimoniais->Descricao->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($bens_patrimoniais->DataAquisao->Visible) { // DataAquisao ?>
		<td data-name="DataAquisao"<?php echo $bens_patrimoniais->DataAquisao->CellAttributes() ?>>
<span<?php echo $bens_patrimoniais->DataAquisao->ViewAttributes() ?>>
<?php echo $bens_patrimoniais->DataAquisao->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($bens_patrimoniais->Tipo->Visible) { // Tipo ?>
		<td data-name="Tipo"<?php echo $bens_patrimoniais->Tipo->CellAttributes() ?>>
<span<?php echo $bens_patrimoniais->Tipo->ViewAttributes() ?>>
<?php echo $bens_patrimoniais->Tipo->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($bens_patrimoniais->Estado_do_bem->Visible) { // Estado_do_bem ?>
		<td data-name="Estado_do_bem"<?php echo $bens_patrimoniais->Estado_do_bem->CellAttributes() ?>>
<span<?php echo $bens_patrimoniais->Estado_do_bem->ViewAttributes() ?>>
<?php echo $bens_patrimoniais->Estado_do_bem->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($bens_patrimoniais->Valor_estimado->Visible) { // Valor_estimado ?>
		<td data-name="Valor_estimado"<?php echo $bens_patrimoniais->Valor_estimado->CellAttributes() ?>>
<span<?php echo $bens_patrimoniais->Valor_estimado->ViewAttributes() ?>>
<?php echo $bens_patrimoniais->Valor_estimado->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($bens_patrimoniais->Situacao->Visible) { // Situacao ?>
		<td data-name="Situacao"<?php echo $bens_patrimoniais->Situacao->CellAttributes() ?>>
<span<?php echo $bens_patrimoniais->Situacao->ViewAttributes() ?>>
<?php echo $bens_patrimoniais->Situacao->ListViewValue() ?></span>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$bens_patrimoniais_list->ListOptions->Render("body", "right", $bens_patrimoniais_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($bens_patrimoniais->CurrentAction <> "gridadd")
		$bens_patrimoniais_list->Recordset->MoveNext();
}
?>
</tbody>
<?php

// Render aggregate row
$bens_patrimoniais->RowType = EW_ROWTYPE_AGGREGATE;
$bens_patrimoniais->ResetAttrs();
$bens_patrimoniais_list->RenderRow();
?>
<?php if ($bens_patrimoniais_list->TotalRecs > 0 && ($bens_patrimoniais->CurrentAction <> "gridadd" && $bens_patrimoniais->CurrentAction <> "gridedit")) { ?>
<tfoot><!-- Table footer -->
	<tr class="ewTableFooter">
<?php

// Render list options
$bens_patrimoniais_list->RenderListOptions();

// Render list options (footer, left)
$bens_patrimoniais_list->ListOptions->Render("footer", "left");
?>
	<?php if ($bens_patrimoniais->Localidade->Visible) { // Localidade ?>
		<td data-name="Localidade"><span id="elf_bens_patrimoniais_Localidade" class="bens_patrimoniais_Localidade">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($bens_patrimoniais->Descricao->Visible) { // Descricao ?>
		<td data-name="Descricao"><span id="elf_bens_patrimoniais_Descricao" class="bens_patrimoniais_Descricao">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($bens_patrimoniais->DataAquisao->Visible) { // DataAquisao ?>
		<td data-name="DataAquisao"><span id="elf_bens_patrimoniais_DataAquisao" class="bens_patrimoniais_DataAquisao">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($bens_patrimoniais->Tipo->Visible) { // Tipo ?>
		<td data-name="Tipo"><span id="elf_bens_patrimoniais_Tipo" class="bens_patrimoniais_Tipo">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($bens_patrimoniais->Estado_do_bem->Visible) { // Estado_do_bem ?>
		<td data-name="Estado_do_bem"><span id="elf_bens_patrimoniais_Estado_do_bem" class="bens_patrimoniais_Estado_do_bem">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($bens_patrimoniais->Valor_estimado->Visible) { // Valor_estimado ?>
		<td data-name="Valor_estimado"><span id="elf_bens_patrimoniais_Valor_estimado" class="bens_patrimoniais_Valor_estimado">
<span class="ewAggregate"><?php echo $Language->Phrase("TOTAL") ?></span>
<?php echo $bens_patrimoniais->Valor_estimado->ViewValue ?>
		</span></td>
	<?php } ?>
	<?php if ($bens_patrimoniais->Situacao->Visible) { // Situacao ?>
		<td data-name="Situacao"><span id="elf_bens_patrimoniais_Situacao" class="bens_patrimoniais_Situacao">
		&nbsp;
		</span></td>
	<?php } ?>
<?php

// Render list options (footer, right)
$bens_patrimoniais_list->ListOptions->Render("footer", "right");
?>
	</tr>
</tfoot>	
<?php } ?>
</table>
<?php } ?>
<?php if ($bens_patrimoniais->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($bens_patrimoniais_list->Recordset)
	$bens_patrimoniais_list->Recordset->Close();
?>
</div>
<?php } ?>
<?php if ($bens_patrimoniais_list->TotalRecs == 0 && $bens_patrimoniais->CurrentAction == "") { // Show other options ?>
<div class="ewListOtherOptions">
<?php
	foreach ($bens_patrimoniais_list->OtherOptions as &$option) {
		$option->ButtonClass = "";
		$option->Render("body", "");
	}
?>
</div>
<div class="clearfix"></div>
<?php } ?>
<?php if ($bens_patrimoniais->Export == "") { ?>
<script type="text/javascript">
fbens_patrimoniaislistsrch.Init();
fbens_patrimoniaislist.Init();
$(document).ready(function($) {	$("#ajuda").click(function() {	bootbox.dialog({title: "Informações de Ajuda", message: '<?php echo str_replace("\r\n"," ",trim($help)) ?>', buttons: { success: { label: "Fechar" }}}); });});
</script>
<?php } ?>
<?php
$bens_patrimoniais_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($bens_patrimoniais->Export == "") { ?>
<script type="text/javascript">
$(document).ready(function($) {
	$("#elf_bens_patrimoniais_Valor_estimado").addClass('badge bg-cobalt');
});
</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$bens_patrimoniais_list->Page_Terminate();
?>
