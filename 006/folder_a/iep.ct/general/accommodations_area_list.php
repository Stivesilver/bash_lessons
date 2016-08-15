<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID = $ds->safeGet('tsRefID');

	$list = new ListClass();

	$list->title   = 'Program Accommodations and Modifications';;

	$list->SQL = "
			SELECT progmod.ssmrefid AS ssmrefid,
				   area.macdesc AS area,
				   progmod.ssmmbrother AS ssmmbrother,
				   progmod.ssmteacherother AS ssmteacherother,
				   progmod.ssmbegdate AS ssmbegdate,
				   progmod.ssmenddate AS ssmenddate
			  FROM webset.std_srv_progmod progmod
			 INNER JOIN webset.statedef_mod_acc_cat area ON area.macrefid = progmod.malrefid
			 WHERE iepyear = " . $stdIEPYear . "
			   AND stdrefid = " . $tsRefID . "
		";

	$list->addColumn('Area')
		->sqlField('area');

	$list->addColumn('Description')
		->sqlField('ssmmbrother');

	$list->addColumn('Site/Activities')
		->sqlField('ssmteacherother');

	$list->addColumn('Begin Date')
		->type('date')
		->sqlField('ssmbegdate');

	$list->addColumn('End Date')
		->type('date')
		->sqlField('ssmenddate');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_srv_progmod')
			->setKeyField('iepyear')
			->applyListClassMode()
	);

	$list->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$list->deleteTableName = 'webset.std_srv_progmod';
	$list->deleteKeyField = 'ssmrefid';

	$list->addURL = CoreUtils::getURL('accommodations_area_edit.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('accommodations_area_edit.php', array('dskey' => $dskey));

	$list->printList();

?>
