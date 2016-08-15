<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$stdrefid   = $ds->safeGet('stdrefid');
	$editUrl    = CoreUtils::getURL('builder_edit.php', array('dskey' => $dskey, 'tsRefID' => io::get('tsRefID')));
	$list       = new ListClass();

	$list->showSearchFields = true;
	$list->title            = "Archived Student Evaluation Report";
	$list->multipleEdit     = false;
	$list->addURL           = $editUrl;
	$list->editURL          = "javascript:api.ajax.process(UIProcessBoxType.REPORT, api.url('" . CoreUtils::getURL('/apps/idea/library/eval_view.ajax.php') . "', {'RefID' : AF_REFID}))";
	$list->SQL              = "
		SELECT esarefid,
               esaname,
               to_char(esadate, 'mm-dd-YYYY'),
               to_char(eval.lastupdate, 'mm-dd-YYYY'),
               eval.lastuser
          FROM webset.es_std_esarchived eval
               INNER JOIN webset.sys_teacherstudentassignment ts ON eval.stdrefid = ts.tsrefid
         WHERE (1=1) ADD_SEARCH
           AND ts.stdrefid = $stdrefid
           AND deleted is NULL
         ORDER BY eval.lastupdate desc
        ";

	if ($_SESSION["s_accessType"] == "1" || $_SESSION["webset_access"] == "admin") {
		$list->SQL = "
			SELECT esarefid,
                   esaname || CASE deleted WHEN 'Y' then ' - <font color=red>disabled</red>' ELSE '' END,
                   to_char(esadate, 'mm-dd-YYYY'),
                   to_char(eval.lastupdate, 'mm-dd-YYYY'),
                   eval.lastuser
              FROM webset.es_std_esarchived eval
                   INNER JOIN webset.sys_teacherstudentassignment ts ON eval.stdrefid = ts.tsrefid
             WHERE (1=1) ADD_SEARCH
               AND ts.stdrefid = $stdrefid
             ORDER BY eval.lastupdate desc
            ";
	}

	$list->addColumn("Report Type");
	$list->addColumn("Evaluation Date");
	$list->addColumn("Archived Date");
	$list->addColumn("Archived by");

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.es_std_esarchived')
			->setKeyField('esarefid')
			->applyListClassMode()
	);

	if (SystemCore::$AccessType == "1") {
		$list->addRecordsProcess('Disable')
			->message('Do you really want to delete this IEP?')
			->url(CoreUtils::getURL('builder_delete.ajax.php', array('dskey' => $dskey)))
			->type(ListClassProcess::DATA_UPDATE)
			->progressBar(false);
	}

	$list->printList();

?>