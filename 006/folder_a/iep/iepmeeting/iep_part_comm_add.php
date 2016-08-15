<?php
    Security::init();

    $dskey      = io::get('dskey');
    $ds         = DataStorage::factory($dskey);
    $tsRefID    = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');
    $iepmode    = io::get('iepmode');

    $edit = new EditClass('edit1', io::geti('RefID'));

    $edit->title = 'Add/Edit IEP Meeting Participation Comment';

    $edit->setSourceTable('webset.std_iepmeetingparticipantscomments', 'simpcrefid');

    $edit->addGroup('General Information');
    $edit->addControl('State Comments List', 'select')
        ->sqlField('impcrefid')
        ->sql("
            SELECT impcrefid,
                   impctext
              FROM webset.statedef_iepmeetpartcomment
             WHERE screfid = " . VNDState::factory()->id ."
        ")
        ->onChange('GetNarr();');

    $edit->addControl('Narrative', 'textarea')
        ->sqlField('simpcnarrtext')
        ->css('WIDTH', '100%')
        ->css('HEIGHT', '100px');

    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
    $edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
    if ($iepmode != 'no') $edit->addControl('IEP Year', 'hidden')->value($stdIEPYear)->sqlField('iep_year');

    $edit->finishURL = CoreUtils::getURL('iep_participants.php', array('dskey'=>$dskey, 'iepmode'=>$iepmode));
    $edit->cancelURL = CoreUtils::getURL('iep_participants.php', array('dskey'=>$dskey, 'iepmode'=>$iepmode));

    $edit->printEdit();

?>