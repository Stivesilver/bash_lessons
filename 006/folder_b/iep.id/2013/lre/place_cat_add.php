<?php

	Security::init();

	$dskey = io::get('dskey');
	$age = io::get('age');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$edit = new EditClass("edit1", io::geti('RefID'));

	$edit->title = 'Add/Edit Educational Environment';

	$edit->setSourceTable('webset.std_placementcode', 'pcrefid');

	switch ($age) {
		case 'k12':
			$where = 'AND ec.spctrefid = 71';
			break;
		case 'ec':
			$where = 'AND ec.spctrefid = 71';
			break;

		default:
			$where = '';
			break;
	}
	
	$edit->addGroup("General Information");
	$edit->addControl("Placement", "select_radio")
		->sqlField('spcrefid')
		->name('spcrefid')
		->sql("
            SELECT plc.spcrefid,
                   CASE spctcode WHEN 'EC' THEN 'EC' ELSE 'K12' END || ' - (' || plc.spccode || ') ' || plc.spcdesc
              FROM webset.statedef_placementcategorycode plc
                   INNER JOIN webset.statedef_placementcategorycodetype ec ON plc.spctrefid = ec.spctrefid
             WHERE plc.screfid = " . VNDState::factory()->id . "
               AND (plc.recdeactivationdt IS NULL or now()< plc.recdeactivationdt)
			   " . $where . "
             ORDER BY 2, plc.spccode
        ")
		->breakRow();
	
	$edit->addControl('Start Date', 'date')
		->sqlField('spcbeg')
		->req();
	
	$edit->addControl('End Date', 'date')
		->sqlField('spcend')
		->req();
		
	$edit->addGroup("Update Information", true);
	$edit->addControl("Last User", "protected")->value($_SESSION["s_userUID"])->sqlField('lastuser');
	$edit->addControl("Last Update", "protected")->value(date("m-d-Y H:i:s"))->sqlField('lastupdate');
	$edit->addControl("Student ID", "hidden")->value($tsRefID)->sqlField('stdrefid');

	$edit->addSQLConstraint(
		'You are trying to add duplicate Placement', "
        SELECT 1 
          FROM webset.std_placementcode
         WHERE stdrefid = " . $tsRefID . "
		   AND spcrefid = [spcrefid]	 
           AND pcrefid!=AF_REFID
    ");

	$edit->finishURL = CoreUtils::getURL('place_cat.php', array('dskey' => $dskey, 'age' => $age));
	$edit->cancelURL = CoreUtils::getURL('place_cat.php', array('dskey' => $dskey, 'age' => $age));

	$edit->saveAndAdd = false;

	$edit->printEdit();
?>