<?php

	Security::init();

	$dskey      = io::get('dskey');
	$typeBlock  = io::geti('idBlock');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');

	$SQL = "INSERT INTO webset_tx.std_transfer_packet (stdrefid, iepyear)
            SELECT $tsRefID, $stdIEPYear
             WHERE NOT EXISTS (SELECT 1 FROM webset_tx.std_transfer_packet
                                WHERE stdrefid = $tsRefID
                                  AND iepyear = $stdIEPYear)";
	db::execSQL($SQL);
	$RefID = db::execSQL("
		SELECT refid
		  FROM webset_tx.std_transfer_packet
         WHERE stdrefid = $tsRefID
           AND iepyear = $stdIEPYear
        ")->getOne();

	$edit = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset_tx.std_transfer_packet', 'refid');

	$edit->title = "Transfer Packet Data";

	$edit->addGroup("Waiting Period");
	$edit->addControl(
			FFSwitchYN::factory("I waive the required 5 school day waiting period between the notice of the ARD/IEP committee meeting and the meeting itself.")
		)
		->sqlField('field0');
	$edit->addGroup("The student's eligibility in former district was verified:");
	$edit->addControl(FFSwitchYN::factory("by telephone."))->sqlField('field1_yn');

	$edit->addControl("Staff member contacted:", "edit")
		->sqlField('field1_oth')
		->size(60);

	$edit->addControl(FFSwitchYN::factory("in writing."))->sqlField('field2_yn');
	$edit->addControl("Documents received:", "edit")
		->sqlField('field2_oth')
		->size(60);

	$edit->addControl("Information from the parent and the former school district indicates that this student has met the eligibility criteria for special education and related services in the area of:", "textarea")
		->sqlField('field3')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addGroup("Former School");
	$edit->addControl("C. Description of services (instructional and related) provided in former school, as described by that district:", "textarea")
		->sqlField('field4')
		->css("width", "100%")
		->css("height", "50px");

	$edit->addControl(FFSwitchYN::factory("Students identified as speech disabled will have a review of records and/or will be evaluated to establish eligibility."))->sqlField('field5');
	$edit->addControl(FFSwitchYN::factory("Students identified as qualifying for special education and related services will have a review of records and/or will be reevaluated to establish eligibility."))->sqlField('field6');
	$edit->addGroup("Development of the IEP");
	$edit->addControl(FFSwitchYN::factory("A current IEP from an in state school district is available, considered appropriate and remains in effect"))->sqlField('field7');
	$edit->addControl(FFSwitchYN::factory("An interim placement has been determined. The IEP will be finalized within 30 school days"))->sqlField('field8');
	$edit->addUpdateInformation();
	$edit->addControl("", "hidden")->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl("", "hidden")->value($stdIEPYear)->sqlField('iepyear');

	$edit->saveAndEdit    = true;
	$edit->firstCellWidth = "50%";

	$edit->printEdit();

?>