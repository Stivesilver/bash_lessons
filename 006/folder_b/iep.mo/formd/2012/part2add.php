<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$RefID = io::get('RefID');

	$edit = new EditClass('edit1', $RefID);

	$edit->title = 'Add/Edit State Accommodations';

	$edit->setSourceTable('webset.std_form_d_acc', 'refid');

	$edit->addGroup('General Information');
	$edit->topButtons = true;

	if ($RefID == 0) {
		$condition = "
           stdrefid = " . $tsRefID . "
           AND syrefid = " . $stdIEPYear . "
        ";
	} else {
		$condition =  "refid = " . $RefID;
	}

	$edit->addControl('Subject', 'select')
		->name('subject')
		->value(db::execSQL("
            SELECT cat
              FROM webset.std_form_d_acc std
                   INNER JOIN webset.statedef_aa_acc acc ON acc.accrefid = std.accrefid
             WHERE $condition
            ORDER BY std.refid DESC

        ")->getOne())
		->sql("
            SELECT code,
                   progdesc
              FROM webset.statedef_aa_prog
             WHERE (enddate IS NULL or now()< enddate)
               AND part2 = 'Y'
             ORDER BY seqnum, progdesc
        ")
		->req();

	if ($RefID == 0) {
		$condition = "
           AND NOT EXISTS (SELECT 1
                             FROM webset.std_form_d_acc std
                            WHERE acc.accrefid = std.accrefid
                              AND stdrefid = " . $tsRefID . "
                              AND syrefid = " . $stdIEPYear . ")
        ";
	} else {
		$condition = '';
	}

	$edit->addControl('Accommodation', 'select_radio')
		->name('accrefid')
		->sqlField('accrefid')
		->sql("
            SELECT accrefid,
                   catdesc || ' - ' || accdesc
              FROM webset.statedef_aa_cat cat
                   INNER JOIN webset.statedef_aa_acc acc ON cat.catrefid = acc.acccat
             WHERE cat = 'VALUE_01'
             	" . $condition . "
             ORDER BY catrefid, seq_num
        ")
		->req()
		->breakRow()
		->tie('subject');

	$edit->addControl('Specify')
		->name('acc_oth')
		->sqlField('acc_oth')
		->showIf('accrefid', db::execSQL("
                                  SELECT accrefid
                                    FROM webset.statedef_aa_acc
                                   WHERE accdesc ILIKE '%other%'
                                 ")->indexAll())
		->size(50);

	$edit->addGroup('Update Information', true);
	$edit->addControl("Last User", "protected")->value($_SESSION["s_userUID"])->sqlField('lastuser');
	$edit->addControl("Last Update", "protected")->value(date("m-d-Y H:i:s"))->sqlField('lastupdate');
	$edit->addControl("Student ID", "hidden")->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl("IEP Year", "hidden")->value($stdIEPYear)->sqlField('syrefid');
	$edit->addControl('Sp Considerations ID', 'hidden')->value(io::geti('spconsid'))->name('spconsid');

	$edit->finishURL = CoreUtils::getURL('part2.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('part2.php', array('dskey' => $dskey));

	$edit->addSQLConstraint('This Accommodations has been already added',
		"
		SELECT 1
		  FROM webset.std_form_d_acc
		 WHERE stdrefid = " . $tsRefID . "
	       AND syrefid = " . $stdIEPYear . "
		   AND accrefid = [accrefid]
	       AND refid != AF_REFID
    ");

	$edit->printEdit();

?>