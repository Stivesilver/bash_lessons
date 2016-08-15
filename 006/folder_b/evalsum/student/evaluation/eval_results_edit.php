<?php

	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$evalproc_id = $ds->safeGet('evalproc_id');
	$staterefid = VNDState::factory()->id;

	$edit = new EditClass("edit1", $RefID);

	$edit->title = 'Add/Edit Evaluation Results';
	$edit->saveAndEdit = true;

	$edit->setSourceTable('webset.es_std_join', 'esrefid');

	$edit->addTab('Assessment Area');
	$SQL = $RefID > 0 ? "
                        SELECT scrrefid,
                               scrdesc
                          FROM webset.es_statedef_screeningtype
                         WHERE scrrefid IN (
								SELECT screening_id
								  FROM webset.es_std_join
								 WHERE esrefid = " . $RefID . "
                               )
                        "
		:
		"
                        SELECT scrrefid,
                               scrdesc
                          FROM webset.es_statedef_screeningtype AS s
                         WHERE screfid = " . VNDState::factory()->id . "
                           AND (enddate IS NULL OR now()< enddate OR scrrefid IN ( " . (IDEACore::disParam(155) ? IDEACore::disParam(155) : 'NULL') . "))
                           AND NOT EXISTS (
								SELECT 1
								  FROM webset.es_std_join AS j
								 WHERE eprefid = " . $evalproc_id . "
								   AND j.screening_id = s.scrrefid
                               )
                         ORDER BY scrseq
                        ";

	if ($RefID > 0) {
		$edit->addControl('Area', 'select')
			->sqlField('screening_id')
			->name('screening_id')
			->sql($SQL)
			->onChange('
				api.ajax.get(
					api.url(
						"eval_results_edit.ajax.php",
						{screening_id: this.value, tsRefID: ' . $tsRefID . ', evalproc_id: ' . $evalproc_id . ' }
					),
					function (answer) {
						if (answer.red) {
							$("#red_summary").val(answer.red);
						} else {
							$("#red_summary").val("");
						}
					}
				)
			'
			)
			->req();
	} else {
		$edit->addControl('Area', 'select')
			->sqlField('screening_id')
			->name('screening_id')
			->sql($SQL)
			->onChange('
				api.ajax.get(
					api.url(
						"eval_results_edit.ajax.php",
						{screening_id: this.value, tsRefID: ' . $tsRefID . ', evalproc_id: ' . $evalproc_id . ' }
					),
					function (answer) {
						if (answer.red) {
							$("#red_summary").val(answer.red);
						} else {
							$("#red_summary").val("");
						}
						if (answer.checked) {
							$("#further_assess").val(answer.checked).change();
						} else {
							$("#further_assess").val("").change();
						}
					}
				)
			'
			)
			->req();
	}

	$statements = UIAnchor::factory('Add Form Statements')
		->onClick('addStatement("screen_summary")')
		->toHTML();

	$edit->addControl('Data Reviewed and Results' . '<br/>' . $statements, 'textarea')
		->sqlField('screen_summary')
		->name('screen_summary')
		->css('width', '100%')
		->css('height', '200px');

	$edit->addControl(FFIDEASwitchYN::factory('Further assessment needed?'))
		->name('further_assess')
		->sqlField('further_assess_needed_sw');

	$edit->addGroup('RED');
	$edit->addControl(FFIDEASwitchYN::factory('Include RED in ER Report?'))
		->sqlField('include_red_sw')
		->value('Y');

	$edit->addControl('RED Summary', 'textarea')
		->name('red_summary')
		->disabled(true)
		->css("width", "100%");

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl("evalproc_id", "hidden")->value($evalproc_id)->sqlField('eprefid');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	if (db::execSQL("
        SELECT 1
          FROM webset.es_statedef_screeningtype st
         WHERE st.screfid = " . $staterefid . "
           AND (st.enddate>now() OR st.enddate IS NULL OR scrrefid IN ( " . (IDEACore::disParam(155) ? IDEACore::disParam(155) : 'NULL') . "))
           AND st.scrrefid NOT IN (
				SELECT screening_id
				  FROM webset.es_std_join
				 WHERE stdrefid = " . $tsRefID . "
				   AND eprefid = " . $evalproc_id . "
               )
        OFFSET 1")->getOne() == ''
	) {
		$edit->saveAndAdd = false;
	}

	$scrinfo = db::execSQL($SQL)->assoc();
	$scrrefid = 0;
	if ($scrinfo['scrrefid'] && $RefID) {
		$scrrefid = (int)$scrinfo['scrrefid'];
	}
	$edit->addTab('Procedures');
	$edit->addIFrame(CoreUtils::getURL('eval_procedures_list.php', array('dskey' => $dskey, 'scrrefid' => $scrrefid)))->height('500');
	$edit->finishURL = CoreUtils::getURL('./eval_results_list.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('./eval_results_list.php', array('dskey' => $dskey));

	$edit->printEdit();

	if (!$RefID) {
		?>
		<script>
			$('#screen_summary').val('');
		</script>
		<?
	}

?>
<script type="text/javascript">
	$("#screening_id").change();
	function addStatement(field) {
		var wnd = api.window.open('', api.url('./statements.php', {
			'screening_id': $("#screening_id").val(),
			'area': $("#screening_id option:selected").text(),
			'field': field
		}));
		wnd.resize(950, 600);
		wnd.center();
		wnd.addEventListener('statetment_selected', onStatement);
		wnd.show();
	}

	function onStatement(e) {
		var statement = e.param.stm;
		var field = e.param.field;
		if ($("#" + field).val() != "")
			statement = "\r" + statement;
		$("#" + field).val($("#" + field).val() + statement);
	}

	function completeForm(RefID, dskey) {
		var win = api.window.open(
			$("#formcaption").val(),
			api.url(
				'./eval_procedures_completer.php',
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
