<?php

	Security::init();

	$record = db::execSQL("
		SELECT (xpath('/record/name/text()', validvalue::XML))[1] AS pname,
               (xpath('/record/role/text()', validvalue::XML))[1] AS prole,
               (xpath('/record/type/text()', validvalue::XML))[1] AS ptype,
               sequence_number
          FROM webset.disdef_validvalues
         WHERE valuename = 'DefaultParticipants'
           AND vndrefid = VNDREFID
           AND refid = " . io::geti('RefID') . "
         ORDER BY sequence_number, validvalue
	")->assoc();

	$edit = new EditClass('edit1', io::geti('RefID'));

	$edit->setSourceTable('webset.disdef_validvalues', 'refid');

	$edit->title = 'Add/Edit Default Participants';

	$edit->addGroup('General Information');

	$edit->addControl('Participant')
		->name('pname')
		->value($record['pname']);

	$edit->addControl(FFSelect::factory('Role'))
		->name('prole')
		->sql("
		   SELECT prdrefid,
				  prddesc
             FROM webset.disdef_participantrolesdef
            WHERE vndrefid = VNDREFID
            ORDER BY seq_num
        ")
		->value($record['prole']);

	$edit->addControl(FFSelect::factory('Attendance Type'))
		->name('ptype')
		->sql("
			SELECT patrefid,
			       patdesc
              FROM webset.statedef_participantattendancetypes
             WHERE screfid = " . VNDState::factory()->id . "
             ORDER BY pat_seq, patdesc
        ")
		->value($record['ptype']);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Value Name', 'hidden')->value('DefaultParticipants')->sqlField('valuename');
	$edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');

	$edit->finishURL = 'participant_list.php';
	$edit->cancelURL = 'participant_list.php';

	$edit->firstCellWidth = "30%";

	$edit->setPostsaveCallback('saveRecord', 'participant_save.inc.php');

	$edit->printEdit();

	print UIMessage::factory('Please use keyword STUDENT in Participant field for Student Name and GUARDIAN for Guardian Name', UIMessage::NOTE)->toHTML();


?>
