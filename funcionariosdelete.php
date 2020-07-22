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

$funcionarios_delete = NULL; // Initialize page object first

class cfuncionarios_delete extends cfuncionarios {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{2B7992FC-5911-46A7-9310-01F4D4225C49}";

	// Table name
	var $TableName = 'funcionarios';

	// Page object name
	var $PageObjName = 'funcionarios_delete';

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
	var $AuditTrailOnDelete = TRUE;

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
			define("EW_PAGE_ID", 'delete', TRUE);

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
		if (!$Security->CanDelete()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate(ew_GetUrl("funcionarioslist.php"));
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();
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
	var $DbMasterFilter = "";
	var $DbDetailFilter = "";
	var $StartRec;
	var $TotalRecs = 0;
	var $RecCnt;
	var $RecKeys = array();
	var $Recordset;
	var $StartRowCnt = 1;
	var $RowCnt = 0;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Load key parameters
		$this->RecKeys = $this->GetRecordKeys(); // Load record keys
		$sFilter = $this->GetKeyFilter();
		if ($sFilter == "")
			$this->Page_Terminate("funcionarioslist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in funcionarios class, funcionariosinfo.php

		$this->CurrentFilter = $sFilter;

		// Get action
		if (@$_POST["a_delete"] <> "") {
			$this->CurrentAction = $_POST["a_delete"];
		} else {
			$this->CurrentAction = "I"; // Display record
		}
		switch ($this->CurrentAction) {
			case "D": // Delete
				$this->SendEmail = TRUE; // Send email on delete success
				if ($this->DeleteRows()) { // Delete rows
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("DeleteSuccess")); // Set up success message
					$this->Page_Terminate($this->getReturnUrl()); // Return to caller
				}
		}
	}

// No functions
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

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
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

	//
	// Delete records based on current filter
	//
	function DeleteRows() {
		global $conn, $Language, $Security;
		if (!$Security->CanDelete()) {
			$this->setFailureMessage($Language->Phrase("NoDeletePermission")); // No delete permission
			return FALSE;
		}
		$DeleteRows = TRUE;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE) {
			return FALSE;
		} elseif ($rs->EOF) {
			$this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
			$rs->Close();
			return FALSE;

		//} else {
		//	$this->LoadRowValues($rs); // Load row values

		}
		$rows = ($rs) ? $rs->GetRows() : array();
		$conn->BeginTrans();
		if ($this->AuditTrailOnDelete) $this->WriteAuditTrailDummy($Language->Phrase("BatchDeleteBegin")); // Batch delete begin

		// Clone old rows
		$rsold = $rows;
		if ($rs)
			$rs->Close();

