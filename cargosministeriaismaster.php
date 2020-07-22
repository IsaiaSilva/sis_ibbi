<?php

// Cargo_Ministerial
?>
<?php if ($cargosministeriais->Visible) { ?>
<!-- <h4 class="ewMasterCaption"><?php echo $cargosministeriais->TableCaption() ?></h4> -->
<table id="tbl_cargosministeriaismaster" class="table table-bordered table-striped ewViewTable">
	<tbody>
<?php if ($cargosministeriais->Cargo_Ministerial->Visible) { // Cargo_Ministerial ?>
		<tr id="r_Cargo_Ministerial">
			<td><?php echo $cargosministeriais->Cargo_Ministerial->FldCaption() ?></td>
			<td<?php echo $cargosministeriais->Cargo_Ministerial->CellAttributes() ?>>
<span id="el_cargosministeriais_Cargo_Ministerial" class="form-group">
<span<?php echo $cargosministeriais->Cargo_Ministerial->ViewAttributes() ?>>
<?php echo $cargosministeriais->Cargo_Ministerial->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
	</tbody>
</table>
<?php } ?>
