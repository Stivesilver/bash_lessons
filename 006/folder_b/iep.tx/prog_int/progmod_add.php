<?php

	Security::init();

	$dskey = io::get('dskey');
	$mode = substr(io::get('RefID'), 0, 1) == 'O' ? 'O' : 'S';
	$RefID = (int)substr(io::get('RefID'), 1);
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$area = io::get('area');

	if(io::exists(('subj'))) {
		$subjs = io::post('subj');
	} else {
		$subjs = db::execSQL("
            SELECT plpgsql_recs_to_str('
                   SELECT sub_refid::varchar AS column
                     FROM webset_tx.std_pi std
                          INNER JOIN webset_tx.def_pi_subjects ON SUBSTRING(std.mod_sub_id FROM ''_(.+)'')::int = sub_refid
                    WHERE std_refid = " . $tsRefID . "
					  AND iep_year = " . $stdIEPYear . "
                      AND SUBSTRING(std.mod_sub_id FROM ''(.+)_'')::int = " . $RefID . "
                    ORDER BY seqnum, sub_desc', ',')
        ")->getOne();
	}

	$edit = new EditClass("edit1", $RefID);

	$edit->title = 'Add/Edit Program Interventions and Accommodations';
	$edit->firstCellWidth = '30%';

	$edit->addGroup("General Information");

	$edit->addControl("", "select_radio")
		->name('mode')
		->value($mode)
		->data(array('S' => 'Common Accommodation', 'O' => 'Own Accommodation'))
		->disabled($RefID > 0);

	$edit->addControl("Modification/Accommodation", "select")
		->sqlField('sub_mod_refid')
		->name('sub_mod_refid')
		->sql("
            SELECT sub_mod_refid, sub_mod_desc
              FROM webset_tx.def_pi_modifications_dtl acc
                   INNER JOIN webset_tx.def_pi_modifications_mst cat ON cat.mod_refid = acc.mod_refid
             WHERE (acc.end_date IS NULL OR now()< acc.end_date)
			   AND (cat.end_date IS NULL OR now()< cat.end_date)
			   AND area_id = " . $area . "
               " . ($RefID > 0 ? " AND sub_mod_refid = " . $RefID : " AND sub_mod_refid NOT IN
																	   (SELECT SUBSTRING(mod_sub_id FROM '(.+)_')::int
																		  FROM webset_tx.std_pi
																	     WHERE std_refid = " . $tsRefID . "
																	       AND iep_year = " . $stdIEPYear . ")
               ") . "
             ORDER BY cat.seqnum, acc.seqnum, sub_mod_desc
        ")
		->showIf('mode', 'S');

	$edit->addControl('Accommodation Title (adjust if neeed)')
		->name('accommodation')
		->value(
			db::execSQL("
				SELECT accommodation
				  FROM webset_tx.std_pi_own
				 WHERE state_accomodation_id = " . $RefID . "
				   AND stdrefid = " . $tsRefID . "
				   AND iepyear = " . $stdIEPYear . "
			")->getOne()
		)
		->sql("
			SELECT COALESCE(accommodation, sub_mod_desc)
			  FROM webset_tx.def_pi_modifications_dtl
			       LEFT OUTER JOIN webset_tx.std_pi_own ON state_accomodation_id = sub_mod_refid AND stdrefid = " . $tsRefID . " AND iepyear = " . $stdIEPYear . "
			 WHERE sub_mod_refid = VALUE_01
		")
		->tie('sub_mod_refid')
		->size(80)
		->showIf('mode', 'S');

	$edit->addControl("Category", "select")
		->sqlField('category_id')
		->name('category_id')
		->sql("
            SELECT mod_refid,
                   mod_desc
              FROM webset_tx.def_pi_modifications_mst
             WHERE (end_date IS NULL OR now()< end_date)
               AND area_id = " . $area . "
             ORDER BY seqnum
        ")
		->showIf('mode', 'O');

	$edit->addControl('Accommodation')
		->name('own_accommodation')
		->value(
			db::execSQL("
				SELECT accommodation
				  FROM webset_tx.std_pi_own
				 WHERE refid = " . $RefID . "
			")->getOne()
		)
		->size(80)
		->showIf('mode', 'O');

	$edit->addControl('Order #', 'integer')
		->name('seqnum')
		->value(
			db::execSQL("
				SELECT seqnum
				  FROM webset_tx.std_pi_own
				 WHERE refid = " . $RefID . "
			")->getOne()
		)
		->showIf('mode', 'O');

	$edit->addControl("Subject", "select_check")
		->name('subjects')
		->sql("
            SELECT sub_refid, sub_desc
              FROM webset_tx.def_pi_subjects
             WHERE (end_date IS NULL OR now()< end_date)
               AND COALESCE(vndrefid, VNDREFID) = VNDREFID
             ORDER BY seqnum, sub_desc
        ")
		->value($subjs)
		->req()
		->breakRow();

	$edit->addControl('Other Subject')
		->name('subject_own')
		->value(
			db::execSQL("
				SELECT subject_own
				  FROM webset_tx.std_pi_own
				 WHERE state_accomodation_id = " . $RefID . "
				   AND stdrefid = " . $tsRefID . "
				   AND iepyear = " . $stdIEPYear . "
			")->getOne()
		)
		->size(50)
		->showIf('mode', 'S');

	$edit->addGroup("Update Information", true);
	$edit->addControl("Last User", "protected")->value($_SESSION["s_userUID"])->sqlField('lastuser');
	$edit->addControl("Last Update", "protected")->value(date("m-d-Y H:i:s"))->sqlField('lastupdate');

	$edit->finishURL = CoreUtils::getURL('./progmod_save.php', array('dskey' => $dskey, 'area' => $area));
	$edit->saveURL = CoreUtils::getURL('./progmod_save.php', array('dskey' => $dskey, 'area' => $area));
	$edit->cancelURL = CoreUtils::getURL('./progmod.php', array('dskey' => $dskey, 'area' => $area));

	#Avoid Save and Add if record existing record
	if ($RefID > 0) $edit->saveAndAdd = false;

	$edit->printEdit();
?>

<script>
	function addNext(dskey, area, subj, mode) {
		api.goto(
			'./progmod_add.php',
			null,
			{
				'dskey' : dskey,
				'area' : area,
				'subj' : subj,
				'mode' : mode
			}
		)
	}
</script>
