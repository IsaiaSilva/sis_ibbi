<?php

// Global variable for table object
$celulas = NULL;

//
// Table class for celulas
//
class ccelulas extends cTable {
	var $Id_celula;
	var $NomeCelula;
	var $Responsavel;
	var $DiasReunioes;
	var $HorarioReunioes;
	var $Endereco;
	var $Anotacoes;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'celulas';
		$this->TableName = 'celulas';
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

		// Id_celula
		$this->Id_celula = new cField('celulas', 'celulas', 'x_Id_celula', 'Id_celula', '`Id_celula`', '`Id_celula`', 3, -1, FALSE, '`Id_celula`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Id_celula->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Id_celula'] = &$this->Id_celula;

		// NomeCelula
		$this->NomeCelula = new cField('celulas', 'celulas', 'x_NomeCelula', 'NomeCelula', '`NomeCelula`', '`NomeCelula`', 200, -1, FALSE, '`NomeCelula`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['NomeCelula'] = &$this->NomeCelula;

		// Responsavel
		$this->Responsavel = new cField('celulas', 'celulas', 'x_Responsavel', 'Responsavel', '`Responsavel`', '`Responsavel`', 200, -1, FALSE, '`Responsavel`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Responsavel'] = &$this->Responsavel;

		// DiasReunioes
		$this->DiasReunioes = new cField('celulas', 'celulas', 'x_DiasReunioes', 'DiasReunioes', '`DiasReunioes`', '`DiasReunioes`', 202, -1, FALSE, '`DiasReunioes`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->DiasReunioes->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['DiasReunioes'] = &$this->DiasReunioes;

		// HorarioReunioes
		$this->HorarioReunioes = new cField('celulas', 'celulas', 'x_HorarioReunioes', 'HorarioReunioes', '`HorarioReunioes`', 'DATE_FORMAT(`HorarioReunioes`, \'%d/%m/%Y\')', 134, -1, FALSE, '`HorarioReunioes`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->HorarioReunioes->FldDefaultErrMsg = $Language->Phrase("IncorrectTime");
		$this->fields['HorarioReunioes'] = &$this->HorarioReunioes;

		// Endereco
		$this->Endereco = new cField('celulas', 'celulas', 'x_Endereco', 'Endereco', '`Endereco`', '`Endereco`', 200, -1, FALSE, '`Endereco`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Endereco'] = &$this->Endereco;

		// Anotacoes
		$this->Anotacoes = new cField('celulas', 'celulas', 'x_Anotacoes', 'Anotacoes', '`Anotacoes`', '`Anotacoes`', 200, -1, FALSE, '`Anotacoes`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
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
			$sDetailUrl .= "&fk_Id_celula=" . urlencode($this->Id_celula->CurrentValue);
		}
		if ($sDetailUrl == "") {
			$sDetailUrl = "celulaslist.php";
		}
		return $sDetailUrl;
	}

	// Table level SQL
	var $_SqlFrom = "";

	function getSqlFrom() { // From
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`celulas`";
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
	var $UpdateTable = "`celulas`";

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
			if (array_key_exists('Id_celula', $rs))
				ew_AddFilter($where, ew_QuotedName('Id_celula') . '=' . ew_QuotedValue($rs['Id_celula'], $this->Id_celula->FldDataType));
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
		return "`Id_celula` = @Id_celula@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->Id_celula->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@Id_celula@", ew_AdjustSql($this->Id_celula->CurrentValue), $sKeyFilter); // Replace key value
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
			return "celulaslist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "celulaslist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("celulasview.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("celulasview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Add URL
	function GetAddUrl($parm = "") {
		if ($parm <> "")
			return "celulasadd.php?" . $this->UrlParm($parm);
		else
			return "celulasadd.php";
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("celulasedit.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("celulasedit.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("celulasadd.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("celulasadd.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("celulasdelete.php", $this->UrlParm());
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->Id_celula->CurrentValue)) {
			$sUrl .= "Id_celula=" . urlencode($this->Id_celula->CurrentValue);
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
			$arKeys[] = @$_GET["Id_celula"]; // Id_celula

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
			$this->Id_celula->CurrentValue = $key;
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
		$this->Id_celula->setDbValue($rs->fields('Id_celula'));
		$this->NomeCelula->setDbValue($rs->fields('NomeCelula'));
		$this->Responsavel->setDbValue($rs->fields('Responsavel'));
		$this->DiasReunioes->setDbValue($rs->fields('DiasReunioes'));
		$this->HorarioReunioes->setDbValue($rs->fields('HorarioReunioes'));
		$this->Endereco->setDbValue($rs->fields('Endereco'));
		$this->Anotacoes->setDbValue($rs->fields('Anotacoes'));
	}

	// Render list row values
	function RenderListRow() {
		global $conn, $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
		// Id_celula

		$this->Id_celula->CellCssStyle = "white-space: nowrap;";

		// NomeCelula
		// Responsavel
		// DiasReunioes
		// HorarioReunioes
		// Endereco
		// Anotacoes
		// Id_celula

		$this->Id_celula->ViewValue = $this->Id_celula->CurrentValue;
		$this->Id_celula->ViewCustomAttributes = "";

		// NomeCelula
		$this->NomeCelula->ViewValue = $this->NomeCelula->CurrentValue;
		$this->NomeCelula->ViewCustomAttributes = "";

		// Responsavel
		$this->Responsavel->ViewValue = $this->Responsavel->CurrentValue;
		$this->Responsavel->ViewCustomAttributes = "";

		// DiasReunioes
		if (strval($this->DiasReunioes->CurrentValue) <> "") {
			$arwrk = explode(",", $this->DiasReunioes->CurrentValue);
			$sFilterWrk = "";
			foreach ($arwrk as $wrk) {
				if ($sFilterWrk <> "") $sFilterWrk .= " OR ";
				$sFilterWrk .= "`Dias`" . ew_SearchString("=", trim($wrk), EW_DATATYPE_STRING);
			}	
		$sSqlWrk = "SELECT `Dias`, `Dias` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `dias_semana`";
		$sWhereWrk = "";
		if ($sFilterWrk <> "") {
			ew_AddFilter($sWhereWrk, $sFilterWrk);
		}

		// Call Lookup selecting
		$this->Lookup_Selecting($this->DiasReunioes, $sWhereWrk);
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->DiasReunioes->ViewValue = "";
				$ari = 0;
				while (!$rswrk->EOF) {
					$this->DiasReunioes->ViewValue .= $rswrk->fields('DispFld');
					$rswrk->MoveNext();
					if (!$rswrk->EOF) $this->DiasReunioes->ViewValue .= ew_ViewOptionSeparator($ari); // Separate Options
					$ari++;
				}
				$rswrk->Close();
			} else {
				$this->DiasReunioes->ViewValue = $this->DiasReunioes->CurrentValue;
			}
		} else {
			$this->DiasReunioes->ViewValue = NULL;
		}
		$this->DiasReunioes->ViewCustomAttributes = "";

		// HorarioReunioes
		$this->HorarioReunioes->ViewValue = $this->HorarioReunioes->CurrentValue;
		$this->HorarioReunioes->CellCssStyle .= "text-align: center;";
		$this->HorarioReunioes->ViewCustomAttributes = "";

		// Endereco
		$this->Endereco->ViewValue = $this->Endereco->CurrentValue;
		$this->Endereco->ViewCustomAttributes = "";

		// Anotacoes
		$this->Anotacoes->ViewValue = $this->Anotacoes->CurrentValue;
		$this->Anotacoes->ViewCustomAttributes = "";

		// Id_celula
		$this->Id_celula->LinkCustomAttributes = "";
		$this->Id_celula->HrefValue = "";
		$this->Id_celula->TooltipValue = "";

		// NomeCelula
		$this->NomeCelula->LinkCustomAttributes = "";
		$this->NomeCelula->HrefValue = "";
		$this->NomeCelula->TooltipValue = "";

		// Responsavel
		$this->Responsavel->LinkCustomAttributes = "";
		$this->Responsavel->HrefValue = "";
		$this->Responsavel->TooltipValue = "";

		// DiasReunioes
		$this->DiasReunioes->LinkCustomAttributes = "";
		$this->DiasReunioes->HrefValue = "";
		$this->DiasReunioes->TooltipValue = "";

		// HorarioReunioes
		$this->HorarioReunioes->LinkCustomAttributes = "";
		$this->HorarioReunioes->HrefValue = "";
		$this->HorarioReunioes->TooltipValue = "";

		// Endereco
		$this->Endereco->LinkCustomAttributes = "";
		$this->Endereco->HrefValue = "";
		$this->Endereco->TooltipValue = "";

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

		// Id_celula
		$this->Id_celula->EditAttrs["class"] = "form-control";
		$this->Id_celula->EditCustomAttributes = "";

		// NomeCelula
		$this->NomeCelula->EditAttrs["class"] = "form-control";
		$this->NomeCelula->EditCustomAttributes = "";
		$this->NomeCelula->EditValue = ew_HtmlEncode($this->NomeCelula->CurrentValue);

		// Responsavel
		$this->Responsavel->EditAttrs["class"] = "form-control";
		$this->Responsavel->EditCustomAttributes = "";
		$this->Responsavel->EditValue = ew_HtmlEncode($this->Responsavel->CurrentValue);

		// DiasReunioes
		$this->DiasReunioes->EditCustomAttributes = "";

		// HorarioReunioes
		$this->HorarioReunioes->EditAttrs["class"] = "form-control";
		$this->HorarioReunioes->EditCustomAttributes = "";
		$this->HorarioReunioes->EditValue = ew_HtmlEncode($this->HorarioReunioes->CurrentValue);

		// Endereco
		$this->Endereco->EditAttrs["class"] = "form-control";
		$this->Endereco->EditCustomAttributes = "";
		$this->Endereco->EditValue = ew_HtmlEncode($this->Endereco->CurrentValue);

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
					if ($this->NomeCelula->Exportable) $Doc->ExportCaption($this->NomeCelula);
					if ($this->Responsavel->Exportable) $Doc->ExportCaption($this->Responsavel);
					if ($this->DiasReunioes->Exportable) $Doc->ExportCaption($this->DiasReunioes);
					if ($this->HorarioReunioes->Exportable) $Doc->ExportCaption($this->HorarioReunioes);
					if ($this->Endereco->Exportable) $Doc->ExportCaption($this->Endereco);
					if ($this->Anotacoes->Exportable) $Doc->ExportCaption($this->Anotacoes);
				} else {
					if ($this->NomeCelula->Exportable) $Doc->ExportCaption($this->NomeCelula);
					if ($this->Responsavel->Exportable) $Doc->ExportCaption($this->Responsavel);
					if ($this->DiasReunioes->Exportable) $Doc->ExportCaption($this->DiasReunioes);
					if ($this->HorarioReunioes->Exportable) $Doc->ExportCaption($this->HorarioReunioes);
					if ($this->Endereco->Exportable) $Doc->ExportCaption($this->Endereco);
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
						if ($this->NomeCelula->Exportable) $Doc->ExportField($this->NomeCelula);
						if ($this->Responsavel->Exportable) $Doc->ExportField($this->Responsavel);
						if ($this->DiasReunioes->Exportable) $Doc->ExportField($this->DiasReunioes);
						if ($this->HorarioReunioes->Exportable) $Doc->ExportField($this->HorarioReunioes);
						if ($this->Endereco->Exportable) $Doc->ExportField($this->Endereco);
						if ($this->Anotacoes->Exportable) $Doc->ExportField($this->Anotacoes);
					} else {
						if ($this->NomeCelula->Exportable) $Doc->ExportField($this->NomeCelula);
						if ($this->Responsavel->Exportable) $Doc->ExportField($this->Responsavel);
						if ($this->DiasReunioes->Exportable) $Doc->ExportField($this->DiasReunioes);
						if ($this->HorarioReunioes->Exportable) $Doc->ExportField($this->HorarioReunioes);
						if ($this->Endereco->Exportable) $Doc->ExportField($this->Endereco);
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
