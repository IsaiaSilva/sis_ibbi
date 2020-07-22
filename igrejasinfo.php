<?php

// Global variable for table object
$igrejas = NULL;

//
// Table class for igrejas
//
class cigrejas extends cTable {
	var $Id_igreja;
	var $Igreja;
	var $CNPJ;
	var $Endereco;
	var $Bairro;
	var $Cidade;
	var $UF;
	var $CEP;
	var $Telefone1;
	var $Telefone2;
	var $Fax;
	var $DirigenteResponsavel;
	var $_Email;
	var $Site_Igreja;
	var $Email_da_igreja;
	var $Modelo;
	var $Data_de_Fundacao;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'igrejas';
		$this->TableName = 'igrejas';
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

		// Id_igreja
		$this->Id_igreja = new cField('igrejas', 'igrejas', 'x_Id_igreja', 'Id_igreja', '`Id_igreja`', '`Id_igreja`', 3, -1, FALSE, '`Id_igreja`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Id_igreja->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Id_igreja'] = &$this->Id_igreja;

		// Igreja
		$this->Igreja = new cField('igrejas', 'igrejas', 'x_Igreja', 'Igreja', '`Igreja`', '`Igreja`', 200, -1, FALSE, '`Igreja`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Igreja'] = &$this->Igreja;

