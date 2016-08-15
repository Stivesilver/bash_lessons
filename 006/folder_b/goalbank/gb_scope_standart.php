<?php

	Security::init();

	$RefID = io::get('RefID');
	$scope_refid = io::get('scope_refid');

	if ($RefID > 0 or $RefID == '0') {

		$edit = new EditClass('edit1', $RefID);

		$edit->title = 'Add/Edit Scope State Standard';

		$edit->setSourceTable('webset.disdef_bgb_standart_scope', 'sssrefid');

		$edit->addTab('General Information');
		$edit->addControl('State Standard Resource <br>(please use such format: http://www.mysite.org)')
			->sqlField('ssdurl')
			->size(70)->req();

		$edit->addControl('Description', 'textarea')->sqlField('ssddesc');

		$edit->addGroup('Update Information', true);
		$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
		$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
		$edit->addControl('Scope ID', 'hidden')->value($scope_refid)->sqlField('scope_refid');

		$edit->finishURL = CoreUtils::getURL('gb_scope_standart.php', array('scope_refid' => $scope_refid));
		$edit->cancelURL = CoreUtils::getURL('gb_scope_standart.php', array('scope_refid' => $scope_refid));

		$edit->printEdit();
	} else {
		$list = new ListClass();

		$list->title = 'Scope State Standard';

		$list->SQL = "
			SELECT sssrefid,
				   ssdurl,
				   ssddesc
			  FROM webset.disdef_bgb_standart_scope
			 WHERE scope_refid::integer = " . $_GET["scope_refid"] . "
			 ORDER BY ssddesc
		";

		$list->addColumn('State Standard Resource');
		$list->addColumn('Description');

		$list->addURL = CoreUtils::getURL('gb_scope_standart.php', array('scope_refid' => $scope_refid));
		$list->editURL = CoreUtils::getURL('gb_scope_standart.php', array('scope_refid' => $scope_refid));

		$list->addButton(
			FFIDEAExportButton::factory()
				->setTable('webset.disdef_bgb_standart_scope')
				->setKeyField('sssrefid')
				->applyListClassMode()
		);

		$list->printList();
	}
?>
