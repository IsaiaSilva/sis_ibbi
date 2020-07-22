<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg11.php" ?>
<?php include_once "ewmysql11.php" ?>
<?php include_once "phpfn11.php" ?>
<?php include_once "agendainfo.php" ?>
<?php include_once "usuariosinfo.php" ?>
<?php include_once "userfn11.php" ?>
<?php

//
// Page class
//

$agenda_add = NULL; // Initialize page object first

class cagenda_add extends cagenda {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{2B7992FC-5911-46A7-9310-01F4D4225C49}";

	// Table name
	var $TableName = 'agenda';

	// Page object name
	var $PageObjName = 'agenda_add';

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

		// Table object (agenda)
		if (!isset($GLOBALS["agenda"]) || get_class($GLOBALS["agenda"]) == "cagenda") {
			$GLOBALS["agenda"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["agenda"];
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
			define("EW_TABLE_NAME", 'agenda', TRUE);

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
			$this->Page_Terminate(ew_GetUrl("agendalist.php"));
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
		global $EW_EXPORT, $agenda;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($agenda);
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
			if (@$_GET["id"] != "") {
				$this->id->setQueryStringValue($_GET["id"]);
				$this->setKey("id", $this->id->CurrentValue); // Set up key
			} else {
				$this->setKey("id", ""); // Clear key
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
					$this->Page_Terminate("agendalist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "agendaview.php")
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
		$this->Prioridade->CurrentValue = NULL;
		$this->Prioridade->OldValue = $this->Prioridade->CurrentValue;
		$this->Data->CurrentValue = NULL;
		$this->Data->OldValue = $this->Data->CurrentValue;
		$this->Horario->CurrentValue = NULL;
		$this->Horario->OldValue = $this->Horario->CurrentValue;
		$this->Assunto->CurrentValue = NULL;
		$this->Assunto->OldValue = $this->Assunto->CurrentValue;
		$this->Tarefa->CurrentValue = NULL;
		$this->Tarefa->OldValue = $this->Tarefa->CurrentValue;
		$this->Resolvido->CurrentValue = 4;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->Prioridade->FldIsDetailKey) {
			$this->Prioridade->setFormValue($objForm->GetValue("x_Prioridade"));
		}
		if (!$this->Data->FldIsDetailKey) {
			$this->Data->setFormValue($objForm->GetValue("x_Data"));
			$this->Data->CurrentValue = ew_UnFormatDateTime($this->Data->CurrentValue, 7);
		}
		if (!$this->Horario->FldIsDetailKey) {
			$this->Horario->setFormValue($objForm->GetValue("x_Horario"));
		}
		if (!$this->Assunto->FldIsDetailKey) {
			$this->Assunto->setFormValue($objForm->GetValue("x_Assunto"));
		}
		if (!$this->Tarefa->FldIsDetailKey) {
			$this->Tarefa->setFormValue($objForm->GetValue("x_Tarefa"));
		}
		if (!$this->Resolvido->FldIsDetailKey) {
			$this->Resolvido->setFormValue($objForm->GetValue("x_Resolvido"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->Prioridade->CurrentValue = $this->Prioridade->FormValue;
		$this->Data->CurrentValue = $this->Data->FormValue;
		$this->Data->CurrentValue = ew_UnFormatDateTime($this->Data->CurrentValue, 7);
		$this->Horario->CurrentValue = $this->Horario->FormValue;
		$this->Assunto->CurrentValue = $this->Assunto->FormValue;
		$this->Tarefa->CurrentValue = $this->Tarefa->FormValue;
		$this->Resolvido->CurrentValue = $this->Resolvido->FormValue;
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
		$this->id->setDbValue($rs->fields('id'));
		$this->Prioridade->setDbValue($rs->fields('Prioridade'));
		$this->Data->setDbValue($rs->fields('Data'));
		$this->Horario->setDbValue($rs->fields('Horario'));
		$this->Assunto->setDbValue($rs->fields('Assunto'));
		$this->Tarefa->setDbValue($rs->fields('Tarefa'));
		$this->Resolvido->setDbValue($rs->fields('Resolvido'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->id->DbValue = $row['id'];
		$this->Prioridade->DbValue = $row['Prioridade'];
		$this->Data->DbValue = $row['Data'];
		$this->Horario->DbValue = $row['Horario'];
		$this->Assunto->DbValue = $row['Assunto'];
		$this->Tarefa->DbValue = $row['Tarefa'];
		$this->Resolvido->DbValue = $row['Resolvido'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("id")) <> "")
			$this->id->CurrentValue = $this->getKey("id"); // id
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
		// id
		// Prioridade
		// Data
		// Horario
		// Assunto
		// Tarefa
		// Resolvido

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// Prioridade
			if (strval($this->Prioridade->CurrentValue) <> "") {
				$sFilterWrk = "`Id_prior`" . ew_SearchString("=", $this->Prioridade->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `Id_prior`, `Prioridade` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `prioridade`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Prioridade, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->Prioridade->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->Prioridade->ViewValue = $this->Prioridade->CurrentValue;
				}
			} else {
				$this->Prioridade->ViewValue = NULL;
			}
			$this->Prioridade->ViewCustomAttributes = "";

			// Data
			$this->Data->ViewValue = $this->Data->CurrentValue;
			$this->Data->ViewValue = ew_FormatDateTime($this->Data->ViewValue, 7);
			$this->Data->ViewCustomAttributes = "";

			// Horario
			$this->Horario->ViewValue = $this->Horario->CurrentValue;
			$this->Horario->ViewCustomAttributes = "";

			// Assunto
			$this->Assunto->ViewValue = $this->Assunto->CurrentValue;
			$this->Assunto->ViewCustomAttributes = "";

			// Tarefa
			$this->Tarefa->ViewValue = $this->Tarefa->CurrentValue;
			$this->Tarefa->ViewCustomAttributes = "";

			// Resolvido
			if (strval($this->Resolvido->CurrentValue) <> "") {
				$sFilterWrk = "`Id`" . ew_SearchString("=", $this->Resolvido->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `Id`, `solucao` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `agsolucao`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Resolvido, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->Resolvido->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->Resolvido->ViewValue = $this->Resolvido->CurrentValue;
				}
			} else {
				$this->Resolvido->ViewValue = NULL;
			}
			$this->Resolvido->ViewCustomAttributes = "";

			// Prioridade
			$this->Prioridade->LinkCustomAttributes = "";
			$this->Prioridade->HrefValue = "";
			$this->Prioridade->TooltipValue = "";

			// Data
			$this->Data->LinkCustomAttributes = "";
			$this->Data->HrefValue = "";
			$this->Data->TooltipValue = "";

			// Horario
			$this->Horario->LinkCustomAttributes = "";
			$this->Horario->HrefValue = "";
			$this->Horario->TooltipValue = "";

			// Assunto
			$this->Assunto->LinkCustomAttributes = "";
			$this->Assunto->HrefValue = "";
			$this->Assunto->TooltipValue = "";

			// Tarefa
			$this->Tarefa->LinkCustomAttributes = "";
			$this->Tarefa->HrefValue = "";
			$this->Tarefa->TooltipValue = "";

			// Resolvido
			$this->Resolvido->LinkCustomAttributes = "";
			$this->Resolvido->HrefValue = "";
			$this->Resolvido->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// Prioridade
			$this->Prioridade->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `Id_prior`, `Prioridade` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `prioridade`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Prioridade, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Prioridade->EditValue = $arwrk;

			// Data
			$this->Data->EditAttrs["class"] = "form-control";
			$this->Data->EditCustomAttributes = "";
			$this->Data->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->Data->CurrentValue, 7));

			// Horario
			$this->Horario->EditAttrs["class"] = "form-control";
			$this->Horario->EditCustomAttributes = "";
			$this->Horario->EditValue = ew_HtmlEncode($this->Horario->CurrentValue);

			// Assunto
			$this->Assunto->EditAttrs["class"] = "form-control";
			$this->Assunto->EditCustomAttributes = "";
			$this->Assunto->EditValue = ew_HtmlEncode($this->Assunto->CurrentValue);

			// Tarefa
			$this->Tarefa->EditAttrs["class"] = "form-control";
			$this->Tarefa->EditCustomAttributes = "";
			$this->Tarefa->EditValue = ew_HtmlEncode($this->Tarefa->CurrentValue);

			// Resolvido
			$this->Resolvido->EditAttrs["class"] = "form-control";
			$this->Resolvido->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `Id`, `solucao` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `agsolucao`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Resolvido, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->Resolvido->EditValue = $arwrk;

			// Edit refer script
			// Prioridade

			$this->Prioridade->HrefValue = "";

			// Data
			$this->Data->HrefValue = "";

			// Horario
			$this->Horario->HrefValue = "";

			// Assunto
			$this->Assunto->HrefValue = "";

			// Tarefa
			$this->Tarefa->HrefValue = "";

			// Resolvido
			$this->Resolvido->HrefValue = "";
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
		if ($this->Prioridade->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Prioridade->FldCaption(), $this->Prioridade->ReqErrMsg));
		}
		if (!$this->Data->FldIsDetailKey && !is_null($this->Data->FormValue) && $this->Data->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Data->FldCaption(), $this->Data->ReqErrMsg));
		}
		if (!ew_CheckEuroDate($this->Data->FormValue)) {
			ew_AddMessage($gsFormError, $this->Data->FldErrMsg());
		}
		if (!$this->Horario->FldIsDetailKey && !is_null($this->Horario->FormValue) && $this->Horario->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Horario->FldCaption(), $this->Horario->ReqErrMsg));
		}
		if (!ew_CheckTime($this->Horario->FormValue)) {
			ew_AddMessage($gsFormError, $this->Horario->FldErrMsg());
		}
		if (!$this->Assunto->FldIsDetailKey && !is_null($this->Assunto->FormValue) && $this->Assunto->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Assunto->FldCaption(), $this->Assunto->ReqErrMsg));
		}
		if (!$this->Tarefa->FldIsDetailKey && !is_null($this->Tarefa->FormValue) && $this->Tarefa->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Tarefa->FldCaption(), $this->Tarefa->ReqErrMsg));
		}
		if (!$this->Resolvido->FldIsDetailKey && !is_null($this->Resolvido->FormValue) && $this->Resolvido->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Resolvido->FldCaption(), $this->Resolvido->ReqErrMsg));
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

		// Prioridade
		$this->Prioridade->SetDbValueDef($rsnew, $this->Prioridade->CurrentValue, NULL, FALSE);

		// Data
		$this->Data->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->Data->CurrentValue, 7), NULL, FALSE);

		// Horario
		$this->Horario->SetDbValueDef($rsnew, $this->Horario->CurrentValue, NULL, FALSE);

		// Assunto
		$this->Assunto->SetDbValueDef($rsnew, $this->Assunto->CurrentValue, NULL, FALSE);

		// Tarefa
		$this->Tarefa->SetDbValueDef($rsnew, $this->Tarefa->CurrentValue, NULL, FALSE);

		// Resolvido
		$this->Resolvido->SetDbValueDef($rsnew, $this->Resolvido->CurrentValue, NULL, strval($this->Resolvido->CurrentValue) == "");

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
			$this->id->setDbValue($conn->Insert_ID());
			$rsnew['id'] = $this->id->DbValue;
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
		$Breadcrumb->Add("list", $this->TableVar, "agendalist.php", "", $this->TableVar, TRUE);
		$PageId = ($this->CurrentAction == "C") ? "Copy" : "Add";
		$Breadcrumb->Add("add", $PageId, $url);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'agenda';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (add page)
	function WriteAuditTrailOnAdd(&$rs) {
		if (!$this->AuditTrailOnAdd) return;
		$table = 'agenda';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['id'];

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
if (!isset($agenda_add)) $agenda_add = new cagenda_add();

// Page init
$agenda_add->Page_Init();

// Page main
$agenda_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$agenda_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var agenda_add = new ew_Page("agenda_add");
agenda_add.PageID = "add"; // Page ID
var EW_PAGE_ID = agenda_add.PageID; // For backward compatibility

// Form object
var fagendaadd = new ew_Form("fagendaadd");

// Validate form
fagendaadd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_Prioridade");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $agenda->Prioridade->FldCaption(), $agenda->Prioridade->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Data");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $agenda->Data->FldCaption(), $agenda->Data->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Data");
			if (elm && !ew_CheckEuroDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($agenda->Data->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Horario");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $agenda->Horario->FldCaption(), $agenda->Horario->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Horario");
			if (elm && !ew_CheckTime(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($agenda->Horario->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Assunto");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $agenda->Assunto->FldCaption(), $agenda->Assunto->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Tarefa");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $agenda->Tarefa->FldCaption(), $agenda->Tarefa->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Resolvido");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $agenda->Resolvido->FldCaption(), $agenda->Resolvido->ReqErrMsg)) ?>");

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
fagendaadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fagendaadd.ValidateRequired = true;
<?php } else { ?>
fagendaadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fagendaadd.Lists["x_Prioridade"] = {"LinkField":"x_Id_prior","Ajax":null,"AutoFill":false,"DisplayFields":["x_Prioridade","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fagendaadd.Lists["x_Resolvido"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_solucao","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

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
<?php $agenda_add->ShowPageHeader(); ?>
<?php
$agenda_add->ShowMessage();
?>
<form name="fagendaadd" id="fagendaadd" class="form-horizontal ewForm ewAddForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($agenda_add->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $agenda_add->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="agenda">
<input type="hidden" name="a_add" id="a_add" value="A">
<div>
<?php if ($agenda->Prioridade->Visible) { // Prioridade ?>
	<div id="r_Prioridade" class="form-group">
		<label id="elh_agenda_Prioridade" class="col-sm-2 control-label ewLabel"><?php echo $agenda->Prioridade->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $agenda->Prioridade->CellAttributes() ?>>
<span id="el_agenda_Prioridade">
<div id="tp_x_Prioridade" class="<?php echo EW_ITEM_TEMPLATE_CLASSNAME ?>"><input type="radio" name="x_Prioridade" id="x_Prioridade" value="{value}"<?php echo $agenda->Prioridade->EditAttributes() ?>></div>
<div id="dsl_x_Prioridade" data-repeatcolumn="5" class="ewItemList">
<?php
$arwrk = $agenda->Prioridade->EditValue;
if (is_array($arwrk)) {
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($agenda->Prioridade->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " checked=\"checked\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;

		// Note: No spacing within the LABEL tag
?>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 1) ?>
<label class="radio-inline"><input type="radio" data-field="x_Prioridade" name="x_Prioridade" id="x_Prioridade_<?php echo $rowcntwrk ?>" value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?><?php echo $agenda->Prioridade->EditAttributes() ?>><?php echo $arwrk[$rowcntwrk][1] ?></label>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 2) ?>
<?php
	}
}
?>
</div>
<script type="text/javascript">
fagendaadd.Lists["x_Prioridade"].Options = <?php echo (is_array($agenda->Prioridade->EditValue)) ? ew_ArrayToJson($agenda->Prioridade->EditValue, 0) : "[]" ?>;
</script>
</span>
<?php echo $agenda->Prioridade->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($agenda->Data->Visible) { // Data ?>
	<div id="r_Data" class="form-group">
		<label id="elh_agenda_Data" for="x_Data" class="col-sm-2 control-label ewLabel"><?php echo $agenda->Data->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $agenda->Data->CellAttributes() ?>>
<span id="el_agenda_Data">
<input type="text" data-field="x_Data" name="x_Data" id="x_Data" size="12" value="<?php echo $agenda->Data->EditValue ?>"<?php echo $agenda->Data->EditAttributes() ?>>
<?php if (!$agenda->Data->ReadOnly && !$agenda->Data->Disabled && @$agenda->Data->EditAttrs["readonly"] == "" && @$agenda->Data->EditAttrs["disabled"] == "") { ?>
<script type="text/javascript">
ew_CreateCalendar("fagendaadd", "x_Data", "%d/%m/%Y");
</script>
<?php } ?>
</span>
<?php echo $agenda->Data->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($agenda->Horario->Visible) { // Horario ?>
	<div id="r_Horario" class="form-group">
		<label id="elh_agenda_Horario" for="x_Horario" class="col-sm-2 control-label ewLabel"><?php echo $agenda->Horario->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $agenda->Horario->CellAttributes() ?>>
<span id="el_agenda_Horario">
<input type="text" data-field="x_Horario" name="x_Horario" id="x_Horario" size="12" value="<?php echo $agenda->Horario->EditValue ?>"<?php echo $agenda->Horario->EditAttributes() ?>>
<?php if (!$agenda->Horario->ReadOnly && !$agenda->Horario->Disabled && @$agenda->Horario->EditAttrs["readonly"] == "" && @$agenda->Horario->EditAttrs["disabled"] == "") { ?>
<script type="text/javascript">ew_CreateTimePicker("fagendaadd", "x_Horario", {"timeFormat":"H:i:s"});</script><?php } ?>
</span>
<?php echo $agenda->Horario->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($agenda->Assunto->Visible) { // Assunto ?>
	<div id="r_Assunto" class="form-group">
		<label id="elh_agenda_Assunto" for="x_Assunto" class="col-sm-2 control-label ewLabel"><?php echo $agenda->Assunto->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $agenda->Assunto->CellAttributes() ?>>
<span id="el_agenda_Assunto">
<input type="text" data-field="x_Assunto" name="x_Assunto" id="x_Assunto" size="60" maxlength="50" value="<?php echo $agenda->Assunto->EditValue ?>"<?php echo $agenda->Assunto->EditAttributes() ?>>
</span>
<?php echo $agenda->Assunto->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($agenda->Tarefa->Visible) { // Tarefa ?>
	<div id="r_Tarefa" class="form-group">
		<label id="elh_agenda_Tarefa" class="col-sm-2 control-label ewLabel"><?php echo $agenda->Tarefa->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $agenda->Tarefa->CellAttributes() ?>>
<span id="el_agenda_Tarefa">
<textarea data-field="x_Tarefa" class="editor" name="x_Tarefa" id="x_Tarefa" cols="70" rows="4"<?php echo $agenda->Tarefa->EditAttributes() ?>><?php echo $agenda->Tarefa->EditValue ?></textarea>
<script type="text/javascript">
ew_CreateEditor("fagendaadd", "x_Tarefa", 70, 4, <?php echo ($agenda->Tarefa->ReadOnly || FALSE) ? "true" : "false" ?>);
</script>
</span>
<?php echo $agenda->Tarefa->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($agenda->Resolvido->Visible) { // Resolvido ?>
	<div id="r_Resolvido" class="form-group">
		<label id="elh_agenda_Resolvido" for="x_Resolvido" class="col-sm-2 control-label ewLabel"><?php echo $agenda->Resolvido->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $agenda->Resolvido->CellAttributes() ?>>
<span id="el_agenda_Resolvido">
<select data-field="x_Resolvido" id="x_Resolvido" name="x_Resolvido"<?php echo $agenda->Resolvido->EditAttributes() ?>>
<?php
if (is_array($agenda->Resolvido->EditValue)) {
	$arwrk = $agenda->Resolvido->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($agenda->Resolvido->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
fagendaadd.Lists["x_Resolvido"].Options = <?php echo (is_array($agenda->Resolvido->EditValue)) ? ew_ArrayToJson($agenda->Resolvido->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $agenda->Resolvido->CustomMsg ?></div></div>
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
fagendaadd.Init();
</script>
<?php
$agenda_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$agenda_add->Page_Terminate();
?>
