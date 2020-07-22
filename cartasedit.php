<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg11.php" ?>
<?php include_once "ewmysql11.php" ?>
<?php include_once "phpfn11.php" ?>
<?php include_once "cartasinfo.php" ?>
<?php include_once "usuariosinfo.php" ?>
<?php include_once "userfn11.php" ?>
<?php

//
// Page class
//

$cartas_edit = NULL; // Initialize page object first

class ccartas_edit extends ccartas {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{2B7992FC-5911-46A7-9310-01F4D4225C49}";

	// Table name
	var $TableName = 'cartas';

	// Page object name
	var $PageObjName = 'cartas_edit';

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
	var $AuditTrailOnEdit = TRUE;

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

		// Table object (cartas)
		if (!isset($GLOBALS["cartas"]) || get_class($GLOBALS["cartas"]) == "ccartas") {
			$GLOBALS["cartas"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["cartas"];
		}

		// Table object (usuarios)
		if (!isset($GLOBALS['usuarios'])) $GLOBALS['usuarios'] = new cusuarios();

		// User table object (usuarios)
		if (!isset($GLOBALS["UserTable"])) $GLOBALS["UserTable"] = new cusuarios();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'edit', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'cartas', TRUE);

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
		if (!$Security->CanEdit()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate(ew_GetUrl("cartaslist.php"));
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->Id->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
		global $EW_EXPORT, $cartas;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($cartas);
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
	var $DbMasterFilter;
	var $DbDetailFilter;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;

		// Load key from QueryString
		if (@$_GET["Id"] <> "") {
			$this->Id->setQueryStringValue($_GET["Id"]);
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Process form if post back
		if (@$_POST["a_edit"] <> "") {
			$this->CurrentAction = $_POST["a_edit"]; // Get action code
			$this->LoadFormValues(); // Get form values
		} else {
			$this->CurrentAction = "I"; // Default action is display
		}

		// Check if valid key
		if ($this->Id->CurrentValue == "")
			$this->Page_Terminate("cartaslist.php"); // Invalid key, return to list

		// Validate form if post back
		if (@$_POST["a_edit"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = ""; // Form error, reset action
				$this->setFailureMessage($gsFormError);
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues();
			}
		}
		switch ($this->CurrentAction) {
			case "I": // Get a record to display
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("cartaslist.php"); // No matching record, return to list
				}
				break;
			Case "U": // Update
				$this->SendEmail = TRUE; // Send email on update success
				if ($this->EditRow()) { // Update record based on key
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("UpdateSuccess")); // Update success
					$sReturnUrl = $this->getReturnUrl();
					$this->Page_Terminate($sReturnUrl); // Return to caller
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Restore form values if update failed
				}
		}

		// Render the record
		$this->RowType = EW_ROWTYPE_EDIT; // Render as Edit
		$this->ResetAttrs();
		$this->RenderRow();
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

