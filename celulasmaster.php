<?php

// NomeCelula
// Responsavel
// DiasReunioes
// HorarioReunioes

?>
<?php if ($celulas->Visible) { ?>
<!-- <h4 class="ewMasterCaption"><?php echo $celulas->TableCaption() ?></h4> -->
<table id="tbl_celulasmaster" class="table table-bordered table-striped ewViewTable">
	<tbody>
<?php if ($celulas->NomeCelula->Visible) { // NomeCelula ?>
		<tr id="r_NomeCelula">
			<td><?php echo $celulas->NomeCelula->FldCaption() ?></td>
			<td<?php echo $celulas->NomeCelula->CellAttributes() ?>>
<span id="el_celulas_NomeCelula" class="form-group">
<span<?php echo $celulas->NomeCelula->ViewAttributes() ?>>
<?php echo $celulas->NomeCelula->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($celulas->Responsavel->Visible) { // Responsavel ?>
		<tr id="r_Responsavel">
			<td><?php echo $celulas->Responsavel->FldCaption() ?></td>
			<td<?php echo $celulas->Responsavel->CellAttributes() ?>>
<span id="el_celulas_Responsavel" class="form-group">
<span<?php echo $celulas->Responsavel->ViewAttributes() ?>>
<?php echo $celulas->Responsavel->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($celulas->DiasReunioes->Visible) { // DiasReunioes ?>
		<tr id="r_DiasReunioes">
			<td><?php echo $celulas->DiasReunioes->FldCaption() ?></td>
			<td<?php echo $celulas->DiasReunioes->CellAttributes() ?>>
<span id="el_celulas_DiasReunioes" class="form-group">
<span<?php echo $celulas->DiasReunioes->ViewAttributes() ?>>
<?php echo $celulas->DiasReunioes->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($celulas->HorarioReunioes->Visible) { // HorarioReunioes ?>
		<tr id="r_HorarioReunioes">
			<td><?php echo $celulas->HorarioReunioes->FldCaption() ?></td>
			<td<?php echo $celulas->HorarioReunioes->CellAttributes() ?>>
<span id="el_celulas_HorarioReunioes" class="form-group">
<span<?php echo $celulas->HorarioReunioes->ViewAttributes() ?>>
<?php echo $celulas->HorarioReunioes->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
	</tbody>
</table>
<?php } ?>
