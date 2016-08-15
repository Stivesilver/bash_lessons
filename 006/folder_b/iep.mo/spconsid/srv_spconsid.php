<?php
    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');
	$set_ini = IDEAFormat::getIniOptions();

    $list = new ListClass();

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
           AND std.syrefid = " . $stdIEPYear . "
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

    $list->addURL = CoreUtils::getURL('srv_spconsid_add.php', array('dskey' => $dskey));
    $list->editURL = CoreUtils::getURL('srv_spconsid_add.php', array('dskey' => $dskey));

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.std_spconsid')
            ->setKeyField('sscmrefid')
            ->applyListClassMode()
    );

	$button = new IDEAPopulateIEPYear($dskey, IDEAAppArea::SPECIAL_CONS, '/apps/idea/iep.mo/spconsid/srv_spconsid_copy_list.php');
	$listButton = $button->getPopulateButton();
	$list->addButton($listButton);

    $list->addButton(
        IDEAFormat::getPrintButton(array('dskey' => $dskey))
    );

	$list->deleteKeyField = 'sscmrefid';
	$list->deleteTableName = 'webset.std_spconsid';
    /*$list->addRecordsProcess('Delete')
        ->message('Do you really want to delete selected questions?')
        ->url(CoreUtils::getURL('srv_spconsid_delete.ajax.php', array('dskey' => $dskey)))
        ->type(ListClassProcess::DATA_UPDATE)
        ->progressBar(false);*/

	$linkedQuestions = db::execSQL("
		SELECT scalinkrefid
		  FROM webset.std_spconsid std
		  	   INNER JOIN webset.statedef_spconsid_answ ans ON ans.scarefid = std.scarefid
	     WHERE stdrefid = " . $tsRefID . "
		   AND std.scarefid IS NOT NULL
		   AND syrefid = " . $stdIEPYear . "
		   AND scalinkrefid > 0 
		   AND scalinkrefid NOT IN (SELECT scqrefid
									  FROM webset.std_spconsid
								     WHERE stdrefid = " . $tsRefID . "
									   AND scarefid IS NOT NULL
									   AND syrefid = " . $stdIEPYear . ")
	")->indexCol(0);

	$linkedSQL = "
		SELECT scmrefid, scmsdesc
		  FROM webset.statedef_spconsid_quest
		 WHERE scmrefid in (" . (count($linkedQuestions)>0 ? implode(',', $linkedQuestions) : '0') . ")
	";

	$notLinkedSQL = "
		SELECT scmrefid, scmsdesc
		  FROM webset.statedef_spconsid_quest
		 WHERE screfid = " . VNDState::factory()->id . "
		   AND scmlinksw = 'N'
		   AND scmrefid NOT IN (SELECT scqrefid
								  FROM webset.std_spconsid
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
            ->value($ds->safeGet('stdname') . ' - ' . $set_ini['sp_consid_title'] . ' Form')
            ->toHTML();

    function markCompletedForm($data, $col) {
        if ($data['saveapp'] == 'Y' || $data['pdf_refid'] > 0 || $data['formrefid'] > 0) {
            if ($data[$col] != '') {
	            return UILayout::factory()
                    ->addHTML($data[$col] . ' completed', '[font-weight: bold;]')
                    ->toHTML();
            }
	        return null;

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
    function completeForm(RefID, dskey) {
        var win = api.window.open(
	        $("#formcaption").val(),
	        api.url(
		        'srv_spconsid_completer.php',
		        {'RefID': RefID, 'dskey': dskey}
	        )
        );
        win.maximize();
        win.addEventListener(WindowEvent.CLOSE, formCompleted);
        win.show();
    }

    function formCompleted() {
        api.reload();
    }
</script>