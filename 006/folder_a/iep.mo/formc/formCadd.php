<?php
    Security::init();
    
    $dskey       = io::get('dskey');
    $ds          = DataStorage::factory($dskey);    
    $tsRefID     = $ds->safeGet('tsRefID');
    $stdIEPYear  = $ds->safeGet('stdIEPYear');
    $RefID       = io::geti('RefID');

    $edit = new EditClass('edit1', io::get('RefID'));

    $edit->setSourceTable('webset.std_form_c_serv', 'refid');

    $edit->title = 'Transition Service';

    $edit->addGroup('General Information');
    $SQL = $RefID > 0 ? "
                        SELECT tarefid, tadesc || COALESCE(' (' || comments || ')','')
                          FROM webset.statedef_transarea area
                         WHERE (enddate IS NULL or now()< enddate)
                           AND EXISTS (SELECT 1 
                                         FROM webset.std_form_c_serv std 
                                        WHERE area.tarefid = std.tarefid AND refid = ".$RefID.")
                         ORDER BY seqnum
                     " : "
                        SELECT tarefid, tadesc || COALESCE(' (' || comments || ')','')
                          FROM webset.statedef_transarea area
                         WHERE (enddate IS NULL or now()< enddate)
                           AND NOT EXISTS (SELECT 1 
                                             FROM webset.std_form_c_serv std 
                                            WHERE area.tarefid = std.tarefid 
                                              AND syrefid = ".$stdIEPYear.")
                         ORDER BY seqnum
                     ";                     
    $edit->addControl('Area ', 'select')
        ->sqlField('tarefid')
        ->sql($SQL);
    
    $edit->addControl('Measurable Postsecondary Goal(S)', 'textarea')
        ->sqlField('postgoals')
        ->value('After high school, I, ' . $ds->get('stdfirstname') . ' ' . $ds->get('stdlastname') . ' WILL ')
        ->css('width', '100%')
        ->css('height', '50px');

    $edit->addGroup('Services Information');
    $edit->addControl('Transition of Service 1', 'textarea')
        ->sqlField('tsdesc')
        ->css('width', '100%')
        ->css('height', '50px');
    
    $edit->addControl('Responsible School', 'textarea')
        ->sqlField('tr_school')
        ->css('width', '100%')
        ->css('height', '50px');

    $edit->addControl('Transition of Service 2', 'textarea')
        ->sqlField('tsdesc_std')
        ->css('width', '100%')
        ->css('height', '50px');
    
    $edit->addControl('Responsible Student')
        ->sqlField('tr_student')
        ->size(80);

    $edit->addControl('Transition of Service 3', 'textarea')
        ->sqlField('tsdesc_par')
        ->css('width', '100%')
        ->css('height', '50px');
    
    $edit->addControl('Responsible Parent')
        ->sqlField('tr_parent')
        ->size(80);

    $edit->addControl('Transition of Service 4', 'textarea')
        ->sqlField('tsdesc_agn')
        ->css('width', '100%')
        ->css('height', '50px');
    
    $edit->addControl('Outside Agency* (specify agency)', 'textarea')
        ->sqlField('tr_agency')
		->help('If appropriate, MUST be invited to IEP meeting with proper consent')
        ->css('width', '100%')
        ->css('height', '50px');
   
    $edit->addGroup("Update Information", true);
    $edit->addControl("Last User", "protected")->value($_SESSION["s_userUID"])->sqlField('lastuser');        
    $edit->addControl("Last Update", "protected")->value(date("m-d-Y H:i:s"))->sqlField('lastupdate');        
    $edit->addControl("Student ID", "hidden")->value($tsRefID)->sqlField('stdrefid');
    $edit->addControl("IEP Year", "hidden")->value($stdIEPYear)->sqlField('syrefid');
    $edit->addControl('Sp Considerations ID', 'hidden')->value(io::geti('spconsid'))->name('spconsid');
    
    $edit->finishURL = CoreUtils::getURL('formCservices.php', array('dskey'=>$dskey, 'spconsid'=>io::geti('spconsid'))); 
    $edit->cancelURL = CoreUtils::getURL('formCservices.php', array('dskey'=>$dskey, 'spconsid'=>io::geti('spconsid')));
    
    $edit->firstCellWidth = '25%';
    
    $edit->topButtons = true;
    $edit->saveAndAdd = (db::execSQL($SQL.' OFFSET 1 ')->getOne()!='');

    $edit->printEdit();
?>
<script type="text/javascript">   
    var edit1 = EditClass.get();
    edit1.onSaveDoneFunc(
        function(refid) {
            api.reload();           
        }
    )
</script>