	// Get upload files
	function GetUploadFiles() {
		global $objForm, $Language;

		// Get upload data
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->Id->FldIsDetailKey)
			$this->Id->setFormValue($objForm->GetValue("x_Id"));
		if (!$this->Corpo_TR->FldIsDetailKey) {
			$this->Corpo_TR->setFormValue($objForm->GetValue("x_Corpo_TR"));
		}
		if (!$this->Atualizado_TR->FldIsDetailKey) {
			$this->Atualizado_TR->setFormValue($objForm->GetValue("x_Atualizado_TR"));
			$this->Atualizado_TR->CurrentValue = ew_UnFormatDateTime($this->Atualizado_TR->CurrentValue, 7);
		}
		if (!$this->Corpo_CR->FldIsDetailKey) {
			$this->Corpo_CR->setFormValue($objForm->GetValue("x_Corpo_CR"));
		}
		if (!$this->Atualizado_CR->FldIsDetailKey) {
			$this->Atualizado_CR->setFormValue($objForm->GetValue("x_Atualizado_CR"));
			$this->Atualizado_CR->CurrentValue = ew_UnFormatDateTime($this->Atualizado_CR->CurrentValue, 7);
		}
		if (!$this->Corpo_EX->FldIsDetailKey) {
			$this->Corpo_EX->setFormValue($objForm->GetValue("x_Corpo_EX"));
		}
		if (!$this->Atualizado_EX->FldIsDetailKey) {
			$this->Atualizado_EX->setFormValue($objForm->GetValue("x_Atualizado_EX"));
			$this->Atualizado_EX->CurrentValue = ew_UnFormatDateTime($this->Atualizado_EX->CurrentValue, 7);
		}
		if (!$this->Corpo_Of->FldIsDetailKey) {
			$this->Corpo_Of->setFormValue($objForm->GetValue("x_Corpo_Of"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->Id->CurrentValue = $this->Id->FormValue;
		$this->Corpo_TR->CurrentValue = $this->Corpo_TR->FormValue;
		$this->Atualizado_TR->CurrentValue = $this->Atualizado_TR->FormValue;
		$this->Atualizado_TR->CurrentValue = ew_UnFormatDateTime($this->Atualizado_TR->CurrentValue, 7);
		$this->Corpo_CR->CurrentValue = $this->Corpo_CR->FormValue;
		$this->Atualizado_CR->CurrentValue = $this->Atualizado_CR->FormValue;
		$this->Atualizado_CR->CurrentValue = ew_UnFormatDateTime($this->Atualizado_CR->CurrentValue, 7);
		$this->Corpo_EX->CurrentValue = $this->Corpo_EX->FormValue;
		$this->Atualizado_EX->CurrentValue = $this->Atualizado_EX->FormValue;
		$this->Atualizado_EX->CurrentValue = ew_UnFormatDateTime($this->Atualizado_EX->CurrentValue, 7);
		$this->Corpo_Of->CurrentValue = $this->Corpo_Of->FormValue;
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
		$this->Corpo_TR->setDbValue($rs->fields('Corpo_TR'));
		$this->Atualizado_TR->setDbValue($rs->fields('Atualizado_TR'));
		$this->Corpo_CR->setDbValue($rs->fields('Corpo_CR'));
		$this->Atualizado_CR->setDbValue($rs->fields('Atualizado_CR'));
		$this->Corpo_EX->setDbValue($rs->fields('Corpo_EX'));
		$this->Atualizado_EX->setDbValue($rs->fields('Atualizado_EX'));
		$this->Corpo_Of->setDbValue($rs->fields('Corpo_Of'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->Id->DbValue = $row['Id'];
		$this->Corpo_TR->DbValue = $row['Corpo_TR'];
		$this->Atualizado_TR->DbValue = $row['Atualizado_TR'];
		$this->Corpo_CR->DbValue = $row['Corpo_CR'];
		$this->Atualizado_CR->DbValue = $row['Atualizado_CR'];
		$this->Corpo_EX->DbValue = $row['Corpo_EX'];
		$this->Atualizado_EX->DbValue = $row['Atualizado_EX'];
		$this->Corpo_Of->DbValue = $row['Corpo_Of'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// Id
		// Corpo_TR
		// Atualizado_TR
		// Corpo_CR
		// Atualizado_CR
		// Corpo_EX
		// Atualizado_EX
		// Corpo_Of

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// Id
			$this->Id->ViewValue = $this->Id->CurrentValue;
			$this->Id->ViewCustomAttributes = "";

			// Corpo_TR
			$this->Corpo_TR->ViewValue = $this->Corpo_TR->CurrentValue;
			$this->Corpo_TR->ViewCustomAttributes = "";

			// Atualizado_TR
			$this->Atualizado_TR->ViewValue = $this->Atualizado_TR->CurrentValue;
			$this->Atualizado_TR->ViewValue = ew_FormatDateTime($this->Atualizado_TR->ViewValue, 7);
			$this->Atualizado_TR->ViewCustomAttributes = "";

			// Corpo_CR
			$this->Corpo_CR->ViewValue = $this->Corpo_CR->CurrentValue;
			$this->Corpo_CR->ViewCustomAttributes = "";

			// Atualizado_CR
			$this->Atualizado_CR->ViewValue = $this->Atualizado_CR->CurrentValue;
			$this->Atualizado_CR->ViewValue = ew_FormatDateTime($this->Atualizado_CR->ViewValue, 7);
			$this->Atualizado_CR->ViewCustomAttributes = "";

			// Corpo_EX
			$this->Corpo_EX->ViewValue = $this->Corpo_EX->CurrentValue;
			$this->Corpo_EX->ViewCustomAttributes = "";

			// Atualizado_EX
			$this->Atualizado_EX->ViewValue = $this->Atualizado_EX->CurrentValue;
			$this->Atualizado_EX->ViewValue = ew_FormatDateTime($this->Atualizado_EX->ViewValue, 7);
			$this->Atualizado_EX->ViewCustomAttributes = "";

			// Corpo_Of
			$this->Corpo_Of->ViewValue = $this->Corpo_Of->CurrentValue;
			$this->Corpo_Of->ViewCustomAttributes = "";

			// Id
			$this->Id->LinkCustomAttributes = "";
			$this->Id->HrefValue = "";
			$this->Id->TooltipValue = "";

			// Corpo_TR
			$this->Corpo_TR->LinkCustomAttributes = "";
			$this->Corpo_TR->HrefValue = "";
			$this->Corpo_TR->TooltipValue = "";

			// Atualizado_TR
			$this->Atualizado_TR->LinkCustomAttributes = "";
			$this->Atualizado_TR->HrefValue = "";
			$this->Atualizado_TR->TooltipValue = "";

			// Corpo_CR
			$this->Corpo_CR->LinkCustomAttributes = "";
			$this->Corpo_CR->HrefValue = "";
			$this->Corpo_CR->TooltipValue = "";

			// Atualizado_CR
			$this->Atualizado_CR->LinkCustomAttributes = "";
			$this->Atualizado_CR->HrefValue = "";
			$this->Atualizado_CR->TooltipValue = "";

			// Corpo_EX
			$this->Corpo_EX->LinkCustomAttributes = "";
			$this->Corpo_EX->HrefValue = "";
			$this->Corpo_EX->TooltipValue = "";

			// Atualizado_EX
			$this->Atualizado_EX->LinkCustomAttributes = "";
			$this->Atualizado_EX->HrefValue = "";
			$this->Atualizado_EX->TooltipValue = "";

			// Corpo_Of
			$this->Corpo_Of->LinkCustomAttributes = "";
			$this->Corpo_Of->HrefValue = "";
			$this->Corpo_Of->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// Id
			$this->Id->EditAttrs["class"] = "form-control";
			$this->Id->EditCustomAttributes = "";

			// Corpo_TR
			$this->Corpo_TR->EditAttrs["class"] = "form-control";
			$this->Corpo_TR->EditCustomAttributes = "";
			$this->Corpo_TR->EditValue = ew_HtmlEncode($this->Corpo_TR->CurrentValue);

			// Atualizado_TR
			// Corpo_CR

			$this->Corpo_CR->EditAttrs["class"] = "form-control";
			$this->Corpo_CR->EditCustomAttributes = "";
			$this->Corpo_CR->EditValue = ew_HtmlEncode($this->Corpo_CR->CurrentValue);

			// Atualizado_CR
			// Corpo_EX

			$this->Corpo_EX->EditAttrs["class"] = "form-control";
			$this->Corpo_EX->EditCustomAttributes = "";
			$this->Corpo_EX->EditValue = ew_HtmlEncode($this->Corpo_EX->CurrentValue);

			// Atualizado_EX
			// Corpo_Of

			$this->Corpo_Of->EditAttrs["class"] = "form-control";
			$this->Corpo_Of->EditCustomAttributes = "";
			$this->Corpo_Of->EditValue = ew_HtmlEncode($this->Corpo_Of->CurrentValue);

			// Edit refer script
			// Id

			$this->Id->HrefValue = "";

			// Corpo_TR
			$this->Corpo_TR->HrefValue = "";

			// Atualizado_TR
			$this->Atualizado_TR->HrefValue = "";

			// Corpo_CR
			$this->Corpo_CR->HrefValue = "";

			// Atualizado_CR
			$this->Atualizado_CR->HrefValue = "";

			// Corpo_EX
			$this->Corpo_EX->HrefValue = "";

			// Atualizado_EX
			$this->Atualizado_EX->HrefValue = "";

			// Corpo_Of
			$this->Corpo_Of->HrefValue = "";
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

	// Validate form
	function ValidateForm() {
		global $Language, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!$this->Corpo_TR->FldIsDetailKey && !is_null($this->Corpo_TR->FormValue) && $this->Corpo_TR->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Corpo_TR->FldCaption(), $this->Corpo_TR->ReqErrMsg));
		}
		if (!$this->Corpo_CR->FldIsDetailKey && !is_null($this->Corpo_CR->FormValue) && $this->Corpo_CR->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Corpo_CR->FldCaption(), $this->Corpo_CR->ReqErrMsg));
		}
		if (!$this->Corpo_EX->FldIsDetailKey && !is_null($this->Corpo_EX->FormValue) && $this->Corpo_EX->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Corpo_EX->FldCaption(), $this->Corpo_EX->ReqErrMsg));
		}
		if (!$this->Corpo_Of->FldIsDetailKey && !is_null($this->Corpo_Of->FormValue) && $this->Corpo_Of->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Corpo_Of->FldCaption(), $this->Corpo_Of->ReqErrMsg));
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsFormError, $sFormCustomError);
		}
		return $ValidateForm;
	}

	// Update record based on key values
	function EditRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE)
			return FALSE;
		if ($rs->EOF) {
			$EditRow = FALSE; // Update Failed
		} else {

			// Save old values
			$rsold = &$rs->fields;
			$this->LoadDbValues($rsold);
			$rsnew = array();

			// Corpo_TR
			$this->Corpo_TR->SetDbValueDef($rsnew, $this->Corpo_TR->CurrentValue, NULL, $this->Corpo_TR->ReadOnly);

			// Corpo_CR
			$this->Corpo_CR->SetDbValueDef($rsnew, $this->Corpo_CR->CurrentValue, NULL, $this->Corpo_CR->ReadOnly);

			// Corpo_EX
			$this->Corpo_EX->SetDbValueDef($rsnew, $this->Corpo_EX->CurrentValue, NULL, $this->Corpo_EX->ReadOnly);

			// Corpo_Of
			$this->Corpo_Of->SetDbValueDef($rsnew, $this->Corpo_Of->CurrentValue, "", $this->Corpo_Of->ReadOnly);

			// Call Row Updating event
			$bUpdateRow = $this->Row_Updating($rsold, $rsnew);
			if ($bUpdateRow) {
				$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
				if (count($rsnew) > 0)
					$EditRow = $this->Update($rsnew, "", $rsold);
				else
					$EditRow = TRUE; // No field to update
				$conn->raiseErrorFn = '';
				if ($EditRow) {
				}
			} else {
				if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

					// Use the message, do nothing
				} elseif ($this->CancelMessage <> "") {
					$this->setFailureMessage($this->CancelMessage);
					$this->CancelMessage = "";
				} else {
					$this->setFailureMessage($Language->Phrase("UpdateCancelled"));
				}
				$EditRow = FALSE;
			}
		}

		// Call Row_Updated event
		if ($EditRow)
			$this->Row_Updated($rsold, $rsnew);
		if ($EditRow) {
			$this->WriteAuditTrailOnEdit($rsold, $rsnew);
		}
		$rs->Close();
		return $EditRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, "cartaslist.php", "", $this->TableVar, TRUE);
		$PageId = "edit";
		$Breadcrumb->Add("edit", $PageId, $url);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'cartas';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (edit page)
	function WriteAuditTrailOnEdit(&$rsold, &$rsnew) {
		if (!$this->AuditTrailOnEdit) return;
		$table = 'cartas';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rsold['Id'];

		// Write Audit Trail
		$dt = ew_StdCurrentDateTime();
		$id = ew_ScriptName();
	  $usr = CurrentUserID();
		foreach (array_keys($rsnew) as $fldname) {
			if ($this->fields[$fldname]->FldDataType <> EW_DATATYPE_BLOB) { // Ignore BLOB fields
				if ($this->fields[$fldname]->FldDataType == EW_DATATYPE_DATE) { // DateTime field
					$modified = (ew_FormatDateTime($rsold[$fldname], 0) <> ew_FormatDateTime($rsnew[$fldname], 0));
				} else {
					$modified = !ew_CompareValue($rsold[$fldname], $rsnew[$fldname]);
				}
				if ($modified) {
					if ($this->fields[$fldname]->FldDataType == EW_DATATYPE_MEMO) { // Memo field
						if (EW_AUDIT_TRAIL_TO_DATABASE) {
							$oldvalue = $rsold[$fldname];
							$newvalue = $rsnew[$fldname];
						} else {
							$oldvalue = "[MEMO]";
							$newvalue = "[MEMO]";
						}
					} elseif ($this->fields[$fldname]->FldDataType == EW_DATATYPE_XML) { // XML field
						$oldvalue = "[XML]";
						$newvalue = "[XML]";
					} else {
						$oldvalue = $rsold[$fldname];
						$newvalue = $rsnew[$fldname];
					}
					ew_WriteAuditTrail("log", $dt, $id, $usr, "U", $table, $fldname, $key, $oldvalue, $newvalue);
				}
			}
		}
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

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		$header = "<div class='alert alert-info'><div class='label label-primary'>Informa&ccedil;&atilde;o</div> Use as seguintes tags no corpo da Carta: [#nome], [#sexo], [#estadocivil], [#nacionalidade], [#cpf], [#cargoministerial], [#admissao], [#rg], [#daigreja], [#dia], [#mes],[#ano]</div>";
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
if (!isset($cartas_edit)) $cartas_edit = new ccartas_edit();

// Page init
$cartas_edit->Page_Init();

// Page main
$cartas_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$cartas_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var cartas_edit = new ew_Page("cartas_edit");
cartas_edit.PageID = "edit"; // Page ID
var EW_PAGE_ID = cartas_edit.PageID; // For backward compatibility

// Form object
var fcartasedit = new ew_Form("fcartasedit");

// Validate form
fcartasedit.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	this.PostAutoSuggest();
	if ($fobj.find("#a_confirm").val() == "F")
		return true;
	var elm, felm, uelm, addcnt = 0;
	var $k = $fobj.find("#" + this.FormKeyCountName); // Get key_count
	var rowcnt = ($k[0]) ? parseInt($k.val(), 10) : 1;
	var startcnt = (rowcnt == 0) ? 0 : 1; // Check rowcnt == 0 => Inline-Add
	var gridinsert = $fobj.find("#a_list").val() == "gridinsert";
	for (var i = startcnt; i <= rowcnt; i++) {
		var infix = ($k[0]) ? String(i) : "";
		$fobj.data("rowindex", infix);
			elm = this.GetElements("x" + infix + "_Corpo_TR");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cartas->Corpo_TR->FldCaption(), $cartas->Corpo_TR->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Corpo_CR");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cartas->Corpo_CR->FldCaption(), $cartas->Corpo_CR->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Corpo_EX");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cartas->Corpo_EX->FldCaption(), $cartas->Corpo_EX->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Corpo_Of");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cartas->Corpo_Of->FldCaption(), $cartas->Corpo_Of->ReqErrMsg)) ?>");

			// Set up row object
			ew_ElementsToRow(fobj);

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
	}

	// Process detail forms
	var dfs = $fobj.find("input[name='detailpage']").get();
	for (var i = 0; i < dfs.length; i++) {
		var df = dfs[i], val = df.value;
		if (val && ewForms[val])
			if (!ewForms[val].Validate())
				return false;
	}
	return true;
}

