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

$funcionarios_edit = NULL; // Initialize page object first

class cfuncionarios_edit extends cfuncionarios {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{2B7992FC-5911-46A7-9310-01F4D4225C49}";

	// Table name
	var $TableName = 'funcionarios';

	// Page object name
	var $PageObjName = 'funcionarios_edit';

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

		// Table object (funcionarios)
		if (!isset($GLOBALS["funcionarios"]) || get_class($GLOBALS["funcionarios"]) == "cfuncionarios") {
			$GLOBALS["funcionarios"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["funcionarios"];
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
			define("EW_TABLE_NAME", 'funcionarios', TRUE);

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
			$this->Page_Terminate(ew_GetUrl("funcionarioslist.php"));
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
			$this->Page_Terminate("funcionarioslist.php"); // Invalid key, return to list

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
					$this->Page_Terminate("funcionarioslist.php"); // No matching record, return to list
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
		if (!$this->EhMembro->FldIsDetailKey) {
			$this->EhMembro->setFormValue($objForm->GetValue("x_EhMembro"));
		}
		if (!$this->Data_Admissao->FldIsDetailKey) {
			$this->Data_Admissao->setFormValue($objForm->GetValue("x_Data_Admissao"));
			$this->Data_Admissao->CurrentValue = ew_UnFormatDateTime($this->Data_Admissao->CurrentValue, 7);
		}
		if (!$this->Nome->FldIsDetailKey) {
			$this->Nome->setFormValue($objForm->GetValue("x_Nome"));
		}
		if (!$this->Data_Nasc->FldIsDetailKey) {
			$this->Data_Nasc->setFormValue($objForm->GetValue("x_Data_Nasc"));
			$this->Data_Nasc->CurrentValue = ew_UnFormatDateTime($this->Data_Nasc->CurrentValue, 7);
		}
		if (!$this->Estado_Civil->FldIsDetailKey) {
			$this->Estado_Civil->setFormValue($objForm->GetValue("x_Estado_Civil"));
		}
		if (!$this->Endereco->FldIsDetailKey) {
			$this->Endereco->setFormValue($objForm->GetValue("x_Endereco"));
		}
		if (!$this->Bairro->FldIsDetailKey) {
			$this->Bairro->setFormValue($objForm->GetValue("x_Bairro"));
		}
		if (!$this->Cidade->FldIsDetailKey) {
			$this->Cidade->setFormValue($objForm->GetValue("x_Cidade"));
		}
		if (!$this->UF->FldIsDetailKey) {
			$this->UF->setFormValue($objForm->GetValue("x_UF"));
		}
		if (!$this->CEP->FldIsDetailKey) {
			$this->CEP->setFormValue($objForm->GetValue("x_CEP"));
		}
		if (!$this->Celular->FldIsDetailKey) {
			$this->Celular->setFormValue($objForm->GetValue("x_Celular"));
		}
		if (!$this->Telefone_Fixo->FldIsDetailKey) {
			$this->Telefone_Fixo->setFormValue($objForm->GetValue("x_Telefone_Fixo"));
		}
		if (!$this->_Email->FldIsDetailKey) {
			$this->_Email->setFormValue($objForm->GetValue("x__Email"));
		}
		if (!$this->Cargo->FldIsDetailKey) {
			$this->Cargo->setFormValue($objForm->GetValue("x_Cargo"));
		}
		if (!$this->Setor->FldIsDetailKey) {
			$this->Setor->setFormValue($objForm->GetValue("x_Setor"));
		}
		if (!$this->CPF->FldIsDetailKey) {
			$this->CPF->setFormValue($objForm->GetValue("x_CPF"));
		}
		if (!$this->RG->FldIsDetailKey) {
			$this->RG->setFormValue($objForm->GetValue("x_RG"));
		}
		if (!$this->Org_Exp->FldIsDetailKey) {
			$this->Org_Exp->setFormValue($objForm->GetValue("x_Org_Exp"));
		}
		if (!$this->Data_Expedicao->FldIsDetailKey) {
			$this->Data_Expedicao->setFormValue($objForm->GetValue("x_Data_Expedicao"));
			$this->Data_Expedicao->CurrentValue = ew_UnFormatDateTime($this->Data_Expedicao->CurrentValue, 7);
		}
		if (!$this->CTPS_N->FldIsDetailKey) {
			$this->CTPS_N->setFormValue($objForm->GetValue("x_CTPS_N"));
		}
		if (!$this->CTPS_Serie->FldIsDetailKey) {
			$this->CTPS_Serie->setFormValue($objForm->GetValue("x_CTPS_Serie"));
		}
		if (!$this->Titulo_Eleitor->FldIsDetailKey) {
			$this->Titulo_Eleitor->setFormValue($objForm->GetValue("x_Titulo_Eleitor"));
		}
		if (!$this->Numero_Filhos->FldIsDetailKey) {
			$this->Numero_Filhos->setFormValue($objForm->GetValue("x_Numero_Filhos"));
		}
		if (!$this->Escolaridade->FldIsDetailKey) {
			$this->Escolaridade->setFormValue($objForm->GetValue("x_Escolaridade"));
		}
		if (!$this->Situacao->FldIsDetailKey) {
			$this->Situacao->setFormValue($objForm->GetValue("x_Situacao"));
		}
		if (!$this->Qual_ano->FldIsDetailKey) {
			$this->Qual_ano->setFormValue($objForm->GetValue("x_Qual_ano"));
		}
		if (!$this->Observacoes->FldIsDetailKey) {
			$this->Observacoes->setFormValue($objForm->GetValue("x_Observacoes"));
		}
		if (!$this->Inativo->FldIsDetailKey) {
			$this->Inativo->setFormValue($objForm->GetValue("x_Inativo"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->Id->CurrentValue = $this->Id->FormValue;
		$this->EhMembro->CurrentValue = $this->EhMembro->FormValue;
		$this->Data_Admissao->CurrentValue = $this->Data_Admissao->FormValue;
		$this->Data_Admissao->CurrentValue = ew_UnFormatDateTime($this->Data_Admissao->CurrentValue, 7);
		$this->Nome->CurrentValue = $this->Nome->FormValue;
		$this->Data_Nasc->CurrentValue = $this->Data_Nasc->FormValue;
		$this->Data_Nasc->CurrentValue = ew_UnFormatDateTime($this->Data_Nasc->CurrentValue, 7);
		$this->Estado_Civil->CurrentValue = $this->Estado_Civil->FormValue;
		$this->Endereco->CurrentValue = $this->Endereco->FormValue;
		$this->Bairro->CurrentValue = $this->Bairro->FormValue;
		$this->Cidade->CurrentValue = $this->Cidade->FormValue;
		$this->UF->CurrentValue = $this->UF->FormValue;
		$this->CEP->CurrentValue = $this->CEP->FormValue;
		$this->Celular->CurrentValue = $this->Celular->FormValue;
		$this->Telefone_Fixo->CurrentValue = $this->Telefone_Fixo->FormValue;
		$this->_Email->CurrentValue = $this->_Email->FormValue;
		$this->Cargo->CurrentValue = $this->Cargo->FormValue;
		$this->Setor->CurrentValue = $this->Setor->FormValue;
		$this->CPF->CurrentValue = $this->CPF->FormValue;
		$this->RG->CurrentValue = $this->RG->FormValue;
		$this->Org_Exp->CurrentValue = $this->Org_Exp->FormValue;
		$this->Data_Expedicao->CurrentValue = $this->Data_Expedicao->FormValue;
		$this->Data_Expedicao->CurrentValue = ew_UnFormatDateTime($this->Data_Expedicao->CurrentValue, 7);
		$this->CTPS_N->CurrentValue = $this->CTPS_N->FormValue;
		$this->CTPS_Serie->CurrentValue = $this->CTPS_Serie->FormValue;
		$this->Titulo_Eleitor->CurrentValue = $this->Titulo_Eleitor->FormValue;
		$this->Numero_Filhos->CurrentValue = $this->Numero_Filhos->FormValue;
		$this->Escolaridade->CurrentValue = $this->Escolaridade->FormValue;
		$this->Situacao->CurrentValue = $this->Situacao->FormValue;
		$this->Qual_ano->CurrentValue = $this->Qual_ano->FormValue;
		$this->Observacoes->CurrentValue = $this->Observacoes->FormValue;
		$this->Inativo->CurrentValue = $this->Inativo->FormValue;
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

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// Id
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

			// Id
			$this->Id->ViewValue = $this->Id->CurrentValue;
			$this->Id->ViewCustomAttributes = "";

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

			// Id
			$this->Id->LinkCustomAttributes = "";
			$this->Id->HrefValue = "";
			$this->Id->TooltipValue = "";

			// EhMembro
			$this->EhMembro->LinkCustomAttributes = "";
			$this->EhMembro->HrefValue = "";
			$this->EhMembro->TooltipValue = "";

			// Data_Admissao
			$this->Data_Admissao->LinkCustomAttributes = "";
			$this->Data_Admissao->HrefValue = "";
			$this->Data_Admissao->TooltipValue = "";

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

			// UF
			$this->UF->LinkCustomAttributes = "";
			$this->UF->HrefValue = "";
			$this->UF->TooltipValue = "";

			// CEP
			$this->CEP->LinkCustomAttributes = "";
			$this->CEP->HrefValue = "";
			$this->CEP->TooltipValue = "";

			// Celular
			$this->Celular->LinkCustomAttributes = "";
			$this->Celular->HrefValue = "";
			$this->Celular->TooltipValue = "";

			// Telefone Fixo
			$this->Telefone_Fixo->LinkCustomAttributes = "";
			$this->Telefone_Fixo->HrefValue = "";
			$this->Telefone_Fixo->TooltipValue = "";

			// Email
			$this->_Email->LinkCustomAttributes = "";
			$this->_Email->HrefValue = "";
			$this->_Email->TooltipValue = "";

			// Cargo
			$this->Cargo->LinkCustomAttributes = "";
			$this->Cargo->HrefValue = "";
			$this->Cargo->TooltipValue = "";

			// Setor
			$this->Setor->LinkCustomAttributes = "";
			$this->Setor->HrefValue = "";
			$this->Setor->TooltipValue = "";

			// CPF
			$this->CPF->LinkCustomAttributes = "";
			$this->CPF->HrefValue = "";
			$this->CPF->TooltipValue = "";

			// RG
			$this->RG->LinkCustomAttributes = "";
			$this->RG->HrefValue = "";
			$this->RG->TooltipValue = "";

			// Org_Exp
			$this->Org_Exp->LinkCustomAttributes = "";
			$this->Org_Exp->HrefValue = "";
			$this->Org_Exp->TooltipValue = "";

			// Data_Expedicao
			$this->Data_Expedicao->LinkCustomAttributes = "";
			$this->Data_Expedicao->HrefValue = "";
			$this->Data_Expedicao->TooltipValue = "";

			// CTPS_N
			$this->CTPS_N->LinkCustomAttributes = "";
			$this->CTPS_N->HrefValue = "";
			$this->CTPS_N->TooltipValue = "";

			// CTPS_Serie
			$this->CTPS_Serie->LinkCustomAttributes = "";
			$this->CTPS_Serie->HrefValue = "";
			$this->CTPS_Serie->TooltipValue = "";

			// Titulo_Eleitor
			$this->Titulo_Eleitor->LinkCustomAttributes = "";
			$this->Titulo_Eleitor->HrefValue = "";
			$this->Titulo_Eleitor->TooltipValue = "";

			// Numero_Filhos
			$this->Numero_Filhos->LinkCustomAttributes = "";
			$this->Numero_Filhos->HrefValue = "";
			$this->Numero_Filhos->TooltipValue = "";

			// Escolaridade
			$this->Escolaridade->LinkCustomAttributes = "";
			$this->Escolaridade->HrefValue = "";
			$this->Escolaridade->TooltipValue = "";

			// Situacao
			$this->Situacao->LinkCustomAttributes = "";
			$this->Situacao->HrefValue = "";
			$this->Situacao->TooltipValue = "";

			// Qual_ano
			$this->Qual_ano->LinkCustomAttributes = "";
			$this->Qual_ano->HrefValue = "";
			$this->Qual_ano->TooltipValue = "";

			// Observacoes
			$this->Observacoes->LinkCustomAttributes = "";
			$this->Observacoes->HrefValue = "";
			$this->Observacoes->TooltipValue = "";

			// Inativo
			$this->Inativo->LinkCustomAttributes = "";
			$this->Inativo->HrefValue = "";
			$this->Inativo->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// Id
			$this->Id->EditAttrs["class"] = "form-control";
			$this->Id->EditCustomAttributes = "";

			// EhMembro
			$this->EhMembro->EditAttrs["class"] = "form-control";
			$this->EhMembro->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->EhMembro->FldTagValue(1), $this->EhMembro->FldTagCaption(1) <> "" ? $this->EhMembro->FldTagCaption(1) : $this->EhMembro->FldTagValue(1));
			$arwrk[] = array($this->EhMembro->FldTagValue(2), $this->EhMembro->FldTagCaption(2) <> "" ? $this->EhMembro->FldTagCaption(2) : $this->EhMembro->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->EhMembro->EditValue = $arwrk;

			// Data_Admissao
			$this->Data_Admissao->EditAttrs["class"] = "form-control";
			$this->Data_Admissao->EditCustomAttributes = "";
			$this->Data_Admissao->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->Data_Admissao->CurrentValue, 7));

			// Nome
			$this->Nome->EditAttrs["class"] = "form-control";
			$this->Nome->EditCustomAttributes = "";
			$this->Nome->EditValue = ew_HtmlEncode($this->Nome->CurrentValue);

			// Data_Nasc
			$this->Data_Nasc->EditAttrs["class"] = "form-control";
			$this->Data_Nasc->EditCustomAttributes = "";
			$this->Data_Nasc->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->Data_Nasc->CurrentValue, 7));

			// Estado_Civil
			$this->Estado_Civil->EditAttrs["class"] = "form-control";
			$this->Estado_Civil->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->Estado_Civil->FldTagValue(1), $this->Estado_Civil->FldTagCaption(1) <> "" ? $this->Estado_Civil->FldTagCaption(1) : $this->Estado_Civil->FldTagValue(1));
			$arwrk[] = array($this->Estado_Civil->FldTagValue(2), $this->Estado_Civil->FldTagCaption(2) <> "" ? $this->Estado_Civil->FldTagCaption(2) : $this->Estado_Civil->FldTagValue(2));
			$arwrk[] = array($this->Estado_Civil->FldTagValue(3), $this->Estado_Civil->FldTagCaption(3) <> "" ? $this->Estado_Civil->FldTagCaption(3) : $this->Estado_Civil->FldTagValue(3));
			$arwrk[] = array($this->Estado_Civil->FldTagValue(4), $this->Estado_Civil->FldTagCaption(4) <> "" ? $this->Estado_Civil->FldTagCaption(4) : $this->Estado_Civil->FldTagValue(4));
			$arwrk[] = array($this->Estado_Civil->FldTagValue(5), $this->Estado_Civil->FldTagCaption(5) <> "" ? $this->Estado_Civil->FldTagCaption(5) : $this->Estado_Civil->FldTagValue(5));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->Estado_Civil->EditValue = $arwrk;

