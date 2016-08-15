<?
	Security::init();

	$staterefid = io::get('staterefid');

	$RefID = io::geti('RefID');

	$edit = new editClass('edit1', $RefID);

	$edit->setSourceTable('webset.statedef_aa_acc', 'accrefid');

	$edit->title = "Add/Edit Assessment";

	$edit->addGroup("General Information");

	$edit->addControl(FFSelect::factory("Category"))
		->sql("
			SELECT catrefid,
                   catdesc
			  FROM webset.statedef_aa_cat
		     WHERE screfid = " . $staterefid . "
               AND (enddate IS NULL or now()< enddate)
		     ORDER BY catdesc
		")
		->sqlField('acccat')
		->req();

	$edit->addControl("Assessment Code")
		->sqlField('acccode');

	$edit->addControl("Assessment Description")
		->sqlField('accdesc')
		->width('100%');

	$edit->addControl(FFMultiSelect::factory("Cat"))
		->sql("
			SELECT progrefid,
                   progdesc
              FROM webset.statedef_aa_prog
             WHERE (enddate IS NULL or now()< enddate)
		      AND screfid = " . $staterefid . "
             ORDER BY seqnum, progdesc
		")
		->sqlField('cat')
		->req();

	$edit->addControl("Sequence", "INTEGER")->sqlField('seq_num');
	$edit->addControl("Expire Date", "DATE")->sqlField('enddate');
	$edit->addUpdateInformation();

	$edit->finishURL = CoreUtils::getURL('./acc_list.php', array('staterefid' => $staterefid));
	$edit->cancelURL = CoreUtils::getURL('./acc_list.php', array('staterefid' => $staterefid));

	$edit->printEdit();

?>
