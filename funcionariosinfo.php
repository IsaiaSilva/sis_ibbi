<?php

// Global variable for table object
$funcionarios = NULL;

//
// Table class for funcionarios
//
class cfuncionarios extends cTable {
	var $Id;
	var $EhMembro;
	var $Data_Admissao;
	var $Nome;
	var $Data_Nasc;
	var $Estado_Civil;
	var $Endereco;
	var $Bairro;
	var $Cidade;
	var $UF;
	var $CEP;
	var $Celular;
	var $Telefone_Fixo;
	var $_Email;
	var $Cargo;
	var $Setor;
	var $CPF;
	var $RG;
	var $Org_Exp;
	var $Data_Expedicao;
	var $CTPS_N;
	var $CTPS_Serie;
	var $Titulo_Eleitor;
	var $Numero_Filhos;
	var $Escolaridade;
	var $Situacao;
	var $Qual_ano;
	var $Observacoes;
	var $Inativo;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'funcionarios';
		$this->TableName = 'funcionarios';
		$this->TableType = 'TABLE';
		$this->ExportAll = TRUE;
		$this->ExportPageBreakCount = 0; // Page break per every n record (PDF only)
		$this->ExportPageOrientation = "portrait"; // Page orientation (PDF only)
		$this->ExportPageSize = "a4"; // Page size (PDF only)
		$this->ExportExcelPageOrientation = ""; // Page orientation (PHPExcel only)
		$this->ExportExcelPageSize = ""; // Page size (PHPExcel only)
		$this->DetailAdd = FALSE; // Allow detail add
		$this->DetailEdit = FALSE; // Allow detail edit
		$this->DetailView = FALSE; // Allow detail view
		$this->ShowMultipleDetails = FALSE; // Show multiple details
		$this->GridAddRowCount = 5;
		$this->AllowAddDeleteRow = ew_AllowAddDeleteRow(); // Allow add/delete row
		$this->UserIDAllowSecurity = 0; // User ID Allow
		$this->BasicSearch = new cBasicSearch($this->TableVar);

		// Id
		$this->Id = new cField('funcionarios', 'funcionarios', 'x_Id', 'Id', '`Id`', '`Id`', 3, -1, FALSE, '`Id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Id->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Id'] = &$this->Id;

