<?php

	Security::init();

	$dskey = io::get('dskey');
	$drefid = io::get('drefid');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$stdrefid = $ds->safeGet('stdrefid');
	$where = '';

	$list = new ListClass();

	$list->title = 'IEP Builder';

	if (!(SystemCore::$AccessType == "1" and IDEACore::disParam(90) != "N")) {
		$where = " AND (iep_status!='I' or iep_status is Null) ";
	}

	if ($drefid > 0) {
		$doc_type = IDEADocumentType::factory($drefid);
		$where .= " AND drefid = " . $drefid . " ";
		$list->title = $doc_type->getTitle() . ' Builder';
	}	

	$list->SQL = "
		SELECT siepmrefid,
			   COALESCE(doctype, rptype),
			   CASE sIEPMTDesc is NULL
			   WHEN TRUE THEN 'ARD/IEP'
			   ELSE sIEPMTDesc END,
			   to_char(iep.stdIEPMeetingDT,'MM-DD-YYYY'),
			   to_char(iep.stdEnrollDT,'MM-DD-YYYY'),
			   iep.lastuser,
			   to_char(iep.sIEPMDocDate,'MM-DD-YYYY') as sIEPMDocDate,
			   iep_status
		  FROM webset.std_iep iep
			   INNER JOIN webset.sys_teacherstudentassignment ts ON iep.stdrefid = ts.tsrefid
			   LEFT OUTER JOIN webset.statedef_ieptypes types ON iep.sIEPMTRefID = types.sIEPMTRefId
			   LEFT OUTER JOIN webset.sped_doctype docs ON iep.rptype = cast(docs.drefid as varchar)
		 WHERE (iep_status!='I' or iep_status is Null)
		   AND ts.stdrefid = " . $stdrefid . "
		   " . $where . "
		 ORDER BY iep.lastupdate desc
    ";

	$list->addColumn('Document')->dataCallback('markInactive');
	$list->addColumn('Type of IEP')->dataCallback('markInactive');
	$list->addColumn('IEP Meeting Date')->dataCallback('markInactive');
	$list->addColumn('IEP Initiation Date')->dataCallback('markInactive');
	$list->addColumn('Archived By')->dataCallback('markInactive');
	$list->addColumn('Archived On')->dataCallback('markInactive');

	$list->addURL = CoreUtils::getURL('main.php', array('dskey' => $dskey, 'drefid' => $drefid));
	$list->editURL = "javascript:api.ajax.process(UIProcessBoxType.REPORT, api.url('" . CoreUtils::getURL('/apps/idea/library/iep_view.ajax.php') . "', {'RefID' : AF_REFID}))";

	$list->multipleEdit = false;

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_iep')
			->setKeyField('siepmrefid')
			->applyListClassMode()
	);

	if (SystemCore::$AccessType == "1") {
		$list->addRecordsProcess('Disable')
			->message('Do you really want to delete this IEP?')
			->url(CoreUtils::getURL('delete.ajax.php', array('dskey' => $dskey)))
			->type(ListClassProcess::DATA_UPDATE)
			->progressBar(false);
	}

	$list->printList();

	function markInactive($data, $col) {
		if ($data['iep_status'] == 'I') {
			if ($col == 2) $data[$col] .= ' [DISABLED]';
			return UILayout::factory()
					->addHTML($data[$col], '[color:gray; font-style: italic;]')
					->toHTML();
		} else {
			return $data[$col];
		}
	}

?>
