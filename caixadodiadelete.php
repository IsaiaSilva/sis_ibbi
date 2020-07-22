<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg11.php" ?>
<?php include_once "ewmysql11.php" ?>
<?php include_once "phpfn11.php" ?>
<?php include_once "caixadodiainfo.php" ?>
<?php include_once "usuariosinfo.php" ?>
<?php include_once "userfn11.php" ?>
<?php

//
// Page class
//

$caixadodia_delete = NULL; // Initialize page object first

class ccaixadodia_delete extends ccaixadodia {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{2B7992FC-5911-46A7-9310-01F4D4225C49}";

	// Table name
	var $TableName = 'caixadodia';

	// Page object name
	var $PageObjName = 'caixadodia_delete';

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

		// Table object (caixadodia)
		if (!isset($GLOBALS["caixadodia"]) || get_class($GLOBALS["caixadodia"]) == "ccaixadodia") {
			$GLOBALS["caixadodia"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["caixadodia"];
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
			define("EW_TABLE_NAME", 'caixadodia', TRUE);

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
			$this->Page_Terminate(ew_GetUrl("caixadodialist.php"));
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
		global $EW_EXPORT, $caixadodia;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($caixadodia);
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
			$this->Page_Terminate("caixadodialist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in caixadodia class, caixadodiainfo.php

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
		$this->Tipo->setDbValue($rs->fields('Tipo'));
		$this->Conta_Caixa->setDbValue($rs->fields('Conta_Caixa'));
		$this->Situacao->setDbValue($rs->fields('Situacao'));
		$this->Descricao->setDbValue($rs->fields('Descricao'));
		$this->id_discipulo->setDbValue($rs->fields('id_discipulo'));
		$this->Receitas->setDbValue($rs->fields('Receitas'));
		$this->Despesas->setDbValue($rs->fields('Despesas'));
		$this->FormaPagto->setDbValue($rs->fields('FormaPagto'));
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
		$this->Conta_Caixa->DbValue = $row['Conta_Caixa'];
		$this->Situacao->DbValue = $row['Situacao'];
		$this->Descricao->DbValue = $row['Descricao'];
		$this->id_discipulo->DbValue = $row['id_discipulo'];
		$this->Receitas->DbValue = $row['Receitas'];
		$this->Despesas->DbValue = $row['Despesas'];
		$this->FormaPagto->DbValue = $row['FormaPagto'];
		$this->N_Documento->DbValue = $row['N_Documento'];
		$this->Dt_Lancamento->DbValue = $row['Dt_Lancamento'];
		$this->Vencimento->DbValue = $row['Vencimento'];
		$this->Centro_de_Custo->DbValue = $row['Centro_de_Custo'];
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

		$this->Id->CellCssStyle = "white-space: nowrap;";

		// Tipo
		// Conta_Caixa
		// Situacao
		// Descricao
		// id_discipulo
		// Receitas
		// Despesas
		// FormaPagto
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
			$this->Situacao->ViewCustomAttributes = "";

			// Descricao
			$this->Descricao->ViewValue = $this->Descricao->CurrentValue;
			$this->Descricao->ViewCustomAttributes = "";

			// id_discipulo
			if (strval($this->id_discipulo->CurrentValue) <> "") {
				$sFilterWrk = "`Id_membro`" . ew_SearchString("=", $this->id_discipulo->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `Id_membro`, `Nome` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `membro`";
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

			// FormaPagto
			$this->FormaPagto->LinkCustomAttributes = "";
			$this->FormaPagto->HrefValue = "";
			$this->FormaPagto->TooltipValue = "";

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
		} else {
			$conn->RollbackTrans(); // Rollback changes
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
		$Breadcrumb->Add("list", $this->TableVar, "caixadodialist.php", "", $this->TableVar, TRUE);
		$PageId = "delete";
		$Breadcrumb->Add("delete", $PageId, $url);
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
if (!isset($caixadodia_delete)) $caixadodia_delete = new ccaixadodia_delete();

// Page init
$caixadodia_delete->Page_Init();

// Page main
$caixadodia_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$caixadodia_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var caixadodia_delete = new ew_Page("caixadodia_delete");
caixadodia_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = caixadodia_delete.PageID; // For backward compatibility

// Form object
var fcaixadodiadelete = new ew_Form("fcaixadodiadelete");

// Form_CustomValidate event
fcaixadodiadelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fcaixadodiadelete.ValidateRequired = true;
<?php } else { ?>
fcaixadodiadelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fcaixadodiadelete.Lists["x_Conta_Caixa"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Conta_Caixa","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fcaixadodiadelete.Lists["x_Situacao"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Situacao","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fcaixadodiadelete.Lists["x_FormaPagto"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Forma_Pagto","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fcaixadodiadelete.Lists["x_Centro_de_Custo"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Conta","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($caixadodia_delete->Recordset = $caixadodia_delete->LoadRecordset())
	$caixadodia_deleteTotalRecs = $caixadodia_delete->Recordset->RecordCount(); // Get record count
if ($caixadodia_deleteTotalRecs <= 0) { // No record found, exit
	if ($caixadodia_delete->Recordset)
		$caixadodia_delete->Recordset->Close();
	$caixadodia_delete->Page_Terminate("caixadodialist.php"); // Return to list
}
?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php $caixadodia_delete->ShowPageHeader(); ?>
<?php
$caixadodia_delete->ShowMessage();
?>
<form name="fcaixadodiadelete" id="fcaixadodiadelete" class="form-inline ewForm ewDeleteForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($caixadodia_delete->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $caixadodia_delete->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="caixadodia">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($caixadodia_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<div class="ewGrid">
<div class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<table class="table ewTable">
<?php echo $caixadodia->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($caixadodia->Tipo->Visible) { // Tipo ?>
		<th><span id="elh_caixadodia_Tipo" class="caixadodia_Tipo"><?php echo $caixadodia->Tipo->FldCaption() ?></span></th>
<?php } ?>
<?php if ($caixadodia->Conta_Caixa->Visible) { // Conta_Caixa ?>
		<th><span id="elh_caixadodia_Conta_Caixa" class="caixadodia_Conta_Caixa"><?php echo $caixadodia->Conta_Caixa->FldCaption() ?></span></th>
<?php } ?>
<?php if ($caixadodia->Situacao->Visible) { // Situacao ?>
		<th><span id="elh_caixadodia_Situacao" class="caixadodia_Situacao"><?php echo $caixadodia->Situacao->FldCaption() ?></span></th>
<?php } ?>
<?php if ($caixadodia->Descricao->Visible) { // Descricao ?>
		<th><span id="elh_caixadodia_Descricao" class="caixadodia_Descricao"><?php echo $caixadodia->Descricao->FldCaption() ?></span></th>
<?php } ?>
<?php if ($caixadodia->Receitas->Visible) { // Receitas ?>
		<th><span id="elh_caixadodia_Receitas" class="caixadodia_Receitas"><?php echo $caixadodia->Receitas->FldCaption() ?></span></th>
<?php } ?>
<?php if ($caixadodia->Despesas->Visible) { // Despesas ?>
		<th><span id="elh_caixadodia_Despesas" class="caixadodia_Despesas"><?php echo $caixadodia->Despesas->FldCaption() ?></span></th>
<?php } ?>
<?php if ($caixadodia->FormaPagto->Visible) { // FormaPagto ?>
		<th><span id="elh_caixadodia_FormaPagto" class="caixadodia_FormaPagto"><?php echo $caixadodia->FormaPagto->FldCaption() ?></span></th>
<?php } ?>
<?php if ($caixadodia->N_Documento->Visible) { // N_Documento ?>
		<th><span id="elh_caixadodia_N_Documento" class="caixadodia_N_Documento"><?php echo $caixadodia->N_Documento->FldCaption() ?></span></th>
<?php } ?>
<?php if ($caixadodia->Dt_Lancamento->Visible) { // Dt_Lancamento ?>
		<th><span id="elh_caixadodia_Dt_Lancamento" class="caixadodia_Dt_Lancamento"><?php echo $caixadodia->Dt_Lancamento->FldCaption() ?></span></th>
<?php } ?>
<?php if ($caixadodia->Vencimento->Visible) { // Vencimento ?>
		<th><span id="elh_caixadodia_Vencimento" class="caixadodia_Vencimento"><?php echo $caixadodia->Vencimento->FldCaption() ?></span></th>
<?php } ?>
<?php if ($caixadodia->Centro_de_Custo->Visible) { // Centro_de_Custo ?>
		<th><span id="elh_caixadodia_Centro_de_Custo" class="caixadodia_Centro_de_Custo"><?php echo $caixadodia->Centro_de_Custo->FldCaption() ?></span></th>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$caixadodia_delete->RecCnt = 0;
$i = 0;
while (!$caixadodia_delete->Recordset->EOF) {
	$caixadodia_delete->RecCnt++;
	$caixadodia_delete->RowCnt++;

	// Set row properties
	$caixadodia->ResetAttrs();
	$caixadodia->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$caixadodia_delete->LoadRowValues($caixadodia_delete->Recordset);

	// Render row
	$caixadodia_delete->RenderRow();
?>
	<tr<?php echo $caixadodia->RowAttributes() ?>>
<?php if ($caixadodia->Tipo->Visible) { // Tipo ?>
		<td<?php echo $caixadodia->Tipo->CellAttributes() ?>>
<span id="el<?php echo $caixadodia_delete->RowCnt ?>_caixadodia_Tipo" class="form-group caixadodia_Tipo">
<span<?php echo $caixadodia->Tipo->ViewAttributes() ?>>
<?php echo $caixadodia->Tipo->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($caixadodia->Conta_Caixa->Visible) { // Conta_Caixa ?>
		<td<?php echo $caixadodia->Conta_Caixa->CellAttributes() ?>>
<span id="el<?php echo $caixadodia_delete->RowCnt ?>_caixadodia_Conta_Caixa" class="form-group caixadodia_Conta_Caixa">
<span<?php echo $caixadodia->Conta_Caixa->ViewAttributes() ?>>
<?php echo $caixadodia->Conta_Caixa->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($caixadodia->Situacao->Visible) { // Situacao ?>
		<td<?php echo $caixadodia->Situacao->CellAttributes() ?>>
<span id="el<?php echo $caixadodia_delete->RowCnt ?>_caixadodia_Situacao" class="form-group caixadodia_Situacao">
<span<?php echo $caixadodia->Situacao->ViewAttributes() ?>>
<?php echo $caixadodia->Situacao->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($caixadodia->Descricao->Visible) { // Descricao ?>
		<td<?php echo $caixadodia->Descricao->CellAttributes() ?>>
<span id="el<?php echo $caixadodia_delete->RowCnt ?>_caixadodia_Descricao" class="form-group caixadodia_Descricao">
<span<?php echo $caixadodia->Descricao->ViewAttributes() ?>>
<?php echo $caixadodia->Descricao->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($caixadodia->Receitas->Visible) { // Receitas ?>
		<td<?php echo $caixadodia->Receitas->CellAttributes() ?>>
<span id="el<?php echo $caixadodia_delete->RowCnt ?>_caixadodia_Receitas" class="form-group caixadodia_Receitas">
<span<?php echo $caixadodia->Receitas->ViewAttributes() ?>>
<?php echo $caixadodia->Receitas->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($caixadodia->Despesas->Visible) { // Despesas ?>
		<td<?php echo $caixadodia->Despesas->CellAttributes() ?>>
<span id="el<?php echo $caixadodia_delete->RowCnt ?>_caixadodia_Despesas" class="form-group caixadodia_Despesas">
<span<?php echo $caixadodia->Despesas->ViewAttributes() ?>>
<?php echo $caixadodia->Despesas->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($caixadodia->FormaPagto->Visible) { // FormaPagto ?>
		<td<?php echo $caixadodia->FormaPagto->CellAttributes() ?>>
<span id="el<?php echo $caixadodia_delete->RowCnt ?>_caixadodia_FormaPagto" class="form-group caixadodia_FormaPagto">
<span<?php echo $caixadodia->FormaPagto->ViewAttributes() ?>>
<?php echo $caixadodia->FormaPagto->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($caixadodia->N_Documento->Visible) { // N_Documento ?>
		<td<?php echo $caixadodia->N_Documento->CellAttributes() ?>>
<span id="el<?php echo $caixadodia_delete->RowCnt ?>_caixadodia_N_Documento" class="form-group caixadodia_N_Documento">
<span<?php echo $caixadodia->N_Documento->ViewAttributes() ?>>
<?php echo $caixadodia->N_Documento->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($caixadodia->Dt_Lancamento->Visible) { // Dt_Lancamento ?>
		<td<?php echo $caixadodia->Dt_Lancamento->CellAttributes() ?>>
<span id="el<?php echo $caixadodia_delete->RowCnt ?>_caixadodia_Dt_Lancamento" class="form-group caixadodia_Dt_Lancamento">
<span<?php echo $caixadodia->Dt_Lancamento->ViewAttributes() ?>>
<?php echo $caixadodia->Dt_Lancamento->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($caixadodia->Vencimento->Visible) { // Vencimento ?>
		<td<?php echo $caixadodia->Vencimento->CellAttributes() ?>>
<span id="el<?php echo $caixadodia_delete->RowCnt ?>_caixadodia_Vencimento" class="form-group caixadodia_Vencimento">
<span<?php echo $caixadodia->Vencimento->ViewAttributes() ?>>
<?php echo $caixadodia->Vencimento->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($caixadodia->Centro_de_Custo->Visible) { // Centro_de_Custo ?>
		<td<?php echo $caixadodia->Centro_de_Custo->CellAttributes() ?>>
<span id="el<?php echo $caixadodia_delete->RowCnt ?>_caixadodia_Centro_de_Custo" class="form-group caixadodia_Centro_de_Custo">
<span<?php echo $caixadodia->Centro_de_Custo->ViewAttributes() ?>>
<?php echo $caixadodia->Centro_de_Custo->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$caixadodia_delete->Recordset->MoveNext();
}
$caixadodia_delete->Recordset->Close();
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
fcaixadodiadelete.Init();
</script>
<?php
$caixadodia_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$caixadodia_delete->Page_Terminate();
?>
