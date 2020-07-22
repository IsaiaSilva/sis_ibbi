<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg11.php" ?>
<?php include_once "ewmysql11.php" ?>
<?php include_once "phpfn11.php" ?>
<?php include_once "financeiroinfo.php" ?>
<?php include_once "usuariosinfo.php" ?>
<?php include_once "userfn11.php" ?>
<?php

//
// Page class
//

$financeiro_search = NULL; // Initialize page object first

class cfinanceiro_search extends cfinanceiro {

	// Page ID
	var $PageID = 'search';

	// Project ID
	var $ProjectID = "{2B7992FC-5911-46A7-9310-01F4D4225C49}";

	// Table name
	var $TableName = 'financeiro';

	// Page object name
	var $PageObjName = 'financeiro_search';

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

		// Table object (financeiro)
		if (!isset($GLOBALS["financeiro"]) || get_class($GLOBALS["financeiro"]) == "cfinanceiro") {
			$GLOBALS["financeiro"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["financeiro"];
		}

		// Table object (usuarios)
		if (!isset($GLOBALS['usuarios'])) $GLOBALS['usuarios'] = new cusuarios();

		// User table object (usuarios)
		if (!isset($GLOBALS["UserTable"])) $GLOBALS["UserTable"] = new cusuarios();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'search', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'financeiro', TRUE);

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
		if (!$Security->CanSearch()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate(ew_GetUrl("financeirolist.php"));
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
		global $EW_EXPORT, $financeiro;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($financeiro);
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
	var $IsModal = FALSE;
	var $SearchLabelClass = "col-sm-3 control-label ewLabel";
	var $SearchRightColumnClass = "col-sm-9";

	//
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsSearchError;
		global $gbSkipHeaderFooter;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Check modal
		$this->IsModal = (@$_GET["modal"] == "1" || @$_POST["modal"] == "1");
		if ($this->IsModal)
			$gbSkipHeaderFooter = TRUE;
		if ($this->IsPageRequest()) { // Validate request

			// Get action
			$this->CurrentAction = $objForm->GetValue("a_search");
			switch ($this->CurrentAction) {
				case "S": // Get search criteria

					// Build search string for advanced search, remove blank field
					$this->LoadSearchValues(); // Get search values
					if ($this->ValidateSearch()) {
						$sSrchStr = $this->BuildAdvancedSearch();
					} else {
						$sSrchStr = "";
						$this->setFailureMessage($gsSearchError);
					}
					if ($sSrchStr <> "") {
						$sSrchStr = $this->UrlParm($sSrchStr);
						$sSrchStr = "financeirolist.php" . "?" . $sSrchStr;
						if ($this->IsModal) {
							$row = array();
							$row["url"] = $sSrchStr;
							echo ew_ArrayToJson(array($row));
							$this->Page_Terminate();
							exit();
						} else {
							$this->Page_Terminate($sSrchStr); // Go to list page
						}
					}
			}
		}

		// Restore search settings from Session
		if ($gsSearchError == "")
			$this->LoadAdvancedSearch();

		// Render row for search
		$this->RowType = EW_ROWTYPE_SEARCH;
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Build advanced search
	function BuildAdvancedSearch() {
		$sSrchUrl = "";
		$this->BuildSearchUrl($sSrchUrl, $this->Tipo); // Tipo
		$this->BuildSearchUrl($sSrchUrl, $this->Tipo_Recebimento); // Tipo_Recebimento
		$this->BuildSearchUrl($sSrchUrl, $this->FormaPagto); // FormaPagto
		$this->BuildSearchUrl($sSrchUrl, $this->Conta_Caixa); // Conta_Caixa
		$this->BuildSearchUrl($sSrchUrl, $this->Situacao); // Situacao
		$this->BuildSearchUrl($sSrchUrl, $this->Descricao); // Descricao
		$this->BuildSearchUrl($sSrchUrl, $this->Receitas); // Receitas
		$this->BuildSearchUrl($sSrchUrl, $this->Despesas); // Despesas
		$this->BuildSearchUrl($sSrchUrl, $this->N_Documento); // N_Documento
		$this->BuildSearchUrl($sSrchUrl, $this->Dt_Lancamento); // Dt_Lancamento
		$this->BuildSearchUrl($sSrchUrl, $this->Vencimento); // Vencimento
		$this->BuildSearchUrl($sSrchUrl, $this->Centro_de_Custo); // Centro_de_Custo
		if ($sSrchUrl <> "") $sSrchUrl .= "&";
		$sSrchUrl .= "cmd=search";
		return $sSrchUrl;
	}

	// Build search URL
	function BuildSearchUrl(&$Url, &$Fld, $OprOnly=FALSE) {
		global $objForm;
		$sWrk = "";
		$FldParm = substr($Fld->FldVar, 2);
		$FldVal = $objForm->GetValue("x_$FldParm");
		$FldOpr = $objForm->GetValue("z_$FldParm");
		$FldCond = $objForm->GetValue("v_$FldParm");
		$FldVal2 = $objForm->GetValue("y_$FldParm");
		$FldOpr2 = $objForm->GetValue("w_$FldParm");
		$FldVal = ew_StripSlashes($FldVal);
		if (is_array($FldVal)) $FldVal = implode(",", $FldVal);
		$FldVal2 = ew_StripSlashes($FldVal2);
		if (is_array($FldVal2)) $FldVal2 = implode(",", $FldVal2);
		$FldOpr = strtoupper(trim($FldOpr));
		$lFldDataType = ($Fld->FldIsVirtual) ? EW_DATATYPE_STRING : $Fld->FldDataType;
		if ($FldOpr == "BETWEEN") {
			$IsValidValue = ($lFldDataType <> EW_DATATYPE_NUMBER) ||
				($lFldDataType == EW_DATATYPE_NUMBER && $this->SearchValueIsNumeric($Fld, $FldVal) && $this->SearchValueIsNumeric($Fld, $FldVal2));
			if ($FldVal <> "" && $FldVal2 <> "" && $IsValidValue) {
				$sWrk = "x_" . $FldParm . "=" . urlencode($FldVal) .
					"&y_" . $FldParm . "=" . urlencode($FldVal2) .
					"&z_" . $FldParm . "=" . urlencode($FldOpr);
			}
		} else {
			$IsValidValue = ($lFldDataType <> EW_DATATYPE_NUMBER) ||
				($lFldDataType == EW_DATATYPE_NUMBER && $this->SearchValueIsNumeric($Fld, $FldVal));
			if ($FldVal <> "" && $IsValidValue && ew_IsValidOpr($FldOpr, $lFldDataType)) {
				$sWrk = "x_" . $FldParm . "=" . urlencode($FldVal) .
					"&z_" . $FldParm . "=" . urlencode($FldOpr);
			} elseif ($FldOpr == "IS NULL" || $FldOpr == "IS NOT NULL" || ($FldOpr <> "" && $OprOnly && ew_IsValidOpr($FldOpr, $lFldDataType))) {
				$sWrk = "z_" . $FldParm . "=" . urlencode($FldOpr);
			}
			$IsValidValue = ($lFldDataType <> EW_DATATYPE_NUMBER) ||
				($lFldDataType == EW_DATATYPE_NUMBER && $this->SearchValueIsNumeric($Fld, $FldVal2));
			if ($FldVal2 <> "" && $IsValidValue && ew_IsValidOpr($FldOpr2, $lFldDataType)) {
				if ($sWrk <> "") $sWrk .= "&v_" . $FldParm . "=" . urlencode($FldCond) . "&";
				$sWrk .= "y_" . $FldParm . "=" . urlencode($FldVal2) .
					"&w_" . $FldParm . "=" . urlencode($FldOpr2);
			} elseif ($FldOpr2 == "IS NULL" || $FldOpr2 == "IS NOT NULL" || ($FldOpr2 <> "" && $OprOnly && ew_IsValidOpr($FldOpr2, $lFldDataType))) {
				if ($sWrk <> "") $sWrk .= "&v_" . $FldParm . "=" . urlencode($FldCond) . "&";
				$sWrk .= "w_" . $FldParm . "=" . urlencode($FldOpr2);
			}
		}
		if ($sWrk <> "") {
			if ($Url <> "") $Url .= "&";
			$Url .= $sWrk;
		}
	}

	function SearchValueIsNumeric($Fld, $Value) {
		if (ew_IsFloatFormat($Fld->FldType)) $Value = ew_StrToFloat($Value);
		return is_numeric($Value);
	}

	//  Load search values for validation
	function LoadSearchValues() {
		global $objForm;

		// Load search values
		// Tipo

		$this->Tipo->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Tipo"));
		$this->Tipo->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Tipo");

		// Tipo_Recebimento
		$this->Tipo_Recebimento->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Tipo_Recebimento"));
		$this->Tipo_Recebimento->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Tipo_Recebimento");

		// FormaPagto
		$this->FormaPagto->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_FormaPagto"));
		$this->FormaPagto->AdvancedSearch->SearchOperator = $objForm->GetValue("z_FormaPagto");

		// Conta_Caixa
		$this->Conta_Caixa->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Conta_Caixa"));
		$this->Conta_Caixa->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Conta_Caixa");

		// Situacao
		$this->Situacao->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Situacao"));
		$this->Situacao->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Situacao");

		// Descricao
		$this->Descricao->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Descricao"));
		$this->Descricao->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Descricao");

		// Receitas
		$this->Receitas->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Receitas"));
		$this->Receitas->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Receitas");

		// Despesas
		$this->Despesas->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Despesas"));
		$this->Despesas->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Despesas");

		// N_Documento
		$this->N_Documento->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_N_Documento"));
		$this->N_Documento->AdvancedSearch->SearchOperator = $objForm->GetValue("z_N_Documento");

		// Dt_Lancamento
		$this->Dt_Lancamento->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Dt_Lancamento"));
		$this->Dt_Lancamento->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Dt_Lancamento");
		$this->Dt_Lancamento->AdvancedSearch->SearchCondition = $objForm->GetValue("v_Dt_Lancamento");
		$this->Dt_Lancamento->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_Dt_Lancamento"));
		$this->Dt_Lancamento->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_Dt_Lancamento");

		// Vencimento
		$this->Vencimento->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Vencimento"));
		$this->Vencimento->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Vencimento");
		$this->Vencimento->AdvancedSearch->SearchCondition = $objForm->GetValue("v_Vencimento");
		$this->Vencimento->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_Vencimento"));
		$this->Vencimento->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_Vencimento");

		// Centro_de_Custo
		$this->Centro_de_Custo->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Centro_de_Custo"));
		$this->Centro_de_Custo->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Centro_de_Custo");
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
		// Tipo
		// Tipo_Recebimento
		// FormaPagto
		// Conta_Caixa
		// Situacao
		// Descricao
		// id_discipulo
		// Receitas
		// Despesas
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

			// Tipo_Recebimento
			if (strval($this->Tipo_Recebimento->CurrentValue) <> "") {
				switch ($this->Tipo_Recebimento->CurrentValue) {
					case $this->Tipo_Recebimento->FldTagValue(1):
						$this->Tipo_Recebimento->ViewValue = $this->Tipo_Recebimento->FldTagCaption(1) <> "" ? $this->Tipo_Recebimento->FldTagCaption(1) : $this->Tipo_Recebimento->CurrentValue;
						break;
					case $this->Tipo_Recebimento->FldTagValue(2):
						$this->Tipo_Recebimento->ViewValue = $this->Tipo_Recebimento->FldTagCaption(2) <> "" ? $this->Tipo_Recebimento->FldTagCaption(2) : $this->Tipo_Recebimento->CurrentValue;
						break;
					default:
						$this->Tipo_Recebimento->ViewValue = $this->Tipo_Recebimento->CurrentValue;
				}
			} else {
				$this->Tipo_Recebimento->ViewValue = NULL;
			}
			$this->Tipo_Recebimento->CellCssStyle .= "text-align: center;";
			$this->Tipo_Recebimento->ViewCustomAttributes = "";

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
			$sSqlWrk .= " ORDER BY `Forma_Pagto` ASC";
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
			$this->Situacao->CellCssStyle .= "text-align: center;";
			$this->Situacao->ViewCustomAttributes = "";

			// Descricao
			$this->Descricao->ViewValue = $this->Descricao->CurrentValue;
			$this->Descricao->ViewCustomAttributes = "";

			// id_discipulo
			if (strval($this->id_discipulo->CurrentValue) <> "") {
				$sFilterWrk = "`Id_membro`" . ew_SearchString("=", $this->id_discipulo->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `Id_membro`, `Nome` AS `DispFld`, `CPF` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `membro`";
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
					$this->id_discipulo->ViewValue .= ew_ValueSeparator(1,$this->id_discipulo) . $rswrk->fields('Disp2Fld');
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
			$sSqlWrk .= " ORDER BY `Conta` ASC";
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

			// Tipo_Recebimento
			$this->Tipo_Recebimento->LinkCustomAttributes = "";
			$this->Tipo_Recebimento->HrefValue = "";
			$this->Tipo_Recebimento->TooltipValue = "";

			// FormaPagto
			$this->FormaPagto->LinkCustomAttributes = "";
			$this->FormaPagto->HrefValue = "";
			$this->FormaPagto->TooltipValue = "";

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
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// Tipo
			$this->Tipo->EditAttrs["class"] = "form-control";
			$this->Tipo->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->Tipo->FldTagValue(1), $this->Tipo->FldTagCaption(1) <> "" ? $this->Tipo->FldTagCaption(1) : $this->Tipo->FldTagValue(1));
			$arwrk[] = array($this->Tipo->FldTagValue(2), $this->Tipo->FldTagCaption(2) <> "" ? $this->Tipo->FldTagCaption(2) : $this->Tipo->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->Tipo->EditValue = $arwrk;

			// Tipo_Recebimento
			$this->Tipo_Recebimento->EditAttrs["class"] = "form-control";
			$this->Tipo_Recebimento->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->Tipo_Recebimento->FldTagValue(1), $this->Tipo_Recebimento->FldTagCaption(1) <> "" ? $this->Tipo_Recebimento->FldTagCaption(1) : $this->Tipo_Recebimento->FldTagValue(1));
			$arwrk[] = array($this->Tipo_Recebimento->FldTagValue(2), $this->Tipo_Recebimento->FldTagCaption(2) <> "" ? $this->Tipo_Recebimento->FldTagCaption(2) : $this->Tipo_Recebimento->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->Tipo_Recebimento->EditValue = $arwrk;

			// FormaPagto
			$this->FormaPagto->EditAttrs["class"] = "form-control";
			$this->FormaPagto->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `Id`, `Forma_Pagto` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `filtro_tipo_recebimento` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `fin_forma_pgto`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->FormaPagto, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `Forma_Pagto` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->FormaPagto->EditValue = $arwrk;

			// Conta_Caixa
			$this->Conta_Caixa->EditAttrs["class"] = "form-control";
			$this->Conta_Caixa->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `Id`, `Conta_Caixa` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `Tipo` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `fin_conta_caixa`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Conta_Caixa, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->Conta_Caixa->EditValue = $arwrk;

			// Situacao
			$this->Situacao->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `Id`, `Situacao` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `id_tipo` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `fin_situacao`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Situacao, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Situacao->EditValue = $arwrk;

			// Descricao
			$this->Descricao->EditAttrs["class"] = "form-control";
			$this->Descricao->EditCustomAttributes = "";
			$this->Descricao->EditValue = ew_HtmlEncode($this->Descricao->AdvancedSearch->SearchValue);

			// Receitas
			$this->Receitas->EditAttrs["class"] = "form-control";
			$this->Receitas->EditCustomAttributes = "";
			$this->Receitas->EditValue = ew_HtmlEncode($this->Receitas->AdvancedSearch->SearchValue);

			// Despesas
			$this->Despesas->EditAttrs["class"] = "form-control";
			$this->Despesas->EditCustomAttributes = "";
			$this->Despesas->EditValue = ew_HtmlEncode($this->Despesas->AdvancedSearch->SearchValue);

			// N_Documento
			$this->N_Documento->EditAttrs["class"] = "form-control";
			$this->N_Documento->EditCustomAttributes = "";
			$this->N_Documento->EditValue = ew_HtmlEncode($this->N_Documento->AdvancedSearch->SearchValue);

			// Dt_Lancamento
			$this->Dt_Lancamento->EditAttrs["class"] = "form-control";
			$this->Dt_Lancamento->EditCustomAttributes = "";
			$this->Dt_Lancamento->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->Dt_Lancamento->AdvancedSearch->SearchValue, 7), 7));
			$this->Dt_Lancamento->EditAttrs["class"] = "form-control";
			$this->Dt_Lancamento->EditCustomAttributes = "";
			$this->Dt_Lancamento->EditValue2 = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->Dt_Lancamento->AdvancedSearch->SearchValue2, 7), 7));

			// Vencimento
			$this->Vencimento->EditAttrs["class"] = "form-control";
			$this->Vencimento->EditCustomAttributes = "";
			$this->Vencimento->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->Vencimento->AdvancedSearch->SearchValue, 7), 7));
			$this->Vencimento->EditAttrs["class"] = "form-control";
			$this->Vencimento->EditCustomAttributes = "";
			$this->Vencimento->EditValue2 = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->Vencimento->AdvancedSearch->SearchValue2, 7), 7));

			// Centro_de_Custo
			$this->Centro_de_Custo->EditAttrs["class"] = "form-control";
			$this->Centro_de_Custo->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `Id`, `Conta` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `fin_centro_de_custo`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Centro_de_Custo, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `Conta` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->Centro_de_Custo->EditValue = $arwrk;
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

	// Validate search
	function ValidateSearch() {
		global $gsSearchError;

		// Initialize
		$gsSearchError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return TRUE;
		if (!ew_CheckNumber($this->Receitas->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->Receitas->FldErrMsg());
		}
		if (!ew_CheckNumber($this->Despesas->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->Despesas->FldErrMsg());
		}
		if (!ew_CheckEuroDate($this->Dt_Lancamento->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->Dt_Lancamento->FldErrMsg());
		}
		if (!ew_CheckEuroDate($this->Dt_Lancamento->AdvancedSearch->SearchValue2)) {
			ew_AddMessage($gsSearchError, $this->Dt_Lancamento->FldErrMsg());
		}
		if (!ew_CheckEuroDate($this->Vencimento->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->Vencimento->FldErrMsg());
		}
		if (!ew_CheckEuroDate($this->Vencimento->AdvancedSearch->SearchValue2)) {
			ew_AddMessage($gsSearchError, $this->Vencimento->FldErrMsg());
		}

		// Return validate result
		$ValidateSearch = ($gsSearchError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateSearch = $ValidateSearch && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsSearchError, $sFormCustomError);
		}
		return $ValidateSearch;
	}

	// Load advanced search
	function LoadAdvancedSearch() {
		$this->Tipo->AdvancedSearch->Load();
		$this->Tipo_Recebimento->AdvancedSearch->Load();
		$this->FormaPagto->AdvancedSearch->Load();
		$this->Conta_Caixa->AdvancedSearch->Load();
		$this->Situacao->AdvancedSearch->Load();
		$this->Descricao->AdvancedSearch->Load();
		$this->Receitas->AdvancedSearch->Load();
		$this->Despesas->AdvancedSearch->Load();
		$this->N_Documento->AdvancedSearch->Load();
		$this->Dt_Lancamento->AdvancedSearch->Load();
		$this->Vencimento->AdvancedSearch->Load();
		$this->Centro_de_Custo->AdvancedSearch->Load();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, "financeirolist.php", "", $this->TableVar, TRUE);
		$PageId = "search";
		$Breadcrumb->Add("search", $PageId, $url);
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
if (!isset($financeiro_search)) $financeiro_search = new cfinanceiro_search();