		// Call row deleting event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$DeleteRows = $this->Row_Deleting($row);
				if (!$DeleteRows) break;
			}
		}
		if ($DeleteRows) {
			$sKey = "";
			foreach ($rsold as $row) {
				$sThisKey = "";
				if ($sThisKey <> "") $sThisKey .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
				$sThisKey .= $row['Id'];
				$this->LoadDbValues($row);
				$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
				$DeleteRows = $this->Delete($row); // Delete
				$conn->raiseErrorFn = '';
				if ($DeleteRows === FALSE)
					break;
				if ($sKey <> "") $sKey .= ", ";
				$sKey .= $sThisKey;
			}
		} else {

			// Set up error message
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("DeleteCancelled"));
			}
		}
		if ($DeleteRows) {
			$conn->CommitTrans(); // Commit the changes
			if ($DeleteRows) {
				foreach ($rsold as $row)
					$this->WriteAuditTrailOnDelete($row);
			}
			if ($this->AuditTrailOnDelete) $this->WriteAuditTrailDummy($Language->Phrase("BatchDeleteSuccess")); // Batch delete success
		} else {
			$conn->RollbackTrans(); // Rollback changes
			if ($this->AuditTrailOnDelete) $this->WriteAuditTrailDummy($Language->Phrase("BatchDeleteRollback")); // Batch delete rollback
		}

		// Call Row Deleted event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$this->Row_Deleted($row);
			}
		}
		return $DeleteRows;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, "funcionarioslist.php", "", $this->TableVar, TRUE);
		$PageId = "delete";
		$Breadcrumb->Add("delete", $PageId, $url);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'funcionarios';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (delete page)
	function WriteAuditTrailOnDelete(&$rs) {
		if (!$this->AuditTrailOnDelete) return;
		$table = 'funcionarios';

		// Get key value
		$key = "";
		if ($key <> "")
			$key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['Id'];

		// Write Audit Trail
		$dt = ew_StdCurrentDateTime();
		$id = ew_ScriptName();
	  $curUser = CurrentUserID();
		foreach (array_keys($rs) as $fldname) {
			if (array_key_exists($fldname, $this->fields) && $this->fields[$fldname]->FldDataType <> EW_DATATYPE_BLOB) { // Ignore BLOB fields
				if ($this->fields[$fldname]->FldDataType == EW_DATATYPE_MEMO) {
					if (EW_AUDIT_TRAIL_TO_DATABASE)
						$oldvalue = $rs[$fldname];
					else
						$oldvalue = "[MEMO]"; // Memo field
				} elseif ($this->fields[$fldname]->FldDataType == EW_DATATYPE_XML) {
					$oldvalue = "[XML]"; // XML field
				} else {
					$oldvalue = $rs[$fldname];
				}
				ew_WriteAuditTrail("log", $dt, $id, $curUser, "D", $table, $fldname, $key, $oldvalue, "");
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
}
?>
<?php ew_Header(TRUE) ?>
<?php

// Create page object
if (!isset($funcionarios_delete)) $funcionarios_delete = new cfuncionarios_delete();

// Page init
$funcionarios_delete->Page_Init();

// Page main
$funcionarios_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$funcionarios_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var funcionarios_delete = new ew_Page("funcionarios_delete");
funcionarios_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = funcionarios_delete.PageID; // For backward compatibility

// Form object
var ffuncionariosdelete = new ew_Form("ffuncionariosdelete");

// Form_CustomValidate event
ffuncionariosdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ffuncionariosdelete.ValidateRequired = true;
<?php } else { ?>
ffuncionariosdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($funcionarios_delete->Recordset = $funcionarios_delete->LoadRecordset())
	$funcionarios_deleteTotalRecs = $funcionarios_delete->Recordset->RecordCount(); // Get record count
if ($funcionarios_deleteTotalRecs <= 0) { // No record found, exit
	if ($funcionarios_delete->Recordset)
		$funcionarios_delete->Recordset->Close();
	$funcionarios_delete->Page_Terminate("funcionarioslist.php"); // Return to list
}
?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php $funcionarios_delete->ShowPageHeader(); ?>
<?php
$funcionarios_delete->ShowMessage();
?>
<form name="ffuncionariosdelete" id="ffuncionariosdelete" class="form-inline ewForm ewDeleteForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($funcionarios_delete->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $funcionarios_delete->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="funcionarios">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($funcionarios_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<div class="ewGrid">
<div class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<table class="table ewTable">
<?php echo $funcionarios->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($funcionarios->EhMembro->Visible) { // EhMembro ?>
		<th><span id="elh_funcionarios_EhMembro" class="funcionarios_EhMembro"><?php echo $funcionarios->EhMembro->FldCaption() ?></span></th>
<?php } ?>
<?php if ($funcionarios->Nome->Visible) { // Nome ?>
		<th><span id="elh_funcionarios_Nome" class="funcionarios_Nome"><?php echo $funcionarios->Nome->FldCaption() ?></span></th>
<?php } ?>
<?php if ($funcionarios->Data_Nasc->Visible) { // Data_Nasc ?>
		<th><span id="elh_funcionarios_Data_Nasc" class="funcionarios_Data_Nasc"><?php echo $funcionarios->Data_Nasc->FldCaption() ?></span></th>
<?php } ?>
<?php if ($funcionarios->Estado_Civil->Visible) { // Estado_Civil ?>
		<th><span id="elh_funcionarios_Estado_Civil" class="funcionarios_Estado_Civil"><?php echo $funcionarios->Estado_Civil->FldCaption() ?></span></th>
<?php } ?>
<?php if ($funcionarios->Telefone_Fixo->Visible) { // Telefone Fixo ?>
		<th><span id="elh_funcionarios_Telefone_Fixo" class="funcionarios_Telefone_Fixo"><?php echo $funcionarios->Telefone_Fixo->FldCaption() ?></span></th>
<?php } ?>
<?php if ($funcionarios->_Email->Visible) { // Email ?>
		<th><span id="elh_funcionarios__Email" class="funcionarios__Email"><?php echo $funcionarios->_Email->FldCaption() ?></span></th>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$funcionarios_delete->RecCnt = 0;
$i = 0;
while (!$funcionarios_delete->Recordset->EOF) {
	$funcionarios_delete->RecCnt++;
	$funcionarios_delete->RowCnt++;

	// Set row properties
	$funcionarios->ResetAttrs();
	$funcionarios->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$funcionarios_delete->LoadRowValues($funcionarios_delete->Recordset);

	// Render row
	$funcionarios_delete->RenderRow();
?>
	<tr<?php echo $funcionarios->RowAttributes() ?>>
<?php if ($funcionarios->EhMembro->Visible) { // EhMembro ?>
		<td<?php echo $funcionarios->EhMembro->CellAttributes() ?>>
<span id="el<?php echo $funcionarios_delete->RowCnt ?>_funcionarios_EhMembro" class="form-group funcionarios_EhMembro">
<span<?php echo $funcionarios->EhMembro->ViewAttributes() ?>>
<?php echo $funcionarios->EhMembro->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($funcionarios->Nome->Visible) { // Nome ?>
		<td<?php echo $funcionarios->Nome->CellAttributes() ?>>
<span id="el<?php echo $funcionarios_delete->RowCnt ?>_funcionarios_Nome" class="form-group funcionarios_Nome">
<span<?php echo $funcionarios->Nome->ViewAttributes() ?>>
<?php echo $funcionarios->Nome->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($funcionarios->Data_Nasc->Visible) { // Data_Nasc ?>
		<td<?php echo $funcionarios->Data_Nasc->CellAttributes() ?>>
<span id="el<?php echo $funcionarios_delete->RowCnt ?>_funcionarios_Data_Nasc" class="form-group funcionarios_Data_Nasc">
<span<?php echo $funcionarios->Data_Nasc->ViewAttributes() ?>>
<?php echo $funcionarios->Data_Nasc->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($funcionarios->Estado_Civil->Visible) { // Estado_Civil ?>
		<td<?php echo $funcionarios->Estado_Civil->CellAttributes() ?>>
<span id="el<?php echo $funcionarios_delete->RowCnt ?>_funcionarios_Estado_Civil" class="form-group funcionarios_Estado_Civil">
<span<?php echo $funcionarios->Estado_Civil->ViewAttributes() ?>>
<?php echo $funcionarios->Estado_Civil->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($funcionarios->Telefone_Fixo->Visible) { // Telefone Fixo ?>
		<td<?php echo $funcionarios->Telefone_Fixo->CellAttributes() ?>>
<span id="el<?php echo $funcionarios_delete->RowCnt ?>_funcionarios_Telefone_Fixo" class="form-group funcionarios_Telefone_Fixo">
<span<?php echo $funcionarios->Telefone_Fixo->ViewAttributes() ?>>
<?php echo $funcionarios->Telefone_Fixo->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($funcionarios->_Email->Visible) { // Email ?>
		<td<?php echo $funcionarios->_Email->CellAttributes() ?>>
<span id="el<?php echo $funcionarios_delete->RowCnt ?>_funcionarios__Email" class="form-group funcionarios__Email">
<span<?php echo $funcionarios->_Email->ViewAttributes() ?>>
<?php echo $funcionarios->_Email->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$funcionarios_delete->Recordset->MoveNext();
}
$funcionarios_delete->Recordset->Close();
?>
</tbody>
</table>
</div>
</div>
<div class="btn-group ewButtonGroup">
<button class="btn btn-primary ewButton btn-danger" name="btnAction" id="btnAction" type="submit"><i class="glyphicon glyphicon-trash"></i>&nbsp;<?php echo $Language->Phrase("DeleteBtn") ?></button>
</div>
</form>
<script type="text/javascript">
ffuncionariosdelete.Init();
</script>
<?php
$funcionarios_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$funcionarios_delete->Page_Terminate();
?>
