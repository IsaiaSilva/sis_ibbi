<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg11.php" ?>
<?php include_once "ewmysql11.php" ?>
<?php include_once "phpfn11.php" ?>
<?php include_once "celulasinfo.php" ?>
<?php include_once "usuariosinfo.php" ?>
<?php include_once "membrogridcls.php" ?>
<?php include_once "userfn11.php" ?>
<?php

//
// Page class
//

$celulas_add = NULL; // Initialize page object first

class ccelulas_add extends ccelulas {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{2B7992FC-5911-46A7-9310-01F4D4225C49}";

	// Table name
	var $TableName = 'celulas';

	// Page object name
	var $PageObjName = 'celulas_add';

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

		// Table object (celulas)
		if (!isset($GLOBALS["celulas"]) || get_class($GLOBALS["celulas"]) == "ccelulas") {
			$GLOBALS["celulas"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["celulas"];
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
			define("EW_TABLE_NAME", 'celulas', TRUE);

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
			$this->Page_Terminate(ew_GetUrl("celulaslist.php"));
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
		global $EW_EXPORT, $celulas;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($celulas);
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
			if (@$_GET["Id_celula"] != "") {
				$this->Id_celula->setQueryStringValue($_GET["Id_celula"]);
				$this->setKey("Id_celula", $this->Id_celula->CurrentValue); // Set up key
			} else {
				$this->setKey("Id_celula", ""); // Clear key
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

		// Set up detail parameters
		$this->SetUpDetailParms();

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
					$this->Page_Terminate("celulaslist.php"); // No matching record, return to list
				}

				// Set up detail parameters
				$this->SetUpDetailParms();
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					if ($this->getCurrentDetailTable() <> "") // Master/detail add
						$sReturnUrl = $this->GetDetailUrl();
					else
						$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "celulasview.php")
						$sReturnUrl = $this->GetViewUrl(); // View paging, return to view page with keyurl directly
					$this->Page_Terminate($sReturnUrl); // Clean up and return
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Add failed, restore form values

					// Set up detail parameters
					$this->SetUpDetailParms();
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
		$this->NomeCelula->CurrentValue = NULL;
		$this->NomeCelula->OldValue = $this->NomeCelula->CurrentValue;
		$this->Responsavel->CurrentValue = NULL;
		$this->Responsavel->OldValue = $this->Responsavel->CurrentValue;
		$this->DiasReunioes->CurrentValue = NULL;
		$this->DiasReunioes->OldValue = $this->DiasReunioes->CurrentValue;
		$this->HorarioReunioes->CurrentValue = NULL;
		$this->HorarioReunioes->OldValue = $this->HorarioReunioes->CurrentValue;
		$this->Endereco->CurrentValue = NULL;
		$this->Endereco->OldValue = $this->Endereco->CurrentValue;
		$this->Anotacoes->CurrentValue = NULL;
		$this->Anotacoes->OldValue = $this->Anotacoes->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->NomeCelula->FldIsDetailKey) {
			$this->NomeCelula->setFormValue($objForm->GetValue("x_NomeCelula"));
		}
		if (!$this->Responsavel->FldIsDetailKey) {
			$this->Responsavel->setFormValue($objForm->GetValue("x_Responsavel"));
		}
		if (!$this->DiasReunioes->FldIsDetailKey) {
			$this->DiasReunioes->setFormValue($objForm->GetValue("x_DiasReunioes"));
		}
		if (!$this->HorarioReunioes->FldIsDetailKey) {
			$this->HorarioReunioes->setFormValue($objForm->GetValue("x_HorarioReunioes"));
		}
		if (!$this->Endereco->FldIsDetailKey) {
			$this->Endereco->setFormValue($objForm->GetValue("x_Endereco"));
		}
		if (!$this->Anotacoes->FldIsDetailKey) {
			$this->Anotacoes->setFormValue($objForm->GetValue("x_Anotacoes"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->NomeCelula->CurrentValue = $this->NomeCelula->FormValue;
		$this->Responsavel->CurrentValue = $this->Responsavel->FormValue;
		$this->DiasReunioes->CurrentValue = $this->DiasReunioes->FormValue;
		$this->HorarioReunioes->CurrentValue = $this->HorarioReunioes->FormValue;
		$this->Endereco->CurrentValue = $this->Endereco->FormValue;
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
		$this->Id_celula->setDbValue($rs->fields('Id_celula'));
		$this->NomeCelula->setDbValue($rs->fields('NomeCelula'));
		$this->Responsavel->setDbValue($rs->fields('Responsavel'));
		$this->DiasReunioes->setDbValue($rs->fields('DiasReunioes'));
		$this->HorarioReunioes->setDbValue($rs->fields('HorarioReunioes'));
		$this->Endereco->setDbValue($rs->fields('Endereco'));
		$this->Anotacoes->setDbValue($rs->fields('Anotacoes'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->Id_celula->DbValue = $row['Id_celula'];
		$this->NomeCelula->DbValue = $row['NomeCelula'];
		$this->Responsavel->DbValue = $row['Responsavel'];
		$this->DiasReunioes->DbValue = $row['DiasReunioes'];
		$this->HorarioReunioes->DbValue = $row['HorarioReunioes'];
		$this->Endereco->DbValue = $row['Endereco'];
		$this->Anotacoes->DbValue = $row['Anotacoes'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("Id_celula")) <> "")
			$this->Id_celula->CurrentValue = $this->getKey("Id_celula"); // Id_celula
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
		// Id_celula
		// NomeCelula
		// Responsavel
		// DiasReunioes
		// HorarioReunioes
		// Endereco
		// Anotacoes

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// NomeCelula
			$this->NomeCelula->ViewValue = $this->NomeCelula->CurrentValue;
			$this->NomeCelula->ViewCustomAttributes = "";

			// Responsavel
			$this->Responsavel->ViewValue = $this->Responsavel->CurrentValue;
			$this->Responsavel->ViewCustomAttributes = "";

			// DiasReunioes
			if (strval($this->DiasReunioes->CurrentValue) <> "") {
				$arwrk = explode(",", $this->DiasReunioes->CurrentValue);
				$sFilterWrk = "";
				foreach ($arwrk as $wrk) {
					if ($sFilterWrk <> "") $sFilterWrk .= " OR ";
					$sFilterWrk .= "`Dias`" . ew_SearchString("=", trim($wrk), EW_DATATYPE_STRING);
				}	
			$sSqlWrk = "SELECT `Dias`, `Dias` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `dias_semana`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->DiasReunioes, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->DiasReunioes->ViewValue = "";
					$ari = 0;
					while (!$rswrk->EOF) {
						$this->DiasReunioes->ViewValue .= $rswrk->fields('DispFld');
						$rswrk->MoveNext();
						if (!$rswrk->EOF) $this->DiasReunioes->ViewValue .= ew_ViewOptionSeparator($ari); // Separate Options
						$ari++;
					}
					$rswrk->Close();
				} else {
					$this->DiasReunioes->ViewValue = $this->DiasReunioes->CurrentValue;
				}
			} else {
				$this->DiasReunioes->ViewValue = NULL;
			}
			$this->DiasReunioes->ViewCustomAttributes = "";

			// HorarioReunioes
			$this->HorarioReunioes->ViewValue = $this->HorarioReunioes->CurrentValue;
			$this->HorarioReunioes->CellCssStyle .= "text-align: center;";
			$this->HorarioReunioes->ViewCustomAttributes = "";

			// Endereco
			$this->Endereco->ViewValue = $this->Endereco->CurrentValue;
			$this->Endereco->ViewCustomAttributes = "";

			// Anotacoes
			$this->Anotacoes->ViewValue = $this->Anotacoes->CurrentValue;
			$this->Anotacoes->ViewCustomAttributes = "";

			// NomeCelula
			$this->NomeCelula->LinkCustomAttributes = "";
			$this->NomeCelula->HrefValue = "";
			$this->NomeCelula->TooltipValue = "";

			// Responsavel
			$this->Responsavel->LinkCustomAttributes = "";
			$this->Responsavel->HrefValue = "";
			$this->Responsavel->TooltipValue = "";

			// DiasReunioes
			$this->DiasReunioes->LinkCustomAttributes = "";
			$this->DiasReunioes->HrefValue = "";
			$this->DiasReunioes->TooltipValue = "";

			// HorarioReunioes
			$this->HorarioReunioes->LinkCustomAttributes = "";
			$this->HorarioReunioes->HrefValue = "";
			$this->HorarioReunioes->TooltipValue = "";

			// Endereco
			$this->Endereco->LinkCustomAttributes = "";
			$this->Endereco->HrefValue = "";
			$this->Endereco->TooltipValue = "";

			// Anotacoes
			$this->Anotacoes->LinkCustomAttributes = "";
			$this->Anotacoes->HrefValue = "";
			$this->Anotacoes->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// NomeCelula
			$this->NomeCelula->EditAttrs["class"] = "form-control";
			$this->NomeCelula->EditCustomAttributes = "";
			$this->NomeCelula->EditValue = ew_HtmlEncode($this->NomeCelula->CurrentValue);

			// Responsavel
			$this->Responsavel->EditAttrs["class"] = "form-control";
			$this->Responsavel->EditCustomAttributes = "";
			$this->Responsavel->EditValue = ew_HtmlEncode($this->Responsavel->CurrentValue);

			// DiasReunioes
			$this->DiasReunioes->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `Dias`, `Dias` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `dias_semana`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->DiasReunioes, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->DiasReunioes->EditValue = $arwrk;

			// HorarioReunioes
			$this->HorarioReunioes->EditAttrs["class"] = "form-control";
			$this->HorarioReunioes->EditCustomAttributes = "";
			$this->HorarioReunioes->EditValue = ew_HtmlEncode($this->HorarioReunioes->CurrentValue);

			// Endereco
			$this->Endereco->EditAttrs["class"] = "form-control";
			$this->Endereco->EditCustomAttributes = "";
			$this->Endereco->EditValue = ew_HtmlEncode($this->Endereco->CurrentValue);

			// Anotacoes
			$this->Anotacoes->EditAttrs["class"] = "form-control";
			$this->Anotacoes->EditCustomAttributes = "";
			$this->Anotacoes->EditValue = ew_HtmlEncode($this->Anotacoes->CurrentValue);

			// Edit refer script
			// NomeCelula

			$this->NomeCelula->HrefValue = "";

			// Responsavel
			$this->Responsavel->HrefValue = "";

			// DiasReunioes
			$this->DiasReunioes->HrefValue = "";

			// HorarioReunioes
			$this->HorarioReunioes->HrefValue = "";

			// Endereco
			$this->Endereco->HrefValue = "";

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
		if (!$this->NomeCelula->FldIsDetailKey && !is_null($this->NomeCelula->FormValue) && $this->NomeCelula->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->NomeCelula->FldCaption(), $this->NomeCelula->ReqErrMsg));
		}
		if (!$this->Responsavel->FldIsDetailKey && !is_null($this->Responsavel->FormValue) && $this->Responsavel->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Responsavel->FldCaption(), $this->Responsavel->ReqErrMsg));
		}
		if ($this->DiasReunioes->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->DiasReunioes->FldCaption(), $this->DiasReunioes->ReqErrMsg));
		}
		if (!$this->HorarioReunioes->FldIsDetailKey && !is_null($this->HorarioReunioes->FormValue) && $this->HorarioReunioes->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->HorarioReunioes->FldCaption(), $this->HorarioReunioes->ReqErrMsg));
		}
		if (!ew_CheckTime($this->HorarioReunioes->FormValue)) {
			ew_AddMessage($gsFormError, $this->HorarioReunioes->FldErrMsg());
		}
		if (!$this->Anotacoes->FldIsDetailKey && !is_null($this->Anotacoes->FormValue) && $this->Anotacoes->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Anotacoes->FldCaption(), $this->Anotacoes->ReqErrMsg));
		}

		// Validate detail grid
		$DetailTblVar = explode(",", $this->getCurrentDetailTable());
		if (in_array("membro", $DetailTblVar) && $GLOBALS["membro"]->DetailAdd) {
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

	// Add record
	function AddRow($rsold = NULL) {
		global $conn, $Language, $Security;

		// Begin transaction
		if ($this->getCurrentDetailTable() <> "")
			$conn->BeginTrans();

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
		}
		$rsnew = array();

		// NomeCelula
		$this->NomeCelula->SetDbValueDef($rsnew, $this->NomeCelula->CurrentValue, NULL, FALSE);

		// Responsavel
		$this->Responsavel->SetDbValueDef($rsnew, $this->Responsavel->CurrentValue, NULL, FALSE);

		// DiasReunioes
		$this->DiasReunioes->SetDbValueDef($rsnew, $this->DiasReunioes->CurrentValue, NULL, FALSE);

		// HorarioReunioes
		$this->HorarioReunioes->SetDbValueDef($rsnew, $this->HorarioReunioes->CurrentValue, NULL, FALSE);

		// Endereco
		$this->Endereco->SetDbValueDef($rsnew, $this->Endereco->CurrentValue, NULL, FALSE);

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
			$this->Id_celula->setDbValue($conn->Insert_ID());
			$rsnew['Id_celula'] = $this->Id_celula->DbValue;
		}

		// Add detail records
		if ($AddRow) {
			$DetailTblVar = explode(",", $this->getCurrentDetailTable());
			if (in_array("membro", $DetailTblVar) && $GLOBALS["membro"]->DetailAdd) {
				$GLOBALS["membro"]->Celula->setSessionValue($this->Id_celula->CurrentValue); // Set master key
				if (!isset($GLOBALS["membro_grid"])) $GLOBALS["membro_grid"] = new cmembro_grid(); // Get detail page object
				$AddRow = $GLOBALS["membro_grid"]->GridInsert();
				if (!$AddRow)
					$GLOBALS["membro"]->Celula->setSessionValue(""); // Clear master key if insert failed
			}
		}

		// Commit/Rollback transaction
		if ($this->getCurrentDetailTable() <> "") {
			if ($AddRow) {
				$conn->CommitTrans(); // Commit transaction
			} else {
				$conn->RollbackTrans(); // Rollback transaction
			}
		}
		if ($AddRow) {

			// Call Row Inserted event
			$rs = ($rsold == NULL) ? NULL : $rsold->fields;
			$this->Row_Inserted($rs, $rsnew);
			$this->WriteAuditTrailOnAdd($rsnew);
		}
		return $AddRow;
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
				if ($GLOBALS["membro_grid"]->DetailAdd) {
					if ($this->CopyRecord)
						$GLOBALS["membro_grid"]->CurrentMode = "copy";
					else
						$GLOBALS["membro_grid"]->CurrentMode = "add";
					$GLOBALS["membro_grid"]->CurrentAction = "gridadd";

					// Save current master table to detail table
					$GLOBALS["membro_grid"]->setCurrentMasterTable($this->TableVar);
					$GLOBALS["membro_grid"]->setStartRecordNumber(1);
					$GLOBALS["membro_grid"]->Celula->FldIsDetailKey = TRUE;
					$GLOBALS["membro_grid"]->Celula->CurrentValue = $this->Id_celula->CurrentValue;
					$GLOBALS["membro_grid"]->Celula->setSessionValue($GLOBALS["membro_grid"]->Celula->CurrentValue);
				}
			}
		}
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, "celulaslist.php", "", $this->TableVar, TRUE);
		$PageId = ($this->CurrentAction == "C") ? "Copy" : "Add";
		$Breadcrumb->Add("add", $PageId, $url);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'celulas';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (add page)
	function WriteAuditTrailOnAdd(&$rs) {
		if (!$this->AuditTrailOnAdd) return;
		$table = 'celulas';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['Id_celula'];

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
if (!isset($celulas_add)) $celulas_add = new ccelulas_add();

// Page init
$celulas_add->Page_Init();

// Page main
$celulas_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$celulas_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var celulas_add = new ew_Page("celulas_add");
celulas_add.PageID = "add"; // Page ID
var EW_PAGE_ID = celulas_add.PageID; // For backward compatibility

// Form object
var fcelulasadd = new ew_Form("fcelulasadd");

// Validate form
fcelulasadd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_NomeCelula");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $celulas->NomeCelula->FldCaption(), $celulas->NomeCelula->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Responsavel");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $celulas->Responsavel->FldCaption(), $celulas->Responsavel->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_DiasReunioes[]");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $celulas->DiasReunioes->FldCaption(), $celulas->DiasReunioes->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_HorarioReunioes");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $celulas->HorarioReunioes->FldCaption(), $celulas->HorarioReunioes->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_HorarioReunioes");
			if (elm && !ew_CheckTime(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($celulas->HorarioReunioes->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Anotacoes");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $celulas->Anotacoes->FldCaption(), $celulas->Anotacoes->ReqErrMsg)) ?>");

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
fcelulasadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fcelulasadd.ValidateRequired = true;
<?php } else { ?>
fcelulasadd.ValidateRequired = false; 
<?php } ?>

// Multi-Page properties
fcelulasadd.MultiPage = new ew_MultiPage("fcelulasadd",
	[["x_NomeCelula",1],["x_Responsavel",1],["x_DiasReunioes",1],["x_HorarioReunioes",1],["x_Endereco",1],["x_Anotacoes",2]]
);

// Dynamic selection lists
fcelulasadd.Lists["x_DiasReunioes[]"] = {"LinkField":"x_Dias","Ajax":null,"AutoFill":false,"DisplayFields":["x_Dias","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

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
<?php $celulas_add->ShowPageHeader(); ?>
<?php
$celulas_add->ShowMessage();
?>
<form name="fcelulasadd" id="fcelulasadd" class="form-horizontal ewForm ewAddForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($celulas_add->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $celulas_add->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="celulas">
<input type="hidden" name="a_add" id="a_add" value="A">
<div>
<div class="tabbable" id="celulas_add">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab_celulas1" data-toggle="tab"><?php echo $celulas->PageCaption(1) ?></a></li>
		<li><a href="#tab_celulas2" data-toggle="tab"><?php echo $celulas->PageCaption(2) ?></a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="tab_celulas1">
<div>
<?php if ($celulas->NomeCelula->Visible) { // NomeCelula ?>
	<div id="r_NomeCelula" class="form-group">
		<label id="elh_celulas_NomeCelula" for="x_NomeCelula" class="col-sm-2 control-label ewLabel"><?php echo $celulas->NomeCelula->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $celulas->NomeCelula->CellAttributes() ?>>
<span id="el_celulas_NomeCelula">
<input type="text" data-field="x_NomeCelula" name="x_NomeCelula" id="x_NomeCelula" size="45" maxlength="60" value="<?php echo $celulas->NomeCelula->EditValue ?>"<?php echo $celulas->NomeCelula->EditAttributes() ?>>
</span>
<?php echo $celulas->NomeCelula->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($celulas->Responsavel->Visible) { // Responsavel ?>
	<div id="r_Responsavel" class="form-group">
		<label id="elh_celulas_Responsavel" for="x_Responsavel" class="col-sm-2 control-label ewLabel"><?php echo $celulas->Responsavel->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $celulas->Responsavel->CellAttributes() ?>>
<span id="el_celulas_Responsavel">
<input type="text" data-field="x_Responsavel" name="x_Responsavel" id="x_Responsavel" size="45" maxlength="60" value="<?php echo $celulas->Responsavel->EditValue ?>"<?php echo $celulas->Responsavel->EditAttributes() ?>>
</span>
<?php echo $celulas->Responsavel->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($celulas->DiasReunioes->Visible) { // DiasReunioes ?>
	<div id="r_DiasReunioes" class="form-group">
		<label id="elh_celulas_DiasReunioes" class="col-sm-2 control-label ewLabel"><?php echo $celulas->DiasReunioes->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $celulas->DiasReunioes->CellAttributes() ?>>
<span id="el_celulas_DiasReunioes">
<div id="tp_x_DiasReunioes" class="<?php echo EW_ITEM_TEMPLATE_CLASSNAME; ?>"><input type="checkbox" name="x_DiasReunioes[]" id="x_DiasReunioes[]" value="{value}"<?php echo $celulas->DiasReunioes->EditAttributes() ?>></div>
<div id="dsl_x_DiasReunioes" data-repeatcolumn="4" class="ewItemList">
<?php
$arwrk = $celulas->DiasReunioes->EditValue;
if (is_array($arwrk)) {
	$armultiwrk= explode(",", strval($celulas->DiasReunioes->CurrentValue));
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
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 4, 1) ?>
<label class="checkbox-inline"><input type="checkbox" data-field="x_DiasReunioes" name="x_DiasReunioes[]" id="x_DiasReunioes_<?php echo $rowcntwrk ?>[]" value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?><?php echo $celulas->DiasReunioes->EditAttributes() ?>><?php echo $arwrk[$rowcntwrk][1] ?></label>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 4, 2) ?>
<?php
	}
}
?>
</div>
<script type="text/javascript">
fcelulasadd.Lists["x_DiasReunioes[]"].Options = <?php echo (is_array($celulas->DiasReunioes->EditValue)) ? ew_ArrayToJson($celulas->DiasReunioes->EditValue, 0) : "[]" ?>;
</script>
</span>
<?php echo $celulas->DiasReunioes->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($celulas->HorarioReunioes->Visible) { // HorarioReunioes ?>
	<div id="r_HorarioReunioes" class="form-group">
		<label id="elh_celulas_HorarioReunioes" for="x_HorarioReunioes" class="col-sm-2 control-label ewLabel"><?php echo $celulas->HorarioReunioes->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $celulas->HorarioReunioes->CellAttributes() ?>>
<span id="el_celulas_HorarioReunioes">
<input type="text" data-field="x_HorarioReunioes" name="x_HorarioReunioes" id="x_HorarioReunioes" size="10" maxlength="8" value="<?php echo $celulas->HorarioReunioes->EditValue ?>"<?php echo $celulas->HorarioReunioes->EditAttributes() ?>>
<?php if (!$celulas->HorarioReunioes->ReadOnly && !$celulas->HorarioReunioes->Disabled && @$celulas->HorarioReunioes->EditAttrs["readonly"] == "" && @$celulas->HorarioReunioes->EditAttrs["disabled"] == "") { ?>
<script type="text/javascript">ew_CreateTimePicker("fcelulasadd", "x_HorarioReunioes", {"timeFormat":"H:i:s"});</script><?php } ?>
</span>
<?php echo $celulas->HorarioReunioes->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($celulas->Endereco->Visible) { // Endereco ?>
	<div id="r_Endereco" class="form-group">
		<label id="elh_celulas_Endereco" for="x_Endereco" class="col-sm-2 control-label ewLabel"><?php echo $celulas->Endereco->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $celulas->Endereco->CellAttributes() ?>>
<span id="el_celulas_Endereco">
<textarea data-field="x_Endereco" name="x_Endereco" id="x_Endereco" cols="50" rows="4"<?php echo $celulas->Endereco->EditAttributes() ?>><?php echo $celulas->Endereco->EditValue ?></textarea>
</span>
<?php echo $celulas->Endereco->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
		</div>
		<div class="tab-pane" id="tab_celulas2">
<div>
<?php if ($celulas->Anotacoes->Visible) { // Anotacoes ?>
	<div id="r_Anotacoes" class="form-group">
		<label id="elh_celulas_Anotacoes" for="x_Anotacoes" class="col-sm-2 control-label ewLabel"><?php echo $celulas->Anotacoes->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $celulas->Anotacoes->CellAttributes() ?>>
<span id="el_celulas_Anotacoes">
<textarea data-field="x_Anotacoes" name="x_Anotacoes" id="x_Anotacoes" cols="50" rows="4"<?php echo $celulas->Anotacoes->EditAttributes() ?>><?php echo $celulas->Anotacoes->EditValue ?></textarea>
</span>
<?php echo $celulas->Anotacoes->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
		</div>
	</div>
</div>
</div>
<?php
	if (in_array("membro", explode(",", $celulas->getCurrentDetailTable())) && $membro->DetailAdd) {
?>
<?php if ($celulas->getCurrentDetailTable() <> "") { ?>
<h4 class="ewDetailCaption"><?php echo $Language->TablePhrase("membro", "TblCaption") ?></h4>
<?php } ?>
<?php include_once "membrogrid.php" ?>
<?php } ?>
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
<button class="btn btn-primary ewButton btn-success" name="btnAction" id="btnAction" type="submit"><i class="fa fa-floppy-o"></i>&nbsp;<?php echo $Language->Phrase("AddBtn") ?></button>
	</div>
</div>
</form>
<script type="text/javascript">
fcelulasadd.Init();
</script>
<?php
$celulas_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$celulas_add->Page_Terminate();
?>
