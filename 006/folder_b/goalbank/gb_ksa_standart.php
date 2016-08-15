<?php

	Security::init();

	$RefID = io::get('RefID');
	$ksa_refid = io::get('ksa_refid');

	if ($RefID > 0 or $RefID == '0') {

		$edit = new EditClass('edit1', $RefID);

		$edit->title = 'Add/Edit Key Skill State Standard';

		$edit->setSourceTable('webset.disdef_bgb_standart_key', 'ksrefid');

		$edit->addTab('General Information');
		$edit->addControl('State Standard Resource <br>(please use such format: http://www.mysite.org)')
			->sqlField('ssdurl')
			->size(70)->req();

		$edit->addControl('Description', 'textarea')->sqlField('ssddesc');

		$edit->addGroup('Update Information', true);
		$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
		$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
		$edit->addControl('KSA ID', 'hidden')->value($ksa_refid)->sqlField('key_refid');

		$edit->finishURL = CoreUtils::getURL('gb_ksa_standart.php', array('ksa_refid' => $ksa_refid));
		$edit->cancelURL = CoreUtils::getURL('gb_ksa_standart.php', array('ksa_refid' => $ksa_refid));

		$edit->printEdit();
	} else {
		$list = new ListClass();

		$list->title = 'Key Skill State Standard';

		$list->SQL = "
			SELECT ksrefid,
				   ssdurl,
				   ssddesc
			  FROM webset.disdef_bgb_standart_key
			 WHERE key_refid::integer = " . $_GET["ksa_refid"] . "
			 ORDER BY ssddesc
		";

		$list->addColumn('State Standard Resource');
		$list->addColumn('Description');

		$list->addURL = CoreUtils::getURL('gb_ksa_standart.php', array('ksa_refid' => $ksa_refid));
		$list->editURL = CoreUtils::getURL('gb_ksa_standart.php', array('ksa_refid' => $ksa_refid));

		$list->addButton(
			FFIDEAExportButton::factory()
				->setTable('webset.disdef_bgb_standart_domain')
				->setKeyField('ssdrefid')
				->applyListClassMode()
		);

		$list->printList();
	}
?>
