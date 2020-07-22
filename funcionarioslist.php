<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg11.php" ?>
<?php include_once "ewmysql11.php" ?>
<?php include_once "phpfn11.php" ?>
<?php include_once "funcionariosinfo.php" ?>
<?php include_once "usuariosinfo.php" ?>
<?php include_once "userfn11.php" ?>
<?php

//
// Page class
//

$funcionarios_list = NULL; // Initialize page object first

class cfuncionarios_list extends cfuncionarios {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{2B7992FC-5911-46A7-9310-01F4D4225C49}";

	// Table name
	var $TableName = 'funcionarios';

	// Page object name
	var $PageObjName = 'funcionarios_list';

	// Grid form hidden field names
	var $FormName = 'ffuncionarioslist';
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

		// Table object (funcionarios)
		if (!isset($GLOBALS["funcionarios"]) || get_class($GLOBALS["funcionarios"]) == "cfuncionarios") {
			$GLOBALS["funcionarios"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["funcionarios"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "funcionariosadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "funcionariosdelete.php";
		$this->MultiUpdateUrl = "funcionariosupdate.php";

		// Table object (usuarios)
		if (!isset($GLOBALS['usuarios'])) $GLOBALS['usuarios'] = new cusuarios();

		// User table object (usuarios)
		if (!isset($GLOBALS["UserTable"])) $GLOBALS["UserTable"] = new cusuarios();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'funcionarios', TRUE);

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
		global $EW_EXPORT, $funcionarios;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($funcionarios);
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
			ew_AddFilter($this->DefaultSearchWhere, $this->BasicSearchWhere(TRUE));

			// Get basic search values
			$this->LoadBasicSearchValues();

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

			// Load basic search from default
			$this->BasicSearch->LoadDefault();
			if ($this->BasicSearch->Keyword != "")
				$sSrchBasic = $this->BasicSearchWhere();
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
		if (count($arrKeyFlds) >= 1) {
			$this->Id->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->Id->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Return basic search SQL
	function BasicSearchSQL($arKeywords, $type) {
		$sWhere = "";
		$this->BuildBasicSearchSQL($sWhere, $this->Nome, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Endereco, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Bairro, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Cidade, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->UF, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->CEP, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Celular, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Telefone_Fixo, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->_Email, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Cargo, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Setor, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->CPF, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->RG, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Org_Exp, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->CTPS_N, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->CTPS_Serie, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Titulo_Eleitor, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Numero_Filhos, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Qual_ano, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->Observacoes, $arKeywords, $type);
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
		return FALSE;
	}

	// Clear all search parameters
	function ResetSearchParms() {

		// Clear search WHERE clause
		$this->SearchWhere = "";
		$this->setSearchWhere($this->SearchWhere);

		// Clear basic search parameters
		$this->ResetBasicSearchParms();
	}

	// Load advanced search default values
	function LoadAdvancedSearchDefault() {
		return FALSE;
	}

	// Clear all basic search parameters
	function ResetBasicSearchParms() {
		$this->BasicSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore basic search values
		$this->BasicSearch->Load();
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for Ctrl pressed
		$bCtrl = (@$_GET["ctrl"] <> "");

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->EhMembro, $bCtrl); // EhMembro
			$this->UpdateSort($this->Nome, $bCtrl); // Nome
			$this->UpdateSort($this->Data_Nasc, $bCtrl); // Data_Nasc
			$this->UpdateSort($this->Estado_Civil, $bCtrl); // Estado_Civil
			$this->UpdateSort($this->Telefone_Fixo, $bCtrl); // Telefone Fixo
			$this->UpdateSort($this->_Email, $bCtrl); // Email
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
				$this->EhMembro->setSort("");
				$this->Nome->setSort("");
				$this->Data_Nasc->setSort("");
				$this->Estado_Civil->setSort("");
				$this->Telefone_Fixo->setSort("");
				$this->_Email->setSort("");
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
				$item->Body = "<a class=\"ewAction ewCustomAction\" href=\"\" onclick=\"ew_SubmitSelected(document.ffuncionarioslist, '" . ew_CurrentUrl() . "', null, '" . $action . "');return false;\">" . $name . "</a>";
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
		$item->Body = "<button type=\"button\" class=\"btn btn-warning ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $Language->Phrase("SearchPanel") . "\" data-caption=\"" . $Language->Phrase("SearchPanel") . "\" data-toggle=\"button\" data-form=\"ffuncionarioslistsrch\">" . $Language->Phrase("SearchBtn") . "</button>";
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

	// Load basic search values
	function LoadBasicSearchValues() {
		$this->BasicSearch->Keyword = @$_GET[EW_TABLE_BASIC_SEARCH];
		if ($this->BasicSearch->Keyword <> "") $this->Command = "search";
		$this->BasicSearch->Type = @$_GET[EW_TABLE_BASIC_SEARCH_TYPE];
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
		$this->EhMembro->setDbValue($rs->fields('EhMembro'));
		$this->Data_Admissao->setDbValue($rs->fields('Data_Admissao'));
		$this->Nome->setDbValue($rs->fields('Nome'));
		$this->Data_Nasc->setDbValue($rs->fields('Data_Nasc'));
		$this->Estado_Civil->setDbValue($rs->fields('Estado_Civil'));
		$this->Endereco->setDbValue($rs->fields('Endereco'));
		$this->Bairro->setDbValue($rs->fields('Bairro'));
		$this->Cidade->setDbValue($rs->fields('Cidade'));
		$this->UF->setDbValue($rs->fields('UF'));
		$this->CEP->setDbValue($rs->fields('CEP'));
		$this->Celular->setDbValue($rs->fields('Celular'));
		$this->Telefone_Fixo->setDbValue($rs->fields('Telefone Fixo'));
		$this->_Email->setDbValue($rs->fields('Email'));
		$this->Cargo->setDbValue($rs->fields('Cargo'));
		$this->Setor->setDbValue($rs->fields('Setor'));
		$this->CPF->setDbValue($rs->fields('CPF'));
		$this->RG->setDbValue($rs->fields('RG'));
		$this->Org_Exp->setDbValue($rs->fields('Org_Exp'));
		$this->Data_Expedicao->setDbValue($rs->fields('Data_Expedicao'));
		$this->CTPS_N->setDbValue($rs->fields('CTPS_N'));
		$this->CTPS_Serie->setDbValue($rs->fields('CTPS_Serie'));
		$this->Titulo_Eleitor->setDbValue($rs->fields('Titulo_Eleitor'));
		$this->Numero_Filhos->setDbValue($rs->fields('Numero_Filhos'));
		$this->Escolaridade->setDbValue($rs->fields('Escolaridade'));
		$this->Situacao->setDbValue($rs->fields('Situacao'));
		$this->Qual_ano->setDbValue($rs->fields('Qual_ano'));
		$this->Observacoes->setDbValue($rs->fields('Observacoes'));
		$this->Inativo->setDbValue($rs->fields('Inativo'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->Id->DbValue = $row['Id'];
		$this->EhMembro->DbValue = $row['EhMembro'];
		$this->Data_Admissao->DbValue = $row['Data_Admissao'];
		$this->Nome->DbValue = $row['Nome'];
		$this->Data_Nasc->DbValue = $row['Data_Nasc'];
		$this->Estado_Civil->DbValue = $row['Estado_Civil'];
		$this->Endereco->DbValue = $row['Endereco'];
		$this->Bairro->DbValue = $row['Bairro'];
		$this->Cidade->DbValue = $row['Cidade'];
		$this->UF->DbValue = $row['UF'];
		$this->CEP->DbValue = $row['CEP'];
		$this->Celular->DbValue = $row['Celular'];
		$this->Telefone_Fixo->DbValue = $row['Telefone Fixo'];
		$this->_Email->DbValue = $row['Email'];
		$this->Cargo->DbValue = $row['Cargo'];
		$this->Setor->DbValue = $row['Setor'];
		$this->CPF->DbValue = $row['CPF'];
		$this->RG->DbValue = $row['RG'];
		$this->Org_Exp->DbValue = $row['Org_Exp'];
		$this->Data_Expedicao->DbValue = $row['Data_Expedicao'];
		$this->CTPS_N->DbValue = $row['CTPS_N'];
		$this->CTPS_Serie->DbValue = $row['CTPS_Serie'];
		$this->Titulo_Eleitor->DbValue = $row['Titulo_Eleitor'];
		$this->Numero_Filhos->DbValue = $row['Numero_Filhos'];
		$this->Escolaridade->DbValue = $row['Escolaridade'];
		$this->Situacao->DbValue = $row['Situacao'];
		$this->Qual_ano->DbValue = $row['Qual_ano'];
		$this->Observacoes->DbValue = $row['Observacoes'];
		$this->Inativo->DbValue = $row['Inativo'];
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

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
		// Id

		$this->Id->CellCssStyle = "white-space: nowrap;";

		// EhMembro
		// Data_Admissao
		// Nome
		// Data_Nasc
		// Estado_Civil
		// Endereco
		// Bairro
		// Cidade
		// UF
		// CEP
		// Celular
		// Telefone Fixo
		// Email
		// Cargo
		// Setor
		// CPF
		// RG
		// Org_Exp
		// Data_Expedicao
		// CTPS_N
		// CTPS_Serie
		// Titulo_Eleitor
		// Numero_Filhos
		// Escolaridade
		// Situacao
		// Qual_ano
		// Observacoes
		// Inativo

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// EhMembro
			if (strval($this->EhMembro->CurrentValue) <> "") {
				switch ($this->EhMembro->CurrentValue) {
					case $this->EhMembro->FldTagValue(1):
						$this->EhMembro->ViewValue = $this->EhMembro->FldTagCaption(1) <> "" ? $this->EhMembro->FldTagCaption(1) : $this->EhMembro->CurrentValue;
						break;
					case $this->EhMembro->FldTagValue(2):
						$this->EhMembro->ViewValue = $this->EhMembro->FldTagCaption(2) <> "" ? $this->EhMembro->FldTagCaption(2) : $this->EhMembro->CurrentValue;
						break;
					default:
						$this->EhMembro->ViewValue = $this->EhMembro->CurrentValue;
				}
			} else {
				$this->EhMembro->ViewValue = NULL;
			}
			$this->EhMembro->ViewCustomAttributes = "";

			// Data_Admissao
			$this->Data_Admissao->ViewValue = $this->Data_Admissao->CurrentValue;
			$this->Data_Admissao->ViewValue = ew_FormatDateTime($this->Data_Admissao->ViewValue, 7);
			$this->Data_Admissao->ViewCustomAttributes = "";

			// Nome
			$this->Nome->ViewValue = $this->Nome->CurrentValue;
			$this->Nome->ViewCustomAttributes = "";

			// Data_Nasc
			$this->Data_Nasc->ViewValue = $this->Data_Nasc->CurrentValue;
			$this->Data_Nasc->ViewValue = ew_FormatDateTime($this->Data_Nasc->ViewValue, 7);
			$this->Data_Nasc->ViewCustomAttributes = "";

			// Estado_Civil
			if (strval($this->Estado_Civil->CurrentValue) <> "") {
				switch ($this->Estado_Civil->CurrentValue) {
					case $this->Estado_Civil->FldTagValue(1):
						$this->Estado_Civil->ViewValue = $this->Estado_Civil->FldTagCaption(1) <> "" ? $this->Estado_Civil->FldTagCaption(1) : $this->Estado_Civil->CurrentValue;
						break;
					case $this->Estado_Civil->FldTagValue(2):
						$this->Estado_Civil->ViewValue = $this->Estado_Civil->FldTagCaption(2) <> "" ? $this->Estado_Civil->FldTagCaption(2) : $this->Estado_Civil->CurrentValue;
						break;
					case $this->Estado_Civil->FldTagValue(3):
						$this->Estado_Civil->ViewValue = $this->Estado_Civil->FldTagCaption(3) <> "" ? $this->Estado_Civil->FldTagCaption(3) : $this->Estado_Civil->CurrentValue;
						break;
					case $this->Estado_Civil->FldTagValue(4):
						$this->Estado_Civil->ViewValue = $this->Estado_Civil->FldTagCaption(4) <> "" ? $this->Estado_Civil->FldTagCaption(4) : $this->Estado_Civil->CurrentValue;
						break;
					case $this->Estado_Civil->FldTagValue(5):
						$this->Estado_Civil->ViewValue = $this->Estado_Civil->FldTagCaption(5) <> "" ? $this->Estado_Civil->FldTagCaption(5) : $this->Estado_Civil->CurrentValue;
						break;
					default:
						$this->Estado_Civil->ViewValue = $this->Estado_Civil->CurrentValue;
				}
			} else {
				$this->Estado_Civil->ViewValue = NULL;
			}
			$this->Estado_Civil->ViewCustomAttributes = "";

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

			// Celular
			$this->Celular->ViewValue = $this->Celular->CurrentValue;
			$this->Celular->ViewCustomAttributes = "";

			// Telefone Fixo
			$this->Telefone_Fixo->ViewValue = $this->Telefone_Fixo->CurrentValue;
			$this->Telefone_Fixo->ViewCustomAttributes = "";

			// Email
			$this->_Email->ViewValue = $this->_Email->CurrentValue;
			$this->_Email->ViewCustomAttributes = "";

			// Cargo
			$this->Cargo->ViewValue = $this->Cargo->CurrentValue;
			$this->Cargo->ViewCustomAttributes = "";

			// Setor
			$this->Setor->ViewValue = $this->Setor->CurrentValue;
			$this->Setor->ViewCustomAttributes = "";

			// CPF
			$this->CPF->ViewValue = $this->CPF->CurrentValue;
			$this->CPF->ViewCustomAttributes = "";

			// RG
			$this->RG->ViewValue = $this->RG->CurrentValue;
			$this->RG->ViewCustomAttributes = "";

			// Org_Exp
			$this->Org_Exp->ViewValue = $this->Org_Exp->CurrentValue;
			$this->Org_Exp->ViewCustomAttributes = "";

			// Data_Expedicao
			$this->Data_Expedicao->ViewValue = $this->Data_Expedicao->CurrentValue;
			$this->Data_Expedicao->ViewValue = ew_FormatDateTime($this->Data_Expedicao->ViewValue, 7);
			$this->Data_Expedicao->ViewCustomAttributes = "";

			// CTPS_N
			$this->CTPS_N->ViewValue = $this->CTPS_N->CurrentValue;
			$this->CTPS_N->ViewCustomAttributes = "";

			// CTPS_Serie
			$this->CTPS_Serie->ViewValue = $this->CTPS_Serie->CurrentValue;
			$this->CTPS_Serie->ViewCustomAttributes = "";

			// Titulo_Eleitor
			$this->Titulo_Eleitor->ViewValue = $this->Titulo_Eleitor->CurrentValue;
			$this->Titulo_Eleitor->ViewCustomAttributes = "";

			// Numero_Filhos
			$this->Numero_Filhos->ViewValue = $this->Numero_Filhos->CurrentValue;
			$this->Numero_Filhos->ViewCustomAttributes = "";

			// Escolaridade
			if (strval($this->Escolaridade->CurrentValue) <> "") {
				switch ($this->Escolaridade->CurrentValue) {
					case $this->Escolaridade->FldTagValue(1):
						$this->Escolaridade->ViewValue = $this->Escolaridade->FldTagCaption(1) <> "" ? $this->Escolaridade->FldTagCaption(1) : $this->Escolaridade->CurrentValue;
						break;
					case $this->Escolaridade->FldTagValue(2):
						$this->Escolaridade->ViewValue = $this->Escolaridade->FldTagCaption(2) <> "" ? $this->Escolaridade->FldTagCaption(2) : $this->Escolaridade->CurrentValue;
						break;
					case $this->Escolaridade->FldTagValue(3):
						$this->Escolaridade->ViewValue = $this->Escolaridade->FldTagCaption(3) <> "" ? $this->Escolaridade->FldTagCaption(3) : $this->Escolaridade->CurrentValue;
						break;
					case $this->Escolaridade->FldTagValue(4):
						$this->Escolaridade->ViewValue = $this->Escolaridade->FldTagCaption(4) <> "" ? $this->Escolaridade->FldTagCaption(4) : $this->Escolaridade->CurrentValue;
						break;
					default:
						$this->Escolaridade->ViewValue = $this->Escolaridade->CurrentValue;
				}
			} else {
				$this->Escolaridade->ViewValue = NULL;
			}
			$this->Escolaridade->ViewCustomAttributes = "";

			// Situacao
			if (strval($this->Situacao->CurrentValue) <> "") {
				switch ($this->Situacao->CurrentValue) {
					case $this->Situacao->FldTagValue(1):
						$this->Situacao->ViewValue = $this->Situacao->FldTagCaption(1) <> "" ? $this->Situacao->FldTagCaption(1) : $this->Situacao->CurrentValue;
						break;
					case $this->Situacao->FldTagValue(2):
						$this->Situacao->ViewValue = $this->Situacao->FldTagCaption(2) <> "" ? $this->Situacao->FldTagCaption(2) : $this->Situacao->CurrentValue;
						break;
					case $this->Situacao->FldTagValue(3):
						$this->Situacao->ViewValue = $this->Situacao->FldTagCaption(3) <> "" ? $this->Situacao->FldTagCaption(3) : $this->Situacao->CurrentValue;
						break;
					default:
						$this->Situacao->ViewValue = $this->Situacao->CurrentValue;
				}
			} else {
				$this->Situacao->ViewValue = NULL;
			}
			$this->Situacao->ViewCustomAttributes = "";

			// Qual_ano
			$this->Qual_ano->ViewValue = $this->Qual_ano->CurrentValue;
			$this->Qual_ano->ViewCustomAttributes = "";

			// Observacoes
			$this->Observacoes->ViewValue = $this->Observacoes->CurrentValue;
			$this->Observacoes->ViewCustomAttributes = "";

			// Inativo
			if (strval($this->Inativo->CurrentValue) <> "") {
				$this->Inativo->ViewValue = "";
				$arwrk = explode(",", strval($this->Inativo->CurrentValue));
				$cnt = count($arwrk);
				for ($ari = 0; $ari < $cnt; $ari++) {
					switch (trim($arwrk[$ari])) {
						case $this->Inativo->FldTagValue(1):
							$this->Inativo->ViewValue .= $this->Inativo->FldTagCaption(1) <> "" ? $this->Inativo->FldTagCaption(1) : trim($arwrk[$ari]);
							break;
						default:
							$this->Inativo->ViewValue .= trim($arwrk[$ari]);
					}
					if ($ari < $cnt-1) $this->Inativo->ViewValue .= ew_ViewOptionSeparator($ari);
				}
			} else {
				$this->Inativo->ViewValue = NULL;
			}
			$this->Inativo->ViewCustomAttributes = "";

			// EhMembro
			$this->EhMembro->LinkCustomAttributes = "";
			$this->EhMembro->HrefValue = "";
			$this->EhMembro->TooltipValue = "";

			// Nome
			$this->Nome->LinkCustomAttributes = "";
			$this->Nome->HrefValue = "";
			$this->Nome->TooltipValue = "";

			// Data_Nasc
			$this->Data_Nasc->LinkCustomAttributes = "";
			$this->Data_Nasc->HrefValue = "";
			$this->Data_Nasc->TooltipValue = "";

			// Estado_Civil
			$this->Estado_Civil->LinkCustomAttributes = "";
			$this->Estado_Civil->HrefValue = "";
			$this->Estado_Civil->TooltipValue = "";

			// Telefone Fixo
			$this->Telefone_Fixo->LinkCustomAttributes = "";
			$this->Telefone_Fixo->HrefValue = "";
			$this->Telefone_Fixo->TooltipValue = "";

			// Email
			$this->_Email->LinkCustomAttributes = "";
			$this->_Email->HrefValue = "";
			$this->_Email->TooltipValue = "";
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

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'funcionarios';
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
if (!isset($funcionarios_list)) $funcionarios_list = new cfuncionarios_list();

// Page init
$funcionarios_list->Page_Init();

// Page main
$funcionarios_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$funcionarios_list->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var funcionarios_list = new ew_Page("funcionarios_list");
funcionarios_list.PageID = "list"; // Page ID
var EW_PAGE_ID = funcionarios_list.PageID; // For backward compatibility

// Form object
var ffuncionarioslist = new ew_Form("ffuncionarioslist");
ffuncionarioslist.FormKeyCountName = '<?php echo $funcionarios_list->FormKeyCountName ?>';

// Form_CustomValidate event
ffuncionarioslist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ffuncionarioslist.ValidateRequired = true;
<?php } else { ?>
ffuncionarioslist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

var ffuncionarioslistsrch = new ew_Form("ffuncionarioslistsrch");

// Init search panel as collapsed
if (ffuncionarioslistsrch) ffuncionarioslistsrch.InitSearchPanel = true;
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php if ($funcionarios_list->TotalRecs > 0 && $funcionarios_list->ExportOptions->Visible()) { ?>
<?php $funcionarios_list->ExportOptions->Render("body") ?>
<?php } ?>
<?php if ($funcionarios_list->SearchOptions->Visible()) { ?>
<?php $funcionarios_list->SearchOptions->Render("body") ?>
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
		if ($funcionarios_list->TotalRecs <= 0)
			$funcionarios_list->TotalRecs = $funcionarios->SelectRecordCount();
	} else {
		if (!$funcionarios_list->Recordset && ($funcionarios_list->Recordset = $funcionarios_list->LoadRecordset()))
			$funcionarios_list->TotalRecs = $funcionarios_list->Recordset->RecordCount();
	}
	$funcionarios_list->StartRec = 1;
	if ($funcionarios_list->DisplayRecs <= 0 || ($funcionarios->Export <> "" && $funcionarios->ExportAll)) // Display all records
		$funcionarios_list->DisplayRecs = $funcionarios_list->TotalRecs;
	if (!($funcionarios->Export <> "" && $funcionarios->ExportAll))
		$funcionarios_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$funcionarios_list->Recordset = $funcionarios_list->LoadRecordset($funcionarios_list->StartRec-1, $funcionarios_list->DisplayRecs);

	// Set no record found message
	if ($funcionarios->CurrentAction == "" && $funcionarios_list->TotalRecs == 0) {
		if (!$Security->CanList())
			$funcionarios_list->setWarningMessage($Language->Phrase("NoPermission"));
		if ($funcionarios_list->SearchWhere == "0=101")
			$funcionarios_list->setWarningMessage($Language->Phrase("EnterSearchCriteria"));
		else
			$funcionarios_list->setWarningMessage($Language->Phrase("NoRecord"));
	}
$funcionarios_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($funcionarios->Export == "" && $funcionarios->CurrentAction == "") { ?>
<form name="ffuncionarioslistsrch" id="ffuncionarioslistsrch" class="form-inline ewForm" action="<?php echo ew_CurrentPage() ?>">
<?php $SearchPanelClass = ($funcionarios_list->SearchWhere <> "") ? " in" : ""; ?>
<div id="ffuncionarioslistsrch_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="funcionarios">
	<div class="ewBasicSearch">
<div id="xsr_1" class="ewRow">
	<div class="ewQuickSearch input-group">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="form-control" value="<?php echo ew_HtmlEncode($funcionarios_list->BasicSearch->getKeyword()) ?>">
	<input type="hidden" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="<?php echo ew_HtmlEncode($funcionarios_list->BasicSearch->getType()) ?>">
	<div class="input-group-btn">
		<!-- <button type="button" data-toggle="dropdown" class="btn btn-default"><span id="searchtype"><?php echo $funcionarios_list->BasicSearch->getTypeNameShort() ?></span><span class="caret"></span></button>
		<ul class="dropdown-menu pull-right" role="menu">
			<li<?php if ($funcionarios_list->BasicSearch->getType() == "") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this)"><?php echo $Language->Phrase("QuickSearchAuto") ?></a></li>
			<li<?php if ($funcionarios_list->BasicSearch->getType() == "=") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'=')"><?php echo $Language->Phrase("QuickSearchExact") ?></a></li>
			<li<?php if ($funcionarios_list->BasicSearch->getType() == "AND") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'AND')"><?php echo $Language->Phrase("QuickSearchAll") ?></a></li>
			<li<?php if ($funcionarios_list->BasicSearch->getType() == "OR") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'OR')"><?php echo $Language->Phrase("QuickSearchAny") ?></a></li>
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
<?php $funcionarios_list->ShowPageHeader(); ?>
<?php
$funcionarios_list->ShowMessage();
?>
<?php if ($funcionarios_list->TotalRecs > 0 || $funcionarios->CurrentAction <> "") { ?>
<div class="ewGrid">
<div class="ewGridUpperPanel">
<?php if ($funcionarios->CurrentAction <> "gridadd" && $funcionarios->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="form-inline ewForm ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($funcionarios_list->Pager)) $funcionarios_list->Pager = new cPrevNextPager($funcionarios_list->StartRec, $funcionarios_list->DisplayRecs, $funcionarios_list->TotalRecs) ?>
<?php if ($funcionarios_list->Pager->RecordCount > 0) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($funcionarios_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $funcionarios_list->PageUrl() ?>start=<?php echo $funcionarios_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($funcionarios_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $funcionarios_list->PageUrl() ?>start=<?php echo $funcionarios_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $funcionarios_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($funcionarios_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $funcionarios_list->PageUrl() ?>start=<?php echo $funcionarios_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($funcionarios_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $funcionarios_list->PageUrl() ?>start=<?php echo $funcionarios_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $funcionarios_list->Pager->PageCount ?></span>
</div>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $funcionarios_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $funcionarios_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $funcionarios_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($funcionarios_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
<div class="clearfix"></div>
</div>
<form name="ffuncionarioslist" id="ffuncionarioslist" class="form-inline ewForm ewListForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($funcionarios_list->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $funcionarios_list->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="funcionarios">
<div id="gmp_funcionarios" class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php if ($funcionarios_list->TotalRecs > 0) { ?>
<table id="tbl_funcionarioslist" class="table ewTable">
<?php echo $funcionarios->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$funcionarios_list->RenderListOptions();

// Render list options (header, left)
$funcionarios_list->ListOptions->Render("header", "left");
?>
<?php if ($funcionarios->EhMembro->Visible) { // EhMembro ?>
	<?php if ($funcionarios->SortUrl($funcionarios->EhMembro) == "") { ?>
		<th data-name="EhMembro"><div id="elh_funcionarios_EhMembro" class="funcionarios_EhMembro"><div class="ewTableHeaderCaption"><?php echo $funcionarios->EhMembro->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="EhMembro"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $funcionarios->SortUrl($funcionarios->EhMembro) ?>',2);"><div id="elh_funcionarios_EhMembro" class="funcionarios_EhMembro">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $funcionarios->EhMembro->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($funcionarios->EhMembro->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($funcionarios->EhMembro->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($funcionarios->Nome->Visible) { // Nome ?>
	<?php if ($funcionarios->SortUrl($funcionarios->Nome) == "") { ?>
		<th data-name="Nome"><div id="elh_funcionarios_Nome" class="funcionarios_Nome"><div class="ewTableHeaderCaption"><?php echo $funcionarios->Nome->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Nome"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $funcionarios->SortUrl($funcionarios->Nome) ?>',2);"><div id="elh_funcionarios_Nome" class="funcionarios_Nome">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $funcionarios->Nome->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($funcionarios->Nome->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($funcionarios->Nome->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($funcionarios->Data_Nasc->Visible) { // Data_Nasc ?>
	<?php if ($funcionarios->SortUrl($funcionarios->Data_Nasc) == "") { ?>
		<th data-name="Data_Nasc"><div id="elh_funcionarios_Data_Nasc" class="funcionarios_Data_Nasc"><div class="ewTableHeaderCaption"><?php echo $funcionarios->Data_Nasc->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Data_Nasc"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $funcionarios->SortUrl($funcionarios->Data_Nasc) ?>',2);"><div id="elh_funcionarios_Data_Nasc" class="funcionarios_Data_Nasc">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $funcionarios->Data_Nasc->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($funcionarios->Data_Nasc->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($funcionarios->Data_Nasc->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($funcionarios->Estado_Civil->Visible) { // Estado_Civil ?>
	<?php if ($funcionarios->SortUrl($funcionarios->Estado_Civil) == "") { ?>
		<th data-name="Estado_Civil"><div id="elh_funcionarios_Estado_Civil" class="funcionarios_Estado_Civil"><div class="ewTableHeaderCaption"><?php echo $funcionarios->Estado_Civil->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Estado_Civil"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $funcionarios->SortUrl($funcionarios->Estado_Civil) ?>',2);"><div id="elh_funcionarios_Estado_Civil" class="funcionarios_Estado_Civil">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $funcionarios->Estado_Civil->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($funcionarios->Estado_Civil->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($funcionarios->Estado_Civil->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($funcionarios->Telefone_Fixo->Visible) { // Telefone Fixo ?>
	<?php if ($funcionarios->SortUrl($funcionarios->Telefone_Fixo) == "") { ?>
		<th data-name="Telefone_Fixo"><div id="elh_funcionarios_Telefone_Fixo" class="funcionarios_Telefone_Fixo"><div class="ewTableHeaderCaption"><?php echo $funcionarios->Telefone_Fixo->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Telefone_Fixo"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $funcionarios->SortUrl($funcionarios->Telefone_Fixo) ?>',2);"><div id="elh_funcionarios_Telefone_Fixo" class="funcionarios_Telefone_Fixo">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $funcionarios->Telefone_Fixo->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($funcionarios->Telefone_Fixo->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($funcionarios->Telefone_Fixo->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($funcionarios->_Email->Visible) { // Email ?>
	<?php if ($funcionarios->SortUrl($funcionarios->_Email) == "") { ?>
		<th data-name="_Email"><div id="elh_funcionarios__Email" class="funcionarios__Email"><div class="ewTableHeaderCaption"><?php echo $funcionarios->_Email->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="_Email"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $funcionarios->SortUrl($funcionarios->_Email) ?>',2);"><div id="elh_funcionarios__Email" class="funcionarios__Email">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $funcionarios->_Email->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($funcionarios->_Email->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($funcionarios->_Email->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$funcionarios_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($funcionarios->ExportAll && $funcionarios->Export <> "") {
	$funcionarios_list->StopRec = $funcionarios_list->TotalRecs;
} else {

	// Set the last record to display
	if ($funcionarios_list->TotalRecs > $funcionarios_list->StartRec + $funcionarios_list->DisplayRecs - 1)
		$funcionarios_list->StopRec = $funcionarios_list->StartRec + $funcionarios_list->DisplayRecs - 1;
	else
		$funcionarios_list->StopRec = $funcionarios_list->TotalRecs;
}
$funcionarios_list->RecCnt = $funcionarios_list->StartRec - 1;
if ($funcionarios_list->Recordset && !$funcionarios_list->Recordset->EOF) {
	$funcionarios_list->Recordset->MoveFirst();
	$bSelectLimit = EW_SELECT_LIMIT;
	if (!$bSelectLimit && $funcionarios_list->StartRec > 1)
		$funcionarios_list->Recordset->Move($funcionarios_list->StartRec - 1);
} elseif (!$funcionarios->AllowAddDeleteRow && $funcionarios_list->StopRec == 0) {
	$funcionarios_list->StopRec = $funcionarios->GridAddRowCount;
}

// Initialize aggregate
$funcionarios->RowType = EW_ROWTYPE_AGGREGATEINIT;
$funcionarios->ResetAttrs();
$funcionarios_list->RenderRow();
while ($funcionarios_list->RecCnt < $funcionarios_list->StopRec) {
	$funcionarios_list->RecCnt++;
	if (intval($funcionarios_list->RecCnt) >= intval($funcionarios_list->StartRec)) {
		$funcionarios_list->RowCnt++;

		// Set up key count
		$funcionarios_list->KeyCount = $funcionarios_list->RowIndex;

		// Init row class and style
		$funcionarios->ResetAttrs();
		$funcionarios->CssClass = "";
		if ($funcionarios->CurrentAction == "gridadd") {
		} else {
			$funcionarios_list->LoadRowValues($funcionarios_list->Recordset); // Load row values
		}
		$funcionarios->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$funcionarios->RowAttrs = array_merge($funcionarios->RowAttrs, array('data-rowindex'=>$funcionarios_list->RowCnt, 'id'=>'r' . $funcionarios_list->RowCnt . '_funcionarios', 'data-rowtype'=>$funcionarios->RowType));

		// Render row
		$funcionarios_list->RenderRow();

		// Render list options
		$funcionarios_list->RenderListOptions();
?>
	<tr<?php echo $funcionarios->RowAttributes() ?>>
<?php

// Render list options (body, left)
$funcionarios_list->ListOptions->Render("body", "left", $funcionarios_list->RowCnt);
?>
	<?php if ($funcionarios->EhMembro->Visible) { // EhMembro ?>
		<td data-name="EhMembro"<?php echo $funcionarios->EhMembro->CellAttributes() ?>>
<span<?php echo $funcionarios->EhMembro->ViewAttributes() ?>>
<?php echo $funcionarios->EhMembro->ListViewValue() ?></span>
<a id="<?php echo $funcionarios_list->PageObjName . "_row_" . $funcionarios_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($funcionarios->Nome->Visible) { // Nome ?>
		<td data-name="Nome"<?php echo $funcionarios->Nome->CellAttributes() ?>>
<span<?php echo $funcionarios->Nome->ViewAttributes() ?>>
<?php echo $funcionarios->Nome->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($funcionarios->Data_Nasc->Visible) { // Data_Nasc ?>
		<td data-name="Data_Nasc"<?php echo $funcionarios->Data_Nasc->CellAttributes() ?>>
<span<?php echo $funcionarios->Data_Nasc->ViewAttributes() ?>>
<?php echo $funcionarios->Data_Nasc->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($funcionarios->Estado_Civil->Visible) { // Estado_Civil ?>
		<td data-name="Estado_Civil"<?php echo $funcionarios->Estado_Civil->CellAttributes() ?>>
<span<?php echo $funcionarios->Estado_Civil->ViewAttributes() ?>>
<?php echo $funcionarios->Estado_Civil->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($funcionarios->Telefone_Fixo->Visible) { // Telefone Fixo ?>
		<td data-name="Telefone_Fixo"<?php echo $funcionarios->Telefone_Fixo->CellAttributes() ?>>
<span<?php echo $funcionarios->Telefone_Fixo->ViewAttributes() ?>>
<?php echo $funcionarios->Telefone_Fixo->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($funcionarios->_Email->Visible) { // Email ?>
		<td data-name="_Email"<?php echo $funcionarios->_Email->CellAttributes() ?>>
<span<?php echo $funcionarios->_Email->ViewAttributes() ?>>
<?php echo $funcionarios->_Email->ListViewValue() ?></span>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$funcionarios_list->ListOptions->Render("body", "right", $funcionarios_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($funcionarios->CurrentAction <> "gridadd")
		$funcionarios_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($funcionarios->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($funcionarios_list->Recordset)
	$funcionarios_list->Recordset->Close();
?>
</div>
<?php } ?>
<?php if ($funcionarios_list->TotalRecs == 0 && $funcionarios->CurrentAction == "") { // Show other options ?>
<div class="ewListOtherOptions">
<?php
	foreach ($funcionarios_list->OtherOptions as &$option) {
		$option->ButtonClass = "";
		$option->Render("body", "");
	}
?>
</div>
<div class="clearfix"></div>
<?php } ?>
<script type="text/javascript">
ffuncionarioslistsrch.Init();
ffuncionarioslist.Init();
$(document).ready(function($) {	$("#ajuda").click(function() {	bootbox.dialog({title: "Informações de Ajuda", message: '<?php echo str_replace("\r\n"," ",trim($help)) ?>', buttons: { success: { label: "Fechar" }}}); });});
</script>
<?php
$funcionarios_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$funcionarios_list->Page_Terminate();
?>
