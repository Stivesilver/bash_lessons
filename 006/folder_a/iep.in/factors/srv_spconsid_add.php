<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$RefID = io::geti('RefID');	

	$edit = new EditClass("edit1", $RefID);

	$edit->setSourceTable('webset.std_spconsid', 'sscmrefid');

	$edit->title = 'Add/Edit Special Factors';

	$linkedQuestions = db::execSQL("
		SELECT scalinkrefid
		  FROM webset.std_spconsid std
		  	   INNER JOIN webset.statedef_spconsid_answ ans ON ans.scarefid = std.scarefid
	     WHERE stdrefid = " . $tsRefID . "
		   AND std.scarefid IS NOT NULL
		   AND scalinkrefid > 0 
		   AND scalinkrefid NOT IN (SELECT scqrefid
									  FROM webset.std_spconsid
								     WHERE stdrefid = " . $tsRefID . "
									   AND scarefid IS NOT NULL)
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
								   AND scarefid IS NOT NULL)
		   AND (recdeactivationdt IS NULL OR now()< recdeactivationdt)
		   AND (set_id IS NULL OR set_id = '"  . IDEAFormat::get('id'). "')
		 ORDER BY seqnum, scmsdesc
	";

	$SQL = $RefID > 0 ? "
                        SELECT scmrefid,
                               scmsdesc
                          FROM webset.statedef_spconsid_quest
                         WHERE scmrefid IN (SELECT scqrefid
                                              FROM webset.std_spconsid
                                             WHERE sscmrefid = " . $RefID . ")
                         ORDER BY seqnum, scmsdesc
                     " : (count($linkedQuestions) > 0 ? $linkedSQL : $notLinkedSQL);

	$edit->addControl('Not Completed Special Factors', 'select')
		->sqlField('scqrefid')
		->name('scqrefid')
		->sql($SQL)
		->req();

	$edit->addControl('Question', 'protected')
		->sql("
            SELECT replace(replace(scmquestion, '<i>', '('), '</i>', ')')
              FROM webset.statedef_spconsid_quest
             WHERE scmrefid = VALUE_01
        ")
		->tie('scqrefid');

	$edit->addControl('Answer', 'select')
		->sqlField('scarefid')
		->name('scarefid')
		->sql("
            SELECT scarefid,
                   scanswer
              FROM webset.statedef_spconsid_answ
             WHERE scmrefid = VALUE_01
             ORDER BY CASE UPPER(SUBSTR(scanswer, 1, 2)) WHEN 'NO' THEN 1 WHEN 'YE' THEN 2 ELSE 3 END, scanswer
        ")
		->tie('scqrefid')
		->req();

	$edit->addControl('Narrative', 'textarea')
		->sqlField('sscmnarrative')
		->css("width", "100%");

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->addControl('Form Caption', 'hidden')
		->name('formcaption')
		->value($ds->safeGet('stdname') . ' - Special Considerations Form')
		->toHTML();

	$edit->addControl('Form ID', 'hidden')
		->name('form_id')
		->sql("
            SELECT xmlform_id
              FROM webset.statedef_spconsid_answ
             WHERE scarefid::varchar = NULLIF('VALUE_01','')
        ")
		->tie('scarefid');

	$edit->addControl('Form Name', 'hidden')
		->name('formname')
		->sql("
            SELECT form_name
              FROM webset.statedef_spconsid_answ
                   INNER JOIN webset.statedef_forms_xml ON xmlform_id = frefid
             WHERE scarefid::varchar = NULLIF('VALUE_01','')
        ")
		->tie('scarefid');

	$edit->addControl('Saved Answer ID', 'hidden')->name('sscmrefid');
	$edit->addControl('dskey', 'hidden')->name('dskey')->value($dskey);
	$edit->addControl('New Record Flag', 'hidden')->name('newrecord')->value($RefID == 0);
	
	$edit->finishURL = CoreUtils::getURL('srv_spconsid.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('srv_spconsid.php', array('dskey' => $dskey));

	$edit->firstCellWidth = '30%';
	$notAnswered = db::execSQL($linkedSQL)->recordCount() + db::execSQL($notLinkedSQL)->recordCount() - 1;
	$edit->addControl('Not Answered', 'hidden')->name('notAnswered')->value($notAnswered);
	
	$edit->saveAndAdd = $notAnswered > 0;

	$edit->printEdit();
?>
<script type="text/javascript">
		var edit1 = EditClass.get();
		edit1.onSaveDoneFunc(
			function(refid) {
				$("#sscmrefid").val(refid);
				if ($("#newrecord").val()) {
					if ($("#form_id").val() > 0) {
						api.confirm('Would you like to complete ' + $("#formname").val() + '?', editForm, onNo);
						return false;
					} else {
						api.reload();
					}
				}
			}
		)

		function editForm() {		
			url = api.url('srv_spconsid_form_edit.ajax.php');
			api.ajax.post(
				url,
				{
					'sp_id': $("#sscmrefid").val(),
					'state_id': $("#form_id").val(),
					'std_id': '0',
					'dskey': $('#dskey').val()
				},
				function(answer) {
					win = api.window.open(answer.caption, answer.url);
					win.maximize();
					win.addEventListener(WindowEvent.CLOSE, onNo);
					win.show();
				}
			);
		}		

		function onNo() {
			if ($("#notAnswered").val() > 1) {    
				api.reload();
			} else {	
		        edit1.cancelEdit();
			}
		}
</script>