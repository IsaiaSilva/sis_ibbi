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

$caixadodia_add = NULL; // Initialize page object first

class ccaixadodia_add extends ccaixadodia {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{2B7992FC-5911-46A7-9310-01F4D4225C49}";

	// Table name
	var $TableName = 'caixadodia';

	// Page object name
	var $PageObjName = 'caixadodia_add';

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

		// Table object (caixadodia)
		if (!isset($GLOBALS["caixadodia"]) || get_class($GLOBALS["caixadodia"]) == "ccaixadodia") {
			$GLOBALS["caixadodia"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["caixadodia"];
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
			define("EW_TABLE_NAME", 'caixadodia', TRUE);

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
			$this->Page_Terminate(ew_GetUrl("caixadodialist.php"));
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
					$this->Page_Terminate("caixadodialist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "caixadodiaview.php")
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
		$this->Tipo->CurrentValue = NULL;
		$this->Tipo->OldValue = $this->Tipo->CurrentValue;
		$this->Conta_Caixa->CurrentValue = NULL;
		$this->Conta_Caixa->OldValue = $this->Conta_Caixa->CurrentValue;
		$this->Situacao->CurrentValue = NULL;
		$this->Situacao->OldValue = $this->Situacao->CurrentValue;
		$this->Descricao->CurrentValue = NULL;
		$this->Descricao->OldValue = $this->Descricao->CurrentValue;
		$this->id_discipulo->CurrentValue = NULL;
		$this->id_discipulo->OldValue = $this->id_discipulo->CurrentValue;
		$this->Receitas->CurrentValue = NULL;
		$this->Receitas->OldValue = $this->Receitas->CurrentValue;
		$this->Despesas->CurrentValue = NULL;
		$this->Despesas->OldValue = $this->Despesas->CurrentValue;
		$this->FormaPagto->CurrentValue = NULL;
		$this->FormaPagto->OldValue = $this->FormaPagto->CurrentValue;
		$this->N_Documento->CurrentValue = NULL;
		$this->N_Documento->OldValue = $this->N_Documento->CurrentValue;
		$this->Dt_Lancamento->CurrentValue = NULL;
		$this->Dt_Lancamento->OldValue = $this->Dt_Lancamento->CurrentValue;
		$this->Vencimento->CurrentValue = NULL;
		$this->Vencimento->OldValue = $this->Vencimento->CurrentValue;
		$this->Centro_de_Custo->CurrentValue = NULL;
		$this->Centro_de_Custo->OldValue = $this->Centro_de_Custo->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->Tipo->FldIsDetailKey) {
			$this->Tipo->setFormValue($objForm->GetValue("x_Tipo"));
		}
		if (!$this->Conta_Caixa->FldIsDetailKey) {
			$this->Conta_Caixa->setFormValue($objForm->GetValue("x_Conta_Caixa"));
		}
		if (!$this->Situacao->FldIsDetailKey) {
			$this->Situacao->setFormValue($objForm->GetValue("x_Situacao"));
		}
		if (!$this->Descricao->FldIsDetailKey) {
			$this->Descricao->setFormValue($objForm->GetValue("x_Descricao"));
		}
		if (!$this->id_discipulo->FldIsDetailKey) {
			$this->id_discipulo->setFormValue($objForm->GetValue("x_id_discipulo"));
		}
		if (!$this->Receitas->FldIsDetailKey) {
			$this->Receitas->setFormValue($objForm->GetValue("x_Receitas"));
		}
		if (!$this->Despesas->FldIsDetailKey) {
			$this->Despesas->setFormValue($objForm->GetValue("x_Despesas"));
		}
		if (!$this->FormaPagto->FldIsDetailKey) {
			$this->FormaPagto->setFormValue($objForm->GetValue("x_FormaPagto"));
		}
		if (!$this->N_Documento->FldIsDetailKey) {
			$this->N_Documento->setFormValue($objForm->GetValue("x_N_Documento"));
		}
		if (!$this->Dt_Lancamento->FldIsDetailKey) {
			$this->Dt_Lancamento->setFormValue($objForm->GetValue("x_Dt_Lancamento"));
			$this->Dt_Lancamento->CurrentValue = ew_UnFormatDateTime($this->Dt_Lancamento->CurrentValue, 7);
		}
		if (!$this->Vencimento->FldIsDetailKey) {
			$this->Vencimento->setFormValue($objForm->GetValue("x_Vencimento"));
			$this->Vencimento->CurrentValue = ew_UnFormatDateTime($this->Vencimento->CurrentValue, 7);
		}
		if (!$this->Centro_de_Custo->FldIsDetailKey) {
			$this->Centro_de_Custo->setFormValue($objForm->GetValue("x_Centro_de_Custo"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->Tipo->CurrentValue = $this->Tipo->FormValue;
		$this->Conta_Caixa->CurrentValue = $this->Conta_Caixa->FormValue;
		$this->Situacao->CurrentValue = $this->Situacao->FormValue;
		$this->Descricao->CurrentValue = $this->Descricao->FormValue;
		$this->id_discipulo->CurrentValue = $this->id_discipulo->FormValue;
		$this->Receitas->CurrentValue = $this->Receitas->FormValue;
		$this->Despesas->CurrentValue = $this->Despesas->FormValue;
		$this->FormaPagto->CurrentValue = $this->FormaPagto->FormValue;
		$this->N_Documento->CurrentValue = $this->N_Documento->FormValue;
		$this->Dt_Lancamento->CurrentValue = $this->Dt_Lancamento->FormValue;
		$this->Dt_Lancamento->CurrentValue = ew_UnFormatDateTime($this->Dt_Lancamento->CurrentValue, 7);
		$this->Vencimento->CurrentValue = $this->Vencimento->FormValue;
		$this->Vencimento->CurrentValue = ew_UnFormatDateTime($this->Vencimento->CurrentValue, 7);
		$this->Centro_de_Custo->CurrentValue = $this->Centro_de_Custo->FormValue;
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

			// id_discipulo
			$this->id_discipulo->LinkCustomAttributes = "";
			$this->id_discipulo->HrefValue = "";
			$this->id_discipulo->TooltipValue = "";

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
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// Tipo
			$this->Tipo->EditAttrs["class"] = "form-control";
			$this->Tipo->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->Tipo->FldTagValue(1), $this->Tipo->FldTagCaption(1) <> "" ? $this->Tipo->FldTagCaption(1) : $this->Tipo->FldTagValue(1));
			$arwrk[] = array($this->Tipo->FldTagValue(2), $this->Tipo->FldTagCaption(2) <> "" ? $this->Tipo->FldTagCaption(2) : $this->Tipo->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->Tipo->EditValue = $arwrk;

			// Conta_Caixa
			$this->Conta_Caixa->EditAttrs["class"] = "form-control";
			$this->Conta_Caixa->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `Id`, `Conta_Caixa` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `Tipo` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `fin_conta_caixa`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Conta_Caixa, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->Conta_Caixa->EditValue = $arwrk;

			// Situacao
			$this->Situacao->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `Id`, `Situacao` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `id_tipo` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `fin_situacao`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Situacao, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Situacao->EditValue = $arwrk;

			// Descricao
			$this->Descricao->EditAttrs["class"] = "form-control";
			$this->Descricao->EditCustomAttributes = "";
			$this->Descricao->EditValue = ew_HtmlEncode($this->Descricao->CurrentValue);

			// id_discipulo
			$this->id_discipulo->EditAttrs["class"] = "form-control";
			$this->id_discipulo->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `Id_membro`, `Nome` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `membro`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->id_discipulo, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `Nome` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->id_discipulo->EditValue = $arwrk;

			// Receitas
			$this->Receitas->EditAttrs["class"] = "form-control";
			$this->Receitas->EditCustomAttributes = "";
			$this->Receitas->EditValue = ew_HtmlEncode($this->Receitas->CurrentValue);
			if (strval($this->Receitas->EditValue) <> "" && is_numeric($this->Receitas->EditValue)) $this->Receitas->EditValue = ew_FormatNumber($this->Receitas->EditValue, -2, -2, -2, -2);

			// Despesas
			$this->Despesas->EditAttrs["class"] = "form-control";
			$this->Despesas->EditCustomAttributes = "";
			$this->Despesas->EditValue = ew_HtmlEncode($this->Despesas->CurrentValue);
			if (strval($this->Despesas->EditValue) <> "" && is_numeric($this->Despesas->EditValue)) $this->Despesas->EditValue = ew_FormatNumber($this->Despesas->EditValue, -2, -2, -2, -2);

			// FormaPagto
			$this->FormaPagto->EditAttrs["class"] = "form-control";
			$this->FormaPagto->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `Id`, `Forma_Pagto` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `fin_forma_pgto`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->FormaPagto, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->FormaPagto->EditValue = $arwrk;

			// N_Documento
			$this->N_Documento->EditAttrs["class"] = "form-control";
			$this->N_Documento->EditCustomAttributes = "";
			$this->N_Documento->EditValue = ew_HtmlEncode($this->N_Documento->CurrentValue);

			// Dt_Lancamento
			$this->Dt_Lancamento->EditAttrs["class"] = "form-control";
			$this->Dt_Lancamento->EditCustomAttributes = "";
			$this->Dt_Lancamento->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->Dt_Lancamento->CurrentValue, 7));

			// Vencimento
			$this->Vencimento->EditAttrs["class"] = "form-control";
			$this->Vencimento->EditCustomAttributes = "";
			$this->Vencimento->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->Vencimento->CurrentValue, 7));

			// Centro_de_Custo
			$this->Centro_de_Custo->EditAttrs["class"] = "form-control";
			$this->Centro_de_Custo->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `Id`, `Conta` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `fin_centro_de_custo`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Centro_de_Custo, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->Centro_de_Custo->EditValue = $arwrk;

			// Edit refer script
			// Tipo

			$this->Tipo->HrefValue = "";

			// Conta_Caixa
			$this->Conta_Caixa->HrefValue = "";

			// Situacao
			$this->Situacao->HrefValue = "";

			// Descricao
			$this->Descricao->HrefValue = "";

			// id_discipulo
			$this->id_discipulo->HrefValue = "";

			// Receitas
			$this->Receitas->HrefValue = "";

			// Despesas
			$this->Despesas->HrefValue = "";

			// FormaPagto
			$this->FormaPagto->HrefValue = "";

			// N_Documento
			$this->N_Documento->HrefValue = "";

			// Dt_Lancamento
			$this->Dt_Lancamento->HrefValue = "";

			// Vencimento
			$this->Vencimento->HrefValue = "";

			// Centro_de_Custo
			$this->Centro_de_Custo->HrefValue = "";
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
		if (!$this->Tipo->FldIsDetailKey && !is_null($this->Tipo->FormValue) && $this->Tipo->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Tipo->FldCaption(), $this->Tipo->ReqErrMsg));
		}
		if (!$this->Conta_Caixa->FldIsDetailKey && !is_null($this->Conta_Caixa->FormValue) && $this->Conta_Caixa->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Conta_Caixa->FldCaption(), $this->Conta_Caixa->ReqErrMsg));
		}
		if ($this->Situacao->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Situacao->FldCaption(), $this->Situacao->ReqErrMsg));
		}
		if (!$this->Descricao->FldIsDetailKey && !is_null($this->Descricao->FormValue) && $this->Descricao->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Descricao->FldCaption(), $this->Descricao->ReqErrMsg));
		}
		if (!$this->Receitas->FldIsDetailKey && !is_null($this->Receitas->FormValue) && $this->Receitas->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Receitas->FldCaption(), $this->Receitas->ReqErrMsg));
		}
		if (!ew_CheckNumber($this->Receitas->FormValue)) {
			ew_AddMessage($gsFormError, $this->Receitas->FldErrMsg());
		}
		if (!$this->Despesas->FldIsDetailKey && !is_null($this->Despesas->FormValue) && $this->Despesas->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Despesas->FldCaption(), $this->Despesas->ReqErrMsg));
		}
		if (!ew_CheckNumber($this->Despesas->FormValue)) {
			ew_AddMessage($gsFormError, $this->Despesas->FldErrMsg());
		}
		if (!$this->FormaPagto->FldIsDetailKey && !is_null($this->FormaPagto->FormValue) && $this->FormaPagto->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->FormaPagto->FldCaption(), $this->FormaPagto->ReqErrMsg));
		}
		if (!$this->Dt_Lancamento->FldIsDetailKey && !is_null($this->Dt_Lancamento->FormValue) && $this->Dt_Lancamento->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Dt_Lancamento->FldCaption(), $this->Dt_Lancamento->ReqErrMsg));
		}
		if (!ew_CheckEuroDate($this->Dt_Lancamento->FormValue)) {
			ew_AddMessage($gsFormError, $this->Dt_Lancamento->FldErrMsg());
		}
		if (!ew_CheckEuroDate($this->Vencimento->FormValue)) {
			ew_AddMessage($gsFormError, $this->Vencimento->FldErrMsg());
		}
		if (!$this->Centro_de_Custo->FldIsDetailKey && !is_null($this->Centro_de_Custo->FormValue) && $this->Centro_de_Custo->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Centro_de_Custo->FldCaption(), $this->Centro_de_Custo->ReqErrMsg));
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

		// Tipo
		$this->Tipo->SetDbValueDef($rsnew, $this->Tipo->CurrentValue, NULL, FALSE);

		// Conta_Caixa
		$this->Conta_Caixa->SetDbValueDef($rsnew, $this->Conta_Caixa->CurrentValue, NULL, FALSE);

		// Situacao
		$this->Situacao->SetDbValueDef($rsnew, $this->Situacao->CurrentValue, NULL, FALSE);

		// Descricao
		$this->Descricao->SetDbValueDef($rsnew, $this->Descricao->CurrentValue, NULL, FALSE);

		// id_discipulo
		$this->id_discipulo->SetDbValueDef($rsnew, $this->id_discipulo->CurrentValue, NULL, FALSE);

		// Receitas
		$this->Receitas->SetDbValueDef($rsnew, $this->Receitas->CurrentValue, NULL, FALSE);

		// Despesas
		$this->Despesas->SetDbValueDef($rsnew, $this->Despesas->CurrentValue, NULL, FALSE);

		// FormaPagto
		$this->FormaPagto->SetDbValueDef($rsnew, $this->FormaPagto->CurrentValue, NULL, FALSE);

		// N_Documento
		$this->N_Documento->SetDbValueDef($rsnew, $this->N_Documento->CurrentValue, NULL, FALSE);

		// Dt_Lancamento
		$this->Dt_Lancamento->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->Dt_Lancamento->CurrentValue, 7), NULL, FALSE);

		// Vencimento
		$this->Vencimento->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->Vencimento->CurrentValue, 7), NULL, FALSE);

		// Centro_de_Custo
		$this->Centro_de_Custo->SetDbValueDef($rsnew, $this->Centro_de_Custo->CurrentValue, NULL, FALSE);

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
		}
		return $AddRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, "caixadodialist.php", "", $this->TableVar, TRUE);
		$PageId = ($this->CurrentAction == "C") ? "Copy" : "Add";
		$Breadcrumb->Add("add", $PageId, $url);
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
if (!isset($caixadodia_add)) $caixadodia_add = new ccaixadodia_add();

