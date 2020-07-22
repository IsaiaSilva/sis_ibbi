<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg11.php" ?>
<?php include_once "ewmysql11.php" ?>
<?php include_once "phpfn11.php" ?>
<?php include_once "audittrailinfo.php" ?>
<?php include_once "usuariosinfo.php" ?>
<?php include_once "userfn11.php" ?>
<?php

//
// Page class
//

$audittrail_search = NULL; // Initialize page object first

class caudittrail_search extends caudittrail {

	// Page ID
	var $PageID = 'search';

	// Project ID
	var $ProjectID = "{2B7992FC-5911-46A7-9310-01F4D4225C49}";

	// Table name
	var $TableName = 'audittrail';

	// Page object name
	var $PageObjName = 'audittrail_search';

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

		// Table object (audittrail)
		if (!isset($GLOBALS["audittrail"]) || get_class($GLOBALS["audittrail"]) == "caudittrail") {
			$GLOBALS["audittrail"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["audittrail"];
		}

		// Table object (usuarios)
		if (!isset($GLOBALS['usuarios'])) $GLOBALS['usuarios'] = new cusuarios();

		// User table object (usuarios)
		if (!isset($GLOBALS["UserTable"])) $GLOBALS["UserTable"] = new cusuarios();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'search', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'audittrail', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();
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
		if (!$Security->CanSearch()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate(ew_GetUrl("audittraillist.php"));
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();

		// Create form object
		$objForm = new cFormObj();
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
		global $EW_EXPORT, $audittrail;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($audittrail);
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
	var $IsModal = FALSE;
	var $SearchLabelClass = "col-sm-3 control-label ewLabel";
	var $SearchRightColumnClass = "col-sm-9";

	//
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsSearchError;
		global $gbSkipHeaderFooter;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Check modal
		$this->IsModal = (@$_GET["modal"] == "1" || @$_POST["modal"] == "1");
		if ($this->IsModal)
			$gbSkipHeaderFooter = TRUE;
		if ($this->IsPageRequest()) { // Validate request

			// Get action
			$this->CurrentAction = $objForm->GetValue("a_search");
			switch ($this->CurrentAction) {
				case "S": // Get search criteria

					// Build search string for advanced search, remove blank field
					$this->LoadSearchValues(); // Get search values
					if ($this->ValidateSearch()) {
						$sSrchStr = $this->BuildAdvancedSearch();
					} else {
						$sSrchStr = "";
						$this->setFailureMessage($gsSearchError);
					}
					if ($sSrchStr <> "") {
						$sSrchStr = $this->UrlParm($sSrchStr);
						$sSrchStr = "audittraillist.php" . "?" . $sSrchStr;
						if ($this->IsModal) {
							$row = array();
							$row["url"] = $sSrchStr;
							echo ew_ArrayToJson(array($row));
							$this->Page_Terminate();
							exit();
						} else {
							$this->Page_Terminate($sSrchStr); // Go to list page
						}
					}
			}
		}

		// Restore search settings from Session
		if ($gsSearchError == "")
			$this->LoadAdvancedSearch();

		// Render row for search
		$this->RowType = EW_ROWTYPE_SEARCH;
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Build advanced search
	function BuildAdvancedSearch() {
		$sSrchUrl = "";
		$this->BuildSearchUrl($sSrchUrl, $this->datetime); // datetime
		$this->BuildSearchUrl($sSrchUrl, $this->script); // script
		$this->BuildSearchUrl($sSrchUrl, $this->user); // user
		$this->BuildSearchUrl($sSrchUrl, $this->action); // action
		$this->BuildSearchUrl($sSrchUrl, $this->_table); // table
		$this->BuildSearchUrl($sSrchUrl, $this->_field); // field
		$this->BuildSearchUrl($sSrchUrl, $this->keyvalue); // keyvalue
		$this->BuildSearchUrl($sSrchUrl, $this->oldvalue); // oldvalue
		$this->BuildSearchUrl($sSrchUrl, $this->newvalue); // newvalue
		if ($sSrchUrl <> "") $sSrchUrl .= "&";
		$sSrchUrl .= "cmd=search";
		return $sSrchUrl;
	}

	// Build search URL
	function BuildSearchUrl(&$Url, &$Fld, $OprOnly=FALSE) {
		global $objForm;
		$sWrk = "";
		$FldParm = substr($Fld->FldVar, 2);
		$FldVal = $objForm->GetValue("x_$FldParm");
		$FldOpr = $objForm->GetValue("z_$FldParm");
		$FldCond = $objForm->GetValue("v_$FldParm");
		$FldVal2 = $objForm->GetValue("y_$FldParm");
		$FldOpr2 = $objForm->GetValue("w_$FldParm");
		$FldVal = ew_StripSlashes($FldVal);
		if (is_array($FldVal)) $FldVal = implode(",", $FldVal);
		$FldVal2 = ew_StripSlashes($FldVal2);
		if (is_array($FldVal2)) $FldVal2 = implode(",", $FldVal2);
		$FldOpr = strtoupper(trim($FldOpr));
		$lFldDataType = ($Fld->FldIsVirtual) ? EW_DATATYPE_STRING : $Fld->FldDataType;
		if ($FldOpr == "BETWEEN") {
			$IsValidValue = ($lFldDataType <> EW_DATATYPE_NUMBER) ||
				($lFldDataType == EW_DATATYPE_NUMBER && $this->SearchValueIsNumeric($Fld, $FldVal) && $this->SearchValueIsNumeric($Fld, $FldVal2));
			if ($FldVal <> "" && $FldVal2 <> "" && $IsValidValue) {
				$sWrk = "x_" . $FldParm . "=" . urlencode($FldVal) .
					"&y_" . $FldParm . "=" . urlencode($FldVal2) .
					"&z_" . $FldParm . "=" . urlencode($FldOpr);
			}
		} else {
			$IsValidValue = ($lFldDataType <> EW_DATATYPE_NUMBER) ||
				($lFldDataType == EW_DATATYPE_NUMBER && $this->SearchValueIsNumeric($Fld, $FldVal));
			if ($FldVal <> "" && $IsValidValue && ew_IsValidOpr($FldOpr, $lFldDataType)) {
				$sWrk = "x_" . $FldParm . "=" . urlencode($FldVal) .
					"&z_" . $FldParm . "=" . urlencode($FldOpr);
			} elseif ($FldOpr == "IS NULL" || $FldOpr == "IS NOT NULL" || ($FldOpr <> "" && $OprOnly && ew_IsValidOpr($FldOpr, $lFldDataType))) {
				$sWrk = "z_" . $FldParm . "=" . urlencode($FldOpr);
			}
			$IsValidValue = ($lFldDataType <> EW_DATATYPE_NUMBER) ||
				($lFldDataType == EW_DATATYPE_NUMBER && $this->SearchValueIsNumeric($Fld, $FldVal2));
			if ($FldVal2 <> "" && $IsValidValue && ew_IsValidOpr($FldOpr2, $lFldDataType)) {
				if ($sWrk <> "") $sWrk .= "&v_" . $FldParm . "=" . urlencode($FldCond) . "&";
				$sWrk .= "y_" . $FldParm . "=" . urlencode($FldVal2) .
					"&w_" . $FldParm . "=" . urlencode($FldOpr2);
			} elseif ($FldOpr2 == "IS NULL" || $FldOpr2 == "IS NOT NULL" || ($FldOpr2 <> "" && $OprOnly && ew_IsValidOpr($FldOpr2, $lFldDataType))) {
				if ($sWrk <> "") $sWrk .= "&v_" . $FldParm . "=" . urlencode($FldCond) . "&";
				$sWrk .= "w_" . $FldParm . "=" . urlencode($FldOpr2);
			}
		}
		if ($sWrk <> "") {
			if ($Url <> "") $Url .= "&";
			$Url .= $sWrk;
		}
	}

	function SearchValueIsNumeric($Fld, $Value) {
		if (ew_IsFloatFormat($Fld->FldType)) $Value = ew_StrToFloat($Value);
		return is_numeric($Value);
	}

	//  Load search values for validation
	function LoadSearchValues() {
		global $objForm;

		// Load search values
		// datetime

		$this->datetime->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_datetime"));
		$this->datetime->AdvancedSearch->SearchOperator = $objForm->GetValue("z_datetime");
		$this->datetime->AdvancedSearch->SearchCondition = $objForm->GetValue("v_datetime");
		$this->datetime->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_datetime"));
		$this->datetime->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_datetime");

		// script
		$this->script->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_script"));
		$this->script->AdvancedSearch->SearchOperator = $objForm->GetValue("z_script");

		// user
		$this->user->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_user"));
		$this->user->AdvancedSearch->SearchOperator = $objForm->GetValue("z_user");

		// action
		$this->action->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_action"));
		$this->action->AdvancedSearch->SearchOperator = $objForm->GetValue("z_action");

		// table
		$this->_table->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x__table"));
		$this->_table->AdvancedSearch->SearchOperator = $objForm->GetValue("z__table");

		// field
		$this->_field->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x__field"));
		$this->_field->AdvancedSearch->SearchOperator = $objForm->GetValue("z__field");

		// keyvalue
		$this->keyvalue->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_keyvalue"));
		$this->keyvalue->AdvancedSearch->SearchOperator = $objForm->GetValue("z_keyvalue");

		// oldvalue
		$this->oldvalue->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_oldvalue"));
		$this->oldvalue->AdvancedSearch->SearchOperator = $objForm->GetValue("z_oldvalue");

		// newvalue
		$this->newvalue->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_newvalue"));
		$this->newvalue->AdvancedSearch->SearchOperator = $objForm->GetValue("z_newvalue");
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// id
		// datetime
		// script
		// user
		// action
		// table
		// field
		// keyvalue
		// oldvalue
		// newvalue

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// datetime
			$this->datetime->ViewValue = $this->datetime->CurrentValue;
			$this->datetime->ViewValue = ew_FormatDateTime($this->datetime->ViewValue, 11);
			$this->datetime->ViewCustomAttributes = "";

			// script
			$this->script->ViewValue = $this->script->CurrentValue;
			$this->script->ViewCustomAttributes = "";

			// user
			$this->user->ViewValue = $this->user->CurrentValue;
			$this->user->ViewCustomAttributes = "";

			// action
			$this->action->ViewValue = $this->action->CurrentValue;
			$this->action->ViewCustomAttributes = "";

			// table
			$this->_table->ViewValue = $this->_table->CurrentValue;
			$this->_table->ViewCustomAttributes = "";

			// field
			$this->_field->ViewValue = $this->_field->CurrentValue;
			$this->_field->ViewCustomAttributes = "";

			// keyvalue
			$this->keyvalue->ViewValue = $this->keyvalue->CurrentValue;
			$this->keyvalue->ViewCustomAttributes = "";

			// oldvalue
			$this->oldvalue->ViewValue = $this->oldvalue->CurrentValue;
			$this->oldvalue->ViewCustomAttributes = "";

			// newvalue
			$this->newvalue->ViewValue = $this->newvalue->CurrentValue;
			$this->newvalue->ViewCustomAttributes = "";

			// datetime
			$this->datetime->LinkCustomAttributes = "";
			$this->datetime->HrefValue = "";
			$this->datetime->TooltipValue = "";

			// script
			$this->script->LinkCustomAttributes = "";
			$this->script->HrefValue = "";
			$this->script->TooltipValue = "";

			// user
			$this->user->LinkCustomAttributes = "";
			$this->user->HrefValue = "";
			$this->user->TooltipValue = "";

			// action
			$this->action->LinkCustomAttributes = "";
			$this->action->HrefValue = "";
			$this->action->TooltipValue = "";

			// table
			$this->_table->LinkCustomAttributes = "";
			$this->_table->HrefValue = "";
			$this->_table->TooltipValue = "";

			// field
			$this->_field->LinkCustomAttributes = "";
			$this->_field->HrefValue = "";
			$this->_field->TooltipValue = "";

			// keyvalue
			$this->keyvalue->LinkCustomAttributes = "";
			$this->keyvalue->HrefValue = "";
			$this->keyvalue->TooltipValue = "";

			// oldvalue
			$this->oldvalue->LinkCustomAttributes = "";
			$this->oldvalue->HrefValue = "";
			$this->oldvalue->TooltipValue = "";

			// newvalue
			$this->newvalue->LinkCustomAttributes = "";
			$this->newvalue->HrefValue = "";
			$this->newvalue->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// datetime
			$this->datetime->EditAttrs["class"] = "form-control";
			$this->datetime->EditCustomAttributes = "";
			$this->datetime->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->datetime->AdvancedSearch->SearchValue, 11), 11));
			$this->datetime->EditAttrs["class"] = "form-control";
			$this->datetime->EditCustomAttributes = "";
			$this->datetime->EditValue2 = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->datetime->AdvancedSearch->SearchValue2, 11), 11));

			// script
			$this->script->EditAttrs["class"] = "form-control";
			$this->script->EditCustomAttributes = "";
			$this->script->EditValue = ew_HtmlEncode($this->script->AdvancedSearch->SearchValue);

			// user
			$this->user->EditAttrs["class"] = "form-control";
			$this->user->EditCustomAttributes = "";
			$this->user->EditValue = ew_HtmlEncode($this->user->AdvancedSearch->SearchValue);

			// action
			$this->action->EditAttrs["class"] = "form-control";
			$this->action->EditCustomAttributes = "";
			$this->action->EditValue = ew_HtmlEncode($this->action->AdvancedSearch->SearchValue);

			// table
			$this->_table->EditAttrs["class"] = "form-control";
			$this->_table->EditCustomAttributes = "";
			$this->_table->EditValue = ew_HtmlEncode($this->_table->AdvancedSearch->SearchValue);

			// field
			$this->_field->EditAttrs["class"] = "form-control";
			$this->_field->EditCustomAttributes = "";
			$this->_field->EditValue = ew_HtmlEncode($this->_field->AdvancedSearch->SearchValue);

			// keyvalue
			$this->keyvalue->EditAttrs["class"] = "form-control";
			$this->keyvalue->EditCustomAttributes = "";
			$this->keyvalue->EditValue = ew_HtmlEncode($this->keyvalue->AdvancedSearch->SearchValue);

			// oldvalue
			$this->oldvalue->EditAttrs["class"] = "form-control";
			$this->oldvalue->EditCustomAttributes = "";
			$this->oldvalue->EditValue = ew_HtmlEncode($this->oldvalue->AdvancedSearch->SearchValue);

			// newvalue
			$this->newvalue->EditAttrs["class"] = "form-control";
			$this->newvalue->EditCustomAttributes = "";
			$this->newvalue->EditValue = ew_HtmlEncode($this->newvalue->AdvancedSearch->SearchValue);
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
		if (!ew_CheckEuroDate($this->datetime->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->datetime->FldErrMsg());
		}
		if (!ew_CheckEuroDate($this->datetime->AdvancedSearch->SearchValue2)) {
			ew_AddMessage($gsSearchError, $this->datetime->FldErrMsg());
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
		$this->datetime->AdvancedSearch->Load();
		$this->script->AdvancedSearch->Load();
		$this->user->AdvancedSearch->Load();
		$this->action->AdvancedSearch->Load();
		$this->_table->AdvancedSearch->Load();
		$this->_field->AdvancedSearch->Load();
		$this->keyvalue->AdvancedSearch->Load();
		$this->oldvalue->AdvancedSearch->Load();
		$this->newvalue->AdvancedSearch->Load();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, "audittraillist.php", "", $this->TableVar, TRUE);
		$PageId = "search";
		$Breadcrumb->Add("search", $PageId, $url);
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
}
?>
<?php ew_Header(TRUE) ?>
<?php

