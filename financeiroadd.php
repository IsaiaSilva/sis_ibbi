<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg11.php" ?>
<?php include_once "ewmysql11.php" ?>
<?php include_once "phpfn11.php" ?>
<?php include_once "financeiroinfo.php" ?>
<?php include_once "usuariosinfo.php" ?>
<?php include_once "userfn11.php" ?>
<?php

//
// Page class
//

$financeiro_add = NULL; // Initialize page object first

class cfinanceiro_add extends cfinanceiro {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{2B7992FC-5911-46A7-9310-01F4D4225C49}";

	// Table name
	var $TableName = 'financeiro';

	// Page object name
	var $PageObjName = 'financeiro_add';

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

		// Table object (financeiro)
		if (!isset($GLOBALS["financeiro"]) || get_class($GLOBALS["financeiro"]) == "cfinanceiro") {
			$GLOBALS["financeiro"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["financeiro"];
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
			define("EW_TABLE_NAME", 'financeiro', TRUE);

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
			$this->Page_Terminate(ew_GetUrl("financeirolist.php"));
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
		global $EW_EXPORT, $financeiro;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($financeiro);
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
					$this->Page_Terminate("financeirolist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "financeiroview.php")
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
		$this->Tipo_Recebimento->CurrentValue = NULL;
		$this->Tipo_Recebimento->OldValue = $this->Tipo_Recebimento->CurrentValue;
		$this->FormaPagto->CurrentValue = NULL;
		$this->FormaPagto->OldValue = $this->FormaPagto->CurrentValue;
		$this->Conta_Caixa->CurrentValue = NULL;
		$this->Conta_Caixa->OldValue = $this->Conta_Caixa->CurrentValue;
		$this->Situacao->CurrentValue = NULL;
		$this->Situacao->OldValue = $this->Situacao->CurrentValue;
		$this->Descricao->CurrentValue = NULL;
		$this->Descricao->OldValue = $this->Descricao->CurrentValue;
		$this->Receitas->CurrentValue = NULL;
		$this->Receitas->OldValue = $this->Receitas->CurrentValue;
		$this->Despesas->CurrentValue = NULL;
		$this->Despesas->OldValue = $this->Despesas->CurrentValue;
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
		if (!$this->Tipo_Recebimento->FldIsDetailKey) {
			$this->Tipo_Recebimento->setFormValue($objForm->GetValue("x_Tipo_Recebimento"));
		}
		if (!$this->FormaPagto->FldIsDetailKey) {
			$this->FormaPagto->setFormValue($objForm->GetValue("x_FormaPagto"));
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
		if (!$this->Receitas->FldIsDetailKey) {
			$this->Receitas->setFormValue($objForm->GetValue("x_Receitas"));
		}
		if (!$this->Despesas->FldIsDetailKey) {
			$this->Despesas->setFormValue($objForm->GetValue("x_Despesas"));
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
		$this->Tipo_Recebimento->CurrentValue = $this->Tipo_Recebimento->FormValue;
		$this->FormaPagto->CurrentValue = $this->FormaPagto->FormValue;
		$this->Conta_Caixa->CurrentValue = $this->Conta_Caixa->FormValue;
		$this->Situacao->CurrentValue = $this->Situacao->FormValue;
		$this->Descricao->CurrentValue = $this->Descricao->FormValue;
		$this->Receitas->CurrentValue = $this->Receitas->FormValue;
		$this->Despesas->CurrentValue = $this->Despesas->FormValue;
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
		$this->Tipo_Recebimento->setDbValue($rs->fields('Tipo_Recebimento'));
		$this->FormaPagto->setDbValue($rs->fields('FormaPagto'));
		$this->Conta_Caixa->setDbValue($rs->fields('Conta_Caixa'));
		$this->Situacao->setDbValue($rs->fields('Situacao'));
		$this->Descricao->setDbValue($rs->fields('Descricao'));
		$this->id_discipulo->setDbValue($rs->fields('id_discipulo'));
		$this->Receitas->setDbValue($rs->fields('Receitas'));
		$this->Despesas->setDbValue($rs->fields('Despesas'));
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
		$this->Tipo_Recebimento->DbValue = $row['Tipo_Recebimento'];
		$this->FormaPagto->DbValue = $row['FormaPagto'];
		$this->Conta_Caixa->DbValue = $row['Conta_Caixa'];
		$this->Situacao->DbValue = $row['Situacao'];
		$this->Descricao->DbValue = $row['Descricao'];
		$this->id_discipulo->DbValue = $row['id_discipulo'];
		$this->Receitas->DbValue = $row['Receitas'];
		$this->Despesas->DbValue = $row['Despesas'];
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
		// Tipo_Recebimento
		// FormaPagto
		// Conta_Caixa
		// Situacao
		// Descricao
		// id_discipulo
		// Receitas
		// Despesas
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

			// Tipo_Recebimento
			if (strval($this->Tipo_Recebimento->CurrentValue) <> "") {
				switch ($this->Tipo_Recebimento->CurrentValue) {
					case $this->Tipo_Recebimento->FldTagValue(1):
						$this->Tipo_Recebimento->ViewValue = $this->Tipo_Recebimento->FldTagCaption(1) <> "" ? $this->Tipo_Recebimento->FldTagCaption(1) : $this->Tipo_Recebimento->CurrentValue;
						break;
					case $this->Tipo_Recebimento->FldTagValue(2):
						$this->Tipo_Recebimento->ViewValue = $this->Tipo_Recebimento->FldTagCaption(2) <> "" ? $this->Tipo_Recebimento->FldTagCaption(2) : $this->Tipo_Recebimento->CurrentValue;
						break;
					default:
						$this->Tipo_Recebimento->ViewValue = $this->Tipo_Recebimento->CurrentValue;
				}
			} else {
				$this->Tipo_Recebimento->ViewValue = NULL;
			}
			$this->Tipo_Recebimento->CellCssStyle .= "text-align: center;";
			$this->Tipo_Recebimento->ViewCustomAttributes = "";

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
			$sSqlWrk .= " ORDER BY `Forma_Pagto` ASC";
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
			$this->Situacao->CellCssStyle .= "text-align: center;";
			$this->Situacao->ViewCustomAttributes = "";

			// Descricao
			$this->Descricao->ViewValue = $this->Descricao->CurrentValue;
			$this->Descricao->ViewCustomAttributes = "";

			// id_discipulo
			if (strval($this->id_discipulo->CurrentValue) <> "") {
				$sFilterWrk = "`Id_membro`" . ew_SearchString("=", $this->id_discipulo->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `Id_membro`, `Nome` AS `DispFld`, `CPF` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `membro`";
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
					$this->id_discipulo->ViewValue .= ew_ValueSeparator(1,$this->id_discipulo) . $rswrk->fields('Disp2Fld');
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
			$sSqlWrk .= " ORDER BY `Conta` ASC";
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

			// Tipo_Recebimento
			$this->Tipo_Recebimento->LinkCustomAttributes = "";
			$this->Tipo_Recebimento->HrefValue = "";
			$this->Tipo_Recebimento->TooltipValue = "";

			// FormaPagto
			$this->FormaPagto->LinkCustomAttributes = "";
			$this->FormaPagto->HrefValue = "";
			$this->FormaPagto->TooltipValue = "";

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

			// Tipo_Recebimento
			$this->Tipo_Recebimento->EditAttrs["class"] = "form-control";
			$this->Tipo_Recebimento->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->Tipo_Recebimento->FldTagValue(1), $this->Tipo_Recebimento->FldTagCaption(1) <> "" ? $this->Tipo_Recebimento->FldTagCaption(1) : $this->Tipo_Recebimento->FldTagValue(1));
			$arwrk[] = array($this->Tipo_Recebimento->FldTagValue(2), $this->Tipo_Recebimento->FldTagCaption(2) <> "" ? $this->Tipo_Recebimento->FldTagCaption(2) : $this->Tipo_Recebimento->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->Tipo_Recebimento->EditValue = $arwrk;

			// FormaPagto
			$this->FormaPagto->EditAttrs["class"] = "form-control";
			$this->FormaPagto->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `Id`, `Forma_Pagto` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `filtro_tipo_recebimento` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `fin_forma_pgto`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->FormaPagto, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `Forma_Pagto` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->FormaPagto->EditValue = $arwrk;

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
			$sSqlWrk .= " ORDER BY `Conta` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->Centro_de_Custo->EditValue = $arwrk;

			// Edit refer script
			// Tipo

			$this->Tipo->HrefValue = "";

			// Tipo_Recebimento
			$this->Tipo_Recebimento->HrefValue = "";

			// FormaPagto
			$this->FormaPagto->HrefValue = "";

			// Conta_Caixa
			$this->Conta_Caixa->HrefValue = "";

			// Situacao
			$this->Situacao->HrefValue = "";

			// Descricao
			$this->Descricao->HrefValue = "";

			// Receitas
			$this->Receitas->HrefValue = "";

			// Despesas
			$this->Despesas->HrefValue = "";

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
		if (!$this->Tipo_Recebimento->FldIsDetailKey && !is_null($this->Tipo_Recebimento->FormValue) && $this->Tipo_Recebimento->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Tipo_Recebimento->FldCaption(), $this->Tipo_Recebimento->ReqErrMsg));
		}
		if (!$this->FormaPagto->FldIsDetailKey && !is_null($this->FormaPagto->FormValue) && $this->FormaPagto->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->FormaPagto->FldCaption(), $this->FormaPagto->ReqErrMsg));
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
		if (!$this->Dt_Lancamento->FldIsDetailKey && !is_null($this->Dt_Lancamento->FormValue) && $this->Dt_Lancamento->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Dt_Lancamento->FldCaption(), $this->Dt_Lancamento->ReqErrMsg));
		}
		if (!ew_CheckEuroDate($this->Dt_Lancamento->FormValue)) {
			ew_AddMessage($gsFormError, $this->Dt_Lancamento->FldErrMsg());
		}
		if (!ew_CheckEuroDate($this->Vencimento->FormValue)) {
			ew_AddMessage($gsFormError, $this->Vencimento->FldErrMsg());
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

		// Tipo_Recebimento
		$this->Tipo_Recebimento->SetDbValueDef($rsnew, $this->Tipo_Recebimento->CurrentValue, 0, FALSE);

		// FormaPagto
		$this->FormaPagto->SetDbValueDef($rsnew, $this->FormaPagto->CurrentValue, NULL, FALSE);

		// Conta_Caixa
		$this->Conta_Caixa->SetDbValueDef($rsnew, $this->Conta_Caixa->CurrentValue, NULL, FALSE);

		// Situacao
		$this->Situacao->SetDbValueDef($rsnew, $this->Situacao->CurrentValue, NULL, FALSE);

		// Descricao
		$this->Descricao->SetDbValueDef($rsnew, $this->Descricao->CurrentValue, NULL, FALSE);

		// Receitas
		$this->Receitas->SetDbValueDef($rsnew, $this->Receitas->CurrentValue, NULL, FALSE);

		// Despesas
		$this->Despesas->SetDbValueDef($rsnew, $this->Despesas->CurrentValue, NULL, FALSE);

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
			$this->WriteAuditTrailOnAdd($rsnew);
		}
		return $AddRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, "financeirolist.php", "", $this->TableVar, TRUE);
		$PageId = ($this->CurrentAction == "C") ? "Copy" : "Add";
		$Breadcrumb->Add("add", $PageId, $url);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'financeiro';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (add page)
	function WriteAuditTrailOnAdd(&$rs) {
		if (!$this->AuditTrailOnAdd) return;
		$table = 'financeiro';

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
if (!isset($financeiro_add)) $financeiro_add = new cfinanceiro_add();

// Page init
$financeiro_add->Page_Init();

// Page main
$financeiro_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$financeiro_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var financeiro_add = new ew_Page("financeiro_add");
financeiro_add.PageID = "add"; // Page ID
var EW_PAGE_ID = financeiro_add.PageID; // For backward compatibility

// Form object
var ffinanceiroadd = new ew_Form("ffinanceiroadd");

// Validate form
ffinanceiroadd.Validate = function() {
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
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $financeiro->Tipo->FldCaption(), $financeiro->Tipo->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Tipo_Recebimento");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $financeiro->Tipo_Recebimento->FldCaption(), $financeiro->Tipo_Recebimento->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_FormaPagto");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $financeiro->FormaPagto->FldCaption(), $financeiro->FormaPagto->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Conta_Caixa");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $financeiro->Conta_Caixa->FldCaption(), $financeiro->Conta_Caixa->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Situacao");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $financeiro->Situacao->FldCaption(), $financeiro->Situacao->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Descricao");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $financeiro->Descricao->FldCaption(), $financeiro->Descricao->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Receitas");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $financeiro->Receitas->FldCaption(), $financeiro->Receitas->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Receitas");
			if (elm && !ew_CheckNumber(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($financeiro->Receitas->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Despesas");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $financeiro->Despesas->FldCaption(), $financeiro->Despesas->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Despesas");
			if (elm && !ew_CheckNumber(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($financeiro->Despesas->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Dt_Lancamento");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $financeiro->Dt_Lancamento->FldCaption(), $financeiro->Dt_Lancamento->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Dt_Lancamento");
			if (elm && !ew_CheckEuroDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($financeiro->Dt_Lancamento->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Vencimento");
			if (elm && !ew_CheckEuroDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($financeiro->Vencimento->FldErrMsg()) ?>");

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
ffinanceiroadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ffinanceiroadd.ValidateRequired = true;
<?php } else { ?>
ffinanceiroadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
ffinanceiroadd.Lists["x_FormaPagto"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Forma_Pagto","","",""],"ParentFields":["x_Tipo_Recebimento"],"FilterFields":["x_filtro_tipo_recebimento"],"Options":[]};
ffinanceiroadd.Lists["x_Conta_Caixa"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Conta_Caixa","","",""],"ParentFields":["x_Tipo"],"FilterFields":["x_Tipo"],"Options":[]};
ffinanceiroadd.Lists["x_Situacao"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Situacao","","",""],"ParentFields":["x_Tipo"],"FilterFields":["x_id_tipo"],"Options":[]};
ffinanceiroadd.Lists["x_Centro_de_Custo"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Conta","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

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
<?php $financeiro_add->ShowPageHeader(); ?>
<?php
$financeiro_add->ShowMessage();
?>
<form name="ffinanceiroadd" id="ffinanceiroadd" class="form-horizontal ewForm ewAddForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($financeiro_add->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $financeiro_add->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="financeiro">
<input type="hidden" name="a_add" id="a_add" value="A">
<div>
<?php if ($financeiro->Tipo->Visible) { // Tipo ?>
	<div id="r_Tipo" class="form-group">
		<label id="elh_financeiro_Tipo" for="x_Tipo" class="col-sm-2 control-label ewLabel"><?php echo $financeiro->Tipo->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $financeiro->Tipo->CellAttributes() ?>>
<span id="el_financeiro_Tipo">
<?php $financeiro->Tipo->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x_Conta_Caixa','x_Situacao']); " . @$financeiro->Tipo->EditAttrs["onchange"]; ?>
<select data-field="x_Tipo" id="x_Tipo" name="x_Tipo"<?php echo $financeiro->Tipo->EditAttributes() ?>>
<?php
if (is_array($financeiro->Tipo->EditValue)) {
	$arwrk = $financeiro->Tipo->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($financeiro->Tipo->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php echo $financeiro->Tipo->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($financeiro->Tipo_Recebimento->Visible) { // Tipo_Recebimento ?>
	<div id="r_Tipo_Recebimento" class="form-group">
		<label id="elh_financeiro_Tipo_Recebimento" for="x_Tipo_Recebimento" class="col-sm-2 control-label ewLabel"><?php echo $financeiro->Tipo_Recebimento->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $financeiro->Tipo_Recebimento->CellAttributes() ?>>
<span id="el_financeiro_Tipo_Recebimento">
<?php $financeiro->Tipo_Recebimento->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x_FormaPagto']); " . @$financeiro->Tipo_Recebimento->EditAttrs["onchange"]; ?>
<select data-field="x_Tipo_Recebimento" id="x_Tipo_Recebimento" name="x_Tipo_Recebimento"<?php echo $financeiro->Tipo_Recebimento->EditAttributes() ?>>
<?php
if (is_array($financeiro->Tipo_Recebimento->EditValue)) {
	$arwrk = $financeiro->Tipo_Recebimento->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($financeiro->Tipo_Recebimento->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php echo $financeiro->Tipo_Recebimento->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($financeiro->FormaPagto->Visible) { // FormaPagto ?>
	<div id="r_FormaPagto" class="form-group">
		<label id="elh_financeiro_FormaPagto" for="x_FormaPagto" class="col-sm-2 control-label ewLabel"><?php echo $financeiro->FormaPagto->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $financeiro->FormaPagto->CellAttributes() ?>>
<span id="el_financeiro_FormaPagto">
<select data-field="x_FormaPagto" id="x_FormaPagto" name="x_FormaPagto"<?php echo $financeiro->FormaPagto->EditAttributes() ?>>
<?php
if (is_array($financeiro->FormaPagto->EditValue)) {
	$arwrk = $financeiro->FormaPagto->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($financeiro->FormaPagto->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
ffinanceiroadd.Lists["x_FormaPagto"].Options = <?php echo (is_array($financeiro->FormaPagto->EditValue)) ? ew_ArrayToJson($financeiro->FormaPagto->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $financeiro->FormaPagto->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($financeiro->Conta_Caixa->Visible) { // Conta_Caixa ?>
	<div id="r_Conta_Caixa" class="form-group">
		<label id="elh_financeiro_Conta_Caixa" for="x_Conta_Caixa" class="col-sm-2 control-label ewLabel"><?php echo $financeiro->Conta_Caixa->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $financeiro->Conta_Caixa->CellAttributes() ?>>
<span id="el_financeiro_Conta_Caixa">
<select data-field="x_Conta_Caixa" id="x_Conta_Caixa" name="x_Conta_Caixa"<?php echo $financeiro->Conta_Caixa->EditAttributes() ?>>
<?php
if (is_array($financeiro->Conta_Caixa->EditValue)) {
	$arwrk = $financeiro->Conta_Caixa->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($financeiro->Conta_Caixa->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php if (AllowAdd(CurrentProjectID() . "fin_conta_caixa")) { ?>
<button type="button" title="<?php echo ew_HtmlTitle($Language->Phrase("AddLink")) . "&nbsp;" . $financeiro->Conta_Caixa->FldCaption() ?>" onclick="ew_AddOptDialogShow({lnk:this,el:'x_Conta_Caixa',url:'fin_conta_caixaaddopt.php'});" class="ewAddOptBtn btn btn-default btn-sm" id="aol_x_Conta_Caixa"><span class="glyphicon glyphicon-plus ewIcon"></span><span class="hide"><?php echo $Language->Phrase("AddLink") ?>&nbsp;<?php echo $financeiro->Conta_Caixa->FldCaption() ?></span></button>
<?php } ?>
<script type="text/javascript">
ffinanceiroadd.Lists["x_Conta_Caixa"].Options = <?php echo (is_array($financeiro->Conta_Caixa->EditValue)) ? ew_ArrayToJson($financeiro->Conta_Caixa->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $financeiro->Conta_Caixa->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($financeiro->Situacao->Visible) { // Situacao ?>
	<div id="r_Situacao" class="form-group">
		<label id="elh_financeiro_Situacao" class="col-sm-2 control-label ewLabel"><?php echo $financeiro->Situacao->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $financeiro->Situacao->CellAttributes() ?>>
<span id="el_financeiro_Situacao">
<div id="tp_x_Situacao" class="<?php echo EW_ITEM_TEMPLATE_CLASSNAME ?>"><input type="radio" name="x_Situacao" id="x_Situacao" value="{value}"<?php echo $financeiro->Situacao->EditAttributes() ?>></div>
<div id="dsl_x_Situacao" data-repeatcolumn="5" class="ewItemList">
<?php
$arwrk = $financeiro->Situacao->EditValue;
if (is_array($arwrk)) {
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($financeiro->Situacao->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " checked=\"checked\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;

		// Note: No spacing within the LABEL tag
?>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 1) ?>
<label class="radio-inline"><input type="radio" data-field="x_Situacao" name="x_Situacao" id="x_Situacao_<?php echo $rowcntwrk ?>" value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?><?php echo $financeiro->Situacao->EditAttributes() ?>><?php echo $arwrk[$rowcntwrk][1] ?></label>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 2) ?>
<?php
	}
}
?>
</div>
<script type="text/javascript">
ffinanceiroadd.Lists["x_Situacao"].Options = <?php echo (is_array($financeiro->Situacao->EditValue)) ? ew_ArrayToJson($financeiro->Situacao->EditValue, 0) : "[]" ?>;
</script>
</span>
<?php echo $financeiro->Situacao->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($financeiro->Descricao->Visible) { // Descricao ?>
	<div id="r_Descricao" class="form-group">
		<label id="elh_financeiro_Descricao" for="x_Descricao" class="col-sm-2 control-label ewLabel"><?php echo $financeiro->Descricao->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $financeiro->Descricao->CellAttributes() ?>>
<span id="el_financeiro_Descricao">
<input type="text" data-field="x_Descricao" name="x_Descricao" id="x_Descricao" size="60" maxlength="60" value="<?php echo $financeiro->Descricao->EditValue ?>"<?php echo $financeiro->Descricao->EditAttributes() ?>>
</span>
<?php echo $financeiro->Descricao->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($financeiro->Receitas->Visible) { // Receitas ?>
	<div id="r_Receitas" class="form-group">
		<label id="elh_financeiro_Receitas" for="x_Receitas" class="col-sm-2 control-label ewLabel"><?php echo $financeiro->Receitas->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $financeiro->Receitas->CellAttributes() ?>>
<span id="el_financeiro_Receitas">
<input type="text" data-field="x_Receitas" name="x_Receitas" id="x_Receitas" size="15" value="<?php echo $financeiro->Receitas->EditValue ?>"<?php echo $financeiro->Receitas->EditAttributes() ?>>
</span>
<?php echo $financeiro->Receitas->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($financeiro->Despesas->Visible) { // Despesas ?>
	<div id="r_Despesas" class="form-group">
		<label id="elh_financeiro_Despesas" for="x_Despesas" class="col-sm-2 control-label ewLabel"><?php echo $financeiro->Despesas->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $financeiro->Despesas->CellAttributes() ?>>
<span id="el_financeiro_Despesas">
<input type="text" data-field="x_Despesas" name="x_Despesas" id="x_Despesas" size="15" value="<?php echo $financeiro->Despesas->EditValue ?>"<?php echo $financeiro->Despesas->EditAttributes() ?>>
</span>
<?php echo $financeiro->Despesas->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($financeiro->N_Documento->Visible) { // N_Documento ?>
	<div id="r_N_Documento" class="form-group">
		<label id="elh_financeiro_N_Documento" for="x_N_Documento" class="col-sm-2 control-label ewLabel"><?php echo $financeiro->N_Documento->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $financeiro->N_Documento->CellAttributes() ?>>
<span id="el_financeiro_N_Documento">
<input type="text" data-field="x_N_Documento" name="x_N_Documento" id="x_N_Documento" size="20" maxlength="20" value="<?php echo $financeiro->N_Documento->EditValue ?>"<?php echo $financeiro->N_Documento->EditAttributes() ?>>
</span>
<?php echo $financeiro->N_Documento->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($financeiro->Dt_Lancamento->Visible) { // Dt_Lancamento ?>
	<div id="r_Dt_Lancamento" class="form-group">
		<label id="elh_financeiro_Dt_Lancamento" for="x_Dt_Lancamento" class="col-sm-2 control-label ewLabel"><?php echo $financeiro->Dt_Lancamento->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $financeiro->Dt_Lancamento->CellAttributes() ?>>
<span id="el_financeiro_Dt_Lancamento">
<input type="text" data-field="x_Dt_Lancamento" name="x_Dt_Lancamento" id="x_Dt_Lancamento" size="10" value="<?php echo $financeiro->Dt_Lancamento->EditValue ?>"<?php echo $financeiro->Dt_Lancamento->EditAttributes() ?>>
<?php if (!$financeiro->Dt_Lancamento->ReadOnly && !$financeiro->Dt_Lancamento->Disabled && @$financeiro->Dt_Lancamento->EditAttrs["readonly"] == "" && @$financeiro->Dt_Lancamento->EditAttrs["disabled"] == "") { ?>
<script type="text/javascript">
ew_CreateCalendar("ffinanceiroadd", "x_Dt_Lancamento", "%d/%m/%Y");
</script>
<?php } ?>
</span>
<?php echo $financeiro->Dt_Lancamento->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($financeiro->Vencimento->Visible) { // Vencimento ?>
	<div id="r_Vencimento" class="form-group">
		<label id="elh_financeiro_Vencimento" for="x_Vencimento" class="col-sm-2 control-label ewLabel"><?php echo $financeiro->Vencimento->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $financeiro->Vencimento->CellAttributes() ?>>
<span id="el_financeiro_Vencimento">
<input type="text" data-field="x_Vencimento" name="x_Vencimento" id="x_Vencimento" size="10" value="<?php echo $financeiro->Vencimento->EditValue ?>"<?php echo $financeiro->Vencimento->EditAttributes() ?>>
<?php if (!$financeiro->Vencimento->ReadOnly && !$financeiro->Vencimento->Disabled && @$financeiro->Vencimento->EditAttrs["readonly"] == "" && @$financeiro->Vencimento->EditAttrs["disabled"] == "") { ?>
<script type="text/javascript">
ew_CreateCalendar("ffinanceiroadd", "x_Vencimento", "%d/%m/%Y");
</script>
<?php } ?>
</span>
<?php echo $financeiro->Vencimento->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($financeiro->Centro_de_Custo->Visible) { // Centro_de_Custo ?>
	<div id="r_Centro_de_Custo" class="form-group">
		<label id="elh_financeiro_Centro_de_Custo" for="x_Centro_de_Custo" class="col-sm-2 control-label ewLabel"><?php echo $financeiro->Centro_de_Custo->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $financeiro->Centro_de_Custo->CellAttributes() ?>>
<span id="el_financeiro_Centro_de_Custo">
<select data-field="x_Centro_de_Custo" id="x_Centro_de_Custo" name="x_Centro_de_Custo"<?php echo $financeiro->Centro_de_Custo->EditAttributes() ?>>
<?php
if (is_array($financeiro->Centro_de_Custo->EditValue)) {
	$arwrk = $financeiro->Centro_de_Custo->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($financeiro->Centro_de_Custo->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php if (AllowAdd(CurrentProjectID() . "fin_centro_de_custo")) { ?>
<button type="button" title="<?php echo ew_HtmlTitle($Language->Phrase("AddLink")) . "&nbsp;" . $financeiro->Centro_de_Custo->FldCaption() ?>" onclick="ew_AddOptDialogShow({lnk:this,el:'x_Centro_de_Custo',url:'fin_centro_de_custoaddopt.php'});" class="ewAddOptBtn btn btn-default btn-sm" id="aol_x_Centro_de_Custo"><span class="glyphicon glyphicon-plus ewIcon"></span><span class="hide"><?php echo $Language->Phrase("AddLink") ?>&nbsp;<?php echo $financeiro->Centro_de_Custo->FldCaption() ?></span></button>
<?php } ?>
<script type="text/javascript">
ffinanceiroadd.Lists["x_Centro_de_Custo"].Options = <?php echo (is_array($financeiro->Centro_de_Custo->EditValue)) ? ew_ArrayToJson($financeiro->Centro_de_Custo->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $financeiro->Centro_de_Custo->CustomMsg ?></div></div>
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
ffinanceiroadd.Init();
</script>
<?php
$financeiro_add->ShowPageFooter();
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
$financeiro_add->Page_Terminate();
?>