// Page init
$caixadodia_add->Page_Init();

// Page main
$caixadodia_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$caixadodia_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var caixadodia_add = new ew_Page("caixadodia_add");
caixadodia_add.PageID = "add"; // Page ID
var EW_PAGE_ID = caixadodia_add.PageID; // For backward compatibility

// Form object
var fcaixadodiaadd = new ew_Form("fcaixadodiaadd");

// Validate form
fcaixadodiaadd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_Tipo");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $caixadodia->Tipo->FldCaption(), $caixadodia->Tipo->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Conta_Caixa");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $caixadodia->Conta_Caixa->FldCaption(), $caixadodia->Conta_Caixa->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Situacao");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $caixadodia->Situacao->FldCaption(), $caixadodia->Situacao->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Descricao");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $caixadodia->Descricao->FldCaption(), $caixadodia->Descricao->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Receitas");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $caixadodia->Receitas->FldCaption(), $caixadodia->Receitas->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Receitas");
			if (elm && !ew_CheckNumber(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($caixadodia->Receitas->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Despesas");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $caixadodia->Despesas->FldCaption(), $caixadodia->Despesas->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Despesas");
			if (elm && !ew_CheckNumber(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($caixadodia->Despesas->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_FormaPagto");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $caixadodia->FormaPagto->FldCaption(), $caixadodia->FormaPagto->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Dt_Lancamento");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $caixadodia->Dt_Lancamento->FldCaption(), $caixadodia->Dt_Lancamento->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Dt_Lancamento");
			if (elm && !ew_CheckEuroDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($caixadodia->Dt_Lancamento->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Vencimento");
			if (elm && !ew_CheckEuroDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($caixadodia->Vencimento->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Centro_de_Custo");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $caixadodia->Centro_de_Custo->FldCaption(), $caixadodia->Centro_de_Custo->ReqErrMsg)) ?>");

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
fcaixadodiaadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fcaixadodiaadd.ValidateRequired = true;
<?php } else { ?>
fcaixadodiaadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fcaixadodiaadd.Lists["x_Conta_Caixa"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Conta_Caixa","","",""],"ParentFields":["x_Tipo"],"FilterFields":["x_Tipo"],"Options":[]};
fcaixadodiaadd.Lists["x_Situacao"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Situacao","","",""],"ParentFields":["x_Tipo"],"FilterFields":["x_id_tipo"],"Options":[]};
fcaixadodiaadd.Lists["x_id_discipulo"] = {"LinkField":"x_Id_membro","Ajax":null,"AutoFill":false,"DisplayFields":["x_Nome","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fcaixadodiaadd.Lists["x_FormaPagto"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Forma_Pagto","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fcaixadodiaadd.Lists["x_Centro_de_Custo"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Conta","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

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
<?php $caixadodia_add->ShowPageHeader(); ?>
<?php
$caixadodia_add->ShowMessage();
?>
<form name="fcaixadodiaadd" id="fcaixadodiaadd" class="form-horizontal ewForm ewAddForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($caixadodia_add->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $caixadodia_add->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="caixadodia">
<input type="hidden" name="a_add" id="a_add" value="A">
<div>
<?php if ($caixadodia->Tipo->Visible) { // Tipo ?>
	<div id="r_Tipo" class="form-group">
		<label id="elh_caixadodia_Tipo" for="x_Tipo" class="col-sm-2 control-label ewLabel"><?php echo $caixadodia->Tipo->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $caixadodia->Tipo->CellAttributes() ?>>
<span id="el_caixadodia_Tipo">
<?php $caixadodia->Tipo->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x_Conta_Caixa','x_Situacao']); " . @$caixadodia->Tipo->EditAttrs["onchange"]; ?>
<select data-field="x_Tipo" id="x_Tipo" name="x_Tipo"<?php echo $caixadodia->Tipo->EditAttributes() ?>>
<?php
if (is_array($caixadodia->Tipo->EditValue)) {
	$arwrk = $caixadodia->Tipo->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($caixadodia->Tipo->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php echo $caixadodia->Tipo->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($caixadodia->Conta_Caixa->Visible) { // Conta_Caixa ?>
	<div id="r_Conta_Caixa" class="form-group">
		<label id="elh_caixadodia_Conta_Caixa" for="x_Conta_Caixa" class="col-sm-2 control-label ewLabel"><?php echo $caixadodia->Conta_Caixa->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $caixadodia->Conta_Caixa->CellAttributes() ?>>
<span id="el_caixadodia_Conta_Caixa">
<select data-field="x_Conta_Caixa" id="x_Conta_Caixa" name="x_Conta_Caixa"<?php echo $caixadodia->Conta_Caixa->EditAttributes() ?>>
<?php
if (is_array($caixadodia->Conta_Caixa->EditValue)) {
	$arwrk = $caixadodia->Conta_Caixa->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($caixadodia->Conta_Caixa->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
fcaixadodiaadd.Lists["x_Conta_Caixa"].Options = <?php echo (is_array($caixadodia->Conta_Caixa->EditValue)) ? ew_ArrayToJson($caixadodia->Conta_Caixa->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $caixadodia->Conta_Caixa->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($caixadodia->Situacao->Visible) { // Situacao ?>
	<div id="r_Situacao" class="form-group">
		<label id="elh_caixadodia_Situacao" class="col-sm-2 control-label ewLabel"><?php echo $caixadodia->Situacao->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $caixadodia->Situacao->CellAttributes() ?>>
<span id="el_caixadodia_Situacao">
<div id="tp_x_Situacao" class="<?php echo EW_ITEM_TEMPLATE_CLASSNAME ?>"><input type="radio" name="x_Situacao" id="x_Situacao" value="{value}"<?php echo $caixadodia->Situacao->EditAttributes() ?>></div>
<div id="dsl_x_Situacao" data-repeatcolumn="5" class="ewItemList">
<?php
$arwrk = $caixadodia->Situacao->EditValue;
if (is_array($arwrk)) {
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($caixadodia->Situacao->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " checked=\"checked\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;

		// Note: No spacing within the LABEL tag
?>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 1) ?>
<label class="radio-inline"><input type="radio" data-field="x_Situacao" name="x_Situacao" id="x_Situacao_<?php echo $rowcntwrk ?>" value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?><?php echo $caixadodia->Situacao->EditAttributes() ?>><?php echo $arwrk[$rowcntwrk][1] ?></label>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 2) ?>
<?php
	}
}
?>
</div>
<script type="text/javascript">
fcaixadodiaadd.Lists["x_Situacao"].Options = <?php echo (is_array($caixadodia->Situacao->EditValue)) ? ew_ArrayToJson($caixadodia->Situacao->EditValue, 0) : "[]" ?>;
</script>
</span>
<?php echo $caixadodia->Situacao->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($caixadodia->Descricao->Visible) { // Descricao ?>
	<div id="r_Descricao" class="form-group">
		<label id="elh_caixadodia_Descricao" for="x_Descricao" class="col-sm-2 control-label ewLabel"><?php echo $caixadodia->Descricao->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $caixadodia->Descricao->CellAttributes() ?>>
<span id="el_caixadodia_Descricao">
<input type="text" data-field="x_Descricao" name="x_Descricao" id="x_Descricao" size="60" maxlength="60" value="<?php echo $caixadodia->Descricao->EditValue ?>"<?php echo $caixadodia->Descricao->EditAttributes() ?>>
</span>
<?php echo $caixadodia->Descricao->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($caixadodia->id_discipulo->Visible) { // id_discipulo ?>
	<div id="r_id_discipulo" class="form-group">
		<label id="elh_caixadodia_id_discipulo" for="x_id_discipulo" class="col-sm-2 control-label ewLabel"><?php echo $caixadodia->id_discipulo->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $caixadodia->id_discipulo->CellAttributes() ?>>
<span id="el_caixadodia_id_discipulo">
<select data-field="x_id_discipulo" id="x_id_discipulo" name="x_id_discipulo"<?php echo $caixadodia->id_discipulo->EditAttributes() ?>>
<?php
if (is_array($caixadodia->id_discipulo->EditValue)) {
	$arwrk = $caixadodia->id_discipulo->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($caixadodia->id_discipulo->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
fcaixadodiaadd.Lists["x_id_discipulo"].Options = <?php echo (is_array($caixadodia->id_discipulo->EditValue)) ? ew_ArrayToJson($caixadodia->id_discipulo->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $caixadodia->id_discipulo->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($caixadodia->Receitas->Visible) { // Receitas ?>
	<div id="r_Receitas" class="form-group">
		<label id="elh_caixadodia_Receitas" for="x_Receitas" class="col-sm-2 control-label ewLabel"><?php echo $caixadodia->Receitas->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $caixadodia->Receitas->CellAttributes() ?>>
<span id="el_caixadodia_Receitas">
<input type="text" data-field="x_Receitas" name="x_Receitas" id="x_Receitas" size="15" value="<?php echo $caixadodia->Receitas->EditValue ?>"<?php echo $caixadodia->Receitas->EditAttributes() ?>>
</span>
<?php echo $caixadodia->Receitas->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($caixadodia->Despesas->Visible) { // Despesas ?>
	<div id="r_Despesas" class="form-group">
		<label id="elh_caixadodia_Despesas" for="x_Despesas" class="col-sm-2 control-label ewLabel"><?php echo $caixadodia->Despesas->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $caixadodia->Despesas->CellAttributes() ?>>
<span id="el_caixadodia_Despesas">
<input type="text" data-field="x_Despesas" name="x_Despesas" id="x_Despesas" size="15" value="<?php echo $caixadodia->Despesas->EditValue ?>"<?php echo $caixadodia->Despesas->EditAttributes() ?>>
</span>
<?php echo $caixadodia->Despesas->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($caixadodia->FormaPagto->Visible) { // FormaPagto ?>
	<div id="r_FormaPagto" class="form-group">
		<label id="elh_caixadodia_FormaPagto" for="x_FormaPagto" class="col-sm-2 control-label ewLabel"><?php echo $caixadodia->FormaPagto->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $caixadodia->FormaPagto->CellAttributes() ?>>
<span id="el_caixadodia_FormaPagto">
<select data-field="x_FormaPagto" id="x_FormaPagto" name="x_FormaPagto"<?php echo $caixadodia->FormaPagto->EditAttributes() ?>>
<?php
if (is_array($caixadodia->FormaPagto->EditValue)) {
	$arwrk = $caixadodia->FormaPagto->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($caixadodia->FormaPagto->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
fcaixadodiaadd.Lists["x_FormaPagto"].Options = <?php echo (is_array($caixadodia->FormaPagto->EditValue)) ? ew_ArrayToJson($caixadodia->FormaPagto->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $caixadodia->FormaPagto->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($caixadodia->N_Documento->Visible) { // N_Documento ?>
	<div id="r_N_Documento" class="form-group">
		<label id="elh_caixadodia_N_Documento" for="x_N_Documento" class="col-sm-2 control-label ewLabel"><?php echo $caixadodia->N_Documento->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $caixadodia->N_Documento->CellAttributes() ?>>
<span id="el_caixadodia_N_Documento">
<input type="text" data-field="x_N_Documento" name="x_N_Documento" id="x_N_Documento" size="30" maxlength="20" value="<?php echo $caixadodia->N_Documento->EditValue ?>"<?php echo $caixadodia->N_Documento->EditAttributes() ?>>
</span>
<?php echo $caixadodia->N_Documento->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($caixadodia->Dt_Lancamento->Visible) { // Dt_Lancamento ?>
	<div id="r_Dt_Lancamento" class="form-group">
		<label id="elh_caixadodia_Dt_Lancamento" for="x_Dt_Lancamento" class="col-sm-2 control-label ewLabel"><?php echo $caixadodia->Dt_Lancamento->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $caixadodia->Dt_Lancamento->CellAttributes() ?>>
<span id="el_caixadodia_Dt_Lancamento">
<input type="text" data-field="x_Dt_Lancamento" name="x_Dt_Lancamento" id="x_Dt_Lancamento" size="10" value="<?php echo $caixadodia->Dt_Lancamento->EditValue ?>"<?php echo $caixadodia->Dt_Lancamento->EditAttributes() ?>>
<?php if (!$caixadodia->Dt_Lancamento->ReadOnly && !$caixadodia->Dt_Lancamento->Disabled && @$caixadodia->Dt_Lancamento->EditAttrs["readonly"] == "" && @$caixadodia->Dt_Lancamento->EditAttrs["disabled"] == "") { ?>
<script type="text/javascript">
ew_CreateCalendar("fcaixadodiaadd", "x_Dt_Lancamento", "%d/%m/%Y");
</script>
<?php } ?>
</span>
<?php echo $caixadodia->Dt_Lancamento->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($caixadodia->Vencimento->Visible) { // Vencimento ?>
	<div id="r_Vencimento" class="form-group">
		<label id="elh_caixadodia_Vencimento" for="x_Vencimento" class="col-sm-2 control-label ewLabel"><?php echo $caixadodia->Vencimento->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $caixadodia->Vencimento->CellAttributes() ?>>
<span id="el_caixadodia_Vencimento">
<input type="text" data-field="x_Vencimento" name="x_Vencimento" id="x_Vencimento" size="10" value="<?php echo $caixadodia->Vencimento->EditValue ?>"<?php echo $caixadodia->Vencimento->EditAttributes() ?>>
<?php if (!$caixadodia->Vencimento->ReadOnly && !$caixadodia->Vencimento->Disabled && @$caixadodia->Vencimento->EditAttrs["readonly"] == "" && @$caixadodia->Vencimento->EditAttrs["disabled"] == "") { ?>
<script type="text/javascript">
ew_CreateCalendar("fcaixadodiaadd", "x_Vencimento", "%d/%m/%Y");
</script>
<?php } ?>
</span>
<?php echo $caixadodia->Vencimento->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($caixadodia->Centro_de_Custo->Visible) { // Centro_de_Custo ?>
	<div id="r_Centro_de_Custo" class="form-group">
		<label id="elh_caixadodia_Centro_de_Custo" for="x_Centro_de_Custo" class="col-sm-2 control-label ewLabel"><?php echo $caixadodia->Centro_de_Custo->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $caixadodia->Centro_de_Custo->CellAttributes() ?>>
<span id="el_caixadodia_Centro_de_Custo">
<select data-field="x_Centro_de_Custo" id="x_Centro_de_Custo" name="x_Centro_de_Custo"<?php echo $caixadodia->Centro_de_Custo->EditAttributes() ?>>
<?php
if (is_array($caixadodia->Centro_de_Custo->EditValue)) {
	$arwrk = $caixadodia->Centro_de_Custo->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($caixadodia->Centro_de_Custo->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
fcaixadodiaadd.Lists["x_Centro_de_Custo"].Options = <?php echo (is_array($caixadodia->Centro_de_Custo->EditValue)) ? ew_ArrayToJson($caixadodia->Centro_de_Custo->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $caixadodia->Centro_de_Custo->CustomMsg ?></div></div>
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
fcaixadodiaadd.Init();
</script>
<?php
$caixadodia_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">
$(document).ready(function($) {
	$("#r_Despesas").hide();
	$("#r_Receitas").hide();
	$("#x_Receitas").attr("placeholder","Valor");
	$("#x_Despesas").attr("placeholder","Valor");
mostracpvalor();
	$("#x_Tipo").change(function() {
	mostracpvalor();
});

function mostracpvalor(){
		if($("#x_Tipo").val()==1){
			$("#r_Despesas").hide();
			$("#r_Receitas").show();
			$("#elh_extratomes_Receitas").text("Valor R$");
		}else{
			$("#r_Despesas").show();
			$("#r_Receitas").hide();
			$("#elh_extratomes_Despesas").text("Valor R$");
		}
}
});
</script>
<?php include_once "footer.php" ?>
<?php
$caixadodia_add->Page_Terminate();
?>
