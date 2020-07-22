<?php

// Global variable for table object
$contatos = NULL;

//
// Table class for contatos
//
class ccontatos extends cTable {
	var $Id;
	var $Pessoa_Empresa;
	var $Telefone_1;
	var $Telefone_2;
	var $Celular_1;
	var $Celular_2;
	var $EnderecoCompleto;
	var $EmailPessoal;
	var $EmailComercial;
	var $Anotacoes;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'contatos';
		$this->TableName = 'contatos';
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
		$this->Id = new cField('contatos', 'contatos', 'x_Id', 'Id', '`Id`', '`Id`', 3, -1, FALSE, '`Id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Id->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Id'] = &$this->Id;

		// Pessoa_Empresa
		$this->Pessoa_Empresa = new cField('contatos', 'contatos', 'x_Pessoa_Empresa', 'Pessoa_Empresa', '`Pessoa_Empresa`', '`Pessoa_Empresa`', 200, -1, FALSE, '`Pessoa_Empresa`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Pessoa_Empresa'] = &$this->Pessoa_Empresa;

		// Telefone_1
		$this->Telefone_1 = new cField('contatos', 'contatos', 'x_Telefone_1', 'Telefone_1', '`Telefone_1`', '`Telefone_1`', 200, -1, FALSE, '`Telefone_1`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Telefone_1'] = &$this->Telefone_1;

		// Telefone_2
		$this->Telefone_2 = new cField('contatos', 'contatos', 'x_Telefone_2', 'Telefone_2', '`Telefone_2`', '`Telefone_2`', 200, -1, FALSE, '`Telefone_2`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Telefone_2'] = &$this->Telefone_2;

		// Celular_1
		$this->Celular_1 = new cField('contatos', 'contatos', 'x_Celular_1', 'Celular_1', '`Celular_1`', '`Celular_1`', 200, -1, FALSE, '`Celular_1`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Celular_1'] = &$this->Celular_1;

		// Celular_2
		$this->Celular_2 = new cField('contatos', 'contatos', 'x_Celular_2', 'Celular_2', '`Celular_2`', '`Celular_2`', 200, -1, FALSE, '`Celular_2`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Celular_2'] = &$this->Celular_2;

