<?php
    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');

    $list = new ListClass();

    $list->title = 'ARD/IEP Supplement';

    $list->SQL = "
        SELECT sscmrefid,
               scmsdesc,
               scmquestion,
               scanswer,
               mfcdoctitle,
               std.formrefid,
               saveapp,
               mfcrefid
          FROM webset_tx.std_ard std
               INNER JOIN webset_tx.def_spconsid_quest quest ON std.scqrefid = quest.scmrefid
               INNER JOIN webset_tx.def_spconsid_answ ans ON ans.scarefid = std.scarefid
               LEFT OUTER JOIN webset.statedef_forms form ON form.mfcrefid = ans.formrefid
         WHERE std.stdrefid = " . $tsRefID . "
           AND std.syrefid = " . $stdIEPYear . "
         ORDER BY seqnum, scmsdesc
    ";

    $list->addColumn('ARD/IEP Supplement to Update' ,' to Update');
    $list->addColumn('Question')->dataCallback('makeShorter');
    $list->addColumn('Answer')->dataCallback('makeShorter');
    $list->addColumn('Form')
        ->type('link')
        ->align('center')
        ->param('javascript:editForm(AF_REFID, "AF_COL7", "AF_COL5")')
        ->dataCallback('markCompletedForm');

    $list->addURL = CoreUtils::getURL('srv_spconsid_add.php', array('dskey' => $dskey));
    $list->editURL = CoreUtils::getURL('srv_spconsid_add.php', array('dskey' => $dskey));

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset_tx.std_ard')
            ->setKeyField('sscmrefid')
            ->applyListClassMode()
    );

    $list->addButton(
        IDEAFormat::getPrintButton(array('dskey' => $dskey))
    );

    $list->addRecordsProcess('Delete')
        ->message('Do you really want to delete selected questions?')
        ->url(CoreUtils::getURL('srv_spconsid_delete.ajax.php', array('dskey' => $dskey)))
        ->type(ListClassProcess::DATA_UPDATE)
        ->progressBar(false);

	$linkedQuestions = db::execSQL("
		SELECT scalinkrefid
		  FROM webset_tx.std_ard std
		  	   INNER JOIN webset_tx.def_spconsid_answ ans ON ans.scarefid = std.scarefid
	     WHERE stdrefid = " . $tsRefID . "
		   AND std.scarefid IS NOT NULL
		   AND syrefid = " . $stdIEPYear . "
		   AND scalinkrefid > 0 
		   AND scalinkrefid NOT IN (SELECT scqrefid
									  FROM webset_tx.std_ard
								     WHERE stdrefid = " . $tsRefID . "
									   AND scarefid IS NOT NULL
									   AND syrefid = " . $stdIEPYear . ")
	")->indexCol(0);

	$linkedSQL = "
		SELECT scmrefid, scmsdesc
		  FROM webset_tx.def_spconsid_quest
		 WHERE scmrefid in (" . (count($linkedQuestions)>0 ? implode(',', $linkedQuestions) : '0') . ")
	";

	$notLinkedSQL = "
		SELECT scmrefid, scmsdesc
		  FROM webset_tx.def_spconsid_quest
		 WHERE screfid = " . VNDState::factory()->id . "
		   AND scmlinksw = 'N'
		   AND scmrefid NOT IN (SELECT scqrefid
								  FROM webset_tx.std_ard
								 WHERE stdrefid = " . $tsRefID . "
								   AND scarefid IS NOT NULL
								   AND syrefid = " . $stdIEPYear . ")
		   AND (recdeactivationdt IS NULL OR now()< recdeactivationdt)
		 ORDER BY seqnum, scmsdesc
	";
	$notAnswered = db::execSQL($linkedSQL)->recordCount() + db::execSQL($notLinkedSQL)->recordCount();
	
    $list->getButton(ListClassButton::ADD_NEW)
        ->disabled($notAnswered == 0);

    $list->multipleEdit = false;

    $list->printList();

    print FormField::factory('hidden')
            ->name('formcaption')
            ->value($ds->safeGet('stdname') . ' - ARD/IEP Supplement Form')
            ->toHTML();
	
    print FormField::factory('hidden')
            ->name('dskey')
            ->value($dskey)
            ->toHTML();

    function markCompletedForm($data, $col) {
        if ($data['saveapp'] == 'Y' || $data['formrefid'] > 0) {
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
	
	function editForm(ard_id, state_id, std_id) {		
        url = api.url('srv_spconsid_form_edit.ajax.php');
        api.ajax.post(
            url,
            {
				'ard_id': ard_id,
				'state_id': state_id,
				'std_id': std_id,
                'dskey': $('#dskey').val()
			},
			function(answer) {
				win = api.window.open(answer.caption, answer.url);
				win.maximize();
				win.addEventListener(WindowEvent.CLOSE, formCompleted);
				win.show();
			}
        );
    }
	
    function formCompleted() {
        api.reload();
    }
</script>
