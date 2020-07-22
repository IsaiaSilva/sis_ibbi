<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg11.php" ?>
<?php include_once "ewmysql11.php" ?>
<?php include_once "phpfn11.php" ?>
<?php include_once "igrejasinfo.php" ?>
<?php include_once "usuariosinfo.php" ?>
<?php include_once "membrogridcls.php" ?>
<?php include_once "userfn11.php" ?>
<?php

//
// Page class
//

$igrejas_edit = NULL; // Initialize page object first

class cigrejas_edit extends cigrejas {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{2B7992FC-5911-46A7-9310-01F4D4225C49}";

	// Table name
	var $TableName = 'igrejas';

	// Page object name
	var $PageObjName = 'igrejas_edit';

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

		// Table object (igrejas)
		if (!isset($GLOBALS["igrejas"]) || get_class($GLOBALS["igrejas"]) == "cigrejas") {
			$GLOBALS["igrejas"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["igrejas"];
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
			define("EW_TABLE_NAME", 'igrejas', TRUE);

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
			$this->Page_Terminate(ew_GetUrl("igrejaslist.php"));
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->Id_igreja->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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

			// Process auto fill for detail table 'membro'
			if (@$_POST["grid"] == "fmembrogrid") {
				if (!isset($GLOBALS["membro_grid"])) $GLOBALS["membro_grid"] = new cmembro_grid;
				$GLOBALS["membro_grid"]->Page_Init();
				$this->Page_Terminate();
				exit();
			}
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
		global $EW_EXPORT, $igrejas;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($igrejas);
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
		if (@$_GET["Id_igreja"] <> "") {
			$this->Id_igreja->setQueryStringValue($_GET["Id_igreja"]);
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Process form if post back
		if (@$_POST["a_edit"] <> "") {
			$this->CurrentAction = $_POST["a_edit"]; // Get action code
			$this->LoadFormValues(); // Get form values

			// Set up detail parameters
			$this->SetUpDetailParms();
		} else {
			$this->CurrentAction = "I"; // Default action is display
		}

		// Check if valid key
		if ($this->Id_igreja->CurrentValue == "")
			$this->Page_Terminate("igrejaslist.php"); // Invalid key, return to list

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
					$this->Page_Terminate("igrejaslist.php"); // No matching record, return to list
				}

				// Set up detail parameters
				$this->SetUpDetailParms();
				break;
			Case "U": // Update
				$this->SendEmail = TRUE; // Send email on update success
				if ($this->EditRow()) { // Update record based on key
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("UpdateSuccess")); // Update success
					if ($this->getCurrentDetailTable() <> "") // Master/detail edit
						$sReturnUrl = $this->GetViewUrl(EW_TABLE_SHOW_DETAIL . "=" . $this->getCurrentDetailTable()); // Master/Detail view page
					else
						$sReturnUrl = $this->getReturnUrl();
					$this->Page_Terminate($sReturnUrl); // Return to caller
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Restore form values if update failed

					// Set up detail parameters
					$this->SetUpDetailParms();
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
		if (!$this->Id_igreja->FldIsDetailKey)
			$this->Id_igreja->setFormValue($objForm->GetValue("x_Id_igreja"));
		if (!$this->Igreja->FldIsDetailKey) {
			$this->Igreja->setFormValue($objForm->GetValue("x_Igreja"));
		}
		if (!$this->CNPJ->FldIsDetailKey) {
			$this->CNPJ->setFormValue($objForm->GetValue("x_CNPJ"));
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
		if (!$this->Telefone1->FldIsDetailKey) {
			$this->Telefone1->setFormValue($objForm->GetValue("x_Telefone1"));
		}
		if (!$this->Telefone2->FldIsDetailKey) {
			$this->Telefone2->setFormValue($objForm->GetValue("x_Telefone2"));
		}
		if (!$this->Fax->FldIsDetailKey) {
			$this->Fax->setFormValue($objForm->GetValue("x_Fax"));
		}
		if (!$this->DirigenteResponsavel->FldIsDetailKey) {
			$this->DirigenteResponsavel->setFormValue($objForm->GetValue("x_DirigenteResponsavel"));
		}
		if (!$this->_Email->FldIsDetailKey) {
			$this->_Email->setFormValue($objForm->GetValue("x__Email"));
		}
		if (!$this->Site_Igreja->FldIsDetailKey) {
			$this->Site_Igreja->setFormValue($objForm->GetValue("x_Site_Igreja"));
		}
		if (!$this->Email_da_igreja->FldIsDetailKey) {
			$this->Email_da_igreja->setFormValue($objForm->GetValue("x_Email_da_igreja"));
		}
		if (!$this->Modelo->FldIsDetailKey) {
			$this->Modelo->setFormValue($objForm->GetValue("x_Modelo"));
		}
		if (!$this->Data_de_Fundacao->FldIsDetailKey) {
			$this->Data_de_Fundacao->setFormValue($objForm->GetValue("x_Data_de_Fundacao"));
			$this->Data_de_Fundacao->CurrentValue = ew_UnFormatDateTime($this->Data_de_Fundacao->CurrentValue, 7);
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->Id_igreja->CurrentValue = $this->Id_igreja->FormValue;
		$this->Igreja->CurrentValue = $this->Igreja->FormValue;
		$this->CNPJ->CurrentValue = $this->CNPJ->FormValue;
		$this->Endereco->CurrentValue = $this->Endereco->FormValue;
		$this->Bairro->CurrentValue = $this->Bairro->FormValue;
		$this->Cidade->CurrentValue = $this->Cidade->FormValue;
		$this->UF->CurrentValue = $this->UF->FormValue;
		$this->CEP->CurrentValue = $this->CEP->FormValue;
		$this->Telefone1->CurrentValue = $this->Telefone1->FormValue;
		$this->Telefone2->CurrentValue = $this->Telefone2->FormValue;
		$this->Fax->CurrentValue = $this->Fax->FormValue;
		$this->DirigenteResponsavel->CurrentValue = $this->DirigenteResponsavel->FormValue;
		$this->_Email->CurrentValue = $this->_Email->FormValue;
		$this->Site_Igreja->CurrentValue = $this->Site_Igreja->FormValue;
		$this->Email_da_igreja->CurrentValue = $this->Email_da_igreja->FormValue;
		$this->Modelo->CurrentValue = $this->Modelo->FormValue;
		$this->Data_de_Fundacao->CurrentValue = $this->Data_de_Fundacao->FormValue;
		$this->Data_de_Fundacao->CurrentValue = ew_UnFormatDateTime($this->Data_de_Fundacao->CurrentValue, 7);
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
		$this->Id_igreja->setDbValue($rs->fields('Id_igreja'));
		$this->Igreja->setDbValue($rs->fields('Igreja'));
		$this->CNPJ->setDbValue($rs->fields('CNPJ'));
		$this->Endereco->setDbValue($rs->fields('Endereco'));
		$this->Bairro->setDbValue($rs->fields('Bairro'));
		$this->Cidade->setDbValue($rs->fields('Cidade'));
		$this->UF->setDbValue($rs->fields('UF'));
		$this->CEP->setDbValue($rs->fields('CEP'));
		$this->Telefone1->setDbValue($rs->fields('Telefone1'));
		$this->Telefone2->setDbValue($rs->fields('Telefone2'));
		$this->Fax->setDbValue($rs->fields('Fax'));
		$this->DirigenteResponsavel->setDbValue($rs->fields('DirigenteResponsavel'));
		$this->_Email->setDbValue($rs->fields('Email'));
		$this->Site_Igreja->setDbValue($rs->fields('Site_Igreja'));
		$this->Email_da_igreja->setDbValue($rs->fields('Email_da_igreja'));
		$this->Modelo->setDbValue($rs->fields('Modelo'));
		$this->Data_de_Fundacao->setDbValue($rs->fields('Data_de_Fundacao'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->Id_igreja->DbValue = $row['Id_igreja'];
		$this->Igreja->DbValue = $row['Igreja'];
		$this->CNPJ->DbValue = $row['CNPJ'];
		$this->Endereco->DbValue = $row['Endereco'];
		$this->Bairro->DbValue = $row['Bairro'];
		$this->Cidade->DbValue = $row['Cidade'];
		$this->UF->DbValue = $row['UF'];
		$this->CEP->DbValue = $row['CEP'];
		$this->Telefone1->DbValue = $row['Telefone1'];
		$this->Telefone2->DbValue = $row['Telefone2'];
		$this->Fax->DbValue = $row['Fax'];
		$this->DirigenteResponsavel->DbValue = $row['DirigenteResponsavel'];
		$this->_Email->DbValue = $row['Email'];
		$this->Site_Igreja->DbValue = $row['Site_Igreja'];
		$this->Email_da_igreja->DbValue = $row['Email_da_igreja'];
		$this->Modelo->DbValue = $row['Modelo'];
		$this->Data_de_Fundacao->DbValue = $row['Data_de_Fundacao'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// Id_igreja
		// Igreja
		// CNPJ
		// Endereco
		// Bairro
		// Cidade
		// UF
		// CEP
		// Telefone1
		// Telefone2
		// Fax
		// DirigenteResponsavel
		// Email
		// Site_Igreja
		// Email_da_igreja
		// Modelo
		// Data_de_Fundacao

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// Id_igreja
			$this->Id_igreja->ViewValue = $this->Id_igreja->CurrentValue;
			$this->Id_igreja->ViewCustomAttributes = "";

			// Igreja
			$this->Igreja->ViewValue = $this->Igreja->CurrentValue;
			$this->Igreja->ViewCustomAttributes = "";

			// CNPJ
			$this->CNPJ->ViewValue = $this->CNPJ->CurrentValue;
			$this->CNPJ->ViewCustomAttributes = "";

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

			// Telefone1
			$this->Telefone1->ViewValue = $this->Telefone1->CurrentValue;
			$this->Telefone1->ViewCustomAttributes = "";

			// Telefone2
			$this->Telefone2->ViewValue = $this->Telefone2->CurrentValue;
			$this->Telefone2->ViewCustomAttributes = "";

			// Fax
			$this->Fax->ViewValue = $this->Fax->CurrentValue;
			$this->Fax->ViewCustomAttributes = "";

			// DirigenteResponsavel
			$this->DirigenteResponsavel->ViewValue = $this->DirigenteResponsavel->CurrentValue;
			$this->DirigenteResponsavel->ViewCustomAttributes = "";

			// Email
			$this->_Email->ViewValue = $this->_Email->CurrentValue;
			$this->_Email->ViewCustomAttributes = "";

			// Site_Igreja
			$this->Site_Igreja->ViewValue = $this->Site_Igreja->CurrentValue;
			$this->Site_Igreja->ViewCustomAttributes = "";

			// Email_da_igreja
			$this->Email_da_igreja->ViewValue = $this->Email_da_igreja->CurrentValue;
			$this->Email_da_igreja->ViewCustomAttributes = "";

			// Modelo
			if (strval($this->Modelo->CurrentValue) <> "") {
				$sFilterWrk = "`Id`" . ew_SearchString("=", $this->Modelo->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `Id`, `Modelo` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `modelo_igreja`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Modelo, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `Modelo` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->Modelo->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->Modelo->ViewValue = $this->Modelo->CurrentValue;
				}
			} else {
				$this->Modelo->ViewValue = NULL;
			}
			$this->Modelo->ViewCustomAttributes = "";

			// Data_de_Fundacao
			$this->Data_de_Fundacao->ViewValue = $this->Data_de_Fundacao->CurrentValue;
			$this->Data_de_Fundacao->ViewValue = ew_FormatDateTime($this->Data_de_Fundacao->ViewValue, 7);
			$this->Data_de_Fundacao->ViewCustomAttributes = "";

			// Id_igreja
			$this->Id_igreja->LinkCustomAttributes = "";
			$this->Id_igreja->HrefValue = "";
			$this->Id_igreja->TooltipValue = "";

			// Igreja
			$this->Igreja->LinkCustomAttributes = "";
			$this->Igreja->HrefValue = "";
			$this->Igreja->TooltipValue = "";

			// CNPJ
			$this->CNPJ->LinkCustomAttributes = "";
			$this->CNPJ->HrefValue = "";
			$this->CNPJ->TooltipValue = "";

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

			// Telefone1
			$this->Telefone1->LinkCustomAttributes = "";
			$this->Telefone1->HrefValue = "";
			$this->Telefone1->TooltipValue = "";

			// Telefone2
			$this->Telefone2->LinkCustomAttributes = "";
			$this->Telefone2->HrefValue = "";
			$this->Telefone2->TooltipValue = "";

			// Fax
			$this->Fax->LinkCustomAttributes = "";
			$this->Fax->HrefValue = "";
			$this->Fax->TooltipValue = "";

			// DirigenteResponsavel
			$this->DirigenteResponsavel->LinkCustomAttributes = "";
			$this->DirigenteResponsavel->HrefValue = "";
			$this->DirigenteResponsavel->TooltipValue = "";

			// Email
			$this->_Email->LinkCustomAttributes = "";
			$this->_Email->HrefValue = "";
			$this->_Email->TooltipValue = "";

			// Site_Igreja
			$this->Site_Igreja->LinkCustomAttributes = "";
			$this->Site_Igreja->HrefValue = "";
			$this->Site_Igreja->TooltipValue = "";

			// Email_da_igreja
			$this->Email_da_igreja->LinkCustomAttributes = "";
			$this->Email_da_igreja->HrefValue = "";
			$this->Email_da_igreja->TooltipValue = "";

			// Modelo
			$this->Modelo->LinkCustomAttributes = "";
			$this->Modelo->HrefValue = "";
			$this->Modelo->TooltipValue = "";

			// Data_de_Fundacao
			$this->Data_de_Fundacao->LinkCustomAttributes = "";
			$this->Data_de_Fundacao->HrefValue = "";
			$this->Data_de_Fundacao->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// Id_igreja
			$this->Id_igreja->EditAttrs["class"] = "form-control";
			$this->Id_igreja->EditCustomAttributes = "";

			// Igreja
			$this->Igreja->EditAttrs["class"] = "form-control";
			$this->Igreja->EditCustomAttributes = "";
			$this->Igreja->EditValue = ew_HtmlEncode($this->Igreja->CurrentValue);

			// CNPJ
			$this->CNPJ->EditAttrs["class"] = "form-control";
			$this->CNPJ->EditCustomAttributes = "";
			$this->CNPJ->EditValue = ew_HtmlEncode($this->CNPJ->CurrentValue);

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

			// Telefone1
			$this->Telefone1->EditAttrs["class"] = "form-control";
			$this->Telefone1->EditCustomAttributes = "";
			$this->Telefone1->EditValue = ew_HtmlEncode($this->Telefone1->CurrentValue);

			// Telefone2
			$this->Telefone2->EditAttrs["class"] = "form-control";
			$this->Telefone2->EditCustomAttributes = "";
			$this->Telefone2->EditValue = ew_HtmlEncode($this->Telefone2->CurrentValue);

			// Fax
			$this->Fax->EditAttrs["class"] = "form-control";
			$this->Fax->EditCustomAttributes = "";
			$this->Fax->EditValue = ew_HtmlEncode($this->Fax->CurrentValue);

			// DirigenteResponsavel
			$this->DirigenteResponsavel->EditAttrs["class"] = "form-control";
			$this->DirigenteResponsavel->EditCustomAttributes = "";
			$this->DirigenteResponsavel->EditValue = ew_HtmlEncode($this->DirigenteResponsavel->CurrentValue);

			// Email
			$this->_Email->EditAttrs["class"] = "form-control";
			$this->_Email->EditCustomAttributes = "";
			$this->_Email->EditValue = ew_HtmlEncode($this->_Email->CurrentValue);

			// Site_Igreja
			$this->Site_Igreja->EditAttrs["class"] = "form-control";
			$this->Site_Igreja->EditCustomAttributes = "";
			$this->Site_Igreja->EditValue = ew_HtmlEncode($this->Site_Igreja->CurrentValue);

			// Email_da_igreja
			$this->Email_da_igreja->EditAttrs["class"] = "form-control";
			$this->Email_da_igreja->EditCustomAttributes = "";
			$this->Email_da_igreja->EditValue = ew_HtmlEncode($this->Email_da_igreja->CurrentValue);

			// Modelo
			$this->Modelo->EditAttrs["class"] = "form-control";
			$this->Modelo->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `Id`, `Modelo` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `modelo_igreja`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Modelo, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `Modelo` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->Modelo->EditValue = $arwrk;

			// Data_de_Fundacao
			$this->Data_de_Fundacao->EditAttrs["class"] = "form-control";
			$this->Data_de_Fundacao->EditCustomAttributes = "";
			$this->Data_de_Fundacao->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->Data_de_Fundacao->CurrentValue, 7));

			// Edit refer script
			// Id_igreja

			$this->Id_igreja->HrefValue = "";

			// Igreja
			$this->Igreja->HrefValue = "";

			// CNPJ
			$this->CNPJ->HrefValue = "";

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

			// Telefone1
			$this->Telefone1->HrefValue = "";

			// Telefone2
			$this->Telefone2->HrefValue = "";

			// Fax
			$this->Fax->HrefValue = "";

			// DirigenteResponsavel
			$this->DirigenteResponsavel->HrefValue = "";

			// Email
			$this->_Email->HrefValue = "";

			// Site_Igreja
			$this->Site_Igreja->HrefValue = "";

			// Email_da_igreja
			$this->Email_da_igreja->HrefValue = "";

			// Modelo
			$this->Modelo->HrefValue = "";

			// Data_de_Fundacao
			$this->Data_de_Fundacao->HrefValue = "";
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
		if (!$this->Igreja->FldIsDetailKey && !is_null($this->Igreja->FormValue) && $this->Igreja->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Igreja->FldCaption(), $this->Igreja->ReqErrMsg));
		}
		if (!$this->CNPJ->FldIsDetailKey && !is_null($this->CNPJ->FormValue) && $this->CNPJ->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->CNPJ->FldCaption(), $this->CNPJ->ReqErrMsg));
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
		if (!$this->DirigenteResponsavel->FldIsDetailKey && !is_null($this->DirigenteResponsavel->FormValue) && $this->DirigenteResponsavel->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->DirigenteResponsavel->FldCaption(), $this->DirigenteResponsavel->ReqErrMsg));
		}
		if (!$this->_Email->FldIsDetailKey && !is_null($this->_Email->FormValue) && $this->_Email->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->_Email->FldCaption(), $this->_Email->ReqErrMsg));
		}
		if (!ew_CheckEmail($this->_Email->FormValue)) {
			ew_AddMessage($gsFormError, $this->_Email->FldErrMsg());
		}
		if (!$this->Site_Igreja->FldIsDetailKey && !is_null($this->Site_Igreja->FormValue) && $this->Site_Igreja->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Site_Igreja->FldCaption(), $this->Site_Igreja->ReqErrMsg));
		}
		if (!ew_CheckEmail($this->Email_da_igreja->FormValue)) {
			ew_AddMessage($gsFormError, $this->Email_da_igreja->FldErrMsg());
		}
		if (!$this->Modelo->FldIsDetailKey && !is_null($this->Modelo->FormValue) && $this->Modelo->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Modelo->FldCaption(), $this->Modelo->ReqErrMsg));
		}
		if (!$this->Data_de_Fundacao->FldIsDetailKey && !is_null($this->Data_de_Fundacao->FormValue) && $this->Data_de_Fundacao->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Data_de_Fundacao->FldCaption(), $this->Data_de_Fundacao->ReqErrMsg));
		}
		if (!ew_CheckEuroDate($this->Data_de_Fundacao->FormValue)) {
			ew_AddMessage($gsFormError, $this->Data_de_Fundacao->FldErrMsg());
		}

		// Validate detail grid
		$DetailTblVar = explode(",", $this->getCurrentDetailTable());
		if (in_array("membro", $DetailTblVar) && $GLOBALS["membro"]->DetailEdit) {
			if (!isset($GLOBALS["membro_grid"])) $GLOBALS["membro_grid"] = new cmembro_grid(); // get detail page object
			$GLOBALS["membro_grid"]->ValidateGridForm();
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
		if ($this->Igreja->CurrentValue <> "") { // Check field with unique index
			$sFilterChk = "(`Igreja` = '" . ew_AdjustSql($this->Igreja->CurrentValue) . "')";
			$sFilterChk .= " AND NOT (" . $sFilter . ")";
			$this->CurrentFilter = $sFilterChk;
			$sSqlChk = $this->SQL();
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			$rsChk = $conn->Execute($sSqlChk);
			$conn->raiseErrorFn = '';
			if ($rsChk === FALSE) {
				return FALSE;
			} elseif (!$rsChk->EOF) {
				$sIdxErrMsg = str_replace("%f", $this->Igreja->FldCaption(), $Language->Phrase("DupIndex"));
				$sIdxErrMsg = str_replace("%v", $this->Igreja->CurrentValue, $sIdxErrMsg);
				$this->setFailureMessage($sIdxErrMsg);
				$rsChk->Close();
				return FALSE;
			}
			$rsChk->Close();
		}
		if ($this->_Email->CurrentValue <> "") { // Check field with unique index
			$sFilterChk = "(`Email` = '" . ew_AdjustSql($this->_Email->CurrentValue) . "')";
			$sFilterChk .= " AND NOT (" . $sFilter . ")";
			$this->CurrentFilter = $sFilterChk;
			$sSqlChk = $this->SQL();
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			$rsChk = $conn->Execute($sSqlChk);
			$conn->raiseErrorFn = '';
			if ($rsChk === FALSE) {
				return FALSE;
			} elseif (!$rsChk->EOF) {
				$sIdxErrMsg = str_replace("%f", $this->_Email->FldCaption(), $Language->Phrase("DupIndex"));
				$sIdxErrMsg = str_replace("%v", $this->_Email->CurrentValue, $sIdxErrMsg);
				$this->setFailureMessage($sIdxErrMsg);
				$rsChk->Close();
				return FALSE;
			}
			$rsChk->Close();
		}
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

			// Begin transaction
			if ($this->getCurrentDetailTable() <> "")
				$conn->BeginTrans();

			// Save old values
			$rsold = &$rs->fields;
			$this->LoadDbValues($rsold);
			$rsnew = array();

			// Igreja
			$this->Igreja->SetDbValueDef($rsnew, $this->Igreja->CurrentValue, NULL, $this->Igreja->ReadOnly);

			// CNPJ
			$this->CNPJ->SetDbValueDef($rsnew, $this->CNPJ->CurrentValue, "", $this->CNPJ->ReadOnly);

			// Endereco
			$this->Endereco->SetDbValueDef($rsnew, $this->Endereco->CurrentValue, "", $this->Endereco->ReadOnly);

			// Bairro
			$this->Bairro->SetDbValueDef($rsnew, $this->Bairro->CurrentValue, NULL, $this->Bairro->ReadOnly);

			// Cidade
			$this->Cidade->SetDbValueDef($rsnew, $this->Cidade->CurrentValue, NULL, $this->Cidade->ReadOnly);

			// UF
			$this->UF->SetDbValueDef($rsnew, $this->UF->CurrentValue, NULL, $this->UF->ReadOnly);

			// CEP
			$this->CEP->SetDbValueDef($rsnew, $this->CEP->CurrentValue, NULL, $this->CEP->ReadOnly);

			// Telefone1
			$this->Telefone1->SetDbValueDef($rsnew, $this->Telefone1->CurrentValue, NULL, $this->Telefone1->ReadOnly);

			// Telefone2
			$this->Telefone2->SetDbValueDef($rsnew, $this->Telefone2->CurrentValue, NULL, $this->Telefone2->ReadOnly);

			// Fax
			$this->Fax->SetDbValueDef($rsnew, $this->Fax->CurrentValue, NULL, $this->Fax->ReadOnly);

			// DirigenteResponsavel
			$this->DirigenteResponsavel->SetDbValueDef($rsnew, $this->DirigenteResponsavel->CurrentValue, NULL, $this->DirigenteResponsavel->ReadOnly);

			// Email
			$this->_Email->SetDbValueDef($rsnew, $this->_Email->CurrentValue, NULL, $this->_Email->ReadOnly);

			// Site_Igreja
			$this->Site_Igreja->SetDbValueDef($rsnew, $this->Site_Igreja->CurrentValue, NULL, $this->Site_Igreja->ReadOnly);

			// Email_da_igreja
			$this->Email_da_igreja->SetDbValueDef($rsnew, $this->Email_da_igreja->CurrentValue, NULL, $this->Email_da_igreja->ReadOnly);

			// Modelo
			$this->Modelo->SetDbValueDef($rsnew, $this->Modelo->CurrentValue, NULL, $this->Modelo->ReadOnly);

			// Data_de_Fundacao
			$this->Data_de_Fundacao->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->Data_de_Fundacao->CurrentValue, 7), NULL, $this->Data_de_Fundacao->ReadOnly);

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

				// Update detail records
				if ($EditRow) {
					$DetailTblVar = explode(",", $this->getCurrentDetailTable());
					if (in_array("membro", $DetailTblVar) && $GLOBALS["membro"]->DetailEdit) {
						if (!isset($GLOBALS["membro_grid"])) $GLOBALS["membro_grid"] = new cmembro_grid(); // Get detail page object
						$EditRow = $GLOBALS["membro_grid"]->GridUpdate();
					}
				}

				// Commit/Rollback transaction
				if ($this->getCurrentDetailTable() <> "") {
					if ($EditRow) {
						$conn->CommitTrans(); // Commit transaction
					} else {
						$conn->RollbackTrans(); // Rollback transaction
					}
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

	// Set up detail parms based on QueryString
	function SetUpDetailParms() {

		// Get the keys for master table
		if (isset($_GET[EW_TABLE_SHOW_DETAIL])) {
			$sDetailTblVar = $_GET[EW_TABLE_SHOW_DETAIL];
			$this->setCurrentDetailTable($sDetailTblVar);
		} else {
			$sDetailTblVar = $this->getCurrentDetailTable();
		}
		if ($sDetailTblVar <> "") {
			$DetailTblVar = explode(",", $sDetailTblVar);
			if (in_array("membro", $DetailTblVar)) {
				if (!isset($GLOBALS["membro_grid"]))
					$GLOBALS["membro_grid"] = new cmembro_grid;
				if ($GLOBALS["membro_grid"]->DetailEdit) {
					$GLOBALS["membro_grid"]->CurrentMode = "edit";
					$GLOBALS["membro_grid"]->CurrentAction = "gridedit";

					// Save current master table to detail table
					$GLOBALS["membro_grid"]->setCurrentMasterTable($this->TableVar);
					$GLOBALS["membro_grid"]->setStartRecordNumber(1);
					$GLOBALS["membro_grid"]->Da_Igreja->FldIsDetailKey = TRUE;
					$GLOBALS["membro_grid"]->Da_Igreja->CurrentValue = $this->Id_igreja->CurrentValue;
					$GLOBALS["membro_grid"]->Da_Igreja->setSessionValue($GLOBALS["membro_grid"]->Da_Igreja->CurrentValue);
				}
			}
		}
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, "igrejaslist.php", "", $this->TableVar, TRUE);
		$PageId = "edit";
		$Breadcrumb->Add("edit", $PageId, $url);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'igrejas';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (edit page)
	function WriteAuditTrailOnEdit(&$rsold, &$rsnew) {
		if (!$this->AuditTrailOnEdit) return;
		$table = 'igrejas';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rsold['Id_igreja'];

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
if (!isset($igrejas_edit)) $igrejas_edit = new cigrejas_edit();

// Page init
$igrejas_edit->Page_Init();

// Page main
$igrejas_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$igrejas_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var igrejas_edit = new ew_Page("igrejas_edit");
igrejas_edit.PageID = "edit"; // Page ID
var EW_PAGE_ID = igrejas_edit.PageID; // For backward compatibility

// Form object
var figrejasedit = new ew_Form("figrejasedit");

// Validate form
figrejasedit.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_Igreja");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $igrejas->Igreja->FldCaption(), $igrejas->Igreja->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_CNPJ");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $igrejas->CNPJ->FldCaption(), $igrejas->CNPJ->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Endereco");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $igrejas->Endereco->FldCaption(), $igrejas->Endereco->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Bairro");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $igrejas->Bairro->FldCaption(), $igrejas->Bairro->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Cidade");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $igrejas->Cidade->FldCaption(), $igrejas->Cidade->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_UF");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $igrejas->UF->FldCaption(), $igrejas->UF->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_CEP");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $igrejas->CEP->FldCaption(), $igrejas->CEP->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_DirigenteResponsavel");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $igrejas->DirigenteResponsavel->FldCaption(), $igrejas->DirigenteResponsavel->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "__Email");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $igrejas->_Email->FldCaption(), $igrejas->_Email->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "__Email");
			if (elm && !ew_CheckEmail(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($igrejas->_Email->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Site_Igreja");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $igrejas->Site_Igreja->FldCaption(), $igrejas->Site_Igreja->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Email_da_igreja");
			if (elm && !ew_CheckEmail(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($igrejas->Email_da_igreja->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Modelo");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $igrejas->Modelo->FldCaption(), $igrejas->Modelo->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Data_de_Fundacao");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $igrejas->Data_de_Fundacao->FldCaption(), $igrejas->Data_de_Fundacao->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Data_de_Fundacao");
			if (elm && !ew_CheckEuroDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($igrejas->Data_de_Fundacao->FldErrMsg()) ?>");

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
figrejasedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
figrejasedit.ValidateRequired = true;
<?php } else { ?>
figrejasedit.ValidateRequired = false; 
<?php } ?>

// Multi-Page properties
figrejasedit.MultiPage = new ew_MultiPage("figrejasedit",
	[["x_Igreja",1],["x_CNPJ",1],["x_Endereco",2],["x_Bairro",2],["x_Cidade",2],["x_UF",2],["x_CEP",2],["x_Telefone1",2],["x_Telefone2",2],["x_Fax",2],["x_DirigenteResponsavel",1],["x__Email",1],["x_Site_Igreja",1],["x_Email_da_igreja",1],["x_Modelo",1],["x_Data_de_Fundacao",1]]
);

// Dynamic selection lists
figrejasedit.Lists["x_Modelo"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Modelo","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

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
<?php $igrejas_edit->ShowPageHeader(); ?>
<?php
$igrejas_edit->ShowMessage();
?>
<form name="figrejasedit" id="figrejasedit" class="form-horizontal ewForm ewEditForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($igrejas_edit->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $igrejas_edit->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="igrejas">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<div>
<div class="tabbable" id="igrejas_edit">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab_igrejas1" data-toggle="tab"><?php echo $igrejas->PageCaption(1) ?></a></li>
		<li><a href="#tab_igrejas2" data-toggle="tab"><?php echo $igrejas->PageCaption(2) ?></a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="tab_igrejas1">
<div>
<?php if ($igrejas->Igreja->Visible) { // Igreja ?>
	<div id="r_Igreja" class="form-group">
		<label id="elh_igrejas_Igreja" for="x_Igreja" class="col-sm-2 control-label ewLabel"><?php echo $igrejas->Igreja->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $igrejas->Igreja->CellAttributes() ?>>
<span id="el_igrejas_Igreja">
<input type="text" data-field="x_Igreja" name="x_Igreja" id="x_Igreja" size="70" maxlength="50" value="<?php echo $igrejas->Igreja->EditValue ?>"<?php echo $igrejas->Igreja->EditAttributes() ?>>
</span>
<?php echo $igrejas->Igreja->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($igrejas->CNPJ->Visible) { // CNPJ ?>
	<div id="r_CNPJ" class="form-group">
		<label id="elh_igrejas_CNPJ" for="x_CNPJ" class="col-sm-2 control-label ewLabel"><?php echo $igrejas->CNPJ->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $igrejas->CNPJ->CellAttributes() ?>>
<span id="el_igrejas_CNPJ">
<input type="text" data-field="x_CNPJ" name="x_CNPJ" id="x_CNPJ" size="30" maxlength="20" value="<?php echo $igrejas->CNPJ->EditValue ?>"<?php echo $igrejas->CNPJ->EditAttributes() ?>>
</span>
<?php echo $igrejas->CNPJ->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($igrejas->DirigenteResponsavel->Visible) { // DirigenteResponsavel ?>
	<div id="r_DirigenteResponsavel" class="form-group">
		<label id="elh_igrejas_DirigenteResponsavel" for="x_DirigenteResponsavel" class="col-sm-2 control-label ewLabel"><?php echo $igrejas->DirigenteResponsavel->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $igrejas->DirigenteResponsavel->CellAttributes() ?>>
<span id="el_igrejas_DirigenteResponsavel">
<input type="text" data-field="x_DirigenteResponsavel" name="x_DirigenteResponsavel" id="x_DirigenteResponsavel" size="70" maxlength="100" value="<?php echo $igrejas->DirigenteResponsavel->EditValue ?>"<?php echo $igrejas->DirigenteResponsavel->EditAttributes() ?>>
</span>
<?php echo $igrejas->DirigenteResponsavel->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($igrejas->_Email->Visible) { // Email ?>
	<div id="r__Email" class="form-group">
		<label id="elh_igrejas__Email" for="x__Email" class="col-sm-2 control-label ewLabel"><?php echo $igrejas->_Email->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $igrejas->_Email->CellAttributes() ?>>
<span id="el_igrejas__Email">
<input type="text" data-field="x__Email" name="x__Email" id="x__Email" size="30" maxlength="100" value="<?php echo $igrejas->_Email->EditValue ?>"<?php echo $igrejas->_Email->EditAttributes() ?>>
</span>
<?php echo $igrejas->_Email->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($igrejas->Site_Igreja->Visible) { // Site_Igreja ?>
	<div id="r_Site_Igreja" class="form-group">
		<label id="elh_igrejas_Site_Igreja" for="x_Site_Igreja" class="col-sm-2 control-label ewLabel"><?php echo $igrejas->Site_Igreja->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $igrejas->Site_Igreja->CellAttributes() ?>>
<span id="el_igrejas_Site_Igreja">
<input type="text" data-field="x_Site_Igreja" name="x_Site_Igreja" id="x_Site_Igreja" size="50" maxlength="100" value="<?php echo $igrejas->Site_Igreja->EditValue ?>"<?php echo $igrejas->Site_Igreja->EditAttributes() ?>>
</span>
<?php echo $igrejas->Site_Igreja->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($igrejas->Email_da_igreja->Visible) { // Email_da_igreja ?>
	<div id="r_Email_da_igreja" class="form-group">
		<label id="elh_igrejas_Email_da_igreja" for="x_Email_da_igreja" class="col-sm-2 control-label ewLabel"><?php echo $igrejas->Email_da_igreja->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $igrejas->Email_da_igreja->CellAttributes() ?>>
<span id="el_igrejas_Email_da_igreja">
<input type="text" data-field="x_Email_da_igreja" name="x_Email_da_igreja" id="x_Email_da_igreja" size="40" maxlength="100" value="<?php echo $igrejas->Email_da_igreja->EditValue ?>"<?php echo $igrejas->Email_da_igreja->EditAttributes() ?>>
</span>
<?php echo $igrejas->Email_da_igreja->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($igrejas->Modelo->Visible) { // Modelo ?>
	<div id="r_Modelo" class="form-group">
		<label id="elh_igrejas_Modelo" for="x_Modelo" class="col-sm-2 control-label ewLabel"><?php echo $igrejas->Modelo->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $igrejas->Modelo->CellAttributes() ?>>
<span id="el_igrejas_Modelo">
<select data-field="x_Modelo" id="x_Modelo" name="x_Modelo"<?php echo $igrejas->Modelo->EditAttributes() ?>>
<?php
if (is_array($igrejas->Modelo->EditValue)) {
	$arwrk = $igrejas->Modelo->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($igrejas->Modelo->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php if (AllowAdd(CurrentProjectID() . "modelo_igreja")) { ?>
<button type="button" title="<?php echo ew_HtmlTitle($Language->Phrase("AddLink")) . "&nbsp;" . $igrejas->Modelo->FldCaption() ?>" onclick="ew_AddOptDialogShow({lnk:this,el:'x_Modelo',url:'modelo_igrejaaddopt.php'});" class="ewAddOptBtn btn btn-default btn-sm" id="aol_x_Modelo"><span class="glyphicon glyphicon-plus ewIcon"></span><span class="hide"><?php echo $Language->Phrase("AddLink") ?>&nbsp;<?php echo $igrejas->Modelo->FldCaption() ?></span></button>
<?php } ?>
<script type="text/javascript">
figrejasedit.Lists["x_Modelo"].Options = <?php echo (is_array($igrejas->Modelo->EditValue)) ? ew_ArrayToJson($igrejas->Modelo->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $igrejas->Modelo->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($igrejas->Data_de_Fundacao->Visible) { // Data_de_Fundacao ?>
	<div id="r_Data_de_Fundacao" class="form-group">
		<label id="elh_igrejas_Data_de_Fundacao" for="x_Data_de_Fundacao" class="col-sm-2 control-label ewLabel"><?php echo $igrejas->Data_de_Fundacao->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $igrejas->Data_de_Fundacao->CellAttributes() ?>>
<span id="el_igrejas_Data_de_Fundacao">
<input type="text" data-field="x_Data_de_Fundacao" name="x_Data_de_Fundacao" id="x_Data_de_Fundacao" size="14" value="<?php echo $igrejas->Data_de_Fundacao->EditValue ?>"<?php echo $igrejas->Data_de_Fundacao->EditAttributes() ?>>
<?php if (!$igrejas->Data_de_Fundacao->ReadOnly && !$igrejas->Data_de_Fundacao->Disabled && @$igrejas->Data_de_Fundacao->EditAttrs["readonly"] == "" && @$igrejas->Data_de_Fundacao->EditAttrs["disabled"] == "") { ?>
<script type="text/javascript">
ew_CreateCalendar("figrejasedit", "x_Data_de_Fundacao", "%d/%m/%Y");
</script>
<?php } ?>
</span>
<?php echo $igrejas->Data_de_Fundacao->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
		</div>
		<div class="tab-pane" id="tab_igrejas2">
<div>
<?php if ($igrejas->Endereco->Visible) { // Endereco ?>
	<div id="r_Endereco" class="form-group">
		<label id="elh_igrejas_Endereco" for="x_Endereco" class="col-sm-2 control-label ewLabel"><?php echo $igrejas->Endereco->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $igrejas->Endereco->CellAttributes() ?>>
<span id="el_igrejas_Endereco">
<input type="text" data-field="x_Endereco" name="x_Endereco" id="x_Endereco" size="60" maxlength="50" value="<?php echo $igrejas->Endereco->EditValue ?>"<?php echo $igrejas->Endereco->EditAttributes() ?>>
</span>
<?php echo $igrejas->Endereco->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($igrejas->Bairro->Visible) { // Bairro ?>
	<div id="r_Bairro" class="form-group">
		<label id="elh_igrejas_Bairro" for="x_Bairro" class="col-sm-2 control-label ewLabel"><?php echo $igrejas->Bairro->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $igrejas->Bairro->CellAttributes() ?>>
<span id="el_igrejas_Bairro">
<input type="text" data-field="x_Bairro" name="x_Bairro" id="x_Bairro" size="30" maxlength="25" value="<?php echo $igrejas->Bairro->EditValue ?>"<?php echo $igrejas->Bairro->EditAttributes() ?>>
</span>
<?php echo $igrejas->Bairro->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($igrejas->Cidade->Visible) { // Cidade ?>
	<div id="r_Cidade" class="form-group">
		<label id="elh_igrejas_Cidade" for="x_Cidade" class="col-sm-2 control-label ewLabel"><?php echo $igrejas->Cidade->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $igrejas->Cidade->CellAttributes() ?>>
<span id="el_igrejas_Cidade">
<input type="text" data-field="x_Cidade" name="x_Cidade" id="x_Cidade" size="30" maxlength="22" value="<?php echo $igrejas->Cidade->EditValue ?>"<?php echo $igrejas->Cidade->EditAttributes() ?>>
</span>
<?php echo $igrejas->Cidade->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($igrejas->UF->Visible) { // UF ?>
	<div id="r_UF" class="form-group">
		<label id="elh_igrejas_UF" for="x_UF" class="col-sm-2 control-label ewLabel"><?php echo $igrejas->UF->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $igrejas->UF->CellAttributes() ?>>
<span id="el_igrejas_UF">
<input type="text" data-field="x_UF" name="x_UF" id="x_UF" size="5" maxlength="2" value="<?php echo $igrejas->UF->EditValue ?>"<?php echo $igrejas->UF->EditAttributes() ?>>
</span>
<?php echo $igrejas->UF->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($igrejas->CEP->Visible) { // CEP ?>
	<div id="r_CEP" class="form-group">
		<label id="elh_igrejas_CEP" for="x_CEP" class="col-sm-2 control-label ewLabel"><?php echo $igrejas->CEP->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $igrejas->CEP->CellAttributes() ?>>
<span id="el_igrejas_CEP">
<input type="text" data-field="x_CEP" name="x_CEP" id="x_CEP" size="12" maxlength="9" value="<?php echo $igrejas->CEP->EditValue ?>"<?php echo $igrejas->CEP->EditAttributes() ?>>
</span>
<?php echo $igrejas->CEP->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($igrejas->Telefone1->Visible) { // Telefone1 ?>
	<div id="r_Telefone1" class="form-group">
		<label id="elh_igrejas_Telefone1" for="x_Telefone1" class="col-sm-2 control-label ewLabel"><?php echo $igrejas->Telefone1->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $igrejas->Telefone1->CellAttributes() ?>>
<span id="el_igrejas_Telefone1">
<input type="text" data-field="x_Telefone1" name="x_Telefone1" id="x_Telefone1" size="15" maxlength="15" value="<?php echo $igrejas->Telefone1->EditValue ?>"<?php echo $igrejas->Telefone1->EditAttributes() ?>>
</span>
<?php echo $igrejas->Telefone1->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($igrejas->Telefone2->Visible) { // Telefone2 ?>
	<div id="r_Telefone2" class="form-group">
		<label id="elh_igrejas_Telefone2" for="x_Telefone2" class="col-sm-2 control-label ewLabel"><?php echo $igrejas->Telefone2->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $igrejas->Telefone2->CellAttributes() ?>>
<span id="el_igrejas_Telefone2">
<input type="text" data-field="x_Telefone2" name="x_Telefone2" id="x_Telefone2" size="15" maxlength="15" value="<?php echo $igrejas->Telefone2->EditValue ?>"<?php echo $igrejas->Telefone2->EditAttributes() ?>>
</span>
<?php echo $igrejas->Telefone2->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($igrejas->Fax->Visible) { // Fax ?>
	<div id="r_Fax" class="form-group">
		<label id="elh_igrejas_Fax" for="x_Fax" class="col-sm-2 control-label ewLabel"><?php echo $igrejas->Fax->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $igrejas->Fax->CellAttributes() ?>>
<span id="el_igrejas_Fax">
<input type="text" data-field="x_Fax" name="x_Fax" id="x_Fax" size="15" maxlength="15" value="<?php echo $igrejas->Fax->EditValue ?>"<?php echo $igrejas->Fax->EditAttributes() ?>>
</span>
<?php echo $igrejas->Fax->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
		</div>
	</div>
</div>
</div>
<span id="el_igrejas_Id_igreja">
<input type="hidden" data-field="x_Id_igreja" name="x_Id_igreja" id="x_Id_igreja" value="<?php echo ew_HtmlEncode($igrejas->Id_igreja->CurrentValue) ?>">
</span>
<?php
	if (in_array("membro", explode(",", $igrejas->getCurrentDetailTable())) && $membro->DetailEdit) {
?>
<?php if ($igrejas->getCurrentDetailTable() <> "") { ?>
<h4 class="ewDetailCaption"><?php echo $Language->TablePhrase("membro", "TblCaption") ?></h4>
<?php } ?>
<?php include_once "membrogrid.php" ?>
<?php } ?>
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><i class="fa fa-floppy-o"></i>&nbsp;<?php echo $Language->Phrase("SaveBtn") ?></button>
	</div>
</div>
</form>
<script type="text/javascript">
figrejasedit.Init();
</script>
<?php
$igrejas_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$igrejas_edit->Page_Terminate();
?>
