<?php

	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::get('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$list = new ListClass();

	$list->title = 'How Progress will be Reported';

	$list->showSearchFields = true;

	$list->SQL = "
			SELECT mi.smiprefid,
				   mipdesc,
				   mi.lastuser,
				   mi.lastupdate
			  FROM webset.std_methodinfo AS mi
				   INNER JOIN webset.statedef_methodinfo state ON state.miprefid = mi.miprefid
			 WHERE mi.stdrefid = $tsRefID
			 ORDER BY CASE WHEN mipdesc ILIKE 'Other%' THEN 2 ELSE 1 END, mipdesc
		";


	$list->addColumn('How Progress will be Reported');
	$list->addColumn('Last User');
	$list->addColumn('Last Update')->type('datetime');

	$list->deleteTableName = "webset.std_methodinfo";
	$list->deleteKeyField = "smiprefid";

	$list->addButton(
		FFIDEAExportButton::factory()
		->setTable('webset.std_methodinfo')
		->setKeyField('smiprefid')
		->applyListClassMode()
	);

	$addnew = 
		IDEAPopulateWindow::factory()
		->addNewItem()
		->setTitle('Add Method')
		->setSQL("
				SELECT miprefid,
					   mipdesc
				  FROM webset.statedef_methodinfo AS state
				 WHERE (screfid = " . VNDState::factory()->id . " OR vndrefid = VNDREFID)
				   AND NOT EXISTS (
						SELECT 1
						  FROM webset.std_methodinfo AS mi
						 WHERE mi.stdrefid = ". $tsRefID."
						   AND mi.miprefid = state.miprefid
					   )
				 ORDER BY CASE mipdesc WHEN 'Other' THEN 'z' ELSE mipdesc END
			")
			->addColumn('How Progress will be Reported')
			->setDestinationTable('webset.std_methodinfo')
			->setDestinationTableKeyField('smiprefid')
			->setSourceTable('webset.statedef_methodinfo')
			->setSourceTableKeyField('miprefid')
			->addPair('stdrefid', $tsRefID, false)
			->addPair('lastuser', SystemCore::$userUID, FALSE)
			->addPair('lastuser', SystemCore::$userUID, FALSE)
			->addPair('lastupdate', 'NOW()', TRUE)
			->addPair('miprefid', 'miprefid', TRUE)
			->getPopulateButton();

	$addnew->value('Add New');


	$list->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$list->addButton($addnew);

	$list->printList();

?>