			// Endereco
			$this->Endereco->EditAttrs["class"] = "form-control";
			$this->Endereco->EditCustomAttributes = "";
			$this->Endereco->EditValue = ew_HtmlEncode($this->Endereco->CurrentValue);

			// Bairro
			$this->Bairro->EditAttrs["class"] = "form-control";
			$this->Bairro->EditCustomAttributes = "";
			$this->Bairro->EditValue = ew_HtmlEncode($this->Bairro->CurrentValue);

			// Cidade
			$this->Cidade->EditAttrs["class"] = "form-control";
			$this->Cidade->EditCustomAttributes = "";
			$this->Cidade->EditValue = ew_HtmlEncode($this->Cidade->CurrentValue);

			// UF
			$this->UF->EditAttrs["class"] = "form-control";
			$this->UF->EditCustomAttributes = "";
			$this->UF->EditValue = ew_HtmlEncode($this->UF->CurrentValue);

			// CEP
			$this->CEP->EditAttrs["class"] = "form-control";
			$this->CEP->EditCustomAttributes = "";
			$this->CEP->EditValue = ew_HtmlEncode($this->CEP->CurrentValue);

			// Celular
			$this->Celular->EditAttrs["class"] = "form-control";
			$this->Celular->EditCustomAttributes = "";
			$this->Celular->EditValue = ew_HtmlEncode($this->Celular->CurrentValue);