		// EhMembro
		$this->EhMembro = new cField('funcionarios', 'funcionarios', 'x_EhMembro', 'EhMembro', '`EhMembro`', '`EhMembro`', 16, -1, FALSE, '`EhMembro`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->EhMembro->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['EhMembro'] = &$this->EhMembro;

		// Data_Admissao
		$this->Data_Admissao = new cField('funcionarios', 'funcionarios', 'x_Data_Admissao', 'Data_Admissao', '`Data_Admissao`', 'DATE_FORMAT(`Data_Admissao`, \'%d/%m/%Y\')', 133, 7, FALSE, '`Data_Admissao`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Data_Admissao->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateDMY"));
		$this->fields['Data_Admissao'] = &$this->Data_Admissao;

		// Nome
		$this->Nome = new cField('funcionarios', 'funcionarios', 'x_Nome', 'Nome', '`Nome`', '`Nome`', 200, -1, FALSE, '`Nome`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Nome'] = &$this->Nome;

		// Data_Nasc
		$this->Data_Nasc = new cField('funcionarios', 'funcionarios', 'x_Data_Nasc', 'Data_Nasc', '`Data_Nasc`', 'DATE_FORMAT(`Data_Nasc`, \'%d/%m/%Y\')', 133, 7, FALSE, '`Data_Nasc`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Data_Nasc->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateDMY"));
		$this->fields['Data_Nasc'] = &$this->Data_Nasc;

		// Estado_Civil
		$this->Estado_Civil = new cField('funcionarios', 'funcionarios', 'x_Estado_Civil', 'Estado_Civil', '`Estado_Civil`', '`Estado_Civil`', 202, -1, FALSE, '`Estado_Civil`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Estado_Civil'] = &$this->Estado_Civil;

		// Endereco
		$this->Endereco = new cField('funcionarios', 'funcionarios', 'x_Endereco', 'Endereco', '`Endereco`', '`Endereco`', 200, -1, FALSE, '`Endereco`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Endereco'] = &$this->Endereco;

		// Bairro
		$this->Bairro = new cField('funcionarios', 'funcionarios', 'x_Bairro', 'Bairro', '`Bairro`', '`Bairro`', 200, -1, FALSE, '`Bairro`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Bairro'] = &$this->Bairro;

		// Cidade
		$this->Cidade = new cField('funcionarios', 'funcionarios', 'x_Cidade', 'Cidade', '`Cidade`', '`Cidade`', 200, -1, FALSE, '`Cidade`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Cidade'] = &$this->Cidade;

		// UF
		$this->UF = new cField('funcionarios', 'funcionarios', 'x_UF', 'UF', '`UF`', '`UF`', 200, -1, FALSE, '`UF`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['UF'] = &$this->UF;

		// CEP
		$this->CEP = new cField('funcionarios', 'funcionarios', 'x_CEP', 'CEP', '`CEP`', '`CEP`', 200, -1, FALSE, '`CEP`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['CEP'] = &$this->CEP;

		// Celular
		$this->Celular = new cField('funcionarios', 'funcionarios', 'x_Celular', 'Celular', '`Celular`', '`Celular`', 200, -1, FALSE, '`Celular`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Celular'] = &$this->Celular;

		// Telefone Fixo
		$this->Telefone_Fixo = new cField('funcionarios', 'funcionarios', 'x_Telefone_Fixo', 'Telefone Fixo', '`Telefone Fixo`', '`Telefone Fixo`', 200, -1, FALSE, '`Telefone Fixo`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Telefone Fixo'] = &$this->Telefone_Fixo;

		// Email
		$this->_Email = new cField('funcionarios', 'funcionarios', 'x__Email', 'Email', '`Email`', '`Email`', 200, -1, FALSE, '`Email`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Email'] = &$this->_Email;

		// Cargo
		$this->Cargo = new cField('funcionarios', 'funcionarios', 'x_Cargo', 'Cargo', '`Cargo`', '`Cargo`', 200, -1, FALSE, '`Cargo`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Cargo'] = &$this->Cargo;

		// Setor
		$this->Setor = new cField('funcionarios', 'funcionarios', 'x_Setor', 'Setor', '`Setor`', '`Setor`', 200, -1, FALSE, '`Setor`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Setor'] = &$this->Setor;

		// CPF
		$this->CPF = new cField('funcionarios', 'funcionarios', 'x_CPF', 'CPF', '`CPF`', '`CPF`', 200, -1, FALSE, '`CPF`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['CPF'] = &$this->CPF;

		// RG
		$this->RG = new cField('funcionarios', 'funcionarios', 'x_RG', 'RG', '`RG`', '`RG`', 200, -1, FALSE, '`RG`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['RG'] = &$this->RG;

		// Org_Exp
		$this->Org_Exp = new cField('funcionarios', 'funcionarios', 'x_Org_Exp', 'Org_Exp', '`Org_Exp`', '`Org_Exp`', 200, -1, FALSE, '`Org_Exp`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Org_Exp'] = &$this->Org_Exp;

		// Data_Expedicao
		$this->Data_Expedicao = new cField('funcionarios', 'funcionarios', 'x_Data_Expedicao', 'Data_Expedicao', '`Data_Expedicao`', 'DATE_FORMAT(`Data_Expedicao`, \'%d/%m/%Y\')', 133, 7, FALSE, '`Data_Expedicao`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Data_Expedicao->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateDMY"));
		$this->fields['Data_Expedicao'] = &$this->Data_Expedicao;

		// CTPS_N
		$this->CTPS_N = new cField('funcionarios', 'funcionarios', 'x_CTPS_N', 'CTPS_N', '`CTPS_N`', '`CTPS_N`', 200, -1, FALSE, '`CTPS_N`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['CTPS_N'] = &$this->CTPS_N;

		// CTPS_Serie
		$this->CTPS_Serie = new cField('funcionarios', 'funcionarios', 'x_CTPS_Serie', 'CTPS_Serie', '`CTPS_Serie`', '`CTPS_Serie`', 200, -1, FALSE, '`CTPS_Serie`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['CTPS_Serie'] = &$this->CTPS_Serie;

		// Titulo_Eleitor
		$this->Titulo_Eleitor = new cField('funcionarios', 'funcionarios', 'x_Titulo_Eleitor', 'Titulo_Eleitor', '`Titulo_Eleitor`', '`Titulo_Eleitor`', 200, -1, FALSE, '`Titulo_Eleitor`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Titulo_Eleitor'] = &$this->Titulo_Eleitor;

		// Numero_Filhos
		$this->Numero_Filhos = new cField('funcionarios', 'funcionarios', 'x_Numero_Filhos', 'Numero_Filhos', '`Numero_Filhos`', '`Numero_Filhos`', 200, -1, FALSE, '`Numero_Filhos`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Numero_Filhos'] = &$this->Numero_Filhos;

		// Escolaridade
		$this->Escolaridade = new cField('funcionarios', 'funcionarios', 'x_Escolaridade', 'Escolaridade', '`Escolaridade`', '`Escolaridade`', 202, -1, FALSE, '`Escolaridade`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Escolaridade'] = &$this->Escolaridade;

		// Situacao
		$this->Situacao = new cField('funcionarios', 'funcionarios', 'x_Situacao', 'Situacao', '`Situacao`', '`Situacao`', 202, -1, FALSE, '`Situacao`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Situacao'] = &$this->Situacao;

		// Qual_ano
		$this->Qual_ano = new cField('funcionarios', 'funcionarios', 'x_Qual_ano', 'Qual_ano', '`Qual_ano`', '`Qual_ano`', 200, -1, FALSE, '`Qual_ano`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Qual_ano'] = &$this->Qual_ano;

		// Observacoes
		$this->Observacoes = new cField('funcionarios', 'funcionarios', 'x_Observacoes', 'Observacoes', '`Observacoes`', '`Observacoes`', 200, -1, FALSE, '`Observacoes`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Observacoes'] = &$this->Observacoes;

		// Inativo
		$this->Inativo = new cField('funcionarios', 'funcionarios', 'x_Inativo', 'Inativo', '`Inativo`', '`Inativo`', 16, -1, FALSE, '`Inativo`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Inativo->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Inativo'] = &$this->Inativo;
	}

	// Multiple column sort
	function UpdateSort(&$ofld, $ctrl) {
		if ($this->CurrentOrder == $ofld->FldName) {
			$sSortField = $ofld->FldExpression;
			$sLastSort = $ofld->getSort();
			if ($this->CurrentOrderType == "ASC" || $this->CurrentOrderType == "DESC") {
				$sThisSort = $this->CurrentOrderType;
			} else {
				$sThisSort = ($sLastSort == "ASC") ? "DESC" : "ASC";
			}
			$ofld->setSort($sThisSort);
			if ($ctrl) {
				$sOrderBy = $this->getSessionOrderBy();
				if (strpos($sOrderBy, $sSortField . " " . $sLastSort) !== FALSE) {
					$sOrderBy = str_replace($sSortField . " " . $sLastSort, $sSortField . " " . $sThisSort, $sOrderBy);
				} else {
					if ($sOrderBy <> "") $sOrderBy .= ", ";
					$sOrderBy .= $sSortField . " " . $sThisSort;
				}
				$this->setSessionOrderBy($sOrderBy); // Save to Session
			} else {
				$this->setSessionOrderBy($sSortField . " " . $sThisSort); // Save to Session
			}
		} else {
			if (!$ctrl) $ofld->setSort("");
		}
	}

	// Table level SQL
	var $_SqlFrom = "";

	function getSqlFrom() { // From
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`funcionarios`";
	}

	function SqlFrom() { // For backward compatibility
    	return $this->getSqlFrom();
	}

	function setSqlFrom($v) {
    	$this->_SqlFrom = $v;
	}
	var $_SqlSelect = "";

	function getSqlSelect() { // Select
		return ($this->_SqlSelect <> "") ? $this->_SqlSelect : "SELECT * FROM " . $this->getSqlFrom();
	}

	function SqlSelect() { // For backward compatibility
    	return $this->getSqlSelect();
	}

	function setSqlSelect($v) {
    	$this->_SqlSelect = $v;
	}
	var $_SqlWhere = "";

	function getSqlWhere() { // Where
		$sWhere = ($this->_SqlWhere <> "") ? $this->_SqlWhere : "";
		$this->TableFilter = "";
		ew_AddFilter($sWhere, $this->TableFilter);
		return $sWhere;
	}

	function SqlWhere() { // For backward compatibility
    	return $this->getSqlWhere();
	}

	function setSqlWhere($v) {
    	$this->_SqlWhere = $v;
	}
	var $_SqlGroupBy = "";

	function getSqlGroupBy() { // Group By
		return ($this->_SqlGroupBy <> "") ? $this->_SqlGroupBy : "";
	}

	function SqlGroupBy() { // For backward compatibility
    	return $this->getSqlGroupBy();
	}

	function setSqlGroupBy($v) {
    	$this->_SqlGroupBy = $v;
	}
	var $_SqlHaving = "";

	function getSqlHaving() { // Having
		return ($this->_SqlHaving <> "") ? $this->_SqlHaving : "";
	}

	function SqlHaving() { // For backward compatibility
    	return $this->getSqlHaving();
	}

	function setSqlHaving($v) {
    	$this->_SqlHaving = $v;
	}
	var $_SqlOrderBy = "";

	function getSqlOrderBy() { // Order By
		return ($this->_SqlOrderBy <> "") ? $this->_SqlOrderBy : "";
	}

	function SqlOrderBy() { // For backward compatibility
    	return $this->getSqlOrderBy();
	}

	function setSqlOrderBy($v) {
    	$this->_SqlOrderBy = $v;
	}

	// Check if Anonymous User is allowed
	function AllowAnonymousUser() {
		switch (@$this->PageID) {
			case "add":
			case "register":
			case "addopt":
				return FALSE;
			case "edit":
			case "update":
			case "changepwd":
			case "forgotpwd":
				return FALSE;
			case "delete":
				return FALSE;
			case "view":
				return FALSE;
			case "search":
				return FALSE;
			default:
				return FALSE;
		}
	}

	// Apply User ID filters
	function ApplyUserIDFilters($sFilter) {
		return $sFilter;
	}

	// Check if User ID security allows view all
	function UserIDAllow($id = "") {
		$allow = EW_USER_ID_ALLOW;
		switch ($id) {
			case "add":
			case "copy":
			case "gridadd":
			case "register":
			case "addopt":
				return (($allow & 1) == 1);
			case "edit":
			case "gridedit":
			case "update":
			case "changepwd":
			case "forgotpwd":
				return (($allow & 4) == 4);
			case "delete":
				return (($allow & 2) == 2);
			case "view":
				return (($allow & 32) == 32);
			case "search":
				return (($allow & 64) == 64);
			default:
				return (($allow & 8) == 8);
		}
	}

	// Get SQL
	function GetSQL($where, $orderby) {
		return ew_BuildSelectSql($this->getSqlSelect(), $this->getSqlWhere(),
			$this->getSqlGroupBy(), $this->getSqlHaving(), $this->getSqlOrderBy(),
			$where, $orderby);
	}

	// Table SQL
	function SQL() {
		$sFilter = $this->CurrentFilter;
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql($this->getSqlSelect(), $this->getSqlWhere(),
			$this->getSqlGroupBy(), $this->getSqlHaving(), $this->getSqlOrderBy(),
			$sFilter, $sSort);
	}

	// Table SQL with List page filter
	function SelectSQL() {
		$sFilter = $this->getSessionWhere();
		ew_AddFilter($sFilter, $this->CurrentFilter);
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$this->Recordset_Selecting($sFilter);
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql($this->getSqlSelect(), $this->getSqlWhere(), $this->getSqlGroupBy(),
			$this->getSqlHaving(), $this->getSqlOrderBy(), $sFilter, $sSort);
	}

	// Get ORDER BY clause
	function GetOrderBy() {
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql("", "", "", "", $this->getSqlOrderBy(), "", $sSort);
	}

	// Try to get record count
	function TryGetRecordCount($sSql) {
		global $conn;
		$cnt = -1;
		if ($this->TableType == 'TABLE' || $this->TableType == 'VIEW') {
			$sSql = "SELECT COUNT(*) FROM" . preg_replace('/^SELECT\s([\s\S]+)?\*\sFROM/i', "", $sSql);
			$sOrderBy = $this->GetOrderBy();
			if (substr($sSql, strlen($sOrderBy) * -1) == $sOrderBy)
				$sSql = substr($sSql, 0, strlen($sSql) - strlen($sOrderBy)); // Remove ORDER BY clause
		} else {
			$sSql = "SELECT COUNT(*) FROM (" . $sSql . ") EW_COUNT_TABLE";
		}
		if ($rs = $conn->Execute($sSql)) {
			if (!$rs->EOF && $rs->FieldCount() > 0) {
				$cnt = $rs->fields[0];
				$rs->Close();
			}
		}
		return intval($cnt);
	}

	// Get record count based on filter (for detail record count in master table pages)
	function LoadRecordCount($sFilter) {
		$origFilter = $this->CurrentFilter;
		$this->CurrentFilter = $sFilter;
		$this->Recordset_Selecting($this->CurrentFilter);

		//$sSql = $this->SQL();
		$sSql = $this->GetSQL($this->CurrentFilter, "");
		$cnt = $this->TryGetRecordCount($sSql);
		if ($cnt == -1) {
			if ($rs = $this->LoadRs($this->CurrentFilter)) {
				$cnt = $rs->RecordCount();
				$rs->Close();
			}
		}
		$this->CurrentFilter = $origFilter;
		return intval($cnt);
	}

	// Get record count (for current List page)
	function SelectRecordCount() {
		global $conn;
		$sSql = $this->SelectSQL();
		$cnt = $this->TryGetRecordCount($sSql);
		if ($cnt == -1) {
			if ($rs = $conn->Execute($sSql)) {
				$cnt = $rs->RecordCount();
				$rs->Close();
			}
		}
		return intval($cnt);
	}

	// Update Table
	var $UpdateTable = "`funcionarios`";

	// INSERT statement
	function InsertSQL(&$rs) {
		global $conn;
		$names = "";
		$values = "";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]))
				continue;
			$names .= $this->fields[$name]->FldExpression . ",";
			$values .= ew_QuotedValue($value, $this->fields[$name]->FldDataType) . ",";
		}
		while (substr($names, -1) == ",")
			$names = substr($names, 0, -1);
		while (substr($values, -1) == ",")
			$values = substr($values, 0, -1);
		return "INSERT INTO " . $this->UpdateTable . " ($names) VALUES ($values)";
	}

	// Insert
	function Insert(&$rs) {
		global $conn;
		return $conn->Execute($this->InsertSQL($rs));
	}

	// UPDATE statement
	function UpdateSQL(&$rs, $where = "") {
		$sql = "UPDATE " . $this->UpdateTable . " SET ";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]))
				continue;
			$sql .= $this->fields[$name]->FldExpression . "=";
			$sql .= ew_QuotedValue($value, $this->fields[$name]->FldDataType) . ",";
		}
		while (substr($sql, -1) == ",")
			$sql = substr($sql, 0, -1);
		$filter = $this->CurrentFilter;
		ew_AddFilter($filter, $where);
		if ($filter <> "")	$sql .= " WHERE " . $filter;
		return $sql;
	}

	// Update
	function Update(&$rs, $where = "", $rsold = NULL) {
		global $conn;
		return $conn->Execute($this->UpdateSQL($rs, $where));
	}

	// DELETE statement
	function DeleteSQL(&$rs, $where = "") {
		$sql = "DELETE FROM " . $this->UpdateTable . " WHERE ";
		if ($rs) {
			if (array_key_exists('Id', $rs))
				ew_AddFilter($where, ew_QuotedName('Id') . '=' . ew_QuotedValue($rs['Id'], $this->Id->FldDataType));
		}
		$filter = $this->CurrentFilter;
		ew_AddFilter($filter, $where);
		if ($filter <> "")
			$sql .= $filter;
		else
			$sql .= "0=1"; // Avoid delete
		return $sql;
	}

	// Delete
	function Delete(&$rs, $where = "") {
		global $conn;
		return $conn->Execute($this->DeleteSQL($rs, $where));
	}

	// Key filter WHERE clause
	function SqlKeyFilter() {
		return "`Id` = @Id@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->Id->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@Id@", ew_AdjustSql($this->Id->CurrentValue), $sKeyFilter); // Replace key value
		return $sKeyFilter;
	}

