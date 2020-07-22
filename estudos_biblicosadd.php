<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg11.php" ?>
<?php include_once "ewmysql11.php" ?>
<?php include_once "phpfn11.php" ?>
<?php include_once "estudos_biblicosinfo.php" ?>
<?php include_once "usuariosinfo.php" ?>
<?php include_once "userfn11.php" ?>
<?php

//
// Page class
//

$estudos_biblicos_add = NULL; // Initialize page object first

class cestudos_biblicos_add extends cestudos_biblicos {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{2B7992FC-5911-46A7-9310-01F4D4225C49}";

	// Table name
	var $TableName = 'estudos_biblicos';

	// Page object name
	var $PageObjName = 'estudos_biblicos_add';

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
	var $AuditTrailOnAdd = TRUE;

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

		// Table object (estudos_biblicos)
		if (!isset($GLOBALS["estudos_biblicos"]) || get_class($GLOBALS["estudos_biblicos"]) == "cestudos_biblicos") {
			$GLOBALS["estudos_biblicos"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["estudos_biblicos"];
		}

		// Table object (usuarios)
		if (!isset($GLOBALS['usuarios'])) $GLOBALS['usuarios'] = new cusuarios();

		// User table object (usuarios)
		if (!isset($GLOBALS["UserTable"])) $GLOBALS["UserTable"] = new cusuarios();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'estudos_biblicos', TRUE);

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
		if (!$Security->CanAdd()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate(ew_GetUrl("estudos_biblicoslist.php"));
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
		global $EW_EXPORT, $estudos_biblicos;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($estudos_biblicos);
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
	var $DbMasterFilter = "";
	var $DbDetailFilter = "";
	var $StartRec;
	var $Priv = 0;
	var $OldRecordset;
	var $CopyRecord;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;

		// Process form if post back
		if (@$_POST["a_add"] <> "") {
			$this->CurrentAction = $_POST["a_add"]; // Get form action
			$this->CopyRecord = $this->LoadOldRecord(); // Load old recordset
			$this->LoadFormValues(); // Load form values
		} else { // Not post back

			// Load key values from QueryString
			$this->CopyRecord = TRUE;
			if (@$_GET["Id_estu_bb"] != "") {
				$this->Id_estu_bb->setQueryStringValue($_GET["Id_estu_bb"]);
				$this->setKey("Id_estu_bb", $this->Id_estu_bb->CurrentValue); // Set up key
			} else {
				$this->setKey("Id_estu_bb", ""); // Clear key
				$this->CopyRecord = FALSE;
			}
			if ($this->CopyRecord) {
				$this->CurrentAction = "C"; // Copy record
			} else {
				$this->CurrentAction = "I"; // Display blank record
				$this->LoadDefaultValues(); // Load default values
			}
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Validate form if post back
		if (@$_POST["a_add"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = "I"; // Form error, reset action
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues(); // Restore form values
				$this->setFailureMessage($gsFormError);
			}
		}

		// Perform action based on action code
		switch ($this->CurrentAction) {
			case "I": // Blank record, no action required
				break;
			case "C": // Copy an existing record
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("estudos_biblicoslist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "estudos_biblicosview.php")
						$sReturnUrl = $this->GetViewUrl(); // View paging, return to view page with keyurl directly
					$this->Page_Terminate($sReturnUrl); // Clean up and return
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Add failed, restore form values
				}
		}

		// Render row based on row type
		$this->RowType = EW_ROWTYPE_ADD;  // Render add type

		// Render row
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm, $Language;

		// Get upload data
	}

	// Load default values
	function LoadDefaultValues() {
		$this->Numero_do_Estudo->CurrentValue = NULL;
		$this->Numero_do_Estudo->OldValue = $this->Numero_do_Estudo->CurrentValue;
		$this->Data_do_Estudo->CurrentValue = NULL;
		$this->Data_do_Estudo->OldValue = $this->Data_do_Estudo->CurrentValue;
		$this->Assunto->CurrentValue = NULL;
		$this->Assunto->OldValue = $this->Assunto->CurrentValue;
		$this->DescricaoMensagem->CurrentValue = NULL;
		$this->DescricaoMensagem->OldValue = $this->DescricaoMensagem->CurrentValue;
		$this->Anotacoes->CurrentValue = NULL;
		$this->Anotacoes->OldValue = $this->Anotacoes->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->Numero_do_Estudo->FldIsDetailKey) {
			$this->Numero_do_Estudo->setFormValue($objForm->GetValue("x_Numero_do_Estudo"));
		}
		if (!$this->Data_do_Estudo->FldIsDetailKey) {
			$this->Data_do_Estudo->setFormValue($objForm->GetValue("x_Data_do_Estudo"));
			$this->Data_do_Estudo->CurrentValue = ew_UnFormatDateTime($this->Data_do_Estudo->CurrentValue, 7);
		}
		if (!$this->Assunto->FldIsDetailKey) {
			$this->Assunto->setFormValue($objForm->GetValue("x_Assunto"));
		}
		if (!$this->DescricaoMensagem->FldIsDetailKey) {
			$this->DescricaoMensagem->setFormValue($objForm->GetValue("x_DescricaoMensagem"));
		}
		if (!$this->Anotacoes->FldIsDetailKey) {
			$this->Anotacoes->setFormValue($objForm->GetValue("x_Anotacoes"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->Numero_do_Estudo->CurrentValue = $this->Numero_do_Estudo->FormValue;
		$this->Data_do_Estudo->CurrentValue = $this->Data_do_Estudo->FormValue;
		$this->Data_do_Estudo->CurrentValue = ew_UnFormatDateTime($this->Data_do_Estudo->CurrentValue, 7);
		$this->Assunto->CurrentValue = $this->Assunto->FormValue;
		$this->DescricaoMensagem->CurrentValue = $this->DescricaoMensagem->FormValue;
		$this->Anotacoes->CurrentValue = $this->Anotacoes->FormValue;
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
		$this->Id_estu_bb->setDbValue($rs->fields('Id_estu_bb'));
		$this->Numero_do_Estudo->setDbValue($rs->fields('Numero_do_Estudo'));
		$this->Data_do_Estudo->setDbValue($rs->fields('Data_do_Estudo'));
		$this->Assunto->setDbValue($rs->fields('Assunto'));
		$this->DescricaoMensagem->setDbValue($rs->fields('DescricaoMensagem'));
		$this->Anotacoes->setDbValue($rs->fields('Anotacoes'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->Id_estu_bb->DbValue = $row['Id_estu_bb'];
		$this->Numero_do_Estudo->DbValue = $row['Numero_do_Estudo'];
		$this->Data_do_Estudo->DbValue = $row['Data_do_Estudo'];
		$this->Assunto->DbValue = $row['Assunto'];
		$this->DescricaoMensagem->DbValue = $row['DescricaoMensagem'];
		$this->Anotacoes->DbValue = $row['Anotacoes'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("Id_estu_bb")) <> "")
			$this->Id_estu_bb->CurrentValue = $this->getKey("Id_estu_bb"); // Id_estu_bb
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
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// Id_estu_bb
		// Numero_do_Estudo
		// Data_do_Estudo
		// Assunto
		// DescricaoMensagem
		// Anotacoes

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// Numero_do_Estudo
			$this->Numero_do_Estudo->ViewValue = $this->Numero_do_Estudo->CurrentValue;
			$this->Numero_do_Estudo->ViewCustomAttributes = "";

			// Data_do_Estudo
			$this->Data_do_Estudo->ViewValue = $this->Data_do_Estudo->CurrentValue;
			$this->Data_do_Estudo->ViewValue = ew_FormatDateTime($this->Data_do_Estudo->ViewValue, 7);
			$this->Data_do_Estudo->ViewCustomAttributes = "";

			// Assunto
			$this->Assunto->ViewValue = $this->Assunto->CurrentValue;
			$this->Assunto->ViewCustomAttributes = "";

			// DescricaoMensagem
			$this->DescricaoMensagem->ViewValue = $this->DescricaoMensagem->CurrentValue;
			$this->DescricaoMensagem->ViewCustomAttributes = "";

			// Anotacoes
			$this->Anotacoes->ViewValue = $this->Anotacoes->CurrentValue;
			$this->Anotacoes->ViewCustomAttributes = "";

			// Numero_do_Estudo
			$this->Numero_do_Estudo->LinkCustomAttributes = "";
			$this->Numero_do_Estudo->HrefValue = "";
			$this->Numero_do_Estudo->TooltipValue = "";

			// Data_do_Estudo
			$this->Data_do_Estudo->LinkCustomAttributes = "";
			$this->Data_do_Estudo->HrefValue = "";
			$this->Data_do_Estudo->TooltipValue = "";

			// Assunto
			$this->Assunto->LinkCustomAttributes = "";
			$this->Assunto->HrefValue = "";
			$this->Assunto->TooltipValue = "";

			// DescricaoMensagem
			$this->DescricaoMensagem->LinkCustomAttributes = "";
			$this->DescricaoMensagem->HrefValue = "";
			$this->DescricaoMensagem->TooltipValue = "";

			// Anotacoes
			$this->Anotacoes->LinkCustomAttributes = "";
			$this->Anotacoes->HrefValue = "";
			$this->Anotacoes->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// Numero_do_Estudo
			$this->Numero_do_Estudo->EditAttrs["class"] = "form-control";
			$this->Numero_do_Estudo->EditCustomAttributes = "";
			$this->Numero_do_Estudo->EditValue = ew_HtmlEncode($this->Numero_do_Estudo->CurrentValue);

			// Data_do_Estudo
			$this->Data_do_Estudo->EditAttrs["class"] = "form-control";
			$this->Data_do_Estudo->EditCustomAttributes = "";
			$this->Data_do_Estudo->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->Data_do_Estudo->CurrentValue, 7));

			// Assunto
			$this->Assunto->EditAttrs["class"] = "form-control";
			$this->Assunto->EditCustomAttributes = "";
			$this->Assunto->EditValue = ew_HtmlEncode($this->Assunto->CurrentValue);

			// DescricaoMensagem
			$this->DescricaoMensagem->EditAttrs["class"] = "form-control";
			$this->DescricaoMensagem->EditCustomAttributes = "";
			$this->DescricaoMensagem->EditValue = ew_HtmlEncode($this->DescricaoMensagem->CurrentValue);

			// Anotacoes
			$this->Anotacoes->EditAttrs["class"] = "form-control";
			$this->Anotacoes->EditCustomAttributes = "";
			$this->Anotacoes->EditValue = ew_HtmlEncode($this->Anotacoes->CurrentValue);

			// Edit refer script
			// Numero_do_Estudo

			$this->Numero_do_Estudo->HrefValue = "";

			// Data_do_Estudo
			$this->Data_do_Estudo->HrefValue = "";

			// Assunto
			$this->Assunto->HrefValue = "";

			// DescricaoMensagem
			$this->DescricaoMensagem->HrefValue = "";

			// Anotacoes
			$this->Anotacoes->HrefValue = "";
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
		if (!$this->Numero_do_Estudo->FldIsDetailKey && !is_null($this->Numero_do_Estudo->FormValue) && $this->Numero_do_Estudo->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Numero_do_Estudo->FldCaption(), $this->Numero_do_Estudo->ReqErrMsg));
		}
		if (!ew_CheckInteger($this->Numero_do_Estudo->FormValue)) {
			ew_AddMessage($gsFormError, $this->Numero_do_Estudo->FldErrMsg());
		}
		if (!$this->Data_do_Estudo->FldIsDetailKey && !is_null($this->Data_do_Estudo->FormValue) && $this->Data_do_Estudo->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Data_do_Estudo->FldCaption(), $this->Data_do_Estudo->ReqErrMsg));
		}
		if (!ew_CheckEuroDate($this->Data_do_Estudo->FormValue)) {
			ew_AddMessage($gsFormError, $this->Data_do_Estudo->FldErrMsg());
		}
		if (!$this->DescricaoMensagem->FldIsDetailKey && !is_null($this->DescricaoMensagem->FormValue) && $this->DescricaoMensagem->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->DescricaoMensagem->FldCaption(), $this->DescricaoMensagem->ReqErrMsg));
		}
		if (!$this->Anotacoes->FldIsDetailKey && !is_null($this->Anotacoes->FormValue) && $this->Anotacoes->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Anotacoes->FldCaption(), $this->Anotacoes->ReqErrMsg));
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

	// Add record
	function AddRow($rsold = NULL) {
		global $conn, $Language, $Security;

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
		}
		$rsnew = array();

		// Numero_do_Estudo
		$this->Numero_do_Estudo->SetDbValueDef($rsnew, $this->Numero_do_Estudo->CurrentValue, NULL, FALSE);

		// Data_do_Estudo
		$this->Data_do_Estudo->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->Data_do_Estudo->CurrentValue, 7), NULL, FALSE);

		// Assunto
		$this->Assunto->SetDbValueDef($rsnew, $this->Assunto->CurrentValue, NULL, FALSE);

		// DescricaoMensagem
		$this->DescricaoMensagem->SetDbValueDef($rsnew, $this->DescricaoMensagem->CurrentValue, NULL, FALSE);

		// Anotacoes
		$this->Anotacoes->SetDbValueDef($rsnew, $this->Anotacoes->CurrentValue, NULL, FALSE);

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);
		if ($bInsertRow) {
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			$AddRow = $this->Insert($rsnew);
			$conn->raiseErrorFn = '';
			if ($AddRow) {
			}
		} else {
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("InsertCancelled"));
			}
			$AddRow = FALSE;
		}

		// Get insert id if necessary
		if ($AddRow) {
			$this->Id_estu_bb->setDbValue($conn->Insert_ID());
			$rsnew['Id_estu_bb'] = $this->Id_estu_bb->DbValue;
		}
		if ($AddRow) {

			// Call Row Inserted event
			$rs = ($rsold == NULL) ? NULL : $rsold->fields;
			$this->Row_Inserted($rs, $rsnew);
			$this->WriteAuditTrailOnAdd($rsnew);
		}
		return $AddRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, "estudos_biblicoslist.php", "", $this->TableVar, TRUE);
		$PageId = ($this->CurrentAction == "C") ? "Copy" : "Add";
		$Breadcrumb->Add("add", $PageId, $url);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'estudos_biblicos';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (add page)
	function WriteAuditTrailOnAdd(&$rs) {
		if (!$this->AuditTrailOnAdd) return;
		$table = 'estudos_biblicos';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['Id_estu_bb'];

		// Write Audit Trail
		$dt = ew_StdCurrentDateTime();
		$id = ew_ScriptName();
	  $usr = CurrentUserID();
		foreach (array_keys($rs) as $fldname) {
			if ($this->fields[$fldname]->FldDataType <> EW_DATATYPE_BLOB) { // Ignore BLOB fields
				if ($this->fields[$fldname]->FldDataType == EW_DATATYPE_MEMO) {
					if (EW_AUDIT_TRAIL_TO_DATABASE)
						$newvalue = $rs[$fldname];
					else
						$newvalue = "[MEMO]"; // Memo Field
				} elseif ($this->fields[$fldname]->FldDataType == EW_DATATYPE_XML) {
					$newvalue = "[XML]"; // XML Field
				} else {
					$newvalue = $rs[$fldname];
				}
				ew_WriteAuditTrail("log", $dt, $id, $usr, "A", $table, $fldname, $key, "", $newvalue);
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
if (!isset($estudos_biblicos_add)) $estudos_biblicos_add = new cestudos_biblicos_add();

// Page init
$estudos_biblicos_add->Page_Init();

// Page main
$estudos_biblicos_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$estudos_biblicos_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var estudos_biblicos_add = new ew_Page("estudos_biblicos_add");
estudos_biblicos_add.PageID = "add"; // Page ID
var EW_PAGE_ID = estudos_biblicos_add.PageID; // For backward compatibility

// Form object
var festudos_biblicosadd = new ew_Form("festudos_biblicosadd");

// Validate form
festudos_biblicosadd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_Numero_do_Estudo");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $estudos_biblicos->Numero_do_Estudo->FldCaption(), $estudos_biblicos->Numero_do_Estudo->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Numero_do_Estudo");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($estudos_biblicos->Numero_do_Estudo->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Data_do_Estudo");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $estudos_biblicos->Data_do_Estudo->FldCaption(), $estudos_biblicos->Data_do_Estudo->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Data_do_Estudo");
			if (elm && !ew_CheckEuroDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($estudos_biblicos->Data_do_Estudo->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_DescricaoMensagem");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $estudos_biblicos->DescricaoMensagem->FldCaption(), $estudos_biblicos->DescricaoMensagem->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Anotacoes");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $estudos_biblicos->Anotacoes->FldCaption(), $estudos_biblicos->Anotacoes->ReqErrMsg)) ?>");

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
festudos_biblicosadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
festudos_biblicosadd.ValidateRequired = true;
<?php } else { ?>
festudos_biblicosadd.ValidateRequired = false; 
<?php } ?>

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
<?php $estudos_biblicos_add->ShowPageHeader(); ?>
<?php
$estudos_biblicos_add->ShowMessage();
?>
<form name="festudos_biblicosadd" id="festudos_biblicosadd" class="form-horizontal ewForm ewAddForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($estudos_biblicos_add->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $estudos_biblicos_add->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="estudos_biblicos">
<input type="hidden" name="a_add" id="a_add" value="A">
<div>
<?php if ($estudos_biblicos->Numero_do_Estudo->Visible) { // Numero_do_Estudo ?>
	<div id="r_Numero_do_Estudo" class="form-group">
		<label id="elh_estudos_biblicos_Numero_do_Estudo" for="x_Numero_do_Estudo" class="col-sm-2 control-label ewLabel"><?php echo $estudos_biblicos->Numero_do_Estudo->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $estudos_biblicos->Numero_do_Estudo->CellAttributes() ?>>
<span id="el_estudos_biblicos_Numero_do_Estudo">
<input type="text" data-field="x_Numero_do_Estudo" name="x_Numero_do_Estudo" id="x_Numero_do_Estudo" size="7" value="<?php echo $estudos_biblicos->Numero_do_Estudo->EditValue ?>"<?php echo $estudos_biblicos->Numero_do_Estudo->EditAttributes() ?>>
</span>
<?php echo $estudos_biblicos->Numero_do_Estudo->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($estudos_biblicos->Data_do_Estudo->Visible) { // Data_do_Estudo ?>
	<div id="r_Data_do_Estudo" class="form-group">
		<label id="elh_estudos_biblicos_Data_do_Estudo" for="x_Data_do_Estudo" class="col-sm-2 control-label ewLabel"><?php echo $estudos_biblicos->Data_do_Estudo->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $estudos_biblicos->Data_do_Estudo->CellAttributes() ?>>
<span id="el_estudos_biblicos_Data_do_Estudo">
<input type="text" data-field="x_Data_do_Estudo" name="x_Data_do_Estudo" id="x_Data_do_Estudo" size="10" value="<?php echo $estudos_biblicos->Data_do_Estudo->EditValue ?>"<?php echo $estudos_biblicos->Data_do_Estudo->EditAttributes() ?>>
<?php if (!$estudos_biblicos->Data_do_Estudo->ReadOnly && !$estudos_biblicos->Data_do_Estudo->Disabled && @$estudos_biblicos->Data_do_Estudo->EditAttrs["readonly"] == "" && @$estudos_biblicos->Data_do_Estudo->EditAttrs["disabled"] == "") { ?>
<script type="text/javascript">
ew_CreateCalendar("festudos_biblicosadd", "x_Data_do_Estudo", "%d/%m/%Y");
</script>
<?php } ?>
</span>
<?php echo $estudos_biblicos->Data_do_Estudo->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($estudos_biblicos->Assunto->Visible) { // Assunto ?>
	<div id="r_Assunto" class="form-group">
		<label id="elh_estudos_biblicos_Assunto" for="x_Assunto" class="col-sm-2 control-label ewLabel"><?php echo $estudos_biblicos->Assunto->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $estudos_biblicos->Assunto->CellAttributes() ?>>
<span id="el_estudos_biblicos_Assunto">
<input type="text" data-field="x_Assunto" name="x_Assunto" id="x_Assunto" size="70" maxlength="100" value="<?php echo $estudos_biblicos->Assunto->EditValue ?>"<?php echo $estudos_biblicos->Assunto->EditAttributes() ?>>
</span>
<?php echo $estudos_biblicos->Assunto->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($estudos_biblicos->DescricaoMensagem->Visible) { // DescricaoMensagem ?>
	<div id="r_DescricaoMensagem" class="form-group">
		<label id="elh_estudos_biblicos_DescricaoMensagem" class="col-sm-2 control-label ewLabel"><?php echo $estudos_biblicos->DescricaoMensagem->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $estudos_biblicos->DescricaoMensagem->CellAttributes() ?>>
<span id="el_estudos_biblicos_DescricaoMensagem">
<textarea data-field="x_DescricaoMensagem" class="editor" name="x_DescricaoMensagem" id="x_DescricaoMensagem" cols="70" rows="5"<?php echo $estudos_biblicos->DescricaoMensagem->EditAttributes() ?>><?php echo $estudos_biblicos->DescricaoMensagem->EditValue ?></textarea>
<script type="text/javascript">
ew_CreateEditor("festudos_biblicosadd", "x_DescricaoMensagem", 70, 5, <?php echo ($estudos_biblicos->DescricaoMensagem->ReadOnly || FALSE) ? "true" : "false" ?>);
</script>
</span>
<?php echo $estudos_biblicos->DescricaoMensagem->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($estudos_biblicos->Anotacoes->Visible) { // Anotacoes ?>
	<div id="r_Anotacoes" class="form-group">
		<label id="elh_estudos_biblicos_Anotacoes" for="x_Anotacoes" class="col-sm-2 control-label ewLabel"><?php echo $estudos_biblicos->Anotacoes->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $estudos_biblicos->Anotacoes->CellAttributes() ?>>
<span id="el_estudos_biblicos_Anotacoes">
<textarea data-field="x_Anotacoes" name="x_Anotacoes" id="x_Anotacoes" cols="70" rows="3"<?php echo $estudos_biblicos->Anotacoes->EditAttributes() ?>><?php echo $estudos_biblicos->Anotacoes->EditValue ?></textarea>
</span>
<?php echo $estudos_biblicos->Anotacoes->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
<button class="btn btn-primary ewButton btn-success" name="btnAction" id="btnAction" type="submit"><i class="fa fa-floppy-o"></i>&nbsp;<?php echo $Language->Phrase("AddBtn") ?></button>
	</div>
</div>
</form>
<script type="text/javascript">
festudos_biblicosadd.Init();
</script>
<?php
$estudos_biblicos_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$estudos_biblicos_add->Page_Terminate();
?>