			// Telefone Fixo
			$this->Telefone_Fixo->EditAttrs["class"] = "form-control";
			$this->Telefone_Fixo->EditCustomAttributes = "";
			$this->Telefone_Fixo->EditValue = ew_HtmlEncode($this->Telefone_Fixo->CurrentValue);

			// Email
			$this->_Email->EditAttrs["class"] = "form-control";
			$this->_Email->EditCustomAttributes = "";
			$this->_Email->EditValue = ew_HtmlEncode($this->_Email->CurrentValue);

			// Cargo
			$this->Cargo->EditAttrs["class"] = "form-control";
			$this->Cargo->EditCustomAttributes = "";
			$this->Cargo->EditValue = ew_HtmlEncode($this->Cargo->CurrentValue);

			// Setor
			$this->Setor->EditAttrs["class"] = "form-control";
			$this->Setor->EditCustomAttributes = "";
			$this->Setor->EditValue = ew_HtmlEncode($this->Setor->CurrentValue);

			// CPF
			$this->CPF->EditAttrs["class"] = "form-control";
			$this->CPF->EditCustomAttributes = "";
			$this->CPF->EditValue = ew_HtmlEncode($this->CPF->CurrentValue);

			// RG
			$this->RG->EditAttrs["class"] = "form-control";
			$this->RG->EditCustomAttributes = "";
			$this->RG->EditValue = ew_HtmlEncode($this->RG->CurrentValue);

			// Org_Exp
			$this->Org_Exp->EditAttrs["class"] = "form-control";
			$this->Org_Exp->EditCustomAttributes = "";
			$this->Org_Exp->EditValue = ew_HtmlEncode($this->Org_Exp->CurrentValue);

