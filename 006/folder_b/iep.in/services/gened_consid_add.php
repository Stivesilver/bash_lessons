<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$student = new IDEAStudent($tsRefID);
	$set_ini = IDEAFormat::getIniOptions();

	$edit = new EditClass('edit1', io::get('RefID'));

	$edit->title = 'Add/Edit ' . $set_ini["in_general_education_considerations"];
	$edit->firstCellWidth = '30%';

	$edit->setSourceTable('webset.std_in_ed_consid', 'conrefid');

	$edit->addGroup('General Information');

	$edit->addControl('Area', 'select')
		->sqlField('progrefid')
		->name('progrefid')
		->sql("
			SELECT stsrefid,
			       macdesc || ' -> ' || stsdesc
			  FROM webset.statedef_mod_acc
			       LEFT OUTER JOIN webset.statedef_mod_acc_cat ON webset.statedef_mod_acc_cat.macrefid = webset.statedef_mod_acc.macrefid
			 WHERE webset.statedef_mod_acc.screfid = " . VNDState::factory()->id . "
			   AND LOWER(modaccommodationsw) = 'y'
			 ORDER BY stsseq, stscode, stsdesc
		")
		->emptyOption(TRUE)
		->req();

	$edit->addControl('Specify')
		->sqlField('other')
		->name('other')
		->showIf('progrefid', db::execSQL("
                                  SELECT stsrefid
                                    FROM webset.statedef_mod_acc
                                   WHERE substring(lower(stsdesc), 1, 5) = 'other'
                                 ")->indexAll())
		->size(50);

	$edit->addControl('Narrative', 'textarea')
		->sqlField('narr')
		->css('width', '100%')
		->css('height', '150px');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->finishURL = CoreUtils::getURL('gened_consid.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('gened_consid.php', array('dskey' => $dskey));

	$edit->printEdit();
?>