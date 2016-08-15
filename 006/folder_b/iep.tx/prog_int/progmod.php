<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$area = io::get('area');
	$blank_page_url = CoreUtils::getURL('progmod_blank.ajax.php', array('dskey' => $dskey, 'area' => $area));

	$list = new ListClass();

	$list->title = 'Program Interventions and Accommodations';

	$list->SQL = "
        SELECT 'S' || sub_mod_refid::varchar,

               COALESCE(accommodation, sub_mod_desc) || COALESCE(' (Other Subject: ' || subject_own || ')', ''),

               plpgsql_recs_to_str('
                   SELECT sub_desc AS column
                     FROM webset_tx.std_pi std
                          INNER JOIN webset_tx.def_pi_subjects ON SUBSTRING(std.mod_sub_id FROM ''_(.+)'')::int = sub_refid
                    WHERE std_refid = " . $tsRefID . "
					  AND iep_year = " . $stdIEPYear . "
                      AND accomod_mode = ''S''
                      AND SUBSTRING(std.mod_sub_id FROM ''(.+)_'')::int = ' || sub_mod_refid || '
                    ORDER BY seqnum, sub_desc', ', '),
			   acc.seqnum as accseqnum,
			   cat.seqnum as catseqnum


          FROM webset_tx.def_pi_modifications_dtl acc
               INNER JOIN webset_tx.def_pi_modifications_mst cat ON cat.mod_refid = acc.mod_refid
			   LEFT OUTER JOIN webset_tx.std_pi_own ON state_accomodation_id = sub_mod_refid AND stdrefid = " . $tsRefID . " AND iepyear = " . $stdIEPYear . "
         WHERE EXISTS (SELECT 1
                         FROM webset_tx.std_pi std
                        WHERE std_refid = " . $tsRefID . "
						  AND iep_year = " . $stdIEPYear . "
						  AND accomod_mode = 'S'
                          AND SUBSTRING(std.mod_sub_id FROM '(.+)_')::int = acc.sub_mod_refid)
		   AND area_id = " . $area . "

         UNION

		 SELECT 'O' || refid::varchar,
               accommodation,

               plpgsql_recs_to_str('
                   SELECT sub_desc AS column
                     FROM webset_tx.std_pi std
                          INNER JOIN webset_tx.def_pi_subjects ON SUBSTRING(std.mod_sub_id FROM ''_(.+)'')::int = sub_refid
                    WHERE std_refid = " . $tsRefID . "
					  AND iep_year = " . $stdIEPYear . "
                      AND accomod_mode = ''O''
                      AND SUBSTRING(std.mod_sub_id FROM ''(.+)_'')::int = ' || acc.refid || '
                    ORDER BY seqnum, sub_desc', ', '),
			   acc.seqnum as accseqnum,
			   cat.seqnum as catseqnum

          FROM webset_tx.std_pi_own acc
               INNER JOIN webset_tx.def_pi_modifications_mst cat ON cat.mod_refid = acc.category_id
         WHERE stdrefid = " . $tsRefID . "
		   AND iepyear = " . $stdIEPYear . "
		   AND area_id = " . $area . "
		   AND state_accomodation_id IS NULL
         ORDER BY catseqnum, accseqnum, 3
    ";

	$list->addColumn('Interventions/Accommodations');
	$list->addColumn('Subjects');
	$list->addColumn('Order #');

	$list->addURL = CoreUtils::getURL('./progmod_add.php', array('dskey' => $dskey, 'area' => $area));
	$list->editURL = CoreUtils::getURL('./progmod_add.php', array('dskey' => $dskey, 'area' => $area));

	$list->addButton(
		FFIDEAExportButton::factory()
		->setTable('webset_tx.def_pi_modifications_dtl')
		->setKeyField("sub_mod_refid::varchar")
		->applyListClassMode()
	);

	$button = new IDEAPopulateIEPYear($dskey, $area, '/apps/idea/iep.tx/prog_int/progmod_copy_list.php');
	$listButton = $button->getPopulateButton();
	$list->addButton($listButton);

	$list->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
			->addItem('Blank Page', 'api.ajax.process(ProcessType.REPORT, ' . json_encode($blank_page_url) . ')', './img/PDF.png')
			//->addItem('Blank Page', 'api.window.open("ProcessType.REPORT", ' . json_encode($blank_page_url) . ')')
	);

	$list->addRecordsProcess('Delete')
		->width('80px')
		->message('Do you really want to delete selected Modifications/Accommodations?')
		->url(CoreUtils::getURL('progmod_delete.ajax.php', array('dskey' => $dskey)))
		->type(ListClassProcess::DATA_UPDATE)
		->progressBar(false);

	$list->printList();
?>

