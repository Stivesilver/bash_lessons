<?php

	Security::init();

	$hisrefid = io::geti('hisrefid');
	$sfrefid = io::geti('sfrefid');
	$frefid = io::geti('frefid');

	if ($frefid) {
		$fb = db::execSQL("
			SELECT is_fb
			  FROM webset.disdef_fif_forms
			 WHERE frefid = $frefid
		")->assoc();
	} elseif ($sfrefid) {
		$fb = db::execSQL("
			SELECT is_fb,
			       f.frefid
			  FROM webset.std_fif_forms s
                   INNER JOIN webset.disdef_fif_forms f ON f.frefid = s.frefid
			 WHERE sfrefid = " . $sfrefid . "
		")->assoc();
		$f_refid = $fb['frefid'];
	}

	if ($fb['is_fb']) {
		if (!$sfrefid) {
			$sfrefid = DBImportRecord::factory('webset.std_fif_forms', 'sfrefid')
				->set('hisrefid', $hisrefid)
				->set('frefid ', $frefid)
				->setUpdateInformation()
				->import(DBImportRecord::INSERT_ONLY)
				->recordID();
		}

		echo UIFB504Form::factory($sfrefid)
			->toHTML();
	} else {

		if ($sfrefid > 0) {
			$form = db::execSQL("
            SELECT fname,
                   xmlbody,
                   values_content,
                   s.frefid,
                   archived
              FROM webset.std_fif_forms s
                   INNER JOIN webset.disdef_fif_forms f ON f.frefid = s.frefid
             WHERE sfrefid = " . $sfrefid . "
        ")->assoc();

			$xml_title = $form['fname'];
			$xml_content = base64_decode($form['xmlbody']);
			$xml_values = base64_decode($form['values_content']);
			$frefid = $form['frefid'];
			$archived = ($form['archived'] == 'Y');
		} else {
			$form = db::execSQL("
            SELECT fname,
                   xmlbody
              FROM webset.disdef_fif_forms
             WHERE frefid = $frefid
        ")->assoc();
			$xml_title = $form['fname'];
			$xml_content = base64_decode($form['xmlbody']);

			$stdrefid = db::execSQL("
        	SELECT stdrefid
              FROM webset.std_fif_history his
	         WHERE hisrefid = " . $hisrefid . "
	    ")->getOne();
			$student = new Student($stdrefid);
			$guardians = $student->getGuardians();
			$parents_name = '';
			$parents_phones = '';
			$parents_emails = '';
			$a = $student->getGrade();
			for ($i = 0; $i < count($guardians); $i++) {
				/** @var Guardian */
				$guardian = $guardians[$i];
				if ($guardian->getName('', 'L')) $parents_name .= $guardian->getName('', 'L') . ', ';
				if ($guardian->getPhone('W')) $parents_phones .= $guardian->getPhone('W') . ', ';
				if ($guardian->getEmail()) $parents_emails .= $guardian->getEmail() . ', ';
				if ($i == 1) break;
			}
			if ($parents_name != '') $parents_name = substr($parents_name, 0, -2);
			if ($parents_phones != '') $parents_phones = substr($parents_phones, 0, -2);
			if ($parents_emails != '') $parents_emails = substr($parents_emails, 0, -2);

			$xml_values = '<values>
                             <value name="StdName">' . $student->getName() . '</value>
                             <value name="StdFirstName">' . $student->getName('F', 'L') . '</value>
                             <value name="StdDob">' . $student->getDob() . '</value>
                             <value name="StdAge">' . $student->getAge() . '</value>
                             <value name="StdGrade">' . $student->getGrade() . '</value>
                             <value name="StdAddress">' . $student->getAddress('W') . '</value>
                             <value name="StdHomePhone">' . $student->getPhone() . '</value>
                             <value name="StdSchool">' . $student->getSchool() . '</value>
                             <value name="StdParents">' . $parents_name . '</value>
                             <value name="StdParentWorkPhone">' . $parents_phones . '</value>
                             <value name="StdParentEmail">' . $parents_emails . '</value>
                             <value name="DistrictName">' . SystemCore::$VndName . '</value>
                             <value name="CurrUser">' . SystemCore::$userName . '</value>
                             <value name="CurrDate">' . date("m/d/Y") . '</value>
                         <values>';
			//die('<textarea cols=50 rows=30>' . $xml_values .'</textarea>');
			$archived = false;
		}

		$url = IDEAForm::factory()
			->setTitle($xml_title)
			->setTemplate($xml_content)
			->setValues($xml_values)
			->setUrlCancel('javascript: api.window.destroy();')
			->setUrlFinish('javascript: api.window.dispatchEvent("form_saved");	api.window.destroy();')
			->setParameter('frefid', $frefid)
			->setParameter('hisrefid', $hisrefid)
			->setParameter('sfrefid', $sfrefid)
			->setUrlSave(CoreUtils::getPhysicalPath('./form_save.php'))
			->setPopulateButton(false)
			->getUrlPanel();

		io::js('api.goto("' . $url . '")', true);
	}
?>
