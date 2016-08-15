<?php

	Security::init();

	$dskey         = io::get('dskey');
	$RefID         = io::get('RefID');
	$ds            = DataStorage::factory($dskey, true);
	$stdSchoolYear = $ds->safeGet('stdIEPYear');
	$tsRefID       = $ds->get('tsRefID');

	$SQL = "
		INSERT INTO webset_tx.std_fie_language (stdrefid, iepyear)
        SELECT $tsRefID, $stdSchoolYear
         WHERE NOT EXISTS (SELECT 1
                             FROM webset_tx.std_fie_language
                            WHERE stdrefid = $tsRefID
                              AND iepyear = $stdSchoolYear
                           )
      	";

	$result = db::execSQL($SQL);
	if (!$result) se($SQL);

	$RefID = db::execSQL("
		SELECT lrefid
		  FROM webset_tx.std_fie_language
         WHERE stdrefid = $tsRefID
           AND iepyear = $stdSchoolYear
        ")->getOne();

	$edit = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset_tx.std_fie_language', 'lrefid');

	$edit->title       = "Language/Communicative Status";
	$edit->saveAndEdit = true;
	$edit->topButtons  = true;
	$edit->finishURL   = 'javascript:nextItem()';

	$edit->addGroup("General Information");
	$edit->addControl("Student's dominant language", "select")
		->sqlField('dominant')
		->name('dominant')
		->data(
			array(
				'Y' => 'English',
				'N' => 'Spanish',
				'O' => 'Other'
			)
		);

	$edit->addControl("Specify Language", "edit")
		->sqlField('dominant_oth')
		->showIf('dominant', 'O')
		->size(30);

	$edit->addControl("Student expresses best:", "select")
		->sqlField('express_best')
		->name('express_best')
		->data(
			array(
				'R' => 'Orally',
				'O' => 'Other'
			)
		);

	$edit->addControl("Specify Language", "edit")
		->sqlField('express_oth')
		->showIf('express_best', 'O')
		->size(30);

	$edit->addGroup("Level of Proficiency");
	$edit->addControl("English - Receptive", "select_radio")
		->sqlField('eng_lep_rec')
		->data(
			array(
				1 => 'Above Average',
				2 => 'Average',
				3 => 'Below Average'
			)
		);

	$edit->addControl("English - Expressive", "select_radio")
		->sqlField('eng_lep_exp')
		->data(
			array(
				1 => 'Above Average',
				2 => 'Average',
				3 => 'Below Average'
			)
		);

	$edit->addControl("Other language", "edit")
		->sqlField('oth_lep_lng')
		->size(50);

	$edit->addControl("Other - Receptive", "select_radio")
		->sqlField('oth_lep_rec')
		->sql("SELECT 1, 'Above Average' UNION SELECT 2, 'Average' UNION SELECT 3, 'Below Average' ORDER BY 1");

	$edit->addControl("Other - Expressive", "select_radio")
		->sqlField('oth_lep_exp')
		->sql("SELECT 1, 'Above Average' UNION SELECT 2, 'Average' UNION SELECT 3, 'Below Average' ORDER BY 1");

	$edit->addControl("Other Info", "textarea")
		->sqlField('text_devider')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addGroup("LPAC");
	$edit->addControl("Name of Test", "edit")
		->sqlField('lpac_test')
		->size(80);

	$edit->addControl("Score/Results", "edit")
		->sqlField('lpac_score')
		->size(40);

	$edit->addControl("This student is limited English proficient", "select_radio")
		->sqlField('limited_prof')
		->data(
			array(
				'N' => 'No',
				'Y' => 'Yes'
			)
		);

	$edit->addControl("If yes, give LPAC recommendations", "textarea")
		->sqlField('limited_recomm')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addControl("Evaluation was conducted", "select")
		->sqlField('conducted')
		->sql("
			SELECT validvalueid, validvalue
              FROM webset.glb_validvalues
		     WHERE valuename = 'TXLEP'
             ORDER BY validvalueid
            ");

	$edit->addControl("Specify", "edit")
		->sqlField('conducted_text')
		->size(80);

	$edit->addControl("Describe other pertinent findings", "textarea")
		->sqlField('findings')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addUpdateInformation();

	$edit->addControl("", "hidden")
		->value($tsRefID)
		->sqlField('stdrefid');

	$edit->addControl("", "hidden")
		->value($stdSchoolYear)
		->sqlField('iepyear');

	$edit->finishURL  = 'javascript:parent.parent.selectNext()';
	$edit->saveAndAdd = false;

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset_tx.std_fie_language')
			->setKeyField('lrefid')
			->applyEditClassMode()
	);

	$edit->printEdit();

?>