	// Return page URL
	function getReturnUrl() {
		$name = EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL;

		// Get referer URL automatically
		if (ew_ServerVar("HTTP_REFERER") <> "" && ew_ReferPage() <> ew_CurrentPage() && ew_ReferPage() <> "login.php") // Referer not same page or login page
			$_SESSION[$name] = ew_ServerVar("HTTP_REFERER"); // Save to Session
		if (@$_SESSION[$name] <> "") {
			return $_SESSION[$name];
		} else {
			return "funcionarioslist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "funcionarioslist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("funcionariosview.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("funcionariosview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Add URL
	function GetAddUrl($parm = "") {
		if ($parm <> "")
			return "funcionariosadd.php?" . $this->UrlParm($parm);
		else
			return "funcionariosadd.php";
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		return $this->KeyUrl("funcionariosedit.php", $this->UrlParm($parm));
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		return $this->KeyUrl("funcionariosadd.php", $this->UrlParm($parm));
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("funcionariosdelete.php", $this->UrlParm());
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->Id->CurrentValue)) {
			$sUrl .= "Id=" . urlencode($this->Id->CurrentValue);
		} else {
			return "javascript:alert(ewLanguage.Phrase('InvalidRecord'));";
		}
		return $sUrl;
	}

	// Sort URL
	function SortUrl(&$fld) {
		if ($this->CurrentAction <> "" || $this->Export <> "" ||
			in_array($fld->FldType, array(128, 204, 205))) { // Unsortable data type
				return "";
		} elseif ($fld->Sortable) {
			$sUrlParm = $this->UrlParm("order=" . urlencode($fld->FldName) . "&amp;ordertype=" . $fld->ReverseSort());
			return ew_CurrentPage() . "?" . $sUrlParm;
		} else {
			return "";
		}
	}

	// Get record keys from $_POST/$_GET/$_SESSION
	function GetRecordKeys() {
		global $EW_COMPOSITE_KEY_SEPARATOR;
		$arKeys = array();
		$arKey = array();
		if (isset($_POST["key_m"])) {
			$arKeys = ew_StripSlashes($_POST["key_m"]);
			$cnt = count($arKeys);
		} elseif (isset($_GET["key_m"])) {
			$arKeys = ew_StripSlashes($_GET["key_m"]);
			$cnt = count($arKeys);
		} elseif (isset($_GET)) {
			$arKeys[] = @$_GET["Id"]; // Id

			//return $arKeys; // Do not return yet, so the values will also be checked by the following code
		}

		// Check keys
		$ar = array();
		foreach ($arKeys as $key) {
			if (!is_numeric($key))
				continue;
			$ar[] = $key;
		}
		return $ar;
	}

	// Get key filter
	function GetKeyFilter() {
		$arKeys = $this->GetRecordKeys();
		$sKeyFilter = "";
		foreach ($arKeys as $key) {
			if ($sKeyFilter <> "") $sKeyFilter .= " OR ";
			$this->Id->CurrentValue = $key;
			$sKeyFilter .= "(" . $this->KeyFilter() . ")";
		}
		return $sKeyFilter;
	}

	// Load rows based on filter
	function &LoadRs($sFilter) {
		global $conn;

		// Set up filter (SQL WHERE clause) and get return SQL
		//$this->CurrentFilter = $sFilter;
		//$sSql = $this->SQL();

		$sSql = $this->GetSQL($sFilter, "");
		$rs = $conn->Execute($sSql);
		return $rs;
	}

	// Load row values from recordset
	function LoadListRowValues(&$rs) {
		$this->Id->setDbValue($rs->fields('Id'));
		$this->EhMembro->setDbValue($rs->fields('EhMembro'));
		$this->Data_Admissao->setDbValue($rs->fields('Data_Admissao'));
		$this->Nome->setDbValue($rs->fields('Nome'));
		$this->Data_Nasc->setDbValue($rs->fields('Data_Nasc'));
		$this->Estado_Civil->setDbValue($rs->fields('Estado_Civil'));
		$this->Endereco->setDbValue($rs->fields('Endereco'));
		$this->Bairro->setDbValue($rs->fields('Bairro'));
		$this->Cidade->setDbValue($rs->fields('Cidade'));
		$this->UF->setDbValue($rs->fields('UF'));
		$this->CEP->setDbValue($rs->fields('CEP'));
		$this->Celular->setDbValue($rs->fields('Celular'));
		$this->Telefone_Fixo->setDbValue($rs->fields('Telefone Fixo'));
		$this->_Email->setDbValue($rs->fields('Email'));
		$this->Cargo->setDbValue($rs->fields('Cargo'));
		$this->Setor->setDbValue($rs->fields('Setor'));
		$this->CPF->setDbValue($rs->fields('CPF'));
		$this->RG->setDbValue($rs->fields('RG'));
		$this->Org_Exp->setDbValue($rs->fields('Org_Exp'));
		$this->Data_Expedicao->setDbValue($rs->fields('Data_Expedicao'));
		$this->CTPS_N->setDbValue($rs->fields('CTPS_N'));
		$this->CTPS_Serie->setDbValue($rs->fields('CTPS_Serie'));
		$this->Titulo_Eleitor->setDbValue($rs->fields('Titulo_Eleitor'));
		$this->Numero_Filhos->setDbValue($rs->fields('Numero_Filhos'));
		$this->Escolaridade->setDbValue($rs->fields('Escolaridade'));
		$this->Situacao->setDbValue($rs->fields('Situacao'));
		$this->Qual_ano->setDbValue($rs->fields('Qual_ano'));
		$this->Observacoes->setDbValue($rs->fields('Observacoes'));
		$this->Inativo->setDbValue($rs->fields('Inativo'));
	}

	// Render list row values
	function RenderListRow() {
		global $conn, $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
		// Id

		$this->Id->CellCssStyle = "white-space: nowrap;";

		// EhMembro
		// Data_Admissao
		// Nome
		// Data_Nasc
		// Estado_Civil
		// Endereco
		// Bairro
		// Cidade
		// UF
		// CEP
		// Celular
		// Telefone Fixo
		// Email
		// Cargo
		// Setor
		// CPF
		// RG
		// Org_Exp
		// Data_Expedicao
		// CTPS_N
		// CTPS_Serie
		// Titulo_Eleitor
		// Numero_Filhos
		// Escolaridade
		// Situacao
		// Qual_ano
		// Observacoes
		// Inativo
		// Id

		$this->Id->ViewValue = $this->Id->CurrentValue;
		$this->Id->ViewCustomAttributes = "";

		// EhMembro
		if (strval($this->EhMembro->CurrentValue) <> "") {
			switch ($this->EhMembro->CurrentValue) {
				case $this->EhMembro->FldTagValue(1):
					$this->EhMembro->ViewValue = $this->EhMembro->FldTagCaption(1) <> "" ? $this->EhMembro->FldTagCaption(1) : $this->EhMembro->CurrentValue;
					break;
				case $this->EhMembro->FldTagValue(2):
					$this->EhMembro->ViewValue = $this->EhMembro->FldTagCaption(2) <> "" ? $this->EhMembro->FldTagCaption(2) : $this->EhMembro->CurrentValue;
					break;
				default:
					$this->EhMembro->ViewValue = $this->EhMembro->CurrentValue;
			}
		} else {
			$this->EhMembro->ViewValue = NULL;
		}
		$this->EhMembro->ViewCustomAttributes = "";

		// Data_Admissao
		$this->Data_Admissao->ViewValue = $this->Data_Admissao->CurrentValue;
		$this->Data_Admissao->ViewValue = ew_FormatDateTime($this->Data_Admissao->ViewValue, 7);
		$this->Data_Admissao->ViewCustomAttributes = "";

		// Nome
		$this->Nome->ViewValue = $this->Nome->CurrentValue;
		$this->Nome->ViewCustomAttributes = "";

		// Data_Nasc
		$this->Data_Nasc->ViewValue = $this->Data_Nasc->CurrentValue;
		$this->Data_Nasc->ViewValue = ew_FormatDateTime($this->Data_Nasc->ViewValue, 7);
		$this->Data_Nasc->ViewCustomAttributes = "";

		// Estado_Civil
		if (strval($this->Estado_Civil->CurrentValue) <> "") {
			switch ($this->Estado_Civil->CurrentValue) {
				case $this->Estado_Civil->FldTagValue(1):
					$this->Estado_Civil->ViewValue = $this->Estado_Civil->FldTagCaption(1) <> "" ? $this->Estado_Civil->FldTagCaption(1) : $this->Estado_Civil->CurrentValue;
					break;
				case $this->Estado_Civil->FldTagValue(2):
					$this->Estado_Civil->ViewValue = $this->Estado_Civil->FldTagCaption(2) <> "" ? $this->Estado_Civil->FldTagCaption(2) : $this->Estado_Civil->CurrentValue;
					break;
				case $this->Estado_Civil->FldTagValue(3):
					$this->Estado_Civil->ViewValue = $this->Estado_Civil->FldTagCaption(3) <> "" ? $this->Estado_Civil->FldTagCaption(3) : $this->Estado_Civil->CurrentValue;
					break;
				case $this->Estado_Civil->FldTagValue(4):
					$this->Estado_Civil->ViewValue = $this->Estado_Civil->FldTagCaption(4) <> "" ? $this->Estado_Civil->FldTagCaption(4) : $this->Estado_Civil->CurrentValue;
					break;
				case $this->Estado_Civil->FldTagValue(5):
					$this->Estado_Civil->ViewValue = $this->Estado_Civil->FldTagCaption(5) <> "" ? $this->Estado_Civil->FldTagCaption(5) : $this->Estado_Civil->CurrentValue;
					break;
				default:
					$this->Estado_Civil->ViewValue = $this->Estado_Civil->CurrentValue;
			}
		} else {
			$this->Estado_Civil->ViewValue = NULL;
		}
		$this->Estado_Civil->ViewCustomAttributes = "";

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

		// Celular
		$this->Celular->ViewValue = $this->Celular->CurrentValue;
		$this->Celular->ViewCustomAttributes = "";

		// Telefone Fixo
		$this->Telefone_Fixo->ViewValue = $this->Telefone_Fixo->CurrentValue;
		$this->Telefone_Fixo->ViewCustomAttributes = "";

		// Email
		$this->_Email->ViewValue = $this->_Email->CurrentValue;
		$this->_Email->ViewCustomAttributes = "";

		// Cargo
		$this->Cargo->ViewValue = $this->Cargo->CurrentValue;
		$this->Cargo->ViewCustomAttributes = "";

		// Setor
		$this->Setor->ViewValue = $this->Setor->CurrentValue;
		$this->Setor->ViewCustomAttributes = "";

		// CPF
		$this->CPF->ViewValue = $this->CPF->CurrentValue;
		$this->CPF->ViewCustomAttributes = "";

		// RG
		$this->RG->ViewValue = $this->RG->CurrentValue;
		$this->RG->ViewCustomAttributes = "";

		// Org_Exp
		$this->Org_Exp->ViewValue = $this->Org_Exp->CurrentValue;
		$this->Org_Exp->ViewCustomAttributes = "";

		// Data_Expedicao
		$this->Data_Expedicao->ViewValue = $this->Data_Expedicao->CurrentValue;
		$this->Data_Expedicao->ViewValue = ew_FormatDateTime($this->Data_Expedicao->ViewValue, 7);
		$this->Data_Expedicao->ViewCustomAttributes = "";

		// CTPS_N
		$this->CTPS_N->ViewValue = $this->CTPS_N->CurrentValue;
		$this->CTPS_N->ViewCustomAttributes = "";

		// CTPS_Serie
		$this->CTPS_Serie->ViewValue = $this->CTPS_Serie->CurrentValue;
		$this->CTPS_Serie->ViewCustomAttributes = "";

		// Titulo_Eleitor
		$this->Titulo_Eleitor->ViewValue = $this->Titulo_Eleitor->CurrentValue;
		$this->Titulo_Eleitor->ViewCustomAttributes = "";

		// Numero_Filhos
		$this->Numero_Filhos->ViewValue = $this->Numero_Filhos->CurrentValue;
		$this->Numero_Filhos->ViewCustomAttributes = "";

		// Escolaridade
		if (strval($this->Escolaridade->CurrentValue) <> "") {
			switch ($this->Escolaridade->CurrentValue) {
				case $this->Escolaridade->FldTagValue(1):
					$this->Escolaridade->ViewValue = $this->Escolaridade->FldTagCaption(1) <> "" ? $this->Escolaridade->FldTagCaption(1) : $this->Escolaridade->CurrentValue;
					break;
				case $this->Escolaridade->FldTagValue(2):
					$this->Escolaridade->ViewValue = $this->Escolaridade->FldTagCaption(2) <> "" ? $this->Escolaridade->FldTagCaption(2) : $this->Escolaridade->CurrentValue;
					break;
				case $this->Escolaridade->FldTagValue(3):
					$this->Escolaridade->ViewValue = $this->Escolaridade->FldTagCaption(3) <> "" ? $this->Escolaridade->FldTagCaption(3) : $this->Escolaridade->CurrentValue;
					break;
				case $this->Escolaridade->FldTagValue(4):
					$this->Escolaridade->ViewValue = $this->Escolaridade->FldTagCaption(4) <> "" ? $this->Escolaridade->FldTagCaption(4) : $this->Escolaridade->CurrentValue;
					break;
				default:
					$this->Escolaridade->ViewValue = $this->Escolaridade->CurrentValue;
			}
		} else {
			$this->Escolaridade->ViewValue = NULL;
		}
		$this->Escolaridade->ViewCustomAttributes = "";

		// Situacao
		if (strval($this->Situacao->CurrentValue) <> "") {
			switch ($this->Situacao->CurrentValue) {
				case $this->Situacao->FldTagValue(1):
					$this->Situacao->ViewValue = $this->Situacao->FldTagCaption(1) <> "" ? $this->Situacao->FldTagCaption(1) : $this->Situacao->CurrentValue;
					break;
				case $this->Situacao->FldTagValue(2):
					$this->Situacao->ViewValue = $this->Situacao->FldTagCaption(2) <> "" ? $this->Situacao->FldTagCaption(2) : $this->Situacao->CurrentValue;
					break;
				case $this->Situacao->FldTagValue(3):
					$this->Situacao->ViewValue = $this->Situacao->FldTagCaption(3) <> "" ? $this->Situacao->FldTagCaption(3) : $this->Situacao->CurrentValue;
					break;
				default:
					$this->Situacao->ViewValue = $this->Situacao->CurrentValue;
			}
		} else {
			$this->Situacao->ViewValue = NULL;
		}
		$this->Situacao->ViewCustomAttributes = "";

		// Qual_ano
		$this->Qual_ano->ViewValue = $this->Qual_ano->CurrentValue;
		$this->Qual_ano->ViewCustomAttributes = "";

		// Observacoes
		$this->Observacoes->ViewValue = $this->Observacoes->CurrentValue;
		$this->Observacoes->ViewCustomAttributes = "";

		// Inativo
		if (strval($this->Inativo->CurrentValue) <> "") {
			$this->Inativo->ViewValue = "";
			$arwrk = explode(",", strval($this->Inativo->CurrentValue));
			$cnt = count($arwrk);
			for ($ari = 0; $ari < $cnt; $ari++) {
				switch (trim($arwrk[$ari])) {
					case $this->Inativo->FldTagValue(1):
						$this->Inativo->ViewValue .= $this->Inativo->FldTagCaption(1) <> "" ? $this->Inativo->FldTagCaption(1) : trim($arwrk[$ari]);
						break;
					default:
						$this->Inativo->ViewValue .= trim($arwrk[$ari]);
				}
				if ($ari < $cnt-1) $this->Inativo->ViewValue .= ew_ViewOptionSeparator($ari);
			}
		} else {
			$this->Inativo->ViewValue = NULL;
		}
		$this->Inativo->ViewCustomAttributes = "";

		// Id
		$this->Id->LinkCustomAttributes = "";
		$this->Id->HrefValue = "";
		$this->Id->TooltipValue = "";

		// EhMembro
		$this->EhMembro->LinkCustomAttributes = "";
		$this->EhMembro->HrefValue = "";
		$this->EhMembro->TooltipValue = "";

		// Data_Admissao
		$this->Data_Admissao->LinkCustomAttributes = "";
		$this->Data_Admissao->HrefValue = "";
		$this->Data_Admissao->TooltipValue = "";

		// Nome
		$this->Nome->LinkCustomAttributes = "";
		$this->Nome->HrefValue = "";
		$this->Nome->TooltipValue = "";

		// Data_Nasc
		$this->Data_Nasc->LinkCustomAttributes = "";
		$this->Data_Nasc->HrefValue = "";
		$this->Data_Nasc->TooltipValue = "";

		// Estado_Civil
		$this->Estado_Civil->LinkCustomAttributes = "";
		$this->Estado_Civil->HrefValue = "";
		$this->Estado_Civil->TooltipValue = "";

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

		// Celular
		$this->Celular->LinkCustomAttributes = "";
		$this->Celular->HrefValue = "";
		$this->Celular->TooltipValue = "";

		// Telefone Fixo
		$this->Telefone_Fixo->LinkCustomAttributes = "";
		$this->Telefone_Fixo->HrefValue = "";
		$this->Telefone_Fixo->TooltipValue = "";

		// Email
		$this->_Email->LinkCustomAttributes = "";
		$this->_Email->HrefValue = "";
		$this->_Email->TooltipValue = "";

		// Cargo
		$this->Cargo->LinkCustomAttributes = "";
		$this->Cargo->HrefValue = "";
		$this->Cargo->TooltipValue = "";

		// Setor
		$this->Setor->LinkCustomAttributes = "";
		$this->Setor->HrefValue = "";
		$this->Setor->TooltipValue = "";

		// CPF
		$this->CPF->LinkCustomAttributes = "";
		$this->CPF->HrefValue = "";
		$this->CPF->TooltipValue = "";

		// RG
		$this->RG->LinkCustomAttributes = "";
		$this->RG->HrefValue = "";
		$this->RG->TooltipValue = "";

		// Org_Exp
		$this->Org_Exp->LinkCustomAttributes = "";
		$this->Org_Exp->HrefValue = "";
		$this->Org_Exp->TooltipValue = "";

		// Data_Expedicao
		$this->Data_Expedicao->LinkCustomAttributes = "";
		$this->Data_Expedicao->HrefValue = "";
		$this->Data_Expedicao->TooltipValue = "";

		// CTPS_N
		$this->CTPS_N->LinkCustomAttributes = "";
		$this->CTPS_N->HrefValue = "";
		$this->CTPS_N->TooltipValue = "";

		// CTPS_Serie
		$this->CTPS_Serie->LinkCustomAttributes = "";
		$this->CTPS_Serie->HrefValue = "";
		$this->CTPS_Serie->TooltipValue = "";

		// Titulo_Eleitor
		$this->Titulo_Eleitor->LinkCustomAttributes = "";
		$this->Titulo_Eleitor->HrefValue = "";
		$this->Titulo_Eleitor->TooltipValue = "";

		// Numero_Filhos
		$this->Numero_Filhos->LinkCustomAttributes = "";
		$this->Numero_Filhos->HrefValue = "";
		$this->Numero_Filhos->TooltipValue = "";

		// Escolaridade
		$this->Escolaridade->LinkCustomAttributes = "";
		$this->Escolaridade->HrefValue = "";
		$this->Escolaridade->TooltipValue = "";

		// Situacao
		$this->Situacao->LinkCustomAttributes = "";
		$this->Situacao->HrefValue = "";
		$this->Situacao->TooltipValue = "";

		// Qual_ano
		$this->Qual_ano->LinkCustomAttributes = "";
		$this->Qual_ano->HrefValue = "";
		$this->Qual_ano->TooltipValue = "";

		// Observacoes
		$this->Observacoes->LinkCustomAttributes = "";
		$this->Observacoes->HrefValue = "";
		$this->Observacoes->TooltipValue = "";

		// Inativo
		$this->Inativo->LinkCustomAttributes = "";
		$this->Inativo->HrefValue = "";
		$this->Inativo->TooltipValue = "";

		// Call Row Rendered event
		$this->Row_Rendered();
	}

	// Render edit row values
	function RenderEditRow() {
		global $conn, $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

		// Id
		$this->Id->EditAttrs["class"] = "form-control";
		$this->Id->EditCustomAttributes = "";

		// EhMembro
		$this->EhMembro->EditAttrs["class"] = "form-control";
		$this->EhMembro->EditCustomAttributes = "";
		$arwrk = array();
		$arwrk[] = array($this->EhMembro->FldTagValue(1), $this->EhMembro->FldTagCaption(1) <> "" ? $this->EhMembro->FldTagCaption(1) : $this->EhMembro->FldTagValue(1));
		$arwrk[] = array($this->EhMembro->FldTagValue(2), $this->EhMembro->FldTagCaption(2) <> "" ? $this->EhMembro->FldTagCaption(2) : $this->EhMembro->FldTagValue(2));
		array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
		$this->EhMembro->EditValue = $arwrk;

		// Data_Admissao
		$this->Data_Admissao->EditAttrs["class"] = "form-control";
		$this->Data_Admissao->EditCustomAttributes = "";
		$this->Data_Admissao->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->Data_Admissao->CurrentValue, 7));

