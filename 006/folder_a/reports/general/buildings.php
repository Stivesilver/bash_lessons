<?php

	Security::init();

	$list = new listClass();
	$list->title = 'District Building Report';
	$list->showSearchFields = true;
	$list->printable = true;

	$state = VNDState::factory()->code;

	$list->SQL = "
        SELECT vourefid,
                   vouname,
                   voustatecode
              FROM public.sys_voumst
             WHERE vndrefid = " . $_SESSION["s_VndRefID"] . "
             ORDER BY vouname
    ";

	$list->addSearchField("Name", "vouname", "text")->sqlMatchType(FormFieldMatch::SUBSTRING);
	$list->addSearchField("Code", "voustatecode", "text")->sqlMatchType(FormFieldMatch::SUBSTRING);

	$list->addColumn('Building')->sqlField('vouname');
	$list->addColumn('Location ID#')->sqlField('vourefid');
	$list->addColumn('State Code#')->sqlField('voustatecode');

	$list->printList();
?>
