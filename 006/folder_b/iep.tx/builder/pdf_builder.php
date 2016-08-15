<?php

	Security::init();

	$dskey    = io::get('dskey');
	$ds       = DataStorage::factory($dskey);
	$addUrl   = CoreUtils::getURL('pdf_builder_edit.php', array('dskey' => $dskey, 'idBlock' => io::get('doc')));
	$stdrefid = $ds->safeGet('stdrefid');
	$tsRefID  = $ds->safeGet('tsRefID');
	$list     = new ListClass();

	$list->addURL       = $addUrl;
	$list->editURL      = "javascript:api.ajax.process(UIProcessBoxType.REPORT, api.url('" . CoreUtils::getURL('/apps/idea/library/iep_view.ajax.php') . "', {'RefID' : AF_REFID}))";
	$list->multipleEdit = true;
	$list->SQL          = "
		SELECT siepmrefid,
               to_char(iep.lastupdate,'MM-DD-YYYY') as sIEPMDocDate,
               COALESCE(sIEPMTDesc,rptype,'ARD/IEP'),
               iep.lastuser
		  FROM webset.std_iep iep
               INNER JOIN webset.sys_teacherstudentassignment ts ON iep.stdrefid = ts.tsrefid
               LEFT OUTER JOIN webset.statedef_ieptypes types ON iep.sIEPMTRefID = types.sIEPMTRefId
		 WHERE (iep_status!='I' or iep_status is Null)
           AND ts.stdrefid = $stdrefid
         ORDER BY iep.lastupdate desc
        ";

	if (io::get("doc")>0) {
		$SQL = "
			SELECT doctype
              FROM webset.sped_doctype
             WHERE drefid = " . io::get("doc")
		;

		$result = db::execSQL($SQL)->assoc();
		$list->title = $result['doctype'] . " Builder";

	}

	$list->addColumn("Archive Date")->width('10%');
	$list->addColumn("Type of Document")->width('80%');
	$list->addColumn("Archived By")->width('10%');

	if ($_SESSION["s_accessType"] == "1") {
		$list->getButton(ListClassButton::DELETE)->value("");
	}

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_iep')
			->setKeyField('siepmrefid')
			->applyListClassMode()
	);

	$list->addRecordsProcess('Disable')
		->message('Do you really want to Disable selected records?')
		->url(CoreUtils::getURL('builder_disable.ajax.php', array('dskey' => $dskey)))
		->type(ListClassProcess::DATA_UPDATE)
		->progressBar(false);

	$list->printList();

?>
