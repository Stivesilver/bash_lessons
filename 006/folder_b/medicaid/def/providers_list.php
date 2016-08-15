<?php

	Security::init();

	$list      = new ListClass();
	$list->SQL = "
		SELECT *
          FROM webset.med_def_provider
         ORDER BY mdp_refid desc
		";

	$list->title           = 'Medicaid Provider Types';
	$list->addURL          = CoreUtils::getURL('providers_edit.php', array());
	$list->editURL         = CoreUtils::getURL('providers_edit.php', array());
	$list->deleteTableName = 'webset.med_def_provider';
	$list->deleteKeyField  = 'mdp_refid';

	$list->addColumn('Code')->sqlField('mdp_provider_type_code');
	$list->addColumn('Provider Type')->sqlField('mdp_provider_type');
	$list->addColumn('Record Status')
		->dataCallback('converKey');

	$list->addColumn('Activation Date', null, 'date');
	$list->addColumn('Retired Date',    null, 'date');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->printList();

	# convert key to title
	function converKey($col, $name) {
		$keys = array(
			'A' => 'Active',
			'R' => 'Retired',
			'P' => 'Pending'
		);

		$key = $col['mdp_status'];
		return $keys[$key];
	}