<?php

	Security::init();

	$RefID = io::get('RefID', true);

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$area_id = io::get('area');
	$path = io::get('path', true);
	$set_ini = IDEAFormat::getIniOptions();

	$list = new listClass();

	$list->title = $set_ini['sp_consid_title'];

	$list->SQL = "
         SELECT 'S_' || sub_mod_refid::varchar || '_' || $RefID,

               COALESCE(accommodation, sub_mod_desc) || COALESCE(' (Other Subject: ' || subject_own || ')', ''),

               plpgsql_recs_to_str('
                   SELECT sub_desc AS column
                     FROM webset_tx.std_pi std
                          INNER JOIN webset_tx.def_pi_subjects ON SUBSTRING(std.mod_sub_id FROM ''_(.+)'')::int = sub_refid
                    WHERE std_refid = " . $tsRefID . "
					  AND iep_year = " . $RefID . "
                      AND accomod_mode = ''S''
                      AND SUBSTRING(std.mod_sub_id FROM ''(.+)_'')::int = ' || sub_mod_refid || '
                    ORDER BY seqnum, sub_desc', ', '),
			   acc.seqnum as accseqnum,
			   cat.seqnum as catseqnum
          FROM webset_tx.def_pi_modifications_dtl acc
               INNER JOIN webset_tx.def_pi_modifications_mst cat ON cat.mod_refid = acc.mod_refid
			   LEFT OUTER JOIN webset_tx.std_pi_own ON state_accomodation_id = sub_mod_refid AND stdrefid = " . $tsRefID . " AND iepyear = " . $RefID . "
         WHERE EXISTS (SELECT 1
                         FROM webset_tx.std_pi std
                        WHERE std_refid = " . $tsRefID . "
						  AND iep_year = " . $RefID . "
						  AND accomod_mode = 'S'
                          AND SUBSTRING(std.mod_sub_id FROM '(.+)_')::int = acc.sub_mod_refid)
		   AND area_id = " . $area_id . "

         UNION

		 SELECT 'O_' || refid::varchar || '_' || $RefID,
               accommodation,

               plpgsql_recs_to_str('
                   SELECT sub_desc AS column
                     FROM webset_tx.std_pi std
                          INNER JOIN webset_tx.def_pi_subjects ON SUBSTRING(std.mod_sub_id FROM ''_(.+)'')::int = sub_refid
                    WHERE std_refid = " . $tsRefID . "
					  AND iep_year = " . $RefID . "
                      AND accomod_mode = ''O''
                      AND SUBSTRING(std.mod_sub_id FROM ''(.+)_'')::int = ' || acc.refid || '
                    ORDER BY seqnum, sub_desc', ', '),
			   acc.seqnum as accseqnum,
			   cat.seqnum as catseqnum

          FROM webset_tx.std_pi_own acc
               INNER JOIN webset_tx.def_pi_modifications_mst cat ON cat.mod_refid = acc.category_id
         WHERE stdrefid = " . $tsRefID . "
		   AND iepyear = " . $RefID . "
		   AND area_id = " . $area_id . "
		   AND state_accomodation_id IS NULL
         ORDER BY catseqnum, accseqnum, 3
    ";

	$list->addColumn('Interventions/Accommodations');
	$list->addColumn('Subjects');
	$list->addColumn('Order #');

	$list->hideCheckBoxes = false;

	$list->addButton('Copy', "copyEntries('$dskey', '$path', '$area_id')")->width('80px');

	$list->printList();

?>

<script type="text/javascript">

	function copyEntries(dskey, path, area) {
		var refid = ListClass.get().getSelectedValues().values;
		if (refid.length == 0) {
			api.alert('Please select at least one record.');
			return;
		}
		api.ajax.process(
			UIProcessBoxType.DATA_UPDATE,
			api.url('./progmod_copy_proc.php', {dskey: dskey}),
			{RefID: refid.join(','), area: area},
			true
		).addEventListener(
			ObjectEvent.COMPLETE,
			function (e) {
				api.window.dispatchEvent(ObjectEvent.COMPLETE);
				api.window.destroy();
			}
		)
	}

</script>
