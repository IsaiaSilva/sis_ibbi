<?php

// Global variable for table object
$bens_patrimoniais = NULL;

//
// Table class for bens_patrimoniais
//
class cbens_patrimoniais extends cTable {
	var $Id_Patri;
	var $Localidade;
	var $Descricao;
	var $DataAquisao;
	var $Tipo;
	var $Estado_do_bem;
	var $Valor_estimado;
	var $Situacao;
	var $Anotacoes;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'bens_patrimoniais';
		$this->TableName = 'bens_patrimoniais';
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

		// Id_Patri
		$this->Id_Patri = new cField('bens_patrimoniais', 'bens_patrimoniais', 'x_Id_Patri', 'Id_Patri', '`Id_Patri`', '`Id_Patri`', 3, -1, FALSE, '`Id_Patri`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Id_Patri->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Id_Patri'] = &$this->Id_Patri;

		// Localidade
		$this->Localidade = new cField('bens_patrimoniais', 'bens_patrimoniais', 'x_Localidade', 'Localidade', '`Localidade`', '`Localidade`', 3, -1, FALSE, '`Localidade`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Localidade'] = &$this->Localidade;

		// Descricao
		$this->Descricao = new cField('bens_patrimoniais', 'bens_patrimoniais', 'x_Descricao', 'Descricao', '`Descricao`', '`Descricao`', 200, -1, FALSE, '`Descricao`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Descricao'] = &$this->Descricao;

