<?php

// Igreja
// CNPJ
// DirigenteResponsavel
// Email
// Modelo

?>
<?php if ($igrejas->Visible) { ?>
<!-- <h4 class="ewMasterCaption"><?php echo $igrejas->TableCaption() ?></h4> -->
<table id="tbl_igrejasmaster" class="table table-bordered table-striped ewViewTable">
	<tbody>
<?php if ($igrejas->Igreja->Visible) { // Igreja ?>
		<tr id="r_Igreja">
			<td><?php echo $igrejas->Igreja->FldCaption() ?></td>
			<td<?php echo $igrejas->Igreja->CellAttributes() ?>>
<span id="el_igrejas_Igreja" class="form-group">
<span<?php echo $igrejas->Igreja->ViewAttributes() ?>>
<?php echo $igrejas->Igreja->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($igrejas->CNPJ->Visible) { // CNPJ ?>
		<tr id="r_CNPJ">
			<td><?php echo $igrejas->CNPJ->FldCaption() ?></td>
			<td<?php echo $igrejas->CNPJ->CellAttributes() ?>>
<span id="el_igrejas_CNPJ" class="form-group">
<span<?php echo $igrejas->CNPJ->ViewAttributes() ?>>
<?php echo $igrejas->CNPJ->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($igrejas->DirigenteResponsavel->Visible) { // DirigenteResponsavel ?>
		<tr id="r_DirigenteResponsavel">
			<td><?php echo $igrejas->DirigenteResponsavel->FldCaption() ?></td>
			<td<?php echo $igrejas->DirigenteResponsavel->CellAttributes() ?>>
<span id="el_igrejas_DirigenteResponsavel" class="form-group">
<span<?php echo $igrejas->DirigenteResponsavel->ViewAttributes() ?>>
<?php echo $igrejas->DirigenteResponsavel->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($igrejas->_Email->Visible) { // Email ?>
		<tr id="r__Email">
			<td><?php echo $igrejas->_Email->FldCaption() ?></td>
			<td<?php echo $igrejas->_Email->CellAttributes() ?>>
<span id="el_igrejas__Email" class="form-group">
<span<?php echo $igrejas->_Email->ViewAttributes() ?>>
<?php echo $igrejas->_Email->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($igrejas->Modelo->Visible) { // Modelo ?>
		<tr id="r_Modelo">
			<td><?php echo $igrejas->Modelo->FldCaption() ?></td>
			<td<?php echo $igrejas->Modelo->CellAttributes() ?>>
<span id="el_igrejas_Modelo" class="form-group">
<span<?php echo $igrejas->Modelo->ViewAttributes() ?>>
<?php echo $igrejas->Modelo->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
	</tbody>
</table>
<?php } ?>
