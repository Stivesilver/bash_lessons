<?php

	Security::init();

	$dskey      = io::get('dskey');
	$RefID      = io::geti('RefID');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');

	$SQL = "
		INSERT INTO webset_tx.std_speech_informal (stdrefid, iepyear)
        SELECT $tsRefID, $stdIEPYear
         WHERE NOT EXISTS (SELECT 1
         					 FROM webset_tx.std_speech_informal
                            WHERE stdrefid = $tsRefID
                              AND iepyear = $stdIEPYear)
        ";
	$result = db::execSQL($SQL);
	if (!$result) se($SQL);

	$RefID = db::execSQL("
		SELECT refid
		  FROM webset_tx.std_speech_informal
         WHERE stdrefid = $tsRefID
           AND iepyear = $stdIEPYear
        ")->getOne();

	$edit = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset_tx.std_speech_informal', 'refid');

	$edit->title = "Informal Assessment";

	$edit->addGroup("General Information");
	$edit->addTab("Part 1");
	$edit->addControl("Informal Assessment procedures used:", "textarea")
		->sqlField('informal')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addGroup("Syntax");
	$edit->addControl("Commensurate with age and/or cognitive ability:", "select_check")
		->sqlField('syn_cognitive')
		->displaySelectAllButton(false)
		->sql("SELECT 'Y', ''");

	$edit->addControl("Relative weakness:", "select_check")
		->sqlField('syn_relative')
		->displaySelectAllButton(false)
		->sql("SELECT 'Y', ''");

	$edit->addControl("Relative weakness:", "textarea")
		->sqlField('syn_relative_txt')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addControl("Significant weakness:", "select_check")
		->sqlField('syn_significant')
		->displaySelectAllButton(false)
		->sql("SELECT 'Y', ''");

	$edit->addControl("Significant weakness:", "textarea")
		->sqlField('syn_significant_txt')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addGroup("Semantics");
	$edit->addControl("Commensurate with age and/or cognitive ability:", "select_check")
		->sqlField('sem_cognitive')
		->displaySelectAllButton(false)
		->sql("SELECT 'Y', ''");

	$edit->addControl("Relative weakness:", "select_check")
		->sqlField('sem_relative')
		->displaySelectAllButton(false)
		->sql("SELECT 'Y', ''");

	$edit->addControl("Relative weakness:", "textarea")
		->sqlField('sem_relative_txt')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addControl("Significant weakness:", "select_check")
		->sqlField('sem_significant')
		->displaySelectAllButton(false)
		->sql("SELECT 'Y', ''");

	$edit->addControl("Significant weakness:", "textarea")
		->sqlField('sem_significant_txt')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addTab("Part 2");
	$edit->addControl("Commensurate with age and/or cognitive ability:", "select_check")
		->sqlField('prag_cognitive')
		->displaySelectAllButton(false)
		->sql("SELECT 'Y', ''");

	$edit->addControl("Relative weakness:", "select_check")
		->sqlField('prag_relative')
		->displaySelectAllButton(false)
		->sql("SELECT 'Y', ''");

	$edit->addControl("Relative weakness:", "textarea")
		->sqlField('prag_relative_txt')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addControl("Significant weakness:", "select_check")
		->sqlField('prag_significant')
		->displaySelectAllButton(false)
		->sql("SELECT 'Y', ''");

	$edit->addControl("Significant weakness:", "textarea")
		->sqlField('prag_significant_txt')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addGroup("Metalinguistics");
	$edit->addControl("Commensurate with age and/or cognitive ability:", "select_check")
		->sqlField('met_cognitive')
		->displaySelectAllButton(false)
		->sql("SELECT 'Y', ''");

	$edit->addControl("Relative weakness:", "select_check")
		->sqlField('met_relative')
		->displaySelectAllButton(false)
		->sql("SELECT 'Y', ''");

	$edit->addControl("Relative weakness:", "textarea")
		->sqlField('met_relative_txt')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addControl("Significant weakness:", "select_check")
		->sqlField('met_significant')
		->displaySelectAllButton(false)
		->sql("SELECT 'Y', ''");

	$edit->addControl("Significant weakness:", "textarea")
		->sqlField('met_significant_txt')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addGroup("Comments");
	$edit->addControl("Comments", "textarea")
		->sqlField('comments')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addUpdateInformation();

	$edit->addControl("", "hidden")
		->value($tsRefID)
		->sqlField('stdrefid');

	$edit->addControl("", "hidden")
		->value($stdIEPYear)->sqlField('iepyear');

	$edit->saveAndEdit    = true;
	$edit->saveAndAdd     = false;
	$edit->firstCellWidth = "30%";
	$edit->finishURL      = 'javascript:parent.parent.selectNext()';

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();

?>