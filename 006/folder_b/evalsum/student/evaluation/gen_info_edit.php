<?
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$evalproc_id = $ds->safeGet('evalproc_id');
	$screenURL = $ds->safeGet('screenURL');

	$student = IDEAStudent::factory($tsRefID);

	$edit = new EditClass('edit1', $evalproc_id);

	$edit->firstCellWidth = '30%';

	$edit->title = 'General Information';

	$edit->setSourceTable('webset.es_std_er_generalinfo', 'eprefid');

	$edit->addGroup('General Information');

	$edit->beginGrid(3);

	$edit->addControl(FFInput::factory(''))
		->caption('Student\'s Name')
		->width('85%')
		->value($student->get('stdnamefml'))
		->sqlField('stdname');

	$edit->addControl(FFInput::factory(''))
		->caption('Date of Birth')
		->width('85%')
		->value($student->get('stddob'))
		->sqlField('stddob');

	$edit->addControl(FFInput::factory(''))
		->caption('Age')
		->width('85%')
		->value($student->get('stdage'))
		->sqlField('stdage');


	$edit->addControl(FFInput::factory(''))
		->caption('Grade')
		->width('85%')
		->value($student->get('grdlevel'))
		->sqlField('stdgrade');

	$edit->addControl(FFInput::factory(''))
		->caption('School')
		->width('85%')
		->value($student->get('vouname'))
		->sqlField('stdschool');

	$edit->endGrid();
	$edit->beginGrid(2);

	$parents = '';
	$guardians = $student->getGuardians();
	foreach ($guardians as $guardian) {
		if (!$parents) {
			$parents .= $guardian['gdfnm'] . ' ' . $guardian['gdlnm'];
		} else {
			$parents .= ', ' . $guardian['gdfnm'] . ' ' . $guardian['gdlnm'];
		}
	}
	$parents = ucwords(strtolower($parents));

	$edit->addControl(FFInput::factory(''))
		->caption('Parent’s Name(s)')
		->width('85%')
		->value($parents)
		->sqlField('stdparent');

	$edit->addControl(FFInput::factory(''))
		->caption('Phone')
		->width('85%')
		->value($student->get('stdhphn'))
		->sqlField('stdphone');

	$edit->addControl(FFInput::factory(''))
		->caption('Address')
		->width('85%')
		->value($student->get('stdaddress'))
		->sqlField('stdaddress');

	$edit->endGrid();

	$edit->addControl(FFIDEASwitchYN::factory('Report Type'))
		->data(array(
			1 => 'Initial Evaluation',
			2 => 'Reevaluation'
		))
		->sqlField('report_type')
		->req();

	$lang = $student->get('prim_lang');
	$lang = $lang ? $lang : 'English';
	$lang = ucwords(strtolower($lang));
	$edit->addControl(FFInput::factory(''))
		->caption('Primary Language')
		->width('25%')
		->value($lang)
		->sqlField('stdlang');

	$edit->addControl(FFIDEASwitchYN::factory('Does student have limited English proficiency?'))
		->sqlField('lep_sw');

	$edit->addControl('Referral Date', 'date')
		->sqlField('refferal_dt');

	$edit->addControl('Review of Existing Data Date', 'date')
		->sqlField('red_dt');

	$edit->addControl('Date of Consent to Evaluate', 'date')
		->sqlField('consent_dt');

	$edit->addControl('Eligibility Staffing Date', 'date')
		->sqlField('eligibility_dt');

	$edit->addControl(FFIDEASwitchYN::factory('Evaluation Held within Required Timelines'))
		->help('include acceptable extensions if appropriate')
		->sqlField('timiline_sw')
		->name('timiline_sw');

	$edit->addControl('Timelines Comments')
		->css('width', '100%')
		->sqlField('timiline_no');

	$edit->addControl(FFInputDropList::factory('Referred By')
		->sqlField('reffered_by')
		->dropListSQL("
            SELECT umrefid, umfirstname || ' ' || umlastname
              FROM public.sys_usermst
             WHERE vndrefid = VNDREFID
               AND um_internal
             ORDER BY UPPER(umlastname), UPPER(umfirstname)
        "))
		->highlightField(false)
		->width('35%');

	$edit->addControl(FFInputDropList::factory('Role')
		->sqlField('reffered_role')
		->dropListSQL("
		   SELECT prddesc
             FROM webset.disdef_participantrolesdef
            WHERE vndrefid = VNDREFID
            ORDER BY seq_num
        "))
		->highlightField(false)
		->width('35%');

	$edit->addControl(FFInput::factory(''))
		->caption('Case Manager (if assigned)')
		->width('25%')
		->value(ucwords(strtolower($student->get('cmname'))))
		->sqlField('stdcmanager');

	if (IDEACore::disParam(114) == "Y") {
		$edit->addControl('Copy of Bill of Rights given to parent(s) on', 'date')
			->sqlSavable(true)
			->value($student->get('parentrightdt'))
			->name('parentrightdt');

		$edit->addControl('Procedural Safeguards given to parent(s) on', 'date')
			->sqlSavable(true)
			->value($student->get('stdprocsafeguarddt'))
			->name('stdprocsafeguarddt');
			$edit->setPostsaveCallback('saveTSDates', './gen_info_edit.inc.php');
	}

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl("evalproc_id", "hidden")->value($evalproc_id)->sqlField('eprefid');
	$edit->addControl("tsrefid", "hidden")->name('tsrefid')->value($tsRefID);

	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('', array('dskey' => $dskey));
	$edit->saveAndAdd = false;
	$edit->saveAndEdit = true;

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.es_std_er_generalinfo')
			->setKeyField('eprefid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>
