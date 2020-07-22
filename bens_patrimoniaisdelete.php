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

$bens_patrimoniais_delete = NULL; // Initialize page object first

class cbens_patrimoniais_delete extends cbens_patrimoniais {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{2B7992FC-5911-46A7-9310-01F4D4225C49}";

	// Table name
	var $TableName = 'bens_patrimoniais';

	// Page object name
	var $PageObjName = 'bens_patrimoniais_delete';

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
			define("EW_PAGE_ID", 'delete', TRUE);

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
		if (!$Security->CanDelete()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate(ew_GetUrl("bens_patrimoniaislist.php"));
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
			$this->Page_Terminate("bens_patrimoniaislist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in bens_patrimoniais class, bens_patrimoniaisinfo.php

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

		$this->Id_Patri->CellCssStyle = "white-space: nowrap;";

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
				$sThisKey .= $row['Id_Patri'];
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
		$Breadcrumb->Add("list", $this->TableVar, "bens_patrimoniaislist.php", "", $this->TableVar, TRUE);
		$PageId = "delete";
		$Breadcrumb->Add("delete", $PageId, $url);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'bens_patrimoniais';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (delete page)
	function WriteAuditTrailOnDelete(&$rs) {
		if (!$this->AuditTrailOnDelete) return;
		$table = 'bens_patrimoniais';

		// Get key value
		$key = "";
		if ($key <> "")
			$key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['Id_Patri'];

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
if (!isset($bens_patrimoniais_delete)) $bens_patrimoniais_delete = new cbens_patrimoniais_delete();

// Page init
$bens_patrimoniais_delete->Page_Init();

// Page main
$bens_patrimoniais_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$bens_patrimoniais_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var bens_patrimoniais_delete = new ew_Page("bens_patrimoniais_delete");
bens_patrimoniais_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = bens_patrimoniais_delete.PageID; // For backward compatibility

// Form object
var fbens_patrimoniaisdelete = new ew_Form("fbens_patrimoniaisdelete");

// Form_CustomValidate event
fbens_patrimoniaisdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fbens_patrimoniaisdelete.ValidateRequired = true;
<?php } else { ?>
fbens_patrimoniaisdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fbens_patrimoniaisdelete.Lists["x_Localidade"] = {"LinkField":"x_Id_igreja","Ajax":null,"AutoFill":false,"DisplayFields":["x_Igreja","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fbens_patrimoniaisdelete.Lists["x_Estado_do_bem"] = {"LinkField":"x_Id_est_patri","Ajax":null,"AutoFill":false,"DisplayFields":["x_Estado_do_Bem","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($bens_patrimoniais_delete->Recordset = $bens_patrimoniais_delete->LoadRecordset())
	$bens_patrimoniais_deleteTotalRecs = $bens_patrimoniais_delete->Recordset->RecordCount(); // Get record count
if ($bens_patrimoniais_deleteTotalRecs <= 0) { // No record found, exit
	if ($bens_patrimoniais_delete->Recordset)
		$bens_patrimoniais_delete->Recordset->Close();
	$bens_patrimoniais_delete->Page_Terminate("bens_patrimoniaislist.php"); // Return to list
}
?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php $bens_patrimoniais_delete->ShowPageHeader(); ?>
<?php
$bens_patrimoniais_delete->ShowMessage();
?>
<form name="fbens_patrimoniaisdelete" id="fbens_patrimoniaisdelete" class="form-inline ewForm ewDeleteForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($bens_patrimoniais_delete->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $bens_patrimoniais_delete->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="bens_patrimoniais">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($bens_patrimoniais_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<div class="ewGrid">
<div class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<table class="table ewTable">
<?php echo $bens_patrimoniais->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($bens_patrimoniais->Localidade->Visible) { // Localidade ?>
		<th><span id="elh_bens_patrimoniais_Localidade" class="bens_patrimoniais_Localidade"><?php echo $bens_patrimoniais->Localidade->FldCaption() ?></span></th>
<?php } ?>
<?php if ($bens_patrimoniais->Descricao->Visible) { // Descricao ?>
		<th><span id="elh_bens_patrimoniais_Descricao" class="bens_patrimoniais_Descricao"><?php echo $bens_patrimoniais->Descricao->FldCaption() ?></span></th>
<?php } ?>
<?php if ($bens_patrimoniais->DataAquisao->Visible) { // DataAquisao ?>
		<th><span id="elh_bens_patrimoniais_DataAquisao" class="bens_patrimoniais_DataAquisao"><?php echo $bens_patrimoniais->DataAquisao->FldCaption() ?></span></th>
<?php } ?>
<?php if ($bens_patrimoniais->Tipo->Visible) { // Tipo ?>
		<th><span id="elh_bens_patrimoniais_Tipo" class="bens_patrimoniais_Tipo"><?php echo $bens_patrimoniais->Tipo->FldCaption() ?></span></th>
<?php } ?>
<?php if ($bens_patrimoniais->Estado_do_bem->Visible) { // Estado_do_bem ?>
		<th><span id="elh_bens_patrimoniais_Estado_do_bem" class="bens_patrimoniais_Estado_do_bem"><?php echo $bens_patrimoniais->Estado_do_bem->FldCaption() ?></span></th>
<?php } ?>
<?php if ($bens_patrimoniais->Valor_estimado->Visible) { // Valor_estimado ?>
		<th><span id="elh_bens_patrimoniais_Valor_estimado" class="bens_patrimoniais_Valor_estimado"><?php echo $bens_patrimoniais->Valor_estimado->FldCaption() ?></span></th>
<?php } ?>
<?php if ($bens_patrimoniais->Situacao->Visible) { // Situacao ?>
		<th><span id="elh_bens_patrimoniais_Situacao" class="bens_patrimoniais_Situacao"><?php echo $bens_patrimoniais->Situacao->FldCaption() ?></span></th>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$bens_patrimoniais_delete->RecCnt = 0;
$i = 0;
while (!$bens_patrimoniais_delete->Recordset->EOF) {
	$bens_patrimoniais_delete->RecCnt++;
	$bens_patrimoniais_delete->RowCnt++;

	// Set row properties
	$bens_patrimoniais->ResetAttrs();
	$bens_patrimoniais->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$bens_patrimoniais_delete->LoadRowValues($bens_patrimoniais_delete->Recordset);

	// Render row
	$bens_patrimoniais_delete->RenderRow();
?>
	<tr<?php echo $bens_patrimoniais->RowAttributes() ?>>
<?php if ($bens_patrimoniais->Localidade->Visible) { // Localidade ?>
		<td<?php echo $bens_patrimoniais->Localidade->CellAttributes() ?>>
<span id="el<?php echo $bens_patrimoniais_delete->RowCnt ?>_bens_patrimoniais_Localidade" class="form-group bens_patrimoniais_Localidade">
<span<?php echo $bens_patrimoniais->Localidade->ViewAttributes() ?>>
<?php echo $bens_patrimoniais->Localidade->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($bens_patrimoniais->Descricao->Visible) { // Descricao ?>
		<td<?php echo $bens_patrimoniais->Descricao->CellAttributes() ?>>
<span id="el<?php echo $bens_patrimoniais_delete->RowCnt ?>_bens_patrimoniais_Descricao" class="form-group bens_patrimoniais_Descricao">
<span<?php echo $bens_patrimoniais->Descricao->ViewAttributes() ?>>
<?php echo $bens_patrimoniais->Descricao->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($bens_patrimoniais->DataAquisao->Visible) { // DataAquisao ?>
		<td<?php echo $bens_patrimoniais->DataAquisao->CellAttributes() ?>>
<span id="el<?php echo $bens_patrimoniais_delete->RowCnt ?>_bens_patrimoniais_DataAquisao" class="form-group bens_patrimoniais_DataAquisao">
<span<?php echo $bens_patrimoniais->DataAquisao->ViewAttributes() ?>>
<?php echo $bens_patrimoniais->DataAquisao->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($bens_patrimoniais->Tipo->Visible) { // Tipo ?>
		<td<?php echo $bens_patrimoniais->Tipo->CellAttributes() ?>>
<span id="el<?php echo $bens_patrimoniais_delete->RowCnt ?>_bens_patrimoniais_Tipo" class="form-group bens_patrimoniais_Tipo">
<span<?php echo $bens_patrimoniais->Tipo->ViewAttributes() ?>>
<?php echo $bens_patrimoniais->Tipo->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($bens_patrimoniais->Estado_do_bem->Visible) { // Estado_do_bem ?>
		<td<?php echo $bens_patrimoniais->Estado_do_bem->CellAttributes() ?>>
<span id="el<?php echo $bens_patrimoniais_delete->RowCnt ?>_bens_patrimoniais_Estado_do_bem" class="form-group bens_patrimoniais_Estado_do_bem">
<span<?php echo $bens_patrimoniais->Estado_do_bem->ViewAttributes() ?>>
<?php echo $bens_patrimoniais->Estado_do_bem->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($bens_patrimoniais->Valor_estimado->Visible) { // Valor_estimado ?>
		<td<?php echo $bens_patrimoniais->Valor_estimado->CellAttributes() ?>>
<span id="el<?php echo $bens_patrimoniais_delete->RowCnt ?>_bens_patrimoniais_Valor_estimado" class="form-group bens_patrimoniais_Valor_estimado">
<span<?php echo $bens_patrimoniais->Valor_estimado->ViewAttributes() ?>>
<?php echo $bens_patrimoniais->Valor_estimado->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($bens_patrimoniais->Situacao->Visible) { // Situacao ?>
		<td<?php echo $bens_patrimoniais->Situacao->CellAttributes() ?>>
<span id="el<?php echo $bens_patrimoniais_delete->RowCnt ?>_bens_patrimoniais_Situacao" class="form-group bens_patrimoniais_Situacao">
<span<?php echo $bens_patrimoniais->Situacao->ViewAttributes() ?>>
<?php echo $bens_patrimoniais->Situacao->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$bens_patrimoniais_delete->Recordset->MoveNext();
}
$bens_patrimoniais_delete->Recordset->Close();
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
fbens_patrimoniaisdelete.Init();
</script>
<?php
$bens_patrimoniais_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$bens_patrimoniais_delete->Page_Terminate();
?>