		// Nome
		$this->Nome->EditAttrs["class"] = "form-control";
		$this->Nome->EditCustomAttributes = "";
		$this->Nome->EditValue = ew_HtmlEncode($this->Nome->CurrentValue);

		// Data_Nasc
		$this->Data_Nasc->EditAttrs["class"] = "form-control";
		$this->Data_Nasc->EditCustomAttributes = "";
		$this->Data_Nasc->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->Data_Nasc->CurrentValue, 7));

		// Estado_Civil
		$this->Estado_Civil->EditAttrs["class"] = "form-control";
		$this->Estado_Civil->EditCustomAttributes = "";
		$arwrk = array();
		$arwrk[] = array($this->Estado_Civil->FldTagValue(1), $this->Estado_Civil->FldTagCaption(1) <> "" ? $this->Estado_Civil->FldTagCaption(1) : $this->Estado_Civil->FldTagValue(1));
		$arwrk[] = array($this->Estado_Civil->FldTagValue(2), $this->Estado_Civil->FldTagCaption(2) <> "" ? $this->Estado_Civil->FldTagCaption(2) : $this->Estado_Civil->FldTagValue(2));
		$arwrk[] = array($this->Estado_Civil->FldTagValue(3), $this->Estado_Civil->FldTagCaption(3) <> "" ? $this->Estado_Civil->FldTagCaption(3) : $this->Estado_Civil->FldTagValue(3));
		$arwrk[] = array($this->Estado_Civil->FldTagValue(4), $this->Estado_Civil->FldTagCaption(4) <> "" ? $this->Estado_Civil->FldTagCaption(4) : $this->Estado_Civil->FldTagValue(4));
		$arwrk[] = array($this->Estado_Civil->FldTagValue(5), $this->Estado_Civil->FldTagCaption(5) <> "" ? $this->Estado_Civil->FldTagCaption(5) : $this->Estado_Civil->FldTagValue(5));
		array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
		$this->Estado_Civil->EditValue = $arwrk;

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

		// Celular
		$this->Celular->EditAttrs["class"] = "form-control";
		$this->Celular->EditCustomAttributes = "";
		$this->Celular->EditValue = ew_HtmlEncode($this->Celular->CurrentValue);

		// Telefone Fixo
		$this->Telefone_Fixo->EditAttrs["class"] = "form-control";
		$this->Telefone_Fixo->EditCustomAttributes = "";
		$this->Telefone_Fixo->EditValue = ew_HtmlEncode($this->Telefone_Fixo->CurrentValue);

		// Email
		$this->_Email->EditAttrs["class"] = "form-control";
		$this->_Email->EditCustomAttributes = "";
		$this->_Email->EditValue = ew_HtmlEncode($this->_Email->CurrentValue);

		// Cargo
		$this->Cargo->EditAttrs["class"] = "form-control";
		$this->Cargo->EditCustomAttributes = "";
		$this->Cargo->EditValue = ew_HtmlEncode($this->Cargo->CurrentValue);

		// Setor
		$this->Setor->EditAttrs["class"] = "form-control";
		$this->Setor->EditCustomAttributes = "";
		$this->Setor->EditValue = ew_HtmlEncode($this->Setor->CurrentValue);

		// CPF
		$this->CPF->EditAttrs["class"] = "form-control";
		$this->CPF->EditCustomAttributes = "";
		$this->CPF->EditValue = ew_HtmlEncode($this->CPF->CurrentValue);

		// RG
		$this->RG->EditAttrs["class"] = "form-control";
		$this->RG->EditCustomAttributes = "";
		$this->RG->EditValue = ew_HtmlEncode($this->RG->CurrentValue);

		// Org_Exp
		$this->Org_Exp->EditAttrs["class"] = "form-control";
		$this->Org_Exp->EditCustomAttributes = "";
		$this->Org_Exp->EditValue = ew_HtmlEncode($this->Org_Exp->CurrentValue);

		// Data_Expedicao
		$this->Data_Expedicao->EditAttrs["class"] = "form-control";
		$this->Data_Expedicao->EditCustomAttributes = "";
		$this->Data_Expedicao->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->Data_Expedicao->CurrentValue, 7));

		// CTPS_N
		$this->CTPS_N->EditAttrs["class"] = "form-control";
		$this->CTPS_N->EditCustomAttributes = "";
		$this->CTPS_N->EditValue = ew_HtmlEncode($this->CTPS_N->CurrentValue);

		// CTPS_Serie
		$this->CTPS_Serie->EditAttrs["class"] = "form-control";
		$this->CTPS_Serie->EditCustomAttributes = "";
		$this->CTPS_Serie->EditValue = ew_HtmlEncode($this->CTPS_Serie->CurrentValue);

		// Titulo_Eleitor
		$this->Titulo_Eleitor->EditAttrs["class"] = "form-control";
		$this->Titulo_Eleitor->EditCustomAttributes = "";
		$this->Titulo_Eleitor->EditValue = ew_HtmlEncode($this->Titulo_Eleitor->CurrentValue);

		// Numero_Filhos
		$this->Numero_Filhos->EditAttrs["class"] = "form-control";
		$this->Numero_Filhos->EditCustomAttributes = "";
		$this->Numero_Filhos->EditValue = ew_HtmlEncode($this->Numero_Filhos->CurrentValue);

		// Escolaridade
		$this->Escolaridade->EditAttrs["class"] = "form-control";
		$this->Escolaridade->EditCustomAttributes = "";
		$arwrk = array();
		$arwrk[] = array($this->Escolaridade->FldTagValue(1), $this->Escolaridade->FldTagCaption(1) <> "" ? $this->Escolaridade->FldTagCaption(1) : $this->Escolaridade->FldTagValue(1));
		$arwrk[] = array($this->Escolaridade->FldTagValue(2), $this->Escolaridade->FldTagCaption(2) <> "" ? $this->Escolaridade->FldTagCaption(2) : $this->Escolaridade->FldTagValue(2));
		$arwrk[] = array($this->Escolaridade->FldTagValue(3), $this->Escolaridade->FldTagCaption(3) <> "" ? $this->Escolaridade->FldTagCaption(3) : $this->Escolaridade->FldTagValue(3));
		$arwrk[] = array($this->Escolaridade->FldTagValue(4), $this->Escolaridade->FldTagCaption(4) <> "" ? $this->Escolaridade->FldTagCaption(4) : $this->Escolaridade->FldTagValue(4));
		array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
		$this->Escolaridade->EditValue = $arwrk;

		// Situacao
		$this->Situacao->EditAttrs["class"] = "form-control";
		$this->Situacao->EditCustomAttributes = "";
		$arwrk = array();
		$arwrk[] = array($this->Situacao->FldTagValue(1), $this->Situacao->FldTagCaption(1) <> "" ? $this->Situacao->FldTagCaption(1) : $this->Situacao->FldTagValue(1));
		$arwrk[] = array($this->Situacao->FldTagValue(2), $this->Situacao->FldTagCaption(2) <> "" ? $this->Situacao->FldTagCaption(2) : $this->Situacao->FldTagValue(2));
		$arwrk[] = array($this->Situacao->FldTagValue(3), $this->Situacao->FldTagCaption(3) <> "" ? $this->Situacao->FldTagCaption(3) : $this->Situacao->FldTagValue(3));
		array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
		$this->Situacao->EditValue = $arwrk;

		// Qual_ano
		$this->Qual_ano->EditAttrs["class"] = "form-control";
		$this->Qual_ano->EditCustomAttributes = "";
		$this->Qual_ano->EditValue = ew_HtmlEncode($this->Qual_ano->CurrentValue);

		// Observacoes
		$this->Observacoes->EditAttrs["class"] = "form-control";
		$this->Observacoes->EditCustomAttributes = "";
		$this->Observacoes->EditValue = ew_HtmlEncode($this->Observacoes->CurrentValue);

		// Inativo
		$this->Inativo->EditCustomAttributes = "";
		$arwrk = array();
		$arwrk[] = array($this->Inativo->FldTagValue(1), $this->Inativo->FldTagCaption(1) <> "" ? $this->Inativo->FldTagCaption(1) : $this->Inativo->FldTagValue(1));
		$this->Inativo->EditValue = $arwrk;

		// Call Row Rendered event
		$this->Row_Rendered();
	}

	// Aggregate list row values
	function AggregateListRowValues() {
	}

	// Aggregate list row (for rendering)
	function AggregateListRow() {
	}
	var $ExportDoc;

	// Export data in HTML/CSV/Word/Excel/Email/PDF format
	function ExportDocument(&$Doc, &$Recordset, $StartRec, $StopRec, $ExportPageType = "") {
		if (!$Recordset || !$Doc)
			return;
		if (!$Doc->ExportCustom) {

			// Write header
			$Doc->ExportTableHeader();
			if ($Doc->Horizontal) { // Horizontal format, write header
				$Doc->BeginExportRow();
				if ($ExportPageType == "view") {
					if ($this->EhMembro->Exportable) $Doc->ExportCaption($this->EhMembro);
					if ($this->Data_Admissao->Exportable) $Doc->ExportCaption($this->Data_Admissao);
					if ($this->Nome->Exportable) $Doc->ExportCaption($this->Nome);
					if ($this->Data_Nasc->Exportable) $Doc->ExportCaption($this->Data_Nasc);
					if ($this->Estado_Civil->Exportable) $Doc->ExportCaption($this->Estado_Civil);
					if ($this->Endereco->Exportable) $Doc->ExportCaption($this->Endereco);
					if ($this->Bairro->Exportable) $Doc->ExportCaption($this->Bairro);
					if ($this->Cidade->Exportable) $Doc->ExportCaption($this->Cidade);
					if ($this->UF->Exportable) $Doc->ExportCaption($this->UF);
					if ($this->CEP->Exportable) $Doc->ExportCaption($this->CEP);
					if ($this->Celular->Exportable) $Doc->ExportCaption($this->Celular);
					if ($this->Telefone_Fixo->Exportable) $Doc->ExportCaption($this->Telefone_Fixo);
					if ($this->_Email->Exportable) $Doc->ExportCaption($this->_Email);
					if ($this->Cargo->Exportable) $Doc->ExportCaption($this->Cargo);
					if ($this->Setor->Exportable) $Doc->ExportCaption($this->Setor);
					if ($this->CPF->Exportable) $Doc->ExportCaption($this->CPF);
					if ($this->RG->Exportable) $Doc->ExportCaption($this->RG);
					if ($this->Org_Exp->Exportable) $Doc->ExportCaption($this->Org_Exp);
					if ($this->Data_Expedicao->Exportable) $Doc->ExportCaption($this->Data_Expedicao);
					if ($this->CTPS_N->Exportable) $Doc->ExportCaption($this->CTPS_N);
					if ($this->CTPS_Serie->Exportable) $Doc->ExportCaption($this->CTPS_Serie);
					if ($this->Titulo_Eleitor->Exportable) $Doc->ExportCaption($this->Titulo_Eleitor);
					if ($this->Numero_Filhos->Exportable) $Doc->ExportCaption($this->Numero_Filhos);
					if ($this->Escolaridade->Exportable) $Doc->ExportCaption($this->Escolaridade);
					if ($this->Situacao->Exportable) $Doc->ExportCaption($this->Situacao);
					if ($this->Qual_ano->Exportable) $Doc->ExportCaption($this->Qual_ano);
					if ($this->Observacoes->Exportable) $Doc->ExportCaption($this->Observacoes);
					if ($this->Inativo->Exportable) $Doc->ExportCaption($this->Inativo);
				} else {
					if ($this->EhMembro->Exportable) $Doc->ExportCaption($this->EhMembro);
					if ($this->Data_Admissao->Exportable) $Doc->ExportCaption($this->Data_Admissao);
					if ($this->Nome->Exportable) $Doc->ExportCaption($this->Nome);
					if ($this->Data_Nasc->Exportable) $Doc->ExportCaption($this->Data_Nasc);
					if ($this->Estado_Civil->Exportable) $Doc->ExportCaption($this->Estado_Civil);
					if ($this->Endereco->Exportable) $Doc->ExportCaption($this->Endereco);
					if ($this->Bairro->Exportable) $Doc->ExportCaption($this->Bairro);
					if ($this->Cidade->Exportable) $Doc->ExportCaption($this->Cidade);
					if ($this->UF->Exportable) $Doc->ExportCaption($this->UF);
					if ($this->CEP->Exportable) $Doc->ExportCaption($this->CEP);
					if ($this->Celular->Exportable) $Doc->ExportCaption($this->Celular);
					if ($this->Telefone_Fixo->Exportable) $Doc->ExportCaption($this->Telefone_Fixo);
					if ($this->_Email->Exportable) $Doc->ExportCaption($this->_Email);
					if ($this->Cargo->Exportable) $Doc->ExportCaption($this->Cargo);
					if ($this->Setor->Exportable) $Doc->ExportCaption($this->Setor);
					if ($this->CPF->Exportable) $Doc->ExportCaption($this->CPF);
					if ($this->RG->Exportable) $Doc->ExportCaption($this->RG);
					if ($this->Org_Exp->Exportable) $Doc->ExportCaption($this->Org_Exp);
					if ($this->Data_Expedicao->Exportable) $Doc->ExportCaption($this->Data_Expedicao);
					if ($this->CTPS_N->Exportable) $Doc->ExportCaption($this->CTPS_N);
					if ($this->CTPS_Serie->Exportable) $Doc->ExportCaption($this->CTPS_Serie);
					if ($this->Titulo_Eleitor->Exportable) $Doc->ExportCaption($this->Titulo_Eleitor);
					if ($this->Numero_Filhos->Exportable) $Doc->ExportCaption($this->Numero_Filhos);
					if ($this->Escolaridade->Exportable) $Doc->ExportCaption($this->Escolaridade);
					if ($this->Situacao->Exportable) $Doc->ExportCaption($this->Situacao);
					if ($this->Qual_ano->Exportable) $Doc->ExportCaption($this->Qual_ano);
					if ($this->Observacoes->Exportable) $Doc->ExportCaption($this->Observacoes);
					if ($this->Inativo->Exportable) $Doc->ExportCaption($this->Inativo);
				}
				$Doc->EndExportRow();
			}
		}

		// Move to first record
		$RecCnt = $StartRec - 1;
		if (!$Recordset->EOF) {
			$Recordset->MoveFirst();
			if ($StartRec > 1)
				$Recordset->Move($StartRec - 1);
		}
		while (!$Recordset->EOF && $RecCnt < $StopRec) {
			$RecCnt++;
			if (intval($RecCnt) >= intval($StartRec)) {
				$RowCnt = intval($RecCnt) - intval($StartRec) + 1;

				// Page break
				if ($this->ExportPageBreakCount > 0) {
					if ($RowCnt > 1 && ($RowCnt - 1) % $this->ExportPageBreakCount == 0)
						$Doc->ExportPageBreak();
				}
				$this->LoadListRowValues($Recordset);

				// Render row
				$this->RowType = EW_ROWTYPE_VIEW; // Render view
				$this->ResetAttrs();
				$this->RenderListRow();
				if (!$Doc->ExportCustom) {
					$Doc->BeginExportRow($RowCnt); // Allow CSS styles if enabled
					if ($ExportPageType == "view") {
						if ($this->EhMembro->Exportable) $Doc->ExportField($this->EhMembro);
						if ($this->Data_Admissao->Exportable) $Doc->ExportField($this->Data_Admissao);
						if ($this->Nome->Exportable) $Doc->ExportField($this->Nome);
						if ($this->Data_Nasc->Exportable) $Doc->ExportField($this->Data_Nasc);
						if ($this->Estado_Civil->Exportable) $Doc->ExportField($this->Estado_Civil);
						if ($this->Endereco->Exportable) $Doc->ExportField($this->Endereco);
						if ($this->Bairro->Exportable) $Doc->ExportField($this->Bairro);
						if ($this->Cidade->Exportable) $Doc->ExportField($this->Cidade);
						if ($this->UF->Exportable) $Doc->ExportField($this->UF);
						if ($this->CEP->Exportable) $Doc->ExportField($this->CEP);
						if ($this->Celular->Exportable) $Doc->ExportField($this->Celular);
						if ($this->Telefone_Fixo->Exportable) $Doc->ExportField($this->Telefone_Fixo);
						if ($this->_Email->Exportable) $Doc->ExportField($this->_Email);
						if ($this->Cargo->Exportable) $Doc->ExportField($this->Cargo);
						if ($this->Setor->Exportable) $Doc->ExportField($this->Setor);
						if ($this->CPF->Exportable) $Doc->ExportField($this->CPF);
						if ($this->RG->Exportable) $Doc->ExportField($this->RG);
						if ($this->Org_Exp->Exportable) $Doc->ExportField($this->Org_Exp);
						if ($this->Data_Expedicao->Exportable) $Doc->ExportField($this->Data_Expedicao);
						if ($this->CTPS_N->Exportable) $Doc->ExportField($this->CTPS_N);
						if ($this->CTPS_Serie->Exportable) $Doc->ExportField($this->CTPS_Serie);
						if ($this->Titulo_Eleitor->Exportable) $Doc->ExportField($this->Titulo_Eleitor);
						if ($this->Numero_Filhos->Exportable) $Doc->ExportField($this->Numero_Filhos);
						if ($this->Escolaridade->Exportable) $Doc->ExportField($this->Escolaridade);
						if ($this->Situacao->Exportable) $Doc->ExportField($this->Situacao);
						if ($this->Qual_ano->Exportable) $Doc->ExportField($this->Qual_ano);
						if ($this->Observacoes->Exportable) $Doc->ExportField($this->Observacoes);
						if ($this->Inativo->Exportable) $Doc->ExportField($this->Inativo);
					} else {
						if ($this->EhMembro->Exportable) $Doc->ExportField($this->EhMembro);
						if ($this->Data_Admissao->Exportable) $Doc->ExportField($this->Data_Admissao);
						if ($this->Nome->Exportable) $Doc->ExportField($this->Nome);
						if ($this->Data_Nasc->Exportable) $Doc->ExportField($this->Data_Nasc);
						if ($this->Estado_Civil->Exportable) $Doc->ExportField($this->Estado_Civil);
						if ($this->Endereco->Exportable) $Doc->ExportField($this->Endereco);
						if ($this->Bairro->Exportable) $Doc->ExportField($this->Bairro);
						if ($this->Cidade->Exportable) $Doc->ExportField($this->Cidade);
						if ($this->UF->Exportable) $Doc->ExportField($this->UF);
						if ($this->CEP->Exportable) $Doc->ExportField($this->CEP);
						if ($this->Celular->Exportable) $Doc->ExportField($this->Celular);
						if ($this->Telefone_Fixo->Exportable) $Doc->ExportField($this->Telefone_Fixo);
						if ($this->_Email->Exportable) $Doc->ExportField($this->_Email);
						if ($this->Cargo->Exportable) $Doc->ExportField($this->Cargo);
						if ($this->Setor->Exportable) $Doc->ExportField($this->Setor);
						if ($this->CPF->Exportable) $Doc->ExportField($this->CPF);
						if ($this->RG->Exportable) $Doc->ExportField($this->RG);
						if ($this->Org_Exp->Exportable) $Doc->ExportField($this->Org_Exp);
						if ($this->Data_Expedicao->Exportable) $Doc->ExportField($this->Data_Expedicao);
						if ($this->CTPS_N->Exportable) $Doc->ExportField($this->CTPS_N);
						if ($this->CTPS_Serie->Exportable) $Doc->ExportField($this->CTPS_Serie);
						if ($this->Titulo_Eleitor->Exportable) $Doc->ExportField($this->Titulo_Eleitor);
						if ($this->Numero_Filhos->Exportable) $Doc->ExportField($this->Numero_Filhos);
						if ($this->Escolaridade->Exportable) $Doc->ExportField($this->Escolaridade);
						if ($this->Situacao->Exportable) $Doc->ExportField($this->Situacao);
						if ($this->Qual_ano->Exportable) $Doc->ExportField($this->Qual_ano);
						if ($this->Observacoes->Exportable) $Doc->ExportField($this->Observacoes);
						if ($this->Inativo->Exportable) $Doc->ExportField($this->Inativo);
					}
					$Doc->EndExportRow();
				}
			}

			// Call Row Export server event
			if ($Doc->ExportCustom)
				$this->Row_Export($Recordset->fields);
			$Recordset->MoveNext();
		}
		if (!$Doc->ExportCustom) {
			$Doc->ExportTableFooter();
		}
	}

	// Get auto fill value
	function GetAutoFill($id, $val) {
		$rsarr = array();
		$rowcnt = 0;

		// Output
		if (is_array($rsarr) && $rowcnt > 0) {
			$fldcnt = count($rsarr[0]);
			for ($i = 0; $i < $rowcnt; $i++) {
				for ($j = 0; $j < $fldcnt; $j++) {
					$str = strval($rsarr[$i][$j]);
					$str = ew_ConvertToUtf8($str);
					if (isset($post["keepCRLF"])) {
						$str = str_replace(array("\r", "\n"), array("\\r", "\\n"), $str);
					} else {
						$str = str_replace(array("\r", "\n"), array(" ", " "), $str);
					}
					$rsarr[$i][$j] = $str;
				}
			}
			return ew_ArrayToJson($rsarr);
		} else {
			return FALSE;
		}
	}

	// Table level events
	// Recordset Selecting event
	function Recordset_Selecting(&$filter) {

		// Enter your code here	
	}

	// Recordset Selected event
	function Recordset_Selected(&$rs) {

		//echo "Recordset Selected";
	}

	// Recordset Search Validated event
	function Recordset_SearchValidated() {

		// Example:
		//$this->MyField1->AdvancedSearch->SearchValue = "your search criteria"; // Search value

	}

	// Recordset Searching event
	function Recordset_Searching(&$filter) {

		// Enter your code here	
	}

	// Row_Selecting event
	function Row_Selecting(&$filter) {

		// Enter your code here	
	}

	// Row Selected event
	function Row_Selected(&$rs) {

		//echo "Row Selected";
	}

	// Row Inserting event
	function Row_Inserting($rsold, &$rsnew) {

		// Enter your code here
		// To cancel, set return value to FALSE

		return TRUE;
	}

	// Row Inserted event
	function Row_Inserted($rsold, &$rsnew) {

		//echo "Row Inserted"
	}

	// Row Updating event
	function Row_Updating($rsold, &$rsnew) {

		// Enter your code here
		// To cancel, set return value to FALSE

		return TRUE;
	}

	// Row Updated event
	function Row_Updated($rsold, &$rsnew) {

		//echo "Row Updated";
	}

	// Row Update Conflict event
	function Row_UpdateConflict($rsold, &$rsnew) {

		// Enter your code here
		// To ignore conflict, set return value to FALSE

		return TRUE;
	}

	// Grid Inserting event
	function Grid_Inserting() {

		// Enter your code here
		// To reject grid insert, set return value to FALSE

		return TRUE;
	}

	// Grid Inserted event
	function Grid_Inserted($rsnew) {

		//echo "Grid Inserted";
	}

	// Grid Updating event
	function Grid_Updating($rsold) {

		// Enter your code here
		// To reject grid update, set return value to FALSE

		return TRUE;
	}

	// Grid Updated event
	function Grid_Updated($rsold, $rsnew) {

		//echo "Grid Updated";
	}

	// Row Deleting event
	function Row_Deleting(&$rs) {

		// Enter your code here
		// To cancel, set return value to False

		return TRUE;
	}

	// Row Deleted event
	function Row_Deleted(&$rs) {

		//echo "Row Deleted";
	}

	// Email Sending event
	function Email_Sending(&$Email, &$Args) {

		//var_dump($Email); var_dump($Args); exit();
		return TRUE;
	}

	// Lookup Selecting event
	function Lookup_Selecting($fld, &$filter) {

		// Enter your code here
	}

	// Row Rendering event
	function Row_Rendering() {

		// Enter your code here	
	}

	// Row Rendered event
	function Row_Rendered() {

		// To view properties of field class, use:
		//var_dump($this-><FieldName>); 

	}

	// User ID Filtering event
	function UserID_Filtering(&$filter) {

		// Enter your code here
	}
}
?>