		// EnderecoCompleto
		$this->EnderecoCompleto = new cField('contatos', 'contatos', 'x_EnderecoCompleto', 'EnderecoCompleto', '`EnderecoCompleto`', '`EnderecoCompleto`', 201, -1, FALSE, '`EnderecoCompleto`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['EnderecoCompleto'] = &$this->EnderecoCompleto;

		// EmailPessoal
		$this->EmailPessoal = new cField('contatos', 'contatos', 'x_EmailPessoal', 'EmailPessoal', '`EmailPessoal`', '`EmailPessoal`', 200, -1, FALSE, '`EmailPessoal`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['EmailPessoal'] = &$this->EmailPessoal;

		// EmailComercial
		$this->EmailComercial = new cField('contatos', 'contatos', 'x_EmailComercial', 'EmailComercial', '`EmailComercial`', '`EmailComercial`', 200, -1, FALSE, '`EmailComercial`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['EmailComercial'] = &$this->EmailComercial;

		// Anotacoes
		$this->Anotacoes = new cField('contatos', 'contatos', 'x_Anotacoes', 'Anotacoes', '`Anotacoes`', '`Anotacoes`', 201, -1, FALSE, '`Anotacoes`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Anotacoes'] = &$this->Anotacoes;
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
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`contatos`";
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
	var $UpdateTable = "`contatos`";

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
			return "contatoslist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "contatoslist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("contatosview.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("contatosview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Add URL
	function GetAddUrl($parm = "") {
		if ($parm <> "")
			return "contatosadd.php?" . $this->UrlParm($parm);
		else
			return "contatosadd.php";
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		return $this->KeyUrl("contatosedit.php", $this->UrlParm($parm));
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		return $this->KeyUrl("contatosadd.php", $this->UrlParm($parm));
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("contatosdelete.php", $this->UrlParm());
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
		$this->Pessoa_Empresa->setDbValue($rs->fields('Pessoa_Empresa'));
		$this->Telefone_1->setDbValue($rs->fields('Telefone_1'));
		$this->Telefone_2->setDbValue($rs->fields('Telefone_2'));
		$this->Celular_1->setDbValue($rs->fields('Celular_1'));
		$this->Celular_2->setDbValue($rs->fields('Celular_2'));
		$this->EnderecoCompleto->setDbValue($rs->fields('EnderecoCompleto'));
		$this->EmailPessoal->setDbValue($rs->fields('EmailPessoal'));
		$this->EmailComercial->setDbValue($rs->fields('EmailComercial'));
		$this->Anotacoes->setDbValue($rs->fields('Anotacoes'));
	}

	// Render list row values
	function RenderListRow() {
		global $conn, $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
		// Id

		$this->Id->CellCssStyle = "white-space: nowrap;";

		// Pessoa_Empresa
		// Telefone_1
		// Telefone_2
		// Celular_1
		// Celular_2
		// EnderecoCompleto
		// EmailPessoal
		// EmailComercial
		// Anotacoes
		// Id

		$this->Id->ViewValue = $this->Id->CurrentValue;
		$this->Id->ViewCustomAttributes = "";

		// Pessoa_Empresa
		$this->Pessoa_Empresa->ViewValue = $this->Pessoa_Empresa->CurrentValue;
		$this->Pessoa_Empresa->ViewCustomAttributes = "";

		// Telefone_1
		$this->Telefone_1->ViewValue = $this->Telefone_1->CurrentValue;
		$this->Telefone_1->ViewCustomAttributes = "";

		// Telefone_2
		$this->Telefone_2->ViewValue = $this->Telefone_2->CurrentValue;
		$this->Telefone_2->ViewCustomAttributes = "";

		// Celular_1
		$this->Celular_1->ViewValue = $this->Celular_1->CurrentValue;
		$this->Celular_1->ViewCustomAttributes = "";

		// Celular_2
		$this->Celular_2->ViewValue = $this->Celular_2->CurrentValue;
		$this->Celular_2->ViewCustomAttributes = "";

		// EnderecoCompleto
		$this->EnderecoCompleto->ViewValue = $this->EnderecoCompleto->CurrentValue;
		if (!is_null($this->EnderecoCompleto->ViewValue)) $this->EnderecoCompleto->ViewValue = str_replace("\n", "<br>", $this->EnderecoCompleto->ViewValue); 
		$this->EnderecoCompleto->ViewCustomAttributes = "";

		// EmailPessoal
		$this->EmailPessoal->ViewValue = $this->EmailPessoal->CurrentValue;
		$this->EmailPessoal->ViewCustomAttributes = "";

		// EmailComercial
		$this->EmailComercial->ViewValue = $this->EmailComercial->CurrentValue;
		$this->EmailComercial->ViewCustomAttributes = "";

		// Anotacoes
		$this->Anotacoes->ViewValue = $this->Anotacoes->CurrentValue;
		if (!is_null($this->Anotacoes->ViewValue)) $this->Anotacoes->ViewValue = str_replace("\n", "<br>", $this->Anotacoes->ViewValue); 
		$this->Anotacoes->ViewCustomAttributes = "";

		// Id
		$this->Id->LinkCustomAttributes = "";
		$this->Id->HrefValue = "";
		$this->Id->TooltipValue = "";

		// Pessoa_Empresa
		$this->Pessoa_Empresa->LinkCustomAttributes = "";
		$this->Pessoa_Empresa->HrefValue = "";
		$this->Pessoa_Empresa->TooltipValue = "";

		// Telefone_1
		$this->Telefone_1->LinkCustomAttributes = "";
		$this->Telefone_1->HrefValue = "";
		$this->Telefone_1->TooltipValue = "";

		// Telefone_2
		$this->Telefone_2->LinkCustomAttributes = "";
		$this->Telefone_2->HrefValue = "";
		$this->Telefone_2->TooltipValue = "";

		// Celular_1
		$this->Celular_1->LinkCustomAttributes = "";
		$this->Celular_1->HrefValue = "";
		$this->Celular_1->TooltipValue = "";

		// Celular_2
		$this->Celular_2->LinkCustomAttributes = "";
		$this->Celular_2->HrefValue = "";
		$this->Celular_2->TooltipValue = "";

		// EnderecoCompleto
		$this->EnderecoCompleto->LinkCustomAttributes = "";
		$this->EnderecoCompleto->HrefValue = "";
		$this->EnderecoCompleto->TooltipValue = "";

		// EmailPessoal
		$this->EmailPessoal->LinkCustomAttributes = "";
		$this->EmailPessoal->HrefValue = "";
		$this->EmailPessoal->TooltipValue = "";

		// EmailComercial
		$this->EmailComercial->LinkCustomAttributes = "";
		$this->EmailComercial->HrefValue = "";
		$this->EmailComercial->TooltipValue = "";

		// Anotacoes
		$this->Anotacoes->LinkCustomAttributes = "";
		$this->Anotacoes->HrefValue = "";
		$this->Anotacoes->TooltipValue = "";

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

		// Pessoa_Empresa
		$this->Pessoa_Empresa->EditAttrs["class"] = "form-control";
		$this->Pessoa_Empresa->EditCustomAttributes = "";
		$this->Pessoa_Empresa->EditValue = ew_HtmlEncode($this->Pessoa_Empresa->CurrentValue);

		// Telefone_1
		$this->Telefone_1->EditAttrs["class"] = "form-control";
		$this->Telefone_1->EditCustomAttributes = "";
		$this->Telefone_1->EditValue = ew_HtmlEncode($this->Telefone_1->CurrentValue);

		// Telefone_2
		$this->Telefone_2->EditAttrs["class"] = "form-control";
		$this->Telefone_2->EditCustomAttributes = "";
		$this->Telefone_2->EditValue = ew_HtmlEncode($this->Telefone_2->CurrentValue);

		// Celular_1
		$this->Celular_1->EditAttrs["class"] = "form-control";
		$this->Celular_1->EditCustomAttributes = "";
		$this->Celular_1->EditValue = ew_HtmlEncode($this->Celular_1->CurrentValue);

		// Celular_2
		$this->Celular_2->EditAttrs["class"] = "form-control";
		$this->Celular_2->EditCustomAttributes = "";
		$this->Celular_2->EditValue = ew_HtmlEncode($this->Celular_2->CurrentValue);

		// EnderecoCompleto
		$this->EnderecoCompleto->EditAttrs["class"] = "form-control";
		$this->EnderecoCompleto->EditCustomAttributes = "";
		$this->EnderecoCompleto->EditValue = ew_HtmlEncode($this->EnderecoCompleto->CurrentValue);

		// EmailPessoal
		$this->EmailPessoal->EditAttrs["class"] = "form-control";
		$this->EmailPessoal->EditCustomAttributes = "";
		$this->EmailPessoal->EditValue = ew_HtmlEncode($this->EmailPessoal->CurrentValue);

		// EmailComercial
		$this->EmailComercial->EditAttrs["class"] = "form-control";
		$this->EmailComercial->EditCustomAttributes = "";
		$this->EmailComercial->EditValue = ew_HtmlEncode($this->EmailComercial->CurrentValue);

		// Anotacoes
		$this->Anotacoes->EditAttrs["class"] = "form-control";
		$this->Anotacoes->EditCustomAttributes = "";
		$this->Anotacoes->EditValue = ew_HtmlEncode($this->Anotacoes->CurrentValue);

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
					if ($this->Pessoa_Empresa->Exportable) $Doc->ExportCaption($this->Pessoa_Empresa);
					if ($this->Telefone_1->Exportable) $Doc->ExportCaption($this->Telefone_1);
					if ($this->Telefone_2->Exportable) $Doc->ExportCaption($this->Telefone_2);
					if ($this->Celular_1->Exportable) $Doc->ExportCaption($this->Celular_1);
					if ($this->Celular_2->Exportable) $Doc->ExportCaption($this->Celular_2);
					if ($this->EnderecoCompleto->Exportable) $Doc->ExportCaption($this->EnderecoCompleto);
					if ($this->EmailPessoal->Exportable) $Doc->ExportCaption($this->EmailPessoal);
					if ($this->EmailComercial->Exportable) $Doc->ExportCaption($this->EmailComercial);
					if ($this->Anotacoes->Exportable) $Doc->ExportCaption($this->Anotacoes);
				} else {
					if ($this->Pessoa_Empresa->Exportable) $Doc->ExportCaption($this->Pessoa_Empresa);
					if ($this->Telefone_1->Exportable) $Doc->ExportCaption($this->Telefone_1);
					if ($this->Telefone_2->Exportable) $Doc->ExportCaption($this->Telefone_2);
					if ($this->Celular_1->Exportable) $Doc->ExportCaption($this->Celular_1);
					if ($this->Celular_2->Exportable) $Doc->ExportCaption($this->Celular_2);
					if ($this->EnderecoCompleto->Exportable) $Doc->ExportCaption($this->EnderecoCompleto);
					if ($this->EmailPessoal->Exportable) $Doc->ExportCaption($this->EmailPessoal);
					if ($this->EmailComercial->Exportable) $Doc->ExportCaption($this->EmailComercial);
					if ($this->Anotacoes->Exportable) $Doc->ExportCaption($this->Anotacoes);
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
						if ($this->Pessoa_Empresa->Exportable) $Doc->ExportField($this->Pessoa_Empresa);
						if ($this->Telefone_1->Exportable) $Doc->ExportField($this->Telefone_1);
						if ($this->Telefone_2->Exportable) $Doc->ExportField($this->Telefone_2);
						if ($this->Celular_1->Exportable) $Doc->ExportField($this->Celular_1);
						if ($this->Celular_2->Exportable) $Doc->ExportField($this->Celular_2);
						if ($this->EnderecoCompleto->Exportable) $Doc->ExportField($this->EnderecoCompleto);
						if ($this->EmailPessoal->Exportable) $Doc->ExportField($this->EmailPessoal);
						if ($this->EmailComercial->Exportable) $Doc->ExportField($this->EmailComercial);
						if ($this->Anotacoes->Exportable) $Doc->ExportField($this->Anotacoes);
					} else {
						if ($this->Pessoa_Empresa->Exportable) $Doc->ExportField($this->Pessoa_Empresa);
						if ($this->Telefone_1->Exportable) $Doc->ExportField($this->Telefone_1);
						if ($this->Telefone_2->Exportable) $Doc->ExportField($this->Telefone_2);
						if ($this->Celular_1->Exportable) $Doc->ExportField($this->Celular_1);
						if ($this->Celular_2->Exportable) $Doc->ExportField($this->Celular_2);
						if ($this->EnderecoCompleto->Exportable) $Doc->ExportField($this->EnderecoCompleto);
						if ($this->EmailPessoal->Exportable) $Doc->ExportField($this->EmailPessoal);
						if ($this->EmailComercial->Exportable) $Doc->ExportField($this->EmailComercial);
						if ($this->Anotacoes->Exportable) $Doc->ExportField($this->Anotacoes);
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
