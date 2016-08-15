<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$edit       = new EditClass('edit1', io::geti('RefID'));

	$edit->setSourceTable('webset_tx.std_sat_beh_prog', 'brefid');

	$edit->title = "Add/Edit Positive Proactive Discipline";

	$edit->addGroup("General Information");
	$edit->addControl("Program", "select")
		->name('item_id')
		->sql(IDEADef::getValidValueSql('TX_SAT_Pos_Proact', 'refid, validvalue'))
		->req(true)
		->sqlField('item_id');

	$edit->addControl("Specify Program", "edit")
		->showIf(
			'item_id',
			db::execSQL(
				IDEADef::getValidValueSql(
					array('TX_SAT_Pos_Proact', "validvalue LIKE '%Other%'"),
					'refid')
			)->indexAll()
		)
		->sqlField('item_other')
		->size(50);

	$edit->addControl("Program Description", "textarea")
		->sqlField('item_desc')
		->css("width",  "100%")
		->css("height", "50px");

	$edit->addControl("Start Date:", "date")->sqlField('date_beg');
	$edit->addControl("Beg Date:", "date")->sqlField('date_end');
	$edit->addUpdateInformation();
	$edit->addControl("", "hidden")->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl("", "hidden")->value($stdIEPYear)->sqlField('iepyear');
	$edit->addControl("", "hidden")->value(1)->sqlField('area');
	$edit->printEdit();

?>