		// DataAquisao
		$this->DataAquisao = new cField('bens_patrimoniais', 'bens_patrimoniais', 'x_DataAquisao', 'DataAquisao', '`DataAquisao`', 'DATE_FORMAT(`DataAquisao`, \'%d/%m/%Y\')', 133, 7, FALSE, '`DataAquisao`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->DataAquisao->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateDMY"));
		$this->fields['DataAquisao'] = &$this->DataAquisao;

		// Tipo
		$this->Tipo = new cField('bens_patrimoniais', 'bens_patrimoniais', 'x_Tipo', 'Tipo', '`Tipo`', '`Tipo`', 202, -1, FALSE, '`Tipo`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Tipo->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Tipo'] = &$this->Tipo;

		// Estado_do_bem
		$this->Estado_do_bem = new cField('bens_patrimoniais', 'bens_patrimoniais', 'x_Estado_do_bem', 'Estado_do_bem', '`Estado_do_bem`', '`Estado_do_bem`', 16, -1, FALSE, '`Estado_do_bem`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Estado_do_bem->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Estado_do_bem'] = &$this->Estado_do_bem;

		// Valor_estimado
		$this->Valor_estimado = new cField('bens_patrimoniais', 'bens_patrimoniais', 'x_Valor_estimado', 'Valor_estimado', '`Valor_estimado`', '`Valor_estimado`', 131, -1, FALSE, '`Valor_estimado`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Valor_estimado->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['Valor_estimado'] = &$this->Valor_estimado;

		// Situacao
		$this->Situacao = new cField('bens_patrimoniais', 'bens_patrimoniais', 'x_Situacao', 'Situacao', '`Situacao`', '`Situacao`', 202, -1, FALSE, '`Situacao`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Situacao'] = &$this->Situacao;

		// Anotacoes
		$this->Anotacoes = new cField('bens_patrimoniais', 'bens_patrimoniais', 'x_Anotacoes', 'Anotacoes', '`Anotacoes`', '`Anotacoes`', 201, -1, FALSE, '`Anotacoes`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
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
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`bens_patrimoniais`";
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
	var $UpdateTable = "`bens_patrimoniais`";

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
			if (array_key_exists('Id_Patri', $rs))
				ew_AddFilter($where, ew_QuotedName('Id_Patri') . '=' . ew_QuotedValue($rs['Id_Patri'], $this->Id_Patri->FldDataType));
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
		return "`Id_Patri` = @Id_Patri@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->Id_Patri->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@Id_Patri@", ew_AdjustSql($this->Id_Patri->CurrentValue), $sKeyFilter); // Replace key value
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
			return "bens_patrimoniaislist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "bens_patrimoniaislist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("bens_patrimoniaisview.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("bens_patrimoniaisview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Add URL
	function GetAddUrl($parm = "") {
		if ($parm <> "")
			return "bens_patrimoniaisadd.php?" . $this->UrlParm($parm);
		else
			return "bens_patrimoniaisadd.php";
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		return $this->KeyUrl("bens_patrimoniaisedit.php", $this->UrlParm($parm));
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		return $this->KeyUrl("bens_patrimoniaisadd.php", $this->UrlParm($parm));
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("bens_patrimoniaisdelete.php", $this->UrlParm());
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->Id_Patri->CurrentValue)) {
			$sUrl .= "Id_Patri=" . urlencode($this->Id_Patri->CurrentValue);
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
			$arKeys[] = @$_GET["Id_Patri"]; // Id_Patri

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
			$this->Id_Patri->CurrentValue = $key;
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

	// Render list row values
	function RenderListRow() {
		global $conn, $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
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
		// Id_Patri

		$this->Id_Patri->ViewValue = $this->Id_Patri->CurrentValue;
		$this->Id_Patri->ViewCustomAttributes = "";

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

		// Id_Patri
		$this->Id_Patri->LinkCustomAttributes = "";
		$this->Id_Patri->HrefValue = "";
		$this->Id_Patri->TooltipValue = "";

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

		// Call Row Rendered event
		$this->Row_Rendered();
	}

	// Render edit row values
	function RenderEditRow() {
		global $conn, $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

		// Id_Patri
		$this->Id_Patri->EditAttrs["class"] = "form-control";
		$this->Id_Patri->EditCustomAttributes = "";

		// Localidade
		$this->Localidade->EditAttrs["class"] = "form-control";
		$this->Localidade->EditCustomAttributes = "";

		// Descricao
		$this->Descricao->EditAttrs["class"] = "form-control";
		$this->Descricao->EditCustomAttributes = "";
		$this->Descricao->EditValue = ew_HtmlEncode($this->Descricao->CurrentValue);

		// DataAquisao
		$this->DataAquisao->EditAttrs["class"] = "form-control";
		$this->DataAquisao->EditCustomAttributes = "";
		$this->DataAquisao->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->DataAquisao->CurrentValue, 7));

		// Tipo
		$this->Tipo->EditCustomAttributes = "";
		$arwrk = array();
		$arwrk[] = array($this->Tipo->FldTagValue(1), $this->Tipo->FldTagCaption(1) <> "" ? $this->Tipo->FldTagCaption(1) : $this->Tipo->FldTagValue(1));
		$arwrk[] = array($this->Tipo->FldTagValue(2), $this->Tipo->FldTagCaption(2) <> "" ? $this->Tipo->FldTagCaption(2) : $this->Tipo->FldTagValue(2));
		$this->Tipo->EditValue = $arwrk;

		// Estado_do_bem
		$this->Estado_do_bem->EditCustomAttributes = "";

		// Valor_estimado
		$this->Valor_estimado->EditAttrs["class"] = "form-control";
		$this->Valor_estimado->EditCustomAttributes = "";
		$this->Valor_estimado->EditValue = ew_HtmlEncode($this->Valor_estimado->CurrentValue);
		if (strval($this->Valor_estimado->EditValue) <> "" && is_numeric($this->Valor_estimado->EditValue)) $this->Valor_estimado->EditValue = ew_FormatNumber($this->Valor_estimado->EditValue, -2, -2, -2, -2);

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
		$this->Anotacoes->EditValue = ew_HtmlEncode($this->Anotacoes->CurrentValue);

		// Call Row Rendered event
		$this->Row_Rendered();
	}

	// Aggregate list row values
	function AggregateListRowValues() {
			if (is_numeric($this->Valor_estimado->CurrentValue))
				$this->Valor_estimado->Total += $this->Valor_estimado->CurrentValue; // Accumulate total
	}

	// Aggregate list row (for rendering)
	function AggregateListRow() {
			$this->Valor_estimado->CurrentValue = $this->Valor_estimado->Total;
			$this->Valor_estimado->ViewValue = $this->Valor_estimado->CurrentValue;
			$this->Valor_estimado->ViewValue = ew_FormatCurrency($this->Valor_estimado->ViewValue, 2, -2, -2, -2);
			$this->Valor_estimado->CellCssStyle .= "text-align: right;";
			$this->Valor_estimado->ViewCustomAttributes = "";
			$this->Valor_estimado->HrefValue = ""; // Clear href value
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
					if ($this->Localidade->Exportable) $Doc->ExportCaption($this->Localidade);
					if ($this->Descricao->Exportable) $Doc->ExportCaption($this->Descricao);
					if ($this->DataAquisao->Exportable) $Doc->ExportCaption($this->DataAquisao);
					if ($this->Tipo->Exportable) $Doc->ExportCaption($this->Tipo);
					if ($this->Estado_do_bem->Exportable) $Doc->ExportCaption($this->Estado_do_bem);
					if ($this->Valor_estimado->Exportable) $Doc->ExportCaption($this->Valor_estimado);
					if ($this->Situacao->Exportable) $Doc->ExportCaption($this->Situacao);
					if ($this->Anotacoes->Exportable) $Doc->ExportCaption($this->Anotacoes);
				} else {
					if ($this->Localidade->Exportable) $Doc->ExportCaption($this->Localidade);
					if ($this->Descricao->Exportable) $Doc->ExportCaption($this->Descricao);
					if ($this->DataAquisao->Exportable) $Doc->ExportCaption($this->DataAquisao);
					if ($this->Tipo->Exportable) $Doc->ExportCaption($this->Tipo);
					if ($this->Estado_do_bem->Exportable) $Doc->ExportCaption($this->Estado_do_bem);
					if ($this->Valor_estimado->Exportable) $Doc->ExportCaption($this->Valor_estimado);
					if ($this->Situacao->Exportable) $Doc->ExportCaption($this->Situacao);
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
				$this->AggregateListRowValues(); // Aggregate row values

				// Render row
				$this->RowType = EW_ROWTYPE_VIEW; // Render view
				$this->ResetAttrs();
				$this->RenderListRow();
				if (!$Doc->ExportCustom) {
					$Doc->BeginExportRow($RowCnt); // Allow CSS styles if enabled
					if ($ExportPageType == "view") {
						if ($this->Localidade->Exportable) $Doc->ExportField($this->Localidade);
						if ($this->Descricao->Exportable) $Doc->ExportField($this->Descricao);
						if ($this->DataAquisao->Exportable) $Doc->ExportField($this->DataAquisao);
						if ($this->Tipo->Exportable) $Doc->ExportField($this->Tipo);
						if ($this->Estado_do_bem->Exportable) $Doc->ExportField($this->Estado_do_bem);
						if ($this->Valor_estimado->Exportable) $Doc->ExportField($this->Valor_estimado);
						if ($this->Situacao->Exportable) $Doc->ExportField($this->Situacao);
						if ($this->Anotacoes->Exportable) $Doc->ExportField($this->Anotacoes);
					} else {
						if ($this->Localidade->Exportable) $Doc->ExportField($this->Localidade);
						if ($this->Descricao->Exportable) $Doc->ExportField($this->Descricao);
						if ($this->DataAquisao->Exportable) $Doc->ExportField($this->DataAquisao);
						if ($this->Tipo->Exportable) $Doc->ExportField($this->Tipo);
						if ($this->Estado_do_bem->Exportable) $Doc->ExportField($this->Estado_do_bem);
						if ($this->Valor_estimado->Exportable) $Doc->ExportField($this->Valor_estimado);
						if ($this->Situacao->Exportable) $Doc->ExportField($this->Situacao);
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

		// Export aggregates (horizontal format only)
		if ($Doc->Horizontal) {
			$this->RowType = EW_ROWTYPE_AGGREGATE;
			$this->ResetAttrs();
			$this->AggregateListRow();
			if (!$Doc->ExportCustom) {
				$Doc->BeginExportRow(-1);
				$Doc->ExportAggregate($this->Localidade, '');
				$Doc->ExportAggregate($this->Descricao, '');
				$Doc->ExportAggregate($this->DataAquisao, '');
				$Doc->ExportAggregate($this->Tipo, '');
				$Doc->ExportAggregate($this->Estado_do_bem, '');
				$Doc->ExportAggregate($this->Valor_estimado, 'TOTAL');
				$Doc->ExportAggregate($this->Situacao, '');
				$Doc->ExportAggregate($this->Anotacoes, '');
				$Doc->EndExportRow();
			}
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
