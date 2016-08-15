<?php

	Security::init();

	$list = new ListClass();

	$list->title = 'Default Participants';

	$list->showSearchFields = true;

	$list->SQL = "
		SELECT refid,
			   (xpath('/record/name/text()', validvalue::XML))[1] AS pname,
			   prddesc AS prole,
			   patdesc AS ptype
		  FROM webset.disdef_validvalues AS dv
			   INNER JOIN webset.disdef_participantrolesdef AS dpt ON ((xpath('/record/role/text()', validvalue::XML))[1]::VARCHAR = dpt.prdrefid::VARCHAR)
			   INNER JOIN webset.statedef_participantattendancetypes AS pttype ON ((xpath('/record/type/text()', validvalue::XML))[1]::VARCHAR = pttype.patrefid::VARCHAR)
		 WHERE valuename = 'DefaultParticipants'
		   AND dv.vndrefid = VNDREFID
		 ORDER BY seq_num, validvalue
        ";

	$list->addColumn('Participant');
	$list->addColumn('Role');
	$list->addColumn('Attendance Type');

	$list->addURL = 'participant_edit.php';
	$list->editURL = 'participant_edit.php';

	$list->deleteKeyField = 'refid';
	$list->deleteTableName = 'webset.disdef_validvalues';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->printList();

?>