// Create page object
if (!isset($audittrail_search)) $audittrail_search = new caudittrail_search();

// Page init
$audittrail_search->Page_Init();

// Page main
$audittrail_search->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$audittrail_search->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var audittrail_search = new ew_Page("audittrail_search");
audittrail_search.PageID = "search"; // Page ID
var EW_PAGE_ID = audittrail_search.PageID; // For backward compatibility

// Form object
var faudittrailsearch = new ew_Form("faudittrailsearch");

// Form_CustomValidate event
faudittrailsearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
faudittrailsearch.ValidateRequired = true;
<?php } else { ?>
faudittrailsearch.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search
// Validate function for search

faudittrailsearch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	this.PostAutoSuggest();
	var infix = "";
	elm = this.GetElements("x" + infix + "_datetime");
	if (elm && !ew_CheckEuroDate(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($audittrail->datetime->FldErrMsg()) ?>");

	// Set up row object
	ew_ElementsToRow(fobj);

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$audittrail_search->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $audittrail_search->ShowPageHeader(); ?>
<?php
$audittrail_search->ShowMessage();
?>
<form name="faudittrailsearch" id="faudittrailsearch" class="form-horizontal ewForm ewSearchForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($audittrail_search->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $audittrail_search->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="audittrail">
<input type="hidden" name="a_search" id="a_search" value="S">
<?php if ($audittrail_search->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<div>
<?php if ($audittrail->datetime->Visible) { // datetime ?>
	<div id="r_datetime" class="form-group">
		<label for="x_datetime" class="<?php echo $audittrail_search->SearchLabelClass ?>"><span id="elh_audittrail_datetime"><?php echo $audittrail->datetime->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("BETWEEN") ?><input type="hidden" name="z_datetime" id="z_datetime" value="BETWEEN"></p>
		</label>
		<div class="<?php echo $audittrail_search->SearchRightColumnClass ?>"><div<?php echo $audittrail->datetime->CellAttributes() ?>>
			<span id="el_audittrail_datetime">
<input type="text" data-field="x_datetime" name="x_datetime" id="x_datetime" size="12" value="<?php echo $audittrail->datetime->EditValue ?>"<?php echo $audittrail->datetime->EditAttributes() ?>>
<?php if (!$audittrail->datetime->ReadOnly && !$audittrail->datetime->Disabled && @$audittrail->datetime->EditAttrs["readonly"] == "" && @$audittrail->datetime->EditAttrs["disabled"] == "") { ?>
<script type="text/javascript">
ew_CreateCalendar("faudittrailsearch", "x_datetime", "%d/%m/%Y %H:%M:%S");
</script>
<?php } ?>
</span>
			<span class="ewSearchCond btw1_datetime">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span id="e2_audittrail_datetime" class="btw1_datetime">
<input type="text" data-field="x_datetime" name="y_datetime" id="y_datetime" size="12" value="<?php echo $audittrail->datetime->EditValue2 ?>"<?php echo $audittrail->datetime->EditAttributes() ?>>
<?php if (!$audittrail->datetime->ReadOnly && !$audittrail->datetime->Disabled && @$audittrail->datetime->EditAttrs["readonly"] == "" && @$audittrail->datetime->EditAttrs["disabled"] == "") { ?>
<script type="text/javascript">
ew_CreateCalendar("faudittrailsearch", "y_datetime", "%d/%m/%Y %H:%M:%S");
</script>
<?php } ?>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($audittrail->script->Visible) { // script ?>
	<div id="r_script" class="form-group">
		<label for="x_script" class="<?php echo $audittrail_search->SearchLabelClass ?>"><span id="elh_audittrail_script"><?php echo $audittrail->script->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_script" id="z_script" value="LIKE"></p>
		</label>
		<div class="<?php echo $audittrail_search->SearchRightColumnClass ?>"><div<?php echo $audittrail->script->CellAttributes() ?>>
			<span id="el_audittrail_script">
<input type="text" data-field="x_script" name="x_script" id="x_script" size="30" maxlength="255" value="<?php echo $audittrail->script->EditValue ?>"<?php echo $audittrail->script->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($audittrail->user->Visible) { // user ?>
	<div id="r_user" class="form-group">
		<label for="x_user" class="<?php echo $audittrail_search->SearchLabelClass ?>"><span id="elh_audittrail_user"><?php echo $audittrail->user->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_user" id="z_user" value="LIKE"></p>
		</label>
		<div class="<?php echo $audittrail_search->SearchRightColumnClass ?>"><div<?php echo $audittrail->user->CellAttributes() ?>>
			<span id="el_audittrail_user">
<input type="text" data-field="x_user" name="x_user" id="x_user" size="30" maxlength="255" value="<?php echo $audittrail->user->EditValue ?>"<?php echo $audittrail->user->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($audittrail->action->Visible) { // action ?>
	<div id="r_action" class="form-group">
		<label for="x_action" class="<?php echo $audittrail_search->SearchLabelClass ?>"><span id="elh_audittrail_action"><?php echo $audittrail->action->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_action" id="z_action" value="LIKE"></p>
		</label>
		<div class="<?php echo $audittrail_search->SearchRightColumnClass ?>"><div<?php echo $audittrail->action->CellAttributes() ?>>
			<span id="el_audittrail_action">
<input type="text" data-field="x_action" name="x_action" id="x_action" size="30" maxlength="255" value="<?php echo $audittrail->action->EditValue ?>"<?php echo $audittrail->action->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($audittrail->_table->Visible) { // table ?>
	<div id="r__table" class="form-group">
		<label for="x__table" class="<?php echo $audittrail_search->SearchLabelClass ?>"><span id="elh_audittrail__table"><?php echo $audittrail->_table->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z__table" id="z__table" value="LIKE"></p>
		</label>
		<div class="<?php echo $audittrail_search->SearchRightColumnClass ?>"><div<?php echo $audittrail->_table->CellAttributes() ?>>
			<span id="el_audittrail__table">
<input type="text" data-field="x__table" name="x__table" id="x__table" size="30" maxlength="255" value="<?php echo $audittrail->_table->EditValue ?>"<?php echo $audittrail->_table->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($audittrail->_field->Visible) { // field ?>
	<div id="r__field" class="form-group">
		<label for="x__field" class="<?php echo $audittrail_search->SearchLabelClass ?>"><span id="elh_audittrail__field"><?php echo $audittrail->_field->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z__field" id="z__field" value="LIKE"></p>
		</label>
		<div class="<?php echo $audittrail_search->SearchRightColumnClass ?>"><div<?php echo $audittrail->_field->CellAttributes() ?>>
			<span id="el_audittrail__field">
<input type="text" data-field="x__field" name="x__field" id="x__field" size="30" maxlength="255" value="<?php echo $audittrail->_field->EditValue ?>"<?php echo $audittrail->_field->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($audittrail->keyvalue->Visible) { // keyvalue ?>
	<div id="r_keyvalue" class="form-group">
		<label for="x_keyvalue" class="<?php echo $audittrail_search->SearchLabelClass ?>"><span id="elh_audittrail_keyvalue"><?php echo $audittrail->keyvalue->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_keyvalue" id="z_keyvalue" value="LIKE"></p>
		</label>
		<div class="<?php echo $audittrail_search->SearchRightColumnClass ?>"><div<?php echo $audittrail->keyvalue->CellAttributes() ?>>
			<span id="el_audittrail_keyvalue">
<input type="text" data-field="x_keyvalue" name="x_keyvalue" id="x_keyvalue" size="35" value="<?php echo $audittrail->keyvalue->EditValue ?>"<?php echo $audittrail->keyvalue->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($audittrail->oldvalue->Visible) { // oldvalue ?>
	<div id="r_oldvalue" class="form-group">
		<label for="x_oldvalue" class="<?php echo $audittrail_search->SearchLabelClass ?>"><span id="elh_audittrail_oldvalue"><?php echo $audittrail->oldvalue->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_oldvalue" id="z_oldvalue" value="LIKE"></p>
		</label>
		<div class="<?php echo $audittrail_search->SearchRightColumnClass ?>"><div<?php echo $audittrail->oldvalue->CellAttributes() ?>>
			<span id="el_audittrail_oldvalue">
<input type="text" data-field="x_oldvalue" name="x_oldvalue" id="x_oldvalue" size="35" value="<?php echo $audittrail->oldvalue->EditValue ?>"<?php echo $audittrail->oldvalue->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($audittrail->newvalue->Visible) { // newvalue ?>
	<div id="r_newvalue" class="form-group">
		<label for="x_newvalue" class="<?php echo $audittrail_search->SearchLabelClass ?>"><span id="elh_audittrail_newvalue"><?php echo $audittrail->newvalue->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_newvalue" id="z_newvalue" value="LIKE"></p>
		</label>
		<div class="<?php echo $audittrail_search->SearchRightColumnClass ?>"><div<?php echo $audittrail->newvalue->CellAttributes() ?>>
			<span id="el_audittrail_newvalue">
<input type="text" data-field="x_newvalue" name="x_newvalue" id="x_newvalue" size="35" value="<?php echo $audittrail->newvalue->EditValue ?>"<?php echo $audittrail->newvalue->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
</div>
<?php if (!$audittrail_search->IsModal) { ?>
<div class="form-group">
	<div class="col-sm-offset-3 col-sm-9">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("Search") ?></button>
<button class="btn btn-default ewButton" name="btnReset" id="btnReset" type="button" onclick="ew_ClearForm(this.form);"><?php echo $Language->Phrase("Reset") ?></button>
	</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
faudittrailsearch.Init();
</script>
<?php
$audittrail_search->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$audittrail_search->Page_Terminate();
?>
