<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg11.php" ?>
<?php include_once "ewmysql11.php" ?>
<?php include_once "phpfn11.php" ?>
<?php include_once "agenda_mortainfo.php" ?>
<?php include_once "usuariosinfo.php" ?>
<?php include_once "userfn11.php" ?>
<?php

//
// Page class
//

$Agenda_Morta_edit = NULL; // Initialize page object first

class cAgenda_Morta_edit extends cAgenda_Morta {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{2B7992FC-5911-46A7-9310-01F4D4225C49}";

	// Table name
	var $TableName = 'Agenda_Morta';

	// Page object name
	var $PageObjName = 'Agenda_Morta_edit';

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

		// Table object (Agenda_Morta)
		if (!isset($GLOBALS["Agenda_Morta"]) || get_class($GLOBALS["Agenda_Morta"]) == "cAgenda_Morta") {
			$GLOBALS["Agenda_Morta"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["Agenda_Morta"];
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
			define("EW_TABLE_NAME", 'Agenda_Morta', TRUE);

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
			$this->Page_Terminate(ew_GetUrl("agenda_mortalist.php"));
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->id->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
		global $EW_EXPORT, $Agenda_Morta;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($Agenda_Morta);
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
		if (@$_GET["id"] <> "") {
			$this->id->setQueryStringValue($_GET["id"]);
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
		if ($this->id->CurrentValue == "")
			$this->Page_Terminate("agenda_mortalist.php"); // Invalid key, return to list

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
					$this->Page_Terminate("agenda_mortalist.php"); // No matching record, return to list
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
		if (!$this->id->FldIsDetailKey)
			$this->id->setFormValue($objForm->GetValue("x_id"));
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
		$this->LoadRow();
		$this->id->CurrentValue = $this->id->FormValue;
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

			// id
			$this->id->ViewValue = $this->id->CurrentValue;
			$this->id->ViewCustomAttributes = "";

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

			// id
			$this->id->LinkCustomAttributes = "";
			$this->id->HrefValue = "";
			$this->id->TooltipValue = "";

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
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// id
			$this->id->EditAttrs["class"] = "form-control";
			$this->id->EditCustomAttributes = "";

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
			// id

			$this->id->HrefValue = "";

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

			// Prioridade
			$this->Prioridade->SetDbValueDef($rsnew, $this->Prioridade->CurrentValue, NULL, $this->Prioridade->ReadOnly);

			// Data
			$this->Data->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->Data->CurrentValue, 7), NULL, $this->Data->ReadOnly);

			// Horario
			$this->Horario->SetDbValueDef($rsnew, $this->Horario->CurrentValue, NULL, $this->Horario->ReadOnly);

			// Assunto
			$this->Assunto->SetDbValueDef($rsnew, $this->Assunto->CurrentValue, NULL, $this->Assunto->ReadOnly);

			// Tarefa
			$this->Tarefa->SetDbValueDef($rsnew, $this->Tarefa->CurrentValue, NULL, $this->Tarefa->ReadOnly);

			// Resolvido
			$this->Resolvido->SetDbValueDef($rsnew, $this->Resolvido->CurrentValue, NULL, $this->Resolvido->ReadOnly);

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
		$rs->Close();
		return $EditRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, "agenda_mortalist.php", "", $this->TableVar, TRUE);
		$PageId = "edit";
		$Breadcrumb->Add("edit", $PageId, $url);
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
if (!isset($Agenda_Morta_edit)) $Agenda_Morta_edit = new cAgenda_Morta_edit();

// Page init
$Agenda_Morta_edit->Page_Init();

// Page main
$Agenda_Morta_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$Agenda_Morta_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var Agenda_Morta_edit = new ew_Page("Agenda_Morta_edit");
Agenda_Morta_edit.PageID = "edit"; // Page ID
var EW_PAGE_ID = Agenda_Morta_edit.PageID; // For backward compatibility

// Form object
var fAgenda_Mortaedit = new ew_Form("fAgenda_Mortaedit");

// Validate form
fAgenda_Mortaedit.Validate = function() {
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
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $Agenda_Morta->Prioridade->FldCaption(), $Agenda_Morta->Prioridade->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Data");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $Agenda_Morta->Data->FldCaption(), $Agenda_Morta->Data->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Data");
			if (elm && !ew_CheckEuroDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($Agenda_Morta->Data->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Horario");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $Agenda_Morta->Horario->FldCaption(), $Agenda_Morta->Horario->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Horario");
			if (elm && !ew_CheckTime(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($Agenda_Morta->Horario->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Assunto");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $Agenda_Morta->Assunto->FldCaption(), $Agenda_Morta->Assunto->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Tarefa");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $Agenda_Morta->Tarefa->FldCaption(), $Agenda_Morta->Tarefa->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Resolvido");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $Agenda_Morta->Resolvido->FldCaption(), $Agenda_Morta->Resolvido->ReqErrMsg)) ?>");

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
fAgenda_Mortaedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fAgenda_Mortaedit.ValidateRequired = true;
<?php } else { ?>
fAgenda_Mortaedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fAgenda_Mortaedit.Lists["x_Prioridade"] = {"LinkField":"x_Id_prior","Ajax":null,"AutoFill":false,"DisplayFields":["x_Prioridade","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fAgenda_Mortaedit.Lists["x_Resolvido"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_solucao","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

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
<?php $Agenda_Morta_edit->ShowPageHeader(); ?>
<?php
$Agenda_Morta_edit->ShowMessage();
?>
<form name="fAgenda_Mortaedit" id="fAgenda_Mortaedit" class="form-horizontal ewForm ewEditForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($Agenda_Morta_edit->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $Agenda_Morta_edit->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="Agenda_Morta">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<div>
<?php if ($Agenda_Morta->Prioridade->Visible) { // Prioridade ?>
	<div id="r_Prioridade" class="form-group">
		<label id="elh_Agenda_Morta_Prioridade" class="col-sm-2 control-label ewLabel"><?php echo $Agenda_Morta->Prioridade->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $Agenda_Morta->Prioridade->CellAttributes() ?>>
<span id="el_Agenda_Morta_Prioridade">
<div id="tp_x_Prioridade" class="<?php echo EW_ITEM_TEMPLATE_CLASSNAME ?>"><input type="radio" name="x_Prioridade" id="x_Prioridade" value="{value}"<?php echo $Agenda_Morta->Prioridade->EditAttributes() ?>></div>
<div id="dsl_x_Prioridade" data-repeatcolumn="5" class="ewItemList">
<?php
$arwrk = $Agenda_Morta->Prioridade->EditValue;
if (is_array($arwrk)) {
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($Agenda_Morta->Prioridade->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " checked=\"checked\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;

		// Note: No spacing within the LABEL tag
?>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 1) ?>
<label class="radio-inline"><input type="radio" data-field="x_Prioridade" name="x_Prioridade" id="x_Prioridade_<?php echo $rowcntwrk ?>" value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?><?php echo $Agenda_Morta->Prioridade->EditAttributes() ?>><?php echo $arwrk[$rowcntwrk][1] ?></label>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 2) ?>
<?php
	}
}
?>
</div>
<script type="text/javascript">
fAgenda_Mortaedit.Lists["x_Prioridade"].Options = <?php echo (is_array($Agenda_Morta->Prioridade->EditValue)) ? ew_ArrayToJson($Agenda_Morta->Prioridade->EditValue, 0) : "[]" ?>;
</script>
</span>
<?php echo $Agenda_Morta->Prioridade->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($Agenda_Morta->Data->Visible) { // Data ?>
	<div id="r_Data" class="form-group">
		<label id="elh_Agenda_Morta_Data" for="x_Data" class="col-sm-2 control-label ewLabel"><?php echo $Agenda_Morta->Data->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $Agenda_Morta->Data->CellAttributes() ?>>
<span id="el_Agenda_Morta_Data">
<input type="text" data-field="x_Data" name="x_Data" id="x_Data" size="12" value="<?php echo $Agenda_Morta->Data->EditValue ?>"<?php echo $Agenda_Morta->Data->EditAttributes() ?>>
<?php if (!$Agenda_Morta->Data->ReadOnly && !$Agenda_Morta->Data->Disabled && @$Agenda_Morta->Data->EditAttrs["readonly"] == "" && @$Agenda_Morta->Data->EditAttrs["disabled"] == "") { ?>
<script type="text/javascript">
ew_CreateCalendar("fAgenda_Mortaedit", "x_Data", "%d/%m/%Y");
</script>
<?php } ?>
</span>
<?php echo $Agenda_Morta->Data->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($Agenda_Morta->Horario->Visible) { // Horario ?>
	<div id="r_Horario" class="form-group">
		<label id="elh_Agenda_Morta_Horario" for="x_Horario" class="col-sm-2 control-label ewLabel"><?php echo $Agenda_Morta->Horario->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $Agenda_Morta->Horario->CellAttributes() ?>>
<span id="el_Agenda_Morta_Horario">
<input type="text" data-field="x_Horario" name="x_Horario" id="x_Horario" size="12" value="<?php echo $Agenda_Morta->Horario->EditValue ?>"<?php echo $Agenda_Morta->Horario->EditAttributes() ?>>
<?php if (!$Agenda_Morta->Horario->ReadOnly && !$Agenda_Morta->Horario->Disabled && @$Agenda_Morta->Horario->EditAttrs["readonly"] == "" && @$Agenda_Morta->Horario->EditAttrs["disabled"] == "") { ?>
<script type="text/javascript">ew_CreateTimePicker("fAgenda_Mortaedit", "x_Horario", {"timeFormat":"H:i:s"});</script><?php } ?>
</span>
<?php echo $Agenda_Morta->Horario->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($Agenda_Morta->Assunto->Visible) { // Assunto ?>
	<div id="r_Assunto" class="form-group">
		<label id="elh_Agenda_Morta_Assunto" for="x_Assunto" class="col-sm-2 control-label ewLabel"><?php echo $Agenda_Morta->Assunto->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $Agenda_Morta->Assunto->CellAttributes() ?>>
<span id="el_Agenda_Morta_Assunto">
<input type="text" data-field="x_Assunto" name="x_Assunto" id="x_Assunto" size="55" maxlength="50" value="<?php echo $Agenda_Morta->Assunto->EditValue ?>"<?php echo $Agenda_Morta->Assunto->EditAttributes() ?>>
</span>
<?php echo $Agenda_Morta->Assunto->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($Agenda_Morta->Tarefa->Visible) { // Tarefa ?>
	<div id="r_Tarefa" class="form-group">
		<label id="elh_Agenda_Morta_Tarefa" class="col-sm-2 control-label ewLabel"><?php echo $Agenda_Morta->Tarefa->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $Agenda_Morta->Tarefa->CellAttributes() ?>>
<span id="el_Agenda_Morta_Tarefa">
<textarea data-field="x_Tarefa" class="editor" name="x_Tarefa" id="x_Tarefa" cols="35" rows="4"<?php echo $Agenda_Morta->Tarefa->EditAttributes() ?>><?php echo $Agenda_Morta->Tarefa->EditValue ?></textarea>
<script type="text/javascript">
ew_CreateEditor("fAgenda_Mortaedit", "x_Tarefa", 35, 4, <?php echo ($Agenda_Morta->Tarefa->ReadOnly || FALSE) ? "true" : "false" ?>);
</script>
</span>
<?php echo $Agenda_Morta->Tarefa->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($Agenda_Morta->Resolvido->Visible) { // Resolvido ?>
	<div id="r_Resolvido" class="form-group">
		<label id="elh_Agenda_Morta_Resolvido" for="x_Resolvido" class="col-sm-2 control-label ewLabel"><?php echo $Agenda_Morta->Resolvido->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $Agenda_Morta->Resolvido->CellAttributes() ?>>
<span id="el_Agenda_Morta_Resolvido">
<select data-field="x_Resolvido" id="x_Resolvido" name="x_Resolvido"<?php echo $Agenda_Morta->Resolvido->EditAttributes() ?>>
<?php
if (is_array($Agenda_Morta->Resolvido->EditValue)) {
	$arwrk = $Agenda_Morta->Resolvido->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($Agenda_Morta->Resolvido->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
fAgenda_Mortaedit.Lists["x_Resolvido"].Options = <?php echo (is_array($Agenda_Morta->Resolvido->EditValue)) ? ew_ArrayToJson($Agenda_Morta->Resolvido->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $Agenda_Morta->Resolvido->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
<span id="el_Agenda_Morta_id">
<input type="hidden" data-field="x_id" name="x_id" id="x_id" value="<?php echo ew_HtmlEncode($Agenda_Morta->id->CurrentValue) ?>">
</span>
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><i class="fa fa-floppy-o"></i>&nbsp;<?php echo $Language->Phrase("SaveBtn") ?></button>
	</div>
</div>
</form>
<script type="text/javascript">
fAgenda_Mortaedit.Init();
</script>
<?php
$Agenda_Morta_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$Agenda_Morta_edit->Page_Terminate();
?>
