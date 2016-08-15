<?php
    Security::init();

    $dskey      = io::get('dskey');
    $ds         = DataStorage::factory($dskey);
    $tsRefID    = $ds->safeGet('tsRefID');
    $siymrefid  = io::geti('siymrefid');
    $sprrefid   = io::geti('sprrefid');
    $esy        = io::get('ESY') == 'Y' ? 'Y' : 'N';

    $edit = new EditClass('edit1', $sprrefid);

    $edit->title = 'Add/Edit Progress';
    
    $edit->setSourceTable('webset.std_progressreportmst', 'sprrefid');    

    $edit->addGroup('General Information');
    $edit->addControl('Extent of Progress toward the goal', 'select_radio')
        ->sqlField('eprefid')
        ->sql("
            SELECT eprefid, 
                   epsdesc || ' - ' || epldesc
              FROM webset.disdef_progressrepext
             WHERE vndrefid = VNDREFID
             ORDER BY epseq, eprefid
        ")
        ->breakRow()		
		->req(); 
        
    $edit->addControl('Narrative', 'textarea')
        ->sqlField('sprnarative')
        ->css('width', '100%')
        ->css('height', '50');

    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');        
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
    $edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid'); 
    
    if ($sprrefid == 0 ) {
        $edit->addControl('Period ID', 'hidden')->value(io::geti('period'))->sqlField('sprmarkingprd');
        $edit->addControl('Goal ID', 'hidden')->value(io::geti('grefid'))->sqlField('stdgoalrefid');
        $edit->addControl('Benchmark ID', 'hidden')->value(io::get('brefid') == 0 ? null : io::get('brefid'))->sqlField('stdbenchmarkrefid');
        $edit->addControl('School Year ID', 'hidden')->value(io::geti('dsyrefid'))->sqlField('dsyrefid');
    }
    
    $edit->finishURL = CoreUtils::getURL('progrep_main.php', array('dskey'=>$dskey, 'ESY'=>$esy, 'siymrefid'=>$siymrefid)); 
    $edit->cancelURL = CoreUtils::getURL('progrep_main.php', array('dskey'=>$dskey, 'ESY'=>$esy, 'siymrefid'=>$siymrefid)); 
    
    $edit->saveAndAdd = false;
    $edit->firstCellWidth  = '30%';

    $edit->printEdit();        
  

?>