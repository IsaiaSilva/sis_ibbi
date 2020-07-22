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

$funcionarios_view = NULL; // Initialize page object first

class cfuncionarios_view extends cfuncionarios {

	// Page ID
	var $PageID = 'view';

	// Project ID
	var $ProjectID = "{2B7992FC-5911-46A7-9310-01F4D4225C49}";

	// Table name
	var $TableName = 'funcionarios';

	// Page object name
	var $PageObjName = 'funcionarios_view';

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
		$KeyUrl = "";
		if (@$_GET["Id"] <> "") {
			$this->RecKey["Id"] = $_GET["Id"];
			$KeyUrl .= "&amp;Id=" . urlencode($this->RecKey["Id"]);
		}
		$this->ExportPrintUrl = $this->PageUrl() . "export=print" . $KeyUrl;
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html" . $KeyUrl;
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel" . $KeyUrl;
		$this->ExportWordUrl = $this->PageUrl() . "export=word" . $KeyUrl;
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml" . $KeyUrl;
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv" . $KeyUrl;
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf" . $KeyUrl;

		// Table object (usuarios)
		if (!isset($GLOBALS['usuarios'])) $GLOBALS['usuarios'] = new cusuarios();

		// User table object (usuarios)
		if (!isset($GLOBALS["UserTable"])) $GLOBALS["UserTable"] = new cusuarios();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'view', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'funcionarios', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();

		// Export options
		$this->ExportOptions = new cListOptions();
		$this->ExportOptions->Tag = "div";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Other options
		$this->OtherOptions['action'] = new cListOptions();
		$this->OtherOptions['action']->Tag = "div";
		$this->OtherOptions['action']->TagClassName = "ewActionOption";
		$this->OtherOptions['detail'] = new cListOptions();
		$this->OtherOptions['detail']->Tag = "div";
		$this->OtherOptions['detail']->TagClassName = "ewDetailOption";
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
		if (!$Security->CanView()) {
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
	var $ExportOptions; // Export options
	var $OtherOptions = array(); // Other options
	var $DisplayRecs = 1;
	var $DbMasterFilter;
	var $DbDetailFilter;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $RecCnt;
	var $RecKey = array();
	var $Recordset;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;

		// Load current record
		$bLoadCurrentRecord = FALSE;
		$sReturnUrl = "";
		$bMatchRecord = FALSE;

		// Set up Breadcrumb
		if ($this->Export == "")
			$this->SetupBreadcrumb();
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET["Id"] <> "") {
				$this->Id->setQueryStringValue($_GET["Id"]);
				$this->RecKey["Id"] = $this->Id->QueryStringValue;
			} else {
				$sReturnUrl = "funcionarioslist.php"; // Return to list
			}