// Form_CustomValidate event
fcartasedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fcartasedit.ValidateRequired = true;
<?php } else { ?>
fcartasedit.ValidateRequired = false; 
<?php } ?>

// Multi-Page properties
fcartasedit.MultiPage = new ew_MultiPage("fcartasedit",
	[["x_Corpo_TR",1],["x_Corpo_CR",2],["x_Corpo_EX",3],["x_Corpo_Of",4]]
);

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php $cartas_edit->ShowPageHeader(); ?>
<?php
$cartas_edit->ShowMessage();
?>
<form name="fcartasedit" id="fcartasedit" class="form-horizontal ewForm ewEditForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($cartas_edit->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $cartas_edit->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="cartas">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<div>
<div class="tabbable" id="cartas_edit">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab_cartas1" data-toggle="tab"><?php echo $cartas->PageCaption(1) ?></a></li>
		<li><a href="#tab_cartas2" data-toggle="tab"><?php echo $cartas->PageCaption(2) ?></a></li>
		<li><a href="#tab_cartas3" data-toggle="tab"><?php echo $cartas->PageCaption(3) ?></a></li>
		<li><a href="#tab_cartas4" data-toggle="tab"><?php echo $cartas->PageCaption(4) ?></a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="tab_cartas1">
<div>
<?php if ($cartas->Corpo_TR->Visible) { // Corpo_TR ?>
	<div id="r_Corpo_TR" class="form-group">
		<label id="elh_cartas_Corpo_TR" class="col-sm-2 control-label ewLabel"><?php echo $cartas->Corpo_TR->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cartas->Corpo_TR->CellAttributes() ?>>
<span id="el_cartas_Corpo_TR">
<textarea data-field="x_Corpo_TR" class="editor" name="x_Corpo_TR" id="x_Corpo_TR" cols="70" rows="15"<?php echo $cartas->Corpo_TR->EditAttributes() ?>><?php echo $cartas->Corpo_TR->EditValue ?></textarea>
<script type="text/javascript">
ew_CreateEditor("fcartasedit", "x_Corpo_TR", 70, 15, <?php echo ($cartas->Corpo_TR->ReadOnly || FALSE) ? "true" : "false" ?>);
</script>
</span>
<?php echo $cartas->Corpo_TR->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
		</div>
		<div class="tab-pane" id="tab_cartas2">
<div>
<?php if ($cartas->Corpo_CR->Visible) { // Corpo_CR ?>
	<div id="r_Corpo_CR" class="form-group">
		<label id="elh_cartas_Corpo_CR" class="col-sm-2 control-label ewLabel"><?php echo $cartas->Corpo_CR->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cartas->Corpo_CR->CellAttributes() ?>>
<span id="el_cartas_Corpo_CR">
<textarea data-field="x_Corpo_CR" class="editor" name="x_Corpo_CR" id="x_Corpo_CR" cols="70" rows="15"<?php echo $cartas->Corpo_CR->EditAttributes() ?>><?php echo $cartas->Corpo_CR->EditValue ?></textarea>
<script type="text/javascript">
ew_CreateEditor("fcartasedit", "x_Corpo_CR", 70, 15, <?php echo ($cartas->Corpo_CR->ReadOnly || FALSE) ? "true" : "false" ?>);
</script>
</span>
<?php echo $cartas->Corpo_CR->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
		</div>
		<div class="tab-pane" id="tab_cartas3">
<div>
<?php if ($cartas->Corpo_EX->Visible) { // Corpo_EX ?>
	<div id="r_Corpo_EX" class="form-group">
		<label id="elh_cartas_Corpo_EX" class="col-sm-2 control-label ewLabel"><?php echo $cartas->Corpo_EX->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cartas->Corpo_EX->CellAttributes() ?>>
<span id="el_cartas_Corpo_EX">
<textarea data-field="x_Corpo_EX" class="editor" name="x_Corpo_EX" id="x_Corpo_EX" cols="70" rows="15"<?php echo $cartas->Corpo_EX->EditAttributes() ?>><?php echo $cartas->Corpo_EX->EditValue ?></textarea>
<script type="text/javascript">
ew_CreateEditor("fcartasedit", "x_Corpo_EX", 70, 15, <?php echo ($cartas->Corpo_EX->ReadOnly || FALSE) ? "true" : "false" ?>);
</script>
</span>
<?php echo $cartas->Corpo_EX->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
		</div>
		<div class="tab-pane" id="tab_cartas4">
<div>
<?php if ($cartas->Corpo_Of->Visible) { // Corpo_Of ?>
	<div id="r_Corpo_Of" class="form-group">
		<label id="elh_cartas_Corpo_Of" class="col-sm-2 control-label ewLabel"><?php echo $cartas->Corpo_Of->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cartas->Corpo_Of->CellAttributes() ?>>
<span id="el_cartas_Corpo_Of">
<textarea data-field="x_Corpo_Of" class="editor" name="x_Corpo_Of" id="x_Corpo_Of" cols="70" rows="15"<?php echo $cartas->Corpo_Of->EditAttributes() ?>><?php echo $cartas->Corpo_Of->EditValue ?></textarea>
<script type="text/javascript">
ew_CreateEditor("fcartasedit", "x_Corpo_Of", 70, 15, <?php echo ($cartas->Corpo_Of->ReadOnly || FALSE) ? "true" : "false" ?>);
</script>
</span>
<?php echo $cartas->Corpo_Of->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
		</div>
	</div>
</div>
</div>
<span id="el_cartas_Id">
<input type="hidden" data-field="x_Id" name="x_Id" id="x_Id" value="<?php echo ew_HtmlEncode($cartas->Id->CurrentValue) ?>">
</span>
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><i class="fa fa-floppy-o"></i>&nbsp;<?php echo $Language->Phrase("SaveBtn") ?></button>
	</div>
</div>
</form>
<script type="text/javascript">
fcartasedit.Init();
</script>
<?php
$cartas_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$cartas_edit->Page_Terminate();
?>
