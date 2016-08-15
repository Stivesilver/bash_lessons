<?
	Security::init();

	$RefID = io::geti('RefID');

	$edit = new editClass('edit1', $RefID);

	$edit->setSourceTable('webset.statedef_aa_act_cat', 'cactrefid');

	$edit->title = "Add/Edit ACT Category";

	$edit->addGroup("General Information");

	$edit->addControl("Category", "EDIT")
		->sqlField('catname')
		->req();
	$edit->addControl("Category Description", "TEXTAREA")->sqlField('catdesc');
	$edit->addControl("Sequence", "INTEGER")->sqlField('seqnum');
	$edit->addControl("Expire Date", "DATE")->sqlField('enddate');
	$edit->addUpdateInformation();

	$edit->finishURL = CoreUtils::getURL('./act_cat_list.php');
	$edit->cancelURL = CoreUtils::getURL('./act_cat_list.php');

	$edit->printEdit();

?>
