<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');

	$edit = new EditClass('edit1', io::geti('RefID'));

	$edit->setSourceTable('webset_tx.std_sat_coreprog', 'prefid');

	$edit->title     = "Add/Edit Core Program";
	$edit->finishURL = CoreUtils::getURL('22_coreprog.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('22_coreprog.php', array('dskey' => $dskey));

	$edit->addGroup("General Information");
	$edit->addControl("Core Program", "select")
		->sqlField('item_id')
		->name('item_id')
		->sql("
			SELECT items.refid,
                   aaadesc || ' ' ||
                   category_name || ' ' ||
                   item_name
              FROM webset_tx.def_sat_program_item items
                   INNER JOIN webset_tx.def_sat_program_cat cat ON category_id = cat.refid
                   INNER JOIN webset.statedef_assess_acc ON  subject_id = aaarefid
             WHERE (items.enddate IS NULL or now()< items.enddate)
             ORDER BY items.enddate desc, aaadesc, cat.seqnum, items.seqnum, item_name
            ");

	$edit->addControl("Specify Program", "edit")
		->sqlField('item_other')
		->showIf(
			'item_id',
			# select rows with string 'Other'
			db::execSQL("
				SELECT items.refid
	              FROM webset_tx.def_sat_program_item items
	                   INNER JOIN webset_tx.def_sat_program_cat cat ON category_id = cat.refid
	                   INNER JOIN webset.statedef_assess_acc ON  subject_id = aaarefid
	             WHERE (items.enddate IS NULL or now()< items.enddate)
	               AND item_name LIKE '%Other%'
	             ORDER BY items.enddate desc, aaadesc, cat.seqnum, items.seqnum, item_name
			")->indexAll()
		)
		->size(50);

	$edit->addControl("Start Date:", "date")->sqlField('program_date');
	$edit->addControl("End Date:", "date")->sqlField('program_end');
	$edit->addUpdateInformation();
	$edit->addControl("", "hidden")
		->value($tsRefID)
		->sqlField('stdrefid');

	$edit->addControl("", "hidden")
		->value($stdIEPYear)
		->sqlField('iepyear');

	$edit->printEdit();

?>