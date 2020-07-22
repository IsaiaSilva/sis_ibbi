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

$bens_patrimoniais_add = NULL; // Initialize page object first

class cbens_patrimoniais_add extends cbens_patrimoniais {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{2B7992FC-5911-46A7-9310-01F4D4225C49}";

	// Table name
	var $TableName = 'bens_patrimoniais';

	// Page object name
	var $PageObjName = 'bens_patrimoniais_add';

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

		// Table object (bens_patrimoniais)
		if (!isset($GLOBALS["bens_patrimoniais"]) || get_class($GLOBALS["bens_patrimoniais"]) == "cbens_patrimoniais") {
			$GLOBALS["bens_patrimoniais"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["bens_patrimoniais"];
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
			define("EW_TABLE_NAME", 'bens_patrimoniais', TRUE);

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
			$this->Page_Terminate(ew_GetUrl("bens_patrimoniaislist.php"));
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
			if (@$_GET["Id_Patri"] != "") {
				$this->Id_Patri->setQueryStringValue($_GET["Id_Patri"]);
				$this->setKey("Id_Patri", $this->Id_Patri->CurrentValue); // Set up key
			} else {
				$this->setKey("Id_Patri", ""); // Clear key
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
					$this->Page_Terminate("bens_patrimoniaislist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "bens_patrimoniaisview.php")
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
		$this->Localidade->CurrentValue = NULL;
		$this->Localidade->OldValue = $this->Localidade->CurrentValue;
		$this->Descricao->CurrentValue = NULL;
		$this->Descricao->OldValue = $this->Descricao->CurrentValue;
		$this->DataAquisao->CurrentValue = NULL;
		$this->DataAquisao->OldValue = $this->DataAquisao->CurrentValue;
		$this->Tipo->CurrentValue = NULL;
		$this->Tipo->OldValue = $this->Tipo->CurrentValue;
		$this->Estado_do_bem->CurrentValue = NULL;
		$this->Estado_do_bem->OldValue = $this->Estado_do_bem->CurrentValue;
		$this->Valor_estimado->CurrentValue = NULL;
		$this->Valor_estimado->OldValue = $this->Valor_estimado->CurrentValue;
		$this->Situacao->CurrentValue = NULL;
		$this->Situacao->OldValue = $this->Situacao->CurrentValue;
		$this->Anotacoes->CurrentValue = NULL;
		$this->Anotacoes->OldValue = $this->Anotacoes->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->Localidade->FldIsDetailKey) {
			$this->Localidade->setFormValue($objForm->GetValue("x_Localidade"));
		}
		if (!$this->Descricao->FldIsDetailKey) {
			$this->Descricao->setFormValue($objForm->GetValue("x_Descricao"));
		}
		if (!$this->DataAquisao->FldIsDetailKey) {
			$this->DataAquisao->setFormValue($objForm->GetValue("x_DataAquisao"));
			$this->DataAquisao->CurrentValue = ew_UnFormatDateTime($this->DataAquisao->CurrentValue, 7);
		}
		if (!$this->Tipo->FldIsDetailKey) {
			$this->Tipo->setFormValue($objForm->GetValue("x_Tipo"));
		}
		if (!$this->Estado_do_bem->FldIsDetailKey) {
			$this->Estado_do_bem->setFormValue($objForm->GetValue("x_Estado_do_bem"));
		}
		if (!$this->Valor_estimado->FldIsDetailKey) {
			$this->Valor_estimado->setFormValue($objForm->GetValue("x_Valor_estimado"));
		}
		if (!$this->Situacao->FldIsDetailKey) {
			$this->Situacao->setFormValue($objForm->GetValue("x_Situacao"));
		}
		if (!$this->Anotacoes->FldIsDetailKey) {
			$this->Anotacoes->setFormValue($objForm->GetValue("x_Anotacoes"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->Localidade->CurrentValue = $this->Localidade->FormValue;
		$this->Descricao->CurrentValue = $this->Descricao->FormValue;
		$this->DataAquisao->CurrentValue = $this->DataAquisao->FormValue;
		$this->DataAquisao->CurrentValue = ew_UnFormatDateTime($this->DataAquisao->CurrentValue, 7);
		$this->Tipo->CurrentValue = $this->Tipo->FormValue;
		$this->Estado_do_bem->CurrentValue = $this->Estado_do_bem->FormValue;
		$this->Valor_estimado->CurrentValue = $this->Valor_estimado->FormValue;
		$this->Situacao->CurrentValue = $this->Situacao->FormValue;
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
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// Localidade
			$this->Localidade->EditAttrs["class"] = "form-control";
			$this->Localidade->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `Id_igreja`, `Igreja` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `igrejas`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Localidade, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `Igreja` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->Localidade->EditValue = $arwrk;

			// Descricao
			$this->Descricao->EditAttrs["class"] = "form-control";
			$this->Descricao->EditCustomAttributes = "";
			$this->Descricao->EditValue = ew_HtmlEncode($this->Descricao->CurrentValue);

			// DataAquisao
			$this->DataAquisao->EditAttrs["class"] = "form-control";
			$this->DataAquisao->EditCustomAttributes = "";
			$this->DataAquisao->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->DataAquisao->CurrentValue, 7));

			// Tipo
			$this->Tipo->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->Tipo->FldTagValue(1), $this->Tipo->FldTagCaption(1) <> "" ? $this->Tipo->FldTagCaption(1) : $this->Tipo->FldTagValue(1));
			$arwrk[] = array($this->Tipo->FldTagValue(2), $this->Tipo->FldTagCaption(2) <> "" ? $this->Tipo->FldTagCaption(2) : $this->Tipo->FldTagValue(2));
			$this->Tipo->EditValue = $arwrk;

			// Estado_do_bem
			$this->Estado_do_bem->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `Id_est_patri`, `Estado_do_Bem` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `estado_patrimonio`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Estado_do_bem, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Estado_do_bem->EditValue = $arwrk;

			// Valor_estimado
			$this->Valor_estimado->EditAttrs["class"] = "form-control";
			$this->Valor_estimado->EditCustomAttributes = "";
			$this->Valor_estimado->EditValue = ew_HtmlEncode($this->Valor_estimado->CurrentValue);
			if (strval($this->Valor_estimado->EditValue) <> "" && is_numeric($this->Valor_estimado->EditValue)) $this->Valor_estimado->EditValue = ew_FormatNumber($this->Valor_estimado->EditValue, -2, -2, -2, -2);

			// Situacao
			$this->Situacao->EditAttrs["class"] = "form-control";
			$this->Situacao->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->Situacao->FldTagValue(1), $this->Situacao->FldTagCaption(1) <> "" ? $this->Situacao->FldTagCaption(1) : $this->Situacao->FldTagValue(1));
			$arwrk[] = array($this->Situacao->FldTagValue(2), $this->Situacao->FldTagCaption(2) <> "" ? $this->Situacao->FldTagCaption(2) : $this->Situacao->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->Situacao->EditValue = $arwrk;

			// Anotacoes
			$this->Anotacoes->EditAttrs["class"] = "form-control";
			$this->Anotacoes->EditCustomAttributes = "";
			$this->Anotacoes->EditValue = ew_HtmlEncode($this->Anotacoes->CurrentValue);

			// Edit refer script
			// Localidade

			$this->Localidade->HrefValue = "";

			// Descricao
			$this->Descricao->HrefValue = "";

			// DataAquisao
			$this->DataAquisao->HrefValue = "";

			// Tipo
			$this->Tipo->HrefValue = "";

			// Estado_do_bem
			$this->Estado_do_bem->HrefValue = "";

			// Valor_estimado
			$this->Valor_estimado->HrefValue = "";

			// Situacao
			$this->Situacao->HrefValue = "";

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
		if (!$this->Localidade->FldIsDetailKey && !is_null($this->Localidade->FormValue) && $this->Localidade->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Localidade->FldCaption(), $this->Localidade->ReqErrMsg));
		}
		if (!$this->Descricao->FldIsDetailKey && !is_null($this->Descricao->FormValue) && $this->Descricao->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Descricao->FldCaption(), $this->Descricao->ReqErrMsg));
		}
		if (!$this->DataAquisao->FldIsDetailKey && !is_null($this->DataAquisao->FormValue) && $this->DataAquisao->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->DataAquisao->FldCaption(), $this->DataAquisao->ReqErrMsg));
		}
		if (!ew_CheckEuroDate($this->DataAquisao->FormValue)) {
			ew_AddMessage($gsFormError, $this->DataAquisao->FldErrMsg());
		}
		if ($this->Tipo->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Tipo->FldCaption(), $this->Tipo->ReqErrMsg));
		}
		if ($this->Estado_do_bem->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Estado_do_bem->FldCaption(), $this->Estado_do_bem->ReqErrMsg));
		}
		if (!$this->Valor_estimado->FldIsDetailKey && !is_null($this->Valor_estimado->FormValue) && $this->Valor_estimado->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Valor_estimado->FldCaption(), $this->Valor_estimado->ReqErrMsg));
		}
		if (!ew_CheckNumber($this->Valor_estimado->FormValue)) {
			ew_AddMessage($gsFormError, $this->Valor_estimado->FldErrMsg());
		}
		if (!$this->Situacao->FldIsDetailKey && !is_null($this->Situacao->FormValue) && $this->Situacao->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Situacao->FldCaption(), $this->Situacao->ReqErrMsg));
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

		// Localidade
		$this->Localidade->SetDbValueDef($rsnew, $this->Localidade->CurrentValue, NULL, FALSE);

		// Descricao
		$this->Descricao->SetDbValueDef($rsnew, $this->Descricao->CurrentValue, NULL, FALSE);

		// DataAquisao
		$this->DataAquisao->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->DataAquisao->CurrentValue, 7), NULL, FALSE);

		// Tipo
		$this->Tipo->SetDbValueDef($rsnew, $this->Tipo->CurrentValue, NULL, FALSE);

		// Estado_do_bem
		$this->Estado_do_bem->SetDbValueDef($rsnew, $this->Estado_do_bem->CurrentValue, NULL, FALSE);

		// Valor_estimado
		$this->Valor_estimado->SetDbValueDef($rsnew, $this->Valor_estimado->CurrentValue, NULL, FALSE);

		// Situacao
		$this->Situacao->SetDbValueDef($rsnew, $this->Situacao->CurrentValue, NULL, FALSE);

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
			$this->Id_Patri->setDbValue($conn->Insert_ID());
			$rsnew['Id_Patri'] = $this->Id_Patri->DbValue;
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
		$Breadcrumb->Add("list", $this->TableVar, "bens_patrimoniaislist.php", "", $this->TableVar, TRUE);
		$PageId = ($this->CurrentAction == "C") ? "Copy" : "Add";
		$Breadcrumb->Add("add", $PageId, $url);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'bens_patrimoniais';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (add page)
	function WriteAuditTrailOnAdd(&$rs) {
		if (!$this->AuditTrailOnAdd) return;
		$table = 'bens_patrimoniais';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['Id_Patri'];

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
if (!isset($bens_patrimoniais_add)) $bens_patrimoniais_add = new cbens_patrimoniais_add();

// Page init
$bens_patrimoniais_add->Page_Init();

// Page main
$bens_patrimoniais_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$bens_patrimoniais_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var bens_patrimoniais_add = new ew_Page("bens_patrimoniais_add");
bens_patrimoniais_add.PageID = "add"; // Page ID
var EW_PAGE_ID = bens_patrimoniais_add.PageID; // For backward compatibility

// Form object
var fbens_patrimoniaisadd = new ew_Form("fbens_patrimoniaisadd");

// Validate form
fbens_patrimoniaisadd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_Localidade");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $bens_patrimoniais->Localidade->FldCaption(), $bens_patrimoniais->Localidade->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Descricao");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $bens_patrimoniais->Descricao->FldCaption(), $bens_patrimoniais->Descricao->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_DataAquisao");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $bens_patrimoniais->DataAquisao->FldCaption(), $bens_patrimoniais->DataAquisao->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_DataAquisao");
			if (elm && !ew_CheckEuroDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($bens_patrimoniais->DataAquisao->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Tipo");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $bens_patrimoniais->Tipo->FldCaption(), $bens_patrimoniais->Tipo->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Estado_do_bem");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $bens_patrimoniais->Estado_do_bem->FldCaption(), $bens_patrimoniais->Estado_do_bem->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Valor_estimado");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $bens_patrimoniais->Valor_estimado->FldCaption(), $bens_patrimoniais->Valor_estimado->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Valor_estimado");
			if (elm && !ew_CheckNumber(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($bens_patrimoniais->Valor_estimado->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Situacao");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $bens_patrimoniais->Situacao->FldCaption(), $bens_patrimoniais->Situacao->ReqErrMsg)) ?>");

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
fbens_patrimoniaisadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fbens_patrimoniaisadd.ValidateRequired = true;
<?php } else { ?>
fbens_patrimoniaisadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fbens_patrimoniaisadd.Lists["x_Localidade"] = {"LinkField":"x_Id_igreja","Ajax":null,"AutoFill":false,"DisplayFields":["x_Igreja","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fbens_patrimoniaisadd.Lists["x_Estado_do_bem"] = {"LinkField":"x_Id_est_patri","Ajax":null,"AutoFill":false,"DisplayFields":["x_Estado_do_Bem","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

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
<?php $bens_patrimoniais_add->ShowPageHeader(); ?>
<?php
$bens_patrimoniais_add->ShowMessage();
?>
<form name="fbens_patrimoniaisadd" id="fbens_patrimoniaisadd" class="form-horizontal ewForm ewAddForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($bens_patrimoniais_add->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $bens_patrimoniais_add->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="bens_patrimoniais">
<input type="hidden" name="a_add" id="a_add" value="A">
<div>
<?php if ($bens_patrimoniais->Localidade->Visible) { // Localidade ?>
	<div id="r_Localidade" class="form-group">
		<label id="elh_bens_patrimoniais_Localidade" for="x_Localidade" class="col-sm-2 control-label ewLabel"><?php echo $bens_patrimoniais->Localidade->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $bens_patrimoniais->Localidade->CellAttributes() ?>>
<span id="el_bens_patrimoniais_Localidade">
<select data-field="x_Localidade" id="x_Localidade" name="x_Localidade"<?php echo $bens_patrimoniais->Localidade->EditAttributes() ?>>
<?php
if (is_array($bens_patrimoniais->Localidade->EditValue)) {
	$arwrk = $bens_patrimoniais->Localidade->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($bens_patrimoniais->Localidade->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
fbens_patrimoniaisadd.Lists["x_Localidade"].Options = <?php echo (is_array($bens_patrimoniais->Localidade->EditValue)) ? ew_ArrayToJson($bens_patrimoniais->Localidade->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $bens_patrimoniais->Localidade->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($bens_patrimoniais->Descricao->Visible) { // Descricao ?>
	<div id="r_Descricao" class="form-group">
		<label id="elh_bens_patrimoniais_Descricao" for="x_Descricao" class="col-sm-2 control-label ewLabel"><?php echo $bens_patrimoniais->Descricao->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $bens_patrimoniais->Descricao->CellAttributes() ?>>
<span id="el_bens_patrimoniais_Descricao">
<input type="text" data-field="x_Descricao" name="x_Descricao" id="x_Descricao" size="70" maxlength="80" value="<?php echo $bens_patrimoniais->Descricao->EditValue ?>"<?php echo $bens_patrimoniais->Descricao->EditAttributes() ?>>
</span>
<?php echo $bens_patrimoniais->Descricao->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($bens_patrimoniais->DataAquisao->Visible) { // DataAquisao ?>
	<div id="r_DataAquisao" class="form-group">
		<label id="elh_bens_patrimoniais_DataAquisao" for="x_DataAquisao" class="col-sm-2 control-label ewLabel"><?php echo $bens_patrimoniais->DataAquisao->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $bens_patrimoniais->DataAquisao->CellAttributes() ?>>
<span id="el_bens_patrimoniais_DataAquisao">
<input type="text" data-field="x_DataAquisao" name="x_DataAquisao" id="x_DataAquisao" size="14" value="<?php echo $bens_patrimoniais->DataAquisao->EditValue ?>"<?php echo $bens_patrimoniais->DataAquisao->EditAttributes() ?>>
<?php if (!$bens_patrimoniais->DataAquisao->ReadOnly && !$bens_patrimoniais->DataAquisao->Disabled && @$bens_patrimoniais->DataAquisao->EditAttrs["readonly"] == "" && @$bens_patrimoniais->DataAquisao->EditAttrs["disabled"] == "") { ?>
<script type="text/javascript">
ew_CreateCalendar("fbens_patrimoniaisadd", "x_DataAquisao", "%d/%m/%Y");
</script>
<?php } ?>
</span>
<?php echo $bens_patrimoniais->DataAquisao->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($bens_patrimoniais->Tipo->Visible) { // Tipo ?>
	<div id="r_Tipo" class="form-group">
		<label id="elh_bens_patrimoniais_Tipo" class="col-sm-2 control-label ewLabel"><?php echo $bens_patrimoniais->Tipo->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $bens_patrimoniais->Tipo->CellAttributes() ?>>
<span id="el_bens_patrimoniais_Tipo">
<div id="tp_x_Tipo" class="<?php echo EW_ITEM_TEMPLATE_CLASSNAME ?>"><input type="radio" name="x_Tipo" id="x_Tipo" value="{value}"<?php echo $bens_patrimoniais->Tipo->EditAttributes() ?>></div>
<div id="dsl_x_Tipo" data-repeatcolumn="5" class="ewItemList">
<?php
$arwrk = $bens_patrimoniais->Tipo->EditValue;
if (is_array($arwrk)) {
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($bens_patrimoniais->Tipo->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " checked=\"checked\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;

		// Note: No spacing within the LABEL tag
?>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 1) ?>
<label class="radio-inline"><input type="radio" data-field="x_Tipo" name="x_Tipo" id="x_Tipo_<?php echo $rowcntwrk ?>" value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?><?php echo $bens_patrimoniais->Tipo->EditAttributes() ?>><?php echo $arwrk[$rowcntwrk][1] ?></label>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 2) ?>
<?php
	}
}
?>
</div>
</span>
<?php echo $bens_patrimoniais->Tipo->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($bens_patrimoniais->Estado_do_bem->Visible) { // Estado_do_bem ?>
	<div id="r_Estado_do_bem" class="form-group">
		<label id="elh_bens_patrimoniais_Estado_do_bem" class="col-sm-2 control-label ewLabel"><?php echo $bens_patrimoniais->Estado_do_bem->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $bens_patrimoniais->Estado_do_bem->CellAttributes() ?>>
<span id="el_bens_patrimoniais_Estado_do_bem">
<div id="tp_x_Estado_do_bem" class="<?php echo EW_ITEM_TEMPLATE_CLASSNAME ?>"><input type="radio" name="x_Estado_do_bem" id="x_Estado_do_bem" value="{value}"<?php echo $bens_patrimoniais->Estado_do_bem->EditAttributes() ?>></div>
<div id="dsl_x_Estado_do_bem" data-repeatcolumn="5" class="ewItemList">
<?php
$arwrk = $bens_patrimoniais->Estado_do_bem->EditValue;
if (is_array($arwrk)) {
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($bens_patrimoniais->Estado_do_bem->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " checked=\"checked\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;

		// Note: No spacing within the LABEL tag
?>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 1) ?>
<label class="radio-inline"><input type="radio" data-field="x_Estado_do_bem" name="x_Estado_do_bem" id="x_Estado_do_bem_<?php echo $rowcntwrk ?>" value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?><?php echo $bens_patrimoniais->Estado_do_bem->EditAttributes() ?>><?php echo $arwrk[$rowcntwrk][1] ?></label>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 2) ?>
<?php
	}
}
?>
</div>
<script type="text/javascript">
fbens_patrimoniaisadd.Lists["x_Estado_do_bem"].Options = <?php echo (is_array($bens_patrimoniais->Estado_do_bem->EditValue)) ? ew_ArrayToJson($bens_patrimoniais->Estado_do_bem->EditValue, 0) : "[]" ?>;
</script>
</span>
<?php echo $bens_patrimoniais->Estado_do_bem->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($bens_patrimoniais->Valor_estimado->Visible) { // Valor_estimado ?>
	<div id="r_Valor_estimado" class="form-group">
		<label id="elh_bens_patrimoniais_Valor_estimado" for="x_Valor_estimado" class="col-sm-2 control-label ewLabel"><?php echo $bens_patrimoniais->Valor_estimado->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $bens_patrimoniais->Valor_estimado->CellAttributes() ?>>
<span id="el_bens_patrimoniais_Valor_estimado">
<input type="text" data-field="x_Valor_estimado" name="x_Valor_estimado" id="x_Valor_estimado" size="15" value="<?php echo $bens_patrimoniais->Valor_estimado->EditValue ?>"<?php echo $bens_patrimoniais->Valor_estimado->EditAttributes() ?>>
</span>
<?php echo $bens_patrimoniais->Valor_estimado->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($bens_patrimoniais->Situacao->Visible) { // Situacao ?>
	<div id="r_Situacao" class="form-group">
		<label id="elh_bens_patrimoniais_Situacao" for="x_Situacao" class="col-sm-2 control-label ewLabel"><?php echo $bens_patrimoniais->Situacao->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $bens_patrimoniais->Situacao->CellAttributes() ?>>
<span id="el_bens_patrimoniais_Situacao">
<select data-field="x_Situacao" id="x_Situacao" name="x_Situacao"<?php echo $bens_patrimoniais->Situacao->EditAttributes() ?>>
<?php
if (is_array($bens_patrimoniais->Situacao->EditValue)) {
	$arwrk = $bens_patrimoniais->Situacao->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($bens_patrimoniais->Situacao->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
</span>
<?php echo $bens_patrimoniais->Situacao->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($bens_patrimoniais->Anotacoes->Visible) { // Anotacoes ?>
	<div id="r_Anotacoes" class="form-group">
		<label id="elh_bens_patrimoniais_Anotacoes" for="x_Anotacoes" class="col-sm-2 control-label ewLabel"><?php echo $bens_patrimoniais->Anotacoes->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $bens_patrimoniais->Anotacoes->CellAttributes() ?>>
<span id="el_bens_patrimoniais_Anotacoes">
<textarea data-field="x_Anotacoes" name="x_Anotacoes" id="x_Anotacoes" cols="60" rows="3"<?php echo $bens_patrimoniais->Anotacoes->EditAttributes() ?>><?php echo $bens_patrimoniais->Anotacoes->EditValue ?></textarea>
</span>
<?php echo $bens_patrimoniais->Anotacoes->CustomMsg ?></div></div>
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
fbens_patrimoniaisadd.Init();
</script>
<?php
$bens_patrimoniais_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$bens_patrimoniais_add->Page_Terminate();
?>
