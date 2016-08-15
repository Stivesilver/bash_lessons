<?PHP

	Security::init();

	$edit = new editClass('edit1', $RefID);

	// --- General Properties ---
	if ($RefID == 0) {
		$edit->title = "Add Information";
	} else {
		$edit->title = "Edit Information";
	}

	$edit->setSourceTable('webset.glb_validvalues', 'refid');

	// --- Edit Controls ---
	$edit->addGroup("General Information");
	$edit->addControl("Category", "EDIT")->sqlField('valuename');
	$edit->addControl("Value Text", "EDIT")->sqlField('validvalue');
	$edit->addControl("Value ID", "EDIT")->sqlField('validvalueid');
	$edit->addControl("Sequence Number", "INTEGER")->sqlField('sequence_number');
	$edit->addUpdateInformation();

	// --- EditPage Actions ---
	$edit->cancelURL = CoreUtils::getURL('./validvalues_list.php');
	$edit->finishURL = CoreUtils::getURL('./validvalues_list.php');

	$edit->printEdit();

?>
