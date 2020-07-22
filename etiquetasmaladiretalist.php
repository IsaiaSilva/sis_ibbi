<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg11.php" ?>
<?php include_once "ewmysql11.php" ?>
<?php include_once "phpfn11.php" ?>
<?php include_once "etiquetasmaladiretainfo.php" ?>
<?php include_once "usuariosinfo.php" ?>
<?php include_once "userfn11.php" ?>
<?php

//
// Page class
//

$EtiquetasMalaDireta_list = NULL; // Initialize page object first

class cEtiquetasMalaDireta_list extends cEtiquetasMalaDireta {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{2B7992FC-5911-46A7-9310-01F4D4225C49}";

	// Table name
	var $TableName = 'EtiquetasMalaDireta';

	// Page object name
	var $PageObjName = 'EtiquetasMalaDireta_list';

	// Grid form hidden field names
	var $FormName = 'fEtiquetasMalaDiretalist';
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
	var $ExportExcelCustom = TRUE;
	var $ExportWordCustom = TRUE;
	var $ExportPdfCustom = TRUE;
	var $ExportEmailCustom = TRUE;

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

		// Table object (EtiquetasMalaDireta)
		if (!isset($GLOBALS["EtiquetasMalaDireta"]) || get_class($GLOBALS["EtiquetasMalaDireta"]) == "cEtiquetasMalaDireta") {
			$GLOBALS["EtiquetasMalaDireta"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["EtiquetasMalaDireta"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "etiquetasmaladiretaadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "etiquetasmaladiretadelete.php";
		$this->MultiUpdateUrl = "etiquetasmaladiretaupdate.php";

		// Table object (usuarios)
		if (!isset($GLOBALS['usuarios'])) $GLOBALS['usuarios'] = new cusuarios();

		// User table object (usuarios)
		if (!isset($GLOBALS["UserTable"])) $GLOBALS["UserTable"] = new cusuarios();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'EtiquetasMalaDireta', TRUE);

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

		// Custom export (post back from ew_ApplyTemplate), export and terminate page
		if (@$_POST["customexport"] <> "") {
			$this->CustomExport = $_POST["customexport"];
			$this->Export = $this->CustomExport;
			$this->Page_Terminate();
			exit();
		}

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
		if (@$_POST["customexport"] == "") {

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();
		}

		// Export
		global $EW_EXPORT, $EtiquetasMalaDireta;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
			if (is_array(@$_SESSION[EW_SESSION_TEMP_IMAGES])) // Restore temp images
				$gTmpImages = @$_SESSION[EW_SESSION_TEMP_IMAGES];
			if (@$_POST["data"] <> "")
				$sContent = $_POST["data"];
			$gsExportFile = @$_POST["filename"];
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($EtiquetasMalaDireta);
				$doc->Text = $sContent;
				if ($this->Export == "email")
					echo $this->ExportEmail($doc->Text);
				else
					$doc->Export();
				ew_DeleteTmpImages(); // Delete temp images
				exit();
			}
		}
	if ($this->CustomExport <> "") { // Save temp images array for custom export
		if (is_array($gTmpImages))
			$_SESSION[EW_SESSION_TEMP_IMAGES] = $gTmpImages;
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
	var $DisplayRecs = 20;
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
			$this->AllowAddDeleteRow = FALSE; // Do not allow add/delete row

		// Search filters
		$sSrchAdvanced = ""; // Advanced search filter
		$sSrchBasic = ""; // Basic search filter
		$sFilter = "";

		// Get command
		$this->Command = strtolower(@$_GET["cmd"]);
		if ($this->IsPageRequest()) { // Validate request

			// Process custom action first
			$this->ProcessCustomAction();

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
			ew_AddFilter($this->DefaultSearchWhere, $this->AdvancedSearchWhere(TRUE));

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

			// Get search criteria for advanced search
			if ($gsSearchError == "")
				$sSrchAdvanced = $this->AdvancedSearchWhere();
		}

		// Restore display records
		if ($this->getRecordsPerPage() <> "") {
			$this->DisplayRecs = $this->getRecordsPerPage(); // Restore from Session
		} else {
			$this->DisplayRecs = 20; // Load default
		}

		// Load Sorting Order
		$this->LoadSortOrder();

		// Load search default if no existing search criteria
		if (!$this->CheckSearchParms()) {

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
		if (count($arrKeyFlds) >= 0) {
		}
		return TRUE;
	}

	// Advanced search WHERE clause based on QueryString
	function AdvancedSearchWhere($Default = FALSE) {
		global $Security;
		$sWhere = "";
		if (!$Security->CanSearch()) return "";
		$this->BuildSearchSql($sWhere, $this->Nome, $Default, FALSE); // Nome
		$this->BuildSearchSql($sWhere, $this->CargoMinisterial, $Default, FALSE); // CargoMinisterial
		$this->BuildSearchSql($sWhere, $this->Funcao, $Default, FALSE); // Funcao
		$this->BuildSearchSql($sWhere, $this->Mes, $Default, FALSE); // Mes
		$this->BuildSearchSql($sWhere, $this->Endereco, $Default, FALSE); // Endereco
		$this->BuildSearchSql($sWhere, $this->Bairro, $Default, FALSE); // Bairro
		$this->BuildSearchSql($sWhere, $this->Cidade, $Default, FALSE); // Cidade
		$this->BuildSearchSql($sWhere, $this->UF, $Default, FALSE); // UF
		$this->BuildSearchSql($sWhere, $this->Sexo, $Default, FALSE); // Sexo
		$this->BuildSearchSql($sWhere, $this->EstadoCivil, $Default, FALSE); // EstadoCivil
		$this->BuildSearchSql($sWhere, $this->Rede_Ministerial, $Default, FALSE); // Rede_Ministerial
		$this->BuildSearchSql($sWhere, $this->Celula, $Default, FALSE); // Celula

		// Set up search parm
		if (!$Default && $sWhere <> "") {
			$this->Command = "search";
		}
		if (!$Default && $this->Command == "search") {
			$this->Nome->AdvancedSearch->Save(); // Nome
			$this->CargoMinisterial->AdvancedSearch->Save(); // CargoMinisterial
			$this->Funcao->AdvancedSearch->Save(); // Funcao
			$this->Mes->AdvancedSearch->Save(); // Mes
			$this->Endereco->AdvancedSearch->Save(); // Endereco
			$this->Bairro->AdvancedSearch->Save(); // Bairro
			$this->Cidade->AdvancedSearch->Save(); // Cidade
			$this->UF->AdvancedSearch->Save(); // UF
			$this->Sexo->AdvancedSearch->Save(); // Sexo
			$this->EstadoCivil->AdvancedSearch->Save(); // EstadoCivil
			$this->Rede_Ministerial->AdvancedSearch->Save(); // Rede_Ministerial
			$this->Celula->AdvancedSearch->Save(); // Celula
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

	// Check if search parm exists
	function CheckSearchParms() {
		if ($this->Nome->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->CargoMinisterial->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Funcao->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Mes->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Endereco->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Bairro->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Cidade->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->UF->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Sexo->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->EstadoCivil->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Rede_Ministerial->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Celula->AdvancedSearch->IssetSession())
			return TRUE;
		return FALSE;
	}

	// Clear all search parameters
	function ResetSearchParms() {

		// Clear search WHERE clause
		$this->SearchWhere = "";
		$this->setSearchWhere($this->SearchWhere);

		// Clear advanced search parameters
		$this->ResetAdvancedSearchParms();
	}

	// Load advanced search default values
	function LoadAdvancedSearchDefault() {
		return FALSE;
	}

	// Clear all advanced search parameters
	function ResetAdvancedSearchParms() {
		$this->Nome->AdvancedSearch->UnsetSession();
		$this->CargoMinisterial->AdvancedSearch->UnsetSession();
		$this->Funcao->AdvancedSearch->UnsetSession();
		$this->Mes->AdvancedSearch->UnsetSession();
		$this->Endereco->AdvancedSearch->UnsetSession();
		$this->Bairro->AdvancedSearch->UnsetSession();
		$this->Cidade->AdvancedSearch->UnsetSession();
		$this->UF->AdvancedSearch->UnsetSession();
		$this->Sexo->AdvancedSearch->UnsetSession();
		$this->EstadoCivil->AdvancedSearch->UnsetSession();
		$this->Rede_Ministerial->AdvancedSearch->UnsetSession();
		$this->Celula->AdvancedSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore advanced search values
		$this->Nome->AdvancedSearch->Load();
		$this->CargoMinisterial->AdvancedSearch->Load();
		$this->Funcao->AdvancedSearch->Load();
		$this->Mes->AdvancedSearch->Load();
		$this->Endereco->AdvancedSearch->Load();
		$this->Bairro->AdvancedSearch->Load();
		$this->Cidade->AdvancedSearch->Load();
		$this->UF->AdvancedSearch->Load();
		$this->Sexo->AdvancedSearch->Load();
		$this->EstadoCivil->AdvancedSearch->Load();
		$this->Rede_Ministerial->AdvancedSearch->Load();
		$this->Celula->AdvancedSearch->Load();
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for Ctrl pressed
		$bCtrl = (@$_GET["ctrl"] <> "");

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->Nome, $bCtrl); // Nome
			$this->UpdateSort($this->CargoMinisterial, $bCtrl); // CargoMinisterial
			$this->UpdateSort($this->Funcao, $bCtrl); // Funcao
			$this->UpdateSort($this->Mes, $bCtrl); // Mes
			$this->UpdateSort($this->Endereco, $bCtrl); // Endereco
			$this->UpdateSort($this->Bairro, $bCtrl); // Bairro
			$this->UpdateSort($this->Cidade, $bCtrl); // Cidade
			$this->UpdateSort($this->CEP, $bCtrl); // CEP
			$this->UpdateSort($this->UF, $bCtrl); // UF
			$this->UpdateSort($this->Sexo, $bCtrl); // Sexo
			$this->UpdateSort($this->EstadoCivil, $bCtrl); // EstadoCivil
			$this->UpdateSort($this->Rede_Ministerial, $bCtrl); // Rede_Ministerial
			$this->UpdateSort($this->Celula, $bCtrl); // Celula
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
				$this->Nome->setSort("");
				$this->CargoMinisterial->setSort("");
				$this->Funcao->setSort("");
				$this->Mes->setSort("");
				$this->Endereco->setSort("");
				$this->Bairro->setSort("");
				$this->Cidade->setSort("");
				$this->CEP->setSort("");
				$this->UF->setSort("");
				$this->Sexo->setSort("");
				$this->EstadoCivil->setSort("");
				$this->Rede_Ministerial->setSort("");
				$this->Celula->setSort("");
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

		// "checkbox"
		$oListOpt = &$this->ListOptions->Items["checkbox"];
		$this->RenderListOptionsExt();

		// Call ListOptions_Rendered event
		$this->ListOptions_Rendered();
	}

	// Set up other options
	function SetupOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
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
				$item->Body = "<a class=\"ewAction ewCustomAction\" href=\"\" onclick=\"ew_SubmitSelected(document.fEtiquetasMalaDiretalist, '" . ew_CurrentUrl() . "', null, '" . $action . "');return false;\">" . $name . "</a>";
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
		$item->Body = "<button type=\"button\" class=\"btn btn-warning ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $Language->Phrase("SearchPanel") . "\" data-caption=\"" . $Language->Phrase("SearchPanel") . "\" data-toggle=\"button\" data-form=\"fEtiquetasMalaDiretalistsrch\">" . $Language->Phrase("SearchBtn") . "</button>";
		$item->Visible = TRUE;

		// Show all button
		$item = &$this->SearchOptions->Add("showall");
		$item->Body = "<a class=\"btn btn-default ewShowAll\" title=\"" . $Language->Phrase("ShowAll") . "\" data-caption=\"" . $Language->Phrase("ShowAll") . "\" href=\"" . $this->PageUrl() . "cmd=reset\">" . $Language->Phrase("ShowAllBtn") . "</a>";
		$item->Visible = ($this->SearchWhere <> $this->DefaultSearchWhere && $this->SearchWhere <> "0=101");

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

	//  Load search values for validation
	function LoadSearchValues() {
		global $objForm;

		// Load search values
		// Nome

		$this->Nome->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Nome"]);
		if ($this->Nome->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Nome->AdvancedSearch->SearchOperator = @$_GET["z_Nome"];

		// CargoMinisterial
		$this->CargoMinisterial->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_CargoMinisterial"]);
		if ($this->CargoMinisterial->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->CargoMinisterial->AdvancedSearch->SearchOperator = @$_GET["z_CargoMinisterial"];

		// Funcao
		$this->Funcao->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Funcao"]);
		if ($this->Funcao->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Funcao->AdvancedSearch->SearchOperator = @$_GET["z_Funcao"];

		// Mes
		$this->Mes->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Mes"]);
		if ($this->Mes->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Mes->AdvancedSearch->SearchOperator = @$_GET["z_Mes"];

		// Endereco
		$this->Endereco->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Endereco"]);
		if ($this->Endereco->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Endereco->AdvancedSearch->SearchOperator = @$_GET["z_Endereco"];

		// Bairro
		$this->Bairro->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Bairro"]);
		if ($this->Bairro->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Bairro->AdvancedSearch->SearchOperator = @$_GET["z_Bairro"];

		// Cidade
		$this->Cidade->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Cidade"]);
		if ($this->Cidade->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Cidade->AdvancedSearch->SearchOperator = @$_GET["z_Cidade"];

		// UF
		$this->UF->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_UF"]);
		if ($this->UF->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->UF->AdvancedSearch->SearchOperator = @$_GET["z_UF"];

		// Sexo
		$this->Sexo->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Sexo"]);
		if ($this->Sexo->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Sexo->AdvancedSearch->SearchOperator = @$_GET["z_Sexo"];

		// EstadoCivil
		$this->EstadoCivil->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_EstadoCivil"]);
		if ($this->EstadoCivil->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->EstadoCivil->AdvancedSearch->SearchOperator = @$_GET["z_EstadoCivil"];

		// Rede_Ministerial
		$this->Rede_Ministerial->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Rede_Ministerial"]);
		if ($this->Rede_Ministerial->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Rede_Ministerial->AdvancedSearch->SearchOperator = @$_GET["z_Rede_Ministerial"];

		// Celula
		$this->Celula->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Celula"]);
		if ($this->Celula->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Celula->AdvancedSearch->SearchOperator = @$_GET["z_Celula"];
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
		$this->Nome->setDbValue($rs->fields('Nome'));
		$this->CargoMinisterial->setDbValue($rs->fields('CargoMinisterial'));
		$this->Funcao->setDbValue($rs->fields('Funcao'));
		$this->Mes->setDbValue($rs->fields('Mes'));
		$this->Endereco->setDbValue($rs->fields('Endereco'));
		$this->Bairro->setDbValue($rs->fields('Bairro'));
		$this->Cidade->setDbValue($rs->fields('Cidade'));
		$this->CEP->setDbValue($rs->fields('CEP'));
		$this->UF->setDbValue($rs->fields('UF'));
		$this->Sexo->setDbValue($rs->fields('Sexo'));
		$this->EstadoCivil->setDbValue($rs->fields('EstadoCivil'));
		$this->Rede_Ministerial->setDbValue($rs->fields('Rede_Ministerial'));
		$this->Celula->setDbValue($rs->fields('Celula'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->Nome->DbValue = $row['Nome'];
		$this->CargoMinisterial->DbValue = $row['CargoMinisterial'];
		$this->Funcao->DbValue = $row['Funcao'];
		$this->Mes->DbValue = $row['Mes'];
		$this->Endereco->DbValue = $row['Endereco'];
		$this->Bairro->DbValue = $row['Bairro'];
		$this->Cidade->DbValue = $row['Cidade'];
		$this->CEP->DbValue = $row['CEP'];
		$this->UF->DbValue = $row['UF'];
		$this->Sexo->DbValue = $row['Sexo'];
		$this->EstadoCivil->DbValue = $row['EstadoCivil'];
		$this->Rede_Ministerial->DbValue = $row['Rede_Ministerial'];
		$this->Celula->DbValue = $row['Celula'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;

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

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
		// Nome
		// CargoMinisterial
		// Funcao
		// Mes
		// Endereco
		// Bairro
		// Cidade
		// CEP
		// UF
		// Sexo
		// EstadoCivil
		// Rede_Ministerial
		// Celula

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// Nome
			$this->Nome->ViewValue = $this->Nome->CurrentValue;
			$this->Nome->ViewCustomAttributes = "";

			// CargoMinisterial
			if (strval($this->CargoMinisterial->CurrentValue) <> "") {
				$sFilterWrk = "`id_cgm`" . ew_SearchString("=", $this->CargoMinisterial->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `id_cgm`, `Cargo_Ministerial` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `cargosministeriais`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->CargoMinisterial, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `Cargo_Ministerial` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->CargoMinisterial->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->CargoMinisterial->ViewValue = $this->CargoMinisterial->CurrentValue;
				}
			} else {
				$this->CargoMinisterial->ViewValue = NULL;
			}
			$this->CargoMinisterial->ViewCustomAttributes = "";

			// Funcao
			if (strval($this->Funcao->CurrentValue) <> "") {
				$sFilterWrk = "`Id`" . ew_SearchString("=", $this->Funcao->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `Id`, `Funcao` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `funcoes_exerce`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Funcao, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `Funcao` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->Funcao->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->Funcao->ViewValue = $this->Funcao->CurrentValue;
				}
			} else {
				$this->Funcao->ViewValue = NULL;
			}
			$this->Funcao->ViewCustomAttributes = "";

			// Mes
			if (strval($this->Mes->CurrentValue) <> "") {
				$sFilterWrk = "`id`" . ew_SearchString("=", $this->Mes->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `id`, `Mes` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `meses`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Mes, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->Mes->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->Mes->ViewValue = $this->Mes->CurrentValue;
				}
			} else {
				$this->Mes->ViewValue = NULL;
			}
			$this->Mes->ViewCustomAttributes = "";

			// Endereco
			$this->Endereco->ViewValue = $this->Endereco->CurrentValue;
			$this->Endereco->ViewCustomAttributes = "";

			// Bairro
			$this->Bairro->ViewValue = $this->Bairro->CurrentValue;
			$this->Bairro->ViewCustomAttributes = "";

			// Cidade
			$this->Cidade->ViewValue = $this->Cidade->CurrentValue;
			$this->Cidade->ViewCustomAttributes = "";

			// CEP
			$this->CEP->ViewValue = $this->CEP->CurrentValue;
			$this->CEP->ViewCustomAttributes = "";

			// UF
			$this->UF->ViewValue = $this->UF->CurrentValue;
			$this->UF->ViewCustomAttributes = "";

			// Sexo
			if (strval($this->Sexo->CurrentValue) <> "") {
				switch ($this->Sexo->CurrentValue) {
					case $this->Sexo->FldTagValue(1):
						$this->Sexo->ViewValue = $this->Sexo->FldTagCaption(1) <> "" ? $this->Sexo->FldTagCaption(1) : $this->Sexo->CurrentValue;
						break;
					case $this->Sexo->FldTagValue(2):
						$this->Sexo->ViewValue = $this->Sexo->FldTagCaption(2) <> "" ? $this->Sexo->FldTagCaption(2) : $this->Sexo->CurrentValue;
						break;
					default:
						$this->Sexo->ViewValue = $this->Sexo->CurrentValue;
				}
			} else {
				$this->Sexo->ViewValue = NULL;
			}
			$this->Sexo->ViewCustomAttributes = "";

			// EstadoCivil
			if (strval($this->EstadoCivil->CurrentValue) <> "") {
				switch ($this->EstadoCivil->CurrentValue) {
					case $this->EstadoCivil->FldTagValue(1):
						$this->EstadoCivil->ViewValue = $this->EstadoCivil->FldTagCaption(1) <> "" ? $this->EstadoCivil->FldTagCaption(1) : $this->EstadoCivil->CurrentValue;
						break;
					case $this->EstadoCivil->FldTagValue(2):
						$this->EstadoCivil->ViewValue = $this->EstadoCivil->FldTagCaption(2) <> "" ? $this->EstadoCivil->FldTagCaption(2) : $this->EstadoCivil->CurrentValue;
						break;
					case $this->EstadoCivil->FldTagValue(3):
						$this->EstadoCivil->ViewValue = $this->EstadoCivil->FldTagCaption(3) <> "" ? $this->EstadoCivil->FldTagCaption(3) : $this->EstadoCivil->CurrentValue;
						break;
					case $this->EstadoCivil->FldTagValue(4):
						$this->EstadoCivil->ViewValue = $this->EstadoCivil->FldTagCaption(4) <> "" ? $this->EstadoCivil->FldTagCaption(4) : $this->EstadoCivil->CurrentValue;
						break;
					case $this->EstadoCivil->FldTagValue(5):
						$this->EstadoCivil->ViewValue = $this->EstadoCivil->FldTagCaption(5) <> "" ? $this->EstadoCivil->FldTagCaption(5) : $this->EstadoCivil->CurrentValue;
						break;
					default:
						$this->EstadoCivil->ViewValue = $this->EstadoCivil->CurrentValue;
				}
			} else {
				$this->EstadoCivil->ViewValue = NULL;
			}
			$this->EstadoCivil->ViewCustomAttributes = "";

			// Rede_Ministerial
			$this->Rede_Ministerial->ViewValue = $this->Rede_Ministerial->CurrentValue;
			$this->Rede_Ministerial->ViewCustomAttributes = "";

			// Celula
			$this->Celula->ViewValue = $this->Celula->CurrentValue;
			$this->Celula->ViewCustomAttributes = "";

			// Nome
			$this->Nome->LinkCustomAttributes = "";
			$this->Nome->HrefValue = "";
			$this->Nome->TooltipValue = "";

			// CargoMinisterial
			$this->CargoMinisterial->LinkCustomAttributes = "";
			$this->CargoMinisterial->HrefValue = "";
			$this->CargoMinisterial->TooltipValue = "";

			// Funcao
			$this->Funcao->LinkCustomAttributes = "";
			$this->Funcao->HrefValue = "";
			$this->Funcao->TooltipValue = "";

			// Mes
			$this->Mes->LinkCustomAttributes = "";
			$this->Mes->HrefValue = "";
			$this->Mes->TooltipValue = "";

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

			// CEP
			$this->CEP->LinkCustomAttributes = "";
			$this->CEP->HrefValue = "";
			$this->CEP->TooltipValue = "";

			// UF
			$this->UF->LinkCustomAttributes = "";
			$this->UF->HrefValue = "";
			$this->UF->TooltipValue = "";

			// Sexo
			$this->Sexo->LinkCustomAttributes = "";
			$this->Sexo->HrefValue = "";
			$this->Sexo->TooltipValue = "";

			// EstadoCivil
			$this->EstadoCivil->LinkCustomAttributes = "";
			$this->EstadoCivil->HrefValue = "";
			$this->EstadoCivil->TooltipValue = "";

			// Rede_Ministerial
			$this->Rede_Ministerial->LinkCustomAttributes = "";
			$this->Rede_Ministerial->HrefValue = "";
			$this->Rede_Ministerial->TooltipValue = "";

			// Celula
			$this->Celula->LinkCustomAttributes = "";
			$this->Celula->HrefValue = "";
			$this->Celula->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// Nome
			$this->Nome->EditAttrs["class"] = "form-control";
			$this->Nome->EditCustomAttributes = "";
			$this->Nome->EditValue = ew_HtmlEncode($this->Nome->AdvancedSearch->SearchValue);

			// CargoMinisterial
			$this->CargoMinisterial->EditAttrs["class"] = "form-control";
			$this->CargoMinisterial->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `id_cgm`, `Cargo_Ministerial` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `cargosministeriais`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->CargoMinisterial, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `Cargo_Ministerial` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->CargoMinisterial->EditValue = $arwrk;

			// Funcao
			$this->Funcao->EditAttrs["class"] = "form-control";
			$this->Funcao->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `Id`, `Funcao` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `funcoes_exerce`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Funcao, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `Funcao` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->Funcao->EditValue = $arwrk;

			// Mes
			$this->Mes->EditAttrs["class"] = "form-control";
			$this->Mes->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `id`, `Mes` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `meses`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Mes, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->Mes->EditValue = $arwrk;

			// Endereco
			$this->Endereco->EditAttrs["class"] = "form-control";
			$this->Endereco->EditCustomAttributes = "";
			$this->Endereco->EditValue = ew_HtmlEncode($this->Endereco->AdvancedSearch->SearchValue);

			// Bairro
			$this->Bairro->EditAttrs["class"] = "form-control";
			$this->Bairro->EditCustomAttributes = "";
			$this->Bairro->EditValue = ew_HtmlEncode($this->Bairro->AdvancedSearch->SearchValue);

			// Cidade
			$this->Cidade->EditAttrs["class"] = "form-control";
			$this->Cidade->EditCustomAttributes = "";
			$this->Cidade->EditValue = ew_HtmlEncode($this->Cidade->AdvancedSearch->SearchValue);

			// CEP
			$this->CEP->EditAttrs["class"] = "form-control";
			$this->CEP->EditCustomAttributes = "";
			$this->CEP->EditValue = ew_HtmlEncode($this->CEP->AdvancedSearch->SearchValue);

			// UF
			$this->UF->EditAttrs["class"] = "form-control";
			$this->UF->EditCustomAttributes = "";
			$this->UF->EditValue = ew_HtmlEncode($this->UF->AdvancedSearch->SearchValue);

			// Sexo
			$this->Sexo->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->Sexo->FldTagValue(1), $this->Sexo->FldTagCaption(1) <> "" ? $this->Sexo->FldTagCaption(1) : $this->Sexo->FldTagValue(1));
			$arwrk[] = array($this->Sexo->FldTagValue(2), $this->Sexo->FldTagCaption(2) <> "" ? $this->Sexo->FldTagCaption(2) : $this->Sexo->FldTagValue(2));
			$this->Sexo->EditValue = $arwrk;

			// EstadoCivil
			$this->EstadoCivil->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->EstadoCivil->FldTagValue(1), $this->EstadoCivil->FldTagCaption(1) <> "" ? $this->EstadoCivil->FldTagCaption(1) : $this->EstadoCivil->FldTagValue(1));
			$arwrk[] = array($this->EstadoCivil->FldTagValue(2), $this->EstadoCivil->FldTagCaption(2) <> "" ? $this->EstadoCivil->FldTagCaption(2) : $this->EstadoCivil->FldTagValue(2));
			$arwrk[] = array($this->EstadoCivil->FldTagValue(3), $this->EstadoCivil->FldTagCaption(3) <> "" ? $this->EstadoCivil->FldTagCaption(3) : $this->EstadoCivil->FldTagValue(3));
			$arwrk[] = array($this->EstadoCivil->FldTagValue(4), $this->EstadoCivil->FldTagCaption(4) <> "" ? $this->EstadoCivil->FldTagCaption(4) : $this->EstadoCivil->FldTagValue(4));
			$arwrk[] = array($this->EstadoCivil->FldTagValue(5), $this->EstadoCivil->FldTagCaption(5) <> "" ? $this->EstadoCivil->FldTagCaption(5) : $this->EstadoCivil->FldTagValue(5));
			$this->EstadoCivil->EditValue = $arwrk;

			// Rede_Ministerial
			$this->Rede_Ministerial->EditAttrs["class"] = "form-control";
			$this->Rede_Ministerial->EditCustomAttributes = "";
			$this->Rede_Ministerial->EditValue = ew_HtmlEncode($this->Rede_Ministerial->AdvancedSearch->SearchValue);

			// Celula
			$this->Celula->EditAttrs["class"] = "form-control";
			$this->Celula->EditCustomAttributes = "";
			$this->Celula->EditValue = ew_HtmlEncode($this->Celula->AdvancedSearch->SearchValue);
		}
		if ($this->RowType == EW_ROWTYPE_ADD ||
			$this->RowType == EW_ROWTYPE_EDIT ||
			$this->RowType == EW_ROWTYPE_SEARCH) { // Add / Edit / Search row
			$this->SetupFieldTitles();
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
		if (!ew_CheckInteger($this->Rede_Ministerial->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->Rede_Ministerial->FldErrMsg());
		}
		if (!ew_CheckInteger($this->Celula->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->Celula->FldErrMsg());
		}

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
		$this->Nome->AdvancedSearch->Load();
		$this->CargoMinisterial->AdvancedSearch->Load();
		$this->Funcao->AdvancedSearch->Load();
		$this->Mes->AdvancedSearch->Load();
		$this->Endereco->AdvancedSearch->Load();
		$this->Bairro->AdvancedSearch->Load();
		$this->Cidade->AdvancedSearch->Load();
		$this->UF->AdvancedSearch->Load();
		$this->Sexo->AdvancedSearch->Load();
		$this->EstadoCivil->AdvancedSearch->Load();
		$this->Rede_Ministerial->AdvancedSearch->Load();
		$this->Celula->AdvancedSearch->Load();
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
		if ($this->ExportExcelCustom)
			$item->Body = "<a href=\"javascript:void(0);\" class=\"ewExportLink ewExcel\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToExcelText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToExcelText")) . "\" onclick=\"ew_Export(document.fEtiquetasMalaDiretalist,'" . $this->ExportExcelUrl . "','excel',true);\">" . $Language->Phrase("ExportToExcel") . "</a>";
		else
			$item->Body = "<a href=\"" . $this->ExportExcelUrl . "\" class=\"ewExportLink ewExcel\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToExcelText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToExcelText")) . "\">" . $Language->Phrase("ExportToExcel") . "</a>";
		$item->Visible = FALSE;

		// Export to Word
		$item = &$this->ExportOptions->Add("word");
		if ($this->ExportWordCustom)
			$item->Body = "<a href=\"javascript:void(0);\" class=\"ewExportLink ewWord\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToWordText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToWordText")) . "\" onclick=\"ew_Export(document.fEtiquetasMalaDiretalist,'" . $this->ExportWordUrl . "','word',true);\">" . $Language->Phrase("ExportToWord") . "</a>";
		else
			$item->Body = "<a href=\"" . $this->ExportWordUrl . "\" class=\"ewExportLink ewWord\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToWordText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToWordText")) . "\">" . $Language->Phrase("ExportToWord") . "</a>";
		$item->Visible = FALSE;

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
		if ($this->ExportPdfCustom)
			$item->Body = "<a href=\"javascript:void(0);\" class=\"ewExportLink ewPdf\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToPDFText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToPDFText")) . "\" onclick=\"ew_Export(document.fEtiquetasMalaDiretalist,'" . $this->ExportPdfUrl . "','pdf',true);\">" . $Language->Phrase("ExportToPDF") . "</a>";
		else
			$item->Body = "<a href=\"" . $this->ExportPdfUrl . "\" class=\"ewExportLink ewPdf\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToPDFText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToPDFText")) . "\">" . $Language->Phrase("ExportToPDF") . "</a>";
		$item->Visible = FALSE;

		// Export to Email
		$item = &$this->ExportOptions->Add("email");
		$url = $this->ExportEmailCustom ? ",url:'" . $this->PageUrl() . "export=email&amp;custom=1'" : "";
		$item->Body = "<button id=\"emf_EtiquetasMalaDireta\" class=\"ewExportLink ewEmail\" title=\"" . $Language->Phrase("ExportToEmailText") . "\" data-caption=\"" . $Language->Phrase("ExportToEmailText") . "\" onclick=\"ew_EmailDialogShow({lnk:'emf_EtiquetasMalaDireta',hdr:ewLanguage.Phrase('ExportToEmailText'),f:document.fEtiquetasMalaDiretalist,sel:false" . $url . "});\">" . $Language->Phrase("ExportToEmail") . "</button>";
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
		echo utf8_encode((empty($_GET["export"])) ? "<div class='ewMessageDialog'><div class='alert alert-info'><i class='icon-print'></i> Na tela de impress�o pressione CTRL + P </div></div>" : "");
		echo (empty($_GET["export"])) ? "" : "<script>$(document).ready(function($) { $('.ewGrid').removeClass();})</script>";
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
if (!isset($EtiquetasMalaDireta_list)) $EtiquetasMalaDireta_list = new cEtiquetasMalaDireta_list();

// Page init
$EtiquetasMalaDireta_list->Page_Init();

// Page main
$EtiquetasMalaDireta_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$EtiquetasMalaDireta_list->Page_Render();
?>
<?php include_once "header.php" ?>
<?php if ($EtiquetasMalaDireta->Export == "") { ?>
<script type="text/javascript">

// Page object
var EtiquetasMalaDireta_list = new ew_Page("EtiquetasMalaDireta_list");
EtiquetasMalaDireta_list.PageID = "list"; // Page ID
var EW_PAGE_ID = EtiquetasMalaDireta_list.PageID; // For backward compatibility

// Form object
var fEtiquetasMalaDiretalist = new ew_Form("fEtiquetasMalaDiretalist");
fEtiquetasMalaDiretalist.FormKeyCountName = '<?php echo $EtiquetasMalaDireta_list->FormKeyCountName ?>';

// Form_CustomValidate event
fEtiquetasMalaDiretalist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fEtiquetasMalaDiretalist.ValidateRequired = true;
<?php } else { ?>
fEtiquetasMalaDiretalist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fEtiquetasMalaDiretalist.Lists["x_CargoMinisterial"] = {"LinkField":"x_id_cgm","Ajax":null,"AutoFill":false,"DisplayFields":["x_Cargo_Ministerial","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fEtiquetasMalaDiretalist.Lists["x_Funcao"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Funcao","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fEtiquetasMalaDiretalist.Lists["x_Mes"] = {"LinkField":"x_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Mes","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
var fEtiquetasMalaDiretalistsrch = new ew_Form("fEtiquetasMalaDiretalistsrch");

// Validate function for search
fEtiquetasMalaDiretalistsrch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	this.PostAutoSuggest();
	var infix = "";
	elm = this.GetElements("x" + infix + "_Rede_Ministerial");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($EtiquetasMalaDireta->Rede_Ministerial->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_Celula");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($EtiquetasMalaDireta->Celula->FldErrMsg()) ?>");

	// Set up row object
	ew_ElementsToRow(fobj);

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}

// Form_CustomValidate event
fEtiquetasMalaDiretalistsrch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fEtiquetasMalaDiretalistsrch.ValidateRequired = true; // Use JavaScript validation
<?php } else { ?>
fEtiquetasMalaDiretalistsrch.ValidateRequired = false; // No JavaScript validation
<?php } ?>

// Dynamic selection lists
fEtiquetasMalaDiretalistsrch.Lists["x_CargoMinisterial"] = {"LinkField":"x_id_cgm","Ajax":null,"AutoFill":false,"DisplayFields":["x_Cargo_Ministerial","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fEtiquetasMalaDiretalistsrch.Lists["x_Funcao"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Funcao","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fEtiquetasMalaDiretalistsrch.Lists["x_Mes"] = {"LinkField":"x_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Mes","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Init search panel as collapsed
if (fEtiquetasMalaDiretalistsrch) fEtiquetasMalaDiretalistsrch.InitSearchPanel = true;
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php if ($EtiquetasMalaDireta->Export == "") { ?>
<div class="ewToolbar">
<?php if ($EtiquetasMalaDireta->Export == "") { ?>
<?php $Breadcrumb->Render(); ?>
<?php } ?>
<?php if ($EtiquetasMalaDireta_list->TotalRecs > 0 && $EtiquetasMalaDireta_list->ExportOptions->Visible()) { ?>
<?php $EtiquetasMalaDireta_list->ExportOptions->Render("body") ?>
<?php } ?>
<?php if ($EtiquetasMalaDireta_list->SearchOptions->Visible()) { ?>
<?php $EtiquetasMalaDireta_list->SearchOptions->Render("body") ?>
<?php } ?>
<?php if ($EtiquetasMalaDireta->Export == "") { ?>
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
		if ($EtiquetasMalaDireta_list->TotalRecs <= 0)
			$EtiquetasMalaDireta_list->TotalRecs = $EtiquetasMalaDireta->SelectRecordCount();
	} else {
		if (!$EtiquetasMalaDireta_list->Recordset && ($EtiquetasMalaDireta_list->Recordset = $EtiquetasMalaDireta_list->LoadRecordset()))
			$EtiquetasMalaDireta_list->TotalRecs = $EtiquetasMalaDireta_list->Recordset->RecordCount();
	}
	$EtiquetasMalaDireta_list->StartRec = 1;
	if ($EtiquetasMalaDireta_list->DisplayRecs <= 0 || ($EtiquetasMalaDireta->Export <> "" && $EtiquetasMalaDireta->ExportAll)) // Display all records
		$EtiquetasMalaDireta_list->DisplayRecs = $EtiquetasMalaDireta_list->TotalRecs;
	if (!($EtiquetasMalaDireta->Export <> "" && $EtiquetasMalaDireta->ExportAll))
		$EtiquetasMalaDireta_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$EtiquetasMalaDireta_list->Recordset = $EtiquetasMalaDireta_list->LoadRecordset($EtiquetasMalaDireta_list->StartRec-1, $EtiquetasMalaDireta_list->DisplayRecs);

	// Set no record found message
	if ($EtiquetasMalaDireta->CurrentAction == "" && $EtiquetasMalaDireta_list->TotalRecs == 0) {
		if (!$Security->CanList())
			$EtiquetasMalaDireta_list->setWarningMessage($Language->Phrase("NoPermission"));
		if ($EtiquetasMalaDireta_list->SearchWhere == "0=101")
			$EtiquetasMalaDireta_list->setWarningMessage($Language->Phrase("EnterSearchCriteria"));
		else
			$EtiquetasMalaDireta_list->setWarningMessage($Language->Phrase("NoRecord"));
	}
$EtiquetasMalaDireta_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($EtiquetasMalaDireta->Export == "" && $EtiquetasMalaDireta->CurrentAction == "") { ?>
<form name="fEtiquetasMalaDiretalistsrch" id="fEtiquetasMalaDiretalistsrch" class="form-inline ewForm" action="<?php echo ew_CurrentPage() ?>">
<?php $SearchPanelClass = ($EtiquetasMalaDireta_list->SearchWhere <> "") ? " in" : ""; ?>
<div id="fEtiquetasMalaDiretalistsrch_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="EtiquetasMalaDireta">
	<div class="ewBasicSearch">
<?php
if ($gsSearchError == "")
	$EtiquetasMalaDireta_list->LoadAdvancedSearch(); // Load advanced search

// Render for search
$EtiquetasMalaDireta->RowType = EW_ROWTYPE_SEARCH;

// Render row
$EtiquetasMalaDireta->ResetAttrs();
$EtiquetasMalaDireta_list->RenderRow();
?>
<div id="xsr_1" class="ewRow">
<?php if ($EtiquetasMalaDireta->Nome->Visible) { // Nome ?>
	<div id="xsc_Nome" class="ewCell form-group">
		<label for="x_Nome" class="ewSearchCaption ewLabel"><?php echo $EtiquetasMalaDireta->Nome->FldCaption() ?></label>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Nome" id="z_Nome" value="LIKE"></span>
		<span class="ewSearchField">
<input type="text" data-field="x_Nome" name="x_Nome" id="x_Nome" size="45" maxlength="60" value="<?php echo $EtiquetasMalaDireta->Nome->EditValue ?>"<?php echo $EtiquetasMalaDireta->Nome->EditAttributes() ?>>
</span>
	</div>
<?php } ?>
<?php if ($EtiquetasMalaDireta->CargoMinisterial->Visible) { // CargoMinisterial ?>
	<div id="xsc_CargoMinisterial" class="ewCell form-group">
		<label for="x_CargoMinisterial" class="ewSearchCaption ewLabel"><?php echo $EtiquetasMalaDireta->CargoMinisterial->FldCaption() ?></label>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_CargoMinisterial" id="z_CargoMinisterial" value="="></span>
		<span class="ewSearchField">
<select data-field="x_CargoMinisterial" id="x_CargoMinisterial" name="x_CargoMinisterial"<?php echo $EtiquetasMalaDireta->CargoMinisterial->EditAttributes() ?>>
<?php
if (is_array($EtiquetasMalaDireta->CargoMinisterial->EditValue)) {
	$arwrk = $EtiquetasMalaDireta->CargoMinisterial->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($EtiquetasMalaDireta->CargoMinisterial->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
<script type="text/javascript">
fEtiquetasMalaDiretalistsrch.Lists["x_CargoMinisterial"].Options = <?php echo (is_array($EtiquetasMalaDireta->CargoMinisterial->EditValue)) ? ew_ArrayToJson($EtiquetasMalaDireta->CargoMinisterial->EditValue, 1) : "[]" ?>;
</script>
</span>
	</div>
<?php } ?>
</div>
<div id="xsr_2" class="ewRow">
<?php if ($EtiquetasMalaDireta->Funcao->Visible) { // Funcao ?>
	<div id="xsc_Funcao" class="ewCell form-group">
		<label for="x_Funcao" class="ewSearchCaption ewLabel"><?php echo $EtiquetasMalaDireta->Funcao->FldCaption() ?></label>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Funcao" id="z_Funcao" value="="></span>
		<span class="ewSearchField">
<select data-field="x_Funcao" id="x_Funcao" name="x_Funcao"<?php echo $EtiquetasMalaDireta->Funcao->EditAttributes() ?>>
<?php
if (is_array($EtiquetasMalaDireta->Funcao->EditValue)) {
	$arwrk = $EtiquetasMalaDireta->Funcao->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($EtiquetasMalaDireta->Funcao->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
<script type="text/javascript">
fEtiquetasMalaDiretalistsrch.Lists["x_Funcao"].Options = <?php echo (is_array($EtiquetasMalaDireta->Funcao->EditValue)) ? ew_ArrayToJson($EtiquetasMalaDireta->Funcao->EditValue, 1) : "[]" ?>;
</script>
</span>
	</div>
<?php } ?>
<?php if ($EtiquetasMalaDireta->Mes->Visible) { // Mes ?>
	<div id="xsc_Mes" class="ewCell form-group">
		<label for="x_Mes" class="ewSearchCaption ewLabel"><?php echo $EtiquetasMalaDireta->Mes->FldCaption() ?></label>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Mes" id="z_Mes" value="="></span>
		<span class="ewSearchField">
<select data-field="x_Mes" id="x_Mes" name="x_Mes"<?php echo $EtiquetasMalaDireta->Mes->EditAttributes() ?>>
<?php
if (is_array($EtiquetasMalaDireta->Mes->EditValue)) {
	$arwrk = $EtiquetasMalaDireta->Mes->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($EtiquetasMalaDireta->Mes->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
<script type="text/javascript">
fEtiquetasMalaDiretalistsrch.Lists["x_Mes"].Options = <?php echo (is_array($EtiquetasMalaDireta->Mes->EditValue)) ? ew_ArrayToJson($EtiquetasMalaDireta->Mes->EditValue, 1) : "[]" ?>;
</script>
</span>
	</div>
<?php } ?>
</div>
<div id="xsr_3" class="ewRow">
<?php if ($EtiquetasMalaDireta->UF->Visible) { // UF ?>
	<div id="xsc_UF" class="ewCell form-group">
		<label for="x_UF" class="ewSearchCaption ewLabel"><?php echo $EtiquetasMalaDireta->UF->FldCaption() ?></label>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_UF" id="z_UF" value="LIKE"></span>
		<span class="ewSearchField">
<input type="text" data-field="x_UF" name="x_UF" id="x_UF" size="5" maxlength="2" value="<?php echo $EtiquetasMalaDireta->UF->EditValue ?>"<?php echo $EtiquetasMalaDireta->UF->EditAttributes() ?>>
</span>
	</div>
<?php } ?>
<?php if ($EtiquetasMalaDireta->Sexo->Visible) { // Sexo ?>
	<div id="xsc_Sexo" class="ewCell form-group">
		<label class="ewSearchCaption ewLabel"><?php echo $EtiquetasMalaDireta->Sexo->FldCaption() ?></label>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Sexo" id="z_Sexo" value="="></span>
		<span class="ewSearchField">
<div id="tp_x_Sexo" class="<?php echo EW_ITEM_TEMPLATE_CLASSNAME ?>"><input type="radio" name="x_Sexo" id="x_Sexo" value="{value}"<?php echo $EtiquetasMalaDireta->Sexo->EditAttributes() ?>></div>
<div id="dsl_x_Sexo" data-repeatcolumn="5" class="ewItemList">
<?php
$arwrk = $EtiquetasMalaDireta->Sexo->EditValue;
if (is_array($arwrk)) {
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($EtiquetasMalaDireta->Sexo->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " checked=\"checked\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;

		// Note: No spacing within the LABEL tag
?>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 1) ?>
<label class="radio-inline"><input type="radio" data-field="x_Sexo" name="x_Sexo" id="x_Sexo_<?php echo $rowcntwrk ?>" value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?><?php echo $EtiquetasMalaDireta->Sexo->EditAttributes() ?>><?php echo $arwrk[$rowcntwrk][1] ?></label>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 2) ?>
<?php
	}
}
?>
</div>
</span>
	</div>
<?php } ?>
</div>
<div id="xsr_4" class="ewRow">
<?php if ($EtiquetasMalaDireta->EstadoCivil->Visible) { // EstadoCivil ?>
	<div id="xsc_EstadoCivil" class="ewCell form-group">
		<label class="ewSearchCaption ewLabel"><?php echo $EtiquetasMalaDireta->EstadoCivil->FldCaption() ?></label>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_EstadoCivil" id="z_EstadoCivil" value="="></span>
		<span class="ewSearchField">
<div id="tp_x_EstadoCivil" class="<?php echo EW_ITEM_TEMPLATE_CLASSNAME ?>"><input type="radio" name="x_EstadoCivil" id="x_EstadoCivil" value="{value}"<?php echo $EtiquetasMalaDireta->EstadoCivil->EditAttributes() ?>></div>
<div id="dsl_x_EstadoCivil" data-repeatcolumn="5" class="ewItemList">
<?php
$arwrk = $EtiquetasMalaDireta->EstadoCivil->EditValue;
if (is_array($arwrk)) {
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($EtiquetasMalaDireta->EstadoCivil->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " checked=\"checked\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;

		// Note: No spacing within the LABEL tag
?>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 1) ?>
<label class="radio-inline"><input type="radio" data-field="x_EstadoCivil" name="x_EstadoCivil" id="x_EstadoCivil_<?php echo $rowcntwrk ?>" value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?><?php echo $EtiquetasMalaDireta->EstadoCivil->EditAttributes() ?>><?php echo $arwrk[$rowcntwrk][1] ?></label>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 2) ?>
<?php
	}
}
?>
</div>
</span>
	</div>
<?php } ?>
<?php if ($EtiquetasMalaDireta->Rede_Ministerial->Visible) { // Rede_Ministerial ?>
	<div id="xsc_Rede_Ministerial" class="ewCell form-group">
		<label for="x_Rede_Ministerial" class="ewSearchCaption ewLabel"><?php echo $EtiquetasMalaDireta->Rede_Ministerial->FldCaption() ?></label>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Rede_Ministerial" id="z_Rede_Ministerial" value="="></span>
		<span class="ewSearchField">
<input type="text" data-field="x_Rede_Ministerial" name="x_Rede_Ministerial" id="x_Rede_Ministerial" size="30" value="<?php echo $EtiquetasMalaDireta->Rede_Ministerial->EditValue ?>"<?php echo $EtiquetasMalaDireta->Rede_Ministerial->EditAttributes() ?>>
</span>
	</div>
<?php } ?>
</div>
<div id="xsr_5" class="ewRow">
<?php if ($EtiquetasMalaDireta->Celula->Visible) { // Celula ?>
	<div id="xsc_Celula" class="ewCell form-group">
		<label for="x_Celula" class="ewSearchCaption ewLabel"><?php echo $EtiquetasMalaDireta->Celula->FldCaption() ?></label>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Celula" id="z_Celula" value="="></span>
		<span class="ewSearchField">
<input type="text" data-field="x_Celula" name="x_Celula" id="x_Celula" size="30" value="<?php echo $EtiquetasMalaDireta->Celula->EditValue ?>"<?php echo $EtiquetasMalaDireta->Celula->EditAttributes() ?>>
</span>
	</div>
<?php } ?>
</div>
<div id="xsr_6" class="ewRow">
	<button class="btn btn-primary ewButton" name="btnsubmit" id="btnsubmit" type="submit"><i class='glyphicon glyphicon-search'></i>&nbsp;<?php echo $Language->Phrase("QuickSearchBtn") ?></button>
</div>
	</div>
</div>
</form>
<?php } ?>
<?php } ?>
<?php $EtiquetasMalaDireta_list->ShowPageHeader(); ?>
<?php
$EtiquetasMalaDireta_list->ShowMessage();
?>
<?php if ($EtiquetasMalaDireta_list->TotalRecs > 0 || $EtiquetasMalaDireta->CurrentAction <> "") { ?>
<div class="ewGrid">
<?php if ($EtiquetasMalaDireta->Export == "") { ?>
<div class="ewGridUpperPanel">
<?php if ($EtiquetasMalaDireta->CurrentAction <> "gridadd" && $EtiquetasMalaDireta->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="form-inline ewForm ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($EtiquetasMalaDireta_list->Pager)) $EtiquetasMalaDireta_list->Pager = new cPrevNextPager($EtiquetasMalaDireta_list->StartRec, $EtiquetasMalaDireta_list->DisplayRecs, $EtiquetasMalaDireta_list->TotalRecs) ?>
<?php if ($EtiquetasMalaDireta_list->Pager->RecordCount > 0) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($EtiquetasMalaDireta_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $EtiquetasMalaDireta_list->PageUrl() ?>start=<?php echo $EtiquetasMalaDireta_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($EtiquetasMalaDireta_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $EtiquetasMalaDireta_list->PageUrl() ?>start=<?php echo $EtiquetasMalaDireta_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $EtiquetasMalaDireta_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($EtiquetasMalaDireta_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $EtiquetasMalaDireta_list->PageUrl() ?>start=<?php echo $EtiquetasMalaDireta_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($EtiquetasMalaDireta_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $EtiquetasMalaDireta_list->PageUrl() ?>start=<?php echo $EtiquetasMalaDireta_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $EtiquetasMalaDireta_list->Pager->PageCount ?></span>
</div>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $EtiquetasMalaDireta_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $EtiquetasMalaDireta_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $EtiquetasMalaDireta_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($EtiquetasMalaDireta_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
<div class="clearfix"></div>
</div>
<?php } ?>
<form name="fEtiquetasMalaDiretalist" id="fEtiquetasMalaDiretalist" class="form-inline ewForm ewListForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($EtiquetasMalaDireta_list->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $EtiquetasMalaDireta_list->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="EtiquetasMalaDireta">
<div id="gmp_EtiquetasMalaDireta" class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php if ($EtiquetasMalaDireta_list->TotalRecs > 0) { ?>
<table id="tbl_EtiquetasMalaDiretalist" class="table ewTable" style="display: none">
<?php echo $EtiquetasMalaDireta->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$EtiquetasMalaDireta_list->RenderListOptions();

// Render list options (header, left)
$EtiquetasMalaDireta_list->ListOptions->Render("header", "", "", "block", $EtiquetasMalaDireta->TableVar, "EtiquetasMalaDiretalist");
?>
<?php if ($EtiquetasMalaDireta->Nome->Visible) { // Nome ?>
	<?php if ($EtiquetasMalaDireta->SortUrl($EtiquetasMalaDireta->Nome) == "") { ?>
		<th data-name="Nome"><div id="elh_EtiquetasMalaDireta_Nome" class="EtiquetasMalaDireta_Nome"><div class="ewTableHeaderCaption"><script id="tpc_EtiquetasMalaDireta_Nome" class="EtiquetasMalaDiretalist" type="text/html"><span><?php echo $EtiquetasMalaDireta->Nome->FldCaption() ?></span></script></div></div></th>
	<?php } else { ?>
		<th data-name="Nome"><script id="tpc_EtiquetasMalaDireta_Nome" class="EtiquetasMalaDiretalist" type="text/html"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $EtiquetasMalaDireta->SortUrl($EtiquetasMalaDireta->Nome) ?>',2);"><div id="elh_EtiquetasMalaDireta_Nome" class="EtiquetasMalaDireta_Nome">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $EtiquetasMalaDireta->Nome->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($EtiquetasMalaDireta->Nome->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($EtiquetasMalaDireta->Nome->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></script></th>
	<?php } ?>
<?php } ?>		
<?php if ($EtiquetasMalaDireta->CargoMinisterial->Visible) { // CargoMinisterial ?>
	<?php if ($EtiquetasMalaDireta->SortUrl($EtiquetasMalaDireta->CargoMinisterial) == "") { ?>
		<th data-name="CargoMinisterial"><div id="elh_EtiquetasMalaDireta_CargoMinisterial" class="EtiquetasMalaDireta_CargoMinisterial"><div class="ewTableHeaderCaption"><script id="tpc_EtiquetasMalaDireta_CargoMinisterial" class="EtiquetasMalaDiretalist" type="text/html"><span><?php echo $EtiquetasMalaDireta->CargoMinisterial->FldCaption() ?></span></script></div></div></th>
	<?php } else { ?>
		<th data-name="CargoMinisterial"><script id="tpc_EtiquetasMalaDireta_CargoMinisterial" class="EtiquetasMalaDiretalist" type="text/html"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $EtiquetasMalaDireta->SortUrl($EtiquetasMalaDireta->CargoMinisterial) ?>',2);"><div id="elh_EtiquetasMalaDireta_CargoMinisterial" class="EtiquetasMalaDireta_CargoMinisterial">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $EtiquetasMalaDireta->CargoMinisterial->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($EtiquetasMalaDireta->CargoMinisterial->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($EtiquetasMalaDireta->CargoMinisterial->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></script></th>
	<?php } ?>
<?php } ?>		
<?php if ($EtiquetasMalaDireta->Funcao->Visible) { // Funcao ?>
	<?php if ($EtiquetasMalaDireta->SortUrl($EtiquetasMalaDireta->Funcao) == "") { ?>
		<th data-name="Funcao"><div id="elh_EtiquetasMalaDireta_Funcao" class="EtiquetasMalaDireta_Funcao"><div class="ewTableHeaderCaption"><script id="tpc_EtiquetasMalaDireta_Funcao" class="EtiquetasMalaDiretalist" type="text/html"><span><?php echo $EtiquetasMalaDireta->Funcao->FldCaption() ?></span></script></div></div></th>
	<?php } else { ?>
		<th data-name="Funcao"><script id="tpc_EtiquetasMalaDireta_Funcao" class="EtiquetasMalaDiretalist" type="text/html"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $EtiquetasMalaDireta->SortUrl($EtiquetasMalaDireta->Funcao) ?>',2);"><div id="elh_EtiquetasMalaDireta_Funcao" class="EtiquetasMalaDireta_Funcao">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $EtiquetasMalaDireta->Funcao->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($EtiquetasMalaDireta->Funcao->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($EtiquetasMalaDireta->Funcao->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></script></th>
	<?php } ?>
<?php } ?>		
<?php if ($EtiquetasMalaDireta->Mes->Visible) { // Mes ?>
	<?php if ($EtiquetasMalaDireta->SortUrl($EtiquetasMalaDireta->Mes) == "") { ?>
		<th data-name="Mes"><div id="elh_EtiquetasMalaDireta_Mes" class="EtiquetasMalaDireta_Mes"><div class="ewTableHeaderCaption"><script id="tpc_EtiquetasMalaDireta_Mes" class="EtiquetasMalaDiretalist" type="text/html"><span><?php echo $EtiquetasMalaDireta->Mes->FldCaption() ?></span></script></div></div></th>
	<?php } else { ?>
		<th data-name="Mes"><script id="tpc_EtiquetasMalaDireta_Mes" class="EtiquetasMalaDiretalist" type="text/html"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $EtiquetasMalaDireta->SortUrl($EtiquetasMalaDireta->Mes) ?>',2);"><div id="elh_EtiquetasMalaDireta_Mes" class="EtiquetasMalaDireta_Mes">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $EtiquetasMalaDireta->Mes->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($EtiquetasMalaDireta->Mes->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($EtiquetasMalaDireta->Mes->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></script></th>
	<?php } ?>
<?php } ?>		
<?php if ($EtiquetasMalaDireta->Endereco->Visible) { // Endereco ?>
	<?php if ($EtiquetasMalaDireta->SortUrl($EtiquetasMalaDireta->Endereco) == "") { ?>
		<th data-name="Endereco"><div id="elh_EtiquetasMalaDireta_Endereco" class="EtiquetasMalaDireta_Endereco"><div class="ewTableHeaderCaption"><script id="tpc_EtiquetasMalaDireta_Endereco" class="EtiquetasMalaDiretalist" type="text/html"><span><?php echo $EtiquetasMalaDireta->Endereco->FldCaption() ?></span></script></div></div></th>
	<?php } else { ?>
		<th data-name="Endereco"><script id="tpc_EtiquetasMalaDireta_Endereco" class="EtiquetasMalaDiretalist" type="text/html"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $EtiquetasMalaDireta->SortUrl($EtiquetasMalaDireta->Endereco) ?>',2);"><div id="elh_EtiquetasMalaDireta_Endereco" class="EtiquetasMalaDireta_Endereco">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $EtiquetasMalaDireta->Endereco->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($EtiquetasMalaDireta->Endereco->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($EtiquetasMalaDireta->Endereco->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></script></th>
	<?php } ?>
<?php } ?>		
<?php if ($EtiquetasMalaDireta->Bairro->Visible) { // Bairro ?>
	<?php if ($EtiquetasMalaDireta->SortUrl($EtiquetasMalaDireta->Bairro) == "") { ?>
		<th data-name="Bairro"><div id="elh_EtiquetasMalaDireta_Bairro" class="EtiquetasMalaDireta_Bairro"><div class="ewTableHeaderCaption"><script id="tpc_EtiquetasMalaDireta_Bairro" class="EtiquetasMalaDiretalist" type="text/html"><span><?php echo $EtiquetasMalaDireta->Bairro->FldCaption() ?></span></script></div></div></th>
	<?php } else { ?>
		<th data-name="Bairro"><script id="tpc_EtiquetasMalaDireta_Bairro" class="EtiquetasMalaDiretalist" type="text/html"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $EtiquetasMalaDireta->SortUrl($EtiquetasMalaDireta->Bairro) ?>',2);"><div id="elh_EtiquetasMalaDireta_Bairro" class="EtiquetasMalaDireta_Bairro">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $EtiquetasMalaDireta->Bairro->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($EtiquetasMalaDireta->Bairro->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($EtiquetasMalaDireta->Bairro->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></script></th>
	<?php } ?>
<?php } ?>		
<?php if ($EtiquetasMalaDireta->Cidade->Visible) { // Cidade ?>
	<?php if ($EtiquetasMalaDireta->SortUrl($EtiquetasMalaDireta->Cidade) == "") { ?>
		<th data-name="Cidade"><div id="elh_EtiquetasMalaDireta_Cidade" class="EtiquetasMalaDireta_Cidade"><div class="ewTableHeaderCaption"><script id="tpc_EtiquetasMalaDireta_Cidade" class="EtiquetasMalaDiretalist" type="text/html"><span><?php echo $EtiquetasMalaDireta->Cidade->FldCaption() ?></span></script></div></div></th>
	<?php } else { ?>
		<th data-name="Cidade"><script id="tpc_EtiquetasMalaDireta_Cidade" class="EtiquetasMalaDiretalist" type="text/html"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $EtiquetasMalaDireta->SortUrl($EtiquetasMalaDireta->Cidade) ?>',2);"><div id="elh_EtiquetasMalaDireta_Cidade" class="EtiquetasMalaDireta_Cidade">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $EtiquetasMalaDireta->Cidade->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($EtiquetasMalaDireta->Cidade->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($EtiquetasMalaDireta->Cidade->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></script></th>
	<?php } ?>
<?php } ?>		
<?php if ($EtiquetasMalaDireta->CEP->Visible) { // CEP ?>
	<?php if ($EtiquetasMalaDireta->SortUrl($EtiquetasMalaDireta->CEP) == "") { ?>
		<th data-name="CEP"><div id="elh_EtiquetasMalaDireta_CEP" class="EtiquetasMalaDireta_CEP"><div class="ewTableHeaderCaption"><script id="tpc_EtiquetasMalaDireta_CEP" class="EtiquetasMalaDiretalist" type="text/html"><span><?php echo $EtiquetasMalaDireta->CEP->FldCaption() ?></span></script></div></div></th>
	<?php } else { ?>
		<th data-name="CEP"><script id="tpc_EtiquetasMalaDireta_CEP" class="EtiquetasMalaDiretalist" type="text/html"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $EtiquetasMalaDireta->SortUrl($EtiquetasMalaDireta->CEP) ?>',2);"><div id="elh_EtiquetasMalaDireta_CEP" class="EtiquetasMalaDireta_CEP">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $EtiquetasMalaDireta->CEP->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($EtiquetasMalaDireta->CEP->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($EtiquetasMalaDireta->CEP->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></script></th>
	<?php } ?>
<?php } ?>		
<?php if ($EtiquetasMalaDireta->UF->Visible) { // UF ?>
	<?php if ($EtiquetasMalaDireta->SortUrl($EtiquetasMalaDireta->UF) == "") { ?>
		<th data-name="UF"><div id="elh_EtiquetasMalaDireta_UF" class="EtiquetasMalaDireta_UF"><div class="ewTableHeaderCaption"><script id="tpc_EtiquetasMalaDireta_UF" class="EtiquetasMalaDiretalist" type="text/html"><span><?php echo $EtiquetasMalaDireta->UF->FldCaption() ?></span></script></div></div></th>
	<?php } else { ?>
		<th data-name="UF"><script id="tpc_EtiquetasMalaDireta_UF" class="EtiquetasMalaDiretalist" type="text/html"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $EtiquetasMalaDireta->SortUrl($EtiquetasMalaDireta->UF) ?>',2);"><div id="elh_EtiquetasMalaDireta_UF" class="EtiquetasMalaDireta_UF">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $EtiquetasMalaDireta->UF->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($EtiquetasMalaDireta->UF->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($EtiquetasMalaDireta->UF->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></script></th>
	<?php } ?>
<?php } ?>		
<?php if ($EtiquetasMalaDireta->Sexo->Visible) { // Sexo ?>
	<?php if ($EtiquetasMalaDireta->SortUrl($EtiquetasMalaDireta->Sexo) == "") { ?>
		<th data-name="Sexo"><div id="elh_EtiquetasMalaDireta_Sexo" class="EtiquetasMalaDireta_Sexo"><div class="ewTableHeaderCaption"><script id="tpc_EtiquetasMalaDireta_Sexo" class="EtiquetasMalaDiretalist" type="text/html"><span><?php echo $EtiquetasMalaDireta->Sexo->FldCaption() ?></span></script></div></div></th>
	<?php } else { ?>
		<th data-name="Sexo"><script id="tpc_EtiquetasMalaDireta_Sexo" class="EtiquetasMalaDiretalist" type="text/html"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $EtiquetasMalaDireta->SortUrl($EtiquetasMalaDireta->Sexo) ?>',2);"><div id="elh_EtiquetasMalaDireta_Sexo" class="EtiquetasMalaDireta_Sexo">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $EtiquetasMalaDireta->Sexo->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($EtiquetasMalaDireta->Sexo->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($EtiquetasMalaDireta->Sexo->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></script></th>
	<?php } ?>
<?php } ?>		
<?php if ($EtiquetasMalaDireta->EstadoCivil->Visible) { // EstadoCivil ?>
	<?php if ($EtiquetasMalaDireta->SortUrl($EtiquetasMalaDireta->EstadoCivil) == "") { ?>
		<th data-name="EstadoCivil"><div id="elh_EtiquetasMalaDireta_EstadoCivil" class="EtiquetasMalaDireta_EstadoCivil"><div class="ewTableHeaderCaption"><script id="tpc_EtiquetasMalaDireta_EstadoCivil" class="EtiquetasMalaDiretalist" type="text/html"><span><?php echo $EtiquetasMalaDireta->EstadoCivil->FldCaption() ?></span></script></div></div></th>
	<?php } else { ?>
		<th data-name="EstadoCivil"><script id="tpc_EtiquetasMalaDireta_EstadoCivil" class="EtiquetasMalaDiretalist" type="text/html"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $EtiquetasMalaDireta->SortUrl($EtiquetasMalaDireta->EstadoCivil) ?>',2);"><div id="elh_EtiquetasMalaDireta_EstadoCivil" class="EtiquetasMalaDireta_EstadoCivil">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $EtiquetasMalaDireta->EstadoCivil->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($EtiquetasMalaDireta->EstadoCivil->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($EtiquetasMalaDireta->EstadoCivil->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></script></th>
	<?php } ?>
<?php } ?>		
<?php if ($EtiquetasMalaDireta->Rede_Ministerial->Visible) { // Rede_Ministerial ?>
	<?php if ($EtiquetasMalaDireta->SortUrl($EtiquetasMalaDireta->Rede_Ministerial) == "") { ?>
		<th data-name="Rede_Ministerial"><div id="elh_EtiquetasMalaDireta_Rede_Ministerial" class="EtiquetasMalaDireta_Rede_Ministerial"><div class="ewTableHeaderCaption"><script id="tpc_EtiquetasMalaDireta_Rede_Ministerial" class="EtiquetasMalaDiretalist" type="text/html"><span><?php echo $EtiquetasMalaDireta->Rede_Ministerial->FldCaption() ?></span></script></div></div></th>
	<?php } else { ?>
		<th data-name="Rede_Ministerial"><script id="tpc_EtiquetasMalaDireta_Rede_Ministerial" class="EtiquetasMalaDiretalist" type="text/html"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $EtiquetasMalaDireta->SortUrl($EtiquetasMalaDireta->Rede_Ministerial) ?>',2);"><div id="elh_EtiquetasMalaDireta_Rede_Ministerial" class="EtiquetasMalaDireta_Rede_Ministerial">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $EtiquetasMalaDireta->Rede_Ministerial->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($EtiquetasMalaDireta->Rede_Ministerial->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($EtiquetasMalaDireta->Rede_Ministerial->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></script></th>
	<?php } ?>
<?php } ?>		
<?php if ($EtiquetasMalaDireta->Celula->Visible) { // Celula ?>
	<?php if ($EtiquetasMalaDireta->SortUrl($EtiquetasMalaDireta->Celula) == "") { ?>
		<th data-name="Celula"><div id="elh_EtiquetasMalaDireta_Celula" class="EtiquetasMalaDireta_Celula"><div class="ewTableHeaderCaption"><script id="tpc_EtiquetasMalaDireta_Celula" class="EtiquetasMalaDiretalist" type="text/html"><span><?php echo $EtiquetasMalaDireta->Celula->FldCaption() ?></span></script></div></div></th>
	<?php } else { ?>
		<th data-name="Celula"><script id="tpc_EtiquetasMalaDireta_Celula" class="EtiquetasMalaDiretalist" type="text/html"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $EtiquetasMalaDireta->SortUrl($EtiquetasMalaDireta->Celula) ?>',2);"><div id="elh_EtiquetasMalaDireta_Celula" class="EtiquetasMalaDireta_Celula">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $EtiquetasMalaDireta->Celula->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($EtiquetasMalaDireta->Celula->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($EtiquetasMalaDireta->Celula->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></script></th>
	<?php } ?>
<?php } ?>		
	</tr>
</thead>
<tbody>
<?php
if ($EtiquetasMalaDireta->ExportAll && $EtiquetasMalaDireta->Export <> "") {
	$EtiquetasMalaDireta_list->StopRec = $EtiquetasMalaDireta_list->TotalRecs;
} else {

	// Set the last record to display
	if ($EtiquetasMalaDireta_list->TotalRecs > $EtiquetasMalaDireta_list->StartRec + $EtiquetasMalaDireta_list->DisplayRecs - 1)
		$EtiquetasMalaDireta_list->StopRec = $EtiquetasMalaDireta_list->StartRec + $EtiquetasMalaDireta_list->DisplayRecs - 1;
	else
		$EtiquetasMalaDireta_list->StopRec = $EtiquetasMalaDireta_list->TotalRecs;
}
$EtiquetasMalaDireta_list->RecCnt = $EtiquetasMalaDireta_list->StartRec - 1;
if ($EtiquetasMalaDireta_list->Recordset && !$EtiquetasMalaDireta_list->Recordset->EOF) {
	$EtiquetasMalaDireta_list->Recordset->MoveFirst();
	$bSelectLimit = EW_SELECT_LIMIT;
	if (!$bSelectLimit && $EtiquetasMalaDireta_list->StartRec > 1)
		$EtiquetasMalaDireta_list->Recordset->Move($EtiquetasMalaDireta_list->StartRec - 1);
} elseif (!$EtiquetasMalaDireta->AllowAddDeleteRow && $EtiquetasMalaDireta_list->StopRec == 0) {
	$EtiquetasMalaDireta_list->StopRec = $EtiquetasMalaDireta->GridAddRowCount;
}

// Initialize aggregate
$EtiquetasMalaDireta->RowType = EW_ROWTYPE_AGGREGATEINIT;
$EtiquetasMalaDireta->ResetAttrs();
$EtiquetasMalaDireta_list->RenderRow();
while ($EtiquetasMalaDireta_list->RecCnt < $EtiquetasMalaDireta_list->StopRec) {
	$EtiquetasMalaDireta_list->RecCnt++;
	if (intval($EtiquetasMalaDireta_list->RecCnt) >= intval($EtiquetasMalaDireta_list->StartRec)) {
		$EtiquetasMalaDireta_list->RowCnt++;

		// Set up key count
		$EtiquetasMalaDireta_list->KeyCount = $EtiquetasMalaDireta_list->RowIndex;

		// Init row class and style
		$EtiquetasMalaDireta->ResetAttrs();
		$EtiquetasMalaDireta->CssClass = "";
		if ($EtiquetasMalaDireta->CurrentAction == "gridadd") {
		} else {
			$EtiquetasMalaDireta_list->LoadRowValues($EtiquetasMalaDireta_list->Recordset); // Load row values
		}
		$EtiquetasMalaDireta->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$EtiquetasMalaDireta->RowAttrs = array_merge($EtiquetasMalaDireta->RowAttrs, array('data-rowindex'=>$EtiquetasMalaDireta_list->RowCnt, 'id'=>'r' . $EtiquetasMalaDireta_list->RowCnt . '_EtiquetasMalaDireta', 'data-rowtype'=>$EtiquetasMalaDireta->RowType));

		// Render row
		$EtiquetasMalaDireta_list->RenderRow();

		// Render list options
		$EtiquetasMalaDireta_list->RenderListOptions();

		// Save row and cell attributes
		$EtiquetasMalaDireta_list->Attrs[$EtiquetasMalaDireta_list->RowCnt] = array("row_attrs" => $EtiquetasMalaDireta->RowAttributes(), "cell_attrs" => array());
		foreach ($EtiquetasMalaDireta_list->fields as $fld)
			$EtiquetasMalaDireta_list->Attrs[$EtiquetasMalaDireta_list->RowCnt]["cell_attrs"][substr($fld->FldVar, 2)] = $fld->CellAttributes();
?>
	<tr<?php echo $EtiquetasMalaDireta->RowAttributes() ?>>
<?php

// Render list options (body, left)
$EtiquetasMalaDireta_list->ListOptions->Render("body", "", $EtiquetasMalaDireta_list->RowCnt, "block", $EtiquetasMalaDireta->TableVar, "EtiquetasMalaDiretalist");
?>
	<?php if ($EtiquetasMalaDireta->Nome->Visible) { // Nome ?>
		<td data-name="Nome"<?php echo $EtiquetasMalaDireta->Nome->CellAttributes() ?>>
<script id="tpx<?php echo $EtiquetasMalaDireta_list->RowCnt ?>_EtiquetasMalaDireta_Nome" class="EtiquetasMalaDiretalist" type="text/html">
<span<?php echo $EtiquetasMalaDireta->Nome->ViewAttributes() ?>>
<?php echo $EtiquetasMalaDireta->Nome->ListViewValue() ?></span>
</script>
<a id="<?php echo $EtiquetasMalaDireta_list->PageObjName . "_row_" . $EtiquetasMalaDireta_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($EtiquetasMalaDireta->CargoMinisterial->Visible) { // CargoMinisterial ?>
		<td data-name="CargoMinisterial"<?php echo $EtiquetasMalaDireta->CargoMinisterial->CellAttributes() ?>>
<script id="tpx<?php echo $EtiquetasMalaDireta_list->RowCnt ?>_EtiquetasMalaDireta_CargoMinisterial" class="EtiquetasMalaDiretalist" type="text/html">
<span<?php echo $EtiquetasMalaDireta->CargoMinisterial->ViewAttributes() ?>>
<?php echo $EtiquetasMalaDireta->CargoMinisterial->ListViewValue() ?></span>
</script>
</td>
	<?php } ?>
	<?php if ($EtiquetasMalaDireta->Funcao->Visible) { // Funcao ?>
		<td data-name="Funcao"<?php echo $EtiquetasMalaDireta->Funcao->CellAttributes() ?>>
<script id="tpx<?php echo $EtiquetasMalaDireta_list->RowCnt ?>_EtiquetasMalaDireta_Funcao" class="EtiquetasMalaDiretalist" type="text/html">
<span<?php echo $EtiquetasMalaDireta->Funcao->ViewAttributes() ?>>
<?php echo $EtiquetasMalaDireta->Funcao->ListViewValue() ?></span>
</script>
</td>
	<?php } ?>
	<?php if ($EtiquetasMalaDireta->Mes->Visible) { // Mes ?>
		<td data-name="Mes"<?php echo $EtiquetasMalaDireta->Mes->CellAttributes() ?>>
<script id="tpx<?php echo $EtiquetasMalaDireta_list->RowCnt ?>_EtiquetasMalaDireta_Mes" class="EtiquetasMalaDiretalist" type="text/html">
<span<?php echo $EtiquetasMalaDireta->Mes->ViewAttributes() ?>>
<?php echo $EtiquetasMalaDireta->Mes->ListViewValue() ?></span>
</script>
</td>
	<?php } ?>
	<?php if ($EtiquetasMalaDireta->Endereco->Visible) { // Endereco ?>
		<td data-name="Endereco"<?php echo $EtiquetasMalaDireta->Endereco->CellAttributes() ?>>
<script id="tpx<?php echo $EtiquetasMalaDireta_list->RowCnt ?>_EtiquetasMalaDireta_Endereco" class="EtiquetasMalaDiretalist" type="text/html">
<span<?php echo $EtiquetasMalaDireta->Endereco->ViewAttributes() ?>>
<?php echo $EtiquetasMalaDireta->Endereco->ListViewValue() ?></span>
</script>
</td>
	<?php } ?>
	<?php if ($EtiquetasMalaDireta->Bairro->Visible) { // Bairro ?>
		<td data-name="Bairro"<?php echo $EtiquetasMalaDireta->Bairro->CellAttributes() ?>>
<script id="tpx<?php echo $EtiquetasMalaDireta_list->RowCnt ?>_EtiquetasMalaDireta_Bairro" class="EtiquetasMalaDiretalist" type="text/html">
<span<?php echo $EtiquetasMalaDireta->Bairro->ViewAttributes() ?>>
<?php echo $EtiquetasMalaDireta->Bairro->ListViewValue() ?></span>
</script>
</td>
	<?php } ?>
	<?php if ($EtiquetasMalaDireta->Cidade->Visible) { // Cidade ?>
		<td data-name="Cidade"<?php echo $EtiquetasMalaDireta->Cidade->CellAttributes() ?>>
<script id="tpx<?php echo $EtiquetasMalaDireta_list->RowCnt ?>_EtiquetasMalaDireta_Cidade" class="EtiquetasMalaDiretalist" type="text/html">
<span<?php echo $EtiquetasMalaDireta->Cidade->ViewAttributes() ?>>
<?php echo $EtiquetasMalaDireta->Cidade->ListViewValue() ?></span>
</script>
</td>
	<?php } ?>
	<?php if ($EtiquetasMalaDireta->CEP->Visible) { // CEP ?>
		<td data-name="CEP"<?php echo $EtiquetasMalaDireta->CEP->CellAttributes() ?>>
<script id="tpx<?php echo $EtiquetasMalaDireta_list->RowCnt ?>_EtiquetasMalaDireta_CEP" class="EtiquetasMalaDiretalist" type="text/html">
<span<?php echo $EtiquetasMalaDireta->CEP->ViewAttributes() ?>>
<?php echo $EtiquetasMalaDireta->CEP->ListViewValue() ?></span>
</script>
</td>
	<?php } ?>
	<?php if ($EtiquetasMalaDireta->UF->Visible) { // UF ?>
		<td data-name="UF"<?php echo $EtiquetasMalaDireta->UF->CellAttributes() ?>>
<script id="tpx<?php echo $EtiquetasMalaDireta_list->RowCnt ?>_EtiquetasMalaDireta_UF" class="EtiquetasMalaDiretalist" type="text/html">
<span<?php echo $EtiquetasMalaDireta->UF->ViewAttributes() ?>>
<?php echo $EtiquetasMalaDireta->UF->ListViewValue() ?></span>
</script>
</td>
	<?php } ?>
	<?php if ($EtiquetasMalaDireta->Sexo->Visible) { // Sexo ?>
		<td data-name="Sexo"<?php echo $EtiquetasMalaDireta->Sexo->CellAttributes() ?>>
<script id="tpx<?php echo $EtiquetasMalaDireta_list->RowCnt ?>_EtiquetasMalaDireta_Sexo" class="EtiquetasMalaDiretalist" type="text/html">
<span<?php echo $EtiquetasMalaDireta->Sexo->ViewAttributes() ?>>
<?php echo $EtiquetasMalaDireta->Sexo->ListViewValue() ?></span>
</script>
</td>
	<?php } ?>
	<?php if ($EtiquetasMalaDireta->EstadoCivil->Visible) { // EstadoCivil ?>
		<td data-name="EstadoCivil"<?php echo $EtiquetasMalaDireta->EstadoCivil->CellAttributes() ?>>
<script id="tpx<?php echo $EtiquetasMalaDireta_list->RowCnt ?>_EtiquetasMalaDireta_EstadoCivil" class="EtiquetasMalaDiretalist" type="text/html">
<span<?php echo $EtiquetasMalaDireta->EstadoCivil->ViewAttributes() ?>>
<?php echo $EtiquetasMalaDireta->EstadoCivil->ListViewValue() ?></span>
</script>
</td>
	<?php } ?>
	<?php if ($EtiquetasMalaDireta->Rede_Ministerial->Visible) { // Rede_Ministerial ?>
		<td data-name="Rede_Ministerial"<?php echo $EtiquetasMalaDireta->Rede_Ministerial->CellAttributes() ?>>
<script id="tpx<?php echo $EtiquetasMalaDireta_list->RowCnt ?>_EtiquetasMalaDireta_Rede_Ministerial" class="EtiquetasMalaDiretalist" type="text/html">
<span<?php echo $EtiquetasMalaDireta->Rede_Ministerial->ViewAttributes() ?>>
<?php echo $EtiquetasMalaDireta->Rede_Ministerial->ListViewValue() ?></span>
</script>
</td>
	<?php } ?>
	<?php if ($EtiquetasMalaDireta->Celula->Visible) { // Celula ?>
		<td data-name="Celula"<?php echo $EtiquetasMalaDireta->Celula->CellAttributes() ?>>
<script id="tpx<?php echo $EtiquetasMalaDireta_list->RowCnt ?>_EtiquetasMalaDireta_Celula" class="EtiquetasMalaDiretalist" type="text/html">
<span<?php echo $EtiquetasMalaDireta->Celula->ViewAttributes() ?>>
<?php echo $EtiquetasMalaDireta->Celula->ListViewValue() ?></span>
</script>
</td>
	<?php } ?>
	</tr>
<?php
	}
	if ($EtiquetasMalaDireta->CurrentAction <> "gridadd")
		$EtiquetasMalaDireta_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($EtiquetasMalaDireta->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
<div id="tpd_EtiquetasMalaDiretalist" class="ewCustomTemplate"></div>
<script id="tpm_EtiquetasMalaDiretalist" type="text/html">
<div id="ct_EtiquetasMalaDireta_list"><?php if ($EtiquetasMalaDireta_list->RowCnt > 0) { ?>
<?php $x=0; ?>
<table class="">
	<tbody><tr>
<?php for ($i = $EtiquetasMalaDireta_list->StartRowCnt; $i <= $EtiquetasMalaDireta_list->RowCnt; $i++) { ?>
	<td height="95 px;" width="350 px;">
			{{include tmpl="#tpx<?php echo $i ?>_EtiquetasMalaDireta_Nome"/}}</br>
			{{include tmpl="#tpx<?php echo $i ?>_EtiquetasMalaDireta_Endereco"/}}</br>
			{{include tmpl="#tpx<?php echo $i ?>_EtiquetasMalaDireta_Bairro"/}} - CEP: {{include tmpl="#tpx<?php echo $i ?>_EtiquetasMalaDireta_CEP"/}}</br>
			{{include tmpl="#tpx<?php echo $i ?>_EtiquetasMalaDireta_Cidade"/}} - {{include tmpl="#tpx<?php echo $i ?>_EtiquetasMalaDireta_UF"/}}
	</td>
<?php
$x++;
if($x % 2==0){
	echo "</tr>";
}
?>
<?php } ?>
</tbody></table>
<?php } ?>
</div>
</script>
</div>
</form>
<?php

// Close recordset
if ($EtiquetasMalaDireta_list->Recordset)
	$EtiquetasMalaDireta_list->Recordset->Close();
?>
</div>
<?php } ?>
<?php if ($EtiquetasMalaDireta_list->TotalRecs == 0 && $EtiquetasMalaDireta->CurrentAction == "") { // Show other options ?>
<div class="ewListOtherOptions">
<?php
	foreach ($EtiquetasMalaDireta_list->OtherOptions as &$option) {
		$option->ButtonClass = "";
		$option->Render("body", "");
	}
?>
</div>
<div class="clearfix"></div>
<?php } ?>
<script type="text/javascript">
ew_ApplyTemplate("tpd_EtiquetasMalaDiretalist", "tpm_EtiquetasMalaDiretalist", "EtiquetasMalaDiretalist", "<?php echo $EtiquetasMalaDireta->CustomExport ?>");
jQuery("script.EtiquetasMalaDiretalist_js").each(function(){ew_AddScript(this.text);});
</script>
<?php if ($EtiquetasMalaDireta->Export == "") { ?>
<script type="text/javascript">
fEtiquetasMalaDiretalistsrch.Init();
fEtiquetasMalaDiretalist.Init();
$(document).ready(function($) {	$("#ajuda").click(function() {	bootbox.dialog({title: "Informações de Ajuda", message: '<?php echo str_replace("\r\n"," ",trim($help)) ?>', buttons: { success: { label: "Fechar" }}}); });});
</script>
<?php } ?>
<?php
$EtiquetasMalaDireta_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($EtiquetasMalaDireta->Export == "") { ?>
<script type="text/javascript">
$(document).ready(function($) {
	$(".ewGrid").removeClass();
})
</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$EtiquetasMalaDireta_list->Page_Terminate();
?>
