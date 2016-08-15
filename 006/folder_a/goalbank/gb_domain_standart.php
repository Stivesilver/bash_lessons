<?php

	Security::init();

	$RefID = io::get('RefID');
	$domain_refid = io::get('domain_refid');

	if ($RefID > 0 or $RefID == '0') {

		$edit = new EditClass('edit1', $RefID);

		$edit->title = 'Add/Edit Domains State Standard';

		$edit->setSourceTable('webset.disdef_bgb_standart_domain', 'ssdrefid');

		$edit->addTab('General Information');
		$edit->addControl('State Standard Resource <br>(please use such format: http://www.mysite.org)')
			->sqlField('ssdurl')
			->size(70)->req();

		$edit->addControl('Description', 'textarea')->sqlField('ssddesc');

		$edit->addGroup('Update Information', true);
		$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
		$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
		$edit->addControl('Domain ID', 'hidden')->value($domain_refid)->sqlField('domain_refid');

		$edit->finishURL = CoreUtils::getURL('gb_domain_standart.php', array('domain_refid' => $domain_refid));
		$edit->cancelURL = CoreUtils::getURL('gb_domain_standart.php', array('domain_refid' => $domain_refid));

		$edit->printEdit();
	} else {
		$list = new ListClass();

		$list->title = 'Domains State Standard';

		$list->SQL = "
			SELECT ssdrefid,
				   ssdurl,
				   ssddesc
			  FROM webset.disdef_bgb_standart_domain
			 WHERE domain_refid::integer = " . $_GET["domain_refid"] . "
			 ORDER BY ssddesc
		";

		$list->addColumn('State Standard Resource');
		$list->addColumn('Description');

		$list->addURL = CoreUtils::getURL('gb_domain_standart.php', array('domain_refid' => $domain_refid));
		$list->editURL = CoreUtils::getURL('gb_domain_standart.php', array('domain_refid' => $domain_refid));

		$list->addButton(
			FFIDEAExportButton::factory()
				->setTable('webset.disdef_bgb_standart_domain')
				->setKeyField('ssdrefid')
				->applyListClassMode()
		);

		$list->printList();
	}
?>
