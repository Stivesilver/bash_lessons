<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$RefID = io::get('RefID');

	$edit = new EditClass('edit1', $tsRefID);

	$edit->title = 'Allowed User Access';

	$edit->setSourceTable('webset.sys_teacherstudentassignment', 'tsrefid');

	$edit->addGroup('General Information');

	$edit->addControl(FFMultiSelect::factory())
		->sqlField('miprefid')
		->rows(9)
		->name('miprefid')
		->sqlTable(
			'webset.std_useraccess',
			'stdrefid',
			array(
				'stdrefid' => $tsRefID,
				'lastuser' => SystemCore::$userUID,
				'lastupdate' => date('m-d-Y H:i:s')
			)
		)
		->setSearchList(
			ListClassContent::factory('')
				->addColumn('Users')
				->addSearchField('Last Name', 'umlastname', FormFieldMatch::SUBSTRING)
				->addSearchField('First Name', 'umfirstname', FormFieldMatch::SUBSTRING)
				->setSQL("
					SELECT sys_usermst.umrefid,
		                   COALESCE(umlastname || ', ', '') || COALESCE(umfirstname, '') ,
		                   umtitle
		              FROM sys_usermst
		             WHERE vndrefid = VNDREFID
		               AND COALESCE(um_internal, TRUE)
		             ORDER BY 2
				")
		);

	$edit->addControl(FFInput::factory())
		->hide(true)
		->sqlField('tsrefid')
		->value($tsRefID);

	$edit->finishURL = CoreUtils::getURL('ua_useraccess.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('ua_useraccess.php', array('dskey' => $dskey));

	$edit->saveAndAdd = false;
	$edit->saveAndEdit = true;

	$edit->firstCellWidth = "30%";

	$edit->printEdit();
?>
