<?PHP

	Security::init();

	$RefID = io::get("RefID");

	$edit = new editClass("edit1", $RefID);

	$edit->title = "Add/Edit IEP Formats";

	$edit->setSourceTable('webset.sped_menu_set', 'srefid');

	$edit->addGroup('General Information');
	$edit->addControl("State")
		->sqlField('state')
		->width('5%')
		->req();

	$edit->addControl("IEP Format")
		->sqlField('shortdesc')
		->width('50%')
		->req();

	$edit->addControl("Description", "textarea")
		->width('90%')
		->sqlField('longdesc');

	$edit->addControl("Generate File")
		->sqlField('gen_file')
		->width('90%')
		->req();

	$edit->addControl("Blocks File")
		->sqlField('gen_block')
		->width('90%')
		->req();

	$edit->addControl(FFSelect::factory("Default Set"))
		->sql("
			SELECT validvalueid, validvalue
              FROM webset.glb_validvalues
		     WHERE valuename = 'YesNo'
             ORDER BY validvalueid"
		)
		->sqlField('def_sw');

	$edit->addControl("Record Deactivation Date", "DATE")->sqlField('enddate');
	$edit->addUpdateInformation();

	$edit->finishURL = "./menuSet.php";
	$edit->cancelURL = "./menuSet.php";

	$edit->printEdit();

	if ($RefID > 0) {

		$tabs = new UITabs('tabs');
		$tabs->indent(10);
		$tabs->addTab('IEP Blocks', CoreUtils::getURL('./iep_blocksMain.php', array('iep' => $RefID)));
		$tabs->addTab('Doc Types', CoreUtils::getURL('./iep_docs_list.php', array('iepformat' => $RefID)));
		$tabs->addTab('Options', CoreUtils::getURL('./iniset_list.php', array('iepformat' => $RefID)));

		print $tabs->toHTML();
	}
?>
