<?php

	Security::init();

	$scrrefid = io::get('scrrefid');
	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$evalproc_id = $ds->safeGet('evalproc_id');


	$edit = new EditClass("edit1", $RefID);

	$edit->title = ($RefID == 0 ? 'Add' : 'Edit') . ' Evaluation Procedures';

	$edit->setSourceTable('webset.es_std_scr', 'shsdrefid');

	$edit->addGroup('General Information');

	$edit->addControl(FFIDEAEvalScreenType::factory())
		->name('screenid')
		->sqlField('screenid')
		->value($scrrefid)
		->disabled(true)
		->req();

	$edit->addControl('Name of Assessment', 'select')
		->sqlField('hsprefid')
		->name('hsprefid')
		->sql("
			SELECT hsprefid,
				   hspdesc
			  FROM webset.es_scr_disdef_proc
			 WHERE vndrefid=" . SystemCore::$VndRefID . "
			   AND (recdeactivationdt IS NULL OR now()< recdeactivationdt)
			   AND xml_test IS NOT NULL
			   AND screenid = VALUE_01
			 ORDER BY CASE WHEN hspdesc ILIKE 'Other%' THEN 2 ELSE 1 END, hspdesc
		")
		->tie('screenid')
		->req();

	$edit->addControl('Other')
		->sqlField('test_name')
		->showIf('hsprefid', db::execSQL("
                                  SELECT hsprefid
                                    FROM webset.es_scr_disdef_proc
								     WHERE substring(lower(hspdesc), 1, 5) = 'other'
                                 ")->indexAll())
		->size(50);

	$edit->addControl("Date of Assessment", "date")
		->sqlField('shsddate');

	$edit->addControl(FFInputDropList::factory('Person Conducting Assessment')
		->sqlField('screener')
		->dropListSQL("
            SELECT umrefid, umfirstname || ' ' || umlastname || COALESCE(' / ' || umtitle, '')
              FROM public.sys_usermst
             WHERE vndrefid = VNDREFID
               AND um_internal
             ORDER BY UPPER(umlastname), UPPER(umfirstname)
        "))
		->highlightField(false)
		->width('400px');

	$edit->addControl('Location of Assessment')
		->sqlField('Location')
		->width('400px');

	$edit->addControl("Order #", "hidden")
		->sqlField('order_num')
		->value(
			(int)db::execSQL("
			SELECT max(order_num)
			  FROM webset.es_std_scr
			 WHERE stdrefid = " . $tsRefID . "
			   AND eprefid = " . $evalproc_id . "
			")->getOne() + 1
		);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl("tsRefID", "hidden")->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl("evalproc_id", "hidden")->value($evalproc_id)->sqlField('eprefid');
	$edit->addControl('dskey', 'hidden')->name('dskey')->value($dskey);
	$edit->addControl('Saved Answer ID', 'hidden')->name('shsdrefid');
	$edit->addControl('New Record Flag', 'hidden')->name('newrecord')->value($RefID == 0);

//	$edit->finishURL = CoreUtils::getURL('./eval_procedures_list.php', array('dskey' => $dskey, 'scrrefid' => $scrrefid));
//	$edit->cancelURL = CoreUtils::getURL('./eval_procedures_list.php', array('dskey' => $dskey, 'scrrefid' => $scrrefid));

	$edit->printEdit();

	print FormField::factory('hidden')
		->name('formcaption')
		->value($ds->safeGet('stdname') . ' - Assessment Form')
		->toHTML();
?>
<script type="text/javascript">
	var edit1 = EditClass.get();
	edit1.onSaveDoneFunc(
		function(refid) {
			$("#shsdrefid").val(refid);
			if ($("#newrecord").val()) {
				if ($("#hsprefid").val() > 0) {
					api.confirm('Would you like to complete ' + $("#hsprefid option:selected").text() + '?', editForm, onNo);
					return false;
				} else {
					api.reload();
				}
			}
		}
	)

	function editForm() {
		var win = api.window.open(
			$("#formcaption").val(),
			api.url(
				'./eval_procedures_completer.php',
				{'RefID': $("#shsdrefid").val(), 'dskey': $('#dskey').val()}
			)
		);
		win.maximize();
		win.addEventListener(WindowEvent.CLOSE, onNo);
		win.show();
	}

	function onNo() {
		if (edit1.getLastSaveMode() == 'add') {
			api.reload();
		} else {
			edit1.cancelEdit();
		}
	}
</script>
