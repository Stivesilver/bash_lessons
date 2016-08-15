<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$screenURL = $ds->safeGet('screenURL');

	$bip_state_id = 881;
	$bip_std_id = (int)IDEAStudentRegistry::readStdKey($tsRefID, 'tx_iep', 'supplement_form_bip', $stdIEPYear);
	$bip_std_id = (int)db::execSQL("
		SELECT smfcrefid
		  FROM webset.std_forms
		 WHERE smfcrefid = " . $bip_std_id . "
		   AND stdrefid = " . $tsRefID . "
	")->getOne();

	$fba_state_id = 879;
	$fba_std_id = (int)IDEAStudentRegistry::readStdKey($tsRefID, 'tx_iep', 'supplement_form_fba', $stdIEPYear);
	$fba_std_id = (int)db::execSQL("
		SELECT smfcrefid
		  FROM webset.std_forms
		 WHERE smfcrefid = " . $fba_std_id . "
		   AND stdrefid = " . $tsRefID . "
	")->getOne();

	$edit = new EditClass("edit1", $stdIEPYear);

	$edit->title = 'Behavior Intervention Plan';
	$edit->saveAndEdit = true;
	$edit->saveAndAdd = false;
	$edit->firstCellWidth = '35%';

	$edit->setSourceTable('webset_tx.std_sam_other', 'iepyear');

	$edit->addGroup('General Information');

	$edit->addControl(
		FFSwitchYN::factory('The student has a BIP')
			->value(IDEAStudentRegistry::readStdKey($tsRefID, 'tx_iep', 'Program Interventions', $stdIEPYear))
			->name('bip')
	);

	$edit->addControl('Behavior Intervention Plan Form', 'protected')
		->name('form_bip')
		->append(
			FFButton::factory('Edit Form')
				->onClick('completeForm(' . json_encode(formUrlPrepare($bip_state_id, $bip_std_id, $dskey, $tsRefID, 'supplement_form_bip')) . ')')
				->enabledIf('bip', 'Y')
				->toHTML()
		)
		->append(
			UILayout::factory()->addHTML('', '10px')->addHTML($bip_std_id > 0 ? 'Form Completed' : '')->toHTML()
		);

	$edit->addControl('Functional Behavioral Assessment Form', 'protected')
		->name('form_fba')
		->append(
			FFButton::factory('Edit Form')
				->onClick('completeForm(' . json_encode(formUrlPrepare($fba_state_id, $fba_std_id, $dskey, $tsRefID, 'supplement_form_fba')) . ')')
				->enabledIf('bip', 'Y')
				->toHTML()
		)
		->append(
			UILayout::factory()->addHTML('', '10px')->addHTML($fba_std_id > 0 ? 'Form Completed' : '')->toHTML()
		);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->finishURL = CoreUtils::getURL('bip_save.php', array('dskey' => $dskey));
	$edit->saveURL = CoreUtils::getURL('bip_save.php', array('dskey' => $dskey));
	$edit->cancelURL = 'javascript:parent.switchTab();';


	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset_tx.std_sam_other')
			->setKeyField('iepyear')
			->applyEditClassMode()
	);

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();

	function formUrlPrepare($state_id, $std_id, $dskey, $tsRefID, $area) {
		$form = db::execSQL("
			SELECT mfcdoctitle,
				   mfcfilename,
				   xml_field_links,
				   form_xml,
				   fdf_content,
				   smfcfilename,
				   smfcrefid
			  FROM webset.statedef_forms stf
				   INNER JOIN webset.statedef_forms_xml stx ON stf.xmlform_id = stx.frefid
				   LEFT OUTER JOIN webset.std_forms std ON smfcrefid = " . $std_id . " AND stdrefid = " . $tsRefID . "
			 WHERE stf.mfcrefid = " . $state_id . "
		")->assoc();

		$template = IDEAFormPDF::replace_id(base64_decode($form['form_xml']), base64_decode($form['xml_field_links']));
		if ($form['fdf_content'] != '') {
			$smfcfilename = $form['smfcfilename'];
			$values = IDEAFormPDF::fdf2xml(base64_decode($form['fdf_content']), $template);
		} else {
			$smfcfilename = 'Form_' . $tsRefID . '_' . date('mdhis') . '.fdf';
			$values = IDEAFormDefaults::factory($tsRefID)->getXML();
		}

		return IDEAForm::factory()
			->setTitle($form['mfcdoctitle'])
			->setTemplate($template)
			->setValues($values)
			->setUrlCancel('javascript:api.window.destroy();')
			->setUrlSave(CoreUtils::getPhysicalPath('bip_form_save.php'))
			->setUrlFinish(CoreUtils::getURL('bip_form_save.php'))
			->setParameter('dskey', $dskey)
			->setParameter('smfcrefid', (int)$form['smfcrefid'])
			->setParameter('mfcrefid', $state_id)
			->setParameter('smfcfilename', $smfcfilename)
			->setParameter('save_area', $area)
			->getUrlPanel();
	}

?>
<script type="text/javascript">
	function completeForm($url) {
		win = api.window.open('Complete Form', $url)
		win.maximize();
		win.addEventListener(WindowEvent.CLOSE, formCompleted);
		win.show();
	}

	function formCompleted() {
		api.reload();
	}
</script>
