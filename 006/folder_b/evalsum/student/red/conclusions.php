<?
	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::get('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$evalproc_id = $ds->safeGet('evalproc_id');

	function getEligibilityControl($sqlField) {
		return UILayout::factory()
			->addHTML('', '10px')
			->addHTML(
				UIAnchor::factory('Disability')
					->onClick('addDisability("' . $sqlField . '")')
					->toHTML()
				)
			->addHTML('', '30px')
			->toHTML();
	}

	if ($RefID == '') {
		$list = new ListClass();

		$list->title = 'Team Conclusions and Decisions';

		$list->SQL = "
            SELECT refid,
                   'Team Conclusions and Decisions',
                   lastuser,
                   lastupdate
              FROM webset.es_std_red_concl
             WHERE stdrefid = " . $tsRefID . "
               AND evalproc_id = $evalproc_id
        ";

		$list->addColumn('Decision')->sortable(false);
		$list->addColumn('Last User');
		$list->addColumn('Last Update')->type('date');

		$list->addURL = CoreUtils::getURL('conclusions.php', array('dskey' => $dskey));
		$list->editURL = CoreUtils::getURL('conclusions.php', array('dskey' => $dskey));

		$list->deleteTableName = 'webset.es_std_red_concl';
		$list->deleteKeyField = 'refid';

		$list->getButton(ListClassButton::ADD_NEW)
			->disabled(db::execSQL("
                SELECT 1
                  FROM webset.es_std_red_concl
                 WHERE stdrefid = " . $tsRefID . "
                   AND evalproc_id = $evalproc_id
            ")->getOne() == '1');

		$list->addButton(
			FFIDEAExportButton::factory()
				->setTable($list->deleteTableName)
				->setKeyField($list->deleteKeyField)
				->applyListClassMode()
		);

		$list->addButton(
			IDEAFormat::getPrintButton(array('dskey' => $dskey))
		);

		$list->printList();

	} else {

		$edit = new EditClass('edit1', $RefID);

		$edit->title = 'Team Conclusions and Decisions';

		$edit->setSourceTable('webset.es_std_red_concl', 'refid');

		$edit->addGroup('General Information');

		$edit->addControl(FFIDEASwitchYN::factory('Additional Data'))
			->name('base_no_data')
			->sqlField('base_no_data')
			->setData(array(
				array(2, 'ADDITIONAL DATA IS NEEDED'),
				array(1, 'NO ADDITIONAL DATA IS NEEDED')
			));

		$edit->addControl('Determination made on', 'date')
			->name('add_data_deter')
			->sqlField('add_data_deter');

		$edit->addGroup('ADDITIONAL DATA IS NEEDED');
		$edit->addControl('For Initial Evaluation', 'select_check')
			->name('yes_data_evi')
			->sqlField('yes_data_evi')
			->data(array(1 => ''))
			->displaySelectAllButton(false)
			->help('MUST provide parent with prior written Notice of Action for intent to evaluate and provide a description of the areas to be assessed and the tests to be administered, if known. Parental consent is required to initiate the evaluation.');

		$edit->addControl('For Reevaluation', 'select_check')
			->name('yes_data_evr')
			->sqlField('yes_data_evr')
			->data(array(1 => ''))
			->displaySelectAllButton(false)
			->help('MUST provide parent with prior written Notice of Action for intent to evaluate and provide a description of the areas to be assessed and the tests to be administered, if known.
			<br/> <br/>
Parental consent is required to initiate the evaluation. However, IF parent does not respond to two attempts by the public agency to provide prior written Notices of Action for intent to reevaluate, the public agency can proceed with reevaluation after the second 10 day waiting period if the parents do not file for due process.');

		$edit->addGroup('NO ADDITIONAL DATA IS NEEDED');
		$edit->addControl('For Initial Evaluation', 'select_check')
			->name('no_data_evi')
			->sqlField('no_data_evi')
			->data(array(1 => ''))
			->displaySelectAllButton(false)
			->help('MUST provide parent with prior written Notice of Action <b>and</b> obtain Parental consent <b>and</b> provide an Evaluation Report that includes an eligibility determination based on the Review of Existing Data.');
		$edit->addControl('For Reevaluation', 'select_check')
			->name('no_data_evr')
			->sqlField('no_data_evr')
			->data(array(1 => ''))
			->displaySelectAllButton(false)
			->help('MUST select one reason below');

		$edit->addControl('', 'select_radio');
		$edit->addControl('The current identification of')
			->css('width', '50%')
			->name('no_data_curtext')
			->sqlField('no_data_curtext')
			->append(getEligibilityControl('no_data_curtext'));

		$edit->addControl('Continues to be appropriate', 'select_check')
			->name('no_data_cur')
			->sqlField('no_data_cur')
			->data(array(1 => ''))
			->displaySelectAllButton(false)
			->help('MUST complete "Parent Notification Regarding Results of Review of Existing Data Documentation Form" (page 6 of the RED form) to provide prior written notice.');

		$sup = '<sup>1</sup>';
		$edit->addControl('', 'select_radio');
		$edit->addControl('Does not continue to show evidence of the disability' . $sup, 'select_check')
			->name('no_data_noevi')
			->sqlField('no_data_noevi')
			->data(array(1 => ''))
			->displaySelectAllButton(false);


		$note = $sup . ' MUST Provide parent with Notice of Action <b>and</b> an Evaluation Report that includes an eligibility determination based on the Review of Existing Data.';

		$edit->addControl('', 'select_radio');
		$edit->addControl('Sufficient information exists to change the current identification' . $sup, 'select_check')
			->name('no_data_change')
			->sqlField('no_data_change')
			->data(array(1 => ''))
			->displaySelectAllButton(false);

		$edit->addControl('From')
			->css('width', '50%')
			->name('no_data_change_from')
			->sqlField('no_data_change_from')
			->append(getEligibilityControl('no_data_change_from'));

		$edit->addControl('To')
			->css('width', '50%')
			->name('no_data_change_to')
			->sqlField('no_data_change_to')
			->append(getEligibilityControl('no_data_change_to'));

		$edit->addGroup('Update Information', true);
		$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
		$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
		$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
		$edit->addControl("evalproc_id", "hidden")
			->value($evalproc_id)
			->sqlField('evalproc_id');

		$edit->finishURL = CoreUtils::getURL('conclusions.php', array('dskey' => $dskey));
		$edit->cancelURL = CoreUtils::getURL('conclusions.php', array('dskey' => $dskey));
		$edit->saveAndAdd = false;
		$edit->saveAndEdit = true;
		$edit->topButtons = true;

		$edit->firstCellWidth = '50%';

		$edit->printEdit();

		print UIMessage::factory($note, UIMessage::NOTE)->toHTML();
	}
?>
<script type="text/javascript">
	function addDisability(field) {
		var wnd = api.window.open('', api.url('disability.php', {'field': field}));
		wnd.resize(950, 600);
		wnd.center();
		wnd.addEventListener('disability_selected', onEvent);
		wnd.show();
	}

	function onEvent(e) {
		var disability = e.param.dsb;
		var field = e.param.field;
		if ($("#" + field).val() != "") disability = $("#" + field).val() + "; " + disability;
		$("#" + field).val(disability);
	}

</script>
