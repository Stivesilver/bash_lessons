<?
	Security::init();

	$RefID = io::geti('RefID');

	$edit = new editClass('edit1', $RefID);

	$edit->setSourceTable('webset.statedef_aa_act_acc', 'actrefid');

	$edit->title = "Add/Edit ACT Accommodations";

	$edit->addGroup("General Information");

	$edit->addControl(FFSelect::factory("Category"))
		->sql("
			SELECT cactrefid,
	               catname
	          FROM webset.statedef_aa_act_cat
	         WHERE (enddate IS NULL OR now()< enddate)
	         ORDER BY seqnum, catname
		")
		->sqlField('actcat')
		->req();

	$edit->addControl("Accommodation")
		->sqlField('actname')
		->req();

	$edit->addControl("Subcat")
		->sqlField('actsubcat');

	$edit->addControl(FFSwitchYN::factory('other'))
		->sqlField('other');

	$edit->addControl("Sequence", "INTEGER")->sqlField('seqnum');
	$edit->addControl("Expire Date", "DATE")->sqlField('enddate');
	$edit->addUpdateInformation();

	$edit->finishURL = CoreUtils::getURL('./act_acc_list.php');
	$edit->cancelURL = CoreUtils::getURL('./act_acc_list.php');

	$edit->printEdit();

?>