// Page init
$financeiro_search->Page_Init();

// Page main
$financeiro_search->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$financeiro_search->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var financeiro_search = new ew_Page("financeiro_search");
financeiro_search.PageID = "search"; // Page ID
var EW_PAGE_ID = financeiro_search.PageID; // For backward compatibility

// Form object
var ffinanceirosearch = new ew_Form("ffinanceirosearch");

// Form_CustomValidate event
ffinanceirosearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ffinanceirosearch.ValidateRequired = true;
<?php } else { ?>
ffinanceirosearch.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
ffinanceirosearch.Lists["x_FormaPagto"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Forma_Pagto","","",""],"ParentFields":["x_Tipo_Recebimento"],"FilterFields":["x_filtro_tipo_recebimento"],"Options":[]};
ffinanceirosearch.Lists["x_Conta_Caixa"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Conta_Caixa","","",""],"ParentFields":["x_Tipo"],"FilterFields":["x_Tipo"],"Options":[]};
ffinanceirosearch.Lists["x_Situacao"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Situacao","","",""],"ParentFields":["x_Tipo"],"FilterFields":["x_id_tipo"],"Options":[]};
ffinanceirosearch.Lists["x_Centro_de_Custo"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Conta","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
// Validate function for search

ffinanceirosearch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	this.PostAutoSuggest();
	var infix = "";
	elm = this.GetElements("x" + infix + "_Receitas");
	if (elm && !ew_CheckNumber(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($financeiro->Receitas->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_Despesas");
	if (elm && !ew_CheckNumber(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($financeiro->Despesas->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_Dt_Lancamento");
	if (elm && !ew_CheckEuroDate(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($financeiro->Dt_Lancamento->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_Vencimento");
	if (elm && !ew_CheckEuroDate(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($financeiro->Vencimento->FldErrMsg()) ?>");

	// Set up row object
	ew_ElementsToRow(fobj);

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$financeiro_search->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $financeiro_search->ShowPageHeader(); ?>
<?php
$financeiro_search->ShowMessage();
?>
<form name="ffinanceirosearch" id="ffinanceirosearch" class="form-horizontal ewForm ewSearchForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($financeiro_search->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $financeiro_search->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="financeiro">
<input type="hidden" name="a_search" id="a_search" value="S">
<?php if ($financeiro_search->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<div>
<?php if ($financeiro->Tipo->Visible) { // Tipo ?>
	<div id="r_Tipo" class="form-group">
		<label for="x_Tipo" class="<?php echo $financeiro_search->SearchLabelClass ?>"><span id="elh_financeiro_Tipo"><?php echo $financeiro->Tipo->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Tipo" id="z_Tipo" value="="></p>
		</label>
		<div class="<?php echo $financeiro_search->SearchRightColumnClass ?>"><div<?php echo $financeiro->Tipo->CellAttributes() ?>>
			<span id="el_financeiro_Tipo">
<?php $financeiro->Tipo->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x_Conta_Caixa','x_Situacao']); " . @$financeiro->Tipo->EditAttrs["onchange"]; ?>
<select data-field="x_Tipo" id="x_Tipo" name="x_Tipo"<?php echo $financeiro->Tipo->EditAttributes() ?>>
<?php
if (is_array($financeiro->Tipo->EditValue)) {
	$arwrk = $financeiro->Tipo->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($financeiro->Tipo->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
		</div></div>
	</div>
<?php } ?>
<?php if ($financeiro->Tipo_Recebimento->Visible) { // Tipo_Recebimento ?>
	<div id="r_Tipo_Recebimento" class="form-group">
		<label for="x_Tipo_Recebimento" class="<?php echo $financeiro_search->SearchLabelClass ?>"><span id="elh_financeiro_Tipo_Recebimento"><?php echo $financeiro->Tipo_Recebimento->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Tipo_Recebimento" id="z_Tipo_Recebimento" value="="></p>
		</label>
		<div class="<?php echo $financeiro_search->SearchRightColumnClass ?>"><div<?php echo $financeiro->Tipo_Recebimento->CellAttributes() ?>>
			<span id="el_financeiro_Tipo_Recebimento">
<?php $financeiro->Tipo_Recebimento->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x_FormaPagto']); " . @$financeiro->Tipo_Recebimento->EditAttrs["onchange"]; ?>
<select data-field="x_Tipo_Recebimento" id="x_Tipo_Recebimento" name="x_Tipo_Recebimento"<?php echo $financeiro->Tipo_Recebimento->EditAttributes() ?>>
<?php
if (is_array($financeiro->Tipo_Recebimento->EditValue)) {
	$arwrk = $financeiro->Tipo_Recebimento->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($financeiro->Tipo_Recebimento->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
		</div></div>
	</div>
<?php } ?>
<?php if ($financeiro->FormaPagto->Visible) { // FormaPagto ?>
	<div id="r_FormaPagto" class="form-group">
		<label for="x_FormaPagto" class="<?php echo $financeiro_search->SearchLabelClass ?>"><span id="elh_financeiro_FormaPagto"><?php echo $financeiro->FormaPagto->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_FormaPagto" id="z_FormaPagto" value="="></p>
		</label>
		<div class="<?php echo $financeiro_search->SearchRightColumnClass ?>"><div<?php echo $financeiro->FormaPagto->CellAttributes() ?>>
			<span id="el_financeiro_FormaPagto">
<select data-field="x_FormaPagto" id="x_FormaPagto" name="x_FormaPagto"<?php echo $financeiro->FormaPagto->EditAttributes() ?>>
<?php
if (is_array($financeiro->FormaPagto->EditValue)) {
	$arwrk = $financeiro->FormaPagto->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($financeiro->FormaPagto->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
ffinanceirosearch.Lists["x_FormaPagto"].Options = <?php echo (is_array($financeiro->FormaPagto->EditValue)) ? ew_ArrayToJson($financeiro->FormaPagto->EditValue, 1) : "[]" ?>;
</script>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($financeiro->Conta_Caixa->Visible) { // Conta_Caixa ?>
	<div id="r_Conta_Caixa" class="form-group">
		<label for="x_Conta_Caixa" class="<?php echo $financeiro_search->SearchLabelClass ?>"><span id="elh_financeiro_Conta_Caixa"><?php echo $financeiro->Conta_Caixa->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Conta_Caixa" id="z_Conta_Caixa" value="="></p>
		</label>
		<div class="<?php echo $financeiro_search->SearchRightColumnClass ?>"><div<?php echo $financeiro->Conta_Caixa->CellAttributes() ?>>
			<span id="el_financeiro_Conta_Caixa">
<select data-field="x_Conta_Caixa" id="x_Conta_Caixa" name="x_Conta_Caixa"<?php echo $financeiro->Conta_Caixa->EditAttributes() ?>>
<?php
if (is_array($financeiro->Conta_Caixa->EditValue)) {
	$arwrk = $financeiro->Conta_Caixa->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($financeiro->Conta_Caixa->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
ffinanceirosearch.Lists["x_Conta_Caixa"].Options = <?php echo (is_array($financeiro->Conta_Caixa->EditValue)) ? ew_ArrayToJson($financeiro->Conta_Caixa->EditValue, 1) : "[]" ?>;
</script>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($financeiro->Situacao->Visible) { // Situacao ?>
	<div id="r_Situacao" class="form-group">
		<label class="<?php echo $financeiro_search->SearchLabelClass ?>"><span id="elh_financeiro_Situacao"><?php echo $financeiro->Situacao->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Situacao" id="z_Situacao" value="="></p>
		</label>
		<div class="<?php echo $financeiro_search->SearchRightColumnClass ?>"><div<?php echo $financeiro->Situacao->CellAttributes() ?>>
			<span id="el_financeiro_Situacao">
<div id="tp_x_Situacao" class="<?php echo EW_ITEM_TEMPLATE_CLASSNAME ?>"><input type="radio" name="x_Situacao" id="x_Situacao" value="{value}"<?php echo $financeiro->Situacao->EditAttributes() ?>></div>
<div id="dsl_x_Situacao" data-repeatcolumn="5" class="ewItemList">
<?php
$arwrk = $financeiro->Situacao->EditValue;
if (is_array($arwrk)) {
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($financeiro->Situacao->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " checked=\"checked\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;

		// Note: No spacing within the LABEL tag
?>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 1) ?>
<label class="radio-inline"><input type="radio" data-field="x_Situacao" name="x_Situacao" id="x_Situacao_<?php echo $rowcntwrk ?>" value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?><?php echo $financeiro->Situacao->EditAttributes() ?>><?php echo $arwrk[$rowcntwrk][1] ?></label>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 2) ?>
<?php
	}
}
?>
</div>
<script type="text/javascript">
ffinanceirosearch.Lists["x_Situacao"].Options = <?php echo (is_array($financeiro->Situacao->EditValue)) ? ew_ArrayToJson($financeiro->Situacao->EditValue, 0) : "[]" ?>;
</script>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($financeiro->Descricao->Visible) { // Descricao ?>
	<div id="r_Descricao" class="form-group">
		<label for="x_Descricao" class="<?php echo $financeiro_search->SearchLabelClass ?>"><span id="elh_financeiro_Descricao"><?php echo $financeiro->Descricao->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Descricao" id="z_Descricao" value="LIKE"></p>
		</label>
		<div class="<?php echo $financeiro_search->SearchRightColumnClass ?>"><div<?php echo $financeiro->Descricao->CellAttributes() ?>>
			<span id="el_financeiro_Descricao">
<input type="text" data-field="x_Descricao" name="x_Descricao" id="x_Descricao" size="60" maxlength="60" value="<?php echo $financeiro->Descricao->EditValue ?>"<?php echo $financeiro->Descricao->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($financeiro->Receitas->Visible) { // Receitas ?>
	<div id="r_Receitas" class="form-group">
		<label for="x_Receitas" class="<?php echo $financeiro_search->SearchLabelClass ?>"><span id="elh_financeiro_Receitas"><?php echo $financeiro->Receitas->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Receitas" id="z_Receitas" value="="></p>
		</label>
		<div class="<?php echo $financeiro_search->SearchRightColumnClass ?>"><div<?php echo $financeiro->Receitas->CellAttributes() ?>>
			<span id="el_financeiro_Receitas">
<input type="text" data-field="x_Receitas" name="x_Receitas" id="x_Receitas" size="15" value="<?php echo $financeiro->Receitas->EditValue ?>"<?php echo $financeiro->Receitas->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($financeiro->Despesas->Visible) { // Despesas ?>
	<div id="r_Despesas" class="form-group">
		<label for="x_Despesas" class="<?php echo $financeiro_search->SearchLabelClass ?>"><span id="elh_financeiro_Despesas"><?php echo $financeiro->Despesas->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Despesas" id="z_Despesas" value="="></p>
		</label>
		<div class="<?php echo $financeiro_search->SearchRightColumnClass ?>"><div<?php echo $financeiro->Despesas->CellAttributes() ?>>
			<span id="el_financeiro_Despesas">
<input type="text" data-field="x_Despesas" name="x_Despesas" id="x_Despesas" size="15" value="<?php echo $financeiro->Despesas->EditValue ?>"<?php echo $financeiro->Despesas->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($financeiro->N_Documento->Visible) { // N_Documento ?>
	<div id="r_N_Documento" class="form-group">
		<label for="x_N_Documento" class="<?php echo $financeiro_search->SearchLabelClass ?>"><span id="elh_financeiro_N_Documento"><?php echo $financeiro->N_Documento->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_N_Documento" id="z_N_Documento" value="LIKE"></p>
		</label>
		<div class="<?php echo $financeiro_search->SearchRightColumnClass ?>"><div<?php echo $financeiro->N_Documento->CellAttributes() ?>>
			<span id="el_financeiro_N_Documento">
<input type="text" data-field="x_N_Documento" name="x_N_Documento" id="x_N_Documento" size="20" maxlength="20" value="<?php echo $financeiro->N_Documento->EditValue ?>"<?php echo $financeiro->N_Documento->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($financeiro->Dt_Lancamento->Visible) { // Dt_Lancamento ?>
	<div id="r_Dt_Lancamento" class="form-group">
		<label for="x_Dt_Lancamento" class="<?php echo $financeiro_search->SearchLabelClass ?>"><span id="elh_financeiro_Dt_Lancamento"><?php echo $financeiro->Dt_Lancamento->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("BETWEEN") ?><input type="hidden" name="z_Dt_Lancamento" id="z_Dt_Lancamento" value="BETWEEN"></p>
		</label>
		<div class="<?php echo $financeiro_search->SearchRightColumnClass ?>"><div<?php echo $financeiro->Dt_Lancamento->CellAttributes() ?>>
			<span id="el_financeiro_Dt_Lancamento">
<input type="text" data-field="x_Dt_Lancamento" name="x_Dt_Lancamento" id="x_Dt_Lancamento" size="10" value="<?php echo $financeiro->Dt_Lancamento->EditValue ?>"<?php echo $financeiro->Dt_Lancamento->EditAttributes() ?>>
<?php if (!$financeiro->Dt_Lancamento->ReadOnly && !$financeiro->Dt_Lancamento->Disabled && @$financeiro->Dt_Lancamento->EditAttrs["readonly"] == "" && @$financeiro->Dt_Lancamento->EditAttrs["disabled"] == "") { ?>
<script type="text/javascript">
ew_CreateCalendar("ffinanceirosearch", "x_Dt_Lancamento", "%d/%m/%Y");
</script>
<?php } ?>
</span>
			<span class="ewSearchCond btw1_Dt_Lancamento">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span id="e2_financeiro_Dt_Lancamento" class="btw1_Dt_Lancamento">
<input type="text" data-field="x_Dt_Lancamento" name="y_Dt_Lancamento" id="y_Dt_Lancamento" size="10" value="<?php echo $financeiro->Dt_Lancamento->EditValue2 ?>"<?php echo $financeiro->Dt_Lancamento->EditAttributes() ?>>
<?php if (!$financeiro->Dt_Lancamento->ReadOnly && !$financeiro->Dt_Lancamento->Disabled && @$financeiro->Dt_Lancamento->EditAttrs["readonly"] == "" && @$financeiro->Dt_Lancamento->EditAttrs["disabled"] == "") { ?>
<script type="text/javascript">
ew_CreateCalendar("ffinanceirosearch", "y_Dt_Lancamento", "%d/%m/%Y");
</script>
<?php } ?>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($financeiro->Vencimento->Visible) { // Vencimento ?>
	<div id="r_Vencimento" class="form-group">
		<label for="x_Vencimento" class="<?php echo $financeiro_search->SearchLabelClass ?>"><span id="elh_financeiro_Vencimento"><?php echo $financeiro->Vencimento->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("BETWEEN") ?><input type="hidden" name="z_Vencimento" id="z_Vencimento" value="BETWEEN"></p>
		</label>
		<div class="<?php echo $financeiro_search->SearchRightColumnClass ?>"><div<?php echo $financeiro->Vencimento->CellAttributes() ?>>
			<span id="el_financeiro_Vencimento">
<input type="text" data-field="x_Vencimento" name="x_Vencimento" id="x_Vencimento" size="10" value="<?php echo $financeiro->Vencimento->EditValue ?>"<?php echo $financeiro->Vencimento->EditAttributes() ?>>
<?php if (!$financeiro->Vencimento->ReadOnly && !$financeiro->Vencimento->Disabled && @$financeiro->Vencimento->EditAttrs["readonly"] == "" && @$financeiro->Vencimento->EditAttrs["disabled"] == "") { ?>
<script type="text/javascript">
ew_CreateCalendar("ffinanceirosearch", "x_Vencimento", "%d/%m/%Y");
</script>
<?php } ?>
</span>
			<span class="ewSearchCond btw1_Vencimento">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span id="e2_financeiro_Vencimento" class="btw1_Vencimento">
<input type="text" data-field="x_Vencimento" name="y_Vencimento" id="y_Vencimento" size="10" value="<?php echo $financeiro->Vencimento->EditValue2 ?>"<?php echo $financeiro->Vencimento->EditAttributes() ?>>
<?php if (!$financeiro->Vencimento->ReadOnly && !$financeiro->Vencimento->Disabled && @$financeiro->Vencimento->EditAttrs["readonly"] == "" && @$financeiro->Vencimento->EditAttrs["disabled"] == "") { ?>
<script type="text/javascript">
ew_CreateCalendar("ffinanceirosearch", "y_Vencimento", "%d/%m/%Y");
</script>
<?php } ?>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($financeiro->Centro_de_Custo->Visible) { // Centro_de_Custo ?>
	<div id="r_Centro_de_Custo" class="form-group">
		<label for="x_Centro_de_Custo" class="<?php echo $financeiro_search->SearchLabelClass ?>"><span id="elh_financeiro_Centro_de_Custo"><?php echo $financeiro->Centro_de_Custo->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Centro_de_Custo" id="z_Centro_de_Custo" value="="></p>
		</label>
		<div class="<?php echo $financeiro_search->SearchRightColumnClass ?>"><div<?php echo $financeiro->Centro_de_Custo->CellAttributes() ?>>
			<span id="el_financeiro_Centro_de_Custo">
<select data-field="x_Centro_de_Custo" id="x_Centro_de_Custo" name="x_Centro_de_Custo"<?php echo $financeiro->Centro_de_Custo->EditAttributes() ?>>
<?php
if (is_array($financeiro->Centro_de_Custo->EditValue)) {
	$arwrk = $financeiro->Centro_de_Custo->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($financeiro->Centro_de_Custo->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
ffinanceirosearch.Lists["x_Centro_de_Custo"].Options = <?php echo (is_array($financeiro->Centro_de_Custo->EditValue)) ? ew_ArrayToJson($financeiro->Centro_de_Custo->EditValue, 1) : "[]" ?>;
</script>
</span>
		</div></div>
	</div>
<?php } ?>
</div>
<?php if (!$financeiro_search->IsModal) { ?>
<div class="form-group">
	<div class="col-sm-offset-3 col-sm-9">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("Search") ?></button>
<button class="btn btn-default ewButton" name="btnReset" id="btnReset" type="button" onclick="ew_ClearForm(this.form);"><?php echo $Language->Phrase("Reset") ?></button>
	</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
ffinanceirosearch.Init();
</script>
<?php
$financeiro_search->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$financeiro_search->Page_Terminate();
?>
