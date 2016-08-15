<?php

	Security::init();

	$RefID = io::get('RefID', true);

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$area_id = IDEAAppArea::ID_EC_MAIN;
	$path = io::get('path', true);
	$set_ini = IDEAFormat::getIniOptions();

	$list = new listClass();

	$list->title = $set_ini['sp_consid_title'];

    $list->SQL = "
        SELECT sscmrefid,
               scmsdesc,
               scmquestion,
               scanswer,
               sscmnarrative,
               mfcdoctitle,
               std.pdf_refid,
               std.formrefid,
               saveapp,
               mfcrefid
          FROM webset.std_spconsid std
               INNER JOIN webset.statedef_spconsid_quest quest ON std.scqrefid = quest.scmrefid
               INNER JOIN webset.statedef_spconsid_answ ans ON ans.scarefid = std.scarefid
               LEFT OUTER JOIN webset.statedef_forms form ON form.mfcrefid = ans.formrefid
         WHERE std.stdrefid = " . $tsRefID . "
           AND std.syrefid = " . $RefID . "
         ORDER BY seqnum, scmsdesc
    ";

    $list->addColumn($set_ini['sp_consid_title'] ,' to Update');
    $list->addColumn('Question')->dataCallback('makeShorter');
    $list->addColumn('Answer')->dataCallback('makeShorter');
    $list->addColumn('Narrative');
    $list->addColumn('Form')
        ->type('link')
        ->align('center')
        ->param('javascript:completeForm(AF_REFID, "' . $dskey . '")')
        ->dataCallback('markCompletedForm');

	$list->hideCheckBoxes = false;

	$list->addButton('Copy', "copyEntries('$dskey', '$path')");

	$list->printList();

    function markCompletedForm($data, $col) {
        if ($data['saveapp'] == 'Y' || $data['pdf_refid'] > 0 || $data['formrefid'] > 0) {
            if ($data[$col] != '') return UILayout::factory()
                        ->addHTML($data[$col] . ' completed', '[font-weight: bold;]')
                        ->toHTML();
        } else {
            if ($data['mfcrefid'] > 0) {
                return $data[$col] . ' not completed';
            } else {
                return $data[$col];
            }
        }
    }

    function makeShorter($data, $col) {
        if (strlen($data[$col]) > 100) {
            return substr($data[$col], 0, 100) . '...';
        } else {
            return $data[$col];
        }
    }
?>

<script type="text/javascript">

	function copyEntries(dskey) {
		var refid = ListClass.get().getSelectedValues().values;
		if (refid.length == 0) {
			api.alert('Please select at least one record.');
			return;
		}
		api.ajax.process(
			UIProcessBoxType.DATA_UPDATE,
			api.url('srv_spconsid_copy_proc.php', {dskey: dskey}),
			{RefID: refid.join(',')},
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