			// Get action
			$this->CurrentAction = "I"; // Display form
			switch ($this->CurrentAction) {
				case "I": // Get a record to display
					if (!$this->LoadRow()) { // Load record based on key
						if ($this->getSuccessMessage() == "" && $this->getFailureMessage() == "")
							$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
						$sReturnUrl = "funcionarioslist.php"; // No matching record, return to list
					}
			}
		} else {
			$sReturnUrl = "funcionarioslist.php"; // Not page request, return to list
		}
		if ($sReturnUrl <> "")
			$this->Page_Terminate($sReturnUrl);

		// Render row
		$this->RowType = EW_ROWTYPE_VIEW;
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Set up other options
	function SetupOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
		$option = &$options["action"];

		// Add
		$item = &$option->Add("add");
		$item->Body = "<a class=\"ewAction ewAdd\" title=\"" . ew_HtmlTitle($Language->Phrase("ViewPageAddLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("ViewPageAddLink")) . "\" href=\"" . ew_HtmlEncode($this->AddUrl) . "\">" . $Language->Phrase("ViewPageAddLink") . "</a>";
		$item->Visible = ($this->AddUrl <> "" && $Security->CanAdd());

		// Edit
		$item = &$option->Add("edit");
		$item->Body = "<a class=\"ewAction ewEdit\" title=\"" . ew_HtmlTitle($Language->Phrase("ViewPageEditLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("ViewPageEditLink")) . "\" href=\"" . ew_HtmlEncode($this->EditUrl) . "\">" . $Language->Phrase("ViewPageEditLink") . "</a>";
		$item->Visible = ($this->EditUrl <> "" && $Security->CanEdit());

		// Delete
		$item = &$option->Add("delete");
		$item->Body = "<a class=\"ewAction ewDelete\" title=\"" . ew_HtmlTitle($Language->Phrase("ViewPageDeleteLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("ViewPageDeleteLink")) . "\" href=\"" . ew_HtmlEncode($this->DeleteUrl) . "\">" . $Language->Phrase("ViewPageDeleteLink") . "</a>";
		$item->Visible = ($this->DeleteUrl <> "" && $Security->CanDelete());

		// Set up action default
		$option = &$options["action"];
		$option->DropDownButtonPhrase = $Language->Phrase("ButtonActions");
		$option->UseImageAndText = TRUE;
		$option->UseDropDownButton = FALSE;
		$option->UseButtonGroup = TRUE;
		$item = &$option->Add($option->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;
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
		$this->AddUrl = $this->GetAddUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();
		$this->ListUrl = $this->GetListUrl();
		$this->SetupOtherOptions();

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
		$Breadcrumb->Add("list", $this->TableVar, "funcionarioslist.php", "", $this->TableVar, TRUE);
		$PageId = "view";
		$Breadcrumb->Add("view", $PageId, $url);
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
if (!isset($funcionarios_view)) $funcionarios_view = new cfuncionarios_view();

// Page init
$funcionarios_view->Page_Init();

// Page main
$funcionarios_view->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$funcionarios_view->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var funcionarios_view = new ew_Page("funcionarios_view");
funcionarios_view.PageID = "view"; // Page ID
var EW_PAGE_ID = funcionarios_view.PageID; // For backward compatibility

// Form object
var ffuncionariosview = new ew_Form("ffuncionariosview");

// Form_CustomValidate event
ffuncionariosview.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ffuncionariosview.ValidateRequired = true;
<?php } else { ?>
ffuncionariosview.ValidateRequired = false; 
<?php } ?>

// Multi-Page properties
ffuncionariosview.MultiPage = new ew_MultiPage("ffuncionariosview",
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
<?php $funcionarios_view->ExportOptions->Render("body") ?>
<?php
	foreach ($funcionarios_view->OtherOptions as &$option)
		$option->Render("body");
?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php $funcionarios_view->ShowPageHeader(); ?>
<?php
$funcionarios_view->ShowMessage();
?>
<form name="ffuncionariosview" id="ffuncionariosview" class="form-inline ewForm ewViewForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($funcionarios_view->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $funcionarios_view->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="funcionarios">
<?php if ($funcionarios->Export == "") { ?>
<div>
<div class="tabbable" id="funcionarios_view">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab_funcionarios1" data-toggle="tab"><?php echo $funcionarios->PageCaption(1) ?></a></li>
		<li><a href="#tab_funcionarios2" data-toggle="tab"><?php echo $funcionarios->PageCaption(2) ?></a></li>
		<li><a href="#tab_funcionarios3" data-toggle="tab"><?php echo $funcionarios->PageCaption(3) ?></a></li>
	</ul>
	<div class="tab-content">
<?php } ?>
<?php if ($funcionarios->Export == "") { ?>
		<div class="tab-pane active" id="tab_funcionarios1">
<?php } ?>
<table class="table table-bordered table-striped ewViewTable">
<?php if ($funcionarios->EhMembro->Visible) { // EhMembro ?>
	<tr id="r_EhMembro">
		<td><span id="elh_funcionarios_EhMembro"><?php echo $funcionarios->EhMembro->FldCaption() ?></span></td>
		<td<?php echo $funcionarios->EhMembro->CellAttributes() ?>>
<span id="el_funcionarios_EhMembro" class="form-group">
<span<?php echo $funcionarios->EhMembro->ViewAttributes() ?>>
<?php echo $funcionarios->EhMembro->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($funcionarios->Data_Admissao->Visible) { // Data_Admissao ?>
	<tr id="r_Data_Admissao">
		<td><span id="elh_funcionarios_Data_Admissao"><?php echo $funcionarios->Data_Admissao->FldCaption() ?></span></td>
		<td<?php echo $funcionarios->Data_Admissao->CellAttributes() ?>>
<span id="el_funcionarios_Data_Admissao" class="form-group">
<span<?php echo $funcionarios->Data_Admissao->ViewAttributes() ?>>
<?php echo $funcionarios->Data_Admissao->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($funcionarios->Nome->Visible) { // Nome ?>
	<tr id="r_Nome">
		<td><span id="elh_funcionarios_Nome"><?php echo $funcionarios->Nome->FldCaption() ?></span></td>
		<td<?php echo $funcionarios->Nome->CellAttributes() ?>>
<span id="el_funcionarios_Nome" class="form-group">
<span<?php echo $funcionarios->Nome->ViewAttributes() ?>>
<?php echo $funcionarios->Nome->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($funcionarios->Data_Nasc->Visible) { // Data_Nasc ?>
	<tr id="r_Data_Nasc">
		<td><span id="elh_funcionarios_Data_Nasc"><?php echo $funcionarios->Data_Nasc->FldCaption() ?></span></td>
		<td<?php echo $funcionarios->Data_Nasc->CellAttributes() ?>>
<span id="el_funcionarios_Data_Nasc" class="form-group">
<span<?php echo $funcionarios->Data_Nasc->ViewAttributes() ?>>
<?php echo $funcionarios->Data_Nasc->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($funcionarios->Estado_Civil->Visible) { // Estado_Civil ?>
	<tr id="r_Estado_Civil">
		<td><span id="elh_funcionarios_Estado_Civil"><?php echo $funcionarios->Estado_Civil->FldCaption() ?></span></td>
		<td<?php echo $funcionarios->Estado_Civil->CellAttributes() ?>>
<span id="el_funcionarios_Estado_Civil" class="form-group">
<span<?php echo $funcionarios->Estado_Civil->ViewAttributes() ?>>
<?php echo $funcionarios->Estado_Civil->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($funcionarios->Celular->Visible) { // Celular ?>
	<tr id="r_Celular">
		<td><span id="elh_funcionarios_Celular"><?php echo $funcionarios->Celular->FldCaption() ?></span></td>
		<td<?php echo $funcionarios->Celular->CellAttributes() ?>>
<span id="el_funcionarios_Celular" class="form-group">
<span<?php echo $funcionarios->Celular->ViewAttributes() ?>>
<?php echo $funcionarios->Celular->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($funcionarios->Telefone_Fixo->Visible) { // Telefone Fixo ?>
	<tr id="r_Telefone_Fixo">
		<td><span id="elh_funcionarios_Telefone_Fixo"><?php echo $funcionarios->Telefone_Fixo->FldCaption() ?></span></td>
		<td<?php echo $funcionarios->Telefone_Fixo->CellAttributes() ?>>
<span id="el_funcionarios_Telefone_Fixo" class="form-group">
<span<?php echo $funcionarios->Telefone_Fixo->ViewAttributes() ?>>
<?php echo $funcionarios->Telefone_Fixo->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($funcionarios->_Email->Visible) { // Email ?>
	<tr id="r__Email">
		<td><span id="elh_funcionarios__Email"><?php echo $funcionarios->_Email->FldCaption() ?></span></td>
		<td<?php echo $funcionarios->_Email->CellAttributes() ?>>
<span id="el_funcionarios__Email" class="form-group">
<span<?php echo $funcionarios->_Email->ViewAttributes() ?>>
<?php echo $funcionarios->_Email->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($funcionarios->Cargo->Visible) { // Cargo ?>
	<tr id="r_Cargo">
		<td><span id="elh_funcionarios_Cargo"><?php echo $funcionarios->Cargo->FldCaption() ?></span></td>
		<td<?php echo $funcionarios->Cargo->CellAttributes() ?>>
<span id="el_funcionarios_Cargo" class="form-group">
<span<?php echo $funcionarios->Cargo->ViewAttributes() ?>>
<?php echo $funcionarios->Cargo->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($funcionarios->Setor->Visible) { // Setor ?>
	<tr id="r_Setor">
		<td><span id="elh_funcionarios_Setor"><?php echo $funcionarios->Setor->FldCaption() ?></span></td>
		<td<?php echo $funcionarios->Setor->CellAttributes() ?>>
<span id="el_funcionarios_Setor" class="form-group">
<span<?php echo $funcionarios->Setor->ViewAttributes() ?>>
<?php echo $funcionarios->Setor->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
</table>
<?php if ($funcionarios->Export == "") { ?>
		</div>
<?php } ?>
<?php if ($funcionarios->Export == "") { ?>
		<div class="tab-pane" id="tab_funcionarios2">
<?php } ?>
<table class="table table-bordered table-striped ewViewTable">
<?php if ($funcionarios->Endereco->Visible) { // Endereco ?>
	<tr id="r_Endereco">
		<td><span id="elh_funcionarios_Endereco"><?php echo $funcionarios->Endereco->FldCaption() ?></span></td>
		<td<?php echo $funcionarios->Endereco->CellAttributes() ?>>
<span id="el_funcionarios_Endereco" class="form-group">
<span<?php echo $funcionarios->Endereco->ViewAttributes() ?>>
<?php echo $funcionarios->Endereco->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($funcionarios->Bairro->Visible) { // Bairro ?>
	<tr id="r_Bairro">
		<td><span id="elh_funcionarios_Bairro"><?php echo $funcionarios->Bairro->FldCaption() ?></span></td>
		<td<?php echo $funcionarios->Bairro->CellAttributes() ?>>
<span id="el_funcionarios_Bairro" class="form-group">
<span<?php echo $funcionarios->Bairro->ViewAttributes() ?>>
<?php echo $funcionarios->Bairro->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($funcionarios->Cidade->Visible) { // Cidade ?>
	<tr id="r_Cidade">
		<td><span id="elh_funcionarios_Cidade"><?php echo $funcionarios->Cidade->FldCaption() ?></span></td>
		<td<?php echo $funcionarios->Cidade->CellAttributes() ?>>
<span id="el_funcionarios_Cidade" class="form-group">
<span<?php echo $funcionarios->Cidade->ViewAttributes() ?>>
<?php echo $funcionarios->Cidade->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($funcionarios->UF->Visible) { // UF ?>
	<tr id="r_UF">
		<td><span id="elh_funcionarios_UF"><?php echo $funcionarios->UF->FldCaption() ?></span></td>
		<td<?php echo $funcionarios->UF->CellAttributes() ?>>
<span id="el_funcionarios_UF" class="form-group">
<span<?php echo $funcionarios->UF->ViewAttributes() ?>>
<?php echo $funcionarios->UF->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($funcionarios->CEP->Visible) { // CEP ?>
	<tr id="r_CEP">
		<td><span id="elh_funcionarios_CEP"><?php echo $funcionarios->CEP->FldCaption() ?></span></td>
		<td<?php echo $funcionarios->CEP->CellAttributes() ?>>
<span id="el_funcionarios_CEP" class="form-group">
<span<?php echo $funcionarios->CEP->ViewAttributes() ?>>
<?php echo $funcionarios->CEP->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($funcionarios->Numero_Filhos->Visible) { // Numero_Filhos ?>
	<tr id="r_Numero_Filhos">
		<td><span id="elh_funcionarios_Numero_Filhos"><?php echo $funcionarios->Numero_Filhos->FldCaption() ?></span></td>
		<td<?php echo $funcionarios->Numero_Filhos->CellAttributes() ?>>
<span id="el_funcionarios_Numero_Filhos" class="form-group">
<span<?php echo $funcionarios->Numero_Filhos->ViewAttributes() ?>>
<?php echo $funcionarios->Numero_Filhos->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($funcionarios->Escolaridade->Visible) { // Escolaridade ?>
	<tr id="r_Escolaridade">
		<td><span id="elh_funcionarios_Escolaridade"><?php echo $funcionarios->Escolaridade->FldCaption() ?></span></td>
		<td<?php echo $funcionarios->Escolaridade->CellAttributes() ?>>
<span id="el_funcionarios_Escolaridade" class="form-group">
<span<?php echo $funcionarios->Escolaridade->ViewAttributes() ?>>
<?php echo $funcionarios->Escolaridade->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($funcionarios->Situacao->Visible) { // Situacao ?>
	<tr id="r_Situacao">
		<td><span id="elh_funcionarios_Situacao"><?php echo $funcionarios->Situacao->FldCaption() ?></span></td>
		<td<?php echo $funcionarios->Situacao->CellAttributes() ?>>
<span id="el_funcionarios_Situacao" class="form-group">
<span<?php echo $funcionarios->Situacao->ViewAttributes() ?>>
<?php echo $funcionarios->Situacao->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($funcionarios->Qual_ano->Visible) { // Qual_ano ?>
	<tr id="r_Qual_ano">
		<td><span id="elh_funcionarios_Qual_ano"><?php echo $funcionarios->Qual_ano->FldCaption() ?></span></td>
		<td<?php echo $funcionarios->Qual_ano->CellAttributes() ?>>
<span id="el_funcionarios_Qual_ano" class="form-group">
<span<?php echo $funcionarios->Qual_ano->ViewAttributes() ?>>
<?php echo $funcionarios->Qual_ano->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
</table>
<?php if ($funcionarios->Export == "") { ?>
		</div>
<?php } ?>
<?php if ($funcionarios->Export == "") { ?>
		<div class="tab-pane" id="tab_funcionarios3">
<?php } ?>
<table class="table table-bordered table-striped ewViewTable">
<?php if ($funcionarios->CPF->Visible) { // CPF ?>
	<tr id="r_CPF">
		<td><span id="elh_funcionarios_CPF"><?php echo $funcionarios->CPF->FldCaption() ?></span></td>
		<td<?php echo $funcionarios->CPF->CellAttributes() ?>>
<span id="el_funcionarios_CPF" class="form-group">
<span<?php echo $funcionarios->CPF->ViewAttributes() ?>>
<?php echo $funcionarios->CPF->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($funcionarios->RG->Visible) { // RG ?>
	<tr id="r_RG">
		<td><span id="elh_funcionarios_RG"><?php echo $funcionarios->RG->FldCaption() ?></span></td>
		<td<?php echo $funcionarios->RG->CellAttributes() ?>>
<span id="el_funcionarios_RG" class="form-group">
<span<?php echo $funcionarios->RG->ViewAttributes() ?>>
<?php echo $funcionarios->RG->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($funcionarios->Org_Exp->Visible) { // Org_Exp ?>
	<tr id="r_Org_Exp">
		<td><span id="elh_funcionarios_Org_Exp"><?php echo $funcionarios->Org_Exp->FldCaption() ?></span></td>
		<td<?php echo $funcionarios->Org_Exp->CellAttributes() ?>>
<span id="el_funcionarios_Org_Exp" class="form-group">
<span<?php echo $funcionarios->Org_Exp->ViewAttributes() ?>>
<?php echo $funcionarios->Org_Exp->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($funcionarios->Data_Expedicao->Visible) { // Data_Expedicao ?>
	<tr id="r_Data_Expedicao">
		<td><span id="elh_funcionarios_Data_Expedicao"><?php echo $funcionarios->Data_Expedicao->FldCaption() ?></span></td>
		<td<?php echo $funcionarios->Data_Expedicao->CellAttributes() ?>>
<span id="el_funcionarios_Data_Expedicao" class="form-group">
<span<?php echo $funcionarios->Data_Expedicao->ViewAttributes() ?>>
<?php echo $funcionarios->Data_Expedicao->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($funcionarios->CTPS_N->Visible) { // CTPS_N ?>
	<tr id="r_CTPS_N">
		<td><span id="elh_funcionarios_CTPS_N"><?php echo $funcionarios->CTPS_N->FldCaption() ?></span></td>
		<td<?php echo $funcionarios->CTPS_N->CellAttributes() ?>>
<span id="el_funcionarios_CTPS_N" class="form-group">
<span<?php echo $funcionarios->CTPS_N->ViewAttributes() ?>>
<?php echo $funcionarios->CTPS_N->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($funcionarios->CTPS_Serie->Visible) { // CTPS_Serie ?>
	<tr id="r_CTPS_Serie">
		<td><span id="elh_funcionarios_CTPS_Serie"><?php echo $funcionarios->CTPS_Serie->FldCaption() ?></span></td>
		<td<?php echo $funcionarios->CTPS_Serie->CellAttributes() ?>>
<span id="el_funcionarios_CTPS_Serie" class="form-group">
<span<?php echo $funcionarios->CTPS_Serie->ViewAttributes() ?>>
<?php echo $funcionarios->CTPS_Serie->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($funcionarios->Titulo_Eleitor->Visible) { // Titulo_Eleitor ?>
	<tr id="r_Titulo_Eleitor">
		<td><span id="elh_funcionarios_Titulo_Eleitor"><?php echo $funcionarios->Titulo_Eleitor->FldCaption() ?></span></td>
		<td<?php echo $funcionarios->Titulo_Eleitor->CellAttributes() ?>>
<span id="el_funcionarios_Titulo_Eleitor" class="form-group">
<span<?php echo $funcionarios->Titulo_Eleitor->ViewAttributes() ?>>
<?php echo $funcionarios->Titulo_Eleitor->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($funcionarios->Observacoes->Visible) { // Observacoes ?>
	<tr id="r_Observacoes">
		<td><span id="elh_funcionarios_Observacoes"><?php echo $funcionarios->Observacoes->FldCaption() ?></span></td>
		<td<?php echo $funcionarios->Observacoes->CellAttributes() ?>>
<span id="el_funcionarios_Observacoes" class="form-group">
<span<?php echo $funcionarios->Observacoes->ViewAttributes() ?>>
<?php echo $funcionarios->Observacoes->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($funcionarios->Inativo->Visible) { // Inativo ?>
	<tr id="r_Inativo">
		<td><span id="elh_funcionarios_Inativo"><?php echo $funcionarios->Inativo->FldCaption() ?></span></td>
		<td<?php echo $funcionarios->Inativo->CellAttributes() ?>>
<span id="el_funcionarios_Inativo" class="form-group">
<span<?php echo $funcionarios->Inativo->ViewAttributes() ?>>
<?php echo $funcionarios->Inativo->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
</table>
<?php if ($funcionarios->Export == "") { ?>
		</div>
<?php } ?>
<?php if ($funcionarios->Export == "") { ?>
	</div>
</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
ffuncionariosview.Init();
</script>
<?php
$funcionarios_view->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$funcionarios_view->Page_Terminate();
?>
