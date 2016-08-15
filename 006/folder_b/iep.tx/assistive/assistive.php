<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$screenURL = $ds->safeGet('screenURL');

	$form_state_id = 912;
	$form_std_id = (int) IDEAStudentRegistry::readStdKey($tsRefID, 'tx_iep', 'assistive_tech_form', $stdIEPYear);
	$form_std_id = (int) db::execSQL("
		SELECT smfcrefid
		  FROM webset.std_forms
		 WHERE smfcrefid = " . $form_std_id . "
		   AND stdrefid = " . $tsRefID . "
	")->getOne();

	$edit = new EditClass("edit1", $stdIEPYear);

	$edit->title = 'Assistive Technology (AT)';
	$edit->saveAndEdit = TRUE;
	$edit->saveAndAdd = FALSE;
	$edit->firstCellWidth = '35%';

	$edit->addGroup('General Information');

	$edit->addControl('Assistive Technology', 'select_radio')
		->name('assistive')
		->data(
			array(
				'Y' => 'Assistive technology is required in order for the student to access the curriculum and make satisfactory  academic progress.',
				'N' => 'The student does not need assistive technology devices in order to access the curriculum and make satisfactory progress.'
			)
		)
		->value(IDEAStudentRegistry::readStdKey($tsRefID, 'tx_iep', 'assistive_technology', $stdIEPYear))
		->breakRow();

	$edit->addControl('Assistive Technology Modifications Form', 'protected')
		->showIf('assistive', 'Y')
		->name('form_bip')
		->append(
			FFButton::factory('Edit Form')
			->onClick('completeForm(' . json_encode(formUrlPrepare($form_state_id, $form_std_id, $dskey, $tsRefID)) . ')')
			->toHTML()
		)
		->append(
			UILayout::factory()->addHTML('', '10px')->addHTML($form_std_id > 0 ? 'Form Completed' : 'Form not yet completed')->toHTML()
		)
	;

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->finishURL = CoreUtils::getURL('assistive_save.php', array('dskey' => $dskey));
	$edit->saveURL = CoreUtils::getURL('assistive_save.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->printEdit();

	function formUrlPrepare($state_id, $std_id, $dskey, $tsRefID) {
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
				->setUrlSave(CoreUtils::getPhysicalPath('assistive_form_save.php'))
				->setUrlFinish(CoreUtils::getURL('assistive_form_save.php'))
				->setParameter('dskey', $dskey)
				->setParameter('smfcrefid', (int) $form['smfcrefid'])
				->setParameter('mfcrefid', $state_id)
				->setParameter('smfcfilename', $smfcfilename)
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
			var edit1 = EditClass.get();
            edit1.saveAndEdit();
		}
</script>
