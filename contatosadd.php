<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg11.php" ?>
<?php include_once "ewmysql11.php" ?>
<?php include_once "phpfn11.php" ?>
<?php include_once "contatosinfo.php" ?>
<?php include_once "usuariosinfo.php" ?>
<?php include_once "userfn11.php" ?>
<?php

//
// Page class
//

$contatos_add = NULL; // Initialize page object first

class ccontatos_add extends ccontatos {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{2B7992FC-5911-46A7-9310-01F4D4225C49}";

	// Table name
	var $TableName = 'contatos';

	// Page object name
	var $PageObjName = 'contatos_add';

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

		// Table object (contatos)
		if (!isset($GLOBALS["contatos"]) || get_class($GLOBALS["contatos"]) == "ccontatos") {
			$GLOBALS["contatos"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["contatos"];
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
			define("EW_TABLE_NAME", 'contatos', TRUE);

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
			$this->Page_Terminate(ew_GetUrl("contatoslist.php"));
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
		global $EW_EXPORT, $contatos;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($contatos);
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
			if (@$_GET["Id"] != "") {
				$this->Id->setQueryStringValue($_GET["Id"]);
				$this->setKey("Id", $this->Id->CurrentValue); // Set up key
			} else {
				$this->setKey("Id", ""); // Clear key
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
					$this->Page_Terminate("contatoslist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "contatosview.php")
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
		$this->Pessoa_Empresa->CurrentValue = NULL;
		$this->Pessoa_Empresa->OldValue = $this->Pessoa_Empresa->CurrentValue;
		$this->Telefone_1->CurrentValue = NULL;
		$this->Telefone_1->OldValue = $this->Telefone_1->CurrentValue;
		$this->Telefone_2->CurrentValue = NULL;
		$this->Telefone_2->OldValue = $this->Telefone_2->CurrentValue;
		$this->Celular_1->CurrentValue = NULL;
		$this->Celular_1->OldValue = $this->Celular_1->CurrentValue;
		$this->Celular_2->CurrentValue = NULL;
		$this->Celular_2->OldValue = $this->Celular_2->CurrentValue;
		$this->EnderecoCompleto->CurrentValue = NULL;
		$this->EnderecoCompleto->OldValue = $this->EnderecoCompleto->CurrentValue;
		$this->EmailPessoal->CurrentValue = NULL;
		$this->EmailPessoal->OldValue = $this->EmailPessoal->CurrentValue;
		$this->EmailComercial->CurrentValue = NULL;
		$this->EmailComercial->OldValue = $this->EmailComercial->CurrentValue;
		$this->Anotacoes->CurrentValue = NULL;
		$this->Anotacoes->OldValue = $this->Anotacoes->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->Pessoa_Empresa->FldIsDetailKey) {
			$this->Pessoa_Empresa->setFormValue($objForm->GetValue("x_Pessoa_Empresa"));
		}
		if (!$this->Telefone_1->FldIsDetailKey) {
			$this->Telefone_1->setFormValue($objForm->GetValue("x_Telefone_1"));
		}
		if (!$this->Telefone_2->FldIsDetailKey) {
			$this->Telefone_2->setFormValue($objForm->GetValue("x_Telefone_2"));
		}
		if (!$this->Celular_1->FldIsDetailKey) {
			$this->Celular_1->setFormValue($objForm->GetValue("x_Celular_1"));
		}
		if (!$this->Celular_2->FldIsDetailKey) {
			$this->Celular_2->setFormValue($objForm->GetValue("x_Celular_2"));
		}
		if (!$this->EnderecoCompleto->FldIsDetailKey) {
			$this->EnderecoCompleto->setFormValue($objForm->GetValue("x_EnderecoCompleto"));
		}
		if (!$this->EmailPessoal->FldIsDetailKey) {
			$this->EmailPessoal->setFormValue($objForm->GetValue("x_EmailPessoal"));
		}
		if (!$this->EmailComercial->FldIsDetailKey) {
			$this->EmailComercial->setFormValue($objForm->GetValue("x_EmailComercial"));
		}
		if (!$this->Anotacoes->FldIsDetailKey) {
			$this->Anotacoes->setFormValue($objForm->GetValue("x_Anotacoes"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->Pessoa_Empresa->CurrentValue = $this->Pessoa_Empresa->FormValue;
		$this->Telefone_1->CurrentValue = $this->Telefone_1->FormValue;
		$this->Telefone_2->CurrentValue = $this->Telefone_2->FormValue;
		$this->Celular_1->CurrentValue = $this->Celular_1->FormValue;
		$this->Celular_2->CurrentValue = $this->Celular_2->FormValue;
		$this->EnderecoCompleto->CurrentValue = $this->EnderecoCompleto->FormValue;
		$this->EmailPessoal->CurrentValue = $this->EmailPessoal->FormValue;
		$this->EmailComercial->CurrentValue = $this->EmailComercial->FormValue;
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
		$this->Id->setDbValue($rs->fields('Id'));
		$this->Pessoa_Empresa->setDbValue($rs->fields('Pessoa_Empresa'));
		$this->Telefone_1->setDbValue($rs->fields('Telefone_1'));
		$this->Telefone_2->setDbValue($rs->fields('Telefone_2'));
		$this->Celular_1->setDbValue($rs->fields('Celular_1'));
		$this->Celular_2->setDbValue($rs->fields('Celular_2'));
		$this->EnderecoCompleto->setDbValue($rs->fields('EnderecoCompleto'));
		$this->EmailPessoal->setDbValue($rs->fields('EmailPessoal'));
		$this->EmailComercial->setDbValue($rs->fields('EmailComercial'));
		$this->Anotacoes->setDbValue($rs->fields('Anotacoes'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->Id->DbValue = $row['Id'];
		$this->Pessoa_Empresa->DbValue = $row['Pessoa_Empresa'];
		$this->Telefone_1->DbValue = $row['Telefone_1'];
		$this->Telefone_2->DbValue = $row['Telefone_2'];
		$this->Celular_1->DbValue = $row['Celular_1'];
		$this->Celular_2->DbValue = $row['Celular_2'];
		$this->EnderecoCompleto->DbValue = $row['EnderecoCompleto'];
		$this->EmailPessoal->DbValue = $row['EmailPessoal'];
		$this->EmailComercial->DbValue = $row['EmailComercial'];
		$this->Anotacoes->DbValue = $row['Anotacoes'];
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
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// Id
		// Pessoa_Empresa
		// Telefone_1
		// Telefone_2
		// Celular_1
		// Celular_2
		// EnderecoCompleto
		// EmailPessoal
		// EmailComercial
		// Anotacoes

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// Pessoa_Empresa
			$this->Pessoa_Empresa->ViewValue = $this->Pessoa_Empresa->CurrentValue;
			$this->Pessoa_Empresa->ViewCustomAttributes = "";

			// Telefone_1
			$this->Telefone_1->ViewValue = $this->Telefone_1->CurrentValue;
			$this->Telefone_1->ViewCustomAttributes = "";

			// Telefone_2
			$this->Telefone_2->ViewValue = $this->Telefone_2->CurrentValue;
			$this->Telefone_2->ViewCustomAttributes = "";

			// Celular_1
			$this->Celular_1->ViewValue = $this->Celular_1->CurrentValue;
			$this->Celular_1->ViewCustomAttributes = "";

			// Celular_2
			$this->Celular_2->ViewValue = $this->Celular_2->CurrentValue;
			$this->Celular_2->ViewCustomAttributes = "";

			// EnderecoCompleto
			$this->EnderecoCompleto->ViewValue = $this->EnderecoCompleto->CurrentValue;
			if (!is_null($this->EnderecoCompleto->ViewValue)) $this->EnderecoCompleto->ViewValue = str_replace("\n", "<br>", $this->EnderecoCompleto->ViewValue); 
			$this->EnderecoCompleto->ViewCustomAttributes = "";

			// EmailPessoal
			$this->EmailPessoal->ViewValue = $this->EmailPessoal->CurrentValue;
			$this->EmailPessoal->ViewCustomAttributes = "";

			// EmailComercial
			$this->EmailComercial->ViewValue = $this->EmailComercial->CurrentValue;
			$this->EmailComercial->ViewCustomAttributes = "";

			// Anotacoes
			$this->Anotacoes->ViewValue = $this->Anotacoes->CurrentValue;
			if (!is_null($this->Anotacoes->ViewValue)) $this->Anotacoes->ViewValue = str_replace("\n", "<br>", $this->Anotacoes->ViewValue); 
			$this->Anotacoes->ViewCustomAttributes = "";

			// Pessoa_Empresa
			$this->Pessoa_Empresa->LinkCustomAttributes = "";
			$this->Pessoa_Empresa->HrefValue = "";
			$this->Pessoa_Empresa->TooltipValue = "";

			// Telefone_1
			$this->Telefone_1->LinkCustomAttributes = "";
			$this->Telefone_1->HrefValue = "";
			$this->Telefone_1->TooltipValue = "";

			// Telefone_2
			$this->Telefone_2->LinkCustomAttributes = "";
			$this->Telefone_2->HrefValue = "";
			$this->Telefone_2->TooltipValue = "";

			// Celular_1
			$this->Celular_1->LinkCustomAttributes = "";
			$this->Celular_1->HrefValue = "";
			$this->Celular_1->TooltipValue = "";

			// Celular_2
			$this->Celular_2->LinkCustomAttributes = "";
			$this->Celular_2->HrefValue = "";
			$this->Celular_2->TooltipValue = "";

			// EnderecoCompleto
			$this->EnderecoCompleto->LinkCustomAttributes = "";
			$this->EnderecoCompleto->HrefValue = "";
			$this->EnderecoCompleto->TooltipValue = "";

			// EmailPessoal
			$this->EmailPessoal->LinkCustomAttributes = "";
			$this->EmailPessoal->HrefValue = "";
			$this->EmailPessoal->TooltipValue = "";

			// EmailComercial
			$this->EmailComercial->LinkCustomAttributes = "";
			$this->EmailComercial->HrefValue = "";
			$this->EmailComercial->TooltipValue = "";

			// Anotacoes
			$this->Anotacoes->LinkCustomAttributes = "";
			$this->Anotacoes->HrefValue = "";
			$this->Anotacoes->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// Pessoa_Empresa
			$this->Pessoa_Empresa->EditAttrs["class"] = "form-control";
			$this->Pessoa_Empresa->EditCustomAttributes = "";
			$this->Pessoa_Empresa->EditValue = ew_HtmlEncode($this->Pessoa_Empresa->CurrentValue);

			// Telefone_1
			$this->Telefone_1->EditAttrs["class"] = "form-control";
			$this->Telefone_1->EditCustomAttributes = "";
			$this->Telefone_1->EditValue = ew_HtmlEncode($this->Telefone_1->CurrentValue);

			// Telefone_2
			$this->Telefone_2->EditAttrs["class"] = "form-control";
			$this->Telefone_2->EditCustomAttributes = "";
			$this->Telefone_2->EditValue = ew_HtmlEncode($this->Telefone_2->CurrentValue);

			// Celular_1
			$this->Celular_1->EditAttrs["class"] = "form-control";
			$this->Celular_1->EditCustomAttributes = "";
			$this->Celular_1->EditValue = ew_HtmlEncode($this->Celular_1->CurrentValue);

			// Celular_2
			$this->Celular_2->EditAttrs["class"] = "form-control";
			$this->Celular_2->EditCustomAttributes = "";
			$this->Celular_2->EditValue = ew_HtmlEncode($this->Celular_2->CurrentValue);

			// EnderecoCompleto
			$this->EnderecoCompleto->EditAttrs["class"] = "form-control";
			$this->EnderecoCompleto->EditCustomAttributes = "";
			$this->EnderecoCompleto->EditValue = ew_HtmlEncode($this->EnderecoCompleto->CurrentValue);

			// EmailPessoal
			$this->EmailPessoal->EditAttrs["class"] = "form-control";
			$this->EmailPessoal->EditCustomAttributes = "";
			$this->EmailPessoal->EditValue = ew_HtmlEncode($this->EmailPessoal->CurrentValue);

			// EmailComercial
			$this->EmailComercial->EditAttrs["class"] = "form-control";
			$this->EmailComercial->EditCustomAttributes = "";
			$this->EmailComercial->EditValue = ew_HtmlEncode($this->EmailComercial->CurrentValue);

			// Anotacoes
			$this->Anotacoes->EditAttrs["class"] = "form-control";
			$this->Anotacoes->EditCustomAttributes = "";
			$this->Anotacoes->EditValue = ew_HtmlEncode($this->Anotacoes->CurrentValue);

			// Edit refer script
			// Pessoa_Empresa

			$this->Pessoa_Empresa->HrefValue = "";

			// Telefone_1
			$this->Telefone_1->HrefValue = "";

			// Telefone_2
			$this->Telefone_2->HrefValue = "";

			// Celular_1
			$this->Celular_1->HrefValue = "";

			// Celular_2
			$this->Celular_2->HrefValue = "";

			// EnderecoCompleto
			$this->EnderecoCompleto->HrefValue = "";

			// EmailPessoal
			$this->EmailPessoal->HrefValue = "";

			// EmailComercial
			$this->EmailComercial->HrefValue = "";

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
		if (!$this->Pessoa_Empresa->FldIsDetailKey && !is_null($this->Pessoa_Empresa->FormValue) && $this->Pessoa_Empresa->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Pessoa_Empresa->FldCaption(), $this->Pessoa_Empresa->ReqErrMsg));
		}
		if (!$this->Telefone_1->FldIsDetailKey && !is_null($this->Telefone_1->FormValue) && $this->Telefone_1->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Telefone_1->FldCaption(), $this->Telefone_1->ReqErrMsg));
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

		// Pessoa_Empresa
		$this->Pessoa_Empresa->SetDbValueDef($rsnew, $this->Pessoa_Empresa->CurrentValue, NULL, FALSE);

		// Telefone_1
		$this->Telefone_1->SetDbValueDef($rsnew, $this->Telefone_1->CurrentValue, "", FALSE);

		// Telefone_2
		$this->Telefone_2->SetDbValueDef($rsnew, $this->Telefone_2->CurrentValue, NULL, FALSE);

		// Celular_1
		$this->Celular_1->SetDbValueDef($rsnew, $this->Celular_1->CurrentValue, NULL, FALSE);

		// Celular_2
		$this->Celular_2->SetDbValueDef($rsnew, $this->Celular_2->CurrentValue, NULL, FALSE);

		// EnderecoCompleto
		$this->EnderecoCompleto->SetDbValueDef($rsnew, $this->EnderecoCompleto->CurrentValue, NULL, FALSE);

		// EmailPessoal
		$this->EmailPessoal->SetDbValueDef($rsnew, $this->EmailPessoal->CurrentValue, NULL, FALSE);

		// EmailComercial
		$this->EmailComercial->SetDbValueDef($rsnew, $this->EmailComercial->CurrentValue, NULL, FALSE);

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
			$this->Id->setDbValue($conn->Insert_ID());
			$rsnew['Id'] = $this->Id->DbValue;
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
		$Breadcrumb->Add("list", $this->TableVar, "contatoslist.php", "", $this->TableVar, TRUE);
		$PageId = ($this->CurrentAction == "C") ? "Copy" : "Add";
		$Breadcrumb->Add("add", $PageId, $url);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'contatos';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (add page)
	function WriteAuditTrailOnAdd(&$rs) {
		if (!$this->AuditTrailOnAdd) return;
		$table = 'contatos';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['Id'];

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
if (!isset($contatos_add)) $contatos_add = new ccontatos_add();

// Page init
$contatos_add->Page_Init();

// Page main
$contatos_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$contatos_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var contatos_add = new ew_Page("contatos_add");
contatos_add.PageID = "add"; // Page ID
var EW_PAGE_ID = contatos_add.PageID; // For backward compatibility

// Form object
var fcontatosadd = new ew_Form("fcontatosadd");

// Validate form
fcontatosadd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_Pessoa_Empresa");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $contatos->Pessoa_Empresa->FldCaption(), $contatos->Pessoa_Empresa->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Telefone_1");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $contatos->Telefone_1->FldCaption(), $contatos->Telefone_1->ReqErrMsg)) ?>");

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
fcontatosadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fcontatosadd.ValidateRequired = true;
<?php } else { ?>
fcontatosadd.ValidateRequired = false; 
<?php } ?>

// Multi-Page properties
fcontatosadd.MultiPage = new ew_MultiPage("fcontatosadd",
	[["x_Pessoa_Empresa",1],["x_Telefone_1",1],["x_Telefone_2",1],["x_Celular_1",1],["x_Celular_2",1],["x_EnderecoCompleto",2],["x_EmailPessoal",2],["x_EmailComercial",2],["x_Anotacoes",2]]
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
<?php $contatos_add->ShowPageHeader(); ?>
<?php
$contatos_add->ShowMessage();
?>
<form name="fcontatosadd" id="fcontatosadd" class="form-horizontal ewForm ewAddForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($contatos_add->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $contatos_add->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="contatos">
<input type="hidden" name="a_add" id="a_add" value="A">
<div>
<div class="tabbable" id="contatos_add">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab_contatos1" data-toggle="tab"><?php echo $contatos->PageCaption(1) ?></a></li>
		<li><a href="#tab_contatos2" data-toggle="tab"><?php echo $contatos->PageCaption(2) ?></a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="tab_contatos1">
<div>
<?php if ($contatos->Pessoa_Empresa->Visible) { // Pessoa_Empresa ?>
	<div id="r_Pessoa_Empresa" class="form-group">
		<label id="elh_contatos_Pessoa_Empresa" for="x_Pessoa_Empresa" class="col-sm-2 control-label ewLabel"><?php echo $contatos->Pessoa_Empresa->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $contatos->Pessoa_Empresa->CellAttributes() ?>>
<span id="el_contatos_Pessoa_Empresa">
<input type="text" data-field="x_Pessoa_Empresa" name="x_Pessoa_Empresa" id="x_Pessoa_Empresa" size="60" maxlength="70" value="<?php echo $contatos->Pessoa_Empresa->EditValue ?>"<?php echo $contatos->Pessoa_Empresa->EditAttributes() ?>>
</span>
<?php echo $contatos->Pessoa_Empresa->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($contatos->Telefone_1->Visible) { // Telefone_1 ?>
	<div id="r_Telefone_1" class="form-group">
		<label id="elh_contatos_Telefone_1" for="x_Telefone_1" class="col-sm-2 control-label ewLabel"><?php echo $contatos->Telefone_1->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $contatos->Telefone_1->CellAttributes() ?>>
<span id="el_contatos_Telefone_1">
<input type="text" data-field="x_Telefone_1" name="x_Telefone_1" id="x_Telefone_1" size="30" maxlength="25" value="<?php echo $contatos->Telefone_1->EditValue ?>"<?php echo $contatos->Telefone_1->EditAttributes() ?>>
</span>
<?php echo $contatos->Telefone_1->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($contatos->Telefone_2->Visible) { // Telefone_2 ?>
	<div id="r_Telefone_2" class="form-group">
		<label id="elh_contatos_Telefone_2" for="x_Telefone_2" class="col-sm-2 control-label ewLabel"><?php echo $contatos->Telefone_2->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $contatos->Telefone_2->CellAttributes() ?>>
<span id="el_contatos_Telefone_2">
<input type="text" data-field="x_Telefone_2" name="x_Telefone_2" id="x_Telefone_2" size="30" maxlength="25" value="<?php echo $contatos->Telefone_2->EditValue ?>"<?php echo $contatos->Telefone_2->EditAttributes() ?>>
</span>
<?php echo $contatos->Telefone_2->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($contatos->Celular_1->Visible) { // Celular_1 ?>
	<div id="r_Celular_1" class="form-group">
		<label id="elh_contatos_Celular_1" for="x_Celular_1" class="col-sm-2 control-label ewLabel"><?php echo $contatos->Celular_1->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $contatos->Celular_1->CellAttributes() ?>>
<span id="el_contatos_Celular_1">
<input type="text" data-field="x_Celular_1" name="x_Celular_1" id="x_Celular_1" size="30" maxlength="25" value="<?php echo $contatos->Celular_1->EditValue ?>"<?php echo $contatos->Celular_1->EditAttributes() ?>>
</span>
<?php echo $contatos->Celular_1->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($contatos->Celular_2->Visible) { // Celular_2 ?>
	<div id="r_Celular_2" class="form-group">
		<label id="elh_contatos_Celular_2" for="x_Celular_2" class="col-sm-2 control-label ewLabel"><?php echo $contatos->Celular_2->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $contatos->Celular_2->CellAttributes() ?>>
<span id="el_contatos_Celular_2">
<input type="text" data-field="x_Celular_2" name="x_Celular_2" id="x_Celular_2" size="30" maxlength="25" value="<?php echo $contatos->Celular_2->EditValue ?>"<?php echo $contatos->Celular_2->EditAttributes() ?>>
</span>
<?php echo $contatos->Celular_2->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
		</div>
		<div class="tab-pane" id="tab_contatos2">
<div>
<?php if ($contatos->EnderecoCompleto->Visible) { // EnderecoCompleto ?>
	<div id="r_EnderecoCompleto" class="form-group">
		<label id="elh_contatos_EnderecoCompleto" for="x_EnderecoCompleto" class="col-sm-2 control-label ewLabel"><?php echo $contatos->EnderecoCompleto->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $contatos->EnderecoCompleto->CellAttributes() ?>>
<span id="el_contatos_EnderecoCompleto">
<textarea data-field="x_EnderecoCompleto" name="x_EnderecoCompleto" id="x_EnderecoCompleto" cols="70" rows="3"<?php echo $contatos->EnderecoCompleto->EditAttributes() ?>><?php echo $contatos->EnderecoCompleto->EditValue ?></textarea>
</span>
<?php echo $contatos->EnderecoCompleto->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($contatos->EmailPessoal->Visible) { // EmailPessoal ?>
	<div id="r_EmailPessoal" class="form-group">
		<label id="elh_contatos_EmailPessoal" for="x_EmailPessoal" class="col-sm-2 control-label ewLabel"><?php echo $contatos->EmailPessoal->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $contatos->EmailPessoal->CellAttributes() ?>>
<span id="el_contatos_EmailPessoal">
<input type="text" data-field="x_EmailPessoal" name="x_EmailPessoal" id="x_EmailPessoal" size="30" maxlength="65" value="<?php echo $contatos->EmailPessoal->EditValue ?>"<?php echo $contatos->EmailPessoal->EditAttributes() ?>>
</span>
<?php echo $contatos->EmailPessoal->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($contatos->EmailComercial->Visible) { // EmailComercial ?>
	<div id="r_EmailComercial" class="form-group">
		<label id="elh_contatos_EmailComercial" for="x_EmailComercial" class="col-sm-2 control-label ewLabel"><?php echo $contatos->EmailComercial->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $contatos->EmailComercial->CellAttributes() ?>>
<span id="el_contatos_EmailComercial">
<input type="text" data-field="x_EmailComercial" name="x_EmailComercial" id="x_EmailComercial" size="30" maxlength="65" value="<?php echo $contatos->EmailComercial->EditValue ?>"<?php echo $contatos->EmailComercial->EditAttributes() ?>>
</span>
<?php echo $contatos->EmailComercial->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($contatos->Anotacoes->Visible) { // Anotacoes ?>
	<div id="r_Anotacoes" class="form-group">
		<label id="elh_contatos_Anotacoes" for="x_Anotacoes" class="col-sm-2 control-label ewLabel"><?php echo $contatos->Anotacoes->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $contatos->Anotacoes->CellAttributes() ?>>
<span id="el_contatos_Anotacoes">
<textarea data-field="x_Anotacoes" name="x_Anotacoes" id="x_Anotacoes" cols="70" rows="4"<?php echo $contatos->Anotacoes->EditAttributes() ?>><?php echo $contatos->Anotacoes->EditValue ?></textarea>
</span>
<?php echo $contatos->Anotacoes->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
		</div>
	</div>
</div>
</div>
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
<button class="btn btn-primary ewButton btn-success" name="btnAction" id="btnAction" type="submit"><i class="fa fa-floppy-o"></i>&nbsp;<?php echo $Language->Phrase("AddBtn") ?></button>
	</div>
</div>
</form>
<script type="text/javascript">
fcontatosadd.Init();
</script>
<?php
$contatos_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$contatos_add->Page_Terminate();
?>