		// CNPJ
		$this->CNPJ = new cField('igrejas', 'igrejas', 'x_CNPJ', 'CNPJ', '`CNPJ`', '`CNPJ`', 200, -1, FALSE, '`CNPJ`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['CNPJ'] = &$this->CNPJ;

		// Endereco
		$this->Endereco = new cField('igrejas', 'igrejas', 'x_Endereco', 'Endereco', '`Endereco`', '`Endereco`', 200, -1, FALSE, '`Endereco`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Endereco'] = &$this->Endereco;

		// Bairro
		$this->Bairro = new cField('igrejas', 'igrejas', 'x_Bairro', 'Bairro', '`Bairro`', '`Bairro`', 200, -1, FALSE, '`Bairro`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Bairro'] = &$this->Bairro;

		// Cidade
		$this->Cidade = new cField('igrejas', 'igrejas', 'x_Cidade', 'Cidade', '`Cidade`', '`Cidade`', 200, -1, FALSE, '`Cidade`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Cidade'] = &$this->Cidade;

		// UF
		$this->UF = new cField('igrejas', 'igrejas', 'x_UF', 'UF', '`UF`', '`UF`', 200, -1, FALSE, '`UF`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['UF'] = &$this->UF;

		// CEP
		$this->CEP = new cField('igrejas', 'igrejas', 'x_CEP', 'CEP', '`CEP`', '`CEP`', 200, -1, FALSE, '`CEP`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['CEP'] = &$this->CEP;

		// Telefone1
		$this->Telefone1 = new cField('igrejas', 'igrejas', 'x_Telefone1', 'Telefone1', '`Telefone1`', '`Telefone1`', 200, -1, FALSE, '`Telefone1`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Telefone1'] = &$this->Telefone1;

		// Telefone2
		$this->Telefone2 = new cField('igrejas', 'igrejas', 'x_Telefone2', 'Telefone2', '`Telefone2`', '`Telefone2`', 200, -1, FALSE, '`Telefone2`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Telefone2'] = &$this->Telefone2;

		// Fax
		$this->Fax = new cField('igrejas', 'igrejas', 'x_Fax', 'Fax', '`Fax`', '`Fax`', 200, -1, FALSE, '`Fax`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Fax'] = &$this->Fax;

		// DirigenteResponsavel
		$this->DirigenteResponsavel = new cField('igrejas', 'igrejas', 'x_DirigenteResponsavel', 'DirigenteResponsavel', '`DirigenteResponsavel`', '`DirigenteResponsavel`', 200, -1, FALSE, '`DirigenteResponsavel`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['DirigenteResponsavel'] = &$this->DirigenteResponsavel;

		// Email
		$this->_Email = new cField('igrejas', 'igrejas', 'x__Email', 'Email', '`Email`', '`Email`', 200, -1, FALSE, '`Email`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->_Email->FldDefaultErrMsg = $Language->Phrase("IncorrectEmail");
		$this->fields['Email'] = &$this->_Email;

		// Site_Igreja
		$this->Site_Igreja = new cField('igrejas', 'igrejas', 'x_Site_Igreja', 'Site_Igreja', '`Site_Igreja`', '`Site_Igreja`', 200, -1, FALSE, '`Site_Igreja`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Site_Igreja'] = &$this->Site_Igreja;

		// Email_da_igreja
		$this->Email_da_igreja = new cField('igrejas', 'igrejas', 'x_Email_da_igreja', 'Email_da_igreja', '`Email_da_igreja`', '`Email_da_igreja`', 200, -1, FALSE, '`Email_da_igreja`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Email_da_igreja->FldDefaultErrMsg = $Language->Phrase("IncorrectEmail");
		$this->fields['Email_da_igreja'] = &$this->Email_da_igreja;

		// Modelo
		$this->Modelo = new cField('igrejas', 'igrejas', 'x_Modelo', 'Modelo', '`Modelo`', '`Modelo`', 16, -1, FALSE, '`Modelo`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Modelo'] = &$this->Modelo;

		// Data_de_Fundacao
		$this->Data_de_Fundacao = new cField('igrejas', 'igrejas', 'x_Data_de_Fundacao', 'Data_de_Fundacao', '`Data_de_Fundacao`', 'DATE_FORMAT(`Data_de_Fundacao`, \'%d/%m/%Y\')', 133, 7, FALSE, '`Data_de_Fundacao`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Data_de_Fundacao->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateDMY"));
		$this->fields['Data_de_Fundacao'] = &$this->Data_de_Fundacao;
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

	// Current detail table name
	function getCurrentDetailTable() {
		return @$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_DETAIL_TABLE];
	}

	function setCurrentDetailTable($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_DETAIL_TABLE] = $v;
	}

	// Get detail url
	function GetDetailUrl() {

		// Detail url
		$sDetailUrl = "";
		if ($this->getCurrentDetailTable() == "membro") {
			$sDetailUrl = $GLOBALS["membro"]->GetListUrl() . "?showmaster=" . $this->TableVar;
			$sDetailUrl .= "&fk_Id_igreja=" . urlencode($this->Id_igreja->CurrentValue);
		}
		if ($sDetailUrl == "") {
			$sDetailUrl = "igrejaslist.php";
		}
		return $sDetailUrl;
	}

	// Table level SQL
	var $_SqlFrom = "";

	function getSqlFrom() { // From
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`igrejas`";
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
	var $UpdateTable = "`igrejas`";

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

		// Cascade Update detail table 'membro'
		$bCascadeUpdate = FALSE;
		$rscascade = array();
		if (!is_null($rsold) && (isset($rs['Id_igreja']) && $rsold['Id_igreja'] <> $rs['Id_igreja'])) { // Update detail field 'Da_Igreja'
			$bCascadeUpdate = TRUE;
			$rscascade['Da_Igreja'] = $rs['Id_igreja']; 
		}
		if ($bCascadeUpdate) {
			if (!isset($GLOBALS["membro"])) $GLOBALS["membro"] = new cmembro();
			$GLOBALS["membro"]->Update($rscascade, "`Da_Igreja` = " . ew_QuotedValue($rsold['Id_igreja'], EW_DATATYPE_NUMBER));
		}
		return $conn->Execute($this->UpdateSQL($rs, $where));
	}

	// DELETE statement
	function DeleteSQL(&$rs, $where = "") {
		$sql = "DELETE FROM " . $this->UpdateTable . " WHERE ";
		if ($rs) {
			if (array_key_exists('Id_igreja', $rs))
				ew_AddFilter($where, ew_QuotedName('Id_igreja') . '=' . ew_QuotedValue($rs['Id_igreja'], $this->Id_igreja->FldDataType));
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

		// Cascade delete detail table 'membro'
		if (!isset($GLOBALS["membro"])) $GLOBALS["membro"] = new cmembro();
		$rscascade = array();
		$GLOBALS["membro"]->Delete($rscascade, "`Da_Igreja` = " . ew_QuotedValue($rs['Id_igreja'], EW_DATATYPE_NUMBER));
		return $conn->Execute($this->DeleteSQL($rs, $where));
	}

	// Key filter WHERE clause
	function SqlKeyFilter() {
		return "`Id_igreja` = @Id_igreja@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->Id_igreja->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@Id_igreja@", ew_AdjustSql($this->Id_igreja->CurrentValue), $sKeyFilter); // Replace key value
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
			return "igrejaslist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "igrejaslist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("igrejasview.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("igrejasview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Add URL
	function GetAddUrl($parm = "") {
		if ($parm <> "")
			return "igrejasadd.php?" . $this->UrlParm($parm);
		else
			return "igrejasadd.php";
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("igrejasedit.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("igrejasedit.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("igrejasadd.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("igrejasadd.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("igrejasdelete.php", $this->UrlParm());
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->Id_igreja->CurrentValue)) {
			$sUrl .= "Id_igreja=" . urlencode($this->Id_igreja->CurrentValue);
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
			$arKeys[] = @$_GET["Id_igreja"]; // Id_igreja

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
			$this->Id_igreja->CurrentValue = $key;
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
		$this->Id_igreja->setDbValue($rs->fields('Id_igreja'));
		$this->Igreja->setDbValue($rs->fields('Igreja'));
		$this->CNPJ->setDbValue($rs->fields('CNPJ'));
		$this->Endereco->setDbValue($rs->fields('Endereco'));
		$this->Bairro->setDbValue($rs->fields('Bairro'));
		$this->Cidade->setDbValue($rs->fields('Cidade'));
		$this->UF->setDbValue($rs->fields('UF'));
		$this->CEP->setDbValue($rs->fields('CEP'));
		$this->Telefone1->setDbValue($rs->fields('Telefone1'));
		$this->Telefone2->setDbValue($rs->fields('Telefone2'));
		$this->Fax->setDbValue($rs->fields('Fax'));
		$this->DirigenteResponsavel->setDbValue($rs->fields('DirigenteResponsavel'));
		$this->_Email->setDbValue($rs->fields('Email'));
		$this->Site_Igreja->setDbValue($rs->fields('Site_Igreja'));
		$this->Email_da_igreja->setDbValue($rs->fields('Email_da_igreja'));
		$this->Modelo->setDbValue($rs->fields('Modelo'));
		$this->Data_de_Fundacao->setDbValue($rs->fields('Data_de_Fundacao'));
	}

	// Render list row values
	function RenderListRow() {
		global $conn, $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
		// Id_igreja

		$this->Id_igreja->CellCssStyle = "white-space: nowrap;";

		// Igreja
		// CNPJ
		// Endereco
		// Bairro
		// Cidade
		// UF
		// CEP
		// Telefone1
		// Telefone2
		// Fax
		// DirigenteResponsavel
		// Email
		// Site_Igreja
		// Email_da_igreja
		// Modelo
		// Data_de_Fundacao
		// Id_igreja

		$this->Id_igreja->ViewValue = $this->Id_igreja->CurrentValue;
		$this->Id_igreja->ViewCustomAttributes = "";

		// Igreja
		$this->Igreja->ViewValue = $this->Igreja->CurrentValue;
		$this->Igreja->ViewCustomAttributes = "";

		// CNPJ
		$this->CNPJ->ViewValue = $this->CNPJ->CurrentValue;
		$this->CNPJ->ViewCustomAttributes = "";

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

		// Telefone1
		$this->Telefone1->ViewValue = $this->Telefone1->CurrentValue;
		$this->Telefone1->ViewCustomAttributes = "";

		// Telefone2
		$this->Telefone2->ViewValue = $this->Telefone2->CurrentValue;
		$this->Telefone2->ViewCustomAttributes = "";

		// Fax
		$this->Fax->ViewValue = $this->Fax->CurrentValue;
		$this->Fax->ViewCustomAttributes = "";

		// DirigenteResponsavel
		$this->DirigenteResponsavel->ViewValue = $this->DirigenteResponsavel->CurrentValue;
		$this->DirigenteResponsavel->ViewCustomAttributes = "";

		// Email
		$this->_Email->ViewValue = $this->_Email->CurrentValue;
		$this->_Email->ViewCustomAttributes = "";

		// Site_Igreja
		$this->Site_Igreja->ViewValue = $this->Site_Igreja->CurrentValue;
		$this->Site_Igreja->ViewCustomAttributes = "";

		// Email_da_igreja
		$this->Email_da_igreja->ViewValue = $this->Email_da_igreja->CurrentValue;
		$this->Email_da_igreja->ViewCustomAttributes = "";

		// Modelo
		if (strval($this->Modelo->CurrentValue) <> "") {
			$sFilterWrk = "`Id`" . ew_SearchString("=", $this->Modelo->CurrentValue, EW_DATATYPE_NUMBER);
		$sSqlWrk = "SELECT `Id`, `Modelo` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `modelo_igreja`";
		$sWhereWrk = "";
		if ($sFilterWrk <> "") {
			ew_AddFilter($sWhereWrk, $sFilterWrk);
		}

		// Call Lookup selecting
		$this->Lookup_Selecting($this->Modelo, $sWhereWrk);
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `Modelo` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->Modelo->ViewValue = $rswrk->fields('DispFld');
				$rswrk->Close();
			} else {
				$this->Modelo->ViewValue = $this->Modelo->CurrentValue;
			}
		} else {
			$this->Modelo->ViewValue = NULL;
		}
		$this->Modelo->ViewCustomAttributes = "";

		// Data_de_Fundacao
		$this->Data_de_Fundacao->ViewValue = $this->Data_de_Fundacao->CurrentValue;
		$this->Data_de_Fundacao->ViewValue = ew_FormatDateTime($this->Data_de_Fundacao->ViewValue, 7);
		$this->Data_de_Fundacao->ViewCustomAttributes = "";

		// Id_igreja
		$this->Id_igreja->LinkCustomAttributes = "";
		$this->Id_igreja->HrefValue = "";
		$this->Id_igreja->TooltipValue = "";

		// Igreja
		$this->Igreja->LinkCustomAttributes = "";
		$this->Igreja->HrefValue = "";
		$this->Igreja->TooltipValue = "";

		// CNPJ
		$this->CNPJ->LinkCustomAttributes = "";
		$this->CNPJ->HrefValue = "";
		$this->CNPJ->TooltipValue = "";

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

		// Telefone1
		$this->Telefone1->LinkCustomAttributes = "";
		$this->Telefone1->HrefValue = "";
		$this->Telefone1->TooltipValue = "";

		// Telefone2
		$this->Telefone2->LinkCustomAttributes = "";
		$this->Telefone2->HrefValue = "";
		$this->Telefone2->TooltipValue = "";

		// Fax
		$this->Fax->LinkCustomAttributes = "";
		$this->Fax->HrefValue = "";
		$this->Fax->TooltipValue = "";

		// DirigenteResponsavel
		$this->DirigenteResponsavel->LinkCustomAttributes = "";
		$this->DirigenteResponsavel->HrefValue = "";
		$this->DirigenteResponsavel->TooltipValue = "";

		// Email
		$this->_Email->LinkCustomAttributes = "";
		$this->_Email->HrefValue = "";
		$this->_Email->TooltipValue = "";

		// Site_Igreja
		$this->Site_Igreja->LinkCustomAttributes = "";
		$this->Site_Igreja->HrefValue = "";
		$this->Site_Igreja->TooltipValue = "";

		// Email_da_igreja
		$this->Email_da_igreja->LinkCustomAttributes = "";
		$this->Email_da_igreja->HrefValue = "";
		$this->Email_da_igreja->TooltipValue = "";

		// Modelo
		$this->Modelo->LinkCustomAttributes = "";
		$this->Modelo->HrefValue = "";
		$this->Modelo->TooltipValue = "";

		// Data_de_Fundacao
		$this->Data_de_Fundacao->LinkCustomAttributes = "";
		$this->Data_de_Fundacao->HrefValue = "";
		$this->Data_de_Fundacao->TooltipValue = "";

		// Call Row Rendered event
		$this->Row_Rendered();
	}

	// Render edit row values
	function RenderEditRow() {
		global $conn, $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

		// Id_igreja
		$this->Id_igreja->EditAttrs["class"] = "form-control";
		$this->Id_igreja->EditCustomAttributes = "";

		// Igreja
		$this->Igreja->EditAttrs["class"] = "form-control";
		$this->Igreja->EditCustomAttributes = "";
		$this->Igreja->EditValue = ew_HtmlEncode($this->Igreja->CurrentValue);

		// CNPJ
		$this->CNPJ->EditAttrs["class"] = "form-control";
		$this->CNPJ->EditCustomAttributes = "";
		$this->CNPJ->EditValue = ew_HtmlEncode($this->CNPJ->CurrentValue);

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

		// Telefone1
		$this->Telefone1->EditAttrs["class"] = "form-control";
		$this->Telefone1->EditCustomAttributes = "";
		$this->Telefone1->EditValue = ew_HtmlEncode($this->Telefone1->CurrentValue);

		// Telefone2
		$this->Telefone2->EditAttrs["class"] = "form-control";
		$this->Telefone2->EditCustomAttributes = "";
		$this->Telefone2->EditValue = ew_HtmlEncode($this->Telefone2->CurrentValue);

		// Fax
		$this->Fax->EditAttrs["class"] = "form-control";
		$this->Fax->EditCustomAttributes = "";
		$this->Fax->EditValue = ew_HtmlEncode($this->Fax->CurrentValue);

		// DirigenteResponsavel
		$this->DirigenteResponsavel->EditAttrs["class"] = "form-control";
		$this->DirigenteResponsavel->EditCustomAttributes = "";
		$this->DirigenteResponsavel->EditValue = ew_HtmlEncode($this->DirigenteResponsavel->CurrentValue);

		// Email
		$this->_Email->EditAttrs["class"] = "form-control";
		$this->_Email->EditCustomAttributes = "";
		$this->_Email->EditValue = ew_HtmlEncode($this->_Email->CurrentValue);

		// Site_Igreja
		$this->Site_Igreja->EditAttrs["class"] = "form-control";
		$this->Site_Igreja->EditCustomAttributes = "";
		$this->Site_Igreja->EditValue = ew_HtmlEncode($this->Site_Igreja->CurrentValue);

		// Email_da_igreja
		$this->Email_da_igreja->EditAttrs["class"] = "form-control";
		$this->Email_da_igreja->EditCustomAttributes = "";
		$this->Email_da_igreja->EditValue = ew_HtmlEncode($this->Email_da_igreja->CurrentValue);

		// Modelo
		$this->Modelo->EditAttrs["class"] = "form-control";
		$this->Modelo->EditCustomAttributes = "";

		// Data_de_Fundacao
		$this->Data_de_Fundacao->EditAttrs["class"] = "form-control";
		$this->Data_de_Fundacao->EditCustomAttributes = "";
		$this->Data_de_Fundacao->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->Data_de_Fundacao->CurrentValue, 7));

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
					if ($this->Igreja->Exportable) $Doc->ExportCaption($this->Igreja);
					if ($this->CNPJ->Exportable) $Doc->ExportCaption($this->CNPJ);
					if ($this->Endereco->Exportable) $Doc->ExportCaption($this->Endereco);
					if ($this->Bairro->Exportable) $Doc->ExportCaption($this->Bairro);
					if ($this->Cidade->Exportable) $Doc->ExportCaption($this->Cidade);
					if ($this->UF->Exportable) $Doc->ExportCaption($this->UF);
					if ($this->CEP->Exportable) $Doc->ExportCaption($this->CEP);
					if ($this->Telefone1->Exportable) $Doc->ExportCaption($this->Telefone1);
					if ($this->Telefone2->Exportable) $Doc->ExportCaption($this->Telefone2);
					if ($this->Fax->Exportable) $Doc->ExportCaption($this->Fax);
					if ($this->DirigenteResponsavel->Exportable) $Doc->ExportCaption($this->DirigenteResponsavel);
					if ($this->_Email->Exportable) $Doc->ExportCaption($this->_Email);
					if ($this->Site_Igreja->Exportable) $Doc->ExportCaption($this->Site_Igreja);
					if ($this->Email_da_igreja->Exportable) $Doc->ExportCaption($this->Email_da_igreja);
					if ($this->Modelo->Exportable) $Doc->ExportCaption($this->Modelo);
					if ($this->Data_de_Fundacao->Exportable) $Doc->ExportCaption($this->Data_de_Fundacao);
				} else {
					if ($this->Igreja->Exportable) $Doc->ExportCaption($this->Igreja);
					if ($this->CNPJ->Exportable) $Doc->ExportCaption($this->CNPJ);
					if ($this->Endereco->Exportable) $Doc->ExportCaption($this->Endereco);
					if ($this->Bairro->Exportable) $Doc->ExportCaption($this->Bairro);
					if ($this->Cidade->Exportable) $Doc->ExportCaption($this->Cidade);
					if ($this->UF->Exportable) $Doc->ExportCaption($this->UF);
					if ($this->CEP->Exportable) $Doc->ExportCaption($this->CEP);
					if ($this->Telefone1->Exportable) $Doc->ExportCaption($this->Telefone1);
					if ($this->Telefone2->Exportable) $Doc->ExportCaption($this->Telefone2);
					if ($this->Fax->Exportable) $Doc->ExportCaption($this->Fax);
					if ($this->DirigenteResponsavel->Exportable) $Doc->ExportCaption($this->DirigenteResponsavel);
					if ($this->_Email->Exportable) $Doc->ExportCaption($this->_Email);
					if ($this->Site_Igreja->Exportable) $Doc->ExportCaption($this->Site_Igreja);
					if ($this->Email_da_igreja->Exportable) $Doc->ExportCaption($this->Email_da_igreja);
					if ($this->Modelo->Exportable) $Doc->ExportCaption($this->Modelo);
					if ($this->Data_de_Fundacao->Exportable) $Doc->ExportCaption($this->Data_de_Fundacao);
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
						if ($this->Igreja->Exportable) $Doc->ExportField($this->Igreja);
						if ($this->CNPJ->Exportable) $Doc->ExportField($this->CNPJ);
						if ($this->Endereco->Exportable) $Doc->ExportField($this->Endereco);
						if ($this->Bairro->Exportable) $Doc->ExportField($this->Bairro);
						if ($this->Cidade->Exportable) $Doc->ExportField($this->Cidade);
						if ($this->UF->Exportable) $Doc->ExportField($this->UF);
						if ($this->CEP->Exportable) $Doc->ExportField($this->CEP);
						if ($this->Telefone1->Exportable) $Doc->ExportField($this->Telefone1);
						if ($this->Telefone2->Exportable) $Doc->ExportField($this->Telefone2);
						if ($this->Fax->Exportable) $Doc->ExportField($this->Fax);
						if ($this->DirigenteResponsavel->Exportable) $Doc->ExportField($this->DirigenteResponsavel);
						if ($this->_Email->Exportable) $Doc->ExportField($this->_Email);
						if ($this->Site_Igreja->Exportable) $Doc->ExportField($this->Site_Igreja);
						if ($this->Email_da_igreja->Exportable) $Doc->ExportField($this->Email_da_igreja);
						if ($this->Modelo->Exportable) $Doc->ExportField($this->Modelo);
						if ($this->Data_de_Fundacao->Exportable) $Doc->ExportField($this->Data_de_Fundacao);
					} else {
						if ($this->Igreja->Exportable) $Doc->ExportField($this->Igreja);
						if ($this->CNPJ->Exportable) $Doc->ExportField($this->CNPJ);
						if ($this->Endereco->Exportable) $Doc->ExportField($this->Endereco);
						if ($this->Bairro->Exportable) $Doc->ExportField($this->Bairro);
						if ($this->Cidade->Exportable) $Doc->ExportField($this->Cidade);
						if ($this->UF->Exportable) $Doc->ExportField($this->UF);
						if ($this->CEP->Exportable) $Doc->ExportField($this->CEP);
						if ($this->Telefone1->Exportable) $Doc->ExportField($this->Telefone1);
						if ($this->Telefone2->Exportable) $Doc->ExportField($this->Telefone2);
						if ($this->Fax->Exportable) $Doc->ExportField($this->Fax);
						if ($this->DirigenteResponsavel->Exportable) $Doc->ExportField($this->DirigenteResponsavel);
						if ($this->_Email->Exportable) $Doc->ExportField($this->_Email);
						if ($this->Site_Igreja->Exportable) $Doc->ExportField($this->Site_Igreja);
						if ($this->Email_da_igreja->Exportable) $Doc->ExportField($this->Email_da_igreja);
						if ($this->Modelo->Exportable) $Doc->ExportField($this->Modelo);
						if ($this->Data_de_Fundacao->Exportable) $Doc->ExportField($this->Data_de_Fundacao);
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
