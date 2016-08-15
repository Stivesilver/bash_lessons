<?php
	Security::init();
	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$RefID = io::geti('RefID');
	$set_ini = IDEAFormat::getIniOptions();
	io::jsVar('dskey', $dskey);

	$edit = new EditClass("edit1", $RefID);

	$edit->setSourceTable('webset.std_spconsid', 'sscmrefid');

	$edit->title = 'Add/Edit ' . $set_ini['sp_consid_title'];

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
		SELECT scmrefid, replace(replace(scmsdesc, '<i>', ''), '</i>', '')
		  FROM webset.statedef_spconsid_quest
		 WHERE scmrefid in (" . (count($linkedQuestions) > 0 ? implode(',', $linkedQuestions) : '0') . ")
	";

	$notLinkedSQL = "
		SELECT scmrefid, replace(replace(scmsdesc, '<i>', ''), '</i>', '')
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

	$SQL = $RefID > 0 ? "
                        SELECT scmrefid,
                               replace(replace(scmsdesc, '<i>', ''), '</i>', '')
                          FROM webset.statedef_spconsid_quest
                         WHERE scmrefid IN (SELECT scqrefid
                                              FROM webset.std_spconsid
                                             WHERE sscmrefid = " . $RefID . "
                                               AND syrefid = " . $stdIEPYear . ")
                         ORDER BY seqnum, scmsdesc
                     " : (count($linkedQuestions) > 0 ? $linkedSQL : $notLinkedSQL);

	$edit->addControl('Not Completed ' . $set_ini['sp_consid_title'], 'select')
		->sqlField('scqrefid')
		->name('scqrefid')
		->sql($SQL)
		->req();

	$edit->addControl('Question', 'textarea')
		->sql("
            SELECT replace(replace(scmquestion, '<i>', '('), '</i>', ')')
              FROM webset.statedef_spconsid_quest
             WHERE scmrefid = VALUE_01
        ")
		->disabled(true)
		->css("width", "100%")
		->tie('scqrefid');

	$edit->addControl('Answer', 'select')
		->sqlField('scarefid')
		->name('scarefid')
		->sql("
            SELECT scarefid,
                   regexp_replace(scanswer, '<[^>]*>', '', 'g') as scanswer
              FROM webset.statedef_spconsid_answ
             WHERE scmrefid = VALUE_01
			   AND (recdeactivationdt IS NULL OR now()< recdeactivationdt)
             ORDER BY order_num, CASE UPPER(SUBSTR(scanswer, 1, 2)) WHEN 'NO' THEN 1 WHEN 'YE' THEN 2 ELSE 3 END, scanswer
        ")
		->tie('scqrefid')
		->req();

	$narrative = $edit->addControl('Narrative', 'textarea')
		->sqlField('sscmnarrative')
		->css("width", "100%");

	if ($RefID == 0) {
		$narrative->SQL("
            SELECT scnarrativedefault
              FROM webset.statedef_spconsid_answ
             WHERE scarefid = VALUE_01
		")->tie('scarefid');
	}

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl("IEP Year", "hidden")->value($stdIEPYear)->sqlField('syrefid');

	$edit->addControl(FFInput::factory('fdfd'))
		->caption('Scan Link')
		->hide(true)
		->name('scalink')
		->sql("
            SELECT scalinkrefid
              FROM webset.statedef_spconsid_answ
             WHERE scarefid = VALUE_01
        ")
		->tie('scarefid');

	$edit->addControl(FFInput::factory())
		->caption('Form Caption')
		->hide(true)
		->name('formcaption')
		->value($ds->safeGet('stdname') . ' - Special Considerations Form')
		->toHTML();

	$edit->addControl(FFInput::factory())
		->name('form_id')
		->hide(true)
		->caption('Form ID')
		->sql("
            SELECT formrefid
              FROM webset.statedef_spconsid_answ
             WHERE scarefid::VARCHAR = NULLIF('VALUE_01','')
        ")
		->tie('scarefid');

	$edit->addControl('Form Name')
		->hide()
		->name('formname')
		->sql("
            SELECT mfcdoctitle
              FROM webset.statedef_spconsid_answ
                   INNER JOIN webset.statedef_forms ON formrefid = mfcrefid
             WHERE scarefid::VARCHAR = NULLIF('VALUE_01','')
        ")
		->tie('scarefid');

	$edit->addControl('Saved Answer ID', 'hidden')->name('sscmrefid');
	$edit->addControl('New Record Flag', 'hidden')->name('newrecord')->value($RefID == 0);

	$edit->finishURL = CoreUtils::getURL('srv_spconsid.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('srv_spconsid.php', array('dskey' => $dskey));

	$edit->firstCellWidth = '30%';
	$notAnswered = db::execSQL($linkedSQL)->recordCount() + db::execSQL($notLinkedSQL)->recordCount() - 1;

	$edit->saveAndAdd = $notAnswered > 0;

	$edit->printEdit();

?>
<script type="text/javascript">
	var edit1 = EditClass.get();
	edit1.onSaveDoneFunc(
		function (refid) {
			$("#sscmrefid").val(refid);
			if ($("#newrecord").val()) {
				if ($("#scalink").val() > 0) {
					api.goto(api.url('./srv_spconsid_add.php', {'dskey': dskey}));
					return false;
				}
				if ($("#form_id").val() > 0) {
					api.confirm('Would you like to complete ' + $("#formname").val() + '?', onOk, onNo);
					return false;
				} else {
					api.reload();
				}
			}
		}
	)

	function onOk() {
		var win = api.window.open(
			$("#formcaption").val(),
			api.url('srv_spconsid_completer.php',
				{
					'RefID': $("#sscmrefid").val(),
					'dskey': dskey
				}
			)
		);
		win.maximize();
		win.addEventListener(WindowEvent.CLOSE, onNo);
		win.show();
	}

	function onNo() {
		var edit1 = EditClass.get();
		if (edit1.getLastSaveMode() == 'add') {
			api.goto(api.url('./srv_spconsid_add.php', {'dskey': dskey}));
		} else {
			api.goto(api.url('./srv_spconsid.php', {'dskey': dskey}));
		}
	}
</script>