			// Data_Expedicao
			$this->Data_Expedicao->EditAttrs["class"] = "form-control";
			$this->Data_Expedicao->EditCustomAttributes = "";
			$this->Data_Expedicao->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->Data_Expedicao->CurrentValue, 7));

			// CTPS_N
			$this->CTPS_N->EditAttrs["class"] = "form-control";
			$this->CTPS_N->EditCustomAttributes = "";
			$this->CTPS_N->EditValue = ew_HtmlEncode($this->CTPS_N->CurrentValue);

			// CTPS_Serie
			$this->CTPS_Serie->EditAttrs["class"] = "form-control";
			$this->CTPS_Serie->EditCustomAttributes = "";
			$this->CTPS_Serie->EditValue = ew_HtmlEncode($this->CTPS_Serie->CurrentValue);

			// Titulo_Eleitor
			$this->Titulo_Eleitor->EditAttrs["class"] = "form-control";
			$this->Titulo_Eleitor->EditCustomAttributes = "";
			$this->Titulo_Eleitor->EditValue = ew_HtmlEncode($this->Titulo_Eleitor->CurrentValue);

			// Numero_Filhos
			$this->Numero_Filhos->EditAttrs["class"] = "form-control";
			$this->Numero_Filhos->EditCustomAttributes = "";
			$this->Numero_Filhos->EditValue = ew_HtmlEncode($this->Numero_Filhos->CurrentValue);

			// Escolaridade
			$this->Escolaridade->EditAttrs["class"] = "form-control";
			$this->Escolaridade->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->Escolaridade->FldTagValue(1), $this->Escolaridade->FldTagCaption(1) <> "" ? $this->Escolaridade->FldTagCaption(1) : $this->Escolaridade->FldTagValue(1));
			$arwrk[] = array($this->Escolaridade->FldTagValue(2), $this->Escolaridade->FldTagCaption(2) <> "" ? $this->Escolaridade->FldTagCaption(2) : $this->Escolaridade->FldTagValue(2));
			$arwrk[] = array($this->Escolaridade->FldTagValue(3), $this->Escolaridade->FldTagCaption(3) <> "" ? $this->Escolaridade->FldTagCaption(3) : $this->Escolaridade->FldTagValue(3));
			$arwrk[] = array($this->Escolaridade->FldTagValue(4), $this->Escolaridade->FldTagCaption(4) <> "" ? $this->Escolaridade->FldTagCaption(4) : $this->Escolaridade->FldTagValue(4));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->Escolaridade->EditValue = $arwrk;

			// Situacao
			$this->Situacao->EditAttrs["class"] = "form-control";
			$this->Situacao->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->Situacao->FldTagValue(1), $this->Situacao->FldTagCaption(1) <> "" ? $this->Situacao->FldTagCaption(1) : $this->Situacao->FldTagValue(1));
			$arwrk[] = array($this->Situacao->FldTagValue(2), $this->Situacao->FldTagCaption(2) <> "" ? $this->Situacao->FldTagCaption(2) : $this->Situacao->FldTagValue(2));
			$arwrk[] = array($this->Situacao->FldTagValue(3), $this->Situacao->FldTagCaption(3) <> "" ? $this->Situacao->FldTagCaption(3) : $this->Situacao->FldTagValue(3));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->Situacao->EditValue = $arwrk;

			// Qual_ano
			$this->Qual_ano->EditAttrs["class"] = "form-control";
			$this->Qual_ano->EditCustomAttributes = "";
			$this->Qual_ano->EditValue = ew_HtmlEncode($this->Qual_ano->CurrentValue);

			// Observacoes
			$this->Observacoes->EditAttrs["class"] = "form-control";
			$this->Observacoes->EditCustomAttributes = "";
			$this->Observacoes->EditValue = ew_HtmlEncode($this->Observacoes->CurrentValue);

			// Inativo
			$this->Inativo->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->Inativo->FldTagValue(1), $this->Inativo->FldTagCaption(1) <> "" ? $this->Inativo->FldTagCaption(1) : $this->Inativo->FldTagValue(1));
			$this->Inativo->EditValue = $arwrk;

			// Edit refer script
			// Id

			$this->Id->HrefValue = "";

			// EhMembro
			$this->EhMembro->HrefValue = "";

			// Data_Admissao
			$this->Data_Admissao->HrefValue = "";

			// Nome
			$this->Nome->HrefValue = "";

			// Data_Nasc
			$this->Data_Nasc->HrefValue = "";

			// Estado_Civil
			$this->Estado_Civil->HrefValue = "";

			// Endereco
			$this->Endereco->HrefValue = "";

			// Bairro
			$this->Bairro->HrefValue = "";

			// Cidade
			$this->Cidade->HrefValue = "";

			// UF
			$this->UF->HrefValue = "";

			// CEP
			$this->CEP->HrefValue = "";

			// Celular
			$this->Celular->HrefValue = "";

			// Telefone Fixo
			$this->Telefone_Fixo->HrefValue = "";

			// Email
			$this->_Email->HrefValue = "";

			// Cargo
			$this->Cargo->HrefValue = "";

			// Setor
			$this->Setor->HrefValue = "";

			// CPF
			$this->CPF->HrefValue = "";

			// RG
			$this->RG->HrefValue = "";

			// Org_Exp
			$this->Org_Exp->HrefValue = "";

			// Data_Expedicao
			$this->Data_Expedicao->HrefValue = "";

			// CTPS_N
			$this->CTPS_N->HrefValue = "";

			// CTPS_Serie
			$this->CTPS_Serie->HrefValue = "";

			// Titulo_Eleitor
			$this->Titulo_Eleitor->HrefValue = "";

			// Numero_Filhos
			$this->Numero_Filhos->HrefValue = "";

			// Escolaridade
			$this->Escolaridade->HrefValue = "";

			// Situacao
			$this->Situacao->HrefValue = "";

			// Qual_ano
			$this->Qual_ano->HrefValue = "";

			// Observacoes
			$this->Observacoes->HrefValue = "";

			// Inativo
			$this->Inativo->HrefValue = "";
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
		if (!$this->EhMembro->FldIsDetailKey && !is_null($this->EhMembro->FormValue) && $this->EhMembro->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->EhMembro->FldCaption(), $this->EhMembro->ReqErrMsg));
		}
		if (!ew_CheckEuroDate($this->Data_Admissao->FormValue)) {
			ew_AddMessage($gsFormError, $this->Data_Admissao->FldErrMsg());
		}
		if (!$this->Nome->FldIsDetailKey && !is_null($this->Nome->FormValue) && $this->Nome->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Nome->FldCaption(), $this->Nome->ReqErrMsg));
		}
		if (!$this->Data_Nasc->FldIsDetailKey && !is_null($this->Data_Nasc->FormValue) && $this->Data_Nasc->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Data_Nasc->FldCaption(), $this->Data_Nasc->ReqErrMsg));
		}
		if (!ew_CheckEuroDate($this->Data_Nasc->FormValue)) {
			ew_AddMessage($gsFormError, $this->Data_Nasc->FldErrMsg());
		}
		if (!$this->Estado_Civil->FldIsDetailKey && !is_null($this->Estado_Civil->FormValue) && $this->Estado_Civil->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Estado_Civil->FldCaption(), $this->Estado_Civil->ReqErrMsg));
		}
		if (!$this->Endereco->FldIsDetailKey && !is_null($this->Endereco->FormValue) && $this->Endereco->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Endereco->FldCaption(), $this->Endereco->ReqErrMsg));
		}
		if (!$this->Bairro->FldIsDetailKey && !is_null($this->Bairro->FormValue) && $this->Bairro->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Bairro->FldCaption(), $this->Bairro->ReqErrMsg));
		}
		if (!$this->Cidade->FldIsDetailKey && !is_null($this->Cidade->FormValue) && $this->Cidade->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Cidade->FldCaption(), $this->Cidade->ReqErrMsg));
		}
		if (!$this->UF->FldIsDetailKey && !is_null($this->UF->FormValue) && $this->UF->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->UF->FldCaption(), $this->UF->ReqErrMsg));
		}
		if (!$this->CEP->FldIsDetailKey && !is_null($this->CEP->FormValue) && $this->CEP->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->CEP->FldCaption(), $this->CEP->ReqErrMsg));
		}
		if (!$this->CPF->FldIsDetailKey && !is_null($this->CPF->FormValue) && $this->CPF->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->CPF->FldCaption(), $this->CPF->ReqErrMsg));
		}
		if (!$this->RG->FldIsDetailKey && !is_null($this->RG->FormValue) && $this->RG->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->RG->FldCaption(), $this->RG->ReqErrMsg));
		}
		if (!ew_CheckEuroDate($this->Data_Expedicao->FormValue)) {
			ew_AddMessage($gsFormError, $this->Data_Expedicao->FldErrMsg());
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

			// EhMembro
			$this->EhMembro->SetDbValueDef($rsnew, $this->EhMembro->CurrentValue, NULL, $this->EhMembro->ReadOnly);

			// Data_Admissao
			$this->Data_Admissao->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->Data_Admissao->CurrentValue, 7), NULL, $this->Data_Admissao->ReadOnly);

			// Nome
			$this->Nome->SetDbValueDef($rsnew, $this->Nome->CurrentValue, "", $this->Nome->ReadOnly);

			// Data_Nasc
			$this->Data_Nasc->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->Data_Nasc->CurrentValue, 7), NULL, $this->Data_Nasc->ReadOnly);

			// Estado_Civil
			$this->Estado_Civil->SetDbValueDef($rsnew, $this->Estado_Civil->CurrentValue, NULL, $this->Estado_Civil->ReadOnly);

			// Endereco
			$this->Endereco->SetDbValueDef($rsnew, $this->Endereco->CurrentValue, NULL, $this->Endereco->ReadOnly);

			// Bairro
			$this->Bairro->SetDbValueDef($rsnew, $this->Bairro->CurrentValue, NULL, $this->Bairro->ReadOnly);

			// Cidade
			$this->Cidade->SetDbValueDef($rsnew, $this->Cidade->CurrentValue, NULL, $this->Cidade->ReadOnly);

			// UF
			$this->UF->SetDbValueDef($rsnew, $this->UF->CurrentValue, NULL, $this->UF->ReadOnly);

			// CEP
			$this->CEP->SetDbValueDef($rsnew, $this->CEP->CurrentValue, NULL, $this->CEP->ReadOnly);

			// Celular
			$this->Celular->SetDbValueDef($rsnew, $this->Celular->CurrentValue, NULL, $this->Celular->ReadOnly);

			// Telefone Fixo
			$this->Telefone_Fixo->SetDbValueDef($rsnew, $this->Telefone_Fixo->CurrentValue, NULL, $this->Telefone_Fixo->ReadOnly);

			// Email
			$this->_Email->SetDbValueDef($rsnew, $this->_Email->CurrentValue, NULL, $this->_Email->ReadOnly);

			// Cargo
			$this->Cargo->SetDbValueDef($rsnew, $this->Cargo->CurrentValue, NULL, $this->Cargo->ReadOnly);

			// Setor
			$this->Setor->SetDbValueDef($rsnew, $this->Setor->CurrentValue, NULL, $this->Setor->ReadOnly);

			// CPF
			$this->CPF->SetDbValueDef($rsnew, $this->CPF->CurrentValue, NULL, $this->CPF->ReadOnly);

			// RG
			$this->RG->SetDbValueDef($rsnew, $this->RG->CurrentValue, NULL, $this->RG->ReadOnly);

			// Org_Exp
			$this->Org_Exp->SetDbValueDef($rsnew, $this->Org_Exp->CurrentValue, NULL, $this->Org_Exp->ReadOnly);

			// Data_Expedicao
			$this->Data_Expedicao->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->Data_Expedicao->CurrentValue, 7), NULL, $this->Data_Expedicao->ReadOnly);

			// CTPS_N
			$this->CTPS_N->SetDbValueDef($rsnew, $this->CTPS_N->CurrentValue, NULL, $this->CTPS_N->ReadOnly);

			// CTPS_Serie
			$this->CTPS_Serie->SetDbValueDef($rsnew, $this->CTPS_Serie->CurrentValue, NULL, $this->CTPS_Serie->ReadOnly);

			// Titulo_Eleitor
			$this->Titulo_Eleitor->SetDbValueDef($rsnew, $this->Titulo_Eleitor->CurrentValue, NULL, $this->Titulo_Eleitor->ReadOnly);

			// Numero_Filhos
			$this->Numero_Filhos->SetDbValueDef($rsnew, $this->Numero_Filhos->CurrentValue, NULL, $this->Numero_Filhos->ReadOnly);

			// Escolaridade
			$this->Escolaridade->SetDbValueDef($rsnew, $this->Escolaridade->CurrentValue, NULL, $this->Escolaridade->ReadOnly);

			// Situacao
			$this->Situacao->SetDbValueDef($rsnew, $this->Situacao->CurrentValue, NULL, $this->Situacao->ReadOnly);

			// Qual_ano
			$this->Qual_ano->SetDbValueDef($rsnew, $this->Qual_ano->CurrentValue, NULL, $this->Qual_ano->ReadOnly);

			// Observacoes
			$this->Observacoes->SetDbValueDef($rsnew, $this->Observacoes->CurrentValue, NULL, $this->Observacoes->ReadOnly);

			// Inativo
			$this->Inativo->SetDbValueDef($rsnew, $this->Inativo->CurrentValue, NULL, $this->Inativo->ReadOnly);

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
		$Breadcrumb->Add("list", $this->TableVar, "funcionarioslist.php", "", $this->TableVar, TRUE);
		$PageId = "edit";
		$Breadcrumb->Add("edit", $PageId, $url);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'funcionarios';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (edit page)
	function WriteAuditTrailOnEdit(&$rsold, &$rsnew) {
		if (!$this->AuditTrailOnEdit) return;
		$table = 'funcionarios';

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
if (!isset($funcionarios_edit)) $funcionarios_edit = new cfuncionarios_edit();

// Page init
$funcionarios_edit->Page_Init();

// Page main
$funcionarios_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$funcionarios_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var funcionarios_edit = new ew_Page("funcionarios_edit");
funcionarios_edit.PageID = "edit"; // Page ID
var EW_PAGE_ID = funcionarios_edit.PageID; // For backward compatibility

// Form object
var ffuncionariosedit = new ew_Form("ffuncionariosedit");

// Validate form
ffuncionariosedit.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_EhMembro");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $funcionarios->EhMembro->FldCaption(), $funcionarios->EhMembro->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Data_Admissao");
			if (elm && !ew_CheckEuroDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($funcionarios->Data_Admissao->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Nome");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $funcionarios->Nome->FldCaption(), $funcionarios->Nome->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Data_Nasc");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $funcionarios->Data_Nasc->FldCaption(), $funcionarios->Data_Nasc->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Data_Nasc");
			if (elm && !ew_CheckEuroDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($funcionarios->Data_Nasc->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Estado_Civil");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $funcionarios->Estado_Civil->FldCaption(), $funcionarios->Estado_Civil->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Endereco");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $funcionarios->Endereco->FldCaption(), $funcionarios->Endereco->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Bairro");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $funcionarios->Bairro->FldCaption(), $funcionarios->Bairro->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Cidade");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $funcionarios->Cidade->FldCaption(), $funcionarios->Cidade->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_UF");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $funcionarios->UF->FldCaption(), $funcionarios->UF->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_CEP");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $funcionarios->CEP->FldCaption(), $funcionarios->CEP->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_CPF");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $funcionarios->CPF->FldCaption(), $funcionarios->CPF->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_RG");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $funcionarios->RG->FldCaption(), $funcionarios->RG->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Data_Expedicao");
			if (elm && !ew_CheckEuroDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($funcionarios->Data_Expedicao->FldErrMsg()) ?>");

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
ffuncionariosedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ffuncionariosedit.ValidateRequired = true;
<?php } else { ?>
ffuncionariosedit.ValidateRequired = false; 
<?php } ?>

