<?
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$evalproc_id = $ds->safeGet('evalproc_id');
	$screenURL = $ds->safeGet('screenURL');

	$edit = new EditClass('edit1', $evalproc_id);

	$edit->title = 'Provide Copy of Evaluation Report to the parents';

	$edit->setSourceTable('webset.es_std_er_providecopy', 'eprefid');

	$edit->addGroup('A copy of the evaluation report including documentation of determination of eligibility was provided to the parent(s)/guardian(s)');

	$edit->addControl(FFInputDropList::factory('By')
		->sqlField('nametitle')
		->dropListSQL("
            SELECT umrefid, umfirstname || ' ' || umlastname || COALESCE(' / ' || umtitle, '')
              FROM public.sys_usermst
             WHERE vndrefid = VNDREFID
               AND um_internal
             ORDER BY UPPER(umlastname), UPPER(umfirstname)
        "))
		->highlightField(false)
		->width('400px');

	$edit->addControl('On', 'date')
		->sqlField('date_provided')
		->width('50%');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl("evalproc_id", "hidden")->value($evalproc_id)->sqlField('eprefid');

	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('', array('dskey' => $dskey));
	$edit->saveAndAdd = false;
	$edit->saveAndEdit = true;

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.es_std_er_providecopy')
			->setKeyField('eprefid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>
