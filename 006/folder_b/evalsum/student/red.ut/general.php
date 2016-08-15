<?
	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::get('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$evalproc_id = $ds->safeGet('evalproc_id');

	if ($RefID == '') {
		$list = new ListClass();

		$list->title = 'Review of Existing Data';

		$list->SQL = "
            SELECT sfrefid,
                   referraldt,
                   finalized_date,
                   CASE
                   WHEN red_data_review IN ('E') THEN 'an initial evaluation'
                   WHEN red_data_review IN ('R','V') THEN 'a required three year reevaluation'
                   WHEN red_data_review IN ('O') THEN red_data_review_o
                   END,
                   CASE red_teammet
                   WHEN 'M' THEN 'met'
                   WHEN 'C' THEN 'conferred'
                   END,
                   red_teammet_dt,
                   lastuser,
                   lastupdate
              FROM webset.es_std_common
             WHERE stdrefid = " . $tsRefID . "
               AND evalproc_id = $evalproc_id
        ";

		$list->addColumn('Referral')->type('date');
		$list->addColumn('Decision')->type('date');
		$list->addColumn('Evaluation');
		$list->addColumn('Team');
		$list->addColumn('Meeting Date')->type('date');
		$list->addColumn('Last User');
		$list->addColumn('Last Update')->type('date');

		$list->addURL = CoreUtils::getURL('general.php', array('dskey' => $dskey));
		$list->editURL = CoreUtils::getURL('general.php', array('dskey' => $dskey));

		$list->deleteTableName = 'webset.es_std_common';
		$list->deleteKeyField = 'sfrefid';

		$list->getButton(ListClassButton::ADD_NEW)
			->disabled(db::execSQL("
                SELECT 1
                  FROM webset.es_std_common
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

		$edit->title = 'Review of Existing Data';

		$edit->setSourceTable('webset.es_std_common', 'sfrefid');

		$edit->addGroup('General Information');

		$disability = UILayout::factory()->addHTML('', '1%')->addHTML(
			UIAnchor::factory('Disability')
				->onClick('addDisability("currdisability")')
				->toHTML()
		)->toHTML();

		$edit->addControl('Current Eligibility Category (for reevaluation ONLY)')
			->css('width', '50%')
			->name('currdisability')
			->sqlField('currdisability')
			->append($disability);

		$edit->addControl('Date of Referral (either for initial evaluation OR parent referral for reevaluation)', 'date')
			->sqlField('referraldt');

		$edit->addControl('Date Review of Existing Data Decision is Finalized', 'date')
			->sqlField('finalized_date');

		$edit->addControl(FFIDEASwitchYN::factory('This data review is being conducted as part of:'))
			->name('red_data_review')
			->sqlField('red_data_review')
			->setData(array(
				array('E', 'an initial evaluation'),
				array('R', 'a reevaluation'),
				array('O', 'Other:')
			));

		$edit->addControl('Narrative')
			->sqlField('red_data_review_o')
			->css('width', '50%')
			->showIf('red_data_review', 'O');

		$edit->addControl(FFIDEASwitchYN::factory('IEP team members and other qualified professional, as appropriate'))
			->sqlField('red_teammet')
			->setData(array(
				array('M', 'met'),
				array('C', 'conferred'),
			));

		$edit->addControl('IEP team met/conferred on:', 'date')
			->sqlField('red_teammet_dt');

		$edit->addControl('Student Grade (fill in if not current):', 'edit')
			->sqlField('stdgrade')
			->size(5)
			->maxlength(10);

		$edit->addControl('Student Age (fill in if not current):', 'edit')
			->sqlField('stdage')
			->size(5)
			->maxlength(10);

		$edit->addControl("evalproc_id", "hidden")
			->value($evalproc_id)
			->sqlField('evalproc_id');

		$edit->addGroup('Update Information', true);
		$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
		$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
		$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

		$edit->finishURL = CoreUtils::getURL('general.php', array('dskey' => $dskey));
		$edit->cancelURL = CoreUtils::getURL('general.php', array('dskey' => $dskey));
		$edit->saveAndAdd = false;
		$edit->firstCellWidth = '50%';

		$edit->printEdit();
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
