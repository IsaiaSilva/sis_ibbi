<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg11.php" ?>
<?php include_once "ewmysql11.php" ?>
<?php include_once "phpfn11.php" ?>
<?php include_once "conta_bancariainfo.php" ?>
<?php include_once "usuariosinfo.php" ?>
<?php include_once "userfn11.php" ?>
<?php

//
// Page class
//

$conta_bancaria_delete = NULL; // Initialize page object first

class cconta_bancaria_delete extends cconta_bancaria {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{2B7992FC-5911-46A7-9310-01F4D4225C49}";

	// Table name
	var $TableName = 'conta_bancaria';

	// Page object name
	var $PageObjName = 'conta_bancaria_delete';

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

		// Table object (conta_bancaria)
		if (!isset($GLOBALS["conta_bancaria"]) || get_class($GLOBALS["conta_bancaria"]) == "cconta_bancaria") {
			$GLOBALS["conta_bancaria"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["conta_bancaria"];
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
			define("EW_TABLE_NAME", 'conta_bancaria', TRUE);

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
			$this->Page_Terminate(ew_GetUrl("conta_bancarialist.php"));
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
		global $EW_EXPORT, $conta_bancaria;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($conta_bancaria);
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
			$this->Page_Terminate("conta_bancarialist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in conta_bancaria class, conta_bancariainfo.php

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
		$this->Banco->setDbValue($rs->fields('Banco'));
		$this->Agencia->setDbValue($rs->fields('Agencia'));
		$this->Conta->setDbValue($rs->fields('Conta'));
		$this->Gerente->setDbValue($rs->fields('Gerente'));
		$this->Telefone->setDbValue($rs->fields('Telefone'));
		$this->Limite->setDbValue($rs->fields('Limite'));
		$this->Site->setDbValue($rs->fields('Site'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->Id->DbValue = $row['Id'];
		$this->Banco->DbValue = $row['Banco'];
		$this->Agencia->DbValue = $row['Agencia'];
		$this->Conta->DbValue = $row['Conta'];
		$this->Gerente->DbValue = $row['Gerente'];
		$this->Telefone->DbValue = $row['Telefone'];
		$this->Limite->DbValue = $row['Limite'];
		$this->Site->DbValue = $row['Site'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Convert decimal values if posted back

		if ($this->Limite->FormValue == $this->Limite->CurrentValue && is_numeric(ew_StrToFloat($this->Limite->CurrentValue)))
			$this->Limite->CurrentValue = ew_StrToFloat($this->Limite->CurrentValue);

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
		// Id

		$this->Id->CellCssStyle = "white-space: nowrap;";

		// Banco
		// Agencia
		// Conta
		// Gerente
		// Telefone
		// Limite
		// Site

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// Banco
			if (strval($this->Banco->CurrentValue) <> "") {
				$sFilterWrk = "`id`" . ew_SearchString("=", $this->Banco->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `id`, `N_do_Banco` AS `DispFld`, `Banco` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `bancos`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Banco, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->Banco->ViewValue = $rswrk->fields('DispFld');
					$this->Banco->ViewValue .= ew_ValueSeparator(1,$this->Banco) . $rswrk->fields('Disp2Fld');
					$rswrk->Close();
				} else {
					$this->Banco->ViewValue = $this->Banco->CurrentValue;
				}
			} else {
				$this->Banco->ViewValue = NULL;
			}
			$this->Banco->ViewCustomAttributes = "";

			// Agencia
			$this->Agencia->ViewValue = $this->Agencia->CurrentValue;
			$this->Agencia->ViewCustomAttributes = "";

			// Conta
			$this->Conta->ViewValue = $this->Conta->CurrentValue;
			$this->Conta->ViewCustomAttributes = "";

			// Gerente
			$this->Gerente->ViewValue = $this->Gerente->CurrentValue;
			$this->Gerente->ViewCustomAttributes = "";

			// Telefone
			$this->Telefone->ViewValue = $this->Telefone->CurrentValue;
			$this->Telefone->ViewCustomAttributes = "";

			// Limite
			$this->Limite->ViewValue = $this->Limite->CurrentValue;
			$this->Limite->ViewCustomAttributes = "";

			// Site
			$this->Site->ViewValue = $this->Site->CurrentValue;
			$this->Site->ViewCustomAttributes = "";

			// Banco
			$this->Banco->LinkCustomAttributes = "";
			$this->Banco->HrefValue = "";
			$this->Banco->TooltipValue = "";

			// Agencia
			$this->Agencia->LinkCustomAttributes = "";
			$this->Agencia->HrefValue = "";
			$this->Agencia->TooltipValue = "";

			// Conta
			$this->Conta->LinkCustomAttributes = "";
			$this->Conta->HrefValue = "";
			$this->Conta->TooltipValue = "";

			// Gerente
			$this->Gerente->LinkCustomAttributes = "";
			$this->Gerente->HrefValue = "";
			$this->Gerente->TooltipValue = "";

			// Telefone
			$this->Telefone->LinkCustomAttributes = "";
			$this->Telefone->HrefValue = "";
			$this->Telefone->TooltipValue = "";

			// Limite
			$this->Limite->LinkCustomAttributes = "";
			$this->Limite->HrefValue = "";
			$this->Limite->TooltipValue = "";

			// Site
			$this->Site->LinkCustomAttributes = "";
			$this->Site->HrefValue = "";
			$this->Site->TooltipValue = "";
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
		$Breadcrumb->Add("list", $this->TableVar, "conta_bancarialist.php", "", $this->TableVar, TRUE);
		$PageId = "delete";
		$Breadcrumb->Add("delete", $PageId, $url);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'conta_bancaria';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (delete page)
	function WriteAuditTrailOnDelete(&$rs) {
		if (!$this->AuditTrailOnDelete) return;
		$table = 'conta_bancaria';

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
if (!isset($conta_bancaria_delete)) $conta_bancaria_delete = new cconta_bancaria_delete();

// Page init
$conta_bancaria_delete->Page_Init();

// Page main
$conta_bancaria_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$conta_bancaria_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var conta_bancaria_delete = new ew_Page("conta_bancaria_delete");
conta_bancaria_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = conta_bancaria_delete.PageID; // For backward compatibility

// Form object
var fconta_bancariadelete = new ew_Form("fconta_bancariadelete");

// Form_CustomValidate event
fconta_bancariadelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fconta_bancariadelete.ValidateRequired = true;
<?php } else { ?>
fconta_bancariadelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fconta_bancariadelete.Lists["x_Banco"] = {"LinkField":"x_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_N_do_Banco","x_Banco","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($conta_bancaria_delete->Recordset = $conta_bancaria_delete->LoadRecordset())
	$conta_bancaria_deleteTotalRecs = $conta_bancaria_delete->Recordset->RecordCount(); // Get record count
if ($conta_bancaria_deleteTotalRecs <= 0) { // No record found, exit
	if ($conta_bancaria_delete->Recordset)
		$conta_bancaria_delete->Recordset->Close();
	$conta_bancaria_delete->Page_Terminate("conta_bancarialist.php"); // Return to list
}
?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php $conta_bancaria_delete->ShowPageHeader(); ?>
<?php
$conta_bancaria_delete->ShowMessage();
?>
<form name="fconta_bancariadelete" id="fconta_bancariadelete" class="form-inline ewForm ewDeleteForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($conta_bancaria_delete->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $conta_bancaria_delete->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="conta_bancaria">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($conta_bancaria_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<div class="ewGrid">
<div class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<table class="table ewTable">
<?php echo $conta_bancaria->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($conta_bancaria->Banco->Visible) { // Banco ?>
		<th><span id="elh_conta_bancaria_Banco" class="conta_bancaria_Banco"><?php echo $conta_bancaria->Banco->FldCaption() ?></span></th>
<?php } ?>
<?php if ($conta_bancaria->Agencia->Visible) { // Agencia ?>
		<th><span id="elh_conta_bancaria_Agencia" class="conta_bancaria_Agencia"><?php echo $conta_bancaria->Agencia->FldCaption() ?></span></th>
<?php } ?>
<?php if ($conta_bancaria->Conta->Visible) { // Conta ?>
		<th><span id="elh_conta_bancaria_Conta" class="conta_bancaria_Conta"><?php echo $conta_bancaria->Conta->FldCaption() ?></span></th>
<?php } ?>
<?php if ($conta_bancaria->Gerente->Visible) { // Gerente ?>
		<th><span id="elh_conta_bancaria_Gerente" class="conta_bancaria_Gerente"><?php echo $conta_bancaria->Gerente->FldCaption() ?></span></th>
<?php } ?>
<?php if ($conta_bancaria->Telefone->Visible) { // Telefone ?>
		<th><span id="elh_conta_bancaria_Telefone" class="conta_bancaria_Telefone"><?php echo $conta_bancaria->Telefone->FldCaption() ?></span></th>
<?php } ?>
<?php if ($conta_bancaria->Limite->Visible) { // Limite ?>
		<th><span id="elh_conta_bancaria_Limite" class="conta_bancaria_Limite"><?php echo $conta_bancaria->Limite->FldCaption() ?></span></th>
<?php } ?>
<?php if ($conta_bancaria->Site->Visible) { // Site ?>
		<th><span id="elh_conta_bancaria_Site" class="conta_bancaria_Site"><?php echo $conta_bancaria->Site->FldCaption() ?></span></th>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$conta_bancaria_delete->RecCnt = 0;
$i = 0;
while (!$conta_bancaria_delete->Recordset->EOF) {
	$conta_bancaria_delete->RecCnt++;
	$conta_bancaria_delete->RowCnt++;

	// Set row properties
	$conta_bancaria->ResetAttrs();
	$conta_bancaria->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$conta_bancaria_delete->LoadRowValues($conta_bancaria_delete->Recordset);

	// Render row
	$conta_bancaria_delete->RenderRow();
?>
	<tr<?php echo $conta_bancaria->RowAttributes() ?>>
<?php if ($conta_bancaria->Banco->Visible) { // Banco ?>
		<td<?php echo $conta_bancaria->Banco->CellAttributes() ?>>
<span id="el<?php echo $conta_bancaria_delete->RowCnt ?>_conta_bancaria_Banco" class="form-group conta_bancaria_Banco">
<span<?php echo $conta_bancaria->Banco->ViewAttributes() ?>>
<?php echo $conta_bancaria->Banco->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($conta_bancaria->Agencia->Visible) { // Agencia ?>
		<td<?php echo $conta_bancaria->Agencia->CellAttributes() ?>>
<span id="el<?php echo $conta_bancaria_delete->RowCnt ?>_conta_bancaria_Agencia" class="form-group conta_bancaria_Agencia">
<span<?php echo $conta_bancaria->Agencia->ViewAttributes() ?>>
<?php echo $conta_bancaria->Agencia->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($conta_bancaria->Conta->Visible) { // Conta ?>
		<td<?php echo $conta_bancaria->Conta->CellAttributes() ?>>
<span id="el<?php echo $conta_bancaria_delete->RowCnt ?>_conta_bancaria_Conta" class="form-group conta_bancaria_Conta">
<span<?php echo $conta_bancaria->Conta->ViewAttributes() ?>>
<?php echo $conta_bancaria->Conta->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($conta_bancaria->Gerente->Visible) { // Gerente ?>
		<td<?php echo $conta_bancaria->Gerente->CellAttributes() ?>>
<span id="el<?php echo $conta_bancaria_delete->RowCnt ?>_conta_bancaria_Gerente" class="form-group conta_bancaria_Gerente">
<span<?php echo $conta_bancaria->Gerente->ViewAttributes() ?>>
<?php echo $conta_bancaria->Gerente->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($conta_bancaria->Telefone->Visible) { // Telefone ?>
		<td<?php echo $conta_bancaria->Telefone->CellAttributes() ?>>
<span id="el<?php echo $conta_bancaria_delete->RowCnt ?>_conta_bancaria_Telefone" class="form-group conta_bancaria_Telefone">
<span<?php echo $conta_bancaria->Telefone->ViewAttributes() ?>>
<?php echo $conta_bancaria->Telefone->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($conta_bancaria->Limite->Visible) { // Limite ?>
		<td<?php echo $conta_bancaria->Limite->CellAttributes() ?>>
<span id="el<?php echo $conta_bancaria_delete->RowCnt ?>_conta_bancaria_Limite" class="form-group conta_bancaria_Limite">
<span<?php echo $conta_bancaria->Limite->ViewAttributes() ?>>
<?php echo $conta_bancaria->Limite->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($conta_bancaria->Site->Visible) { // Site ?>
		<td<?php echo $conta_bancaria->Site->CellAttributes() ?>>
<span id="el<?php echo $conta_bancaria_delete->RowCnt ?>_conta_bancaria_Site" class="form-group conta_bancaria_Site">
<span<?php echo $conta_bancaria->Site->ViewAttributes() ?>>
<?php echo $conta_bancaria->Site->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$conta_bancaria_delete->Recordset->MoveNext();
}
$conta_bancaria_delete->Recordset->Close();
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
fconta_bancariadelete.Init();
</script>
<?php
$conta_bancaria_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$conta_bancaria_delete->Page_Terminate();
?>
