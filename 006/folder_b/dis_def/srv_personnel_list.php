<?php

	Security::init();

	$list = new listClass();

	$list->title = 'Support For School Personnel Defaults';
	
	$list->SQL = "
			SELECT dis.refid,
				   sspdesc,
				   validvalue
			  FROM webset.statedef_services_supppersonnel state
				   INNER JOIN webset.disdef_validvalues dis ON state.ssprefid = dis.validvalueid::int
			 WHERE screfid = " . VNDState::factory()->id . "
			   AND ((CASE enddate<now() WHEN true THEN 2 ELSE 1 END) = '1') 
			   AND valuename = 'MO_Personnel_Defaults'
			   AND vndrefid = VNDREFID
			 ORDER BY seqnum, sspdesc
        ";

	$list->addColumn("Support for School Personnel", "", "text", "", "", "");
	$list->addColumn("Default Narative", "", "text", "", "", "");

	$list->deleteKeyField = 'refid';
	$list->deleteTableName = 'webset.disdef_validvalues';

	$list->addURL = "srv_personnel_edit.php";
	$list->editURL = "srv_personnel_edit.php";
	
	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.disdef_validvalues')
			->setKeyField('refid')
			->applyListClassMode()
	);

	$list->printList();
?>