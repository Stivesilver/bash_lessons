<?php
    Security::init();

    $dskey      = io::get('dskey');
    $ds         = DataStorage::factory($dskey);
    $tsRefID    = $ds->safeGet('tsRefID');
    $siymrefid  = io::geti('siymrefid');
    $sprrefid   = io::geti('sprrefid');
    $esy        = io::get('ESY');    

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
        ->breakRow(); 
        
    $edit->addControl('Narrative', 'textarea')
        ->sqlField('sprnarative')
        ->css('width', '100%')
        ->css('height', '50');
        
    if (IDEACore::disParam(3) == 'Y') {
        $edit->addControl(
             FFInput::factory('int_range')
                ->caption('Percent of Progress')
                ->sqlField('percentofprogress')
                ->limit(0, 100)
        );
    }
    if (IDEAFormat::get('id') == 19 && io::get('brefid') == 0) {
        $edit->addControl('Progress', 'select_radio')
            ->sqlField('pr_result')
            ->sql("
                SELECT refid, 
                       validvalue
                  FROM webset.glb_validvalues
                 WHERE valuename = 'MOBGBProgressResults'
                 ORDER BY validvalueid , validvalue
            ")
            ->breakRow();
    }        

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
    
    $edit->finishURL = CoreUtils::getURL('pr_progrepMain.php', array('dskey'=>$dskey, 'ESY'=>$esy, 'siymrefid'=>$siymrefid)); 
    $edit->cancelURL = CoreUtils::getURL('pr_progrepMain.php', array('dskey'=>$dskey, 'ESY'=>$esy, 'siymrefid'=>$siymrefid)); 
    
    $edit->saveAndAdd = false;
    $edit->firstCellWidth  = '30%';

    $edit->printEdit();        
  

?>