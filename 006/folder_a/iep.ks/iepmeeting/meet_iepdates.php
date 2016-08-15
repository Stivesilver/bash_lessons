<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$screenURL = $ds->safeGet('screenURL');
	$state_form_id = IDEAFormat::getIniOptions('ks_amendment_form_id');
	$form_title = db::execSQL("
        SELECT mfcdoctitle
          FROM webset.statedef_forms form
         WHERE mfcrefid = " . $state_form_id . "
    ");
	$std_form_id = IDEAStudentRegistry::readStdKey($tsRefID, "ks_iep", "iep_meeting_form", $stdIEPYear);

	$edit = new EditClass('edit1', $tsRefID);

	$edit->title = 'IEP Dates';

	$data = db::execSQL("
        SELECT stdiepmeetingdt,
               stdcmpltdt,
               receiveddate,
               stdevaldt,
               stdtriennialdt,
               stddraftiepcopydt,
               stdiepcopydt,
               amendment,
               uni_field3,
               uni_field5,
               parentrightdt,
               addcomments,
               ks_cur_iep,
               ks_trs_iep,
               ks_cmp_iep,
               stdenrolldt,
               t0.lastuser,
               t0.lastupdate
          FROM webset.sys_teacherstudentassignment  t0
               LEFT OUTER JOIN webset.std_common t1 ON t0.tsrefid = t1.stdrefid
         WHERE tsRefID = " . $tsRefID . "
    ")->assoc();

	$edit->addGroup("General Information");

	$edit->addControl("IEP Meeting Date ", "date")
		->name('stdiepmeetingdt')
		->value($data['stdiepmeetingdt']);

	/** @var FFDateTime */
	$stdcmpltdt = $edit->addControl('IEP Projected Date of Annual Review', "date")
		->name('stdcmpltdt')
		->value($data['stdcmpltdt']);

	$edit->addControl('Evaluation Consent Obtained', 'date')
		->name('receiveddate')
		->value($data['receiveddate']);

	$edit->addControl('Current Evaluation Completed Date', 'date')
		->name('stdevaldt')
		->value($data['stdevaldt']);

	/** @var FFDateTime */
	$stdtriennialdt = $edit->addControl('Triennial Due Date: ', 'date')
		->name('stdtriennialdt')
		->value($data['stdtriennialdt']);

	$edit->addControl('Draft IEP copy provided on', 'date')
		->name('stddraftiepcopydt')
		->value($data['stddraftiepcopydt']);

	$edit->addControl('IEP copy provided on', 'date')
		->name('stdiepcopydt')
		->value($data['stdiepcopydt']);

	$edit->addControl('Amendment Date 1', 'date')
		->name('amendment')
		->value($data['amendment']);

	$edit->addControl('Amendment Date 2', 'date')
		->name('uni_field3')
		->value($data['uni_field3']);

	$edit->addControl('Amendment Date 3', 'date')
		->name('uni_field5')
		->value($data['uni_field5']);

	$edit->addControl('Parents received Parental Rights', 'date')
		->name('parentrightdt')
		->value($data['parentrightdt']);

	$edit->addControl('Additional Comments', 'textarea')
		->name('addcomments')
		->value($data['addcomments']);

	if (IDEACore::disParam(47) != "N") {
		$stdcmpltdt->sql("
            SELECT NULLIF('VALUE_01','')::DATE + INTERVAL '1 YEAR'
        ")->tie('stdiepmeetingdt');

		$stdtriennialdt->sql("
            SELECT NULLIF('VALUE_01','')::DATE + INTERVAL '3 YEARS'
        ")->tie('stdevaldt');
	}

	$edit->addGroup('Transfer Information - Complete this section only if the student transfers in from another school district');
	$edit->addControl(FFSwitchYN::factory('Does the student have a current IEP?'))
		->name('ks_cur_iep')
		->value($data['ks_cur_iep']);

	$edit->addControl(FFSwitchYN::factory('Does the special education team accept the <b>transfer</b> IEP?'))
		->name('ks_trs_iep')
		->value($data['ks_trs_iep']);

	$edit->addControl(FFSwitchYN::factory('Does the special education team accept the <b>complete</b> IEP'))
		->name('ks_cmp_iep')//->append(UIAnchor::factory('Form')->onClick('formComplete()'))
		->value($data['ks_cmp_iep']);

	$edit->addControl('IEP Inititation Date Within this District', 'date')
		->name('stdenrolldt')
		->value($data['stdenrolldt']);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->value($data['lastuser']);
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->value($data['lastupdate']);

	$edit->setPresaveCallback('saveData', 'meet_iepdates.inc.php');
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

	$edit->saveAndAdd = false;
	$edit->saveAndEdit = true;
	$edit->firstCellWidth = '40%';

	$edit->addControl('dskey', 'hidden')->name('dskey')->value($dskey);
	$edit->addControl('state_form_id', 'hidden')->name('state_form_id')->value($state_form_id);
	$edit->addControl('std_form_id', 'hidden')->name('std_form_id')->value($std_form_id);

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sys_teacherstudentassignment')
			->setKeyField('tsrefid')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();
?>
<script type="text/javascript">
	function formComplete() {
		var wnd = api.window.open('Complete Form', api.url('meet_form.php', {dskey: dskey, state_form_id: state_form_id, std_form_id: std_form_id}));
		wnd.resize(950, 600);
		wnd.center();
		wnd.addEventListener('form_completed', onEvent);
		wnd.show();
	}

	function onEvent(e) {
		var name = e.param.name;
		var title = e.param.title;
		$("#participantname").val(name);
		$("#participantrole").val(title);
	}

</script>
