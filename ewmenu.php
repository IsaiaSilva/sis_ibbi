<!-- Begin Main Menu -->
<?php $RootMenu = new cMenu(EW_MENUBAR_ID) ?>
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(104, "mci_Agenda", $Language->MenuPhrase("104", "MenuText"), "", -1, "", IsLoggedIn(), FALSE, TRUE);
$RootMenu->AddMenuItem(16, "mi_agenda", $Language->MenuPhrase("16", "MenuText"), "agendalist.php", 104, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}agenda'), FALSE);
$RootMenu->AddMenuItem(73, "mi_Agenda_Morta", $Language->MenuPhrase("73", "MenuText"), "agenda_mortalist.php", 104, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}Agenda_Morta'), FALSE);
$RootMenu->AddMenuItem(12, "mi_igrejas", $Language->MenuPhrase("12", "MenuText"), "igrejaslist.php", -1, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}igrejas'), FALSE);
$RootMenu->AddMenuItem(4, "mi_celulas", $Language->MenuPhrase("4", "MenuText"), "celulaslist.php", -1, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}celulas'), FALSE);
$RootMenu->AddMenuItem(13, "mi_membro", $Language->MenuPhrase("13", "MenuText"), "membrolist.php?cmd=resetall", -1, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}membro'), FALSE);
$RootMenu->AddMenuItem(1, "mi_bens_patrimoniais", $Language->MenuPhrase("1", "MenuText"), "bens_patrimoniaislist.php", -1, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}bens_patrimoniais'), FALSE);
$RootMenu->AddMenuItem(18, "mi_contatos", $Language->MenuPhrase("18", "MenuText"), "contatoslist.php", -1, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}contatos'), FALSE);
$RootMenu->AddMenuItem(19, "mi_controle_tarefas", $Language->MenuPhrase("19", "MenuText"), "controle_tarefaslist.php", -1, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}controle_tarefas'), FALSE);
$RootMenu->AddMenuItem(22, "mi_eventos", $Language->MenuPhrase("22", "MenuText"), "eventoslist.php", -1, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}eventos'), FALSE);
$RootMenu->AddMenuItem(21, "mi_estudos_biblicos", $Language->MenuPhrase("21", "MenuText"), "estudos_biblicoslist.php", -1, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}estudos_biblicos'), FALSE);
$RootMenu->AddMenuItem(28, "mi_plano_oracao", $Language->MenuPhrase("28", "MenuText"), "plano_oracaolist.php", -1, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}plano_oracao'), FALSE);
$RootMenu->AddMenuItem(71, "mci_Financeiro", $Language->MenuPhrase("71", "MenuText"), "", -1, "", IsLoggedIn(), FALSE, TRUE);
$RootMenu->AddMenuItem(11, "mi_financeiro", $Language->MenuPhrase("11", "MenuText"), "financeirolist.php", 71, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}financeiro'), FALSE);
$RootMenu->AddMenuItem(318, "mi_caixadodia", $Language->MenuPhrase("318", "MenuText"), "caixadodialist.php", 71, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}caixadodia'), FALSE);
$RootMenu->AddMenuItem(17, "mi_bancos", $Language->MenuPhrase("17", "MenuText"), "bancoslist.php", 71, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}bancos'), FALSE);
$RootMenu->AddMenuItem(187, "mi_conta_bancaria", $Language->MenuPhrase("187", "MenuText"), "conta_bancarialist.php", 71, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}conta_bancaria'), FALSE);
$RootMenu->AddMenuItem(189, "mi_fin_conta_caixa", $Language->MenuPhrase("189", "MenuText"), "fin_conta_caixalist.php", 71, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}fin_conta_caixa'), FALSE);
$RootMenu->AddMenuItem(188, "mi_fin_centro_de_custo", $Language->MenuPhrase("188", "MenuText"), "fin_centro_de_custolist.php", 71, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}fin_centro_de_custo'), FALSE);
$RootMenu->AddMenuItem(190, "mi_fin_forma_pgto", $Language->MenuPhrase("190", "MenuText"), "fin_forma_pgtolist.php", 71, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}fin_forma_pgto'), FALSE);
$RootMenu->AddMenuItem(315, "mci_Relatf3rios_Financeiros", $Language->MenuPhrase("315", "MenuText"), "", -1, "", IsLoggedIn(), FALSE, TRUE);
$RootMenu->AddMenuItem(317, "mi_dizimosporcriterio", $Language->MenuPhrase("317", "MenuText"), "dizimosporcriteriolist.php", 315, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}dizimosporcriterio'), FALSE);
$RootMenu->AddMenuItem(257, "mi_dizimosmesatual", $Language->MenuPhrase("257", "MenuText"), "dizimosmesatuallist.php", 315, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}dizimosmesatual'), FALSE);
$RootMenu->AddMenuItem(386, "mi_ofertasporcriterio", $Language->MenuPhrase("386", "MenuText"), "ofertasporcriteriolist.php", 315, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}ofertasporcriterio'), FALSE);
$RootMenu->AddMenuItem(387, "mi_ofertassmesatual", $Language->MenuPhrase("387", "MenuText"), "ofertassmesatuallist.php", 315, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}ofertassmesatual'), FALSE);
$RootMenu->AddMenuItem(27, "mi_cartas", $Language->MenuPhrase("27", "MenuText"), "cartaslist.php", -1, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}cartas'), FALSE);
$RootMenu->AddMenuItem(381, "mci_Impresse3o_de_Cartas", $Language->MenuPhrase("381", "MenuText"), "", -1, "", IsLoggedIn(), FALSE, TRUE);
$RootMenu->AddMenuItem(319, "mi_print_cartarecomendacao", $Language->MenuPhrase("319", "MenuText"), "print_cartarecomendacaolist.php", 381, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}print_cartarecomendacao'), FALSE);
$RootMenu->AddMenuItem(383, "mi_print_transferencia", $Language->MenuPhrase("383", "MenuText"), "print_transferencialist.php", 381, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}print_transferencia'), FALSE);
$RootMenu->AddMenuItem(382, "mi_print_cartaexclusao", $Language->MenuPhrase("382", "MenuText"), "print_cartaexclusaolist.php", 381, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}print_cartaexclusao'), FALSE);
$RootMenu->AddMenuItem(389, "mi_print_cartaoficio", $Language->MenuPhrase("389", "MenuText"), "print_cartaoficiolist.php", 381, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}print_cartaoficio'), FALSE);
$RootMenu->AddMenuItem(249, "mci_Relatf3rios", $Language->MenuPhrase("249", "MenuText"), "", -1, "", IsLoggedIn(), FALSE, TRUE);
$RootMenu->AddMenuItem(197, "mi_Aniversariantes", $Language->MenuPhrase("197", "MenuText"), "aniversarianteslist.php", 249, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}Aniversariantes'), FALSE);
$RootMenu->AddMenuItem(199, "mi_EtiquetasMalaDireta", $Language->MenuPhrase("199", "MenuText"), "etiquetasmaladiretalist.php", 249, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}EtiquetasMalaDireta'), FALSE);
$RootMenu->AddMenuItem(250, "mi_RelatorioCasamento", $Language->MenuPhrase("250", "MenuText"), "relatoriocasamentolist.php", 249, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}RelatorioCasamento'), FALSE);
$RootMenu->AddMenuItem(251, "mi_RelatorioAdmissao", $Language->MenuPhrase("251", "MenuText"), "relatorioadmissaolist.php", 249, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}RelatorioAdmissao'), FALSE);
$RootMenu->AddMenuItem(388, "mi_RelatoriodataBatismo", $Language->MenuPhrase("388", "MenuText"), "relatoriodatabatismolist.php", 249, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}RelatoriodataBatismo'), FALSE);
$RootMenu->AddMenuItem(198, "mi_Lista_de_Emails", $Language->MenuPhrase("198", "MenuText"), "lista_de_emailslist.php", 249, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}Lista_de_Emails'), FALSE);
$RootMenu->AddMenuItem(254, "mi_ListadeEmailporCargo", $Language->MenuPhrase("254", "MenuText"), "listadeemailporcargoreport.php", 249, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}ListadeEmailporCargo'), FALSE);
$RootMenu->AddMenuItem(253, "mi_ListaEmailsporFuncao", $Language->MenuPhrase("253", "MenuText"), "listaemailsporfuncaoreport.php", 249, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}ListaEmailsporFuncao'), FALSE);
$RootMenu->AddMenuItem(25, "mi_lista_videos", $Language->MenuPhrase("25", "MenuText"), "lista_videoslist.php", -1, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}lista_videos'), FALSE);
$RootMenu->AddMenuItem(69, "mci_Manutene7e3o", $Language->MenuPhrase("69", "MenuText"), "", -1, "", IsLoggedIn(), FALSE, TRUE);
$RootMenu->AddMenuItem(2, "mi_cargoscelulas", $Language->MenuPhrase("2", "MenuText"), "cargoscelulaslist.php", 69, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}cargoscelulas'), FALSE);
$RootMenu->AddMenuItem(3, "mi_cargosministeriais", $Language->MenuPhrase("3", "MenuText"), "cargosministeriaislist.php", 69, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}cargosministeriais'), FALSE);
$RootMenu->AddMenuItem(195, "mi_funcoes_exerce", $Language->MenuPhrase("195", "MenuText"), "funcoes_exercelist.php", 69, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}funcoes_exerce'), FALSE);
$RootMenu->AddMenuItem(194, "mi_rede_ministerial", $Language->MenuPhrase("194", "MenuText"), "rede_ministeriallist.php", 69, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}rede_ministerial'), FALSE);
$RootMenu->AddMenuItem(36, "mi_escolaridade", $Language->MenuPhrase("36", "MenuText"), "escolaridadelist.php", 69, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}escolaridade'), FALSE);
$RootMenu->AddMenuItem(193, "mi_situacao_membro", $Language->MenuPhrase("193", "MenuText"), "situacao_membrolist.php", 69, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}situacao_membro'), FALSE);
$RootMenu->AddMenuItem(196, "mi_tipo_admissao", $Language->MenuPhrase("196", "MenuText"), "tipo_admissaolist.php", 69, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}tipo_admissao'), FALSE);
$RootMenu->AddMenuItem(35, "mi_modelo_igreja", $Language->MenuPhrase("35", "MenuText"), "modelo_igrejalist.php", 69, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}modelo_igreja'), FALSE);
$RootMenu->AddMenuItem(385, "mi_ajuda", $Language->MenuPhrase("385", "MenuText"), "ajudalist.php", 69, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}ajuda'), FALSE);
$RootMenu->AddMenuItem(34, "mi_smtp", $Language->MenuPhrase("34", "MenuText"), "smtplist.php", 69, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}smtp'), FALSE);
$RootMenu->AddMenuItem(23, "mi_funcionarios", $Language->MenuPhrase("23", "MenuText"), "funcionarioslist.php", -1, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}funcionarios'), FALSE);
$RootMenu->AddMenuItem(15, "mi_usuarios", $Language->MenuPhrase("15", "MenuText"), "usuarioslist.php", -1, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}usuarios'), FALSE);
$RootMenu->AddMenuItem(33, "mi_userlevels", $Language->MenuPhrase("33", "MenuText"), "userlevelslist.php", -1, "", (@$_SESSION[EW_SESSION_USER_LEVEL] & EW_ALLOW_ADMIN) == EW_ALLOW_ADMIN, FALSE);
$RootMenu->AddMenuItem(31, "mi_audittrail", $Language->MenuPhrase("31", "MenuText"), "audittraillist.php", -1, "", AllowListMenu('{2B7992FC-5911-46A7-9310-01F4D4225C49}audittrail'), FALSE);
$RootMenu->AddMenuItem(-2, "mi_changepwd", $Language->Phrase("ChangePwd"), "changepwd.php", -1, "", IsLoggedIn() && !IsSysAdmin());
$RootMenu->AddMenuItem(-1, "mi_logout", $Language->Phrase("Logout"), "logout.php", -1, "", IsLoggedIn());
$RootMenu->AddMenuItem(-1, "mi_login", $Language->Phrase("Login"), "login.php", -1, "", !IsLoggedIn() && substr(@$_SERVER["URL"], -1 * strlen("login.php")) <> "login.php");
$RootMenu->Render();
?>
<!-- End Main Menu -->
