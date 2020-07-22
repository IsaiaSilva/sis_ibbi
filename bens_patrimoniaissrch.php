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

$bens_patrimoniais_search = NULL; // Initialize page object first

class cbens_patrimoniais_search extends cbens_patrimoniais {

	// Page ID
	var $PageID = 'search';

	// Project ID
	var $ProjectID = "{2B7992FC-5911-46A7-9310-01F4D4225C49}";

	// Table name
	var $TableName = 'bens_patrimoniais';

	// Page object name
	var $PageObjName = 'bens_patrimoniais_search';

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
			define("EW_PAGE_ID", 'search', TRUE);

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
		if (!$Security->CanSearch()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate(ew_GetUrl("bens_patrimoniaislist.php"));
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
						$sSrchStr = "bens_patrimoniaislist.php" . "?" . $sSrchStr;
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
		$this->BuildSearchUrl($sSrchUrl, $this->Localidade); // Localidade
		$this->BuildSearchUrl($sSrchUrl, $this->Descricao); // Descricao
		$this->BuildSearchUrl($sSrchUrl, $this->DataAquisao); // DataAquisao
		$this->BuildSearchUrl($sSrchUrl, $this->Tipo); // Tipo
		$this->BuildSearchUrl($sSrchUrl, $this->Estado_do_bem); // Estado_do_bem
		$this->BuildSearchUrl($sSrchUrl, $this->Valor_estimado); // Valor_estimado
		$this->BuildSearchUrl($sSrchUrl, $this->Situacao); // Situacao
		$this->BuildSearchUrl($sSrchUrl, $this->Anotacoes); // Anotacoes
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
		// Localidade

		$this->Localidade->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Localidade"));
		$this->Localidade->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Localidade");

		// Descricao
		$this->Descricao->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Descricao"));
		$this->Descricao->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Descricao");

		// DataAquisao
		$this->DataAquisao->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_DataAquisao"));
		$this->DataAquisao->AdvancedSearch->SearchOperator = $objForm->GetValue("z_DataAquisao");

		// Tipo
		$this->Tipo->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Tipo"));
		$this->Tipo->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Tipo");

		// Estado_do_bem
		$this->Estado_do_bem->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Estado_do_bem"));
		$this->Estado_do_bem->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Estado_do_bem");

		// Valor_estimado
		$this->Valor_estimado->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Valor_estimado"));
		$this->Valor_estimado->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Valor_estimado");

		// Situacao
		$this->Situacao->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Situacao"));
		$this->Situacao->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Situacao");

		// Anotacoes
		$this->Anotacoes->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Anotacoes"));
		$this->Anotacoes->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Anotacoes");
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

			// Anotacoes
			$this->Anotacoes->LinkCustomAttributes = "";
			$this->Anotacoes->HrefValue = "";
			$this->Anotacoes->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// Localidade
			$this->Localidade->EditAttrs["class"] = "form-control";
			$this->Localidade->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `Id_igreja`, `Igreja` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `igrejas`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Localidade, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `Igreja` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->Localidade->EditValue = $arwrk;

			// Descricao
			$this->Descricao->EditAttrs["class"] = "form-control";
			$this->Descricao->EditCustomAttributes = "";
			$this->Descricao->EditValue = ew_HtmlEncode($this->Descricao->AdvancedSearch->SearchValue);

			// DataAquisao
			$this->DataAquisao->EditAttrs["class"] = "form-control";
			$this->DataAquisao->EditCustomAttributes = "";
			$this->DataAquisao->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->DataAquisao->AdvancedSearch->SearchValue, 7), 7));

			// Tipo
			$this->Tipo->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->Tipo->FldTagValue(1), $this->Tipo->FldTagCaption(1) <> "" ? $this->Tipo->FldTagCaption(1) : $this->Tipo->FldTagValue(1));
			$arwrk[] = array($this->Tipo->FldTagValue(2), $this->Tipo->FldTagCaption(2) <> "" ? $this->Tipo->FldTagCaption(2) : $this->Tipo->FldTagValue(2));
			$this->Tipo->EditValue = $arwrk;

			// Estado_do_bem
			$this->Estado_do_bem->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `Id_est_patri`, `Estado_do_Bem` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `estado_patrimonio`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Estado_do_bem, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->Estado_do_bem->EditValue = $arwrk;

			// Valor_estimado
			$this->Valor_estimado->EditAttrs["class"] = "form-control";
			$this->Valor_estimado->EditCustomAttributes = "";
			$this->Valor_estimado->EditValue = ew_HtmlEncode($this->Valor_estimado->AdvancedSearch->SearchValue);

			// Situacao
			$this->Situacao->EditAttrs["class"] = "form-control";
			$this->Situacao->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->Situacao->FldTagValue(1), $this->Situacao->FldTagCaption(1) <> "" ? $this->Situacao->FldTagCaption(1) : $this->Situacao->FldTagValue(1));
			$arwrk[] = array($this->Situacao->FldTagValue(2), $this->Situacao->FldTagCaption(2) <> "" ? $this->Situacao->FldTagCaption(2) : $this->Situacao->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->Situacao->EditValue = $arwrk;

			// Anotacoes
			$this->Anotacoes->EditAttrs["class"] = "form-control";
			$this->Anotacoes->EditCustomAttributes = "";
			$this->Anotacoes->EditValue = ew_HtmlEncode($this->Anotacoes->AdvancedSearch->SearchValue);
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
		if (!ew_CheckEuroDate($this->DataAquisao->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->DataAquisao->FldErrMsg());
		}
		if (!ew_CheckNumber($this->Valor_estimado->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->Valor_estimado->FldErrMsg());
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
		$this->Localidade->AdvancedSearch->Load();
		$this->Descricao->AdvancedSearch->Load();
		$this->DataAquisao->AdvancedSearch->Load();
		$this->Tipo->AdvancedSearch->Load();
		$this->Estado_do_bem->AdvancedSearch->Load();
		$this->Valor_estimado->AdvancedSearch->Load();
		$this->Situacao->AdvancedSearch->Load();
		$this->Anotacoes->AdvancedSearch->Load();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, "bens_patrimoniaislist.php", "", $this->TableVar, TRUE);
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
if (!isset($bens_patrimoniais_search)) $bens_patrimoniais_search = new cbens_patrimoniais_search();

// Page init
$bens_patrimoniais_search->Page_Init();

// Page main
$bens_patrimoniais_search->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$bens_patrimoniais_search->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var bens_patrimoniais_search = new ew_Page("bens_patrimoniais_search");
bens_patrimoniais_search.PageID = "search"; // Page ID
var EW_PAGE_ID = bens_patrimoniais_search.PageID; // For backward compatibility

// Form object
var fbens_patrimoniaissearch = new ew_Form("fbens_patrimoniaissearch");

// Form_CustomValidate event
fbens_patrimoniaissearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fbens_patrimoniaissearch.ValidateRequired = true;
<?php } else { ?>
fbens_patrimoniaissearch.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fbens_patrimoniaissearch.Lists["x_Localidade"] = {"LinkField":"x_Id_igreja","Ajax":null,"AutoFill":false,"DisplayFields":["x_Igreja","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fbens_patrimoniaissearch.Lists["x_Estado_do_bem"] = {"LinkField":"x_Id_est_patri","Ajax":null,"AutoFill":false,"DisplayFields":["x_Estado_do_Bem","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
// Validate function for search

fbens_patrimoniaissearch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	this.PostAutoSuggest();
	var infix = "";
	elm = this.GetElements("x" + infix + "_DataAquisao");
	if (elm && !ew_CheckEuroDate(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($bens_patrimoniais->DataAquisao->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_Valor_estimado");
	if (elm && !ew_CheckNumber(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($bens_patrimoniais->Valor_estimado->FldErrMsg()) ?>");

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
<?php if (!$bens_patrimoniais_search->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $bens_patrimoniais_search->ShowPageHeader(); ?>
<?php
$bens_patrimoniais_search->ShowMessage();
?>
<form name="fbens_patrimoniaissearch" id="fbens_patrimoniaissearch" class="form-horizontal ewForm ewSearchForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($bens_patrimoniais_search->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $bens_patrimoniais_search->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="bens_patrimoniais">
<input type="hidden" name="a_search" id="a_search" value="S">
<?php if ($bens_patrimoniais_search->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<div>
<?php if ($bens_patrimoniais->Localidade->Visible) { // Localidade ?>
	<div id="r_Localidade" class="form-group">
		<label for="x_Localidade" class="<?php echo $bens_patrimoniais_search->SearchLabelClass ?>"><span id="elh_bens_patrimoniais_Localidade"><?php echo $bens_patrimoniais->Localidade->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Localidade" id="z_Localidade" value="LIKE"></p>
		</label>
		<div class="<?php echo $bens_patrimoniais_search->SearchRightColumnClass ?>"><div<?php echo $bens_patrimoniais->Localidade->CellAttributes() ?>>
			<span id="el_bens_patrimoniais_Localidade">
<select data-field="x_Localidade" id="x_Localidade" name="x_Localidade"<?php echo $bens_patrimoniais->Localidade->EditAttributes() ?>>
<?php
if (is_array($bens_patrimoniais->Localidade->EditValue)) {
	$arwrk = $bens_patrimoniais->Localidade->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($bens_patrimoniais->Localidade->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
fbens_patrimoniaissearch.Lists["x_Localidade"].Options = <?php echo (is_array($bens_patrimoniais->Localidade->EditValue)) ? ew_ArrayToJson($bens_patrimoniais->Localidade->EditValue, 1) : "[]" ?>;
</script>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($bens_patrimoniais->Descricao->Visible) { // Descricao ?>
	<div id="r_Descricao" class="form-group">
		<label for="x_Descricao" class="<?php echo $bens_patrimoniais_search->SearchLabelClass ?>"><span id="elh_bens_patrimoniais_Descricao"><?php echo $bens_patrimoniais->Descricao->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Descricao" id="z_Descricao" value="LIKE"></p>
		</label>
		<div class="<?php echo $bens_patrimoniais_search->SearchRightColumnClass ?>"><div<?php echo $bens_patrimoniais->Descricao->CellAttributes() ?>>
			<span id="el_bens_patrimoniais_Descricao">
<input type="text" data-field="x_Descricao" name="x_Descricao" id="x_Descricao" size="70" maxlength="80" value="<?php echo $bens_patrimoniais->Descricao->EditValue ?>"<?php echo $bens_patrimoniais->Descricao->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($bens_patrimoniais->DataAquisao->Visible) { // DataAquisao ?>
	<div id="r_DataAquisao" class="form-group">
		<label for="x_DataAquisao" class="<?php echo $bens_patrimoniais_search->SearchLabelClass ?>"><span id="elh_bens_patrimoniais_DataAquisao"><?php echo $bens_patrimoniais->DataAquisao->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_DataAquisao" id="z_DataAquisao" value="="></p>
		</label>
		<div class="<?php echo $bens_patrimoniais_search->SearchRightColumnClass ?>"><div<?php echo $bens_patrimoniais->DataAquisao->CellAttributes() ?>>
			<span id="el_bens_patrimoniais_DataAquisao">
<input type="text" data-field="x_DataAquisao" name="x_DataAquisao" id="x_DataAquisao" size="14" value="<?php echo $bens_patrimoniais->DataAquisao->EditValue ?>"<?php echo $bens_patrimoniais->DataAquisao->EditAttributes() ?>>
<?php if (!$bens_patrimoniais->DataAquisao->ReadOnly && !$bens_patrimoniais->DataAquisao->Disabled && @$bens_patrimoniais->DataAquisao->EditAttrs["readonly"] == "" && @$bens_patrimoniais->DataAquisao->EditAttrs["disabled"] == "") { ?>
<script type="text/javascript">
ew_CreateCalendar("fbens_patrimoniaissearch", "x_DataAquisao", "%d/%m/%Y");
</script>
<?php } ?>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($bens_patrimoniais->Tipo->Visible) { // Tipo ?>
	<div id="r_Tipo" class="form-group">
		<label class="<?php echo $bens_patrimoniais_search->SearchLabelClass ?>"><span id="elh_bens_patrimoniais_Tipo"><?php echo $bens_patrimoniais->Tipo->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Tipo" id="z_Tipo" value="="></p>
		</label>
		<div class="<?php echo $bens_patrimoniais_search->SearchRightColumnClass ?>"><div<?php echo $bens_patrimoniais->Tipo->CellAttributes() ?>>
			<span id="el_bens_patrimoniais_Tipo">
<div id="tp_x_Tipo" class="<?php echo EW_ITEM_TEMPLATE_CLASSNAME ?>"><input type="radio" name="x_Tipo" id="x_Tipo" value="{value}"<?php echo $bens_patrimoniais->Tipo->EditAttributes() ?>></div>
<div id="dsl_x_Tipo" data-repeatcolumn="5" class="ewItemList">
<?php
$arwrk = $bens_patrimoniais->Tipo->EditValue;
if (is_array($arwrk)) {
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($bens_patrimoniais->Tipo->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " checked=\"checked\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;

		// Note: No spacing within the LABEL tag
?>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 1) ?>
<label class="radio-inline"><input type="radio" data-field="x_Tipo" name="x_Tipo" id="x_Tipo_<?php echo $rowcntwrk ?>" value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?><?php echo $bens_patrimoniais->Tipo->EditAttributes() ?>><?php echo $arwrk[$rowcntwrk][1] ?></label>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 2) ?>
<?php
	}
}
?>
</div>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($bens_patrimoniais->Estado_do_bem->Visible) { // Estado_do_bem ?>
	<div id="r_Estado_do_bem" class="form-group">
		<label class="<?php echo $bens_patrimoniais_search->SearchLabelClass ?>"><span id="elh_bens_patrimoniais_Estado_do_bem"><?php echo $bens_patrimoniais->Estado_do_bem->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Estado_do_bem" id="z_Estado_do_bem" value="="></p>
		</label>
		<div class="<?php echo $bens_patrimoniais_search->SearchRightColumnClass ?>"><div<?php echo $bens_patrimoniais->Estado_do_bem->CellAttributes() ?>>
			<span id="el_bens_patrimoniais_Estado_do_bem">
<div id="tp_x_Estado_do_bem" class="<?php echo EW_ITEM_TEMPLATE_CLASSNAME ?>"><input type="radio" name="x_Estado_do_bem" id="x_Estado_do_bem" value="{value}"<?php echo $bens_patrimoniais->Estado_do_bem->EditAttributes() ?>></div>
<div id="dsl_x_Estado_do_bem" data-repeatcolumn="5" class="ewItemList">
<?php
$arwrk = $bens_patrimoniais->Estado_do_bem->EditValue;
if (is_array($arwrk)) {
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($bens_patrimoniais->Estado_do_bem->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " checked=\"checked\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;

		// Note: No spacing within the LABEL tag
?>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 1) ?>
<label class="radio-inline"><input type="radio" data-field="x_Estado_do_bem" name="x_Estado_do_bem" id="x_Estado_do_bem_<?php echo $rowcntwrk ?>" value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?><?php echo $bens_patrimoniais->Estado_do_bem->EditAttributes() ?>><?php echo $arwrk[$rowcntwrk][1] ?></label>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 2) ?>
<?php
	}
}
?>
</div>
<script type="text/javascript">
fbens_patrimoniaissearch.Lists["x_Estado_do_bem"].Options = <?php echo (is_array($bens_patrimoniais->Estado_do_bem->EditValue)) ? ew_ArrayToJson($bens_patrimoniais->Estado_do_bem->EditValue, 0) : "[]" ?>;
</script>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($bens_patrimoniais->Valor_estimado->Visible) { // Valor_estimado ?>
	<div id="r_Valor_estimado" class="form-group">
		<label for="x_Valor_estimado" class="<?php echo $bens_patrimoniais_search->SearchLabelClass ?>"><span id="elh_bens_patrimoniais_Valor_estimado"><?php echo $bens_patrimoniais->Valor_estimado->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Valor_estimado" id="z_Valor_estimado" value="="></p>
		</label>
		<div class="<?php echo $bens_patrimoniais_search->SearchRightColumnClass ?>"><div<?php echo $bens_patrimoniais->Valor_estimado->CellAttributes() ?>>
			<span id="el_bens_patrimoniais_Valor_estimado">
<input type="text" data-field="x_Valor_estimado" name="x_Valor_estimado" id="x_Valor_estimado" size="15" value="<?php echo $bens_patrimoniais->Valor_estimado->EditValue ?>"<?php echo $bens_patrimoniais->Valor_estimado->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($bens_patrimoniais->Situacao->Visible) { // Situacao ?>
	<div id="r_Situacao" class="form-group">
		<label for="x_Situacao" class="<?php echo $bens_patrimoniais_search->SearchLabelClass ?>"><span id="elh_bens_patrimoniais_Situacao"><?php echo $bens_patrimoniais->Situacao->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Situacao" id="z_Situacao" value="="></p>
		</label>
		<div class="<?php echo $bens_patrimoniais_search->SearchRightColumnClass ?>"><div<?php echo $bens_patrimoniais->Situacao->CellAttributes() ?>>
			<span id="el_bens_patrimoniais_Situacao">
<select data-field="x_Situacao" id="x_Situacao" name="x_Situacao"<?php echo $bens_patrimoniais->Situacao->EditAttributes() ?>>
<?php
if (is_array($bens_patrimoniais->Situacao->EditValue)) {
	$arwrk = $bens_patrimoniais->Situacao->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($bens_patrimoniais->Situacao->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php if ($bens_patrimoniais->Anotacoes->Visible) { // Anotacoes ?>
	<div id="r_Anotacoes" class="form-group">
		<label for="x_Anotacoes" class="<?php echo $bens_patrimoniais_search->SearchLabelClass ?>"><span id="elh_bens_patrimoniais_Anotacoes"><?php echo $bens_patrimoniais->Anotacoes->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Anotacoes" id="z_Anotacoes" value="LIKE"></p>
		</label>
		<div class="<?php echo $bens_patrimoniais_search->SearchRightColumnClass ?>"><div<?php echo $bens_patrimoniais->Anotacoes->CellAttributes() ?>>
			<span id="el_bens_patrimoniais_Anotacoes">
<input type="text" data-field="x_Anotacoes" name="x_Anotacoes" id="x_Anotacoes" size="60" value="<?php echo $bens_patrimoniais->Anotacoes->EditValue ?>"<?php echo $bens_patrimoniais->Anotacoes->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
</div>
<?php if (!$bens_patrimoniais_search->IsModal) { ?>
<div class="form-group">
	<div class="col-sm-offset-3 col-sm-9">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("Search") ?></button>
<button class="btn btn-default ewButton" name="btnReset" id="btnReset" type="button" onclick="ew_ClearForm(this.form);"><?php echo $Language->Phrase("Reset") ?></button>
	</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fbens_patrimoniaissearch.Init();
</script>
<?php
$bens_patrimoniais_search->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$bens_patrimoniais_search->Page_Terminate();
?>
