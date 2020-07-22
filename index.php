<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg11.php" ?>
<?php include_once "ewmysql11.php" ?>
<?php include_once "phpfn11.php" ?>
<?php include_once "usuariosinfo.php" ?>
<?php include_once "userfn11.php" ?>
<?php

//
// Page class
//

$default = NULL; // Initialize page object first

class cdefault {

	// Page ID
	var $PageID = 'default';

	// Project ID
	var $ProjectID = "{2B7992FC-5911-46A7-9310-01F4D4225C49}";

	// Page object name
	var $PageObjName = 'default';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
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

		// User table object (usuarios)
		if (!isset($GLOBALS["UserTable"])) $GLOBALS["UserTable"] = new cusuarios();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'default', TRUE);

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

	//
	// Page main
	//
	function Page_Main() {
		global $Security, $Language;
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
		$Security->LoadUserLevel(); // Load User Level
		if ($Security->AllowList(CurrentProjectID() . 'agenda'))
		$this->Page_Terminate("agendalist.php"); // Exit and go to default page
		if ($Security->AllowList(CurrentProjectID() . 'Agenda_Morta'))
			$this->Page_Terminate("agenda_mortalist.php");
		if ($Security->AllowList(CurrentProjectID() . 'ajuda'))
			$this->Page_Terminate("ajudalist.php");
		if ($Security->AllowList(CurrentProjectID() . 'Aniversariantes'))
			$this->Page_Terminate("aniversarianteslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'audittrail'))
			$this->Page_Terminate("audittraillist.php");
		if ($Security->AllowList(CurrentProjectID() . 'bancos'))
			$this->Page_Terminate("bancoslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'bens_patrimoniais'))
			$this->Page_Terminate("bens_patrimoniaislist.php");
		if ($Security->AllowList(CurrentProjectID() . 'caixadodia'))
			$this->Page_Terminate("caixadodialist.php");
		if ($Security->AllowList(CurrentProjectID() . 'cargoscelulas'))
			$this->Page_Terminate("cargoscelulaslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'cargosministeriais'))
			$this->Page_Terminate("cargosministeriaislist.php");
		if ($Security->AllowList(CurrentProjectID() . 'cartas'))
			$this->Page_Terminate("cartaslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'celulas'))
			$this->Page_Terminate("celulaslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'conta_bancaria'))
			$this->Page_Terminate("conta_bancarialist.php");
		if ($Security->AllowList(CurrentProjectID() . 'contatos'))
			$this->Page_Terminate("contatoslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'controle_tarefas'))
			$this->Page_Terminate("controle_tarefaslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'dizimos'))
			$this->Page_Terminate("dizimoslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'dizimosmesatual'))
			$this->Page_Terminate("dizimosmesatuallist.php");
		if ($Security->AllowList(CurrentProjectID() . 'dizimosporcriterio'))
			$this->Page_Terminate("dizimosporcriteriolist.php");
		if ($Security->AllowList(CurrentProjectID() . 'escolaridade'))
			$this->Page_Terminate("escolaridadelist.php");
		if ($Security->AllowList(CurrentProjectID() . 'estudos_biblicos'))
			$this->Page_Terminate("estudos_biblicoslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'EtiquetasMalaDireta'))
			$this->Page_Terminate("etiquetasmaladiretalist.php");
		if ($Security->AllowList(CurrentProjectID() . 'eventos'))
			$this->Page_Terminate("eventoslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'fin_centro_de_custo'))
			$this->Page_Terminate("fin_centro_de_custolist.php");
		if ($Security->AllowList(CurrentProjectID() . 'fin_conta_caixa'))
			$this->Page_Terminate("fin_conta_caixalist.php");
		if ($Security->AllowList(CurrentProjectID() . 'fin_forma_pgto'))
			$this->Page_Terminate("fin_forma_pgtolist.php");
		if ($Security->AllowList(CurrentProjectID() . 'financeiro'))
			$this->Page_Terminate("financeirolist.php");
		if ($Security->AllowList(CurrentProjectID() . 'funcionarios'))
			$this->Page_Terminate("funcionarioslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'funcoes_exerce'))
			$this->Page_Terminate("funcoes_exercelist.php");
		if ($Security->AllowList(CurrentProjectID() . 'igrejas'))
			$this->Page_Terminate("igrejaslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'Lista_de_Emails'))
			$this->Page_Terminate("lista_de_emailslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'lista_videos'))
			$this->Page_Terminate("lista_videoslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'ListadeEmailporCargo'))
			$this->Page_Terminate("listadeemailporcargoreport.php");
		if ($Security->AllowList(CurrentProjectID() . 'ListaEmailsporFuncao'))
			$this->Page_Terminate("listaemailsporfuncaoreport.php");
		if ($Security->AllowList(CurrentProjectID() . 'membro'))
			$this->Page_Terminate("membrolist.php");
		if ($Security->AllowList(CurrentProjectID() . 'modelo_igreja'))
			$this->Page_Terminate("modelo_igrejalist.php");
		if ($Security->AllowList(CurrentProjectID() . 'ofertasporcriterio'))
			$this->Page_Terminate("ofertasporcriteriolist.php");
		if ($Security->AllowList(CurrentProjectID() . 'ofertassmesatual'))
			$this->Page_Terminate("ofertassmesatuallist.php");
		if ($Security->AllowList(CurrentProjectID() . 'plano_oracao'))
			$this->Page_Terminate("plano_oracaolist.php");
		if ($Security->AllowList(CurrentProjectID() . 'print_cartaexclusao'))
			$this->Page_Terminate("print_cartaexclusaolist.php");
		if ($Security->AllowList(CurrentProjectID() . 'print_cartaoficio'))
			$this->Page_Terminate("print_cartaoficiolist.php");
		if ($Security->AllowList(CurrentProjectID() . 'print_cartarecomendacao'))
			$this->Page_Terminate("print_cartarecomendacaolist.php");
		if ($Security->AllowList(CurrentProjectID() . 'print_transferencia'))
			$this->Page_Terminate("print_transferencialist.php");
		if ($Security->AllowList(CurrentProjectID() . 'rede_ministerial'))
			$this->Page_Terminate("rede_ministeriallist.php");
		if ($Security->AllowList(CurrentProjectID() . 'RelatorioAdmissao'))
			$this->Page_Terminate("relatorioadmissaolist.php");
		if ($Security->AllowList(CurrentProjectID() . 'RelatorioCasamento'))
			$this->Page_Terminate("relatoriocasamentolist.php");
		if ($Security->AllowList(CurrentProjectID() . 'RelatoriodataBatismo'))
			$this->Page_Terminate("relatoriodatabatismolist.php");
		if ($Security->AllowList(CurrentProjectID() . 'situacao_membro'))
			$this->Page_Terminate("situacao_membrolist.php");
		if ($Security->AllowList(CurrentProjectID() . 'smtp'))
			$this->Page_Terminate("smtplist.php");
		if ($Security->AllowList(CurrentProjectID() . 'tipo_admissao'))
			$this->Page_Terminate("tipo_admissaolist.php");
		if ($Security->AllowList(CurrentProjectID() . 'userlevels'))
			$this->Page_Terminate("userlevelslist.php");
		if ($Security->AllowList(CurrentProjectID() . 'usuarios'))
			$this->Page_Terminate("usuarioslist.php");
		if ($Security->IsLoggedIn()) {
			$this->setFailureMessage($Language->Phrase("NoPermission") . "<br><br><a href=\"logout.php\">" . $Language->Phrase("BackToLogin") . "</a>");
		} else {
			$this->Page_Terminate("login.php"); // Exit and go to login page
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
	// $type = ''|'success'|'failure'
	function Message_Showing(&$msg, $type) {

		// Example:
		//if ($type == 'success') $msg = "your success message";

	}
}
?>
<?php ew_Header(TRUE) ?>
<?php

// Create page object
if (!isset($default)) $default = new cdefault();

// Page init
$default->Page_Init();

// Page main
$default->Page_Main();
?>
<?php include_once "header.php" ?>
<?php
$default->ShowMessage();
?>
<?php include_once "footer.php" ?>
<?php
$default->Page_Terminate();
?>
