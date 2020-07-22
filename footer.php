<?php if (@$gsExport == "") { ?>
<?php if (@!$gbSkipHeaderFooter) { ?>
				<!-- right column (end) -->
				<?php if (isset($gTimer)) $gTimer->Stop() ?>
			</div>
		</div>
	</div>
	<div id="ewFooterRow" class="ewFooterRow">	
		<div class="ewFooterText"><?php echo $Language->ProjectPhrase("FooterText") ?></div>
		<!-- Place other links, for example, disclaimer, here -->		
	</div>
	<!-- footer (end) -->	
</div>
<?php } ?>
<!-- search dialog -->
<div id="ewSearchDialog" class="modal"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><h4 class="modal-title"></h4></div><div class="modal-body"></div><div class="modal-footer"><button type="button" class="btn btn-primary ewButton"><?php echo $Language->Phrase("Search") ?></button><button type="button" class="btn btn-default ewButton" data-dismiss="modal" aria-hidden="true"><?php echo $Language->Phrase("CancelBtn") ?></button></div></div></div></div>
<!-- add option dialog -->
<div id="ewAddOptDialog" class="modal"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title"></h4></div><div class="modal-body"></div><div class="modal-footer"><button type="button" class="btn btn-primary ewButton"><?php echo $Language->Phrase("AddBtn") ?></button><button type="button" class="btn btn-default ewButton" data-dismiss="modal" aria-hidden="true"><?php echo $Language->Phrase("CancelBtn") ?></button></div></div></div></div>
<!-- message box -->
<div id="ewMsgBox" class="modal"><div class="modal-dialog"><div class="modal-content"><div class="modal-body"></div><div class="modal-footer"><button type="button" class="btn btn-primary ewButton" data-dismiss="modal" aria-hidden="true"><?php echo $Language->Phrase("MessageOK") ?></button></div></div></div></div>
<!-- tooltip -->
<div id="ewTooltip"></div>
<?php } ?>
<?php if (@$gsExport == "") { ?>
<script type="text/javascript">
$(document).ready(function($) {
$("#mi_igrejas > a").each( function(index, value) {
	$(this).prepend('<span class="fa fa-university"></span> ');
});
$("#mci_Agenda > a").each( function(index, value) {
	$(this).html("Agenda <div class='badge bg-red'>Hoje: <?php echo $_SESSION["agendados"] ?></div>");
	$(this).prepend('<span class="glyphicon glyphicon-calendar"></span> ');
});
$("#mi_bens_patrimoniais > a").each( function(index, value) {
	$(this).prepend('<span class="glyphicon glyphicon-list-alt"></span> ');
});
$("#mi_celulas > a").each( function(index, value) {
	$(this).prepend('<span class="fa fa-users"></span> ');
});
$("#mi_eventos > a").each( function(index, value) {
	$(this).prepend('<span class="fa fa-coffee"></span> ');
});
$("#mi_usuarios > a").each( function(index, value) {
	$(this).prepend('<span class="glyphicon glyphicon-user"></span> ');
});
$("#mi_changepwd > a").each( function(index, value) {
	$(this).prepend('<span class="glyphicon glyphicon-refresh"></span> ');
});
$("#mi_logout > a").each( function(index, value) {
	$(this).prepend('<span class="glyphicon glyphicon-off"></span> ');
});
$("#mi_plano_oracao > a").each( function(index, value) {
	$(this).prepend('<span class="fa fa-comments-o"></span> ');
});
$("#mi_estudos_biblicos > a").each( function(index, value) {
	$(this).prepend('<span class="fa fa-book"></span> ');
});
$("#mci_Cadastros > a").each( function(index, value) {
	$(this).prepend('<span class="fa fa-folder-open"></span> ');
});
$("#mi_membro > a").each( function(index, value) {
	$(this).prepend('<span class="fa fa-male"></span> ');
});
$("#mi_cargoscelulas > a").each( function(index, value) {
	$(this).prepend('<span class="fa fa-tag"></span> ');
});
$("#mi_cargosministeriais > a").each( function(index, value) {
	$(this).prepend('<span class="fa fa-tag"></span> ');
});
$("#mi_userlevels > a").each( function(index, value) {
	$(this).prepend('<span class="fa fa-lock"></span> ');
});
$("#mi_controle_tarefas > a").each( function(index, value) {
	$(this).prepend('<span class="fa fa-plug"></span> ');
});
$("#mci_Manutene7e3o > a").each( function(index, value) {
	$(this).prepend('<span class="fa fa-gears"></span> ');
});
$("#mi_lista_videos > a").each( function(index, value) {
	$(this).prepend('<span class="fa fa-file-video-o"></span> ');
});
$("#mci_Financeiro > a").each( function(index, value) {
	$(this).prepend('<span class="fa fa-usd"></span> ');
});
$("#mi_contatos > a").each( function(index, value) {
	$(this).prepend('<span class="fa fa-fax"></span> ');
});
$("#mi_funcionarios > a").each( function(index, value) {
	$(this).prepend('<span class="fa fa-user"></span> ');
});
$("#mi_cartas > a").each( function(index, value) {
	$(this).prepend('<span class="glyphicon glyphicon-pencil"></span> ');
});

/*
fa fa-envelope-o
glyphicon glyphicon-envelope
*/
});
</script>
<?php } ?>
</body>
</html>