// Multi-Page properties
ffuncionariosedit.MultiPage = new ew_MultiPage("ffuncionariosedit",
	[["x_EhMembro",1],["x_Data_Admissao",1],["x_Nome",1],["x_Data_Nasc",1],["x_Estado_Civil",1],["x_Endereco",2],["x_Bairro",2],["x_Cidade",2],["x_UF",2],["x_CEP",2],["x_Celular",1],["x_Telefone_Fixo",1],["x__Email",1],["x_Cargo",1],["x_Setor",1],["x_CPF",3],["x_RG",3],["x_Org_Exp",3],["x_Data_Expedicao",3],["x_CTPS_N",3],["x_CTPS_Serie",3],["x_Titulo_Eleitor",3],["x_Numero_Filhos",2],["x_Escolaridade",2],["x_Situacao",2],["x_Qual_ano",2],["x_Observacoes",3],["x_Inativo",3]]
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
<?php $funcionarios_edit->ShowPageHeader(); ?>
<?php
$funcionarios_edit->ShowMessage();
?>
<form name="ffuncionariosedit" id="ffuncionariosedit" class="form-horizontal ewForm ewEditForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($funcionarios_edit->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $funcionarios_edit->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="funcionarios">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<div>
<div class="tabbable" id="funcionarios_edit">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab_funcionarios1" data-toggle="tab"><?php echo $funcionarios->PageCaption(1) ?></a></li>
		<li><a href="#tab_funcionarios2" data-toggle="tab"><?php echo $funcionarios->PageCaption(2) ?></a></li>
		<li><a href="#tab_funcionarios3" data-toggle="tab"><?php echo $funcionarios->PageCaption(3) ?></a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="tab_funcionarios1">
<div>
<?php if ($funcionarios->EhMembro->Visible) { // EhMembro ?>
	<div id="r_EhMembro" class="form-group">
		<label id="elh_funcionarios_EhMembro" for="x_EhMembro" class="col-sm-2 control-label ewLabel"><?php echo $funcionarios->EhMembro->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $funcionarios->EhMembro->CellAttributes() ?>>
<span id="el_funcionarios_EhMembro">
<select data-field="x_EhMembro" id="x_EhMembro" name="x_EhMembro"<?php echo $funcionarios->EhMembro->EditAttributes() ?>>
<?php
if (is_array($funcionarios->EhMembro->EditValue)) {
	$arwrk = $funcionarios->EhMembro->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($funcionarios->EhMembro->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php echo $funcionarios->EhMembro->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($funcionarios->Data_Admissao->Visible) { // Data_Admissao ?>
	<div id="r_Data_Admissao" class="form-group">
		<label id="elh_funcionarios_Data_Admissao" for="x_Data_Admissao" class="col-sm-2 control-label ewLabel"><?php echo $funcionarios->Data_Admissao->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $funcionarios->Data_Admissao->CellAttributes() ?>>
<span id="el_funcionarios_Data_Admissao">
<input type="text" data-field="x_Data_Admissao" name="x_Data_Admissao" id="x_Data_Admissao" size="10" value="<?php echo $funcionarios->Data_Admissao->EditValue ?>"<?php echo $funcionarios->Data_Admissao->EditAttributes() ?>>
</span>
<?php echo $funcionarios->Data_Admissao->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($funcionarios->Nome->Visible) { // Nome ?>
	<div id="r_Nome" class="form-group">
		<label id="elh_funcionarios_Nome" for="x_Nome" class="col-sm-2 control-label ewLabel"><?php echo $funcionarios->Nome->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $funcionarios->Nome->CellAttributes() ?>>
<span id="el_funcionarios_Nome">
<input type="text" data-field="x_Nome" name="x_Nome" id="x_Nome" size="55" maxlength="100" value="<?php echo $funcionarios->Nome->EditValue ?>"<?php echo $funcionarios->Nome->EditAttributes() ?>>
</span>
<?php echo $funcionarios->Nome->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($funcionarios->Data_Nasc->Visible) { // Data_Nasc ?>
	<div id="r_Data_Nasc" class="form-group">
		<label id="elh_funcionarios_Data_Nasc" for="x_Data_Nasc" class="col-sm-2 control-label ewLabel"><?php echo $funcionarios->Data_Nasc->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $funcionarios->Data_Nasc->CellAttributes() ?>>
<span id="el_funcionarios_Data_Nasc">
<input type="text" data-field="x_Data_Nasc" name="x_Data_Nasc" id="x_Data_Nasc" value="<?php echo $funcionarios->Data_Nasc->EditValue ?>"<?php echo $funcionarios->Data_Nasc->EditAttributes() ?>>
<?php if (!$funcionarios->Data_Nasc->ReadOnly && !$funcionarios->Data_Nasc->Disabled && @$funcionarios->Data_Nasc->EditAttrs["readonly"] == "" && @$funcionarios->Data_Nasc->EditAttrs["disabled"] == "") { ?>
<script type="text/javascript">
ew_CreateCalendar("ffuncionariosedit", "x_Data_Nasc", "%d/%m/%Y");
</script>
<?php } ?>
</span>
<?php echo $funcionarios->Data_Nasc->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($funcionarios->Estado_Civil->Visible) { // Estado_Civil ?>
	<div id="r_Estado_Civil" class="form-group">
		<label id="elh_funcionarios_Estado_Civil" for="x_Estado_Civil" class="col-sm-2 control-label ewLabel"><?php echo $funcionarios->Estado_Civil->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $funcionarios->Estado_Civil->CellAttributes() ?>>
<span id="el_funcionarios_Estado_Civil">
<select data-field="x_Estado_Civil" id="x_Estado_Civil" name="x_Estado_Civil"<?php echo $funcionarios->Estado_Civil->EditAttributes() ?>>
<?php
if (is_array($funcionarios->Estado_Civil->EditValue)) {
	$arwrk = $funcionarios->Estado_Civil->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($funcionarios->Estado_Civil->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php echo $funcionarios->Estado_Civil->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($funcionarios->Celular->Visible) { // Celular ?>
	<div id="r_Celular" class="form-group">
		<label id="elh_funcionarios_Celular" for="x_Celular" class="col-sm-2 control-label ewLabel"><?php echo $funcionarios->Celular->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $funcionarios->Celular->CellAttributes() ?>>
<span id="el_funcionarios_Celular">
<input type="text" data-field="x_Celular" name="x_Celular" id="x_Celular" size="30" maxlength="20" value="<?php echo $funcionarios->Celular->EditValue ?>"<?php echo $funcionarios->Celular->EditAttributes() ?>>
</span>
<?php echo $funcionarios->Celular->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($funcionarios->Telefone_Fixo->Visible) { // Telefone Fixo ?>
	<div id="r_Telefone_Fixo" class="form-group">
		<label id="elh_funcionarios_Telefone_Fixo" for="x_Telefone_Fixo" class="col-sm-2 control-label ewLabel"><?php echo $funcionarios->Telefone_Fixo->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $funcionarios->Telefone_Fixo->CellAttributes() ?>>
<span id="el_funcionarios_Telefone_Fixo">
<input type="text" data-field="x_Telefone_Fixo" name="x_Telefone_Fixo" id="x_Telefone_Fixo" size="30" maxlength="50" value="<?php echo $funcionarios->Telefone_Fixo->EditValue ?>"<?php echo $funcionarios->Telefone_Fixo->EditAttributes() ?>>
</span>
<?php echo $funcionarios->Telefone_Fixo->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($funcionarios->_Email->Visible) { // Email ?>
	<div id="r__Email" class="form-group">
		<label id="elh_funcionarios__Email" for="x__Email" class="col-sm-2 control-label ewLabel"><?php echo $funcionarios->_Email->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $funcionarios->_Email->CellAttributes() ?>>
<span id="el_funcionarios__Email">
<input type="text" data-field="x__Email" name="x__Email" id="x__Email" size="30" maxlength="100" value="<?php echo $funcionarios->_Email->EditValue ?>"<?php echo $funcionarios->_Email->EditAttributes() ?>>
</span>
<?php echo $funcionarios->_Email->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($funcionarios->Cargo->Visible) { // Cargo ?>
	<div id="r_Cargo" class="form-group">
		<label id="elh_funcionarios_Cargo" for="x_Cargo" class="col-sm-2 control-label ewLabel"><?php echo $funcionarios->Cargo->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $funcionarios->Cargo->CellAttributes() ?>>
<span id="el_funcionarios_Cargo">
<input type="text" data-field="x_Cargo" name="x_Cargo" id="x_Cargo" size="30" maxlength="100" value="<?php echo $funcionarios->Cargo->EditValue ?>"<?php echo $funcionarios->Cargo->EditAttributes() ?>>
</span>
<?php echo $funcionarios->Cargo->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($funcionarios->Setor->Visible) { // Setor ?>
	<div id="r_Setor" class="form-group">
		<label id="elh_funcionarios_Setor" for="x_Setor" class="col-sm-2 control-label ewLabel"><?php echo $funcionarios->Setor->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $funcionarios->Setor->CellAttributes() ?>>
<span id="el_funcionarios_Setor">
<input type="text" data-field="x_Setor" name="x_Setor" id="x_Setor" size="30" maxlength="50" value="<?php echo $funcionarios->Setor->EditValue ?>"<?php echo $funcionarios->Setor->EditAttributes() ?>>
</span>
<?php echo $funcionarios->Setor->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
		</div>
		<div class="tab-pane" id="tab_funcionarios2">
<div>
<?php if ($funcionarios->Endereco->Visible) { // Endereco ?>
	<div id="r_Endereco" class="form-group">
		<label id="elh_funcionarios_Endereco" for="x_Endereco" class="col-sm-2 control-label ewLabel"><?php echo $funcionarios->Endereco->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $funcionarios->Endereco->CellAttributes() ?>>
<span id="el_funcionarios_Endereco">
<input type="text" data-field="x_Endereco" name="x_Endereco" id="x_Endereco" size="70" maxlength="100" value="<?php echo $funcionarios->Endereco->EditValue ?>"<?php echo $funcionarios->Endereco->EditAttributes() ?>>
</span>
<?php echo $funcionarios->Endereco->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($funcionarios->Bairro->Visible) { // Bairro ?>
	<div id="r_Bairro" class="form-group">
		<label id="elh_funcionarios_Bairro" for="x_Bairro" class="col-sm-2 control-label ewLabel"><?php echo $funcionarios->Bairro->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $funcionarios->Bairro->CellAttributes() ?>>
<span id="el_funcionarios_Bairro">
<input type="text" data-field="x_Bairro" name="x_Bairro" id="x_Bairro" size="30" maxlength="50" value="<?php echo $funcionarios->Bairro->EditValue ?>"<?php echo $funcionarios->Bairro->EditAttributes() ?>>
</span>
<?php echo $funcionarios->Bairro->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($funcionarios->Cidade->Visible) { // Cidade ?>
	<div id="r_Cidade" class="form-group">
		<label id="elh_funcionarios_Cidade" for="x_Cidade" class="col-sm-2 control-label ewLabel"><?php echo $funcionarios->Cidade->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $funcionarios->Cidade->CellAttributes() ?>>
<span id="el_funcionarios_Cidade">
<input type="text" data-field="x_Cidade" name="x_Cidade" id="x_Cidade" size="30" maxlength="60" value="<?php echo $funcionarios->Cidade->EditValue ?>"<?php echo $funcionarios->Cidade->EditAttributes() ?>>
</span>
<?php echo $funcionarios->Cidade->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($funcionarios->UF->Visible) { // UF ?>
	<div id="r_UF" class="form-group">
		<label id="elh_funcionarios_UF" for="x_UF" class="col-sm-2 control-label ewLabel"><?php echo $funcionarios->UF->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $funcionarios->UF->CellAttributes() ?>>
<span id="el_funcionarios_UF">
<input type="text" data-field="x_UF" name="x_UF" id="x_UF" size="30" maxlength="2" value="<?php echo $funcionarios->UF->EditValue ?>"<?php echo $funcionarios->UF->EditAttributes() ?>>
</span>
<?php echo $funcionarios->UF->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($funcionarios->CEP->Visible) { // CEP ?>
	<div id="r_CEP" class="form-group">
		<label id="elh_funcionarios_CEP" for="x_CEP" class="col-sm-2 control-label ewLabel"><?php echo $funcionarios->CEP->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $funcionarios->CEP->CellAttributes() ?>>
<span id="el_funcionarios_CEP">
<input type="text" data-field="x_CEP" name="x_CEP" id="x_CEP" size="10" maxlength="10" value="<?php echo $funcionarios->CEP->EditValue ?>"<?php echo $funcionarios->CEP->EditAttributes() ?>>
</span>
<?php echo $funcionarios->CEP->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($funcionarios->Numero_Filhos->Visible) { // Numero_Filhos ?>
	<div id="r_Numero_Filhos" class="form-group">
		<label id="elh_funcionarios_Numero_Filhos" for="x_Numero_Filhos" class="col-sm-2 control-label ewLabel"><?php echo $funcionarios->Numero_Filhos->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $funcionarios->Numero_Filhos->CellAttributes() ?>>
<span id="el_funcionarios_Numero_Filhos">
<input type="text" data-field="x_Numero_Filhos" name="x_Numero_Filhos" id="x_Numero_Filhos" size="5" maxlength="5" value="<?php echo $funcionarios->Numero_Filhos->EditValue ?>"<?php echo $funcionarios->Numero_Filhos->EditAttributes() ?>>
</span>
<?php echo $funcionarios->Numero_Filhos->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($funcionarios->Escolaridade->Visible) { // Escolaridade ?>
	<div id="r_Escolaridade" class="form-group">
		<label id="elh_funcionarios_Escolaridade" for="x_Escolaridade" class="col-sm-2 control-label ewLabel"><?php echo $funcionarios->Escolaridade->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $funcionarios->Escolaridade->CellAttributes() ?>>
<span id="el_funcionarios_Escolaridade">
<select data-field="x_Escolaridade" id="x_Escolaridade" name="x_Escolaridade"<?php echo $funcionarios->Escolaridade->EditAttributes() ?>>
<?php
if (is_array($funcionarios->Escolaridade->EditValue)) {
	$arwrk = $funcionarios->Escolaridade->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($funcionarios->Escolaridade->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php echo $funcionarios->Escolaridade->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($funcionarios->Situacao->Visible) { // Situacao ?>
	<div id="r_Situacao" class="form-group">
		<label id="elh_funcionarios_Situacao" for="x_Situacao" class="col-sm-2 control-label ewLabel"><?php echo $funcionarios->Situacao->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $funcionarios->Situacao->CellAttributes() ?>>
<span id="el_funcionarios_Situacao">
<select data-field="x_Situacao" id="x_Situacao" name="x_Situacao"<?php echo $funcionarios->Situacao->EditAttributes() ?>>
<?php
if (is_array($funcionarios->Situacao->EditValue)) {
	$arwrk = $funcionarios->Situacao->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($funcionarios->Situacao->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php echo $funcionarios->Situacao->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($funcionarios->Qual_ano->Visible) { // Qual_ano ?>
	<div id="r_Qual_ano" class="form-group">
		<label id="elh_funcionarios_Qual_ano" for="x_Qual_ano" class="col-sm-2 control-label ewLabel"><?php echo $funcionarios->Qual_ano->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $funcionarios->Qual_ano->CellAttributes() ?>>
<span id="el_funcionarios_Qual_ano">
<input type="text" data-field="x_Qual_ano" name="x_Qual_ano" id="x_Qual_ano" size="5" maxlength="5" value="<?php echo $funcionarios->Qual_ano->EditValue ?>"<?php echo $funcionarios->Qual_ano->EditAttributes() ?>>
</span>
<?php echo $funcionarios->Qual_ano->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
		</div>
		<div class="tab-pane" id="tab_funcionarios3">
<div>
<?php if ($funcionarios->CPF->Visible) { // CPF ?>
	<div id="r_CPF" class="form-group">
		<label id="elh_funcionarios_CPF" for="x_CPF" class="col-sm-2 control-label ewLabel"><?php echo $funcionarios->CPF->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $funcionarios->CPF->CellAttributes() ?>>
<span id="el_funcionarios_CPF">
<input type="text" data-field="x_CPF" name="x_CPF" id="x_CPF" size="30" maxlength="11" value="<?php echo $funcionarios->CPF->EditValue ?>"<?php echo $funcionarios->CPF->EditAttributes() ?>>
</span>
<?php echo $funcionarios->CPF->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($funcionarios->RG->Visible) { // RG ?>
	<div id="r_RG" class="form-group">
		<label id="elh_funcionarios_RG" for="x_RG" class="col-sm-2 control-label ewLabel"><?php echo $funcionarios->RG->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $funcionarios->RG->CellAttributes() ?>>
<span id="el_funcionarios_RG">
<input type="text" data-field="x_RG" name="x_RG" id="x_RG" size="30" maxlength="25" value="<?php echo $funcionarios->RG->EditValue ?>"<?php echo $funcionarios->RG->EditAttributes() ?>>
</span>
<?php echo $funcionarios->RG->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($funcionarios->Org_Exp->Visible) { // Org_Exp ?>
	<div id="r_Org_Exp" class="form-group">
		<label id="elh_funcionarios_Org_Exp" for="x_Org_Exp" class="col-sm-2 control-label ewLabel"><?php echo $funcionarios->Org_Exp->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $funcionarios->Org_Exp->CellAttributes() ?>>
<span id="el_funcionarios_Org_Exp">
<input type="text" data-field="x_Org_Exp" name="x_Org_Exp" id="x_Org_Exp" size="30" maxlength="20" value="<?php echo $funcionarios->Org_Exp->EditValue ?>"<?php echo $funcionarios->Org_Exp->EditAttributes() ?>>
</span>
<?php echo $funcionarios->Org_Exp->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($funcionarios->Data_Expedicao->Visible) { // Data_Expedicao ?>
	<div id="r_Data_Expedicao" class="form-group">
		<label id="elh_funcionarios_Data_Expedicao" for="x_Data_Expedicao" class="col-sm-2 control-label ewLabel"><?php echo $funcionarios->Data_Expedicao->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $funcionarios->Data_Expedicao->CellAttributes() ?>>
<span id="el_funcionarios_Data_Expedicao">
<input type="text" data-field="x_Data_Expedicao" name="x_Data_Expedicao" id="x_Data_Expedicao" value="<?php echo $funcionarios->Data_Expedicao->EditValue ?>"<?php echo $funcionarios->Data_Expedicao->EditAttributes() ?>>
</span>
<?php echo $funcionarios->Data_Expedicao->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($funcionarios->CTPS_N->Visible) { // CTPS_N ?>
	<div id="r_CTPS_N" class="form-group">
		<label id="elh_funcionarios_CTPS_N" for="x_CTPS_N" class="col-sm-2 control-label ewLabel"><?php echo $funcionarios->CTPS_N->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $funcionarios->CTPS_N->CellAttributes() ?>>
<span id="el_funcionarios_CTPS_N">
<input type="text" data-field="x_CTPS_N" name="x_CTPS_N" id="x_CTPS_N" size="30" maxlength="30" value="<?php echo $funcionarios->CTPS_N->EditValue ?>"<?php echo $funcionarios->CTPS_N->EditAttributes() ?>>
</span>
<?php echo $funcionarios->CTPS_N->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($funcionarios->CTPS_Serie->Visible) { // CTPS_Serie ?>
	<div id="r_CTPS_Serie" class="form-group">
		<label id="elh_funcionarios_CTPS_Serie" for="x_CTPS_Serie" class="col-sm-2 control-label ewLabel"><?php echo $funcionarios->CTPS_Serie->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $funcionarios->CTPS_Serie->CellAttributes() ?>>
<span id="el_funcionarios_CTPS_Serie">
<input type="text" data-field="x_CTPS_Serie" name="x_CTPS_Serie" id="x_CTPS_Serie" size="10" maxlength="10" value="<?php echo $funcionarios->CTPS_Serie->EditValue ?>"<?php echo $funcionarios->CTPS_Serie->EditAttributes() ?>>
</span>
<?php echo $funcionarios->CTPS_Serie->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($funcionarios->Titulo_Eleitor->Visible) { // Titulo_Eleitor ?>
	<div id="r_Titulo_Eleitor" class="form-group">
		<label id="elh_funcionarios_Titulo_Eleitor" for="x_Titulo_Eleitor" class="col-sm-2 control-label ewLabel"><?php echo $funcionarios->Titulo_Eleitor->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $funcionarios->Titulo_Eleitor->CellAttributes() ?>>
<span id="el_funcionarios_Titulo_Eleitor">
<input type="text" data-field="x_Titulo_Eleitor" name="x_Titulo_Eleitor" id="x_Titulo_Eleitor" size="30" maxlength="20" value="<?php echo $funcionarios->Titulo_Eleitor->EditValue ?>"<?php echo $funcionarios->Titulo_Eleitor->EditAttributes() ?>>
</span>
<?php echo $funcionarios->Titulo_Eleitor->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($funcionarios->Observacoes->Visible) { // Observacoes ?>
	<div id="r_Observacoes" class="form-group">
		<label id="elh_funcionarios_Observacoes" for="x_Observacoes" class="col-sm-2 control-label ewLabel"><?php echo $funcionarios->Observacoes->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $funcionarios->Observacoes->CellAttributes() ?>>
<span id="el_funcionarios_Observacoes">
<textarea data-field="x_Observacoes" name="x_Observacoes" id="x_Observacoes" cols="70" rows="2"<?php echo $funcionarios->Observacoes->EditAttributes() ?>><?php echo $funcionarios->Observacoes->EditValue ?></textarea>
</span>
<?php echo $funcionarios->Observacoes->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($funcionarios->Inativo->Visible) { // Inativo ?>
	<div id="r_Inativo" class="form-group">
		<label id="elh_funcionarios_Inativo" class="col-sm-2 control-label ewLabel"><?php echo $funcionarios->Inativo->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $funcionarios->Inativo->CellAttributes() ?>>
<span id="el_funcionarios_Inativo">
<div id="tp_x_Inativo" class="<?php echo EW_ITEM_TEMPLATE_CLASSNAME; ?>"><input type="checkbox" name="x_Inativo[]" id="x_Inativo[]" value="{value}"<?php echo $funcionarios->Inativo->EditAttributes() ?>></div>
<div id="dsl_x_Inativo" data-repeatcolumn="5" class="ewItemList">
<?php
$arwrk = $funcionarios->Inativo->EditValue;
if (is_array($arwrk)) {
	$armultiwrk= explode(",", strval($funcionarios->Inativo->CurrentValue));
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = "";
		$cnt = count($armultiwrk);
		for ($ari = 0; $ari < $cnt; $ari++) {
			if (strval($arwrk[$rowcntwrk][0]) == trim(strval($armultiwrk[$ari]))) {
				$selwrk = " checked=\"checked\"";
				if ($selwrk <> "") $emptywrk = FALSE;
				break;
			}
		}

		// Note: No spacing within the LABEL tag
?>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 1) ?>
<label class="checkbox-inline"><input type="checkbox" data-field="x_Inativo" name="x_Inativo[]" id="x_Inativo_<?php echo $rowcntwrk ?>[]" value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?><?php echo $funcionarios->Inativo->EditAttributes() ?>><?php echo $arwrk[$rowcntwrk][1] ?></label>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 2) ?>
<?php
	}
}
?>
</div>
</span>
<?php echo $funcionarios->Inativo->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
		</div>
	</div>
</div>
</div>
<span id="el_funcionarios_Id">
<input type="hidden" data-field="x_Id" name="x_Id" id="x_Id" value="<?php echo ew_HtmlEncode($funcionarios->Id->CurrentValue) ?>">
</span>
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><i class="fa fa-floppy-o"></i>&nbsp;<?php echo $Language->Phrase("SaveBtn") ?></button>
	</div>
</div>
</form>
<script type="text/javascript">
ffuncionariosedit.Init();
</script>
<?php
$funcionarios_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$funcionarios_edit->Page_Terminate();
?>
