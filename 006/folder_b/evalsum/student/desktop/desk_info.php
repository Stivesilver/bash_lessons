<?php
    Security::init();
    
    $dskey     = io::get('dskey');
    $ds        = DataStorage::factory($dskey);    
    $tsRefID   = $ds->safeGet('tsRefID');
    $student   = IDEAStudent::factory($tsRefID);
    
    $edit1 = new EditClass('edit1', 0);    
    $edit1->firstCellWidth = '50%';        
    $edit1->addGroup('General Information');
    $edit1->addControl('Student Name', 'protected')->value($student->get('stdnamefml'))->name('1');
    $edit1->addControl('Date of Birth', 'protected')->value($student->get('stddob'))->name('2');
    $edit1->addControl('Age', 'protected')->value($student->get('stdage'))->name('3');
    $edit1->addControl('Grade', 'protected')->value($student->get('grdlevel'))->name('4');
    $edit1->addControl('Gender', 'protected')->value($student->get('stdsex'))->name('5');
    $edit1->addControl((IDEAFormat::get('id')==3?'Student ID':'Federal ID'), 'protected')->value($student->get('stdfedidnmbr'))->name('6');
    $edit1->addControl((IDEAFormat::get('id')==3?'STN#':'Student ID'), 'protected')->value($student->get('stdschid'))->name('7');
    $edit1->addControl('State ID', 'protected')->value($student->get('stdstateidnmbr'))->name('8');
    $edit1->addControl('Lumen ID', 'protected')->value($student->get('stdrefid'))->name('9');
    $edit1->addControl('Lumen Sp Ed ID', 'protected')->value($student->get('tsrefid'))->name('10');
    $edit1->addControl('Ethnicity', 'protected')->value($student->get('ethdesc'))->name('11');
    $edit1->addControl('Primary Language', 'protected')->value($student->get('prim_lang'))->name('12');
    $edit1->addControl('Attending School', 'protected')->value($student->get('vouname'))->name('13');
    $edit1->addGroup('Address Information');
    $edit1->addControl('Home Address', 'protected')->value($student->get('stdhadr1'))->name('14');
    $edit1->addControl('City, State, Zip', 'protected')->value($student->get('stdhcity').', '.$student->get('stdhstate').' '.$student->get('stdhzip'))->name('15');
    $edit1->addControl('Home Phone', 'protected')->value($student->get('stdhphn'))->name('16');
    $edit1->addControl('Mobile Phone', 'protected')->value($student->get('stdhphnmob'))->name('17');
    
    $edit2 = new EditClass('edit2', 0);    
    $edit2->firstCellWidth = '50%';    
    $guardians = $student->getGuardians();    
    for($i=0;$i<count($guardians);$i++){
        $edit2->addGroup('Parent Information');
        $edit2->addControl('Parent Name', 'protected')->value($guardians[$i]['gdfnm'].' '.$guardians[$i]['gdlnm']);
        $edit2->addControl('Parent Type', 'protected')->value($guardians[$i]['gtdesc']);
        $edit2->addControl('Home Phone', 'protected')->value($guardians[$i]['gdhphn']);
        $edit2->addControl('Work Phone', 'protected')->value($guardians[$i]['gdwphn']);
        $edit2->addControl('Mobile Phone', 'protected')->value($guardians[$i]['gdmphn']);
        $edit2->addControl('Address', 'protected')->value($guardians[$i]['gdadr1']);
        $edit2->addControl('City, State, Zip', 'protected')->value($guardians[$i]['gdcity'].', '.$guardians[$i]['gdstate'].' '.$guardians[$i]['gdcitycode']);
    }
    
    //$url = SystemCore::$Registry->getOne('websis', 'demographics', 'dmg_photo_path') . '/' . $student->get('stdphoto');
    //$url = str_replace('/sec_disk', SystemCore::$secDisk, $url);   
    
    print UILayout::factory() 
             ->addHTML($edit1->printEdit(true), '50% top') 
             ->addHTML(count($guardians) > 0 ? $edit2->printEdit(true) : '', '50% top') 
             ->toHTML();
             
             
?>
