<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID = $ds->safeGet('tsRefID');

	io::jsVar('dskey', $dskey);

	$list = new ListClass();
	$list->title = 'Outcome/Service Summary Page (Optional)';
	$list->hideCheckBoxes = false;

	$studentTN = IDEAStudentTN::factory($tsRefID);
	$goals = $studentTN->getBgbGoals();

	$gsentance = array();
	foreach ($goals as $goal) {
		$gsentance[$goal['grefid']] = array(
			'grefid' => $goal['grefid'],
			'g_num' => $goal['g_num'],
			'gsentance' => $goal['gsentance']
		);
	}

	$list->fillData($gsentance);

	$list->addColumn('Number')
		->sqlField('g_num')
		->width('1%');

	$list->addColumn('Major Outcome')
		->sqlField('gsentance');

	$list->setColumnsGroup('Services to be Provided (required by Part C)');

	$serv = $studentTN->getServices();
	foreach ($serv as $stn_refid => $ser) {
		$list->addColumn(str::substr($ser['nsdesc'], 0, 1))
			->hint($ser['nsdesc'])
			->append(
				FileUtils::getIMGFile('view.png')
					->css('cursor', 'pointer')
					->hint($ser['nsdesc'])
					->toHTML()
			)
			->dataCallback(create_function('$data', 'return getStudentServisesSummaryRecuired($data, ' . $stn_refid . ');'))
			->sortable(false)
			->width('1%');
	}

	$list->setColumnsGroup('Non-req. Services');

	$justifications = $studentTN->getJustificationForProvision();
	foreach ($justifications as $sns_refid => $val) {
		$list->addColumn(str::substr($val['txt01'], 0, 1))
			->hint($val['txt01'])
			->append(
				FileUtils::getIMGFile('view.png')
					->css('cursor', 'pointer')
					->hint($val['txt01'])
					->toHTML()
			)
			->dataCallback(create_function('$data', 'return getStudentServisesJustification($data, ' . $sns_refid . ');'))
			->sortable(false)
			->width('1%');
	}

	$list->addButton(
		FFIDEAHelpButton::factory()
			->setHTMLByConstruction(IDEAAppArea::TN_IFSP_OUTCOME_SUMMARY)
	);

	$list->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$list->editURL = CoreUtils::getURL('./outcome_services_edit.php', array('dskey' => $dskey));

	$list->addButton('Clear', 'clearServices()', null, 'clear.png')->width(80);

	$list->printList();

	#----------------------------- functions ---------------------------#

	function getStudentServisesJustification($data, $sns_refid) {

		$SQL = "
			SELECT stsn_provided
			  FROM webset.std_tn_serv_summ_nr
			 WHERE grefid = " . $data['grefid'] . "
			   AND sns_refid = " . $sns_refid . "
		";

		return db::execSQL($SQL)->getOne();
	}

	function getStudentServisesSummaryRecuired($data, $stn_refid) {

		$SQL = "
			SELECT stsr_provided
			  FROM webset.std_tn_serv_summ_rc
			 WHERE grefid = " . $data['grefid'] . "
			   AND stn_refid = " . $stn_refid . "
		";

		return db::execSQL($SQL)->getOne();
	}
?>
<script type="text/javascript">

	function clearServices() {
		var values = ListClass.get().getSelectedValues().values;

		if (!values.length) {
			api.alert('Please select at least one record.');
			return;
		}

		api.confirm(
			'Do you really want to clear the selected record (s)?',
			function() {
				api.ajax.process(
					UIProcessBoxType.PROCESS,
					api.url('./outcome_services_clear.ajax.php'),
					{'values' : values, 'dskey' : dskey},
					true
				).addEventListener(
					WindowEvent.CLOSE,
					function() {
						api.reload();
					}
				);
			}
		);
	}

</script>
